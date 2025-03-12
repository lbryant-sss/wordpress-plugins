import { r as registerInstance, h } from './index-745b6bec.js';

const scDividerCss = ":host{display:block;min-height:1px}.divider{position:relative;padding:var(--spacing) 0}.line__container{position:absolute;top:0;right:0;bottom:0;left:0;display:flex;align-items:center}.line{width:100%;border-top:1px solid var(--sc-divider-border-top-color, var(--sc-color-gray-200))}.text__container{position:relative;display:flex;justify-content:center;font-size:var(--sc-font-size-small)}.text{padding:0 var(--sc-spacing-small);background:var(--sc-divider-text-background-color, var(--sc-color-white));color:var(--sc-color-gray-500)}";
const ScDividerStyle0 = scDividerCss;

const ScDivider = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
    }
    render() {
        return (h("div", { key: '651248229068ecdfd4f1ac5e4ae84abc2becae8d', class: "divider", part: "base" }, h("div", { key: '75aa2f723657c1a5d71ea5ab846cc4bb9687acd5', class: "line__container", "aria-hidden": "true", part: "line-container" }, h("div", { key: 'f6a13f6d045ecd09822a842c3820952ae6fe4e6d', class: "line", part: "line" })), h("div", { key: '7b756704ecaa1464daae45d5375ecb67e0cb89b7', class: "text__container", part: "text-container" }, h("span", { key: 'f4a5ba8033f2f12b745cf1ff45fbf483aead7ca0', class: "text", part: "text" }, h("slot", { key: '4ed4bc7fad287d05519e23672602f86ed78a8f72' })))));
    }
};
ScDivider.style = ScDividerStyle0;

export { ScDivider as sc_divider };

//# sourceMappingURL=sc-divider.entry.js.map