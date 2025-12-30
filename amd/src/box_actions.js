import notification from 'core/notification';
import {getClassTotalAJAX} from "./repository";

const Selectors = {
    actions: {
        checkBox: '[data-action="mod_vocabcoach/check_box"]',
        addUserVocab: '[data-action="mod_vocabcoach/add_vocab_user"]',
        addList: '[data-action="mod_vocabcoach/add_vocab_list"]',
        showLists: '[data-action="mod_vocabcoach/show_lists"]',
        forceCheck: '[data-action="mod_vocabcoach/force_check_all"]',
        showPdfUser: '[data-action="mod_vocabcoach/show_pdf_user"]',
        viewBox: '[data-action="mod_vocabcoach/view_box"]',
    },
    elements: {
        dropdown: '.dropdown',
    }
};

export function init(cmid, userid, courseid) {
    document.addEventListener('click', e => {
        if (e.target.closest(Selectors.actions.forceCheck)) {
            checkBox(cmid, e.target.closest(Selectors.actions.checkBox), true);
        } else if (e.target.closest(Selectors.actions.showPdfUser)) {
            const stage = e.target.closest(Selectors.actions.showPdfUser).getAttribute('data-stage');
            window.open('exorts/pdf.php?userid=' + userid + '&cmid=' +
                cmid + '&stage=' + stage);
        } else if (e.target.closest(Selectors.actions.viewBox)) {
            const stage = e.target.closest(Selectors.actions.viewBox).getAttribute('data-stage');
            location.href = 'viewbox.php?id=' + cmid + '&stage=' + stage;
        } else if (e.target.closest(Selectors.elements.dropdown)) {
            return false;
        }  else if (e.target.closest(Selectors.actions.checkBox)) {
            checkBox(cmid, e.target.closest(Selectors.actions.checkBox));
        } else if (e.target.closest(Selectors.actions.addUserVocab)) {
            location.href = 'add_vocab.php?id=' + cmid + '&mode=user';
        } else if (e.target.closest(Selectors.actions.addList)) {
            location.href = 'add_vocab.php?id=' + cmid + '&mode=list';
        } else if (e.target.closest(Selectors.actions.showLists)) {
            location.href = 'lists.php?id=' + cmid;
        }
    });

    getClassTotalAJAX(cmid, courseid).then(
        (result) => {
            document.getElementById('vocabcoach-class-total').innerHTML = result.total;
        }
    );
    setInterval( () => {
        getClassTotalAJAX(cmid, courseid).then(
            (result) => {
                document.getElementById('vocabcoach-class-total').innerHTML = result.total;
            }
        );
    }, 1000);
}

function checkBox(cmid, box, force = false) {
    if (parseInt(box.getAttribute('data-total')) === 0) {
        const msgData = {
            type: "info",
            message: 'In dieser Box sind zur Zeit keine Vokabeln enthalten.'
        };
        notification.addNotification(msgData).then(null);
    } else if (force && parseInt(box.getAttribute('data-due')) > 0) {
        notification.addNotification({type: 'info',
            message: 'Wiederhole zuerst die aktuell fälligen Vokabeln in dieser Box. ' +
                'Erst danach kannst du auch die anderen abfragen.'}).then(null);
    } else if (!force && parseInt(box.getAttribute('data-due')) === 0) {
        const dueTime =  box.getAttribute('data-next-due');
        const msgData = {
            type: "info",
            message : "In dieser Box hast du bereits alle Vokabeln gelernt. Die nächsten Vokabeln sind in " + dueTime
                +" fällig."
        };
        notification.addNotification(msgData).then(null);
    } else {
        const stage = box.getAttribute('data-stage');
        location.href = 'check.php?id=' + cmid + '&stage=' + stage + "&force=" + force;
    }
}