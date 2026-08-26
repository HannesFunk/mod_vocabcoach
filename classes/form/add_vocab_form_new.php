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

namespace mod_vocabcoach\form;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once("$CFG->libdir/formslib.php");

/**
 * Form to add new vocab items.
 *
 * @package   mod_vocabcoach
 * @copyright 2023 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Johannes Funk
 */
class add_vocab_form_new extends \moodleform {
    /**
     * Defines the form.
     * @return void
     */
    public function definition(): void {
        $mform = $this->_form;

        global $DB;

        $mode = $this->_customdata['mode'];
        $id = $this->_customdata['id'];

        $cm = get_coursemodule_from_id('vocabcoach', $id, 0, false, MUST_EXIST);
        $moduleinstance = $DB->get_record('vocabcoach', ['id' => $cm->instance], '*', MUST_EXIST);

        $descfront = $moduleinstance->desc_front;
        $descback = $moduleinstance->desc_back;
        $instructions = $moduleinstance->instructions;

        $mform->addElement('hidden', 'id', $id);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'mode', $mode);
        $mform->setType('mode', PARAM_TEXT);
        $mform->addElement('hidden', 'listid', $this->_customdata['listid'] ?? 0);
        $mform->setType('listid', PARAM_TEXT);

        if (!empty($instructions)) {
            $mform->addElement('header', 'instructionsheader', get_string('instructions', 'mod_vocabcoach'));
            $instructionsformatted = '<div class="pl-5 pr-3 pt-3 pb-3">' . $instructions . '</div>';
            $mform->addElement('html', $instructionsformatted);
        }

        if ($mode === 'list' || $mode === 'edit') {
            $mform->addElement('header', 'listsectionheader', get_string('listprops', 'mod_vocabcoach'));

            $mform->addElement('text', 'list_title', get_string('list_title', 'mod_vocabcoach'));
            $mform->setType('list_title', PARAM_TEXT);
            $mform->addRule('list_title', get_string('err_required', 'form'), 'required');

            $mform->addElement('text', 'list_book', get_string('book', 'mod_vocabcoach'));
            $mform->setType('list_book', PARAM_TEXT);
            $mform->setDefault('list_book', get_string('list_book_default', 'mod_vocabcoach'));

            $years = [];
            for ($i = 5; $i <= 13; $i++) {
                $years[$i] = $i;
            }
            $mform->addElement('select', 'list_year', get_string('year', 'mod_vocabcoach'), $years, ['disabled']);
            $mform->setDefault('list_year', $this->_customdata['year']);
            $mform->disable_form_change_checker();

            $mform->addElement('text', 'list_unit', get_string('unit', 'mod_vocabcoach'));
            $mform->setType('list_unit', PARAM_TEXT);

            $mform->addElement(
                'advcheckbox',
                'add_to_user_database',
                get_string('add_vocab_add_to_user_database', 'mod_vocabcoach')
            );
            $mform->addHelpButton('add_to_user_database', 'add_vocab_add_to_user_database', 'mod_vocabcoach');

            $mform->addElement(
                'advcheckbox',
                'list_private',
                get_string('list_private', 'mod_vocabcoach'),
                '',
                null,
                [false, true]
            );
            $mform->addHelpButton('list_private', 'list_private', 'mod_vocabcoach');

            $mform->addElement(
                'advcheckbox',
                'list_distribute_now',
                get_string('list_distribute_now', 'mod_vocabcoach'),
                '',
                null,
                [false, true]
            );
            $mform->addHelpButton('list_distribute_now', 'list_distribute_now', 'mod_vocabcoach');
            $mform->setDefault('list_distribute_now', 1);
        }

        $mform->addElement('header', 'vocabsectionheader', get_string('vocabplural', 'mod_vocabcoach'));
        $mform->setExpanded('vocabsectionheader');

        if ($mode === 'edit') {
            $text = get_string('add_vocab_info_lines', 'mod_vocabcoach') .
                    ' ' . get_string('edit_vocab_instructions', 'mod_vocabcoach');
            $mform->addElement('static', 'info_lines', '', $text);
        } else {
            $mform->addElement('static', 'info_lines', '', get_string('add_vocab_info_lines', 'mod_vocabcoach'));
        }

        $repeatarray = [
            $mform->createElement('text', 'option', "no"),
            $mform->createElement('text', 'limit', "limitno"),
            $mform->createElement('hidden', 'optionid', 0),
            $mform->createElement('submit', 'delete', 'delete', [], false),
        ];



        $repeatno = 5;

        $repeateloptions = [
            'limit' => [
                'default' => 0,
                'disabledif' => array('limitanswers', 'eq', 0),
                'rule' => 'numeric',
                'type' => PARAM_INT,
            ],
            'option' => [
                'helpbutton' => [
                    'add_vocab_add_to_user_database',
                    'vocabcoach',
                ]
            ]
        ];

        $mform->setType('option', PARAM_CLEANHTML);
        $mform->setType('optionid', PARAM_INT);

        $this->repeat_elements(
            $repeatarray,
            $repeatno,
            $repeateloptions,
            'option_repeats',
            'option_add_fields',
            3,
            null,
            true,
            'delete',
        );

        $this->add_action_buttons();
    }

    /**
     * Possible validation.
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files): array {
        return [];
    }
}
