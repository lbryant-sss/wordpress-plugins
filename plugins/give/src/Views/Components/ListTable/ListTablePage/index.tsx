import {createContext, useRef, useState} from 'react';
import {__} from '@wordpress/i18n';
import {A11yDialog} from 'react-a11y-dialog';
import A11yDialogInstance from 'a11y-dialog';
import {GiveIcon} from '@givewp/components';
import {ListTable} from '../ListTable';
import Pagination from '../Pagination';
import {Filter, getInitialFilterState} from '../Filters';
import useDebounce from '../hooks/useDebounce';
import {useResetPage} from '../hooks/useResetPage';
import ListTableApi from '../api';
import styles from './ListTablePage.module.scss';
import cx from 'classnames';
import {BulkActionSelect} from '@givewp/components/ListTable/BulkActions/BulkActionSelect';
import ToggleSwitch from '@givewp/components/ListTable/ToggleSwitch';
import DeleteIcon from '@givewp/components/ListTable/ListTablePage/DeleteIcon';

export interface ListTablePageProps {
    //required
    title: string;
    apiSettings: {apiRoot; apiNonce; table};

    //optional
    bulkActions?: Array<BulkActionsConfig> | null;
    pluralName?: string;
    singleName?: string;
    children?: JSX.Element | JSX.Element[] | null;
    rowActions?: JSX.Element | JSX.Element[] | Function | null;
    filterSettings?;
    align?: 'start' | 'center' | 'end';
    paymentMode?: boolean;
    listTableBlankSlate: JSX.Element;
    productRecommendation?: JSX.Element;
    columnFilters?: Array<ColumnFilterConfig>;
    banner?: () => JSX.Element;
    contentMode?: boolean;
}

export interface FilterConfig {
    // required
    name: string;
    type: 'select' | 'formselect' | 'search' | 'checkbox' | 'hidden';

    // optional
    ariaLabel?: string;
    inlineSize?: string;
    text?: string;
    options?: Array<{text: string; value: string}>;
}

export interface ColumnFilterConfig {
    column: string;
    filter: Function;
}

interface BulkActionsConfigBase {
    //required
    label: string;
    value: string | number;
    confirm: (
        selected: Array<string | number>,
        names?: Array<string>,
        isOpen?: boolean,
        setOpen?: (isOpen?: boolean) => void
    ) => JSX.Element | JSX.Element[] | string;

    //optional
    isVisible?: (data: any, parameters: any) => boolean;
    isIdSelectable?: (id: string, data: any) => boolean;
    type?: 'normal' | 'warning' | 'danger' | 'custom';
}

// Makes the "action" property required for the standard types
interface BulkActionsConfigWithAction extends BulkActionsConfigBase {
    type: 'normal' | 'warning' | 'danger';
    action: (selected: Array<string | number>) => Promise<{errors: string | number; successes: string | number}>;
}

// Makes the "action" property required for the undefined type
interface BulkActionsConfigWithoutType extends BulkActionsConfigBase {
    type?: undefined;
    action: (selected: Array<string | number>) => Promise<{errors: string | number; successes: string | number}>;
}

// Makes the "action" property forbidden for the custom type
export interface BulkActionsConfigWithoutAction extends BulkActionsConfigBase {
    type: 'custom';
}

export type BulkActionsConfig =
    | BulkActionsConfigWithAction
    | BulkActionsConfigWithoutType
    | BulkActionsConfigWithoutAction;

export const ShowConfirmModalContext = createContext(
    (label, confirm, action, type = null, confirmButtonText = __('Confirm', 'give')) => {}
);
export const CheckboxContext = createContext(null);

export default function ListTablePage({
    title,
    apiSettings,
    bulkActions = null,
    filterSettings = [],
    singleName = __('item', 'give'),
    pluralName = __('items', 'give'),
    rowActions = null,
    children = null,
    align = 'start',
    paymentMode,
    listTableBlankSlate,
    productRecommendation,
    columnFilters = [],
    banner,
    contentMode,
}: ListTablePageProps) {
    const [page, setPage] = useState<number>(1);
    const [perPage, setPerPage] = useState<number>(30);
    const [filters, setFilters] = useState(getInitialFilterState(filterSettings));
    const [isOpen, setOpen] = useState(false);
    const [modalContent, setModalContent] = useState<{
        confirm;
        action?;
        label;
        confirmButtonText?: string;
        type?: 'normal' | 'warning' | 'danger' | 'custom';
    }>({
        confirm: (selected) => {},
        action: (selected) => {},
        label: '',
        confirmButtonText: '',
    });
    const [selectedAction, setSelectedAction] = useState<string>('');
    const [selectedIds, setSelectedIds] = useState([]);
    const [selectedNames, setSelectedNames] = useState([]);
    const dialog = useRef() as {current: A11yDialogInstance};
    const checkboxRefs = useRef([]);
    const [sortField, setSortField] = useState<{sortColumn: string; sortDirection: string}>({
        sortColumn: 'id',
        sortDirection: 'desc',
    });
    const [testMode, setTestMode] = useState(paymentMode);

    const {sortColumn, sortDirection} = sortField;
    const locale = navigator.language || navigator.languages[0];
    const testModeFilter = filterSettings.find((filter) => filter.name === 'toggle');

    const parameters = {
        page,
        perPage,
        sortColumn,
        sortDirection,
        locale,
        testMode,
        ...filters,
    };

    const archiveApi = useRef(new ListTableApi(apiSettings)).current;

    const {data, error, isValidating, mutate} = archiveApi.useListTable(parameters);

    useResetPage(data, page, setPage, filters);

    const handleFilterChange = (name, value) => {
        setFilters((prevState) => ({...prevState, [name]: value}));
    };

    const handleDebouncedFilterChange = useDebounce(handleFilterChange);

    const showConfirmActionModal = (
        label,
        confirm,
        action,
        type?: 'normal' | 'warning' | 'danger' | 'custom' | null,
        confirmButtonText?: string
    ) => {
        setModalContent({label, confirm, action, type, confirmButtonText});
        dialog.current.show();
    };

    const openBulkActionModal = (event) => {
        event.preventDefault();

        if (window.GiveDonations && window.GiveDonations.addonsBulkActions) {
            bulkActions = [...bulkActions, ...window.GiveDonations.addonsBulkActions];
        }

        const bulkAction = bulkActions.find((config) => selectedAction === config.value);

        if (!bulkAction) return;

        const selected = [];
        const names = [];
        const selectedRefs = checkboxRefs.current.filter((checkbox) => {
            const isSelectable = bulkAction?.isIdSelectable?.(checkbox.dataset.id, data) ?? true;
            return checkbox.checked && isSelectable;
        });
        selectedRefs.forEach((checkbox) => {
            selected.push(checkbox.dataset.id);
            names.push(checkbox.dataset.name);
        });
        setSelectedIds(selected);
        setSelectedNames(names);
        if (selected.length) {
            setModalContent({...bulkAction});
            if ('custom' === bulkAction.type) {
                setOpen(true);
                bulkAction?.confirm(selected, names, isOpen, setOpen);
            } else {
                dialog.current.show();
            }
        }
    };

    const setSortDirectionForColumn = (column, direction) => {
        setSortField((previousState) => {
            return {
                ...previousState,
                sortColumn: column,
                sortDirection: direction,
            };
        });
    };

    const showPagination = () => (
        <Pagination
            currentPage={page}
            totalPages={data ? data.totalPages : 1}
            disabled={!data}
            totalItems={data ? parseInt(data.totalItems) : -1}
            setPage={setPage}
            singleName={singleName}
            pluralName={pluralName}
        />
    );

    const PageActions = ({PageActionsTop}: {PageActionsTop?: boolean}) => {
        return (
            <div className={cx(styles.pageActions, {[styles.alignEnd]: !bulkActions})}>
                <BulkActionSelect
                    selectedState={[selectedAction, setSelectedAction]}
                    parameters={parameters}
                    data={data}
                    bulkActions={bulkActions}
                    showModal={openBulkActionModal}
                />
                {PageActionsTop && testModeFilter && <TestModeFilter />}
                {!PageActionsTop && page && setPage && showPagination()}
            </div>
        );
    };

    const TestModeFilter = () => (
        <ToggleSwitch ariaLabel={testModeFilter?.ariaLabel} onChange={setTestMode} checked={testMode} />
    );

    const TestModeBadge = () => <span>{testModeFilter?.text}</span>;

    return (
        <>
            <article className={styles.page}>
                {contentMode ? (
                    <>
                        <section role="search" id={styles.searchContainer}>
                            <div className={styles.flexRow}>
                                <PageActions PageActionsTop />
                            </div>
                            <div className={styles.flexRow}>
                                {filterSettings.map((filter) => (
                                    <Filter
                                        key={filter.name}
                                        value={filters[filter.name]}
                                        filter={filter}
                                        onChange={handleFilterChange}
                                        debouncedOnChange={handleDebouncedFilterChange}
                                    />
                                ))}
                            </div>
                        </section>
                    </>
                ) : (
                    <>
                        <header className={styles.pageHeader}>
                            <div className={styles.flexRow}>
                                <GiveIcon size={'1.875rem'} />
                                <h1 className={styles.pageTitle}>{title}</h1>
                                {testModeFilter && testMode && <TestModeBadge />}
                            </div>
                            {children && <div className={styles.flexRow}>{children}</div>}
                        </header>
                        {banner && <section role="banner">{banner()}</section>}
                        <section role="search" id={styles.searchContainer}>
                            <div className={styles.flexRow}>
                                <PageActions PageActionsTop />
                            </div>
                            <div className={styles.flexRow}>
                                {filterSettings.map((filter) => (
                                    <Filter
                                        key={filter.name}
                                        value={filters[filter.name]}
                                        filter={filter}
                                        onChange={handleFilterChange}
                                        debouncedOnChange={handleDebouncedFilterChange}
                                    />
                                ))}
                            </div>
                        </section>
                    </>
                )}

                <div className={cx('wp-header-end', 'hidden')} />
                <div className={styles.pageContent}>
                    {contentMode && children ? <>{children}</> : <br />}
                    <CheckboxContext.Provider value={checkboxRefs}>
                        <ShowConfirmModalContext.Provider value={showConfirmActionModal}>
                            <ListTable
                                apiSettings={apiSettings}
                                sortField={sortField}
                                setSortDirectionForColumn={setSortDirectionForColumn}
                                singleName={singleName}
                                pluralName={pluralName}
                                title={title}
                                rowActions={rowActions}
                                parameters={parameters}
                                data={data}
                                error={error}
                                isLoading={isValidating}
                                align={align}
                                testMode={testMode}
                                listTableBlankSlate={listTableBlankSlate}
                                productRecommendation={productRecommendation}
                                columnFilters={columnFilters}
                            />
                        </ShowConfirmModalContext.Provider>
                    </CheckboxContext.Provider>
                    <PageActions />
                </div>
            </article>
            <A11yDialog
                id="giveListTableModal"
                dialogRef={(instance) => (dialog.current = instance)}
                title={
                    <>
                        {modalContent?.type === 'danger' && <DeleteIcon />}
                        {modalContent?.label}
                    </>
                }
                titleId={styles.modalTitle}
                classNames={{
                    container: styles.container,
                    overlay: styles.overlay,
                    dialog: cx(styles.dialog, {
                        [styles.warning]: modalContent?.type === 'warning',
                        [styles.danger]: modalContent?.type === 'danger',
                    }),
                    closeButton: 'hidden',
                }}
            >
                <div className={styles.modalContent}>
                    {modalContent?.confirm(selectedIds, selectedNames, isOpen, setOpen) || null}
                </div>
                <div className={styles.gutter}>
                    <button id={styles.cancel} onClick={(event) => dialog.current?.hide()}>
                        {__('Cancel', 'give')}
                    </button>
                    <button
                        id={styles.confirm}
                        onClick={async (event) => {
                            dialog.current?.hide();
                            try {
                                await modalContent.action(selectedIds);
                                await mutate();
                            } catch (error) {
                                console.error('Bulk action error:', error);
                                
                                // Create a user-friendly error message
                                let errorMessage = __('An error occurred while performing this action.', 'give');
                                
                                if (error.message && error.message.includes('permission')) {
                                    errorMessage = __('You don\'t have permission to perform this action.', 'give');
                                } else if (error.message && error.message.includes('403')) {
                                    errorMessage = __('Access denied. You don\'t have permission to perform this action.', 'give');
                                } else if (error.message) {
                                    // Try to extract a meaningful message from the error
                                    const match = error.message.match(/You don't have permission[^"]*|You don&#039;t have permission[^"]*/i);
                                    if (match) {
                                        errorMessage = match[0].replace(/&#039;/g, "'");
                                    }
                                }
                                
                                // Show error as a notice/alert
                                alert(errorMessage);
                            }
                        }}
                    >
                        {modalContent?.confirmButtonText ?? __('Confirm', 'give')}
                    </button>
                </div>
            </A11yDialog>
        </>
    );
}
