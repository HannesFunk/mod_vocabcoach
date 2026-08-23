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

require(__DIR__ . '/../../config.php');

$cmid = required_param('id', PARAM_INT);
$stage = required_param('stage', PARAM_INT);

$cm = get_coursemodule_from_id('vocabcoach', $cmid, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$moduleinstance = $DB->get_record('vocabcoach', ['id' => $cm->instance], '*', MUST_EXIST);

require_login($course, true, $cm);
$modulecontext = context_module::instance($cm->id);

$PAGE->set_context($modulecontext);
$PAGE->set_url('/mod/vocabcoach/viewbox.php', ['id' => $cmid, 'stage' => $stage]);
$PAGE->set_title(get_string('list_pagetitle', 'mod_vocabcoach'));
$PAGE->set_heading(get_string('list_pagetitle', 'mod_vocabcoach'));
$PAGE->navbar->add(get_string('view_box_title', 'mod_vocabcoach', $stage));
$PAGE->requires->css('/mod/vocabcoach/styles/check.css');
$PAGE->requires->js_call_amd('mod_vocabcoach/viewbox', 'init');

$checkapi = new \mod_vocabcoach\external\vocab_api();
$vocabarray = $checkapi->get_user_vocabs($cmid, $stage, true);

$templatecontext = [
    'fronttitle' => $moduleinstance->desc_front,
    'backtitle' => $moduleinstance->desc_back,
    'vocabs' => $vocabarray,
];

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('view_box_title', 'mod_vocabcoach', $stage));
echo $OUTPUT->render_from_template('mod_vocabcoach/viewbox', (object)$templatecontext);
echo $OUTPUT->footer();
