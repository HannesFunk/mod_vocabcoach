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
global $PAGE, $OUTPUT, $DB, $USER;

/**
 * Prints an instance of mod_vocabcoach.
 *
 * @package     mod_vocabcoach
 * @copyright   2023 J. Funk, johannesfunk@outlook.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_vocabcoach\box_manager;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once(__DIR__.'/classes/forms/add_vocab_form.php');
require_once(__DIR__.'/classes/box_manager.php');
require_once(__DIR__.'/classes/activity_tracker.php');

// Course module id.
$id = optional_param('id', 0, PARAM_INT);

// Activity instance id.
$v = optional_param('v', 0, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('vocabcoach', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('vocabcoach', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    $moduleinstance = $DB->get_record('vocabcoach', array('id' => $v), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('vocabcoach', $moduleinstance->id, $course->id, false, MUST_EXIST);
}

require_login($course, true, $cm);
$modulecontext = context_module::instance($cm->id);

$PAGE->set_url('/mod/vocabcoach/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);

$PAGE->requires->css('/mod/vocabcoach/styles/boxes.css');
$PAGE->requires->css('/mod/vocabcoach/styles/activity.css');
$PAGE->requires->js_call_amd('mod_vocabcoach/box_actions', 'init', array($id));

$box_manager = new box_manager($id, $USER->id);
$box_data = $box_manager->get_box_details();

$al = new activity_tracker($USER->id, $id);
$al->log($al->types['ACT_LOGGED_IN']);
if ($al->is_all_done($box_data)) {
    $al->log($al->types['ACT_CHECKED_ALL']);
}

$templatecontext = [
    'addvocaburl'=>new moodle_url('/mod/vocabcoach/add_vocab.php', ['id'=>$cm->id, 'mode'=>'user']),
    'addlisturl'=>new moodle_url('/mod/vocabcoach/add_vocab.php', ['id'=>$cm->id, 'mode'=>'list']),
    'boxdata'=> $box_data,
    'listsurl'=>new moodle_url('/mod/vocabcoach/lists.php', ['id'=>$cm->id]),
    'days_logged_in' => $al->get_continuous_days($al->types['ACT_LOGGED_IN']),
    'days_checked_all' => $al->get_continuous_days($al->types['ACT_CHECKED_ALL']),
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('mod_vocabcoach/view', (object) $templatecontext);
echo $OUTPUT->footer();
