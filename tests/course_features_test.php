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
 * Unit tests for the course features class.
 *
 * @package   mod_vocabcoach
 * @copyright 2026, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_vocabcoach;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/vocabcoach/classes/course_features.php');

/**
 * Test class for course_features.
 *
 * @package   mod_vocabcoach
 * @copyright 2026 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \mod_vocabcoach\course_features
 */
final class course_features_test extends \advanced_testcase {
    /** @var \stdClass The course holding the activity. */
    private \stdClass $course;

    /** @var \stdClass The course module record. */
    private \stdClass $cm;

    /** @var \core\context\module The module context. */
    private \core\context\module $context;

    /** @var \mod_vocabcoach_generator The plugin data generator. */
    private \mod_vocabcoach_generator $plugingen;

    /**
     * Build a course and an activity for each test. Students are added per test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();

        $datagen = $this->getDataGenerator();
        $this->course = $datagen->create_course();
        $instance = $datagen->create_module('vocabcoach', ['course' => $this->course->id]);
        $this->cm = get_coursemodule_from_instance('vocabcoach', $instance->id);
        $this->context = \core\context\module::instance($this->cm->id);
        $this->plugingen = $datagen->get_plugin_generator('mod_vocabcoach');

        $this->setAdminUser();
    }

    /**
     * Create a user enrolled in the course as a student.
     *
     * @return \stdClass
     */
    private function create_student(): \stdClass {
        $datagen = $this->getDataGenerator();
        $user = $datagen->create_user();
        $this->assertTrue($datagen->enrol_user($user->id, $this->course->id, 'student'));

        return $user;
    }

    /**
     * Put one card in a user's box.
     *
     * @param int $userid
     * @param string $word Distinct text for this card; vocab items are deduplicated on their text.
     * @param int $stage
     * @param int|null $lastchecked When it was last checked; null leaves it due.
     */
    private function add_card(int $userid, string $word, int $stage = 1, ?int $lastchecked = null): void {
        $this->plugingen->create_vocab([
            'userid' => $userid,
            'cmid' => $this->cm->id,
            'front' => $word,
            'back' => $word . '-back',
            'stage' => $stage,
            'lastchecked' => $lastchecked,
        ]);
    }

    /**
     * Build the course features object for this activity.
     *
     * @return course_features
     */
    private function features(): course_features {
        return new course_features($this->context);
    }

    /**
     * The class total is the real number of due cards across all students, not a truthy placeholder.
     */
    public function test_class_total_counts_every_due_card(): void {
        $first = $this->create_student();
        $second = $this->create_student();

        $this->add_card($first->id, 'eins');
        $this->add_card($first->id, 'zwei');
        $this->add_card($second->id, 'drei');

        $this->assertSame(3, $this->features()->get_class_total());
    }

    /**
     * Cards that are present but not yet due are left out of the class total.
     */
    public function test_class_total_excludes_cards_that_are_not_due(): void {
        $student = $this->create_student();

        $this->add_card($student->id, 'faellig');
        $this->add_card($student->id, 'frisch', 1, time());

        $this->assertSame(1, $this->features()->get_class_total());
    }

    /**
     * Orphan vocabdata rows must not inflate the class total.
     */
    public function test_class_total_ignores_orphans(): void {
        $student = $this->create_student();

        $this->add_card($student->id, 'echt');
        $this->plugingen->create_orphan_vocabdata([
            'userid' => $student->id,
            'cmid' => $this->cm->id,
            'stage' => 1,
            'lastchecked' => 0,
        ]);

        $this->assertSame(1, $this->features()->get_class_total());
    }

    /**
     * With no students enrolled the total is the -1 sentinel that the view renders as a dash.
     */
    public function test_class_total_without_students(): void {
        $this->assertSame(-1, $this->features()->get_class_total());
    }

    /**
     * A student with nothing due still belongs on the leaderboard, with a count of zero.
     */
    public function test_leaderboard_includes_students_with_nothing_due(): void {
        $busy = $this->create_student();
        $idle = $this->create_student();

        $this->add_card($busy->id, 'eins');
        $this->add_card($busy->id, 'zwei');

        $numbers = [];
        foreach ($this->features()->get_leaderboard($idle->id) as $row) {
            $numbers[(int)$row->id] = $row->number;
        }

        $this->assertArrayHasKey((int)$idle->id, $numbers);
        $this->assertSame(0, $numbers[(int)$idle->id]);
        $this->assertSame(2, $numbers[(int)$busy->id]);
    }

    /**
     * Orphan rows must not inflate a student's leaderboard count.
     */
    public function test_leaderboard_ignores_orphans(): void {
        $student = $this->create_student();

        $this->add_card($student->id, 'echt');
        $this->plugingen->create_orphan_vocabdata([
            'userid' => $student->id,
            'cmid' => $this->cm->id,
            'stage' => 1,
            'lastchecked' => 0,
        ]);

        $board = $this->features()->get_leaderboard($student->id);

        $this->assertSame(1, $board[0]->number);
    }

    /**
     * The viewer is flagged even when they are ranked first.
     */
    public function test_leaderboard_marks_the_viewer_at_rank_one(): void {
        $first = $this->create_student();
        $second = $this->create_student();

        $this->add_card($second->id, 'eins');

        $board = $this->features()->get_leaderboard($first->id);

        $this->assertSame((int)$first->id, (int)$board[0]->id);
        $this->assertSame(1, $board[0]->rank);
        $this->assertTrue($board[0]->self);
        $this->assertFalse($board[1]->self);
    }

    /**
     * Tied students share a rank, which is printed only on the first of them.
     */
    public function test_leaderboard_blanks_the_rank_of_tied_students(): void {
        $this->create_student();
        $viewer = $this->create_student();
        $behind = $this->create_student();

        $this->add_card($behind->id, 'eins');

        $board = $this->features()->get_leaderboard($viewer->id);

        $this->assertCount(3, $board);
        $this->assertSame(1, $board[0]->rank);
        $this->assertSame('', $board[1]->rank);
        $this->assertSame(2, $board[2]->rank);
    }

    /**
     * A second enrolment must not multiply a student's due count.
     *
     * Joining the enrolment tables alongside the vocabdata table produces a cartesian product, so
     * a student enrolled by two methods had their count multiplied by the number of enrolments.
     */
    public function test_leaderboard_is_not_multiplied_by_a_second_enrolment(): void {
        global $DB;

        $student = $this->create_student();
        $this->add_card($student->id, 'eins');
        $this->add_card($student->id, 'zwei');
        $this->add_card($student->id, 'drei');

        $selfplugin = enrol_get_plugin('self');
        if (!$selfplugin) {
            $this->markTestSkipped('The self enrolment plugin is not available.');
        }
        $studentroleid = $DB->get_field('role', 'id', ['shortname' => 'student'], MUST_EXIST);
        $instanceid = $selfplugin->add_instance($this->course, [
            'status' => ENROL_INSTANCE_ENABLED,
            'roleid' => $studentroleid,
        ]);
        $instance = $DB->get_record('enrol', ['id' => $instanceid], '*', MUST_EXIST);
        $selfplugin->enrol_user($instance, $student->id, $studentroleid);

        $this->assertSame(
            2,
            $DB->count_records('user_enrolments', ['userid' => $student->id]),
            'Precondition: the student must hold two enrolments for this test to mean anything.'
        );

        $board = $this->features()->get_leaderboard($student->id);

        $this->assertCount(1, $board);
        $this->assertSame(3, $board[0]->number);
        $this->assertSame(3, $this->features()->get_class_total());
    }
}
