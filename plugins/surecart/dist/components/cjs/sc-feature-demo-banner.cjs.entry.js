'use strict';

Object.defineProperty(exports, '__esModule', { value: true });

const index = require('./index-8acc3c89.js');

const scFeatureDemoBannerCss = ".sc-banner{background-color:var(--sc-color-brand-primary);color:white;display:flex;align-items:center;justify-content:center}.sc-banner>p{font-size:14px;line-height:1;margin:var(--sc-spacing-small)}.sc-banner>p a{color:inherit;font-weight:600;margin-left:10px;display:inline-flex;align-items:center;gap:8px;text-decoration:none;border-bottom:1px solid;padding-bottom:2px}";
const ScFeatureDemoBannerStyle0 = scFeatureDemoBannerCss;

const ScFeatureDemoBanner = class {
    constructor(hostRef) {
        index.registerInstance(this, hostRef);
        this.url = 'https://app.surecart.com/plans';
        this.buttonText = wp.i18n.__('Upgrade Your Plan', 'surecart');
    }
    render() {
        return (index.h("div", { key: 'aa8578ce1640e18afd168375b5f9ba4f265b5693', class: { 'sc-banner': true } }, index.h("p", { key: '23530b1cfafbd8cd431c7c87ff1a33be743d4958' }, index.h("slot", { key: '6421c064499a6751ff3693b030e4e1a97abec2c5' }, wp.i18n.__('This is a feature demo. In order to use it, you must upgrade your plan.', 'surecart')), index.h("a", { key: '473ff26457ef064d6c56005f751717481580b3fc', href: this.url, target: "_blank" }, index.h("slot", { key: 'bd9327c7d7fafeb49e9eb360b6e547198f6121db', name: "link" }, this.buttonText, " ", index.h("sc-icon", { key: '82d7ace4b4c06660c7e68c57f38a2d5eaa074263', name: "arrow-right" }))))));
    }
};
ScFeatureDemoBanner.style = ScFeatureDemoBannerStyle0;

exports.sc_feature_demo_banner = ScFeatureDemoBanner;

//# sourceMappingURL=sc-feature-demo-banner.cjs.entry.js.map