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

import {Vocab} from '../types';
import String from '@moodle/lms/core/String';
import {getString} from "@moodle/lms/core/stringUtils";
import {use} from "react";

type VocabActionProps = {
    onDelete: () => void,
    onEdit: () => void,
}

export default function VocabAction({onDelete, onEdit}: VocabActionProps) {

    const showActions = use(getString('actions_show_actions', 'mod_vocabcoach'));
    const editString = use(getString('edit', 'core'));
    const deleteString = use(getString('delete', 'core'));

    return (
        <div className="dropdown">
            <a href="#" className="btn btn-link btn-icon colour-primary text-decoration-none d-flex ml-auto"
               role="button" data-bs-toggle="dropdown" data-bs-boundary="window" aria-haspopup="true" tabIndex={0}
               aria-label={showActions}
               aria-expanded="false">
                <i className="fa fa-ellipsis-h text-dark  py-2" aria-hidden="true"></i>
            </a>
            <div className="dropdown-menu dropdown-menu-right"
                 data-rel="menu-content"
                 role="menu">
                <a className="dropdown-item menu-action"
                    role="menuitem"
                    tabIndex={-1}
                    title={editString}
                    href="#"
                    onClick={onEdit}>
                    <i className="fa fa-edit  pr-2" aria-hidden="true"></i>
                    <span className="menu-action-text">{editString}</span>
                </a>
                <a className="dropdown-item menu-action"
                    role="menuitem"
                    tabIndex={-1}
                    title={deleteString}
                    href="#"
                    onClick={onDelete}>
                    <i className="fa fa-trash pr-2" aria-hidden="true"></i>
                    <span className="menu-action-text">{deleteString}</span>
                </a>
            </div>
        </div>
    );
}
