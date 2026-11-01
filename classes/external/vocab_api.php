<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace mod_vocabcoach\external;

use core\exception\required_capability_exception;
use core_external\external_api;
use core_external\external_description;
use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use mod_vocabcoach\vocab_manager;
use mod_vocabcoach\vocabhelper;

/**
 * Check-Vocab-API. Manages all interactions with the vocab database.
 *
 * @package   mod_vocabcoach
 * @copyright 2023 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Johannes Funk
 */
class vocab_api extends external_api {
    /**
     * Returns description of update_vocab() parameters.
     *
     * @return external_function_parameters
     * @since  Moodle 3.4
     */
    public static function update_vocab_parameters(): external_function_parameters {
        return new external_function_parameters([
            'id' => new external_value(PARAM_INT),
            'cmid' => new external_value(PARAM_INT),
            'known' => new external_value(PARAM_BOOL),
        ]);
    }

    /**
     * Returns description of update_vocab() result value.
     *
     * @return external_description|null
     */
    public static function update_vocab_returns(): ?external_description {
        return null;
    }

    /**
     * Updates a given vocab item. Called after a check.
     * @param int $id ID of the vocab item.
     * @param int $cmid Course module id
     * @param bool $known
     */
    public static function update_vocab(int $id, int $cmid, bool $known): void {
        global $DB, $USER;

        ['id' => $id, 'known' => $known] = self::validate_parameters(
            self::update_vocab_parameters(),
            ['id' => $id, 'known' => $known, 'cmid' => $cmid]
        );
        $context = \core\context\module::instance($cmid);
        self::validate_context($context);
        require_capability('mod/vocabcoach:view', $context);

        $record = $DB->get_record(
            'vocabcoach_vocabdata',
            ['vocabid' => $id, 'userid' => $USER->id, 'cmid' => $cmid],
            '*',
            MUST_EXIST
        );

        $record->stage = $known ? min($record->stage + 1, 5) : 1;
        $record->lastchecked = time();

        $DB->update_record('vocabcoach_vocabdata', $record);
    }

    /**
     * Returns description of get_user_vocabs() parameters.
     *
     * @return external_function_parameters
     */
    public static function get_user_vocabs_parameters(): external_function_parameters {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'cmid', VALUE_REQUIRED),
            'stage' => new external_value(PARAM_INT, 'stage', VALUE_REQUIRED),
            'force' => new external_value(PARAM_BOOL, 'force', VALUE_REQUIRED),
        ]);
    }

    /**
     * Returns description of get_user_vocabs() result value.
     *
     * @return external_multiple_structure
     */
    public static function get_user_vocabs_returns(): external_multiple_structure {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_INT),
                'front' => new external_value(PARAM_TEXT),
                'back' => new external_value(PARAM_TEXT),
            ])
        );
    }

    /**
     * Returns all vocab items in a certain stage
     *
     * @param int $cmid
     * @param int $stage
     * @param bool $force
     * @return array
     * @throws \dml_exception|required_capability_exception
     */
    public static function get_user_vocabs(int $cmid, int $stage, bool $force): array {
        global $DB, $USER;
        ['cmid' => $cmid, 'stage' => $stage, 'force' => $force] = self::validate_parameters(
            self::get_user_vocabs_parameters(),
            ['cmid' => $cmid, 'stage' => $stage, 'force' => $force]
        );
        $context = \core\context\module::instance($cmid);
        self::validate_context($context);
        require_capability('mod/vocabcoach:view', $context);

        $query = "SELECT vocab.id AS id, front, back FROM {vocabcoach_vocab} vocab
                   JOIN {vocabcoach_vocabdata} vd ON vocab.id = vd.vocabid
                   WHERE vd.userid= :userid AND vd.stage = :stage AND vd.cmid = :cmid";
        $params = ['userid' => $USER->id, 'stage' => $stage, 'cmid' => $cmid];

        if (!$force) {
            $query .= " AND vd.lastchecked < :lastchecked";

            $vocabhelper = new vocabhelper($cmid);
            if (!array_key_exists($stage, $vocabhelper->boxtimes)) {
                throw new \invalid_parameter_exception('Invalid stage: ' . $stage);
            }
            $days = $vocabhelper->boxtimes[$stage];
            $mintimestamp = $vocabhelper->old_timestamp($days);
            $params['lastchecked'] = $mintimestamp;
        }

        $output = $DB->get_records_sql($query, $params);

        return array_values($output);
    }

    /**
     * Returns description of get_list_vocabs() parameters.
     *
     * @return external_function_parameters
     */
    public static function get_list_vocabs_parameters(): external_function_parameters {
        return new external_function_parameters([
            'listid' => new external_value(PARAM_INT, VALUE_REQUIRED),
        ]);
    }
    /**
     * Returns description of get_list_vocabs() result value.
     *
     * @return external_multiple_structure
     */
    public static function get_list_vocabs_returns(): external_multiple_structure {
        return self::vocab_returns();
    }

    /**
     * Returns description of vocab_returns() result value.
     *
     * @return external_multiple_structure
     */
    public static function vocab_returns(): external_multiple_structure {
        return new external_multiple_structure(
            new external_single_structure([
                'dataid' => new external_value(PARAM_INT),
                'front' => new external_value(PARAM_TEXT),
                'back' => new external_value(PARAM_TEXT),
            ])
        );
    }

    /**
     * Returns all vocab items on a list.
     * @param int $listid
     * @return array
     * @throws \invalid_parameter_exception
     */
    public static function get_list_vocabs(int $listid): array {
        self::validate_parameters(self::get_list_vocabs_parameters(), ['listid' => $listid]);

        global $DB;

        $query = "SELECT vocab.ID AS id, front, back FROM {vocabcoach_vocab} vocab
            INNER JOIN {vocabcoach_list_vocabs} list_contains ON  list_contains.vocabID = vocab.ID
            WHERE list_contains.listID = $listid;";
        try {
            $output = $DB->get_records_sql($query);
            return array_values($output);
        } catch (\dml_exception $e) {
            return [];
        }
    }

    /**
     * Returns description of delete_vocab_from_user() parameters.
     *
     * @return external_function_parameters
     */
    public static function delete_vocab_from_user_parameters(): external_function_parameters {
        return new external_function_parameters([
            'vocabid' => new external_value(PARAM_INT),
            'cmid' => new external_value(PARAM_INT),
        ]);
    }

    /**
     * Returns description of delete_vocab_from_user() result value.
     *
     * @return external_description|null
     */
    public static function delete_vocab_from_user_returns(): ?external_description {
        return null;
    }

    /**
     * Removes one vocab item from a user box.
     * @param int $vocabid ID of the vocab to be delelted
     * @param int $cmid course module ID
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     */
    public static function delete_vocab_from_user(int $vocabid, int $cmid): void {
        global $USER;
        ['vocabid' => $vocabid, 'cmid' => $cmid] = self::validate_parameters(
            self::delete_vocab_from_user_parameters(),
            ['vocabid' => $vocabid, 'cmid' => $cmid]
        );

        $context = \core\context\module::instance($cmid);
        self::validate_context($context);

        $vm = new vocab_manager();
        $vm->delete_vocab_from_user($vocabid, $cmid, $USER->id);
    }

    /**
     * Returns description of get_class_total() parameters.
     *
     * @return external_function_parameters
     */
    public static function get_class_total_parameters(): external_function_parameters {
        return new external_function_parameters([
                'cmid' => new external_value(PARAM_INT),
        ]);
    }

    /**
     * Returns description of get_class_total() result value.
     *
     * @return external_value
     */
    public static function get_class_total_returns(): external_value {
        return new external_value(PARAM_INT, 'the total number of due vocab');
    }

    /**
     * Return the total number of vocab waiting for revision in a course, defaults to -1 at error.
     *
     * @param int $cmid
     * @return int
     * @throws \invalid_parameter_exception
     */
    public static function get_class_total(int $cmid): int {
        ['cmid' => $cmid] = self::validate_parameters(
            self::get_class_total_parameters(),
            ['cmid' => $cmid]
        );

        $context = \core\context\module::instance($cmid);
        self::validate_context($context);

        require_capability('mod/vocabcoach:show_class_total_live', $context);

        $cf = new \mod_vocabcoach\course_features($context);
        return $cf->get_class_total();
    }


    /**
     * Returns description of get_class_total() parameters.
     *
     * @return external_function_parameters
     */
    public static function edit_user_vocab_parameters(): external_function_parameters {
        return new external_function_parameters([
                'front' => new external_value(PARAM_TEXT),
                'back' => new external_value(PARAM_TEXT),
                'vocabid' => new external_value(PARAM_INT),
                'cmid' => new external_value(PARAM_INT),
        ]);
    }

    /**
     * Returns description of get_class_total() result value.
     *
     * @return external_single_structure
     */
    public static function edit_user_vocab_returns(): external_single_structure {
        return new external_single_structure([
            'id' => new external_value(PARAM_INT, 'the vocabid of the new vocab'),
            'front' => new external_value(PARAM_TEXT, 'Front of the new vocab'),
            'back' => new external_value(PARAM_TEXT, 'Back of the new vocab'),
        ]);
    }

    /**
     * Edit a user vocab, i.e. create a new vocab and replace the old vocabdata entry by the new one.
     *
     * @param string $front
     * @param string $back
     * @param int $vocabid
     * @return array
     */
    public static function edit_user_vocab(string $front, string $back, int $vocabid, int $cmid): array {
        global $USER;

        ['front' => $front, 'back' => $back, 'vocabid' => $vocabid, 'cmid' => $cmid] = self::validate_parameters(
            self::edit_user_vocab_parameters(),
            ['front' => $front, 'back' => $back, 'vocabid' => $vocabid, 'cmid' => $cmid]
        );
        $context = \core\context\module::instance($cmid);
        self::validate_context($context);
        require_capability('mod/vocabcoach:view', $context);

        $vm = new \mod_vocabcoach\vocab_manager();
        $newvocab = $vm->edit_user_vocab($vocabid, $front, $back, $cmid, $USER->id);

        return [
            'id' => $newvocab->get('id'),
            'front' => $newvocab->get('front'),
            'back' => $newvocab->get('back'),
        ];
    }
}
