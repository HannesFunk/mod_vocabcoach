<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Mobile app support for VocabCoach module
 *
 * @package     mod_vocabcoach
 * @copyright   2023 J. Funk, johannesfunk@outlook.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$addons = [
    'mod_vocabcoach' => [
        'handlers' => [
            'vocabcoach' => [
                'displaydata' => [
                    'icon' => $CFG->wwwroot . '/mod/vocabcoach/pix/icon.png',
                    'class' => '',
                ],
                'delegate' => 'CoreCourseModuleDelegate',
                'method' => 'mobile_view',
                'offlinefunctions' => [],
                'styles' => [
                    'url' => $CFG->wwwroot . '/mod/vocabcoach/styles/mobile.css',
                    'version' => 2025110320
                ]
            ]
        ],
        'lang' => [
            ['pluginname', 'mod_vocabcoach'],
            ['mobile_main_title', 'mod_vocabcoach'],
            ['mobile_box_due', 'mod_vocabcoach'],
            ['mobile_check_start', 'mod_vocabcoach'],
            ['mobile_no_due_vocab', 'mod_vocabcoach'],
            ['mobile_progress_text', 'mod_vocabcoach'],
            ['mobile_check_title', 'mod_vocabcoach'],
            ['mobile_check_mode_buttons', 'mod_vocabcoach'],
            ['mobile_check_mode_type', 'mod_vocabcoach'],
            ['mobile_check_show_back', 'mod_vocabcoach'],
            ['mobile_check_known', 'mod_vocabcoach'],
            ['mobile_check_unknown', 'mod_vocabcoach'],
            ['mobile_check_type_answer', 'mod_vocabcoach'],
            ['mobile_check_answer', 'mod_vocabcoach'],
            ['mobile_summary_title', 'mod_vocabcoach'],
            ['mobile_summary_known', 'mod_vocabcoach'],
            ['mobile_summary_unknown', 'mod_vocabcoach'],
            ['mobile_finish_check', 'mod_vocabcoach']
        ]
    ]
];
