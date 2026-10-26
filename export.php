<?php
// This file is part of Moodle - http://moodle.org
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

require(__DIR__ . '/../../config.php');

$cmid = required_param('id', PARAM_INT);
$listid = required_param('listid', PARAM_INT);
$dataformat = required_param('dataformat', PARAM_ALPHA);

[$course, $cm] = get_course_and_cm_from_cmid($cmid, 'vocabcoach');
require_login($course, false, $cm);
$context = \core\context\module::instance($cmid);
require_capability('mod/vocabcoach:view', $context);

$list = new \mod_vocabcoach\local\vocablist($listid);
if (!$list->can_view($USER->id)) {
    throw new \core\exception\access_denied_exception('nopermissiontoviewlist', 'mod_vocabcoach');
}

$instance = $DB->get_record('vocabcoach', ['id' => $cm->instance], '*', MUST_EXIST);
$headers = ['front' => $instance->desc_front, 'back' => $instance->desc_back];

$sql = "SELECT front, back FROM {vocabcoach_vocab} v
        JOIN {vocabcoach_list_vocabs} lv ON lv.vocabid = v.id 
        WHERE lv.listid = :listid";
$rs = $DB->get_recordset_sql($sql,['listid' => $listid]);
\core\dataformat::download_data(
    clean_filename($list->get('title')),
    $dataformat,
    $headers,
    $rs,
    fn($row) => ['front' => $row->front, 'back' => $row->back]
);
$rs->close();



