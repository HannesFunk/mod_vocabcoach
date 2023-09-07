<?php

class activity_tracker {
    public int $ACT_LOGGED_IN = 1;
    public int $ACT_CHECKED_ALL = 2;
    function __construct($userid, $cmid) {
        $this->userid = $userid;
        $this->cmid = $cmid;
    }

    function log(int $type, string $date = 'today') : bool {
        if (! in_array($type, [$this->ACT_LOGGED_IN, $this->ACT_CHECKED_ALL])) {
            return false;
        }

        global $DB;
        $log = new StdClass();
        $log->userid = $this->userid;
        $log->cmid = $this->cmid;
        $log->type = $type;
        $log->date = $this->formatDate($date);

        if ($DB->count_records('mod_vocabcoach_activitylog', ['userid'=>$log->userid, 'cmid'=>$log->cmid, 'type'=>$log->type, 'date'=>$log->date]) > 0) {
            return true;
        }

        $DB->insert_record('mod_vocabcoach_activitylog', $log, true);
    return true;
    }

    function formatDate($datestring) : int {
        if ($datestring === 'today') {
            $datestring = date('d.m.Y');
        }
        $date = date_create($datestring);
        $year = (int) date_format($date, 'y');
        $dayofyear = (int) date_format($date, 'z');
        return $year * 1000 + $dayofyear;
    }

    function day_before(int $dayint) : int {
        $day = $dayint % 1000;
        $year = ($dayint - $day) / 1000;

        if ($day !== 0) {
            return $dayint - 1;
        }

        $leap_year_correction = (($year -1) % 4 === 0) ? 1 : 0;
        return ($year - 1) * 1000 + 365 + $leap_year_correction;
    }

    function is_all_done (array $boxdata) : bool {
        foreach ($boxdata as $box) {
            if ($box['due'] != 0) {
                return false;
            }
        }
        return true;
    }

    function get_continuous_days($type) {
        global $DB;
        $conditions = [
            'userid' => $this->userid,
            'cmid' => $this->cmid,
            'type' => $type,
        ];
        $records = $DB->get_records('mod_vocabcoach_activitylog', $conditions, 'date DESC');
        $activities = array_values($records);
        $day = $this->day_before($this->formatDate('today'));
        $i = 1;
        while (1) {
            if (!isset($activities[$i])) {
                return ($i - 1);
            }
            if ($activities[$i]->date != $day) {
                return ($i - 1);
            }
            $i++;
            $day = $this->day_before($day);
        }
    }
}