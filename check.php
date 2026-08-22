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

use mod_vocabcoach\external\vocab_api;

require(__DIR__ . '/../../config.php');
global $PAGE, $OUTPUT, $DB, $USER;
require_once(__DIR__ . '/lib.php');
require_once(__DIR__ . '/classes/vocab_manager.php');

$cmid = required_param('id', PARAM_INT);

$cm = get_coursemodule_from_id('vocabcoach', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$moduleinstance = $DB->get_record('vocabcoach', ['id' => $cm->instance], '*', MUST_EXIST);

require_login($course, true, $cm);
$modulecontext = context_module::instance($cm->id);

$PAGE->set_context($modulecontext);
$PAGE->set_url('/mod/vocabcoach/check.php');
$PAGE->set_title(get_string('check_pagetitle', 'mod_vocabcoach'));
$PAGE->set_heading(get_string('check_pagetitle', 'mod_vocabcoach'));
$PAGE->navbar->add(get_string('check', 'mod_vocabcoach'));

$PAGE->requires->css('/mod/vocabcoach/styles/check.css');
$PAGE->requires->css('/mod/vocabcoach/styles/style.css');
$source = optional_param('source', 'user', PARAM_TEXT);
$force = optional_param('force', false, PARAM_BOOL);

$userprefs = new \mod_vocabcoach\user_preferences($cmid, $USER->id);
$checkcontext = $userprefs->get_template_context();

$checkcontext = [
    'userid' => $USER->id,
    'force' => $force,
    'cmid' => $cmid,
    'source' => $source,
    'mode'  => 'front'
];
if ($source === 'user') {
    $stage = required_param('stage', PARAM_INT);
    $checkcontext['subheadline'] = get_string('box', 'mod_vocabcoach') . " " . $stage;

    $vocabapi = new \mod_vocabcoach\external\vocab_api();
    $vocabarray = vocab_api::clean_returnvalue(
        vocab_api::get_user_vocabs_returns(),
        $vocabapi->get_user_vocabs($cmid, $stage, $force)
    );
    $checkcontext['itemsjson'] = json_encode($vocabarray);
} else if ($source === 'list') {
    $jsdata['listid'] = required_param('listid', PARAM_INT);
    $listrecord = $DB->get_record('vocabcoach_lists', ['id' => $jsdata['listid']], 'title', MUST_EXIST);
    $subheadline = $listrecord->title;
} else {
    throw new \invalid_parameter_exception();
}


$checkcontext['modelabelsjson'] = json_encode([
    'front' => get_string('checkmode_front', 'mod_vocabcoach'),
    'back' => get_string('checkmode_back', 'mod_vocabcoach'),
    'random' => get_string('checkmode_random', 'mod_vocabcoach'),
    'type' => get_string('checkmode_type', 'mod_vocabcoach'),
]);

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('mod_vocabcoach/check', $checkcontext);
echo $OUTPUT->footer();
