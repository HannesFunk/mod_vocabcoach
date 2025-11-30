<?php
// This file is part of Moodle - https://moodle.org/

defined('MOODLE_INTERNAL') || die();

$tasks = [
    [
        'classname' => 'mod_vocabcoach\task\send_due_notifications',
        'blocking' => 0,
        'minute' => '*/1',
        'hour' => '*', // every 3 minutes; adjust as needed or make configurable
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    ]
];
