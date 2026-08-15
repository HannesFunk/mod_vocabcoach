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
require_once(__DIR__ . '/../user_preferences.php');

use core_external\external_function_parameters;
use core_external\external_api;
use core_external\external_single_structure;
use core_external\external_value;

/**
 * External API for check mode preferences.
 * @package   mod_vocabcoach
 * @copyright 2023 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Johannes Funk
 */
class checkprefs_api extends external_api {
    /**
     * Describes the parameters.
     *
     * @return external_function_parameters
     */
    public static function set_mode_parameters(): external_function_parameters {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'Course module id', VALUE_REQUIRED),
            'userid' => new external_value(PARAM_INT, 'User id', VALUE_REQUIRED),
            'mode' => new external_value(PARAM_ALPHANUMEXT, 'Mode (front, back, random, type)', VALUE_REQUIRED),
        ]);
    }

    /**
     * Sets the check mode for a user in a specific course module.
     * @param int $cmid Course module id
     * @param int $userid User id
     * @param string $mode Mode (front, back, random, type)
     *
     * @return array Success flag
     */
    public static function set_mode(int $cmid, int $userid, string $mode): array {
        self::validate_parameters(self::set_mode_parameters(), ['cmid' => $cmid, 'userid' => $userid, 'mode' => $mode]);
        $userprefs = new \mod_vocabcoach\user_preferences($cmid, $userid);
        $userprefs->set_mode($mode);
        return ['success' => true];
    }

    /**
     * Describes the return structure of the service.
     *
     * @return external_single_structure the return structure
     */
    public static function set_mode_returns(): external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Success flag'),
        ]);
    }

    /**
     * Describes the parameters.
     *
     * @return external_function_parameters
     */
    public static function set_email_notifications_parameters(): external_function_parameters {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'Course module id', VALUE_REQUIRED),
            'userid' => new external_value(PARAM_INT, 'User id', VALUE_REQUIRED),
            'enabled' => new external_value(PARAM_BOOL, 'Whether email notifications are enabled', VALUE_REQUIRED),
        ]);
    }

    /**
     * Sets the email notification preference for a user in a specific course module.
     * @param int $cmid Course module id
     * @param int $userid User id
     * @param bool $enabled Whether email notifications are enabled
     *
     * @return array Success flag
     */
    public static function set_email_notifications(int $cmid, int $userid, bool $enabled): array {
        self::validate_parameters(self::set_email_notifications_parameters(), [
            'cmid' => $cmid,
            'userid' => $userid,
            'enabled' => $enabled,
        ]);
        $userprefs = new \mod_vocabcoach\user_preferences($cmid, $userid);
        $userprefs->set_email_notifications($enabled);
        return ['success' => true];
    }

    /**
     * Describes the return structure of the service.
     *
     * @return external_single_structure the return structure
     */
    public static function set_email_notifications_returns(): external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Success flag'),
        ]);
    }
}
