/**
 * External dependencies
 */
import { produce } from 'immer';

/**
 * Internal dependencies
 */
import { DEFAULT_STATE } from './constants';
import {
	ADD_CONNECTION,
	ADD_TOAST,
	CLEAR_ERRORS,
	CLEAR_FLAGS,
	REFRESH_CONNECTIONS,
	REMOVE_TOAST,
} from './actions';

/**
 * Reducer to handle state changes.
 *
 * @param {Object} state  - The current state.
 * @param {Object} action - The action object.
 * @return {Object} The new state.
 */
export default function reducer( state = DEFAULT_STATE, action ) {
	switch ( action.type ) {
		case ADD_CONNECTION:
			if ( action.data.success === true ) {
				return produce( state, ( draft ) => {
					draft.connections = Object.values( action.data.data );
					draft.flags.connection_saved = true;
					draft.errors = [];
					draft.toasts.push( action.toast );
				} );
			}

			return produce( state, ( draft ) => {
				draft.errors = action.data.data;
			} );

		case CLEAR_FLAGS:
			return produce( state, ( draft ) => {
				draft.flags[ action.data ] = false;
			} );

		case REMOVE_TOAST:
			return produce( state, ( draft ) => {
				draft.toasts = draft.toasts.filter(
					( toast ) => toast.id !== action.data
				);
			} );

		case REFRESH_CONNECTIONS:
			return produce( state, ( draft ) => {
				draft.connections = Object.values( action.data );
				draft.toasts.push( action.toast );
			} );
		case CLEAR_ERRORS:
			return produce( state, ( draft ) => {
				draft.errors = [];
			} );
		case ADD_TOAST:
			return produce( state, ( draft ) => {
				draft.toasts.push( action.toast );
			} );
		default:
			return state;
	}
}
