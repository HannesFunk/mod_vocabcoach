var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { Fragment, jsxDEV } from "react/jsx-dev-runtime";
import { useState } from "react";
import CheckLabel from "@moodle/lms/mod_vocabcoach/check/CheckLabel";
function CheckCard({ vocab, mode, onAnswer, buttonLabels }) {
  const [revealed, setRevealed] = useState(false);
  const [concealed] = useState(
    () => {
      if (mode === "type") {
        return "front";
      } else if (mode === "random") {
        return Math.random() < 0.5 ? "front" : "back";
      } else {
        return mode;
      }
    }
  );
  return /* @__PURE__ */ jsxDEV(Fragment, { children: [
    /* @__PURE__ */ jsxDEV("div", { id: "check-area", children: [
      /* @__PURE__ */ jsxDEV(
        "div",
        {
          id: "check-box-front",
          className: "check-box d-flex",
          onClick: () => setRevealed(true),
          children: /* @__PURE__ */ jsxDEV(CheckLabel, { id: "check-front", text: vocab.front, concealed: concealed === "front" && !revealed }, void 0, false, {
            fileName: "public/mod/vocabcoach/js/esm/src/check/CheckCard.tsx",
            lineNumber: 64,
            columnNumber: 21
          }, this)
        },
        void 0,
        false,
        {
          fileName: "public/mod/vocabcoach/js/esm/src/check/CheckCard.tsx",
          lineNumber: 61,
          columnNumber: 17
        },
        this
      ),
      /* @__PURE__ */ jsxDEV(
        "div",
        {
          id: "check-box-back",
          className: "check-box d-flex",
          onClick: () => setRevealed(true),
          children: /* @__PURE__ */ jsxDEV(CheckLabel, { id: "check-back", text: vocab.back, concealed: concealed === "back" && !revealed }, void 0, false, {
            fileName: "public/mod/vocabcoach/js/esm/src/check/CheckCard.tsx",
            lineNumber: 70,
            columnNumber: 21
          }, this)
        },
        void 0,
        false,
        {
          fileName: "public/mod/vocabcoach/js/esm/src/check/CheckCard.tsx",
          lineNumber: 66,
          columnNumber: 17
        },
        this
      )
    ] }, void 0, true, {
      fileName: "public/mod/vocabcoach/js/esm/src/check/CheckCard.tsx",
      lineNumber: 60,
      columnNumber: 13
    }, this),
    /* @__PURE__ */ jsxDEV("div", { id: "check-buttons", children: [
      /* @__PURE__ */ jsxDEV(
        "button",
        {
          id: "check-button-no",
          className: "btn btn-secondary",
          onClick: () => onAnswer(false),
          children: buttonLabels.no
        },
        void 0,
        false,
        {
          fileName: "public/mod/vocabcoach/js/esm/src/check/CheckCard.tsx",
          lineNumber: 75,
          columnNumber: 13
        },
        this
      ),
      /* @__PURE__ */ jsxDEV(
        "button",
        {
          id: "check-button-no",
          className: "btn btn-secondary",
          onClick: () => onAnswer(true),
          children: buttonLabels.yes
        },
        void 0,
        false,
        {
          fileName: "public/mod/vocabcoach/js/esm/src/check/CheckCard.tsx",
          lineNumber: 80,
          columnNumber: 13
        },
        this
      )
    ] }, void 0, true, {
      fileName: "public/mod/vocabcoach/js/esm/src/check/CheckCard.tsx",
      lineNumber: 74,
      columnNumber: 9
    }, this)
  ] }, void 0, true, {
    fileName: "public/mod/vocabcoach/js/esm/src/check/CheckCard.tsx",
    lineNumber: 59,
    columnNumber: 9
  }, this);
}
__name(CheckCard, "CheckCard");
export {
  CheckCard as default
};
//# sourceMappingURL=CheckCard.dev.js.map
