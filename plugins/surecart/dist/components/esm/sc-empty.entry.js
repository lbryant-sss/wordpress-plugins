import { r as registerInstance, h } from './index-745b6bec.js';

const scEmptyCss = ":host{display:block}.empty{display:flex;flex-direction:column;align-items:center;padding:var(--sc-spacing-large);text-align:center;gap:var(--sc-spacing-small);color:var(--sc-empty-color, var(--sc-color-gray-500))}.empty sc-icon{font-size:var(--sc-font-size-xx-large);color:var(--sc-empty-icon-color, var(--sc-color-gray-700))}";
const ScEmptyStyle0 = scEmptyCss;

const ScEmpty = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.icon = undefined;
    }
    render() {
        return (h("div", { key: 'a9a79a6829a6b0c0cd813b34309c4f3dd1fcb509', part: "base", class: "empty" }, !!this.icon && h("sc-icon", { key: '4cab24882e6679e284f9a723aa5a4a4a69f3e663', exportparts: "base:icon", name: this.icon }), h("slot", { key: 'a39dc8fb17cfa8b9d37a079d5e98f21d542ef336' })));
    }
};
ScEmpty.style = ScEmptyStyle0;

export { ScEmpty as sc_empty };

//# sourceMappingURL=sc-empty.entry.js.map