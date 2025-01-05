/**
 * External dependencies
 */
import qs from 'qs';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

/**
 * Action to add a new provider.
 *
 * @param {Object} provider - The provider details.
 * @return {Object} The action object.
 */
export async function addProvider( provider ) {
	const result = await apiFetch( {
		url:
			addQueryArgs( ajaxurl, {
				action: 'solidwp_mail_save_connection',
				solidwp_mail_connections_nonce: SolidWPMail.nonces.save_connection,
			} ),
		method: 'POST',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded',
		},
		body: qs.stringify( provider ),
	} );
	return {
		type: ADD_CONNECTION,
		data: result,
		toast: {
			id: Date.now(),
			message: __( 'The provider has been added successfully.', 'LION' ),
		},
	};
}

/**
 * Action to clear a specific flag.
 *
 * @param {string} name - The name of the flag to clear.
 * @return {Object} The action object.
 */
export function clearFlags( name ) {
	return {
		type: CLEAR_FLAGS,
		data: name,
	};
}

/**
 * Action to remove a toast notification.
 *
 * @param {string} ID - The unique identifier of the toast notification to remove.
 * @return {Object} The action object.
 */
export function removeToast( ID ) {
	return {
		type: REMOVE_TOAST,
		data: ID,
	};
}

/**
 * Sets the connection as active and returns an action to refresh connections.
 *
 * @async
 * @function
 * @param {string} ID - The ID of the connection to set as active.
 * @return {Promise<Object>} The action object containing the type, data, and toast message.
 */
export async function setConnectionActive( ID ) {
	// const result = await makeProviderActive( ID );
	const result = await apiFetch( {
		url:
			addQueryArgs( ajaxurl, {
				action: 'solidwp_mail_make_provider_active',
				solidwp_mail_connections_nonce: SolidWPMail.nonces.make_connection_active,
			} ),
		method: 'POST',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded',
		},
		body: qs.stringify( {
			provider_id: ID,
			'solidwp-mail-connections-nonce':
			SolidWPMail.nonces.make_connection_active,
		} ),
	} );
	return {
		type: REFRESH_CONNECTIONS,
		data: result.data,
		toast: {
			id: Date.now(),
			message: __(
				'The connection has been set as the default.',
				'LION'
			),
		},
	};
}

/**
 * Deletes a connection and returns an action to refresh connections.
 *
 * @param {string} ID - The ID of the connection to delete.
 * @return {Promise<Object>} The action object containing the type and data.
 */
export async function deleteConnection( ID ) {
	// const result = await deleteProvider( ID );
	const result = await apiFetch( {
		url:
			addQueryArgs( ajaxurl, {
				action: 'solidwp_mail_delete_connection',
				solidwp_mail_connections_nonce: SolidWPMail.nonces.delete_connection,
			} ),
		method: 'POST',
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded',
		},
		body: qs.stringify( {
			provider_id: ID,
			'solidwp-mail-connections-nonce':
			SolidWPMail.nonces.delete_connection,
		} ),
	} );
	return {
		type: REFRESH_CONNECTIONS,
		data: result.data,
		toast: {
			id: Date.now(),
			message: __( 'The connection has been deleted.', 'LION' ),
		},
	};
}

/**
 * Clear all the errors that happen from previous submit.
 */
export async function clearErrors() {
	return {
		type: CLEAR_ERRORS,
	};
}

export function addToast( message ) {
	return {
		type: ADD_TOAST,
		toast: {
			id: Date.now(),
			message,
		},
	};
}

export const ADD_CONNECTION = 'ADD_CONNECTION';
export const CLEAR_FLAGS = 'CLEAR_FLAGS';
export const REFRESH_CONNECTIONS = 'REFRESH_CONNECTIONS';
export const REMOVE_TOAST = 'REMOVE_TOAST';
export const CLEAR_ERRORS = 'CLEAR_ERRORS';
export const ADD_TOAST = 'ADD_TOAST';
