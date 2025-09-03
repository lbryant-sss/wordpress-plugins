import { r as registerInstance, h, a as getElement } from './index-745b6bec.js';
import { s as state } from './watchers-38693c1f.js';
import './index-06061d4e.js';

const scPaymentMethodChoiceCss = ":host{display:block}:slotted([slot=\"summary\"]){line-height:1;display:flex;align-items:center;gap:0.5em}";
const ScPaymentMethodChoiceStyle0 = scPaymentMethodChoiceCss;

const ScPaymentMethodChoice = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.methodId = undefined;
        this.processorId = undefined;
        this.isManual = undefined;
        this.card = undefined;
    }
    isSelected() {
        if (this.methodId) {
            return (state === null || state === void 0 ? void 0 : state.id) === this.processorId && (state === null || state === void 0 ? void 0 : state.method) == this.methodId;
        }
        return !(state === null || state === void 0 ? void 0 : state.method) && (state === null || state === void 0 ? void 0 : state.id) === this.processorId;
    }
    getAllOptions() {
        const parentGroup = this.el.closest('sc-payment') || this.el.parentElement;
        if (!parentGroup) {
            return [];
        }
        return [...parentGroup.querySelectorAll(this.el.tagName)];
    }
    getSiblingItems() {
        return this.getAllOptions().filter(choice => choice !== this.el);
    }
    hasOthers() {
        var _a;
        return !!((_a = this.getSiblingItems()) === null || _a === void 0 ? void 0 : _a.length);
    }
    render() {
        const Tag = this.hasOthers() ? 'sc-toggle' : 'div';
        return (h(Tag, { key: '56eaef03ea99ae08b743dbd5b64e954eda688f9b', "show-control": true, borderless: true, open: this.isSelected(), onScShow: () => {
                state.id = this.processorId;
                state.manual = !!this.isManual;
                state.method = this.methodId;
            } }, this.hasOthers() && h("slot", { key: '81a1389709b3d3505e7428d250982e51810db2c6', name: "summary", slot: "summary" }), this.card && !this.hasOthers() ? (h("sc-card", null, h("slot", null))) : (h("slot", null))));
    }
    get el() { return getElement(this); }
};
ScPaymentMethodChoice.style = ScPaymentMethodChoiceStyle0;

const scPaymentSelectedCss = ":host{display:block}::slotted([slot=icon]){display:block;font-size:24px}.payment-selected{display:flex;flex-direction:column;gap:var(--sc-spacing-x-small)}.payment-selected__label{color:var(--sc-input-label-color);line-height:var(--sc-line-height-dense);font-size:var(--sc-font-size-medium)}.payment-selected__instructions{display:flex;justify-content:flex-start;align-items:center;gap:1em}.payment-selected__instructions svg{width:42px;height:42px;flex-shrink:0}.payment-selected__instructions-text{color:var(--sc-input-label-color);font-size:var(--sc-font-size-small);line-height:var(--sc-line-height-dense)}";
const ScPaymentSelectedStyle0 = scPaymentSelectedCss;

const ScPaymentSelected = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.iconName = undefined;
        this.label = undefined;
    }
    render() {
        return (h("div", { key: '7d3893db9e9aa8cba0c0820d71e3e7494741fd67', class: "payment-selected", part: "base" }, h("slot", { key: '838c4063b7fdeaac2eebc852ca738e71c75142b4', name: "icon" }), h("div", { key: '011903054a33276610b2bdbff7c62cba5324e7bb', class: "payment-selected__label" }, this.label), h("sc-divider", { key: '4c74387b07d1223636885592e3921c5383da87ab', style: { '--spacing': 'var(--sc-spacing-xx-small)' }, exportparts: "base:divider, line:divider__line" }), h("div", { key: '5b8808ee121c5aa0f798cbd77aadf0dbefe52dcb', part: "instructions", class: "payment-selected__instructions" }, h("svg", { key: '168d058acbb6d0cf3708fe6d805e7e9f878cd7bf', part: "icon", viewBox: "0 0 48 40", fill: "var(--sc-color-gray-500)", xmlns: "http://www.w3.org/2000/svg", role: "presentation" }, h("path", { key: '41d10b712b3ac6dc8dd733bed6ee89398d51633b', opacity: ".6", "fill-rule": "evenodd", "clip-rule": "evenodd", d: "M43 5a4 4 0 00-4-4H17a4 4 0 00-4 4v11a1 1 0 102 0V5a2 2 0 012-2h22a2 2 0 012 2v30a2 2 0 01-2 2H17a2 2 0 01-2-2v-9a1 1 0 10-2 0v9a4 4 0 004 4h22a4 4 0 004-4V5zM17.992 16.409L21.583 20H6a1 1 0 100 2h15.583l-3.591 3.591a1 1 0 101.415 1.416l5.3-5.3a1 1 0 000-1.414l-5.3-5.3a1 1 0 10-1.415 1.416zM17 6a1 1 0 011-1h15a1 1 0 011 1v2a1 1 0 01-1 1H18a1 1 0 01-1-1V6zm21-1a1 1 0 100 2 1 1 0 000-2z" })), h("div", { key: '3e95a46fada81fd1a53acff5a94e2dcf1806b894', part: "text", class: "payment-selected__instructions-text" }, h("slot", { key: 'deb46982302122635725176635ba5068c7ec014d' })))));
    }
};
ScPaymentSelected.style = ScPaymentSelectedStyle0;

export { ScPaymentMethodChoice as sc_payment_method_choice, ScPaymentSelected as sc_payment_selected };

//# sourceMappingURL=sc-payment-method-choice_2.entry.js.map