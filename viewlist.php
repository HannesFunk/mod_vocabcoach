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
require_once(__DIR__.'/classes/external/check_vocab_api.php');
require_once(__DIR__.'/classes/forms/view_list_form.php');
require_once(__DIR__.'/classes/vocab_manager.php');

$id = required_param('id', PARAM_INT);
$userid = $USER->id;
$listid = required_param('listid', PARAM_INT);
$list_info = $DB->get_record('vocabcoach_lists', ['id'=>$listid],
        'title, book, unit, year');

$cm = get_coursemodule_from_id('vocabcoach', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$moduleinstance = $DB->get_record('vocabcoach', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);
$modulecontext = context_module::instance($cm->id);

$PAGE->set_context($modulecontext);
$PAGE->set_url('/mod/vocabcoach/viewlist.php');
$PAGE->set_title('Vokabelcoach - Liste');
$PAGE->set_heading('Vokabelcoach - Liste');
$PAGE->navbar->add($list_info->title);
$PAGE->requires->css('/mod/vocabcoach/styles/check.css');

$check_api = new \mod_vocabcoach\external\check_vocab_api();
$vocab_array = $check_api->get_list_vocabs($listid);

$templateParameters = [
    'title'=>$list_info->title,
    'vocabarray'=>$vocab_array,
    'third_active' => $moduleinstance->thirdactive,
];

$mform = new view_list_form(null, ['vocabdata' => json_encode($vocab_array), 'id' => $id, 'listid' => $listid]);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/mod/vocabcoach/lists.php', ['id'=>$id]));
} else if ($formdata = $mform->get_data()) {
    $vm = new \mod_vocabcoach\vocab_manager($userid);
    foreach (array_keys((array) $formdata) as $key) {
        if (!str_contains($key, "vocab-")) {
            continue;
        }
        $vocabid = substr($key, strlen("vocab-"));
        $vm->add_vocab_to_user($vocabid, $id);
    }
    redirect(new moodle_url('/mod/vocabcoach/view.php', ['id'=>$id]));
}

echo $OUTPUT->header();
echo $OUTPUT->heading('Vokabelliste '.$list_info->title);
$mform->display();
echo $OUTPUT->footer();
