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
 * Unit tests for streak manager class.
 *
 * @package   mod_vocabcoach
 * @copyright 2026, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_vocabcoach;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/vocabcoach/classes/streak_manager.php');

/**
 * Test class for streak_manager.
 *
 * @package   mod_vocabcoach
 * @copyright 2026 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \mod_vocabcoach\streak_manager
 */
class streak_manager_test extends \advanced_testcase {

    /**
     * Create a course module for vocabcoach tests.
     *
     * @return object[]
     */
    private function create_test_context(): array {
        $datagen = $this->getDataGenerator();
        $course = $datagen->create_course();
        $user = $datagen->create_user();
        $vocabcoach = $datagen->create_module('vocabcoach', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('vocabcoach', $vocabcoach->id);

        return [$course, $user, $vocabcoach, $cm];
    }

    /**
     * Seed a streak record.
     *
     * @param int $userid
     * @param int $cmid
     * @param int $streak
     * @param int $timemodified
     * @param string $type
     * @return int
     */
    private function create_streak_record(int $userid, int $cmid, int $streak, int $timemodified, string $type = 'login'): int {
        global $DB;

        return $DB->insert_record('vocabcoach_streaks', (object)[
            'userid' => $userid,
            'cmid' => $cmid,
            'streak' => $streak,
            'timemodified' => $timemodified,
            'type' => $type,
        ]);
    }

    /**
     * Test streak remains unchanged when already updated today.
     */
    public function test_update_type_keeps_today_streak(): void {
        $this->resetAfterTest(true);

        [, $user, , $cm] = $this->create_test_context();
        $this->create_streak_record($user->id, $cm->id, 17, strtotime('-2 hours'));

        $sm = new streak_manager($user->id, $cm->id);
        $sm->update('login');
        $streakinfo = $sm->get_streak_info('login');

        $this->assertEquals(17, $streakinfo->streak);
    }

    /**
     * Test streak increments when the last update was yesterday.
     */
    public function test_update_type_increments_yesterday_streak(): void {
        $this->resetAfterTest(true);

        [, $user, , $cm] = $this->create_test_context();
        $yesterday = strtotime('yesterday 12:00');
        $this->create_streak_record($user->id, $cm->id, 4, $yesterday);

        $sm = new streak_manager($user->id, $cm->id);
        $sm->update('login');
        $streakinfo = $sm->get_streak_info('login');

        $this->assertEquals(5, $streakinfo->streak);
    }

    /**
     * Test streak resets when the last update is older than yesterday.
     */
    public function test_update_type_resets_after_gap(): void {
        $this->resetAfterTest(true);

        [, $user, , $cm] = $this->create_test_context();
        $olderthanrestorewindow = strtotime('-3 days 12:00');
        $this->create_streak_record($user->id, $cm->id, 9, $olderthanrestorewindow);

        $sm = new streak_manager($user->id, $cm->id);
        $sm->update('login');
        $streakinfo = $sm->get_streak_info('login');

        $this->assertEquals(1, $streakinfo->streak);
    }
}