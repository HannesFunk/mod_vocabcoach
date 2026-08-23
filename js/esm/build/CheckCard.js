import{useState as s}from"react";import{Fragment as b,jsx as e,jsxs as o}from"react/jsx-runtime";/**
 * React component that render one vocab card with front / back
 *
 * @module     mod_vocabcoach/check
 * @copyright  2026 J. Funk, johannesfunk@outlook.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */function l({vocab:t,mode:c,onAnswer:n,buttonLabels:a}){const[d,r]=s(!1),[i]=s(()=>c==="type"?"front":c==="random"?Math.random()<.5?"front":"back":c);return o(b,{children:[o("div",{id:"check-area",children:[e("div",{id:"check-box-front",className:"check-box d-flex",onClick:()=>r(!0),children:e("div",{id:"check-front",className:`check-label ${i==="front"&&!d?"visually-hidden":""}`,children:t.front})}),e("div",{id:"check-box-back",className:"check-box d-flex",onClick:()=>r(!0),children:e("div",{id:"check-back",className:`check-label ${i==="back"&&!d?"visually-hidden":""}`,children:t.back})})]}),o("div",{id:"check-buttons",children:[e("button",{id:"check-button-no",className:"btn btn-secondary",onClick:()=>n(!1),children:a.no}),e("button",{id:"check-button-no",className:"btn btn-secondary",onClick:()=>n(!0),children:a.yes})]})]})}export{l as default};
