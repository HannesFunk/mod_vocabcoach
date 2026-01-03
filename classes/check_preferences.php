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

defined('MOODLE_INTERNAL') || die();

/**
 * Helper to get/set per-user check mode preferences per activity.
 */
class check_preferences {
    /**
     * Allowed modes.
     * @return string[]
     */
    public static function allowed_modes(): array {
        return ['front', 'back', 'random', 'type'];
    }

    /**
     * Get the user preference for this cm; default to 'random' if none.
     *
     * @param int $cmid
     * @param int $userid
     * @return string
     */
    public static function get_mode(int $cmid, int $userid): string {
        global $DB;
        $record = $DB->get_record('vocabcoach_checkprefs', ['cmid' => $cmid, 'userid' => $userid]);
        if (!$record) {
            return 'random';
        }
        if (!in_array($record->mode, self::allowed_modes(), true)) {
            return 'random';
        }
        return $record->mode;
    }

    /**
     * Store/update the user preference.
     *
     * @param int $cmid
     * @param int $userid
     * @param string $mode
     * @return void
     */
    public static function set_mode(int $cmid, int $userid, string $mode): void {
        global $DB;
        if (!in_array($mode, self::allowed_modes(), true)) {
            throw new \invalid_parameter_exception('invalidmode');
        }
        $now = time();
        $data = (object) [
            'cmid' => $cmid,
            'userid' => $userid,
            'mode' => $mode,
            'timemodified' => $now,
        ];
        $existing = $DB->get_record('vocabcoach_checkprefs', ['cmid' => $cmid, 'userid' => $userid]);
        if ($existing) {
            $data->id = $existing->id;
            $DB->update_record('vocabcoach_checkprefs', $data);
        } else {
            $DB->insert_record('vocabcoach_checkprefs', $data);
        }
    }
}

