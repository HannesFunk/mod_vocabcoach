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
import {ButtonLabels, Mode, NotificationModule, Vocab} from "../types";
import CheckSettings from "./CheckSettings";
import TypeArea from "@moodle/lms/mod_vocabcoach/check/TypeArea";
import ProgressDots from "@moodle/lms/mod_vocabcoach/check/ProgressDots";
import MoodleString from '@moodle/lms/core/String';
import VocabAction from "@moodle/lms/mod_vocabcoach/check/VocabActions";
import {deleteVocabFromUser, editUserVocab, updateVocab} from "@moodle/lms/mod_vocabcoach/repository";
import {requireAsync} from "@moodle/lms/core/amd";
import {isMoodleAjaxError} from "@moodle/lms/core/ajax";
import {getString} from "@moodle/lms/core/stringUtils";
import EditVocabForm from "@moodle/lms/mod_vocabcoach/check/EditVocabForm";

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
    const [outcomes, setOutcomes] = useState<boolean[]>([]);
    const [editing, setEditing] = useState(false);

    const current = vocabs[0];
    let remaining = vocabs.length;

    if (remaining === 0) {
        const knownCount = outcomes.filter(o => o).length;
        return (
            <div className="check-summary">
                <MoodleString identifier="check_summary_result" component="mod_vocabcoach"
                    params={{known: knownCount, total: outcomes.length}}/>
            </div>
        );
    }

    return (
        <div id="check-container">
            <div id="check-header" className="d-flex justify-content-between align-items-center mb-2">
                <CheckSettings
                    currentmode={mode} modelabels={modelabels} onModeChange={handleModeChange} />
                <div>{remaining} left</div>
            </div>
            <ProgressDots total={items.length} outcomes={outcomes} />
            {fromList
                ? ''
                : <VocabAction onDelete={() => deleteVocab(current)} onEdit={() => setEditing(true)} />}
            {editing
                ? <EditVocabForm vocab={current} onSave={handleSaveEdit} onCancel={() => setEditing(false)} />
                : (mode === 'type' ?
                    <TypeArea vocab={current} onAnswer={handleAnswer} buttonLabels={buttonLabels}
                              key={`${current.id}-${mode}`} />
                    : <CheckCard
                        key={`${current.id}-${mode}`}
                        vocab={current}
                        mode={mode}
                        buttonLabels={buttonLabels}
                        onAnswer={handleAnswer} />
                )
            }
        </div>
    );

    async function handleAnswer(known: boolean) {
        setVocabs(prev => prev.slice(1));
        setOutcomes((prev) => [...prev, known]);

        if (fromList) {
            return;
        }

        try {
            await updateVocab(current.id, cmid, known);
        } catch (err) {
            await notifyError(err);
        }
    }

    async function deleteVocab(vocab: Vocab) {
        const Notification = await requireAsync<NotificationModule>('core/notification');
        try {
            await Notification.deleteCancelPromise(
                getString('confirm', 'core'),
                getString('confirm_delete_vocab', 'mod_vocabcoach'),
                getString('delete', 'core')
            );
        } catch {
            return;
        }

        try {
            await deleteVocabFromUser(cmid, vocab.id);
            setVocabs(prev => prev.filter(v => v !== vocab));
        } catch (err) {
            await notifyError(err);
        }
    }


    function handleModeChange(newMode: Mode) {
        setMode(newMode);
    }

    async function notifyError(err: unknown) {
        const Notification = await requireAsync<NotificationModule>('core/notification');
        if (isMoodleAjaxError(err)) {
            await Notification.exception(err);
            return;
        } else {
            await Notification.addNotification({
                message: err instanceof Error ? err.message : String(err),
                type: 'error'
            });
        }
    }

    async function handleSaveEdit(front: string, back: string) {
        try {
            const result = await editUserVocab(current.id, cmid, front, back);
            setVocabs(prev => [{id: result.id, front: result.front, back: result.back}, ...prev.slice(1)]);
            setEditing(false);
        } catch (err) {
            await notifyError(err);
        }


    }
}
