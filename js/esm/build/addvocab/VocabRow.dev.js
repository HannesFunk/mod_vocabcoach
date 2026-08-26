var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { jsxDEV } from "react/jsx-dev-runtime";
function VocabRow({ vocab, placeholders, onChange }) {
  return /* @__PURE__ */ jsxDEV("div", { className: "d-flex align-items-center gap-3 m-2", children: [
    /* @__PURE__ */ jsxDEV(
      "input",
      {
        type: "text",
        className: "form-control",
        name: "front[]",
        value: vocab.front,
        placeholder: placeholders.front,
        onChange: (e) => onChange("front", e.target.value)
      },
      void 0,
      false,
      {
        fileName: "public/mod/vocabcoach/js/esm/src/addvocab/VocabRow.tsx",
        lineNumber: 29,
        columnNumber: 11
      },
      this
    ),
    /* @__PURE__ */ jsxDEV(
      "input",
      {
        type: "text",
        className: "form-control",
        name: "back[]",
        value: vocab.back,
        placeholder: placeholders.back,
        onChange: (e) => onChange("back", e.target.value)
      },
      void 0,
      false,
      {
        fileName: "public/mod/vocabcoach/js/esm/src/addvocab/VocabRow.tsx",
        lineNumber: 32,
        columnNumber: 11
      },
      this
    )
  ] }, void 0, true, {
    fileName: "public/mod/vocabcoach/js/esm/src/addvocab/VocabRow.tsx",
    lineNumber: 28,
    columnNumber: 7
  }, this);
}
__name(VocabRow, "VocabRow");
export {
  VocabRow
};
//# sourceMappingURL=VocabRow.dev.js.map
