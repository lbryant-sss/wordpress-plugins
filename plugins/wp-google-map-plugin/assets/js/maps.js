/* @preserve
 * Leaflet 1.3.1, a JS library for interactive maps. http://leafletjs.com
 * (c) 2010-2017 Vladimir Agafonkin, (c) 2010-2011 CloudMade
 */
!function(t,i){"object"==typeof exports&&"undefined"!=typeof module?i(exports):"function"==typeof define&&define.amd?define(["exports"],i):i(t.L={})}(this,function(t){"use strict";function i(t){var i,e,n,o;for(e=1,n=arguments.length;e<n;e++){o=arguments[e];for(i in o)t[i]=o[i]}return t}function e(t,i){var e=Array.prototype.slice;if(t.bind)return t.bind.apply(t,e.call(arguments,1));var n=e.call(arguments,2);return function(){return t.apply(i,n.length?n.concat(e.call(arguments)):arguments)}}function n(t){return t._leaflet_id=t._leaflet_id||++ti,t._leaflet_id}function o(t,i,e){var n,o,s,r;return r=function(){n=!1,o&&(s.apply(e,o),o=!1)},s=function(){n?o=arguments:(t.apply(e,arguments),setTimeout(r,i),n=!0)}}function s(t,i,e){var n=i[1],o=i[0],s=n-o;return t===n&&e?t:((t-o)%s+s)%s+o}function r(){return!1}function a(t,i){var e=Math.pow(10,void 0===i?6:i);return Math.round(t*e)/e}function h(t){return t.trim?t.trim():t.replace(/^\s+|\s+$/g,"")}function u(t){return h(t).split(/\s+/)}function l(t,i){t.hasOwnProperty("options")||(t.options=t.options?Qt(t.options):{});for(var e in i)t.options[e]=i[e];return t.options}function c(t,i,e){var n=[];for(var o in t)n.push(encodeURIComponent(e?o.toUpperCase():o)+"="+encodeURIComponent(t[o]));return(i&&-1!==i.indexOf("?")?"&":"?")+n.join("&")}function _(t,i){return t.replace(ii,function(t,e){var n=i[e];if(void 0===n)throw new Error("No value provided for variable "+t);return"function"==typeof n&&(n=n(i)),n})}function d(t,i){for(var e=0;e<t.length;e++)if(t[e]===i)return e;return-1}function p(t){return window["webkit"+t]||window["moz"+t]||window["ms"+t]}function m(t){var i=+new Date,e=Math.max(0,16-(i-oi));return oi=i+e,window.setTimeout(t,e)}function f(t,i,n){if(!n||si!==m)return si.call(window,e(t,i));t.call(i)}function g(t){t&&ri.call(window,t)}function v(){}function y(t){if("undefined"!=typeof L&&L&&L.Mixin){t=ei(t)?t:[t];for(var i=0;i<t.length;i++)t[i]===L.Mixin.Events&&console.warn("Deprecated include of L.Mixin.Events: this property will be removed in future releases, please inherit from L.Evented instead.",(new Error).stack)}}function x(t,i,e){this.x=e?Math.round(t):t,this.y=e?Math.round(i):i}function w(t,i,e){return t instanceof x?t:ei(t)?new x(t[0],t[1]):void 0===t||null===t?t:"object"==typeof t&&"x"in t&&"y"in t?new x(t.x,t.y):new x(t,i,e)}function P(t,i){if(t)for(var e=i?[t,i]:t,n=0,o=e.length;n<o;n++)this.extend(e[n])}function b(t,i){return!t||t instanceof P?t:new P(t,i)}function T(t,i){if(t)for(var e=i?[t,i]:t,n=0,o=e.length;n<o;n++)this.extend(e[n])}function z(t,i){return t instanceof T?t:new T(t,i)}function M(t,i,e){if(isNaN(t)||isNaN(i))throw new Error("Invalid LatLng object: ("+t+", "+i+")");this.lat=+t,this.lng=+i,void 0!==e&&(this.alt=+e)}function C(t,i,e){return t instanceof M?t:ei(t)&&"object"!=typeof t[0]?3===t.length?new M(t[0],t[1],t[2]):2===t.length?new M(t[0],t[1]):null:void 0===t||null===t?t:"object"==typeof t&&"lat"in t?new M(t.lat,"lng"in t?t.lng:t.lon,t.alt):void 0===i?null:new M(t,i,e)}function Z(t,i,e,n){if(ei(t))return this._a=t[0],this._b=t[1],this._c=t[2],void(this._d=t[3]);this._a=t,this._b=i,this._c=e,this._d=n}function S(t,i,e,n){return new Z(t,i,e,n)}function E(t){return document.createElementNS("http://www.w3.org/2000/svg",t)}function k(t,i){var e,n,o,s,r,a,h="";for(e=0,o=t.length;e<o;e++){for(n=0,s=(r=t[e]).length;n<s;n++)a=r[n],h+=(n?"L":"M")+a.x+" "+a.y;h+=i?Xi?"z":"x":""}return h||"M0 0"}function I(t){return navigator.userAgent.toLowerCase().indexOf(t)>=0}function A(t,i,e,n){return"touchstart"===i?O(t,e,n):"touchmove"===i?W(t,e,n):"touchend"===i&&H(t,e,n),this}function B(t,i,e){var n=t["_leaflet_"+i+e];return"touchstart"===i?t.removeEventListener(Qi,n,!1):"touchmove"===i?t.removeEventListener(te,n,!1):"touchend"===i&&(t.removeEventListener(ie,n,!1),t.removeEventListener(ee,n,!1)),this}function O(t,i,n){var o=e(function(t){if("mouse"!==t.pointerType&&t.MSPOINTER_TYPE_MOUSE&&t.pointerType!==t.MSPOINTER_TYPE_MOUSE){if(!(ne.indexOf(t.target.tagName)<0))return;$(t)}j(t,i)});t["_leaflet_touchstart"+n]=o,t.addEventListener(Qi,o,!1),se||(document.documentElement.addEventListener(Qi,R,!0),document.documentElement.addEventListener(te,D,!0),document.documentElement.addEventListener(ie,N,!0),document.documentElement.addEventListener(ee,N,!0),se=!0)}function R(t){oe[t.pointerId]=t,re++}function D(t){oe[t.pointerId]&&(oe[t.pointerId]=t)}function N(t){delete oe[t.pointerId],re--}function j(t,i){t.touches=[];for(var e in oe)t.touches.push(oe[e]);t.changedTouches=[t],i(t)}function W(t,i,e){var n=function(t){(t.pointerType!==t.MSPOINTER_TYPE_MOUSE&&"mouse"!==t.pointerType||0!==t.buttons)&&j(t,i)};t["_leaflet_touchmove"+e]=n,t.addEventListener(te,n,!1)}function H(t,i,e){var n=function(t){j(t,i)};t["_leaflet_touchend"+e]=n,t.addEventListener(ie,n,!1),t.addEventListener(ee,n,!1)}function F(t,i,e){function n(t){var i;if(Ui){if(!Pi||"mouse"===t.pointerType)return;i=re}else i=t.touches.length;if(!(i>1)){var e=Date.now(),n=e-(s||e);r=t.touches?t.touches[0]:t,a=n>0&&n<=h,s=e}}function o(t){if(a&&!r.cancelBubble){if(Ui){if(!Pi||"mouse"===t.pointerType)return;var e,n,o={};for(n in r)e=r[n],o[n]=e&&e.bind?e.bind(r):e;r=o}r.type="dblclick",i(r),s=null}}var s,r,a=!1,h=250;return t[ue+ae+e]=n,t[ue+he+e]=o,t[ue+"dblclick"+e]=i,t.addEventListener(ae,n,!1),t.addEventListener(he,o,!1),t.addEventListener("dblclick",i,!1),this}function U(t,i){var e=t[ue+ae+i],n=t[ue+he+i],o=t[ue+"dblclick"+i];return t.removeEventListener(ae,e,!1),t.removeEventListener(he,n,!1),Pi||t.removeEventListener("dblclick",o,!1),this}function V(t,i,e,n){if("object"==typeof i)for(var o in i)G(t,o,i[o],e);else for(var s=0,r=(i=u(i)).length;s<r;s++)G(t,i[s],e,n);return this}function q(t,i,e,n){if("object"==typeof i)for(var o in i)K(t,o,i[o],e);else if(i)for(var s=0,r=(i=u(i)).length;s<r;s++)K(t,i[s],e,n);else{for(var a in t[le])K(t,a,t[le][a]);delete t[le]}return this}function G(t,i,e,o){var s=i+n(e)+(o?"_"+n(o):"");if(t[le]&&t[le][s])return this;var r=function(i){return e.call(o||t,i||window.event)},a=r;Ui&&0===i.indexOf("touch")?A(t,i,r,s):!Vi||"dblclick"!==i||!F||Ui&&Si?"addEventListener"in t?"mousewheel"===i?t.addEventListener("onwheel"in t?"wheel":"mousewheel",r,!1):"mouseenter"===i||"mouseleave"===i?(r=function(i){i=i||window.event,ot(t,i)&&a(i)},t.addEventListener("mouseenter"===i?"mouseover":"mouseout",r,!1)):("click"===i&&Ti&&(r=function(t){st(t,a)}),t.addEventListener(i,r,!1)):"attachEvent"in t&&t.attachEvent("on"+i,r):F(t,r,s),t[le]=t[le]||{},t[le][s]=r}function K(t,i,e,o){var s=i+n(e)+(o?"_"+n(o):""),r=t[le]&&t[le][s];if(!r)return this;Ui&&0===i.indexOf("touch")?B(t,i,s):!Vi||"dblclick"!==i||!U||Ui&&Si?"removeEventListener"in t?"mousewheel"===i?t.removeEventListener("onwheel"in t?"wheel":"mousewheel",r,!1):t.removeEventListener("mouseenter"===i?"mouseover":"mouseleave"===i?"mouseout":i,r,!1):"detachEvent"in t&&t.detachEvent("on"+i,r):U(t,s),t[le][s]=null}function Y(t){return t.stopPropagation?t.stopPropagation():t.originalEvent?t.originalEvent._stopped=!0:t.cancelBubble=!0,nt(t),this}function X(t){return G(t,"mousewheel",Y),this}function J(t){return V(t,"mousedown touchstart dblclick",Y),G(t,"click",et),this}function $(t){return t.preventDefault?t.preventDefault():t.returnValue=!1,this}function Q(t){return $(t),Y(t),this}function tt(t,i){if(!i)return new x(t.clientX,t.clientY);var e=i.getBoundingClientRect(),n=e.width/i.offsetWidth||1,o=e.height/i.offsetHeight||1;return new x(t.clientX/n-e.left-i.clientLeft,t.clientY/o-e.top-i.clientTop)}function it(t){return Pi?t.wheelDeltaY/2:t.deltaY&&0===t.deltaMode?-t.deltaY/ce:t.deltaY&&1===t.deltaMode?20*-t.deltaY:t.deltaY&&2===t.deltaMode?60*-t.deltaY:t.deltaX||t.deltaZ?0:t.wheelDelta?(t.wheelDeltaY||t.wheelDelta)/2:t.detail&&Math.abs(t.detail)<32765?20*-t.detail:t.detail?t.detail/-32765*60:0}function et(t){_e[t.type]=!0}function nt(t){var i=_e[t.type];return _e[t.type]=!1,i}function ot(t,i){var e=i.relatedTarget;if(!e)return!0;try{for(;e&&e!==t;)e=e.parentNode}catch(t){return!1}return e!==t}function st(t,i){var e=t.timeStamp||t.originalEvent&&t.originalEvent.timeStamp,n=pi&&e-pi;n&&n>100&&n<500||t.target._simulatedClick&&!t._simulated?Q(t):(pi=e,i(t))}function rt(t){return"string"==typeof t?document.getElementById(t):t}function at(t,i){var e=t.style[i]||t.currentStyle&&t.currentStyle[i];if((!e||"auto"===e)&&document.defaultView){var n=document.defaultView.getComputedStyle(t,null);e=n?n[i]:null}return"auto"===e?null:e}function ht(t,i,e){var n=document.createElement(t);return n.className=i||"",e&&e.appendChild(n),n}function ut(t){var i=t.parentNode;i&&i.removeChild(t)}function lt(t){for(;t.firstChild;)t.removeChild(t.firstChild)}function ct(t){var i=t.parentNode;i.lastChild!==t&&i.appendChild(t)}function _t(t){var i=t.parentNode;i.firstChild!==t&&i.insertBefore(t,i.firstChild)}function dt(t,i){if(void 0!==t.classList)return t.classList.contains(i);var e=gt(t);return e.length>0&&new RegExp("(^|\\s)"+i+"(\\s|$)").test(e)}function pt(t,i){if(void 0!==t.classList)for(var e=u(i),n=0,o=e.length;n<o;n++)t.classList.add(e[n]);else if(!dt(t,i)){var s=gt(t);ft(t,(s?s+" ":"")+i)}}function mt(t,i){void 0!==t.classList?t.classList.remove(i):ft(t,h((" "+gt(t)+" ").replace(" "+i+" "," ")))}function ft(t,i){void 0===t.className.baseVal?t.className=i:t.className.baseVal=i}function gt(t){return void 0===t.className.baseVal?t.className:t.className.baseVal}function vt(t,i){"opacity"in t.style?t.style.opacity=i:"filter"in t.style&&yt(t,i)}function yt(t,i){var e=!1,n="DXImageTransform.Microsoft.Alpha";try{e=t.filters.item(n)}catch(t){if(1===i)return}i=Math.round(100*i),e?(e.Enabled=100!==i,e.Opacity=i):t.style.filter+=" progid:"+n+"(opacity="+i+")"}function xt(t){for(var i=document.documentElement.style,e=0;e<t.length;e++)if(t[e]in i)return t[e];return!1}function wt(t,i,e){var n=i||new x(0,0);t.style[pe]=(Oi?"translate("+n.x+"px,"+n.y+"px)":"translate3d("+n.x+"px,"+n.y+"px,0)")+(e?" scale("+e+")":"")}function Lt(t,i){t._leaflet_pos=i,Ni?wt(t,i):(t.style.left=i.x+"px",t.style.top=i.y+"px")}function Pt(t){return t._leaflet_pos||new x(0,0)}function bt(){V(window,"dragstart",$)}function Tt(){q(window,"dragstart",$)}function zt(t){for(;-1===t.tabIndex;)t=t.parentNode;t.style&&(Mt(),ve=t,ye=t.style.outline,t.style.outline="none",V(window,"keydown",Mt))}function Mt(){ve&&(ve.style.outline=ye,ve=void 0,ye=void 0,q(window,"keydown",Mt))}function Ct(t,i){if(!i||!t.length)return t.slice();var e=i*i;return t=kt(t,e),t=St(t,e)}function Zt(t,i,e){return Math.sqrt(Rt(t,i,e,!0))}function St(t,i){var e=t.length,n=new(typeof Uint8Array!=void 0+""?Uint8Array:Array)(e);n[0]=n[e-1]=1,Et(t,n,i,0,e-1);var o,s=[];for(o=0;o<e;o++)n[o]&&s.push(t[o]);return s}function Et(t,i,e,n,o){var s,r,a,h=0;for(r=n+1;r<=o-1;r++)(a=Rt(t[r],t[n],t[o],!0))>h&&(s=r,h=a);h>e&&(i[s]=1,Et(t,i,e,n,s),Et(t,i,e,s,o))}function kt(t,i){for(var e=[t[0]],n=1,o=0,s=t.length;n<s;n++)Ot(t[n],t[o])>i&&(e.push(t[n]),o=n);return o<s-1&&e.push(t[s-1]),e}function It(t,i,e,n,o){var s,r,a,h=n?Se:Bt(t,e),u=Bt(i,e);for(Se=u;;){if(!(h|u))return[t,i];if(h&u)return!1;a=Bt(r=At(t,i,s=h||u,e,o),e),s===h?(t=r,h=a):(i=r,u=a)}}function At(t,i,e,n,o){var s,r,a=i.x-t.x,h=i.y-t.y,u=n.min,l=n.max;return 8&e?(s=t.x+a*(l.y-t.y)/h,r=l.y):4&e?(s=t.x+a*(u.y-t.y)/h,r=u.y):2&e?(s=l.x,r=t.y+h*(l.x-t.x)/a):1&e&&(s=u.x,r=t.y+h*(u.x-t.x)/a),new x(s,r,o)}function Bt(t,i){var e=0;return t.x<i.min.x?e|=1:t.x>i.max.x&&(e|=2),t.y<i.min.y?e|=4:t.y>i.max.y&&(e|=8),e}function Ot(t,i){var e=i.x-t.x,n=i.y-t.y;return e*e+n*n}function Rt(t,i,e,n){var o,s=i.x,r=i.y,a=e.x-s,h=e.y-r,u=a*a+h*h;return u>0&&((o=((t.x-s)*a+(t.y-r)*h)/u)>1?(s=e.x,r=e.y):o>0&&(s+=a*o,r+=h*o)),a=t.x-s,h=t.y-r,n?a*a+h*h:new x(s,r)}function Dt(t){return!ei(t[0])||"object"!=typeof t[0][0]&&void 0!==t[0][0]}function Nt(t){return console.warn("Deprecated use of _flat, please use L.LineUtil.isFlat instead."),Dt(t)}function jt(t,i,e){var n,o,s,r,a,h,u,l,c,_=[1,4,2,8];for(o=0,u=t.length;o<u;o++)t[o]._code=Bt(t[o],i);for(r=0;r<4;r++){for(l=_[r],n=[],o=0,s=(u=t.length)-1;o<u;s=o++)a=t[o],h=t[s],a._code&l?h._code&l||((c=At(h,a,l,i,e))._code=Bt(c,i),n.push(c)):(h._code&l&&((c=At(h,a,l,i,e))._code=Bt(c,i),n.push(c)),n.push(a));t=n}return t}function Wt(t,i){var e,n,o,s,r="Feature"===t.type?t.geometry:t,a=r?r.coordinates:null,h=[],u=i&&i.pointToLayer,l=i&&i.coordsToLatLng||Ht;if(!a&&!r)return null;switch(r.type){case"Point":return e=l(a),u?u(t,e):new Xe(e);case"MultiPoint":for(o=0,s=a.length;o<s;o++)e=l(a[o]),h.push(u?u(t,e):new Xe(e));return new qe(h);case"LineString":case"MultiLineString":return n=Ft(a,"LineString"===r.type?0:1,l),new tn(n,i);case"Polygon":case"MultiPolygon":return n=Ft(a,"Polygon"===r.type?1:2,l),new en(n,i);case"GeometryCollection":for(o=0,s=r.geometries.length;o<s;o++){var c=Wt({geometry:r.geometries[o],type:"Feature",properties:t.properties},i);c&&h.push(c)}return new qe(h);default:throw new Error("Invalid GeoJSON object.")}}function Ht(t){return new M(t[1],t[0],t[2])}function Ft(t,i,e){for(var n,o=[],s=0,r=t.length;s<r;s++)n=i?Ft(t[s],i-1,e):(e||Ht)(t[s]),o.push(n);return o}function Ut(t,i){return i="number"==typeof i?i:6,void 0!==t.alt?[a(t.lng,i),a(t.lat,i),a(t.alt,i)]:[a(t.lng,i),a(t.lat,i)]}function Vt(t,i,e,n){for(var o=[],s=0,r=t.length;s<r;s++)o.push(i?Vt(t[s],i-1,e,n):Ut(t[s],n));return!i&&e&&o.push(o[0]),o}function qt(t,e){return t.feature?i({},t.feature,{geometry:e}):Gt(e)}function Gt(t){return"Feature"===t.type||"FeatureCollection"===t.type?t:{type:"Feature",properties:{},geometry:t}}function Kt(t,i){return new nn(t,i)}function Yt(t,i){return new dn(t,i)}function Xt(t){return Yi?new fn(t):null}function Jt(t){return Xi||Ji?new xn(t):null}var $t=Object.freeze;Object.freeze=function(t){return t};var Qt=Object.create||function(){function t(){}return function(i){return t.prototype=i,new t}}(),ti=0,ii=/\{ *([\w_-]+) *\}/g,ei=Array.isArray||function(t){return"[object Array]"===Object.prototype.toString.call(t)},ni="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=",oi=0,si=window.requestAnimationFrame||p("RequestAnimationFrame")||m,ri=window.cancelAnimationFrame||p("CancelAnimationFrame")||p("CancelRequestAnimationFrame")||function(t){window.clearTimeout(t)},ai=(Object.freeze||Object)({freeze:$t,extend:i,create:Qt,bind:e,lastId:ti,stamp:n,throttle:o,wrapNum:s,falseFn:r,formatNum:a,trim:h,splitWords:u,setOptions:l,getParamString:c,template:_,isArray:ei,indexOf:d,emptyImageUrl:ni,requestFn:si,cancelFn:ri,requestAnimFrame:f,cancelAnimFrame:g});v.extend=function(t){var e=function(){this.initialize&&this.initialize.apply(this,arguments),this.callInitHooks()},n=e.__super__=this.prototype,o=Qt(n);o.constructor=e,e.prototype=o;for(var s in this)this.hasOwnProperty(s)&&"prototype"!==s&&"__super__"!==s&&(e[s]=this[s]);return t.statics&&(i(e,t.statics),delete t.statics),t.includes&&(y(t.includes),i.apply(null,[o].concat(t.includes)),delete t.includes),o.options&&(t.options=i(Qt(o.options),t.options)),i(o,t),o._initHooks=[],o.callInitHooks=function(){if(!this._initHooksCalled){n.callInitHooks&&n.callInitHooks.call(this),this._initHooksCalled=!0;for(var t=0,i=o._initHooks.length;t<i;t++)o._initHooks[t].call(this)}},e},v.include=function(t){return i(this.prototype,t),this},v.mergeOptions=function(t){return i(this.prototype.options,t),this},v.addInitHook=function(t){var i=Array.prototype.slice.call(arguments,1),e="function"==typeof t?t:function(){this[t].apply(this,i)};return this.prototype._initHooks=this.prototype._initHooks||[],this.prototype._initHooks.push(e),this};var hi={on:function(t,i,e){if("object"==typeof t)for(var n in t)this._on(n,t[n],i);else for(var o=0,s=(t=u(t)).length;o<s;o++)this._on(t[o],i,e);return this},off:function(t,i,e){if(t)if("object"==typeof t)for(var n in t)this._off(n,t[n],i);else for(var o=0,s=(t=u(t)).length;o<s;o++)this._off(t[o],i,e);else delete this._events;return this},_on:function(t,i,e){this._events=this._events||{};var n=this._events[t];n||(n=[],this._events[t]=n),e===this&&(e=void 0);for(var o={fn:i,ctx:e},s=n,r=0,a=s.length;r<a;r++)if(s[r].fn===i&&s[r].ctx===e)return;s.push(o)},_off:function(t,i,e){var n,o,s;if(this._events&&(n=this._events[t]))if(i){if(e===this&&(e=void 0),n)for(o=0,s=n.length;o<s;o++){var a=n[o];if(a.ctx===e&&a.fn===i)return a.fn=r,this._firingCount&&(this._events[t]=n=n.slice()),void n.splice(o,1)}}else{for(o=0,s=n.length;o<s;o++)n[o].fn=r;delete this._events[t]}},fire:function(t,e,n){if(!this.listens(t,n))return this;var o=i({},e,{type:t,target:this,sourceTarget:e&&e.sourceTarget||this});if(this._events){var s=this._events[t];if(s){this._firingCount=this._firingCount+1||1;for(var r=0,a=s.length;r<a;r++){var h=s[r];h.fn.call(h.ctx||this,o)}this._firingCount--}}return n&&this._propagateEvent(o),this},listens:function(t,i){var e=this._events&&this._events[t];if(e&&e.length)return!0;if(i)for(var n in this._eventParents)if(this._eventParents[n].listens(t,i))return!0;return!1},once:function(t,i,n){if("object"==typeof t){for(var o in t)this.once(o,t[o],i);return this}var s=e(function(){this.off(t,i,n).off(t,s,n)},this);return this.on(t,i,n).on(t,s,n)},addEventParent:function(t){return this._eventParents=this._eventParents||{},this._eventParents[n(t)]=t,this},removeEventParent:function(t){return this._eventParents&&delete this._eventParents[n(t)],this},_propagateEvent:function(t){for(var e in this._eventParents)this._eventParents[e].fire(t.type,i({layer:t.target,propagatedFrom:t.target},t),!0)}};hi.addEventListener=hi.on,hi.removeEventListener=hi.clearAllEventListeners=hi.off,hi.addOneTimeEventListener=hi.once,hi.fireEvent=hi.fire,hi.hasEventListeners=hi.listens;var ui=v.extend(hi),li=Math.trunc||function(t){return t>0?Math.floor(t):Math.ceil(t)};x.prototype={clone:function(){return new x(this.x,this.y)},add:function(t){return this.clone()._add(w(t))},_add:function(t){return this.x+=t.x,this.y+=t.y,this},subtract:function(t){return this.clone()._subtract(w(t))},_subtract:function(t){return this.x-=t.x,this.y-=t.y,this},divideBy:function(t){return this.clone()._divideBy(t)},_divideBy:function(t){return this.x/=t,this.y/=t,this},multiplyBy:function(t){return this.clone()._multiplyBy(t)},_multiplyBy:function(t){return this.x*=t,this.y*=t,this},scaleBy:function(t){return new x(this.x*t.x,this.y*t.y)},unscaleBy:function(t){return new x(this.x/t.x,this.y/t.y)},round:function(){return this.clone()._round()},_round:function(){return this.x=Math.round(this.x),this.y=Math.round(this.y),this},floor:function(){return this.clone()._floor()},_floor:function(){return this.x=Math.floor(this.x),this.y=Math.floor(this.y),this},ceil:function(){return this.clone()._ceil()},_ceil:function(){return this.x=Math.ceil(this.x),this.y=Math.ceil(this.y),this},trunc:function(){return this.clone()._trunc()},_trunc:function(){return this.x=li(this.x),this.y=li(this.y),this},distanceTo:function(t){var i=(t=w(t)).x-this.x,e=t.y-this.y;return Math.sqrt(i*i+e*e)},equals:function(t){return(t=w(t)).x===this.x&&t.y===this.y},contains:function(t){return t=w(t),Math.abs(t.x)<=Math.abs(this.x)&&Math.abs(t.y)<=Math.abs(this.y)},toString:function(){return"Point("+a(this.x)+", "+a(this.y)+")"}},P.prototype={extend:function(t){return t=w(t),this.min||this.max?(this.min.x=Math.min(t.x,this.min.x),this.max.x=Math.max(t.x,this.max.x),this.min.y=Math.min(t.y,this.min.y),this.max.y=Math.max(t.y,this.max.y)):(this.min=t.clone(),this.max=t.clone()),this},getCenter:function(t){return new x((this.min.x+this.max.x)/2,(this.min.y+this.max.y)/2,t)},getBottomLeft:function(){return new x(this.min.x,this.max.y)},getTopRight:function(){return new x(this.max.x,this.min.y)},getTopLeft:function(){return this.min},getBottomRight:function(){return this.max},getSize:function(){return this.max.subtract(this.min)},contains:function(t){var i,e;return(t="number"==typeof t[0]||t instanceof x?w(t):b(t))instanceof P?(i=t.min,e=t.max):i=e=t,i.x>=this.min.x&&e.x<=this.max.x&&i.y>=this.min.y&&e.y<=this.max.y},intersects:function(t){t=b(t);var i=this.min,e=this.max,n=t.min,o=t.max,s=o.x>=i.x&&n.x<=e.x,r=o.y>=i.y&&n.y<=e.y;return s&&r},overlaps:function(t){t=b(t);var i=this.min,e=this.max,n=t.min,o=t.max,s=o.x>i.x&&n.x<e.x,r=o.y>i.y&&n.y<e.y;return s&&r},isValid:function(){return!(!this.min||!this.max)}},T.prototype={extend:function(t){var i,e,n=this._southWest,o=this._northEast;if(t instanceof M)i=t,e=t;else{if(!(t instanceof T))return t?this.extend(C(t)||z(t)):this;if(i=t._southWest,e=t._northEast,!i||!e)return this}return n||o?(n.lat=Math.min(i.lat,n.lat),n.lng=Math.min(i.lng,n.lng),o.lat=Math.max(e.lat,o.lat),o.lng=Math.max(e.lng,o.lng)):(this._southWest=new M(i.lat,i.lng),this._northEast=new M(e.lat,e.lng)),this},pad:function(t){var i=this._southWest,e=this._northEast,n=Math.abs(i.lat-e.lat)*t,o=Math.abs(i.lng-e.lng)*t;return new T(new M(i.lat-n,i.lng-o),new M(e.lat+n,e.lng+o))},getCenter:function(){return new M((this._southWest.lat+this._northEast.lat)/2,(this._southWest.lng+this._northEast.lng)/2)},getSouthWest:function(){return this._southWest},getNorthEast:function(){return this._northEast},getNorthWest:function(){return new M(this.getNorth(),this.getWest())},getSouthEast:function(){return new M(this.getSouth(),this.getEast())},getWest:function(){return this._southWest.lng},getSouth:function(){return this._southWest.lat},getEast:function(){return this._northEast.lng},getNorth:function(){return this._northEast.lat},contains:function(t){t="number"==typeof t[0]||t instanceof M||"lat"in t?C(t):z(t);var i,e,n=this._southWest,o=this._northEast;return t instanceof T?(i=t.getSouthWest(),e=t.getNorthEast()):i=e=t,i.lat>=n.lat&&e.lat<=o.lat&&i.lng>=n.lng&&e.lng<=o.lng},intersects:function(t){t=z(t);var i=this._southWest,e=this._northEast,n=t.getSouthWest(),o=t.getNorthEast(),s=o.lat>=i.lat&&n.lat<=e.lat,r=o.lng>=i.lng&&n.lng<=e.lng;return s&&r},overlaps:function(t){t=z(t);var i=this._southWest,e=this._northEast,n=t.getSouthWest(),o=t.getNorthEast(),s=o.lat>i.lat&&n.lat<e.lat,r=o.lng>i.lng&&n.lng<e.lng;return s&&r},toBBoxString:function(){return[this.getWest(),this.getSouth(),this.getEast(),this.getNorth()].join(",")},equals:function(t,i){return!!t&&(t=z(t),this._southWest.equals(t.getSouthWest(),i)&&this._northEast.equals(t.getNorthEast(),i))},isValid:function(){return!(!this._southWest||!this._northEast)}},M.prototype={equals:function(t,i){return!!t&&(t=C(t),Math.max(Math.abs(this.lat-t.lat),Math.abs(this.lng-t.lng))<=(void 0===i?1e-9:i))},toString:function(t){return"LatLng("+a(this.lat,t)+", "+a(this.lng,t)+")"},distanceTo:function(t){return _i.distance(this,C(t))},wrap:function(){return _i.wrapLatLng(this)},toBounds:function(t){var i=180*t/40075017,e=i/Math.cos(Math.PI/180*this.lat);return z([this.lat-i,this.lng-e],[this.lat+i,this.lng+e])},clone:function(){return new M(this.lat,this.lng,this.alt)}};var ci={latLngToPoint:function(t,i){var e=this.projection.project(t),n=this.scale(i);return this.transformation._transform(e,n)},pointToLatLng:function(t,i){var e=this.scale(i),n=this.transformation.untransform(t,e);return this.projection.unproject(n)},project:function(t){return this.projection.project(t)},unproject:function(t){return this.projection.unproject(t)},scale:function(t){return 256*Math.pow(2,t)},zoom:function(t){return Math.log(t/256)/Math.LN2},getProjectedBounds:function(t){if(this.infinite)return null;var i=this.projection.bounds,e=this.scale(t);return new P(this.transformation.transform(i.min,e),this.transformation.transform(i.max,e))},infinite:!1,wrapLatLng:function(t){var i=this.wrapLng?s(t.lng,this.wrapLng,!0):t.lng;return new M(this.wrapLat?s(t.lat,this.wrapLat,!0):t.lat,i,t.alt)},wrapLatLngBounds:function(t){var i=t.getCenter(),e=this.wrapLatLng(i),n=i.lat-e.lat,o=i.lng-e.lng;if(0===n&&0===o)return t;var s=t.getSouthWest(),r=t.getNorthEast();return new T(new M(s.lat-n,s.lng-o),new M(r.lat-n,r.lng-o))}},_i=i({},ci,{wrapLng:[-180,180],R:6371e3,distance:function(t,i){var e=Math.PI/180,n=t.lat*e,o=i.lat*e,s=Math.sin((i.lat-t.lat)*e/2),r=Math.sin((i.lng-t.lng)*e/2),a=s*s+Math.cos(n)*Math.cos(o)*r*r,h=2*Math.atan2(Math.sqrt(a),Math.sqrt(1-a));return this.R*h}}),di={R:6378137,MAX_LATITUDE:85.0511287798,project:function(t){var i=Math.PI/180,e=this.MAX_LATITUDE,n=Math.max(Math.min(e,t.lat),-e),o=Math.sin(n*i);return new x(this.R*t.lng*i,this.R*Math.log((1+o)/(1-o))/2)},unproject:function(t){var i=180/Math.PI;return new M((2*Math.atan(Math.exp(t.y/this.R))-Math.PI/2)*i,t.x*i/this.R)},bounds:function(){var t=6378137*Math.PI;return new P([-t,-t],[t,t])}()};Z.prototype={transform:function(t,i){return this._transform(t.clone(),i)},_transform:function(t,i){return i=i||1,t.x=i*(this._a*t.x+this._b),t.y=i*(this._c*t.y+this._d),t},untransform:function(t,i){return i=i||1,new x((t.x/i-this._b)/this._a,(t.y/i-this._d)/this._c)}};var pi,mi,fi,gi,vi=i({},_i,{code:"EPSG:3857",projection:di,transformation:function(){var t=.5/(Math.PI*di.R);return S(t,.5,-t,.5)}()}),yi=i({},vi,{code:"EPSG:900913"}),xi=document.documentElement.style,wi="ActiveXObject"in window,Li=wi&&!document.addEventListener,Pi="msLaunchUri"in navigator&&!("documentMode"in document),bi=I("webkit"),Ti=I("android"),zi=I("android 2")||I("android 3"),Mi=parseInt(/WebKit\/([0-9]+)|$/.exec(navigator.userAgent)[1],10),Ci=Ti&&I("Google")&&Mi<537&&!("AudioNode"in window),Zi=!!window.opera,Si=I("chrome"),Ei=I("gecko")&&!bi&&!Zi&&!wi,ki=!Si&&I("safari"),Ii=I("phantom"),Ai="OTransition"in xi,Bi=0===navigator.platform.indexOf("Win"),Oi=wi&&"transition"in xi,Ri="WebKitCSSMatrix"in window&&"m11"in new window.WebKitCSSMatrix&&!zi,Di="MozPerspective"in xi,Ni=!window.L_DISABLE_3D&&(Oi||Ri||Di)&&!Ai&&!Ii,ji="undefined"!=typeof orientation||I("mobile"),Wi=ji&&bi,Hi=ji&&Ri,Fi=!window.PointerEvent&&window.MSPointerEvent,Ui=!(!window.PointerEvent&&!Fi),Vi=!window.L_NO_TOUCH&&(Ui||"ontouchstart"in window||window.DocumentTouch&&document instanceof window.DocumentTouch),qi=ji&&Zi,Gi=ji&&Ei,Ki=(window.devicePixelRatio||window.screen.deviceXDPI/window.screen.logicalXDPI)>1,Yi=!!document.createElement("canvas").getContext,Xi=!(!document.createElementNS||!E("svg").createSVGRect),Ji=!Xi&&function(){try{var t=document.createElement("div");t.innerHTML='<v:shape adj="1"/>';var i=t.firstChild;return i.style.behavior="url(#default#VML)",i&&"object"==typeof i.adj}catch(t){return!1}}(),$i=(Object.freeze||Object)({ie:wi,ielt9:Li,edge:Pi,webkit:bi,android:Ti,android23:zi,androidStock:Ci,opera:Zi,chrome:Si,gecko:Ei,safari:ki,phantom:Ii,opera12:Ai,win:Bi,ie3d:Oi,webkit3d:Ri,gecko3d:Di,any3d:Ni,mobile:ji,mobileWebkit:Wi,mobileWebkit3d:Hi,msPointer:Fi,pointer:Ui,touch:Vi,mobileOpera:qi,mobileGecko:Gi,retina:Ki,canvas:Yi,svg:Xi,vml:Ji}),Qi=Fi?"MSPointerDown":"pointerdown",te=Fi?"MSPointerMove":"pointermove",ie=Fi?"MSPointerUp":"pointerup",ee=Fi?"MSPointerCancel":"pointercancel",ne=["INPUT","SELECT","OPTION"],oe={},se=!1,re=0,ae=Fi?"MSPointerDown":Ui?"pointerdown":"touchstart",he=Fi?"MSPointerUp":Ui?"pointerup":"touchend",ue="_leaflet_",le="_leaflet_events",ce=Bi&&Si?2*window.devicePixelRatio:Ei?window.devicePixelRatio:1,_e={},de=(Object.freeze||Object)({on:V,off:q,stopPropagation:Y,disableScrollPropagation:X,disableClickPropagation:J,preventDefault:$,stop:Q,getMousePosition:tt,getWheelDelta:it,fakeStop:et,skipped:nt,isExternalTarget:ot,addListener:V,removeListener:q}),pe=xt(["transform","WebkitTransform","OTransform","MozTransform","msTransform"]),me=xt(["webkitTransition","transition","OTransition","MozTransition","msTransition"]),fe="webkitTransition"===me||"OTransition"===me?me+"End":"transitionend";if("onselectstart"in document)mi=function(){V(window,"selectstart",$)},fi=function(){q(window,"selectstart",$)};else{var ge=xt(["userSelect","WebkitUserSelect","OUserSelect","MozUserSelect","msUserSelect"]);mi=function(){if(ge){var t=document.documentElement.style;gi=t[ge],t[ge]="none"}},fi=function(){ge&&(document.documentElement.style[ge]=gi,gi=void 0)}}var ve,ye,xe=(Object.freeze||Object)({TRANSFORM:pe,TRANSITION:me,TRANSITION_END:fe,get:rt,getStyle:at,create:ht,remove:ut,empty:lt,toFront:ct,toBack:_t,hasClass:dt,addClass:pt,removeClass:mt,setClass:ft,getClass:gt,setOpacity:vt,testProp:xt,setTransform:wt,setPosition:Lt,getPosition:Pt,disableTextSelection:mi,enableTextSelection:fi,disableImageDrag:bt,enableImageDrag:Tt,preventOutline:zt,restoreOutline:Mt}),we=ui.extend({run:function(t,i,e,n){this.stop(),this._el=t,this._inProgress=!0,this._duration=e||.25,this._easeOutPower=1/Math.max(n||.5,.2),this._startPos=Pt(t),this._offset=i.subtract(this._startPos),this._startTime=+new Date,this.fire("start"),this._animate()},stop:function(){this._inProgress&&(this._step(!0),this._complete())},_animate:function(){this._animId=f(this._animate,this),this._step()},_step:function(t){var i=+new Date-this._startTime,e=1e3*this._duration;i<e?this._runFrame(this._easeOut(i/e),t):(this._runFrame(1),this._complete())},_runFrame:function(t,i){var e=this._startPos.add(this._offset.multiplyBy(t));i&&e._round(),Lt(this._el,e),this.fire("step")},_complete:function(){g(this._animId),this._inProgress=!1,this.fire("end")},_easeOut:function(t){return 1-Math.pow(1-t,this._easeOutPower)}}),Le=ui.extend({options:{crs:vi,center:void 0,zoom:void 0,minZoom:void 0,maxZoom:void 0,layers:[],maxBounds:void 0,renderer:void 0,zoomAnimation:!0,zoomAnimationThreshold:4,fadeAnimation:!0,markerZoomAnimation:!0,transform3DLimit:8388608,zoomSnap:1,zoomDelta:1,trackResize:!0},initialize:function(t,i){i=l(this,i),this._initContainer(t),this._initLayout(),this._onResize=e(this._onResize,this),this._initEvents(),i.maxBounds&&this.setMaxBounds(i.maxBounds),void 0!==i.zoom&&(this._zoom=this._limitZoom(i.zoom)),i.center&&void 0!==i.zoom&&this.setView(C(i.center),i.zoom,{reset:!0}),this._handlers=[],this._layers={},this._zoomBoundLayers={},this._sizeChanged=!0,this.callInitHooks(),this._zoomAnimated=me&&Ni&&!qi&&this.options.zoomAnimation,this._zoomAnimated&&(this._createAnimProxy(),V(this._proxy,fe,this._catchTransitionEnd,this)),this._addLayers(this.options.layers)},setView:function(t,e,n){return e=void 0===e?this._zoom:this._limitZoom(e),t=this._limitCenter(C(t),e,this.options.maxBounds),n=n||{},this._stop(),this._loaded&&!n.reset&&!0!==n&&(void 0!==n.animate&&(n.zoom=i({animate:n.animate},n.zoom),n.pan=i({animate:n.animate,duration:n.duration},n.pan)),this._zoom!==e?this._tryAnimatedZoom&&this._tryAnimatedZoom(t,e,n.zoom):this._tryAnimatedPan(t,n.pan))?(clearTimeout(this._sizeTimer),this):(this._resetView(t,e),this)},setZoom:function(t,i){return this._loaded?this.setView(this.getCenter(),t,{zoom:i}):(this._zoom=t,this)},zoomIn:function(t,i){return t=t||(Ni?this.options.zoomDelta:1),this.setZoom(this._zoom+t,i)},zoomOut:function(t,i){return t=t||(Ni?this.options.zoomDelta:1),this.setZoom(this._zoom-t,i)},setZoomAround:function(t,i,e){var n=this.getZoomScale(i),o=this.getSize().divideBy(2),s=(t instanceof x?t:this.latLngToContainerPoint(t)).subtract(o).multiplyBy(1-1/n),r=this.containerPointToLatLng(o.add(s));return this.setView(r,i,{zoom:e})},_getBoundsCenterZoom:function(t,i){i=i||{},t=t.getBounds?t.getBounds():z(t);var e=w(i.paddingTopLeft||i.padding||[0,0]),n=w(i.paddingBottomRight||i.padding||[0,0]),o=this.getBoundsZoom(t,!1,e.add(n));if((o="number"==typeof i.maxZoom?Math.min(i.maxZoom,o):o)===1/0)return{center:t.getCenter(),zoom:o};var s=n.subtract(e).divideBy(2),r=this.project(t.getSouthWest(),o),a=this.project(t.getNorthEast(),o);return{center:this.unproject(r.add(a).divideBy(2).add(s),o),zoom:o}},fitBounds:function(t,i){if(!(t=z(t)).isValid())throw new Error("Bounds are not valid.");var e=this._getBoundsCenterZoom(t,i);return this.setView(e.center,e.zoom,i)},fitWorld:function(t){return this.fitBounds([[-90,-180],[90,180]],t)},panTo:function(t,i){return this.setView(t,this._zoom,{pan:i})},panBy:function(t,i){if(t=w(t).round(),i=i||{},!t.x&&!t.y)return this.fire("moveend");if(!0!==i.animate&&!this.getSize().contains(t))return this._resetView(this.unproject(this.project(this.getCenter()).add(t)),this.getZoom()),this;if(this._panAnim||(this._panAnim=new we,this._panAnim.on({step:this._onPanTransitionStep,end:this._onPanTransitionEnd},this)),i.noMoveStart||this.fire("movestart"),!1!==i.animate){pt(this._mapPane,"leaflet-pan-anim");var e=this._getMapPanePos().subtract(t).round();this._panAnim.run(this._mapPane,e,i.duration||.25,i.easeLinearity)}else this._rawPanBy(t),this.fire("move").fire("moveend");return this},flyTo:function(t,i,e){function n(t){var i=(g*g-m*m+(t?-1:1)*x*x*v*v)/(2*(t?g:m)*x*v),e=Math.sqrt(i*i+1)-i;return e<1e-9?-18:Math.log(e)}function o(t){return(Math.exp(t)-Math.exp(-t))/2}function s(t){return(Math.exp(t)+Math.exp(-t))/2}function r(t){return o(t)/s(t)}function a(t){return m*(s(w)/s(w+y*t))}function h(t){return m*(s(w)*r(w+y*t)-o(w))/x}function u(t){return 1-Math.pow(1-t,1.5)}function l(){var e=(Date.now()-L)/b,n=u(e)*P;e<=1?(this._flyToFrame=f(l,this),this._move(this.unproject(c.add(_.subtract(c).multiplyBy(h(n)/v)),p),this.getScaleZoom(m/a(n),p),{flyTo:!0})):this._move(t,i)._moveEnd(!0)}if(!1===(e=e||{}).animate||!Ni)return this.setView(t,i,e);this._stop();var c=this.project(this.getCenter()),_=this.project(t),d=this.getSize(),p=this._zoom;t=C(t),i=void 0===i?p:i;var m=Math.max(d.x,d.y),g=m*this.getZoomScale(p,i),v=_.distanceTo(c)||1,y=1.42,x=y*y,w=n(0),L=Date.now(),P=(n(1)-w)/y,b=e.duration?1e3*e.duration:1e3*P*.8;return this._moveStart(!0,e.noMoveStart),l.call(this),this},flyToBounds:function(t,i){var e=this._getBoundsCenterZoom(t,i);return this.flyTo(e.center,e.zoom,i)},setMaxBounds:function(t){return(t=z(t)).isValid()?(this.options.maxBounds&&this.off("moveend",this._panInsideMaxBounds),this.options.maxBounds=t,this._loaded&&this._panInsideMaxBounds(),this.on("moveend",this._panInsideMaxBounds)):(this.options.maxBounds=null,this.off("moveend",this._panInsideMaxBounds))},setMinZoom:function(t){var i=this.options.minZoom;return this.options.minZoom=t,this._loaded&&i!==t&&(this.fire("zoomlevelschange"),this.getZoom()<this.options.minZoom)?this.setZoom(t):this},setMaxZoom:function(t){var i=this.options.maxZoom;return this.options.maxZoom=t,this._loaded&&i!==t&&(this.fire("zoomlevelschange"),this.getZoom()>this.options.maxZoom)?this.setZoom(t):this},panInsideBounds:function(t,i){this._enforcingBounds=!0;var e=this.getCenter(),n=this._limitCenter(e,this._zoom,z(t));return e.equals(n)||this.panTo(n,i),this._enforcingBounds=!1,this},invalidateSize:function(t){if(!this._loaded)return this;t=i({animate:!1,pan:!0},!0===t?{animate:!0}:t);var n=this.getSize();this._sizeChanged=!0,this._lastCenter=null;var o=this.getSize(),s=n.divideBy(2).round(),r=o.divideBy(2).round(),a=s.subtract(r);return a.x||a.y?(t.animate&&t.pan?this.panBy(a):(t.pan&&this._rawPanBy(a),this.fire("move"),t.debounceMoveend?(clearTimeout(this._sizeTimer),this._sizeTimer=setTimeout(e(this.fire,this,"moveend"),200)):this.fire("moveend")),this.fire("resize",{oldSize:n,newSize:o})):this},stop:function(){return this.setZoom(this._limitZoom(this._zoom)),this.options.zoomSnap||this.fire("viewreset"),this._stop()},locate:function(t){if(t=this._locateOptions=i({timeout:1e4,watch:!1},t),!("geolocation"in navigator))return this._handleGeolocationError({code:0,message:"Geolocation not supported."}),this;var n=e(this._handleGeolocationResponse,this),o=e(this._handleGeolocationError,this);return t.watch?this._locationWatchId=navigator.geolocation.watchPosition(n,o,t):navigator.geolocation.getCurrentPosition(n,o,t),this},stopLocate:function(){return navigator.geolocation&&navigator.geolocation.clearWatch&&navigator.geolocation.clearWatch(this._locationWatchId),this._locateOptions&&(this._locateOptions.setView=!1),this},_handleGeolocationError:function(t){var i=t.code,e=t.message||(1===i?"permission denied":2===i?"position unavailable":"timeout");this._locateOptions.setView&&!this._loaded&&this.fitWorld(),this.fire("locationerror",{code:i,message:"Geolocation error: "+e+"."})},_handleGeolocationResponse:function(t){var i=new M(t.coords.latitude,t.coords.longitude),e=i.toBounds(t.coords.accuracy),n=this._locateOptions;if(n.setView){var o=this.getBoundsZoom(e);this.setView(i,n.maxZoom?Math.min(o,n.maxZoom):o)}var s={latlng:i,bounds:e,timestamp:t.timestamp};for(var r in t.coords)"number"==typeof t.coords[r]&&(s[r]=t.coords[r]);this.fire("locationfound",s)},addHandler:function(t,i){if(!i)return this;var e=this[t]=new i(this);return this._handlers.push(e),this.options[t]&&e.enable(),this},remove:function(){if(this._initEvents(!0),this._containerId!==this._container._leaflet_id)throw new Error("Map container is being reused by another instance");try{delete this._container._leaflet_id,delete this._containerId}catch(t){this._container._leaflet_id=void 0,this._containerId=void 0}void 0!==this._locationWatchId&&this.stopLocate(),this._stop(),ut(this._mapPane),this._clearControlPos&&this._clearControlPos(),this._clearHandlers(),this._loaded&&this.fire("unload");var t;for(t in this._layers)this._layers[t].remove();for(t in this._panes)ut(this._panes[t]);return this._layers=[],this._panes=[],delete this._mapPane,delete this._renderer,this},createPane:function(t,i){var e=ht("div","leaflet-pane"+(t?" leaflet-"+t.replace("Pane","")+"-pane":""),i||this._mapPane);return t&&(this._panes[t]=e),e},getCenter:function(){return this._checkIfLoaded(),this._lastCenter&&!this._moved()?this._lastCenter:this.layerPointToLatLng(this._getCenterLayerPoint())},getZoom:function(){return this._zoom},getBounds:function(){var t=this.getPixelBounds();return new T(this.unproject(t.getBottomLeft()),this.unproject(t.getTopRight()))},getMinZoom:function(){return void 0===this.options.minZoom?this._layersMinZoom||0:this.options.minZoom},getMaxZoom:function(){return void 0===this.options.maxZoom?void 0===this._layersMaxZoom?1/0:this._layersMaxZoom:this.options.maxZoom},getBoundsZoom:function(t,i,e){t=z(t),e=w(e||[0,0]);var n=this.getZoom()||0,o=this.getMinZoom(),s=this.getMaxZoom(),r=t.getNorthWest(),a=t.getSouthEast(),h=this.getSize().subtract(e),u=b(this.project(a,n),this.project(r,n)).getSize(),l=Ni?this.options.zoomSnap:1,c=h.x/u.x,_=h.y/u.y,d=i?Math.max(c,_):Math.min(c,_);return n=this.getScaleZoom(d,n),l&&(n=Math.round(n/(l/100))*(l/100),n=i?Math.ceil(n/l)*l:Math.floor(n/l)*l),Math.max(o,Math.min(s,n))},getSize:function(){return this._size&&!this._sizeChanged||(this._size=new x(this._container.clientWidth||0,this._container.clientHeight||0),this._sizeChanged=!1),this._size.clone()},getPixelBounds:function(t,i){var e=this._getTopLeftPoint(t,i);return new P(e,e.add(this.getSize()))},getPixelOrigin:function(){return this._checkIfLoaded(),this._pixelOrigin},getPixelWorldBounds:function(t){return this.options.crs.getProjectedBounds(void 0===t?this.getZoom():t)},getPane:function(t){return"string"==typeof t?this._panes[t]:t},getPanes:function(){return this._panes},getContainer:function(){return this._container},getZoomScale:function(t,i){var e=this.options.crs;return i=void 0===i?this._zoom:i,e.scale(t)/e.scale(i)},getScaleZoom:function(t,i){var e=this.options.crs;i=void 0===i?this._zoom:i;var n=e.zoom(t*e.scale(i));return isNaN(n)?1/0:n},project:function(t,i){return i=void 0===i?this._zoom:i,this.options.crs.latLngToPoint(C(t),i)},unproject:function(t,i){return i=void 0===i?this._zoom:i,this.options.crs.pointToLatLng(w(t),i)},layerPointToLatLng:function(t){var i=w(t).add(this.getPixelOrigin());return this.unproject(i)},latLngToLayerPoint:function(t){return this.project(C(t))._round()._subtract(this.getPixelOrigin())},wrapLatLng:function(t){return this.options.crs.wrapLatLng(C(t))},wrapLatLngBounds:function(t){return this.options.crs.wrapLatLngBounds(z(t))},distance:function(t,i){return this.options.crs.distance(C(t),C(i))},containerPointToLayerPoint:function(t){return w(t).subtract(this._getMapPanePos())},layerPointToContainerPoint:function(t){return w(t).add(this._getMapPanePos())},containerPointToLatLng:function(t){var i=this.containerPointToLayerPoint(w(t));return this.layerPointToLatLng(i)},latLngToContainerPoint:function(t){return this.layerPointToContainerPoint(this.latLngToLayerPoint(C(t)))},mouseEventToContainerPoint:function(t){return tt(t,this._container)},mouseEventToLayerPoint:function(t){return this.containerPointToLayerPoint(this.mouseEventToContainerPoint(t))},mouseEventToLatLng:function(t){return this.layerPointToLatLng(this.mouseEventToLayerPoint(t))},_initContainer:function(t){var i=this._container=rt(t);if(!i)throw new Error("Map container not found.");if(i._leaflet_id)throw new Error("Map container is already initialized.");V(i,"scroll",this._onScroll,this),this._containerId=n(i)},_initLayout:function(){var t=this._container;this._fadeAnimated=this.options.fadeAnimation&&Ni,pt(t,"leaflet-container"+(Vi?" leaflet-touch":"")+(Ki?" leaflet-retina":"")+(Li?" leaflet-oldie":"")+(ki?" leaflet-safari":"")+(this._fadeAnimated?" leaflet-fade-anim":""));var i=at(t,"position");"absolute"!==i&&"relative"!==i&&"fixed"!==i&&(t.style.position="relative"),this._initPanes(),this._initControlPos&&this._initControlPos()},_initPanes:function(){var t=this._panes={};this._paneRenderers={},this._mapPane=this.createPane("mapPane",this._container),Lt(this._mapPane,new x(0,0)),this.createPane("tilePane"),this.createPane("shadowPane"),this.createPane("overlayPane"),this.createPane("markerPane"),this.createPane("tooltipPane"),this.createPane("popupPane"),this.options.markerZoomAnimation||(pt(t.markerPane,"leaflet-zoom-hide"),pt(t.shadowPane,"leaflet-zoom-hide"))},_resetView:function(t,i){Lt(this._mapPane,new x(0,0));var e=!this._loaded;this._loaded=!0,i=this._limitZoom(i),this.fire("viewprereset");var n=this._zoom!==i;this._moveStart(n,!1)._move(t,i)._moveEnd(n),this.fire("viewreset"),e&&this.fire("load")},_moveStart:function(t,i){return t&&this.fire("zoomstart"),i||this.fire("movestart"),this},_move:function(t,i,e){void 0===i&&(i=this._zoom);var n=this._zoom!==i;return this._zoom=i,this._lastCenter=t,this._pixelOrigin=this._getNewPixelOrigin(t),(n||e&&e.pinch)&&this.fire("zoom",e),this.fire("move",e)},_moveEnd:function(t){return t&&this.fire("zoomend"),this.fire("moveend")},_stop:function(){return g(this._flyToFrame),this._panAnim&&this._panAnim.stop(),this},_rawPanBy:function(t){Lt(this._mapPane,this._getMapPanePos().subtract(t))},_getZoomSpan:function(){return this.getMaxZoom()-this.getMinZoom()},_panInsideMaxBounds:function(){this._enforcingBounds||this.panInsideBounds(this.options.maxBounds)},_checkIfLoaded:function(){if(!this._loaded)throw new Error("Set map center and zoom first.")},_initEvents:function(t){this._targets={},this._targets[n(this._container)]=this;var i=t?q:V;i(this._container,"click dblclick mousedown mouseup mouseover mouseout mousemove contextmenu keypress",this._handleDOMEvent,this),this.options.trackResize&&i(window,"resize",this._onResize,this),Ni&&this.options.transform3DLimit&&(t?this.off:this.on).call(this,"moveend",this._onMoveEnd)},_onResize:function(){g(this._resizeRequest),this._resizeRequest=f(function(){this.invalidateSize({debounceMoveend:!0})},this)},_onScroll:function(){this._container.scrollTop=0,this._container.scrollLeft=0},_onMoveEnd:function(){var t=this._getMapPanePos();Math.max(Math.abs(t.x),Math.abs(t.y))>=this.options.transform3DLimit&&this._resetView(this.getCenter(),this.getZoom())},_findEventTargets:function(t,i){for(var e,o=[],s="mouseout"===i||"mouseover"===i,r=t.target||t.srcElement,a=!1;r;){if((e=this._targets[n(r)])&&("click"===i||"preclick"===i)&&!t._simulated&&this._draggableMoved(e)){a=!0;break}if(e&&e.listens(i,!0)){if(s&&!ot(r,t))break;if(o.push(e),s)break}if(r===this._container)break;r=r.parentNode}return o.length||a||s||!ot(r,t)||(o=[this]),o},_handleDOMEvent:function(t){if(this._loaded&&!nt(t)){var i=t.type;"mousedown"!==i&&"keypress"!==i||zt(t.target||t.srcElement),this._fireDOMEvent(t,i)}},_mouseEvents:["click","dblclick","mouseover","mouseout","contextmenu"],_fireDOMEvent:function(t,e,n){if("click"===t.type){var o=i({},t);o.type="preclick",this._fireDOMEvent(o,o.type,n)}if(!t._stopped&&(n=(n||[]).concat(this._findEventTargets(t,e))).length){var s=n[0];"contextmenu"===e&&s.listens(e,!0)&&$(t);var r={originalEvent:t};if("keypress"!==t.type){var a=s.getLatLng&&(!s._radius||s._radius<=10);r.containerPoint=a?this.latLngToContainerPoint(s.getLatLng()):this.mouseEventToContainerPoint(t),r.layerPoint=this.containerPointToLayerPoint(r.containerPoint),r.latlng=a?s.getLatLng():this.layerPointToLatLng(r.layerPoint)}for(var h=0;h<n.length;h++)if(n[h].fire(e,r,!0),r.originalEvent._stopped||!1===n[h].options.bubblingMouseEvents&&-1!==d(this._mouseEvents,e))return}},_draggableMoved:function(t){return(t=t.dragging&&t.dragging.enabled()?t:this).dragging&&t.dragging.moved()||this.boxZoom&&this.boxZoom.moved()},_clearHandlers:function(){for(var t=0,i=this._handlers.length;t<i;t++)this._handlers[t].disable()},whenReady:function(t,i){return this._loaded?t.call(i||this,{target:this}):this.on("load",t,i),this},_getMapPanePos:function(){return Pt(this._mapPane)||new x(0,0)},_moved:function(){var t=this._getMapPanePos();return t&&!t.equals([0,0])},_getTopLeftPoint:function(t,i){return(t&&void 0!==i?this._getNewPixelOrigin(t,i):this.getPixelOrigin()).subtract(this._getMapPanePos())},_getNewPixelOrigin:function(t,i){var e=this.getSize()._divideBy(2);return this.project(t,i)._subtract(e)._add(this._getMapPanePos())._round()},_latLngToNewLayerPoint:function(t,i,e){var n=this._getNewPixelOrigin(e,i);return this.project(t,i)._subtract(n)},_latLngBoundsToNewLayerBounds:function(t,i,e){var n=this._getNewPixelOrigin(e,i);return b([this.project(t.getSouthWest(),i)._subtract(n),this.project(t.getNorthWest(),i)._subtract(n),this.project(t.getSouthEast(),i)._subtract(n),this.project(t.getNorthEast(),i)._subtract(n)])},_getCenterLayerPoint:function(){return this.containerPointToLayerPoint(this.getSize()._divideBy(2))},_getCenterOffset:function(t){return this.latLngToLayerPoint(t).subtract(this._getCenterLayerPoint())},_limitCenter:function(t,i,e){if(!e)return t;var n=this.project(t,i),o=this.getSize().divideBy(2),s=new P(n.subtract(o),n.add(o)),r=this._getBoundsOffset(s,e,i);return r.round().equals([0,0])?t:this.unproject(n.add(r),i)},_limitOffset:function(t,i){if(!i)return t;var e=this.getPixelBounds(),n=new P(e.min.add(t),e.max.add(t));return t.add(this._getBoundsOffset(n,i))},_getBoundsOffset:function(t,i,e){var n=b(this.project(i.getNorthEast(),e),this.project(i.getSouthWest(),e)),o=n.min.subtract(t.min),s=n.max.subtract(t.max);return new x(this._rebound(o.x,-s.x),this._rebound(o.y,-s.y))},_rebound:function(t,i){return t+i>0?Math.round(t-i)/2:Math.max(0,Math.ceil(t))-Math.max(0,Math.floor(i))},_limitZoom:function(t){var i=this.getMinZoom(),e=this.getMaxZoom(),n=Ni?this.options.zoomSnap:1;return n&&(t=Math.round(t/n)*n),Math.max(i,Math.min(e,t))},_onPanTransitionStep:function(){this.fire("move")},_onPanTransitionEnd:function(){mt(this._mapPane,"leaflet-pan-anim"),this.fire("moveend")},_tryAnimatedPan:function(t,i){var e=this._getCenterOffset(t)._trunc();return!(!0!==(i&&i.animate)&&!this.getSize().contains(e))&&(this.panBy(e,i),!0)},_createAnimProxy:function(){var t=this._proxy=ht("div","leaflet-proxy leaflet-zoom-animated");this._panes.mapPane.appendChild(t),this.on("zoomanim",function(t){var i=pe,e=this._proxy.style[i];wt(this._proxy,this.project(t.center,t.zoom),this.getZoomScale(t.zoom,1)),e===this._proxy.style[i]&&this._animatingZoom&&this._onZoomTransitionEnd()},this),this.on("load moveend",function(){var t=this.getCenter(),i=this.getZoom();wt(this._proxy,this.project(t,i),this.getZoomScale(i,1))},this),this._on("unload",this._destroyAnimProxy,this)},_destroyAnimProxy:function(){ut(this._proxy),delete this._proxy},_catchTransitionEnd:function(t){this._animatingZoom&&t.propertyName.indexOf("transform")>=0&&this._onZoomTransitionEnd()},_nothingToAnimate:function(){return!this._container.getElementsByClassName("leaflet-zoom-animated").length},_tryAnimatedZoom:function(t,i,e){if(this._animatingZoom)return!0;if(e=e||{},!this._zoomAnimated||!1===e.animate||this._nothingToAnimate()||Math.abs(i-this._zoom)>this.options.zoomAnimationThreshold)return!1;var n=this.getZoomScale(i),o=this._getCenterOffset(t)._divideBy(1-1/n);return!(!0!==e.animate&&!this.getSize().contains(o))&&(f(function(){this._moveStart(!0,!1)._animateZoom(t,i,!0)},this),!0)},_animateZoom:function(t,i,n,o){this._mapPane&&(n&&(this._animatingZoom=!0,this._animateToCenter=t,this._animateToZoom=i,pt(this._mapPane,"leaflet-zoom-anim")),this.fire("zoomanim",{center:t,zoom:i,noUpdate:o}),setTimeout(e(this._onZoomTransitionEnd,this),250))},_onZoomTransitionEnd:function(){this._animatingZoom&&(this._mapPane&&mt(this._mapPane,"leaflet-zoom-anim"),this._animatingZoom=!1,this._move(this._animateToCenter,this._animateToZoom),f(function(){this._moveEnd(!0)},this))}}),Pe=v.extend({options:{position:"topright"},initialize:function(t){l(this,t)},getPosition:function(){return this.options.position},setPosition:function(t){var i=this._map;return i&&i.removeControl(this),this.options.position=t,i&&i.addControl(this),this},getContainer:function(){return this._container},addTo:function(t){this.remove(),this._map=t;var i=this._container=this.onAdd(t),e=this.getPosition(),n=t._controlCorners[e];return pt(i,"leaflet-control"),-1!==e.indexOf("bottom")?n.insertBefore(i,n.firstChild):n.appendChild(i),this},remove:function(){return this._map?(ut(this._container),this.onRemove&&this.onRemove(this._map),this._map=null,this):this},_refocusOnMap:function(t){this._map&&t&&t.screenX>0&&t.screenY>0&&this._map.getContainer().focus()}}),be=function(t){return new Pe(t)};Le.include({addControl:function(t){return t.addTo(this),this},removeControl:function(t){return t.remove(),this},_initControlPos:function(){function t(t,o){var s=e+t+" "+e+o;i[t+o]=ht("div",s,n)}var i=this._controlCorners={},e="leaflet-",n=this._controlContainer=ht("div",e+"control-container",this._container);t("top","left"),t("top","right"),t("bottom","left"),t("bottom","right")},_clearControlPos:function(){for(var t in this._controlCorners)ut(this._controlCorners[t]);ut(this._controlContainer),delete this._controlCorners,delete this._controlContainer}});var Te=Pe.extend({options:{collapsed:!0,position:"topright",autoZIndex:!0,hideSingleBase:!1,sortLayers:!1,sortFunction:function(t,i,e,n){return e<n?-1:n<e?1:0}},initialize:function(t,i,e){l(this,e),this._layerControlInputs=[],this._layers=[],this._lastZIndex=0,this._handlingClick=!1;for(var n in t)this._addLayer(t[n],n);for(n in i)this._addLayer(i[n],n,!0)},onAdd:function(t){this._initLayout(),this._update(),this._map=t,t.on("zoomend",this._checkDisabledLayers,this);for(var i=0;i<this._layers.length;i++)this._layers[i].layer.on("add remove",this._onLayerChange,this);return this._container},addTo:function(t){return Pe.prototype.addTo.call(this,t),this._expandIfNotCollapsed()},onRemove:function(){this._map.off("zoomend",this._checkDisabledLayers,this);for(var t=0;t<this._layers.length;t++)this._layers[t].layer.off("add remove",this._onLayerChange,this)},addBaseLayer:function(t,i){return this._addLayer(t,i),this._map?this._update():this},addOverlay:function(t,i){return this._addLayer(t,i,!0),this._map?this._update():this},removeLayer:function(t){t.off("add remove",this._onLayerChange,this);var i=this._getLayer(n(t));return i&&this._layers.splice(this._layers.indexOf(i),1),this._map?this._update():this},expand:function(){pt(this._container,"leaflet-control-layers-expanded"),this._form.style.height=null;var t=this._map.getSize().y-(this._container.offsetTop+50);return t<this._form.clientHeight?(pt(this._form,"leaflet-control-layers-scrollbar"),this._form.style.height=t+"px"):mt(this._form,"leaflet-control-layers-scrollbar"),this._checkDisabledLayers(),this},collapse:function(){return mt(this._container,"leaflet-control-layers-expanded"),this},_initLayout:function(){var t="leaflet-control-layers",i=this._container=ht("div",t),e=this.options.collapsed;i.setAttribute("aria-haspopup",!0),J(i),X(i);var n=this._form=ht("form",t+"-list");e&&(this._map.on("click",this.collapse,this),Ti||V(i,{mouseenter:this.expand,mouseleave:this.collapse},this));var o=this._layersLink=ht("a",t+"-toggle",i);o.href="#",o.title="Layers",Vi?(V(o,"click",Q),V(o,"click",this.expand,this)):V(o,"focus",this.expand,this),e||this.expand(),this._baseLayersList=ht("div",t+"-base",n),this._separator=ht("div",t+"-separator",n),this._overlaysList=ht("div",t+"-overlays",n),i.appendChild(n)},_getLayer:function(t){for(var i=0;i<this._layers.length;i++)if(this._layers[i]&&n(this._layers[i].layer)===t)return this._layers[i]},_addLayer:function(t,i,n){this._map&&t.on("add remove",this._onLayerChange,this),this._layers.push({layer:t,name:i,overlay:n}),this.options.sortLayers&&this._layers.sort(e(function(t,i){return this.options.sortFunction(t.layer,i.layer,t.name,i.name)},this)),this.options.autoZIndex&&t.setZIndex&&(this._lastZIndex++,t.setZIndex(this._lastZIndex)),this._expandIfNotCollapsed()},_update:function(){if(!this._container)return this;lt(this._baseLayersList),lt(this._overlaysList),this._layerControlInputs=[];var t,i,e,n,o=0;for(e=0;e<this._layers.length;e++)n=this._layers[e],this._addItem(n),i=i||n.overlay,t=t||!n.overlay,o+=n.overlay?0:1;return this.options.hideSingleBase&&(t=t&&o>1,this._baseLayersList.style.display=t?"":"none"),this._separator.style.display=i&&t?"":"none",this},_onLayerChange:function(t){this._handlingClick||this._update();var i=this._getLayer(n(t.target)),e=i.overlay?"add"===t.type?"overlayadd":"overlayremove":"add"===t.type?"baselayerchange":null;e&&this._map.fire(e,i)},_createRadioElement:function(t,i){var e='<input type="radio" class="leaflet-control-layers-selector" name="'+t+'"'+(i?' checked="checked"':"")+"/>",n=document.createElement("div");return n.innerHTML=e,n.firstChild},_addItem:function(t){var i,e=document.createElement("label"),o=this._map.hasLayer(t.layer);t.overlay?((i=document.createElement("input")).type="checkbox",i.className="leaflet-control-layers-selector",i.defaultChecked=o):i=this._createRadioElement("leaflet-base-layers",o),this._layerControlInputs.push(i),i.layerId=n(t.layer),V(i,"click",this._onInputClick,this);var s=document.createElement("span");s.innerHTML=" "+t.name;var r=document.createElement("div");return e.appendChild(r),r.appendChild(i),r.appendChild(s),(t.overlay?this._overlaysList:this._baseLayersList).appendChild(e),this._checkDisabledLayers(),e},_onInputClick:function(){var t,i,e=this._layerControlInputs,n=[],o=[];this._handlingClick=!0;for(var s=e.length-1;s>=0;s--)t=e[s],i=this._getLayer(t.layerId).layer,t.checked?n.push(i):t.checked||o.push(i);for(s=0;s<o.length;s++)this._map.hasLayer(o[s])&&this._map.removeLayer(o[s]);for(s=0;s<n.length;s++)this._map.hasLayer(n[s])||this._map.addLayer(n[s]);this._handlingClick=!1,this._refocusOnMap()},_checkDisabledLayers:function(){for(var t,i,e=this._layerControlInputs,n=this._map.getZoom(),o=e.length-1;o>=0;o--)t=e[o],i=this._getLayer(t.layerId).layer,t.disabled=void 0!==i.options.minZoom&&n<i.options.minZoom||void 0!==i.options.maxZoom&&n>i.options.maxZoom},_expandIfNotCollapsed:function(){return this._map&&!this.options.collapsed&&this.expand(),this},_expand:function(){return this.expand()},_collapse:function(){return this.collapse()}}),ze=Pe.extend({options:{position:"topleft",zoomInText:"+",zoomInTitle:"Zoom in",zoomOutText:"&#x2212;",zoomOutTitle:"Zoom out"},onAdd:function(t){var i="leaflet-control-zoom",e=ht("div",i+" leaflet-bar"),n=this.options;return this._zoomInButton=this._createButton(n.zoomInText,n.zoomInTitle,i+"-in",e,this._zoomIn),this._zoomOutButton=this._createButton(n.zoomOutText,n.zoomOutTitle,i+"-out",e,this._zoomOut),this._updateDisabled(),t.on("zoomend zoomlevelschange",this._updateDisabled,this),e},onRemove:function(t){t.off("zoomend zoomlevelschange",this._updateDisabled,this)},disable:function(){return this._disabled=!0,this._updateDisabled(),this},enable:function(){return this._disabled=!1,this._updateDisabled(),this},_zoomIn:function(t){!this._disabled&&this._map._zoom<this._map.getMaxZoom()&&this._map.zoomIn(this._map.options.zoomDelta*(t.shiftKey?3:1))},_zoomOut:function(t){!this._disabled&&this._map._zoom>this._map.getMinZoom()&&this._map.zoomOut(this._map.options.zoomDelta*(t.shiftKey?3:1))},_createButton:function(t,i,e,n,o){var s=ht("a",e,n);return s.innerHTML=t,s.href="#",s.title=i,s.setAttribute("role","button"),s.setAttribute("aria-label",i),J(s),V(s,"click",Q),V(s,"click",o,this),V(s,"click",this._refocusOnMap,this),s},_updateDisabled:function(){var t=this._map,i="leaflet-disabled";mt(this._zoomInButton,i),mt(this._zoomOutButton,i),(this._disabled||t._zoom===t.getMinZoom())&&pt(this._zoomOutButton,i),(this._disabled||t._zoom===t.getMaxZoom())&&pt(this._zoomInButton,i)}});Le.mergeOptions({zoomControl:!0}),Le.addInitHook(function(){this.options.zoomControl&&(this.zoomControl=new ze,this.addControl(this.zoomControl))});var Me=Pe.extend({options:{position:"bottomleft",maxWidth:100,metric:!0,imperial:!0},onAdd:function(t){var i=ht("div","leaflet-control-scale"),e=this.options;return this._addScales(e,"leaflet-control-scale-line",i),t.on(e.updateWhenIdle?"moveend":"move",this._update,this),t.whenReady(this._update,this),i},onRemove:function(t){t.off(this.options.updateWhenIdle?"moveend":"move",this._update,this)},_addScales:function(t,i,e){t.metric&&(this._mScale=ht("div",i,e)),t.imperial&&(this._iScale=ht("div",i,e))},_update:function(){var t=this._map,i=t.getSize().y/2,e=t.distance(t.containerPointToLatLng([0,i]),t.containerPointToLatLng([this.options.maxWidth,i]));this._updateScales(e)},_updateScales:function(t){this.options.metric&&t&&this._updateMetric(t),this.options.imperial&&t&&this._updateImperial(t)},_updateMetric:function(t){var i=this._getRoundNum(t),e=i<1e3?i+" m":i/1e3+" km";this._updateScale(this._mScale,e,i/t)},_updateImperial:function(t){var i,e,n,o=3.2808399*t;o>5280?(i=o/5280,e=this._getRoundNum(i),this._updateScale(this._iScale,e+" mi",e/i)):(n=this._getRoundNum(o),this._updateScale(this._iScale,n+" ft",n/o))},_updateScale:function(t,i,e){t.style.width=Math.round(this.options.maxWidth*e)+"px",t.innerHTML=i},_getRoundNum:function(t){var i=Math.pow(10,(Math.floor(t)+"").length-1),e=t/i;return e=e>=10?10:e>=5?5:e>=3?3:e>=2?2:1,i*e}}),Ce=Pe.extend({options:{position:"bottomright",prefix:'<a href="http://leafletjs.com" title="A JS library for interactive maps">Leaflet</a>'},initialize:function(t){l(this,t),this._attributions={}},onAdd:function(t){t.attributionControl=this,this._container=ht("div","leaflet-control-attribution"),J(this._container);for(var i in t._layers)t._layers[i].getAttribution&&this.addAttribution(t._layers[i].getAttribution());return this._update(),this._container},setPrefix:function(t){return this.options.prefix=t,this._update(),this},addAttribution:function(t){return t?(this._attributions[t]||(this._attributions[t]=0),this._attributions[t]++,this._update(),this):this},removeAttribution:function(t){return t?(this._attributions[t]&&(this._attributions[t]--,this._update()),this):this},_update:function(){if(this._map){var t=[];for(var i in this._attributions)this._attributions[i]&&t.push(i);var e=[];this.options.prefix&&e.push(this.options.prefix),t.length&&e.push(t.join(", ")),this._container.innerHTML=e.join(" | ")}}});Le.mergeOptions({attributionControl:!0}),Le.addInitHook(function(){this.options.attributionControl&&(new Ce).addTo(this)});Pe.Layers=Te,Pe.Zoom=ze,Pe.Scale=Me,Pe.Attribution=Ce,be.layers=function(t,i,e){return new Te(t,i,e)},be.zoom=function(t){return new ze(t)},be.scale=function(t){return new Me(t)},be.attribution=function(t){return new Ce(t)};var Ze=v.extend({initialize:function(t){this._map=t},enable:function(){return this._enabled?this:(this._enabled=!0,this.addHooks(),this)},disable:function(){return this._enabled?(this._enabled=!1,this.removeHooks(),this):this},enabled:function(){return!!this._enabled}});Ze.addTo=function(t,i){return t.addHandler(i,this),this};var Se,Ee={Events:hi},ke=Vi?"touchstart mousedown":"mousedown",Ie={mousedown:"mouseup",touchstart:"touchend",pointerdown:"touchend",MSPointerDown:"touchend"},Ae={mousedown:"mousemove",touchstart:"touchmove",pointerdown:"touchmove",MSPointerDown:"touchmove"},Be=ui.extend({options:{clickTolerance:3},initialize:function(t,i,e,n){l(this,n),this._element=t,this._dragStartTarget=i||t,this._preventOutline=e},enable:function(){this._enabled||(V(this._dragStartTarget,ke,this._onDown,this),this._enabled=!0)},disable:function(){this._enabled&&(Be._dragging===this&&this.finishDrag(),q(this._dragStartTarget,ke,this._onDown,this),this._enabled=!1,this._moved=!1)},_onDown:function(t){if(!t._simulated&&this._enabled&&(this._moved=!1,!dt(this._element,"leaflet-zoom-anim")&&!(Be._dragging||t.shiftKey||1!==t.which&&1!==t.button&&!t.touches||(Be._dragging=this,this._preventOutline&&zt(this._element),bt(),mi(),this._moving)))){this.fire("down");var i=t.touches?t.touches[0]:t;this._startPoint=new x(i.clientX,i.clientY),V(document,Ae[t.type],this._onMove,this),V(document,Ie[t.type],this._onUp,this)}},_onMove:function(t){if(!t._simulated&&this._enabled)if(t.touches&&t.touches.length>1)this._moved=!0;else{var i=t.touches&&1===t.touches.length?t.touches[0]:t,e=new x(i.clientX,i.clientY).subtract(this._startPoint);(e.x||e.y)&&(Math.abs(e.x)+Math.abs(e.y)<this.options.clickTolerance||($(t),this._moved||(this.fire("dragstart"),this._moved=!0,this._startPos=Pt(this._element).subtract(e),pt(document.body,"leaflet-dragging"),this._lastTarget=t.target||t.srcElement,window.SVGElementInstance&&this._lastTarget instanceof SVGElementInstance&&(this._lastTarget=this._lastTarget.correspondingUseElement),pt(this._lastTarget,"leaflet-drag-target")),this._newPos=this._startPos.add(e),this._moving=!0,g(this._animRequest),this._lastEvent=t,this._animRequest=f(this._updatePosition,this,!0)))}},_updatePosition:function(){var t={originalEvent:this._lastEvent};this.fire("predrag",t),Lt(this._element,this._newPos),this.fire("drag",t)},_onUp:function(t){!t._simulated&&this._enabled&&this.finishDrag()},finishDrag:function(){mt(document.body,"leaflet-dragging"),this._lastTarget&&(mt(this._lastTarget,"leaflet-drag-target"),this._lastTarget=null);for(var t in Ae)q(document,Ae[t],this._onMove,this),q(document,Ie[t],this._onUp,this);Tt(),fi(),this._moved&&this._moving&&(g(this._animRequest),this.fire("dragend",{distance:this._newPos.distanceTo(this._startPos)})),this._moving=!1,Be._dragging=!1}}),Oe=(Object.freeze||Object)({simplify:Ct,pointToSegmentDistance:Zt,closestPointOnSegment:function(t,i,e){return Rt(t,i,e)},clipSegment:It,_getEdgeIntersection:At,_getBitCode:Bt,_sqClosestPointOnSegment:Rt,isFlat:Dt,_flat:Nt}),Re=(Object.freeze||Object)({clipPolygon:jt}),De={project:function(t){return new x(t.lng,t.lat)},unproject:function(t){return new M(t.y,t.x)},bounds:new P([-180,-90],[180,90])},Ne={R:6378137,R_MINOR:6356752.314245179,bounds:new P([-20037508.34279,-15496570.73972],[20037508.34279,18764656.23138]),project:function(t){var i=Math.PI/180,e=this.R,n=t.lat*i,o=this.R_MINOR/e,s=Math.sqrt(1-o*o),r=s*Math.sin(n),a=Math.tan(Math.PI/4-n/2)/Math.pow((1-r)/(1+r),s/2);return n=-e*Math.log(Math.max(a,1e-10)),new x(t.lng*i*e,n)},unproject:function(t){for(var i,e=180/Math.PI,n=this.R,o=this.R_MINOR/n,s=Math.sqrt(1-o*o),r=Math.exp(-t.y/n),a=Math.PI/2-2*Math.atan(r),h=0,u=.1;h<15&&Math.abs(u)>1e-7;h++)i=s*Math.sin(a),i=Math.pow((1-i)/(1+i),s/2),a+=u=Math.PI/2-2*Math.atan(r*i)-a;return new M(a*e,t.x*e/n)}},je=(Object.freeze||Object)({LonLat:De,Mercator:Ne,SphericalMercator:di}),We=i({},_i,{code:"EPSG:3395",projection:Ne,transformation:function(){var t=.5/(Math.PI*Ne.R);return S(t,.5,-t,.5)}()}),He=i({},_i,{code:"EPSG:4326",projection:De,transformation:S(1/180,1,-1/180,.5)}),Fe=i({},ci,{projection:De,transformation:S(1,0,-1,0),scale:function(t){return Math.pow(2,t)},zoom:function(t){return Math.log(t)/Math.LN2},distance:function(t,i){var e=i.lng-t.lng,n=i.lat-t.lat;return Math.sqrt(e*e+n*n)},infinite:!0});ci.Earth=_i,ci.EPSG3395=We,ci.EPSG3857=vi,ci.EPSG900913=yi,ci.EPSG4326=He,ci.Simple=Fe;var Ue=ui.extend({options:{pane:"overlayPane",attribution:null,bubblingMouseEvents:!0},addTo:function(t){return t.addLayer(this),this},remove:function(){return this.removeFrom(this._map||this._mapToAdd)},removeFrom:function(t){return t&&t.removeLayer(this),this},getPane:function(t){return this._map.getPane(t?this.options[t]||t:this.options.pane)},addInteractiveTarget:function(t){return this._map._targets[n(t)]=this,this},removeInteractiveTarget:function(t){return delete this._map._targets[n(t)],this},getAttribution:function(){return this.options.attribution},_layerAdd:function(t){var i=t.target;if(i.hasLayer(this)){if(this._map=i,this._zoomAnimated=i._zoomAnimated,this.getEvents){var e=this.getEvents();i.on(e,this),this.once("remove",function(){i.off(e,this)},this)}this.onAdd(i),this.getAttribution&&i.attributionControl&&i.attributionControl.addAttribution(this.getAttribution()),this.fire("add"),i.fire("layeradd",{layer:this})}}});Le.include({addLayer:function(t){if(!t._layerAdd)throw new Error("The provided object is not a Layer.");var i=n(t);return this._layers[i]?this:(this._layers[i]=t,t._mapToAdd=this,t.beforeAdd&&t.beforeAdd(this),this.whenReady(t._layerAdd,t),this)},removeLayer:function(t){var i=n(t);return this._layers[i]?(this._loaded&&t.onRemove(this),t.getAttribution&&this.attributionControl&&this.attributionControl.removeAttribution(t.getAttribution()),delete this._layers[i],this._loaded&&(this.fire("layerremove",{layer:t}),t.fire("remove")),t._map=t._mapToAdd=null,this):this},hasLayer:function(t){return!!t&&n(t)in this._layers},eachLayer:function(t,i){for(var e in this._layers)t.call(i,this._layers[e]);return this},_addLayers:function(t){for(var i=0,e=(t=t?ei(t)?t:[t]:[]).length;i<e;i++)this.addLayer(t[i])},_addZoomLimit:function(t){!isNaN(t.options.maxZoom)&&isNaN(t.options.minZoom)||(this._zoomBoundLayers[n(t)]=t,this._updateZoomLevels())},_removeZoomLimit:function(t){var i=n(t);this._zoomBoundLayers[i]&&(delete this._zoomBoundLayers[i],this._updateZoomLevels())},_updateZoomLevels:function(){var t=1/0,i=-1/0,e=this._getZoomSpan();for(var n in this._zoomBoundLayers){var o=this._zoomBoundLayers[n].options;t=void 0===o.minZoom?t:Math.min(t,o.minZoom),i=void 0===o.maxZoom?i:Math.max(i,o.maxZoom)}this._layersMaxZoom=i===-1/0?void 0:i,this._layersMinZoom=t===1/0?void 0:t,e!==this._getZoomSpan()&&this.fire("zoomlevelschange"),void 0===this.options.maxZoom&&this._layersMaxZoom&&this.getZoom()>this._layersMaxZoom&&this.setZoom(this._layersMaxZoom),void 0===this.options.minZoom&&this._layersMinZoom&&this.getZoom()<this._layersMinZoom&&this.setZoom(this._layersMinZoom)}});var Ve=Ue.extend({initialize:function(t,i){l(this,i),this._layers={};var e,n;if(t)for(e=0,n=t.length;e<n;e++)this.addLayer(t[e])},addLayer:function(t){var i=this.getLayerId(t);return this._layers[i]=t,this._map&&this._map.addLayer(t),this},removeLayer:function(t){var i=t in this._layers?t:this.getLayerId(t);return this._map&&this._layers[i]&&this._map.removeLayer(this._layers[i]),delete this._layers[i],this},hasLayer:function(t){return!!t&&(t in this._layers||this.getLayerId(t)in this._layers)},clearLayers:function(){return this.eachLayer(this.removeLayer,this)},invoke:function(t){var i,e,n=Array.prototype.slice.call(arguments,1);for(i in this._layers)(e=this._layers[i])[t]&&e[t].apply(e,n);return this},onAdd:function(t){this.eachLayer(t.addLayer,t)},onRemove:function(t){this.eachLayer(t.removeLayer,t)},eachLayer:function(t,i){for(var e in this._layers)t.call(i,this._layers[e]);return this},getLayer:function(t){return this._layers[t]},getLayers:function(){var t=[];return this.eachLayer(t.push,t),t},setZIndex:function(t){return this.invoke("setZIndex",t)},getLayerId:function(t){return n(t)}}),qe=Ve.extend({addLayer:function(t){return this.hasLayer(t)?this:(t.addEventParent(this),Ve.prototype.addLayer.call(this,t),this.fire("layeradd",{layer:t}))},removeLayer:function(t){return this.hasLayer(t)?(t in this._layers&&(t=this._layers[t]),t.removeEventParent(this),Ve.prototype.removeLayer.call(this,t),this.fire("layerremove",{layer:t})):this},setStyle:function(t){return this.invoke("setStyle",t)},bringToFront:function(){return this.invoke("bringToFront")},bringToBack:function(){return this.invoke("bringToBack")},getBounds:function(){var t=new T;for(var i in this._layers){var e=this._layers[i];t.extend(e.getBounds?e.getBounds():e.getLatLng())}return t}}),Ge=v.extend({options:{popupAnchor:[0,0],tooltipAnchor:[0,0]},initialize:function(t){l(this,t)},createIcon:function(t){return this._createIcon("icon",t)},createShadow:function(t){return this._createIcon("shadow",t)},_createIcon:function(t,i){var e=this._getIconUrl(t);if(!e){if("icon"===t)throw new Error("iconUrl not set in Icon options (see the docs).");return null}var n=this._createImg(e,i&&"IMG"===i.tagName?i:null);return this._setIconStyles(n,t),n},_setIconStyles:function(t,i){var e=this.options,n=e[i+"Size"];"number"==typeof n&&(n=[n,n]);var o=w(n),s=w("shadow"===i&&e.shadowAnchor||e.iconAnchor||o&&o.divideBy(2,!0));t.className="leaflet-marker-"+i+" "+(e.className||""),s&&(t.style.marginLeft=-s.x+"px",t.style.marginTop=-s.y+"px"),o&&(t.style.width=o.x+"px",t.style.height=o.y+"px")},_createImg:function(t,i){return i=i||document.createElement("img"),i.src=t,i},_getIconUrl:function(t){return Ki&&this.options[t+"RetinaUrl"]||this.options[t+"Url"]}}),Ke=Ge.extend({options:{iconUrl:"marker-icon.png",iconRetinaUrl:"marker-icon-2x.png",shadowUrl:"marker-shadow.png",iconSize:[25,41],iconAnchor:[12,41],popupAnchor:[1,-34],tooltipAnchor:[16,-28],shadowSize:[41,41]},_getIconUrl:function(t){return Ke.imagePath||(Ke.imagePath=this._detectIconPath()),(this.options.imagePath||Ke.imagePath)+Ge.prototype._getIconUrl.call(this,t)},_detectIconPath:function(){var t=ht("div","leaflet-default-icon-path",document.body),i=at(t,"background-image")||at(t,"backgroundImage");return document.body.removeChild(t),i=null===i||0!==i.indexOf("url")?"":i.replace(/^url\(["']?/,"").replace(/marker-icon\.png["']?\)$/,"")}}),Ye=Ze.extend({initialize:function(t){this._marker=t},addHooks:function(){var t=this._marker._icon;this._draggable||(this._draggable=new Be(t,t,!0)),this._draggable.on({dragstart:this._onDragStart,predrag:this._onPreDrag,drag:this._onDrag,dragend:this._onDragEnd},this).enable(),pt(t,"leaflet-marker-draggable")},removeHooks:function(){this._draggable.off({dragstart:this._onDragStart,predrag:this._onPreDrag,drag:this._onDrag,dragend:this._onDragEnd},this).disable(),this._marker._icon&&mt(this._marker._icon,"leaflet-marker-draggable")},moved:function(){return this._draggable&&this._draggable._moved},_adjustPan:function(t){var i=this._marker,e=i._map,n=this._marker.options.autoPanSpeed,o=this._marker.options.autoPanPadding,s=L.DomUtil.getPosition(i._icon),r=e.getPixelBounds(),a=e.getPixelOrigin(),h=b(r.min._subtract(a).add(o),r.max._subtract(a).subtract(o));if(!h.contains(s)){var u=w((Math.max(h.max.x,s.x)-h.max.x)/(r.max.x-h.max.x)-(Math.min(h.min.x,s.x)-h.min.x)/(r.min.x-h.min.x),(Math.max(h.max.y,s.y)-h.max.y)/(r.max.y-h.max.y)-(Math.min(h.min.y,s.y)-h.min.y)/(r.min.y-h.min.y)).multiplyBy(n);e.panBy(u,{animate:!1}),this._draggable._newPos._add(u),this._draggable._startPos._add(u),L.DomUtil.setPosition(i._icon,this._draggable._newPos),this._onDrag(t),this._panRequest=f(this._adjustPan.bind(this,t))}},_onDragStart:function(){this._oldLatLng=this._marker.getLatLng(),this._marker.closePopup().fire("movestart").fire("dragstart")},_onPreDrag:function(t){this._marker.options.autoPan&&(g(this._panRequest),this._panRequest=f(this._adjustPan.bind(this,t)))},_onDrag:function(t){var i=this._marker,e=i._shadow,n=Pt(i._icon),o=i._map.layerPointToLatLng(n);e&&Lt(e,n),i._latlng=o,t.latlng=o,t.oldLatLng=this._oldLatLng,i.fire("move",t).fire("drag",t)},_onDragEnd:function(t){g(this._panRequest),delete this._oldLatLng,this._marker.fire("moveend").fire("dragend",t)}}),Xe=Ue.extend({options:{icon:new Ke,interactive:!0,draggable:!1,autoPan:!1,autoPanPadding:[50,50],autoPanSpeed:10,keyboard:!0,title:"",alt:"",zIndexOffset:0,opacity:1,riseOnHover:!1,riseOffset:250,pane:"markerPane",bubblingMouseEvents:!1},initialize:function(t,i){l(this,i),this._latlng=C(t)},onAdd:function(t){this._zoomAnimated=this._zoomAnimated&&t.options.markerZoomAnimation,this._zoomAnimated&&t.on("zoomanim",this._animateZoom,this),this._initIcon(),this.update()},onRemove:function(t){this.dragging&&this.dragging.enabled()&&(this.options.draggable=!0,this.dragging.removeHooks()),delete this.dragging,this._zoomAnimated&&t.off("zoomanim",this._animateZoom,this),this._removeIcon(),this._removeShadow()},getEvents:function(){return{zoom:this.update,viewreset:this.update}},getLatLng:function(){return this._latlng},setLatLng:function(t){var i=this._latlng;return this._latlng=C(t),this.update(),this.fire("move",{oldLatLng:i,latlng:this._latlng})},setZIndexOffset:function(t){return this.options.zIndexOffset=t,this.update()},setIcon:function(t){return this.options.icon=t,this._map&&(this._initIcon(),this.update()),this._popup&&this.bindPopup(this._popup,this._popup.options),this},getElement:function(){return this._icon},update:function(){if(this._icon&&this._map){var t=this._map.latLngToLayerPoint(this._latlng).round();this._setPos(t)}return this},_initIcon:function(){var t=this.options,i="leaflet-zoom-"+(this._zoomAnimated?"animated":"hide"),e=t.icon.createIcon(this._icon),n=!1;e!==this._icon&&(this._icon&&this._removeIcon(),n=!0,t.title&&(e.title=t.title),"IMG"===e.tagName&&(e.alt=t.alt||"")),pt(e,i),t.keyboard&&(e.tabIndex="0"),this._icon=e,t.riseOnHover&&this.on({mouseover:this._bringToFront,mouseout:this._resetZIndex});var o=t.icon.createShadow(this._shadow),s=!1;o!==this._shadow&&(this._removeShadow(),s=!0),o&&(pt(o,i),o.alt=""),this._shadow=o,t.opacity<1&&this._updateOpacity(),n&&this.getPane().appendChild(this._icon),this._initInteraction(),o&&s&&this.getPane("shadowPane").appendChild(this._shadow)},_removeIcon:function(){this.options.riseOnHover&&this.off({mouseover:this._bringToFront,mouseout:this._resetZIndex}),ut(this._icon),this.removeInteractiveTarget(this._icon),this._icon=null},_removeShadow:function(){this._shadow&&ut(this._shadow),this._shadow=null},_setPos:function(t){Lt(this._icon,t),this._shadow&&Lt(this._shadow,t),this._zIndex=t.y+this.options.zIndexOffset,this._resetZIndex()},_updateZIndex:function(t){this._icon.style.zIndex=this._zIndex+t},_animateZoom:function(t){var i=this._map._latLngToNewLayerPoint(this._latlng,t.zoom,t.center).round();this._setPos(i)},_initInteraction:function(){if(this.options.interactive&&(pt(this._icon,"leaflet-interactive"),this.addInteractiveTarget(this._icon),Ye)){var t=this.options.draggable;this.dragging&&(t=this.dragging.enabled(),this.dragging.disable()),this.dragging=new Ye(this),t&&this.dragging.enable()}},setOpacity:function(t){return this.options.opacity=t,this._map&&this._updateOpacity(),this},_updateOpacity:function(){var t=this.options.opacity;vt(this._icon,t),this._shadow&&vt(this._shadow,t)},_bringToFront:function(){this._updateZIndex(this.options.riseOffset)},_resetZIndex:function(){this._updateZIndex(0)},_getPopupAnchor:function(){return this.options.icon.options.popupAnchor},_getTooltipAnchor:function(){return this.options.icon.options.tooltipAnchor}}),Je=Ue.extend({options:{stroke:!0,color:"#3388ff",weight:3,opacity:1,lineCap:"round",lineJoin:"round",dashArray:null,dashOffset:null,fill:!1,fillColor:null,fillOpacity:.2,fillRule:"evenodd",interactive:!0,bubblingMouseEvents:!0},beforeAdd:function(t){this._renderer=t.getRenderer(this)},onAdd:function(){this._renderer._initPath(this),this._reset(),this._renderer._addPath(this)},onRemove:function(){this._renderer._removePath(this)},redraw:function(){return this._map&&this._renderer._updatePath(this),this},setStyle:function(t){return l(this,t),this._renderer&&this._renderer._updateStyle(this),this},bringToFront:function(){return this._renderer&&this._renderer._bringToFront(this),this},bringToBack:function(){return this._renderer&&this._renderer._bringToBack(this),this},getElement:function(){return this._path},_reset:function(){this._project(),this._update()},_clickTolerance:function(){return(this.options.stroke?this.options.weight/2:0)+this._renderer.options.tolerance}}),$e=Je.extend({options:{fill:!0,radius:10},initialize:function(t,i){l(this,i),this._latlng=C(t),this._radius=this.options.radius},setLatLng:function(t){return this._latlng=C(t),this.redraw(),this.fire("move",{latlng:this._latlng})},getLatLng:function(){return this._latlng},setRadius:function(t){return this.options.radius=this._radius=t,this.redraw()},getRadius:function(){return this._radius},setStyle:function(t){var i=t&&t.radius||this._radius;return Je.prototype.setStyle.call(this,t),this.setRadius(i),this},_project:function(){this._point=this._map.latLngToLayerPoint(this._latlng),this._updateBounds()},_updateBounds:function(){var t=this._radius,i=this._radiusY||t,e=this._clickTolerance(),n=[t+e,i+e];this._pxBounds=new P(this._point.subtract(n),this._point.add(n))},_update:function(){this._map&&this._updatePath()},_updatePath:function(){this._renderer._updateCircle(this)},_empty:function(){return this._radius&&!this._renderer._bounds.intersects(this._pxBounds)},_containsPoint:function(t){return t.distanceTo(this._point)<=this._radius+this._clickTolerance()}}),Qe=$e.extend({initialize:function(t,e,n){if("number"==typeof e&&(e=i({},n,{radius:e})),l(this,e),this._latlng=C(t),isNaN(this.options.radius))throw new Error("Circle radius cannot be NaN");this._mRadius=this.options.radius},setRadius:function(t){return this._mRadius=t,this.redraw()},getRadius:function(){return this._mRadius},getBounds:function(){var t=[this._radius,this._radiusY||this._radius];return new T(this._map.layerPointToLatLng(this._point.subtract(t)),this._map.layerPointToLatLng(this._point.add(t)))},setStyle:Je.prototype.setStyle,_project:function(){var t=this._latlng.lng,i=this._latlng.lat,e=this._map,n=e.options.crs;if(n.distance===_i.distance){var o=Math.PI/180,s=this._mRadius/_i.R/o,r=e.project([i+s,t]),a=e.project([i-s,t]),h=r.add(a).divideBy(2),u=e.unproject(h).lat,l=Math.acos((Math.cos(s*o)-Math.sin(i*o)*Math.sin(u*o))/(Math.cos(i*o)*Math.cos(u*o)))/o;(isNaN(l)||0===l)&&(l=s/Math.cos(Math.PI/180*i)),this._point=h.subtract(e.getPixelOrigin()),this._radius=isNaN(l)?0:h.x-e.project([u,t-l]).x,this._radiusY=h.y-r.y}else{var c=n.unproject(n.project(this._latlng).subtract([this._mRadius,0]));this._point=e.latLngToLayerPoint(this._latlng),this._radius=this._point.x-e.latLngToLayerPoint(c).x}this._updateBounds()}}),tn=Je.extend({options:{smoothFactor:1,noClip:!1},initialize:function(t,i){l(this,i),this._setLatLngs(t)},getLatLngs:function(){return this._latlngs},setLatLngs:function(t){return this._setLatLngs(t),this.redraw()},isEmpty:function(){return!this._latlngs.length},closestLayerPoint:function(t){for(var i,e,n=1/0,o=null,s=Rt,r=0,a=this._parts.length;r<a;r++)for(var h=this._parts[r],u=1,l=h.length;u<l;u++){var c=s(t,i=h[u-1],e=h[u],!0);c<n&&(n=c,o=s(t,i,e))}return o&&(o.distance=Math.sqrt(n)),o},getCenter:function(){if(!this._map)throw new Error("Must add layer to map before using getCenter()");var t,i,e,n,o,s,r,a=this._rings[0],h=a.length;if(!h)return null;for(t=0,i=0;t<h-1;t++)i+=a[t].distanceTo(a[t+1])/2;if(0===i)return this._map.layerPointToLatLng(a[0]);for(t=0,n=0;t<h-1;t++)if(o=a[t],s=a[t+1],e=o.distanceTo(s),(n+=e)>i)return r=(n-i)/e,this._map.layerPointToLatLng([s.x-r*(s.x-o.x),s.y-r*(s.y-o.y)])},getBounds:function(){return this._bounds},addLatLng:function(t,i){return i=i||this._defaultShape(),t=C(t),i.push(t),this._bounds.extend(t),this.redraw()},_setLatLngs:function(t){this._bounds=new T,this._latlngs=this._convertLatLngs(t)},_defaultShape:function(){return Dt(this._latlngs)?this._latlngs:this._latlngs[0]},_convertLatLngs:function(t){for(var i=[],e=Dt(t),n=0,o=t.length;n<o;n++)e?(i[n]=C(t[n]),this._bounds.extend(i[n])):i[n]=this._convertLatLngs(t[n]);return i},_project:function(){var t=new P;this._rings=[],this._projectLatlngs(this._latlngs,this._rings,t);var i=this._clickTolerance(),e=new x(i,i);this._bounds.isValid()&&t.isValid()&&(t.min._subtract(e),t.max._add(e),this._pxBounds=t)},_projectLatlngs:function(t,i,e){var n,o,s=t[0]instanceof M,r=t.length;if(s){for(o=[],n=0;n<r;n++)o[n]=this._map.latLngToLayerPoint(t[n]),e.extend(o[n]);i.push(o)}else for(n=0;n<r;n++)this._projectLatlngs(t[n],i,e)},_clipPoints:function(){var t=this._renderer._bounds;if(this._parts=[],this._pxBounds&&this._pxBounds.intersects(t))if(this.options.noClip)this._parts=this._rings;else{var i,e,n,o,s,r,a,h=this._parts;for(i=0,n=0,o=this._rings.length;i<o;i++)for(e=0,s=(a=this._rings[i]).length;e<s-1;e++)(r=It(a[e],a[e+1],t,e,!0))&&(h[n]=h[n]||[],h[n].push(r[0]),r[1]===a[e+1]&&e!==s-2||(h[n].push(r[1]),n++))}},_simplifyPoints:function(){for(var t=this._parts,i=this.options.smoothFactor,e=0,n=t.length;e<n;e++)t[e]=Ct(t[e],i)},_update:function(){this._map&&(this._clipPoints(),this._simplifyPoints(),this._updatePath())},_updatePath:function(){this._renderer._updatePoly(this)},_containsPoint:function(t,i){var e,n,o,s,r,a,h=this._clickTolerance();if(!this._pxBounds||!this._pxBounds.contains(t))return!1;for(e=0,s=this._parts.length;e<s;e++)for(n=0,o=(r=(a=this._parts[e]).length)-1;n<r;o=n++)if((i||0!==n)&&Zt(t,a[o],a[n])<=h)return!0;return!1}});tn._flat=Nt;var en=tn.extend({options:{fill:!0},isEmpty:function(){return!this._latlngs.length||!this._latlngs[0].length},getCenter:function(){if(!this._map)throw new Error("Must add layer to map before using getCenter()");var t,i,e,n,o,s,r,a,h,u=this._rings[0],l=u.length;if(!l)return null;for(s=r=a=0,t=0,i=l-1;t<l;i=t++)e=u[t],n=u[i],o=e.y*n.x-n.y*e.x,r+=(e.x+n.x)*o,a+=(e.y+n.y)*o,s+=3*o;return h=0===s?u[0]:[r/s,a/s],this._map.layerPointToLatLng(h)},_convertLatLngs:function(t){var i=tn.prototype._convertLatLngs.call(this,t),e=i.length;return e>=2&&i[0]instanceof M&&i[0].equals(i[e-1])&&i.pop(),i},_setLatLngs:function(t){tn.prototype._setLatLngs.call(this,t),Dt(this._latlngs)&&(this._latlngs=[this._latlngs])},_defaultShape:function(){return Dt(this._latlngs[0])?this._latlngs[0]:this._latlngs[0][0]},_clipPoints:function(){var t=this._renderer._bounds,i=this.options.weight,e=new x(i,i);if(t=new P(t.min.subtract(e),t.max.add(e)),this._parts=[],this._pxBounds&&this._pxBounds.intersects(t))if(this.options.noClip)this._parts=this._rings;else for(var n,o=0,s=this._rings.length;o<s;o++)(n=jt(this._rings[o],t,!0)).length&&this._parts.push(n)},_updatePath:function(){this._renderer._updatePoly(this,!0)},_containsPoint:function(t){var i,e,n,o,s,r,a,h,u=!1;if(!this._pxBounds.contains(t))return!1;for(o=0,a=this._parts.length;o<a;o++)for(s=0,r=(h=(i=this._parts[o]).length)-1;s<h;r=s++)e=i[s],n=i[r],e.y>t.y!=n.y>t.y&&t.x<(n.x-e.x)*(t.y-e.y)/(n.y-e.y)+e.x&&(u=!u);return u||tn.prototype._containsPoint.call(this,t,!0)}}),nn=qe.extend({initialize:function(t,i){l(this,i),this._layers={},t&&this.addData(t)},addData:function(t){var i,e,n,o=ei(t)?t:t.features;if(o){for(i=0,e=o.length;i<e;i++)((n=o[i]).geometries||n.geometry||n.features||n.coordinates)&&this.addData(n);return this}var s=this.options;if(s.filter&&!s.filter(t))return this;var r=Wt(t,s);return r?(r.feature=Gt(t),r.defaultOptions=r.options,this.resetStyle(r),s.onEachFeature&&s.onEachFeature(t,r),this.addLayer(r)):this},resetStyle:function(t){return t.options=i({},t.defaultOptions),this._setLayerStyle(t,this.options.style),this},setStyle:function(t){return this.eachLayer(function(i){this._setLayerStyle(i,t)},this)},_setLayerStyle:function(t,i){"function"==typeof i&&(i=i(t.feature)),t.setStyle&&t.setStyle(i)}}),on={toGeoJSON:function(t){return qt(this,{type:"Point",coordinates:Ut(this.getLatLng(),t)})}};Xe.include(on),Qe.include(on),$e.include(on),tn.include({toGeoJSON:function(t){var i=!Dt(this._latlngs),e=Vt(this._latlngs,i?1:0,!1,t);return qt(this,{type:(i?"Multi":"")+"LineString",coordinates:e})}}),en.include({toGeoJSON:function(t){var i=!Dt(this._latlngs),e=i&&!Dt(this._latlngs[0]),n=Vt(this._latlngs,e?2:i?1:0,!0,t);return i||(n=[n]),qt(this,{type:(e?"Multi":"")+"Polygon",coordinates:n})}}),Ve.include({toMultiPoint:function(t){var i=[];return this.eachLayer(function(e){i.push(e.toGeoJSON(t).geometry.coordinates)}),qt(this,{type:"MultiPoint",coordinates:i})},toGeoJSON:function(t){var i=this.feature&&this.feature.geometry&&this.feature.geometry.type;if("MultiPoint"===i)return this.toMultiPoint(t);var e="GeometryCollection"===i,n=[];return this.eachLayer(function(i){if(i.toGeoJSON){var o=i.toGeoJSON(t);if(e)n.push(o.geometry);else{var s=Gt(o);"FeatureCollection"===s.type?n.push.apply(n,s.features):n.push(s)}}}),e?qt(this,{geometries:n,type:"GeometryCollection"}):{type:"FeatureCollection",features:n}}});var sn=Kt,rn=Ue.extend({options:{opacity:1,alt:"",interactive:!1,crossOrigin:!1,errorOverlayUrl:"",zIndex:1,className:""},initialize:function(t,i,e){this._url=t,this._bounds=z(i),l(this,e)},onAdd:function(){this._image||(this._initImage(),this.options.opacity<1&&this._updateOpacity()),this.options.interactive&&(pt(this._image,"leaflet-interactive"),this.addInteractiveTarget(this._image)),this.getPane().appendChild(this._image),this._reset()},onRemove:function(){ut(this._image),this.options.interactive&&this.removeInteractiveTarget(this._image)},setOpacity:function(t){return this.options.opacity=t,this._image&&this._updateOpacity(),this},setStyle:function(t){return t.opacity&&this.setOpacity(t.opacity),this},bringToFront:function(){return this._map&&ct(this._image),this},bringToBack:function(){return this._map&&_t(this._image),this},setUrl:function(t){return this._url=t,this._image&&(this._image.src=t),this},setBounds:function(t){return this._bounds=z(t),this._map&&this._reset(),this},getEvents:function(){var t={zoom:this._reset,viewreset:this._reset};return this._zoomAnimated&&(t.zoomanim=this._animateZoom),t},setZIndex:function(t){return this.options.zIndex=t,this._updateZIndex(),this},getBounds:function(){return this._bounds},getElement:function(){return this._image},_initImage:function(){var t="IMG"===this._url.tagName,i=this._image=t?this._url:ht("img");pt(i,"leaflet-image-layer"),this._zoomAnimated&&pt(i,"leaflet-zoom-animated"),this.options.className&&pt(i,this.options.className),i.onselectstart=r,i.onmousemove=r,i.onload=e(this.fire,this,"load"),i.onerror=e(this._overlayOnError,this,"error"),this.options.crossOrigin&&(i.crossOrigin=""),this.options.zIndex&&this._updateZIndex(),t?this._url=i.src:(i.src=this._url,i.alt=this.options.alt)},_animateZoom:function(t){var i=this._map.getZoomScale(t.zoom),e=this._map._latLngBoundsToNewLayerBounds(this._bounds,t.zoom,t.center).min;wt(this._image,e,i)},_reset:function(){var t=this._image,i=new P(this._map.latLngToLayerPoint(this._bounds.getNorthWest()),this._map.latLngToLayerPoint(this._bounds.getSouthEast())),e=i.getSize();Lt(t,i.min),t.style.width=e.x+"px",t.style.height=e.y+"px"},_updateOpacity:function(){vt(this._image,this.options.opacity)},_updateZIndex:function(){this._image&&void 0!==this.options.zIndex&&null!==this.options.zIndex&&(this._image.style.zIndex=this.options.zIndex)},_overlayOnError:function(){this.fire("error");var t=this.options.errorOverlayUrl;t&&this._url!==t&&(this._url=t,this._image.src=t)}}),an=rn.extend({options:{autoplay:!0,loop:!0},_initImage:function(){var t="VIDEO"===this._url.tagName,i=this._image=t?this._url:ht("video");if(pt(i,"leaflet-image-layer"),this._zoomAnimated&&pt(i,"leaflet-zoom-animated"),i.onselectstart=r,i.onmousemove=r,i.onloadeddata=e(this.fire,this,"load"),t){for(var n=i.getElementsByTagName("source"),o=[],s=0;s<n.length;s++)o.push(n[s].src);this._url=n.length>0?o:[i.src]}else{ei(this._url)||(this._url=[this._url]),i.autoplay=!!this.options.autoplay,i.loop=!!this.options.loop;for(var a=0;a<this._url.length;a++){var h=ht("source");h.src=this._url[a],i.appendChild(h)}}}}),hn=Ue.extend({options:{offset:[0,7],className:"",pane:"popupPane"},initialize:function(t,i){l(this,t),this._source=i},onAdd:function(t){this._zoomAnimated=t._zoomAnimated,this._container||this._initLayout(),t._fadeAnimated&&vt(this._container,0),clearTimeout(this._removeTimeout),this.getPane().appendChild(this._container),this.update(),t._fadeAnimated&&vt(this._container,1),this.bringToFront()},onRemove:function(t){t._fadeAnimated?(vt(this._container,0),this._removeTimeout=setTimeout(e(ut,void 0,this._container),200)):ut(this._container)},getLatLng:function(){return this._latlng},setLatLng:function(t){return this._latlng=C(t),this._map&&(this._updatePosition(),this._adjustPan()),this},getContent:function(){return this._content},setContent:function(t){return this._content=t,this.update(),this},getElement:function(){return this._container},update:function(){this._map&&(this._container.style.visibility="hidden",this._updateContent(),this._updateLayout(),this._updatePosition(),this._container.style.visibility="",this._adjustPan())},getEvents:function(){var t={zoom:this._updatePosition,viewreset:this._updatePosition};return this._zoomAnimated&&(t.zoomanim=this._animateZoom),t},isOpen:function(){return!!this._map&&this._map.hasLayer(this)},bringToFront:function(){return this._map&&ct(this._container),this},bringToBack:function(){return this._map&&_t(this._container),this},_updateContent:function(){if(this._content){var t=this._contentNode,i="function"==typeof this._content?this._content(this._source||this):this._content;if("string"==typeof i)t.innerHTML=i;else{for(;t.hasChildNodes();)t.removeChild(t.firstChild);t.appendChild(i)}this.fire("contentupdate")}},_updatePosition:function(){if(this._map){var t=this._map.latLngToLayerPoint(this._latlng),i=w(this.options.offset),e=this._getAnchor();this._zoomAnimated?Lt(this._container,t.add(e)):i=i.add(t).add(e);var n=this._containerBottom=-i.y,o=this._containerLeft=-Math.round(this._containerWidth/2)+i.x;this._container.style.bottom=n+"px",this._container.style.left=o+"px"}},_getAnchor:function(){return[0,0]}}),un=hn.extend({options:{maxWidth:300,minWidth:50,maxHeight:null,autoPan:!0,autoPanPaddingTopLeft:null,autoPanPaddingBottomRight:null,autoPanPadding:[5,5],keepInView:!1,closeButton:!0,autoClose:!0,closeOnEscapeKey:!0,className:""},openOn:function(t){return t.openPopup(this),this},onAdd:function(t){hn.prototype.onAdd.call(this,t),t.fire("popupopen",{popup:this}),this._source&&(this._source.fire("popupopen",{popup:this},!0),this._source instanceof Je||this._source.on("preclick",Y))},onRemove:function(t){hn.prototype.onRemove.call(this,t),t.fire("popupclose",{popup:this}),this._source&&(this._source.fire("popupclose",{popup:this},!0),this._source instanceof Je||this._source.off("preclick",Y))},getEvents:function(){var t=hn.prototype.getEvents.call(this);return(void 0!==this.options.closeOnClick?this.options.closeOnClick:this._map.options.closePopupOnClick)&&(t.preclick=this._close),this.options.keepInView&&(t.moveend=this._adjustPan),t},_close:function(){this._map&&this._map.closePopup(this)},_initLayout:function(){var t="leaflet-popup",i=this._container=ht("div",t+" "+(this.options.className||"")+" leaflet-zoom-animated"),e=this._wrapper=ht("div",t+"-content-wrapper",i);if(this._contentNode=ht("div",t+"-content",e),J(e),X(this._contentNode),V(e,"contextmenu",Y),this._tipContainer=ht("div",t+"-tip-container",i),this._tip=ht("div",t+"-tip",this._tipContainer),this.options.closeButton){var n=this._closeButton=ht("a",t+"-close-button",i);n.href="#close",n.innerHTML="&#215;",V(n,"click",this._onCloseButtonClick,this)}},_updateLayout:function(){var t=this._contentNode,i=t.style;i.width="",i.whiteSpace="nowrap";var e=t.offsetWidth;e=Math.min(e,this.options.maxWidth),e=Math.max(e,this.options.minWidth),i.width=e+1+"px",i.whiteSpace="",i.height="";var n=t.offsetHeight,o=this.options.maxHeight;o&&n>o?(i.height=o+"px",pt(t,"leaflet-popup-scrolled")):mt(t,"leaflet-popup-scrolled"),this._containerWidth=this._container.offsetWidth},_animateZoom:function(t){var i=this._map._latLngToNewLayerPoint(this._latlng,t.zoom,t.center),e=this._getAnchor();Lt(this._container,i.add(e))},_adjustPan:function(){if(!(!this.options.autoPan||this._map._panAnim&&this._map._panAnim._inProgress)){var t=this._map,i=parseInt(at(this._container,"marginBottom"),10)||0,e=this._container.offsetHeight+i,n=this._containerWidth,o=new x(this._containerLeft,-e-this._containerBottom);o._add(Pt(this._container));var s=t.layerPointToContainerPoint(o),r=w(this.options.autoPanPadding),a=w(this.options.autoPanPaddingTopLeft||r),h=w(this.options.autoPanPaddingBottomRight||r),u=t.getSize(),l=0,c=0;s.x+n+h.x>u.x&&(l=s.x+n-u.x+h.x),s.x-l-a.x<0&&(l=s.x-a.x),s.y+e+h.y>u.y&&(c=s.y+e-u.y+h.y),s.y-c-a.y<0&&(c=s.y-a.y),(l||c)&&t.fire("autopanstart").panBy([l,c])}},_onCloseButtonClick:function(t){this._close(),Q(t)},_getAnchor:function(){return w(this._source&&this._source._getPopupAnchor?this._source._getPopupAnchor():[0,0])}});Le.mergeOptions({closePopupOnClick:!0}),Le.include({openPopup:function(t,i,e){return t instanceof un||(t=new un(e).setContent(t)),i&&t.setLatLng(i),this.hasLayer(t)?this:(this._popup&&this._popup.options.autoClose&&this.closePopup(),this._popup=t,this.addLayer(t))},closePopup:function(t){return t&&t!==this._popup||(t=this._popup,this._popup=null),t&&this.removeLayer(t),this}}),Ue.include({bindPopup:function(t,i){return t instanceof un?(l(t,i),this._popup=t,t._source=this):(this._popup&&!i||(this._popup=new un(i,this)),this._popup.setContent(t)),this._popupHandlersAdded||(this.on({click:this._openPopup,keypress:this._onKeyPress,remove:this.closePopup,move:this._movePopup}),this._popupHandlersAdded=!0),this},unbindPopup:function(){return this._popup&&(this.off({click:this._openPopup,keypress:this._onKeyPress,remove:this.closePopup,move:this._movePopup}),this._popupHandlersAdded=!1,this._popup=null),this},openPopup:function(t,i){if(t instanceof Ue||(i=t,t=this),t instanceof qe)for(var e in this._layers){t=this._layers[e];break}return i||(i=t.getCenter?t.getCenter():t.getLatLng()),this._popup&&this._map&&(this._popup._source=t,this._popup.update(),this._map.openPopup(this._popup,i)),this},closePopup:function(){return this._popup&&this._popup._close(),this},togglePopup:function(t){return this._popup&&(this._popup._map?this.closePopup():this.openPopup(t)),this},isPopupOpen:function(){return!!this._popup&&this._popup.isOpen()},setPopupContent:function(t){return this._popup&&this._popup.setContent(t),this},getPopup:function(){return this._popup},_openPopup:function(t){var i=t.layer||t.target;this._popup&&this._map&&(Q(t),i instanceof Je?this.openPopup(t.layer||t.target,t.latlng):this._map.hasLayer(this._popup)&&this._popup._source===i?this.closePopup():this.openPopup(i,t.latlng))},_movePopup:function(t){this._popup.setLatLng(t.latlng)},_onKeyPress:function(t){13===t.originalEvent.keyCode&&this._openPopup(t)}});var ln=hn.extend({options:{pane:"tooltipPane",offset:[0,0],direction:"auto",permanent:!1,sticky:!1,interactive:!1,opacity:.9},onAdd:function(t){hn.prototype.onAdd.call(this,t),this.setOpacity(this.options.opacity),t.fire("tooltipopen",{tooltip:this}),this._source&&this._source.fire("tooltipopen",{tooltip:this},!0)},onRemove:function(t){hn.prototype.onRemove.call(this,t),t.fire("tooltipclose",{tooltip:this}),this._source&&this._source.fire("tooltipclose",{tooltip:this},!0)},getEvents:function(){var t=hn.prototype.getEvents.call(this);return Vi&&!this.options.permanent&&(t.preclick=this._close),t},_close:function(){this._map&&this._map.closeTooltip(this)},_initLayout:function(){var t="leaflet-tooltip "+(this.options.className||"")+" leaflet-zoom-"+(this._zoomAnimated?"animated":"hide");this._contentNode=this._container=ht("div",t)},_updateLayout:function(){},_adjustPan:function(){},_setPosition:function(t){var i=this._map,e=this._container,n=i.latLngToContainerPoint(i.getCenter()),o=i.layerPointToContainerPoint(t),s=this.options.direction,r=e.offsetWidth,a=e.offsetHeight,h=w(this.options.offset),u=this._getAnchor();"top"===s?t=t.add(w(-r/2+h.x,-a+h.y+u.y,!0)):"bottom"===s?t=t.subtract(w(r/2-h.x,-h.y,!0)):"center"===s?t=t.subtract(w(r/2+h.x,a/2-u.y+h.y,!0)):"right"===s||"auto"===s&&o.x<n.x?(s="right",t=t.add(w(h.x+u.x,u.y-a/2+h.y,!0))):(s="left",t=t.subtract(w(r+u.x-h.x,a/2-u.y-h.y,!0))),mt(e,"leaflet-tooltip-right"),mt(e,"leaflet-tooltip-left"),mt(e,"leaflet-tooltip-top"),mt(e,"leaflet-tooltip-bottom"),pt(e,"leaflet-tooltip-"+s),Lt(e,t)},_updatePosition:function(){var t=this._map.latLngToLayerPoint(this._latlng);this._setPosition(t)},setOpacity:function(t){this.options.opacity=t,this._container&&vt(this._container,t)},_animateZoom:function(t){var i=this._map._latLngToNewLayerPoint(this._latlng,t.zoom,t.center);this._setPosition(i)},_getAnchor:function(){return w(this._source&&this._source._getTooltipAnchor&&!this.options.sticky?this._source._getTooltipAnchor():[0,0])}});Le.include({openTooltip:function(t,i,e){return t instanceof ln||(t=new ln(e).setContent(t)),i&&t.setLatLng(i),this.hasLayer(t)?this:this.addLayer(t)},closeTooltip:function(t){return t&&this.removeLayer(t),this}}),Ue.include({bindTooltip:function(t,i){return t instanceof ln?(l(t,i),this._tooltip=t,t._source=this):(this._tooltip&&!i||(this._tooltip=new ln(i,this)),this._tooltip.setContent(t)),this._initTooltipInteractions(),this._tooltip.options.permanent&&this._map&&this._map.hasLayer(this)&&this.openTooltip(),this},unbindTooltip:function(){return this._tooltip&&(this._initTooltipInteractions(!0),this.closeTooltip(),this._tooltip=null),this},_initTooltipInteractions:function(t){if(t||!this._tooltipHandlersAdded){var i=t?"off":"on",e={remove:this.closeTooltip,move:this._moveTooltip};this._tooltip.options.permanent?e.add=this._openTooltip:(e.mouseover=this._openTooltip,e.mouseout=this.closeTooltip,this._tooltip.options.sticky&&(e.mousemove=this._moveTooltip),Vi&&(e.click=this._openTooltip)),this[i](e),this._tooltipHandlersAdded=!t}},openTooltip:function(t,i){if(t instanceof Ue||(i=t,t=this),t instanceof qe)for(var e in this._layers){t=this._layers[e];break}return i||(i=t.getCenter?t.getCenter():t.getLatLng()),this._tooltip&&this._map&&(this._tooltip._source=t,this._tooltip.update(),this._map.openTooltip(this._tooltip,i),this._tooltip.options.interactive&&this._tooltip._container&&(pt(this._tooltip._container,"leaflet-clickable"),this.addInteractiveTarget(this._tooltip._container))),this},closeTooltip:function(){return this._tooltip&&(this._tooltip._close(),this._tooltip.options.interactive&&this._tooltip._container&&(mt(this._tooltip._container,"leaflet-clickable"),this.removeInteractiveTarget(this._tooltip._container))),this},toggleTooltip:function(t){return this._tooltip&&(this._tooltip._map?this.closeTooltip():this.openTooltip(t)),this},isTooltipOpen:function(){return this._tooltip.isOpen()},setTooltipContent:function(t){return this._tooltip&&this._tooltip.setContent(t),this},getTooltip:function(){return this._tooltip},_openTooltip:function(t){var i=t.layer||t.target;this._tooltip&&this._map&&this.openTooltip(i,this._tooltip.options.sticky?t.latlng:void 0)},_moveTooltip:function(t){var i,e,n=t.latlng;this._tooltip.options.sticky&&t.originalEvent&&(i=this._map.mouseEventToContainerPoint(t.originalEvent),e=this._map.containerPointToLayerPoint(i),n=this._map.layerPointToLatLng(e)),this._tooltip.setLatLng(n)}});var cn=Ge.extend({options:{iconSize:[12,12],html:!1,bgPos:null,className:"leaflet-div-icon"},createIcon:function(t){var i=t&&"DIV"===t.tagName?t:document.createElement("div"),e=this.options;if(i.innerHTML=!1!==e.html?e.html:"",e.bgPos){var n=w(e.bgPos);i.style.backgroundPosition=-n.x+"px "+-n.y+"px"}return this._setIconStyles(i,"icon"),i},createShadow:function(){return null}});Ge.Default=Ke;var _n=Ue.extend({options:{tileSize:256,opacity:1,updateWhenIdle:ji,updateWhenZooming:!0,updateInterval:200,zIndex:1,bounds:null,minZoom:0,maxZoom:void 0,maxNativeZoom:void 0,minNativeZoom:void 0,noWrap:!1,pane:"tilePane",className:"",keepBuffer:2},initialize:function(t){l(this,t)},onAdd:function(){this._initContainer(),this._levels={},this._tiles={},this._resetView(),this._update()},beforeAdd:function(t){t._addZoomLimit(this)},onRemove:function(t){this._removeAllTiles(),ut(this._container),t._removeZoomLimit(this),this._container=null,this._tileZoom=void 0},bringToFront:function(){return this._map&&(ct(this._container),this._setAutoZIndex(Math.max)),this},bringToBack:function(){return this._map&&(_t(this._container),this._setAutoZIndex(Math.min)),this},getContainer:function(){return this._container},setOpacity:function(t){return this.options.opacity=t,this._updateOpacity(),this},setZIndex:function(t){return this.options.zIndex=t,this._updateZIndex(),this},isLoading:function(){return this._loading},redraw:function(){return this._map&&(this._removeAllTiles(),this._update()),this},getEvents:function(){var t={viewprereset:this._invalidateAll,viewreset:this._resetView,zoom:this._resetView,moveend:this._onMoveEnd};return this.options.updateWhenIdle||(this._onMove||(this._onMove=o(this._onMoveEnd,this.options.updateInterval,this)),t.move=this._onMove),this._zoomAnimated&&(t.zoomanim=this._animateZoom),t},createTile:function(){return document.createElement("div")},getTileSize:function(){var t=this.options.tileSize;return t instanceof x?t:new x(t,t)},_updateZIndex:function(){this._container&&void 0!==this.options.zIndex&&null!==this.options.zIndex&&(this._container.style.zIndex=this.options.zIndex)},_setAutoZIndex:function(t){for(var i,e=this.getPane().children,n=-t(-1/0,1/0),o=0,s=e.length;o<s;o++)i=e[o].style.zIndex,e[o]!==this._container&&i&&(n=t(n,+i));isFinite(n)&&(this.options.zIndex=n+t(-1,1),this._updateZIndex())},_updateOpacity:function(){if(this._map&&!Li){vt(this._container,this.options.opacity);var t=+new Date,i=!1,e=!1;for(var n in this._tiles){var o=this._tiles[n];if(o.current&&o.loaded){var s=Math.min(1,(t-o.loaded)/200);vt(o.el,s),s<1?i=!0:(o.active?e=!0:this._onOpaqueTile(o),o.active=!0)}}e&&!this._noPrune&&this._pruneTiles(),i&&(g(this._fadeFrame),this._fadeFrame=f(this._updateOpacity,this))}},_onOpaqueTile:r,_initContainer:function(){this._container||(this._container=ht("div","leaflet-layer "+(this.options.className||"")),this._updateZIndex(),this.options.opacity<1&&this._updateOpacity(),this.getPane().appendChild(this._container))},_updateLevels:function(){var t=this._tileZoom,i=this.options.maxZoom;if(void 0!==t){for(var e in this._levels)this._levels[e].el.children.length||e===t?(this._levels[e].el.style.zIndex=i-Math.abs(t-e),this._onUpdateLevel(e)):(ut(this._levels[e].el),this._removeTilesAtZoom(e),this._onRemoveLevel(e),delete this._levels[e]);var n=this._levels[t],o=this._map;return n||((n=this._levels[t]={}).el=ht("div","leaflet-tile-container leaflet-zoom-animated",this._container),n.el.style.zIndex=i,n.origin=o.project(o.unproject(o.getPixelOrigin()),t).round(),n.zoom=t,this._setZoomTransform(n,o.getCenter(),o.getZoom()),n.el.offsetWidth,this._onCreateLevel(n)),this._level=n,n}},_onUpdateLevel:r,_onRemoveLevel:r,_onCreateLevel:r,_pruneTiles:function(){if(this._map){var t,i,e=this._map.getZoom();if(e>this.options.maxZoom||e<this.options.minZoom)this._removeAllTiles();else{for(t in this._tiles)(i=this._tiles[t]).retain=i.current;for(t in this._tiles)if((i=this._tiles[t]).current&&!i.active){var n=i.coords;this._retainParent(n.x,n.y,n.z,n.z-5)||this._retainChildren(n.x,n.y,n.z,n.z+2)}for(t in this._tiles)this._tiles[t].retain||this._removeTile(t)}}},_removeTilesAtZoom:function(t){for(var i in this._tiles)this._tiles[i].coords.z===t&&this._removeTile(i)},_removeAllTiles:function(){for(var t in this._tiles)this._removeTile(t)},_invalidateAll:function(){for(var t in this._levels)ut(this._levels[t].el),this._onRemoveLevel(t),delete this._levels[t];this._removeAllTiles(),this._tileZoom=void 0},_retainParent:function(t,i,e,n){var o=Math.floor(t/2),s=Math.floor(i/2),r=e-1,a=new x(+o,+s);a.z=+r;var h=this._tileCoordsToKey(a),u=this._tiles[h];return u&&u.active?(u.retain=!0,!0):(u&&u.loaded&&(u.retain=!0),r>n&&this._retainParent(o,s,r,n))},_retainChildren:function(t,i,e,n){for(var o=2*t;o<2*t+2;o++)for(var s=2*i;s<2*i+2;s++){var r=new x(o,s);r.z=e+1;var a=this._tileCoordsToKey(r),h=this._tiles[a];h&&h.active?h.retain=!0:(h&&h.loaded&&(h.retain=!0),e+1<n&&this._retainChildren(o,s,e+1,n))}},_resetView:function(t){var i=t&&(t.pinch||t.flyTo);this._setView(this._map.getCenter(),this._map.getZoom(),i,i)},_animateZoom:function(t){this._setView(t.center,t.zoom,!0,t.noUpdate)},_clampZoom:function(t){var i=this.options;return void 0!==i.minNativeZoom&&t<i.minNativeZoom?i.minNativeZoom:void 0!==i.maxNativeZoom&&i.maxNativeZoom<t?i.maxNativeZoom:t},_setView:function(t,i,e,n){var o=this._clampZoom(Math.round(i));(void 0!==this.options.maxZoom&&o>this.options.maxZoom||void 0!==this.options.minZoom&&o<this.options.minZoom)&&(o=void 0);var s=this.options.updateWhenZooming&&o!==this._tileZoom;n&&!s||(this._tileZoom=o,this._abortLoading&&this._abortLoading(),this._updateLevels(),this._resetGrid(),void 0!==o&&this._update(t),e||this._pruneTiles(),this._noPrune=!!e),this._setZoomTransforms(t,i)},_setZoomTransforms:function(t,i){for(var e in this._levels)this._setZoomTransform(this._levels[e],t,i)},_setZoomTransform:function(t,i,e){var n=this._map.getZoomScale(e,t.zoom),o=t.origin.multiplyBy(n).subtract(this._map._getNewPixelOrigin(i,e)).round();Ni?wt(t.el,o,n):Lt(t.el,o)},_resetGrid:function(){var t=this._map,i=t.options.crs,e=this._tileSize=this.getTileSize(),n=this._tileZoom,o=this._map.getPixelWorldBounds(this._tileZoom);o&&(this._globalTileRange=this._pxBoundsToTileRange(o)),this._wrapX=i.wrapLng&&!this.options.noWrap&&[Math.floor(t.project([0,i.wrapLng[0]],n).x/e.x),Math.ceil(t.project([0,i.wrapLng[1]],n).x/e.y)],this._wrapY=i.wrapLat&&!this.options.noWrap&&[Math.floor(t.project([i.wrapLat[0],0],n).y/e.x),Math.ceil(t.project([i.wrapLat[1],0],n).y/e.y)]},_onMoveEnd:function(){this._map&&!this._map._animatingZoom&&this._update()},_getTiledPixelBounds:function(t){var i=this._map,e=i._animatingZoom?Math.max(i._animateToZoom,i.getZoom()):i.getZoom(),n=i.getZoomScale(e,this._tileZoom),o=i.project(t,this._tileZoom).floor(),s=i.getSize().divideBy(2*n);return new P(o.subtract(s),o.add(s))},_update:function(t){var i=this._map;if(i){var e=this._clampZoom(i.getZoom());if(void 0===t&&(t=i.getCenter()),void 0!==this._tileZoom){var n=this._getTiledPixelBounds(t),o=this._pxBoundsToTileRange(n),s=o.getCenter(),r=[],a=this.options.keepBuffer,h=new P(o.getBottomLeft().subtract([a,-a]),o.getTopRight().add([a,-a]));if(!(isFinite(o.min.x)&&isFinite(o.min.y)&&isFinite(o.max.x)&&isFinite(o.max.y)))throw new Error("Attempted to load an infinite number of tiles");for(var u in this._tiles){var l=this._tiles[u].coords;l.z===this._tileZoom&&h.contains(new x(l.x,l.y))||(this._tiles[u].current=!1)}if(Math.abs(e-this._tileZoom)>1)this._setView(t,e);else{for(var c=o.min.y;c<=o.max.y;c++)for(var _=o.min.x;_<=o.max.x;_++){var d=new x(_,c);if(d.z=this._tileZoom,this._isValidTile(d)){var p=this._tiles[this._tileCoordsToKey(d)];p?p.current=!0:r.push(d)}}if(r.sort(function(t,i){return t.distanceTo(s)-i.distanceTo(s)}),0!==r.length){this._loading||(this._loading=!0,this.fire("loading"));var m=document.createDocumentFragment();for(_=0;_<r.length;_++)this._addTile(r[_],m);this._level.el.appendChild(m)}}}}},_isValidTile:function(t){var i=this._map.options.crs;if(!i.infinite){var e=this._globalTileRange;if(!i.wrapLng&&(t.x<e.min.x||t.x>e.max.x)||!i.wrapLat&&(t.y<e.min.y||t.y>e.max.y))return!1}if(!this.options.bounds)return!0;var n=this._tileCoordsToBounds(t);return z(this.options.bounds).overlaps(n)},_keyToBounds:function(t){return this._tileCoordsToBounds(this._keyToTileCoords(t))},_tileCoordsToNwSe:function(t){var i=this._map,e=this.getTileSize(),n=t.scaleBy(e),o=n.add(e);return[i.unproject(n,t.z),i.unproject(o,t.z)]},_tileCoordsToBounds:function(t){var i=this._tileCoordsToNwSe(t),e=new T(i[0],i[1]);return this.options.noWrap||(e=this._map.wrapLatLngBounds(e)),e},_tileCoordsToKey:function(t){return t.x+":"+t.y+":"+t.z},_keyToTileCoords:function(t){var i=t.split(":"),e=new x(+i[0],+i[1]);return e.z=+i[2],e},_removeTile:function(t){var i=this._tiles[t];i&&(Ci||i.el.setAttribute("src",ni),ut(i.el),delete this._tiles[t],this.fire("tileunload",{tile:i.el,coords:this._keyToTileCoords(t)}))},_initTile:function(t){pt(t,"leaflet-tile");var i=this.getTileSize();t.style.width=i.x+"px",t.style.height=i.y+"px",t.onselectstart=r,t.onmousemove=r,Li&&this.options.opacity<1&&vt(t,this.options.opacity),Ti&&!zi&&(t.style.WebkitBackfaceVisibility="hidden")},_addTile:function(t,i){var n=this._getTilePos(t),o=this._tileCoordsToKey(t),s=this.createTile(this._wrapCoords(t),e(this._tileReady,this,t));this._initTile(s),this.createTile.length<2&&f(e(this._tileReady,this,t,null,s)),Lt(s,n),this._tiles[o]={el:s,coords:t,current:!0},i.appendChild(s),this.fire("tileloadstart",{tile:s,coords:t})},_tileReady:function(t,i,n){if(this._map){i&&this.fire("tileerror",{error:i,tile:n,coords:t});var o=this._tileCoordsToKey(t);(n=this._tiles[o])&&(n.loaded=+new Date,this._map._fadeAnimated?(vt(n.el,0),g(this._fadeFrame),this._fadeFrame=f(this._updateOpacity,this)):(n.active=!0,this._pruneTiles()),i||(pt(n.el,"leaflet-tile-loaded"),this.fire("tileload",{tile:n.el,coords:t})),this._noTilesToLoad()&&(this._loading=!1,this.fire("load"),Li||!this._map._fadeAnimated?f(this._pruneTiles,this):setTimeout(e(this._pruneTiles,this),250)))}},_getTilePos:function(t){return t.scaleBy(this.getTileSize()).subtract(this._level.origin)},_wrapCoords:function(t){var i=new x(this._wrapX?s(t.x,this._wrapX):t.x,this._wrapY?s(t.y,this._wrapY):t.y);return i.z=t.z,i},_pxBoundsToTileRange:function(t){var i=this.getTileSize();return new P(t.min.unscaleBy(i).floor(),t.max.unscaleBy(i).ceil().subtract([1,1]))},_noTilesToLoad:function(){for(var t in this._tiles)if(!this._tiles[t].loaded)return!1;return!0}}),dn=_n.extend({options:{minZoom:0,maxZoom:18,subdomains:"abc",errorTileUrl:"",zoomOffset:0,tms:!1,zoomReverse:!1,detectRetina:!1,crossOrigin:!1},initialize:function(t,i){this._url=t,(i=l(this,i)).detectRetina&&Ki&&i.maxZoom>0&&(i.tileSize=Math.floor(i.tileSize/2),i.zoomReverse?(i.zoomOffset--,i.minZoom++):(i.zoomOffset++,i.maxZoom--),i.minZoom=Math.max(0,i.minZoom)),"string"==typeof i.subdomains&&(i.subdomains=i.subdomains.split("")),Ti||this.on("tileunload",this._onTileRemove)},setUrl:function(t,i){return this._url=t,i||this.redraw(),this},createTile:function(t,i){var n=document.createElement("img");return V(n,"load",e(this._tileOnLoad,this,i,n)),V(n,"error",e(this._tileOnError,this,i,n)),this.options.crossOrigin&&(n.crossOrigin=""),n.alt="",n.setAttribute("role","presentation"),n.src=this.getTileUrl(t),n},getTileUrl:function(t){var e={r:Ki?"@2x":"",s:this._getSubdomain(t),x:t.x,y:t.y,z:this._getZoomForUrl()};if(this._map&&!this._map.options.crs.infinite){var n=this._globalTileRange.max.y-t.y;this.options.tms&&(e.y=n),e["-y"]=n}return _(this._url,i(e,this.options))},_tileOnLoad:function(t,i){Li?setTimeout(e(t,this,null,i),0):t(null,i)},_tileOnError:function(t,i,e){var n=this.options.errorTileUrl;n&&i.getAttribute("src")!==n&&(i.src=n),t(e,i)},_onTileRemove:function(t){t.tile.onload=null},_getZoomForUrl:function(){var t=this._tileZoom,i=this.options.maxZoom,e=this.options.zoomReverse,n=this.options.zoomOffset;return e&&(t=i-t),t+n},_getSubdomain:function(t){var i=Math.abs(t.x+t.y)%this.options.subdomains.length;return this.options.subdomains[i]},_abortLoading:function(){var t,i;for(t in this._tiles)this._tiles[t].coords.z!==this._tileZoom&&((i=this._tiles[t].el).onload=r,i.onerror=r,i.complete||(i.src=ni,ut(i),delete this._tiles[t]))}}),pn=dn.extend({defaultWmsParams:{service:"WMS",request:"GetMap",layers:"",styles:"",format:"image/jpeg",transparent:!1,version:"1.1.1"},options:{crs:null,uppercase:!1},initialize:function(t,e){this._url=t;var n=i({},this.defaultWmsParams);for(var o in e)o in this.options||(n[o]=e[o]);var s=(e=l(this,e)).detectRetina&&Ki?2:1,r=this.getTileSize();n.width=r.x*s,n.height=r.y*s,this.wmsParams=n},onAdd:function(t){this._crs=this.options.crs||t.options.crs,this._wmsVersion=parseFloat(this.wmsParams.version);var i=this._wmsVersion>=1.3?"crs":"srs";this.wmsParams[i]=this._crs.code,dn.prototype.onAdd.call(this,t)},getTileUrl:function(t){var i=this._tileCoordsToNwSe(t),e=this._crs,n=b(e.project(i[0]),e.project(i[1])),o=n.min,s=n.max,r=(this._wmsVersion>=1.3&&this._crs===He?[o.y,o.x,s.y,s.x]:[o.x,o.y,s.x,s.y]).join(","),a=L.TileLayer.prototype.getTileUrl.call(this,t);return a+c(this.wmsParams,a,this.options.uppercase)+(this.options.uppercase?"&BBOX=":"&bbox=")+r},setParams:function(t,e){return i(this.wmsParams,t),e||this.redraw(),this}});dn.WMS=pn,Yt.wms=function(t,i){return new pn(t,i)};var mn=Ue.extend({options:{padding:.1,tolerance:0},initialize:function(t){l(this,t),n(this),this._layers=this._layers||{}},onAdd:function(){this._container||(this._initContainer(),this._zoomAnimated&&pt(this._container,"leaflet-zoom-animated")),this.getPane().appendChild(this._container),this._update(),this.on("update",this._updatePaths,this)},onRemove:function(){this.off("update",this._updatePaths,this),this._destroyContainer()},getEvents:function(){var t={viewreset:this._reset,zoom:this._onZoom,moveend:this._update,zoomend:this._onZoomEnd};return this._zoomAnimated&&(t.zoomanim=this._onAnimZoom),t},_onAnimZoom:function(t){this._updateTransform(t.center,t.zoom)},_onZoom:function(){this._updateTransform(this._map.getCenter(),this._map.getZoom())},_updateTransform:function(t,i){var e=this._map.getZoomScale(i,this._zoom),n=Pt(this._container),o=this._map.getSize().multiplyBy(.5+this.options.padding),s=this._map.project(this._center,i),r=this._map.project(t,i).subtract(s),a=o.multiplyBy(-e).add(n).add(o).subtract(r);Ni?wt(this._container,a,e):Lt(this._container,a)},_reset:function(){this._update(),this._updateTransform(this._center,this._zoom);for(var t in this._layers)this._layers[t]._reset()},_onZoomEnd:function(){for(var t in this._layers)this._layers[t]._project()},_updatePaths:function(){for(var t in this._layers)this._layers[t]._update()},_update:function(){var t=this.options.padding,i=this._map.getSize(),e=this._map.containerPointToLayerPoint(i.multiplyBy(-t)).round();this._bounds=new P(e,e.add(i.multiplyBy(1+2*t)).round()),this._center=this._map.getCenter(),this._zoom=this._map.getZoom()}}),fn=mn.extend({getEvents:function(){var t=mn.prototype.getEvents.call(this);return t.viewprereset=this._onViewPreReset,t},_onViewPreReset:function(){this._postponeUpdatePaths=!0},onAdd:function(){mn.prototype.onAdd.call(this),this._draw()},_initContainer:function(){var t=this._container=document.createElement("canvas");V(t,"mousemove",o(this._onMouseMove,32,this),this),V(t,"click dblclick mousedown mouseup contextmenu",this._onClick,this),V(t,"mouseout",this._handleMouseOut,this),this._ctx=t.getContext("2d")},_destroyContainer:function(){delete this._ctx,ut(this._container),q(this._container),delete this._container},_updatePaths:function(){if(!this._postponeUpdatePaths){this._redrawBounds=null;for(var t in this._layers)this._layers[t]._update();this._redraw()}},_update:function(){if(!this._map._animatingZoom||!this._bounds){this._drawnLayers={},mn.prototype._update.call(this);var t=this._bounds,i=this._container,e=t.getSize(),n=Ki?2:1;Lt(i,t.min),i.width=n*e.x,i.height=n*e.y,i.style.width=e.x+"px",i.style.height=e.y+"px",Ki&&this._ctx.scale(2,2),this._ctx.translate(-t.min.x,-t.min.y),this.fire("update")}},_reset:function(){mn.prototype._reset.call(this),this._postponeUpdatePaths&&(this._postponeUpdatePaths=!1,this._updatePaths())},_initPath:function(t){this._updateDashArray(t),this._layers[n(t)]=t;var i=t._order={layer:t,prev:this._drawLast,next:null};this._drawLast&&(this._drawLast.next=i),this._drawLast=i,this._drawFirst=this._drawFirst||this._drawLast},_addPath:function(t){this._requestRedraw(t)},_removePath:function(t){var i=t._order,e=i.next,n=i.prev;e?e.prev=n:this._drawLast=n,n?n.next=e:this._drawFirst=e,delete t._order,delete this._layers[L.stamp(t)],this._requestRedraw(t)},_updatePath:function(t){this._extendRedrawBounds(t),t._project(),t._update(),this._requestRedraw(t)},_updateStyle:function(t){this._updateDashArray(t),this._requestRedraw(t)},_updateDashArray:function(t){if(t.options.dashArray){var i,e=t.options.dashArray.split(","),n=[];for(i=0;i<e.length;i++)n.push(Number(e[i]));t.options._dashArray=n}},_requestRedraw:function(t){this._map&&(this._extendRedrawBounds(t),this._redrawRequest=this._redrawRequest||f(this._redraw,this))},_extendRedrawBounds:function(t){if(t._pxBounds){var i=(t.options.weight||0)+1;this._redrawBounds=this._redrawBounds||new P,this._redrawBounds.extend(t._pxBounds.min.subtract([i,i])),this._redrawBounds.extend(t._pxBounds.max.add([i,i]))}},_redraw:function(){this._redrawRequest=null,this._redrawBounds&&(this._redrawBounds.min._floor(),this._redrawBounds.max._ceil()),this._clear(),this._draw(),this._redrawBounds=null},_clear:function(){var t=this._redrawBounds;if(t){var i=t.getSize();this._ctx.clearRect(t.min.x,t.min.y,i.x,i.y)}else this._ctx.clearRect(0,0,this._container.width,this._container.height)},_draw:function(){var t,i=this._redrawBounds;if(this._ctx.save(),i){var e=i.getSize();this._ctx.beginPath(),this._ctx.rect(i.min.x,i.min.y,e.x,e.y),this._ctx.clip()}this._drawing=!0;for(var n=this._drawFirst;n;n=n.next)t=n.layer,(!i||t._pxBounds&&t._pxBounds.intersects(i))&&t._updatePath();this._drawing=!1,this._ctx.restore()},_updatePoly:function(t,i){if(this._drawing){var e,n,o,s,r=t._parts,a=r.length,h=this._ctx;if(a){for(this._drawnLayers[t._leaflet_id]=t,h.beginPath(),e=0;e<a;e++){for(n=0,o=r[e].length;n<o;n++)s=r[e][n],h[n?"lineTo":"moveTo"](s.x,s.y);i&&h.closePath()}this._fillStroke(h,t)}}},_updateCircle:function(t){if(this._drawing&&!t._empty()){var i=t._point,e=this._ctx,n=Math.max(Math.round(t._radius),1),o=(Math.max(Math.round(t._radiusY),1)||n)/n;this._drawnLayers[t._leaflet_id]=t,1!==o&&(e.save(),e.scale(1,o)),e.beginPath(),e.arc(i.x,i.y/o,n,0,2*Math.PI,!1),1!==o&&e.restore(),this._fillStroke(e,t)}},_fillStroke:function(t,i){var e=i.options;e.fill&&(t.globalAlpha=e.fillOpacity,t.fillStyle=e.fillColor||e.color,t.fill(e.fillRule||"evenodd")),e.stroke&&0!==e.weight&&(t.setLineDash&&t.setLineDash(i.options&&i.options._dashArray||[]),t.globalAlpha=e.opacity,t.lineWidth=e.weight,t.strokeStyle=e.color,t.lineCap=e.lineCap,t.lineJoin=e.lineJoin,t.stroke())},_onClick:function(t){for(var i,e,n=this._map.mouseEventToLayerPoint(t),o=this._drawFirst;o;o=o.next)(i=o.layer).options.interactive&&i._containsPoint(n)&&!this._map._draggableMoved(i)&&(e=i);e&&(et(t),this._fireEvent([e],t))},_onMouseMove:function(t){if(this._map&&!this._map.dragging.moving()&&!this._map._animatingZoom){var i=this._map.mouseEventToLayerPoint(t);this._handleMouseHover(t,i)}},_handleMouseOut:function(t){var i=this._hoveredLayer;i&&(mt(this._container,"leaflet-interactive"),this._fireEvent([i],t,"mouseout"),this._hoveredLayer=null)},_handleMouseHover:function(t,i){for(var e,n,o=this._drawFirst;o;o=o.next)(e=o.layer).options.interactive&&e._containsPoint(i)&&(n=e);n!==this._hoveredLayer&&(this._handleMouseOut(t),n&&(pt(this._container,"leaflet-interactive"),this._fireEvent([n],t,"mouseover"),this._hoveredLayer=n)),this._hoveredLayer&&this._fireEvent([this._hoveredLayer],t)},_fireEvent:function(t,i,e){this._map._fireDOMEvent(i,e||i.type,t)},_bringToFront:function(t){var i=t._order,e=i.next,n=i.prev;e&&(e.prev=n,n?n.next=e:e&&(this._drawFirst=e),i.prev=this._drawLast,this._drawLast.next=i,i.next=null,this._drawLast=i,this._requestRedraw(t))},_bringToBack:function(t){var i=t._order,e=i.next,n=i.prev;n&&(n.next=e,e?e.prev=n:n&&(this._drawLast=n),i.prev=null,i.next=this._drawFirst,this._drawFirst.prev=i,this._drawFirst=i,this._requestRedraw(t))}}),gn=function(){try{return document.namespaces.add("lvml","urn:schemas-microsoft-com:vml"),function(t){return document.createElement("<lvml:"+t+' class="lvml">')}}catch(t){return function(t){return document.createElement("<"+t+' xmlns="urn:schemas-microsoft.com:vml" class="lvml">')}}}(),vn={_initContainer:function(){this._container=ht("div","leaflet-vml-container")},_update:function(){this._map._animatingZoom||(mn.prototype._update.call(this),this.fire("update"))},_initPath:function(t){var i=t._container=gn("shape");pt(i,"leaflet-vml-shape "+(this.options.className||"")),i.coordsize="1 1",t._path=gn("path"),i.appendChild(t._path),this._updateStyle(t),this._layers[n(t)]=t},_addPath:function(t){var i=t._container;this._container.appendChild(i),t.options.interactive&&t.addInteractiveTarget(i)},_removePath:function(t){var i=t._container;ut(i),t.removeInteractiveTarget(i),delete this._layers[n(t)]},_updateStyle:function(t){var i=t._stroke,e=t._fill,n=t.options,o=t._container;o.stroked=!!n.stroke,o.filled=!!n.fill,n.stroke?(i||(i=t._stroke=gn("stroke")),o.appendChild(i),i.weight=n.weight+"px",i.color=n.color,i.opacity=n.opacity,n.dashArray?i.dashStyle=ei(n.dashArray)?n.dashArray.join(" "):n.dashArray.replace(/( *, *)/g," "):i.dashStyle="",i.endcap=n.lineCap.replace("butt","flat"),i.joinstyle=n.lineJoin):i&&(o.removeChild(i),t._stroke=null),n.fill?(e||(e=t._fill=gn("fill")),o.appendChild(e),e.color=n.fillColor||n.color,e.opacity=n.fillOpacity):e&&(o.removeChild(e),t._fill=null)},_updateCircle:function(t){var i=t._point.round(),e=Math.round(t._radius),n=Math.round(t._radiusY||e);this._setPath(t,t._empty()?"M0 0":"AL "+i.x+","+i.y+" "+e+","+n+" 0,23592600")},_setPath:function(t,i){t._path.v=i},_bringToFront:function(t){ct(t._container)},_bringToBack:function(t){_t(t._container)}},yn=Ji?gn:E,xn=mn.extend({getEvents:function(){var t=mn.prototype.getEvents.call(this);return t.zoomstart=this._onZoomStart,t},_initContainer:function(){this._container=yn("svg"),this._container.setAttribute("pointer-events","none"),this._rootGroup=yn("g"),this._container.appendChild(this._rootGroup)},_destroyContainer:function(){ut(this._container),q(this._container),delete this._container,delete this._rootGroup,delete this._svgSize},_onZoomStart:function(){this._update()},_update:function(){if(!this._map._animatingZoom||!this._bounds){mn.prototype._update.call(this);var t=this._bounds,i=t.getSize(),e=this._container;this._svgSize&&this._svgSize.equals(i)||(this._svgSize=i,e.setAttribute("width",i.x),e.setAttribute("height",i.y)),Lt(e,t.min),e.setAttribute("viewBox",[t.min.x,t.min.y,i.x,i.y].join(" ")),this.fire("update")}},_initPath:function(t){var i=t._path=yn("path");t.options.className&&pt(i,t.options.className),t.options.interactive&&pt(i,"leaflet-interactive"),this._updateStyle(t),this._layers[n(t)]=t},_addPath:function(t){this._rootGroup||this._initContainer(),this._rootGroup.appendChild(t._path),t.addInteractiveTarget(t._path)},_removePath:function(t){ut(t._path),t.removeInteractiveTarget(t._path),delete this._layers[n(t)]},_updatePath:function(t){t._project(),t._update()},_updateStyle:function(t){var i=t._path,e=t.options;i&&(e.stroke?(i.setAttribute("stroke",e.color),i.setAttribute("stroke-opacity",e.opacity),i.setAttribute("stroke-width",e.weight),i.setAttribute("stroke-linecap",e.lineCap),i.setAttribute("stroke-linejoin",e.lineJoin),e.dashArray?i.setAttribute("stroke-dasharray",e.dashArray):i.removeAttribute("stroke-dasharray"),e.dashOffset?i.setAttribute("stroke-dashoffset",e.dashOffset):i.removeAttribute("stroke-dashoffset")):i.setAttribute("stroke","none"),e.fill?(i.setAttribute("fill",e.fillColor||e.color),i.setAttribute("fill-opacity",e.fillOpacity),i.setAttribute("fill-rule",e.fillRule||"evenodd")):i.setAttribute("fill","none"))},_updatePoly:function(t,i){this._setPath(t,k(t._parts,i))},_updateCircle:function(t){var i=t._point,e=Math.max(Math.round(t._radius),1),n="a"+e+","+(Math.max(Math.round(t._radiusY),1)||e)+" 0 1,0 ",o=t._empty()?"M0 0":"M"+(i.x-e)+","+i.y+n+2*e+",0 "+n+2*-e+",0 ";this._setPath(t,o)},_setPath:function(t,i){t._path.setAttribute("d",i)},_bringToFront:function(t){ct(t._path)},_bringToBack:function(t){_t(t._path)}});Ji&&xn.include(vn),Le.include({getRenderer:function(t){var i=t.options.renderer||this._getPaneRenderer(t.options.pane)||this.options.renderer||this._renderer;return i||(i=this._renderer=this.options.preferCanvas&&Xt()||Jt()),this.hasLayer(i)||this.addLayer(i),i},_getPaneRenderer:function(t){if("overlayPane"===t||void 0===t)return!1;var i=this._paneRenderers[t];return void 0===i&&(i=xn&&Jt({pane:t})||fn&&Xt({pane:t}),this._paneRenderers[t]=i),i}});var wn=en.extend({initialize:function(t,i){en.prototype.initialize.call(this,this._boundsToLatLngs(t),i)},setBounds:function(t){return this.setLatLngs(this._boundsToLatLngs(t))},_boundsToLatLngs:function(t){return t=z(t),[t.getSouthWest(),t.getNorthWest(),t.getNorthEast(),t.getSouthEast()]}});xn.create=yn,xn.pointsToPath=k,nn.geometryToLayer=Wt,nn.coordsToLatLng=Ht,nn.coordsToLatLngs=Ft,nn.latLngToCoords=Ut,nn.latLngsToCoords=Vt,nn.getFeature=qt,nn.asFeature=Gt,Le.mergeOptions({boxZoom:!0});var Ln=Ze.extend({initialize:function(t){this._map=t,this._container=t._container,this._pane=t._panes.overlayPane,this._resetStateTimeout=0,t.on("unload",this._destroy,this)},addHooks:function(){V(this._container,"mousedown",this._onMouseDown,this)},removeHooks:function(){q(this._container,"mousedown",this._onMouseDown,this)},moved:function(){return this._moved},_destroy:function(){ut(this._pane),delete this._pane},_resetState:function(){this._resetStateTimeout=0,this._moved=!1},_clearDeferredResetState:function(){0!==this._resetStateTimeout&&(clearTimeout(this._resetStateTimeout),this._resetStateTimeout=0)},_onMouseDown:function(t){if(!t.shiftKey||1!==t.which&&1!==t.button)return!1;this._clearDeferredResetState(),this._resetState(),mi(),bt(),this._startPoint=this._map.mouseEventToContainerPoint(t),V(document,{contextmenu:Q,mousemove:this._onMouseMove,mouseup:this._onMouseUp,keydown:this._onKeyDown},this)},_onMouseMove:function(t){this._moved||(this._moved=!0,this._box=ht("div","leaflet-zoom-box",this._container),pt(this._container,"leaflet-crosshair"),this._map.fire("boxzoomstart")),this._point=this._map.mouseEventToContainerPoint(t);var i=new P(this._point,this._startPoint),e=i.getSize();Lt(this._box,i.min),this._box.style.width=e.x+"px",this._box.style.height=e.y+"px"},_finish:function(){this._moved&&(ut(this._box),mt(this._container,"leaflet-crosshair")),fi(),Tt(),q(document,{contextmenu:Q,mousemove:this._onMouseMove,mouseup:this._onMouseUp,keydown:this._onKeyDown},this)},_onMouseUp:function(t){if((1===t.which||1===t.button)&&(this._finish(),this._moved)){this._clearDeferredResetState(),this._resetStateTimeout=setTimeout(e(this._resetState,this),0);var i=new T(this._map.containerPointToLatLng(this._startPoint),this._map.containerPointToLatLng(this._point));this._map.fitBounds(i).fire("boxzoomend",{boxZoomBounds:i})}},_onKeyDown:function(t){27===t.keyCode&&this._finish()}});Le.addInitHook("addHandler","boxZoom",Ln),Le.mergeOptions({doubleClickZoom:!0});var Pn=Ze.extend({addHooks:function(){this._map.on("dblclick",this._onDoubleClick,this)},removeHooks:function(){this._map.off("dblclick",this._onDoubleClick,this)},_onDoubleClick:function(t){var i=this._map,e=i.getZoom(),n=i.options.zoomDelta,o=t.originalEvent.shiftKey?e-n:e+n;"center"===i.options.doubleClickZoom?i.setZoom(o):i.setZoomAround(t.containerPoint,o)}});Le.addInitHook("addHandler","doubleClickZoom",Pn),Le.mergeOptions({dragging:!0,inertia:!zi,inertiaDeceleration:3400,inertiaMaxSpeed:1/0,easeLinearity:.2,worldCopyJump:!1,maxBoundsViscosity:0});var bn=Ze.extend({addHooks:function(){if(!this._draggable){var t=this._map;this._draggable=new Be(t._mapPane,t._container),this._draggable.on({dragstart:this._onDragStart,drag:this._onDrag,dragend:this._onDragEnd},this),this._draggable.on("predrag",this._onPreDragLimit,this),t.options.worldCopyJump&&(this._draggable.on("predrag",this._onPreDragWrap,this),t.on("zoomend",this._onZoomEnd,this),t.whenReady(this._onZoomEnd,this))}pt(this._map._container,"leaflet-grab leaflet-touch-drag"),this._draggable.enable(),this._positions=[],this._times=[]},removeHooks:function(){mt(this._map._container,"leaflet-grab"),mt(this._map._container,"leaflet-touch-drag"),this._draggable.disable()},moved:function(){return this._draggable&&this._draggable._moved},moving:function(){return this._draggable&&this._draggable._moving},_onDragStart:function(){var t=this._map;if(t._stop(),this._map.options.maxBounds&&this._map.options.maxBoundsViscosity){var i=z(this._map.options.maxBounds);this._offsetLimit=b(this._map.latLngToContainerPoint(i.getNorthWest()).multiplyBy(-1),this._map.latLngToContainerPoint(i.getSouthEast()).multiplyBy(-1).add(this._map.getSize())),this._viscosity=Math.min(1,Math.max(0,this._map.options.maxBoundsViscosity))}else this._offsetLimit=null;t.fire("movestart").fire("dragstart"),t.options.inertia&&(this._positions=[],this._times=[])},_onDrag:function(t){if(this._map.options.inertia){var i=this._lastTime=+new Date,e=this._lastPos=this._draggable._absPos||this._draggable._newPos;this._positions.push(e),this._times.push(i),this._prunePositions(i)}this._map.fire("move",t).fire("drag",t)},_prunePositions:function(t){for(;this._positions.length>1&&t-this._times[0]>50;)this._positions.shift(),this._times.shift()},_onZoomEnd:function(){var t=this._map.getSize().divideBy(2),i=this._map.latLngToLayerPoint([0,0]);this._initialWorldOffset=i.subtract(t).x,this._worldWidth=this._map.getPixelWorldBounds().getSize().x},_viscousLimit:function(t,i){return t-(t-i)*this._viscosity},_onPreDragLimit:function(){if(this._viscosity&&this._offsetLimit){var t=this._draggable._newPos.subtract(this._draggable._startPos),i=this._offsetLimit;t.x<i.min.x&&(t.x=this._viscousLimit(t.x,i.min.x)),t.y<i.min.y&&(t.y=this._viscousLimit(t.y,i.min.y)),t.x>i.max.x&&(t.x=this._viscousLimit(t.x,i.max.x)),t.y>i.max.y&&(t.y=this._viscousLimit(t.y,i.max.y)),this._draggable._newPos=this._draggable._startPos.add(t)}},_onPreDragWrap:function(){var t=this._worldWidth,i=Math.round(t/2),e=this._initialWorldOffset,n=this._draggable._newPos.x,o=(n-i+e)%t+i-e,s=(n+i+e)%t-i-e,r=Math.abs(o+e)<Math.abs(s+e)?o:s;this._draggable._absPos=this._draggable._newPos.clone(),this._draggable._newPos.x=r},_onDragEnd:function(t){var i=this._map,e=i.options,n=!e.inertia||this._times.length<2;if(i.fire("dragend",t),n)i.fire("moveend");else{this._prunePositions(+new Date);var o=this._lastPos.subtract(this._positions[0]),s=(this._lastTime-this._times[0])/1e3,r=e.easeLinearity,a=o.multiplyBy(r/s),h=a.distanceTo([0,0]),u=Math.min(e.inertiaMaxSpeed,h),l=a.multiplyBy(u/h),c=u/(e.inertiaDeceleration*r),_=l.multiplyBy(-c/2).round();_.x||_.y?(_=i._limitOffset(_,i.options.maxBounds),f(function(){i.panBy(_,{duration:c,easeLinearity:r,noMoveStart:!0,animate:!0})})):i.fire("moveend")}}});Le.addInitHook("addHandler","dragging",bn),Le.mergeOptions({keyboard:!0,keyboardPanDelta:80});var Tn=Ze.extend({keyCodes:{left:[37],right:[39],down:[40],up:[38],zoomIn:[187,107,61,171],zoomOut:[189,109,54,173]},initialize:function(t){this._map=t,this._setPanDelta(t.options.keyboardPanDelta),this._setZoomDelta(t.options.zoomDelta)},addHooks:function(){var t=this._map._container;t.tabIndex<=0&&(t.tabIndex="0"),V(t,{focus:this._onFocus,blur:this._onBlur,mousedown:this._onMouseDown},this),this._map.on({focus:this._addHooks,blur:this._removeHooks},this)},removeHooks:function(){this._removeHooks(),q(this._map._container,{focus:this._onFocus,blur:this._onBlur,mousedown:this._onMouseDown},this),this._map.off({focus:this._addHooks,blur:this._removeHooks},this)},_onMouseDown:function(){if(!this._focused){var t=document.body,i=document.documentElement,e=t.scrollTop||i.scrollTop,n=t.scrollLeft||i.scrollLeft;this._map._container.focus(),window.scrollTo(n,e)}},_onFocus:function(){this._focused=!0,this._map.fire("focus")},_onBlur:function(){this._focused=!1,this._map.fire("blur")},_setPanDelta:function(t){var i,e,n=this._panKeys={},o=this.keyCodes;for(i=0,e=o.left.length;i<e;i++)n[o.left[i]]=[-1*t,0];for(i=0,e=o.right.length;i<e;i++)n[o.right[i]]=[t,0];for(i=0,e=o.down.length;i<e;i++)n[o.down[i]]=[0,t];for(i=0,e=o.up.length;i<e;i++)n[o.up[i]]=[0,-1*t]},_setZoomDelta:function(t){var i,e,n=this._zoomKeys={},o=this.keyCodes;for(i=0,e=o.zoomIn.length;i<e;i++)n[o.zoomIn[i]]=t;for(i=0,e=o.zoomOut.length;i<e;i++)n[o.zoomOut[i]]=-t},_addHooks:function(){V(document,"keydown",this._onKeyDown,this)},_removeHooks:function(){q(document,"keydown",this._onKeyDown,this)},_onKeyDown:function(t){if(!(t.altKey||t.ctrlKey||t.metaKey)){var i,e=t.keyCode,n=this._map;if(e in this._panKeys){if(n._panAnim&&n._panAnim._inProgress)return;i=this._panKeys[e],t.shiftKey&&(i=w(i).multiplyBy(3)),n.panBy(i),n.options.maxBounds&&n.panInsideBounds(n.options.maxBounds)}else if(e in this._zoomKeys)n.setZoom(n.getZoom()+(t.shiftKey?3:1)*this._zoomKeys[e]);else{if(27!==e||!n._popup||!n._popup.options.closeOnEscapeKey)return;n.closePopup()}Q(t)}}});Le.addInitHook("addHandler","keyboard",Tn),Le.mergeOptions({scrollWheelZoom:!0,wheelDebounceTime:40,wheelPxPerZoomLevel:60});var zn=Ze.extend({addHooks:function(){V(this._map._container,"mousewheel",this._onWheelScroll,this),this._delta=0},removeHooks:function(){q(this._map._container,"mousewheel",this._onWheelScroll,this)},_onWheelScroll:function(t){var i=it(t),n=this._map.options.wheelDebounceTime;this._delta+=i,this._lastMousePos=this._map.mouseEventToContainerPoint(t),this._startTime||(this._startTime=+new Date);var o=Math.max(n-(+new Date-this._startTime),0);clearTimeout(this._timer),this._timer=setTimeout(e(this._performZoom,this),o),Q(t)},_performZoom:function(){var t=this._map,i=t.getZoom(),e=this._map.options.zoomSnap||0;t._stop();var n=this._delta/(4*this._map.options.wheelPxPerZoomLevel),o=4*Math.log(2/(1+Math.exp(-Math.abs(n))))/Math.LN2,s=e?Math.ceil(o/e)*e:o,r=t._limitZoom(i+(this._delta>0?s:-s))-i;this._delta=0,this._startTime=null,r&&("center"===t.options.scrollWheelZoom?t.setZoom(i+r):t.setZoomAround(this._lastMousePos,i+r))}});Le.addInitHook("addHandler","scrollWheelZoom",zn),Le.mergeOptions({tap:!0,tapTolerance:15});var Mn=Ze.extend({addHooks:function(){V(this._map._container,"touchstart",this._onDown,this)},removeHooks:function(){q(this._map._container,"touchstart",this._onDown,this)},_onDown:function(t){if(t.touches){if($(t),this._fireClick=!0,t.touches.length>1)return this._fireClick=!1,void clearTimeout(this._holdTimeout);var i=t.touches[0],n=i.target;this._startPos=this._newPos=new x(i.clientX,i.clientY),n.tagName&&"a"===n.tagName.toLowerCase()&&pt(n,"leaflet-active"),this._holdTimeout=setTimeout(e(function(){this._isTapValid()&&(this._fireClick=!1,this._onUp(),this._simulateEvent("contextmenu",i))},this),1e3),this._simulateEvent("mousedown",i),V(document,{touchmove:this._onMove,touchend:this._onUp},this)}},_onUp:function(t){if(clearTimeout(this._holdTimeout),q(document,{touchmove:this._onMove,touchend:this._onUp},this),this._fireClick&&t&&t.changedTouches){var i=t.changedTouches[0],e=i.target;e&&e.tagName&&"a"===e.tagName.toLowerCase()&&mt(e,"leaflet-active"),this._simulateEvent("mouseup",i),this._isTapValid()&&this._simulateEvent("click",i)}},_isTapValid:function(){return this._newPos.distanceTo(this._startPos)<=this._map.options.tapTolerance},_onMove:function(t){var i=t.touches[0];this._newPos=new x(i.clientX,i.clientY),this._simulateEvent("mousemove",i)},_simulateEvent:function(t,i){var e=document.createEvent("MouseEvents");e._simulated=!0,i.target._simulatedClick=!0,e.initMouseEvent(t,!0,!0,window,1,i.screenX,i.screenY,i.clientX,i.clientY,!1,!1,!1,!1,0,null),i.target.dispatchEvent(e)}});Vi&&!Ui&&Le.addInitHook("addHandler","tap",Mn),Le.mergeOptions({touchZoom:Vi&&!zi,bounceAtZoomLimits:!0});var Cn=Ze.extend({addHooks:function(){pt(this._map._container,"leaflet-touch-zoom"),V(this._map._container,"touchstart",this._onTouchStart,this)},removeHooks:function(){mt(this._map._container,"leaflet-touch-zoom"),q(this._map._container,"touchstart",this._onTouchStart,this)},_onTouchStart:function(t){var i=this._map;if(t.touches&&2===t.touches.length&&!i._animatingZoom&&!this._zooming){var e=i.mouseEventToContainerPoint(t.touches[0]),n=i.mouseEventToContainerPoint(t.touches[1]);this._centerPoint=i.getSize()._divideBy(2),this._startLatLng=i.containerPointToLatLng(this._centerPoint),"center"!==i.options.touchZoom&&(this._pinchStartLatLng=i.containerPointToLatLng(e.add(n)._divideBy(2))),this._startDist=e.distanceTo(n),this._startZoom=i.getZoom(),this._moved=!1,this._zooming=!0,i._stop(),V(document,"touchmove",this._onTouchMove,this),V(document,"touchend",this._onTouchEnd,this),$(t)}},_onTouchMove:function(t){if(t.touches&&2===t.touches.length&&this._zooming){var i=this._map,n=i.mouseEventToContainerPoint(t.touches[0]),o=i.mouseEventToContainerPoint(t.touches[1]),s=n.distanceTo(o)/this._startDist;if(this._zoom=i.getScaleZoom(s,this._startZoom),!i.options.bounceAtZoomLimits&&(this._zoom<i.getMinZoom()&&s<1||this._zoom>i.getMaxZoom()&&s>1)&&(this._zoom=i._limitZoom(this._zoom)),"center"===i.options.touchZoom){if(this._center=this._startLatLng,1===s)return}else{var r=n._add(o)._divideBy(2)._subtract(this._centerPoint);if(1===s&&0===r.x&&0===r.y)return;this._center=i.unproject(i.project(this._pinchStartLatLng,this._zoom).subtract(r),this._zoom)}this._moved||(i._moveStart(!0,!1),this._moved=!0),g(this._animRequest);var a=e(i._move,i,this._center,this._zoom,{pinch:!0,round:!1});this._animRequest=f(a,this,!0),$(t)}},_onTouchEnd:function(){this._moved&&this._zooming?(this._zooming=!1,g(this._animRequest),q(document,"touchmove",this._onTouchMove),q(document,"touchend",this._onTouchEnd),this._map.options.zoomAnimation?this._map._animateZoom(this._center,this._map._limitZoom(this._zoom),!0,this._map.options.zoomSnap):this._map._resetView(this._center,this._map._limitZoom(this._zoom))):this._zooming=!1}});Le.addInitHook("addHandler","touchZoom",Cn),Le.BoxZoom=Ln,Le.DoubleClickZoom=Pn,Le.Drag=bn,Le.Keyboard=Tn,Le.ScrollWheelZoom=zn,Le.Tap=Mn,Le.TouchZoom=Cn;var Zn=window.L;window.L=t,Object.freeze=$t,t.version="1.3.1",t.noConflict=function(){return window.L=Zn,this},t.Control=Pe,t.control=be,t.Browser=$i,t.Evented=ui,t.Mixin=Ee,t.Util=ai,t.Class=v,t.Handler=Ze,t.extend=i,t.bind=e,t.stamp=n,t.setOptions=l,t.DomEvent=de,t.DomUtil=xe,t.PosAnimation=we,t.Draggable=Be,t.LineUtil=Oe,t.PolyUtil=Re,t.Point=x,t.point=w,t.Bounds=P,t.bounds=b,t.Transformation=Z,t.transformation=S,t.Projection=je,t.LatLng=M,t.latLng=C,t.LatLngBounds=T,t.latLngBounds=z,t.CRS=ci,t.GeoJSON=nn,t.geoJSON=Kt,t.geoJson=sn,t.Layer=Ue,t.LayerGroup=Ve,t.layerGroup=function(t,i){return new Ve(t,i)},t.FeatureGroup=qe,t.featureGroup=function(t){return new qe(t)},t.ImageOverlay=rn,t.imageOverlay=function(t,i,e){return new rn(t,i,e)},t.VideoOverlay=an,t.videoOverlay=function(t,i,e){return new an(t,i,e)},t.DivOverlay=hn,t.Popup=un,t.popup=function(t,i){return new un(t,i)},t.Tooltip=ln,t.tooltip=function(t,i){return new ln(t,i)},t.Icon=Ge,t.icon=function(t){return new Ge(t)},t.DivIcon=cn,t.divIcon=function(t){return new cn(t)},t.Marker=Xe,t.marker=function(t,i){return new Xe(t,i)},t.TileLayer=dn,t.tileLayer=Yt,t.GridLayer=_n,t.gridLayer=function(t){return new _n(t)},t.SVG=xn,t.svg=Jt,t.Renderer=mn,t.Canvas=fn,t.canvas=Xt,t.Path=Je,t.CircleMarker=$e,t.circleMarker=function(t,i){return new $e(t,i)},t.Circle=Qe,t.circle=function(t,i,e){return new Qe(t,i,e)},t.Polyline=tn,t.polyline=function(t,i){return new tn(t,i)},t.Polygon=en,t.polygon=function(t,i){return new en(t,i)},t.Rectangle=wn,t.rectangle=function(t,i){return new wn(t,i)},t.Map=Le,t.map=function(t,i){return new Le(t,i)}});

(function (root, factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD. Register as an anonymous module.
		define(['leaflet'], factory);
	} else if (typeof modules === 'object' && module.exports) {
		// define a Common JS module that relies on 'leaflet'
		module.exports = factory(require('leaflet'));
	} else {
		// Assume Leaflet is loaded into global object L already
		factory(L);
	}
}(this, function (L) {
	'use strict';

	L.TileLayer.Provider = L.TileLayer.extend({
		initialize: function (arg, options) {
			var providers = L.TileLayer.Provider.providers;
			var parts = arg.split('.');

			var providerName = parts[0];
			var variantName = parts[1];

			if (!providers[providerName]) {
				throw 'No such provider (' + providerName + ')';
			}

			var provider = {
				url: providers[providerName].url,
				options: providers[providerName].options
			};



			// overwrite values in provider from variant.
			if (variantName && 'variants' in providers[providerName]) {
				if (!(variantName in providers[providerName].variants)) {
					throw 'No such variant of ' + providerName + ' (' + variantName + ')';
				}
				var variant = providers[providerName].variants[variantName];
				var variantOptions;
				if (typeof variant === 'string') {
					variantOptions = {
						variant: variant
					};
				} else {
					variantOptions = variant.options;
				}
				provider = {
					url: variant.url || provider.url,
					options: L.Util.extend({}, provider.options, variantOptions)
				};
			}

			// replace attribution placeholders with their values from toplevel provider attribution,
			// recursively
			var attributionReplacer = function (attr) {
				if (attr.indexOf('{attribution.') === -1) {
					return attr;
				}
				return attr.replace(/\{attribution.(\w*)\}/g,
					function (match, attributionName) {
						return attributionReplacer(providers[attributionName].options.attribution);
					}
				);
			};
			provider.options.attribution = attributionReplacer(provider.options.attribution);

			// Compute final options combining provider options with any user overrides
			var layerOpts = L.Util.extend({}, provider.options, options);
			L.TileLayer.prototype.initialize.call(this, provider.url, layerOpts);
		}
	});

	/**
	 * Definition of providers.
	 * see http://leafletjs.com/reference.html#tilelayer for options in the options map.
	 */

	L.TileLayer.Provider.providers = {
		OpenStreetMap: {
			url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
			options: {
				maxZoom: 19,
				attribution:
					'&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
			},
			variants: {
				Mapnik: {},
				DE: {
					url: 'https://{s}.tile.openstreetmap.de/tiles/osmde/{z}/{x}/{y}.png',
					options: {
						maxZoom: 18
					}
				},
				CH: {
					url: 'https://tile.osm.ch/switzerland/{z}/{x}/{y}.png',
					options: {
						maxZoom: 18,
						bounds: [[45, 5], [48, 11]]
					}
				},
				France: {
					url: 'https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png',
					options: {
						maxZoom: 20,
						attribution: '&copy; Openstreetmap France | {attribution.OpenStreetMap}'
					}
				},
				HOT: {
					url: 'https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png',
					options: {
						attribution:
							'{attribution.OpenStreetMap}, ' +
							'Tiles style by <a href="https://www.hotosm.org/" target="_blank">Humanitarian OpenStreetMap Team</a> ' +
							'hosted by <a href="https://openstreetmap.fr/" target="_blank">OpenStreetMap France</a>'
					}
				},
				BZH: {
					url: 'https://tile.openstreetmap.bzh/br/{z}/{x}/{y}.png',
					options: {
						attribution: '{attribution.OpenStreetMap}, Tiles courtesy of <a href="http://www.openstreetmap.bzh/" target="_blank">Breton OpenStreetMap Team</a>',
						bounds: [[46.2, -5.5], [50, 0.7]]
					}
				}
			}
		},
		OpenSeaMap: {
			url: 'https://tiles.openseamap.org/seamark/{z}/{x}/{y}.png',
			options: {
				attribution: 'Map data: &copy; <a href="http://www.openseamap.org">OpenSeaMap</a> contributors'
			}
		},
		OpenPtMap: {
			url: 'http://openptmap.org/tiles/{z}/{x}/{y}.png',
			options: {
				maxZoom: 17,
				attribution: 'Map data: &copy; <a href="http://www.openptmap.org">OpenPtMap</a> contributors'
			}
		},
		OpenTopoMap: {
			url: 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
			options: {
				maxZoom: 17,
				attribution: 'Map data: {attribution.OpenStreetMap}, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)'
			}
		},
		OpenRailwayMap: {
			url: 'https://{s}.tiles.openrailwaymap.org/standard/{z}/{x}/{y}.png',
			options: {
				maxZoom: 19,
				attribution: 'Map data: {attribution.OpenStreetMap} | Map style: &copy; <a href="https://www.OpenRailwayMap.org">OpenRailwayMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)'
			}
		},
		OpenFireMap: {
			url: 'http://openfiremap.org/hytiles/{z}/{x}/{y}.png',
			options: {
				maxZoom: 19,
				attribution: 'Map data: {attribution.OpenStreetMap} | Map style: &copy; <a href="http://www.openfiremap.org">OpenFireMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)'
			}
		},
		SafeCast: {
			url: 'https://s3.amazonaws.com/te512.safecast.org/{z}/{x}/{y}.png',
			options: {
				maxZoom: 16,
				attribution: 'Map data: {attribution.OpenStreetMap} | Map style: &copy; <a href="https://blog.safecast.org/about/">SafeCast</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)'
			}
		},
		Thunderforest: {
			url: 'https://{s}.tile.thunderforest.com/{variant}/{z}/{x}/{y}.png?apikey={apikey}',
			options: {
				attribution:
					'&copy; <a href="http://www.thunderforest.com/">Thunderforest</a>, {attribution.OpenStreetMap}',
				variant: 'cycle',
				apikey: '<insert your api key here>',
				maxZoom: 22
			},
			variants: {
				OpenCycleMap: 'cycle',
				Transport: {
					options: {
						variant: 'transport'
					}
				},
				TransportDark: {
					options: {
						variant: 'transport-dark'
					}
				},
				SpinalMap: {
					options: {
						variant: 'spinal-map'
					}
				},
				Landscape: 'landscape',
				Outdoors: 'outdoors',
				Pioneer: 'pioneer',
				MobileAtlas: 'mobile-atlas',
				Neighbourhood: 'neighbourhood'
			}
		},
		CyclOSM: {
			url: 'https://dev.{s}.tile.openstreetmap.fr/cyclosm/{z}/{x}/{y}.png',
			options: {
				maxZoom: 20,
				attribution: '<a href="https://github.com/cyclosm/cyclosm-cartocss-style/releases" title="CyclOSM - Open Bicycle render">CyclOSM</a> | Map data: {attribution.OpenStreetMap}'
			}
		},
		OpenMapSurfer: {
			url: 'https://maps.heigit.org/openmapsurfer/tiles/{variant}/webmercator/{z}/{x}/{y}.png',
			options: {
				maxZoom: 19,
				variant: 'roads',
				attribution: 'Imagery from <a href="http://giscience.uni-hd.de/">GIScience Research Group @ University of Heidelberg</a> | Map data '
			},
			variants: {
				Roads: {
					options: {
						variant: 'roads',
						attribution: '{attribution.OpenMapSurfer}{attribution.OpenStreetMap}'
					}
				},
				Hybrid: {
					options: {
						variant: 'hybrid',
						attribution: '{attribution.OpenMapSurfer}{attribution.OpenStreetMap}'
					}
				},
				AdminBounds: {
					options: {
						variant: 'adminb',
						maxZoom: 18,
						attribution: '{attribution.OpenMapSurfer}{attribution.OpenStreetMap}'
					}
				},
				ContourLines: {
					options: {
						variant: 'asterc',
						maxZoom: 18,
						minZoom: 13,
						attribution: '{attribution.OpenMapSurfer} <a href="https://lpdaac.usgs.gov/products/aster_policies">ASTER GDEM</a>'
					}
				},
				Hillshade: {
					options: {
						variant: 'asterh',
						maxZoom: 18,
						attribution: '{attribution.OpenMapSurfer} <a href="https://lpdaac.usgs.gov/products/aster_policies">ASTER GDEM</a>, <a href="http://srtm.csi.cgiar.org/">SRTM</a>'
					}
				},
				ElementsAtRisk: {
					options: {
						variant: 'elements_at_risk',
						attribution: '{attribution.OpenMapSurfer}{attribution.OpenStreetMap}'
					}
				}
			}
		},
		Hydda: {
			url: 'https://{s}.tile.openstreetmap.se/hydda/{variant}/{z}/{x}/{y}.png',
			options: {
				maxZoom: 18,
				variant: 'full',
				attribution: 'Tiles courtesy of <a href="http://openstreetmap.se/" target="_blank">OpenStreetMap Sweden</a> &mdash; Map data {attribution.OpenStreetMap}'
			},
			variants: {
				Full: 'full',
				Base: 'base',
				RoadsAndLabels: 'roads_and_labels'
			}
		},
		MapBox: {
			url: 'https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token={accessToken}',
			options: {
				attribution:
					'<a href="https://www.mapbox.com/about/maps/" target="_blank">&copy; Mapbox</a> ' +
					'{attribution.OpenStreetMap} ' +
					'<a href="https://www.mapbox.com/map-feedback/" target="_blank">Improve this map</a>',
				subdomains: 'abcd',
				id: 'mapbox.streets',
				accessToken: '<insert your access token here>',
				maxZoom: 25,
				tileSize: 512,
  				zoomOffset: -1,
			}
		},
		Stamen: {
			url: 'https://tiles.stadiamaps.com/tiles/stamen_toner/{z}/{x}/{y}{r}.{ext}',
			options: {
				attribution:
					'Map tiles by <a href="http://stamen.com">Stamen Design</a>, ' +
					'<a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a> &mdash; ' +
					'Map data {attribution.OpenStreetMap}',
				subdomains: 'abcd',
				minZoom: 0,
				maxZoom: 20,
				variant: 'toner',
				ext: 'png'
			},
			variants: {
				Toner: 'toner',
				TonerBackground: 'toner-background',
				TonerHybrid: 'toner-hybrid',
				TonerLines: 'toner-lines',
				TonerLabels: 'toner-labels',
				TonerLite: 'toner-lite',
				Watercolor: {
					url: 'https://stamen-tiles-{s}.a.ssl.fastly.net/{variant}/{z}/{x}/{y}.{ext}',
					options: {
						variant: 'watercolor',
						ext: 'jpg',
						minZoom: 1,
						maxZoom: 16
					}
				},
				Terrain: {
					options: {
						variant: 'terrain',
						minZoom: 0,
						maxZoom: 18
					}
				},
				TerrainBackground: {
					options: {
						variant: 'terrain-background',
						minZoom: 0,
						maxZoom: 18
					}
				},
				TerrainLabels: {
					options: {
						variant: 'terrain-labels',
						minZoom: 0,
						maxZoom: 18
					}
				},
				TopOSMRelief: {
					url: 'https://stamen-tiles-{s}.a.ssl.fastly.net/{variant}/{z}/{x}/{y}.{ext}',
					options: {
						variant: 'toposm-color-relief',
						ext: 'jpg',
						bounds: [[22, -132], [51, -56]]
					}
				},
				TopOSMFeatures: {
					options: {
						variant: 'toposm-features',
						bounds: [[22, -132], [51, -56]],
						opacity: 0.9
					}
				}
			}
		},
		TomTom: {
			url: 'https://{s}.api.tomtom.com/map/1/tile/{variant}/{style}/{z}/{x}/{y}.{ext}?key={apikey}',
			options: {
				variant: 'basic',
				maxZoom: 22,
				attribution:
					'<a href="https://tomtom.com" target="_blank">&copy;  1992 - ' + new Date().getFullYear() + ' TomTom.</a> ',
				subdomains: 'abcd',
				style: 'main',
				ext: 'png',
				apikey: '<insert your API key here>',
			},
			variants: {
				Basic: 'basic',
				Hybrid: 'hybrid',
				Labels: 'labels'
			}
		},
		Esri: {
			url: 'https://server.arcgisonline.com/ArcGIS/rest/services/{variant}/MapServer/tile/{z}/{y}/{x}',
			options: {
				variant: 'World_Street_Map',
				attribution: 'Tiles &copy; Esri'
			},
			variants: {
				WorldStreetMap: {
					options: {
						attribution:
							'{attribution.Esri} &mdash; ' +
							'Source: Esri, DeLorme, NAVTEQ, USGS, Intermap, iPC, NRCAN, Esri Japan, METI, Esri China (Hong Kong), Esri (Thailand), TomTom, 2012'
					}
				},
				DeLorme: {
					options: {
						variant: 'Specialty/DeLorme_World_Base_Map',
						minZoom: 1,
						maxZoom: 11,
						attribution: '{attribution.Esri} &mdash; Copyright: &copy;2012 DeLorme'
					}
				},
				WorldTopoMap: {
					options: {
						variant: 'World_Topo_Map',
						attribution:
							'{attribution.Esri} &mdash; ' +
							'Esri, DeLorme, NAVTEQ, TomTom, Intermap, iPC, USGS, FAO, NPS, NRCAN, GeoBase, Kadaster NL, Ordnance Survey, Esri Japan, METI, Esri China (Hong Kong), and the GIS User Community'
					}
				},
				WorldImagery: {
					options: {
						variant: 'World_Imagery',
						attribution:
							'{attribution.Esri} &mdash; ' +
							'Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
					}
				},
				WorldTerrain: {
					options: {
						variant: 'World_Terrain_Base',
						maxZoom: 13,
						attribution:
							'{attribution.Esri} &mdash; ' +
							'Source: USGS, Esri, TANA, DeLorme, and NPS'
					}
				},
				WorldShadedRelief: {
					options: {
						variant: 'World_Shaded_Relief',
						maxZoom: 13,
						attribution: '{attribution.Esri} &mdash; Source: Esri'
					}
				},
				WorldPhysical: {
					options: {
						variant: 'World_Physical_Map',
						maxZoom: 8,
						attribution: '{attribution.Esri} &mdash; Source: US National Park Service'
					}
				},
				OceanBasemap: {
					options: {
						variant: 'Ocean_Basemap',
						maxZoom: 13,
						attribution: '{attribution.Esri} &mdash; Sources: GEBCO, NOAA, CHS, OSU, UNH, CSUMB, National Geographic, DeLorme, NAVTEQ, and Esri'
					}
				},
				NatGeoWorldMap: {
					options: {
						variant: 'NatGeo_World_Map',
						maxZoom: 16,
						attribution: '{attribution.Esri} &mdash; National Geographic, Esri, DeLorme, NAVTEQ, UNEP-WCMC, USGS, NASA, ESA, METI, NRCAN, GEBCO, NOAA, iPC'
					}
				},
				WorldGrayCanvas: {
					options: {
						variant: 'Canvas/World_Light_Gray_Base',
						maxZoom: 16,
						attribution: '{attribution.Esri} &mdash; Esri, DeLorme, NAVTEQ'
					}
				}
			}
		},
		OpenWeatherMap: {
			url: 'http://{s}.tile.openweathermap.org/map/{variant}/{z}/{x}/{y}.png?appid={apiKey}',
			options: {
				maxZoom: 19,
				attribution: 'Map data &copy; <a href="http://openweathermap.org">OpenWeatherMap</a>',
				apiKey:'<insert your api key here>',
				opacity: 0.5
			},
			variants: {
				Clouds: 'clouds',
				CloudsClassic: 'clouds_cls',
				Precipitation: 'precipitation',
				PrecipitationClassic: 'precipitation_cls',
				Rain: 'rain',
				RainClassic: 'rain_cls',
				Pressure: 'pressure',
				PressureContour: 'pressure_cntr',
				Wind: 'wind',
				Temperature: 'temp',
				Snow: 'snow'
			}
		},
		HERE: {
			/*
			 * HERE maps, formerly Nokia maps.
			 * These basemaps are free, but you need an API key. Please sign up at
			 * https://developer.here.com/plans
			 */
			url:
				'https://{s}.{base}.maps.api.here.com/maptile/2.1/' +
				'{type}/{mapID}/{variant}/{z}/{x}/{y}/{size}/{format}?' +
				'app_id={app_id}&app_code={app_code}&lg={language}',
			options: {
				attribution:
					'Map &copy; 1987-' + new Date().getFullYear() + ' <a href="http://developer.here.com">HERE</a>',
				subdomains: '1234',
				mapID: 'newest',
				'app_id': '<insert your app_id here>',
				'app_code': '<insert your app_code here>',
				base: 'base',
				variant: 'normal.day',
				maxZoom: 20,
				type: 'maptile',
				language: 'eng',
				format: 'png8',
				size: '256'
			},
			variants: {
				normalDay: 'normal.day',
				normalDayCustom: 'normal.day.custom',
				normalDayGrey: 'normal.day.grey',
				normalDayMobile: 'normal.day.mobile',
				normalDayGreyMobile: 'normal.day.grey.mobile',
				normalDayTransit: 'normal.day.transit',
				normalDayTransitMobile: 'normal.day.transit.mobile',
				normalDayTraffic: {
					options: {
						variant: 'normal.traffic.day',
						base: 'traffic',
						type: 'traffictile'
					}
				},
				normalNight: 'normal.night',
				normalNightMobile: 'normal.night.mobile',
				normalNightGrey: 'normal.night.grey',
				normalNightGreyMobile: 'normal.night.grey.mobile',
				normalNightTransit: 'normal.night.transit',
				normalNightTransitMobile: 'normal.night.transit.mobile',
				reducedDay: 'reduced.day',
				reducedNight: 'reduced.night',
				basicMap: {
					options: {
						type: 'basetile'
					}
				},
				mapLabels: {
					options: {
						type: 'labeltile',
						format: 'png'
					}
				},
				trafficFlow: {
					options: {
						base: 'traffic',
						type: 'flowtile'
					}
				},
				carnavDayGrey: 'carnav.day.grey',
				hybridDay: {
					options: {
						base: 'aerial',
						variant: 'hybrid.day'
					}
				},
				hybridDayMobile: {
					options: {
						base: 'aerial',
						variant: 'hybrid.day.mobile'
					}
				},
				hybridDayTransit: {
					options: {
						base: 'aerial',
						variant: 'hybrid.day.transit'
					}
				},
				hybridDayGrey: {
					options: {
						base: 'aerial',
						variant: 'hybrid.grey.day'
					}
				},
				hybridDayTraffic: {
					options: {
						variant: 'hybrid.traffic.day',
						base: 'traffic',
						type: 'traffictile'
					}
				},
				pedestrianDay: 'pedestrian.day',
				pedestrianNight: 'pedestrian.night',
				satelliteDay: {
					options: {
						base: 'aerial',
						variant: 'satellite.day'
					}
				},
				terrainDay: {
					options: {
						base: 'aerial',
						variant: 'terrain.day'
					}
				},
				terrainDayMobile: {
					options: {
						base: 'aerial',
						variant: 'terrain.day.mobile'
					}
				}
			}
		},
		FreeMapSK: {
			url: 'http://t{s}.freemap.sk/T/{z}/{x}/{y}.jpeg',
			options: {
				minZoom: 8,
				maxZoom: 16,
				subdomains: '1234',
				bounds: [[47.204642, 15.996093], [49.830896, 22.576904]],
				attribution:
					'{attribution.OpenStreetMap}, vizualization CC-By-SA 2.0 <a href="http://freemap.sk">Freemap.sk</a>'
			}
		},
		MtbMap: {
			url: 'http://tile.mtbmap.cz/mtbmap_tiles/{z}/{x}/{y}.png',
			options: {
				attribution:
					'{attribution.OpenStreetMap} &amp; USGS'
			}
		},
		CartoDB: {
			url: 'https://{s}.basemaps.cartocdn.com/{variant}/{z}/{x}/{y}{r}.png',
			options: {
				attribution: '{attribution.OpenStreetMap} &copy; <a href="https://carto.com/attributions">CARTO</a>',
				subdomains: 'abcd',
				maxZoom: 19,
				variant: 'light_all'
			},
			variants: {
				Positron: 'light_all',
				PositronNoLabels: 'light_nolabels',
				PositronOnlyLabels: 'light_only_labels',
				DarkMatter: 'dark_all',
				DarkMatterNoLabels: 'dark_nolabels',
				DarkMatterOnlyLabels: 'dark_only_labels',
				Voyager: 'rastertiles/voyager',
				VoyagerNoLabels: 'rastertiles/voyager_nolabels',
				VoyagerOnlyLabels: 'rastertiles/voyager_only_labels',
				VoyagerLabelsUnder: 'rastertiles/voyager_labels_under'
			}
		},
		HikeBike: {
			url: 'https://tiles.wmflabs.org/{variant}/{z}/{x}/{y}.png',
			options: {
				maxZoom: 19,
				attribution: '{attribution.OpenStreetMap}',
				variant: 'hikebike'
			},
			variants: {
				HikeBike: {},
				HillShading: {
					options: {
						maxZoom: 15,
						variant: 'hillshading'
					}
				}
			}
		},
		BasemapAT: {
			url: 'https://maps{s}.wien.gv.at/basemap/{variant}/normal/google3857/{z}/{y}/{x}.{format}',
			options: {
				maxZoom: 19,
				attribution: 'Datenquelle: <a href="https://www.basemap.at">basemap.at</a>',
				subdomains: ['', '1', '2', '3', '4'],
				format: 'png',
				bounds: [[46.358770, 8.782379], [49.037872, 17.189532]],
				variant: 'geolandbasemap'
			},
			variants: {
				basemap: {
					options: {
						maxZoom: 20, // currently only in Vienna
						variant: 'geolandbasemap'
					}
				},
				grau: 'bmapgrau',
				overlay: 'bmapoverlay',
				highdpi: {
					options: {
						variant: 'bmaphidpi',
						format: 'jpeg'
					}
				},
				orthofoto: {
					options: {
						maxZoom: 20, // currently only in Vienna
						variant: 'bmaporthofoto30cm',
						format: 'jpeg'
					}
				}
			}
		},
		nlmaps: {
			url: 'https://geodata.nationaalgeoregister.nl/tiles/service/wmts/{variant}/EPSG:3857/{z}/{x}/{y}.png',
			options: {
				minZoom: 6,
				maxZoom: 19,
				bounds: [[50.5, 3.25], [54, 7.6]],
				attribution: 'Kaartgegevens &copy; <a href="kadaster.nl">Kadaster</a>'
			},
			variants: {
				'standaard': 'brtachtergrondkaart',
				'pastel': 'brtachtergrondkaartpastel',
				'grijs': 'brtachtergrondkaartgrijs',
				'luchtfoto': {
					'url': 'https://geodata.nationaalgeoregister.nl/luchtfoto/rgb/wmts/1.0.0/2016_ortho25/EPSG:3857/{z}/{x}/{y}.png',
				}
			}
		},
		NASAGIBS: {
			url: 'https://map1.vis.earthdata.nasa.gov/wmts-webmerc/{variant}/default/{time}/{tilematrixset}{maxZoom}/{z}/{y}/{x}.{format}',
			options: {
				attribution:
					'Imagery provided by services from the Global Imagery Browse Services (GIBS), operated by the NASA/GSFC/Earth Science Data and Information System ' +
					'(<a href="https://earthdata.nasa.gov">ESDIS</a>) with funding provided by NASA/HQ.',
				bounds: [[-85.0511287776, -179.999999975], [85.0511287776, 179.999999975]],
				minZoom: 1,
				maxZoom: 9,
				format: 'jpg',
				time: '',
				tilematrixset: 'GoogleMapsCompatible_Level'
			},
			variants: {
				ModisTerraTrueColorCR: 'MODIS_Terra_CorrectedReflectance_TrueColor',
				ModisTerraBands367CR: 'MODIS_Terra_CorrectedReflectance_Bands367',
				ViirsEarthAtNight2012: {
					options: {
						variant: 'VIIRS_CityLights_2012',
						maxZoom: 8
					}
				},
				ModisTerraLSTDay: {
					options: {
						variant: 'MODIS_Terra_Land_Surface_Temp_Day',
						format: 'png',
						maxZoom: 7,
						opacity: 0.75
					}
				},
				ModisTerraSnowCover: {
					options: {
						variant: 'MODIS_Terra_Snow_Cover',
						format: 'png',
						maxZoom: 8,
						opacity: 0.75
					}
				},
				ModisTerraAOD: {
					options: {
						variant: 'MODIS_Terra_Aerosol',
						format: 'png',
						maxZoom: 6,
						opacity: 0.75
					}
				},
				ModisTerraChlorophyll: {
					options: {
						variant: 'MODIS_Terra_Chlorophyll_A',
						format: 'png',
						maxZoom: 7,
						opacity: 0.75
					}
				}
			}
		},
		NLS: {
			// NLS maps are copyright National library of Scotland.
			// http://maps.nls.uk/projects/api/index.html
			// Please contact NLS for anything other than non-commercial low volume usage
			//
			// Map sources: Ordnance Survey 1:1m to 1:63K, 1920s-1940s
			//   z0-9  - 1:1m
			//  z10-11 - quarter inch (1:253440)
			//  z12-18 - one inch (1:63360)
			url: 'https://nls-{s}.tileserver.com/nls/{z}/{x}/{y}.jpg',
			options: {
				attribution: '<a href="http://geo.nls.uk/maps/">National Library of Scotland Historic Maps</a>',
				bounds: [[49.6, -12], [61.7, 3]],
				minZoom: 1,
				maxZoom: 18,
				subdomains: '0123',
			}
		},
		JusticeMap: {
			// Justice Map (http://www.justicemap.org/)
			// Visualize race and income data for your community, county and country.
			// Includes tools for data journalists, bloggers and community activists.
			url: 'http://www.justicemap.org/tile/{size}/{variant}/{z}/{x}/{y}.png',
			options: {
				attribution: '<a href="http://www.justicemap.org/terms.php">Justice Map</a>',
				// one of 'county', 'tract', 'block'
				size: 'county',
				// Bounds for USA, including Alaska and Hawaii
				bounds: [[14, -180], [72, -56]]
			},
			variants: {
				income: 'income',
				americanIndian: 'indian',
				asian: 'asian',
				black: 'black',
				hispanic: 'hispanic',
				multi: 'multi',
				nonWhite: 'nonwhite',
				white: 'white',
				plurality: 'plural'
			}
		},
		Wikimedia: {
			url: 'https://maps.wikimedia.org/osm-intl/{z}/{x}/{y}{r}.png',
			options: {
				attribution: '<a href="https://wikimediafoundation.org/wiki/Maps_Terms_of_Use">Wikimedia</a>',
				minZoom: 1,
				maxZoom: 19
			}
		},
		GeoportailFrance: {
			url: 'https://wxs.ign.fr/{apikey}/geoportail/wmts?REQUEST=GetTile&SERVICE=WMTS&VERSION=1.0.0&STYLE={style}&TILEMATRIXSET=PM&FORMAT={format}&LAYER={variant}&TILEMATRIX={z}&TILEROW={y}&TILECOL={x}',
			options: {
				attribution: '<a target="_blank" href="https://www.geoportail.gouv.fr/">Geoportail France</a>',
				bounds: [[-75, -180], [81, 180]],
				minZoom: 2,
				maxZoom: 18,
				// Get your own geoportail apikey here : http://professionnels.ign.fr/ign/contrats/
				// NB : 'choisirgeoportail' is a demonstration key that comes with no guarantee
				apikey: 'choisirgeoportail',
				format: 'image/jpeg',
				style : 'normal',
				variant: 'GEOGRAPHICALGRIDSYSTEMS.MAPS.SCAN-EXPRESS.STANDARD'
			},
			variants: {
				parcels: {
					options : {
						variant: 'CADASTRALPARCELS.PARCELS',
						maxZoom: 20,
						style : 'bdparcellaire',
						format: 'image/png'
					}
				},
				ignMaps: 'GEOGRAPHICALGRIDSYSTEMS.MAPS',
				maps: 'GEOGRAPHICALGRIDSYSTEMS.MAPS.SCAN-EXPRESS.STANDARD',
				orthos: {
					options: {
						maxZoom: 19,
						variant: 'ORTHOIMAGERY.ORTHOPHOTOS'
					}
				}
			}
		},
		OneMapSG: {
			url: 'https://maps-{s}.onemap.sg/v3/{variant}/{z}/{x}/{y}.png',
			options: {
				variant: 'Default',
				minZoom: 11,
				maxZoom: 18,
				bounds: [[1.56073, 104.11475], [1.16, 103.502]],
				attribution: '<img src="https://docs.onemap.sg/maps/images/oneMap64-01.png" style="height:20px;width:20px;"/> New OneMap | Map data &copy; contributors, <a href="http://SLA.gov.sg">Singapore Land Authority</a>'
			},
			variants: {
				Default: 'Default',
				Night: 'Night',
				Original: 'Original',
				Grey: 'Grey',
				LandLot: 'LandLot'
			}
		}
	};

	L.tileLayer.provider = function (provider, options) {
		return new L.TileLayer.Provider(provider, options);
	};

	return L;
}));

(function ($) {

    class GeoJsonAutocomplete {
        constructor(inputElement, options = {}) {
          this.inputElement = inputElement;
          this.settings = Object.assign({
            geojsonServiceAddress: "https://nominatim.openstreetmap.org/search",
            placeholderMessage: "Search...",
            foundRecordsMessage: "showing results.",
            limit: 10,
            notFoundMessage: "not found.",
            notFoundHint: "Make sure your search criteria is correct and try again.",
            drawColor: "blue",
            pointGeometryZoomLevel: -1,
            pagingActive: true,
            map_obj: null,
            onSelect: null, // this is the callback
          }, options);
      
          this.activeResult = -1;
          this.resultCount = 0;
          this.lastSearch = "";
          this.searchLayer = null;
          this.focusLayer = null;
          this.searchLayerType = 0;
          this.features = [];
          this.featureCollection = [];
          this.offset = 0;
          this.collapseOnBlur = true;
      
          this._init();
        }
      
        _init() {
          const $input = $(this.inputElement);
          $input.parent().addClass("searchContainer wpgmp-autocomplete-wrapper");
          $input.val("");
      
          $input.on("keyup", this._debounce((e) => this._handleKeyUp(e), 300));
          $input.on("focus", () => this._showResults());
          $input.on("blur", (e) => this._handleBlur(e));
        }
      
        _handleKeyUp(event) {
          const target = $(event.target);
          const keyCode = event.keyCode;
      
          switch (keyCode) {
            case 13:
              this._search(target);
              break;
            case 38:
              this._prevResult(target);
              break;
            case 40:
              this._nextResult(target);
              break;
            case 37:
            case 39:
              break;
            default:
              if (target.val().length > 0) {
                this.offset = 0;
                this._fetchResults(false, target);
              } else {
                this._clearResults(target);
              }
              break;
          }
        }
      
        _fetchResults(withPaging, target) {
          this.activeResult = -1;
          this.features = [];
          this.featureCollection = [];
          this.lastSearch = target.val();
      
          const query = {
            q: this.lastSearch,
            limit: this.settings.limit + (withPaging ? 1 : 0),
            format: "json",
            addressdetails: 1,
          };
      
          if (this.settings.pagingActive) query.offset = this.offset;
      
          $.getJSON(this.settings.geojsonServiceAddress, query)
            .done((json) => {
              this.resultCount = json.length;
              this.features = json;
              this.featureCollection = withPaging ? json.slice(0, json.length - 1) : json;
              this._renderDropdown(withPaging, target);
            })
            .fail(() => this._handleNoResults(target));
        }
      
        _renderDropdown(withPaging, target) {
          const $parent = $(target).parent();
          $parent.find(".resultsDiv").remove();
      
          const $resultsDiv = $(`
            <div class='result resultsDiv'>
              <ul class='list resultList wpgmp-autosuggest-results'></ul>
            </div>
          `);
          $parent.append($resultsDiv);
      
          const $list = $resultsDiv.find("ul");
      
          const loopCount = this.features.length - (withPaging ? 1 : 0);
      
          for (let i = 0; i < loopCount; i++) {
            const feature = this.features[i];
            const $item = $(`
              <li class='listResult listElement${i}' data-index='${i}'>
                <span class='content listElementContent${i}'>
                  <strong>${feature.display_name}</strong><br/>
                  <small>${feature.lat}, ${feature.lon}</small>
                </span>
              </li>
            `);
            $list.append($item);
      
            $item.on("mouseenter", () => this._highlightItem(i, target));
            $item.on("mouseleave", () => this._unhighlightItem(i, target));
            $item.on("mousedown", () => this._selectItem(i, target));
          }
      
          // You can also add paging and styling logic here.
        }
      
        _highlightItem(index, target) {
          $(target).parent().find(`.listElement${index}`).addClass("mouseover");
        }
      
        _unhighlightItem(index, target) {
          $(target).parent().find(`.listElement${index}`).removeClass("mouseover");
        }
      
        _selectItem(index, target) {
          this.activeResult = index;
          this._fillSearchBox(target);
        }
      
        _showResults() {
          $(this.inputElement).parent().find(".wpgmp-autosuggest-results").show();
        }
      
        _handleBlur(e) {
            const input = e.target;
            const resultsDiv = $(input).closest('.searchContainer').find('.resultsDiv');
    
            setTimeout(() => {
                // Only hide if mouse is NOT on the results dropdown
                if (this.collapseOnBlur && !resultsDiv.is(':hover')) {
                resultsDiv.find('.wpgmp-autosuggest-results').hide();
                } else {
                this.collapseOnBlur = true;
                $(input).focus(); // optional: re-focus if needed
                }
            }, 150);
            }
    
      
        _clearResults(target) {
          this.activeResult = -1;
          this.features = [];
          this.lastSearch = "";
          $(target).parent().find(".resultsDiv").remove();
        }
      
        _handleNoResults(target) {
          this._clearResults(target);
          $(target).parent().append(`<div class='resultsDiv'><i>${this.lastSearch} ${this.settings.notFoundMessage}</i><p><small>${this.settings.notFoundHint}</small></p></div>`);
        }
      
        _search(target) {
          this._fetchResults(this.settings.pagingActive, target);
        }
      
        _prevResult(target) {
          if (this.resultCount <= 0) return;
          if (this.activeResult > 0) this.activeResult--;
          this._fillSearchBox(target);
        }
      
        _nextResult(target) {
          if (this.resultCount <= 0) return;
          if (this.activeResult < this.resultCount - 1) this.activeResult++;
          this._fillSearchBox(target);
        }
      
        _fillSearchBox(target) {
          const feature = this.features[this.activeResult];
          if (!feature) return;
          
          if (this.settings && typeof this.settings.onSelect === 'function') {
            this.settings.onSelect({
                target,        // input element
                feature,       // selected GeoJSON result
              });
          }
          
          
        }
      
        _debounce(fn, delay) {
          let timer = null;
          return function (...args) {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
          };
        }
      }
      
     
        $.fn.GeoJsonAutocomplete = function (userOptions) {
          return this.each(function () {
            if (!this._geoJsonAutocompleteInstance) {
              this._geoJsonAutocompleteInstance = new GeoJsonAutocomplete(this, userOptions);
            }
          });
        };
      })(jQuery);
    
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['leaflet'], factory);
    } else if (typeof module !== 'undefined') {
        // Node/CommonJS
        module.exports = factory(require('leaflet'));
    } else {
        // Browser globals
        if (typeof window.L === 'undefined') {
            throw new Error('Leaflet must be loaded first');
        }
        factory(window.L);
    }
}(function (L) {
    L.Control.Fullscreen = L.Control.extend({
        options: {
            position: 'topleft',
            title: {
                'false': 'View Fullscreen',
                'true': 'Exit Fullscreen'
            }
        },

        onAdd: function (map) {
            var container = L.DomUtil.create('div', 'leaflet-control-fullscreen leaflet-bar leaflet-control');

            this.link = L.DomUtil.create('a', 'leaflet-control-fullscreen-button leaflet-bar-part', container);
            this.link.href = '#';

            this._map = map;
            this._map.on('fullscreenchange', this._toggleTitle, this);
            this._toggleTitle();

            L.DomEvent.on(this.link, 'click', this._click, this);

            return container;
        },

        _click: function (e) {
            L.DomEvent.stopPropagation(e);
            L.DomEvent.preventDefault(e);
            this._map.toggleFullscreen(this.options);
        },

        _toggleTitle: function() {
            this.link.title = this.options.title[this._map.isFullscreen()];
        }
    });

    L.Map.include({
        isFullscreen: function () {
            return this._isFullscreen || false;
        },

        toggleFullscreen: function (options) {
            var container = this.getContainer();
            if (this.isFullscreen()) {
                if (options && options.pseudoFullscreen) {
                    this._disablePseudoFullscreen(container);
                } else if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitCancelFullScreen) {
                    document.webkitCancelFullScreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                } else {
                    this._disablePseudoFullscreen(container);
                }
            } else {
                if (options && options.pseudoFullscreen) {
                    this._enablePseudoFullscreen(container);
                } else if (container.requestFullscreen) {
                    container.requestFullscreen();
                } else if (container.mozRequestFullScreen) {
                    container.mozRequestFullScreen();
                } else if (container.webkitRequestFullscreen) {
                    container.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
                } else if (container.msRequestFullscreen) {
                    container.msRequestFullscreen();
                } else {
                    this._enablePseudoFullscreen(container);
                }
            }

        },

        _enablePseudoFullscreen: function (container) {
            L.DomUtil.addClass(container, 'leaflet-pseudo-fullscreen');
            this._setFullscreen(true);
            this.fire('fullscreenchange');
        },

        _disablePseudoFullscreen: function (container) {
            L.DomUtil.removeClass(container, 'leaflet-pseudo-fullscreen');
            this._setFullscreen(false);
            this.fire('fullscreenchange');
        },

        _setFullscreen: function(fullscreen) {
            this._isFullscreen = fullscreen;
            var container = this.getContainer();
            if (fullscreen) {
                L.DomUtil.addClass(container, 'leaflet-fullscreen-on');
            } else {
                L.DomUtil.removeClass(container, 'leaflet-fullscreen-on');
            }
            this.invalidateSize();
        },

        _onFullscreenChange: function (e) {
            var fullscreenElement =
                document.fullscreenElement ||
                document.mozFullScreenElement ||
                document.webkitFullscreenElement ||
                document.msFullscreenElement;

            if (fullscreenElement === this.getContainer() && !this._isFullscreen) {
                this._setFullscreen(true);
                this.fire('fullscreenchange');
            } else if (fullscreenElement !== this.getContainer() && this._isFullscreen) {
                this._setFullscreen(false);
                this.fire('fullscreenchange');
            }
        }
    });

    L.Map.mergeOptions({
        fullscreenControl: false
    });

    L.Map.addInitHook(function () {
        if (this.options.fullscreenControl) {
            this.fullscreenControl = new L.Control.Fullscreen(this.options.fullscreenControl);
            this.addControl(this.fullscreenControl);
        }

        var fullscreenchange;

        if ('onfullscreenchange' in document) {
            fullscreenchange = 'fullscreenchange';
        } else if ('onmozfullscreenchange' in document) {
            fullscreenchange = 'mozfullscreenchange';
        } else if ('onwebkitfullscreenchange' in document) {
            fullscreenchange = 'webkitfullscreenchange';
        } else if ('onmsfullscreenchange' in document) {
            fullscreenchange = 'MSFullscreenChange';
        }

        if (fullscreenchange) {
            var onFullscreenChange = L.bind(this._onFullscreenChange, this);

            this.whenReady(function () {
                L.DomEvent.on(document, fullscreenchange, onFullscreenChange);
            });

            this.on('unload', function () {
                L.DomEvent.off(document, fullscreenchange, onFullscreenChange);
            });
        }
    });

    L.control.fullscreen = function (options) {
        return new L.Control.Fullscreen(options);
    };
}));
/*!
Copyright (c) 2016 Dominik Moritz

This file is part of the leaflet locate control. It is licensed under the MIT license.
You can find the project at: https://github.com/domoritz/leaflet-locatecontrol
*/
(function (factory, window) {
     // see https://github.com/Leaflet/Leaflet/blob/master/PLUGIN-GUIDE.md#module-loaders
     // for details on how to structure a leaflet plugin.

    // define an AMD module that relies on 'leaflet'
    if (typeof define === 'function' && define.amd) {
        define(['leaflet'], factory);

    // define a Common JS module that relies on 'leaflet'
    } else if (typeof exports === 'object') {
        if (typeof window !== 'undefined' && window.L) {
            module.exports = factory(L);
        } else {
            module.exports = factory(require('leaflet'));
        }
    }

    // attach your plugin to the global 'L' variable
    if (typeof window !== 'undefined' && window.L){
        window.L.Control.Locate = factory(L);
    }
} (function (L) {
    var LDomUtilApplyClassesMethod = function(method, element, classNames) {
        classNames = classNames.split(' ');
        classNames.forEach(function(className) {
            L.DomUtil[method].call(this, element, className);
        });
    };

    var addClasses = function(el, names) { LDomUtilApplyClassesMethod('addClass', el, names); };
    var removeClasses = function(el, names) { LDomUtilApplyClassesMethod('removeClass', el, names); };

    /**
     * Compatible with L.Circle but a true marker instead of a path
     */
    var LocationMarker = L.Marker.extend({
        initialize: function (latlng, options) {
            L.Util.setOptions(this, options);
            this._latlng = latlng;
            this.createIcon();
        },

        /**
         * Create a styled circle location marker
         */
        createIcon: function() {
            var opt = this.options;

            var style = '';

            if (opt.color !== undefined) {
                style += 'stroke:'+opt.color+';';
            }
            if (opt.weight !== undefined) {
                style += 'stroke-width:'+opt.weight+';';
            }
            if (opt.fillColor !== undefined) {
                style += 'fill:'+opt.fillColor+';';
            }
            if (opt.fillOpacity !== undefined) {
                style += 'fill-opacity:'+opt.fillOpacity+';';
            }
            if (opt.opacity !== undefined) {
                style += 'opacity:'+opt.opacity+';';
            }

            var icon = this._getIconSVG(opt, style);

            this._locationIcon = L.divIcon({
                className: icon.className,
                html: icon.svg,
                iconSize: [icon.w,icon.h],
            });

            this.setIcon(this._locationIcon);
        },

        /**
         * Return the raw svg for the shape
         *
         * Split so can be easily overridden
         */
        _getIconSVG: function(options, style) {
            var r = options.radius;
            var w = options.weight;
            var s = r + w;
            var s2 = s * 2;
            var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="'+s2+'" height="'+s2+'" version="1.1" viewBox="-'+s+' -'+s+' '+s2+' '+s2+'">' +
            '<circle r="'+r+'" style="'+style+'" />' +
            '</svg>';
            return {
                className: 'leaflet-control-locate-location',
                svg: svg,
                w: s2,
                h: s2
            };
        },

        setStyle: function(style) {
            L.Util.setOptions(this, style);
            this.createIcon();
        }
    });

    var CompassMarker = LocationMarker.extend({
        initialize: function (latlng, heading, options) {
            L.Util.setOptions(this, options);
            this._latlng = latlng;
            this._heading = heading;
            this.createIcon();
        },

        setHeading: function(heading) {
            this._heading = heading;
        },

        /**
         * Create a styled arrow compass marker
         */
        _getIconSVG: function(options, style) {
            var r = options.radius;
            var w = (options.width + options.weight);
            var h = (r+options.depth + options.weight)*2;
            var path = 'M0,0 l'+(options.width/2)+','+options.depth+' l-'+(w)+',0 z';
            var svgstyle = 'transform: rotate('+this._heading+'deg)';
            var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="'+(w)+'" height="'+h+'" version="1.1" viewBox="-'+(w/2)+' 0 '+w+' '+h+'" style="'+svgstyle+'">'+
            '<path d="'+path+'" style="'+style+'" />'+
            '</svg>';
            return {
                className: 'leaflet-control-locate-heading',
                svg: svg,
                w: w,
                h: h
            };
        },
    });


    var LocateControl = L.Control.extend({
        options: {
            /** Position of the control */
            position: 'topleft',
            /** The layer that the user's location should be drawn on. By default creates a new layer. */
            layer: undefined,
            /**
             * Automatically sets the map view (zoom and pan) to the user's location as it updates.
             * While the map is following the user's location, the control is in the `following` state,
             * which changes the style of the control and the circle marker.
             *
             * Possible values:
             *  - false: never updates the map view when location changes.
             *  - 'once': set the view when the location is first determined
             *  - 'always': always updates the map view when location changes.
             *              The map view follows the user's location.
             *  - 'untilPan': like 'always', except stops updating the
             *                view if the user has manually panned the map.
             *                The map view follows the user's location until she pans.
             *  - 'untilPanOrZoom': (default) like 'always', except stops updating the
             *                view if the user has manually panned the map.
             *                The map view follows the user's location until she pans.
             */
            setView: 'untilPanOrZoom',
            /** Keep the current map zoom level when setting the view and only pan. */
            keepCurrentZoomLevel: false,
            /**
             * This callback can be used to override the viewport tracking
             * This function should return a LatLngBounds object.
             *
             * For example to extend the viewport to ensure that a particular LatLng is visible:
             *
             * getLocationBounds: function(locationEvent) {
             *    return locationEvent.bounds.extend([-33.873085, 151.219273]);
             * },
             */
            getLocationBounds: function (locationEvent) {
                return locationEvent.bounds;
            },
            /** Smooth pan and zoom to the location of the marker. Only works in Leaflet 1.0+. */
            flyTo: false,
            /**
             * The user location can be inside and outside the current view when the user clicks on the
             * control that is already active. Both cases can be configures separately.
             * Possible values are:
             *  - 'setView': zoom and pan to the current location
             *  - 'stop': stop locating and remove the location marker
             */
            clickBehavior: {
                /** What should happen if the user clicks on the control while the location is within the current view. */
                inView: 'stop',
                /** What should happen if the user clicks on the control while the location is outside the current view. */
                outOfView: 'setView',
                /**
                 * What should happen if the user clicks on the control while the location is within the current view
                 * and we could be following but are not. Defaults to a special value which inherits from 'inView';
                 */
                inViewNotFollowing: 'inView',
            },
            /**
             * If set, save the map bounds just before centering to the user's
             * location. When control is disabled, set the view back to the
             * bounds that were saved.
             */
            returnToPrevBounds: false,
            /**
             * Keep a cache of the location after the user deactivates the control. If set to false, the user has to wait
             * until the locate API returns a new location before they see where they are again.
             */
            cacheLocation: true,
            /** If set, a circle that shows the location accuracy is drawn. */
            drawCircle: true,
            /** If set, the marker at the users' location is drawn. */
            drawMarker: true,
            /** If set and supported then show the compass heading */
            showCompass: true,
            /** The class to be used to create the marker. For example L.CircleMarker or L.Marker */
            markerClass: LocationMarker,
            /** The class us be used to create the compass bearing arrow */
            compassClass: CompassMarker,
            /** Accuracy circle style properties. NOTE these styles should match the css animations styles */
            circleStyle: {
                className:   'leaflet-control-locate-circle',
                color:       '#136AEC',
                fillColor:   '#136AEC',
                fillOpacity: 0.15,
                weight:      0
            },
            /** Inner marker style properties. Only works if your marker class supports `setStyle`. */
            markerStyle: {
                className:   'leaflet-control-locate-marker',
                color:       '#fff',
                fillColor:   '#2A93EE',
                fillOpacity: 1,
                weight:      3,
                opacity:     1,
                radius:      9
            },
            /** Compass */
            compassStyle: {
                fillColor:   '#2A93EE',
                fillOpacity: 1,
                weight:      0,
                color:       '#fff',
                opacity:     1,
                radius:      9, // How far is the arrow is from the center of of the marker
                width:       9, // Width of the arrow
                depth:       6  // Length of the arrow
            },
            /**
             * Changes to accuracy circle and inner marker while following.
             * It is only necessary to provide the properties that should change.
             */
            followCircleStyle: {},
            followMarkerStyle: {
                // color: '#FFA500',
                // fillColor: '#FFB000'
            },
            followCompassStyle: {},
            /** The CSS class for the icon. For example fa-location-arrow or fa-map-marker */
            icon: 'fa fa-map-marker',
            iconLoading: 'fa fa-spinner fa-spin',
            /** The element to be created for icons. For example span or i */
            iconElementTag: 'span',
            /** Padding around the accuracy circle. */
            circlePadding: [0, 0],
            /** Use metric units. */
            metric: true,
            /**
             * This callback can be used in case you would like to override button creation behavior.
             * This is useful for DOM manipulation frameworks such as angular etc.
             * This function should return an object with HtmlElement for the button (link property) and the icon (icon property).
             */
            createButtonCallback: function (container, options) {
                var link = L.DomUtil.create('a', 'leaflet-bar-part leaflet-bar-part-single', container);
                link.title = options.strings.title;
                var icon = L.DomUtil.create(options.iconElementTag, options.icon, link);
                return { link: link, icon: icon };
            },
            /** This event is called in case of any location error that is not a time out error. */
            onLocationError: function(err, control) {
                alert(err.message);
            },
            /**
             * This event is called when the user's location is outside the bounds set on the map.
             * The event is called repeatedly when the location changes.
             */
            onLocationOutsideMapBounds: function(control) {
                control.stop();
                alert(control.options.strings.outsideMapBoundsMsg);
            },
            /** Display a pop-up when the user click on the inner marker. */
            showPopup: true,
            strings: {
                title: "Show me where I am",
                metersUnit: "meters",
                feetUnit: "feet",
                popup: "You are within {distance} {unit} from this point",
                outsideMapBoundsMsg: "You seem located outside the boundaries of the map"
            },
            /** The default options passed to leaflets locate method. */
            locateOptions: {
                maxZoom: Infinity,
                watch: true,  // if you overwrite this, visualization cannot be updated
                setView: false // have to set this to false because we have to
                               // do setView manually
            }
        },

        initialize: function (options) {
            // set default options if nothing is set (merge one step deep)
            for (var i in options) {
                if (typeof this.options[i] === 'object') {
                    L.extend(this.options[i], options[i]);
                } else {
                    this.options[i] = options[i];
                }
            }

            // extend the follow marker style and circle from the normal style
            this.options.followMarkerStyle = L.extend({}, this.options.markerStyle, this.options.followMarkerStyle);
            this.options.followCircleStyle = L.extend({}, this.options.circleStyle, this.options.followCircleStyle);
            this.options.followCompassStyle = L.extend({}, this.options.compassStyle, this.options.followCompassStyle);
        },

        /**
         * Add control to map. Returns the container for the control.
         */
        onAdd: function (map) {
            var container = L.DomUtil.create('div',
                'leaflet-control-locate leaflet-bar leaflet-control');

            this._layer = this.options.layer || new L.LayerGroup();
            this._layer.addTo(map);
            this._event = undefined;
            this._compassHeading = null;
            this._prevBounds = null;

            var linkAndIcon = this.options.createButtonCallback(container, this.options);
            this._link = linkAndIcon.link;
            this._icon = linkAndIcon.icon;

            L.DomEvent
                .on(this._link, 'click', L.DomEvent.stopPropagation)
                .on(this._link, 'click', L.DomEvent.preventDefault)
                .on(this._link, 'click', this._onClick, this)
                .on(this._link, 'dblclick', L.DomEvent.stopPropagation);

            this._resetVariables();

            this._map.on('unload', this._unload, this);

            return container;
        },

        /**
         * This method is called when the user clicks on the control.
         */
        _onClick: function() {
            this._justClicked = true;
            var wasFollowing =  this._isFollowing();
            this._userPanned = false;
            this._userZoomed = false;

            if (this._active && !this._event) {
                // click while requesting
                this.stop();
            } else if (this._active && this._event !== undefined) {
                var behaviors = this.options.clickBehavior;
                var behavior = behaviors.outOfView;
                if (this._map.getBounds().contains(this._event.latlng)) {
                    behavior = wasFollowing ? behaviors.inView : behaviors.inViewNotFollowing;
                }

                // Allow inheriting from another behavior
                if (behaviors[behavior]) {
                    behavior = behaviors[behavior];
                }

                switch (behavior) {
                    case 'setView':
                        this.setView();
                        break;
                    case 'stop':
                        this.stop();
                        if (this.options.returnToPrevBounds) {
                            var f = this.options.flyTo ? this._map.flyToBounds : this._map.fitBounds;
                            f.bind(this._map)(this._prevBounds);
                        }
                        break;
                }
            } else {
                if (this.options.returnToPrevBounds) {
                  this._prevBounds = this._map.getBounds();
                }
                this.start();
            }

            this._updateContainerStyle();
        },

        /**
         * Starts the plugin:
         * - activates the engine
         * - draws the marker (if coordinates available)
         */
        start: function() {
            this._activate();

            if (this._event) {
                this._drawMarker(this._map);

                // if we already have a location but the user clicked on the control
                if (this.options.setView) {
                    this.setView();
                }
            }
            this._updateContainerStyle();
        },

        /**
         * Stops the plugin:
         * - deactivates the engine
         * - reinitializes the button
         * - removes the marker
         */
        stop: function() {
            this._deactivate();

            this._cleanClasses();
            this._resetVariables();

            this._removeMarker();
        },

        /**
         * Keep the control active but stop following the location
         */
        stopFollowing: function() {
            this._userPanned = true;
            this._updateContainerStyle();
            this._drawMarker();
        },

        /**
         * This method launches the location engine.
         * It is called before the marker is updated,
         * event if it does not mean that the event will be ready.
         *
         * Override it if you want to add more functionalities.
         * It should set the this._active to true and do nothing if
         * this._active is true.
         */
        _activate: function() {
            if (!this._active) {
                this._map.locate(this.options.locateOptions);
                this._active = true;

                // bind event listeners
                this._map.on('locationfound', this._onLocationFound, this);
                this._map.on('locationerror', this._onLocationError, this);
                this._map.on('dragstart', this._onDrag, this);
                this._map.on('zoomstart', this._onZoom, this);
                this._map.on('zoomend', this._onZoomEnd, this);
                if (this.options.showCompass) {
                    if ('ondeviceorientationabsolute' in window) {
                        L.DomEvent.on(window, 'deviceorientationabsolute', this._onDeviceOrientation, this);
                    } else if ('ondeviceorientation' in window) {
                        L.DomEvent.on(window, 'deviceorientation', this._onDeviceOrientation, this);
                    }
                }
            }
        },

        /**
         * Called to stop the location engine.
         *
         * Override it to shutdown any functionalities you added on start.
         */
        _deactivate: function() {
            this._map.stopLocate();
            this._active = false;

            if (!this.options.cacheLocation) {
                this._event = undefined;
            }

            // unbind event listeners
            this._map.off('locationfound', this._onLocationFound, this);
            this._map.off('locationerror', this._onLocationError, this);
            this._map.off('dragstart', this._onDrag, this);
            this._map.off('zoomstart', this._onZoom, this);
            this._map.off('zoomend', this._onZoomEnd, this);
            if (this.options.showCompass) {
                this._compassHeading = null;
                if ('ondeviceorientationabsolute' in window) {
                    L.DomEvent.off(window, 'deviceorientationabsolute', this._onDeviceOrientation, this);
                } else if ('ondeviceorientation' in window) {
                    L.DomEvent.off(window, 'deviceorientation', this._onDeviceOrientation, this);
                }
            }
        },

        /**
         * Zoom (unless we should keep the zoom level) and an to the current view.
         */
        setView: function() {
            this._drawMarker();
            if (this._isOutsideMapBounds()) {
                this._event = undefined;  // clear the current location so we can get back into the bounds
                this.options.onLocationOutsideMapBounds(this);
            } else {
                if (this.options.keepCurrentZoomLevel) {
                    var f = this.options.flyTo ? this._map.flyTo : this._map.panTo;
                    f.bind(this._map)([this._event.latitude, this._event.longitude]);
                } else {
                    var f = this.options.flyTo ? this._map.flyToBounds : this._map.fitBounds;
                    // Ignore zoom events while setting the viewport as these would stop following
                    this._ignoreEvent = true;
                    f.bind(this._map)(this.options.getLocationBounds(this._event), {
                        padding: this.options.circlePadding,
                        maxZoom: this.options.locateOptions.maxZoom
                    });
                    L.Util.requestAnimFrame(function(){
                        // Wait until after the next animFrame because the flyTo can be async
                        this._ignoreEvent = false;
                    }, this);

                }
            }
        },

        /**
         *
         */
        _drawCompass: function() {
            if (!this._event) {
                return;
            }

            var latlng = this._event.latlng;

            if (this.options.showCompass && latlng && this._compassHeading !== null) {
                var cStyle = this._isFollowing() ? this.options.followCompassStyle : this.options.compassStyle;
                if (!this._compass) {
                    this._compass = new this.options.compassClass(latlng, this._compassHeading, cStyle).addTo(this._layer);
                } else {
                    this._compass.setLatLng(latlng);
                    this._compass.setHeading(this._compassHeading);
                    // If the compassClass can be updated with setStyle, update it.
                    if (this._compass.setStyle) {
                        this._compass.setStyle(cStyle);
                    }
                }
                // 
            }
            if (this._compass && (!this.options.showCompass || this._compassHeading === null)) {
                this._compass.removeFrom(this._layer);
                this._compass = null;
            }
        },

        /**
         * Draw the marker and accuracy circle on the map.
         *
         * Uses the event retrieved from onLocationFound from the map.
         */
        _drawMarker: function() {
            if (this._event.accuracy === undefined) {
                this._event.accuracy = 0;
            }

            var radius = this._event.accuracy;
            var latlng = this._event.latlng;

            // circle with the radius of the location's accuracy
            if (this.options.drawCircle) {
                var style = this._isFollowing() ? this.options.followCircleStyle : this.options.circleStyle;

                if (!this._circle) {
                    this._circle = L.circle(latlng, radius, style).addTo(this._layer);
                } else {
                    this._circle.setLatLng(latlng).setRadius(radius).setStyle(style);
                }
            }

            var distance, unit;
            if (this.options.metric) {
                distance = radius.toFixed(0);
                unit =  this.options.strings.metersUnit;
            } else {
                distance = (radius * 3.2808399).toFixed(0);
                unit = this.options.strings.feetUnit;
            }

            // small inner marker
            if (this.options.drawMarker) {
                var mStyle = this._isFollowing() ? this.options.followMarkerStyle : this.options.markerStyle;
                if (!this._marker) {
                    this._marker = new this.options.markerClass(latlng, mStyle).addTo(this._layer);
                } else {
                    this._marker.setLatLng(latlng);
                    // If the markerClass can be updated with setStyle, update it.
                    if (this._marker.setStyle) {
                        this._marker.setStyle(mStyle);
                    }
                }
            }

            this._drawCompass();

            var t = this.options.strings.popup;
            if (this.options.showPopup && t && this._marker) {
                this._marker
                    .bindPopup(L.Util.template(t, {distance: distance, unit: unit}))
                    ._popup.setLatLng(latlng);
            }
            if (this.options.showPopup && t && this._compass) {
                this._compass
                    .bindPopup(L.Util.template(t, {distance: distance, unit: unit}))
                    ._popup.setLatLng(latlng);
            }
        },

        /**
         * Remove the marker from map.
         */
        _removeMarker: function() {
            this._layer.clearLayers();
            this._marker = undefined;
            this._circle = undefined;
        },

        /**
         * Unload the plugin and all event listeners.
         * Kind of the opposite of onAdd.
         */
        _unload: function() {
            this.stop();
            this._map.off('unload', this._unload, this);
        },

        /**
         * Sets the compass heading
         */
        _setCompassHeading: function(angle) {
            if (!isNaN(parseFloat(angle)) && isFinite(angle)) {
                angle = Math.round(angle);

                this._compassHeading = angle;
                L.Util.requestAnimFrame(this._drawCompass, this);
            } else {
                this._compassHeading = null;
            }
        },

        /**
         * If the compass fails calibration just fail safely and remove the compass
         */
        _onCompassNeedsCalibration: function() {
            this._setCompassHeading();
        },

        /**
         * Process and normalise compass events
         */
        _onDeviceOrientation: function(e) {
            if (!this._active) {
                return;
            }

            if (e.webkitCompassHeading) {
                // iOS
                this._setCompassHeading(e.webkitCompassHeading);
            } else if (e.absolute && e.alpha) {
                // Android
                this._setCompassHeading(360 - e.alpha)
            }
        },

        /**
         * Calls deactivate and dispatches an error.
         */
        _onLocationError: function(err) {
            // ignore time out error if the location is watched
            if (err.code == 3 && this.options.locateOptions.watch) {
                return;
            }

            this.stop();
            this.options.onLocationError(err, this);
        },

        /**
         * Stores the received event and updates the marker.
         */
        _onLocationFound: function(e) {
            // no need to do anything if the location has not changed
            if (this._event &&
                (this._event.latlng.lat === e.latlng.lat &&
                 this._event.latlng.lng === e.latlng.lng &&
                     this._event.accuracy === e.accuracy)) {
                return;
            }

            if (!this._active) {
                // we may have a stray event
                return;
            }

            this._event = e;

            this._drawMarker();
            this._updateContainerStyle();

            switch (this.options.setView) {
                case 'once':
                    if (this._justClicked) {
                        this.setView();
                    }
                    break;
                case 'untilPan':
                    if (!this._userPanned) {
                        this.setView();
                    }
                    break;
                case 'untilPanOrZoom':
                    if (!this._userPanned && !this._userZoomed) {
                        this.setView();
                    }
                    break;
                case 'always':
                    this.setView();
                    break;
                case false:
                    // don't set the view
                    break;
            }

            this._justClicked = false;
        },

        /**
         * When the user drags. Need a separate event so we can bind and unbind event listeners.
         */
        _onDrag: function() {
            // only react to drags once we have a location
            if (this._event && !this._ignoreEvent) {
                this._userPanned = true;
                this._updateContainerStyle();
                this._drawMarker();
            }
        },

        /**
         * When the user zooms. Need a separate event so we can bind and unbind event listeners.
         */
        _onZoom: function() {
            // only react to drags once we have a location
            if (this._event && !this._ignoreEvent) {
                this._userZoomed = true;
                this._updateContainerStyle();
                this._drawMarker();
            }
        },

        /**
         * After a zoom ends update the compass and handle sideways zooms
         */
        _onZoomEnd: function() {
            if (this._event) {
                this._drawCompass();
            }

            if (this._event && !this._ignoreEvent) {
                // If we have zoomed in and out and ended up sideways treat it as a pan
                if (this._marker && !this._map.getBounds().pad(-.3).contains(this._marker.getLatLng())) {
                    this._userPanned = true;
                    this._updateContainerStyle();
                    this._drawMarker();
                }
            }
        },

        /**
         * Compute whether the map is following the user location with pan and zoom.
         */
        _isFollowing: function() {
            if (!this._active) {
                return false;
            }

            if (this.options.setView === 'always') {
                return true;
            } else if (this.options.setView === 'untilPan') {
                return !this._userPanned;
            } else if (this.options.setView === 'untilPanOrZoom') {
                return !this._userPanned && !this._userZoomed;
            }
        },

        /**
         * Check if location is in map bounds
         */
        _isOutsideMapBounds: function() {
            if (this._event === undefined) {
                return false;
            }
            return this._map.options.maxBounds &&
                !this._map.options.maxBounds.contains(this._event.latlng);
        },

        /**
         * Toggles button class between following and active.
         */
        _updateContainerStyle: function() {
            if (!this._container) {
                return;
            }

            if (this._active && !this._event) {
                // active but don't have a location yet
                this._setClasses('requesting');
            } else if (this._isFollowing()) {
                this._setClasses('following');
            } else if (this._active) {
                this._setClasses('active');
            } else {
                this._cleanClasses();
            }
        },

        /**
         * Sets the CSS classes for the state.
         */
        _setClasses: function(state) {
            if (state == 'requesting') {
                removeClasses(this._container, "active following");
                addClasses(this._container, "requesting");

                removeClasses(this._icon, this.options.icon);
                addClasses(this._icon, this.options.iconLoading);
            } else if (state == 'active') {
                removeClasses(this._container, "requesting following");
                addClasses(this._container, "active");

                removeClasses(this._icon, this.options.iconLoading);
                addClasses(this._icon, this.options.icon);
            } else if (state == 'following') {
                removeClasses(this._container, "requesting");
                addClasses(this._container, "active following");

                removeClasses(this._icon, this.options.iconLoading);
                addClasses(this._icon, this.options.icon);
            }
        },

        /**
         * Removes all classes from button.
         */
        _cleanClasses: function() {
            L.DomUtil.removeClass(this._container, "requesting");
            L.DomUtil.removeClass(this._container, "active");
            L.DomUtil.removeClass(this._container, "following");

            removeClasses(this._icon, this.options.iconLoading);
            addClasses(this._icon, this.options.icon);
        },

        /**
         * Reinitializes state variables.
         */
        _resetVariables: function() {
            // whether locate is active or not
            this._active = false;

            // true if the control was clicked for the first time
            // we need this so we can pan and zoom once we have the location
            this._justClicked = false;

            // true if the user has panned the map after clicking the control
            this._userPanned = false;

            // true if the user has zoomed the map after clicking the control
            this._userZoomed = false;
        }
    });

    L.control.locate = function (options) {
        return new L.Control.Locate(options);
    };

    return LocateControl;
}, window));
(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(_dereq_,module,exports){function corslite(url,callback,cors){var sent=false;if(typeof window.XMLHttpRequest==="undefined"){return callback(Error("Browser not supported"))}if(typeof cors==="undefined"){var m=url.match(/^\s*https?:\/\/[^\/]*/);cors=m&&m[0]!==location.protocol+"//"+location.hostname+(location.port?":"+location.port:"")}var x=new window.XMLHttpRequest;function isSuccessful(status){return status>=200&&status<300||status===304}if(cors&&!("withCredentials"in x)){x=new window.XDomainRequest;var original=callback;callback=function(){if(sent){original.apply(this,arguments)}else{var that=this,args=arguments;setTimeout(function(){original.apply(that,args)},0)}}}function loaded(){if(x.status===undefined||isSuccessful(x.status))callback.call(x,null,x);else callback.call(x,x,null)}if("onload"in x){x.onload=loaded}else{x.onreadystatechange=function readystate(){if(x.readyState===4){loaded()}}}x.onerror=function error(evt){callback.call(this,evt||true,null);callback=function(){}};x.onprogress=function(){};x.ontimeout=function(evt){callback.call(this,evt,null);callback=function(){}};x.onabort=function(evt){callback.call(this,evt,null);callback=function(){}};x.open("GET",url,true);x.send(null);sent=true;return x}if(typeof module!=="undefined")module.exports=corslite},{}],2:[function(_dereq_,module,exports){"use strict";var polyline={};function py2_round(value){return Math.floor(Math.abs(value)+.5)*Math.sign(value)}function encode(current,previous,factor){current=py2_round(current*factor);previous=py2_round(previous*factor);var coordinate=current-previous;coordinate<<=1;if(current-previous<0){coordinate=~coordinate}var output="";while(coordinate>=32){output+=String.fromCharCode((32|coordinate&31)+63);coordinate>>=5}output+=String.fromCharCode(coordinate+63);return output}polyline.decode=function(str,precision){var index=0,lat=0,lng=0,coordinates=[],shift=0,result=0,byte=null,latitude_change,longitude_change,factor=Math.pow(10,precision||5);while(index<str.length){byte=null;shift=0;result=0;do{byte=str.charCodeAt(index++)-63;result|=(byte&31)<<shift;shift+=5}while(byte>=32);latitude_change=result&1?~(result>>1):result>>1;shift=result=0;do{byte=str.charCodeAt(index++)-63;result|=(byte&31)<<shift;shift+=5}while(byte>=32);longitude_change=result&1?~(result>>1):result>>1;lat+=latitude_change;lng+=longitude_change;coordinates.push([lat/factor,lng/factor])}return coordinates};polyline.encode=function(coordinates,precision){if(!coordinates.length){return""}var factor=Math.pow(10,precision||5),output=encode(coordinates[0][0],0,factor)+encode(coordinates[0][1],0,factor);for(var i=1;i<coordinates.length;i++){var a=coordinates[i],b=coordinates[i-1];output+=encode(a[0],b[0],factor);output+=encode(a[1],b[1],factor)}return output};function flipped(coords){var flipped=[];for(var i=0;i<coords.length;i++){flipped.push(coords[i].slice().reverse())}return flipped}polyline.fromGeoJSON=function(geojson,precision){if(geojson&&geojson.type==="Feature"){geojson=geojson.geometry}if(!geojson||geojson.type!=="LineString"){throw new Error("Input must be a GeoJSON LineString")}return polyline.encode(flipped(geojson.coordinates),precision)};polyline.toGeoJSON=function(str,precision){var coords=polyline.decode(str,precision);return{type:"LineString",coordinates:flipped(coords)}};if(typeof module==="object"&&module.exports){module.exports=polyline}},{}],3:[function(_dereq_,module,exports){var languages=_dereq_("./languages");var instructions=languages.instructions;var grammars=languages.grammars;var abbreviations=languages.abbreviations;module.exports=function(version){Object.keys(instructions).forEach(function(code){if(!instructions[code][version]){throw"invalid version "+version+": "+code+" not supported"}});return{capitalizeFirstLetter:function(language,string){return string.charAt(0).toLocaleUpperCase(language)+string.slice(1)},ordinalize:function(language,number){if(!language)throw new Error("No language code provided");return instructions[language][version].constants.ordinalize[number.toString()]||""},directionFromDegree:function(language,degree){if(!language)throw new Error("No language code provided");if(!degree&&degree!==0){return""}else if(degree>=0&&degree<=20){return instructions[language][version].constants.direction.north}else if(degree>20&&degree<70){return instructions[language][version].constants.direction.northeast}else if(degree>=70&&degree<=110){return instructions[language][version].constants.direction.east}else if(degree>110&&degree<160){return instructions[language][version].constants.direction.southeast}else if(degree>=160&&degree<=200){return instructions[language][version].constants.direction.south}else if(degree>200&&degree<250){return instructions[language][version].constants.direction.southwest}else if(degree>=250&&degree<=290){return instructions[language][version].constants.direction.west}else if(degree>290&&degree<340){return instructions[language][version].constants.direction.northwest}else if(degree>=340&&degree<=360){return instructions[language][version].constants.direction.north}else{throw new Error("Degree "+degree+" invalid")}},laneConfig:function(step){if(!step.intersections||!step.intersections[0].lanes)throw new Error("No lanes object");var config=[];var currentLaneValidity=null;step.intersections[0].lanes.forEach(function(lane){if(currentLaneValidity===null||currentLaneValidity!==lane.valid){if(lane.valid){config.push("o")}else{config.push("x")}currentLaneValidity=lane.valid}});return config.join("")},getWayName:function(language,step,options){var classes=options?options.classes||[]:[];if(typeof step!=="object")throw new Error("step must be an Object");if(!language)throw new Error("No language code provided");if(!Array.isArray(classes))throw new Error("classes must be an Array or undefined");var wayName;var name=step.name||"";var ref=(step.ref||"").split(";")[0];if(name===step.ref){name=""}name=name.replace(" ("+step.ref+")","");var wayMotorway=classes.indexOf("motorway")!==-1;if(name&&ref&&name!==ref&&!wayMotorway){var phrase=instructions[language][version].phrase["name and ref"]||instructions.en[version].phrase["name and ref"];wayName=this.tokenize(language,phrase,{name:name,ref:ref},options)}else if(name&&ref&&wayMotorway&&/\d/.test(ref)){wayName=options&&options.formatToken?options.formatToken("ref",ref):ref}else if(!name&&ref){wayName=options&&options.formatToken?options.formatToken("ref",ref):ref}else{wayName=options&&options.formatToken?options.formatToken("name",name):name}return wayName},compile:function(language,step,opts){if(!language)throw new Error("No language code provided");if(languages.supportedCodes.indexOf(language)===-1)throw new Error("language code "+language+" not loaded");if(!step.maneuver)throw new Error("No step maneuver provided");var options=opts||{};var type=step.maneuver.type;var modifier=step.maneuver.modifier;var mode=step.mode;var side=step.driving_side;if(!type){throw new Error("Missing step maneuver type")}if(type!=="depart"&&type!=="arrive"&&!modifier){throw new Error("Missing step maneuver modifier")}if(!instructions[language][version][type]){console.log("Encountered unknown instruction type: "+type);type="turn"}var instructionObject;if(instructions[language][version].modes[mode]){instructionObject=instructions[language][version].modes[mode]}else{var omitSide=type==="off ramp"&&modifier.indexOf(side)>=0;if(instructions[language][version][type][modifier]&&!omitSide){instructionObject=instructions[language][version][type][modifier]}else{instructionObject=instructions[language][version][type].default}}var laneInstruction;switch(type){case"use lane":laneInstruction=instructions[language][version].constants.lanes[this.laneConfig(step)];if(!laneInstruction){instructionObject=instructions[language][version]["use lane"].no_lanes}break;case"rotary":case"roundabout":if(step.rotary_name&&step.maneuver.exit&&instructionObject.name_exit){instructionObject=instructionObject.name_exit}else if(step.rotary_name&&instructionObject.name){instructionObject=instructionObject.name}else if(step.maneuver.exit&&instructionObject.exit){instructionObject=instructionObject.exit}else{instructionObject=instructionObject.default}break;default:}var wayName=this.getWayName(language,step,options);var instruction;if(step.destinations&&step.exits&&instructionObject.exit_destination){instruction=instructionObject.exit_destination}else if(step.destinations&&instructionObject.destination){instruction=instructionObject.destination}else if(step.exits&&instructionObject.exit){instruction=instructionObject.exit}else if(wayName&&instructionObject.name){instruction=instructionObject.name}else if(options.waypointName&&instructionObject.named){instruction=instructionObject.named}else{instruction=instructionObject.default}var destinations=step.destinations&&step.destinations.split(": ");var destinationRef=destinations&&destinations[0].split(",")[0];var destination=destinations&&destinations[1]&&destinations[1].split(",")[0];var firstDestination;if(destination&&destinationRef){firstDestination=destinationRef+": "+destination}else{firstDestination=destinationRef||destination||""}var nthWaypoint=options.legIndex>=0&&options.legIndex!==options.legCount-1?this.ordinalize(language,options.legIndex+1):"";var replaceTokens={way_name:wayName,destination:firstDestination,exit:(step.exits||"").split(";")[0],exit_number:this.ordinalize(language,step.maneuver.exit||1),rotary_name:step.rotary_name,lane_instruction:laneInstruction,modifier:instructions[language][version].constants.modifier[modifier],direction:this.directionFromDegree(language,step.maneuver.bearing_after),nth:nthWaypoint,waypoint_name:options.waypointName};return this.tokenize(language,instruction,replaceTokens,options)},grammarize:function(language,name,grammar){if(!language)throw new Error("No language code provided");if(name&&grammar&&grammars&&grammars[language]&&grammars[language][version]){var rules=grammars[language][version][grammar];if(rules){var n=" "+name+" ";var flags=grammars[language].meta.regExpFlags||"";rules.forEach(function(rule){var re=new RegExp(rule[0],flags);n=n.replace(re,rule[1])});return n.trim()}}return name},abbreviations:abbreviations,tokenize:function(language,instruction,tokens,options){if(!language)throw new Error("No language code provided");var that=this;var startedWithToken=false;var output=instruction.replace(/\{(\w+)(?::(\w+))?\}/g,function(token,tag,grammar,offset){var value=tokens[tag];if(typeof value==="undefined"){return token}value=that.grammarize(language,value,grammar);if(offset===0&&instructions[language].meta.capitalizeFirstLetter){startedWithToken=true;value=that.capitalizeFirstLetter(language,value)}if(options&&options.formatToken){value=options.formatToken(tag,value)}return value}).replace(/ {2}/g," ");if(!startedWithToken&&instructions[language].meta.capitalizeFirstLetter){return this.capitalizeFirstLetter(language,output)}return output}}}},{"./languages":4}],4:[function(_dereq_,module,exports){var instructionsDa=_dereq_("./languages/translations/da.json");var instructionsDe=_dereq_("./languages/translations/de.json");var instructionsEn=_dereq_("./languages/translations/en.json");var instructionsEo=_dereq_("./languages/translations/eo.json");var instructionsEs=_dereq_("./languages/translations/es.json");var instructionsEsEs=_dereq_("./languages/translations/es-ES.json");var instructionsFi=_dereq_("./languages/translations/fi.json");var instructionsFr=_dereq_("./languages/translations/fr.json");var instructionsHe=_dereq_("./languages/translations/he.json");var instructionsId=_dereq_("./languages/translations/id.json");var instructionsIt=_dereq_("./languages/translations/it.json");var instructionsKo=_dereq_("./languages/translations/ko.json");var instructionsMy=_dereq_("./languages/translations/my.json");var instructionsNl=_dereq_("./languages/translations/nl.json");var instructionsNo=_dereq_("./languages/translations/no.json");var instructionsPl=_dereq_("./languages/translations/pl.json");var instructionsPtBr=_dereq_("./languages/translations/pt-BR.json");var instructionsPtPt=_dereq_("./languages/translations/pt-PT.json");var instructionsRo=_dereq_("./languages/translations/ro.json");var instructionsRu=_dereq_("./languages/translations/ru.json");var instructionsSv=_dereq_("./languages/translations/sv.json");var instructionsTr=_dereq_("./languages/translations/tr.json");var instructionsUk=_dereq_("./languages/translations/uk.json");var instructionsVi=_dereq_("./languages/translations/vi.json");var instructionsZhHans=_dereq_("./languages/translations/zh-Hans.json");var grammarFr=_dereq_("./languages/grammar/fr.json");var grammarRu=_dereq_("./languages/grammar/ru.json");var abbreviationsBg=_dereq_("./languages/abbreviations/bg.json");var abbreviationsCa=_dereq_("./languages/abbreviations/ca.json");var abbreviationsDa=_dereq_("./languages/abbreviations/da.json");var ebbreviationsDe=_dereq_("./languages/abbreviations/de.json");var abbreviationsEn=_dereq_("./languages/abbreviations/en.json");var abbreviationsEs=_dereq_("./languages/abbreviations/es.json");var abbreviationsFr=_dereq_("./languages/abbreviations/fr.json");var abbreviationsHe=_dereq_("./languages/abbreviations/he.json");var abbreviationsHu=_dereq_("./languages/abbreviations/hu.json");var abbreviationsLt=_dereq_("./languages/abbreviations/lt.json");var abbreviationsNl=_dereq_("./languages/abbreviations/nl.json");var abbreviationsRu=_dereq_("./languages/abbreviations/ru.json");var abbreviationsSl=_dereq_("./languages/abbreviations/sl.json");var abbreviationsSv=_dereq_("./languages/abbreviations/sv.json");var abbreviationsUk=_dereq_("./languages/abbreviations/uk.json");var abbreviationsVi=_dereq_("./languages/abbreviations/vi.json");var instructions={da:instructionsDa,de:instructionsDe,en:instructionsEn,eo:instructionsEo,es:instructionsEs,"es-ES":instructionsEsEs,fi:instructionsFi,fr:instructionsFr,he:instructionsHe,id:instructionsId,it:instructionsIt,ko:instructionsKo,my:instructionsMy,nl:instructionsNl,no:instructionsNo,pl:instructionsPl,"pt-BR":instructionsPtBr,"pt-PT":instructionsPtPt,ro:instructionsRo,ru:instructionsRu,sv:instructionsSv,tr:instructionsTr,uk:instructionsUk,vi:instructionsVi,"zh-Hans":instructionsZhHans};var grammars={fr:grammarFr,ru:grammarRu};var abbreviations={bg:abbreviationsBg,ca:abbreviationsCa,da:abbreviationsDa,de:ebbreviationsDe,en:abbreviationsEn,es:abbreviationsEs,fr:abbreviationsFr,he:abbreviationsHe,hu:abbreviationsHu,lt:abbreviationsLt,nl:abbreviationsNl,ru:abbreviationsRu,sl:abbreviationsSl,sv:abbreviationsSv,uk:abbreviationsUk,vi:abbreviationsVi};module.exports={supportedCodes:Object.keys(instructions),instructions:instructions,grammars:grammars,abbreviations:abbreviations}},{"./languages/abbreviations/bg.json":5,"./languages/abbreviations/ca.json":6,"./languages/abbreviations/da.json":7,"./languages/abbreviations/de.json":8,"./languages/abbreviations/en.json":9,"./languages/abbreviations/es.json":10,"./languages/abbreviations/fr.json":11,"./languages/abbreviations/he.json":12,"./languages/abbreviations/hu.json":13,"./languages/abbreviations/lt.json":14,"./languages/abbreviations/nl.json":15,"./languages/abbreviations/ru.json":16,"./languages/abbreviations/sl.json":17,"./languages/abbreviations/sv.json":18,"./languages/abbreviations/uk.json":19,"./languages/abbreviations/vi.json":20,"./languages/grammar/fr.json":21,"./languages/grammar/ru.json":22,"./languages/translations/da.json":23,"./languages/translations/de.json":24,"./languages/translations/en.json":25,"./languages/translations/eo.json":26,"./languages/translations/es-ES.json":27,"./languages/translations/es.json":28,"./languages/translations/fi.json":29,"./languages/translations/fr.json":30,"./languages/translations/he.json":31,"./languages/translations/id.json":32,"./languages/translations/it.json":33,"./languages/translations/ko.json":34,"./languages/translations/my.json":35,"./languages/translations/nl.json":36,"./languages/translations/no.json":37,"./languages/translations/pl.json":38,"./languages/translations/pt-BR.json":39,"./languages/translations/pt-PT.json":40,"./languages/translations/ro.json":41,"./languages/translations/ru.json":42,"./languages/translations/sv.json":43,"./languages/translations/tr.json":44,"./languages/translations/uk.json":45,"./languages/translations/vi.json":46,"./languages/translations/zh-Hans.json":47}],5:[function(_dereq_,module,exports){module.exports={abbreviations:{"международен":"Межд","старши":"Стрш","възел":"Въз","пазар":"Mkt","светисвети":"СвСв","сестра":"сес","уилям":"Ум","апартаменти":"ап","езеро":"Ез","свети":"Св","център":"Ц-р","парк":"Пк","маршрут":"М-т","площад":"Пл","национален":"Нац","училище":"Уч","река":"Рек","поток":"П-к","район":"Р-н","крепост":"К-т","паметник":"Пам","университет":"Уни","Връх":"Вр","точка":"Точ","планина":"Пл","село":"с.","височини":"вис","младши":"Мл","станция":"С-я","проход":"Прох","баща":"Бщ"},classifications:{"шофиране":"Шоф","плавен":"Пл","място":"Мя","тераса":"Тер","магистрала":"М-ла","площад":"Пл","пеш":"Пеш","залив":"З-в","пътека":"П-ка","платно":"Пл","улица":"Ул","алея":"Ал","пешеходна":"Пеш","точка":"Тч","задминаване":"Задм","кръгово":"Кр","връх":"Вр","съд":"Сд","булевард":"Бул","път":"Път","скоростна":"Скор","мост":"Мо"},directions:{"северозапад":"СЗ","североизток":"СИ","югозапад":"ЮЗ","югоизток":"ЮИ","север":"С","изток":"И","юг":"Ю"}}},{}],6:[function(_dereq_,module,exports){module.exports={abbreviations:{comunicacions:"Com.","entitat de població":"Nucli",disseminat:"Diss.","cap de municipi":"Cap",indret:"Indr.",comarca:"Cca.","relleu del litoral":"Lit.",municipi:"Mun.","xarxa hidrogràfica":"Curs Fluv.",equipament:"Equip.",orografia:"Orogr.",barri:"Barri","edificació":"Edif.","edificació històrica":"Edif. Hist.","entitat descentralitzada":"E.M.D.","element hidrogràfic":"Hidr."},classifications:{rotonda:"Rot.",carrerada:"Ca.","jardí":"J.",paratge:"Pge.",pont:"Pont",lloc:"Lloc",rambla:"Rbla.",cases:"Cses.",barranc:"Bnc.",plana:"Plana","polígon":"Pol.",muralla:"Mur.","enllaç":"Ellaç","antiga carretera":"Actra",glorieta:"Glor.",autovia:"Autv.","prolongació":"Prol.","calçada":"Cda.",carretera:"Ctra.",pujada:"Pda.",torrent:"T.",disseminat:"Disse",barri:"B.","cinturó":"Cinto",passera:"Psera",sender:"Send.",carrer:"C.","sèquia":"Sèq.",blocs:"Bloc",rambleta:"Rblt.",partida:"Par.",costa:"Cos.",sector:"Sec.","corraló":"Crral","urbanització":"Urb.",autopista:"Autp.",grup:"Gr.",platja:"Pja.",jardins:"J.",complex:"Comp.",portals:"Ptals",finca:"Fin.",travessera:"Trav.","plaça":"Pl.",travessia:"Trv.","polígon industrial":"PI.",passatge:"Ptge.",apartaments:"Apmt.",mirador:"Mira.",antic:"Antic","accés":"Acc.","colònia":"Col.",corriol:"Crol.",portal:"Ptal.",porta:"Pta.",port:"Port","carreró":"Cró.",riera:"Ra.","circumval·lació":"Cval.",baixada:"Bda.",placeta:"Plta.",escala:"Esc.","gran via":"GV",rial:"Rial",conjunt:"Conj.",avinguda:"Av.",esplanada:"Esp.",cantonada:"Cant.",ronda:"Rda.",corredor:"Cdor.",drecera:"Drec.","passadís":"Pdís.",viaducte:"Vdct.",passeig:"Pg.","veïnat":"Veï."},directions:{sudest:"SE",sudoest:"SO",nordest:"NE",nordoest:"NO",est:"E",nord:"N",oest:"O",sud:"S"}}},{}],7:[function(_dereq_,module,exports){module.exports={abbreviations:{skole:"Sk.",ved:"v.",centrum:"C.",sankt:"Skt.",vestre:"v.",hospital:"Hosp.","stræde":"Str.",nordre:"Nr.",plads:"Pl.",universitet:"Uni.","vænge":"vg.",station:"St."},classifications:{avenue:"Ave",gammel:"Gl.",dronning:"Dronn.","sønder":"Sdr.","nørre":"Nr.",vester:"V.",vestre:"V.","øster":"Ø.","østre":"Ø.",boulevard:"Boul."},directions:{"sydøst":"SØ",nordvest:"NV",syd:"S","nordøst":"NØ",sydvest:"SV",vest:"V",nord:"N","øst":"Ø"}}},{}],8:[function(_dereq_,module,exports){module.exports={abbreviations:{},classifications:{},directions:{osten:"O",nordosten:"NO","süden":"S",nordwest:"NW",norden:"N","südost":"SO","südwest":"SW",westen:"W"}}},{}],9:[function(_dereq_,module,exports){module.exports={abbreviations:{square:"Sq",centre:"Ctr",sister:"Sr",lake:"Lk",fort:"Ft",route:"Rte",william:"Wm",national:"Nat’l",junction:"Jct",center:"Ctr",saint:"St",saints:"SS",station:"Sta",mount:"Mt",junior:"Jr",mountain:"Mtn",heights:"Hts",university:"Univ",school:"Sch",international:"Int’l",apartments:"Apts",crossing:"Xing",creek:"Crk",township:"Twp",downtown:"Dtwn",father:"Fr",senior:"Sr",point:"Pt",river:"Riv",market:"Mkt",village:"Vil",park:"Pk",memorial:"Mem"},classifications:{place:"Pl",circle:"Cir",bypass:"Byp",motorway:"Mwy",crescent:"Cres",road:"Rd",cove:"Cv",lane:"Ln",square:"Sq",street:"St",freeway:"Fwy",walk:"Wk",plaza:"Plz",parkway:"Pky",avenue:"Ave",pike:"Pk",drive:"Dr",highway:"Hwy",footway:"Ftwy",point:"Pt",court:"Ct",terrace:"Ter",walkway:"Wky",alley:"Aly",expressway:"Expy",bridge:"Br",boulevard:"Blvd",turnpike:"Tpk"},directions:{southeast:"SE",northwest:"NW",south:"S",west:"W",southwest:"SW",north:"N",east:"E",northeast:"NE"}}},{}],10:[function(_dereq_,module,exports){module.exports={abbreviations:{segunda:"2ª",octubre:"8bre",doctores:"Drs",doctora:"Dra",internacional:"Intl",doctor:"Dr",segundo:"2º","señorita":"Srta",doctoras:"Drs",primera:"1ª",primero:"1º",san:"S",colonia:"Col","doña":"Dña",septiembre:"7bre",diciembre:"10bre","señor":"Sr",ayuntamiento:"Ayto","señora":"Sra",tercera:"3ª",tercero:"3º",don:"D",santa:"Sta",ciudad:"Cdad",noviembre:"9bre",departamento:"Dep"},classifications:{camino:"Cmno",avenida:"Av",paseo:"Pº",autopista:"Auto",calle:"C",plaza:"Pza",carretera:"Crta"},directions:{este:"E",noreste:"NE",sur:"S",suroeste:"SO",noroeste:"NO",oeste:"O",sureste:"SE",norte:"N"}}},{}],11:[function(_dereq_,module,exports){module.exports={abbreviations:{"allée":"All","aérodrome":"Aérod","aéroport":"Aérop"},classifications:{centrale:"Ctrale",campings:"Camp.",urbains:"Urb.",mineure:"Min.",publique:"Publ.","supérieur":"Sup.","fédération":"Féd.","notre-dame":"ND",saint:"St","centre hospitalier régional":"CHR",exploitation:"Exploit.","général":"Gal",civiles:"Civ.",maritimes:"Marit.",aviation:"Aviat.",iii:"3","archéologique":"Archéo.",musical:"Music.",musicale:"Music.",immeuble:"Imm.",xv:"15","hôtel":"Hôt.",alpine:"Alp.",communale:"Commun.",v:"5",global:"Glob.","université":"Univ.","confédéral":"Conféd.",xx:"20",x:"10",piscine:"Pisc.",dimanche:"di.",fleuve:"Flv",postaux:"Post.",musicienne:"Music.","département":"Dépt","février":"Févr.",municipales:"Munic.",province:"Prov.","communautés":"Commtés",barrage:"Barr.",mercredi:"me.","présidentes":"Pdtes","cafétérias":"Cafét.","théâtral":"Thé.",viticulteur:"Vitic.",poste:"Post.","spécialisée":"Spéc.",agriculture:"Agric.",infirmier:"Infirm.",animation:"Anim.",mondiale:"Mond.","arrêt":"Arr.",zone:"zon.",municipaux:"Munic.",grand:"Gd",janvier:"Janv.",fondateur:"Fond.","première":"1re",municipale:"Munic.",direction:"Dir.",anonyme:"Anon.","départementale":"Dépt",moyens:"Moy.",novembre:"Nov.",jardin:"Jard.",petites:"Pet.","privé":"Priv.",centres:"Ctres",forestier:"Forest.",xiv:"14",africaines:"Afric.",sergent:"Sgt","européenne":"Eur.","privée":"Priv.","café":"Cfé",xix:"19",hautes:"Htes",major:"Mjr",vendredi:"ve.","municipalité":"Munic.","sous-préfecture":"Ss-préf.","spéciales":"Spéc.",secondaires:"Second.",viie:"7e",moyenne:"Moy.",commerciale:"Commerc.","région":"Rég.","américaines":"Amér.","américains":"Amér.",service:"Sce",professeur:"Prof.","départemental":"Dépt","hôtels":"Hôt.",mondiales:"Mond.",ire:"1re",caporal:"Capo.",militaire:"Milit.","lycée d'enseignement professionnel":"LEP",adjudant:"Adj.","médicale":"Méd.","conférences":"Confér.",universelle:"Univ.",xiie:"12e","supérieures":"Sup.",naturel:"Natur.","société nationale":"SN",hospitalier:"Hosp.",culturelle:"Cult.","américain":"Amér.","son altesse royale":"S.A.R.","infirmière":"Infirm.",viii:"8",fondatrice:"Fond.",madame:"Mme","métropolitain":"Métrop.",ophtalmologues:"Ophtalmos",xviie:"18e",viiie:"8e","commerçante":"Commerç.","centre d'enseignement du second degré":"CES",septembre:"Sept.",agriculteur:"Agric.",xiii:"13",pontifical:"Pontif.","cafétéria":"Cafét.",prince:"Pce",vie:"6e",archiduchesse:"Archid.",occidental:"Occ.",spectacles:"Spect.",camping:"Camp.","métro":"Mº",arrondissement:"Arrond.",viticole:"Vitic.",ii:"2","siècle":"Si.",chapelles:"Chap.",centre:"Ctre","sapeur-pompiers":"Sap.-pomp.","établissements":"Étabts","société anonyme":"SA",directeurs:"Dir.",vii:"7",culturel:"Cult.",central:"Ctral","métropolitaine":"Métrop.",administrations:"Admin.",amiraux:"Amir.",sur:"s/",premiers:"1ers","provence-alpes-côte d'azur":"PACA","cathédrale":"Cathéd.",iv:"4",postale:"Post.",social:"Soc.","spécialisé":"Spéc.",district:"Distr.",technologique:"Techno.",viticoles:"Vitic.",ix:"9","protégés":"Prot.",historiques:"Hist.",sous:"s/s",national:"Nal",ambassade:"Amb.","cafés":"Cfés",agronomie:"Agro.",sapeurs:"Sap.",petits:"Pet.",monsieur:"M.",boucher:"Bouch.",restaurant:"Restau.","lycée":"Lyc.",urbaine:"Urb.","préfecture":"Préf.",districts:"Distr.",civil:"Civ.","protégées":"Prot.",sapeur:"Sap.","théâtre":"Thé.","collège":"Coll.",mardi:"ma.","mémorial":"Mémor.",africain:"Afric.","républicaine":"Républ.",sociale:"Soc.","spécial":"Spéc.",technologie:"Techno.",charcuterie:"Charc.",commerces:"Commerc.",fluviale:"Flv",parachutistes:"Para.",primaires:"Prim.",directions:"Dir.","présidentiel":"Pdtl",nationales:"Nales","après":"apr.",samedi:"sa.","unité":"U.",xxiii:"23","associé":"Assoc.","électrique":"Électr.",populaire:"Pop.",asiatique:"Asiat.",navigable:"Navig.","présidente":"Pdte",xive:"14e","associés":"Assoc.",pompiers:"Pomp.",agricoles:"Agric.","élém":"Élém.","décembre":"Déc.","son altesse":"S.Alt.","après-midi":"a.-m.",mineures:"Min.",juillet:"Juil.",aviatrices:"Aviat.",fondation:"Fond.",pontificaux:"Pontif.",temple:"Tple","européennes":"Eur.","régionale":"Rég.",informations:"Infos",mondiaux:"Mond.",infanterie:"Infant.","archéologie":"Archéo.",dans:"d/",hospice:"Hosp.",spectacle:"Spect.","hôtels-restaurants":"Hôt.-Rest.","hôtel-restaurant":"Hôt.-Rest.","hélicoptère":"hélico",xixe:"19e",cliniques:"Clin.",docteur:"Dr",secondaire:"Second.",municipal:"Munic.","générale":"Gale","château":"Chât.","commerçant":"Commerç.",avril:"Avr.",clinique:"Clin.",urbaines:"Urb.",navale:"Nav.",navigation:"Navig.",asiatiques:"Asiat.",pontificales:"Pontif.",administrative:"Admin.",syndicat:"Synd.",lundi:"lu.",petite:"Pet.",maritime:"Marit.","métros":"Mº",enseignement:"Enseign.",fluviales:"Flv",historique:"Hist.","comtés":"Ctés","résidentiel":"Résid.",international:"Int.","supérieure":"Sup.","centre hospitalier universitaire":"CHU","confédération":"Conféd.",boucherie:"Bouch.",fondatrices:"Fond.","médicaux":"Méd.","européens":"Eur.",orientaux:"Ori.",naval:"Nav.","étang":"Étg",provincial:"Prov.",junior:"Jr","départementales":"Dépt",musique:"Musiq.",directrices:"Dir.","maréchal":"Mal",civils:"Civ.","protégé":"Prot.","établissement":"Étabt",trafic:"Traf.",aviateur:"Aviat.",archives:"Arch.",africains:"Afric.",maternelle:"Matern.",industrielle:"Ind.",administratif:"Admin.",oriental:"Ori.",universitaire:"Univ.",majeur:"Maj.",haute:"Hte",communal:"Commun.",petit:"Pet.",commune:"Commun.",exploitant:"Exploit.","conférence":"Confér.",monseigneur:"Mgr",pharmacien:"Pharm.",jeudi:"je.",primaire:"Prim.","hélicoptères":"hélicos",agronomique:"Agro.","médecin":"Méd.",ve:"5e",pontificale:"Pontif.",ier:"1er","cinéma":"Ciné",fluvial:"Flv",occidentaux:"Occ.","commerçants":"Commerç.",banque:"Bq",moyennes:"Moy.",pharmacienne:"Pharm.","démocratique":"Dém.","cinémas":"Cinés","spéciale":"Spéc.","présidents":"Pdts",directrice:"Dir.",vi:"6",basse:"Bas.",xve:"15e","état":"É.",aviateurs:"Aviat.",majeurs:"Maj.",infirmiers:"Infirm.","église":"Égl.","confédérale":"Conféd.",xxie:"21e",comte:"Cte","européen":"Eur.",union:"U.",pharmacie:"Pharm.","infirmières":"Infirm.","comté":"Cté",sportive:"Sport.","deuxième":"2e",xvi:"17",haut:"Ht","médicales":"Méd.","développé":"Dévelop.","bâtiment":"Bât.",commerce:"Commerc.",ive:"4e",associatif:"Assoc.",rural:"Rur.","cimetière":"Cim.","régional":"Rég.",ferroviaire:"Ferr.",vers:"v/","mosquée":"Mosq.",mineurs:"Min.",nautique:"Naut.","châteaux":"Chât.",sportif:"Sport.",mademoiselle:"Mle","école":"Éc.",doyen:"Doy.",industriel:"Ind.",chapelle:"Chap.","sociétés":"Stés",internationale:"Int.","coopératif":"Coop.",hospices:"Hosp.",xxii:"22",parachutiste:"Para.",alpines:"Alp.",civile:"Civ.",xvie:"17e","états":"É.","musée":"Msée",centrales:"Ctrales",globaux:"Glob.","supérieurs":"Sup.",syndicats:"Synd.","archevêque":"Archev.",docteurs:"Drs","bibliothèque":"Biblio.",lieutenant:"Lieut.","république":"Rép.","vétérinaire":"Vét.","départementaux":"Dépt",premier:"1er",fluviaux:"Flv","animé":"Anim.",orientales:"Ori.",technologiques:"Techno.",princesse:"Pse","routière":"Rout.","coopérative":"Coop.",scolaire:"Scol.","écoles":"Éc.",football:"Foot",territoriale:"Territ.",commercial:"Commerc.",mineur:"Min.","millénaires":"Mill.",association:"Assoc.",catholique:"Cathol.",administration:"Admin.",mairie:"Mair.",portuaire:"Port.",tertiaires:"Terti.","théâtrale":"Thé.",palais:"Pal.","troisième":"3e",directeur:"Dir.","vétérinaires":"Vét.","faculté":"Fac.",occidentales:"Occ.",viticulteurs:"Vitic.",xvii:"18",occidentale:"Occ.",amiral:"Amir.",professionnel:"Profess.",administratives:"Admin.",commerciales:"Commerc.",saints:"Sts",agronomes:"Agro.",stade:"Std","sous-préfet":"Ss-préf.",senior:"Sr",agronome:"Agro.",terrain:"Terr.",catholiques:"Cathol.","résidentielle":"Résid.",grands:"Gds",exploitants:"Exploit.",xiiie:"13e",croix:"Cx","généraux":"Gaux","crédit":"Créd.","cimetières":"Cim.",antenne:"Ant.","médical":"Méd.","collèges":"Coll.",musicien:"Music.",apostolique:"Apost.",postal:"Post.",territorial:"Territ.",urbanisme:"Urb.","préfectorale":"Préf.",fondateurs:"Fond.",information:"Info.","églises":"Égl.",ophtalmologue:"Ophtalmo","congrégation":"Congrég.",charcutier:"Charc.","étage":"ét.",consulat:"Consul.",public:"Publ.","ferrée":"Ferr.",matin:"mat.","société anonyme à responsabilité limitée":"SARL",monuments:"Mmts",protection:"Prot.",universel:"Univ.",nationale:"Nale","président":"Pdt",provinciale:"Prov.",agriculteurs:"Agric.","préfectoral":"Préf.",xxe:"20e",alpins:"Alp.",avant:"av.",infirmerie:"Infirm.","deux mil":"2000",rurale:"Rur.",administratifs:"Admin.",octobre:"Oct.",archipel:"Archip.","communauté":"Commté",globales:"Glob.",alpin:"Alp.","numéros":"Nºˢ","lieutenant-colonel":"Lieut.-Col.","jésus-christ":"J.-C.",agricole:"Agric.","sa majesté":"S.Maj.",associative:"Assoc.",xxi:"21","présidentielle":"Pdtle",moyen:"Moy.","fédéral":"Féd.",professionnelle:"Profess.",tertiaire:"Terti.",ixe:"9e","hôpital":"Hôp.",technologies:"Techno.",iiie:"3e","développement":"Dévelop.",monument:"Mmt","forestière":"Forest.","numéro":"Nº",viticulture:"Vitic.","traversière":"Traver.",technique:"Tech.","électriques":"Électr.",militaires:"Milit.",pompier:"Pomp.","américaine":"Amér.","préfet":"Préf.","congrégations":"Congrég.","pâtissier":"Pâtiss.",mondial:"Mond.",ophtalmologie:"Ophtalm.",sainte:"Ste",africaine:"Afric.",aviatrice:"Aviat.",doyens:"Doy.","société":"Sté",majeures:"Maj.",orientale:"Ori.","ministère":"Min.",archiduc:"Archid.",territoire:"Territ.",techniques:"Tech.","île-de-france":"IDF",globale:"Glob.",xe:"10e",xie:"11e",majeure:"Maj.",commerciaux:"Commerc.",maire:"Mair.","spéciaux":"Spéc.",grande:"Gde",messieurs:"MM",colonel:"Col.","millénaire":"Mill.",xi:"11",urbain:"Urb.","fédérale":"Féd.","ferré":"Ferr.","rivière":"Riv.","républicain":"Républ.",grandes:"Gdes","régiment":"Régim.",hauts:"Hts","catégorie":"Catég.",basses:"Bas.",xii:"12",agronomiques:"Agro.",iie:"2e","protégée":"Prot.","sapeur-pompier":"Sap.-pomp."},directions:{"est-nord-est":"ENE",
"nord-est":"NE",ouest:"O","sud-est":"SE","est-sud-est":"ESE","nord-nord-est":"NNE",sud:"S","nord-nord-ouest":"NNO","nord-ouest":"NO",nord:"N","ouest-sud-ouest":"OSO","ouest-nord-ouest":"ONO","sud-ouest":"SO","sud-sud-est":"SSE","sud-sud-ouest":"SSO",est:"E"}}},{}],12:[function(_dereq_,module,exports){module.exports={abbreviations:{"שדרות":"שד'"},classifications:{},directions:{}}},{}],13:[function(_dereq_,module,exports){module.exports={abbreviations:{},classifications:{},directions:{kelet:"K","északkelet":"ÉK","dél":"D","északnyugat":"ÉNY","észak":"É","délkelet":"DK","délnyugat":"DNY",nyugat:"NY"}}},{}],14:[function(_dereq_,module,exports){module.exports={abbreviations:{apartamentai:"Apt","aukštumos":"Aukš",centras:"Ctr","ežeras":"Ež",fortas:"Ft",greitkelis:"Grtkl",juosta:"Jst",kaimas:"Km",kalnas:"Kln",kelias:"Kl",kiemelis:"Kml",miestelis:"Mstl","miesto centras":"M.Ctr",mokykla:"Mok",nacionalinis:"Nac",paminklas:"Pmkl",parkas:"Pk",pusratis:"Psrt","sankryža":"Skrž","sesė":"Sesė",skveras:"Skv",stotis:"St","šv":"Šv",tarptautinis:"Trptaut","taškas":"Tšk","tėvas":"Tėv",turgus:"Tgs",universitetas:"Univ","upė":"Up",upelis:"Up",vieta:"Vt"},classifications:{"aikštė":"a.","alėja":"al.",aplinkkelis:"aplinkl.",autostrada:"auto.",bulvaras:"b.","gatvė":"g.",kelias:"kel.","krantinė":"krant.",prospektas:"pr.",plentas:"pl.",skersgatvis:"skg.",takas:"tak.",tiltas:"tlt."},directions:{"pietūs":"P",vakarai:"V","šiaurė":"Š","šiaurės vakarai":"ŠV","pietryčiai":"PR","šiaurės rytai":"ŠR",rytai:"R",pietvakariai:"PV"}}},{}],15:[function(_dereq_,module,exports){module.exports={abbreviations:{centrum:"Cntrm",nationaal:"Nat’l",berg:"Brg",meer:"Mr",kruising:"Krsng",toetreden:"Ttrdn"},classifications:{bypass:"Pass",brug:"Br",straat:"Str",rechtbank:"Rbank",snoek:"Snk",autobaan:"Baan",terras:"Trrs",punt:"Pt",plaza:"Plz",rijden:"Rijd",parkway:"Pky",inham:"Nham",snelweg:"Weg","halve maan":"Maan",cirkel:"Crkl",laan:"Ln",rijbaan:"Strook",weg:"Weg",lopen:"Lpn",autoweg:"Weg",boulevard:"Blvd",plaats:"Plts",steeg:"Stg",voetpad:"Stoep"},directions:{noordoost:"NO",westen:"W",zuiden:"Z",zuidwest:"ZW",oost:"O",zuidoost:"ZO",noordwest:"NW",noorden:"N"}}},{}],16:[function(_dereq_,module,exports){module.exports={abbreviations:{"апостола":"ап.","апостолов":"апп.","великомученика":"вмч","великомученицы":"вмц.","владение":"вл.","город":"г.","деревня":"д.","имени":"им.","мученика":"мч.","мучеников":"мчч.","мучениц":"мцц.","мученицы":"мц.","озеро":"о.","посёлок":"п.","преподобного":"прп.","преподобных":"прпп.","река":"р.","святителей":"свтт.","святителя":"свт.","священномученика":"сщмч.","священномучеников":"сщмчч.","станция":"ст.","участок":"уч."},classifications:{"проезд":"пр-д","проспект":"пр.","переулок":"пер.","набережная":"наб.","площадь":"пл.","шоссе":"ш.","бульвар":"б.","тупик":"туп.","улица":"ул."},directions:{"восток":"В","северо-восток":"СВ","юго-восток":"ЮВ","юго-запад":"ЮЗ","северо-запад":"СЗ","север":"С","запад":"З","юг":"Ю"}}},{}],17:[function(_dereq_,module,exports){module.exports={abbreviations:{},classifications:{},directions:{vzhod:"V",severovzhod:"SV",jug:"J",severozahod:"SZ",sever:"S",jugovzhod:"JV",jugozahod:"JZ",zahod:"Z"}}},{}],18:[function(_dereq_,module,exports){module.exports={abbreviations:{sankta:"s:ta",gamla:"G:la",sankt:"s:t"},classifications:{Bro:"Br"},directions:{norr:"N","sydöst":"SO","väster":"V","öster":"O","nordväst":"NV","sydväst":"SV","söder":"S","nordöst":"NO"}}},{}],19:[function(_dereq_,module,exports){module.exports={abbreviations:{},classifications:{},directions:{"схід":"Сх","північний схід":"ПнСх","південь":"Пд","північний захід":"ПнЗд","північ":"Пн","південний схід":"ПдСх","південний захід":"ПдЗх","захід":"Зх"}}},{}],20:[function(_dereq_,module,exports){module.exports={abbreviations:{"viện bảo tàng":"VBT","thị trấn":"Tt","đại học":"ĐH","căn cứ không quan":"CCKQ","câu lạc bộ":"CLB","bưu điện":"BĐ","khách sạn":"KS","khu du lịch":"KDL","khu công nghiệp":"KCN","khu nghỉ mát":"KNM","thị xã":"Tx","khu chung cư":"KCC","phi trường":"PT","trung tâm":"TT","tổng công ty":"TCty","trung học cơ sở":"THCS","sân bay quốc tế":"SBQT","trung học phổ thông":"THPT","cao đẳng":"CĐ","công ty":"Cty","sân bay":"SB","thành phố":"Tp","công viên":"CV","sân vận động":"SVĐ","linh mục":"LM","vườn quốc gia":"VQG"},classifications:{"huyện lộ":"HL","đường tỉnh":"ĐT","quốc lộ":"QL","xa lộ":"XL","hương lộ":"HL","tỉnh lộ":"TL","đường huyện":"ĐH","đường cao tốc":"ĐCT","đại lộ":"ĐL","việt nam":"VN","quảng trường":"QT","đường bộ":"ĐB"},directions:{"tây":"T",nam:"N","đông nam":"ĐN","đông bắc":"ĐB","tây nam":"TN","đông":"Đ","bắc":"B"}}},{}],21:[function(_dereq_,module,exports){module.exports={meta:{regExpFlags:"gi"},v5:{article:[["^ Acc[èe]s "," l’accès "],["^ Aire "," l’aire "],["^ All[ée]e "," l’allée "],["^ Anse "," l’anse "],["^ (L['’])?Autoroute "," l’autoroute "],["^ Avenue "," l’avenue "],["^ Barreau "," le barreau "],["^ Boulevard "," le boulevard "],["^ Chemin "," le chemin "],["^ Petit[\\- ]Chemin "," le petit chemin "],["^ Cit[ée] "," la cité "],["^ Clos "," le clos "],["^ Corniche "," la corniche "],["^ Cour "," la cour "],["^ Cours "," le cours "],["^ D[ée]viation "," la déviation "],["^ Entr[ée]e "," l’entrée "],["^ Esplanade "," l’esplanade "],["^ Galerie "," la galerie "],["^ Impasse "," l’impasse "],["^ Lotissement "," le lotissement "],["^ Mont[ée]e "," la montée "],["^ Parc "," le parc "],["^ Parvis "," le parvis "],["^ Passage "," le passage "],["^ Place "," la place "],["^ Petit[\\- ]Pont "," le petit-pont "],["^ Pont "," le pont "],["^ Promenade "," la promenade "],["^ Quai "," le quai "],["^ Rocade "," la rocade "],["^ Rond[\\- ]?Point "," le rond-point "],["^ Route "," la route "],["^ Rue "," la rue "],["^ Grande Rue "," la grande rue "],["^ Sente "," la sente "],["^ Sentier "," le sentier "],["^ Sortie "," la sortie "],["^ Souterrain "," le souterrain "],["^ Square "," le square "],["^ Terrasse "," la terrasse "],["^ Traverse "," la traverse "],["^ Tunnel "," le tunnel "],["^ Viaduc "," le viaduc "],["^ Villa "," la villa "],["^ Village "," le village "],["^ Voie "," la voie "],[" ([dl])'"," $1’"]],preposition:[["^ Le ","  du "],["^ Les ","  des "],["^ La ","  de La "],["^ Acc[èe]s ","  de l’accès "],["^ Aire ","  de l’aire "],["^ All[ée]e ","  de l’allée "],["^ Anse ","  de l’anse "],["^ (L['’])?Autoroute ","  de l’autoroute "],["^ Avenue ","  de l’avenue "],["^ Barreau ","  du barreau "],["^ Boulevard ","  du boulevard "],["^ Chemin ","  du chemin "],["^ Petit[\\- ]Chemin ","  du petit chemin "],["^ Cit[ée] ","  de la cité "],["^ Clos ","  du clos "],["^ Corniche ","  de la corniche "],["^ Cour ","  de la cour "],["^ Cours ","  du cours "],["^ D[ée]viation ","  de la déviation "],["^ Entr[ée]e ","  de l’entrée "],["^ Esplanade ","  de l’esplanade "],["^ Galerie ","  de la galerie "],["^ Impasse ","  de l’impasse "],["^ Lotissement ","  du lotissement "],["^ Mont[ée]e ","  de la montée "],["^ Parc ","  du parc "],["^ Parvis ","  du parvis "],["^ Passage ","  du passage "],["^ Place ","  de la place "],["^ Petit[\\- ]Pont ","  du petit-pont "],["^ Pont ","  du pont "],["^ Promenade ","  de la promenade "],["^ Quai ","  du quai "],["^ Rocade ","  de la rocade "],["^ Rond[\\- ]?Point ","  du rond-point "],["^ Route ","  de la route "],["^ Rue ","  de la rue "],["^ Grande Rue ","  de la grande rue "],["^ Sente ","  de la sente "],["^ Sentier ","  du sentier "],["^ Sortie ","  de la sortie "],["^ Souterrain ","  du souterrain "],["^ Square ","  du square "],["^ Terrasse ","  de la terrasse "],["^ Traverse ","  de la traverse "],["^ Tunnel ","  du tunnel "],["^ Viaduc ","  du viaduc "],["^ Villa ","  de la villa "],["^ Village ","  du village "],["^ Voie ","  de la voie "],["^ ([AÂÀEÈÉÊËIÎÏOÔUÙÛÜYŸÆŒ])","  d’$1"],["^ (\\S)","  de $1"],[" ([dl])'"," $1’"]],rotary:[["^ Le ","  le rond-point du "],["^ Les ","  le rond-point des "],["^ La ","  le rond-point de La "],["^ Acc[èe]s "," le rond-point de l’accès "],["^ Aire ","  le rond-point de l’aire "],["^ All[ée]e ","  le rond-point de l’allée "],["^ Anse ","  le rond-point de l’anse "],["^ (L['’])?Autoroute ","  le rond-point de l’autoroute "],["^ Avenue ","  le rond-point de l’avenue "],["^ Barreau ","  le rond-point du barreau "],["^ Boulevard ","  le rond-point du boulevard "],["^ Chemin ","  le rond-point du chemin "],["^ Petit[\\- ]Chemin ","  le rond-point du petit chemin "],["^ Cit[ée] ","  le rond-point de la cité "],["^ Clos ","  le rond-point du clos "],["^ Corniche ","  le rond-point de la corniche "],["^ Cour ","  le rond-point de la cour "],["^ Cours ","  le rond-point du cours "],["^ D[ée]viation ","  le rond-point de la déviation "],["^ Entr[ée]e ","  le rond-point de l’entrée "],["^ Esplanade ","  le rond-point de l’esplanade "],["^ Galerie ","  le rond-point de la galerie "],["^ Impasse ","  le rond-point de l’impasse "],["^ Lotissement ","  le rond-point du lotissement "],["^ Mont[ée]e ","  le rond-point de la montée "],["^ Parc ","  le rond-point du parc "],["^ Parvis ","  le rond-point du parvis "],["^ Passage ","  le rond-point du passage "],["^ Place ","  le rond-point de la place "],["^ Petit[\\- ]Pont ","  le rond-point du petit-pont "],["^ Pont ","  le rond-point du pont "],["^ Promenade ","  le rond-point de la promenade "],["^ Quai ","  le rond-point du quai "],["^ Rocade ","  le rond-point de la rocade "],["^ Rond[\\- ]?Point ","  le rond-point "],["^ Route ","  le rond-point de la route "],["^ Rue ","  le rond-point de la rue "],["^ Grande Rue ","  le rond-point de la grande rue "],["^ Sente ","  le rond-point de la sente "],["^ Sentier ","  le rond-point du sentier "],["^ Sortie ","  le rond-point de la sortie "],["^ Souterrain ","  le rond-point du souterrain "],["^ Square ","  le rond-point du square "],["^ Terrasse ","  le rond-point de la terrasse "],["^ Traverse ","  le rond-point de la traverse "],["^ Tunnel ","  le rond-point du tunnel "],["^ Viaduc ","  le rond-point du viaduc "],["^ Villa ","  le rond-point de la villa "],["^ Village ","  le rond-point du village "],["^ Voie ","  le rond-point de la voie "],["^ ([AÂÀEÈÉÊËIÎÏOÔUÙÛÜYŸÆŒ])","  le rond-point d’$1"],["^ (\\S)","  le rond-point de $1"],[" ([dl])'"," $1’"]],arrival:[["^ Le ","  au "],["^ Les ","  aux "],["^ La ","  à La "],["^ (\\S)","  à $1"],[" ([dl])'"," $1’"]]}}},{}],22:[function(_dereq_,module,exports){module.exports={meta:{regExpFlags:""},v5:{accusative:[['^ ([«"])'," трасса $1"],["^ (\\S+)ая [Аа]ллея "," $1ую аллею "],["^ (\\S+)ья [Аа]ллея "," $1ью аллею "],["^ (\\S+)яя [Аа]ллея "," $1юю аллею "],["^ (\\d+)-я (\\S+)ая [Аа]ллея "," $1-ю $2ую аллею "],["^ [Аа]ллея "," аллею "],["^ (\\S+)ая-(\\S+)ая [Уу]лица "," $1ую-$2ую улицу "],["^ (\\S+)ая [Уу]лица "," $1ую улицу "],["^ (\\S+)ья [Уу]лица "," $1ью улицу "],["^ (\\S+)яя [Уу]лица "," $1юю улицу "],["^ (\\d+)-я [Уу]лица "," $1-ю улицу "],["^ (\\d+)-я (\\S+)ая [Уу]лица "," $1-ю $2ую улицу "],["^ (\\S+)ая (\\S+)ая [Уу]лица "," $1ую $2ую улицу "],["^ (\\S+[вн])а [Уу]лица "," $1у улицу "],["^ (\\S+)ая (\\S+[вн])а [Уу]лица "," $1ую $2у улицу "],["^ Даньславля [Уу]лица "," Даньславлю улицу "],["^ Добрыня [Уу]лица "," Добрыню улицу "],["^ Людогоща [Уу]лица "," Людогощу улицу "],["^ [Уу]лица "," улицу "],["^ (\\d+)-я [Лл]иния "," $1-ю линию "],["^ (\\d+)-(\\d+)-я [Лл]иния "," $1-$2-ю линию "],["^ (\\S+)ая [Лл]иния "," $1ую линию "],["^ (\\S+)ья [Лл]иния "," $1ью линию "],["^ (\\S+)яя [Лл]иния "," $1юю линию "],["^ (\\d+)-я (\\S+)ая [Лл]иния "," $1-ю $2ую линию "],["^ [Лл]иния "," линию "],["^ (\\d+)-(\\d+)-я [Лл]инии "," $1-$2-ю линии "],["^ (\\S+)ая [Нн]абережная "," $1ую набережную "],["^ (\\S+)ья [Нн]абережная "," $1ью набережную "],["^ (\\S+)яя [Нн]абережная "," $1юю набережную "],["^ (\\d+)-я (\\S+)ая [Нн]абережная "," $1-ю $2ую набережную "],["^ [Нн]абережная "," набережную "],["^ (\\S+)ая [Пп]лощадь "," $1ую площадь "],["^ (\\S+)ья [Пп]лощадь "," $1ью площадь "],["^ (\\S+)яя [Пп]лощадь "," $1юю площадь "],["^ (\\S+[вн])а [Пп]лощадь "," $1у площадь "],["^ (\\d+)-я (\\S+)ая [Пп]лощадь "," $1-ю $2ую площадь "],["^ [Пп]лощадь "," площадь "],["^ (\\S+)ая [Пп]росека "," $1ую просеку "],["^ (\\S+)ья [Пп]росека "," $1ью просеку "],["^ (\\S+)яя [Пп]росека "," $1юю просеку "],["^ (\\d+)-я [Пп]росека "," $1-ю просеку "],["^ [Пп]росека "," просеку "],["^ (\\S+)ая [Ээ]стакада "," $1ую эстакаду "],["^ (\\S+)ья [Ээ]стакада "," $1ью эстакаду "],["^ (\\S+)яя [Ээ]стакада "," $1юю эстакаду "],["^ (\\d+)-я (\\S+)ая [Ээ]стакада "," $1-ю $2ую эстакаду "],["^ [Ээ]стакада "," эстакаду "],["^ (\\S+)ая [Мм]агистраль "," $1ую магистраль "],["^ (\\S+)ья [Мм]агистраль "," $1ью магистраль "],["^ (\\S+)яя [Мм]агистраль "," $1юю магистраль "],["^ (\\S+)ая (\\S+)ая [Мм]агистраль "," $1ую $2ую магистраль "],["^ (\\d+)-я (\\S+)ая [Мм]агистраль "," $1-ю $2ую магистраль "],["^ [Мм]агистраль "," магистраль "],["^ (\\S+)ая [Рр]азвязка "," $1ую развязку "],["^ (\\S+)ья [Рр]азвязка "," $1ью развязку "],["^ (\\S+)яя [Рр]азвязка "," $1юю развязку "],["^ (\\d+)-я (\\S+)ая [Рр]азвязка "," $1-ю $2ую развязку "],["^ [Рр]азвязка "," развязку "],["^ (\\S+)ая [Тт]расса "," $1ую трассу "],["^ (\\S+)ья [Тт]расса "," $1ью трассу "],["^ (\\S+)яя [Тт]расса "," $1юю трассу "],["^ (\\d+)-я (\\S+)ая [Тт]расса "," $1-ю $2ую трассу "],["^ [Тт]расса "," трассу "],["^ (\\S+)ая ([Аа]вто)?[Дд]орога "," $1ую $2дорогу "],["^ (\\S+)ья ([Аа]вто)?[Дд]орога "," $1ью $2дорогу "],["^ (\\S+)яя ([Аа]вто)?[Дд]орога "," $1юю $2дорогу "],["^ (\\S+)ая (\\S+)ая ([Аа]вто)?[Дд]орога "," $1ую $2ую $3дорогу "],["^ (\\d+)-я (\\S+)ая ([Аа]вто)?[Дд]орога "," $1-ю $2ую $3дорогу "],["^ ([Аа]вто)?[Дд]орога "," $1дорогу "],["^ (\\S+)ая [Дд]орожка "," $1ую дорожку "],["^ (\\S+)ья [Дд]орожка "," $1ью дорожку "],["^ (\\S+)яя [Дд]орожка "," $1юю дорожку "],["^ (\\d+)-я (\\S+)ая [Дд]орожка "," $1-ю $2ую дорожку "],["^ [Дд]орожка "," дорожку "],["^ (\\S+)ая [Кк]оса "," $1ую косу "],["^ (\\S+)ая [Хх]орда "," $1ую хорду "],["^ [Дд]убл[её]р "," дублёр "]],dative:[['^ ([«"])'," трасса $1"],["^ (\\S+)ая [Аа]ллея "," $1ой аллее "],["^ (\\S+)ья [Аа]ллея "," $1ьей аллее "],["^ (\\S+)яя [Аа]ллея "," $1ей аллее "],["^ (\\d+)-я (\\S+)ая [Аа]ллея "," $1-й $2ой аллее "],["^ [Аа]ллея "," аллее "],["^ (\\S+)ая-(\\S+)ая [Уу]лица "," $1ой-$2ой улице "],["^ (\\S+)ая [Уу]лица "," $1ой улице "],["^ (\\S+)ья [Уу]лица "," $1ьей улице "],["^ (\\S+)яя [Уу]лица "," $1ей улице "],["^ (\\d+)-я [Уу]лица "," $1-й улице "],["^ (\\d+)-я (\\S+)ая [Уу]лица "," $1-й $2ой улице "],["^ (\\S+)ая (\\S+)ая [Уу]лица "," $1ой $2ой улице "],["^ (\\S+[вн])а [Уу]лица "," $1ой улице "],["^ (\\S+)ая (\\S+[вн])а [Уу]лица "," $1ой $2ой улице "],["^ Даньславля [Уу]лица "," Даньславлей улице "],["^ Добрыня [Уу]лица "," Добрыней улице "],["^ Людогоща [Уу]лица "," Людогощей улице "],["^ [Уу]лица "," улице "],["^ (\\d+)-я [Лл]иния "," $1-й линии "],["^ (\\d+)-(\\d+)-я [Лл]иния "," $1-$2-й линии "],["^ (\\S+)ая [Лл]иния "," $1ой линии "],["^ (\\S+)ья [Лл]иния "," $1ьей линии "],["^ (\\S+)яя [Лл]иния "," $1ей линии "],["^ (\\d+)-я (\\S+)ая [Лл]иния "," $1-й $2ой линии "],["^ [Лл]иния "," линии "],["^ (\\d+)-(\\d+)-я [Лл]инии "," $1-$2-й линиям "],["^ (\\S+)ая [Нн]абережная "," $1ой набережной "],["^ (\\S+)ья [Нн]абережная "," $1ьей набережной "],["^ (\\S+)яя [Нн]абережная "," $1ей набережной "],["^ (\\d+)-я (\\S+)ая [Нн]абережная "," $1-й $2ой набережной "],["^ [Нн]абережная "," набережной "],["^ (\\S+)ая [Пп]лощадь "," $1ой площади "],["^ (\\S+)ья [Пп]лощадь "," $1ьей площади "],["^ (\\S+)яя [Пп]лощадь "," $1ей площади "],["^ (\\S+[вн])а [Пп]лощадь "," $1ой площади "],["^ (\\d+)-я (\\S+)ая [Пп]лощадь "," $1-й $2ой площади "],["^ [Пп]лощадь "," площади "],["^ (\\S+)ая [Пп]росека "," $1ой просеке "],["^ (\\S+)ья [Пп]росека "," $1ьей просеке "],["^ (\\S+)яя [Пп]росека "," $1ей просеке "],["^ (\\d+)-я [Пп]росека "," $1-й просеке "],["^ [Пп]росека "," просеке "],["^ (\\S+)ая [Ээ]стакада "," $1ой эстакаде "],["^ (\\S+)ья [Ээ]стакада "," $1ьей эстакаде "],["^ (\\S+)яя [Ээ]стакада "," $1ей эстакаде "],["^ (\\d+)-я (\\S+)ая [Ээ]стакада "," $1-й $2ой эстакаде "],["^ [Ээ]стакада "," эстакаде "],["^ (\\S+)ая [Мм]агистраль "," $1ой магистрали "],["^ (\\S+)ья [Мм]агистраль "," $1ьей магистрали "],["^ (\\S+)яя [Мм]агистраль "," $1ей магистрали "],["^ (\\S+)ая (\\S+)ая [Мм]агистраль "," $1ой $2ой магистрали "],["^ (\\d+)-я (\\S+)ая [Мм]агистраль "," $1-й $2ой магистрали "],["^ [Мм]агистраль "," магистрали "],["^ (\\S+)ая [Рр]азвязка "," $1ой развязке "],["^ (\\S+)ья [Рр]азвязка "," $1ьей развязке "],["^ (\\S+)яя [Рр]азвязка "," $1ей развязке "],["^ (\\d+)-я (\\S+)ая [Рр]азвязка "," $1-й $2ой развязке "],["^ [Рр]азвязка "," развязке "],["^ (\\S+)ая [Тт]расса "," $1ой трассе "],["^ (\\S+)ья [Тт]расса "," $1ьей трассе "],["^ (\\S+)яя [Тт]расса "," $1ей трассе "],["^ (\\d+)-я (\\S+)ая [Тт]расса "," $1-й $2ой трассе "],["^ [Тт]расса "," трассе "],["^ (\\S+)ая ([Аа]вто)?[Дд]орога "," $1ой $2дороге "],["^ (\\S+)ья ([Аа]вто)?[Дд]орога "," $1ьей $2дороге "],["^ (\\S+)яя ([Аа]вто)?[Дд]орога "," $1ей $2дороге "],["^ (\\S+)ая (\\S+)ая ([Аа]вто)?[Дд]орога "," $1ой $2ой $3дороге "],["^ (\\d+)-я (\\S+)ая ([Аа]вто)?[Дд]орога "," $1-й $2ой $3дороге "],["^ ([Аа]вто)?[Дд]орога "," $1дороге "],["^ (\\S+)ая [Дд]орожка "," $1ой дорожке "],["^ (\\S+)ья [Дд]орожка "," $1ьей дорожке "],["^ (\\S+)яя [Дд]орожка "," $1ей дорожке "],["^ (\\d+)-я (\\S+)ая [Дд]орожка "," $1-й $2ой дорожке "],["^ [Дд]орожка "," дорожке "],["^ (\\S+)во [Пп]оле "," $1ву полю "],["^ (\\S+)ая [Кк]оса "," $1ой косе "],["^ (\\S+)ая [Хх]орда "," $1ой хорде "],["^ (\\S+)[иоы]й [Пп]роток "," $1ому протоку "],["^ (\\S+н)ий [Бб]ульвар "," $1ему бульвару "],["^ (\\S+)[иоы]й [Бб]ульвар "," $1ому бульвару "],["^ (\\S+[иы]н) [Бб]ульвар "," $1у бульвару "],["^ (\\S+)[иоы]й (\\S+н)ий [Бб]ульвар "," $1ому $2ему бульвару "],["^ (\\S+н)ий (\\S+)[иоы]й [Бб]ульвар "," $1ему $2ому бульвару "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Бб]ульвар "," $1ому $2ому бульвару "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Бб]ульвар "," $1ому $2у бульвару "],["^ (\\d+)-й (\\S+н)ий [Бб]ульвар "," $1-му $2ему бульвару "],["^ (\\d+)-й (\\S+)[иоы]й [Бб]ульвар "," $1-му $2ому бульвару "],["^ (\\d+)-й (\\S+[иы]н) [Бб]ульвар "," $1-му $2у бульвару "],["^ [Бб]ульвар "," бульвару "],["^ [Дд]убл[её]р "," дублёру "],["^ (\\S+н)ий [Зз]аезд "," $1ему заезду "],["^ (\\S+)[иоы]й [Зз]аезд "," $1ому заезду "],["^ (\\S+[еёо]в) [Зз]аезд "," $1у заезду "],["^ (\\S+[иы]н) [Зз]аезд "," $1у заезду "],["^ (\\S+)[иоы]й (\\S+н)ий [Зз]аезд "," $1ому $2ему заезду "],["^ (\\S+н)ий (\\S+)[иоы]й [Зз]аезд "," $1ему $2ому заезду "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Зз]аезд "," $1ому $2ому заезду "],["^ (\\S+)[иоы]й (\\S+[еёо]в) [Зз]аезд "," $1ому $2у заезду "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Зз]аезд "," $1ому $2у заезду "],["^ (\\d+)-й (\\S+н)ий [Зз]аезд "," $1-му $2ему заезду "],["^ (\\d+)-й (\\S+)[иоы]й [Зз]аезд "," $1-му $2ому заезду "],["^ (\\d+)-й (\\S+[еёо]в) [Зз]аезд "," $1-му $2у заезду "],["^ (\\d+)-й (\\S+[иы]н) [Зз]аезд "," $1-му $2у заезду "],["^ [Зз]аезд "," заезду "],["^ (\\S+н)ий [Мм]ост "," $1ему мосту "],["^ (\\S+)[иоы]й [Мм]ост "," $1ому мосту "],["^ (\\S+[еёо]в) [Мм]ост "," $1у мосту "],["^ (\\S+[иы]н) [Мм]ост "," $1у мосту "],["^ (\\S+)[иоы]й (\\S+н)ий [Мм]ост "," $1ому $2ему мосту "],["^ (\\S+н)ий (\\S+)[иоы]й [Мм]ост "," $1ему $2ому мосту "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Мм]ост "," $1ому $2ому мосту "],["^ (\\S+)[иоы]й (\\S+[еёо]в) [Мм]ост "," $1ому $2у мосту "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Мм]ост "," $1ому $2у мосту "],["^ (\\d+)-й [Мм]ост "," $1-му мосту "],["^ (\\d+)-й (\\S+н)ий [Мм]ост "," $1-му $2ему мосту "],["^ (\\d+)-й (\\S+)[иоы]й [Мм]ост "," $1-му $2ому мосту "],["^ (\\d+)-й (\\S+[еёо]в) [Мм]ост "," $1-му $2у мосту "],["^ (\\d+)-й (\\S+[иы]н) [Мм]ост "," $1-му $2у мосту "],["^ [Мм]ост "," мосту "],["^ (\\S+н)ий [Оо]бход "," $1ему обходу "],["^ (\\S+)[иоы]й [Оо]бход "," $1ому обходу "],["^ [Оо]бход "," обходу "],["^ (\\S+н)ий [Пп]арк "," $1ему парку "],["^ (\\S+)[иоы]й [Пп]арк "," $1ому парку "],["^ (\\S+[иы]н) [Пп]арк "," $1у парку "],["^ (\\S+)[иоы]й (\\S+н)ий [Пп]арк "," $1ому $2ему парку "],["^ (\\S+н)ий (\\S+)[иоы]й [Пп]арк "," $1ему $2ому парку "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Пп]арк "," $1ому $2ому парку "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Пп]арк "," $1ому $2у парку "],["^ (\\d+)-й (\\S+н)ий [Пп]арк "," $1-му $2ему парку "],["^ (\\d+)-й (\\S+)[иоы]й [Пп]арк "," $1-му $2ому парку "],["^ (\\d+)-й (\\S+[иы]н) [Пп]арк "," $1-му $2у парку "],["^ [Пп]арк "," парку "],["^ (\\S+)[иоы]й-(\\S+)[иоы]й [Пп]ереулок "," $1ому-$2ому переулку "],["^ (\\d+)-й (\\S+)[иоы]й-(\\S+)[иоы]й [Пп]ереулок "," $1-му $2ому-$3ому переулку "],["^ (\\S+н)ий [Пп]ереулок "," $1ему переулку "],["^ (\\S+)[иоы]й [Пп]ереулок "," $1ому переулку "],["^ (\\S+[еёо]в) [Пп]ереулок "," $1у переулку "],["^ (\\S+[иы]н) [Пп]ереулок "," $1у переулку "],["^ (\\S+)[иоы]й (\\S+н)ий [Пп]ереулок "," $1ому $2ему переулку "],["^ (\\S+н)ий (\\S+)[иоы]й [Пп]ереулок "," $1ему $2ому переулку "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Пп]ереулок "," $1ому $2ому переулку "],["^ (\\S+)[иоы]й (\\S+[еёо]в) [Пп]ереулок "," $1ому $2у переулку "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Пп]ереулок "," $1ому $2у переулку "],["^ (\\d+)-й [Пп]ереулок "," $1-му переулку "],["^ (\\d+)-й (\\S+н)ий [Пп]ереулок "," $1-му $2ему переулку "],["^ (\\d+)-й (\\S+)[иоы]й [Пп]ереулок "," $1-му $2ому переулку "],["^ (\\d+)-й (\\S+[еёо]в) [Пп]ереулок "," $1-му $2у переулку "],["^ (\\d+)-й (\\S+[иы]н) [Пп]ереулок "," $1-му $2у переулку "],["^ [Пп]ереулок "," переулку "],["^ [Пп]одъезд "," подъезду "],["^ (\\S+[еёо]в)-(\\S+)[иоы]й [Пп]роезд "," $1у-$2ому проезду "],["^ (\\S+н)ий [Пп]роезд "," $1ему проезду "],["^ (\\S+)[иоы]й [Пп]роезд "," $1ому проезду "],["^ (\\S+[еёо]в) [Пп]роезд "," $1у проезду "],["^ (\\S+[иы]н) [Пп]роезд "," $1у проезду "],["^ (\\S+)[иоы]й (\\S+н)ий [Пп]роезд "," $1ому $2ему проезду "],["^ (\\S+н)ий (\\S+)[иоы]й [Пп]роезд "," $1ему $2ому проезду "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Пп]роезд "," $1ому $2ому проезду "],["^ (\\S+)[иоы]й (\\S+[еёо]в) [Пп]роезд "," $1ому $2у проезду "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Пп]роезд "," $1ому $2у проезду "],["^ (\\d+)-й [Пп]роезд "," $1-му проезду "],["^ (\\d+)-й (\\S+н)ий [Пп]роезд "," $1-му $2ему проезду "],["^ (\\d+)-й (\\S+)[иоы]й [Пп]роезд "," $1-му $2ому проезду "],["^ (\\d+)-й (\\S+[еёо]в) [Пп]роезд "," $1-му $2у проезду "],["^ (\\d+)-й (\\S+[иы]н) [Пп]роезд "," $1-му $2у проезду "],["^ (\\d+)-й (\\S+н)ий (\\S+)[иоы]й [Пп]роезд "," $1-му $2ему $3ому проезду "],["^ (\\d+)-й (\\S+)[иоы]й (\\S+)[иоы]й [Пп]роезд "," $1-му $2ому $3ому проезду "],["^ [Пп]роезд "," проезду "],["^ (\\S+н)ий [Пп]роспект "," $1ему проспекту "],["^ (\\S+)[иоы]й [Пп]роспект "," $1ому проспекту "],["^ (\\S+[иы]н) [Пп]роспект "," $1у проспекту "],["^ (\\S+)[иоы]й (\\S+н)ий [Пп]роспект "," $1ому $2ему проспекту "],["^ (\\S+н)ий (\\S+)[иоы]й [Пп]роспект "," $1ему $2ому проспекту "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Пп]роспект "," $1ому $2ому проспекту "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Пп]роспект "," $1ому $2у проспекту "],["^ (\\d+)-й (\\S+н)ий [Пп]роспект "," $1-му $2ему проспекту "],["^ (\\d+)-й (\\S+)[иоы]й [Пп]роспект "," $1-му $2ому проспекту "],["^ (\\d+)-й (\\S+[иы]н) [Пп]роспект "," $1-му $2у проспекту "],["^ [Пп]роспект "," проспекту "],["^ (\\S+н)ий [Пп]утепровод "," $1ему путепроводу "],["^ (\\S+)[иоы]й [Пп]утепровод "," $1ому путепроводу "],["^ (\\S+[иы]н) [Пп]утепровод "," $1у путепроводу "],["^ (\\S+)[иоы]й (\\S+н)ий [Пп]утепровод "," $1ому $2ему путепроводу "],["^ (\\S+н)ий (\\S+)[иоы]й [Пп]утепровод "," $1ему $2ому путепроводу "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Пп]утепровод "," $1ому $2ому путепроводу "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Пп]утепровод "," $1ому $2у путепроводу "],["^ (\\d+)-й (\\S+н)ий [Пп]утепровод "," $1-му $2ему путепроводу "],["^ (\\d+)-й (\\S+)[иоы]й [Пп]утепровод "," $1-му $2ому путепроводу "],["^ (\\d+)-й (\\S+[иы]н) [Пп]утепровод "," $1-му $2у путепроводу "],["^ [Пп]утепровод "," путепроводу "],["^ (\\S+н)ий [Сс]пуск "," $1ему спуску "],["^ (\\S+)[иоы]й [Сс]пуск "," $1ому спуску "],["^ (\\S+[еёо]в) [Сс]пуск "," $1у спуску "],["^ (\\S+[иы]н) [Сс]пуск "," $1у спуску "],["^ (\\S+)[иоы]й (\\S+н)ий [Сс]пуск "," $1ому $2ему спуску "],["^ (\\S+н)ий (\\S+)[иоы]й [Сс]пуск "," $1ему $2ому спуску "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Сс]пуск "," $1ому $2ому спуску "],["^ (\\S+)[иоы]й (\\S+[еёо]в) [Сс]пуск "," $1ому $2у спуску "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Сс]пуск "," $1ому $2у спуску "],["^ (\\d+)-й (\\S+н)ий [Сс]пуск "," $1-му $2ему спуску "],["^ (\\d+)-й (\\S+)[иоы]й [Сс]пуск "," $1-му $2ому спуску "],["^ (\\d+)-й (\\S+[еёо]в) [Сс]пуск "," $1-му $2у спуску "],["^ (\\d+)-й (\\S+[иы]н) [Сс]пуск "," $1-му $2у спуску "],["^ [Сс]пуск "," спуску "],["^ (\\S+н)ий [Сс]ъезд "," $1ему съезду "],["^ (\\S+)[иоы]й [Сс]ъезд "," $1ому съезду "],["^ (\\S+[иы]н) [Сс]ъезд "," $1у съезду "],["^ (\\S+)[иоы]й (\\S+н)ий [Сс]ъезд "," $1ому $2ему съезду "],["^ (\\S+н)ий (\\S+)[иоы]й [Сс]ъезд "," $1ему $2ому съезду "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Сс]ъезд "," $1ому $2ому съезду "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Сс]ъезд "," $1ому $2у съезду "],["^ (\\d+)-й (\\S+н)ий [Сс]ъезд "," $1-му $2ему съезду "],["^ (\\d+)-й (\\S+)[иоы]й [Сс]ъезд "," $1-му $2ому съезду "],["^ (\\d+)-й (\\S+[иы]н) [Сс]ъезд "," $1-му $2у съезду "],["^ [Сс]ъезд "," съезду "],["^ (\\S+н)ий [Тт][уо]ннель "," $1ему тоннелю "],["^ (\\S+)[иоы]й [Тт][уо]ннель "," $1ому тоннелю "],["^ (\\S+[иы]н) [Тт][уо]ннель "," $1у тоннелю "],["^ (\\S+)[иоы]й (\\S+н)ий [Тт][уо]ннель "," $1ому $2ему тоннелю "],["^ (\\S+н)ий (\\S+)[иоы]й [Тт][уо]ннель "," $1ему $2ому тоннелю "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Тт][уо]ннель "," $1ому $2ому тоннелю "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Тт][уо]ннель "," $1ому $2у тоннелю "],["^ (\\d+)-й (\\S+н)ий [Тт][уо]ннель "," $1-му $2ему тоннелю "],["^ (\\d+)-й (\\S+)[иоы]й [Тт][уо]ннель "," $1-му $2ому тоннелю "],["^ (\\d+)-й (\\S+[иы]н) [Тт][уо]ннель "," $1-му $2у тоннелю "],["^ [Тт][уо]ннель "," тоннелю "],["^ (\\S+н)ий [Тт]ракт "," $1ему тракту "],["^ (\\S+)[иоы]й [Тт]ракт "," $1ому тракту "],["^ (\\S+[еёо]в) [Тт]ракт "," $1у тракту "],["^ (\\S+[иы]н) [Тт]ракт "," $1у тракту "],["^ (\\S+)[иоы]й (\\S+н)ий [Тт]ракт "," $1ому $2ему тракту "],["^ (\\S+н)ий (\\S+)[иоы]й [Тт]ракт "," $1ему $2ому тракту "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Тт]ракт "," $1ому $2ому тракту "],["^ (\\S+)[иоы]й (\\S+[еёо]в) [Тт]ракт "," $1ому $2у тракту "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Тт]ракт "," $1ому $2у тракту "],["^ (\\d+)-й (\\S+н)ий [Тт]ракт "," $1-му $2ему тракту "],["^ (\\d+)-й (\\S+)[иоы]й [Тт]ракт "," $1-му $2ому тракту "],["^ (\\d+)-й (\\S+[еёо]в) [Тт]ракт "," $1-му $2у тракту "],["^ (\\d+)-й (\\S+[иы]н) [Тт]ракт "," $1-му $2у тракту "],["^ [Тт]ракт "," тракту "],["^ (\\S+н)ий [Тт]упик "," $1ему тупику "],["^ (\\S+)[иоы]й [Тт]упик "," $1ому тупику "],["^ (\\S+[еёо]в) [Тт]упик "," $1у тупику "],["^ (\\S+[иы]н) [Тт]упик "," $1у тупику "],["^ (\\S+)[иоы]й (\\S+н)ий [Тт]упик "," $1ому $2ему тупику "],["^ (\\S+н)ий (\\S+)[иоы]й [Тт]упик "," $1ему $2ому тупику "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Тт]упик "," $1ому $2ому тупику "],["^ (\\S+)[иоы]й (\\S+[еёо]в) [Тт]упик "," $1ому $2у тупику "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Тт]упик "," $1ому $2у тупику "],["^ (\\d+)-й [Тт]упик "," $1-му тупику "],["^ (\\d+)-й (\\S+н)ий [Тт]упик "," $1-му $2ему тупику "],["^ (\\d+)-й (\\S+)[иоы]й [Тт]упик "," $1-му $2ому тупику "],["^ (\\d+)-й (\\S+[еёо]в) [Тт]упик "," $1-му $2у тупику "],["^ (\\d+)-й (\\S+[иы]н) [Тт]упик "," $1-му $2у тупику "],["^ [Тт]упик "," тупику "],["^ (\\S+[ео])е ([Пп]олу)?[Кк]ольцо "," $1му $2кольцу "],["^ (\\S+ье) ([Пп]олу)?[Кк]ольцо "," $1му $2кольцу "],["^ (\\S+[ео])е (\\S+[ео])е ([Пп]олу)?[Кк]ольцо "," $1му $2му $3кольцу "],["^ (\\S+ье) (\\S+[ео])е ([Пп]олу)?[Кк]ольцо "," $1му $2му $3кольцу "],["^ (\\d+)-е (\\S+[ео])е ([Пп]олу)?[Кк]ольцо "," $1-му $2му $3кольцу "],["^ (\\d+)-е (\\S+ье) ([Пп]олу)?[Кк]ольцо "," $1-му $2му $3кольцу "],["^ ([Пп]олу)?[Кк]ольцо "," $1кольцу "],["^ (\\S+[ео])е [Шш]оссе "," $1му шоссе "],["^ (\\S+ье) [Шш]оссе "," $1му шоссе "],["^ (\\S+[ео])е (\\S+[ео])е [Шш]оссе "," $1му $2му шоссе "],["^ (\\S+ье) (\\S+[ео])е [Шш]оссе "," $1му $2му шоссе "],["^ (\\d+)-е (\\S+[ео])е [Шш]оссе "," $1-му $2му шоссе "],["^ (\\d+)-е (\\S+ье) [Шш]оссе "," $1-му $2му шоссе "],[" ([Тт])ретому "," $1ретьему "],["([жч])ому ","$1ьему "],["([жч])ой ","$1ей "]],
genitive:[['^ ([«"])'," трасса $1"],["^ (\\S+)ая [Аа]ллея "," $1ой аллеи "],["^ (\\S+)ья [Аа]ллея "," $1ьей аллеи "],["^ (\\S+)яя [Аа]ллея "," $1ей аллеи "],["^ (\\d+)-я (\\S+)ая [Аа]ллея "," $1-й $2ой аллеи "],["^ [Аа]ллея "," аллеи "],["^ (\\S+)ая-(\\S+)ая [Уу]лица "," $1ой-$2ой улицы "],["^ (\\S+)ая [Уу]лица "," $1ой улицы "],["^ (\\S+)ья [Уу]лица "," $1ьей улицы "],["^ (\\S+)яя [Уу]лица "," $1ей улицы "],["^ (\\d+)-я [Уу]лица "," $1-й улицы "],["^ (\\d+)-я (\\S+)ая [Уу]лица "," $1-й $2ой улицы "],["^ (\\S+)ая (\\S+)ая [Уу]лица "," $1ой $2ой улицы "],["^ (\\S+[вн])а [Уу]лица "," $1ой улицы "],["^ (\\S+)ая (\\S+[вн])а [Уу]лица "," $1ой $2ой улицы "],["^ Даньславля [Уу]лица "," Даньславлей улицы "],["^ Добрыня [Уу]лица "," Добрыней улицы "],["^ Людогоща [Уу]лица "," Людогощей улицы "],["^ [Уу]лица "," улицы "],["^ (\\d+)-я [Лл]иния "," $1-й линии "],["^ (\\d+)-(\\d+)-я [Лл]иния "," $1-$2-й линии "],["^ (\\S+)ая [Лл]иния "," $1ой линии "],["^ (\\S+)ья [Лл]иния "," $1ьей линии "],["^ (\\S+)яя [Лл]иния "," $1ей линии "],["^ (\\d+)-я (\\S+)ая [Лл]иния "," $1-й $2ой линии "],["^ [Лл]иния "," линии "],["^ (\\d+)-(\\d+)-я [Лл]инии "," $1-$2-й линий "],["^ (\\S+)ая [Нн]абережная "," $1ой набережной "],["^ (\\S+)ья [Нн]абережная "," $1ьей набережной "],["^ (\\S+)яя [Нн]абережная "," $1ей набережной "],["^ (\\d+)-я (\\S+)ая [Нн]абережная "," $1-й $2ой набережной "],["^ [Нн]абережная "," набережной "],["^ (\\S+)ая [Пп]лощадь "," $1ой площади "],["^ (\\S+)ья [Пп]лощадь "," $1ьей площади "],["^ (\\S+)яя [Пп]лощадь "," $1ей площади "],["^ (\\S+[вн])а [Пп]лощадь "," $1ой площади "],["^ (\\d+)-я (\\S+)ая [Пп]лощадь "," $1-й $2ой площади "],["^ [Пп]лощадь "," площади "],["^ (\\S+)ая [Пп]росека "," $1ой просеки "],["^ (\\S+)ья [Пп]росека "," $1ьей просеки "],["^ (\\S+)яя [Пп]росека "," $1ей просеки "],["^ (\\d+)-я [Пп]росека "," $1-й просеки "],["^ [Пп]росека "," просеки "],["^ (\\S+)ая [Ээ]стакада "," $1ой эстакады "],["^ (\\S+)ья [Ээ]стакада "," $1ьей эстакады "],["^ (\\S+)яя [Ээ]стакада "," $1ей эстакады "],["^ (\\d+)-я (\\S+)ая [Ээ]стакада "," $1-й $2ой эстакады "],["^ [Ээ]стакада "," эстакады "],["^ (\\S+)ая [Мм]агистраль "," $1ой магистрали "],["^ (\\S+)ья [Мм]агистраль "," $1ьей магистрали "],["^ (\\S+)яя [Мм]агистраль "," $1ей магистрали "],["^ (\\S+)ая (\\S+)ая [Мм]агистраль "," $1ой $2ой магистрали "],["^ (\\d+)-я (\\S+)ая [Мм]агистраль "," $1-й $2ой магистрали "],["^ [Мм]агистраль "," магистрали "],["^ (\\S+)ая [Рр]азвязка "," $1ой развязки "],["^ (\\S+)ья [Рр]азвязка "," $1ьей развязки "],["^ (\\S+)яя [Рр]азвязка "," $1ей развязки "],["^ (\\d+)-я (\\S+)ая [Рр]азвязка "," $1-й $2ой развязки "],["^ [Рр]азвязка "," развязки "],["^ (\\S+)ая [Тт]расса "," $1ой трассы "],["^ (\\S+)ья [Тт]расса "," $1ьей трассы "],["^ (\\S+)яя [Тт]расса "," $1ей трассы "],["^ (\\d+)-я (\\S+)ая [Тт]расса "," $1-й $2ой трассы "],["^ [Тт]расса "," трассы "],["^ (\\S+)ая ([Аа]вто)?[Дд]орога "," $1ой $2дороги "],["^ (\\S+)ья ([Аа]вто)?[Дд]орога "," $1ьей $2дороги "],["^ (\\S+)яя ([Аа]вто)?[Дд]орога "," $1ей $2дороги "],["^ (\\S+)ая (\\S+)ая ([Аа]вто)?[Дд]орога "," $1ой $2ой $3дороги "],["^ (\\d+)-я (\\S+)ая ([Аа]вто)?[Дд]орога "," $1-й $2ой $3дороги "],["^ ([Аа]вто)?[Дд]орога "," $1дороги "],["^ (\\S+)ая [Дд]орожка "," $1ой дорожки "],["^ (\\S+)ья [Дд]орожка "," $1ьей дорожки "],["^ (\\S+)яя [Дд]орожка "," $1ей дорожки "],["^ (\\d+)-я (\\S+)ая [Дд]орожка "," $1-й $2ой дорожки "],["^ [Дд]орожка "," дорожки "],["^ (\\S+)во [Пп]оле "," $1ва поля "],["^ (\\S+)ая [Кк]оса "," $1ой косы "],["^ (\\S+)ая [Хх]орда "," $1ой хорды "],["^ (\\S+)[иоы]й [Пп]роток "," $1ого протока "],["^ (\\S+н)ий [Бб]ульвар "," $1его бульвара "],["^ (\\S+)[иоы]й [Бб]ульвар "," $1ого бульвара "],["^ (\\S+[иы]н) [Бб]ульвар "," $1ого бульвара "],["^ (\\S+)[иоы]й (\\S+н)ий [Бб]ульвар "," $1ого $2его бульвара "],["^ (\\S+н)ий (\\S+)[иоы]й [Бб]ульвар "," $1его $2ого бульвара "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Бб]ульвар "," $1ого $2ого бульвара "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Бб]ульвар "," $1ого $2ого бульвара "],["^ (\\d+)-й (\\S+н)ий [Бб]ульвар "," $1-го $2его бульвара "],["^ (\\d+)-й (\\S+)[иоы]й [Бб]ульвар "," $1-го $2ого бульвара "],["^ (\\d+)-й (\\S+[иы]н) [Бб]ульвар "," $1-го $2ого бульвара "],["^ [Бб]ульвар "," бульвара "],["^ [Дд]убл[её]р "," дублёра "],["^ (\\S+н)ий [Зз]аезд "," $1его заезда "],["^ (\\S+)[иоы]й [Зз]аезд "," $1ого заезда "],["^ (\\S+[еёо]в) [Зз]аезд "," $1а заезда "],["^ (\\S+[иы]н) [Зз]аезд "," $1а заезда "],["^ (\\S+)[иоы]й (\\S+н)ий [Зз]аезд "," $1ого $2его заезда "],["^ (\\S+н)ий (\\S+)[иоы]й [Зз]аезд "," $1его $2ого заезда "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Зз]аезд "," $1ого $2ого заезда "],["^ (\\S+)[иоы]й (\\S+[еёо]в) [Зз]аезд "," $1ого $2а заезда "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Зз]аезд "," $1ого $2а заезда "],["^ (\\d+)-й (\\S+н)ий [Зз]аезд "," $1-го $2его заезда "],["^ (\\d+)-й (\\S+)[иоы]й [Зз]аезд "," $1-го $2ого заезда "],["^ (\\d+)-й (\\S+[еёо]в) [Зз]аезд "," $1-го $2а заезда "],["^ (\\d+)-й (\\S+[иы]н) [Зз]аезд "," $1-го $2а заезда "],["^ [Зз]аезд "," заезда "],["^ (\\S+н)ий [Мм]ост "," $1его моста "],["^ (\\S+)[иоы]й [Мм]ост "," $1ого моста "],["^ (\\S+[еёо]в) [Мм]ост "," $1а моста "],["^ (\\S+[иы]н) [Мм]ост "," $1а моста "],["^ (\\S+)[иоы]й (\\S+н)ий [Мм]ост "," $1ого $2его моста "],["^ (\\S+н)ий (\\S+)[иоы]й [Мм]ост "," $1его $2ого моста "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Мм]ост "," $1ого $2ого моста "],["^ (\\S+)[иоы]й (\\S+[еёо]в) [Мм]ост "," $1ого $2а моста "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Мм]ост "," $1ого $2а моста "],["^ (\\d+)-й [Мм]ост "," $1-го моста "],["^ (\\d+)-й (\\S+н)ий [Мм]ост "," $1-го $2его моста "],["^ (\\d+)-й (\\S+)[иоы]й [Мм]ост "," $1-го $2ого моста "],["^ (\\d+)-й (\\S+[еёо]в) [Мм]ост "," $1-го $2а моста "],["^ (\\d+)-й (\\S+[иы]н) [Мм]ост "," $1-го $2а моста "],["^ [Мм]ост "," моста "],["^ (\\S+н)ий [Оо]бход "," $1его обхода "],["^ (\\S+)[иоы]й [Оо]бход "," $1ого обхода "],["^ [Оо]бход "," обхода "],["^ (\\S+н)ий [Пп]арк "," $1его парка "],["^ (\\S+)[иоы]й [Пп]арк "," $1ого парка "],["^ (\\S+[иы]н) [Пп]арк "," $1ого парка "],["^ (\\S+)[иоы]й (\\S+н)ий [Пп]арк "," $1ого $2его парка "],["^ (\\S+н)ий (\\S+)[иоы]й [Пп]арк "," $1его $2ого парка "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Пп]арк "," $1ого $2ого парка "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Пп]арк "," $1ого $2ого парка "],["^ (\\d+)-й (\\S+н)ий [Пп]арк "," $1-го $2его парка "],["^ (\\d+)-й (\\S+)[иоы]й [Пп]арк "," $1-го $2ого парка "],["^ (\\d+)-й (\\S+[иы]н) [Пп]арк "," $1-го $2ого парка "],["^ [Пп]арк "," парка "],["^ (\\S+)[иоы]й-(\\S+)[иоы]й [Пп]ереулок "," $1ого-$2ого переулка "],["^ (\\d+)-й (\\S+)[иоы]й-(\\S+)[иоы]й [Пп]ереулок "," $1-го $2ого-$3ого переулка "],["^ (\\S+н)ий [Пп]ереулок "," $1его переулка "],["^ (\\S+)[иоы]й [Пп]ереулок "," $1ого переулка "],["^ (\\S+[еёо]в) [Пп]ереулок "," $1а переулка "],["^ (\\S+[иы]н) [Пп]ереулок "," $1а переулка "],["^ (\\S+)[иоы]й (\\S+н)ий [Пп]ереулок "," $1ого $2его переулка "],["^ (\\S+н)ий (\\S+)[иоы]й [Пп]ереулок "," $1его $2ого переулка "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Пп]ереулок "," $1ого $2ого переулка "],["^ (\\S+)[иоы]й (\\S+[еёо]в) [Пп]ереулок "," $1ого $2а переулка "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Пп]ереулок "," $1ого $2а переулка "],["^ (\\d+)-й [Пп]ереулок "," $1-го переулка "],["^ (\\d+)-й (\\S+н)ий [Пп]ереулок "," $1-го $2его переулка "],["^ (\\d+)-й (\\S+)[иоы]й [Пп]ереулок "," $1-го $2ого переулка "],["^ (\\d+)-й (\\S+[еёо]в) [Пп]ереулок "," $1-го $2а переулка "],["^ (\\d+)-й (\\S+[иы]н) [Пп]ереулок "," $1-го $2а переулка "],["^ [Пп]ереулок "," переулка "],["^ [Пп]одъезд "," подъезда "],["^ (\\S+[еёо]в)-(\\S+)[иоы]й [Пп]роезд "," $1а-$2ого проезда "],["^ (\\S+н)ий [Пп]роезд "," $1его проезда "],["^ (\\S+)[иоы]й [Пп]роезд "," $1ого проезда "],["^ (\\S+[еёо]в) [Пп]роезд "," $1а проезда "],["^ (\\S+[иы]н) [Пп]роезд "," $1а проезда "],["^ (\\S+)[иоы]й (\\S+н)ий [Пп]роезд "," $1ого $2его проезда "],["^ (\\S+н)ий (\\S+)[иоы]й [Пп]роезд "," $1его $2ого проезда "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Пп]роезд "," $1ого $2ого проезда "],["^ (\\S+)[иоы]й (\\S+[еёо]в) [Пп]роезд "," $1ого $2а проезда "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Пп]роезд "," $1ого $2а проезда "],["^ (\\d+)-й [Пп]роезд "," $1-го проезда "],["^ (\\d+)-й (\\S+н)ий [Пп]роезд "," $1-го $2его проезда "],["^ (\\d+)-й (\\S+)[иоы]й [Пп]роезд "," $1-го $2ого проезда "],["^ (\\d+)-й (\\S+[еёо]в) [Пп]роезд "," $1-го $2а проезда "],["^ (\\d+)-й (\\S+[иы]н) [Пп]роезд "," $1-го $2а проезда "],["^ (\\d+)-й (\\S+н)ий (\\S+)[иоы]й [Пп]роезд "," $1-го $2его $3ого проезда "],["^ (\\d+)-й (\\S+)[иоы]й (\\S+)[иоы]й [Пп]роезд "," $1-го $2ого $3ого проезда "],["^ [Пп]роезд "," проезда "],["^ (\\S+н)ий [Пп]роспект "," $1его проспекта "],["^ (\\S+)[иоы]й [Пп]роспект "," $1ого проспекта "],["^ (\\S+[иы]н) [Пп]роспект "," $1ого проспекта "],["^ (\\S+)[иоы]й (\\S+н)ий [Пп]роспект "," $1ого $2его проспекта "],["^ (\\S+н)ий (\\S+)[иоы]й [Пп]роспект "," $1его $2ого проспекта "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Пп]роспект "," $1ого $2ого проспекта "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Пп]роспект "," $1ого $2ого проспекта "],["^ (\\d+)-й (\\S+н)ий [Пп]роспект "," $1-го $2его проспекта "],["^ (\\d+)-й (\\S+)[иоы]й [Пп]роспект "," $1-го $2ого проспекта "],["^ (\\d+)-й (\\S+[иы]н) [Пп]роспект "," $1-го $2ого проспекта "],["^ [Пп]роспект "," проспекта "],["^ (\\S+н)ий [Пп]утепровод "," $1его путепровода "],["^ (\\S+)[иоы]й [Пп]утепровод "," $1ого путепровода "],["^ (\\S+[иы]н) [Пп]утепровод "," $1ого путепровода "],["^ (\\S+)[иоы]й (\\S+н)ий [Пп]утепровод "," $1ого $2его путепровода "],["^ (\\S+н)ий (\\S+)[иоы]й [Пп]утепровод "," $1его $2ого путепровода "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Пп]утепровод "," $1ого $2ого путепровода "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Пп]утепровод "," $1ого $2ого путепровода "],["^ (\\d+)-й (\\S+н)ий [Пп]утепровод "," $1-го $2его путепровода "],["^ (\\d+)-й (\\S+)[иоы]й [Пп]утепровод "," $1-го $2ого путепровода "],["^ (\\d+)-й (\\S+[иы]н) [Пп]утепровод "," $1-го $2ого путепровода "],["^ [Пп]утепровод "," путепровода "],["^ (\\S+н)ий [Сс]пуск "," $1его спуска "],["^ (\\S+)[иоы]й [Сс]пуск "," $1ого спуска "],["^ (\\S+[еёо]в) [Сс]пуск "," $1а спуска "],["^ (\\S+[иы]н) [Сс]пуск "," $1а спуска "],["^ (\\S+)[иоы]й (\\S+н)ий [Сс]пуск "," $1ого $2его спуска "],["^ (\\S+н)ий (\\S+)[иоы]й [Сс]пуск "," $1его $2ого спуска "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Сс]пуск "," $1ого $2ого спуска "],["^ (\\S+)[иоы]й (\\S+[еёо]в) [Сс]пуск "," $1ого $2а спуска "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Сс]пуск "," $1ого $2а спуска "],["^ (\\d+)-й (\\S+н)ий [Сс]пуск "," $1-го $2его спуска "],["^ (\\d+)-й (\\S+)[иоы]й [Сс]пуск "," $1-го $2ого спуска "],["^ (\\d+)-й (\\S+[еёо]в) [Сс]пуск "," $1-го $2а спуска "],["^ (\\d+)-й (\\S+[иы]н) [Сс]пуск "," $1-го $2а спуска "],["^ [Сс]пуск "," спуска "],["^ (\\S+н)ий [Сс]ъезд "," $1его съезда "],["^ (\\S+)[иоы]й [Сс]ъезд "," $1ого съезда "],["^ (\\S+[иы]н) [Сс]ъезд "," $1ого съезда "],["^ (\\S+)[иоы]й (\\S+н)ий [Сс]ъезд "," $1ого $2его съезда "],["^ (\\S+н)ий (\\S+)[иоы]й [Сс]ъезд "," $1его $2ого съезда "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Сс]ъезд "," $1ого $2ого съезда "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Сс]ъезд "," $1ого $2ого съезда "],["^ (\\d+)-й (\\S+н)ий [Сс]ъезд "," $1-го $2его съезда "],["^ (\\d+)-й (\\S+)[иоы]й [Сс]ъезд "," $1-го $2ого съезда "],["^ (\\d+)-й (\\S+[иы]н) [Сс]ъезд "," $1-го $2ого съезда "],["^ [Сс]ъезд "," съезда "],["^ (\\S+н)ий [Тт][уо]ннель "," $1его тоннеля "],["^ (\\S+)[иоы]й [Тт][уо]ннель "," $1ого тоннеля "],["^ (\\S+[иы]н) [Тт][уо]ннель "," $1ого тоннеля "],["^ (\\S+)[иоы]й (\\S+н)ий [Тт][уо]ннель "," $1ого $2его тоннеля "],["^ (\\S+н)ий (\\S+)[иоы]й [Тт][уо]ннель "," $1его $2ого тоннеля "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Тт][уо]ннель "," $1ого $2ого тоннеля "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Тт][уо]ннель "," $1ого $2ого тоннеля "],["^ (\\d+)-й (\\S+н)ий [Тт][уо]ннель "," $1-го $2его тоннеля "],["^ (\\d+)-й (\\S+)[иоы]й [Тт][уо]ннель "," $1-го $2ого тоннеля "],["^ (\\d+)-й (\\S+[иы]н) [Тт][уо]ннель "," $1-го $2ого тоннеля "],["^ [Тт][уо]ннель "," тоннеля "],["^ (\\S+н)ий [Тт]ракт "," $1ем тракта "],["^ (\\S+)[иоы]й [Тт]ракт "," $1ого тракта "],["^ (\\S+[еёо]в) [Тт]ракт "," $1а тракта "],["^ (\\S+[иы]н) [Тт]ракт "," $1а тракта "],["^ (\\S+)[иоы]й (\\S+н)ий [Тт]ракт "," $1ого $2его тракта "],["^ (\\S+н)ий (\\S+)[иоы]й [Тт]ракт "," $1его $2ого тракта "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Тт]ракт "," $1ого $2ого тракта "],["^ (\\S+)[иоы]й (\\S+[еёо]в) [Тт]ракт "," $1ого $2а тракта "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Тт]ракт "," $1ого $2а тракта "],["^ (\\d+)-й (\\S+н)ий [Тт]ракт "," $1-го $2его тракта "],["^ (\\d+)-й (\\S+)[иоы]й [Тт]ракт "," $1-го $2ого тракта "],["^ (\\d+)-й (\\S+[еёо]в) [Тт]ракт "," $1-го $2а тракта "],["^ (\\d+)-й (\\S+[иы]н) [Тт]ракт "," $1-го $2а тракта "],["^ [Тт]ракт "," тракта "],["^ (\\S+н)ий [Тт]упик "," $1его тупика "],["^ (\\S+)[иоы]й [Тт]упик "," $1ого тупика "],["^ (\\S+[еёо]в) [Тт]упик "," $1а тупика "],["^ (\\S+[иы]н) [Тт]упик "," $1а тупика "],["^ (\\S+)[иоы]й (\\S+н)ий [Тт]упик "," $1ого $2его тупика "],["^ (\\S+н)ий (\\S+)[иоы]й [Тт]упик "," $1его $2ого тупика "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Тт]упик "," $1ого $2ого тупика "],["^ (\\S+)[иоы]й (\\S+[еёо]в) [Тт]упик "," $1ого $2а тупика "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Тт]упик "," $1ого $2а тупика "],["^ (\\d+)-й [Тт]упик "," $1-го тупика "],["^ (\\d+)-й (\\S+н)ий [Тт]упик "," $1-го $2его тупика "],["^ (\\d+)-й (\\S+)[иоы]й [Тт]упик "," $1-го $2ого тупика "],["^ (\\d+)-й (\\S+[еёо]в) [Тт]упик "," $1-го $2а тупика "],["^ (\\d+)-й (\\S+[иы]н) [Тт]упик "," $1-го $2а тупика "],["^ [Тт]упик "," тупика "],["^ (\\S+[ео])е ([Пп]олу)?[Кк]ольцо "," $1го $2кольца "],["^ (\\S+ье) ([Пп]олу)?[Кк]ольцо "," $1го $2кольца "],["^ (\\S+[ео])е (\\S+[ео])е ([Пп]олу)?[Кк]ольцо "," $1го $2го $3кольца "],["^ (\\S+ье) (\\S+[ео])е ([Пп]олу)?[Кк]ольцо "," $1го $2го $3кольца "],["^ (\\d+)-е (\\S+[ео])е ([Пп]олу)?[Кк]ольцо "," $1-го $2го $3кольца "],["^ (\\d+)-е (\\S+ье) ([Пп]олу)?[Кк]ольцо "," $1-го $2го $3кольца "],["^ ([Пп]олу)?[Кк]ольцо "," $1кольца "],["^ (\\S+[ео])е [Шш]оссе "," $1го шоссе "],["^ (\\S+ье) [Шш]оссе "," $1го шоссе "],["^ (\\S+[ео])е (\\S+[ео])е [Шш]оссе "," $1го $2го шоссе "],["^ (\\S+ье) (\\S+[ео])е [Шш]оссе "," $1го $2го шоссе "],["^ (\\d+)-е (\\S+[ео])е [Шш]оссе "," $1-го $2го шоссе "],["^ (\\d+)-е (\\S+ье) [Шш]оссе "," $1-го $2го шоссе "],[" ([Тт])ретого "," $1ретьего "],["([жч])ого ","$1ьего "]],prepositional:[['^ ([«"])'," трасса $1"],["^ (\\S+)ая [Аа]ллея "," $1ой аллее "],["^ (\\S+)ья [Аа]ллея "," $1ьей аллее "],["^ (\\S+)яя [Аа]ллея "," $1ей аллее "],["^ (\\d+)-я (\\S+)ая [Аа]ллея "," $1-й $2ой аллее "],["^ [Аа]ллея "," аллее "],["^ (\\S+)ая-(\\S+)ая [Уу]лица "," $1ой-$2ой улице "],["^ (\\S+)ая [Уу]лица "," $1ой улице "],["^ (\\S+)ья [Уу]лица "," $1ьей улице "],["^ (\\S+)яя [Уу]лица "," $1ей улице "],["^ (\\d+)-я [Уу]лица "," $1-й улице "],["^ (\\d+)-я (\\S+)ая [Уу]лица "," $1-й $2ой улице "],["^ (\\S+)ая (\\S+)ая [Уу]лица "," $1ой $2ой улице "],["^ (\\S+[вн])а [Уу]лица "," $1ой улице "],["^ (\\S+)ая (\\S+[вн])а [Уу]лица "," $1ой $2ой улице "],["^ Даньславля [Уу]лица "," Даньславлей улице "],["^ Добрыня [Уу]лица "," Добрыней улице "],["^ Людогоща [Уу]лица "," Людогощей улице "],["^ [Уу]лица "," улице "],["^ (\\d+)-я [Лл]иния "," $1-й линии "],["^ (\\d+)-(\\d+)-я [Лл]иния "," $1-$2-й линии "],["^ (\\S+)ая [Лл]иния "," $1ой линии "],["^ (\\S+)ья [Лл]иния "," $1ьей линии "],["^ (\\S+)яя [Лл]иния "," $1ей линии "],["^ (\\d+)-я (\\S+)ая [Лл]иния "," $1-й $2ой линии "],["^ [Лл]иния "," линии "],["^ (\\d+)-(\\d+)-я [Лл]инии "," $1-$2-й линиях "],["^ (\\S+)ая [Нн]абережная "," $1ой набережной "],["^ (\\S+)ья [Нн]абережная "," $1ьей набережной "],["^ (\\S+)яя [Нн]абережная "," $1ей набережной "],["^ (\\d+)-я (\\S+)ая [Нн]абережная "," $1-й $2ой набережной "],["^ [Нн]абережная "," набережной "],["^ (\\S+)ая [Пп]лощадь "," $1ой площади "],["^ (\\S+)ья [Пп]лощадь "," $1ьей площади "],["^ (\\S+)яя [Пп]лощадь "," $1ей площади "],["^ (\\S+[вн])а [Пп]лощадь "," $1ой площади "],["^ (\\d+)-я (\\S+)ая [Пп]лощадь "," $1-й $2ой площади "],["^ [Пп]лощадь "," площади "],["^ (\\S+)ая [Пп]росека "," $1ой просеке "],["^ (\\S+)ья [Пп]росека "," $1ьей просеке "],["^ (\\S+)яя [Пп]росека "," $1ей просеке "],["^ (\\d+)-я [Пп]росека "," $1-й просеке "],["^ [Пп]росека "," просеке "],["^ (\\S+)ая [Ээ]стакада "," $1ой эстакаде "],["^ (\\S+)ья [Ээ]стакада "," $1ьей эстакаде "],["^ (\\S+)яя [Ээ]стакада "," $1ей эстакаде "],["^ (\\d+)-я (\\S+)ая [Ээ]стакада "," $1-й $2ой эстакаде "],["^ [Ээ]стакада "," эстакаде "],["^ (\\S+)ая [Мм]агистраль "," $1ой магистрали "],["^ (\\S+)ья [Мм]агистраль "," $1ьей магистрали "],["^ (\\S+)яя [Мм]агистраль "," $1ей магистрали "],["^ (\\S+)ая (\\S+)ая [Мм]агистраль "," $1ой $2ой магистрали "],["^ (\\d+)-я (\\S+)ая [Мм]агистраль "," $1-й $2ой магистрали "],["^ [Мм]агистраль "," магистрали "],["^ (\\S+)ая [Рр]азвязка "," $1ой развязке "],["^ (\\S+)ья [Рр]азвязка "," $1ьей развязке "],["^ (\\S+)яя [Рр]азвязка "," $1ей развязке "],["^ (\\d+)-я (\\S+)ая [Рр]азвязка "," $1-й $2ой развязке "],["^ [Рр]азвязка "," развязке "],["^ (\\S+)ая [Тт]расса "," $1ой трассе "],["^ (\\S+)ья [Тт]расса "," $1ьей трассе "],["^ (\\S+)яя [Тт]расса "," $1ей трассе "],["^ (\\d+)-я (\\S+)ая [Тт]расса "," $1-й $2ой трассе "],["^ [Тт]расса "," трассе "],["^ (\\S+)ая ([Аа]вто)?[Дд]орога "," $1ой $2дороге "],["^ (\\S+)ья ([Аа]вто)?[Дд]орога "," $1ьей $2дороге "],["^ (\\S+)яя ([Аа]вто)?[Дд]орога "," $1ей $2дороге "],["^ (\\S+)ая (\\S+)ая ([Аа]вто)?[Дд]орога "," $1ой $2ой $3дороге "],["^ (\\d+)-я (\\S+)ая ([Аа]вто)?[Дд]орога "," $1-й $2ой $3дороге "],["^ ([Аа]вто)?[Дд]орога "," $1дороге "],["^ (\\S+)ая [Дд]орожка "," $1ой дорожке "],["^ (\\S+)ья [Дд]орожка "," $1ьей дорожке "],["^ (\\S+)яя [Дд]орожка "," $1ей дорожке "],["^ (\\d+)-я (\\S+)ая [Дд]орожка "," $1-й $2ой дорожке "],["^ [Дд]орожка "," дорожке "],["^ (\\S+)во [Пп]оле "," $1вом поле "],["^ (\\S+)ая [Кк]оса "," $1ой косе "],["^ (\\S+)ая [Хх]орда "," $1ой хорде "],["^ (\\S+)[иоы]й [Пп]роток "," $1ом протоке "],["^ (\\S+н)ий [Бб]ульвар "," $1ем бульваре "],["^ (\\S+)[иоы]й [Бб]ульвар "," $1ом бульваре "],["^ (\\S+[иы]н) [Бб]ульвар "," $1ом бульваре "],["^ (\\S+)[иоы]й (\\S+н)ий [Бб]ульвар "," $1ом $2ем бульваре "],["^ (\\S+н)ий (\\S+)[иоы]й [Бб]ульвар "," $1ем $2ом бульваре "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Бб]ульвар "," $1ом $2ом бульваре "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Бб]ульвар "," $1ом $2ом бульваре "],["^ (\\d+)-й (\\S+н)ий [Бб]ульвар "," $1-м $2ем бульваре "],["^ (\\d+)-й (\\S+)[иоы]й [Бб]ульвар "," $1-м $2ом бульваре "],["^ (\\d+)-й (\\S+[иы]н) [Бб]ульвар "," $1-м $2ом бульваре "],["^ [Бб]ульвар "," бульваре "],["^ [Дд]убл[её]р "," дублёре "],["^ (\\S+н)ий [Зз]аезд "," $1ем заезде "],["^ (\\S+)[иоы]й [Зз]аезд "," $1ом заезде "],["^ (\\S+[еёо]в) [Зз]аезд "," $1ом заезде "],["^ (\\S+[иы]н) [Зз]аезд "," $1ом заезде "],["^ (\\S+)[иоы]й (\\S+н)ий [Зз]аезд "," $1ом $2ем заезде "],["^ (\\S+н)ий (\\S+)[иоы]й [Зз]аезд "," $1ем $2ом заезде "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Зз]аезд "," $1ом $2ом заезде "],["^ (\\S+)[иоы]й (\\S+[еёо]в) [Зз]аезд "," $1ом $2ом заезде "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Зз]аезд "," $1ом $2ом заезде "],["^ (\\d+)-й (\\S+н)ий [Зз]аезд "," $1-м $2ем заезде "],["^ (\\d+)-й (\\S+)[иоы]й [Зз]аезд "," $1-м $2ом заезде "],["^ (\\d+)-й (\\S+[еёо]в) [Зз]аезд "," $1-м $2ом заезде "],["^ (\\d+)-й (\\S+[иы]н) [Зз]аезд "," $1-м $2ом заезде "],["^ [Зз]аезд "," заезде "],["^ (\\S+н)ий [Мм]ост "," $1ем мосту "],["^ (\\S+)[иоы]й [Мм]ост "," $1ом мосту "],["^ (\\S+[еёо]в) [Мм]ост "," $1ом мосту "],["^ (\\S+[иы]н) [Мм]ост "," $1ом мосту "],["^ (\\S+)[иоы]й (\\S+н)ий [Мм]ост "," $1ом $2ем мосту "],["^ (\\S+н)ий (\\S+)[иоы]й [Мм]ост "," $1ем $2ом мосту "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Мм]ост "," $1ом $2ом мосту "],["^ (\\S+)[иоы]й (\\S+[еёо]в) [Мм]ост "," $1ом $2ом мосту "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Мм]ост "," $1ом $2ом мосту "],["^ (\\d+)-й [Мм]ост "," $1-м мосту "],["^ (\\d+)-й (\\S+н)ий [Мм]ост "," $1-м $2ем мосту "],["^ (\\d+)-й (\\S+)[иоы]й [Мм]ост "," $1-м $2ом мосту "],["^ (\\d+)-й (\\S+[еёо]в) [Мм]ост "," $1-м $2ом мосту "],["^ (\\d+)-й (\\S+[иы]н) [Мм]ост "," $1-м $2ом мосту "],["^ [Мм]ост "," мосту "],["^ (\\S+н)ий [Оо]бход "," $1ем обходе "],["^ (\\S+)[иоы]й [Оо]бход "," $1ом обходе "],["^ [Оо]бход "," обходе "],["^ (\\S+н)ий [Пп]арк "," $1ем парке "],["^ (\\S+)[иоы]й [Пп]арк "," $1ом парке "],["^ (\\S+[иы]н) [Пп]арк "," $1ом парке "],["^ (\\S+)[иоы]й (\\S+н)ий [Пп]арк "," $1ом $2ем парке "],["^ (\\S+н)ий (\\S+)[иоы]й [Пп]арк "," $1ем $2ом парке "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Пп]арк "," $1ом $2ом парке "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Пп]арк "," $1ом $2ом парке "],["^ (\\d+)-й (\\S+н)ий [Пп]арк "," $1-м $2ем парке "],["^ (\\d+)-й (\\S+)[иоы]й [Пп]арк "," $1-м $2ом парке "],["^ (\\d+)-й (\\S+[иы]н) [Пп]арк "," $1-м $2ом парке "],["^ [Пп]арк "," парке "],["^ (\\S+)[иоы]й-(\\S+)[иоы]й [Пп]ереулок "," $1ом-$2ом переулке "],["^ (\\d+)-й (\\S+)[иоы]й-(\\S+)[иоы]й [Пп]ереулок "," $1-м $2ом-$3ом переулке "],["^ (\\S+н)ий [Пп]ереулок "," $1ем переулке "],["^ (\\S+)[иоы]й [Пп]ереулок "," $1ом переулке "],["^ (\\S+[еёо]в) [Пп]ереулок "," $1ом переулке "],["^ (\\S+[иы]н) [Пп]ереулок "," $1ом переулке "],["^ (\\S+)[иоы]й (\\S+н)ий [Пп]ереулок "," $1ом $2ем переулке "],["^ (\\S+н)ий (\\S+)[иоы]й [Пп]ереулок "," $1ем $2ом переулке "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Пп]ереулок "," $1ом $2ом переулке "],["^ (\\S+)[иоы]й (\\S+[еёо]в) [Пп]ереулок "," $1ом $2ом переулке "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Пп]ереулок "," $1ом $2ом переулке "],["^ (\\d+)-й [Пп]ереулок "," $1-м переулке "],["^ (\\d+)-й (\\S+н)ий [Пп]ереулок "," $1-м $2ем переулке "],["^ (\\d+)-й (\\S+)[иоы]й [Пп]ереулок "," $1-м $2ом переулке "],["^ (\\d+)-й (\\S+[еёо]в) [Пп]ереулок "," $1-м $2ом переулке "],["^ (\\d+)-й (\\S+[иы]н) [Пп]ереулок "," $1-м $2ом переулке "],["^ [Пп]ереулок "," переулке "],["^ [Пп]одъезд "," подъезде "],["^ (\\S+[еёо]в)-(\\S+)[иоы]й [Пп]роезд "," $1ом-$2ом проезде "],["^ (\\S+н)ий [Пп]роезд "," $1ем проезде "],["^ (\\S+)[иоы]й [Пп]роезд "," $1ом проезде "],["^ (\\S+[еёо]в) [Пп]роезд "," $1ом проезде "],["^ (\\S+[иы]н) [Пп]роезд "," $1ом проезде "],["^ (\\S+)[иоы]й (\\S+н)ий [Пп]роезд "," $1ом $2ем проезде "],["^ (\\S+н)ий (\\S+)[иоы]й [Пп]роезд "," $1ем $2ом проезде "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Пп]роезд "," $1ом $2ом проезде "],["^ (\\S+)[иоы]й (\\S+[еёо]в) [Пп]роезд "," $1ом $2ом проезде "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Пп]роезд "," $1ом $2ом проезде "],["^ (\\d+)-й [Пп]роезд "," $1-м проезде "],["^ (\\d+)-й (\\S+н)ий [Пп]роезд "," $1-м $2ем проезде "],["^ (\\d+)-й (\\S+)[иоы]й [Пп]роезд "," $1-м $2ом проезде "],["^ (\\d+)-й (\\S+[еёо]в) [Пп]роезд "," $1-м $2ом проезде "],["^ (\\d+)-й (\\S+[иы]н) [Пп]роезд "," $1-м $2ом проезде "],["^ (\\d+)-й (\\S+н)ий (\\S+)[иоы]й [Пп]роезд "," $1-м $2ем $3ом проезде "],["^ (\\d+)-й (\\S+)[иоы]й (\\S+)[иоы]й [Пп]роезд "," $1-м $2ом $3ом проезде "],["^ [Пп]роезд "," проезде "],["^ (\\S+н)ий [Пп]роспект "," $1ем проспекте "],["^ (\\S+)[иоы]й [Пп]роспект "," $1ом проспекте "],["^ (\\S+[иы]н) [Пп]роспект "," $1ом проспекте "],["^ (\\S+)[иоы]й (\\S+н)ий [Пп]роспект "," $1ом $2ем проспекте "],["^ (\\S+н)ий (\\S+)[иоы]й [Пп]роспект "," $1ем $2ом проспекте "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Пп]роспект "," $1ом $2ом проспекте "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Пп]роспект "," $1ом $2ом проспекте "],["^ (\\d+)-й (\\S+н)ий [Пп]роспект "," $1-м $2ем проспекте "],["^ (\\d+)-й (\\S+)[иоы]й [Пп]роспект "," $1-м $2ом проспекте "],["^ (\\d+)-й (\\S+[иы]н) [Пп]роспект "," $1-м $2ом проспекте "],["^ [Пп]роспект "," проспекте "],["^ (\\S+н)ий [Пп]утепровод "," $1ем путепроводе "],["^ (\\S+)[иоы]й [Пп]утепровод "," $1ом путепроводе "],["^ (\\S+[иы]н) [Пп]утепровод "," $1ом путепроводе "],["^ (\\S+)[иоы]й (\\S+н)ий [Пп]утепровод "," $1ом $2ем путепроводе "],["^ (\\S+н)ий (\\S+)[иоы]й [Пп]утепровод "," $1ем $2ом путепроводе "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Пп]утепровод "," $1ом $2ом путепроводе "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Пп]утепровод "," $1ом $2ом путепроводе "],["^ (\\d+)-й (\\S+н)ий [Пп]утепровод "," $1-м $2ем путепроводе "],["^ (\\d+)-й (\\S+)[иоы]й [Пп]утепровод "," $1-м $2ом путепроводе "],["^ (\\d+)-й (\\S+[иы]н) [Пп]утепровод "," $1-м $2ом путепроводе "],["^ [Пп]утепровод "," путепроводе "],["^ (\\S+н)ий [Сс]пуск "," $1ем спуске "],["^ (\\S+)[иоы]й [Сс]пуск "," $1ом спуске "],["^ (\\S+[еёо]в) [Сс]пуск "," $1ом спуске "],["^ (\\S+[иы]н) [Сс]пуск "," $1ом спуске "],["^ (\\S+)[иоы]й (\\S+н)ий [Сс]пуск "," $1ом $2ем спуске "],["^ (\\S+н)ий (\\S+)[иоы]й [Сс]пуск "," $1ем $2ом спуске "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Сс]пуск "," $1ом $2ом спуске "],["^ (\\S+)[иоы]й (\\S+[еёо]в) [Сс]пуск "," $1ом $2ом спуске "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Сс]пуск "," $1ом $2ом спуске "],["^ (\\d+)-й (\\S+н)ий [Сс]пуск "," $1-м $2ем спуске "],["^ (\\d+)-й (\\S+)[иоы]й [Сс]пуск "," $1-м $2ом спуске "],["^ (\\d+)-й (\\S+[еёо]в) [Сс]пуск "," $1-м $2ом спуске "],["^ (\\d+)-й (\\S+[иы]н) [Сс]пуск "," $1-м $2ом спуске "],["^ [Сс]пуск "," спуске "],["^ (\\S+н)ий [Сс]ъезд "," $1ем съезде "],["^ (\\S+)[иоы]й [Сс]ъезд "," $1ом съезде "],["^ (\\S+[иы]н) [Сс]ъезд "," $1ом съезде "],["^ (\\S+)[иоы]й (\\S+н)ий [Сс]ъезд "," $1ом $2ем съезде "],["^ (\\S+н)ий (\\S+)[иоы]й [Сс]ъезд "," $1ем $2ом съезде "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Сс]ъезд "," $1ом $2ом съезде "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Сс]ъезд "," $1ом $2ом съезде "],["^ (\\d+)-й (\\S+н)ий [Сс]ъезд "," $1-м $2ем съезде "],["^ (\\d+)-й (\\S+)[иоы]й [Сс]ъезд "," $1-м $2ом съезде "],["^ (\\d+)-й (\\S+[иы]н) [Сс]ъезд "," $1-м $2ом съезде "],["^ [Сс]ъезд "," съезде "],["^ (\\S+н)ий [Тт][уо]ннель "," $1ем тоннеле "],["^ (\\S+)[иоы]й [Тт][уо]ннель "," $1ом тоннеле "],["^ (\\S+[иы]н) [Тт][уо]ннель "," $1ом тоннеле "],["^ (\\S+)[иоы]й (\\S+н)ий [Тт][уо]ннель "," $1ом $2ем тоннеле "],["^ (\\S+н)ий (\\S+)[иоы]й [Тт][уо]ннель "," $1ем $2ом тоннеле "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Тт][уо]ннель "," $1ом $2ом тоннеле "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Тт][уо]ннель "," $1ом $2ом тоннеле "],["^ (\\d+)-й (\\S+н)ий [Тт][уо]ннель "," $1-м $2ем тоннеле "],["^ (\\d+)-й (\\S+)[иоы]й [Тт][уо]ннель "," $1-м $2ом тоннеле "],["^ (\\d+)-й (\\S+[иы]н) [Тт][уо]ннель "," $1-м $2ом тоннеле "],["^ [Тт][уо]ннель "," тоннеле "],["^ (\\S+н)ий [Тт]ракт "," $1ем тракте "],["^ (\\S+)[иоы]й [Тт]ракт "," $1ом тракте "],["^ (\\S+[еёо]в) [Тт]ракт "," $1ом тракте "],["^ (\\S+[иы]н) [Тт]ракт "," $1ом тракте "],["^ (\\S+)[иоы]й (\\S+н)ий [Тт]ракт "," $1ом $2ем тракте "],["^ (\\S+н)ий (\\S+)[иоы]й [Тт]ракт "," $1ем $2ом тракте "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Тт]ракт "," $1ом $2ом тракте "],["^ (\\S+)[иоы]й (\\S+[еёо]в) [Тт]ракт "," $1ом $2ом тракте "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Тт]ракт "," $1ом $2ом тракте "],["^ (\\d+)-й (\\S+н)ий [Тт]ракт "," $1-м $2ем тракте "],["^ (\\d+)-й (\\S+)[иоы]й [Тт]ракт "," $1-м $2ом тракте "],["^ (\\d+)-й (\\S+[еёо]в) [Тт]ракт "," $1-м $2ом тракте "],["^ (\\d+)-й (\\S+[иы]н) [Тт]ракт "," $1-м $2ом тракте "],["^ [Тт]ракт "," тракте "],["^ (\\S+н)ий [Тт]упик "," $1ем тупике "],["^ (\\S+)[иоы]й [Тт]упик "," $1ом тупике "],["^ (\\S+[еёо]в) [Тт]упик "," $1ом тупике "],["^ (\\S+[иы]н) [Тт]упик "," $1ом тупике "],["^ (\\S+)[иоы]й (\\S+н)ий [Тт]упик "," $1ом $2ем тупике "],["^ (\\S+н)ий (\\S+)[иоы]й [Тт]упик "," $1ем $2ом тупике "],["^ (\\S+)[иоы]й (\\S+)[иоы]й [Тт]упик "," $1ом $2ом тупике "],["^ (\\S+)[иоы]й (\\S+[еёо]в) [Тт]упик "," $1ом $2ом тупике "],["^ (\\S+)[иоы]й (\\S+[иы]н) [Тт]упик "," $1ом $2ом тупике "],["^ (\\d+)-й [Тт]упик "," $1-м тупике "],["^ (\\d+)-й (\\S+н)ий [Тт]упик "," $1-м $2ем тупике "],["^ (\\d+)-й (\\S+)[иоы]й [Тт]упик "," $1-м $2ом тупике "],["^ (\\d+)-й (\\S+[еёо]в) [Тт]упик "," $1-м $2ом тупике "],["^ (\\d+)-й (\\S+[иы]н) [Тт]упик "," $1-м $2ом тупике "],["^ [Тт]упик "," тупике "],["^ (\\S+[ео])е ([Пп]олу)?[Кк]ольцо "," $1м $2кольце "],["^ (\\S+ье) ([Пп]олу)?[Кк]ольцо "," $1м $2кольце "],["^ (\\S+[ео])е (\\S+[ео])е ([Пп]олу)?[Кк]ольцо "," $1м $2м $3кольце "],["^ (\\S+ье) (\\S+[ео])е ([Пп]олу)?[Кк]ольцо "," $1м $2м $3кольце "],["^ (\\d+)-е (\\S+[ео])е ([Пп]олу)?[Кк]ольцо "," $1-м $2м $3кольце "],["^ (\\d+)-е (\\S+ье) ([Пп]олу)?[Кк]ольцо "," $1-м $2м $3кольце "],["^ ([Пп]олу)?[Кк]ольцо "," $1кольце "],["^ (\\S+[ео])е [Шш]оссе "," $1м шоссе "],["^ (\\S+ье) [Шш]оссе "," $1м шоссе "],["^ (\\S+[ео])е (\\S+[ео])е [Шш]оссе "," $1м $2м шоссе "],["^ (\\S+ье) (\\S+[ео])е [Шш]оссе "," $1м $2м шоссе "],["^ (\\d+)-е (\\S+[ео])е [Шш]оссе "," $1-м $2м шоссе "],["^ (\\d+)-е (\\S+ье) [Шш]оссе "," $1-м $2м шоссе "],[" ([Тт])ретом "," $1ретьем "],["([жч])ом ","$1ьем "]]}}},{}],23:[function(_dereq_,module,exports){module.exports={meta:{capitalizeFirstLetter:true},v5:{constants:{ordinalize:{1:"første",2:"anden",3:"tredje",4:"fjerde",5:"femte",6:"sjette",7:"syvende",8:"ottende",9:"niende",10:"tiende"},direction:{north:"Nord",northeast:"Nordøst",east:"Øst",southeast:"Sydøst",south:"Syd",southwest:"Sydvest",west:"Vest",northwest:"Nordvest"},modifier:{left:"venstresving",right:"højresving","sharp left":"skarpt venstresving","sharp right":"skarpt højresving","slight left":"svagt venstresving","slight right":"svagt højresving",straight:"ligeud",uturn:"U-vending"},lanes:{xo:"Hold til højre",ox:"Hold til venstre",xox:"Benyt midterste spor",oxo:"Hold til højre eller venstre"}},modes:{ferry:{default:"Tag færgen",name:"Tag færgen {way_name}",destination:"Tag færgen i retning {destination}"}},phrase:{"two linked by distance":"{instruction_one} derefter, efter {distance}, {instruction_two}","two linked":"{instruction_one}, derefter {instruction_two}","one in distance":"Efter {distance} {instruction_one}","name and ref":"{name} ({ref})","exit with number":"afkørsel {exit}"},arrive:{default:{default:"Du er ankommet til din {nth} destination",upcoming:"Du vil ankomme til din {nth} destination",short:"Du er ankommet","short-upcoming":"Du vil ankomme",named:"Du er ankommet til {waypoint_name}"},left:{default:"Du er ankommet til din {nth} destination, som befinder sig til venstre",upcoming:"Du vil ankomme til din {nth} destination på venstre hånd",short:"Du er ankommet","short-upcoming":"Du vil ankomme",named:"Du er ankommet til {waypoint_name}, som befinder sig til venstre"},right:{default:"Du er ankommet til din {nth} destination, som befinder sig til højre",upcoming:"Du vil ankomme til din {nth} destination på højre hånd",short:"Du er ankommet","short-upcoming":"Du vil ankomme",named:"Du er ankommet til {waypoint_name}, som befinder sig til højre"},"sharp left":{default:"Du er ankommet til din {nth} destination, som befinder sig til venstre",upcoming:"Du vil ankomme til din {nth} destination på venstre hånd",short:"Du er ankommet","short-upcoming":"Du vil ankomme",named:"Du er ankommet til {waypoint_name}, som befinder sig til venstre"},"sharp right":{default:"Du er ankommet til din {nth} destination, som befinder sig til højre",upcoming:"Du vil ankomme til din {nth} destination på højre hånd",short:"Du er ankommet","short-upcoming":"Du vil ankomme",named:"Du er ankommet til {waypoint_name}, som befinder sig til højre"},"slight right":{default:"Du er ankommet til din {nth} destination, som befinder sig til højre",upcoming:"Du vil ankomme til din {nth} destination på højre hånd",short:"Du er ankommet","short-upcoming":"Du vil ankomme",named:"Du er ankommet til {waypoint_name}, som befinder sig til højre"},"slight left":{default:"Du er ankommet til din {nth} destination, som befinder sig til venstre",upcoming:"Du vil ankomme til din {nth} destination på venstre hånd",short:"Du er ankommet","short-upcoming":"Du vil ankomme",named:"Du er ankommet til {waypoint_name}, som befinder sig til venstre"},straight:{default:"Du er ankommet til din {nth} destination, der befinder sig lige frem",upcoming:"Du vil ankomme til din {nth} destination foran dig",short:"Du er ankommet","short-upcoming":"Du vil ankomme",named:"Du er ankommet til {waypoint_name}, der befinder sig lige frem"}},continue:{default:{default:"Drej til {modifier}",name:"Drej til {modifier} videre ad {way_name}",destination:"Drej til {modifier} mod {destination}",exit:"Drej til {modifier} ad {way_name}"},straight:{default:"Fortsæt ligeud",
name:"Fortsæt ligeud ad {way_name}",destination:"Fortsæt mod {destination}",distance:"Fortsæt {distance} ligeud",namedistance:"Fortsæt {distance} ad {way_name}"},"sharp left":{default:"Drej skarpt til venstre",name:"Drej skarpt til venstre videre ad {way_name}",destination:"Drej skarpt til venstre mod {destination}"},"sharp right":{default:"Drej skarpt til højre",name:"Drej skarpt til højre videre ad {way_name}",destination:"Drej skarpt til højre mod {destination}"},"slight left":{default:"Drej left til venstre",name:"Drej let til venstre videre ad {way_name}",destination:"Drej let til venstre mod {destination}"},"slight right":{default:"Drej let til højre",name:"Drej let til højre videre ad {way_name}",destination:"Drej let til højre mod {destination}"},uturn:{default:"Foretag en U-vending",name:"Foretag en U-vending tilbage ad {way_name}",destination:"Foretag en U-vending mod {destination}"}},depart:{default:{default:"Kør mod {direction}",name:"Kør mod {direction} ad {way_name}",namedistance:"Fortsæt {distance} ad {way_name}mod {direction}"}},"end of road":{default:{default:"Drej til {modifier}",name:"Drej til {modifier} ad {way_name}",destination:"Drej til {modifier} mof {destination}"},straight:{default:"Fortsæt ligeud",name:"Fortsæt ligeud ad {way_name}",destination:"Fortsæt ligeud mod {destination}"},uturn:{default:"Foretag en U-vending for enden af vejen",name:"Foretag en U-vending ad {way_name} for enden af vejen",destination:"Foretag en U-vending mod {destination} for enden af vejen"}},fork:{default:{default:"Hold til {modifier} ved udfletningen",name:"Hold mod {modifier} på {way_name}",destination:"Hold mod {modifier} mod {destination}"},"slight left":{default:"Hold til venstre ved udfletningen",name:"Hold til venstre på {way_name}",destination:"Hold til venstre mod {destination}"},"slight right":{default:"Hold til højre ved udfletningen",name:"Hold til højre på {way_name}",destination:"Hold til højre mod {destination}"},"sharp left":{default:"Drej skarpt til venstre ved udfletningen",name:"Drej skarpt til venstre ad {way_name}",destination:"Drej skarpt til venstre mod {destination}"},"sharp right":{default:"Drej skarpt til højre ved udfletningen",name:"Drej skarpt til højre ad {way_name}",destination:"Drej skarpt til højre mod {destination}"},uturn:{default:"Foretag en U-vending",name:"Foretag en U-vending ad {way_name}",destination:"Foretag en U-vending mod {destination}"}},merge:{default:{default:"Flet til {modifier}",name:"Flet til {modifier} ad {way_name}",destination:"Flet til {modifier} mod {destination}"},straight:{default:"Flet",name:"Flet ind på {way_name}",destination:"Flet ind mod {destination}"},"slight left":{default:"Flet til venstre",name:"Flet til venstre ad {way_name}",destination:"Flet til venstre mod {destination}"},"slight right":{default:"Flet til højre",name:"Flet til højre ad {way_name}",destination:"Flet til højre mod {destination}"},"sharp left":{default:"Flet til venstre",name:"Flet til venstre ad {way_name}",destination:"Flet til venstre mod {destination}"},"sharp right":{default:"Flet til højre",name:"Flet til højre ad {way_name}",destination:"Flet til højre mod {destination}"},uturn:{default:"Foretag en U-vending",name:"Foretag en U-vending ad {way_name}",destination:"Foretag en U-vending mod {destination}"}},"new name":{default:{default:"Fortsæt {modifier}",name:"Fortsæt {modifier} ad {way_name}",destination:"Fortsæt {modifier} mod {destination}"},straight:{default:"Fortsæt ligeud",name:"Fortsæt ad {way_name}",destination:"Fortsæt mod {destination}"},"sharp left":{default:"Drej skarpt til venstre",name:"Drej skarpt til venstre ad {way_name}",destination:"Drej skarpt til venstre mod {destination}"},"sharp right":{default:"Drej skarpt til højre",name:"Drej skarpt til højre ad {way_name}",destination:"Drej skarpt til højre mod {destination}"},"slight left":{default:"Fortsæt til venstre",name:"Fortsæt til venstre ad {way_name}",destination:"Fortsæt til venstre mod {destination}"},"slight right":{default:"Fortsæt til højre",name:"Fortsæt til højre ad {way_name}",destination:"Fortsæt til højre mod {destination}"},uturn:{default:"Foretag en U-vending",name:"Foretag en U-vending ad {way_name}",destination:"Foretag en U-vending mod {destination}"}},notification:{default:{default:"Fortsæt {modifier}",name:"Fortsæt {modifier} ad {way_name}",destination:"Fortsæt {modifier} mod {destination}"},uturn:{default:"Foretag en U-vending",name:"Foretag en U-vending ad {way_name}",destination:"Foretag en U-vending mod {destination}"}},"off ramp":{default:{default:"Tag afkørslen",name:"Tag afkørslen ad {way_name}",destination:"Tag afkørslen mod {destination}",exit:"Vælg afkørsel {exit}",exit_destination:"Vælg afkørsel {exit} mod {destination}"},left:{default:"Tag afkørslen til venstre",name:"Tag afkørslen til venstre ad {way_name}",destination:"Tag afkørslen til venstre mod {destination}",exit:"Vælg afkørsel {exit} til venstre",exit_destination:"Vælg afkørsel {exit} til venstre mod {destination}\n"},right:{default:"Tag afkørslen til højre",name:"Tag afkørslen til højre ad {way_name}",destination:"Tag afkørslen til højre mod {destination}",exit:"Vælg afkørsel {exit} til højre",exit_destination:"Vælg afkørsel {exit} til højre mod {destination}"},"sharp left":{default:"Tag afkørslen til venstre",name:"Tag afkørslen til venstre ad {way_name}",destination:"Tag afkørslen til venstre mod {destination}",exit:"Vælg afkørsel {exit} til venstre",exit_destination:"Vælg afkørsel {exit} til venstre mod {destination}\n"},"sharp right":{default:"Tag afkørslen til højre",name:"Tag afkørslen til højre ad {way_name}",destination:"Tag afkørslen til højre mod {destination}",exit:"Vælg afkørsel {exit} til højre",exit_destination:"Vælg afkørsel {exit} til højre mod {destination}"},"slight left":{default:"Tag afkørslen til venstre",name:"Tag afkørslen til venstre ad {way_name}",destination:"Tag afkørslen til venstre mod {destination}",exit:"Vælg afkørsel {exit} til venstre",exit_destination:"Vælg afkørsel {exit} til venstre mod {destination}\n"},"slight right":{default:"Tag afkørslen til højre",name:"Tag afkørslen til højre ad {way_name}",destination:"Tag afkørslen til højre mod {destination}",exit:"Vælg afkørsel {exit} til højre",exit_destination:"Vælg afkørsel {exit} til højre mod {destination}"}},"on ramp":{default:{default:"Tag afkørslen",name:"Tag afkørslen ad {way_name}",destination:"Tag afkørslen mod {destination}"},left:{default:"Tag afkørslen til venstre",name:"Tag afkørslen til venstre ad {way_name}",destination:"Tag afkørslen til venstre mod {destination}"},right:{default:"Tag afkørslen til højre",name:"Tag afkørslen til højre ad {way_name}",destination:"Tag afkørslen til højre mod {destination}"},"sharp left":{default:"Tag afkørslen til venstre",name:"Tag afkørslen til venstre ad {way_name}",destination:"Tag afkørslen til venstre mod {destination}"},"sharp right":{default:"Tag afkørslen til højre",name:"Tag afkørslen til højre ad {way_name}",destination:"Tag afkørslen til højre mod {destination}"},"slight left":{default:"Tag afkørslen til venstre",name:"Tag afkørslen til venstre ad {way_name}",destination:"Tag afkørslen til venstre mod {destination}"},"slight right":{default:"Tag afkørslen til højre",name:"Tag afkørslen til højre ad {way_name}",destination:"Tag afkørslen til højre mod {destination}"}},rotary:{default:{default:{default:"Kør ind i rundkørslen",name:"Tag rundkørslen og kør fra ad {way_name}",destination:"Tag rundkørslen og kør mod {destination}"},name:{default:"Kør ind i {rotary_name}",name:"Kør ind i {rotary_name} og kør ad {way_name} ",destination:"Kør ind i {rotary_name} og kør mod {destination}"},exit:{default:"Tag rundkørslen og forlad ved {exit_number} afkørsel",name:"Tag rundkørslen og forlad ved {exit_number} afkørsel ad {way_name}",destination:"Tag rundkørslen og forlad ved {exit_number} afkørsel mod {destination}"},name_exit:{default:"Kør ind i {rotary_name} og forlad ved {exit_number} afkørsel",name:"Kør ind i {rotary_name} og forlad ved {exit_number} afkørsel ad {way_name}",destination:"Kør ind i {rotary_name} og forlad ved {exit_number} afkørsel mod {destination}"}}},roundabout:{default:{exit:{default:"Tag rundkørslen og forlad ved {exit_number} afkørsel",name:"Tag rundkørslen og forlad ved {exit_number} afkørsel ad {way_name}",destination:"Tag rundkørslen og forlad ved {exit_number} afkørsel mod {destination}"},default:{default:"Kør ind i rundkørslen",name:"Tag rundkørslen og kør fra ad {way_name}",destination:"Tag rundkørslen og kør mod {destination}"}}},"roundabout turn":{default:{default:"Foretag et {modifier}",name:"Foretag et {modifier} ad {way_name}",destination:"Foretag et {modifier} mod {destination}"},left:{default:"Drej til venstre",name:"Drej til venstre ad {way_name}",destination:"Drej til venstre mod {destination}"},right:{default:"Drej til højre",name:"Drej til højre ad {way_name}",destination:"Drej til højre mod {destination}"},straight:{default:"Fortsæt ligeud",name:"Fortsæt ligeud ad {way_name}",destination:"Fortsæt ligeud mod {destination}"}},"exit roundabout":{default:{default:"Forlad rundkørslen",name:"Forlad rundkørslen ad {way_name}",destination:"Forlad rundkørslen mod  {destination}"}},"exit rotary":{default:{default:"Forlad rundkørslen",name:"Forlad rundkørslen ad {way_name}",destination:"Forlad rundkørslen mod {destination}"}},turn:{default:{default:"Foretag et {modifier}",name:"Foretag et {modifier} ad {way_name}",destination:"Foretag et {modifier} mod {destination}"},left:{default:"Drej til venstre",name:"Drej til venstre ad {way_name}",destination:"Drej til venstre mod {destination}"},right:{default:"Drej til højre",name:"Drej til højre ad {way_name}",destination:"Drej til højre mod {destination}"},straight:{default:"Fortsæt ligeud",name:"Kør ligeud ad {way_name}",destination:"Kør ligeud mod {destination}"}},"use lane":{no_lanes:{default:"Fortsæt ligeud"},default:{default:"{lane_instruction}"}}}}},{}],24:[function(_dereq_,module,exports){module.exports={meta:{capitalizeFirstLetter:true},v5:{constants:{ordinalize:{1:"erste",2:"zweite",3:"dritte",4:"vierte",5:"fünfte",6:"sechste",7:"siebente",8:"achte",9:"neunte",10:"zehnte"},direction:{north:"Norden",northeast:"Nordosten",east:"Osten",southeast:"Südosten",south:"Süden",southwest:"Südwesten",west:"Westen",northwest:"Nordwesten"},modifier:{left:"links",right:"rechts","sharp left":"scharf links","sharp right":"scharf rechts","slight left":"leicht links","slight right":"leicht rechts",straight:"geradeaus",uturn:"180°-Wendung"},lanes:{xo:"Rechts halten",ox:"Links halten",xox:"Mittlere Spur nutzen",oxo:"Rechts oder links halten"}},modes:{ferry:{default:"Fähre nehmen",name:"Fähre nehmen {way_name}",destination:"Fähre nehmen Richtung {destination}"}},phrase:{"two linked by distance":"{instruction_one} danach in {distance} {instruction_two}","two linked":"{instruction_one} danach {instruction_two}","one in distance":"In {distance}, {instruction_one}","name and ref":"{name} ({ref})","exit with number":"exit {exit}"},arrive:{default:{default:"Sie haben Ihr {nth} Ziel erreicht",upcoming:"Sie haben Ihr {nth} Ziel erreicht",short:"Sie haben Ihr {nth} Ziel erreicht","short-upcoming":"Sie haben Ihr {nth} Ziel erreicht",named:"Sie haben Ihr {waypoint_name}"},left:{default:"Sie haben Ihr {nth} Ziel erreicht, es befindet sich links",upcoming:"Sie haben Ihr {nth} Ziel erreicht, es befindet sich links",short:"Sie haben Ihr {nth} Ziel erreicht","short-upcoming":"Sie haben Ihr {nth} Ziel erreicht",named:"Sie haben Ihr {waypoint_name}, es befindet sich links"},right:{default:"Sie haben Ihr {nth} Ziel erreicht, es befindet sich rechts",upcoming:"Sie haben Ihr {nth} Ziel erreicht, es befindet sich rechts",short:"Sie haben Ihr {nth} Ziel erreicht","short-upcoming":"Sie haben Ihr {nth} Ziel erreicht",named:"Sie haben Ihr {waypoint_name}, es befindet sich rechts"},"sharp left":{default:"Sie haben Ihr {nth} Ziel erreicht, es befindet sich links",upcoming:"Sie haben Ihr {nth} Ziel erreicht, es befindet sich links",short:"Sie haben Ihr {nth} Ziel erreicht","short-upcoming":"Sie haben Ihr {nth} Ziel erreicht",named:"Sie haben Ihr {waypoint_name}, es befindet sich links"},"sharp right":{default:"Sie haben Ihr {nth} Ziel erreicht, es befindet sich rechts",upcoming:"Sie haben Ihr {nth} Ziel erreicht, es befindet sich rechts",short:"Sie haben Ihr {nth} Ziel erreicht","short-upcoming":"Sie haben Ihr {nth} Ziel erreicht",named:"Sie haben Ihr {waypoint_name}, es befindet sich rechts"},"slight right":{default:"Sie haben Ihr {nth} Ziel erreicht, es befindet sich rechts",upcoming:"Sie haben Ihr {nth} Ziel erreicht, es befindet sich rechts",short:"Sie haben Ihr {nth} Ziel erreicht","short-upcoming":"Sie haben Ihr {nth} Ziel erreicht",named:"Sie haben Ihr {waypoint_name}, es befindet sich rechts"},"slight left":{default:"Sie haben Ihr {nth} Ziel erreicht, es befindet sich links",upcoming:"Sie haben Ihr {nth} Ziel erreicht, es befindet sich links",short:"Sie haben Ihr {nth} Ziel erreicht","short-upcoming":"Sie haben Ihr {nth} Ziel erreicht",named:"Sie haben Ihr {waypoint_name}, es befindet sich links"},straight:{default:"Sie haben Ihr {nth} Ziel erreicht, es befindet sich geradeaus",upcoming:"Sie haben Ihr {nth} Ziel erreicht, es befindet sich geradeaus",short:"Sie haben Ihr {nth} Ziel erreicht","short-upcoming":"Sie haben Ihr {nth} Ziel erreicht",named:"Sie haben Ihr {waypoint_name}, es befindet sich geradeaus"}},continue:{default:{default:"{modifier} abbiegen",name:"{modifier} weiterfahren auf {way_name}",destination:"{modifier} abbiegen Richtung {destination}",exit:"{modifier} abbiegen auf {way_name}"},straight:{default:"Geradeaus weiterfahren",name:"Geradeaus weiterfahren auf {way_name}",destination:"Weiterfahren in Richtung {destination}",distance:"Geradeaus weiterfahren für {distance}",namedistance:"Geradeaus weiterfahren auf {way_name} für {distance}"},"sharp left":{default:"Scharf links",name:"Scharf links weiterfahren auf {way_name}",destination:"Scharf links Richtung {destination}"},"sharp right":{default:"Scharf rechts",name:"Scharf rechts weiterfahren auf {way_name}",destination:"Scharf rechts Richtung {destination}"},"slight left":{default:"Leicht links",name:"Leicht links weiter auf {way_name}",destination:"Leicht links weiter Richtung {destination}"},"slight right":{default:"Leicht rechts weiter",name:"Leicht rechts weiter auf {way_name}",destination:"Leicht rechts weiter Richtung {destination}"},uturn:{default:"180°-Wendung",name:"180°-Wendung auf {way_name}",destination:"180°-Wendung Richtung {destination}"}},depart:{default:{default:"Fahren Sie Richtung {direction}",name:"Fahren Sie Richtung {direction} auf {way_name}",namedistance:"Fahren Sie Richtung {direction} auf {way_name} für {distance}"}},"end of road":{default:{default:"{modifier} abbiegen",name:"{modifier} abbiegen auf {way_name}",destination:"{modifier} abbiegen Richtung {destination}"},straight:{default:"Geradeaus weiterfahren",name:"Geradeaus weiterfahren auf {way_name}",destination:"Geradeaus weiterfahren Richtung {destination}"},uturn:{default:"180°-Wendung am Ende der Straße",name:"180°-Wendung auf {way_name} am Ende der Straße",destination:"180°-Wendung Richtung {destination} am Ende der Straße"}},fork:{default:{default:"{modifier} halten an der Gabelung",name:"{modifier} halten an der Gabelung auf {way_name}",destination:"{modifier}  halten an der Gabelung Richtung {destination}"},"slight left":{default:"Links halten an der Gabelung",name:"Links halten an der Gabelung auf {way_name}",destination:"Links halten an der Gabelung Richtung {destination}"},"slight right":{default:"Rechts halten an der Gabelung",name:"Rechts halten an der Gabelung auf {way_name}",destination:"Rechts halten an der Gabelung Richtung {destination}"},"sharp left":{default:"Scharf links abbiegen an der Gabelung",name:"Scharf links auf {way_name}",destination:"Scharf links Richtung {destination}"},"sharp right":{default:"Scharf rechts abbiegen an der Gabelung",name:"Scharf rechts auf {way_name}",destination:"Scharf rechts Richtung {destination}"},uturn:{default:"180°-Wendung",name:"180°-Wendung auf {way_name}",destination:"180°-Wendung Richtung {destination}"}},merge:{default:{default:"{modifier} auffahren",name:"{modifier} auffahren auf {way_name}",destination:"{modifier} auffahren Richtung {destination}"},straight:{default:"geradeaus auffahren",name:"geradeaus auffahren auf {way_name}",destination:"geradeaus auffahren Richtung {destination}"},"slight left":{default:"Leicht links auffahren",name:"Leicht links auffahren auf {way_name}",destination:"Leicht links auffahren Richtung {destination}"},"slight right":{default:"Leicht rechts auffahren",name:"Leicht rechts auffahren auf {way_name}",destination:"Leicht rechts auffahren Richtung {destination}"},"sharp left":{default:"Scharf links auffahren",name:"Scharf links auffahren auf {way_name}",destination:"Scharf links auffahren Richtung {destination}"},"sharp right":{default:"Scharf rechts auffahren",name:"Scharf rechts auffahren auf {way_name}",destination:"Scharf rechts auffahren Richtung {destination}"},uturn:{default:"180°-Wendung",name:"180°-Wendung auf {way_name}",destination:"180°-Wendung Richtung {destination}"}},"new name":{default:{default:"{modifier} weiterfahren",name:"{modifier} weiterfahren auf {way_name}",destination:"{modifier} weiterfahren Richtung {destination}"},straight:{default:"Geradeaus weiterfahren",name:"Weiterfahren auf {way_name}",destination:"Weiterfahren in Richtung {destination}"},"sharp left":{default:"Scharf links",name:"Scharf links auf {way_name}",destination:"Scharf links Richtung {destination}"},"sharp right":{default:"Scharf rechts",name:"Scharf rechts auf {way_name}",destination:"Scharf rechts Richtung {destination}"},"slight left":{default:"Leicht links weiter",name:"Leicht links weiter auf {way_name}",destination:"Leicht links weiter Richtung {destination}"},"slight right":{default:"Leicht rechts weiter",name:"Leicht rechts weiter auf {way_name}",destination:"Leicht rechts weiter Richtung {destination}"},uturn:{default:"180°-Wendung",name:"180°-Wendung auf {way_name}",destination:"180°-Wendung Richtung {destination}"}},notification:{default:{default:"{modifier} weiterfahren",name:"{modifier} weiterfahren auf {way_name}",destination:"{modifier} weiterfahren Richtung {destination}"},uturn:{default:"180°-Wendung",name:"180°-Wendung auf {way_name}",destination:"180°-Wendung Richtung {destination}"}},"off ramp":{default:{default:"Ausfahrt nehmen",name:"Ausfahrt nehmen auf {way_name}",destination:"Ausfahrt nehmen Richtung {destination}",exit:"Ausfahrt {exit} nehmen",exit_destination:"Ausfahrt {exit} nehmen Richtung {destination}"},left:{default:"Ausfahrt links nehmen",name:"Ausfahrt links nehmen auf {way_name}",destination:"Ausfahrt links nehmen Richtung {destination}",exit:"Ausfahrt {exit} links nehmen",exit_destination:"Ausfahrt {exit} links nehmen Richtung {destination}"},right:{default:"Ausfahrt rechts nehmen",name:"Ausfahrt rechts nehmen Richtung {way_name}",destination:"Ausfahrt rechts nehmen Richtung {destination}",exit:"Ausfahrt {exit} rechts nehmen",exit_destination:"Ausfahrt {exit} nehmen Richtung {destination}"},"sharp left":{default:"Ausfahrt links nehmen",name:"Ausfahrt links Seite nehmen auf {way_name}",destination:"Ausfahrt links nehmen Richtung {destination}",exit:"Ausfahrt {exit} links nehmen",exit_destination:"Ausfahrt{exit} links nehmen Richtung {destination}"},"sharp right":{default:"Ausfahrt rechts nehmen",name:"Ausfahrt rechts nehmen auf {way_name}",destination:"Ausfahrt rechts nehmen Richtung {destination}",exit:"Ausfahrt {exit} rechts nehmen",exit_destination:"Ausfahrt {exit} nehmen Richtung {destination}"},"slight left":{default:"Ausfahrt links nehmen",name:"Ausfahrt links nehmen auf {way_name}",destination:"Ausfahrt links nehmen Richtung {destination}",exit:"Ausfahrt {exit} nehmen",exit_destination:"Ausfahrt {exit} links nehmen Richtung {destination}"},"slight right":{default:"Ausfahrt rechts nehmen",name:"Ausfahrt rechts nehmen auf {way_name}",destination:"Ausfahrt rechts nehmen Richtung {destination}",exit:"Ausfahrt {exit} rechts nehmen",exit_destination:"Ausfahrt {exit} nehmen Richtung {destination}"}},"on ramp":{default:{default:"Auffahrt nehmen",name:"Auffahrt nehmen auf {way_name}",destination:"Auffahrt nehmen Richtung {destination}"},left:{default:"Auffahrt links nehmen",name:"Auffahrt links nehmen auf {way_name}",destination:"Auffahrt links nehmen Richtung {destination}"},right:{default:"Auffahrt rechts nehmen",name:"Auffahrt rechts nehmen auf {way_name}",destination:"Auffahrt rechts nehmen Richtung {destination}"},"sharp left":{default:"Auffahrt links nehmen",name:"Auffahrt links nehmen auf {way_name}",destination:"Auffahrt links nehmen Richtung {destination}"},"sharp right":{default:"Auffahrt rechts nehmen",name:"Auffahrt rechts nehmen auf {way_name}",destination:"Auffahrt rechts nehmen Richtung {destination}"},"slight left":{default:"Auffahrt links Seite nehmen",name:"Auffahrt links nehmen auf {way_name}",destination:"Auffahrt links nehmen Richtung {destination}"},"slight right":{default:"Auffahrt rechts nehmen",name:"Auffahrt rechts nehmen auf {way_name}",destination:"Auffahrt rechts nehmen Richtung {destination}"}},rotary:{default:{default:{default:"In den Kreisverkehr fahren",name:"Im Kreisverkehr die Ausfahrt auf {way_name} nehmen",destination:"Im Kreisverkehr die Ausfahrt Richtung {destination} nehmen"},name:{default:"In {rotary_name} fahren",name:"In {rotary_name} die Ausfahrt auf {way_name} nehmen",destination:"In {rotary_name} die Ausfahrt Richtung {destination} nehmen"},exit:{default:"Im Kreisverkehr die {exit_number} Ausfahrt nehmen",name:"Im Kreisverkehr die {exit_number} Ausfahrt nehmen auf {way_name}",destination:"Im Kreisverkehr die {exit_number} Ausfahrt nehmen Richtung {destination}"},name_exit:{default:"In den Kreisverkehr fahren und {exit_number} Ausfahrt nehmen",name:"In den Kreisverkehr fahren und {exit_number} Ausfahrt nehmen auf {way_name}",destination:"In den Kreisverkehr fahren und {exit_number} Ausfahrt nehmen Richtung {destination}"}}},roundabout:{default:{exit:{default:"Im Kreisverkehr die {exit_number} Ausfahrt nehmen",name:"Im Kreisverkehr die {exit_number} Ausfahrt nehmen auf {way_name}",destination:"Im Kreisverkehr die {exit_number} Ausfahrt nehmen Richtung {destination}"},default:{default:"In den Kreisverkehr fahren",name:"Im Kreisverkehr die Ausfahrt auf {way_name} nehmen",destination:"Im Kreisverkehr die Ausfahrt Richtung {destination} nehmen"}}},"roundabout turn":{default:{default:"{modifier} abbiegen",name:"{modifier} abbiegen auf {way_name}",destination:"{modifier} abbiegen Richtung {destination}"},left:{default:"Links abbiegen",name:"Links abbiegen auf {way_name}",destination:"Links abbiegen Richtung {destination}"},right:{default:"Rechts abbiegen",name:"Rechts abbiegen auf {way_name}",destination:"Rechts abbiegen Richtung {destination}"},straight:{default:"Geradeaus weiterfahren",name:"Geradeaus weiterfahren auf {way_name}",destination:"Geradeaus weiterfahren Richtung {destination}"}},"exit roundabout":{default:{default:"{modifier} abbiegen",name:"{modifier} abbiegen auf {way_name}",destination:"{modifier} abbiegen Richtung {destination}"},left:{default:"Links abbiegen",name:"Links abbiegen auf {way_name}",destination:"Links abbiegen Richtung {destination}"},right:{default:"Rechts abbiegen",name:"Rechts abbiegen auf {way_name}",destination:"Rechts abbiegen Richtung {destination}"},straight:{default:"Geradeaus weiterfahren",name:"Geradeaus weiterfahren auf {way_name}",destination:"Geradeaus weiterfahren Richtung {destination}"}},"exit rotary":{default:{default:"{modifier} abbiegen",name:"{modifier} abbiegen auf {way_name}",destination:"{modifier} abbiegen Richtung {destination}"},left:{default:"Links abbiegen",name:"Links abbiegen auf {way_name}",destination:"Links abbiegen Richtung {destination}"},right:{default:"Rechts abbiegen",name:"Rechts abbiegen auf {way_name}",destination:"Rechts abbiegen Richtung {destination}"},straight:{default:"Geradeaus weiterfahren",name:"Geradeaus weiterfahren auf {way_name}",destination:"Geradeaus weiterfahren Richtung {destination}"}},turn:{default:{default:"{modifier} abbiegen",name:"{modifier} abbiegen auf {way_name}",destination:"{modifier} abbiegen Richtung {destination}"},left:{default:"Links abbiegen",name:"Links abbiegen auf {way_name}",destination:"Links abbiegen Richtung {destination}"},right:{default:"Rechts abbiegen",name:"Rechts abbiegen auf {way_name}",destination:"Rechts abbiegen Richtung {destination}"},straight:{default:"Geradeaus weiterfahren",name:"Geradeaus weiterfahren auf {way_name}",destination:"Geradeaus weiterfahren Richtung {destination}"}},"use lane":{no_lanes:{default:"Geradeaus weiterfahren"},default:{default:"{lane_instruction}"}}}}},{}],25:[function(_dereq_,module,exports){module.exports={meta:{capitalizeFirstLetter:true},v5:{constants:{ordinalize:{1:"1st",2:"2nd",3:"3rd",4:"4th",5:"5th",6:"6th",7:"7th",8:"8th",9:"9th",10:"10th"},direction:{north:"north",northeast:"northeast",east:"east",southeast:"southeast",south:"south",southwest:"southwest",west:"west",northwest:"northwest"},modifier:{left:"left",right:"right","sharp left":"sharp left","sharp right":"sharp right","slight left":"slight left","slight right":"slight right",straight:"straight",uturn:"U-turn"},lanes:{xo:"Keep right",ox:"Keep left",xox:"Keep in the middle",oxo:"Keep left or right"}},modes:{ferry:{default:"Take the ferry",name:"Take the ferry {way_name}",destination:"Take the ferry towards {destination}"}},phrase:{"two linked by distance":"{instruction_one}, then, in {distance}, {instruction_two}","two linked":"{instruction_one}, then {instruction_two}","one in distance":"In {distance}, {instruction_one}","name and ref":"{name} ({ref})","exit with number":"exit {exit}"},arrive:{default:{default:"You have arrived at your {nth} destination",upcoming:"You will arrive at your {nth} destination",short:"You have arrived","short-upcoming":"You will arrive",named:"You have arrived at {waypoint_name}"},left:{default:"You have arrived at your {nth} destination, on the left",upcoming:"You will arrive at your {nth} destination, on the left",short:"You have arrived","short-upcoming":"You will arrive",named:"You have arrived at {waypoint_name}, on the left"},right:{default:"You have arrived at your {nth} destination, on the right",upcoming:"You will arrive at your {nth} destination, on the right",short:"You have arrived","short-upcoming":"You will arrive",named:"You have arrived at {waypoint_name}, on the right"},"sharp left":{default:"You have arrived at your {nth} destination, on the left",upcoming:"You will arrive at your {nth} destination, on the left",short:"You have arrived","short-upcoming":"You will arrive",named:"You have arrived at {waypoint_name}, on the left"},"sharp right":{default:"You have arrived at your {nth} destination, on the right",upcoming:"You will arrive at your {nth} destination, on the right",short:"You have arrived","short-upcoming":"You will arrive",named:"You have arrived at {waypoint_name}, on the right"},"slight right":{default:"You have arrived at your {nth} destination, on the right",upcoming:"You will arrive at your {nth} destination, on the right",short:"You have arrived","short-upcoming":"You will arrive",named:"You have arrived at {waypoint_name}, on the right"},"slight left":{default:"You have arrived at your {nth} destination, on the left",upcoming:"You will arrive at your {nth} destination, on the left",short:"You have arrived","short-upcoming":"You will arrive",named:"You have arrived at {waypoint_name}, on the left"},straight:{default:"You have arrived at your {nth} destination, straight ahead",upcoming:"You will arrive at your {nth} destination, straight ahead",short:"You have arrived","short-upcoming":"You will arrive",named:"You have arrived at {waypoint_name}, straight ahead"}},continue:{default:{default:"Turn {modifier}",name:"Turn {modifier} to stay on {way_name}",destination:"Turn {modifier} towards {destination}",exit:"Turn {modifier} onto {way_name}"},straight:{default:"Continue straight",name:"Continue straight to stay on {way_name}",destination:"Continue towards {destination}",distance:"Continue straight for {distance}",namedistance:"Continue on {way_name} for {distance}"},"sharp left":{default:"Make a sharp left",name:"Make a sharp left to stay on {way_name}",destination:"Make a sharp left towards {destination}"},"sharp right":{default:"Make a sharp right",name:"Make a sharp right to stay on {way_name}",destination:"Make a sharp right towards {destination}"},"slight left":{default:"Make a slight left",name:"Make a slight left to stay on {way_name}",destination:"Make a slight left towards {destination}"},"slight right":{default:"Make a slight right",name:"Make a slight right to stay on {way_name}",destination:"Make a slight right towards {destination}"},uturn:{default:"Make a U-turn",name:"Make a U-turn and continue on {way_name}",destination:"Make a U-turn towards {destination}"}},depart:{default:{default:"Head {direction}",name:"Head {direction} on {way_name}",namedistance:"Head {direction} on {way_name} for {distance}"}},"end of road":{default:{default:"Turn {modifier}",name:"Turn {modifier} onto {way_name}",destination:"Turn {modifier} towards {destination}"},straight:{default:"Continue straight",name:"Continue straight onto {way_name}",destination:"Continue straight towards {destination}"},uturn:{default:"Make a U-turn at the end of the road",name:"Make a U-turn onto {way_name} at the end of the road",destination:"Make a U-turn towards {destination} at the end of the road"}},fork:{default:{default:"Keep {modifier} at the fork",name:"Keep {modifier} onto {way_name}",destination:"Keep {modifier} towards {destination}"},"slight left":{default:"Keep left at the fork",name:"Keep left onto {way_name}",destination:"Keep left towards {destination}"},"slight right":{default:"Keep right at the fork",name:"Keep right onto {way_name}",destination:"Keep right towards {destination}"},"sharp left":{default:"Take a sharp left at the fork",name:"Take a sharp left onto {way_name}",destination:"Take a sharp left towards {destination}"},"sharp right":{default:"Take a sharp right at the fork",name:"Take a sharp right onto {way_name}",destination:"Take a sharp right towards {destination}"},uturn:{default:"Make a U-turn",name:"Make a U-turn onto {way_name}",destination:"Make a U-turn towards {destination}"}},merge:{default:{default:"Merge {modifier}",name:"Merge {modifier} onto {way_name}",destination:"Merge {modifier} towards {destination}"},straight:{default:"Merge",name:"Merge onto {way_name}",destination:"Merge towards {destination}"},"slight left":{default:"Merge left",name:"Merge left onto {way_name}",destination:"Merge left towards {destination}"},"slight right":{default:"Merge right",name:"Merge right onto {way_name}",destination:"Merge right towards {destination}"},"sharp left":{default:"Merge left",name:"Merge left onto {way_name}",destination:"Merge left towards {destination}"},"sharp right":{default:"Merge right",name:"Merge right onto {way_name}",destination:"Merge right towards {destination}"},uturn:{default:"Make a U-turn",name:"Make a U-turn onto {way_name}",destination:"Make a U-turn towards {destination}"}},"new name":{default:{default:"Continue {modifier}",name:"Continue {modifier} onto {way_name}",destination:"Continue {modifier} towards {destination}"},straight:{default:"Continue straight",name:"Continue onto {way_name}",destination:"Continue towards {destination}"},"sharp left":{default:"Take a sharp left",name:"Take a sharp left onto {way_name}",destination:"Take a sharp left towards {destination}"},"sharp right":{default:"Take a sharp right",name:"Take a sharp right onto {way_name}",destination:"Take a sharp right towards {destination}"},"slight left":{default:"Continue slightly left",name:"Continue slightly left onto {way_name}",
destination:"Continue slightly left towards {destination}"},"slight right":{default:"Continue slightly right",name:"Continue slightly right onto {way_name}",destination:"Continue slightly right towards {destination}"},uturn:{default:"Make a U-turn",name:"Make a U-turn onto {way_name}",destination:"Make a U-turn towards {destination}"}},notification:{default:{default:"Continue {modifier}",name:"Continue {modifier} onto {way_name}",destination:"Continue {modifier} towards {destination}"},uturn:{default:"Make a U-turn",name:"Make a U-turn onto {way_name}",destination:"Make a U-turn towards {destination}"}},"off ramp":{default:{default:"Take the ramp",name:"Take the ramp onto {way_name}",destination:"Take the ramp towards {destination}",exit:"Take exit {exit}",exit_destination:"Take exit {exit} towards {destination}"},left:{default:"Take the ramp on the left",name:"Take the ramp on the left onto {way_name}",destination:"Take the ramp on the left towards {destination}",exit:"Take exit {exit} on the left",exit_destination:"Take exit {exit} on the left towards {destination}"},right:{default:"Take the ramp on the right",name:"Take the ramp on the right onto {way_name}",destination:"Take the ramp on the right towards {destination}",exit:"Take exit {exit} on the right",exit_destination:"Take exit {exit} on the right towards {destination}"},"sharp left":{default:"Take the ramp on the left",name:"Take the ramp on the left onto {way_name}",destination:"Take the ramp on the left towards {destination}",exit:"Take exit {exit} on the left",exit_destination:"Take exit {exit} on the left towards {destination}"},"sharp right":{default:"Take the ramp on the right",name:"Take the ramp on the right onto {way_name}",destination:"Take the ramp on the right towards {destination}",exit:"Take exit {exit} on the right",exit_destination:"Take exit {exit} on the right towards {destination}"},"slight left":{default:"Take the ramp on the left",name:"Take the ramp on the left onto {way_name}",destination:"Take the ramp on the left towards {destination}",exit:"Take exit {exit} on the left",exit_destination:"Take exit {exit} on the left towards {destination}"},"slight right":{default:"Take the ramp on the right",name:"Take the ramp on the right onto {way_name}",destination:"Take the ramp on the right towards {destination}",exit:"Take exit {exit} on the right",exit_destination:"Take exit {exit} on the right towards {destination}"}},"on ramp":{default:{default:"Take the ramp",name:"Take the ramp onto {way_name}",destination:"Take the ramp towards {destination}"},left:{default:"Take the ramp on the left",name:"Take the ramp on the left onto {way_name}",destination:"Take the ramp on the left towards {destination}"},right:{default:"Take the ramp on the right",name:"Take the ramp on the right onto {way_name}",destination:"Take the ramp on the right towards {destination}"},"sharp left":{default:"Take the ramp on the left",name:"Take the ramp on the left onto {way_name}",destination:"Take the ramp on the left towards {destination}"},"sharp right":{default:"Take the ramp on the right",name:"Take the ramp on the right onto {way_name}",destination:"Take the ramp on the right towards {destination}"},"slight left":{default:"Take the ramp on the left",name:"Take the ramp on the left onto {way_name}",destination:"Take the ramp on the left towards {destination}"},"slight right":{default:"Take the ramp on the right",name:"Take the ramp on the right onto {way_name}",destination:"Take the ramp on the right towards {destination}"}},rotary:{default:{default:{default:"Enter the traffic circle",name:"Enter the traffic circle and exit onto {way_name}",destination:"Enter the traffic circle and exit towards {destination}"},name:{default:"Enter {rotary_name}",name:"Enter {rotary_name} and exit onto {way_name}",destination:"Enter {rotary_name} and exit towards {destination}"},exit:{default:"Enter the traffic circle and take the {exit_number} exit",name:"Enter the traffic circle and take the {exit_number} exit onto {way_name}",destination:"Enter the traffic circle and take the {exit_number} exit towards {destination}"},name_exit:{default:"Enter {rotary_name} and take the {exit_number} exit",name:"Enter {rotary_name} and take the {exit_number} exit onto {way_name}",destination:"Enter {rotary_name} and take the {exit_number} exit towards {destination}"}}},roundabout:{default:{exit:{default:"Enter the traffic circle and take the {exit_number} exit",name:"Enter the traffic circle and take the {exit_number} exit onto {way_name}",destination:"Enter the traffic circle and take the {exit_number} exit towards {destination}"},default:{default:"Enter the traffic circle",name:"Enter the traffic circle and exit onto {way_name}",destination:"Enter the traffic circle and exit towards {destination}"}}},"roundabout turn":{default:{default:"Make a {modifier}",name:"Make a {modifier} onto {way_name}",destination:"Make a {modifier} towards {destination}"},left:{default:"Turn left",name:"Turn left onto {way_name}",destination:"Turn left towards {destination}"},right:{default:"Turn right",name:"Turn right onto {way_name}",destination:"Turn right towards {destination}"},straight:{default:"Continue straight",name:"Continue straight onto {way_name}",destination:"Continue straight towards {destination}"}},"exit roundabout":{default:{default:"Exit the traffic circle",name:"Exit the traffic circle onto {way_name}",destination:"Exit the traffic circle towards {destination}"}},"exit rotary":{default:{default:"Exit the traffic circle",name:"Exit the traffic circle onto {way_name}",destination:"Exit the traffic circle towards {destination}"}},turn:{default:{default:"Make a {modifier}",name:"Make a {modifier} onto {way_name}",destination:"Make a {modifier} towards {destination}"},left:{default:"Turn left",name:"Turn left onto {way_name}",destination:"Turn left towards {destination}"},right:{default:"Turn right",name:"Turn right onto {way_name}",destination:"Turn right towards {destination}"},straight:{default:"Go straight",name:"Go straight onto {way_name}",destination:"Go straight towards {destination}"}},"use lane":{no_lanes:{default:"Continue straight"},default:{default:"{lane_instruction}"}}}}},{}],26:[function(_dereq_,module,exports){module.exports={meta:{capitalizeFirstLetter:true},v5:{constants:{ordinalize:{1:"1.",2:"2.",3:"3.",4:"4.",5:"5.",6:"6.",7:"7.",8:"8.",9:"9.",10:"10."},direction:{north:"norden",northeast:"nord-orienten",east:"orienten",southeast:"sud-orienten",south:"suden",southwest:"sud-okcidenten",west:"okcidenten",northwest:"nord-okcidenten"},modifier:{left:"maldekstren",right:"dekstren","sharp left":"maldekstregen","sharp right":"dekstregen","slight left":"maldekstreten","slight right":"dekstreten",straight:"rekten",uturn:"turniĝu malantaŭen"},lanes:{xo:"Veturu dekstre",ox:"Veturu maldekstre",xox:"Veturu meze",oxo:"Veturu dekstre aŭ maldekstre"}},modes:{ferry:{default:"Enpramiĝu",name:"Enpramiĝu {way_name}",destination:"Enpramiĝu direkte al {destination}"}},phrase:{"two linked by distance":"{instruction_one} kaj post {distance} {instruction_two}","two linked":"{instruction_one} kaj sekve {instruction_two}","one in distance":"Post {distance}, {instruction_one}","name and ref":"{name} ({ref})","exit with number":"elveturejo {exit}"},arrive:{default:{default:"Vi atingis vian {nth} celon",upcoming:"Vi atingos vian {nth} celon",short:"Vi atingis","short-upcoming":"Vi atingos",named:"Vi atingis {waypoint_name}"},left:{default:"Vi atingis vian {nth} celon ĉe maldekstre",upcoming:"Vi atingos vian {nth} celon ĉe maldekstre",short:"Vi atingis","short-upcoming":"Vi atingos",named:"Vi atingis {waypoint_name}, ĉe maldekstre"},right:{default:"Vi atingis vian {nth} celon ĉe dekstre",upcoming:"Vi atingos vian {nth} celon ĉe dekstre",short:"Vi atingis","short-upcoming":"Vi atingos",named:"Vi atingis {waypoint_name}, ĉe dekstre"},"sharp left":{default:"Vi atingis vian {nth} celon ĉe maldekstre",upcoming:"Vi atingos vian {nth} celon ĉe maldekstre",short:"Vi atingis","short-upcoming":"Vi atingos",named:"Vi atingis {waypoint_name}, ĉe maldekstre"},"sharp right":{default:"Vi atingis vian {nth} celon ĉe dekstre",upcoming:"Vi atingos vian {nth} celon ĉe dekstre",short:"Vi atingis","short-upcoming":"Vi atingos",named:"Vi atingis {waypoint_name}, ĉe dekstre"},"slight right":{default:"Vi atingis vian {nth} celon ĉe dekstre",upcoming:"Vi atingos vian {nth} celon ĉe dekstre",short:"Vi atingis","short-upcoming":"Vi atingos",named:"Vi atingis {waypoint_name}, ĉe dekstre"},"slight left":{default:"Vi atingis vian {nth} celon ĉe maldekstre",upcoming:"Vi atingos vian {nth} celon ĉe maldekstre",short:"Vi atingis","short-upcoming":"Vi atingos",named:"Vi atingis {waypoint_name}, ĉe maldekstre"},straight:{default:"Vi atingis vian {nth} celon",upcoming:"Vi atingos vian {nth} celon rekte",short:"Vi atingis","short-upcoming":"Vi atingos",named:"Vi atingis {waypoint_name} antaŭe"}},continue:{default:{default:"Veturu {modifier}",name:"Veturu {modifier} al {way_name}",destination:"Veturu {modifier} direkte al {destination}",exit:"Veturu {modifier} direkte al {way_name}"},straight:{default:"Veturu rekten",name:"Veturu rekten al {way_name}",destination:"Veturu rekten direkte al {destination}",distance:"Veturu rekten dum {distance}",namedistance:"Veturu rekten al {way_name} dum {distance}"},"sharp left":{default:"Turniĝu ege maldekstren",name:"Turniĝu ege maldekstren al {way_name}",destination:"Turniĝu ege maldekstren direkte al {destination}"},"sharp right":{default:"Turniĝu ege dekstren",name:"Turniĝu ege dekstren al {way_name}",destination:"Turniĝu ege dekstren direkte al {destination}"},"slight left":{default:"Turniĝu ete maldekstren",name:"Turniĝu ete maldekstren al {way_name}",destination:"Turniĝu ete maldekstren direkte al {destination}"},"slight right":{default:"Turniĝu ete dekstren",name:"Turniĝu ete dekstren al {way_name}",destination:"Turniĝu ete dekstren direkte al {destination}"},uturn:{default:"Turniĝu malantaŭen",name:"Turniĝu malantaŭen al {way_name}",destination:"Turniĝu malantaŭen direkte al {destination}"}},depart:{default:{default:"Direktiĝu {direction}",name:"Direktiĝu {direction} al {way_name}",namedistance:"Direktiĝu {direction} al {way_name} tra {distance}"}},"end of road":{default:{default:"Veturu {modifier}",name:"Veturu {modifier} direkte al {way_name}",destination:"Veturu {modifier} direkte al {destination}"},straight:{default:"Veturu rekten",name:"Veturu rekten al {way_name}",destination:"Veturu rekten direkte al {destination}"},uturn:{default:"Turniĝu malantaŭen ĉe fino de la vojo",name:"Turniĝu malantaŭen al {way_name} ĉe fino de la vojo",destination:"Turniĝu malantaŭen direkte al {destination} ĉe fino de la vojo"}},fork:{default:{default:"Daŭru {modifier} ĉe la vojforko",name:"Pluu {modifier} al {way_name}",destination:"Pluu {modifier} direkte al {destination}"},"slight left":{default:"Maldekstren ĉe la vojforko",name:"Pluu maldekstren al {way_name}",destination:"Pluu maldekstren direkte al {destination}"},"slight right":{default:"Dekstren ĉe la vojforko",name:"Pluu dekstren al {way_name}",destination:"Pluu dekstren direkte al {destination}"},"sharp left":{default:"Ege maldekstren ĉe la vojforko",name:"Turniĝu ege maldekstren al {way_name}",destination:"Turniĝu ege maldekstren direkte al {destination}"},"sharp right":{default:"Ege dekstren ĉe la vojforko",name:"Turniĝu ege dekstren al {way_name}",destination:"Turniĝu ege dekstren direkte al {destination}"},uturn:{default:"Turniĝu malantaŭen",name:"Turniĝu malantaŭen al {way_name}",destination:"Turniĝu malantaŭen direkte al {destination}"}},merge:{default:{default:"Enveturu {modifier}",name:"Enveturu {modifier} al {way_name}",destination:"Enveturu {modifier} direkte al {destination}"},straight:{default:"Enveturu",name:"Enveturu al {way_name}",destination:"Enveturu direkte al {destination}"},"slight left":{default:"Enveturu de maldekstre",name:"Enveturu de maldekstre al {way_name}",destination:"Enveturu de maldekstre direkte al {destination}"},"slight right":{default:"Enveturu de dekstre",name:"Enveturu de dekstre al {way_name}",destination:"Enveturu de dekstre direkte al {destination}"},"sharp left":{default:"Enveturu de maldekstre",name:"Enveture de maldekstre al {way_name}",destination:"Enveturu de maldekstre direkte al {destination}"},"sharp right":{default:"Enveturu de dekstre",name:"Enveturu de dekstre al {way_name}",destination:"Enveturu de dekstre direkte al {destination}"},uturn:{default:"Turniĝu malantaŭen",name:"Turniĝu malantaŭen al {way_name}",destination:"Turniĝu malantaŭen direkte al {destination}"}},"new name":{default:{default:"Pluu {modifier}",name:"Pluu {modifier} al {way_name}",destination:"Pluu {modifier} direkte al {destination}"},straight:{default:"Veturu rekten",name:"Veturu rekten al {way_name}",destination:"Veturu rekten direkte al {destination}"},"sharp left":{default:"Turniĝu ege maldekstren",name:"Turniĝu ege maldekstren al {way_name}",destination:"Turniĝu ege maldekstren direkte al {destination}"},"sharp right":{default:"Turniĝu ege dekstren",name:"Turniĝu ege dekstren al {way_name}",destination:"Turniĝu ege dekstren direkte al {destination}"},"slight left":{default:"Pluu ete maldekstren",name:"Pluu ete maldekstren al {way_name}",destination:"Pluu ete maldekstren direkte al {destination}"},"slight right":{default:"Pluu ete dekstren",name:"Pluu ete dekstren al {way_name}",destination:"Pluu ete dekstren direkte al {destination}"},uturn:{default:"Turniĝu malantaŭen",name:"Turniĝu malantaŭen al {way_name}",destination:"Turniĝu malantaŭen direkte al {destination}"}},notification:{default:{default:"Pluu {modifier}",name:"Pluu {modifier} al {way_name}",destination:"Pluu {modifier} direkte al {destination}"},uturn:{default:"Turniĝu malantaŭen",name:"Turniĝu malantaŭen al {way_name}",destination:"Turniĝu malantaŭen direkte al {destination}"}},"off ramp":{default:{default:"Direktiĝu al enveturejo",name:"Direktiĝu al enveturejo al {way_name}",destination:"Direktiĝu al enveturejo direkte al {destination}",exit:"Direktiĝu al elveturejo {exit}",exit_destination:"Direktiĝu al elveturejo {exit} direkte al {destination}"},left:{default:"Direktiĝu al enveturejo ĉe maldekstre",name:"Direktiĝu al enveturejo ĉe maldekstre al {way_name}",destination:"Direktiĝu al enveturejo ĉe maldekstre al {destination}",exit:"Direktiĝu al elveturejo {exit} ĉe maldekstre",exit_destination:"Direktiĝu al elveturejo {exit} ĉe maldekstre direkte al {destination}"},right:{default:"Direktiĝu al enveturejo ĉe dekstre",name:"Direktiĝu al enveturejo ĉe dekstre al {way_name}",destination:"Direktiĝu al enveturejo ĉe dekstre al {destination}",exit:"Direktiĝu al {exit} elveturejo ĉe ldekstre",exit_destination:"Direktiĝu al elveturejo {exit} ĉe dekstre direkte al {destination}"},"sharp left":{default:"Direktiĝu al enveturejo ĉe maldekstre",name:"Direktiĝu al enveturejo ĉe maldekstre al {way_name}",destination:"Direktiĝu al enveturejo ĉe maldekstre al {destination}",exit:"Direktiĝu al {exit} elveturejo ĉe maldekstre",exit_destination:"Direktiĝu al elveturejo {exit} ĉe maldekstre direkte al {destination}"},"sharp right":{default:"Direktiĝu al enveturejo ĉe dekstre",name:"Direktiĝu al enveturejo ĉe dekstre al {way_name}",destination:"Direktiĝu al enveturejo ĉe dekstre al {destination}",exit:"Direktiĝu al elveturejo {exit} ĉe dekstre",exit_destination:"Direktiĝu al elveturejo {exit} ĉe dekstre direkte al {destination}"},"slight left":{default:"Direktiĝu al enveturejo ĉe maldekstre",name:"Direktiĝu al enveturejo ĉe maldekstre al {way_name}",destination:"Direktiĝu al enveturejo ĉe maldekstre al {destination}",exit:"Direktiĝu al {exit} elveturejo ĉe maldekstre",exit_destination:"Direktiĝu al elveturejo {exit} ĉe maldekstre direkte al {destination}"},"slight right":{default:"Direktiĝu al enveturejo ĉe dekstre",name:"Direktiĝu al enveturejo ĉe dekstre al {way_name}",destination:"Direktiĝu al enveturejo ĉe dekstre al {destination}",exit:"Direktiĝu al {exit} elveturejo ĉe ldekstre",exit_destination:"Direktiĝu al elveturejo {exit} ĉe dekstre direkte al {destination}"}},"on ramp":{default:{default:"Direktiĝu al enveturejo",name:"Direktiĝu al enveturejo al {way_name}",destination:"Direktiĝu al enveturejo direkte al {destination}"},left:{default:"Direktiĝu al enveturejo ĉe maldekstre",name:"Direktiĝu al enveturejo ĉe maldekstre al {way_name}",destination:"Direktiĝu al enveturejo ĉe maldekstre al {destination}"},right:{default:"Direktiĝu al enveturejo ĉe dekstre",name:"Direktiĝu al enveturejo ĉe dekstre al {way_name}",destination:"Direktiĝu al enveturejo ĉe dekstre al {destination}"},"sharp left":{default:"Direktiĝu al enveturejo ĉe maldekstre",name:"Direktiĝu al enveturejo ĉe maldekstre al {way_name}",destination:"Direktiĝu al enveturejo ĉe maldekstre al {destination}"},"sharp right":{default:"Direktiĝu al enveturejo ĉe dekstre",name:"Direktiĝu al enveturejo ĉe dekstre al {way_name}",destination:"Direktiĝu al enveturejo ĉe dekstre al {destination}"},"slight left":{default:"Direktiĝu al enveturejo ĉe maldekstre",name:"Direktiĝu al enveturejo ĉe maldekstre al {way_name}",destination:"Direktiĝu al enveturejo ĉe maldekstre al {destination}"},"slight right":{default:"Direktiĝu al enveturejo ĉe dekstre",name:"Direktiĝu al enveturejo ĉe dekstre al {way_name}",destination:"Direktiĝu al enveturejo ĉe dekstre al {destination}"}},rotary:{default:{default:{default:"Enveturu trafikcirklegon",name:"Enveturu trafikcirklegon kaj elveturu al {way_name}",destination:"Enveturu trafikcirklegon kaj elveturu direkte al {destination}"},name:{default:"Enveturu {rotary_name}",name:"Enveturu {rotary_name} kaj elveturu al {way_name}",destination:"Enveturu {rotary_name} kaj elveturu direkte al {destination}"},exit:{default:"Enveturu trafikcirklegon kaj sekve al {exit_number} elveturejo",name:"Enveturu trafikcirklegon kaj sekve al {exit_number} elveturejo al {way_name}",destination:"Enveturu trafikcirklegon kaj sekve al {exit_number} elveturejo direkte al {destination}"},name_exit:{default:"Enveturu {rotary_name} kaj sekve al {exit_number} elveturejo",name:"Enveturu {rotary_name} kaj sekve al {exit_number} elveturejo al {way_name}",destination:"Enveturu {rotary_name} kaj sekve al {exit_number} elveturejo direkte al {destination}"}}},roundabout:{default:{exit:{default:"Enveturu trafikcirklegon kaj sekve al {exit_number} elveturejo",name:"Enveturu trafikcirklegon kaj sekve al {exit_number} elveturejo al {way_name}",destination:"Enveturu trafikcirklegon kaj sekve al {exit_number} elveturejo direkte al {destination}"},default:{default:"Enveturu trafikcirklegon",name:"Enveturu trafikcirklegon kaj elveturu al {way_name}",destination:"Enveturu trafikcirklegon kaj elveturu direkte al {destination}"}}},"roundabout turn":{default:{default:"Veturu {modifier}",name:"Veturu {modifier} al {way_name}",destination:"Veturu {modifier} direkte al {destination}"},left:{default:"Turniĝu maldekstren",name:"Turniĝu maldekstren al {way_name}",destination:"Turniĝu maldekstren direkte al {destination}"},right:{default:"Turniĝu dekstren",name:"Turniĝu dekstren al {way_name}",destination:"Turniĝu dekstren direkte al {destination}"},straight:{default:"Pluu rekten",name:"Veturu rekten al {way_name}",destination:"Veturu rekten direkte al {destination}"}},"exit roundabout":{default:{default:"Elveturu trafikcirklegon",name:"Elveturu trafikcirklegon al {way_name}",destination:"Elveturu trafikcirklegon direkte al {destination}"}},"exit rotary":{default:{default:"Eliru trafikcirklegon",name:"Elveturu trafikcirklegon al {way_name}",destination:"Elveturu trafikcirklegon direkte al {destination}"}},turn:{default:{default:"Veturu {modifier}",name:"Veturu {modifier} al {way_name}",destination:"Veturu {modifier} direkte al {destination}"},left:{default:"Turniĝu maldekstren",name:"Turniĝu maldekstren al {way_name}",destination:"Turniĝu maldekstren direkte al {destination}"},right:{default:"Turniĝu dekstren",name:"Turniĝu dekstren al {way_name}",destination:"Turniĝu dekstren direkte al {destination}"},straight:{default:"Veturu rekten",name:"Veturu rekten al {way_name}",destination:"Veturu rekten direkte al {destination}"}},"use lane":{no_lanes:{default:"Pluu rekten"},default:{default:"{lane_instruction}"}}}}},{}],27:[function(_dereq_,module,exports){module.exports={meta:{capitalizeFirstLetter:true},v5:{constants:{ordinalize:{1:"1ª",2:"2ª",3:"3ª",4:"4ª",5:"5ª",6:"6ª",7:"7ª",8:"8ª",9:"9ª",10:"10ª"},direction:{north:"norte",northeast:"noreste",east:"este",southeast:"sureste",south:"sur",southwest:"suroeste",west:"oeste",northwest:"noroeste"},modifier:{left:"a la izquierda",right:"a la derecha","sharp left":"cerrada a la izquierda","sharp right":"cerrada a la derecha","slight left":"ligeramente a la izquierda","slight right":"ligeramente a la derecha",straight:"recto",uturn:"cambio de sentido"},lanes:{xo:"Mantente a la derecha",ox:"Mantente a la izquierda",xox:"Mantente en el medio",oxo:"Mantente a la izquierda o a la derecha"}},modes:{ferry:{default:"Coge el ferry",name:"Coge el ferry {way_name}",destination:"Coge el ferry hacia {destination}"}},phrase:{"two linked by distance":"{instruction_one} y luego en {distance}, {instruction_two}","two linked":"{instruction_one} y luego {instruction_two}","one in distance":"A {distance}, {instruction_one}","name and ref":"{name} ({ref})","exit with number":"salida {exit}"},arrive:{default:{default:"Has llegado a tu {nth} destino",upcoming:"Vas a llegar a tu {nth} destino",short:"Has llegado","short-upcoming":"Vas a llegar",named:"Has llegado a {waypoint_name}"},left:{default:"Has llegado a tu {nth} destino, a la izquierda",upcoming:"Vas a llegar a tu {nth} destino, a la izquierda",short:"Has llegado","short-upcoming":"Vas a llegar",named:"Has llegado a {waypoint_name}, a la izquierda"},right:{default:"Has llegado a tu {nth} destino, a la derecha",upcoming:"Vas a llegar a tu {nth} destino, a la derecha",short:"Has llegado","short-upcoming":"Vas a llegar",named:"Has llegado a {waypoint_name}, a la derecha"},"sharp left":{default:"Has llegado a tu {nth} destino, a la izquierda",upcoming:"Vas a llegar a tu {nth} destino, a la izquierda",short:"Has llegado","short-upcoming":"Vas a llegar",named:"Has llegado a {waypoint_name}, a la izquierda"},"sharp right":{default:"Has llegado a tu {nth} destino, a la derecha",upcoming:"Vas a llegar a tu {nth} destino, a la derecha",short:"Has llegado","short-upcoming":"Vas a llegar",named:"Has llegado a {waypoint_name}, a la derecha"},"slight right":{default:"Has llegado a tu {nth} destino, a la derecha",upcoming:"Vas a llegar a tu {nth} destino, a la derecha",short:"Has llegado","short-upcoming":"Vas a llegar",named:"Has llegado a {waypoint_name}, a la derecha"},"slight left":{default:"Has llegado a tu {nth} destino, a la izquierda",upcoming:"Vas a llegar a tu {nth} destino, a la izquierda",short:"Has llegado","short-upcoming":"Vas a llegar",named:"Has llegado a {waypoint_name}, a la izquierda"},straight:{default:"Has llegado a tu {nth} destino, en frente",upcoming:"Vas a llegar a tu {nth} destino, en frente",short:"Has llegado","short-upcoming":"Vas a llegar",named:"Has llegado a {waypoint_name}, en frente"}},continue:{default:{default:"Gire {modifier}",name:"Cruce {modifier} en {way_name}",destination:"Gire {modifier} hacia {destination}",exit:"Gire {modifier} en {way_name}"},straight:{default:"Continúa recto",name:"Continúa en {way_name}",destination:"Continúa hacia {destination}",distance:"Continúa recto por {distance}",namedistance:"Continúa recto en {way_name} por {distance}"},"sharp left":{default:"Gire a la izquierda",name:"Gire a la izquierda en {way_name}",destination:"Gire a la izquierda hacia {destination}"},"sharp right":{default:"Gire a la derecha",name:"Gire a la derecha en {way_name}",destination:"Gire a la derecha hacia {destination}"},"slight left":{default:"Gire a la izquierda",name:"Doble levemente a la izquierda en {way_name}",destination:"Gire a la izquierda hacia {destination}"},"slight right":{default:"Gire a la izquierda",name:"Doble levemente a la derecha en {way_name}",destination:"Gire a la izquierda hacia {destination}"},uturn:{default:"Haz un cambio de sentido",name:"Haz un cambio de sentido y continúa en {way_name}",destination:"Haz un cambio de sentido hacia {destination}"}},depart:{default:{default:"Dirígete al {direction}",name:"Dirígete al {direction} por {way_name}",namedistance:"Dirígete al {direction} en {way_name} por {distance}"}},"end of road":{default:{default:"Al final de la calle gira {modifier}",name:"Al final de la calle gira {modifier} por {way_name}",destination:"Al final de la calle gira {modifier} hacia {destination}"},straight:{default:"Al final de la calle continúa recto",name:"Al final de la calle continúa recto por {way_name}",destination:"Al final de la calle continúa recto hacia {destination}"},uturn:{default:"Al final de la calle haz un cambio de sentido",name:"Al final de la calle haz un cambio de sentido en {way_name}",destination:"Al final de la calle haz un cambio de sentido hacia {destination}"}},fork:{default:{default:"Mantente {modifier} en el cruce",name:"Mantente {modifier} por {way_name}",destination:"Mantente {modifier} hacia {destination}"},"slight left":{default:"Mantente a la izquierda en el cruce",name:"Mantente a la izquierda por {way_name}",destination:"Mantente a la izquierda hacia {destination}"},"slight right":{default:"Mantente a la derecha en el cruce",name:"Mantente a la derecha por {way_name}",destination:"Mantente a la derecha hacia {destination}"},"sharp left":{default:"Gira la izquierda en el cruce",name:"Gira a la izquierda por {way_name}",destination:"Gira a la izquierda hacia {destination}"},"sharp right":{default:"Gira a la derecha en el cruce",name:"Gira a la derecha por {way_name}",destination:"Gira a la derecha hacia {destination}"},uturn:{default:"Haz un cambio de sentido",name:"Haz un cambio de sentido en {way_name}",destination:"Haz un cambio de sentido hacia {destination}"}},merge:{default:{default:"Incorpórate {modifier}",name:"Incorpórate {modifier} por {way_name}",destination:"Incorpórate {modifier} hacia {destination}"},straight:{default:"Incorpórate",name:"Incorpórate por {way_name}",destination:"Incorpórate hacia {destination}"},"slight left":{default:"Incorpórate a la izquierda",name:"Incorpórate a la izquierda por {way_name}",destination:"Incorpórate a la izquierda hacia {destination}"},"slight right":{default:"Incorpórate a la derecha",name:"Incorpórate a la derecha por {way_name}",destination:"Incorpórate a la derecha hacia {destination}"},"sharp left":{default:"Incorpórate a la izquierda",name:"Incorpórate a la izquierda por {way_name}",destination:"Incorpórate a la izquierda hacia {destination}"},"sharp right":{default:"Incorpórate a la derecha",name:"Incorpórate a la derecha por {way_name}",destination:"Incorpórate a la derecha hacia {destination}"},uturn:{default:"Haz un cambio de sentido",name:"Haz un cambio de sentido en {way_name}",destination:"Haz un cambio de sentido hacia {destination}"}},"new name":{default:{default:"Continúa {modifier}",name:"Continúa {modifier} por {way_name}",destination:"Continúa {modifier} hacia {destination}"},straight:{default:"Continúa recto",name:"Continúa por {way_name}",destination:"Continúa hacia {destination}"},"sharp left":{default:"Gira a la izquierda",name:"Gira a la izquierda por {way_name}",destination:"Gira a la izquierda hacia {destination}"},"sharp right":{default:"Gira a la derecha",name:"Gira a la derecha por {way_name}",destination:"Gira a la derecha hacia {destination}"},"slight left":{default:"Continúa ligeramente por la izquierda",name:"Continúa ligeramente por la izquierda por {way_name}",destination:"Continúa ligeramente por la izquierda hacia {destination}"},"slight right":{default:"Continúa ligeramente por la derecha",name:"Continúa ligeramente por la derecha por {way_name}",destination:"Continúa ligeramente por la derecha hacia {destination}"},uturn:{default:"Haz un cambio de sentido",name:"Haz un cambio de sentido en {way_name}",destination:"Haz un cambio de sentido hacia {destination}"}},notification:{default:{default:"Continúa {modifier}",name:"Continúa {modifier} por {way_name}",destination:"Continúa {modifier} hacia {destination}"},uturn:{default:"Haz un cambio de sentido",name:"Haz un cambio de sentido en {way_name}",destination:"Haz un cambio de sentido hacia {destination}"}},"off ramp":{default:{default:"Coge la cuesta abajo",name:"Coge la cuesta abajo por {way_name}",destination:"Coge la cuesta abajo hacia {destination}",exit:"Coge la cuesta abajo {exit}",exit_destination:"Coge la cuesta abajo {exit} hacia {destination}"},left:{default:"Coge la cuesta abajo de la izquierda",name:"Coge la cuesta abajo de la izquierda por {way_name}",destination:"Coge la cuesta abajo de la izquierda hacia {destination}",exit:"Coge la cuesta abajo {exit} a tu izquierda",exit_destination:"Coge la cuesta abajo {exit} a tu izquierda hacia {destination}"},right:{default:"Coge la cuesta abajo de la derecha",name:"Coge la cuesta abajo de la derecha por {way_name}",destination:"Coge la cuesta abajo de la derecha hacia {destination}",exit:"Coge la cuesta abajo {exit}",exit_destination:"Coge la cuesta abajo {exit} hacia {destination}"},"sharp left":{default:"Coge la cuesta abajo de la izquierda",name:"Coge la cuesta abajo de la izquierda por {way_name}",destination:"Coge la cuesta abajo de la izquierda hacia {destination}",exit:"Coge la cuesta abajo {exit} a tu izquierda",exit_destination:"Coge la cuesta abajo {exit} a tu izquierda hacia {destination}"},"sharp right":{default:"Coge la cuesta abajo de la derecha",name:"Coge la cuesta abajo de la derecha por {way_name}",destination:"Coge la cuesta abajo de la derecha hacia {destination}",exit:"Coge la cuesta abajo {exit}",exit_destination:"Coge la cuesta abajo {exit} hacia {destination}"},"slight left":{default:"Coge la cuesta abajo de la izquierda",name:"Coge la cuesta abajo de la izquierda por {way_name}",destination:"Coge la cuesta abajo de la izquierda hacia {destination}",exit:"Coge la cuesta abajo {exit} a tu izquierda",exit_destination:"Coge la cuesta abajo {exit} a tu izquierda hacia {destination}"},"slight right":{default:"Coge la cuesta abajo de la derecha",name:"Coge la cuesta abajo de la derecha por {way_name}",destination:"Coge la cuesta abajo de la derecha hacia {destination}",exit:"Coge la cuesta abajo {exit}",exit_destination:"Coge la cuesta abajo {exit} hacia {destination}"}},"on ramp":{default:{default:"Coge la cuesta",name:"Coge la cuesta por {way_name}",destination:"Coge la cuesta hacia {destination}"},left:{default:"Coge la cuesta de la izquierda",name:"Coge la cuesta de la izquierda por {way_name}",destination:"Coge la cuesta de la izquierda hacia {destination}"},right:{default:"Coge la cuesta de la derecha",name:"Coge la cuesta de la derecha por {way_name}",destination:"Coge la cuesta de la derecha hacia {destination}"},"sharp left":{default:"Coge la cuesta de la izquierda",name:"Coge la cuesta de la izquierda por {way_name}",destination:"Coge la cuesta de la izquierda hacia {destination}"},"sharp right":{default:"Coge la cuesta de la derecha",name:"Coge la cuesta de la derecha por {way_name}",destination:"Coge la cuesta de la derecha hacia {destination}"},"slight left":{default:"Coge la cuesta de la izquierda",name:"Coge la cuesta de la izquierda por {way_name}",destination:"Coge la cuesta de la izquierda hacia {destination}"},"slight right":{default:"Coge la cuesta de la derecha",name:"Coge la cuesta de la derecha por {way_name}",destination:"Coge la cuesta de la derecha hacia {destination}"}},rotary:{default:{default:{default:"Incorpórate en la rotonda",name:"En la rotonda sal por {way_name}",destination:"En la rotonda sal hacia {destination}"},name:{default:"En {rotary_name}",name:"En {rotary_name} sal por {way_name}",destination:"En {rotary_name} sal hacia {destination}"},exit:{default:"En la rotonda toma la {exit_number} salida",
name:"En la rotonda toma la {exit_number} salida por {way_name}",destination:"En la rotonda toma la {exit_number} salida hacia {destination}"},name_exit:{default:"En {rotary_name} toma la {exit_number} salida",name:"En {rotary_name} toma la {exit_number} salida por {way_name}",destination:"En {rotary_name} toma la {exit_number} salida hacia {destination}"}}},roundabout:{default:{exit:{default:"En la rotonda toma la {exit_number} salida",name:"En la rotonda toma la {exit_number} salida por {way_name}",destination:"En la rotonda toma la {exit_number} salida hacia {destination}"},default:{default:"Incorpórate en la rotonda",name:"Incorpórate en la rotonda y sal en {way_name}",destination:"Incorpórate en la rotonda y sal hacia {destination}"}}},"roundabout turn":{default:{default:"Siga {modifier}",name:"Siga {modifier} en {way_name}",destination:"Siga {modifier} hacia {destination}"},left:{default:"Gire a la izquierda",name:"Gire a la izquierda en {way_name}",destination:"Gire a la izquierda hacia {destination}"},right:{default:"Gire a la derecha",name:"Gire a la derecha en {way_name}",destination:"Gire a la derecha hacia {destination}"},straight:{default:"Continúa recto",name:"Continúa recto por {way_name}",destination:"Continúa recto hacia {destination}"}},"exit roundabout":{default:{default:"Sal la rotonda",name:"Toma la salida por {way_name}",destination:"Toma la salida hacia {destination}"}},"exit rotary":{default:{default:"Sal la rotonda",name:"Toma la salida por {way_name}",destination:"Toma la salida hacia {destination}"}},turn:{default:{default:"Gira {modifier}",name:"Gira {modifier} por {way_name}",destination:"Gira {modifier} hacia {destination}"},left:{default:"Gira a la izquierda",name:"Gira a la izquierda por {way_name}",destination:"Gira a la izquierda hacia {destination}"},right:{default:"Gira a la derecha",name:"Gira a la derecha por {way_name}",destination:"Gira a la derecha hacia {destination}"},straight:{default:"Continúa recto",name:"Continúa recto por {way_name}",destination:"Continúa recto hacia {destination}"}},"use lane":{no_lanes:{default:"Continúa recto"},default:{default:"{lane_instruction}"}}}}},{}],28:[function(_dereq_,module,exports){module.exports={meta:{capitalizeFirstLetter:true},v5:{constants:{ordinalize:{1:"1ª",2:"2ª",3:"3ª",4:"4ª",5:"5ª",6:"6ª",7:"7ª",8:"8ª",9:"9ª",10:"10ª"},direction:{north:"norte",northeast:"noreste",east:"este",southeast:"sureste",south:"sur",southwest:"suroeste",west:"oeste",northwest:"noroeste"},modifier:{left:"izquierda",right:"derecha","sharp left":"cerrada a la izquierda","sharp right":"cerrada a la derecha","slight left":"levemente a la izquierda","slight right":"levemente a la derecha",straight:"recto",uturn:"cambio de sentido"},lanes:{xo:"Mantente a la derecha",ox:"Mantente a la izquierda",xox:"Mantente en el medio",oxo:"Mantente a la izquierda o derecha"}},modes:{ferry:{default:"Coge el ferry",name:"Coge el ferry {way_name}",destination:"Coge el ferry a {destination}"}},phrase:{"two linked by distance":"{instruction_one} y luego a {distance}, {instruction_two}","two linked":"{instruction_one} y luego {instruction_two}","one in distance":"A {distance}, {instruction_one}","name and ref":"{name} ({ref})","exit with number":"salida {exit}"},arrive:{default:{default:"Has llegado a tu {nth} destino",upcoming:"Vas a llegar a tu {nth} destino",short:"Has llegado","short-upcoming":"Vas a llegar",named:"Has llegado a {waypoint_name}"},left:{default:"Has llegado a tu {nth} destino, a la izquierda",upcoming:"Vas a llegar a tu {nth} destino, a la izquierda",short:"Has llegado","short-upcoming":"Vas a llegar",named:"Has llegado a {waypoint_name}, a la izquierda"},right:{default:"Has llegado a tu {nth} destino, a la derecha",upcoming:"Vas a llegar a tu {nth} destino, a la derecha",short:"Has llegado","short-upcoming":"Vas a llegar",named:"Has llegado a {waypoint_name}, a la derecha"},"sharp left":{default:"Has llegado a tu {nth} destino, a la izquierda",upcoming:"Vas a llegar a tu {nth} destino, a la izquierda",short:"Has llegado","short-upcoming":"Vas a llegar",named:"Has llegado a {waypoint_name}, a la izquierda"},"sharp right":{default:"Has llegado a tu {nth} destino, a la derecha",upcoming:"Vas a llegar a tu {nth} destino, a la derecha",short:"Has llegado","short-upcoming":"Vas a llegar",named:"Has llegado a {waypoint_name}, a la derecha"},"slight right":{default:"Has llegado a tu {nth} destino, a la derecha",upcoming:"Vas a llegar a tu {nth} destino, a la derecha",short:"Has llegado","short-upcoming":"Vas a llegar",named:"Has llegado a {waypoint_name}, a la derecha"},"slight left":{default:"Has llegado a tu {nth} destino, a la izquierda",upcoming:"Vas a llegar a tu {nth} destino, a la izquierda",short:"Has llegado","short-upcoming":"Vas a llegar",named:"Has llegado a {waypoint_name}, a la izquierda"},straight:{default:"Has llegado a tu {nth} destino, en frente",upcoming:"Vas a llegar a tu {nth} destino, en frente",short:"Has llegado","short-upcoming":"Vas a llegar",named:"Has llegado a {waypoint_name}, en frente"}},continue:{default:{default:"Gira a {modifier}",name:"Cruza a la{modifier}  en {way_name}",destination:"Gira a {modifier} hacia {destination}",exit:"Gira a {modifier} en {way_name}"},straight:{default:"Continúa recto",name:"Continúa en {way_name}",destination:"Continúa hacia {destination}",distance:"Continúa recto por {distance}",namedistance:"Continúa recto en {way_name} por {distance}"},"sharp left":{default:"Gira a la izquierda",name:"Gira a la izquierda en {way_name}",destination:"Gira a la izquierda hacia {destination}"},"sharp right":{default:"Gira a la derecha",name:"Gira a la derecha en {way_name}",destination:"Gira a la derecha hacia {destination}"},"slight left":{default:"Gira a la izquierda",name:"Dobla levemente a la izquierda en {way_name}",destination:"Gira a la izquierda hacia {destination}"},"slight right":{default:"Gira a la izquierda",name:"Dobla levemente a la derecha en {way_name}",destination:"Gira a la izquierda hacia {destination}"},uturn:{default:"Haz un cambio de sentido",name:"Haz un cambio de sentido y continúa en {way_name}",destination:"Haz un cambio de sentido hacia {destination}"}},depart:{default:{default:"Ve a {direction}",name:"Ve a {direction} en {way_name}",namedistance:"Ve a {direction} en {way_name} por {distance}"}},"end of road":{default:{default:"Gira  a {modifier}",name:"Gira a {modifier} en {way_name}",destination:"Gira a {modifier} hacia {destination}"},straight:{default:"Continúa recto",name:"Continúa recto en {way_name}",destination:"Continúa recto hacia {destination}"},uturn:{default:"Haz un cambio de sentido al final de la via",name:"Haz un cambio de sentido en {way_name} al final de la via",destination:"Haz un cambio de sentido hacia {destination} al final de la via"}},fork:{default:{default:"Mantente  {modifier} en el cruza",name:"Mantente {modifier} en {way_name}",destination:"Mantente {modifier} hacia {destination}"},"slight left":{default:"Mantente a la izquierda en el cruza",name:"Mantente a la izquierda en {way_name}",destination:"Mantente a la izquierda hacia {destination}"},"slight right":{default:"Mantente a la derecha en el cruza",name:"Mantente a la derecha en {way_name}",destination:"Mantente a la derecha hacia {destination}"},"sharp left":{default:"Gira a la izquierda en el cruza",name:"Gira a la izquierda en {way_name}",destination:"Gira a la izquierda hacia {destination}"},"sharp right":{default:"Gira a la derecha en el cruza",name:"Gira a la derecha en {way_name}",destination:"Gira a la derecha hacia {destination}"},uturn:{default:"Haz un cambio de sentido",name:"Haz un cambio de sentido en {way_name}",destination:"Haz un cambio de sentido hacia {destination}"}},merge:{default:{default:"Incorpórate a {modifier}",name:"Incorpórate a {modifier} en {way_name}",destination:"Incorpórate a {modifier} hacia {destination}"},straight:{default:"Incorpórate",name:"Incorpórate a {way_name}",destination:"Incorpórate hacia {destination}"},"slight left":{default:"Incorpórate a la izquierda",name:"Incorpórate a la izquierda en {way_name}",destination:"Incorpórate a la izquierda hacia {destination}"},"slight right":{default:"Incorpórate a la derecha",name:"Incorpórate a la derecha en {way_name}",destination:"Incorpórate a la derecha hacia {destination}"},"sharp left":{default:"Incorpórate a la izquierda",name:"Incorpórate a la izquierda en {way_name}",destination:"Incorpórate a la izquierda hacia {destination}"},"sharp right":{default:"Incorpórate a la derecha",name:"Incorpórate a la derecha en {way_name}",destination:"Incorpórate a la derecha hacia {destination}"},uturn:{default:"Haz un cambio de sentido",name:"Haz un cambio de sentido en {way_name}",destination:"Haz un cambio de sentido hacia {destination}"}},"new name":{default:{default:"Continúa {modifier}",name:"Continúa {modifier} en {way_name}",destination:"Continúa {modifier} hacia {destination}"},straight:{default:"Continúa recto",name:"Continúa en {way_name}",destination:"Continúa hacia {destination}"},"sharp left":{default:"Gira a la izquierda",name:"Gira a la izquierda en {way_name}",destination:"Gira a la izquierda hacia {destination}"},"sharp right":{default:"Gira a la derecha",name:"Gira a la derecha en {way_name}",destination:"Gira a la derecha hacia {destination}"},"slight left":{default:"Continúa levemente a la izquierda",name:"Continúa levemente a la izquierda en {way_name}",destination:"Continúa levemente a la izquierda hacia {destination}"},"slight right":{default:"Continúa levemente a la derecha",name:"Continúa levemente a la derecha en {way_name}",destination:"Continúa levemente a la derecha hacia {destination}"},uturn:{default:"Haz un cambio de sentido",name:"Haz un cambio de sentido en {way_name}",destination:"Haz un cambio de sentido hacia {destination}"}},notification:{default:{default:"Continúa {modifier}",name:"Continúa {modifier} en {way_name}",destination:"Continúa {modifier} hacia {destination}"},uturn:{default:"Haz un cambio de sentido",name:"Haz un cambio de sentido en {way_name}",destination:"Haz un cambio de sentido hacia {destination}"}},"off ramp":{default:{default:"Toma la salida",name:"Toma la salida en {way_name}",destination:"Toma la salida hacia {destination}",exit:"Toma la salida {exit}",exit_destination:"Toma la salida {exit} hacia {destination}"},left:{default:"Toma la salida en la izquierda",name:"Toma la salida en la izquierda en {way_name}",destination:"Toma la salida en la izquierda en {destination}",exit:"Toma la salida {exit} en la izquierda",exit_destination:"Toma la salida {exit} en la izquierda hacia {destination}"},right:{default:"Toma la salida en la derecha",name:"Toma la salida en la derecha en {way_name}",destination:"Toma la salida en la derecha hacia {destination}",exit:"Toma la salida {exit} en la derecha",exit_destination:"Toma la salida {exit} en la derecha hacia {destination}"},"sharp left":{default:"Ve cuesta abajo en la izquierda",name:"Ve cuesta abajo en la izquierda en {way_name}",destination:"Ve cuesta abajo en la izquierda hacia {destination}",exit:"Toma la salida {exit} en la izquierda",exit_destination:"Toma la salida {exit} en la izquierda hacia {destination}"},"sharp right":{default:"Ve cuesta abajo en la derecha",name:"Ve cuesta abajo en la derecha en {way_name}",destination:"Ve cuesta abajo en la derecha hacia {destination}",exit:"Toma la salida {exit} en la derecha",exit_destination:"Toma la salida {exit} en la derecha hacia {destination}"},"slight left":{default:"Ve cuesta abajo en la izquierda",name:"Ve cuesta abajo en la izquierda en {way_name}",destination:"Ve cuesta abajo en la izquierda hacia {destination}",exit:"Toma la salida {exit} en la izquierda",exit_destination:"Toma la salida {exit} en la izquierda hacia {destination}"},"slight right":{default:"Toma la salida en la derecha",name:"Toma la salida en la derecha en {way_name}",destination:"Toma la salida en la derecha hacia {destination}",exit:"Toma la salida {exit} en la derecha",exit_destination:"Toma la salida {exit} en la derecha hacia {destination}"}},"on ramp":{default:{default:"Toma la rampa",name:"Toma la rampa en {way_name}",destination:"Toma la rampa hacia {destination}"},left:{default:"Toma la rampa en la izquierda",name:"Toma la rampa en la izquierda en {way_name}",destination:"Toma la rampa en la izquierda hacia {destination}"},right:{default:"Toma la rampa en la derecha",name:"Toma la rampa en la derecha en {way_name}",destination:"Toma la rampa en la derecha hacia {destination}"},"sharp left":{default:"Toma la rampa en la izquierda",name:"Toma la rampa en la izquierda en {way_name}",destination:"Toma la rampa en la izquierda hacia {destination}"},"sharp right":{default:"Toma la rampa en la derecha",name:"Toma la rampa en la derecha en {way_name}",destination:"Toma la rampa en la derecha hacia {destination}"},"slight left":{default:"Toma la rampa en la izquierda",name:"Toma la rampa en la izquierda en {way_name}",destination:"Toma la rampa en la izquierda hacia {destination}"},"slight right":{default:"Toma la rampa en la derecha",name:"Toma la rampa en la derecha en {way_name}",destination:"Toma la rampa en la derecha hacia {destination}"}},rotary:{default:{default:{default:"Entra en la rotonda",name:"Entra en la rotonda y sal en {way_name}",destination:"Entra en la rotonda y sal hacia {destination}"},name:{default:"Entra en {rotary_name}",name:"Entra en {rotary_name} y sal en {way_name}",destination:"Entra en {rotary_name} y sal hacia {destination}"},exit:{default:"Entra en la rotonda y toma la {exit_number} salida",name:"Entra en la rotonda y toma la {exit_number} salida a {way_name}",destination:"Entra en la rotonda y toma la {exit_number} salida hacia {destination}"},name_exit:{default:"Entra en {rotary_name} y coge la {exit_number} salida",name:"Entra en {rotary_name} y coge la {exit_number} salida en {way_name}",destination:"Entra en {rotary_name} y coge la {exit_number} salida hacia {destination}"}}},roundabout:{default:{exit:{default:"Entra en la rotonda y toma la {exit_number} salida",name:"Entra en la rotonda y toma la {exit_number} salida a {way_name}",destination:"Entra en la rotonda y toma la {exit_number} salida hacia {destination}"},default:{default:"Entra en la rotonda",name:"Entra en la rotonda y sal en {way_name}",destination:"Entra en la rotonda y sal hacia {destination}"}}},"roundabout turn":{default:{default:"Sigue {modifier}",name:"Sigue {modifier} en {way_name}",destination:"Sigue {modifier} hacia {destination}"},left:{default:"Gira a la izquierda",name:"Gira a la izquierda en {way_name}",destination:"Gira a la izquierda hacia {destination}"},right:{default:"Gira a la derecha",name:"Gira a la derecha en {way_name}",destination:"Gira a la derecha hacia {destination}"},straight:{default:"Continúa recto",name:"Continúa recto en {way_name}",destination:"Continúa recto hacia {destination}"}},"exit roundabout":{default:{default:"Sal la rotonda",name:"Sal la rotonda en {way_name}",destination:"Sal la rotonda hacia {destination}"}},"exit rotary":{default:{default:"Sal la rotonda",name:"Sal la rotonda en {way_name}",destination:"Sal la rotonda hacia {destination}"}},turn:{default:{default:"Sigue {modifier}",name:"Sigue {modifier} en {way_name}",destination:"Sigue {modifier} hacia {destination}"},left:{default:"Gira a la izquierda",name:"Gira a la izquierda en {way_name}",destination:"Gira a la izquierda hacia {destination}"},right:{default:"Gira a la derecha",name:"Gira a la derecha en {way_name}",destination:"Gira a la derecha hacia {destination}"},straight:{default:"Ve recto",name:"Ve recto en {way_name}",destination:"Ve recto hacia {destination}"}},"use lane":{no_lanes:{default:"Continúa recto"},default:{default:"{lane_instruction}"}}}}},{}],29:[function(_dereq_,module,exports){module.exports={meta:{capitalizeFirstLetter:true},v5:{constants:{ordinalize:{1:"1.",2:"2.",3:"3.",4:"4.",5:"5.",6:"6.",7:"7.",8:"8.",9:"9.",10:"10."},direction:{north:"pohjoiseen",northeast:"koilliseen",east:"itään",southeast:"kaakkoon",south:"etelään",southwest:"lounaaseen",west:"länteen",northwest:"luoteeseen"},modifier:{left:"vasemmall(e/a)",right:"oikeall(e/a)","sharp left":"jyrkästi vasempaan","sharp right":"jyrkästi oikeaan","slight left":"loivasti vasempaan","slight right":"loivasti oikeaan",straight:"suoraan eteenpäin",uturn:"U-käännös"},lanes:{xo:"Pysy oikealla",ox:"Pysy vasemmalla",xox:"Pysy keskellä",oxo:"Pysy vasemmalla tai oikealla"}},modes:{ferry:{default:"Aja lautalle",name:"Aja lautalle {way_name}",destination:"Aja lautalle, jonka määränpää on {destination}"}},phrase:{"two linked by distance":"{instruction_one}, sitten {distance} päästä, {instruction_two}","two linked":"{instruction_one}, sitten {instruction_two}","one in distance":"{distance} päästä, {instruction_one}","name and ref":"{name} ({ref})","exit with number":"{exit}"},arrive:{default:{default:"Olet saapunut {nth} määränpäähäsi",upcoming:"Saavut {nth} määränpäähäsi",short:"Olet saapunut","short-upcoming":"Saavut",named:"Olet saapunut määränpäähän {waypoint_name}"},left:{default:"Olet saapunut {nth} määränpäähäsi, joka on vasemmalla puolellasi",upcoming:"Saavut {nth} määränpäähäsi, joka on vasemmalla puolellasi",short:"Olet saapunut","short-upcoming":"Saavut",named:"Olet saapunut määränpäähän {waypoint_name}, joka on vasemmalla puolellasi"},right:{default:"Olet saapunut {nth} määränpäähäsi, joka on oikealla puolellasi",upcoming:"Saavut {nth} määränpäähäsi, joka on oikealla puolellasi",short:"Olet saapunut","short-upcoming":"Saavut",named:"Olet saapunut määränpäähän {waypoint_name}, joka on oikealla puolellasi"},"sharp left":{default:"Olet saapunut {nth} määränpäähäsi, joka on vasemmalla puolellasi",upcoming:"Saavut {nth} määränpäähäsi, joka on vasemmalla puolellasi",short:"Olet saapunut","short-upcoming":"Saavut",named:"Olet saapunut määränpäähän {waypoint_name}, joka on vasemmalla puolellasi"},"sharp right":{default:"Olet saapunut {nth} määränpäähäsi, joka on oikealla puolellasi",upcoming:"Saavut {nth} määränpäähäsi, joka on oikealla puolellasi",short:"Olet saapunut","short-upcoming":"Saavut",named:"Olet saapunut määränpäähän {waypoint_name}, joka on oikealla puolellasi"},"slight right":{default:"Olet saapunut {nth} määränpäähäsi, joka on oikealla puolellasi",upcoming:"Saavut {nth} määränpäähäsi, joka on oikealla puolellasi",short:"Olet saapunut","short-upcoming":"Saavut",named:"Olet saapunut määränpäähän {waypoint_name}, joka on oikealla puolellasi"},"slight left":{default:"Olet saapunut {nth} määränpäähäsi, joka on vasemmalla puolellasi",upcoming:"Saavut {nth} määränpäähäsi, joka on vasemmalla puolellasi",short:"Olet saapunut","short-upcoming":"Saavut",named:"Olet saapunut määränpäähän {waypoint_name}, joka on vasemmalla puolellasi"},straight:{default:"Olet saapunut {nth} määränpäähäsi, joka on suoraan edessäsi",upcoming:"Saavut {nth} määränpäähäsi, suoraan edessä",short:"Olet saapunut","short-upcoming":"Saavut",named:"Olet saapunut määränpäähän {waypoint_name}, joka on suoraan edessäsi"}},continue:{default:{default:"Käänny {modifier}",name:"Käänny {modifier} pysyäksesi tiellä {way_name}",destination:"Käänny {modifier} suuntana {destination}",exit:"Käänny {modifier} tielle {way_name}"},straight:{default:"Jatka suoraan eteenpäin",name:"Jatka suoraan pysyäksesi tiellä {way_name}",destination:"Jatka suuntana {destination}",distance:"Jatka suoraan {distance}",namedistance:"Jatka tiellä {way_name} {distance}"},"sharp left":{default:"Jatka jyrkästi vasempaan",name:"Jatka jyrkästi vasempaan pysyäksesi tiellä {way_name}",destination:"Jatka jyrkästi vasempaan suuntana {destination}"},"sharp right":{default:"Jatka jyrkästi oikeaan",name:"Jatka jyrkästi oikeaan pysyäksesi tiellä {way_name}",destination:"Jatka jyrkästi oikeaan suuntana {destination}"},"slight left":{default:"Jatka loivasti vasempaan",name:"Jatka loivasti vasempaan pysyäksesi tiellä {way_name}",destination:"Jatka loivasti vasempaan suuntana {destination}"},"slight right":{default:"Jatka loivasti oikeaan",name:"Jatka loivasti oikeaan pysyäksesi tiellä {way_name}",destination:"Jatka loivasti oikeaan suuntana {destination}"},uturn:{default:"Tee U-käännös",name:"Tee U-käännös ja jatka tietä {way_name}",destination:"Tee U-käännös suuntana {destination}"}},depart:{default:{default:"Aja {direction}",name:"Aja tietä {way_name} {direction}",namedistance:"Aja {distance} {direction} tietä {way_name} "}},"end of road":{default:{default:"Käänny {modifier}",name:"Käänny {modifier} tielle {way_name}",destination:"Käänny {modifier} suuntana {destination}"},straight:{default:"Jatka suoraan eteenpäin",name:"Jatka suoraan eteenpäin tielle {way_name}",destination:"Jatka suoraan eteenpäin suuntana {destination}"},uturn:{default:"Tien päässä tee U-käännös",name:"Tien päässä tee U-käännös tielle {way_name}",destination:"Tien päässä tee U-käännös suuntana {destination}"}},fork:{default:{default:"Jatka tienhaarassa {modifier}",name:"Jatka {modifier} tielle {way_name}",destination:"Jatka {modifier} suuntana {destination}"},"slight left":{default:"Pysy vasemmalla tienhaarassa",name:"Pysy vasemmalla tielle {way_name}",destination:"Pysy vasemmalla suuntana {destination}"},"slight right":{default:"Pysy oikealla tienhaarassa",name:"Pysy oikealla tielle {way_name}",destination:"Pysy oikealla suuntana {destination}"},"sharp left":{default:"Käänny tienhaarassa jyrkästi vasempaan",name:"Käänny tienhaarassa jyrkästi vasempaan tielle {way_name}",destination:"Käänny tienhaarassa jyrkästi vasempaan suuntana {destination}"},"sharp right":{default:"Käänny tienhaarassa jyrkästi oikeaan",name:"Käänny tienhaarassa jyrkästi oikeaan tielle {way_name}",destination:"Käänny tienhaarassa jyrkästi oikeaan suuntana {destination}"},uturn:{default:"Tee U-käännös",name:"Tee U-käännös tielle {way_name}",destination:"Tee U-käännös suuntana {destination}"}},merge:{default:{default:"Liity {modifier}",name:"Liity {modifier}, tielle {way_name}",destination:"Liity {modifier}, suuntana {destination}"},straight:{default:"Liity",name:"Liity tielle {way_name}",destination:"Liity suuntana {destination}"},"slight left":{default:"Liity vasemmalle",name:"Liity vasemmalle, tielle {way_name}",destination:"Liity vasemmalle, suuntana {destination}"},"slight right":{default:"Liity oikealle",name:"Liity oikealle, tielle {way_name}",destination:"Liity oikealle, suuntana {destination}"},"sharp left":{default:"Liity vasemmalle",name:"Liity vasemmalle, tielle {way_name}",destination:"Liity vasemmalle, suuntana {destination}"},"sharp right":{default:"Liity oikealle",name:"Liity oikealle, tielle {way_name}",destination:"Liity oikealle, suuntana {destination}"},uturn:{default:"Tee U-käännös",name:"Tee U-käännös tielle {way_name}",destination:"Tee U-käännös suuntana {destination}"}},"new name":{default:{default:"Jatka {modifier}",name:"Jatka {modifier} tielle {way_name}",destination:"Jatka {modifier} suuntana {destination}"},straight:{default:"Jatka suoraan eteenpäin",name:"Jatka tielle {way_name}",destination:"Jatka suuntana {destination}"},"sharp left":{default:"Käänny jyrkästi vasempaan",name:"Käänny jyrkästi vasempaan tielle {way_name}",destination:"Käänny jyrkästi vasempaan suuntana {destination}"},"sharp right":{default:"Käänny jyrkästi oikeaan",name:"Käänny jyrkästi oikeaan tielle {way_name}",destination:"Käänny jyrkästi oikeaan suuntana {destination}"},"slight left":{default:"Jatka loivasti vasempaan",name:"Jatka loivasti vasempaan tielle {way_name}",destination:"Jatka loivasti vasempaan suuntana {destination}"},"slight right":{default:"Jatka loivasti oikeaan",name:"Jatka loivasti oikeaan tielle {way_name}",destination:"Jatka loivasti oikeaan suuntana {destination}"},uturn:{default:"Tee U-käännös",name:"Tee U-käännös tielle {way_name}",destination:"Tee U-käännös suuntana {destination}"}},notification:{default:{default:"Jatka {modifier}",name:"Jatka {modifier} tielle {way_name}",destination:"Jatka {modifier} suuntana {destination}"},uturn:{default:"Tee U-käännös",name:"Tee U-käännös tielle {way_name}",destination:"Tee U-käännös suuntana {destination}"}},"off ramp":{default:{default:"Aja erkanemiskaistalle",name:"Aja erkanemiskaistaa tielle {way_name}",destination:"Aja erkanemiskaistalle suuntana {destination}",exit:"Ota poistuminen {exit}",exit_destination:"Ota poistuminen {exit}, suuntana {destination}"},left:{default:"Aja vasemmalla olevalle erkanemiskaistalle",name:"Aja vasemmalla olevaa erkanemiskaistaa tielle {way_name}",destination:"Aja vasemmalla olevalle erkanemiskaistalle suuntana {destination}",exit:"Ota poistuminen {exit} vasemmalla",exit_destination:"Ota poistuminen {exit} vasemmalla, suuntana {destination}"},right:{default:"Aja oikealla olevalle erkanemiskaistalle",name:"Aja oikealla olevaa erkanemiskaistaa tielle {way_name}",destination:"Aja oikealla olevalle erkanemiskaistalle suuntana {destination}",exit:"Ota poistuminen {exit} oikealla",exit_destination:"Ota poistuminen {exit} oikealla, suuntana {destination}"},"sharp left":{default:"Aja vasemmalla olevalle erkanemiskaistalle",name:"Aja vasemmalla olevaa erkanemiskaistaa tielle {way_name}",destination:"Aja vasemmalla olevalle erkanemiskaistalle suuntana {destination}",exit:"Ota poistuminen {exit} vasemmalla",exit_destination:"Ota poistuminen {exit} vasemmalla, suuntana {destination}"},"sharp right":{default:"Aja oikealla olevalle erkanemiskaistalle",name:"Aja oikealla olevaa erkanemiskaistaa tielle {way_name}",destination:"Aja oikealla olevalle erkanemiskaistalle suuntana {destination}",exit:"Ota poistuminen {exit} oikealla",exit_destination:"Ota poistuminen {exit} oikealla, suuntana {destination}"},"slight left":{default:"Aja vasemmalla olevalle erkanemiskaistalle",name:"Aja vasemmalla olevaa erkanemiskaistaa tielle {way_name}",destination:"Aja vasemmalla olevalle erkanemiskaistalle suuntana {destination}",exit:"Ota poistuminen {exit} vasemmalla",exit_destination:"Ota poistuminen {exit} vasemmalla, suuntana {destination}"},"slight right":{default:"Aja oikealla olevalle erkanemiskaistalle",name:"Aja oikealla olevaa erkanemiskaistaa tielle {way_name}",destination:"Aja oikealla olevalle erkanemiskaistalle suuntana {destination}",exit:"Ota poistuminen {exit} oikealla",exit_destination:"Ota poistuminen {exit} oikealla, suuntana {destination}"}},"on ramp":{default:{default:"Aja erkanemiskaistalle",name:"Aja erkanemiskaistaa tielle {way_name}",destination:"Aja erkanemiskaistalle suuntana {destination}"},left:{default:"Aja vasemmalla olevalle erkanemiskaistalle",name:"Aja vasemmalla olevaa erkanemiskaistaa tielle {way_name}",destination:"Aja vasemmalla olevalle erkanemiskaistalle suuntana {destination}"},right:{default:"Aja oikealla olevalle erkanemiskaistalle",name:"Aja oikealla olevaa erkanemiskaistaa tielle {way_name}",destination:"Aja oikealla olevalle erkanemiskaistalle suuntana {destination}"},"sharp left":{default:"Aja vasemmalla olevalle erkanemiskaistalle",name:"Aja vasemmalla olevaa erkanemiskaistaa tielle {way_name}",destination:"Aja vasemmalla olevalle erkanemiskaistalle suuntana {destination}"},"sharp right":{default:"Aja oikealla olevalle erkanemiskaistalle",name:"Aja oikealla olevaa erkanemiskaistaa tielle {way_name}",destination:"Aja oikealla olevalle erkanemiskaistalle suuntana {destination}"},"slight left":{default:"Aja vasemmalla olevalle erkanemiskaistalle",name:"Aja vasemmalla olevaa erkanemiskaistaa tielle {way_name}",destination:"Aja vasemmalla olevalle erkanemiskaistalle suuntana {destination}"},"slight right":{default:"Aja oikealla olevalle erkanemiskaistalle",name:"Aja oikealla olevaa erkanemiskaistaa tielle {way_name}",destination:"Aja oikealla olevalle erkanemiskaistalle suuntana {destination}"}},rotary:{default:{default:{default:"Aja liikenneympyrään",name:"Aja liikenneympyrään ja valitse erkanemiskaista tielle {way_name}",destination:"Aja liikenneympyrään ja valitse erkanemiskaista suuntana {destination}"},name:{default:"Aja liikenneympyrään {rotary_name}",name:"Aja liikenneympyrään {rotary_name} ja valitse erkanemiskaista tielle {way_name}",destination:"Aja liikenneympyrään {rotary_name} ja valitse erkanemiskaista suuntana {destination}"},exit:{default:"Aja liikenneympyrään ja valitse {exit_number} erkanemiskaista",name:"Aja liikenneympyrään ja valitse {exit_number} erkanemiskaista tielle {way_name}",destination:"Aja liikenneympyrään ja valitse {exit_number} erkanemiskaista suuntana {destination}"},name_exit:{default:"Aja liikenneympyrään {rotary_name} ja valitse {exit_number} erkanemiskaista",name:"Aja liikenneympyrään {rotary_name} ja valitse {exit_number} erkanemiskaista tielle {way_name}",destination:"Aja liikenneympyrään {rotary_name} ja valitse {exit_number} erkanemiskaista suuntana {destination}"}}},roundabout:{default:{exit:{default:"Aja liikenneympyrään ja valitse {exit_number} erkanemiskaista",name:"Aja liikenneympyrään ja valitse {exit_number} erkanemiskaista tielle {way_name}",destination:"Aja liikenneympyrään ja valitse {exit_number} erkanemiskaista suuntana {destination}"},default:{default:"Aja liikenneympyrään",name:"Aja liikenneympyrään ja valitse erkanemiskaista tielle {way_name}",destination:"Aja liikenneympyrään ja valitse erkanemiskaista suuntana {destination}"}}},"roundabout turn":{default:{default:"Käänny {modifier}",name:"Käänny {modifier} tielle {way_name}",destination:"Käänny {modifier} suuntana {destination}"},left:{default:"Käänny vasempaan",name:"Käänny vasempaan tielle {way_name}",destination:"Käänny vasempaan suuntana {destination}"},right:{default:"Käänny oikeaan",name:"Käänny oikeaan tielle {way_name}",destination:"Käänny oikeaan suuntana {destination}"},straight:{default:"Jatka suoraan eteenpäin",name:"Jatka suoraan eteenpäin tielle {way_name}",destination:"Jatka suoraan eteenpäin suuntana {destination}"}},"exit roundabout":{default:{default:"Poistu liikenneympyrästä",name:"Poistu liikenneympyrästä tielle {way_name}",destination:"Poistu liikenneympyrästä suuntana {destination}"}},"exit rotary":{default:{default:"Poistu liikenneympyrästä",name:"Poistu liikenneympyrästä tielle {way_name}",destination:"Poistu liikenneympyrästä suuntana {destination}"}},turn:{default:{default:"Käänny {modifier}",name:"Käänny {modifier} tielle {way_name}",destination:"Käänny {modifier} suuntana {destination}"},left:{default:"Käänny vasempaan",name:"Käänny vasempaan tielle {way_name}",destination:"Käänny vasempaan suuntana {destination}"},right:{default:"Käänny oikeaan",name:"Käänny oikeaan tielle {way_name}",destination:"Käänny oikeaan suuntana {destination}"},straight:{default:"Aja suoraan eteenpäin",name:"Aja suoraan eteenpäin tielle {way_name}",destination:"Aja suoraan eteenpäin suuntana {destination}"}},"use lane":{no_lanes:{default:"Jatka suoraan eteenpäin"},default:{default:"{lane_instruction}"}}}}},{}],30:[function(_dereq_,module,exports){module.exports={meta:{capitalizeFirstLetter:true},v5:{constants:{ordinalize:{1:"première",2:"seconde",3:"troisième",4:"quatrième",5:"cinquième",6:"sixième",7:"septième",8:"huitième",9:"neuvième",10:"dixième"},direction:{north:"le nord",northeast:"le nord-est",east:"l’est",southeast:"le sud-est",south:"le sud",southwest:"le sud-ouest",west:"l’ouest",northwest:"le nord-ouest"},modifier:{left:"à gauche",right:"à droite","sharp left":"franchement à gauche","sharp right":"franchement à droite","slight left":"légèrement à gauche","slight right":"légèrement à droite",straight:"tout droit",uturn:"demi-tour"},lanes:{xo:"Tenir la droite",ox:"Tenir la gauche",xox:"Rester au milieu",oxo:"Tenir la gauche ou la droite"}},modes:{ferry:{default:"Prendre le ferry",name:"Prendre le ferry {way_name:article}",destination:"Prendre le ferry en direction {destination:preposition}"}},phrase:{"two linked by distance":"{instruction_one}, puis, dans {distance}, {instruction_two}","two linked":"{instruction_one}, puis {instruction_two}","one in distance":"Dans {distance}, {instruction_one}","name and ref":"{name} ({ref})","exit with number":"sortie n°{exit}"},arrive:{default:{
default:"Vous êtes arrivé à votre {nth} destination",upcoming:"Vous arriverez à votre {nth} destination",short:"Vous êtes arrivé","short-upcoming":"Vous arriverez",named:"Vous êtes arrivé {waypoint_name:arrival}"},left:{default:"Vous êtes arrivé à votre {nth} destination, sur la gauche",upcoming:"Vous arriverez à votre {nth} destination, sur la gauche",short:"Vous êtes arrivé","short-upcoming":"Vous arriverez",named:"Vous êtes arrivé {waypoint_name:arrival}, sur la gauche"},right:{default:"Vous êtes arrivé à votre {nth} destination, sur la droite",upcoming:"Vous arriverez à votre {nth} destination, sur la droite",short:"Vous êtes arrivé","short-upcoming":"Vous arriverez",named:"Vous êtes arrivé à  {waypoint_name:arrival}, sur la droite"},"sharp left":{default:"Vous êtes arrivé à votre {nth} destination, sur la gauche",upcoming:"Vous arriverez à votre {nth} destination, sur la gauche",short:"Vous êtes arrivé","short-upcoming":"Vous arriverez",named:"Vous êtes arrivé {waypoint_name:arrival}, sur la gauche"},"sharp right":{default:"Vous êtes arrivé à votre {nth} destination, sur la droite",upcoming:"Vous arriverez à votre {nth} destination, sur la droite",short:"Vous êtes arrivé","short-upcoming":"Vous arriverez",named:"Vous êtes arrivé {waypoint_name:arrival}, sur la droite"},"slight right":{default:"Vous êtes arrivé à votre {nth} destination, sur la droite",upcoming:"Vous arriverez à votre {nth} destination, sur la droite",short:"Vous êtes arrivé","short-upcoming":"Vous arriverez",named:"Vous êtes arrivé {waypoint_name:arrival}, sur la droite"},"slight left":{default:"Vous êtes arrivé à votre {nth} destination, sur la gauche",upcoming:"Vous arriverez à votre {nth} destination, sur la gauche",short:"Vous êtes arrivé","short-upcoming":"Vous êtes arrivé",named:"Vous êtes arrivé {waypoint_name:arrival}, sur la gauche"},straight:{default:"Vous êtes arrivé à votre {nth} destination, droit devant",upcoming:"Vous arriverez à votre {nth} destination, droit devant",short:"Vous êtes arrivé","short-upcoming":"Vous êtes arrivé",named:"Vous êtes arrivé {waypoint_name:arrival}, droit devant"}},continue:{default:{default:"Tourner {modifier}",name:"Tourner {modifier} pour rester sur {way_name:article}",destination:"Tourner {modifier} en direction {destination:preposition}",exit:"Tourner {modifier} sur {way_name:article}"},straight:{default:"Continuer tout droit",name:"Continuer tout droit pour rester sur {way_name:article}",destination:"Continuer tout droit en direction {destination:preposition}",distance:"Continuer tout droit sur {distance}",namedistance:"Continuer sur {way_name:article} sur {distance}"},"sharp left":{default:"Tourner franchement à gauche",name:"Tourner franchement à gauche pour rester sur {way_name:article}",destination:"Tourner franchement à gauche en direction {destination:preposition}"},"sharp right":{default:"Tourner franchement à droite",name:"Tourner franchement à droite pour rester sur {way_name:article}",destination:"Tourner franchement à droite en direction {destination:preposition}"},"slight left":{default:"Tourner légèrement à gauche",name:"Tourner légèrement à gauche pour rester sur {way_name:article}",destination:"Tourner légèrement à gauche en direction {destination:preposition}"},"slight right":{default:"Tourner légèrement à droite",name:"Tourner légèrement à droite pour rester sur {way_name:article}",destination:"Tourner légèrement à droite en direction {destination:preposition}"},uturn:{default:"Faire demi-tour",name:"Faire demi-tour et continuer sur {way_name:article}",destination:"Faire demi-tour en direction {destination:preposition}"}},depart:{default:{default:"Se diriger vers {direction}",name:"Se diriger vers {direction} sur {way_name:article}",namedistance:"Se diriger vers {direction} sur {way_name:article} sur {distance}"}},"end of road":{default:{default:"Tourner {modifier}",name:"Tourner {modifier} sur {way_name:article}",destination:"Tourner {modifier} en direction {destination:preposition}"},straight:{default:"Continuer tout droit",name:"Continuer tout droit sur {way_name:article}",destination:"Continuer tout droit en direction {destination:preposition}"},uturn:{default:"Faire demi-tour à la fin de la route",name:"Faire demi-tour à la fin {way_name:preposition}",destination:"Faire demi-tour à la fin de la route en direction {destination:preposition}"}},fork:{default:{default:"Tenir {modifier} à l’embranchement",name:"Tenir {modifier} sur {way_name:article}",destination:"Tenir {modifier} en direction {destination:preposition}"},"slight left":{default:"Tenir la gauche à l’embranchement",name:"Tenir la gauche sur {way_name:article}",destination:"Tenir la gauche en direction {destination:preposition}"},"slight right":{default:"Tenir la droite à l’embranchement",name:"Tenir la droite sur {way_name:article}",destination:"Tenir la droite en direction {destination:preposition}"},"sharp left":{default:"Tourner franchement à gauche à l’embranchement",name:"Tourner franchement à gauche sur {way_name:article}",destination:"Tourner franchement à gauche en direction {destination:preposition}"},"sharp right":{default:"Tourner franchement à droite à l’embranchement",name:"Tourner franchement à droite sur {way_name:article}",destination:"Tourner franchement à droite en direction {destination:preposition}"},uturn:{default:"Faire demi-tour",name:"Faire demi-tour sur {way_name:article}",destination:"Faire demi-tour en direction {destination:preposition}"}},merge:{default:{default:"S’insérer {modifier}",name:"S’insérer {modifier} sur {way_name:article}",destination:"S’insérer {modifier} en direction {destination:preposition}"},straight:{default:"S’insérer",name:"S’insérer sur {way_name:article}",destination:"S’insérer en direction {destination:preposition}"},"slight left":{default:"S’insérer légèrement à gauche",name:"S’insérer légèrement à gauche sur {way_name:article}",destination:"S’insérer légèrement à gauche en direction {destination:preposition}"},"slight right":{default:"S’insérer légèrement à droite",name:"S’insérer légèrement à droite sur {way_name:article}",destination:"S’insérer à droite en direction {destination:preposition}"},"sharp left":{default:"S’insérer à gauche",name:"S’insérer à gauche sur {way_name:article}",destination:"S’insérer à gauche en direction {destination:preposition}"},"sharp right":{default:"S’insérer à droite",name:"S’insérer à droite sur {way_name:article}",destination:"S’insérer à droite en direction {destination:preposition}"},uturn:{default:"Faire demi-tour",name:"Faire demi-tour sur {way_name:article}",destination:"Faire demi-tour en direction {destination:preposition}"}},"new name":{default:{default:"Continuer {modifier}",name:"Continuer {modifier} sur {way_name:article}",destination:"Continuer {modifier} en direction {destination:preposition}"},straight:{default:"Continuer tout droit",name:"Continuer tout droit sur {way_name:article}",destination:"Continuer tout droit en direction {destination:preposition}"},"sharp left":{default:"Tourner franchement à gauche",name:"Tourner franchement à gauche sur {way_name:article}",destination:"Tourner franchement à gauche en direction {destination:preposition}"},"sharp right":{default:"Tourner franchement à droite",name:"Tourner franchement à droite sur {way_name:article}",destination:"Tourner franchement à droite en direction {destination:preposition}"},"slight left":{default:"Continuer légèrement à gauche",name:"Continuer légèrement à gauche sur {way_name:article}",destination:"Continuer légèrement à gauche en direction {destination:preposition}"},"slight right":{default:"Continuer légèrement à droite",name:"Continuer légèrement à droite sur {way_name:article}",destination:"Continuer légèrement à droite en direction {destination:preposition}"},uturn:{default:"Faire demi-tour",name:"Faire demi-tour sur {way_name:article}",destination:"Faire demi-tour en direction {destination:preposition}"}},notification:{default:{default:"Continuer {modifier}",name:"Continuer {modifier} sur {way_name:article}",destination:"Continuer {modifier} en direction {destination:preposition}"},uturn:{default:"Faire demi-tour",name:"Faire demi-tour sur {way_name:article}",destination:"Faire demi-tour en direction {destination:preposition}"}},"off ramp":{default:{default:"Prendre la sortie",name:"Prendre la sortie sur {way_name:article}",destination:"Prendre la sortie en direction {destination:preposition}",exit:"Prendre la sortie {exit}",exit_destination:"Prendre la sortie {exit} en direction {destination:preposition}"},left:{default:"Prendre la sortie à gauche",name:"Prendre la sortie à gauche sur {way_name:article}",destination:"Prendre la sortie à gauche en direction {destination:preposition}",exit:"Prendre la sortie {exit} sur la gauche",exit_destination:"Prendre la sortie {exit} sur la gauche en direction {destination:preposition}"},right:{default:"Prendre la sortie à droite",name:"Prendre la sortie à droite sur {way_name:article}",destination:"Prendre la sortie à droite en direction {destination:preposition}",exit:"Prendre la sortie {exit} sur la droite",exit_destination:"Prendre la sortie {exit} sur la droite en direction {destination:preposition}"},"sharp left":{default:"Prendre la sortie à gauche",name:"Prendre la sortie à gauche sur {way_name:article}",destination:"Prendre la sortie à gauche en direction {destination:preposition}",exit:"Prendre la sortie {exit} sur la gauche",exit_destination:"Prendre la sortie {exit} sur la gauche en direction {destination:preposition}"},"sharp right":{default:"Prendre la sortie à droite",name:"Prendre la sortie à droite sur {way_name:article}",destination:"Prendre la sortie à droite en direction {destination:preposition}",exit:"Prendre la sortie {exit} sur la droite",exit_destination:"Prendre la sortie {exit} sur la droite en direction {destination:preposition}"},"slight left":{default:"Prendre la sortie à gauche",name:"Prendre la sortie à gauche sur {way_name:article}",destination:"Prendre la sortie à gauche en direction {destination:preposition}",exit:"Prendre la sortie {exit} sur la gauche",exit_destination:"Prendre la sortie {exit} sur la gauche en direction {destination:preposition}"},"slight right":{default:"Prendre la sortie à droite",name:"Prendre la sortie à droite sur {way_name:article}",destination:"Prendre la sortie à droite en direction {destination:preposition}",exit:"Prendre la sortie {exit} sur la droite",exit_destination:"Prendre la sortie {exit} sur la droite en direction {destination:preposition}"}},"on ramp":{default:{default:"Prendre la sortie",name:"Prendre la sortie sur {way_name:article}",destination:"Prendre la sortie en direction {destination:preposition}"},left:{default:"Prendre la sortie à gauche",name:"Prendre la sortie à gauche sur {way_name:article}",destination:"Prendre la sortie à gauche en direction {destination:preposition}"},right:{default:"Prendre la sortie à droite",name:"Prendre la sortie à droite sur {way_name:article}",destination:"Prendre la sortie à droite en direction {destination:preposition}"},"sharp left":{default:"Prendre la sortie à gauche",name:"Prendre la sortie à gauche sur {way_name:article}",destination:"Prendre la sortie à gauche en direction {destination:preposition}"},"sharp right":{default:"Prendre la sortie à droite",name:"Prendre la sortie à droite sur {way_name:article}",destination:"Prendre la sortie à droite en direction {destination:preposition}"},"slight left":{default:"Prendre la sortie à gauche",name:"Prendre la sortie à gauche sur {way_name:article}",destination:"Prendre la sortie à gauche en direction {destination:preposition}"},"slight right":{default:"Prendre la sortie à droite",name:"Prendre la sortie à droite sur {way_name:article}",destination:"Prendre la sortie à droite en direction {destination:preposition}"}},rotary:{default:{default:{default:"Prendre le rond-point",name:"Prendre le rond-point, puis sortir sur {way_name:article}",destination:"Prendre le rond-point, puis sortir en direction {destination:preposition}"},name:{default:"Prendre {rotary_name:rotary}",name:"Prendre {rotary_name:rotary}, puis sortir par {way_name:article}",destination:"Prendre {rotary_name:rotary}, puis sortir en direction {destination:preposition}"},exit:{default:"Prendre le rond-point, puis la {exit_number} sortie",name:"Prendre le rond-point, puis la {exit_number} sortie sur {way_name:article}",destination:"Prendre le rond-point, puis la {exit_number} sortie en direction {destination:preposition}"},name_exit:{default:"Prendre {rotary_name:rotary}, puis la {exit_number} sortie",name:"Prendre {rotary_name:rotary}, puis la {exit_number} sortie sur {way_name:article}",destination:"Prendre {rotary_name:rotary}, puis la {exit_number} sortie en direction {destination:preposition}"}}},roundabout:{default:{exit:{default:"Prendre le rond-point, puis la {exit_number} sortie",name:"Prendre le rond-point, puis la {exit_number} sortie sur {way_name:article}",destination:"Prendre le rond-point, puis la {exit_number} sortie en direction {destination:preposition}"},default:{default:"Prendre le rond-point",name:"Prendre le rond-point, puis sortir sur {way_name:article}",destination:"Prendre le rond-point, puis sortir en direction {destination:preposition}"}}},"roundabout turn":{default:{default:"Tourner {modifier}",name:"Tourner {modifier} sur {way_name:article}",destination:"Tourner {modifier} en direction {destination:preposition}"},left:{default:"Tourner à gauche",name:"Tourner à gauche sur {way_name:article}",destination:"Tourner à gauche en direction {destination:preposition}"},right:{default:"Tourner à droite",name:"Tourner à droite sur {way_name:article}",destination:"Tourner à droite en direction {destination:preposition}"},straight:{default:"Continuer tout droit",name:"Continuer tout droit sur {way_name:article}",destination:"Continuer tout droit en direction {destination:preposition}"}},"exit roundabout":{default:{default:"Sortir du rond-point",name:"Sortir du rond-point sur {way_name:article}",destination:"Sortir du rond-point en direction {destination:preposition}"}},"exit rotary":{default:{default:"Sortir du rond-point",name:"Sortir du rond-point sur {way_name:article}",destination:"Sortir du rond-point en direction {destination:preposition}"}},turn:{default:{default:"Tourner {modifier}",name:"Tourner {modifier} sur {way_name:article}",destination:"Tourner {modifier} en direction {destination:preposition}"},left:{default:"Tourner à gauche",name:"Tourner à gauche sur {way_name:article}",destination:"Tourner à gauche en direction {destination:preposition}"},right:{default:"Tourner à droite",name:"Tourner à droite sur {way_name:article}",destination:"Tourner à droite en direction {destination:preposition}"},straight:{default:"Aller tout droit",name:"Aller tout droit sur {way_name:article}",destination:"Aller tout droit en direction {destination:preposition}"}},"use lane":{no_lanes:{default:"Continuer tout droit"},default:{default:"{lane_instruction}"}}}}},{}],31:[function(_dereq_,module,exports){module.exports={meta:{capitalizeFirstLetter:true},v5:{constants:{ordinalize:{1:"ראשונה",2:"שניה",3:"שלישית",4:"רביעית",5:"חמישית",6:"שישית",7:"שביעית",8:"שמינית",9:"תשיעית",10:"עשירית"},direction:{north:"צפון",northeast:"צפון מזרח",east:"מזרח",southeast:"דרום מזרח",south:"דרום",southwest:"דרום מערב",west:"מערב",northwest:"צפון מערב"},modifier:{left:"שמאלה",right:"ימינה","sharp left":"חדה שמאלה","sharp right":"חדה ימינה","slight left":"קלה שמאלה","slight right":"קלה ימינה",straight:"ישר",uturn:"פניית פרסה"},lanes:{xo:"היצמד לימין",ox:"היצמד לשמאל",xox:"המשך בנתיב האמצעי",oxo:"היצמד לימין או לשמאל"}},modes:{ferry:{default:"עלה על המעבורת",name:"עלה על המעבורת {way_name}",destination:"עלה על המעבורת לכיוון {destination}"}},phrase:{"two linked by distance":"{instruction_one}, ואז, בעוד{distance}, {instruction_two}","two linked":"{instruction_one}, ואז {instruction_two}","one in distance":"בעוד {distance}, {instruction_one}","name and ref":"{name} ({ref})","exit with number":"יציאה {exit}"},arrive:{default:{default:"הגעת אל היעד ה{nth} שלך",upcoming:"אתה תגיע אל היעד ה{nth} שלך",short:"הגעת","short-upcoming":"תגיע",named:"הגעת אל {waypoint_name}"},left:{default:"הגעת אל היעד ה{nth} שלך משמאלך",upcoming:"אתה תגיע אל היעד ה{nth} שלך משמאלך",short:"הגעת","short-upcoming":"תגיע",named:"הגעת אל {waypoint_name} שלך משמאלך"},right:{default:"הגעת אל היעד ה{nth} שלך מימינך",upcoming:"אתה תגיע אל היעד ה{nth} שלך מימינך",short:"הגעת","short-upcoming":"תגיע",named:"הגעת אל {waypoint_name} שלך מימינך"},"sharp left":{default:"הגעת אל היעד ה{nth} שלך משמאלך",upcoming:"אתה תגיע אל היעד ה{nth} שלך משמאלך",short:"הגעת","short-upcoming":"תגיע",named:"הגעת אל {waypoint_name} שלך משמאלך"},"sharp right":{default:"הגעת אל היעד ה{nth} שלך מימינך",upcoming:"אתה תגיע אל היעד ה{nth} שלך מימינך",short:"הגעת","short-upcoming":"תגיע",named:"הגעת אל {waypoint_name} שלך מימינך"},"slight right":{default:"הגעת אל היעד ה{nth} שלך מימינך",upcoming:"אתה תגיע אל היעד ה{nth} שלך מימינך",short:"הגעת","short-upcoming":"תגיע",named:"הגעת אל {waypoint_name} שלך מימינך"},"slight left":{default:"הגעת אל היעד ה{nth} שלך משמאלך",upcoming:"אתה תגיע אל היעד ה{nth} שלך משמאלך",short:"הגעת","short-upcoming":"תגיע",named:"הגעת אל {waypoint_name} שלך משמאלך"},straight:{default:"הגעת אל היעד ה{nth} שלך, בהמשך",upcoming:"אתה תגיע אל היעד ה{nth} שלך, בהמשך",short:"הגעת","short-upcoming":"תגיע",named:"הגעת אל {waypoint_name}, בהמשך"}},continue:{default:{default:"פנה {modifier}",name:"פנה {modifier} כדי להישאר ב{way_name}",destination:"פנה {modifier} לכיוון {destination}",exit:"פנה {modifier} על {way_name}"},straight:{default:"המשך ישר",name:"המשך ישר כדי להישאר על {way_name}",destination:"המשך לכיוון {destination}",distance:"המשך ישר לאורך {distance}",namedistance:"המשך על {way_name} לאורך {distance}"},"sharp left":{default:"פנה בחדות שמאלה",name:"פנה בחדות שמאלה כדי להישאר על {way_name}",destination:"פנה בחדות שמאלה לכיוון {destination}"},"sharp right":{default:"פנה בחדות ימינה",name:"פנה בחדות ימינה כדי להישאר על {way_name}",destination:"פנה בחדות ימינה לכיוון {destination}"},"slight left":{default:"פנה קלות שמאלה",name:"פנה קלות שמאלה כדי להישאר על {way_name}",destination:"פנה קלות שמאלה לכיוון {destination}"},"slight right":{default:"פנה קלות ימינה",name:"פנה קלות ימינה כדי להישאר על {way_name}",destination:"פנה קלות ימינה לכיוון {destination}"},uturn:{default:"פנה פניית פרסה",name:"פנה פניית פרסה והמשך על {way_name}",destination:"פנה פניית פרסה לכיוון {destination}"}},depart:{default:{default:"התכוונן {direction}",name:"התכוונן {direction} על {way_name}",namedistance:"התכוונן {direction} על {way_name} לאורך {distance}"}},"end of road":{default:{default:"פנה {modifier}",name:"פנה {modifier} על {way_name}",destination:"פנה {modifier} לכיוון {destination}"},straight:{default:"המשך ישר",name:"המשך ישר על {way_name}",destination:"המשך ישר לכיוון {destination}"},uturn:{default:"פנה פניית פרסה בסוף הדרך",name:"פנה פניית פרסה על {way_name} בסוף הדרך",destination:"פנה פניית פרסה לכיוון {destination} בסוף הדרך"}},fork:{default:{default:"היצמד {modifier} בהתפצלות",name:"היצמד {modifier} על {way_name}",destination:"היצמד {modifier} לכיוון {destination}"},"slight left":{default:"היצמד לשמאל בהתפצלות",name:"היצמד לשמאל על {way_name}",destination:"היצמד לשמאל לכיוון {destination}"},"slight right":{default:"היצמד ימינה בהתפצלות",name:"היצמד לימין על {way_name}",destination:"היצמד לימין לכיוון {destination}"},"sharp left":{default:"פנה בחדות שמאלה בהתפצלות",name:"פנה בחדות שמאלה על {way_name}",destination:"פנה בחדות שמאלה לכיוון {destination}"},"sharp right":{default:"פנה בחדות ימינה בהתפצלות",name:"פנה בחדות ימינה על {way_name}",destination:"פנה בחדות ימינה לכיוון {destination}"},uturn:{default:"פנה פניית פרסה",name:"פנה פניית פרסה על {way_name}",destination:"פנה פניית פרסה לכיוון {destination}"}},merge:{default:{default:"השתלב {modifier}",name:"השתלב {modifier} על {way_name}",destination:"השתלב {modifier} לכיוון {destination}"},straight:{default:"השתלב",name:"השתלב על {way_name}",destination:"השתלב לכיוון {destination}"},"slight left":{default:"השתלב שמאלה",name:"השתלב שמאלה על {way_name}",destination:"השתלב שמאלה לכיוון {destination}"},"slight right":{default:"השתלב ימינה",name:"השתלב ימינה על {way_name}",destination:"השתלב ימינה לכיוון {destination}"},"sharp left":{default:"השתלב שמאלה",name:"השתלב שמאלה על {way_name}",destination:"השתלב שמאלה לכיוון {destination}"},"sharp right":{default:"השתלב ימינה",name:"השתלב ימינה על {way_name}",destination:"השתלב ימינה לכיוון {destination}"},uturn:{default:"פנה פניית פרסה",name:"פנה פניית פרסה על {way_name}",destination:"פנה פניית פרסה לכיוון {destination}"}},"new name":{default:{default:"המשך {modifier}",name:"המשך {modifier} על {way_name}",destination:"המשך {modifier} לכיוון {destination}"},straight:{default:"המשך ישר",name:"המשך על {way_name}",destination:"המשך לכיוון {destination}"},"sharp left":{default:"פנה בחדות שמאלה",name:"פנה בחדות שמאלה על {way_name}",destination:"פנה בחדות שמאלה לכיוון {destination}"},"sharp right":{default:"פנה בחדות ימינה",name:"פנה בחדות ימינה על {way_name}",destination:"פנה בחדות ימינה לכיוון {destination}"},"slight left":{default:"המשך בנטייה קלה שמאלה",name:"המשך בנטייה קלה שמאלה על {way_name}",destination:"המשך בנטייה קלה שמאלה לכיוון {destination}"},"slight right":{default:"המשך בנטייה קלה ימינה",name:"המשך בנטייה קלה ימינה על {way_name}",destination:"המשך בנטייה קלה ימינה לכיוון {destination}"},uturn:{default:"פנה פניית פרסה",name:"פנה פניית פרסה על {way_name}",destination:"פנה פניית פרסה לכיוון {destination}"}},notification:{default:{default:"המשך {modifier}",name:"המשך {modifier} על {way_name}",destination:"המשך {modifier} לכיוון {destination}"},uturn:{default:"פנה פניית פרסה",name:"פנה פניית פרסה על {way_name}",destination:"פנה פניית פרסה לכיוון {destination}"}},"off ramp":{default:{default:"צא ביציאה",name:"צא ביציאה על {way_name}",destination:"צא ביציאה לכיוון {destination}",exit:"צא ביציאה {exit}",exit_destination:"צא ביציאה {exit} לכיוון {destination}"},left:{default:"צא ביציאה שמשמאלך",name:"צא ביציאה שמשמאלך על {way_name}",destination:"צא ביציאה שמשמאלך לכיוון {destination}",exit:"צא ביציאה {exit} משמאלך",exit_destination:"צא ביציאה {exit} משמאלך לכיוון {destination}"},right:{default:"צא ביציאה שמימינך",name:"צא ביציאה שמימינך על {way_name}",destination:"צא ביציאה שמימינך לכיוון {destination}",exit:"צא ביציאה {exit} מימינך",exit_destination:"צא ביציאה {exit} מימינך לכיוון {destination}"},"sharp left":{default:"צא ביציאה שבשמאלך",name:"צא ביציאה שמשמאלך על {way_name}",destination:"צא ביציאה שמשמאלך לכיוון {destination}",exit:"צא ביציאה {exit} משמאלך",exit_destination:"צא ביציאה {exit} משמאלך לכיוון {destination}"},"sharp right":{default:"צא ביציאה שמימינך",name:"צא ביציאה שמימינך על {way_name}",destination:"צא ביציאה שמימינך לכיוון {destination}",exit:"צא ביציאה {exit} מימינך",exit_destination:"צא ביציאה {exit} מימינך לכיוון {destination}"},"slight left":{default:"צא ביציאה שבשמאלך",name:"צא ביציאה שמשמאלך על {way_name}",destination:"צא ביציאה שמשמאלך לכיוון {destination}",exit:"צא ביציאה {exit} משמאלך",exit_destination:"צא ביציאה {exit} משמאלך לכיוון {destination}"},"slight right":{default:"צא ביציאה שמימינך",name:"צא ביציאה שמימינך על {way_name}",destination:"צא ביציאה שמימינך לכיוון {destination}",exit:"צא ביציאה {exit} מימינך",exit_destination:"צא ביציאה {exit} מימינך לכיוון {destination}"}},"on ramp":{default:{default:"צא ביציאה",name:"צא ביציאה על {way_name}",destination:"צא ביציאה לכיוון {destination}"},left:{default:"צא ביציאה שבשמאלך",name:"צא ביציאה שמשמאלך על {way_name}",destination:"צא ביציאה שמשמאלך לכיוון {destination}"},right:{default:"צא ביציאה שמימינך",name:"צא ביציאה שמימינך על {way_name}",destination:"צא ביציאה שמימינך לכיוון {destination}"},"sharp left":{default:"צא ביציאה שבשמאלך",name:"צא ביציאה שמשמאלך על {way_name}",destination:"צא ביציאה שמשמאלך לכיוון {destination}"},"sharp right":{default:"צא ביציאה שמימינך",name:"צא ביציאה שמימינך על {way_name}",destination:"צא ביציאה שמימינך לכיוון {destination}"},"slight left":{default:"צא ביציאה שבשמאלך",name:"צא ביציאה שמשמאלך על {way_name}",destination:"צא ביציאה שמשמאלך לכיוון {destination}"},"slight right":{default:"צא ביציאה שמימינך",name:"צא ביציאה שמימינך על {way_name}",destination:"צא ביציאה שמימינך לכיוון {destination}"}},rotary:{default:{default:{default:"השתלב במעגל התנועה",name:"השתלב במעגל התנועה וצא על {way_name}",destination:"השתלב במעגל התנועה וצא לכיוון {destination}"},name:{default:"היכנס ל{rotary_name}",name:"היכנס ל{rotary_name} וצא על {way_name}",destination:"היכנס ל{rotary_name} וצא לכיוון {destination}"},exit:{default:"השתלב במעגל התנועה וצא ביציאה {exit_number}",name:"השתלב במעגל התנועה וצא ביציאה {exit_number} ל{way_name}",destination:"השתלב במעגל התנועה וצא ביציאה {exit_number} לכיוון {destination}"},name_exit:{default:"היכנס ל{rotary_name} וצא ביציאה ה{exit_number}",name:"היכנס ל{rotary_name} וצא ביציאה ה{exit_number} ל{way_name}",destination:"היכנס ל{rotary_name} וצא ביציאה ה{exit_number} לכיוון {destination}"}}},roundabout:{default:{exit:{default:"השתלב במעגל התנועה וצא ביציאה {exit_number}",name:"השתלב במעגל התנועה וצא ביציאה {exit_number} ל{way_name}",destination:"השתלב במעגל התנועה וצא ביציאה {exit_number} לכיוון {destination}"},default:{default:"השתלב במעגל התנועה",name:"השתלב במעגל התנועה וצא על {way_name}",destination:"השתלב במעגל התנועה וצא לכיוון {destination}"}}},"roundabout turn":{default:{default:"פנה {modifier}",name:"פנה {modifier} על {way_name}",destination:"פנה {modifier} לכיוון {destination}"},left:{default:"פנה שמאלה",name:"פנה שמאלה ל{way_name}",destination:"פנה שמאלה לכיוון {destination}"},right:{default:"פנה ימינה",name:"פנה ימינה ל{way_name}",destination:"פנה ימינה לכיוון {destination}"},straight:{default:"המשך ישר",name:"המשך ישר על {way_name}",destination:"המשך ישר לכיוון {destination}"}},"exit roundabout":{default:{default:"צא ממעגל התנועה",name:"צא ממעגל התנועה ל{way_name}",destination:"צא ממעגל התנועה לכיוון {destination}"}},"exit rotary":{default:{default:"צא ממעגל התנועה",name:"צא ממעגל התנועה ל{way_name}",destination:"צא ממעגל התנועה לכיוון {destination}"}},turn:{default:{default:"פנה {modifier}",name:"פנה {modifier} על {way_name}",destination:"פנה {modifier} לכיוון {destination}"},left:{default:"פנה שמאלה",name:"פנה שמאלה ל{way_name}",destination:"פנה שמאלה לכיוון {destination}"},right:{default:"פנה ימינה",name:"פנה ימינה ל{way_name}",destination:"פנה ימינה לכיוון {destination}"},straight:{default:"המשך ישר",name:"המשך ישר ל{way_name}",destination:"המשך ישר לכיוון {destination}"}},"use lane":{no_lanes:{default:"המשך ישר"},default:{default:"{lane_instruction}"}}}}},{}],32:[function(_dereq_,module,exports){module.exports={meta:{capitalizeFirstLetter:true},v5:{constants:{ordinalize:{1:"1",2:"2",3:"3",4:"4",5:"5",6:"6",7:"7",8:"8",9:"9",10:"10"},direction:{north:"utara",northeast:"timur laut",east:"timur",southeast:"tenggara",south:"selatan",southwest:"barat daya",west:"barat",northwest:"barat laut"},modifier:{left:"kiri",right:"kanan","sharp left":"tajam kiri","sharp right":"tajam kanan","slight left":"agak ke kiri","slight right":"agak ke kanan",straight:"lurus",uturn:"putar balik"},lanes:{xo:"Tetap di kanan",ox:"Tetap di kiri",xox:"Tetap di tengah",oxo:"Tetap di kiri atau kanan"}},modes:{ferry:{default:"Naik ferry",name:"Naik ferry di {way_name}",destination:"Naik ferry menuju {destination}"}},phrase:{"two linked by distance":"{instruction_one}, then, in {distance}, {instruction_two}","two linked":"{instruction_one}, then {instruction_two}","one in distance":"In {distance}, {instruction_one}","name and ref":"{name} ({ref})","exit with number":"exit {exit}"},arrive:{default:{default:"Anda telah tiba di tujuan ke-{nth}",upcoming:"Anda telah tiba di tujuan ke-{nth}",short:"Anda telah tiba di tujuan ke-{nth}","short-upcoming":"Anda telah tiba di tujuan ke-{nth}",named:"Anda telah tiba di {waypoint_name}"},left:{default:"Anda telah tiba di tujuan ke-{nth}, di sebelah kiri",upcoming:"Anda telah tiba di tujuan ke-{nth}, di sebelah kiri",short:"Anda telah tiba di tujuan ke-{nth}","short-upcoming":"Anda telah tiba di tujuan ke-{nth}",named:"Anda telah tiba di {waypoint_name}, di sebelah kiri"},right:{default:"Anda telah tiba di tujuan ke-{nth}, di sebelah kanan",upcoming:"Anda telah tiba di tujuan ke-{nth}, di sebelah kanan",short:"Anda telah tiba di tujuan ke-{nth}","short-upcoming":"Anda telah tiba di tujuan ke-{nth}",named:"Anda telah tiba di {waypoint_name}, di sebelah kanan"},"sharp left":{default:"Anda telah tiba di tujuan ke-{nth}, di sebelah kiri",upcoming:"Anda telah tiba di tujuan ke-{nth}, di sebelah kiri",short:"Anda telah tiba di tujuan ke-{nth}","short-upcoming":"Anda telah tiba di tujuan ke-{nth}",named:"Anda telah tiba di {waypoint_name}, di sebelah kiri"},"sharp right":{default:"Anda telah tiba di tujuan ke-{nth}, di sebelah kanan",upcoming:"Anda telah tiba di tujuan ke-{nth}, di sebelah kanan",short:"Anda telah tiba di tujuan ke-{nth}","short-upcoming":"Anda telah tiba di tujuan ke-{nth}",named:"Anda telah tiba di {waypoint_name}, di sebelah kanan"},"slight right":{default:"Anda telah tiba di tujuan ke-{nth}, di sebelah kanan",upcoming:"Anda telah tiba di tujuan ke-{nth}, di sebelah kanan",short:"Anda telah tiba di tujuan ke-{nth}","short-upcoming":"Anda telah tiba di tujuan ke-{nth}",named:"Anda telah tiba di {waypoint_name}, di sebelah kanan"},"slight left":{default:"Anda telah tiba di tujuan ke-{nth}, di sebelah kiri",upcoming:"Anda telah tiba di tujuan ke-{nth}, di sebelah kiri",short:"Anda telah tiba di tujuan ke-{nth}","short-upcoming":"Anda telah tiba di tujuan ke-{nth}",named:"Anda telah tiba di {waypoint_name}, di sebelah kiri"},straight:{default:"Anda telah tiba di tujuan ke-{nth}, lurus saja",upcoming:"Anda telah tiba di tujuan ke-{nth}, lurus saja",short:"Anda telah tiba di tujuan ke-{nth}","short-upcoming":"Anda telah tiba di tujuan ke-{nth}",named:"Anda telah tiba di {waypoint_name}, lurus saja"}},continue:{default:{default:"Belok {modifier}",name:"Terus {modifier} ke {way_name}",destination:"Belok {modifier} menuju {destination}",exit:"Belok {modifier} ke {way_name}"},straight:{default:"Lurus terus",name:"Terus ke {way_name}",destination:"Terus menuju {destination}",distance:"Continue straight for {distance}",namedistance:"Continue on {way_name} for {distance}"},"sharp left":{default:"Belok kiri tajam",name:"Make a sharp left to stay on {way_name}",destination:"Belok kiri tajam menuju {destination}"},"sharp right":{default:"Belok kanan tajam",name:"Make a sharp right to stay on {way_name}",destination:"Belok kanan tajam menuju {destination}"},"slight left":{default:"Tetap agak di kiri",name:"Tetap agak di kiri ke {way_name}",destination:"Tetap agak di kiri menuju {destination}"},"slight right":{default:"Tetap agak di kanan",name:"Tetap agak di kanan ke {way_name}",destination:"Tetap agak di kanan menuju {destination}"},uturn:{default:"Putar balik",name:"Putar balik ke arah {way_name}",destination:"Putar balik menuju {destination}"}},depart:{default:{default:"Arah {direction}",name:"Arah {direction} di {way_name}",namedistance:"Head {direction} on {way_name} for {distance}"}},"end of road":{default:{default:"Belok {modifier}",name:"Belok {modifier} ke {way_name}",destination:"Belok {modifier} menuju {destination}"},straight:{default:"Lurus terus",name:"Tetap lurus ke {way_name} ",destination:"Tetap lurus menuju {destination}"},uturn:{default:"Putar balik di akhir jalan",name:"Putar balik di {way_name} di akhir jalan",destination:"Putar balik menuju {destination} di akhir jalan"}},fork:{default:{default:"Tetap {modifier} di pertigaan",name:"Tetap {modifier} di pertigaan ke {way_name}",destination:"Tetap {modifier} di pertigaan menuju {destination}"},"slight left":{default:"Tetap di kiri pada pertigaan",name:"Tetap di kiri pada pertigaan ke arah {way_name}",destination:"Tetap di kiri pada pertigaan menuju {destination}"},"slight right":{default:"Tetap di kanan pada pertigaan",
name:"Tetap di kanan pada pertigaan ke arah {way_name}",destination:"Tetap di kanan pada pertigaan menuju {destination}"},"sharp left":{default:"Belok kiri pada pertigaan",name:"Belok kiri tajam ke arah {way_name}",destination:"Belok kiri tajam menuju {destination}"},"sharp right":{default:"Belok kanan pada pertigaan",name:"Belok kanan tajam ke arah {way_name}",destination:"Belok kanan tajam menuju {destination}"},uturn:{default:"Putar balik",name:"Putar balik ke arah {way_name}",destination:"Putar balik menuju {destination}"}},merge:{default:{default:"Bergabung {modifier}",name:"Bergabung {modifier} ke arah {way_name}",destination:"Bergabung {modifier} menuju {destination}"},straight:{default:"Bergabung lurus",name:"Bergabung lurus ke arah {way_name}",destination:"Bergabung lurus menuju {destination}"},"slight left":{default:"Bergabung di kiri",name:"Bergabung di kiri ke arah {way_name}",destination:"Bergabung di kiri menuju {destination}"},"slight right":{default:"Bergabung di kanan",name:"Bergabung di kanan ke arah {way_name}",destination:"Bergabung di kanan menuju {destination}"},"sharp left":{default:"Bergabung di kiri",name:"Bergabung di kiri ke arah {way_name}",destination:"Bergabung di kiri menuju {destination}"},"sharp right":{default:"Bergabung di kanan",name:"Bergabung di kanan ke arah {way_name}",destination:"Bergabung di kanan menuju {destination}"},uturn:{default:"Putar balik",name:"Putar balik ke arah {way_name}",destination:"Putar balik menuju {destination}"}},"new name":{default:{default:"Lanjutkan {modifier}",name:"Lanjutkan {modifier} menuju {way_name}",destination:"Lanjutkan {modifier} menuju {destination}"},straight:{default:"Lurus terus",name:"Terus ke {way_name}",destination:"Terus menuju {destination}"},"sharp left":{default:"Belok kiri tajam",name:"Belok kiri tajam ke arah {way_name}",destination:"Belok kiri tajam menuju {destination}"},"sharp right":{default:"Belok kanan tajam",name:"Belok kanan tajam ke arah {way_name}",destination:"Belok kanan tajam menuju {destination}"},"slight left":{default:"Lanjut dengan agak ke kiri",name:"Lanjut dengan agak di kiri ke {way_name}",destination:"Tetap agak di kiri menuju {destination}"},"slight right":{default:"Tetap agak di kanan",name:"Tetap agak di kanan ke {way_name}",destination:"Tetap agak di kanan menuju {destination}"},uturn:{default:"Putar balik",name:"Putar balik ke arah {way_name}",destination:"Putar balik menuju {destination}"}},notification:{default:{default:"Lanjutkan {modifier}",name:"Lanjutkan {modifier} menuju {way_name}",destination:"Lanjutkan {modifier} menuju {destination}"},uturn:{default:"Putar balik",name:"Putar balik ke arah {way_name}",destination:"Putar balik menuju {destination}"}},"off ramp":{default:{default:"Ambil jalan melandai",name:"Ambil jalan melandai ke {way_name}",destination:"Ambil jalan melandai menuju {destination}",exit:"Take exit {exit}",exit_destination:"Take exit {exit} towards {destination}"},left:{default:"Ambil jalan yang melandai di sebelah kiri",name:"Ambil jalan melandai di sebelah kiri ke arah {way_name}",destination:"Ambil jalan melandai di sebelah kiri menuju {destination}",exit:"Take exit {exit} on the left",exit_destination:"Take exit {exit} on the left towards {destination}"},right:{default:"Ambil jalan melandai di sebelah kanan",name:"Ambil jalan melandai di sebelah kanan ke {way_name}",destination:"Ambil jalan melandai di sebelah kanan menuju {destination}",exit:"Take exit {exit} on the right",exit_destination:"Take exit {exit} on the right towards {destination}"},"sharp left":{default:"Ambil jalan yang melandai di sebelah kiri",name:"Ambil jalan melandai di sebelah kiri ke arah {way_name}",destination:"Ambil jalan melandai di sebelah kiri menuju {destination}",exit:"Take exit {exit} on the left",exit_destination:"Take exit {exit} on the left towards {destination}"},"sharp right":{default:"Ambil jalan melandai di sebelah kanan",name:"Ambil jalan melandai di sebelah kanan ke {way_name}",destination:"Ambil jalan melandai di sebelah kanan menuju {destination}",exit:"Take exit {exit} on the right",exit_destination:"Take exit {exit} on the right towards {destination}"},"slight left":{default:"Ambil jalan yang melandai di sebelah kiri",name:"Ambil jalan melandai di sebelah kiri ke arah {way_name}",destination:"Ambil jalan melandai di sebelah kiri menuju {destination}",exit:"Take exit {exit} on the left",exit_destination:"Take exit {exit} on the left towards {destination}"},"slight right":{default:"Ambil jalan melandai di sebelah kanan",name:"Ambil jalan melandai di sebelah kanan ke {way_name}",destination:"Ambil jalan melandai di sebelah kanan  menuju {destination}",exit:"Take exit {exit} on the right",exit_destination:"Take exit {exit} on the right towards {destination}"}},"on ramp":{default:{default:"Ambil jalan melandai",name:"Ambil jalan melandai ke {way_name}",destination:"Ambil jalan melandai menuju {destination}"},left:{default:"Ambil jalan yang melandai di sebelah kiri",name:"Ambil jalan melandai di sebelah kiri ke arah {way_name}",destination:"Ambil jalan melandai di sebelah kiri menuju {destination}"},right:{default:"Ambil jalan melandai di sebelah kanan",name:"Ambil jalan melandai di sebelah kanan ke {way_name}",destination:"Ambil jalan melandai di sebelah kanan  menuju {destination}"},"sharp left":{default:"Ambil jalan yang melandai di sebelah kiri",name:"Ambil jalan melandai di sebelah kiri ke arah {way_name}",destination:"Ambil jalan melandai di sebelah kiri menuju {destination}"},"sharp right":{default:"Ambil jalan melandai di sebelah kanan",name:"Ambil jalan melandai di sebelah kanan ke {way_name}",destination:"Ambil jalan melandai di sebelah kanan  menuju {destination}"},"slight left":{default:"Ambil jalan yang melandai di sebelah kiri",name:"Ambil jalan melandai di sebelah kiri ke arah {way_name}",destination:"Ambil jalan melandai di sebelah kiri menuju {destination}"},"slight right":{default:"Ambil jalan melandai di sebelah kanan",name:"Ambil jalan melandai di sebelah kanan ke {way_name}",destination:"Ambil jalan melandai di sebelah kanan  menuju {destination}"}},rotary:{default:{default:{default:"Masuk bundaran",name:"Masuk bundaran dan keluar arah {way_name}",destination:"Masuk bundaran dan keluar menuju {destination}"},name:{default:"Masuk {rotary_name}",name:"Masuk {rotary_name} dan keluar arah {way_name}",destination:"Masuk {rotary_name} dan keluar menuju {destination}"},exit:{default:"Masuk bundaran dan ambil jalan keluar {exit_number}",name:"Masuk bundaran dan ambil jalan keluar {exit_number} arah {way_name}",destination:"Masuk bundaran dan ambil jalan keluar {exit_number} menuju {destination}"},name_exit:{default:"Masuk {rotary_name} dan ambil jalan keluar {exit_number}",name:"Masuk {rotary_name} dan ambil jalan keluar {exit_number} arah {way_name}",destination:"Masuk {rotary_name} dan ambil jalan keluar {exit_number} menuju {destination}"}}},roundabout:{default:{exit:{default:"Masuk bundaran dan ambil jalan keluar {exit_number}",name:"Masuk bundaran dan ambil jalan keluar {exit_number} arah {way_name}",destination:"Masuk bundaran dan ambil jalan keluar {exit_number} menuju {destination}"},default:{default:"Masuk bundaran",name:"Masuk bundaran dan keluar arah {way_name}",destination:"Masuk bundaran dan keluar menuju {destination}"}}},"roundabout turn":{default:{default:"Lakukan {modifier}",name:"Lakukan {modifier} ke arah {way_name}",destination:"Lakukan {modifier} menuju {destination}"},left:{default:"Belok kiri",name:"Belok kiri ke {way_name}",destination:"Belok kiri menuju {destination}"},right:{default:"Belok kanan",name:"Belok kanan ke {way_name}",destination:"Belok kanan menuju {destination}"},straight:{default:"Lurus terus",name:"Tetap lurus ke {way_name} ",destination:"Tetap lurus menuju {destination}"}},"exit roundabout":{default:{default:"Lakukan {modifier}",name:"Lakukan {modifier} ke arah {way_name}",destination:"Lakukan {modifier} menuju {destination}"},left:{default:"Belok kiri",name:"Belok kiri ke {way_name}",destination:"Belok kiri menuju {destination}"},right:{default:"Belok kanan",name:"Belok kanan ke {way_name}",destination:"Belok kanan menuju {destination}"},straight:{default:"Lurus terus",name:"Tetap lurus ke {way_name} ",destination:"Tetap lurus menuju {destination}"}},"exit rotary":{default:{default:"Lakukan {modifier}",name:"Lakukan {modifier} ke arah {way_name}",destination:"Lakukan {modifier} menuju {destination}"},left:{default:"Belok kiri",name:"Belok kiri ke {way_name}",destination:"Belok kiri menuju {destination}"},right:{default:"Belok kanan",name:"Belok kanan ke {way_name}",destination:"Belok kanan menuju {destination}"},straight:{default:"Lurus",name:"Lurus arah {way_name}",destination:"Lurus menuju {destination}"}},turn:{default:{default:"Lakukan {modifier}",name:"Lakukan {modifier} ke arah {way_name}",destination:"Lakukan {modifier} menuju {destination}"},left:{default:"Belok kiri",name:"Belok kiri ke {way_name}",destination:"Belok kiri menuju {destination}"},right:{default:"Belok kanan",name:"Belok kanan ke {way_name}",destination:"Belok kanan menuju {destination}"},straight:{default:"Lurus",name:"Lurus arah {way_name}",destination:"Lurus menuju {destination}"}},"use lane":{no_lanes:{default:"Lurus terus"},default:{default:"{lane_instruction}"}}}}},{}],33:[function(_dereq_,module,exports){module.exports={meta:{capitalizeFirstLetter:true},v5:{constants:{ordinalize:{1:"1ª",2:"2ª",3:"3ª",4:"4ª",5:"5ª",6:"6ª",7:"7ª",8:"8ª",9:"9ª",10:"10ª"},direction:{north:"nord",northeast:"nord-est",east:"est",southeast:"sud-est",south:"sud",southwest:"sud-ovest",west:"ovest",northwest:"nord-ovest"},modifier:{left:"sinistra",right:"destra","sharp left":"sinistra","sharp right":"destra","slight left":"sinistra leggermente","slight right":"destra leggermente",straight:"dritto",uturn:"inversione a U"},lanes:{xo:"Mantieni la destra",ox:"Mantieni la sinistra",xox:"Rimani in mezzo",oxo:"Mantieni la destra o la sinistra"}},modes:{ferry:{default:"Prendi il traghetto",name:"Prendi il traghetto {way_name}",destination:"Prendi il traghetto verso {destination}"}},phrase:{"two linked by distance":"{instruction_one}, poi tra {distance},{instruction_two}","two linked":"{instruction_one}, poi {instruction_two}","one in distance":"tra {distance} {instruction_one}","name and ref":"{name} ({ref})","exit with number":"exit {exit}"},arrive:{default:{default:"Sei arrivato alla tua {nth} destinazione",upcoming:"Sei arrivato alla tua {nth} destinazione",short:"Sei arrivato alla tua {nth} destinazione","short-upcoming":"Sei arrivato alla tua {nth} destinazione",named:"Sei arrivato a {waypoint_name}"},left:{default:"sei arrivato alla tua {nth} destinazione, sulla sinistra",upcoming:"sei arrivato alla tua {nth} destinazione, sulla sinistra",short:"Sei arrivato alla tua {nth} destinazione","short-upcoming":"Sei arrivato alla tua {nth} destinazione",named:"sei arrivato a {waypoint_name}, sulla sinistra"},right:{default:"sei arrivato alla tua {nth} destinazione, sulla destra",upcoming:"sei arrivato alla tua {nth} destinazione, sulla destra",short:"Sei arrivato alla tua {nth} destinazione","short-upcoming":"Sei arrivato alla tua {nth} destinazione",named:"sei arrivato a {waypoint_name}, sulla destra"},"sharp left":{default:"sei arrivato alla tua {nth} destinazione, sulla sinistra",upcoming:"sei arrivato alla tua {nth} destinazione, sulla sinistra",short:"Sei arrivato alla tua {nth} destinazione","short-upcoming":"Sei arrivato alla tua {nth} destinazione",named:"sei arrivato a {waypoint_name}, sulla sinistra"},"sharp right":{default:"sei arrivato alla tua {nth} destinazione, sulla destra",upcoming:"sei arrivato alla tua {nth} destinazione, sulla destra",short:"Sei arrivato alla tua {nth} destinazione","short-upcoming":"Sei arrivato alla tua {nth} destinazione",named:"sei arrivato a {waypoint_name}, sulla destra"},"slight right":{default:"sei arrivato alla tua {nth} destinazione, sulla destra",upcoming:"sei arrivato alla tua {nth} destinazione, sulla destra",short:"Sei arrivato alla tua {nth} destinazione","short-upcoming":"Sei arrivato alla tua {nth} destinazione",named:"sei arrivato a {waypoint_name}, sulla destra"},"slight left":{default:"sei arrivato alla tua {nth} destinazione, sulla sinistra",upcoming:"sei arrivato alla tua {nth} destinazione, sulla sinistra",short:"Sei arrivato alla tua {nth} destinazione","short-upcoming":"Sei arrivato alla tua {nth} destinazione",named:"sei arrivato a {waypoint_name}, sulla sinistra"},straight:{default:"sei arrivato alla tua {nth} destinazione, si trova davanti a te",upcoming:"sei arrivato alla tua {nth} destinazione, si trova davanti a te",short:"Sei arrivato alla tua {nth} destinazione","short-upcoming":"Sei arrivato alla tua {nth} destinazione",named:"sei arrivato a {waypoint_name}, si trova davanti a te"}},continue:{default:{default:"Gira a {modifier}",name:"Gira a {modifier} per stare su {way_name}",destination:"Gira a {modifier} verso {destination}",exit:"Gira a {modifier} in {way_name}"},straight:{default:"Continua dritto",name:"Continua dritto per stare su {way_name}",destination:"Continua verso {destination}",distance:"Continua dritto per {distance}",namedistance:"Continua su {way_name} per {distance}"},"sharp left":{default:"Svolta a sinistra",name:"Fai una stretta curva a sinistra per stare su {way_name}",destination:"Svolta a sinistra verso {destination}"},"sharp right":{default:"Svolta a destra",name:"Fau una stretta curva a destra per stare su {way_name}",destination:"Svolta a destra verso {destination}"},"slight left":{default:"Fai una leggera curva a sinistra",name:"Fai una leggera curva a sinistra per stare su {way_name}",destination:"Fai una leggera curva a sinistra verso {destination}"},"slight right":{default:"Fai una leggera curva a destra",name:"Fai una leggera curva a destra per stare su {way_name}",destination:"Fai una leggera curva a destra verso {destination}"},uturn:{default:"Fai un'inversione a U",name:"Fai un'inversione ad U poi continua su {way_name}",destination:"Fai un'inversione a U verso {destination}"}},depart:{default:{default:"Continua verso {direction}",name:"Continua verso {direction} in {way_name}",namedistance:"Head {direction} on {way_name} for {distance}"}},"end of road":{default:{default:"Gira a {modifier}",name:"Gira a {modifier} in {way_name}",destination:"Gira a {modifier} verso {destination}"},straight:{default:"Continua dritto",name:"Continua dritto in {way_name}",destination:"Continua dritto verso {destination}"},uturn:{default:"Fai un'inversione a U alla fine della strada",name:"Fai un'inversione a U in {way_name} alla fine della strada",destination:"Fai un'inversione a U verso {destination} alla fine della strada"}},fork:{default:{default:"Mantieni la {modifier} al bivio",name:"Mantieni la {modifier} al bivio in {way_name}",destination:"Mantieni la {modifier} al bivio verso {destination}"},"slight left":{default:"Mantieni la sinistra al bivio",name:"Mantieni la sinistra al bivio in {way_name}",destination:"Mantieni la sinistra al bivio verso {destination}"},"slight right":{default:"Mantieni la destra al bivio",name:"Mantieni la destra al bivio in {way_name}",destination:"Mantieni la destra al bivio verso {destination}"},"sharp left":{default:"Svolta a sinistra al bivio",name:"Svolta a sinistra in {way_name}",destination:"Svolta a sinistra verso {destination}"},"sharp right":{default:"Svolta a destra al bivio",name:"Svolta a destra in {way_name}",destination:"Svolta a destra verso {destination}"},uturn:{default:"Fai un'inversione a U",name:"Fai un'inversione a U in {way_name}",destination:"Fai un'inversione a U verso {destination}"}},merge:{default:{default:"Immettiti a {modifier}",name:"Immettiti {modifier} in {way_name}",destination:"Immettiti {modifier} verso {destination}"},straight:{default:"Immettiti a dritto",name:"Immettiti dritto in {way_name}",destination:"Immettiti dritto verso {destination}"},"slight left":{default:"Immettiti a sinistra",name:"Immettiti a sinistra in {way_name}",destination:"Immettiti a sinistra verso {destination}"},"slight right":{default:"Immettiti a destra",name:"Immettiti a destra in {way_name}",destination:"Immettiti a destra verso {destination}"},"sharp left":{default:"Immettiti a sinistra",name:"Immettiti a sinistra in {way_name}",destination:"Immettiti a sinistra verso {destination}"},"sharp right":{default:"Immettiti a destra",name:"Immettiti a destra in {way_name}",destination:"Immettiti a destra verso {destination}"},uturn:{default:"Fai un'inversione a U",name:"Fai un'inversione a U in {way_name}",destination:"Fai un'inversione a U verso {destination}"}},"new name":{default:{default:"Continua a {modifier}",name:"Continua a {modifier} in {way_name}",destination:"Continua a {modifier} verso {destination}"},straight:{default:"Continua dritto",name:"Continua in {way_name}",destination:"Continua verso {destination}"},"sharp left":{default:"Svolta a sinistra",name:"Svolta a sinistra in {way_name}",destination:"Svolta a sinistra verso {destination}"},"sharp right":{default:"Svolta a destra",name:"Svolta a destra in {way_name}",destination:"Svolta a destra verso {destination}"},"slight left":{default:"Continua leggermente a sinistra",name:"Continua leggermente a sinistra in {way_name}",destination:"Continua leggermente a sinistra verso {destination}"},"slight right":{default:"Continua leggermente a destra",name:"Continua leggermente a destra in {way_name} ",destination:"Continua leggermente a destra verso {destination}"},uturn:{default:"Fai un'inversione a U",name:"Fai un'inversione a U in {way_name}",destination:"Fai un'inversione a U verso {destination}"}},notification:{default:{default:"Continua a {modifier}",name:"Continua a {modifier} in {way_name}",destination:"Continua a {modifier} verso {destination}"},uturn:{default:"Fai un'inversione a U",name:"Fai un'inversione a U in {way_name}",destination:"Fai un'inversione a U verso {destination}"}},"off ramp":{default:{default:"Prendi la rampa",name:"Prendi la rampa in {way_name}",destination:"Prendi la rampa verso {destination}",exit:"Prendi l'uscita {exit}",exit_destination:"Prendi l'uscita  {exit} verso {destination}"},left:{default:"Prendi la rampa a sinistra",name:"Prendi la rampa a sinistra in {way_name}",destination:"Prendi la rampa a sinistra verso {destination}",exit:"Prendi l'uscita {exit} a sinistra",exit_destination:"Prendi la {exit}  uscita a sinistra verso {destination}"},right:{default:"Prendi la rampa a destra",name:"Prendi la rampa a destra in {way_name}",destination:"Prendi la rampa a destra verso {destination}",exit:"Prendi la {exit} uscita a destra",exit_destination:"Prendi la {exit} uscita a destra verso {destination}"},"sharp left":{default:"Prendi la rampa a sinistra",name:"Prendi la rampa a sinistra in {way_name}",destination:"Prendi la rampa a sinistra verso {destination}",exit:"Prendi l'uscita {exit} a sinistra",exit_destination:"Prendi la {exit}  uscita a sinistra verso {destination}"},"sharp right":{default:"Prendi la rampa a destra",name:"Prendi la rampa a destra in {way_name}",destination:"Prendi la rampa a destra verso {destination}",exit:"Prendi la {exit} uscita a destra",exit_destination:"Prendi la {exit} uscita a destra verso {destination}"},"slight left":{default:"Prendi la rampa a sinistra",name:"Prendi la rampa a sinistra in {way_name}",destination:"Prendi la rampa a sinistra verso {destination}",exit:"Prendi l'uscita {exit} a sinistra",exit_destination:"Prendi la {exit}  uscita a sinistra verso {destination}"},"slight right":{default:"Prendi la rampa a destra",name:"Prendi la rampa a destra in {way_name}",destination:"Prendi la rampa a destra verso {destination}",exit:"Prendi la {exit} uscita a destra",exit_destination:"Prendi la {exit} uscita a destra verso {destination}"}},"on ramp":{default:{default:"Prendi la rampa",name:"Prendi la rampa in {way_name}",destination:"Prendi la rampa verso {destination}"},left:{default:"Prendi la rampa a sinistra",name:"Prendi la rampa a sinistra in {way_name}",destination:"Prendi la rampa a sinistra verso {destination}"},right:{default:"Prendi la rampa a destra",name:"Prendi la rampa a destra in {way_name}",destination:"Prendi la rampa a destra verso {destination}"},"sharp left":{default:"Prendi la rampa a sinistra",name:"Prendi la rampa a sinistra in {way_name}",destination:"Prendi la rampa a sinistra verso {destination}"},"sharp right":{default:"Prendi la rampa a destra",name:"Prendi la rampa a destra in {way_name}",destination:"Prendi la rampa a destra verso {destination}"},"slight left":{default:"Prendi la rampa a sinistra",name:"Prendi la rampa a sinistra in {way_name}",destination:"Prendi la rampa a sinistra verso {destination}"},"slight right":{default:"Prendi la rampa a destra",name:"Prendi la rampa a destra in {way_name}",destination:"Prendi la rampa a destra verso {destination}"}},rotary:{default:{default:{default:"Immettiti nella rotonda",name:"Immettiti nella ritonda ed esci in {way_name}",destination:"Immettiti nella ritonda ed esci verso {destination}"},name:{default:"Immettiti in {rotary_name}",name:"Immettiti in {rotary_name} ed esci su {way_name}",destination:"Immettiti in {rotary_name} ed esci verso {destination}"},exit:{default:"Immettiti nella rotonda e prendi la {exit_number} uscita",name:"Immettiti nella rotonda e prendi la {exit_number} uscita in {way_name}",destination:"Immettiti nella rotonda e prendi la {exit_number} uscita verso   {destination}"},name_exit:{default:"Immettiti in {rotary_name} e prendi la {exit_number} uscita",name:"Immettiti in {rotary_name} e prendi la {exit_number} uscita in {way_name}",destination:"Immettiti in {rotary_name} e prendi la {exit_number}  uscita verso {destination}"}}},roundabout:{default:{exit:{default:"Immettiti nella rotonda e prendi la {exit_number} uscita",name:"Immettiti nella rotonda e prendi la {exit_number} uscita in {way_name}",destination:"Immettiti nella rotonda e prendi la {exit_number} uscita verso {destination}"},default:{default:"Entra nella rotonda",name:"Entra nella rotonda e prendi l'uscita in {way_name}",destination:"Entra nella rotonda e prendi l'uscita verso {destination}"}}},"roundabout turn":{default:{default:"Fai una {modifier}",name:"Fai una {modifier} in {way_name}",destination:"Fai una {modifier} verso {destination}"},left:{default:"Svolta a sinistra",name:"Svolta a sinistra in {way_name}",destination:"Svolta a sinistra verso {destination}"},right:{default:"Gira a destra",name:"Svolta a destra in {way_name}",destination:"Svolta a destra verso {destination}"},straight:{default:"Continua dritto",name:"Continua dritto in {way_name}",destination:"Continua dritto verso {destination}"}},"exit roundabout":{default:{default:"Fai una {modifier}",name:"Fai una {modifier} in {way_name}",destination:"Fai una {modifier} verso {destination}"},left:{default:"Svolta a sinistra",name:"Svolta a sinistra in {way_name}",destination:"Svolta a sinistra verso {destination}"},right:{default:"Gira a destra",name:"Svolta a destra in {way_name}",destination:"Svolta a destra verso {destination}"},straight:{default:"Continua dritto",name:"Continua dritto in {way_name}",destination:"Continua dritto verso {destination}"}},"exit rotary":{default:{default:"Fai una {modifier}",name:"Fai una {modifier} in {way_name}",destination:"Fai una {modifier} verso {destination}"},left:{default:"Svolta a sinistra",name:"Svolta a sinistra in {way_name}",destination:"Svolta a sinistra verso {destination}"},right:{default:"Gira a destra",name:"Svolta a destra in {way_name}",destination:"Svolta a destra verso {destination}"},straight:{default:"Prosegui dritto",name:"Continua su {way_name}",destination:"Continua verso {destination}"}},turn:{default:{default:"Fai una {modifier}",name:"Fai una {modifier} in {way_name}",destination:"Fai una {modifier} verso {destination}"},left:{default:"Svolta a sinistra",name:"Svolta a sinistra in {way_name}",destination:"Svolta a sinistra verso {destination}"},right:{default:"Gira a destra",name:"Svolta a destra in {way_name}",destination:"Svolta a destra verso {destination}"},straight:{default:"Prosegui dritto",name:"Continua su {way_name}",destination:"Continua verso {destination}"}},"use lane":{no_lanes:{default:"Continua dritto"},default:{default:"{lane_instruction}"}}}}},{}],34:[function(_dereq_,module,exports){module.exports={meta:{capitalizeFirstLetter:false},v5:{constants:{ordinalize:{1:"첫번쩨",2:"두번째",3:"세번째",4:"네번쩨",5:"다섯번째",6:"여섯번째",7:"일곱번째",8:"여덟번째",9:"아홉번째",10:"열번째"},direction:{north:"북쪽",northeast:"북동쪽",east:"동쪽",southeast:"남동쪽",south:"남쪽",southwest:"남서쪽",west:"서쪽",northwest:"북서쪽"},modifier:{left:"좌회전",right:"우회전","sharp left":"바로좌회전","sharp right":"바로우회전","slight left":"조금왼쪽","slight right":"조금오른쪽",straight:"직진",uturn:"유턴"},lanes:{xo:"우측차선 유지",ox:"좌측차선 유지",xox:"중앙유지",oxo:"계속 좌측 또는 우측 차선"}},modes:{ferry:{default:"페리를 타시오",name:"페리를 타시오 {way_name}",destination:"페리를 타고 {destination}까지 가세요."}},phrase:{"two linked by distance":"{instruction_one}, 그리고, {distance} 안에, {instruction_two}","two linked":"{instruction_one}, 그리고 {instruction_two}","one in distance":"{distance} 내에, {instruction_one}","name and ref":"{name} ({ref})","exit with number":"{exit}번으로 나가세요."},arrive:{default:{default:" {nth}목적지에 도착하였습니다.",upcoming:"{nth}목적지에 곧 도착할 예정입니다.",short:"도착하였습니다","short-upcoming":"도착할 예정입니다.",named:"경유지 {waypoint_name}에 도착하였습니다."},left:{default:"좌측에 {nth} 목적지가 있습니다.",upcoming:"좌측에 {nth} 목적지에 도착할 예정입니다.",short:"도착하였습니다","short-upcoming":"목적지에 곧 도착할 예정입니다.",named:"좌측에 경유지 {waypoint_name}에 도착하였습니다."},right:{default:"우측에 {nth} 목적지가 있습니다.",upcoming:"우측에 {nth} 목적지에 도착할 예정입니다.",short:"도착하였습니다","short-upcoming":"목적지에 곧 도착할 예정입니다.",named:"우측에 경유지 {waypoint_name}에 도착하였습니다."},"sharp left":{default:"좌측에 {nth} 목적지가 있습니다.",upcoming:"좌측에 {nth} 목적지에 도착할 예정입니다.",short:"도착하였습니다","short-upcoming":"목적지에 곧 도착할 예정입니다.",named:"좌측에 경유지 {waypoint_name}에 도착하였습니다."},"sharp right":{default:"우측에 {nth} 목적지가 있습니다.",upcoming:"우측에 {nth} 목적지에 도착할 예정입니다.",short:"도착하였습니다","short-upcoming":"목적지에 곧 도착할 예정입니다.",named:"우측에 경유지 {waypoint_name}에 도착하였습니다."},"slight right":{default:"우측에 {nth} 목적지가 있습니다.",upcoming:"우측에 {nth} 목적지에 도착할 예정입니다.",short:"도착하였습니다","short-upcoming":"목적지에 곧 도착할 예정입니다.",named:"우측에 경유지 {waypoint_name}에 도착하였습니다."},"slight left":{default:"좌측에 {nth} 목적지가 있습니다.",upcoming:"좌측에 {nth} 목적지에 도착할 예정입니다.",short:"도착하였습니다","short-upcoming":"목적지에 곧 도착할 예정입니다.",named:"좌측에 경유지 {waypoint_name}에 도착하였습니다."},straight:{default:"바로 앞에 {nth} 목적지가 있습니다.",upcoming:"직진하시면 {nth} 목적지에 도착할 예정입니다.",short:"도착하였습니다","short-upcoming":"목적지에 곧 도착할 예정입니다.",named:"정면에 경유지 {waypoint_name}에 도착하였습니다."}},continue:{default:{default:"{modifier} 회전",name:"{modifier} 회전하고 {way_name}로 직진해 주세요.",destination:"{modifier} 회전하고 {destination}까지 가세요.",exit:"{way_name} 쪽으로 {modifier} 회전 하세요."},straight:{default:"계속 직진해 주세요.",name:"{way_name} 로 계속 직진해 주세요.",destination:"{destination}까지 직진해 주세요.",distance:"{distance}까지 직진해 주세요.",namedistance:"{distance}까지 {way_name}로 가주세요."},"sharp left":{default:"급좌회전 하세요.",name:"급좌회전 하신 후 {way_name}로 가세요.",destination:"급좌회전 하신 후 {destination}로 가세요."},"sharp right":{default:"급우회전 하세요.",name:"급우회전 하고 {way_name}로 가세요.",destination:"급우회전 하신 후 {destination}로 가세요."},"slight left":{default:"약간 좌회전하세요.",name:"약간 좌회전 하고 {way_name}로 가세요.",destination:"약간 좌회전 하신 후 {destination}로 가세요."},"slight right":{default:"약간 우회전하세요.",name:"약간 우회전 하고 {way_name}로 가세요.",destination:"약간 우회전 하신 후 {destination}로 가세요."},uturn:{default:"유턴 하세요",name:"유턴해서 {way_name}로 가세요.",destination:"유턴하신 후 {destination}로 가세요."}},depart:{default:{default:"{direction}로 가세요",name:"{direction} 로 가서 {way_name} 를 이용하세요. ",namedistance:"{direction}로 가서{way_name} 를 {distance}까지 가세요."}},"end of road":{default:{default:"{modifier} 회전하세요.",name:"{modifier}회전하고 {way_name}로 가세요.",destination:"{modifier}회전 하신 후 {destination}로 가세요."},straight:{default:"계속 직진해 주세요.",name:"{way_name}로 계속 직진해 주세요.",destination:"{destination}까지 직진해 주세요."},uturn:{default:"도로 끝까지 가서 유턴해 주세요.",name:"도로 끝까지 가서 유턴해서 {way_name}로 가세요.",destination:"도로 끝까지 가서 유턴해서 {destination} 까지 가세요."}},fork:{default:{default:"갈림길에서 {modifier} 으로 가세요.",name:"{modifier}하고 {way_name}로 가세요.",destination:"{modifier}하고 {destination}까지 가세요."},"slight left":{default:"갈림길에서 좌회전 하세요.",name:"좌회전 해서 {way_name}로 가세요.",destination:"좌회전 해서 {destination}까지 가세요."},"slight right":{default:"갈림길에서 우회전 하세요.",name:"우회전 해서 {way_name}로 가세요.",destination:"우회전 해서 {destination}까지 가세요."},"sharp left":{default:"갈림길에서 급좌회전 하세요.",name:"급좌회전 해서 {way_name}로 가세요.",destination:"급좌회전 해서 {destination}까지 가세요."},"sharp right":{default:"갈림길에서 급우회전 하세요.",name:"급우회전 해서 {way_name}로 가세요.",destination:"급우회전 해서 {destination}까지 가세요."},uturn:{default:"유턴하세요.",name:"유턴해서 {way_name}로 가세요.",destination:"유턴해서 {destination}까지 가세요."}},merge:{default:{default:"{modifier} 합류",name:"{modifier} 합류하여 {way_name}로 가세요.",destination:"{modifier} 합류하여 {destination}로 가세요."},straight:{default:"합류",name:"{way_name}로 합류하세요.",destination:"{destination}로 합류하세요."},"slight left":{default:"좌측으로 합류하세요.",name:"좌측{way_name}로 합류하세요.",destination:"좌측으로 합류하여 {destination}까지 가세요."},"slight right":{default:"우측으로 합류하세요.",name:"우측{way_name}로 합류하세요.",destination:"우측으로 합류하여 {destination}까지 가세요."},"sharp left":{default:"좌측으로 합류하세요.",name:"좌측{way_name}로 합류하세요.",destination:"좌측으로 합류하여 {destination}까지 가세요."},"sharp right":{default:"우측으로 합류하세요.",name:"우측{way_name}로 합류하세요.",destination:"우측으로 합류하여 {destination}까지 가세요."},uturn:{default:"유턴하세요.",name:"유턴해서 {way_name}로 가세요.",destination:"유턴해서 {destination}까지 가세요."}},"new name":{default:{default:"{modifier} 유지하세요.",name:"{modifier} 유지해서 {way_name}로 가세요.",destination:"{modifier} 유지해서 {destination}까지 가세요."},straight:{default:"직진해주세요.",name:"{way_name}로 계속 가세요.",destination:"{destination}까지 계속 가세요."},"sharp left":{default:"급좌회전 하세요.",name:"급좌회전 해서 {way_name}로 가세요.",destination:"급좌회전 해서 {destination}까지 가세요."},"sharp right":{default:"급우회전 하세요.",name:"급우회전 해서 {way_name}로 가세요.",destination:"급우회전 해서 {destination}까지 가세요."},"slight left":{default:"약간 좌회전 해세요.",name:"약간 좌회전해서 {way_name}로 가세요.",destination:"약간 좌회전 해서 {destination}까지 가세요."},"slight right":{default:"약간 우회전 해세요.",name:"약간 우회전해서 {way_name}로 가세요.",destination:"약간 우회전 해서 {destination}까지 가세요."},uturn:{default:"유턴해주세요.",name:"유턴해서 {way_name}로 가세요.",destination:"유턴해서 {destination}까지 가세요."}},notification:{default:{default:"{modifier} 하세요.",name:"{modifier}해서 {way_name}로 가세요.",destination:"{modifier}해서 {destination}까지 가세요."},uturn:{default:"유턴하세요.",name:"유턴해서 {way_name}로 가세요.",destination:"유턴해서 {destination}까지 가세요."}},"off ramp":{default:{default:"램프로 진출해 주세요..",name:"램프로 진출해서 {way_name}로 가세요.",destination:"램프로 진출해서 {destination}까지 가세요.",exit:"{exit} 출구로 나가세요.",exit_destination:"{exit} 출구로 나가서 {destination}까지 가세요."},left:{default:"왼쪽의 램프로 진출해 주세요.",name:"왼쪽의 램프로 진출해서 {way_name}로 가세요.",destination:"왼쪽의 램프로 진출해서 {destination}까지 가세요.",exit:"{exit} 왼쪽의 출구로 나가세요.",exit_destination:"{exit} 왼쪽의 출구로 가나서 {destination}까지 가세요."},right:{default:"오른쪽의 램프로 진출해 주세요.",name:"오른쪽의 램프로 진출해서 {way_name}로 가세요.",destination:"오른쪽의 램프로 진출해서 {destination}까지 가세요.",exit:"{exit} 오른쪽의 출구로 나가세요.",exit_destination:"{exit} 오른쪽의 출구로 가나서 {destination}까지 가세요."},"sharp left":{default:"왼쪽의 램프로 진출해 주세요.",name:"왼쪽의 램프로 진출해서 {way_name}로 가세요.",destination:"왼쪽의 램프로 진출해서 {destination}까지 가세요.",exit:"{exit} 왼쪽의 출구로 나가세요.",exit_destination:"{exit} 왼쪽의 출구로 가나서 {destination}까지 가세요."},"sharp right":{default:"오른쪽의 램프로 진출해 주세요.",name:"오른쪽의 램프로 진출해서 {way_name}로 가세요.",destination:"오른쪽의 램프로 진출해서 {destination}까지 가세요.",exit:"{exit} 오른쪽의 출구로 나가세요.",exit_destination:"{exit} 오른쪽의 출구로 가나서 {destination}까지 가세요."},"slight left":{default:"왼쪽의 램프로 진출해 주세요.",name:"왼쪽의 램프로 진출해서 {way_name}로 가세요.",destination:"왼쪽의 램프로 진출해서 {destination}까지 가세요.",exit:"{exit} 왼쪽의 출구로 나가세요.",exit_destination:"{exit} 왼쪽의 출구로 가나서 {destination}까지 가세요."},"slight right":{default:"오른쪽의 램프로 진출해 주세요.",name:"오른쪽의 램프로 진출해서 {way_name}로 가세요.",destination:"오른쪽의 램프로 진출해서 {destination}까지 가세요.",exit:"{exit} 오른쪽의 출구로 나가세요.",exit_destination:"{exit} 오른쪽의 출구로 가나서 {destination}까지 가세요."}
},"on ramp":{default:{default:"램프로 진입해 주세요..",name:"램프로 진입해서 {way_name}로 가세요.",destination:"램프로 진입해서 {destination}까지 가세요."},left:{default:"왼쪽의 램프로 진입해 주세요.",name:"왼쪽의 램프로 진입해서 {way_name}로 가세요.",destination:"왼쪽의 램프로 진입해서 {destination}까지 가세요."},right:{default:"오른쪽의 램프로 진입해 주세요.",name:"오른쪽의 램프로 진입해서 {way_name}로 가세요.",destination:"오른쪽의 램프로 진입해서 {destination}까지 가세요."},"sharp left":{default:"왼쪽의 램프로 진입해 주세요.",name:"왼쪽의 램프로 진입해서 {way_name}로 가세요.",destination:"왼쪽의 램프로 진입해서 {destination}까지 가세요."},"sharp right":{default:"오른쪽의 램프로 진입해 주세요.",name:"오른쪽의 램프로 진입해서 {way_name}로 가세요.",destination:"오른쪽의 램프로 진입해서 {destination}까지 가세요."},"slight left":{default:"왼쪽의 램프로 진입해 주세요.",name:"왼쪽의 램프로 진입해서 {way_name}로 가세요.",destination:"왼쪽의 램프로 진입해서 {destination}까지 가세요."},"slight right":{default:"오른쪽의 램프로 진입해 주세요.",name:"오른쪽의 램프로 진입해서 {way_name}로 가세요.",destination:"오른쪽의 램프로 진입해서 {destination}까지 가세요."}},rotary:{default:{default:{default:"로터리로 진입하세요.",name:"로터리로 진입해서 {way_name} 나가세요.",destination:"로터리로 진입해서 {destination}로 나가세요."},name:{default:"{rotary_name}로 진입하세요.",name:"{rotary_name}로 진입해서 {way_name}로 나가세요.",destination:"{rotary_name}로 진입해서 {destination}로 나가세요."},exit:{default:"로터리로 진입해서 {exit_number} 출구로 나가세요.",name:"로터리로 진입해서 {exit_number} 출구로 나가 {way_name}로 가세요.",destination:"로터리로 진입해서 {exit_number} 출구로 나가 {destination}로 가세요."},name_exit:{default:"{rotary_name}로 진입해서 {exit_number}번 출구로 나가세요.",name:"{rotary_name}로 진입해서 {exit_number}번 출구로 나가 {way_name}로 가세요.",destination:"{rotary_name}로 진입해서 {exit_number}번 출구로 나가 {destination}로 가세요."}}},roundabout:{default:{exit:{default:"로터리로 진입해서 {exit_number}로 나가세요.",name:"로터리로 진입해서 {exit_number}로 나가서 {way_name}로 가세요.",destination:"로터리로 진입해서 {exit_number}로 나가서 {destination}로 가세요."},default:{default:"로터리로 진입하세요.",name:"로터리로 진입해서 {way_name} 나가세요.",destination:"로터리로 진입해서 {destination}로 나가세요."}}},"roundabout turn":{default:{default:"{modifier} 하세요.",name:"{modifier} 하시고 {way_name}로 가세요.",destination:"{modifier} 하시고 {destination}까지 가세요."},left:{default:"좌회전 하세요.",name:"좌회전 하시고 {way_name}로 가세요.",destination:"좌회전 하시고 {destination}까지 가세요."},right:{default:"우회전 하세요.",name:"우회전 하시고 {way_name}로 가세요.",destination:"우회전 하시고 {destination}까지 가세요."},straight:{default:"직진 하세요.",name:"직진하시고 {way_name}로 가세요.",destination:"직진하시고 {destination}까지 가세요."}},"exit roundabout":{default:{default:"로타리에서 진출하세요.",name:"로타리에서 진출해서 {way_name}로 가세요.",destination:"로타리에서 진출해서 {destination}까지 가세요."}},"exit rotary":{default:{default:"로타리에서 진출하세요.",name:"로타리에서 진출해서 {way_name}로 가세요.",destination:"로타리에서 진출해서 {destination}까지 가세요."}},turn:{default:{default:"{modifier} 하세요.",name:"{modifier} 하시고 {way_name}로 가세요.",destination:"{modifier} 하시고 {destination}까지 가세요."},left:{default:"좌회전 하세요.",name:"좌회전 하시고 {way_name}로 가세요.",destination:"좌회전 하시고 {destination}까지 가세요."},right:{default:"우회전 하세요.",name:"우회전 하시고 {way_name}로 가세요.",destination:"우회전 하시고 {destination}까지 가세요."},straight:{default:"직진 하세요.",name:"직진하시고 {way_name}로 가세요.",destination:"직진하시고 {destination}까지 가세요."}},"use lane":{no_lanes:{default:"직진하세요."},default:{default:"{lane_instruction}"}}}}},{}],35:[function(_dereq_,module,exports){module.exports={meta:{capitalizeFirstLetter:false},v5:{constants:{ordinalize:{1:"ပထမ",2:"ဒုတိယ",3:"တတိယ",4:"စတုတၳ",5:"ပဥၥမ",6:"ဆဌမ",7:"သတၱမ",8:"အဌမ",9:"နဝမ",10:"ဒသမ"},direction:{north:"ေျမာက္အရပ္",northeast:"အေရွ႕ေျမာက္အရပ္",east:"အေရွ႕အရပ္",southeast:"အေရွ႕ေတာင္အရပ္",south:"ေတာင္အရပ္",southwest:"အေနာက္ေတာင္အရပ္",west:"အေနာက္အရပ္",northwest:"အေနာက္ေျမာက္အရပ္"},modifier:{left:"ဘယ္ဘက္",right:"ညာဘက္","sharp left":"ဘယ္ဘက္ ေထာင့္ခ်ိဳး","sharp right":"ညာဘက္ ေထာင္႔ခ်ိဳး","slight left":"ဘယ္ဘက္ အနည္းငယ္","slight right":"ညာဘက္ အနည္းငယ္",straight:"ေျဖာင္႔ေျဖာင္႔တန္းတန္း",uturn:"ဂ-ေကြ႔"},lanes:{xo:"ညာဘက္သို႕ဆက္သြားပါ",ox:"ဘယ္ဘက္သို႕ဆက္သြားပါ",xox:"အလယ္တြင္ဆက္ေနပါ",oxo:"ဘယ္ သို႕မဟုတ္ ညာဘက္သို႕ ဆက္သြားပါ"}},modes:{ferry:{default:"ဖယ္ရီ စီးသြားပါ",name:"{way_name}ကို ဖယ္ရီစီးသြားပါ",destination:"{destination}ဆီသို႕ ဖယ္ရီစီးသြားပါ"}},phrase:{"two linked by distance":"{instruction_one}ျပီးေနာက္ {distance}အတြင္း {instruction_two}","two linked":"{instruction_one}ျပီးေနာက္ {instruction_two}","one in distance":"{distance}အတြင္း {instruction_one}","name and ref":"{name}( {ref})","exit with number":"{exit}မွထြက္ပါ"},arrive:{default:{default:"{nth}သင္ သြားလိုေသာ ခရီးပန္းတိုင္သို႕ေရာက္ရွိျပီ",upcoming:"သင္ သြားလိုေသာ {nth}ခရီးပန္းတိုင္သို႕ေရာက္လိမ့္မည္",short:"သင္သြားလိုေသာ ေနရာသို႔ ေရာက္ရိွၿပီ","short-upcoming":"သင္သြားလိုေသာ ေနရာသို႔ ေရာက္လိမ့္မည္",named:"သင္ သည္ {waypoint_name} မွာ ေရာက္ရွိျပီ"},left:{default:"သင္ သြားလိုေသာ {nth}ခရီးပန္းတိုင္သို႕ဘယ္ဘက္တြင္ေရာက္ရွိျပီ",upcoming:"သင္ သြားလိုေသာ {nth}ခရီးပန္းတိုင္သို႕ဘယ္ဘက္တြင္ေရာက္လိမ့္မည္",short:"သင္သြားလိုေသာ ေနရာသို႔ ေရာက္ရိွၿပီ","short-upcoming":"သင္သြားလိုေသာ ေနရာသို႔ ေရာက္လိမ့္မည္",named:"သင္ သည္ {waypoint_name}မွာဘယ္ဘက္ေကြ႕ကာ ေရာက္ရွိျပီ"},right:{default:"သင္ သြားလိုေသာ {nth}ခရီးပန္းတိုင္သို႕ ညာဘက္ေကြ႕ကာ ေရာက္ရွိျပီ",upcoming:"သင္ သြားလိုေသာ{nth} ခရီးပန္းတိုင္သို႕ ညာဘက္ေကြ႕ကာ ေရာက္လိမ့္မည္",short:"သင္သြားလိုေသာ ေနရာသို႔ ေရာက္ရိွၿပီ","short-upcoming":"သင္သြားလိုေသာ ေနရာသို႔ ေရာက္လိမ့္မည္",named:"သင္ သည္ {waypoint_name} မွာညာဘက္ေကြ႕ကာ ေရာက္ရွိျပီ"},"sharp left":{default:"သင္ သြားလိုေသာ {nth}ခရီးပန္းတိုင္သို႕ဘယ္ဘက္တြင္ေရာက္ရွိျပီ",upcoming:"သင္ သြားလိုေသာ {nth}ခရီးပန္းတိုင္သို႕ဘယ္ဘက္တြင္ေရာက္ရွိျပီ",short:"သင္သြားလိုေသာ ေနရာသို႔ ေရာက္ရိွၿပီ","short-upcoming":"သင္သြားလိုေသာ ေနရာသို႔ ေရာက္လိမ့္မည္",named:"သင္ သည္ {waypoint_name}မွာဘယ္ဘက္ေကြ႕ကာ ေရာက္ရွိျပီ"},"sharp right":{default:"သင္ သြားလိုေသာ {nth}ခရီးပန္းတိုင္သို႕ ညာဘက္ေကြ႕ကာ ေရာက္ရွိျပီ",upcoming:"သင္ သြားလိုေသာ{nth} ခရီးပန္းတိုင္သို႕ ညာဘက္ေကြ႕ကာ ေရာက္လိမ့္မည္",short:"သင္သြားလိုေသာ ေနရာသို႔ ေရာက္ရိွၿပီ","short-upcoming":"သင္သြားလိုေသာ ေနရာသို႔ ေရာက္လိမ့္မည္",named:"သင္ သည္ {waypoint_name} မွာညာဘက္ေကြ႕ကာ ေရာက္ရွိျပီ"},"slight right":{default:"သင္ သြားလိုေသာ {nth}ခရီးပန္းတိုင္သို႕ ညာဘက္ေကြ႕ကာ ေရာက္ရွိျပီ",upcoming:"သင္ သြားလိုေသာ{nth} ခရီးပန္းတိုင္သို႕ ညာဘက္ေကြ႕ကာ ေရာက္လိမ့္မည္",short:"သင္သြားလိုေသာ ေနရာသို႔ ေရာက္ရိွၿပီ","short-upcoming":"သင္သြားလိုေသာ ေနရာသို႔ ေရာက္လိမ့္မည္",named:"သင္ သည္ {waypoint_name} မွာညာဘက္ေကြ႕ကာ ေရာက္ရွိျပီ"},"slight left":{default:"သင္ သြားလိုေသာ {nth}ခရီးပန္းတိုင္သို႕ဘယ္ဘက္တြင္ေရာက္ရွိျပီ",upcoming:"သင္ သြားလိုေသာ {nth}ခရီးပန္းတိုင္သို႕ဘယ္ဘက္တြင္ေရာက္ရွိျပီ",short:"သင္သြားလိုေသာ ေနရာသို႔ ေရာက္ရွိျပီ","short-upcoming":"သင္သြားလိုေသာ ေနရာသို႔ ေရာက္လိမ့္မည္",named:"သင္ သည္ {waypoint_name}မွာဘယ္ဘက္ေကြ႕ကာ ေရာက္ရွိျပီ"},straight:{default:"သင္ သြားလိုေသာ {nth}ခရီးပန္းတိုင္သို႕တည့္တည့္သြားကာရာက္ရွိျပီ",upcoming:"သင္ သြားလိုေသာ {nth}ခရီးပန္းတိုင္သို႕တည့္တည့္သြားကာရာက္ရွိမည္",short:"သင္သြားလိုေသာ ေနရာသို႔ ေရာက္ရွိျပီ","short-upcoming":"သင္သြားလိုေသာ ေနရာသို႔ ေရာက္လိမ့္မည္",named:"သင္ သည္ {waypoint_name}မွာတည့္တည့္သြားကာ ေရာက္ရွိျပီ"}},continue:{default:{default:"{modifier}ကိုလွည့္ပါ",name:"{way_name}​​ေပၚတြင္ေနရန္ {modifier}ကိုလွည့္ပါ",destination:"{destination}ဆီသို႕ {modifier}ကို လွည္႕ပါ",exit:"{way_name}​​ေပၚသို႕ {modifier}ကိုလွည့္ပါ"},straight:{default:"ေျဖာင္႔ေျဖာင္႔တန္းတန္း ဆက္သြားပါ",name:"{way_name}​​ေပၚတြင္ေနရန္တည္တည့္ဆက္သြာပါ",destination:"{destination}ဆီသို႕ဆက္သြားပါ",distance:"{distance}ေလာက္ တည့္တည့္ ဆက္သြားပါ",namedistance:"{way_name}​​ေပၚတြင္{distance}ေလာက္ဆက္သြားပါ"},"sharp left":{default:"ဘယ္ဘက္ေထာင့္ခ်ိဳးေကြ႕ပါ",name:"{way_name}​ေပၚတြင္ေနရန္ ဘယ္ဘက္ေထာင့္ခ်ိဳးေကြ႕ပါ",destination:"{destination}ဆီသို႕ ဘယ္ဘက္ေထာင့္ခ်ိဳးေကြ႕ပါ"},"sharp right":{default:"ညာဘက္ ေထာင္႔ခ်ိဳးေကြ႕ပါ",name:"{way_name}​ေပၚတြင္ေနရန္ ညာဘက္ေထာင့္ခ်ိဳးေကြ႕ပါ",destination:"{destination}ဆီသို႕ ညာဘက္ေထာင့္ခ်ိဳးေကြ႕ပါ"},"slight left":{default:"ဘယ္ဘက္ အနည္းငယ္ေကြ႕ပါ",name:"{way_name}​ေပၚတြင္ေနရန္ ဘယ္ဘက္အနည္းငယ္ေကြ႕ပါ",destination:"{destination}ဆီသို႕ ဘယ္ဘက္အနည္းငယ္ခ်ိဳးေကြ႕ပါ"},"slight right":{default:"ညာဘက္ အနည္းငယ္ခ်ိဳးေကြ႕ပါ",name:"{way_name}​ေပၚတြင္ေနရန္ ညာဘက္အနည္းငယ္ေကြ႕ပါ",destination:"{destination}ဆီသို႕ ညာဘက္အနည္းငယ္ခ်ိဳးေကြ႕ပါ"},uturn:{default:"ဂ-ေကြ႔ ေကြ႔ပါ",name:"{way_name}လမ္းဘက္သို႕ ဂ-ေကြ႕ေကြ႕ျပီးဆက္သြားပါ",destination:"{destination}ဆီသို႕ ဂေကြ႕ခ်ိဳးေကြ႕ပါ"}},depart:{default:{default:"{direction}သို႕ ဦးတည္ပါ",name:"{direction}ကို {way_name}အေပၚတြင္ ဦးတည္ပါ",namedistance:"{direction}ကို {way_name}အေပၚတြင္{distance}ေလာက္ ဦးတည္ဆက္သြားပါ"}},"end of road":{default:{default:"{modifier}သို႕လွည့္ပါ",name:"{way_name}​​ေပၚသို႕ {modifier}ကိုလွည့္ပါ",destination:"{destination}ဆီသို႕ {modifier}ကို လွည္႕ပါ"},straight:{default:"ေျဖာင္႔ေျဖာင္႔တန္းတန္း ဆက္သြားပါ",name:"{way_name}​​ေပၚသို႕တည့္တည့္ဆက္သြားပါ",destination:"{destination}ဆီသို႕တည့္တည့္ဆက္သြားပါ"},uturn:{default:"လမ္းအဆံုးတြင္ ဂ-ေကြ႕ေကြ႕ပါ",name:"လမ္းအဆံုးတြင္ {way_name}​​ေပၚသို႕ဂ-ေကြ႕ေကြ႕ပါ",destination:"လမ္းအဆံုးတြင္{destination}ဆီသို႕ ဂေကြ႕ခ်ိဳးေကြ႕ပါ"}},fork:{default:{default:"လမ္းဆံုလမ္းခြတြင္ {modifier}ကိုဆက္သြားပါ",name:"{way_name}​​ေပၚသို႕ {modifier}ကိုဆက္သြားပါ",destination:"{destination}ဆီသို႕ {modifier}ကို ဆက္သြားပါ"},"slight left":{default:"လမ္းဆံုလမ္းခြတြင္ဘယ္ဘက္ကိုဆက္သြားပါ",name:"{way_name}​​ေပၚသို႕ဘယ္ဘက္ကိုဆက္သြားပါ",destination:"{destination}ဆီသို႕ဘယ္ဘက္ကို ဆက္သြားပါ"},"slight right":{default:"လမ္းဆံုလမ္းခြတြင္ညာဘက္ကိုဆက္သြားပါ",name:"{way_name}​​ေပၚသို႕ညာဘက္ကိုဆက္သြားပါ",destination:"{destination}ဆီသို႕ညာဘက္ကို ဆက္သြားပါ"},"sharp left":{default:"လမ္းဆံုလမ္းခြတြင္ဘယ္ဘက္ေထာင့္ခ်ိဳးကိုသြားပါ",name:"{way_name}​ေပၚတြင္ေနရန္ ဘယ္ဘက္ေထာင့္ခ်ိဳးယူပါ",destination:"{destination}ဆီသို႕ဘယ္ဘက္ေထာင့္ခ်ိဳး သြားပါ"},"sharp right":{default:"လမ္းဆံုလမ္းခြတြင္ညာဘက္ေထာင့္ခ်ိဳးကိုသြားပါ",name:"{way_name}​ေပၚသို႕ ညာဘက္ေထာင့္ခ်ိဳးယူပါ",destination:"{destination}ဆီသို႕ညာဘက္ေထာင့္ခ်ိဳး သြားပါ"},uturn:{default:"ဂ-ေကြ႔ ေကြ႔ပါ",name:"{way_name}သို႕ဂ-ေကြ႕ေကြ႕ပါ",destination:"{destination}ဆီသို႕ ဂေကြ႕ခ်ိဳးေကြ႕ပါ"}},merge:{default:{default:"{modifier}ကိုလာေရာက္ေပါင္းဆံုပါ",name:"{way_name}​​ေပၚသို႕ {modifier}ကိုလာေရာက္ေပါင္းဆံုပါ",destination:"{destination}ဆီသို႕ {modifier}ကို လာေရာက္ေပါင္းဆံုပါ"},straight:{default:"လာေရာက္ေပါင္းဆံုပါ",name:"{way_name}​​ေပၚသို႕လာေရာက္ေပါင္းဆံုပါ",destination:"{destination}ဆီသို႕ လာေရာက္ေပါင္းဆံုပါ"},"slight left":{default:"ဘယ္ဘက္သို႕လာေရာက္ေပါင္းဆံုပါ",name:"{way_name}​​ေပၚသို႕ဘယ္ဘက္ကိုလာေရာက္ေပါင္းဆံုပါ",destination:"{destination}ဆီသို႕ဘယ္ဘက္ကို လာေရာက္ေပါင္းဆံုပါ"},"slight right":{default:"ညာဘက္သို႕လာေရာက္ေပါင္းဆံုပါ",name:"{way_name}​​ေပၚသို႕ညာဘက္ကိုလာေရာက္ေပါင္းဆံုပါ",destination:"{destination}ဆီသို႕ညာဘက္ကို လာေရာက္ေပါင္းဆံုပါ"},"sharp left":{default:"ဘယ္ဘက္သို႕လာေရာက္ေပါင္းဆံုပါ",name:"{way_name}​​ေပၚသို႕ဘယ္ဘက္ကိုလာေရာက္ေပါင္းဆံုပါ",destination:"{destination}ဆီသို႕ဘယ္ဘက္ကို လာေရာက္ေပါင္းဆံုပါ"},"sharp right":{default:"ညာဘက္သို႕လာေရာက္ေပါင္းဆံုပါ",name:"{way_name}​​ေပၚသို႕ညာဘက္ကိုလာေရာက္ေပါင္းဆံုပါ",destination:"{destination}ဆီသို႕ညာဘက္ကို လာေရာက္ေပါင္းဆံုပါ"},uturn:{default:"ဂ-ေကြ႔ ေကြ႕ပါ",name:"{way_name}လမ္းဘက္သို႔ ဂ-ေကြ႔ ေကြ႔ပါ ",destination:"{destination}ဆီသို႕ ဂေကြ႕ခ်ိဳးေကြ႕ပါ"}},"new name":{default:{default:"{modifier}ကိုဆက္သြားပါ",name:"{way_name}​​ေပၚသို႕ {modifier}ကိုဆက္သြားပါ",destination:"{destination}ဆီသို႕ {modifier}ကို ဆက္သြားပါ"},straight:{default:"ေျဖာင္႔ေျဖာင္႔တန္းတန္း ဆက္သြားပါ",name:"{way_name}​​ေပၚသို႕ဆက္သြားပါ",destination:"{destination}ဆီသို႕ဆက္သြားပါ"},"sharp left":{default:"ဘယ္ဘက္ေထာင့္ခ်ိဳးယူပါ",name:"{way_name}​ေပၚတြင္ေနရန္ ဘယ္ဘက္ေထာင့္ခ်ိဳးယူပါ",destination:"{destination}ဆီသို႕ဘယ္ဘက္ေထာင့္ခ်ိဳး သြားပါ"},"sharp right":{default:"ညာဘက္ ေထာင္႔ခ်ိဳးယူပါ",name:"{way_name}​ေပၚသို႕ ညာဘက္ေထာင့္ခ်ိဳးယူပါ",destination:"{destination}ဆီသို႕ညာဘက္ေထာင့္ခ်ိဳး သြားပါ"},"slight left":{default:"ဘယ္ဘက္ အနည္းငယ္ဆက္သြားပါ",name:"{way_name}​​ေပၚသို႕ဘယ္ဘက္ အနည္းငယ္ဆက္သြားပါ",destination:"{destination}ဆီသို႕ဘယ္ဘက္အနည္းငယ္ဆက္သြားပါ"},"slight right":{default:"ညာဘက္ အနည္းငယ္ဆက္သြားပါ",name:"{way_name}​​ေပၚသို႕ညာဘက္ အနည္းငယ္ဆက္သြားပါ",destination:"{destination}ဆီသို႕ညာဘက္အနည္းငယ္ဆက္သြားပါ"},uturn:{default:"ဂ-ေကြ႔ ေကြ႔ပါ",name:"{way_name}လမ္းဘက္သို႔ ဂ-ေကြ႔ ေကြ႔ပါ",destination:"{destination}ဆီသို႕ ဂေကြ႕ခ်ိဳးေကြ႕ပါ"}},notification:{default:{default:"{modifier}ကိုဆက္သြားပါ",name:"{way_name}​​ေပၚသို႕ {modifier}ကိုဆက္သြားပါ",destination:"{destination}ဆီသို႕ {modifier}ကို ဆက္သြားပါ"},uturn:{default:"ဂ-ေကြ႔ ေကြ႔ပါ",name:"{way_name}လမ္းဘက္သို႔ ဂ-ေကြ႔ ေကြ႔ပါ",destination:"{destination}ဆီသို႕ ဂေကြ႕ခ်ိဳးေကြ႕ပါ"}},"off ramp":{default:{default:"ခ်ဥ္းကပ္လမ္းကိုယူပါ",name:"{way_name}​ေပၚသို႕ခ်ဥ္းကပ္လမ္းကိုယူပါ",destination:"{destination}ဆီသို႕ ခ်ဥ္းကပ္လမ္းကိုယူပါ",exit:"{exit}ကို ယူပါ",exit_destination:"{destination}ဆီသို႕ {exit} ကိုယူပါ"},left:{default:"ဘယ္ဘက္သို႕ခ်ဥ္းကပ္လမ္းကိုယူပါ",name:"{way_name}​ေပၚသို႕ဘယ္ဘက္ ​ေပၚတြင္ခ်ဥ္းကပ္လမ္းကိုယူပါ",destination:"{destination}ဆီသို႕ ဘယ္ဘက္​ေပၚတြင္ခ်ဥ္းကပ္လမ္းကိုယူပါ",exit:"ဘယ္ဘက္တြင္{exit}ကို ယူပါ",exit_destination:"{destination}ဆီသို႕ဘယ္ဘက္မွ {exit} ကိုယူပါ"},right:{default:"ညာဘက္သို႕ခ်ဥ္းကပ္လမ္းကိုယူပါ",name:"{way_name}​ေပၚသို႕ညာဘက္ ​ေပၚတြင္ခ်ဥ္းကပ္လမ္းကိုယူပါ",destination:"{destination}ဆီသို႕ ညာဘက္​ေပၚတြင္ခ်ဥ္းကပ္လမ္းကိုယူပါ",exit:"ညာဘက္တြင္{exit}ကို ယူပါ",exit_destination:"{destination}ဆီသို႕ညာဘက္မွ {exit} ကိုယူပါ"},"sharp left":{default:"ဘယ္ဘက္သို႕ခ်ဥ္းကပ္လမ္းကိုယူပါ",name:"{way_name}​ေပၚသို႕ဘယ္ဘက္ ​ေပၚတြင္ခ်ဥ္းကပ္လမ္းကိုယူပါ",destination:"{destination}ဆီသို႕ ဘယ္ဘက္​ေပၚတြင္ခ်ဥ္းကပ္လမ္းကိုယူပါ",exit:"ဘယ္ဘက္တြင္{exit}ကို ယူပါ",exit_destination:"{destination}ဆီသို႕ဘယ္ဘက္မွ {exit} ကိုယူပါ"},"sharp right":{default:"ညာဘက္သို႕ခ်ဥ္းကပ္လမ္းကိုယူပါ",name:"{way_name}​ေပၚသို႕ညာဘက္ ​ေပၚတြင္ခ်ဥ္းကပ္လမ္းကိုယူပါ",destination:"{destination}ဆီသို႕ ညာဘက္​ေပၚတြင္ခ်ဥ္းကပ္လမ္းကိုယူပါ",exit:"ညာဘက္တြင္{exit}ကို ယူပါ",exit_destination:"{destination}ဆီသို႕ညာဘက္မွ {exit} ကိုယူပါ"},"slight left":{default:"ဘယ္ဘက္သို႕ခ်ဥ္းကပ္လမ္းကိုယူပါ",name:"{way_name}​ေပၚသို႕ဘယ္ဘက္ ​ေပၚတြင္ခ်ဥ္းကပ္လမ္းကိုယူပါ",destination:"{destination}ဆီသို႕ ဘယ္ဘက္​ေပၚတြင္ခ်ဥ္းကပ္လမ္းကိုယူပါ",exit:"ဘယ္ဘက္တြင္{exit}ကို ယူပါ",exit_destination:"{destination}ဆီသို႕ဘယ္ဘက္မွ {exit} ကိုယူပါ"},"slight right":{default:"ညာဘက္သို႕ခ်ဥ္းကပ္လမ္းကိုယူပါ",name:"{way_name}​ေပၚသို႕ညာဘက္ ​ေပၚတြင္ခ်ဥ္းကပ္လမ္းကိုယူပါ",destination:"{destination}ဆီသို႕ ညာဘက္​ေပၚတြင္ခ်ဥ္းကပ္လမ္းကိုယူပါ",exit:"ညာဘက္တြင္{exit}ကို ယူပါ",exit_destination:"{destination}ဆီသို႕ညာဘက္မွ {exit} ကိုယူပါ"}},"on ramp":{default:{default:"ခ်ဥ္းကပ္လမ္းကိုယူပါ",name:"{way_name}​ေပၚသို႕ခ်ဥ္းကပ္လမ္းကိုယူပါ",destination:"{destination}ဆီသို႕ ခ်ဥ္းကပ္လမ္းကိုယူပါ"},left:{default:"ဘယ္ဘက္သို႕ခ်ဥ္းကပ္လမ္းကိုယူပါ",name:"{way_name}​ေပၚသို႕ဘယ္ဘက္ ​ေပၚတြင္ခ်ဥ္းကပ္လမ္းကိုယူပါ",destination:"{destination}ဆီသို႕ ဘယ္ဘက္​ေပၚတြင္ခ်ဥ္းကပ္လမ္းကိုယူပါ"},right:{default:"ညာဘက္သို႕ခ်ဥ္းကပ္လမ္းကိုယူပါ",name:"{way_name}​ေပၚသို႕ညာဘက္ ​ေပၚတြင္ခ်ဥ္းကပ္လမ္းကိုယူပါ",destination:"{destination}ဆီသို႕ ညာဘက္​ေပၚတြင္ခ်ဥ္းကပ္လမ္းကိုယူပါ"},"sharp left":{default:"ဘယ္ဘက္သို႕ခ်ဥ္းကပ္လမ္းကိုယူပါ",name:"{way_name}​ေပၚသို႕ဘယ္ဘက္ ​ေပၚတြင္ခ်ဥ္းကပ္လမ္းကိုယူပါ",destination:"{destination}ဆီသို႕ ဘယ္ဘက္​ေပၚတြင္ခ်ဥ္းကပ္လမ္းကိုယူပါ"},"sharp right":{default:"ညာဘက္သို႕ခ်ဥ္းကပ္လမ္းကိုယူပါ",name:"{way_name}​ေပၚသို႕ညာဘက္ ​ေပၚတြင္ခ်ဥ္းကပ္လမ္းကိုယူပါ",destination:"{destination}ဆီသို႕ ညာဘက္​ေပၚတြင္ခ်ဥ္းကပ္လမ္းကိုယူပါ"},"slight left":{default:"ဘယ္ဘက္သို႕ခ်ဥ္းကပ္လမ္းကိုယူပါ",name:"{way_name}​ေပၚသို႕ဘယ္ဘက္ ​ေပၚတြင္ခ်ဥ္းကပ္လမ္းကိုယူပါ",destination:"{destination}ဆီသို႕ ဘယ္ဘက္​ေပၚတြင္ခ်ဥ္းကပ္လမ္းကိုယူပါ"},"slight right":{default:"ညာဘက္သို႕ခ်ဥ္းကပ္လမ္းကိုယူပါ",name:"{way_name}​ေပၚသို႕ညာဘက္ ​ေပၚတြင္ခ်ဥ္းကပ္လမ္းကိုယူပါ",destination:"{destination}ဆီသို႕ ညာဘက္​ေပၚတြင္ခ်ဥ္းကပ္လမ္းကိုယူပါ"}},rotary:{default:{default:{default:"အဝိုင္းပတ္သို႕ဝင္ပါ",name:"{way_name}ေပၚသို႔အဝိုင္းပတ္လမ္းမွထြက္ပါ ",destination:"{destination}ေပၚသို႔အဝိုင္းပတ္လမ္းမွထြက္ပါ"},name:{default:"{rotary_name}သို႕ဝင္ပါ",name:"{rotary_name}အဝိုင္းပတ္ဝင္ျပီး{way_name}ေပၚသို႕ထြက္ပါ",destination:"{rotary_name}အဝိုင္းပတ္ဝင္ျပီး{destination}ဆီသို႕ထြက္ပါ"},exit:{default:"အဝိုင္းပတ္ဝင္ျပီး{exit_number}ကိုယူကာျပန္ထြက္ပါ",name:"အဝိုင္းပတ္သို႕ဝင္ျပီး{exit_number}ကိုယူကာ{way_name}ေပၚသို႕ထြက္ပါ",destination:"အဝိုင္းပတ္ဝင္ျပီး{exit_number}ကိုယူကာ{destination}ဆီသို႕ထြက္ပါ"},name_exit:{default:"{rotary_name}ကိုဝင္ျပီး {exit_number}ကိုယူကာထြက္ပါ",name:"{rotary_name}ကိုဝင္ျပီး{exit_number}ကိုယူကာ{way_name}ေပၚသို႕ထြက္ပါ",destination:"{rotary_name}ဝင္ျပီး{exit_number}ကိုယူကာ{destination}ဆီသို႕ထြက္ပါ"}}},roundabout:{default:{exit:{default:"{exit_number}ေပၚသို႔အဝိုင္းပတ္လမ္းမွထြက္ပါ",name:"အဝိုင္းပတ္ဝင္ျပီး{exit_number}ကိုယူကာ{way_name}ေပၚသို႕ထြက္ပါ",destination:"အဝိုင္းပတ္ဝင္ျပီး{exit_number}ကိုယူကာ{destination}ဆီသို႕ထြက္ပါ"},default:{default:"အဝိုင္းပတ္ဝင္ပါ",name:"{way_name}ေပၚသို႔အဝိုင္းပတ္လမ္းမွထြက္ပါ",destination:"{destination}ေပၚသို႔အဝိုင္းပတ္လမ္းမွထြက္ပါ"}}},"roundabout turn":{default:{default:"{modifier}ကိုလွည့္ပါ ",name:"{modifier}​ေပၚသို{way_name}ကိုဆက္သြားပါ ",destination:"{modifier}ဆီသို႕{destination}ကို ဆက္သြားပါ "},left:{default:"ဘယ္ဘက္သို႕ျပန္လွည္႔ပါ",name:"{way_name}​ေပၚသို႕ဘယ္ဘက္ကိုဆက္သြားပါ ",destination:"{destination}ဆီသို႕ဘယ္ဘက္မွ ေကြ႔ပါ"},right:{default:"ညာဘက္သို႔ျပန္လွည္႔ပါ",name:"{way_name}​ေပၚသို႕ညာဘက္ကိုလာေရာက္ေပါင္းဆံုပါ ",destination:"{destination}ညာဘက္သို႔ ေကြ႔ပါ"},straight:{default:"ေျဖာင္႔ေျဖာင္႔တန္းတန္း ဆက္သြားပါ",name:"{way_name}​​ေပၚသို႕တည့္တည့္ဆက္သြားပါ",destination:"{destination}ဆီသို႕တည့္တည့္ဆက္သြားပါ"}},"exit roundabout":{default:{default:"အဝိုင္းပတ္လမ္းမွထြက္ပါ",name:"{way_name}ေပၚသို႔အဝိုင္းပတ္လမ္းမွထြက္ပါ",destination:"ဦးတည္အဝိုင္းပတ္လမ္းမွထြက္ပါ{destination}"}},"exit rotary":{default:{default:"အဝိုင္းပတ္လမ္းမွထြက္ပါဦးတည္အဝိုင္းပတ္လမ္းမွထြက္ပါ",name:"{way_name}ေပၚသို႔အဝိုင္းပတ္လမ္းမွထြက္ပါ",destination:"ဦးတည္အဝိုင္းပတ္လမ္းမွထြက္ပါ{destination}"}},turn:{default:{default:"{modifier}ကိုလွည့္ပါ ",name:"{modifier}​ေပၚသို{way_name}ကိုဆက္သြားပါ ",destination:"{modifier}ဆီသို႕{destination}ကို ဆက္သြားပါ "},left:{default:"ဘယ္ဘက္သို႕ျပန္လွည္႔ပါ",name:"{way_name}​ေပၚသို႕ဘယ္ဘက္ကိုဆက္သြားပါ ",destination:"{destination}ဘယ္ဘက္သို႔ ေကြ႔ပါ"},right:{default:"ညာဘက္သို႔ျပန္လွည္႔ပါ",name:"{way_name}​ေပၚသို႕ညာဘက္ကိုလာေရာက္ေပါင္းဆံုပါ ",destination:"{destination}ညာဘက္သို႔ ေကြ႔ပါ"},straight:{default:"တည္႔တည္႔သြားပါ",name:"{way_name}",destination:"{destination}ဆီသို႕တည့္တည့္သြားပါ"}},"use lane":{no_lanes:{default:"ေျဖာင္႔ေျဖာင္႔တန္းတန္း ဆက္သြားပါ"},default:{default:"{lane_instruction}"}}}}},{}],36:[function(_dereq_,module,exports){module.exports={meta:{capitalizeFirstLetter:true},v5:{constants:{ordinalize:{1:"1e",2:"2e",3:"3e",4:"4e",5:"5e",6:"6e",7:"7e",8:"8e",9:"9e",10:"10e"},direction:{north:"noord",northeast:"noordoost",east:"oost",southeast:"zuidoost",south:"zuid",southwest:"zuidwest",west:"west",northwest:"noordwest"},modifier:{left:"links",right:"rechts","sharp left":"scherpe bocht naar links","sharp right":"scherpe bocht naar rechts","slight left":"iets naar links","slight right":"iets naar rechts",straight:"rechtdoor",uturn:"omkeren"},lanes:{xo:"Rechts aanhouden",ox:"Links aanhouden",xox:"In het midden blijven",oxo:"Links of rechts blijven"}},modes:{ferry:{default:"Neem de veerpont",name:"Neem de veerpont {way_name}",destination:"Neem de veerpont richting {destination}"}},phrase:{"two linked by distance":"{instruction_one}, dan na {distance}, {instruction_two}","two linked":"{instruction_one}, daarna {instruction_two}","one in distance":"Over {distance}, {instruction_one}","name and ref":"{name} ({ref})","exit with number":"afslag {exit}"},arrive:{default:{default:"Je bent gearriveerd op de {nth} bestemming.",upcoming:"U arriveert op de {nth} bestemming",short:"U bent gearriveerd","short-upcoming":"U zult aankomen",named:"U bent gearriveerd bij {waypoint_name}"},left:{default:"Je bent gearriveerd. De {nth} bestemming bevindt zich links.",upcoming:"Uw {nth} bestemming bevindt zich aan de linkerkant",short:"U bent gearriveerd","short-upcoming":"U zult aankomen",named:"U bent gearriveerd bij {waypoint_name}, de bestemming is aan de linkerkant"},right:{default:"Je bent gearriveerd. De {nth} bestemming bevindt zich rechts.",upcoming:"Uw {nth} bestemming bevindt zich aan de rechterkant",short:"U bent gearriveerd","short-upcoming":"U zult aankomen",named:"U bent gearriveerd bij {waypoint_name}, de bestemming is aan de  rechterkant"},"sharp left":{default:"Je bent gearriveerd. De {nth} bestemming bevindt zich links.",upcoming:"Uw {nth} bestemming bevindt zich aan de linkerkant",short:"U bent gearriveerd","short-upcoming":"U zult aankomen",named:"U bent gearriveerd bij {waypoint_name}, de bestemming is aan de linkerkant"},"sharp right":{default:"Je bent gearriveerd. De {nth} bestemming bevindt zich rechts.",upcoming:"Uw {nth} bestemming bevindt zich aan de rechterkant",short:"U bent gearriveerd","short-upcoming":"U zult aankomen",named:"U bent gearriveerd bij {waypoint_name},  de bestemming is aan de rechterkant"},"slight right":{default:"Je bent gearriveerd. De {nth} bestemming bevindt zich rechts.",upcoming:"Uw {nth} bestemming bevindt zich aan de rechterkant",short:"U bent gearriveerd","short-upcoming":"U zult aankomen",named:"U bent gearriveerd bij {waypoint_name},  de bestemming is aan de rechterkant"},"slight left":{default:"Je bent gearriveerd. De {nth} bestemming bevindt zich links.",upcoming:"Uw {nth} bestemming bevindt zich aan de linkerkant",short:"U bent gearriveerd","short-upcoming":"U zult aankomen",named:"U bent gearriveerd bij {waypoint_name},  de bestemming is aan de linkerkant"},straight:{default:"Je bent gearriveerd. De {nth} bestemming bevindt zich voor je.",upcoming:"Uw {nth} bestemming is recht voor u",short:"U bent gearriveerd","short-upcoming":"U zult aankomen",named:"U bent gearriveerd bij {waypoint_name}, de bestemming is recht voor u"}},continue:{default:{default:"Ga {modifier}",name:"Sla {modifier} om op {way_name} te blijven",destination:"Ga {modifier} richting {destination}",exit:"Ga {modifier} naar {way_name}"},straight:{default:"Ga rechtdoor",name:"Blijf rechtdoor gaan op {way_name}",destination:"Ga rechtdoor richting {destination}",distance:"Ga rechtdoor voor {distance}",namedistance:"Ga verder op {way_name} voor {distance}"},"sharp left":{default:"Linksaf",name:"Sla scherp links af om op {way_name} te blijven",destination:"Linksaf richting {destination}"},"sharp right":{default:"Rechtsaf",name:"Sla scherp rechts af om op {way_name} te blijven",destination:"Rechtsaf richting {destination}"},"slight left":{default:"Ga links",name:"Links afbuigen om op {way_name} te blijven",destination:"Rechts afbuigen om op {destination} te blijven"},"slight right":{default:"Rechts afbuigen",name:"Rechts afbuigen om op {way_name} te blijven",destination:"Rechts afbuigen richting {destination}"},uturn:{default:"Keer om",name:"Draai om en ga verder op {way_name}",destination:"Keer om richting {destination}"}},depart:{default:{default:"Vertrek in {direction}elijke richting",name:"Neem {way_name} in {direction}elijke richting",namedistance:"Ga richting {direction} op {way_name} voor {distance}"}},"end of road":{default:{default:"Ga {modifier}",name:"Ga {modifier} naar {way_name}",destination:"Ga {modifier} richting {destination}"},straight:{default:"Ga in de aangegeven richting",name:"Ga naar {way_name}",destination:"Ga richting {destination}"},uturn:{default:"Keer om",name:"Keer om naar {way_name}",destination:"Keer om richting {destination}"}},fork:{default:{default:"Ga {modifier} op de splitsing",name:"Houd {modifier} aan, tot {way_name}",destination:"Houd {modifier}, in de richting van {destination}"},"slight left":{default:"Links aanhouden op de splitsing",name:"Houd links aan, tot {way_name}",destination:"Houd links aan, richting {destination}"},"slight right":{default:"Rechts aanhouden op de splitsing",name:"Houd rechts aan, tot {way_name}",destination:"Houd rechts aan, richting {destination}"},"sharp left":{default:"Neem bij de splitsing, een scherpe bocht, naar links ",name:"Neem een scherpe bocht naar links, tot aan {way_name}",destination:"Neem een scherpe bocht naar links, richting {destination}"},"sharp right":{default:"Neem  op de splitsing, een scherpe bocht, naar rechts",name:"Neem een scherpe bocht naar rechts, tot aan {way_name}",destination:"Neem een scherpe bocht naar rechts, richting {destination}"},uturn:{default:"Keer om",name:"Keer om naar {way_name}",destination:"Keer om richting {destination}"}},merge:{default:{default:"Bij de splitsing {modifier}",name:"Bij de splitsing {modifier} naar {way_name}",destination:"Bij de splitsing {modifier} richting {destination}"},straight:{default:"Samenvoegen",name:"Ga verder op {way_name}",destination:"Ga verder richting {destination}"},"slight left":{default:"Bij de splitsing links aanhouden",name:"Bij de splitsing links aanhouden naar {way_name}",destination:"Bij de splitsing links aanhouden richting {destination}"},"slight right":{default:"Bij de splitsing rechts aanhouden",name:"Bij de splitsing rechts aanhouden naar {way_name}",destination:"Bij de splitsing rechts aanhouden richting {destination}"},"sharp left":{default:"Bij de splitsing linksaf",name:"Bij de splitsing linksaf naar {way_name}",destination:"Bij de splitsing linksaf richting {destination}"},"sharp right":{default:"Bij de splitsing rechtsaf",name:"Bij de splitsing rechtsaf naar {way_name}",destination:"Bij de splitsing rechtsaf richting {destination}"},uturn:{default:"Keer om",name:"Keer om naar {way_name}",destination:"Keer om richting {destination}"}},"new name":{default:{default:"Ga {modifier}",name:"Ga {modifier} naar {way_name}",destination:"Ga {modifier} richting {destination}"},straight:{default:"Ga in de aangegeven richting",name:"Ga rechtdoor naar {way_name}",destination:"Ga rechtdoor richting {destination}"},"sharp left":{default:"Neem een scherpe bocht, naar links",name:"Linksaf naar {way_name}",destination:"Linksaf richting {destination}"},"sharp right":{default:"Neem een scherpe bocht, naar rechts",name:"Rechtsaf naar {way_name}",destination:"Rechtsaf richting {destination}"},"slight left":{default:"Links aanhouden",name:"Links aanhouden naar {way_name}",destination:"Links aanhouden richting {destination}"},"slight right":{default:"Rechts aanhouden",name:"Rechts aanhouden naar {way_name}",destination:"Rechts aanhouden richting {destination}"},uturn:{default:"Keer om",name:"Keer om naar {way_name}",destination:"Keer om richting {destination}"}},notification:{default:{default:"Ga {modifier}",name:"Ga {modifier} naar {way_name}",destination:"Ga {modifier} richting {destination}"},uturn:{default:"Keer om",name:"Keer om naar {way_name}",destination:"Keer om richting {destination}"}},"off ramp":{default:{default:"Neem de afrit",name:"Neem de afrit naar {way_name}",destination:"Neem de afrit richting {destination}",exit:"Neem afslag {exit}",exit_destination:"Neem afslag {exit} richting {destination}"},left:{default:"Neem de afrit links",name:"Neem de afrit links naar {way_name}",destination:"Neem de afrit links richting {destination}",exit:"Neem afslag {exit} aan de linkerkant",exit_destination:"Neem afslag {exit} aan de linkerkant richting {destination}"},right:{default:"Neem de afrit rechts",name:"Neem de afrit rechts naar {way_name}",destination:"Neem de afrit rechts richting {destination}",exit:"Neem afslag {exit} aan de rechterkant",exit_destination:"Neem afslag {exit} aan de rechterkant richting {destination}"},"sharp left":{default:"Neem de afrit links",name:"Neem de afrit links naar {way_name}",destination:"Neem de afrit links richting {destination}",exit:"Neem afslag {exit} aan de linkerkant",exit_destination:"Neem afslag {exit} aan de linkerkant richting {destination}"},"sharp right":{default:"Neem de afrit rechts",name:"Neem de afrit rechts naar {way_name}",destination:"Neem de afrit rechts richting {destination}",exit:"Neem afslag {exit} aan de rechterkant",exit_destination:"Neem afslag {exit} aan de rechterkant richting {destination}"},"slight left":{default:"Neem de afrit links",name:"Neem de afrit links naar {way_name}",destination:"Neem de afrit links richting {destination}",exit:"Neem afslag {exit} aan de linkerkant",exit_destination:"Neem afslag {exit} aan de linkerkant richting {destination}"},"slight right":{default:"Neem de afrit rechts",name:"Neem de afrit rechts naar {way_name}",destination:"Neem de afrit rechts richting {destination}",exit:"Neem afslag {exit} aan de rechterkant",exit_destination:"Neem afslag {exit} aan de rechterkant richting {destination}"}},"on ramp":{default:{default:"Neem de oprit",name:"Neem de oprit naar {way_name}",destination:"Neem de oprit richting {destination}"},left:{default:"Neem de oprit links",name:"Neem de oprit links naar {way_name}",destination:"Neem de oprit links richting {destination}"},right:{default:"Neem de oprit rechts",name:"Neem de oprit rechts naar {way_name}",destination:"Neem de oprit rechts richting {destination}"},"sharp left":{default:"Neem de oprit links",name:"Neem de oprit links naar {way_name}",destination:"Neem de oprit links richting {destination}"},"sharp right":{default:"Neem de oprit rechts",name:"Neem de oprit rechts naar {way_name}",destination:"Neem de oprit rechts richting {destination}"},"slight left":{default:"Neem de oprit links",name:"Neem de oprit links naar {way_name}",destination:"Neem de oprit links richting {destination}"},"slight right":{default:"Neem de oprit rechts",name:"Neem de oprit rechts naar {way_name}",destination:"Neem de oprit rechts richting {destination}"}},rotary:{default:{default:{default:"Betreedt de rotonde",name:"Betreedt rotonde en sla af op {way_name}",destination:"Betreedt rotonde en sla af richting {destination}"},name:{default:"Ga het knooppunt {rotary_name} op",name:"Verlaat het knooppunt {rotary_name} naar {way_name}",destination:"Verlaat het knooppunt {rotary_name} richting {destination}"},exit:{default:"Betreedt rotonde en neem afslag {exit_number}",name:"Betreedt rotonde en neem afslag {exit_number} naar {way_name}",destination:"Betreedt rotonde en neem afslag {exit_number} richting {destination}"},name_exit:{default:"Ga het knooppunt {rotary_name} op en neem afslag {exit_number}",name:"Ga het knooppunt {rotary_name} op en neem afslag {exit_number} naar {way_name}",destination:"Ga het knooppunt {rotary_name} op en neem afslag {exit_number} richting {destination}"}}},roundabout:{default:{exit:{default:"Betreedt rotonde en neem afslag {exit_number}",name:"Betreedt rotonde en neem afslag {exit_number} naar {way_name}",destination:"Betreedt rotonde en neem afslag {exit_number} richting {destination}"},default:{default:"Betreedt de rotonde",name:"Betreedt rotonde en sla af op {way_name}",destination:"Betreedt rotonde en sla af richting {destination}"}}},"roundabout turn":{default:{default:"Ga {modifier}",name:"Ga {modifier} naar {way_name}",destination:"Ga {modifier} richting {destination}"},left:{default:"Ga linksaf",name:"Ga linksaf naar {way_name}",destination:"Ga linksaf richting {destination}"},right:{default:"Ga rechtsaf",name:"Ga rechtsaf naar {way_name}",destination:"Ga rechtsaf richting {destination}"},straight:{default:"Ga in de aangegeven richting",name:"Ga naar {way_name}",destination:"Ga richting {destination}"}},"exit roundabout":{default:{default:"Verlaat de rotonde",name:"Verlaat de rotonde en ga verder op {way_name}",destination:"Verlaat de rotonde richting {destination}"}},"exit rotary":{default:{default:"Verlaat de rotonde",name:"Verlaat de rotonde en ga verder op {way_name}",destination:"Verlaat de rotonde richting {destination}"}},turn:{default:{default:"Ga {modifier}",name:"Ga {modifier} naar {way_name}",destination:"Ga {modifier} richting {destination}"},left:{default:"Ga linksaf",name:"Ga linksaf naar {way_name}",destination:"Ga linksaf richting {destination}"},right:{default:"Ga rechtsaf",name:"Ga rechtsaf naar {way_name}",destination:"Ga rechtsaf richting {destination}"},straight:{default:"Ga rechtdoor",name:"Ga rechtdoor naar {way_name}",destination:"Ga rechtdoor richting {destination}"}},"use lane":{no_lanes:{default:"Rechtdoor"},default:{default:"{lane_instruction}"}}}}},{}],37:[function(_dereq_,module,exports){module.exports={meta:{capitalizeFirstLetter:true},v5:{constants:{ordinalize:{1:"1.",2:"2.",3:"3.",4:"4.",5:"5.",6:"6.",7:"7.",8:"8.",9:"9.",10:"10."},direction:{north:"nord",northeast:"nordøst",east:"øst",southeast:"sørøst",south:"sør",southwest:"sørvest",west:"vest",northwest:"nordvest"},modifier:{left:"venstre",right:"høyre","sharp left":"skarp venstre","sharp right":"skarp høyre","slight left":"litt til venstre","slight right":"litt til høyre",straight:"rett frem",
uturn:"U-sving"},lanes:{xo:"Hold til høyre",ox:"Hold til venstre",xox:"Hold deg i midten",oxo:"Hold til venstre eller høyre"}},modes:{ferry:{default:"Ta ferja",name:"Ta ferja {way_name}",destination:"Ta ferja til {destination}"}},phrase:{"two linked by distance":"{instruction_one}, deretter {instruction_two} om {distance}","two linked":"{instruction_one}, deretter {instruction_two}","one in distance":"Om {distance}, {instruction_one}","name and ref":"{name} ({ref})","exit with number":"avkjørsel {exit}"},arrive:{default:{default:"Du har ankommet din {nth} destinasjon",upcoming:"Du vil ankomme din {nth} destinasjon",short:"Du har ankommet","short-upcoming":"Du vil ankomme",named:"Du har ankommet {waypoint_name}"},left:{default:"Du har ankommet din {nth} destinasjon, på din venstre side",upcoming:"Du vil ankomme din {nth} destinasjon, på din venstre side",short:"Du har ankommet","short-upcoming":"Du vil ankomme",named:"Du har ankommet {waypoint_name}, på din venstre side"},right:{default:"Du har ankommet din {nth} destinasjon, på din høyre side",upcoming:"Du vil ankomme din {nth} destinasjon, på din høyre side",short:"Du har ankommet","short-upcoming":"Du vil ankomme",named:"Du har ankommet {waypoint_name}, på din høyre side"},"sharp left":{default:"Du har ankommet din {nth} destinasjon, på din venstre side",upcoming:"Du vil ankomme din {nth} destinasjon, på din venstre side",short:"Du har ankommet","short-upcoming":"Du vil ankomme",named:"Du har ankommet {waypoint_name}, på din venstre side"},"sharp right":{default:"Du har ankommet din {nth} destinasjon, på din høyre side",upcoming:"Du vil ankomme din {nth} destinasjon, på din høyre side",short:"Du har ankommet","short-upcoming":"Du vil ankomme",named:"Du har ankommet {waypoint_name}, på din høyre side"},"slight right":{default:"Du har ankommet din {nth} destinasjon, på din høyre side",upcoming:"Du vil ankomme din {nth} destinasjon, på din høyre side",short:"Du har ankommet","short-upcoming":"Du vil ankomme",named:"Du har ankommet {waypoint_name}, på din høyre side"},"slight left":{default:"Du har ankommet din {nth} destinasjon, på din venstre side",upcoming:"Du vil ankomme din {nth} destinasjon, på din venstre side",short:"Du har ankommet","short-upcoming":"Du vil ankomme",named:"Du har ankommet {waypoint_name}, på din venstre side"},straight:{default:"Du har ankommet din {nth} destinasjon, rett forut",upcoming:"Du vil ankomme din {nth} destinasjon, rett forut",short:"Du har ankommet","short-upcoming":"Du vil ankomme",named:"Du har ankommet {waypoint_name}, rett forut"}},continue:{default:{default:"Ta til {modifier}",name:"Ta til {modifier} for å bli værende på {way_name}",destination:"Ta til {modifier} mot {destination}",exit:"Ta til {modifier} inn på {way_name}"},straight:{default:"Fortsett rett frem",name:"Fortsett rett frem for å bli værende på {way_name}",destination:"Fortsett mot {destination}",distance:"Fortsett rett frem, {distance} ",namedistance:"Fortsett på {way_name}, {distance}"},"sharp left":{default:"Sving skarpt til venstre",name:"Sving skarpt til venstre for å bli værende på {way_name}",destination:"Sving skarpt til venstre mot {destination}"},"sharp right":{default:"Sving skarpt til høyre",name:"Sving skarpt til høyre for å bli værende på {way_name}",destination:"Sving skarpt mot {destination}"},"slight left":{default:"Sving svakt til venstre",name:"Sving svakt til venstre for å bli værende på {way_name}",destination:"Sving svakt til venstre mot {destination}"},"slight right":{default:"Sving svakt til høyre",name:"Sving svakt til høyre for å bli værende på {way_name}",destination:"Sving svakt til høyre mot {destination}"},uturn:{default:"Ta en U-sving",name:"Ta en U-sving og fortsett på {way_name}",destination:"Ta en U-sving mot {destination}"}},depart:{default:{default:"Kjør i retning {direction}",name:"Kjør i retning {direction} på {way_name}",namedistance:"Kjør i retning {direction} på {way_name}, {distance}"}},"end of road":{default:{default:"Sving {modifier}",name:"Ta til {modifier} inn på {way_name}",destination:"Sving {modifier} mot {destination}"},straight:{default:"Fortsett rett frem",name:"Fortsett rett frem til  {way_name}",destination:"Fortsett rett frem mot {destination}"},uturn:{default:"Ta en U-sving i enden av veien",name:"Ta en U-sving til {way_name} i enden av veien",destination:"Ta en U-sving mot {destination} i enden av veien"}},fork:{default:{default:"Hold til {modifier} i veikrysset",name:"Hold til {modifier} inn på {way_name}",destination:"Hold til {modifier} mot {destination}"},"slight left":{default:"Hold til venstre i veikrysset",name:"Hold til venstre inn på {way_name}",destination:"Hold til venstre mot {destination}"},"slight right":{default:"Hold til høyre i veikrysset",name:"Hold til høyre inn på {way_name}",destination:"Hold til høyre mot {destination}"},"sharp left":{default:"Sving skarpt til venstre i veikrysset",name:"Sving skarpt til venstre inn på {way_name}",destination:"Sving skarpt til venstre mot {destination}"},"sharp right":{default:"Sving skarpt til høyre i veikrysset",name:"Sving skarpt til høyre inn på {way_name}",destination:"Svings skarpt til høyre mot {destination}"},uturn:{default:"Ta en U-sving",name:"Ta en U-sving til {way_name}",destination:"Ta en U-sving mot {destination}"}},merge:{default:{default:"Hold {modifier} kjørefelt",name:"Hold {modifier} kjørefelt inn på {way_name}",destination:"Hold {modifier} kjørefelt mot {destination}"},straight:{default:"Hold kjørefelt",name:"Hold kjørefelt inn på {way_name}",destination:"Hold kjørefelt mot {destination}"},"slight left":{default:"Hold venstre kjørefelt",name:"Hold venstre kjørefelt inn på {way_name}",destination:"Hold venstre kjørefelt mot {destination}"},"slight right":{default:"Hold høyre kjørefelt",name:"Hold høyre kjørefelt inn på {way_name}",destination:"Hold høyre kjørefelt mot {destination}"},"sharp left":{default:"Hold venstre kjørefelt",name:"Hold venstre kjørefelt inn på {way_name}",destination:"Hold venstre kjørefelt mot {destination}"},"sharp right":{default:"Hold høyre kjørefelt",name:"Hold høyre kjørefelt inn på {way_name}",destination:"Hold høyre kjørefelt mot {destination}"},uturn:{default:"Ta en U-sving",name:"Ta en U-sving til {way_name}",destination:"Ta en U-sving mot {destination}"}},"new name":{default:{default:"Fortsett {modifier}",name:"Fortsett {modifier} til {way_name}",destination:"Fortsett {modifier} mot  {destination}"},straight:{default:"Fortsett rett frem",name:"Fortsett inn på {way_name}",destination:"Fortsett mot {destination}"},"sharp left":{default:"Sving skarpt til venstre",name:"Sving skarpt til venstre inn på {way_name}",destination:"Sving skarpt til venstre mot {destination}"},"sharp right":{default:"Sving skarpt til høyre",name:"Sving skarpt til høyre inn på {way_name}",destination:"Svings skarpt til høyre mot {destination}"},"slight left":{default:"Fortsett litt mot venstre",name:"Fortsett litt mot venstre til {way_name}",destination:"Fortsett litt mot venstre mot {destination}"},"slight right":{default:"Fortsett litt mot høyre",name:"Fortsett litt mot høyre til {way_name}",destination:"Fortsett litt mot høyre mot {destination}"},uturn:{default:"Ta en U-sving",name:"Ta en U-sving til {way_name}",destination:"Ta en U-sving mot {destination}"}},notification:{default:{default:"Fortsett {modifier}",name:"Fortsett {modifier} til {way_name}",destination:"Fortsett {modifier} mot  {destination}"},uturn:{default:"Ta en U-sving",name:"Ta en U-sving til {way_name}",destination:"Ta en U-sving mot {destination}"}},"off ramp":{default:{default:"Ta avkjørselen",name:"Ta avkjørselen inn på {way_name}",destination:"Ta avkjørselen mot {destination}",exit:"Ta avkjørsel {exit}",exit_destination:"Ta avkjørsel {exit} mot {destination}"},left:{default:"Ta avkjørselen på venstre side",name:"Ta avkjørselen på venstre side inn på {way_name}",destination:"Ta avkjørselen på venstre side mot {destination}",exit:"Ta avkjørsel {exit} på venstre side",exit_destination:"Ta avkjørsel {exit} på venstre side mot {destination}"},right:{default:"Ta avkjørselen på høyre side",name:"Ta avkjørselen på høyre side inn på {way_name}",destination:"Ta avkjørselen på høyre side mot {destination}",exit:"Ta avkjørsel {exit} på høyre side",exit_destination:"Ta avkjørsel {exit} på høyre side mot {destination}"},"sharp left":{default:"Ta avkjørselen på venstre side",name:"Ta avkjørselen på venstre side inn på {way_name}",destination:"Ta avkjørselen på venstre side mot {destination}",exit:"Ta avkjørsel {exit} på venstre side",exit_destination:"Ta avkjørsel {exit} på venstre side mot {destination}"},"sharp right":{default:"Ta avkjørselen på høyre side",name:"Ta avkjørselen på høyre side inn på {way_name}",destination:"Ta avkjørselen på høyre side mot {destination}",exit:"Ta avkjørsel {exit} på høyre side",exit_destination:"Ta avkjørsel {exit} på høyre side mot {destination}"},"slight left":{default:"Ta avkjørselen på venstre side",name:"Ta avkjørselen på venstre side inn på {way_name}",destination:"Ta avkjørselen på venstre side mot {destination}",exit:"Ta avkjørsel {exit} på venstre side",exit_destination:"Ta avkjørsel {exit} på venstre side mot {destination}"},"slight right":{default:"Ta avkjørselen på høyre side",name:"Ta avkjørselen på høyre side inn på {way_name}",destination:"Ta avkjørselen på høyre side mot {destination}",exit:"Ta avkjørsel {exit} på høyre side",exit_destination:"Ta avkjørsel {exit} på høyre side mot {destination}"}},"on ramp":{default:{default:"Ta avkjørselen",name:"Ta avkjørselen inn på {way_name}",destination:"Ta avkjørselen mot {destination}"},left:{default:"Ta avkjørselen på venstre side",name:"Ta avkjørselen på venstre side inn på {way_name}",destination:"Ta avkjørselen på venstre side mot {destination}"},right:{default:"Ta avkjørselen på høyre side",name:"Ta avkjørselen på høyre side inn på {way_name}",destination:"Ta avkjørselen på høyre side mot {destination}"},"sharp left":{default:"Ta avkjørselen på venstre side",name:"Ta avkjørselen på venstre side inn på {way_name}",destination:"Ta avkjørselen på venstre side mot {destination}"},"sharp right":{default:"Ta avkjørselen på høyre side",name:"Ta avkjørselen på høyre side inn på {way_name}",destination:"Ta avkjørselen på høyre side mot {destination}"},"slight left":{default:"Ta avkjørselen på venstre side",name:"Ta avkjørselen på venstre side inn på {way_name}",destination:"Ta avkjørselen på venstre side mot {destination}"},"slight right":{default:"Ta avkjørselen på høyre side",name:"Ta avkjørselen på høyre side inn på {way_name}",destination:"Ta avkjørselen på høyre side mot {destination}"}},rotary:{default:{default:{default:"Kjør inn i rundkjøringen",name:"Kjør inn i rundkjøringen og deretter ut på {way_name}",destination:"Kjør inn i rundkjøringen og deretter ut mot {destination}"},name:{default:"Kjør inn i {rotary_name}",name:"Kjør inn i {rotary_name} og deretter ut på {way_name}",destination:"Kjør inn i {rotary_name} og deretter ut mot {destination}"},exit:{default:"Kjør inn i rundkjøringen og ta {exit_number} avkjørsel",name:"Kjør inn i rundkjøringen og ta {exit_number} avkjørsel ut på {way_name}",destination:"Kjør inn i rundkjøringen og ta {exit_number} avkjørsel ut mot {destination} "},name_exit:{default:"Kjør inn i {rotary_name} og ta {exit_number} avkjørsel",name:"Kjør inn i {rotary_name} og ta {exit_number} avkjørsel inn på {way_name}",destination:"Kjør inn i {rotary_name} og ta {exit_number} avkjørsel mot {destination}"}}},roundabout:{default:{exit:{default:"Kjør inn i rundkjøringen og ta {exit_number} avkjørsel",name:"Kjør inn i rundkjøringen og ta {exit_number} avkjørsel inn på {way_name}",destination:"Kjør inn i rundkjøringen og ta {exit_number} avkjørsel ut mot {destination} "},default:{default:"Kjør inn i rundkjøringen",name:"Kjør inn i rundkjøringen og deretter ut på {way_name}",destination:"Kjør inn i rundkjøringen og deretter ut mot {destination}"}}},"roundabout turn":{default:{default:"Ta en {modifier}",name:"Ta en {modifier} inn på {way_name}",destination:"Ta en {modifier} mot {destination}"},left:{default:"Sving til venstre",name:"Sving til venstre inn på {way_name}",destination:"Sving til venstre mot {destination}"},right:{default:"Sving til høyre",name:"Sving til høyre inn på {way_name}",destination:"Sving til høyre mot {destination}"},straight:{default:"Fortsett rett frem",name:"Fortsett rett frem til  {way_name}",destination:"Fortsett rett frem mot {destination}"}},"exit roundabout":{default:{default:"Kjør ut av rundkjøringen",name:"Kjør ut av rundkjøringen og inn på {way_name}",destination:"Kjør ut av rundkjøringen mot {destination}"}},"exit rotary":{default:{default:"Kjør ut av rundkjøringen",name:"Kjør ut av rundkjøringen og inn på {way_name}",destination:"Kjør ut av rundkjøringen mot {destination}"}},turn:{default:{default:"Ta en {modifier}",name:"Ta en {modifier} inn på {way_name}",destination:"Ta en {modifier} mot {destination}"},left:{default:"Sving til venstre",name:"Sving til venstre inn på {way_name}",destination:"Sving til venstre mot {destination}"},right:{default:"Sving til høyre",name:"Sving til høyre inn på {way_name}",destination:"Sving til høyre mot {destination}"},straight:{default:"Kjør rett frem",name:"Kjør rett frem og inn på {way_name}",destination:"Kjør rett frem mot {destination}"}},"use lane":{no_lanes:{default:"Fortsett rett frem"},default:{default:"{lane_instruction}"}}}}},{}],38:[function(_dereq_,module,exports){module.exports={meta:{capitalizeFirstLetter:true},v5:{constants:{ordinalize:{1:"1.",2:"2.",3:"3.",4:"4.",5:"5.",6:"6.",7:"7.",8:"8.",9:"9.",10:"10."},direction:{north:"północ",northeast:"północny wschód",east:"wschód",southeast:"południowy wschód",south:"południe",southwest:"południowy zachód",west:"zachód",northwest:"północny zachód"},modifier:{left:"lewo",right:"prawo","sharp left":"ostro w lewo","sharp right":"ostro w prawo","slight left":"łagodnie w lewo","slight right":"łagodnie w prawo",straight:"prosto",uturn:"zawróć"},lanes:{xo:"Trzymaj się prawej strony",ox:"Trzymaj się lewej strony",xox:"Trzymaj się środka",oxo:"Trzymaj się lewej lub prawej strony"}},modes:{ferry:{default:"Weź prom",name:"Weź prom {way_name}",destination:"Weź prom w kierunku {destination}"}},phrase:{"two linked by distance":"{instruction_one}, następnie za {distance} {instruction_two}","two linked":"{instruction_one}, następnie {instruction_two}","one in distance":"Za {distance}, {instruction_one}","name and ref":"{name} ({ref})","exit with number":"exit {exit}"},arrive:{default:{default:"Dojechano do miejsca docelowego {nth}",upcoming:"Dojechano do miejsca docelowego {nth}",short:"Dojechano do miejsca docelowego {nth}","short-upcoming":"Dojechano do miejsca docelowego {nth}",named:"Dojechano do {waypoint_name}"},left:{default:"Dojechano do miejsca docelowego {nth}, po lewej stronie",upcoming:"Dojechano do miejsca docelowego {nth}, po lewej stronie",short:"Dojechano do miejsca docelowego {nth}","short-upcoming":"Dojechano do miejsca docelowego {nth}",named:"Dojechano do {waypoint_name}, po lewej stronie"},right:{default:"Dojechano do miejsca docelowego {nth}, po prawej stronie",upcoming:"Dojechano do miejsca docelowego {nth}, po prawej stronie",short:"Dojechano do miejsca docelowego {nth}","short-upcoming":"Dojechano do miejsca docelowego {nth}",named:"Dojechano do {waypoint_name}, po prawej stronie"},"sharp left":{default:"Dojechano do miejsca docelowego {nth}, po lewej stronie",upcoming:"Dojechano do miejsca docelowego {nth}, po lewej stronie",short:"Dojechano do miejsca docelowego {nth}","short-upcoming":"Dojechano do miejsca docelowego {nth}",named:"Dojechano do {waypoint_name}, po lewej stronie"},"sharp right":{default:"Dojechano do miejsca docelowego {nth}, po prawej stronie",upcoming:"Dojechano do miejsca docelowego {nth}, po prawej stronie",short:"Dojechano do miejsca docelowego {nth}","short-upcoming":"Dojechano do miejsca docelowego {nth}",named:"Dojechano do {waypoint_name}, po prawej stronie"},"slight right":{default:"Dojechano do miejsca docelowego {nth}, po prawej stronie",upcoming:"Dojechano do miejsca docelowego {nth}, po prawej stronie",short:"Dojechano do miejsca docelowego {nth}","short-upcoming":"Dojechano do miejsca docelowego {nth}",named:"Dojechano do {waypoint_name}, po prawej stronie"},"slight left":{default:"Dojechano do miejsca docelowego {nth}, po lewej stronie",upcoming:"Dojechano do miejsca docelowego {nth}, po lewej stronie",short:"Dojechano do miejsca docelowego {nth}","short-upcoming":"Dojechano do miejsca docelowego {nth}",named:"Dojechano do {waypoint_name}, po lewej stronie"},straight:{default:"Dojechano do miejsca docelowego {nth} , prosto",upcoming:"Dojechano do miejsca docelowego {nth} , prosto",short:"Dojechano do miejsca docelowego {nth}","short-upcoming":"Dojechano do miejsca docelowego {nth}",named:"Dojechano do {waypoint_name}, prosto"}},continue:{default:{default:"Skręć {modifier}",name:"Skręć w {modifier}, aby pozostać na {way_name}",destination:"Skręć {modifier} w kierunku {destination}",exit:"Skręć {modifier} na {way_name}"},straight:{default:"Kontynuuj prosto",name:"Jedź dalej prosto, aby pozostać na {way_name}",destination:"Kontynuuj w kierunku {destination}",distance:"Jedź dalej prosto przez {distance}",namedistance:"Jedź dalej {way_name} przez {distance}"},"sharp left":{default:"Skręć ostro w lewo",name:"Skręć w lewo w ostry zakręt, aby pozostać na {way_name}",destination:"Skręć ostro w lewo w kierunku {destination}"},"sharp right":{default:"Skręć ostro w prawo",name:"Skręć w prawo w ostry zakręt, aby pozostać na {way_name}",destination:"Skręć ostro w prawo w kierunku {destination}"},"slight left":{default:"Skręć w lewo w łagodny zakręt",name:"Skręć w lewo w łagodny zakręt, aby pozostać na {way_name}",destination:"Skręć w lewo w łagodny zakręt na {destination}"},"slight right":{default:"Skręć w prawo w łagodny zakręt",name:"Skręć w prawo w łagodny zakręt, aby pozostać na {way_name}",destination:"Skręć w prawo w łagodny zakręt na {destination}"},uturn:{default:"Zawróć",name:"Zawróć i jedź dalej {way_name}",destination:"Zawróć w kierunku {destination}"}},depart:{default:{default:"Kieruj się {direction}",name:"Kieruj się {direction} na {way_name}",namedistance:"Head {direction} on {way_name} for {distance}"}},"end of road":{default:{default:"Skręć {modifier}",name:"Skręć {modifier} na {way_name}",destination:"Skręć {modifier} w kierunku {destination}"},straight:{default:"Kontynuuj prosto",name:"Kontynuuj prosto na {way_name}",destination:"Kontynuuj prosto w kierunku {destination}"},uturn:{default:"Zawróć na końcu ulicy",name:"Zawróć na końcu ulicy na {way_name}",destination:"Zawróć na końcu ulicy w kierunku {destination}"}},fork:{default:{default:"Na rozwidleniu trzymaj się {modifier}",name:"Na rozwidleniu trzymaj się {modifier} na {way_name}",destination:"Na rozwidleniu trzymaj się {modifier} w kierunku {destination}"},"slight left":{default:"Na rozwidleniu trzymaj się lewej strony",name:"Na rozwidleniu trzymaj się lewej strony w {way_name}",destination:"Na rozwidleniu trzymaj się lewej strony w kierunku {destination}"},"slight right":{default:"Na rozwidleniu trzymaj się prawej strony",name:"Na rozwidleniu trzymaj się prawej strony na {way_name}",destination:"Na rozwidleniu trzymaj się prawej strony w kierunku {destination}"},"sharp left":{default:"Na rozwidleniu skręć ostro w lewo",name:"Skręć ostro w lewo w {way_name}",destination:"Skręć ostro w lewo w kierunku {destination}"},"sharp right":{default:"Na rozwidleniu skręć ostro w prawo",name:"Skręć ostro w prawo na {way_name}",destination:"Skręć ostro w prawo w kierunku {destination}"},uturn:{default:"Zawróć",name:"Zawróć na {way_name}",destination:"Zawróć w kierunku {destination}"}},merge:{default:{default:"Włącz się {modifier}",name:"Włącz się {modifier} na {way_name}",destination:"Włącz się {modifier} w kierunku {destination}"},straight:{default:"Włącz się prosto",name:"Włącz się prosto na {way_name}",destination:"Włącz się prosto w kierunku {destination}"},"slight left":{default:"Włącz się z lewej strony",name:"Włącz się z lewej strony na {way_name}",destination:"Włącz się z lewej strony w kierunku {destination}"},"slight right":{default:"Włącz się z prawej strony",name:"Włącz się z prawej strony na {way_name}",destination:"Włącz się z prawej strony w kierunku {destination}"},"sharp left":{default:"Włącz się z lewej strony",name:"Włącz się z lewej strony na {way_name}",destination:"Włącz się z lewej strony w kierunku {destination}"},"sharp right":{default:"Włącz się z prawej strony",name:"Włącz się z prawej strony na {way_name}",destination:"Włącz się z prawej strony w kierunku {destination}"},uturn:{default:"Zawróć",name:"Zawróć na {way_name}",destination:"Zawróć w kierunku {destination}"}},"new name":{default:{default:"Kontynuuj {modifier}",name:"Kontynuuj {modifier} na {way_name}",destination:"Kontynuuj {modifier} w kierunku {destination}"},straight:{default:"Kontynuuj prosto",name:"Kontynuuj na {way_name}",destination:"Kontynuuj w kierunku {destination}"},"sharp left":{default:"Skręć ostro w lewo",name:"Skręć ostro w lewo w {way_name}",destination:"Skręć ostro w lewo w kierunku {destination}"},"sharp right":{default:"Skręć ostro w prawo",name:"Skręć ostro w prawo na {way_name}",destination:"Skręć ostro w prawo w kierunku {destination}"},"slight left":{default:"Kontynuuj łagodnie w lewo",name:"Kontynuuj łagodnie w lewo na {way_name}",destination:"Kontynuuj łagodnie w lewo w kierunku {destination}"},"slight right":{default:"Kontynuuj łagodnie w prawo",name:"Kontynuuj łagodnie w prawo na {way_name}",destination:"Kontynuuj łagodnie w prawo w kierunku {destination}"},uturn:{default:"Zawróć",name:"Zawróć na {way_name}",destination:"Zawróć w kierunku {destination}"}},notification:{default:{default:"Kontynuuj {modifier}",name:"Kontynuuj {modifier} na {way_name}",destination:"Kontynuuj {modifier} w kierunku {destination}"},uturn:{default:"Zawróć",name:"Zawróć na {way_name}",destination:"Zawróć w kierunku {destination}"}},"off ramp":{default:{default:"Zjedź",name:"Weź zjazd na {way_name}",destination:"Weź zjazd w kierunku {destination}",exit:"Zjedź zjazdem {exit}",exit_destination:"Zjedź zjazdem {exit} na {destination}"},left:{default:"Weź zjazd po lewej",name:"Weź zjazd po lewej na {way_name}",destination:"Weź zjazd po lewej w kierunku {destination}",exit:"Zjedź zjazdem {exit} po lewej stronie",exit_destination:"Zjedź zjazdem {exit} po lewej stronie na {destination}"},right:{default:"Weź zjazd po prawej",name:"Weź zjazd po prawej na {way_name}",destination:"Weź zjazd po prawej w kierunku {destination}",exit:"Zjedź zjazdem {exit} po prawej stronie",exit_destination:"Zjedź zjazdem {exit} po prawej stronie na {destination}"},"sharp left":{default:"Weź zjazd po lewej",name:"Weź zjazd po lewej na {way_name}",destination:"Weź zjazd po lewej w kierunku {destination}",exit:"Zjedź zjazdem {exit} po lewej stronie",exit_destination:"Zjedź zjazdem {exit} po lewej stronie na {destination}"},"sharp right":{default:"Weź zjazd po prawej",name:"Weź zjazd po prawej na {way_name}",destination:"Weź zjazd po prawej w kierunku {destination}",exit:"Zjedź zjazdem {exit} po prawej stronie",exit_destination:"Zjedź zjazdem {exit} po prawej stronie na {destination}"},"slight left":{default:"Weź zjazd po lewej",name:"Weź zjazd po lewej na {way_name}",destination:"Weź zjazd po lewej w kierunku {destination}",exit:"Zjedź zjazdem {exit} po lewej stronie",exit_destination:"Zjedź zjazdem {exit} po lewej stronie na {destination}"},"slight right":{default:"Weź zjazd po prawej",name:"Weź zjazd po prawej na {way_name}",destination:"Weź zjazd po prawej w kierunku {destination}",exit:"Zjedź zjazdem {exit} po prawej stronie",exit_destination:"Zjedź zjazdem {exit} po prawej stronie na {destination}"}},"on ramp":{default:{default:"Weź zjazd",name:"Weź zjazd na {way_name}",destination:"Weź zjazd w kierunku {destination}"},left:{default:"Weź zjazd po lewej",name:"Weź zjazd po lewej na {way_name}",destination:"Weź zjazd po lewej w kierunku {destination}"},right:{default:"Weź zjazd po prawej",name:"Weź zjazd po prawej na {way_name}",destination:"Weź zjazd po prawej w kierunku {destination}"},"sharp left":{default:"Weź zjazd po lewej",name:"Weź zjazd po lewej na {way_name}",destination:"Weź zjazd po lewej w kierunku {destination}"},"sharp right":{default:"Weź zjazd po prawej",name:"Weź zjazd po prawej na {way_name}",destination:"Weź zjazd po prawej w kierunku {destination}"},"slight left":{default:"Weź zjazd po lewej",name:"Weź zjazd po lewej na {way_name}",destination:"Weź zjazd po lewej w kierunku {destination}"},"slight right":{default:"Weź zjazd po prawej",name:"Weź zjazd po prawej na {way_name}",destination:"Weź zjazd po prawej w kierunku {destination}"}},rotary:{default:{default:{default:"Wjedź na rondo",name:"Wjedź na rondo i skręć na {way_name}",destination:"Wjedź na rondo i skręć w kierunku {destination}"},name:{default:"Wjedź na {rotary_name}",name:"Wjedź na {rotary_name} i skręć na {way_name}",destination:"Wjedź na {rotary_name} i skręć w kierunku {destination}"},exit:{default:"Wjedź na rondo i wyjedź {exit_number} zjazdem",name:"Wjedź na rondo i wyjedź {exit_number} zjazdem na {way_name}",destination:"Wjedź na rondo i wyjedź {exit_number} zjazdem w kierunku {destination}"},name_exit:{default:"Wjedź na {rotary_name} i wyjedź {exit_number} zjazdem",name:"Wjedź na {rotary_name} i wyjedź {exit_number} zjazdem na {way_name}",destination:"Wjedź na {rotary_name} i wyjedź {exit_number} zjazdem w kierunku {destination}"}}},roundabout:{default:{exit:{default:"Wjedź na rondo i wyjedź {exit_number} zjazdem",name:"Wjedź na rondo i wyjedź {exit_number} zjazdem na {way_name}",destination:"Wjedź na rondo i wyjedź {exit_number} zjazdem w kierunku {destination}"},default:{default:"Wjedź na rondo",name:"Wjedź na rondo i wyjedź na {way_name}",destination:"Wjedź na rondo i wyjedź w kierunku {destination}"}}},"roundabout turn":{default:{default:"{modifier}",name:"{modifier} na {way_name}",destination:"{modifier} w kierunku {destination}"},left:{default:"Skręć w lewo",name:"Skręć w lewo na {way_name}",destination:"Skręć w lewo w kierunku {destination}"},right:{default:"Skręć w prawo",name:"Skręć w prawo na {way_name}",destination:"Skręć w prawo w kierunku {destination}"},straight:{default:"Kontynuuj prosto",name:"Kontynuuj prosto na {way_name}",destination:"Kontynuuj prosto w kierunku {destination}"}},"exit roundabout":{default:{default:"{modifier}",name:"{modifier} na {way_name}",destination:"{modifier} w kierunku {destination}"},left:{default:"Skręć w lewo",name:"Skręć w lewo na {way_name}",destination:"Skręć w lewo w kierunku {destination}"},right:{default:"Skręć w prawo",name:"Skręć w prawo na {way_name}",destination:"Skręć w prawo w kierunku {destination}"},straight:{default:"Kontynuuj prosto",name:"Kontynuuj prosto na {way_name}",destination:"Kontynuuj prosto w kierunku {destination}"}},"exit rotary":{default:{default:"{modifier}",name:"{modifier} na {way_name}",destination:"{modifier} w kierunku {destination}"},left:{default:"Skręć w lewo",name:"Skręć w lewo na {way_name}",destination:"Skręć w lewo w kierunku {destination}"},right:{default:"Skręć w prawo",name:"Skręć w prawo na {way_name}",destination:"Skręć w prawo w kierunku {destination}"},straight:{default:"Jedź prosto",name:"Jedź prosto na {way_name}",destination:"Jedź prosto w kierunku {destination}"}},turn:{default:{default:"{modifier}",name:"{modifier} na {way_name}",destination:"{modifier} w kierunku {destination}"},left:{default:"Skręć w lewo",name:"Skręć w lewo na {way_name}",destination:"Skręć w lewo w kierunku {destination}"},right:{default:"Skręć w prawo",name:"Skręć w prawo na {way_name}",destination:"Skręć w prawo w kierunku {destination}"},straight:{default:"Jedź prosto",name:"Jedź prosto na {way_name}",destination:"Jedź prosto w kierunku {destination}"}},"use lane":{no_lanes:{default:"Kontynuuj prosto"},default:{default:"{lane_instruction}"}}}}},{}],39:[function(_dereq_,module,exports){module.exports={meta:{capitalizeFirstLetter:true},v5:{constants:{ordinalize:{1:"1º",2:"2º",3:"3º",4:"4º",5:"5º",6:"6º",7:"7º",8:"8º",9:"9º",10:"10º"},direction:{north:"norte",northeast:"nordeste",east:"leste",southeast:"sudeste",south:"sul",southwest:"sudoeste",west:"oeste",northwest:"noroeste"},modifier:{left:"à esquerda",right:"à direita","sharp left":"fechada à esquerda","sharp right":"fechada à direita","slight left":"suave à esquerda","slight right":"suave à direita",straight:"em frente",uturn:"retorno"},lanes:{xo:"Mantenha-se à direita",ox:"Mantenha-se à esquerda",xox:"Mantenha-se ao centro",oxo:"Mantenha-se à esquerda ou direita"}},modes:{ferry:{default:"Pegue a balsa",name:"Pegue a balsa {way_name}",destination:"Pegue a balsa sentido {destination}"}},phrase:{"two linked by distance":"{instruction_one}, então, em {distance}, {instruction_two}","two linked":"{instruction_one}, então {instruction_two}","one in distance":"Em {distance}, {instruction_one}","name and ref":"{name} ({ref})","exit with number":"saída {exit}"},arrive:{default:{default:"Você chegou ao seu {nth} destino",upcoming:"Você chegará ao seu {nth} destino",short:"Você chegou","short-upcoming":"Você vai chegar",named:"Você chegou a {waypoint_name}"},left:{default:"Você chegou ao seu {nth} destino, à esquerda",upcoming:"Você chegará ao seu {nth} destino, à esquerda",short:"Você chegou","short-upcoming":"Você vai chegar",named:"Você chegou {waypoint_name}, à esquerda"},right:{default:"Você chegou ao seu {nth} destino, à direita",upcoming:"Você chegará ao seu {nth} destino, à direita",short:"Você chegou","short-upcoming":"Você vai chegar",named:"Você chegou {waypoint_name}, à direita"},"sharp left":{default:"Você chegou ao seu {nth} destino, à esquerda",upcoming:"Você chegará ao seu {nth} destino, à esquerda",short:"Você chegou","short-upcoming":"Você vai chegar",named:"Você chegou {waypoint_name}, à esquerda"},"sharp right":{default:"Você chegou ao seu {nth} destino, à direita",upcoming:"Você chegará ao seu {nth} destino, à direita",short:"Você chegou","short-upcoming":"Você vai chegar",named:"Você chegou {waypoint_name}, à direita"},"slight right":{default:"Você chegou ao seu {nth} destino, à direita",upcoming:"Você chegará ao seu {nth} destino, à direita",short:"Você chegou","short-upcoming":"Você vai chegar",named:"Você chegou {waypoint_name}, à direita"},"slight left":{default:"Você chegou ao seu {nth} destino, à esquerda",upcoming:"Você chegará ao seu {nth} destino, à esquerda",short:"Você chegou","short-upcoming":"Você vai chegar",named:"Você chegou {waypoint_name}, à esquerda"},straight:{default:"Você chegou ao seu {nth} destino, em frente",upcoming:"Você vai chegar ao seu {nth} destino, em frente",short:"Você chegou","short-upcoming":"Você vai chegar",named:"You have arrived at {waypoint_name}, straight ahead"}},continue:{default:{default:"Vire {modifier}",name:"Vire {modifier} para manter-se na {way_name}",destination:"Vire {modifier} sentido {destination}",exit:"Vire {modifier} em {way_name}"},straight:{default:"Continue em frente",name:"Continue em frente para manter-se na {way_name}",destination:"Continue em direção à {destination}",distance:"Continue em frente por {distance}",namedistance:"Continue na {way_name} por {distance}"},"sharp left":{default:"Faça uma curva fechada a esquerda",name:"Faça uma curva fechada a esquerda para manter-se na {way_name}",destination:"Faça uma curva fechada a esquerda sentido {destination}"},"sharp right":{default:"Faça uma curva fechada a direita",name:"Faça uma curva fechada a direita para manter-se na {way_name}",destination:"Faça uma curva fechada a direita sentido {destination}"},"slight left":{default:"Faça uma curva suave a esquerda",name:"Faça uma curva suave a esquerda para manter-se na {way_name}",destination:"Faça uma curva suave a esquerda em direção a {destination}"},"slight right":{default:"Faça uma curva suave a direita",
name:"Faça uma curva suave a direita para manter-se na {way_name}",destination:"Faça uma curva suave a direita em direção a {destination}"},uturn:{default:"Faça o retorno",name:"Faça o retorno e continue em {way_name}",destination:"Faça o retorno sentido {destination}"}},depart:{default:{default:"Siga {direction}",name:"Siga {direction} em {way_name}",namedistance:"Siga {direction} na {way_name} por {distance}"}},"end of road":{default:{default:"Vire {modifier}",name:"Vire {modifier} em {way_name}",destination:"Vire {modifier} sentido {destination}"},straight:{default:"Continue em frente",name:"Continue em frente em {way_name}",destination:"Continue em frente sentido {destination}"},uturn:{default:"Faça o retorno no fim da rua",name:"Faça o retorno em {way_name} no fim da rua",destination:"Faça o retorno sentido {destination} no fim da rua"}},fork:{default:{default:"Mantenha-se {modifier} na bifurcação",name:"Mantenha-se {modifier} na bifurcação em {way_name}",destination:"Mantenha-se {modifier} na bifurcação sentido {destination}"},"slight left":{default:"Mantenha-se à esquerda na bifurcação",name:"Mantenha-se à esquerda na bifurcação em {way_name}",destination:"Mantenha-se à esquerda na bifurcação sentido {destination}"},"slight right":{default:"Mantenha-se à direita na bifurcação",name:"Mantenha-se à direita na bifurcação em {way_name}",destination:"Mantenha-se à direita na bifurcação sentido {destination}"},"sharp left":{default:"Faça uma curva fechada à esquerda na bifurcação",name:"Faça uma curva fechada à esquerda em {way_name}",destination:"Faça uma curva fechada à esquerda sentido {destination}"},"sharp right":{default:"Faça uma curva fechada à direita na bifurcação",name:"Faça uma curva fechada à direita em {way_name}",destination:"Faça uma curva fechada à direita sentido {destination}"},uturn:{default:"Faça o retorno",name:"Faça o retorno em {way_name}",destination:"Faça o retorno sentido {destination}"}},merge:{default:{default:"Entre {modifier}",name:"Entre {modifier} na {way_name}",destination:"Entre {modifier} em direção à {destination}"},straight:{default:"Mesclar",name:"Entre reto na {way_name}",destination:"Entre reto em direção à {destination}"},"slight left":{default:"Entre à esquerda",name:"Entre à esquerda na {way_name}",destination:"Entre à esquerda em direção à {destination}"},"slight right":{default:"Entre à direita",name:"Entre à direita na {way_name}",destination:"Entre à direita em direção à {destination}"},"sharp left":{default:"Entre à esquerda",name:"Entre à esquerda na {way_name}",destination:"Entre à esquerda em direção à {destination}"},"sharp right":{default:"Entre à direita",name:"Entre à direita na {way_name}",destination:"Entre à direita em direção à {destination}"},uturn:{default:"Faça o retorno",name:"Faça o retorno em {way_name}",destination:"Faça o retorno sentido {destination}"}},"new name":{default:{default:"Continue {modifier}",name:"Continue {modifier} em {way_name}",destination:"Continue {modifier} sentido {destination}"},straight:{default:"Continue em frente",name:"Continue em {way_name}",destination:"Continue em direção à {destination}"},"sharp left":{default:"Faça uma curva fechada à esquerda",name:"Faça uma curva fechada à esquerda em {way_name}",destination:"Faça uma curva fechada à esquerda sentido {destination}"},"sharp right":{default:"Faça uma curva fechada à direita",name:"Faça uma curva fechada à direita em {way_name}",destination:"Faça uma curva fechada à direita sentido {destination}"},"slight left":{default:"Continue ligeiramente à esquerda",name:"Continue ligeiramente à esquerda em {way_name}",destination:"Continue ligeiramente à esquerda sentido {destination}"},"slight right":{default:"Continue ligeiramente à direita",name:"Continue ligeiramente à direita em {way_name}",destination:"Continue ligeiramente à direita sentido {destination}"},uturn:{default:"Faça o retorno",name:"Faça o retorno em {way_name}",destination:"Faça o retorno sentido {destination}"}},notification:{default:{default:"Continue {modifier}",name:"Continue {modifier} em {way_name}",destination:"Continue {modifier} sentido {destination}"},uturn:{default:"Faça o retorno",name:"Faça o retorno em {way_name}",destination:"Faça o retorno sentido {destination}"}},"off ramp":{default:{default:"Pegue a rampa",name:"Pegue a rampa em {way_name}",destination:"Pegue a rampa sentido {destination}",exit:"Pegue a saída {exit}",exit_destination:"Pegue a saída {exit} em direção à {destination}"},left:{default:"Pegue a rampa à esquerda",name:"Pegue a rampa à esquerda em {way_name}",destination:"Pegue a rampa à esquerda sentido {destination}",exit:"Pegue a saída {exit} à esquerda",exit_destination:"Pegue a saída {exit}  à esquerda em direção à {destination}"},right:{default:"Pegue a rampa à direita",name:"Pegue a rampa à direita em {way_name}",destination:"Pegue a rampa à direita sentido {destination}",exit:"Pegue a saída {exit} à direita",exit_destination:"Pegue a saída {exit} à direita em direção à {destination}"},"sharp left":{default:"Pegue a rampa à esquerda",name:"Pegue a rampa à esquerda em {way_name}",destination:"Pegue a rampa à esquerda sentido {destination}",exit:"Pegue a saída {exit} à esquerda",exit_destination:"Pegue a saída {exit}  à esquerda em direção à {destination}"},"sharp right":{default:"Pegue a rampa à direita",name:"Pegue a rampa à direita em {way_name}",destination:"Pegue a rampa à direita sentido {destination}",exit:"Pegue a saída {exit} à direita",exit_destination:"Pegue a saída {exit} à direita em direção à {destination}"},"slight left":{default:"Pegue a rampa à esquerda",name:"Pegue a rampa à esquerda em {way_name}",destination:"Pegue a rampa à esquerda sentido {destination}",exit:"Pegue a saída {exit} à esquerda",exit_destination:"Pegue a saída {exit}  à esquerda em direção à {destination}"},"slight right":{default:"Pegue a rampa à direita",name:"Pegue a rampa à direita em {way_name}",destination:"Pegue a rampa à direita sentido {destination}",exit:"Pegue a saída {exit} à direita",exit_destination:"Pegue a saída {exit} à direita em direção à {destination}"}},"on ramp":{default:{default:"Pegue a rampa",name:"Pegue a rampa em {way_name}",destination:"Pegue a rampa sentido {destination}"},left:{default:"Pegue a rampa à esquerda",name:"Pegue a rampa à esquerda em {way_name}",destination:"Pegue a rampa à esquerda sentido {destination}"},right:{default:"Pegue a rampa à direita",name:"Pegue a rampa à direita em {way_name}",destination:"Pegue a rampa à direita sentid {destination}"},"sharp left":{default:"Pegue a rampa à esquerda",name:"Pegue a rampa à esquerda em {way_name}",destination:"Pegue a rampa à esquerda sentido {destination}"},"sharp right":{default:"Pegue a rampa à direita",name:"Pegue a rampa à direita em {way_name}",destination:"Pegue a rampa à direita sentido {destination}"},"slight left":{default:"Pegue a rampa à esquerda",name:"Pegue a rampa à esquerda em {way_name}",destination:"Pegue a rampa à esquerda sentido {destination}"},"slight right":{default:"Pegue a rampa à direita",name:"Pegue a rampa à direita em {way_name}",destination:"Pegue a rampa à direita sentido {destination}"}},rotary:{default:{default:{default:"Entre na rotatória",name:"Entre na rotatória e saia na {way_name}",destination:"Entre na rotatória e saia sentido {destination}"},name:{default:"Entre em {rotary_name}",name:"Entre em {rotary_name} e saia em {way_name}",destination:"Entre em {rotary_name} e saia sentido {destination}"},exit:{default:"Entre na rotatória e pegue a {exit_number} saída",name:"Entre na rotatória e pegue a {exit_number} saída na {way_name}",destination:"Entre na rotatória e pegue a {exit_number} saída sentido {destination}"},name_exit:{default:"Entre em {rotary_name} e saia na {exit_number} saída",name:"Entre em {rotary_name} e saia na {exit_number} saída em {way_name}",destination:"Entre em {rotary_name} e saia na {exit_number} saída sentido {destination}"}}},roundabout:{default:{exit:{default:"Entre na rotatória e pegue a {exit_number} saída",name:"Entre na rotatória e pegue a {exit_number} saída na {way_name}",destination:"Entre na rotatória e pegue a {exit_number} saída sentido {destination}"},default:{default:"Entre na rotatória",name:"Entre na rotatória e saia na {way_name}",destination:"Entre na rotatória e saia sentido {destination}"}}},"roundabout turn":{default:{default:"Siga {modifier}",name:"Siga {modifier} em {way_name}",destination:"Siga {modifier} sentido {destination}"},left:{default:"Vire à esquerda",name:"Vire à esquerda em {way_name}",destination:"Vire à esquerda sentido {destination}"},right:{default:"Vire à direita",name:"Vire à direita em {way_name}",destination:"Vire à direita sentido {destination}"},straight:{default:"Continue em frente",name:"Continue em frente em {way_name}",destination:"Continue em frente sentido {destination}"}},"exit roundabout":{default:{default:"Saia da rotatória",name:"Exit the traffic circle onto {way_name}",destination:"Exit the traffic circle towards {destination}"}},"exit rotary":{default:{default:"Saia da rotatória",name:"Exit the traffic circle onto {way_name}",destination:"Exit the traffic circle towards {destination}"}},turn:{default:{default:"Siga {modifier}",name:"Siga {modifier} em {way_name}",destination:"Siga {modifier} sentido {destination}"},left:{default:"Vire à esquerda",name:"Vire à esquerda em {way_name}",destination:"Vire à esquerda sentido {destination}"},right:{default:"Vire à direita",name:"Vire à direita em {way_name}",destination:"Vire à direita sentido {destination}"},straight:{default:"Siga em frente",name:"Siga em frente em {way_name}",destination:"Siga em frente sentido {destination}"}},"use lane":{no_lanes:{default:"Continue em frente"},default:{default:"{lane_instruction}"}}}}},{}],40:[function(_dereq_,module,exports){module.exports={meta:{capitalizeFirstLetter:true},v5:{constants:{ordinalize:{1:"1º",2:"2º",3:"3º",4:"4º",5:"5º",6:"6º",7:"7º",8:"8º",9:"9º",10:"10º"},direction:{north:"norte",northeast:"nordeste",east:"este",southeast:"sudeste",south:"sul",southwest:"sudoeste",west:"oeste",northwest:"noroeste"},modifier:{left:"à esquerda",right:"à direita","sharp left":"acentuadamente à esquerda","sharp right":"acentuadamente à direita","slight left":"ligeiramente à esquerda","slight right":"ligeiramente à direita",straight:"em frente",uturn:"inversão de marcha"},lanes:{xo:"Mantenha-se à direita",ox:"Mantenha-se à esquerda",xox:"Mantenha-se ao meio",oxo:"Mantenha-se à esquerda ou à direita"}},modes:{ferry:{default:"Apanhe o ferry",name:"Apanhe o ferry {way_name}",destination:"Apanhe o ferry para {destination}"}},phrase:{"two linked by distance":"{instruction_one}, depois, a {distance}, {instruction_two}","two linked":"{instruction_one}, depois {instruction_two}","one in distance":"A {distance}, {instruction_one}","name and ref":"{name} ({ref})","exit with number":"saída {exit}"},arrive:{default:{default:"Chegou ao seu {nth} destino",upcoming:"Está a chegar ao seu {nth} destino",short:"Chegou","short-upcoming":"Está a chegar",named:"Chegou a {waypoint_name}"},left:{default:"Chegou ao seu {nth} destino, à esquerda",upcoming:"Está a chegar ao seu {nth} destino, à esquerda",short:"Chegou","short-upcoming":"Está a chegar",named:"Chegou a {waypoint_name}, à esquerda"},right:{default:"Chegou ao seu {nth} destino, à direita",upcoming:"Está a chegar ao seu {nth} destino, à direita",short:"Chegou","short-upcoming":"Está a chegar",named:"Chegou a {waypoint_name}, à direita"},"sharp left":{default:"Chegou ao seu {nth} destino, à esquerda",upcoming:"Está a chegar ao seu {nth} destino, à esquerda",short:"Chegou","short-upcoming":"Está a chegar",named:"Chegou a {waypoint_name}, à esquerda"},"sharp right":{default:"Chegou ao seu {nth} destino, à direita",upcoming:"Está a chegar ao seu {nth} destino, à direita",short:"Chegou","short-upcoming":"Está a chegar",named:"Chegou a {waypoint_name}, à direita"},"slight right":{default:"Chegou ao seu {nth} destino, à direita",upcoming:"Está a chegar ao seu {nth} destino, à direita",short:"Chegou","short-upcoming":"Está a chegar",named:"Chegou a {waypoint_name}, à direita"},"slight left":{default:"Chegou ao seu {nth} destino, à esquerda",upcoming:"Está a chegar ao seu {nth} destino, à esquerda",short:"Chegou","short-upcoming":"Está a chegar",named:"Chegou a {waypoint_name}, à esquerda"},straight:{default:"Chegou ao seu {nth} destino, em frente",upcoming:"Está a chegar ao seu {nth} destino, em frente",short:"Chegou","short-upcoming":"Está a chegar",named:"Chegou a {waypoint_name}, em frente"}},continue:{default:{default:"Vire {modifier}",name:"Vire {modifier} para se manter em {way_name}",destination:"Vire {modifier} em direção a {destination}",exit:"Vire {modifier} para {way_name}"},straight:{default:"Continue em frente",name:"Continue em frente para se manter em {way_name}",destination:"Continue em direção a {destination}",distance:"Continue em frente por {distance}",namedistance:"Continue em {way_name} por {distance}"},"sharp left":{default:"Vire acentuadamente à esquerda",name:"Vire acentuadamente à esquerda para se manter em {way_name}",destination:"Vire acentuadamente à esquerda em direção a {destination}"},"sharp right":{default:"Vire acentuadamente à direita",name:"Vire acentuadamente à direita para se manter em {way_name}",destination:"Vire acentuadamente à direita em direção a {destination}"},"slight left":{default:"Vire ligeiramente à esquerda",name:"Vire ligeiramente à esquerda para se manter em {way_name}",destination:"Vire ligeiramente à esquerda em direção a {destination}"},"slight right":{default:"Vire ligeiramente à direita",name:"Vire ligeiramente à direita para se manter em {way_name}",destination:"Vire ligeiramente à direita em direção a {destination}"},uturn:{default:"Faça inversão de marcha",name:"Faça inversão de marcha e continue em {way_name}",destination:"Faça inversão de marcha em direção a {destination}"}},depart:{default:{default:"Dirija-se para {direction}",name:"Dirija-se para {direction} em {way_name}",namedistance:"Dirija-se para {direction} em {way_name} por {distance}"}},"end of road":{default:{default:"Vire {modifier}",name:"Vire {modifier} para {way_name}",destination:"Vire {modifier} em direção a {destination}"},straight:{default:"Continue em frente",name:"Continue em frente para {way_name}",destination:"Continue em frente em direção a {destination}"},uturn:{default:"No final da estrada faça uma inversão de marcha",name:"No final da estrada faça uma inversão de marcha para {way_name} ",destination:"No final da estrada faça uma inversão de marcha em direção a {destination}"}},fork:{default:{default:"Na bifurcação mantenha-se {modifier}",name:"Mantenha-se {modifier} para {way_name}",destination:"Mantenha-se {modifier} em direção a {destination}"},"slight left":{default:"Na bifurcação mantenha-se à esquerda",name:"Mantenha-se à esquerda para {way_name}",destination:"Mantenha-se à esquerda em direção a {destination}"},"slight right":{default:"Na bifurcação mantenha-se à direita",name:"Mantenha-se à direita para {way_name}",destination:"Mantenha-se à direita em direção a {destination}"},"sharp left":{default:"Na bifurcação vire acentuadamente à esquerda",name:"Vire acentuadamente à esquerda para {way_name}",destination:"Vire acentuadamente à esquerda em direção a {destination}"},"sharp right":{default:"Na bifurcação vire acentuadamente à direita",name:"Vire acentuadamente à direita para {way_name}",destination:"Vire acentuadamente à direita em direção a {destination}"},uturn:{default:"Faça inversão de marcha",name:"Faça inversão de marcha para {way_name}",destination:"Faça inversão de marcha em direção a {destination}"}},merge:{default:{default:"Una-se ao tráfego {modifier}",name:"Una-se ao tráfego {modifier} para {way_name}",destination:"Una-se ao tráfego {modifier} em direção a {destination}"},straight:{default:"Una-se ao tráfego",name:" Una-se ao tráfego para {way_name}",destination:"Una-se ao tráfego em direção a {destination}"},"slight left":{default:"Una-se ao tráfego à esquerda",name:"Una-se ao tráfego à esquerda para {way_name}",destination:"Una-se ao tráfego à esquerda em direção a {destination}"},"slight right":{default:"Una-se ao tráfego à direita",name:"Una-se ao tráfego à direita para {way_name}",destination:"Una-se ao tráfego à direita em direção a {destination}"},"sharp left":{default:"Una-se ao tráfego à esquerda",name:"Una-se ao tráfego à esquerda para {way_name}",destination:"Una-se ao tráfego à esquerda em direção a {destination}"},"sharp right":{default:"Una-se ao tráfego à direita",name:"Una-se ao tráfego à direita para {way_name}",destination:"Una-se ao tráfego à direita em direção a {destination}"},uturn:{default:"Faça inversão de marcha",name:"Faça inversão de marcha para {way_name}",destination:"Faça inversão de marcha em direção a {destination}"}},"new name":{default:{default:"Continue {modifier}",name:"Continue {modifier} para {way_name}",destination:"Continue {modifier} em direção a {destination}"},straight:{default:"Continue em frente",name:"Continue para {way_name}",destination:"Continue em direção a {destination}"},"sharp left":{default:"Vire acentuadamente à esquerda",name:"Vire acentuadamente à esquerda para {way_name}",destination:"Vire acentuadamente à esquerda em direção a{destination}"},"sharp right":{default:"Vire acentuadamente à direita",name:"Vire acentuadamente à direita para {way_name}",destination:"Vire acentuadamente à direita em direção a {destination}"},"slight left":{default:"Continue ligeiramente à esquerda",name:"Continue ligeiramente à esquerda para {way_name}",destination:"Continue ligeiramente à esquerda em direção a {destination}"},"slight right":{default:"Continue ligeiramente à direita",name:"Continue ligeiramente à direita para {way_name}",destination:"Continue ligeiramente à direita em direção a {destination}"},uturn:{default:"Faça inversão de marcha",name:"Faça inversão de marcha para {way_name}",destination:"Faça inversão de marcha em direção a {destination}"}},notification:{default:{default:"Continue {modifier}",name:"Continue {modifier} para {way_name}",destination:"Continue {modifier} em direção a {destination}"},uturn:{default:"Faça inversão de marcha",name:"Faça inversão de marcha para {way_name}",destination:"Faça inversão de marcha em direção a {destination}"}},"off ramp":{default:{default:"Saia na saída",name:"Saia na saída para {way_name}",destination:"Saia na saída em direção a {destination}",exit:"Saia na saída {exit}",exit_destination:"Saia na saída {exit} em direção a {destination}"},left:{default:"Saia na saída à esquerda",name:"Saia na saída à esquerda para {way_name}",destination:"Saia na saída à esquerda em direção a {destination}",exit:"Saia na saída {exit} à esquerda",exit_destination:"Saia na saída {exit} à esquerda em direção a {destination}"},right:{default:"Saia na saída à direita",name:"Saia na saída à direita para {way_name}",destination:"Saia na saída à direita em direção a {destination}",exit:"Saia na saída {exit} à direita",exit_destination:"Saia na saída {exit} à direita em direção a {destination}"},"sharp left":{default:"Saia na saída à esquerda",name:"Saia na saída à esquerda para {way_name}",destination:"Saia na saída à esquerda em direção a {destination}",exit:"Saia na saída {exit} à esquerda",exit_destination:"Saia na saída {exit} à esquerda em direção a {destination}"},"sharp right":{default:"Saia na saída à direita",name:"Saia na saída à direita para {way_name}",destination:"Saia na saída à direita em direção a {destination}",exit:"Saia na saída {exit} à direita",exit_destination:"Saia na saída {exit} à direita em direção a {destination}"},"slight left":{default:"Saia na saída à esquerda",name:"Saia na saída à esquerda para {way_name}",destination:"Saia na saída à esquerda em direção a {destination}",exit:"Saia na saída {exit} à esquerda",exit_destination:"Saia na saída {exit} à esquerda em direção a {destination}"},"slight right":{default:"Saia na saída à direita",name:"Saia na saída à direita para {way_name}",destination:"Saia na saída à direita em direção a {destination}",exit:"Saia na saída {exit} à direita",exit_destination:"Saia na saída {exit} à direita em direção a {destination}"}},"on ramp":{default:{default:"Saia na saída",name:"Saia na saída para {way_name}",destination:"Saia na saída em direção a {destination}"},left:{default:"Saia na saída à esquerda",name:"Saia na saída à esquerda para {way_name}",destination:"Saia na saída à esquerda em direção a {destination}"},right:{default:"Saia na saída à direita",name:"Saia na saída à direita para {way_name}",destination:"Saia na saída à direita em direção a {destination}"},"sharp left":{default:"Saia na saída à esquerda",name:"Saia na saída à esquerda para {way_name}",destination:"Saia na saída à esquerda em direção a {destination}"},"sharp right":{default:"Saia na saída à direita",name:"Saia na saída à direita para {way_name}",destination:"Saia na saída à direita em direção a {destination}"},"slight left":{default:"Saia na saída à esquerda",name:"Saia na saída à esquerda para {way_name}",destination:"Saia na saída à esquerda em direção a {destination}"},"slight right":{default:"Saia na saída à direita",name:"Saia na saída à direita para {way_name}",destination:"Saia na saída à direita em direção a {destination}"}},rotary:{default:{default:{default:"Entre na rotunda",name:"Entre na rotunda e saia para {way_name}",destination:"Entre na rotunda e saia em direção a {destination}"},name:{default:"Entre em {rotary_name}",name:"Entre em {rotary_name} e saia para {way_name}",destination:"Entre em {rotary_name} e saia em direção a {destination}"},exit:{default:"Entre na rotunda e saia na saída {exit_number}",name:"Entre na rotunda e saia na saída {exit_number} para {way_name}",destination:"Entre na rotunda e saia na saída {exit_number} em direção a {destination}"},name_exit:{default:"Entre em {rotary_name} e saia na saída {exit_number}",name:"Entre em {rotary_name} e saia na saída {exit_number} para {way_name}",destination:"Entre em{rotary_name} e saia na saída {exit_number} em direção a {destination}"}}},roundabout:{default:{exit:{default:"Entre na rotunda e saia na saída {exit_number}",name:"Entre na rotunda e saia na saída {exit_number} para {way_name}",destination:"Entre na rotunda e saia na saída {exit_number} em direção a {destination}"},default:{default:"Entre na rotunda",name:"Entre na rotunda e saia para {way_name}",destination:"Entre na rotunda e saia em direção a {destination}"}}},"roundabout turn":{default:{default:"Siga {modifier}",name:"Siga {modifier} para {way_name}",destination:"Siga {modifier} em direção a {destination}"},left:{default:"Vire à esquerda",name:"Vire à esquerda para {way_name}",destination:"Vire à esquerda em direção a {destination}"},right:{default:"Vire à direita",name:"Vire à direita para {way_name}",destination:"Vire à direita em direção a {destination}"},straight:{default:"Continue em frente",name:"Continue em frente para {way_name}",destination:"Continue em frente em direção a {destination}"}},"exit roundabout":{default:{default:"Saia da rotunda",name:"Saia da rotunda para {way_name}",destination:"Saia da rotunda em direção a {destination}"}},"exit rotary":{default:{default:"Saia da rotunda",name:"Saia da rotunda para {way_name}",destination:"Saia da rotunda em direção a {destination}"}},turn:{default:{default:"Siga {modifier}",name:"Siga {modifier} para{way_name}",destination:"Siga {modifier} em direção a {destination}"},left:{default:"Vire à esquerda",name:"Vire à esquerda para {way_name}",destination:"Vire à esquerda em direção a {destination}"},right:{default:"Vire à direita",name:"Vire à direita para {way_name}",destination:"Vire à direita em direção a {destination}"},straight:{default:"Vá em frente",name:"Vá em frente para {way_name}",destination:"Vá em frente em direção a {destination}"}},"use lane":{no_lanes:{default:"Continue em frente"},default:{default:"{lane_instruction}"}}}}},{}],41:[function(_dereq_,module,exports){module.exports={meta:{capitalizeFirstLetter:true},v5:{constants:{ordinalize:{1:"prima",2:"a doua",3:"a treia",4:"a patra",5:"a cincea",6:"a șasea",7:"a șaptea",8:"a opta",9:"a noua",10:"a zecea"},direction:{north:"nord",northeast:"nord-est",east:"est",southeast:"sud-est",south:"sud",southwest:"sud-vest",west:"vest",northwest:"nord-vest"},modifier:{left:"stânga",right:"dreapta","sharp left":"puternic stânga","sharp right":"puternic dreapta","slight left":"ușor stânga","slight right":"ușor dreapta",straight:"înainte",uturn:"întoarcere"},lanes:{xo:"Țineți stânga",ox:"Țineți dreapta",xox:"Țineți pe mijloc",oxo:"Țineți pe laterale"}},modes:{ferry:{default:"Luați feribotul",name:"Luați feribotul {way_name}",destination:"Luați feribotul spre {destination}"}},phrase:{"two linked by distance":"{instruction_one}, apoi în {distance}, {instruction_two}","two linked":"{instruction_one} apoi {instruction_two}","one in distance":"În {distance}, {instruction_one}","name and ref":"{name} ({ref})","exit with number":"ieșirea {exit}"},arrive:{default:{default:"Ați ajuns la {nth} destinație",upcoming:"Ați ajuns la {nth} destinație",short:"Ați ajuns","short-upcoming":"Veți ajunge",named:"Ați ajuns {waypoint_name}"},left:{default:"Ați ajuns la {nth} destinație, pe stânga",upcoming:"Ați ajuns la {nth} destinație, pe stânga",short:"Ați ajuns","short-upcoming":"Veți ajunge",named:"Ați ajuns {waypoint_name}, pe stânga"},right:{default:"Ați ajuns la {nth} destinație, pe dreapta",upcoming:"Ați ajuns la {nth} destinație, pe dreapta",short:"Ați ajuns","short-upcoming":"Veți ajunge",named:"Ați ajuns {waypoint_name}, pe dreapta"},"sharp left":{default:"Ați ajuns la {nth} destinație, pe stânga",upcoming:"Ați ajuns la {nth} destinație, pe stânga",short:"Ați ajuns","short-upcoming":"Veți ajunge",named:"Ați ajuns {waypoint_name}, pe stânga"},"sharp right":{default:"Ați ajuns la {nth} destinație, pe dreapta",upcoming:"Ați ajuns la {nth} destinație, pe dreapta",short:"Ați ajuns","short-upcoming":"Veți ajunge",named:"Ați ajuns {waypoint_name}, pe dreapta"},"slight right":{default:"Ați ajuns la {nth} destinație, pe dreapta",upcoming:"Ați ajuns la {nth} destinație, pe dreapta",short:"Ați ajuns","short-upcoming":"Veți ajunge",named:"Ați ajuns {waypoint_name}, pe dreapta"},"slight left":{default:"Ați ajuns la {nth} destinație, pe stânga",upcoming:"Ați ajuns la {nth} destinație, pe stânga",short:"Ați ajuns","short-upcoming":"Veți ajunge",named:"Ați ajuns {waypoint_name}, pe stânga"},straight:{default:"Ați ajuns la {nth} destinație, în față",upcoming:"Ați ajuns la {nth} destinație, în față",short:"Ați ajuns","short-upcoming":"Veți ajunge",named:"Ați ajuns {waypoint_name}, în față"}},continue:{default:{default:"Virați {modifier}",name:"Virați {modifier} pe {way_name}",destination:"Virați {modifier} spre {destination}",exit:"Virați {modifier} pe {way_name}"},straight:{default:"Mergeți înainte",name:"Mergeți înainte pe {way_name}",destination:"Continuați spre {destination}",distance:"Mergeți înainte pentru {distance}",namedistance:"Continuați pe {way_name} pentru {distance}"},"sharp left":{default:"Virați puternic la stânga",name:"Virați puternic la stânga pe {way_name}",destination:"Virați puternic la stânga spre {destination}"},"sharp right":{default:"Virați puternic la dreapta",name:"Virați puternic la dreapta pe {way_name}",destination:"Virați puternic la dreapta spre {destination}"},"slight left":{default:"Virați ușor la stânga",name:"Virați ușor la stânga pe {way_name}",destination:"Virați ușor la stânga spre {destination}"},"slight right":{default:"Virați ușor la dreapta",name:"Virați ușor la dreapta pe {way_name}",destination:"Virați ușor la dreapta spre {destination}"},uturn:{default:"Întoarceți-vă",name:"Întoarceți-vă și continuați pe {way_name}",destination:"Întoarceți-vă spre {destination}"}},depart:{default:{default:"Mergeți spre {direction}",name:"Mergeți spre {direction} pe {way_name}",namedistance:"Mergeți spre {direction} pe {way_name} pentru {distance}"}},"end of road":{default:{default:"Virați {modifier}",name:"Virați {modifier} pe {way_name}",destination:"Virați {modifier} spre {destination}"},straight:{default:"Continuați înainte",name:"Continuați înainte pe {way_name}",destination:"Continuați înainte spre {destination}"},uturn:{default:"Întoarceți-vă la sfârșitul drumului",name:"Întoarceți-vă pe {way_name} la sfârșitul drumului",destination:"Întoarceți-vă spre {destination} la sfârșitul drumului"}},fork:{default:{default:"Țineți {modifier} la bifurcație",name:"Țineți {modifier} la bifurcație pe {way_name}",destination:"Țineți {modifier} la bifurcație spre {destination}"},"slight left":{default:"Țineți pe stânga la bifurcație",name:"Țineți pe stânga la bifurcație pe {way_name}",destination:"Țineți pe stânga la bifurcație spre {destination}"},"slight right":{default:"Țineți pe dreapta la bifurcație",name:"Țineți pe dreapta la bifurcație pe {way_name}",destination:"Țineți pe dreapta la bifurcație spre {destination}"},"sharp left":{default:"Virați puternic stânga la bifurcație",name:"Virați puternic stânga la bifurcație pe {way_name}",destination:"Virați puternic stânga la bifurcație spre {destination}"},"sharp right":{default:"Virați puternic dreapta la bifurcație",name:"Virați puternic dreapta la bifurcație pe {way_name}",destination:"Virați puternic dreapta la bifurcație spre {destination}"},uturn:{default:"Întoarceți-vă",name:"Întoarceți-vă pe {way_name}",destination:"Întoarceți-vă spre {destination}"}},merge:{default:{default:"Intrați în {modifier}",name:"Intrați în {modifier} pe {way_name}",destination:"Intrați în {modifier} spre {destination}"},straight:{default:"Intrați",name:"Intrați pe {way_name}",destination:"Intrați spre {destination}"},"slight left":{default:"Intrați în stânga",name:"Intrați în stânga pe {way_name}",destination:"Intrați în stânga spre {destination}"},"slight right":{default:"Intrați în dreapta",name:"Intrați în dreapta pe {way_name}",destination:"Intrați în dreapta spre {destination}"},"sharp left":{default:"Intrați în stânga",name:"Intrați în stânga pe {way_name}",destination:"Intrați în stânga spre {destination}"},"sharp right":{default:"Intrați în dreapta",name:"Intrați în dreapta pe {way_name}",destination:"Intrați în dreapta spre {destination}"},uturn:{default:"Întoarceți-vă",name:"Întoarceți-vă pe {way_name}",destination:"Întoarceți-vă spre {destination}"}},"new name":{default:{default:"Continuați {modifier}",name:"Continuați {modifier} pe {way_name}",destination:"Continuați {modifier} spre {destination}"},straight:{default:"Continuați înainte",name:"Continuați pe {way_name}",destination:"Continuați spre {destination}"},"sharp left":{default:"Virați puternic la stânga",name:"Virați puternic la stânga pe {way_name}",destination:"Virați puternic la stânga spre {destination}"},"sharp right":{default:"Virați puternic la dreapta",name:"Virați puternic la dreapta pe {way_name}",destination:"Virați puternic la dreapta spre {destination}"},"slight left":{default:"Continuați ușor la stânga",name:"Continuați ușor la stânga pe {way_name}",destination:"Continuați ușor la stânga spre {destination}"},"slight right":{default:"Continuați ușor la dreapta",name:"Continuați ușor la dreapta pe {way_name}",destination:"Continuați ușor la dreapta spre {destination}"},uturn:{default:"Întoarceți-vă",name:"Întoarceți-vă pe {way_name}",destination:"Întoarceți-vă spre {destination}"}},notification:{default:{default:"Continuați {modifier}",name:"Continuați {modifier} pe {way_name}",destination:"Continuați {modifier} spre {destination}"},uturn:{default:"Întoarceți-vă",name:"Întoarceți-vă pe {way_name}",destination:"Întoarceți-vă spre {destination}"}},"off ramp":{default:{default:"Urmați breteaua",name:"Urmați breteaua pe {way_name}",destination:"Urmați breteaua spre {destination}",exit:"Urmați ieșirea {exit}",exit_destination:"Urmați ieșirea {exit} spre {destination}"},left:{default:"Urmați breteaua din stânga",
name:"Urmați breteaua din stânga pe {way_name}",destination:"Urmați breteaua din stânga spre {destination}",exit:"Urmați ieșirea {exit} pe stânga",exit_destination:"Urmați ieșirea {exit} pe stânga spre {destination}"},right:{default:"Urmați breteaua din dreapta",name:"Urmați breteaua din dreapta pe {way_name}",destination:"Urmați breteaua din dreapta spre {destination}",exit:"Urmați ieșirea {exit} pe dreapta",exit_destination:"Urmați ieșirea {exit} pe dreapta spre {destination}"},"sharp left":{default:"Urmați breteaua din stânga",name:"Urmați breteaua din stânga pe {way_name}",destination:"Urmați breteaua din stânga spre {destination}",exit:"Urmați ieșirea {exit} pe stânga",exit_destination:"Urmați ieșirea {exit} pe stânga spre {destination}"},"sharp right":{default:"Urmați breteaua din dreapta",name:"Urmați breteaua din dreapta pe {way_name}",destination:"Urmați breteaua din dreapta spre {destination}",exit:"Urmați ieșirea {exit} pe dreapta",exit_destination:"Urmați ieșirea {exit} pe dreapta spre {destination}"},"slight left":{default:"Urmați breteaua din stânga",name:"Urmați breteaua din stânga pe {way_name}",destination:"Urmați breteaua din stânga spre {destination}",exit:"Urmați ieșirea {exit} pe stânga",exit_destination:"Urmați ieșirea {exit} pe stânga spre {destination}"},"slight right":{default:"Urmați breteaua din dreapta",name:"Urmați breteaua din dreapta pe {way_name}",destination:"Urmați breteaua din dreapta spre {destination}",exit:"Urmați ieșirea {exit} pe dreapta",exit_destination:"Urmați ieșirea {exit} pe dreapta spre {destination}"}},"on ramp":{default:{default:"Urmați breteaua de intrare",name:"Urmați breteaua pe {way_name}",destination:"Urmați breteaua spre {destination}"},left:{default:"Urmați breteaua din stânga",name:"Urmați breteaua din stânga pe {way_name}",destination:"Urmați breteaua din stânga spre {destination}"},right:{default:"Urmați breteaua din dreapta",name:"Urmați breteaua din dreapta pe {way_name}",destination:"Urmați breteaua din dreapta spre {destination}"},"sharp left":{default:"Urmați breteaua din stânga",name:"Urmați breteaua din stânga pe {way_name}",destination:"Urmați breteaua din stânga spre {destination}"},"sharp right":{default:"Urmați breteaua din dreapta",name:"Urmați breteaua din dreapta pe {way_name}",destination:"Urmați breteaua din dreapta spre {destination}"},"slight left":{default:"Urmați breteaua din stânga",name:"Urmați breteaua din stânga pe {way_name}",destination:"Urmați breteaua din stânga spre {destination}"},"slight right":{default:"Urmați breteaua din dreapta",name:"Urmați breteaua din dreapta pe {way_name}",destination:"Urmați breteaua din dreapta spre {destination}"}},rotary:{default:{default:{default:"Intrați în sensul giratoriu",name:"Intrați în sensul giratoriu și ieșiți pe {way_name}",destination:"Intrați în sensul giratoriu și ieșiți spre {destination}"},name:{default:"Intrați în {rotary_name}",name:"Intrați în {rotary_name} și ieșiți pe {way_name}",destination:"Intrați în {rotary_name} și ieșiți spre {destination}"},exit:{default:"Intrați în sensul giratoriu și urmați {exit_number} ieșire",name:"Intrați în sensul giratoriu și urmați {exit_number} ieșire pe {way_name}",destination:"Intrați în sensul giratoriu și urmați {exit_number} ieșire spre {destination}"},name_exit:{default:"Intrați în {rotary_name} și urmați {exit_number} ieșire",name:"Intrați în {rotary_name} și urmați {exit_number} ieșire pe {way_name}",destination:"Intrați în  {rotary_name} și urmați {exit_number} ieșire spre {destination}"}}},roundabout:{default:{exit:{default:"Intrați în sensul giratoriu și urmați {exit_number} ieșire",name:"Intrați în sensul giratoriu și urmați {exit_number} ieșire pe {way_name}",destination:"Intrați în sensul giratoriu și urmați {exit_number} ieșire spre {destination}"},default:{default:"Intrați în sensul giratoriu",name:"Intrați în sensul giratoriu și ieșiți pe {way_name}",destination:"Intrați în sensul giratoriu și ieșiți spre {destination}"}}},"roundabout turn":{default:{default:"La sensul giratoriu virați {modifier}",name:"La sensul giratoriu virați {modifier} pe {way_name}",destination:"La sensul giratoriu virați {modifier} spre {destination}"},left:{default:"La sensul giratoriu virați la stânga",name:"La sensul giratoriu virați la stânga pe {way_name}",destination:"La sensul giratoriu virați la stânga spre {destination}"},right:{default:"La sensul giratoriu virați la dreapta",name:"La sensul giratoriu virați la dreapta pe {way_name}",destination:"La sensul giratoriu virați la dreapta spre {destination}"},straight:{default:"La sensul giratoriu continuați înainte",name:"La sensul giratoriu continuați înainte pe {way_name}",destination:"La sensul giratoriu continuați înainte spre {destination}"}},"exit roundabout":{default:{default:"Ieșiți din sensul giratoriu",name:"Ieșiți din sensul giratoriu pe {way_name}",destination:"Ieșiți din sensul giratoriu spre {destination}"}},"exit rotary":{default:{default:"Ieșiți din sensul giratoriu",name:"Ieșiți din sensul giratoriu pe {way_name}",destination:"Ieșiți din sensul giratoriu spre {destination}"}},turn:{default:{default:"Virați {modifier}",name:"Virați {modifier} pe {way_name}",destination:"Virați {modifier} spre {destination}"},left:{default:"Virați la stânga",name:"Virați la stânga pe {way_name}",destination:"Virați la stânga spre {destination}"},right:{default:"Virați la dreapta",name:"Virați la dreapta pe {way_name}",destination:"Virați la dreapta spre {destination}"},straight:{default:"Mergeți înainte",name:"Mergeți înainte pe {way_name}",destination:"Mergeți înainte spre {destination}"}},"use lane":{no_lanes:{default:"Mergeți înainte"},default:{default:"{lane_instruction}"}}}}},{}],42:[function(_dereq_,module,exports){module.exports={meta:{capitalizeFirstLetter:true},v5:{constants:{ordinalize:{1:"первый",2:"второй",3:"третий",4:"четвёртый",5:"пятый",6:"шестой",7:"седьмой",8:"восьмой",9:"девятый",10:"десятый"},direction:{north:"северном",northeast:"северо-восточном",east:"восточном",southeast:"юго-восточном",south:"южном",southwest:"юго-западном",west:"западном",northwest:"северо-западном"},modifier:{left:"налево",right:"направо","sharp left":"налево","sharp right":"направо","slight left":"левее","slight right":"правее",straight:"прямо",uturn:"на разворот"},lanes:{xo:"Держитесь правее",ox:"Держитесь левее",xox:"Держитесь посередине",oxo:"Держитесь слева или справа"}},modes:{ferry:{default:"Погрузитесь на паром",name:"Погрузитесь на паром {way_name}",destination:"Погрузитесь на паром в направлении {destination}"}},phrase:{"two linked by distance":"{instruction_one}, затем через {distance} {instruction_two}","two linked":"{instruction_one}, затем {instruction_two}","one in distance":"Через {distance} {instruction_one}","name and ref":"{name} ({ref})","exit with number":"съезд {exit}"},arrive:{default:{default:"Вы прибыли в {nth} пункт назначения",upcoming:"Вы прибудете в {nth} пункт назначения",short:"Вы прибыли","short-upcoming":"Вы скоро прибудете",named:"Вы прибыли в пункт назначения, {waypoint_name}"},left:{default:"Вы прибыли в {nth} пункт назначения, он находится слева",upcoming:"Вы прибудете в {nth} пункт назначения, он будет слева",short:"Вы прибыли","short-upcoming":"Вы скоро прибудете",named:"Вы прибыли в пункт назначения, {waypoint_name}, он находится слева"},right:{default:"Вы прибыли в {nth} пункт назначения, он находится справа",upcoming:"Вы прибудете в {nth} пункт назначения, он будет справа",short:"Вы прибыли","short-upcoming":"Вы скоро прибудете",named:"Вы прибыли в пункт назначения, {waypoint_name}, он находится справа"},"sharp left":{default:"Вы прибыли в {nth} пункт назначения, он находится слева сзади",upcoming:"Вы прибудете в {nth} пункт назначения, он будет слева сзади",short:"Вы прибыли","short-upcoming":"Вы скоро прибудете",named:"Вы прибыли в пункт назначения, {waypoint_name}, он находится слева сзади"},"sharp right":{default:"Вы прибыли в {nth} пункт назначения, он находится справа сзади",upcoming:"Вы прибудете в {nth} пункт назначения, он будет справа сзади",short:"Вы прибыли","short-upcoming":"Вы скоро прибудете",named:"Вы прибыли в пункт назначения, {waypoint_name}, он находится справа сзади"},"slight right":{default:"Вы прибыли в {nth} пункт назначения, он находится справа впереди",upcoming:"Вы прибудете в {nth} пункт назначения, он будет справа впереди",short:"Вы прибыли","short-upcoming":"Вы скоро прибудете",named:"Вы прибыли в пункт назначения, {waypoint_name}, он находится справа впереди"},"slight left":{default:"Вы прибыли в {nth} пункт назначения, он находится слева впереди",upcoming:"Вы прибудете в {nth} пункт назначения, он будет слева впереди",short:"Вы прибыли","short-upcoming":"Вы скоро прибудете",named:"Вы прибыли в пункт назначения, {waypoint_name}, он находится слева впереди"},straight:{default:"Вы прибыли в {nth} пункт назначения, он находится перед Вами",upcoming:"Вы прибудете в {nth} пункт назначения, он будет перед Вами",short:"Вы прибыли","short-upcoming":"Вы скоро прибудете",named:"Вы прибыли в пункт назначения, {waypoint_name}, он находится перед Вами"}},continue:{default:{default:"Двигайтесь {modifier}",name:"Двигайтесь {modifier} по {way_name:dative}",destination:"Двигайтесь {modifier} в направлении {destination}",exit:"Двигайтесь {modifier} на {way_name:accusative}"},straight:{default:"Двигайтесь прямо",name:"Продолжите движение по {way_name:dative}",destination:"Продолжите движение в направлении {destination}",distance:"Двигайтесь прямо {distance}",namedistance:"Двигайтесь прямо {distance} по {way_name:dative}"},"sharp left":{default:"Резко поверните налево",name:"Резко поверните налево на {way_name:accusative}",destination:"Резко поверните налево в направлении {destination}"},"sharp right":{default:"Резко поверните направо",name:"Резко поверните направо на {way_name:accusative}",destination:"Резко поверните направо в направлении {destination}"},"slight left":{default:"Плавно поверните налево",name:"Плавно поверните налево на {way_name:accusative}",destination:"Плавно поверните налево в направлении {destination}"},"slight right":{default:"Плавно поверните направо",name:"Плавно поверните направо на {way_name:accusative}",destination:"Плавно поверните направо в направлении {destination}"},uturn:{default:"Развернитесь",name:"Развернитесь и продолжите движение по {way_name:dative}",destination:"Развернитесь в направлении {destination}"}},depart:{default:{default:"Двигайтесь в {direction} направлении",name:"Двигайтесь в {direction} направлении по {way_name:dative}",namedistance:"Двигайтесь {distance} в {direction} направлении по {way_name:dative}"}},"end of road":{default:{default:"Поверните {modifier}",name:"Поверните {modifier} на {way_name:accusative}",destination:"Поверните {modifier} в направлении {destination}"},straight:{default:"Двигайтесь прямо",name:"Двигайтесь прямо по {way_name:dative}",destination:"Двигайтесь прямо в направлении {destination}"},uturn:{default:"В конце дороги развернитесь",name:"Развернитесь в конце {way_name:genitive}",destination:"В конце дороги развернитесь в направлении {destination}"}},fork:{default:{default:"На развилке двигайтесь {modifier}",name:"На развилке двигайтесь {modifier} на {way_name:accusative}",destination:"На развилке двигайтесь {modifier} в направлении {destination}"},"slight left":{default:"На развилке держитесь левее",name:"На развилке держитесь левее на {way_name:accusative}",destination:"На развилке держитесь левее и продолжите движение в направлении {destination}"},"slight right":{default:"На развилке держитесь правее",name:"На развилке держитесь правее на {way_name:accusative}",destination:"На развилке держитесь правее и продолжите движение в направлении {destination}"},"sharp left":{default:"На развилке резко поверните налево",name:"Резко поверните налево на {way_name:accusative}",destination:"Резко поверните налево и продолжите движение в направлении {destination}"},"sharp right":{default:"На развилке резко поверните направо",name:"Резко поверните направо на {way_name:accusative}",destination:"Резко поверните направо и продолжите движение в направлении {destination}"},uturn:{default:"На развилке развернитесь",name:"На развилке развернитесь на {way_name:prepositional}",destination:"На развилке развернитесь и продолжите движение в направлении {destination}"}},merge:{default:{default:"Перестройтесь {modifier}",name:"Перестройтесь {modifier} на {way_name:accusative}",destination:"Перестройтесь {modifier} в направлении {destination}"},straight:{default:"Двигайтесь прямо",name:"Продолжите движение по {way_name:dative}",destination:"Продолжите движение в направлении {destination}"},"slight left":{default:"Перестройтесь левее",name:"Перестройтесь левее на {way_name:accusative}",destination:"Перестройтесь левее в направлении {destination}"},"slight right":{default:"Перестройтесь правее",name:"Перестройтесь правее на {way_name:accusative}",destination:"Перестройтесь правее в направлении {destination}"},"sharp left":{default:"Перестраивайтесь левее",name:"Перестраивайтесь левее на {way_name:accusative}",destination:"Перестраивайтесь левее в направлении {destination}"},"sharp right":{default:"Перестраивайтесь правее",name:"Перестраивайтесь правее на {way_name:accusative}",destination:"Перестраивайтесь правее в направлении {destination}"},uturn:{default:"Развернитесь",name:"Развернитесь на {way_name:prepositional}",destination:"Развернитесь в направлении {destination}"}},"new name":{default:{default:"Двигайтесь {modifier}",name:"Двигайтесь {modifier} на {way_name:accusative}",destination:"Двигайтесь {modifier} в направлении {destination}"},straight:{default:"Двигайтесь прямо",name:"Продолжите движение по {way_name:dative}",destination:"Продолжите движение в направлении {destination}"},"sharp left":{default:"Резко поверните налево",name:"Резко поверните налево на {way_name:accusative}",destination:"Резко поверните налево и продолжите движение в направлении {destination}"},"sharp right":{default:"Резко поверните направо",name:"Резко поверните направо на {way_name:accusative}",destination:"Резко поверните направо и продолжите движение в направлении {destination}"},"slight left":{default:"Плавно поверните налево",name:"Плавно поверните налево на {way_name:accusative}",destination:"Плавно поверните налево в направлении {destination}"},"slight right":{default:"Плавно поверните направо",name:"Плавно поверните направо на {way_name:accusative}",destination:"Плавно поверните направо в направлении {destination}"},uturn:{default:"Развернитесь",name:"Развернитесь на {way_name:prepositional}",destination:"Развернитесь и продолжите движение в направлении {destination}"}},notification:{default:{default:"Двигайтесь {modifier}",name:"Двигайтесь {modifier} по {way_name:dative}",destination:"Двигайтесь {modifier} в направлении {destination}"},uturn:{default:"Развернитесь",name:"Развернитесь на {way_name:prepositional}",destination:"Развернитесь и продолжите движение в направлении {destination}"}},"off ramp":{default:{default:"Сверните на съезд",name:"Сверните на съезд на {way_name:accusative}",destination:"Сверните на съезд в направлении {destination}",exit:"Сверните на съезд {exit}",exit_destination:"Сверните на съезд {exit} в направлении {destination}"},left:{default:"Сверните на левый съезд",name:"Сверните на левый съезд на {way_name:accusative}",destination:"Сверните на левый съезд в направлении {destination}",exit:"Сверните на съезд {exit} слева",exit_destination:"Сверните на съезд {exit} слева в направлении {destination}"},right:{default:"Сверните на правый съезд",name:"Сверните на правый съезд на {way_name:accusative}",destination:"Сверните на правый съезд в направлении {destination}",exit:"Сверните на съезд {exit} справа",exit_destination:"Сверните на съезд {exit} справа в направлении {destination}"},"sharp left":{default:"Поверните налево на съезд",name:"Поверните налево на съезд на {way_name:accusative}",destination:"Поверните налево на съезд в направлении {destination}",exit:"Поверните налево на съезд {exit}",exit_destination:"Поверните налево на съезд {exit} в направлении {destination}"},"sharp right":{default:"Поверните направо на съезд",name:"Поверните направо на съезд на {way_name:accusative}",destination:"Поверните направо на съезд в направлении {destination}",exit:"Поверните направо на съезд {exit}",exit_destination:"Поверните направо на съезд {exit} в направлении {destination}"},"slight left":{default:"Перестройтесь левее на съезд",name:"Перестройтесь левее на съезд на {way_name:accusative}",destination:"Перестройтесь левее на съезд в направлении {destination}",exit:"Перестройтесь левее на {exit}",exit_destination:"Перестройтесь левее на съезд {exit} в направлении {destination}"},"slight right":{default:"Перестройтесь правее на съезд",name:"Перестройтесь правее на съезд на {way_name:accusative}",destination:"Перестройтесь правее на съезд в направлении {destination}",exit:"Перестройтесь правее на съезд {exit}",exit_destination:"Перестройтесь правее на съезд {exit} в направлении {destination}"}},"on ramp":{default:{default:"Сверните на автомагистраль",name:"Сверните на въезд на {way_name:accusative}",destination:"Сверните на въезд на автомагистраль в направлении {destination}"},left:{default:"Сверните на левый въезд на автомагистраль",name:"Сверните на левый въезд на {way_name:accusative}",destination:"Сверните на левый въезд на автомагистраль в направлении {destination}"},right:{default:"Сверните на правый въезд на автомагистраль",name:"Сверните на правый въезд на {way_name:accusative}",destination:"Сверните на правый въезд на автомагистраль в направлении {destination}"},"sharp left":{default:"Поверните на левый въезд на автомагистраль",name:"Поверните на левый въезд на {way_name:accusative}",destination:"Поверните на левый въезд на автомагистраль в направлении {destination}"},"sharp right":{default:"Поверните на правый въезд на автомагистраль",name:"Поверните на правый въезд на {way_name:accusative}",destination:"Поверните на правый въезд на автомагистраль в направлении {destination}"},"slight left":{default:"Перестройтесь левее на въезд на автомагистраль",name:"Перестройтесь левее на {way_name:accusative}",destination:"Перестройтесь левее на автомагистраль в направлении {destination}"},"slight right":{default:"Перестройтесь правее на въезд на автомагистраль",name:"Перестройтесь правее на {way_name:accusative}",destination:"Перестройтесь правее на автомагистраль в направлении {destination}"}},rotary:{default:{default:{default:"Продолжите движение по круговой развязке",name:"На круговой развязке сверните на {way_name:accusative}",destination:"На круговой развязке сверните в направлении {destination}"},name:{default:"Продолжите движение по {rotary_name:dative}",name:"На {rotary_name:prepositional} сверните на {way_name:accusative}",destination:"На {rotary_name:prepositional} сверните в направлении {destination}"},exit:{default:"На круговой развязке сверните на {exit_number} съезд",name:"На круговой развязке сверните на {exit_number} съезд на {way_name:accusative}",destination:"На круговой развязке сверните на {exit_number} съезд в направлении {destination}"},name_exit:{default:"На {rotary_name:prepositional} сверните на {exit_number} съезд",name:"На {rotary_name:prepositional} сверните на {exit_number} съезд на {way_name:accusative}",destination:"На {rotary_name:prepositional} сверните на {exit_number} съезд в направлении {destination}"}}},roundabout:{default:{exit:{default:"На круговой развязке сверните на {exit_number} съезд",name:"На круговой развязке сверните на {exit_number} съезд на {way_name:accusative}",destination:"На круговой развязке сверните на {exit_number} съезд в направлении {destination}"},default:{default:"Продолжите движение по круговой развязке",name:"На круговой развязке сверните на {way_name:accusative}",destination:"На круговой развязке сверните в направлении {destination}"}}},"roundabout turn":{default:{default:"Двигайтесь {modifier}",name:"Двигайтесь {modifier} на {way_name:accusative}",destination:"Двигайтесь {modifier} в направлении {destination}"},left:{default:"Сверните налево",name:"Сверните налево на {way_name:accusative}",destination:"Сверните налево в направлении {destination}"},right:{default:"Сверните направо",name:"Сверните направо на {way_name:accusative}",destination:"Сверните направо в направлении {destination}"},straight:{default:"Двигайтесь прямо",name:"Двигайтесь прямо по {way_name:dative}",destination:"Двигайтесь прямо в направлении {destination}"}},"exit roundabout":{default:{default:"Сверните с круговой развязки",name:"Сверните с круговой развязки на {way_name:accusative}",destination:"Сверните с круговой развязки в направлении {destination}"}},"exit rotary":{default:{default:"Сверните с круговой развязки",name:"Сверните с круговой развязки на {way_name:accusative}",destination:"Сверните с круговой развязки в направлении {destination}"}},turn:{default:{default:"Двигайтесь {modifier}",name:"Двигайтесь {modifier} на {way_name:accusative}",destination:"Двигайтесь {modifier}  в направлении {destination}"},left:{default:"Поверните налево",name:"Поверните налево на {way_name:accusative}",destination:"Поверните налево в направлении {destination}"},right:{default:"Поверните направо",name:"Поверните направо на {way_name:accusative}",destination:"Поверните направо  в направлении {destination}"},straight:{default:"Двигайтесь прямо",name:"Двигайтесь по {way_name:dative}",destination:"Двигайтесь в направлении {destination}"}},"use lane":{no_lanes:{default:"Продолжайте движение прямо"},default:{default:"{lane_instruction}"}}}}},{}],43:[function(_dereq_,module,exports){module.exports={meta:{capitalizeFirstLetter:true},v5:{constants:{ordinalize:{1:"1:a",2:"2:a",3:"3:e",4:"4:e",5:"5:e",6:"6:e",7:"7:e",8:"8:e",9:"9:e",10:"10:e"},direction:{north:"norr",northeast:"nordost",east:"öster",southeast:"sydost",south:"söder",southwest:"sydväst",west:"väster",northwest:"nordväst"},modifier:{left:"vänster",right:"höger","sharp left":"vänster","sharp right":"höger","slight left":"vänster","slight right":"höger",straight:"rakt fram",uturn:"U-sväng"},lanes:{xo:"Håll till höger",ox:"Håll till vänster",xox:"Håll till mitten",oxo:"Håll till vänster eller höger"}},modes:{ferry:{default:"Ta färjan",name:"Ta färjan på {way_name}",destination:"Ta färjan mot {destination}"}},phrase:{"two linked by distance":"{instruction_one}, sedan efter {distance}, {instruction_two}","two linked":"{instruction_one}, sedan {instruction_two}","one in distance":"Om {distance}, {instruction_one}","name and ref":"{name} ({ref})","exit with number":"exit {exit}"},arrive:{default:{default:"Du är framme vid din {nth} destination",upcoming:"Du är snart framme vid din {nth} destination",short:"Du är framme","short-upcoming":"Du är snart framme",named:"Du är framme vid {waypoint_name}"},left:{default:"Du är framme vid din {nth} destination, till vänster",upcoming:"Du är snart framme vid din {nth} destination, till vänster",short:"Du är framme","short-upcoming":"Du är snart framme",named:"Du är framme vid {waypoint_name}, till vänster"},right:{default:"Du är framme vid din {nth} destination, till höger",upcoming:"Du är snart framme vid din {nth} destination, till höger",short:"Du är framme","short-upcoming":"Du är snart framme",named:"Du är framme vid {waypoint_name}, till höger"},"sharp left":{default:"Du är framme vid din {nth} destination, till vänster",upcoming:"Du är snart framme vid din {nth} destination, till vänster",short:"Du är framme","short-upcoming":"Du är snart framme",named:"Du är framme vid {waypoint_name}, till vänster"},"sharp right":{default:"Du är framme vid din {nth} destination, till höger",upcoming:"Du är snart framme vid din {nth} destination, till höger",short:"Du är framme","short-upcoming":"Du är snart framme",named:"Du är framme vid {waypoint_name}, till höger"},"slight right":{default:"Du är framme vid din {nth} destination, till höger",upcoming:"Du är snart framme vid din {nth} destination, till höger",short:"Du är framme","short-upcoming":"Du är snart framme",named:"Du är framme vid {waypoint_name}, till höger"},"slight left":{default:"Du är framme vid din {nth} destination, till vänster",upcoming:"Du är snart framme vid din {nth} destination, till vänster",short:"Du är framme","short-upcoming":"Du är snart framme",named:"Du är framme vid {waypoint_name}, till vänster"},straight:{default:"Du är framme vid din {nth} destination, rakt fram",upcoming:"Du är snart framme vid din {nth} destination, rakt fram",short:"Du är framme","short-upcoming":"Du är snart framme",named:"Du är framme vid {waypoint_name}, rakt fram"}},continue:{default:{default:"Sväng {modifier}",name:"Sväng {modifier} och fortsätt på {way_name}",destination:"Sväng {modifier} mot {destination}",exit:"Sväng {modifier} in på {way_name}"},straight:{default:"Fortsätt rakt fram",name:"Kör rakt fram och fortsätt på {way_name}",destination:"Fortsätt mot {destination}",distance:"Fortsätt rakt fram i {distance}",namedistance:"Fortsätt på {way_name} i {distance}"},"sharp left":{default:"Sväng vänster",name:"Sväng vänster och fortsätt på {way_name}",destination:"Sväng vänster mot {destination}"},"sharp right":{default:"Sväng höger",name:"Sväng höger och fortsätt på {way_name}",destination:"Sväng höger mot {destination}"},"slight left":{default:"Sväng vänster",name:"Sväng vänster och fortsätt på {way_name}",destination:"Sväng vänster mot {destination}"},"slight right":{default:"Sväng höger",name:"Sväng höger och fortsätt på {way_name}",destination:"Sväng höger mot {destination}"},uturn:{default:"Gör en U-sväng",name:"Gör en U-sväng och fortsätt på {way_name}",destination:"Gör en U-sväng mot {destination}"}},depart:{default:{default:"Kör åt {direction}",name:"Kör åt {direction} på {way_name}",namedistance:"Kör {distance} åt {direction} på {way_name}"}},"end of road":{default:{default:"Sväng {modifier}",name:"Sväng {modifier} in på {way_name}",destination:"Sväng {modifier} mot {destination}"},straight:{default:"Fortsätt rakt fram",name:"Fortsätt rakt fram in på {way_name}",destination:"Fortsätt rakt fram mot {destination}"},uturn:{default:"Gör en U-sväng i slutet av vägen",name:"Gör en U-sväng in på {way_name} i slutet av vägen",destination:"Gör en U-sväng mot {destination} i slutet av vägen"}},fork:{default:{default:"Håll till {modifier} där vägen delar sig",name:"Håll till {modifier} in på {way_name}",destination:"Håll till {modifier} mot {destination}"},"slight left":{default:"Håll till vänster där vägen delar sig",name:"Håll till vänster in på {way_name}",destination:"Håll till vänster mot {destination}"},"slight right":{default:"Håll till höger där vägen delar sig",name:"Håll till höger in på {way_name}",destination:"Håll till höger mot {destination}"},"sharp left":{default:"Sväng vänster där vägen delar sig",name:"Sväng vänster in på {way_name}",destination:"Sväng vänster mot {destination}"},"sharp right":{default:"Sväng höger där vägen delar sig",name:"Sväng höger in på {way_name}",destination:"Sväng höger mot {destination}"},uturn:{default:"Gör en U-sväng",name:"Gör en U-sväng in på {way_name}",destination:"Gör en U-sväng mot {destination}"}},merge:{default:{default:"Byt till {modifier} körfält",name:"Byt till {modifier} körfält, in på {way_name}",destination:"Byt till {modifier} körfält, mot {destination}"},straight:{default:"Fortsätt",name:"Kör in på {way_name}",destination:"Kör mot {destination}"},"slight left":{default:"Byt till vänstra körfältet",name:"Byt till vänstra körfältet, in på {way_name}",destination:"Byt till vänstra körfältet, mot {destination}"},"slight right":{default:"Byt till högra körfältet",name:"Byt till högra körfältet, in på {way_name}",destination:"Byt till högra körfältet, mot {destination}"},"sharp left":{default:"Byt till vänstra körfältet",name:"Byt till vänstra körfältet, in på {way_name}",destination:"Byt till vänstra körfältet, mot {destination}"},"sharp right":{default:"Byt till högra körfältet",name:"Byt till högra körfältet, in på {way_name}",destination:"Byt till högra körfältet, mot {destination}"},uturn:{default:"Gör en U-sväng",name:"Gör en U-sväng in på {way_name}",destination:"Gör en U-sväng mot {destination}"}},"new name":{default:{default:"Fortsätt {modifier}",name:"Fortsätt {modifier} på {way_name}",destination:"Fortsätt {modifier} mot {destination}"},straight:{default:"Fortsätt rakt fram",name:"Fortsätt in på {way_name}",destination:"Fortsätt mot {destination}"},"sharp left":{default:"Gör en skarp vänstersväng",name:"Gör en skarp vänstersväng in på {way_name}",destination:"Gör en skarp vänstersväng mot {destination}"},"sharp right":{default:"Gör en skarp högersväng",name:"Gör en skarp högersväng in på {way_name}",destination:"Gör en skarp högersväng mot {destination}"},"slight left":{default:"Fortsätt med lätt vänstersväng",name:"Fortsätt med lätt vänstersväng in på {way_name}",destination:"Fortsätt med lätt vänstersväng mot {destination}"},"slight right":{default:"Fortsätt med lätt högersväng",name:"Fortsätt med lätt högersväng in på {way_name}",destination:"Fortsätt med lätt högersväng mot {destination}"},uturn:{default:"Gör en U-sväng",name:"Gör en U-sväng in på {way_name}",destination:"Gör en U-sväng mot {destination}"}},notification:{default:{default:"Fortsätt {modifier}",name:"Fortsätt {modifier} på {way_name}",destination:"Fortsätt {modifier} mot {destination}"},uturn:{default:"Gör en U-sväng",name:"Gör en U-sväng in på {way_name}",destination:"Gör en U-sväng mot {destination}"}},"off ramp":{default:{default:"Ta avfarten",name:"Ta avfarten in på {way_name}",destination:"Ta avfarten mot {destination}",exit:"Ta avfart {exit} ",exit_destination:"Ta avfart {exit} mot {destination}"},left:{default:"Ta avfarten till vänster",name:"Ta avfarten till vänster in på {way_name}",destination:"Ta avfarten till vänster mot {destination}",exit:"Ta avfart {exit} till vänster",exit_destination:"Ta avfart {exit} till vänster mot {destination}"},right:{default:"Ta avfarten till höger",name:"Ta avfarten till höger in på {way_name}",destination:"Ta avfarten till höger mot {destination}",exit:"Ta avfart {exit} till höger",exit_destination:"Ta avfart {exit} till höger mot {destination}"},"sharp left":{default:"Ta avfarten till vänster",name:"Ta avfarten till vänster in på {way_name}",destination:"Ta avfarten till vänster mot {destination}",exit:"Ta avfart {exit} till vänster",exit_destination:"Ta avfart {exit} till vänster mot {destination}"},"sharp right":{default:"Ta avfarten till höger",name:"Ta avfarten till höger in på {way_name}",destination:"Ta avfarten till höger mot {destination}",exit:"Ta avfart {exit} till höger",exit_destination:"Ta avfart {exit} till höger mot {destination}"},"slight left":{default:"Ta avfarten till vänster",name:"Ta avfarten till vänster in på {way_name}",destination:"Ta avfarten till vänster mot {destination}",exit:"Ta avfart {exit} till vänster",exit_destination:"Ta avfart{exit} till vänster mot {destination}"},"slight right":{default:"Ta avfarten till höger",name:"Ta avfarten till höger in på {way_name}",destination:"Ta avfarten till höger mot {destination}",exit:"Ta avfart {exit} till höger",exit_destination:"Ta avfart {exit} till höger mot {destination}"}},"on ramp":{default:{default:"Ta påfarten",name:"Ta påfarten in på {way_name}",destination:"Ta påfarten mot {destination}"},left:{default:"Ta påfarten till vänster",name:"Ta påfarten till vänster in på {way_name}",destination:"Ta påfarten till vänster mot {destination}"},right:{default:"Ta påfarten till höger",name:"Ta påfarten till höger in på {way_name}",destination:"Ta påfarten till höger mot {destination}"},"sharp left":{default:"Ta påfarten till vänster",name:"Ta påfarten till vänster in på {way_name}",destination:"Ta påfarten till vänster mot {destination}"},"sharp right":{default:"Ta påfarten till höger",name:"Ta påfarten till höger in på {way_name}",destination:"Ta påfarten till höger mot {destination}"},"slight left":{default:"Ta påfarten till vänster",
name:"Ta påfarten till vänster in på {way_name}",destination:"Ta påfarten till vänster mot {destination}"},"slight right":{default:"Ta påfarten till höger",name:"Ta påfarten till höger in på {way_name}",destination:"Ta påfarten till höger mot {destination}"}},rotary:{default:{default:{default:"Kör in i rondellen",name:"I rondellen, ta avfarten in på {way_name}",destination:"I rondellen, ta av mot {destination}"},name:{default:"Kör in i {rotary_name}",name:"I {rotary_name}, ta av in på {way_name}",destination:"I {rotary_name}, ta av mot {destination}"},exit:{default:"I rondellen, ta {exit_number} avfarten",name:"I rondellen, ta {exit_number} avfarten in på {way_name}",destination:"I rondellen, ta {exit_number} avfarten mot {destination}"},name_exit:{default:"I {rotary_name}, ta {exit_number} avfarten",name:"I {rotary_name}, ta {exit_number}  avfarten in på {way_name}",destination:"I {rotary_name}, ta {exit_number} avfarten mot {destination}"}}},roundabout:{default:{exit:{default:"I rondellen, ta {exit_number} avfarten",name:"I rondellen, ta {exit_number} avfarten in på {way_name}",destination:"I rondellen, ta {exit_number} avfarten mot {destination}"},default:{default:"Kör in i rondellen",name:"I rondellen, ta avfarten in på {way_name}",destination:"I rondellen, ta av mot {destination}"}}},"roundabout turn":{default:{default:"Sväng {modifier}",name:"Sväng {modifier} in på {way_name}",destination:"Sväng {modifier} mot {destination}"},left:{default:"Sväng vänster",name:"Sväng vänster in på {way_name}",destination:"Sväng vänster mot {destination}"},right:{default:"Sväng höger",name:"Sväng höger in på {way_name}",destination:"Sväng höger mot {destination}"},straight:{default:"Fortsätt rakt fram",name:"Fortsätt rakt fram in på {way_name}",destination:"Fortsätt rakt fram mot {destination}"}},"exit roundabout":{default:{default:"Kör ut ur rondellen",name:"Kör ut ur rondellen in på {way_name}",destination:"Kör ut ur rondellen mot {destination}"}},"exit rotary":{default:{default:"Kör ut ur rondellen",name:"Kör ut ur rondellen in på {way_name}",destination:"Kör ut ur rondellen mot {destination}"}},turn:{default:{default:"Sväng {modifier}",name:"Sväng {modifier} in på {way_name}",destination:"Sväng {modifier} mot {destination}"},left:{default:"Sväng vänster",name:"Sväng vänster in på {way_name}",destination:"Sväng vänster mot {destination}"},right:{default:"Sväng höger",name:"Sväng höger in på {way_name}",destination:"Sväng höger mot {destination}"},straight:{default:"Kör rakt fram",name:"Kör rakt fram in på {way_name}",destination:"Kör rakt fram mot {destination}"}},"use lane":{no_lanes:{default:"Fortsätt rakt fram"},default:{default:"{lane_instruction}"}}}}},{}],44:[function(_dereq_,module,exports){module.exports={meta:{capitalizeFirstLetter:true},v5:{constants:{ordinalize:{1:"birinci",2:"ikinci",3:"üçüncü",4:"dördüncü",5:"beşinci",6:"altıncı",7:"yedinci",8:"sekizinci",9:"dokuzuncu",10:"onuncu"},direction:{north:"kuzey",northeast:"kuzeydoğu",east:"doğu",southeast:"güneydoğu",south:"güney",southwest:"güneybatı",west:"batı",northwest:"kuzeybatı"},modifier:{left:"sol",right:"sağ","sharp left":"keskin sol","sharp right":"keskin sağ","slight left":"hafif sol","slight right":"hafif sağ",straight:"düz",uturn:"U dönüşü"},lanes:{xo:"Sağda kalın",ox:"Solda kalın",xox:"Ortada kalın",oxo:"Solda veya sağda kalın"}},modes:{ferry:{default:"Vapur kullan",name:"{way_name} vapurunu kullan",destination:"{destination} istikametine giden vapuru kullan"}},phrase:{"two linked by distance":"{instruction_one} ve {distance} sonra {instruction_two}","two linked":"{instruction_one} ve sonra {instruction_two}","one in distance":"{distance} sonra, {instruction_one}","name and ref":"{name} ({ref})","exit with number":"exit {exit}"},arrive:{default:{default:"{nth} hedefinize ulaştınız",upcoming:"{nth} hedefinize ulaştınız",short:"{nth} hedefinize ulaştınız","short-upcoming":"{nth} hedefinize ulaştınız",named:"{waypoint_name} ulaştınız"},left:{default:"{nth} hedefinize ulaştınız, hedefiniz solunuzdadır",upcoming:"{nth} hedefinize ulaştınız, hedefiniz solunuzdadır",short:"{nth} hedefinize ulaştınız","short-upcoming":"{nth} hedefinize ulaştınız",named:"{waypoint_name} ulaştınız, hedefiniz solunuzdadır"},right:{default:"{nth} hedefinize ulaştınız, hedefiniz sağınızdadır",upcoming:"{nth} hedefinize ulaştınız, hedefiniz sağınızdadır",short:"{nth} hedefinize ulaştınız","short-upcoming":"{nth} hedefinize ulaştınız",named:"{waypoint_name} ulaştınız, hedefiniz sağınızdadır"},"sharp left":{default:"{nth} hedefinize ulaştınız, hedefiniz solunuzdadır",upcoming:"{nth} hedefinize ulaştınız, hedefiniz solunuzdadır",short:"{nth} hedefinize ulaştınız","short-upcoming":"{nth} hedefinize ulaştınız",named:"{waypoint_name} ulaştınız, hedefiniz solunuzdadır"},"sharp right":{default:"{nth} hedefinize ulaştınız, hedefiniz sağınızdadır",upcoming:"{nth} hedefinize ulaştınız, hedefiniz sağınızdadır",short:"{nth} hedefinize ulaştınız","short-upcoming":"{nth} hedefinize ulaştınız",named:"{waypoint_name} ulaştınız, hedefiniz sağınızdadır"},"slight right":{default:"{nth} hedefinize ulaştınız, hedefiniz sağınızdadır",upcoming:"{nth} hedefinize ulaştınız, hedefiniz sağınızdadır",short:"{nth} hedefinize ulaştınız","short-upcoming":"{nth} hedefinize ulaştınız",named:"{waypoint_name} ulaştınız, hedefiniz sağınızdadır"},"slight left":{default:"{nth} hedefinize ulaştınız, hedefiniz solunuzdadır",upcoming:"{nth} hedefinize ulaştınız, hedefiniz solunuzdadır",short:"{nth} hedefinize ulaştınız","short-upcoming":"{nth} hedefinize ulaştınız",named:"{waypoint_name} ulaştınız, hedefiniz solunuzdadır"},straight:{default:"{nth} hedefinize ulaştınız, hedefiniz karşınızdadır",upcoming:"{nth} hedefinize ulaştınız, hedefiniz karşınızdadır",short:"{nth} hedefinize ulaştınız","short-upcoming":"{nth} hedefinize ulaştınız",named:"{waypoint_name} ulaştınız, hedefiniz karşınızdadır"}},continue:{default:{default:"{modifier} yöne dön",name:"{way_name} üzerinde kalmak için {modifier} yöne dön",destination:"{destination} istikametinde {modifier} yöne dön",exit:"{way_name} üzerinde {modifier} yöne dön"},straight:{default:"Düz devam edin",name:"{way_name} üzerinde kalmak için düz devam et",destination:"{destination} istikametinde devam et",distance:"{distance} boyunca düz devam et",namedistance:"{distance} boyunca {way_name} üzerinde devam et"},"sharp left":{default:"Sola keskin dönüş yap",name:"{way_name} üzerinde kalmak için sola keskin dönüş yap",destination:"{destination} istikametinde sola keskin dönüş yap"},"sharp right":{default:"Sağa keskin dönüş yap",name:"{way_name} üzerinde kalmak için sağa keskin dönüş yap",destination:"{destination} istikametinde sağa keskin dönüş yap"},"slight left":{default:"Sola hafif dönüş yap",name:"{way_name} üzerinde kalmak için sola hafif dönüş yap",destination:"{destination} istikametinde sola hafif dönüş yap"},"slight right":{default:"Sağa hafif dönüş yap",name:"{way_name} üzerinde kalmak için sağa hafif dönüş yap",destination:"{destination} istikametinde sağa hafif dönüş yap"},uturn:{default:"U dönüşü yapın",name:"Bir U-dönüşü yap ve {way_name} devam et",destination:"{destination} istikametinde bir U-dönüşü yap"}},depart:{default:{default:"{direction} tarafına yönelin",name:"{way_name} üzerinde {direction} yöne git",namedistance:"Head {direction} on {way_name} for {distance}"}},"end of road":{default:{default:"{modifier} tarafa dönün",name:"{way_name} üzerinde {modifier} yöne dön",destination:"{destination} istikametinde {modifier} yöne dön"},straight:{default:"Düz devam edin",name:"{way_name} üzerinde düz devam et",destination:"{destination} istikametinde düz devam et"},uturn:{default:"Yolun sonunda U dönüşü yapın",name:"Yolun sonunda {way_name} üzerinde bir U-dönüşü yap",destination:"Yolun sonunda {destination} istikametinde bir U-dönüşü yap"}},fork:{default:{default:"Yol ayrımında {modifier} yönde kal",name:"{way_name} üzerindeki yol ayrımında {modifier} yönde kal",destination:"{destination} istikametindeki yol ayrımında {modifier} yönde kal"},"slight left":{default:"Çatalın solundan devam edin",name:"Çatalın solundan {way_name} yoluna doğru ",destination:"{destination} istikametindeki yol ayrımında solda kal"},"slight right":{default:"Çatalın sağından devam edin",name:"{way_name} üzerindeki yol ayrımında sağda kal",destination:"{destination} istikametindeki yol ayrımında sağda kal"},"sharp left":{default:"Çatalda keskin sola dönün",name:"{way_name} yoluna doğru sola keskin dönüş yapın",destination:"{destination} istikametinde sola keskin dönüş yap"},"sharp right":{default:"Çatalda keskin sağa dönün",name:"{way_name} yoluna doğru sağa keskin dönüş yapın",destination:"{destination} istikametinde sağa keskin dönüş yap"},uturn:{default:"U dönüşü yapın",name:"{way_name} yoluna U dönüşü yapın",destination:"{destination} istikametinde bir U-dönüşü yap"}},merge:{default:{default:"{modifier} yöne gir",name:"{way_name} üzerinde {modifier} yöne gir",destination:"{destination} istikametinde {modifier} yöne gir"},straight:{default:"düz yöne gir",name:"{way_name} üzerinde düz yöne gir",destination:"{destination} istikametinde düz yöne gir"},"slight left":{default:"Sola gir",name:"{way_name} üzerinde sola gir",destination:"{destination} istikametinde sola gir"},"slight right":{default:"Sağa gir",name:"{way_name} üzerinde sağa gir",destination:"{destination} istikametinde sağa gir"},"sharp left":{default:"Sola gir",name:"{way_name} üzerinde sola gir",destination:"{destination} istikametinde sola gir"},"sharp right":{default:"Sağa gir",name:"{way_name} üzerinde sağa gir",destination:"{destination} istikametinde sağa gir"},uturn:{default:"U dönüşü yapın",name:"{way_name} yoluna U dönüşü yapın",destination:"{destination} istikametinde bir U-dönüşü yap"}},"new name":{default:{default:"{modifier} yönde devam et",name:"{way_name} üzerinde {modifier} yönde devam et",destination:"{destination} istikametinde {modifier} yönde devam et"},straight:{default:"Düz devam et",name:"{way_name} üzerinde devam et",destination:"{destination} istikametinde devam et"},"sharp left":{default:"Sola keskin dönüş yapın",name:"{way_name} yoluna doğru sola keskin dönüş yapın",destination:"{destination} istikametinde sola keskin dönüş yap"},"sharp right":{default:"Sağa keskin dönüş yapın",name:"{way_name} yoluna doğru sağa keskin dönüş yapın",destination:"{destination} istikametinde sağa keskin dönüş yap"},"slight left":{default:"Hafif soldan devam edin",name:"{way_name} üzerinde hafif solda devam et",destination:"{destination} istikametinde hafif solda devam et"},"slight right":{default:"Hafif sağdan devam edin",name:"{way_name} üzerinde hafif sağda devam et",destination:"{destination} istikametinde hafif sağda devam et"},uturn:{default:"U dönüşü yapın",name:"{way_name} yoluna U dönüşü yapın",destination:"{destination} istikametinde bir U-dönüşü yap"}},notification:{default:{default:"{modifier} yönde devam et",name:"{way_name} üzerinde {modifier} yönde devam et",destination:"{destination} istikametinde {modifier} yönde devam et"},uturn:{default:"U dönüşü yapın",name:"{way_name} yoluna U dönüşü yapın",destination:"{destination} istikametinde bir U-dönüşü yap"}},"off ramp":{default:{default:"Bağlantı yoluna geç",name:"{way_name} üzerindeki bağlantı yoluna geç",destination:"{destination} istikametine giden bağlantı yoluna geç",exit:"{exit} çıkış yoluna geç",exit_destination:"{destination} istikametindeki {exit} çıkış yoluna geç"},left:{default:"Soldaki bağlantı yoluna geç",name:"{way_name} üzerindeki sol bağlantı yoluna geç",destination:"{destination} istikametine giden sol bağlantı yoluna geç",exit:"Soldaki {exit} çıkış yoluna geç",exit_destination:"{destination} istikametindeki {exit} sol çıkış yoluna geç"},right:{default:"Sağdaki bağlantı yoluna geç",name:"{way_name} üzerindeki sağ bağlantı yoluna geç",destination:"{destination} istikametine giden sağ bağlantı yoluna geç",exit:"Sağdaki {exit} çıkış yoluna geç",exit_destination:"{destination} istikametindeki {exit} sağ çıkış yoluna geç"},"sharp left":{default:"Soldaki bağlantı yoluna geç",name:"{way_name} üzerindeki sol bağlantı yoluna geç",destination:"{destination} istikametine giden sol bağlantı yoluna geç",exit:"Soldaki {exit} çıkış yoluna geç",exit_destination:"{destination} istikametindeki {exit} sol çıkış yoluna geç"},"sharp right":{default:"Sağdaki bağlantı yoluna geç",name:"{way_name} üzerindeki sağ bağlantı yoluna geç",destination:"{destination} istikametine giden sağ bağlantı yoluna geç",exit:"Sağdaki {exit} çıkış yoluna geç",exit_destination:"{destination} istikametindeki {exit} sağ çıkış yoluna geç"},"slight left":{default:"Soldaki bağlantı yoluna geç",name:"{way_name} üzerindeki sol bağlantı yoluna geç",destination:"{destination} istikametine giden sol bağlantı yoluna geç",exit:"Soldaki {exit} çıkış yoluna geç",exit_destination:"{destination} istikametindeki {exit} sol çıkış yoluna geç"},"slight right":{default:"Sağdaki bağlantı yoluna geç",name:"{way_name} üzerindeki sağ bağlantı yoluna geç",destination:"{destination} istikametine giden sağ bağlantı yoluna geç",exit:"Sağdaki {exit} çıkış yoluna geç",exit_destination:"{destination} istikametindeki {exit} sağ çıkış yoluna geç"}},"on ramp":{default:{default:"Bağlantı yoluna geç",name:"{way_name} üzerindeki bağlantı yoluna geç",destination:"{destination} istikametine giden bağlantı yoluna geç"},left:{default:"Soldaki bağlantı yoluna geç",name:"{way_name} üzerindeki sol bağlantı yoluna geç",destination:"{destination} istikametine giden sol bağlantı yoluna geç"},right:{default:"Sağdaki bağlantı yoluna geç",name:"{way_name} üzerindeki sağ bağlantı yoluna geç",destination:"{destination} istikametine giden sağ bağlantı yoluna geç"},"sharp left":{default:"Soldaki bağlantı yoluna geç",name:"{way_name} üzerindeki sol bağlantı yoluna geç",destination:"{destination} istikametine giden sol bağlantı yoluna geç"},"sharp right":{default:"Sağdaki bağlantı yoluna geç",name:"{way_name} üzerindeki sağ bağlantı yoluna geç",destination:"{destination} istikametine giden sağ bağlantı yoluna geç"},"slight left":{default:"Soldaki bağlantı yoluna geç",name:"{way_name} üzerindeki sol bağlantı yoluna geç",destination:"{destination} istikametine giden sol bağlantı yoluna geç"},"slight right":{default:"Sağdaki bağlantı yoluna geç",name:"{way_name} üzerindeki sağ bağlantı yoluna geç",destination:"{destination} istikametine giden sağ bağlantı yoluna geç"}},rotary:{default:{default:{default:"Dönel kavşağa gir",name:"Dönel kavşağa gir ve {way_name} üzerinde çık",destination:"Dönel kavşağa gir ve {destination} istikametinde çık"},name:{default:"{rotary_name} dönel kavşağa gir",name:"{rotary_name} dönel kavşağa gir ve {way_name} üzerinde çık",destination:"{rotary_name} dönel kavşağa gir ve {destination} istikametinde çık"},exit:{default:"Dönel kavşağa gir ve {exit_number} numaralı çıkışa gir",name:"Dönel kavşağa gir ve {way_name} üzerindeki {exit_number} numaralı çıkışa gir",destination:"Dönel kavşağa gir ve {destination} istikametindeki {exit_number} numaralı çıkışa gir"},name_exit:{default:"{rotary_name} dönel kavşağa gir ve {exit_number} numaralı çıkışa gir",name:"{rotary_name} dönel kavşağa gir ve {way_name} üzerindeki {exit_number} numaralı çıkışa gir",destination:"{rotary_name} dönel kavşağa gir ve {destination} istikametindeki {exit_number} numaralı çıkışa gir"}}},roundabout:{default:{exit:{default:"Göbekli kavşağa gir ve {exit_number} numaralı çıkışa gir",name:"Göbekli kavşağa gir ve {way_name} üzerindeki {exit_number} numaralı çıkışa gir",destination:"Göbekli kavşağa gir ve {destination} istikametindeki {exit_number} numaralı çıkışa gir"},default:{default:"Göbekli kavşağa gir",name:"Göbekli kavşağa gir ve {way_name} üzerinde çık",destination:"Göbekli kavşağa gir ve {destination} istikametinde çık"}}},"roundabout turn":{default:{default:"{modifier} yöne dön",name:"{way_name} üzerinde {modifier} yöne dön",destination:"{destination} istikametinde {modifier} yöne dön"},left:{default:"Sola dön",name:"{way_name} üzerinde sola dön",destination:"{destination} istikametinde sola dön"},right:{default:"Sağa dön",name:"{way_name} üzerinde sağa dön",destination:"{destination} istikametinde sağa dön"},straight:{default:"Düz devam et",name:"{way_name} üzerinde düz devam et",destination:"{destination} istikametinde düz devam et"}},"exit roundabout":{default:{default:"{modifier} yöne dön",name:"{way_name} üzerinde {modifier} yöne dön",destination:"{destination} istikametinde {modifier} yöne dön"},left:{default:"Sola dön",name:"{way_name} üzerinde sola dön",destination:"{destination} istikametinde sola dön"},right:{default:"Sağa dön",name:"{way_name} üzerinde sağa dön",destination:"{destination} istikametinde sağa dön"},straight:{default:"Düz devam et",name:"{way_name} üzerinde düz devam et",destination:"{destination} istikametinde düz devam et"}},"exit rotary":{default:{default:"{modifier} yöne dön",name:"{way_name} üzerinde {modifier} yöne dön",destination:"{destination} istikametinde {modifier} yöne dön"},left:{default:"Sola dön",name:"{way_name} üzerinde sola dön",destination:"{destination} istikametinde sola dön"},right:{default:"Sağa dön",name:"{way_name} üzerinde sağa dön",destination:"{destination} istikametinde sağa dön"},straight:{default:"Düz devam et",name:"{way_name} üzerinde düz devam et",destination:"{destination} istikametinde düz devam et"}},turn:{default:{default:"{modifier} yöne dön",name:"{way_name} üzerinde {modifier} yöne dön",destination:"{destination} istikametinde {modifier} yöne dön"},left:{default:"Sola dönün",name:"{way_name} üzerinde sola dön",destination:"{destination} istikametinde sola dön"},right:{default:"Sağa dönün",name:"{way_name} üzerinde sağa dön",destination:"{destination} istikametinde sağa dön"},straight:{default:"Düz git",name:"{way_name} üzerinde düz git",destination:"{destination} istikametinde düz git"}},"use lane":{no_lanes:{default:"Düz devam edin"},default:{default:"{lane_instruction}"}}}}},{}],45:[function(_dereq_,module,exports){module.exports={meta:{capitalizeFirstLetter:true},v5:{constants:{ordinalize:{1:"1й",2:"2й",3:"3й",4:"4й",5:"5й",6:"6й",7:"7й",8:"8й",9:"9й",10:"10й"},direction:{north:"північ",northeast:"північний схід",east:"схід",southeast:"південний схід",south:"південь",southwest:"південний захід",west:"захід",northwest:"північний захід"},modifier:{left:"ліворуч",right:"праворуч","sharp left":"різко ліворуч","sharp right":"різко праворуч","slight left":"плавно ліворуч","slight right":"плавно праворуч",straight:"прямо",uturn:"розворот"},lanes:{xo:"Тримайтесь праворуч",ox:"Тримайтесь ліворуч",xox:"Тримайтесь в середині",oxo:"Тримайтесь праворуч або ліворуч"}},modes:{ferry:{default:"Скористайтесь поромом",name:"Скористайтесь поромом {way_name}",destination:"Скористайтесь поромом у напрямку {destination}"}},phrase:{"two linked by distance":"{instruction_one}, потім, через {distance}, {instruction_two}","two linked":"{instruction_one}, потім {instruction_two}","one in distance":"Через {distance}, {instruction_one}","name and ref":"{name} ({ref})","exit with number":"з'їзд {exit}"},arrive:{default:{default:"Ви прибули у ваш {nth} пункт призначення",upcoming:"Ви наближаєтесь до вашого {nth} місця призначення",short:"Ви прибули","short-upcoming":"Ви прибудете",named:"Ви прибули у {waypoint_name}"},left:{default:"Ви прибули у ваш {nth} пункт призначення, він – ліворуч",upcoming:"Ви наближаєтесь до вашого {nth} місця призначення, ліворуч",short:"Ви прибули","short-upcoming":"Ви прибудете",named:"Ви прибули у {waypoint_name} ліворуч"},right:{default:"Ви прибули у ваш {nth} пункт призначення, він – праворуч",upcoming:"Ви наближаєтесь до вашого {nth} місця призначення, праворуч",short:"Ви прибули","short-upcoming":"Ви прибудете",named:"Ви прибули у {waypoint_name} праворуч"},"sharp left":{default:"Ви прибули у ваш {nth} пункт призначення, він – ліворуч",upcoming:"Ви наближаєтесь до вашого {nth} місця призначення, ліворуч",short:"Ви прибули","short-upcoming":"Ви прибудете",named:"Ви прибули у {waypoint_name} ліворуч"},"sharp right":{default:"Ви прибули у ваш {nth} пункт призначення, він – праворуч",upcoming:"Ви наближаєтесь до вашого {nth} місця призначення, праворуч",short:"Ви прибули","short-upcoming":"Ви прибудете",named:"Ви прибули у {waypoint_name} праворуч"},"slight right":{default:"Ви прибули у ваш {nth} пункт призначення, він – праворуч",upcoming:"Ви наближаєтесь до вашого {nth} місця призначення, праворуч",short:"Ви прибули","short-upcoming":"Ви прибудете",named:"Ви прибули у {waypoint_name} праворуч"},"slight left":{default:"Ви прибули у ваш {nth} пункт призначення, він – ліворуч",upcoming:"Ви наближаєтесь до вашого {nth} місця призначення, ліворуч",short:"Ви прибули","short-upcoming":"Ви прибудете",named:"Ви прибули у {waypoint_name} ліворуч"},straight:{default:"Ви прибули у ваш {nth} пункт призначення, він – прямо перед вами",upcoming:"Ви наближаєтесь до вашого {nth} місця призначення, прямо перед вами",short:"Ви прибули","short-upcoming":"Ви прибудете",named:"Ви прибули у {waypoint_name} прямо перед вами"}},continue:{default:{default:"Поверніть {modifier}",name:"Поверніть{modifier} залишаючись на {way_name}",destination:"Поверніть {modifier} у напрямку {destination}",exit:"Поверніть {modifier} на {way_name}"},straight:{default:"Продовжуйте рух прямо",name:"Продовжуйте рух прямо залишаючись на {way_name}",destination:"Рухайтесь у напрямку {destination}",distance:"Продовжуйте рух прямо {distance}",namedistance:"Продовжуйте рух по {way_name} {distance}"},"sharp left":{default:"Поверніть різко ліворуч",name:"Поверніть різко ліворуч щоб залишитись на {way_name}",destination:"Поверніть різко ліворуч у напрямку {destination}"},"sharp right":{default:"Поверніть різко праворуч",name:"Поверніть різко праворуч щоб залишитись на {way_name}",destination:"Поверніть різко праворуч у напрямку {destination}"},"slight left":{default:"Поверніть різко ліворуч",name:"Поверніть плавно ліворуч щоб залишитись на {way_name}",destination:"Поверніть плавно ліворуч у напрямку {destination}"},"slight right":{default:"Поверніть плавно праворуч",name:"Поверніть плавно праворуч щоб залишитись на {way_name}",destination:"Поверніть плавно праворуч у напрямку {destination}"},uturn:{default:"Здійсніть розворот",name:"Здійсніть розворот та рухайтесь по {way_name}",destination:"Здійсніть розворот у напрямку {destination}"}},depart:{default:{default:"Прямуйте на {direction}",name:"Прямуйте на {direction} по {way_name}",namedistance:"Прямуйте на {direction} по {way_name} {distance}"}},"end of road":{default:{default:"Поверніть {modifier}",name:"Поверніть {modifier} на {way_name}",destination:"Поверніть {modifier} у напрямку {destination}"},straight:{default:"Продовжуйте рух прямо",name:"Продовжуйте рух прямо до {way_name}",destination:"Продовжуйте рух прямо у напрямку {destination}"},uturn:{default:"Здійсніть розворот в кінці дороги",name:"Здійсніть розворот на {way_name} в кінці дороги",destination:"Здійсніть розворот у напрямку {destination} в кінці дороги"}},fork:{default:{default:"На роздоріжжі тримайтеся {modifier}",name:"Тримайтеся {modifier} і рухайтесь на {way_name}",destination:"Тримайтеся {modifier} в напрямку {destination}"},"slight left":{default:"На роздоріжжі тримайтеся ліворуч",name:"Тримайтеся ліворуч і рухайтесь на {way_name}",destination:"Тримайтеся ліворуч в напрямку {destination}"},"slight right":{default:"На роздоріжжі тримайтеся праворуч",name:"Тримайтеся праворуч і рухайтесь на {way_name}",destination:"Тримайтеся праворуч в напрямку {destination}"},"sharp left":{default:"На роздоріжжі різко поверніть ліворуч",name:"Прийміть різко ліворуч на {way_name}",destination:"Прийміть різко ліворуч у напрямку {destination}"},"sharp right":{default:"На роздоріжжі різко поверніть праворуч",name:"Прийміть різко праворуч на {way_name}",destination:"Прийміть різко праворуч у напрямку {destination}"},uturn:{default:"Здійсніть розворот",name:"Здійсніть розворот на {way_name}",destination:"Здійсніть розворот у напрямку {destination}"}},merge:{default:{default:"Приєднайтеся до потоку {modifier}",name:"Приєднайтеся до потоку {modifier} на {way_name}",destination:"Приєднайтеся до потоку {modifier} у напрямку {destination}"},straight:{default:"Приєднайтеся до потоку",name:"Приєднайтеся до потоку на {way_name}",destination:"Приєднайтеся до потоку у напрямку {destination}"},"slight left":{default:"Приєднайтеся до потоку ліворуч",name:"Приєднайтеся до потоку ліворуч на {way_name}",destination:"Приєднайтеся до потоку ліворуч у напрямку {destination}"},"slight right":{default:"Приєднайтеся до потоку праворуч",name:"Приєднайтеся до потоку праворуч на {way_name}",destination:"Приєднайтеся до потоку праворуч у напрямку {destination}"},"sharp left":{default:"Приєднайтеся до потоку ліворуч",name:"Приєднайтеся до потоку ліворуч на {way_name}",destination:"Приєднайтеся до потоку ліворуч у напрямку {destination}"},"sharp right":{default:"Приєднайтеся до потоку праворуч",name:"Приєднайтеся до потоку праворуч на {way_name}",destination:"Приєднайтеся до потоку праворуч у напрямку {destination}"},uturn:{default:"Здійсніть розворот",name:"Здійсніть розворот на {way_name}",destination:"Здійсніть розворот у напрямку {destination}"}},"new name":{default:{default:"Рухайтесь {modifier}",name:"Рухайтесь {modifier} на {way_name}",destination:"Рухайтесь {modifier} у напрямку {destination}"},straight:{default:"Рухайтесь прямо",name:"Рухайтесь по {way_name}",destination:"Рухайтесь у напрямку {destination}"},"sharp left":{default:"Прийміть різко ліворуч",name:"Прийміть різко ліворуч на {way_name}",destination:"Прийміть різко ліворуч у напрямку {destination}"},"sharp right":{default:"Прийміть різко праворуч",name:"Прийміть різко праворуч на {way_name}",destination:"Прийміть різко праворуч у напрямку {destination}"},"slight left":{default:"Рухайтесь плавно ліворуч",name:"Рухайтесь плавно ліворуч на {way_name}",destination:"Рухайтесь плавно ліворуч у напрямку {destination}"},"slight right":{default:"Рухайтесь плавно праворуч",name:"Рухайтесь плавно праворуч на {way_name}",destination:"Рухайтесь плавно праворуч у напрямку {destination}"},uturn:{default:"Здійсніть розворот",name:"Здійсніть розворот на {way_name}",destination:"Здійсніть розворот у напрямку {destination}"}},notification:{default:{default:"Рухайтесь {modifier}",name:"Рухайтесь {modifier} на {way_name}",destination:"Рухайтесь {modifier} у напрямку {destination}"},uturn:{default:"Здійсніть розворот",name:"Здійсніть розворот на {way_name}",destination:"Здійсніть розворот у напрямку {destination}"}},"off ramp":{default:{default:"Рухайтесь на зʼїзд",name:"Рухайтесь на зʼїзд на {way_name}",destination:"Рухайтесь на зʼїзд у напрямку {destination}",exit:"Оберіть з'їзд {exit}",exit_destination:"Оберіть з'їзд {exit} у напрямку {destination}"},left:{default:"Рухайтесь на зʼїзд ліворуч",name:"Рухайтесь на зʼїзд ліворуч на {way_name}",destination:"Рухайтесь на зʼїзд ліворуч у напрямку {destination}",exit:"Оберіть з'їзд {exit} ліворуч",exit_destination:"Оберіть з'їзд {exit} ліворуч у напрямку {destination}"},right:{default:"Рухайтесь на зʼїзд праворуч",name:"Рухайтесь на зʼїзд праворуч на {way_name}",destination:"Рухайтесь на зʼїзд праворуч у напрямку {destination}",exit:"Оберіть з'їзд {exit} праворуч",exit_destination:"Оберіть з'їзд {exit} праворуч у напрямку {destination}"},"sharp left":{default:"Рухайтесь на зʼїзд ліворуч",name:"Рухайтесь на зʼїзд ліворуч на {way_name}",destination:"Рухайтесь на зʼїзд ліворуч у напрямку {destination}",exit:"Оберіть з'їзд {exit} ліворуч",exit_destination:"Оберіть з'їзд {exit} ліворуч у напрямку {destination}"},"sharp right":{default:"Рухайтесь на зʼїзд праворуч",name:"Рухайтесь на зʼїзд праворуч на {way_name}",destination:"Рухайтесь на зʼїзд праворуч у напрямку {destination}",exit:"Оберіть з'їзд {exit} праворуч",exit_destination:"Оберіть з'їзд {exit} праворуч у напрямку {destination}"},"slight left":{default:"Рухайтесь на зʼїзд ліворуч",name:"Рухайтесь на зʼїзд ліворуч на {way_name}",destination:"Рухайтесь на зʼїзд ліворуч у напрямку {destination}",exit:"Оберіть з'їзд {exit} ліворуч",exit_destination:"Оберіть з'їзд {exit} ліворуч у напрямку {destination}"},"slight right":{default:"Рухайтесь на зʼїзд праворуч",name:"Рухайтесь на зʼїзд праворуч на {way_name}",destination:"Рухайтесь на зʼїзд праворуч у напрямку {destination}",exit:"Оберіть з'їзд {exit} праворуч",exit_destination:"Оберіть з'їзд {exit} праворуч у напрямку {destination}"}},"on ramp":{default:{default:"Рухайтесь на вʼїзд",name:"Рухайтесь на вʼїзд на {way_name}",destination:"Рухайтесь на вʼїзд у напрямку {destination}"},left:{default:"Рухайтесь на вʼїзд ліворуч",name:"Рухайтесь на вʼїзд ліворуч на {way_name}",destination:"Рухайтесь на вʼїзд ліворуч у напрямку {destination}"},right:{default:"Рухайтесь на вʼїзд праворуч",name:"Рухайтесь на вʼїзд праворуч на {way_name}",destination:"Рухайтесь на вʼїзд праворуч у напрямку {destination}"},"sharp left":{default:"Рухайтесь на вʼїзд ліворуч",name:"Рухайтесь на вʼїзд ліворуч на {way_name}",destination:"Рухайтесь на вʼїзд ліворуч у напрямку {destination}"},"sharp right":{default:"Рухайтесь на вʼїзд праворуч",name:"Рухайтесь на вʼїзд праворуч на {way_name}",destination:"Рухайтесь на вʼїзд праворуч у напрямку {destination}"},"slight left":{default:"Рухайтесь на вʼїзд ліворуч",name:"Рухайтесь на вʼїзд ліворуч на {way_name}",destination:"Рухайтесь на вʼїзд ліворуч у напрямку {destination}"},"slight right":{default:"Рухайтесь на вʼїзд праворуч",name:"Рухайтесь на вʼїзд праворуч на {way_name}",destination:"Рухайтесь на вʼїзд праворуч у напрямку {destination}"}},rotary:{default:{default:{default:"Рухайтесь по колу",name:"Рухайтесь по колу до {way_name}",destination:"Рухайтесь по колу в напрямку {destination}"},name:{default:"Рухайтесь по {rotary_name}",name:"Рухайтесь по {rotary_name} та поверніть на {way_name}",destination:"Рухайтесь по {rotary_name} та поверніть в напрямку {destination}"},exit:{default:"Рухайтесь по колу та повереніть у {exit_number} з'їзд",name:"Рухайтесь по колу та поверніть у {exit_number} з'їзд на {way_name}",destination:"Рухайтесь по колу та поверніть у {exit_number} з'їзд у напрямку {destination}"},name_exit:{default:"Рухайтесь по {rotary_name} та поверніть у {exit_number} з'їзд",name:"Рухайтесь по {rotary_name} та поверніть у {exit_number} з'їзд на {way_name}",destination:"Рухайтесь по {rotary_name} та поверніть у {exit_number} з'їзд в напрямку {destination}"}}},roundabout:{default:{exit:{default:"Рухайтесь по колу та повереніть у {exit_number} з'їзд",name:"Рухайтесь по колу та поверніть у {exit_number} з'їзд на {way_name}",destination:"Рухайтесь по колу та поверніть у {exit_number} з'їзд у напрямку {destination}"},default:{default:"Рухайтесь по колу",name:"Рухайтесь по колу до {way_name}",destination:"Рухайтесь по колу в напрямку {destination}"}}},"roundabout turn":{default:{default:"Рухайтесь {modifier}",name:"Рухайтесь {modifier} на {way_name}",destination:"Рухайтесь {modifier} в напрямку {destination}"},left:{default:"Поверніть ліворуч",name:"Поверніть ліворуч на {way_name}",destination:"Поверніть ліворуч у напрямку {destination}"},right:{default:"Поверніть праворуч",name:"Поверніть праворуч на {way_name}",destination:"Поверніть праворуч у напрямку {destination}"},straight:{default:"Рухайтесь прямо",name:"Продовжуйте рух прямо до {way_name}",destination:"Продовжуйте рух прямо у напрямку {destination}"}},"exit roundabout":{default:{default:"Залишить коло",name:"Залишить коло на {way_name} зʼїзді",destination:"Залишить коло в напрямку {destination}"}},"exit rotary":{default:{default:"Залишить коло",name:"Залишить коло на {way_name} зʼїзді",destination:"Залишить коло в напрямку {destination}"}},turn:{default:{default:"Рухайтесь {modifier}",name:"Рухайтесь {modifier} на {way_name}",destination:"Рухайтесь {modifier} в напрямку {destination}"},left:{default:"Поверніть ліворуч",name:"Поверніть ліворуч на {way_name}",destination:"Поверніть ліворуч у напрямку {destination}"},right:{default:"Поверніть праворуч",
name:"Поверніть праворуч на {way_name}",destination:"Поверніть праворуч у напрямку {destination}"},straight:{default:"Рухайтесь прямо",name:"Рухайтесь прямо по {way_name}",destination:"Рухайтесь прямо у напрямку {destination}"}},"use lane":{no_lanes:{default:"Продовжуйте рух прямо"},default:{default:"{lane_instruction}"}}}}},{}],46:[function(_dereq_,module,exports){module.exports={meta:{capitalizeFirstLetter:true},v5:{constants:{ordinalize:{1:"đầu tiên",2:"thứ 2",3:"thứ 3",4:"thứ 4",5:"thứ 5",6:"thú 6",7:"thứ 7",8:"thứ 8",9:"thứ 9",10:"thứ 10"},direction:{north:"bắc",northeast:"đông bắc",east:"đông",southeast:"đông nam",south:"nam",southwest:"tây nam",west:"tây",northwest:"tây bắc"},modifier:{left:"trái",right:"phải","sharp left":"trái gắt","sharp right":"phải gắt","slight left":"trái nghiêng","slight right":"phải nghiêng",straight:"thẳng",uturn:"ngược"},lanes:{xo:"Đi bên phải",ox:"Đi bên trái",xox:"Đi vào giữa",oxo:"Đi bên trái hay bên phải"}},modes:{ferry:{default:"Lên phà",name:"Lên phà {way_name}",destination:"Lên phà đi {destination}"}},phrase:{"two linked by distance":"{instruction_one}, rồi {distance} nữa thì {instruction_two}","two linked":"{instruction_one}, rồi {instruction_two}","one in distance":"{distance} nữa thì {instruction_one}","name and ref":"{name} ({ref})","exit with number":"lối ra {exit}"},arrive:{default:{default:"Đến nơi {nth}",upcoming:"Đến nơi {nth}",short:"Đến nơi","short-upcoming":"Đến nơi",named:"Đến {waypoint_name}"},left:{default:"Đến nơi {nth} ở bên trái",upcoming:"Đến nơi {nth} ở bên trái",short:"Đến nơi","short-upcoming":"Đến nơi",named:"Đến {waypoint_name} ở bên trái"},right:{default:"Đến nơi {nth} ở bên phải",upcoming:"Đến nơi {nth} ở bên phải",short:"Đến nơi","short-upcoming":"Đến nơi",named:"Đến {waypoint_name} ở bên phải"},"sharp left":{default:"Đến nơi {nth} ở bên trái",upcoming:"Đến nơi {nth} ở bên trái",short:"Đến nơi","short-upcoming":"Đến nơi",named:"Đến {waypoint_name} ở bên trái"},"sharp right":{default:"Đến nơi {nth} ở bên phải",upcoming:"Đến nơi {nth} ở bên phải",short:"Đến nơi","short-upcoming":"Đến nơi",named:"Đến {waypoint_name} ở bên phải"},"slight right":{default:"Đến nơi {nth} ở bên phải",upcoming:"Đến nơi {nth} ở bên phải",short:"Đến nơi","short-upcoming":"Đến nơi",named:"Đến {waypoint_name} ở bên phải"},"slight left":{default:"Đến nơi {nth} ở bên trái",upcoming:"Đến nơi {nth} ở bên trái",short:"Đến nơi","short-upcoming":"Đến nơi",named:"Đến {waypoint_name} ở bên trái"},straight:{default:"Đến nơi {nth} ở trước mặt",upcoming:"Đến nơi {nth} ở trước mặt",short:"Đến nơi","short-upcoming":"Đến nơi",named:"Đến {waypoint_name} ở trước mặt"}},continue:{default:{default:"Quẹo {modifier}",name:"Quẹo {modifier} để chạy tiếp trên {way_name}",destination:"Quẹo {modifier} về {destination}",exit:"Quẹo {modifier} vào {way_name}"},straight:{default:"Chạy thẳng",name:"Chạy tiếp trên {way_name}",destination:"Chạy tiếp về {destination}",distance:"Chạy thẳng cho {distance}",namedistance:"Chạy tiếp trên {way_name} cho {distance}"},"sharp left":{default:"Quẹo gắt bên trái",name:"Quẹo gắt bên trái để chạy tiếp trên {way_name}",destination:"Quẹo gắt bên trái về {destination}"},"sharp right":{default:"Quẹo gắt bên phải",name:"Quẹo gắt bên phải để chạy tiếp trên {way_name}",destination:"Quẹo gắt bên phải về {destination}"},"slight left":{default:"Nghiêng về bên trái",name:"Nghiêng về bên trái để chạy tiếp trên {way_name}",destination:"Nghiêng về bên trái về {destination}"},"slight right":{default:"Nghiêng về bên phải",name:"Nghiêng về bên phải để chạy tiếp trên {way_name}",destination:"Nghiêng về bên phải về {destination}"},uturn:{default:"Quẹo ngược lại",name:"Quẹo ngược lại trên {way_name}",destination:"Quẹo ngược về {destination}"}},depart:{default:{default:"Đi về hướng {direction}",name:"Đi về hướng {direction} trên {way_name}",namedistance:"Đi về hướng {direction} trên {way_name} cho {distance}"}},"end of road":{default:{default:"Quẹo {modifier}",name:"Quẹo {modifier} vào {way_name}",destination:"Quẹo {modifier} về {destination}"},straight:{default:"Chạy thẳng",name:"Chạy tiếp trên {way_name}",destination:"Chạy tiếp về {destination}"},uturn:{default:"Quẹo ngược lại tại cuối đường",name:"Quẹo ngược vào {way_name} tại cuối đường",destination:"Quẹo ngược về {destination} tại cuối đường"}},fork:{default:{default:"Đi bên {modifier} ở ngã ba",name:"Giữ bên {modifier} vào {way_name}",destination:"Giữ bên {modifier} về {destination}"},"slight left":{default:"Nghiêng về bên trái ở ngã ba",name:"Giữ bên trái vào {way_name}",destination:"Giữ bên trái về {destination}"},"slight right":{default:"Nghiêng về bên phải ở ngã ba",name:"Giữ bên phải vào {way_name}",destination:"Giữ bên phải về {destination}"},"sharp left":{default:"Quẹo gắt bên trái ở ngã ba",name:"Quẹo gắt bên trái vào {way_name}",destination:"Quẹo gắt bên trái về {destination}"},"sharp right":{default:"Quẹo gắt bên phải ở ngã ba",name:"Quẹo gắt bên phải vào {way_name}",destination:"Quẹo gắt bên phải về {destination}"},uturn:{default:"Quẹo ngược lại",name:"Quẹo ngược lại {way_name}",destination:"Quẹo ngược lại về {destination}"}},merge:{default:{default:"Nhập sang {modifier}",name:"Nhập sang {modifier} vào {way_name}",destination:"Nhập sang {modifier} về {destination}"},straight:{default:"Nhập đường",name:"Nhập vào {way_name}",destination:"Nhập đường về {destination}"},"slight left":{default:"Nhập sang trái",name:"Nhập sang trái vào {way_name}",destination:"Nhập sang trái về {destination}"},"slight right":{default:"Nhập sang phải",name:"Nhập sang phải vào {way_name}",destination:"Nhập sang phải về {destination}"},"sharp left":{default:"Nhập sang trái",name:"Nhập sang trái vào {way_name}",destination:"Nhập sang trái về {destination}"},"sharp right":{default:"Nhập sang phải",name:"Nhập sang phải vào {way_name}",destination:"Nhập sang phải về {destination}"},uturn:{default:"Quẹo ngược lại",name:"Quẹo ngược lại {way_name}",destination:"Quẹo ngược lại về {destination}"}},"new name":{default:{default:"Chạy tiếp bên {modifier}",name:"Chạy tiếp bên {modifier} trên {way_name}",destination:"Chạy tiếp bên {modifier} về {destination}"},straight:{default:"Chạy thẳng",name:"Chạy tiếp trên {way_name}",destination:"Chạy tiếp về {destination}"},"sharp left":{default:"Quẹo gắt bên trái",name:"Quẹo gắt bên trái vào {way_name}",destination:"Quẹo gắt bên trái về {destination}"},"sharp right":{default:"Quẹo gắt bên phải",name:"Quẹo gắt bên phải vào {way_name}",destination:"Quẹo gắt bên phải về {destination}"},"slight left":{default:"Nghiêng về bên trái",name:"Nghiêng về bên trái vào {way_name}",destination:"Nghiêng về bên trái về {destination}"},"slight right":{default:"Nghiêng về bên phải",name:"Nghiêng về bên phải vào {way_name}",destination:"Nghiêng về bên phải về {destination}"},uturn:{default:"Quẹo ngược lại",name:"Quẹo ngược lại {way_name}",destination:"Quẹo ngược lại về {destination}"}},notification:{default:{default:"Chạy tiếp bên {modifier}",name:"Chạy tiếp bên {modifier} trên {way_name}",destination:"Chạy tiếp bên {modifier} về {destination}"},uturn:{default:"Quẹo ngược lại",name:"Quẹo ngược lại {way_name}",destination:"Quẹo ngược lại về {destination}"}},"off ramp":{default:{default:"Đi đường nhánh",name:"Đi đường nhánh {way_name}",destination:"Đi đường nhánh về {destination}",exit:"Đi theo lối ra {exit}",exit_destination:"Đi theo lối ra {exit} về {destination}"},left:{default:"Đi đường nhánh bên trái",name:"Đi đường nhánh {way_name} bên trái",destination:"Đi đường nhánh bên trái về {destination}",exit:"Đi theo lối ra {exit} bên trái",exit_destination:"Đi theo lối ra {exit} bên trái về {destination}"},right:{default:"Đi đường nhánh bên phải",name:"Đi đường nhánh {way_name} bên phải",destination:"Đi đường nhánh bên phải về {destination}",exit:"Đi theo lối ra {exit} bên phải",exit_destination:"Đi theo lối ra {exit} bên phải về {destination}"},"sharp left":{default:"Đi đường nhánh bên trái",name:"Đi đường nhánh {way_name} bên trái",destination:"Đi đường nhánh bên trái về {destination}",exit:"Đi theo lối ra {exit} bên trái",exit_destination:"Đi theo lối ra {exit} bên trái về {destination}"},"sharp right":{default:"Đi đường nhánh bên phải",name:"Đi đường nhánh {way_name} bên phải",destination:"Đi đường nhánh bên phải về {destination}",exit:"Đi theo lối ra {exit} bên phải",exit_destination:"Đi theo lối ra {exit} bên phải về {destination}"},"slight left":{default:"Đi đường nhánh bên trái",name:"Đi đường nhánh {way_name} bên trái",destination:"Đi đường nhánh bên trái về {destination}",exit:"Đi theo lối ra {exit} bên trái",exit_destination:"Đi theo lối ra {exit} bên trái về {destination}"},"slight right":{default:"Đi đường nhánh bên phải",name:"Đi đường nhánh {way_name} bên phải",destination:"Đi đường nhánh bên phải về {destination}",exit:"Đi theo lối ra {exit} bên phải",exit_destination:"Đi theo lối ra {exit} bên phải về {destination}"}},"on ramp":{default:{default:"Đi đường nhánh",name:"Đi đường nhánh {way_name}",destination:"Đi đường nhánh về {destination}"},left:{default:"Đi đường nhánh bên trái",name:"Đi đường nhánh {way_name} bên trái",destination:"Đi đường nhánh bên trái về {destination}"},right:{default:"Đi đường nhánh bên phải",name:"Đi đường nhánh {way_name} bên phải",destination:"Đi đường nhánh bên phải về {destination}"},"sharp left":{default:"Đi đường nhánh bên trái",name:"Đi đường nhánh {way_name} bên trái",destination:"Đi đường nhánh bên trái về {destination}"},"sharp right":{default:"Đi đường nhánh bên phải",name:"Đi đường nhánh {way_name} bên phải",destination:"Đi đường nhánh bên phải về {destination}"},"slight left":{default:"Đi đường nhánh bên trái",name:"Đi đường nhánh {way_name} bên trái",destination:"Đi đường nhánh bên trái về {destination}"},"slight right":{default:"Đi đường nhánh bên phải",name:"Đi đường nhánh {way_name} bên phải",destination:"Đi đường nhánh bên phải về {destination}"}},rotary:{default:{default:{default:"Đi vào bùng binh",name:"Đi vào bùng binh và ra tại {way_name}",destination:"Đi vào bùng binh và ra về {destination}"},name:{default:"Đi vào {rotary_name}",name:"Đi vào {rotary_name} và ra tại {way_name}",destination:"Đi và {rotary_name} và ra về {destination}"},exit:{default:"Đi vào bùng binh và ra tại đường {exit_number}",name:"Đi vào bùng binh và ra tại đường {exit_number} tức {way_name}",destination:"Đi vào bùng binh và ra tại đường {exit_number} về {destination}"},name_exit:{default:"Đi vào {rotary_name} và ra tại đường {exit_number}",name:"Đi vào {rotary_name} và ra tại đường {exit_number} tức {way_name}",destination:"Đi vào {rotary_name} và ra tại đường {exit_number} về {destination}"}}},roundabout:{default:{exit:{default:"Đi vào bùng binh và ra tại đường {exit_number}",name:"Đi vào bùng binh và ra tại đường {exit_number} tức {way_name}",destination:"Đi vào bùng binh và ra tại đường {exit_number} về {destination}"},default:{default:"Đi vào bùng binh",name:"Đi vào bùng binh và ra tại {way_name}",destination:"Đi vào bùng binh và ra về {destination}"}}},"roundabout turn":{default:{default:"Quẹo {modifier}",name:"Quẹo {modifier} vào {way_name}",destination:"Quẹo {modifier} về {destination}"},left:{default:"Quẹo trái",name:"Quẹo trái vào {way_name}",destination:"Quẹo trái về {destination}"},right:{default:"Quẹo phải",name:"Quẹo phải vào {way_name}",destination:"Quẹo phải về {destination}"},straight:{default:"Chạy thẳng",name:"Chạy tiếp trên {way_name}",destination:"Chạy tiếp về {destination}"}},"exit roundabout":{default:{default:"Ra bùng binh",name:"Ra bùng binh vào {way_name}",destination:"Ra bùng binh về {destination}"}},"exit rotary":{default:{default:"Ra bùng binh",name:"Ra bùng binh vào {way_name}",destination:"Ra bùng binh về {destination}"}},turn:{default:{default:"Quẹo {modifier}",name:"Quẹo {modifier} vào {way_name}",destination:"Quẹo {modifier} về {destination}"},left:{default:"Quẹo trái",name:"Quẹo trái vào {way_name}",destination:"Quẹo trái về {destination}"},right:{default:"Quẹo phải",name:"Quẹo phải vào {way_name}",destination:"Quẹo phải về {destination}"},straight:{default:"Chạy thẳng",name:"Chạy thẳng vào {way_name}",destination:"Chạy thẳng về {destination}"}},"use lane":{no_lanes:{default:"Chạy thẳng"},default:{default:"{lane_instruction}"}}}}},{}],47:[function(_dereq_,module,exports){module.exports={meta:{capitalizeFirstLetter:false},v5:{constants:{ordinalize:{1:"第一",2:"第二",3:"第三",4:"第四",5:"第五",6:"第六",7:"第七",8:"第八",9:"第九",10:"第十"},direction:{north:"北",northeast:"东北",east:"东",southeast:"东南",south:"南",southwest:"西南",west:"西",northwest:"西北"},modifier:{left:"向左",right:"向右","sharp left":"急向左","sharp right":"急向右","slight left":"稍向左","slight right":"稍向右",straight:"直行",uturn:"调头"},lanes:{xo:"靠右行驶",ox:"靠左行驶",xox:"保持在道路中间行驶",oxo:"保持在道路左侧或右侧行驶"}},modes:{ferry:{default:"乘坐轮渡",name:"乘坐{way_name}轮渡",destination:"乘坐开往{destination}的轮渡"}},phrase:{"two linked by distance":"{instruction_one}，{distance}后{instruction_two}","two linked":"{instruction_one}，随后{instruction_two}","one in distance":"{distance}后{instruction_one}","name and ref":"{name}（{ref}）","exit with number":"出口{exit}"},arrive:{default:{default:"您已经到达您的{nth}个目的地",upcoming:"您即将到达您的{nth}个目的地",short:"已到达目的地","short-upcoming":"即将到达目的地",named:"您已到达{waypoint_name}"},left:{default:"您已经到达您的{nth}个目的地，目的地在道路左侧",upcoming:"您即将到达您的{nth}个目的地，目的地在道路左侧",short:"已到达目的地","short-upcoming":"即将到达目的地",named:"您已到达{waypoint_name}，目的地在您左边。"},right:{default:"您已经到达您的{nth}个目的地，目的地在道路右侧",upcoming:"您即将到达您的{nth}个目的地，目的地在道路右侧",short:"已到达目的地","short-upcoming":"即将到达目的地",named:"您已到达{waypoint_name}，目的地在您右边。"},"sharp left":{default:"您已经到达您的{nth}个目的地，目的地在道路左侧",upcoming:"您即将到达您的{nth}个目的地，目的地在道路左侧",short:"已到达目的地","short-upcoming":"即将到达目的地",named:"您已到达{waypoint_name}，目的地在您左边。"},"sharp right":{default:"您已经到达您的{nth}个目的地，目的地在道路右侧",upcoming:"您即将到达您的{nth}个目的地，目的地在道路右侧",short:"已到达目的地","short-upcoming":"即将到达目的地",named:"您已到达{waypoint_name}，目的地在您右边。"},"slight right":{default:"您已经到达您的{nth}个目的地，目的地在道路左侧",upcoming:"您即将到达您的{nth}个目的地，目的地在道路左侧",short:"已到达目的地","short-upcoming":"即将到达目的地",named:"您已到达{waypoint_name}，目的地在您右边。"},"slight left":{default:"您已经到达您的{nth}个目的地，目的地在道路右侧",upcoming:"您即将到达您的{nth}个目的地，目的地在道路右侧",short:"已到达目的地","short-upcoming":"即将到达目的地",named:"您已到达{waypoint_name}，目的地在您左边。"},straight:{default:"您已经到达您的{nth}个目的地，目的地在您正前方",upcoming:"您即将到达您的{nth}个目的地，目的地在您正前方",short:"已到达目的地","short-upcoming":"即将到达目的地",named:"您已到达{waypoint_name}，目的地在您前方。"}},continue:{default:{default:"{modifier}行驶",name:"在{way_name}上继续{modifier}行驶",destination:"{modifier}行驶，{destination}方向",exit:"{modifier}行驶，驶入{way_name}"},straight:{default:"继续直行",name:"在{way_name}上继续直行",destination:"继续直行，前往{destination}",distance:"继续直行{distance}",namedistance:"继续在{way_name}上直行{distance}"},"sharp left":{default:"前方左急转弯",name:"前方左急转弯，继续在{way_name}上行驶",destination:"左急转弯，前往{destination}"},"sharp right":{default:"前方右急转弯",name:"前方右急转弯，继续在{way_name}上行驶",destination:"右急转弯，前往{destination}"},"slight left":{default:"前方稍向左转",name:"前方稍向左转，继续在{way_name}上行驶",destination:"稍向左转，前往{destination}"},"slight right":{default:"前方稍向右转",name:"前方稍向右转，继续在{way_name}上行驶",destination:"前方稍向右转，前往{destination}"},uturn:{default:"前方调头",name:"前方调头，继续在{way_name}上行驶",destination:"前方调头，前往{destination}"}},depart:{default:{default:"出发向{direction}",name:"出发向{direction}，驶入{way_name}",namedistance:"出发向{direction}，在{way_name}上继续行驶{distance}"}},"end of road":{default:{default:"{modifier}行驶",name:"{modifier}行驶，驶入{way_name}",destination:"{modifier}行驶，前往{destination}"},straight:{default:"继续直行",name:"继续直行，驶入{way_name}",destination:"继续直行，前往{destination}"},uturn:{default:"在道路尽头调头",name:"在道路尽头调头驶入{way_name}",destination:"在道路尽头调头，前往{destination}"}},fork:{default:{default:"在岔道保持{modifier}",name:"在岔道口保持{modifier}，驶入{way_name}",destination:"在岔道口保持{modifier}，前往{destination}"},"slight left":{default:"在岔道口保持左侧行驶",name:"在岔道口保持左侧行驶，驶入{way_name}",destination:"在岔道口保持左侧行驶，前往{destination}"},"slight right":{default:"在岔道口保持右侧行驶",name:"在岔道口保持右侧行驶，驶入{way_name}",destination:"在岔道口保持右侧行驶，前往{destination}"},"sharp left":{default:"在岔道口左急转弯",name:"在岔道口左急转弯，驶入{way_name}",destination:"在岔道口左急转弯，前往{destination}"},"sharp right":{default:"在岔道口右急转弯",name:"在岔道口右急转弯，驶入{way_name}",destination:"在岔道口右急转弯，前往{destination}"},uturn:{default:"前方调头",name:"前方调头，驶入{way_name}",destination:"前方调头，前往{destination}"}},merge:{default:{default:"{modifier}并道",name:"{modifier}并道，驶入{way_name}",destination:"{modifier}并道，前往{destination}"},straight:{default:"直行并道",name:"直行并道，驶入{way_name}",destination:"直行并道，前往{destination}"},"slight left":{default:"稍向左并道",name:"稍向左并道，驶入{way_name}",destination:"稍向左并道，前往{destination}"},"slight right":{default:"稍向右并道",name:"稍向右并道，驶入{way_name}",destination:"稍向右并道，前往{destination}"},"sharp left":{default:"急向左并道",name:"急向左并道，驶入{way_name}",destination:"急向左并道，前往{destination}"},"sharp right":{default:"急向右并道",name:"急向右并道，驶入{way_name}",destination:"急向右并道，前往{destination}"},uturn:{default:"前方调头",name:"前方调头，驶入{way_name}",destination:"前方调头，前往{destination}"}},"new name":{default:{default:"继续{modifier}",name:"继续{modifier}，驶入{way_name}",destination:"继续{modifier}，前往{destination}"},straight:{default:"继续直行",name:"继续在{way_name}上直行",destination:"继续直行，前往{destination}"},"sharp left":{default:"前方左急转弯",name:"前方左急转弯，驶入{way_name}",destination:"左急转弯，前往{destination}"},"sharp right":{default:"前方右急转弯",name:"前方右急转弯，驶入{way_name}",destination:"右急转弯，前往{destination}"},"slight left":{default:"继续稍向左",name:"继续稍向左，驶入{way_name}",destination:"继续稍向左，前往{destination}"},"slight right":{default:"继续稍向右",name:"继续稍向右，驶入{way_name}",destination:"继续稍向右，前往{destination}"},uturn:{default:"前方调头",name:"前方调头，上{way_name}",destination:"前方调头，前往{destination}"}},notification:{default:{default:"继续{modifier}",name:"继续{modifier}，驶入{way_name}",destination:"继续{modifier}，前往{destination}"},uturn:{default:"前方调头",name:"前方调头，驶入{way_name}",destination:"前方调头，前往{destination}"}},"off ramp":{default:{default:"下匝道",name:"下匝道，驶入{way_name}",destination:"下匝道，前往{destination}",exit:"从{exit}出口驶出",exit_destination:"从{exit}出口驶出，前往{destination}"},left:{default:"下左侧匝道",name:"下左侧匝道，上{way_name}",destination:"下左侧匝道，前往{destination}",exit:"从左侧{exit}出口驶出",exit_destination:"从左侧{exit}出口驶出，前往{destination}"},right:{default:"下右侧匝道",name:"下右侧匝道，驶入{way_name}",destination:"下右侧匝道，前往{destination}",exit:"从右侧{exit}出口驶出",exit_destination:"从右侧{exit}出口驶出，前往{destination}"},"sharp left":{default:"急向左下匝道",name:"急向左下匝道，驶入{way_name}",destination:"急向左下匝道，前往{destination}",exit:"从左侧{exit}出口驶出",exit_destination:"从左侧{exit}出口驶出，前往{destination}"},"sharp right":{default:"急向右下匝道",name:"急向右下匝道，驶入{way_name}",destination:"急向右下匝道，前往{destination}",exit:"从右侧{exit}出口驶出",exit_destination:"从右侧{exit}出口驶出，前往{destination}"},"slight left":{default:"稍向左下匝道",name:"稍向左下匝道，驶入{way_name}",destination:"稍向左下匝道，前往{destination}",exit:"从左侧{exit}出口驶出",exit_destination:"从左侧{exit}出口驶出，前往{destination}"},"slight right":{default:"稍向右下匝道",name:"稍向右下匝道，驶入{way_name}",destination:"稍向右下匝道，前往{destination}",exit:"从右侧{exit}出口驶出",exit_destination:"从右侧{exit}出口驶出，前往{destination}"}},"on ramp":{default:{default:"上匝道",name:"上匝道，驶入{way_name}",destination:"上匝道，前往{destination}"},left:{default:"上左侧匝道",name:"上左侧匝道，驶入{way_name}",destination:"上左侧匝道，前往{destination}"},right:{default:"上右侧匝道",name:"上右侧匝道，驶入{way_name}",destination:"上右侧匝道，前往{destination}"},"sharp left":{default:"急向左上匝道",name:"急向左上匝道，驶入{way_name}",destination:"急向左上匝道，前往{destination}"},"sharp right":{default:"急向右上匝道",name:"急向右上匝道，驶入{way_name}",destination:"急向右上匝道，前往{destination}"},"slight left":{default:"稍向左上匝道",name:"稍向左上匝道，驶入{way_name}",destination:"稍向左上匝道，前往{destination}"},"slight right":{default:"稍向右上匝道",name:"稍向右上匝道，驶入{way_name}",destination:"稍向右上匝道，前往{destination}"}},rotary:{default:{default:{default:"进入环岛",name:"通过环岛后驶入{way_name}",destination:"通过环岛后前往{destination}"},name:{default:"进入{rotary_name}环岛",name:"通过{rotary_name}环岛后驶入{way_name}",destination:"通过{rotary_name}环岛后前往{destination}"},exit:{default:"进入环岛后从{exit_number}出口驶出",name:"进入环岛后从{exit_number}出口驶出，上{way_name}",destination:"进入环岛后从{exit_number}出口驶出，前往{destination}"},name_exit:{default:"进入{rotary_name}环岛后从{exit_number}出口驶出",name:"进入{rotary_name}环岛后从{exit_number}出口驶出，上{way_name}",destination:"进入{rotary_name}环岛后从{exit_number}出口驶出，前往{destination}"}}},roundabout:{default:{exit:{default:"进入环岛后从{exit_number}出口驶出",name:"进入环岛后从{exit_number}出口驶出，上{way_name}",destination:"进入环岛后从{exit_number}出口驶出，前往{destination}"},default:{default:"进入环岛",name:"通过环岛后驶入{way_name}",destination:"通过环岛后前往{destination}"}}},"roundabout turn":{default:{default:"{modifier}转弯",name:"{modifier}转弯，驶入{way_name}",destination:"{modifier}转弯，前往{destination}"},left:{default:"左转",name:"左转，驶入{way_name}",destination:"左转，前往{destination}"},right:{default:"右转",name:"右转，驶入{way_name}",destination:"右转，前往{destination}"},straight:{default:"继续直行",name:"继续直行，驶入{way_name}",destination:"继续直行，前往{destination}"}},"exit roundabout":{default:{default:"驶离环岛",name:"驶离环岛，驶入{way_name}",destination:"驶离环岛，前往{destination}"}},"exit rotary":{default:{default:"驶离环岛",name:"驶离环岛，驶入{way_name}",destination:"驶离环岛，前往{destination}"}},turn:{default:{default:"{modifier}转弯",name:"{modifier}转弯，驶入{way_name}",destination:"{modifier}转弯，前往{destination}"},left:{default:"左转",name:"左转，驶入{way_name}",destination:"左转，前往{destination}"},right:{default:"右转",name:"右转，驶入{way_name}",destination:"右转，前往{destination}"},straight:{default:"直行",name:"直行，驶入{way_name}",destination:"直行，前往{destination}"}},"use lane":{no_lanes:{default:"继续直行"},default:{default:"{lane_instruction}"}}}}},{}],48:[function(_dereq_,module,exports){(function(global){(function(){"use strict";var L=typeof window!=="undefined"?window["L"]:typeof global!=="undefined"?global["L"]:null;module.exports=L.Class.extend({options:{timeout:500,blurTimeout:100,noResultsMessage:"No results found."},initialize:function(elem,callback,context,options){L.setOptions(this,options);this._elem=elem;this._resultFn=options.resultFn?L.Util.bind(options.resultFn,options.resultContext):null;this._autocomplete=options.autocompleteFn?L.Util.bind(options.autocompleteFn,options.autocompleteContext):null;this._selectFn=L.Util.bind(callback,context);this._container=L.DomUtil.create("div","leaflet-routing-geocoder-result");this._resultTable=L.DomUtil.create("table","",this._container);L.DomEvent.addListener(this._elem,"input",this._keyPressed,this);L.DomEvent.addListener(this._elem,"keypress",this._keyPressed,this);L.DomEvent.addListener(this._elem,"keydown",this._keyDown,this);L.DomEvent.addListener(this._elem,"blur",function(){if(this._isOpen){this.close()}},this)},close:function(){L.DomUtil.removeClass(this._container,"leaflet-routing-geocoder-result-open");this._isOpen=false},_open:function(){var rect=this._elem.getBoundingClientRect();if(!this._container.parentElement){var scrollX=window.pageXOffset!==undefined?window.pageXOffset:(document.documentElement||document.body.parentNode||document.body).scrollLeft;var scrollY=window.pageYOffset!==undefined?window.pageYOffset:(document.documentElement||document.body.parentNode||document.body).scrollTop;this._container.style.left=rect.left+scrollX+"px";this._container.style.top=rect.bottom+scrollY+"px";this._container.style.width=rect.right-rect.left+"px";document.body.appendChild(this._container)}L.DomUtil.addClass(this._container,"leaflet-routing-geocoder-result-open");this._isOpen=true},_setResults:function(results){var i,tr,td,text;delete this._selection;this._results=results;while(this._resultTable.firstChild){this._resultTable.removeChild(this._resultTable.firstChild)}for(i=0;i<results.length;i++){tr=L.DomUtil.create("tr","",this._resultTable);tr.setAttribute("data-result-index",i);td=L.DomUtil.create("td","",tr);text=document.createTextNode(results[i].name);td.appendChild(text);L.DomEvent.addListener(td,"mousedown",L.DomEvent.preventDefault);L.DomEvent.addListener(td,"click",this._createClickListener(results[i]))}if(!i){tr=L.DomUtil.create("tr","",this._resultTable);td=L.DomUtil.create("td","leaflet-routing-geocoder-no-results",tr);td.innerHTML=this.options.noResultsMessage}this._open();if(results.length>0){this._select(1)}},_createClickListener:function(r){var resultSelected=this._resultSelected(r);return L.bind(function(){this._elem.blur();resultSelected()},this)},_resultSelected:function(r){return L.bind(function(){this.close();this._elem.value=r.name;this._lastCompletedText=r.name;this._selectFn(r)},this)},_keyPressed:function(e){var index;if(this._isOpen&&e.keyCode===13&&this._selection){index=parseInt(this._selection.getAttribute("data-result-index"),10);this._resultSelected(this._results[index])();L.DomEvent.preventDefault(e);return}if(e.keyCode===13){L.DomEvent.preventDefault(e);this._complete(this._resultFn,true);return}if(this._autocomplete&&document.activeElement===this._elem){if(this._timer){clearTimeout(this._timer)}this._timer=setTimeout(L.Util.bind(function(){this._complete(this._autocomplete)},this),this.options.timeout);return}this._unselect()},_select:function(dir){var sel=this._selection;if(sel){L.DomUtil.removeClass(sel.firstChild,"leaflet-routing-geocoder-selected");sel=sel[dir>0?"nextSibling":"previousSibling"]}if(!sel){sel=this._resultTable[dir>0?"firstChild":"lastChild"]}if(sel){L.DomUtil.addClass(sel.firstChild,"leaflet-routing-geocoder-selected");this._selection=sel}},_unselect:function(){if(this._selection){L.DomUtil.removeClass(this._selection.firstChild,"leaflet-routing-geocoder-selected")}delete this._selection},_keyDown:function(e){if(this._isOpen){switch(e.keyCode){case 27:this.close();L.DomEvent.preventDefault(e);return;case 38:this._select(-1);L.DomEvent.preventDefault(e);return;case 40:this._select(1);L.DomEvent.preventDefault(e);return}}},_complete:function(completeFn,trySelect){var v=this._elem.value;function completeResults(results){this._lastCompletedText=v;if(trySelect&&results.length===1){this._resultSelected(results[0])()}else{this._setResults(results)}}if(!v){return}if(v!==this._lastCompletedText){completeFn(v,completeResults,this)}else if(trySelect){completeResults.call(this,this._results)}}})})()}).call(this,typeof global!=="undefined"?global:typeof self!=="undefined"?self:typeof window!=="undefined"?window:{})},{}],49:[function(_dereq_,module,exports){(function(global){(function(){"use strict";var L=typeof window!=="undefined"?window["L"]:typeof global!=="undefined"?global["L"]:null;var Itinerary=_dereq_("./itinerary");var Line=_dereq_("./line");var Plan=_dereq_("./plan");var OSRMv1=_dereq_("./osrm-v1");module.exports=Itinerary.extend({options:{fitSelectedRoutes:"smart",routeLine:function(route,options){return new Line(route,options)},autoRoute:true,routeWhileDragging:false,routeDragInterval:500,waypointMode:"connect",showAlternatives:false,defaultErrorHandler:function(e){console.error("Routing error:",e.error)}},initialize:function(options){L.Util.setOptions(this,options);this._router=this.options.router||new OSRMv1(options);this._plan=this.options.plan||new Plan(this.options.waypoints,options);this._requestCount=0;Itinerary.prototype.initialize.call(this,options);this.on("routeselected",this._routeSelected,this);if(this.options.defaultErrorHandler){this.on("routingerror",this.options.defaultErrorHandler)}this._plan.on("waypointschanged",this._onWaypointsChanged,this);if(options.routeWhileDragging){this._setupRouteDragging()}},_onZoomEnd:function(){if(!this._selectedRoute||!this._router.requiresMoreDetail){return}var map=this._map;if(this._router.requiresMoreDetail(this._selectedRoute,map.getZoom(),map.getBounds())){this.route({callback:L.bind(function(err,routes){var i;if(!err){for(i=0;i<routes.length;i++){this._routes[i].properties=routes[i].properties}this._updateLineCallback(err,routes)}},this),simplifyGeometry:false,geometryOnly:true})}},onAdd:function(map){if(this.options.autoRoute){this.route()}var container=Itinerary.prototype.onAdd.call(this,map);this._map=map;this._map.addLayer(this._plan);this._map.on("zoomend",this._onZoomEnd,this);if(this._plan.options.geocoder){container.insertBefore(this._plan.createGeocoders(),container.firstChild)}return container},onRemove:function(map){map.off("zoomend",this._onZoomEnd,this);if(this._line){map.removeLayer(this._line)}map.removeLayer(this._plan);if(this._alternatives&&this._alternatives.length>0){for(var i=0,len=this._alternatives.length;i<len;i++){map.removeLayer(this._alternatives[i])}}return Itinerary.prototype.onRemove.call(this,map)},getWaypoints:function(){return this._plan.getWaypoints()},setWaypoints:function(waypoints){this._plan.setWaypoints(waypoints);return this},spliceWaypoints:function(){var removed=this._plan.spliceWaypoints.apply(this._plan,arguments);return removed},getPlan:function(){return this._plan},getRouter:function(){return this._router},_routeSelected:function(e){var route=this._selectedRoute=e.route,alternatives=this.options.showAlternatives&&e.alternatives,fitMode=this.options.fitSelectedRoutes,fitBounds=fitMode==="smart"&&!this._waypointsVisible()||fitMode!=="smart"&&fitMode;this._updateLines({route:route,alternatives:alternatives});if(fitBounds){this._map.fitBounds(this._line.getBounds())}if(this.options.waypointMode==="snap"){this._plan.off("waypointschanged",this._onWaypointsChanged,this);this.setWaypoints(route.waypoints);this._plan.on("waypointschanged",this._onWaypointsChanged,this)}},_waypointsVisible:function(){var wps=this.getWaypoints(),mapSize,bounds,boundsSize,i,p;try{mapSize=this._map.getSize();for(i=0;i<wps.length;i++){p=this._map.latLngToLayerPoint(wps[i].latLng);if(bounds){bounds.extend(p)}else{bounds=L.bounds([p])}}boundsSize=bounds.getSize();return(boundsSize.x>mapSize.x/5||boundsSize.y>mapSize.y/5)&&this._waypointsInViewport()}catch(e){return false}},_waypointsInViewport:function(){var wps=this.getWaypoints(),mapBounds,i;try{mapBounds=this._map.getBounds()}catch(e){return false}for(i=0;i<wps.length;i++){if(mapBounds.contains(wps[i].latLng)){return true}}return false},_updateLines:function(routes){var addWaypoints=this.options.addWaypoints!==undefined?this.options.addWaypoints:true;this._clearLines();this._alternatives=[];if(routes.alternatives)routes.alternatives.forEach(function(alt,i){this._alternatives[i]=this.options.routeLine(alt,L.extend({isAlternative:true},this.options.altLineOptions||this.options.lineOptions));this._alternatives[i].addTo(this._map);this._hookAltEvents(this._alternatives[i])},this);this._line=this.options.routeLine(routes.route,L.extend({addWaypoints:addWaypoints,extendToWaypoints:this.options.waypointMode==="connect"},this.options.lineOptions));this._line.addTo(this._map);this._hookEvents(this._line)},_hookEvents:function(l){l.on("linetouched",function(e){this._plan.dragNewWaypoint(e)},this)},_hookAltEvents:function(l){l.on("linetouched",function(e){var alts=this._routes.slice();var selected=alts.splice(e.target._route.routesIndex,1)[0];this.fire("routeselected",{route:selected,alternatives:alts})},this)},_onWaypointsChanged:function(e){if(this.options.autoRoute){this.route({})}if(!this._plan.isReady()){this._clearLines();this._clearAlts()}this.fire("waypointschanged",{waypoints:e.waypoints})},_setupRouteDragging:function(){var timer=0,waypoints;this._plan.on("waypointdrag",L.bind(function(e){waypoints=e.waypoints;if(!timer){timer=setTimeout(L.bind(function(){this.route({waypoints:waypoints,geometryOnly:true,callback:L.bind(this._updateLineCallback,this)});timer=undefined},this),this.options.routeDragInterval)}},this));this._plan.on("waypointdragend",function(){if(timer){clearTimeout(timer);timer=undefined}this.route()},this)},_updateLineCallback:function(err,routes){if(!err){routes=routes.slice();var selected=routes.splice(this._selectedRoute.routesIndex,1)[0];this._updateLines({route:selected,alternatives:this.options.showAlternatives?routes:[]})}else if(err.type!=="abort"){this._clearLines()}},route:function(options){var ts=++this._requestCount,wps;if(this._pendingRequest&&this._pendingRequest.abort){this._pendingRequest.abort();this._pendingRequest=null}options=options||{};if(this._plan.isReady()){if(this.options.useZoomParameter){options.z=this._map&&this._map.getZoom()}wps=options&&options.waypoints||this._plan.getWaypoints();this.fire("routingstart",{waypoints:wps})
;this._pendingRequest=this._router.route(wps,function(err,routes){this._pendingRequest=null;if(options.callback){return options.callback.call(this,err,routes)}if(ts===this._requestCount){this._clearLines();this._clearAlts();if(err&&err.type!=="abort"){this.fire("routingerror",{error:err});return}routes.forEach(function(route,i){route.routesIndex=i});if(!options.geometryOnly){this.fire("routesfound",{waypoints:wps,routes:routes});this.setAlternatives(routes)}else{var selectedRoute=routes.splice(0,1)[0];this._routeSelected({route:selectedRoute,alternatives:routes})}}},this,options)}},_clearLines:function(){if(this._line){this._map.removeLayer(this._line);delete this._line}if(this._alternatives&&this._alternatives.length){for(var i in this._alternatives){this._map.removeLayer(this._alternatives[i])}this._alternatives=[]}}})})()}).call(this,typeof global!=="undefined"?global:typeof self!=="undefined"?self:typeof window!=="undefined"?window:{})},{"./itinerary":55,"./line":56,"./osrm-v1":59,"./plan":60}],50:[function(_dereq_,module,exports){(function(global){(function(){"use strict";var L=typeof window!=="undefined"?window["L"]:typeof global!=="undefined"?global["L"]:null;module.exports=L.Control.extend({options:{header:"Routing error",formatMessage:function(error){if(error.status<0){return"Calculating the route caused an error. Technical description follows: <code><pre>"+error.message+"</pre></code"}else{return"The route could not be calculated. "+error.message}}},initialize:function(routingControl,options){L.Control.prototype.initialize.call(this,options);routingControl.on("routingerror",L.bind(function(e){if(this._element){this._element.children[1].innerHTML=this.options.formatMessage(e.error);this._element.style.visibility="visible"}},this)).on("routingstart",L.bind(function(){if(this._element){this._element.style.visibility="hidden"}},this))},onAdd:function(){var header,message;this._element=L.DomUtil.create("div","leaflet-bar leaflet-routing-error");this._element.style.visibility="hidden";header=L.DomUtil.create("h3",null,this._element);message=L.DomUtil.create("span",null,this._element);header.innerHTML=this.options.header;return this._element},onRemove:function(){delete this._element}})})()}).call(this,typeof global!=="undefined"?global:typeof self!=="undefined"?self:typeof window!=="undefined"?window:{})},{}],51:[function(_dereq_,module,exports){(function(global){(function(){"use strict";var L=typeof window!=="undefined"?window["L"]:typeof global!=="undefined"?global["L"]:null;var Localization=_dereq_("./localization");module.exports=L.Class.extend({options:{units:"metric",unitNames:null,language:"en",roundingSensitivity:1,distanceTemplate:"{value} {unit}"},initialize:function(options){L.setOptions(this,options);var langs=L.Util.isArray(this.options.language)?this.options.language:[this.options.language,"en"];this._localization=new Localization(langs)},formatDistance:function(d,sensitivity){var un=this.options.unitNames||this._localization.localize("units"),simpleRounding=sensitivity<=0,round=simpleRounding?function(v){return v}:L.bind(this._round,this),v,yards,data,pow10;if(this.options.units==="imperial"){yards=d/.9144;if(yards>=1e3){data={value:round(d/1609.344,sensitivity),unit:un.miles}}else{data={value:round(yards,sensitivity),unit:un.yards}}}else{v=round(d,sensitivity);data={value:v>=1e3?v/1e3:v,unit:v>=1e3?un.kilometers:un.meters}}if(simpleRounding){data.value=data.value.toFixed(-sensitivity)}return L.Util.template(this.options.distanceTemplate,data)},_round:function(d,sensitivity){var s=sensitivity||this.options.roundingSensitivity,pow10=Math.pow(10,(Math.floor(d/s)+"").length-1),r=Math.floor(d/pow10),p=r>5?pow10:pow10/2;return Math.round(d/p)*p},formatTime:function(t){var un=this.options.unitNames||this._localization.localize("units");t=Math.round(t/30)*30;if(t>86400){return Math.round(t/3600)+" "+un.hours}else if(t>3600){return Math.floor(t/3600)+" "+un.hours+" "+Math.round(t%3600/60)+" "+un.minutes}else if(t>300){return Math.round(t/60)+" "+un.minutes}else if(t>60){return Math.floor(t/60)+" "+un.minutes+(t%60!==0?" "+t%60+" "+un.seconds:"")}else{return t+" "+un.seconds}},formatInstruction:function(instr,i){if(instr.text===undefined){return this.capitalize(L.Util.template(this._getInstructionTemplate(instr,i),L.extend({},instr,{exitStr:instr.exit?this._localization.localize("formatOrder")(instr.exit):"",dir:this._localization.localize(["directions",instr.direction]),modifier:this._localization.localize(["directions",instr.modifier])})))}else{return instr.text}},getIconName:function(instr,i){switch(instr.type){case"Head":if(i===0){return"depart"}break;case"WaypointReached":return"via";case"Roundabout":return"enter-roundabout";case"DestinationReached":return"arrive"}switch(instr.modifier){case"Straight":return"continue";case"SlightRight":return"bear-right";case"Right":return"turn-right";case"SharpRight":return"sharp-right";case"TurnAround":case"Uturn":return"u-turn";case"SharpLeft":return"sharp-left";case"Left":return"turn-left";case"SlightLeft":return"bear-left"}},capitalize:function(s){return s.charAt(0).toUpperCase()+s.substring(1)},_getInstructionTemplate:function(instr,i){var type=instr.type==="Straight"?i===0?"Head":"Continue":instr.type,strings=this._localization.localize(["instructions",type]);if(!strings){strings=[this._localization.localize(["directions",type])," "+this._localization.localize(["instructions","Onto"])]}return strings[0]+(strings.length>1&&instr.road?strings[1]:"")}})})()}).call(this,typeof global!=="undefined"?global:typeof self!=="undefined"?self:typeof window!=="undefined"?window:{})},{"./localization":57}],52:[function(_dereq_,module,exports){(function(global){(function(){"use strict";var L=typeof window!=="undefined"?window["L"]:typeof global!=="undefined"?global["L"]:null;var Autocomplete=_dereq_("./autocomplete");var Localization=_dereq_("./localization");function selectInputText(input){if(input.setSelectionRange){input.setSelectionRange(0,9999)}else{input.select()}}module.exports=L.Class.extend({includes:typeof L.Evented!=="undefined"&&L.Evented.prototype||L.Mixin.Events,options:{createGeocoder:function(i,nWps,options){var container=L.DomUtil.create("div","leaflet-routing-geocoder"),input=L.DomUtil.create("input","",container),remove=options.addWaypoints?L.DomUtil.create("span","leaflet-routing-remove-waypoint",container):undefined;input.disabled=!options.addWaypoints;return{container:container,input:input,closeButton:remove}},geocoderPlaceholder:function(i,numberWaypoints,geocoderElement){var l=new Localization(geocoderElement.options.language).localize("ui");return i===0?l.startPlaceholder:i<numberWaypoints-1?L.Util.template(l.viaPlaceholder,{viaNumber:i}):l.endPlaceholder},geocoderClass:function(){return""},waypointNameFallback:function(latLng){var ns=latLng.lat<0?"S":"N",ew=latLng.lng<0?"W":"E",lat=(Math.round(Math.abs(latLng.lat)*1e4)/1e4).toString(),lng=(Math.round(Math.abs(latLng.lng)*1e4)/1e4).toString();return ns+lat+", "+ew+lng},maxGeocoderTolerance:200,autocompleteOptions:{},language:"en"},initialize:function(wp,i,nWps,options){L.setOptions(this,options);var g=this.options.createGeocoder(i,nWps,this.options),closeButton=g.closeButton,geocoderInput=g.input;geocoderInput.setAttribute("placeholder",this.options.geocoderPlaceholder(i,nWps,this));geocoderInput.className=this.options.geocoderClass(i,nWps);this._element=g;this._waypoint=wp;this.update();geocoderInput.value=wp.name;L.DomEvent.addListener(geocoderInput,"click",function(){selectInputText(this)},geocoderInput);if(closeButton){L.DomEvent.addListener(closeButton,"click",function(){this.fire("delete",{waypoint:this._waypoint})},this)}new Autocomplete(geocoderInput,function(r){geocoderInput.value=r.name;wp.name=r.name;wp.latLng=r.center;this.fire("geocoded",{waypoint:wp,value:r})},this,L.extend({resultFn:this.options.geocoder.geocode,resultContext:this.options.geocoder,autocompleteFn:this.options.geocoder.suggest,autocompleteContext:this.options.geocoder},this.options.autocompleteOptions))},getContainer:function(){return this._element.container},setValue:function(v){this._element.input.value=v},update:function(force){var wp=this._waypoint,wpCoords;wp.name=wp.name||"";if(wp.latLng&&(force||!wp.name)){wpCoords=this.options.waypointNameFallback(wp.latLng);if(this.options.geocoder&&this.options.geocoder.reverse){this.options.geocoder.reverse(wp.latLng,67108864,function(rs){if(rs.length>0&&rs[0].center.distanceTo(wp.latLng)<this.options.maxGeocoderTolerance){wp.name=rs[0].name}else{wp.name=wpCoords}this._update()},this)}else{wp.name=wpCoords;this._update()}}},focus:function(){var input=this._element.input;input.focus();selectInputText(input)},_update:function(){var wp=this._waypoint,value=wp&&wp.name?wp.name:"";this.setValue(value);this.fire("reversegeocoded",{waypoint:wp,value:value})}})})()}).call(this,typeof global!=="undefined"?global:typeof self!=="undefined"?self:typeof window!=="undefined"?window:{})},{"./autocomplete":48,"./localization":57}],53:[function(_dereq_,module,exports){(function(global){var L=typeof window!=="undefined"?window["L"]:typeof global!=="undefined"?global["L"]:null,Control=_dereq_("./control"),Itinerary=_dereq_("./itinerary"),Line=_dereq_("./line"),OSRMv1=_dereq_("./osrm-v1"),Plan=_dereq_("./plan"),Waypoint=_dereq_("./waypoint"),Autocomplete=_dereq_("./autocomplete"),Formatter=_dereq_("./formatter"),GeocoderElement=_dereq_("./geocoder-element"),Localization=_dereq_("./localization"),ItineraryBuilder=_dereq_("./itinerary-builder"),Mapbox=_dereq_("./mapbox"),ErrorControl=_dereq_("./error-control");L.routing={control:function(options){return new Control(options)},itinerary:function(options){return Itinerary(options)},line:function(route,options){return new Line(route,options)},plan:function(waypoints,options){return new Plan(waypoints,options)},waypoint:function(latLng,name,options){return new Waypoint(latLng,name,options)},osrmv1:function(options){return new OSRMv1(options)},localization:function(options){return new Localization(options)},formatter:function(options){return new Formatter(options)},geocoderElement:function(wp,i,nWps,plan){return new L.Routing.GeocoderElement(wp,i,nWps,plan)},itineraryBuilder:function(options){return new ItineraryBuilder(options)},mapbox:function(accessToken,options){return new Mapbox(accessToken,options)},errorControl:function(routingControl,options){return new ErrorControl(routingControl,options)},autocomplete:function(elem,callback,context,options){return new Autocomplete(elem,callback,context,options)}};module.exports=L.Routing={Control:Control,Itinerary:Itinerary,Line:Line,OSRMv1:OSRMv1,Plan:Plan,Waypoint:Waypoint,Autocomplete:Autocomplete,Formatter:Formatter,GeocoderElement:GeocoderElement,Localization:Localization,Formatter:Formatter,ItineraryBuilder:ItineraryBuilder,control:L.routing.control,itinerary:L.routing.itinerary,line:L.routing.line,plan:L.routing.plan,waypoint:L.routing.waypoint,osrmv1:L.routing.osrmv1,geocoderElement:L.routing.geocoderElement,mapbox:L.routing.mapbox,errorControl:L.routing.errorControl}}).call(this,typeof global!=="undefined"?global:typeof self!=="undefined"?self:typeof window!=="undefined"?window:{})},{"./autocomplete":48,"./control":49,"./error-control":50,"./formatter":51,"./geocoder-element":52,"./itinerary":55,"./itinerary-builder":54,"./line":56,"./localization":57,"./mapbox":58,"./osrm-v1":59,"./plan":60,"./waypoint":61}],54:[function(_dereq_,module,exports){(function(global){(function(){"use strict";var L=typeof window!=="undefined"?window["L"]:typeof global!=="undefined"?global["L"]:null;module.exports=L.Class.extend({options:{containerClassName:""},initialize:function(options){L.setOptions(this,options)},createContainer:function(className){var table=L.DomUtil.create("table",(className||"")+" "+this.options.containerClassName),colgroup=L.DomUtil.create("colgroup","",table);L.DomUtil.create("col","leaflet-routing-instruction-icon",colgroup);L.DomUtil.create("col","leaflet-routing-instruction-text",colgroup);L.DomUtil.create("col","leaflet-routing-instruction-distance",colgroup);return table},createStepsContainer:function(){return L.DomUtil.create("tbody","")},createStep:function(text,distance,icon,steps){var row=L.DomUtil.create("tr","",steps),span,td;td=L.DomUtil.create("td","",row);span=L.DomUtil.create("span","leaflet-routing-icon leaflet-routing-icon-"+icon,td);td.appendChild(span);td=L.DomUtil.create("td","",row);td.appendChild(document.createTextNode(text));td=L.DomUtil.create("td","",row);td.appendChild(document.createTextNode(distance));return row}})})()}).call(this,typeof global!=="undefined"?global:typeof self!=="undefined"?self:typeof window!=="undefined"?window:{})},{}],55:[function(_dereq_,module,exports){(function(global){(function(){"use strict";var L=typeof window!=="undefined"?window["L"]:typeof global!=="undefined"?global["L"]:null;var Formatter=_dereq_("./formatter");var ItineraryBuilder=_dereq_("./itinerary-builder");module.exports=L.Control.extend({includes:typeof L.Evented!=="undefined"&&L.Evented.prototype||L.Mixin.Events,options:{pointMarkerStyle:{radius:5,color:"#03f",fillColor:"white",opacity:1,fillOpacity:.7},summaryTemplate:"<h2>{name}</h2><h3>{distance}, {time}</h3>",timeTemplate:"{time}",containerClassName:"",alternativeClassName:"",minimizedClassName:"",itineraryClassName:"",totalDistanceRoundingSensitivity:-1,show:true,collapsible:undefined,collapseBtn:function(itinerary){var collapseBtn=L.DomUtil.create("span",itinerary.options.collapseBtnClass);L.DomEvent.on(collapseBtn,"click",itinerary._toggle,itinerary);itinerary._container.insertBefore(collapseBtn,itinerary._container.firstChild)},collapseBtnClass:"leaflet-routing-collapse-btn"},initialize:function(options){L.setOptions(this,options);this._formatter=this.options.formatter||new Formatter(this.options);this._itineraryBuilder=this.options.itineraryBuilder||new ItineraryBuilder({containerClassName:this.options.itineraryClassName})},onAdd:function(map){var collapsible=this.options.collapsible;collapsible=collapsible||collapsible===undefined&&map.getSize().x<=640;this._container=L.DomUtil.create("div","leaflet-routing-container leaflet-bar "+(!this.options.show?"leaflet-routing-container-hide ":"")+(collapsible?"leaflet-routing-collapsible ":"")+this.options.containerClassName);this._altContainer=this.createAlternativesContainer();this._container.appendChild(this._altContainer);L.DomEvent.disableClickPropagation(this._container);L.DomEvent.addListener(this._container,"mousewheel",function(e){L.DomEvent.stopPropagation(e)});if(collapsible){this.options.collapseBtn(this)}return this._container},onRemove:function(){},createAlternativesContainer:function(){return L.DomUtil.create("div","leaflet-routing-alternatives-container")},setAlternatives:function(routes){var i,alt,altDiv;this._clearAlts();this._routes=routes;for(i=0;i<this._routes.length;i++){alt=this._routes[i];altDiv=this._createAlternative(alt,i);this._altContainer.appendChild(altDiv);this._altElements.push(altDiv)}this._selectRoute({route:this._routes[0],alternatives:this._routes.slice(1)});return this},show:function(){L.DomUtil.removeClass(this._container,"leaflet-routing-container-hide")},hide:function(){L.DomUtil.addClass(this._container,"leaflet-routing-container-hide")},_toggle:function(){var collapsed=L.DomUtil.hasClass(this._container,"leaflet-routing-container-hide");this[collapsed?"show":"hide"]()},_createAlternative:function(alt,i){var altDiv=L.DomUtil.create("div","leaflet-routing-alt "+this.options.alternativeClassName+(i>0?" leaflet-routing-alt-minimized "+this.options.minimizedClassName:"")),template=this.options.summaryTemplate,data=L.extend({name:alt.name,distance:this._formatter.formatDistance(alt.summary.totalDistance,this.options.totalDistanceRoundingSensitivity),time:this._formatter.formatTime(alt.summary.totalTime)},alt);altDiv.innerHTML=typeof template==="function"?template(data):L.Util.template(template,data);L.DomEvent.addListener(altDiv,"click",this._onAltClicked,this);this.on("routeselected",this._selectAlt,this);altDiv.appendChild(this._createItineraryContainer(alt));return altDiv},_clearAlts:function(){var el=this._altContainer;while(el&&el.firstChild){el.removeChild(el.firstChild)}this._altElements=[]},_createItineraryContainer:function(r){var container=this._itineraryBuilder.createContainer(),steps=this._itineraryBuilder.createStepsContainer(),i,instr,step,distance,text,icon;container.appendChild(steps);for(i=0;i<r.instructions.length;i++){instr=r.instructions[i];text=this._formatter.formatInstruction(instr,i);distance=this._formatter.formatDistance(instr.distance);icon=this._formatter.getIconName(instr,i);step=this._itineraryBuilder.createStep(text,distance,icon,steps);if(instr.index){this._addRowListeners(step,r.coordinates[instr.index])}}return container},_addRowListeners:function(row,coordinate){L.DomEvent.addListener(row,"mouseover",function(){this._marker=L.circleMarker(coordinate,this.options.pointMarkerStyle).addTo(this._map)},this);L.DomEvent.addListener(row,"mouseout",function(){if(this._marker){this._map.removeLayer(this._marker);delete this._marker}},this);L.DomEvent.addListener(row,"click",function(e){this._map.panTo(coordinate);L.DomEvent.stopPropagation(e)},this)},_onAltClicked:function(e){var altElem=e.target||window.event.srcElement;while(!L.DomUtil.hasClass(altElem,"leaflet-routing-alt")){altElem=altElem.parentElement}var j=this._altElements.indexOf(altElem);var alts=this._routes.slice();var route=alts.splice(j,1)[0];this.fire("routeselected",{route:route,alternatives:alts})},_selectAlt:function(e){var altElem,j,n,classFn;altElem=this._altElements[e.route.routesIndex];if(L.DomUtil.hasClass(altElem,"leaflet-routing-alt-minimized")){for(j=0;j<this._altElements.length;j++){n=this._altElements[j];classFn=j===e.route.routesIndex?"removeClass":"addClass";L.DomUtil[classFn](n,"leaflet-routing-alt-minimized");if(this.options.minimizedClassName){L.DomUtil[classFn](n,this.options.minimizedClassName)}if(j!==e.route.routesIndex)n.scrollTop=0}}L.DomEvent.stop(e)},_selectRoute:function(routes){if(this._marker){this._map.removeLayer(this._marker);delete this._marker}this.fire("routeselected",routes)}})})()}).call(this,typeof global!=="undefined"?global:typeof self!=="undefined"?self:typeof window!=="undefined"?window:{})},{"./formatter":51,"./itinerary-builder":54}],56:[function(_dereq_,module,exports){(function(global){(function(){"use strict";var L=typeof window!=="undefined"?window["L"]:typeof global!=="undefined"?global["L"]:null;module.exports=L.LayerGroup.extend({includes:typeof L.Evented!=="undefined"&&L.Evented.prototype||L.Mixin.Events,options:{styles:[{color:"black",opacity:.15,weight:9},{color:"white",opacity:.8,weight:6},{color:"red",opacity:1,weight:2}],missingRouteStyles:[{color:"black",opacity:.15,weight:7},{color:"white",opacity:.6,weight:4},{color:"gray",opacity:.8,weight:2,dashArray:"7,12"}],addWaypoints:true,extendToWaypoints:true,missingRouteTolerance:10},initialize:function(route,options){L.setOptions(this,options);L.LayerGroup.prototype.initialize.call(this,options);this._route=route;if(this.options.extendToWaypoints){this._extendToWaypoints()}this._addSegment(route.coordinates,this.options.styles,this.options.addWaypoints)},getBounds:function(){return L.latLngBounds(this._route.coordinates)},_findWaypointIndices:function(){var wps=this._route.inputWaypoints,indices=[],i;for(i=0;i<wps.length;i++){indices.push(this._findClosestRoutePoint(wps[i].latLng))}return indices},_findClosestRoutePoint:function(latlng){var minDist=Number.MAX_VALUE,minIndex,i,d;for(i=this._route.coordinates.length-1;i>=0;i--){d=latlng.distanceTo(this._route.coordinates[i]);if(d<minDist){minIndex=i;minDist=d}}return minIndex},_extendToWaypoints:function(){var wps=this._route.inputWaypoints,wpIndices=this._getWaypointIndices(),i,wpLatLng,routeCoord;for(i=0;i<wps.length;i++){wpLatLng=wps[i].latLng;routeCoord=L.latLng(this._route.coordinates[wpIndices[i]]);if(wpLatLng.distanceTo(routeCoord)>this.options.missingRouteTolerance){this._addSegment([wpLatLng,routeCoord],this.options.missingRouteStyles)}}},_addSegment:function(coords,styles,mouselistener){var i,pl;for(i=0;i<styles.length;i++){pl=L.polyline(coords,styles[i]);this.addLayer(pl);if(mouselistener){pl.on("mousedown",this._onLineTouched,this)}}},_findNearestWpBefore:function(i){var wpIndices=this._getWaypointIndices(),j=wpIndices.length-1;while(j>=0&&wpIndices[j]>i){j--}return j},_onLineTouched:function(e){var afterIndex=this._findNearestWpBefore(this._findClosestRoutePoint(e.latlng));this.fire("linetouched",{afterIndex:afterIndex,latlng:e.latlng});L.DomEvent.stop(e)},_getWaypointIndices:function(){if(!this._wpIndices){this._wpIndices=this._route.waypointIndices||this._findWaypointIndices()}return this._wpIndices}})})()}).call(this,typeof global!=="undefined"?global:typeof self!=="undefined"?self:typeof window!=="undefined"?window:{})},{}],57:[function(_dereq_,module,exports){(function(){"use strict";var spanish={directions:{N:"norte",NE:"noreste",E:"este",SE:"sureste",S:"sur",SW:"suroeste",W:"oeste",NW:"noroeste",SlightRight:"leve giro a la derecha",Right:"derecha",SharpRight:"giro pronunciado a la derecha",SlightLeft:"leve giro a la izquierda",Left:"izquierda",SharpLeft:"giro pronunciado a la izquierda",Uturn:"media vuelta"},instructions:{Head:["Derecho {dir}"," sobre {road}"],Continue:["Continuar {dir}"," en {road}"],TurnAround:["Dar vuelta"],WaypointReached:["Llegó a un punto del camino"],Roundabout:["Tomar {exitStr} salida en la rotonda"," en {road}"],DestinationReached:["Llegada a destino"],Fork:["En el cruce gira a {modifier}"," hacia {road}"],Merge:["Incorpórate {modifier}"," hacia {road}"],OnRamp:["Gira {modifier} en la salida"," hacia {road}"],OffRamp:["Toma la salida {modifier}"," hacia {road}"],EndOfRoad:["Gira {modifier} al final de la carretera"," hacia {road}"],Onto:"hacia {road}"},formatOrder:function(n){return n+"º"},ui:{startPlaceholder:"Inicio",viaPlaceholder:"Via {viaNumber}",endPlaceholder:"Destino"},units:{meters:"m",kilometers:"km",yards:"yd",miles:"mi",hours:"h",minutes:"min",seconds:"s"}};L.Routing=L.Routing||{};var Localization=L.Class.extend({initialize:function(langs){this._langs=L.Util.isArray(langs)?langs.slice():[langs,"en"];for(var i=0,l=this._langs.length;i<l;i++){var generalizedCode=/([A-Za-z]+)/.exec(this._langs[i])[1];if(!Localization[this._langs[i]]){if(Localization[generalizedCode]){this._langs[i]=generalizedCode}else{throw new Error('No localization for language "'+this._langs[i]+'".')}}}},localize:function(keys){var dict,key,value;keys=L.Util.isArray(keys)?keys:[keys];for(var i=0,l=this._langs.length;i<l;i++){dict=Localization[this._langs[i]];for(var j=0,nKeys=keys.length;dict&&j<nKeys;j++){key=keys[j];value=dict[key];dict=value}if(value){return value}}}});module.exports=L.extend(Localization,{en:{directions:{N:"north",NE:"northeast",E:"east",SE:"southeast",S:"south",SW:"southwest",W:"west",NW:"northwest",SlightRight:"slight right",Right:"right",SharpRight:"sharp right",SlightLeft:"slight left",Left:"left",SharpLeft:"sharp left",Uturn:"Turn around"},instructions:{Head:["Head {dir}"," on {road}"],Continue:["Continue {dir}"],TurnAround:["Turn around"],WaypointReached:["Waypoint reached"],Roundabout:["Take the {exitStr} exit in the roundabout"," onto {road}"],DestinationReached:["Destination reached"],Fork:["At the fork, turn {modifier}"," onto {road}"],Merge:["Merge {modifier}"," onto {road}"],OnRamp:["Turn {modifier} on the ramp"," onto {road}"],OffRamp:["Take the ramp on the {modifier}"," onto {road}"],EndOfRoad:["Turn {modifier} at the end of the road"," onto {road}"],Onto:"onto {road}"},formatOrder:function(n){var i=n%10-1,suffix=["st","nd","rd"];return suffix[i]?n+suffix[i]:n+"th"},ui:{startPlaceholder:"Start",viaPlaceholder:"Via {viaNumber}",endPlaceholder:"End"},units:{meters:"m",kilometers:"km",yards:"yd",miles:"mi",hours:"h",minutes:"min",seconds:"s"}},de:{directions:{N:"Norden",NE:"Nordosten",E:"Osten",SE:"Südosten",S:"Süden",SW:"Südwesten",W:"Westen",NW:"Nordwesten",SlightRight:"leicht rechts",Right:"rechts",SharpRight:"scharf rechts",SlightLeft:"leicht links",Left:"links",SharpLeft:"scharf links",Uturn:"Wenden"},instructions:{Head:["Richtung {dir}"," auf {road}"],Continue:["Geradeaus Richtung {dir}"," auf {road}"],SlightRight:["Leicht rechts abbiegen"," auf {road}"],Right:["Rechts abbiegen"," auf {road}"],SharpRight:["Scharf rechts abbiegen"," auf {road}"],TurnAround:["Wenden"],SharpLeft:["Scharf links abbiegen"," auf {road}"],Left:["Links abbiegen"," auf {road}"],SlightLeft:["Leicht links abbiegen"," auf {road}"],WaypointReached:["Zwischenhalt erreicht"],Roundabout:["Nehmen Sie die {exitStr} Ausfahrt im Kreisverkehr"," auf {road}"],DestinationReached:["Sie haben ihr Ziel erreicht"],Fork:["An der Kreuzung {modifier}"," auf {road}"],Merge:["Fahren Sie {modifier} weiter"," auf {road}"],OnRamp:["Fahren Sie {modifier} auf die Auffahrt"," auf {road}"],OffRamp:["Nehmen Sie die Ausfahrt {modifier}"," auf {road}"],EndOfRoad:["Fahren Sie {modifier} am Ende der Straße"," auf {road}"],Onto:"auf {road}"},formatOrder:function(n){return n+"."},ui:{startPlaceholder:"Start",viaPlaceholder:"Via {viaNumber}",endPlaceholder:"Ziel"}},sv:{directions:{N:"norr",NE:"nordost",E:"öst",SE:"sydost",S:"syd",SW:"sydväst",W:"väst",NW:"nordväst",SlightRight:"svagt höger",Right:"höger",SharpRight:"skarpt höger",SlightLeft:"svagt vänster",Left:"vänster",SharpLeft:"skarpt vänster",Uturn:"Vänd"},instructions:{Head:["Åk åt {dir}"," till {road}"],Continue:["Fortsätt {dir}"],SlightRight:["Svagt höger"," till {road}"],Right:["Sväng höger"," till {road}"],SharpRight:["Skarpt höger"," till {road}"],TurnAround:["Vänd"],SharpLeft:["Skarpt vänster"," till {road}"],Left:["Sväng vänster"," till {road}"],SlightLeft:["Svagt vänster"," till {road}"],WaypointReached:["Viapunkt nådd"],Roundabout:["Tag {exitStr} avfarten i rondellen"," till {road}"],DestinationReached:["Framme vid resans mål"],Fork:["Tag av {modifier}"," till {road}"],Merge:["Anslut {modifier} "," till {road}"],OnRamp:["Tag påfarten {modifier}"," till {road}"],OffRamp:["Tag avfarten {modifier}"," till {road}"],EndOfRoad:["Sväng {modifier} vid vägens slut"," till {road}"],Onto:"till {road}"},formatOrder:function(n){return["första","andra","tredje","fjärde","femte","sjätte","sjunde","åttonde","nionde","tionde"][n-1]},ui:{startPlaceholder:"Från",viaPlaceholder:"Via {viaNumber}",endPlaceholder:"Till"}},es:spanish,sp:spanish,nl:{directions:{N:"noordelijke",NE:"noordoostelijke",E:"oostelijke",SE:"zuidoostelijke",S:"zuidelijke",SW:"zuidewestelijke",W:"westelijke",NW:"noordwestelijke"},instructions:{Head:["Vertrek in {dir} richting"," de {road} op"],Continue:["Ga in {dir} richting"," de {road} op"],SlightRight:["Volg de weg naar rechts"," de {road} op"],Right:["Ga rechtsaf"," de {road} op"],SharpRight:["Ga scherpe bocht naar rechts"," de {road} op"],TurnAround:["Keer om"],SharpLeft:["Ga scherpe bocht naar links"," de {road} op"],Left:["Ga linksaf"," de {road} op"],SlightLeft:["Volg de weg naar links"," de {road} op"],WaypointReached:["Aangekomen bij tussenpunt"],Roundabout:["Neem de {exitStr} afslag op de rotonde"," de {road} op"],DestinationReached:["Aangekomen op eindpunt"]},formatOrder:function(n){if(n===1||n>=20){return n+"ste"}else{return n+"de"}},ui:{startPlaceholder:"Vertrekpunt",viaPlaceholder:"Via {viaNumber}",endPlaceholder:"Bestemming"}},fr:{directions:{N:"nord",NE:"nord-est",E:"est",SE:"sud-est",S:"sud",SW:"sud-ouest",W:"ouest",NW:"nord-ouest"},instructions:{Head:["Tout droit au {dir}"," sur {road}"],Continue:["Continuer au {dir}"," sur {road}"],SlightRight:["Légèrement à droite"," sur {road}"],Right:["A droite"," sur {road}"],SharpRight:["Complètement à droite"," sur {road}"],TurnAround:["Faire demi-tour"],SharpLeft:["Complètement à gauche"," sur {road}"],Left:["A gauche"," sur {road}"],SlightLeft:["Légèrement à gauche"," sur {road}"],WaypointReached:["Point d'étape atteint"],Roundabout:["Au rond-point, prenez la {exitStr} sortie"," sur {road}"],DestinationReached:["Destination atteinte"]},formatOrder:function(n){return n+"º"},ui:{startPlaceholder:"Départ",viaPlaceholder:"Intermédiaire {viaNumber}",endPlaceholder:"Arrivée"}},it:{directions:{N:"nord",NE:"nord-est",E:"est",SE:"sud-est",S:"sud",SW:"sud-ovest",W:"ovest",NW:"nord-ovest"},instructions:{Head:["Dritto verso {dir}"," su {road}"],Continue:["Continuare verso {dir}"," su {road}"],SlightRight:["Mantenere la destra"," su {road}"],Right:["A destra"," su {road}"],SharpRight:["Strettamente a destra"," su {road}"],TurnAround:["Fare inversione di marcia"],SharpLeft:["Strettamente a sinistra"," su {road}"],Left:["A sinistra"," sur {road}"],SlightLeft:["Mantenere la sinistra"," su {road}"],WaypointReached:["Punto di passaggio raggiunto"],Roundabout:["Alla rotonda, prendere la {exitStr} uscita"],DestinationReached:["Destinazione raggiunta"]},formatOrder:function(n){return n+"º"},ui:{startPlaceholder:"Partenza",viaPlaceholder:"Intermedia {viaNumber}",endPlaceholder:"Destinazione"}},pt:{directions:{N:"norte",NE:"nordeste",E:"leste",SE:"sudeste",S:"sul",SW:"sudoeste",W:"oeste",NW:"noroeste",SlightRight:"curva ligeira a direita",Right:"direita",SharpRight:"curva fechada a direita",SlightLeft:"ligeira a esquerda",Left:"esquerda",SharpLeft:"curva fechada a esquerda",Uturn:"Meia volta"},instructions:{Head:["Siga {dir}"," na {road}"],Continue:["Continue {dir}"," na {road}"],SlightRight:["Curva ligeira a direita"," na {road}"],Right:["Curva a direita"," na {road}"],SharpRight:["Curva fechada a direita"," na {road}"],TurnAround:["Retorne"],SharpLeft:["Curva fechada a esquerda"," na {road}"],Left:["Curva a esquerda"," na {road}"],SlightLeft:["Curva ligueira a esquerda"," na {road}"],WaypointReached:["Ponto de interesse atingido"],Roundabout:["Pegue a {exitStr} saída na rotatória"," na {road}"],DestinationReached:["Destino atingido"],Fork:["Na encruzilhada, vire a {modifier}"," na {road}"],Merge:["Entre à {modifier}"," na {road}"],OnRamp:["Vire {modifier} na rampa"," na {road}"],OffRamp:["Entre na rampa na {modifier}"," na {road}"],EndOfRoad:["Vire {modifier} no fim da rua"," na {road}"],Onto:"na {road}"},formatOrder:function(n){return n+"º"},ui:{startPlaceholder:"Origem",viaPlaceholder:"Intermédio {viaNumber}",endPlaceholder:"Destino"}},sk:{directions:{N:"sever",NE:"serverovýchod",E:"východ",SE:"juhovýchod",S:"juh",SW:"juhozápad",W:"západ",NW:"serverozápad"},instructions:{Head:["Mierte na {dir}"," na {road}"],Continue:["Pokračujte na {dir}"," na {road}"],SlightRight:["Mierne doprava"," na {road}"],Right:["Doprava"," na {road}"],SharpRight:["Prudko doprava"," na {road}"],TurnAround:["Otočte sa"],SharpLeft:["Prudko doľava"," na {road}"],Left:["Doľava"," na {road}"],SlightLeft:["Mierne doľava"," na {road}"],WaypointReached:["Ste v prejazdovom bode."],Roundabout:["Odbočte na {exitStr} výjazde"," na {road}"],DestinationReached:["Prišli ste do cieľa."]},formatOrder:function(n){var i=n%10-1,suffix=[".",".","."];return suffix[i]?n+suffix[i]:n+"."},ui:{startPlaceholder:"Začiatok",viaPlaceholder:"Cez {viaNumber}",endPlaceholder:"Koniec"}},el:{directions:{N:"βόρεια",NE:"βορειοανατολικά",E:"ανατολικά",SE:"νοτιοανατολικά",S:"νότια",SW:"νοτιοδυτικά",W:"δυτικά",NW:"βορειοδυτικά"},instructions:{Head:["Κατευθυνθείτε {dir}"," στην {road}"],Continue:["Συνεχίστε {dir}"," στην {road}"],SlightRight:["Ελαφρώς δεξιά"," στην {road}"],Right:["Δεξιά"," στην {road}"],SharpRight:["Απότομη δεξιά στροφή"," στην {road}"],TurnAround:["Κάντε αναστροφή"],SharpLeft:["Απότομη αριστερή στροφή"," στην {road}"],Left:["Αριστερά"," στην {road}"],SlightLeft:["Ελαφρώς αριστερά"," στην {road}"],WaypointReached:["Φτάσατε στο σημείο αναφοράς"],Roundabout:["Ακολουθήστε την {exitStr} έξοδο στο κυκλικό κόμβο"," στην {road}"],DestinationReached:["Φτάσατε στον προορισμό σας"]},formatOrder:function(n){return n+"º"},ui:{startPlaceholder:"Αφετηρία",viaPlaceholder:"μέσω {viaNumber}",endPlaceholder:"Προορισμός"}},ca:{
directions:{N:"nord",NE:"nord-est",E:"est",SE:"sud-est",S:"sud",SW:"sud-oest",W:"oest",NW:"nord-oest",SlightRight:"lleu gir a la dreta",Right:"dreta",SharpRight:"gir pronunciat a la dreta",SlightLeft:"gir pronunciat a l'esquerra",Left:"esquerra",SharpLeft:"lleu gir a l'esquerra",Uturn:"mitja volta"},instructions:{Head:["Recte {dir}"," sobre {road}"],Continue:["Continuar {dir}"],TurnAround:["Donar la volta"],WaypointReached:["Ha arribat a un punt del camí"],Roundabout:["Agafar {exitStr} sortida a la rotonda"," a {road}"],DestinationReached:["Arribada al destí"],Fork:["A la cruïlla gira a la {modifier}"," cap a {road}"],Merge:["Incorpora't {modifier}"," a {road}"],OnRamp:["Gira {modifier} a la sortida"," cap a {road}"],OffRamp:["Pren la sortida {modifier}"," cap a {road}"],EndOfRoad:["Gira {modifier} al final de la carretera"," cap a {road}"],Onto:"cap a {road}"},formatOrder:function(n){return n+"º"},ui:{startPlaceholder:"Origen",viaPlaceholder:"Via {viaNumber}",endPlaceholder:"Destí"},units:{meters:"m",kilometers:"km",yards:"yd",miles:"mi",hours:"h",minutes:"min",seconds:"s"}},ru:{directions:{N:"север",NE:"северовосток",E:"восток",SE:"юговосток",S:"юг",SW:"югозапад",W:"запад",NW:"северозапад",SlightRight:"плавно направо",Right:"направо",SharpRight:"резко направо",SlightLeft:"плавно налево",Left:"налево",SharpLeft:"резко налево",Uturn:"развернуться"},instructions:{Head:["Начать движение на {dir}"," по {road}"],Continue:["Продолжать движение на {dir}"," по {road}"],SlightRight:["Плавный поворот направо"," на {road}"],Right:["Направо"," на {road}"],SharpRight:["Резкий поворот направо"," на {road}"],TurnAround:["Развернуться"],SharpLeft:["Резкий поворот налево"," на {road}"],Left:["Поворот налево"," на {road}"],SlightLeft:["Плавный поворот налево"," на {road}"],WaypointReached:["Точка достигнута"],Roundabout:["{exitStr} съезд с кольца"," на {road}"],DestinationReached:["Окончание маршрута"],Fork:["На развилке поверните {modifier}"," на {road}"],Merge:["Перестройтесь {modifier}"," на {road}"],OnRamp:["Поверните {modifier} на съезд"," на {road}"],OffRamp:["Съезжайте на {modifier}"," на {road}"],EndOfRoad:["Поверните {modifier} в конце дороги"," на {road}"],Onto:"на {road}"},formatOrder:function(n){return n+"-й"},ui:{startPlaceholder:"Начало",viaPlaceholder:"Через {viaNumber}",endPlaceholder:"Конец"},units:{meters:"м",kilometers:"км",yards:"ярд",miles:"ми",hours:"ч",minutes:"м",seconds:"с"}},pl:{directions:{N:"północ",NE:"północny wschód",E:"wschód",SE:"południowy wschód",S:"południe",SW:"południowy zachód",W:"zachód",NW:"północny zachód",SlightRight:"lekko w prawo",Right:"w prawo",SharpRight:"ostro w prawo",SlightLeft:"lekko w lewo",Left:"w lewo",SharpLeft:"ostro w lewo",Uturn:"zawróć"},instructions:{Head:["Kieruj się na {dir}"," na {road}"],Continue:["Jedź dalej przez {dir}"],TurnAround:["Zawróć"],WaypointReached:["Punkt pośredni"],Roundabout:["Wyjedź {exitStr} zjazdem na rondzie"," na {road}"],DestinationReached:["Dojechano do miejsca docelowego"],Fork:["Na rozwidleniu {modifier}"," na {road}"],Merge:["Zjedź {modifier}"," na {road}"],OnRamp:["Wjazd {modifier}"," na {road}"],OffRamp:["Zjazd {modifier}"," na {road}"],EndOfRoad:["Skręć {modifier} na końcu drogi"," na {road}"],Onto:"na {road}"},formatOrder:function(n){return n+"."},ui:{startPlaceholder:"Początek",viaPlaceholder:"Przez {viaNumber}",endPlaceholder:"Koniec"},units:{meters:"m",kilometers:"km",yards:"yd",miles:"mi",hours:"godz",minutes:"min",seconds:"s"}}})})()},{}],58:[function(_dereq_,module,exports){(function(global){(function(){"use strict";var L=typeof window!=="undefined"?window["L"]:typeof global!=="undefined"?global["L"]:null;var OSRMv1=_dereq_("./osrm-v1");module.exports=OSRMv1.extend({options:{serviceUrl:"https://api.mapbox.com/directions/v5",profile:"mapbox/driving",useHints:false},initialize:function(accessToken,options){L.Routing.OSRMv1.prototype.initialize.call(this,options);this.options.requestParameters=this.options.requestParameters||{};this.options.requestParameters.access_token=accessToken}})})()}).call(this,typeof global!=="undefined"?global:typeof self!=="undefined"?self:typeof window!=="undefined"?window:{})},{"./osrm-v1":59}],59:[function(_dereq_,module,exports){(function(global){(function(){"use strict";var L=typeof window!=="undefined"?window["L"]:typeof global!=="undefined"?global["L"]:null,corslite=_dereq_("@mapbox/corslite"),polyline=_dereq_("@mapbox/polyline"),osrmTextInstructions=_dereq_("osrm-text-instructions")("v5");var Waypoint=_dereq_("./waypoint");module.exports=L.Class.extend({options:{serviceUrl:"https://router.project-osrm.org/route/v1",profile:"driving",timeout:30*1e3,routingOptions:{alternatives:true,steps:true},polylinePrecision:5,useHints:true,suppressDemoServerWarning:false,language:"en"},initialize:function(options){L.Util.setOptions(this,options);this._hints={locations:{}};if(!this.options.suppressDemoServerWarning&&this.options.serviceUrl.indexOf("//router.project-osrm.org")>=0){console.warn("You are using OSRM's demo server. "+"Please note that it is **NOT SUITABLE FOR PRODUCTION USE**.\n"+"Refer to the demo server's usage policy: "+"https://github.com/Project-OSRM/osrm-backend/wiki/Api-usage-policy\n\n"+"To change, set the serviceUrl option.\n\n"+"Please do not report issues with this server to neither "+"Leaflet Routing Machine or OSRM - it's for\n"+"demo only, and will sometimes not be available, or work in "+"unexpected ways.\n\n"+"Please set up your own OSRM server, or use a paid service "+"provider for production.")}},route:function(waypoints,callback,context,options){var timedOut=false,wps=[],url,timer,wp,i,xhr;options=L.extend({},this.options.routingOptions,options);url=this.buildRouteUrl(waypoints,options);if(this.options.requestParameters){url+=L.Util.getParamString(this.options.requestParameters,url)}timer=setTimeout(function(){timedOut=true;callback.call(context||callback,{status:-1,message:"OSRM request timed out."})},this.options.timeout);for(i=0;i<waypoints.length;i++){wp=waypoints[i];wps.push(new Waypoint(wp.latLng,wp.name,wp.options))}return xhr=corslite(url,L.bind(function(err,resp){var data,error={};clearTimeout(timer);if(!timedOut){if(!err){try{data=JSON.parse(resp.responseText);try{return this._routeDone(data,wps,options,callback,context)}catch(ex){error.status=-3;error.message=ex.toString()}}catch(ex){error.status=-2;error.message="Error parsing OSRM response: "+ex.toString()}}else{error.message="HTTP request failed: "+err.type+(err.target&&err.target.status?" HTTP "+err.target.status+": "+err.target.statusText:"");error.url=url;error.status=-1;error.target=err}callback.call(context||callback,error)}else{xhr.abort()}},this))},requiresMoreDetail:function(route,zoom,bounds){if(!route.properties.isSimplified){return false}var waypoints=route.inputWaypoints,i;for(i=0;i<waypoints.length;++i){if(!bounds.contains(waypoints[i].latLng)){return true}}return false},_routeDone:function(response,inputWaypoints,options,callback,context){var alts=[],actualWaypoints,i,route;context=context||callback;if(response.code!=="Ok"){callback.call(context,{status:response.code});return}actualWaypoints=this._toWaypoints(inputWaypoints,response.waypoints);for(i=0;i<response.routes.length;i++){route=this._convertRoute(response.routes[i]);route.inputWaypoints=inputWaypoints;route.waypoints=actualWaypoints;route.properties={isSimplified:!options||!options.geometryOnly||options.simplifyGeometry};alts.push(route)}this._saveHintData(response.waypoints,inputWaypoints);callback.call(context,null,alts)},_convertRoute:function(responseRoute){var result={name:"",coordinates:[],instructions:[],summary:{totalDistance:responseRoute.distance,totalTime:responseRoute.duration}},legNames=[],waypointIndices=[],index=0,legCount=responseRoute.legs.length,hasSteps=responseRoute.legs[0].steps.length>0,i,j,leg,step,geometry,type,modifier,text,stepToText;if(this.options.stepToText){stepToText=this.options.stepToText}else{stepToText=L.bind(osrmTextInstructions.compile,osrmTextInstructions,this.options.language)}for(i=0;i<legCount;i++){leg=responseRoute.legs[i];legNames.push(leg.summary&&leg.summary.charAt(0).toUpperCase()+leg.summary.substring(1));for(j=0;j<leg.steps.length;j++){step=leg.steps[j];geometry=this._decodePolyline(step.geometry);result.coordinates.push.apply(result.coordinates,geometry);type=this._maneuverToInstructionType(step.maneuver,i===legCount-1);modifier=this._maneuverToModifier(step.maneuver);text=stepToText(step,{legCount:legCount,legIndex:i});if(type){if(i==0&&step.maneuver.type=="depart"||step.maneuver.type=="arrive"){waypointIndices.push(index)}result.instructions.push({type:type,distance:step.distance,time:step.duration,road:step.name,direction:this._bearingToDirection(step.maneuver.bearing_after),exit:step.maneuver.exit,index:index,mode:step.mode,modifier:modifier,text:text})}index+=geometry.length}}result.name=legNames.join(", ");if(!hasSteps){result.coordinates=this._decodePolyline(responseRoute.geometry)}else{result.waypointIndices=waypointIndices}return result},_bearingToDirection:function(bearing){var oct=Math.round(bearing/45)%8;return["N","NE","E","SE","S","SW","W","NW"][oct]},_maneuverToInstructionType:function(maneuver,lastLeg){switch(maneuver.type){case"new name":return"Continue";case"depart":return"Head";case"arrive":return lastLeg?"DestinationReached":"WaypointReached";case"roundabout":case"rotary":return"Roundabout";case"merge":case"fork":case"on ramp":case"off ramp":case"end of road":return this._camelCase(maneuver.type);default:return this._camelCase(maneuver.modifier)}},_maneuverToModifier:function(maneuver){var modifier=maneuver.modifier;switch(maneuver.type){case"merge":case"fork":case"on ramp":case"off ramp":case"end of road":modifier=this._leftOrRight(modifier)}return modifier&&this._camelCase(modifier)},_camelCase:function(s){var words=s.split(" "),result="";for(var i=0,l=words.length;i<l;i++){result+=words[i].charAt(0).toUpperCase()+words[i].substring(1)}return result},_leftOrRight:function(d){return d.indexOf("left")>=0?"Left":"Right"},_decodePolyline:function(routeGeometry){var cs=polyline.decode(routeGeometry,this.options.polylinePrecision),result=new Array(cs.length),i;for(i=cs.length-1;i>=0;i--){result[i]=L.latLng(cs[i])}return result},_toWaypoints:function(inputWaypoints,vias){var wps=[],i,viaLoc;for(i=0;i<vias.length;i++){viaLoc=vias[i].location;wps.push(new Waypoint(L.latLng(viaLoc[1],viaLoc[0]),inputWaypoints[i].name,inputWaypoints[i].options))}return wps},buildRouteUrl:function(waypoints,options){var locs=[],hints=[],wp,latLng,computeInstructions,computeAlternative=true;for(var i=0;i<waypoints.length;i++){wp=waypoints[i];latLng=wp.latLng;locs.push(latLng.lng+","+latLng.lat);hints.push(this._hints.locations[this._locationKey(latLng)]||"")}computeInstructions=true;return this.options.serviceUrl+"/"+this.options.profile+"/"+locs.join(";")+"?"+(options.geometryOnly?options.simplifyGeometry?"":"overview=full":"overview=false")+"&alternatives="+computeAlternative.toString()+"&steps="+computeInstructions.toString()+(this.options.useHints?"&hints="+hints.join(";"):"")+(options.allowUTurns?"&continue_straight="+!options.allowUTurns:"")},_locationKey:function(location){return location.lat+","+location.lng},_saveHintData:function(actualWaypoints,waypoints){var loc;this._hints={locations:{}};for(var i=actualWaypoints.length-1;i>=0;i--){loc=waypoints[i].latLng;this._hints.locations[this._locationKey(loc)]=actualWaypoints[i].hint}}})})()}).call(this,typeof global!=="undefined"?global:typeof self!=="undefined"?self:typeof window!=="undefined"?window:{})},{"./waypoint":61,"@mapbox/corslite":1,"@mapbox/polyline":2,"osrm-text-instructions":3}],60:[function(_dereq_,module,exports){(function(global){(function(){"use strict";var L=typeof window!=="undefined"?window["L"]:typeof global!=="undefined"?global["L"]:null;var GeocoderElement=_dereq_("./geocoder-element");var Waypoint=_dereq_("./waypoint");module.exports=(L.Layer||L.Class).extend({includes:typeof L.Evented!=="undefined"&&L.Evented.prototype||L.Mixin.Events,options:{dragStyles:[{color:"black",opacity:.15,weight:9},{color:"white",opacity:.8,weight:6},{color:"red",opacity:1,weight:2,dashArray:"7,12"}],draggableWaypoints:true,routeWhileDragging:false,addWaypoints:true,reverseWaypoints:false,addButtonClassName:"",language:"en",createGeocoderElement:function(wp,i,nWps,plan){return new GeocoderElement(wp,i,nWps,plan)},createMarker:function(i,wp){var options={draggable:this.draggableWaypoints},marker=L.marker(wp.latLng,options);return marker},geocodersClassName:""},initialize:function(waypoints,options){L.Util.setOptions(this,options);this._waypoints=[];this.setWaypoints(waypoints)},isReady:function(){var i;for(i=0;i<this._waypoints.length;i++){if(!this._waypoints[i].latLng){return false}}return true},getWaypoints:function(){var i,wps=[];for(i=0;i<this._waypoints.length;i++){wps.push(this._waypoints[i])}return wps},setWaypoints:function(waypoints){var args=[0,this._waypoints.length].concat(waypoints);this.spliceWaypoints.apply(this,args);return this},spliceWaypoints:function(){var args=[arguments[0],arguments[1]],i;for(i=2;i<arguments.length;i++){args.push(arguments[i]&&arguments[i].hasOwnProperty("latLng")?arguments[i]:new Waypoint(arguments[i]))}[].splice.apply(this._waypoints,args);while(this._waypoints.length<2){this.spliceWaypoints(this._waypoints.length,0,null)}this._updateMarkers();this._fireChanged.apply(this,args)},onAdd:function(map){this._map=map;this._updateMarkers()},onRemove:function(){var i;this._removeMarkers();if(this._newWp){for(i=0;i<this._newWp.lines.length;i++){this._map.removeLayer(this._newWp.lines[i])}}delete this._map},createGeocoders:function(){var container=L.DomUtil.create("div","leaflet-routing-geocoders "+this.options.geocodersClassName),waypoints=this._waypoints,addWpBtn,reverseBtn;this._geocoderContainer=container;this._geocoderElems=[];if(this.options.addWaypoints){addWpBtn=L.DomUtil.create("button","leaflet-routing-add-waypoint "+this.options.addButtonClassName,container);addWpBtn.setAttribute("type","button");L.DomEvent.addListener(addWpBtn,"click",function(){this.spliceWaypoints(waypoints.length,0,null)},this)}if(this.options.reverseWaypoints){reverseBtn=L.DomUtil.create("button","leaflet-routing-reverse-waypoints",container);reverseBtn.setAttribute("type","button");L.DomEvent.addListener(reverseBtn,"click",function(){this._waypoints.reverse();this.setWaypoints(this._waypoints)},this)}this._updateGeocoders();this.on("waypointsspliced",this._updateGeocoders);return container},_createGeocoder:function(i){var geocoder=this.options.createGeocoderElement(this._waypoints[i],i,this._waypoints.length,this.options);geocoder.on("delete",function(){if(i>0||this._waypoints.length>2){this.spliceWaypoints(i,1)}else{this.spliceWaypoints(i,1,new Waypoint)}},this).on("geocoded",function(e){this._updateMarkers();this._fireChanged();this._focusGeocoder(i+1);this.fire("waypointgeocoded",{waypointIndex:i,waypoint:e.waypoint})},this).on("reversegeocoded",function(e){this.fire("waypointgeocoded",{waypointIndex:i,waypoint:e.waypoint})},this);return geocoder},_updateGeocoders:function(){var elems=[],i,geocoderElem;for(i=0;i<this._geocoderElems.length;i++){this._geocoderContainer.removeChild(this._geocoderElems[i].getContainer())}for(i=this._waypoints.length-1;i>=0;i--){geocoderElem=this._createGeocoder(i);this._geocoderContainer.insertBefore(geocoderElem.getContainer(),this._geocoderContainer.firstChild);elems.push(geocoderElem)}this._geocoderElems=elems.reverse()},_removeMarkers:function(){var i;if(this._markers){for(i=0;i<this._markers.length;i++){if(this._markers[i]){this._map.removeLayer(this._markers[i])}}}this._markers=[]},_updateMarkers:function(){var i,m;if(!this._map){return}this._removeMarkers();for(i=0;i<this._waypoints.length;i++){if(this._waypoints[i].latLng){m=this.options.createMarker(i,this._waypoints[i],this._waypoints.length);if(m){m.addTo(this._map);if(this.options.draggableWaypoints){this._hookWaypointEvents(m,i)}}}else{m=null}this._markers.push(m)}},_fireChanged:function(){this.fire("waypointschanged",{waypoints:this.getWaypoints()});if(arguments.length>=2){this.fire("waypointsspliced",{index:Array.prototype.shift.call(arguments),nRemoved:Array.prototype.shift.call(arguments),added:arguments})}},_hookWaypointEvents:function(m,i,trackMouseMove){var eventLatLng=function(e){return trackMouseMove?e.latlng:e.target.getLatLng()},dragStart=L.bind(function(e){this.fire("waypointdragstart",{index:i,latlng:eventLatLng(e)})},this),drag=L.bind(function(e){this._waypoints[i].latLng=eventLatLng(e);this.fire("waypointdrag",{index:i,latlng:eventLatLng(e)})},this),dragEnd=L.bind(function(e){this._waypoints[i].latLng=eventLatLng(e);this._waypoints[i].name="";if(this._geocoderElems){this._geocoderElems[i].update(true)}this.fire("waypointdragend",{index:i,latlng:eventLatLng(e)});this._fireChanged()},this),mouseMove,mouseUp;if(trackMouseMove){mouseMove=L.bind(function(e){this._markers[i].setLatLng(e.latlng);drag(e)},this);mouseUp=L.bind(function(e){this._map.dragging.enable();this._map.off("mouseup",mouseUp);this._map.off("mousemove",mouseMove);dragEnd(e)},this);this._map.dragging.disable();this._map.on("mousemove",mouseMove);this._map.on("mouseup",mouseUp);dragStart({latlng:this._waypoints[i].latLng})}else{m.on("dragstart",dragStart);m.on("drag",drag);m.on("dragend",dragEnd)}},dragNewWaypoint:function(e){var newWpIndex=e.afterIndex+1;if(this.options.routeWhileDragging){this.spliceWaypoints(newWpIndex,0,e.latlng);this._hookWaypointEvents(this._markers[newWpIndex],newWpIndex,true)}else{this._dragNewWaypoint(newWpIndex,e.latlng)}},_dragNewWaypoint:function(newWpIndex,initialLatLng){var wp=new Waypoint(initialLatLng),prevWp=this._waypoints[newWpIndex-1],nextWp=this._waypoints[newWpIndex],marker=this.options.createMarker(newWpIndex,wp,this._waypoints.length+1),lines=[],draggingEnabled=this._map.dragging.enabled(),mouseMove=L.bind(function(e){var i,latLngs;if(marker){marker.setLatLng(e.latlng)}for(i=0;i<lines.length;i++){latLngs=lines[i].getLatLngs();latLngs.splice(1,1,e.latlng);lines[i].setLatLngs(latLngs)}L.DomEvent.stop(e)},this),mouseUp=L.bind(function(e){var i;if(marker){this._map.removeLayer(marker)}for(i=0;i<lines.length;i++){this._map.removeLayer(lines[i])}this._map.off("mousemove",mouseMove);this._map.off("mouseup",mouseUp);this.spliceWaypoints(newWpIndex,0,e.latlng);if(draggingEnabled){this._map.dragging.enable()}L.DomEvent.stop(e)},this),i;if(marker){marker.addTo(this._map)}for(i=0;i<this.options.dragStyles.length;i++){lines.push(L.polyline([prevWp.latLng,initialLatLng,nextWp.latLng],this.options.dragStyles[i]).addTo(this._map))}if(draggingEnabled){this._map.dragging.disable()}this._map.on("mousemove",mouseMove);this._map.on("mouseup",mouseUp)},_focusGeocoder:function(i){if(this._geocoderElems[i]){this._geocoderElems[i].focus()}else{document.activeElement.blur()}}})})()}).call(this,typeof global!=="undefined"?global:typeof self!=="undefined"?self:typeof window!=="undefined"?window:{})},{"./geocoder-element":52,"./waypoint":61}],61:[function(_dereq_,module,exports){(function(global){(function(){"use strict";var L=typeof window!=="undefined"?window["L"]:typeof global!=="undefined"?global["L"]:null;module.exports=L.Class.extend({options:{allowUTurn:false},initialize:function(latLng,name,options){L.Util.setOptions(this,options);this.latLng=L.latLng(latLng);this.name=name}})})()}).call(this,typeof global!=="undefined"?global:typeof self!=="undefined"?self:typeof window!=="undefined"?window:{})},{}]},{},[53]);

(function (L) {
  if (!L) return;

  /**
   * ----------------------------------------
   * MARKER EXTENSIONS
   * ----------------------------------------
   */

  // Save original setIcon method for safe override
  if (!L.Marker.prototype._setIconOriginal) {
    L.Marker.prototype._setIconOriginal = L.Marker.prototype.setIcon;
  }

  // Extend L.Marker with Google Maps–like methods
  L.Marker.include({

    /**
     * Mimics Google Maps setMap() to add or remove marker from map.
     */
    setMap: function (map) {
      if (map) {
        this._mapRef = map;
        this.addTo(map);
        this._visible = true;
      } else if (this._mapRef) {
        this._mapRef.removeLayer(this);
        this._visible = false;
      }
    },

    /**
     * Show or hide marker on map.
     */
    setVisible: function (show) {
      if (!this._mapRef && this._map) {
        this._mapRef = this._map;
      }

      if (show) {
        this._mapRef && this.addTo(this._mapRef);
        this._visible = true;
      } else if (this._mapRef) {
        this._mapRef.removeLayer(this);
        this._visible = false;
      }
    },

    /**
     * Return true if marker is currently visible.
     */
    getVisible: function () {
      return !!this._visible;
    },

    /**
     * Returns current position of the marker.
     */
    getPosition: function () {
      return this.getLatLng();
    },

    /**
     * Sets marker position to the given latlng.
     */
    setPosition: function (latlng) {
      return this.setLatLng(latlng);
    },

    /**
     * Binds a reference to the map, used internally for visibility.
     */
    bindMap: function (map) {
      this._mapRef = map;
      this._visible = true;
    },

    /**
     * Apply animation classes (limited to CSS classes like bounce).
     */
    setAnimation: function (animationType) {
      const el = this._icon;
      if (!el) return;

      el.classList.remove('leaflet-bounce-animation');

      if (animationType === 'bounce') {
        el.classList.add('leaflet-bounce-animation');
      }
    },

    /**
     * Safe override of setIcon to accept URL strings (like Google).
     */
    setIcon: function (icon) {
      // If icon is a string, convert to default L.icon
      if (typeof icon === 'string') {
        icon = L.icon({
          iconUrl: icon,
          iconSize: [32, 32],
          iconAnchor: [16, 32],
          popupAnchor: [0, -55]
        });
      }
  
      // If icon is an object with Google-style format
      if (icon && icon.url) {
        const size = icon.scaledSize || [32, 32];
        icon = L.icon({
          iconUrl: icon.url,
          iconSize: size,
          iconAnchor: [size[0] / 2, size[1]],
          popupAnchor: [0, -Math.max(40, size[1])]
        });
      }
  
      return this._setIconOriginal(icon);
    }
  });

  /**
   * ----------------------------------------
   * POPUP EXTENSIONS
   * ----------------------------------------
   */
  L.Popup.include({

    /**
     * Opens popup at marker position on given map (Google-style).
     */
    open: function (map, marker) {
      if (!map || !marker) return this;

      const latlng = marker.getLatLng();
      this.setLatLng(latlng);

      if (!this._map) {
        map.openPopup(this);
      }

      return this;
    },

    /**
     * Closes popup from its parent map.
     */
    close: function () {
      if (this._map) {
        this._map.closePopup(this);
      }
    }
  });

  /**
   * ----------------------------------------
   * POLYLINE EXTENSIONS
   * ----------------------------------------
   */
  if (L.Polyline) {
    L.Polyline.include({

      /**
       * Mimics Google Maps Polyline setMap() method.
       */
      setMap: function (map) {
        if (map) {
          if (!map.hasLayer(this)) {
            this.addTo(map);
          }
        } else if (this._map) {
          this._map.removeLayer(this);
        }
      }
    });
  }

  /**
   * ----------------------------------------
   * MAP EXTENSIONS
   * ----------------------------------------
   */
  L.Map.include({

    /**
     * Placeholder for setOptions() (noop for Leaflet).
     */
    setOptions: function (options) {
      console.warn('setOptions() called in Leaflet. This method does nothing.');
      return this;
    },

    /**
     * Mimics Google Maps setCenter(), accepting [lat, lng] or LatLng object.
     */
    setCenter: function (position) {
      if (!position) return;

      const latlng = Array.isArray(position)
        ? L.latLng(position[0], position[1])
        : position;

      this.panTo(latlng);
    }
  });

})(window.L);

/**
 * WpgmpBaseMaps class - provides shared map functionality.
 */
(function ($, window, document) {

class WpgmpBaseMaps {
  constructor(element, map_data = {},places = []) {
    var options;
    this.element = element;
    this.map_data = $.extend({}, {}, map_data);
    options = this.map_data.map_options;
    this.settings = $.extend({
        "min_zoom": "0",
        "max_zoom": "19",
        "zoom": "5",
        "map_type_id": "ROADMAP",
        "scroll_wheel": true,
        "map_visual_refresh": false,
        "full_screen_control": false,
        "full_screen_control_position": "BOTTOM_RIGHT",
        "zoom_control": true,
        "zoom_control_style": "SMALL",
        "zoom_control_position": "TOP_LEFT",
        "camera_control_position" : "BOTTOM_RIGHT",
        "map_type_control": true,
        "map_type_control_style": "HORIZONTAL_BAR",
        "map_type_control_position": "RIGHT_TOP",
        "scale_control": true,
        "street_view_control": true,
        "street_view_control_position": "TOP_LEFT",
        "overview_map_control": true,
        "camera_control": true,
        "center_lat": "40.6153983",
        "center_lng": "-74.2535216",
        "draggable": true,
        "gesture": "auto",
    }, {}, options);
    this.container = $("div[rel='" + $(this.element).attr("id") + "']");
    $(document).find(".wpgmp_map_container_placeholder").remove();
    $(document).find(".wpgmp_hide_map_container").removeClass("wpgmp_hide_map_container");

    
    this.drawingmanager = {};
    this.places = [];
    this.show_places = [];
    this.places_for_category_tabs = [];
    this.categories = {};
    this.tabs = [];
    this.all_shapes = [];
    this.wpgmp_polylines = [];
    this.wpgmp_polygons = [];
    this.wpgmp_circles = [];
    this.wpgmp_shape_events = [];
    this.wpgmp_rectangles = [];
    this.per_page_value = 0;
    this.last_remove_cat_id = '';
    this.last_selected_cat_id = '';
    this.last_category_chkbox_action = '';
    this.current_amenities = [];
    this.route_directions = [];
    this.search_area = '';
    this.filter_position = '';
    this.filter_content = '';
    this.markerClusterer = null;
    this.url_filters = [];
    this.wpgmp_search_form = '';
    this.enable_search_term = false;
    this.search_term = '';
    this.filters = {};
    this.addonInfo = [];
  }

  // -----------------------------
  // 🌀 Lifecycle
  // -----------------------------
  init() { 

    var map_obj = this;

  }
  map_loaded() { }
  /**
 * Resizes and re-centers the map
 * Preserves the zoom level and current center position
 */
resize_map() {
  const map_obj = this;
  const isGoogle = map_obj.isMapProvider("google");
  const map = map_obj.map;

  if (!map) return;

  const center = map.getCenter();
  const zoom = map.getZoom();

  if (isGoogle) {
    google.maps.event.trigger(map, "resize");
    map.setCenter(center);
    map.setZoom(zoom);
  } else {
    setTimeout(() => {
      map.invalidateSize(true);
      map.setView(center, zoom);
    }, 200);
  }
}

  responsive_map() {
    const map_obj = this;
    const isGoogle = map_obj.isMapProvider("google");
    const map = map_obj.map;
  
    window.addEventListener("resize", function () {
      const zoom = map.getZoom();
      const center = map.getCenter();
  
      if (isGoogle) {
        google.maps.event.trigger(map, "resize");
        map.setZoom(zoom);
        map.setCenter(center);
        map.getBounds();
        if (map_obj.map_data.marker_cluster) {
          map_obj.set_marker_cluster();
        }
      } else {
        setTimeout(() => {
          map.invalidateSize(true);
          map.setView(center, zoom);
        }, 200);
      }
    });
  } 
  

  // -----------------------------
  // 📍 Marker Management
  // -----------------------------
  createMarker() { }
  marker_bind(marker, handler) {
    const map_obj = this;
  
    // If no handler is provided, use default logic
    const defaultHandler = function () {
      const position = map_obj.isMapProvider("google")
        ? marker.getPosition()
        : marker.getLatLng();
  
      const lat = map_obj.isMapProvider("google") ? position.lat() : position.lat;
      const lng = map_obj.isMapProvider("google") ? position.lng() : position.lng;
  
      map_obj.wpgmp_geocode(lat, lng).then((result) => {
        $("#googlemap_address").val(result.address);
        $(".google_latitude").val(result.latitude);
        $(".google_longitude").val(result.longitude);
        $(".google_city").val(result.city);
        $(".google_state").val(result.state);
        $(".google_country").val(result.country);
        $(".google_postal_code").val(result.postal_code);
      }).catch((err) => {
        console.error(err);
      });
    };
  
    const dragendHandler = handler || defaultHandler;
  
    if (map_obj.isMapProvider("google")) {
      google.maps.event.addListener(marker, "dragend", dragendHandler);
    } else {
      marker.on("dragend", dragendHandler);
    }
  }
    
  create_markers() {
    const map_obj = this;
    const isGoogle = map_obj.isMapProvider("google");
    const places = map_obj.map_data.places;
    const remove_keys = [];
    const spiderfier_enabled = map_obj.map_data.map_marker_spiderfier_setting?.marker_spiderfy === "true";
    let oms;
  
    if (isGoogle && spiderfier_enabled) {
      let oms_args = {
        markersWontMove: true,
        markersWontHide: true,
        basicFormatEvents: true,
        keepSpiderfied: true,
      };
  
      if (map_obj.map_data.map_marker_spiderfier_setting.marker_enable_spiral === "true") {
        oms_args.circleSpiralSwitchover = map_obj.map_data.map_marker_spiderfier_setting.minimum_markers;
      }
  
      oms = new OverlappingMarkerSpiderfier(map_obj.map, oms_args);
    }
       
    $.each(places, function (key, place) {
      if (!(place.location.lat && place.location.lng)) {
        remove_keys.push(key);
        return;
      }
  
      place.categories ??= {};
      place.location.icon ??= map_obj.settings.marker_default_icon;
  
      // Create marker
      if (isGoogle) {
        
        place.marker = map_obj.create_google_marker({
          map: map_obj.map,
          position: place.location,
          iconUrl: place.location.icon,
          draggable: place.location.draggable,
          clickable: place.location.infowindow_disable,
        });

        place.marker.bindMap(map_obj.map);
        place.marker.setVisible(true);
  
        if (map_obj.settings.infowindow_drop_animation || place.location.animation === "DROP") {
          place.marker.setAnimation(google.maps.Animation.DROP);
        }
        if (place.location.animation === "BOUNCE1") {
          place.marker.setAnimation(google.maps.Animation.BOUNCE);
        }

        if (map_obj.settings.infowindow_filter_only) {
          place.marker.setVisible(false);
        }

      } else {

        place.marker = map_obj.create_leaflet_marker({
          map: map_obj.map,
          position: [parseFloat(place.location.lat), parseFloat(place.location.lng)],
          iconUrl: place.location.icon,
          draggable: place.location.draggable,
        });

        place.marker.addTo(map_obj.map);
        place.marker.setVisible(true);
  
        if (map_obj.settings.infowindow_filter_only) {
          place.marker.setVisible(false);
        }
  
        if (place.location.infowindow_disable || place.source !== "manual") {
          place.marker.bindPopup(place.content);
        }
        
      }
  
      // 🪢 Marker bind if editing
      if (map_obj.map_data.page === "edit_location") {
        map_obj.marker_bind(place.marker);
      }
  
      // Infowindow content replacement
      const location_categories = Object.values(place.categories).map(cat => "<div class='fc-badge'>"+cat.name+"</div>");
      const marker_image = place.source === "post"
        ? place.location.extra_fields.post_featured_image
        : place.location.marker_image;
  
      let template = place.source === "post"
        ? map_obj.settings.infowindow_geotags_setting
        : map_obj.settings.infowindow_setting;
  
      const post_info_class = place.source === "post"
      ? `wpgmp_infowindow_post fc-infobox-${map_obj.settings.infowindow_post_skin.name} fc-item-${map_obj.settings.infowindow_post_skin.name}`
      : `fc-infobox-${map_obj.settings.infowindow_skin?.name} fc-infowindow-${map_obj.settings.infowindow_skin?.name || "default"}`;

  
      template ??= place.content;
  
      const replaceData = {
        "{marker_id}": place.id,
        "{marker_title}": place.title,
        "{marker_address}": place.address,
        "{marker_latitude}": place.location.lat,
        "{marker_longitude}": place.location.lng,
        "{marker_city}": place.location.city,
        "{marker_state}": place.location.state,
        "{marker_country}": place.location.country,
        "{marker_postal_code}": place.location.postal_code,
        "{marker_zoom}": place.location.zoom,
        "{marker_icon}": place.location.icon,
        "{marker_category}": location_categories.join(""),
        "{marker_message}": place.content,
        "{marker_image}": marker_image,
        "{get_directions_link}": `https://www.google.com/maps/place/${parseFloat(place.location.lat)},${parseFloat(place.location.lng)}`
      };
  
      // Inject extra fields
      if (place.location.extra_fields) {
        for (const [key, val] of Object.entries(place.location.extra_fields)) {
          replaceData[`{${key}}`] = val || "<div class='wpgmp_empty'>wpgmp_empty</div>";
        }
      }
  
      template = template.replace(/{#if (.*?)}([\s\S]*?){\/if}/g, (_, key, content) => {
        const val = replaceData[`{${key}}`];
        return val && val !== "<div class='wpgmp_empty'>wpgmp_empty</div>" ? content : "";
      });
  
      if (map_obj.map_data.map_options.link_extra_field) {
        const anchor_tag = map_obj.map_data.map_options.link_extra_field;
        for (const prop_an in anchor_tag) {
          if (replaceData[prop_an] && replaceData[prop_an] !== "<div class='wpgmp_empty'>wpgmp_empty</div>") {
            template = template.replace(prop_an, anchor_tag[prop_an]);
          }
        }
      }
  
      for (const prop in replaceData) {
        template = template.replaceAll(prop, replaceData[prop] ?? "");
      }
  
      const tempObject = $("<div/>").html(template);
      tempObject.find(".wpgmp_extra_field:contains('wpgmp_empty')").remove();
      tempObject.find(".wpgmp_empty").prev().remove();
      tempObject.find(".wpgmp_empty").remove();
  
      let content = tempObject.prop("outerHTML") || "";
  
      if (map_obj.settings.map_infowindow_customisations && map_obj.settings.show_infowindow_header) {
        content = `<div class="wpgmp_infowindow ${post_info_class}"><div class="wpgmp_iw_head"><div class="wpgmp_iw_head_content">${place.title}</div></div><div class="wpgmp_iw_content">${content}</div></div>`;
      } else {
        content = `<div class="wpgmp_infowindow ${post_info_class}"><div class="wpgmp_iw_content">${content}</div></div>`;
      }
  
      place.infowindow_data = content;
      place.infowindow = map_obj.infowindow_marker;
  
      
  
      let on_event = map_obj.settings.infowindow_open_event;
      if (map_obj.isMobile) on_event = "click";
      if (oms && on_event === "click") on_event = "spider_click";
  

      if (map_obj.useAdvancedMarker && place.marker?.content && on_event == 'mouseover') {
        const content = place.marker.content;
      
        content.addEventListener(on_event, function () {
      
          map_obj.places.forEach(p => {
            p.infowindow?.close?.();
            p.marker?.setAnimation?.(null);
          });
      
          map_obj.openInfoWindow(place);
      
          if (map_obj.settings.infowindow_bounce_animation === on_event) {
            map_obj.toggle_bounce(place.marker);
          }
        });
      } else {
        // Fallback for classic markers (already working for you)
        map_obj.event_listener(place.marker, on_event, function () {

          map_obj.places.forEach(p => {
            p.infowindow?.close?.();
            p.marker?.setAnimation?.(null);
          });
      
          map_obj.openInfoWindow(place);
      
          if (map_obj.settings.infowindow_bounce_animation === on_event) {
            map_obj.toggle_bounce(place.marker);
          }
        });
      }
      
  
      if (oms) oms.addMarker(place.marker);
  
      if (on_event === "mouseover") {
        map_obj.event_listener(place.marker, "click", function () {
          if (map_obj.settings.infowindow_click_change_zoom > 0) {
            map_obj.map.setZoom(map_obj.settings.infowindow_click_change_zoom);
          }
          if (map_obj.settings.infowindow_click_change_center) {
            map_obj.map.setCenter(place.marker.getPosition());
          }
        });
      }
  
      if (map_obj.settings.infowindow_bounce_animation === "mouseover" && on_event !== "mouseover") {
        map_obj.event_listener(place.marker, "mouseover", function () {
          place.marker.setAnimation("bounce");
        });
        map_obj.event_listener(place.marker, "mouseout", function () {
          place.marker.setAnimation(null);
        });
      }
  
      if (map_obj.settings.infowindow_bounce_animation) {
        if (isGoogle) {
          google.maps.event.addListener(place.infowindow, "closeclick", function () {
            place.marker.setAnimation(null);
          });
        }
      }
      
      if (place.location.infowindow_default_open || map_obj.settings.default_infowindow_open) {
        setTimeout(function(){
          map_obj.openInfoWindow(place);
        },500)
      }
      map_obj.places.push(place);
    });
  
    remove_keys.forEach(key => delete places[key]);
  }
  
  display_markers() {
    const map_obj = this;
  
    map_obj.show_places = [];
    const categories = {};
  
    for (const place of map_obj.places) {
  
      if (map_obj.settings.infowindow_filter_only === true) {
        place.marker.setVisible(false);
      } else {
        place.marker.setVisible(true);
        map_obj.show_places.push(place);
      }
  
      if (place.categories) {
        $.each(place.categories, (_, category) => {
          if (!categories[category.name]) {
            categories[category.name] = category;
          }
        });
      }
    }
  
    map_obj.categories = categories;
  }
  placeMarker(lat, lng) {
    const map_obj = this;
    const isGoogle = map_obj.isMapProvider("google");
    const position = isGoogle
      ? {"lat":lat,"lng":lng}
      : L.latLng(lat, lng);
    // 🗑️ Remove existing marker (Leaflet: setMap(null), Google: setMap(null))
    if (map_obj.searchMarker) {
      map_obj.searchMarker.setMap(null);
    }
  
    // Create new marker
    if (isGoogle) {
      
      map_obj.searchMarker = map_obj.create_google_marker({
        map: map_obj.map,
        position,
        iconUrl: map_obj.settings.marker_default_icon || '',
        draggable: true,
        anchorPoint: { x: 0, y: -29 },
      });
      

    } else {

      map_obj.searchMarker = map_obj.create_leaflet_marker({
        map: map_obj.map,
        position,
        iconUrl: map_obj.settings.marker_default_icon || "",
        draggable: true,
      });

      
    }
  
    // Add to map using .setMap()
    map_obj.searchMarker.setMap(map_obj.map);
  
    // ind marker events if defined
    if (typeof map_obj.marker_bind === "function") {
      map_obj.marker_bind(map_obj.searchMarker);
    }
  
    // Center and zoom
    map_obj.map.setCenter(position);
    map_obj.map.setZoom(17);
  }
  
  toggle_bounce(marker) {
    const isGoogle = this.isMapProvider("google");
    if (isGoogle) {
      if (marker.getAnimation() !== null) {
        marker.setAnimation(null);
      } else {
        marker.setAnimation(google.maps.Animation.BOUNCE);
      }
    } else {
      const el = marker._icon;
      if (!el) return;
      const className = 'leaflet-bounce-animation';
      if (el.classList.contains(className)) {
        el.classList.remove(className);
      } else {
        el.classList.add(className);
      }
    }
  }
  show_center_circle() {
    const map_obj = this;
    const isGoogle = map_obj.isMapProvider("google");
  
    if (!map_obj.settings.center_circle_radius) {
      map_obj.settings.center_circle_radius = 5;
    }
  
    const radiusInMeters = parseInt(map_obj.settings.center_circle_radius) * 1000;
    const center = map_obj.map.getCenter();
  
    if (isGoogle) {
      map_obj.set_center_circle = new google.maps.Circle({
        map: map_obj.map,
        center: center,
        fillColor: map_obj.settings.center_circle_fillcolor,
        fillOpacity: map_obj.settings.center_circle_fillopacity,
        strokeColor: map_obj.settings.center_circle_strokecolor,
        strokeOpacity: map_obj.settings.center_circle_strokeopacity,
        strokeWeight: map_obj.settings.center_circle_strokeweight,
        radius: radiusInMeters
      });
    } else {
      map_obj.set_center_circle = L.circle(center, {
        fillColor: map_obj.settings.center_circle_fillcolor,
        fillOpacity: map_obj.settings.center_circle_fillopacity,
        color: map_obj.settings.center_circle_strokecolor,
        opacity: map_obj.settings.center_circle_strokeopacity,
        weight: map_obj.settings.center_circle_strokeweight,
        radius: radiusInMeters
      }).addTo(map_obj.map);
    }
  }
  
  /**
 * Adds a marker to the center of the map and binds an optional infowindow
 */
show_center_marker() {
  const map_obj = this;
  const isGoogle = map_obj.isMapProvider("google");
  const center = map_obj.map.getCenter();
  const hasInfo = map_obj.settings.center_marker_infowindow !== "";

  if (isGoogle) {

    map_obj.map_center_marker = map_obj.create_google_marker({
      map: map_obj.map,
      position: {lat:center.lat(),lng:center.lng()},
      title: map_obj.settings.center_marker_infowindow,
      iconUrl: map_obj.settings.center_marker_icon,
      clickable: hasInfo,
    });
    

    if (!map_obj.map_center_info) {
      map_obj.map_center_info = map_obj.infowindow_marker;
    }

    if (hasInfo) {
      google.maps.event.addListener(map_obj.map_center_marker, "click", function () {
        map_obj.map_center_info.setPosition(center);
        const content = map_obj.settings.map_infowindow_customisations === true
          ? `<div class='fc-infobox-root'><div class='fc-infobox'><div class='fc-infobox-body'>${map_obj.settings.center_marker_infowindow}</div></div></div>`
          : map_obj.settings.center_marker_infowindow;
        map_obj.map_center_info.setContent(content);
        map_obj.map_center_info.open(map_obj.map, this);
      });
    }
  } else {
    
    map_obj.map_center_marker = map_obj.create_leaflet_marker({
      map: map_obj.map,
      position: center,
      iconUrl: map_obj.settings.center_marker_icon,
      draggable: true,
    });
    

    if (!map_obj.map_center_info) {
      map_obj.map_center_info = map_obj.infowindow_marker;
    }

    if (hasInfo) {
      map_obj.event_listener(map_obj.map_center_marker, "click", function () {
        const content = `<div class='fc-infobox-root'><div class='fc-infobox'><div class='fc-infobox-body'>${map_obj.settings.center_marker_infowindow}</div></div></div>`;
        const latlng = map_obj.map.getCenter();
        map_obj.map_center_info.setContent(content).setLatLng(latlng).openOn(map_obj.map);
      });
    }
  }
}

  // Unified version to center the map based on user's current location
center_by_nearest() {
  const map_obj = this;
  const isGoogle = map_obj.isMapProvider("google");

  this.get_current_location(
    function (user_position) {
      if (!map_obj.user_location_marker) {
        if (isGoogle) {

          map_obj.user_location_marker = map_obj.create_google_marker({
            map: map_obj.map,
            position: {"lat":user_position.lat(),"lng":user_position.lng()},
            title: wpgmp_local.center_location_message,
            iconUrl: map_obj.map_data.map_options.center_marker_icon,
          });

          
        } else {

          map_obj.user_location_marker = map_obj.create_leaflet_marker({
            map: map_obj.map,
            position: user_position,
            iconUrl: map_obj.settings.center_marker_icon,
            title: wpgmp_local.center_location_message,
          });
          

        }
      }

      if (!map_obj.map_center_info) {
        map_obj.map_center_info = map_obj.infowindow_marker;
      }

      if (map_obj.settings.center_marker_infowindow !== "") {
        const showInfo = function () {
          const content = map_obj.settings.map_infowindow_customisations
            ? `<div class='wpgmp_infowindow'><div class='wpgmp_iw_content'>${map_obj.settings.center_marker_infowindow}</div></div>`
            : map_obj.settings.center_marker_infowindow;

          if (isGoogle) {
            map_obj.map_center_info.setPosition(user_position);
            map_obj.map_center_info.setContent(content);
            map_obj.map_center_info.open(map_obj.map, map_obj.user_location_marker);
          } else {
            map_obj.map_center_info.setContent(content);
            map_obj.map_center_info.setLatLng(user_position).openOn(map_obj.map);
          }
        };

        if (isGoogle) {
          google.maps.event.addListener(map_obj.user_location_marker, "click", showInfo);
        } else {
          map_obj.event_listener(map_obj.user_location_marker, "click", showInfo);
        }
      }

      if (isGoogle) {
        map_obj.map.setCenter(user_position);
      } else {
        map_obj.map.setView(user_position, map_obj.settings.zoom);
      }

      if (map_obj.settings.show_center_circle === true) {
        map_obj.show_center_circle();
      }

      if (
        map_obj.map_data.listing &&
        map_obj.map_data.listing.apply_default_radius === true
      ) {
        map_obj.search_area = user_position;
      }
    },
    function (user_position) {
      // Fallback on error with same logic
      if (!map_obj.user_location_marker) {
        map_obj.user_location_marker = L.marker(user_position, {
          icon: L.icon({
            iconUrl: map_obj.settings.center_marker_icon,
            iconAnchor: [16, 32],
            popupAnchor: [0, -30]
          }),
          title: wpgmp_local.center_location_message
        }).addTo(map_obj.map);
      }

      if (!map_obj.map_center_info) {
        map_obj.map_center_info = map_obj.infowindow_marker;
      }

      if (map_obj.settings.center_marker_infowindow !== "") {
        map_obj.event_listener(map_obj.user_location_marker, "click", function () {
          const content = `<div class='wpgmp_infowindow'><div class='wpgmp_iw_content'>${map_obj.settings.center_marker_infowindow}</div></div>`;
          map_obj.map_center_info.setContent(content);
          map_obj.map_center_info.setLatLng(user_position).openOn(map_obj.map);
        });
      }

      map_obj.map.panTo(user_position);

      if (map_obj.settings.show_center_circle === true) {
        map_obj.show_center_circle();
      }

      if (
        map_obj.map_data.listing &&
        map_obj.map_data.listing.apply_default_radius === true
      ) {
        map_obj.search_area = user_position;
      }
    }
  );
} 

  /**
 * Adjusts the map viewport to fit all visible markers
 */
fit_bounds() {
  const map_obj = this;
  const isGoogle = map_obj.isMapProvider("google");
  const places = map_obj.map_data.places || [];
  let bounds;
  if (places.length === 0) return;

  if (isGoogle) {
    bounds = new google.maps.LatLngBounds();
    places.forEach((place) => {
      if (place.location.lat && place.location.lng) {
        bounds.extend(new google.maps.LatLng(
          parseFloat(place.location.lat),
          parseFloat(place.location.lng)
        ));
      }
    });
    map_obj.map.fitBounds(bounds);
  } else {
    bounds = new L.LatLngBounds();
    places.forEach((place) => {
      if (place.location.lat && place.location.lng) {
        bounds.extend(L.latLng(
          parseFloat(place.location.lat),
          parseFloat(place.location.lng)
        ));
      }
    });
    map_obj.map.fitBounds(bounds);
  }

  // Set center marker and circle to bounds center
  const center = isGoogle ? bounds.getCenter() : bounds.getCenter();

  if (map_obj.map_center_marker) {
    map_obj.map_center_marker.setPosition(center);
  }

  if (map_obj.set_center_circle) {
    if (isGoogle ) {
      map_obj.set_center_circle.setCenter(center);
    } else {
      map_obj.set_center_circle.setLatLng(center);
    }
  }
} 

  open_infowindow(current_place_id) {
    const map_obj = this;
    const isGoogle = map_obj.isMapProvider("google");
    const open_event = map_obj.settings.infowindow_open_event || 'click';
    const zoom = parseInt(map_obj.settings.infowindow_click_change_zoom);
    const shouldZoom = !isNaN(zoom) && zoom > 0;
    const shouldCenter = map_obj.settings.infowindow_click_change_center === true;
  
    const all_places = map_obj.map_data.places || [];
  
    for (const place of all_places) {
      const marker = place.marker;
  
      if (!marker || marker.getVisible?.() === false) continue;
  
      // If this is the target place
      if (parseInt(place.id) === parseInt(current_place_id)) {
        if (shouldZoom) {
          map_obj.map.setZoom(zoom);
        }
  
        if (shouldCenter) {
          map_obj.map.setCenter(marker.getPosition());
        }
  
        // Trigger the configured open event
        if (isGoogle) {
          google.maps.event.trigger(marker, open_event);
        } else {
          marker.fire(open_event);
        }
      }
    }
  }
  openInfoWindow(place) {
    const map_obj = this;
    const isGoogle = map_obj.isMapProvider("google");
    let skin = "default";
  
    if (place.source === "post") {
      skin = map_obj.settings.infowindow_post_skin?.name || "default";
    } else if (
      map_obj.map_data.page !== "edit_location" &&
      map_obj.settings.infowindow_skin
    ) {
      skin = map_obj.settings.infowindow_skin.name;
    }
  
    // Handle redirect actions
    const action = place.location.onclick_action;
    const openInNewTab = place.location.open_new_tab === "yes";
    if (action === "post") {
      window.open(place.location.redirect_permalink, openInNewTab ? "_blank" : "_self");
      return;
    } else if (action === "custom_link") {
      window.open(place.location.redirect_custom_link, openInNewTab ? "_blank" : "_self");
      return;
    }
  
    // Infowindow logic
    if (isGoogle && skin !== "default") {
      const infoboxText = document.createElement("div");
      infoboxText.className = "wpgmp_infobox wpgmp-infowindow-addon";
      infoboxText.innerHTML = place.infowindow_data;
  
      place.infowindow = map_obj.infobox;
      place.infowindow.setOptions({
        content: infoboxText,
        disableAutoPan: false,
        alignBottom: true,
        maxWidth: 0,
        zIndex: null,
        closeBoxMargin: "0",
        closeBoxURL: "",
        infoBoxClearance: new google.maps.Size(25, 25),
        isHidden: false,
        pane: "floatPane",
        enableEventPropagation: false,
      });

      const mapOptions = map_obj?.map_data?.map_options || {};
      let width = "100%";
      if( map_obj.settings.map_infowindow_customisations ) {
        width = mapOptions.infowindow_width || '100%';
      }
      const markerSize = map_obj.getMarkerSize?.() || [32, 32];

      // Calculate offsetX
      let offsetX = -175;
      if (width !== '100%') {
        const numericWidth = parseInt(width);
        if (!isNaN(numericWidth)) {
          offsetX = -(numericWidth / 2);
        }
      }

      // Calculate offsetY based on marker height
      const markerHeight = parseInt(markerSize[1]) || 32;
      const offsetY = -(markerHeight) -15;
      // Apply offset to infowindow
      
      place.infowindow.setOptions({
        pixelOffset: new google.maps.Size(offsetX, offsetY),
      });

    } else {

      let offsetX = 0;
      const markerSize = map_obj.getMarkerSize?.() || [32, 32];
      const markerHeight = parseInt(markerSize[1]) || 32;
      const offsetY = -(markerHeight / 2);
      place.infowindow = isGoogle
        ? map_obj.infowindow_marker
        : L.popup({offset:[offsetX,offsetY]});
  
      place.infowindow.setContent(place.infowindow_data);
    }
  
    // Open infowindow
    place.infowindow.open(map_obj.map, place.marker);
  
    // Move to marker if configured
    if (map_obj.settings.infowindow_click_change_center) {
      map_obj.map.setCenter(place.marker.getPosition());
    }
  
    if (
      map_obj.settings.infowindow_click_change_zoom &&
      map_obj.settings.infowindow_click_change_zoom > 0
    ) {
      map_obj.map.setZoom(map_obj.settings.infowindow_click_change_zoom);
      map_obj.map.setCenter(place.marker.getPosition());
    }
  
    // Autofill directions
    if (
      map_obj.map_data.map_tabs?.direction_tab?.dir_tab === true
    ) {
      $(map_obj.container).find(".start_point").val(place.address);
      $(map_obj.container).find(".start_point").data("longitude", place.location.lng);
      $(map_obj.container).find(".start_point").data("latitude", place.location.lat);
    }
  
    // Cleanup
    $(map_obj.container).find(".wpgmp_extra_field:contains('wpgmp_empty')").remove();
    $(map_obj.container).find(".wpgmp_empty").prev().remove();
    $(map_obj.container).find(".wpgmp_empty").remove();

    // Dispatch marker click event for addons to hook into
    $(document).trigger("wpgmp_marker_clicked", [this, place]);

  }
  create_element(controlDiv, map, html_element) {

    // Set CSS for the control border
    controlDiv.className = 'wpgmp-control-outer';
    var controlUI = document.createElement('div');
    controlUI.className = 'wpgmp-control-inner';
    controlDiv.appendChild(controlUI);

    // Set CSS for the control interior
    var controlText = document.createElement('div');
    controlText.className = 'wpgmp-control-content';
    controlText.innerHTML = html_element;
    controlUI.appendChild(controlText);

  }
  // -----------------------------
  // 📑 Templates & Rendering
  // -----------------------------
  renderCategories(template, data) {
    let rendered = template;

    // Handle the case where parent categories do not exist
    if (!data || data.length === 0) {
      rendered = rendered.replace(
        /\{#each parent_categories\}[\s\S]+?\{\/parent_categories\}/g,
        "No categories available."
      );
      return rendered;
    }

    // Replace the each block for parent and child categories
    const parentPattern =
      /\{#each parent_categories\}([\s\S]+?)\{\/parent_categories\}/g;
    rendered = rendered.replace(
      parentPattern,
      function (match, parentTemplate) {
        let result = "";

        data.forEach((parentCategory) => {
          let parentHTML = parentTemplate;
          // Replace placeholders for parent category
          parentHTML = parentHTML.replace(
            /\{parent_id\}/g,
            parentCategory.parent_id || ""
          );
          parentHTML = parentHTML.replace(
            /\{child_list\}/g,
            parentCategory.child_list || ""
          );
          parentHTML = parentHTML.replace(
            /\{parent_category\}/g,
            parentCategory.parent_category || "Unknown Category"
          );

          // Handle child categories
          let childPattern =
            /\{#each child_categories\}([\s\S]+?)\{\/child_categories\}/g;

          if (
            parentCategory.child_categories &&
            parentCategory.child_categories.length > 0
          ) {
            parentHTML = parentHTML.replace(
              childPattern,
              function (childMatch, childTemplate) {
                let childResult = "";
                parentCategory.child_categories.forEach((child) => {
                  let childTemp = childTemplate;
                  childTemp = childTemp.replace(
                    /\{child_id\}/g,
                    child.child_id || ""
                  );
                  childTemp = childTemp.replace(
                    /\{child_category\}/g,
                    child.child_category || "Unknown Child Category"
                  );
                  childResult += childTemp;
                });
                return childResult;
              }
            );
          } else {
            // If no child categories exist, remove the entire child category box
            parentHTML = parentHTML.replace(childPattern, "");
          }

          result += parentHTML;
        });

        return result;
      }
    );

    return rendered;
  }
  renderTemplate(template, data) {
    var rendered = template;

    // Handle if-else condition outside of each loop
    var ifPattern = /\{#if (\w+)\}([\s\S]+?)\{else\}([\s\S]+?)\{\/if\}/g;
    rendered = rendered.replace(
      ifPattern,
      function (ifMatch, ifKey, ifTrue, ifFalse) {
        return data[ifKey] ? ifTrue : ifFalse;
      }
    );

    var ifSimplePattern = /\{#if (\w+)\}([\s\S]+?)\{\/if\}/g;
    rendered = rendered.replace(
      ifSimplePattern,
      function (ifMatch, ifKey, ifTrue) {
        return data[ifKey] ? ifTrue : "";
      }
    );

    // Replace the each block for all_filters
    var eachPattern = /\{#each all_filters\}([\s\S]+?)\{\/all_filters\}/g;
    rendered = rendered.replace(eachPattern, function (match, p2) {
      var result = "";

      var allFilters = data["all_filters"];

      // Iterate over all filters (facility, bathrooms, etc.)
      for (var filterName in allFilters) {
        var filterData = allFilters[filterName];

        var firstKey = Object.keys(filterData)[0]; // Gets the first key
        var filterLabel = filterData[firstKey].label; // Gets the corresponding value

        var filterTemplate = p2;

        // Replace {filter_name} with the filter key (e.g., facility, bathrooms)
        filterTemplate = filterTemplate.replace(
          /\{filter_name\}/g,
          filterName
        );
        filterTemplate = filterTemplate.replace(
          /\{filter_label\}/g,
          filterLabel
        );

        // Now handle the inner {#each filter} loop for options
        var innerEachPattern = /\{#each filter\}([\s\S]+?)\{\/filter\}/g;
        filterTemplate = filterTemplate.replace(
          innerEachPattern,
          function (innerMatch, innerP2) {
            var innerResult = "";

            for (var key in filterData) {
              var option = {
                id: key.toLowerCase(),
                name: filterData[key].name,
                label: filterData[key].label,
              };

              if (filterName != "category") {
                option.id = filterData[key].id;
              }

              var optionTemplate = innerP2;

              // Replace the placeholders {id} and {name}
              optionTemplate = optionTemplate.replace(/\{id\}/g, option.id);

              optionTemplate = optionTemplate.replace(
                /\{name\}/g,
                option.name
              );

              innerResult += optionTemplate;
            }
            return innerResult;
          }
        );

        result += filterTemplate;
      }

      return result;
    });

    var eachPattern = /\{#each ([\w%_-]+)\}([\s\S]+?)\{\/\1\}/g; // Updated to allow dashes
    rendered = rendered.replace(eachPattern, function (match, p1, p2) {
      var items = data[p1];
      var result = "";

      if (
        typeof items === "object" &&
        items !== null &&
        !Array.isArray(items)
      ) {
        for (var key in items) {
          var item;
          if (p1 == "routes") {
            item = {
              id: key.toLowerCase(),
              name: items[key][0],
              color: items[key][1],
            };
          } else {
            item = { id: key.toLowerCase(), name: items[key] };
          }

          var itemTemplate = p2;

          // Handle if-else condition inside each loop
          itemTemplate = itemTemplate.replace(
            ifPattern,
            function (ifMatch, ifKey, ifTrue, ifFalse) {
              return item[ifKey] ? ifTrue : ifFalse;
            }
          );

          itemTemplate = itemTemplate.replace(
            ifSimplePattern,
            function (ifMatch, ifKey, ifTrue) {
              return item[ifKey] ? ifTrue : "";
            }
          );

          // Replace the placeholders within each item
          for (var fieldKey in item) {
            itemTemplate = itemTemplate.replace(
              new RegExp("\\{" + fieldKey + "\\}", "g"),
              item[fieldKey]
            ); // Escape braces
          }
          result += itemTemplate;
        }
      }

      return result;
    });

    // Replace the placeholders outside the each block
    for (var key in data) {
      if (!Array.isArray(data[key]) && typeof data[key] === "object") {
        // Skip objects being handled within each block
        continue;
      }
      rendered = rendered.replace(
        new RegExp("\\{" + key + "\\}", "g"),
        data[key]
      ); // Escape braces
    }

    return rendered;
  }
 
  create_filters() {
    var map_obj = this;
    var options = "";
    var filters = {};
    var places = this.map_data.places;
    var wpgmp_listing_filter = this.map_data.listing;
    var wpgmp_alltfilter = wpgmp_listing_filter.display_taxonomies_all_filter;

    $.each(places, function (index, place) {
      if (typeof place.categories == "undefined") {
        place.categories = {};
      }
      $.each(place.categories, function (cat_index, category) {
        if (typeof filters[category.type] == "undefined") {
          filters[category.type] = {};
        }

        if (category.name) {
          if (
            category.extension_fields &&
            category.extension_fields.cat_order
          ) {
            filters[category.type][category.name] = {
              id: category.id,
              order: category.extension_fields.cat_order,
              name: category.name,
            };
          } else {
            filters[category.type][category.name] = {
              id: category.id,
              order: 0,
              name: category.name,
            };
          }
        }
      });
    });
    // now create select boxes

    var content = "",
      by = "name",
      type = "",
      inorder = "asc";

    if (map_obj.map_data.listing) {
      if (map_obj.map_data.listing.default_sorting) {
        if (map_obj.map_data.listing.default_sorting.orderby == "listorder") {
          by = "order";
          type = "num";
          inorder = map_obj.map_data.listing.default_sorting.inorder;
        }
        inorder = map_obj.map_data.listing.default_sorting.inorder;
      }
    }

    if (
      map_obj.map_data.checkbox_filter_addon_enable != undefined &&
      map_obj.map_data.checkbox_filter_addon_enable == true
    ) {
      $.each(filters, function (index, options) {
        if (
          wpgmp_listing_filter.display_category_filter === true &&
          index == "category"
        ) {
          content += '<div class="wpgmp_filters_checklist">';
          content +=
            '<label  data-filter = "place_' +
            index +
            '" >Select ' +
            index +
            "</label>";
          options = map_obj.sort_object_by_keyvalue(
            options,
            by,
            type,
            inorder
          );
          $.each(options, function (name, value) {
            if (value != "" && value != null)
              content +=
                "<input data-filter='checklist' type='checkbox' data-name = 'category' value='" +
                value.id +
                "'><span class='wpgmp_checklist_title'>" +
                value.name +
                "</span>";
          });
          content += "</div>";
        } else if (wpgmp_listing_filter.display_taxonomies_filter === true) {
          if (wpgmp_alltfilter === null) return false;

          if (wpgmp_alltfilter.indexOf(index) > -1) {
            content += '<div class="wpgmp_filters_checklist">';
            content +=
              '<label  data-filter = "place_' +
              index +
              '" >Select ' +
              index +
              "</label>";

            $.each(options, function (name, value) {
              if (value != "" && value != null)
                content +=
                  "<input data-filter='checklist' type='checkbox' data-name = 'category' value='" +
                  value.id +
                  "'><span class='wpgmp_checklist_title'>" +
                  value.name +
                  "</span>";
            });
            content += "</div>";
          }
        }
      });
    } else {
      $.each(filters, function (index, options) {
        if (
          wpgmp_listing_filter.display_category_filter === true &&
          index == "category"
        ) {
          content +=
            '<select data-wpgmp-filter="true" data-wpgmp-map-id="'+map_obj.map_data.map_property.map_id+'" data-wpgmp-filter-by="category" data-filter="dropdown" data-name="category" name="place_' +
            index +
            '">';
          content +=
            '<option value="">' + wpgmp_local.select_category + "</option>";
          options = map_obj.sort_object_by_keyvalue(
            options,
            by,
            type,
            inorder
          );
          $.each(options, function (name, value) {
            content +=
              "<option value='" + value.id + "'>" + value.name + "</option>";
          });
          content += "</select>";
        } else if (wpgmp_listing_filter.display_taxonomies_filter === true) {
          if (wpgmp_alltfilter === null) return false;

          if (wpgmp_alltfilter.indexOf(index) > -1) {
            content +=
              '<select data-filter="dropdown" data-name="category" name="place_' +
              index +
              '">';
            content += '<option value="">Select ' + index + "</option>";
            $.each(options, function (name, value) {
              content +=
                "<option value='" + value + "'>" + name + "</option>";
            });
            content += "</select>";
          }
        }
      });
    }

    if (map_obj.map_data.map_options.advance_template) {
      if (!map_obj.template_data) {
        map_obj.template_data = {}; // Ensure template_data exists
      }
      if (!Array.isArray(map_obj.template_data.filter)) {
        map_obj.template_data.filter = []; // Ensure filter is an array
      }
      map_obj.template_data.filter.push(filters);
    }

    return content;
  }

  update_places_listing() {
    var map_obj = this;

    if (map_obj.per_page_value > 0)
      map_obj.per_page_value = map_obj.per_page_value;
    else
      map_obj.per_page_value =
        map_obj.map_data.listing.pagination.listing_per_page;

    $(map_obj.container)
      .find(".location_pagination" + map_obj.map_data.map_property.map_id)
      .pagination(map_obj.show_places.length, {
        callback: map_obj.display_places_listing,
        map_data: map_obj,
        items_per_page: map_obj.per_page_value,
        prev_text: wpgmp_local.prev,
        next_text: wpgmp_local.next,
      });
  }

  display_filters_listing() {
    if (this.map_data.listing) {
      var hide_locations = this.map_data.listing.hide_locations;

      var wpgmpgl = this.map_data.listing.list_grid;

      if (hide_locations != true) {
        var content = '<div class="wpgmp_listing_container">';

        content +=
          "<div class='wpgmp_categories wpgmp_print_listing " +
          wpgmpgl +
          "' data-container='wpgmp-listing-" +
          $(this.element).attr("id") +
          "'></div>";

        content += "</div>";
        $(this.map_data.listing.listing_container).html(content);
      }
    }
  }

  display_filters() {
    var hide_locations = this.map_data.listing.hide_locations;
    var listing_header = this.map_data.listing.listing_header;
    var content = "";
    if (listing_header != undefined) {
      content +=
        '<div class="wpgmp_before_listing">' +
        this.map_data.listing.listing_header +
        "</div>";
    }
    if (this.map_data.listing.display_search_form === true) {
      var autosuggest_class = "";

      if (this.map_data.listing.search_field_autosuggest === true) {
        autosuggest_class = "wpgmp_auto_suggest";
      }

      content +=
        '<div class="wpgmp_listing_header"><div class="wpgmp_search_form"><input type="text" rel="24" data-input="wpgmp-search-text" name="wpgmp_search_input" class="wpgmp_search_input ' +
        autosuggest_class +
        '" placeholder="' +
        wpgmp_local.search_placeholder +
        '"></div></div>';
    }

    content +=
      '<div class="categories_filter">' +
      this.create_filters() +
      '<div data-container="wpgmp-filters-container"></div>';

    if (hide_locations != true) content += this.create_sorting() + "";

    if (
      hide_locations != true &&
      this.map_data.listing.display_location_per_page_filter === true
    ) {
      content += " " + this.create_perpage_option() + " ";
    }

    content += " " + this.create_radius() + " ";

    if (
      hide_locations != true &&
      this.map_data.listing.display_print_option === true
    ) {
      content += " " + wpgmp_local.img_print;
    }

    if (
      hide_locations != true &&
      this.map_data.listing.display_grid_option === true
    ) {
      content += " " + wpgmp_local.img_grid + wpgmp_local.img_list;
    }

    if (
      typeof this.map_data.map_options.display_reset_button != "undefined" &&
      this.map_data.map_options.display_reset_button === true
    ) {
      content +=
        '<div class="categories_filter_reset"><input type="button" class="categories_filter_reset_btn" name="categories_filter_reset_btn" value="' +
        this.map_data.map_options.map_reset_button_text +
        '"></div>';
    }

    content += "</div>";

    return content;
  }

  set_icon_marker(place, iconUrl, iconSize = [32, 32]) {
    if (!place || !place.marker || !iconUrl) return;
  
    const isGoogle = this.isMapProvider('google');
    const isSvg = iconUrl.toLowerCase().includes('image/svg') || iconUrl.toLowerCase().endsWith('.svg');
  
    if (isGoogle) {
      // Google Maps: uses scaledSize and url object
      const icon = { url: iconUrl };

      if (isSvg) {
        icon.scaledSize = new google.maps.Size(iconSize[0], iconSize[1]);
      }

      place.marker.setIcon(icon);

  
    } else {
      const icon = { url: iconUrl };

      if (isSvg) {
        icon.scaledSize = [iconSize[0], iconSize[1]];
      }

      place.marker.setIcon(icon);

    }
  }

  
  display_places_listing(page_index, jq) {
    var content = "";
    var map_obj = this;
    var category_selector_dropdown = $('select[name = "place_category"]');
    var items_per_page = 10;
    if (map_obj.items_per_page) items_per_page = map_obj.items_per_page;
    else
      items_per_page =
        map_obj.map_data.map_data.listing.pagination.listing_per_page;

    if ($.isFunction($.fn.locationSortByDistance)) {
      var distance_image =
        map_obj.map_data.map_data.plugin_url +
        "assets/images/location-icon.png";
      if (
        ($("#search_location_autocomplete").val() != "" &&
          map_obj.map_data.is_place_changed) ||
        map_obj.map_data.is_location_allowed
      ) {
        map_obj.map_data.show_places =
          map_obj.map_data.sorted_locations_by_search;
      }
    }

    var data_source = map_obj.map_data.show_places;

    var listing_container =
      map_obj.map_data.map_data.listing.listing_container;

    var listing_placeholder =
      map_obj.map_data.map_data.listing.listing_placeholder;

    var max_elem = Math.min(
      (page_index + 1) * items_per_page,
      data_source.length
    );
    var link = "";
    var onclick_action = "";
    if (max_elem > 0) {
      for (var i = page_index * items_per_page; i < max_elem; i++) {
        var place = data_source[i];
        var temp_listing_placeholder = listing_placeholder;
        if (place.marker.getVisible() === true) {
          if (place.id) {
            if (place.location.onclick_action == "marker") {
              link =
                '<a href="javascript:void(0);" class="place_title" data-zoom="' +
                place.location.zoom +
                '"  data-marker="' +
                place.id +
                '" >' +
                place.title +
                "</a>";
              onclick_action =
                'href="javascript:void(0);" data-zoom="' +
                place.location.zoom +
                '"  data-marker="' +
                place.id +
                '"';
            } else if (place.location.onclick_action == "post") {
              link =
                '<a href="' +
                place.location.redirect_permalink +
                '" target="_blank">' +
                place.title +
                "</a>";
              onclick_action =
                'href="' +
                place.location.redirect_permalink +
                '" target="_blank"';
            } else if (place.location.onclick_action == "custom_link") {
              link =
                '<a href="' +
                place.location.redirect_custom_link +
                '" target="_blank">' +
                place.title +
                "</a>";
              onclick_action =
                'href="' +
                place.location.redirect_custom_link +
                '" target="_blank"';
            } else {
              link =
                '<a href="javascript:void(0);" class="place_title" data-zoom="' +
                place.location.zoom +
                '"  data-marker="' +
                place.id +
                '" >' +
                place.title +
                "</a>";
              onclick_action =
                'href="javascript:void(0);" data-zoom="' +
                place.location.zoom +
                '"  data-marker="' +
                place.id +
                '"';
            }
          }

          var image = [];
          var category_name = [];
          var wpgmp_arr = {};

          if (place.categories) {
            for (var c = 0; c < place.categories.length; c++) {
              if (place.categories[c].icon !== "") {
                image.push(
                  "<img title='" +
                    place.categories[c].name +
                    "' alt='" +
                    place.categories[c].name +
                    "' src='" +
                    place.categories[c].icon +
                    "' />"
                );
              }

              if (
                place.categories[c].type == "category" &&
                place.categories[c].name != ""
              ) {
                if (
                  map_obj.map_data.map_data.map_options.advance_template ==
                  true
                ) {
                  category_name.push(
                    '<span class="wep-chip">' +
                      place.categories[c].name +
                      "</span>"
                  );
                } else {
                  category_name.push('<div class="fc-badge">'+place.categories[c].name+'</div>');
                }
              }

              if (place.categories[c].type != "category") {
                if (typeof place.categories[c].name == "undefined") continue;

                if (place.categories[c].name) var sep = ",";

                if (typeof wpgmp_arr[place.categories[c].type] == "undefined")
                  wpgmp_arr[place.categories[c].type] = "";

                wpgmp_arr[place.categories[c].type] +=
                  place.categories[c].name + sep;
              }
            }
          }

          var marker_image = "";

          if (place.source == "post") {
            marker_image = place.location.extra_fields.post_featured_image;
          } else {
            marker_image = place.location.marker_image;
          }

          category_name = category_name.join("");

          var replaceData = {
            "{marker_id}": place.id,
            "{marker_title}": link,
            "{marker_address}": place.address,
            "{marker_latitude}": place.location.lat,
            "{marker_longitude}": place.location.lng,
            "{marker_city}": place.location.city,
            "{marker_state}": place.location.state,
            "{marker_country}": place.location.country,
            "{marker_postal_code}": place.location.postal_code,
            "{marker_zoom}": place.location.zoom,
            "{marker_icon}": image,
            "{marker_category}": category_name,
            "{marker_message}": place.content,
            "{marker_image}": marker_image,
            "{marker_featured_image}": marker_image,
            "{wpgmp_listing_html}": place.listing_hook,
            "{onclick_action}": onclick_action,
            "{distance}": "",
            "{get_directions_link}":
              "http://www.google.com/maps/place/" +
              parseFloat(place.location.lat) +
              "," +
              parseFloat(place.location.lng),
          };

          if ($.isFunction($.fn.locationSortByDistance)) {
            if (
              typeof place.distance !== "undefined" &&
              place.distance !== null &&
              place.distance !== "NaN"
            ) {
              var dimension =
                map_obj.map_data.map_data.listing.radius_dimension;
              if (dimension == "km") {
                if ((place.distance / 1000).toFixed(2) != "NaN") {
                  place.distance = (place.distance / 1000).toFixed(2) + " Km";
                  place.location.extra_fields.distance =
                    '<img width="21px" height="26px" src="' +
                    distance_image +
                    '" style="width: 21px!important;height: 26px!important;margin-right: 3px!important;margin-top: 0px!important;"><span class="distance_calculate">' +
                    place.distance +
                    "</span>";
                }
              } else {
                if ((place.distance / 1000).toFixed(2) != "NaN") {
                  place.distance = (place.distance / 1000).toFixed(2);
                  place.distance =
                    (place.distance / 1.6).toFixed(2) + " Miles";
                  place.location.extra_fields.distance =
                    '<img width="21px" height="26px" src="' +
                    distance_image +
                    '" style="width: 21px!important;height: 26px!important;margin-right: 3px!important;margin-top: 0px!important;"><span class="distance_calculate">' +
                    place.distance +
                    "</span>";
                }
              }
            }
          }

          //Add extra fields of locations
          if (typeof place.location.extra_fields != "undefined") {
            for (var extra in place.location.extra_fields) {
              if (!place.location.extra_fields[extra]) {
                replaceData["{" + extra + "}"] =
                  "<div class='wpgmp_empty'>wpgmp_empty</div>";
              } else {
                replaceData["{" + extra + "}"] =
                  place.location.extra_fields[extra];
              }
            }
          }

          temp_listing_placeholder = temp_listing_placeholder.replace(
            /{#if (.*?)}([\s\S]*?){\/if}/g,
            function (match, p1, p2) {
              const key = "{" + p1 + "}";
              const value = replaceData[key];
              return value &&
                value !== "<div class='wpgmp_empty'>wpgmp_empty</div>"
                ? p2
                : "";
            }
          );

          if (
            map_obj.map_data.map_data.map_options.link_extra_field !=
              undefined &&
            map_obj.map_data.map_data.map_options.link_extra_field != ""
          ) {
            var anchor_tag =
              map_obj.map_data.map_data.map_options.link_extra_field;
            for (var prop_an in anchor_tag) {
              if (
                replaceData[prop_an] !=
                  "<div class='wpgmp_empty'>wpgmp_empty</div>" &&
                prop_an != ""
              ) {
                temp_listing_placeholder = temp_listing_placeholder.replace(
                  prop_an,
                  anchor_tag[prop_an]
                );
              }
            }
          }

          for (var prop in replaceData) {
            if (
              replaceData[prop] == undefined ||
              replaceData[prop] == "undefined"
            )
              replaceData[prop] = "";
          }

          if (wpgmp_arr) {
            for (var n in wpgmp_arr) {
              replaceData["{" + n + "}"] = wpgmp_remove_last_comma(
                wpgmp_arr[n]
              );
            }
          }

          var wpgmp_remove_last_comma = function (strng) {
            var n = strng.lastIndexOf(",");
            var a = strng.substring(0, n);
            return a;
          };

          temp_listing_placeholder = temp_listing_placeholder.replace(
            /{[^{}]+}/g,
            function (match) {
              if (match in replaceData) {
                return replaceData[match];
              } else {
                return "";
              }
            }
          );

          content += temp_listing_placeholder;
        }
      }
    } else {
      content =
        "<div class='wpgmp_no_locations'>" +
        wpgmp_local.wpgmp_location_no_results +
        "</div>";
    }

    for (var j = 0; j < data_source.length; j++) {
      place = data_source[j];
      if (
        typeof place.categories.length !== undefined &&
        place.categories.length > 1
      ) {
        var iconSize = map_obj.map_data.getMarkerSize();
        if (category_selector_dropdown.val() !== "") {
          if (place.categories) {
            for (var c = 0; c < place.categories.length; c++) {
              if (
                category_selector_dropdown.val() == place.categories[c].id
              ) {
                
                map_obj.map_data.set_icon_marker(place, place.categories[c].icon,iconSize);
                
                break;
              }
            }
          }
        } else {

          map_obj.map_data.set_icon_marker(place, place.location.icon,iconSize);
          
        }
      }
    }

    // content += '<div id="wpgmp_pagination"></div>';

    if (map_obj.map_data.map_data.map_options.advance_template === true) {
      var $listingElement = $(document).find(
        '[data-listing-content="true"][data-wpgmp-map-id]'
      );
      var allClasses = $listingElement.attr("class").split(" ");
      listing_container = "." + allClasses[0];
      $(listing_container).html(content);
    } else {
      content =
        '<div class="fc-' +
        map_obj.map_data.map_data.listing.list_item_skin.type +
        "-" +
        map_obj.map_data.map_data.listing.list_item_skin.name +
        ' fc-wait"><div data-page="2" class="fc-component-6" data-layout="' +
        map_obj.map_data.map_data.listing.list_item_skin.name +
        '" >' +
        content +
        "</div></div>";
      $(listing_container).find(".wpgmp_categories").html(content);
    }

    $(listing_container)
      .find(".wpgmp_extra_field:contains('wpgmp_empty')")
      .remove();
    $(listing_container).find(".wpgmp_empty").prev().remove();
    $(listing_container).find(".wpgmp_empty").remove();

    if (map_obj.map_data.map_data.listing.list_grid == "wpgmp_listing_grid") {
      $(window).on("load", function () {
        try {
          var container = $(listing_container).find(".wpgmp_listing_grid");
          if (container.length) {
            var msnry = container.data("masonry");
            if (msnry) {
              msnry.destroy();
            }

            container.imagesLoaded(function () {
              // init Masonry after all images have loaded
              container.masonry({
                itemSelector: ".wpgmp_locations",
                columnWidth: ".wpgmp_locations",
                percentPosition: true,
              });

              // Now safe to call reload
              container.masonry("layout");
            });

          }
        } catch (err) {
          console.log(err);
        }
      });
    }
    return false;
  }
  // -----------------------------
  // 📊 Filters & Listings
  // -----------------------------
  apply_filters() {
    var map_obj = this;
    var filters = map_obj.filters;
    var showAll = true;
    var show = true;
    map_obj.show_places = [];
    // Filter by search box.
    if ($.isFunction($.fn.locationSortByDistance)) {
      map_obj.sorted_locations_by_search = [];
    } else {
      if (
        $(map_obj.container).find('[data-input="wpgmp-search-text"]').length >
        0
      ) {
        map_obj.search_term = $(map_obj.container)
          .find('[data-input="wpgmp-search-text"]')
          .val()
          .trim();

        if (map_obj.search_term.length > 0) {
          map_obj.search_term = map_obj.search_term.toLowerCase();
          map_obj.enable_search_term = true;
        }
      }
    }

    if (
      (map_obj.map_data.map_tabs &&
        map_obj.map_data.map_tabs.category_tab &&
        map_obj.map_data.map_tabs.category_tab.cat_tab === true) ||
      $(map_obj.container).find("input[data-marker-category]").length > 0
    ) {
      var all_selected_category_sel = $(map_obj.container).find(
        "input[data-marker-category]:checked"
      );
      var all_selected_category = [];
      var all_not_selected_location = [];
      if (all_selected_category_sel.length > 0) {
        $.each(
          all_selected_category_sel,
          function (index, selected_category) {
            all_selected_category.push(
              $(selected_category).data("marker-category")
            );
            var all_not_selected_location_sel = $(selected_category)
              .closest('[data-container="wpgmp-category-tab-item"]')
              .find("input[data-marker-location]:not(:checked)");
            if (all_not_selected_location_sel.length > 0) {
              $.each(
                all_not_selected_location_sel,
                function (index, not_selected_location) {
                  all_not_selected_location.push(
                    $(not_selected_location).data("marker-location")
                  );
                }
              );
            }
          }
        );
      }
      var all_selected_location_sel = $(map_obj.container)
        .find('[data-container="wpgmp-category-tab-item"]')
        .find("input[data-marker-location]:checked");
      var all_selected_location = [];
      if (all_selected_location_sel.length > 0) {
        $.each(
          all_selected_location_sel,
          function (index, selected_location) {
            all_selected_location.push(
              $(selected_location).data("marker-location")
            );
          }
        );
      }
    }

    if (typeof map_obj.map_data.places != "undefined") {
      $.each(map_obj.map_data.places, function (place_key, place) {
        show = true;
        if (typeof filters != "undefined") {
          $.each(filters, function (filter_key, filter_values) {
            var in_fields = false;

            if ($.isArray(filter_values)) {
              if (
                typeof place.categories != "undefined" &&
                filter_key == "category"
              ) {
                $.each(place.categories, function (cat_index, category) {
                  if ($.inArray(category.id, filter_values) > -1) {
                    in_fields = true;
                  }
                });
              }

              if (typeof place.custom_filters != "undefined") {
                $.each(place.custom_filters, function (k, val) {
                  if (filter_key == k) {
                    in_fields = false;
                    if ($.isArray(val)) {
                      $.each(val, function (index, value) {
                        if ($.inArray(value, filter_values) > -1)
                          in_fields = true;
                      });
                    } else if (val == filter_values.val) in_fields = true;
                  }
                });
              }

              if (typeof place[filter_key] != "undefined") {
                if (
                  $.inArray(place[filter_key].toLowerCase(), filter_values) >
                  -1
                ) {
                  in_fields = true;
                }
              } else if (typeof place.location[filter_key] != "undefined") {
                if (
                  $.inArray(
                    place.location[filter_key].toLowerCase(),
                    filter_values
                  ) > -1
                ) {
                  in_fields = true;
                }
              } else if (
                place.location.extra_fields &&
                typeof place.location.extra_fields[filter_key] != "undefined"
              ) {
                var dropdown_value = filter_values[0];

                if (place.location.extra_fields[filter_key] != "") {
                  var arrayResult =
                    place.location.extra_fields[filter_key].split(",");

                  var trimmedArray = $.map(arrayResult, function (value) {
                    return $.trim(value).toLowerCase();
                  });
                }
                if (place.location.extra_fields[filter_key]) {
                  var dropdown_val =
                    place.location.extra_fields[filter_key].toLowerCase();
                  var dropdown_val_decoded = $("<div>")
                    .html(dropdown_val)
                    .text();
                  if (
                    dropdown_val &&
                    (dropdown_val.indexOf(dropdown_value) > -1 ||
                      dropdown_val_decoded.indexOf(dropdown_value) > -1)
                  ) {
                    in_fields = true;
                  } else if ($.inArray(dropdown_val, filter_values) > -1) {
                    in_fields = true;
                  } else if (
                    trimmedArray != undefined &&
                    $.inArray(dropdown_value, trimmedArray) > -1
                  ) {
                    in_fields = true;
                  } else if (Array.isArray(dropdown_value) && trimmedArray !== undefined && dropdown_value.some(value => trimmedArray.includes(value))) {
                    in_fields = true;
                }
                }
              }

              if (map_obj.addonInfo !== "") {
                $.each(map_obj.addonInfo, function (key, method) {
                  var method_name = method.filtration_logic;

                  if (
                    typeof map_obj[method_name] !== "undefined" &&
                    $.isFunction(map_obj[method_name])
                  ) {
                    in_fields = map_obj[method_name](
                      in_fields,
                      place,
                      filter_values,
                      filter_key
                    );
                  }
                });
              }

              if (in_fields == false) show = false;
            } else {
              filter_values.val = "";
            }
          });
        }

        var search_fields = map_obj.map_data.map_options.search_fields;
        var exclude_fields = map_obj.map_data.map_options.exclude_fields;

        if (Array.isArray(exclude_fields) && exclude_fields.length > 0) {
          if (exclude_fields.includes("{post_title}")) {
            if (!exclude_fields.includes("{post_link}")) {
              exclude_fields.push("{post_link}");
            }
            if (!exclude_fields.includes("{marker_title}")) {
              exclude_fields.push("{marker_title}");
            }
          }

          if (exclude_fields.includes("{post_content}")) {
            if (!exclude_fields.includes("{post_excerpt}")) {
              exclude_fields.push("{post_excerpt}");
            }
            if (!exclude_fields.includes("{marker_message}")) {
              exclude_fields.push("{marker_message}");
            }
          }

          if (
            exclude_fields.includes("{post_excerpt}") &&
            !exclude_fields.includes("{post_content}")
          ) {
            exclude_fields.push("{post_content}");
          }

          if (
            exclude_fields.includes("{marker_address}") &&
            !exclude_fields.includes("{%_wpgmp_location_address%}")
          ) {
            exclude_fields.push("{%_wpgmp_location_address%}");
          }

          if (
            exclude_fields.includes("{marker_city}") &&
            !exclude_fields.includes("{%_wpgmp_location_city%}")
          ) {
            exclude_fields.push("{%_wpgmp_location_city%}");
          }

          if (
            exclude_fields.includes("{marker_country}") &&
            !exclude_fields.includes("{%_wpgmp_location_country%}")
          ) {
            exclude_fields.push("{%_wpgmp_location_country%}");
          }

          if (
            exclude_fields.includes("{marker_state}") &&
            !exclude_fields.includes("{%_wpgmp_location_state%}")
          ) {
            exclude_fields.push("{%_wpgmp_location_state%}");
          }

          if (
            exclude_fields.includes("{marker_longitude}") &&
            !exclude_fields.includes("{%_wpgmp_metabox_longitude%}")
          ) {
            exclude_fields.push("{%_wpgmp_metabox_longitude%}");
          }

          if (
            exclude_fields.includes("{marker_latitude}") &&
            !exclude_fields.includes("{%_wpgmp_metabox_latitude%}")
          ) {
            exclude_fields.push("{%_wpgmp_metabox_latitude%}");
          }
        }
        var extra_fields_array = place.location.extra_fields;
        var flag_search = false;
        var flag_exclude = false;

        var search_exclude = {};
        if (
          typeof exclude_fields !== "undefined" &&
          exclude_fields.length > 0
        ) {
          flag_exclude = true;
          search_exclude = Object.keys(extra_fields_array)
            .filter((key) => !exclude_fields.includes(`{${key}}`))
            .reduce((obj, key) => {
              obj[key] = extra_fields_array[key];
              return obj;
            }, {});
        }
        //search with only these field
        var search_fields_present = [];
        if (
          typeof search_fields !== "undefined" &&
          search_fields.length > 0
        ) {
          flag_search = true;
          for (var i = 0; i < search_fields.length; i++) {
            if (
              extra_fields_array.hasOwnProperty(search_fields[i].slice(1, -1))
            ) {
              search_fields_present.push(search_fields[i]);
            }
          }
        }

        //Apply Search Filter.
        var search_term = map_obj.search_term;
        if (search_term && search_term.length > 0) {
          if (map_obj.enable_search_term === true && show === true) {
            if (flag_search == true || flag_exclude == true) {
              if (
                place.title != undefined &&
                place.title.toLowerCase().indexOf(search_term) >= 0 &&
                ((flag_search && search_fields.includes("{marker_title}")) ||
                  (flag_exclude &&
                    !exclude_fields.includes("{marker_title}")))
              ) {
                show = true;
              } else if (
                place.content != undefined &&
                place.content.toLowerCase().indexOf(search_term) >= 0 &&
                ((flag_search == true &&
                  search_fields.includes("{marker_message}")) ||
                  (flag_exclude == true &&
                    !exclude_fields.includes("{marker_message}")))
              ) {
                show = true;
              } else if (
                String(place.location.lat)
                  .toLowerCase()
                  .indexOf(search_term) >= 0 &&
                ((flag_search == true &&
                  search_fields.includes("{marker_latitude}")) ||
                  (flag_exclude == true &&
                    !exclude_fields.includes("{marker_latitude}")))
              ) {
                show = true;
              } else if (
                String(place.location.lng)
                  .toLowerCase()
                  .indexOf(search_term) >= 0 &&
                ((flag_search == true &&
                  search_fields.includes("{marker_longitude}")) ||
                  (flag_exclude == true &&
                    !exclude_fields.includes("{marker_longitude}")))
              ) {
                show = true;
              } else if (
                place.address &&
                place.address.toString().toLowerCase().indexOf(search_term) >=
                  0 &&
                ((flag_search == true &&
                  search_fields.includes("{marker_address}")) ||
                  (flag_exclude == true &&
                    !exclude_fields.includes("{marker_address}")))
              ) {
                show = true;
              } else if (
                place.location.state &&
                place.location.state.toLowerCase().indexOf(search_term) >=
                  0 &&
                ((flag_search == true &&
                  search_fields.includes("{marker_state}")) ||
                  (flag_exclude == true &&
                    !exclude_fields.includes("{marker_state}")))
              ) {
                show = true;
              } else if (
                place.location.country &&
                place.location.country.toLowerCase().indexOf(search_term) >=
                  0 &&
                ((flag_search == true &&
                  search_fields.includes("{marker_country}")) ||
                  (flag_exclude == true &&
                    !exclude_fields.includes("{marker_country}")))
              ) {
                show = true;
              } else if (
                place.location.postal_code &&
                String(place.location.postal_code)
                  .toLowerCase()
                  .indexOf(search_term) >= 0 &&
                ((flag_search == true &&
                  search_fields.includes("{marker_postal_code}")) ||
                  (flag_exclude == true &&
                    !exclude_fields.includes("{marker_postal_code}")))
              ) {
                show = true;
              } else if (
                place.location.city &&
                place.location.city.toLowerCase().indexOf(search_term) >= 0 &&
                ((flag_search == true &&
                  search_fields.includes("{marker_city}")) ||
                  (flag_exclude == true &&
                    !exclude_fields.includes("{marker_city}")))
              ) {
                show = true;
              } else {
                show = false;
              }

              if (flag_search == true) {
                if (search_fields_present.length > 0) {
                  for (const field of search_fields_present) {
                    if (typeof place.location.extra_fields != "undefined") {
                      $.each(
                        place.location.extra_fields,
                        function (field, value) {
                          if (
                            value &&
                            $.inArray(
                              "{" + field + "}",
                              search_fields_present
                            ) !== -1
                          ) {
                            value = value.toString();
                            if (
                              value &&
                              value.toLowerCase().indexOf(search_term) >= 0
                            )
                              show = true;
                          }
                        }
                      );
                    }
                  }
                }
              } else if (flag_exclude == true) {
                for (var field in search_exclude) {
                  var value = search_exclude[field];

                  if (value) {
                    value = value.toString();

                    if (
                      value &&
                      value.toLowerCase().indexOf(search_term) >= 0
                    ) {
                      show = true;
                    }
                  }
                }
              }
            } else {
              if (
                place.title != undefined &&
                place.title.toLowerCase().indexOf(search_term) >= 0
              ) {
                show = true;
              } else if (
                place.content != undefined &&
                place.content.toLowerCase().indexOf(search_term) >= 0
              ) {
                show = true;
              } else if (
                String(place.location.lat)
                  .toLowerCase()
                  .indexOf(search_term) >= 0
              ) {
                show = true;
              } else if (
                String(place.location.lng)
                  .toLowerCase()
                  .indexOf(search_term) >= 0
              ) {
                show = true;
              } else if (
                place.address &&
                place.address.toString().toLowerCase().indexOf(search_term) >=
                  0
              ) {
                show = true;
              } else if (
                place.location.state &&
                place.location.state.toLowerCase().indexOf(search_term) >= 0
              ) {
                show = true;
              } else if (
                place.location.country &&
                place.location.country.toLowerCase().indexOf(search_term) >= 0
              ) {
                show = true;
              } else if (
                place.location.postal_code &&
                String(place.location.postal_code)
                  .toLowerCase()
                  .indexOf(search_term) >= 0
              ) {
                show = true;
              } else if (
                place.location.city &&
                place.location.city.toLowerCase().indexOf(search_term) >= 0
              ) {
                show = true;
              } else {
                show = false;
              }

              if (typeof place.location.extra_fields != "undefined") {
                $.each(place.location.extra_fields, function (field, value) {
                  if (value) {
                    value = value.toString();
                    if (
                      value &&
                      value.toLowerCase().indexOf(search_term) >= 0
                    )
                      show = true;
                  }
                });
              }
            }
          }
        }

        //Exclude locations without category if location filters are choosed by user
        if (
          (place.categories.length == undefined ||
            place.categories.length == "undefined") &&
          all_selected_category &&
          all_selected_category.length > 0 &&
          $(map_obj.container)
            .find('input[name="wpgmp_select_all"]')
            .is(":checked") == false &&
          show
        ) {
          show = false;
        }

        // if checked category
        if (
          all_selected_category &&
          show != false &&
          place.categories.length != undefined
        ) {
          var in_checked_category = false;

          if (all_selected_category.length === 0) {
            // means no any category selected so show those location without categories.
            if (typeof place.categories != "undefined") {
              $.each(place.categories, function (cat_index, category) {
                if (category.id === "") in_checked_category = true;
              });
            }
          } else {
            if (typeof place.categories != "undefined") {
              $.each(place.categories, function (cat_index, category) {
                if (category.id === "") in_checked_category = true;
                else if (
                  $.inArray(parseInt(category.id), all_selected_category) > -1
                ) {
                  in_checked_category = true;
                  var iconSize = map_obj.getMarkerSize();
                  map_obj.set_icon_marker(place, category.icon,iconSize);
                  
                }
              });
            }
          }

          //Hide unchecked  locations.
          if (all_not_selected_location.length !== 0) {
            if (
              $.inArray(parseInt(place.id), all_not_selected_location) > -1
            ) {
              in_checked_category = false;
            }
          }

          //var checked_categories = $(map_obj.container).find('input[data-marker-category]:checked').length;
          if (in_checked_category === false) show = false;
          else show = true;

          //Show Here checked location.
          if (all_selected_location.length !== 0) {
            if ($.inArray(parseInt(place.id), all_selected_location) > -1) {
              show = true;
            }
          }
        }
        
        if( show == true ) {
          place.marker.bindMap(map_obj.map);
        }
        place.marker.setVisible(show);

        if (show == false) {
          place.infowindow.close();
        }
        place.marker.setAnimation(null);
        if ($.isFunction($.fn.locationSortByDistance)) {
          if (show === true) {
            map_obj.show_places.push(place);
            map_obj.sorted_locations_by_search.push(place);
          }
        } else {
          if (show === true) map_obj.show_places.push(place);
        }
      });

      if ($.isFunction($.fn.locationSortByDistance)) {
        if (map_obj.search_place != undefined) {
          map_obj.is_place_changed = true;
          var sotred_locs = map_obj.shuffle_by_distance(
            map_obj.sorted_locations_by_search,
            map_obj.search_place
          );
          map_obj.places = sotred_locs;
        }
      }
    }

    if (
      typeof map_obj.map_data.map_options.bound_map_after_filter !== 'undefined' &&
      map_obj.map_data.map_options.bound_map_after_filter === true
    ) {
      const isGoogle = map_obj.isMapProvider("google");

      // Create a bounds object based on provider
      const bounds = isGoogle
        ? new google.maps.LatLngBounds()
        : new L.LatLngBounds();

      if (Array.isArray(map_obj.show_places) && map_obj.show_places.length > 0) {
        map_obj.show_places.forEach((place) => {
          const lat = parseFloat(place.location.lat);
          const lng = parseFloat(place.location.lng);

          if (!isNaN(lat) && !isNaN(lng)) {
            const latLng = isGoogle
              ? new google.maps.LatLng(lat, lng)
              : L.latLng(lat, lng);

            bounds.extend(latLng);
          }
        });
        // Fit bounds to map
        if (isGoogle) {
          map_obj.map.fitBounds(bounds);
        } else {
          map_obj.map.fitBounds(bounds, { padding: [20, 20] }); // optional padding
        }
      }
    }


    if (map_obj.map_data.listing) {
      if ($(map_obj.container).find('[data-filter="map-sorting"]').val()) {
        var order_data = $(map_obj.container)
          .find('[data-filter="map-sorting"]')
          .val()
          .split("__");
        var data_type = "";
        if (order_data[0] !== "" && order_data[1] !== "") {
          if (typeof order_data[2] != "undefined") {
            data_type = order_data[2];
          }
          map_obj.sorting(order_data[0], order_data[1], data_type);
        }
      } else {
        if (
          map_obj.map_data.listing.default_sorting &&
          map_obj.map_data.autosort
        ) {
          var data_type = "";
          if (
            map_obj.map_data.listing.default_sorting.orderby == "listorder"
          ) {
            data_type = "num";
          }
          map_obj.sorting(
            map_obj.map_data.listing.default_sorting.orderby,
            map_obj.map_data.listing.default_sorting.inorder,
            data_type
          );
        }
      }

      map_obj.update_places_listing();
    }

    if (map_obj.map_data.marker_cluster) {
      setTimeout(function () {
        map_obj.set_marker_cluster();
      }, 2000);
    }
  }
  update_filters() {
    var map_obj = this;
    map_obj.filters = {};
    var all_dropdowns = $(map_obj.container).find('[data-filter="dropdown"]');
    var all_checkboxes = $(map_obj.container).find(
      '[data-filter="checklist"]:checked'
    );
    var all_list = $(map_obj.container).find(
      '[data-filter="list"].fc_selected'
    );
    const map_id = map_obj.map_data.map_property.map_id;
    $.each(all_dropdowns, function (index, element) {
      if ($(this).val() != "") {
        if (typeof map_obj.filters[$(this).data("name")] == "undefined") {
          map_obj.filters[$(this).data("name")] = [];
        }

        map_obj.filters[$(this).data("name")].push($(this).val());
      }
    });

    $.each(all_checkboxes, function (index, element) {
      if (typeof map_obj.filters[$(this).data("name")] == "undefined") {
        map_obj.filters[$(this).data("name")] = [];
      }

      map_obj.filters[$(this).data("name")].push($(this).val());
    });

    $.each(all_list, function (index, element) {
      if (typeof map_obj.filters[$(this).data("name")] == "undefined") {
        map_obj.filters[$(this).data("name")] = [];
      }

      map_obj.filters[$(this).data("name")].push(
        $(this).data("value").toString()
      );
    });

    $('[data-wpgmp-filter="true"][data-wpgmp-map-id="' + map_id + '"]').each(
      function () {
        const filterBy = $(this).data("wpgmp-filter-by");
        const inputType = $(this).prop("type").toLowerCase();
        let inputValue;
        if (inputType === "checkbox" && $(this).is(":checked")) {
          inputValue = $(this).val();
        } else if (inputType === "select-one" || inputType === "select") {
          inputValue = $(this).find("option:selected").val();
        } else if (inputType === "radio" && $(this).is(":checked")) {
          inputValue = $(this).val();
        }

        if (filterBy == "search" && $(this).val().length > 2) {
          map_obj.search_term = $(this).val().toLowerCase();
          map_obj.enable_search_term = true;
        } else if (filterBy == "search" && $(this).val().length < 2) {
          map_obj.search_term = "";
          map_obj.enable_search_term = true;
        }

        if (inputValue !== undefined && inputValue !== "") {
          map_obj.filters[filterBy] = map_obj.filters[filterBy] || [];
          map_obj.filters[filterBy].push(inputValue);
        }
      }
    );

    if (map_obj.addonInfo !== "") {
      $.each(map_obj.addonInfo, function (key, method) {
        var method_name = method.filter_register_mthod;

        if (
          typeof map_obj[method_name] !== "undefined" &&
          $.isFunction(map_obj[method_name])
        ) {
          map_obj[method_name]();
        }
      });
    }

    this.apply_filters();
  }
  
  register_events() {
    const map_obj = this;
    const map_id = map_obj.map_data.map_property.map_id;
    const $container = $(map_obj.container);
    const template = map_obj.map_data.map_options.template_name;
    const hasSearchButton = !!$(`[data-wpgmp-filter="true"][data-wpgmp-search-button="true"]`).length;
    
    $container.on("input change", ".wpgmp_error", function () {
      $(this).removeClass("wpgmp_error");
  });
  
    // -------------------------------
    // 🔁 Filters (dropdown, checklist, list, radius, per-page)
    // -------------------------------
    
     // Accordion tab toggle UI (for listings/filters etc.)
     $("body").on("click", ".fc-accordion-tab", function () {
      if ($(this).hasClass("active")) {
        $(this).removeClass("active");
  
        var acc_child = $(this).next().removeClass("active");
      } else {
        $(".fc-accordion-tab").removeClass("active");
  
        $(".fc-accordion dd").removeClass("active");
  
        $(this).addClass("active");
  
        var acc_child = $(this).next().addClass("active");
      }
    });

    if (map_obj.map_data.listing) {
      // Hook for dynamic taxonomy filters
      $.each(map_obj.map_data.listing.filters, function (key, filter) {
        $(map_obj.container)
          .find('select[name="' + filter + '"]')
          .on("change", function () {
            map_obj.update_filters();
          });
      });
    }

    $container.on("change", `select[data-wpgmp-filter="true"][data-wpgmp-map-id="${map_id}"]`, () => {
      map_obj.update_filters();
    });
    
    $container.on('click', '.categories_filter_reset_btn', function () {
      $(map_obj.container).find('.wpgmp_filter_wrappers select').each(function () {
          $(this).find('option:first').attr('selected', 'selected');
          $(this).find('option:first').prop('selected', 'selected');
      });

      $('.wpgmp_search_input').val('');

      if (map_obj.addonInfo !== '') {

          $.each(map_obj.addonInfo, function (key, method) {
              var method_name = method.filter_reset_method;

              if (typeof map_obj[method_name] !== 'undefined' && $.isFunction(map_obj[method_name])) {

                  map_obj[method_name]();
              }

          });

      }
      map_obj.update_filters();

  });
  
  $(map_obj.container).on("click", '[data-filter="dropdown"]', () => map_obj.update_filters());
  $container.on("click", '[data-filter="checklist"]', () => map_obj.update_filters());
  $container.on("click", '[data-filter="list"]', function () {
      $(this).toggleClass("fc_selected");
      map_obj.update_filters();
    });

    // Marker link scroll-to functionality
    $container.on("click", ".wpgmp_marker_link", function () {
      const markerId = $(this).data("marker");
      const source = $(this).data("source");
      $("html, body").animate(
        { scrollTop: $(map_obj.container).offset().top - 150 },
        500
      );
      map_obj.open_infowindow(markerId, source);
    });
  
    if (!hasSearchButton && (template !== 'layout_6' && template !== 'layout_7')) {
      $container.on("keyup", `input[data-wpgmp-filter="true"][data-wpgmp-map-id="${map_id}"]`, () => {
        map_obj.update_filters();
      });
    }
  
    // Reset all filters (button with reset attribute)
    $container.on("click", '[data-filter-reset="true"]', () => {
      $('input[type="text"][data-wpgmp-filter="true"]').val('');
      $('select[data-wpgmp-filter="true"]').prop("selectedIndex", 0);
      $('input[type="checkbox"][data-filter="checklist"], input[type="checkbox"][data-wpgmp-filter="true"]').prop("checked", false);
      $('[data-filter="list"].fc_selected').removeClass("fc_selected");
      $('input[type="radio"][data-wpgmp-filter="true"]').prop("checked", false);
      map_obj.update_filters();
    });
  
    // Advanced template → search button click triggers filtering
    $container.on("click", `[data-wpgmp-filter="true"][data-wpgmp-search-button="true"]`, () => {
      map_obj.update_filters();
    });

    // -------------------------------
    // 🔍 Search Input
    // -------------------------------
    $container.on("keyup", '[data-input="wpgmp-search-text"]', function () {
      const searchVal = $(this).val().trim();
      $container.find('[data-filter="map-radius"]').val("");
      map_obj.search_area = "";
  
      if (searchVal.length >= 2 && map_obj.map_data.listing.apply_default_radius === true) {

        map_obj.wpgmp_set_search_area(searchVal);

      } else {
        map_obj.update_filters();
      }
    });
  
    // -------------------------------
    // 🔃 Sorting & Per Page
    // -------------------------------
    $container.on("change", `select[data-wpgmp-map-id="${map_id}"][data-filter="map-sorting"]`, function () {
      const [orderby, order, type = ""] = $(this).val().split("__");
      if (orderby && order) {
        map_obj.sorting(orderby, order, type);
        map_obj.update_places_listing();
      }
    });
  
    $container.on("change", `select[data-wpgmp-map-id="${map_id}"][data-filter="map-perpage-location-sorting"]`, function () {
      map_obj.per_page_value = $(this).val();
      map_obj.update_filters();
    });
  
    // -------------------------------
    // 🗺️ Marker & InfoWindow Events
    // -------------------------------
    
    $container.on("click", ".wpgmp_location_container a[data-marker]", function (e) {
      e.preventDefault();
      const markerId = $(this).data("marker");
      if (!markerId || typeof map_obj.open_infowindow !== "function") return;
  
      map_obj.open_infowindow(markerId);
    });
    
    // Listing infowindow (custom event trigger like 'click', 'hover')
    $container.on(map_obj.settings.listing_infowindow_open_event, ".wpgmp_locations a[data-marker]", function () {
      const $el = $(this);
      $("html, body").animate({ scrollTop: $container.offset().top - 150 }, 500);
      setTimeout(() => map_obj.open_infowindow($el.data("marker")), 300);
    });

    $container.on(map_obj.settings.listing_infowindow_open_event, ".wep-card__title a[data-marker]", function () {
      const $el = $(this);
      $("html, body").animate({ scrollTop: $container.offset().top - 150 }, 500);
      setTimeout(() => map_obj.open_infowindow($el.data("marker")), 300);
    });
  
    // -------------------------------
    // 📑 Tabs & UI Toggles
    // -------------------------------
    $container.on("click", ".wpgmp_toggle_container", function () {
      $container.find(".wpgmp_toggle_main_container").slideToggle("slow");
      const txt = $(this).text();
      $(this).text(txt === wpgmp_local.hide ? wpgmp_local.show : wpgmp_local.hide);
    });
  
    $container.on("click", "li[class^='wpgmp-tab-'] a", function () {
      const tabId = $(this).parent().attr("rel");
      $container.find("li[class^='wpgmp-tab-'] a").removeClass("active");
      $(this).addClass("active");
      $container.find(".wpgmp_toggle_main_container div[id^='wpgmp_tab_']").hide();
      $container.find(`#wpgmp_tab_${tabId}`).show();
    });

    $container.find(".wpgmp_find_nearby_button").click(function () {
      const target = $(this).closest(".wpgmp_nearby_container");
      const inputField = $(target).find(".wpgmp_auto_suggest");
      const lat = inputField.data('latitude');
      const lon = inputField.data('longitude');
  
      const radiusField = $(map_obj.container).find("input[name='wpgmp_radius']");
      const radius = radiusField.val();
  
      let hasError = false;
  
      // Reset previous error classes
      inputField.removeClass("wpgmp_error");
      radiusField.removeClass("wpgmp_error");
  
      // Validate location
      if (!lat || !lon) {
          inputField.addClass("wpgmp_error").focus();
          hasError = true;
      }
  
      // Validate radius
      if (!radius || isNaN(radius) || parseInt(radius) <= 0) {
          radiusField.addClass("wpgmp_error");
          hasError = true;
      }
  
      if (hasError) {
          return; // Stop execution
      }
  
      const dim = $(map_obj.container).find("select[name='wpgmp_route_dimension']").val();
      const amenities = $(map_obj.container).find('input[name^="wpgmp_place_types"]:checked');
      const divide_by = (dim === 'miles') ? 1.60934 : 1;
      const circle_radius_meters = parseInt(radius) * divide_by * 1000;
  
      // Clear existing amenities
      if (map_obj.current_amenities?.length > 0) {
          map_obj.current_amenities.forEach((amenity) => {
              if (amenity.marker?.setMap) {
                  amenity.marker.setMap(null);
              }
          });
      }
  
      map_obj.current_amenities = [];
      map_obj.amenity_infowindow = map_obj.infowindow_marker;
  
      const place_types = amenities.map(function () {
          return $(this).val();
      }).get();
  
      place_types.forEach(function (placeType) {
          const request = {
              location: new google.maps.LatLng(lat, lon),
              radius: circle_radius_meters,
              types: [placeType],
              rankBy: "DISTANCE"
          };
  
          map_obj.fetchNearbyAmenities(request, function (places) {
              places.forEach(function (place) {
                  const marker = map_obj.createMarker(place);
                  map_obj.current_amenities.push({ marker, place });
              });
          });
      });
  
      // Center map and draw circle
      const centerLatLng = new google.maps.LatLng(lat, lon);
      map_obj.map.setCenter(centerLatLng);
  
      if (map_obj.map_data.map_tabs.nearby_tab?.show_nearby_circle) {
          if (map_obj.set_nearbycenter_circle) {
              map_obj.set_nearbycenter_circle.setMap(null);
          }
  
          map_obj.set_nearbycenter_circle = new google.maps.Circle({
              map: map_obj.map,
              fillColor: map_obj.map_data.map_tabs.nearby_tab.nearby_circle_fillcolor,
              fillOpacity: map_obj.map_data.map_tabs.nearby_tab.nearby_circle_fillopacity,
              strokeColor: map_obj.map_data.map_tabs.nearby_tab.nearby_circle_strokecolor,
              strokeOpacity: map_obj.map_data.map_tabs.nearby_tab.nearby_circle_strokeopacity,
              strokeWeight: map_obj.map_data.map_tabs.nearby_tab.nearby_circle_strokeweight,
              center: centerLatLng,
              radius: circle_radius_meters
          });
  
          map_obj.map.setZoom(parseInt(map_obj.map_data.map_tabs.nearby_tab.nearby_circle_zoom));
      }
  });
  
  
  
    // -------------------------------
    // 📦 Category / Checkbox
    // -------------------------------
    $container.on("change", "input[data-marker-category]", function () {
      const $el = $(this), isChecked = $el.is(":checked");
      const catId = $el.data("marker-category");
      const $wrap = $el.closest('[data-container="wpgmp-category-tab-item"]');
  
      map_obj.last_category_chkbox_action = isChecked ? "checked" : "unchecked";
      if (isChecked) {
        map_obj.last_selected_cat_id = catId;
        $wrap.find("input[data-marker-location]").prop("checked", true);
      } else {
        map_obj.last_remove_cat_id = catId;
        $wrap.find("input[data-marker-location]").prop("checked", false);
      }
  
      const childCats = $el.data("child-cats");
      if (childCats) {
        childCats.toString().split(",").forEach(cat => {
          const $child = $el.closest(".wpgmp_tab_item").find(`[data-marker-category="${cat}"]`);
          $child.prop("checked", isChecked);
          $child.closest(".wpgmp_tab_item").find("input[data-marker-location]").prop("checked", isChecked);
        });
      }
  
      map_obj.update_filters();
    });
  
    $container.on("change", "input[data-marker-location]", () => map_obj.update_filters());
  
    // -------------------------------
    // 🖨️ Print Listings
    // -------------------------------
    $container.on("click", '[data-action="wpgmp-print"]', function () {
      const id = $(map_obj.element).attr("id");
      const $default = $(`[data-container="wpgmp-listing-${id}"]`);
      const $custom = $(`[data-container="wpgmp-custom-listing-${id}"]`);
      $default.length && $default.print();
      $custom.length && $custom.print();
    });
  
    // -------------------------------
    // 🔁 Select All Categories
    // -------------------------------
    if (map_obj.map_data.map_tabs?.category_tab?.select_all === true) {
      $container.on("click", 'input[name="wpgmp_select_all"]', function () {
        const isChecked = $(this).is(":checked");
        $container.find("input[data-marker-category], input[data-marker-location]").prop("checked", isChecked);
        map_obj.update_filters();
      });
    }
  
    // -------------------------------
    // 🔍 View Details
    // -------------------------------
    $container.on("click", "[data-view-details]", function (e) {
      e.preventDefault();
      const id = $(this).data("view-details");
      const target = $(this).attr("target");
  
      map_obj.places.forEach(place => {
        if (place.id == id) {
          if (place.source === "post") {
            const url = place.location.extra_fields.post_link;
            target === "_blank" ? window.open(url, "_blank") : location.href = url;
          } else {
            $("html, body").animate({ scrollTop: $container.offset().top - 150 }, 500);
            map_obj.open_infowindow(place.id);
          }
        }
      });
    });
  
    // -------------------------------
    // 📍 Locate Me Button
    // -------------------------------
    $container.on("click", `[data-wpgmp='mcurrent_location'][data-wpgmp-map-id="${map_id}"]`, function () {
      const $this = $(this);
      const $input = $this.parent().find(".wpgmp_auto_suggest");

      map_obj.get_current_location(
        function (loc) {
          const lat = map_obj.isMapProvider("google") ? loc.lat() : loc.lat;
          const lng = map_obj.isMapProvider("google") ? loc.lng() : loc.lng;
  
          $input.data("latitude", lat);
          $input.data("longitude", lng);
  
          map_obj.wpgmp_geocode(lat, lng).then((result) => {
            $input.val(result.address);
          }).catch(console.error);
        },
        function () {}
      );

    });

  
    // -------------------------------
    // 📍 Route Toggle Handling
    // -------------------------------
    $container.find(".wpgmp_specific_route_item").prop("checked", true);
  
    $container.on("change", `[data-wpgmp='route_toggle'][data-wpgmp-map-id="${map_id}"]`, function () {
      const route = map_obj.route_directions[$(this).val()];
      if (route) route.setMap($(this).is(":checked") ? map_obj.map : null);
    });

    // -------------------------------
    // 📍 Drawing Events
    // --
    $('.wpgmp-shape-delete').click(function (e) {
      e.preventDefault();

      map_obj.deleteSelectedShape();
      $('.temp_row').addClass('hiderow');
  });

  $('select[name="shape_stroke_opacity"]').change(function () {
      map_obj.set_shapes_options(map_obj.selectedShape);
  });
  $('select[name="shape_stroke_weight"]').change(function () {
      map_obj.set_shapes_options(map_obj.selectedShape);
  });

  $('select[name="shape_fill_opacity"]').change(function () {
      map_obj.set_shapes_options(map_obj.selectedShape);
  });
 

  if (typeof map_obj.map_data.shapes != 'undefined') {
      if (map_obj.map_data.shapes.drawing_editable === true) {
         
          $('.shape_fill_color').wpColorPicker({
              change: function(event, ui) {
                  var polyOptions2 = {
                      fillColor: $(this).val(),
                  };
                  map_obj.selectedShape.setOptions(polyOptions2);
              }
          });

          $('.shape_stroke_color').wpColorPicker({
              change: function(event, ui) {
                  var polyOptions2 = {
                      strokeColor: $(this).val(),
                  };
                  map_obj.selectedShape.setOptions(polyOptions2);
              }
          });
      }
  }

  $('input[name="shape_click_url"]').change(function () {
      map_obj.set_shapes_options(map_obj.selectedShape);
  });
  $('textarea[name="shape_click_message"]').blur(function () {
      map_obj.set_shapes_options(map_obj.selectedShape);
  });

  $("textarea[name='shape_path']").blur(function () {
      var cordinates = $(this).val().split(' ');
      if (cordinates.length == 1) {
          cordinates = $(this).val().split("\n");
      }
      var path = [];
      $.each(cordinates, function (ind, cordinate) {
          var latlng = cordinate.split(',');
          path.push(new google.maps.LatLng(latlng[0], latlng[1]));
      });
      map_obj.selectedShape.setPath(path);
  });

  $("input[name='shape_radius']").blur(function () {
      var radius = parseFloat($(this).val());
      map_obj.selectedShape.setRadius(radius);
  });

  $("input[name='shape_center']").blur(function () {
      var latlng = $(this).val().split(',');
      map_obj.selectedShape.setCenter(new google.maps.LatLng(parseFloat(latlng[0]), parseFloat(latlng[1])));
  });

  $("input[name='shape_northeast']").blur(function () {
      var ea = $(this).val().split(',');
      var sw = $("input[name='shape_southwest']").val().split(',');

      map_obj.selectedShape.setBounds(new google.maps.LatLngBounds(new google.maps.LatLng(parseFloat(sw[0]), parseFloat(sw[1])), new google.maps.LatLng(parseFloat(ea[0]), parseFloat(ea[1]))));
  });

  $("input[name='shape_southwest']").blur(function () {
      var sw = $(this).val().split(',');
      var ea = $("input[name='shape_northeast']").val().split(',');

      map_obj.selectedShape.setBounds(new google.maps.LatLngBounds(new google.maps.LatLng(parseFloat(sw[0]), parseFloat(sw[1])), new google.maps.LatLng(parseFloat(ea[0]), parseFloat(ea[1]))));
  });

  $("input[name='shape_center']").blur(function () {
      var latlng = $(this).val().split(',');
      map_obj.selectedShape.setCenter(new google.maps.LatLng(parseFloat(latlng[0]), parseFloat(latlng[1])));
  });


  }
  
  create_perpage_option() {
    var map_obj = this;
    var options = "";
    var content = "";

    content +=
      '<select name="map_perpage_location_sorting" data-wpgmp-map-id="'+this.map_data.map_property.map_id+'" data-filter="map-perpage-location-sorting" class="choose_salutation">';
    content +=
      '<option value="' +
      map_obj.map_data.listing.pagination.listing_per_page +
      '">' +
      wpgmp_local.show_locations +
      "</option>";
    content += '<option value="25">25</option>';
    content += '<option value="50">50</option>';
    content += '<option value="100">100</option>';
    content += '<option value="200">200</option>';
    content += '<option value="500">500</option>';
    content +=
      '<option value="' +
      map_obj.show_places.length +
      '">' +
      wpgmp_local.all_location +
      "</option>";
    content += "</select>";

    return content;
  }
  create_sorting() {
    var options = "";

    var content = "";

    if (this.map_data.listing.display_sorting_filter === true) {
      content +=
        '<select name="map_sorting" data-wpgmp-map-id="'+this.map_data.map_property.map_id+'" data-filter="map-sorting"><option value="">' +
        wpgmp_local.sort_by +
        "</option>";
      $.each(this.map_data.listing.sorting_options, function (id, name) {
        content += "<option value='" + id + "'>" + name + "</option>";
      });
      content += "</select>";
    }

    return content;
  }
  create_radius() {
    var options = "";

    var content = "";
    if (this.map_data.listing.display_radius_filter === true) {
      var radius_options = this.map_data.listing.radius_options;

      if (radius_options != undefined) {
        content +=
          '<select data-name="radius"  data-wpgmp-map-id="'+this.map_data.map_property.map_id+'" name="map_radius"><option value="">' +
          wpgmp_local.select_radius +
          "</option>";
        var radius_dimension = this.map_data.listing.radius_dimension;
        $.each(radius_options.split(","), function (id, name) {
          if (radius_dimension == "miles") {
            content +=
              "<option value='" +
              name +
              "'>" +
              name +
              " " +
              wpgmp_local.miles +
              "</option>";
          } else {
            content +=
              "<option value='" +
              name +
              "'>" +
              name +
              " " +
              wpgmp_local.km +
              "</option>";
          }
        });
        content += "</select>";
      }
    }

    return content;
  }
  custom_filters() {
    var map_obj = this;
    var options = "";
    var places = this.map_data.places;
    var wpgmp_filters = this.map_data.filters;
    map_obj.template_data = [];
    this.filter_content = this.display_filters();

    this.filter_position = this.map_data.listing.filters_position;

    if (
      $(this.container).find(".wpgmp_filter_wrappers").length > 0 &&
      map_obj.map_data.map_options.advance_template == false
    )
      $(this.container)
        .find(".wpgmp_filter_wrappers")
        .html(this.filter_content);

    if (
      typeof wpgmp_filters == "undefined" ||
      typeof wpgmp_filters.custom_filters == "undefined" ||
      wpgmp_filters.custom_filters.length == 0
    ) {
      return;
    }

    $.each(
      wpgmp_filters.custom_filters,
      function (template_shortcode, filter_options) {
        var all_filters = [];
        var content = "";
        var filters = {};
        $.each(filter_options, function (filter_type, filter_parameter) {
          $.each(filter_parameter, function (filter_name, filter_label) {
            $.each(places, function (index, place) {
              if (filter_name == "category") {
                if (typeof place.categories == "undefined") {
                  place.categories = {};
                }
                $.each(place.categories, function (cat_index, category) {
                  if (typeof filters[category.type] == "undefined") {
                    filters[category.type] = {};
                  }
                  if (category.name) {
                    filters[category.type][category.name] = category.id;
                  }
                });
              } else {
                if (typeof place[filter_name] != "undefined") {
                  if (typeof filters[filter_name] == "undefined") {
                    filters[filter_name] = {};
                  }
                  if (place[filter_name]) {
                    filters[filter_name][place[filter_name]] =
                      place[filter_name];
                  }
                }

                if (
                  typeof place.location.extra_fields[filter_name] !=
                  "undefined"
                ) {
                  if (typeof filters[filter_name] == "undefined") {
                    filters[filter_name] = {};
                  }
                  if (place.location.extra_fields[filter_name]) {
                    filters[filter_name][
                      place.location.extra_fields[filter_name]
                    ] = place.location.extra_fields[filter_name];
                  }
                }

                if (typeof place.location[filter_name] != "undefined") {
                  if (typeof filters[filter_name] == "undefined") {
                    filters[filter_name] = {};
                  }
                  if (place.location[filter_name]) {
                    filters[filter_name][place.location[filter_name]] =
                      place.location[filter_name];
                  }
                }

                if (
                  typeof place.custom_filters != "undefined" &&
                  typeof place.custom_filters[filter_name] != "undefined"
                ) {
                  if (typeof filters[filter_name] == "undefined") {
                    filters[filter_name] = {};
                  }
                  if (place.custom_filters[filter_name]) {
                    var options = place.custom_filters[filter_name];
                    if ($.isArray(options)) {
                      $.each(options, function (index, value) {
                        filters[filter_name][value] = value;
                      });
                    } else {
                      filters[filter_name][options] = options;
                    }
                  }
                }

                // It could be radius filter.
                if (filter_name == "radius") {
                  if (typeof filters[filter_name] == "undefined") {
                    filters[filter_name] = {};
                  }

                  var radius_options = wpgmp_filters.radius_options;
                  var radius_dimension = wpgmp_filters.radius_dimension;
                  $.each(radius_options.split(","), function (id, name) {
                    if (radius_dimension == "miles") {
                      filters[filter_name][name + " " + wpgmp_local.miles] =
                        name;
                    } else {
                      filters[filter_name][name + " " + wpgmp_local.km] =
                        name;
                    }
                  });
                }
              }
            });
          });

          if (filter_type == "dropdown") {
            if (typeof filters != "undefined") {
              $.each(filters, function (index, options) {
                if(Object.keys(options).length < 1){
                  return true;
                }
                options = map_obj.sort_object_by_value(options);
                options = map_obj.sort_object_by_unique_values(options);
                options = map_obj.sort_numeric_value_filters(options);
                content +=
                  '<select data-filter="dropdown"  name="place_' +
                  index +
                  '" data-name = "' +
                  index +
                  '">';
                content +=
                  '<option value="">' +
                  (filter_parameter[index]
                    ? filter_parameter[index]
                    : "Select " + index) +
                  "</option>";
                $.each(options, function (name, value) {
                  var optionlabel = value;
                  value = value.replace("'", "&#39;");
                  value = value.replace('"', "&#34;");
                  if (value != "" && value != null)
                    content +=
                      "<option value='" +
                      value.toLowerCase() +
                      "'>" +
                      optionlabel +
                      "</option>";
                });
                content += "</select>";
              });
            }
          }

          if (filter_type == "checklist") {
            if (typeof filters != "undefined") {
              $.each(filters, function (index, options) {
                options = map_obj.sort_object_by_value(options);
                options = map_obj.sort_object_by_unique_values(options);
                options = map_obj.sort_numeric_value_filters(options);

                content += '<div class="wpgmp_filters_checklist">';
                content +=
                  '<label  data-filter = "place_' +
                  index +
                  '" >' +
                  (filter_parameter[index]
                    ? filter_parameter[index]
                    : "Select " + index) +
                  "</label>";
                content += '<div class="wpgmp_checklist_wrap">';
                $.each(options, function (name, value) {
                  if (value != "" && value != null)
                    content +=
                      "<div class='wpgmp_checkbox_input'><input data-filter='checklist' type='checkbox' data-name = '" +
                      index +
                      "' value='" +
                      value.toLowerCase() +
                      "'><span class='wpgmp_checklist_title'>" +
                      value +
                      "</span></div>";
                });
                content += "</div></div>";
              });
            }
          }

          if (filter_type == "list") {
            if (typeof filters != "undefined") {
              $.each(filters, function (index, options) {
                content += '<div class="wpgmp_filters_list">';
                content +=
                  '<label  data-filter = "place_' +
                  index +
                  '" >' +
                  (wpgmp_filters.custom_filters[index]
                    ? wpgmp_filters.custom_filters[index]
                    : "Select " + index) +
                  "</label><ul>";
                $.each(options, function (name, value) {
                  if (value != "" && value != null)
                    content +=
                      "<li data-filter='list' data-name = '" +
                      index +
                      "' data-value='" +
                      value +
                      "'>" +
                      name +
                      "</li>";
                });
                content += "</ul></div>";
              });
            }
          }
          if (map_obj.map_data.map_options.advance_template) {
            if (!map_obj.template_data) {
              map_obj.template_data = {}; // Ensure template_data exists
            }
            if (!Array.isArray(map_obj.template_data.filter)) {
              map_obj.template_data.filter = []; // Ensure filter is an array
            }
            map_obj.template_data.filter.push(filters);
          }
        });

        //$(map_obj.container).find("."+template_shortcode).html(content);
        if (map_obj.map_data.map_options.advance_template == false) {
          $(map_obj.container)
            .find(wpgmp_filters.filters_container)
            .append(content);
        }
      }
    );

    // now create select boxes
  }
  sort_numeric_value_filters(filter_options) {
    if (!filter_options.some(isNaN)) {
      filter_options.sort(function (a, b) {
        return a - b;
      });
    }
    return filter_options;
  }

  // -----------------------------
  // 📦 Sorting Utilities
  // -----------------------------
  sorting(order_by, in_order, data_type) {
    switch (order_by) {
      case "category":
        if (this.places !== undefined) {
          this.places.sort(this.sortByCategory);
        }
        if (this.show_places !== undefined) {
          this.show_places.sort(this.sortByCategory);
        }
        if (in_order == "desc") {
          this.places.reverse();
          this.show_places.reverse();
        }
        break;

      case "title":
        if (this.map_data.places !== undefined) {
          this.map_data.places.sort(this.sortByTitle);
        }
        if (this.show_places !== undefined) {
          this.show_places.sort(this.sortByTitle);
        }
        if (in_order == "desc") {
          this.map_data.places.reverse();
          this.places.reverse();
          this.show_places.reverse();
        }
        break;

      case "address":
        if (this.map_data.places !== undefined) {
          this.map_data.places.sort(this.sortByAddress);
        }
        if (this.show_places !== undefined) {
          this.show_places.sort(this.sortByAddress);
        }
        if (in_order == "desc") {
          this.places.reverse();
          this.show_places.reverse();
        }
        break;
      default:
        var first_place = this.map_data.places[0];
        if (typeof first_place[order_by] != "undefined") {
          this.map_data.places.sort(this.sortByPlace(order_by, data_type));
          this.show_places.sort(this.sortByPlace(order_by, data_type));
        } else if (typeof first_place.location[order_by] != "undefined") {
          this.map_data.places.sort(this.sortByLocation(order_by, data_type));
          this.show_places.sort(this.sortByLocation(order_by, data_type));
        } else if (
          typeof first_place.location.extra_fields[order_by] != "undefined"
        ) {
          this.map_data.places.sort(
            this.sortByExtraFields(order_by, data_type)
          );
          this.show_places.sort(this.sortByExtraFields(order_by, data_type));
        }

        if (in_order == "desc") {
          this.places.reverse();
          this.show_places.reverse();
        }
    }
  }
  sorting_inside_tabs(data, in_order) {
    if (in_order == "asc") {
      data.sort();
    }
    if (in_order == "desc") {
      data.sort();
      data.reverse();
    }
    return data;
  }
  sortByTitle(a, b) {
    var a_val = a.title.toLowerCase();
    var b_val = b.title.toLowerCase();
    return a_val < b_val ? -1 : a_val > b_val ? 1 : 0;
  }

  sortByValue(a, b) {
    var a_val = a.toLowerCase();
    var b_val = b.toLowerCase();
    return a_val < b_val ? -1 : a_val > b_val ? 1 : 0;
  }
  sortByCategory(a, b) {
    if (b.categories[0] && a.categories[0]) {
      if (a.categories[0].name && b.categories[0].name) {
        var a_val = a.categories[0].name.toLowerCase();
        var b_val = b.categories[0].name.toLowerCase();
        return a_val < b_val ? -1 : a_val > b_val ? 1 : 0;
      }
    }
  }
  sortByAddress(a, b) {
    var a_val = a.address.toLowerCase();
    var b_val = b.address.toLowerCase();
    return a_val < b_val ? -1 : a_val > b_val ? 1 : 0;
  }
  sortByPlace(order_by, data_type) {
    return function (a, b) {
      if (b[order_by] && a[order_by]) {
        if (a[order_by] && b[order_by]) {
          var a_val = a[order_by].toLowerCase();
          var b_val = b[order_by].toLowerCase();
          if (data_type == "num") {
            a_val = parseInt(a_val);
            b_val = parseInt(b_val);
          }
          return a_val < b_val ? -1 : a_val > b_val ? 1 : 0;
        }
      }
    };
  }
  sortByExtraFields(order_by, data_type) {
    return function (a, b) {
      if (
        typeof b.location.extra_fields[order_by] != "undefined" &&
        typeof a.location.extra_fields[order_by] != "undefined"
      ) {
        if (b.location.extra_fields[order_by] == null) {
          b.location.extra_fields[order_by] = "";
        }

        if (a.location.extra_fields[order_by] == null) {
          a.location.extra_fields[order_by] = "";
        }

        if (data_type == "num") {
          var a_val = parseInt(a.location.extra_fields[order_by]);
          var b_val = parseInt(b.location.extra_fields[order_by]);
        } else {
          var a_val = a.location.extra_fields[order_by].toLowerCase();
          var b_val = b.location.extra_fields[order_by].toLowerCase();
        }

        return a_val < b_val ? -1 : a_val > b_val ? 1 : 0;
      }
    };
  }
  sortByLocation(order_by, data_type) {
    return function (a, b) {
      if (b.location[order_by] && a.location[order_by]) {
        if (a.location[order_by] && b.location[order_by]) {
          var a_val = a.location[order_by].toLowerCase();
          var b_val = b.location[order_by].toLowerCase();
          if (data_type == "num") {
            a_val = parseInt(a_val);
            b_val = parseInt(b_val);
          }
          return a_val < b_val ? -1 : a_val > b_val ? 1 : 0;
        }
      }
    };
  }
  sort_object_by_keyvalue(options, by, type, in_order) {
    var sortable = [];
    for (var key in options) {
      sortable.push(options[key]);
    }

    sortable.sort(this.sortByPlace(by, type));

    if (in_order == "desc") {
      sortable.reverse();
    }

    return sortable;
  }
  sort_object_by_unique_values(options) {
    var new_options = [];
    var uniqueNames = [];
    for (var key in options) {
      if (options[key].indexOf(",") > -1) {
        options[key].split(/\s*,\s*/).forEach(function (single_option_value) {
          new_options.push(single_option_value.trim());
        });
      } else {
        new_options.push(options[key].trim());
      }
    }

    // var new_options = new_options.map(function(item) {
    //     return item.toLowerCase();
    // });

    uniqueNames = new_options.filter(function (item, pos) {
      return new_options.indexOf(item) == pos;
    });

    return uniqueNames.sort();
  }
  sort_object_by_value(options) {
    var sortable = [];
    for (var key in options) {
      sortable.push(key);
    }

    sortable.sort(this.sortByValue);
    var new_options = {};
    for (var i = 0; i < sortable.length; i++) {
      new_options[sortable[i]] = options[sortable[i]];
    }

    return new_options;
  }

  // -----------------------------
  // 📌 Directions & Nearby
  // -----------------------------
  get_user_position() { }

  wpgmp_get_nearby_locations() {}
  wpgmp_within_radius() {}
  wpgmp_set_search_area() {}
  map_widgets() {}
  widget_route_tab() {}
  widget_nearby() {}
  widget_directions() {}
  widget_category() {}
  add_tab() {}
  show_tabs() {}
  create_routes() {}
  wpgmp_draw_route() {}
  wpgmp_find_direction() {}
  wpgmp_direction_steps() {}
  register_direction_tab() {}
  
  wpgmp_sort_distance(obj) {
    var arr = [];
    for (var prop in obj) {
      if (obj.hasOwnProperty(prop)) {
        arr.push({
          key: prop,
          value: obj[prop],
        });
      }
    }
    arr.sort(function (a, b) {
      return a.value - b.value;
    });
    return arr;
  }
  // ------------- Location Auto Suggest Control----------------
  // 🧭 Search & Autocomplete
  // -----------------------------
  
 
  async _handleKeyUp(e, inputField, resultsElement) {
    const map_obj = this;
    const isGoogle = map_obj.isMapProvider("google");
    const inputText = inputField.value.trim();
    const wrapper = $(inputField).closest(".wpgmp-autocomplete-wrapper");
    const loader = wrapper.find(".wpgmp-autosuggest-loader");
    loader.show();
    let activeIndex = -1;
  
    // Clear results if empty input
    if (!inputText) {
      resultsElement.innerHTML = "";
      resultsElement.style.display = "none";
      $(".wpgmp-autosuggest-results").hide();
      loader.hide();
      return;
    }
  
    // Utility to render list and bind interactions
    const renderSuggestions = (suggestions, getLabel, onSelect) => {
      resultsElement.innerHTML = "";
      resultsElement.style.display = "block";
      $(".wpgmp-autosuggest-results").show();
      loader.hide();
      suggestions.forEach((item, index) => {
        const li = document.createElement("li");
        li.textContent = getLabel(item);
        li.tabIndex = 0;
        li.dataset.index = index;
  
        const handleSelection = () => {
          onSelect(item);
          resultsElement.innerHTML = "";
          resultsElement.style.display = "none";
          $(".wpgmp-autosuggest-results").hide();
          activeIndex = -1;
        };
  
        li.addEventListener("click", handleSelection);
        li.addEventListener("keypress", (e) => {
          if (e.key === "Enter") handleSelection();
        });
  
        resultsElement.appendChild(li);
      });
  
      // ⌨️ Keyboard support
      inputField.addEventListener("keydown", (event) => {
        const items = resultsElement.querySelectorAll("li");
        if (!items.length) return;
      
        if (event.key === "ArrowDown") {
          event.preventDefault();
          activeIndex = (activeIndex + 1) % items.length;
        } else if (event.key === "ArrowUp") {
          event.preventDefault();
          activeIndex = (activeIndex - 1 + items.length) % items.length;
        } else if (event.key === "Enter" && activeIndex >= 0) {
          event.preventDefault();
          const selectedItem = items[activeIndex];
          selectedItem.dispatchEvent(new Event("click")); // triggers the right one
          return;
        } else if (event.key === "Escape") {
          resultsElement.innerHTML = "";
          resultsElement.style.display = "none";
          $(".wpgmp-autosuggest-results").hide();
          return;
        }
      
        items.forEach((item, i) => {
          const isActive = i === activeIndex;
          item.classList.toggle("active", isActive);
          if (isActive) {
            inputField.value = item.textContent; // 🟢 update input as you move
          }
        });
      });
      
  
      // 🖱️ Close on outside click
      const closeOnOutsideClick = (e) => {
        if (!resultsElement.contains(e.target) && e.target !== inputField) {
          resultsElement.innerHTML = "";
          resultsElement.style.display = "none";
          $(".wpgmp-autosuggest-results").hide();

          document.removeEventListener("click", closeOnOutsideClick);
        }
      };
  
      setTimeout(() => {
        document.addEventListener("click", closeOnOutsideClick);
      }, 0);
    };
  
    // 🌍 Leaflet (Nominatim)
    if (!isGoogle) {
      let baseUrl = "https://nominatim.openstreetmap.org/search";
      const request = {
        q: inputText,
        limit: 10,
        format: "json",
        addressdetails: 1,
      };
  
      if (
        wpgmp_local.wpgmp_country_specific &&
        wpgmp_local.wpgmp_country_specific == true &&
        wpgmp_local.wpgmp_countries &&
        wpgmp_local.wpgmp_countries.length > 0
      ) {
        const country = wpgmp_local.wpgmp_countries.join(",");
        baseUrl += `?countrycodes=${country}`;
      }
      
      try {
        const suggestions = await $.getJSON(baseUrl, request);
  
        renderSuggestions(
          suggestions,
          (item) => item.display_name,
          (selected) => {
            map_obj.wpgmp_handle_place_selection(selected, inputField);
            inputField.value = selected.display_name;
          }
        );
      } catch (error) {
        console.error("Nominatim autocomplete failed:", error);
      }
  
      return;
    }
  
    // 📍 Google Places
    try {
      const {
        Place,
        AutocompleteSessionToken,
        AutocompleteSuggestion,
      } = await google.maps.importLibrary("places");
  
      const token = new AutocompleteSessionToken();
      const request = { input: inputText, sessionToken: token };
  
      if (
        wpgmp_local.wpgmp_country_specific &&
        wpgmp_local.wpgmp_country_specific == true &&
        wpgmp_local.wpgmp_countries &&
        wpgmp_local.wpgmp_countries.length > 0
      ) {
        request.includedRegionCodes = wpgmp_local.wpgmp_countries;
      }
      
      const { suggestions } =
        await AutocompleteSuggestion.fetchAutocompleteSuggestions(request);
  
      renderSuggestions(
        suggestions,
        (item) => item.placePrediction.text.toString(),
        async (selected) => {
          const place = selected.placePrediction.toPlace();
          await place.fetchFields({
            fields: [
              "displayName",
              "formattedAddress",
              "location",
              "viewport",
              "addressComponents",
            ],
          });
  
          map_obj.wpgmp_handle_place_selection(place, inputField);
          inputField.value = place.formattedAddress;
        }
      );
    } catch (error) {
      console.error("Google Places autocomplete failed:", error);
    }
  }
  
  google_auto_suggest(obj) {
    var map_obj = this;

    obj.each(function () {
      var current_input = this;

      if (!$(current_input).parent().hasClass("wpgmp-autocomplete-wrapper")) {
        $(current_input)
          .wrap('<div class="wpgmp-autocomplete-wrapper"></div>')
          .after(' <div class="wpgmp-autosuggest-loader" style="display:none;"></div><ul class="wpgmp-autosuggest-results"></ul>');
      }

      const resultsElement = $(current_input).siblings(".wpgmp-autosuggest-results")[0];

      map_obj.wpgmp_initialise_autosuggest(current_input, resultsElement);
    });
  }

  wpgmp_initialise_autosuggest(inputField, resultsElement) {
      
    $(inputField).on("input", this._debounce((e) => this._handleKeyUp(e,inputField,resultsElement), 500));

  }

  wpgmp_extract_place_data(place) {
    var map_obj = this;
    const data = {
        address: '',
        lat: '',
        lng: '',
        city: '',
        state: '',
        country: '',
        postal: ''
    };

    if (map_obj.isMapProvider('leaflet')) {
        data.address = place.display_name || '';
        data.lat = parseFloat(place.lat);
        data.lng = parseFloat(place.lon);

        const address = place.address || {};
        data.city = address.city || address.county || '';
        data.state = address.state || '';
        data.country = address.country || '';
        data.postal = address.postcode || '';

    } else {
        data.address = place.formattedAddress || '';
        if (place.location) {
            data.lat = place.location.lat();
            data.lng = place.location.lng();
        }

        // Assume `this` refers to your instance with wpgmp_finddata method
        const finder = this.wpgmp_finddata || instance?.wpgmp_finddata;

        if (typeof finder === 'function') {
            data.country = finder.call(this, place, 'country') || '';
            data.state = finder.call(this, place, 'administrative_area_level_1') || '';
            data.city = (
                finder.call(this, place, 'locality') ||
                finder.call(this, place, 'administrative_area_level_2') ||
                finder.call(this, place, 'administrative_area_level_3') || ''
            );
            data.postal = finder.call(this, place, 'postal_code') || '';
        }
    }

    return data;
}

wpgmp_handle_place_selection(place, inputField) {
  const map_obj = this;
  const isGoogle = map_obj.isMapProvider("google");
  const fieldName = $(inputField).attr("name");
  const callback = $(inputField).data("wpgmp-autosuggest-callback");

  if (typeof callback === "function") {
    callback(place);
    return;
  }

  // Extract data from place
  let lat, lng, city, state, country, postal, fullAddress;

  if (isGoogle) {
    lat = place.location.lat();
    lng = place.location.lng();
    city = map_obj.wpgmp_finddata(place, "administrative_area_level_3") ||
           map_obj.wpgmp_finddata(place, "locality");
    state = map_obj.wpgmp_finddata(place, "administrative_area_level_1");
    country = map_obj.wpgmp_finddata(place, "country");
    postal = map_obj.wpgmp_finddata(place, "postal_code");
    fullAddress = place.formattedAddress || $(inputField).val();
  } else {
    lat = parseFloat(place.lat);
    lng = parseFloat(place.lon);
    const address = place.address || {};
    city = address.city || address.county || "";
    state = address.state || "";
    country = address.country || "";
    postal = address.postcode || "";
    fullAddress = place.display_name || $(inputField).val();
  }

  // Apply marker and view
  map_obj.placeMarker(lat, lng);

  // Update relevant fields
  if (fieldName === "location_address") {
    $(".google_latitude").val(lat);
    $(".google_longitude").val(lng);
    $(".google_city").val(city);
    $(".google_state").val(state);
    $(".google_country").val(country);
    $(".google_postal_code").val(postal);

    if (isGoogle && place.viewport) {
      map_obj.map.fitBounds(place.viewport);
    }

  } else if (fieldName === "wpgmp_metabox_location") {
    $("input[name='wpgmp_metabox_latitude']").val(lat);
    $("input[name='wpgmp_metabox_longitude']").val(lng);
    $("input[name='wpgmp_metabox_location_city']").val(city);
    $("input[name='wpgmp_metabox_location_state']").val(state);
    $("input[name='wpgmp_metabox_location_country']").val(country);
    $("input[name='wpgmp_metabox_location_hidden']").val(fullAddress);

    if (isGoogle && place.viewport) {
      map_obj.map.fitBounds(place.viewport);
    }

  } else if (fieldName === "map-search-control") {
    const zoom = parseInt(map_obj.settings.zoom_level_after_search || 10);
    map_obj.map.setZoom(zoom);
    map_obj.map.setCenter(isGoogle ? place.location : [lat, lng]);

  } else {
    // Other pages (frontend search, etc.)
    map_obj.map.setCenter(isGoogle ? place.location : [lat, lng]);

    $(inputField).data("longitude", lng);
    $(inputField).data("latitude", lat);

    if (
      fieldName === "wpgmp_search_input" ||
      $(inputField).data("wpgmp-filter-by") === "search"
    ) {
      if (map_obj.map_data.listing) {
        map_obj.search_area = isGoogle ? place.location : [lat, lng];
      }
      map_obj.update_filters();
    }

    if (typeof map_obj.display_circle_when_searched === "function") {
      map_obj.display_circle_when_searched(place, inputField);
    }
  }
}

  // ------------- Ended Location Auto Suggest Control----------------


  get_current_location(success_func, error_func) {
    const map_obj = this;
  
    if (typeof map_obj.user_location === "undefined") {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          const lat = position.coords.latitude;
          const lng = position.coords.longitude;
  
          map_obj.user_location = map_obj.isMapProvider("google")
            ? new google.maps.LatLng(lat, lng)
            : L.latLng(lat, lng);
  
          if (success_func) success_func(map_obj.user_location);
        },
        (err) => {
          console.warn("Geolocation error:", err);
  
          map_obj.user_location = map_obj.isMapProvider("google")
            ? map_obj.map.getCenter()
            : map_obj.map.getCenter(); // both APIs support getCenter()
  
          if (error_func) error_func(map_obj.user_location);
        },
        {
          enableHighAccuracy: true,
          timeout: 50000,
          maximumAge: 0,
        }
      );
    } else {
      if (success_func) success_func(map_obj.user_location);
    }
  }

  wpgmp_geocode_address(address) {
    const map_obj = this;
    const isGoogle = map_obj.isMapProvider("google");
  
    return new Promise((resolve) => {
      if (!address || address.trim().length < 2) {
        resolve(null);
        return;
      }
  
      const countries = map_obj.settings.country_codes || [];
      const restrictCountries = map_obj.settings.country_specific === true;
  
      if (isGoogle) {
        const request = { address };
        if (restrictCountries && countries.length > 0) {
          request.componentRestrictions = {
            country: countries.join(","),
          };
        }
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode(request, (results, status) => {
          if (status === google.maps.GeocoderStatus.OK && results[0]) {
            const result = results[0];
            const location = result.geometry.location;
            const components = result.address_components;
  
            const extract = (type) =>
              components.find((c) => c.types.includes(type))?.long_name || "";
  
            resolve({
              address: result.formatted_address,
              city:
                extract("locality") || extract("administrative_area_level_3"),
              state: extract("administrative_area_level_1"),
              country: extract("country"),
              postal: extract("postal_code"),
              lat: location.lat(),
              lng: location.lng(),
            });
          } else {
            resolve(null);
          }
        });
      } else {
        const nominatimUrl = "https://nominatim.openstreetmap.org/search";
        const params = {
          q: address,
          format: "json",
          limit: 1,
          addressdetails: 1,
        };
  
        if (restrictCountries && countries.length > 0) {
          params.countrycodes = countries.join(",");
        }
  
        $.getJSON(nominatimUrl, params)
          .done((results) => {
            if (results && results.length > 0) {
              const r = results[0];
              const a = r.address || {};
  
              resolve({
                address: r.display_name,
                city: a.city || a.county || a.town || a.village || "",
                state: a.state || "",
                country: a.country || "",
                postal: a.postcode || "",
                lat: parseFloat(r.lat),
                lng: parseFloat(r.lon),
              });
            } else {
              resolve(null);
            }
          })
          .fail(() => resolve(null));
      }
    });
  }
  
  
  wpgmp_geocode(lat, lng) {
    var map_obj = this;
    
    return new Promise((resolve, reject) => {
      if (map_obj.isMapProvider('google') && typeof google !== 'undefined') {
        const geocoder = new google.maps.Geocoder();
        const latlng = new google.maps.LatLng(lat, lng);
  
        geocoder.geocode({ location: latlng }, (results, status) => {
          if (status === google.maps.GeocoderStatus.OK && results[0]) {
            const components = results[0].address_components;
  
            const getComp = (type) => {
              const match = components.find(c => c.types.includes(type));
              return match ? match.long_name : '';
            };
  
            resolve({
              address: results[0].formatted_address,
              latitude: lat,
              longitude: lng,
              city: getComp("locality") || getComp("administrative_area_level_2") || getComp("administrative_area_level_3"),
              state: getComp("administrative_area_level_1"),
              country: getComp("country"),
              postal_code: getComp("postal_code")
            });
          } else {
            reject('Google Geocoding failed: ' + status);
          }
        });
  
      } else if (map_obj.isMapProvider('leaflet')) {
        const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`;
  
        fetch(url)
          .then(res => res.json())
          .then(data => {
            const addr = data.address || {};
            resolve({
              address: data.display_name || '',
              latitude: lat,
              longitude: lng,
              city: addr.city || addr.town || addr.village || addr.county || '',
              state: addr.state || '',
              country: addr.country || '',
              postal_code: addr.postcode || ''
            });
          })
          .catch(err => reject('Leaflet/OSM reverse geocoding failed: ' + err));
      } else {
        reject('Unknown map provider: ' + provider);
      }
    });
  }
  
  show_search_control() {
    const map_obj = this;
    const isGoogle = map_obj.isMapProvider("google");
    const input = $(map_obj.container).find('[data-input="map-search-control"]');
    if (!input) return;
  
    // Create a wrapper and move input inside it
    const wrapper = document.createElement('div');
    wrapper.className = 'wpgmp-search-control-wrapper';
    if (input[0]) {
      wrapper.appendChild(input[0]);
    }
    
  
    if (isGoogle) {
      const positionKey = map_obj.settings.search_control_position?.toUpperCase();
      const position = google.maps.ControlPosition?.[positionKey];
  
      if (position !== undefined && map_obj.map.controls) {
        map_obj.map.controls[position].push(wrapper);
      } else {
        console.warn("Invalid Google search control position:", positionKey);
      }

      // ✅ Initialize autocomplete
      if (typeof map_obj.google_auto_suggest === "function") {
        map_obj.google_auto_suggest(input);
      } else {
        console.warn("google_auto_suggest() is not defined or not a function.");
      }

  
    } else {
      // Create a custom Leaflet control
      const LeafletSearchControl = L.Control.extend({
        options: {
          position: (map_obj.settings.search_control_position || 'topright').toLowerCase(),
        },
        onAdd: function () {
          return wrapper;
        },
      });
  
      map_obj.map.addControl(new LeafletSearchControl());
    }
  }
  

  // -----------------------------
  // 🗂️ Categories
  // -----------------------------
  search_category(
    array,
    cat_id,
    index,
    categories_tab_data,
    child_categories_tab_data
  ) {
    var map_obj = this;
    var flag = true;
    $.each(array, function (k, i) {
      if (k == cat_id) {
        index = i;
        flag = false;
        if (typeof child_categories_tab_data[cat_id] == "undefined") {
          child_categories_tab_data[cat_id] = {};
          child_categories_tab_data[cat_id]["data"] = [];
          child_categories_tab_data[cat_id]["parent_cat"] = i;
          child_categories_tab_data[cat_id]["cat_id"] = cat_id;
          $.each(map_obj.categories, function (k, e) {
            if (e.group_map_id == cat_id) {
              child_categories_tab_data[cat_id]["cat_title"] =
                e.group_map_title;
              child_categories_tab_data[cat_id]["cat_marker_icon"] =
                e.group_marker;
            }
          });
        }
        index = map_obj.search_category(
          map_obj.map_data.map_tabs.category_tab.child_cats,
          i,
          index,
          categories_tab_data,
          child_categories_tab_data
        );
      }
    });
    if (flag == true) {
      if (typeof categories_tab_data[cat_id] == "undefined") {
        categories_tab_data[cat_id] = {};
        categories_tab_data[cat_id]["data"] = [];
        categories_tab_data[cat_id]["cat_id"] = cat_id;
        $.each(map_obj.categories, function (k, e) {
          if (e.group_map_id == cat_id) {
            categories_tab_data[cat_id]["cat_title"] = e.group_map_title;
            categories_tab_data[cat_id]["cat_marker_icon"] = e.group_marker;
          }
        });
      }
    }
    return index;
  }
  display_sub_categories(
    child_categories_tab_data,
    cat_id,
    content,
    padding
  ) {
    var map_obj = this;

    var category_orders = [];
    if (typeof child_categories_tab_data != "undefined") {
      $.each(child_categories_tab_data, function (index, categories) {
        var loc_count = categories.data.length;

        if (typeof child_categories_tab_data != "undefined") {
          $.each(child_categories_tab_data, function (c, ccat) {
            if (ccat.parent_cat == categories.cat_id) {
              loc_count = loc_count + ccat.data.length;
              $.each(child_categories_tab_data, function (cc, cccat) {
                if (cccat.parent_cat == ccat.cat_id) {
                  loc_count = loc_count + cccat.data.length;
                }
              });
            }
          });
        }
        categories.loc_count = loc_count;

        if (map_obj.map_data.map_tabs.category_tab.cat_order_by == "count") {
          category_orders.push(categories.loc_count);
        } else if (
          map_obj.map_data.map_tabs.category_tab.cat_order_by == "category"
        ) {
          if (categories.cat_order) {
            category_orders.push(categories.cat_order);
          } else if (
            !categories.cat_order &&
            map_obj.map_data.map_tabs.category_tab.all_cats[categories.cat_id]
          ) {
            categories.cat_order =
              map_obj.map_data.map_tabs.category_tab.all_cats[
                categories.cat_id
              ].extensions_fields.cat_order;
            category_orders.push(categories.cat_order);
          }
        } else {
          if (categories.cat_title) {
            category_orders.push(categories.cat_title);
          } else if (
            !categories.cat_title &&
            map_obj.map_data.map_tabs.category_tab.all_cats[categories.cat_id]
          ) {
            categories.cat_title =
              map_obj.map_data.map_tabs.category_tab.all_cats[
                categories.cat_id
              ].group_map_title;
            category_orders.push(categories.cat_title);
          }
        }
      });
    }
    if (map_obj.map_data.map_tabs.category_tab.cat_order_by == "category") {
      category_orders.sort(function (a, b) {
        return a - b;
      });
    } else if (
      map_obj.map_data.map_tabs.category_tab.cat_order_by == "count"
    ) {
      category_orders.sort(function (a, b) {
        return b - a;
      });
    } else {
      category_orders.sort();
    }
    var ordered_categories = [];
    var check_cats = [];
    $.each(category_orders, function (index, cat_title) {
      $.each(child_categories_tab_data, function (index, categories) {
        var compare_with;
        if (map_obj.map_data.map_tabs.category_tab.cat_order_by == "count") {
          compare_with = categories.loc_count;
        } else if (
          map_obj.map_data.map_tabs.category_tab.cat_order_by == "category"
        ) {
          compare_with = categories.cat_order;
        } else {
          compare_with = categories.cat_title;
        }

        if (
          cat_title == compare_with &&
          $.inArray(categories.cat_id, check_cats) == -1
        ) {
          ordered_categories.push(categories);
          check_cats.push(categories.cat_id);
        }
      });
    });

    $.each(ordered_categories, function (index, child_cat) {
      if (child_cat.parent_cat == cat_id) {
        var category_image = "";

        if (
          !child_cat.cat_title &&
          map_obj.map_data.map_tabs.category_tab.all_cats[child_cat.cat_id]
        ) {
          child_cat.cat_title =
            map_obj.map_data.map_tabs.category_tab.all_cats[
              child_cat.cat_id
            ].group_map_title;
        }

        if (
          !child_cat.cat_marker_icon &&
          map_obj.map_data.map_tabs.category_tab.all_cats[child_cat.cat_id]
        ) {
          child_cat.cat_marker_icon =
            map_obj.map_data.map_tabs.category_tab.all_cats[
              child_cat.cat_id
            ].group_marker;
        }
        if (typeof child_cat.cat_marker_icon != "undefined") {
          category_image =
            '<span class="arrow"><img src="' +
            child_cat.cat_marker_icon +
            '"></span>';
        }
        content +=
          '<div class="wpgmp_tab_item" data-container="wpgmp-category-tab-item" style="padding-left:' +
          padding +
          'px;">';

        if (
          map_obj.map_data.map_tabs.category_tab.parent_cats !== undefined &&
          map_obj.map_data.map_tabs.category_tab.parent_cats[child_cat.cat_id]
        )
          var child_cats_str =
            ' data-child-cats="' +
            map_obj.map_data.map_tabs.category_tab.parent_cats[
              child_cat.cat_id
            ].join(",") +
            '"';
        else var child_cats_str = "";

        content +=
          '<input type="checkbox"' +
          child_cats_str +
          ' data-parent-cat="' +
          cat_id +
          '" data-marker-category="' +
          child_cat.cat_id +
          '" value="' +
          child_cat.cat_id +
          '">';

        var loc_count = child_cat.loc_count;

        $.each(
          map_obj.map_data.map_tabs.category_tab.child_cats,
          function (k, v) {
            if (v == child_cat.cat_id && loc_count == 0) loc_count = "";
          }
        );

        var location_count = "";
        if (
          map_obj.map_data.map_tabs.category_tab.show_count === true &&
          loc_count != ""
        ) {
          location_count = " (" + loc_count + ")";
        } else {
          location_count = "";
        }

        content +=
          '<a href="javascript:void(0);" class="wpgmp_cat_title wpgmp-accordion accordion-close">' +
          child_cat.cat_title +
          location_count +
          category_image +
          "</a>";

        if (map_obj.map_data.map_tabs.category_tab.hide_location !== true) {
          content +=
            '<div class="scroll-pane" style="height: 97px; width:100%;">';
          content += '<ul class="wpgmp_location_container">';

          $.each(child_cat.data, function (name, location) {
            if (location.onclick_action == "marker") {
              content +=
                '<li><input type="checkbox" data-marker-location="' +
                location.cat_location_id +
                '"  value="' +
                location.cat_location_id +
                '" /><a data-marker="' +
                location.cat_location_id +
                '" data-zoom="' +
                location.cat_location_zoom +
                '" href="javascript:void(0);">' +
                location.cat_location_title +
                "</a></li>";
            } else if (location.onclick_action == "post") {
              content +=
                '<li><input type="checkbox" data-marker-location="' +
                location.cat_location_id +
                '"  value="' +
                location.cat_location_id +
                '" /><a href="' +
                location.redirect_permalink +
                '" target="_blank">' +
                location.cat_location_title +
                "</a></li>";
            } else if (location.onclick_action == "custom_link") {
              content +=
                '<li><input type="checkbox" data-marker-location="' +
                location.cat_location_id +
                '"  value="' +
                location.cat_location_id +
                '" /><a href="' +
                location.redirect_custom_link +
                '" target="_blank">' +
                location.cat_location_title +
                "</a></li>";
            }
          });

          content += "</ul>";
          content += "</div>";
        }
        content += "</div>";
        content += map_obj.display_sub_categories(
          child_categories_tab_data,
          child_cat.cat_id,
          "",
          padding + 20
        );
      } else if (index + 1 == child_categories_tab_data.length) return;
    });
    return content;
  }

  // -----------------------------
  // ✏️ Drawing Tools
  // -----------------------------
  enable_drawing() { }
  create_polygon() { }
  create_polyline() { }
  create_circle() { }
  create_rectangle() { }
  deleteSelectedShape() { }
  clearSelection() { }
  setSelection() { }
  event_listener() { }

  // -----------------------------
  // 💾 Shape Management
  // -----------------------------
  get_shapes_options() { }
  set_shapes_options() { }
  wpgmp_save_shapes() { }
  wpgmp_shape_complete() { }
  wpgmp_save_polylines() { }
  wpgmp_save_polygons() { }
  wpgmp_save_circles() { }
  wpgmp_save_rectangles() { }

  // -----------------------------
  // 🧷 Overlays & Layers
  // -----------------------------
  wpgmp_image_type_overlays() { }
  set_kml_layer() { }
  set_marker_cluster() { }
  set_panning_control() { }
  set_visual_refresh() { }
  set_45_imagery() { }
  set_overlay() { }
  set_bicyle_layer() { }
  set_traffic_layer() { }
  set_panoramic_layer() { }
  set_transit_layer() { }
  set_weather_layer() { }
  set_streetview() { }
  
  /**
 * Creates a labeled marker compatible with both Google Maps and Leaflet.
 */
create_labeled_marker(label, title, position) {
  const map_obj = this;
  const isGoogle = map_obj.isMapProvider("google");

  if (isGoogle) {
    
    var iconUrl;
    position = {'lat':position.lat(),'lng':position.lng()};
    if( label.toLowerCase() == 'a') {
      iconUrl = wpgmp_local.place_icon_url+'marker-alpha-1.svg';
    }

    if( label.toLowerCase() == 'b') {
      iconUrl = wpgmp_local.place_icon_url+'marker-alpha-2.svg';
    }

    return map_obj.create_google_marker({
      map: map_obj.map,
      position,
      iconUrl:iconUrl,
      label,
      title,
    });

  } else {
    
    const marker = map_obj.create_leaflet_marker({
      map: map_obj.map,
      position,
      label,
      isDivIcon: true  
     });
    

    return marker;
  }
}
  

  // -----------------------------
  // 🏪 Amenities
  // -----------------------------
  create_amenities_markers() { }

  // -----------------------------
  // 🔧 Utilities
  // -----------------------------
  load_google_fonts(fonts) {
    if (fonts && fonts.length > 0) {
      $.each(fonts, function (k, font) {
        if (font.indexOf(",") >= 0) {
          font = font.split(",");
          font = font[0];
        }
        if (font.indexOf('"') >= 0) {
          font = font.replace('"', "");
          font = font.replace('"', "");
        }
        WebFont.load({
          google: {
            families: [font],
          },
        });
      });
    }
  }
  load_json(url) {
    this.map.data.loadGeoJson(url);
  }
  wpgmp_finddata() { }
  place_info(place_id) {
    var place_obj;

    $.each(this.places, function (index, place) {
      if (parseInt(place.id) == parseInt(place_id)) {
        place_obj = place;
      }
    });

    return place_obj;
  }

  _debounce(fn, delay) {
    let timer = null;
    return function (...args) {
      clearTimeout(timer);
      timer = setTimeout(() => fn.apply(this, args), delay);
    };
  }

  _encodePolyline(coords) {
    let output = '';
    let prevLat = 0, prevLng = 0;
  
    for (const [lat, lng] of coords) {
      const latE5 = Math.round(lat * 1e5);
      const lngE5 = Math.round(lng * 1e5);
  
      const dLat = latE5 - prevLat;
      const dLng = lngE5 - prevLng;
  
      output += encodeSignedNumber(dLat) + encodeSignedNumber(dLng);
  
      prevLat = latE5;
      prevLng = lngE5;
    }
  
    return output;
  
    function encodeSignedNumber(num) {
      let sgnNum = num << 1;
      if (num < 0) {
        sgnNum = ~sgnNum;
      }
      return encodeNumber(sgnNum);
    }
  
    function encodeNumber(num) {
      let encodeString = '';
      while (num >= 0x20) {
        encodeString += String.fromCharCode((0x20 | (num & 0x1f)) + 63);
        num >>= 5;
      }
      encodeString += String.fromCharCode(num + 63);
      return encodeString;
    }
  }

  _decodePolyline(encoded) {
    let index = 0, lat = 0, lng = 0, coordinates = [];
  
    while (index < encoded.length) {
      let result = 1, shift = 0, b;
  
      do {
        b = encoded.charCodeAt(index++) - 63 - 1;
        result += b << shift;
        shift += 5;
      } while (b >= 0x1f);
  
      const deltaLat = (result & 1) ? ~(result >> 1) : (result >> 1);
      lat += deltaLat;
  
      result = 1;
      shift = 0;
  
      do {
        b = encoded.charCodeAt(index++) - 63 - 1;
        result += b << shift;
        shift += 5;
      } while (b >= 0x1f);
  
      const deltaLng = (result & 1) ? ~(result >> 1) : (result >> 1);
      lng += deltaLng;
  
      coordinates.push([lat / 1e5, lng / 1e5]);
    }
  
    return coordinates;
  }

  formatRouteDuration(durationString) {
    const totalSeconds = parseInt(durationString, 10);
  
    const hours = Math.floor(totalSeconds / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);
  
    let formatted = '';
  
    if (hours > 0) {
      formatted += hours + (hours === 1 ? ' hr ' : ' hrs ');
    }
  
    if (minutes > 0 || hours === 0) { // Always show minutes if no hours
      formatted += minutes + ' mins';
    }
  
    return formatted.trim();
  }

  isMapProvider(providerName) {
    const map_obj = this;
    const currentProvider = (map_obj?.map_data?.provider || wpgmp_local?.map_provider || 'google').toLowerCase();
    return currentProvider === providerName.toLowerCase();
  }

  isRouteProvider(providerName) {
    const map_obj = this;
    const currentProvider = (map_obj?.map_data?.route_provider || wpgmp_local?.route_provider || 'leaflet').toLowerCase();

    return currentProvider === providerName.toLowerCase();
  }

  /**
 * Adds a locate-me control to the map for both Google Maps and Leaflet
 */

register_locate_me_control() {
  const map_obj = this;
  if (map_obj.settings.locateme_control !== true) return;

  const className = "wpgmp_locateme_control " + (map_obj.settings.locateme_control_position || "topright").toLowerCase();
  const innerHTML = `<span title='${wpgmp_local.locate_me}'></span>`;

  if (map_obj.isMapProvider("google")) {
    const controlDiv = document.createElement("div");
    controlDiv.className = className;
    controlDiv.innerHTML = innerHTML;
    map_obj.map.controls[
      google.maps.ControlPosition[map_obj.settings.locateme_control_position || "TOP_RIGHT"]
    ].push(controlDiv);
  } else {
    const LocateControl = L.Control.extend({
      options: { position: map_obj.settings.locateme_control_position?.toLowerCase() || "topright" },
      onAdd() {
        const container = L.DomUtil.create("div", className);
        container.innerHTML = innerHTML;
        container.title = wpgmp_local.locate_me;
        L.DomEvent.disableClickPropagation(container);
        return container;
      }
    });
    map_obj.map.addControl(new LocateControl());
  }

  // Click Event Binding (Same for both)
  $(map_obj.container).on("click", ".wpgmp_locateme_control", function () {
    map_obj.get_current_location((user_location) => {
      const isGoogle = map_obj.isMapProvider("google");
      const lat = typeof user_location.lat === 'function' ? user_location.lat() : user_location.lat;
      const lng = typeof user_location.lng === 'function' ? user_location.lng() : user_location.lng;
      const latLngObj = isGoogle ? new google.maps.LatLng(lat, lng) : L.latLng(lat, lng);

      if (!isGoogle) {
        const bounds = latLngObj.toBounds(100);
        map_obj.map.flyToBounds(bounds, {
          padding: [50, 50],
          maxZoom: 15,
          animate: true,
          duration: 1.5
        });
      } else {
        map_obj.map.panTo(latLngObj);
        map_obj.map.setZoom(15);
      }

      if (!isGoogle && map_obj.pulse_circle && map_obj.map.hasLayer(map_obj.pulse_circle)) {
        map_obj.map.removeLayer(map_obj.pulse_circle);
      }

      if (!isGoogle) {

        map_obj.pulse_circle = map_obj.create_leaflet_marker({
          map: map_obj.map,
          position: latLngObj,
          isDivIcon: true,
          html: '<div class="pulse-marker"></div>',
          customClass: '', // optional, for no extra class
        }); 
      
      } else {
        // Google Maps fallback visual marker (if needed)
        if (map_obj.pulse_circle) map_obj.pulse_circle.setMap(null);
        const markerDiv = document.createElement('div');
        markerDiv.className = 'pulse-marker';
        const overlay = new google.maps.OverlayView();
        overlay.onAdd = function () {
          const layer = this.getPanes().overlayMouseTarget;
          layer.appendChild(markerDiv);
          markerDiv.style.position = 'absolute';
        };
        overlay.draw = function () {
          const projection = this.getProjection();
          const point = projection.fromLatLngToDivPixel(latLngObj);
          if (point) {
            markerDiv.style.left = point.x - 15 + 'px';
            markerDiv.style.top = point.y - 15 + 'px';
          }
        };
        overlay.onRemove = function () {
          if (markerDiv.parentNode) markerDiv.parentNode.removeChild(markerDiv);
        };
        overlay.setMap(map_obj.map);
        map_obj.pulse_circle = overlay;
      }
     
      if (map_obj.map_center_marker) {
        map_obj.map_center_marker.setPosition(latLngObj);
      }
      // Draw/Move circle
      if (map_obj.set_center_circle) {
        if (map_obj.isMapProvider("google")) {
          map_obj.set_center_circle.setCenter(latLngObj);
        } else if (typeof map_obj.set_center_circle.setLatLng === "function") {
          map_obj.set_center_circle.setLatLng(latLngObj);
        }
      }


    });
  });
}

getMarkerSize() {
  const isMobile = window.innerWidth <= 767;
  const isRetina = window.devicePixelRatio > 1.5;

  let size;
  if (isMobile) {
    size = wpgmp_local.mobile_marker_size || [24,24];
  } else if (isRetina) {
    size = wpgmp_local.retina_marker_size || [64,64];
  } else {
    size = wpgmp_local.desktop_marker_size || [32,32];
  }

  return size;
}

 compute_distance(lat1, lng1, lat2, lng2) {
  var map_obj = this;
  const isGoogle = map_obj.isMapProvider('google');

  lat1 = parseFloat(lat1);
  lng1 = parseFloat(lng1);
  lat2 = parseFloat(lat2);
  lng2 = parseFloat(lng2);

  if (isGoogle && typeof google !== 'undefined' && google.maps?.geometry?.spherical) {
    const point1 = new google.maps.LatLng(lat1, lng1);
    const point2 = new google.maps.LatLng(lat2, lng2);
    return google.maps.geometry.spherical.computeDistanceBetween(point1, point2); // meters
  }

  // Leaflet fallback
  if (typeof L !== 'undefined') {
    const point1 = L.latLng(lat1, lng1);
    const point2 = L.latLng(lat2, lng2);
    return point1.distanceTo(point2); // meters
  }

  console.warn('No supported map provider available to compute distance.');
  return null;
}



}

window.WpgmpBaseMaps = WpgmpBaseMaps;

})(jQuery, window, document);
/**
 * WpgmpGoogleMaps class - extends BaseMaps for Google Maps provider.
 */

(function ($, window, document) {
  class WpgmpGoogleMaps extends WpgmpBaseMaps {
    constructor(element, map_data = {}, places = []) {
      super(element, map_data, places);

      var suppress_markers = false;
      if (this.map_data.map_tabs && this.map_data.map_tabs.direction_tab) {
        suppress_markers =
          this.map_data.map_tabs.direction_tab.suppress_markers;
      }
     
      this.infowindow_marker = new google.maps.InfoWindow();
      this.infobox = this.wpgmpInitializeInfoBox();
      this.newRoutesApiURL =
        "https://routes.googleapis.com/directions/v2:computeRoutes";
      this.searchNearbyApiURL =
        "https://places.googleapis.com/v1/places:searchNearby";
        
    }
    // =========================
// 🌀 Part 1: Initialization & Responsive Setup
// =========================
    init() {
      const map_obj = this;

      // Determine current screen type
      let isMobile = false;
      let screen_type = "desktop";
      const screen_size = $(window).width();

      if (screen_size <= 480) {
        screen_type = "smartphones";
      } else if (screen_size > 480 && screen_size <= 768) {
        screen_type = "ipads";
      } else if (screen_size >= 1824) {
        screen_type = "large-screens";
      }

      // Apply mobile-specific settings if configured
      if (screen_type !== "desktop" && map_obj.settings.mobile_specific === true) {
        isMobile = true;

        const mobileSettings = map_obj.settings.screens?.[screen_type];
        if (mobileSettings) {
          map_obj.settings.width_mobile = mobileSettings.map_width_mobile;
          map_obj.settings.height_mobile = mobileSettings.map_height_mobile;
          map_obj.settings.zoom = parseInt(mobileSettings.map_zoom_level_mobile);
          map_obj.settings.draggable = mobileSettings.map_draggable_mobile !== "false";
          map_obj.settings.scroll_wheel = mobileSettings.map_scrolling_wheel_mobile !== "false";
        }

        // Apply mobile dimensions
        if (map_obj.settings.width_mobile !== "") {
          $(map_obj.element).css("width", map_obj.settings.width_mobile);
        }

        if (map_obj.settings.height_mobile !== "") {
          $(map_obj.element).css("height", map_obj.settings.height_mobile);
        }
      }

      // =========================
    // 🗺️ Part 2: Map Initialization and Configuration
    // =========================

    const center = new google.maps.LatLng(
      map_obj.settings.center_lat,
      map_obj.settings.center_lng
    );

    const mapOptions = {
      zoom: parseInt(map_obj.settings.zoom),
      center: center,
      scrollwheel: map_obj.settings.scroll_wheel !== "true",
      disableDoubleClickZoom: map_obj.settings.doubleclickzoom === false,
      zoomControl: map_obj.settings.zoom_control === true,
      fullscreenControl: map_obj.settings.full_screen_control === true,
      fullscreenControlOptions: {
        position: google.maps.ControlPosition[map_obj.settings.full_screen_control_position],
      },
      zoomControlOptions: {
        style: google.maps.ZoomControlStyle[map_obj.settings.zoom_control_style],
        position: google.maps.ControlPosition[map_obj.settings.zoom_control_position],
      },
      mapTypeControl: map_obj.settings.map_type_control === true,
      mapTypeControlOptions: {
        style: google.maps.MapTypeControlStyle[map_obj.settings.map_type_control_style],
        position: google.maps.ControlPosition[map_obj.settings.map_type_control_position],
      },
      cameraControlOptions: {
        position: google.maps.ControlPosition[map_obj.settings.camera_control_position],
      },
      scaleControl: map_obj.settings.scale_control === true,
      streetViewControl: map_obj.settings.street_view_control === true,
      streetViewControlOptions: {
        position: google.maps.ControlPosition[map_obj.settings.street_view_control_position],
      },
      overviewMapControl: map_obj.settings.overview_map_control === true,
      overviewMapControlOptions: {
        opened: map_obj.settings.overview_map_control,
      },
      draggable: map_obj.settings.draggable,
      mapTypeId: google.maps.MapTypeId[map_obj.settings.map_type_id],
      styles: eval(map_obj.map_data.styles),
      minZoom: parseInt(map_obj.settings.min_zoom),
      maxZoom: parseInt(map_obj.settings.max_zoom),
      gestureHandling: map_obj.settings.gesture,
      cameraControl: map_obj.settings.camera_control === true,
    }

    let useAdvancedMarker = (wpgmp_local.use_advanced_marker == 'true') ? true : false;
    const isApplyStyles = (map_obj.map_data.styles && map_obj.map_data.styles!='') ? true:false;
    const spiderfier_enabled = map_obj.map_data.map_marker_spiderfier_setting?.marker_spiderfy === "true";

    if(map_obj.map_data.marker_cluster){
      // Do not work with marker cluster
      useAdvancedMarker = false;
    } else if(isApplyStyles) {
      // Do not work with snazzy maps or custom styles
      useAdvancedMarker = false;
    } else if(spiderfier_enabled){
      // Do no work with spiderfier effects.
      useAdvancedMarker = false;
    }

    map_obj.useAdvancedMarker = useAdvancedMarker;

    if (map_obj.useAdvancedMarker === true && isApplyStyles == false) {
      mapOptions.mapId = "mapID" + map_obj.map_data.map_property.map_id;
    }

    map_obj.map = new google.maps.Map(map_obj.element, mapOptions);

    if (typeof google?.maps?.marker?.AdvancedMarkerElement !== 'undefined') {
      
      if (typeof google.maps.Animation === 'undefined') {
        google.maps.Animation = {
          BOUNCE: 'BOUNCE',
          DROP: 'DROP' // Optional: future support
        };
      }

      const proto = google.maps.marker.AdvancedMarkerElement.prototype;
    
      if (typeof proto.getPosition !== 'function') {
        proto.getPosition = function () {
          return this.position;
        };
      }
    
      if (typeof proto.setPosition !== 'function') {
        proto.setPosition = function (latlng) {
          this.position = latlng;
        };
      }
    
      if (typeof proto.setAnimation !== 'function') {
        proto.setAnimation = function (animationType) {
          //console.warn("setAnimation() is not natively supported in AdvancedMarkerElement. Applying custom behavior.");
          this._animation = animationType;
    
          const el = this.content;
          if (!el || !(el instanceof HTMLElement)) return;
    
          el.classList.remove('gmp-marker-bounce', 'gmp-marker-drop');
    
          if (animationType === google.maps.Animation.BOUNCE) {
            el.classList.add('gmp-marker-bounce');
          } else if (animationType === google.maps.Animation.DROP) {
            el.classList.add('gmp-marker-drop');
          }

        };
      }
    
      if (typeof proto.getAnimation !== 'function') {
        proto.getAnimation = function () {
          return this._animation || null;
        };
      }
    
      if (typeof proto.setVisible !== 'function') {
        proto.setVisible = function (visible) {
          if (visible) {
            if (!this.map && this._boundMap) {
              this.map = this._boundMap;
            }
          } else {
            this._boundMap = this.map; // store map reference before hiding
            this.map = null;
          }
          this._visible = visible;
        };
      }
      
      if (typeof proto.getVisible !== 'function') {
        proto.getVisible = function () {
          return this._visible || !!this.map;
        };
      }

      if (typeof proto.bindMap !== 'function') {
        proto.bindMap = function (map) {
          this._boundMap = map;
          this.map = map;
          this._visible = true;
        };
      }

      if (typeof google.maps.Marker.prototype.bindMap !== 'function') {
        google.maps.Marker.prototype.bindMap = function (map) {
          this._boundMap = map;
          this.setMap(map);
        };
      }

      if (typeof proto.setIcon !== 'function') {
        proto.setIcon = function (icon) {
          if (typeof icon !== 'object' || !icon.url) {
            console.warn('Invalid icon object passed to setIcon');
            return;
          }
    
          const iconUrl = icon.url;
          const size = icon.scaledSize 
            ? [icon.scaledSize.width, icon.scaledSize.height] 
            : [32, 32]; // fallback default size
    
          // Store icon size for reuse
          this._iconSize = size;
          
          /*
          const iconElement = document.createElement('img');
          iconElement.src = iconUrl;
          iconElement.alt = this.title || '';
          iconElement.style.width = `${size[0]}px`;
          iconElement.style.height = `${size[1]}px`;
    
          if (this.title) {
            iconElement.title = this.title;
          }
            */
          const iconElement = map_obj.createIconElement(iconUrl, this.title, size);

          this.content = iconElement;
        };
      }
    
      // Optional: allow storing the icon size for reuse
      if (typeof proto.setIconSize !== 'function') {
        proto.setIconSize = function (sizeArray) {
          if (Array.isArray(sizeArray) && sizeArray.length === 2) {
            this._iconSize = sizeArray;
          }
        };
      }

    }
    
    

    // Apply localization strings if available
    if (map_obj.map_data.map_options.search_placeholder) {
      wpgmp_local.search_placeholder = map_obj.map_data.map_options.search_placeholder;
    }

    if (map_obj.map_data.map_options.select_category) {
      wpgmp_local.select_category = map_obj.map_data.map_options.select_category;
    }

    // Handle template placeholder storage if using advanced template mode
    map_obj.search_button = false;
    if (map_obj.map_data.map_options.advance_template === true) {
      const $listingElement = $(document).find(
        '[data-listing-content="true"][data-wpgmp-map-id]'
      );
      const eachContent = $listingElement.prop("innerHTML");

      if (eachContent) {
        map_obj.map_data.listing.listing_placeholder = eachContent;
      }
    }

    // =========================
    // 📍 Part 3: Core Marker + Layer Initialization
    // =========================

      // Trigger lifecycle hook after map is initialized
      map_obj.map_loaded();

      // Handle responsive layout
      map_obj.responsive_map();
  
      // Create and render all map markers
      map_obj.create_markers();
      map_obj.display_markers();

      // Load Google Fonts if configured
      if (typeof map_obj.settings.google_fonts !== "undefined") {
        map_obj.load_google_fonts(map_obj.settings.google_fonts);
      }

      // Show search control box if enabled
      if (map_obj.settings.search_control === true) {
        map_obj.show_search_control();
      }
      // Add custom map control buttons if enabled
      if (map_obj.settings.map_control === true) {
        if (typeof map_obj.settings.map_control_settings !== "undefined") {
          $.each(map_obj.settings.map_control_settings, function (k, val) {
            const centerControlDiv = document.createElement("div");
            map_obj.create_element(centerControlDiv, map_obj.map, val.html);
            centerControlDiv.index = 1;
            map_obj.map.controls[
              google.maps.ControlPosition[val.position]
            ].push(centerControlDiv);
          });
        }
      }

      // Register "Locate Me" button if enabled
      if (map_obj.settings.locateme_control === true && map_obj.settings.locateme_control_position) {
        map_obj.register_locate_me_control();
      }

      // Overlays and Visual Layers
      if (map_obj.map_data.street_view) map_obj.set_streetview(center);
      if (map_obj.map_data.weather_layer) map_obj.set_weather_layer();
      if (map_obj.map_data.bicyle_layer) map_obj.set_bicyle_layer();
      if (map_obj.map_data.traffic_layer) map_obj.set_traffic_layer();
      if (map_obj.map_data.transit_layer) map_obj.set_transit_layer();
      if (map_obj.map_data.panoramio_layer) map_obj.set_panoramic_layer();
      if (map_obj.map_data.overlay_setting) map_obj.set_overlay();
      if (map_obj.settings.display_45_imagery === "45") map_obj.set_45_imagery();
      if (map_obj.map_data.map_visual_refresh === true) map_obj.set_visual_refresh();

      // Cluster markers if enabled
      if (map_obj.map_data.marker_cluster) map_obj.set_marker_cluster();

      // Enable panning control if configured
      if (map_obj.map_data.panning_control) map_obj.set_panning_control();

      // Load KML layer
      if (map_obj.map_data.kml_layer) map_obj.set_kml_layer();

      // =========================
    // 🧩 Part 4: Shapes, Routes, Sorting, and Listings
    // =========================

      // Create shapes (polygons, polylines, circles, rectangles)
      if (map_obj.map_data.shapes?.shape) {
        map_obj.opened_info = map_obj.infowindow_marker;

        if (map_obj.map_data.shapes.shape.polygons) {
          map_obj.create_polygon();
        }

        if (map_obj.map_data.shapes.shape.polylines) {
          map_obj.create_polyline();
        }

        if (map_obj.map_data.shapes.shape.circles) {
          map_obj.create_circle();
        }

        if (map_obj.map_data.shapes.shape.rectangles) {
          map_obj.create_rectangle();
        }
      }

      // Apply default sorting if enabled (for listing or category tabs)
      if (map_obj.map_data.listing) {
        if (map_obj.map_data.listing.default_sorting && map_obj.map_data.autosort) {
          let data_type = "";
          if (map_obj.map_data.listing.default_sorting.orderby === "listorder") {
            data_type = "num";
          }

          map_obj.sorting(
            map_obj.map_data.listing.default_sorting.orderby,
            map_obj.map_data.listing.default_sorting.inorder,
            data_type
          );
        }
      } else if (map_obj.map_data.map_tabs?.category_tab?.cat_tab) {
        if (!map_obj.map_data.map_tabs.category_tab.cat_post_order) {
          map_obj.map_data.map_tabs.category_tab.cat_post_order = "asc";
        }

        if (map_obj.map_data.autosort) {
          map_obj.sorting("title", map_obj.map_data.map_tabs.category_tab.cat_post_order);
          map_obj.map_data.places_for_category_tabs = map_obj.map_data.places;
          map_obj.map_data.places_for_category_tabs = map_obj.sorting_inside_tabs(
            map_obj.map_data.places_for_category_tabs,
            map_obj.map_data.map_tabs.category_tab.cat_post_order
          );
        }
      }

      // =========================
    // 📑 Part 5: Filter UI, Events & Listeners
    // =========================

    if (map_obj.map_data.listing) {

      // Inject filters and custom filters into UI
      map_obj.display_filters_listing();
      map_obj.custom_filters();
      if (
        map_obj !== "undefined" &&
        map_obj.map_data.default_amenities != undefined &&
        map_obj.map_data.enableAmenitiesListing != undefined &&
        map_obj.map_data.enableAmenitiesListing
      ) {
        //WAIT WHILE LOAD AMENITIES
      } else {
        $(map_obj.container)
          .find(".location_pagination" + map_obj.map_data.map_property.map_id)
          .pagination(map_obj.show_places.length, {
            callback: map_obj.display_places_listing,
            map_data: map_obj,
            items_per_page:
              map_obj.map_data.listing.pagination.listing_per_page,
            prev_text: wpgmp_local.prev,
            next_text: wpgmp_local.next,
          });
      }

      
    }

    // =========================
    // 🔍 Part 7: Autocomplete, Events & Final Touches
    // =========================

      // Load GeoJSON data (if any)
      if (typeof map_obj.map_data.geojson !== "undefined") {
        map_obj.load_json(map_obj.map_data.geojson);
      }

      

      // Register and re-register Google Autocomplete
      map_obj.google_auto_suggest($(".wpgmp_auto_suggest"));

      // Optionally show center circle and marker
      if (map_obj.settings.show_center_circle === true) {
        map_obj.show_center_circle();
      }

      if (map_obj.settings.show_center_marker === true) {
        map_obj.show_center_marker();
      }

      // Enable drawing manager if shape editing is enabled
      
      if (map_obj.map_data.shapes?.drawing_editable === true) {
        map_obj.enable_drawing();
      }

      // Fit bounds to markers if setting enabled
      if (map_obj.settings.fit_bounds === true) {
        map_obj.fit_bounds();
      }

      // Register any custom plugin or map-specific JS events
      map_obj.register_events();

      // Initialize accordion behavior
      $(map_obj.container).find(".wpgmp-accordion").accordion({
        speed: "slow",
      });

    }

    createIconElement(iconUrl, title = '', iconSize = [32, 32]) {
      const icon = document.createElement('img');
      icon.src = iconUrl;
      icon.alt = title;
      // Only apply size if the icon is an SVG
      if (iconUrl.toLowerCase().includes('image/svg') || iconUrl.toLowerCase().endsWith('.svg')) {
        icon.style.width = `${iconSize[0]}px`;
        icon.style.height = `${iconSize[1]}px`;
      }
      icon.title = title;
      icon.style.objectFit = 'contain';
      return icon;
    }
    
    createMarker(place) {
      var map_obj = this;
      var map = map_obj.map;
      var iconSize = map_obj.getMarkerSize();
      var image = {
        url: place.icon,
        scaledSize: new google.maps.Size(iconSize[0], iconSize[1]),
      };

      place.marker = map_obj.create_google_marker({
        map: map,
        position: {lat:place.geometry.location.lat(),lng:place.geometry.location.lng()},
        iconUrl: place.icon,
      });

      var post_info_class = "fc-infowindow-";
      place.marker = place.marker;
      place.address = place.vicinity;
      place.title = place.name;
      place.location = {};
      place.location.onclick_action = "marker";
      var content = "";

      var temp_listing_placeholder = "";
      var post_info_class = "fc-infowindow-";
      if (place.source == "post") {
        temp_listing_placeholder = map_obj.settings.infowindow_geotags_setting;
        post_info_class =
          "wpgmp_infowindow_post fc-item-" +
          map_obj.settings.infowindow_post_skin.name;
      } else {
        temp_listing_placeholder = map_obj.settings.infowindow_setting;
        if (
          map_obj.map_data.page != "edit_location" &&
          map_obj.settings.infowindow_skin
        )
          post_info_class =
            "fc-infowindow-" + map_obj.settings.infowindow_skin.name;
      }

      if (typeof temp_listing_placeholder == "undefined") {
        temp_listing_placeholder = place.content;
      }

      var marker_image = "";
      var image_url =
        typeof place.photos !== "undefined"
          ? place.photos[0].getUrl({ maxWidth: 400, maxHeight: 400 })
          : place.icon;
      marker_image =
        "<div class='fc-feature-img amenities_image'><img alt='" +
        place.vicinity +
        "' src='" +
        image_url +
        "' class='wpgmp_marker_image wpgmp_amenities_image' /></div>";

      var replaceData = {
        "{marker_id}": place.id,
        "{marker_title}": place.name,
        "{marker_address}": place.vicinity,
        "{marker_latitude}": place.geometry.location.lat(),
        "{marker_longitude}": place.geometry.location.lng(),
        "{marker_city}": "",
        "{marker_state}": "",
        "{marker_country}": "",
        "{marker_postal_code}": "",
        "{marker_zoom}": "",
        "{marker_icon}": place.icon,
        "{marker_category}": place.types[0],
        "{marker_message}": place.content,
        "{marker_image}": marker_image,
      };

      for (var prop in replaceData) {
        if (replaceData[prop] == undefined || replaceData[prop] == "undefined")
          replaceData[prop] = "";
      }

      if (temp_listing_placeholder) {
        temp_listing_placeholder = temp_listing_placeholder.replace(
          /{#if (.*?)}([\s\S]*?){\/if}/g,
          function (match, p1, p2) {
            return replaceData["{" + p1 + "}"] ? p2 : "";
          }
        );

        temp_listing_placeholder = temp_listing_placeholder.replace(
          /{[^{}]+}/g,
          function (match) {
            if (match in replaceData) {
              return replaceData[match];
            } else {
              return "";
            }
          }
        );

        var temp_string = temp_listing_placeholder;
        var temp_object = $("<div/>").html(temp_string);
        $(temp_object)
          .find(".wpgmp_extra_field:contains('wpgmp_empty')")
          .remove();
        $(temp_object).find(".wpgmp_empty").prev().remove();
        $(temp_object).find(".wpgmp_empty").remove();

        content = $(temp_object).prop("outerHTML").toString();
      }

      if (content === "") {
        if (
          map_obj.settings.map_infowindow_customisations === true &&
          map_obj.settings.show_infowindow_header === true
        )
          content =
            '<div class="wpgmp_infowindow ' +
            post_info_class +
            '"><div class="wpgmp_iw_head"><div class="wpgmp_iw_head_content">' +
            place.name +
            '</div></div><div class="wpgmp_iw_content">' +
            place.vicinity +
            "</div></div>";
        else
          content =
            '<div class="wpgmp_infowindow ' +
            post_info_class +
            '"><div class="wpgmp_iw_content">' +
            place.content +
            "</div></div>";
      } else {
        if (
          map_obj.settings.map_infowindow_customisations === true &&
          map_obj.settings.show_infowindow_header === true
        )
          content =
            '<div class="wpgmp_infowindow ' +
            post_info_class +
            '"><div class="wpgmp_iw_head"><div class="wpgmp_iw_head_content">' +
            place.name +
            '</div></div><div class="wpgmp_iw_content">' +
            content +
            "</div></div>";
        else
          content =
            '<div class="wpgmp_infowindow ' +
            post_info_class +
            '"><div class="wpgmp_iw_content">' +
            content +
            "</div></div>";
      }

      place.infowindow_data = content;
      place.infowindow = map_obj.infowindow_marker;
      var on_event = map_obj.settings.infowindow_open_event;

      map_obj.event_listener(place.marker, on_event, function () {
        $.each(map_obj.places, function (key, prev_place) {
          prev_place.infowindow.close();
          prev_place.marker.setAnimation(null);
        });
        map_obj.openInfoWindow(place);
      });

      map_obj.places.push(place);
    }
    wpgmp_image_type_overlays() {
      var map_obj = this;
      var imageMapType = new google.maps.ImageMapType({
        getTileUrl(coord, zoom) {
          return [
            "http://www.gstatic.com/io2010maps/tiles/5/L2_",
            zoom,
            "_",
            coord.x,
            "_",
            coord.y,
            ".png",
          ].join("");
        },
        tileSize: new google.maps.Size(256, 256),
      });

      map_obj.map.overlayMapTypes.push(imageMapType);
    }
   
    get_user_position() {
      var map_obj = this;

      navigator.geolocation.getCurrentPosition(
        function (position) {
          map_obj.user_lat_lng = new google.maps.LatLng(
            position.coords.latitude,
            position.coords.longitude
          );
        },
        function (ErrorPosition) {},
        {
          enableHighAccuracy: true,
          timeout: 5000,
          maximumAge: 0,
        }
      );
    }

    wpgmp_finddata(result, type) {
      var component_name = "";

      for (var i = 0; i < result.addressComponents.length; ++i) {
        var component = result.addressComponents[i];
        $.each(component.types, function (index, value) {
          if (value == type) {
            component_name = component.longText;
          }
        });
      }

      return component_name;
    }

    clearSelection() {
      var map_obj = this;
      if (map_obj.selectedShape) {
        map_obj.selectedShape.setEditable(false);
        map_obj.selectedShape = null;
      }
    }
    setSelection(shape) {
      var map_obj = this;
      map_obj.clearSelection();
      map_obj.selectedShape = shape;
      map_obj.selectedShape.setEditable(true);
    }
    deleteSelectedShape() {
      var map_obj = this;
      var key;
      if (map_obj.selectedShape) {
        for (key in map_obj.wpgmp_circles) {
          if (map_obj.wpgmp_circles[key] == map_obj.selectedShape) {
            map_obj.wpgmp_circles.splice(key, 1);
          }
        }
        for (key in map_obj.wpgmp_rectangles) {
          if (map_obj.wpgmp_rectangles[key] == map_obj.selectedShape) {
            map_obj.wpgmp_rectangles.splice(key, 1);
          }
        }
        for (key in map_obj.wpgmp_polygons) {
          if (map_obj.wpgmp_polygons[key] == map_obj.selectedShape) {
            map_obj.wpgmp_polygons.splice(key, 1);
          }
        }
        for (key in map_obj.wpgmp_polylines) {
          if (map_obj.wpgmp_polylines[key] == map_obj.selectedShape) {
            map_obj.wpgmp_polylines.splice(key, 1);
          }
        }
        map_obj.selectedShape.setMap(null);
      }
    }

    create_google_marker({
      map,
      position,
      iconUrl = null,
      title = '',
      label = '',
      draggable = false,
      clickable = true,
      anchorPoint = null
    }) {
      const map_obj = this;
    
      const lat = parseFloat(position.lat || position.lat());
      const lng = parseFloat(position.lng || position.lng());
      const markerLatLng = new google.maps.LatLng(lat, lng);
    
      const iconSize = map_obj.getMarkerSize?.() || [32, 32];
    
      // Default icon fallback
      if (!iconUrl || iconUrl.trim() === '') {
        iconUrl = map_obj.settings.marker_default_icon || wpgmp_local.default_marker_icon;
      }

      const isSvg = iconUrl.toLowerCase().includes('image/svg') || iconUrl.toLowerCase().endsWith('.svg');
    
      if (map_obj.useAdvancedMarker &&  google.maps.marker?.AdvancedMarkerElement ) {
        // AdvancedMarkerElement version
        const iconElement = isSvg ? map_obj.createIconElement(iconUrl, title, iconSize) : map_obj.createIconElement(iconUrl, title);

        return new google.maps.marker.AdvancedMarkerElement({
          map: map,
          position: markerLatLng,
          title: title,
          content: iconElement,
        });
    
      } else {
        // Fallback to classic google.maps.Marker
        const markerOptions = {
          position: markerLatLng,
          map,
          title,
          label,
          draggable,
          clickable,
        };
    
        if (iconUrl) {
          markerOptions.icon = {
            url: iconUrl,
          };
          if (isSvg) {
            markerOptions.icon.scaledSize = new google.maps.Size(parseInt(iconSize[0]), parseInt(iconSize[1]));
          }
        }
    
        if (anchorPoint) {
          markerOptions.anchorPoint = new google.maps.Point(anchorPoint.x, anchorPoint.y);
        }
    
        return new google.maps.Marker(markerOptions);
      }
    }    

    enable_drawing() {
      var map_obj = this;
      map_obj.drawingmanager = new google.maps.drawing.DrawingManager({
        drawingMode: null,
        drawingControl: true,
        drawingControlOptions: {
          position: google.maps.ControlPosition.TOP_CENTER,
          drawingModes: [
            google.maps.drawing.OverlayType.CIRCLE,
            google.maps.drawing.OverlayType.POLYGON,
            google.maps.drawing.OverlayType.POLYLINE,
            google.maps.drawing.OverlayType.RECTANGLE,
          ],
        },

        circleOptions: {
          fillColor: "#003dce",
          strokeColor: "#003dce",
          strokeWeight: "1",
          strokeOpacity: "0.5",
          zindex: 1,
          fillOpacity: "0.5",
          editable: false,
          draggable: false,
        },
        polygonOptions: {
          fillColor: "#003dce",
          strokeColor: "#003dce",
          strokeWeight: "1",
          strokeOpacity: "0.5",
          zindex: 1,
          fillOpacity: "0.5",
          editable: false,
          draggable: false,
        },
        polylineOptions: {
          fillColor: "#003dce",
          strokeColor: "#003dce",
          strokeWeight: "1",
          strokeOpacity: "0.5",
          zindex: 1,
          fillOpacity: "0.5",
          editable: false,
          draggable: false,
        },
        rectangleOptions: {
          fillColor: "#003dce",
          strokeColor: "#003dce",
          strokeWeight: "1",
          strokeOpacity: "0.5",
          zindex: 1,
          fillOpacity: "0.5",
          editable: false,
          draggable: false,
        },
      });
      map_obj.drawingmanager.setMap(map_obj.map);
      map_obj.event_listener(
        map_obj.drawingmanager,
        "circlecomplete",
        function (circle) {
          map_obj.wpgmp_circles.push(circle);
          map_obj.wpgmp_shape_complete(circle, "circle");
        }
      );
      map_obj.event_listener(
        map_obj.drawingmanager,
        "polygoncomplete",
        function (polygon) {
          map_obj.wpgmp_polygons.push(polygon);
          map_obj.wpgmp_shape_complete(polygon, "polygon");
        }
      );
      map_obj.event_listener(
        map_obj.drawingmanager,
        "polylinecomplete",
        function (polyline) {
          map_obj.wpgmp_polylines.push(polyline);
          map_obj.wpgmp_shape_complete(polyline, "polyline");
        }
      );
      map_obj.event_listener(
        map_obj.drawingmanager,
        "rectanglecomplete",
        function (rectangle) {
          map_obj.wpgmp_rectangles.push(rectangle);
          map_obj.wpgmp_shape_complete(rectangle, "rectangle");
        }
      );
    }

    create_polygon() {
      var map_obj = this;

      $.each(this.map_data.shapes.shape.polygons, function (index, polygon) {
        var path = [];
        $.each(polygon.cordinates, function (ind, cordinate) {
          var latlng = cordinate.split(",");
          path.push(new google.maps.LatLng(latlng[0], latlng[1]));
        });

        polygon.reference = new google.maps.Polygon({
          paths: path,
          strokeColor: polygon.settings.stroke_color,
          strokeOpacity: polygon.settings.stroke_opacity,
          strokeWeight: polygon.settings.stroke_weight,
          fillColor: polygon.settings.fill_color,
          fillOpacity: polygon.settings.fill_opacity,
        });
        if (typeof map_obj.map_data.shapes != "undefined") {
          if (map_obj.map_data.shapes.drawing_editable === true) {
            map_obj.event_listener(polygon.reference, "click", function () {
              map_obj.setSelection(polygon.reference);
              map_obj.get_shapes_options(polygon.reference, "polygon");
            });
          } else if (
            polygon.events !== undefined &&
            (polygon.events.url !== "" || polygon.events.message !== "")
          ) {
            map_obj.event_listener(polygon.reference, "click", function () {
              if (
                (polygon.events.url === "" ||
                  polygon.events.url === undefined) &&
                polygon.events.message !== ""
              ) {
                var bounds = new google.maps.LatLngBounds();
                polygon.reference.getPath().forEach(function (element, index) {
                  bounds.extend(element);
                });
                $.each(map_obj.places, function (key, place) {
                  place.infowindow.close();
                });
                map_obj.opened_info.setPosition(bounds.getCenter());
                if (map_obj.settings.map_infowindow_customisations === true)
                  map_obj.opened_info.setContent(
                    '<div class="wpgmp_infowindow"><div class="wpgmp_iw_content">' +
                      polygon.events.message +
                      "</div></div>"
                  );
                else map_obj.opened_info.setContent(polygon.events.message);
                map_obj.opened_info.open(map_obj.map, this);
              } else {
                if (
                  polygon.events.url !== "undefined" &&
                  polygon.events.url !== undefined
                )
                  window.location = polygon.events.url;
              }
            });
          }
        }
        polygon.reference.setMap(map_obj.map);

        map_obj.wpgmp_polygons.push(polygon.reference);

        if (polygon.events == undefined) {
          polygon.events = {};
          polygon.events.url = "";
          polygon.events.message = "";
        }

        map_obj.wpgmp_shape_events.push({
          shape: polygon.reference,
          url: polygon.events.url,
          message: polygon.events.message,
        });
      });
    }

    create_polyline() {
      var map_obj = this;

      $.each(this.map_data.shapes.shape.polylines, function (index, polyline) {
        var path = [];

        if (typeof polyline.cordinates != "undefined") {
          $.each(polyline.cordinates, function (ind, cordinate) {
            var latlng = cordinate.split(",");
            path.push(new google.maps.LatLng(latlng[0], latlng[1]));
          });

          polyline.reference = new google.maps.Polyline({
            path: path,
            strokeColor: polyline.settings.stroke_color,
            strokeOpacity: polyline.settings.stroke_opacity,
            strokeWeight: polyline.settings.stroke_weight,
          });

          if (typeof map_obj.map_data.shapes != "undefined") {
            if (map_obj.map_data.shapes.drawing_editable === true) {
              map_obj.event_listener(polyline.reference, "click", function () {
                map_obj.setSelection(polyline.reference);
                map_obj.get_shapes_options(polyline.reference, "polyline");
              });
            } else if (
              polyline.events !== undefined &&
              (polyline.events.url !== "" || polyline.events.message !== "")
            ) {
              map_obj.event_listener(polyline.reference, "click", function () {
                if (
                  (polyline.events.url === "" ||
                    polyline.events.url === undefined) &&
                  polyline.events.message !== ""
                ) {
                  var bounds = new google.maps.LatLngBounds();
                  polyline.reference
                    .getPath()
                    .forEach(function (element, index) {
                      bounds.extend(element);
                    });
                  $.each(map_obj.places, function (key, place) {
                    place.infowindow.close();
                  });
                  map_obj.opened_info.setPosition(bounds.getCenter());
                  if (map_obj.settings.map_infowindow_customisations === true)
                    map_obj.opened_info.setContent(
                      '<div class="wpgmp_infowindow"><div class="wpgmp_iw_content">' +
                        polyline.events.message +
                        "</div></div>"
                    );
                  else map_obj.opened_info.setContent(polyline.events.message);
                  map_obj.opened_info.open(map_obj.map, this);
                } else if (polyline.events.url !== "") {
                  if (
                    polyline.events.url !== "undefined" &&
                    polyline.events.url !== undefined
                  )
                    window.location = polyline.events.url;
                }
              });
            }
          }
        }
        if (typeof polyline.reference != "undefined") {
          polyline.reference.setMap(map_obj.map);
          map_obj.wpgmp_polylines.push(polyline.reference);

          if (polyline.events == undefined) {
            polyline.events = {};
            polyline.events.url = "";
            polyline.events.message = "";
          }

          map_obj.wpgmp_shape_events.push({
            shape: polyline.reference,
            url: polyline.events.url,
            message: polyline.events.message,
          });
        }
      });
    }
    event_listener(obj, type, func) {
      google.maps.event.addListener(obj, type, func);
    }
    create_circle() {
      var map_obj = this;
      $.each(this.map_data.shapes.shape.circles, function (index, circle) {
        var path;
        $.each(circle.cordinates, function (ind, cordinate) {
          var latlng = cordinate.split(",");
          path = new google.maps.LatLng(latlng[0], latlng[1]);
        });

        circle.reference = new google.maps.Circle({
          fillColor: circle.settings.fill_color,
          fillOpacity: circle.settings.fill_opacity,
          strokeColor: circle.settings.stroke_color,
          strokeOpacity: circle.settings.stroke_opacity,
          strokeWeight: circle.settings.stroke_weight,
          center: path,
          radius: parseInt(circle.settings.radius),
        });

        if (typeof map_obj.map_data.shapes != "undefined") {
          if (map_obj.map_data.shapes.drawing_editable === true) {
            map_obj.event_listener(circle.reference, "click", function () {
              map_obj.setSelection(circle.reference);
              map_obj.get_shapes_options(circle.reference, "circle");
            });
          } else if (
            circle.events !== undefined &&
            (circle.events.url !== "" || circle.events.message !== "")
          ) {
            map_obj.event_listener(circle.reference, "click", function () {
              if (
                (circle.events.url === "" || circle.events.url === undefined) &&
                circle.events.message !== ""
              ) {
                $.each(map_obj.places, function (key, place) {
                  place.infowindow.close();
                });
                map_obj.opened_info.setPosition(circle.reference.getCenter());
                if (map_obj.settings.map_infowindow_customisations === true)
                  map_obj.opened_info.setContent(
                    '<div class="wpgmp_infowindow"><div class="wpgmp_iw_content">' +
                      circle.events.message +
                      "</div></div>"
                  );
                else map_obj.opened_info.setContent(circle.events.message);
                map_obj.opened_info.open(map_obj.map, this);
              } else if (circle.events.url !== "") {
                if (
                  circle.events.url !== "undefined" &&
                  circle.events.url !== undefined
                )
                  window.location = circle.events.url;
              }
            });
          }
        }

        circle.reference.setMap(map_obj.map);
        map_obj.wpgmp_circles.push(circle.reference);

        if (circle.events == undefined) {
          circle.events = {};
          circle.events.url = "";
          circle.events.message = "";
        }

        map_obj.wpgmp_shape_events.push({
          shape: circle.reference,
          url: circle.events.url,
          message: circle.events.message,
        });
      });
    }

    create_rectangle() {
      var map_obj = this;
      $.each(
        this.map_data.shapes.shape.rectangles,
        function (index, rectangle) {
          var left_latlng = rectangle.cordinates[0].split(",");
          var right_latlng = rectangle.cordinates[1].split(",");

          var path = new google.maps.LatLngBounds(
            new google.maps.LatLng(left_latlng[0], left_latlng[1]),
            new google.maps.LatLng(right_latlng[0], right_latlng[1])
          );

          rectangle.reference = new google.maps.Rectangle({
            bounds: path,
            fillColor: rectangle.settings.fill_color,
            fillOpacity: rectangle.settings.fill_opacity,
            strokeColor: rectangle.settings.stroke_color,
            strokeOpacity: rectangle.settings.stroke_opacity,
            strokeWeight: rectangle.settings.stroke_weight,
          });

          if (typeof map_obj.map_data.shapes != "undefined") {
            if (map_obj.map_data.shapes.drawing_editable === true) {
              map_obj.event_listener(rectangle.reference, "click", function () {
                map_obj.setSelection(rectangle.reference);
                map_obj.get_shapes_options(rectangle.reference, "rectangle");
              });
            } else if (
              rectangle.events !== undefined &&
              (rectangle.events.url !== "" || rectangle.events.message !== "")
            ) {
              map_obj.event_listener(rectangle.reference, "click", function () {
                if (
                  (rectangle.events.url === "" ||
                    rectangle.events.url === undefined) &&
                  rectangle.events.message !== ""
                ) {
                  $.each(map_obj.places, function (key, place) {
                    place.infowindow.close();
                  });
                  map_obj.opened_info.setPosition(
                    rectangle.reference.getBounds().getCenter()
                  );
                  if (map_obj.settings.map_infowindow_customisations === true)
                    map_obj.opened_info.setContent(
                      '<div class="wpgmp_infowindow"><div class="wpgmp_iw_content">' +
                        rectangle.events.message +
                        "</div></div>"
                    );
                  else map_obj.opened_info.setContent(rectangle.events.message);
                  map_obj.opened_info.open(map_obj.map, this);
                } else if (rectangle.events.url !== "") {
                  if (
                    rectangle.events.url !== "undefined" &&
                    rectangle.events.url !== undefined
                  )
                    window.location = rectangle.events.url;
                }
              });
            }
          }

          rectangle.reference.setMap(map_obj.map);
          map_obj.wpgmp_rectangles.push(rectangle.reference);

          if (rectangle.events == undefined) {
            rectangle.events = {};
            rectangle.events.url = "";
            rectangle.events.message = "";
          }

          map_obj.wpgmp_shape_events.push({
            shape: rectangle.reference,
            url: rectangle.events.url,
            message: rectangle.events.message,
          });
        }
      );
    }

    get_shapes_options(shape, type) {
      $(".hiderow").addClass("temp_row").removeClass("hiderow");

      // Set value to the input
      $("input[name='shape_fill_color']").val(shape.fillColor);

      // Set background color of the visible preview button (.wp-color-result)
      $("input[name='shape_fill_color']")
        .closest(".wp-picker-container")
        .find(".wp-color-result")
        .css("background-color", shape.fillColor);

      $("input[name='shape_stroke_color']").val(shape.strokeColor);

      // Set background color of the visible preview button (.wp-color-result)
      $("input[name='shape_stroke_color']")
        .closest(".wp-picker-container")
        .find(".wp-color-result")
        .css("background-color", shape.strokeColor);

      $("select[name='shape_fill_opacity']").val(shape.fillOpacity);
      $("select[name='shape_stroke_opacity']").val(shape.strokeOpacity);
      $("select[name='shape_stroke_weight']").val(shape.strokeWeight);
      $("textarea[name='shape_path']").parent().hide();
      $("input[name='shape_radius']").parent().hide();
      $("input[name='shape_center']").parent().hide();
      $("input[name='shape_northeast']").parent().hide();
      $("input[name='shape_southwest']").parent().hide();

      var all_shape_events = this.wpgmp_shape_events;
      $.each(all_shape_events, function (i, shape_event) {
        if (shape_event.shape == shape) {
          $("input[name='shape_click_url']").val(shape_event.url);
          $("textarea[name='shape_click_message']").val(shape_event.message);
        }
      });
      if (type == "circle") {
        $("input[name='shape_radius']").parent().show();
        $("input[name='shape_radius']").val(shape.getRadius());
        $("input[name='shape_center']").parent().show();
        $("input[name='shape_center']").val(
          shape.getCenter().lat() + "," + shape.getCenter().lng()
        );
      } else if (type == "rectangle") {
        $("input[name='shape_northeast']").parent().show();
        $("input[name='shape_northeast']").val(
          shape.getBounds().getNorthEast().lat() +
            "," +
            shape.getBounds().getNorthEast().lng()
        );
        $("input[name='shape_southwest']").parent().show();
        $("input[name='shape_southwest']").val(
          shape.getBounds().getSouthWest().lat() +
            "," +
            shape.getBounds().getSouthWest().lng()
        );
      } else {
        var polygon_cordinate = [];

        var cordinates = shape.getPath();

        cordinates.forEach(function (latlng, index) {
          var latlngin = [latlng.lat(), latlng.lng()];

          if (latlng.lat() !== "" && latlng.lng() !== "")
            polygon_cordinate.push(latlngin);
        });
        $("textarea[name='shape_path']").parent().show();
        $("textarea[name='shape_path']").val(polygon_cordinate.join(" "));
      }
    }

    set_shapes_options(shape) {
      var polyOptions2 = {
        fillColor: $("input[name='shape_fill_color']").val(),
        fillOpacity: $("select[name='shape_fill_opacity']").val(),
        strokeColor: $("input[name='shape_stroke_color']").val(),
        strokeOpacity: $("select[name='shape_stroke_opacity']").val(),
        strokeWeight: $("select[name='shape_stroke_weight']").val(),
      };
      shape.setOptions(polyOptions2);
      var all_shape_events = this.wpgmp_shape_events;
      $.each(all_shape_events, function (i, shape_event) {
        if (shape_event.shape == shape) {
          shape_event.url = $("input[name='shape_click_url']").val();
          shape_event.message = $("textarea[name='shape_click_message']").val();
        }
      });
    }

    wpgmp_shape_complete(shape, type) {
      var map_obj = this;
      map_obj.setSelection(shape);
      map_obj.drawingmanager.setDrawingMode(null);
      if (typeof map_obj.map_data.shapes != "undefined") {
        if (map_obj.map_data.shapes.drawing_editable === true) {
          map_obj.event_listener(shape, "click", function () {
            map_obj.setSelection(shape);
            map_obj.get_shapes_options(shape, type);
          });

          map_obj.wpgmp_shape_events.push({
            shape: shape,
            url: "",
            message: "",
          });
        }
      }
    }


    set_kml_layer() {
      var map_obj = this.map;

      $.each(this.map_data.kml_layer.kml_layers_links, function (index, link) {
        var kmlLayerOptions = {
          url: link,
          map: map_obj,
          preserveViewport: true,
        };

        new google.maps.KmlLayer(kmlLayerOptions);
      });
    }

    set_marker_cluster() {
      var map_obj = this;
      var markers = [];
      var clusterStyles = [
        {
          textColor: "black",
          url: map_obj.map_data.marker_cluster.icon,
          height: 32,
          width: 33,
        },
      ];
      $.each(this.places, function (index, place) {
        if (place.marker.getVisible() == true) {
          markers.push(place.marker);
        }
      });

      if (map_obj.map_data.marker_cluster.apply_style === true) {
        if (!map_obj.markerClusterer) {
          map_obj.markerClusterer = new MarkerClusterer(
            map_obj.map,
            {},
            {
              gridSize: parseInt(map_obj.map_data.marker_cluster.grid),
              maxZoom: parseInt(map_obj.map_data.marker_cluster.max_zoom),
              styles: clusterStyles,
            }
          );
        }

        map_obj.markerClusterer.clearMarkers();
        map_obj.markerClusterer.addMarkers(markers);

        google.maps.event.addListener(
          map_obj.markerClusterer,
          "mouseover",
          function (c) {
            c.clusterIcon_.div_.firstChild.src =
              map_obj.map_data.marker_cluster.hover_icon;
          }
        );

        google.maps.event.addListener(
          map_obj.markerClusterer,
          "mouseout",
          function (c) {
            c.clusterIcon_.div_.firstChild.src =
              map_obj.map_data.marker_cluster.icon;
          }
        );
      } else {
        if (!map_obj.markerClusterer) {
          map_obj.markerClusterer = new MarkerClusterer(
            map_obj.map,
            {},
            {
              gridSize: parseInt(map_obj.map_data.marker_cluster.grid),
              maxZoom: parseInt(map_obj.map_data.marker_cluster.max_zoom),
              imagePath: map_obj.map_data.marker_cluster.image_path,
            }
          );
        }

        map_obj.markerClusterer.clearMarkers();
        map_obj.markerClusterer.addMarkers(markers);
      }
    }

    set_panning_control() {
      var panning_data = this.map_data.panning_control;
      var panning_map_obj = this.map;
      var map_obj = this;

      var strictBounds = new google.maps.LatLngBounds(
        new google.maps.LatLng(
          panning_data.from_latitude,
          panning_data.from_longitude
        ),
        new google.maps.LatLng(
          panning_data.to_latitude,
          panning_data.to_longitude
        )
      );

      google.maps.event.addListener(panning_map_obj, "dragend", function () {
        if (strictBounds.contains(panning_map_obj.getCenter())) return;

        var c = panning_map_obj.getCenter(),
          x = c.lng(),
          y = c.lat(),
          maxX = strictBounds.getNorthEast().lng(),
          maxY = strictBounds.getNorthEast().lat(),
          minX = strictBounds.getSouthWest().lng(),
          minY = strictBounds.getSouthWest().lat();

        if (x < minX) x = minX;
        if (x > maxX) x = maxX;
        if (y < minY) y = minY;
        if (y > maxY) y = maxY;

        panning_map_obj.setCenter(new google.maps.LatLng(y, x));
      });

      google.maps.event.addListener(
        panning_map_obj,
        "zoom_changed",
        function () {
          if (panning_map_obj.getZoom() < panning_data.zoom_level) {
            panning_map_obj.setZoom(parseInt(map_obj.settings.zoom));
          }
        }
      );
    }

    set_visual_refresh() {
      google.maps.visualRefresh = true;
    }

    set_45_imagery() {
      //this.map.setTilt(45);
    }

    set_overlay_generator(tileSize, options) {

      var overlay_generator = function (tileSize, options) {

        this.tileSize = tileSize;
        this.overlay_options = options;
        if (this.overlay_options.font_size != undefined) {
            this.overlay_options.font_size = this.overlay_options.font_size.replace('px', '');
        } else {
            this.overlay_options.font_size = '16';
        }
        if (this.overlay_options.border_width != undefined) {
            this.overlay_options.border_width = this.overlay_options.border_width.replace('px', '');
        } else {
            this.overlay_options.border_width = '2';
        }
  
    }
  
    overlay_generator.prototype.getTile = function (coord, zoom, ownerDocument) {
  
        var div = ownerDocument.createElement("div");
        div.innerHTML = coord;
        div.style.width = this.tileSize.width;
        div.style.height = this.tileSize.height;
        div.style.fontSize = this.overlay_options.font_size + "px";
        div.style.borderStyle = this.overlay_options.border_style;
        div.style.borderWidth = this.overlay_options.border_width + "px";
        div.style.borderColor = this.overlay_options.border_color;
        return div;
    };

    return new overlay_generator(tileSize, options);
    
    }
    
    set_overlay() {
      if (this.map_data.overlay_setting.width != undefined) {
        this.map_data.overlay_setting.width =
          this.map_data.overlay_setting.width.replace("px", "");
      } else {
        this.map_data.overlay_setting.width = "200";
      }
      if (this.map_data.overlay_setting.height != undefined) {
        this.map_data.overlay_setting.height =
          this.map_data.overlay_setting.height.replace("px", "");
      } else {
        this.map_data.overlay_setting.height = "200";
      }

      this.map.overlayMapTypes.insertAt(
        0,
        this.set_overlay_generator(
          new google.maps.Size(
            this.map_data.overlay_setting.width,
            this.map_data.overlay_setting.height
          ),
          this.map_data.overlay_setting
        )
      );
    }

    set_bicyle_layer() {
      var bikeLayer = new google.maps.BicyclingLayer();
      bikeLayer.setMap(this.map);
    }

    set_traffic_layer() {
      var traffic_layer = new google.maps.TrafficLayer();
      traffic_layer.setMap(this.map);
    }

    set_panoramic_layer() {
      var panoramic_layer = new google.maps.panoramio.PanoramioLayer();
      panoramic_layer.setMap(this.map);
    }

    set_transit_layer() {
      var transit_layer = new google.maps.TransitLayer();
      transit_layer.setMap(this.map);
    }

    set_weather_layer() {
      var weatherLayer = new google.maps.weather.WeatherLayer({
        windSpeedUnit: eval(
          "google.maps.weather.WindSpeedUnit." +
            this.map_data.weather_layer.wind_unit
        ),
        temperatureUnits: eval(
          "google.maps.weather.TemperatureUnit." +
            this.map_data.weather_layer.temperature_unit
        ),
      });

      weatherLayer.setMap(this.map);
      var cloudLayer = new google.maps.weather.CloudLayer();
      cloudLayer.setMap(this.map);
    }

    set_streetview(latlng) {
      var panoOptions = {
        position: latlng,
        addressControlOptions: {
          position: google.maps.ControlPosition.BOTTOM_CENTER,
        },
        linksControl: this.map_data.street_view.links_control,
        panControl: this.map_data.street_view.street_view_pan_control,
        zoomControlOptions: {
          style: google.maps.ZoomControlStyle.SMALL,
        },
        enableCloseButton: this.map_data.street_view.street_view_close_button,
      };
      if (
        this.map_data.street_view.pov_heading &&
        this.map_data.street_view.pov_pitch
      ) {
        panoOptions["pov"] = {
          heading: parseInt(this.map_data.street_view.pov_heading),
          pitch: parseInt(this.map_data.street_view.pov_pitch),
        };
      }
      var panorama = new google.maps.StreetViewPanorama(
        this.element,
        panoOptions
      );
    }
   
    /**
 * Handles post-load setup for Google Maps
 * Includes center logic, infowindow cleanup, amenities load, and UI skinning
 */
map_loaded() {
  const map_obj = this;
  const map = map_obj.map;

  // Ensure Google map is fully loaded and properly centered after resize
  google.maps.event.addListenerOnce(map, "idle", function () {
    const center = map.getCenter();
    google.maps.event.trigger(map, "resize");
    map.setCenter(center);
  });

  // Optionally center map by user's location
  if (map_obj.settings.center_by_nearest === true) {
    map_obj.center_by_nearest();
  }

  // Close all open infowindows on map click
  if (map_obj.settings.close_infowindow_on_map_click === true) {
    google.maps.event.addListener(map, "click", function () {
      map_obj.places?.forEach((place) => {
        place.infowindow?.close();
        place.marker?.setAnimation(null);
      });
    });
  }

 

  // DOM Ready styling for Infobox (if custom infowindow skin is active)
  google.maps.event.addListener(map_obj.infobox, "domready", function () {
    const $outer = $(map_obj.container).find(".infoBox");
    const needsSkin =
      $outer.find(".fc-infowindow-default").length === 0 &&
      $outer.find(".fc-item-default").length === 0 &&
      $outer.find(".wpgmp_infowindow").length > 0;

    //accordian
      $outer.on('click', ".fc-accordion-tab", function () {
          if ($(this).hasClass('active')) {
              $(this).removeClass('active');
              var acc_child = $(this).next().removeClass('active');
          } else {
              $(".fc-accordion-tab").removeClass('active');
              $(".fc-accordion dd").removeClass('active');
              $(this).addClass('active');
              var acc_child = $(this).next().addClass('active');
          }
      });

    if (needsSkin) {
      $outer.find(".wpgmp_infowindow").prepend('<div class="infowindow-close"></div>');

      $(".infowindow-close").click(() => {
        map_obj.places?.forEach((place) => {
          place.infowindow?.close();
          place.marker?.setAnimation(null);
        });
      });

      if (
        $outer.find(".fc-infowindow-fano").length === 0 &&
        $outer.find(".fc-item-fano").length === 0
      ) {
        $outer.addClass("infoBoxTail");
      } else {
        $outer.removeClass("infoBoxTail");
      }
    }
  });

}

    wpgmpInitializeInfoBox() {
      var InfoBox = function (opt_opts) {
        opt_opts = opt_opts || {};

        google.maps.OverlayView.apply(this, arguments);

        // Standard options (in common with google.maps.InfoWindow):
        //
        this.content_ = opt_opts.content || "";
        this.disableAutoPan_ = opt_opts.disableAutoPan || false;
        this.maxWidth_ = opt_opts.maxWidth || 0;
        this.pixelOffset_ = opt_opts.pixelOffset || new google.maps.Size(0, 0);
        this.position_ = opt_opts.position || new google.maps.LatLng(0, 0);
        this.zIndex_ = opt_opts.zIndex || null;

        // Additional options (unique to InfoBox):
        //
        this.boxClass_ = opt_opts.boxClass || "infoBox";
        this.boxStyle_ = opt_opts.boxStyle || {};
        this.closeBoxMargin_ = opt_opts.closeBoxMargin || "2px";
        this.closeBoxURL_ =
          opt_opts.closeBoxURL ||
          "//www.google.com/intl/en_us/mapfiles/close.gif";
        if (opt_opts.closeBoxURL === "") {
          this.closeBoxURL_ = "";
        }
        this.closeBoxTitle_ = opt_opts.closeBoxTitle || " Close ";
        this.infoBoxClearance_ =
          opt_opts.infoBoxClearance || new google.maps.Size(1, 1);

        if (typeof opt_opts.visible === "undefined") {
          if (typeof opt_opts.isHidden === "undefined") {
            opt_opts.visible = true;
          } else {
            opt_opts.visible = !opt_opts.isHidden;
          }
        }
        this.isHidden_ = !opt_opts.visible;

        this.alignBottom_ = opt_opts.alignBottom || false;
        this.pane_ = opt_opts.pane || "floatPane";
        this.enableEventPropagation_ = opt_opts.enableEventPropagation || false;

        this.div_ = null;
        this.closeListener_ = null;
        this.moveListener_ = null;
        this.contextListener_ = null;
        this.eventListeners_ = null;
        this.fixedWidthSet_ = null;
      };

      InfoBox.prototype = new google.maps.OverlayView();

      /**
       * Creates the DIV representing the InfoBox.
       * @private
       */
      InfoBox.prototype.createInfoBoxDiv_ = function () {
        var i;
        var events;
        var bw;
        var me = this;

        // This handler prevents an event in the InfoBox from being passed on to the map.
        //
        var cancelHandler = function (e) {
          e.cancelBubble = true;
          if (e.stopPropagation) {
            e.stopPropagation();
          }
        };

        // This handler ignores the current event in the InfoBox and conditionally prevents
        // the event from being passed on to the map. It is used for the contextmenu event.
        //
        var ignoreHandler = function (e) {
          e.returnValue = false;

          if (e.preventDefault) {
            e.preventDefault();
          }

          if (!me.enableEventPropagation_) {
            cancelHandler(e);
          }
        };

        if (!this.div_) {
          this.div_ = document.createElement("div");

          this.setBoxStyle_();

          if (typeof this.content_.nodeType === "undefined") {
            this.div_.innerHTML = this.getCloseBoxImg_() + this.content_;
          } else {
            this.div_.innerHTML = this.getCloseBoxImg_();
            this.div_.appendChild(this.content_);
          }

          // Add the InfoBox DIV to the DOM
          this.getPanes()[this.pane_].appendChild(this.div_);

          this.addClickHandler_();

          if (this.div_.style.width) {
            this.fixedWidthSet_ = true;
          } else {
            if (
              this.maxWidth_ !== 0 &&
              this.div_.offsetWidth > this.maxWidth_
            ) {
              this.div_.style.width = this.maxWidth_;
              this.div_.style.overflow = "auto";
              this.fixedWidthSet_ = true;
            } else {
              // The following code is needed to overcome problems with MSIE

              bw = this.getBoxWidths_();

              this.div_.style.width =
                this.div_.offsetWidth - bw.left - bw.right + "px";
              this.fixedWidthSet_ = false;
            }
          }

          this.panBox_(this.disableAutoPan_);

          if (!this.enableEventPropagation_) {
            this.eventListeners_ = [];

            // Cancel event propagation.
            //
            // Note: mousemove not included (to resolve Issue 152)
            events = [
              "mousedown",
              "mouseover",
              "mouseout",
              "mouseup",
              "click",
              "dblclick",
              "touchstart",
              "touchend",
              "touchmove",
            ];

            for (i = 0; i < events.length; i++) {
              this.eventListeners_.push(
                this.div_.addEventListener(
                  events[i],
                  cancelHandler
                )
              );
            }

            // Workaround for Google bug that causes the cursor to change to a pointer
            // when the mouse moves over a marker underneath InfoBox.
            this.eventListeners_.push(
              this.div_.addEventListener(
                "mouseover",
                function (e) {
                  this.style.cursor = "default";
                }
              )
            );
          }

          this.contextListener_ = this.div_.addEventListener(
            "contextmenu",
            ignoreHandler
          );

          /**
           * This event is fired when the DIV containing the InfoBox's content is attached to the DOM.
           * @name InfoBox#domready
           * @event
           */
          google.maps.event.trigger(this, "domready");
        }
      };

      /**
       * Returns the HTML <IMG> tag for the close box.
       * @private
       */
      InfoBox.prototype.getCloseBoxImg_ = function () {
        var img = "";

        if (this.closeBoxURL_ !== "") {
          img = "<img";
          img += " src='" + this.closeBoxURL_ + "'";
          img += " align=right"; // Do this because Opera chokes on style='float: right;'
          img += " title='" + this.closeBoxTitle_ + "'";
          img += " style='";
          img += " position: relative;"; // Required by MSIE
          img += " cursor: pointer;";
          img += " margin: " + this.closeBoxMargin_ + ";";
          img += "'>";
        }

        return img;
      };

      /**
       * Adds the click handler to the InfoBox close box.
       * @private
       */
      InfoBox.prototype.addClickHandler_ = function () {
        var closeBox;

        if (this.closeBoxURL_ !== "") {
          closeBox = this.div_.firstChild;
          this.closeListener_ = closeBox.addEventListener(
            "click",
            this.getCloseClickHandler_()
          );
        } else {
          this.closeListener_ = null;
        }
      };

      /**
       * Returns the function to call when the user clicks the close box of an InfoBox.
       * @private
       */
      InfoBox.prototype.getCloseClickHandler_ = function () {
        var me = this;

        return function (e) {
          // 1.0.3 fix: Always prevent propagation of a close box click to the map:
          e.cancelBubble = true;

          if (e.stopPropagation) {
            e.stopPropagation();
          }

          /**
           * This event is fired when the InfoBox's close box is clicked.
           * @name InfoBox#closeclick
           * @event
           */
          google.maps.event.trigger(me, "closeclick");

          me.close();
        };
      };

      /**
       * Pans the map so that the InfoBox appears entirely within the map's visible area.
       * @private
       */
      InfoBox.prototype.panBox_ = function (disablePan) {
        var map;
        var bounds;
        var xOffset = 0,
          yOffset = 0;

        if (!disablePan) {
          map = this.getMap();

          if (map instanceof google.maps.Map) {
            // Only pan if attached to map, not panorama

            if (!map.getBounds().contains(this.position_)) {
              // Marker not in visible area of map, so set center
              // of map to the marker position first.
              map.setCenter(this.position_);
            }

            var iwOffsetX = this.pixelOffset_.width;
            var iwOffsetY = this.pixelOffset_.height;
            var iwWidth = this.div_.offsetWidth;
            var iwHeight = this.div_.offsetHeight;
            var padX = this.infoBoxClearance_.width;
            var padY = this.infoBoxClearance_.height;

            if (map.panToBounds.length == 2) {
              // Using projection.fromLatLngToContainerPixel to compute the infowindow position
              // does not work correctly anymore for JS Maps API v3.32 and above if there is a
              // previous synchronous call that causes the map to animate (e.g. setCenter when
              // the position is not within bounds). Hence, we are using panToBounds with
              // padding instead, which works synchronously.
              var padding = {
                left: 0,
                right: 0,
                top: 0,
                bottom: 0,
              };
              padding.left = -iwOffsetX + padX;
              padding.right = iwOffsetX + iwWidth + padX;
              if (this.alignBottom_) {
                padding.top = -iwOffsetY + padY + iwHeight;
                padding.bottom = iwOffsetY + padY;
              } else {
                padding.top = -iwOffsetY + padY;
                padding.bottom = iwOffsetY + iwHeight + padY;
              }
              map.panToBounds(
                new google.maps.LatLngBounds(this.position_),
                padding
              );
            } else {
              var mapDiv = map.getDiv();
              var mapWidth = mapDiv.offsetWidth;
              var mapHeight = mapDiv.offsetHeight;
              var pixPosition = this.getProjection().fromLatLngToContainerPixel(
                this.position_
              );

              if (pixPosition.x < -iwOffsetX + padX) {
                xOffset = pixPosition.x + iwOffsetX - padX;
              } else if (
                pixPosition.x + iwWidth + iwOffsetX + padX >
                mapWidth
              ) {
                xOffset = pixPosition.x + iwWidth + iwOffsetX + padX - mapWidth;
              }
              if (this.alignBottom_) {
                if (pixPosition.y < -iwOffsetY + padY + iwHeight) {
                  yOffset = pixPosition.y + iwOffsetY - padY - iwHeight;
                } else if (pixPosition.y + iwOffsetY + padY > mapHeight) {
                  yOffset = pixPosition.y + iwOffsetY + padY - mapHeight;
                }
              } else {
                if (pixPosition.y < -iwOffsetY + padY) {
                  yOffset = pixPosition.y + iwOffsetY - padY;
                } else if (
                  pixPosition.y + iwHeight + iwOffsetY + padY >
                  mapHeight
                ) {
                  yOffset =
                    pixPosition.y + iwHeight + iwOffsetY + padY - mapHeight;
                }
              }

              if (!(xOffset === 0 && yOffset === 0)) {
                // Move the map to the shifted center.
                //
                var c = map.getCenter();
                map.panBy(xOffset, yOffset);
              }
            }
          }
        }
      };

      /**
       * Sets the style of the InfoBox by setting the style sheet and applying
       * other specific styles requested.
       * @private
       */
      InfoBox.prototype.setBoxStyle_ = function () {
        var i, boxStyle;

        if (this.div_) {
          // Apply style values from the style sheet defined in the boxClass parameter:
          this.div_.className = this.boxClass_;

          // Clear existing inline style values:
          this.div_.style.cssText = "";

          // Apply style values defined in the boxStyle parameter:
          boxStyle = this.boxStyle_;
          for (i in boxStyle) {
            if (boxStyle.hasOwnProperty(i)) {
              this.div_.style[i] = boxStyle[i];
            }
          }

          // Fix for iOS disappearing InfoBox problem.
          // See http://stackoverflow.com/questions/9229535/google-maps-markers-disappear-at-certain-zoom-level-only-on-iphone-ipad
          // Required: use "matrix" technique to specify transforms in order to avoid this bug.
          if (
            typeof this.div_.style.WebkitTransform === "undefined" ||
            (this.div_.style.WebkitTransform.indexOf("translateZ") === -1 &&
              this.div_.style.WebkitTransform.indexOf("matrix") === -1)
          ) {
            this.div_.style.WebkitTransform = "translateZ(0)";
          }

          // Fix up opacity style for benefit of MSIE:
          //
          if (
            typeof this.div_.style.opacity !== "undefined" &&
            this.div_.style.opacity !== ""
          ) {
            // See http://www.quirksmode.org/css/opacity.html
            this.div_.style.MsFilter =
              '"progid:DXImageTransform.Microsoft.Alpha(Opacity=' +
              this.div_.style.opacity * 100 +
              ')"';
            this.div_.style.filter =
              "alpha(opacity=" + this.div_.style.opacity * 100 + ")";
          }

          // Apply required styles:
          //
          this.div_.style.position = "absolute";
          this.div_.style.visibility = "hidden";
          if (this.zIndex_ !== null) {
            this.div_.style.zIndex = this.zIndex_;
          }
        }
      };

      /**
       * Get the widths of the borders of the InfoBox.
       * @private
       * @return {Object} widths object (top, bottom left, right)
       */
      InfoBox.prototype.getBoxWidths_ = function () {
        var computedStyle;
        var bw = {
          top: 0,
          bottom: 0,
          left: 0,
          right: 0,
        };
        var box = this.div_;

        if (document.defaultView && document.defaultView.getComputedStyle) {
          computedStyle = box.ownerDocument.defaultView.getComputedStyle(
            box,
            ""
          );

          if (computedStyle) {
            // The computed styles are always in pixel units (good!)
            bw.top = parseInt(computedStyle.borderTopWidth, 10) || 0;
            bw.bottom = parseInt(computedStyle.borderBottomWidth, 10) || 0;
            bw.left = parseInt(computedStyle.borderLeftWidth, 10) || 0;
            bw.right = parseInt(computedStyle.borderRightWidth, 10) || 0;
          }
        } else if (document.documentElement.currentStyle) {
          // MSIE

          if (box.currentStyle) {
            // The current styles may not be in pixel units, but assume they are (bad!)
            bw.top = parseInt(box.currentStyle.borderTopWidth, 10) || 0;
            bw.bottom = parseInt(box.currentStyle.borderBottomWidth, 10) || 0;
            bw.left = parseInt(box.currentStyle.borderLeftWidth, 10) || 0;
            bw.right = parseInt(box.currentStyle.borderRightWidth, 10) || 0;
          }
        }

        return bw;
      };

      /**
       * Invoked when <tt>close</tt> is called. Do not call it directly.
       */
      InfoBox.prototype.onRemove = function () {
        if (this.div_) {
          this.div_.parentNode.removeChild(this.div_);
          this.div_ = null;
        }
      };

      /**
       * Draws the InfoBox based on the current map projection and zoom level.
       */
      InfoBox.prototype.draw = function () {
        this.createInfoBoxDiv_();

        var pixPosition = this.getProjection().fromLatLngToDivPixel(
          this.position_
        );

        this.div_.style.left = pixPosition.x + this.pixelOffset_.width + "px";

        if (this.alignBottom_) {
          this.div_.style.bottom =
            -(pixPosition.y + this.pixelOffset_.height) + "px";
        } else {
          this.div_.style.top = pixPosition.y + this.pixelOffset_.height + "px";
        }

        if (this.isHidden_) {
          this.div_.style.visibility = "hidden";
        } else {
          this.div_.style.visibility = "visible";
        }
      };

      /**
       * Sets the options for the InfoBox. Note that changes to the <tt>maxWidth</tt>,
       *  <tt>closeBoxMargin</tt>, <tt>closeBoxTitle</tt>, <tt>closeBoxURL</tt>, and
       *  <tt>enableEventPropagation</tt> properties have no affect until the current
       *  InfoBox is <tt>close</tt>d and a new one is <tt>open</tt>ed.
       * @param {InfoBoxOptions} opt_opts
       */
      InfoBox.prototype.setOptions = function (opt_opts) {
        if (typeof opt_opts.boxClass !== "undefined") {
          // Must be first

          this.boxClass_ = opt_opts.boxClass;
          this.setBoxStyle_();
        }
        if (typeof opt_opts.boxStyle !== "undefined") {
          // Must be second

          this.boxStyle_ = opt_opts.boxStyle;
          this.setBoxStyle_();
        }
        if (typeof opt_opts.content !== "undefined") {
          this.setContent(opt_opts.content);
        }
        if (typeof opt_opts.disableAutoPan !== "undefined") {
          this.disableAutoPan_ = opt_opts.disableAutoPan;
        }
        if (typeof opt_opts.maxWidth !== "undefined") {
          this.maxWidth_ = opt_opts.maxWidth;
        }
        if (typeof opt_opts.pixelOffset !== "undefined") {
          this.pixelOffset_ = opt_opts.pixelOffset;
        }
        if (typeof opt_opts.alignBottom !== "undefined") {
          this.alignBottom_ = opt_opts.alignBottom;
        }
        if (typeof opt_opts.position !== "undefined") {
          this.setPosition(opt_opts.position);
        }
        if (typeof opt_opts.zIndex !== "undefined") {
          this.setZIndex(opt_opts.zIndex);
        }
        if (typeof opt_opts.closeBoxMargin !== "undefined") {
          this.closeBoxMargin_ = opt_opts.closeBoxMargin;
        }
        if (typeof opt_opts.closeBoxURL !== "undefined") {
          this.closeBoxURL_ = opt_opts.closeBoxURL;
        }
        if (typeof opt_opts.closeBoxTitle !== "undefined") {
          this.closeBoxTitle_ = opt_opts.closeBoxTitle;
        }
        if (typeof opt_opts.infoBoxClearance !== "undefined") {
          this.infoBoxClearance_ = opt_opts.infoBoxClearance;
        }
        if (typeof opt_opts.isHidden !== "undefined") {
          this.isHidden_ = opt_opts.isHidden;
        }
        if (typeof opt_opts.visible !== "undefined") {
          this.isHidden_ = !opt_opts.visible;
        }
        if (typeof opt_opts.enableEventPropagation !== "undefined") {
          this.enableEventPropagation_ = opt_opts.enableEventPropagation;
        }

        if (this.div_) {
          this.draw();
        }
      };

      /**
       * Sets the content of the InfoBox.
       *  The content can be plain text or an HTML DOM node.
       * @param {string|Node} content
       */
      InfoBox.prototype.setContent = function (content) {
        this.content_ = content;

        if (this.div_) {
          if (this.closeListener_) {
            google.maps.event.removeListener(this.closeListener_);
            this.closeListener_ = null;
          }

          // Odd code required to make things work with MSIE.
          //
          if (!this.fixedWidthSet_) {
            this.div_.style.width = "";
          }

          if (typeof content.nodeType === "undefined") {
            this.div_.innerHTML = this.getCloseBoxImg_() + content;
          } else {
            this.div_.innerHTML = this.getCloseBoxImg_();
            this.div_.appendChild(content);
          }

          // Perverse code required to make things work with MSIE.
          // (Ensures the close box does, in fact, float to the right.)
          //
          if (!this.fixedWidthSet_) {
            this.div_.style.width = this.div_.offsetWidth + "px";
            if (typeof content.nodeType === "undefined") {
              this.div_.innerHTML = this.getCloseBoxImg_() + content;
            } else {
              this.div_.innerHTML = this.getCloseBoxImg_();
              this.div_.appendChild(content);
            }
          }

          this.addClickHandler_();
        }

        /**
         * This event is fired when the content of the InfoBox changes.
         * @name InfoBox#content_changed
         * @event
         */
        google.maps.event.trigger(this, "content_changed");
      };

      /**
       * Sets the geographic location of the InfoBox.
       * @param {LatLng} latlng
       */
      InfoBox.prototype.setPosition = function (latlng) {
        this.position_ = latlng;

        if (this.div_) {
          this.draw();
        }

        /**
         * This event is fired when the position of the InfoBox changes.
         * @name InfoBox#position_changed
         * @event
         */
        google.maps.event.trigger(this, "position_changed");
      };

      /**
       * Sets the zIndex style for the InfoBox.
       * @param {number} index
       */
      InfoBox.prototype.setZIndex = function (index) {
        this.zIndex_ = index;

        if (this.div_) {
          this.div_.style.zIndex = index;
        }

        /**
         * This event is fired when the zIndex of the InfoBox changes.
         * @name InfoBox#zindex_changed
         * @event
         */
        google.maps.event.trigger(this, "zindex_changed");
      };

      /**
       * Sets the visibility of the InfoBox.
       * @param {boolean} isVisible
       */
      InfoBox.prototype.setVisible = function (isVisible) {
        this.isHidden_ = !isVisible;
        if (this.div_) {
          this.div_.style.visibility = this.isHidden_ ? "hidden" : "visible";
        }
      };

      /**
       * Returns the content of the InfoBox.
       * @returns {string}
       */
      InfoBox.prototype.getContent = function () {
        return this.content_;
      };

      /**
       * Returns the geographic location of the InfoBox.
       * @returns {LatLng}
       */
      InfoBox.prototype.getPosition = function () {
        return this.position_;
      };

      /**
       * Returns the zIndex for the InfoBox.
       * @returns {number}
       */
      InfoBox.prototype.getZIndex = function () {
        return this.zIndex_;
      };

      /**
       * Returns a flag indicating whether the InfoBox is visible.
       * @returns {boolean}
       */
      InfoBox.prototype.getVisible = function () {
        var isVisible;

        if (typeof this.getMap() === "undefined" || this.getMap() === null) {
          isVisible = false;
        } else {
          isVisible = !this.isHidden_;
        }
        return isVisible;
      };

      /**
       * Returns the width of the InfoBox in pixels.
       * @returns {number}
       */
      InfoBox.prototype.getWidth = function () {
        var width = null;

        if (this.div_) {
          width = this.div_.offsetWidth;
        }

        return width;
      };

      /**
       * Returns the height of the InfoBox in pixels.
       * @returns {number}
       */
      InfoBox.prototype.getHeight = function () {
        var height = null;

        if (this.div_) {
          height = this.div_.offsetHeight;
        }

        return height;
      };

      /**
       * Shows the InfoBox. [Deprecated; use <tt>setVisible</tt> instead.]
       */
      InfoBox.prototype.show = function () {
        this.isHidden_ = false;
        if (this.div_) {
          this.div_.style.visibility = "visible";
        }
      };

      /**
       * Hides the InfoBox. [Deprecated; use <tt>setVisible</tt> instead.]
       */
      InfoBox.prototype.hide = function () {
        this.isHidden_ = true;
        if (this.div_) {
          this.div_.style.visibility = "hidden";
        }
      };

      /**
       * Adds the InfoBox to the specified map or Street View panorama. If <tt>anchor</tt>
       *  (usually a <tt>google.maps.Marker</tt>) is specified, the position
       *  of the InfoBox is set to the position of the <tt>anchor</tt>. If the
       *  anchor is dragged to a new location, the InfoBox moves as well.
       * @param {Map|StreetViewPanorama} map
       * @param {MVCObject} [anchor]
       */
      InfoBox.prototype.open = function (map, anchor) {
        var me = this;

        if (anchor) {
          this.setPosition(anchor.getPosition()); // BUG FIX 2/17/2018: needed for v3.32
          this.moveListener_ = google.maps.event.addListener(
            anchor,
            "position_changed",
            function () {
              me.setPosition(this.getPosition());
            }
          );
        }

        this.setMap(map);

        if (this.div_) {
          this.panBox_(this.disableAutoPan_); // BUG FIX 2/17/2018: add missing parameter
        }
      };

      /**
       * Removes the InfoBox from the map.
       */
      InfoBox.prototype.close = function () {
        var i;

        if (this.closeListener_) {
          google.maps.event.removeListener(this.closeListener_);
          this.closeListener_ = null;
        }

        if (this.eventListeners_) {
          for (i = 0; i < this.eventListeners_.length; i++) {
            google.maps.event.removeListener(this.eventListeners_[i]);
          }
          this.eventListeners_ = null;
        }

        if (this.moveListener_) {
          google.maps.event.removeListener(this.moveListener_);
          this.moveListener_ = null;
        }

        if (this.contextListener_) {
          google.maps.event.removeListener(this.contextListener_);
          this.contextListener_ = null;
        }

        this.setMap(null);
      };

      return new InfoBox();
    }
  }

  window.WpgmpGoogleMaps = WpgmpGoogleMaps;
})(jQuery, window, document);

/**
 * WpgmpLeafletMaps class - extends BaseMaps for Leaflet provider.
 */

(function ($, window, document) {
  class WpgmpLeafletMaps extends WpgmpBaseMaps {
    constructor(element, map_data = {}, places = []) {
      super(element, map_data, places);

      const options = this.map_data.map_options;
      this.settings = $.extend(
        {
          min_zoom: "0",

          max_zoom: "19",

          zoom: "5",

          map_type_id: "mapbox.streets",

          scroll_wheel: true,

          map_visual_refresh: false,

          full_screen_control: false,

          full_screen_control_position: "bottomright",

          zoom_control: true,

          zoom_control_style: "SMALL",

          zoom_control_position: "topleft",

          map_type_control: true,

          map_type_control_style: "HORIZONTAL_BAR",

          map_type_control_position: "topright",

          scale_control: true,

          overview_map_control: true,

          center_lat: "40.6153983",

          center_lng: "-74.2535216",

          draggable: true,

          gesture: "auto",

          infowindow_open_event: "click",
          map_tile_url: "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
          search_control: false,
          locateme_control: false,
          map_type_control: false,
        },
        {},
        options
      );

      this.container = $("div[rel='" + $(this.element).attr("id") + "']");

      this.places = [];

      this.show_places = [];

      this.categories = {};

      this.tabs = [];

      this.per_page_value = 0;

      this.last_remove_cat_id = "";

      this.last_selected_cat_id = "";

      this.last_category_chkbox_action = "";

      this.search_area = "";

      this.url_filters = [];

      this.mbAttr =
        'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>';

      this.mbURL = "";

      this.isMobile = false;

      this.bingmaplayers = {};
      this.infowindow_marker = L.popup({
        closeOnClick: this.settings.close_infowindow_on_map_click === true,
      });
    }

    /**
     * Initializes and renders the Leaflet map with full configuration.
     *
     * Breakdown of steps:
     * 1. Device & screen detection with mobile-specific settings
     * 2. Leaflet map initialization using selected provider (Mapbox, or Custom Tile)
     * 3. Adds common map controls (zoom, scale, fullscreen, locate)
     * 4. Registers dynamic map style switchers and dropdown UI controls
     * 5. Builds and displays markers, adjusts layout responsively
     * 6. Sets up listings, filtering, sorting, and pagination
     * 7. Handles GeoJSON, marker interactions, and scroll-to behavior
     * 8. Configures category/location tabs, filter checkboxes, and dynamic content toggles
     * 9. Enables accordion UI, autosuggest search, and direction panel logic
     * 10. Registers all final event listeners (print, radius change, per-page switch, etc.)
     */

    init() {
      const map_obj = this;

      // Fix default Leaflet marker icon path
      delete L.Icon.Default.prototype._getIconUrl;

      L.Icon.Default.mergeOptions({
        iconRetinaUrl: wpgmp_local.default_marker_icon,
        iconUrl: wpgmp_local.default_marker_icon,
        shadowUrl: wpgmp_local.default_marker_icon,
      });

      // --------------------------------------------------
      // 1. Device Detection and Responsive Settings
      // --------------------------------------------------
      const screenWidth = $(window).width();
      const screenType =
        screenWidth <= 480
          ? "smartphones"
          : screenWidth <= 768
          ? "ipads"
          : screenWidth >= 1824
          ? "large-screens"
          : "desktop";

      map_obj.isMobile =
        screenType !== "desktop" && map_obj.settings.mobile_specific === true;

      if (map_obj.isMobile) {
        const screenConfig = map_obj.settings.screens?.[screenType];

        if (screenConfig) {
          Object.assign(map_obj.settings, {
            width_mobile: screenConfig.map_width_mobile,
            height_mobile: screenConfig.map_height_mobile,
            zoom: parseInt(screenConfig.map_zoom_level_mobile),
            draggable: screenConfig.map_draggable_mobile !== "false",
            scroll_wheel: screenConfig.map_scrolling_wheel_mobile !== "false",
          });
        } else {
          map_obj.settings.width_mobile = "";
          map_obj.settings.height_mobile = "";
        }

        if (map_obj.settings.width_mobile)
          $(map_obj.element).css("width", map_obj.settings.width_mobile);

        if (map_obj.settings.height_mobile)
          $(map_obj.element).css("height", map_obj.settings.height_mobile);
      }

      // --------------------------------------------------
      // 2. Map Options
      // --------------------------------------------------
      const center = L.latLng(
        map_obj.settings.center_lat,
        map_obj.settings.center_lng
      );

      const options = {
        center,
        zoom: parseInt(map_obj.settings.zoom),
        minZoom: parseInt(map_obj.settings.min_zoom),
        maxZoom: parseInt(map_obj.settings.max_zoom),
        scrollWheelZoom: map_obj.settings.scroll_wheel !== "true",
        doubleClickZoom: map_obj.settings.doubleclickzoom === true,
        dragging: map_obj.settings.draggable,
        zoomControl: false,
        attributionControl: map_obj.settings.attribution_screen_control,
        closePopupOnClick:
          map_obj.settings.close_infowindow_on_map_click === true,
      };

      // --------------------------------------------------
      // 3. Map Provider Setup
      // --------------------------------------------------
      const provider =
        map_obj.settings.tiles_provider ||
        wpgmp_local.tiles_provider ||
        "OpenStreetMap.Mapnik";
      const mapElement = map_obj.element;
      try {
        // Special case: Bing Maps (not supported by Leaflet Providers)
        try {
          // Special case: Bing Maps (not in Leaflet Providers)
          if (provider === "bingmap" && wpgmp_local.wpomp_bingmap_key) {
            const apiKey = wpgmp_local.wpomp_bingmap_key;
            const defaults = { key: apiKey, detectRetina: true };
            map_obj.bingmaplayers = {};

            [
              "Aerial",
              "AerialWithLabelsOnDemand",
              "RoadOnDemand",
              "CanvasDark",
              "CanvasLight",
              "CanvasGray",
            ].forEach((type) => {
              map_obj.bingmaplayers[type] = L.bingLayer({
                ...defaults,
                imagerySet: type,
              });
            });

            map_obj.bingmaplayers["CanvasGray"].addTo(map_obj.map);
          }
          // Standard Leaflet Providers support nested "BasemapAT.grau", "CartoDB.Positron", etc.
          else {
            map_obj.map = L.map(mapElement, options);
            L.tileLayer
              .provider(provider, { accessToken: wpgmp_local.wpgmp_mapbox_key })
              .addTo(map_obj.map);
          }
        } catch (error) {
          console.error(
            "Error loading map tiles from provider:",
            provider,
            error
          );
        }
      } catch (error) {
        console.error("Error initializing tile layer:", error);
      }

      // --------------------------------------------------
      // 4. Controls (Zoom, Scale, Fullscreen, Locate)
      // --------------------------------------------------
      if (map_obj.settings.scale_control) {
        L.control.scale({ position: "bottomleft" }).addTo(map_obj.map);
      }

      if (map_obj.settings.zoom_control === true) {
        L.control
          .zoom({
            position: map_obj.settings.zoom_control_position,
          })
          .addTo(map_obj.map);
      }

      if (map_obj.settings.full_screen_control === true) {
        map_obj.map.addControl(
          new L.Control.Fullscreen({
            position: map_obj.settings.full_screen_control_position,
          })
        );
      }

      if (map_obj.map_data.page === "edit_location") {
        setTimeout(() => map_obj.map.invalidateSize(true), 300);
      }

      // --------------------------------------------------
      // 5. Map Type Control / Style Dropdowns
      // --------------------------------------------------
      if (map_obj.settings.map_type_control) {
        let mapStylesMarkup = map_obj.settings.openstreet_styles_markup;
        if (provider === "mapbox")
          mapStylesMarkup = map_obj.settings.map_box_styles_markup;

        const MapStyleControl = L.Control.extend({
          options: { position: map_obj.settings.map_type_control_position },
          onAdd() {
            const div = L.DomUtil.create("div", "info legend");
            div.innerHTML = mapStylesMarkup;
            div.firstChild.onmousedown = div.firstChild.ondblclick =
              L.DomEvent.stopPropagation;
            return div;
          },
        });

        map_obj.map.addControl(new MapStyleControl());

        // Map style dropdowns for providers
        $(document).on("change", "select.wpomp_map_type", function () {
          const config = map_obj.settings.openstreet_styles[$(this).val()];
          L.tileLayer(config, {
            maxZoom: parseInt(map_obj.settings.max_zoom),
          }).addTo(map_obj.map);
        });

        $(document).on("change", "select.wpomp_mapbox_type", function () {
          const tile_url = `https://api.mapbox.com/styles/v1/mapbox/${$(
            this
          ).val()}/tiles/{z}/{x}/{y}?access_token=${
            wpgmp_local.wpgmp_mapbox_key
          }`;
          L.tileLayer(tile_url, {
            maxZoom: parseInt(map_obj.settings.max_zoom),
          }).addTo(map_obj.map);
        });
      }

      // Further sections to be optimized next:
      // - Marker creation, listing integration, and filters
      // - Event bindings and route controls
      // --------------------------------------------------
      // 6. Marker Creation, Responsive Layout & Listings
      // --------------------------------------------------

      // Create and display all markers
      map_obj.create_markers();
      map_obj.display_markers();

      // Load Google Fonts if defined
      if (typeof map_obj.settings.google_fonts !== "undefined") {
        map_obj.load_google_fonts(map_obj.settings.google_fonts);
      }

      // Capture initial HTML listing content for advanced template
      if (map_obj.map_data.map_options?.advance_template === true) {
        const listingEl = document.querySelector(
          '[data-listing-content="true"][data-wpgmp-map-id]'
        );
        if (listingEl?.innerHTML) {
          map_obj.map_data.listing.listing_placeholder = listingEl.innerHTML;
        }
      }

      // Fire map loaded hook
      map_obj.map_loaded();
      // Adjust layout based on screen/mobile settings
      map_obj.responsive_map();

      // Show search control box if enabled
      if (map_obj.settings.search_control === true) {
        map_obj.show_search_control();
      }

      // Show locateme control if enabled
      if (map_obj.settings.locateme_control === true) {
        map_obj.register_locate_me_control();
      }

      // Further sections to be optimized next:
      // - Listing pagination & sorting
      // - Filter UI and update handlers
      // - Tabs, accordions, events, and route direction logic

      // --------------------------------------------------
      // 7. Listing Pagination, Sorting, and Filters
      // --------------------------------------------------

      const listing = map_obj.map_data.listing;

      // Sort default if defined
      if (listing?.default_sorting) {
        const sort = listing.default_sorting;
        const dataType = sort.orderby === "listorder" ? "num" : "";
        map_obj.sorting(sort.orderby, sort.inorder, dataType);
      }

      // If no listing, but tab filters exist (e.g. category tab sorting)
      const tabs = map_obj.map_data.map_tabs;
      if (!listing && tabs?.category_tab?.cat_tab) {
        const catTab = tabs.category_tab;
        const order = catTab.cat_post_order ?? "asc";
        map_obj.sorting("title", order);
      }

      // Pagination UI
      if (listing) {
        $(map_obj.container).on("click", ".categories_filter_reset_btn", () => {
          $(map_obj.container)
            .find(".wpgmp_filter_wrappers select")
            .each(function () {
              $(this).val($(this).find("option:first").val());
            });

          $(".wpgmp_search_input").val("");
          map_obj.update_filters();
        });

        // Custom filters
        map_obj.display_filters_listing();
        map_obj.custom_filters();

        // Pagination plugin
        $(map_obj.container)
          .find(`.location_pagination${map_obj.map_data.map_property.map_id}`)
          .pagination(map_obj.show_places.length, {
            callback: map_obj.display_places_listing,
            map_data: map_obj,
            items_per_page: listing.pagination.listing_per_page,
            prev_text: wpgmp_local.prev,
            next_text: wpgmp_local.next,
          });
      }

      // Further sections to be optimized next:
      // - Tabs, accordions, direction panels
      // - Marker detail click and scroll to view
      
      // --------------------------------------------------
      // 9. Accordion, Auto Suggest, Directions Panel, Events
      // --------------------------------------------------

      $(map_obj.container)
        .find(".wpgmp-accordion")
        .accordion({ speed: "slow" });

      map_obj.google_auto_suggest($(".wpgmp_auto_suggest"));

      // --------------------------------------------------
      // 10. Final Event Registration
      // --------------------------------------------------

      map_obj.register_events();
    }

    event_listener(obj, type, func) {
      if (obj && typeof obj.on === "function") {
        obj.on(type, func);
      } else {
        console.warn("event_listener: .on() is not a function for", obj);
      }
    }

    create_leaflet_marker({
      map,
      position, // [lat, lng]
      iconUrl = "",
      popupAnchor = [0, -30],
      draggable = false,
      title = "",
      isDivIcon = false,
      label = "",
      customClass = "wpgmp-custom-label-icon",
      addToMap = true,
    }) {
      var map_obj = this;
      let icon;
      var iconSize = map_obj.getMarkerSize();

      if (!iconUrl || iconUrl.trim() === "") {
        iconUrl =
          map_obj.settings.marker_default_icon ||
          wpgmp_local.default_marker_icon;
      }

      const isSvg = iconUrl.toLowerCase().includes('image/svg') || iconUrl.toLowerCase().endsWith('.svg');

      if (isDivIcon) {
        const divIconOptions = {
          className: customClass,
          html: `<div class="wpgmp-marker-label">${label}</div>`,
        };

        if (isSvg) {
          divIconOptions.iconSize = iconSize;
          divIconOptions.iconAnchor = [iconSize[0] / 2, iconSize[1] / 2];
        }

      icon = L.divIcon(divIconOptions);

      } else {
        const iconOptions = {
          iconUrl,
          iconAnchor: [iconSize[0] / 2, iconSize[1]],
          popupAnchor,
        };

        // Only apply iconSize if SVG
        if (isSvg) {
          iconOptions.iconSize = iconSize;
        }

        icon = L.icon(iconOptions);

      }

      const marker = L.marker(position, {
        icon,
        draggable,
        title,
      });

      if (addToMap && map) {
        marker.addTo(map);
      }

      return marker;
    }

    map_loaded() {
      const map_obj = this;
      const map = map_obj.map;

      // Center map using current location or static marker
      if (map_obj.settings.center_by_nearest === true) {
        map_obj.center_by_nearest();
      } else if (map_obj.settings.show_center_marker === true) {
        map_obj.show_center_marker();
      }

      // Show center circle if manually positioned
      if (
        map_obj.settings.center_by_nearest === false &&
        map_obj.settings.show_center_circle === true
      ) {
        map_obj.show_center_circle();
      }

      // Fit bounds to all markers if enabled
      if (map_obj.settings.fit_bounds === true) {
        map_obj.fit_bounds();
      }

      function adjustLeafletPopup(popup) {
        const popupEl = popup.getElement();
        if (!popupEl) return;

        const wrapper = popupEl.querySelector(".leaflet-popup-content-wrapper");
        const content = popupEl.querySelector(".leaflet-popup-content");
        const markerSize = map_obj.getMarkerSize?.() || [32, 32];

        if (wrapper) {
          wrapper.classList.add("wpgmp-infowindow-addon");
          wrapper.removeAttribute("style");
        }
        if (content) {
          content.removeAttribute("style");
        }

        // Calculate the popup width and center it
        const rect = popupEl.getBoundingClientRect();
        const width = rect.width || 200; // fallback if rect is 0
        popupEl.style.left = `-${width / 2}px`;
        popupEl.style.bottom = `${markerSize[1] / 2}px`;
      }
      
      
      // Remove inline styling for Leaflet popups and apply custom class
      map.on("popupopen", function (e) {
        adjustLeafletPopup(e.popup);
      });

      map.on("zoomend", function () {
        const openPopup = map._popup; // Leaflet stores currently open popup here
        if (openPopup) {
          adjustLeafletPopup(openPopup);
        }
      });
      

    }
  }

  window.WpgmpLeafletMaps = WpgmpLeafletMaps;
})(jQuery, window, document);

class WpgmpMapFactory {
  create(provider, element, options, places) {
    // Use fallback if provider is missing
    provider = provider || (options?.provider) || 'google';
    options.provider = provider; // enforce for downstream use

    switch (provider.toLowerCase()) {
      case 'leaflet':
        return new WpgmpLeafletMaps(element, options, places);
      case 'google':
        return new WpgmpGoogleMaps(element, options, places);
      default:
        console.warn(`Unknown provider "${provider}". Falling back to Google Maps.`);
        return new WpgmpGoogleMaps(element, options, places);
    }
  }
}
(function ($, window, document) {
  "use strict";

  $.fn.maps = function (options = {}, places = []) {
    return this.each(function () {
      const stepLabel = `[wpgmp/maps]`;

      if (typeof options === 'string') {
        try {
          options = JSON.parse(atob(options));
          if (wpgmp_local.debug_mode) {
            console.log(`${stepLabel} [Step 1] Options decoded from base64`, options);
          }
        } catch (e) {
          console.error(`${stepLabel} [Step 1] Failed to decode options:`, e);
          options = {};
        }
      }

      if (!$.data(this, "wpgmp_maps")) {
        let provider = wpgmp_local.map_provider || "google";
        if (provider !== "google") {
          provider = "leaflet";
        }

        if (wpgmp_local.debug_mode) {
          console.log(`${stepLabel} [Step 2] Preparing to initialize map...`);
          console.log(`${stepLabel} Provider: ${provider}`);
          console.log(`${stepLabel} Target Element:`, this);
          console.log(`${stepLabel} Options:`, options);
        }

        setTimeout(() => {
          const instance = new WpgmpMapFactory().create(provider, this, options, places);
          instance.init();
          $.data(this, "wpgmp_maps", instance);

          if (wpgmp_local.debug_mode) {
            console.log(`${stepLabel} [Step 3] Map instance created and initialized.`);
          }

          // 🔔 Internal event to signal complete readiness
          if (wpgmp_local.debug_mode) {
            console.log(`${stepLabel} [Step 4] Events dispatched: wpgmpInstanceReady, wpgmpLoaded`);
          }
          const internalEvent = new CustomEvent("wpgmpInstanceReady", {
            detail: { element: this, instance, provider },
          });
          document.dispatchEvent(internalEvent);

          // External/public event
          const externalEvent = new CustomEvent("wpgmpLoaded", {
            detail: { element: this, instance, provider },
          });
          document.dispatchEvent(externalEvent);

          

        }, wpgmp_local.set_timeout);
      } else {
        if (wpgmp_local.debug_mode) {
          console.log(`${stepLabel} [Skipped] Map already initialized on element:`, this);
        }
      }
    });
  };
})(jQuery, window, document);

function wpgmpInitMap() {
  jQuery(document).ready(function ($) {
    var stepLabel = `[wpgmpInitMap]`;

    if (!$.fn.maps) {
      if (wpgmp_local.debug_mode) {
        console.warn(`${stepLabel} [Abort] $.fn.maps not defined`);
      }
      return;
    }

    // ✅ STEP 1: Initialize each map
    $(".wpgmp_map").each(function () {
      stepLabel = `[wpgmpShowMap]`;
      const mapId = $(this).data("map-id");
      const mapVarName = "mapdata" + mapId;
      const mapData = window.wpgmp?.[mapVarName];

      if (mapData) {
        if (wpgmp_local.debug_mode) {
          console.log(`${stepLabel} [Step 1] Map data found for ID: ${mapId}`);
        }
        $("#map" + mapId).maps(mapData);
      } else {
        if (wpgmp_local.debug_mode) {
          console.warn(`${stepLabel} [Step 1] Map data missing for ID: ${mapId}, retrying in ${wpgmp_local.set_timeout}ms...`);
        }

        setTimeout(() => {
          const retryData = window.wpgmp?.[mapVarName];
          if (retryData) {
            if (wpgmp_local.debug_mode) {
              console.log(`${stepLabel} [Step 1] Retry succeeded for ID: ${mapId}`);
            }
            $("#map" + mapId).maps(retryData);
          } else {
            console.error(`${stepLabel} [Step 1] Retry failed: Map data still missing for ID: ${mapId}`);
          }
        }, wpgmp_local.set_timeout);
      }
    });

    // ✅ STEP 2: Listen for internal complete signal
    document.addEventListener("wpgmpInstanceReady", function (e) {
      const { element, instance, provider } = e.detail;
      const mapId = $(element).data("map-id");

      if (wpgmp_local.debug_mode) {
        console.log(`${stepLabel} [Step 2] Map fully initialized.`);
        console.log(`${stepLabel} Map ID: ${mapId}`);
        console.log(`${stepLabel} Provider: ${provider}`);
        console.log(`${stepLabel} Instance:`, instance);
      }
    });

    // ✅ STEP 3: Dispatch global ready signal
    window.wpgmpInitialized = true;
    stepLabel = `[wpgmpInitMap]`;
    setTimeout(() => {
      if (wpgmp_local.debug_mode) {
        console.log(`${stepLabel} [Step 1] Global event dispatched: wpgmpReady`);
      }
      document.dispatchEvent(new Event("wpgmpReady"));
    }, wpgmp_local.set_timeout);
  });
}