import {addListToUserAJAX, deleteListAJAX, distributeListAJAX, getListsAJAX} from "./repository";
import mustache from 'core/mustache';
import notification, {saveCancel} from 'core/notification';
import Log from 'core/log';

let cmid;
let userId;

function addListToUser(listid) {
    addListToUserAJAX(listid, userId, cmid).then(
        () => {
            const notificationData = {
                message: "Neue Vokabeln von dieser Liste wurden deinem Karteikarten hinzugefügt.",
                type: "success",
            };
            notification.addNotification(notificationData).then(null);
        }
    );
}

export function init(moduleId, userIdString, capabilitiesInfo) {
    cmid = parseInt(moduleId);
    userId = parseInt(userIdString);

    printLists(JSON.parse(capabilitiesInfo));

    document.addEventListener('click', e => {
        if (e.target.closest(Selectors.actions.deleteList)) {
            deleteList(e.target.getAttribute('data-list-id'));
        } else if (e.target.closest(Selectors.actions.checkList)) {
            location.href = 'check.php?id=' + cmid + '&source=list&listid=' + e.target.getAttribute('data-list-id');
        } else if (e.target.closest(Selectors.actions.showPdf)) {
            const menuItem = e.target.closest(Selectors.actions.showPdf);
            window.open('vocablist_pdf.php?listid=' + menuItem.getAttribute('data-list-id'), '_blank').focus();
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
        editList: '[data-action="mod_vocabcoach/edit_list"]',
        addListToUser: '[data-action="mod_vocabcoach/add_list_to_user"]',
        closePage: '[data-action="mod_vocabcoach/close_page"]',
        distributeList: '[data-action="mod_vocabcoach/distribute_list"]',
        toggleUserSwitch: '[data-action="mod_vocabcoach/toggle-user-switch"]',
    }
};

export function printLists(capInfo, onlyOwnLists = false) {
    let json = null;
    let template = null;
    const getData = getListsAJAX(cmid, userId, onlyOwnLists).then(
        res => {
            res.forEach(list => {
                list.editable = capInfo.canEdit || list.createdby === userId;
                list.distributable = capInfo.canDistribute;
            });
            json = {'lists': res, 'loading': false, 'cmid': cmid, 'onlyOwnLists': onlyOwnLists};
            if (res.length === 0) {
                json.emptyList = true;
            }
        }
    );

    const templateSource =  '../../mod/vocabcoach/templates/lists.mustache';
    const fetchTemplate = fetch(templateSource).then(
        (res) => { return res.text(); }
    ).then(
        (text) => { template = text; }
    );

    Promise.all([getData, fetchTemplate]).then(() => {
            mustache.parse(template);
            document.querySelectorAll('[role="main"]')[0].innerHTML = mustache.render(template, json);
            return true;
        }
    );
}

function deleteList(listid) {
    saveCancel('Bestätigung', 'Soll diese Liste wirklich gelöscht werden?', 'Bestätigen', () => {
        deleteListAJAX(listid).then(
            () => {
                document.querySelectorAll('tr[data-list-id="' + listid +'"]')[0].remove();
                notification.addNotification({type: 'success', message: 'Liste gelöscht.'}).then(null);
            }
        );
    }, null).catch ((error) => Log.debug(error));
}

function distributeList(listid, vocabcoachId) {
    const doIt = () => {
        distributeListAJAX(listid, vocabcoachId).then(() => {
            notification.addNotification(
                {type: 'success', message: 'Liste an alle Teilnehmer in diesem Kurs verteilt.'}
            ).then(null);
        });
    };

    saveCancel('Bestätigung',
        'Soll diese Liste wirklich an alle Teilnehmer in diesem Kurs verteilt werden?',
        'Bestätigen',
        doIt,
        null).
    catch((error) => Log.debug(error));
}
