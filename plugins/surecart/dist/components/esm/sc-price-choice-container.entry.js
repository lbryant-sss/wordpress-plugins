import { r as registerInstance, c as createEvent, h, F as Fragment } from './index-745b6bec.js';
import { i as intervalString } from './price-af9f0dbf.js';
import './currency-a0c9bff4.js';

const scPriceChoiceContainerCss = ":host {\n  display: block;\n  min-width: 0;\n  width: 100%;\n}\n\nsc-choice-container {\n  container-type: inline-size;\n}\n\n.price-choice {\n  display: flex;\n  align-items: center;\n  justify-content: space-between;\n  line-height: var(--sc-line-height-dense);\n  gap: var(--sc-spacing-small);\n}\n.price-choice__name {\n  display: inline-block;\n  font-size: var(--sc-price-choice-name-size, var(--sc-input-label-font-size-medium));\n  font-weight: var(--sc-price-choice-name-font-weight, var(--sc-font-weight-bold));\n  text-transform: var(--sc-price-choice-text-transform, var(--sc-input-label-text-transform, none));\n}\n.price-choice > *:not(:first-child):last-child {\n  text-align: right;\n}\n.price-choice__details {\n  flex: 1 0 50%;\n  display: grid;\n  gap: var(--sc-spacing-xxx-small);\n}\n.price-choice__trial, .price-choice__setup-fee, .price-choice__price {\n  font-size: var(--sc-font-size-small);\n  opacity: 0.8;\n}\n.price-choice__price {\n  font-weight: var(--sc-price-choice-price-font-weight, var(--sc-font-weight-normal));\n}\n\n@container (max-width: 325px) {\n  .price-choice {\n    flex-direction: column;\n    align-items: flex-start;\n    gap: var(--sc-spacing-xx-small);\n  }\n  .price-choice > *:not(:first-child):last-child {\n    text-align: initial;\n  }\n}";
const ScPriceChoiceContainerStyle0 = scPriceChoiceContainerCss;

const ScPriceChoiceContainer = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.scChange = createEvent(this, "scChange", 7);
        this.price = undefined;
        this.loading = false;
        this.label = undefined;
        this.showLabel = true;
        this.showPrice = true;
        this.showControl = false;
        this.description = undefined;
        this.type = undefined;
        this.required = false;
        this.checked = false;
        this.priceData = undefined;
    }
    handlePriceChange() {
        this.priceData = typeof this.price === 'string' ? JSON.parse(this.price) : this.price;
    }
    componentWillLoad() {
        this.handlePriceChange();
    }
    renderPrice() {
        var _a;
        return (h(Fragment, null, (_a = this.priceData) === null || _a === void 0 ? void 0 :
            _a.display_amount, intervalString(this.priceData, {
            showOnce: true,
            abbreviate: true,
            labels: {
                interval: '/',
                period: 
                /** translators: used as in time period: "for 3 months" */
                wp.i18n.__('for', 'surecart'),
            },
        })));
    }
    render() {
        var _a, _b, _c, _d, _e, _f, _g, _h, _j, _k, _l, _m;
        if (this.loading) {
            return (h("sc-choice-container", { showControl: this.showControl, name: "loading", disabled: true }, h("div", { class: "price-choice" }, h("sc-skeleton", { style: { width: '60px', display: 'inline-block' } }), h("sc-skeleton", { style: { width: '80px', display: 'inline-block' } }))));
        }
        return (h("sc-choice-container", { value: (_a = this.priceData) === null || _a === void 0 ? void 0 : _a.id, type: this.type, showControl: this.showControl, checked: this.checked, onScChange: () => this.scChange.emit(), required: this.required }, h("div", { class: "price-choice" }, this.showLabel && (h("div", { class: "price-choice__title" }, h("div", { class: "price-choice__name" }, this.label || this.priceData.name), !!this.description && h("div", { class: "price-choice__description" }, this.description))), this.showPrice && (h("div", { class: "price-choice__details" }, h("div", { class: "price-choice__price" }, ((_b = this.priceData) === null || _b === void 0 ? void 0 : _b.ad_hoc) ? (wp.i18n.__('Custom Amount', 'surecart')) : (h(Fragment, null, (_c = this.priceData) === null || _c === void 0 ? void 0 :
            _c.display_amount, ((_d = this.priceData) === null || _d === void 0 ? void 0 : _d.recurring_period_count) && 1 <= ((_e = this.priceData) === null || _e === void 0 ? void 0 : _e.recurring_period_count) && (h("sc-visually-hidden", null, ' ', wp.i18n.__('This is a repeating price. Payment will happen', 'surecart'), ' ', intervalString(this.priceData, {
            showOnce: true,
            abbreviate: false,
            labels: {
                interval: wp.i18n.__('every', 'surecart'),
                period: 
                /** translators: used as in time period: "for 3 months" */
                wp.i18n.__('for', 'surecart'),
            },
        }))), h("span", { "aria-hidden": "true" }, intervalString(this.priceData, {
            showOnce: true,
            abbreviate: true,
            labels: {
                interval: '/',
                period: 
                /** translators: used as in time period: "for 3 months" */
                wp.i18n.__('for', 'surecart'),
            },
        }))))), !!((_f = this.priceData) === null || _f === void 0 ? void 0 : _f.trial_duration_days) && (h(Fragment, null, h("sc-visually-hidden", null, wp.i18n.sprintf(wp.i18n.__('You have a %d-day trial before payment becomes necessary.', 'surecart'), (_g = this.priceData) === null || _g === void 0 ? void 0 : _g.trial_duration_days)), h("div", { class: "price-choice__trial", "aria-hidden": "true" }, wp.i18n.sprintf(wp.i18n._n('Starting in %s day', 'Starting in %s days', this.priceData.trial_duration_days, 'surecart'), this.priceData.trial_duration_days)))), !!((_h = this.priceData) === null || _h === void 0 ? void 0 : _h.setup_fee_enabled) && ((_j = this.priceData) === null || _j === void 0 ? void 0 : _j.setup_fee_amount) && (h("div", { class: "price-choice__setup-fee" }, h("sc-visually-hidden", null, wp.i18n.__('This payment plan has', 'surecart'), " "), (_k = this.priceData) === null || _k === void 0 ? void 0 :
            _k.setup_fee_display_amount, ' ', ((_l = this.priceData) === null || _l === void 0 ? void 0 : _l.setup_fee_name) || (((_m = this.priceData) === null || _m === void 0 ? void 0 : _m.setup_fee_amount) < 0 ? wp.i18n.__('Discount', 'surecart') : wp.i18n.__('Setup Fee', 'surecart')))))))));
    }
    static get watchers() { return {
        "price": ["handlePriceChange"]
    }; }
};
ScPriceChoiceContainer.style = ScPriceChoiceContainerStyle0;

export { ScPriceChoiceContainer as sc_price_choice_container };

//# sourceMappingURL=sc-price-choice-container.entry.js.map