!function(){"use strict";var e=window.wp.blocks,t=(window.wp.i18n,JSON.parse('{"u2":"mine-cloudvod/course-single","TN":"Single Course","qv":"welcome-learn-more","WL":"Show single course detail."}')),o=window.wp.element,n=window.wp.components,l=window.wp.blockEditor;const i={edit:(0,n.withNotices)((e=>{let{attributes:i,setAttributes:r}=e;const{cid:s}=i,c=(0,o.useRef)(),a=(0,l.useBlockProps)({ref:c}),[d,u]=(0,o.useState)([]);let w=(0,o.useRef)(!0);(0,o.useEffect)((()=>(wp.apiFetch({path:wp.url.addQueryArgs("/wp/v2/mcv_course",{per_page:-1,orderby:"id",order:"desc",_fields:["id","title"]})}).then((e=>{w&&u(e.map((e=>({value:e.id,label:e.title.rendered}))))})).catch((()=>{w&&u([])})),()=>{w=!1})),[]);const p=(0,o.createElement)(o.Fragment,null,(0,o.createElement)(l.InspectorControls,null,(0,o.createElement)(n.PanelBody,{title:t.TN+wp.i18n.__("Settings","mine-cloudvod")},(0,o.createElement)(n.ComboboxControl,{label:wp.i18n.__("Courses","mine-cloudvod"),value:s,onChange:e=>{r({cid:e})},options:d,onInputChange:e=>u(options.filter((t=>t.label.toLowerCase().startsWith(e.toLowerCase()))))}))));return(0,o.createElement)("div",a,p,(0,o.createElement)(n.Placeholder,{className:"mine-cloudvod__placeholder is-loading",label:t.TN,instructions:t.WL,icon:t.qv},(0,o.createElement)("p",null,"This block will show a course's info, check it in the frontend.")))})),save:function(){return null}};(0,e.registerBlockType)(t.u2,i)}();