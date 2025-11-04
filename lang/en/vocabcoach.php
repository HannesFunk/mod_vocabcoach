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

// Mobile app strings.
$string['check_vocab'] = 'Check vocabulary';
$string['box'] = 'Box';
$string['due'] = 'Due';
$string['total'] = 'Total';
$string['start_check'] = 'Start check';
$string['no_vocab_due'] = 'No vocabulary due in this box';
$string['check_complete'] = 'Check complete';
$string['correct'] = 'Correct';
$string['incorrect'] = 'Incorrect';
$string['known'] = 'Known';
$string['unknown'] = 'Unknown';
$string['next_vocab'] = 'Next vocabulary';
$string['finish_check'] = 'Finish check';
$string['next_due'] = 'Next due';
$string['no_vocab_available'] = 'No vocabulary available';
$string['days_logged_in'] = 'Days logged in';
$string['days_checked_all'] = 'Days all checked';
$string['check_mode_buttons'] = 'Button mode';
$string['check_mode_type'] = 'Type mode';
$string['type_answer'] = 'Type your answer';
$string['check_answer'] = 'Check answer';
$string['show_answer'] = 'Show answer';
$string['back_to_boxes'] = 'Back to boxes';

// Additional mobile app strings expected by mobile configuration
$string['mobile_main_title'] = 'Vocabulary Boxes';
$string['mobile_box_due'] = 'Due: {$a}';
$string['mobile_check_start'] = 'Start Learning';
$string['mobile_no_due_vocab'] = 'No vocabulary due for review';
$string['mobile_progress_text'] = '{$a->current} of {$a->total}';
$string['mobile_check_title'] = 'Vocabulary Check';
$string['mobile_check_mode_buttons'] = 'Button Mode';
$string['mobile_check_mode_type'] = 'Type Mode';
$string['mobile_check_show_back'] = 'Show Answer';
$string['mobile_check_known'] = 'Known';
$string['mobile_check_unknown'] = 'Unknown';
$string['mobile_check_type_answer'] = 'Type your answer';
$string['mobile_check_answer'] = 'Answer';
$string['mobile_summary_title'] = 'Summary';
$string['mobile_summary_known'] = 'Known: {$a}';
$string['mobile_summary_unknown'] = 'Unknown: {$a}';
$string['mobile_finish_check'] = 'Finish Check';
