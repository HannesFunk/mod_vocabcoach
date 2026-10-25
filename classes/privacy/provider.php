<?php
// This file is part of Moodle - http://moodle.org
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Privacy Subsystem implementation for mod_vocabcoach.
 *
 * @package    mod_vocabcoach
 * @category   privacy
 * @copyright  2026 Johannes Funk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_vocabcoach\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;

class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider,
    \core_privacy\local\request\core_userlist_provider {
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
            'vocabcoach_vocabdata',
            [
                'userid'  => 'privacy:metadata:vocabcoach_vocabdata:userid',
                'vocabid'  => 'privacy:metadata:vocabcoach_vocabdata:vocabid',
                'stage'  => 'privacy:metadata:vocabcoach_vocabdata:stage',
                'lastchecked'  => 'privacy:metadata:vocabcoach_vocabdata:lastchecked',
            ],
            'privacy:metadata:vocabcoach_vocabdata'
        );

        $collection->add_database_table(
            'vocabcoach_userprefs',
            [
                'userid' => 'privacy:metadata:vocabcoach_userprefs:userid',
                'mode' => 'privacy:metadata:vocabcoach_userprefs:mode',
                'email_notifications' => 'privacy:metadata:vocabcoach_userprefs:email_notifications',
                'timemodified' => 'privacy:metadata:vocabcoach_userprefs:timemodified',
            ],
            'privacy:metadata:vocabcoach_userprefs'
        );

        $collection->add_database_table(
            'vocabcoach_streaks',
            [
                'userid' => 'privacy:metadata:vocabcoach_streaks:userid',
                'type' => 'privacy:metadata:vocabcoach_streaks:type',
                'streak' => 'privacy:metadata:vocabcoach_streaks:streak',
                'timemodified' => 'privacy:metadata:vocabcoach_streaks:timemodified',
            ],
            'privacy:metadata:vocabcoach_streaks'
        );

        $collection->add_database_table(
            'vocabcoach_lists',
            [
                'createdby' => 'privacy:metadata:vocabcoach_lists:createdby',
                'usermodified' => 'privacy:metadata:vocabcoach_lists:usermodified',
                'title' => 'privacy:metadata:vocabcoach_lists:title',
                'book' => 'privacy:metadata:vocabcoach_lists:book',
                'unit' => 'privacy:metadata:vocabcoach_lists:unit',
                'timecreated' => 'privacy:metadata:vocabcoach_lists:timecreated',
                'timemodified' => 'privacy:metadata:vocabcoach_lists:timemodified',
            ],
            'privacy:metadata:vocabcoach_lists'
        );

        $collection->add_database_table(
            'vocabcoach_vocab',
            [
                'createdby' => 'privacy:metadata:vocabcoach_vocab:createdby',
                'usermodified' => 'privacy:metadata:vocabcoach_vocab:usermodified',
                'front' => 'privacy:metadata:vocabcoach_vocab:front',
                'back' => 'privacy:metadata:vocabcoach_vocab:back',
                'timecreated' => 'privacy:metadata:vocabcoach_vocab:timecreated',
                'timemodified' => 'privacy:metadata:vocabcoach_vocab:timemodified',
            ],
            'privacy:metadata:vocabcoach_vocab'
        );

        $collection->add_subsystem_link('core_message', [], 'privacy:metadata:core_message');

        return $collection;
    }

    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if (!$context instanceof \context_module) {
            return;
        }

        $cmid = $context->instanceid;

        $DB->delete_records('vocabcoach_lists', ['cmid' => $cmid]);
        $DB->delete_records('vocabcoach_vocabdata', ['cmid' => $cmid]);
        $DB->delete_records('vocabcoach_streaks', ['cmid' => $cmid]);
        $DB->delete_records('vocabcoach_userprefs', ['cmid' => $cmid]);
    }


    public static function export_user_data(approved_contextlist $contextlist) {
        // TODO: Implement export_user_data() method.
    }

    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();

        $sql = "SELECT ctx.id
                FROM {context} ctx
                JOIN {vocabcoach_vocabdata} vd ON vd.cmid = ctx.instanceid AND ctx.contextlevel = :contextlevel
                WHERE vd.userid = :userid";
        $contextlist->add_from_sql($sql, ['userid' => $userid, 'contextlevel' => CONTEXT_MODULE]);

        $sql = "SELECT ctx.id
                FROM {context} ctx
                JOIN {vocabcoach_userprefs} up ON up.cmid = ctx.instanceid AND ctx.contextlevel = :contextlevel
                WHERE up.userid = :userid";
        $contextlist->add_from_sql($sql, ['userid' => $userid, 'contextlevel' => CONTEXT_MODULE]);

        $sql = "SELECT ctx.id
                FROM {context} ctx
                JOIN {vocabcoach_streaks} s ON s.cmid = ctx.instanceid AND ctx.contextlevel = :contextlevel
                WHERE s.userid = :userid";
        $contextlist->add_from_sql($sql, ['userid' => $userid, 'contextlevel' => CONTEXT_MODULE]);

        $sql = "SELECT ctx.id
                FROM {context} ctx
                JOIN {vocabcoach_lists} l ON l.cmid = ctx.instanceid AND ctx.contextlevel = :contextlevel
                WHERE l.createdby = :createdby OR l.usermodified = :usermodified";
        $params = ['createdby' => $userid, 'usermodified' => $userid, 'contextlevel' => CONTEXT_MODULE];
        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();
        if (!$context instanceof \context_module) {
            return;
        }
        $params = ['cmid' => $context->instanceid];

        $sql = "SELECT vd.userid FROM {vocabcoach_vocabdata} vd WHERE vd.cmid = :cmid";
        $userlist->add_from_sql('userid', $sql, $params);
        $sql = "SELECT up.userid FROM {vocabcoach_userprefs} up WHERE up.cmid = :cmid";
        $userlist->add_from_sql('userid', $sql, $params);
        $sql = "SELECT s.userid FROM {vocabcoach_streaks} s WHERE s.cmid = :cmid";
        $userlist->add_from_sql('userid', $sql, $params);
        $sql = "SELECT l.createdby FROM {vocabcoach_lists} l WHERE l.cmid = :cmid";
        $userlist->add_from_sql('createdby', $sql, $params);
        $sql = "SELECT l.usermodified FROM {vocabcoach_lists} l WHERE l.cmid = :cmid";
        $userlist->add_from_sql('usermodified', $sql, $params);
    }

    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;
        $userid = $contextlist->get_user()->id;

        foreach ($contextlist->get_contexts() as $context) {
            if (!$context instanceof \context_module) {
                continue;
            }

            $cmid = $context->instanceid;

            $DB->set_field('vocabcoach_lists', 'createdby', 0, ['cmid' => $cmid, 'createdby' => $userid, 'private' => false]);
            $DB->set_field('vocabcoach_lists', 'usermodified', 0, ['cmid' => $cmid, 'usermodified' => $userid]);
            $DB->delete_records('vocabcoach_lists', ['cmid' => $cmid, 'createdby' => $userid, 'private' => true]);

            $DB->set_field_select(
                'vocabcoach_vocab',
                'createdby',
                0,
                'createdby = :userid AND id IN (SELECT vocabid FROM {vocabcoach_vocabdata} WHERE cmid = :cmid)',
                ['cmid' => $cmid, 'userid' => $userid]
            );
            $DB->set_field_select(
                'vocabcoach_vocab',
                'usermodified',
                0,
                'usermodified = :usermodified AND id IN (SELECT vocabid FROM {vocabcoach_vocabdata} WHERE cmid = :cmid)',
                ['cmid' => $cmid, 'usermodified' => $userid]
            );

            $DB->delete_records('vocabcoach_streaks', ['userid' => $userid, 'cmid' => $cmid]);
            $DB->delete_records('vocabcoach_vocabdata', ['userid' => $userid, 'cmid' => $cmid]);
            $DB->delete_records('vocabcoach_userprefs', ['userid' => $userid, 'cmid' => $cmid]);
        }
    }

    public static function delete_data_for_users(approved_userlist $userlist) {
        // TODO: Implement delete_data_for_users() method.
    }
}
