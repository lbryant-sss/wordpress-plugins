<?php
/**
 * Controller class
 *
 * @author Flipper Code<hello@flippercode.com>
 * @version 3.0.0
 * @package Core
 */

if ( ! class_exists( 'Flippercode_Core_Controller' ) ) {

	/**
	 * Controller class to display views.
	 *
	 * @author Flipper Code<hello@flippercode.com>
	 * @version 3.0.0
	 * @package Core
	 */
	class Flippercode_Core_Controller {

		/**
		 * Store object type
		 *
		 * @var  String
		 */
		private $entity;
		/**
		 * Store entity object return  by factory
		 *
		 * @var Object
		 */
		private $entityObj;
		/**
		 * Store properties of the $entity object.
		 *
		 * @var Array
		 */
		private $entityObjProperties;
		/**
		 * Store path to modules
		 *
		 * @var String
		 */
		private $modulePath;
		/**
		 * Store current module prefix according to plugin for frontend prefix needs.
		 *
		 * @var String
		 */
		private $modulePrefix;
		/**
		 * Store plugin's text domain.
		 *
		 * @var String
		 */
		private $textDomain;


		private $pluginInstance = null;

		function __construct( $objectType, $module_path, $modulePrefix = '', $plugin_controller_obj = '' ) {

			$this->entity     = $objectType;
			$this->modulePath = $module_path;
			$this->modulePrefix = $modulePrefix;
			if ( ! empty( $plugin_controller_obj ) ) {
				$this->pluginInstance = $plugin_controller_obj;
			}

			if ( file_exists( $this->modulePath . $this->entity . '/model.' . $this->entity . '.php' ) ) {
				$factoryObject   = new Flippercode_Factory_Model( $module_path, $modulePrefix );
				$this->entityObj = $factoryObject->create_object( $this->entity );
				if ( is_object( $this->entityObj ) ) {
					$this->entityObjProperties = get_object_vars( $this->entityObj );
				}
			} else {
				$object_name = $modulePrefix . ucwords( $this->entity );
				if ( is_object( new $object_name() ) ) {
					$this->entityObj = new $object_name();
				}
			}

		}
		/**
		 * Load requested views.
		 *
		 * @param  String $view View name.
		 * @param array  $options View Options.
		 */
		public function display( $view, $options = array() ) {

			$this->entity = apply_filters( 'fc_plugin_module_to_load', $this->entity );
			if ( isset( $this->pluginInstance ) && $this->pluginInstance->needs_license_verification() ) {
				if ( $this->entity == 'debug' ) {
					$view = 'form';
				}
			}

			$response = $this->do_action();

			switch ( $view ) {
				default:
					$view = $view . '.php';
			}

			$this->modulePath = apply_filters('fc_backend_module_path', $this->modulePath,$this->entity, $view );
			
			if ( ! empty( $view ) ) {
				if ( file_exists( $this->modulePath . "{$this->entity}/views/" . $view ) ) {
					return include $this->modulePath . "{$this->entity}/views/" . $view;
				} else {
					if ( is_object( $this->entityObj ) ) {
						return $this->entityObj->display( $this->entity, $view, $response ); // Extension Object.
					}
				}
			}
		}
		/**
		 * Return entity name.
		 *
		 * @return String Type of entity.
		 */
		protected function get_entity() {
			return $this->entity;}
		/**
		 * Handle form submissions
		 *
		 * @param  string $action Action name.
		 * @return [type]         Success or Failure response.
		 */
		protected function do_action( $action = '' ) {

			global $wpdb;

			$response = array();

			try {
				if ( isset( $_POST['operation'] ) and sanitize_text_field( wp_unslash( $_POST['operation'] ) ) != '' ) {
					$operation = sanitize_text_field( wp_unslash( $_POST['operation'] ) );
					if ( method_exists( $this->entityObj, $operation ) ) {
						$response = $this->entityObj->$operation();
					}
				}
			} catch ( Exception $e ) {
				$response['error'] = $e->getMessage();
			}

			return $response;
		}
		/**
		 * Handle Add & Edit operations.
		 *
		 * @return Array Success or Failure response.
		 */
		protected function action_add_edit() {

			if ( isset( $_REQUEST['_wpnonce'] ) ) {
					$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ); }

			if ( ! wp_verify_nonce( $nonce, 'wpgmp-nonce' ) ) {

				die( 'Cheating...' );

			}

			$response = array();
			// Ignore changes in these class variables while setting up class object for insertion/updation.
			$properties_to_ignore = array( 'validations', 'table', 'unique' );

			foreach ( $properties_to_ignore as $classproperty ) {

				if ( array_key_exists( $classproperty, $this->entityObjProperties ) ) {
					unset( $this->entityObjProperties[ $classproperty ] ); }
			}

			if ( is_object( $this->entityObjProperties ) ) {

				foreach ( $this->entityObjProperties as $key => $val ) {

					if ( isset( $_POST[ $key ] ) and ! is_array( $_POST[ $key ] ) ) {
						$post_key = sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
					} else {
						$post_key = array_map( 'esc_attr', (array) wp_unslash( $_POST[ $key ] ) );

					}

					if ( isset( $key ) && isset( $post_key ) && is_object( $this->entityObj ) ) {
						$this->entityObj->set_val( $key, $post_key );
					}
				}
			}

			if ( isset( $_POST['entityID'] ) ) {
				// Setting value of Id field in case of edit.
				$this->entityObj->set_val( $this->entity . '_id', intval( wp_unslash( $_POST['entityID'] ) ) ); }

			if ( $this->entityObj->save() > 0 ) {

					$current_obj_name = ucfirst( $this->entity );
					$_POST            = array();
			}

			return $response;
		}
		/**
		 * Handle import locations action.
		 *
		 * @return Array Success or Failure Information.
		 */
		public function action_import_location() {
			$response = $this->entityObj->import_location();
			return $response; }
		/**
		 * Handle Backup action.
		 *
		 * @return Array Success or Failure Information.
		 */
		public function action_take_backup() {
			$response = $this->entityObj->take_backup();
			return $response; }
		/**
		 * Handle upload backup action.
		 *
		 * @return Array Success or Failure Information.
		 */
		public function action_upload_backup() {
			$response = $this->entityObj->upload_backup();
			return $response; }
		/**
		 * Handle import backup action.
		 *
		 * @return Array Success or Failure Information.
		 */
		public function action_import_backup() {
			$response = $this->entityObj->import_backup();
			return $response; }

	}
}
