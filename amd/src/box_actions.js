import notification from 'core/notification';

const Selectors = {
    actions: {
        checkBox: '[data-action="mod_vocabcoach/check_box"]',
        addUserVocab: '[data-action="mod_vocabcoach/add_vocab_user"]',
        addList: '[data-action="mod_vocabcoach/add_vocab_list"]',
        showLists: '[data-action="mod_vocabcoach/show_lists"]',
    }
};

export function init(cmid) {
    document.addEventListener('click', e => {
        if (e.target.closest(Selectors.actions.checkBox)) {
            const box = e.target.closest(Selectors.actions.checkBox);
            if (parseInt(box.getAttribute('data-due')) === 0) {
                const msgData = {
                    message: "In dieser Box hast du bereits alle Vokabeln gelernt.",
                    type: "info",
                };
                notification.addNotification(msgData);
            } else {
                const stage = box.getAttribute('data-stage');
                location.href = 'check.php?id=' + cmid + '&stage=' + stage;
            }
        } else if (e.target.closest(Selectors.actions.addUserVocab)) {
            location.href = 'add_vocab.php?id=' + cmid + '&mode=user';
        } else if (e.target.closest(Selectors.actions.addList)) {
            location.href = 'add_vocab.php?id=' + cmid + '&mode=list';
        } else if (e.target.closest(Selectors.actions.showLists)) {
            location.href = 'lists.php?id=' + cmid;
        }
    });
}