var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { jsxDEV } from "react/jsx-dev-runtime";
import { useId, useState } from "react";
import MoodleString from "@moodle/lms/core/String";
function EditVocabForm({ vocab, onSave, onCancel, saving = false }) {
  const [front, setFront] = useState(vocab.front);
  const [back, setBack] = useState(vocab.back);
  const frontid = useId();
  const backid = useId();
  const isComplete = front.trim() !== "" && back.trim() !== "";
  return /* @__PURE__ */ jsxDEV("form", { className: "check-edit-form", onSubmit: handleSubmit, children: [
    /* @__PURE__ */ jsxDEV("h3", { className: "h5", children: /* @__PURE__ */ jsxDEV(MoodleString, { identifier: "edit_vocab_title", component: "mod_vocabcoach" }, void 0, false, {
      fileName: "public/mod/vocabcoach/js/esm/src/check/EditVocabForm.tsx",
      lineNumber: 39,
      columnNumber: 17
    }, this) }, void 0, false, {
      fileName: "public/mod/vocabcoach/js/esm/src/check/EditVocabForm.tsx",
      lineNumber: 38,
      columnNumber: 13
    }, this),
    /* @__PURE__ */ jsxDEV("div", { className: "mb-3", children: [
      /* @__PURE__ */ jsxDEV("label", { className: "form-label", htmlFor: frontid, children: /* @__PURE__ */ jsxDEV(MoodleString, { identifier: "front", component: "mod_vocabcoach" }, void 0, false, {
        fileName: "public/mod/vocabcoach/js/esm/src/check/EditVocabForm.tsx",
        lineNumber: 44,
        columnNumber: 21
      }, this) }, void 0, false, {
        fileName: "public/mod/vocabcoach/js/esm/src/check/EditVocabForm.tsx",
        lineNumber: 43,
        columnNumber: 17
      }, this),
      /* @__PURE__ */ jsxDEV(
        "input",
        {
          type: "text",
          className: "form-control",
          id: frontid,
          value: front,
          disabled: saving,
          autoFocus: true,
          onChange: (e) => setFront(e.target.value)
        },
        void 0,
        false,
        {
          fileName: "public/mod/vocabcoach/js/esm/src/check/EditVocabForm.tsx",
          lineNumber: 46,
          columnNumber: 17
        },
        this
      )
    ] }, void 0, true, {
      fileName: "public/mod/vocabcoach/js/esm/src/check/EditVocabForm.tsx",
      lineNumber: 42,
      columnNumber: 13
    }, this),
    /* @__PURE__ */ jsxDEV("div", { className: "mb-3", children: [
      /* @__PURE__ */ jsxDEV("label", { className: "form-label", htmlFor: backid, children: /* @__PURE__ */ jsxDEV(MoodleString, { identifier: "back", component: "mod_vocabcoach" }, void 0, false, {
        fileName: "public/mod/vocabcoach/js/esm/src/check/EditVocabForm.tsx",
        lineNumber: 53,
        columnNumber: 21
      }, this) }, void 0, false, {
        fileName: "public/mod/vocabcoach/js/esm/src/check/EditVocabForm.tsx",
        lineNumber: 52,
        columnNumber: 17
      }, this),
      /* @__PURE__ */ jsxDEV(
        "input",
        {
          type: "text",
          className: "form-control",
          id: backid,
          value: back,
          disabled: saving,
          onChange: (e) => setBack(e.target.value)
        },
        void 0,
        false,
        {
          fileName: "public/mod/vocabcoach/js/esm/src/check/EditVocabForm.tsx",
          lineNumber: 55,
          columnNumber: 17
        },
        this
      )
    ] }, void 0, true, {
      fileName: "public/mod/vocabcoach/js/esm/src/check/EditVocabForm.tsx",
      lineNumber: 51,
      columnNumber: 13
    }, this),
    /* @__PURE__ */ jsxDEV("div", { className: "d-flex gap-2", children: [
      /* @__PURE__ */ jsxDEV("button", { type: "submit", className: "btn btn-primary", disabled: saving || !isComplete, children: /* @__PURE__ */ jsxDEV(MoodleString, { identifier: "save" }, void 0, false, {
        fileName: "public/mod/vocabcoach/js/esm/src/check/EditVocabForm.tsx",
        lineNumber: 62,
        columnNumber: 21
      }, this) }, void 0, false, {
        fileName: "public/mod/vocabcoach/js/esm/src/check/EditVocabForm.tsx",
        lineNumber: 61,
        columnNumber: 17
      }, this),
      /* @__PURE__ */ jsxDEV("button", { type: "button", className: "btn btn-secondary", disabled: saving, onClick: onCancel, children: /* @__PURE__ */ jsxDEV(MoodleString, { identifier: "cancel" }, void 0, false, {
        fileName: "public/mod/vocabcoach/js/esm/src/check/EditVocabForm.tsx",
        lineNumber: 65,
        columnNumber: 21
      }, this) }, void 0, false, {
        fileName: "public/mod/vocabcoach/js/esm/src/check/EditVocabForm.tsx",
        lineNumber: 64,
        columnNumber: 17
      }, this)
    ] }, void 0, true, {
      fileName: "public/mod/vocabcoach/js/esm/src/check/EditVocabForm.tsx",
      lineNumber: 60,
      columnNumber: 13
    }, this)
  ] }, void 0, true, {
    fileName: "public/mod/vocabcoach/js/esm/src/check/EditVocabForm.tsx",
    lineNumber: 37,
    columnNumber: 9
  }, this);
  function handleSubmit(event) {
    event.preventDefault();
    if (!isComplete || saving) {
      return;
    }
    onSave(front.trim(), back.trim());
  }
  __name(handleSubmit, "handleSubmit");
}
__name(EditVocabForm, "EditVocabForm");
export {
  EditVocabForm as default
};
//# sourceMappingURL=EditVocabForm.dev.js.map
