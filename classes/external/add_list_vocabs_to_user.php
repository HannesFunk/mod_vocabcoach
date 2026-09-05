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
use mod_vocabcoach\vocab_manager;

class add_list_vocabs_to_user extends external_api {
    /**
     * Returns description of add_list_to_user() parameters.
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
     * Returns description of add_list_to_user() result value.
     *
     * @return external_description|null
     */
    public static function execute_returns(): ?external_description {
        return null;
    }

    /**
     * Adds vocabs from one list to user boxes.
     * @param int $listid
     * @param int $cmid
     */
    public static function execute(int $listid, int $cmid): void {
        global $USER;
        ['listid' => $listid, 'cmid' => $cmid] = self::validate_parameters(
            self::execute_parameters(),
            ['listid' => $listid, 'cmid' => $cmid]
        );

        $context = \core\context\module::instance($cmid);
        self::validate_context($context);

        require_capability('mod/vocabcoach:view', $context);

        $list = new vocablist($listid);
        $listinactivity = (int) $list->get('id') === $cmid;
        $canseelist = !$list->get('private') || (int)$list->get('createdby') === $USER->id;

        if (!$listinactivity || !$canseelist) {
            throw \core\exception\access_denied_exception("You don' have access to copy from that list.");
        }

        $vm = new vocab_manager();
        $vm->add_list_to_user_database($listid, $cmid);
    }
}