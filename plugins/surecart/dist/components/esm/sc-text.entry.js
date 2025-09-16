import { r as registerInstance, h } from './index-745b6bec.js';
import { i as isRtl } from './page-align-0cdacf32.js';

const scTextCss = ":host{display:block;--font-size:var(--font-size, var(--sc-font-size-medium));--font-weight:var(--font-size, var(--sc-font-weight-normal));--line-height:var(--font-size, var(--sc-line-height-medium));--text-align:left;--color:var(--color, inherit)}.text{margin:0;font-size:var(--font-size);font-weight:var(--font-weight);line-height:var(--line-height);text-align:var(--text-align);color:var(--sc-stacked-list-row-text-color, var(--color))}.text.is-truncated{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.text--is-rtl .text{text-align:right}";
const ScTextStyle0 = scTextCss;

const ScText = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.tag = 'p';
        this.truncate = false;
    }
    render() {
        const CustomTag = this.tag;
        return (h(CustomTag, { key: '22ec7b3d2d1741e7160fb6d58b1d327fecb599dc', class: {
                'text': true,
                'is-truncated': this.truncate,
                'text--is-rtl': isRtl()
            } }, h("slot", { key: 'a5d106ea4bdc6df76ce214be362cab2df15f8cc9' })));
    }
};
ScText.style = ScTextStyle0;

export { ScText as sc_text };

//# sourceMappingURL=sc-text.entry.js.map