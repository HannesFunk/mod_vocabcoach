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
require_once($CFG->dirroot . '/mod/vocabcoach/classes/streak_restorer.php');

/**
 * Test class for streak_restorer.
 *
 * @package   mod_vocabcoach
 * @copyright 2026 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \mod_vocabcoach\streak_restorer
 */
class streak_manager_test extends \advanced_testcase
{

    /**
     * Test can restore streak when limit not reached.
     */
    public function test_correctly_updates_streak()
    {
        global $DB;
        $this->resetAfterTest(true);

        $datagen = $this->getDataGenerator();

        $course = $datagen->create_course();

        $user1 = $datagen->create_user();
//        $user2 = $datagen->create_user();
//        $user3 = $datagen->create_user();
//        $user4 = $datagen->create_user();

        $vc = $datagen->create_module('vocabcoach', ['course' => $course->id]);
        $cm = get_coursemodule_from_instance('vocabcoach', $vc->id);

        // User 1: Has already logged in today. Streak should remain
        $streak = (object)[
            'userid' => $user1->id,
            'cmid' => $cm->id,
            'streak' => 17,
            'lastmodified' => strtotime("-2 hours"),
            'type' => 'login',
        ];

        $DB->insert_record('vocabcoach_streaks', $streak);

        $sm = new streak_manager($user1->id, $cm->id);
        $sm->update_type('login');
        $streakinfo = $sm->get_streak_info('login');

        $this->assertEquals($streakinfo->streak, 17);
    }
}