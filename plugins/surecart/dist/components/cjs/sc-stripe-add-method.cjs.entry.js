'use strict';

Object.defineProperty(exports, '__esModule', { value: true });

const index = require('./index-8acc3c89.js');
const pure = require('./pure-bd6f0a6e.js');
const fetch = require('./fetch-d644cebd.js');
const addQueryArgs = require('./add-query-args-49dcb630.js');
require('./remove-query-args-b57e8cd3.js');

const scStripeAddMethodCss = "sc-stripe-add-method{display:block}sc-stripe-add-method [hidden]{display:none}.loader{display:grid;height:128px;gap:2em}.loader__row{display:flex;align-items:flex-start;justify-content:space-between;gap:1em}.loader__details{display:grid;gap:0.5em}";
const ScStripeAddMethodStyle0 = scStripeAddMethodCss;

const ScStripeAddMethod = class {
    constructor(hostRef) {
        index.registerInstance(this, hostRef);
        this.liveMode = true;
        this.customerId = undefined;
        this.successUrl = undefined;
        this.loading = undefined;
        this.loaded = undefined;
        this.error = undefined;
        this.paymentIntent = undefined;
    }
    componentWillLoad() {
        this.createPaymentIntent();
    }
    async handlePaymentIntentCreate() {
        var _a, _b, _c, _d, _e, _f, _g, _h, _j, _k, _l, _m, _o, _p, _q, _r, _s, _t;
        // we need this data.
        if (!((_c = (_b = (_a = this.paymentIntent) === null || _a === void 0 ? void 0 : _a.processor_data) === null || _b === void 0 ? void 0 : _b.stripe) === null || _c === void 0 ? void 0 : _c.publishable_key) || !((_f = (_e = (_d = this.paymentIntent) === null || _d === void 0 ? void 0 : _d.processor_data) === null || _e === void 0 ? void 0 : _e.stripe) === null || _f === void 0 ? void 0 : _f.account_id))
            return;
        // check if stripe has been initialized
        if (!this.stripe) {
            try {
                this.stripe = await pure.pure.loadStripe((_j = (_h = (_g = this.paymentIntent) === null || _g === void 0 ? void 0 : _g.processor_data) === null || _h === void 0 ? void 0 : _h.stripe) === null || _j === void 0 ? void 0 : _j.publishable_key, { stripeAccount: (_m = (_l = (_k = this.paymentIntent) === null || _k === void 0 ? void 0 : _k.processor_data) === null || _l === void 0 ? void 0 : _l.stripe) === null || _m === void 0 ? void 0 : _m.account_id });
            }
            catch (e) {
                this.error = (e === null || e === void 0 ? void 0 : e.message) || wp.i18n.__('Stripe could not be loaded', 'surecart');
                // don't continue.
                return;
            }
        }
        // load the element.
        // we need a stripe instance and client secret.
        if (!((_q = (_p = (_o = this.paymentIntent) === null || _o === void 0 ? void 0 : _o.processor_data) === null || _p === void 0 ? void 0 : _p.stripe) === null || _q === void 0 ? void 0 : _q.client_secret) || !this.container) {
            console.warn('do not have client secret or container');
            return;
        }
        // get the computed styles.
        const styles = getComputedStyle(document.body);
        // we have what we need, load elements.
        this.elements = this.stripe.elements({
            clientSecret: (_t = (_s = (_r = this.paymentIntent) === null || _r === void 0 ? void 0 : _r.processor_data) === null || _s === void 0 ? void 0 : _s.stripe) === null || _t === void 0 ? void 0 : _t.client_secret,
            appearance: {
                variables: {
                    colorPrimary: styles.getPropertyValue('--sc-color-primary-500'),
                    colorText: styles.getPropertyValue('--sc-input-label-color'),
                    borderRadius: styles.getPropertyValue('--sc-input-border-radius-medium'),
                    colorBackground: styles.getPropertyValue('--sc-input-background-color'),
                    fontSizeBase: styles.getPropertyValue('--sc-input-font-size-medium'),
                },
                rules: {
                    '.Input': {
                        border: styles.getPropertyValue('--sc-input-border'),
                    },
                    '.Input::placeholder': {
                        color: styles.getPropertyValue('--sc-input-placeholder-color'),
                    },
                },
            },
        });
        // create the payment element.
        this.elements
            .create('payment', {
            wallets: {
                applePay: 'never',
                googlePay: 'never',
            },
        })
            .mount('.sc-payment-element-container');
        this.element = this.elements.getElement('payment');
        this.element.on('ready', () => (this.loaded = true));
    }
    async createPaymentIntent() {
        try {
            this.loading = true;
            this.error = '';
            this.paymentIntent = await fetch.apiFetch({
                method: 'POST',
                path: 'surecart/v1/payment_intents',
                data: {
                    processor_type: 'stripe',
                    live_mode: this.liveMode,
                    customer_id: this.customerId,
                    refresh_status: true,
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
    /**
     * Handle form submission.
     */
    async handleSubmit(e) {
        var _a;
        e.preventDefault();
        this.loading = true;
        try {
            const confirmed = await this.stripe.confirmSetup({
                elements: this.elements,
                confirmParams: {
                    return_url: addQueryArgs.addQueryArgs(this.successUrl, {
                        payment_intent: (_a = this.paymentIntent) === null || _a === void 0 ? void 0 : _a.id,
                    }),
                },
                redirect: 'always',
            });
            if (confirmed === null || confirmed === void 0 ? void 0 : confirmed.error) {
                this.error = confirmed.error.message;
                throw confirmed.error;
            }
        }
        catch (e) {
            console.error(e);
            this.error = (e === null || e === void 0 ? void 0 : e.message) || wp.i18n.__('Something went wrong', 'surecart');
            this.loading = false;
        }
    }
    render() {
        return (index.h("sc-form", { key: '70a43181d1f9a3d74e139378b19c2df26e702373', onScFormSubmit: e => this.handleSubmit(e) }, this.error && (index.h("sc-alert", { key: 'deaa073654f29697ff07482d2b55b1c70a0e3afa', open: !!this.error, type: "danger" }, index.h("span", { key: '9c57a212a53d2d73853b07d63ee7677a83380b42', slot: "title" }, wp.i18n.__('Error', 'surecart')), this.error)), index.h("div", { key: '7ef79871baf6df33b9f39d0865c4de30a865af9c', class: "loader", hidden: this.loaded }, index.h("div", { key: '4483b4a52b46069070032c7f422b7c45c266ec61', class: "loader__row" }, index.h("div", { key: 'd5c4a660254e6b9e7e325e0fc4f355389d3b5c57', style: { width: '50%' } }, index.h("sc-skeleton", { key: 'a1178b7a09dc7eefe5a3d095cd9e78d5de60c7f5', style: { width: '50%', marginBottom: '0.5em' } }), index.h("sc-skeleton", { key: 'a587c572b86ca6f783450896a1dfe07d119e0252' })), index.h("div", { key: '91d6a3de64329ae52eac30b6139ab4a773ed4e5e', style: { flex: '1' } }, index.h("sc-skeleton", { key: '046d4bd1d2a7f3a17a7103955fc8a00ed932476b', style: { width: '50%', marginBottom: '0.5em' } }), index.h("sc-skeleton", { key: 'f5a6a7f2e393125e186a2c91bbddf7e331c36a60' })), index.h("div", { key: '92aec2540b5131f91b426b6f57c582614ac769af', style: { flex: '1' } }, index.h("sc-skeleton", { key: '489307c622a568c5c925a7f4a8e45047cad7a7e0', style: { width: '50%', marginBottom: '0.5em' } }), index.h("sc-skeleton", { key: '3cd03fcb24e967efb3b2fde948cdd11644ec7fbc' }))), index.h("div", { key: 'f43b727dde7596ddcbeed04a77d8dfbb1d77ed58', class: "loader__details" }, index.h("sc-skeleton", { key: '7414c230d19158698e42e0f80f90b622afd85832', style: { height: '1rem' } }), index.h("sc-skeleton", { key: '76a941b76f8ceeac3a40cd3d6904a007e30cab71', style: { height: '1rem', width: '30%' } }))), index.h("div", { key: '99007446f44222102e32a53811bfc54292149ca7', hidden: !this.loaded, class: "sc-payment-element-container", ref: el => (this.container = el) }), index.h("sc-button", { key: 'e2e915414d0cc164c51f54c8f54cb60e8e65aa1c', type: "primary", submit: true, full: true, loading: this.loading }, wp.i18n.__('Save Payment Method', 'surecart'))));
    }
    static get watchers() { return {
        "paymentIntent": ["handlePaymentIntentCreate"]
    }; }
};
ScStripeAddMethod.style = ScStripeAddMethodStyle0;

exports.sc_stripe_add_method = ScStripeAddMethod;

//# sourceMappingURL=sc-stripe-add-method.cjs.entry.js.map