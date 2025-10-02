import { r as registerInstance, h, H as Host } from './index-745b6bec.js';
import './watchers-c215fc6b.js';
import { s as state } from './store-4bc13420.js';
import { a as isBusy } from './getters-1899e179.js';
import { t as trackOffer, p as preview } from './mutations-b0435825.js';
import './watchers-fbf07f32.js';
import './index-06061d4e.js';
import './google-dd89f242.js';
import './currency-a0c9bff4.js';
import './google-a86aa761.js';
import './utils-cd1431df.js';
import './util-50af2a83.js';
import './index-c5a96d53.js';
import './add-query-args-0e2a8393.js';
import './fetch-bc141774.js';
import './remove-query-args-938c53ea.js';
import './mutations-ed6d0770.js';

const scUpsellCss = ":host{display:block}.confirm__icon{margin-bottom:var(--sc-spacing-medium);display:flex;justify-content:center}.confirm__icon-container{background:var(--sc-color-primary-500);width:55px;height:55px;border-radius:999999px;display:flex;align-items:center;justify-content:center;font-size:26px;line-height:1;color:white}";
const ScUpsellStyle0 = scUpsellCss;

const ScUpsell = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
    }
    componentWillLoad() {
        trackOffer();
        preview();
    }
    render() {
        var _a, _b, _c, _d, _e, _f, _g, _h, _j;
        const manualPaymentMethod = (_a = state.checkout) === null || _a === void 0 ? void 0 : _a.manual_payment_method;
        return (h(Host, { key: '20bce298c7b9408c722334096903abb9cb59229d' }, h("slot", { key: 'fa9cacb7af0ae3120afbd7f289df7f8f539d00af' }), isBusy() && h("sc-block-ui", { key: '02d6413900e28b4b31e1fd1f3ca361e193963b65', style: { 'z-index': '30', '--sc-block-ui-position': 'fixed' } }), h("sc-dialog", { key: 'd1050d52b2635ed0ec48c4cb03319f9516996604', open: state.loading === 'complete', style: { '--body-spacing': 'var(--sc-spacing-xxx-large)' }, noHeader: true, onScRequestClose: e => e.preventDefault() }, h("div", { key: 'd950bd5caa790b421a4bd9b44a842f96af1ffa4f', class: "confirm__icon" }, h("div", { key: 'fb53fc6bfc865c18be6a81498ba6c5eaf9048aaf', class: "confirm__icon-container" }, h("sc-icon", { key: 'cd81a2a7b9b76e63233d2c64f51e9540fa65c39b', name: "check" }))), h("sc-dashboard-module", { key: 'f0347549bc1c94a5ec5d5e84f0aca02f73db366f', heading: ((_c = (_b = state === null || state === void 0 ? void 0 : state.text) === null || _b === void 0 ? void 0 : _b.success) === null || _c === void 0 ? void 0 : _c.title) || wp.i18n.__('Thank you!', 'surecart'), style: { '--sc-dashboard-module-spacing': 'var(--sc-spacing-x-large)', 'textAlign': 'center' } }, h("span", { key: 'c71a0eed59d771a96081f0f5dbe565b73f65b347', slot: "description" }, ((_e = (_d = state === null || state === void 0 ? void 0 : state.text) === null || _d === void 0 ? void 0 : _d.success) === null || _e === void 0 ? void 0 : _e.description) || wp.i18n.__('Your purchase was successful. A receipt is on its way to your inbox.', 'surecart')), !!(manualPaymentMethod === null || manualPaymentMethod === void 0 ? void 0 : manualPaymentMethod.name) && !!(manualPaymentMethod === null || manualPaymentMethod === void 0 ? void 0 : manualPaymentMethod.instructions) && (h("sc-alert", { key: '6ac05dbc57c36b4237067b6f825b81e3d7daec5c', type: "info", open: true, style: { 'text-align': 'left' } }, h("span", { key: 'c4da44791c31c54f7553f98833ea54e9c51e3726', slot: "title" }, manualPaymentMethod === null || manualPaymentMethod === void 0 ? void 0 : manualPaymentMethod.name), h("div", { key: 'aa7ffb085176b05b704b3e7cc8882bf99d507ae7', innerHTML: manualPaymentMethod === null || manualPaymentMethod === void 0 ? void 0 : manualPaymentMethod.instructions }))), h("sc-button", { key: 'f0d51dc6ed1a7b41aecde1fe26b8d3337c4f8004', href: (_g = (_f = window === null || window === void 0 ? void 0 : window.scData) === null || _f === void 0 ? void 0 : _f.pages) === null || _g === void 0 ? void 0 : _g.dashboard, size: "large", type: "primary", autofocus: true }, ((_j = (_h = state === null || state === void 0 ? void 0 : state.text) === null || _h === void 0 ? void 0 : _h.success) === null || _j === void 0 ? void 0 : _j.button) || wp.i18n.__('Continue', 'surecart'), h("sc-icon", { key: '12943ba8e153a0f9b535fa8f1e4079068f4aec53', name: "arrow-right", slot: "suffix" }))))));
    }
};
ScUpsell.style = ScUpsellStyle0;

export { ScUpsell as sc_upsell };

//# sourceMappingURL=sc-upsell.entry.js.map