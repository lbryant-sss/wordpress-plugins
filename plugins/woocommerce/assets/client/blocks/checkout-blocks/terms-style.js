"use strict";(self.webpackChunkwebpackWcBlocksStylingJsonp=self.webpackChunkwebpackWcBlocksStylingJsonp||[]).push([[4654],{66777:(e,t,s)=>{s.d(t,{w:()=>o});var n=s(47594),c=s(47143),a=s(1614),r=s(66379);const o=()=>{const{isCalculating:e,isBeforeProcessing:t,isProcessing:s,isAfterProcessing:o,isComplete:i,hasError:l}=(0,c.useSelect)((e=>{const t=e(n.CHECKOUT_STORE_KEY);return{isCalculating:t.isCalculating(),isBeforeProcessing:t.isBeforeProcessing(),isProcessing:t.isProcessing(),isAfterProcessing:t.isAfterProcessing(),isComplete:t.isComplete(),hasError:t.hasError()}})),{activePaymentMethod:d,isExpressPaymentMethodActive:m}=(0,c.useSelect)((e=>{const t=e(n.PAYMENT_STORE_KEY);return{activePaymentMethod:t.getActivePaymentMethod(),isExpressPaymentMethodActive:t.isExpressPaymentMethodActive()}})),{onSubmit:u}=(0,a.E)(),{paymentMethods:h={}}=(0,r.m)(),E=s||o||t,g=i&&!l;return{paymentMethodButtonLabel:(h[d]||{}).placeOrderButtonLabel,onSubmit:u,isCalculating:e,isDisabled:s||m,waitingForProcessing:E,waitingForRedirect:g}}},7214:(e,t,s)=>{s.r(t),s.d(t,{default:()=>E});var n=s(51609),c=s(27723),a=s(70851),r=s(86087),o=s(14656),i=s(66777),l=s(29491),d=s(47143),m=s(47594),u=s(15995),h=s(41360);const E=(0,l.withInstanceId)((({text:e,checkbox:t,instanceId:s,className:l,showSeparator:E})=>{const[g,p]=(0,r.useState)(!1),{isDisabled:_}=(0,i.w)(),b="terms-and-conditions-"+s,{setValidationErrors:k,clearValidationError:P}=(0,d.useDispatch)(m.VALIDATION_STORE_KEY),w=(0,d.useSelect)((e=>e(m.VALIDATION_STORE_KEY).getValidationError(b))),S=!(null==w||!w.message||null!=w&&w.hidden);return(0,r.useEffect)((()=>{if(t)return g?P(b):k({[b]:{message:(0,c.__)("Please read and accept the terms and conditions.","woocommerce"),hidden:!0}}),()=>{P(b)}}),[t,g,b,P,k]),(0,n.createElement)(n.Fragment,null,(0,n.createElement)(h.VM,null),(0,n.createElement)("div",{className:(0,a.A)("wc-block-checkout__terms",{"wc-block-checkout__terms--disabled":_,"wc-block-checkout__terms--with-separator":"false"!==E&&!1!==E},l)},t?(0,n.createElement)(n.Fragment,null,(0,n.createElement)(o.CheckboxControl,{id:"terms-and-conditions",checked:g,onChange:()=>p((e=>!e)),hasError:S,disabled:_},(0,n.createElement)("span",{className:"wc-block-components-checkbox__label",dangerouslySetInnerHTML:{__html:e||u.R}}))):(0,n.createElement)("span",{className:"wc-block-components-checkbox__label",dangerouslySetInnerHTML:{__html:e||u.G}})))}))}}]);