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
 * Step definitions for mod_vocabcoach.
 *
 * @package   mod_vocabcoach
 * @category  test
 * @copyright 2026, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

/**
 * Step definitions for mod_vocabcoach.
 *
 * @package   mod_vocabcoach
 * @category  test
 * @copyright 2026, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_mod_vocabcoach extends behat_base {
    /**
     * Convert page names to URLs for steps like 'When I am on the "[identifier]" "[page type]" page'.
     *
     * Recognised page names are:
     * | pagetype | name meaning                      | description                     |
     * | Check    | Vocabcoach name or idnumber       | Check of box 1 (check.php)      |
     *
     * @param string $type identifies which type of page this is, e.g. 'Check'.
     * @param string $identifier identifies the particular page, e.g. 'VC1'.
     * @return moodle_url the corresponding URL.
     * @throws Exception with a meaningful error message if the specified page cannot be found.
     */
    protected function resolve_page_instance_url(string $type, string $identifier): moodle_url {
        switch (strtolower($type)) {
            case 'check':
                return new moodle_url('/mod/vocabcoach/check.php', [
                    'id' => $this->get_cm_by_activity_name('vocabcoach', $identifier)->id,
                    'stage' => 1,
                ]);

            default:
                throw new Exception("Unrecognised page type '{$type}'");
        }
    }
}
