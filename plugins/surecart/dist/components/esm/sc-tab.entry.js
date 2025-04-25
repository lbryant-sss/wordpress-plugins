import { r as registerInstance, c as createEvent, h, a as getElement } from './index-745b6bec.js';
import { i as isRtl } from './page-align-0cdacf32.js';

const scTabCss = ":host{display:block}.tab{font-family:var(--sc-font-sans);color:var(--sc-color-gray-600);display:flex;align-items:center;justify-content:flex-start;line-height:1;padding:var(--sc-spacing-small) var(--sc-spacing-small);font-size:var(--sc-font-size-medium);font-weight:var(--sc-font-weight-semibold);border-radius:var(--sc-border-radius-small);cursor:pointer;transition:color 0.35s ease, background-color 0.35s ease;user-select:none;text-decoration:none}.tab.tab--active,.tab:hover{color:var(--sc-tab-active-color, var(--sc-color-gray-900));background-color:var(--sc-tab-active-background, var(--sc-color-gray-100))}.tab.tab--disabled{cursor:not-allowed;color:var(--sc-color-gray-400)}.tab__content{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;line-height:var(--sc-line-height-dense)}.tab__prefix,.tab__suffix{flex:0 0 auto;display:flex;align-items:center}.tab__suffix{margin-left:auto}.tab__counter{background:var(--sc-color-gray-200);display:inline-block;padding:var(--sc-spacing-xx-small) var(--sc-spacing-small);border-radius:var(--sc-border-radius-pill);font-size:var(--sc-font-size-small);text-align:center;line-height:1;transition:color 0.35s ease, background-color 0.35s ease}.tab.tab--active .tab__counter,.tab:hover .tab__counter{background:var(--sc-color-white)}.tab--has-prefix{padding-left:var(--sc-spacing-small)}.tab--has-prefix .tab__content{padding-left:var(--sc-spacing-small)}.tab--has-suffix{padding-right:var(--sc-spacing-small)}.tab--has-suffix .tab__label{padding-right:var(--sc-spacing-small)}.tab--is-rtl.tab--has-prefix .tab__content{padding-left:0;padding-right:var(--sc-spacing-small)}";
const ScTabStyle0 = scTabCss;

let id = 0;
const ScTab = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.scClose = createEvent(this, "scClose", 7);
        this.componentId = `tab-${++id}`;
        this.panel = '';
        this.href = undefined;
        this.active = false;
        this.disabled = false;
        this.count = undefined;
        this.hasPrefix = false;
        this.hasSuffix = false;
    }
    /** Sets focus to the tab. */
    async triggerFocus(options) {
        this.tab.focus(options);
    }
    /** Removes focus from the tab. */
    async triggerBlur() {
        this.tab.blur();
    }
    handleSlotChange() {
        this.hasPrefix = !!this.el.querySelector('[slot="prefix"]');
        this.hasSuffix = !!this.el.querySelector('[slot="suffix"]');
    }
    render() {
        // If the user didn't provide an ID, we'll set one so we can link tabs and tab panels with aria labels
        this.el.id = this.el.id || this.componentId;
        const Tag = this.href ? 'a' : 'div';
        return (h(Tag, { key: '9785c34bdd33cb48183cd008c3243b1dbb9f5eb1', part: `base ${this.active ? `active` : ``}`, href: this.href, class: {
                'tab': true,
                'tab--active': this.active,
                'tab--disabled': this.disabled,
                'tab--has-prefix': this.hasPrefix,
                'tab--has-suffix': this.hasSuffix,
                'tab--is-rtl': isRtl(),
            }, ref: el => (this.tab = el), role: "tab", "aria-disabled": this.disabled ? 'true' : 'false', "aria-selected": this.active ? 'true' : 'false', tabindex: this.disabled ? '-1' : '0' }, h("span", { key: '6b1ac4991650b08ea34879bdffdc5a0cca7bc0b2', part: "prefix", class: "tab__prefix" }, h("slot", { key: '3cc6cc285315e65c8289575294437c94d1ff3f2a', onSlotchange: () => this.handleSlotChange(), name: "prefix" })), h("div", { key: '48cc51f066621ccb23d1a5cd827d87cd703c1c71', class: "tab__content", part: "content" }, h("slot", { key: '50bdf98e910e1fe29a25dfa6643bf62b187af66c' })), h("span", { key: '370d98f09ec4cd5bd2e258a064d034b1075f76cd', part: "suffix", class: "tab__suffix" }, h("slot", { key: '103b1e7df685a7e7bcd3ba52a6793709ed05cd2f', onSlotchange: () => this.handleSlotChange(), name: "suffix" })), h("slot", { key: 'c2544c913b7c4577c35820035f630efce29f6e8a', name: "suffix" }, !!this.count && (h("div", { key: '540e8d21ce31df8a2be674d5d6521fe01c7c0378', class: "tab__counter", part: "counter" }, this.count)))));
    }
    get el() { return getElement(this); }
};
ScTab.style = ScTabStyle0;

export { ScTab as sc_tab };

//# sourceMappingURL=sc-tab.entry.js.map