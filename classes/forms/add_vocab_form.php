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

//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");

class add_vocab_form extends moodleform {
    //Add elements to form
    public function definition() :void {
        $mform = $this->_form; // Don't forget the underscore!
        $mode = $this->_customdata['mode'];
        $id = $this->_customdata['id'];
        $third_active = $this->_customdata['third_active'] == true;

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
            for ($i=5; $i<=13; $i++) {
                $years[$i] = $i;
            }
            $mform->addElement('select', 'list_year', 'Jahrgangsstufe', $years, ['disabled']);
            $mform->setDefault('list_year', $this->_customdata['year']);
            $mform->disable_form_change_checker();

            $mform->addElement('text', 'list_unit', 'Unit');
            $mform->setType('list_unit', PARAM_TEXT);

            $mform->addElement('checkbox', 'add_to_user_database', get_string('add_vocab_add_to_user_database', 'mod_vocabcoach'));
            $mform->addHelpButton('add_to_user_database', 'add_vocab_add_to_user_database', 'mod_vocabcoach');

            $mform->addElement('advcheckbox', 'list_private', get_string('list_private', 'mod_vocabcoach'), '', null, array(false, true));
            $mform->addHelpButton('list_private', 'list_private', 'mod_vocabcoach');

            $mform->addElement('advcheckbox', 'list_distribute_now', get_string('list_distribute_now', 'mod_vocabcoach'), '', null, array(false, true));
            $mform->addHelpButton('list_distribute_now', 'list_distribute_now', 'mod_vocabcoach');
            $mform->setDefault('list_distribute_now', 1);
        }

        $mform->addElement('header', 'vocabsectionheader', get_string('vocabplural',  'mod_vocabcoach'));

        if ($mode === 'edit') {
            $text = get_string('add_vocab_info_lines', 'mod_vocabcoach').' '.get_string('edit_vocab_instructions', 'mod_vocabcoach');
            $mform->addElement('static', 'info_lines', '', $text);
        } else {
            $mform->addElement('static', 'info_lines', '', get_string('add_vocab_info_lines', 'mod_vocabcoach'));
        }

        $vocabrow = array();
        $vocabrow[] =& $mform->createElement('hidden', 'vocabid[]');
        $vocabrow[] =& $mform->createElement('text', 'front[]', '', 'autocapitalize=off placeholder="Englisch"');
        $vocabrow[] =& $mform->createElement('text', 'back[]', '', 'placeholder="Deutsch"');
        if ($third_active) {
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

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }

private string $instructions = ' 
 <style>.vocabcoach-instructions li {
    margin-bottom: 7px;
 }
 .vocabcoach-instructions {
    list-style-type: square;
    margin-bottom: 20px;
 }
 </style>
 <div class="pl-5 pr-3"><p>Beachte folgende Hinweise, wenn du neue Vokabeln eintippst, damit alle ähnliche Form haben. Wenn du weitere Vorschläge hast, lass es mich jederzeit wissen.</p>
<ul class="vocabcoach-instructions">
    <li><b>Verben:</b> im Englischen mit <i>to</i> einleiten (ohne Klammern etc.): <i>to go - gehen.</i></li>
    <li><b>Abkürzungen:</b> Normalerweise wie im Schulbuch verwenden, z. B. nicht <s>somebody</s> oder <s>sbd</s>, sondern <i>sb.</i> (mit Punkt). Hier eine Liste gängiger Abkürzungen: <br />
    Englisch: <i>sb. - sth. </i><br />
    Deutsch: <i>etw. - jmd.</i> (für jemandem, jemanden, jemand)
    </li>
    <li><b>Klammern vermeiden:</b> Präpositionen etc. einfach ohne Klammern übernehmen, im Deutschen wie im Englischen: <i>fear of - Angst vor</i>.</li>
</ul>
</div>
';
}