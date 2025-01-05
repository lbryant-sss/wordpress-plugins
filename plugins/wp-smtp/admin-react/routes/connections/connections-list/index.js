/**
 * External dependencies
 */
/**
 * External dependencies
 */
import { useState } from '@wordpress/element';
import { useNavigate } from 'react-router-dom';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useDispatch } from '@wordpress/data';

/**
 * SolidWP dependencies
 */
import { Button, Surface } from '@ithemes/ui';

/**
 * Internal dependencies
 */
import { STORE_NAME } from '../../../data/src/connections/constants';
import ConfirmationDialog from "../../../components/confirmation-dialog";
import ConnectionItem from '../connection-item';
import {
	ConnectionsTable,
	ConnectionsTableRow,
	StyledFlex,
	StyledText,
} from './styles';


/**
 * Component to display a list of connections with actions to add, set active, edit, or delete connections.
 *
 * @param {Object} props             - Component properties.
 * @param {Array}  props.connections - Array of connection objects.
 * @return {JSX.Element} The ConnectionsList component.
 */
function ConnectionsList( { connections } ) {
	const { setConnectionActive, deleteConnection } = useDispatch( STORE_NAME );
	const [ deleteConnectionID, setDeleteConnectionID ] = useState( false )
	const [ confirmationDialogState, setConfirmDialogState ] = useState( 'closed' )
	const [ isDeleting, setIsDeleting ] = useState( false )

	const navigate = useNavigate();
	const onSetConnectionActive = ( id, value ) => {
		if ( value ) {
			setConnectionActive( id );
		}
	};

	const onDeleteButtonClick = ( id ) => {
		setDeleteConnectionID( id );
		setConfirmDialogState( 'open' )
	};

	const onEditButtonClick = ( id ) => {
		navigate( 'edit/' + id );
	};

	const confirmDelete = async () => {
		setIsDeleting( true );
		await deleteConnection( deleteConnectionID );
		setConfirmDialogState( 'close' );
		setDeleteConnectionID( false );
		setIsDeleting( false );
	};

	const cancelDelete = () => {
		setConfirmDialogState( 'close' );
		setDeleteConnectionID( false );
	};

	return (
		<>
			<StyledFlex justify={ 'right' }>
				<Button
					variant={ 'primary' }
					icon={ 'plus' }
					onClick={ () => {
						navigate( 'add' );
					} }
				>
					{ __( 'Add new Connection', 'LION' ) }
				</Button>
			</StyledFlex>
			<Surface variant="primary">
				<ConnectionsTable>
					<ConnectionsTableRow>
						<StyledText weight={ 500 }>
							{ __( 'Provider', 'LION' ) }
						</StyledText>
						<StyledText weight={ 500 } hideOnSmall={ true }>
							{ __( 'Active Connection', 'LION' ) }
						</StyledText>
						<StyledText weight={ 500 }>
							{ __( 'Actions', 'LION' ) }
						</StyledText>
					</ConnectionsTableRow>
					{ connections.map( ( item, index ) => (
						<ConnectionItem
							connection={ item }
							key={ index }
							onSetConnectionActive={ onSetConnectionActive }
							onDeleteButtonClick={ onDeleteButtonClick }
							onEditButtonClick={ onEditButtonClick }
						/>
					) ) }
				</ConnectionsTable>
			</Surface>
			{
				( confirmationDialogState === 'open' && deleteConnectionID !== false ) && (
					<ConfirmationDialog
						onCancel={ cancelDelete }
						onContinue={ confirmDelete }
						title={ __( 'Confirm Deletion', 'LION' ) }
						body={ __( 'Are you sure you want to delete this item?', 'LION' ) }
                        isBusy={ isDeleting }
						continueText={ __( 'Delete', 'LION' ) }/>
				)
			}
		</>
	);
}

export default ConnectionsList;
