var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { Fragment, jsxDEV } from "react/jsx-dev-runtime";
/**
 * React component that render one vocab card with front / back
 *
 * @module     mod_vocabcoach/check
 * @copyright  2026 J. Funk, johannesfunk@outlook.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import { useState } from "react";
import String from "@moodle/lms/core/String";
function CheckCard({ vocab, mode, onAnswer }) {
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
          children: /* @__PURE__ */ jsxDEV(
            "div",
            {
              id: "check-front",
              className: `check-label ${concealed === "front" && !revealed ? "visually-hidden" : ""}`,
              children: vocab.front
            },
            void 0,
            false,
            {
              fileName: "public/mod/vocabcoach/js/esm/src/CheckCard.tsx",
              lineNumber: 56,
              columnNumber: 21
            },
            this
          )
        },
        void 0,
        false,
        {
          fileName: "public/mod/vocabcoach/js/esm/src/CheckCard.tsx",
          lineNumber: 53,
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
          children: /* @__PURE__ */ jsxDEV(
            "div",
            {
              id: "check-back",
              className: `check-label ${concealed === "back" && !revealed ? "visually-hidden" : ""}`,
              children: vocab.back
            },
            void 0,
            false,
            {
              fileName: "public/mod/vocabcoach/js/esm/src/CheckCard.tsx",
              lineNumber: 65,
              columnNumber: 21
            },
            this
          )
        },
        void 0,
        false,
        {
          fileName: "public/mod/vocabcoach/js/esm/src/CheckCard.tsx",
          lineNumber: 61,
          columnNumber: 17
        },
        this
      )
    ] }, void 0, true, {
      fileName: "public/mod/vocabcoach/js/esm/src/CheckCard.tsx",
      lineNumber: 52,
      columnNumber: 13
    }, this),
    /* @__PURE__ */ jsxDEV("div", { id: "check-buttons", children: [
      /* @__PURE__ */ jsxDEV(
        "button",
        {
          id: "check-button-no",
          className: "btn btn-secondary",
          onClick: () => onAnswer(false),
          children: /* @__PURE__ */ jsxDEV(String, { identifier: "check_button_no", component: "mod_vocabcoach" }, void 0, false, {
            fileName: "public/mod/vocabcoach/js/esm/src/CheckCard.tsx",
            lineNumber: 76,
            columnNumber: 17
          }, this)
        },
        void 0,
        false,
        {
          fileName: "public/mod/vocabcoach/js/esm/src/CheckCard.tsx",
          lineNumber: 73,
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
          children: /* @__PURE__ */ jsxDEV(String, { identifier: "check_button_yes", component: "mod_vocabcoach" }, void 0, false, {
            fileName: "public/mod/vocabcoach/js/esm/src/CheckCard.tsx",
            lineNumber: 81,
            columnNumber: 17
          }, this)
        },
        void 0,
        false,
        {
          fileName: "public/mod/vocabcoach/js/esm/src/CheckCard.tsx",
          lineNumber: 78,
          columnNumber: 13
        },
        this
      )
    ] }, void 0, true, {
      fileName: "public/mod/vocabcoach/js/esm/src/CheckCard.tsx",
      lineNumber: 72,
      columnNumber: 9
    }, this)
  ] }, void 0, true, {
    fileName: "public/mod/vocabcoach/js/esm/src/CheckCard.tsx",
    lineNumber: 51,
    columnNumber: 9
  }, this);
}
__name(CheckCard, "CheckCard");
export {
  CheckCard as default
};
//# sourceMappingURL=CheckCard.dev.js.map
