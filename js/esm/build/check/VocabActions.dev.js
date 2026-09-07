var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { jsxDEV } from "react/jsx-dev-runtime";
import { getString } from "@moodle/lms/core/stringUtils";
import { use } from "react";
function VocabAction({ onDelete, onEdit }) {
  const showActions = use(getString("actions_show_actions", "mod_vocabcoach"));
  const editString = use(getString("edit", "core"));
  const deleteString = use(getString("delete", "core"));
  return /* @__PURE__ */ jsxDEV("div", { className: "dropdown", children: [
    /* @__PURE__ */ jsxDEV(
      "a",
      {
        href: "#",
        className: "btn btn-link btn-icon colour-primary text-decoration-none d-flex ml-auto",
        role: "button",
        "data-bs-toggle": "dropdown",
        "data-bs-boundary": "window",
        "aria-haspopup": "true",
        tabIndex: 0,
        "aria-label": showActions,
        "aria-expanded": "false",
        children: /* @__PURE__ */ jsxDEV("i", { className: "fa fa-ellipsis-h text-dark  py-2", "aria-hidden": "true" }, void 0, false, {
          fileName: "public/mod/vocabcoach/js/esm/src/check/VocabActions.tsx",
          lineNumber: 53,
          columnNumber: 17
        }, this)
      },
      void 0,
      false,
      {
        fileName: "public/mod/vocabcoach/js/esm/src/check/VocabActions.tsx",
        lineNumber: 49,
        columnNumber: 13
      },
      this
    ),
    /* @__PURE__ */ jsxDEV(
      "div",
      {
        className: "dropdown-menu dropdown-menu-right",
        "data-rel": "menu-content",
        role: "menu",
        children: [
          /* @__PURE__ */ jsxDEV(
            "a",
            {
              className: "dropdown-item menu-action",
              role: "menuitem",
              tabIndex: -1,
              title: editString,
              href: "#",
              onClick: onEdit,
              children: [
                /* @__PURE__ */ jsxDEV("i", { className: "fa fa-edit  pr-2", "aria-hidden": "true" }, void 0, false, {
                  fileName: "public/mod/vocabcoach/js/esm/src/check/VocabActions.tsx",
                  lineNumber: 64,
                  columnNumber: 21
                }, this),
                /* @__PURE__ */ jsxDEV("span", { className: "menu-action-text", children: editString }, void 0, false, {
                  fileName: "public/mod/vocabcoach/js/esm/src/check/VocabActions.tsx",
                  lineNumber: 65,
                  columnNumber: 21
                }, this)
              ]
            },
            void 0,
            true,
            {
              fileName: "public/mod/vocabcoach/js/esm/src/check/VocabActions.tsx",
              lineNumber: 58,
              columnNumber: 17
            },
            this
          ),
          /* @__PURE__ */ jsxDEV(
            "a",
            {
              className: "dropdown-item menu-action",
              role: "menuitem",
              tabIndex: -1,
              title: deleteString,
              href: "#",
              onClick: onDelete,
              children: [
                /* @__PURE__ */ jsxDEV("i", { className: "fa fa-trash pr-2", "aria-hidden": "true" }, void 0, false, {
                  fileName: "public/mod/vocabcoach/js/esm/src/check/VocabActions.tsx",
                  lineNumber: 73,
                  columnNumber: 21
                }, this),
                /* @__PURE__ */ jsxDEV("span", { className: "menu-action-text", children: deleteString }, void 0, false, {
                  fileName: "public/mod/vocabcoach/js/esm/src/check/VocabActions.tsx",
                  lineNumber: 74,
                  columnNumber: 21
                }, this)
              ]
            },
            void 0,
            true,
            {
              fileName: "public/mod/vocabcoach/js/esm/src/check/VocabActions.tsx",
              lineNumber: 67,
              columnNumber: 17
            },
            this
          )
        ]
      },
      void 0,
      true,
      {
        fileName: "public/mod/vocabcoach/js/esm/src/check/VocabActions.tsx",
        lineNumber: 55,
        columnNumber: 13
      },
      this
    )
  ] }, void 0, true, {
    fileName: "public/mod/vocabcoach/js/esm/src/check/VocabActions.tsx",
    lineNumber: 48,
    columnNumber: 9
  }, this);
}
__name(VocabAction, "VocabAction");
export {
  VocabAction as default
};
//# sourceMappingURL=VocabActions.dev.js.map
