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
require_once(__DIR__.'/lib.php');
require_login();

global $USER;
$id = required_param('id', PARAM_INT);

$PAGE->set_context(\context_system::instance());
$PAGE->set_url('/mod/vocabcoach/lists.php');
$PAGE->set_title('Vokabelcoach - Vokabellisten');
$PAGE->set_heading('Vokabelcoach - Vokabellisten');

$PAGE->requires->js_call_amd('mod_vocabcoach/lists', 'init', [$id]);
$PAGE->requires->css('/mod/vocabcoach/styles/spinner.css');

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('mod_vocabcoach/lists', ['loading' => true]);
echo $OUTPUT->footer();
