<?php
defined('MOODLE_INTERNAL') || die();
$addons = [
    'mod_vocabcoach' => [
        'handlers' => [
            'vocabcoach_module' => [
                'delegate' => 'CoreCourseModuleDelegate',
                'method'   => 'mobile_view',
                'displaydata' => ['icon' => 'mod/vocabcoach:icon'],
            ],
        ],
        'lang' => [
            ['pluginname', 'mod_vocabcoach'],
        ],
    ],
];
