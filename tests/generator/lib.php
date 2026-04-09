<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

defined('MOODLE_INTERNAL') || die();

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
}
