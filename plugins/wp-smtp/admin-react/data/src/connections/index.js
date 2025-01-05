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
 */
const store = createReduxStore( STORE_NAME, {
	actions,
	selectors,
	resolvers,
	controls,
	reducer,
} );

register( store );
