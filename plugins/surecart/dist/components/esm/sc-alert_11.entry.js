import { r as registerInstance, c as createEvent, h, H as Host, a as getElement, F as Fragment } from './index-745b6bec.js';
import { i as isRtl } from './page-align-0cdacf32.js';
import { s as setDefaultAnimation, g as getAnimation, a as animateTo, b as stopAnimations } from './animation-registry-12eed2e3.js';
import { g as getIconLibrary } from './library-e110eea6.js';
import { a as apiFetch } from './fetch-8ecbbe53.js';
import { a as addQueryArgs } from './add-query-args-0e2a8393.js';
import './remove-query-args-938c53ea.js';

const scAlertCss = ":host{display:block}[hidden]{display:none !important}::slotted(*:not(:first-child)){margin-top:0.5rem;margin-bottom:0}::slotted(ul){line-height:1.4em;list-style-type:disc;margin:0;padding:0;padding-left:20px}.alert{font-family:var(--sc-input-font-family);font-weight:var(--sc-font-weight-normal);font-size:var(--sc-button-font-size-medium);line-height:var(--sc-line-height-dense);border-radius:var(--sc-alert-border-radius, var(--sc-border-radius-medium));padding:var(--sc-spacing-large);margin-bottom:var(--sc-spacing-large);display:flex;align-items:flex-start;border:var(--sc-alert-border, var(--sc-input-border));border-top:solid var(--sc-alert-border-width, 3px);color:var(--sc-alert-color, var(--sc-input-label-color));background:var(--sc-alert-background-color, var(--sc-color-white));box-shadow:var(--sc-shadow-small)}.alert__text{flex:1}.alert.alert--primary{border-top-color:var(--sc-alert-primary-border-color, var(--sc-color-primary-500))}.alert.alert--primary a{color:var(--sc-color-primary-900)}.alert.alert--primary .alert__title{color:var(--sc-alert-title-color, var(--sc-color-gray-800))}.alert.alert--primary .alert__icon{color:var(--sc-alert-primary-icon-color, var(--sc-color-primary-500))}.alert.alert--info{border-top-color:var(--sc-alert-info-border-color, var(--sc-color-info-500))}.alert.alert--info a{color:var(--sc-color-info-900)}.alert.alert--info .alert__title{color:var(--sc-alert-title-color, var(--sc-color-gray-800))}.alert.alert--info .alert__icon{color:var(--sc-alert-info-icon-color, var(--sc-color-info-500))}.alert.alert--danger{border-top-color:var(--sc-alert-danger-border-color, var(--sc-color-danger-500))}.alert.alert--danger a{color:var(--sc-color-danger-900)}.alert.alert--danger .alert__title{color:var(--sc-alert-title-color, var(--sc-color-gray-800))}.alert.alert--danger .alert__icon{color:var(--sc-alert-danger-icon-color, var(--sc-color-danger-500))}.alert.alert--warning{border-top-color:var(--sc-alert-warning-border-color, var(--sc-color-warning-500))}.alert.alert--warning a{color:var(--sc-color-warning-900)}.alert.alert--warning .alert__title{color:var(--sc-alert-title-color, var(--sc-color-gray-800))}.alert.alert--warning .alert__icon{color:var(--sc-alert-warning-icon-color, var(--sc-color-warning-500))}.alert.alert--success{border-top-color:var(--sc-alert-success-border-color, var(--sc-color-success-500))}.alert.alert--success a{color:var(--sc-color-success-900)}.alert.alert--success .alert__title{color:var(--sc-alert-title-color, var(--sc-color-gray-800))}.alert.alert--success .alert__icon{color:var(--sc-alert-success-icon-color, var(--sc-color-success-500))}.alert__icon{flex:1;flex:0 0 auto;display:flex;align-items:center;font-size:var(--sc-font-size-large);padding-inline-end:var(--sc-spacing-medium)}.alert__title{font-weight:var(--sc-font-weight-semibold)}.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0, 0, 0, 0);border:0}.alert__close{transition:background-color var(--sc-transition-fast) ease;display:inline-flex;border-radius:var(--sc-border-radius-small);padding:var(--sc-spacing-x-small);margin-left:auto;cursor:pointer}.alert__close svg{width:1em;height:1em}.alert--is-rtl{text-align:right}.alert--is-rtl.alert-close{margin-right:auto;margin-left:unset}.alert--is-rtl ::slotted(ul){margin:0;padding:0;padding-right:20px}";
const ScAlertStyle0 = scAlertCss;

const ScAlert = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.scHide = createEvent(this, "scHide", 7);
        this.scShow = createEvent(this, "scShow", 7);
        this.open = false;
        this.title = undefined;
        this.closable = false;
        this.type = 'primary';
        this.duration = Infinity;
        this.scrollOnOpen = undefined;
        this.scrollMargin = '0px';
        this.noIcon = undefined;
        this.autoHideTimeout = undefined;
    }
    /** Shows the alert. */
    async show() {
        if (this.open) {
            return;
        }
        this.open = true;
    }
    /** Hides the alert */
    async hide() {
        if (!this.open) {
            return;
        }
        this.open = false;
    }
    restartAutoHide() {
        clearTimeout(this.autoHideTimeout);
        if (this.open && this.duration < Infinity) {
            this.autoHideTimeout = setTimeout(() => this.hide(), this.duration);
        }
    }
    handleMouseMove() {
        this.restartAutoHide();
    }
    handleCloseClick() {
        this.hide();
    }
    /** Emit event when showing or hiding changes */
    handleOpenChange() {
        this.open ? this.scShow.emit() : this.scHide.emit();
        if (this.open && this.scrollOnOpen) {
            this.el.scrollIntoView({ behavior: 'smooth' });
        }
    }
    componentDidLoad() {
        this.handleOpenChange();
    }
    iconName() {
        switch (this.type) {
            case 'danger':
                return 'alert-circle';
            case 'success':
                return 'check-circle';
            case 'warning':
                return 'alert-triangle';
            default:
                return 'info';
        }
    }
    icon() {
        return h("sc-icon", { name: this.iconName() });
    }
    render() {
        return (h(Host, { key: '09f038b869c46bb2a469d707488252f854190590', style: { 'scroll-margin-top': this.scrollMargin } }, h("div", { key: '88a711dfdc7b39aa0e4238dc47251c453f0ba02b', class: {
                'alert': true,
                'alert--primary': this.type === 'primary',
                'alert--success': this.type === 'success',
                'alert--info': this.type === 'info',
                'alert--warning': this.type === 'warning',
                'alert--danger': this.type === 'danger',
                'alert--is-rtl': isRtl()
            }, part: "base", role: "alert", "aria-live": "assertive", "aria-atomic": "true", "aria-hidden": this.open ? 'false' : 'true', hidden: this.open ? false : true, onMouseMove: () => this.handleMouseMove() }, h("div", { key: 'c21265104fee4edcd348738b77b46bb70a485f5d', class: "alert__icon", part: "icon" }, h("slot", { key: '3d62bb971e893c65526c7adce67a09508b2ed377', name: "icon" }, this.icon())), h("div", { key: 'a5cbb772ac33e726ee88fd7e329b3f524975a8d3', class: "alert__text", part: "text" }, h("div", { key: 'c737729ab271b6dc6f615378d05b9992f46e3529', class: "alert__title", part: "title" }, h("slot", { key: 'd941d1ddb86031f5d5162ce0c5528899911bb418', name: "title" }, this.title)), h("div", { key: 'af69867824717107976a3435fe1c53c27a07b7d8', class: "alert__message", part: "message" }, h("slot", { key: 'a26ad3def26671c72aff76c0cdec1d6cf8c82080' }))), this.closable && (h("span", { key: '5dd48cb13843ad29e028771b18f00a28883b38ca', part: "close", class: "alert__close", onClick: () => this.handleCloseClick() }, h("span", { key: '7a11566917da7243f87ebe41fb5ae43c6b2614d8', class: "sr-only" }, "Dismiss"), h("svg", { key: '958f27bd6907865e4725fe3645f5e63c479cd3ab', class: "h-5 w-5", "x-description": "Heroicon name: solid/x", xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 20 20", fill: "currentColor", "aria-hidden": "true" }, h("path", { key: '8098bc65a08397682f66032aa85e6460277e924f', "fill-rule": "evenodd", d: "M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z", "clip-rule": "evenodd" })))))));
    }
    get el() { return getElement(this); }
    static get watchers() { return {
        "open": ["handleOpenChange"]
    }; }
};
ScAlert.style = ScAlertStyle0;

const scBlockUiCss = ":host{display:block;position:var(--sc-block-ui-position, absolute);top:-5px;left:-5px;right:-5px;bottom:-5px;overflow:hidden;display:flex;align-items:center;justify-content:center}:host>*{z-index:1}:host:after{content:\"\";position:var(--sc-block-ui-position, absolute);top:0;left:0;right:0;bottom:0;cursor:var(--sc-block-ui-cursor, wait);background:var(--sc-block-ui-background-color, var(--sc-color-white));opacity:var(--sc-block-ui-opacity, 0.15)}:host.transparent:after{background:transparent}.overlay__content{font-size:var(--sc-font-size-large);font-weight:var(--sc-font-weight-semibold);display:grid;gap:0.5em;text-align:center}";
const ScBlockUiStyle0 = scBlockUiCss;

const ScBlockUi = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.zIndex = 1;
        this.transparent = undefined;
        this.spinner = undefined;
    }
    render() {
        return (h("div", { key: '9681c9104c4d01ee05c0151c9574f0a33b88b0e7', part: "base", class: { overlay: true, transparent: this.transparent }, style: { 'z-index': this.zIndex.toString() } }, h("div", { key: '7232fa02fb8767911454188287a19cbfc0d82af5', class: "overlay__content", part: "content" }, h("slot", { key: '2aab74d4d58c9a7b4401576ec4ebf5ab6bdd3740', name: "spinner" }, !this.transparent && this.spinner && h("sc-spinner", { key: 'a47feab5434e70c0e93ed35d390d8ac99826a5a7' })), h("slot", { key: 'd0d84b1d1fb5295c0fbab68a202e338b40dfac3d' }))));
    }
};
ScBlockUi.style = ScBlockUiStyle0;

const scButtonCss = ":host{display:inline-block;width:auto;cursor:pointer;--primary-color:var(--sc-color-primary-text);--primary-background:var(--sc-color-primary-500)}:host([full]){display:block}::slotted(*){pointer-events:none}.button{box-sizing:border-box;z-index:10;display:inline-flex;align-items:stretch;justify-content:center;width:100%;border-style:solid;border-width:var(--sc-input-border-width);font-family:var(--sc-input-font-family);font-weight:var(--sc-font-weight-semibold);text-decoration:none;user-select:none;white-space:nowrap;vertical-align:middle;padding:0;transition:var(--sc-input-transition, var(--sc-transition-medium)) background-color, var(--sc-input-transition, var(--sc-transition-medium)) color, var(--sc-input-transition, var(--sc-transition-medium)) border, var(--sc-input-transition, var(--sc-transition-medium)) box-shadow, var(--sc-input-transition, var(--sc-transition-medium)) opacity;cursor:inherit}.button::-moz-focus-inner{border:0}.button:focus{outline:none}.button:focus-visible{box-shadow:0 0 0 var(--sc-focus-ring-width) var(--sc-focus-ring-color-primary)}.button.button--disabled{cursor:not-allowed}.button.button--disabled *{pointer-events:none}.button.button--disabled .button__label,.button.button--disabled .button__suffix,.button.button--disabled .button__prefix{opacity:0.5}.button ::slotted(.sc--icon){pointer-events:none}.button__prefix,.button__suffix{flex:0 0 auto;display:flex;align-items:center}.button__label{display:flex;align-items:center}.button__label ::slotted(sc-icon){vertical-align:-2px}.button:not(.button--text):not(.button--link){box-shadow:var(--sc-shadow-small)}.button.button--standard.button--default{background-color:var(--sc-button-default-background-color, var(--sc-color-white));border-color:var(--sc-button-default-border-color, var(--sc-color-gray-300));color:var(--sc-button-default-color, var(--sc-color-gray-600))}.button.button--standard.button--default:hover:not(.button--disabled){background-color:var(--sc-button-default-hover-background-color, var(--sc-color-white));border-color:var(--sc-button-default-focus-border-color, var(--primary-background));color:var(--primary-background)}.button.button--standard.button--default:focus:not(.button--disabled){background-color:var(--sc-button-default-focus-background-color, var(--sc-color-white));border-color:var(--sc-button-default-focus-border-color, var(--sc-color-white));color:var(--primary-background);box-shadow:0 0 0 var(--sc-focus-ring-width) var(--sc-focus-ring-color-primary)}.button.button--standard.button--default:active:not(.button--disabled){background-color:var(--sc-button-default-active-background-color, var(--sc-color-white));border-color:var(--sc-button-default-active-border-color, var(--sc-color-white));color:var(--primary-background)}.button.button--standard.button--primary{background-color:var(--primary-background);border-color:var(--primary-background);color:var(--primary-color)}.button.button--standard.button--primary:hover:not(.button--disabled){opacity:0.8}.button.button--standard.button--primary:focus:not(.button--disabled){opacity:0.8;color:var(--primary-color);border-color:var(--sc-color-white);box-shadow:0 0 0 var(--sc-focus-ring-width) var(--sc-focus-ring-color-primary)}.button.button--standard.button--primary:active:not(.button--disabled){background-color:var(--primary-background);border-color:var(--sc-color-white);color:var(--primary-color)}.button.button--standard.button--success{background-color:var(--sc-color-success-500);border-color:var(--sc-color-success-500);color:var(--sc-color-success-text)}.button.button--standard.button--success:hover:not(.button--disabled){background-color:var(--sc-color-success-400);border-color:var(--sc-color-success-400);color:var(--sc-color-success-text)}.button.button--standard.button--success:focus:not(.button--disabled){background-color:var(--sc-color-success-400);border-color:var(--sc-color-success-400);color:var(--sc-color-success-text);box-shadow:0 0 0 var(--sc-focus-ring-width) var(--sc-focus-ring-color-success)}.button.button--standard.button--success:active:not(.button--disabled){background-color:var(--sc-color-success-500);border-color:var(--sc-color-success-500);color:var(--sc-color-success-text)}.button.button--standard.button--info{background-color:var(--sc-color-info-500);border-color:var(--sc-color-info-500);color:var(--sc-color-info-text)}.button.button--standard.button--info:hover:not(.button--disabled){background-color:var(--sc-color-info-400);border-color:var(--sc-color-info-400);color:var(--sc-color-info-text)}.button.button--standard.button--info:focus:not(.button--disabled){background-color:var(--sc-color-info-400);border-color:var(--sc-color-info-400);color:var(--sc-color-info-text);box-shadow:0 0 0 var(--sc-focus-ring-width) var(--sc-focus-ring-color-info)}.button.button--standard.button--info:active:not(.button--disabled){background-color:var(--sc-color-info-500);border-color:var(--sc-color-info-500);color:var(--sc-color-info-text)}.button.button--standard.button--warning{background-color:var(--sc-color-warning-500);border-color:var(--sc-color-warning-500);color:var(--sc-color-warning-text)}.button.button--standard.button--warning:hover:not(.button--disabled){background-color:var(--sc-color-warning-400);border-color:var(--sc-color-warning-400);color:var(--sc-color-warning-text)}.button.button--standard.button--warning:focus:not(.button--disabled){background-color:var(--sc-color-warning-400);border-color:var(--sc-color-warning-400);color:var(--sc-color-warning-text);box-shadow:0 0 0 var(--sc-focus-ring-width) var(--sc-focus-ring-color-warning)}.button.button--standard.button--warning:active:not(.button--disabled){background-color:var(--sc-color-warning-500);border-color:var(--sc-color-warning-500);color:var(--sc-color-warning-text)}.button.button--standard.button--danger{background-color:var(--sc-color-danger-500);border-color:var(--sc-color-danger-500);color:var(--sc-color-danger-text)}.button.button--standard.button--danger:hover:not(.button--disabled){background-color:var(--sc-color-danger-400);border-color:var(--sc-color-danger-400);color:var(--sc-color-danger-text)}.button.button--standard.button--danger:focus:not(.button--disabled){background-color:var(--sc-color-danger-400);border-color:var(--sc-color-danger-400);color:var(--sc-color-danger-text);box-shadow:0 0 0 var(--sc-focus-ring-width) var(--sc-focus-ring-color-danger)}.button.button--standard.button--danger:active:not(.button--disabled){background-color:var(--sc-color-danger-500);border-color:var(--sc-color-danger-500);color:var(--sc-color-danger-text)}.button--outline{background:none;border:solid 1px}.button--outline.button--default{border-color:var(--sc-color-gray-300);color:var(--sc-color-gray-700)}.button--outline.button--default:hover:not(.button--disabled){border-color:var(--primary-background);background-color:var(--primary-background);color:var(--sc-color-white)}.button--outline.button--default:focus:not(.button--disabled){border-color:var(--primary-background);box-shadow:0 0 0 var(--sc-focus-ring-width) var(--primary-background)/var(--sc-focus-ring-alpha)}.button--outline.button--default:active:not(.button--disabled){opacity:0.8;color:var(--sc-color-white)}.button--outline.button--primary{border-color:var(--primary-background);color:var(--primary-background)}.button--outline.button--primary:hover:not(.button--disabled){background-color:var(--primary-background);opacity:0.8;color:var(--sc-color-white)}.button--outline.button--primary:focus:not(.button--disabled){border-color:var(--primary-background);box-shadow:0 0 0 var(--sc-focus-ring-width) var(--primary-background)/var(--sc-focus-ring-alpha)}.button--outline.button--primary:active:not(.button--disabled){border-color:var(--primary-background);background-color:var(--primary-background);opacity:0.9;color:var(--sc-color-white)}.button--outline.button--success{border-color:var(--sc-color-success-500);color:var(--sc-color-success-500)}.button--outline.button--success:hover:not(.button--disabled){background-color:var(--sc-color-success-500);color:var(--sc-color-white)}.button--outline.button--success:focus:not(.button--disabled){border-color:var(--sc-color-success-500);box-shadow:0 0 0 var(--sc-focus-ring-width) var(--sc-color-success-500)/var(--sc-focus-ring-alpha)}.button--outline.button--success:active:not(.button--disabled){border-color:var(--sc-color-success-700);background-color:var(--sc-color-success-700);color:var(--sc-color-white)}.button--outline.button--info{border-color:var(--sc-color-gray-500);color:var(--sc-color-gray-500)}.button--outline.button--info:hover:not(.button--disabled){background-color:var(--sc-color-gray-500);color:var(--sc-color-white)}.button--outline.button--info:focus:not(.button--disabled){border-color:var(--sc-color-gray-500);box-shadow:0 0 0 var(--sc-focus-ring-width) var(--sc-color-gray-500)/var(--sc-focus-ring-alpha)}.button--outline.button--info:active:not(.button--disabled){border-color:var(--sc-color-gray-700);background-color:var(--sc-color-gray-700);color:var(--sc-color-white)}.button--outline.button--warning{border-color:var(--sc-color-warning-500);color:var(--sc-color-warning-500)}.button--outline.button--warning:hover:not(.button--disabled){background-color:var(--sc-color-warning-500);color:var(--sc-color-white)}.button--outline.button--warning:focus:not(.button--disabled){border-color:var(--sc-color-warning-500);box-shadow:0 0 0 var(--sc-focus-ring-width) var(--sc-color-warning-500)/var(--sc-focus-ring-alpha)}.button--outline.button--warning:active:not(.button--disabled){border-color:var(--sc-color-warning-700);background-color:var(--sc-color-warning-700);color:var(--sc-color-white)}.button--outline.button--danger{border-color:var(--sc-color-danger-500);color:var(--sc-color-danger-500)}.button--outline.button--danger:hover:not(.button--disabled){background-color:var(--sc-color-danger-500);color:var(--sc-color-white)}.button--outline.button--danger:focus:not(.button--disabled){border-color:var(--sc-color-danger-500);box-shadow:0 0 0 var(--sc-focus-ring-width) var(--sc-color-danger-500)/var(--sc-focus-ring-alpha)}.button--outline.button--danger:active:not(.button--disabled){border-color:var(--sc-color-danger-700);background-color:var(--sc-color-danger-700);color:var(--sc-color-white)}.button--text{background-color:transparent;border-color:transparent;color:inherit}.button--text:hover:not(.button--disabled){background-color:transparent;border-color:transparent;color:var(--sc-color-gray-600)}.button--text:focus:not(.button--disabled){background-color:transparent;border-color:transparent;box-shadow:0}.button--text:active:not(.button--disabled){background-color:transparent;border-color:transparent;box-shadow:0}.button--text.button--caret.button--has-label{padding-right:var(--sc-spacing-xx-small)}.button--text.button--caret.button--has-label .button__label{padding:0 var(--sc-spacing-xx-small) !important}.button--link{background-color:transparent;border-color:transparent;box-shadow:none;color:var(--sc-button-link-color, var(--primary-background));transition:opacity var(--sc-input-transition, var(--sc-transition-medium)) ease;text-decoration:var(--sc-button-link-text-decoration, none)}.button--link.button--has-label.button--small .button__label,.button--link.button--has-label.button--medium .button__label,.button--link.button--has-label.button--large .button__label{padding:0}.button--link:hover:not(.button--disabled){background-color:transparent;border-color:transparent;opacity:0.75}.button--link:focus:not(.button--disabled){background-color:transparent;border-color:transparent}.button--link:active:not(.button--disabled){background-color:transparent;border-color:transparent}.button--link.button--has-prefix:not(.button--text).button--small,.button--link.button--has-prefix:not(.button--text).button--medium,.button--link.button--has-prefix:not(.button--text).button--large{padding-left:0}.button--link.button--has-prefix:not(.button--text).button--small .button__label,.button--link.button--has-prefix:not(.button--text).button--medium .button__label,.button--link.button--has-prefix:not(.button--text).button--large .button__label{padding-left:var(--sc-spacing-xx-small)}.button--link.button--has-suffix:not(.button--text).button--small,.button--link.button--has-suffix:not(.button--text).button--medium,.button--link.button--has-suffix:not(.button--text).button--large{padding-right:0}.button--link.button--has-suffix:not(.button--text).button--small .button__label,.button--link.button--has-suffix:not(.button--text).button--medium .button__label,.button--link.button--has-suffix:not(.button--text).button--large .button__label{padding-right:var(--sc-spacing-xx-small)}.button--small{font-size:var(--sc-button-font-size-small);height:var(--sc-input-height-small);line-height:calc(var(--sc-input-height-small) - var(--sc-input-border-width) * 2);border-radius:var(--button-border-radius, var(--sc-input-border-radius-small))}.button--medium{font-size:var(--sc-button-font-size-medium);height:var(--sc-input-height-medium);line-height:calc(var(--sc-input-height-medium) - var(--sc-input-border-width) * 2);border-radius:var(--button-border-radius, var(--sc-input-border-radius-medium))}.button--large{font-size:var(--sc-button-font-size-large);height:var(--sc-input-height-large);line-height:calc(var(--sc-input-height-large) - var(--sc-input-border-width) * 2);border-radius:var(--button-border-radius, var(--sc-input-border-radius-large))}.button--full{display:block}.button--pill.button--small{border-radius:var(--sc-input-height-small)}.button--pill.button--medium{border-radius:var(--sc-input-height-medium)}.button--pill.button--large{border-radius:var(--sc-input-height-large)}.button--circle{padding-left:0;padding-right:0}.button--circle.button--small{width:var(--sc-input-height-small);border-radius:50%}.button--circle.button--medium{width:var(--sc-input-height-medium);border-radius:50%}.button--circle.button--large{width:var(--sc-input-height-large);border-radius:50%}.button--circle .button__prefix,.button--circle .button__suffix,.button--circle .button__caret{display:none}.button--caret .button__suffix{display:none}.button--caret .button__caret{display:flex;align-items:center}.button--caret .button__caret svg{width:1em;height:1em}.button--busy{position:relative;cursor:wait}.button--busy .button__prefix,.button--busy .button__label,.button--busy .button__suffix,.button--busy .button__caret{visibility:hidden}.button--busy *{pointer-events:none}.button--loading{position:relative;cursor:wait}.button--loading .button__prefix,.button--loading .button__label,.button--loading .button__suffix,.button--loading .button__caret{visibility:hidden}sc-spinner::part(base){--indicator-color:currentColor;--spinner-size:12px;position:absolute;top:calc(50% - var(--spinner-size) + var(--spinner-size) / 4);left:calc(50% - var(--spinner-size) + var(--spinner-size) / 4)}.button ::slotted(sc-badge){position:absolute;top:0;right:0;transform:translateY(-50%) translateX(50%);pointer-events:none}.button--has-label.button--small .button__label{padding:0 var(--sc-spacing-small)}.button--has-label.button--medium .button__label{padding:0 var(--sc-spacing-medium)}.button--has-label.button--large .button__label{padding:0 var(--sc-spacing-large)}.button--has-prefix:not(.button--text).button--small{padding-left:var(--sc-spacing-x-small)}.button--has-prefix:not(.button--text).button--small .button__label{padding-left:var(--sc-spacing-x-small)}.button--has-prefix:not(.button--text).button--medium{padding-left:var(--sc-spacing-small)}.button--has-prefix:not(.button--text).button--medium .button__label{padding-left:var(--sc-spacing-small)}.button--has-prefix:not(.button--text).button--large{padding-left:var(--sc-spacing-small)}.button--has-prefix:not(.button--text).button--large .button__label{padding-left:var(--sc-spacing-small)}.button--has-suffix.button--small,.button--caret.button--small{padding-right:var(--sc-spacing-x-small)}.button--has-suffix.button--small .button__label,.button--caret.button--small .button__label{padding-right:var(--sc-spacing-x-small)}.button--has-suffix.button--medium,.button--caret.button--medium{padding-right:var(--sc-spacing-small)}.button--has-suffix.button--medium .button__label,.button--caret.button--medium .button__label{padding-right:var(--sc-spacing-small)}.button--has-suffix.button--large,.button--caret.button--large{padding-right:var(--sc-spacing-small)}.button--has-suffix.button--large .button__label,.button--caret.button--large .button__label{padding-right:var(--sc-spacing-small)}:host(.sc-button-group__button--first) .button{border-top-right-radius:0;border-bottom-right-radius:0}:host(.sc-button-group__button--inner) .button{border-radius:0}:host(.sc-button-group__button--last) .button{border-top-left-radius:0;border-bottom-left-radius:0}:host(.sc-button-group__button:not(.sc-button-group__button--first)){margin-left:calc(-1 * var(--sc-input-border-width))}:host(.sc-button-group__button:not(.sc-button-group__button--focus,.sc-button-group__button--first,[type=default]):not(:hover,:active,:focus)) .button:after{content:\"\";position:absolute;top:0;left:0;bottom:0;border-left:solid 1px rgba(255, 255, 255, 0.2666666667);mix-blend-mode:lighten}:host(.sc-button-group__button--hover){z-index:1}:host(.sc-button-group__button--focus){z-index:2}@keyframes busy-animation{0%{background-position:200px 0}}.button--is-rtl.button--has-prefix.button--small,.button--is-rtl.button--has-prefix.button--medium,.button--is-rtl.button--has-prefix.button--large{padding-left:0}.button--is-rtl.button--has-prefix.button--small .button__label,.button--is-rtl.button--has-prefix.button--medium .button__label,.button--is-rtl.button--has-prefix.button--large .button__label{padding-left:0;padding-right:var(--sc-spacing-xx-small)}.button--is-rtl.button--has-suffix.button--small,.button--is-rtl.button--has-suffix.button--medium,.button--is-rtl.button--has-suffix.button--large{padding-right:0}.button--is-rtl.button--has-suffix.button--small .button__label,.button--is-rtl.button--has-suffix.button--medium .button__label,.button--is-rtl.button--has-suffix.button--large .button__label{padding-right:0;padding-left:var(--sc-spacing-xx-small)}";
const ScButtonStyle0 = scButtonCss;

const ScButton = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.scBlur = createEvent(this, "scBlur", 7);
        this.scFocus = createEvent(this, "scFocus", 7);
        this.hasFocus = false;
        this.hasLabel = false;
        this.hasPrefix = false;
        this.hasSuffix = false;
        this.type = 'default';
        this.size = 'medium';
        this.caret = false;
        this.full = false;
        this.disabled = false;
        this.loading = false;
        this.outline = false;
        this.busy = false;
        this.pill = false;
        this.circle = false;
        this.submit = false;
        this.name = undefined;
        this.value = undefined;
        this.href = undefined;
        this.target = undefined;
        this.download = undefined;
        this.autofocus = undefined;
    }
    componentWillLoad() {
        this.handleSlotChange();
    }
    /** Simulates a click on the button. */
    click() {
        this.button.click();
    }
    /** Sets focus on the button. */
    focus(options) {
        this.button.focus(options);
    }
    /** Removes focus from the button. */
    blur() {
        this.button.blur();
    }
    handleSlotChange() {
        this.hasLabel = !!this.button.children;
        this.hasPrefix = !!this.button.querySelector('[slot="prefix"]');
        this.hasSuffix = !!this.button.querySelector('[slot="suffix"]');
    }
    handleBlur() {
        this.hasFocus = false;
        this.scBlur.emit();
    }
    handleFocus() {
        this.hasFocus = true;
        this.scFocus.emit();
    }
    handleClick(event) {
        if (this.disabled || this.loading || this.busy) {
            event.preventDefault();
            event.stopPropagation();
        }
        if (this.submit) {
            this.submitForm();
        }
    }
    submitForm() {
        var _a, _b;
        const form = ((_b = (_a = this.button.closest('sc-form')) === null || _a === void 0 ? void 0 : _a.shadowRoot) === null || _b === void 0 ? void 0 : _b.querySelector('form')) || this.button.closest('form');
        // Calling form.submit() seems to bypass the submit event and constraint validation. Instead, we can inject a
        // native submit button into the form, click it, then remove it to simulate a standard form submission.
        const button = document.createElement('button');
        if (form) {
            button.type = 'submit';
            button.style.position = 'absolute';
            button.style.width = '0';
            button.style.height = '0';
            button.style.clip = 'rect(0 0 0 0)';
            button.style.clipPath = 'inset(50%)';
            button.style.overflow = 'hidden';
            button.style.whiteSpace = 'nowrap';
            form.append(button);
            button.click();
            button.remove();
        }
    }
    render() {
        const Tag = this.href ? 'a' : 'button';
        const interior = (h(Fragment, { key: 'f24e2a28bcd34f5dc690696251990215748f3b90' }, h("span", { key: '3e40c4bd145c10b04baeb7367a34197214a34040', part: "prefix", class: "button__prefix" }, h("slot", { key: 'e43d867289b8089f9506dc01bbceaf0c0c6578e7', onSlotchange: () => this.handleSlotChange(), name: "prefix" })), h("span", { key: '2fd1716e139d428c0357b30c1257fb142a4c5539', part: "label", class: "button__label" }, h("slot", { key: 'fe59ee1a2300225bd4d4b46b104c6604814a1cfb', onSlotchange: () => this.handleSlotChange() })), h("span", { key: '52914e300a151d2453e9edcee91f2984426b5460', part: "suffix", class: "button__suffix" }, h("slot", { key: 'a3d7be32fdcd5ca13ae78ed5d1a43be01cda91a2', onSlotchange: () => this.handleSlotChange(), name: "suffix" })), this.caret ? (h("span", { part: "caret", class: "button__caret" }, h("svg", { viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", "stroke-width": "2", "stroke-linecap": "round", "stroke-linejoin": "round" }, h("polyline", { points: "6 9 12 15 18 9" })))) : (''), this.loading || this.busy ? h("sc-spinner", { exportparts: "base:spinner" }) : ''));
        return (h(Tag, { key: 'd3ac910cccb0996e75392170ca3f3d5f97aec797', part: "base", class: {
                'button': true,
                [`button--${this.type}`]: !!this.type,
                [`button--${this.size}`]: true,
                'button--caret': this.caret,
                'button--circle': this.circle,
                'button--disabled': this.disabled,
                'button--focused': this.hasFocus,
                'button--loading': this.loading,
                'button--busy': this.busy,
                'button--pill': this.pill,
                'button--standard': !this.outline,
                'button--outline': this.outline,
                'button--has-label': this.hasLabel,
                'button--has-prefix': this.hasPrefix,
                'button--has-suffix': this.hasSuffix,
                'button--is-rtl': isRtl(),
            }, href: this.href, target: this.target, download: this.download, autoFocus: this.autofocus, rel: this.target ? 'noreferrer noopener' : undefined, role: "button", "aria-disabled": this.disabled ? 'true' : 'false', "aria-busy": this.busy || this.loading ? 'true' : 'false', tabindex: this.disabled ? '-1' : '0', disabled: this.disabled || this.busy, type: this.submit ? 'submit' : 'button', name: this.name, value: this.value, onBlur: () => this.handleBlur(), onFocus: () => this.handleFocus(), onClick: e => this.handleClick(e) }, interior));
    }
    get button() { return getElement(this); }
};
ScButton.style = ScButtonStyle0;

const scDashboardModuleCss = ":host{display:block;position:relative}.dashboard-module{display:grid;gap:var(--sc-dashboard-module-spacing, 1em)}.heading{font-family:var(--sc-font-sans);display:flex;flex-wrap:wrap;gap:1em;align-items:center;justify-content:space-between}.heading__text{display:grid;flex:1;gap:calc(var(--sc-dashboard-module-spacing, 1em) / 2)}@media screen and (min-width: 720px){.heading{gap:2em}}.heading__title{font-size:var(--sc-dashbaord-module-heading-size, var(--sc-font-size-x-large));font-weight:var(--sc-dashbaord-module-heading-weight, var(--sc-font-weight-bold));line-height:var(--sc-dashbaord-module-heading-line-height, var(--sc-line-height-dense));white-space:nowrap}.heading__description{font-size:var(--sc-font-size-normal);line-height:var(--sc-line-height-dense);opacity:0.85}";
const ScDashboardModuleStyle0 = scDashboardModuleCss;

const ScDashboardModule = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.heading = undefined;
        this.error = undefined;
        this.loading = undefined;
    }
    render() {
        return (h("div", { key: '3de0cde0fe04ee3424defbce8cfa68025c79e3e0', class: "dashboard-module", part: "base" }, !!this.error && (h("sc-alert", { key: 'bc897abbb84b511f7e23b07f9a869319f18f62b2', exportparts: "base:error__base, icon:error__icon, text:error__text, title:error__title, message:error__message", open: !!this.error, type: "danger" }, h("span", { key: '1b0bc02a679ae34033f457714f7ce2588729d0c6', slot: "title" }, wp.i18n.__('Error', 'surecart')), this.error)), h("div", { key: 'af6d73e44be70a61fea4e1381c1f4b5254956135', class: "heading", part: "heading" }, h("div", { key: '9e29217cbb7333ba5df3d3525dd685c0768874be', class: "heading__text", part: "heading-text" }, h("div", { key: '7c76e45b97ec123f2bf8ded60867630e6fe6c2a5', class: "heading__title", part: "heading-title" }, h("slot", { key: 'f0679df2c2654bade5c39dd72237c02a4d244801', name: "heading", "aria-label": this.heading }, this.heading)), h("div", { key: '336a6d29e685ccee3f4a4fbab04f4360c1f14077', class: "heading__description", part: "heading-description" }, h("slot", { key: 'bae0d71b143c65fda0e81b16926260ead5496939', name: "description" }))), h("slot", { key: '9aefc55963fc5b0daadb91dc4960b7327b375589', name: "end" })), h("slot", { key: '898f1e47a2afc0eff3762f50a1ce407a7a392dc7' })));
    }
};
ScDashboardModule.style = ScDashboardModuleStyle0;

const locks = new Set();
//
// Prevents body scrolling. Keeps track of which elements requested a lock so multiple levels of locking are possible
// without premature unlocking.
//
function lockBodyScrolling(lockingEl) {
    locks.add(lockingEl);
    document.body.classList.add('sc-scroll-lock');
}
//
// Unlocks body scrolling. Scrolling will only be unlocked once all elements that requested a lock call this method.
//
function unlockBodyScrolling(lockingEl) {
    locks.delete(lockingEl);
    if (locks.size === 0) {
        document.body.classList.remove('sc-scroll-lock');
    }
}

const scDialogCss = ":host{--width:31rem;--header-spacing:var(--sc-spacing-large);--body-spacing:var(--sc-spacing-large);--footer-spacing:var(--sc-spacing-large);display:contents}[hidden]{display:none !important}.dialog{display:flex;align-items:center;justify-content:center;position:fixed;top:0;right:0;bottom:0;left:0;z-index:var(--sc-z-index-dialog);box-sizing:border-box;text-align:left}.dialog__panel{display:flex;flex-direction:column;z-index:2;width:var(--width);max-width:100vw;max-height:100vh;background-color:var(--sc-panel-background-color);border-radius:var(--sc-border-radius-medium);box-shadow:var(--sc-shadow-x-large);position:relative}.dialog__panel:focus{outline:none}@media screen and (max-width: 420px){.dialog__panel{max-height:80vh}}.dialog--open .dialog__panel{display:flex;opacity:1;transform:none}.dialog__header{flex:0 0 auto;display:flex;border-bottom:1px solid var(--sc-color-gray-300)}.dialog__title{flex:1 1 auto;font:inherit;font-size:var(--sc-font-size-large);line-height:var(--sc-line-height-dense);padding:var(--header-spacing);margin:0}.dialog__close{flex:0 0 auto;display:flex;align-items:center;font-size:var(--sc-font-size-x-large);padding:0 calc(var(--header-spacing) / 2);z-index:2}.dialog__body{flex:1 1 auto;padding:var(--body-spacing);overflow:var(--dialog-body-overflow, auto);-webkit-overflow-scrolling:touch}.dialog__footer{flex:0 0 auto;text-align:right;padding:var(--footer-spacing)}.dialog__footer ::slotted(sl-button:not(:first-of-type)){margin-left:var(--sc-spacing-x-small)}.dialog:not(.dialog--has-footer) .dialog__footer{display:none}.dialog__overlay{position:fixed;top:0;right:0;bottom:0;left:0;background-color:var(--sc-overlay-background-color)}";
const ScDialogStyle0 = scDialogCss;

const ScDialog = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.scRequestClose = createEvent(this, "scRequestClose", 7);
        this.scShow = createEvent(this, "scShow", 7);
        this.scAfterShow = createEvent(this, "scAfterShow", 7);
        this.scHide = createEvent(this, "scHide", 7);
        this.scAfterHide = createEvent(this, "scAfterHide", 7);
        this.scInitialFocus = createEvent(this, "scInitialFocus", 7);
        this.open = false;
        this.label = '';
        this.noHeader = false;
        this.hasFooter = false;
    }
    /** Shows the dialog. */
    async show() {
        if (this.open) {
            return undefined;
        }
        this.open = true;
    }
    /** Hides the dialog */
    async hide() {
        if (!this.open) {
            return undefined;
        }
        this.open = false;
    }
    requestClose(source) {
        const slRequestClose = this.scRequestClose.emit(source);
        if (slRequestClose.defaultPrevented) {
            const animation = getAnimation(this.el, 'dialog.denyClose');
            animateTo(this.panel, animation.keyframes, animation.options);
            return;
        }
        this.hide();
    }
    handleKeyDown(event) {
        if (event.key === 'Escape') {
            event.stopPropagation();
            this.requestClose('keyboard');
        }
    }
    async handleOpenChange() {
        if (this.open) {
            // Show
            this.scShow.emit();
            lockBodyScrolling(this.el);
            // When the dialog is shown, Safari will attempt to set focus on whatever element has autofocus. This can cause
            // the dialogs's animation to jitter (if it starts offscreen), so we'll temporarily remove the attribute, call
            // `focus({ preventScroll: true })` ourselves, and add the attribute back afterwards.
            //
            // Related: https://github.com/shoelace-style/shoelace/issues/693
            //
            const autoFocusTarget = this.el.querySelector('[autofocus]');
            if (autoFocusTarget) {
                autoFocusTarget.removeAttribute('autofocus');
            }
            await Promise.all([stopAnimations(this.dialog), stopAnimations(this.overlay)]);
            this.dialog.hidden = false;
            // Set initial focus
            requestAnimationFrame(() => {
                const slInitialFocus = this.scInitialFocus.emit();
                if (!slInitialFocus.defaultPrevented) {
                    // Set focus to the autofocus target and restore the attribute
                    if (autoFocusTarget) {
                        autoFocusTarget.focus({ preventScroll: true });
                    }
                    else {
                        this.panel.focus({ preventScroll: true });
                    }
                }
                // Restore the autofocus attribute
                if (autoFocusTarget) {
                    autoFocusTarget.setAttribute('autofocus', '');
                }
            });
            const panelAnimation = getAnimation(this.el, 'dialog.show');
            const overlayAnimation = getAnimation(this.el, 'dialog.overlay.show');
            await Promise.all([animateTo(this.panel, panelAnimation.keyframes, panelAnimation.options), animateTo(this.overlay, overlayAnimation.keyframes, overlayAnimation.options)]);
            this.scAfterShow.emit();
        }
        else {
            // Hide
            this.scHide.emit();
            await Promise.all([stopAnimations(this.dialog), stopAnimations(this.overlay)]);
            const panelAnimation = getAnimation(this.el, 'dialog.hide');
            const overlayAnimation = getAnimation(this.el, 'dialog.overlay.hide');
            await Promise.all([animateTo(this.panel, panelAnimation.keyframes, panelAnimation.options), animateTo(this.overlay, overlayAnimation.keyframes, overlayAnimation.options)]);
            this.dialog.hidden = true;
            unlockBodyScrolling(this.el);
            // Restore focus to the original trigger
            const trigger = this.originalTrigger;
            if (typeof (trigger === null || trigger === void 0 ? void 0 : trigger.focus) === 'function') {
                setTimeout(() => trigger.focus());
            }
            this.scAfterHide.emit();
        }
    }
    componentDidLoad() {
        this.hasFooter = !!this.el.querySelector('[slot="footer"]');
        this.dialog.hidden = !this.open;
        if (this.open) {
            lockBodyScrolling(this.el);
        }
    }
    disconnectedCallback() {
        unlockBodyScrolling(this.el);
    }
    render() {
        return (h("div", { key: '38d2c6af4a8d7b070bb6dc5c0245b1358c304352', part: "base", ref: el => (this.dialog = el), class: {
                'dialog': true,
                'dialog--open': this.open,
                'dialog--has-footer': this.hasFooter,
            }, onKeyDown: e => this.handleKeyDown(e) }, h("div", { key: '64cd971562326940559b8a592deffa0fb17f9e12', part: "overlay", class: "dialog__overlay", onClick: e => {
                e.preventDefault();
                e.stopImmediatePropagation();
                this.requestClose('overlay');
            }, ref: el => (this.overlay = el), tabindex: "-1" }), h("div", { key: '351e0dddabf3e637bb62544fe6a6cbd8e80040f4', part: "panel", class: "dialog__panel", role: "dialog", "aria-modal": "true", "aria-hidden": this.open ? 'false' : 'true', "aria-label": this.noHeader || this.label, "aria-labelledby": !this.noHeader || 'title', ref: el => (this.panel = el), tabindex: "0" }, !this.noHeader && (h("header", { key: '012652b477eae5e1ddc6eb90b50e104ae3db7aa5', part: "header", class: "dialog__header" }, h("h2", { key: '0a43e4c4920988c8216638eec54878ddf46397db', part: "title", class: "dialog__title", id: "title" }, h("slot", { key: '047d8992c93bb06ca291263e73536e07c3bb3726', name: "label" }, " ", this.label.length > 0 ? this.label : String.fromCharCode(65279), " ")), h("sc-button", { key: 'a75189397456cb80e10f902fc1b28d3ed6c233a5', class: "dialog__close", type: "text", circle: true, part: "close-button", exportparts: "base:close-button__base", onClick: e => {
                e.preventDefault();
                e.stopImmediatePropagation();
                this.requestClose('close-button');
            } }, h("sc-icon", { key: '81562918427a9de80a8ba4601dd6d803d37f36f4', name: "x", label: wp.i18n.__('Close', 'surecart') })))), h("div", { key: '8e39e9f87b95b59be6ba7dcf82ddb6eb136caf5a', part: "body", class: "dialog__body" }, h("slot", { key: 'bdd41f463913cbc66280d29102caa33e862365f5' })), h("footer", { key: '10b6ed05749a307aa57ef26a692eaf78495f3bcc', part: "footer", class: "dialog__footer" }, h("slot", { key: 'cd85833b30866c11c047c4800d245dcb6704e3ef', name: "footer" })))));
    }
    get el() { return getElement(this); }
    static get watchers() { return {
        "open": ["handleOpenChange"]
    }; }
};
setDefaultAnimation('dialog.show', {
    keyframes: [
        { opacity: 0, transform: 'scale(0.8)' },
        { opacity: 1, transform: 'scale(1)' },
    ],
    options: { duration: 150, easing: 'ease' },
});
setDefaultAnimation('dialog.hide', {
    keyframes: [
        { opacity: 1, transform: 'scale(1)' },
        { opacity: 0, transform: 'scale(0.8)' },
    ],
    options: { duration: 150, easing: 'ease' },
});
setDefaultAnimation('dialog.denyClose', {
    keyframes: [{ transform: 'scale(1)' }, { transform: 'scale(1.02)' }, { transform: 'scale(1)' }],
    options: { duration: 150 },
});
setDefaultAnimation('dialog.overlay.show', {
    keyframes: [{ opacity: 0 }, { opacity: 1 }],
    options: { duration: 150 },
});
setDefaultAnimation('dialog.overlay.hide', {
    keyframes: [{ opacity: 1 }, { opacity: 0 }],
    options: { duration: 150 },
});
ScDialog.style = ScDialogStyle0;

const scFlexCss = ":host{display:block;--spacing:var(--sc-spacing-small)}.flex{display:flex;gap:var(--sc-flex-column-gap, var(--spacing));justify-content:var(--sc-flex-space-between, space-between)}.justify-flex-start{justify-content:flex-start}.justify-flex-end{justify-content:flex-end}.justify-center{justify-content:center}.justify-space-between{justify-content:space-between}.justify-space-around{justify-content:space-around}.justify-space-evenly{justify-content:space-evenly}.wrap-wrap{flex-wrap:wrap}.wrap-no-wrap{flex-wrap:no-wrap}.align-flex-start{align-items:flex-start}.align-flex-end{align-items:flex-end}.align-center{align-items:center}.align-baseline{align-items:baseline}.align-stretch{align-items:stretch}.direction-row{flex-direction:row}.direction-row-reverse{flex-direction:row-reverse}.direction-column{flex-direction:column}.direction-column-reverse{flex-direction:column-reverse}@media (max-width: 480px){.stack-mobile{flex-direction:column}}@media (max-width: 768px){.stack-tablet{flex-direction:column}}@media (max-width: 1180px){.stack-desktop{flex-direction:column}}";
const ScFlexStyle0 = scFlexCss;

const ScFlex = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.alignItems = undefined;
        this.justifyContent = undefined;
        this.flexDirection = undefined;
        this.columnGap = undefined;
        this.flexWrap = undefined;
        this.stack = undefined;
    }
    render() {
        return (h("div", { key: 'fe79ec5e2b745f72291050f684e1e8755f824b41', part: "base", class: {
                flex: true,
                ...(this.justifyContent ? { [`justify-${this.justifyContent}`]: true } : {}),
                ...(this.alignItems ? { [`align-${this.alignItems}`]: true } : {}),
                ...(this.flexDirection ? { [`direction-${this.flexDirection}`]: true } : {}),
                ...(this.columnGap ? { [`column-gap-${this.columnGap}`]: true } : {}),
                ...(this.flexWrap ? { [`wrap-${this.flexWrap}`]: true } : {}),
                ...(this.stack ? { [`stack-${this.stack}`]: true } : {}),
            } }, h("slot", { key: '9772fa51d4c6e255454cc9feae4850805e21d30c' })));
    }
};
ScFlex.style = ScFlexStyle0;

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
        return h("div", { key: 'deedb35b3e492ab36508a8a896e3089f2e11626b', part: "base", class: "icon", role: "img", "aria-label": this.getLabel(), innerHTML: this.svg });
    }
    static get assetsDirs() { return ["icon-assets"]; }
    static get watchers() { return {
        "name": ["setIcon"],
        "src": ["setIcon"],
        "library": ["setIcon"]
    }; }
};
ScIcon.style = ScIconStyle0;

const scSkeletonCss = ":host{position:relative;box-sizing:border-box}:host *,:host *:before,:host *:after{box-sizing:inherit}:host{--border-radius:var(--sc-border-radius-pill);--color:var(--sc-skeleton-color, var(--sc-color-gray-300));--sheen-color:var(--sc-skeleton-sheen-color, var(--sc-color-gray-400));display:block;position:relative}.skeleton{display:flex;width:100%;height:100%;min-height:1rem}.skeleton__indicator{flex:1 1 auto;background:var(--color);border-radius:var(--border-radius)}.skeleton--sheen .skeleton__indicator{background:linear-gradient(270deg, var(--sheen-color), var(--color), var(--color), var(--sheen-color));background-size:400% 100%;background-size:400% 100%;animation:sheen 3s ease-in-out infinite}.skeleton--pulse .skeleton__indicator{animation:pulse 2s ease-in-out 0.5s infinite}@keyframes sheen{0%{background-position:200% 0}to{background-position:-200% 0}}@keyframes pulse{0%{opacity:1}50%{opacity:0.4}100%{opacity:1}}";
const ScSkeletonStyle0 = scSkeletonCss;

const ScSkeleton = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.effect = 'sheen';
    }
    render() {
        return (h("div", { key: '1a76dbb79dc081052cb7afe60389c12bb0df3d62', part: "base", class: {
                'skeleton': true,
                'skeleton--pulse': this.effect === 'pulse',
                'skeleton--sheen': this.effect === 'sheen',
            }, "aria-busy": "true", "aria-live": "polite" }, h("div", { key: 'bd4cf347f55fe6e4201df7e6fc33b7f842820dd6', part: "indicator", class: "skeleton__indicator" })));
    }
};
ScSkeleton.style = ScSkeletonStyle0;

const scSpinnerCss = ":host{--track-color:#0d131e20;--indicator-color:var(--sc-color-primary-500);--stroke-width:2px;--spinner-size:1em;display:inline-block}.spinner{display:inline-block;width:var(--spinner-size);height:var(--spinner-size);border-radius:50%;border:solid var(--stroke-width) var(--track-color);border-top-color:var(--indicator-color);border-right-color:var(--indicator-color);animation:1s linear infinite spin}@keyframes spin{0%{transform:rotate(0deg)}100%{transform:rotate(360deg)}}";
const ScSpinnerStyle0 = scSpinnerCss;

const ScSpinner = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
    }
    render() {
        return h("span", { key: 'af8f23733cc642dfc04fbabda8b20785bbd29ee5', part: "base", class: "spinner", "aria-busy": "true", "aria-live": "polite" });
    }
};
ScSpinner.style = ScSpinnerStyle0;

const ScSubscriptionReactivate = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.scRequestClose = createEvent(this, "scRequestClose", 7);
        this.scRefresh = createEvent(this, "scRefresh", 7);
        this.open = undefined;
        this.subscription = undefined;
        this.busy = undefined;
        this.error = undefined;
        this.upcomingPeriod = undefined;
        this.loading = false;
    }
    openChanged() {
        if (this.open) {
            this.fetchUpcoming();
        }
    }
    async fetchUpcoming() {
        var _a;
        this.loading = true;
        try {
            this.upcomingPeriod = await apiFetch({
                method: 'PATCH',
                path: addQueryArgs(`surecart/v1/subscriptions/${(_a = this.subscription) === null || _a === void 0 ? void 0 : _a.id}/upcoming_period`, {
                    skip_product_group_validation: true,
                    expand: ['period.checkout'],
                }),
                data: {
                    purge_pending_update: false,
                },
            });
        }
        catch (e) {
            this.error = (e === null || e === void 0 ? void 0 : e.message) || wp.i18n.__('Something went wrong', 'surecart');
        }
        finally {
            this.loading = false;
        }
    }
    async reactivateSubscription() {
        var _a;
        try {
            this.error = '';
            this.busy = true;
            await apiFetch({
                path: `surecart/v1/subscriptions/${(_a = this.subscription) === null || _a === void 0 ? void 0 : _a.id}/restore`,
                method: 'PATCH',
            });
            this.scRefresh.emit();
            this.scRequestClose.emit('close-button');
        }
        catch (e) {
            this.error = (e === null || e === void 0 ? void 0 : e.message) || wp.i18n.__('Something went wrong', 'surecart');
        }
        finally {
            this.busy = false;
        }
    }
    renderLoading() {
        return (h("sc-flex", { flexDirection: "column", style: { gap: '1em' } }, h("sc-skeleton", { style: { width: '20%', display: 'inline-block' } }), h("sc-skeleton", { style: { width: '60%', display: 'inline-block' } }), h("sc-skeleton", { style: { width: '40%', display: 'inline-block' } })));
    }
    render() {
        var _a, _b, _c;
        return (h("sc-dialog", { key: '55896ac441036c347fc591fe3b94790d5893abf6', noHeader: true, open: this.open, style: { '--width': '600px', '--body-spacing': 'var(--sc-spacing-xxx-large)' } }, h("sc-dashboard-module", { key: '02066e7a5bda7097076185aa35e41c7ebf732b0c', loading: this.loading, heading: wp.i18n.__('Resubscribe', 'surecart'), class: "subscription-reactivate", error: this.error, style: { '--sc-dashboard-module-spacing': '1em' } }, this.loading ? (this.renderLoading()) : (h(Fragment, null, h("div", { slot: "description" }, h("sc-alert", { open: true, type: "warning", title: wp.i18n.__('Confirm Charge', 'surecart') }, wp.i18n.__('You will be charged', 'surecart'), " ", (_b = (_a = this.upcomingPeriod) === null || _a === void 0 ? void 0 : _a.checkout) === null || _b === void 0 ? void 0 :
            _b.amount_due_display_amount, wp.i18n.__('immediately for your subscription.', 'surecart')), h("sc-text", { style: {
                '--font-size': 'var(--sc-font-size-medium)',
                '--color': 'var(--sc-input-label-color)',
                '--line-height': 'var(--sc-line-height-dense)',
                'margin-top': 'var(--sc-spacing-medium)',
            } }, wp.i18n.__('Your subscription will be reactivated and will renew automatically on', 'surecart'), " ", h("strong", null, (_c = this.upcomingPeriod) === null || _c === void 0 ? void 0 : _c.end_at_date))), h("sc-flex", { justifyContent: "flex-start" }, h("sc-button", { type: "primary", loading: this.busy, disabled: this.busy, onClick: () => this.reactivateSubscription() }, wp.i18n.__('Yes, Reactivate', 'surecart')), h("sc-button", { disabled: this.busy, style: { color: 'var(--sc-color-gray-500)' }, type: "text", onClick: () => this.scRequestClose.emit() }, wp.i18n.__('No, Keep Inactive', 'surecart'))))), this.busy && h("sc-block-ui", { key: '7a1109e5dc7b2978dfd4a4de82e9974855f939da' }))));
    }
    static get watchers() { return {
        "open": ["openChanged"]
    }; }
};

const scTextCss = ":host{display:block;--font-size:var(--font-size, var(--sc-font-size-medium));--font-weight:var(--font-size, var(--sc-font-weight-normal));--line-height:var(--font-size, var(--sc-line-height-medium));--text-align:left;--color:var(--color, inherit)}.text{margin:0;font-size:var(--font-size);font-weight:var(--font-weight);line-height:var(--line-height);text-align:var(--text-align);color:var(--sc-stacked-list-row-text-color, var(--color))}.text.is-truncated{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.text--is-rtl .text{text-align:right}";
const ScTextStyle0 = scTextCss;

const ScText = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.tag = 'p';
        this.truncate = false;
    }
    render() {
        const CustomTag = this.tag;
        return (h(CustomTag, { key: '79f18432e864c05635a6b06d89f459c108e791ca', class: {
                'text': true,
                'is-truncated': this.truncate,
                'text--is-rtl': isRtl()
            } }, h("slot", { key: '6756a4105209c7c6e195ec669e0713c8bf173e94' })));
    }
};
ScText.style = ScTextStyle0;

export { ScAlert as sc_alert, ScBlockUi as sc_block_ui, ScButton as sc_button, ScDashboardModule as sc_dashboard_module, ScDialog as sc_dialog, ScFlex as sc_flex, ScIcon as sc_icon, ScSkeleton as sc_skeleton, ScSpinner as sc_spinner, ScSubscriptionReactivate as sc_subscription_reactivate, ScText as sc_text };

//# sourceMappingURL=sc-alert_11.entry.js.map