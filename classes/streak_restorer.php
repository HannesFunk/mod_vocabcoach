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
 * Streak restorer utility class for vocabcoach module.
 *
 * @package   mod_vocabcoach
 * @copyright 2026, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_vocabcoach;
defined('MOODLE_INTERNAL') || die();

/**
 * Class for managing streak restores with monthly limits.
 */
class streak_restorer
{

    const MAX_RESTORES_PER_MONTH = 3;

    /**
     * Get the current month in YYYY-MM format.
     *
     * @return string
     */
    public static function get_current_month()
    {
        return date('Y-m');
    }

    /**
     * Check if a user can restore a streak.
     *
     * @param int $userid User ID
     * @param int $cmid Course module ID
     * @param string $streak_type Type of streak (login, checkall)
     * @return bool True if user can restore, false otherwise
     */
    public static function can_restore_streak($userid, $cmid, $streak_type)
    {
        global $DB;

        $month_year = self::get_current_month();

        $record = $DB->get_record('vocabcoach_streak_restores', [
            'userid' => $userid,
            'cmid' => $cmid,
            'streak_type' => $streak_type,
            'month_year' => $month_year,
        ]);

        if (!$record) {
            return true;
        }

        return $record->restore_count < self::MAX_RESTORES_PER_MONTH;
    }

    /**
     * Get remaining restores for this month.
     *
     * @param int $userid User ID
     * @param int $cmid Course module ID
     * @param string $streak_type Type of streak (login, checkall)
     * @return int Number of restores remaining (0-3)
     */
    public static function get_remaining_restores($userid, $cmid, $streak_type)
    {
        global $DB;

        $month_year = self::get_current_month();

        $record = $DB->get_record('vocabcoach_streak_restores', [
            'userid' => $userid,
            'cmid' => $cmid,
            'streak_type' => $streak_type,
            'month_year' => $month_year,
        ]);

        if (!$record) {
            return self::MAX_RESTORES_PER_MONTH;
        }

        return max(0, self::MAX_RESTORES_PER_MONTH - $record->restore_count);
    }

    /**
     * Restore a streak by incrementing its value by 1.
     *
     * @param int $userid User ID
     * @param int $cmid Course module ID
     * @param string $streak_type Type of streak (login, checkall)
     * @return bool True on success, false if restore limit reached
     * @throws \Exception If streak record not found
     */
    public static function restore_streak($userid, $cmid, $streak_type)
    {
        global $DB;

        $transaction = $DB->start_delegated_transaction();

        try {
            $month_year = self::get_current_month();

            $record = $DB->get_record('vocabcoach_streak_restores', [
                'userid' => $userid,
                'cmid' => $cmid,
                'streak_type' => $streak_type,
                'month_year' => $month_year,
            ]);

            if ($record) {
                $record->restore_count++;
                $record->timemodified = time();
                $DB->update_record('vocabcoach_streak_restores', $record);
            } else {
                $record = (object)[
                    'userid' => $userid,
                    'cmid' => $cmid,
                    'streak_type' => $streak_type,
                    'restore_count' => 1,
                    'month_year' => $month_year,
                    'timemodified' => time(),
                ];
                $DB->insert_record('vocabcoach_streak_restores', $record);
            }

            $streak = $DB->get_record('vocabcoach_streaks', [
                'userid' => $userid,
                'cmid' => $cmid,
                'type' => $streak_type,
            ]);

            if (!$streak) {
                throw new \Exception('Streak record not found');
            }

            $streak->streak++;
            $streak->timemodified = time();
            $DB->update_record('vocabcoach_streaks', $streak);

            $transaction->allow_commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollback($e);
            return false;
        }
    }

    /**
     * Get restore stats for display.
     *
     * @param int $userid User ID
     * @param int $cmid Course module ID
     * @param string $streak_type Type of streak (login, checkall)
     * @return object Object with used and remaining properties
     */
    public static function get_restore_stats($userid, $cmid, $streak_type)
    {
        $remaining = self::get_remaining_restores($userid, $cmid, $streak_type);
        $used = self::MAX_RESTORES_PER_MONTH - $remaining;

        return (object)[
            'used' => $used,
            'remaining' => $remaining,
            'max' => self::MAX_RESTORES_PER_MONTH,
        ];
    }
}
