import { r as registerInstance, h } from './index-745b6bec.js';

const scFeatureDemoBannerCss = ".sc-banner{background-color:var(--sc-color-brand-primary);color:white;display:flex;align-items:center;justify-content:center}.sc-banner>p{font-size:14px;line-height:1;margin:var(--sc-spacing-small)}.sc-banner>p a{color:inherit;font-weight:600;margin-left:10px;display:inline-flex;align-items:center;gap:8px;text-decoration:none;border-bottom:1px solid;padding-bottom:2px}";
const ScFeatureDemoBannerStyle0 = scFeatureDemoBannerCss;

const ScFeatureDemoBanner = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.url = 'https://app.surecart.com/plans';
        this.buttonText = wp.i18n.__('Upgrade Your Plan', 'surecart');
    }
    render() {
        return (h("div", { key: 'cf8ed462173f6d52090b45b653547617e10662cf', class: { 'sc-banner': true } }, h("p", { key: '6c49e37541e061c530c454d6ac6e78a6733e2038' }, h("slot", { key: '2072be7ff550af45893123b0d8ade6f888c8eaae' }, wp.i18n.__('This is a feature demo. In order to use it, you must upgrade your plan.', 'surecart')), h("a", { key: '24844b8301c33fe0bfc09fdae3cfd099b03b8885', href: this.url, target: "_blank" }, h("slot", { key: 'a575f07bd841c20e4a3361cdf5fc289627e5f24c', name: "link" }, this.buttonText, " ", h("sc-icon", { key: 'd12ede7ee4ce2860109fb09fb7cba7c690120647', name: "arrow-right" }))))));
    }
};
ScFeatureDemoBanner.style = ScFeatureDemoBannerStyle0;

export { ScFeatureDemoBanner as sc_feature_demo_banner };

//# sourceMappingURL=sc-feature-demo-banner.entry.js.map