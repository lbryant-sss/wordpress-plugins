"use strict";(globalThis.webpackChunkwebpackWcBlocksCartCheckoutFrontendJsonp=globalThis.webpackChunkwebpackWcBlocksCartCheckoutFrontendJsonp||[]).push([[6073],{5299:(e,t,o)=>{o.d(t,{A:()=>c});var s=o(7723);const c=({defaultTitle:e=(0,s.__)("Step","woocommerce"),defaultDescription:t=(0,s.__)("Step description text.","woocommerce"),defaultShowStepNumber:o=!0})=>({title:{type:"string",default:e},description:{type:"string",default:t},showStepNumber:{type:"boolean",default:o}})},6431:(e,t,o)=>{o.r(t),o.d(t,{default:()=>k});var s=o(4921),c=o(5460),n=o(1616),r=o(4656),a=o(7143),i=o(7594),l=o(8696),d=o(4199),p=o(4914),u=o(790);const m=({noPaymentMethods:e})=>(0,u.jsx)(p.A,{noPaymentMethods:e});var h=o(7723);const b={...(0,o(5299).A)({defaultTitle:(0,h.__)("Payment options","woocommerce"),defaultDescription:""}),className:{type:"string",default:""},lock:{type:"object",default:{move:!0,remove:!0}}},k=(0,n.withFilteredAttributes)(b)((({title:e,description:t,children:o,className:n})=>{const{showFormStepNumbers:p}=(0,d.O)(),h=(0,a.useSelect)((e=>e(i.checkoutStore).isProcessing())),{cartNeedsPayment:b}=(0,c.V)();return b?(0,u.jsxs)(r.FormStep,{id:"payment-method",disabled:h,className:(0,s.A)("wc-block-checkout__payment-method",n),title:e,description:t,showStepNumber:p,children:[(0,u.jsx)(r.StoreNoticesContainer,{context:l.tG.PAYMENTS}),(0,u.jsx)(m,{}),o]}):null}))}}]);