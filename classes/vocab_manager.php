<?php
// This file is part of Moodle Course Rollover Plugin
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace mod_vocabcoach;

use dml_exception;
use mod_vocabcoach\local\vocab;
use stdClass;

/**
 * Vocab-Manager. Manages database-user interactions.
 *
 * @package   mod_vocabcoach
 * @copyright 2023 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Johannes Funk
 */
class vocab_manager {
    /**
     * Construct the class.
     * @param int $userid
     */
    public function __construct() {
    }

    /**
     * Inserts a new vocab item into the database.
     *
     * @param object $vocab a vocab item.
     * @return int The ID of the inserted vocab item.
     * @throws dml_exception
     */
    public function insert_vocab(object $vocab): int {
        global $USER;
        if ($this->does_vocab_exist($vocab)) {
            return $this->determine_id($vocab);
        } else {
            $vocab->createdby = $USER->id;
            return $this->create_record($vocab);
        }
    }

    /**
     * Checks whether a given vocab already exists in the database.
     * @param object $vocab
     * @return bool
     */
    private function does_vocab_exist(object $vocab): bool {
        global $DB;
        $condition1 = $DB->sql_compare_text('front') . '  = ' . $DB->sql_compare_text(':front');
        $condition2 = $DB->sql_compare_text('back') . ' = ' . $DB->sql_compare_text(':back');
        $query = "SELECT COUNT(*) FROM {vocabcoach_vocab} WHERE $condition1 AND $condition2";
        try {
            $count = $DB->count_records_sql(
                $query,
                ['front' => $vocab->front, 'back' => $vocab->back]
            );
            return $count > 0;
        } catch (dml_exception $e) {
            return false;
        }
    }

    public function get_or_create_vocab(string $front, string $back): vocab {
        global $USER;
        $existing = vocab::get_record(['front' => $front, 'back' => $back]);
        if ($existing) {
            return $existing;
        }

        $newvocab = [
            'front' => $front,
            'back' => $back,
            'createdby' => $USER->id,
        ];
        $vocab = new vocab(0, (object) $newvocab);
        $vocab->create();
        return $vocab;
    }

    /**
     * Creates a new vocab item.
     * @param object $vocab
     * @return int The ID of the created element.
     * @throws dml_exception
     */
    public function create_record(object $vocab): int {
        global $DB;

        try {
            return $DB->insert_record('vocabcoach_vocab', $vocab);
        } catch (dml_exception $e) {
            throw $e;
        }
    }

    /**
     * Find the ID of a given vocab.
     * @param object $vocab
     * @return int
     * @throws dml_exception
     */
    private function determine_id(object $vocab): int {
        global $DB;

        $condition1 = $DB->sql_compare_text('front') . '  = ' . $DB->sql_compare_text(':front');
        $condition2 = $DB->sql_compare_text('back') . ' = ' . $DB->sql_compare_text(':back');

        $query = "SELECT id FROM {vocabcoach_vocab} WHERE $condition1 AND $condition2";
        $records = $DB->get_records_sql($query, ['front' => $vocab->front, 'back' => $vocab->back], 0, 1);
        return array_values($records)[0]->id;
    }

    /**
     * Adds a vocab item to user database.
     * @param int $vocabid
     * @param int $cmid
     * @throws dml_exception
     * @return bool
     */
    public function add_vocabs_to_user(array $vocabs, int $cmid, ?int $userid = null): void {
        global $DB;
        global $USER;

        if (!$userid) {
            $userid = $USER->id;
        }

        $ids = array_map(
            fn($vocab) => (int) $this->get_or_create_vocab($vocab['front'], $vocab['back'])->get('id'),
            $vocabs
        );
        $ids = array_unique($ids);

        [$insql, $params] = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED);
        $params['userid']  = $userid;
        $params['cmid'] = $cmid;
        $existing = $DB->get_fieldset_select(
            'vocabcoach_vocabdata',
            'vocabid',
            "userid = :userid AND cmid = :cmid AND vocabid $insql",
            $params
        );

        $toadd = array_diff($ids, array_map('intval', $existing));

        if (!$toadd) {
            return;
        }

        $rows = array_map(
            fn($vocabid) => [
                'userid' => $userid,
                'cmid' => $cmid,
                'vocabid' => $vocabid,
                'stage' => 1,
                'lastchecked' => strtotime('2000-01-01 00:00:00'),
            ],
            $toadd
        );

        $DB->insert_records('vocabcoach_vocabdata', $rows);
    }

    /**
     * Creates a new list.
     * @param array $listinfo
     * @return int
     */
    public function add_list(array $listinfo): int {
        global $DB;

        try {
            return $DB->insert_record('vocabcoach_lists', $listinfo);
        } catch (dml_exception $e) {
            return -1;
        }
    }

    /**
     * Adds a vocab item to a lsit.
     * @param int $vocabid
     * @param int $listid
     * @return bool
     */
    public function add_vocab_to_list(int $vocabid, int $listid): bool {
        global $DB;
        $conditions = [
            'vocabid' => $vocabid,
            'listid' => $listid,
        ];

        try {
            if ($DB->count_records('vocabcoach_list_contains', $conditions) > 0) {
                return false;
            }
            $DB->insert_record('vocabcoach_list_contains', $conditions);
        } catch (dml_exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Removes a vocab item from a list.
     * @param int $vocabid
     * @param int $listid
     * @return bool
     */
    public function remove_vocab_from_list(int $vocabid, int $listid): bool {
        global $DB;

        try {
            $DB->delete_records('vocabcoach_list_contains', ['vocabid' => $vocabid, 'listid' => $listid]);
        } catch (dml_exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Adds an entire list to a user box.
     * @param int $listid
     * @param int $cmid
     * @return bool
     */
    public function add_list_to_user_database(int $listid, int $cmid): bool {
        global $DB, $USER;
        $userid = $USER->id;

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
        } catch (dml_exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Updates information for a list.
     * @param int $listid
     * @param array $vocabarray
     * @return void
     * @throws dml_exception
     */
    public function edit_list(int $listid, array $vocabarray): void {
        global $DB;

        foreach ($vocabarray as $vocab) {
            if ($vocab->correct_everywhere) {
                $DB->update_record('vocabcoach_vocab', $vocab);
            } else {
                $this->remove_vocab_from_list($vocab->id, $listid);
                $newid = $this->insert_vocab($vocab);
                $this->add_vocab_to_list($newid, $listid);
            }
        }
    }

    /**
     * Checks whether the current user is owner of a list.
     * @param int $userid
     * @param int $listid
     * @return bool
     * @throws dml_exception
     */
    public function user_owns_list(int $userid, int $listid): bool {
        global $DB;
        $record = $DB->get_record('vocabcoach_lists', ['id' => $listid], 'createdby');
        return $record->createdby == $userid;
    }

    /**
     * Removes a vocab item from the database if it is not contained in any list or userbox.
     * @param int $vocabid
     * @return void
     * @throws dml_exception
     */
    public static function remove_if_unused(int $vocabid): void {
        global $DB;

        $containedinlist = self::is_contained_in_list($vocabid);
        $containedinuserbox = self::is_contained_in_userbox($vocabid);

        if ($containedinlist || $containedinuserbox) {
            return;
        }

        $DB->delete_records('vocabcoach_vocab', ['id' => $vocabid]);
    }

    /**
     * Checks whether a vocab item is contained in any list.
     * @param int $vocabid
     * @return bool
     * @throws dml_exception
     */
    public static function is_contained_in_list(int $vocabid): bool {
        global $DB;

        $count = $DB->count_records('vocabcoach_list_contains', ['vocabid' => $vocabid]);
        return $count > 0;
    }

    /**
     * Checks whether a vocab item is contained in any userbox.
     * @param int $vocabid
     * @return bool
     * @throws dml_exception
     */
    public static function is_contained_in_userbox(int $vocabid): bool {
        global $DB;

        $count = $DB->count_records('vocabcoach_vocabdata', ['vocabid' => $vocabid]);
        return $count > 0;
    }
}
