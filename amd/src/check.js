import {getBoxArrayAJAX, getFeedbackLineAJAX, getListArrayAJAX, logCheckedVocabsAJAX, updateVocabAJAX} from "./repository";
import mustache from 'core/mustache';
import {showElement, showElements} from "./general";

let vocabArrayJSON = null;
let knownCount = 0;
let unknownCount = 0;
let mode = '';
let config = {};

export const init = (configuration) => {
    config = JSON.parse(configuration);
    config.userid = parseInt(config.userid);

    mode = document.querySelector(Selectors.formElements.mode).value;

    getVocabArray(config)
        .then(() => {
            initDots();
            changeMode();
        });

    addListeners();

};

// Unified function to fetch vocab data based on config.source and set vocabArrayJSON.
function getVocabArray(cfg) {
    if (cfg.source === 'list') {
        return getListArrayAJAX(cfg.listid).then(response => {
            vocabArrayJSON = response;
        });
    } else if (cfg.source === 'user') {
        return getBoxArrayAJAX(cfg.userid, cfg.cmid, cfg.stage, cfg.force).then(response => {
            vocabArrayJSON = response;
        });
    }
    // Default: resolve immediately if no known source.
    return Promise.resolve();
}

function addListeners() {
    document.addEventListener('click', e => {
        if (e.target.closest(Selectors.actions.checkTypedVocab)) {
            checkTypedVocab(config.userid);
        } else if (e.target.closest(Selectors.actions.revealCard) && mode !== 'type') {
            const trigger = e.target.closest(Selectors.actions.revealCard);
            const label = trigger.getElementsByClassName('vc-check-label')[0];
            showElement(label, true);
            // showElement('check-third', config.thirdActive);
        } else if (e.target.closest(Selectors.actions.updateVocab)) {
            checkDone(vocabArrayJSON[0].dataid, e.target.getAttribute('data-vocabcoach-known') === 'true');
        } else if (e.target.closest(Selectors.actions.endCheck)) {
            endCheck();
        } else if (e.target.closest(Selectors.actions.revealTypedVocab)) {
            document.getElementById('input-vocab-front').value = vocabArrayJSON[0].front;
            document.getElementById('input-vocab-front').disabled = true;

            showElements(['button-typed-vocab-next', 'button-typed-vocab-override'], true);
            showElement('check-third', config.thirdActive);
            showElements(['button-typed-vocab-check', 'button-typed-vocab-reveal'], false);
        } else if (e.target.closest(Selectors.actions.typedVocabOverride)) {
            checkDone(vocabArrayJSON[0].dataid, true);
        } else if (e.target.closest(Selectors.actions.typedVocabUnknown)) {
            checkDone(vocabArrayJSON[0].dataid, false);
        }
    });

    document.addEventListener('change', (e) => {
        if (e.target.closest(Selectors.formElements.mode)) {
            changeMode();
        }
    });

    document.addEventListener('change', e => {
        if (e.target.closest(Selectors.formElements.typedVocab)) {
            document.getElementById('input-vocab-front').classList.remove('wrong');
        }
    });

    document.addEventListener('keyup', e => {
        if (e.target.closest(Selectors.formElements.typedVocab) &&
            e.key === 'Enter') {
                checkTypedVocab(config.userid);
        }
    });
}

const Selectors = {
    actions: {
        revealCard: '[data-action="mod_vocabcoach/reveal-card"]',
        updateVocab: '[data-action="mod_vocabcoach/update-vocab"]',
        endCheck: '[data-action="mod_vocabcoach/end-check"]',
        modeChanged: '[data-action="mod-vocabcoach/change-mode"]',
        checkTypedVocab: '[data-action="mod-vocabcoach/typed-vocab-check"]',
        revealTypedVocab: '[data-action="mod-vocabcoach/typed-vocab-reveal"]',
        typedVocabUnknown: '[data-action="mod-vocabcoach/typed-vocab-unknown"]',
        typedVocabOverride: '[data-action="mod-vocabcoach/typed-vocab-override"]',
    },
    formElements: {
        mode: '[id="checkmode-select"]',
        modeEmpty: '[value="empty"]',
        typedVocab: '[id="input-vocab-front"]',
    },
};

function initDots() {
    const progressBar = document.getElementById('progress-bar');
    const totalVocab = vocabArrayJSON.length;
    const greyDotContainer = document.createElement('div');
    const greyDot = document.createElement('div');
    greyDot.classList.add('vocab-dot');
    greyDot.classList.add('unchecked');
    greyDotContainer.appendChild(greyDot);

    for (let i = 0; i < totalVocab; i++) {
        let newDot = greyDotContainer.cloneNode(true);
        newDot.style.transform = 'translateX(calc(100% - ' + (i+1)*12 + 'px))';
        newDot.setAttribute('data-new-shift', (totalVocab-i)*12);
        progressBar.appendChild(newDot);
    }
}

export function changeMode() {
    mode = document.getElementById('checkmode-select').value;
    vocabArrayJSON = shuffle(vocabArrayJSON);
    const checkAreaElem = document.getElementById('check-area');
    startAnimation(checkAreaElem, 'animation-slide-out').then(
        () => {
            if (mode === 'type') {
                showElements(['check-box-front', 'check-buttons'], false);
                showElements(['check-back', 'check-type-area'], true);
                document.getElementById('input-vocab-front').value = '';
                showElement(document.getElementsByClassName('instruction-front-back-random')[0], false);
            } else {
                showElements(['check-buttons', 'check-box-front'], true);
                showElements(['check-type-area'], false);
                showElement(document.getElementsByClassName('instruction-front-back-random')[0], true);
            }
            resetCheckFields();
            updateLabels();
            startAnimation(checkAreaElem, 'animation-slide-in').then(null);
        });
}

function checkTypedVocab () {
    const typed = cleanString(document.getElementById('input-vocab-front').value);
    const correct = cleanString(vocabArrayJSON[0].front);

    if (typed === correct && !config.force) {
        updateVocabAJAX(vocabArrayJSON[0].dataid, config.userid, true).then(
            () => {
                updateCount(true);
                showNext();
            }
        );
    } else if (typed === correct) {
        updateCount(true);
        showNext();
    } else {
        document.getElementById('input-vocab-front').classList.add('wrong');
    }
}

function showNext(removeShown = true) {
    if (removeShown) {
        vocabArrayJSON.splice(0, 1);
    }

    const numberRemaining = vocabArrayJSON.length;
    document.getElementsByClassName('check-number-remaining')[0].innerHTML = 'Noch ' + numberRemaining +
        ' Vokabel' + (numberRemaining === 1 ? '' : 'n');

    if (numberRemaining === 0) {
        showSummary();
        return;
    }

    const checkAreaElem = document.getElementById('check-area');
    startAnimation(checkAreaElem, 'animation-slide-out').then(
        () => {
            resetCheckFields();
            updateLabels();
            startAnimation(checkAreaElem, 'animation-slide-in').then(null);
        }
    );
}

function updateLabels () {
    const frontBox = document.getElementById('check-front');
    const backBox = document.getElementById('check-back');
    frontBox.innerHTML = vocabArrayJSON[0].front;
    backBox.innerHTML = vocabArrayJSON[0].back;

    const thirdBox = document.getElementById('check-third');
    if (thirdBox !== null) {
        thirdBox.innerHTML = vocabArrayJSON[0].third;
    }

    [frontBox, backBox].forEach(
        elem => adjustFontSizeToBoxHeight(elem)
    );

    document.getElementById('check-container').setAttribute('data-vocab-data-id', vocabArrayJSON[0].dataid);
}

function adjustFontSizeToBoxHeight (elem) {
    const parentHeight = elem.parentNode.offsetHeight;
    const elemDisplayOld = elem.style.display;
    elem.style.display = 'block';
    elem.style.fontSize = "";

    while (elem.offsetHeight > parentHeight) {
        let fontSize = getComputedStyle(elem).getPropertyValue('font-size');
        fontSize = parseFloat(fontSize);

        if (fontSize <= 18) {
            break;
        }

        elem.style.fontSize = (fontSize - 2) + 'px';
    }

    elem.style.display = elemDisplayOld;
}

function resetCheckFields() {
    showElement('check-third', false);
    switch (mode) {
        case 'random': {
            const random = Math.floor(Math.random() * 2);
            showElement('check-front', random === 1);
            showElement('check-back', random === 0);
            break;
        }
        case 'front':
        case 'back':
            showElement('check-front', mode === 'back');
            showElement('check-back', mode === 'front');
            break;

        case 'type':
            showElements(['button-typed-vocab-next', 'button-typed-vocab-override'], false);
            showElements(['button-typed-vocab-reveal', 'button-typed-vocab-check'], true);

            document.getElementById('input-vocab-front').value = '';
            document.getElementById('input-vocab-front').disabled = false;
            break;
    }
}

function endCheck() {
    location.href = '../../mod/vocabcoach/view.php?id=' + config.cmid;
}

function showSummary() {
    let templateData = null;
    let template = null;
    const getMsg = getFeedbackLineAJAX(getSummaryAchievement()).then(
        (result) => {
            templateData = {
                known: knownCount,
                total: knownCount + unknownCount,
                message: result.line,
                thirdActive: config.thirdActive,
            };
        }
    );

    const getTemplate = fetch('../../mod/vocabcoach/templates/check_summary.mustache').then(
        (res) => {
            return res.text();
        }
    ).then(
        (text) => {template = text; }
    );

    const logDetails = {
        total: knownCount + unknownCount,
        known: knownCount,
        force: config.force,
        mode: config.source
    };
    if (config.source === 'user') {
        logDetails.stage = config.stage;
    }
    const logNumber = logCheckedVocabsAJAX(config.userid, config.cmid, JSON.stringify(logDetails));

    Promise.all([getMsg, getTemplate, logNumber]).then(() => {
        const summaryContainer = document.getElementsByClassName('check-summary')[0];
        summaryContainer.innerHTML = mustache.render(template, templateData);
        showElement(summaryContainer, true);
        showElements(['check-box-front', 'check-box-back', 'check-type-area', 'check-box-third', 'check-buttons'], false);
        const instructionElement = document.getElementsByClassName('instruction-front-back-random')[0];
        instructionElement.innerHTML = "Klicke in das Feld, um die Abfrage zu beenden.";
        showElement(instructionElement, true);
    });
}

function getSummaryAchievement() {
    const ratio = knownCount/(unknownCount + knownCount);

    if (ratio > 0.9) {
        return 5;
    }
    if (ratio > 0.7) {
        return 3;
    }
    if (ratio > 0.5) {
        return 2;
    }
    if (ratio > 0.3) {
        return 1;
    }
    return 0;
}

function checkDone(vocabId, known) {
    if (config.source === 'list' || config.force) {
        updateCount(known);
        showNext();
    }
    else {
        updateVocabAJAX(vocabId, config.userid, known).then(
            () => {
                updateCount(known);
                showNext();
            }
        );
    }
    resetCheckFields();
}

function updateCount(known) {
    if (known) {
        knownCount++;
    } else {
        unknownCount++;
    }

    let bullets = document.querySelectorAll('.vocab-dot.unchecked');
    let bullet = bullets[bullets.length - 1];
    const newShift = bullet.parentNode.getAttribute('data-new-shift');
    bullet.parentNode.style.transform = 'translateX(' + newShift + 'px)';
    bullet.classList.add(known ? 'dot-green' : 'dot-red');
    bullet.classList.remove('unchecked');
}

function shuffle(array) {
    for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
    return array;
}

let startAnimation = (el, animation) => {
    return new Promise(resolve => {
        const listener = () => {
            el.removeEventListener('animationend', listener);
            el.classList.remove(animation);
            resolve();
        };
        el.addEventListener('animationend', listener);
        el.classList.add(animation);
    });
};

function cleanString (input) {
    const replacements = [
        {'search': /\(/g, 'replace': ''},
        {'search': /\)/g, 'replace': ''},
        {'search': /\./g, 'replace': ''},
        {'search': /, /g, 'replace': ','},
        {'search': / ,/g, 'replace': ','},
        {'search': /something/g, 'replace': 'sth'},
        {'search': / smt /g, 'replace': 'sth'},
        {'search': / somebody /g, 'replace': 'sb'},
        {'search': / someone /g, 'replace': 'sb'},
        {'search': / smb /g, 'replace': 'sb'},
    ];
    let output = input;
    replacements.forEach(
        (replacement) => {
            output = output.replace(replacement.search, replacement.replace);
        }
    );

    return output.trim();
}