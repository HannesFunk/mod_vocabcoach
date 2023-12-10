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
 * Prints an instance of mod_vocabcoach.
 *
 * @package     mod_vocabcoach
 * @copyright   2023 J. Funk, johannesfunk@outlook.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class vocabhelper {
    public int $boxnumber = 5;
    public array $boxtimes = [0, 1, 2, 5, 10, 30];

    public function __construct($cmid) {
        global $DB;
        $cm = get_coursemodule_from_id('vocabcoach', $cmid, 0, false, MUST_EXIST);
        $instanceinfo = $DB->get_record('vocabcoach', ['id' => $cm->instance], '*');
        for ($i = 1; $i <= 5; $i++) {
            $this->boxtimes[$i] = $instanceinfo->{'boxtime_'.$i};
        }
    }

    public function old_timestamp($daysago) : int {
        $now = time();
        return $now - ($daysago - 0.5) * 60 * 60 * 24;
    }

    /**
     * @param $lastchecked
     * @param $boxtime
     * @return string
     */
    public function compute_due_time_string ($lastchecked, $boxtime) : string {
        if ($lastchecked === null) {
            return '-';
        }
        $nextdue = time() + $boxtime * 60 * 60 * 24;
        $secondsleft = $nextdue - $lastchecked;
        if ($secondsleft > 60 * 60 * 24) {
            $time = floor($secondsleft / (60 * 60 * 24));
            return $time.($time > 1 ? ' Tagen' : ' Tag');
        } else {
            $time = floor ($secondsleft / (60 * 60));
            return $time.($time > 1 ? ' Stunden' : ' Stunde');
        }
    }

    public function get_sql_box_conditions() {
        $boxconditions = "";
        for ($i = 1; $i <= $this->boxnumber; $i++) {
            if ($i != 1) {
                $boxconditions .= " OR ";
            }
            $boxconditions .= " (vd.stage = $i AND lastchecked < " . $this->old_timestamp($this->boxtimes[$i]).")";
        }
        return $boxconditions;
    }
}
