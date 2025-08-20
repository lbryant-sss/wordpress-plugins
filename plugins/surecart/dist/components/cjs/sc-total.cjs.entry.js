'use strict';

Object.defineProperty(exports, '__esModule', { value: true });

const index = require('./index-8acc3c89.js');
const mutations = require('./mutations-10a18c83.js');
require('./index-bcdafe6e.js');
require('./utils-2e91d46c.js');
require('./remove-query-args-b57e8cd3.js');
require('./add-query-args-49dcb630.js');
require('./index-fb76df07.js');
require('./google-59d23803.js');
require('./currency-71fce0f0.js');
require('./store-4a539aea.js');
require('./price-5b1afcfe.js');

const scTotalCss = ":host{display:block}.total-amount{display:inline-block}";
const ScTotalStyle0 = scTotalCss;

const ORDER_KEYS = {
    total: 'total_display_amount',
    subtotal: 'subtotal_display_amount',
    amount_due: 'amount_due_display_amount',
};
const ScTotal = class {
    constructor(hostRef) {
        index.registerInstance(this, hostRef);
        this.order_key = {
            total: 'total_display_amount',
            subtotal: 'subtotal_display_amount',
            amount_due: 'amount_due_display_amount',
        };
        this.total = 'amount_due';
        this.checkout = undefined;
    }
    render() {
        var _a, _b;
        const checkoutData = this.checkout || (mutations.state === null || mutations.state === void 0 ? void 0 : mutations.state.checkout);
        if (!(checkoutData === null || checkoutData === void 0 ? void 0 : checkoutData.currency))
            return;
        if (!((_b = (_a = checkoutData === null || checkoutData === void 0 ? void 0 : checkoutData.line_items) === null || _a === void 0 ? void 0 : _a.data) === null || _b === void 0 ? void 0 : _b.length))
            return;
        return (checkoutData === null || checkoutData === void 0 ? void 0 : checkoutData[ORDER_KEYS[this.total]]) || '';
    }
};
ScTotal.style = ScTotalStyle0;

exports.sc_total = ScTotal;

//# sourceMappingURL=sc-total.cjs.entry.js.map