var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { jsxDEV } from "react/jsx-dev-runtime";
import { useLayoutEffect, useRef } from "react";
const MIN_FONT_SIZE = 18;
const FONT_SIZE_STEP = 2;
function CheckLabel({ text, id, concealed }) {
  const labelref = useRef(null);
  useLayoutEffect(() => {
    const label = labelref.current;
    const box = label?.parentElement;
    if (!label || !box) {
      return void 0;
    }
    const fit = /* @__PURE__ */ __name(() => {
      label.style.fontSize = "";
      if (concealed) {
        return;
      }
      let size = parseFloat(getComputedStyle(label).fontSize);
      while (label.offsetHeight > box.clientHeight && size > MIN_FONT_SIZE) {
        size -= FONT_SIZE_STEP;
        label.style.fontSize = `${size}px`;
      }
    }, "fit");
    fit();
    const observer = new ResizeObserver(fit);
    observer.observe(box);
    return () => observer.disconnect();
  }, [text, concealed]);
  return /* @__PURE__ */ jsxDEV(
    "div",
    {
      id,
      ref: labelref,
      className: `check-label ${concealed ? "visually-hidden" : ""}`,
      children: text
    },
    void 0,
    false,
    {
      fileName: "public/mod/vocabcoach/js/esm/src/check/CheckLabel.tsx",
      lineNumber: 65,
      columnNumber: 9
    },
    this
  );
}
__name(CheckLabel, "CheckLabel");
export {
  CheckLabel as default
};
//# sourceMappingURL=CheckLabel.dev.js.map
