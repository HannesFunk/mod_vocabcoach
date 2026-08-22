import{useState as s}from"react";import b from"./CheckCard";import v from"./CheckSettings";import{updateVocab as k}from"./repository";import{requireAsync as y}from"@moodle/lms/core/amd";import{isMoodleAjaxError as C}from"@moodle/lms/core/ajax";import{jsx as i,jsxs as n}from"react/jsx-runtime";/**
 * React component that administers the check page.
 *
 * @module     mod_vocabcoach/check
 * @copyright  2026 J. Funk, johannesfunk@outlook.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */function x({items:m,cmid:f,modelabels:p,currentmode:l}){const[a,h]=s(m),[e,u]=s(l),r=a[0];let c=a.length;if(c===0)return i("span",{children:"Done!"});return n("div",{id:"check-container",children:[n("div",{id:"check-header",children:[i(v,{currentmode:e,modelabels:p,onModeChange:g}),n("div",{className:"text-end",children:[c," left"]})]}),i(b,{vocab:r,mode:e,onAnswer:M},`${r.id}-${e}`)]});async function M(t){try{await k(r.id,f,t),h(o=>o.slice(1))}catch(o){const d=await y("core/notification");C(o)?await d.exception(o):await d.addNotification({message:o instanceof Error?o.message:String(o),type:"error"})}}function g(t){u(t)}}export{x as default};
