<?php
defined( 'ABSPATH' ) || exit;

/**
 * Tell WP what to do when admin is loaded aka upgrader.
 *
 * @since 1.0
 */
function _imagify_upgrader() {
	// Back-compat' with previous version of the upgrader.
	imagify_upgrader_upgrade();

	// Version stored on the network.
	$network_version = Imagify_Options::get_instance()->get( 'version' );
	// Version stored at the site level.
	$site_version = Imagify_Data::get_instance()->get( 'version' );

	if ( ! $network_version ) {
		// First install (network).

		/**
		 * Triggered on Imagify first install (network).
		 *
		 * @since  1.7
		 * @author Grégory Viguier
		 */
		do_action( 'imagify_first_network_install' );
	} elseif ( IMAGIFY_VERSION !== $network_version ) {
		// Already installed but got updated (network).

		/**
		 * Triggered on Imagify upgrade (network).
		 *
		 * @since  1.7
		 * @author Grégory Viguier
		 *
		 * @param string $network_version Previous version stored on the network.
		 * @param string $site_version    Previous version stored on site level.
		 */
		do_action( 'imagify_network_upgrade', $network_version, $site_version );
	}

	// If any upgrade has been done, we flush and update version.
	if ( did_action( 'imagify_first_network_install' ) || did_action( 'imagify_network_upgrade' ) ) {
		Imagify_Options::get_instance()->set( 'version', IMAGIFY_VERSION );
	}

	if ( ! $site_version ) {
		// First install (site level).

		/**
		 * Triggered on Imagify first install (site level).
		 *
		 * @since 1.0
		 */
		do_action( 'imagify_first_install' );
	} elseif ( IMAGIFY_VERSION !== $site_version ) {
		// Already installed but got updated (site level).

		/**
		 * Triggered on Imagify upgrade (site level).
		 *
		 * @since 1.0
		 * @since 1.7 $network_version replaces the "new version" (which can easily be grabbed with the constant).
		 *
		 * @param string $network_version Previous version stored on the network.
		 * @param string $site_version    Previous version stored on site level.
		 */
		do_action( 'imagify_upgrade', $network_version, $site_version );
	}

	// If any upgrade has been done, we flush and update version.
	if ( did_action( 'imagify_first_install' ) || did_action( 'imagify_upgrade' ) ) {
		Imagify_Data::get_instance()->set( 'version', IMAGIFY_VERSION );
	}
}
add_action( 'admin_init', '_imagify_upgrader' );

/**
 * Upgrade the upgrader:
 * Imagify 1.7 splits "network version" and "site version". Since the "site version" didn't exist before 1.7, we need to provide a version based on the "network version".
 *
 * @since  1.7
 * @author Grégory Viguier
 */
function imagify_upgrader_upgrade() {
	global $wpdb;

	// Version stored on the network.
	$network_version = Imagify_Options::get_instance()->get( 'version' );

	if ( ! $network_version ) {
		// Really first install.
		return;
	}

	// Version stored at the site level.
	$site_version = Imagify_Data::get_instance()->get( 'version' );

	if ( $site_version ) {
		// This site's upgrader is already upgraded.
		return;
	}

	if ( ! is_multisite() ) {
		// Not a multisite, so both versions must have the same value.
		Imagify_Data::get_instance()->set( 'version', $network_version );
		return;
	}

	$sites = get_site_option( 'imagify_old_version' );

	if ( IMAGIFY_VERSION !== $network_version && ! $sites ) {
		// The network is not up-to-date yet: store the site IDs that must be updated.
		$network_id = function_exists( 'get_current_network_id' ) ? get_current_network_id() : $wpdb->siteid;
		$sites      = $wpdb->get_col( $wpdb->prepare( "SELECT blog_id FROM $wpdb->blogs WHERE site_id = %d AND archived = 0 AND deleted = 0", $network_id ) );
		$sites      = array_map( 'absint', $sites );
		$sites      = array_filter( $sites );

		if ( ! $sites ) {
			// Uh?
			return;
		}

		// We store the old network version and the site Ids: those sites will need to be upgraded from this version.
		$sites['version'] = $network_version;

		add_site_option( 'imagify_old_version', $sites );
	}

	if ( empty( $sites['version'] ) ) {
		// WTF.
		delete_site_option( 'imagify_old_version' );
		return;
	}

	$network_version = $sites['version'];
	unset( $sites['version'] );

	$sites   = array_flip( $sites );
	$site_id = get_current_blog_id();

	if ( ! isset( $sites[ $site_id ] ) ) {
		// This site is already upgraded.
		return;
	}

	unset( $sites[ $site_id ] );

	if ( ! $sites ) {
		// We're done, all the sites have been upgraded.
		delete_site_option( 'imagify_old_version' );
	} else {
		// Some sites still need to be upgraded.
		$sites            = array_flip( $sites );
		$sites['version'] = $network_version;
		update_site_option( 'imagify_old_version', $sites );
	}

	Imagify_Data::get_instance()->set( 'version', $network_version );
}

/**
 * Keeps this function up to date at each version.
 *
 * @since 1.0
 */
function _imagify_first_install() {
	// Set a transient to know when we will have to display a notice to ask the user to rate the plugin.
	set_site_transient( 'imagify_seen_rating_notice', true, DAY_IN_SECONDS * 3 );
}
add_action( 'imagify_first_network_install', '_imagify_first_install' );

/**
 * What to do when Imagify is updated, depending on versions.
 *
 * @since 1.0
 * @since 1.7 $network_version replaces the "new version" (which can easily be grabbed with the constant).
 *
 * @param string $network_version Previous version stored on the network.
 * @param string $site_version    Previous version stored on site level.
 */
function _imagify_new_upgrade( $network_version, $site_version ) {
	global $wpdb;

	$options = Imagify_Options::get_instance();

	// 1.2
	if ( version_compare( $site_version, '1.2' ) < 0 ) {
		// Update all already optimized images status from 'error' to 'already_optimized'.
		$query = new WP_Query(
			[
				'is_imagify'             => true,
				'post_type'              => 'attachment',
				'post_status'            => imagify_get_post_statuses(),
				'post_mime_type'         => imagify_get_mime_types(),
				'meta_key'               => '_imagify_status',
				'meta_value'             => 'error',
				'posts_per_page'         => -1,
				'update_post_term_cache' => false,
				'no_found_rows'          => true,
				'fields'                 => 'ids',
			]
		);

		if ( $query->posts ) {
			foreach ( (array) $query->posts as $id ) {
				$data  = get_post_meta( $id, '_imagify_data', true );
				$error = ! empty( $data['sizes']['full']['error'] ) ? $data['sizes']['full']['error'] : '';

				if ( false !== strpos( $error, 'This image is already compressed' ) ) {
					update_post_meta( $id, '_imagify_status', 'already_optimized' );
				}
			}
		}

		// Auto-activate the Admin Bar option.
		$options->set( 'admin_bar_menu', 1 );
	}

	// 1.3.2
	if ( version_compare( $site_version, '1.3.2' ) < 0 ) {
		// Update all already optimized images status from 'error' to 'already_optimized'.
		$query = new WP_Query(
			[
				'is_imagify'             => true,
				'post_type'              => 'attachment',
				'post_status'            => imagify_get_post_statuses(),
				'post_mime_type'         => imagify_get_mime_types(),
				'meta_query'             => array(
					'relation' => 'AND',
					array(
						'key'     => '_imagify_data',
						'compare' => 'EXISTS',
					),
					array(
						'key'     => '_imagify_optimization_level',
						'compare' => 'NOT EXISTS',
					),
				),
				'posts_per_page'         => -1,
				'update_post_term_cache' => false,
				'no_found_rows'          => true,
				'fields'                 => 'ids',
			]
		);

		if ( $query->posts ) {
			foreach ( (array) $query->posts as $id ) {
				$data  = get_post_meta( $id, '_imagify_data', true );
				$stats = isset( $data['stats'] ) ? $data['stats'] : [];

				if ( isset( $stats['aggressive'] ) ) {
					update_post_meta( $id, '_imagify_optimization_level', (int) $stats['aggressive'] );
				}
			}
		}
	}

	// 1.4.5
	if ( version_compare( $site_version, '1.4.5' ) < 0 ) {
		// Delete all transients used for async optimization.
		$wpdb->query( $wpdb->prepare( "DELETE from $wpdb->options WHERE option_name LIKE %s", Imagify_DB::esc_like( '_transient_imagify-async-in-progress-' ) . '%' ) );
	}

	// 1.7
	if ( version_compare( $site_version, '1.7' ) < 0 ) {
		// Migrate data.
		Imagify_Cron_Library_Size::get_instance()->do_event();

		if ( ! imagify_is_active_for_network() ) {
			// Make sure the settings are autoloaded.
			$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->options} SET `autoload` = 'yes' WHERE `autoload` != 'yes' AND option_name = %s", $options->get_option_name() ) );
		}

		// Rename the option that stores the NGG table version. Since the table is also updated in 1.7, let's simply delete the option.
		delete_option( $wpdb->prefix . 'ngg_imagify_data_db_version' );
	}

	// 1.8.1
	if ( version_compare( $site_version, '1.8.1' ) < 0 ) {
		// Custom folders: replace `{{ABSPATH}}/` by `{{ROOT}}/`.
		$filesystem  = imagify_get_filesystem();
		$replacement = '{{ROOT}}/';

		if ( $filesystem->has_wp_its_own_directory() ) {
			$replacement .= preg_replace( '@^' . preg_quote( $filesystem->get_site_root(), '@' ) . '@', '', $filesystem->get_abspath() );
		}

		$replacement = Imagify_DB::esc_like( $replacement );

		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->base_prefix}imagify_files SET path = REPLACE( path, '{{ABSPATH}}/', %s ) WHERE path LIKE %s", $replacement, '{{ABSPATH}}/%' ) );
		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->base_prefix}imagify_folders SET path = REPLACE( path, '{{ABSPATH}}/', %s ) WHERE path LIKE %s", $replacement, '{{ABSPATH}}/%' ) );
	}

	// 1.8.2
	if ( version_compare( $site_version, '1.8.2' ) < 0 ) {
		$options->set( 'partner_links', 1 );
	}

	// 1.9.6
	if ( version_compare( $site_version, '1.9.6' ) < 0 ) {
		\Imagify\Stats\OptimizedMediaWithoutNextGen::get_instance()->clear_cache();
	}

	// 1.9.11
	if ( version_compare( $site_version, '1.9.11' ) < 0 ) {
		imagify_secure_custom_directories();
	}

	if ( version_compare( $site_version, '2.0' ) < 0 ) {
		$options->set( 'optimization_level', 2 );
	}

	if ( version_compare( $site_version, '2.2' ) < 0 ) {
		$options->set( 'display_nextgen', $options->get( 'display_webp', 0 ) );
		$options->set( 'display_nextgen_method', $options->get( 'display_webp_method' ) );
	}

	if ( version_compare( $site_version, '2.2.2', '<' ) ) {
		if ( $options->get( 'convert_to_avif' ) ) {
			$options->set( 'optimization_format', 'avif' );
		} else {
			$options->set( 'optimization_format', 'webp' );
		}
	}
}
add_action( 'imagify_upgrade', '_imagify_new_upgrade', 10, 2 );

/**
 * Scan imagify directories and add `index.php` files where missing.
 *
 * @since 1.9.11
 *
 * @return void
 */
function imagify_secure_custom_directories() {
	$filesystem = imagify_get_filesystem();

	Imagify_Custom_Folders::add_indexes();

	$conf_dir = $filesystem->get_site_root() . 'conf';
	Imagify_Custom_Folders::add_indexes( $conf_dir );

	$backup_dir = get_imagify_backup_dir_path();
	Imagify_Custom_Folders::add_indexes( $backup_dir );
}
add_action( 'imagify_activation', 'imagify_secure_custom_directories' );
