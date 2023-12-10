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

class activity_tracker {
    private int $userid, $cmid;
    public array $typesdaily = [
        "ACT_LOGGED_IN" => 1,
        "ACT_CHECKED_ALL" => 2,
    ];
    public array $typesalways = [
        "ACT_CHECKED_VOCAB" => 3,
        "ACT_ENTERED_VOCAB" => 4,
        "ACT_CREATED_LIST" => 5,
    ];

    public function __construct($userid, $cmid) {
        $this->userid = $userid;
        $this->cmid = $cmid;
    }

    /**
     * Log user activity
     *
     * @param int $type The type of the activity, see activity_tracker->$types. Can include: ACT_LOGGED_IN, ACT_CHECKED_ALL
     * @param string $date Date of the log entry.
     * @return bool Whether the log was successful
     */
    public function log(int $type, string $details = "", string $date = 'today') : bool {
        if (!in_array($type, $this->typesdaily) && !in_array($type, $this->typesalways)) {
            return false;
        }

        global $DB;
        $log = new StdClass();
        $log->userid = $this->userid;
        $log->cmid = $this->cmid;
        $log->type = $type;
        $log->date = $this->format_date($date);
        $log->details = $details;

        try {
            if (in_array($type, $this->typesdaily) &&
                $DB->count_records('vocabcoach_activitylog',
                    ['userid' => $log->userid, 'cmid' => $log->cmid, 'type' => $log->type, 'date' => $log->date]) > 0) {
                return true;
            }

            $DB->insert_record('vocabcoach_activitylog', $log);
            return true;
        } catch (dml_exception $e) {
            return false;
        }
    }

    public function format_date($datestring) : int {
        if ($datestring === 'today') {
            $datestring = date('d.m.Y');
        }
        $date = date_create($datestring);
        $year = (int) date_format($date, 'y');
        $dayofyear = (int) date_format($date, 'z');
        return $year * 1000 + $dayofyear;
    }

    private function day_before(int $dayint) : int {
        $day = $dayint % 1000;
        $year = ($dayint - $day) / 1000;

        if ($day !== 0) {
            return $dayint - 1;
        }

        $leapyearcorrection = (($year - 1) % 4 === 0) ? 1 : 0;
        return ($year - 1) * 1000 + 365 + $leapyearcorrection;
    }

    public function is_all_done (array $boxdata) : bool {
        foreach ($boxdata as $box) {
            if ($box['due'] != 0) {
                return false;
            }
        }
        return true;
    }

    public function get_continuous_days($type) : int {
        global $DB;
        $conditions = [
            'userid' => $this->userid,
            'cmid' => $this->cmid,
            'type' => $type,
        ];
        try {
            $records = $DB->get_records('vocabcoach_activitylog', $conditions, 'date DESC');
        } catch (dml_exception $e) {
            return -1;
        }
        $activities = array_values($records);
        $day = $this->day_before($this->format_date('today'));
        $i = 1;
        while (1) {
            if (!isset($activities[$i])) {
                return $i;
            }
            if ($activities[$i]->date != $day) {
                return $i;
            }
            $i++;
            $day = $this->day_before($day);
        }
    }
}
