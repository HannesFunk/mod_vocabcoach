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
use mod_vocabcoach\vocab_manager;
use mod_vocabcoach\form\add_vocab_form;

require(__DIR__ . '/../../config.php');

$cmid = required_param('id', PARAM_INT);
$listid = optional_param('listid', null,PARAM_INT);

$cm = get_coursemodule_from_id('vocabcoach', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$moduleinstance = $DB->get_record('vocabcoach', ['id' => $cm->instance], '*', MUST_EXIST);

require_login($course, true, $cm);
$modulecontext = context_module::instance($cm->id);

$PAGE->set_url(new moodle_url('/mod/vocabcoach/add_vocab.php', ['id' => $cm->id]));
$PAGE->set_context($modulecontext);
$PAGE->set_title(get_string('add_vocab_title', 'mod_vocabcoach'));
$PAGE->set_heading(get_string('add_vocab_title', 'mod_vocabcoach'));

if ($listid) {
    $lm = new \mod_vocabcoach\listmanager($listid, $cmid);
    $vocabarray = $lm->get_vocabs();
} else {
    $vocabarray = [];
}

$context = [
    'vocabs' => $vocabarray,
    'cmid' => $cmid,
    'listid' => $listid,
    'placeholders' => [
        'front' => $moduleinstance->desc_front,
        'back' => $moduleinstance->desc_back,
    ],
    'instructions' => format_text($moduleinstance->instructions, FORMAT_HTML, ['context' => $modulecontext]),
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('mod_vocabcoach/addvocab', ['propsjson' => json_encode($context)]);
echo $OUTPUT->footer();
