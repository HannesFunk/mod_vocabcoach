<?php
namespace mod_vocabcoach\output;
defined('MOODLE_INTERNAL') || die();

class mobile {
    public static function mobile_view(array $args) : array {
        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => '<h1 class="text-center">Hallo, ballo!</h1>',
                ],
            ],
        ];
    }
}
