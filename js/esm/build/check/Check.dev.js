var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { jsxDEV } from "react/jsx-dev-runtime";
import { useState } from "react";
import CheckCard from "./CheckCard";
import CheckSettings from "./CheckSettings";
import { updateVocab } from "../repository";
import { requireAsync } from "@moodle/lms/core/amd";
import { isMoodleAjaxError } from "@moodle/lms/core/ajax";
import TypeArea from "@moodle/lms/mod_vocabcoach/check/TypeArea";
import ProgressDots from "@moodle/lms/mod_vocabcoach/check/ProgressDots";
import String from "@moodle/lms/core/String";
function Check({ items, cmid, modelabels, currentmode, buttonLabels, fromList }) {
  const [vocabs, setVocabs] = useState(items);
  const [mode, setMode] = useState(currentmode);
  const [outcomes, setOutcomes] = useState([]);
  const current = vocabs[0];
  let remaining = vocabs.length;
  if (remaining === 0) {
    const knownCount = outcomes.filter((o) => o === "known").length;
    return /* @__PURE__ */ jsxDEV("div", { className: "check-summary-mesasge", children: /* @__PURE__ */ jsxDEV(
      String,
      {
        identifier: "check_summary_result",
        component: "mod_vocabcoach",
        params: { known: knownCount, total: outcomes.length }
      },
      void 0,
      false,
      {
        fileName: "public/mod/vocabcoach/js/esm/src/check/Check.tsx",
        lineNumber: 63,
        columnNumber: 17
      },
      this
    ) }, void 0, false, {
      fileName: "public/mod/vocabcoach/js/esm/src/check/Check.tsx",
      lineNumber: 62,
      columnNumber: 13
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
          lineNumber: 72,
          columnNumber: 17
        },
        this
      ),
      /* @__PURE__ */ jsxDEV("div", { className: "text-end", children: [
        remaining,
        " left"
      ] }, void 0, true, {
        fileName: "public/mod/vocabcoach/js/esm/src/check/Check.tsx",
        lineNumber: 74,
        columnNumber: 17
      }, this)
    ] }, void 0, true, {
      fileName: "public/mod/vocabcoach/js/esm/src/check/Check.tsx",
      lineNumber: 71,
      columnNumber: 13
    }, this),
    /* @__PURE__ */ jsxDEV(ProgressDots, { total: items.length, outcomes }, void 0, false, {
      fileName: "public/mod/vocabcoach/js/esm/src/check/Check.tsx",
      lineNumber: 76,
      columnNumber: 13
    }, this),
    mode === "type" ? /* @__PURE__ */ jsxDEV(
      TypeArea,
      {
        vocab: current,
        onAnswer: handleAnswer,
        buttonLabels
      },
      `${current.id}-${mode}`,
      false,
      {
        fileName: "public/mod/vocabcoach/js/esm/src/check/Check.tsx",
        lineNumber: 78,
        columnNumber: 17
      },
      this
    ) : /* @__PURE__ */ jsxDEV(
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
        lineNumber: 80,
        columnNumber: 19
      },
      this
    )
  ] }, void 0, true, {
    fileName: "public/mod/vocabcoach/js/esm/src/check/Check.tsx",
    lineNumber: 70,
    columnNumber: 9
  }, this);
  async function handleAnswer(known) {
    setVocabs((prev) => prev.slice(1));
    const outcome = known ? "known" : "unknown";
    setOutcomes((prev) => [...prev, outcome]);
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
