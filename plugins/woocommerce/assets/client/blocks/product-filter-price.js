(()=>{var e,t,o,r={644:(e,t,o)=>{"use strict";o.r(t);const r=window.wp.blocks,n=window.wc.wcSettings;var c=o(1609),l=o(5573);const i=JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","name":"woocommerce/product-filter-price","version":"1.0.0","title":"Price (Experimental)","description":"Let shoppers filter products by choosing a price range.","category":"woocommerce","keywords":["WooCommerce"],"textdomain":"woocommerce","apiVersion":3,"ancestor":["woocommerce/product-filters"],"supports":{"interactivity":true,"html":false},"usesContext":["query","filterParams"],"attributes":{"clearButton":{"type":"boolean","default":true}}}'),s=window.wp.blockEditor;var a=o(6087),u=o(4717);const p=window.wc.wcTypes;var d=o(5574),m=o(923),f=o.n(m);function w(e){const t=(0,a.useRef)(e);return f()(e,t.current)||(t.current=e),t.current}const v=window.wc.wcBlocksData,k=window.wp.data,g=(0,a.createContext)("page"),y=()=>(0,a.useContext)(g),b=(g.Provider,e=>{const t=y();e=e||t;const o=(0,k.useSelect)((t=>t(v.QUERY_STATE_STORE_KEY).getValueForQueryContext(e,void 0)),[e]),{setValueForQueryContext:r}=(0,k.useDispatch)(v.QUERY_STATE_STORE_KEY);return[o,(0,a.useCallback)((t=>{r(e,t)}),[e,r])]}),C=(e,t,o)=>{const r=y();o=o||r;const n=(0,k.useSelect)((r=>r(v.QUERY_STATE_STORE_KEY).getValueForQueryKey(o,e,t)),[o,e]),{setQueryValue:c}=(0,k.useDispatch)(v.QUERY_STATE_STORE_KEY);return[n,(0,a.useCallback)((t=>{c(o,e,t)}),[o,e,c])]},_=({queryAttribute:e,queryPrices:t,queryStock:o,queryRating:r,queryState:n,isEditor:c=!1})=>{let l=y();l=`${l}-collection-data`;const[i]=b(l),[s,m]=C("calculate_attribute_counts",[],l),[f,g]=C("calculate_price_range",null,l),[_,B]=C("calculate_stock_status_counts",null,l),[h,E]=C("calculate_rating_counts",null,l),x=w(e||{}),P=w(t),S=w(o),O=w(r);(0,a.useEffect)((()=>{"object"==typeof x&&Object.keys(x).length&&(s.find((e=>(0,p.objectHasProp)(x,"taxonomy")&&e.taxonomy===x.taxonomy))||m([...s,x]))}),[x,s,m]),(0,a.useEffect)((()=>{f!==P&&void 0!==P&&g(P)}),[P,g,f]),(0,a.useEffect)((()=>{_!==S&&void 0!==S&&B(S)}),[S,B,_]),(0,a.useEffect)((()=>{h!==O&&void 0!==O&&E(O)}),[O,E,h]);const[I,T]=(0,a.useState)(c),[j]=(0,u.d7)(I,200);I||T(!0);const R=(0,a.useMemo)((()=>(e=>{const t=e;return Array.isArray(e.calculate_attribute_counts)&&(t.calculate_attribute_counts=(0,d.di)(e.calculate_attribute_counts.map((({taxonomy:e,queryType:t})=>({taxonomy:e,query_type:t})))).asc(["taxonomy","query_type"])),t})(i)),[i]),{results:q,isLoading:V}=(e=>{const{namespace:t,resourceName:o,resourceValues:r=[],query:n={},shouldSelect:c=!0}=e;if(!t||!o)throw new Error("The options object must have valid values for the namespace and the resource properties.");const l=(0,a.useRef)({results:[],isLoading:!0}),i=w(n),s=w(r),u=(()=>{const[,e]=(0,a.useState)();return(0,a.useCallback)((t=>{e((()=>{throw t}))}),[])})(),d=(0,k.useSelect)((e=>{if(!c)return null;const r=e(v.COLLECTIONS_STORE_KEY),n=[t,o,i,s],l=r.getCollectionError(...n);if(l){if(!(0,p.isError)(l))throw new Error("TypeError: `error` object is not an instance of Error constructor");u(l)}return{results:r.getCollection(...n),isLoading:!r.hasFinishedResolution("getCollection",n)}}),[t,o,s,i,c,u]);return null!==d&&(l.current=d),l.current})({namespace:"/wc/store/v1",resourceName:"products/collection-data",query:{...n,page:void 0,per_page:void 0,orderby:void 0,order:void 0,...R},shouldSelect:j});return{data:q,isLoading:V}};var B=o(7723);const h=window.wp.components,E=(e=[])=>(0,r.getBlockTypes)().map((e=>e.name)).filter((t=>!e.includes(t))),x=window.wc.priceFormat;function P(e,t){return("number"==typeof e?e:parseInt(e,10))/10**t.minorUnit}function S(e){if(!(0,p.objectHasProp)(e,"price_range"))return{minPrice:0,maxPrice:0,minRange:0,maxRange:0};const t=(0,x.getCurrencyFromPriceResponse)(e.price_range),o=(0,p.objectHasProp)(e.price_range,"min_price")&&(0,p.isString)(e.price_range.min_price)?P(e.price_range.min_price,t):0,r=(0,p.objectHasProp)(e.price_range,"max_price")&&(0,p.isString)(e.price_range.max_price)?P(e.price_range.max_price,t):0;return{minPrice:o,maxPrice:r,minRange:o,maxRange:r}}o(9498);const O=({children:e})=>(0,c.createElement)("div",{className:"wc-block-product-filter-components-initial-disabled"},(0,c.createElement)("div",{className:"wc-block-product-filter-components-initial-disabled-overlay"}),e),I=e=>{if(!e)return;const{getBlock:t,getBlockParents:o,getBlockOrder:r}=(0,k.select)(s.store),n=o(e,!0),c=n.length?t(n[0]):null,l=r(null==c?void 0:c.clientId);return{blockPositionIndex:null==l?void 0:l.findIndex((t=>t===e)),parentBlockId:null==c?void 0:c.clientId}},T=(e,t)=>{if(e){if(e.name===t)return e.clientId;if(e.innerBlocks&&e.innerBlocks.length>0)for(const o of e.innerBlocks){const e=T(o,t);if(e)return e}}},j=(e,t)=>{if(!e)return null;if(0===e.innerBlocks.length)return null;for(const o of e.innerBlocks){if(t(o))return o;const e=j(o,t);if(e)return e}return null},R={lock:{remove:!0,move:!1}},q=(()=>{const e=function({clientId:t,showClearButton:o,positionIndexToInsertBlock:n,parentClientIdToInsertBlock:c}){const{clearButtonBlock:l}=(e=>{const{getBlock:t}=(0,k.select)(s.store),o=t(e),r=T(o,"woocommerce/product-filter-clear-button");return{clearButtonBlock:r?t(r):void 0}})(t);void 0===e.previousClearButtonBlockPosition&&(e.previousClearButtonBlockPosition=I(null==l?void 0:l.clientId));const{previousClearButtonBlockPosition:i}=e,a=I(null==l?void 0:l.clientId),{getBlock:u}=(0,k.select)(s.store),{insertBlock:p,removeBlock:d,updateBlockAttributes:m}=(0,k.dispatch)(s.store);function f(){e.previousClearButtonBlockPosition=void 0}var w;if(!1===o&&Boolean(null==l?void 0:l.clientId)&&(m(null==l?void 0:l.clientId,{lock:{remove:!1,move:!1}}),d(null==l?void 0:l.clientId,!1),w=a,e.previousClearButtonBlockPosition=w),!0===o&&!l){let e=function(){if(i&&u(i.parentBlockId)){const{blockPositionIndex:e,parentBlockId:t}=i;return p((0,r.createBlock)("woocommerce/product-filter-clear-button",R),e,t,!1),f(),!0}return!1}();e||(e=void 0===n&&void 0===c&&!!u(c)&&(p((0,r.createBlock)("woocommerce/product-filter-clear-button",R),n,c,!1),f(),!0)),e||(e=function(){const e=u(t),o=("core/group",j(e,(function(e){return"core/group"===e.name})));if(!o)return!1;const n=T(o,"core/heading"),c=o.innerBlocks.length;return!!Boolean(n)&&(p((0,r.createBlock)("woocommerce/product-filter-clear-button",R),c,null==o?void 0:o.clientId,!1),!0)}()),e||(p((0,r.createBlock)("woocommerce/product-filter-clear-button",R),0,t,!1),f(),e=!0)}};return e})();(()=>{const{experimentalBlocksEnabled:e}=(0,n.getSetting)("wcBlocksConfig",{experimentalBlocksEnabled:!1});return e})()&&(0,r.registerBlockType)(i,{icon:()=>(0,c.createElement)(l.SVG,{width:"24",height:"24",viewBox:"0 0 24 24",fill:"none",xmlns:"http://www.w3.org/2000/svg"},(0,c.createElement)("line",{x1:"4",y1:"15.25",x2:"20",y2:"15.25",stroke:"currentColor",strokeWidth:"1.5"}),(0,c.createElement)("line",{x1:"4",y1:"19.25",x2:"13",y2:"19.25",stroke:"currentColor",strokeWidth:"1.5"}),(0,c.createElement)(l.Circle,{cx:"8.75",cy:"7.75",r:"4.75",stroke:"currentColor",strokeWidth:"1.4",fill:"none"}),(0,c.createElement)(l.Path,{d:"M7.99278 6.34419C7.8133 6.46983 7.75717 6.60229 7.75717 6.7002C7.75717 6.79811 7.8133 6.93057 7.99278 7.0562C8.17091 7.18089 8.43983 7.27162 8.75717 7.27162C9.2288 7.27162 9.67418 7.40475 10.0131 7.64199C10.3506 7.87828 10.6143 8.24582 10.6143 8.7002C10.6143 9.15457 10.3506 9.52211 10.0131 9.7584C9.77922 9.92211 9.49465 10.0362 9.18574 10.0913V10.2716C9.18574 10.5083 8.99386 10.7002 8.75717 10.7002C8.52047 10.7002 8.3286 10.5083 8.3286 10.2716V10.0913C8.01968 10.0362 7.73512 9.92211 7.50125 9.7584C7.16369 9.52211 6.90002 9.15457 6.90002 8.70019C6.90002 8.4635 7.0919 8.27162 7.3286 8.27162C7.56529 8.27162 7.75717 8.4635 7.75717 8.7002C7.75717 8.79811 7.8133 8.93057 7.99278 9.0562C8.17091 9.18089 8.43983 9.27162 8.75717 9.27162C9.07451 9.27162 9.34342 9.18089 9.52155 9.0562C9.70103 8.93057 9.75717 8.79811 9.75717 8.7002C9.75717 8.60229 9.70103 8.46983 9.52155 8.34419C9.34342 8.2195 9.07451 8.12877 8.75717 8.12877C8.28553 8.12877 7.84016 7.99564 7.50125 7.7584C7.16369 7.52211 6.90002 7.15457 6.90002 6.7002C6.90002 6.24582 7.16369 5.87828 7.50125 5.64199C7.73512 5.47828 8.01969 5.36415 8.3286 5.30912V5.12877C8.3286 4.89207 8.52047 4.7002 8.75717 4.7002C8.99386 4.7002 9.18574 4.89207 9.18574 5.12877V5.30912C9.49465 5.36415 9.77922 5.47828 10.0131 5.64199C10.3506 5.87828 10.6143 6.24582 10.6143 6.7002C10.6143 6.93689 10.4224 7.12877 10.1857 7.12877C9.94905 7.12877 9.75717 6.93689 9.75717 6.7002C9.75717 6.60229 9.70103 6.46983 9.52155 6.34419C9.34342 6.2195 9.07451 6.12877 8.75717 6.12877C8.43983 6.12877 8.17091 6.2195 7.99278 6.34419Z",fill:"currentColor"})),edit:e=>{const{attributes:t,setAttributes:o,clientId:r}=e,{clearButton:n}=t,l=(0,s.useBlockProps)(),{data:i,isLoading:a}=_({queryPrices:!0,queryState:{},isEditor:!0});return(0,c.createElement)("div",{...l},(0,c.createElement)(s.InspectorControls,{group:"styles"},(0,c.createElement)(h.PanelBody,{title:(0,B.__)("Display","woocommerce")},(0,c.createElement)(h.ToggleControl,{label:(0,B.__)("Clear button","woocommerce"),checked:n,onChange:e=>{o({clearButton:e}),q({clientId:r,showClearButton:e})}}))),(0,c.createElement)(O,null,(0,c.createElement)(s.BlockContextProvider,{value:{filterData:{price:S(i),isLoading:a}}},(0,c.createElement)(s.InnerBlocks,{allowedBlocks:E(),template:[["core/group",{layout:{type:"flex",flexWrap:"nowrap"},metadata:{name:(0,B.__)("Header","woocommerce")},style:{spacing:{blockGap:"0"}}},[["core/heading",{level:4,content:(0,B.__)("Price","woocommerce")}],n?["woocommerce/product-filter-clear-button",{lock:{remove:!0,move:!1}}]:null].filter(Boolean)],["woocommerce/product-filter-price-slider",{}]]}))))},save:()=>{const e=s.useBlockProps.save(),t=s.useInnerBlocksProps.save(e);return(0,c.createElement)("div",{...t})}})},9498:()=>{},1609:e=>{"use strict";e.exports=window.React},6087:e=>{"use strict";e.exports=window.wp.element},7723:e=>{"use strict";e.exports=window.wp.i18n},923:e=>{"use strict";e.exports=window.wp.isShallowEqual},5573:e=>{"use strict";e.exports=window.wp.primitives}},n={};function c(e){var t=n[e];if(void 0!==t)return t.exports;var o=n[e]={exports:{}};return r[e].call(o.exports,o,o.exports,c),o.exports}c.m=r,e=[],c.O=(t,o,r,n)=>{if(!o){var l=1/0;for(u=0;u<e.length;u++){for(var[o,r,n]=e[u],i=!0,s=0;s<o.length;s++)(!1&n||l>=n)&&Object.keys(c.O).every((e=>c.O[e](o[s])))?o.splice(s--,1):(i=!1,n<l&&(l=n));if(i){e.splice(u--,1);var a=r();void 0!==a&&(t=a)}}return t}n=n||0;for(var u=e.length;u>0&&e[u-1][2]>n;u--)e[u]=e[u-1];e[u]=[o,r,n]},c.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return c.d(t,{a:t}),t},o=Object.getPrototypeOf?e=>Object.getPrototypeOf(e):e=>e.__proto__,c.t=function(e,r){if(1&r&&(e=this(e)),8&r)return e;if("object"==typeof e&&e){if(4&r&&e.__esModule)return e;if(16&r&&"function"==typeof e.then)return e}var n=Object.create(null);c.r(n);var l={};t=t||[null,o({}),o([]),o(o)];for(var i=2&r&&e;"object"==typeof i&&!~t.indexOf(i);i=o(i))Object.getOwnPropertyNames(i).forEach((t=>l[t]=()=>e[t]));return l.default=()=>e,c.d(n,l),n},c.d=(e,t)=>{for(var o in t)c.o(t,o)&&!c.o(e,o)&&Object.defineProperty(e,o,{enumerable:!0,get:t[o]})},c.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),c.r=e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},c.j=5303,(()=>{var e={5303:0};c.O.j=t=>0===e[t];var t=(t,o)=>{var r,n,[l,i,s]=o,a=0;if(l.some((t=>0!==e[t]))){for(r in i)c.o(i,r)&&(c.m[r]=i[r]);if(s)var u=s(c)}for(t&&t(o);a<l.length;a++)n=l[a],c.o(e,n)&&e[n]&&e[n][0](),e[n]=0;return c.O(u)},o=self.webpackChunkwebpackWcBlocksMainJsonp=self.webpackChunkwebpackWcBlocksMainJsonp||[];o.forEach(t.bind(null,0)),o.push=t.bind(null,o.push.bind(o))})();var l=c.O(void 0,[94],(()=>c(644)));l=c.O(l),((this.wc=this.wc||{}).blocks=this.wc.blocks||{})["product-filter-price"]=l})();