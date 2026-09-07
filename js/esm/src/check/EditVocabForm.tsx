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

import {useId, useState} from 'react';
import type {FormEvent} from 'react';
import type {Vocab} from '../types';
import MoodleString from '@moodle/lms/core/String';

type EditVocabFormProps = {
    vocab: Vocab,
    onSave: (front: string, back: string) => void,
    onCancel: () => void,
    saving?: boolean
};

export default function EditVocabForm({vocab, onSave, onCancel, saving = false}: EditVocabFormProps) {
    const [front, setFront] = useState(vocab.front);
    const [back, setBack] = useState(vocab.back);
    const frontid = useId();
    const backid = useId();

    const isComplete = front.trim() !== '' && back.trim() !== '';

    return (
        <form className="check-edit-form" onSubmit={handleSubmit}>
            <h3 className="h5">
                <MoodleString identifier="edit_vocab_title" component="mod_vocabcoach" />
            </h3>

            <div className="mb-3">
                <label className="form-label" htmlFor={frontid}>
                    <MoodleString identifier="front" component="mod_vocabcoach" />
                </label>
                <input type="text" className="form-control" id={frontid}
                       value={front} disabled={saving} autoFocus
                       onChange={(e) => setFront(e.target.value)} />
            </div>

            <div className="mb-3">
                <label className="form-label" htmlFor={backid}>
                    <MoodleString identifier="back" component="mod_vocabcoach" />
                </label>
                <input type="text" className="form-control" id={backid}
                       value={back} disabled={saving}
                       onChange={(e) => setBack(e.target.value)} />
            </div>

            <div className="d-flex gap-2">
                <button type="submit" className="btn btn-primary" disabled={saving || !isComplete}>
                    <MoodleString identifier="save" />
                </button>
                <button type="button" className="btn btn-secondary" disabled={saving} onClick={onCancel}>
                    <MoodleString identifier="cancel" />
                </button>
            </div>
        </form>
    );

    /**
     * Hands the trimmed values to the parent, which owns the request and the vocab state.
     */
    function handleSubmit(event: FormEvent<HTMLFormElement>): void {
        event.preventDefault();
        if (!isComplete || saving) {
            return;
        }
        onSave(front.trim(), back.trim());
    }
}
