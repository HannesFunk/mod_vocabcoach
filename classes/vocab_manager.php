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


/**
 * @package     mod_vocabcoach
 * @author      J. Funk
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_vocabcoach;

use dml_exception;
use stdClass;

class vocab_manager {
    private int $userid;

    public function __construct($userid) {
        $this->userid = $userid;
    }

    public function insert_vocab($vocab, int $userid) : int {
        $front = $vocab->front;
        $back = $vocab->back;
        if($this->does_vocab_exist($front, $back)) {
            return $this->determine_id($front, $back);
        } else {
            return $this->create_record($front, $back, $userid);
        }
    }

    function does_vocab_exist(string $front, string $back) : bool {
        global $DB;
        $condition1 = $DB->sql_compare_text('front') . ' = ' . $DB->sql_compare_text(':front');
        $condition2 = $DB->sql_compare_text('back') . ' = ' . $DB->sql_compare_text(':back');

        $query = "SELECT COUNT(*) FROM {mod_vocabcoach_vocab} WHERE $condition1 AND $condition2";
        $count = $DB->count_records_sql($query, array('front'=>$front, 'back'=>$back));
        return $count > 0;
    }

    function create_record ($front, $back, $userid) : int {
        global $DB;
        $new_vocab = new stdClass();
        $new_vocab->front = $front;
        $new_vocab->back = $back;
        $new_vocab->createdby = $userid;

        try {
            return $DB->insert_record('mod_vocabcoach_vocab', $new_vocab, true);
        } catch (dml_exception $e) {
            return -1;
        }
    }

    function determine_id($front, $back) : int {
        global $DB;
        $condition1 = $DB->sql_compare_text('front') . ' = ' . $DB->sql_compare_text(':front');
        $condition2 = $DB->sql_compare_text('back') . ' = ' . $DB->sql_compare_text(':back');

        $query = "SELECT id FROM {mod_vocabcoach_vocab} WHERE $condition1 AND $condition2";
        $records = $DB->get_records_sql($query, array('front'=>$front, 'back'=>$back), 0,1);
        return array_values($records)[0]->id;
    }

    function add_vocab_to_user(int $vocabid, int $userid, int $cmid) : bool {
        global $DB;

        if ($DB->count_records_select('mod_vocabcoach_vocabdata',
                "vocabid = ? AND userid = ? AND cmid = ?", [$vocabid, $userid, $cmid]) > 0) {
            return true; // user already has this vocab -> we're done
        } else {
            $new_vocabdata = new stdClass();
            $new_vocabdata->userid = $userid;
            $new_vocabdata->vocabid = $vocabid;
            $new_vocabdata->cmid = $cmid;
            $new_vocabdata->stage = 1;
            $new_vocabdata->lastchecked = strtotime('2000-01-01 00:00:00');

            try {
                $DB->insert_record('mod_vocabcoach_vocabdata', $new_vocabdata, false);
                return true;
            } catch (dml_exception $e) {
                die($e->getMessage());
            }
        }
    }

   function add_list(array $list_info) :int {
        global $DB;

        try {
            $list_id = $DB->insert_record('mod_vocabcoach_lists', $list_info, true);
        } catch (dml_exception $e) {
            return -1;
        }

        return $list_id;
   }

   function add_vocab_to_list (int $vocabid, int $listid) : bool {
        global $DB;

        try {
            $DB->insert_record('mod_vocabcoach_list_contains', ['vocabid'=>$vocabid, 'listid'=>$listid]);
        } catch (dml_exception $e) {
            return false;
        }
        return true;
   }

   function remove_vocab_from_list (int $vocabid, int $listid) : bool {
        global $DB;

        try {
            $DB->delete_records('mod_vocabcoach_list_contains', ['vocabid'=>$vocabid, 'listid'=>$listid]);
        } catch (dml_exception $e) {
            return false;
        }
        return true;
   }

   function add_list_to_user_database (int $listid, int $userid, int $cmid) : bool {
        global $DB;

        $time = strtotime('2000-01-01 00:00:00');

        $query = "SELECT id, vocabid FROM {mod_vocabcoach_list_contains} list_contains 
                                WHERE list_contains.listid = $listid 
                                AND list_contains.vocabid NOT IN
       (SELECT vocabID FROM {mod_vocabcoach_vocabdata} vocabdata WHERE userid = $userid AND cmid = $cmid)";

        try {
            $records = $DB->get_records_sql($query);
            $insertarray = array();
            foreach (array_values($records) as $record) {
                $insert = new stdClass();
                $insert->vocabid = $record->vocabid;
                $insert->userid = $userid;
                $insert->cmid = $cmid;
                $insert->stage = 1;
                $insert->lastchecked = $time;
                $insertarray[] = $insert;
            }
            $DB->insert_records('mod_vocabcoach_vocabdata', $insertarray);
        } catch (dml_exception $e) {
            return false;
        }

        return true;
   }

   public function edit_list($listid, $vocabarray) {
        global $DB;

        foreach($vocabarray as $vocab) {
            if ($vocab->correct_everywhere) {
                $DB->update_record('mod_vocabcoach_vocab', $vocab);
            } else {
                $new_id = $this->insert_vocab($vocab, $this->userid);
                $this->add_vocab_to_list($new_id, $listid);
                $this->remove_vocab_from_list($vocab->id, $listid);
            }
        }


   }
}