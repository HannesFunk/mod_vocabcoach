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

use core_external\external_api;
use core_external\external_description;
use core_external\external_function_parameters;
use core_external\external_value;
use invalid_parameter_exception;
use mod_vocabcoach\list_manager;

/**
 * Manage-lists-API: manages lists.
 *
 * @package   mod_vocabcoach
 * @copyright 2023 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Johannes Funk
 */
class distribute_list extends external_api {
    /**
     * Returns description of distribute_list() parameters.
     *
     * @return external_function_parameters
     */

    public static function execute_parameters(): external_function_parameters {
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
    public static function execute(int $listid, int $cmid): void {
        ['listid' => $listid, 'cmid' => $cmid] = self::validate_parameters(
            self::execute_parameters(),
            ['listid' => $listid, 'cmid' => $cmid]
        );

        $context = \core\context\module::instance($cmid);
        self::validate_context($context);
        require_capability('mod/vocabcoach:distribute_lists', $context);

        $lm = new list_manager($listid, $cmid);
        $lm->distribute_to_students();
    }

    /**
     * Returns description of execute() result value.
     *
     * @return external_description|null
     */
    public static function execute_returns(): ?external_description {
        return null;
    }
}
