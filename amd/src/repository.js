import {call as fetchMany} from 'core/ajax';

export const updateVocabAJAX = (
    vocabid, userid, known
) => fetchMany ([{
    methodname: 'mod_vocabcoach_update_vocab',
    args: {
        dataid: vocabid,
        userid: userid,
        known: known
    },
}])[0];

export const getBoxArrayAJAX = (
    userid,
    cmid,
    stage,
    force
) => fetchMany([{
    methodname: 'mod_vocabcoach_get_user_vocabs',
    args: {
        userid,
        cmid,
        stage,
        force
    },
}])[0];

export const getListArrayAJAX = (
    listid
) => fetchMany([{
    methodname: 'mod_vocabcoach_get_list_vocabs',
    args: {
        listid
    },
}])[0];
export const getListsAJAX = (
    cmid
) => fetchMany([{
    methodname: 'mod_vocabcoach_get_lists',
    args: {
        'cmid': cmid
    },
}])[0];

export const deleteListAJAX = (
    listid
) => fetchMany([{
    methodname: 'mod_vocabcoach_delete_list',
    args: {
        'listid': listid
    },
}])[0];

export const addListToUserAJAX = (
    listid,
    userid,
    cmid
) => fetchMany([{
    methodname: 'mod_vocabcoach_add_list_to_user',
    args: {
        'listid': listid,
        'userid': userid,
        'cmid': cmid
    },
}])[0];
export const distributeListAJAX = (
    listid,
    cmid
) => fetchMany([{
    methodname: 'mod_vocabcoach_distribute_list',
    args: {
        'listid': listid,
        'cmid': cmid
    },
}])[0];

export const getFeedbackLineAJAX = (
    achievement
) => fetchMany([{
    methodname: 'mod_vocabcoach_get_feedback_line',
    args: {
        'achievement': achievement
    },
}])[0];


