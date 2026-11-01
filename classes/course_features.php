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

use core\context\module;

/**
 * Course Features. Provides information across users from the same course.
 *
 * @package   mod_vocabcoach
 * @copyright 2023 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Johannes Funk
 */
class course_features {
    /** @var module $context The module context */
    private module $context;
    /** @var vocabhelper $vh Vocabhelper class */
    private vocabhelper $vh;

    /**
     * Construct the class.
     * @param module $context module context
     */
    public function __construct(module $context) {
        $this->context = $context;
        $this->vh = new vocabhelper($context->instanceid);
    }

    /**
     * Returns the leaderboard.
     * @param int $userid ID of the current user
     * @return array the list of the first three leaders and the user.
     */
    public function get_leaderboard(int $userid): array {
        global $DB;
        $cmid = $this->context->instanceid;

        $students = $this->get_student_users();
        if (empty($students)) {
            return [];
        }

        [$insql, $inparams] = $DB->get_in_or_equal(array_column($students, 'id'), SQL_PARAMS_NAMED, 'uid');
        [$duesql, $dueparams] = $this->vh->get_due_sql_all();

        $sql = "SELECT uu.id, uu.firstname, uu.lastname, COUNT(v.id) AS number
                FROM {user} uu
                LEFT JOIN {vocabcoach_vocabdata} vd
                ON vd.userid = uu.id AND vd.cmid = :cmid AND $duesql
                LEFT JOIN {vocabcoach_vocab} v ON v.id = vd.vocabid
                WHERE uu.id $insql
                GROUP BY uu.id, uu.firstname, uu.lastname
                ORDER BY number ASC, uu.lastname ASC, uu.firstname ASC, uu.id ASC";

        $records = $DB->get_records_sql($sql, $dueparams + $inparams + ['cmid' => $cmid]);
        return $this->rank_leaderboard(array_values($records), $userid);
    }

    private function rank_leaderboard(array $records, int $userid): array {
        // Ranking algorithm: array is already ordered, now compute the (dense) rank and edit rows accordingly.
        $rank = 0;
        $previous = null;
        $lasttopthree = 0;
        $ownindex = null;

        foreach ($records as $i => $record) {
            $record->number = (int) $record->number;
            if ($record->number !== $previous) {
                $rank++;
                $record->rank = $rank;
                $previous = $record->number;
            } else {
                $record->rank = "";
            }

            if ($rank <= 3) {
                $lasttopthree = $i;
            }

            $record->self = (int) $record->id === $userid;
            if ($record->self) {
                $ownindex = $i;

            }
        }

        $topthree = array_slice($records, 0, $lasttopthree + 1);

        if ($ownindex && $ownindex > $lasttopthree) {
            $topthree[] = (object)['id' => 0, 'firstname' => "...", 'lastname' => "", 'number' => ""];
            $topthree[] = $records[$ownindex];
        }
        return $topthree;
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

        $coursecontext = $this->context->get_course_context();
        return get_role_users($studentrole->id, $coursecontext, false, 'u.*');
    }

    /**
     * Counts the number of vocab items that are due for all students in the course.
     * @return int Total count of due vocab items
     */
    public function get_class_total(): int {
        $userids = $this->get_student_users();
        return $this->get_due_count($userids);
    }

    /**
     * Returns the number of vocab items that are due
     * @param array $users
     * @return int
     * @throws \dml_exception
     */
    public function get_due_count(array $users): int {
        if (empty($users)) {
            return -1;
        }
        global $DB;
        [$duesql, $dueparams] = $this->vh->get_due_sql_all();
        [$insql, $inparams] = $DB->get_in_or_equal(array_column($users, 'id'), SQL_PARAMS_NAMED, 'userid');

        $query = "SELECT COUNT(*)
                  FROM {vocabcoach_vocabdata} vd
                  JOIN {vocabcoach_vocab} v
                  ON v.id = vd.vocabid
                  WHERE vd.userid $insql AND vd.cmid = :cmid AND $duesql";
        $params = $dueparams + $inparams + ['cmid' => $this->context->instanceid];

        return $DB->count_records_sql($query, $params);
    }
}
