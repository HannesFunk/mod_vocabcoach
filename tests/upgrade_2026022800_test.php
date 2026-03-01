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
 * Unit tests for database upgrade step 2026022800 (vocabcoach_streaks table creation and data migration).
 *
 * @package   mod_vocabcoach
 * @copyright 2026, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Johannes Funk
 * @category  test
 */

namespace mod_vocabcoach;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/vocabcoach/db/upgrade.php');
require_once($CFG->dirroot . '/mod/vocabcoach/classes/activity_tracker.php');

/**
 * Test class for database upgrade step 2026022800.
 *
 * Tests the creation of vocabcoach_streaks table and migration of activity log data.
 *
 * @package   mod_vocabcoach
 * @copyright 2026 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    ::xmldb_vocabcoach_upgrade
 */
class upgrade_2026022800_test extends \advanced_testcase {

    /**
     * Test the creation of vocabcoach_streaks table and data migration from activity log.
     *
     * @return void
     */
    public function test_upgrade_creates_streaks_table_and_migrates_data() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $dbman = $DB->get_manager();

        // Create test data: course, users, vocabcoach activity.
        $course = $this->getDataGenerator()->create_course();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $vocabcoach = $this->getDataGenerator()->create_module('vocabcoach', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('vocabcoach', $vocabcoach->id);
        $cmid = $cm->id;

        // Create activity log entries for user1 with a 5-day login streak.
        $tracker1 = new \activity_tracker($user1->id, $cmid);
        $basedate = strtotime('today');

        // User1: 5 consecutive days of login (including today).
        for ($i = 0; $i < 5; $i++) {
            $date = date('d.m.Y', $basedate - ($i * 86400));
            $DB->insert_record('vocabcoach_activitylog', (object)[
                'userid' => $user1->id,
                'cmid' => $cmid,
                'date' => $tracker1->format_date($date),
                'type' => $tracker1->typesdaily['ACT_LOGGED_IN'],
                'details' => 'Test login',
            ]);
        }

        // User1: 3 consecutive days of "checked all" (including today).
        for ($i = 0; $i < 3; $i++) {
            $date = date('d.m.Y', $basedate - ($i * 86400));
            $DB->insert_record('vocabcoach_activitylog', (object)[
                'userid' => $user1->id,
                'cmid' => $cmid,
                'date' => $tracker1->format_date($date),
                'type' => $tracker1->typesdaily['ACT_CHECKED_ALL'],
                'details' => 'Test checked all',
            ]);
        }

        // User2: 2 consecutive days of login.
        $tracker2 = new \activity_tracker($user2->id, $cmid);
        for ($i = 0; $i < 2; $i++) {
            $date = date('d.m.Y', $basedate - ($i * 86400));
            $DB->insert_record('vocabcoach_activitylog', (object)[
                'userid' => $user2->id,
                'cmid' => $cmid,
                'date' => $tracker2->format_date($date),
                'type' => $tracker2->typesdaily['ACT_LOGGED_IN'],
                'details' => 'Test login',
            ]);
        }

        // User2: 7 consecutive days of "checked all".
        for ($i = 0; $i < 7; $i++) {
            $date = date('d.m.Y', $basedate - ($i * 86400));
            $DB->insert_record('vocabcoach_activitylog', (object)[
                'userid' => $user2->id,
                'cmid' => $cmid,
                'date' => $tracker2->format_date($date),
                'type' => $tracker2->typesdaily['ACT_CHECKED_ALL'],
                'details' => 'Test checked all',
            ]);
        }

        // Verify activity log entries were created.
        $this->assertEquals(5, $DB->count_records('vocabcoach_activitylog', [
            'userid' => $user1->id,
            'type' => $tracker1->typesdaily['ACT_LOGGED_IN'],
        ]));
        $this->assertEquals(3, $DB->count_records('vocabcoach_activitylog', [
            'userid' => $user1->id,
            'type' => $tracker1->typesdaily['ACT_CHECKED_ALL'],
        ]));
        $this->assertEquals(2, $DB->count_records('vocabcoach_activitylog', [
            'userid' => $user2->id,
            'type' => $tracker2->typesdaily['ACT_LOGGED_IN'],
        ]));
        $this->assertEquals(7, $DB->count_records('vocabcoach_activitylog', [
            'userid' => $user2->id,
            'type' => $tracker2->typesdaily['ACT_CHECKED_ALL'],
        ]));

        // Drop the streaks table if it exists (to simulate pre-upgrade state).
        $table = new \xmldb_table('vocabcoach_streaks');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Verify table doesn't exist.
        $this->assertFalse($dbman->table_exists($table));

        // Run the upgrade.
        xmldb_vocabcoach_upgrade(2026022799);

        // Verify table was created.
        $this->assertTrue($dbman->table_exists($table));

        // Verify table structure.
        $this->assertTrue($dbman->field_exists($table, new \xmldb_field('id')));
        $this->assertTrue($dbman->field_exists($table, new \xmldb_field('cmid')));
        $this->assertTrue($dbman->field_exists($table, new \xmldb_field('userid')));
        $this->assertTrue($dbman->field_exists($table, new \xmldb_field('streak')));
        $this->assertTrue($dbman->field_exists($table, new \xmldb_field('timemodified')));

        // Verify data was migrated correctly.
        $streaks = $DB->get_records('vocabcoach_streaks', [], 'userid, type');

        // Should have 4 records total (2 users × 2 streak types).
        $this->assertEquals(4, count($streaks));

        // Get streaks for user1.
        $user1_login_streak = $DB->get_record('vocabcoach_streaks', [
            'userid' => $user1->id,
            'cmid' => $cmid,
            'type' => 'login',
        ]);
        $user1_checkall_streak = $DB->get_record('vocabcoach_streaks', [
            'userid' => $user1->id,
            'cmid' => $cmid,
            'type' => 'checkall',
        ]);

        // Verify user1 streaks.
        $this->assertNotFalse($user1_login_streak, 'User1 login streak should exist');
        $this->assertNotFalse($user1_checkall_streak, 'User1 checkall streak should exist');

        // User1 should have a login streak of 5 (yesterday + 4 days before).
        // Note: get_continuous_days counts from yesterday, not today.
        $expected_login_streak_user1 = 5;
        $this->assertEquals($expected_login_streak_user1, $user1_login_streak->streak,
            'User1 login streak should be ' . $expected_login_streak_user1);

        // User1 should have a checkall streak of 3.
        $expected_checkall_streak_user1 = 3;
        $this->assertEquals($expected_checkall_streak_user1, $user1_checkall_streak->streak,
            'User1 checkall streak should be ' . $expected_checkall_streak_user1);

        // Get streaks for user2.
        $user2_login_streak = $DB->get_record('vocabcoach_streaks', [
            'userid' => $user2->id,
            'cmid' => $cmid,
            'type' => 'login',
        ]);
        $user2_checkall_streak = $DB->get_record('vocabcoach_streaks', [
            'userid' => $user2->id,
            'cmid' => $cmid,
            'type' => 'checkall',
        ]);

        // Verify user2 streaks.
        $this->assertNotFalse($user2_login_streak, 'User2 login streak should exist');
        $this->assertNotFalse($user2_checkall_streak, 'User2 checkall streak should exist');

        // User2 should have a login streak of 2.
        $expected_login_streak_user2 = 2;
        $this->assertEquals($expected_login_streak_user2, $user2_login_streak->streak,
            'User2 login streak should be ' . $expected_login_streak_user2);

        // User2 should have a checkall streak of 7.
        $expected_checkall_streak_user2 = 7;
        $this->assertEquals($expected_checkall_streak_user2, $user2_checkall_streak->streak,
            'User2 checkall streak should be ' . $expected_checkall_streak_user2);

        // Verify timemodified fields are set.
        $this->assertGreaterThan(0, $user1_login_streak->timemodified);
        $this->assertGreaterThan(0, $user1_checkall_streak->timemodified);
        $this->assertGreaterThan(0, $user2_login_streak->timemodified);
        $this->assertGreaterThan(0, $user2_checkall_streak->timemodified);
    }

    /**
     * Test upgrade when no activity log data exists.
     *
     * @return void
     */
    public function test_upgrade_with_no_activity_log_data() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $dbman = $DB->get_manager();

        // Drop the streaks table if it exists.
        $table = new \xmldb_table('vocabcoach_streaks');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Ensure no activity log records exist.
        $DB->delete_records('vocabcoach_activitylog');

        // Run the upgrade.
        xmldb_vocabcoach_upgrade(2026022799);

        // Verify table was created.
        $this->assertTrue($dbman->table_exists($table));

        // Verify no streak records were created.
        $this->assertEquals(0, $DB->count_records('vocabcoach_streaks'));
    }

    /**
     * Test upgrade when activity log exists but with gaps (non-consecutive days).
     *
     * @return void
     */
    public function test_upgrade_with_non_consecutive_activity_log() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $dbman = $DB->get_manager();

        // Create test data.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $vocabcoach = $this->getDataGenerator()->create_module('vocabcoach', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('vocabcoach', $vocabcoach->id);
        $cmid = $cm->id;

        // Create activity log with gaps.
        $tracker = new \activity_tracker($user->id, $cmid);
        $basedate = strtotime('today');

        // Log for today and yesterday (2 consecutive days).
        for ($i = 0; $i < 2; $i++) {
            $date = date('d.m.Y', $basedate - ($i * 86400));
            $DB->insert_record('vocabcoach_activitylog', (object)[
                'userid' => $user->id,
                'cmid' => $cmid,
                'date' => $tracker->format_date($date),
                'type' => $tracker->typesdaily['ACT_LOGGED_IN'],
                'details' => 'Test login',
            ]);
        }

        // Skip 2 days and add another entry (this should not count in the streak).
        $date = date('d.m.Y', $basedate - (4 * 86400));
        $DB->insert_record('vocabcoach_activitylog', (object)[
            'userid' => $user->id,
            'cmid' => $cmid,
            'date' => $tracker->format_date($date),
            'type' => $tracker->typesdaily['ACT_LOGGED_IN'],
            'details' => 'Test login',
        ]);

        // Drop and recreate the table.
        $table = new \xmldb_table('vocabcoach_streaks');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Run the upgrade.
        xmldb_vocabcoach_upgrade(2026022799);

        // Verify streak only counts consecutive days.
        $login_streak = $DB->get_record('vocabcoach_streaks', [
            'userid' => $user->id,
            'cmid' => $cmid,
            'type' => 'login',
        ]);

        $this->assertNotFalse($login_streak);
        $this->assertEquals(2, $login_streak->streak, 'Streak should only count consecutive days');
    }

    /**
     * Test that upgrade is idempotent (running it twice doesn't cause issues).
     *
     * @return void
     */
    public function test_upgrade_idempotent() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $dbman = $DB->get_manager();

        // Create test data.
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $vocabcoach = $this->getDataGenerator()->create_module('vocabcoach', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('vocabcoach', $vocabcoach->id);
        $cmid = $cm->id;

        $tracker = new \activity_tracker($user->id, $cmid);
        $basedate = strtotime('today');

        // Create one activity log entry.
        $date = date('d.m.Y', $basedate);
        $DB->insert_record('vocabcoach_activitylog', (object)[
            'userid' => $user->id,
            'cmid' => $cmid,
            'date' => $tracker->format_date($date),
            'type' => $tracker->typesdaily['ACT_LOGGED_IN'],
            'details' => 'Test login',
        ]);

        // Drop the table.
        $table = new \xmldb_table('vocabcoach_streaks');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Run upgrade first time.
        xmldb_vocabcoach_upgrade(2026022799);

        $count_first = $DB->count_records('vocabcoach_streaks');

        // Run upgrade second time (should not duplicate data).
        xmldb_vocabcoach_upgrade(2026022799);

        $count_second = $DB->count_records('vocabcoach_streaks');

        // Verify running twice doesn't create duplicate records.
        $this->assertEquals($count_first, $count_second, 'Running upgrade twice should not duplicate data');
    }

    /**
     * Test upgrade with multiple vocabcoach activities.
     *
     * @return void
     */
    public function test_upgrade_with_multiple_activities() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $dbman = $DB->get_manager();

        // Create test data with multiple activities.
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();

        $vocabcoach1 = $this->getDataGenerator()->create_module('vocabcoach', ['course' => $course1->id]);
        $vocabcoach2 = $this->getDataGenerator()->create_module('vocabcoach', ['course' => $course2->id]);

        $cm1 = get_coursemodule_from_instance('vocabcoach', $vocabcoach1->id);
        $cm2 = get_coursemodule_from_instance('vocabcoach', $vocabcoach2->id);

        $tracker1 = new \activity_tracker($user->id, $cm1->id);
        $tracker2 = new \activity_tracker($user->id, $cm2->id);

        $basedate = strtotime('today');

        // Create logs for first activity (3 days streak).
        for ($i = 0; $i < 3; $i++) {
            $date = date('d.m.Y', $basedate - ($i * 86400));
            $DB->insert_record('vocabcoach_activitylog', (object)[
                'userid' => $user->id,
                'cmid' => $cm1->id,
                'date' => $tracker1->format_date($date),
                'type' => $tracker1->typesdaily['ACT_LOGGED_IN'],
                'details' => 'Test',
            ]);
        }

        // Create logs for second activity (5 days streak).
        for ($i = 0; $i < 5; $i++) {
            $date = date('d.m.Y', $basedate - ($i * 86400));
            $DB->insert_record('vocabcoach_activitylog', (object)[
                'userid' => $user->id,
                'cmid' => $cm2->id,
                'date' => $tracker2->format_date($date),
                'type' => $tracker2->typesdaily['ACT_LOGGED_IN'],
                'details' => 'Test',
            ]);
        }

        // Drop the table.
        $table = new \xmldb_table('vocabcoach_streaks');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Run upgrade.
        xmldb_vocabcoach_upgrade(2026022799);

        // Verify streaks for both activities.
        $streak1 = $DB->get_record('vocabcoach_streaks', [
            'userid' => $user->id,
            'cmid' => $cm1->id,
            'type' => 'login',
        ]);

        $streak2 = $DB->get_record('vocabcoach_streaks', [
            'userid' => $user->id,
            'cmid' => $cm2->id,
            'type' => 'login',
        ]);

        $this->assertNotFalse($streak1);
        $this->assertNotFalse($streak2);
        $this->assertEquals(3, $streak1->streak);
        $this->assertEquals(5, $streak2->streak);
    }
}
