import {addListToUserAJAX, deleteListAJAX, getListsAJAX} from "./repository";
import mustache from 'core/mustache';
import notification, {saveCancel} from 'core/notification';
import Log from 'core/log';

let vocabcoachId;
let userId;

function addListToUser(listid) {
    addListToUserAJAX(listid, userId, vocabcoachId).then(
        () => {
            const notificationData = {
                message: "Neue Vokabeln von dieser Liste wurden deinem Karteikarten hinzugefügt.",
                type: "success",
            };
            notification.addNotification(notificationData);
        }
    );
}

export function init(cmid, usrid) {
    vocabcoachId = cmid;
    userId = usrid;

    printLists();

    document.addEventListener('click', e => {
        if (e.target.closest(Selectors.actions.deleteList)) {
            deleteList(e.target.getAttribute('data-list-id'));
        } else if (e.target.closest(Selectors.actions.checkList)) {
            location.href = 'check.php?id=' + vocabcoachId + '&mode=list&listid=' + e.target.getAttribute('data-list-id');
        } else if (e.target.closest(Selectors.actions.showPdf)) {
            const menuItem = e.target.closest(Selectors.actions.showPdf);
            window.open('pages/vocablist_pdf.php?listid=' + menuItem.getAttribute('data-list-id'), '_blank').focus();
        } else if (e.target.closest(Selectors.actions.editList)) {
            const menuItem = e.target.closest(Selectors.actions.editList);
            location.href = 'add_vocab.php?id=' + vocabcoachId + '&mode=edit&listid=' + menuItem.getAttribute('data-list-id');
        } else if (e.target.closest(Selectors.actions.addListToUser)) {
            const menuItem = e.target.closest(Selectors.actions.addListToUser);
            addListToUser(menuItem.getAttribute('data-list-id'));
        } else if (e.target.closest(Selectors.actions.closePage)) {
            location.href = '../../mod/vocabcoach/view.php?id=' + vocabcoachId;
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
    }
};

// COMMENT: this code is taken from https://stackoverflow.com/questions/40064129/
// how-can-i-use-a-template-that-is-located-in-a-separate-file-with-mustache-js
export function printLists() {
    let json = null;
    let template = null;
    const getData = getListsAJAX(vocabcoachId).then(
        res => {
            json = {'lists': res, 'loading': false};
            if (res.length === 0) {
                json.emptyList = true;
            }
        }
    );

    const fetchTemplate = fetch('../../mod/vocabcoach/templates/lists.mustache').then(
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
            }
        );
    }, null).catch ((error) => Log.debug(error));
}
