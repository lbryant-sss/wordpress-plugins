'use strict';

Object.defineProperty(exports, '__esModule', { value: true });

const index = require('./index-8acc3c89.js');

const scDividerCss = ":host{display:block;min-height:1px}.divider{position:relative;padding:var(--spacing) 0}.line__container{position:absolute;top:0;right:0;bottom:0;left:0;display:flex;align-items:center}.line{width:100%;border-top:1px solid var(--sc-divider-border-top-color, var(--sc-color-gray-200))}.text__container{position:relative;display:flex;justify-content:center;font-size:var(--sc-font-size-small)}.text{padding:0 var(--sc-spacing-small);background:var(--sc-divider-text-background-color, var(--sc-color-white));color:var(--sc-color-gray-500)}";
const ScDividerStyle0 = scDividerCss;

const ScDivider = class {
    constructor(hostRef) {
        index.registerInstance(this, hostRef);
    }
    render() {
        return (index.h("div", { key: 'ae7483aab453e66780c81ba6943e34b451e371ab', class: "divider", part: "base" }, index.h("div", { key: '06056aadc09fccbeef602e37051c72d725910028', class: "line__container", "aria-hidden": "true", part: "line-container" }, index.h("div", { key: '9bbde66d3820e7fc7dab80ed5c0293a721c44697', class: "line", part: "line" })), index.h("div", { key: '94d1d3b518d2ffdb875f9671eb76460ccd1e31fe', class: "text__container", part: "text-container" }, index.h("span", { key: 'a75f53039b66bffaffa1c100c41874f85e4deef2', class: "text", part: "text" }, index.h("slot", { key: '4f016e08c25126f8efcb1c40602bbf0d90815f7c' })))));
    }
};
ScDivider.style = ScDividerStyle0;

exports.sc_divider = ScDivider;

//# sourceMappingURL=sc-divider.cjs.entry.js.map