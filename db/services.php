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
 * Defines services for AJAX use.
 *
 * @package   mod_vocabcoach
 * @copyright 2023 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Johannes Funk
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'mod_vocabcoach_update_vocab' => [
        'classname' => 'mod_vocabcoach\external\vocab_api',
        'methodname' => 'update_vocab',
        'description' => 'Updates vocabulary and moves them to stage 1 or the next stage',
        'type' => 'write',
        'ajax' => true,
    ],
    'mod_vocabcoach_get_vocabs' => [
        'classname' => 'mod_vocabcoach\external\vocab_api',
        'methodname' => 'get_vocabs',
        'description' => 'Updates vocabulary and moves them to stage 1 or the next stage',
        'type' => 'write',
        'ajax' => true,
    ], 'mod_vocabcoach_get_lists' => [
        'classname' => 'mod_vocabcoach\external\lists_api',
        'methodname' => 'get_lists',
        'description' => 'Return all the lists meeting certain (or no) search criteria.',
        'type' => 'write',
        'ajax' => true,
    ],  'mod_vocabcoach_get_user_vocabs' => [
        'classname' => 'mod_vocabcoach\external\vocab_api',
        'methodname' => 'get_user_vocabs',
        'description' => 'Retrieves all the vocabulary for a given user and stage.',
        'type' => 'write',
        'ajax' => true,
    ],  'mod_vocabcoach_get_list_vocabs' => [
        'classname' => 'mod_vocabcoach\external\vocab_api',
        'methodname' => 'get_list_vocabs',
        'description' => 'Retrieves vocabulary from a list with given listid.',
        'type' => 'write',
        'ajax' => true,
    ], 'mod_vocabcoach_delete_list' => [
        'classname' => 'mod_vocabcoach\external\lists_api',
        'methodname' => 'delete_list',
        'description' => 'Remove a list with certain id.',
        'type' => 'write',
        'ajax' => true,
    ],
    'mod_vocabcoach_add_list_to_user' => [
        'classname' => 'mod_vocabcoach\external\lists_api',
        'methodname' => 'add_list_to_user',
        'description' => 'Add all the new vocab from one list to the user db.',
        'type' => 'write',
        'ajax' => true,
    ],
    'mod_vocabcoach_distribute_list' => [
        'classname' => 'mod_vocabcoach\external\lists_api',
        'methodname' => 'distribute_list',
        'description' => 'Add all the new vocab from one list to dbs owned by any student of the course.',
        'type' => 'write',
        'ajax' => true,
    ],
    'mod_vocabcoach_get_feedback_line' => [
        'classname' => 'mod_vocabcoach\external\feedback_api',
        'methodname' => 'get_feedback_line',
        'description' => 'Gets a feedback line, depending on the achievement.',
        'type' => 'write',
        'ajax' => true,
    ],
    'mod_vocabcoach_log_checked_vocabs' => [
        'classname' => 'mod_vocabcoach\external\vocab_api',
        'methodname' => 'log_checked_vocabs',
        'description' => 'Logs the number and type of checked vocabs.',
        'type' => 'write',
        'ajax' => true,
    ],
    'mod_vocabcoach_remove_vocab_from_user' => [
        'classname' => 'mod_vocabcoach\external\vocab_api',
        'methodname' => 'remove_vocab_from_user',
        'description' => 'Removes the vocabitem with given id from the box of a user.',
        'type' => 'write',
        'ajax' => true,
    ],
    'mod_vocabcoach_get_class_total' => [
        'classname' => 'mod_vocabcoach\external\vocab_api',
        'methodname' => 'get_class_total',
        'description' => 'Count the total of vocabs due across all users in a course.',
        'type' => 'write',
        'ajax' => true,
    ],
    'mod_vocabcoach_set_checkmode' => [
        'classname' => 'mod_vocabcoach\external\checkprefs_api',
        'methodname' => 'set_mode',
        'description' => 'Set the user check mode preference for this activity',
        'type' => 'write',
        'ajax' => true,
    ],
    'mod_vocabcoach_get_checkmode' => [
        'classname' => 'mod_vocabcoach\external\checkprefs_api',
        'methodname' => 'get_mode',
        'description' => 'Get the user check mode preference for this activity',
        'type' => 'read',
        'ajax' => true,
    ],
];
