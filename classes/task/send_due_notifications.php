<?php

namespace mod_vocabcoach\task;
use mod_vocabcoach\vocabhelper;

defined('MOODLE_INTERNAL') || die();

/**
 * Scheduled task: send notifications about due vocab items to students.
 *
 * @package    mod_vocabcoach
 * @copyright  2025 Johannes Funk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class send_due_notifications extends \core\task\scheduled_task {
    public function get_name(): string {
        return get_string('task_sendduenotifications', 'mod_vocabcoach');
    }

    public function execute(): void {
        global $DB;

        mtrace('mod_vocabcoach: sending due notifications...');

        // Find all course modules for this activity.
        $cms = $DB->get_records_sql("SELECT cm.id AS cmid, cm.course
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

        mtrace('mod_vocabcoach: found ' . count($cms) . ' course modules.');

        foreach ($cms as $cm) {
            // instantiate vocabhelper for this cmid. Skip if something goes wrong.
            try {
                $vh = new vocabhelper($cm->cmid);
            } catch (\dml_exception | \coding_exception $e) {
                // skip invalid/removed instances
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
                    $sql = "SELECT COUNT(*) FROM {vocabcoach_vocabdata} vd WHERE vd.userid = :userid AND vd.cmid = :cmid AND (" . $boxconditions . ")";
                    $count = (int)$DB->count_records_sql($sql, ['userid' => $student->id, 'cmid' => $cm->cmid]);

                    if ($count <= 0) {
                        continue;
                    }

                    // prepare message with direct link to the module
                    $url = new \moodle_url('/mod/vocabcoach/view.php', ['id' => $cm->cmid]);
                    $noreply = \core_user::get_noreply_user();

                    $message = new \core\message\message();
                    $message->component = 'mod_vocabcoach';
                    $message->name = 'due_notification';
                    $message->userfrom = $noreply;
                    $message->userto = $student;
                    $message->subject = get_string('due_notification_subject', 'mod_vocabcoach', $count);
                    $message->fullmessage = get_string('due_notification_body', 'mod_vocabcoach', ['count' => $count, 'url' => $url->out(false)]);
                    $message->fullmessageformat = FORMAT_PLAIN;
                    $message->fullmessagehtml = '';
                    $message->smallmessage = get_string('due_notification_small', 'mod_vocabcoach', $count);
                    $message->contexturl = $url->out(false);
                    $message->contexturlname = get_string('pluginname', 'mod_vocabcoach');

                    message_send($message);
                } catch (\Exception $e) {
                    mtrace('mod_vocabcoach: failed to send message to user ' . $student->id . ': ' . $e->getMessage());
                }

            }
        mtrace('mod_vocabcoach: sent due notifications to ' . count($students) . ' students in module ' . $cm->cmid . '.');
        }
    }
}

