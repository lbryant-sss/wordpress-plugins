<?php
/**
 * Cloud Connect
 * Called for the Token Activation
 *
 * @package HMWP/Connect
 * @file The Cloud Connect file
 */

defined( 'ABSPATH' ) || die( 'Cheatin\' uh?' );

class HMWP_Controllers_Connect extends HMWP_Classes_FrontController {

	/**
	 * Called when an action is triggered
	 *
	 * @throws Exception
	 */
	public function action() {
		parent::action();

		// Check user permission.
		if ( ! HMWP_Classes_Tools::userCan( HMWP_CAPABILITY ) ) {
			return;
		}

		switch ( HMWP_Classes_Tools::getValue( 'action' ) ) {

			case 'hmwp_connect':
				// Connect to API with the Email
				$email = sanitize_email( HMWP_Classes_Tools::getValue( 'hmwp_email', '' ) );
				$token = HMWP_Classes_Tools::getValue( 'hmwp_token', '' );

				$redirect_to = HMWP_Classes_Tools::getSettingsUrl();
				if ( $token <> '' ) {
					if ( preg_match( '/^[a-z0-9\-]{32}$/i', $token ) ) {
						HMWP_Classes_Tools::saveOptions( 'hmwp_token', $token );
						HMWP_Classes_Tools::saveOptions( 'error', false );
						HMWP_Classes_Tools::checkAccountApi();

						// Save the working options into backup
						HMWP_Classes_Tools::saveOptionsBackup();

					} else {
						HMWP_Classes_Error::setNotification( __( 'ERROR! Please make sure you use a valid token to activate the plugin', 'hide-my-wp' ) . " <br /> " );
					}
				} elseif ( $email <> '' ) {
					HMWP_Classes_Tools::checkAccountApi( $email, $redirect_to );
				} else {
					HMWP_Classes_Error::setNotification( __( 'ERROR! Please make sure you use the right token to activate the plugin', 'hide-my-wp' ) . " <br /> " );
				}
				break;

			case 'hmwp_reconnect':

				// Clear the current connection and start over
				$redirect_to = HMWP_Classes_Tools::getSettingsUrl();

				HMWP_Classes_Tools::saveOptions( 'hmwp_token', false );
				HMWP_Classes_Tools::saveOptions( 'error', false );

				wp_redirect( $redirect_to );
				exit();

			case 'hmwp_dont_connect':

				// If skipped activation, generate a random local token
				$redirect_to = HMWP_Classes_Tools::getSettingsUrl();

				HMWP_Classes_Tools::saveOptions( 'hmwp_token', md5( home_url() ) );
				HMWP_Classes_Tools::saveOptions( 'error', false );

				// Save the working options into backup
				HMWP_Classes_Tools::saveOptionsBackup();

				wp_redirect( $redirect_to );
				exit();

		}


	}

}
