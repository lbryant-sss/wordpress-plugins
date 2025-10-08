import { Download } from '../../../../types';
export declare class ScDownloadsList {
    el: HTMLScDownloadsListElement;
    customerId: string;
    productId: string;
    heading: string;
    downloads: Download[];
    downloading: string;
    busy: boolean;
    error: string;
    pagination: {
        total: number;
        total_pages: number;
    };
    query: any;
    componentWillLoad(): void;
    fetchItems(): Promise<void>;
    /** Get all subscriptions */
    getItems(): Promise<Download[]>;
    nextPage(): void;
    prevPage(): void;
    downloadItem(download: any): Promise<void>;
    downloadFile(path: any, filename: any): void;
    renderFileExt: (download: any) => any;
    renderList(): any;
    renderLoading(): any;
    renderEmpty(): any;
    render(): any;
}
