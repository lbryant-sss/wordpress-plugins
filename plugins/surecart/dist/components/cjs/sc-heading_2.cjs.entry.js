'use strict';

Object.defineProperty(exports, '__esModule', { value: true });

const index = require('./index-8acc3c89.js');

const scHeadingCss = ":host{display:block}.heading{font-family:var(--sc-font-sans);display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between}.heading--small .heading__title{font-size:var(--sc-font-size-small);text-transform:uppercase}.heading__text{width:100%}.heading__title{font-size:var(--sc-font-size-x-large);font-weight:var(--sc-font-weight-bold);line-height:var(--sc-line-height-dense);white-space:normal}.heading__description{font-size:var(--sc-font-size-normal);line-height:var(--sc-line-height-dense);color:var(--sc-color-gray-500)}";
const ScHeadingStyle0 = scHeadingCss;

const ScHeading = class {
    constructor(hostRef) {
        index.registerInstance(this, hostRef);
        this.size = 'medium';
    }
    render() {
        return (index.h("div", { key: '09047c0bbf108f16b1439c15f7d359fa3d6fac7c', part: "base", class: {
                'heading': true,
                'heading--small': this.size === 'small',
                'heading--medium': this.size === 'medium',
                'heading--large': this.size === 'large',
            } }, index.h("div", { key: '2460b2388cd79f9c2697a80fcec2a7c3d0f3dc7e', class: { heading__text: true } }, index.h("div", { key: '97191bba70ddc15e2f96061ecab6950e595ac2e2', class: "heading__title", part: "title" }, index.h("slot", { key: 'ba9b432499a9c8edeb976699a901b76fcd79971c' })), index.h("div", { key: '7d3e9cac2a2b012e5d34b439ebcb7b8aaf2ea6c1', class: "heading__description", part: "description" }, index.h("slot", { key: '1c05640c30a1143acb23a9c286749bac9ab49f9a', name: "description" }))), index.h("slot", { key: '415e9766f9d7ffe294397df29dc18b1f093ac678', name: "end" })));
    }
    get el() { return index.getElement(this); }
};
ScHeading.style = ScHeadingStyle0;

const ScOrderConfirmComponentsValidator = class {
    constructor(hostRef) {
        index.registerInstance(this, hostRef);
        this.checkout = undefined;
        this.hasManualInstructions = undefined;
    }
    handleOrderChange() {
        var _a;
        if ((_a = this.checkout) === null || _a === void 0 ? void 0 : _a.manual_payment) {
            this.addManualPaymentInstructions();
        }
    }
    addManualPaymentInstructions() {
        var _a, _b;
        if (this.hasManualInstructions)
            return;
        const details = this.el.shadowRoot
            .querySelector('slot')
            .assignedElements({ flatten: true })
            .find(element => element.tagName === 'SC-ORDER-CONFIRMATION-DETAILS');
        const address = document.createElement('sc-order-manual-instructions');
        (_b = (_a = details === null || details === void 0 ? void 0 : details.parentNode) === null || _a === void 0 ? void 0 : _a.insertBefore) === null || _b === void 0 ? void 0 : _b.call(_a, address, details);
        this.hasManualInstructions = true;
    }
    componentWillLoad() {
        this.hasManualInstructions = !!this.el.querySelector('sc-order-manual-instructions');
    }
    render() {
        return index.h("slot", { key: '7e2dfbd843bf6a068d9f7c7f6aa06c31a91508a3' });
    }
    get el() { return index.getElement(this); }
    static get watchers() { return {
        "checkout": ["handleOrderChange"]
    }; }
};

exports.sc_heading = ScHeading;
exports.sc_order_confirm_components_validator = ScOrderConfirmComponentsValidator;

//# sourceMappingURL=sc-heading_2.cjs.entry.js.map