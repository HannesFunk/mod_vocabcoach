<?php

$functions = [
    'mod_vocabcoach_update_vocab' => [
        'classname' => 'mod_vocabcoach\external\check_vocab_api',
        'methodname' => 'update_vocab',
        'description' => 'Updates vocabulary and moves them to stage 1 or the next stage',
        'type' => 'write',
        'ajax' => true,
    ],
    'mod_vocabcoach_get_vocabs' => [
        'classname' => 'mod_vocabcoach\external\check_vocab_api',
        'methodname' => 'get_vocabs',
        'description' => 'Updates vocabulary and moves them to stage 1 or the next stage',
        'type' => 'write',
        'ajax' => true,
    ], 'mod_vocabcoach_get_lists' => [
        'classname' => 'mod_vocabcoach\external\manage_lists_api',
        'methodname' => 'get_lists',
        'description' => 'Return all the lists meeting certain (or no) search criteria.',
        'type' => 'write',
        'ajax' => true,
    ],  'mod_vocabcoach_get_user_vocabs' => [
        'classname' => 'mod_vocabcoach\external\check_vocab_api',
        'methodname' => 'get_user_vocabs',
        'description' => 'Retrieves all the vocabulary for a given user and stage.',
        'type' => 'write',
        'ajax' => true,
    ],  'mod_vocabcoach_get_list_vocabs' => [
        'classname' => 'mod_vocabcoach\external\check_vocab_api',
        'methodname' => 'get_list_vocabs',
        'description' => 'Retrieves vocabulary from a list with given listid.',
        'type' => 'write',
        'ajax' => true,
    ], 'mod_vocabcoach_delete_list' => [
        'classname' => 'mod_vocabcoach\external\manage_lists_api',
        'methodname' => 'delete_list',
        'description' => 'Remove a list with certain id.',
        'type' => 'write',
        'ajax' => true,
    ],
    'mod_vocabcoach_add_list_to_user' => [
        'classname' => 'mod_vocabcoach\external\manage_lists_api',
        'methodname' => 'add_list_to_user',
        'description' => 'Add all the new vocab from one list to the user db.',
        'type' => 'write',
        'ajax' => true,
    ],
    'mod_vocabcoach_distribute_list' => [
        'classname' => 'mod_vocabcoach\external\manage_lists_api',
        'methodname' => 'distribute_list',
        'description' => 'Add all the new vocab from one list to dbs owned by any student of the course.',
        'type' => 'write',
        'ajax' => true,
    ],
    'mod_vocabcoach_get_feedback_line' => [
        'classname' => 'mod_vocabcoach\external\feedback_api',
        'methodname' => 'get_feedback_line',
        'description' => 'Gets a feedback line, depending on the achievement.',
        'type' => 'write',
        'ajax' => true,
    ],
];