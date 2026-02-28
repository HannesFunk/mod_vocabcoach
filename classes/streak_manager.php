<?php

namespace mod_vocabcoach;

use invalid_parameter_exception;

class streak_manager {
        /**
        * @var int $userid User ID
        * @var int $cmid Course Module ID
        */
        private int $userid, $cmid;
        private array $types = ['login', 'checkall'];
        private array $statekeys = [
            0 => 'active',
            1 => 'restorable',
            2 => 'obsolete'
        ];

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
         * Get the current streak of the user.
         * @return int Current streak
         */
        public function get_streak_count(string $type) : int {
            if (!in_array($type, $this->types)) {
                throw new invalid_parameter_exception("Invalid type for streak. Allowed types: " . implode(", ", $this->types));
            }
            global $DB;
            $record = $DB->get_record(
                'vocabcoach_streaks',
                ['userid' => $this->userid, 'cmid' => $this->cmid, 'type' => $type]
            );
            return $record ? $record->streak : 0;
        }

        public function get_streak(string $type) : object|null {
            if (!in_array($type, $this->types)) {
                return null;
            }

            global $DB;
            $record = $DB->get_record(
                'vocabcoach_streaks',
                ['userid' => $this->userid, 'cmid' => $this->cmid, 'type' => $type]
            );

            if (!$record) {
                return (object) [
                    'userid' => $this->userid,
                    'cmid' => $this->cmid,
                    'type' => $type,
                    'streak' => 0,
                    'status' => 'active'
                ];
            }
            $record->state = $this->statekeys[$record->state];
            return $record;
        }


        public function get_streak_info() : object {
            $info = [];
            foreach ($this->types as $type) {
                $streak = $this->get_streak($type);
                $info[$type]['streak'] = $streak->streak;
                $info[$type]['status'] = $streak->status;
            }
            return (object) $info;
        }

        public function update_streak(string $type) : void {
            if (!in_array($type, $this->types)) {
                throw new invalid_parameter_exception("Invalid type for streak. Allowed types: " . implode(", ", $this->types));
            }
            global $DB;
            $record = $DB->get_record(
                'vocabcoach_streaks',
                ['userid' => $this->userid, 'cmid' => $this->cmid, 'type' => $type]
            );
            if (!$record) {
                $this->start_streak($type);
                return;
            }

            $record->streak = $this->compute_streak_number($record->streak, $record->lastactive);
            $record->lastactive = time();
            $DB->update_record('vocabcoach_streaks', $record);
        }

        function start_streak(string $type): void {
            global $DB;
            $record = new \stdClass();
            $record->userid = $this->userid;
            $record->cmid = $this->cmid;
            $record->type = $type;
            $record->streak = 0;
            $DB->insert_record('vocabcoach_streaks', $record);
        }

        function compute_streak_number($streak, $lastactive): int {
            if ($lastactive < strtotime("yesterday midnight")) {
                return 1;
            }

            if ($lastactive < strtotime("today midnight")) {
                return $streak + 1;
            }
            return $streak;
        }
}