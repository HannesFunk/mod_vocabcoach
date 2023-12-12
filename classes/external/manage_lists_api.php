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

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once("{$CFG->libdir}/externallib.php");

use context_course;
use external_api;
use external_function_parameters;
use external_value;
use external_multiple_structure;
use external_single_structure;
use dml_exception;
use invalid_parameter_exception;
use stdClass;

/**
 * Manage-lists-API: manages lists.
 *
 * @package   mod_vocabcoach
 * @copyright 2023 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Johannes Funk
 */
class manage_lists_api extends external_api {
    /**
     * Returns description of get_lists() parameters.
     *
     * @return external_function_parameters
     */
    public static function get_lists_parameters() : external_function_parameters {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, VALUE_OPTIONAL),
            'userid' => new external_value(PARAM_INT, VALUE_OPTIONAL),
            'onlyownlists' => new external_value(PARAM_BOOL, VALUE_OPTIONAL),
        ]);
    }

    /**
     * Returns description of get_lists() result value.
     *
     * @return external_multiple_structure
     */
    public static function get_lists_returns() : external_multiple_structure {
        return new external_multiple_structure(
            new external_single_structure([
            'id' => new external_value(PARAM_INT),
            'title' => new external_value(PARAM_TEXT),
            'year' => new external_value(PARAM_INT),
            'book' => new external_value(PARAM_TEXT),
            'unit' => new external_value(PARAM_TEXT),
            'number' => new external_value(PARAM_INT),
            'createdby' => new external_value(PARAM_INT),
            'creator' => new external_value(PARAM_TEXT),
            'private' => new external_value(PARAM_BOOL),
            ])
        );
    }

    /**
     * Returns all lists in a course module.
     * @param int $cmid
     * @param int $userid
     * @param bool $onlyownlists
     * @return array|null
     * @throws invalid_parameter_exception
     */
    public static function get_lists(int $cmid, int $userid, bool $onlyownlists = false) : array {

        self::validate_parameters(self::get_lists_parameters(),
            ['cmid' => $cmid, 'userid' => $userid, 'onlyownlists' => $onlyownlists]);

        global $DB;

        try {
            $conditions = 'cmid = '.$cmid.' AND (private = 0 OR createdby = '.$userid.')';
            if ($onlyownlists) {
                $conditions .= ' AND createdby = '.$userid;
            }
            $records = $DB->get_records_sql("SELECT id, title, year, book, unit, createdby, private
                FROM {vocabcoach_lists} WHERE ".$conditions);
            $output = [];
            foreach ($records as $record) {
                $query = "SELECT COUNT(DISTINCT(vocabid)) FROM {vocabcoach_list_contains} WHERE listid = ".$record->id.";";
                $vocabnumber = $DB->count_records_sql($query);
                $record->number = $vocabnumber;
                $creator = \core_user::get_user($record->createdby);
                $record->creator = fullname($creator);
                $output[] = $record;
            }
            return array_values($output);
        } catch (\dml_exception $e) {
            return [];
        }
    }

    /**
     * Returns description of delete_list() parameters.
     *
     * @return external_function_parameters
     */
    public static function delete_list_parameters() : external_function_parameters {
        return new external_function_parameters([
            'listid' => new external_value(PARAM_INT, VALUE_REQUIRED),
        ]);
    }

    /**
     * Returns description of delete_list() result value.
     *
     * @return external_single_structure
     */
    public static function delete_list_returns() : external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Whether Delete was successful.'),
        ]);
    }

    /**
     * Deletes a list
     * @param int $listid
     * @return false[]|true[]
     * @throws invalid_parameter_exception
     */
    public static function delete_list(int $listid) : array {
        self::validate_parameters(self::delete_list_parameters(), ['listid' => $listid]);

        global $DB;
        try {
            $DB->delete_records('vocabcoach_lists', ['id' => $listid]);
            $DB->delete_records('vocabcoach_list_contains', ['listid' => $listid]);
        } catch (\dml_exception $e) {
            return ['success' => false];
        }

        return ['success' => true];
    }

    /**
     * Returns description of add_list_to_user() parameters.
     *
     * @return external_function_parameters
     */
    public static function add_list_to_user_parameters() : external_function_parameters {
        return new external_function_parameters([
            'listid' => new external_value(PARAM_INT, VALUE_REQUIRED),
            'userid' => new external_value(PARAM_INT, VALUE_REQUIRED),
            'cmid' => new external_value(PARAM_INT, VALUE_REQUIRED),
        ]);
    }

    /**
     * Adds vocabs from one list to user boxes.
     * @param int $listid
     * @param int $userid
     * @param int $cmid
     * @return false[]|true[]
     * @throws invalid_parameter_exception
     */
    public static function add_list_to_user(int $listid, int $userid, int $cmid) : array {
        self::validate_parameters(self::add_list_to_user_parameters(), ['listid' => $listid, 'userid' => $userid, 'cmid' => $cmid]);

        global $DB;

        $time = strtotime('2000-01-01 00:00:00');

        $query = "SELECT id, vocabid FROM {vocabcoach_list_contains} list_contains
                                WHERE list_contains.listid = $listid
                                AND list_contains.vocabid NOT IN
       (SELECT vocabID FROM {vocabcoach_vocabdata} vocabdata WHERE userid = $userid AND cmid = $cmid)";

        try {
            $records = $DB->get_records_sql($query);
            $insertarray = [];
            foreach (array_values($records) as $record) {
                $insert = new stdClass();
                $insert->vocabid = $record->vocabid;
                $insert->userid = $userid;
                $insert->cmid = $cmid;
                $insert->stage = 1;
                $insert->lastchecked = $time;
                $insertarray[] = $insert;
            }
            $DB->insert_records('vocabcoach_vocabdata', $insertarray);
            return ['success' => true];
        } catch (dml_exception $e) {
            return ['success' => false];
        }
    }

    /**
     * Returns description of add_list_to_user() result value.
     *
     * @return external_single_structure
     */
    public static function add_list_to_user_returns() : external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Whether Delete was successful.'),
        ]);
    }

    /**
     * Returns description of distribute_list() parameters.
     *
     * @return external_function_parameters
     */
    public static function distribute_list_parameters() : external_function_parameters {
        return new external_function_parameters([
                'listid' => new external_value(PARAM_INT, VALUE_REQUIRED),
                'cmid' => new external_value(PARAM_INT, VALUE_REQUIRED),
        ]);
    }

    /**
     * Adds vocabs from a list to all user boxes in a course.
     * @param int $listid
     * @param int $cmid
     * @return true[]
     * @throws invalid_parameter_exception
     */
    public static function distribute_list(int $listid, int $cmid) : array {
        self::validate_parameters(self::distribute_list_parameters(), ['listid' => $listid, 'cmid' => $cmid]);

        $cm = get_coursemodule_from_id('vocabcoach', $cmid, 0, false, MUST_EXIST);
        $context = context_course::instance($cm->course);

        $students = get_enrolled_users($context);

        foreach ($students as $student) {
            self::add_list_to_user($listid, $student->id, $cmid);
        }

        return ['success' => true];
    }

    /**
     * Returns description of distribute_list() result value.
     *
     * @return external_single_structure
     */
    public static function distribute_list_returns() : external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Whether Delete was successful.'),
        ]);
    }
}
