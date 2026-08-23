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

namespace mod_vocabcoach\task;

/**
 * Scheduled task: send notifications about due vocab items to students.
 *
 * @package    mod_vocabcoach
 * @copyright  2025 Johannes Funk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_due_notifications extends \core\task\scheduled_task {
    /**
     * Returns the name of the task.
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('task_sendduenotifications', 'mod_vocabcoach');
    }

    /**
     * Execute the task.
     */
    public function execute(): void {
        global $DB;

        mtrace('mod_vocabcoach: sending due notifications...');

        // Find all course modules for this activity.
        $cms = $DB->get_records_sql("SELECT cm.id AS cmid, cm.course, cm.instance
            FROM {course_modules} cm
            JOIN {modules} md ON md.id = cm.module
            WHERE md.name = 'vocabcoach'");

        if (empty($cms)) {
            mtrace('mod_vocabcoach: no course modules found.');
            return;
        }

        $studentrole = $DB->get_record('role', ['shortname' => 'student']);
        if (!$studentrole) {
            mtrace('mod_vocabcoach: role student not found.');
            return;
        }

        mtrace('mod_vocabcoach: found ' . count($cms) . ' course module(s).');

        foreach ($cms as $cm) {
            // Load module instance and skip if notifications disabled.
            $instance = $DB->get_record('vocabcoach', ['id' => $cm->instance]);
            if (!$instance) {
                mtrace('mod_vocabcoach: missing instance for cmid ' . $cm->cmid);
                continue;
            }
            if (isset($instance->notifications_enabled) && (int)$instance->notifications_enabled == 0) {
                    mtrace('mod_vocabcoach: notifications disabled for cmid ' . $cm->cmid);
                    continue;
            }

            $notificationstotal = 0;

            try {
                $vh = new \mod_vocabcoach\vocabhelper($cm->cmid);
            } catch (\dml_exception | \coding_exception $e) {
                // Skip invalid/removed instances.
                mtrace('mod_vocabcoach: skipping cmid ' . $cm->cmid . ' (' . $e->getMessage() . ')');
                continue;
            }

            $boxconditions = $vh->get_sql_box_conditions();
            if (trim($boxconditions) === '') {
                continue;
            }

            $coursecontext = \context_course::instance($cm->course);
            $students = get_role_users($studentrole->id, $coursecontext, false, 'u.*');
            if (empty($students)) {
                mtrace('mod_vocabcoach: no students found in course ' . $cm->course);
                continue;
            }

            foreach ($students as $student) {
                try {
                    $userprefs = new \mod_vocabcoach\user_preferences($cm->cmid, $student->id);
                    if (!$userprefs->get_email_notifications_enabled()) {
                        continue;
                    }

                    $sql = "SELECT COUNT(*) FROM {vocabcoach_vocabdata} vd
                            WHERE vd.userid = :userid AND vd.cmid = :cmid AND (" . $boxconditions . ")";
                    $count = (int)$DB->count_records_sql($sql, ['userid' => $student->id, 'cmid' => $cm->cmid]);

                    if ($count <= 0) {
                        continue;
                    }

                    // Prepare message with direct link to the module.
                    $url = new \moodle_url('/mod/vocabcoach/view.php', ['id' => $cm->cmid]);
                    $noreply = \core_user::get_noreply_user();

                    $message = new \core\message\message();
                    $message->component = 'mod_vocabcoach';
                    $message->name = 'due_notification';
                    $message->userfrom = $noreply;
                    $message->userto = $student;
                    $message->subject = get_string('due_notification_subject', 'mod_vocabcoach', $count);
                    $message->fullmessage = get_string(
                        'due_notification_body',
                        'mod_vocabcoach',
                        ['count' => $count, 'url' => $url->out(false)]
                    );
                    $message->fullmessageformat = FORMAT_PLAIN;
                    $message->fullmessagehtml = '';
                    $message->smallmessage = get_string('due_notification_small', 'mod_vocabcoach', $count);
                    $message->contexturl = $url->out(false);
                    $message->contexturlname = get_string('pluginname', 'mod_vocabcoach');
                    message_send($message);
                    $notificationstotal++;
                } catch (\Exception $e) {
                    mtrace('mod_vocabcoach: failed to send message to user ' . $student->id . ': ' . $e->getMessage());
                }
            }
            mtrace('mod_vocabcoach: processed ' . count($students) .
                ' students in module ' . $cm->cmid . '.' .
                ' Notifications sent to ' . $notificationstotal . ' users.');
        }
    }
}
