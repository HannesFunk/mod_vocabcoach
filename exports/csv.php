<?php

require(__DIR__ . '/../../../config.php');
require("../classes/external/check_vocab_api.php");
use mod_vocabcoach\external\check_vocab_api;

if (!isset($_GET['mode'])) {
    die ("Wrong parameters.");
}

if ($_GET['mode'] == 'list') {
    $vAPI = new check_vocab_api();
    $vocabarray = $vAPI->get_list_vocabs($_GET['listid']);
} else {
    $vocabarray = [];
}

$stream = fopen('php://output', 'w') or die ("Can't open php-output.");

header("Content-Type:application/csv");
header("Content-Disposition:attachment;filename=vocab.csv");

foreach($vocabarray as $vocab) {
    fputcsv($stream, [$vocab->front, $vocab->back, $vocab->third]);
}

fclose($stream);
