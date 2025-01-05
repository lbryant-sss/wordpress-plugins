/**
 * External dependencies
 */
import { createRoot } from '@wordpress/element';
import { createHashRouter, RouterProvider } from 'react-router-dom';

/**
 * Internal dependencies
 */
import Logs from './routes/logs';

/**
 * Sets up the router configuration.
 */
const router = createHashRouter( [
	{
		path: '/',
		element: <Logs />,
	},
] );

/**
 * Retrieves the root element and initializes the React application.
 */
const rootElement = document.getElementById( 'solidwp-mail-root' );
const root = createRoot( rootElement );

/**
 * Renders the application with the RouterProvider.
 */
root.render( <RouterProvider router={ router } /> );
