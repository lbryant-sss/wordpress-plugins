<?php

/**
 * Manages settings compatibility helpers. Those allow us to make
 * changes to node settings without breaking compatibility with
 * existing nodes that have already been saved to the database.
 *
 * @since 2.2
 */
final class FLBuilderSettingsCompat {

	/**
	 * An array of registered compatibility helpers.
	 *
	 * @since 2.2
	 * @var array $helpers
	 */
	static private $helpers = array();

	/**
	 * Initialize.
	 *
	 * @since 2.2
	 * @return void
	 */
	static public function init() {
		require_once FL_BUILDER_DIR . 'classes/compat/class-fl-builder-settings-compat-helper.php';
		require_once FL_BUILDER_DIR . 'classes/compat/class-fl-builder-settings-compat-generic.php';
		require_once FL_BUILDER_DIR . 'classes/compat/class-fl-builder-settings-compat-row.php';
		require_once FL_BUILDER_DIR . 'classes/compat/class-fl-builder-settings-compat-column.php';
		require_once FL_BUILDER_DIR . 'classes/compat/class-fl-builder-settings-compat-module.php';

		self::register_helper( 'generic', 'FLBuilderSettingsCompatGeneric' );
		self::register_helper( 'row', 'FLBuilderSettingsCompatRow' );
		self::register_helper( 'column', 'FLBuilderSettingsCompatColumn' );
		self::register_helper( 'module', 'FLBuilderSettingsCompatModule' );
	}

	/**
	 * Registers a compatibility helper for a node.
	 *
	 * @since 2.2
	 * @param string $type
	 * @param string $class
	 * @return void
	 */
	static public function register_helper( $type, $class ) {
		self::$helpers[ $type ] = new $class();
	}

	/**
	 * Loops through layout data and ensures node settings
	 * are backwards compatible.
	 *
	 * @since 2.2
	 * @param object data
	 * @return object
	 */
	static public function filter_layout_data( $data ) {
		foreach ( $data as $node_id => $node ) {
			if ( isset( $node->settings ) && is_object( $node->settings ) ) {
				$data[ $node_id ]->settings = self::filter_node_settings( $node );
			}
		}
		return $data;
	}

	/**
	 * Ensures settings are backwards compatible for a single node.
	 *
	 * @since 2.2
	 * @param object $node
	 * @return object
	 */
	static public function filter_node_settings( $node ) {
		$type     = $node->type;
		$settings = $node->settings;

		// Filter raw module settings without defaults first.
		if ( 'module' === $type && isset( FLBuilderModel::$modules[ $settings->type ] ) ) {
			$module   = FLBuilderModel::$modules[ $settings->type ];
			$defaults = FLBuilderModel::get_node_defaults( $node );
			$settings = $module->filter_raw_settings( $settings, $defaults );
		}

		// Make sure the defaults are merged.
		$settings = FLBuilderModel::get_node_settings_with_defaults_merged( $node );

		// Filter with the generic helper for all node types.
		$settings = self::$helpers['generic']->filter_settings( $settings );

		// Filter with a node specific helper if one is available.
		$helper = isset( self::$helpers[ $type ] ) ? self::$helpers[ $type ] : null;

		if ( $helper ) {
			$settings = $helper->filter_settings( $settings );
		}

		// Filter with a module specific helper if one is available.
		if ( 'module' === $type && isset( FLBuilderModel::$modules[ $settings->type ] ) ) {
			$module   = FLBuilderModel::$modules[ $settings->type ];
			$settings = $module->filter_settings( $settings, self::$helpers['module'] );
		}

		return $settings;
	}
}

FLBuilderSettingsCompat::init();
