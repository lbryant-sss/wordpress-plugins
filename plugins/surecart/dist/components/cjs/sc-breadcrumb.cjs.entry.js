'use strict';

Object.defineProperty(exports, '__esModule', { value: true });

const index = require('./index-8acc3c89.js');

const scBreadcrumbCss = ":host{display:inline-flex}.breadcrumb-item{display:inline-flex;align-items:center;font-family:var(--sc-font-sans);font-size:var(--sc-font-size-small);font-weight:var(--sc-font-weight-semibold);color:var(--sc-breadcrumb-color, var(--sc-color-gray-600));line-height:var(--sc-line-height-normal);white-space:nowrap}.breadcrumb-item__label{display:inline-block;font-family:inherit;font-size:inherit;font-weight:inherit;line-height:inherit;text-decoration:none;color:inherit;background:none;border:none;border-radius:var(--sc-border-radius-medium);padding:0;margin:0;cursor:pointer;transition:color var(--sc-transition-fast) ease}:host(:not(:last-of-type)) .breadcrumb-item__label{color:var(--sc-breadcrumb-item-label-color, var(--sc-color-gray-900))}:host(:not(:last-of-type)) .breadcrumb-item__label:hover{color:var(--sc-breadcrumb-item-label-hover-color, var(--sc-color-primary-500))}:host(:not(:last-of-type)) .breadcrumb-item__label:active{color:var(--sc-breadcrumb-item-label-active-color, var(--sc-color-gray-900))}.breadcrumb-item__label:focus{box-shadow:var(--sc-focus-ring)}.breadcrumb-item__prefix,.breadcrumb-item__suffix{display:none;flex:0 0 auto;display:flex;align-items:center}.breadcrumb-item--has-prefix .breadcrumb-item__prefix{display:inline-flex;margin-right:var(--sc-spacing-x-small)}.breadcrumb-item--has-suffix .breadcrumb-item__suffix{display:inline-flex;margin-left:var(--sc-spacing-x-small)}:host(:last-of-type) .breadcrumb-item__separator{display:none}.breadcrumb-item__separator{display:inline-flex;align-items:center;margin:0 var(--sc-spacing-x-small);user-select:none}";
const ScBreadcrumbStyle0 = scBreadcrumbCss;

const ScBreadcrumb = class {
    constructor(hostRef) {
        index.registerInstance(this, hostRef);
        this.href = undefined;
        this.target = undefined;
        this.rel = 'noreferrer noopener';
        this.hasPrefix = undefined;
        this.hasSuffix = undefined;
    }
    handleSlotChange() {
        this.hasPrefix = !!this.el.querySelector('[slot="prefix"]');
        this.hasSuffix = !!this.el.querySelector('[slot="suffix"]');
    }
    render() {
        const Tag = this.href ? 'a' : 'div';
        return (index.h("div", { key: 'a6c5bdb3ecaad172d54c7fa66196b3c534f89f9d', part: "base", class: {
                'breadcrumb-item': true,
                'breadcrumb-item--has-prefix': this.hasPrefix,
                'breadcrumb-item--has-suffix': this.hasSuffix,
            } }, index.h("span", { key: '46e40c6e050d6f49f7923f6db9ca94bdc079a5a2', part: "prefix", class: "breadcrumb-item__prefix" }, index.h("slot", { key: 'b268f0c29c70ec12829d9a95c02394b03018e7ad', name: "prefix" })), index.h(Tag, { key: '9397ab426fd9a537115dbb4b799fd2259b638bfe', part: "label", class: "breadcrumb-item__label breadcrumb-item__label--link", href: this.href, target: this.target, rel: this.rel }, index.h("slot", { key: '610140218c3134ca0d1dcdf0bb1459fdc503897e' })), index.h("span", { key: '816306b0e51612b52aee78bbdedfaa2fb84e50ea', part: "suffix", class: "breadcrumb-item__suffix" }, index.h("slot", { key: '37d093943ef2eb43f7d26a583011d5c8d96a5d43', name: "suffix", onSlotchange: () => this.handleSlotChange() })), index.h("span", { key: '1811165836c86d2946e5160c2e9623849581ce92', part: "separator", class: "breadcrumb-item__separator", "aria-hidden": "true" }, index.h("slot", { key: '241731a354f3eb6ce99dc50f9e2e2a1a6a6225fa', name: "separator", onSlotchange: () => this.handleSlotChange() }, index.h("sc-icon", { key: '244213b938d339ebbe7f4571665867230175683e', name: "chevron-right" })))));
    }
    get el() { return index.getElement(this); }
};
ScBreadcrumb.style = ScBreadcrumbStyle0;

exports.sc_breadcrumb = ScBreadcrumb;

//# sourceMappingURL=sc-breadcrumb.cjs.entry.js.map