var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { Fragment, jsxDEV } from "react/jsx-dev-runtime";
import { useState } from "react";
function TypeArea({ vocab, onAnswer, buttonLabels }) {
  const [revealed, setRevealed] = useState(false);
  const [typedInput, setTypedInput] = useState("");
  return /* @__PURE__ */ jsxDEV(Fragment, { children: [
    /* @__PURE__ */ jsxDEV("form", { id: "check-type-area", onSubmit: (e) => onSubmit(e), children: [
      /* @__PURE__ */ jsxDEV("label", { htmlFor: "input-vocab-front" }, void 0, false, {
        fileName: "public/mod/vocabcoach/js/esm/src/check/TypeArea.tsx",
        lineNumber: 32,
        columnNumber: 17
      }, this),
      /* @__PURE__ */ jsxDEV(
        "input",
        {
          type: "text",
          name: "input-vocab-front",
          id: "input-vocab-front",
          autoCapitalize: "off",
          autoComplete: "none",
          spellCheck: "false",
          value: typedInput,
          onChange: (e) => setTypedInput(e.target.value),
          disabled: revealed
        },
        void 0,
        false,
        {
          fileName: "public/mod/vocabcoach/js/esm/src/check/TypeArea.tsx",
          lineNumber: 33,
          columnNumber: 17
        },
        this
      ),
      revealed ? /* @__PURE__ */ jsxDEV("div", { className: "d-flex gap-2 justify-content-center", children: [
        /* @__PURE__ */ jsxDEV(
          "button",
          {
            type: "button",
            className: "btn btn-secondary",
            onClick: () => onAnswer(false),
            children: buttonLabels.typeNo
          },
          void 0,
          false,
          {
            fileName: "public/mod/vocabcoach/js/esm/src/check/TypeArea.tsx",
            lineNumber: 41,
            columnNumber: 25
          },
          this
        ),
        /* @__PURE__ */ jsxDEV(
          "button",
          {
            type: "button",
            className: "btn btn-secondary",
            onClick: () => onAnswer(true),
            children: buttonLabels.typeOverride
          },
          void 0,
          false,
          {
            fileName: "public/mod/vocabcoach/js/esm/src/check/TypeArea.tsx",
            lineNumber: 45,
            columnNumber: 25
          },
          this
        )
      ] }, void 0, true, {
        fileName: "public/mod/vocabcoach/js/esm/src/check/TypeArea.tsx",
        lineNumber: 40,
        columnNumber: 21
      }, this) : /* @__PURE__ */ jsxDEV("div", { className: "d-flex gap-2 justify-content-center", children: [
        /* @__PURE__ */ jsxDEV("button", { type: "submit", className: "btn btn-secondary", children: buttonLabels.typeCheck }, void 0, false, {
          fileName: "public/mod/vocabcoach/js/esm/src/check/TypeArea.tsx",
          lineNumber: 51,
          columnNumber: 25
        }, this),
        /* @__PURE__ */ jsxDEV(
          "button",
          {
            type: "button",
            className: "btn btn-secondary",
            onClick: () => reveal(false),
            children: buttonLabels.typeReveal
          },
          void 0,
          false,
          {
            fileName: "public/mod/vocabcoach/js/esm/src/check/TypeArea.tsx",
            lineNumber: 54,
            columnNumber: 25
          },
          this
        )
      ] }, void 0, true, {
        fileName: "public/mod/vocabcoach/js/esm/src/check/TypeArea.tsx",
        lineNumber: 50,
        columnNumber: 23
      }, this)
    ] }, void 0, true, {
      fileName: "public/mod/vocabcoach/js/esm/src/check/TypeArea.tsx",
      lineNumber: 31,
      columnNumber: 13
    }, this),
    /* @__PURE__ */ jsxDEV("div", { id: "check-box-back", className: "check-box d-flex", children: /* @__PURE__ */ jsxDEV("div", { id: "check-back", className: "check-label", children: vocab.back }, void 0, false, {
      fileName: "public/mod/vocabcoach/js/esm/src/check/TypeArea.tsx",
      lineNumber: 62,
      columnNumber: 17
    }, this) }, void 0, false, {
      fileName: "public/mod/vocabcoach/js/esm/src/check/TypeArea.tsx",
      lineNumber: 61,
      columnNumber: 13
    }, this)
  ] }, void 0, true, {
    fileName: "public/mod/vocabcoach/js/esm/src/check/TypeArea.tsx",
    lineNumber: 30,
    columnNumber: 9
  }, this);
  function onSubmit(e) {
    e.preventDefault();
    reveal(true);
  }
  __name(onSubmit, "onSubmit");
  function reveal(checkInput) {
    if (checkInput && typedInput === vocab.front) {
      onAnswer(true);
      return;
    }
    setTypedInput(vocab.front);
    setRevealed(true);
  }
  __name(reveal, "reveal");
}
__name(TypeArea, "TypeArea");
export {
  TypeArea as default
};
//# sourceMappingURL=TypeArea.dev.js.map
