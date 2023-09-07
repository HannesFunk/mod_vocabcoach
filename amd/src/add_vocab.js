
export const init = () => {
    document.addEventListener('change', event => {
        const element = event.target;
        if (element.getAttribute('name') === 'front[]') {
           addRowMaybe(element);
        }
    });

    document.getElementsByName('front[]')[0].setAttribute('data-islast', true);
};

function addRowMaybe(callingElement) {
    if (!callingElement.getAttribute('data-islast')) {
        return false;
    }

    const vocabRow = callingElement.closest('div.row.form-group');
    let template = document.createElement('div');
    template.classList.add('form-group', 'row', 'fitem');
    template.setAttribute('datagroupname', 'vocabrow');
    template.innerHTML = vocabRow.innerHTML;

    let inputElement = template.querySelectorAll('input', 'name="front[]"')[0];
    inputElement.name = 'front[]';
    inputElement.setAttribute('data-isLast', true);
     vocabRow.parentNode.insertBefore(template, vocabRow.nextSibling);

    callingElement.setAttribute('data-islast', false);

    return true;

}