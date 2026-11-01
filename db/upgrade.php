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
 * Database upgrade steps for mod_vocabcoach.
 *
 * @package   mod_vocabcoach
 * @copyright 2023 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Johannes Funk
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade the mod_vocabcoach database schema.
 *
 * @param int $oldversion The version we are upgrading from.
 * @return bool Always true; a failed step throws instead.
 */
function xmldb_vocabcoach_upgrade(int $oldversion): bool {
    // No upgrade steps yet. Add them above the return, newest last.

    return true;
}
