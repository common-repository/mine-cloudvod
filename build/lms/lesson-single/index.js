!function(){"use strict";var e=window.wp.blocks,n=(window.wp.i18n,JSON.parse('{"u2":"mine-cloudvod/lesson-single","TN":"Single Lesson","qv":"welcome-learn-more","WL":"Show single lesson detail."}')),t=window.wp.element,o=window.wp.components,l=window.wp.blockEditor;const{useState:i,Fragment:s,useRef:r}=wp.element,c={edit:(0,o.withNotices)((e=>{let{attributes:i,setAttributes:s}=e;const c=r(),w=(0,l.useBlockProps)({ref:c});return(0,t.createElement)("div",w,(0,t.createElement)(o.Placeholder,{className:"mine-cloudvod__placeholder is-loading",label:n.TN,instructions:n.WL,icon:n.qv},(0,t.createElement)("p",null,(0,t.createElement)("strong",null,wp.i18n.__("Do not remove this block!")))))})),save:function(){return null}};"site-editor"==window.pagenow&&(0,e.registerBlockType)(n.u2,c)}();