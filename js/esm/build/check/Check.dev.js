var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { jsxDEV } from "react/jsx-dev-runtime";
import { useState } from "react";
import CheckCard from "./CheckCard";
import CheckSettings from "./CheckSettings";
import TypeArea from "@moodle/lms/mod_vocabcoach/check/TypeArea";
import ProgressDots from "@moodle/lms/mod_vocabcoach/check/ProgressDots";
import MoodleString from "@moodle/lms/core/String";
import VocabAction from "@moodle/lms/mod_vocabcoach/check/VocabActions";
import { deleteVocabFromUser, editUserVocab, updateVocab } from "@moodle/lms/mod_vocabcoach/repository";
import { requireAsync } from "@moodle/lms/core/amd";
import { isMoodleAjaxError } from "@moodle/lms/core/ajax";
import { getString } from "@moodle/lms/core/stringUtils";
import EditVocabForm from "@moodle/lms/mod_vocabcoach/check/EditVocabForm";
import { relativeUrl } from "@moodle/lms/core/url";
function Check({ items, cmid, modelabels, currentmode, buttonLabels, fromList }) {
  const [vocabs, setVocabs] = useState(items);
  const [mode, setMode] = useState(currentmode);
  const [outcomes, setOutcomes] = useState([]);
  const [editing, setEditing] = useState(false);
  const current = vocabs[0];
  let remaining = vocabs.length;
  if (remaining === 0) {
    const knownCount = outcomes.filter((o) => o).length;
    return /* @__PURE__ */ jsxDEV("div", { className: "check-summary", children: [
      /* @__PURE__ */ jsxDEV(
        MoodleString,
        {
          identifier: "check_summary_result",
          component: "mod_vocabcoach",
          params: { known: knownCount, total: outcomes.length }
        },
        void 0,
        false,
        {
          fileName: "public/mod/vocabcoach/js/esm/src/check/Check.tsx",
          lineNumber: 69,
          columnNumber: 17
        },
        this
      ),
      /* @__PURE__ */ jsxDEV("a", { className: "btn btn-primary mt-3", href: relativeUrl("/mod/vocabcoach/view.php", { id: cmid }), children: /* @__PURE__ */ jsxDEV(MoodleString, { identifier: "back", component: "mod_vocabcoach" }, void 0, false, {
        fileName: "public/mod/vocabcoach/js/esm/src/check/Check.tsx",
        lineNumber: 72,
        columnNumber: 21
      }, this) }, void 0, false, {
        fileName: "public/mod/vocabcoach/js/esm/src/check/Check.tsx",
        lineNumber: 71,
        columnNumber: 17
      }, this)
    ] }, void 0, true, {
      fileName: "public/mod/vocabcoach/js/esm/src/check/Check.tsx",
      lineNumber: 68,
      columnNumber: 13
    }, this);
  }
  return /* @__PURE__ */ jsxDEV("div", { id: "check-container", children: [
    /* @__PURE__ */ jsxDEV("div", { id: "check-header", className: "d-flex justify-content-between align-items-center mb-2", children: [
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
          lineNumber: 81,
          columnNumber: 17
        },
        this
      ),
      /* @__PURE__ */ jsxDEV("div", { children: [
        remaining,
        " left"
      ] }, void 0, true, {
        fileName: "public/mod/vocabcoach/js/esm/src/check/Check.tsx",
        lineNumber: 83,
        columnNumber: 17
      }, this)
    ] }, void 0, true, {
      fileName: "public/mod/vocabcoach/js/esm/src/check/Check.tsx",
      lineNumber: 80,
      columnNumber: 13
    }, this),
    /* @__PURE__ */ jsxDEV(ProgressDots, { total: items.length, outcomes }, void 0, false, {
      fileName: "public/mod/vocabcoach/js/esm/src/check/Check.tsx",
      lineNumber: 85,
      columnNumber: 13
    }, this),
    fromList ? "" : /* @__PURE__ */ jsxDEV(VocabAction, { onDelete: () => deleteVocab(current), onEdit: () => setEditing(true) }, void 0, false, {
      fileName: "public/mod/vocabcoach/js/esm/src/check/Check.tsx",
      lineNumber: 88,
      columnNumber: 19
    }, this),
    editing ? /* @__PURE__ */ jsxDEV(EditVocabForm, { vocab: current, onSave: handleSaveEdit, onCancel: () => setEditing(false) }, void 0, false, {
      fileName: "public/mod/vocabcoach/js/esm/src/check/Check.tsx",
      lineNumber: 90,
      columnNumber: 19
    }, this) : mode === "type" ? /* @__PURE__ */ jsxDEV(
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
        lineNumber: 92,
        columnNumber: 21
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
        lineNumber: 94,
        columnNumber: 23
      },
      this
    )
  ] }, void 0, true, {
    fileName: "public/mod/vocabcoach/js/esm/src/check/Check.tsx",
    lineNumber: 79,
    columnNumber: 9
  }, this);
  async function handleAnswer(known) {
    setVocabs((prev) => prev.slice(1));
    setOutcomes((prev) => [...prev, known]);
    if (fromList) {
      return;
    }
    try {
      await updateVocab(current.id, cmid, known);
    } catch (err) {
      await notifyError(err);
    }
  }
  __name(handleAnswer, "handleAnswer");
  async function deleteVocab(vocab) {
    const Notification = await requireAsync("core/notification");
    try {
      await Notification.deleteCancelPromise(
        getString("confirm", "core"),
        getString("confirm_delete_vocab", "mod_vocabcoach"),
        getString("delete", "core")
      );
    } catch {
      return;
    }
    try {
      await deleteVocabFromUser(cmid, vocab.id);
      setVocabs((prev) => prev.filter((v) => v !== vocab));
    } catch (err) {
      await notifyError(err);
    }
  }
  __name(deleteVocab, "deleteVocab");
  function handleModeChange(newMode) {
    setMode(newMode);
  }
  __name(handleModeChange, "handleModeChange");
  async function notifyError(err) {
    const Notification = await requireAsync("core/notification");
    if (isMoodleAjaxError(err)) {
      await Notification.exception(err);
      return;
    } else {
      await Notification.addNotification({
        message: err instanceof Error ? err.message : String(err),
        type: "error"
      });
    }
  }
  __name(notifyError, "notifyError");
  async function handleSaveEdit(front, back) {
    try {
      const result = await editUserVocab(current.id, cmid, front, back);
      setVocabs((prev) => [{ id: result.id, front: result.front, back: result.back }, ...prev.slice(1)]);
      setEditing(false);
    } catch (err) {
      await notifyError(err);
    }
  }
  __name(handleSaveEdit, "handleSaveEdit");
}
__name(Check, "Check");
export {
  Check as default
};
//# sourceMappingURL=Check.dev.js.map
