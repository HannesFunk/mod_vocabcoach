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
class user_preferences {
    /** @var int Course module ID */
    private int $cmid;

    /** @var int User ID */
    private int $userid;
    /** @var mixed existing DB user preferences */
    private mixed $existing_prefs;

    /**
     * Constructor.
     *
     * @param int $cmid Course module ID
     * @param int $userid User ID
     */
    public function __construct(int $cmid, int $userid) {
        global $DB;
        $this->cmid = $cmid;
        $this->userid = $userid;
        $this->existing_prefs = $DB->get_record('vocabcoach_userprefs', ['cmid' => $this->cmid, 'userid' => $this->userid]);
    }

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
     * @return string
     */
    public function get_mode(): string {
        $record = $this->existing_prefs;
        if (!$record) {
            return 'random';
        }
        if (!in_array($record->mode, self::allowed_modes(), true)) {
            return 'random';
        }
        return $record->mode;
    }

    public function get_template_context() : array {
        $mode = $this->get_mode();
        return [
            'frontSelected' => $mode === 'front',
            'backSelected' => $mode === 'back',
            'randomSelected' => $mode === 'random',
            'typeSelected' => $mode === 'type',
            'userNotificationsEnabled' => $this->get_email_notifications_enabled(),
        ];
    }

    /**
     * Store/update the user preference.
     *
     * @param string $mode
     * @return void
     */
    public function set_mode(string $mode): void {
        global $DB;
        if (!in_array($mode, self::allowed_modes(), true)) {
            throw new \invalid_parameter_exception('invalidmode');
        }
        $now = time();
        $data = (object) [
            'cmid' => $this->cmid,
            'userid' => $this->userid,
            'mode' => $mode,
            'timemodified' => $now,
        ];
        $existing = $this->existing_prefs;
        if ($existing) {
            $data->id = $existing->id;
            $DB->update_record('vocabcoach_userprefs', $data);
        } else {
            $DB->insert_record('vocabcoach_userprefs', $data);
        }
    }

    /**
     * Get the user's email notification preference for this cm.
     *
     * @return bool
     */
    public function get_email_notifications_enabled(): bool {
        global $DB;
        $record = $this->existing_prefs;
        if ($record) {
            return $record->email_notifications;
        }
        $cm = get_coursemodule_from_id('vocabcoach', $this->cmid, 0, false, MUST_EXIST);
        $moduleinstance = $DB->get_record('vocabcoach', ['id' => $cm->instance], '*', MUST_EXIST);
        return $moduleinstance->notifications_optout;
    }

    /**
     * Set the user's email notification preference.
     *
     * @param bool $enabled
     * @return void
     */
    public function set_email_notifications(bool $enabled): void {
        global $DB;

        $now = time();
        $data = (object) [
            'cmid' => $this->cmid,
            'userid' => $this->userid,
            'email_notifications' => $enabled ? 1 : 0,
            'timemodified' => $now,
        ];

        $existing = $this->existing_prefs;
        if ($existing) {
            $data->id = $existing->id;
            $DB->update_record('vocabcoach_userprefs', $data);
        } else {
            $DB->insert_record('vocabcoach_userprefs', $data);
        }
    }
}