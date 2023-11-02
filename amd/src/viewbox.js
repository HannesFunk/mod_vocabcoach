import {removeVocabFromUserAJAX} from "./repository";

export function init () {
    document.addEventListener('click', e => {
       if (e.target.closest(Selectors.actions.removeVocab)) {
           let dataid = e.target.closest(Selectors.actions.removeVocab).getAttribute('data-dataid');
            removeVocabFromUserAJAX(dataid).then(
                () => {
                    e.target.closest('tr').remove();
                }
            );
       }
    });
}

const Selectors = {
    actions: {
        removeVocab: '[data-action="mod_vocabcoach/remove_vocab_from_user"]'
    }
};