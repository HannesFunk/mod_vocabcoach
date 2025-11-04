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

import {updateVocabAJAX, logCheckedVocabsAJAX} from "./repository";

let vocabArrayJSON = null;
let knownCount = 0;
let unknownCount = 0;
let currentIndex = 0;
let mode = 'buttons';
let config = {};
let checkResults = [];

/**
 * Initialize mobile check functionality
 * @param {Object} configuration Configuration object
 */
const mobileCheckInit = (configuration) => {
    config = configuration;
    
    // Get vocabulary data from the template
    const vocabDataElement = document.getElementById('mobile-vocab-data');
    if (vocabDataElement) {
        try {
            vocabArrayJSON = JSON.parse(vocabDataElement.textContent);
        } catch (e) {
            // Failed to parse vocabulary data
            return;
        }
    }
    
    if (!vocabArrayJSON || vocabArrayJSON.length === 0) {
        // No vocabulary data available
        return;
    }
    
    // Shuffle vocabulary array
    vocabArrayJSON = shuffle(vocabArrayJSON);
    
    // Initialize the interface
    initProgressDots();
    addEventListeners();
    changeMode();
    
    // Make function globally available for template calls
    window.mobileCheckInit = mobileCheckInit;
};

/**
 * Initialize progress dots
 */
function initProgressDots() {
    const dotsContainer = document.getElementById('mobile-progress-dots');
    if (!dotsContainer || !vocabArrayJSON) {
        return;
    }
    
    dotsContainer.innerHTML = '';
    
    // Limit dots to show max 20 for performance
    const maxDots = Math.min(20, vocabArrayJSON.length);
    const showEvery = Math.ceil(vocabArrayJSON.length / maxDots);
    
    // I think this should one go up to maxDots!
    for (let i = 0; i < vocabArrayJSON.length; i += showEvery) {
        const dot = document.createElement('div');
        dot.className = 'mobile-progress-dot';
        dot.setAttribute('data-index', i);
        dotsContainer.appendChild(dot);
    }
}

/**
 * Add event listeners
 */
function addEventListeners() {
    // Mode selector
    const modeSelector = document.getElementById('mobile-check-mode');
    if (modeSelector) {
        modeSelector.addEventListener('change', () => {
            mode = modeSelector.value;
            changeMode();
        });
    }
    
    // Show back button
    const showBackBtn = document.getElementById('mobile-show-back');
    if (showBackBtn) {
        showBackBtn.addEventListener('click', showBack);
    }
    
    // Known/Unknown buttons
    const knownBtn = document.getElementById('mobile-btn-known');
    const unknownBtn = document.getElementById('mobile-btn-unknown');
    
    if (knownBtn) {
        knownBtn.addEventListener('click', () => checkDone(true));
    }
    
    if (unknownBtn) {
        unknownBtn.addEventListener('click', () => checkDone(false));
    }
    
    // Type mode elements
    const checkAnswerBtn = document.getElementById('mobile-check-answer');
    const vocabInput = document.getElementById('mobile-vocab-input');
    
    if (checkAnswerBtn) {
        checkAnswerBtn.addEventListener('click', checkTypedAnswer);
    }
    
    if (vocabInput) {
        vocabInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                checkTypedAnswer();
            }
        });
    }
    
    // Finish check button
    const finishBtn = document.getElementById('mobile-finish-check');
    if (finishBtn) {
        finishBtn.addEventListener('click', finishCheck);
    }
}

/**
 * Change check mode
 */
function changeMode() {
    const typeArea = document.getElementById('mobile-type-area');
    const checkButtons = document.getElementById('mobile-check-buttons');
    
    if (mode === 'type') {
        if (typeArea) {
            typeArea.style.display = 'block';
        }
        if (checkButtons) {
            checkButtons.style.display = 'none';
        }
    } else {
        if (typeArea) {
            typeArea.style.display = 'none';
        }
        if (checkButtons) {
            checkButtons.style.display = 'block';
        }
    }
    
    resetCheckFields();
    updateLabels();
}

/**
 * Reset check fields
 */
function resetCheckFields() {
    const frontElement = document.getElementById('mobile-vocab-front');
    const backElement = document.getElementById('mobile-vocab-back');
    const showBackBtn = document.getElementById('mobile-show-back');
    const resultButtons = document.getElementById('mobile-result-buttons');
    const vocabInput = document.getElementById('mobile-vocab-input');
    
    if (vocabArrayJSON && currentIndex < vocabArrayJSON.length) {
        const currentVocab = vocabArrayJSON[currentIndex];
        
        // Update front side
        if (frontElement) {
            frontElement.textContent = currentVocab.front || '';
            frontElement.style.display = 'flex';
        }
        
        // Hide back side initially
        if (backElement) {
            backElement.style.display = 'none';
        }
        
        // Reset buttons
        if (showBackBtn) {
            showBackBtn.style.display = 'block';
        }
        
        if (resultButtons) {
            resultButtons.style.display = 'none';
        }
        
        // Clear input
        if (vocabInput) {
            vocabInput.value = '';
        }
    }
}

/**
 * Update labels and progress
 */
function updateLabels() {
    const currentElement = document.getElementById('mobile-current-vocab');
    const totalElement = document.getElementById('mobile-total-vocab');
    
    if (currentElement) {
        currentElement.textContent = currentIndex + 1;
    }
    
    if (totalElement && vocabArrayJSON) {
        totalElement.textContent = vocabArrayJSON.length;
    }
    
    // Update progress dots
    updateProgressDots();
}

/**
 * Update progress dots
 */
function updateProgressDots() {
    const dots = document.querySelectorAll('.mobile-progress-dot');
    dots.forEach((dot) => {
        const dotIndex = parseInt(dot.getAttribute('data-index'));
        dot.classList.remove('active', 'correct', 'incorrect');
        
        if (dotIndex === currentIndex) {
            dot.classList.add('active');
        } else if (dotIndex < currentIndex) {
            // Find result for this index
            const result = checkResults.find(r => r.index === dotIndex);
            if (result) {
                dot.classList.add(result.known ? 'correct' : 'incorrect');
            }
        }
    });
}

/**
 * Show back side of vocabulary
 */
function showBack() {
    const frontElement = document.getElementById('mobile-vocab-front');
    const backElement = document.getElementById('mobile-vocab-back');
    const showBackBtn = document.getElementById('mobile-show-back');
    const resultButtons = document.getElementById('mobile-result-buttons');
    
    if (vocabArrayJSON && currentIndex < vocabArrayJSON.length) {
        const currentVocab = vocabArrayJSON[currentIndex];
        
        if (backElement) {
            backElement.textContent = currentVocab.back || '';
            backElement.style.display = 'flex';
        }
        
        if (frontElement) {
            frontElement.style.display = 'none';
        }
    }
    
    if (showBackBtn) {
        showBackBtn.style.display = 'none';
    }
    
    if (resultButtons) {
        resultButtons.style.display = 'flex';
    }
}

/**
 * Check typed answer
 */
function checkTypedAnswer() {
    const vocabInput = document.getElementById('mobile-vocab-input');
    const backElement = document.getElementById('mobile-vocab-back');
    
    if (!vocabInput || !vocabArrayJSON || currentIndex >= vocabArrayJSON.length) {
        return;
    }
    
    const userAnswer = vocabInput.value.trim().toLowerCase();
    const correctAnswer = vocabArrayJSON[currentIndex].back.toLowerCase();
    
    const isCorrect = cleanString(userAnswer) === cleanString(correctAnswer);
    
    // Show the correct answer
    if (backElement) {
        backElement.innerHTML = `
            <div>
                <div style="margin-bottom: 8px; color: ${isCorrect ? '#28a745' : '#dc3545'};">
                    ${isCorrect ? '✓ Correct!' : '✗ Incorrect'}
                </div>
                <div style="font-size: 20px;">${vocabArrayJSON[currentIndex].back}</div>
            </div>
        `;
        backElement.style.display = 'flex';
    }
    
    // Auto-advance after a short delay
    setTimeout(() => {
        checkDone(isCorrect);
    }, 1500);
}

/**
 * Mark vocabulary as done
 * @param {boolean} known Whether the vocabulary was known
 */
function checkDone(known) {
    if (!vocabArrayJSON || currentIndex >= vocabArrayJSON.length) {
        return;
    }
    
    const vocabId = vocabArrayJSON[currentIndex].dataid;
    
    // Store result
    checkResults.push({
        index: currentIndex,
        vocabId: vocabId,
        known: known
    });
    
    // Update counts
    if (known) {
        knownCount++;
    } else {
        unknownCount++;
    }
    
    // Update vocabulary on server
    updateVocabAJAX(vocabId, config.userid, known).then(() => {
        // Vocabulary updated successfully
    }).catch(() => {
        // Failed to update vocabulary
    });
    
    // Move to next vocabulary or show summary
    currentIndex++;
    
    if (currentIndex >= vocabArrayJSON.length) {
        showSummary();
    } else {
        resetCheckFields();
        updateLabels();
    }
}

/**
 * Show check summary
 */
function showSummary() {
    const checkArea = document.getElementById('mobile-check-area');
    const summaryArea = document.getElementById('mobile-check-summary');
    const knownCountElement = document.getElementById('mobile-known-count');
    const unknownCountElement = document.getElementById('mobile-unknown-count');
    
    if (summaryArea) {
        summaryArea.style.display = 'block';
    }
    
    if (knownCountElement) {
        knownCountElement.textContent = knownCount;
    }
    
    if (unknownCountElement) {
        unknownCountElement.textContent = unknownCount;
    }
    
    // Hide other elements
    const elementsToHide = [
        'mobile-progress-dots',
        'mobile-vocab-card', 
        'mobile-mode-selector',
        'mobile-type-area',
        'mobile-check-buttons'
    ];
    
    elementsToHide.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.style.display = 'none';
        }
    });
    
    // Log the results
    const details = JSON.stringify({
        stage: config.stage,
        total: vocabArrayJSON.length,
        known: knownCount,
        unknown: unknownCount,
        mode: mode
    });
    
    logCheckedVocabsAJAX(config.userid, config.cmid, details).then(() => {
        // Check results logged successfully
    }).catch(() => {
        // Failed to log check results
    });
}

/**
 * Finish check and go back
 */
function finishCheck() {
    // Navigate back to main view
    if (window.mobileGoBack) {
        window.mobileGoBack();
    } else {
        // Fallback
        if (window.history && window.history.length > 1) {
            window.history.back();
        }
    }
}

/**
 * Shuffle array
 * @param {Array} array Array to shuffle
 * @returns {Array} Shuffled array
 */
function shuffle(array) {
    const shuffled = [...array];
    for (let i = shuffled.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
    }
    return shuffled;
}

/**
 * Clean string for comparison
 * @param {string} input Input string
 * @returns {string} Cleaned string
 */
function cleanString(input) {
    return input
        .toLowerCase()
        .replace(/[.,;:!?()'"]/g, '')
        .replace(/\s+/g, ' ')
        .trim();
}

// Export for global use
export default {
    mobileCheckInit
};