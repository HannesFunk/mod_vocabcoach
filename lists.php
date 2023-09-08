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

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

$id = required_param('id', PARAM_INT);

$cm = get_coursemodule_from_id('vocabcoach', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$moduleinstance = $DB->get_record('vocabcoach', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);
$modulecontext = context_module::instance($cm->id);

$canedit = has_capability('mod/vocabcoach:delete_lists', $modulecontext);

$PAGE->set_context($modulecontext);
$PAGE->set_url('/mod/vocabcoach/lists.php', ['id' => $id]);
$PAGE->set_title('Vokabelcoach - Vokabellisten');
$PAGE->set_heading('Vokabelcoach - Vokabellisten');

$PAGE->requires->js_call_amd('mod_vocabcoach/lists', 'init', [$id, $USER->id, $canedit]);
$PAGE->requires->css('/mod/vocabcoach/styles/spinner.css');
$PAGE->requires->css('/mod/vocabcoach/styles/style.css');

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('mod_vocabcoach/lists', ['loading' => true]);
echo $OUTPUT->footer();
