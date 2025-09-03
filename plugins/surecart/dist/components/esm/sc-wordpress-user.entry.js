import { r as registerInstance, h, F as Fragment } from './index-745b6bec.js';
import { a as addQueryArgs } from './add-query-args-0e2a8393.js';

const scWordpressUserCss = ":host{display:block;position:relative}.customer-details{display:grid;gap:0.75em}";
const ScWordpressUserStyle0 = scWordpressUserCss;

const ScWordPressUser = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.heading = undefined;
        this.user = undefined;
    }
    renderContent() {
        var _a, _b, _c, _d, _e, _f, _g, _h;
        if (!this.user) {
            return this.renderEmpty();
        }
        return (h(Fragment, null, !!((_a = this === null || this === void 0 ? void 0 : this.user) === null || _a === void 0 ? void 0 : _a.display_name) && (h("sc-stacked-list-row", { style: { '--columns': '3' }, mobileSize: 480 }, h("div", null, h("strong", null, wp.i18n.__('Display Name', 'surecart'))), h("div", null, (_b = this.user) === null || _b === void 0 ? void 0 : _b.display_name), h("div", null))), !!((_c = this === null || this === void 0 ? void 0 : this.user) === null || _c === void 0 ? void 0 : _c.email) && (h("sc-stacked-list-row", { style: { '--columns': '3' }, mobileSize: 480 }, h("div", null, h("strong", null, wp.i18n.__('Account Email', 'surecart'))), h("div", null, (_d = this.user) === null || _d === void 0 ? void 0 : _d.email), h("div", null))), !!((_e = this === null || this === void 0 ? void 0 : this.user) === null || _e === void 0 ? void 0 : _e.first_name) && (h("sc-stacked-list-row", { style: { '--columns': '3' }, mobileSize: 480 }, h("div", null, h("strong", null, wp.i18n.__('First Name', 'surecart'))), h("div", null, (_f = this.user) === null || _f === void 0 ? void 0 : _f.first_name), h("div", null))), !!((_g = this === null || this === void 0 ? void 0 : this.user) === null || _g === void 0 ? void 0 : _g.last_name) && (h("sc-stacked-list-row", { style: { '--columns': '3' }, mobileSize: 480 }, h("div", null, h("strong", null, wp.i18n.__('Last Name', 'surecart'))), h("div", null, (_h = this.user) === null || _h === void 0 ? void 0 : _h.last_name), h("div", null)))));
    }
    renderEmpty() {
        return h("slot", { name: "empty" }, wp.i18n.__('User not found.', 'surecart'));
    }
    render() {
        return (h("sc-dashboard-module", { key: '9cf7d7aeee30a0be1d72ca2e20bdfd2b5285b370', class: "customer-details" }, h("span", { key: '8d28975d9704a0470acaacce9bf38dc3dfa4895c', slot: "heading" }, this.heading || wp.i18n.__('Account Details', 'surecart'), " "), h("sc-button", { key: 'cd983f32a7674015b7c96416da22352a554dcfa7', type: "link", href: addQueryArgs(window.location.href, {
                action: 'edit',
                model: 'user',
            }), slot: "end" }, h("sc-icon", { key: 'd956cb8d81189386622860075d2c5e326151a159', name: "edit-3", slot: "prefix" }), wp.i18n.__('Update', 'surecart')), h("sc-card", { key: '85215257135cbeb9f183ad0ee6601c6cd6f18918', "no-padding": true }, h("sc-stacked-list", { key: '6eb15920d0e416060c19003a17f0594c12cf0326' }, this.renderContent()))));
    }
};
ScWordPressUser.style = ScWordpressUserStyle0;

export { ScWordPressUser as sc_wordpress_user };

//# sourceMappingURL=sc-wordpress-user.entry.js.map