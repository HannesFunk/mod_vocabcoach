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

function xmldb_vocabcoach_upgrade($oldversion): bool {
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

        // Vocabcoach savepoint reached.
        upgrade_mod_savepoint(true, 2023100309, 'vocabcoach');
    }

    if ($oldversion < 2023103022) {
        // Define field id to be added to vocabcoach.
        $table = new xmldb_table('vocabcoach');
        $field = new xmldb_field('thirdactive', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, 1, null);

        // Conditionally launch add field id.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Vocabcoach savepoint reached.
        upgrade_mod_savepoint(true, 2023103022, 'vocabcoach');
    }
    return true;
}
