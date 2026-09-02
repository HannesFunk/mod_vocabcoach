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

use core\notification;
use mod_vocabcoach\local\vocablist;
use mod_vocabcoach\vocab_manager;
use mod_vocabcoach\form\add_vocab_form;

require(__DIR__ . '/../../config.php');

$cmid = required_param('id', PARAM_INT);
$listid = optional_param('listid', null, PARAM_INT);
$addtouser = optional_param('addtouser', false, PARAM_BOOL);

$cm = get_coursemodule_from_id('vocabcoach', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$moduleinstance = $DB->get_record('vocabcoach', ['id' => $cm->instance], '*', MUST_EXIST);

require_login($course, true, $cm);
$modulecontext = context_module::instance($cm->id);

$PAGE->set_url(new moodle_url('/mod/vocabcoach/add_vocab.php', ['id' => $cm->id, 'listid' => $listid, 'addtouser' => $addtouser]));
$PAGE->set_context($modulecontext);
$PAGE->set_title(get_string('add_vocab_title', 'mod_vocabcoach'));
$PAGE->set_heading(get_string('add_vocab_title', 'mod_vocabcoach'));

$formparameters = [
    'cmid' => $cmid,
    'addtouser' => $addtouser,
    'listid' => $listid,
    'vocabarray' => [],
    'instructions' => format_text($moduleinstance->instructions, FORMAT_HTML, ['context' => $modulecontext]),
    'desc_front' => $moduleinstance->desc_front,
    'desc_back' => $moduleinstance->desc_back,
    'year' => $moduleinstance->year,
];

if ($listid) {
    $list = new vocablist($listid);
    $userisowner = $list->get('createdby') === (int) $USER->id;
    $canedit = has_capability('mod/vocabcoach:delete_lists', $modulecontext) || $userisowner;
    if (!$canedit) {
        redirect(
            new moodle_url('/mod/vocabcoach/view.php', ['id' => $cm->id]),
            get_string('edit_list_not_allowed', 'mod_vocabcoach'),
            notification::ERROR
        );
    }

    $lm = new \mod_vocabcoach\listmanager($listid, $cmid);
    $formparameters['vocabarray'] = $lm->get_vocabs();

    $mform = new add_vocab_form(null, $formparameters);

    $listinfo = [];
    foreach (['title', 'book', 'year', 'unit', 'private'] as $key) {
        $listinfo['list_' . $key] = $list->get($key);
    }
    $mform->set_data($listinfo);
} else {
    $mform = new add_vocab_form(null, $formparameters);
}

if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot . '/mod/vocabcoach/view.php?id=' . $cm->id);
} else if ($formdata = $mform->get_data()) {
    global $USER;
    $userid = $USER->id;
    $redirect = true;
    $vocabmanager = new vocab_manager($userid);

    $vocabarray = json_decode($formdata->vocabs, true);

    // If mode is user, don't bother about lists.
    if ($addtouser) {
        $vm = new vocab_manager();
        $vm->add_vocabs_to_user($vocabarray, $cmid);
        redirect(
            new moodle_url('/mod/vocabcoach/view.php', ['id' => $cm->id]),
            get_string('add_vocab_successful', 'mod_vocabcoach')
        );
    }

    $listinfo = [
        'title' => $formdata->list_title,
        'book' => $formdata->list_book,
        'year' => $formdata->list_year,
        'unit' => $formdata->list_unit,
        'private' => $formdata->list_private,
    ];

    if ($listid) {
        $list = new vocablist($listid);
        $list->set_many($listinfo);
        $list->update();
    } else {
        $listinfo['createdby'] = $USER->id;
        $listinfo['cmid'] = $cmid;
        $list = new vocablist(0, (object)$listinfo);
        $list->create();
        $listid = $list->get('id');
    }

    $lm = new \mod_vocabcoach\listmanager($listid, $cmid);
    $lm->set_vocabs($vocabarray);

    if ($formdata->add_to_user_database) {
        $vocabmanager->add_list_to_user_database($listid, $cmid);
    }

    if ($formdata->list_distribute_now) {
        $listsapi = new \mod_vocabcoach\external\lists_api();
        $listsapi->distribute_list($listid, $cmid);
    }

    redirect(
        new moodle_url('/mod/vocabcoach/view.php', ['id' => $cm->id]),
        get_string('edit_vocab_successful', 'mod_vocabcoach')
    );
}

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
