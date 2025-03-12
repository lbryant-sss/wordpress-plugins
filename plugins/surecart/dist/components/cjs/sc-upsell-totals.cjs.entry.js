'use strict';

Object.defineProperty(exports, '__esModule', { value: true });

const index = require('./index-8acc3c89.js');
require('./watchers-4a82a9b2.js');
const store = require('./store-ce062aec.js');
require('./watchers-db03ec4e.js');
require('./index-bcdafe6e.js');
require('./google-03835677.js');
require('./currency-71fce0f0.js');
require('./google-59d23803.js');
require('./utils-2e91d46c.js');
require('./util-b877b2bd.js');
require('./index-fb76df07.js');
require('./getters-a0ce2d19.js');
require('./mutations-ac3b22d5.js');
require('./fetch-d644cebd.js');
require('./add-query-args-49dcb630.js');
require('./remove-query-args-b57e8cd3.js');
require('./mutations-11c8f9a8.js');

const scUpsellTotalsCss = ":host{display:block}sc-divider{margin:16px 0 !important}.conversion-description{color:var(--sc-color-gray-500);font-size:var(--sc-font-size-small);margin-right:var(--sc-spacing-xx-small)}";
const ScUpsellTotalsStyle0 = scUpsellTotalsCss;

const ScUpsellTotals = class {
    constructor(hostRef) {
        index.registerInstance(this, hostRef);
    }
    renderAmountDue() {
        var _a, _b;
        return store.state.amount_due > 0 ? (_a = store.state === null || store.state === void 0 ? void 0 : store.state.line_item) === null || _a === void 0 ? void 0 : _a.total_display_amount : !!((_b = store.state === null || store.state === void 0 ? void 0 : store.state.line_item) === null || _b === void 0 ? void 0 : _b.trial_amount) ? wp.i18n.__('Trial', 'surecart') : wp.i18n.__('Free', 'surecart');
    }
    renderConversion() {
        var _a, _b, _c, _d, _e, _f, _g, _h;
        // need to check the checkout for a few things.
        const checkout = store.state === null || store.state === void 0 ? void 0 : store.state.checkout;
        if (!(checkout === null || checkout === void 0 ? void 0 : checkout.show_converted_total)) {
            return null;
        }
        // the currency is the same as the current currency.
        if ((checkout === null || checkout === void 0 ? void 0 : checkout.currency) === (checkout === null || checkout === void 0 ? void 0 : checkout.current_currency)) {
            return null;
        }
        // there is no amount due.
        if (!((_a = store.state === null || store.state === void 0 ? void 0 : store.state.line_item) === null || _a === void 0 ? void 0 : _a.total_amount)) {
            return null;
        }
        return (index.h(index.Fragment, null, index.h("sc-divider", null), index.h("sc-line-item", { style: { '--price-size': 'var(--sc-font-size-x-large)' } }, index.h("span", { slot: "title" }, index.h("slot", { name: "charge-amount-description" }, wp.i18n.sprintf(wp.i18n.__('Payment Total', 'surecart'), (_c = (_b = store.state === null || store.state === void 0 ? void 0 : store.state.line_item) === null || _b === void 0 ? void 0 : _b.currency) === null || _c === void 0 ? void 0 : _c.toUpperCase()))), index.h("span", { slot: "price" }, index.h("span", { class: "currency-label" }, (_e = (_d = store.state === null || store.state === void 0 ? void 0 : store.state.line_item) === null || _d === void 0 ? void 0 : _d.currency) === null || _e === void 0 ? void 0 : _e.toUpperCase()), (_f = store.state === null || store.state === void 0 ? void 0 : store.state.line_item) === null || _f === void 0 ? void 0 :
            _f.total_default_currency_display_amount)), index.h("sc-line-item", null, index.h("span", { slot: "description", class: "conversion-description" }, wp.i18n.sprintf(wp.i18n.__('Your payment will be processed in %s.', 'surecart'), (_h = (_g = store.state === null || store.state === void 0 ? void 0 : store.state.line_item) === null || _g === void 0 ? void 0 : _g.currency) === null || _h === void 0 ? void 0 : _h.toUpperCase())))));
    }
    render() {
        var _a, _b, _c, _d, _e, _f, _g;
        return (index.h("sc-summary", { key: 'cf5e97154cb804df64baab62407bf893f7e600df', "open-text": "Total", "closed-text": "Total", collapsible: true, collapsed: true }, !!((_a = store.state.line_item) === null || _a === void 0 ? void 0 : _a.id) && index.h("span", { key: '674f5871e7314fa027c0d6a91ef0b413ee24e82a', slot: "price" }, this.renderAmountDue()), index.h("sc-divider", { key: '94f9e988682bbffb040e888029b8311c7d02d476' }), index.h("sc-line-item", { key: '1556c85d44b81672b5ca7dbd5f497a2e976a4955' }, index.h("span", { key: '00a955869c9372d0d91dd80e377cc66209b1ba7e', slot: "description" }, wp.i18n.__('Subtotal', 'surecart')), index.h("span", { key: '26198c2c805ccfc9cd14dce7aa24ca09391747f6', slot: "price" }, (_b = store.state.line_item) === null || _b === void 0 ? void 0 : _b.subtotal_display_amount)), (((_d = (_c = store.state === null || store.state === void 0 ? void 0 : store.state.line_item) === null || _c === void 0 ? void 0 : _c.fees) === null || _d === void 0 ? void 0 : _d.data) || [])
            .filter(fee => fee.fee_type === 'upsell') // only upsell fees.
            .map(fee => {
            return (index.h("sc-line-item", null, index.h("span", { slot: "description" }, fee.description, " ", `(${wp.i18n.__('one time', 'surecart')})`), index.h("span", { slot: "price" }, fee === null || fee === void 0 ? void 0 : fee.display_amount)));
        }), !!((_e = store.state.line_item) === null || _e === void 0 ? void 0 : _e.tax_amount) && (index.h("sc-line-item", { key: 'e16f6421ae2a47aa8a91ece9ff0f9b6922c64d29' }, index.h("span", { key: 'ce2b82fc9a4e189f7cc587c5358a0682e106172e', slot: "description" }, wp.i18n.__('Tax', 'surecart')), index.h("span", { key: '667f772a3d683d30c2fc85b5ce592453343f41ee', slot: "price" }, (_f = store.state.line_item) === null || _f === void 0 ? void 0 : _f.tax_display_amount))), index.h("sc-divider", { key: 'd4a567ed93ddbf2971f603f8b088eab6dadfd246' }), index.h("sc-line-item", { key: '8030e0e7343b08d28b9126ef3d3d810bd34daff3', style: { '--price-size': 'var(--sc-font-size-x-large)' } }, index.h("span", { key: '5ce49a6dfedc3d422753f0699c2ac304cd3a41d4', slot: "title" }, wp.i18n.__('Total', 'surecart')), index.h("span", { key: '4d860880bc32d1dd917f274696672957d694d440', slot: "price" }, (_g = store.state.line_item) === null || _g === void 0 ? void 0 : _g.total_display_amount)), this.renderConversion()));
    }
};
ScUpsellTotals.style = ScUpsellTotalsStyle0;

exports.sc_upsell_totals = ScUpsellTotals;

//# sourceMappingURL=sc-upsell-totals.cjs.entry.js.map