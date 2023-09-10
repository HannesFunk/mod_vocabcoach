import {getBoxArrayAJAX, getListArrayAJAX, updateVocabAJAX} from "./repository";
import mustache from 'core/mustache';
import {showElement, showElements} from "./general";

let vocabArrayJSON = null;
let modid = -1;
let knownCount = 0;
let unknownCount = 0;
let mode = 'front';
let force = false;

export const init = (userid, addInfo, moduleid, force_init = false) => {
    modid = moduleid;
    userid = parseInt(userid);
    force = force_init;
    if (userid === -1) {
        getListArrayAJAX(addInfo).then(response => {
            vocabArrayJSON = response;
            showNext(false);
            }
        );
    } else {
        getBoxArrayAJAX(userid, modid, addInfo, force).then(response => {
            vocabArrayJSON = response;
            showNext(false);
            }
        );
    }
    addListeners(userid);
};

function addListeners(userid) {
    document.addEventListener('click', e => {
        if (e.target.closest(Selectors.actions.checkTypedVocab)) {
            checkTypedVocab(userid);
        } else if (e.target.closest(Selectors.actions.revealCard) && mode !== 'type') {
            const label = e.target.closest(Selectors.actions.revealCard).getElementsByClassName('vc-check-label')[0];
            showElement(label, true);
        } else if (e.target.closest(Selectors.actions.updateVocab)) {
            checkDone(vocabArrayJSON[0].dataid, userid, e.target.getAttribute('data-vocabcoach-known') === 'true');
        } else if (e.target.closest(Selectors.actions.endCheck)) {
            endCheck();
        } else if (e.target.closest(Selectors.actions.revealTypedVocab)) {
            document.getElementById('input-vocab-front').value = vocabArrayJSON[0].front;
            document.getElementById('input-vocab-front').disabled = true;

            showElements(['button-typed-vocab-next'], true);
            showElements(['button-typed-vocab-check', 'button-typed-vocab-reveal'], false);
        } else if (e.target.closest(Selectors.actions.typedVocabUnknown)) {
            showElements(['button-typed-vocab-next'], false);
            showElements(['button-typed-vocab-reveal', 'button-typed-vocab-check'], true);

            document.getElementById('input-vocab-front').value = '';
            document.getElementById('input-vocab-front').disabled = false;

            updateVocabAJAX(vocabArrayJSON[0].dataid, userid, false).then(() => { showNext();});
        }
    });

    document.addEventListener('change', e => {
        if (e.target.closest(Selectors.formElements.mode)) {
            changeMode();
        }
    });

    document.addEventListener('keyup', e => {
        if (e.target.closest(Selectors.formElements.typedVocab)) {
            document.getElementById('input-vocab-front').classList.remove('wrong');
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
    },
    formElements : {
        mode: '[id="check-mode"]',
        typedVocab: '[id="input-vocab-front"]',
    },
};
export function changeMode() {
    mode = document.getElementById('check-mode').value;
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

function checkTypedVocab (userid) {
    const typed = document.getElementById('input-vocab-front').value;
    const correct = vocabArrayJSON[0].front;

    if (typed === correct && !force) {
        updateVocabAJAX(vocabArrayJSON[0].dataid, userid, true).then(
            () => {
                knownCount++;
                showNext();
            }
        );
    } else if (typed === correct) {
        knownCount++;
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
    document.getElementById('check-front').innerHTML = vocabArrayJSON[0].front;
    document.getElementById('check-back').innerHTML = vocabArrayJSON[0].back;
    document.getElementById('check-container').setAttribute('data-vocab-data-id', vocabArrayJSON[0].dataid);
}
function resetCheckFields() {
    switch (mode) {
        case 'random': {
            const random = Math.floor(Math.random() * 2);
            showElement('check-front', random === 1);
            showElement('check-back', random === 0);
            break;
        }
        case 'front':
        case 'back':
            showElement('check-front', mode === 'front');
            showElement('check-back', mode === 'back');
            break;

        case 'type':
            document.getElementById('input-vocab-front').value = '';
            break;
    }
}

function endCheck() {
    location.href = '../../mod/vocabcoach/view.php?id=' + modid;
}

function showSummary() {
    const templateData = {
        known: knownCount,
        total: knownCount + unknownCount,
        message: getSummaryMessage()
    };
    fetch('../../mod/vocabcoach/templates/check_summary.mustache').then(
        (res) => {
            return res.text();
        }
    ).then(
        (template) => {
            const output = mustache.render(template, templateData);
            const summaryContainer = document.getElementsByClassName('check-summary')[0];
            summaryContainer.innerHTML = output;
            showElement(summaryContainer, true);
            showElements(['check-box-front', 'check-box-back'], false);
        }
    );
}

function getSummaryMessage() {
    const ratio = knownCount/(unknownCount + knownCount);

    if (ratio > 0.9) {
        return "Hervorragend!";
    }
    if (ratio > 0.7) {
        return "Gut gemacht!";
    }
    if (ratio > 0.5) {
        return "Du hast einiges erreicht!";
    }
    if (ratio > 0.3) {
        return "Das kannst du besser!";
    }
    return "Hm. Da musst du nochmal ran!";
}

function checkDone(vocabId, userId, known) {
    if (userId === -1 || force) {
        updateCount(known);
        showNext();
    }
    else {
    updateVocabAJAX(vocabId, userId, known).then(
            () => {
                updateCount(known);
                showNext();
            }
        );
    }
}

function updateCount(known) {
    if (known) {
        knownCount++;
    } else {
        unknownCount++;
    }
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