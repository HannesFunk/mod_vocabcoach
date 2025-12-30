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
 * Form to add new vocab items.
 *
 * @package   mod_vocabcoach
 * @copyright 2023 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Johannes Funk
 */
class add_vocab_form extends moodleform {
    /**
     * Defines the form.
     * @return void
     */
    public function definition() :void {
        $mform = $this->_form;

        global $DB;

       // $vocabcoach = $DB->get_re

        $mode = $this->_customdata['mode'];
        $id = $this->_customdata['id'];

        $cm = get_coursemodule_from_id('vocabcoach', $id, 0, false, MUST_EXIST);
        $moduleinstance = $DB->get_record('vocabcoach', ['id' => $cm->instance], '*', MUST_EXIST);


        $usesthird = $moduleinstance->thirdactive;
        $desc_front = $moduleinstance->desc_front;
        $desc_back = $moduleinstance->desc_back;

        $mform->addElement('hidden', 'id', $id);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'mode', $mode);
        $mform->setType('mode', PARAM_TEXT);
        $mform->addElement('hidden', 'listid', $this->_customdata['listid'] ?? 0);
        $mform->setType('listid', PARAM_TEXT);

        $mform->addElement('header', 'instructionsheader', get_string('instructions', 'mod_vocabcoach'));
        $mform->addElement('html', $this->instructions);

        if ($mode === 'list' || $mode === 'edit') {
            $mform->addElement('header', 'listsectionheader', get_string('listprops', 'mod_vocabcoach'));

            $mform->addElement('text', 'list_title', 'Name der Liste');
            $mform->setType('list_title', PARAM_TEXT);
            $mform->addRule('list_title', 'Darf nicht leer sein.', 'required');

            $mform->addElement('text', 'list_book', 'Schulbuch');
            $mform->setType('list_book', PARAM_TEXT);
            $mform->setDefault('list_book', 'Access');

            $years = [];
            for ($i = 5; $i <= 13; $i++) {
                $years[$i] = $i;
            }
            $mform->addElement('select', 'list_year', get_string('year', 'mod_vocabcoach'), $years, ['disabled']);
            $mform->setDefault('list_year', $this->_customdata['year']);
            $mform->disable_form_change_checker();

            $mform->addElement('text', 'list_unit', get_string('unit', 'mod_vocabcoach'));
            $mform->setType('list_unit', PARAM_TEXT);

            $mform->addElement('checkbox', 'add_to_user_database', get_string('add_vocab_add_to_user_database', 'mod_vocabcoach'));
            $mform->addHelpButton('add_to_user_database', 'add_vocab_add_to_user_database', 'mod_vocabcoach');

            $mform->addElement('advcheckbox', 'list_private',
                    get_string('list_private', 'mod_vocabcoach'), '', null, [false, true]);
            $mform->addHelpButton('list_private', 'list_private', 'mod_vocabcoach');

            $mform->addElement('advcheckbox', 'list_distribute_now',
                    get_string('list_distribute_now', 'mod_vocabcoach'), '', null, [false, true]);
            $mform->addHelpButton('list_distribute_now', 'list_distribute_now', 'mod_vocabcoach');
            $mform->setDefault('list_distribute_now', 1);
        }

        $mform->addElement('header', 'vocabsectionheader', get_string('vocabplural',  'mod_vocabcoach'));
        $mform->setExpanded('vocabsectionheader');

        if ($mode === 'edit') {
            $text = get_string('add_vocab_info_lines', 'mod_vocabcoach').
                    ' '.get_string('edit_vocab_instructions', 'mod_vocabcoach');
            $mform->addElement('static', 'info_lines', '', $text);
        } else {
            $mform->addElement('static', 'info_lines', '', get_string('add_vocab_info_lines', 'mod_vocabcoach'));
        }

        $vocabrow = [];
        $vocabrow[] =& $mform->createElement('hidden', 'vocabid[]');
        $vocabrow[] =& $mform->createElement('text', 'front[]', '', 'autocapitalize=off placeholder="'.$desc_front .'"');
        $vocabrow[] =& $mform->createElement('text', 'back[]', '', 'placeholder="'.$desc_back.'"');
        if ($usesthird) {
            $vocabrow[] =& $mform->createElement('text', 'third[]', '', 'placeholder="Zusatzinformation"');
        } else {
            $vocabrow[] =& $mform->createElement('hidden', 'third[]', '');
        }
        $mform->addGroup(
            $vocabrow,
            'vocabrow',
            get_string('vocab', 'mod_vocabcoach'),
            null,
            false
        );
        $mform->setType('vocabid[]', PARAM_INT);
        $mform->setType('front[]', PARAM_TEXT);
        $mform->setType('back[]', PARAM_TEXT);
        $mform->setType('third[]', PARAM_TEXT);

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

    /**
     * @var string $instructions Class hints on vocab formats.
     */
    private string $instructions = '
     <style>.vocabcoach-instructions li {
        margin-bottom: 7px;
     }
     .vocabcoach-instructions {
        list-style-type: square;
        margin-bottom: 20px;
     }
     </style>
     <div class="pl-5 pr-3"><p>Beachte folgende Hinweise, wenn du neue Vokabeln eintippst, damit alle ähnliche Form haben.
     Wenn du weitere Vorschläge hast, lass es mich jederzeit wissen.</p>
    <ul class="vocabcoach-instructions">
        <li><b>Verben:</b> im Englischen mit <i>to</i> einleiten (ohne Klammern etc.): <i>to go - gehen.</i></li>
        <li><b>Abkürzungen:</b> Normalerweise wie im Schulbuch verwenden,
        z. B. nicht <s>somebody</s> oder <s>sbd</s>, sondern <i>sb.</i> (mit Punkt). Hier eine Liste gängiger Abkürzungen: <br />
        Englisch: <i>sb. - sth. </i><br />
        Deutsch: <i>etw. - jmd.</i> (für jemandem, jemanden, jemand)
        </li>
        <li><b>Klammern vermeiden:</b> Präpositionen etc. einfach ohne Klammern übernehmen,
        im Deutschen wie im Englischen: <i>fear of - Angst vor</i>.</li>
    </ul>
    </div>
    ';
}
