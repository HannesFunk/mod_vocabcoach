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
 * Form to view a user box (and possibly delete elements).
 *
 * @package   mod_vocabcoach
 * @copyright 2023 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Johannes Funk
 */
class view_box_form extends moodleform {
    /**
     * Defines the form.
     * @return void
     */
    public function definition(): void {

        $mform = $this->_form;
        $vocabarray = json_decode($this->_customdata['vocabdata']);

        $id = $this->_customdata['id'];
        $usesthird = $this->_customdata['third_active'] == 1;

        $mform->addElement('hidden', 'id', $id);
        $mform->setType('id', PARAM_INT);

        $tableheaderhtml = '<table id="table-list" class="table generaltable">
        <tbody>
        <tr>
            <th>Englisch</th>
            <th>Deutsch</th>'.($usesthird ? '<th>Zusatzinfo</th>' : '').'
            <th></th>
            </tr>';
        $mform->addElement('html', $tableheaderhtml);

        foreach ($vocabarray as $vocab) {
            $vocabrow = [];
            $vocabrow[] =& $mform->createElement('html', '<tr>');
            $vocabitemhtml = '<td>'.$vocab->front.'</td><td>'.$vocab->back.'</td>';
            if ($usesthird) {
                $vocabitemhtml .= '<td>' . $vocab->third . '</td>';
            }
            $vocabitemhtml .= '<td><input type="button" class="btn btn-secondary mb-1" value="Aus meinem Kasten entfernen"
                data-action="mod_vocabcoach/remove_vocab_from_user" data-dataid="'.$vocab->dataid.'"></button></td>';
            $vocabrow[] =& $mform->createElement('html', $vocabitemhtml.'</tr>');
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

    /**
     * Validation of form data.
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) : array {
        return [];
    }
}
