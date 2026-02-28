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
 * Utility class for iterating through vocabcoach cmids and users.
 *
 * @package   mod_vocabcoach
 * @copyright 2026 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Johannes Funk
 */

namespace mod_vocabcoach;

/**
 * Utility class for vocabcoach data iteration.
 */
class cmid_user_iterator {
    /**
     * Get all vocabcoach course module instances.
     *
     * @return array Array of course module records.
     */
    public static function get_all_cmids(): array {
        global $DB;

        $sql = "SELECT DISTINCT cm.id, cm.course, cm.instance
                FROM {course_modules} cm
                JOIN {modules} m ON m.id = cm.module
                WHERE m.name = 'vocabcoach'
                ORDER BY cm.id";

        return $DB->get_records_sql($sql);
    }

    /**
     * Get all active users (excluding deleted and guest accounts).
     *
     * @return array Array of user records.
     */
    public static function get_all_users(): array {
        global $DB;

        // Exclude guest user (id=1) and deleted users.
        return $DB->get_records_select('user', 'deleted = 0 AND id != ?', [1], 'id');
    }

    /**
     * Get all users enrolled in a specific vocabcoach activity.
     *
     * @param int $cmid The course module ID.
     * @return array Array of enrolled user records.
     */
    public static function get_users_in_cmid(int $cmid): array {
        global $DB;

        $sql = "SELECT DISTINCT u.id, u.username, u.firstname, u.lastname, u.email
                FROM {user} u
                JOIN {user_enrolments} ue ON u.id = ue.userid
                JOIN {enrol} e ON ue.enrolid = e.id
                JOIN {course_modules} cm ON e.courseid = cm.course
                WHERE cm.id = ? AND u.deleted = 0 AND u.id != 1
                ORDER BY u.id";

        return $DB->get_records_sql($sql, [$cmid]);
    }

    /**
     * Get all cmids for a specific user (all vocabcoach activities they're enrolled in).
     *
     * @param int $userid The user ID.
     * @return array Array of course module records.
     */
    public static function get_cmids_for_user(int $userid): array {
        global $DB;

        $sql = "SELECT DISTINCT cm.id, cm.course, cm.instance
                FROM {course_modules} cm
                JOIN {modules} m ON m.id = cm.module
                JOIN {user_enrolments} ue ON cm.course = e.courseid
                JOIN {enrol} e ON ue.enrolid = e.id
                WHERE m.name = 'vocabcoach' AND ue.userid = ?
                ORDER BY cm.id";

        return $DB->get_records_sql($sql, [$userid]);
    }

    /**
     * Iterate through all cmids and all users with a callback function.
     *
     * @param callable $callback Function to call for each user-cmid combination.
     *                           Receives parameters: $userid, $cmid
     * @return int Total number of combinations processed.
     */
    public static function iterate_all_combinations(callable $callback): int {
        $cmids = self::get_all_cmids();
        $users = self::get_all_users();

        $count = 0;
        foreach ($cmids as $cm) {
            foreach ($users as $user) {
                call_user_func($callback, $user->id, $cm->id);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Iterate through enrolled users for each cmid with a callback function.
     *
     * @param callable $callback Function to call for each user-cmid combination.
     *                           Receives parameters: $userid, $cmid, $user_record, $cm_record
     * @return int Total number of combinations processed.
     */
    public static function iterate_enrolled_combinations(callable $callback): int {
        $cmids = self::get_all_cmids();

        $count = 0;
        foreach ($cmids as $cm) {
            $users = self::get_users_in_cmid($cm->id);
            foreach ($users as $user) {
                call_user_func($callback, $user->id, $cm->id, $user, $cm);
                $count++;
            }
        }

        return $count;
    }
}
