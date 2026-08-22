var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { fetchOne } from "@moodle/lms/core/ajax";
const updateVocab = /* @__PURE__ */ __name((id, cmid, known) => fetchOne({
  methodname: "mod_vocabcoach_update_vocab",
  args: { id, cmid, known }
}), "updateVocab");
export {
  updateVocab
};
//# sourceMappingURL=repository.dev.js.map
