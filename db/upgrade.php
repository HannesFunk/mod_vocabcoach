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
 * Make DB upgrades.
 *
 * @package   mod_vocabcoach
 * @copyright 2023 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Johannes Funk
 */

/**
 * Make Database upgrades.
 * @param int $oldversion
 * @return bool
 * @throws ddl_exception
 * @throws ddl_table_missing_exception
 * @throws downgrade_exception
 * @throws moodle_exception
 * @throws upgrade_exception
 */
function xmldb_vocabcoach_upgrade(int $oldversion): bool {
    global $DB;
    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    if ($oldversion < 2023100309) {
        // Define field id to be added to vocabcoach_lists.
        $table = new xmldb_table('vocabcoach_lists');
        $field = new xmldb_field('private', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, 0, null);

        // Conditionally launch add field id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
    }

    if ($oldversion < 2023103022) {
        // Define field id to be added to vocabcoach.
        $table = new xmldb_table('vocabcoach');
        $field = new xmldb_field('thirdactive', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, 1, null);

        // Conditionally launch add field id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
    }

    if ($oldversion < 2025100222) {
        $table = new xmldb_table('vocabcoach');
        $field_front = new xmldb_field('desc_front', XMLDB_TYPE_CHAR, '127', null, XMLDB_NOTNULL, null, "Englisch", null);
        $field_back = new xmldb_field('desc_back', XMLDB_TYPE_CHAR, '127', null, XMLDB_NOTNULL, null, "Deutsch", null);

        foreach ([$field_front, $field_back] as $field) {
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
        }
    }

    if ($oldversion < 2025120100) {
        $table = new xmldb_table('vocabcoach');
        $field = new xmldb_field('notify_students', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 1, null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
    }

    if ($oldversion < 2025123002) {
        $table = new xmldb_table('vocabcoach');
        $field = new xmldb_field('instructions', XMLDB_TYPE_TEXT);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
    }
    if ($oldversion < 2026010100) {
        $table = new xmldb_table('vocabcoach_userprefs');
        if (!$dbman->table_exists($table)) {
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null, 'primary key');
            $table->add_field('cmid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, null, 'cmid');
            $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, null, 'userid');
            $table->add_field('mode', XMLDB_TYPE_CHAR, '16', null, XMLDB_NOTNULL, null, 'random', null, null, 'mode');
            $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0, null, null, 'timemodified');

            $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
            $table->add_key('uniq_cmid_user', XMLDB_KEY_UNIQUE, ['cmid', 'userid']);
            $table->add_key('fk_cmid', XMLDB_KEY_FOREIGN, ['cmid'], 'course_modules', ['id']);
            $table->add_key('fk_user', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);

            $dbman->create_table($table);
        }
        upgrade_mod_savepoint(true, 2026010100, 'vocabcoach');
    }

    if ($oldversion < 2026020700) {
        $table = new xmldb_table('vocabcoach_userprefs');
        $field = new xmldb_field('email_notifications', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 0);

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2026020700, 'vocabcoach');
    }

    if ($oldversion < 2026020800) {
        $table = new xmldb_table('vocabcoach');
        $field = new xmldb_field('notify_students', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 1, null);
        $dbman->rename_field($table, $field, "notifications_enabled");

        $field = new xmldb_field('notifications_optout', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 0);
        $dbman->add_field($table, $field);

        upgrade_mod_savepoint(true, 2026020800, 'vocabcoach');
    }

    if ($oldversion < 2026020801) {
        // Rename vocabcoach_checkprefs table to vocabcoach_userprefs.
        $table = new xmldb_table('vocabcoach_checkprefs');
        if ($dbman->table_exists($table)) {
            $dbman->rename_table($table, 'vocabcoach_userprefs');
        }

        upgrade_mod_savepoint(true, 2026020801, 'vocabcoach');
    }

    return true;
}
