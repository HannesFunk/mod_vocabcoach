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
use core\persistent;

class vocablist_old extends persistent {
    const string TABLE = "vocabcoach_lists";

    protected static function define_properties() {
        return [
            'title' => [
                'type' => PARAM_TEXT
            ],
            'year' => [
                'type' => PARAM_INT,
            ],
            'book' => [
                'type' => PARAM_TEXT,
            ],
            'unit' => [
                'type' => PARAM_TEXT,
            ],
            'createdby' => [
                'type' => PARAM_INT,
            ],
        ];
    }

    public function __construct(int $listid, int $cmid) {
        global $DB;
        $this->listid = $listid;

        $list = $DB->get_record('vocabcoach_lists', ['id' => $this->listid], 'cmid', MUST_EXIST);
        if ((int) $list->cmid !== $cmid) {
            throw new moodle_exception("Querried list does not belong to this activity.");
        }

    }

    public function get_vocabs(): array {
        global $DB;
        $query = "SELECT vocab.id AS id, front, back FROM {vocabcoach_vocab} vocab
            JOIN {vocabcoach_list_contains} lc ON lc.vocabid = vocab.id 
            WHERE lc.listid = :listid";
        $params = ['listid' => $this->listid];

        $result = $DB->get_records_sql($query, $params);
        return array_map(
            fn($item) => [
                'id' => (int) $item->id,
                'front' => $item->front,
                'back' => $item->back,
            ],
            array_values($result)
        );
    }

    public function get_title(): string {
        global $DB;
        $list = $DB->get_record('vocabcoach_lists', ['id' => $this->listid], 'title', MUST_EXIST);
        return $list->title;
    }

    public function update($vocabs): void {
        global $DB, $USER;

        $DB->delete_records(
            'vocabcoach_list_contains',
                ['cmid' => $this->cmid, 'listid' => $this->listid]
        );

        $manager = new vocab_manager($USER->id);
        foreach ($vocabs as $vocab) {
            $manager->add_vocab_to_list($d)
        }
    }
}




































