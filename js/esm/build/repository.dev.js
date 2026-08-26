var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { fetchOne } from "@moodle/lms/core/ajax";
const updateVocab = /* @__PURE__ */ __name((id, cmid, known) => fetchOne({
  methodname: "mod_vocabcoach_update_vocab",
  args: { id, cmid, known }
}), "updateVocab");
const addVocab = /* @__PURE__ */ __name((listid, cmid, vocabsToAdd) => fetchOne({
  methodname: "mod_vocabcoach_add_vocabs_to_list",
  args: {
    cmid,
    listid,
    vocabs: vocabsToAdd
  }
}), "addVocab");
export {
  addVocab,
  updateVocab
};
//# sourceMappingURL=repository.dev.js.map
