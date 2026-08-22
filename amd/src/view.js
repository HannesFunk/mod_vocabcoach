/**
 * Activity view page: box navigation, exports and user preferences.
 *
 * @module     mod_vocabcoach/view
 * @copyright  2023 J. Funk <johannesfunk@outlook.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import notification from 'core/notification';
import {getString} from 'core/str';
import {getClassTotalAJAX, setCheckModeAJAX, setEmailNotificationsAJAX} from "./repository";

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
        emailNotifications: '#email-notifications'
    }
};

/**
 * Sets up the activity view page.
 *
 * Registers the delegated click handler for the boxes and the dropdown actions, the
 * live update poller for the class total, and the user preference listeners.
 *
 * @param {Number} cmid The course module id.
 * @param {Number} userid The id of the current user.
 * @param {Number} courseid The id of the course the activity belongs to.
 */
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
        } else if (e.target.closest(Selectors.actions.addUserVocab)) {
            location.href = 'add_vocab.php?id=' + cmid + '&mode=user';
        } else if (e.target.closest(Selectors.actions.addList)) {
            location.href = 'add_vocab.php?id=' + cmid + '&mode=list';
        } else if (e.target.closest(Selectors.actions.showLists)) {
            location.href = 'lists.php?id=' + cmid;
        } else if (e.target.closest(Selectors.elements.dropdown)) { // keep this after dropdown-items, but before opening boxes
            return false;
        }  else if (e.target.closest(Selectors.actions.checkBox)) {
            checkBox(cmid, e.target.closest(Selectors.actions.checkBox));
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

    // Email notifications checkbox listener
    const emailNotificationsCheckbox = document.querySelector(Selectors.elements.emailNotifications);
    if (emailNotificationsCheckbox) {
        emailNotificationsCheckbox.addEventListener('change', () => {
            const enabled = emailNotificationsCheckbox.checked;
            setEmailNotificationsAJAX(cmid, userid, enabled)
                .catch(err => notification.exception(err))
                .then(() => {
                    return getString('notification_userprefs_updated', 'mod_vocabcoach').then(msg => {
                        const msgData = {
                            type: "success",
                            message: msg
                        };
                        notification.addNotification(msgData);
                    });
                });
        });
    }
}

/**
 * Shows an info notification with the given string.
 *
 * @param {String} key The string identifier in mod_vocabcoach.
 * @param {*} [param=null] Optional parameter for the string.
 */
function notifyInfo(key, param = null) {
    getString(key, 'mod_vocabcoach', param).then((message) => {
        return notification.addNotification({type: 'info', message: message});
    }).catch((err) => notification.exception(err));
}

/**
 * Opens a check for the given box, or explains why it cannot be started.
 *
 * @param {Number} cmid The course module id.
 * @param {HTMLElement} box The box element that was clicked, carrying the data-* counts.
 * @param {Boolean} [force=false] Whether to check the whole box instead of the due items only.
 */
function checkBox(cmid, box, force = false) {
    if (parseInt(box.getAttribute('data-total')) === 0) {
        notifyInfo('view_box_empty');
    } else if (force && parseInt(box.getAttribute('data-due')) > 0) {
        notifyInfo('view_box_due_first');
    } else if (!force && parseInt(box.getAttribute('data-due')) === 0) {
        notifyInfo('view_box_all_done', box.getAttribute('data-next-due'));
    } else {
        const stage = box.getAttribute('data-stage');
        location.href = 'check.php?id=' + cmid + '&stage=' + stage + "&force=" + force;
    }
}

