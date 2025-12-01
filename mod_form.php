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
 * @package     mod_vocabcoach
 * @copyright   2023 J. Funk, johannesfunk@outlook.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form.
 *
 * @package     mod_vocabcoach
 * @copyright   2023 J. Funk, johannesfunk@outlook.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_vocabcoach_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() :void {
        global $CFG;

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are shown.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('vocabcoachname', 'mod_vocabcoach'), ['size' => '64']);

        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }

        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'vocabcoachname', 'mod_vocabcoach');

        // Adding the standard "intro" and "introformat" fields.
        if ($CFG->branch >= 29) {
            $this->standard_intro_elements();
        } else {
            $this->add_intro_editor();
        }

        $years = [];
        for ($i = 5; $i <= 13; $i++) {
            $years[$i] = $i;
        }
        $mform->addElement('select', 'year', 'Jahrgangsstufe', $years);

        $mform->addElement('text', 'desc_front', 'Vorderseite');
        $mform->addElement('text', 'desc_back', 'Rückseite');
        $mform->setType('desc_front', PARAM_TEXT);
        $mform->setType('desc_back', PARAM_TEXT);
        $mform->setDefault('desc_back', 'Deutsch');


        $mform->addElement('checkbox', 'third_active', get_string('third_active', 'vocabcoach'));

        $mform->addElement('header', 'boxtimes', get_string('boxtimes', 'mod_vocabcoach'));
        $mform->addElement('static', 'info_boxtimes', '', get_string('info_boxtimes', 'mod_vocabcoach'));
        $defaultboxtimes = [0, 1, 2, 5, 10, 30];
        for ($i = 1; $i <= 5; $i++) {
            $mform->addElement('text', 'boxtime_'.$i, get_string('boxtime', 'mod_vocabcoach').' '.$i);
            $mform->setType('boxtime_'.$i, PARAM_INT);
            $mform->setDefault('boxtime_'.$i, $defaultboxtimes[$i]);
        }

        $mform->addElement('checkbox', 'move_undue', get_string('move_undue', 'vocabcoach'));
        $mform->addHelpButton('move_undue', 'move_undue', 'mod_vocabcoach');

        // Option to enable/disable due-vocab notifications for this instance.
        $mform->addElement('checkbox', 'notify_students', get_string('notify_students', 'mod_vocabcoach'));
        $mform->setDefault('notify_students', 0);
        $mform->addHelpButton('notify_students', 'notify_students', 'mod_vocabcoach');

        // Add standard elements.
        $this->standard_coursemodule_elements();

        // Add standard buttons.
        $this->add_action_buttons();
    }
}
