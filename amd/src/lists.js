/**
 * List overview page: shows the vocabulary lists and their actions.
 *
 * @module     mod_vocabcoach/lists
 * @copyright  2023 J. Funk <johannesfunk@outlook.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {addListToUserAJAX, deleteListAJAX, distributeListAJAX, getListsAJAX} from "./repository";
import Template from 'core/templates';
import notification, {saveCancel} from 'core/notification';
import Log from 'core/log';
import {getString, getStrings} from 'core/str';

let cmid;
let userId;

/**
 * Adds a list to the current user's box and notifies them about the result.
 *
 * @param {Number} listid The id of the list to add.
 */
function addListToUser(listid) {
    addListToUserAJAX(listid, userId, cmid)
        .then(() => getString('lists_added_to_box', 'mod_vocabcoach'))
        .then((message) => notification.addNotification({message: message, type: "success"}))
        .catch((error) => Log.debug(error));
}

/**
 * Sets up the list overview page.
 *
 * @param {String} moduleId The course module id.
 * @param {String} userIdString The id of the current user.
 * @param {String} capabilitiesInfo JSON encoded object with the canEdit and canDistribute flags.
 */
export function init(moduleId, userIdString, capabilitiesInfo) {
    cmid = parseInt(moduleId);
    userId = parseInt(userIdString);

    printLists(JSON.parse(capabilitiesInfo));

    document.addEventListener('click', e => {
        if (e.target.closest(Selectors.actions.deleteList)) {
            deleteList(e.target.getAttribute('data-list-id'));
        } else if (e.target.closest(Selectors.actions.checkList)) {
            const listId = e.target.getAttribute('data-list-id');
            location.href = 'check.php?id=' + cmid + '&listid=' + listId;
        } else if (e.target.closest(Selectors.actions.showCsv)) {
            const menuItem = e.target.closest(Selectors.actions.showCsv);
            window.open('exports/csv.php?mode=list&listid=' + menuItem.getAttribute('data-list-id'), '_blank').focus();
        } else if (e.target.closest(Selectors.actions.showPdf)) {
            const menuItem = e.target.closest(Selectors.actions.showPdf);
            window.open('exports/pdf.php?listid=' + menuItem.getAttribute('data-list-id')+"&cmid="+cmid, '_blank').focus();
        } else if (e.target.closest(Selectors.actions.editList)) {
            const menuItem = e.target.closest(Selectors.actions.editList);
            location.href = 'add_vocab.php?id=' + cmid + '&mode=edit&listid=' + menuItem.getAttribute('data-list-id');
        } else if (e.target.closest(Selectors.actions.addListToUser)) {
            const menuItem = e.target.closest(Selectors.actions.addListToUser);
            addListToUser(menuItem.getAttribute('data-list-id'));
        } else if (e.target.closest(Selectors.actions.closePage)) {
            location.href = '../../mod/vocabcoach/view.php?id=' + cmid;
        }else if (e.target.closest(Selectors.actions.toggleUserSwitch)) {
            const userSwitch = document.getElementById('userswitch');
            const newState = !userSwitch.checked;
            userSwitch.checked = newState;
            printLists(capabilitiesInfo, newState);
        } else if (e.target.closest(Selectors.actions.distributeList)) {
            const menuItem = e.target.closest(Selectors.actions.distributeList);
            distributeList(menuItem.getAttribute('data-list-id'), cmid);

        }
    });

    document.addEventListener('change', (e) => {
        if (e.target.getAttribute('data-action') === 'toggle-user-switch') {
            printLists(capabilitiesInfo, true);
        }
    });
}

const Selectors = {
    actions: {
        deleteList: '[data-action="mod_vocabcoach/delete_list"]',
        checkList: '[data-action="mod_vocabcoach/check_list"]',
        showPdf: '[data-action="mod_vocabcoach/show_pdf"]',
        showCsv: '[data-action="mod_vocabcoach/show_csv"]',
        editList: '[data-action="mod_vocabcoach/edit_list"]',
        addListToUser: '[data-action="mod_vocabcoach/add_list_to_user"]',
        closePage: '[data-action="mod_vocabcoach/close_page"]',
        distributeList: '[data-action="mod_vocabcoach/distribute_list"]',
        toggleUserSwitch: '[data-action="mod_vocabcoach/toggle-user-switch"]',
    }
};

/**
 * Fetches the lists and re-renders the page content with them.
 *
 * @param {Object} capInfo Object with the canEdit and canDistribute flags of the current user.
 * @param {Boolean} [onlyOwnLists=false] Whether to show only the lists created by the current user.
 */
export function printLists(capInfo, onlyOwnLists = false) {

    getListsAJAX(cmid, userId, onlyOwnLists).then(
        (res) => {
            res.forEach(list => {
                list.editable = capInfo.canEdit || list.createdby === userId;
                list.distributable = capInfo.canDistribute;
            });
            return {'lists': res, 'loading': false, 'cmid': cmid, 'onlyOwnLists': onlyOwnLists};
        }
    ).then(
        (json) => { return Template.renderForPromise('mod_vocabcoach/lists', json); }
    ).then((res) => {
        document.querySelectorAll('[role="main"]')[0].innerHTML = res.html;
    });
}

/**
 * Asks for confirmation and then deletes a list, removing its row from the table.
 *
 * @param {Number} listid The id of the list to delete.
 */
function deleteList(listid) {
    getStrings([
        {key: 'confirm', component: 'core'},
        {key: 'lists_confirm_delete', component: 'mod_vocabcoach'},
        {key: 'delete', component: 'core'},
        {key: 'lists_deleted', component: 'mod_vocabcoach'},
    ]).then(([title, question, buttonLabel, successMessage]) => {
        return saveCancel(title, question, buttonLabel, () => {
            deleteListAJAX(listid).then(
                () => {
                    document.querySelectorAll('tr[data-list-id="' + listid + '"]')[0].remove();
                    return notification.addNotification({type: 'success', message: successMessage});
                }
            ).catch((error) => Log.debug(error));
        }, null);
    }).catch((error) => Log.debug(error));
}

/**
 * Asks for confirmation and then adds a list to the boxes of all participants.
 *
 * @param {Number} listid The id of the list to distribute.
 * @param {Number} vocabcoachId The course module id of the activity.
 */
function distributeList(listid, vocabcoachId) {
    getStrings([
        {key: 'confirm', component: 'core'},
        {key: 'lists_confirm_distribute', component: 'mod_vocabcoach'},
        {key: 'confirm', component: 'core'},
        {key: 'lists_distributed', component: 'mod_vocabcoach'},
    ]).then(([title, question, buttonLabel, successMessage]) => {
        return saveCancel(title, question, buttonLabel, () => {
            distributeListAJAX(listid, vocabcoachId).then(
                () => notification.addNotification({type: 'success', message: successMessage})
            ).catch((error) => Log.debug(error));
        }, null);
    }).catch((error) => Log.debug(error));
}
