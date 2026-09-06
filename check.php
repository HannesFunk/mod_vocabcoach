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

$cmid = required_param('id', PARAM_INT);
$listid = optional_param('listid', null, PARAM_INT);

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
$force = optional_param('force', false, PARAM_BOOL);

$userprefs = new \mod_vocabcoach\user_preferences($cmid, $USER->id);

$checkcontext = [
    'force' => $force,
    'cmid' => $cmid,
    'currentmode' => $userprefs->get_mode(),
    'fromList' => $listid !== null,
];

if ($listid) {
    $lm = new \mod_vocabcoach\list_manager($listid, $cmid);
    $checkcontext['subheadline']  = $lm->get_list()->get('title');
    $checkcontext['items'] = $lm->get_vocabs();
} else {
    $stage = required_param('stage', PARAM_INT);
    $checkcontext['subheadline']  = get_string('box', 'mod_vocabcoach') . " " . $stage;

    $vocabapi = new \mod_vocabcoach\external\vocab_api();
    $checkcontext['items'] = vocab_api::clean_returnvalue(
        vocab_api::get_user_vocabs_returns(),
        $vocabapi->get_user_vocabs($cmid, $stage, $force)
    );
}

$checkcontext['modelabels'] = [
    'front' => get_string('checkmode_front', 'mod_vocabcoach'),
    'back' => get_string('checkmode_back', 'mod_vocabcoach'),
    'random' => get_string('checkmode_random', 'mod_vocabcoach'),
    'type' => get_string('checkmode_type', 'mod_vocabcoach'),
];
$checkcontext['buttonLabels'] = [
    'no' => get_string('check_button_no', 'mod_vocabcoach'),
    'yes' => get_string('check_button_yes', 'mod_vocabcoach'),
    'typeCheck' => get_string('check_button_verify', 'mod_vocabcoach'),
    'typeReveal' => get_string('check_button_reveal', 'mod_vocabcoach'),
    'typeNo' => get_string('check_button_no', 'mod_vocabcoach'),
    'typeOverride' => get_string('check_button_override', 'mod_vocabcoach'),
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('mod_vocabcoach/check', ['propsjson' => json_encode($checkcontext)]);
echo $OUTPUT->footer();
