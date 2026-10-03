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
final class streak_manager_test extends \advanced_testcase {
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
     * Data provider for test_update_streak_rules.
     *
     * Times are relative strings for strtotime(), evaluated when the test runs.
     *
     * @return array
     */
    public static function update_streak_rules_provider(): array {
        return [
            'today, extend' => ['today 00:00:01', true, 4],
            'today, no extend' => ['today 00:00:01', false, 4],
            'yesterday start, extend' => ['yesterday 00:00:01', true, 5],
            'yesterday start, no extend' => ['yesterday 00:00:01', false, 4],
            'yesterday noon, extend' => ['yesterday 12:01', true, 5],
            'yesterday noon, no extend' => ['yesterday 12:011', false, 4],
            'yesterday end, extend' => ['yesterday 23:59:59', true, 5],
            'yesterday end, no extend' => ['yesterday 23:59:59', false, 4],
            'day before yesterday end, extend' => ['today -1 day -1 second', true, 1],
            'day before yesterday end, no extend' => ['today -1 day -1 second', false, 0],
            'three days ago, extend' => ['-3 days 12:00', true, 1],
            'three days ago, no extend' => ['-3 days 12:00', false, 0],
        ];
    }

    /**
     * Test the streak rules depending on the last update and the extend flag.
     *
     * @param string $lastupdate Relative time of the last streak update.
     * @param bool $extend Whether the streak should be extended.
     * @param int $expected Expected streak after the update.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('update_streak_rules_provider')]
    public function test_update_streak_rules(string $lastupdate, bool $extend, int $expected): void {
        $this->resetAfterTest(true);

        [, $user, , $cm] = $this->create_test_context();
        $this->create_streak_record($user->id, $cm->id, 4, strtotime($lastupdate));

        $before = time();
        $sm = new streak_manager($user->id, $cm->id);
        $sm->update('login', $extend);
        $streak = $sm->get_streak('login');

        $this->assertEquals($expected, $streak->streak);
        $this->assertGreaterThanOrEqual($before, $streak->timemodified);
    }

    /**
     * Test a new streak record is created when none exists.
     */
    public function test_update_creates_record(): void {
        $this->resetAfterTest(true);

        [, $user, , $cm] = $this->create_test_context();

        $sm = new streak_manager($user->id, $cm->id);
        $sm->update('login');
        $sm->update('checkall', false);

        $this->assertEquals(1, $sm->get_streak('login')->streak);
        $this->assertEquals(0, $sm->get_streak('checkall')->streak);
    }

    /**
     * Test streak types are tracked independently.
     */
    public function test_update_only_affects_given_type(): void {
        $this->resetAfterTest(true);

        [, $user, , $cm] = $this->create_test_context();
        $yesterday = strtotime('yesterday 12:00');
        $this->create_streak_record($user->id, $cm->id, 4, $yesterday, 'login');
        $this->create_streak_record($user->id, $cm->id, 7, $yesterday, 'checkall');

        $sm = new streak_manager($user->id, $cm->id);
        $sm->update('login');

        $this->assertEquals(5, $sm->get_streak('login')->streak);
        $this->assertEquals(7, $sm->get_streak('checkall')->streak);
    }

    /**
     * Test an invalid streak type is rejected.
     */
    public function test_update_invalid_type(): void {
        $this->resetAfterTest(true);

        [, $user, , $cm] = $this->create_test_context();

        $sm = new streak_manager($user->id, $cm->id);
        $this->expectException(\invalid_parameter_exception::class);
        $sm->update('invalid');
    }
}
