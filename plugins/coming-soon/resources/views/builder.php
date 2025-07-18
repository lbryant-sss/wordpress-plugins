<?php



require_once SEEDPROD_PLUGIN_PATH . 'resources/data-templates/basic-page.php';

/**
 * Rehydrate settings for the builder
 *
 * @param array  $object_to_hydrate            Object to hydrate.
 * @param string $seedprod_lite_block_templates Seedprod setting defaults.
 * @return void
 */
function seedprod_lite_rehydrate_settings( &$object_to_hydrate, $seedprod_lite_block_templates ) {
	// Decode the default block templates
	$defaults = json_decode( $seedprod_lite_block_templates, true );

	if ( json_last_error() !== JSON_ERROR_NONE ) {
		return;
	}

	/**
	 * Custom merge function that preserves existing values but ensures all default keys exist
	 *
	 * @param array   $defaults Defaults.
	 * @param array   $settings Settings.
	 * @param integer $depth    Depth.
	 * @return array
	 */
	function merge_preserve_existing( $defaults, $settings, $depth = 0 ) {
		// Guard clauses for safety
		if ( ! is_array( $defaults ) ) {
			return $settings;
		}
		if ( ! is_array( $settings ) ) {
			return $defaults;
		}
		// Prevent infinite recursion
		if ( $depth > 100 ) {
			return $settings;
		}

		$result = $defaults;

		foreach ( $settings as $key => $value ) {
			// Special handling for 'items' array - preserve as-is without merging defaults
			if ( $key === 'items' || $key === 'featuresList' ) {
				$result[ $key ] = $value;
				continue;
			}

			// If both values are arrays, merge recursively
			if ( isset( $result[ $key ] ) && is_array( $result[ $key ] ) && is_array( $value ) ) {
				$result[ $key ] = merge_preserve_existing( $result[ $key ], $value, $depth + 1 );
			} else {
				// If the value is not an array, preserve the setting value
				$result[ $key ] = $value;
			}
		}

		return $result;
	}

	function apply_defaults( &$element, $defaults ) {
		$type                = $element['type'] ?? '';
		$element_defaults    = $defaults[ $type ] ?? array();
		$element['settings'] = merge_preserve_existing( $element_defaults, $element['settings'] ?? array() );
	}

	if ( isset( $object_to_hydrate['document'] ) ) {
		$document_defaults                         = $defaults['document'] ?? array();
		$object_to_hydrate['document']['settings'] = array_replace_recursive( $document_defaults, $object_to_hydrate['document']['settings'] ?? array() );
	}

	// Iterate over document sections and apply defaults
	if ( isset( $object_to_hydrate['document']['sections'] ) && is_array( $object_to_hydrate['document']['sections'] ) ) {

		// $element_defaults = $defaults['document'] ?? [];
		// $object_to_hydrate['document']['settings'] = $element_defaults;

		foreach ( $object_to_hydrate['document']['sections'] as &$section ) {
			if ( isset( $section['type'] ) && $section['type'] === 'section' ) {

				// Apply section defaults
				apply_defaults( $section, $defaults );

				// Iterate over rows in the section
				if ( isset( $section['rows'] ) && is_array( $section['rows'] ) ) {
					foreach ( $section['rows'] as &$row ) {
						if ( isset( $row['type'] ) && $row['type'] === 'row' ) {
							// Apply row defaults
							apply_defaults( $row, $defaults );

							// Iterate over cols in the row
							if ( isset( $row['cols'] ) && is_array( $row['cols'] ) ) {
								foreach ( $row['cols'] as &$col ) {
									if ( isset( $col['type'] ) && $col['type'] === 'col' ) {
										// Apply col defaults
										apply_defaults( $col, $defaults );

										// Iterate over blocks in the col
										if ( isset( $col['blocks'] ) && is_array( $col['blocks'] ) ) {
											foreach ( $col['blocks'] as &$block ) {
												if ( isset( $block['type'] ) ) {
													// Apply block defaults
													apply_defaults( $block, $defaults );
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
}


global $wpdb;

// look for mixed content and mis configured WordPress sites.
$actual_link          = ( isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http' ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$seedprod_builder_url = wp_parse_url( $actual_link );
$mixed_content        = false;
if ( false !== $seedprod_builder_url ) {
	if ( ! empty( $seedprod_builder_url['scheme'] && 'https' == $seedprod_builder_url['scheme'] ) ) {
		$sp_home_url        = get_option( 'home' );
		$sp_home_url_parsed = wp_parse_url( $sp_home_url );
		$sp_site_url        = get_option( 'siteurl' );
		$sp_site_url_parsed = wp_parse_url( $sp_site_url );

		if ( ! empty( $sp_home_url_parsed['scheme'] ) && 'http' == $sp_home_url_parsed['scheme'] ) {
			$mixed_content = true;
		}

		if ( ! empty( $site_url_parsed['scheme'] ) && 'http' == $site_url_parsed['scheme'] ) {
			$mixed_content = true;
		}
	}
}


// current user
$sp_current_user           = wp_get_current_user();
$current_user_name         = $sp_current_user->display_name;
$current_user_email        = $sp_current_user->user_email;
$current_user_email_hash   = md5( $sp_current_user->user_email );
$free_templates_subscribed = get_option( 'seedprod_free_templates_subscribed' );
if ( $free_templates_subscribed ) {
	$free_templates_subscribed = '1';
}
$seedprod_nonce = wp_create_nonce( 'seedprod_nonce' );


$lpage_id = '';
if ( ! empty( $_GET['id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$lpage_id = absint( $_GET['id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
} else {
	wp_die();
}

// Template Vars
$timezones     = seedprod_lite_get_timezones();
$times         = seedprod_lite_get_times();
$block_options = seedprod_lite_block_options();


// get page
$tablename = $wpdb->prefix . 'posts';
$sql       = "SELECT * FROM $tablename WHERE id = %d";
$safe_sql  = $wpdb->prepare( $sql, $lpage_id ); // phpcs:ignore
$lpage     = $wpdb->get_row( $safe_sql ); // phpcs:ignore

// reset id
$lpage->id = $lpage->ID;

// Get page uuid
$lpage_uuid = get_post_meta( $lpage->id, '_seedprod_page_uuid', true );
if ( empty( $lpage_uuid ) ) {
	$this_uuid = wp_generate_uuid4();
	update_post_meta( $lpage->id, '_seedprod_page_uuid', $this_uuid );
	$lpage_uuid = $this_uuid;
}

// add default settings if they do not exisits
if ( empty( $lpage->post_content_filtered ) ) {
	require_once SEEDPROD_PLUGIN_PATH . 'resources/data-templates/basic-page.php';
	$settings                            = json_decode( $seedprod_basic_lpage, true );
	$settings['page_type']               = 'lp';
	$settings['from_edit_with_seedprod'] = true;
	// TODO Check if theme builder active
	if ( ! empty( $_GET['from'] ) && 'post' === $_GET['from'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$settings['page_type'] = 'post'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
} else {
	// get settings and maybe modify
	$settings = json_decode( $lpage->post_content_filtered, true );

	if ( null === $settings && JSON_ERROR_NONE !== json_last_error() ) {
		// JSON is invalid
		// Handle the error or display an error message
		// echo "<script>alert('JSON is invalid. Entering Recovery Mode'); </script>";
		require_once SEEDPROD_PLUGIN_PATH . 'resources/data-templates/basic-page.php';
		$settings = json_decode( $seedprod_recovery, true );

	} else {
		// rehydrate settings if json is invalid
		seedprod_lite_rehydrate_settings( $settings, $seedprod_lite_block_templates );
	}
}

// Check is this is a seedprod template part or page/post using the seedprod editor
$seedprod_is_theme_template = get_post_meta( $lpage_id, '_seedprod_is_theme_template', true );
$edited_with_seedprod       = get_post_meta( $lpage_id, '_seedprod_edited_with_seedprod', true );
if ( empty( $seedprod_is_theme_template ) ) {
	$seedprod_is_theme_template = get_post_meta( $lpage_id, '_seedprod_edited_with_seedprod', true );
}
if ( empty( $seedprod_is_theme_template ) ) {
	if ( 'post' === $settings['page_type'] ) {
		$seedprod_is_theme_template = 1;
	}
}

// Check for landing page types
$is_landing_page    = true;
$landing_page_types = array( 'cs', 'mm', 'p404', 'loginp', 'lp' );
if ( ! in_array( $settings['page_type'], $landing_page_types, true ) ) {
	$is_landing_page = false;
}


// get post types
$post_types = get_post_types();

// get seedprod setting tp check special pages states
$seedprod_settings = get_option( 'seedprod_settings' );
if ( ! empty( $seedprod_settings ) ) {
	$seedprod_settings = json_decode( stripslashes( $seedprod_settings ) );
}


// get global css settings
$global_css_settings = array();
$global_css_page_id  = get_option( 'seedprod_global_css_page_id' );




// get preview link
// $preview_link = get_preview_post_link( $lpage_id );
if ( 'lp' === $settings['page_type'] ) {
	$preview_link = home_url() . "/?page_id=$lpage_id&preview_id=$lpage_id&preview_nonce=" . wp_create_nonce( 'post_preview_' . $lpage_id ) . '&preview=true';
} else {
	$preview_link = home_url() . "/?post_type=seedprod&page_id=$lpage_id&preview_id=$lpage_id&preview_nonce=" . wp_create_nonce( 'post_preview_' . $lpage_id ) . '&preview=true';
}

// keep track for changes
$settings['post_title']  = $lpage->post_title;
$settings['post_name']   = $lpage->post_name;
$settings['post_status'] = $lpage->post_status;

$show_bottombar_cta    = true;
$dismiss_bottombar_cta = get_option( 'seedprod_dismiss_upsell_2' );
if ( $dismiss_bottombar_cta ) {
	$show_bottombar_cta = false;
}


// Email integration logic
$seedprod_api_token  = get_option( 'seedprod_api_token' );
$seedprod_user_id    = get_option( 'seedprod_user_id' );
$seedprod_site_token = get_option( 'seedprod_token' );
if ( empty( $seedprod_site_token ) ) {
	$seedprod_site_token = wp_generate_uuid4();
	update_option( 'seedprod_token', $seedprod_site_token );
}
$license_key           = get_option( 'seedprod_api_key' );
$email_integration_url = '';

// stripe connect
$seedprod_stripe_connect_origin = get_option( 'seedprod_stripe_connect_origin' );
if ( empty( $seedprod_stripe_connect_origin ) ) {
	$seedprod_stripe_connect_origin = wp_generate_uuid4();
	add_option( 'seedprod_stripe_connect_origin', $seedprod_stripe_connect_origin );
}
// Set stripe token
if ( ! empty( $_GET['seedprod_stripe_connect_token'] ) ) {
	if ( ! empty( $_GET['seedprod_stripe_connect_origin'] ) ) {
		if ( $seedprod_stripe_connect_origin == $_GET['seedprod_stripe_connect_origin'] && current_user_can( 'manage_options' ) ) {
			update_option( 'seedprod_stripe_connect_token', $_GET['seedprod_stripe_connect_token'] );
		}
	}
}
// get stripe token
$seedprod_stripe_connect_token = get_option( 'seedprod_stripe_connect_token' );
if ( empty( $seedprod_stripe_connect_token ) ) {
	$seedprod_stripe_connect_token = '';
}



$seedprod_web_api = SEEDPROD_WEB_API_URL;




$seedprod_app_settings = get_option( 'seedprod_app_settings' );
if ( ! empty( $seedprod_app_settings ) ) {
	$seedprod_app_settings = json_decode( stripslashes( $seedprod_app_settings ) );
} else {
	// fail safe incase settings go missing
	require_once SEEDPROD_PLUGIN_PATH . 'resources/data-templates/default-settings.php';
	update_option( 'seedprod_app_settings', $seedprod_app_default_settings );
	$seedprod_app_settings = json_decode( $seedprod_app_default_settings );
}


$template_preview_path = 'https://assets.seedprod.com/preview-';


// Preference array.
$user_personalization_preferences_schema = array(
	'show_templatetag_settings'             => true,
	'show_woocommerce_templatetag_settings' => true,
	'show_edd_templatetag_settings'         => true,
	'show_entry_settings'                   => true,
	'show_entry_settings_2'                 => true,
	'show_entry_settings_4'                 => true,
	'show_entry_settings_5'                 => true,
	'show_entry_settings_3'                 => false,
	'show_entry_settings_6'                 => true,
	'show_layoutnav'                        => false,
);

// Get user personalization preferences.
if ( metadata_exists( 'user', $sp_current_user->ID, 'seedprod_personalization_preferences' ) ) {
	$user_personalization_preferences = get_user_meta( $sp_current_user->ID, 'seedprod_personalization_preferences', true );

	$decoded_json = json_decode( $user_personalization_preferences, true );

	// Validate user meta.
	if ( empty( $user_personalization_preferences ) || ! isset( $user_personalization_preferences ) || ! is_array( $decoded_json ) || json_last_error() ) {
		delete_user_meta( $sp_current_user->ID, 'seedprod_personalization_preferences' );
		// Set default user personalization preferences.
		add_user_meta( $sp_current_user->ID, 'seedprod_personalization_preferences', wp_json_encode( $user_personalization_preferences_schema ), true );
		$user_personalization_preferences = get_user_meta( $sp_current_user->ID, 'seedprod_personalization_preferences', true );
	}
} else {
	// Set default user personalization preferences.
	add_user_meta( $sp_current_user->ID, 'seedprod_personalization_preferences', wp_json_encode( $user_personalization_preferences_schema ), true );
	$user_personalization_preferences = get_user_meta( $sp_current_user->ID, 'seedprod_personalization_preferences', true );
}

// Check if DB array has all the keys.
$get_array_keys = array_keys( $user_personalization_preferences_schema );

/**
 * Check if array keys exist func.
 *
 * @param array $keys           Array of keys.
 * @param array $array_to_check Array of keys.
 * @return boolean
 */
function array_keys_exists( array $keys, array $array_to_check ) {
	$diff = array_diff_key( array_flip( $keys ), $array_to_check );
	return count( $diff ) === 0;
}

// Decode user meta.
$decoded_personalization_preferences = json_decode( $user_personalization_preferences, true );

// Update user meta with new settings.
if ( is_array( $decoded_personalization_preferences ) && null !== $decoded_personalization_preferences ) {
	// Determine whether to update or not.
	if ( ! array_keys_exists( $get_array_keys, $decoded_personalization_preferences ) ) {
		// Update user meta with new settings.
		update_user_meta( $sp_current_user->ID, 'seedprod_personalization_preferences', wp_json_encode( $user_personalization_preferences_schema ), $user_personalization_preferences );
		// Get updated settings.
		$user_personalization_preferences = json_decode( get_user_meta( $sp_current_user->ID, 'seedprod_personalization_preferences', true ) );
	}

	// Get current settings.
	$user_personalization_preferences = json_decode( $user_personalization_preferences, true );
} else {
	// If not array/is not set/empty set default settings.
	if ( metadata_exists( 'user', $sp_current_user->ID, 'seedprod_personalization_preferences' ) ) {
		// Update empty user meta with new settings.
		delete_user_meta( $sp_current_user->ID, 'seedprod_personalization_preferences' );
	}

	// Set default user personalization preferences.
	add_user_meta( $sp_current_user->ID, 'seedprod_personalization_preferences', wp_json_encode( $user_personalization_preferences_schema ), true );
	$user_personalization_preferences = get_user_meta( $sp_current_user->ID, 'seedprod_personalization_preferences', true );
}

// Pers
$per                        = array();
$active_license             = false;
$template_dev_mode          = false;
$template_dev_mode_url      = false;
$template_dev_mode_password = false;

?>


<style>
.sp-mobile-view  .sp-w-full {
	width: 100% !important;
}

.sp-mobile-view .sp-el-section,.sp-mobile-view .sp-el-row,.sp-mobile-view .sp-el-col,.sp-mobile-view .sp-el-block{
	padding: 5px !important;
}

.sp-mobile-view .sm\:sp-flex {
	display: block;
}

.mce-content-body {
	line-height: 1.5;
}

h1.mce-content-body,h2.mce-content-body,h3.mce-content-body,h4.mce-content-body,h5.mce-content-body,h6.mce-content-body {
	line-height: 1.1;
}
</style>

<div id="seedprod-vue-app-builder" class="sp-font-sans"></div>

<?php
	$fontawesome_file = SEEDPROD_PLUGIN_PATH . 'resources/data-templates/fontawesome.json';
	$fontawesome_json = json_decode( file_get_contents( $fontawesome_file ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$icons            = array();
foreach ( $fontawesome_json as $v ) {
	$icons[] = array(
		'c' => 'fa',
		'n' => $v,
	);
}

	$googlefonts_file = SEEDPROD_PLUGIN_PATH . 'resources/data-templates/googlefonts.json';

	$fonts['Standard Fonts'] = array(
		"'Helvetica Neue', Arial, sans-serif"   => 'Helvetica Neue',
		'Garamond, serif'                       => 'Garamond',
		'Georgia, serif'                        => 'Georgia',
		'Impact, Charcoal, sans-serif'          => 'Impact',
		'Tahoma, Geneva, sans-serif'            => 'Tahoma',
		"'Times New Roman', Times,serif"        => 'Times New Roman',
		"'Trebuchet MS', Helvetica, sans-serif" => 'Trebuchet',
		'Verdana, Geneva, sans-serif'           => 'Verdana',
		'Courier, monospace'                    => 'Courier',
		"'Comic Sans MS', cursive"              => 'Comic Sans',
	);
	$fonts['Google Fonts']   = json_decode( file_get_contents( $googlefonts_file ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	// $googlefonts_json = json_decode(file_get_contents($googlefonts_file));

	// get list of fonts to load
	$google_fonts_str = seedprod_lite_construct_font_str( $settings['document'] );

	?>

<?php if ( ! empty( $google_fonts_str ) ) : ?>
<!-- Google Font -->
<link rel="stylesheet" href="<?php echo esc_url( $google_fonts_str ); ?>"> <?php // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet ?>
<?php endif; ?>

<script>
//var seedprod_copy_paste_enabled = false;
var seedprod_nonce = <?php echo wp_json_encode( $seedprod_nonce ); ?>;
var seedprod_page = <?php echo wp_json_encode( sanitize_text_field( wp_unslash( $_GET['page'] ) ) ); ?>; <?php // phpcs:ignore ?>
var seedprod_remote_api = "<?php echo esc_url( SEEDPROD_API_URL ); ?>";
<?php
$from = '';
if ( ! empty( $_GET['from'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$form = sanitize_text_field( wp_unslash( $_GET['from'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
}
?>
var seedprod_from = <?php echo wp_json_encode( $from ); ?>;
<?php
// see if we need below
$ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_save_template', 'seedprod_lite_save_template' ) );
?>
var seedprod_template_save_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_template_subscribe', 'seedprod_lite_template_subscribe' ) ); ?>
var seedprod_template_subscribe_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_save_page', 'seedprod_lite_save_page' ) ); ?>
var seedprod_save_lpage_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $utc_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_get_utc_offset', 'seedprod_lite_get_utc_offset' ) ); ?>
var seedprod_utc_url = <?php echo wp_json_encode( esc_url_raw( $utc_url ) ); ?>;

<?php $get_namespaced_custom_css_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_get_namespaced_custom_css', 'seedprod_lite_get_namespaced_custom_css' ) ); ?>
var seedprod_get_namespaced_custom_css_url = <?php echo wp_json_encode( esc_url_raw( $get_namespaced_custom_css_url ) ); ?>;

<?php $stockimages_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_get_stockimages', 'seedprod_lite_get_stockimages' ) ); ?>
var seedprod_stockimages_url = <?php echo wp_json_encode( esc_url_raw( $stockimages_url ) ); ?>;

<?php $backgrounds_sideload_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_backgrounds_sideload', 'seedprod_lite_backgrounds_sideload' ) ); ?>
var seedprod_backgrounds_sideload_url = <?php echo wp_json_encode( esc_url_raw( $backgrounds_sideload_url ) ); ?>;

<?php $backgrounds_download_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_backgrounds_download', 'seedprod_lite_backgrounds_download' ) ); ?>
var seedprod_backgrounds_download_url = <?php echo wp_json_encode( esc_url_raw( $backgrounds_download_url ) ); ?>;

<?php $open_ai_backgrounds_sideload_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_open_ai_backgrounds_sideload', 'seedprod_lite_open_ai_backgrounds_sideload' ) ); ?>
var seedprod_open_ai_backgrounds_sideload_url = <?php echo wp_json_encode( esc_url_raw( $open_ai_backgrounds_sideload_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_slug_exists', 'seedprod_lite_slug_exists' ) ); ?>
var seedprod_slug_exists_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $seedprod_upgrade_link = seedprod_lite_upgrade_link( '' ); ?>

<?php $url = seedprod_lite_get_plugins_install_url( 'all-in-one-seo-pack' ); ?>
var seedprod_seo_install_link = <?php echo wp_json_encode( esc_url_raw( htmlspecialchars_decode( $url ) ) ); ?>;

<?php $url = seedprod_lite_get_plugins_install_url( 'wpforms-lite' ); ?>
var seedprod_form_install_link = <?php echo wp_json_encode( esc_url_raw( htmlspecialchars_decode( $url ) ) ); ?>;

<?php $url = seedprod_lite_get_plugins_install_url( 'pushengage' ); ?>
var seedprod_push_notifications_install_link = <?php echo wp_json_encode( esc_url_raw( htmlspecialchars_decode( $url ) ) ); ?>;

<?php $url = seedprod_lite_get_plugins_install_url( 'envira-gallery-lite' ); ?>
var seedprod_envira_install_link = <?php echo wp_json_encode( esc_url_raw( htmlspecialchars_decode( $url ) ) ); ?>;

<?php
	$url = seedprod_lite_get_plugins_activate_url( 'pushengage/main.php' );
?>

var seedprod_push_notifications_activate_link = <?php echo wp_json_encode( esc_url_raw( htmlspecialchars_decode( $url ) ) ); ?>;

<?php $url = seedprod_lite_get_plugins_install_url( 'rafflepress' ); ?>
var seedprod_giveaway_install_link = <?php echo wp_json_encode( esc_url_raw( htmlspecialchars_decode( $url ) ) ); ?>;

<?php $url = seedprod_lite_get_plugins_install_url( 'mypaykit' ); ?>
var seedprod_mypaykit_install_link = <?php echo wp_json_encode( esc_url_raw( htmlspecialchars_decode( $url ) ) ); ?>;

<?php $url = seedprod_lite_get_plugins_activate_url( 'mypaykit/mypaykit.php' ); ?>
var seedprod_mypaykit_activate_link = <?php echo wp_json_encode( esc_url_raw( htmlspecialchars_decode( $url ) ) ); ?>;

<?php $url = seedprod_lite_get_plugins_install_url( 'google-analytics-for-wordpress' ); ?>
var seedprod_analytics_install_link = <?php echo wp_json_encode( esc_url_raw( htmlspecialchars_decode( $url ) ) ); ?>;

<?php
	$url = seedprod_lite_get_plugins_activate_url( 'google-analytics-for-wordpress/googleanalytics.php' );
?>

var seedprod_analytics_activate_link = <?php echo wp_json_encode( esc_url_raw( htmlspecialchars_decode( $url ) ) ); ?>;

<?php
	$url = seedprod_lite_get_plugins_activate_url( 'google-analytics-premium/googleanalytics-premium.php' );
?>

var seedprod_analytics_pro_activate_link = <?php echo wp_json_encode( esc_url_raw( htmlspecialchars_decode( $url ) ) ); ?>;

<?php
	$url = seedprod_lite_get_plugins_activate_url( 'wpforms-lite/wpforms.php' );
?>

var seedprod_form_activate_link = <?php echo wp_json_encode( esc_url_raw( htmlspecialchars_decode( $url ) ) ); ?>;


<?php
	$url = seedprod_lite_get_plugins_activate_url( 'envira-gallery-lite/envira-gallery-lite.php' );
?>
var seedprod_envira_activate_link = <?php echo wp_json_encode( esc_url_raw( htmlspecialchars_decode( $url ) ) ); ?>;

<?php
	$url = seedprod_lite_get_plugins_activate_url( 'all-in-one-seo-pack/all_in_one_seo_pack.php' );
?>
var seedprod_seo_activate_link = <?php echo wp_json_encode( esc_url_raw( htmlspecialchars_decode( $url ) ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_install_addon', 'seedprod_lite_install_addon' ) ); ?>
var seedprod_get_install_addon_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_activate_addon', 'seedprod_lite_activate_addon' ) ); ?>
var seedprod_activate_addon_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_deactivate_addon', 'seedprod_lite_deactivate_addon' ) ); ?>
var seedprod_deactivate_addon_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_plugin_nonce', 'seedprod_lite_plugin_nonce' ) ); ?>
var seedprod_plugin_nonce_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_dismiss_upsell', 'seedprod_lite_dismiss_upsell' ) ); ?>
var seedprod_dismiss_upsell = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=seedprod_lite_import_cross_site_paste', 'seedprod_lite_import_cross_site_paste' ) ); ?>
var seedprod_import_cross_site_url = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

<?php
	// user has to have this capability
	$unfiltered_html = false;
if ( current_user_can( 'unfiltered_html' ) ) {
	$unfiltered_html = true;
}
?>

<?php
	// get menus
	$seedprod_menus      = wp_get_nav_menus();
	$seedprod_options    = array();
	$seedprod_first_menu = '';
if ( count( $seedprod_menus ) > 0 ) {
	$menu_counter = 0;
	foreach ( $seedprod_menus as $sp_menu ) {
		if ( 0 === $menu_counter ) {
			$seedprod_first_menu = $sp_menu->slug;
		}
		++$menu_counter;
		$seedprod_options[ $sp_menu->slug ] = $sp_menu->name;
	}
}
?>

<?php
	// get tempplate parts
	$seedprod_template_parts = array();
	$seedprod_theme_parts    = array();
	$seedprod_selected_template_parts = '';
if ( count( $seedprod_theme_parts ) > 0 ) {

	$counter = 0;
	foreach ( $seedprod_theme_parts as $tparts ) {
		if ( 0 === $counter ) {
			$seedprod_selected_template_parts = $tparts->ID; }
			++$counter;
			$seedprod_template_parts[ $tparts->ID ] = $tparts->post_title;
	}
}

$wp_getlocale = get_locale();
$wp_getlocale = substr( $wp_getlocale, 0, 2 );
?>


var seedprod_data = 
<?php
$seedprod_data = array(
	'seedprod_web_api'                    => $seedprod_web_api,
	'seedprod_stripe_connect_token'       => $seedprod_stripe_connect_token,
	'seedprod_stripe_connect_origin'      => $seedprod_stripe_connect_origin,
	'seedprod_settings'                   => $seedprod_settings,
	'mixed_content'                       => $mixed_content,
	'is_landing_page'                     => $is_landing_page,
	'edited_with_seedprod'                => $edited_with_seedprod,
	'unfiltered_html'                     => $unfiltered_html,
	'global_css_page_id'                  => $global_css_page_id,
	'global_css_settings'                 => $global_css_settings,
	'show_bottombar_cta'                  => $show_bottombar_cta,
	'template_preview_path'               => $template_preview_path,
	'page_uuid'                           => $lpage_uuid,
	'placeholder_image'                   => SEEDPROD_PLUGIN_URL . 'public/img/img-placeholder.png',
	'placeholder_sm_image'                => SEEDPROD_PLUGIN_URL . 'public/img/img-placeholder-sm.png',
	'block_templates'                     => json_decode( $seedprod_lite_block_templates ),
	'seedprod_menus'                      => $seedprod_options,
	'seedprod_first_menu'                 => $seedprod_first_menu,
	'expire_times'                        => seedprod_lite_get_expire_times(),
	'roles'                               => seedprod_lite_get_roles(),
	'my_ip'                               => seedprod_lite_get_ip(),
	'plugins_installed'                   => seedprod_lite_get_plugins_array(),
	'giveaway_plugins_installed'          => seedprod_lite_get_giveaway_plugins_list(),
	'form_plugins_installed'              => seedprod_lite_get_form_plugins_list(),
	'seo_plugins_installed'               => seedprod_lite_get_seo_plugins_list(),
	'analytics_plugins_installed'         => seedprod_lite_get_analytics_plugins_list(),
	'push_notification_plugins_installed' => seedprod_lite_get_push_notification_plugins_list(),
	'seedprod_template_parts'             => $seedprod_template_parts,
	'seedprod_selected_template_parts'    => $seedprod_selected_template_parts,
	'page_type'                           => $settings['page_type'],
	'current_user_name'                   => $current_user_name,
	'current_user_email_hash'             => $current_user_email_hash,
	'current_user_email'                  => $current_user_email,
	'free_templates_subscribed'           => $free_templates_subscribed,
	'preview_link'                        => $preview_link,
	'icons'                               => $icons,
	'googlefonts'                         => $fonts,
	'api_token'                           => $seedprod_api_token,
	'seedprod_user_id'                    => $seedprod_user_id,
	'site_token'                          => $seedprod_site_token,
	'license_key'                         => $license_key,
	'page_path'                           => 'seedprod_lite',
	'plugin_path'                         => SEEDPROD_PLUGIN_URL,
	'web_path'                            => SEEDPROD_WEB_API_URL,
	'home_url'                            => home_url(),
	'upgrade_link'                        => $seedprod_upgrade_link,
	'lpage'                               => $lpage,
	'settings'                            => $settings,
	'app_settings'                        => $seedprod_app_settings,
	'block_options'                       => $block_options,
	'timezones'                           => $timezones,
	'times'                               => $times,
	'template_dev_mode'                   => $template_dev_mode,
	'template_dev_mode_url'               => $template_dev_mode_url,
	'template_dev_mode_password'          => $template_dev_mode_password,
	'email_integration_url'               => $email_integration_url,
	'per'                                 => $per,
	'credits'                             => seedprod_lite_get_ai_credits(),
	'active_license'                      => $active_license,
	'is_theme_template'                   => $seedprod_is_theme_template,
	'personalization_preferences'         => $user_personalization_preferences,
	'wplocale'                            => $wp_getlocale,
);

	$seedprod_data['envira'] = array(
		'add_envira_gallery' => admin_url( 'post-new.php?post_type=envira' ),
		'placeholder'        => sprintf( '<img src="%s" width="180px" alt="Envira Gallery Logo"/>', esc_url( SEEDPROD_PLUGIN_URL . 'public/img/plugin-envira.svg' ) ),
	);

	$seedprod_data['wpforms'] = array(
		'edit_form_url' => admin_url( 'admin.php?page=wpforms-builder&view=fields&form_id=' ),
		'add_form_url'  => admin_url( 'admin.php?page=wpforms-builder&view=setup' ),
		'placeholder'   => sprintf( '<img src="%s" width="80px" alt="WPForms Logo"/>', esc_url( SEEDPROD_PLUGIN_URL . 'public/img/plugin-wpforms.png' ) ),
	);

	$rp_version = 'lite';
	if ( function_exists( 'rafflepress_pro_load_textdomain' ) ) {
		$rp_version = 'pro';
	}

	$seedprod_data['rafflepress'] = array(
		'edit_form_url' => admin_url( 'admin.php?page=rafflepress_' . $rp_version . '_builder&id=$id$#/setup/$id$' ),
		'add_form_url'  => admin_url( 'admin.php?page=rafflepress_' . $rp_version . '_builder&id=0#/template' ),
		'placeholder'   => sprintf( '<img src="%s" width="80px" alt="RafflePress Logo"/>', esc_url( SEEDPROD_PLUGIN_URL . 'public/img/plugin-rp.png' ) ),
	);

	$seedprod_data['mypaykit'] = array(
		'edit_form_url' => '',
		'add_form_url'  => '',
		'placeholder'   => sprintf( '<img src="%s" width="80px" alt="MyPayKit Logo"/>', esc_url( SEEDPROD_PLUGIN_URL . 'public/img/plugin-mp.png' ) ),
	);

	if ( defined( 'MYPAYKIT_WEB_URL' ) ) {
		// Check if get_option('mypaykit_site_token') & get_option('mypaykit_token') are available & not empty
		if ( get_option( 'mypaykit_site_token' ) && get_option( 'mypaykit_token' ) ) {
			$seedprod_data['mypaykit'] = array(
				'edit_form_url' => add_query_arg(
					array(
						'site_token'     => urlencode( get_option( 'mypaykit_site_token' ) ),
						'mypaykit_token' => urlencode( get_option( 'mypaykit_token' ) ),
					),
					MYPAYKIT_WEB_URL . '/form/$id$'
				),
				'add_form_url'  => add_query_arg(
					array(
						'site_token'     => urlencode( get_option( 'mypaykit_site_token' ) ),
						'mypaykit_token' => urlencode( get_option( 'mypaykit_token' ) ),
					),
					MYPAYKIT_WEB_URL . '/form'
				),
				'placeholder'   => sprintf( '<img src="%s" width="80px" alt="MyPayKit Logo"/>', esc_url( SEEDPROD_PLUGIN_URL . 'public/img/plugin-mp.png' ) ),
			);
		} else {
			$seedprod_data['mypaykit'] = array(
				'edit_form_url' => admin_url( 'admin.php?page=mypaykit-forms' ),
				'add_form_url'  => admin_url( 'admin.php?page=mypaykit-forms' ),
				'placeholder'   => sprintf( '<img src="%s" width="80px" alt="MyPayKit Logo"/>', esc_url( SEEDPROD_PLUGIN_URL . 'public/img/plugin-mp.png' ) ),
			);
		}
	}

	// Check if WooCommerce is active
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		$seedprod_data['wc_active'] = true;
	} else {
		$seedprod_data['wc_active'] = false;
	}

	// Check if ACF is active
	if ( ( in_array( 'advanced-custom-fields/acf.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || in_array( 'advanced-custom-fields-pro/acf.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) && function_exists( 'acf_get_field_groups' ) ) {
		$seedprod_data['acf_active'] = true;

		// Get ACF options.
		$seedprod_data['acf_groups'] = array();

	} else {
		$seedprod_data['acf_active'] = false;
		$seedprod_data['acf_groups'] = array();
	}

	// Check if Easy Digital Downloads is active
	if ( in_array( 'easy-digital-downloads/easy-digital-downloads.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || in_array( 'easy-digital-downloads-pro/easy-digital-downloads.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		$seedprod_data['edd_active'] = true;
	} else {
		$seedprod_data['edd_active'] = false;
	}

	// Get translations
	$seedprod_data['translations_pro'] = seedprod_lite_get_jed_locale_data( 'coming-soon' );

	// Get help documents
	$seedprod_data['inline_help_articles'] = seedprod_lite_fetch_inline_help_data();

	echo wp_json_encode( $seedprod_data );
	?>
	;

	jQuery('link[href*="forms.css"]').remove();
	jQuery('link[href*="common.css"]').remove();


	xdLocalStorage.init({
		iframeUrl:'https://assets.seedprod.com/cross-domain-local-storage/cross-domain-local-storage.html',
		initCallback: function () {

			xdLocalStorage.getItem('seedprod_section_data', function (data) {
				if (data.value=='' || data.value==null) {
						seedprod_store.seedprod_copy_paste_enabled = false;
				} else {
					seedprod_store.seedprod_copy_paste_enabled = true;
				}
			});
		}
	});

	function setxdLocalStorageKeyValue (key,value) {

		xdLocalStorage.setItem(key, value);
		seedprod_store.seedprod_copy_paste_enabled= true;

	}

	function getxdLocalStorageKeyValue(key){

		xdLocalStorage.getItem(key, function (data) {
			seedprod_section_data = JSON.parse(data.value);
		});

	}

	function getxdLocalStorageValue(){

		xdLocalStorage.getItem('seedprod_section_data', function (data) {
			seedprod_section_data = JSON.parse(data.value);
		});

	}

	function setxdLocalStorageValue (value) {

		xdLocalStorage.setItem('seedprod_section_data', value);
		seedprod_store.seedprod_copy_paste_enabled= true;

	}



</script>

