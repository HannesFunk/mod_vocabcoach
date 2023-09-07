import {getListArrayAJAX} from "./repository";

let template = null;

export const init = (listid = -1) => {
    initTemplate();

    if (listid != -1) {
        const row = document.getElementsByName('front[]')[0].closest('div.row.form-group');
        row.style.display = 'none';
        const spinner = document.createElement('div');
        spinner.classList.add('spinner');
        row.parentNode.insertBefore(spinner, row.nextSibling);

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

    const frontElements =  document.getElementsByName('front[]');
    frontElements[frontElements.length - 1].setAttribute('data-islast', true);
};

function initTemplate() {
    const vocabRow = document.getElementsByName('front[]')[0].closest('div.row.form-group');
    let temp = document.createElement('div');
    temp.classList.add('form-group', 'row', 'fitem');
    temp.setAttribute('datagroupname', 'vocabrow');
    temp.innerHTML = vocabRow.innerHTML;

    template = temp;
}

function addRow(id = 0, front = "", back = "") {
    const firstRow = document.getElementsByName('front[]')[0].closest('div.row.form-group');
    const lastRow = firstRow.parentNode.lastChild;
    const tempRow = template.cloneNode(true);
    tempRow.querySelectorAll('input[name="vocabid[]"]')[0].value = id;
    tempRow.querySelectorAll('input[name="front[]"]')[0].value = front;
    tempRow.querySelectorAll('input[name="back[]"]')[0].value = back;
    lastRow.parentNode.insertBefore(tempRow, lastRow.nextSibling);
    return true;
}

function addRowMaybe(callingElement) {
    if (isLast(callingElement)) {
        addRow();
    }
    return false;
}

function isLast(caller) {
    const vocabRow = caller.closest('div.row.form-group');
    return !vocabRow.nextSibling;
}