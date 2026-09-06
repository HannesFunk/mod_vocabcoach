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

import {TypeButtonLabels, Vocab} from '../types';
import {useState} from "react";


type TypeAreaProps = {
    vocab: Vocab,
    onAnswer: (known:boolean) => void,
    buttonLabels: TypeButtonLabels
}

export default function TypeArea({vocab, onAnswer, buttonLabels}: TypeAreaProps) {
    const [revealed, setRevealed] = useState(false);
    const [typedInput, setTypedInput] = useState('');

    return (
        <>
            <div id="check-type-area">
                <label htmlFor="input-vocab-front"></label>
                <input type="text" name="input-vocab-front" id="input-vocab-front"
                       autoCapitalize="off" autoComplete="none" spellCheck="false"
                       value={typedInput}
                       onChange={(e) => setTypedInput(e.target.value)}
                       disabled={revealed}
                />
                {revealed ?
                    <div className="d-flex gap-2 justify-content-center">
                        <button className="btn btn-secondary"
                                onClick={() => onAnswer(false)}>
                            {buttonLabels.typeNo}
                        </button>
                        <button className="btn btn-secondary"
                                onClick={() => onAnswer(true)}>
                            {buttonLabels.typeOverride}
                        </button>
                    </div>
                    : <div className="d-flex gap-2 justify-content-center">
                        <button className="btn btn-secondary"
                                onClick={() => reveal(true)}>
                            {buttonLabels.typeCheck}
                        </button>
                        <button className="btn btn-secondary"
                                onClick={() => reveal(false)}>
                            {buttonLabels.typeReveal}
                        </button>
                    </div>
                }
            </div>
            <div id="check-box-back" className="check-box d-flex">
                <div id="check-back" className="check-label">
                    {vocab.back}
                </div>
            </div>
        </>
    );

    function reveal(checkInput: boolean) {
        if (checkInput && typedInput === vocab.front) {
            onAnswer(true);
            return;
        }
        setTypedInput(vocab.front);
        setRevealed(true);
    }

}