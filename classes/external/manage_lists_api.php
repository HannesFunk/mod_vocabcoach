<?php

namespace mod_vocabcoach\external;

global $CFG;
require_once("{$CFG->libdir}/externallib.php");

use context_course;
use external_api;
use external_function_parameters;
use external_value;
use external_multiple_structure;
use external_single_structure;
use dml_exception;
use stdClass;

class manage_lists_api extends external_api {
    public static function get_lists_parameters() : external_function_parameters {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, VALUE_OPTIONAL),
            'userid' => new external_value(PARAM_INT, VALUE_OPTIONAL),
            'onlyOwnLists' => new external_value(PARAM_BOOL, VALUE_OPTIONAL)
        ]);
    }

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
            ])
        );
    }

    public static function get_lists ($cmid, $userid, $bOnlyOwnUser = false) : array|null{

        self::validate_parameters(self::get_lists_parameters(), ['cmid' => $cmid, 'userid' => $userid, 'onlyOwnLists' => $bOnlyOwnUser]);

        global $DB;

        try {
            $conditions = 'cmid = '.$cmid.' AND (private = 0 OR createdby = '.$userid.')';
            if ($bOnlyOwnUser) {
                $conditions .= ' AND createdby = '.$userid;
            }
            $records = $DB->get_records_sql("SELECT id, title, year, book, unit, createdby FROM {vocabcoach_lists} WHERE ".$conditions);
            //$records = $DB->get_records('vocabcoach_lists', $conditions, '', 'id, title, year, book, unit, createdby');
            $output = array();
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
            return null;
        }
    }

    public static function delete_list_parameters() : external_function_parameters {
        return new external_function_parameters([
            'listid' => new external_value(PARAM_INT, VALUE_REQUIRED)
        ]);
    }


    public static function delete_list_returns() : external_single_structure
    {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Whether Delete was successful.'),
        ]);
    }

    public static function delete_list($listid) : array {
        self::validate_parameters(self::delete_list_parameters(), ['listid' => $listid]);

        global $DB;
        try {
            $DB->delete_records('vocabcoach_lists', ['id'=>$listid]);
            $DB->delete_records('vocabcoach_list_contains', ['listid'=>$listid]);
        } catch (\dml_exception $e) {
            return ['success' => false];
        }

        return ['success'=>true];
    }

    public static function add_list_to_user_parameters() : external_function_parameters {
        return new external_function_parameters([
            'listid' => new external_value(PARAM_INT, VALUE_REQUIRED),
            'userid' => new external_value(PARAM_INT, VALUE_REQUIRED),
            'cmid' => new external_value(PARAM_INT, VALUE_REQUIRED),
        ]);
    }

    public static function add_list_to_user($listid, $userid, $cmid) : array {
        self::validate_parameters(self::add_list_to_user_parameters(), ['listid' => $listid, 'userid' => $userid, 'cmid' => $cmid]);

        global $DB;

        $time = strtotime('2000-01-01 00:00:00');

        $query = "SELECT id, vocabid FROM {vocabcoach_list_contains} list_contains 
                                WHERE list_contains.listid = $listid 
                                AND list_contains.vocabid NOT IN
       (SELECT vocabID FROM {vocabcoach_vocabdata} vocabdata WHERE userid = $userid AND cmid = $cmid)";

        try {
            $records = $DB->get_records_sql($query);
            $insert_array = array();
            foreach (array_values($records) as $record) {
                $insert = new stdClass();
                $insert->vocabid = $record->vocabid;
                $insert->userid = $userid;
                $insert->cmid = $cmid;
                $insert->stage = 1;
                $insert->lastchecked = $time;
                $insert_array[] = $insert;
            }
            $DB->insert_records('vocabcoach_vocabdata', $insert_array);
            return ['success' => true];
        } catch (dml_exception $e) {
            return ['success' => false];
        }
    }

    public static function add_list_to_user_returns() : external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Whether Delete was successful.'),
        ]);
    }

    public static function distribute_list_parameters() : external_function_parameters {
        return new external_function_parameters([
                'listid' => new external_value(PARAM_INT, VALUE_REQUIRED),
                'cmid' => new external_value(PARAM_INT, VALUE_REQUIRED),
        ]);
    }

    public static function distribute_list($listid, $cmid) : array {
        self::validate_parameters(self::distribute_list_parameters(), ['listid' => $listid, 'cmid' => $cmid]);

        $cm = get_coursemodule_from_id('vocabcoach', $cmid, 0, false, MUST_EXIST);
        $context = context_course::instance($cm->course);

        $students = get_enrolled_users($context);

        foreach ($students as $student) {
            self::add_list_to_user($listid, $student->id, $cmid);
        }

        return ['success' => true];
    }

        public static function distribute_list_returns() : external_single_structure {
        return new external_single_structure([
                'success' => new external_value(PARAM_BOOL, 'Whether Delete was successful.'),
        ]);
    }

}