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

namespace mod_vocabcoach;

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
     * @var int $cmid The course module id.
     */
    public int $cmid;
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
        $this->cmid = $cmid;
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

    public function get_class_total (int $courseid) :int {
        global $DB;

        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        if (!$studentrole) {
            return -1;
        }

        $coursecontext = \context_course::instance($courseid);
        $userids = get_role_users($studentrole->id, $coursecontext, false, 'u.*');
        return self::get_due_count($userids);
    }

    /**
     * Returns the number of vocab items that are due
     * @param array $userids
     * @return int
     * @throws \dml_exception
     */
    public function get_due_count (array $userids) : int {
        global $DB;
        $vocabhelper = new vocabhelper($this->cmid);
        $boxconditions = $vocabhelper->get_sql_box_conditions();

        $useridlist = implode(',', array_map(fn($user) => $user->id, $userids));

        $query = "SELECT COUNT(*) AS total FROM {vocabcoach_vocabdata} vd
             WHERE userid IN ($useridlist) AND cmid = $this->cmid AND ($boxconditions)";
        $record = $DB->get_record_sql($query);
        if (!$record) {
            return -1;
        }
        return $record->total;
    }
}
