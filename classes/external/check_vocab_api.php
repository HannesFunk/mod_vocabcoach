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
require_once(__DIR__.'/../vocabhelper.php');
require_once(__DIR__.'/../activity_tracker.php');

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_multiple_structure;
use vocabhelper;

class check_vocab_api extends external_api {
    public static function update_vocab_parameters() : external_function_parameters {
        return new external_function_parameters([
            'dataid' => new external_value(PARAM_INT),
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

        self::validate_parameters(self::update_vocab_parameters(), ['dataid' => $dataid, 'userid' => $userid, 'known' => $known]);

        try {
            $record = $DB->get_record_sql("SELECT * FROM {vocabcoach_vocabdata} WHERE id = ?;", [$dataid], MUST_EXIST);

            $record->stage = $known ? min($record->stage + 1, 5) : 1;
            $record->lastchecked = time();

            $DB->update_record('vocabcoach_vocabdata', $record);
        } catch (\dml_exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }

        return ['success' => true, 'message' => 'That worked.'];
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
        self::validate_parameters(self::get_user_vocabs_parameters(),
            ['userid' => $userid, 'cmid' => $cmid, 'stage' => $stage, 'force' => $force]);

        $vocabhelper = new vocabhelper($cmid);
        $days = $vocabhelper->boxtimes[$stage];
        $mintimestamp = $vocabhelper->old_timestamp($days);

        $query = "SELECT vd.ID AS dataid, front, back, third
                FROM {vocabcoach_vocab} vocab
                JOIN {vocabcoach_vocabdata} vd ON vocab.ID = vd.vocabID
               WHERE vd.userID= ? AND vd.stage = ? AND vd.cmid = ?";
        if (!$force) {
            $query .= "AND vd.lastchecked < ?;";
        } else {
            $query .= ';';
        }
        try {
            $output = $DB->get_records_sql($query, [$userid, $stage, $cmid, $mintimestamp]);
        } catch (\dml_exception $e) {
            return [$e->getMessage()];
        }

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
                'third' => new external_value(PARAM_TEXT),
            ])
        );
    }

    public static function get_list_vocabs(int $listid) : array {
        self::validate_parameters(self::get_list_vocabs_parameters(), ['listid' => $listid]);

        global $DB;

        $query = "SELECT vocab.ID AS dataid, front, back, third FROM {vocabcoach_vocab} vocab
            INNER JOIN {vocabcoach_list_contains} list_contains ON  list_contains.vocabID = vocab.ID
            WHERE list_contains.listID = $listid;";
        try {
            $output = $DB->get_records_sql($query);
            return array_values($output);
        } catch (\dml_exception $e) {
            return [];
        }
    }

    public static function log_checked_vocabs_parameters() : external_function_parameters {
        return new external_function_parameters([
                'cmid' => new external_value(PARAM_INT),
                'userid' => new external_value(PARAM_INT),
                'details' => new external_value(PARAM_TEXT),
        ]);
    }

    public static function log_checked_vocabs_returns() : external_single_structure {
        return new external_single_structure([
                'success' => new external_value(PARAM_BOOL, 'whether the update was successful.'),
                'message' => new external_value(PARAM_TEXT, 'a message'),
        ]);
    }

    public static function log_checked_vocabs(int $cmid, int $userid, $details) :array {
        self::validate_parameters(self::log_checked_vocabs_parameters(),
                ['cmid' => $cmid, 'userid' => $userid, 'details' => $details]);

        $at = new \activity_tracker($userid, $cmid);
        $at->log($at->typesalways['ACT_CHECKED_VOCAB'], $details);

        return ['success' => true, 'message' => 'Logged successfully.'];
    }

    public static function remove_vocab_from_user_parameters() : external_function_parameters {
        return new external_function_parameters([
                'dataid' => new external_value(PARAM_INT),
        ]);
    }

    public static function remove_vocab_from_user_returns() : external_single_structure {
        return new external_single_structure([
                'success' => new external_value(PARAM_BOOL, 'whether the removal was successful.'),
                'message' => new external_value(PARAM_TEXT, 'a message'),
        ]);
    }

    public static function remove_vocab_from_user(int $dataid) :array {
        self::validate_parameters(self::remove_vocab_from_user_parameters(),
                ['dataid' => $dataid]);

        global $DB;

        $DB->delete_records('vocabcoach_vocabdata', ['id' => $dataid]);

        return ['success' => true, 'message' => 'Removed successfully.'];

    }

    public static function get_class_total_parameters() : external_function_parameters {
        return new external_function_parameters([
                'cmid' => new external_value(PARAM_INT),
                'courseid' => new external_value(PARAM_INT),
        ]);
    }

    public static function get_class_total_returns() : external_single_structure {
        return new external_single_structure([
                'success' => new external_value(PARAM_BOOL, 'whether the removal was successful.'),
                'message' => new external_value(PARAM_TEXT, 'a message'),
                'total' => new external_value(PARAM_INT, 'the total number of due vocab'),
        ]);
    }

    public static function get_class_total(int $cmid, int $courseid) :array {
        self::validate_parameters(self::get_class_total_parameters(),
                ['cmid' => $cmid, 'courseid' => $courseid]);

        $subquery = "SELECT userid FROM {user_enrolments} ue JOIN {enrol} en ON ue.enrolid = en.id
                JOIN {user} uu ON uu.id = ue.userid WHERE en.courseid = $courseid";
        $total = self::get_due_count($cmid, $subquery);
        return ['success' => true, 'message' => 'Removed successfully.', 'total' => $total];
    }

    private static function get_due_count (int $cmid, string $useridlist) : int {
        global $DB;
        $vocabhelper = new vocabhelper($cmid);
        $boxconditions = $vocabhelper->get_sql_box_conditions();

        $query = "SELECT COUNT(*) AS total FROM {vocabcoach_vocabdata} vd
             WHERE userid IN ($useridlist) AND cmid = $cmid AND ($boxconditions)";
        $record = $DB->get_record_sql($query);
        return $record->total;
    }
}
