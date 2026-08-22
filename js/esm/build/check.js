import{useState as s}from"react";import g from"./CheckCard";import b from"./CheckSettings";import{updateVocab as v}from"./repository";import{requireAsync as k}from"@moodle/lms/core/amd";import{isMoodleAjaxError as y}from"@moodle/lms/core/ajax";import{jsx as i,jsxs as n}from"react/jsx-runtime";/**
 * React component that administers the check page.
 *
 * @module     mod_vocabcoach/check
 * @copyright  2026 J. Funk, johannesfunk@outlook.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */function C({items:m,cmid:x,modelabels:f,currentmode:p}){const[a,l]=s(m),[e,h]=s(p),r=a[0];let c=a.length;if(c===0)return i("span",{children:"Done!"});return n("div",{id:"check-container",children:[n("div",{id:"check-header",children:[i(b,{currentmode:e,modelabels:f,onModeChange:M}),n("div",{className:"text-end",children:[c," left"]})]}),i(g,{vocab:r,mode:e,onAnswer:u},`${r.id}-${e}`)]});async function u(t){try{await v(r.id,t),l(o=>o.slice(1))}catch(o){const d=await k("core/notification");y(o)?await d.exception(o):await d.addNotification({message:o instanceof Error?o.message:String(o),type:"error"})}}function M(t){h(t)}}export{C as default};
