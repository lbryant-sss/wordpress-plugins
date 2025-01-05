/**
 * External dependencies
 */
import { useState } from '@wordpress/element';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Flex, FlexItem } from '@wordpress/components';
import { useDispatch } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

/**
 * SolidWP dependencies
 */
import { Button } from '@ithemes/ui';

/**
 * Internal dependencies
 */
import { STORE_NAME as connectionStore } from '../../data/src/connections/constants';
import { FormTextInput, FormTextarea } from '../../components/form';
import { StyledSurface } from '../../assets/common';
import { Container } from './styles';
import qs from 'qs';

/**
 * Component for sending a test email.
 *
 * @return {JSX.Element} The rendered EmailTest component.
 */
const EmailTest = () => {
	const [ toEmail, setToEmail ] = useState( '' );
	const [ subject, setSubject ] = useState( '' );
	const [ message, setMessage ] = useState( '' );
	const [ loading, setLoading ] = useState( false );
	const [ errors, setErrors ] = useState( [] );

	const { addToast } = useDispatch( connectionStore );

	const handleSendTestEmail = async ( event ) => {
		event.preventDefault();
		try {
			setLoading( true );
			// const response = await sendTestEmail( toEmail, subject, message );
			const response = await apiFetch( {
				url:
					addQueryArgs( ajaxurl, {
						action: 'solidwp_mail_send_test_email',
						solidwp_mail_connections_nonce: SolidWPMail.nonces.send_test_email,
					} ),
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: qs.stringify( {
					to_email: toEmail,
					subject,
					message,
				} ),
			} );
			if ( response.success === false && response.data.validation ) {
				setErrors( response.data.validation );
				addToast(
					__(
						'Validation failed. Please check the highlighted fields and try again.',
						'LION'
					)
				);
			} else {
				addToast( response.data.message );
			}
			setLoading( false );
		} catch ( error ) {
			addToast(
				__(
					'Error sending test email',
					'LION'
				)
			);
		}
	};

	return (
		<Container>
			<form method="post" onSubmit={ handleSendTestEmail }>
				<StyledSurface>
					<FormTextInput
						label={ __( 'To Email', 'LION' ) }
						value={ toEmail }
						error={ errors.to_email }
						onChange={ setToEmail }
						description={ __( "Enter the recipient's email address", 'LION' ) }
					/>
					<FormTextInput
						label={ __( 'Subject', 'LION' ) }
						value={ subject }
						error={ errors.subject }
						onChange={ setSubject }
						description={ __( 'Provide the subject of the test email.', 'LION' ) }
					/>
					<FormTextarea
						label={ __( 'Message', 'LION' ) }
						value={ message }
						error={ errors.message }
						onChange={ setMessage }
						description={ __( 'Enter the email message', 'LION' ) }
					/>
				</StyledSurface>
				<Flex align={ 'flex-end' } direction={ 'column' }>
					<FlexItem>
						<Button
							disabled={ loading }
							isBusy={ loading }
							variant={ 'primary' }
							type={ 'submit' }
						>
							{ __( 'Send Test Email', 'LION' ) }
						</Button>
					</FlexItem>
				</Flex>
			</form>
		</Container>
	);
};

export default EmailTest;
