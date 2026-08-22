import{fetchOne as c}from"@moodle/lms/core/ajax";const s=(e,o,a)=>c({methodname:"mod_vocabcoach_update_vocab",args:{id:e,cmid:o,known:a}});export{s as updateVocab};
