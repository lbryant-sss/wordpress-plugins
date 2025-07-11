<?php

// Check if emails should be send or not
function cau_check_updates_mail() {

	// Notify of pending updates
	if( cau_get_db_value( 'send' ) == 'on' ) { 
		cau_list_theme_updates(); // Check for theme updates
		cau_list_plugin_updates(); // Check for plugin updates
	}

	// Notify of completed updates
	if( cau_get_db_value( 'sendupdate' ) == 'on' && cau_get_db_value( 'plugins' ) == 'on' ) {
		cau_plugin_updated(); // Check for updated plugins
	}

	// Notify of required db update
	if( cau_get_db_value( 'dbupdateemails' ) == 'on' ) {
		cau_notify_outofdate_db();
	}

}

// Notify of out of date software
function cau_outdated_notifier_mail() {
	if( cau_get_db_value( 'sendoutdated' ) == 'on' ) {
		cau_list_outdated_software();  // Check for oudated plugins
	}
}

// Ge the emailadresses it should be send to
function cau_set_email() {

	$emailArray 	= array();

	if( cau_get_db_value( 'email' ) == '' ) {
		array_push( $emailArray, get_option('admin_email') );
	} else {
		$emailAdresses 	= cau_get_db_value( 'email' );
		$list 			= explode( ", ", $emailAdresses );
		foreach ( $list as $key ) {
			array_push( $emailArray, $list );	
		}
	}

	return $emailArray;

}

// Mail format
function cau_is_html() {
	if ( !function_exists( 'cau_get_db_value' ) ) require_once( plugin_dir_path( __FILE__ ) . 'cau_function.php' );
	return ( cau_get_db_value( 'html_or_text' ) == 'html' ) ? true : false;
}

// Set the content for the emails about pending updates
function cau_outdated_message( $single, $plural, $list ) {

	// WP version 
	$wpversion = get_bloginfo( 'version' );

	// Base text
	/* translators: update type, like 'plugins' or 'themes' and site-url */
	$text = sprintf( esc_html__( 'You have %1$s on your WordPress site at %2$s that have not been tested with the latest 3 major releases of WordPress.', 'companion-auto-update' ), $plural, get_site_url() );
	$text .= "\n";
	$text .= "\n";

	// The list
	if( !empty( $list ) ) {

		/* translators: update type, like 'plugins' or 'themes' and the wordpress version */
		$text .= sprintf( esc_html__( 'The following %1$s have not been tested with WordPress %2$s:', 'companion-auto-update' ), $plural, $wpversion );
		$text .= "\n";
		$text .= "\n";

		foreach ( $list as $plugin => $version ) {
			if( $version == '' ) $version = esc_html__( "Unknown", "companion-auto-update" );
			/* translators: plugin/theme name and the wordpress version */
			$text .= "- ".sprintf( esc_html__( '%1$s tested up to: %2$s', 'companion-auto-update' ), $plugin, $version )."\n";
		}

	}

	return $text;

}

// Set the content for the emails about pending updates
function cau_pending_message( $single, $plural, $list ) {

	// What markup to use
	if( cau_is_html() ) $break = '<br />';
	else $break = "\n";

	// Base text
	/* translators: update type, like 'plugins' or 'themes' and site-url */
	$text = sprintf( esc_html__( 'You have pending %1$s updates on your WordPress site at %2$s.', 'companion-auto-update' ), $single, get_site_url() );
	$text .= $break;

	if( !empty( $list ) ) {
		
		$text .= $break;
		/* translators: update type, like 'plugins' or 'themes' */
		$text .= sprintf( esc_html__( 'The following %1$s have new versions available.', 'companion-auto-update' ), $plural );
		$text .= $break;

		if( cau_is_html() ) $text .= "<ol>";
		foreach ( $list as $key => $value ) {
			$text .= cau_is_html() ? "<li>$value</li>" : "-$value\n";
		}
		if( cau_is_html() ) $text .= "</ol>";
		
		$text .= $break;
	}

	$text .= esc_html__( 'Leaving your site outdated is a security risk so please consider manually updating them.', 'companion-auto-update' );
	$text .= $break;

	// End
	/* translators: url to the wordpress update page */
	$text .= sprintf( esc_html__( 'Head over to %1$s and check the ones you want to update.', 'companion-auto-update' ), get_admin_url().'update-core.php' );

	return $text;

}

// Set the content for the emails about recent updates
function cau_updated_message( $type, $updatedList ) {

	// What markup to use
	if( cau_is_html() ) $break = '<br />';
	else $break = "\n";

	// The message
	/* translators: update type, like 'plugins' or 'themes' and site-url */
	$text = sprintf( esc_html__( 
		'One or more %1$s on your WordPress site at %2$s have been updated by Companion Auto Update. No further action is needed on your part. 
For more info on what is new visit your dashboard and check the changelog.', 'companion-auto-update'
	), $type, get_site_url() );

	$text .= $break;
	$text .= $break;

	/* translators: update type, like 'plugins' or 'themes' */
	$text .= sprintf( esc_html__( 
		'The following %1$s have been updated:', 'companion-auto-update'
	), $type );

	$text .= $break;
	$text .= $updatedList;

	$text .= $break;
	$text .= esc_html__( "(You'll also receive this email if you manually updated a plugin or theme)", "companion-auto-update"  );

	return $text;

}

// Checks if plugins are out of date
function cau_list_outdated_software() {

	// Check if cau_get_db_value() function exists.
	if ( !function_exists( 'cau_get_db_value' ) ) require_once( plugin_dir_path( __FILE__ ) . 'cau_function.php' );

	// Set up mail
	$subject 		= '['.get_bloginfo( 'name' ).'] ' . esc_html__( 'You have outdated plugins on your site.', 'companion-auto-update' );
	$type 			= esc_html__( 'plugin', 'companion-auto-update' );
	$type_plural	= esc_html__( 'plugins', 'companion-auto-update' );
	$message 		= cau_outdated_message( $type, $type_plural, cau_list_outdated() );

	// Send to all addresses
	foreach ( cau_set_email() as $key => $value ) {
		foreach ( $value as $k => $v ) {
			wp_mail( $v, $subject, $message );
		}
		break;
	}

}

// Checks if theme updates are available
function cau_list_theme_updates() {

	global $wpdb;
	$table_name = $wpdb->prefix . "auto_updates"; 

	$configs = $wpdb->get_results( "SELECT * FROM $table_name WHERE name = 'themes'");
	foreach ( $configs as $config ) {

		if( $config->onoroff != 'on' ) {

			// Check for required files
			if ( !function_exists( 'get_theme_updates' ) ) {
				require_once ABSPATH . 'wp-admin/includes/update.php';
			}

			// Begin
			$themes = get_theme_updates();
			$list 	= array();

			if ( !empty( $themes ) ) {
				
				foreach ( $themes as $stylesheet => $theme ) {
					array_push( $list, $theme->get( 'Name' ) );
				}

				$subject 		= '[' . get_bloginfo( 'name' ) . '] ' . esc_html__( 'Theme update available.', 'companion-auto-update' );
				$type 			= esc_html__('theme', 'companion-auto-update');
				$type_plural	= esc_html__('themes', 'companion-auto-update');
				$message 		= cau_pending_message( $type, $type_plural, $list );
				
				foreach ( cau_set_email() as $key => $value) {
					foreach ($value as $k => $v) {
						wp_mail( $v, $subject, $message );
					}
					break;
				}
			}

		}

	}

}

// Checks if plugin updates are available
function cau_list_plugin_updates() {
	
	global $wpdb;
	$table_name = $wpdb->prefix . "auto_updates"; 

	$configs = $wpdb->get_results( "SELECT * FROM $table_name WHERE name = 'plugins'");
	foreach ( $configs as $config ) {

		if( $config->onoroff != 'on' ) {

			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';

			// Make sure get_plugin_updates() and get_plugins() are defined
			if ( !function_exists( 'get_plugin_updates' ) OR !function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
				require_once ABSPATH . 'wp-admin/includes/update.php';
			}

			// Begin
			$plugins = get_plugin_updates();

			if ( !empty( $plugins ) ) {

				$list = array();
				foreach ( (array) $plugins as $plugin_file => $plugin_data ) {
					$plugin_data 	= (object) _get_plugin_data_markup_translate( $plugin_file, (array) $plugin_data, false, true );
					$name 			= $plugin_data->Name;
					array_push( $list, $name );
				}

				$subject 		= '[' . get_bloginfo( 'name' ) . '] ' . esc_html__( 'Plugin update available.', 'companion-auto-update' );
				$type 			= esc_html__( 'plugin', 'companion-auto-update' );
				$type_plural	= esc_html__( 'plugins', 'companion-auto-update' );
				$message 		= cau_pending_message( $type, $type_plural, $list );

				foreach ( cau_set_email() as $key => $value) {
					foreach ($value as $k => $v) {
						wp_mail( $v, $subject, $message );
					}
					break;
				}
			}

		}

	}
}

// Alerts when plugin has been updated
function cau_plugin_updated() {

	// Check if cau_get_db_value() function exists.
	if ( !function_exists( 'cau_get_db_value' ) ) require_once( plugin_dir_path( __FILE__ ) . 'cau_function.php' );

	// Create arrays
	$pluginNames 	= array();
	$pluginDates 	= array();
	$pluginVersion 	= array();
	$pluginSlug  	= array();
	$pluginTimes 	= array();
	$themeNames 	= array();
	$themeDates 	= array();
	$themeTimes		= array();

	// Where to look for plugins
	$plugdir    	= plugin_dir_path( __DIR__ );
	if ( !function_exists( 'get_plugins' ) ) require_once( ABSPATH . 'wp-admin/includes/plugin.php' );  // Check if get_plugins() function exists.
	$allPlugins 	= get_plugins();

	// Where to look for themes
	$themedir   	= get_theme_root();
	$allThemes 		= wp_get_themes();

	// Mail schedule
	$schedule_mail 	= wp_get_schedule( 'cau_set_schedule_mail' );

	// Loop trough all plugins
	foreach ( $allPlugins as $key => $value ) {

		// Get plugin data
		$fullPath 	= $plugdir.'/'.$key;
		$getFile 	= $path_parts = pathinfo( $fullPath );
		$pluginData = get_plugin_data( $fullPath );

		// Get the slug
		$explosion 		= explode( '/', $key );
		$actualSlug 	= array_shift( $explosion );

		// Get last update date
		$fileDate 	= date ( 'YmdHi', filemtime( $fullPath ) );

		switch ( $schedule_mail ) {
			case 'hourly':
				$lastday = date( 'YmdHi', strtotime( '-1 hour', time() ) );
				break;
			case 'twicedaily':
				$lastday = date( 'YmdHi', strtotime( '-12 hours', time() ) );
				break;
			default:
				$lastday = date( 'YmdHi', strtotime( '-1 day', time() ) );
				break;
		}

		$dateFormat = get_option( 'date_format' );
		$timestamp 	= date_i18n( $dateFormat, filemtime( $fullPath ) );
		$timestamp .= ' - '.date( 'H:i', filemtime( $fullPath ) );

		if( $fileDate >= $lastday ) {

			// Get plugin name
			foreach ( $pluginData as $dataKey => $dataValue ) {
				if( $dataKey == 'Name') {
					array_push( $pluginNames , $dataValue );
				}
				if( $dataKey == 'Version') {
					array_push( $pluginVersion , $dataValue );
				}
			}

			array_push( $pluginDates, $fileDate );
			array_push( $pluginSlug, $actualSlug );
			array_push( $pluginTimes, $timestamp );
		}

	}

	// Loop trough all themes
	foreach ( $allThemes as $key => $value ) {

		// Get theme data
		$fullPath 	= $themedir.'/'.$key;
		$getFile 	= $path_parts = pathinfo( $fullPath );

		// Get last update date
		$dateFormat = get_option( 'date_format' );
		$fileDate 	= date ( 'YmdHi', filemtime( $fullPath ) );

		if( $schedule_mail == 'hourly' ) {
			$lastday = date( 'YmdHi', strtotime( '-1 hour', time() ) );
		} elseif( $schedule_mail == 'twicedaily' ) {
			$lastday = date( 'YmdHi', strtotime( '-12 hours', time() ) );
		} elseif( $schedule_mail == 'daily' ) {
			$lastday = date( 'YmdHi', strtotime( '-1 day', time() ) );
		}

		$dateFormat = get_option( 'date_format' );
		$timestamp 	= date_i18n( $dateFormat, filemtime( $fullPath ) );
		$timestamp .= ' - '.date( 'H:i', filemtime( $fullPath ) );

		if( $fileDate >= $lastday ) {
			array_push( $themeNames, $path_parts['filename'] );
			array_push( $themeDates, $fileDate );
			array_push( $themeTimes, $timestamp );
		}

	}
	
	$totalNumP 		= 0;
	$totalNumT		= 0;
	$updatedListP 	= '';
	$updatedListT 	= '';
	
	if( cau_get_db_value( 'html_or_text' ) == 'html' ) {
		$updatedListP 	.= '<ol>';
		$updatedListT 	.= '<ol>';
	}

	foreach ( $pluginDates as $key => $value ) {

		// Set up some var
		$plugin_name 	= $pluginNames[$key];
		$plugin_slug 	= $pluginSlug[$key];
		$to_version		= esc_html__( "to version", "companion-auto-update" ).' '.$pluginVersion[$key];
		$more_info_arr	= array( esc_html__( "Time of update", "companion-auto-update" ) => $pluginTimes[$key] );

		// Plugin links
		if( cau_get_db_value( 'plugin_links_emails' ) == 'on' ) {
			$more_info_arr[esc_html__( "Plugin details", "companion-auto-update" )] 	= "<a href='https://wordpress.org/plugins/".esc_html( $plugin_slug )."/'>".esc_html__( "Visit", "companion-auto-update" )."</a>";
			$more_info_arr[esc_html__( "Release notes", "companion-auto-update" )] 		= "<a href='https://wordpress.org/plugins/".esc_html( $plugin_slug )."/#developers'>".esc_html__( "Visit", "companion-auto-update" )."</a>";
			$more_info_arr[esc_html__( "Support", "companion-auto-update" )] 			= "<a href='https://wordpress.org/support/plugin/".esc_html( $plugin_slug )."/'>".esc_html__( "Visit", "companion-auto-update" )."</a>";
		}

		// Email format
		$use_html 		= ( cau_get_db_value( 'html_or_text' ) == 'html' ) ? true : false;

		// Email content
		$updatedListP 	.= $use_html ? "<li>" : "-"; // Start row

			$updatedListP 	.= $use_html ? "<strong>{$plugin_name}</strong> " : "{$plugin_name} "; // Show plugin name
			$updatedListP 	.= $to_version; // To version

			// Get advanced info
			if( cau_get_db_value( 'advanced_info_emails' ) == 'on' ) {
				foreach( $more_info_arr as $label => $value ) {
					$updatedListP 	.= $use_html ? "<br />{$label}: {$value}" : "\n{$label}: {$value}";
				}
			}

		$updatedListP 	.= $use_html ? "</li>" : "\n"; // End row

		$totalNumP++;
	}

	foreach ( $themeNames as $key => $value ) {

		if( cau_get_db_value( 'html_or_text' ) == 'html' ) {

			$more_info = '';
			if( cau_get_db_value( 'advanced_info_emails' ) == 'on' ) $more_info = "<br /><span style='opacity: 0.5;'>".esc_html__( "Time of update", "companion-auto-update" ).": ".$themeTimes[$key]."</span>"; 
			$updatedListT .= "<li><strong>".esc_html( $themeNames[$key] )."</strong>".esc_html( $more_info )."</li>";

		} else {
			$updatedListT .= "- ".esc_attr( $themeNames[$key] )."\n";
		}

		$totalNumT++;
	}

	if( cau_get_db_value( 'html_or_text' ) == 'html' ) {
		$updatedListP 	.= '</ol>';
		$updatedListT 	.= '</ol>';
	}

	// Set the email content type
	if( cau_get_db_value( 'html_or_text' ) == 'html' ) {
		function cau_mail_content_type() {
		    return 'text/html';
		}
		add_filter( 'wp_mail_content_type', 'cau_mail_content_type' );
	}

	// If plugins have been updated, send email
	if( $totalNumP > 0 ) {

		// E-mail content
		$subject 		= '[' . get_bloginfo( 'name' ) . '] ' . esc_html__('One or more plugins have been updated.', 'companion-auto-update');
		$type 			= esc_html__('plugins', 'companion-auto-update');
		$message 		= cau_updated_message( $type, $updatedListP );

		// Send to all addresses
		foreach ( cau_set_email() as $key => $value) {
			foreach ($value as $k => $v) {
				wp_mail( $v, $subject, $message );
			}
			break;
		}

	}

	// If themes have been updated, send email
	if( $totalNumT > 0 ) {

		// E-mail content
		$subject 		= '[' . get_bloginfo( 'name' ) . '] ' . esc_html__('One or more themes have been updated.', 'companion-auto-update');
		$type 			= esc_html__('themes', 'companion-auto-update');
		$message 		= cau_updated_message( $type, $updatedListT );

		// Send to all addresses
		foreach ( cau_set_email() as $key => $value) {
			foreach ($value as $k => $v) {
				wp_mail( $v, $subject, $message );
			}
			break;
		}

	}

	if( cau_get_db_value( 'html_or_text' ) == 'html' ) remove_filter( 'wp_mail_content_type', 'cau_mail_content_type' );
	
	// Prevent duplicate emails by setting the event again
	if( $totalNumT > 0 OR $totalNumP > 0 ) {
		if( $schedule_mail == 'hourly' ) {
			wp_clear_scheduled_hook('cau_set_schedule_mail');
			wp_schedule_event( strtotime( '+1 hour', time() ) , 'hourly', 'cau_set_schedule_mail' );
		}
	}

}

function cau_notify_outofdate_db() {

	// Check if cau_get_db_value() function exists.
	if ( !function_exists( 'cau_get_db_value' ) ) require_once( plugin_dir_path( __FILE__ ) . 'cau_function.php' );

	// Database requires an update
	if ( cau_incorrectDatabaseVersion() ) {

		// Set up mail
		$subject 		= '[' . get_bloginfo( 'name' ) . '] ' . esc_html__( 'We need your help with something', 'companion-auto-update' );
		$message 		= esc_html__( 'Hi there! We need your help updating the database of Companion Auto Update to the latest version. No rush, old features will continue to work but some new features might not work until you update the database.', 'companion-auto-update' );

		// Send to all addresses
		foreach ( cau_set_email() as $key => $value ) {
			foreach ( $value as $k => $v ) {
				wp_mail( $v, $subject, $message );
			}
			break;
		}

	}

}
