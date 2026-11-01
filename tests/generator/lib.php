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
 * Generator for mod_vocabcoach.
 *
 * @package   mod_vocabcoach
 * @copyright 2026, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_vocabcoach_generator extends testing_module_generator {
    /**
     * Create a new instance of the vocabcoach activity.
     *
     * @param array|stdClass $record
     * @param array $options
     * @return stdClass activity record
     */
    public function create_instance($record = null, array $options = null) {
        $record = (object)(array)$record;

        $defaultsettings = [
            'name' => 'Test vocabcoach',
            'intro' => 'Test vocabcoach description',
            'introformat' => FORMAT_MOODLE,
            'move_undue' => false,
            'thirdactive' => false,
            'notifications_enabled' => true,
            'instructions' => '',
            'boxtime_1' => 1,
            'boxtime_2' => 2,
            'boxtime_3' => 5,
            'boxtime_4' => 10,
            'boxtime_5' => 30,
            'desc_front' => 'front',
            'desc_back' => 'back',
        ];

        foreach ($defaultsettings as $name => $value) {
            if (!isset($record->{$name})) {
                $record->{$name} = $value;
            }
        }

        return parent::create_instance($record, (array)$options);
    }

    /**
     * Create a streak record for testing.
     *
     * @param array $record
     * @return stdClass
     */
    public function create_streak($record = []) {
        global $DB;

        $defaults = [
            'userid' => 0,
            'cmid' => 0,
            'type' => 'login',
            'streak' => 1,
            'timemodified' => time(),
        ];

        $record = (object)array_merge($defaults, $record);
        $record->id = $DB->insert_record('vocabcoach_streaks', $record);

        return $record;
    }

    /**
     * Create a vocab item and place it in a user's box.
     *
     * Vocab items are deduplicated on their text and a user cannot hold the same item twice, so
     * calling this again with a front/back that already exists creates no second card and instead
     * moves the existing one to the given stage. Pass a distinct front/back for every card.
     *
     * @param array $record Requires userid and cmid. Optional: front, back, stage, lastchecked.
     *                      Omitting lastchecked leaves the card due for checking.
     * @return stdClass
     */
    public function create_vocab($record = []) {
        global $DB;

        $defaults = [
            'front' => 'front',
            'back' => 'back',
            'stage' => 1,
            'lastchecked' => null,
        ];

        $record = (object)array_merge($defaults, $record);

        $manager = new \mod_vocabcoach\vocab_manager();
        $vocab = $manager->get_or_create_vocab($record->front, $record->back);
        $manager->add_vocab_ids_to_user([$vocab->get('id')], $record->cmid, $record->userid);

        $record->id = $vocab->get('id');

        $data = $DB->get_record(
            'vocabcoach_vocabdata',
            ['vocabid' => $record->id, 'cmid' => $record->cmid, 'userid' => $record->userid],
            '*',
            MUST_EXIST
        );
        $data->stage = $record->stage;
        if ($record->lastchecked !== null) {
            $data->lastchecked = $record->lastchecked;
        }
        $DB->update_record('vocabcoach_vocabdata', $data);

        $record->dataid = $data->id;

        return $record;
    }

    /**
     * Create a vocabdata row whose vocabid points at no vocab item.
     *
     * This models the orphan rows that arise because Moodle does not enforce the declared
     * foreign key, and that box and leaderboard counts must ignore.
     *
     * @param array $record Requires userid and cmid. Optional: stage, lastchecked.
     * @return stdClass
     */
    public function create_orphan_vocabdata($record = []) {
        global $DB;

        $defaults = [
            'stage' => 1,
            'lastchecked' => 0,
        ];

        $record = (object)array_merge($defaults, $record);
        $record->vocabid = (int)$DB->get_field_sql('SELECT COALESCE(MAX(id), 0) FROM {vocabcoach_vocab}') + 1000;
        $record->id = $DB->insert_record('vocabcoach_vocabdata', $record);

        return $record;
    }
}
