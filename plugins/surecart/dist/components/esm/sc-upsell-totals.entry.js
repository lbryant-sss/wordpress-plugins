import { r as registerInstance, h, F as Fragment } from './index-745b6bec.js';
import './watchers-91c4d57e.js';
import { s as state } from './store-4bc13420.js';
import './watchers-fbf07f32.js';
import './index-06061d4e.js';
import './google-dd89f242.js';
import './currency-a0c9bff4.js';
import './google-a86aa761.js';
import './utils-cd1431df.js';
import './util-50af2a83.js';
import './index-c5a96d53.js';
import './getters-1899e179.js';
import './mutations-5702cb96.js';
import './fetch-8ecbbe53.js';
import './add-query-args-0e2a8393.js';
import './remove-query-args-938c53ea.js';
import './mutations-ed6d0770.js';

const scUpsellTotalsCss = ":host{display:block}sc-divider{margin:16px 0 !important}.conversion-description{color:var(--sc-color-gray-500);font-size:var(--sc-font-size-small);margin-right:var(--sc-spacing-xx-small)}";
const ScUpsellTotalsStyle0 = scUpsellTotalsCss;

const ScUpsellTotals = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
    }
    renderAmountDue() {
        var _a, _b;
        return state.amount_due > 0 ? (_a = state === null || state === void 0 ? void 0 : state.line_item) === null || _a === void 0 ? void 0 : _a.total_display_amount : !!((_b = state === null || state === void 0 ? void 0 : state.line_item) === null || _b === void 0 ? void 0 : _b.trial_amount) ? wp.i18n.__('Trial', 'surecart') : wp.i18n.__('Free', 'surecart');
    }
    renderConversion() {
        var _a, _b, _c, _d, _e, _f, _g, _h;
        // need to check the checkout for a few things.
        const checkout = state === null || state === void 0 ? void 0 : state.checkout;
        if (!(checkout === null || checkout === void 0 ? void 0 : checkout.show_converted_total)) {
            return null;
        }
        // the currency is the same as the current currency.
        if ((checkout === null || checkout === void 0 ? void 0 : checkout.currency) === (checkout === null || checkout === void 0 ? void 0 : checkout.current_currency)) {
            return null;
        }
        // there is no amount due.
        if (!((_a = state === null || state === void 0 ? void 0 : state.line_item) === null || _a === void 0 ? void 0 : _a.total_amount)) {
            return null;
        }
        return (h(Fragment, null, h("sc-divider", null), h("sc-line-item", { style: { '--price-size': 'var(--sc-font-size-x-large)' } }, h("span", { slot: "title" }, h("slot", { name: "charge-amount-description" }, wp.i18n.sprintf(wp.i18n.__('Payment Total', 'surecart'), (_c = (_b = state === null || state === void 0 ? void 0 : state.line_item) === null || _b === void 0 ? void 0 : _b.currency) === null || _c === void 0 ? void 0 : _c.toUpperCase()))), h("span", { slot: "price" }, h("span", { class: "currency-label" }, (_e = (_d = state === null || state === void 0 ? void 0 : state.line_item) === null || _d === void 0 ? void 0 : _d.currency) === null || _e === void 0 ? void 0 : _e.toUpperCase()), (_f = state === null || state === void 0 ? void 0 : state.line_item) === null || _f === void 0 ? void 0 :
            _f.total_default_currency_display_amount)), h("sc-line-item", null, h("span", { slot: "description", class: "conversion-description" }, wp.i18n.sprintf(wp.i18n.__('Your payment will be processed in %s.', 'surecart'), (_h = (_g = state === null || state === void 0 ? void 0 : state.line_item) === null || _g === void 0 ? void 0 : _g.currency) === null || _h === void 0 ? void 0 : _h.toUpperCase())))));
    }
    render() {
        var _a, _b, _c, _d, _e, _f, _g;
        return (h("sc-summary", { key: 'cf5e97154cb804df64baab62407bf893f7e600df', "open-text": "Total", "closed-text": "Total", collapsible: true, collapsed: true }, !!((_a = state.line_item) === null || _a === void 0 ? void 0 : _a.id) && h("span", { key: '674f5871e7314fa027c0d6a91ef0b413ee24e82a', slot: "price" }, this.renderAmountDue()), h("sc-divider", { key: '94f9e988682bbffb040e888029b8311c7d02d476' }), h("sc-line-item", { key: '1556c85d44b81672b5ca7dbd5f497a2e976a4955' }, h("span", { key: '00a955869c9372d0d91dd80e377cc66209b1ba7e', slot: "description" }, wp.i18n.__('Subtotal', 'surecart')), h("span", { key: '26198c2c805ccfc9cd14dce7aa24ca09391747f6', slot: "price" }, (_b = state.line_item) === null || _b === void 0 ? void 0 : _b.subtotal_display_amount)), (((_d = (_c = state === null || state === void 0 ? void 0 : state.line_item) === null || _c === void 0 ? void 0 : _c.fees) === null || _d === void 0 ? void 0 : _d.data) || [])
            .filter(fee => fee.fee_type === 'upsell') // only upsell fees.
            .map(fee => {
            return (h("sc-line-item", null, h("span", { slot: "description" }, fee.description, " ", `(${wp.i18n.__('one time', 'surecart')})`), h("span", { slot: "price" }, fee === null || fee === void 0 ? void 0 : fee.display_amount)));
        }), !!((_e = state.line_item) === null || _e === void 0 ? void 0 : _e.tax_amount) && (h("sc-line-item", { key: 'e16f6421ae2a47aa8a91ece9ff0f9b6922c64d29' }, h("span", { key: 'ce2b82fc9a4e189f7cc587c5358a0682e106172e', slot: "description" }, wp.i18n.__('Tax', 'surecart')), h("span", { key: '667f772a3d683d30c2fc85b5ce592453343f41ee', slot: "price" }, (_f = state.line_item) === null || _f === void 0 ? void 0 : _f.tax_display_amount))), h("sc-divider", { key: 'd4a567ed93ddbf2971f603f8b088eab6dadfd246' }), h("sc-line-item", { key: '8030e0e7343b08d28b9126ef3d3d810bd34daff3', style: { '--price-size': 'var(--sc-font-size-x-large)' } }, h("span", { key: '5ce49a6dfedc3d422753f0699c2ac304cd3a41d4', slot: "title" }, wp.i18n.__('Total', 'surecart')), h("span", { key: '4d860880bc32d1dd917f274696672957d694d440', slot: "price" }, (_g = state.line_item) === null || _g === void 0 ? void 0 : _g.total_display_amount)), this.renderConversion()));
    }
};
ScUpsellTotals.style = ScUpsellTotalsStyle0;

export { ScUpsellTotals as sc_upsell_totals };

//# sourceMappingURL=sc-upsell-totals.entry.js.map