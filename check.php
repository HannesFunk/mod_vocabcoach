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
global $PAGE, $OUTPUT, $DB;

/**
 * Prints an instance of mod_vocabcoach.
 *
 * @package     mod_vocabcoach
 * @copyright   2023 J. Funk, johannesfunk@outlook.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


global $USER;

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once(__DIR__.'/classes/vocab_manager.php');
require_once(__DIR__.'/classes/forms/check_settings_form.php');

$id = required_param('id', PARAM_INT);

$cm = get_coursemodule_from_id('vocabcoach', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$moduleinstance = $DB->get_record('vocabcoach', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);
$modulecontext = context_module::instance($cm->id);

$PAGE->set_context($modulecontext);
$PAGE->set_url('/mod/vocabcoach/view.php');
$PAGE->set_title('Vokabelcoach - Abfrage');
$PAGE->set_heading('Vokabelcoach - Abfrage');

$check_settings_form = new check_settings_form();
$form_html = $check_settings_form->toHtml();


$PAGE->requires->css('/mod/vocabcoach/styles/check.css');
$PAGE->requires->css('/mod/vocabcoach/styles/style.css');
$mode = optional_param('mode', 'user', PARAM_TEXT);

$js_data = [
    'userid'=>$USER->id,
    'force'=>optional_param('force', false, PARAM_BOOL),
    'cmid'=>$id,
    'mode'=>$mode,
];
if ($mode === 'user') {
    $js_data['stage'] = required_param('stage', PARAM_INT);

} else if ($mode === 'list') {
    $js_data['listid'] = required_param('listid', PARAM_INT);
}

$PAGE->requires->js_call_amd(
        'mod_vocabcoach/check',
        'init',
        array(json_encode($js_data))
);

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('mod_vocabcoach/check', ['check-settings-form'=>$form_html]);
echo $OUTPUT->footer();
