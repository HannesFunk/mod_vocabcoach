/**
 * Web service calls used by the mod_vocabcoach modules.
 *
 * @module     mod_vocabcoach/repository
 * @copyright  2023 J. Funk <johannesfunk@outlook.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {call as fetchMany} from 'core/ajax';

/**
 * Records whether the user knew a vocabulary item and moves it to the next box stage.
 *
 * @param {Number} vocabid The id of the user's vocabulary record.
 * @param {Number} userid The id of the user the record belongs to.
 * @param {Boolean} known Whether the user answered the item correctly.
 * @returns {Promise} Resolves once the record has been updated.
 */
export const updateVocabAJAX = (
    vocabid, userid, known
) => fetchMany([{
    methodname: 'mod_vocabcoach_update_vocab',
    args: {
        dataid: vocabid,
        userid: userid,
        known: known
    },
}])[0];

/**
 * Saves an edited vocabulary item belonging to a user.
 *
 * @param {Object} updatedVocab The vocabulary item, with front, back and dataid properties.
 * @returns {Promise<Object>} Resolves with the new dataid, or -1 if the item could not be saved.
 */
export const editUserVocabAJAX = (
    updatedVocab,
) => fetchMany([{
    methodname: 'mod_vocabcoach_edit_user_vocab',
    args: {
        front: updatedVocab.front,
        back: updatedVocab.back,
        dataid: updatedVocab.dataid
    },
}])[0];

/**
 * Fetches the vocabulary items in one stage of a user's box.
 *
 * @param {Number} userid The id of the user whose box is read.
 * @param {Number} cmid The course module id.
 * @param {Number} stage The box stage to read.
 * @param {Boolean} force Whether to include items that are not due yet.
 * @returns {Promise<Array>} Resolves with the vocabulary items.
 */
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

/**
 * Fetches the vocabulary lists available in this activity.
 *
 * @param {Number} cmid The course module id.
 * @param {Number} userid The id of the user the lists are fetched for.
 * @param {Boolean} onlyOwnLists Whether to limit the result to lists created by that user.
 * @returns {Promise<Array>} Resolves with the lists.
 */
export const getListsAJAX = (
    cmid,
    userid,
    onlyOwnLists
) => fetchMany([{
    methodname: 'mod_vocabcoach_get_lists',
    args: {
        'cmid': cmid,
        'userid': userid,
        'onlyownlists': onlyOwnLists
    },
}])[0];

/**
 * Deletes a vocabulary list.
 *
 * @param {Number} listid The id of the list to delete.
 * @returns {Promise} Resolves once the list has been deleted.
 */
export const deleteListAJAX = (
    listid
) => fetchMany([{
    methodname: 'mod_vocabcoach_delete_list',
    args: {
        'listid': listid
    },
}])[0];

/**
 * Adds all vocabulary items of a list to a user's box.
 *
 * @param {Number} listid The id of the list to add.
 * @param {Number} userid The id of the user receiving the items.
 * @param {Number} cmid The course module id.
 * @returns {Promise} Resolves once the items have been added.
 */
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

/**
 * Adds a vocabulary list to the boxes of all participants of the activity.
 *
 * @param {Number} listid The id of the list to distribute.
 * @param {Number} cmid The course module id.
 * @returns {Promise} Resolves once the list has been distributed.
 */
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

/**
 * Removes a vocabulary item from the user's box.
 *
 * @param {Number} dataid The id of the user's vocabulary record.
 * @returns {Promise<Object>} Resolves with a success flag.
 */
export const removeVocabFromUserAJAX = (
    dataid) =>
    fetchMany([{
        methodname: 'mod_vocabcoach_remove_vocab_from_user',
        args: {
            'dataid': dataid
        },
    }])[0];

/**
 * Fetches the number of vocabulary items the whole course has practised.
 *
 * @param {Number} cmid The course module id.
 * @param {Number} courseid The course id.
 * @returns {Promise<Object>} Resolves with the total, or -1 if it is not available.
 */
export const getClassTotalAJAX = (
    cmid, courseid) =>
    fetchMany([{
        methodname: 'mod_vocabcoach_get_class_total',
        args: {
            'cmid': cmid,
            'courseid': courseid
        },
    }])[0];

/**
 * Stores the user's preferred check mode.
 *
 * @param {Number} cmid The course module id.
 * @param {Number} userid The id of the user.
 * @param {String} mode The check mode to store.
 * @returns {Promise} Resolves once the preference has been saved.
 */
export const setCheckModeAJAX = (
    cmid,
    userid,
    mode
) => fetchMany([{
    methodname: 'mod_vocabcoach_set_checkmode',
    args: {
        cmid,
        userid,
        mode,
    },
}])[0];

/**
 * Stores whether the user wants to receive email reminders.
 *
 * @param {Number} cmid The course module id.
 * @param {Number} userid The id of the user.
 * @param {Boolean} enabled Whether reminders are enabled.
 * @returns {Promise} Resolves once the preference has been saved.
 */
export const setEmailNotificationsAJAX = (
    cmid,
    userid,
    enabled
) => fetchMany([{
    methodname: 'mod_vocabcoach_set_email_notifications',
    args: {
        cmid,
        userid,
        enabled,
    },
}])[0];