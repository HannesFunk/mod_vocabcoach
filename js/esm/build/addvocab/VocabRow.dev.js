var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { jsxDEV } from "react/jsx-dev-runtime";
function VocabRow({ vocab, placeholders, onChange }) {
  return /* @__PURE__ */ jsxDEV("div", { className: "mb-3 row fitem", children: /* @__PURE__ */ jsxDEV("div", { className: "col-md-9 offset-md-3 d-flex align-items-center gap-3 felement", children: [
    /* @__PURE__ */ jsxDEV(
      "input",
      {
        type: "text",
        className: "form-control",
        value: vocab.front,
        placeholder: placeholders.front,
        onChange: (e) => onChange("front", e.target.value)
      },
      void 0,
      false,
      {
        fileName: "public/mod/vocabcoach/js/esm/src/addvocab/VocabRow.tsx",
        lineNumber: 30,
        columnNumber: 15
      },
      this
    ),
    /* @__PURE__ */ jsxDEV(
      "input",
      {
        type: "text",
        className: "form-control",
        value: vocab.back,
        placeholder: placeholders.back,
        onChange: (e) => onChange("back", e.target.value)
      },
      void 0,
      false,
      {
        fileName: "public/mod/vocabcoach/js/esm/src/addvocab/VocabRow.tsx",
        lineNumber: 33,
        columnNumber: 15
      },
      this
    )
  ] }, void 0, true, {
    fileName: "public/mod/vocabcoach/js/esm/src/addvocab/VocabRow.tsx",
    lineNumber: 29,
    columnNumber: 11
  }, this) }, void 0, false, {
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
