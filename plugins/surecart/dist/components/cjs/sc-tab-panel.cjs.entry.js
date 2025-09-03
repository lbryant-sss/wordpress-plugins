'use strict';

Object.defineProperty(exports, '__esModule', { value: true });

const index = require('./index-8acc3c89.js');

const scTabPanelCss = ":host{--padding:0;--spacing:var(--sc-spacing-large);display:block}::slotted(*~*){margin-top:var(--spacing)}.tab-panel{border:solid 1px transparent;padding:var(--padding);font-family:var(--sc-font-sans);font-size:var(--sc-font-size-medium)}";
const ScTabPanelStyle0 = scTabPanelCss;

let id = 0;
const ScTabPanel = class {
    constructor(hostRef) {
        index.registerInstance(this, hostRef);
        this.componentId = `tab-panel-${++id}`;
        this.name = '';
        this.active = false;
    }
    render() {
        // If the user didn't provide an ID, we'll set one so we can link tabs and tab panels with aria labels
        this.el.id = this.el.id || this.componentId;
        return (index.h(index.Host, { key: '3085ccd8e69183325be23061c8f69b00969745cb', style: { display: this.active ? 'block' : 'none' } }, index.h("div", { key: 'b838e5788d8b088c5eecd62f9df3e4154c7a4379', part: "base", class: "tab-panel", role: "tabpanel", "aria-hidden": this.active ? 'false' : 'true' }, index.h("slot", { key: 'd7162f49a28d392b6470681264937b844a97b64e' }))));
    }
    get el() { return index.getElement(this); }
};
ScTabPanel.style = ScTabPanelStyle0;

exports.sc_tab_panel = ScTabPanel;

//# sourceMappingURL=sc-tab-panel.cjs.entry.js.map