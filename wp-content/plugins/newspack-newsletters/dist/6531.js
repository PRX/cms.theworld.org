(self.webpackChunkwebpack=self.webpackChunkwebpack||[]).push([[6531,9589],{6531:(t,e,n)=>{!function(t){"use strict";var e={script:[["lang",/(javascript|babel)/i,"javascript"],["type",/^(?:text|application)\/(?:x-)?(?:java|ecma)script$|^module$|^$/i,"javascript"],["type",/./,"text/plain"],[null,null,"javascript"]],style:[["lang",/^css$/i,"css"],["type",/^(text\/)?(x-)?(stylesheet|css)$/i,"css"],["type",/./,"text/plain"],[null,null,"css"]]};function n(t,e,n){var a=t.current(),r=a.search(e);return r>-1?t.backUp(a.length-r):a.match(/<\/?$/)&&(t.backUp(a.length),t.match(e,!1)||t.match(a)),n}var a={};function r(t){var e=a[t];return e||(a[t]=new RegExp("\\s+"+t+"\\s*=\\s*('|\")?([^'\"]+)('|\")?\\s*"))}function o(t,e){var n=t.match(r(e));return n?/^\s*(.*?)\s*$/.exec(n[2])[1]:""}function i(t,e){return new RegExp((e?"^":"")+"</s*"+t+"s*>","i")}function l(t,e){for(var n in t)for(var a=e[n]||(e[n]=[]),r=t[n],o=r.length-1;o>=0;o--)a.unshift(r[o])}function c(t,e){for(var n=0;n<t.length;n++){var a=t[n];if(!a[0]||a[1].test(o(e,a[0])))return a[2]}}t.defineMode("htmlmixed",(function(a,r){var o=t.getMode(a,{name:"xml",htmlMode:!0,multilineTagIndentFactor:r.multilineTagIndentFactor,multilineTagIndentPastTag:r.multilineTagIndentPastTag,allowMissingTagName:r.allowMissingTagName}),u={},s=r&&r.tags,d=r&&r.scriptTypes;if(l(e,u),s&&l(s,u),d)for(var m=d.length-1;m>=0;m--)u.script.unshift(["type",d[m].matches,d[m].mode]);function f(e,r){var l,s=o.token(e,r.htmlState),d=/\btag\b/.test(s);if(d&&!/[<>\s\/]/.test(e.current())&&(l=r.htmlState.tagName&&r.htmlState.tagName.toLowerCase())&&u.hasOwnProperty(l))r.inTag=l+" ";else if(r.inTag&&d&&/>$/.test(e.current())){var m=/^([\S]+) (.*)/.exec(r.inTag);r.inTag=null;var g=">"==e.current()&&c(u[m[1]],m[2]),p=t.getMode(a,g),h=i(m[1],!0),x=i(m[1],!1);r.token=function(t,e){return t.match(h,!1)?(e.token=f,e.localState=e.localMode=null,null):n(t,x,e.localMode.token(t,e.localState))},r.localMode=p,r.localState=t.startState(p,o.indent(r.htmlState,"",""))}else r.inTag&&(r.inTag+=e.current(),e.eol()&&(r.inTag+=" "));return s}return{startState:function(){return{token:f,inTag:null,localMode:null,localState:null,htmlState:t.startState(o)}},copyState:function(e){var n;return e.localState&&(n=t.copyState(e.localMode,e.localState)),{token:e.token,inTag:e.inTag,localMode:e.localMode,localState:n,htmlState:t.copyState(o,e.htmlState)}},token:function(t,e){return e.token(t,e)},indent:function(e,n,a){return!e.localMode||/^\s*<\//.test(n)?o.indent(e.htmlState,n,a):e.localMode.indent?e.localMode.indent(e.localState,n,a):t.Pass},innerMode:function(t){return{state:t.localState||t.htmlState,mode:t.localMode||o}}}}),"xml","javascript","css"),t.defineMIME("text/html","htmlmixed")}(n(4631),n(9589),n(6876),n(6629))},9589:(t,e,n)=>{!function(t){"use strict";var e={autoSelfClosers:{area:!0,base:!0,br:!0,col:!0,command:!0,embed:!0,frame:!0,hr:!0,img:!0,input:!0,keygen:!0,link:!0,meta:!0,param:!0,source:!0,track:!0,wbr:!0,menuitem:!0},implicitlyClosed:{dd:!0,li:!0,optgroup:!0,option:!0,p:!0,rp:!0,rt:!0,tbody:!0,td:!0,tfoot:!0,th:!0,tr:!0},contextGrabbers:{dd:{dd:!0,dt:!0},dt:{dd:!0,dt:!0},li:{li:!0},option:{option:!0,optgroup:!0},optgroup:{optgroup:!0},p:{address:!0,article:!0,aside:!0,blockquote:!0,dir:!0,div:!0,dl:!0,fieldset:!0,footer:!0,form:!0,h1:!0,h2:!0,h3:!0,h4:!0,h5:!0,h6:!0,header:!0,hgroup:!0,hr:!0,menu:!0,nav:!0,ol:!0,p:!0,pre:!0,section:!0,table:!0,ul:!0},rp:{rp:!0,rt:!0},rt:{rp:!0,rt:!0},tbody:{tbody:!0,tfoot:!0},td:{td:!0,th:!0},tfoot:{tbody:!0},th:{td:!0,th:!0},thead:{tbody:!0,tfoot:!0},tr:{tr:!0}},doNotIndent:{pre:!0},allowUnquoted:!0,allowMissing:!0,caseFold:!0},n={autoSelfClosers:{},implicitlyClosed:{},contextGrabbers:{},doNotIndent:{},allowUnquoted:!1,allowMissing:!1,allowMissingTagName:!1,caseFold:!1};t.defineMode("xml",(function(a,r){var o,i,l=a.indentUnit,c={},u=r.htmlMode?e:n;for(var s in u)c[s]=u[s];for(var s in r)c[s]=r[s];function d(t,e){function n(n){return e.tokenize=n,n(t,e)}var a=t.next();return"<"==a?t.eat("!")?t.eat("[")?t.match("CDATA[")?n(g("atom","]]>")):null:t.match("--")?n(g("comment","--\x3e")):t.match("DOCTYPE",!0,!0)?(t.eatWhile(/[\w\._\-]/),n(p(1))):null:t.eat("?")?(t.eatWhile(/[\w\._\-]/),e.tokenize=g("meta","?>"),"meta"):(o=t.eat("/")?"closeTag":"openTag",e.tokenize=m,"tag bracket"):"&"==a?(t.eat("#")?t.eat("x")?t.eatWhile(/[a-fA-F\d]/)&&t.eat(";"):t.eatWhile(/[\d]/)&&t.eat(";"):t.eatWhile(/[\w\.\-:]/)&&t.eat(";"))?"atom":"error":(t.eatWhile(/[^&<]/),null)}function m(t,e){var n=t.next();if(">"==n||"/"==n&&t.eat(">"))return e.tokenize=d,o=">"==n?"endTag":"selfcloseTag","tag bracket";if("="==n)return o="equals",null;if("<"==n){e.tokenize=d,e.state=b,e.tagName=e.tagStart=null;var a=e.tokenize(t,e);return a?a+" tag error":"tag error"}return/[\'\"]/.test(n)?(e.tokenize=f(n),e.stringStartCol=t.column(),e.tokenize(t,e)):(t.match(/^[^\s\u00a0=<>\"\']*[^\s\u00a0=<>\"\'\/]/),"word")}function f(t){var e=function(e,n){for(;!e.eol();)if(e.next()==t){n.tokenize=m;break}return"string"};return e.isInAttribute=!0,e}function g(t,e){return function(n,a){for(;!n.eol();){if(n.match(e)){a.tokenize=d;break}n.next()}return t}}function p(t){return function(e,n){for(var a;null!=(a=e.next());){if("<"==a)return n.tokenize=p(t+1),n.tokenize(e,n);if(">"==a){if(1==t){n.tokenize=d;break}return n.tokenize=p(t-1),n.tokenize(e,n)}}return"meta"}}function h(t){return t&&t.toLowerCase()}function x(t,e,n){this.prev=t.context,this.tagName=e||"",this.indent=t.indented,this.startOfLine=n,(c.doNotIndent.hasOwnProperty(e)||t.context&&t.context.noIndent)&&(this.noIndent=!0)}function k(t){t.context&&(t.context=t.context.prev)}function v(t,e){for(var n;;){if(!t.context)return;if(n=t.context.tagName,!c.contextGrabbers.hasOwnProperty(h(n))||!c.contextGrabbers[h(n)].hasOwnProperty(h(e)))return;k(t)}}function b(t,e,n){return"openTag"==t?(n.tagStart=e.column(),T):"closeTag"==t?S:b}function T(t,e,n){return"word"==t?(n.tagName=e.current(),i="tag",y):c.allowMissingTagName&&"endTag"==t?(i="tag bracket",y(t,e,n)):(i="error",T)}function S(t,e,n){if("word"==t){var a=e.current();return n.context&&n.context.tagName!=a&&c.implicitlyClosed.hasOwnProperty(h(n.context.tagName))&&k(n),n.context&&n.context.tagName==a||!1===c.matchClosing?(i="tag",w):(i="tag error",M)}return c.allowMissingTagName&&"endTag"==t?(i="tag bracket",w(t,e,n)):(i="error",M)}function w(t,e,n){return"endTag"!=t?(i="error",w):(k(n),b)}function M(t,e,n){return i="error",w(t,e,n)}function y(t,e,n){if("word"==t)return i="attribute",N;if("endTag"==t||"selfcloseTag"==t){var a=n.tagName,r=n.tagStart;return n.tagName=n.tagStart=null,"selfcloseTag"==t||c.autoSelfClosers.hasOwnProperty(h(a))?v(n,a):(v(n,a),n.context=new x(n,a,r==n.indented)),b}return i="error",y}function N(t,e,n){return"equals"==t?C:(c.allowMissing||(i="error"),y(t,e,n))}function C(t,e,n){return"string"==t?I:"word"==t&&c.allowUnquoted?(i="string",y):(i="error",y(t,e,n))}function I(t,e,n){return"string"==t?I:y(t,e,n)}return d.isInText=!0,{startState:function(t){var e={tokenize:d,state:b,indented:t||0,tagName:null,tagStart:null,context:null};return null!=t&&(e.baseIndent=t),e},token:function(t,e){if(!e.tagName&&t.sol()&&(e.indented=t.indentation()),t.eatSpace())return null;o=null;var n=e.tokenize(t,e);return(n||o)&&"comment"!=n&&(i=null,e.state=e.state(o||n,t,e),i&&(n="error"==i?n+" error":i)),n},indent:function(e,n,a){var r=e.context;if(e.tokenize.isInAttribute)return e.tagStart==e.indented?e.stringStartCol+1:e.indented+l;if(r&&r.noIndent)return t.Pass;if(e.tokenize!=m&&e.tokenize!=d)return a?a.match(/^(\s*)/)[0].length:0;if(e.tagName)return!1!==c.multilineTagIndentPastTag?e.tagStart+e.tagName.length+2:e.tagStart+l*(c.multilineTagIndentFactor||1);if(c.alignCDATA&&/<!\[CDATA\[/.test(n))return 0;var o=n&&/^<(\/)?([\w_:\.-]*)/.exec(n);if(o&&o[1])for(;r;){if(r.tagName==o[2]){r=r.prev;break}if(!c.implicitlyClosed.hasOwnProperty(h(r.tagName)))break;r=r.prev}else if(o)for(;r;){var i=c.contextGrabbers[h(r.tagName)];if(!i||!i.hasOwnProperty(h(o[2])))break;r=r.prev}for(;r&&r.prev&&!r.startOfLine;)r=r.prev;return r?r.indent+l:e.baseIndent||0},electricInput:/<\/[\s\w:]+>$/,blockCommentStart:"\x3c!--",blockCommentEnd:"--\x3e",configuration:c.htmlMode?"html":"xml",helperType:c.htmlMode?"html":"xml",skipAttribute:function(t){t.state==C&&(t.state=y)},xmlCurrentTag:function(t){return t.tagName?{name:t.tagName,close:"closeTag"==t.type}:null},xmlCurrentContext:function(t){for(var e=[],n=t.context;n;n=n.prev)e.push(n.tagName);return e.reverse()}}})),t.defineMIME("text/xml","xml"),t.defineMIME("application/xml","xml"),t.mimeModes.hasOwnProperty("text/html")||t.defineMIME("text/html",{name:"xml",htmlMode:!0})}(n(4631))}}]);