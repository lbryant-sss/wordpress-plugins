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
        return (index.h("div", { key: '46a8064b7e929b1b38e5e042825e9d6b139da8a8', part: "base", class: {
                'heading': true,
                'heading--small': this.size === 'small',
                'heading--medium': this.size === 'medium',
                'heading--large': this.size === 'large',
            } }, index.h("div", { key: '9c0cabaa5553c59cc375a5e9e105829a61478781', class: { heading__text: true } }, index.h("div", { key: '69b5a96d1fd1a64ad24c259f399331058b087fdc', class: "heading__title", part: "title" }, index.h("slot", { key: 'af929231f23a796359f76455fe519cdec6bac65e' })), index.h("div", { key: '926d4d8253193eb4aa9e5354cdd485fcbff821cf', class: "heading__description", part: "description" }, index.h("slot", { key: '8b893b04743c8ae0c5107073914d7cd6a848587e', name: "description" }))), index.h("slot", { key: '5c60966952e60bf83ea1f2e5fb3a4a6f9f6e070a', name: "end" })));
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
        return index.h("slot", { key: 'dc86b9dc0945488adaca74b65a593b91712f594a' });
    }
    get el() { return index.getElement(this); }
    static get watchers() { return {
        "checkout": ["handleOrderChange"]
    }; }
};

exports.sc_heading = ScHeading;
exports.sc_order_confirm_components_validator = ScOrderConfirmComponentsValidator;

//# sourceMappingURL=sc-heading_2.cjs.entry.js.map