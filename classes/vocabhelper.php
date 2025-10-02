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
 * Vocabhelper - several methods to deal with database-display interaction
 *
 * @package   mod_vocabcoach
 * @copyright 2023 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Johannes Funk
 */
class vocabhelper {
    /**
     * @var int $boxnumber Number of boxes.
     */
    public int $boxnumber = 5;
    /**
     * @var array|int[] $boxtimes Times (in days) after vocab in a box is revisited.
     */
    public array $boxtimes = [0, 1, 2, 5, 10, 30];

    /**
     * Construct the class.
     * @param int $cmid
     * @throws dml_exception
     */
    public function __construct(int $cmid) {
        global $DB;
        $cm = get_coursemodule_from_id('vocabcoach', $cmid, 0, false, MUST_EXIST);
        $instanceinfo = $DB->get_record('vocabcoach', ['id' => $cm->instance]);
        for ($i = 1; $i <= 5; $i++) {
            $this->boxtimes[$i] = $instanceinfo->{'boxtime_'.$i};
        }
    }

    /**
     * Returns a timestamp
     * @param int $daysago Number of days before now
     * @return int
     */
    public function old_timestamp(int $daysago) : int {
        $now = time();
        return $now - ($daysago - 0.5) * 60 * 60 * 24;
    }

    /**
     * Returns a string when the vocab is due next
     * @param int|null $lastchecked
     * @param int $boxtime
     * @return string
     */
    public function compute_due_time_string (int|null $lastchecked, int $boxtime) : string {
        if ($lastchecked === null) {
            return '-';
        }
    public function compute_due_time_string (int $lastchecked, int $boxtime) : string {
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

    /**
     * Return SQL conditions for vocabs to be due in a box.
     * @return string
     */
    public function get_sql_box_conditions() : string {
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
