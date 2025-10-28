async function detectAdblock({ timeoutMs = 2500 } = {}) {

    ensureBait();

    const controlOk = await loadScript(wpdm_url.site+'wp-content/plugins/download-manager/assets/js/control/ping.js', timeoutMs);

    const decoyOk = await loadScript(wpdm_url.site+'wp-content/plugins/download-manager/assets/js/ads/adserver.js', timeoutMs);

    const baitHidden = isBaitHidden();

    const likelyAdblock = controlOk && !decoyOk;
    return {
        isAdblock: likelyAdblock || (likelyAdblock && baitHidden),
        details: { controlOk, decoyOk, baitHidden }
    };
}

function loadScript(src, timeoutMs) {
    return new Promise((resolve) => {
        const s = document.createElement('script');
        s.src = src + (src.includes('?') ? '&' : '?') + 'v=' + Date.now();
        s.async = true;

        let settled = false;
        const done = (ok) => { if (!settled) { settled = true; resolve(ok); } };

        const timer = setTimeout(() => done(false), timeoutMs);
        s.onload = () => { clearTimeout(timer); done(true); };
        s.onerror = () => { clearTimeout(timer); done(false); };

        document.head.appendChild(s);
    });
}

function ensureBait() {
    if (document.getElementById('__ad_bait__')) return;
    const bait = document.createElement('div');
    bait.id = '__ad_bait__';
    bait.className = 'ads ad ad-banner adsbox ad-wrapper';
    Object.assign(bait.style, {
        position: 'absolute',
        left: '-9999px',
        top: '-9999px',
        width: '1px',
        height: '1px',
        pointerEvents: 'none'
    });
    document.body.appendChild(bait);
}

function isBaitHidden() {
    const el = document.getElementById('__ad_bait__');
    if (!el) return false;
    const cs = getComputedStyle(el);
    const hidden =
        el.offsetParent === null ||
        cs.display === 'none' ||
        cs.visibility === 'hidden' ||
        parseInt(cs.height, 10) === 0 ||
        parseInt(cs.width, 10) === 0 ||
        cs.opacity === '0';
    return hidden;
}

jQuery(function($) {

    detectAdblock().then(({ isAdblock, details }) => {
        console.log('Adblock?', isAdblock, details);
        if (isAdblock) {
            const alldlbtns = jQuery('.wpdm-download-link.download-on-click');
            alldlbtns.removeAttr('data-downloadurl');
            alldlbtns.on('click', function (e) {
                WPDM.bootAlert("Ad blocker detected", abmsg);
                return false;
            });
            //WPDM.bootAlert("Ad blocker detected", abmsg)
        }
    });

});

