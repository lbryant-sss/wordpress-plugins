'use strict';

Object.defineProperty(exports, '__esModule', { value: true });

const index = require('./index-8acc3c89.js');

const scOrderReturnBadgeCss = ":host{display:inline-block}";
const ScOrderReturnBadgeStyle0 = scOrderReturnBadgeCss;

const status = {
    open: wp.i18n.__('Return in progress', 'surecart'),
    completed: wp.i18n.__('Returned', 'surecart'),
};
const type = {
    open: 'warning',
    completed: 'success',
};
const ScOrderReturnBadge = class {
    constructor(hostRef) {
        index.registerInstance(this, hostRef);
        this.status = undefined;
        this.size = 'medium';
        this.pill = false;
        this.clearable = false;
    }
    render() {
        return (index.h("sc-tag", { key: '69767d8e3afdcaf659318b8f1e55595e81702b6c', type: type === null || type === void 0 ? void 0 : type[this === null || this === void 0 ? void 0 : this.status], pill: this.pill }, (status === null || status === void 0 ? void 0 : status[this.status]) || this.status));
    }
};
ScOrderReturnBadge.style = ScOrderReturnBadgeStyle0;

exports.sc_order_return_badge = ScOrderReturnBadge;

//# sourceMappingURL=sc-order-return-badge.cjs.entry.js.map