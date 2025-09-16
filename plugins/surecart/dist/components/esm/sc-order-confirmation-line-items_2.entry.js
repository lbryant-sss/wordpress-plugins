import { r as registerInstance, h, F as Fragment } from './index-745b6bec.js';
import { o as openWormhole } from './consumer-e06b16d3.js';
import { a as getHumanDiscount } from './price-af9f0dbf.js';
import { f as formatTaxDisplay } from './tax-a03623ca.js';
import './currency-a0c9bff4.js';

const scOrderConfirmationLineItemsCss = ":host{display:block}.line-items{display:grid;gap:var(--sc-spacing-small)}.line-item{display:grid;gap:var(--sc-spacing-small)}.fee__description{opacity:0.75}";
const ScOrderConfirmationLineItemsStyle0 = scOrderConfirmationLineItemsCss;

const ScOrderConfirmationLineItems = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.order = undefined;
        this.loading = undefined;
    }
    render() {
        var _a, _b;
        if (!!this.loading) {
            return (h("sc-line-item", null, h("sc-skeleton", { style: { 'width': '50px', 'height': '50px', '--border-radius': '0' }, slot: "image" }), h("sc-skeleton", { slot: "title", style: { width: '120px', display: 'inline-block' } }), h("sc-skeleton", { slot: "description", style: { width: '60px', display: 'inline-block' } }), h("sc-skeleton", { style: { width: '120px', display: 'inline-block' }, slot: "price" }), h("sc-skeleton", { style: { width: '60px', display: 'inline-block' }, slot: "price-description" })));
        }
        return (h("div", { class: { 'confirmation-summary': true } }, h("div", { class: "line-items", part: "line-items" }, (_b = (_a = this.order) === null || _a === void 0 ? void 0 : _a.line_items) === null || _b === void 0 ? void 0 : _b.data.map(item => {
            var _a, _b, _c, _d, _e, _f, _g, _h, _j;
            return (h("div", { class: "line-item" }, h("sc-product-line-item", { key: item.id, image: (_b = (_a = item === null || item === void 0 ? void 0 : item.price) === null || _a === void 0 ? void 0 : _a.product) === null || _b === void 0 ? void 0 : _b.line_item_image, name: `${(_d = (_c = item === null || item === void 0 ? void 0 : item.price) === null || _c === void 0 ? void 0 : _c.product) === null || _d === void 0 ? void 0 : _d.name}`, price: (_e = item === null || item === void 0 ? void 0 : item.price) === null || _e === void 0 ? void 0 : _e.name, variant: item === null || item === void 0 ? void 0 : item.variant_display_options, editable: false, removable: false, quantity: item.quantity, fees: (_f = item === null || item === void 0 ? void 0 : item.fees) === null || _f === void 0 ? void 0 : _f.data, note: item === null || item === void 0 ? void 0 : item.display_note, amount: item.ad_hoc_display_amount ? item.ad_hoc_display_amount : item.subtotal_display_amount, scratch: !item.ad_hoc_display_amount && (item === null || item === void 0 ? void 0 : item.scratch_display_amount), trial: (_g = item === null || item === void 0 ? void 0 : item.price) === null || _g === void 0 ? void 0 : _g.trial_text, interval: `${(_h = item === null || item === void 0 ? void 0 : item.price) === null || _h === void 0 ? void 0 : _h.short_interval_text} ${(_j = item === null || item === void 0 ? void 0 : item.price) === null || _j === void 0 ? void 0 : _j.short_interval_count_text}`, purchasableStatus: item === null || item === void 0 ? void 0 : item.purchasable_status_display, sku: item === null || item === void 0 ? void 0 : item.sku })));
        }))));
    }
};
openWormhole(ScOrderConfirmationLineItems, ['order', 'busy', 'loading', 'empty'], false);
ScOrderConfirmationLineItems.style = ScOrderConfirmationLineItemsStyle0;

const scOrderConfirmationTotalsCss = ":host{display:block}";
const ScOrderConfirmationTotalsStyle0 = scOrderConfirmationTotalsCss;

const ScOrderConfirmationTotals = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.order = undefined;
    }
    renderDiscountLine() {
        var _a, _b, _c, _d, _e, _f, _g, _h, _j, _k, _l, _m, _o, _p;
        if (!((_c = (_b = (_a = this.order) === null || _a === void 0 ? void 0 : _a.discount) === null || _b === void 0 ? void 0 : _b.promotion) === null || _c === void 0 ? void 0 : _c.code)) {
            return null;
        }
        let humanDiscount = '';
        if ((_e = (_d = this.order) === null || _d === void 0 ? void 0 : _d.discount) === null || _e === void 0 ? void 0 : _e.coupon) {
            humanDiscount = getHumanDiscount((_g = (_f = this.order) === null || _f === void 0 ? void 0 : _f.discount) === null || _g === void 0 ? void 0 : _g.coupon);
        }
        return (h("sc-line-item", { style: { marginTop: 'var(--sc-spacing-small)' } }, h("span", { slot: "description" }, wp.i18n.__('Discount', 'surecart'), h("br", null), ((_k = (_j = (_h = this.order) === null || _h === void 0 ? void 0 : _h.discount) === null || _j === void 0 ? void 0 : _j.promotion) === null || _k === void 0 ? void 0 : _k.code) && (h("sc-tag", { type: "success", size: "small" }, (_o = (_m = (_l = this.order) === null || _l === void 0 ? void 0 : _l.discount) === null || _m === void 0 ? void 0 : _m.promotion) === null || _o === void 0 ? void 0 : _o.code))), humanDiscount && (h("span", { class: "coupon-human-discount", slot: "price-description" }, "(", humanDiscount, ")")), h("span", { slot: "price" }, (_p = this.order) === null || _p === void 0 ? void 0 : _p.discounts_display_amount)));
    }
    render() {
        var _a, _b, _c, _d, _e, _f, _g, _h, _j, _k, _l, _m, _o, _p, _q, _r, _s, _t, _u, _v, _w, _x, _y, _z, _0, _1, _2, _3, _4, _5, _6, _7, _8, _9, _10, _11, _12, _13, _14, _15, _16, _17, _18;
        const shippingMethod = (_b = (_a = this.order) === null || _a === void 0 ? void 0 : _a.selected_shipping_choice) === null || _b === void 0 ? void 0 : _b.shipping_method;
        const shippingMethodName = shippingMethod === null || shippingMethod === void 0 ? void 0 : shippingMethod.name;
        return (h("div", { key: 'd7ec8909e2e183a83142cdb11ee48d33714dc281', class: { 'line-item-totals': true } }, ((_c = this.order) === null || _c === void 0 ? void 0 : _c.subtotal_amount) !== ((_d = this.order) === null || _d === void 0 ? void 0 : _d.total_amount) && (h("sc-line-item", { key: '2392fe6f2a1c1033b7469ec491c703bad484029f' }, h("span", { key: '075c1be7e8b1e2834a62ae3d838a13beb1c938c0', slot: "description" }, wp.i18n.__('Subtotal', 'surecart')), h("span", { key: '3627b5373a820bc760299db49f52c7be72697ecc', slot: "price", style: {
                'font-weight': 'var(--sc-font-weight-semibold)',
                'color': 'var(--sc-color-gray-800)',
            } }, (_e = this.order) === null || _e === void 0 ? void 0 : _e.subtotal_display_amount))), !!((_f = this.order) === null || _f === void 0 ? void 0 : _f.trial_amount) && (h("sc-line-item", { key: '3a3b9afb535b87b0192fdc98bd8155949b12f3f3' }, h("span", { key: '43731921e5304a5de5b8d6e31fc7a762cb8d12e3', slot: "description" }, wp.i18n.__('Trial', 'surecart')), h("span", { key: '54ad4de3522f8fc95dde4cc1efb2bd9e95e282ec', slot: "price", style: {
                'font-weight': 'var(--sc-font-weight-semibold)',
                'color': 'var(--sc-color-gray-800)',
            } }, (_g = this.order) === null || _g === void 0 ? void 0 : _g.trial_display_amount))), !!((_h = this.order) === null || _h === void 0 ? void 0 : _h.discounts) && (h("sc-line-item", { key: '25cc68f5e1159d636ac6ea3755b17686d2c0480d' }, h("span", { key: '0592cf38df35773e3176b5009cfa1582533201c4', slot: "description" }, wp.i18n.__('Discounts', 'surecart')), h("span", { key: '6fbd7c5c25fbd52d88a31e6ffb9389c86544265d', slot: "price", style: {
                'font-weight': 'var(--sc-font-weight-semibold)',
                'color': 'var(--sc-color-gray-800)',
            } }, (_j = this.order) === null || _j === void 0 ? void 0 : _j.discounts_display))), !!((_m = (_l = (_k = this.order) === null || _k === void 0 ? void 0 : _k.discount) === null || _l === void 0 ? void 0 : _l.promotion) === null || _m === void 0 ? void 0 : _m.code) && (h("sc-line-item", { key: '14d97c4b3aab52b67106808e7112217420f478b7' }, h("span", { key: '493c2d6d22aaf3b0a90528efdd1b8fef144e6681', slot: "description" }, wp.i18n.__('Discount', 'surecart'), h("br", { key: 'beea305a652d415e913abf32441d1f73a65f56ed' }), h("sc-tag", { key: '1b73143056ccd7c46939ade30ac0b0925ae99b66', type: "success" }, wp.i18n.__('Coupon:', 'surecart'), " ", (_q = (_p = (_o = this.order) === null || _o === void 0 ? void 0 : _o.discount) === null || _p === void 0 ? void 0 : _p.promotion) === null || _q === void 0 ? void 0 :
            _q.code)), h("span", { key: 'd5e9cfb62fe70a296db1a1e1288a498c57ecb5a3', slot: "price", style: {
                'font-weight': 'var(--sc-font-weight-semibold)',
                'color': 'var(--sc-color-gray-800)',
            } }, (_r = this.order) === null || _r === void 0 ? void 0 : _r.discounts_display_amount))), !!((_s = this.order) === null || _s === void 0 ? void 0 : _s.shipping_amount) && (h("sc-line-item", { key: '436e8992e88ec206e8a51f576785170b60b97679' }, h("span", { key: 'd47d2a1a6cae6cc4982c5a29157474dbbafe6576', slot: "description" }, `${wp.i18n.__('Shipping', 'surecart')} ${shippingMethodName ? `(${shippingMethodName})` : ''}`), h("span", { key: '8e37587322a6c1eeb2597e5f28a53056ddee9cdb', slot: "price", style: {
                'font-weight': 'var(--sc-font-weight-semibold)',
                'color': 'var(--sc-color-gray-800)',
            } }, (_t = this.order) === null || _t === void 0 ? void 0 : _t.shipping_display_amount))), !!((_u = this.order) === null || _u === void 0 ? void 0 : _u.tax_amount) && (h("sc-line-item", { key: 'fc8ea9167e85451eb970f4f84601f13254333f32' }, h("span", { key: '0f0ce17b654b63577120ddb769bbc38ced8b8fe4', slot: "description" }, `${formatTaxDisplay((_v = this.order) === null || _v === void 0 ? void 0 : _v.tax_label, ((_w = this.order) === null || _w === void 0 ? void 0 : _w.tax_status) === 'estimated')} (${(_x = this.order) === null || _x === void 0 ? void 0 : _x.tax_percent}%)`), h("span", { key: '04edd1984f8f629b76446091a86edf79246be0d6', slot: "price" }, (_y = this.order) === null || _y === void 0 ? void 0 : _y.tax_display_amount), !!((_z = this.order) === null || _z === void 0 ? void 0 : _z.tax_inclusive_amount) && h("span", { key: '40dbcdfb316dfe9f12fd5a7d10d17ac90e5d1834', slot: "price-description" }, `(${wp.i18n.__('included', 'surecart')})`))), h("sc-divider", { key: '3abe92d22d43c620c93b016ef2623f0edb169bbb', style: { '--spacing': 'var(--sc-spacing-x-small)' } }), h("sc-line-item", { key: '18f884174825c83e688eac794ebc16915adf1cdb', style: {
                'width': '100%',
                '--price-size': 'var(--sc-font-size-x-large)',
            } }, h("span", { key: '598032e8993675b76fecb1ab48578c69ae48f956', slot: "title" }, wp.i18n.__('Total', 'surecart')), h("span", { key: 'ce0c8b28cdb570814c4882b6c5e4b24f1f995c02', slot: "price" }, (_0 = this.order) === null || _0 === void 0 ? void 0 : _0.total_display_amount), h("span", { key: '70f434cf99646f47114e4c2f81dea901ff9f60a9', slot: "currency" }, (_1 = this.order) === null || _1 === void 0 ? void 0 : _1.currency)), !!((_2 = this.order) === null || _2 === void 0 ? void 0 : _2.proration_amount) && (h("sc-line-item", { key: '73d432b7097188525c3877ed725739d4fb381329' }, h("span", { key: 'c8faaa6437e5db6fa2764de8370044c31fe32d69', slot: "description" }, wp.i18n.__('Proration', 'surecart')), h("span", { key: 'ba1928ae23196622c0f426901a2129e84f72d3a3', slot: "price", style: {
                'font-weight': 'var(--sc-font-weight-semibold)',
                'color': 'var(--sc-color-gray-800)',
            } }, (_3 = this.order) === null || _3 === void 0 ? void 0 : _3.proration_display_amount))), !!((_4 = this.order) === null || _4 === void 0 ? void 0 : _4.applied_balance_amount) && (h("sc-line-item", { key: 'f52b062b1cf20e52eb674022004f636a66c5c4e0' }, h("span", { key: '5a6d9ea09f86d8d29fa669c3c9cd359f05199035', slot: "description" }, wp.i18n.__('Applied Balance', 'surecart')), h("span", { key: '53daae2b26bfa7646d90046e39d53ad2dde67999', style: {
                'font-weight': 'var(--sc-font-weight-semibold)',
                'color': 'var(--sc-color-gray-800)',
            }, slot: "price" }, (_5 = this.order) === null || _5 === void 0 ? void 0 : _5.applied_balance_display_amount))), !!((_6 = this.order) === null || _6 === void 0 ? void 0 : _6.credited_balance_amount) && (h("sc-line-item", { key: 'e80b1a13b1eb6316d02cf6bc67fd298174169636' }, h("span", { key: '35a1410ae3b7f405851ca5eb85da4fcee5a48704', slot: "description" }, wp.i18n.__('Credited Balance', 'surecart')), h("span", { key: 'd00d42e732ef2798ca268eeae50dc6dcc7a661ca', slot: "price", style: {
                'font-weight': 'var(--sc-font-weight-semibold)',
                'color': 'var(--sc-color-gray-800)',
            } }, (_7 = this.order) === null || _7 === void 0 ? void 0 : _7.credited_balance_display_amount))), ((_8 = this.order) === null || _8 === void 0 ? void 0 : _8.amount_due) !== ((_9 = this.order) === null || _9 === void 0 ? void 0 : _9.total_amount) && (h("sc-line-item", { key: '8204edf37ddddf30697e616104b3398a427652b7', style: {
                'width': '100%',
                '--price-size': 'var(--sc-font-size-x-large)',
            } }, h("span", { key: 'fabeec9fb543336c243a3586d3cca6a28d0f54fa', slot: "title" }, wp.i18n.__('Amount Due', 'surecart')), h("span", { key: '4f698b3547651910327449a4f3a9cd5001cf2677', slot: "price" }, (_10 = this.order) === null || _10 === void 0 ? void 0 : _10.amount_due_display_amount), h("span", { key: 'f66cd99ff5c2796b2ac8f20e9af60dc12bc4c601', slot: "currency" }, (_11 = this.order) === null || _11 === void 0 ? void 0 : _11.currency))), h("sc-divider", { key: '677a3670b1c06446509f4f47fb57208c6a8a7f71', style: { '--spacing': 'var(--sc-spacing-x-small)' } }), !!((_12 = this.order) === null || _12 === void 0 ? void 0 : _12.paid_amount) && (h("sc-line-item", { key: '7f34751ccd7e6836937c30fd1c123f7107254525', style: {
                'width': '100%',
                '--price-size': 'var(--sc-font-size-x-large)',
            } }, h("span", { key: 'abfad59eabf13609b9162fd32379878ec0250d98', slot: "title" }, wp.i18n.__('Paid', 'surecart')), h("span", { key: '651511073c5f071b05181ee7412bf640d9446e44', slot: "price" }, (_13 = this.order) === null || _13 === void 0 ? void 0 : _13.paid_display_amount), h("span", { key: '4e7132dbaf856598ba065ce19131032a7d301a46', slot: "currency" }, (_14 = this.order) === null || _14 === void 0 ? void 0 : _14.currency))), !!((_15 = this.order) === null || _15 === void 0 ? void 0 : _15.refunded_amount) && (h(Fragment, { key: '544c3ef838c7a2e05d70b3a24cf2513f9ee7397b' }, h("sc-line-item", { key: '99ad3a16df151afd2e6f869fa4b1d8f300e53c3e', style: {
                'width': '100%',
                '--price-size': 'var(--sc-font-size-x-large)',
            } }, h("span", { key: '439fee92f02b69c062be5036c719746ed1a15a81', slot: "description" }, wp.i18n.__('Refunded', 'surecart')), h("span", { key: '15fa649e6bc6d9e5b4cdd5643f9ecec36d0be2cd', slot: "price" }, (_16 = this.order) === null || _16 === void 0 ? void 0 : _16.refunded_display_amount)), h("sc-line-item", { key: 'b9b20e02ba6c0b85b3ef5c4982451df9dd50ef0c', style: {
                'width': '100%',
                '--price-size': 'var(--sc-font-size-x-large)',
            } }, h("span", { key: '9db04fbf4d0ee4974069f1763f55f12061f6ef99', slot: "title" }, wp.i18n.__('Net Payment', 'surecart')), h("span", { key: 'a1f91cc39d57e831131ecd88e5d54c82aee43b27', slot: "price" }, (_17 = this.order) === null || _17 === void 0 ? void 0 : _17.net_paid_display_amount)))), ((_18 = this.order) === null || _18 === void 0 ? void 0 : _18.tax_reverse_charged_amount) > 0 && (h("sc-line-item", { key: '6d93d4ea65f3685e79fa55f9dc51462aed9408dd' }, h("span", { key: 'f1399755fbfb0630ef895812fc4204d2b9e06abb', slot: "description" }, wp.i18n.__('*Tax to be paid on reverse charge basis', 'surecart'))))));
    }
};
openWormhole(ScOrderConfirmationTotals, ['order', 'busy', 'loading', 'empty'], false);
ScOrderConfirmationTotals.style = ScOrderConfirmationTotalsStyle0;

export { ScOrderConfirmationLineItems as sc_order_confirmation_line_items, ScOrderConfirmationTotals as sc_order_confirmation_totals };

//# sourceMappingURL=sc-order-confirmation-line-items_2.entry.js.map