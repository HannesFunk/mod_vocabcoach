import notification from 'core/notification';
import {getString} from 'core/str';
import {getClassTotalAJAX, setCheckModeAJAX} from "./repository";

const Selectors = {
    actions: {
        checkBox: '[data-action="mod_vocabcoach/check_box"]',
        addUserVocab: '[data-action="mod_vocabcoach/add_vocab_user"]',
        addList: '[data-action="mod_vocabcoach/add_vocab_list"]',
        showLists: '[data-action="mod_vocabcoach/show_lists"]',
        forceCheck: '[data-action="mod_vocabcoach/force_check_all"]',
        showPdfUser: '[data-action="mod_vocabcoach/show_pdf_user"]',
        viewBox: '[data-action="mod_vocabcoach/view_box"]',
        liveUpdate: '[data-action="mod_vocabcoach/live_update"]',
    },
    elements: {
        dropdown: '.dropdown',
        checkModeSelect: '#checkmode-select',
    }
};

export function init(cmid, userid, courseid) {
    document.addEventListener('click', e => {
        if (e.target.closest(Selectors.actions.forceCheck)) {
            checkBox(cmid, e.target.closest(Selectors.actions.checkBox), true);
        } else if (e.target.closest(Selectors.actions.showPdfUser)) {
            const stage = e.target.closest(Selectors.actions.showPdfUser).getAttribute('data-stage');
            window.open('exports/pdf.php?userid=' + userid + '&cmid=' +
                cmid + '&stage=' + stage);
        } else if (e.target.closest(Selectors.actions.viewBox)) {
            const stage = e.target.closest(Selectors.actions.viewBox).getAttribute('data-stage');
            location.href = 'viewbox.php?id=' + cmid + '&stage=' + stage;
        }  else if (e.target.closest(Selectors.actions.checkBox)) {
            checkBox(cmid, e.target.closest(Selectors.actions.checkBox));
        } else if (e.target.closest(Selectors.actions.addUserVocab)) {
            location.href = 'add_vocab.php?id=' + cmid + '&mode=user';
        } else if (e.target.closest(Selectors.actions.addList)) {
            location.href = 'add_vocab.php?id=' + cmid + '&mode=list';
        } else if (e.target.closest(Selectors.actions.showLists)) {
            location.href = 'lists.php?id=' + cmid;
        } else if (e.target.closest(Selectors.elements.dropdown)) { // keep this last!
            return false;
        }
    });

    const checkBoxLiveUpdate = document.querySelector(Selectors.actions.liveUpdate);
    if (checkBoxLiveUpdate) {
        checkBoxLiveUpdate.addEventListener('change', () => {
            if (checkBoxLiveUpdate.checked) {
                if (checkBoxLiveUpdate.hasAttribute('data-interval-id')) {
                    return;
                }
                const intervalID = setInterval( () => {
                    getClassTotalAJAX(cmid, courseid).then(
                        (result) => {
                            document.getElementById('vocabcoach-class-total').innerHTML = result.total === -1 ? '-' : result.total;
                        }
                    );
                }, 1000);
                checkBoxLiveUpdate.setAttribute('data-interval-id', intervalID.toString());
            } else {
                const intervalID = checkBoxLiveUpdate.getAttribute('data-interval-id');
                if (intervalID !== "") {
                    clearInterval(parseInt(intervalID));
                    checkBoxLiveUpdate.removeAttribute('data-interval-id');
                }
            }
        });
    }

    const checkModeSelect = document.querySelector(Selectors.elements.checkModeSelect);
    const userPrefsListener = () => {
        if (!checkModeSelect) {
            return;
        }
        const mode = checkModeSelect.value;
        if (mode === 'empty') {
            return;
        }
        setCheckModeAJAX(cmid, userid, mode)
            .catch(err => notification.exception(err))
            .then(() => {
                    return getString('notification_userprefs_updated', 'mod_vocabcoach').then(msg => {
                        const msgData = {
                            type: "success",
                            message: msg
                        };
                        notification.addNotification(msgData);
                    });
                }
            );
    };
    checkModeSelect.addEventListener('change', userPrefsListener);
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

