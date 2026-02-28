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
 * Unit tests for streak restorer class.
 *
 * @package   mod_vocabcoach
 * @copyright 2026, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_vocabcoach;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/vocabcoach/classes/streak_restorer.php');

/**
 * Test class for streak_restorer.
 *
 * @package   mod_vocabcoach
 * @copyright 2026 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \streak_restorer
 */
class streak_restorer_test extends \advanced_testcase {

    /**
     * Test can restore streak when limit not reached.
     */
    public function test_can_restore_streak_within_limit() {
        global $DB;

        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $vocabcoach = $this->getDataGenerator()->create_module('vocabcoach', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('vocabcoach', $vocabcoach->id);

        $this->assertTrue(\streak_restorer::can_restore_streak($user->id, $cm->id, 'login'));
    }

    /**
     * Test cannot restore streak when limit reached.
     */
    public function test_cannot_restore_streak_after_limit() {
        global $DB;

        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $vocabcoach = $this->getDataGenerator()->create_module('vocabcoach', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('vocabcoach', $vocabcoach->id);

        // Create a streak record.
        $DB->insert_record('vocabcoach_streaks', (object)[
            'cmid' => $cm->id,
            'userid' => $user->id,
            'streak' => 5,
            'type' => 'login',
            'timemodified' => time(),
        ]);

        // Create restore records to reach the limit.
        $month_year = \streak_restorer::get_current_month();
        $DB->insert_record('vocabcoach_streak_restores', (object)[
            'userid' => $user->id,
            'cmid' => $cm->id,
            'streak_type' => 'login',
            'restore_count' => 3,
            'month_year' => $month_year,
            'timemodified' => time(),
        ]);

        $this->assertFalse(\streak_restorer::can_restore_streak($user->id, $cm->id, 'login'));
    }

    /**
     * Test get remaining restores.
     */
    public function test_get_remaining_restores() {
        global $DB;

        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $vocabcoach = $this->getDataGenerator()->create_module('vocabcoach', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('vocabcoach', $vocabcoach->id);

        // Initially should have 3 restores.
        $remaining = \streak_restorer::get_remaining_restores($user->id, $cm->id, 'login');
        $this->assertEquals(3, $remaining);

        // Create one restore record.
        $month_year = \streak_restorer::get_current_month();
        $DB->insert_record('vocabcoach_streak_restores', (object)[
            'userid' => $user->id,
            'cmid' => $cm->id,
            'streak_type' => 'login',
            'restore_count' => 1,
            'month_year' => $month_year,
            'timemodified' => time(),
        ]);

        $remaining = \streak_restorer::get_remaining_restores($user->id, $cm->id, 'login');
        $this->assertEquals(2, $remaining);
    }

    /**
     * Test restore streak increments streak value.
     */
    public function test_restore_streak_increments_value() {
        global $DB;

        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $vocabcoach = $this->getDataGenerator()->create_module('vocabcoach', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('vocabcoach', $vocabcoach->id);

        // Create a streak record.
        $DB->insert_record('vocabcoach_streaks', (object)[
            'cmid' => $cm->id,
            'userid' => $user->id,
            'streak' => 5,
            'type' => 'login',
            'timemodified' => time(),
        ]);

        // Restore the streak.
        $result = \streak_restorer::restore_streak($user->id, $cm->id, 'login');
        $this->assertTrue($result);

        // Verify streak was incremented.
        $streak = $DB->get_record('vocabcoach_streaks', [
            'userid' => $user->id,
            'cmid' => $cm->id,
            'type' => 'login',
        ]);

        $this->assertEquals(6, $streak->streak);
    }

    /**
     * Test restore streak tracks restore count.
     */
    public function test_restore_streak_tracks_usage() {
        global $DB;

        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $vocabcoach = $this->getDataGenerator()->create_module('vocabcoach', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('vocabcoach', $vocabcoach->id);

        // Create a streak record.
        $DB->insert_record('vocabcoach_streaks', (object)[
            'cmid' => $cm->id,
            'userid' => $user->id,
            'streak' => 5,
            'type' => 'login',
            'timemodified' => time(),
        ]);

        // Restore 3 times.
        for ($i = 0; $i < 3; $i++) {
            \streak_restorer::restore_streak($user->id, $cm->id, 'login');
        }

        // Verify restore count.
        $month_year = \streak_restorer::get_current_month();
        $restore_record = $DB->get_record('vocabcoach_streak_restores', [
            'userid' => $user->id,
            'cmid' => $cm->id,
            'streak_type' => 'login',
            'month_year' => $month_year,
        ]);

        $this->assertEquals(3, $restore_record->restore_count);
    }

    /**
     * Test get restore stats.
     */
    public function test_get_restore_stats() {
        global $DB;

        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $vocabcoach = $this->getDataGenerator()->create_module('vocabcoach', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('vocabcoach', $vocabcoach->id);

        // Create a streak record.
        $DB->insert_record('vocabcoach_streaks', (object)[
            'cmid' => $cm->id,
            'userid' => $user->id,
            'streak' => 5,
            'type' => 'login',
            'timemodified' => time(),
        ]);

        // Restore once.
        \streak_restorer::restore_streak($user->id, $cm->id, 'login');

        $stats = \streak_restorer::get_restore_stats($user->id, $cm->id, 'login');

        $this->assertEquals(1, $stats->used);
        $this->assertEquals(2, $stats->remaining);
        $this->assertEquals(3, $stats->max);
    }

    /**
     * Test restore fails when streak record doesn't exist.
     */
    public function test_restore_fails_when_streak_not_found() {
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $vocabcoach = $this->getDataGenerator()->create_module('vocabcoach', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('vocabcoach', $vocabcoach->id);

        // Try to restore without a streak record.
        $result = \streak_restorer::restore_streak($user->id, $cm->id, 'login');
        $this->assertFalse($result);
    }

    /**
     * Test multiple restores in succession.
     */
    public function test_multiple_restores_in_succession() {
        global $DB;

        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $vocabcoach = $this->getDataGenerator()->create_module('vocabcoach', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('vocabcoach', $vocabcoach->id);

        // Create a streak record.
        $DB->insert_record('vocabcoach_streaks', (object)[
            'cmid' => $cm->id,
            'userid' => $user->id,
            'streak' => 5,
            'type' => 'login',
            'timemodified' => time(),
        ]);

        // Restore 3 times successfully.
        for ($i = 1; $i <= 3; $i++) {
            $result = \streak_restorer::restore_streak($user->id, $cm->id, 'login');
            $this->assertTrue($result);

            $streak = $DB->get_record('vocabcoach_streaks', [
                'userid' => $user->id,
                'cmid' => $cm->id,
                'type' => 'login',
            ]);
            $this->assertEquals(5 + $i, $streak->streak);
        }

        // 4th restore should fail (limit reached).
        $result = \streak_restorer::restore_streak($user->id, $cm->id, 'login');
        $this->assertFalse($result);

        // Streak should not have been updated.
        $streak = $DB->get_record('vocabcoach_streaks', [
            'userid' => $user->id,
            'cmid' => $cm->id,
            'type' => 'login',
        ]);
        $this->assertEquals(8, $streak->streak);
    }

    /**
     * Test different streak types are tracked separately.
     */
    public function test_different_streak_types_separate() {
        global $DB;

        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $vocabcoach = $this->getDataGenerator()->create_module('vocabcoach', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('vocabcoach', $vocabcoach->id);

        // Create two streak records - one for login, one for checkall.
        $DB->insert_record('vocabcoach_streaks', (object)[
            'cmid' => $cm->id,
            'userid' => $user->id,
            'streak' => 5,
            'type' => 'login',
            'timemodified' => time(),
        ]);

        $DB->insert_record('vocabcoach_streaks', (object)[
            'cmid' => $cm->id,
            'userid' => $user->id,
            'streak' => 3,
            'type' => 'checkall',
            'timemodified' => time(),
        ]);

        // Restore login 3 times.
        for ($i = 0; $i < 3; $i++) {
            \streak_restorer::restore_streak($user->id, $cm->id, 'login');
        }

        // Checkall should still have 3 restores available.
        $remaining = \streak_restorer::get_remaining_restores($user->id, $cm->id, 'checkall');
        $this->assertEquals(3, $remaining);

        // Login should have 0 restores available.
        $remaining = \streak_restorer::get_remaining_restores($user->id, $cm->id, 'login');
        $this->assertEquals(0, $remaining);
    }
}
