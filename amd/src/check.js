import {
    getBoxArrayAJAX, getFeedbackLineAJAX, getListArrayAJAX,
    updateVocabAJAX, editUserVocabAJAX, removeVocabFromUserAJAX
}
    from "./repository";
import mustache from 'core/mustache';
import {showElement, showElements} from "./general";
import Modal from 'core/modal';
import notification from "core/notification";
import {getString} from 'core/str';

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
            const label = trigger.querySelector('.vc-check-label');
            showElement(label, true);
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
        } else if (e.target.closest(Selectors.actions.editVocab)) {
            editVocab(vocabArrayJSON[0]);
        } else if (e.target.closest(Selectors.actions.deleteVocab)) {
            deleteVocab(vocabArrayJSON[0].dataid);
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
        editVocab: '[data-action="mod_vocabcoach/edit-vocab"]',
        deleteVocab: '[data-action="mod_vocabcoach/delete-vocab"]',
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

    const maxWidth = progressBar.offsetWidth;
    let showEveryNthDot = 1;
    while ((totalVocab * 12)/showEveryNthDot > maxWidth - 50) {
        showEveryNthDot++;
    }

    for (let i = 0; i < totalVocab; i++) {
        let newDot = greyDotContainer.cloneNode(true);
        if ((i % showEveryNthDot) !== 0) {
            newDot.classList.add('hidden');
        }
        let xShift = 12 + i * 12 / showEveryNthDot;
        newDot.style.transform = 'translateX(calc(100% - ' + xShift + 'px))';
        newDot.setAttribute('data-new-shift', (totalVocab - i) * 12 / showEveryNthDot);

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

function showNext() {
    vocabArrayJSON.splice(0, 1);

    const numberRemaining = vocabArrayJSON.length;
    document.querySelector('.check-number-remaining').innerHTML = 'Noch ' + numberRemaining +
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

function adjustFontSizeToBoxHeight(elem) {
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
    Promise.all([getMsg, getTemplate]).then(() => {
        const summaryContainer = document.querySelector('.check-summary');
        summaryContainer.innerHTML = mustache.render(template, templateData);
        showElement(summaryContainer, true);
        showElements(['check-box-front', 'check-box-back', 'check-type-area', 'check-box-third', 'check-buttons'], false);
        const instructionElement = document.querySelector('.instruction-front-back-random');
        instructionElement.innerHTML = "Klicke in das Feld, um die Abfrage zu beenden.";
        showElement(instructionElement, true);
    });
}

function getSummaryAchievement() {
    const ratio = knownCount / (unknownCount + knownCount);

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

function cleanString(input) {
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

async function editVocab(vocab) {
    const esc = s => String(s ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');

    let body = `
        <div class="mb-3">
            <label class="form-label" for="vc-edit-front">Front</label>
            <input class="form-control" id="vc-edit-front" type="text" value="${esc(vocab.front)}" />
        </div>
        <div class="mb-3">
            <label class="form-label" for="vc-edit-back">Back</label>
            <input class="form-control" id="vc-edit-back" type="text" value="${esc(vocab.back)}" />
        </div>`;

    const modal = await Modal.create({
        title: 'Edit vocab',
        body: body,
        footer: `<button type="button" class="btn btn-primary" data-action="vc-edit-save">Save</button>
                 <button type="button" class="btn btn-secondary" data-action="vc-edit-cancel">Cancel</button>`,
        show: true,
        removeOnClose: true,
    });

    modal.getRoot()[0].addEventListener('click', (e) => {
        if (e.target.closest('[data-action="vc-edit-save"]')) {
            let updatedVocab = {
                front: document.getElementById('vc-edit-front').value,
                back: document.getElementById('vc-edit-back').value,
                dataid: vocab.dataid
            };
            editUserVocabAJAX(updatedVocab).then(
                (result) => {
                    if (result.dataid === -1) {
                        notification.addNotification({
                            type: 'error',
                            message: 'Error editing vocab. Please try again later.'
                        });
                        return null;
                    }
                    updatedVocab.dataid = result.dataid;
                    vocabArrayJSON[0] = updatedVocab;
                    updateLabels();
                    return updatedVocab;
                }
            );
            modal.destroy();
        } else if (e.target.closest('[data-action="vc-edit-cancel"]')) {
            modal.destroy();
        }
    });
}

function deleteVocab(dataid) {
    notification.deleteCancelPromise(
        getString('confirm', 'core'),
        getString('confirm_delete_vocab', 'mod_vocabcoach'),
        getString('delete', 'core')
    )
        .then(() => removeVocabFromUserAJAX(dataid))
        .then((result) => {
            if (result.success) {
                showNext();
            }
            return result;
        })
        .catch(() => null);
}