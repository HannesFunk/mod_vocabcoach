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

namespace mod_vocabcoach;

use core\exception\moodle_exception;
use mod_vocabcoach\vocablist;
use \core\context\module;

class listmanager {
    private vocablist $list;
    private module $context;

    public function __construct (int $listid, int $cmid) {
        $list = new vocablist($listid);
        $context = module::instance($cmid);

        if ($list->get('cmid') !== $cmid) {
            throw new moodle_exception('listnotinactivity', 'modvocabcoach');
        }

        $this->list = $list;
        $this->context = $context;
    }

    public function get_vocabs(): array {
        global $DB;
        $query = "SELECT vocab.id AS id, front, back FROM {vocabcoach_vocab} vocab
            JOIN {vocabcoach_list_contains} lc ON lc.vocabid = vocab.id 
            WHERE lc.listid = :listid";
        $params = ['listid' => $this->list->get('id')];

        $result = $DB->get_records_sql($query, $params);
        return array_map(
            fn($item) => [
                'id' => (int)$item->id,
                'front' => $item->front,
                'back' => $item->back,
            ],
            array_values($result)
        );
    }

    public function get_list(): vocablist {
        return $this->list;
    }

    public function set_vocabs(array $vocabs): void {
        global $DB;
        $listid = $this->list->get('id');

        $idsfromform = [];
        $vm = new vocab_manager();
        foreach ($vocabs as $vocab) {
            $voc = $vm->get_or_create_vocab($vocab['front'], $vocab['back']);
            $idsfromform[] = (int) $voc->get('id');
        }

        $result = $DB->get_records('vocabcoach_list_contains', ['listid' => $listid]);
        $current = array_map(fn($record) => intval($record->vocabid), $result);

        $toadd = array_diff($idsfromform, $current);
        $toremove = array_diff($current, $idsfromform);

        if ($toadd) {
            $rows = array_map(
                fn($vocabid) => ['listid' => $listid, 'vocabid' => $vocabid],
                $toadd
            );
            $DB->insert_records('vocabcoach_list_contains', $rows);
        }
        if ($toremove) {
            [$insql, $params] = $DB->get_in_or_equal($toremove, SQL_PARAMS_NAMED);
            $params['listid'] = $listid;
            $DB->delete_records_select(
                'vocabcoach_list_contains',
                "listid = :listid AND vocabid $insql",
                $params
            );
        }
    }
}