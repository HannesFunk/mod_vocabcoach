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

import {useLayoutEffect, useRef} from 'react';

/** Font size in pixels below which long words are allowed to overflow instead of shrinking further. */
const MIN_FONT_SIZE = 18;

/** Amount the font size is reduced by on each attempt, in pixels. */
const FONT_SIZE_STEP = 2;

type CheckLabelProps = {
    /** The word to show. */
    text: string,
    /** Element id, kept so the existing check.css rules still apply. */
    id: string,
    /** Whether the word is currently hidden from the learner. */
    concealed: boolean
};

export default function CheckLabel({text, id, concealed}: CheckLabelProps) {
    const labelref = useRef<HTMLDivElement>(null);

    useLayoutEffect(() => {
        const label = labelref.current;
        const box = label?.parentElement;
        if (!label || !box) {
            return undefined;
        }

        const fit = () => {
            // Always start from the size in the stylesheet, so a shorter word can grow back.
            label.style.fontSize = '';
            if (concealed) {
                return;
            }
            let size = parseFloat(getComputedStyle(label).fontSize);
            while (label.offsetHeight > box.clientHeight && size > MIN_FONT_SIZE) {
                size -= FONT_SIZE_STEP;
                label.style.fontSize = `${size}px`;
            }
        };

        fit();

        // The box height follows the viewport (25vh), so refit when it changes.
        const observer = new ResizeObserver(fit);
        observer.observe(box);
        return () => observer.disconnect();
    }, [text, concealed]);

    return (
        <div id={id}
             ref={labelref}
             className={`check-label ${concealed ? 'visually-hidden' : ''}`}>
            {text}
        </div>
    );
}
