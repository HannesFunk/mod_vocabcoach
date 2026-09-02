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

use context_module;

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
class add_vocab_form extends \moodleform {
    /**
     * Defines the form.
     * @return void
     */
    public function definition(): void {
        $mform = $this->_form;

        global $OUTPUT;

        $cmid = $this->_customdata['cmid'];
        $listid = $this->_customdata['listid'];

        $mform->addElement('hidden', 'id', $cmid);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'listid', $this->_customdata['listid']);
        $mform->setType('listid', PARAM_INT);
        $mform->addElement('hidden', 'addtouser', $this->_customdata['addtouser']);
        $mform->setType('addtouser', PARAM_BOOL);

        if (!$this->_customdata['addtouser']) {
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

        if (!empty($this->_customdata['instructions'])) {
            $mform->addElement('header', 'instructionsheader', get_string('instructions', 'mod_vocabcoach'));
            $instructionsformatted = '<div class="pl-5 pr-3 pt-3 pb-3">' . $this->_customdata['instructions'] . '</div>';
            $mform->addElement('html', $instructionsformatted);
        }

        $mform->addElement('header', 'vocabsectionheader', get_string('vocabplural', 'mod_vocabcoach'));
        $mform->setExpanded('vocabsectionheader');

        $mform->addElement('static', 'info_lines', '', get_string('add_vocab_info_lines', 'mod_vocabcoach'));

        $vocabsfield = $mform->addElement('hidden', 'vocabs');
        $mform->setType('vocabs', PARAM_RAW);
        if (!$vocabsfieldid = $vocabsfield->getAttribute('id')) {
            $vocabsfield->_generateId();
            $vocabsfieldid = $vocabsfield->getAttribute('id');
        }

        $context = [
            'vocabs' => $this->_customdata['vocabarray'],
            'cmid' => $cmid,
            'listid' => $listid,
            'placeholders' => [
                'front' => $this->_customdata['desc_front'],
                'back' => $this->_customdata['desc_back'],
            ],
            'vocabsfieldid' => $vocabsfieldid,
        ];

        $mform->addElement('html',
            $OUTPUT->render_from_template('mod_vocabcoach/addvocab', ['propsjson' => json_encode($context)]));

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
