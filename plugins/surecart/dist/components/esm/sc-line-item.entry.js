import { r as registerInstance, h, a as getElement } from './index-745b6bec.js';
import { i as isRtl } from './page-align-0cdacf32.js';

const scLineItemCss = ":host{display:block;--mobile-size:380px;--price-size:var(--sc-font-size-medium);--line-item-grid-template-columns:auto 1fr 1fr;line-height:var(--sc-line-height-dense)}.item{display:grid;align-items:center;grid-template-columns:var(--line-item-grid-template-columns)}@media screen and (min-width: var(--mobile-size)){.item{flex-wrap:no-wrap}}.item__title{color:var(--sc-line-item-title-color)}.item__price{color:var(--sc-input-label-color)}.item__title,.item__price{font-size:var(--sc-font-size-medium);font-weight:var(--sc-font-weight-semibold)}.item__description,.item__price-description{font-size:var(--sc-font-size-small);line-height:var(--sc-line-height-dense);color:var(--sc-input-label-color)}::slotted([slot=price-description]){margin-top:var(--sc-line-item-text-margin, 5px);color:var(--sc-input-label-color);text-decoration:none}.item__end{flex:1;display:flex;align-items:center;justify-content:flex-end;flex-wrap:wrap;align-self:flex-end;width:100%;margin-top:20px}@media screen and (min-width: 280px){.item__end{width:auto;text-align:right;margin-left:20px;margin-top:0}.item--is-rtl .item__end{margin-left:0;margin-right:20px}.item__price-text{text-align:right;display:flex;flex-direction:column;align-items:flex-end}}.item__price-currency{font-size:var(--sc-font-size-small);color:var(--sc-input-label-color);text-transform:var(--sc-currency-transform, uppercase);margin-right:8px}.item__text{flex:1}.item__price-description{display:-webkit-box}::slotted([slot=image]){margin-right:20px;width:50px;height:50px;object-fit:cover;border-radius:4px;border:1px solid var(--sc-color-gray-200);display:block;box-shadow:var(--sc-input-box-shadow)}::slotted([slot=price-description]){display:inline-block;width:100%;line-height:1}.item__price-layout{font-size:var(--sc-font-size-x-large);font-weight:var(--sc-font-weight-semibold);display:flex;align-items:center}.item__price{font-size:var(--price-size)}.item_currency{font-weight:var(--sc-font-weight-normal);font-size:var(--sc-font-size-xx-small);color:var(--sc-input-label-color);margin-right:var(--sc-spacing-small);text-transform:var(--sc-currency-text-transform, uppercase)}.item--is-rtl.item__description,.item--is-rtl.item__price-description{text-align:right}.item--is-rtl .item__text{text-align:right}@media screen and (min-width: 280px){.item--is-rtl .item__end{width:auto;text-align:left;margin-left:0;margin-top:0}.item--is-rtl .item__price-text{text-align:left}}";
const ScLineItemStyle0 = scLineItemCss;

const ScLineItem = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.price = undefined;
        this.currency = undefined;
        this.hasImageSlot = undefined;
        this.hasTitleSlot = undefined;
        this.hasDescriptionSlot = undefined;
        this.hasPriceSlot = undefined;
        this.hasPriceDescriptionSlot = undefined;
        this.hasCurrencySlot = undefined;
    }
    componentWillLoad() {
        this.hasImageSlot = !!this.hostElement.querySelector('[slot="image"]');
        this.hasTitleSlot = !!this.hostElement.querySelector('[slot="title"]');
        this.hasDescriptionSlot = !!this.hostElement.querySelector('[slot="description"]');
        this.hasPriceSlot = !!this.hostElement.querySelector('[slot="price"]');
        this.hasPriceDescriptionSlot = !!this.hostElement.querySelector('[slot="price-description"]');
        this.hasCurrencySlot = !!this.hostElement.querySelector('[slot="currency"]');
    }
    render() {
        return (h("div", { key: 'eb779fdabfb8e7b574c3498f18bf61da0fccd15c', part: "base", class: {
                'item': true,
                'item--has-image': this.hasImageSlot,
                'item--has-title': this.hasTitleSlot,
                'item--has-description': this.hasDescriptionSlot,
                'item--has-price': this.hasPriceSlot,
                'item--has-price-description': this.hasPriceDescriptionSlot,
                'item--has-price-currency': this.hasCurrencySlot,
                'item--is-rtl': isRtl(),
            } }, h("div", { key: '18fc5cb86fc42bd327b84f5d8e4fe5d72c7332ee', class: "item__image", part: "image" }, h("slot", { key: '75ea227a9f3346d15b81863bca5adec355a77240', name: "image" })), h("div", { key: '224b09648ec562e551d3f631d2082425b796bf97', class: "item__text", part: "text" }, h("div", { key: 'dc73bfa9f8606ba547c3ea213763414f379428e7', class: "item__title", part: "title" }, h("slot", { key: 'd457093d784e3db2a4b52cd96080483278a7211b', name: "title" })), h("div", { key: '5389fdc871db15411ece3abd75291178bfa093ac', class: "item__description", part: "description" }, h("slot", { key: '9db9c5a0de16257e9870cd49e7d363b0ac8bc475', name: "description" }))), h("div", { key: 'dc268af361360718f637b4d4ba65e42d75a2b7c6', class: "item__end", part: "price" }, h("div", { key: '526361c6956f56458395d99d6415db1cbbfd10da', class: "item__price-currency", part: "currency" }, h("slot", { key: '405e5ae1d0b0c18d8aac14761fe9ddb4b8dde115', name: "currency" })), h("div", { key: 'aeb4a8bf5e6db065f17c68be9e8cb38e0fa6ec42', class: "item__price-text", part: "price-text" }, h("div", { key: '1dbd5313812dfe8e1abb389b74799676609c53f3', class: "item__price", part: "price" }, h("slot", { key: '9aafba491c1dac375d47c116f663d7006695c45e', name: "price" })), h("div", { key: 'ecc3b906c22c6091e77b462a580cf08f80e070e6', class: "item__price-description", part: "price-description" }, h("slot", { key: '876483735ba8d990f84ce51c7f59abcbe7f1a9d9', name: "price-description" }))))));
    }
    get hostElement() { return getElement(this); }
};
ScLineItem.style = ScLineItemStyle0;

export { ScLineItem as sc_line_item };

//# sourceMappingURL=sc-line-item.entry.js.map