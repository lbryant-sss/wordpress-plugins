/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { ToggleControl } from '@wordpress/components';

/**
 * SolidWP dependencies
 */
import { Button, Text } from '@ithemes/ui';

/**
 * Internal dependencies
 */
import {
	BrevoIcon,
	SendGridIcon,
	AwsIcon,
	MailIcon,
	MailGunIcon,
	PostmarkIcon,
} from '../../../components/icons';
import { ConnectionsTableRow } from '../connections-list/styles';
import {
	ConnectionActions,
	ConnectionInfo,
	ConnectionInfoImage,
	ConnectionInfoName,
	ConnectionToggle,
} from './styles';

/**
 * Component to display a connection item with actions to set it as active, edit, or delete.
 *
 * @param {Object}   props                       - Component properties.
 * @param {Object}   props.connection            - The connection data.
 * @param {Function} props.onSetConnectionActive - Function to call when setting the connection as active.
 * @param {Function} props.onDeleteButtonClick   - Function to call when deleting the connection.
 * @param {Function} props.onEditButtonClick     - Function to call when editing the connection.
 * @return {JSX.Element} The ConnectionItem component.
 */
function ConnectionItem( {
	connection,
	onSetConnectionActive,
	onDeleteButtonClick,
	onEditButtonClick,
} ) {
	return (
		<ConnectionsTableRow>
			<ConnectionInfo>
				<ConnectionInfoImage>
					{ connection.name === 'brevo' && <BrevoIcon /> }
					{ connection.name === 'sendgrid' && <SendGridIcon /> }
					{ connection.name === 'mailgun' && <MailGunIcon /> }
					{ connection.name === 'amazon_ses' && <AwsIcon /> }
					{ connection.name === 'postmark' && <PostmarkIcon /> }
					{ connection.name === 'other' && <MailIcon /> }
				</ConnectionInfoImage>
				<ConnectionInfoName>
					<Text as="p" weight={ 500 }>
						{ connection.display_name }
					</Text>
					<Text variant="muted">
						-{ ' ' }
						{ sprintf(
							/* translators: 1. From Name */
							__( 'From: %s', 'LION' ),
							connection.from_name
						) }
					</Text>
					<Text variant="muted">
						-{ ' ' }
						{ sprintf(
							/* translators: 1. Email address */
							__( 'From Email: %s', 'LION' ),
							connection.from_email
						) }
					</Text>
				</ConnectionInfoName>
			</ConnectionInfo>
			<ConnectionToggle>
				<ToggleControl
					label=""
					onChange={ ( value ) => {
						onSetConnectionActive( connection.id, value );
					} }
					checked={ connection.is_active }
				/>
			</ConnectionToggle>
			<ConnectionActions>
				<Button
					icon={ 'edit' }
					variant="secondary"
					onClick={ () => {
						onEditButtonClick( connection.id );
					} }
				>
					Edit
				</Button>
				<Button
					disabled={ connection.is_active }
					icon={ 'trash' }
					onClick={ () => onDeleteButtonClick( connection.id ) }
					variant="secondary"
				>
					Delete
				</Button>
			</ConnectionActions>
		</ConnectionsTableRow>
	);
}

export default ConnectionItem;
