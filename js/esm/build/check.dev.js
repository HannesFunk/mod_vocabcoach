var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { jsxDEV } from "react/jsx-dev-runtime";
/**
 * React component that administers the check page.
 *
 * @module     mod_vocabcoach/check
 * @copyright  2026 J. Funk, johannesfunk@outlook.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import { useState } from "react";
import CheckCard from "./CheckCard";
import CheckSettings from "./CheckSettings";
function Check({ items, cmid, modelabels, currentmode }) {
  const [vocabs, setVocabs] = useState(items);
  const [mode, setMode] = useState(currentmode);
  const current = vocabs[0];
  let total = vocabs.length;
  if (total === 0) {
    return /* @__PURE__ */ jsxDEV("span", { children: "Done!" }, void 0, false, {
      fileName: "public/mod/vocabcoach/js/esm/src/check.tsx",
      lineNumber: 45,
      columnNumber: 17
    }, this);
  }
  return /* @__PURE__ */ jsxDEV("div", { id: "check-container", children: [
    /* @__PURE__ */ jsxDEV("div", { id: "check-header", children: [
      /* @__PURE__ */ jsxDEV(
        CheckSettings,
        {
          currentmode: mode,
          modelabels,
          onModeChange: handleModeChange
        },
        void 0,
        false,
        {
          fileName: "public/mod/vocabcoach/js/esm/src/check.tsx",
          lineNumber: 51,
          columnNumber: 17
        },
        this
      ),
      /* @__PURE__ */ jsxDEV("div", { className: "text-end", children: [
        total,
        " left"
      ] }, void 0, true, {
        fileName: "public/mod/vocabcoach/js/esm/src/check.tsx",
        lineNumber: 53,
        columnNumber: 17
      }, this)
    ] }, void 0, true, {
      fileName: "public/mod/vocabcoach/js/esm/src/check.tsx",
      lineNumber: 50,
      columnNumber: 13
    }, this),
    /* @__PURE__ */ jsxDEV(
      CheckCard,
      {
        vocab: current,
        mode,
        onAnswer: handleAnswer
      },
      `${current.id}-${mode}`,
      false,
      {
        fileName: "public/mod/vocabcoach/js/esm/src/check.tsx",
        lineNumber: 55,
        columnNumber: 13
      },
      this
    )
  ] }, void 0, true, {
    fileName: "public/mod/vocabcoach/js/esm/src/check.tsx",
    lineNumber: 49,
    columnNumber: 9
  }, this);
  function handleAnswer(known) {
    setVocabs((prev) => prev.slice(1));
  }
  __name(handleAnswer, "handleAnswer");
  function handleModeChange(newMode) {
    setMode(newMode);
  }
  __name(handleModeChange, "handleModeChange");
}
__name(Check, "Check");
export {
  Check as default
};
//# sourceMappingURL=check.dev.js.map
