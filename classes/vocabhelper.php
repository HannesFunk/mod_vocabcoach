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
    public int $BOX_NUMBER = 5;
    public array $BOXES_TIMES = [0, 1, 2, 5, 10, 30];

    function __construct($cmid) {
        global $DB;
        $cm = get_coursemodule_from_id('vocabcoach', $cmid, 0, false, MUST_EXIST);
        $instance_info = $DB->get_record('vocabcoach', ['id'=>$cm->instance], '*');
        for ($i=1; $i<=5; $i++) {
            $this->BOXES_TIMES[$i] = $instance_info->{'boxtime_'.$i};
        }
    }

    function old_timestamp($days_ago) : int {
        $now = time();
        return $now - ($days_ago - 0.5) * 60 * 60 * 24;
    }

    /**
     * @param $last_checked
     * @param $box_time
     * @return string
     */
    function compute_due_time_string ($last_checked, $box_time) : string {
        if ($last_checked === null) {
            return '-';
        }
        $next_due = time() + $box_time * 60 * 60 * 24;
        $seconds_left = $next_due - $last_checked;
        if ($seconds_left > 60 * 60 * 24) {
            $time = floor($seconds_left / (60 * 60 * 24));
            return $time.($time > 1 ? ' Tagen' : ' Tag');
        } else {
            $time = floor ($seconds_left / (60 * 60));
            return $time.($time > 1 ? ' Stunden' : ' Stunde');
        }

    }
}