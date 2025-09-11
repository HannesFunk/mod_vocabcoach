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

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once("$CFG->libdir/formslib.php");

/**
 * Form to display settings during check.
 *
 * @package   mod_vocabcoach
 * @copyright 2023 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Johannes Funk
 */
class check_settings_form extends moodleform {
    /**
     * Defines the form.
     * @return void
     */
    public function definition() : void {
        $mform = $this->_form;
        $mform->disable_form_change_checker();

        $modeoptions = [
            'type' => 'Englische Vokabeln tippen',
            'back' => 'Nach Vorderseite fragen',
            'front' => 'Nach Rückseite fragen',
            'random' => 'Zufällig',
        ];
        $mform->addElement('select', 'mode',
            get_string('mode', 'mod_vocabcoach'),
            $modeoptions,
            ['id' => 'check-mode']
        );
    }

    /**
     * Output HTML of the form.
     * @return string
     */
    public function to_html() : string {
        return $this->_form->toHtml();
    }
}
