import { r as registerInstance, h } from './index-745b6bec.js';
import { i as intervalString } from './price-af9f0dbf.js';
import { a as addQueryArgs } from './add-query-args-0e2a8393.js';
import './currency-a0c9bff4.js';

const scSubscriptionAdHocConfirmCss = ":host{display:block}";
const ScSubscriptionAdHocConfirmStyle0 = scSubscriptionAdHocConfirmCss;

const ScSubscriptionAdHocConfirm = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.heading = undefined;
        this.price = undefined;
        this.busy = false;
    }
    async handleSubmit(e) {
        const { ad_hoc_amount } = await e.target.getFormJson();
        this.busy = true;
        return window.location.assign(addQueryArgs(window.location.href, {
            action: 'confirm',
            ad_hoc_amount,
        }));
    }
    render() {
        return (h("sc-dashboard-module", { key: '450b43a56ed13e55be235ce595a0c37910c4f271', heading: this.heading || wp.i18n.__('Enter An Amount', 'surecart'), class: "subscription-switch" }, h("sc-card", { key: '20da7c86030bc61f579793d7bd50f4c7a0279dde' }, h("sc-form", { key: '452af4e15286f2bc762231a83a61cc403e041489', onScSubmit: e => this.handleSubmit(e) }, h("sc-price-input", { key: 'c47e270663f11f837741f30f91bf58ac5fbc7ce1', label: "Amount", name: "ad_hoc_amount", autofocus: true, required: true }, h("span", { key: '3548ca68906a2a1fd07eacfcf614bcb1909a7290', slot: "suffix", style: { opacity: '0.75' } }, intervalString(this.price))), h("sc-button", { key: '8dff737f24e608dea10e2929b9bbd4b36738da75', type: "primary", full: true, submit: true, loading: this.busy }, wp.i18n.__('Next', 'surecart'), " ", h("sc-icon", { key: '84cb7c105c518e6ec64a0bbb8ed6ab8a5ddf9274', name: "arrow-right", slot: "suffix" })))), this.busy && h("sc-block-ui", { key: 'e54c74a6e24feb59e11be0b025c19cffa946ac73', style: { zIndex: '9' } })));
    }
};
ScSubscriptionAdHocConfirm.style = ScSubscriptionAdHocConfirmStyle0;

export { ScSubscriptionAdHocConfirm as sc_subscription_ad_hoc_confirm };

//# sourceMappingURL=sc-subscription-ad-hoc-confirm.entry.js.map