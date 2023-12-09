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

class view_box_form extends moodleform {
    //Add elements to form
    public function definition(): void {

        $mform = $this->_form; // Don't forget the underscore!
        $vocab_array = json_decode($this->_customdata['vocabdata']);

        $id = $this->_customdata['id'];
        $third_active = $this->_customdata['third_active'] == 1;

        $mform->addElement('hidden', 'id', $id);
        $mform->setType('id', PARAM_INT);


        $table_header_html = '<table id="table-list" class="table generaltable">
        <tbody>
        <tr>
            <th>Englisch</th>
            <th>Deutsch</th>'.($third_active ? '<th>Zusatzinfo</th>' : '').'
            <th></th>
            </tr>';
        $mform->addElement('html', $table_header_html);

        foreach ($vocab_array as $vocab) {
            $vocabrow = array();
            $vocabrow[] =& $mform->createElement('html', '<tr>');
            $vocab_item_html = '<td>'.$vocab->front.'</td><td>'.$vocab->back.'</td>';
            if ($third_active == true) {
                $vocab_item_html .= '<td>' . $vocab->third . '</td>';
            }
            $vocab_item_html .= '<td><input type="button" class="btn btn-secondary mb-1" value="Aus meinem Kasten entfernen" data-action="mod_vocabcoach/remove_vocab_from_user" data-dataid="'.$vocab->dataid.'"></button></td>';
            $vocabrow[] =& $mform->createElement('html', $vocab_item_html.'</tr>');
            $mform->addGroup(
                    $vocabrow,
                    'vocabrow',
                    '',
                    '',
                    false
            );
        }

        $mform->addElement('html', '</tbody></table>');
    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}