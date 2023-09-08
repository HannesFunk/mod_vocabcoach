<?php
// This file is part of Moodle Course Rollover Plugin
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


/**
 * @package     mod_vocabcoach
 * @author      J. Funk
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_vocabcoach;

require ('vocabhelper.php');

class box_manager {

    private \vocabhelper $vocabhelper;
    private int $cmid;

    public function __construct(int $cmid) {
        $this->vocabhelper = new \vocabhelper();
        $this->cmid = $cmid;
    }

    public function get_box_numbers($userid) {
        global $DB;

        $output = array();
        for ($i=1; $i<=$this->vocabhelper->BOX_NUMBER; $i++) {
            $total = $DB->count_records_select('mod_vocabcoach_vocabdata', 'userid = ? AND cmid = ? AND stage = ?', [$userid, $this->cmid, $i]);

            $min_days_since_check = $this->vocabhelper->BOXES_TIMES[$i];
            $due = $DB->count_records_select('mod_vocabcoach_vocabdata',
                'userid = ? AND cmid = ? AND stage = ? AND lastchecked < ?', [$userid, $this->cmid, $i, $this->vocabhelper->old_timestamp($min_days_since_check)]);

            $output[] = ['stage'=>$i, 'due'=>$due, 'total'=>$total, 'inactive' => $due == 0];
        }
        return $output;
    }
}