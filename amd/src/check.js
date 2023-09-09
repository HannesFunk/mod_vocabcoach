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
        getListVocab(addInfo);
    } else {
        getBoxVocab(userid, addInfo, force);
    }
    addListeners(userid);
    resetCheckFields();
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
        } else if (e.target.closest(Selectors.actions.typedVocabUnkown)) {
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
        typedVocabUnkown: '[data-action="mod-vocabcoach/typed-vocab-unknown"]',
    },
    formElements : {
        mode: '[id="check-mode"]',
        typedVocab: '[id="input-vocab-front"]',
    },
};
export function changeMode() {
    mode = document.getElementById('check-mode').value;
    if (mode === 'type') {
        showElements(['check-front', 'check-buttons', ], false);
        showElements(['check-back', 'check-type-area'], true);
        document.getElementById('input-vocab-front').value = '';
        showElement(document.getElementsByClassName('instruction-front-back-random')[0], false);
    } else {
        showElements(['check-front', 'check-buttons'], true);
        showElements(['check-type-area'], false);
        showElement(document.getElementsByClassName('instruction-front-back-random')[0], true);
    }

    if (mode === 'front' || mode === 'back') {
        resetCheckFields(mode);
    }
}

function getBoxVocab (userid, stage, force)  {
    getBoxArrayAJAX(userid, modid, stage, force).then(response => {
        vocabArrayJSON = response;
        showNext();
        }
    );
}

function getListVocab (listid)  {
    getListArrayAJAX(listid).then(response => {
            vocabArrayJSON = response;
            showNext();
        }
    );
}

function checkTypedVocab (userid) {
    const currElementDataID = parseInt(document.getElementById('check-container').getAttribute('data-vocab-data-id'));

    if (currElementDataID !== -1 && vocabArrayJSON[0].dataid !== currElementDataID) { // This is weird and shouldn't happen!
        console.log("currElementID does not equal 0-element in vocabArrayJSON");
        return;
    }

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
function showNext() {
    const currElementDataID = parseInt(document.getElementById('check-container').getAttribute('data-vocab-data-id'));

    if (currElementDataID !== -1 && vocabArrayJSON[0].dataid !== currElementDataID) { // This is weird and shouldn't happen!
        console.log("currElementID does not equal 0-element in vocabArrayJSON");
        return;
    }

    if (currElementDataID !== -1) {
        vocabArrayJSON.splice(0, 1);
    }

    const numberRemaining = vocabArrayJSON.length;
    document.getElementsByClassName('check-number-remaining')[0].innerHTML = 'Noch ' + numberRemaining +
        ' Vokabel' + (numberRemaining === 1 ? '' : 'n');

    if (numberRemaining === 0) {
        showSummary();
        return;
    }

    document.getElementById('check-front').innerHTML = vocabArrayJSON[0].front;
    document.getElementById('check-back').innerHTML = vocabArrayJSON[0].back;
    document.getElementById('check-container').setAttribute('data-vocab-data-id', vocabArrayJSON[0].dataid);

    if (mode === 'random') {
        const random = Math.floor(Math.random() * 2) === 0;
        resetCheckFields(random);
    } else if (mode === 'type') {
        document.getElementById('input-vocab-front').value = '';
    } else {
        resetCheckFields(mode);
    }
}

function resetCheckFields(side) {
    showElement('check-front', side === 'front');
    showElement('check-back', side !== 'front');
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
//
// function reveal(triggeringBox) {
//     showtriggeringBox.getElementsByClassName('vc-check-label')[0].classList.remove('hidden');
// }

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
