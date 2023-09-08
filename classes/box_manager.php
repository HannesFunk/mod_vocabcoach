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

    public function __construct(int $cmid, int $userid) {
        $this->vocabhelper = new \vocabhelper();
        $this->cmid = $cmid;
        $this->userid = $userid;
    }

    public function get_box_details() : array {
        global $DB;

        $output = array();
        for ($i=1; $i<=$this->vocabhelper->BOX_NUMBER; $i++) {
            $total = $DB->count_records_select('mod_vocabcoach_vocabdata', 'userid = ? AND cmid = ? AND stage = ?', [$this->userid, $this->cmid, $i]);

            $min_days_since_check = $this->vocabhelper->BOXES_TIMES[$i];
            $due = $DB->count_records_select('mod_vocabcoach_vocabdata',
                'userid = ? AND cmid = ? AND stage = ? AND lastchecked < ?', [$this->userid, $this->cmid, $i, $this->vocabhelper->old_timestamp($min_days_since_check)]);

            if ($due === 0) {
                $query = "SELECT MIN(vd.lastchecked) AS recent 
                            FROM {mod_vocabcoach_vocabdata} vd
                            WHERE userid = {$this->userid} AND cmid = {$this->cmid} AND stage = {$i}
                            ";
                $record = $DB->get_record_sql($query);
                $next_due = $this->vocabhelper->compute_due_time_string($record->recent, $this->vocabhelper->BOXES_TIMES[$i]);
            } else {
                $next_due = 'Jetzt';
            }

            $output[] = [
                'stage'=>$i,
                'due'=>$due,
                'total'=>$total,
                'inactive' => $due == 0,
                'next_due' => $next_due];
        }
        return $output;
    }
}