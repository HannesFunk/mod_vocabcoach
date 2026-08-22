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
import { updateVocab } from "./repository";
import { requireAsync } from "@moodle/lms/core/amd";
import { isMoodleAjaxError } from "@moodle/lms/core/ajax";
function Check({ items, cmid, modelabels, currentmode }) {
  const [vocabs, setVocabs] = useState(items);
  const [mode, setMode] = useState(currentmode);
  const current = vocabs[0];
  let total = vocabs.length;
  if (total === 0) {
    return /* @__PURE__ */ jsxDEV("span", { children: "Done!" }, void 0, false, {
      fileName: "public/mod/vocabcoach/js/esm/src/check.tsx",
      lineNumber: 53,
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
          lineNumber: 59,
          columnNumber: 17
        },
        this
      ),
      /* @__PURE__ */ jsxDEV("div", { className: "text-end", children: [
        total,
        " left"
      ] }, void 0, true, {
        fileName: "public/mod/vocabcoach/js/esm/src/check.tsx",
        lineNumber: 61,
        columnNumber: 17
      }, this)
    ] }, void 0, true, {
      fileName: "public/mod/vocabcoach/js/esm/src/check.tsx",
      lineNumber: 58,
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
        lineNumber: 63,
        columnNumber: 13
      },
      this
    )
  ] }, void 0, true, {
    fileName: "public/mod/vocabcoach/js/esm/src/check.tsx",
    lineNumber: 57,
    columnNumber: 9
  }, this);
  async function handleAnswer(known) {
    try {
      await updateVocab(current.id, cmid, known);
      setVocabs((prev) => prev.slice(1));
    } catch (err) {
      const Notification = await requireAsync("core/notification");
      if (isMoodleAjaxError(err)) {
        await Notification.exception(err);
      } else {
        await Notification.addNotification({
          message: err instanceof Error ? err.message : String(err),
          type: "error"
        });
      }
    }
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
