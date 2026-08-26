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

import {Mode, MODES} from '../types';
import String from '@moodle/lms/core/String';

type CheckSettingsProps = {
    currentmode: Mode,
    modelabels: Record<Mode, string>
    onModeChange: (mode: Mode) => void
}

export default function CheckSettings({currentmode, modelabels, onModeChange}: CheckSettingsProps) {
    return (
        <div className="d-flex align-items-center ml-auto">
            <label htmlFor="checkmode-select" className="mr-2 ml-auto">
                <String identifier="checkmode" component="mod_vocabcoach" />
            </label>
            <select id="checkmode-select" className="custom-select w-auto" value={currentmode}
                    onChange={(e) => onModeChange(e.target.value as Mode)}>
                {MODES.map(mode =>
                    <option value={mode} key={mode}>{modelabels[mode]}</option>
                )}
            </select>
        </div>
    );
}
