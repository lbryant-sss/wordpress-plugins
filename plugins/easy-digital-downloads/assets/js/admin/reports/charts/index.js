/* global eddAdminReportsCharts */

/**
 * Internal dependencies.
 */
import { render as lineChartRender } from './line.js';
import { render as pieChartRender } from './pie.js';
import { isPieChart } from './utils.js';

// Access existing global `edd` variable, or create a new object.
window.edd = window.edd || {};

/**
 * Render a chart based on config.
 *
 * This function is attached to the `edd` property attached to the `window`.
 *
 * @param {Object} config Chart config.
 */
window.edd.renderChart = ( config ) => {
	if ( isPieChart( config ) ) {
		pieChartRender( config );
	} else {
		lineChartRender( config );
	}
};
