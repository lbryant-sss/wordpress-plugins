import { r as registerInstance, h, F as Fragment, a as getElement } from './index-745b6bec.js';
import { g as getLineItemByProductId } from './getters-970cdda4.js';
import { u as updateFormState, s as state, o as onChange } from './mutations-6bbbe793.js';
import { g as getVariantFromValues } from './util-50af2a83.js';
import { u as updateLineItem, a as addLineItem } from './index-a2617916.js';
import { c as createErrorNotice } from './mutations-ed6d0770.js';
import { i as isProductVariantOptionSoldOut, a as isProductVariantOptionMissing } from './utils-cd1431df.js';
import './address-b892540d.js';
import './index-06061d4e.js';
import './remove-query-args-938c53ea.js';
import './add-query-args-0e2a8393.js';
import './index-c5a96d53.js';
import './google-a86aa761.js';
import './currency-a0c9bff4.js';
import './store-627acec4.js';
import './price-af9f0dbf.js';
import './fetch-8ecbbe53.js';

const scCheckoutProductPriceVariantSelectorCss = "sc-checkout-product-price-variant-selector{display:block}.sc-form-label{font-family:var(--sc-font-sans);font-size:var(--sc-font-size-medium);font-weight:var(--sc-font-weight-normal);margin-bottom:var(--sc-input-label-margin);display:inline-block;color:var(--sc-input-label-color);font-weight:var(--sc-input-label-font-weight);text-transform:var(--sc-input-label-text-transform, none);letter-spacing:var(--sc-input-label-letter-spacing, 0)}.sc-form-select{--sc-form-select-bg-img:url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23374151' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e\");display:block;width:100%;padding:0 var(--sc-input-spacing-medium);height:calc(var(--sc-input-height-medium) - var(--sc-input-border-width) * 2);font-size:var(--sc-input-font-size-medium);font-family:var(--sc-input-font-family);font-weight:var(--sc-input-font-weight);color:var(--sc-color-gray-700);box-sizing:border-box;background-color:var(--sc-input-background-color);background-image:var(--sc-form-select-bg-img), var(--sc-form-select-bg-icon, none);background-repeat:no-repeat;background-position:right calc(var(--sc-input-spacing-medium) - 2px) center;background-size:16px 12px;border:var(--sc-input-border-width) solid var(--sc-input-border-color);border-radius:var(--sc-border-radius-medium);box-shadow:var(--sc-input-box-shadow);transition:var(--sc-input-transition, var(--sc-transition-medium)) color, var(--sc-input-transition, var(--sc-transition-medium)) border, var(--sc-input-transition, var(--sc-transition-medium)) box-shadow;appearance:none}.sc-form-select:hover{color:var(--sc-input-color-hover);background-color:var(--sc-input-background-color-hover);border-color:var(--sc-input-border-color-hover);z-index:7}.sc-form-select:focus{color:var(--sc-input-color-focus);background-color:var(--sc-input-background-color-focus) !important;border-color:var(--sc-input-border-color-focus) !important;outline:0;box-shadow:0 0 0 var(--sc-focus-ring-width) var(--sc-focus-ring-color-primary)}.sc-form-select[multiple],.sc-form-select[size]:not([size=\"1\"]){padding-right:var(--sc-input-spacing-medium);background-image:none}.sc-form-select:disabled{color:var(--sc-input-color-disabled);background-color:var(--sc-input-background-color-disabled);border-color:var(--sc-input-border-color-disabled)}.sc-form-select:-moz-focusring{color:transparent;text-shadow:0 0 0 var(--sc-input-color)}.form-select-sm{padding-top:var(--sc-input-spacing-small);padding-bottom:var(--sc-input-spacing-small);padding-left:var(--sc-input-spacing-small);font-size:var(--sc-input-font-size-small);border-radius:var(--sc-border-radius-small)}.form-select-lg{padding-top:var(--sc-input-spacing-large);padding-bottom:var(--sc-input-spacing-large);padding-left:var(--sc-input-spacing-large);font-size:var(--sc-input-font-size-large);border-radius:var(--sc-border-radius-large)}.sc-checkout-product-price-variant-selector{position:relative}.sc-checkout-product-price-variant-selector>*:not(:last-child){display:block;margin-bottom:var(--sc-form-row-spacing, 0.75em)}.sc-checkout-product-price-variant-selector__pills-wrapper{display:flex;flex-wrap:wrap;gap:var(--sc-spacing-x-small)}.sc-checkout-product-price-variant-selector__hidden-input{position:absolute !important;top:0 !important;left:0 !important;opacity:0 !important;padding:0px !important;margin:0px !important;pointer-events:none !important;width:0 !important}";
const ScCheckoutProductPriceVariantSelectorStyle0 = scCheckoutProductPriceVariantSelectorCss;

const ScProductCheckoutSelectVariantOption = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.product = undefined;
        this.label = undefined;
        this.selectorTitle = undefined;
        this.selectedVariant = undefined;
        this.selectedPrice = undefined;
        this.option1 = undefined;
        this.option2 = undefined;
        this.option3 = undefined;
    }
    /** When option values are selected, attempt to find a matching variant. */
    handleOptionChange() {
        var _a, _b;
        this.selectedVariant = getVariantFromValues({
            variants: (_b = (_a = this.product) === null || _a === void 0 ? void 0 : _a.variants) === null || _b === void 0 ? void 0 : _b.data,
            values: {
                ...(this.option1 ? { option_1: this.option1 } : {}),
                ...(this.option2 ? { option_2: this.option2 } : {}),
                ...(this.option3 ? { option_3: this.option3 } : {}),
            },
        });
    }
    /**
     * Is the selected variant out of stock?
     * @returns {boolean} Whether the selected variant is out of stock.
     */
    isSelectedVariantOutOfStock() {
        var _a, _b;
        return ((_a = this.product) === null || _a === void 0 ? void 0 : _a.stock_enabled) && this.hasVariants() && !((_b = this.product) === null || _b === void 0 ? void 0 : _b.allow_out_of_stock_purchases) && this.selectedVariant.available_stock < 1;
    }
    /**
     * Do we have the required selected variant?
     * @returns {boolean} Whether the product has a required variant and it is not selected.
     */
    hasRequiredSelectedVariant() {
        var _a;
        if (!this.hasVariants()) {
            return true;
        }
        return (_a = this.selectedVariant) === null || _a === void 0 ? void 0 : _a.id;
    }
    async reportValidity() {
        this.input.setCustomValidity('');
        if (!this.hasVariants()) {
            return this.input.reportValidity();
        }
        // We don't have a required selected variant.
        if (!this.hasRequiredSelectedVariant()) {
            this.input.setCustomValidity(wp.i18n.__('Please choose an available option.', 'surecart'));
            return this.input.reportValidity();
        }
        // don't let the person checkout with an out of stock selection.
        if (this.isSelectedVariantOutOfStock()) {
            this.input.setCustomValidity(wp.i18n.__('This selection is not available.', 'surecart'));
            return this.input.reportValidity();
        }
        return this.input.reportValidity();
    }
    getSelectedPrice() {
        var _a, _b, _c, _d, _e;
        if (((_c = (_b = (_a = this.product) === null || _a === void 0 ? void 0 : _a.prices) === null || _b === void 0 ? void 0 : _b.data) === null || _c === void 0 ? void 0 : _c.length) === 1) {
            return (_e = (_d = this.product) === null || _d === void 0 ? void 0 : _d.prices) === null || _e === void 0 ? void 0 : _e.data[0];
        }
        return this.selectedPrice;
    }
    /** When selected variant and selected price are set, we can update the checkout. */
    async updateLineItems() {
        var _a, _b, _c, _d, _e;
        const selectedPrice = this.getSelectedPrice();
        // We need a price.
        if (!(selectedPrice === null || selectedPrice === void 0 ? void 0 : selectedPrice.id))
            return;
        // get the existing line item.
        const lineItem = this.lineItem();
        // no changes.
        if (((_a = lineItem === null || lineItem === void 0 ? void 0 : lineItem.price) === null || _a === void 0 ? void 0 : _a.id) === (selectedPrice === null || selectedPrice === void 0 ? void 0 : selectedPrice.id) && ((_b = lineItem === null || lineItem === void 0 ? void 0 : lineItem.variant) === null || _b === void 0 ? void 0 : _b.id) === ((_c = this.selectedVariant) === null || _c === void 0 ? void 0 : _c.id))
            return;
        // We need a selected variant if this product has variants.
        if (!this.hasRequiredSelectedVariant())
            return;
        // Don't let the person checkout with an out of stock selection.
        if (this.isSelectedVariantOutOfStock())
            return;
        // create or update the
        try {
            updateFormState('FETCH');
            if (lineItem === null || lineItem === void 0 ? void 0 : lineItem.id) {
                state.checkout = await updateLineItem({
                    id: lineItem === null || lineItem === void 0 ? void 0 : lineItem.id,
                    data: {
                        variant: (_d = this.selectedVariant) === null || _d === void 0 ? void 0 : _d.id,
                        price: selectedPrice === null || selectedPrice === void 0 ? void 0 : selectedPrice.id,
                        quantity: 1,
                    },
                });
            }
            else {
                state.checkout = await addLineItem({
                    checkout: state.checkout,
                    data: {
                        variant: (_e = this.selectedVariant) === null || _e === void 0 ? void 0 : _e.id,
                        price: selectedPrice === null || selectedPrice === void 0 ? void 0 : selectedPrice.id,
                        quantity: 1,
                    },
                });
            }
            updateFormState('RESOLVE');
        }
        catch (e) {
            console.error(e);
            createErrorNotice(e);
            updateFormState('REJECT');
        }
    }
    componentWillLoad() {
        // when checkout changes, update the selected variant and price.
        this.removeListener = onChange('checkout', () => {
            var _a, _b, _c;
            const lineItem = this.lineItem();
            this.selectedVariant = lineItem === null || lineItem === void 0 ? void 0 : lineItem.variant;
            this.selectedPrice = lineItem === null || lineItem === void 0 ? void 0 : lineItem.price;
            this.option1 = (_a = lineItem === null || lineItem === void 0 ? void 0 : lineItem.variant) === null || _a === void 0 ? void 0 : _a.option_1;
            this.option2 = (_b = lineItem === null || lineItem === void 0 ? void 0 : lineItem.variant) === null || _b === void 0 ? void 0 : _b.option_2;
            this.option3 = (_c = lineItem === null || lineItem === void 0 ? void 0 : lineItem.variant) === null || _c === void 0 ? void 0 : _c.option_3;
        });
    }
    // remove listener to prevent leaks.
    disconnectedCallback() {
        this.removeListener();
    }
    // get the line item by product id.
    lineItem() {
        var _a;
        return getLineItemByProductId((_a = this.product) === null || _a === void 0 ? void 0 : _a.id);
    }
    hasVariants() {
        var _a, _b, _c;
        return ((_c = (_b = (_a = this.product) === null || _a === void 0 ? void 0 : _a.variants) === null || _b === void 0 ? void 0 : _b.data) === null || _c === void 0 ? void 0 : _c.length) > 0;
    }
    isProductInCheckout() {
        var _a, _b;
        return (((_b = (_a = state.checkout) === null || _a === void 0 ? void 0 : _a.line_items) === null || _b === void 0 ? void 0 : _b.data) || []).some(lineItem => { var _a, _b, _c; return ((_b = (_a = lineItem.price) === null || _a === void 0 ? void 0 : _a.product) === null || _b === void 0 ? void 0 : _b.id) === ((_c = this.product) === null || _c === void 0 ? void 0 : _c.id); });
    }
    renderDropdown({ name, values, index }) {
        if (!this.isProductInCheckout()) {
            return null;
        }
        return (h("div", { class: "sc-select-option__wrapper" }, h("label", { class: "sc-form-label" }, name), h("select", { class: "sc-form-select", onChange: e => (this[`option${index + 1}`] = e.target.value) }, values.map(value => {
            const isUnavailable = isProductVariantOptionSoldOut.apply(void 0, [
                index + 1,
                value,
                {
                    ...(this.option1 ? { option_1: this.option1 } : {}),
                    ...(this.option2 ? { option_2: this.option2 } : {}),
                    ...(this.option3 ? { option_3: this.option3 } : {}),
                },
                this.product,
            ]) ||
                isProductVariantOptionMissing.apply(void 0, [
                    index + 1,
                    value,
                    {
                        ...(this.option1 ? { option_1: this.option1 } : {}),
                        ...(this.option2 ? { option_2: this.option2 } : {}),
                        ...(this.option3 ? { option_3: this.option3 } : {}),
                    },
                    this.product,
                ]);
            return (h("option", { value: value, selected: this[`option${index + 1}`] === value }, value, " ", isUnavailable && h(Fragment, null, " ", wp.i18n.__('(unavailable)', 'surecart'))));
        }))));
    }
    renderPills({ name, values, index }) {
        if (!this.isProductInCheckout()) {
            return null;
        }
        return (h("sc-form-control", { label: name }, h("div", { class: "sc-checkout-product-price-variant-selector__pills-wrapper" }, (values || []).map(value => {
            const args = [
                index + 1,
                value,
                {
                    ...(this.option1 ? { option_1: this.option1 } : {}),
                    ...(this.option2 ? { option_2: this.option2 } : {}),
                    ...(this.option3 ? { option_3: this.option3 } : {}),
                },
                this.product,
            ];
            const isUnavailable = isProductVariantOptionSoldOut.apply(void 0, args) || isProductVariantOptionMissing.apply(void 0, args);
            return (h("sc-pill-option", { isUnavailable: isUnavailable, isSelected: this[`option${index + 1}`] === value, onClick: () => (this[`option${index + 1}`] = value) }, h("span", { "aria-hidden": "true" }, value), h("sc-visually-hidden", null, wp.i18n.sprintf(wp.i18n.__('Select %s: %s', 'surecart'), name, value), isUnavailable && h(Fragment, null, " ", wp.i18n.__('(option unavailable)', 'surecart')))));
        }))));
    }
    render() {
        var _a, _b, _c, _d, _e;
        return (h("sc-form-control", { key: '461b978789aa5d4cbedf2a73e751690fce87a686', class: "sc-checkout-product-price-variant-selector", label: this.selectorTitle }, (this.product.variant_options.data || []).map(({ name, values, display_type }, index) => {
            if (display_type === 'dropdown') {
                return this.renderDropdown({ name, values, index });
            }
            return this.renderPills({ name, values, index });
        }), ((_c = (_b = (_a = this.product) === null || _a === void 0 ? void 0 : _a.prices) === null || _b === void 0 ? void 0 : _b.data) === null || _c === void 0 ? void 0 : _c.length) > 1 && (h("sc-form-control", { key: '710dacae832e154690c7a29c0badb450a0b4552a', label: !!((_d = this.product.variant_options.data) === null || _d === void 0 ? void 0 : _d.length) ? this.label : null }, h("sc-choices", { key: 'bae8821a607043516f2c572f51689e0dad21aa37' }, (this.product.prices.data || [])
            .sort((a, b) => (a === null || a === void 0 ? void 0 : a.position) - (b === null || b === void 0 ? void 0 : b.position))
            .map(price => {
            var _a, _b, _c;
            return (h("sc-price-choice-container", { required: true, price: price, label: (price === null || price === void 0 ? void 0 : price.name) || ((_a = this.product) === null || _a === void 0 ? void 0 : _a.name), checked: ((_c = (_b = this.lineItem()) === null || _b === void 0 ? void 0 : _b.price) === null || _c === void 0 ? void 0 : _c.id) === (price === null || price === void 0 ? void 0 : price.id), onScChange: e => {
                    if (e.target.checked) {
                        this.selectedPrice = price;
                    }
                } }));
        })))), h("input", { key: '2ce25324f8defdd723d576e21d87d2ecefbfafa1', class: "sc-checkout-product-price-variant-selector__hidden-input", ref: el => (this.input = el), value: (_e = this.selectedVariant) === null || _e === void 0 ? void 0 : _e.id })));
    }
    get el() { return getElement(this); }
    static get watchers() { return {
        "option1": ["handleOptionChange"],
        "option2": ["handleOptionChange"],
        "option3": ["handleOptionChange"],
        "selectedVariant": ["updateLineItems"],
        "selectedPrice": ["updateLineItems"]
    }; }
};
ScProductCheckoutSelectVariantOption.style = ScCheckoutProductPriceVariantSelectorStyle0;

export { ScProductCheckoutSelectVariantOption as sc_checkout_product_price_variant_selector };

//# sourceMappingURL=sc-checkout-product-price-variant-selector.entry.js.map