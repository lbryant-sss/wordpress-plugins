'use strict';

Object.defineProperty(exports, '__esModule', { value: true });

const index = require('./index-8acc3c89.js');
const fetch = require('./fetch-d374a251.js');
const lazy = require('./lazy-2b509fa7.js');
const addQueryArgs = require('./add-query-args-49dcb630.js');
require('./remove-query-args-b57e8cd3.js');

const scDownloadsListCss = ":host{display:block}.purchase{display:flex;flex-direction:column;gap:var(--sc-spacing-large)}.single-download .single-download__preview{display:flex;align-items:center;justify-content:center;background:var(--sc-color-gray-200);border-radius:var(--sc-border-radius-small);height:4rem;min-width:4rem;width:4rem}";
const ScDownloadsListStyle0 = scDownloadsListCss;

const ScDownloadsList = class {
    constructor(hostRef) {
        index.registerInstance(this, hostRef);
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
            return index.h("sc-icon", { name: "file" });
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
        lazy.onFirstVisible(this.el, () => {
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
        const response = (await fetch.apiFetch({
            path: addQueryArgs.addQueryArgs(`surecart/v1/downloads/`, {
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
            const media = (await fetch.apiFetch({
                path: addQueryArgs.addQueryArgs(`surecart/v1/customers/${this.customerId}/expose/${mediaId}`, {
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
        return (index.h("sc-card", { "no-padding": true }, index.h("sc-stacked-list", null, downloads.map(download => {
            var _a, _b, _c, _d;
            const media = download === null || download === void 0 ? void 0 : download.media;
            return (index.h("sc-stacked-list-row", { style: { '--columns': '1' } }, index.h("sc-flex", { class: "single-download", justifyContent: "flex-start", alignItems: "center" }, index.h("div", { class: "single-download__preview" }, this.renderFileExt(download)), index.h("div", null, index.h("div", null, index.h("strong", null, (_b = (_a = media === null || media === void 0 ? void 0 : media.filename) !== null && _a !== void 0 ? _a : download === null || download === void 0 ? void 0 : download.name) !== null && _b !== void 0 ? _b : '')), index.h("sc-flex", { justifyContent: "flex-start", alignItems: "center", style: { gap: '0.5em' } }, (media === null || media === void 0 ? void 0 : media.byte_size) && index.h("sc-format-bytes", { value: media.byte_size }), !!((_c = media === null || media === void 0 ? void 0 : media.release_json) === null || _c === void 0 ? void 0 : _c.version) && (index.h("sc-tag", { type: "primary", size: "small", style: {
                    '--sc-tag-primary-background-color': '#f3e8ff',
                    '--sc-tag-primary-color': '#6b21a8',
                } }, "v", (_d = media === null || media === void 0 ? void 0 : media.release_json) === null || _d === void 0 ? void 0 :
                _d.version))))), index.h("sc-button", { size: "small", slot: "suffix", onClick: () => this.downloadItem(download), busy: (media === null || media === void 0 ? void 0 : media.id) ? this.downloading == (media === null || media === void 0 ? void 0 : media.id) : false, disabled: (media === null || media === void 0 ? void 0 : media.id) ? this.downloading == (media === null || media === void 0 ? void 0 : media.id) : false }, wp.i18n.__('Download', 'surecart'))));
        }))));
    }
    renderLoading() {
        return (index.h("sc-card", { "no-padding": true, style: { '--overflow': 'hidden' } }, index.h("sc-stacked-list", null, index.h("sc-stacked-list-row", { style: { '--columns': '2' }, "mobile-size": 0 }, index.h("div", { style: { padding: '0.5em' } }, index.h("sc-skeleton", { style: { width: '30%', marginBottom: '0.75em' } }), index.h("sc-skeleton", { style: { width: '20%' } }))))));
    }
    renderEmpty() {
        return (index.h("div", null, index.h("sc-divider", { style: { '--spacing': '0' } }), index.h("slot", { name: "empty" }, index.h("sc-empty", { icon: "download" }, wp.i18n.__("You don't have any downloads.", 'surecart')))));
    }
    render() {
        var _a;
        return (index.h("sc-dashboard-module", { key: '65aeac85deaf63e1ff72ce800cc82cb4309021b4', class: "purchase", part: "base", heading: wp.i18n.__('Downloads', 'surecart') }, index.h("span", { key: 'b414cae876550abbbe37fdb73fd8f6c7ac4d9dce', slot: "heading" }, index.h("slot", { key: '8cb776835f359a0961e674c85eff78f28af41d47', name: "heading" }, this.heading || wp.i18n.__('Downloads', 'surecart'))), this.renderList(), index.h("sc-pagination", { key: '2ddad307c9df1310b553a7117155a417fcdf414d', page: this.query.page, perPage: this.query.per_page, total: this.pagination.total, totalPages: this.pagination.total_pages, totalShowing: (_a = this === null || this === void 0 ? void 0 : this.downloads) === null || _a === void 0 ? void 0 : _a.length, onScNextPage: () => this.nextPage(), onScPrevPage: () => this.prevPage() }), this.busy && index.h("sc-block-ui", { key: 'c69765404bc5dcf8e1f81705e9e0c0cbb52eab7d' })));
    }
    get el() { return index.getElement(this); }
};
ScDownloadsList.style = ScDownloadsListStyle0;

exports.sc_downloads_list = ScDownloadsList;

//# sourceMappingURL=sc-downloads-list.cjs.entry.js.map