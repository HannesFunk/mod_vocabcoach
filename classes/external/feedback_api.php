<?php

namespace mod_vocabcoach\external;

global $CFG;
require_once("{$CFG->libdir}/externallib.php");
require(__DIR__.'/../vocabhelper.php');

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;

class feedback_api extends external_api {
    public static function get_feedback_line_parameters(): external_function_parameters {
        return new external_function_parameters([
                'achievement' => new external_value(PARAM_INT),
        ]);
    }

    public static function get_feedback_line_returns(): external_single_structure {
        return new external_single_structure([
                'line' => new external_value(PARAM_TEXT, 'a message'),
        ]);
    }

    public static function get_feedback_line($achievement): array {
        self::validate_parameters(self::get_feedback_line_parameters(), ['achievement' => $achievement]);

        global $DB;
        $ids = $DB->get_records('vocabcoach_feedback', ['type' => $achievement], '', 'id');
        $random_id = $ids[array_rand($ids)]->id;

        $feedback = $DB->get_record('vocabcoach_feedback', ['id' => $random_id], 'message');
        return ['line' => $feedback->message];
    }
}