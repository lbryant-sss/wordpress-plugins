'use strict';

Object.defineProperty(exports, '__esModule', { value: true });

const index = require('./index-8acc3c89.js');
const fetch = require('./fetch-d644cebd.js');
const lazy = require('./lazy-2b509fa7.js');
const addQueryArgs = require('./add-query-args-49dcb630.js');
require('./remove-query-args-b57e8cd3.js');

const scInvoicesListCss = ":host{display:block}.orders-list{display:grid;gap:0.75em}.orders-list__heading{display:flex;flex-wrap:wrap;align-items:flex-end;justify-content:space-between}.orders-list__title{font-size:var(--sc-font-size-x-large);font-weight:var(--sc-font-weight-bold);line-height:var(--sc-line-height-dense)}.orders-list a{text-decoration:none;font-weight:var(--sc-font-weight-semibold);display:inline-flex;align-items:center;gap:0.25em;color:var(--sc-color-primary-500)}.order__row{color:var(--sc-color-gray-800);text-decoration:none;display:grid;align-items:center;justify-content:space-between;gap:0;grid-template-columns:1fr 1fr 1fr auto;margin:0;padding:var(--sc-spacing-small) var(--sc-spacing-large)}.order__row:not(:last-child){border-bottom:1px solid var(--sc-color-gray-200)}.order__row:hover{background:var(--sc-color-gray-50)}.order__date{font-weight:var(--sc-font-weight-semibold)}";
const ScInvoicesListStyle0 = scInvoicesListCss;

const ScInvoicesList = class {
    constructor(hostRef) {
        index.registerInstance(this, hostRef);
        this.query = {
            page: 1,
            per_page: 10,
        };
        this.allLink = undefined;
        this.heading = undefined;
        this.isCustomer = undefined;
        this.invoices = [];
        this.loading = undefined;
        this.busy = undefined;
        this.error = undefined;
        this.pagination = {
            total: 0,
            total_pages: 0,
        };
    }
    /** Only fetch if visible */
    componentWillLoad() {
        lazy.onFirstVisible(this.el, () => {
            this.initialFetch();
        });
    }
    async initialFetch() {
        try {
            this.loading = true;
            await this.getInvoices();
        }
        catch (e) {
            console.error(this.error);
            this.error = (e === null || e === void 0 ? void 0 : e.message) || wp.i18n.__('Something went wrong', 'surecart');
        }
        finally {
            this.loading = false;
        }
    }
    async fetchInvoices() {
        try {
            this.busy = true;
            await this.getInvoices();
        }
        catch (e) {
            console.error(this.error);
            this.error = (e === null || e === void 0 ? void 0 : e.message) || wp.i18n.__('Something went wrong', 'surecart');
        }
        finally {
            this.busy = false;
        }
    }
    /** Get all invoices */
    async getInvoices() {
        if (!this.isCustomer) {
            return;
        }
        const response = (await await fetch.apiFetch({
            path: addQueryArgs.addQueryArgs(`surecart/v1/invoices/`, {
                expand: ['checkout'],
                ...this.query,
            }),
            parse: false,
        }));
        this.pagination = {
            total: parseInt(response.headers.get('X-WP-Total')),
            total_pages: parseInt(response.headers.get('X-WP-TotalPages')),
        };
        this.invoices = (await response.json());
        return this.invoices;
    }
    nextPage() {
        this.query.page = this.query.page + 1;
        this.fetchInvoices();
    }
    prevPage() {
        this.query.page = this.query.page - 1;
        this.fetchInvoices();
    }
    renderLoading() {
        return (index.h("sc-card", { noPadding: true }, index.h("sc-stacked-list", null, index.h("sc-stacked-list-row", { style: { '--columns': '4' }, "mobile-size": 500 }, [...Array(4)].map(() => (index.h("sc-skeleton", { style: { width: '100px', display: 'inline-block' } })))))));
    }
    renderEmpty() {
        return (index.h("div", null, index.h("sc-divider", { style: { '--spacing': '0' } }), index.h("slot", { name: "empty" }, index.h("sc-empty", { icon: "shopping-bag" }, wp.i18n.__("You don't have any invoices.", 'surecart')))));
    }
    getInvoiceRedirectUrl(invoice) {
        var _a, _b, _c;
        // if it's open, redirect to checkout for payment.
        if (invoice.status === 'open') {
            return `${window.scData.pages.checkout}?checkout_id=${(_a = invoice === null || invoice === void 0 ? void 0 : invoice.checkout) === null || _a === void 0 ? void 0 : _a.id}`;
        }
        // else it's paid(as we're fetching only open/paid), redirect to order detail page.
        return addQueryArgs.addQueryArgs(window.location.href, {
            action: 'show',
            model: 'order',
            id: (_c = (_b = invoice === null || invoice === void 0 ? void 0 : invoice.checkout) === null || _b === void 0 ? void 0 : _b.order) === null || _c === void 0 ? void 0 : _c.id,
        });
    }
    renderList() {
        return this.invoices.map(invoice => {
            const { checkout, due_date_date } = invoice;
            if (!checkout)
                return null;
            const { amount_due_display_amount } = checkout;
            return (index.h("sc-stacked-list-row", { href: this.getInvoiceRedirectUrl(invoice), style: { '--columns': '4' }, "mobile-size": 500 }, index.h("div", null, "#", invoice === null || invoice === void 0 ? void 0 :
                invoice.order_number), index.h("div", null, due_date_date && (invoice === null || invoice === void 0 ? void 0 : invoice.status) === 'open' ? wp.i18n.sprintf(wp.i18n.__('Due %s', 'surecart'), due_date_date) : '—'), index.h("div", { class: "invoices-list__status" }, index.h("sc-invoice-status-badge", { status: invoice === null || invoice === void 0 ? void 0 : invoice.status })), index.h("div", null, amount_due_display_amount)));
        });
    }
    renderContent() {
        var _a;
        if (this.loading) {
            return this.renderLoading();
        }
        if (((_a = this.invoices) === null || _a === void 0 ? void 0 : _a.length) === 0) {
            return this.renderEmpty();
        }
        return (index.h("sc-card", { "no-padding": true }, index.h("sc-stacked-list", null, this.renderList())));
    }
    render() {
        var _a, _b;
        return (index.h("sc-dashboard-module", { key: '48903841bd51cc6d15f6137974a3aca51d2a64f2', class: "invoices-list", error: this.error }, index.h("span", { key: '132aba486b307cf53361f2cd99e771eeff585866', slot: "heading" }, index.h("slot", { key: '49319d28dde268ad2e88f123672bedd268f9d606', name: "heading" }, this.heading || wp.i18n.__('Invoices', 'surecart'))), !!this.allLink && !!((_a = this.invoices) === null || _a === void 0 ? void 0 : _a.length) && (index.h("sc-button", { key: '6766e31aa8028421210ad47f848eb351153d5ab0', type: "link", href: this.allLink, slot: "end", "aria-label": wp.i18n.sprintf(wp.i18n.__('View all %s', 'surecart'), this.heading || wp.i18n.__('Invoices', 'surecart')) }, wp.i18n.__('View all', 'surecart'), index.h("sc-icon", { key: '9e2e4e728a577df926ffb7da4b4f1b08ba959b17', "aria-hidden": "true", name: "chevron-right", slot: "suffix" }))), this.renderContent(), !this.allLink && (index.h("sc-pagination", { key: '0bf3d371c4fb8f0625005545d8050c2a7e2e877b', page: this.query.page, perPage: this.query.per_page, total: this.pagination.total, totalPages: this.pagination.total_pages, totalShowing: (_b = this === null || this === void 0 ? void 0 : this.invoices) === null || _b === void 0 ? void 0 : _b.length, onScNextPage: () => this.nextPage(), onScPrevPage: () => this.prevPage() })), this.busy && index.h("sc-block-ui", { key: '86adde80321aa866904551e4ed2efed45d3cf0f6' })));
    }
    get el() { return index.getElement(this); }
};
ScInvoicesList.style = ScInvoicesListStyle0;

exports.sc_invoices_list = ScInvoicesList;

//# sourceMappingURL=sc-invoices-list.cjs.entry.js.map