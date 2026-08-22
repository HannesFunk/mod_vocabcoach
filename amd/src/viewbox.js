/**
 * Box content page: lets the user remove single vocabulary items from their box.
 *
 * @module     mod_vocabcoach/viewbox
 * @copyright  2023 J. Funk <johannesfunk@outlook.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {removeVocabFromUserAJAX} from "./repository";

/**
 * Registers the click handler that removes a vocabulary item from the user's box.
 *
 * The handler is delegated on the document, so it also covers rows added after init.
 */
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