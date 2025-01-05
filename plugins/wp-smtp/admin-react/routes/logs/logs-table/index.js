/**
 * External dependencies
 */
import { useState } from '@wordpress/element';
/**
 * WordPress dependencies
 */
import { CheckboxControl, FlexBlock, FlexItem } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * SolidWP dependencies
 */
import { Button, Text, TextVariant } from '@ithemes/ui';

/**
 * Internal dependencies
 */
import ConfirmationDialog from "../../../components/confirmation-dialog";
import { LogItem, ErrorBadge, SenderEmail, StyledFlex, ActionsBar } from './styles';
import { StyledSurface } from "../styles";
import { useDispatch, useSelect } from "@wordpress/data";
import { STORE_NAME as LogsStore } from "../../../data/src/logs/constants";

/**
 * Component for displaying a table of logs.
 *
 * @param {Object}   props             - The component props.
 * @param {Array}    props.logs        - The array of log entries.
 * @param {Object}   props.selectedLog - The currently selected log entry.
 * @param {Function} props.selectLog   - The function to set the selected log entry.
 */
function LogsTable( { logs, selectedLog, selectLog } ) {
	// define state
	const [ isAllChecked, setIsAllChecked ] = useState( false );
	const [ checkedItems, setCheckedItems ] = useState( [] );
	const [ isDialogOpen, setIsDialogOpen ] = useState( false );
	const [ isDeleting, setIsDeleting ] = useState( false )

	const currentPage = useSelect( ( select ) => select( LogsStore ).getCurrentPage(), [] );
	const { deleteLog } = useDispatch( LogsStore );

	const handleSelectAll = () => {
		const newCheckedState = !isAllChecked;
		setIsAllChecked( newCheckedState );

		// If selecting all, update checkedItems with all log IDs, otherwise clear it
		if ( newCheckedState ) {
			const allIds = logs.map( ( item ) => item.mail_id );
			setCheckedItems( allIds );
		} else {
			setCheckedItems( [] );
		}
	};

	const handleCheckboxChange = ( mail_id ) => {
		setCheckedItems( ( prevCheckedItems ) => {
			// Toggle the ID in the checkedItems array
			if ( prevCheckedItems.includes( mail_id ) ) {
				return prevCheckedItems.filter( id => id !== mail_id );
			} else {
				return [ ...prevCheckedItems, mail_id ];
			}
		} );
	};

	const handleDeleteSelected = () => {
		// Show the confirmation dialog
		setIsDialogOpen(true);
	};

	const handleConfirmDelete = async () => {
		setIsDeleting( true )
		// Perform the actual delete action here
		await deleteLog( checkedItems, currentPage );
		// Close the dialog after deletion
		setIsDialogOpen(false);
		// reset the list
		setCheckedItems( [] )
		// reset the state
		setIsDeleting( false )
	};

	const handleCancelDelete = () => {
		// Simply close the dialog
		setIsDialogOpen(false);
	};

	return (
		<>
			<ActionsBar>
				<StyledFlex gap={ 3 } align={"center"}>
					<CheckboxControl
						checked={ isAllChecked }
						onChange={ handleSelectAll }
					/>
					<FlexBlock>
						<Button
							variant={ "secondary" }
							onClick={ handleDeleteSelected }
							disabled={ checkedItems.length === 0 }>
							{ __( 'Delete Selected', 'LION' ) }
						</Button>
					</FlexBlock>
				</StyledFlex>
			</ActionsBar>
			<StyledSurface>
			{ logs.map( ( item ) => (
				<LogItem
					key={ item.mail_id }
					onClick={ () => {
						selectLog( item );
					} }
					className={
						selectedLog !== null &&
						item.mail_id === selectedLog.mail_id
							? 'active'
							: ''
					}
				>
					<StyledFlex gap={ 3 }>
						<FlexItem>
							<CheckboxControl
								checked={ checkedItems.includes( item.mail_id ) }
								onChange={ () => handleCheckboxChange( item.mail_id ) }
							/>
						</FlexItem>
						<FlexBlock>
							<SenderEmail>{ item.to }</SenderEmail>
							<Text as={ 'p' } variant={ TextVariant.MUTED }>
								{ item.subject }
							</Text>
							{ item.error && (
								<ErrorBadge variant={ 'error' }>
									{ __( 'error', 'LION' ) }
								</ErrorBadge>
							) }
						</FlexBlock>
						<FlexItem>
							<Text>{ item.timestamp }</Text>
						</FlexItem>
					</StyledFlex>
				</LogItem>
			) ) }
			</StyledSurface>

			{ isDialogOpen && (
				<ConfirmationDialog
					onCancel={ handleCancelDelete }
					onContinue={ handleConfirmDelete }
					title={ __( 'Confirm Deletion', 'LION' ) }
					body={ __( 'Are you sure you want to delete the selected logs?', 'LION' ) }
					continueText={ __( 'Delete', 'LION' ) }
					isBusy={ isDeleting }
				/>
			) }
		</>
	);
}

export default LogsTable;
