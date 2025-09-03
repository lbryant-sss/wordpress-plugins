import { r as registerInstance, h, H as Host } from './index-745b6bec.js';
import { b as getFormattedRemainingTime } from './getters-1899e179.js';
import './store-4bc13420.js';
import './utils-cd1431df.js';
import './index-06061d4e.js';

const scUpsellCountdownTimerCss = ":host{display:flex;justify-content:var(--sc-upsell-countdown-timer-justify-content, center);align-items:var(--sc-upsell-countdown-timer-align-items, center);text-align:var(--sc-upsell-countdown-timer-text-align, center);flex-wrap:wrap;gap:var(--sc-upsell-countdown-timer-gap, 0.5em);line-height:1;padding:var(--sc-upsell-countdown-timer-padding, var(--sc-spacing-medium));border-radius:var(--sc-upsell-countdown-timer-border-radius, var(--sc-border-radius-pill));background-color:var(--sc-upsell-countdown-timer-background-color, rgb(226, 249, 235));color:var(--sc-upsell-countdown-timer-color, rgb(71, 91, 80))}";
const ScUpsellCountdownTimerStyle0 = scUpsellCountdownTimerCss;

const ScUpsellCountdownTimer = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.timeRemaining = Infinity;
        this.formattedTime = undefined;
        this.showIcon = true;
    }
    componentDidLoad() {
        this.updateCountdown();
    }
    updateCountdown() {
        this.formattedTime = getFormattedRemainingTime();
        setInterval(() => {
            this.formattedTime = getFormattedRemainingTime();
        }, 1000);
    }
    render() {
        return (h(Host, { key: '9a9efb0825555f64b0e1065138fe9358309b2a3b', role: "timer", class: {
                'sc-upsell-countdown-timer': true,
            } }, this.showIcon && h("sc-icon", { key: 'cb68f938472b6955f5f7ed0105c5a8cada231e79', name: "clock" }), h("span", { key: '6c13e82c3709e2895d4617590de4e8ef6f27824a' }, h("slot", { key: 'b38c01b8b89ad649cbeaad2b7a6e63665a19aa72', name: "offer-expire-text" }), " ", h("strong", { key: 'af31554277d95a58c265f830045946ddbd1c19ed' }, this.formattedTime))));
    }
};
ScUpsellCountdownTimer.style = ScUpsellCountdownTimerStyle0;

export { ScUpsellCountdownTimer as sc_upsell_countdown_timer };

//# sourceMappingURL=sc-upsell-countdown-timer.entry.js.map