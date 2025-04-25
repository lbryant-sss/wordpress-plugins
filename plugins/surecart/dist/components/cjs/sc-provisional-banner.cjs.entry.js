'use strict';

Object.defineProperty(exports, '__esModule', { value: true });

const index = require('./index-8acc3c89.js');

const scProvisionalBannerCss = ".sc-banner{background-color:var(--sc-color-brand-primary);color:white;display:flex;align-items:center;justify-content:center}.sc-banner>p{font-size:14px;line-height:1;margin:var(--sc-spacing-small)}.sc-banner>p a{color:inherit;font-weight:600;margin-left:10px;display:inline-flex;align-items:center;gap:8px;text-decoration:none;border-bottom:1px solid;padding-bottom:2px}";
const ScProvisionalBannerStyle0 = scProvisionalBannerCss;

const ScProvisionalBanner = class {
    constructor(hostRef) {
        index.registerInstance(this, hostRef);
        this.claimUrl = '';
    }
    render() {
        return (index.h("div", { key: '616fd32b9e782380b72df3ffe0f8be85c0fac695', class: { 'sc-banner': true } }, index.h("p", { key: '1b7d14bb0596313a2868f6a470d584540e556c3b' }, wp.i18n.__('Complete your store setup to go live.', 'surecart'), index.h("a", { key: 'd4769f098e3ba2879ef1e1aca5ce23ab07849330', href: this.claimUrl }, wp.i18n.__('Complete Setup', 'surecart'), " ", index.h("sc-icon", { key: 'c3e7b2e226579bb63ab21a80454403d57fa8a414', name: "arrow-right" })))));
    }
};
ScProvisionalBanner.style = ScProvisionalBannerStyle0;

exports.sc_provisional_banner = ScProvisionalBanner;

//# sourceMappingURL=sc-provisional-banner.cjs.entry.js.map