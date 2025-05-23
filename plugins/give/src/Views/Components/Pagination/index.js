import { __ } from '@wordpress/i18n'

const Pagination = ({currentPage = 1, totalPages = 0, disabled = false, setPage = (page) => {}}) => {
    if (1 >= totalPages) {
        return null;
    }

    const nextPage = parseInt(currentPage) + 1;
    const previousPage = parseInt(currentPage) - 1;

    return (
        <div className="tablenav bottom">
            <div className="tablenav-pages">
                <div className="pagination-links">
                    {previousPage > 0 ? (
                        <>
                            <a
                                href="#"
                                className="tablenav-pages-navspan button"
                                onClick={(e) => {
                                    e.preventDefault();
                                    if (!disabled) {
                                        setPage(1);
                                    }
                                }}
                            >
                                «
                            </a>{' '}
                            <a
                                href="#"
                                className="tablenav-pages-navspan button"
                                onClick={(e) => {
                                    e.preventDefault();
                                    if (!disabled) {
                                        setPage(parseInt(currentPage) - 1);
                                    }
                                }}
                            >
                                ‹
                            </a>
                        </>
                    ) : (
                        <span className="tablenav-pages-navspan button disabled">‹</span>
                    )}

                    <span className="screen-reader-text">{__('Current Page', 'give')}</span>
                    <span id="table-paging" className="paging-input">
                        <span className="tablenav-paging-text">
                            {' '}
                            {currentPage} {__('of', 'give')} <span className="total-pages">{totalPages}</span>{' '}
                        </span>
                    </span>

                    {nextPage <= totalPages ? (
                        <>
                            <a
                                href="#"
                                className="tablenav-pages-navspan button"
                                onClick={(e) => {
                                    e.preventDefault();
                                    if (!disabled) {
                                        setPage(parseInt(currentPage) + 1);
                                    }
                                }}
                            >
                                ›
                            </a>{' '}
                            <a
                                href="#"
                                className="tablenav-pages-navspan button"
                                onClick={(e) => {
                                    e.preventDefault();
                                    if (!disabled) {
                                        setPage(totalPages);
                                    }
                                }}
                            >
                                »
                            </a>
                        </>
                    ) : (
                        <span className="tablenav-pages-navspan button disabled">›</span>
                    )}
                </div>
            </div>
        </div>
    );
};



export default Pagination;
