<?php
// This file is part of Moodle - https://moodle.org/

defined('MOODLE_INTERNAL') || die();

// Define message providers for this plugin.
// See: https://docs.moodle.org/dev/Messaging_2.0#Message_providers

$messageproviders = [
    // Notification to users when they have due vocab items.
    'due_notification' => [
        // Optional: restrict by capability. Adjust if a different capability makes sense.
        // 'capability' => 'mod/vocabcoach:view',

        // Default delivery settings: enable email by default, popup off.
        'defaults' => [
            'email' => 1,
            'popup' => 0,
        ],
    ],
];

