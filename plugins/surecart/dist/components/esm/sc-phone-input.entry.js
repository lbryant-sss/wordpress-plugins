import { r as registerInstance, c as createEvent, h, H as Host, a as getElement } from './index-745b6bec.js';
import { a as applyFilters } from './index-871d88b8.js';
import { F as FormSubmitController } from './form-data-76641f16.js';

const scPhoneInputCss = ":host{--focus-ring:0 0 0 var(--sc-focus-ring-width) var(--sc-focus-ring-color-primary);display:block;position:relative}:host([invalid]) .input,:host([invalid]) .input:hover:not(.input--disabled),:host([invalid]) .input--focused:not(.input--disabled){border-color:var(--sc-input-border-color-invalid);box-shadow:0 0 0 var(--sc-focus-ring-width) var(--sc-input-border-color-invalid)}.input__control[type=number]{-moz-appearance:textfield}.input__control::-webkit-outer-spin-button,.input__control::-webkit-inner-spin-button{-webkit-appearance:none}.input{flex:1 1 auto;display:inline-flex;align-items:center;justify-content:start;position:relative;width:100%;box-sizing:border-box;font-family:var(--sc-input-font-family);font-weight:var(--sc-input-font-weight);letter-spacing:var(--sc-input-letter-spacing);background-color:var(--sc-input-background-color);border:solid 1px var(--sc-input-border-color, var(--sc-input-border));vertical-align:middle;box-shadow:var(--sc-input-box-shadow);transition:var(--sc-transition-fast) color, var(--sc-transition-fast) border, var(--sc-transition-fast) box-shadow;cursor:text}.input:hover:not(.input--disabled){background-color:var(--sc-input-background-color-hover);border-color:var(--sc-input-border-color-hover);z-index:7}.input:hover:not(.input--disabled) .input__control{color:var(--sc-input-color-hover)}.input.input--focused:not(.input--disabled){background-color:var(--sc-input-background-color-focus);border-color:var(--sc-input-border-color-focus);box-shadow:var(--focus-ring);z-index:8}.input.input--focused:not(.input--disabled) .input__control{color:var(--sc-input-color-focus)}.input.input--disabled{background-color:var(--sc-input-background-color-disabled);border-color:var(--sc-input-border-color-disabled);opacity:0.5;cursor:not-allowed}.input.input--disabled .input__control{color:var(--sc-input-color-disabled)}.input.input--disabled .input__control::placeholder{color:var(--sc-input-placeholder-color-disabled)}.input__control{flex:1 1 auto;font-family:inherit;font-size:inherit;font-weight:inherit;min-width:0;height:100%;color:var(--sc-input-color);border:none;background:none;box-shadow:none;padding:0;margin:0;cursor:inherit;-webkit-appearance:none}.input__control::-webkit-search-decoration,.input__control::-webkit-search-cancel-button,.input__control::-webkit-search-results-button,.input__control::-webkit-search-results-decoration{-webkit-appearance:none}.input__control:-webkit-autofill,.input__control:-webkit-autofill:hover,.input__control:-webkit-autofill:focus,.input__control:-webkit-autofill:active{box-shadow:0 0 0 var(--sc-input-height-large) var(--sc-input-background-color-hover) inset !important;-webkit-text-fill-color:var(--sc-input-color)}.input__control::placeholder{color:var(--sc-input-placeholder-color);user-select:none}.input__control:focus{outline:none}.input__prefix,.input__suffix{display:inline-flex;flex:0 0 auto;align-items:center;color:var(--sc-input-color);cursor:default}.input__prefix ::slotted(sc-icon),.input__suffix ::slotted(sc-icon){color:var(--sc-input-icon-color)}.input--small{border-radius:var(--sc-input-border-radius-small);font-size:var(--sc-input-font-size-small);height:var(--sc-input-height-small)}.input--small .input__control{height:calc(var(--sc-input-height-small) - var(--sc-input-border-width) * 2);padding:0 var(--sc-input-spacing-small)}.input--small .input__clear,.input--small .input__password-toggle{margin-right:var(--sc-input-spacing-small)}.input--small .input__prefix ::slotted(*){margin-left:var(--sc-input-spacing-small)}.input--small .input__suffix ::slotted(*){margin-right:var(--sc-input-spacing-small)}.input--small .input__suffix ::slotted(sc-dropdown){margin:0}.input--medium{border-radius:var(--sc-input-border-radius-medium);font-size:var(--sc-input-font-size-medium);height:var(--sc-input-height-medium)}.input--medium .input__control{height:calc(var(--sc-input-height-medium) - var(--sc-input-border-width) * 2);padding:0 var(--sc-input-spacing-medium)}.input--medium .input__clear,.input--medium .input__password-toggle{margin-right:var(--sc-input-spacing-medium)}.input--medium .input__prefix ::slotted(*){margin-left:var(--sc-input-spacing-medium) !important}.input--medium .input__suffix ::slotted(:not(sc-button[size=medium])){margin-right:var(--sc-input-spacing-medium) !important}.input--medium .input__suffix ::slotted(sc-tag){margin-right:var(--sc-input-spacing-small) !important}.input--medium .input__suffix ::slotted(sc-dropdown){margin:3px}.input--large{border-radius:var(--sc-input-border-radius-large);font-size:var(--sc-input-font-size-large);height:var(--sc-input-height-large)}.input--large .input__control{height:calc(var(--sc-input-height-large) - var(--sc-input-border-width) * 2);padding:0 var(--sc-input-spacing-large)}.input--large .input__clear,.input--large .input__password-toggle{margin-right:var(--sc-input-spacing-large)}.input--large .input__prefix ::slotted(*){margin-left:var(--sc-input-spacing-large)}.input--large .input__suffix ::slotted(*){margin-right:var(--sc-input-spacing-large)}.input--large .input__suffix ::slotted(sc-dropdown){margin:3px}.input--pill.input--small{border-radius:var(--sc-input-height-small)}.input--pill.input--medium{border-radius:var(--sc-input-height-medium)}.input--pill.input--large{border-radius:var(--sc-input-height-large)}.input__clear,.input__password-toggle{display:inline-flex;align-items:center;font-size:inherit;color:var(--sc-input-icon-color);border:none;background:none;padding:0;transition:var(--sc-transition-fast) color;cursor:pointer}.input__clear:hover,.input__password-toggle:hover{color:var(--sc-input-icon-color-hover)}.input__clear:focus,.input__password-toggle:focus{outline:none}.input--empty .input__clear{visibility:hidden}.input--squared{border-radius:0}.input--squared-top{border-top-left-radius:0;border-top-right-radius:0}.input--squared-bottom{border-bottom-left-radius:0;border-bottom-right-radius:0}.input--squared-left{border-top-left-radius:0;border-bottom-left-radius:0}.input--squared-right{border-top-right-radius:0;border-bottom-right-radius:0}";
const ScPhoneInputStyle0 = scPhoneInputCss;

let id = 0;
const ScPhoneInput = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.scChange = createEvent(this, "scChange", 7);
        this.scClear = createEvent(this, "scClear", 7);
        this.scInput = createEvent(this, "scInput", 7);
        this.scFocus = createEvent(this, "scFocus", 7);
        this.scBlur = createEvent(this, "scBlur", 7);
        this.inputId = `phone-input-${++id}`;
        this.helpId = `phone-input-help-text-${id}`;
        this.labelId = `phone-input-label-${id}`;
        this.squared = undefined;
        this.squaredBottom = undefined;
        this.squaredTop = undefined;
        this.squaredLeft = undefined;
        this.squaredRight = undefined;
        this.hidden = false;
        this.size = 'medium';
        this.name = undefined;
        this.value = '';
        this.pill = false;
        this.label = undefined;
        this.showLabel = true;
        this.help = '';
        this.clearable = false;
        this.togglePassword = false;
        this.placeholder = undefined;
        this.disabled = false;
        this.readonly = false;
        this.minlength = undefined;
        this.maxlength = undefined;
        this.min = undefined;
        this.max = undefined;
        this.step = undefined;
        this.pattern = '[-s#0-9_+/().]*';
        this.required = false;
        this.invalid = false;
        this.autocorrect = undefined;
        this.autocomplete = undefined;
        this.autofocus = undefined;
        this.spellcheck = undefined;
        this.hasFocus = undefined;
    }
    async reportValidity() {
        return this.input.reportValidity();
    }
    /** Sets focus on the input. */
    async triggerFocus(options) {
        return this.input.focus(options);
    }
    /** Sets a custom validation message. If `message` is not empty, the field will be considered invalid. */
    async setCustomValidity(message) {
        this.input.setCustomValidity(message);
        this.invalid = !this.input.checkValidity();
    }
    /** Removes focus from the input. */
    async triggerBlur() {
        return this.input.blur();
    }
    /** Selects all the text in the input. */
    select() {
        return this.input.select();
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
        this.value = this.input.value;
        this.scChange.emit();
    }
    handleInput() {
        this.value = this.input.value.replace(/\s/g, '');
        this.input.value = this.value;
        this.scInput.emit();
    }
    handleClearClick(event) {
        this.value = '';
        this.scClear.emit();
        this.scInput.emit();
        this.scChange.emit();
        this.input.focus();
        event.stopPropagation();
    }
    handleFocusChange() {
        setTimeout(() => {
            this.hasFocus && this.input ? this.input.focus() : this.input.blur();
        }, 0);
    }
    handleValueChange() {
        if (this.input) {
            this.invalid = !this.input.checkValidity();
        }
    }
    componentDidLoad() {
        this.formController = new FormSubmitController(this.el).addFormData();
        this.handleFocusChange();
    }
    disconnectedCallback() {
        var _a;
        (_a = this.formController) === null || _a === void 0 ? void 0 : _a.removeFormData();
    }
    render() {
        var _a;
        return (h(Host, { key: '5af8e6e719326bd10b7ff6107df7f567435cd717', hidden: this.hidden }, h("sc-form-control", { key: 'f5dabd19605fe2613480c8c1f45e177d80b86fe6', exportparts: "label, help-text, form-control", size: this.size, required: this.required, label: this.label, showLabel: this.showLabel, help: this.help, inputId: this.inputId, helpId: this.helpId, labelId: this.labelId, name: this.name, "aria-label": this.label }, h("slot", { key: '42fccaf894b83525a2dda7b21025751cb6949be6', name: "label-end", slot: "label-end" }), h("div", { key: 'd9e910d5529409ee9b4d37b2f2fe24489207009f', part: "base", class: {
                'input': true,
                // Sizes
                'input--small': this.size === 'small',
                'input--medium': this.size === 'medium',
                'input--large': this.size === 'large',
                // States
                'input--focused': this.hasFocus,
                'input--invalid': this.invalid,
                'input--disabled': this.disabled,
                'input--squared': this.squared,
                'input--squared-bottom': this.squaredBottom,
                'input--squared-top': this.squaredTop,
                'input--squared-left': this.squaredLeft,
                'input--squared-right': this.squaredRight,
            } }, h("span", { key: '721e4896cd363b33549e8b777d07e019dd22f4f0', part: "prefix", class: "input__prefix" }, h("slot", { key: 'a96112e51a2497f16991980b7e5a59f4a196baca', name: "prefix" })), h("slot", { key: '9015feeac3291b50be8e90f6dfcf022c444a3dbf' }, h("input", { key: '3704dd32f87e03db2b28c441c19260093ef16a52', part: "input", id: this.inputId, class: "input__control", ref: el => (this.input = el), type: "tel", name: this.name, disabled: this.disabled, readonly: this.readonly, required: this.required, placeholder: this.placeholder, minlength: this.minlength, maxlength: this.maxlength, min: this.min, max: this.max, step: this.step,
            // TODO: Test These below
            autocomplete: 'tel', autocorrect: this.autocorrect, autofocus: this.autofocus, spellcheck: this.spellcheck, pattern: applyFilters('surecart/sc-phone-input/pattern', this.pattern), inputmode: 'numeric', "aria-label": this.label, "aria-labelledby": this.label, "aria-invalid": this.invalid ? true : false, value: this.value, onChange: () => this.handleChange(), onInput: () => this.handleInput(), onFocus: () => this.handleFocus(), onBlur: () => this.handleBlur() })), h("span", { key: '403cc791e3d27a9ab8f42bcdb88fdd602a9fddf5', part: "suffix", class: "input__suffix" }, h("slot", { key: '566a7ec9bb6456f6755a40057c5e19112fb05abc', name: "suffix" })), this.clearable && ((_a = this.value) === null || _a === void 0 ? void 0 : _a.length) > 0 && (h("button", { key: '5f1b5582e39ccc724d248b6a1270a21946c4245f', part: "clear-button", class: "input__clear", type: "button", onClick: e => this.handleClearClick(e), tabindex: "-1" }, h("slot", { key: '552b4d83027e3327ec8c3f4d430df43fcc40be84', name: "clear-icon" }, h("svg", { key: '65fcca5afb9fa10a25db959b4835810a12176693', xmlns: "http://www.w3.org/2000/svg", width: "16", height: "16", viewBox: "0 0 24 24", fill: "none", stroke: "currentColor", "stroke-width": "2", "stroke-linecap": "round", "stroke-linejoin": "round", class: "feather feather-x" }, h("line", { key: '1c040e045a8a8c59b8d3e11e6c8645130c7da486', x1: "18", y1: "6", x2: "6", y2: "18" }), h("line", { key: 'e2f9d2c7c6036781075b9a2d368c1d1158ef42db', x1: "6", y1: "6", x2: "18", y2: "18" })))))))));
    }
    get el() { return getElement(this); }
    static get watchers() { return {
        "hasFocus": ["handleFocusChange"],
        "value": ["handleValueChange"]
    }; }
};
ScPhoneInput.style = ScPhoneInputStyle0;

export { ScPhoneInput as sc_phone_input };

//# sourceMappingURL=sc-phone-input.entry.js.map