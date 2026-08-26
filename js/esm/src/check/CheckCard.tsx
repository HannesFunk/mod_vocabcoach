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


import type {Vocab, Mode} from '../types';
import {useState} from "react";

type CheckCardProps = {
    vocab: Vocab,
    mode: Mode,
    onAnswer: (known: boolean) => void,
    buttonLabels: Record<'yes' | 'no', string>
};

export default function CheckCard({vocab, mode, onAnswer, buttonLabels}: CheckCardProps) {
    const [revealed, setRevealed] = useState(false);

    const [concealed] = useState<'front' | 'back'>(
        () => {
            if (mode === 'type') {
                return 'front';
            } else if (mode === 'random') {
                return Math.random() < 0.5 ? 'front' : 'back';
            } else {
                return mode;
            }
        }
    );

    return (
        <>
            <div id="check-area">
                <div id="check-box-front"
                     className="check-box d-flex"
                     onClick={() => setRevealed(true)}>
                    <div id="check-front"
                         className={`check-label ${(concealed === 'front' && !revealed) ? 'visually-hidden' : ''}`}>
                        {vocab.front}
                    </div>
                </div>
                <div
                    id="check-box-back"
                    className="check-box d-flex"
                    onClick={() => setRevealed(true)}>
                    <div id="check-back"
                        className={`check-label ${(concealed === 'back' && !revealed) ? 'visually-hidden' : ''}`}>
                        {vocab.back}
                    </div>
                </div>
            </div>

        <div id="check-buttons">
            <button id="check-button-no"
                className="btn btn-secondary"
                onClick={() => onAnswer(false)}>
                {buttonLabels.no}
            </button>
            <button id="check-button-no"
                className="btn btn-secondary"
                onClick={() => onAnswer(true)}>
                {buttonLabels.yes}
            </button>
        </div>
    </>
    );
}
