import { r as registerInstance, h, a as getElement } from './index-745b6bec.js';
import { a as apiFetch } from './fetch-bc141774.js';
import { o as onFirstVisible } from './lazy-deb42890.js';
import { a as addQueryArgs } from './add-query-args-0e2a8393.js';
import './remove-query-args-938c53ea.js';

const scDashboardCustomerDetailsCss = ":host{display:block;position:relative}.customer-details{display:grid;gap:0.75em}";
const ScDashboardCustomerDetailsStyle0 = scDashboardCustomerDetailsCss;

const ScDashboardCustomerDetails = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.customerId = undefined;
        this.heading = undefined;
        this.customer = undefined;
        this.loading = undefined;
        this.error = undefined;
    }
    componentWillLoad() {
        onFirstVisible(this.el, () => {
            this.fetch();
        });
    }
    async fetch() {
        if ('' === this.customerId) {
            return;
        }
        try {
            this.loading = true;
            this.customer = (await await apiFetch({
                path: addQueryArgs(`surecart/v1/customers/${this.customerId}`, {
                    expand: ['shipping_address', 'billing_address', 'tax_identifier'],
                }),
            }));
        }
        catch (e) {
            if (e === null || e === void 0 ? void 0 : e.message) {
                this.error = e.message;
            }
            else {
                this.error = wp.i18n.__('Something went wrong', 'surecart');
            }
            console.error(this.error);
        }
        finally {
            this.loading = false;
        }
    }
    render() {
        return (h("sc-customer-details", { key: 'daa6010b4f0b5ace8987b50dfdaa0818df7fd0f8', exportparts: "base, heading, heading-text, heading-title, heading-description, error__base, error__icon, error__text, error__title, error__message, test-tag__base, test-tag__content, button__base, button__label, button__prefix", customer: this.customer, loading: this.loading, error: this.error, heading: this.heading, "edit-link": addQueryArgs(window.location.href, {
                action: 'edit',
                model: 'customer',
                id: this.customerId,
            }) }));
    }
    get el() { return getElement(this); }
};
ScDashboardCustomerDetails.style = ScDashboardCustomerDetailsStyle0;

export { ScDashboardCustomerDetails as sc_dashboard_customer_details };

//# sourceMappingURL=sc-dashboard-customer-details.entry.js.map