<?php

namespace mod_vocabcoach\external;

global $CFG;
require_once("{$CFG->libdir}/externallib.php");

use external_api;
use external_function_parameters;
use external_value;
use external_multiple_structure;
use external_single_structure;
class manage_lists_api extends external_api {
    public static function get_lists_parameters() : external_function_parameters {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, VALUE_OPTIONAL)
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
            'creator' => new external_value(PARAM_TEXT),
            ])
        );
    }

    public static function get_lists ($cmid = 0) : array|null{

        self::validate_parameters(self::get_lists_parameters(), ['cmid' => $cmid]);

        global $DB;

        try {
            $records = $DB->get_records('mod_vocabcoach_lists', ['cmid'=>$cmid], '', 'id, title, year, book, unit, createdby');
            $output = array();
            foreach ($records as $record) {
                $query = "SELECT COUNT(DISTINCT(vocabid)) FROM {mod_vocabcoach_list_contains} WHERE listid = ".$record->id.";";
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

    public static function delete_list_returns() : external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Whether Delete was successful.'),
        ]);
    }

    public static function delete_list($listid) : array {
        self::validate_parameters(self::delete_list_parameters(), ['listid' => $listid]);

        global $DB;
        try {
            $DB->delete_records('mod_vocabcoach_lists', ['id'=>$listid]);
            $DB->delete_records('mod_vocabcoach_list_contains', ['listid'=>$listid]);
        } catch (\dml_exception $e) {
            return ['success' => false];
        }

        return ['success'=>true];



    }
}