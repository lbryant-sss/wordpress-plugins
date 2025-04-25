import { r as registerInstance, h, H as Host, a as getElement } from './index-745b6bec.js';

const scTabPanelCss = ":host{--padding:0;--spacing:var(--sc-spacing-large);display:block}::slotted(*~*){margin-top:var(--spacing)}.tab-panel{border:solid 1px transparent;padding:var(--padding);font-family:var(--sc-font-sans);font-size:var(--sc-font-size-medium)}";
const ScTabPanelStyle0 = scTabPanelCss;

let id = 0;
const ScTabPanel = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.componentId = `tab-panel-${++id}`;
        this.name = '';
        this.active = false;
    }
    render() {
        // If the user didn't provide an ID, we'll set one so we can link tabs and tab panels with aria labels
        this.el.id = this.el.id || this.componentId;
        return (h(Host, { key: '9bea43e75042f939545cfc017200efdb354cb891', style: { display: this.active ? 'block' : 'none' } }, h("div", { key: 'b9e286f8e109dd8dea718f04f2b6ae120d5a4d9e', part: "base", class: "tab-panel", role: "tabpanel", "aria-hidden": this.active ? 'false' : 'true' }, h("slot", { key: '14ffcb095fe7a5c91bcb0c30853aaabe1c5d80e7' }))));
    }
    get el() { return getElement(this); }
};
ScTabPanel.style = ScTabPanelStyle0;

export { ScTabPanel as sc_tab_panel };

//# sourceMappingURL=sc-tab-panel.entry.js.map