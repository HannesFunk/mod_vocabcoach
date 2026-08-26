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

class vocablist extends persistent {
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
            'cmid' => [
                'type' => PARAM_INT,
            ],
        ];
    }
}
