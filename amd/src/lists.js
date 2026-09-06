/**
 * List overview page: shows the vocabulary lists and their actions.
 *
 * @module     mod_vocabcoach/lists
 * @copyright  2023 J. Funk <johannesfunk@outlook.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {addListToUserAJAX, deleteListAJAX, distributeListAJAX} from "./repository";
import notification, {saveCancelPromise} from 'core/notification';
import Log from 'core/log';
import {getString, getStrings} from 'core/str';
import {getTableFromId, refreshTableContent, setFilters} from "core_table/dynamic";

let cmid;

/**
 * Adds a list to the current user's box and notifies them about the result.
 *
 * @param {Number} listid The id of the list to add.
 */
function addListToUser(listid) {
    addListToUserAJAX(listid, cmid)
        .then(() => getString('lists_added_to_box', 'mod_vocabcoach'))
        .then((message) => notification.addNotification({message: message, type: "success"}))
        .catch((error) => Log.debug(error));
}

/**
 * Sets up the list overview page.
 *
 * @param {String} moduleId The course module id.
 */
export function init(moduleId) {
    cmid = parseInt(moduleId, 10);

    document.addEventListener('click', e => {
        const menuItem = e.target.closest('[data-action^="mod_vocabcoach/"]');
        if (!menuItem) {
            return;
        }
        const listId = parseInt(menuItem.dataset.listId, 10);
        switch (menuItem.dataset.action) {
            case Selectors.actions.deleteList:
                deleteList(listId);
                break;

            case Selectors.actions.checkList:
                location.href = 'check.php?id=' + cmid + '&listid=' + listId;
                break;

            case Selectors.actions.editList:
                location.href = 'add_vocab.php?id=' + cmid + '&listid=' + listId;
                break;

            case Selectors.actions.addListToUser:
                addListToUser(listId);
                break;

            case Selectors.actions.distributeList:
                distributeList(listId, cmid);
                break;

            // Those two omitted for now.
            // case Selectors.actions.showCsv:
            // case Selectors.actions.showPdf:
        }

        if (menuItem.href === '#') {
            e.preventDefault();
        }

    });

    document.addEventListener('change', (e) => {
        if (e.target.id === 'userswitch') {
            const checked = document.getElementById('userswitch').checked;
            setFilters(getTableFromId('vocabcoach-lists'), {
                jointype: 1,
                filters: [
                    {name: 'cmid', jointype: 1, values: [cmid]},
                    {name: 'ownlists', jointype: 1, values: [checked ? 1 : 0]}
                ]
            });
            e.target.blur();
        }
    });
}

const Selectors = {
    actions: {
        deleteList: 'mod_vocabcoach/delete_list',
        checkList: 'mod_vocabcoach/check_list',
        showPdf: 'mod_vocabcoach/show_pdf',
        showCsv: 'mod_vocabcoach/show_csv',
        editList: 'mod_vocabcoach/edit_list',
        addListToUser: 'mod_vocabcoach/add_list_to_user',
        distributeList: 'mod_vocabcoach/distribute_list',
    }
};


/**
 * Asks for confirmation and then deletes a list, removing its row from the table.
 *
 * @param {Number} listid The id of the list to delete.
 */
async function deleteList(listid) {
    const [title, question, buttonLabel, successMessage] = await getStrings([
        {key: 'confirm', component: 'core'},
        {key: 'lists_confirm_delete', component: 'mod_vocabcoach'},
        {key: 'delete', component: 'core'},
        {key: 'lists_deleted', component: 'mod_vocabcoach'},
    ]);

    try {
        await saveCancelPromise(title, question, buttonLabel);
    } catch {
        return;
    }

    try {
        await deleteListAJAX(listid, cmid);
        await refreshTableContent(getTableFromId('vocabcoach-lists'));
        await notification.addNotification({type: 'success', message: successMessage});
    } catch (err) {
        await notification.exception(err);
    }
}

/**
 * Asks for confirmation and then adds a list to the boxes of all participants.
 *
 * @param {Number} listid The id of the list to distribute.
 * @param {Number} vocabcoachId The course module id of the activity.
 */
async function distributeList(listid, vocabcoachId) {
    const [title, question, buttonLabel, successMessage] = await getStrings([
        {key: 'confirm', component: 'core'},
        {key: 'lists_confirm_distribute', component: 'mod_vocabcoach'},
        {key: 'confirm', component: 'core'},
        {key: 'lists_distributed', component: 'mod_vocabcoach'},
    ]);

    try {
        await saveCancelPromise(title, question, buttonLabel);
    } catch {
        return;
    }

    try {
        await distributeListAJAX(listid, vocabcoachId);
        await notification.addNotification({type: 'success', message: successMessage});
    } catch (err) {
        await notification.exception(err);
    }
}
