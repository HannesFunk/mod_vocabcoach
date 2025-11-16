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

namespace mod_vocabcoach;

/**
 * Course Features. Provides information across users from the same course.
 *
 * @package   mod_vocabcoach
 * @copyright 2023 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Johannes Funk
 */
class course_features {
    /**
     * @var int $courseid Course ID
     * @var int $cmid Course Module ID
     * @var int $userid  User ID
     */
    private int $courseid, $cmid, $userid;

    /**
     * Construct the class.
     * @param int $courseid Course ID
     * @param int $cmid Course module id
     * @param int $userid User id
     */
    public function __construct(int $courseid, int $cmid, int $userid) {
        $this->courseid = $courseid;
        $this->cmid = $cmid;
        $this->userid = $userid;
    }

    /**
     * Returns the leaderboard.
     * @return array the list of the first three leaders and the user.
     * @throws \dml_exception
     */
    public function get_leaderboard() : array {
        global $DB;
        $vh = new \vocabhelper($this->cmid);
        $boxconditions = $vh->get_sql_box_conditions();

        $users = $this->get_student_users();
        // $userids = array_map(function($user) { return $user->id; }, $users);
        // $useridslist = implode(',', $userids);
        // $query = "SELECT COUNT(*) AS number FROM {vocabcoach_vocabdata} vd WHERE userid IN ($useridslist) AND cmid = $this->cmid AND ($boxconditions);";

        $perfect = [];
        foreach ($users as $user) {
            $query =
                    "SELECT COUNT(*) AS number FROM {vocabcoach_vocabdata} vd
                          WHERE userid = $user->id AND cmid = $this->cmid AND ($boxconditions);";
            $count = $DB->get_record_sql($query);
            if ($count->number == 0) {
                $perfect[] =
                        (object) ['id' => $user->id, 'firstname' => $user->firstname, 'lastname' => $user->lastname, 'number' => 0];
            }
        }

        $query = "SELECT uu.id, uu.firstname, uu.lastname, COUNT(vd.userid) AS number
                FROM {user} uu
                JOIN {user_enrolments} ue ON uu.id = ue.userid
                JOIN {enrol} en ON ue.enrolid = en.id
                LEFT JOIN {vocabcoach_vocabdata} vd ON vd.userid = uu.id
                WHERE en.courseid = $this->courseid AND vd.cmid = $this->cmid AND ($boxconditions)
                GROUP BY uu.id
                ORDER BY number;";
        $nonperfect = $DB->get_records_sql($query);

        $records = array_merge($perfect, $nonperfect);

        $rank = 1;
        $lasttopthree = 0;
        $records = array_values($records);
        $ownindex = 0;
        for ($i = 0; $i < count($records); $i++) {
            if ($i > 0 && $records[$i - 1]->number < $records[$i]->number) {
                $rank++;
                if ($rank > 3 && $lasttopthree == 0) {
                    $lasttopthree = $i - 1;
                }
                $records[$i]->rank = $rank;
            } else {
                $records[$i]->rank = ($i == 0) ? $rank : "";
            }

            if ($records[$i]->id == $this->userid) {
                $ownindex = $i;
                $records[$i]->self = true;
            }
        }

        if ($lasttopthree == 0) {
            $lasttopthree = $i - 1;
        }

        $topthree = array_slice($records, 0, $lasttopthree + 1);

        if ($ownindex <= $lasttopthree) {
            return $topthree;
        } else {
            $topthree[] = (object) ['id' => 0, 'firstname' => "...", 'lastname' => "", 'number' => ""];
            $topthree[] = $records[$ownindex];
            return $topthree;
        }
    }

   /**
     * Get all students using the standard 'student' archetype role.
     * @return array Array of user objects
     * @throws \dml_exception
     */
    public function get_student_users(): array {
        global $DB;

        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        if (!$studentrole) {
            return [];
        }

        $coursecontext = \context_course::instance($this->courseid);
        return get_role_users($studentrole->id, $coursecontext, false, 'u.*');
    }
}
