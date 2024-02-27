!function(){"use strict";var e=window.React,l=window.wp.blocks,t=window.wp.i18n,r=window.wp.blockEditor,n=window.wp.components,s=JSON.parse('{"UU":"tw/scroll-gallery"}');(0,l.registerBlockType)(s.UU,{icon:()=>(0,e.createElement)("svg",{width:"24",height:"24",viewBox:"0 0 24 24",fill:"none",xmlns:"http://www.w3.org/2000/svg"},(0,e.createElement)("path",{d:"M7 2H17",stroke:"black","stroke-width":"2","stroke-linecap":"round","stroke-linejoin":"round"}),(0,e.createElement)("path",{d:"M5 6H19",stroke:"black","stroke-width":"2","stroke-linecap":"round","stroke-linejoin":"round"}),(0,e.createElement)("path",{d:"M19 10H5C3.89543 10 3 10.8954 3 12V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V12C21 10.8954 20.1046 10 19 10Z",fill:"black",stroke:"black","stroke-width":"2","stroke-linecap":"round","stroke-linejoin":"round"})),edit:function(l){const{attributes:s,setAttributes:a}=l,{transition:o}=s,c=[...new Set(["tw-scroll-gallery","alignfull"])].join(" "),i=(0,r.useBlockProps)({className:c}),d=(0,r.useInnerBlocksProps)(i,{allowedBlocks:["tw/scroll-gallery-slide"],defaultBlock:{name:"tw/scroll-gallery-slide",attributes:{}},directInsert:!0,renderAppender:r.InnerBlocks.DefaultBlockAppender,template:[["tw/scroll-gallery-slide",{}]]}),{children:u,...w}=d;return w["data-transition"]=o||"push",(0,e.createElement)("div",{...w},(0,e.createElement)(r.InspectorControls,{key:"settings"},(0,e.createElement)(n.__experimentalToolsPanel,{label:(0,t.__)("Scroll Gallery Options","tw-scroll-gallery"),resetAll:function(){a({transition:"push"})}},(0,e.createElement)(n.__experimentalToolsPanelItem,{label:(0,t.__)("Transition","tw-scroll-gallery"),hasValue:()=>!0,isShownByDefault:!0},(0,e.createElement)(n.SelectControl,{label:(0,t.__)("Transition","tw-scroll-gallery"),value:o,help:(0,e.createElement)(e.Fragment,null,(0,e.createElement)("p",null,(0,t.__)("Select transition when slides change.","tw-scroll-gallery")),(0,e.createElement)("dl",null,(0,e.createElement)("dt",null,(0,t.__)("Push (Default)","tw-scroll-gallery")),(0,e.createElement)("dd",null,(0,t.__)("Next slide pushes previous slide off screen.","tw-scroll-gallery")),(0,e.createElement)("dt",null,(0,t.__)("Stack","tw-scroll-gallery")),(0,e.createElement)("dd",null,(0,t.__)("Next slide scrolls over top of previous slide.","tw-scroll-gallery")),(0,e.createElement)("dt",null,(0,t.__)("Fade","tw-scroll-gallery")),(0,e.createElement)("dd",null,(0,t.__)("Cross fade between slides.","tw-scroll-gallery")))),options:[{value:"push",label:"Push"},{value:"stack",label:"Stack"},{value:"fade",label:"Fade"}],onChange:function(e){a({transition:e})}})))),u)},save:function(l){const{attributes:t}=l,{transition:n}=t,s=r.useBlockProps.save({className:"tw-scroll-gallery","data-transition":n||"push"}),a=r.useInnerBlocksProps.save(s);return(0,e.createElement)("div",{...a})}})}();