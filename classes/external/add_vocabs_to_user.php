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

use core_external\external_function_parameters;
use core_external\external_value;
use mod_vocabcoach\vocab_manager;

class add_vocabs_to_user extends \core_external\external_api {
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'course module id.'),
            'vocabs' => new \core_external\external_multiple_structure(
                new \core_external\external_single_structure([
                    'id' => new external_value(PARAM_INT, 'Vocab ID.', VALUE_DEFAULT, -1),
                    'front' => new external_value(PARAM_TEXT, 'Front'),
                    'back' => new external_value(PARAM_TEXT, 'Back'),
                ]),
                'Vocabs to be added.'
            )
        ]);
    }

    public static function execute_returns(): ?external_value {
        return null;
    }

    public static function execute(int $cmid, array $vocabs): void {
        global $USER;
        ['cmid' => $cmid, 'vocabs' => $vocabs] = self::validate_parameters(
            self::execute_parameters(),
            ['cmid' => $cmid, 'vocabs' => $vocabs]
        );
        $userid = $USER->id;
        $context = \core\context\module::instance($cmid);
        self::validate_context($context);

        $vm = new vocab_manager();
        $vm->add_vocabs_to_user($vocabs, $cmid);
    }
}
