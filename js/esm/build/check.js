import{useState as s}from"react";import y from"./CheckCard";import k from"./CheckSettings";import{updateVocab as C}from"./repository";import{requireAsync as x}from"@moodle/lms/core/amd";import{isMoodleAjaxError as w}from"@moodle/lms/core/ajax";import{jsx as i,jsxs as n}from"react/jsx-runtime";/**
 * React component that administers the check page.
 *
 * @module     mod_vocabcoach/check
 * @copyright  2026 J. Funk, johannesfunk@outlook.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */function A({items:m,cmid:f,modelabels:l,currentmode:p,buttonLabels:u,fromList:b}){const[a,h]=s(m),[e,g]=s(p),t=a[0];let c=a.length;if(c===0)return i("span",{children:"Done!"});return n("div",{id:"check-container",children:[n("div",{id:"check-header",children:[i(k,{currentmode:e,modelabels:l,onModeChange:v}),n("div",{className:"text-end",children:[c," left"]})]}),i(y,{vocab:t,mode:e,buttonLabels:u,onAnswer:M},`${t.id}-${e}`)]});async function M(r){if(h(o=>o.slice(1)),!b)try{await C(t.id,f,r)}catch(o){const d=await x("core/notification");w(o)?await d.exception(o):await d.addNotification({message:o instanceof Error?o.message:String(o),type:"error"})}}function v(r){g(r)}}export{A as default};
