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

defined('MOODLE_INTERNAL') || die();

/**
 * Box manager class. Manages the vocab boxes for a user.
 *
 * @package   mod_vocabcoach
 * @copyright 2023 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Johannes Funk
 */
class box_manager {
    /** @var vocabhelper The instance of vocabhelper to do basic vocab operations */
    private vocabhelper $vocabhelper;

    /** @var int $userid User ID */
    private int $userid;

    /** @var array $boxdata Box data */
    private array $boxdata = [];

    /**
     * Construct the class.
     *
     * @param int $cmid Course module id
     * @param int $userid User id
     * @throws \dml_exception
     */
    public function __construct(int $cmid, int $userid) {
        $this->vocabhelper = new vocabhelper($cmid);
        $this->userid = $userid;
        $this->boxdata = $this->query_box_details();
    }

    /**
     * Determine the details of a user's box
     * @return array
     */
    private function query_box_details(): array {
        global $DB;
        $vh = $this->vocabhelper;
        [$from, $params] = $vh->get_vocabs_sql($this->userid);
        [$duesql, $dueparams] = $vh->get_due_sql_all();

        $sql = "SELECT vd.stage AS stage,
                       COUNT(*) AS total,
                       SUM(CASE WHEN $duesql THEN 1 ELSE 0 END) AS due,
                       MIN(vd.lastchecked) AS oldest
                $from
                    GROUP BY vd.stage";
        $rows = $DB->get_records_sql($sql, array_merge($params, $dueparams));

        for ($i = 1; $i <= $vh->boxnumber; $i++) {
            if (!isset($rows[$i])) {
                $row = (object)[
                    'total' => 0,
                    'due' => 0,
                    'stage' => $i,
                    'oldest' => null,
                ];
                $rows[$i] = $row;
            }

            $row = $rows[$i];
            $row->due = (int) $row->due;
            $row->total = (int) $row->total;
            $row->stage = (int) $row->stage;

            $row->status = self::compute_box_status($row->due, $row->total);
            $row->nextdue = $row->due === 0
                ?  $vh->compute_due_time_string($row->oldest, $i)
                : get_string('duetime_now', 'mod_vocabcoach');
        }
        ksort($rows);
        return $rows;
    }

    /**
     * Returns an array to display all the information used on the view page
     * @return array
     */
    public function get_box_details(): array {
        return array_values($this->boxdata);
    }

    /**
     * Computes the status of a box based on due and total counts.
     *
     * @param int $due Number of due items
     * @param int $total Total number of items
     * @return string Status of the box ('empty', 'done', or 'todo')
     */
    private static function compute_box_status(int $due, int $total): string {
        if ($total == 0) {
            return 'empty';
        }
        if ($due == 0) {
            return 'done';
        }
        return 'todo';
    }
    /**
     * Returns the total number of due items across all boxes.
     * @return int Total number of due items
     */
    public function get_total_due(): int {
        return array_sum(array_column($this->boxdata, 'due'));
    }
}
