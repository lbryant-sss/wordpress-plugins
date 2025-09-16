export declare class ScProductLineItemNote {
    el: HTMLElement;
    note: string;
    expanded: boolean;
    isOverflowing: boolean;
    private noteEl?;
    private resizeObserver?;
    private mutationObserver?;
    componentDidLoad(): void;
    disconnectedCallback(): void;
    setupObservers(): void;
    cleanupObservers(): void;
    checkOverflow(): void;
    toggle(): void;
    render(): any;
}
