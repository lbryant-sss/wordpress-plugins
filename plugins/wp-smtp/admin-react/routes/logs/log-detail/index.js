/**
 * External dependencies
 */
import { useState } from '@wordpress/element';

/**
 * WordPress dependencies
 */
import { useDispatch, useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

/**
 * SolidWP dependencies
 */
import { Button, Text, TextSize, TextVariant, TextWeight } from '@ithemes/ui';

/**
 * Internal dependencies
 */
import ConfirmationDialog from '../../../components/confirmation-dialog';
import { STORE_NAME as LogsStore } from '../../../data/src/logs/constants';
import { Logo } from '../../../components/icons';
import Message from '../message';
import { Body, Empty, Header, StyledNotice, StyledSurface } from './styles';

/**
 * Component for displaying the details of a log.
 */
function LogDetail() {
	const { selectedLog, currentPage } = useSelect( ( select ) => ( {
		selectedLog: select( LogsStore ).getSelectedLog(),
		currentPage: select( LogsStore ).getCurrentPage(),
	} ), [] );
	const { deleteLog } = useDispatch( LogsStore );
	const [ isDialogOpen, setIsDialogOpen ] = useState( false );
	const [ isDeleting, setIsDeleting ] = useState( false );

	// Handle delete confirmation
	const handleDelete = () => {
		setIsDialogOpen( true );
	};

	// Confirm deletion and proceed with log deletion
	const handleConfirmDelete = async () => {
		setIsDeleting( true );
		await deleteLog( [ selectedLog.mail_id ], currentPage );
		setIsDialogOpen( false );
		setIsDeleting( false );
	};

	// Cancel deletion
	const handleCancelDelete = () => {
		setIsDialogOpen( false );
	};

	if ( selectedLog === null || selectedLog === undefined ) {
		return (
			<Empty>
				<Logo />
			</Empty>
		);
	}
	return (
		<>
			{ selectedLog.error !== null && selectedLog.error.length > 0 && (
				<StyledNotice text={ selectedLog.error } type={ 'danger' } />
			) }
			<Header>
				<Text weight={ TextWeight.HEAVY }>{ selectedLog.to.join( ', ' ) }</Text>
				<Text weight={ TextWeight.HEAVY }>
					{ selectedLog.timestamp }
				</Text>
			</Header>
			<Body>
				<Text variant={ TextVariant.DARK } as={ 'p' }>
					{ __( 'Subject', 'LION' ) }:{ ' ' }
					<Text size={ TextSize.LARGE } weight={ 600 }>
						{ selectedLog.subject }
					</Text>
				</Text>
				<Text variant={ TextVariant.DARK } as={ 'p' }>
					{ __( 'Body', 'LION' ) }
				</Text>
				<StyledSurface>
					<Message email={ selectedLog } key={ selectedLog.mail_id } />
				</StyledSurface>
				<Button
					variant={ 'secondary' }
					onClick={ handleDelete }
					icon={ 'trash' }
				>
					{ __( 'Delete', 'LION' ) }
				</Button>
			</Body>
			{ isDialogOpen && (
				<ConfirmationDialog
					onCancel={ handleCancelDelete } // Cancel action
					onContinue={ handleConfirmDelete } // Confirm and delete
					title={ __( 'Confirm Deletion', 'LION' ) }
					body={ __( 'Are you sure you want to delete this log?', 'LION' ) }
					continueText={ __( 'Delete', 'LION' ) }
					isBusy={ isDeleting }
				/>
			) }
		</>
	);
}

export default LogDetail;
