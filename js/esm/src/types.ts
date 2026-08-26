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


import {MoodleAjaxError} from "@moodle/lms/core/ajax";

export type Vocab = {
    id: number,
    front: string,
    back: string
}

export type VocabDraft = {
    key: string,
    front: string,
    back: string
}

export type NotificationModule = {
    addNotification: (n: {message: string, type?: string}) => Promise<void>;
    exception: (err: MoodleAjaxError | Error) => Promise<void>;
}


export const MODES = ['front', 'back', 'random', 'type'] as const;
export type Mode = (typeof MODES)[number];
