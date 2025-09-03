'use strict';

Object.defineProperty(exports, '__esModule', { value: true });

const index = require('./index-8acc3c89.js');
const animationRegistry = require('./animation-registry-f7f1a08b.js');
const pageAlign = require('./page-align-5a2ab493.js');
const index$1 = require('./index-fb76df07.js');

const scToggleCss = ":host{display:block;font-family:var(--sc-font-sans);--sc-toggle-padding:var(--sc-spacing-medium)}::slotted([slot=summary]){display:flex;align-items:center;flex-direction:flex-start;gap:var(--sc-spacing-x-small)}.details{border-radius:var(--sc-border-radius-medium);background-color:var(--sc-toggle-background-color, var(--sc-color-white));overflow-anchor:none}.details__radio{flex:0 0 auto;position:relative;display:inline-flex;align-items:center;justify-content:center;background-color:var(--sc-input-background-color);color:transparent;border-radius:50%;border:solid var(--sc-toggle-border-width, var(--sc-input-border-width)) var(--sc-toggle-border-color, var(--sc-input-border-color));background-color:var(--sc-input-background-color);display:inline-flex;color:transparent;width:var(--sc-toggle-radio-size, var(--sc-radio-size));height:var(--sc-toggle-radio-size, var(--sc-radio-size));transition:var(--sc-input-transition, var(--sc-transition-medium)) border-color, var(--sc-input-transition, var(--sc-transition-medium)) background-color, var(--sc-input-transition, var(--sc-transition-medium)) color, var(--sc-input-transition, var(--sc-transition-medium)) box-shadow}.details__radio svg{width:100%;height:100%}.details--open .details__radio{color:var(--sc-color-white);border-color:var(--sc-color-primary-500);background-color:var(--sc-color-primary-500)}.details:not(.details--borderless){border:solid 1px var(--sc-toggle-border-color, var(--sc-color-gray-200))}.details--disabled{opacity:0.5}.details__header{display:flex;align-items:center;border-radius:inherit;padding:var(--sc-toggle-header-padding, var(--sc-toggle-padding));user-select:none;cursor:pointer;color:var(--sc-toggle-header-color, var(--sc-input-label-color));gap:0.75em}.details__header:focus{box-shadow:var(--sc-focus-ring)}.details__header:focus-visible{box-shadow:var(--sc-focus-ring)}.details--disabled .details__header{cursor:not-allowed}.details--disabled .details__header:focus-visible{outline:none;box-shadow:none}.details__summary{flex:1 1 auto;display:flex;align-items:center}.details__summary-icon{flex:0 0 auto;display:flex;align-items:center;transition:var(--sc-transition-medium) transform ease}.details--open .details__summary-icon{transform:rotate(90deg)}.details__content{padding:var(--sc-toggle-content-padding, var(--sc-toggle-padding));padding-top:calc(var(--sc-toggle-content-padding, var(--sc-toggle-padding)) / 4)}.details--shady .details__body{border-top:solid var(--sc-input-border-width) var(--sc-input-border-color);background:var(--sc-toggle-shady-color, var(--sc-color-gray-50))}.details--shady .details__content{padding-top:var(--sc-toggle-content-padding, var(--sc-toggle-padding))}";
const ScToggleStyle0 = scToggleCss;

const ScToggle = class {
    constructor(hostRef) {
        index.registerInstance(this, hostRef);
        this.scShow = index.createEvent(this, "scShow", 7);
        this.scHide = index.createEvent(this, "scHide", 7);
        this.open = false;
        this.summary = undefined;
        this.disabled = false;
        this.borderless = false;
        this.shady = false;
        this.showControl = false;
        this.showIcon = true;
        this.collapsible = true;
    }
    componentDidLoad() {
        this.body.hidden = !this.open;
        this.body.style.height = this.open ? 'auto' : '0';
    }
    /** Shows the details. */
    async show() {
        if (this.open || this.disabled) {
            return undefined;
        }
        this.open = true;
        index$1.speak(wp.i18n.__('Summary Shown', 'surecart'));
    }
    /** Hides the details */
    async hide() {
        if (!this.open || this.disabled || !this.collapsible) {
            return undefined;
        }
        this.open = false;
        index$1.speak(wp.i18n.__('Summary Hidden', 'surecart'));
    }
    handleSummaryClick() {
        if (!this.disabled) {
            if (this.open) {
                this.hide();
            }
            else {
                this.show();
            }
            this.header.focus();
        }
    }
    handleSummaryKeyDown(event) {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            if (this.open) {
                this.hide();
            }
            else {
                this.show();
            }
        }
        if (event.key === 'ArrowUp' || event.key === 'ArrowLeft') {
            event.preventDefault();
            this.hide();
        }
        if (event.key === 'ArrowDown' || event.key === 'ArrowRight') {
            event.preventDefault();
            this.show();
        }
    }
    async handleOpenChange() {
        if (this.open) {
            this.scShow.emit();
            await animationRegistry.stopAnimations(this.body);
            this.body.hidden = false;
            this.body.style.overflow = 'hidden';
            const { keyframes, options } = animationRegistry.getAnimation(this.el, 'details.show');
            await animationRegistry.animateTo(this.body, animationRegistry.shimKeyframesHeightAuto(keyframes, this.body.scrollHeight), options);
            this.body.style.height = 'auto';
            this.body.style.overflow = 'visible';
        }
        else {
            this.scHide.emit();
            await animationRegistry.stopAnimations(this.body);
            this.body.style.overflow = 'hidden';
            const { keyframes, options } = animationRegistry.getAnimation(this.el, 'details.hide');
            await animationRegistry.animateTo(this.body, animationRegistry.shimKeyframesHeightAuto(keyframes, this.body.scrollHeight), options);
            this.body.hidden = true;
            this.body.style.height = 'auto';
            this.body.style.overflow = 'visible';
        }
    }
    render() {
        return (index.h("div", { key: '4f7e60e13082b6bc381ecb488de64902813fe88a', part: "base", class: {
                'details': true,
                'details--open': this.open,
                'details--disabled': this.disabled,
                'details--borderless': this.borderless,
                'details--shady': this.shady,
                'details--is-rtl': pageAlign.isRtl(),
            } }, index.h("header", { key: 'dc48114172a3cac02c86eaebb3956728ed72bd10', ref: el => (this.header = el), part: "header", id: "header", class: "details__header", role: "button", "aria-expanded": this.open ? 'true' : 'false', "aria-controls": "content", "aria-disabled": this.disabled ? 'true' : 'false', tabindex: this.disabled ? '-1' : '0', onClick: () => this.handleSummaryClick(), onKeyDown: e => this.handleSummaryKeyDown(e) }, this.showControl && (index.h("span", { key: 'b2ea60e40c832d913b48daa49aa0a7d45143d268', part: "radio", class: "details__radio" }, index.h("svg", { key: 'b4d2f6a27c269402ad34a4bd457823ec466c4b76', viewBox: "0 0 16 16" }, index.h("g", { key: 'ca54f1a897447a44673acf5ec2798d9e623ff99b', stroke: "none", "stroke-width": "1", fill: "none", "fill-rule": "evenodd" }, index.h("g", { key: 'fde41771e1fa16c542dc4d9896a6314fd1689962', fill: "currentColor" }, index.h("circle", { key: 'fba9474f2a4bd98a7529397c3f2ed663915c2550', cx: "8", cy: "8", r: "3.42857143" })))))), index.h("div", { key: '61bb920a0db6c3318aac00d64286ac3a1a8ed415', part: "summary", class: "details__summary" }, index.h("slot", { key: '2d30b79bea39f49db200dce124d791d3dfe05e4c', name: "summary" }, this.summary)), this.showIcon && (index.h("span", { key: '5c329e78aa42b86690661aa9c9ec428a9fcc81ec', part: "summary-icon", class: "details__summary-icon" }, index.h("slot", { key: 'bc0cb13335a2d39fcc80c192d6fe78fe7ac305eb', name: "icon" }, index.h("sc-icon", { key: '7a2fff29d5f344d11f4993e4d2ba45edff6c80c9', name: "chevron-right" }))))), index.h("div", { key: '35544000f0eca7aa7ac62d7e5f3c121284a0aa2c', class: "details__body", ref: el => (this.body = el), part: "body" }, index.h("div", { key: '86673fe7e01eb8a375f00eebc269d0be912b3e7e', part: "content", id: "content", class: "details__content", role: "region", "aria-labelledby": "header" }, index.h("slot", { key: '5cda0c1279b610b7777ab928eb36abc255d68fcb' })))));
    }
    get el() { return index.getElement(this); }
    static get watchers() { return {
        "open": ["handleOpenChange"]
    }; }
};
animationRegistry.setDefaultAnimation('details.show', {
    keyframes: [
        { height: '0', opacity: '0' },
        { height: 'auto', opacity: '1' },
    ],
    options: { duration: 250, easing: 'ease' },
});
animationRegistry.setDefaultAnimation('details.hide', {
    keyframes: [
        { height: 'auto', opacity: '1' },
        { height: '0', opacity: '0' },
    ],
    options: { duration: 250, easing: 'ease' },
});
ScToggle.style = ScToggleStyle0;

exports.sc_toggle = ScToggle;

//# sourceMappingURL=sc-toggle.cjs.entry.js.map