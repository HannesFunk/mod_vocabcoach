<?php

class activity_tracker {
    private int $userid, $cmid;
    public array $types_daily = [
        "ACT_LOGGED_IN" => 1,
        "ACT_CHECKED_ALL" => 2,
    ];
    public array $types_always = [
        "ACT_CHECKED_VOCAB" => 3,
        "ACT_ENTERED_VOCAB" => 4,
        "ACT_CREATED_LIST" => 5,
    ];

    function __construct($userid, $cmid) {
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
    function log(int $type, string $details = "", string $date = 'today') : bool {
        if (!in_array($type, $this->types_daily) && !in_array($type, $this->types_always)) {
            return false;
        }

        global $DB;
        $log = new StdClass();
        $log->userid = $this->userid;
        $log->cmid = $this->cmid;
        $log->type = $type;
        $log->date = $this->formatDate($date);
        $log->details = $details;

        try {
            if (in_array($type, $this->types_daily) &&
                $DB->count_records('vocabcoach_activitylog', ['userid' => $log->userid, 'cmid' => $log->cmid, 'type' => $log->type, 'date' => $log->date]) > 0) {
                return true;
            }

            $DB->insert_record('vocabcoach_activitylog', $log);
            return true;
        } catch (dml_exception) {
            return false;
        }
    }

    function formatDate($date_string) : int {
        if ($date_string === 'today') {
            $date_string = date('d.m.Y');
        }
        $date = date_create($date_string);
        $year = (int) date_format($date, 'y');
        $day_of_year = (int) date_format($date, 'z');
        return $year * 1000 + $day_of_year;
    }

    function day_before(int $day_int) : int {
        $day = $day_int % 1000;
        $year = ($day_int - $day) / 1000;

        if ($day !== 0) {
            return $day_int - 1;
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

    function get_continuous_days($type) : int {
        global $DB;
        $conditions = [
            'userid' => $this->userid,
            'cmid' => $this->cmid,
            'type' => $type,
        ];
        try {
            $records = $DB->get_records('vocabcoach_activitylog', $conditions, 'date DESC');
        } catch (dml_exception) {
            return -1;
        }
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



    //function get_class_log ($cmid) {
    //    global $DB;
    //    $cm = get_coursemodule_from_id('vocabcoach', $cmid, 0, false, MUST_EXIST);
    //    $context = context_course::instance($cm->course);
    //
    //    $students = get_enrolled_users($context);
    //
    //    foreach ($this->types as $type) {
    //        $query = "SELECT MIN(date) FROM {vocabcoach_activitylog} WHERE cmid = {$cmid} AND type = {$type};";
    //        $DB->get_records_sql()
    //    }
    //}
}