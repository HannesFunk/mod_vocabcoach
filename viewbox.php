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
require_once(__DIR__ . '/classes/external/vocab_api.php');
require_once(__DIR__.'/classes/forms/view_box_form.php');
require_once(__DIR__.'/classes/vocab_manager.php');

$id = required_param('id', PARAM_INT);
$stage = required_param('stage', PARAM_INT);

$cm = get_coursemodule_from_id('vocabcoach', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$moduleinstance = $DB->get_record('vocabcoach', ['id' => $cm->instance], '*', MUST_EXIST);

require_login($course, true, $cm);
$modulecontext = context_module::instance($cm->id);

$PAGE->set_context($modulecontext);
$PAGE->set_url('/mod/vocabcoach/viewlist.php');
$PAGE->set_title('Vokabelcoach - Liste');
$PAGE->set_heading('Vokabelcoach - Liste');
$PAGE->navbar->add("Box ".$stage);
$PAGE->requires->css('/mod/vocabcoach/styles/check.css');
$PAGE->requires->js_call_amd('mod_vocabcoach/viewbox', 'init');

$checkapi = new \mod_vocabcoach\external\vocab_api();
$vocabarray = $checkapi->get_user_vocabs($USER->id, $id, $stage, true);

$mform = new view_box_form(null,
        ['vocabdata' => json_encode($vocabarray),
        'id' => $id,
        'third_active' => $moduleinstance->thirdactive,
]);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/mod/vocabcoach/lists.php', ['id' => $id]));
}

echo $OUTPUT->header();
echo $OUTPUT->heading('Box '.$stage);
$mform->display();
echo $OUTPUT->footer();
