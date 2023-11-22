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

use core\notification;
use mod_vocabcoach\external\check_vocab_api;
use mod_vocabcoach\vocab_manager;

require(__DIR__.'/../../config.php');
global $CFG;
require_once(__DIR__.'/lib.php');
require_once(__DIR__.'/classes/forms/add_vocab_form.php');
require_once(__DIR__.'/classes/vocab_manager.php');
include_once(__DIR__.'/classes/activity_tracker.php');

// Course module id.
$id = required_param('id', PARAM_INT);
$mode = required_param('mode', PARAM_TEXT);
if ($mode === 'edit') {
    $editlistid = required_param('listid', PARAM_INT);
}

$cm = get_coursemodule_from_id('vocabcoach', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$moduleinstance = $DB->get_record('vocabcoach', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);
$modulecontext = context_module::instance($cm->id);

if ($mode === 'edit') {
    $vocabmanager = new vocab_manager($USER->id);
    $has_edit_superpower = has_capability('mod/vocabcoach:delete_lists', $modulecontext);
    $canedit = $has_edit_superpower || $vocabmanager->user_owns_list($USER->id, $editlistid);
    if (!$canedit) {
        redirect(new moodle_url('/mod/vocabcoach/view.php', ['id' => $cm->id]), get_string('edit_list_not_allowed', 'mod_vocabcoach'),
            notification::ERROR);
    }
}

$PAGE->set_url(new moodle_url('/mod/vocabcoach/add_vocab.php', ['id'=>$cm->id]));
$PAGE->set_context($modulecontext);
$PAGE->set_title(get_string('add_vocab_title', 'mod_vocabcoach'));
$PAGE->set_heading(get_string('add_vocab_title', 'mod_vocabcoach'));
$PAGE->requires->js_call_amd('mod_vocabcoach/add_vocab', 'init', [$editlistid ?? -1]);
$PAGE->requires->css('/mod/vocabcoach/styles/spinner.css');

$instance_info = $DB->get_record('vocabcoach', ['id'=>$cm->instance], '*');

$form_parameters = [
        'mode' => $mode,
        'id' => $id,
        'year' => $moduleinstance->year,
        'third_active' => $instance_info->thirdactive
];

if ($mode === 'edit') {
    $form_parameters['listid'] = $editlistid;
    $mform = new add_vocab_form(null, $form_parameters);
    $listinfo = $DB->get_record('vocabcoach_lists', ['id'=>$editlistid],
        'title AS list_title, 
        book AS list_book, 
        unit AS list_unit, 
        year AS list_year,
        private AS list_private');
    $mform->set_data($listinfo);
} else {
    $mform = new add_vocab_form(null, $form_parameters);
}

if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot.'/mod/vocabcoach/view.php?id='.$cm->id);
} else if ($formdata = $mform->get_data()) {
    global $USER;
    $userid = $USER->id;
    $redirect = true;
    $vocabmanager = new vocab_manager($userid);

    // Step 0a: construct $vocab_array directly from $_POST - this is a dirty hack, but all I can think of right now.
    $vocabarray = array();
    for ($i=0; $i<count($_POST['front']); $i++) {
        if ($_POST['front'][$i] === '' && $_POST['back'][$i] === '' ) {
            continue;
        }
        $vocab = new stdClass();
        $vocab->id = $_POST['vocabid'][$i] ?? '0';
        $vocab->correct_everywhere = false;
        $vocab->front = trim($_POST['front'][$i]);
        $vocab->back = trim($_POST['back'][$i]);
        $vocab->third = trim($_POST['third'][$i]);
        $vocabarray[] = $vocab;
    }

    // If mode is user, don't bother about lists
    if ($mode === 'user') {
        foreach ($vocabarray as $vocab) {
            $vocabid = $vocabmanager->insert_vocab($vocab);
            if (!$vocabmanager->add_vocab_to_user($vocabid, $id)) {
                notification::add('Fehler beim Hinzufügen der Vokabeln zu deinem Kasten. ', notification::ERROR);
            }
        }
        $at = new activity_tracker($USER->id, $id);
        $at->log($at->types_always['ACT_ENTERED_VOCAB'], count($vocabarray));
        redirect(new moodle_url('/mod/vocabcoach/view.php', ['id' => $cm->id]), get_string('add_vocab_successful', 'mod_vocabcoach'));
    }

    // Step 0b: Gather list information
    $listkeys = ['title', 'book', 'unit', 'year', 'private'];
    $listinfo = ['createdby'=> $userid, 'cmid' => $cm->id];
    foreach ($listkeys as $key) {
        $listinfo[$key] = $formdata->{'list_' . $key};
    }

    if ($mode === 'edit') {
        $listinfo['id'] = $editlistid;
        $DB->update_record('vocabcoach_lists', (object) $listinfo);
        $vocabmanager->edit_list($editlistid, $vocabarray);
        redirect(new moodle_url('/mod/vocabcoach/view.php', ['id' => $cm->id]), get_string('edit_vocab_successful', 'mod_vocabcoach'));
    }

    // Step 1: Generate List

    $listid = $vocabmanager->add_list($listinfo);
    if ($listid == -1) {
        notification::add('Fehler beim Anlegen der Liste. ', notification::ERROR);
        $redirect = false;
    }
    $listinfo['id'] = $listid;

    // Step 2: Add all the vocabulary, find their ID and link them to the list
    foreach ($vocabarray as $vocab) {
        $vocabid = $vocabmanager->insert_vocab($vocab);
        if (!$vocabmanager->add_vocab_to_list($vocabid, $listid)) {
            notification::add('Fehler beim Eintragen der Vokabeln in die Liste. ', notification::ERROR);
            $redirect = false;
        }
        $at = new activity_tracker($USER->id, $id);
        $at->log($at->types_always['ACT_CREATED_LIST'], $listid);
    }

    // Step 3: add list to user (if necessary)
    if (isset($formdata->add_to_user_database) && $formdata->add_to_user_database == 1) {
        if (!$vocabmanager->add_list_to_user_database($listid, $id)) {
            notification::add('Fehler beim Eintragen der Vokabeln. ', notification::ERROR);
            notification::add('Fehler beim Hinzufügen der Vokabeln zu deinem Kasten. ', notification::ERROR);
            $redirect = false;
        }
    }

    // Step 4: If selected, distribute the list to all users
    if (isset($formdata->list_distribute_now) && $formdata->list_distribute_now == 1) {
        $listsAPI = new \mod_vocabcoach\external\manage_lists_api();
        $listsAPI->distribute_list($listid, $id);
    }

    if ($redirect) {
        redirect(new moodle_url('/mod/vocabcoach/view.php', ['id' => $cm->id]), get_string('add_vocab_successful', 'mod_vocabcoach'));
    }
}


echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
