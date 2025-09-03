import { r as registerInstance, h } from './index-745b6bec.js';

const scBadgeNoticeCss = ":host{display:block}.notice{background:var(--sc-badge-notice-background-color, var(--sc-color-white));color:var(--sc-badge-notice-text-color, var(--sc-color-gray-950));border:solid 1px var(--sc-badge-notice-border-color, var(--sc-color-white));border-radius:var(--sc-border-radius-small);padding:var(--sc-spacing-small);font-size:var(--sc-font-size-x-small);display:flex;gap:0.5em;line-height:1}.notice--warning{background:var(--sc-color-warning-50);color:var(--sc-color-warning-700)}";
const ScBadgeNoticeStyle0 = scBadgeNoticeCss;

const ScBadgeNotice = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.type = 'primary';
        this.label = undefined;
        this.size = 'small';
    }
    render() {
        return (h("div", { key: '4058c090b7202247076b3c2823758a805cda6bdd', class: {
                'notice': true,
                'notice--is-small': this.size === 'small',
                'notice--is-medium': this.size === 'medium',
                'notice--is-large': this.size === 'large',
                'notice--primary': this.type === 'primary',
                'notice--success': this.type === 'success',
                'notice--warning': this.type === 'warning',
                'notice--danger': this.type === 'danger',
                'notice--default': this.type === 'default',
            } }, h("sc-tag", { key: '2a16cfe998e448147a0c06595b30da12b9b6bd81', size: this.size, type: this.type }, this.label), h("slot", { key: '1e2974cf55b5a91abb415b64c60bda9a3d124f6c' })));
    }
};
ScBadgeNotice.style = ScBadgeNoticeStyle0;

export { ScBadgeNotice as sc_badge_notice };

//# sourceMappingURL=sc-badge-notice.entry.js.map