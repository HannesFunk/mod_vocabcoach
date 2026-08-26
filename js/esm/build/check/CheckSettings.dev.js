var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { jsxDEV } from "react/jsx-dev-runtime";
import { MODES } from "../types";
import String from "@moodle/lms/core/String";
function CheckSettings({ currentmode, modelabels, onModeChange }) {
  return /* @__PURE__ */ jsxDEV("div", { className: "d-flex align-items-center ml-auto", children: [
    /* @__PURE__ */ jsxDEV("label", { htmlFor: "checkmode-select", className: "mr-2 ml-auto", children: /* @__PURE__ */ jsxDEV(String, { identifier: "checkmode", component: "mod_vocabcoach" }, void 0, false, {
      fileName: "public/mod/vocabcoach/js/esm/src/check/CheckSettings.tsx",
      lineNumber: 44,
      columnNumber: 17
    }, this) }, void 0, false, {
      fileName: "public/mod/vocabcoach/js/esm/src/check/CheckSettings.tsx",
      lineNumber: 43,
      columnNumber: 13
    }, this),
    /* @__PURE__ */ jsxDEV(
      "select",
      {
        id: "checkmode-select",
        className: "custom-select w-auto",
        value: currentmode,
        onChange: (e) => onModeChange(e.target.value),
        children: MODES.map(
          (mode) => /* @__PURE__ */ jsxDEV("option", { value: mode, children: modelabels[mode] }, mode, false, {
            fileName: "public/mod/vocabcoach/js/esm/src/check/CheckSettings.tsx",
            lineNumber: 49,
            columnNumber: 21
          }, this)
        )
      },
      void 0,
      false,
      {
        fileName: "public/mod/vocabcoach/js/esm/src/check/CheckSettings.tsx",
        lineNumber: 46,
        columnNumber: 13
      },
      this
    )
  ] }, void 0, true, {
    fileName: "public/mod/vocabcoach/js/esm/src/check/CheckSettings.tsx",
    lineNumber: 42,
    columnNumber: 9
  }, this);
}
__name(CheckSettings, "CheckSettings");
export {
  CheckSettings as default
};
//# sourceMappingURL=CheckSettings.dev.js.map
