/**
 * External dependencies
 */
import { useEffect } from '@wordpress/element';
import { useNavigate } from 'react-router-dom';

/**
 * WordPress dependencies
 */
import { useSelect, useDispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { Flex } from '@wordpress/components';

/**
 * SolidWP dependencies
 */
import { Button } from '@ithemes/ui';

/**
 * Internal dependencies
 */
import { STORE_NAME as connectionsStore } from '../../../data/src/connections/constants';
import SmtpConnector from '../../../routes/connections/connectors/smtp-provider';
import Brevo from '../../../routes/connections/connectors/brevo';
import AmazonSes from '../../../routes/connections/connectors/amazon-ses';
import Sendgrid from '../../../routes/connections/connectors/sendgrid';
import Mailgun from '../../../routes/connections/connectors/mailgun';
import Postmark from "../../../routes/connections/connectors/postmark";
import { FormToggle } from "../../../components/form";
import { StyledSurface } from "../../../assets/common";


function ConnectionForm( { model, setModel } ) {
	const navigate = useNavigate();

	const { errors, flags, texts } = useSelect( ( select ) => ( {
		errors: select( connectionsStore ).getErrors(),
		flags: select( connectionsStore ).getFlags(),
		texts: select( connectionsStore ).getTexts(),
	} ), [] );

	const { addProvider, clearFlags, clearErrors } =
		useDispatch( connectionsStore );

	useEffect( () => {
		if ( flags.connection_saved === true ) {
			clearFlags( 'connection_saved' );
			navigate( '/' );
		}
	}, [ flags.connection_saved, clearFlags, navigate ] );

	useEffect( () => {
		// clear the error when this component loaded.
		clearErrors();
	}, [ clearErrors ] );

	function handleFormPost( event ) {
		event.preventDefault();
		addProvider( model );
	}

	/**
	 * Handles the change in input fields for the SMTP provider form.
	 *
	 * @param {string} name  - The name of the input field.
	 * @param {string} value - The value of the input field.
	 */
	const handleInputChange = ( name, value ) => {
		setModel( ( prevProvider ) => ( {
			...prevProvider,
			[ name ]: value,
		} ) );
	};

	return (
		<form method={ 'post' } onSubmit={ handleFormPost }>
			{ 'other' === model.name && (
				<SmtpConnector
					model={ model }
					handleInputChange={ handleInputChange }
					errors={ errors }
					texts={ texts }
				/>
			) }
			{ 'brevo' === model.name && (
				<Brevo
					model={ model }
					handleInputChange={ handleInputChange }
					errors={ errors }
					texts={ texts }
				/>
			) }
			{ 'mailgun' === model.name && (
				<Mailgun
					model={ model }
					handleInputChange={ handleInputChange }
					errors={ errors }
					texts={ texts }
				/>
			) }
			{ 'sendgrid' === model.name && (
				<Sendgrid
					model={ model }
					handleInputChange={ handleInputChange }
					errors={ errors }
					texts={ texts }
				/>
			) }
			{ 'amazon_ses' === model.name && (
				<AmazonSes
					model={ model }
					handleInputChange={ handleInputChange }
					errors={ errors }
					texts={ texts }
				/>
			) }
      { 'postmark' === model.name && (
				<Postmark
					model={ model }
					handleInputChange={ handleInputChange }
					errors={ errors }
					texts={ texts }
				/>
			) }
			<StyledSurface>
				<FormToggle
					label={ __( 'Is Active', 'LION' ) }
					name="is_active"
					value={ model.is_active }
					onChange={ ( value ) => {
						handleInputChange( 'is_active', value )
					} }
					help={ texts.is_active }
				/>
			</StyledSurface>
			
			<Flex justify={ 'end' } direction={ 'row' } gap={ 5 }>
				<Button
					variant={ 'secondary' }
					type={ 'button' }
					onClick={ () => navigate( '/providers' ) }
				>
					{ __( 'Back to Connections', 'LION' ) }
				</Button>
				<Button variant={ 'primary' } type={ 'submit' }>
					{ __( 'Save Connection', 'LION' ) }
				</Button>
			</Flex>

		</form>
	);
}

export default ConnectionForm;
