'use strict';

Object.defineProperty(exports, '__esModule', { value: true });

const index = require('./index-8acc3c89.js');
const fetch = require('./fetch-d374a251.js');
const addQueryArgs = require('./add-query-args-49dcb630.js');
require('./remove-query-args-b57e8cd3.js');

const scCustomerEditCss = ":host{display:block;position:relative}.customer-edit{display:grid;gap:0.75em}";
const ScCustomerEditStyle0 = scCustomerEditCss;

const ScCustomerEdit = class {
    constructor(hostRef) {
        index.registerInstance(this, hostRef);
        this.heading = undefined;
        this.customer = undefined;
        this.successUrl = undefined;
        this.i18n = undefined;
        this.loading = undefined;
        this.error = undefined;
    }
    async handleSubmit(e) {
        var _a;
        this.loading = true;
        try {
            const { email, first_name, last_name, phone, billing_matches_shipping, shipping_name, shipping_city, 'tax_identifier.number_type': tax_identifier_number_type, 'tax_identifier.number': tax_identifier_number, shipping_country, shipping_line_1, shipping_postal_code, shipping_state, billing_name, billing_city, billing_country, billing_line_1, billing_postal_code, billing_state, } = await e.target.getFormJson();
            this.customer.billing_address = {
                name: billing_name,
                city: billing_city,
                country: billing_country,
                line_1: billing_line_1,
                postal_code: billing_postal_code,
                state: billing_state,
            };
            this.customer.shipping_address = {
                name: shipping_name,
                city: shipping_city,
                country: shipping_country,
                line_1: shipping_line_1,
                postal_code: shipping_postal_code,
                state: shipping_state,
            };
            await fetch.apiFetch({
                path: addQueryArgs.addQueryArgs(`surecart/v1/customers/${(_a = this.customer) === null || _a === void 0 ? void 0 : _a.id}`, { expand: ['tax_identifier'] }),
                method: 'PATCH',
                data: {
                    email,
                    first_name,
                    last_name,
                    phone,
                    billing_matches_shipping: billing_matches_shipping === true || billing_matches_shipping === 'on',
                    shipping_address: this.customer.shipping_address,
                    billing_address: this.customer.billing_address,
                    ...(tax_identifier_number && tax_identifier_number_type
                        ? {
                            tax_identifier: {
                                number: tax_identifier_number,
                                number_type: tax_identifier_number_type,
                            },
                        }
                        : {}),
                },
            });
            if (this.successUrl) {
                window.location.assign(this.successUrl);
            }
            else {
                this.loading = false;
            }
        }
        catch (e) {
            this.error = (e === null || e === void 0 ? void 0 : e.message) || wp.i18n.__('Something went wrong', 'surecart');
            this.loading = false;
        }
    }
    render() {
        var _a, _b, _c, _d, _e, _f, _g, _h, _j, _k, _l, _m, _o, _p, _q, _r;
        return (index.h("sc-dashboard-module", { key: 'fc2a01e2655af832eae0188eebf5f219afd9a439', class: "customer-edit", error: this.error }, index.h("span", { key: 'eb2f50a85feef3ff2cf9bca75dda3db2fbd7ddbe', slot: "heading" }, this.heading || wp.i18n.__('Update Billing Details', 'surecart'), ' ', !((_a = this === null || this === void 0 ? void 0 : this.customer) === null || _a === void 0 ? void 0 : _a.live_mode) && (index.h("sc-tag", { key: '70ee58142547769f0c34661d3d51dce8aa8e8fc0', type: "warning", size: "small" }, wp.i18n.__('Test', 'surecart')))), index.h("sc-card", { key: '1d8a0f9c01219824cca3d87113b1d8b1315bdc68' }, index.h("sc-form", { key: '46da394161424b40d43566c140190993402a751f', onScFormSubmit: e => this.handleSubmit(e) }, index.h("sc-columns", { key: '4d7352b34c2efc69865feb4bd913d0c560b0ce9e', style: { '--sc-column-spacing': 'var(--sc-spacing-medium)' } }, index.h("sc-column", { key: '9b3c5580865c0557d30059562fbe63793c5b546e' }, index.h("sc-input", { key: '458d46ce3ceb0f447ace3a00d911d54a4c16d1cb', label: wp.i18n.__('First Name', 'surecart'), name: "first_name", value: (_b = this.customer) === null || _b === void 0 ? void 0 : _b.first_name })), index.h("sc-column", { key: 'aaa9e1ec4f8cc45f1df57c61053a01c491f27549' }, index.h("sc-input", { key: '785440bae6877973ea81caec34be96a54c8b20f5', label: wp.i18n.__('Last Name', 'surecart'), name: "last_name", value: (_c = this.customer) === null || _c === void 0 ? void 0 : _c.last_name }))), index.h("sc-column", { key: 'ceb011d7237897fd083b48a4d2b871b724141155' }, index.h("sc-phone-input", { key: '43636b1bfa61abc8d1fffe024a25bcd2e0234fea', label: wp.i18n.__('Phone', 'surecart'), name: "phone", value: (_d = this.customer) === null || _d === void 0 ? void 0 : _d.phone })), index.h("sc-flex", { key: 'c8fb6baa5adbd19bf006a4b29930373b74082d2a', style: { '--sc-flex-column-gap': 'var(--sc-spacing-medium)' }, flexDirection: "column" }, index.h("div", { key: 'a6b487ad38c6aba8c6801e1734c4d58b3b63dcce' }, index.h("sc-address", { key: 'ea38822db22e5303cc925ad378df5dfdbd8c5233', label: wp.i18n.__('Shipping Address', 'surecart'), showName: true, address: {
                ...(_e = this.customer) === null || _e === void 0 ? void 0 : _e.shipping_address,
            }, required: false, names: {
                name: 'shipping_name',
                country: 'shipping_country',
                line_1: 'shipping_line_1',
                line_2: 'shipping_line_2',
                city: 'shipping_city',
                postal_code: 'shipping_postal_code',
                state: 'shipping_state',
            }, defaultCountryFields: ((_f = this.i18n) === null || _f === void 0 ? void 0 : _f.defaultCountryFields) || [], countryFields: ((_g = this.i18n) === null || _g === void 0 ? void 0 : _g.countryFields) || [] })), index.h("div", { key: '6b0af7282a092b773274e47976c2eced822659da' }, index.h("sc-checkbox", { key: 'efec6ff52047115297f8ec67854537315357d25e', name: "billing_matches_shipping", checked: (_h = this.customer) === null || _h === void 0 ? void 0 : _h.billing_matches_shipping, onScChange: e => {
                this.customer = {
                    ...this.customer,
                    billing_matches_shipping: e.target.checked,
                };
            }, value: "on" }, wp.i18n.__('Billing address is same as shipping', 'surecart'))), index.h("div", { key: 'a31374770a9da201c4802a674c9c84d5a2b6c78e', style: { display: ((_j = this.customer) === null || _j === void 0 ? void 0 : _j.billing_matches_shipping) ? 'none' : 'block' } }, index.h("sc-address", { key: 'd52a0b9ba7840f623533a45aa98e9b0b77713235', label: wp.i18n.__('Billing Address', 'surecart'), showName: true, address: {
                ...(_k = this.customer) === null || _k === void 0 ? void 0 : _k.billing_address,
            }, names: {
                name: 'billing_name',
                country: 'billing_country',
                line_1: 'billing_line_1',
                line_2: 'billing_line_2',
                city: 'billing_city',
                postal_code: 'billing_postal_code',
                state: 'billing_state',
            }, required: true, defaultCountryFields: ((_l = this.i18n) === null || _l === void 0 ? void 0 : _l.defaultCountryFields) || [], countryFields: ((_m = this.i18n) === null || _m === void 0 ? void 0 : _m.countryFields) || [] })), index.h("sc-tax-id-input", { key: 'c08582893cb644fdcd07e93728a1c78b2d66b670', show: true, number: (_p = (_o = this.customer) === null || _o === void 0 ? void 0 : _o.tax_identifier) === null || _p === void 0 ? void 0 : _p.number, type: (_r = (_q = this.customer) === null || _q === void 0 ? void 0 : _q.tax_identifier) === null || _r === void 0 ? void 0 : _r.number_type })), index.h("div", { key: '0a3d94aab2a00b8ecafa6f15a077dbd47017bfb7' }, index.h("sc-button", { key: '87d72bde353879db1951d8a1247f7a2b0d7fda7d', type: "primary", full: true, submit: true }, wp.i18n.__('Save', 'surecart'))))), this.loading && index.h("sc-block-ui", { key: '946a72ea0cf8ef464bf258b255ac8466c2c2f973', spinner: true })));
    }
};
ScCustomerEdit.style = ScCustomerEditStyle0;

exports.sc_customer_edit = ScCustomerEdit;

//# sourceMappingURL=sc-customer-edit.cjs.entry.js.map