import { r as registerInstance, h } from './index-745b6bec.js';

const ScPaymentMethodDetails = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.paymentMethod = undefined;
        this.editHandler = undefined;
    }
    render() {
        var _a, _b, _c, _d, _e, _f, _g, _h, _j, _k;
        return (h("sc-card", { key: '5f83c8fed482b9d03935310ae11707526256482e' }, h("sc-flex", { key: '4796822ea72c2d32ba72fc6d3fc79cba388650ce', alignItems: "center", justifyContent: "flex-start", style: { gap: '0.5em' } }, h("sc-payment-method", { key: 'ae08245ed91dac78e864e86eb8f14d98bf8d29d9', paymentMethod: this.paymentMethod }), h("div", { key: '45f31d232bacfb676bbc4acef32f83e4747fada9' }, !!((_b = (_a = this.paymentMethod) === null || _a === void 0 ? void 0 : _a.card) === null || _b === void 0 ? void 0 : _b.exp_month) && (h("span", { key: '0a39442c4db23572c350d5ff58ad58ae98b010b1' }, 
        // Translators: %d/%d is month and year of expiration.
        wp.i18n.sprintf(wp.i18n.__('Exp. %d/%d', 'surecart'), (_d = (_c = this.paymentMethod) === null || _c === void 0 ? void 0 : _c.card) === null || _d === void 0 ? void 0 : _d.exp_month, (_f = (_e = this.paymentMethod) === null || _e === void 0 ? void 0 : _e.card) === null || _f === void 0 ? void 0 : _f.exp_year))), !!((_h = (_g = this.paymentMethod) === null || _g === void 0 ? void 0 : _g.paypal_account) === null || _h === void 0 ? void 0 : _h.email) && ((_k = (_j = this.paymentMethod) === null || _j === void 0 ? void 0 : _j.paypal_account) === null || _k === void 0 ? void 0 : _k.email)), h("sc-button", { key: '564fc1aa03033b87ea1da3d0358e46a70f17de6b', type: "text", circle: true, onClick: this.editHandler }, h("sc-icon", { key: 'a01656f1a395e3fb62a6de5d05f0048788db7427', name: "edit-2" })))));
    }
};

export { ScPaymentMethodDetails as sc_payment_method_details };

//# sourceMappingURL=sc-payment-method-details.entry.js.map