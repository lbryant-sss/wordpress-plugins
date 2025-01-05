/**
 * Selectors to access store state.
 */
const selectors = {
	/**
	 * Get the list of providers.
	 *
	 * @param {Object} state - The current state.
	 * @return {Array} The list of providers.
	 */
	getConnections( state ) {
		return state.connections;
	},

	/**
	 * Get the localized texts.
	 *
	 * @param {Object} state - The current state.
	 * @return {Object} The localized texts.
	 */
	getTexts( state ) {
		return state.texts;
	},

	/**
	 * Get the available providers.
	 *
	 * @param {Object} state - The current state.
	 * @return {Object} The available providers.
	 */
	getAvailableConnections( state ) {
		return state.availableConnections;
	},

	/**
	 * Get the list of errors.
	 *
	 * @param {Object} state - The current state.
	 * @return {Array} The list of errors.
	 */
	getErrors( state ) {
		return state.errors;
	},

	/**
	 * Get the flags state.
	 *
	 * @param {Object} state - The current state.
	 * @return {Object} The flags state.
	 */
	getFlags( state ) {
		return state.flags;
	},
	/**
	 * Get the connection by ID.
	 *
	 * @param {Object} state - The current state.
	 * @param {string} ID    - The ID of the connection.
	 * @return {Object|null} The connection if found, otherwise null.
	 */
	getConnectionById( state, ID ) {
		return (
			state.connections.find( ( connection ) => connection.id === ID ) ||
			null
		);
	},
	/**
	 * Get the list of toast notifications.
	 *
	 * @param {Object} state - The current state.
	 * @return {Array} The list of toast notifications.
	 */
	getToasts( state ) {
		return state.toasts;
	},

	/**
	 * Determines if the newly added connection should be set as active.
	 * The first connection added will be considered active by default.
	 *
	 * @param {Object} state - The current state object.
	 * @param {Array} state.connections - List of existing connections.
	 * @returns {boolean} - Returns true if there are no existing connections, false otherwise.
	 */
	shouldSetActive( state ) {
		return state.connections.length === 0
	}
};

export default selectors;
