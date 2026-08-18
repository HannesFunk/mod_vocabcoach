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
use invalid_parameter_exception;
defined('MOODLE_INTERNAL') || die();

/**
 * Stream Manager
 *
 * @package   mod_vocabcoach
 * @copyright 2026 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Johannes Funk
 */
class streak_manager {
    /**
     * @var int $userid User ID
     * @var int $cmid Course Module ID
     */
    private int $userid, $cmid;
    private array $types = ['login', 'checkall'];

    /**
     * Construct the class.
     * @param int $userid User id
     * @param int $cmid Course module id
     */
    public function __construct(int $userid, int $cmid)
    {
        $this->userid = $userid;
        $this->cmid = $cmid;
    }

    /**
     * Get the current streak info of the user.
     * @return object Current streak
     */
    public function get_streak(string $type): object
    {
        global $DB;
        $record = $DB->get_record(
            'vocabcoach_streaks',
            ['userid' => $this->userid, 'cmid' => $this->cmid, 'type' => $type]
        );

        if (!$record) {
            return (object)[
                'userid' => $this->userid,
                'cmid' => $this->cmid,
                'type' => $type,
                'streak' => 1,
                'timemodified' => 0,
            ];
        }
        return $record;
    }

    public function get_streak_info(string|null $selectedtype = null): object {
        $info = [];
        if ($selectedtype && !in_array($selectedtype, $this->types)) {
            throw new \core\exception\invalid_parameter_exception("Invalid type for streak. Allowed types: " . implode(", ", $this->types));
        }
        $types = $selectedtype ? [$selectedtype] : $this->types;
        foreach ($types as $type) {
            $streak = $this->get_streak($type);
            $info[$type]['streak'] = $streak->streak;
        }
        if ($selectedtype) {
            return (object)$info[$selectedtype];
        } else {
            return (object)$info;
        }
    }

   public function update(string $type, bool $maintained = true): void {
        if (!in_array($type, $this->types)) {
            throw new invalid_parameter_exception("Invalid type for streak. Allowed types: " . implode(", ", $this->types));
        }
        global $DB;
        $streak = $this->get_streak($type);

        if (empty($streak->id)) {
            $streak->timemodified = time();
            $DB->insert_record('vocabcoach_streaks', $streak);
            return;
        }

        if ($streak->timemodified < strtotime("-2 days midnight")) {
            $streak->timemodified = time();
            $streak->streak = 1;
            $DB->update_record('vocabcoach_streaks', $streak);
        }
        else if ($streak->timemodified < strtotime("today midnight") && $maintained) {
            $streak->timemodified = time();
            $streak->streak++;
            $DB->update_record('vocabcoach_streaks', $streak);
        }
    }
}