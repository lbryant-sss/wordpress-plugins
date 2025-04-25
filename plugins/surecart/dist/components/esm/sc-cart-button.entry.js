import { r as registerInstance, h, H as Host, a as getElement } from './index-745b6bec.js';
import { a as store, d as onChange, s as state } from './mutations-6f9b9a86.js';
import './index-06061d4e.js';
import './utils-cd1431df.js';
import './remove-query-args-938c53ea.js';
import './add-query-args-0e2a8393.js';
import './index-c5a96d53.js';
import './google-a86aa761.js';
import './currency-a0c9bff4.js';
import './store-627acec4.js';
import './price-7bb626d0.js';

const scCartButtonCss = ":host{display:inline-block;vertical-align:middle;line-height:1}::slotted(*){display:block !important;line-height:1}.cart__button{padding:0 4px;height:100%;display:grid;align-items:center}.cart__content{position:relative}.cart__count{box-sizing:border-box;position:absolute;inset:-12px -16px auto auto;text-align:center;font-size:10px;font-weight:bold;border-radius:var(--sc-cart-icon-counter-border-radius, 9999px);color:var(--sc-cart-icon-counter-color, var(--sc-color-primary-text, var(--sc-color-white)));background:var(--sc-cart-icon-counter-background, var(--sc-color-primary-500));box-shadow:var(--sc-cart-icon-box-shadow, var(--sc-shadow-x-large));padding:2px 6px;line-height:14px;min-width:14px;z-index:1}.cart__icon{font-size:var(--sc-cart-icon-size, 1.1em);cursor:pointer}.cart__icon sc-icon{display:block}";
const ScCartButtonStyle0 = scCartButtonCss;

const ScCartButton = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.open = null;
        this.count = 0;
        this.formId = undefined;
        this.mode = 'live';
        this.cartMenuAlwaysShown = true;
        this.showEmptyCount = false;
    }
    /** Count the number of items in the cart. */
    getItemsCount() {
        var _a, _b;
        const items = (_b = (_a = state === null || state === void 0 ? void 0 : state.checkout) === null || _a === void 0 ? void 0 : _a.line_items) === null || _b === void 0 ? void 0 : _b.data;
        let count = 0;
        (items || []).forEach(item => {
            count = count + (item === null || item === void 0 ? void 0 : item.quantity);
        });
        return count;
    }
    componentDidLoad() {
        this.link = this.el.closest('a');
        // this is to keep the structure that WordPress expects for theme styling.
        this.link.addEventListener('click', e => {
            e.preventDefault();
            e.stopImmediatePropagation();
            store.state.cart = { ...store.state.cart, open: !store.state.cart.open };
            return false;
        });
        // maybe hide the parent <a> if there are no items in the cart.
        this.handleParentLinkDisplay();
        onChange(this.mode, () => this.handleParentLinkDisplay());
    }
    handleParentLinkDisplay() {
        this.link.style.display = !this.cartMenuAlwaysShown && !this.getItemsCount() ? 'none' : null;
    }
    render() {
        return (h(Host, { key: 'b5f3145db423dbff854a1b888bd57cc56fa9432b', tabindex: 0, role: "button", "aria-label": wp.i18n.sprintf(wp.i18n.__('Open Cart Menu Icon with %d items.', 'surecart'), this.getItemsCount()), onKeyDown: e => {
                if ('Enter' === (e === null || e === void 0 ? void 0 : e.code) || 'Space' === (e === null || e === void 0 ? void 0 : e.code)) {
                    store.state.cart = { ...store.state.cart, open: !store.state.cart.open };
                    e.preventDefault();
                }
            } }, h("div", { key: '326c221365d5f0caf5dd4d992800dcfa1169de14', class: "cart__button", part: "base" }, h("div", { key: 'e55c4ab8950b409b6e1a7bf001ab1a8bc3033f44', class: "cart__content" }, (this.showEmptyCount || !!this.getItemsCount()) && (h("span", { key: '1291c1c23a2704ec69845c6775f7b0cc2a549a0c', class: "cart__count", part: "count" }, this.getItemsCount())), h("div", { key: '579c7f0c9f91ba2c0664c78e9c57481a73325013', class: "cart__icon" }, h("slot", { key: '5ff4227d1e493e58239c7cde85fdfc7eb05fcf63' }))))));
    }
    get el() { return getElement(this); }
};
ScCartButton.style = ScCartButtonStyle0;

export { ScCartButton as sc_cart_button };

//# sourceMappingURL=sc-cart-button.entry.js.map