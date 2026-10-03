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
use dml_exception;
use invalid_parameter_exception;

/**
 * Streak manager. Provides information about the streaks of a user in a vocabcoach instance.
 *
 * @package   mod_vocabcoach
 * @copyright 2026, Johannes Funk
 * @author Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class streak_manager
{
    /** @var int $userid User ID  */
    private int $userid;
    /** @var int $cmid Course Module ID */
    private int $cmid;
    /** @var array $types Allowed streak types */
    private array $types = ['login', 'checkall'];

    /**
     * Construct the class.
     * @param int $userid User id
     * @param int $cmid Course module id
     */
    public function __construct(int $userid, int $cmid) {
        $this->userid = $userid;
        $this->cmid = $cmid;
    }

    /**
     * Get the current streak info of the user.
     * @param string $type Type of the streak
     * @return object|null Current streak
     */
    public function get_streak(string $type): object|null {
        global $DB;
        $record = $DB->get_record(
            'vocabcoach_streaks',
            ['userid' => $this->userid, 'cmid' => $this->cmid, 'type' => $type]
        );
        return $record ?: null;
    }

    /**
     * Get the current streak info of the user.
     * @param string|null $selectedtype Optional type to get streak info for. If null, returns info for all types.
     * @return object Current streak information
     * @throws \core\exception\invalid_parameter_exception
     */
    public function get_streak_info(string|null $selectedtype = null): object {
        $info = [];
        if ($selectedtype && !in_array($selectedtype, $this->types)) {
            throw new invalid_parameter_exception("Invalid type for streak. Allowed types: " . implode(", ", $this->types));
        }
        $types = $selectedtype ? [$selectedtype] : $this->types;
        foreach ($types as $type) {
            $streak = $this->get_streak($type);
            $info[$type]['streak'] = $streak->streak;
            $info[$type]['isactive'] = self::is_active($streak);
        }
        if ($selectedtype) {
            return (object)$info[$selectedtype];
        } else {
            return (object)$info;
        }
    }

    public static function is_active(object|null $streak): bool {
        if (!$streak) {
            return false;
        }
        return $streak->timemodified > strtotime('today midnight');
    }

    /**
     * Update the streak info of the user.
     * @param string $type Streak type
     * @throws \core\exception\invalid_parameter_exception|dml_exception
     */
    public function update(string $type, bool $extend): void {
        if (!in_array($type, $this->types)) {
            throw new invalid_parameter_exception("Invalid type for streak. Allowed types: " . implode(", ", $this->types));
        }
        global $DB;
        $streak = $this->get_streak($type);

        if (!$streak) {
            $streak = (object)[
                'userid' => $this->userid,
                'cmid' => $this->cmid,
                'type' => $type,
                'streak' => $extend ? 1 : 0,
                'timemodified' => time(),
            ];
            $DB->insert_record('vocabcoach_streaks', $streak);
            return;
        }

        if ($streak->timemodified > strtotime("today midnight")) {
            // Streak has already been updated today.
            $streak->timemodified = time();
        } else if ($streak->timemodified > strtotime("yesterday midnight")) {
            // Streak has not yet been updated.
            $streak->timemodified = time();
            if ($extend) {
                $streak->streak++;
            }
        } else {
            // Streak is reset.
            $streak->timemodified = time();
            if ($extend) {
                $streak->streak = 1;
            } else {
                $DB->delete_records('vocabcoach_streaks', ['id' => $streak->id]);
                return;
            }
        }
        $DB->update_record('vocabcoach_streaks', $streak);
    }
}
