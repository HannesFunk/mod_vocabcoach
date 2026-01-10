// noinspection SpellCheckingInspection

import {getListArrayAJAX} from "./repository";
import {showElement} from "./general";

let template = null;

/**
 * Initializes the add_vocab module.
 *
 * @param {string} [listidString="-1"] - The ID of the vocabulary list as a string. Defaults to "-1".
 * If a valid list ID is provided, it fetches the vocabulary list and populates the rows.
 */
export const init = (listidString = "-1") => {

    const vocabRow = document.querySelector('input[name="front[]"]').closest('[data-groupname="vocabrow"]');
    const rowDiv = vocabRow.querySelector('#id_vocabid_').parentNode;

    for (let i = rowDiv.childNodes.length - 1; i >= 0; i--) {
        const child = rowDiv.childNodes[i];
        if (child.nodeType === Node.TEXT_NODE) {
            rowDiv.removeChild(child);
        }
    }

    let temp = document.createElement('div');
    temp.classList.add('form-group', 'row', 'fitem');
    temp.setAttribute('datagroupname', 'vocabrow');
    temp.innerHTML = vocabRow.innerHTML;

    template = temp;

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
                    addRow(vocab.dataid, vocab.front, vocab.back, vocab.third);
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

/**
 * Adds a new vocabulary row to the form.
 *
 * @param {number} [id=0] - The ID of the vocabulary item. Defaults to 0.
 * @param {string} [front=""] - The front text of the vocabulary item. Defaults to an empty string.
 * @param {string} [back=""] - The back text of the vocabulary item. Defaults to an empty string.
 * @param {string} [third=""] - The third text of the vocabulary item. Defaults to an empty string.
 * @returns {boolean} - Returns true after adding the row.
 */
function addRow(id = 0, front = "", back = "", third = "") {
    const firstRow = document.getElementsByName('front[]')[0].closest('[data-groupname="vocabrow"]');
    const lastRow = firstRow.parentNode.lastChild;
    const tempRow = template.cloneNode(true);
    tempRow.querySelectorAll('input[name="vocabid[]"]')[0].value = id;
    tempRow.querySelectorAll('input[name="front[]"]')[0].value = front;
    tempRow.querySelectorAll('input[name="back[]"]')[0].value = back;
    tempRow.querySelectorAll('input[name="third[]"]')[0].value = third;
    lastRow.parentNode.insertBefore(tempRow, lastRow.nextSibling);
    return true;
}

/**
 * Adds a new row if the calling element is the last row.
 *
 * @param {HTMLElement} callingElement - The input element that triggered the event.
 * @returns {boolean} - Returns true if a new row is added, false otherwise.
 */
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