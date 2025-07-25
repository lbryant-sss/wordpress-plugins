<?php
/**
 * Gutenberg-specific scripts.
 */

/**
 * Gutenberg editor assets.
 */
function exactmetrics_gutenberg_editor_assets() {
	global $wp_scripts;

	// stop loading gutenberg related assets/blocks/sidebars if WP version is less than 5.4
	if ( ! exactmetrics_load_gutenberg_app() ) {
		return;
	}

	if ( function_exists( 'get_current_screen' ) ) {
		$current_screen = get_current_screen();

		if ( is_object( $current_screen ) && 'widgets' === $current_screen->id ) {
			return;
		}
	}

	$suffix = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';
	wp_enqueue_script( 'lodash', includes_url('js') . '/underscore.min.js' );
	// @TODO Robo minification is breaking the editor. We will use the main version for now.
	$plugins_js_path    = '/assets/gutenberg/js/editor.js';
	$plugins_style_path = '/assets/gutenberg/css/editor.css';
	$version_path       = exactmetrics_is_pro_version() ? 'pro' : 'lite';

	$plugins_js_url = apply_filters(
		'exactmetrics_editor_scripts_url',
		plugins_url( $plugins_js_path, EXACTMETRICS_PLUGIN_FILE )
	);

	$plugins_css_url = apply_filters(
		'exactmetrics_editor_style_url',
		plugins_url( $plugins_style_path, EXACTMETRICS_PLUGIN_FILE )
	);

	$js_dependencies = array(
		'wp-plugins',
		'wp-element',
		'wp-i18n',
		'wp-api-request',
		'wp-data',
		'wp-hooks',
		'wp-plugins',
		'wp-components',
		'wp-blocks',
		'wp-block-editor',
		'wp-compose',
	);

	if (
		! $wp_scripts->query( 'wp-edit-widgets', 'enqueued' ) &&
		! $wp_scripts->query( 'wp-customize-widgets', 'enqueued' )
	) {
		$js_dependencies[] = 'wp-editor';
		$js_dependencies[] = 'wp-edit-post';
	}

	// Enqueue our plugin JavaScript.
	wp_enqueue_script(
		'exactmetrics-gutenberg-editor-js',
		$plugins_js_url,
		$js_dependencies,
		exactmetrics_get_asset_version(),
		true
	);

	// Enqueue our plugin JavaScript.
	wp_enqueue_style(
		'exactmetrics-gutenberg-editor-css',
		$plugins_css_url,
		array(),
		exactmetrics_get_asset_version()
	);

	$plugins                 = get_plugins();
	$install_woocommerce_url = false;
	if ( current_user_can( 'install_plugins' ) ) {
		$woo_key = 'woocommerce/woocommerce.php';
		if ( array_key_exists( $woo_key, $plugins ) ) {
			$install_woocommerce_url = wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=' . $woo_key ), 'activate-plugin_' . $woo_key );
		} else {
			$install_woocommerce_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=woocommerce' ), 'install-plugin_woocommerce' );
		}
	}

	$posttype = exactmetrics_get_current_post_type();

	// Localize script for sidebar plugins.
	wp_localize_script(
		'exactmetrics-gutenberg-editor-js',
		'exactmetrics_gutenberg_tool_vars',
		apply_filters( 'exactmetrics_gutenberg_tool_vars', array(
			'ajaxurl'                      => admin_url( 'admin-ajax.php' ),
			'nonce'                        => wp_create_nonce( 'exactmetrics_gutenberg_headline_nonce' ),
			'allowed_post_types'           => apply_filters( 'exactmetrics_headline_analyzer_post_types', array( 'post' ) ),
			'current_post_type'            => $posttype,
			'is_headline_analyzer_enabled' => apply_filters( 'exactmetrics_headline_analyzer_enabled', true ) && 'true' !== exactmetrics_get_option( 'disable_headline_analyzer' ),
			'reports_url'                  => add_query_arg( 'page', 'exactmetrics_reports', admin_url( 'admin.php' ) ),
			'vue_assets_path'              => plugins_url( $version_path . '/assets/vue/', EXACTMETRICS_PLUGIN_FILE ),
			'is_woocommerce_installed'     => class_exists( 'WooCommerce' ),
			'license_type'                 => ExactMetrics()->license->get_license_type(),
			'upgrade_url'                  => exactmetrics_get_upgrade_link( 'pageinsights-meta', 'products' ),
			'install_woocommerce_url'      => $install_woocommerce_url,
			'supports_custom_fields'       => post_type_supports( $posttype, 'custom-fields' ),
			'public_post_type'             => $posttype ? is_post_type_viewable( $posttype ) : 0,
			'page_insights_addon_active'   => class_exists( 'ExactMetrics_Page_Insights' ),
			'page_insights_nonce'          => wp_create_nonce( 'mi-admin-nonce' ),
			'isnetwork'                    => is_network_admin(),
			'is_v4'                        => true,
		) )
	);

	$textdomain  = exactmetrics_is_pro_version() ? 'exactmetrics-premium' : 'google-analytics-dashboard-for-wp';

	wp_scripts()->add_inline_script(
		'exactmetrics-gutenberg-editor-js',
		exactmetrics_get_printable_translations( $textdomain )
	);

}

add_action( 'enqueue_block_editor_assets', 'exactmetrics_gutenberg_editor_assets' );
