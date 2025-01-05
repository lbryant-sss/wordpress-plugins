/**
 * WordPress dependencies
 */
import { createReduxStore, register } from '@wordpress/data';

/**
 * Internal dependencies
 */
import reducer from './reducer';
import * as actions from './actions';
import selectors from './selectors';
import controls from './controls';
import resolvers from './resolvers';
import { STORE_NAME } from './constants';

/**
 * Create and register the store.
 *
 * @constant {Object} store - The created Redux store with specified actions, selectors, resolvers, controls, and reducer.
 */
const store = createReduxStore( STORE_NAME, {
	actions,
	selectors,
	resolvers,
	controls,
	reducer,
} );

/**
 * Register the created store.
 */
register( store );
