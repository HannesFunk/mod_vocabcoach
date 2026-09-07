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

import {fetchOne} from '@moodle/lms/core/ajax';
import {Vocab} from "@moodle/lms/mod_vocabcoach/types";

export const updateVocab = (
    id: number,
    cmid: number,
    known: boolean
): Promise<boolean> => fetchOne<boolean>({
    methodname: 'mod_vocabcoach_update_vocab',
    args: {id, cmid, known}
});

export const addVocabsToList = (
    listid: number,
    cmid: number,
    vocabsToAdd: {front: string, back: string}[]
) => fetchOne<void>({
    methodname: 'mod_vocabcoach_add_vocabs_to_list',
    args: {
        cmid: cmid,
        listid: listid,
        vocabs: vocabsToAdd
    }
});

export const addVocabsToUser = (
    cmid: number,
    vocabsToAdd: {front: string, back: string}[]
) => fetchOne<void>({
    methodname: 'mod_vocabcoach_add_vocabs_to_user',
    args: {
        cmid: cmid,
        vocabs: vocabsToAdd
    }
});

export const deleteVocabFromUser = (
    cmid: number,
    vocabid: number,
) => fetchOne<void>({
    methodname: 'mod_vocabcoach_delete_vocab_from_user',
    args: {
        cmid: cmid,
        vocabid: vocabid,
    }
});

export const editUserVocab = (
    id: number,
    cmid: number,
    front: string,
    back: string,
) => fetchOne<Vocab>({
    methodname: 'mod_vocabcoach_edit_user_vocab',
    args: {vocabid: id, cmid, front, back},
});
