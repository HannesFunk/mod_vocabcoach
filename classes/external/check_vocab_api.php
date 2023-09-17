<?php

namespace mod_vocabcoach\external;

global $CFG;
require_once("{$CFG->libdir}/externallib.php");
require(__DIR__.'/../vocabhelper.php');

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_multiple_structure;
use vocabhelper;

class check_vocab_api extends external_api {
    public static function update_vocab_parameters() : external_function_parameters {
        return new external_function_parameters([
            'dataid'=> new external_value(PARAM_INT),
            'userid' => new external_value(PARAM_INT),
            'known' => new external_value(PARAM_BOOL),
        ]);
    }

    public static function update_vocab_returns() : external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'whether the update was successful.'),
            'message' => new external_value(PARAM_TEXT, 'a message'),
        ]);
    }

    public static function update_vocab($dataid, $userid, $known) : array {
        global $DB;

        self::validate_parameters(self::update_vocab_parameters(), ['dataid'=>$dataid, 'userid'=>$userid, 'known'=>$known]);

        try {
            $record = $DB->get_record_sql("SELECT * FROM {vocabcoach_vocabdata} WHERE id = ?;", [$dataid], MUST_EXIST);

            $record->stage = $known ? max($record->stage + 1, 5) : 1;
            $record->lastchecked = time();

            $DB->update_record('vocabcoach_vocabdata', $record);
        } catch (\dml_exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }

        return ['success'=>true, 'message'=>'That worked.'];
    }

    public static function get_user_vocabs_parameters() : external_function_parameters {
        return new external_function_parameters([
            'userid' => new external_value(PARAM_INT, VALUE_REQUIRED),
            'cmid' => new external_value(PARAM_INT, VALUE_REQUIRED),
            'stage' => new external_value(PARAM_INT, VALUE_REQUIRED),
            'force' => new external_value(PARAM_BOOL, VALUE_REQUIRED),
        ]);
    }

    public static function get_user_vocabs_returns() : external_multiple_structure {
        return self::vocab_returns();
    }

    public static function get_user_vocabs($userid, $cmid, $stage, $force) : array {
        global $DB;
        self::validate_parameters(self::get_user_vocabs_parameters(), ['userid'=>$userid, 'cmid'=>$cmid, 'stage'=>$stage, 'force' => $force]);

        $vocabhelper = new vocabhelper($cmid);
        $days = $vocabhelper->BOXES_TIMES[$stage];
        $min_timestamp = $vocabhelper->old_timestamp($days);

        $query = "SELECT vd.ID AS dataid, front, back 
                FROM {vocabcoach_vocab} vocab 
                JOIN {vocabcoach_vocabdata} vd ON vocab.ID = vd.vocabID 
               WHERE vd.userID= ? AND vd.stage = ? AND vd.cmid = ?";
        if (!$force) {
            $query .= "AND vd.lastchecked < ?;";
        } else {
            $query .= ';';
        }
        $output =  $DB->get_records_sql($query, [$userid, $stage, $cmid, $min_timestamp]);

        return array_values($output);
    }

    public static function get_list_vocabs_parameters() : external_function_parameters {
        return new external_function_parameters([
            'listid' => new external_value(PARAM_INT, VALUE_REQUIRED),
        ]);
    }

    public static function get_list_vocabs_returns() : external_multiple_structure {
        return self::vocab_returns();
    }

    public static function vocab_returns() : external_multiple_structure {
        return new external_multiple_structure(
            new external_single_structure([
                'dataid' => new external_value(PARAM_INT),
                'front' => new external_value(PARAM_TEXT),
                'back' => new external_value(PARAM_TEXT),
            ])
        );
    }

    public static function get_list_vocabs(int $listid) : array {
        self::validate_parameters(self::get_list_vocabs_parameters(), ['listid'=>$listid]);

        global $DB;

        $query = "SELECT vocab.ID AS dataid, front, back FROM {vocabcoach_vocab} vocab 
            INNER JOIN {vocabcoach_list_contains} list_contains ON  list_contains.vocabID = vocab.ID
            WHERE list_contains.listID = $listid;";
        try {
            $output =  $DB->get_records_sql($query);
            return array_values($output);
        } catch(\dml_exception) {
            return [];
        }
    }
}
