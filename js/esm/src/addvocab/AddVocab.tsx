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

import {NotificationModule, Vocab, VocabDraft} from '../types';
import {useEffect, useState} from "react";
import MoodleString from "@moodle/lms/core/String";
import {VocabRow} from "./VocabRow";
import {addVocabsToList, addVocabsToUser} from "@moodle/lms/mod_vocabcoach/repository";
import {requireAsync} from "@moodle/lms/core/amd";
import {isMoodleAjaxError} from "@moodle/lms/core/ajax";
import Log from "@moodle/lms/core/log";

type AddVocabProps = {
    vocabs: Vocab[],
    cmid: number,
    listid: number | null,
    placeholders: Record<'front' | 'back', string>,
    vocabsfieldid: string
};
export default function AddVocab({vocabs, cmid, listid, placeholders, vocabsfieldid}: AddVocabProps) {
    const newRow = (): VocabDraft => ({key: crypto.randomUUID(), front: '', back: ''});

    const [items, setItems] = useState<VocabDraft[]>(() => {
        let vocabNew = vocabs.map(v => {
            return {front: v.front, back: v.back, key: crypto.randomUUID()};
        });
        return [...vocabNew, newRow()];
    });

    useEffect(() => {
        const field = document.getElementById(vocabsfieldid);
        if (field instanceof HTMLInputElement) {
            field.value = JSON.stringify(items.filter(
                item => (item.front !== "" || item.back !== ""))
                .map(({key, ...rest}) => rest)
            );
        } else {
            Log.error(`Hidden vocab field #${vocabsfieldid} not found`, 'mod_vocabcoach/AddVocab');
        }
    }, [items, vocabsfieldid]);

    return (
        <div id="rowsContainer">
        { items.map(
            (item, i) =>
                <VocabRow vocab={item}
                    placeholders={placeholders}
                    key={item.key}
                    onChange={(field, value) => handleVocabChange(item.key, field, value)} />
        ) }
        </div>
    );

    function handleVocabChange(key: string, field: 'front' | 'back', value: string) {
        setItems(prev => {
            const newItems = prev.map(item =>
                item.key === key ? {...item, [field]: value} : item);
            const lastRow = newItems[newItems.length - 1];
            return (lastRow.front === '' && lastRow.back === '') ? newItems : [...newItems, newRow()];
        });
    }
}