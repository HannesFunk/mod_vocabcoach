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

namespace mod_vocabcoach;

/**
 * Vocabhelper - several methods to deal with database-display interaction
 *
 * @package   mod_vocabcoach
 * @copyright 2023 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Johannes Funk
 */
class vocabhelper {
    /**
     * @var int $boxnumber Number of boxes.
     */
    public int $boxnumber = 5;
    /**
     * @var int $cmid The course module id.
     */
    public int $cmid;
    /**
     * @var array|int[] $boxtimes Times (in days) after vocab in a box is revisited.
     */
    public array $boxtimes = [0, 1, 2, 5, 10, 30];

    /**
     * Construct the class.
     * @param int $cmid
     * @throws dml_exception
     */
    public function __construct(int $cmid) {
        global $DB;
        $this->cmid = $cmid;
        $cm = get_coursemodule_from_id('vocabcoach', $cmid, 0, false, MUST_EXIST);
        $instanceinfo = $DB->get_record('vocabcoach', ['id' => $cm->instance]);
        for ($i = 1; $i <= 5; $i++) {
            $this->boxtimes[$i] = $instanceinfo->{'boxtime_' . $i};
        }
    }

    /**
     * Returns a timestamp
     * @param int $daysago Number of days before now
     * @return int
     */
    public function old_timestamp(int $daysago): int {
        $now = time();
        return $now - ($daysago - 0.5) * 60 * 60 * 24;
    }

    /**
     * Returns a string when the vocab is due next
     * @param int|null $lastchecked The last time the vocab was checked
     * @param int $stage The stage the vocab is currently at
     * @return string
     */
    public function compute_due_time_string(int|null $lastchecked, int $stage): string {
        if ($lastchecked === null) {
            return '-';
        }

        $boxtime = $this->boxtimes[$stage];
        $nextdue = time() + $boxtime * 60 * 60 * 24;
        $secondsleft = $nextdue - $lastchecked;
        if ($secondsleft > 60 * 60 * 24) {
            $time = floor($secondsleft / (60 * 60 * 24));
            return get_string($time > 1 ? 'duetime_days' : 'duetime_day', 'mod_vocabcoach', $time);
        } else {
            $time = floor($secondsleft / (60 * 60));
            return get_string($time > 1 ? 'duetime_hours' : 'duetime_hour', 'mod_vocabcoach', $time);
        }
    }

    public function get_due_sql(int $stage): array {
        if (!array_key_exists($stage, $this->boxtimes)) {
            throw new \invalid_parameter_exception('Invalid stage: ' . $stage);
        }

        $key = 'duetime' . $stage;
        return [
            "vd.lastchecked < :$key",
            [$key => $this->old_timestamp($this->boxtimes[$stage])]
        ];
    }

    public function get_due_sql_all(): array {
        $clauses = [];
        $params = [];
        for ($i = 1; $i <= $this->boxnumber; $i++) {
            [$clause, $param] = $this->get_due_sql($i);
            $clauses[] = "(vd.stage = $i AND $clause)";
            $params += $param;
        }
        $sql = '(' . implode(' OR ', $clauses) . ')';
        return [$sql, $params];
    }

    public function get_vocabs_sql(int $userid): array {
        $sql = " FROM {vocabcoach_vocabdata} vd
                 JOIN {vocabcoach_vocab} v ON v.id = vd.vocabid
                 WHERE vd.userid = :userid AND vd.cmid = :cmid";
        $params = ['userid' => $userid, 'cmid' => $this->cmid];
        return [$sql, $params];
    }
}
