import notification from 'core/notification';

const Selectors = {
    actions: {
        checkBox: '[data-action="mod_vocabcoach/check_box"]',
        addUserVocab: '[data-action="mod_vocabcoach/add_vocab_user"]',
        addList: '[data-action="mod_vocabcoach/add_vocab_list"]',
        showLists: '[data-action="mod_vocabcoach/show_lists"]',
        forceCheck: '[data-action="mod_vocabcoach/force_check_all"]',
        dropdown: '[class="dropdown"]',
    }
};

export function init(cmid) {
    document.addEventListener('click', e => {
        if (e.target.closest(Selectors.actions.dropdown)) {
            return false;
        } else if (e.target.closest(Selectors.actions.forceCheck)) {
           checkBox(cmid, e.target.closest(Selectors.actions.checkBox), true);
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
}

function checkBox(cmid, box, force = false) {
    if (parseInt(box.getAttribute('data-total')) === 0) {
        const msgData = {
            type: "info",
            message: 'In dieser Box sind zur Zeit keine Vokabeln enthalten.'
        };
        notification.addNotification(msgData).then(null);
    }
    else if (!force && parseInt(box.getAttribute('data-due')) === 0) {
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