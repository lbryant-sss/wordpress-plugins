import { r as registerInstance, c as createEvent, h, a as getElement } from './index-745b6bec.js';
import { g as getIconLibrary } from './library-e110eea6.js';
import { i as isRtl } from './page-align-0cdacf32.js';

const iconFiles = new Map();
const requestIcon = (url) => {
    if (iconFiles.has(url)) {
        return iconFiles.get(url);
    }
    else {
        const request = fetch(url).then(async (response) => {
            if (response.ok) {
                const div = document.createElement('div');
                div.innerHTML = await response.text();
                const svg = div.firstElementChild;
                return {
                    ok: response.ok,
                    status: response.status,
                    svg: svg && svg.tagName.toLowerCase() === 'svg' ? svg.outerHTML : '',
                };
            }
            else {
                return {
                    ok: response.ok,
                    status: response.status,
                    svg: null,
                };
            }
        });
        iconFiles.set(url, request);
        return request;
    }
};

const scIconCss = ":host{--width:1em;--height:1em;display:inline-block;width:var(--width);height:var(--height);contain:strict;box-sizing:content-box !important}.icon,svg{display:block;height:100%;width:100%;stroke-width:var(--sc-icon-stroke-width, 2px)}";
const ScIconStyle0 = scIconCss;

/**
 * The icon's label used for accessibility.
 */
const LABEL_MAPPINGS = {
    'chevron-down': wp.i18n.__('Open', 'surecart'),
    'chevron-up': wp.i18n.__('Close', 'surecart'),
    'chevron-right': wp.i18n.__('Next', 'surecart'),
    'chevron-left': wp.i18n.__('Previous', 'surecart'),
    'arrow-right': wp.i18n.__('Next', 'surecart'),
    'arrow-left': wp.i18n.__('Previous', 'surecart'),
    'arrow-down': wp.i18n.__('Down', 'surecart'),
    'arrow-up': wp.i18n.__('Up', 'surecart'),
    'alert-circle': wp.i18n.__('Alert', 'surecart'),
};
const parser = new DOMParser();
const ScIcon = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.scLoad = createEvent(this, "scLoad", 7);
        this.svg = '';
        this.name = undefined;
        this.src = undefined;
        this.label = undefined;
        this.library = 'default';
    }
    /** @internal Fetches the icon and redraws it. Used to handle library registrations. */
    redraw() {
        this.setIcon();
    }
    componentWillLoad() {
        this.setIcon();
    }
    getLabel() {
        let label = '';
        if (this.label) {
            label = (LABEL_MAPPINGS === null || LABEL_MAPPINGS === void 0 ? void 0 : LABEL_MAPPINGS[this.label]) || this.label;
        }
        else if (this.name) {
            label = ((LABEL_MAPPINGS === null || LABEL_MAPPINGS === void 0 ? void 0 : LABEL_MAPPINGS[this.name]) || this.name).replace(/-/g, ' ');
        }
        else if (this.src) {
            label = this.src.replace(/.*\//, '').replace(/-/g, ' ').replace(/\.svg/i, '');
        }
        return label;
    }
    async setIcon() {
        const library = getIconLibrary(this.library);
        const url = this.getUrl();
        if (url) {
            try {
                const file = await requestIcon(url);
                if (url !== this.getUrl()) {
                    // If the url has changed while fetching the icon, ignore this request
                    return;
                }
                else if (file.ok) {
                    const doc = parser.parseFromString(file.svg, 'text/html');
                    const svgEl = doc.body.querySelector('svg');
                    if (svgEl) {
                        if (library && library.mutator) {
                            library.mutator(svgEl);
                        }
                        this.svg = svgEl.outerHTML;
                        this.scLoad.emit();
                    }
                    else {
                        this.svg = '';
                        console.error({ status: file === null || file === void 0 ? void 0 : file.status });
                    }
                }
                else {
                    this.svg = '';
                    console.error({ status: file === null || file === void 0 ? void 0 : file.status });
                }
            }
            catch {
                console.error({ status: -1 });
            }
        }
        else if (this.svg) {
            // If we can't resolve a URL and an icon was previously set, remove it
            this.svg = '';
        }
    }
    getUrl() {
        const library = getIconLibrary(this.library);
        if (this.name && library) {
            return library.resolver(this.name);
        }
        else {
            return this.src;
        }
    }
    render() {
        return h("div", { key: '47af7eccf5b06b1c0e5a27c3484aaa0081a22461', part: "base", class: "icon", role: "img", "aria-label": this.getLabel(), innerHTML: this.svg });
    }
    static get assetsDirs() { return ["icon-assets"]; }
    static get watchers() { return {
        "name": ["setIcon"],
        "src": ["setIcon"],
        "library": ["setIcon"]
    }; }
};
ScIcon.style = ScIconStyle0;

const scProductLineItemCss = ":host {\n  display: block;\n  font-family: var(--sc-font-sans);\n  --sc-product-line-item-line-gap: 6px;\n}\n\n.item {\n  box-sizing: border-box;\n  margin: 0px;\n  min-width: 0px;\n  display: flex;\n  gap: var(--sc-spacing-large);\n  justify-content: space-between;\n  align-items: stretch;\n  width: 100%;\n  border-bottom: none;\n  container-type: inline-size;\n}\n.item__text-container {\n  box-sizing: border-box;\n  margin: 0px;\n  min-width: 0px;\n  display: flex;\n  flex-direction: column;\n  gap: var(--sc-product-line-item-line-gap);\n  justify-content: space-between;\n  align-items: stretch;\n  width: 100%;\n  border-bottom: none;\n}\n.item__row {\n  display: flex;\n  gap: 18px;\n  justify-content: space-between;\n  align-items: stretch;\n  width: 100%;\n}\n.item__row.stick-bottom {\n  margin-top: auto;\n}\n.item__scratch-price {\n  text-decoration: line-through;\n  font-size: var(--sc-font-size-small);\n  line-height: 1;\n  color: var(--sc-input-help-text-color);\n  white-space: nowrap;\n}\n.item__remove-container {\n  display: flex;\n  gap: 6px;\n  align-items: center;\n  line-height: 1;\n  cursor: pointer;\n  color: var(--sc-input-help-text-color);\n  font-size: var(--sc-input-help-text-font-size-medium);\n}\n\n.item__text {\n  box-sizing: border-box;\n  margin: 0px;\n  min-width: 0px;\n  display: flex;\n  gap: 6px;\n  flex-direction: column;\n  align-items: flex-start;\n  justify-content: flex-start;\n  flex: 1 1 0%;\n}\n\n.item__text-details {\n  display: grid;\n  gap: var(--sc-product-line-item-line-gap);\n}\n\n.item__title {\n  box-sizing: border-box;\n  min-width: 0px;\n  margin: 0;\n  color: var(--sc-line-item-title-color, var(--sc-input-label-color));\n  font-size: var(--sc-font-size-medium);\n  font-weight: var(--sc-font-weight-semibold);\n  line-height: 1;\n  cursor: pointer;\n  display: -webkit-box;\n  display: -moz-box;\n  -webkit-box-orient: vertical;\n  -moz-box-orient: vertical;\n  -webkit-line-clamp: 3;\n  -moz-box-lines: 3;\n  overflow: hidden;\n  text-overflow: ellipsis;\n}\n\n.item__suffix {\n  flex: 1;\n  box-sizing: border-box;\n  margin: 0px;\n  min-width: 0px;\n  display: flex;\n  flex-direction: column;\n  -webkit-box-pack: start;\n  justify-content: space-between;\n  align-items: flex-end;\n  min-width: 100px;\n  margin-left: auto;\n  align-self: center;\n}\n\n.product-line-item__removable .item__suffix {\n  align-self: flex-start;\n}\n\n.product-line-item__editable .item__suffix {\n  align-self: flex-start;\n}\n\n.product-line-item__purchasable-status {\n  font-size: var(--sc-font-size-x-small);\n  color: var(--sc-input-error-text-color);\n}\n\n.item__price {\n  text-align: right;\n  max-width: 100%;\n  display: grid;\n  gap: var(--sc-product-line-item-line-gap);\n}\n\n.item__description {\n  color: var(--sc-price-label-color, var(--sc-input-help-text-color));\n  font-size: var(--sc-price-label-font-size, var(--sc-input-help-text-font-size-medium));\n  line-height: 1;\n  display: flex;\n  flex-wrap: wrap;\n  flex-direction: column;\n  gap: var(--sc-product-line-item-line-gap);\n  text-wrap: pretty;\n}\n.item__description:last-child {\n  align-items: flex-end;\n  text-align: right;\n}\n\n.item__image-placeholder {\n  width: var(--sc-product-line-item-image-size, 65px);\n  height: var(--sc-product-line-item-image-size, 65px);\n  background-color: var(--sc-input-border-color, var(--sc-input-border));\n  border-radius: 4px;\n  flex: 0 0 var(--sc-product-line-item-image-size, 65px);\n}\n\n.item__image,\n.attachment-thumbnail {\n  width: var(--sc-product-line-item-image-size, 65px);\n  height: var(--sc-product-line-item-image-size, 65px);\n  object-fit: cover;\n  border-radius: 4px;\n  border: solid 1px var(--sc-input-border-color, var(--sc-input-border));\n  display: block;\n  box-shadow: var(--sc-input-box-shadow);\n  align-self: flex-start;\n}\n\n@container (max-width: 380px) {\n  .item__image,\n  .item__image-placeholder {\n    display: var(--sc-product-line-item-mobile-image-display, none);\n  }\n}\n.product__description {\n  display: flex;\n  gap: 0.5em;\n  align-items: center;\n}\n\n.price {\n  font-size: var(--sc-font-size-medium);\n  font-weight: var(--sc-font-weight-semibold);\n  color: var(--sc-input-label-color);\n  line-height: 1;\n  white-space: nowrap;\n  display: flex;\n  gap: 4px;\n  align-items: baseline;\n}\n\n.price__description {\n  font-size: var(--sc-font-size-small);\n  line-height: 1;\n  color: var(--sc-input-help-text-color);\n  text-align: right;\n  white-space: nowrap;\n}\n\n.item--is-rtl.price {\n  text-align: right;\n}\n.item--is-rtl .item__price {\n  text-align: left;\n}\n\n.base {\n  display: grid;\n  gap: var(--sc-spacing-x-small);\n}\n\n.fee__description {\n  color: var(--sc-input-help-text-color);\n}\n\nsc-quantity-select::part(base) {\n  box-shadow: none;\n  background-color: transparent;\n}\n\nsc-quantity-select::part(base):not(:focus-within) {\n  border-color: transparent;\n}\n\nsc-quantity-select::part(input),\nsc-quantity-select::part(plus),\nsc-quantity-select::part(minus) {\n  background-color: transparent;\n}";
const ScProductLineItemStyle0 = scProductLineItemCss;

const ScProductLineItem = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.scUpdateQuantity = createEvent(this, "scUpdateQuantity", 3);
        this.scRemove = createEvent(this, "scRemove", 3);
        this.image = undefined;
        this.name = undefined;
        this.amount = undefined;
        this.scratch = undefined;
        this.displayAmount = undefined;
        this.scratchDisplayAmount = undefined;
        this.fees = undefined;
        this.price = undefined;
        this.variant = '';
        this.quantity = undefined;
        this.interval = undefined;
        this.trial = undefined;
        this.removable = undefined;
        this.editable = true;
        this.max = undefined;
        this.sku = '';
        this.purchasableStatus = undefined;
        this.note = undefined;
    }
    render() {
        var _a, _b, _c;
        const isImageFallback = ((_a = this.image) === null || _a === void 0 ? void 0 : _a.type) === 'fallback';
        return (h("div", { key: 'acb31c652369e666ba0772323bb4a764d92f1745', class: "base", part: "base" }, h("div", { key: '4200949e59f4f0c5df4410f372b8072d18865445', part: "product-line-item", class: {
                'item': true,
                'item--has-image': !!((_b = this.image) === null || _b === void 0 ? void 0 : _b.src),
                'item--is-rtl': isRtl(),
                'product-line-item__editable': this.editable,
                'product-line-item__removable': this.removable,
            } }, !!((_c = this.image) === null || _c === void 0 ? void 0 : _c.src) ? (h("img", { ...this.image, part: isImageFallback ? 'placeholder__image' : 'image', class: isImageFallback ? 'item__image-placeholder' : 'item__image' })) : (h("div", { class: "item__image-placeholder", part: "placeholder__image" })), h("div", { key: '82f5eb7d59528d09f1efd683ecb2a022851a55bb', class: "item__text-container" }, h("div", { key: 'cf21770ddef7c26370ca4b93ae12b83d847c519c', class: "item__row" }, h("div", { key: '56c046f833d636300b6d5021f24dcf141b1dcabc', class: "item__title", part: "title" }, h("slot", { key: '09c60e7f51d89129df4d144a63c0216b532acbff', name: "title" }, this.name)), h("div", { key: 'd30573431da79fc330a6f0156c4d904f0bc1d235', class: "price", part: "price__amount" }, !!this.scratch && this.scratch !== this.amount && h("span", { key: 'c3f7ff4ac6b16179939f8dbf471b5daf6606ed3e', class: "item__scratch-price" }, this.scratch), this.amount, h("div", { key: '8fe7b288da8350315cc27a54cbc03e675b781cd3', class: "price__description", part: "price__description" }, this.interval))), h("div", { key: 'cbb34bf3f0cc3981d285af323a4493d952e169c9', class: "item__row" }, h("div", { key: '1574b717f3db4caa379df27f7c1bdac18be18045', class: "item__description", part: "description" }, this.variant && h("div", { key: 'f6258615ba64013ff07d16027f0419090aafc57e' }, this.variant), this.price && h("div", { key: 'a403241ba07d9c0ab96c8205368eec1e7c445f39' }, this.price), this.sku && (h("div", { key: '29e252e1885cd58f5acb21371fb7f1a11cd098ca' }, wp.i18n.__('SKU:', 'surecart'), " ", this.sku)), !!this.purchasableStatus && h("div", { key: '1a57bb5329e4968a449959c98496430c6ece16bf' }, this.purchasableStatus), !!this.note && h("sc-product-line-item-note", { key: '6d7799cd8844ec43bf15f8a5c078869142f8e1cb', note: this.note })), h("div", { key: '85f5dca906ef31adea6376f0d473bf3bee83c98b', class: "item__description", part: "trial-fees" }, !!this.trial && h("div", { key: '09024d942a2b6200fa572f0d9c72159a3636c854' }, this.trial), (this.fees || []).map(fee => {
            return (h("div", null, fee === null || fee === void 0 ? void 0 :
                fee.display_amount, " ", fee === null || fee === void 0 ? void 0 :
                fee.description));
        }))), h("div", { key: '59f95fadf33b9cfcc341e1bc1493618a3e1bd4da', class: "item__row stick-bottom" }, this.editable ? (h("sc-quantity-select", { max: this.max || Infinity, exportparts: "base:quantity, minus:quantity__minus, minus-icon:quantity__minus-icon, plus:quantity__plus, plus-icon:quantity__plus-icon, input:quantity__input", clickEl: this.el, quantity: this.quantity, size: "small", onScChange: e => e.detail && this.scUpdateQuantity.emit(e.detail), "aria-label": 
            /** translators: %1$s: product name, %2$s: product price name */
            wp.i18n.sprintf(wp.i18n.__('Change Quantity - %1$s %2$s', 'surecart'), this.name, this.price) })) : (h("span", { class: "item__description", part: "static-quantity" }, wp.i18n.__('Qty:', 'surecart'), " ", this.quantity)), !!this.removable && (h("div", { key: '53bd671f6422947b7174b5036d81aac82e38906b', class: "item__remove-container", onClick: () => this.scRemove.emit(), onKeyDown: e => {
                if (e.key === 'Enter') {
                    this.scRemove.emit();
                }
            }, "aria-label": wp.i18n.sprintf(wp.i18n.__('Remove Item - %1$s %2$s', 'surecart'), this.name, this.price), tabIndex: 0 }, h("sc-icon", { key: '8017412201ecd5ecc307269fd0183c2904eb4989', exportparts: "base:remove-icon__base", class: "item__remove", name: "x" }), h("span", { key: '6162f3401bb9ec7d77aecc813daef39e19c40ec9', class: "item__remove-text" }, wp.i18n.__('Remove', 'surecart')))))))));
    }
    get el() { return getElement(this); }
};
ScProductLineItem.style = ScProductLineItemStyle0;

const scProductLineItemNoteCss = ".line-item-note{display:flex;align-items:flex-start;gap:0.25em;min-height:1.5em}.line-item-note--clickable{cursor:pointer}.line-item-note__text{line-height:1.4;flex:1;display:-webkit-box;-webkit-box-orient:vertical;line-clamp:1;-webkit-line-clamp:1;overflow:hidden;text-overflow:ellipsis;transition:all 0.2s}.line-item-note--is-expanded .line-item-note__text{display:block;line-clamp:unset;-webkit-line-clamp:unset;overflow:visible;text-overflow:unset}.line-item-note__toggle{background:none;border:none;color:var(--sc-color-gray-500);cursor:pointer;padding:0;align-self:flex-start;transition:opacity 0.2s ease;border-radius:var(--sc-border-radius-small)}.line-item-note__toggle:hover{opacity:0.8}.line-item-note__toggle:focus-visible{outline:2px solid var(--sc-color-primary-500);outline-offset:2px}.line-item-note__toggle:focus{outline:2px solid var(--sc-color-primary-500);outline-offset:2px}";
const ScProductLineItemNoteStyle0 = scProductLineItemNoteCss;

const ScProductLineItemNote = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.note = undefined;
        this.expanded = false;
        this.isOverflowing = false;
    }
    componentDidLoad() {
        this.setupObservers();
        this.checkOverflow();
    }
    disconnectedCallback() {
        this.cleanupObservers();
    }
    setupObservers() {
        if (!this.noteEl)
            return;
        // ResizeObserver for container size changes
        if (typeof ResizeObserver !== 'undefined') {
            this.resizeObserver = new ResizeObserver(() => {
                this.checkOverflow();
            });
            this.resizeObserver.observe(this.noteEl);
        }
        // MutationObserver for content changes
        if (typeof MutationObserver !== 'undefined') {
            this.mutationObserver = new MutationObserver(() => {
                this.checkOverflow();
            });
            this.mutationObserver.observe(this.noteEl, {
                characterData: true,
                subtree: true,
                childList: true,
            });
        }
    }
    cleanupObservers() {
        if (this.resizeObserver) {
            this.resizeObserver.disconnect();
            this.resizeObserver = undefined;
        }
        if (this.mutationObserver) {
            this.mutationObserver.disconnect();
            this.mutationObserver = undefined;
        }
    }
    checkOverflow() {
        if (!this.noteEl)
            return;
        this.isOverflowing = this.noteEl.scrollHeight > this.noteEl.clientHeight;
    }
    toggle() {
        this.expanded = !this.expanded;
    }
    render() {
        if (!this.note)
            return null;
        return (h("div", { class: "base", part: "base" }, h("div", { class: {
                'line-item-note': true,
                'line-item-note--is-expanded': this.expanded,
                'line-item-note--clickable': this.isOverflowing || this.expanded,
            }, tabIndex: this.isOverflowing || this.expanded ? 0 : undefined, onClick: () => (this.isOverflowing || this.expanded) && this.toggle() }, h("div", { ref: el => (this.noteEl = el), class: "line-item-note__text" }, this.note), (this.isOverflowing || this.expanded) && (h("button", { class: "line-item-note__toggle", type: "button", onClick: e => {
                e.stopPropagation();
                this.toggle();
            }, title: this.expanded ? wp.i18n.__('Collapse note', 'surecart') : wp.i18n.__('Expand note', 'surecart') }, h("slot", { name: "icon" }, h("sc-icon", { name: this.expanded ? 'chevron-up' : 'chevron-down', style: { width: '16px', height: '16px' } })))))));
    }
    get el() { return getElement(this); }
};
ScProductLineItemNote.style = ScProductLineItemNoteStyle0;

const scQuantitySelectCss = ":host{--focus-ring:0 0 0 var(--sc-focus-ring-width) var(--sc-focus-ring-color-primary);--border-radius:var(--sc-quantity-border-radius, var(--sc-input-border-radius-small));display:inline-block}.input__control{text-align:center;line-height:1;border:none;flex:1;max-width:var(--sc-quantity-input-max-width, 35px);background-color:var(--sc-input-control-background-color, var(--sc-color-white));color:var(--sc-input-control-color, var(--sc-color-black));-moz-appearance:textfield}.input__control::-webkit-outer-spin-button,.input__control::-webkit-inner-spin-button{-webkit-appearance:none}.input__control::-webkit-search-decoration,.input__control::-webkit-search-cancel-button,.input__control::-webkit-search-results-button,.input__control::-webkit-search-results-decoration{-webkit-appearance:none}.input__control:-webkit-autofill,.input__control:-webkit-autofill:hover,.input__control:-webkit-autofill:focus,.input__control:-webkit-autofill:active{box-shadow:0 0 0 var(--sc-input-height-large) var(--sc-input-background-color-hover) inset !important;-webkit-text-fill-color:var(--sc-input-color)}.input__control::placeholder{color:var(--sc-input-placeholder-color);user-select:none}.input__control:focus{outline:none}.quantity--trigger{cursor:pointer;white-space:nowrap}.quantity{position:relative;display:inline-block;width:var(--sc-quantity-select-width, 100px);height:var(--sc-quantity-control-height, var(--sc-input-height-small));display:flex;align-items:stretch;font-family:var(--sc-input-font-family);font-weight:var(--sc-input-font-weight);letter-spacing:var(--sc-input-letter-spacing);background-color:var(--sc-input-background-color);border:var(--sc-input-border);border-radius:var(--border-radius);vertical-align:middle;box-shadow:var(--sc-input-box-shadow);transition:var(--sc-input-transition, var(--sc-transition-medium)) color, var(--sc-input-transition, var(--sc-transition-medium)) border, var(--sc-input-transition, var(--sc-transition-medium)) box-shadow}.quantity:hover:not(.quantity--disabled){background-color:var(--sc-input-background-color-hover);border-color:var(--sc-input-border-color-hover)}.quantity:hover:not(.quantity--disabled) .quantity__control{color:var(--sc-input-color-hover)}.quantity.quantity--focused:not(.quantity--disabled){background-color:var(--sc-input-background-color-focus);border-color:var(--sc-input-border-color-focus);box-shadow:var(--focus-ring)}.quantity.quantity--focused:not(.quantity--disabled) .quantity__control{color:var(--sc-input-color-focus)}.quantity.quantity--disabled{background-color:var(--sc-input-background-color-disabled);border-color:var(--sc-input-border-color-disabled);opacity:0.5;cursor:not-allowed}.quantity.quantity--disabled .input__control{color:var(--sc-input-color-disabled)}.quantity.quantity--disabled .input__control::placeholder{color:var(--sc-input-placeholder-color-disabled)}.button__decrease,.button__increase{display:inline-block;text-align:center;vertical-align:middle;line-height:0;height:auto;top:1px;bottom:1px;width:32px;background:var(--sc-input-background-color);color:var(--sc-input-help-text-color);cursor:pointer;font-size:13px;user-select:none;border-width:0;padding:0}.button__decrease:hover:not(.button--disabled) .quantity__control,.button__increase:hover:not(.button--disabled) .quantity__control{color:var(--sc-input-color-hover)}.button__decrease.button--disabled,.button__increase.button--disabled{background-color:var(--sc-input-background-color-disabled);border-color:var(--sc-input-border-color-disabled);opacity:0.5;cursor:not-allowed}.quantity--small{width:var(--sc-quantity-select-width-small, 76px);height:var(--sc-quantity-control-height-small, 26px)}.quantity--small .button__decrease,.quantity--small .button__increase{width:24px;border:none}.quantity--small .input__control{max-width:24px}.button__decrease{left:1px;border-radius:var(--border-radius) 0 0 var(--border-radius);border-right:var(--sc-input-border)}.button__increase{right:1px;border-radius:0 var(--border-radius) var(--border-radius) 0;border-left:var(--sc-input-border)}.quantity--is-rtl .button__decrease{right:1px;border-left:var(--sc-input-border);border-right:0}.quantity--is-rtl .button__increase{left:1px;border-right:var(--sc-input-border);border-left:0}";
const ScQuantitySelectStyle0 = scQuantitySelectCss;

const ScQuantitySelect = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.scChange = createEvent(this, "scChange", 7);
        this.scInput = createEvent(this, "scInput", 7);
        this.scFocus = createEvent(this, "scFocus", 7);
        this.scBlur = createEvent(this, "scBlur", 7);
        this.clickEl = undefined;
        this.disabled = undefined;
        this.max = Infinity;
        this.min = 1;
        this.quantity = 0;
        this.size = 'medium';
        this.hasFocus = undefined;
    }
    decrease() {
        if (this.disabled)
            return;
        this.quantity = Math.max(this.quantity - 1, this.min);
        this.scChange.emit(this.quantity);
        this.scInput.emit(this.quantity);
    }
    increase() {
        if (this.disabled)
            return;
        this.quantity = Math.min(this.quantity + 1, this.max);
        this.scChange.emit(this.quantity);
        this.scInput.emit(this.quantity);
    }
    handleBlur() {
        this.hasFocus = false;
        this.scBlur.emit();
    }
    handleFocus() {
        this.hasFocus = true;
        this.scFocus.emit();
    }
    handleChange() {
        this.quantity = parseInt(this.input.value) > this.max ? this.max : parseInt(this.input.value);
        this.scChange.emit(this.quantity);
    }
    handleInput() {
        this.quantity = parseInt(this.input.value);
        this.scInput.emit(this.quantity);
    }
    render() {
        return (h("div", { key: '4e754aec3280773a31926b14c3c09b79704b648e', part: "base", class: {
                'quantity': true,
                // States
                'quantity--focused': this.hasFocus,
                'quantity--disabled': this.disabled,
                'quantity--is-rtl': isRtl(),
                'quantity--small': this.size === 'small',
            } }, h("button", { key: '8f208ff69fa1a4e8855fb86c179e9cb11d47ba0e', part: "minus", "aria-label": wp.i18n.__('Decrease quantity by one.', 'surecart'), "aria-disabled": this.disabled || (this.quantity <= this.min && this.min > 1), class: { 'button__decrease': true, 'button--disabled': this.quantity <= this.min && this.min > 1 }, onClick: () => this.quantity > this.min && this.decrease(), disabled: this.disabled || (this.quantity <= this.min && this.min > 1) }, h("sc-icon", { key: '31debc92bac2af36f198ba72a633550a4e150b8c', name: "minus", exportparts: "base:minus__icon" })), h("input", { key: '3b6e1888af6714b9248cc7cfa02c3fb646de8e69', part: "input", class: "input__control", ref: el => (this.input = el), step: "1", type: "number", max: this.max, min: this.min, value: this.quantity, disabled: this.disabled, autocomplete: "off", role: "spinbutton", "aria-valuemax": this.max, "aria-valuemin": this.min, "aria-valuenow": this.quantity, "aria-disabled": this.disabled, onChange: () => this.handleChange(), onInput: () => this.handleInput(), onFocus: () => this.handleFocus(), onBlur: () => this.handleBlur() }), h("button", { key: '7d3b145dafbd1efd8d153364bcb88e7742667fc7', part: "plus", "aria-label": wp.i18n.__('Increase quantity by one.', 'surecart'), class: { 'button__increase': true, 'button--disabled': this.quantity >= this.max }, onClick: () => this.quantity < this.max && this.increase(), "aria-disabled": this.disabled || this.quantity >= this.max, disabled: this.disabled || this.quantity >= this.max }, h("sc-icon", { key: '16efc991d1cee8776959a7cf99461b307aad147a', name: "plus", exportparts: "base:plus__icon" }))));
    }
    get el() { return getElement(this); }
};
ScQuantitySelect.style = ScQuantitySelectStyle0;

export { ScIcon as sc_icon, ScProductLineItem as sc_product_line_item, ScProductLineItemNote as sc_product_line_item_note, ScQuantitySelect as sc_quantity_select };

//# sourceMappingURL=sc-icon_4.entry.js.map