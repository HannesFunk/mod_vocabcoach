var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { jsxDEV } from "react/jsx-dev-runtime";
import { useState } from "react";
import MoodleString from "@moodle/lms/core/String";
import { VocabRow } from "./VocabRow";
import { addVocabsToList, addVocabsToUser } from "@moodle/lms/mod_vocabcoach/repository";
import { requireAsync } from "@moodle/lms/core/amd";
import { isMoodleAjaxError } from "@moodle/lms/core/ajax";
function AddVocab({ vocabs, cmid, listid, placeholders }) {
  const newRow = /* @__PURE__ */ __name(() => ({ key: crypto.randomUUID(), front: "", back: "" }), "newRow");
  const [items, setItems] = useState(() => {
    let vocabNew = vocabs.map((v) => {
      return { front: v.front, back: v.back, key: crypto.randomUUID() };
    });
    return [...vocabNew, newRow()];
  });
  return /* @__PURE__ */ jsxDEV("div", { children: [
    /* @__PURE__ */ jsxDEV("fieldset", { className: "clearfix fitem collapsible", children: [
      /* @__PURE__ */ jsxDEV("legend", { children: /* @__PURE__ */ jsxDEV(MoodleString, { identifier: "vocabplural", component: "mod_vocabcoach" }, void 0, false, {
        fileName: "public/mod/vocabcoach/js/esm/src/addvocab/AddVocab.tsx",
        lineNumber: 43,
        columnNumber: 25
      }, this) }, void 0, false, {
        fileName: "public/mod/vocabcoach/js/esm/src/addvocab/AddVocab.tsx",
        lineNumber: 43,
        columnNumber: 17
      }, this),
      /* @__PURE__ */ jsxDEV("div", { id: "rowsContainer", children: items.map(
        (item, i) => /* @__PURE__ */ jsxDEV(
          VocabRow,
          {
            vocab: item,
            placeholders,
            onChange: (field, value) => handleVocabChange(item.key, field, value)
          },
          void 0,
          false,
          {
            fileName: "public/mod/vocabcoach/js/esm/src/addvocab/AddVocab.tsx",
            lineNumber: 47,
            columnNumber: 25
          },
          this
        )
      ) }, void 0, false, {
        fileName: "public/mod/vocabcoach/js/esm/src/addvocab/AddVocab.tsx",
        lineNumber: 44,
        columnNumber: 17
      }, this)
    ] }, void 0, true, {
      fileName: "public/mod/vocabcoach/js/esm/src/addvocab/AddVocab.tsx",
      lineNumber: 42,
      columnNumber: 13
    }, this),
    /* @__PURE__ */ jsxDEV("button", { className: "btn btn-primary", onClick: () => submitVocab(), children: "Submit" }, void 0, false, {
      fileName: "public/mod/vocabcoach/js/esm/src/addvocab/AddVocab.tsx",
      lineNumber: 52,
      columnNumber: 13
    }, this)
  ] }, void 0, true, {
    fileName: "public/mod/vocabcoach/js/esm/src/addvocab/AddVocab.tsx",
    lineNumber: 41,
    columnNumber: 9
  }, this);
  function handleVocabChange(key, field, value) {
    setItems((prev) => {
      const newItems = prev.map((item) => item.key === key ? { ...item, [field]: value } : item);
      const lastRow = newItems[newItems.length - 1];
      return lastRow.front === "" && lastRow.back === "" ? newItems : [...newItems, newRow()];
    });
  }
  __name(handleVocabChange, "handleVocabChange");
  async function submitVocab() {
    let vocabsToAdd = items.filter(
      (item) => item.front !== "" || item.back !== ""
    ).map(
      ({ key, ...rest }) => rest
    );
    try {
      if (listid) {
        await addVocabsToList(listid, cmid, vocabsToAdd);
      } else {
        await addVocabsToUser(cmid, vocabsToAdd);
      }
    } catch (err) {
      const Notification = await requireAsync("core/notification");
      if (isMoodleAjaxError(err)) {
        await Notification.exception(err);
      } else {
        await Notification.addNotification({
          message: err instanceof Error ? err.message : typeof err === "object" && err !== null && "message" in err ? String(err.message) : JSON.stringify(err),
          type: "error"
        });
      }
    }
  }
  __name(submitVocab, "submitVocab");
}
__name(AddVocab, "AddVocab");
export {
  AddVocab as default
};
//# sourceMappingURL=AddVocab.dev.js.map
