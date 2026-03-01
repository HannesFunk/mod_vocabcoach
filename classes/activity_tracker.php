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
 * Activity Tracker class. Tracks various types of interaction of the user with the activity.
 *
 * @package     mod_vocabcoach
 * @author      Johannes Funk
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright   2023 onwards, Johannes Funk
 */
class activity_tracker {
    /**
     * @var int $userid User ID
     * @var int $cmid Course Module ID
     */
    private int $userid, $cmid;

    /**
     * @var array $typesdaily Types of the interactions that are tracked once a day
     * @var array $typesalways Types of the interactions that are tracked whenever they occur
     */
    public array $typesdaily = [
        "ACT_LOGGED_IN" => 1,
        "ACT_CHECKED_ALL" => 2,
    ],
    $typesalways = [
        "ACT_CHECKED_VOCAB" => 3,
        "ACT_ENTERED_VOCAB" => 4,
        "ACT_CREATED_LIST" => 5,
    ];

    /**
     * Construct the class.
     * @param int $userid User id
     * @param int $cmid Course module id
     */
    public function __construct(int $userid, int $cmid) {
        $this->userid = $userid;
        $this->cmid = $cmid;
    }

    /**
     * Log user activity
     *
     * @param int $type The type of the activity, see activity_tracker->$types. Can include: ACT_LOGGED_IN, ACT_CHECKED_ALL
     * @param string $details Additional information.
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

    /**
     * Formats the current date in a way that is usable in the database.
     *
     * @param string $datestring
     * @return int
     */
    public function format_date(string $datestring) : int {
        if ($datestring === 'today') {
            $datestring = date('d.m.Y');
        }
        $date = date_create($datestring);
        $year = (int) date_format($date, 'y');
        $dayofyear = (int) date_format($date, 'z');
        return $year * 1000 + $dayofyear;
    }

    /**
     * Computes the day before a given date-number.
     *
     * @param int $dayint
     * @return int
     */
    private function day_before(int $dayint) : int {
        $day = $dayint % 1000;
        $year = ($dayint - $day) / 1000;

        if ($day !== 0) {
            return $dayint - 1;
        }

        $leapyearcorrection = (($year - 1) % 4 === 0) ? 1 : 0;
        return ($year - 1) * 1000 + 365 + $leapyearcorrection;
    }

    /**
     * Checks whether all boxes are done currently.
     *
     * @param array $boxdata Information the boxes.
     * @return bool
     */
    public function is_all_done (array $boxdata) : bool {
        foreach ($boxdata as $box) {
            if ($box['due'] != 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * Computes the number of days a certain interaction has been done consecutively.
     *
     * @param int $type type number of the interaction
     * @return int
     */
    public function get_continuous_days(int $type) : int {
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
            if (!isset($activities[$i]->date)) {
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
