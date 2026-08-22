import{useState as i}from"react";import b from"./CheckCard";import k from"./CheckSettings";import{jsx as o,jsxs as n}from"react/jsx-runtime";/**
 * React component that administers the check page.
 *
 * @module     mod_vocabcoach/check
 * @copyright  2026 J. Funk, johannesfunk@outlook.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */function v({items:a,cmid:C,modelabels:s,currentmode:m}){const[t,l]=i(a),[e,h]=i(m),d=t[0];let c=t.length;if(c===0)return o("span",{children:"Done!"});return n("div",{id:"check-container",children:[n("div",{id:"check-header",children:[o(k,{currentmode:e,modelabels:s,onModeChange:f}),n("div",{className:"text-end",children:[c," left"]})]}),o(b,{vocab:d,mode:e,onAnswer:p},`${d.id}-${e}`)]});function p(r){l(u=>u.slice(1))}function f(r){h(r)}}export{v as default};
