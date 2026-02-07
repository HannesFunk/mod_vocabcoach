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
 * Prints an instance of mod_vocabcoach.
 *
 * @package     mod_vocabcoach
 * @copyright   2023 J. Funk, johannesfunk@outlook.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
global $PAGE, $OUTPUT, $DB, $USER;
require_once(__DIR__.'/lib.php');
require_once(__DIR__.'/classes/box_manager.php');
require_once(__DIR__.'/classes/activity_tracker.php');

use mod_vocabcoach\box_manager;
// Course module id.
$cmid = optional_param('id', 0, PARAM_INT);

// Activity instance id.
$v = optional_param('v', 0, PARAM_INT);

if ($cmid) {
    $cm = get_coursemodule_from_id('vocabcoach', $cmid, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('vocabcoach', ['id' => $cm->instance], '*', MUST_EXIST);
} else {
    $moduleinstance = $DB->get_record('vocabcoach', ['id' => $v], '*', MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $moduleinstance->course], '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('vocabcoach', $moduleinstance->id, $course->id, false, MUST_EXIST);
}

require_login($course, true, $cm);
$modulecontext = context_module::instance($cm->id);

$PAGE->set_url('/mod/vocabcoach/view.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);

$PAGE->requires->css('/mod/vocabcoach/styles/boxes.css');
$PAGE->requires->css('/mod/vocabcoach/styles/activity.css');
$PAGE->requires->js_call_amd('mod_vocabcoach/view', 'init', [$cmid, $USER->id, $course->id]);

$boxmanager = new box_manager($cmid, $USER->id);
$boxdata = $boxmanager->get_box_details();

$al = new activity_tracker($USER->id, $cmid);
$al->log($al->typesdaily['ACT_LOGGED_IN']);
if ($al->is_all_done($boxdata)) {
    $al->log($al->typesdaily['ACT_CHECKED_ALL']);
}

$userpreferences = new \mod_vocabcoach\user_preferences($cm->id, $USER->id);
$prefcontext = $userpreferences->get_template_context();

$templatecontext = [
    'boxdata' => $boxdata,
    'days_logged_in' => $al->get_continuous_days($al->typesdaily['ACT_LOGGED_IN']),
    'days_checked_all' => $al->get_continuous_days($al->typesdaily['ACT_CHECKED_ALL']),
    'cmid' => $cm->id,
    'userid' => $USER->id,
    'courseNotificationsEnabled' => $moduleinstance->notifications_enabled == 1,
    ...$prefcontext,
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('mod_vocabcoach/view', (object) $templatecontext);

$cf = new \mod_vocabcoach\course_features($course->id, $cmid, $USER->id);

if (has_capability('mod/vocabcoach:show_class_total', $modulecontext)) {
    $total = $cf->get_class_total();
    if ($total == -1) {
        $total = "-";
    }
    $canliveupdate = has_capability('mod/vocabcoach:show_class_total_live', $modulecontext);
    echo $OUTPUT->render_from_template('mod_vocabcoach/class-total', (object)['total' => $total, 'liveupdate' => $canliveupdate]);
}

if (has_capability('mod/vocabcoach:show_leaderboard', $modulecontext)) {
    $leaderboarddata = $cf->get_leaderboard();
    if (!empty($leaderboarddata)) {
        echo $OUTPUT->render_from_template('mod_vocabcoach/leaderboard', (object) ['leaders' => $leaderboarddata]);
    }
}
echo $OUTPUT->footer();
