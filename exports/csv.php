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
 * Create CSV files.
 *
 * @package   mod_vocabcoach
 * @copyright 2023 onwards, Johannes Funk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Johannes Funk
 */

defined('MOODLE_INTERNAL') || die();

require(__DIR__ . '/../../../config.php');
require_login();
require("../classes/external/vocab_api.php");
use mod_vocabcoach\external\vocab_api;

if (!isset($_GET['mode'])) {
    die ("Wrong parameters.");
}

if ($_GET['mode'] == 'list') {
    $vocabapi = new vocab_api();
    $vocabarray = $vocabapi->get_list_vocabs($_GET['listid']);
} else {
    $vocabarray = [];
}

$stream = fopen('php://output', 'w');
if (!$stream) {
    die ("Can't open CSV-output.");
}

header("Content-Type:application/csv");
header("Content-Disposition:attachment;filename=vocab.csv");

foreach ($vocabarray as $vocab) {
    fputcsv($stream, [$vocab->front, $vocab->back, $vocab->third]);
}

fclose($stream);
