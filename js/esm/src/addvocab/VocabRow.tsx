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

import {Vocab} from '../types';
import {ChangeEventHandler} from "react";
import {VocabDraft} from "../types";

type VocabRowProps = {
    vocab: VocabDraft,
    placeholders: Record<'front' | 'back', string>,
    onChange: (field: 'front' | 'back', value: string) => void
}

export function VocabRow({vocab, placeholders, onChange}: VocabRowProps) {
    return (
      <div className="d-flex align-items-center gap-3 m-2">
          <input type="text" className="form-control" name="front[]"
                 value={vocab.front} placeholder={placeholders.front}
                 onChange={(e) => onChange('front', e.target.value)} />
          <input type="text" className="form-control" name="back[]"
                 value={vocab.back} placeholder={placeholders.back}
                 onChange={e => onChange('back', e.target.value)} />
      </div>
    );

}