var __defProp = Object.defineProperty;
var __name = (target, value) => __defProp(target, "name", { value, configurable: true });
import { jsxDEV } from "react/jsx-dev-runtime";
import { useEffect, useRef, useState } from "react";
const DOT_SPACING = 12;
const RESERVED_WIDTH = 50;
const OUTCOME_CLASSES = {
  known: "dot-green",
  unknown: "dot-red",
  deleted: ""
};
function ProgressDots({ total, outcomes }) {
  const barRef = useRef(null);
  const [barWidth, setBarWidth] = useState(0);
  useEffect(() => {
    const bar = barRef.current;
    if (!bar) {
      return void 0;
    }
    const observer = new ResizeObserver(([entry]) => setBarWidth(entry.contentRect.width));
    observer.observe(bar);
    return () => observer.disconnect();
  }, []);
  const everyNth = getEveryNth(total, barWidth);
  const step = DOT_SPACING / everyNth;
  return /* @__PURE__ */ jsxDEV("div", { id: "progress-bar", className: "mb-2", ref: barRef, children: Array.from({ length: total }, (unused, index) => {
    const outcome = outcomes[index];
    const done = outcome !== void 0;
    return /* @__PURE__ */ jsxDEV(
      "div",
      {
        className: index % everyNth === 0 ? "" : "hidden",
        style: { transform: done ? `translateX(${index * step}px)` : `translateX(calc(100% - ${(total - index) * step}px))` },
        children: /* @__PURE__ */ jsxDEV("div", { className: `vocab-dot ${done ? OUTCOME_CLASSES[outcome] : "unchecked"}` }, void 0, false, {
          fileName: "public/mod/vocabcoach/js/esm/src/check/ProgressDots.tsx",
          lineNumber: 65,
          columnNumber: 25
        }, this)
      },
      index,
      false,
      {
        fileName: "public/mod/vocabcoach/js/esm/src/check/ProgressDots.tsx",
        lineNumber: 60,
        columnNumber: 21
      },
      this
    );
  }) }, void 0, false, {
    fileName: "public/mod/vocabcoach/js/esm/src/check/ProgressDots.tsx",
    lineNumber: 55,
    columnNumber: 9
  }, this);
}
__name(ProgressDots, "ProgressDots");
function getEveryNth(total, barWidth) {
  const available = barWidth - RESERVED_WIDTH;
  if (available <= 0) {
    return 1;
  }
  return Math.max(1, Math.ceil(total * DOT_SPACING / available));
}
__name(getEveryNth, "getEveryNth");
export {
  ProgressDots as default
};
//# sourceMappingURL=ProgressDots.dev.js.map
