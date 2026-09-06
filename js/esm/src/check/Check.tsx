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


import {useState} from 'react';
import CheckCard from './CheckCard';
import {ButtonLabels, Mode, NotificationModule, Outcome, Vocab} from "../types";
import CheckSettings from "./CheckSettings";
import {updateVocab} from "../repository";
import {requireAsync} from "@moodle/lms/core/amd";
import {isMoodleAjaxError} from "@moodle/lms/core/ajax";
import TypeArea from "@moodle/lms/mod_vocabcoach/check/TypeArea";
import ProgressDots from "@moodle/lms/mod_vocabcoach/check/ProgressDots";
import MoodleString from '@moodle/lms/core/String';

export type CheckProps = {
    items: Vocab[],
    cmid: number,
    modelabels: Record<Mode, string>,
    currentmode: Mode,
    buttonLabels: ButtonLabels
    fromList: boolean
}

export default function Check({items, cmid, modelabels, currentmode, buttonLabels, fromList}: CheckProps) {
    const [vocabs, setVocabs] = useState(items);
    const [mode, setMode] = useState(currentmode);
    const [outcomes, setOutcomes] = useState<Outcome[]>([]);
    const current = vocabs[0];
    let remaining = vocabs.length;

    if (remaining === 0) {
        const knownCount = outcomes.filter(o => o === 'known').length;
        return (
            <div className="check-summary">
                <MoodleString identifier="check_summary_result" component="mod_vocabcoach"
                    params={{known: knownCount, total: outcomes.length}}/>
            </div>
        );
    }

    return (
        <div id="check-container">
            <div id="check-header">
                <CheckSettings
                    currentmode={mode} modelabels={modelabels} onModeChange={handleModeChange} />
                <div className="text-end">{remaining} left</div>
            </div>
            <ProgressDots total={items.length} outcomes={outcomes} />
            {mode === 'type' ?
                <TypeArea vocab={current} onAnswer={handleAnswer} buttonLabels={buttonLabels}
                          key={`${current.id}-${mode}`} />
                : <CheckCard
                    key={`${current.id}-${mode}`}
                    vocab={current}
                    mode={mode}
                    buttonLabels={buttonLabels}
                    onAnswer={handleAnswer} />
            }
        </div>
    );

    async function handleAnswer(known: boolean) {
        setVocabs(prev => prev.slice(1));
        const outcome = known ? 'known' : 'unknown';
        setOutcomes((prev) => [...prev, outcome]);

        if (fromList) {
            return;
        }

        try {
            await updateVocab(current.id, cmid, known);
        } catch (err) {
            const Notification = await requireAsync<NotificationModule>('core/notification');
            if (isMoodleAjaxError(err)) {
                await Notification.exception(err);
            } else {
                await Notification.addNotification({
                    message: err instanceof Error ? err.message : String(err),
                    type: 'error'
                });
            }
        }
    }

    function handleModeChange(newMode: Mode) {
        setMode(newMode);
    }
}
