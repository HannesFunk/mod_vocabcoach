<?php

namespace mod_vocabcoach;
class course_features {

    public function __construct($courseid, $cmid, $userid) {
        $this->courseid = $courseid;
        $this->cmid = $cmid;
        $this->userid = $userid;
    }

    public function get_leaderboard() {
        global $DB;
        $vh = new \vocabhelper($this->cmid);
        $box_conditions = $vh->get_sql_box_conditions();

        $users_query =
                "SELECT uu.id, uu.firstname, uu.lastname FROM {user} uu JOIN {user_enrolments} ue ON uu.id = ue.userid JOIN {enrol} en ON ue.enrolid = en.id WHERE en.courseid = $this->courseid;";

        $users = $DB->get_records_sql($users_query);

        $perfectStudents = [];
        foreach ($users as $user) {
            $query =
                    "SELECT COUNT(*) AS number FROM {vocabcoach_vocabdata} vd WHERE userid = $user->id AND cmid = $this->cmid AND ($box_conditions);";
            $count = $DB->get_record_sql($query);
            if ($count->number == 0) {
                $perfectStudents[] =
                        (object) ['id' => $user->id, 'firstname' => $user->firstname, 'lastname' => $user->lastname, 'number' => 0];
            }
        }

        $query = "SELECT uu.id, uu.firstname, uu.lastname, COUNT(vd.userid) AS number 
                FROM {user} uu 
                JOIN {user_enrolments} ue ON uu.id = ue.userid 
                JOIN {enrol} en ON ue.enrolid = en.id 
                LEFT JOIN {vocabcoach_vocabdata} vd ON vd.userid = uu.id 
                WHERE en.courseid = $this->courseid AND vd.cmid = $this->cmid AND ($box_conditions)
                GROUP BY uu.id
                ORDER BY number;";
        $nonPerfect = $DB->get_records_sql($query);

        $records = array_merge($perfectStudents, $nonPerfect);

        $rank = 1;
        $last_top_three = 0;
        $records = array_values($records);
        $own_index = 0;
        for ($i = 0; $i < count($records); $i++) {
            if ($i > 0 && $records[$i - 1]->number < $records[$i]->number) {
                $rank++;
                if ($rank > 3 && $last_top_three == 0) {
                    $last_top_three = $i - 1;
                }
                $records[$i]->rank = $rank;
            } else {
                $records[$i]->rank = ($i == 0) ? $rank : "";
            }

            if ($records[$i]->id == $this->userid) {
                $own_index = $i;
                $records[$i]->self = true;
            }
        }

        if ($last_top_three == 0) {
            $last_top_three = $i - 1;
        }

        $top_three = array_slice($records, 0, $last_top_three + 1);

        if ($own_index <= $last_top_three) {
            return $top_three;
        } else {
            $top_three[] = (object) ['id' => 0, 'firstname' => "...", 'lastname' => "", 'number' => ""];
            $top_three[] = $records[$own_index];
            return $top_three;
        }

    }

}