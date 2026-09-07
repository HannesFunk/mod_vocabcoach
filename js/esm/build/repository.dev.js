var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { fetchOne } from "@moodle/lms/core/ajax";
const updateVocab = /* @__PURE__ */ __name((id, cmid, known) => fetchOne({
  methodname: "mod_vocabcoach_update_vocab",
  args: { id, cmid, known }
}), "updateVocab");
const addVocabsToList = /* @__PURE__ */ __name((listid, cmid, vocabsToAdd) => fetchOne({
  methodname: "mod_vocabcoach_add_vocabs_to_list",
  args: {
    cmid,
    listid,
    vocabs: vocabsToAdd
  }
}), "addVocabsToList");
const addVocabsToUser = /* @__PURE__ */ __name((cmid, vocabsToAdd) => fetchOne({
  methodname: "mod_vocabcoach_add_vocabs_to_user",
  args: {
    cmid,
    vocabs: vocabsToAdd
  }
}), "addVocabsToUser");
const deleteVocabFromUser = /* @__PURE__ */ __name((cmid, vocabid) => fetchOne({
  methodname: "mod_vocabcoach_delete_vocab_from_user",
  args: {
    cmid,
    vocabid
  }
}), "deleteVocabFromUser");
const editUserVocab = /* @__PURE__ */ __name((id, cmid, front, back) => fetchOne({
  methodname: "mod_vocabcoach_edit_user_vocab",
  args: { vocabid: id, cmid, front, back }
}), "editUserVocab");
export {
  addVocabsToList,
  addVocabsToUser,
  deleteVocabFromUser,
  editUserVocab,
  updateVocab
};
//# sourceMappingURL=repository.dev.js.map
