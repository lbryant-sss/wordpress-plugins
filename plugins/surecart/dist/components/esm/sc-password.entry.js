import { r as registerInstance, h } from './index-745b6bec.js';
import { s as speak } from './index-c5a96d53.js';

const scPasswordCss = ":host{display:block}.password{display:grid;gap:var(--sc-form-row-spacing, 0.75em)}.password__hint{padding-top:0.36rem;color:red}";
const ScPasswordStyle0 = scPasswordCss;

let showHintTimer, showVerificationTimer;
const ScPassword = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.size = 'medium';
        this.value = '';
        this.pill = false;
        this.label = undefined;
        this.showLabel = true;
        this.help = '';
        this.placeholder = undefined;
        this.disabled = false;
        this.readonly = false;
        this.required = false;
        this.autofocus = undefined;
        this.confirmation = false;
        this.name = 'password';
        this.confirmationLabel = undefined;
        this.confirmationPlaceholder = undefined;
        this.confirmationHelp = undefined;
        this.enableValidation = true;
        this.hintText = undefined;
        this.verifyText = undefined;
    }
    /** Sets focus on the input. */
    async triggerFocus(options) {
        return this.input.triggerFocus(options);
    }
    async reportValidity() {
        var _a, _b, _c, _d, _e, _f, _g;
        (_b = (_a = this.input) === null || _a === void 0 ? void 0 : _a.setCustomValidity) === null || _b === void 0 ? void 0 : _b.call(_a, '');
        (_d = (_c = this.confirmInput) === null || _c === void 0 ? void 0 : _c.setCustomValidity) === null || _d === void 0 ? void 0 : _d.call(_c, '');
        // confirmation is enabled.
        if (this.confirmation) {
            if (((_e = this.confirmInput) === null || _e === void 0 ? void 0 : _e.value) && ((_f = this.input) === null || _f === void 0 ? void 0 : _f.value) !== ((_g = this.confirmInput) === null || _g === void 0 ? void 0 : _g.value)) {
                this.confirmInput.setCustomValidity(wp.i18n.__('Password does not match.', 'surecart'));
                speak(wp.i18n.__('Password does not match.', 'surecart'), 'assertive');
            }
        }
        // hint text is not empty.
        if (!!this.hintText) {
            this.input.setCustomValidity(wp.i18n.__(this.hintText, 'surecart'));
        }
        const valid = await this.input.reportValidity();
        if (!valid) {
            return false;
        }
        if (this.confirmInput) {
            return this.confirmInput.reportValidity();
        }
        return valid;
    }
    /** Handle password verification. */
    handleVerification() {
        clearTimeout(showVerificationTimer);
        // show hint text after some delay
        showVerificationTimer = setTimeout(() => {
            this.verifyPassword();
        }, 500);
    }
    /** Handle password validation. */
    handleValidate() {
        this.handleVerification();
        // clear existing timeout
        clearTimeout(showHintTimer);
        // show hint text after some delay
        showHintTimer = setTimeout(() => {
            this.validatePassword();
        }, 500);
    }
    /** Validate the password input. */
    validatePassword() {
        var _a, _b, _c;
        if (!this.enableValidation)
            return;
        // nothing entered.
        if (((_a = this.input) === null || _a === void 0 ? void 0 : _a.value.trim().length) === 0) {
            this.hintText = '';
            return;
        }
        // must be at least 6 characters.
        if (((_b = this.input) === null || _b === void 0 ? void 0 : _b.value.trim().length) < 6) {
            return (this.hintText = wp.i18n.__('The password must be at least 6 characters in length.', 'surecart'));
        }
        // must contain a special charater.
        const regex = /[-'`~!#*$@_%+=.,^&(){}[\]|;:”<>?\\]/;
        if (!regex.test((_c = this.input) === null || _c === void 0 ? void 0 : _c.value)) {
            return (this.hintText = wp.i18n.__('Passwords must contain a special character.', 'surecart'));
        }
        this.hintText = '';
    }
    /** Verify the password confirmation. */
    verifyPassword() {
        var _a, _b, _c, _d, _e, _f, _g;
        if (((_a = this.confirmInput) === null || _a === void 0 ? void 0 : _a.value) && ((_b = this.input) === null || _b === void 0 ? void 0 : _b.value) !== ((_c = this.confirmInput) === null || _c === void 0 ? void 0 : _c.value)) {
            this.verifyText = wp.i18n.__('Password does not match.', 'surecart');
            speak(this.verifyText, 'assertive');
            return;
        }
        if (!!((_d = this.input) === null || _d === void 0 ? void 0 : _d.value) && !!((_e = this.confirmInput) === null || _e === void 0 ? void 0 : _e.value) && ((_f = this.input) === null || _f === void 0 ? void 0 : _f.value) === ((_g = this.confirmInput) === null || _g === void 0 ? void 0 : _g.value)) {
            speak(wp.i18n.__('Password is matched.', 'surecart'), 'assertive');
        }
        this.verifyText = '';
    }
    handleHintTextChange() {
        speak(this.hintText, 'assertive');
    }
    render() {
        var _a;
        return (h("div", { key: '5dc92980dc16fa7331b5fd3d18ec271e7120dd1b', class: "password" }, h("div", { key: 'd8715a1d7f8ce722ec3faea3e00c3455144012b6' }, h("sc-input", { key: 'f7f5f1f2b3b16b653127b1e47f8f3643488e5016', ref: el => (this.input = el), label: this.label, help: this.help, autofocus: this.autofocus, placeholder: this.placeholder, showLabel: this.showLabel, size: this.size ? this.size : 'medium', type: "password", name: "password", value: this.value, required: this.required, disabled: this.disabled, onScInput: () => this.handleValidate() }), !!this.hintText && h("small", { key: '01ac00189b62ce8e6e85c81bb27d118541517f44', class: "password__hint" }, this.hintText)), this.confirmation && (h("div", { key: '7e82a44b8e7985a37c21fbcddff21fadb0c7ce69' }, h("sc-input", { key: 'e765ade791105217214c0b8e3a92fbbacca4daa8', ref: el => (this.confirmInput = el), label: (_a = this.confirmationLabel) !== null && _a !== void 0 ? _a : wp.i18n.__('Confirm Password', 'surecart'), help: this.confirmationHelp, placeholder: this.confirmationPlaceholder, size: this.size ? this.size : 'medium', type: "password", value: this.value, onScInput: () => this.handleVerification(), required: this.required, disabled: this.disabled }), !!this.verifyText && h("small", { key: 'afc2d328972b6965791fabb09d166206fdb56324', class: "password__hint" }, this.verifyText)))));
    }
    static get watchers() { return {
        "hintText": ["handleHintTextChange"]
    }; }
};
ScPassword.style = ScPasswordStyle0;

export { ScPassword as sc_password };

//# sourceMappingURL=sc-password.entry.js.map