var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { jsxDEV } from "react/jsx-dev-runtime";
import { useEffect, useState } from "react";
import { VocabRow } from "./VocabRow";
import Log from "@moodle/lms/core/log";
function AddVocab({ vocabs, cmid, listid, placeholders, vocabsfieldid }) {
  const newRow = /* @__PURE__ */ __name(() => ({ key: crypto.randomUUID(), front: "", back: "" }), "newRow");
  const [items, setItems] = useState(() => {
    let vocabNew = vocabs.map((v) => {
      return { front: v.front, back: v.back, key: crypto.randomUUID() };
    });
    return [...vocabNew, newRow()];
  });
  useEffect(() => {
    const field = document.getElementById(vocabsfieldid);
    if (field instanceof HTMLInputElement) {
      field.value = JSON.stringify(
        items.filter(
          (item) => item.front !== "" || item.back !== ""
        ).map(({ key, ...rest }) => rest)
      );
    } else {
      Log.error(`Hidden vocab field #${vocabsfieldid} not found`, "mod_vocabcoach/AddVocab");
    }
  }, [items, vocabsfieldid]);
  return /* @__PURE__ */ jsxDEV("div", { id: "rowsContainer", children: items.map(
    (item, i) => /* @__PURE__ */ jsxDEV(
      VocabRow,
      {
        vocab: item,
        placeholders,
        onChange: (field, value) => handleVocabChange(item.key, field, value)
      },
      item.key,
      false,
      {
        fileName: "public/mod/vocabcoach/js/esm/src/addvocab/AddVocab.tsx",
        lineNumber: 58,
        columnNumber: 17
      },
      this
    )
  ) }, void 0, false, {
    fileName: "public/mod/vocabcoach/js/esm/src/addvocab/AddVocab.tsx",
    lineNumber: 55,
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
}
__name(AddVocab, "AddVocab");
export {
  AddVocab as default
};
//# sourceMappingURL=AddVocab.dev.js.map
