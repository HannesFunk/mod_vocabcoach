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

namespace mod_vocabcoach\external;

defined('MOODLE_INTERNAL') || die();
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
        $randomid = $ids[array_rand($ids)]->id;

        $feedback = $DB->get_record('vocabcoach_feedback', ['id' => $randomid], 'message');
        return ['line' => $feedback->message];
    }
}
