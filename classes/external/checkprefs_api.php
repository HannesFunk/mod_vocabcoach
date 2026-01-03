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
require_once(__DIR__ . '/../check_preferences.php');

use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use mod_vocabcoach\check_preferences;

/**
 * External API for check mode preferences.
 */
class checkprefs_api extends external_api {
    public static function set_mode_parameters(): external_function_parameters {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'Course module id', VALUE_REQUIRED),
            'userid' => new external_value(PARAM_INT, 'User id', VALUE_REQUIRED),
            'mode' => new external_value(PARAM_ALPHANUMEXT, 'Mode (front, back, random, type)', VALUE_REQUIRED),
        ]);
    }

    public static function set_mode(int $cmid, int $userid, string $mode): array {
        global $USER;
        self::validate_parameters(self::set_mode_parameters(), ['cmid' => $cmid, 'userid' => $userid, 'mode' => $mode]);
        check_preferences::set_mode($cmid, $userid, $mode);
        return ['success' => true];
    }

    public static function set_mode_returns(): external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Success flag'),
        ]);
    }

    public static function get_mode_parameters(): external_function_parameters {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'Course module id', VALUE_REQUIRED),
            'userid' => new external_value(PARAM_INT, 'User id', VALUE_REQUIRED),
        ]);
    }

    public static function get_mode(int $cmid, int $userid): array {
        global $USER;
        self::validate_parameters(self::get_mode_parameters(), ['cmid' => $cmid, 'userid' => $userid]);
        $mode = check_preferences::get_mode($cmid, $userid);
        return ['mode' => $mode];
    }

    public static function get_mode_returns(): external_single_structure {
        return new external_single_structure([
            'mode' => new external_value(PARAM_ALPHANUMEXT, 'Current mode'),
        ]);
    }
}
