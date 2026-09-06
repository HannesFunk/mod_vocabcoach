<?php
// This file is part of Moodle - http://moodle.org
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

namespace mod_vocabcoach\external;

use core_external\external_api;
use core_external\external_description;
use core_external\external_function_parameters;
use core_external\external_value;
use mod_vocabcoach\local\vocablist;

/**
 * delete_list: Webservice to delete a vocablist.
 *
 * @package   mod_vocabcoach
 * @copyright 2023 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Johannes Funk
 */
class delete_list extends external_api {
    /**
     * Returns description of execute() parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'listid' => new external_value(PARAM_INT, 'ID of the list to be deleted', VALUE_REQUIRED),
            'cmid' => new external_value(PARAM_INT, 'Course module ID', VALUE_REQUIRED),
        ]);
    }

    /**
     * Returns description of execute() result value.
     *
     * @return external_description|null
     */
    public static function execute_returns(): ?external_description {
        return null;
    }

    /**
     * Deletes a list
     * @param int $listid
     * @param int $cmid
     */
    public static function execute(int $listid, int $cmid): void {
        global $DB, $USER;

        ['listid' => $listid, 'cmid' => $cmid] = self::validate_parameters(
            self::execute_parameters(),
            ['listid' => $listid, 'cmid' => $cmid]
        );
        $context = \core\context\module::instance($cmid);
        self::validate_context($context);

        $candelete = has_capability('mod/vocabcoach:delete_lists', $context);
        $list = new vocablist($listid);
        $isowner = $list->get('createdby') === (int)$USER->id;

        if (!$candelete && !$isowner) {
            throw new \core\exception\moodle_exception('nopermissiontodeletelist', 'mod_voacabcoach');
        }

        $DB->delete_records('vocabcoach_lists', ['id' => $listid]);
        $DB->delete_records('vocabcoach_list_vocabs', ['listid' => $listid]);
    }
}