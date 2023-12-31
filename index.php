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
 * Display information about all the mod_vocabcoach modules in the requested course.
 *
 * @package     mod_vocabcoach
 * @copyright   2023 J. Funk, johannesfunk@outlook.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
global $DB, $PAGE, $OUTPUT;

require_once(__DIR__.'/lib.php');

$id = required_param('id', PARAM_INT);

$course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);
require_course_login($course);

$coursecontext = context_course::instance($course->id);

$PAGE->set_url('/mod/vocabcoach/index.php', ['id' => $id]);
$PAGE->set_title(format_string($course->fullname));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($coursecontext);

echo $OUTPUT->header();

$modulenameplural = get_string('modulenameplural', 'mod_vocabcoach');
echo $OUTPUT->heading($modulenameplural);

$vocabcoachs = get_all_instances_in_course('vocabcoach', $course);

if (empty($vocabcoachs)) {
    notice(get_string('no$vocabcoachinstances', 'mod_vocabcoach'), new moodle_url('/course/view.php', ['id' => $course->id]));
}

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

if ($course->format == 'weeks') {
    $table->head  = [get_string('week'), get_string('name')];
    $table->align = ['center', 'left'];
} else if ($course->format == 'topics') {
    $table->head  = [get_string('topic'), get_string('name')];
    $table->align = ['center', 'left', 'left', 'left'];
} else {
    $table->head  = [get_string('name')];
    $table->align = ['left', 'left', 'left'];
}

foreach ($vocabcoachs as $vocabcoach) {
    if (!$vocabcoach->visible) {
        $link = html_writer::link(
            new moodle_url('/mod/vocabcoach/view.php', ['id' => $vocabcoach->coursemodule]),
            format_string($vocabcoach->name),
            ['class' => 'dimmed']);
    } else {
        $link = html_writer::link(
            new moodle_url('/mod/vocabcoach/view.php', ['id' => $vocabcoach->coursemodule]),
            format_string($vocabcoach->name));
    }

    if ($course->format == 'weeks' || $course->format == 'topics') {
        $table->data[] = [$vocabcoach->section, $link];
    } else {
        $table->data[] = [$link];
    }
}

echo html_writer::table($table);
echo $OUTPUT->footer();
