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

import {useEffect, useRef, useState} from 'react';
import type {Outcome} from '../types';

/** Horizontal distance between two dots, in pixels. Matches the dot size in check.css. */
const DOT_SPACING = 12;

/** Width kept free at the edge of the bar so the dot strip never touches it. */
const RESERVED_WIDTH = 50;

/** Modifier class used to colour a dot, per outcome. Deleted items advance the bar uncoloured. */
const OUTCOME_CLASSES: Record<Outcome, string> = {
    known: 'dot-green',
    unknown: 'dot-red',
    deleted: '',
};

type ProgressDotsProps = {
    total: number,
    outcomes: Outcome[]
};

export default function ProgressDots({total, outcomes}: ProgressDotsProps) {
    const barRef = useRef<HTMLDivElement>(null);
    const [barWidth, setBarWidth] = useState(0);

    useEffect(() => {
        const bar = barRef.current;
        if (!bar) {
            return undefined;
        }
        const observer = new ResizeObserver(([entry]) => setBarWidth(entry.contentRect.width));
        observer.observe(bar);
        return () => observer.disconnect();
    }, []);

    const everyNth = getEveryNth(total, barWidth);
    const step = DOT_SPACING / everyNth;

    return (
        <div id="progress-bar" className="mb-2" ref={barRef}>
            {Array.from({length: total}, (unused, index) => {
                const outcome = outcomes[index];
                const done = outcome !== undefined;
                return (
                    <div key={index}
                         className={index % everyNth === 0 ? '' : 'hidden'}
                         style={{transform: done
                             ? `translateX(${index * step}px)`
                             : `translateX(calc(100% - ${(total - index) * step}px))`}}>
                        <div className={`vocab-dot ${done ? OUTCOME_CLASSES[outcome] : 'unchecked'}`} />
                    </div>
                );
            })}
        </div>
    );
}

/**
 * Works out how many dots have to share one visible position so that the strip still fits.
 *
 * Returns 1 while the bar has not been measured yet, so the first render is never thinned out.
 *
 * @param total Number of items in the check.
 * @param barWidth Measured width of the progress bar, in pixels.
 */
function getEveryNth(total: number, barWidth: number): number {
    const available = barWidth - RESERVED_WIDTH;
    if (available <= 0) {
        return 1;
    }
    return Math.max(1, Math.ceil((total * DOT_SPACING) / available));
}
