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
    stage
) => fetchMany([{
    methodname: 'mod_vocabcoach_get_user_vocabs',
    args: {
        userid,
        cmid,
        stage
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


