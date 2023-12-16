<?php
// This file is part of Moodle Course Rollover Plugin
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

namespace mod_vocabcoach;
use vocabhelper;

defined('MOODLE_INTERNAL') || die();
require('vocabhelper.php');

/**
 * Box manager class. Manages the vocab boxes for a user.
 *
 * @package   mod_vocabcoach
 * @copyright 2023 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Johannes Funk
 */
class box_manager {
    /**
     * @var vocabhelper The instance of vocabhelper to do basic vocab operations
     */
    private vocabhelper $vocabhelper;
    /**
     * @var int $cmid Course Module ID
     * @var int $userid  User ID
     */
    private int $cmid, $userid;

    /**
     * Construct the class.
     *
     * @param int $cmid Course module id
     * @param int $userid User id
     * @throws \dml_exception
     */
    public function __construct(int $cmid, int $userid) {
        $this->vocabhelper = new vocabhelper($cmid);
        $this->cmid = $cmid;
        $this->userid = $userid;
    }

    /**
     * Returns an array to display all the information used on the first page
     * @return array
     */
    public function get_box_details() : array {
        global $DB;

        $output = [];
        for ($i = 1; $i <= $this->vocabhelper->boxnumber; $i++) {
            try {
                $total = $DB->count_records_select('vocabcoach_vocabdata', 'userid = ? AND cmid = ? AND stage = ?',
                    [$this->userid, $this->cmid, $i]);

                $mindayssincecheck = $this->vocabhelper->boxtimes[$i];
                $due = $DB->count_records_select('vocabcoach_vocabdata',
                    'userid = ? AND cmid = ? AND stage = ? AND lastchecked < ?',
                    [$this->userid, $this->cmid, $i, $this->vocabhelper->old_timestamp($mindayssincecheck)]);
            } catch (\dml_exception $e) {
                die ($e->getMessage());
            }

            if ($due === 0) {
                $query = "SELECT MIN(vd.lastchecked) AS recent
                            FROM {vocabcoach_vocabdata} vd
                            WHERE userid = {$this->userid} AND cmid = {$this->cmid} AND stage = {$i}
                            ";
                try {
                    $record = $DB->get_record_sql($query);
                    $nextdue = $this->vocabhelper->compute_due_time_string($record->recent, $this->vocabhelper->boxtimes[$i]);
                } catch (\dml_exception $e) {
                    $nextdue = '-';
                }
            } else {
                $nextdue = 'Jetzt';
            }

            $output[] = [
                'stage' => $i,
                'due' => $due,
                'total' => $total,
                'inactive' => $due == 0,
                'next_due' => $nextdue,
            ];
        }
        return $output;
    }
}
