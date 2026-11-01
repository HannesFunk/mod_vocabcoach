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
 * Unit tests for the box manager class.
 *
 * @package   mod_vocabcoach
 * @copyright 2026, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_vocabcoach;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/vocabcoach/classes/box_manager.php');

/**
 * Test class for box_manager.
 *
 * @package   mod_vocabcoach
 * @copyright 2026 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \mod_vocabcoach\box_manager
 */
final class box_manager_test extends \advanced_testcase {
    /** @var \stdClass The course module the boxes belong to. */
    private \stdClass $cm;

    /** @var \stdClass The user learning the vocab. */
    private \stdClass $user;

    /** @var \mod_vocabcoach_generator The plugin data generator. */
    private \mod_vocabcoach_generator $plugingen;

    /**
     * Build a course, an activity and an enrolled student for each test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();

        $datagen = $this->getDataGenerator();
        $course = $datagen->create_course();
        $this->user = $datagen->create_user();
        $datagen->enrol_user($this->user->id, $course->id, 'student');

        $instance = $datagen->create_module('vocabcoach', ['course' => $course->id]);
        $this->cm = get_coursemodule_from_instance('vocabcoach', $instance->id);
        $this->plugingen = $datagen->get_plugin_generator('mod_vocabcoach');

        $this->setAdminUser();
    }

    /**
     * Put one card in the test user's box.
     *
     * @param string $word Distinct text for this card; vocab items are deduplicated on their text.
     * @param int $stage The box to place it in.
     * @param int|null $lastchecked When it was last checked; null leaves it due.
     */
    private function add_card(string $word, int $stage, ?int $lastchecked = null): void {
        $this->plugingen->create_vocab([
            'userid' => $this->user->id,
            'cmid' => $this->cm->id,
            'front' => $word,
            'back' => $word . '-back',
            'stage' => $stage,
            'lastchecked' => $lastchecked,
        ]);
    }

    /**
     * Build the box manager for the test user.
     *
     * @return box_manager
     */
    private function manager(): box_manager {
        return new box_manager($this->cm->id, $this->user->id);
    }

    /**
     * The view template iterates the result, so it has to be a zero-indexed list ordered by stage,
     * including when the lowest boxes are empty and the rows therefore arrive out of order.
     */
    public function test_get_box_details_is_an_ordered_zero_indexed_list(): void {
        $this->add_card('zwei', 2);
        $this->add_card('vier', 4);

        $boxes = $this->manager()->get_box_details();

        $this->assertSame([0, 1, 2, 3, 4], array_keys($boxes));
        $this->assertSame([1, 2, 3, 4, 5], array_column($boxes, 'stage'));
    }

    /**
     * A user who owns no vocab gets five empty boxes and no PHP warnings.
     */
    public function test_user_without_vocab_has_five_empty_boxes(): void {
        $boxes = $this->manager()->get_box_details();

        $this->assertCount(5, $boxes);
        foreach ($boxes as $box) {
            $this->assertSame(0, $box->total);
            $this->assertSame(0, $box->due);
            $this->assertSame('empty', $box->status);
            $this->assertSame('-', $box->nextdue);
        }
        $this->assertSame(0, $this->manager()->get_total_due());
    }

    /**
     * Totals, due counts and statuses are reported per box.
     */
    public function test_counts_and_status_per_box(): void {
        $this->add_card('eins', 1);
        $this->add_card('eins-b', 1);
        $this->add_card('drei', 3, time());

        $boxes = $this->manager()->get_box_details();

        $this->assertSame(2, $boxes[0]->total);
        $this->assertSame(2, $boxes[0]->due);
        $this->assertSame('todo', $boxes[0]->status);
        $this->assertSame(get_string('duetime_now', 'mod_vocabcoach'), $boxes[0]->nextdue);

        $this->assertSame(0, $boxes[1]->total);
        $this->assertSame('empty', $boxes[1]->status);

        $this->assertSame(1, $boxes[2]->total);
        $this->assertSame(0, $boxes[2]->due);
        $this->assertSame('done', $boxes[2]->status);
        $this->assertNotSame('-', $boxes[2]->nextdue);

        $this->assertSame(2, $this->manager()->get_total_due());
    }

    /**
     * A vocabdata row pointing at a deleted vocab item must not be counted.
     *
     * Such a row is unreachable: the check endpoint joins the vocab table and so never serves it,
     * meaning its lastchecked can never advance. Counting it pins the box at 'todo' forever and
     * keeps the due-vocab notification firing every day.
     */
    public function test_orphan_vocabdata_is_ignored(): void {
        $this->add_card('echt', 1);
        $this->plugingen->create_orphan_vocabdata([
            'userid' => $this->user->id,
            'cmid' => $this->cm->id,
            'stage' => 5,
            'lastchecked' => 0,
        ]);

        $boxes = $this->manager()->get_box_details();

        $this->assertSame(0, $boxes[4]->total);
        $this->assertSame(0, $boxes[4]->due);
        $this->assertSame('empty', $boxes[4]->status);

        $this->assertSame(1, $boxes[0]->total);
        $this->assertSame(1, $this->manager()->get_total_due());
    }

    /**
     * Cards count as due only once their box's repetition interval has elapsed.
     *
     * The offsets are a minute rather than a second because vocabhelper::old_timestamp() reads the
     * clock again when the box query runs, so the threshold drifts slightly during the test.
     */
    public function test_due_boundary(): void {
        $vocabhelper = new vocabhelper($this->cm->id);
        $threshold = $vocabhelper->old_timestamp($vocabhelper->boxtimes[1]);

        $this->add_card('alt', 1, $threshold - MINSECS);
        $this->add_card('neu', 1, $threshold + MINSECS);

        $boxes = $this->manager()->get_box_details();

        $this->assertSame(2, $boxes[0]->total);
        $this->assertSame(1, $boxes[0]->due);
        $this->assertSame('todo', $boxes[0]->status);
    }

    /**
     * Only the cards belonging to this user and this activity are counted.
     */
    public function test_counts_are_scoped_to_user_and_activity(): void {
        $datagen = $this->getDataGenerator();
        $otheruser = $datagen->create_user();
        $othercm = get_coursemodule_from_instance(
            'vocabcoach',
            $datagen->create_module('vocabcoach', ['course' => $this->cm->course])->id
        );

        $this->add_card('mein', 1);
        $this->plugingen->create_vocab([
            'userid' => $otheruser->id, 'cmid' => $this->cm->id, 'front' => 'fremd', 'back' => 'fremd-b',
        ]);
        $this->plugingen->create_vocab([
            'userid' => $this->user->id, 'cmid' => $othercm->id, 'front' => 'anderswo', 'back' => 'anderswo-b',
        ]);

        $this->assertSame(1, $this->manager()->get_box_details()[0]->total);
        $this->assertSame(1, $this->manager()->get_total_due());
    }
}
