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
    $add_version = 4;
    upgrade_mod_savepoint(true, 2025110520 + $add_version, 'vocabcoach');
    return true;
}
