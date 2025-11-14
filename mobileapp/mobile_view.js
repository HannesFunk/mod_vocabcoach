// This file is part of Moodle - http://moodle.org/
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

/**
 * Mobile vocabulary checking functionality for VocabCoach module
 *
 * @copyright   2023 J. Funk, johannesfunk@outlook.com
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Add debug log to know when this module is loaded/executed
console.log('🔍 MOBILE_VIEW: JS script executed from file!');
console.log('🔍 Does this shit work?!');



function mobileStartCheck(cmid, stage) {
    console.log('🔍 MOBILE_VIEW: mobileStartCheck called with cmid:', cmid, 'stage:', stage);
    
    // Create a form to submit with POST to refresh the content with new parameters
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = window.location.pathname;
    
    // Add stage parameter
    const stageInput = document.createElement('input');
    stageInput.type = 'hidden';
    stageInput.name = 'stage';
    stageInput.value = stage;
    form.appendChild(stageInput);
    
    // Add action parameter
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = 'check';
    form.appendChild(actionInput);
    
    document.body.appendChild(form);
    form.submit();
}

// Make function globally accessible for onclick handlers
window.mobileStartCheck = mobileStartCheck;
console.log('🔍 MOBILE_VIEW: window.mobileStartCheck =', typeof window.mobileStartCheck);
