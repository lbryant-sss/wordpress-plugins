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
        return (index.h("div", { key: '558639c1382dae2a42e8a93176e96562730385ac', class: { 'sc-banner': true } }, index.h("p", { key: '354681ff5de229930610d0f44fa398e90b72e845' }, index.h("slot", { key: 'a63f319190ec29357631926bb83d4a6b6bd3ff58' }, wp.i18n.__('This is a feature demo. In order to use it, you must upgrade your plan.', 'surecart')), index.h("a", { key: 'b2ad0ce419d51255376c6184f18e3b0d0a3a48a2', href: this.url, target: "_blank" }, index.h("slot", { key: '8bb9c68afb7a696a47a46a653c7cecab1ca3d2bc', name: "link" }, this.buttonText, " ", index.h("sc-icon", { key: 'ff0442729f4135e1858b7463e0b51822a24e1e71', name: "arrow-right" }))))));
    }
};
ScFeatureDemoBanner.style = ScFeatureDemoBannerStyle0;

exports.sc_feature_demo_banner = ScFeatureDemoBanner;

//# sourceMappingURL=sc-feature-demo-banner.cjs.entry.js.map