var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { jsxDEV } from "react/jsx-dev-runtime";
import { useState } from "react";
import CheckCard from "./CheckCard";
import CheckSettings from "./CheckSettings";
import { updateVocab } from "../repository";
import { requireAsync } from "@moodle/lms/core/amd";
import { isMoodleAjaxError } from "@moodle/lms/core/ajax";
function Check({ items, cmid, modelabels, currentmode, buttonLabels, fromList }) {
  const [vocabs, setVocabs] = useState(items);
  const [mode, setMode] = useState(currentmode);
  const current = vocabs[0];
  let total = vocabs.length;
  if (total === 0) {
    return /* @__PURE__ */ jsxDEV("span", { children: "Done!" }, void 0, false, {
      fileName: "public/mod/vocabcoach/js/esm/src/check/Check.tsx",
      lineNumber: 56,
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
          fileName: "public/mod/vocabcoach/js/esm/src/check/Check.tsx",
          lineNumber: 62,
          columnNumber: 17
        },
        this
      ),
      /* @__PURE__ */ jsxDEV("div", { className: "text-end", children: [
        total,
        " left"
      ] }, void 0, true, {
        fileName: "public/mod/vocabcoach/js/esm/src/check/Check.tsx",
        lineNumber: 64,
        columnNumber: 17
      }, this)
    ] }, void 0, true, {
      fileName: "public/mod/vocabcoach/js/esm/src/check/Check.tsx",
      lineNumber: 61,
      columnNumber: 13
    }, this),
    /* @__PURE__ */ jsxDEV(
      CheckCard,
      {
        vocab: current,
        mode,
        buttonLabels,
        onAnswer: handleAnswer
      },
      `${current.id}-${mode}`,
      false,
      {
        fileName: "public/mod/vocabcoach/js/esm/src/check/Check.tsx",
        lineNumber: 66,
        columnNumber: 13
      },
      this
    )
  ] }, void 0, true, {
    fileName: "public/mod/vocabcoach/js/esm/src/check/Check.tsx",
    lineNumber: 60,
    columnNumber: 9
  }, this);
  async function handleAnswer(known) {
    setVocabs((prev) => prev.slice(1));
    if (fromList) {
      return;
    }
    try {
      await updateVocab(current.id, cmid, known);
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
//# sourceMappingURL=Check.dev.js.map
