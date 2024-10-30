!function(){"use strict";var e={8679:function(e,t,n){var r=n(9864),a={childContextTypes:!0,contextType:!0,contextTypes:!0,defaultProps:!0,displayName:!0,getDefaultProps:!0,getDerivedStateFromError:!0,getDerivedStateFromProps:!0,mixins:!0,propTypes:!0,type:!0},c={name:!0,length:!0,prototype:!0,caller:!0,callee:!0,arguments:!0,arity:!0},o={$$typeof:!0,compare:!0,defaultProps:!0,displayName:!0,propTypes:!0,type:!0},i={};function l(e){return r.isMemo(e)?o:i[e.$$typeof]||a}i[r.ForwardRef]={$$typeof:!0,render:!0,defaultProps:!0,displayName:!0,propTypes:!0},i[r.Memo]=o;var s=Object.defineProperty,u=Object.getOwnPropertyNames,d=Object.getOwnPropertySymbols,m=Object.getOwnPropertyDescriptor,v=Object.getPrototypeOf,p=Object.prototype;e.exports=function e(t,n,r){if("string"!=typeof n){if(p){var a=v(n);a&&a!==p&&e(t,a,r)}var o=u(n);d&&(o=o.concat(d(n)));for(var i=l(t),f=l(n),y=0;y<o.length;++y){var h=o[y];if(!(c[h]||r&&r[h]||f&&f[h]||i&&i[h])){var _=m(n,h);try{s(t,h,_)}catch(e){}}}}return t}},9921:function(e,t){var n="function"==typeof Symbol&&Symbol.for,r=n?Symbol.for("react.element"):60103,a=n?Symbol.for("react.portal"):60106,c=n?Symbol.for("react.fragment"):60107,o=n?Symbol.for("react.strict_mode"):60108,i=n?Symbol.for("react.profiler"):60114,l=n?Symbol.for("react.provider"):60109,s=n?Symbol.for("react.context"):60110,u=n?Symbol.for("react.async_mode"):60111,d=n?Symbol.for("react.concurrent_mode"):60111,m=n?Symbol.for("react.forward_ref"):60112,v=n?Symbol.for("react.suspense"):60113,p=n?Symbol.for("react.suspense_list"):60120,f=n?Symbol.for("react.memo"):60115,y=n?Symbol.for("react.lazy"):60116,h=n?Symbol.for("react.block"):60121,_=n?Symbol.for("react.fundamental"):60117,g=n?Symbol.for("react.responder"):60118,b=n?Symbol.for("react.scope"):60119;function w(e){if("object"==typeof e&&null!==e){var t=e.$$typeof;switch(t){case r:switch(e=e.type){case u:case d:case c:case i:case o:case v:return e;default:switch(e=e&&e.$$typeof){case s:case m:case y:case f:case l:return e;default:return t}}case a:return t}}}function E(e){return w(e)===d}t.AsyncMode=u,t.ConcurrentMode=d,t.ContextConsumer=s,t.ContextProvider=l,t.Element=r,t.ForwardRef=m,t.Fragment=c,t.Lazy=y,t.Memo=f,t.Portal=a,t.Profiler=i,t.StrictMode=o,t.Suspense=v,t.isAsyncMode=function(e){return E(e)||w(e)===u},t.isConcurrentMode=E,t.isContextConsumer=function(e){return w(e)===s},t.isContextProvider=function(e){return w(e)===l},t.isElement=function(e){return"object"==typeof e&&null!==e&&e.$$typeof===r},t.isForwardRef=function(e){return w(e)===m},t.isFragment=function(e){return w(e)===c},t.isLazy=function(e){return w(e)===y},t.isMemo=function(e){return w(e)===f},t.isPortal=function(e){return w(e)===a},t.isProfiler=function(e){return w(e)===i},t.isStrictMode=function(e){return w(e)===o},t.isSuspense=function(e){return w(e)===v},t.isValidElementType=function(e){return"string"==typeof e||"function"==typeof e||e===c||e===d||e===i||e===o||e===v||e===p||"object"==typeof e&&null!==e&&(e.$$typeof===y||e.$$typeof===f||e.$$typeof===l||e.$$typeof===s||e.$$typeof===m||e.$$typeof===_||e.$$typeof===g||e.$$typeof===b||e.$$typeof===h)},t.typeOf=w},9864:function(e,t,n){e.exports=n(9921)}},t={};function n(r){var a=t[r];if(void 0!==a)return a.exports;var c=t[r]={exports:{}};return e[r](c,c.exports,n),c.exports}n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,{a:t}),t},n.d=function(e,t){for(var r in t)n.o(t,r)&&!n.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:t[r]})},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},function(){var e={};n.r(e),n.d(e,{buckets:function(){return Xe},bucketsFetched:function(){return at},errors:function(){return nt},isLoading:function(){return tt},ui:function(){return ct},uploads:function(){return et},videos:function(){return Je},videosFetched:function(){return rt}});var t={};n.r(t),n.d(t,{addError:function(){return ht},addUploads:function(){return dt},addVideos:function(){return lt},removeError:function(){return _t},removeUpload:function(){return mt},removeVideo:function(){return st},setBuckets:function(){return it},setBucketsFetched:function(){return pt},setLoading:function(){return ft},setUI:function(){return yt},setUploads:function(){return ut},setVideos:function(){return ot},setVideosFetched:function(){return vt}});var r=window.wp.element,a=window.wp.domReady,c=n.n(a),o=window.React,i=function(){function e(e){var t=this;this._insertTag=function(e){var n;n=0===t.tags.length?t.insertionPoint?t.insertionPoint.nextSibling:t.prepend?t.container.firstChild:t.before:t.tags[t.tags.length-1].nextSibling,t.container.insertBefore(e,n),t.tags.push(e)},this.isSpeedy=void 0===e.speedy||e.speedy,this.tags=[],this.ctr=0,this.nonce=e.nonce,this.key=e.key,this.container=e.container,this.prepend=e.prepend,this.insertionPoint=e.insertionPoint,this.before=null}var t=e.prototype;return t.hydrate=function(e){e.forEach(this._insertTag)},t.insert=function(e){this.ctr%(this.isSpeedy?65e3:1)==0&&this._insertTag(function(e){var t=document.createElement("style");return t.setAttribute("data-emotion",e.key),void 0!==e.nonce&&t.setAttribute("nonce",e.nonce),t.appendChild(document.createTextNode("")),t.setAttribute("data-s",""),t}(this));var t=this.tags[this.tags.length-1];if(this.isSpeedy){var n=function(e){if(e.sheet)return e.sheet;for(var t=0;t<document.styleSheets.length;t++)if(document.styleSheets[t].ownerNode===e)return document.styleSheets[t]}(t);try{n.insertRule(e,n.cssRules.length)}catch(e){}}else t.appendChild(document.createTextNode(e));this.ctr++},t.flush=function(){this.tags.forEach((function(e){return e.parentNode&&e.parentNode.removeChild(e)})),this.tags=[],this.ctr=0},e}(),l=Math.abs,s=String.fromCharCode,u=Object.assign;function d(e){return e.trim()}function m(e,t,n){return e.replace(t,n)}function v(e,t){return e.indexOf(t)}function p(e,t){return 0|e.charCodeAt(t)}function f(e,t,n){return e.slice(t,n)}function y(e){return e.length}function h(e){return e.length}function _(e,t){return t.push(e),e}var g=1,b=1,w=0,E=0,x=0,S="";function k(e,t,n,r,a,c,o){return{value:e,root:t,parent:n,type:r,props:a,children:c,line:g,column:b,length:o,return:""}}function N(e,t){return u(k("",null,null,"",null,null,0),e,{length:-e.length},t)}function O(){return x=E>0?p(S,--E):0,b--,10===x&&(b=1,g--),x}function C(){return x=E<w?p(S,E++):0,b++,10===x&&(b=1,g++),x}function $(){return p(S,E)}function A(){return E}function D(e,t){return f(S,e,t)}function P(e){switch(e){case 0:case 9:case 10:case 13:case 32:return 5;case 33:case 43:case 44:case 47:case 62:case 64:case 126:case 59:case 123:case 125:return 4;case 58:return 3;case 34:case 39:case 40:case 91:return 2;case 41:case 93:return 1}return 0}function R(e){return g=b=1,w=y(S=e),E=0,[]}function T(e){return S="",e}function I(e){return d(D(E-1,U(91===e?e+2:40===e?e+1:e)))}function M(e){for(;(x=$())&&x<33;)C();return P(e)>2||P(x)>3?"":" "}function F(e,t){for(;--t&&C()&&!(x<48||x>102||x>57&&x<65||x>70&&x<97););return D(e,A()+(t<6&&32==$()&&32==C()))}function U(e){for(;C();)switch(x){case e:return E;case 34:case 39:34!==e&&39!==e&&U(x);break;case 40:41===e&&U(e);break;case 92:C()}return E}function j(e,t){for(;C()&&e+x!==57&&(e+x!==84||47!==$()););return"/*"+D(t,E-1)+"*"+s(47===e?e:C())}function B(e){for(;!P($());)C();return D(e,E)}var L="-ms-",V="-moz-",z="-webkit-",q="comm",G="rule",H="decl",W="@keyframes";function K(e,t){for(var n="",r=h(e),a=0;a<r;a++)n+=t(e[a],a,e,t)||"";return n}function Q(e,t,n,r){switch(e.type){case"@import":case H:return e.return=e.return||e.value;case q:return"";case W:return e.return=e.value+"{"+K(e.children,r)+"}";case G:e.value=e.props.join(",")}return y(n=K(e.children,r))?e.return=e.value+"{"+n+"}":""}function Y(e,t){switch(function(e,t){return(((t<<2^p(e,0))<<2^p(e,1))<<2^p(e,2))<<2^p(e,3)}(e,t)){case 5103:return z+"print-"+e+e;case 5737:case 4201:case 3177:case 3433:case 1641:case 4457:case 2921:case 5572:case 6356:case 5844:case 3191:case 6645:case 3005:case 6391:case 5879:case 5623:case 6135:case 4599:case 4855:case 4215:case 6389:case 5109:case 5365:case 5621:case 3829:return z+e+e;case 5349:case 4246:case 4810:case 6968:case 2756:return z+e+V+e+L+e+e;case 6828:case 4268:return z+e+L+e+e;case 6165:return z+e+L+"flex-"+e+e;case 5187:return z+e+m(e,/(\w+).+(:[^]+)/,"-webkit-box-$1$2-ms-flex-$1$2")+e;case 5443:return z+e+L+"flex-item-"+m(e,/flex-|-self/,"")+e;case 4675:return z+e+L+"flex-line-pack"+m(e,/align-content|flex-|-self/,"")+e;case 5548:return z+e+L+m(e,"shrink","negative")+e;case 5292:return z+e+L+m(e,"basis","preferred-size")+e;case 6060:return z+"box-"+m(e,"-grow","")+z+e+L+m(e,"grow","positive")+e;case 4554:return z+m(e,/([^-])(transform)/g,"$1-webkit-$2")+e;case 6187:return m(m(m(e,/(zoom-|grab)/,z+"$1"),/(image-set)/,z+"$1"),e,"")+e;case 5495:case 3959:return m(e,/(image-set\([^]*)/,z+"$1$`$1");case 4968:return m(m(e,/(.+:)(flex-)?(.*)/,"-webkit-box-pack:$3-ms-flex-pack:$3"),/s.+-b[^;]+/,"justify")+z+e+e;case 4095:case 3583:case 4068:case 2532:return m(e,/(.+)-inline(.+)/,z+"$1$2")+e;case 8116:case 7059:case 5753:case 5535:case 5445:case 5701:case 4933:case 4677:case 5533:case 5789:case 5021:case 4765:if(y(e)-1-t>6)switch(p(e,t+1)){case 109:if(45!==p(e,t+4))break;case 102:return m(e,/(.+:)(.+)-([^]+)/,"$1-webkit-$2-$3$1"+V+(108==p(e,t+3)?"$3":"$2-$3"))+e;case 115:return~v(e,"stretch")?Y(m(e,"stretch","fill-available"),t)+e:e}break;case 4949:if(115!==p(e,t+1))break;case 6444:switch(p(e,y(e)-3-(~v(e,"!important")&&10))){case 107:return m(e,":",":"+z)+e;case 101:return m(e,/(.+:)([^;!]+)(;|!.+)?/,"$1"+z+(45===p(e,14)?"inline-":"")+"box$3$1"+z+"$2$3$1"+L+"$2box$3")+e}break;case 5936:switch(p(e,t+11)){case 114:return z+e+L+m(e,/[svh]\w+-[tblr]{2}/,"tb")+e;case 108:return z+e+L+m(e,/[svh]\w+-[tblr]{2}/,"tb-rl")+e;case 45:return z+e+L+m(e,/[svh]\w+-[tblr]{2}/,"lr")+e}return z+e+L+e+e}return e}function Z(e){return T(J("",null,null,null,[""],e=R(e),0,[0],e))}function J(e,t,n,r,a,c,o,i,l){for(var u=0,d=0,p=o,f=0,h=0,g=0,b=1,w=1,E=1,x=0,S="",k=a,N=c,D=r,P=S;w;)switch(g=x,x=C()){case 40:if(108!=g&&58==P.charCodeAt(p-1)){-1!=v(P+=m(I(x),"&","&\f"),"&\f")&&(E=-1);break}case 34:case 39:case 91:P+=I(x);break;case 9:case 10:case 13:case 32:P+=M(g);break;case 92:P+=F(A()-1,7);continue;case 47:switch($()){case 42:case 47:_(ee(j(C(),A()),t,n),l);break;default:P+="/"}break;case 123*b:i[u++]=y(P)*E;case 125*b:case 59:case 0:switch(x){case 0:case 125:w=0;case 59+d:h>0&&y(P)-p&&_(h>32?te(P+";",r,n,p-1):te(m(P," ","")+";",r,n,p-2),l);break;case 59:P+=";";default:if(_(D=X(P,t,n,u,d,a,i,S,k=[],N=[],p),c),123===x)if(0===d)J(P,t,D,D,k,c,p,i,N);else switch(f){case 100:case 109:case 115:J(e,D,D,r&&_(X(e,D,D,0,0,a,i,S,a,k=[],p),N),a,N,p,i,r?k:N);break;default:J(P,D,D,D,[""],N,0,i,N)}}u=d=h=0,b=E=1,S=P="",p=o;break;case 58:p=1+y(P),h=g;default:if(b<1)if(123==x)--b;else if(125==x&&0==b++&&125==O())continue;switch(P+=s(x),x*b){case 38:E=d>0?1:(P+="\f",-1);break;case 44:i[u++]=(y(P)-1)*E,E=1;break;case 64:45===$()&&(P+=I(C())),f=$(),d=p=y(S=P+=B(A())),x++;break;case 45:45===g&&2==y(P)&&(b=0)}}return c}function X(e,t,n,r,a,c,o,i,s,u,v){for(var p=a-1,y=0===a?c:[""],_=h(y),g=0,b=0,w=0;g<r;++g)for(var E=0,x=f(e,p+1,p=l(b=o[g])),S=e;E<_;++E)(S=d(b>0?y[E]+" "+x:m(x,/&\f/g,y[E])))&&(s[w++]=S);return k(e,t,n,0===a?G:i,s,u,v)}function ee(e,t,n){return k(e,t,n,q,s(x),f(e,2,-2),0)}function te(e,t,n,r){return k(e,t,n,H,f(e,0,r),f(e,r+1,-1),r)}var ne=function(e,t,n){for(var r=0,a=0;r=a,a=$(),38===r&&12===a&&(t[n]=1),!P(a);)C();return D(e,E)},re=new WeakMap,ae=function(e){if("rule"===e.type&&e.parent&&!(e.length<1)){for(var t=e.value,n=e.parent,r=e.column===n.column&&e.line===n.line;"rule"!==n.type;)if(!(n=n.parent))return;if((1!==e.props.length||58===t.charCodeAt(0)||re.get(n))&&!r){re.set(e,!0);for(var a=[],c=function(e,t){return T(function(e,t){var n=-1,r=44;do{switch(P(r)){case 0:38===r&&12===$()&&(t[n]=1),e[n]+=ne(E-1,t,n);break;case 2:e[n]+=I(r);break;case 4:if(44===r){e[++n]=58===$()?"&\f":"",t[n]=e[n].length;break}default:e[n]+=s(r)}}while(r=C());return e}(R(e),t))}(t,a),o=n.props,i=0,l=0;i<c.length;i++)for(var u=0;u<o.length;u++,l++)e.props[l]=a[i]?c[i].replace(/&\f/g,o[u]):o[u]+" "+c[i]}}},ce=function(e){if("decl"===e.type){var t=e.value;108===t.charCodeAt(0)&&98===t.charCodeAt(2)&&(e.return="",e.value="")}},oe=[function(e,t,n,r){if(e.length>-1&&!e.return)switch(e.type){case H:e.return=Y(e.value,e.length);break;case W:return K([N(e,{value:m(e.value,"@","@"+z)})],r);case G:if(e.length)return function(e,t){return e.map(t).join("")}(e.props,(function(t){switch(function(e,t){return(e=/(::plac\w+|:read-\w+)/.exec(e))?e[0]:e}(t)){case":read-only":case":read-write":return K([N(e,{props:[m(t,/:(read-\w+)/,":-moz-$1")]})],r);case"::placeholder":return K([N(e,{props:[m(t,/:(plac\w+)/,":-webkit-input-$1")]}),N(e,{props:[m(t,/:(plac\w+)/,":-moz-$1")]}),N(e,{props:[m(t,/:(plac\w+)/,L+"input-$1")]})],r)}return""}))}}],ie=function(e){var t=e.key;if("css"===t){var n=document.querySelectorAll("style[data-emotion]:not([data-s])");Array.prototype.forEach.call(n,(function(e){-1!==e.getAttribute("data-emotion").indexOf(" ")&&(document.head.appendChild(e),e.setAttribute("data-s",""))}))}var r,a,c=e.stylisPlugins||oe,o={},l=[];r=e.container||document.head,Array.prototype.forEach.call(document.querySelectorAll('style[data-emotion^="'+t+' "]'),(function(e){for(var t=e.getAttribute("data-emotion").split(" "),n=1;n<t.length;n++)o[t[n]]=!0;l.push(e)}));var s,u,d,m,v=[Q,(m=function(e){s.insert(e)},function(e){e.root||(e=e.return)&&m(e)})],p=(u=[ae,ce].concat(c,v),d=h(u),function(e,t,n,r){for(var a="",c=0;c<d;c++)a+=u[c](e,t,n,r)||"";return a});a=function(e,t,n,r){s=n,K(Z(e?e+"{"+t.styles+"}":t.styles),p),r&&(f.inserted[t.name]=!0)};var f={key:t,sheet:new i({key:t,container:r,nonce:e.nonce,speedy:e.speedy,prepend:e.prepend,insertionPoint:e.insertionPoint}),nonce:e.nonce,inserted:o,registered:{},insert:a};return f.sheet.hydrate(l),f},le=function(e,t,n){var r=e.key+"-"+t.name;!1===n&&void 0===e.registered[r]&&(e.registered[r]=t.styles)},se=function(e){for(var t,n=0,r=0,a=e.length;a>=4;++r,a-=4)t=1540483477*(65535&(t=255&e.charCodeAt(r)|(255&e.charCodeAt(++r))<<8|(255&e.charCodeAt(++r))<<16|(255&e.charCodeAt(++r))<<24))+(59797*(t>>>16)<<16),n=1540483477*(65535&(t^=t>>>24))+(59797*(t>>>16)<<16)^1540483477*(65535&n)+(59797*(n>>>16)<<16);switch(a){case 3:n^=(255&e.charCodeAt(r+2))<<16;case 2:n^=(255&e.charCodeAt(r+1))<<8;case 1:n=1540483477*(65535&(n^=255&e.charCodeAt(r)))+(59797*(n>>>16)<<16)}return(((n=1540483477*(65535&(n^=n>>>13))+(59797*(n>>>16)<<16))^n>>>15)>>>0).toString(36)},ue={animationIterationCount:1,borderImageOutset:1,borderImageSlice:1,borderImageWidth:1,boxFlex:1,boxFlexGroup:1,boxOrdinalGroup:1,columnCount:1,columns:1,flex:1,flexGrow:1,flexPositive:1,flexShrink:1,flexNegative:1,flexOrder:1,gridRow:1,gridRowEnd:1,gridRowSpan:1,gridRowStart:1,gridColumn:1,gridColumnEnd:1,gridColumnSpan:1,gridColumnStart:1,msGridRow:1,msGridRowSpan:1,msGridColumn:1,msGridColumnSpan:1,fontWeight:1,lineHeight:1,opacity:1,order:1,orphans:1,tabSize:1,widows:1,zIndex:1,zoom:1,WebkitLineClamp:1,fillOpacity:1,floodOpacity:1,stopOpacity:1,strokeDasharray:1,strokeDashoffset:1,strokeMiterlimit:1,strokeOpacity:1,strokeWidth:1},de=/[A-Z]|^ms/g,me=/_EMO_([^_]+?)_([^]*?)_EMO_/g,ve=function(e){return 45===e.charCodeAt(1)},pe=function(e){return null!=e&&"boolean"!=typeof e},fe=function(e){var t=Object.create(null);return function(e){return void 0===t[e]&&(t[e]=ve(n=e)?n:n.replace(de,"-$&").toLowerCase()),t[e];var n}}(),ye=function(e,t){switch(e){case"animation":case"animationName":if("string"==typeof t)return t.replace(me,(function(e,t,n){return _e={name:t,styles:n,next:_e},t}))}return 1===ue[e]||ve(e)||"number"!=typeof t||0===t?t:t+"px"};function he(e,t,n){if(null==n)return"";if(void 0!==n.__emotion_styles)return n;switch(typeof n){case"boolean":return"";case"object":if(1===n.anim)return _e={name:n.name,styles:n.styles,next:_e},n.name;if(void 0!==n.styles){var r=n.next;if(void 0!==r)for(;void 0!==r;)_e={name:r.name,styles:r.styles,next:_e},r=r.next;return n.styles+";"}return function(e,t,n){var r="";if(Array.isArray(n))for(var a=0;a<n.length;a++)r+=he(e,t,n[a])+";";else for(var c in n){var o=n[c];if("object"!=typeof o)null!=t&&void 0!==t[o]?r+=c+"{"+t[o]+"}":pe(o)&&(r+=fe(c)+":"+ye(c,o)+";");else if(!Array.isArray(o)||"string"!=typeof o[0]||null!=t&&void 0!==t[o[0]]){var i=he(e,t,o);switch(c){case"animation":case"animationName":r+=fe(c)+":"+i+";";break;default:r+=c+"{"+i+"}"}}else for(var l=0;l<o.length;l++)pe(o[l])&&(r+=fe(c)+":"+ye(c,o[l])+";")}return r}(e,t,n);case"function":if(void 0!==e){var a=_e,c=n(e);return _e=a,he(e,t,c)}}if(null==t)return n;var o=t[n];return void 0!==o?o:n}var _e,ge=/label:\s*([^\s;\n{]+)\s*(;|$)/g,be=function(e,t,n){if(1===e.length&&"object"==typeof e[0]&&null!==e[0]&&void 0!==e[0].styles)return e[0];var r=!0,a="";_e=void 0;var c=e[0];null==c||void 0===c.raw?(r=!1,a+=he(n,t,c)):a+=c[0];for(var o=1;o<e.length;o++)a+=he(n,t,e[o]),r&&(a+=c[o]);ge.lastIndex=0;for(var i,l="";null!==(i=ge.exec(a));)l+="-"+i[1];return{name:se(a)+l,styles:a,next:_e}},we=!!o.useInsertionEffect&&o.useInsertionEffect,Ee=we||function(e){return e()},xe=(we||o.useLayoutEffect,{}.hasOwnProperty),Se=(0,o.createContext)("undefined"!=typeof HTMLElement?ie({key:"css"}):null);Se.Provider;var ke=function(e){return(0,o.forwardRef)((function(t,n){var r=(0,o.useContext)(Se);return e(t,r,n)}))},Ne=(0,o.createContext)({}),Oe="__EMOTION_TYPE_PLEASE_DO_NOT_USE__",Ce=function(e,t){var n={};for(var r in t)xe.call(t,r)&&(n[r]=t[r]);return n[Oe]=e,n},$e=function(e){var t=e.cache,n=e.serialized,r=e.isStringTag;return le(t,n,r),Ee((function(){return function(e,t,n){le(e,t,n);var r=e.key+"-"+t.name;if(void 0===e.inserted[t.name]){var a=t;do{e.insert(t===a?"."+r:"",a,e.sheet,!0),a=a.next}while(void 0!==a)}}(t,n,r)})),null},Ae=ke((function(e,t,n){var r=e.css;"string"==typeof r&&void 0!==t.registered[r]&&(r=t.registered[r]);var a=e[Oe],c=[r],i="";"string"==typeof e.className?i=function(e,t,n){var r="";return n.split(" ").forEach((function(n){void 0!==e[n]?t.push(e[n]+";"):r+=n+" "})),r}(t.registered,c,e.className):null!=e.className&&(i=e.className+" ");var l=be(c,void 0,(0,o.useContext)(Ne));i+=t.key+"-"+l.name;var s={};for(var u in e)xe.call(e,u)&&"css"!==u&&u!==Oe&&(s[u]=e[u]);return s.ref=n,s.className=i,(0,o.createElement)(o.Fragment,null,(0,o.createElement)($e,{cache:t,serialized:l,isStringTag:"string"==typeof a}),(0,o.createElement)(a,s))}));n(8679);var De=function(e,t){var n=arguments;if(null==t||!xe.call(t,"css"))return o.createElement.apply(void 0,n);var r=n.length,a=new Array(r);a[0]=Ae,a[1]=Ce(e,t);for(var c=2;c<r;c++)a[c]=n[c];return o.createElement.apply(null,a)};function Pe(){for(var e=arguments.length,t=new Array(e),n=0;n<e;n++)t[n]=arguments[n];return be(t)}var Re=window.wp.i18n,Te=window.wp.components,Ie=window.wp.apiFetch,Me=n.n(Ie),Fe=window.wp.data;const Ue=e=>{let{item:t}=e,n=!1;mcv_addons.actived&&mcv_addons.actived.filter((e=>e.id==t.id)).length>0&&(n=!0);const[a,c]=(0,r.useState)(n),o=e=>{var n=layer.load(1,{shade:[.3,"#fff"]});Me()({path:"mine-cloudvod/v1/addons/buy",method:"POST",data:{addons:t.id,price:t.price,met:e}}).then((n=>{if(layer.closeAll(),"1"==n.status){n.data.tradeno;var r="#00a7ef",a=mcv_addons.plugin_url+"/static/img/alipay.jpg",c=(0,Re.__)("Alipay scan code payment","mine-cloudvod"),i=(0,Re.__)("Please use Alipay <br>to scan the QR code to pay","mine-cloudvod"),l="435.4px",s="";"wxpay"==e&&(r="#00b54b",a=mcv_addons.plugin_url+"/static/img/wxzf.jpg",c=(0,Re.__)("Wechat scan code payment","mine-cloudvod"),i=(0,Re.__)("Please use Wechat <br>to scan the QR code to pay","mine-cloudvod"),l="422.4px",t.price>999&&(s="display:none;",l="390.4px")),layer.open({type:1,title:!1,area:["300px",l],content:'<style>.layui-layer-content{overflow:hidden !important;}#btb_alipay,#btb_wxpay{width:50%;color: #fff;display:inline-block;margin:0;padding:0;border:none;cursor:pointer;padding:7px 0;background:#ddd;}#btb_alipay{background:#00a7ef;border-color:#00a7ef;}#btb_wxpay{background:#00b54b;border-color:#00b54b;}</style><div id="swal2-content" style="display: block;width:300px;text-align: center;"><div style="border-bottom: 2px solid '+r+";"+s+'"><input type="button" id="btb_alipay" class="" value="Alipay"><input type="button" id="btb_wxpay" class="cur" value="Wechat"></div><div style=""> <h5 style="padding: 0;margin-top: 1.8em;"> <img src="'+a+'" style="display: inline-block;margin: 0;padding: 0;width: 120px;text-align: center;"> </h5> <div style="font-size: 16px;margin: 10px auto;">'+c+" "+n.data.payamount+" "+(0,Re.__)("Yuan","mine-cloudvod")+'</div> <div align="center" class="qrcode"> <img style="width: 200px;height: 200px;" src="'+n.data.paycode+'" id="buytimebug_qrcode"> </div> <div style="width: 100%;color: #f2f2f2;padding: 16px 0px;text-align: center;font-size: 14px;margin-top: 20px;background: '+r+';"> '+i+"<br> </div> </div></div>",success:function(e,t){jQuery("#btb_alipay",e).on("click",(function(){o("alipay")})),jQuery("#btb_wxpay",e).on("click",(function(){o("wxpay")}))}})}})).catch((e=>{layer.close(n),console.log(e)}))};return De("div",{className:"mcv-col-lg-6 mcv-col-xl-4 mcv-col-xxl-3 mcv-mb-32"},De("div",{className:t.require?"mcv-card mcv-card-md mcv-addon-card is-require mcv-addon-card-2":"mcv-card mcv-card-md mcv-addon-card  mcv-addon-card-2"},"pro"==t.type?De("div",{className:"tooltip-wrap mcv-price pro"},De("b",null,"Pro"),De("span",{className:"tooltip-txt tooltip-top"},"Available in Pro")):null,"free"==t.type?De("div",{className:"tooltip-wrap mcv-price free"},De("i",null,"Free"),De("span",{className:"tooltip-txt tooltip-top"},"Free")):null,"buy"==t.type?De("div",{className:"tooltip-wrap mcv-price buy"},De("font",{color:"green",size:"3"},De("b",null,"￥",t.price)),mcv_addons?.myaddons?.includes(t.id)?"已购买":De("del",null,"￥",t.oprice),De("span",{className:"tooltip-txt tooltip-top"},(0,Re.__)("One-time Fee.","mine-cloudvod"))):null,De("div",{className:"mcv-card-body"},De("div",{className:"mcv-addon-logo mcv-mb-32"},De("div",{className:"mcv-ratio mcv-ratio-1x1"},De("img",{src:t.logo,alt:t.name}))),De("div",{className:"mcv-addon-title mcv-fs-6 mcv-fw-medium mcv-color-black mcv-mb-20"},t.name,t.doc&&De("a",{href:t.doc,target:"_blank",style:{marginLeft:"20px"}},De("svg",{t:"1677557299272",className:"icon",viewBox:"0 0 1024 1024",version:"1.1",xmlns:"http://www.w3.org/2000/svg",width:"14",height:"14"},De("path",{d:"M719.168 207.168L576 64h384v384l-150.272-150.272-264.128 264.064-90.496-90.496 264.064-264.128zM192 960H64V64h384v128H192v640h640V576h128v384H192z",fill:"#13227a"})),"  ",(0,Re.__)("Doc","mine-cloudvod"))),De("div",{className:"mcv-addon-description mcv-fs-7 mcv-color-secondary"},t.description)),De("div",{className:"mcv-card-footer mcv-d-flex mcv-justify-between mcv-align-center mcv-mt-auto"},De("div",{className:"mcv-fs-7 mcv-fw-medium mcv-color-muted"},De("div",{className:"mcv-color-muted mcv-fs-7 mcv-fw-medium mcv-d-flex"},t.require?De("span",{dangerouslySetInnerHTML:{__html:(0,Re.__)("Required","mine-cloudvod")+": "+t.require}}):De("span",null,(0,Re.__)("No extra plugin required","mine-cloudvod")))),mcv_addons?.myaddons?.includes(t.id)||"pro"==t.type||"free"==t.type?De("div",{style:{display:"flex"}},De(Te.ToggleControl,{onChange:e=>{(async e=>{if("pro"==t.type&&e&&mcv_addons.et<Date.parse(new Date)/1e3)return void(0,Fe.dispatch)("mine-cloudvod/vod").setUI("proFeature",!0);e||c(!1);let n=await Me()({path:"mine-cloudvod/v1/addons/active",method:"POST",data:{addons:t.id,status:e}});e&&n.result&&c(!0),n.result||layer.msg((0,Re.__)("Add-ons activation failed!","mine-cloudvod"))})(e)},checked:a,css:Pe`margin-bottom:0!important;`}),a&&t.setting&&De("a",{href:t.setting},De("svg",{className:"icon",viewBox:"0 0 1024 1024",version:"1.1",xmlns:"http://www.w3.org/2000/svg",width:"18",height:"18"},De("path",{d:"M512.5 390.6c-29.9 0-57.9 11.6-79.1 32.8-21.1 21.2-32.8 49.2-32.8 79.1 0 29.9 11.7 57.9 32.8 79.1 21.2 21.1 49.2 32.8 79.1 32.8 29.9 0 57.9-11.7 79.1-32.8 21.1-21.2 32.8-49.2 32.8-79.1 0-29.9-11.7-57.9-32.8-79.1-21.2-21.2-49.2-32.8-79.1-32.8z"}),De("path",{d:"M924.8 626.1l-65.4-55.9c3.1-19 4.7-38.4 4.7-57.7s-1.6-38.8-4.7-57.7l65.4-55.9c10.1-8.6 13.8-22.6 9.3-35.2l-0.9-2.6c-18.1-50.4-44.8-96.8-79.6-137.7l-1.8-2.1c-8.6-10.1-22.5-13.9-35.1-9.5l-81.2 28.9c-30-24.6-63.4-44-99.6-57.5l-15.7-84.9c-2.4-13.1-12.7-23.3-25.8-25.7l-2.7-0.5c-52-9.4-106.8-9.4-158.8 0l-2.7 0.5c-13.1 2.4-23.4 12.6-25.8 25.7l-15.8 85.3c-35.9 13.6-69.1 32.9-98.9 57.3l-81.8-29.1c-12.5-4.4-26.5-0.7-35.1 9.5l-1.8 2.1c-34.8 41.1-61.5 87.4-79.6 137.7l-0.9 2.6c-4.5 12.5-0.8 26.5 9.3 35.2l66.2 56.5c-3.1 18.8-4.6 38-4.6 57 0 19.2 1.5 38.4 4.6 57l-66 56.5c-10.1 8.6-13.8 22.6-9.3 35.2l0.9 2.6c18.1 50.3 44.8 96.8 79.6 137.7l1.8 2.1c8.6 10.1 22.5 13.9 35.1 9.5l81.8-29.1c29.8 24.5 63 43.9 98.9 57.3l15.8 85.3c2.4 13.1 12.7 23.3 25.8 25.7l2.7 0.5c26.1 4.7 52.7 7.1 79.4 7.1 26.7 0 53.4-2.4 79.4-7.1l2.7-0.5c13.1-2.4 23.4-12.6 25.8-25.7l15.7-84.9c36.2-13.6 69.6-32.9 99.6-57.5l81.2 28.9c12.5 4.4 26.5 0.7 35.1-9.5l1.8-2.1c34.8-41.1 61.5-87.4 79.6-137.7l0.9-2.6c4.3-12.4 0.6-26.3-9.5-35z m-412.3 52.2c-97.1 0-175.8-78.7-175.8-175.8s78.7-175.8 175.8-175.8 175.8 78.7 175.8 175.8-78.7 175.8-175.8 175.8z"})))):De(Te.Button,{variant:"primary",onClick:()=>{o("wxpay")},style:{height:"22px"}},(0,Re.__)("Click to buy","mine-cloudvod")))))},je=()=>{const[e,t]=(0,r.useState)(""),[n,a]=(0,r.useState)(""),[c,o]=(0,r.useState)("");if(!mcv_addons?.list)return;const[i,l]=(0,r.useState)(!1);return(0,r.useEffect)((()=>{Me()({path:"mine-cloudvod/v1/addons/infos",method:"POST"}).then((e=>{mcv_addons.et=new Date(e.et).getTime()/1e3,mcv_addons.myaddons=Array.isArray(e.addons)?e.addons:[],l(!0)})).catch((e=>{console.log(e),mcv_addons.myaddons=[],l(!0)}))}),[]),i?De("div",null,De("header",{className:"mcv-wp-dashboard-header mcv-px-24 mcv-mb-24"},De("div",{className:"mcv-row mcv-align-lg-center"},De("div",{className:"mcv-col-lg"},De("div",{className:"mcv-p-12"},De("span",{className:"mcv-fs-5 mcv-fw-medium mcv-mr-16"},(0,Re.__)("Add-ons","mine-cloudvod")))),De("div",{className:"mcv-col-lg-auto"},De("ul",{className:"mcv-nav mcv-nav-admin"},De("li",{className:"mcv-nav-item",onClick:()=>{t("")}},De("a",{className:"mcv-nav-link"+(""==e?" is-active":"")},(0,Re.__)("All","mine-cloudvod"))),De("li",{className:"mcv-nav-item",onClick:()=>{t("plyr"),a("")}},De("a",{className:"mcv-nav-link"+("plyr"==e?" is-active":"")},(0,Re.__)("Player","mine-cloudvod"))),De("li",{className:"mcv-nav-item",onClick:()=>{t("lms"),a("")}},De("a",{className:"mcv-nav-link"+("lms"==e?" is-active":"")},(0,Re.__)("LMS","mine-cloudvod"))),De("li",{className:"mcv-nav-item",onClick:()=>{t("integration"),a("")}},De("a",{className:"mcv-nav-link"+("integration"==e?" is-active":"")},(0,Re.__)("Integration","mine-cloudvod"))),De("li",{className:"mcv-nav-item",onClick:()=>{t("active"),a("")}},De("a",{className:"mcv-nav-link"+("active"==e?" is-active":"")},(0,Re.__)("Active","mine-cloudvod")+" ("+mcv_addons.actived?.length+")")),De("li",{className:"mcv-nav-item",onClick:()=>{t("deactive"),a("")}},De("a",{className:"mcv-nav-link"+("deactive"==e?" is-active":"")},(0,Re.__)("Deactive","mine-cloudvod")+" ("+(mcv_addons.list.length-mcv_addons.actived.length)+")")))))),De("div",{className:"mcv-admin-body",style:{display:"flex",flexDirection:"row",justifyContent:"flex-end",flexWrap:"wrap"}},De(Te.TextControl,{onChange:e=>{o(e)},value:c,style:{padding:"9px"}}),De(Te.ButtonGroup,{style:{margin:"0 0 10px 10px"}},De(Te.Button,{variant:""==n?"primary":"secondary",onClick:()=>a("")},(0,Re.__)("All","mine-cloudvod")),De(Te.Button,{variant:"free"==n?"primary":"secondary",onClick:()=>a("free")},(0,Re.__)("Free","mine-cloudvod")),De(Te.Button,{variant:"pro"==n?"primary":"secondary",onClick:()=>a("pro")},(0,Re.__)("Pro","mine-cloudvod")),De(Te.Button,{variant:"buy"==n?"primary":"secondary",onClick:()=>a("buy")},(0,Re.__)("One-time Fee.","mine-cloudvod")))),De("div",{className:"mcv-admin-body"},De("div",{className:"mcv-addons-list-body"},De("div",{className:"mcv-addons-list-items mcv-row mcv-gx-xxl-4 mcv-mt-32"},mcv_addons.list.filter((t=>"active"==e?mcv_addons.actived.some((e=>e.id===t.id)):"deactive"==e?!mcv_addons.actived.some((e=>e.id===t.id)):(""==e||e==t.tag)&&(""==n||t.type==n)&&(""==c||t.name.indexOf(c)>-1||t.description.indexOf(c)>-1||t.id.indexOf(c)>-1))).map((e=>De(Ue,{item:e,key:e.id}))))))):De("center",null,De(Te.Spinner,null))};var Be=()=>(0,r.createElement)("div",{className:"mcv-admin-wrap"},(0,r.createElement)(je,null));const Le=()=>{let e="overview",t=Ve("sub");return t&&(e=t),e},Ve=e=>{var t=new RegExp("(^|&)"+e+"=([^&]*)(&|$)","i"),n=window.location.search.substr(1).match(t);return null!=n?unescape(n[2]):null},ze=e=>{let{title:t}=e,n=Le();return(0,r.createElement)("header",{className:"mcv-wp-dashboard-header mcv-px-24 mcv-mb-24"},(0,r.createElement)("div",{className:"mcv-row mcv-align-lg-center"},(0,r.createElement)("div",{className:"mcv-col-lg"},(0,r.createElement)("div",{className:"mcv-p-12"},(0,r.createElement)("span",{className:"mcv-fs-5 mcv-fw-medium mcv-mr-16"},t))),(0,r.createElement)("div",{className:"mcv-col-lg-auto"},(0,r.createElement)("ul",{className:"mcv-nav mcv-nav-admin"},(0,r.createElement)("li",{className:"mcv-nav-item"},(0,r.createElement)("a",{className:"mcv-nav-link"+("overview"==n?" is-active":""),href:window?.mcv_nonce.pageurl+"&sub=overview"},(0,Re.__)("Overview","mine-cloudvod"))),(0,r.createElement)("li",{className:"mcv-nav-item"},(0,r.createElement)("a",{className:"mcv-nav-link"+("order"==n?" is-active":""),href:window?.mcv_nonce.pageurl+"&sub=order"},(0,Re.__)("Orders","mine-cloudvod"))),(0,r.createElement)("li",{className:"mcv-nav-item"},(0,r.createElement)("a",{className:"mcv-nav-link"+("student"==n?" is-active":""),href:window?.mcv_nonce.pageurl+"&sub=student"},(0,Re.__)("Student","mine-cloudvod")))))))},qe=()=>{const[e,t]=(0,r.useState)([]);return(0,r.useEffect)((()=>{Me()({path:"mine-cloudvod/v1/lms/report/overview",method:"POST",data:{}}).then((e=>{console.log(e),t(e.count)})).catch((e=>{console.log(e)}))}),[]),(0,r.createElement)("div",null,(0,r.createElement)(ze,{title:(0,Re.__)("Overview","mine-cloudvod")}),(0,r.createElement)("div",{className:"mcv-admin-body mcv-row mcv-gx-4"},(0,r.createElement)("div",{className:"mcv-col-md-6 mcv-col-xl-3 mcv-my-8 mcv-my-md-16"},(0,r.createElement)("div",{className:"mcv-card mcv-card-secondary mcv-p-24"},(0,r.createElement)("div",{className:"mcv-d-flex"},(0,r.createElement)("div",{className:"mcv-ml-20"},(0,r.createElement)("div",{className:"mcv-fs-4 mcv-fw-bold mcv-color-black"},e.course),(0,r.createElement)("div",{className:"mcv-fs-7 mcv-color-secondary"},(0,Re.__)("Published Courses","mine-cloudvod")))))),(0,r.createElement)("div",{className:"mcv-col-md-6 mcv-col-xl-3 mcv-my-8 mcv-my-md-16"},(0,r.createElement)("div",{className:"mcv-card mcv-card-secondary mcv-p-24"},(0,r.createElement)("div",{className:"mcv-d-flex"},(0,r.createElement)("div",{className:"mcv-ml-20"},(0,r.createElement)("div",{className:"mcv-fs-4 mcv-fw-bold mcv-color-black"},e.student),(0,r.createElement)("div",{className:"mcv-fs-7 mcv-color-secondary"},(0,Re.__)("Students","mine-cloudvod"))))))))},Ge=()=>{const[e,t]=(0,r.useState)([]),[n,a]=(0,r.useState)({});return(0,r.useEffect)((()=>{Me()({path:"mine-cloudvod/v1/lms/report/order",method:"POST",data:{user_id:Ve("user_id")}}).then((e=>{t(e.list),e.user&&a(e.user)})).catch((e=>{console.log(e)}))}),[]),De("div",null,De(ze,{title:(0,Re.__)("Orders","mine-cloudvod")}),e&&0!=e.length?De("div",{className:"mcv-admin-body"},n?.ID&&De("p",null,(0,Re.sprintf)((0,Re.__)("Orders created by %1$s","mine-cloudvod"),n.data.user_nicename)),De("table",{className:"wp-list-table widefat striped"},De("thead",null,De("tr",null,De("th",{scope:"col"},"#"),De("th",{scope:"col"},(0,Re.__)("Title")),De("th",{scope:"col"},(0,Re.__)("Status","mine-cloudvod")),De("th",{scope:"col"},(0,Re.__)("Order Amount","mine-cloudvod")),De("th",{scope:"col"},(0,Re.__)("Payment Method","mine-cloudvod")),De("th",{scope:"col"},(0,Re.__)("User")),De("th",{scope:"col"},(0,Re.__)("Date Created","mine-cloudvod")),De("th",{scope:"col"}," "))),De("tbody",null,e.map((e=>De("tr",{key:"key_"+e.ID},De("th",{scope:"row"},e.ID),De("td",null,e.post_title),De("td",null,e.order_status),De("td",null,e.order_amount),De("td",null,e.order_payment),De("td",null,e.post_author.data.user_nicename),De("td",null,e.create_time),De("td",null,De("a",{href:""},(0,Re.__)("Details"))))))))):De("center",null,De(Te.Spinner,null)))},He=()=>{const[e,t]=(0,r.useState)([]),[n,a]=(0,r.useState)({});return(0,r.useEffect)((()=>{Me()({path:"mine-cloudvod/v1/lms/report/course",method:"POST",data:{user_id:Ve("user_id")}}).then((e=>{t(e.list),e.user&&a(e.user)})).catch((e=>{console.log(e)}))}),[]),De("div",null,De(ze,{title:(0,Re.__)("Student","mine-cloudvod")}),e&&0!=e.length?De("div",{className:"mcv-admin-body"},n?.ID&&De("p",null,(0,Re.sprintf)((0,Re.__)("Courses enrolled by %1$s","mine-cloudvod"),n.data.user_nicename)),De("table",{className:"wp-list-table widefat striped"},De("thead",null,De("tr",null,De("th",{scope:"col"},"#"),De("th",{scope:"col"},(0,Re.__)("Title")),De("th",{scope:"col"},(0,Re.__)("Post type")),De("th",{scope:"col"},(0,Re.__)("Enrolled Date","mine-cloudvod")),De("th",{scope:"col"},(0,Re.__)("Progress","mine-cloudvod")))),De("tbody",null,e.map((e=>De("tr",{key:"key_"+e.ID},De("th",{scope:"row"},e.ID),De("td",null,De("a",{href:e.link,target:"_blank"},e.post_title)),De("td",null,e.post_type),De("td",null,e.enrolled_date),De("td",null,e?.progress,"%"))))))):De("center",null,De(Te.Spinner,null)))},We=()=>{if(Ve("user_id"))return De(He,null);const[e,t]=(0,r.useState)([]),[n,a]=(0,r.useState)({});return(0,r.useEffect)((()=>{Me()({path:"mine-cloudvod/v1/lms/course/student",method:"POST",data:{course_id:Ve("course_id")}}).then((e=>{t(e.list),e.course&&a(e.course)})).catch((e=>{console.log(e)}))}),[]),0==e.length?De("center",null,De(Te.Spinner,null)):De("div",null,De(ze,{title:(0,Re.__)("Student","mine-cloudvod")}),De("div",{className:"mcv-admin-body"},n?.ID&&De("p",null,(0,Re.sprintf)((0,Re.__)("Students of %1$s","mine-cloudvod"),n.post_title)),De("table",{className:"wp-list-table widefat striped"},De("thead",null,De("tr",null,De("th",{scope:"col"},"#"),De("th",{scope:"col"},(0,Re.__)("Username")),De("th",{scope:"col"},(0,Re.__)("Email")),De("th",{scope:"col"},(0,Re.__)("Number of enrolled","mine-cloudvod")),De("th",{scope:"col"},(0,Re.__)("Number of orders","mine-cloudvod")),De("th",{scope:"col"},(0,Re.__)("Registration Date")))),De("tbody",null,e.map((e=>De("tr",{key:"key_"+e.id},De("th",{scope:"row"},e.id),De("td",null,e.username),De("td",null,e.email),De("td",null,De("a",{href:window.mcv_nonce.pageurl+"&sub=student&user_id="+e.id},e.enrolled_num)),De("td",null,De("a",{href:window.mcv_nonce.pageurl+"&sub=order&user_id="+e.id},e.order_num)),De("td",null,e.registered))))))))};var Ke=()=>{let e=Le();return(0,r.createElement)("div",{className:"mcv-admin-wrap"},"overview"==e&&(0,r.createElement)(qe,null),"order"==e&&(0,r.createElement)(Ge,null),"student"==e&&(0,r.createElement)(We,null))};function Qe(){return(0,Fe.useSelect)((e=>e("mine-cloudvod/vod").ui("proFeature")))?(0,r.createElement)(Te.Modal,{title:wp.i18n.__("Pro Feature","mine-cloudvod"),onRequestClose:()=>{(0,Fe.dispatch)("mine-cloudvod/vod").setUI("proFeature",!1)}},(0,r.createElement)("h2",null,wp.i18n.__("Unlock Mine CloudVoD","mine-cloudvod")),(0,r.createElement)("p",null,wp.i18n.__("Get this feature and more with the Pro version of Mine CloudVod!","mine-cloudvod")),(0,r.createElement)(Te.Button,{href:mcv_nonce.buynow,target:"_blank",isPrimary:!0},wp.i18n.__("Buy Now","mine-cloudvod")),(0,r.createElement)(Te.Button,{href:"https://www.zwtt8.com/docs-category/mine-cloudvod/",target:"_blank"},wp.i18n.__("Learn More","mine-cloudvod"))):""}const{combineReducers:Ye}=wp.data;var Ze=Ye({videosReducer:function(){let e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:[],t=arguments.length>1?arguments[1]:void 0;switch(t.type){case"SET_VIDEOS":return t.value;case"ADD_VIDEOS":return[...e,...t.value];case"ADD_VIDEO":return[...e,t.value];case"REMOVE_VIDEO":return e.filter((e=>e.videoId!==t.value.videoId))}return e},uploadsReducer:function(){let e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:[],t=arguments.length>1?arguments[1]:void 0;switch(t.type){case"SET_UPLOADS":return t.value;case"ADD_UPLOADS":return[...e,...t.value];case"ADD_UPLOAD":return[...e,t.value];case"UPDATE_UPLOAD":return e.map(((e,n)=>e.id!==t.value.id?e:{...e,...t.value}));case"REMOVE_UPLOAD":return e.filter((e=>e!==t.value))}return e},UIReducer:function(){let e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{loading:!1,videosFetched:!1,bucketsFetched:!1,createCollection:!1,selectedId:null},t=arguments.length>1?arguments[1]:void 0;switch(t.type){case"SET_LOADING":return{...e,loading:t.value};case"SET_VIDEOS_FETCHED":return{...e,videosFetched:t.value};case"SET_BUCKETS_FETCHED":return{...e,bucketsFetched:t.value};case"SET_UI_ITEM":return{...e,[t.item]:t.value}}return e},errorReducer:function(){let e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:[],t=arguments.length>1?arguments[1]:void 0;switch(t.type){case"ADD_ERROR":return[...e,t.value];case"REMOVE_ERROR":return e.filter((e=>e!==t.value))}return e},bucketsReducer:function(){let e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:[],t=arguments.length>1?arguments[1]:void 0;return"SET_BUCKETS"===t.type?t.value:e}});function Je(e){return e.videosReducer||[]}function Xe(e){return e.bucketsReducer||[]}function et(e){return e.uploadsReducer||[]}function tt(e){return e.UIReducer.loading||!1}function nt(e){return e.errorReducer||[]}function rt(e){return e.UIReducer.videosFetched||!1}function at(e){return e.UIReducer.bucketsFetched||!1}function ct(e,t){return e.UIReducer[t]}function ot(e){return{type:"SET_VIDEOS",value:e}}function it(e){return{type:"SET_BUCKETS",value:e}}function lt(e){return{type:"ADD_VIDEOS",value:e}}function st(e){return{type:"REMOVE_VIDEO",value:e}}function ut(e){return{type:"SET_UPLOADS",value:e}}function dt(e){return{type:"ADD_UPLOADS",value:e}}function mt(e){return{type:"REMOVE_UPLOAD",value:e}}function vt(e){return{type:"SET_VIDEOS_FETCHED",value:e}}function pt(e){return{type:"SET_BUCKETS_FETCHED",value:e}}function ft(e){return{type:"SET_LOADING",value:e}}function yt(e,t){return{type:"SET_UI_ITEM",item:e,value:t}}function ht(e){return{type:"ADD_ERROR",value:e}}function _t(e){return{type:"REMOVE_ERROR",value:e}}window.mcv_store_registed||((0,Fe.register)((0,Fe.createReduxStore)("mine-cloudvod/vod",{reducer:Ze,selectors:e,actions:t})),window.mcv_store_registed=!0),c()((function(){if(document.getElementById("mcv-addons-list")&&(r.createRoot?(0,r.createRoot)(document.getElementById("mcv-addons-list")).render((0,r.createElement)(Be,null)):(0,r.render)((0,r.createElement)(Be,null),document.getElementById("mcv-addons-list"))),document.getElementById("mcv-report-wrap")&&(r.createRoot?(0,r.createRoot)(document.getElementById("mcv-report-wrap")).render((0,r.createElement)(Ke,null)):(0,r.render)((0,r.createElement)(Ke,null),document.getElementById("mcv-report-wrap"))),!document.getElementById("mcv-plugin-app")){let e=document.createElement("div");e.setAttribute("id","mcv-plugin-app"),document.querySelector("body").append(e)}r.createRoot?(0,r.createRoot)(document.getElementById("mcv-plugin-app")).render((0,r.createElement)(Qe,null)):(0,r.render)((0,r.createElement)(Qe,null),document.getElementById("mcv-plugin-app"))}))}()}();