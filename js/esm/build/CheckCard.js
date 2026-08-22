import{useState as r}from"react";import b from"@moodle/lms/core/String";import{Fragment as k,jsx as e,jsxs as o}from"react/jsx-runtime";/**
 * React component that render one vocab card with front / back
 *
 * @module     mod_vocabcoach/check
 * @copyright  2026 J. Funk, johannesfunk@outlook.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */function l({vocab:t,mode:c,onAnswer:n}){const[a,d]=r(!1),[i]=r(()=>c==="type"?"front":c==="random"?Math.random()<.5?"front":"back":c);return o(k,{children:[o("div",{id:"check-area",children:[e("div",{id:"check-box-front",className:"check-box d-flex",onClick:()=>d(!0),children:e("div",{id:"check-front",className:`check-label ${i==="front"&&!a?"visually-hidden":""}`,children:t.front})}),e("div",{id:"check-box-back",className:"check-box d-flex",onClick:()=>d(!0),children:e("div",{id:"check-back",className:`check-label ${i==="back"&&!a?"visually-hidden":""}`,children:t.back})})]}),o("div",{id:"check-buttons",children:[e("button",{id:"check-button-no",className:"btn btn-secondary",onClick:()=>n(!1),children:e(b,{identifier:"check_button_no",component:"mod_vocabcoach"})}),e("button",{id:"check-button-no",className:"btn btn-secondary",onClick:()=>n(!0),children:e(b,{identifier:"check_button_yes",component:"mod_vocabcoach"})})]})]})}export{l as default};
