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

require(__DIR__.'/../../config.php');
global $CFG;
require_once(__DIR__.'/lib.php');
require_once(__DIR__.'/classes/forms/add_vocab_form.php');
require_once(__DIR__.'/classes/vocab_manager.php');


// Course module id.
$id = required_param('id', PARAM_INT);
$mode = required_param('mode', PARAM_TEXT);
if ($mode === 'edit') {
    $editlistid = required_param('listid', PARAM_INT);
}

if ($id) {
    $cm = get_coursemodule_from_id('vocabcoach', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('vocabcoach', array('id' => $cm->instance), '*', MUST_EXIST);
}

require_login($course, true, $cm);
$modulecontext = context_module::instance($cm->id);

$PAGE->set_url(new moodle_url('/mod/vocabcoach/add_vocab.php', ['id'=>$cm->id]));
$PAGE->set_context($modulecontext);
$PAGE->set_title(get_string('add_vocab_title', 'mod_vocabcoach'));
$PAGE->set_heading(get_string('add_vocab_title', 'mod_vocabcoach'));
$PAGE->requires->js_call_amd('mod_vocabcoach/add_vocab', 'init', [$editlistid ?? -1]);
$PAGE->requires->css('/mod/vocabcoach/styles/spinner.css');

if ($mode === 'edit') {
    $listapi = new \mod_vocabcoach\external\check_vocab_api();
    $old_vocab_array = $listapi->get_list_vocabs($editlistid);
    $mform = new add_vocab_form(null, ['mode'=>$mode, 'old'=>$old_vocab_array, 'listid'=>$editlistid, 'year'=>$moduleinstance->year]);
    $listinfo = $DB->get_record('mod_vocabcoach_lists', ['id'=>$editlistid],
        'title AS list_title, 
        book AS list_book, 
        unit AS list_unit, 
        year AS list_year');
    $mform->set_data($listinfo);
} else {
    $mform = new add_vocab_form(null, ['mode' => $mode, 'old' => null, 'year'=>$moduleinstance->year]);
}

if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot.'/mod/vocabcoach/view.php?id='.$cm->id, get_string('cancelled_form', 'mod_vocabcoach'));;
} else if ($formdata = $mform->get_data()) {
    global $USER;
    $userid = $USER->id;
    $redirect = true;
    $vocabmanager = new \mod_vocabcoach\vocab_manager($userid);

    // Step 0: construct $vocab_array directly from $_POST - this is a dirty hack, but all I can think of right now.
    $vocabarray = array();
    for ($i=0; $i<count($_POST['front']); $i++) {
        if ($_POST['front'][$i] === '' || $_POST['back'][$i] === '' ) {
            continue;
        }
        $vocab = new stdClass();
        $vocab->id = $_POST['vocabid'][$i] ?? '0';
        $vocab->correct_everywhere = false;
        $vocab->front = $_POST['front'][$i];
        $vocab->back = $_POST['back'][$i];
        $vocabarray[] = $vocab;
    }

    // If mode is user, don't bother about lists
    if ($mode === 'user') {
        foreach ($vocabarray as $vocab) {
            $vocabid = $vocabmanager->insert_vocab($vocab, $userid);
            if (!$vocabmanager->add_vocab_to_user($vocabid, $userid, $id)) {
                \core\notification::add('Fehler beim Hinzufügen der Vokabeln zu deinem Kasten. ', \core\notification::ERROR);
            }
        }
        redirect(new moodle_url('/mod/vocabcoach/view.php', ['id' => $cm->id]), get_string('add_vocab_successful', 'mod_vocabcoach'));
    }

    if ($mode === 'edit') {
        $vocabmanager->edit_list($editlistid, $vocabarray);
        redirect(new moodle_url('/mod/vocabcoach/view.php', ['id' => $cm->id]), get_string('edit_vocab_successful', 'mod_vocabcoach'));
    }

    // Step 1: Generate List
    $listkeys = ['title', 'book', 'unit', 'year'];
    $listinfo = ['createdby' => $userid, 'cmid' => $cm->id];
    foreach ($listkeys as $key) {
        $listinfo[$key] = $formdata->{'list_' . $key};
    }
    $listid = $vocabmanager->add_list($listinfo);
    if ($listid ==  -1) {
        \core\notification::add('Fehler beim Anlegen der Liste. ', \core\notification::ERROR);
        $redirect = false;
    }

    // Step 2: Add all the vocabulary, find their ID and link them to the list
    foreach ($vocabarray as $vocab) {
        $vocabid = $vocabmanager->insert_vocab($vocab, $userid);
        if (!$vocabmanager->add_vocab_to_list($vocabid, $listid)) {
            \core\notification::add('Fehler beim Eintragen der Vokabeln in die Liste. ', \core\notification::ERROR);
            $redirect = false;
        }
    }

    // Step 3: add list to user (if necessary)
    if (isset($formdata->add_to_user_database) && $formdata->add_to_user_database == 1) {
        if (!$vocabmanager->add_list_to_user_database($listid, $userid, $id)) {
            \core\notification::add('Fehler beim Eintragen der Vokabeln. ', \core\notification::ERROR);\core\notification::add('Fehler beim Hinzufügen der Vokabeln zu deinem Kasten. ', \core\notification::ERROR);
            $redirect = false;
        }
    }

    if ($redirect) {
        redirect(new moodle_url('/mod/vocabcoach/view.php', ['id' => $cm->id]), get_string('add_vocab_successful', 'mod_vocabcoach'));
    }
}


echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
