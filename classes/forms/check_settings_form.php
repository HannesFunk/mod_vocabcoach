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

/**
 * The main mod_vocabcoach configuration form.
 *
 * @package     check_settings_form
 * @copyright   2023 J. Funk, johannesfunk@outlook.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;
require_once("$CFG->libdir/formslib.php");

class check_settings_form extends moodleform {
    public function definition() : void {
        $mform = $this->_form;
        $mform->disable_form_change_checker();

        $mode_options = [
            'back'=>'Nach Englisch fragen',
            'front'=>'Nach Deutsch fragen',
            'random'=>'Zufällig', 'type'=>'Englische Vokabeln tippen'
        ];
        $mform->addElement('select', 'mode',
            get_string('mode', 'mod_vocabcoach'),
            $mode_options,
            array('id'=>'check-mode')
        );

    }

    public function toHtml() : string {
        return $this->_form->toHtml();
    }
}
