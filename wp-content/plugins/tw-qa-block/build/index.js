!function(){"use strict";var e,r={802:function(){var e=window.wp.blocks,r=window.wp.element;function t(){return t=Object.assign?Object.assign.bind():function(e){for(var r=1;r<arguments.length;r++){var t=arguments[r];for(var n in t)Object.prototype.hasOwnProperty.call(t,n)&&(e[n]=t[n])}return e},t.apply(this,arguments)}function n(e,r){(null==r||r>e.length)&&(r=e.length);for(var t=0,n=new Array(r);t<r;t++)n[t]=e[t];return n}function o(e){return function(e){if(Array.isArray(e))return n(e)}(e)||function(e){if("undefined"!=typeof Symbol&&null!=e[Symbol.iterator]||null!=e["@@iterator"])return Array.from(e)}(e)||function(e,r){if(e){if("string"==typeof e)return n(e,r);var t=Object.prototype.toString.call(e).slice(8,-1);return"Object"===t&&e.constructor&&(t=e.constructor.name),"Map"===t||"Set"===t?Array.from(e):"Arguments"===t||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t)?n(e,r):void 0}}(e)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}var a=window.wp.i18n,l=window.wp.blockEditor,c=window.wp.components;(0,e.registerBlockType)("tw/qa-block",{edit:function(e){var n=e.attributes,i=e.setAttributes,s=n.question,u=n.answer,d=n.bordered,f=(0,l.useBlockProps)(),p=f.className.split(" "),b=new Set(p);d&&b.add("qa-wrap--border-around");var w=o(b).join(" ");return(0,r.createElement)("div",t({},f,{className:w}),(0,r.createElement)(l.InspectorControls,{key:"settings"},(0,r.createElement)(c.__experimentalToolsPanel,{label:(0,a.__)("Q&A Options","tw-qa-block"),resetAll:function(){i({bordered:!1})}},(0,r.createElement)(c.__experimentalToolsPanelItem,{label:(0,a.__)("Bordered","tw-qa-block"),hasValue:function(){return!0},isShownByDefault:!0},(0,r.createElement)(c.ToggleControl,{label:(0,a.__)("Bordered","tw-qa-block"),help:(0,a.__)("Add border arround Q&A block."),checked:d,onChange:function(e){i({bordered:e})}})))),(0,r.createElement)(l.RichText,{className:"qa-question",placeholder:(0,a.__)("Question...","tw-qa-block"),value:s,onChange:function(e){i({question:e})},allowedFormats:["core/bold","core/italic","core/strikethrough","core/link"]}),(0,r.createElement)(l.RichText,{className:"qa-answer",placeholder:(0,a.__)("Answer...","tw-qa-block"),value:u,onChange:function(e){i({answer:e})},allowedFormats:["core/bold","core/italic","core/strikethrough","core/link"]}))},save:function(e){var n=e.attributes,a=n.question,c=n.answer,i=n.bordered,s=l.useBlockProps.save().className.split(" "),u=new Set(s);u.delete("wp-block-tw-qa-block"),u.add("qa-wrap"),i&&u.add("qa-wrap--border-around");var d=o(u).join(" ");return(0,r.createElement)("div",t({},l.useBlockProps.save(),{className:d}),(0,r.createElement)(l.RichText.Content,{tagName:"div",className:"qa-question",value:a}),(0,r.createElement)(l.RichText.Content,{tagName:"div",className:"qa-answer",value:c}))},transforms:{from:[{type:"raw",selector:"div.qa-wrap",transform:function(r){var t=(0,e.getBlockAttributes)("tw/qa-block",r);return(0,e.createBlock)("tw/qa-block",t)}}]}})}},t={};function n(e){var o=t[e];if(void 0!==o)return o.exports;var a=t[e]={exports:{}};return r[e](a,a.exports,n),a.exports}n.m=r,e=[],n.O=function(r,t,o,a){if(!t){var l=1/0;for(u=0;u<e.length;u++){t=e[u][0],o=e[u][1],a=e[u][2];for(var c=!0,i=0;i<t.length;i++)(!1&a||l>=a)&&Object.keys(n.O).every((function(e){return n.O[e](t[i])}))?t.splice(i--,1):(c=!1,a<l&&(l=a));if(c){e.splice(u--,1);var s=o();void 0!==s&&(r=s)}}return r}a=a||0;for(var u=e.length;u>0&&e[u-1][2]>a;u--)e[u]=e[u-1];e[u]=[t,o,a]},n.o=function(e,r){return Object.prototype.hasOwnProperty.call(e,r)},function(){var e={826:0,431:0};n.O.j=function(r){return 0===e[r]};var r=function(r,t){var o,a,l=t[0],c=t[1],i=t[2],s=0;if(l.some((function(r){return 0!==e[r]}))){for(o in c)n.o(c,o)&&(n.m[o]=c[o]);if(i)var u=i(n)}for(r&&r(t);s<l.length;s++)a=l[s],n.o(e,a)&&e[a]&&e[a][0](),e[a]=0;return n.O(u)},t=self.webpackChunktw_qa_block=self.webpackChunktw_qa_block||[];t.forEach(r.bind(null,0)),t.push=r.bind(null,t.push.bind(t))}();var o=n.O(void 0,[431],(function(){return n(802)}));o=n.O(o)}();