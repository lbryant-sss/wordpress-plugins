<?php
/**
 * API endpoint /sites/%s/delete-backup-helper-script
 * This API endpoint deletes a Jetpack Backup Helper Script
 *
 * @package automattic/jetpack
 */

use Automattic\Jetpack\Backup\V0005\Helper_Script_Manager;

/**
 * API endpoint /sites/%s/delete-backup-helper-script
 * This API endpoint deletes a Jetpack Backup Helper Script
 *
 * @phan-constructor-used-for-side-effects
 */
class Jetpack_JSON_API_Delete_Backup_Helper_Script_Endpoint extends Jetpack_JSON_API_Endpoint {
	/**
	 * This endpoint is only accessible from Jetpack Backup; it requires no further capabilities.
	 *
	 * @var array
	 */
	protected $needed_capabilities = array();

	/**
	 * Method to call when running this endpoint (delete)
	 *
	 * @var string
	 */
	protected $action = 'delete';

	/**
	 * Local path to the Helper Script to delete.
	 *
	 * @var string|null
	 */
	protected $script_path = null;

	/**
	 * An array with 'success' => true if the specified file has been successfully deleted, or an instance of WP_Error.
	 *
	 * @var array|WP_Error
	 */
	protected $result;

	/**
	 * Checks that the input args look like a valid Helper Script path.
	 *
	 * @param  null $object  Unused.
	 * @return bool|WP_Error a WP_Error object or true if the input seems ok.
	 */
	protected function validate_input( $object ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$args = $this->input();

		if ( ! isset( $args['path'] ) ) {
			return new WP_Error( 'invalid_args', __( 'You must specify a helper script path', 'jetpack' ), 400 );
		}

		$this->script_path = $args['path'];
		return true;
	}

	/**
	 * Deletes the specified Helper Script.
	 */
	protected function delete() {
		$delete_result = Helper_Script_Manager::delete_helper_script( $this->script_path );
		Helper_Script_Manager::cleanup_expired_helper_scripts();

		if ( is_wp_error( $delete_result ) ) {
			$this->result = $delete_result;
		} else {
			$this->result = array( 'success' => true );
		}
	}

	/**
	 * Returns the success or failure of the deletion operation
	 *
	 * @return array An array containing one key; 'success', which specifies whether the operation was successful.
	 */
	protected function result() {
		return $this->result;
	}
}
