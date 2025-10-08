import { r as registerInstance, h, a as getElement } from './index-745b6bec.js';
import { a as apiFetch } from './fetch-bc141774.js';
import { o as onFirstVisible } from './lazy-deb42890.js';
import { a as addQueryArgs } from './add-query-args-0e2a8393.js';
import './remove-query-args-938c53ea.js';

const scDownloadsListCss = ":host{display:block}.purchase{display:flex;flex-direction:column;gap:var(--sc-spacing-large)}.single-download .single-download__preview{display:flex;align-items:center;justify-content:center;background:var(--sc-color-gray-200);border-radius:var(--sc-border-radius-small);height:4rem;min-width:4rem;width:4rem}";
const ScDownloadsListStyle0 = scDownloadsListCss;

const ScDownloadsList = class {
    constructor(hostRef) {
        registerInstance(this, hostRef);
        this.renderFileExt = download => {
            var _a, _b, _c, _d, _e, _f, _g, _h, _j;
            if ((_a = download === null || download === void 0 ? void 0 : download.media) === null || _a === void 0 ? void 0 : _a.filename) {
                return (_e = (_d = (_c = (_b = download.media.filename).split) === null || _c === void 0 ? void 0 : _c.call(_b, '.')) === null || _d === void 0 ? void 0 : _d.pop) === null || _e === void 0 ? void 0 : _e.call(_d);
            }
            if (download === null || download === void 0 ? void 0 : download.url) {
                try {
                    const url = new URL(download.url);
                    if (url.pathname.includes('.')) {
                        return (_j = (_h = (_g = (_f = url.pathname).split) === null || _g === void 0 ? void 0 : _g.call(_f, '.')) === null || _h === void 0 ? void 0 : _h.pop) === null || _j === void 0 ? void 0 : _j.call(_h);
                    }
                }
                catch (err) {
                    console.error(err);
                }
            }
            return h("sc-icon", { name: "file" });
        };
        this.customerId = undefined;
        this.productId = undefined;
        this.heading = undefined;
        this.downloads = undefined;
        this.downloading = undefined;
        this.busy = undefined;
        this.error = undefined;
        this.pagination = {
            total: 0,
            total_pages: 0,
        };
        this.query = {
            page: 1,
            per_page: 20,
        };
    }
    componentWillLoad() {
        onFirstVisible(this.el, () => {
            this.fetchItems();
        });
    }
    async fetchItems() {
        if (!this.productId || !this.customerId) {
            return;
        }
        try {
            this.busy = true;
            await this.getItems();
        }
        catch (e) {
            console.error(this.error);
            this.error = (e === null || e === void 0 ? void 0 : e.message) || wp.i18n.__('Something went wrong', 'surecart');
        }
        finally {
            this.busy = false;
        }
    }
    /** Get all subscriptions */
    async getItems() {
        const response = (await apiFetch({
            path: addQueryArgs(`surecart/v1/downloads/`, {
                product_ids: [this.productId],
                customer_ids: [this.customerId],
                downloadable: true,
                ...this.query,
            }),
            parse: false,
        }));
        this.pagination = {
            total: parseInt(response.headers.get('X-WP-Total')),
            total_pages: parseInt(response.headers.get('X-WP-TotalPages')),
        };
        this.downloads = (await response.json());
        return this.downloads;
    }
    nextPage() {
        this.query.page = this.query.page + 1;
        this.fetchItems();
    }
    prevPage() {
        this.query.page = this.query.page - 1;
        this.fetchItems();
    }
    async downloadItem(download) {
        var _a, _b;
        if (download === null || download === void 0 ? void 0 : download.url) {
            this.downloadFile(download.url, (_a = download === null || download === void 0 ? void 0 : download.name) !== null && _a !== void 0 ? _a : 'file');
            return;
        }
        const mediaId = (_b = download === null || download === void 0 ? void 0 : download.media) === null || _b === void 0 ? void 0 : _b.id;
        if (!mediaId)
            return;
        try {
            this.downloading = mediaId;
            const media = (await apiFetch({
                path: addQueryArgs(`surecart/v1/customers/${this.customerId}/expose/${mediaId}`, {
                    expose_for: 60,
                }),
            }));
            if (!(media === null || media === void 0 ? void 0 : media.url)) {
                throw {
                    message: wp.i18n.__('Could not download the file.', 'surecart'),
                };
            }
            this.downloadFile(media === null || media === void 0 ? void 0 : media.url, media.filename);
        }
        catch (e) {
            console.error(e);
            this.error = (e === null || e === void 0 ? void 0 : e.message) || wp.i18n.__('Something went wrong', 'surecart');
        }
        finally {
            this.downloading = null;
        }
    }
    downloadFile(path, filename) {
        // Create a new link
        const anchor = document.createElement('a');
        anchor.href = path;
        anchor.download = filename;
        // Append to the DOM
        document.body.appendChild(anchor);
        // Trigger `click` event
        anchor.click();
        // To make this work on Firefox we need to wait
        // a little while before removing it.
        setTimeout(() => {
            document.body.removeChild(anchor);
        }, 0);
    }
    renderList() {
        var _a, _b;
        if ((this === null || this === void 0 ? void 0 : this.busy) && !((_a = this === null || this === void 0 ? void 0 : this.downloads) === null || _a === void 0 ? void 0 : _a.length)) {
            return this.renderLoading();
        }
        if (!((_b = this === null || this === void 0 ? void 0 : this.downloads) === null || _b === void 0 ? void 0 : _b.length)) {
            return this.renderEmpty();
        }
        const downloads = this.downloads || [];
        return (h("sc-card", { "no-padding": true }, h("sc-stacked-list", null, downloads.map(download => {
            var _a, _b, _c, _d;
            const media = download === null || download === void 0 ? void 0 : download.media;
            return (h("sc-stacked-list-row", { style: { '--columns': '1' } }, h("sc-flex", { class: "single-download", justifyContent: "flex-start", alignItems: "center" }, h("div", { class: "single-download__preview" }, this.renderFileExt(download)), h("div", null, h("div", null, h("strong", null, (_b = (_a = media === null || media === void 0 ? void 0 : media.filename) !== null && _a !== void 0 ? _a : download === null || download === void 0 ? void 0 : download.name) !== null && _b !== void 0 ? _b : '')), h("sc-flex", { justifyContent: "flex-start", alignItems: "center", style: { gap: '0.5em' } }, (media === null || media === void 0 ? void 0 : media.byte_size) && h("sc-format-bytes", { value: media.byte_size }), !!((_c = media === null || media === void 0 ? void 0 : media.release_json) === null || _c === void 0 ? void 0 : _c.version) && (h("sc-tag", { type: "primary", size: "small", style: {
                    '--sc-tag-primary-background-color': '#f3e8ff',
                    '--sc-tag-primary-color': '#6b21a8',
                } }, "v", (_d = media === null || media === void 0 ? void 0 : media.release_json) === null || _d === void 0 ? void 0 :
                _d.version))))), h("sc-button", { size: "small", slot: "suffix", onClick: () => this.downloadItem(download), busy: (media === null || media === void 0 ? void 0 : media.id) ? this.downloading == (media === null || media === void 0 ? void 0 : media.id) : false, disabled: (media === null || media === void 0 ? void 0 : media.id) ? this.downloading == (media === null || media === void 0 ? void 0 : media.id) : false }, wp.i18n.__('Download', 'surecart'))));
        }))));
    }
    renderLoading() {
        return (h("sc-card", { "no-padding": true, style: { '--overflow': 'hidden' } }, h("sc-stacked-list", null, h("sc-stacked-list-row", { style: { '--columns': '2' }, "mobile-size": 0 }, h("div", { style: { padding: '0.5em' } }, h("sc-skeleton", { style: { width: '30%', marginBottom: '0.75em' } }), h("sc-skeleton", { style: { width: '20%' } }))))));
    }
    renderEmpty() {
        return (h("div", null, h("sc-divider", { style: { '--spacing': '0' } }), h("slot", { name: "empty" }, h("sc-empty", { icon: "download" }, wp.i18n.__("You don't have any downloads.", 'surecart')))));
    }
    render() {
        var _a;
        return (h("sc-dashboard-module", { key: '65aeac85deaf63e1ff72ce800cc82cb4309021b4', class: "purchase", part: "base", heading: wp.i18n.__('Downloads', 'surecart') }, h("span", { key: 'b414cae876550abbbe37fdb73fd8f6c7ac4d9dce', slot: "heading" }, h("slot", { key: '8cb776835f359a0961e674c85eff78f28af41d47', name: "heading" }, this.heading || wp.i18n.__('Downloads', 'surecart'))), this.renderList(), h("sc-pagination", { key: '2ddad307c9df1310b553a7117155a417fcdf414d', page: this.query.page, perPage: this.query.per_page, total: this.pagination.total, totalPages: this.pagination.total_pages, totalShowing: (_a = this === null || this === void 0 ? void 0 : this.downloads) === null || _a === void 0 ? void 0 : _a.length, onScNextPage: () => this.nextPage(), onScPrevPage: () => this.prevPage() }), this.busy && h("sc-block-ui", { key: 'c69765404bc5dcf8e1f81705e9e0c0cbb52eab7d' })));
    }
    get el() { return getElement(this); }
};
ScDownloadsList.style = ScDownloadsListStyle0;

export { ScDownloadsList as sc_downloads_list };

//# sourceMappingURL=sc-downloads-list.entry.js.map