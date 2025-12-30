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
 * Plugin strings are defined here.
 *
 * @package     mod_vocabcoach
 * @category    string
 * @copyright   2023 J. Funk, johannesfunk@outlook.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Vocab Coach';
$string['plugin_name'] = 'Vocab Coach';
$string['modulename'] = 'Vocabulary box';
$string['modulenameplural'] = 'Vocabulary boxes';
$string['pluginadministration'] = 'Settings';


$string['vocabcoachsettings'] = 'Settings';
$string['vocabcoachname'] = 'Vocabulary box';
$string['vocabcoachname_help'] = 'Help';
$string['vocabcoachnameplural'] = 'Vocabulary box';

$string['boxtimes'] = 'Revision intervals';
$string['info_boxtimes'] = 'Set the intervals after which words in the different boxes should be reviewed and become "due" again.';
$string['boxtime'] = 'Interval for box';
$string['move_undue'] = 'Move words that are reviewed before the interval has elapsed to the next box.';
$string['move_undue_help'] = 'Words can be reviewed at any time. If this box is ticked, words reviewed before the interval has elapsed will also be moved to the next box. Otherwise, they will remain in the current box.';
$string['third_active'] = 'Enable third column for additional information.';

$string['add_vocab_title'] = 'Add vocabulary';
$string['add_vocab_user'] = 'Add words only for myself';
$string['add_vocab_list'] = 'Add a list for the course';
$string['add_vocab'] = 'Add words';
$string['show_lists'] = 'Show all lists';
$string['front'] = 'Front';
$string['back'] = 'Back';
$string['cancelled_form'] = 'Entry cancelled.';
$string['add_vocab_successful'] = 'Vocabulary added.';
$string['edit_vocab_successful'] = 'List updated.';
$string['add_vocab_info_lines'] = 'Additional rows will be added automatically.';
$string['add_vocab_add_to_user_database'] = 'Add immediately to your own box.';
$string['edit_vocab_instructions'] = 'To delete a vocabulary item, leave both sides empty.';
$string['edit_list_not_allowed'] = 'You do not have permission to edit this list.';
$string['add_vocab_add_to_user_database_help'] = 'If you do not tick this box, only the list will be created and the words will not be added to your box yet. You can add them later at any time.';

$string['vocab'] = 'Word';
$string['vocabplural'] = 'Words';
$string['list'] = 'List';
$string['listplural'] = 'Lists';
$string['listprops'] = 'List properties';
$string['instructions'] = 'Instructions for typing vocabulary';
$string['instructions_short'] = 'Instructions';
$string['instructions_help'] = 'Insert instructions that students see on the page they type vocabulary (for example, whether standard abbreviations should be used).';
$string['instructions_default'] = '<div class="pl-5 pr-3"><p>Pay attention to the following instructions to make sure words added by different users all have the same style.</p></div>
    ';
$string['list_private'] = 'Private list';
$string['list_private_help'] = 'If you tick this box, only you can see this list. Otherwise, other participants in the course can see the list, but not edit it.';
$string['list_distribute_now'] = 'Distribute to everyone';
$string['list_distribute_now_help'] = 'If you tick this box, the words will be added immediately to all other students in this course. Please tick for vocab duties (in-class or homework).';

$string['type_vocab_label'] = 'Type vocabulary';
$string['mode'] = 'Practice mode';

// Lists page.
$string['lists'] = 'Vocabulary lists';
$string['intro_lists'] = 'Here you can see all public vocabulary lists created by other students in this course. You can either study directly from these lists or copy the entire list into your own box.';
$string['lists_onlyown'] = 'Show only mine';
$string['lists_empty'] = 'No vocabulary list found.';

$string['name'] = 'Name';
$string['year_short'] = 'Yr.';
$string['year'] = 'Year';
$string['book'] = 'Book';
$string['unit'] = 'Unit';
$string['count'] = 'Number of words';
$string['creator'] = 'Created by';
$string['actions'] = 'Actions';

// Action menu (lists_action_menu.mustache).
$string['actions_show_actions'] = 'Show actions';
$string['actions_start_check'] = 'Start check';
$string['actions_add_to_my_box'] = 'Add to my box';
$string['actions_export_pdf'] = 'Export as PDF';
$string['actions_export_csv'] = 'Export as CSV';
$string['actions_edit'] = 'Edit';
$string['actions_delete'] = 'Delete';
$string['actions_distribute_to_course'] = 'Distribute to course';

// Task / notification strings
$string['task_sendduenotifications'] = 'Send due vocab notifications';
$string['due_notification_subject'] = '{$a} vocab items due';
$string['due_notification_small'] = '{$a} vocab due';
$string['due_notification_body'] = 'You have {$a->count} vocabulary items due. Open the activity: {$a->url}';
$string['notify_students'] = 'Send due-vocab notifications to students';
$string['notify_students_help'] = 'If enabled, the scheduled task will send email notifications to students when they have due vocabulary items for this activity instance.';
