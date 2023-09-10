// noinspection SpellCheckingInspection

import {getListArrayAJAX} from "./repository";
import {showElement} from "./general";

let template = null;

export const init = (listidString = "-1") => {
    initTemplate();
    let listid = parseInt(listidString);

    if (listid !== -1) {
        const row = document.getElementsByName('front[]')[0].closest('[data-groupname="vocabrow"]');
        showElement(row, false);
        const spinnerContainer = document.createElement('div');
        spinnerContainer.classList.add('spinner-container');
        const spinner = document.createElement('div');
        spinner.classList.add('spinner');
        spinnerContainer.appendChild(spinner);
        row.parentNode.insertBefore(spinnerContainer, row.nextSibling);

        getListArrayAJAX(listid).then(
            (array) => {
                spinner.remove();
                for (let i=0; i<array.length; i++) {
                    let vocab = array[i];
                    addRow(vocab.dataid, vocab.front, vocab.back);
                }
                addRow();
            }
        );
    }

    document.addEventListener('change', event => {
        const element = event.target;
        if (element.getAttribute('name') === 'front[]') {
           addRowMaybe(element);
        }
    });
};

function initTemplate() {
    const vocabRow = document.getElementsByName('front[]')[0].closest('[data-groupname="vocabrow"]');
    let temp = document.createElement('div');
    temp.classList.add('form-group', 'row', 'fitem');
    temp.setAttribute('datagroupname', 'vocabrow');
    temp.innerHTML = vocabRow.innerHTML;

    template = temp;
}

function addRow(id = 0, front = "", back = "") {
    const firstRow = document.getElementsByName('front[]')[0].closest('[data-groupname="vocabrow"]');
    const lastRow = firstRow.parentNode.lastChild;
    const tempRow = template.cloneNode(true);
    tempRow.querySelectorAll('input[name="vocabid[]"]')[0].value = id;
    tempRow.querySelectorAll('input[name="front[]"]')[0].value = front;
    tempRow.querySelectorAll('input[name="back[]"]')[0].value = back;
    lastRow.parentNode.insertBefore(tempRow, lastRow.nextSibling);
    return true;
}

function addRowMaybe(callingElement) {
    const frontInputs = document.querySelectorAll('input[name="front[]"]');
    const noElements = frontInputs.length;
    const index = Array.from(frontInputs).indexOf(callingElement);
    if (index === noElements - 1) {
        addRow();
        return true;
    }
    return false;
}