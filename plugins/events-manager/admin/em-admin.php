<?php
//Admin functions
function em_admin_menu(){
	global $menu, $submenu, $pagenow;
	//Count pending bookings	
   	if( get_option('dbem_rsvp_enabled') ){
		$bookings_num = '';
		$bookings_pending_count = apply_filters('em_bookings_pending_count',0);
		if( get_option('dbem_bookings_approval') == 1){ 
			$bookings_pending_count += EM_Bookings::count(array('status'=>'0', 'blog'=>get_current_blog_id()));
		}
		if($bookings_pending_count > 0){
			$bookings_num = '<span class="update-plugins count-'.$bookings_pending_count.'"><span class="plugin-count">'.$bookings_pending_count.'</span></span>';
		}
   	}else{
   		$bookings_num = '';
		$bookings_pending_count = 0;
   	}
	//Count pending events
	$events_num = '';
	$events_pending_count = EM_Events::count(array('status'=>0, 'scope'=>'all', 'blog'=>get_current_blog_id()));
	//TODO Add flexible permissions
	if($events_pending_count > 0){
		$events_num = '<span class="update-plugins count-'.$events_pending_count.'"><span class="plugin-count">'.$events_pending_count.'</span></span>';
	}
	//Count pending recurring events
	$events_recurring_num = '';
	$events_recurring_pending_count = EM_Events::count(array('status'=>0, 'recurring'=>1, 'scope'=>'all', 'blog'=>get_current_blog_id()));
	//TODO Add flexible permissions
	if($events_recurring_pending_count > 0){
		$events_recurring_num = '<span class="update-plugins count-'.$events_recurring_pending_count.'"><span class="plugin-count">'.$events_recurring_pending_count.'</span></span>';
	}
	$both_pending_count = apply_filters('em_items_pending_count', $events_pending_count + $bookings_pending_count + $events_recurring_pending_count);
	$both_num = ($both_pending_count > 0) ? '<span class="update-plugins count-'.$both_pending_count.'"><span class="plugin-count">'.$both_pending_count.'</span></span>':'';
  	// Add a submenu to the custom top-level menu:
   	$plugin_pages = array();
   	if( get_option('dbem_rsvp_enabled') ){
		$plugin_pages['bookings'] = add_submenu_page('edit.php?post_type='.EM_POST_TYPE_EVENT, __('Bookings', 'events-manager'), __('Bookings', 'events-manager').$bookings_num, 'manage_bookings', 'events-manager-bookings', "em_bookings_page");
   	}
	$plugin_pages['options'] = add_submenu_page('edit.php?post_type='.EM_POST_TYPE_EVENT, __('Events Manager Settings','events-manager'),__('Settings','events-manager'), 'manage_options', "events-manager-options", 'em_admin_options_page');
	$plugin_pages['help'] = add_submenu_page('edit.php?post_type='.EM_POST_TYPE_EVENT, __('Getting Help for Events Manager','events-manager'),__('Help','events-manager'), 'manage_options', "events-manager-help", 'em_admin_help_page');
	//If multisite global with locations set to be saved in main blogs we can force locations to be created on the main blog only
	if( EM_MS_GLOBAL && !is_main_site() && get_site_option('dbem_ms_mainblog_locations') ){
		include( dirname(__FILE__)."/em-ms-locations.php" );
		$plugin_pages['locations'] = add_submenu_page('edit.php?post_type='.EM_POST_TYPE_EVENT, __('Locations','events-manager'),__('Locations','events-manager'), 'read_others_locations', "locations", 'em_admin_ms_locations');
	}
	$plugin_pages = apply_filters('em_create_events_submenu',$plugin_pages);
	//We have to modify the menus manually
	if( !empty($both_num) ){ //Main Event Menu
		//go through the menu array and modify the events menu if found
		foreach ( (array)$menu as $key => $parent_menu ) {
			if ( $parent_menu[2] == 'edit.php?post_type='.EM_POST_TYPE_EVENT ){
				$menu[$key][0] = $menu[$key][0]. $both_num;
				break;
			}
		}
	}
	if( !empty($events_num) && !empty($submenu['edit.php?post_type='.EM_POST_TYPE_EVENT]) ){ //Submenu Event Item
		//go through the menu array and modify the events menu if found
		foreach ( (array)$submenu['edit.php?post_type='.EM_POST_TYPE_EVENT] as $key => $submenu_item ) {
			if ( $submenu_item[2] == 'edit.php?post_type='.EM_POST_TYPE_EVENT ){
				$submenu['edit.php?post_type='.EM_POST_TYPE_EVENT][$key][0] = $submenu['edit.php?post_type='.EM_POST_TYPE_EVENT][$key][0]. $events_num;
				break;
			}
		}
	}
	if( !empty($events_recurring_num) && !empty($submenu['edit.php?post_type='.EM_POST_TYPE_EVENT]) ){ //Submenu Recurring Event Item
		//go through the menu array and modify the events menu if found
		foreach ( (array)$submenu['edit.php?post_type='.EM_POST_TYPE_EVENT] as $key => $submenu_item ) {
			if ( $submenu_item[2] == 'edit.php?post_type=event-recurring' ){
				$submenu['edit.php?post_type='.EM_POST_TYPE_EVENT][$key][0] = $submenu['edit.php?post_type='.EM_POST_TYPE_EVENT][$key][0]. $events_recurring_num;
				break;
			}
		}
	}
	/* Hack! Add location/recurrence isn't possible atm so this is a workaround */
	global $_wp_submenu_nopriv;
	if( $pagenow == 'post-new.php' && !empty($_REQUEST['post_type']) ){
		if( $_REQUEST['post_type'] == EM_POST_TYPE_LOCATION && !empty($_wp_submenu_nopriv['edit.php']['post-new.php']) && current_user_can('edit_locations') ){
			unset($_wp_submenu_nopriv['edit.php']['post-new.php']);
		}
		if( $_REQUEST['post_type'] == 'event-recurring' && !empty($_wp_submenu_nopriv['edit.php']['post-new.php']) && current_user_can('edit_recurring_events') ){
			unset($_wp_submenu_nopriv['edit.php']['post-new.php']);
		}
	}
}
add_action('admin_menu','em_admin_menu');

function em_admin_dashicon(){
	?>
	<style type="text/css">
		@font-face {
		  font-family: 'em_dashicons';
		  src: url('<?php echo EM_DIR_URI; ?>includes/fonts/em-dashicons.eot'); // this is for IE
		}
		@font-face {
		  font-family: 'em_dashicons';
		  src: url(data:application/font-woff;charset=utf-8;base64,d09GRk9UVE8AAAVIAAoAAAAABQAAAQAAAAAAAAAAAAAAAAAAAAAAAAAAAABDRkYgAAAA9AAAAb8AAAG/+vk+uU9TLzIAAAK0AAAAYAAAAGAIIvy2Y21hcAAAAxQAAABMAAAATBpVzFdnYXNwAAADYAAAAAgAAAAIAAAAEGhlYWQAAANoAAAANgAAADYBZ6hBaGhlYQAAA6AAAAAkAAAAJAPIAeZobXR4AAADxAAAABQAAAAUAwAAM21heHAAAAPYAAAABgAAAAYABVAAbmFtZQAAA+AAAAFFAAABRVcZpu5wb3N0AAAFKAAAACAAAAAgAAMAAAEABAQAAQEBCGljb21vb24AAQIAAQA6+BwC+BsD+BgEHgoAGVP/i4seCgAZU/+LiwwHi2v4lPh0BR0AAAB2Dx0AAAB7ER0AAAAJHQAAAbYSAAYBAQgPERMWG2ljb21vb25pY29tb29udTB1MXUyMHVFNjAwAAACAYkAAwAFAgABAAQABwAKAA0BLfyUDvyUDvyUDvuUDvcu+FoVfIuAgIt9CItYBYt8loCai5mLlpaLmgiLvgWLmYCWfYsI93qLFX2Lf4CLfQiLWAWLfJeAmYuZi5eWi5oIi74Fi5l/ln2LCPth+0cVi1hYi4u+vosFpYsVvouLWFiLi74F2IsVvouLWFiLi74F14sVv4uLWFeLi74Fiz4VvouLWFiLi74FPosVv4uLWFeLi74FPosVv4uLWFeLi74FP4sVvouLWFiLi74F1z8Vv4uLV1eLi78F2IsVv4uLV1eLi78F9y33ihWGc3d5cotui3Wii6gIi5cFdI91jXiLd4t1iXWHCIt+BYtvdHRui3KLd52Go2yCdoSLi4uLi2eLfIv7lfduWYuLi4v3bb2L95WLmYuwi4uLi3aSbJQIDviUFPiUFYsMCgAAAwIAAZAABQAAAUwBZgAAAEcBTAFmAAAA9QAZAIQAAAAAAAAAAAAAAAAAAAABEAAAAAAAAAAAAAAAAAAAAABAAADmAAHg/+D/4AHgACAAAAABAAAAAAAAAAAAAAAgAAAAAAACAAAAAwAAABQAAwABAAAAFAAEADgAAAAKAAgAAgACAAEAIOYA//3//wAAAAAAIOYA//3//wAB/+MaBAADAAEAAAAAAAAAAAAAAAEAAf//AA8AAQAAAAEAAIXyBpNfDzz1AAsCAAAAAADQMTPCAAAAANAxM8IAAP/6AeYBxgAAAAgAAgAAAAAAAAABAAAB4P/gAAACAAAAAAAB5gABAAAAAAAAAAAAAAAAAAAABQAAAAAAAAAAAAAAAAEAAAACAAAzAABQAAAFAAAAAAAOAK4AAQAAAAAAAQAOAAAAAQAAAAAAAgAOAEcAAQAAAAAAAwAOACQAAQAAAAAABAAOAFUAAQAAAAAABQAWAA4AAQAAAAAABgAHADIAAQAAAAAACgA0AGMAAwABBAkAAQAOAAAAAwABBAkAAgAOAEcAAwABBAkAAwAOACQAAwABBAkABAAOAFUAAwABBAkABQAWAA4AAwABBAkABgAOADkAAwABBAkACgA0AGMAaQBjAG8AbQBvAG8AbgBWAGUAcgBzAGkAbwBuACAAMQAuADAAaQBjAG8AbQBvAG8Abmljb21vb24AaQBjAG8AbQBvAG8AbgBSAGUAZwB1AGwAYQByAGkAYwBvAG0AbwBvAG4ARgBvAG4AdAAgAGcAZQBuAGUAcgBhAHQAZQBkACAAYgB5ACAASQBjAG8ATQBvAG8AbgAuAAAAAAMAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=) format('woff'), 
		    url(data:application/x-font-ttf;charset=utf-8;base64,AAEAAAALAIAAAwAwT1MvMggi/LYAAAC8AAAAYGNtYXAaVcxXAAABHAAAAExnYXNwAAAAEAAAAWgAAAAIZ2x5ZsaoTWEAAAFwAAACFGhlYWQBZ6hBAAADhAAAADZoaGVhA8gB5gAAA7wAAAAkaG10eAMAADMAAAPgAAAAFGxvY2EAKAEeAAAD9AAAAAxtYXhwABMAowAABAAAAAAgbmFtZVcZpu4AAAQgAAABRXBvc3QAAwAAAAAFaAAAACAAAwIAAZAABQAAAUwBZgAAAEcBTAFmAAAA9QAZAIQAAAAAAAAAAAAAAAAAAAABEAAAAAAAAAAAAAAAAAAAAABAAADmAAHg/+D/4AHgACAAAAABAAAAAAAAAAAAAAAgAAAAAAACAAAAAwAAABQAAwABAAAAFAAEADgAAAAKAAgAAgACAAEAIOYA//3//wAAAAAAIOYA//3//wAB/+MaBAADAAEAAAAAAAAAAAAAAAEAAf//AA8AAQAAAAAAAAAAAAIAADc5AQAAAAABAAAAAAAAAAAAAgAANzkBAAAAAAEAAAAAAAAAAAACAAA3OQEAAAAADQAz//oB5gHGABoANQA6AD8ARABJAE4AUwBYAF0AYgBnAKAAABMiBgcOAQcXBhYXHgEzMjY3PgE3JzYmJy4BIzMiBgcOARcHHgEXHgEzMjY3PgEnNy4BJy4BIwcXIzczOwEXIzc7AQcjJzsBFyM3BzMHIycjMxcjNyMzByMnIzMXIzcXMwcjJzsBFyM3Nw4BBw4BIyImJy4BJzcuASciJiMiBiMOAQ8BFgYHDgEjIiYnLgEnDgEzIhYHHgEzIjYnNiYzIiYnmgUKAgUDAQEBBQMECAcECgMEAwEBAQUCBQgG5wYJBAMFAQEBAwUCCwQGCQQDBQEBAQMFAgsEzgE0ATIbMgE0AUw0ATIBTTMBNQEBNAEyAUwzATUBTjUBMwFLMgE0AUs1ATMBTjMBNQGYAQoGBxAKChQFCAcBAQoPCQcRBgkOCQgRBwEBCgUIEgwIEgUICAMWHgEBAQEB2QEB2gEBAQEBHBgBxgQDBAkFMwYJBAMEBAMECQYzBQkEAwQEAwQJBTMGCQQDBAQDBAkGMwUJBAMEszMzMzMzMzMzTTMzMzMzMzMzTDQ0NDT2CQ8GBQcIBwcTCwwCAgEBAQECAg0KEwcHCAcFBg8JBwkoC8FycsELKAkHAAEAAAABAACs5UCKXw889QALAgAAAAAA0DEzwgAAAADQMTPCAAD/+gHmAcYAAAAIAAIAAAAAAAAAAQAAAeD/4AAAAgAAAAAAAeYAAQAAAAAAAAAAAAAAAAAAAAUAAAAAAAAAAAAAAAABAAAAAgAAMwAAAAAACgAUAB4BCgABAAAABQChAA0AAAAAAAIAAAAAAAAAAAAAAAAAAAAAAAAADgCuAAEAAAAAAAEADgAAAAEAAAAAAAIADgBHAAEAAAAAAAMADgAkAAEAAAAAAAQADgBVAAEAAAAAAAUAFgAOAAEAAAAAAAYABwAyAAEAAAAAAAoANABjAAMAAQQJAAEADgAAAAMAAQQJAAIADgBHAAMAAQQJAAMADgAkAAMAAQQJAAQADgBVAAMAAQQJAAUAFgAOAAMAAQQJAAYADgA5AAMAAQQJAAoANABjAGkAYwBvAG0AbwBvAG4AVgBlAHIAcwBpAG8AbgAgADEALgAwAGkAYwBvAG0AbwBvAG5pY29tb29uAGkAYwBvAG0AbwBvAG4AUgBlAGcAdQBsAGEAcgBpAGMAbwBtAG8AbwBuAEYAbwBuAHQAIABnAGUAbgBlAHIAYQB0AGUAZAAgAGIAeQAgAEkAYwBvAE0AbwBvAG4ALgAAAAADAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA) format('truetype');
		  font-weight: normal;
		  font-style: normal;
		}
		.menu-icon-event .dashicons-calendar:before, #toplevel_page_events-manager-options .dashicons-calendar:before {
		  font-family: 'em_dashicons';
		  content: '\e600';
		}
	</style>
	<?php
}
add_action('admin_head','em_admin_dashicon');

function em_ms_admin_menu(){
	add_menu_page( __('Events Manager','events-manager'), __('Events Manager','events-manager'), 'activate_plugins', 'events-manager-options', 'em_ms_admin_options_page', 'dashicons-calendar' );
	add_submenu_page('events-manager-options', __('Update Blogs','events-manager'),__('Update Blogs','events-manager'), 'activate_plugins', "events-manager-update", 'em_ms_upgrade');
}
add_action('network_admin_menu','em_ms_admin_menu');

function em_admin_init(){
	//in MS global mode and locations are stored in the main blog, then a user must have at least a subscriber role
	if( EM_MS_GLOBAL && is_user_logged_in() && !is_main_site() && get_site_option('dbem_ms_mainblog_locations') ){
		EM_Object::ms_global_switch();
		$user = new WP_User(get_current_user_id());
		if( count($user->roles) == 0 ){
			$user->set_role('subscriber');
		}
		EM_Object::ms_global_switch_back();
	}
}
add_action('admin_init','em_admin_init');

/**
 * Generate warnings and notices in the admin area
 */
function em_admin_warnings() {
	global $EM_Notices;
	//If we're editing the events page show hello to new user
	$events_page_id = get_option ( 'dbem_events_page' );
	$dismiss_link_joiner = ( count($_GET) > 0 ) ? '&amp;':'?';
	
	if( current_user_can('activate_plugins') ){
		
		//If events page couldn't be created or is missing
		if( !empty($_GET['em_dismiss_events_page']) && wp_verify_nonce($_GET['em_dismiss_events_page'], 'em_dismiss_notice') ){
			update_option('dbem_dismiss_events_page',1);
		}else{
			if ( !get_page($events_page_id) && !get_option('dbem_dismiss_events_page') ){
				?>
				<div id="em_page_error" class="updated">
					<p><?php echo sprintf ( __( 'Uh Oh! For some reason WordPress could not create an events page for you (or you just deleted it). Not to worry though, all you have to do is create an empty page, name it whatever you want, and select it as your events page in your <a href="%s">settings page</a>. Sorry for the extra step! If you know what you are doing, you may have done this on purpose, if so <a href="%s">ignore this message</a>', 'events-manager'), EM_ADMIN_URL .'&amp;page=events-manager-options', esc_url($_SERVER['REQUEST_URI'].$dismiss_link_joiner.'em_dismiss_events_page='.wp_create_nonce('em_dismiss_notice')) ); ?></p>
				</div>
				<?php
			}
		}
	
		if( is_multisite() && !empty($_REQUEST['page']) && $_REQUEST['page']=='events-manager-options' && em_wp_is_super_admin() && get_option('dbem_ms_update_nag') ){
			if( !empty($_GET['disable_dbem_ms_update_nag'])  && wp_verify_nonce($_GET['disable_dbem_ms_update_nag'], 'em_dismiss_notice') ){
				delete_site_option('dbem_ms_update_nag');
			}else{
				?>
				<div id="em_page_error" class="updated">
					<p><?php echo sprintf(__('MultiSite options have moved <a href="%s">here</a>. <a href="%s">Dismiss message</a>','events-manager'),admin_url().'network/admin.php?page=events-manager-options', esc_url($_SERVER['REQUEST_URI'].'&amp;disable_dbem_ms_update_nag='.wp_create_nonce('em_dismiss_notice'))); ?></p>
				</div>
				<?php
			}
		}
		if( !empty($_REQUEST['page']) && 'events-manager-options' == $_REQUEST['page'] && get_option('dbem_pro_dev_updates') == 1 ){
			?>
			<div id="message" class="updated">
				<p><?php echo sprintf(__('Dev Mode active: Just a friendly reminder that you are updating to development versions. Only admins see this message, and it will go away when you disable this <a href="#pro-api">here</a> in your settings.','events-manager'),'<code>define(\'EMP_DEV_UPDATES\',true);</code>'); ?></p>
			</div>
			<?php
		}
		if( class_exists('SitePress') && !class_exists('EM_WPML') && !get_site_option('disable_em_wpml_warning') ){
			if( !empty($_REQUEST['disable_em_wpml_warning']) && wp_verify_nonce($_GET['disable_em_wpml_warning'], 'em_dismiss_notice') ){
				update_site_option('disable_em_wpml_warning',1);
			}else{
				?>
				<div id="message" class="updated">
					<p><?php echo sprintf(__('It looks like you have WPML enabled on your site. We advise you also install our extra <a href="%s">Events Manager WPML Connector</a> plugin which helps the two work better together. <a href="%s">Dismiss message</a>','events-manager'),'http://wordpress.org/extend/plugins/events-manager-wpml/', esc_url(add_query_arg(array('disable_em_wpml_warning'=>wp_create_nonce('em_dismiss_notice'))))); ?></p>
				</div>
				<?php
			}
		}
	}
	//Warn about EM page edit
	if ( preg_match( '/(post|page).php/', $_SERVER ['SCRIPT_NAME']) && isset ( $_GET ['action'] ) && $_GET ['action'] == 'edit' && isset ( $_GET ['post'] ) && $_GET ['post'] == "$events_page_id") {
		$message = sprintf ( __ ( "This page corresponds to the <strong>Events Manager</strong> %s page. Its content will be overridden by Events Manager, although if you include the word CONTENTS (exactly in capitals) and surround it with other text, only CONTENTS will be overwritten. If you want to change the way your events look, go to the <a href='%s'>settings</a> page. ", 'events-manager'), __('Events','events-manager'), EM_ADMIN_URL .'&amp;page=events-manager-options' );
		$notice = "<div class='error'><p>$message</p></div>";
		echo $notice;
	}
	echo $EM_Notices;		
}
add_action ( 'admin_notices', 'em_admin_warnings', 100 );

/**
 * Settings link in the plugins page menu
 * @param array $links
 * @param string $file
 * @return array
 */
function em_plugin_action_links($actions, $file, $plugin_data) {
	$new_actions = array();
	$new_actions[] = sprintf( '<a href="'.EM_ADMIN_URL.'&amp;page=events-manager-options">%s</a>', __('Settings', 'events-manager') );
	$new_actions = array_merge($new_actions, $actions);
	if( is_multisite() ){
		$uninstall_url = admin_url().'network/admin.php?page=events-manager-options&amp;action=uninstall&amp;_wpnonce='.wp_create_nonce('em_uninstall_'.get_current_user_id().'_wpnonce');
	}else{
		$uninstall_url = EM_ADMIN_URL.'&amp;page=events-manager-options&amp;action=uninstall&amp;_wpnonce='.wp_create_nonce('em_uninstall_'.get_current_user_id().'_wpnonce');
	}
	$new_actions[] = '<span class="delete"><a href="'.$uninstall_url.'" class="delete">'.__('Uninstall','events-manager').'</a></span>';
	return $new_actions;
}
add_filter( 'plugin_action_links_events-manager/events-manager.php', 'em_plugin_action_links', 10, 3 );

//Updates and Dev versions
function em_updates_check( $transient ) {
    // Check if the transient contains the 'checked' information
    if( empty( $transient->checked ) )
        return $transient;
        
    //only bother if we're checking for dev versions
    if( get_option('em_check_dev_version') || get_option('dbem_pro_dev_updates') ){     
	    //check WP repo for trunk version, other EM-related plugins on .org can hook here to make the best of our admin setting option
	    $plugins = apply_filters('em_org_dev_versions', array(
	    	'events-manager'=> array(
	    		'slug' => EM_SLUG,
			    'version' => EM_VERSION
		    )
		));
	    foreach( $plugins as $org_slug => $plugin_info ) {
		    $request = wp_remote_get('https://plugins.svn.wordpress.org/'.$org_slug.'/trunk/'.$org_slug.'.php');
		    $wp_slug = $plugin_info['slug'];
		    if( empty($transient->checked[$wp_slug]) ){
			    $transient->checked[$wp_slug] = !empty($plugin_info['version']) ? $plugin_info['version'] : 0;
		    }
		    if (!is_wp_error($request)) {
			    preg_match('/Version: ([0-9a-z\.]+)/', $request['body'], $matches);
			
			    if (!empty($matches[1])) {
				    //we have a version number!
				    $response = new stdClass();
				    $response->slug = $wp_slug;
				    $response->new_version = $matches[1];
				    $response->url = 'http://wordpress.org/extend/plugins/'.$org_slug.'/';
				    $response->package = 'http://downloads.wordpress.org/plugin/'.$org_slug.'.zip';
				    $icon_test = wp_remote_get('https://ps.w.org/'.$org_slug.'/assets/icon-128x128.png');
				    if( !is_wp_error($icon_test) && $icon_test['response']['code'] == 200 ){
					    $response->icons = array(
					        '1x' => 'https://ps.w.org/'.$org_slug.'/assets/icon-128x128.png',
					        '2x' => 'https://ps.w.org/'.$org_slug.'/assets/icon-256x256.png'
					    );
					}
				    if ( version_compare($transient->checked[$wp_slug], $matches[1]) < 0) {
					    $transient->response[$wp_slug] = $response;
				    }else{
					    $transient->no_update[$wp_slug] = $response;
				    }
			    }
		    }
	    }
		delete_option('em_check_dev_version');
    }
    
    return $transient;
}
add_filter('pre_set_site_transient_update_plugins', 'em_updates_check', 100); // Hook into the plugin update check and mod for dev version

function em_user_action_links( $actions, $user ){
	if ( !is_network_admin() && current_user_can( 'manage_others_bookings' ) ){
		if( get_option('dbem_edit_bookings_page') && (!is_admin() || !empty($_REQUEST['is_public'])) ){
			$my_bookings_page = get_permalink(get_option('dbem_edit_bookings_page'));
			$bookings_link = em_add_get_params($my_bookings_page, array('person_id'=>$user->ID), false);
		}else{
			$bookings_link = EM_ADMIN_URL. "&page=events-manager-bookings&person_id=".$user->ID;
		}
		$actions['bookings'] = "<a href='$bookings_link'>" . __( 'Bookings','events-manager') . "</a>";
	}
	return $actions;
}
add_filter('user_row_actions','em_user_action_links',10,2);

add_filter('em_admin_notice_v7-reconvert-recurrences_message', function(){
	ob_start();
	?>
	It looks like you may have previously upgraded from Events Manager 6 and converted your repeating events into our new recurring events format in Events Manager 7
	<p>We recommend running the conversion one more time due to some issues that may have prevented the full conversion in versions 7.0 - 7.0.2.</p>
	<?php
	$warning = esc_html__('This will delete all event recurrence posts/pages, and unify them into one URL. Any 404 pages resulting from these will automatically 302-redirect to the recurring event (which will be a new URL).', 'events-manager');
	$convert_url = esc_url( add_query_arg( ['action' => 'convert_repeating_to_recurrence', 'nonce' => 'x'], EM_ADMIN_URL .'&page=events-manager-options' ) );
	$convert_nonce = wp_create_nonce('convert_repeating_to_recurrence_'. get_current_user_id());
	EM\Scripts_and_Styles::add_js_var('convert_recurring_warning', __('Are you sure you want to convert all repeating events into recurring events?', 'events-manager') . "\n\n" . __('WARNING: This action cannot be undone.', 'events-manager') . "\n\n"  . $warning);
	?>
	<p><a href="<?php echo esc_url($convert_url); ?>" class="button-secondary em-convert-recurrence-link" data-nonce="<?php echo esc_attr($convert_nonce); ?>"><?php esc_html_e('Convert ALL Repeating Events', 'events-manager'); ?></a></p>
	<?php
	return ob_get_clean();
});
?>