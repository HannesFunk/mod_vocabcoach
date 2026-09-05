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

use context_course;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use dml_exception;
use invalid_parameter_exception;
use mod_vocabcoach\local\vocablist;
use stdClass;

/**
 * Manage-lists-API: manages lists.
 *
 * @package   mod_vocabcoach
 * @copyright 2023 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Johannes Funk
 */
class lists_api extends external_api {
    /**
     * Returns description of delete_list() parameters.
     *
     * @return external_function_parameters
     */
    public static function delete_list_parameters(): external_function_parameters {
        return new external_function_parameters([
            'listid' => new external_value(PARAM_INT, 'ID of the list to be deleted', VALUE_REQUIRED),
        ]);
    }

    /**
     * Returns description of delete_list() result value.
     *
     * @return external_single_structure
     */
    public static function delete_list_returns(): external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Whether Delete was successful.'),
        ]);
    }

    /**
     * Deletes a list
     * @param int $listid
     * @param int $cmid
     * @throws invalid_parameter_exception
     */
    public static function delete_list(int $listid, int $cmid): void {
        global $DB, $USER;

        ['listid' => $listid] = self::validate_parameters(self::delete_list_parameters(), ['listid' => $listid]);
        $context = \core\context\module::instance($cmid);

        $candelete = has_capability('mod/vocabcoach:delete_lists', $context);
        $list = new vocablist($listid);
        $isowner = $list->get('id') === (int) $USER->id;

        if (!$candelete && !$isowner) {
            return;
        }

        $DB->delete_records('vocabcoach_lists', ['id' => $listid]);
        $DB->delete_records('vocabcoach_list_vocabs', ['listid' => $listid]);
    }


    /**
     * Returns description of distribute_list() parameters.
     *
     * @return external_function_parameters
     */
    public static function distribute_list_parameters(): external_function_parameters {
        return new external_function_parameters([
                'listid' => new external_value(PARAM_INT, 'ID of the list', VALUE_REQUIRED),
                'cmid' => new external_value(PARAM_INT, 'course module ID', VALUE_REQUIRED),
        ]);
    }

    /**
     * Adds vocabs from a list to all user boxes in a course.
     * @param int $listid
     * @param int $cmid
     * @return true[]
     * @throws invalid_parameter_exception
     */
    public static function distribute_list(int $listid, int $cmid): array {
        self::validate_parameters(self::distribute_list_parameters(), ['listid' => $listid, 'cmid' => $cmid]);

        $cm = get_coursemodule_from_id('vocabcoach', $cmid, 0, false, MUST_EXIST);
        $context = context_course::instance($cm->course);

        $students = get_enrolled_users($context);

        foreach ($students as $student) {
            self::add_list_to_user($listid, $student->id, $cmid);
        }

        return ['success' => true];
    }

    /**
     * Returns description of distribute_list() result value.
     *
     * @return external_single_structure
     */
    public static function distribute_list_returns(): external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Whether Delete was successful.'),
        ]);
    }
}
