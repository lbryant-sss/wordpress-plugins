'use strict';

Object.defineProperty(exports, '__esModule', { value: true });

const index = require('./index-8acc3c89.js');

const ScPaymentMethodDetails = class {
    constructor(hostRef) {
        index.registerInstance(this, hostRef);
        this.paymentMethod = undefined;
        this.editHandler = undefined;
    }
    render() {
        var _a, _b, _c, _d, _e, _f, _g, _h, _j, _k;
        return (index.h("sc-card", { key: '451021fec002652c456fe4154736acb31a863bae' }, index.h("sc-flex", { key: 'f075911208daddc1d4915f1c387be2112e2ee62a', alignItems: "center", justifyContent: "flex-start", style: { gap: '0.5em' } }, index.h("sc-payment-method", { key: '963044589629cad4cf4cac0c3e7a5d450d359977', paymentMethod: this.paymentMethod }), index.h("div", { key: '08dcf5706690f7547118079a03baf0585c084c51' }, !!((_b = (_a = this.paymentMethod) === null || _a === void 0 ? void 0 : _a.card) === null || _b === void 0 ? void 0 : _b.exp_month) && (index.h("span", { key: 'ecc0bdbf8ef1fb01e6ab73a8ba28edc82d8676be' }, 
        // Translators: %d/%d is month and year of expiration.
        wp.i18n.sprintf(wp.i18n.__('Exp. %d/%d', 'surecart'), (_d = (_c = this.paymentMethod) === null || _c === void 0 ? void 0 : _c.card) === null || _d === void 0 ? void 0 : _d.exp_month, (_f = (_e = this.paymentMethod) === null || _e === void 0 ? void 0 : _e.card) === null || _f === void 0 ? void 0 : _f.exp_year))), !!((_h = (_g = this.paymentMethod) === null || _g === void 0 ? void 0 : _g.paypal_account) === null || _h === void 0 ? void 0 : _h.email) && ((_k = (_j = this.paymentMethod) === null || _j === void 0 ? void 0 : _j.paypal_account) === null || _k === void 0 ? void 0 : _k.email)), index.h("sc-button", { key: '673125e00cf94ce97fded1ab031ad37eb0d0d85c', type: "text", circle: true, onClick: this.editHandler }, index.h("sc-icon", { key: '872dec14bec924edf103edbb84c19f044c905757', name: "edit-2" })))));
    }
};

exports.sc_payment_method_details = ScPaymentMethodDetails;

//# sourceMappingURL=sc-payment-method-details.cjs.entry.js.map