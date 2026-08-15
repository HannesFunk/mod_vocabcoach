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

$string['actions'] = 'Actions';
$string['actions_add_to_my_box'] = 'Add to my box';
$string['actions_delete'] = 'Delete';
$string['actions_distribute_to_course'] = 'Distribute to course';
$string['actions_edit'] = 'Edit';
$string['actions_export_csv'] = 'Export as CSV';
$string['actions_export_pdf'] = 'Export as PDF';
$string['actions_show_actions'] = 'Show actions';
$string['actions_start_check'] = 'Start check';
$string['add_to_own_box'] = 'Add to my own vocabulary box';
$string['add_vocab'] = 'Add words';
$string['add_vocab_add_to_user_database'] = 'Add immediately to your own box.';
$string['add_vocab_add_to_user_database_help'] = 'If you do not tick this box, only the list will be created and the words will not be added to your box yet. You can add them later at any time.';
$string['add_vocab_info_lines'] = 'Additional rows will be added automatically.';
$string['add_vocab_list'] = 'Add a list for the course';
$string['add_vocab_successful'] = 'Vocabulary added.';
$string['add_vocab_title'] = 'Add vocabulary';
$string['add_vocab_user'] = 'Add words only for myself';
$string['back'] = 'Back';
$string['book'] = 'Book';
$string['box'] = 'box';
$string['boxtime'] = 'Interval for box';
$string['boxtimes'] = 'Revision intervals';
$string['cancelled_form'] = 'Entry cancelled.';
$string['check'] = 'Check';
$string['check_button_no'] = "Didn't know that!";
$string['check_button_override'] = 'I knew that!';
$string['check_button_reveal'] = 'Reveal';
$string['check_button_unknown'] = "Didn't know it. Next";
$string['check_button_verify'] = 'Check';
$string['check_button_yes'] = 'Got it!';
$string['check_end_instruction'] = 'Click into the field to end the check.';
$string['check_instructions'] = "Remember the translation, then check your solution by clicking or tipping into the empty box.";
$string['check_pagetitle'] = 'Vocab Coach - Check';
$string['check_remaining'] = '{$a} words remaining';
$string['check_remaining_one'] = '1 word remaining';
$string['check_result_excellent'] = 'Very well done!';
$string['check_result_good'] = 'Good job!';
$string['check_result_ok'] = 'Okay.';
$string['check_result_poor'] = 'Hm. There is room for improvement!';
$string['check_result_solid'] = 'Solid!';
$string['check_summary_result'] = '{$a->known} of {$a->total} correct!';
$string['checkmode'] = 'Check mode';
$string['checkmode_back'] = 'Ask for back';
$string['checkmode_front'] = 'Ask for front';
$string['checkmode_random'] = 'Random';
$string['checkmode_type'] = 'Type word on the front';
$string['confirm_delete_vocab'] = 'Should this vocab really be deleted from your box?';
$string['count'] = 'Number of words';
$string['creator'] = 'Created by';
$string['csv_filename'] = 'vocabulary.csv';
$string['desc_back_default'] = 'Translation';
$string['due_notification_body'] = 'You have {$a->count} vocabulary items due. Open the activity: {$a->url}';
$string['due_notification_small'] = '{$a} vocab due';
$string['due_notification_subject'] = '{$a} vocab items due';
$string['duetime_day'] = '{$a} day';
$string['duetime_days'] = '{$a} days';
$string['duetime_hour'] = '{$a} hour';
$string['duetime_hours'] = '{$a} hours';
$string['duetime_now'] = 'now';
$string['edit_list_not_allowed'] = 'You do not have permission to edit this list.';
$string['edit_vocab_instructions'] = 'To delete a vocabulary item, leave both sides empty.';
$string['edit_vocab_successful'] = 'List updated.';
$string['edit_vocab_title'] = 'Edit word';
$string['email_notifications'] = 'Email notifications';
$string['email_notifications_help'] = 'Receive email notifications when vocabulary is due for review.';
$string['error_add_vocab_to_list'] = 'Error adding the words to the list.';
$string['error_add_vocab_to_user'] = 'Error adding the words to your box.';
$string['error_create_list'] = 'Error creating the list.';
$string['error_csv_output'] = 'Cannot open the CSV output.';
$string['error_edit_vocab'] = 'Error editing the word. Please try again later.';
$string['error_wrong_parameters'] = 'Wrong parameters.';
$string['front'] = 'Front';
$string['info_boxtimes'] = 'Set the intervals after which words in the different boxes should be reviewed and become "due" again.';
$string['instructions'] = 'Instructions for typing vocabulary';
$string['instructions_default'] = '<div class="pl-5 pr-3"><p>Pay attention to the following instructions to make sure words added by different users all have the same style.</p></div>
    ';
$string['instructions_help'] = 'Insert instructions that students see on the page they type vocabulary (for example, whether standard abbreviations should be used).';
$string['instructions_short'] = 'Instructions';
$string['intro_lists'] = 'Here you can see all public vocabulary lists created by other students in this course. You can either study directly from these lists or copy the entire list into your own box.';
$string['leaderboard'] = 'Leaderboard';
$string['leaderboard_due'] = 'Due words';
$string['leaderboard_rank'] = 'Rank';
$string['list'] = 'List';
$string['list_book_default'] = 'Access';
$string['list_distribute_now'] = 'Distribute to everyone';
$string['list_distribute_now_help'] = 'If you tick this box, the words will be added immediately to all other students in this course. Please tick for vocab duties (in-class or homework).';
$string['list_pagetitle'] = 'Vocab Coach - List';
$string['list_private'] = 'Private list';
$string['list_private_help'] = 'If you tick this box, only you can see this list. Otherwise, other participants in the course can see the list, but not edit it.';
$string['list_title'] = 'List name';
$string['listplural'] = 'Lists';
$string['listprops'] = 'List properties';
$string['lists'] = 'Vocabulary lists';
$string['lists_added_to_box'] = 'New words from this list have been added to your box.';
$string['lists_confirm_delete'] = 'Should this list really be deleted?';
$string['lists_confirm_distribute'] = 'Should this list really be distributed to all participants in this course?';
$string['lists_deleted'] = 'List deleted.';
$string['lists_distributed'] = 'List distributed to all participants in this course.';
$string['lists_empty'] = 'No vocabulary list found.';
$string['lists_onlyown'] = 'Show only mine';
$string['lists_pagetitle'] = 'Vocab Coach - Vocabulary lists';
$string['loading'] = "Loading...";
$string['modulename'] = 'Vocabulary box';
$string['modulenameplural'] = 'Vocabulary boxes';
$string['move_undue'] = 'Move words that are reviewed before the interval has elapsed to the next box.';
$string['move_undue_help'] = 'Words can be reviewed at any time. If this box is ticked, words reviewed before the interval has elapsed will also be moved to the next box. Otherwise, they will remain in the current box.';
$string['name'] = 'Name';
$string['no_vocabs_to_check'] = 'No vocab here.';
$string['notification_userprefs_updated'] = 'Userpreferences updated successfully.';
$string['notifications_enabled'] = 'Enable e-mail notifications.';
$string['notifications_enabled_help'] = 'If enabled, the scheduled task will send email notifications to students when they have due vocabulary items for this activity instance.';
$string['notifications_optout'] = 'Send notifications by default (participants can still opt-out).';
$string['notifications_optout_help'] = 'Otherwise, participants have to opt-in to notifications.';
$string['pdf_created_for'] = 'Created for {$a->name} on {$a->date}';
$string['pdf_filename'] = 'vocabulary-list.pdf';
$string['pdf_title_box'] = 'Vocabulary list (box {$a})';
$string['plugin_name'] = 'Vocab Coach';
$string['pluginadministration'] = 'Settings';
$string['pluginname'] = 'Vocab Coach';
$string['remove_from_box'] = "Remove from my box";
$string['restores_used'] = 'Restores used: {$a->used}/{$a->max}';
$string['settings'] = 'Settings';
$string['show_lists'] = 'Show all lists';
$string['streak_checkall'] = 'Check-all streak';
$string['streak_login'] = 'Login streak';
$string['streak_restore_button'] = 'Restore Streak';
$string['streak_restore_description'] = 'Restore your broken streak';
$string['streak_restore_failed'] = 'Failed to restore streak. Please try again.';
$string['streak_restore_limit'] = 'You can restore your streak {$a} times per month.';
$string['streak_restore_success'] = 'Your streak has been restored!';
$string['streak_restore_title'] = 'Streak Restore';
$string['streak_restores_limit_reached'] = 'You have reached the monthly limit of streak restores. Try again next month!';
$string['streak_restores_remaining'] = 'Restores remaining this month: {$a}';
$string['task_sendduenotifications'] = 'Send due vocab notifications';
$string['unit'] = 'Unit';
$string['update_vocab_success'] = 'Update successful.';
$string['view_actions'] = 'Actions';
$string['view_activity'] = 'Activity';
$string['view_box_all_done'] = 'You have already learned all words in this box. The next words are due in {$a}.';
$string['view_box_due_first'] = 'Revise the words that are currently due in this box first. Only then can you check the others as well.';
$string['view_box_empty'] = 'This box currently contains no words.';
$string['view_box_title'] = 'Box {$a}';
$string['view_check_undue'] = 'Check not yet due';
$string['view_checkall_streak'] = 'Reviewed all vocabulary consistently for {$a} days.';
$string['view_class_total'] = 'Total number of unrevised vocabulary in class';
$string['view_export_pdf'] = 'Export as PDF';
$string['view_live_update'] = 'Update live.';
$string['view_logged_in_streak'] = 'Logged in consistently for {$a} days.';
$string['view_remove_incorrect'] = 'Remove incorrect';
$string['view_show_actions'] = 'Show actions';
$string['view_your_vocab_box'] = 'Your vocabulary box';
$string['viewlist_heading'] = 'Vocabulary list {$a}';
$string['vocab'] = 'Word';
$string['vocabcoach:addinstance'] = 'Add a new Vocab Coach activity';
$string['vocabcoach:delete_lists'] = 'Delete vocabulary lists';
$string['vocabcoach:distribute_lists'] = 'Distribute vocabulary lists to course';
$string['vocabcoach:mod/vocabcoach:show_class_total_live'] = 'Show live updates of the number of total due vocab.';
$string['vocabcoach:show_class_total'] = 'View total unrevised vocabulary in class';
$string['vocabcoach:show_leaderboard'] = 'View leaderboard';
$string['vocabcoach:view'] = 'View Vocab Coach activity';
$string['vocabcoachname'] = 'Vocabulary box';
$string['vocabcoachname_help'] = 'Help';
$string['vocabcoachnameplural'] = 'Vocabulary box';
$string['vocabcoachsettings'] = 'Settings';
$string['vocablist'] = 'Vocabulary list';
$string['vocabplural'] = 'Words';
$string['year'] = 'Year';
$string['year_short'] = 'Yr.';
