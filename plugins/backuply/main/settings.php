<?php

/*
* BACKUPLY
* https://backuply.com
* (c) Backuply Team
*/

if(!defined('ABSPATH')) {
	die('HACKING ATTEMPT!');
}

// The Backuply Admin Options Page
function backuply_page_header($title = 'Settings') {
	global $backuply;
	
	wp_enqueue_style('backuply-styles', BACKUPLY_URL . '/assets/css/styles.css', array(), BACKUPLY_VERSION);
	
	// TODO:: Is only being used for a modal so create custom modal.
	wp_enqueue_style('backuply-jquery-ui', BACKUPLY_URL . '/assets/css/base-jquery-ui.css', array(), BACKUPLY_VERSION);
	wp_enqueue_style('backuply-jstree', BACKUPLY_URL . '/assets/css/jstree.css', array(), BACKUPLY_VERSION);

	wp_enqueue_script('backuply-js', BACKUPLY_URL . '/assets/js/backuply.js', array('jquery-ui-dialog'), BACKUPLY_VERSION, true);
	wp_enqueue_script('backuply-jstree', BACKUPLY_URL . '/assets/js/jstree.min.js', array('jquery'), BACKUPLY_VERSION, true);
	
	wp_localize_script('backuply-js', 'backuply_obj', array(
		'nonce' => wp_create_nonce('backuply_nonce'),
		'ajax_url' => admin_url('admin-ajax.php'),
		'cron_task' => get_option('backuply_cron_settings'),
		'images' => BACKUPLY_URL . '/assets/images/',
		'backuply_url' => BACKUPLY_URL,
		'creating_session' => wp_generate_password(32, false),
		'status_key' => urlencode(backuply_get_status_key()),
		'site_url' => site_url(),
	));
	
	if(defined('BACKUPLY_PRO')){
		backuply_load_license();
	}

	// Updating the Timezone to match the time of the users WordPress timezone
	$time_zone_string = wp_timezone_string();
	
	if(strpos($time_zone_string, '-') === 0 || strpos($time_zone_string, '+') === 0){
		$time_zone_string = 'UTC';
	}
	
	date_default_timezone_set($time_zone_string);
	
	$bcloud_trial_time = get_option('bcloud_trial_time', 0);

	if(!empty($bcloud_trial_time)){
		$bcloud_rem_time = $bcloud_trial_time - time();
		$bcloud_rem_time = floor($bcloud_rem_time/86400);
		if($bcloud_rem_time < 0 || empty($backuply['bcloud_key']) || defined('BACKUPLY_PRO')){
			delete_option('bcloud_trial_time');
			$bcloud_rem_time = '';
		}
		
	}

	echo '<div id="backuply-snackbar"></div>
	<div style="margin: 10px 20px 0 2px;">	
<div class="metabox-holder columns-2">
<div class="postbox-container">	
<div id="top-sortables" class="meta-box-sortables ui-sortable">
	
	<table cellpadding="2" cellspacing="1" width="100%" class="fixed" border="0">
		<tr>
			<td valign="top"><h3>Backuply - '.esc_html($title). (!empty($bcloud_rem_time) ? '&nbsp;<span style="background-color:red; font-size:12px; color:white; padding: 5px 7px; border-radius:3px;">'.esc_html($bcloud_rem_time).' days left for Backuply Cloud trial</span>' : '').'</h3></td>
			<td valign="center" align="right" style="font-size:1.1rem; line-height:1.6;">'.
			(!defined('SITEPAD') ? '<a href="https://wordpress.org/plugins/backuply" target="_blank" class="button button-secondary" style="margin-right:10px">Review Backuply</a>' : '').
			'<a href="https://twitter.com/wpbackuply" target="_blank" title="Twitter Profile"><span class="dashicons dashicons-twitter"></span></a>
			<a href="https://facebook.com/backuply" target="_blank" title="Facebook Profile"><span class="dashicons dashicons-facebook-alt"></span></a>
			<a href="'.BACKUPLY_WWW_URL.'" target="_blank" title="Backuply Website"><span class="dashicons dashicons-admin-site-alt3"></span></a>
			<a href="'.BACKUPLY_DOCS.'" target="_blank" title="Backuply Docs"><span class="dashicons dashicons-media-document"></span></a>
		</td>
		</tr>
	</table>
	<hr />
	
	<!--Main Table-->
	<table cellpadding="8" cellspacing="1" width="100%" class="fixed">
	<tr>
		<td valign="top" width="82%">';
}

function backuply_promotion_tmpl() {
	global $backuply;
	
?>
	<a href="https://wordpress.org/support/plugin/backuply/reviews/?filter=5#new-post" target="_blank" style="text-decoration:none;">
	<div class="backuply-promotion-content" style="background: rgb(56,120,255);
background: linear-gradient(61deg, rgba(56,120,255,1) 0%, rgba(98,178,255,1) 100%);
 color: white; margin-bottom:10px; position:relative;">
		<h2 style="color:white; margin:0 0 5px 0; padding:0;">Rate us <span class="dashicons dashicons-star-filled"></span></h2>
		<p style="margin:0; padding:0;"><?php esc_html_e('If you find it useful, Please rate and support us.', 'backuply');?></p>
	</div>
	</a>
	
	<?php
	if(empty($backuply['bcloud_key'])){
		echo '<div class="backuply-promotion-content backuply-cloud-banner" style="background-color:#000;">
			<div class="backuply-cloud-gtext"><div>Backuply</div> <div>Cloud</div></div>
			<div class="bcloud-banner-content">
				<ul>
					<li>'.__('Never lose your data with Backuply Cloud.', 'backuply').'</li>
					<li>'.__('Easy to integrate with Backuply.', 'backuply').'</li>
					<li>'.__('10 GB of free storage for All Pro users', 'backuply').'</li>
				</ul>
				<div style="text-align:center;">
					<a href="https://backuply.com/wordpress-backup-trial" class="backuply-trial-btn" target="_blank">'.__('Try Now').' <span class="dashicons dashicons-external"></span></a>
				</div>
			</div>
		</div>';
	}
	
	?>
	<div class="backuply-promotion-content" style="margin-top: 20px;">
			<h2 class="hndle ui-sortable-handle">
				<span><a target="_blank" href="https://pagelayer.com/?from=backuply-plugin"><img src="<?php echo BACKUPLY_URL ?>/assets/images/pagelayer_product.png" width="100%" /></a></span>
			</h2>
			<div>
				<em>The Best WordPress <b>Site Builder</b> </em>:<br>
				<ul>
					<li>Gutenberg Blocks</li>
					<li>Drag & Drop Editor</li>
					<li>Widgets</li>
					<li>In-line Editing</li>
					<li>Styling Options</li>
					<li>Animations</li>
					<li>Easily customizable</li>
					<li>Real Time Design</li>
					<li>And many more ...</li>
				</ul>
				<center><a class="button button-primary" target="_blank" href="https://pagelayer.com/?from=backuply-plugin">Visit Pagelayer</a></center>
			</div>
	</div>

	<div class="backuply-promotion-content" style="margin-top: 20px;">
		<h2 class="hndle ui-sortable-handle">
			<span><a target="_blank" href="https://loginizer.com/?from=backuply-plugin"><img src="<?php echo BACKUPLY_URL ?>/assets/images/loginizer_product.png" width="100%" /></a></span>
		</h2>
		<div>
			<em>Protect your WordPress website from <b>unauthorized access and malware</b> </em>:<br>
			<ul>
				<li>BruteForce Protection</li>
				<li>reCaptcha</li>
				<li>Two Factor Authentication</li>
				<li>Black/Whitelist IP</li>
				<li>Detailed Logs</li>
				<li>Extended Lockouts</li>
				<li>2FA via Email</li>
				<li>And many more ...</li>
			</ul>
			<center><a class="button button-primary" target="_blank" href="https://loginizer.com/?from=backuply-plugin">Visit Loginizer</a></center>
		</div>
	</div>
<?php
}

function backuply_page_backup(){
	
	global $wpdb, $error, $backuply, $success, $protocols, $bcloud_keys;

	$protocols = backuply_get_protocols();
	
	// Get the current Users Information
	$current_user = wp_get_current_user();

	// Load Backuply's remote backup locations.
	$backuply_remote_backup_locs = get_option('backuply_remote_backup_locs');

	$backuply_backup_dir = backuply_cleanpath(BACKUPLY_BACKUP_DIR);

	// Check if backup/restore is under process
	if(is_dir($backuply_backup_dir.'/restoration')){
		$restoring_up = true;
	}
	
	//if(!get_option('backuply_status') && $timestamp = wp_next_scheduled( 'backuply_backup_cron' )){
		//Unshedule Events here incase the backup fails for some reason. Can tell the users to either delete the sql option or add a button to do that for them
		//wp_unschedule_event( $timestamp, 'backuply_backup_cron' );
	//}

	if(isset($_POST['restore'])){
		
		// Verify Nonce	
		if(!wp_verify_nonce(backuply_optreq('security'), 'backuply_nonce')){
			$error[] = __('Security check Failed!', 'backuply');
			return false;
		}
		
		backuply_restore_curl($_POST);
	}
	
	if(isset($_POST['backuply_delete_backup'])){
		
		// Verify Nonce
		if(!wp_verify_nonce(backuply_optreq('security'), 'backuply_nonce')){
			$error[] = __('Security check Failed!', 'backuply');
			return false;
		}
		
		$res = backuply_delete_backup(backuply_optpost('tar_file'));
		
		if($res) {
			$success = __('The backup was deleted successfully.', 'backuply');
			return true;
		}
	}
	
	// Save backuply Settings
	if(isset($_POST['backuply_save_settings'])) {
		
		// Verify Nonce
		if(!wp_verify_nonce(backuply_optreq('security'), 'backuply_nonce')){
			$error[] = __('Security check Failed!', 'backuply');
			return false;
		}
		
		$tmp_add_to_fileindex = array();
		
		// Save additional file/folder details
		if(isset($_POST['add_to_fileindex'])){
			$tmp_add_to_fileindex = map_deep($_POST['add_to_fileindex'], 'sanitize_text_field');
		}
		
		$saved = false;
		
		if(!empty($tmp_add_to_fileindex)){
			
			foreach($tmp_add_to_fileindex as $tk => $tv){
				
				if(substr_count(trim($tv), './') > 0){
					$error[] = __('There were some invalid files/folders selected', 'backuply'). ' - '.esc_attr($tv);
				}
			}

			$fetch_fileindex = get_option('backuply_additional_fileindex');
			if(empty($fetch_fileindex)){
				add_option('backuply_additional_fileindex', $tmp_add_to_fileindex, false);
				$saved = true;
			}else{
				update_option('backuply_additional_fileindex', $tmp_add_to_fileindex, false);
				$saved = true;
			}
		}else{
			delete_option('backuply_additional_fileindex');
		}

		// Save Email setting
		$notify_email_address = sanitize_email(backuply_optpost('notify_email_address'));

		if(isset($notify_email_address)){
			update_option('backuply_notify_email_address', is_email($notify_email_address) ? $notify_email_address : '');
			$saved = true;
		}

		if(!empty($error)){
			return false;
		}
	
		if(isset($_POST['backuply_cron_schedule'])){
			
			if(empty(backuply_optpost('backuply_cron_schedule'))){
				delete_option('backuply_cron_settings');
				$timestamp = wp_next_scheduled( 'backuply_auto_backup_cron' );
				wp_unschedule_event( $timestamp, 'backuply_auto_backup_cron' );
			}
			
			if(!empty(backuply_optpost('backuply_cron_schedule')) && (!empty(backuply_optpost('auto_backup_dir')) || !empty(backuply_optpost('auto_backup_db')))){

				$cron_settings = [];

				$cron_settings['backup_dir'] = isset($_POST['auto_backup_dir']) ? 1 : 0;
				$cron_settings['backup_db'] = isset($_POST['auto_backup_db']) ? 1 : 0;
				$cron_settings['backup_rotation'] = backuply_optpost('backup_rotation');
				$cron_settings['backup_location'] = backuply_optpost('backup_location');

				if($_POST['backuply_cron_schedule'] == 'custom') {
					$cron_settings['backuply_cron_schedule'] = backuply_optpost('backuply_cron_schedule');
					$cron_settings['backuply_custom_cron'][] = backuply_optpost('cron_minute');
					$cron_settings['backuply_custom_cron'][] = backuply_optpost('cron_hour');
					$cron_settings['backuply_custom_cron'][] = backuply_optpost('cron_day');
					$cron_settings['backuply_custom_cron'][] = backuply_optpost('cron_month');
					$cron_settings['backuply_custom_cron'][] = backuply_optpost('cron_weekday');
				} else {
					$cron_settings['backuply_cron_schedule'] = backuply_optpost('backuply_cron_schedule');
				}

				update_option('backuply_cron_settings', $cron_settings);
				
				if($timestamp = wp_next_scheduled( 'backuply_auto_backup_cron' )) {
					wp_unschedule_event( $timestamp, 'backuply_auto_backup_cron' );
				}

				if(function_exists('backuply_add_auto_backup_schedule')){
					backuply_add_auto_backup_schedule();
				}
			}
			
			$backuply['settings']['backup_dir'] = !empty($_POST['backup_dir']) ? 1 : 0;
			$backuply['settings']['backup_db'] = !empty($_POST['backup_db']) ? 1 : 0;
			$backuply['settings']['backup_location'] = !empty($_POST['backup_location']) ? backuply_optpost('backup_location') : '';
			
			update_option('backuply_settings', $backuply['settings']);
			
			if(!empty($error)){
				return false;
			}
			
			update_option('backuply_debug', backuply_is_checked('debug_mode'));
		}
		
		// Saving db excludes
		if(!empty($_POST['exclude_db_table'])) {
			$backuply['excludes']['db'] = map_deep($_POST['exclude_db_table'], 'sanitize_text_field');
			update_option('backuply_excludes', $backuply['excludes']);
		} else {
			unset($backuply['excludes']['db']);
			update_option('backuply_excludes', $backuply['excludes']);
		}
		

		$success = __('The Backup settings were saved successfully', 'backuply');
		return true;
	}

	$del_loc_id = backuply_optreq('del_loc_id');
	$edit_loc_id = backuply_optreq('edit_loc_id');
	$backuply_remote_backup_locs = get_option('backuply_remote_backup_locs');

	// Handle backup location delete request
	if(isset($_REQUEST['backuply_delete_location']) && array_key_exists($del_loc_id, $backuply_remote_backup_locs)){
		
		// Verify Nonce
		if(!wp_verify_nonce(backuply_optreq('security'), 'backuply_nonce')){
			$error[] = __('Security check Failed!', 'backuply');
			return false;
		}
		
		unset($backuply_remote_backup_locs[$del_loc_id]);
		
		if(empty($backuply_remote_backup_locs)){
			delete_option('backuply_remote_backup_locs');
		}else{
			update_option('backuply_remote_backup_locs', $backuply_remote_backup_locs);
		}

		$success = __('Backup location was deleted successfully', 'backuply');
		return true;
	}

	// Handle edit backup location request
	if(isset($_REQUEST['backuply_edit_location']) && array_key_exists($edit_loc_id, (array)$backuply_remote_backup_locs)){
	
		// Verify Nonce
		if(!wp_verify_nonce(backuply_optreq('security'), 'backuply_nonce')){
			$error[] = __('Security check Failed!', 'backuply');
			return false;
		}

		$backuply_remote_backup_locs = get_option('backuply_remote_backup_locs');
		$edit_loc_id = backuply_optreq('edit_loc_id');
		$edit_loc_name = backuply_optreq('location_name');
		$edit_backup_loc = backuply_optreq('backup_loc');
		$protocol = $backuply_remote_backup_locs[$edit_loc_id]['protocol'];

		if(empty($protocol)){
			$protocol = 'ftp';
		}

		// Let it be any protocol, we welcome all. But class is must. - Jelastic
		if(!backuply_include_lib($protocol)) {
			$error[] = __('Invalid Protocol', 'backuply');
		}
		
		$proto_arr = array('dropbox', 'gdrive', 'aws', 'caws', 'bcloud', 'onedrive');
		
		if(in_array($protocol, $proto_arr)){
			
			// The Backup PATH
			$edit_backup_loc = @trim($edit_backup_loc, '/');
			if(!empty($edit_backup_loc)){
				$edit_backup_loc = '/'.$edit_backup_loc;
			}
			
			if($protocol == 'dropbox'){
				
				$dropbox_access_token = $backuply_remote_backup_locs[$edit_loc_id]['dropbox_token'];
				$dropbox_refresh_token = $backuply_remote_backup_locs[$edit_loc_id]['refresh_token'];

				$full_backup_loc = $protocol.'://'.$dropbox_refresh_token.$edit_backup_loc;
				
			}elseif($protocol == 'bcloud'){
				$bcloud = backuply_load_remote_backup('bcloud'); 
				
				
			}elseif($protocol == 'gdrive'){
				$gdrive = backuply_load_remote_backup('gdrive');

				$gdrive_refresh_token = $backuply_remote_backup_locs[$edit_loc_id]['gdrive_refresh_token'];
				$gdrive_old_folder = $backuply_remote_backup_locs[$edit_loc_id]['backup_loc'];
				
				$app_dir_loc = $protocol.'://'.rawurlencode($gdrive_refresh_token).'/'.$gdrive->app_dir;
				$full_backup_loc = $protocol.'://'.rawurlencode($gdrive_refresh_token).'/'.$gdrive->app_dir.$edit_backup_loc;
				$backup_old_loc = $protocol.'://'.rawurlencode($gdrive_refresh_token).'/'.$gdrive->app_dir.$gdrive_old_folder;
				
				// Updating the target Folder
				if(!empty($edit_backup_loc)) {
					backuply_stream_wrapper_register($protocol, $protocol);
					
					if(file_exists($backup_old_loc) && $backup_old_loc != $app_dir_loc){
						if(!@rename($backup_old_loc, $full_backup_loc)) {
							$error[] = __('Unable to rename the folder', 'backuply');
						}
					} else {
						if(!@mkdir($full_backup_loc)){
							$error[] = __('Failed to create a folder', 'backuply');
						}
					}
				}

			}elseif($protocol == 'aws' || $protocol == 'caws'){
				$endpoint = $backuply_remote_backup_locs[$edit_loc_id]['aws_endpoint'];
				$region = $backuply_remote_backup_locs[$edit_loc_id]['aws_region'];
				$bucketName = $backuply_remote_backup_locs[$edit_loc_id]['aws_bucketname'];
				$accessKey = $backuply_remote_backup_locs[$edit_loc_id]['aws_accessKey'];
				$secretKey = !empty($_REQUEST['aws_secretKey']) ? backuply_optreq('aws_secretKey') : $backuply_remote_backup_locs[$edit_loc_id]['aws_secretKey'];
				$defaultdir = '/'. $aws->app_dir;

				backuply_stream_wrapper_register($protocol, $protocol);
				
				$full_backup_loc = $protocol.'://'.rawurlencode($accessKey).':'.rawurlencode($secretKey).'@'.$bucketName.'/'.$endpoint.'/'.$region.$defaultdir.$edit_backup_loc;
				
				if(!@opendir($full_backup_loc)){
					if(!@mkdir($full_backup_loc)){
						$error[] = __('Failed to create bucket', 'backuply');
					}
				}
			}elseif($protocol == 'onedrive'){
				$onedrive = backuply_load_remote_backup('onedrive');
				$onedrive_refresh_token = $backuply_remote_backup_locs[$edit_loc_id]['onedrive_refresh_token'];
				
				$full_backup_loc = $protocol.'://'.rawurlencode($onedrive_refresh_token).'/'.$onedrive->app_dir.$edit_backup_loc;
				$onedrive->create_onedrive_app_dir($onedrive_refresh_token);
			}
		}else{
			// Server Host
			$server_host = $backuply_remote_backup_locs[$edit_loc_id]['server_host'];	
			$server_host = @rtrim($server_host, '/');
			
			$port = (int) $backuply_remote_backup_locs[$edit_loc_id]['port'];
			if(empty($port)){
				$port = ($protocol == 'softsftp' ? 22 : 21);
			}
			
			// The FTP user
			$ftp_user = $backuply_remote_backup_locs[$edit_loc_id]['ftp_user'];
			
			// FTP Pass
			$ftp_pass = !empty($_REQUEST['ftp_pass']) ? backuply_optreq('ftp_pass') : $backuply_remote_backup_locs[$edit_loc_id]['ftp_pass'];
			
			// The Private Key
			// $private_key = str_replace("\r", '', rawGPC(optPOST('private_key')));
			// $private_key = trim($private_key);			
			
			// SFTP using keys
			// if(!empty($private_key)){
				
			// 	// Passphrase
			// 	if(isset($_POST['passphrase']) && !empty($_POST['passphrase'])){
			// 		$passphrase = rawGPC($_POST['passphrase']);
			// 	}
			// }			
			
			if(empty($ftp_pass)){
				$error[] = __('Please provide either a password or private key', 'backuply'); 
			}
			
			// The Backup PATH
			$edit_backup_loc = '/'.@trim($edit_backup_loc, '/');
			
			//'@' in username was causing issue in fopen while creating backup
			$full_backup_loc = $protocol.'://'.rawurlencode(($protocol == 'softsftp' && empty($ftp_pass) ? $edit_loc_id : $ftp_user)).':'.rawurlencode($ftp_pass).'@'.$server_host.(!empty($port) ? ':'.$port : '').$edit_backup_loc;
			
			
			// Test the connection
			if($protocol == 'webdav'){
				backuply_stream_wrapper_register($protocol, $protocol);
				
				if(!empty($edit_backup_loc)){
					mkdir($full_backup_loc);
				}
				
				$webdav_connect = stat($full_backup_loc);
				
				if(empty($webdav_connect['mtime']) && empty($webdav_connect['ctime'])){
					$error[] = 'Failed to connect to the WebDAV server';
				}
			}
		}

		//Check for Duplicate Backup Location
		$existing_backup_locs = get_option('backuply_remote_backup_locs');

		foreach($existing_backup_locs as $k => $v){
			if($edit_loc_id != $k && $full_backup_loc == $v['full_backup_loc']){
				$error[] = __('Backup Location with the same path already exists', 'backuply');
				break;
			}
		}	

		if($protocol != 'dropbox' && $protocol != 'gdrive' && $protocol != 'webdav' && $protocol != 'aws' && $protocol != 'caws' && $protocol != 'onedrive'){
		
			//Connection established or not?
			$ftp = backuply_sftp_connect($server_host, $ftp_user, $ftp_pass, $protocol, $port, false);

			if(!is_object($ftp) && $ftp == '-2'){
				$error[] = __('Could not login with specified details', 'backuply');
			}
			
			if(!is_object($ftp) && $ftp == '-1'){
				$error[] = __('Could not Resolve Domain Name', 'backuply');
			}
			
			if(!is_object($ftp) && $ftp == '-3'){
				$error[] = sprintf( esc_html__( 'Specified %1$s Path does not exist', 'backuply'), $protocols[$protocol]);
			}
			
			if($ftp == '-4'){
				$error[] = __('Could not connect to the server', 'backuply');
			}
			
			if($ftp == '-5'){
				$error[] = __('Could not connect to the server', 'backuply');
			}
			//backuply_log('isobject ftp : '.var_export($ftp, 1));
			
			if(!is_object($ftp)){
				$error[] = __('Could not connect to the server', 'backuply');
			}
			
			if(!empty($error)){
				return false;
			}

			if($protocol == 'softsftp'){
				
				$ftp->chdir('~');
				$pwd = $ftp->pwd();
				
				// Some servers pwd is empty and some / in that case we cannot check if it is absolute dir
				if(!empty($pwd) && $pwd != '/'){
					$homedir = $pwd.'/';
					
					if(!preg_match('/^'.preg_quote($homedir, '/').'/is', $edit_backup_loc)){
						$error[] = __('The backup path is not an absolute path. Please provide an absolute path', 'backuply');
					}
				}
			}

			//backuply_log('ftp connection is successful');

			// Create backup locaion dir for ftp/sftp
			backuply_stream_wrapper_register($protocol, $protocol);
			if(!empty($edit_backup_loc)){
				mkdir($full_backup_loc);
			}
		}

		$backuply_remote_backup_locs[$edit_loc_id]['name'] = $edit_loc_name;
		$backuply_remote_backup_locs[$edit_loc_id]['backup_loc'] = $edit_backup_loc;
		$backuply_remote_backup_locs[$edit_loc_id]['full_backup_loc'] = $full_backup_loc;
	
		if('aws' == $protocol){
			$backuply_remote_backup_locs[$edit_loc_id]['aws_sse'] = isset($_POST['aws_sse']) ? true : false;
		}

		if(!empty($error)){
			return false;
		}
		
		update_option('backuply_remote_backup_locs', $backuply_remote_backup_locs);
		
		if(!empty($_POST['backup_sync_infos'])){
			backuply_sync_remote_backup_infos($edit_loc_id);
		}
		
		$saved = true;
		
		if(!empty($saved)){
			$success = __('Backup location details saved successfully', 'backuply');
			return true;
		}
	}
	
	// Handles Adding New Backup Location
	if(isset($_REQUEST['addbackuploc'])){
		
		// Verify Nonce
		if(!wp_verify_nonce(backuply_optreq('security'), 'backuply_nonce')){
			$error[] = __('Security check Failed!', 'backuply');
			return false;
		}
		
		$protocol = backuply_optreq('protocol');
		if(empty($protocol)){
			$protocol = 'ftp';
		}
		
		// We dont ask the user location_name so we are fixing it here when Backuply Cloud is selected.
		if($protocol == 'bcloud'){
			$_REQUEST['location_name'] = 'Backuply Cloud';
		}
		
		$loc_name = backuply_REQUEST('location_name', __('No backup location name was specified', 'backuply'));

		$existing_backup_locs = get_option('backuply_remote_backup_locs');
		$remote_backup_locs = (!empty($existing_backup_locs) ? $existing_backup_locs: array());
		$location_id = (empty($remote_backup_locs) ? 1 : max(array_keys($remote_backup_locs)) + 1);

		$dropbox = backuply_load_remote_backup('dropbox');
		$proto_arr = ['dropbox', 'gdrive', 'aws', 'caws', 'onedrive', 'bcloud'];
		
		if(in_array($protocol, $proto_arr)){

			$backup_loc = backuply_optreq('backup_loc');
			$backup_loc = @trim($backup_loc, '/');
			if(!empty($backup_loc)){
				$backup_loc = '/'.$backup_loc;
			}

			if($protocol == 'onedrive'){
				
				$onedrive = backuply_load_remote_backup('onedrive');
				$callback_uri = menu_page_url('backuply', false) . '&security='.wp_create_nonce('backuply_nonce');
				
				$onedrive_error = backuply_optget('error');
				$onedrive_auth_code = backuply_optget('onedrive_auth_code');

				if(!empty($onedrive_error)){
					$error[] = __('It seems you cancelled the authorization process', 'backuply');
				}
				
				if(empty($onedrive_auth_code)){

					$auth_url = 'https://api.backuply.com/onedrive/token.php?action=add_location&loc_name='.rawurlencode($loc_name).'&backup_loc='.rawurlencode($backup_loc).'&url='.rawurlencode($callback_uri).'&softtoken='.rawurlencode(backuply_csrf_get_token()).'&scope='.rawurlencode($onedrive->scopes);
					
					backuply_redirect(filter_var($auth_url, FILTER_SANITIZE_URL), false);
					exit;
					
				}else{
					
					$gen_token_resp = $onedrive->generate_onedrive_token($onedrive_auth_code);
					
					$onedrive_access_token = $gen_token_resp['access_token'];
					$onedrive_refresh_token = $gen_token_resp['refresh_token'];
					
					if(empty($onedrive_access_token)){
						$error[] = __('Failed to generate OneDrive Access Token', 'backuply');
					}
				}
				
				$full_backup_loc = $protocol.'://'.rawurlencode($onedrive_refresh_token).'/'.$onedrive->app_dir.$backup_loc;
				$onedrive->create_onedrive_app_dir($onedrive_refresh_token);
			}elseif($protocol == 'bcloud'){
				$bcloud = backuply_load_remote_backup('bcloud');
				backuply_stream_wrapper_register($protocol, $protocol);
	
				$license = get_option('backuply_license');
				$bcloud_key = backuply_optreq('bcloud_key');
	
				if(!empty($bcloud) && !empty($bcloud->bcloud_key)){
					$error[] = __('Backuply Cloud is already added as a Location');
				} else if(empty($license) || empty($license['license']) || empty($license['active'])){
					$error[] = __('Your License is not linked yet', 'backuply');
				} else {
					$full_backup_loc = $protocol . '://'. rawurlencode(site_url()).'/'. $license['license'] . '?bcloud_key='.$bcloud_key;

					if(!$oepn_dir = @opendir($full_backup_loc)){
						$error[] = __('Failed to create a folder', 'backuply');
					}
				}

			}elseif($protocol == 'gdrive'){
				
				$gdrive = backuply_load_remote_backup('gdrive');
				$callback_uri = menu_page_url('backuply', false) . '&security='.wp_create_nonce('backuply_nonce');
				
				$gdrive_error = backuply_optget('error');
				$gdrive_auth_code = backuply_optget('gdrive_auth_code');
				
				if(!empty($gdrive_error)){
					$error[] = __('It seems you cancelled the authorization process', 'backuply');
				}
				
				if(empty($gdrive_auth_code)){

					$auth_url = 'https://api.backuply.com/gdrive/token.php?action=add_location&loc_name='.rawurlencode($loc_name).'&backup_loc='.rawurlencode($backup_loc).'&url='.rawurlencode($callback_uri).'&softtoken='.rawurlencode(backuply_csrf_get_token());
					
					backuply_redirect(filter_var($auth_url, FILTER_SANITIZE_URL), false, true);
					exit;
					
				}else{
					$gen_token_resp = $gdrive->generate_gdrive_token($gdrive_auth_code);
					
					$gdrive_access_token = $gen_token_resp['access_token'];
					$gdrive_refresh_token = $gen_token_resp['refresh_token'];
					
					if(empty($gdrive_access_token)){
						$error[] = __('Failed to generate Google Drive Access Token', 'backuply');
					}
				}

				$full_backup_loc = $protocol.'://'.rawurlencode($gdrive_refresh_token).'/'.$gdrive->app_dir.$backup_loc;
				$gdrive->create_gdrive_app_dir($gdrive_refresh_token);
				
				backuply_stream_wrapper_register($protocol, $protocol);
				
				if(!@opendir($full_backup_loc)){
					if(!@mkdir($full_backup_loc)){
						$error[] = __('Failed to create a folder', 'backuply');
					}
				}

			}elseif($protocol == 'aws' || $protocol == 'caws'){
				
				$aws = backuply_load_remote_backup($protocol);
				
				$endpoint = backuply_POST('aws_endpoint', 
					__('Please provide the AWS S3 endpoint', 'backuply'));
				$region = backuply_POST('aws_region', 
					__('Please provide the AWS S3 region', 'backuply'));
				$bucketName = backuply_POST('aws_bucketname', 
					__('Please provide the AWS S3 bucket name', 'backuply'));
				$accessKey = backuply_POST('aws_accessKey', 
					__('Please provide the AWS S3 access key', 'backuply'));
				$secretKey = backuply_POST('aws_secretKey', 
					__('Please provide the AWS S3 secret key', 'backuply'));
					
				if('aws' == $protocol){
					$aws_sse = isset($_POST['aws_sse']) ? true : false;
				}
				
				if('caws' == $protocol){
					$s3_compat = backuply_optpost('s3_compatible');
				}

				$defaultdir = '/'. $aws->app_dir;
				backuply_stream_wrapper_register($protocol, $protocol);
				
				$full_backup_loc = $protocol.'://'.rawurlencode($accessKey).':'.rawurlencode($secretKey).'@'.$bucketName.'/'.$endpoint.'/'.$region.$defaultdir.$backup_loc;
			
				if(!@opendir($full_backup_loc)){
					
					if(!@mkdir($full_backup_loc)){
						$error[] = __('Failed to create bucket', 'backuply');
					}
				}

			}elseif($protocol == 'dropbox'){
				
				$access_code = backuply_optget('access_code');
				
				if(empty($access_code)){
					$callback_uri = menu_page_url('backuply', false) . '&security='.wp_create_nonce('backuply_nonce');
					
					$url = 'https://api.backuply.com/dropbox/token.php?action=add_location&loc_name='.rawurlencode($loc_name).'&backup_loc='.rawurlencode($backup_loc).'&url='.rawurlencode($callback_uri).'&softtoken='.rawurlencode(backuply_csrf_get_token());
					
					backuply_redirect($url, false);
					exit;
					
				}else {
					$dropbox_tokens = $dropbox->generate_dropbox_token($access_code);
					$dropbox_access_token = $dropbox_tokens['access_token'];
					$dropbox_refresh_token = $dropbox_tokens['refresh_token'];
					
					if(empty($dropbox_access_token) && !empty($access_code)){
						$error[] = __('Failed to generate Dropbox Access Token', 'backuply');
					}
					
					$full_backup_loc = $protocol.'://'.$dropbox_refresh_token.$backup_loc;
				}

			}
		}else{
			// Server Host
			$server_host = backuply_optreq('server_host');
			$server_host = @rtrim($server_host, '/');
			
			// The Port
			$port = (int) backuply_optreq('port');
			if(empty($port)){
				$port = ($protocol == 'softsftp' ? 22 : 21);
			}
			
			// The FTP user
			$ftp_user = backuply_POST('ftp_user', __('No FTP user was specified', 'backuply'));
			
			// FTP Pass
			if(backuply_optpost('ftp_pass') && !empty(backuply_optpost('ftp_pass'))){
				$ftp_pass = backuply_optpost('ftp_pass');
			}

			// The Backup PATH
			$backup_loc = backuply_POST('backup_loc', __('No Backup Location was specified', 'backuply'));
			
			$backup_loc = '/'.@trim($backup_loc, '/');

			//'@' in username was causing issue in fopen while creating backup
			$full_backup_loc = $protocol.'://'.rawurlencode(($protocol == 'softsftp' && empty($ftp_pass) ? $location_id : $ftp_user)).':'.rawurlencode($ftp_pass).'@'.$server_host.(!empty($port) ? ':'.$port : '').$backup_loc;

			// Test the connection
			if($protocol == 'webdav'){
				backuply_stream_wrapper_register($protocol, $protocol);
				
				if(!empty($backup_loc)){
					mkdir($full_backup_loc);
				}
				
				$webdav_connect = stat($full_backup_loc);
				
				if(empty($webdav_connect['mtime']) && empty($webdav_connect['ctime'])){
					$error[] = __('Failed to connect to the WebDAV server', 'backuply');
				}
			}
		}

		//Check for Duplicate Backup Location
		if(!empty($existing_backup_locs)){
			// We do not want to go in the loop if there are no existing backup locations
			foreach($existing_backup_locs as $k => $v){
				if($full_backup_loc == $v['full_backup_loc']){
					$error[] = __('Backup Location with the same path already exists', 'backuply');
					break;
				}
			}
		}

		if($protocol != 'dropbox' && $protocol != 'gdrive' && $protocol != 'webdav' && $protocol != 'aws' && $protocol != 'caws' && $protocol != 'onedrive' && $protocol != 'bcloud'){
			
			//Connection established or not?
			$ftp = backuply_sftp_connect($server_host, $ftp_user, $ftp_pass, $protocol, $port, false);

			if(!is_object($ftp) && $ftp == '-2'){
				$error[] = __('Could not login with specified details', 'backuply');
			}
			
			if(!is_object($ftp) && $ftp == '-1'){
				$error[] = __('Could not Resolve Domain Name', 'backuply');
			}
			
			if(!is_object($ftp) && $ftp == '-3'){
				$error[] = sprintf(esc_html__('Specified %1$s Path does not exist', 'backuply'), $protocols[$protocol]);
			}
			
			if($ftp == '-4'){
				$error[] = __('Could not connect to the server', 'backuply');
			}
			
			if($ftp == '-5'){
				$error[] = __('Could not connect to the server', 'backuply');
			}
			//backuply_log('isobject ftp : '.var_export($ftp, 1));
			
			if(!is_object($ftp)){
				$error[] = __('Could not connect to the server', 'backuply');
			}
			
			if(!empty($error)){
				return false;
			}

			if($protocol == 'softsftp'){
				$ftp->chdir('~');
				$pwd = $ftp->pwd();
				
				// Some servers pwd is empty and some / in that case we cannot check if it is absolute dir
				if(!empty($pwd) && $pwd != '/'){
					$homedir = $pwd.'/';
					if(!preg_match('/^'.preg_quote($homedir, '/').'/is', backuply_cleanpath($backup_loc).'/')){
						$error[] = sprintf(esc_html__('The backup path is not an absolute path (%1$s). Please provide an absolute path within your home dir (%2$s)', 'backuply'), $backup_loc, $homedir);
					}
				}
			}

			//backuply_log('ftp connection is successful');

			// Create backup locaion dir for ftp/sftp
			backuply_stream_wrapper_register($protocol, $protocol);
			if(!empty($backup_loc)){
				mkdir($full_backup_loc);
			}
		}

		$remote_backup_locs[$location_id]['id'] = $location_id;
		$remote_backup_locs[$location_id]['name'] = $loc_name;
		$remote_backup_locs[$location_id]['protocol'] = $protocol;
		$remote_backup_locs[$location_id]['backup_loc'] = $backup_loc;
		$remote_backup_locs[$location_id]['full_backup_loc'] = !empty($full_backup_loc) ? $full_backup_loc : '';

		if($protocol == 'dropbox'){
			$remote_backup_locs[$location_id]['dropbox_token'] = $dropbox_access_token;
			$remote_backup_locs[$location_id]['refresh_token'] = $dropbox_refresh_token;
		}elseif($protocol == 'gdrive'){
			$remote_backup_locs[$location_id]['gdrive_token'] = $gdrive_access_token;
			$remote_backup_locs[$location_id]['gdrive_refresh_token'] = $gdrive_refresh_token;

		}elseif($protocol == 'onedrive'){
			$remote_backup_locs[$location_id]['onedrive_token'] = $onedrive_access_token;
			$remote_backup_locs[$location_id]['onedrive_refresh_token'] = $onedrive_refresh_token;

		}elseif($protocol == 'aws' || $protocol == 'caws'){
			$remote_backup_locs[$location_id]['aws_accessKey'] = $accessKey;
			$remote_backup_locs[$location_id]['aws_secretKey'] = $secretKey;
			$remote_backup_locs[$location_id]['aws_endpoint'] = $endpoint;
			$remote_backup_locs[$location_id]['aws_region'] = $region;
			$remote_backup_locs[$location_id]['aws_bucketname'] = $bucketName;
			if(!empty($s3_compat)){
				$remote_backup_locs[$location_id]['s3_compatible'] = $s3_compat;
			}
			
			if(!empty($aws_sse)){
				$remote_backup_locs[$location_id]['aws_sse'] = $aws_sse;
			}

		}elseif($protocol == 'bcloud'){
			$remote_backup_locs[$location_id]['bcloud_key'] = !empty($bcloud_keys['bcloud_key']) ? sanitize_key($bcloud_keys['bcloud_key']) : '';
			$remote_backup_locs[$location_id]['full_backup_loc'] = !empty($bcloud_keys) ? 'bcloud://'.$bcloud_keys['access_key'].':'.$bcloud_keys['secret_key'].'@'.$license['license'].'/'.$bcloud_keys['region'].'/us-east-1/'.$bcloud_keys['bcloud_key'] : '';
			
			if(!empty($bcloud_keys['bcloud_key'])){
				update_option('bcloud_key', $bcloud_keys['bcloud_key']);
			}
			
		}else{
			$remote_backup_locs[$location_id]['server_host'] = $server_host;
			$remote_backup_locs[$location_id]['port'] = $port;
			$remote_backup_locs[$location_id]['ftp_user'] = $ftp_user;
			$remote_backup_locs[$location_id]['ftp_pass'] = $ftp_pass;
		}

		if(!empty($error)){
			return false;
		}
		
		update_option('backuply_remote_backup_locs', $remote_backup_locs);
		
		if(!empty($_POST['backup_sync_infos'])){
			backuply_sync_remote_backup_infos($location_id);
		}

		$saved = true;
		$success = __('Backup Location added successfully', 'backuply');
		return true;
		
	}
	
}
	
function backuply_page_theme(){
	
global $backuply, $error, $success, $protocols, $wpdb;
	
	//Load the theme's header
	backuply_page_header('Backup');
	
	$backups_dir = backuply_glob('backups');
	$backups_info_dir = backuply_glob('backups_info');
	
	if(empty($backups_dir) || empty($backups_info_dir)){
		
		$backup_folder_suffix = wp_generate_password(6, false);
		
		$error['folder_issue'] = __('Backuply was unable to create a directory where it creates backups, please create the following folders in wp-content/backuply directory', 'backuply') . '<code>backups_info-' . $backup_folder_suffix .'</code> and <code>backups-' . $backup_folder_suffix . '</code> <a href="https://backuply.com/docs/common-issues/backup-unable-to-open-in-write-mode/" target="_blank">Read More for detailed guide</a>';
	}

	if(!empty($error)){
		backuply_report_error($error);
	}
	
	if(!empty($success)){
		backuply_report_success($success);
	}
	
	//Load Backuply's cron task, if any.
	$cron_task = get_option('backuply_cron_settings');

	//Get the main part of the backup file location
	$backup_file_loc = preg_replace("/wp-admin\/admin.php/", '', wp_kses_post($_SERVER['PHP_SELF']));
	$backup_file_loc = preg_replace("/\/$/", "", $backup_file_loc);
	
	//Get the home path and remove the "/" at the end.
	$dir_path = backuply_cleanpath(get_home_path());
	
	// Cron schedules
	$cron_schedules = backuply_add_cron_interval([]);
	$backuply_nonce = wp_create_nonce('backuply_nonce');
?>

<div id="process_message" <?php echo !empty(get_option('backuply_status')) ? '' : 'style="display:none"';?>>
	<div id="message" class="updated">
		<div style="display:flex; justify-content: space-between; align-items:center;  user-select: none;">
			<p><?php esc_html_e('Backup is under process...', 'backuply'); ?></p>
			<div class="backuply-check-status" style="display:flex; flex-direction:row; cursor:pointer;">
				<div class="backuply-ring-wrap">
					<div class="backuply-ring-outer"></div>
					<div class="backuply-ring-circle"></div>
				</div>
				<span style="font-weight:bold;">Check Status</span>
			</div>
		</div>
	</div>
	<br />
</div>

<?php
$is_restoring = false;

if(file_exists(BACKUPLY_BACKUP_DIR . 'restoration/restoration.php')){
	$is_restoring = true;
}

?>

<div id="restore_message"  <?php echo !empty($is_restoring) ? '' : 'style="display:none"';?>>
	<div id="message" class="updated">
		<div style="display:flex; justify-content: space-between; align-items:center;">
			<p><?php esc_html_e('Restoration is under process...', 'backuply'); ?></p>
			<div class="backuply-check-status" style="display:flex; flex-direction:row; cursor:pointer; user-select: none;">
				<div class="backuply-ring-wrap">
					<div class="backuply-ring-outer"></div>
					<div class="backuply-ring-circle"></div>
				</div>
				<span style="font-weight:bold;">Check Status</span>
			</div>
		</div>
	</div><br />
</div>

<div id="backuply_htaccess_error" style="display: <?php echo (isset($backuply['htaccess_error']) && $backuply['htaccess_error'] ? '' : 'none');?>">
	<div id="message" class="notice notice-error is-dismissible">
		<p><?php esc_html_e('Your Backup folder is not secure please fix it', 'backuply'); ?> <a href="#" id="backuply-htaccess-fix"/>Read More</a></p>
	</div>
</div>

<div id="backuply_index_html_error" style="display: <?php echo (isset($backuply['index_html_error']) && $backuply['index_html_error'] ? '' : 'none');?>">
	<div id="message" class="notice notice-error is-dismissible">
		<p><?php esc_html_e('Your Backup folder is not secure, index.html file is missing', 'backuply'); ?> <a href="#" id="backuply-index-html-fix"/>Read More</a></p>
	</div>
</div>

<!-- Create Backup -->
<div class="tabs-wrapper">
	<h2 class="nav-tab-wrapper backuply-tab-wrapper">
		<a href="#backuply-dashboard" class="nav-tab "><?php esc_html_e('Dashboard', 'backuply');?></a>
		<a href="#backuply-settings" class="nav-tab"><?php esc_html_e('Settings', 'backuply');?></a>
		<a href="#backuply-location" class="nav-tab"><?php esc_html_e('Backup Locations', 'backuply');?></a>
		<a href="#backuply-history" class="nav-tab"><?php esc_html_e('Backup History', 'backuply');?></a>
		<a href="#backuply-support" class="nav-tab "><?php esc_html_e('Support', 'backuply');?></a>
		<?php echo !defined('BACKUPLY_PRO') ? '<a href="#backuply-pro" class="nav-tab ">'. esc_html__('Pro Version', 'backuply'). '</a>' : ''; ?>
	</h2>
	
	<?php
		$backuply_active = backuply_active();

		$backup_last_log = (file_exists(BACKUPLY_BACKUP_DIR . 'backuply_backup_log.php')) ? ' <span class="backuply-backup-last-log">(Last log)</span>' : '';
		$restore_last_log = (file_exists(BACKUPLY_BACKUP_DIR . 'backuply_restore_log.php')) ? ' <span class="backuply-restore-last-log">(Last log)</span>' : '';
		
		$last_backup = get_option('backuply_last_backup') ? backuply_format_unix_time(get_option('backuply_last_backup')) . $backup_last_log : 'None';
		$last_restore = get_option('backuply_last_restore') ? backuply_format_unix_time(get_option('backuply_last_restore')) . $restore_last_log : 'None';
		$auto_backup_schedule = wp_next_scheduled('backuply_auto_backup_cron') ? date('Y-m-d h:i A', wp_next_scheduled('backuply_auto_backup_cron')) : '';
		
		if(!empty($auto_backup_schedule)) {
			$now = new DateTime();
			$date = new DateTime($auto_backup_schedule);
			$diff = $date->diff($now);
			
			if($diff->d == 0 && $diff->h == 0 && $diff->i == 0)
				$auto_backup_schedule = 'Now';
			elseif($diff->d == 0 && $diff->h == 0)
				$auto_backup_schedule = $diff->format("%i minutes");
			elseif($diff->d == 0)
				$auto_backup_schedule = $diff->format("%h hours and %i minutes");
			else
				$auto_backup_schedule = $diff->format("%d days, %h hours and %i minutes");
		}
	?>
	
	<div id="backuply-dashboard" class="postbox backuply-tab" style="display:none;">	
		<div class="postbox-header">
			<h2 class="hndle">
				<span><?php esc_html_e('Dashboard', 'backuply'); ?> <span></span></span>
			</h2>
		</div>
		<div class="inside">
			<div class="backuply-settings-block">
				<div style="display:flex; flex-direction: column; row-gap: 5px; font-size: 1rem !important;">
					<span><strong>Last Backup:</strong> <?php echo wp_kses_post($last_backup); ?></span>
					<span><strong>Last Restore:</strong> <?php echo wp_kses_post($last_restore); ?></span>
					<?php
					if(defined('BACKUPLY_PRO') || !empty($backuply['bcloud_key'])){
						$auto_sche = !empty($auto_backup_schedule) ? $auto_backup_schedule : 'Nothing Scheduled';
						echo '<span><strong>Auto Backup Next Schedule:</strong> '.esc_html($auto_sche).'</span>';
					}
					?>
				</div>
			</div>
		
			<div class="backuply-settings-block">
				<h2 class="backuply-heading-2">
					<?php esc_html_e('Create Backup', 'backuply'); ?>
				</h2>
				
				<div class="backuply-form-wrap">
				<form method="post">
					<div class="backuply-option-wrap">
						<label class="form-check-label">
							<input type="checkbox" class="form-check-input disable" id="backup_dir" name="backup_dir" value="1" <?php echo(!empty($backuply['settings']['backup_dir']) ? ' checked' : '');?>/><?php esc_html_e('Backup Directories', 'backuply'); ?>
						</label>
					</div>
					<div class="backuply-option-wrap">	
						<label class="form-check-label">
							<input type="checkbox" class="form-check-input disable" id="backup_db" name="backup_db" value="1" <?php echo(!empty($backuply['settings']['backup_db']) ? ' checked' : '');?>/><?php esc_html_e('Backup Database', 'backuply'); ?>
						</label>
					</div>
					<br>
					<select name="backup_location" class="disable">
						<option value="">Local Folder (Default)</option>
						<?php
						$backuply_remote_backup_locs = get_option('backuply_remote_backup_locs');
						
						if(!empty($backuply_remote_backup_locs)){
							foreach($backuply_remote_backup_locs as $k => $v){
								if(!defined('BACKUPLY_PRO') && !array_key_exists($v['protocol'], backuply_get_protocols())){
									continue;
								}
								
								$selected = '';
								
								if(!empty($backuply['settings']['backup_location']) && $backuply['settings']['backup_location'] == $k) {
									$selected = 'selected';
								}
								
								echo '<option value="'.esc_attr($k).'" '.esc_attr($selected).' data-protocol="'.esc_attr($v['protocol']).'">'.esc_attr($v['name']).' ('.esc_attr($protocols[$v['protocol']]).')</option>';
								
							}
						}
						?>
					</select>
					<details style="margin:6px 0 15px 0;">
						<summary style="color:rgba(56,120,255); cursor:pointer; user-select: none;">Additional Options</summary>
						<label style="font-weight:500;" for="backup-note"><?php _e('Backup Note(Optional)', 'backuply');?></label><br/>
						<textarea name="backup_note" id="backup-note" maxlength="80" cols="40" placeholder="<?php _e('You can write maximum of 80 characters', 'backuply'); ?>"></textarea>
					</details>
					<input name="backuply_create_backup" class="button action" style="background: #5cb85c; color:white; border:#5cb85c" value="<?php esc_html_e('Create Backup', 'backuply'); ?>" type="submit" <?php echo ($backuply_active ? 'disabled' : '');?>/>
					<input name="backuply_stop_backup" class="button-secondary" value="<?php esc_html_e('Stop Backup', 'backuply');?>" type="submit" <?php echo ($backuply_active ? '' : 'disabled'); ?>>
				</form>
				
				</div>
			</div>
		</div>
	</div>

	<!-- Backup Locations -->
	<div id="backuply-location" class="postbox backuply-tab" style="display:none;">
		<div class="postbox-header">
			<h2 class="hndle">
				<span><?php esc_html_e('Backup Locations', 'backuply'); ?></span>
			</h2>
		</div>
		<div class="inside">
			<div class="row mt-4 backuply-settings-block">
				<div class="col-12 px-4">

					<table border="0" cellpadding="5" cellspacing="0" width="100%" class="table table-hover mb-2 borderless backup-table">
						<thead class="sai_head2" style="text-align:left;">
							<!--<th style="text-align:center;"><?php //esc_html_e('Default', 'backuply'); ?></th>-->
							<th style="min-width:20%;"><?php esc_html_e('Name', 'backuply'); ?></th>
							<th><?php esc_html_e('Protocol', 'backuply'); ?></th>
							<th><?php esc_html_e('Host', 'backuply'); ?></th>
							<th><?php esc_html_e('Backup Location', 'backuply'); ?></th>
							<th><?php esc_html_e('Quota', 'backuply'); ?></th>
							<th><?php esc_html_e('Edit', 'backuply'); ?></th>
							<th><?php esc_html_e('Delete', 'backuply'); ?></th>
						</thead>
					
						<tr style="text-align:left;">
							<!--<td style="text-align:center;"><input type="radio" name="default_backup_location" value="0" /></td>-->
							<td><?php esc_html_e('Local Folder', 'backuply'); ?></td>
							<td>-</td>
							<td>-</td>
							<td><?php echo esc_html(backuply_cleanpath(BACKUPLY_BACKUP_DIR.'backups')); ?></td>
							<td>-</td>
							<td>-</td>
							<td>-</td>
						</tr>
						
				<?php
				
				// Load Backuply's remote backup locations.
				$backuply_remote_backup_locs = get_option('backuply_remote_backup_locs');

				if(!empty($backuply_remote_backup_locs) ){

					foreach($backuply_remote_backup_locs as $k => $v){ 
					
						if(!defined('BACKUPLY_PRO') && !array_key_exists($v['protocol'], backuply_get_protocols())){
							continue;
						}
					
					?>
						<tr style="text-align:left;">
							<form method="post">
								<!--<td style="text-align:center;"><input type="radio" name="default_backup_location" /></td>-->
								<td><?php echo esc_html($v['name']); ?></td>
								<td>
								<?php
									$logo = esc_attr($v['protocol']).'.svg';

									if(!empty($v['s3_compatible'])){
										$logo = esc_attr($v['s3_compatible']).'.svg';
									}

								?>
									<div style="display:flex; align-items:center;">
										<img src="<?php echo BACKUPLY_URL . '/assets/images/'.$logo; ?>" height="26" width="26"/> &nbsp;<?php echo esc_html($protocols[$v['protocol']]); ?>
									</div>
								</td>
								<td><?php echo isset($v['server_host']) ? esc_html($v['server_host']) : '-' ?></td>
								<td><?php echo !empty($v['backup_loc']) ? esc_html($v['backup_loc']) : '-' ?></td>
								<td><?php
									$class_to_add = '';
									if(in_array($v['protocol'], ['onedrive', 'dropbox', 'gdrive', 'bcloud'])){
										$class_to_add = 'backuply-update-quota';
									}

									echo '<div class="backuply-quota '.esc_attr($class_to_add).'" data-storage="'.esc_attr($v['protocol']).'" title="Click to refresh">';

									if(!empty($v['backup_quota']) || in_array($v['protocol'], ['onedrive', 'dropbox', 'gdrive', 'bcloud'])){
										echo '<span class="backuply-quota-text">' . (!empty($v['backup_quota']) ? esc_html(size_format($v['backup_quota'])) : '-') . '</span>';

										if(!empty($v['allocated_storage'])){
											echo '/'. size_format(esc_html($v['allocated_storage']));
										}

									} else {
										echo '-';
									}
								
									echo '</div></td>
									<td>';

								if($v['protocol'] === 'bcloud'){
									echo '-';
								} else {
									echo '<input type="hidden" name="edit_loc_id" value="'.esc_attr($k).'"/>
									<input type="hidden" name="edit_protocol" value="'.esc_attr($v['protocol']).'"/>
									<input type="hidden" name="security" value="'.esc_attr($backuply_nonce).'"/>
									<input name="backuply_edit_loc" class="button button-primary action" value="'.esc_html__('Edit Location', 'backuply').'"  type="submit" />';
								}?>
								</td>
							</form>
							<td>
								<form method="post">
									<input type="hidden" name="security" value="<?php echo esc_attr($backuply_nonce); ?>"/>
									<input type="hidden" name="del_loc_id" value="<?php echo esc_attr($k); ?>"/>
									<input name="backuply_delete_location" class="button button-primary action" onclick="return conf_del('Are you sure you want to delete this backup location ?');" value="<?php esc_html_e('Delete', 'backuply'); ?>"  type="submit" />
								</form>
							</td>
						</tr> <?php
					}
				}
				?>
					</table><br/>
						<button type="button" class="button button-primary" id="add_backup_loc_btn"><?php esc_html_e('Add Backup Location', 'backuply'); ?></button>
					<?php if(!defined('BACKUPLY_PRO')) { ?>
						<div class="backuply-settings-block backuply-premium-box">
							<h2 align="center"><a href="<?php echo BACKUPLY_PRO_URL; ?>" target="_blank">Upgrade to Backuply Pro</a></h2>
							<p align="center">To unlock more backup locations</p>
							<br/>
							<ul class="backuply-loc-types-showcase">
								<li><img class="backuply-grayscale" src="<?php echo BACKUPLY_URL . '/assets/images/softftpes.svg'?>" height="64" width="64" alt="FTPS Logo" title="FTPS"/></li>
								<li><img class="backuply-grayscale" src="<?php echo BACKUPLY_URL . '/assets/images/softsftp.svg'?>" height="64" width="64" alt="SFTP Logo" title="SFTP"/></li>
								<li><img class="backuply-grayscale" src="<?php echo BACKUPLY_URL . '/assets/images/dropbox.svg'?>" height="64" width="64" alt="Dropbox Logo" title="Dropbox"/></li>
								<li><img class="backuply-grayscale" src="<?php echo BACKUPLY_URL . '/assets/images/onedrive.svg'?>" height="64" width="64" alt="OneDrive Logo" title="OneDrive"/></li>
								<li><img class="backuply-grayscale" src="<?php echo BACKUPLY_URL . '/assets/images/aws.svg'?>" height="64" width="64" alt="Amazon S3 Logo" title="Amazon S3"/></li>
								<li><img class="backuply-grayscale" src="<?php echo BACKUPLY_URL . '/assets/images/webdav.svg'?>" height="64" width="64" alt="WebDav Logo" title="WebDav"/></li>
								<li><img class="backuply-grayscale" src="<?php echo BACKUPLY_URL . '/assets/images/digitalocean.svg'?>" height="64" width="64" alt="DigitalOcean Logo" title="DigitalOcean Spaces"/></li><li><img class="backuply-grayscale" src="<?php echo BACKUPLY_URL . '/assets/images/cloudflare.svg'?>" height="64" width="64" alt="Cloudflare Logo" title="Cloudflare R2"/></li>
								<li><img class="backuply-grayscale" src="<?php echo BACKUPLY_URL . '/assets/images/vultr.svg'?>" height="64" width="64" alt="Vultr Logo" title="Vultr Object Storage"/></li>
								<li><img class="backuply-grayscale" src="<?php echo BACKUPLY_URL . '/assets/images/linode.svg'?>" height="64" width="64" alt="Linode Logo" title="Linode Object Storage"/></li>
							</ul>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>

	<!-- Add Backup Location Modal -->
	<div class="postbox" id="add_backup_loc_form" title="Add Backup Location" style="display:none;">
		
		<div class="inside">
			<form method="post">	
				<?php
				backuply_report_error($error);
				
				$loc_name = $protocol = $aws_endpoint = $aws_region = $aws_access_key = $aws_secret_key = $aws_basket_name = $access_code = $server_host = $port = $ftp_user = $ftp_pass = $backup_loc = $bcloud_region = '';
				
				if(!empty($error)) {
					$loc_name = !empty($_POST['location_name']) ? backuply_optpost('location_name') : '';
					$protocol = !empty($_POST['protocol']) ? backuply_optpost('protocol') : '';
					$bcloud_key = !empty($_POST['bcloud_key']) ? backuply_optpost('bcloud_key') : '';
					$s3_compatible = !empty($_POST['s3_compatible']) ? backuply_optpost('s3_compatible') : '';
					$aws_endpoint = !empty($_POST['aws_endpoint']) ? backuply_optpost('aws_endpoint') : '';
					$aws_region = !empty($_POST['aws_region']) ? backuply_optpost('aws_region') : '';
					$aws_access_key = !empty($_POST['aws_accessKey']) ? backuply_optpost('aws_accessKey') : '';
					$aws_secret_key = !empty($_POST['aws_secretKey']) ? backuply_optpost('aws_secretKey') : '';
					$aws_sse = !empty($_POST) ? (isset($_POST['aws_sse']) ? 'checked' : '') : '';
					$aws_basket_name = !empty($_POST['aws_bucketname']) ? backuply_optpost('aws_bucketname') : '';
					$access_code = !empty($_POST['access_code']) ? backuply_optpost('access_code') : '';
					$server_host = !empty($_POST['server_host']) ? backuply_optpost('server_host') : '';
					$port = !empty($_POST['port']) ? backuply_optpost('port') : '';
					$ftp_user = !empty($_POST['ftp_user']) ? backuply_optpost('ftp_user') : '';
					$ftp_pass = !empty($_POST['ftp_pass']) ? backuply_optpost('ftp_pass') : '';
					$backup_loc = !empty($_POST['backup_loc']) ? backuply_optpost('backup_loc') : '';
					$backup_sync_infos = !empty($_POST) ? (isset($_POST['backup_sync_infos']) ? 'checked' : '') : 'checked';
				}
				?>
				
					
				<div class="backuply-option-wrap">
					<label class="backuply-opt-label" for="location_name">
						<span class="backuply-opt-label__title"><?php esc_html_e('Location Name', 'backuply'); ?></span>
						<span class="backuply-opt-label__helper"><?php esc_html_e('Choose a name for backup location for your reference', 'backuply'); ?></span>
					</label>
					<input type="text" size="50" name="location_name" id="location_name" value="<?php echo esc_attr($loc_name); ?>"/>
				</div>
				<div class="backuply-option-wrap">
					<label class="backuply-opt-label" for="protocol">
						<span class="backuply-opt-label__title"><?php esc_html_e('Protocol', 'backuply'); ?></span>
						<span class="backuply-opt-label__helper"><?php esc_html_e('Select the protocol by which Backuply will communicate', 'backuply'); ?></span>
					</label>
				
					<select name="protocol" class="form-control" id="protocol" value="<?php echo  esc_attr($protocol); ?>">
						<?php
						foreach(backuply_get_protocols() as $key => $remote_loc) {
							$selected = (isset($protocol) && $protocol == $key) ? ' selected' : '';
							echo '<option value="'.esc_attr($key).'" '.esc_attr($selected).'>'.esc_attr($remote_loc).'</option>';
						}
						?>
					</select>
				</div>
				<div class="backuply-option-wrap" style="display:none;">
					<label class="backuply-opt-label" for="backuply-cloud-key">
						<span class="backuply-opt-label__title"><?php esc_html_e('Backuply Cloud Key', 'backuply'); ?>
							<input type="text" size="50" name="bcloud_key" id="backuply-cloud-key" value="<?php echo !empty($backuply['bcloud_key']) ? esc_attr($backuply['bcloud_key']) : ''?>"/>
						</span>
						<span class="backuply-opt-label__helper"><?php esc_html_e('Keep this empty if you dont already have a Backuply Cloud Key', 'backuply'); ?></span>
					</label>

				</div>
				
				<?php 
				if(defined('BACKUPLY_PRO')){ ?>
				<div class="backuply-option-wrap aws_s3bucket bakuply-s3-compatible">
					<label class="backuply-opt-label" for="s3_compatible">
						<span class="backuply-opt-label__title"><?php esc_html_e('S3 Compatible Storages', 'backuply'); ?></span>
						<span class="backuply-opt-label__helper"><?php esc_html_e('Select the S3 Compatible Storage if any', 'backuply'); ?></span>
					</label>
				
					<select name="s3_compatible" class="form-control" id="s3_compatible" value="<?php echo esc_attr($protocol); ?>">
						<?php
						$s3_comp = array('digitalocean' => 'DigitalOcean Spaces', 'linode' => 'Linode Object Storage', 'vultr' => 'Vultr Object Storage', 'cloudflare' => 'Cloudflare R2', 'wasabi' => 'Wasabi Object Storage');
						
						foreach($s3_comp as $key => $remote_loc) {
							$selected = (isset($s3_compatible) && $s3_compatible == $key) ? ' selected' : '';
							echo '<option value="'.esc_attr($key).'" '.esc_attr($selected).'>'.esc_attr($remote_loc).'</option>';
						}
						?>
					</select>
				</div>
				<div class="backuply-option-wrap aws_s3bucket">
					<label class="backuply-opt-label" for="aws_endpoint">
						<span class="backuply-opt-label__title"><?php esc_html_e('AWS S3 Endpoint', 'backuply'); ?></span>
						<span class="backuply-opt-label__helper"><?php esc_html_e('Enter your AWS S3 Endpoint e.g. "s3.amazonaws.com"', 'backuply'); ?></span>
					</label>
					<input type="text" size="50" name="aws_endpoint" id="aws_endpoint" value="<?php echo esc_attr($aws_endpoint); ?>"/>
				</div>
				<div class="backuply-option-wrap aws_s3bucket">
					<label class="backuply-opt-label" for="aws_region">
						<span class="backuply-opt-label__title"><?php esc_html_e('AWS S3 Region', 'backuply'); ?></span>
						<span class="backuply-opt-label__helper"><?php esc_html_e('Enter your AWS S3 Region e.g. "us-east-1"', 'backuply'); ?></span>
					</label>
					<input type="text" size="50" name="aws_region" id="aws_region" value="<?php echo esc_attr($aws_region); ?>"/>
				</div>
				<div class="backuply-option-wrap aws_s3bucket">
					<label class="backuply-opt-label" for="aws_accessKey">
						<span class="backuply-opt-label__title"><?php esc_html_e('AWS S3 Access Key', 'backuply'); ?></span>
					</label>
					<input type="text" size="50" name="aws_accessKey" id="aws_accessKey" value="<?php echo esc_attr($aws_access_key); ?>"/>
				</div>
				<div class="backuply-option-wrap aws_s3bucket">
					<label class="backuply-opt-label" for="aws_secretKey">
						<span class="backuply-opt-label__title"><?php esc_html_e('AWS S3 Secret Key', 'backuply'); ?></span>
					</label>
					<input type="password" size="50" name="aws_secretKey" id="aws_secretKey" value="<?php echo esc_attr($aws_secret_key); ?>"/>
				</div>
				<div class="backuply-option-wrap aws_s3bucket">
					<label class="backuply-opt-label" for="aws_bucketname">
						<span class="backuply-opt-label__title"><?php esc_html_e('AWS S3 Bucket Name', 'backuply'); ?></span>
						<span class="backuply-opt-label__helper"><?php esc_html_e('Enter the AWS S3 bucket name where you wish to create Backuply backups. If the bucket is not present, it will be created automatically', 'backuply'); ?></span>
					</label>
					<input type="text" size="50" name="aws_bucketname" id="aws_bucketname" value="<?php echo esc_attr($aws_basket_name); ?>"/>
				</div>
				<div class="backuply-option-wrap aws_s3bucket aws_sse">
					<label class="backuply-opt-label" for="aws_sse">
						<span class="backuply-opt-label__title"><?php esc_html_e('Enable Server Side Encryption', 'backuply'); ?>
							<input type="checkbox" size="50" name="aws_sse" id="aws_sse" <?php echo !empty($aws_sse) ? esc_attr($aws_sse) : ''; ?>/>
						</span>
						<span class="backuply-opt-label__helper"><?php esc_html_e('If checked, AWS will encrypt the data we upload', 'backuply'); ?></span>
						
					</label>
				</div>

				<?php } ?>
				<div class="backuply-option-wrap ftp_details">
					<label class="backuply-opt-label" for="server_host">
						<span class="backuply-opt-label__title"><?php esc_html_e('Server Host (Required)', 'backuply'); ?></span>
						<span class="backuply-opt-label__helper"><?php esc_html_e('Enter the server host e.g. ftp.mydomain.com', 'backuply'); ?></span>
					</label>
					<input type="text" size="50" name="server_host" id="server_host" value="<?php echo esc_attr($server_host); ?>"/>
				</div>
				<div class="backuply-option-wrap ftp_details">
					<label class="backuply-opt-label" for="port">
						<span class="backuply-opt-label__title"><?php esc_html_e('Port', 'backuply'); ?></span>
						<span class="backuply-opt-label__helper"><?php esc_html_e('Enter the port to connect (default FTP port is 21)', 'backuply'); ?></span>
					</label>
					<input type="number" size="50" name="port" id="port" value="<?php echo esc_attr($port); ?>"/>
				</div>
				<div class="backuply-option-wrap ftp_credentials">
					<label class="backuply-opt-label" for="ftp_user">
						<span class="backuply-opt-label__title"><?php esc_html_e('FTP Username', 'backuply'); ?></span>
						<span class="backuply-opt-label__helper"><?php esc_html_e('The Username of your FTP Account', 'backuply'); ?></span>
					</label>
					<input type="text" size="50" name="ftp_user" id="ftp_user" value="<?php echo esc_attr($ftp_user); ?>"/>
				</div>
				<div class="backuply-option-wrap ftp_credentials">
					<label class="backuply-opt-label" for="ftp_pass">
						<span class="backuply-opt-label__title"><?php esc_html_e('FTP Password', 'backuply'); ?></span>
						<span class="backuply-opt-label__helper"><?php esc_html_e('The Password of your FTP account', 'backuply'); ?></span>
					</label>
					<input type="password" size="50" name="ftp_pass" id="ftp_pass" value="<?php echo esc_attr($ftp_pass); ?>"/>
				</div>
				<div class="backuply-option-wrap">
					<label class="backuply-opt-label" for="backup_loc">
						<span class="backuply-opt-label__title"><?php esc_html_e('Backup Location (Optional)', 'backuply'); ?></span>
						<span class="backuply-opt-label__helper"><?php esc_html_e('Backup Directory e.g. /backups or you can leave empty to allow Backuply to manage the', 'backuply'); ?></span>
					</label>
					<input type="text" size="50" name="backup_loc" id="backup_loc" value="<?php echo esc_attr($backup_loc); ?>"/>
				</div>
				<div class="backuply-option-wrap">
					<label class="backuply-opt-label" for="backup_sync_infos">
						<span class="backuply-opt-label__title">
							<?php esc_html_e('Sync Infos', 'backuply'); ?>
							<input type="checkbox" name="backup_sync_infos" id="backup_sync_infos" <?php echo isset($backup_sync_infos) ? esc_attr($backup_sync_infos) : ''; ?> />
						</span>
						<span class="backuply-opt-label__helper"><?php esc_html_e('If checked, Backuply will check for any existing info files on this Backup Location and compare it with the local info files. Any info file not present on the local system, will be synced from the remote location', 'backuply'); ?></span>
					</label>
				</div>
				<input type="hidden" name="security" value="<?php echo esc_attr($backuply_nonce);?>"/>
				<div class="backuply-option-wrap">
					<input type="hidden" name="edit_loc_id" value="" />
					<input type="hidden" name="addbackuploc" value="" />
					<input type="hidden" name="backuply_edit_location" value=""/>
					<button class="button button-primary action" type="submit"><?php esc_html_e('Add Backup Location', 'backuply'); ?></button>
				</div>
			</form>
		</div>
	</div>
	
	<!-- Backup Settings -->
	<div id="backuply-settings" class="postbox backuply-tab" style="display:none;">
		<div class="postbox-header">
			<h2 class="hndle">
				<span><?php esc_html_e('General Settings', 'backuply'); ?></span>
			</h2>
		</div>
		<div class="inside">
			<form method="post">
				<!--Backup Settings-->
				<div class="backuply-settings-block">
					<h2 class="backuply-heading-2">
						<?php esc_html_e('Backups Settings', 'backuply'); ?>
					</h2>
					
					<div class="backuply-form-wrap">
						<div class="backuply-option-wrap">
							<div class="backuply-opt-label">
								<span class="backuply-opt-label__title"><?php esc_html_e('Backup Options', 'backuply'); ?></span>
							</div>
							<div style="margin-left:5px;">
								<div class="form-check">
									<label class="form-check-label">
									<input type="checkbox" class="form-check-input" id="setting_backup_dir" name="backup_dir" <?php echo(!empty($backuply['settings']['backup_dir']) ? ' checked' : '');?>><?php esc_html_e('Backup Directories', 'backuply'); ?>
								  </label>
								</div>

								<div class="form-check">
									<label class="form-check-label">
										<input type="checkbox" class="form-check-input" id="setting_backup_db" name="backup_db" <?php echo(!empty($backuply['settings']['backup_db']) ? ' checked' : '');?>><?php esc_html_e('Backup Database', 'backuply'); ?>
									</label>
								</div>
							</div>
						</div>
						
						<div class="backuply-option-wrap">
							<label class="backuply-opt-label" for="backup_location">
								<span class="backuply-opt-label__title"><?php esc_html_e('Backup Location', 'backuply'); ?></span>
							</label>
							<select name="backup_location">
								<option value="">Local Folder (Default)</option>
								<?php
								if(!empty($backuply_remote_backup_locs)){
									foreach($backuply_remote_backup_locs as $k => $v){ 
										if(!defined('BACKUPLY_PRO') && !array_key_exists($v['protocol'], backuply_get_protocols())){
											continue;
										}
									
										$selected = '';
								
										if(!empty($backuply['settings']['backup_location']) && $backuply['settings']['backup_location'] == $k) {
											$selected = 'selected';
										}
									?>
										<option value="<?php echo esc_attr($k); ?>" <?php echo esc_attr($selected);?>><?php echo esc_html($v['name']); ?></option>
									<?php }
								} ?>
							</select>
						</div>
					</div>
				</div>
				
				<!--Auto Backups-->
				<div class="backuply-settings-block">
					<?php if(!defined('BACKUPLY_PRO') && empty($backuply['bcloud_key'])){
							echo '<span class="backuply-pro-block-badge"><span class="dashicons dashicons-lock"></span> Pro Feature</span><div class="backuply-pro-block-notice"></div>';
						}
						
					?>
					<h2 class="backuply-heading-2">
						<?php esc_html_e('Auto Backups Settings', 'backuply'); ?>
					</h2>
					<div class="backuply-form-wrap">
						<div class="backuply-option-wrap">
							<label class="backuply-opt-label">
								<span class="backuply-opt-label__title"><?php esc_html_e('Auto Backup', 'backuply'); ?></span>
							</label>
							<select name="backuply_cron_schedule" onchange="backuply_cron_backup_style(this.value)">
								<option value=""><?php esc_html_e('Don\'t Backup', 'backuply');?></option>
									<?php foreach($cron_schedules as $schedule_key => $schedule_value){
											$selected = '';
											if(!empty($cron_task) && $cron_task['backuply_cron_schedule'] == $schedule_key){
												$selected = 'selected';
											}
									?>
								<option value="<?php echo esc_attr($schedule_key); ?>" <?php echo esc_attr($selected); ?>><?php echo esc_attr($schedule_value['display']); ?></option>
								<?php } 
								
								$custom = '';
								
								if(!empty($cron_task) && $cron_task['backuply_cron_schedule'] == 'custom') {
									$custom = 'selected';
								}
								?>
								
								<option value="custom" <?php echo esc_attr($custom); ?>>Custom</option>
							</select>
						</div>
						
						<div class="backuply-option-wrap">
							<label class="backuply-opt-label" for="backup_rotation">
								<span class="backuply-opt-label__title"><?php esc_html_e('Backup Rotation', 'backuply'); ?></span>
							</label>
							<select id="backup_rotation" name="backup_rotation" disabled >
								<option value=""><?php esc_html_e('Unlimited', 'backuply'); ?></option>
								<?php for($i = 1; $i <= 10; $i++) {
									
									$selected = '';
									
									if(isset($cron_task['backup_rotation']) && $cron_task['backup_rotation'] == $i) {
										$selected = 'selected';
									}

									?>
								<option value="<?php echo esc_attr($i);?>" <?php echo esc_attr($selected);?>><?php echo esc_attr($i);?></option>
								<?php } ?>
							</select>
						</div>
	
						<div class="backuply-option-wrap" id="backuply_cron_checkbox">
							<div class="backuply-opt-label">
								<span class="backuply-opt-label__title"><?php esc_html_e('Backup options', 'backuply'); ?></span>
							</div>
							<div style="margin-left:5px;">
								<div class="form-check">
									<label class="form-check-label">
										<input type="checkbox" class="form-check-input" value="1" id="setting_auto_backup_dir" name="auto_backup_dir" <?php echo(!empty($cron_task['backup_dir']) ? 'checked': '');?>><?php esc_html_e('Backup Directories', 'backuply'); ?>
									</label>
								</div>
								<div class="form-check">
									<label class="form-check-label">
										<input type="checkbox" class="form-check-input" value="1" id="setting_auto_backup_db" name="auto_backup_db" <?php echo(!empty($cron_task['backup_db']) ? 'checked' : '');?>><?php esc_html_e('Backup Database', 'backuply'); ?> 
									</label>
								</div>
							</div>
						</div>

						<div class="backuply-option-wrap" id="backuply-custom-cron"  style="<?php echo (!empty($cron_task['backuply_cron_schedule']) && $cron_task['backuply_cron_schedule'] == 'custom') ? 'display:none' : ''?>">
							<div class="backuply-opt-label">
								<span class="backuply-opt-label__title"><?php esc_html_e('Auto Backup Cron Command', 'backuply')?></span>
								<span class="backuply-opt-label_helper"><?php esc_html_e('You will need to manually set Custom Cron', 'backuply'); ?> <a href="https://backuply.com/docs/how-to/how-to-setup-custom-auto-backup/" target="_blank" title="<?php esc_html_e('Doc on how to setup Custom Auto Backup', 'backuply');?>"><span class="dashicons dashicons-external"></span></a></span>
								<span class="backuply-opt-label_helper"><?php esc_html_e('Set cron time as per your choice in the Cron Job wizard', 'backuply'); ?></span>
							</div>
							<div class="backuply-code-text-wrap">
								<span class="backuply-code-text">
									wget --delete-after "<?php echo esc_url(site_url()); ?>/?action=backuply_custom_cron&backuply_key=<?php echo esc_html(get_option('backuply_config_keys')['BACKUPLY_KEY']); ?>"</span>
								<span class="backuply-code-copy">Copy</span>
								<span class="backuply-code-copied">Copied</span>
							</div>
						</div>
					</div>
				</div>
				
				<!-- Exclude Block -->
				<div class="backuply-settings-block">
					<h2 class="backuply-heading-2">
						<?php esc_html_e('Exclude Settings', 'backuply'); ?>
					</h2>
					
					<div class="backuply-form-wrap">
						<div class="backuply-option-wrap">
							<label class="backuply-opt-label">
								<span class="backuply-opt-label__title"><span class="dashicons dashicons-database"></span> <?php esc_html_e('Exclude DB Table', 'backuply'); ?></span>
							</label>
							<div id="advoptions_toggle" onclick="toggle_advoptions('exclude_table');" class="dashicons-before dashicons-plus-alt" style="cursor:pointer;">
								<label class="form-check-label"><?php esc_html_e('Select database tables from the following list', 'backuply'); ?>
								</label>
							</div>
							<div id="exclude_table" style="display:none; overflow-y:auto; max-height: 250px;">
								<table class="table table-hover">
									<?php
									$tables = $wpdb->get_results('SHOW TABLES');
									
									if(empty($tables)){
										$tables = array();
									}
									
									foreach($tables as $table){
										foreach($table as $t){
											$is_checked = '';

											if(!empty($backuply['excludes']['db']) && in_array($t, $backuply['excludes']['db'])){
												$is_checked = 'checked';
											}

											echo '<tr>
												<td><input type="checkbox" name="exclude_db_table[]" class="soft_filelist" value="'. esc_attr($t) .'" id="exclude_db_table_'. esc_attr($t). '" ' . esc_attr($is_checked) . '/></td>
												<td><label for="add_to_fileindex_'. esc_attr($t).'" style="cursor:pointer;">'. esc_html($t).'</label></td>
											</tr>';
										}
									} ?>
								</table>
							</div>
						</div>
						
						<div class="backuply-option-wrap">
							<label class="backuply-opt-label" for="backuply_exclude_files">
								<span class="backuply-opt-label__title"><span class="dashicons dashicons-category"></span> <?php esc_html_e('Exclude Files/Folders', 'backuply'); ?></span>
								<span class="backuply-opt-label__helper"><?php esc_html_e('Exclude specific files, or though certain patters', 'backuply'); ?></span>
								<div class="backuply_exclude_file_block" id="backuply-exclude-file-specific">
									<div class="backuply_exclude_file_header"><?php esc_html_e('Exclude Specific Folder/Folder', 'backuply'); ?></div>
									<div class="backuply_exclude_file_list"></div>
								</div>
								<div class="backuply_exclude_file_block" id="backuply-exclude-file-pattern">
									<div class="backuply_exclude_file_header"><?php esc_html_e('Exclude a pattern', 'backuply'); ?></div>
									<div class="backuply_exclude_file_list"></div>
								</div>
							</label>
						</div>
					</div>
				</div>

				<!-- Additional Settings -->
				<div class="backuply-settings-block">
					<h2 class="backuply-heading-2">
						<?php esc_html_e('Additional Settings', 'backuply'); ?>
					</h2>
					<?php $notify_email_address = get_option('backuply_notify_email_address'); ?>
					<div class="backuply-form-wrap">
						<div class="backuply-option-wrap">
							<label class="backuply-opt-label" for="notify_email_address">
								<span class="backuply-opt-label__title"><?php esc_html_e('Email', 'backuply'); ?></span>
								<span class="backuply-opt-label__helper"><?php esc_html_e('The email address to send mails to', 'backuply'); ?></span>
							</label>
								
							<input type="email" size="50" value="<?php echo esc_attr($notify_email_address); ?>" name="notify_email_address" id="notify_email_address"/>
						</div>
							
						<?php
						$add_to_fileindex = backuply_sfilelist($dir_path, 0);
						
						if(!empty($add_to_fileindex)){?>
							<div class="backuply-option-wrap">
								<label class="backuply-opt-label">
									<span class="backuply-opt-label__title"><?php esc_html_e('Additional Files', 'backuply'); ?></span>
								</label>
								<div id="advoptions_toggle" onclick="toggle_advoptions('selectfile');" class="dashicons-before dashicons-plus-alt" style="cursor:pointer;">
									<label class="form-check-label"><?php esc_html_e('Select additional files/folders from the following list', 'backuply'); ?>
									</label>
								</div>
								<div id="selectfile" style="display:none">
									<table class="table table-hover">
										<thead style="text-align: left">
											<tr>
												<th width="10%"><input type="checkbox" id="check_all_edit" name="check_all_edit"></th>
												<th colspan="2"><?php esc_html_e('Check All', 'backuply'); ?></th>
											</tr>
										</thead>

										<?php
										$backuply_core_fileindex = backuply_core_fileindex();
										$backuply_additional_fileindex = get_option('backuply_additional_fileindex');
										
										if(empty($backuply_additional_fileindex)){
											$backuply_additional_fileindex = array();
										}
										
										foreach($add_to_fileindex as $ck => $cv){
											if(in_array($cv['name'], $backuply_core_fileindex)) continue; ?>
											<tr>
												<td><input type="checkbox" name="add_to_fileindex[]" class="soft_filelist" value="<?php echo esc_attr($cv['name']); ?>" id="add_to_fileindex_<?php echo esc_attr($cv['name']);  ?>" <?php echo esc_attr(backuply_POSTmulticheck('add_to_fileindex', $cv['name'], $backuply_additional_fileindex)) ?> /></td>
												<?php
												if(!empty($cv['dir'])){ ?>
													<td width="5%"><div class ="dashicons-before dashicons-open-folder"></div></td>
												<?php }else{ ?>
													<td width="5%"><div class ="dashicons-before dashicons-format-aside"></div></td>
												<?php }
												?>
												<td><label for="add_to_fileindex_"<?php echo esc_attr($cv['name']);?> style="cursor:pointer;"><?php echo esc_html($cv['name']); ?></label></td>
											</tr>
										<?php }
										?>
									</table>
								</div>
							</div>
						<?php } ?>
						<div class="backuply-option-wrap">
							<?php	
							$checked = !empty(get_option('backuply_debug')) ? 'checked' : '';	
							?>
							<label class="backuply-opt-label" for="backuply_debug_mode">
								<span class="backuply-opt-label__title"><?php esc_html_e('Debug Mode', 'backuply'); ?></span>
								<input type="checkbox" name="debug_mode" id="backuply_debug_mode"  <?php echo esc_attr($checked); ?>/>
								<span class="backuply-opt-label__helper"><?php esc_html_e('Enable Debug Mode (Don\'t keep it always enabled)', 'backuply'); ?></span>
							</label>
						</div>
					</div>
				</div>
				
				<div style="margin-top:20px; text-align:center;">
					<input type="hidden" name="security" value="<?php echo esc_attr($backuply_nonce);?>"/>
					<input name="backuply_save_settings" class="button-primary" value="<?php esc_html_e('Save Settings', 'backuply'); ?>" type="submit" />
				</div>
			</form>
		</div>
	</div>
	
	<!-- Backup History -->
	<div id="backuply-history" class="postbox backuply-tab" style="display:none;">
		<div class="postbox-header">
			<h2 class="hndle">
				<div>
				<?php
					echo '<select name="backup_sync_id">
						<option value="loc">Local Folder</option>';


					if(!empty($backuply_remote_backup_locs)){
						foreach($backuply_remote_backup_locs as $k => $v){
							if(!array_key_exists($v['protocol'], backuply_get_protocols())){
								continue;
							}
							
							echo '<option value="'.esc_attr($k).'">'.esc_html($v['name']).' ('.esc_html($protocols[$v['protocol']]).')</option>';
						}
					}

					echo '</select>
					<button class="button button-primary" id="backuply-btn-sync-bak">Sync Backups</button>
					<button class="button button-primary" id="backuply-btn-upload-bak" style="margin-left:10px;" title="Upload Backup"><span class="dashicons dashicons-upload" style="vertical-align:sub;"></span></button>';
					
					?>
					</div>
					
				<span><?php esc_html_e('Backup History', 'backuply'); ?></span>
				<div style="display:flex; gap:10px;">
					<span class="spinner"></span>
					<button class="button button-primary" id="backuply-bak-select-all">Select all</button>
					<button class="backuply-btn backuply-btn--danger" id="backuply-bak-multi-delete">Delete</button>
				</div>
			</h2>
		</div>
		<div class="inside">
			<div class="backuply-settings-block">
				<table class="table" style="width:100%;">
					<thead>
						<tr>
							<th style="width:3%;text-align:left;">&nbsp;</th>
							<th style="width:30%;text-align:left;"><?php esc_html_e('Backup Time', 'backuply'); ?></th>
							<th style="width:15%;text-align:left;"><?php esc_html_e('Backup Location', 'backuply'); ?></th>
							<th style="width:15%;text-align:left;"><?php esc_html_e('Host', 'backuply'); ?></th>
							<th style="width:20%;text-align:left;"><?php esc_html_e('File Size', 'backuply'); ?></th>
							<th style="width:15%;text-align:left;"><?php esc_html_e('Will Restore', 'backuply'); ?></th>
							<th style="width:15%;text-align:center;"><?php esc_html_e('Restore', 'backuply'); ?></th>
							<th style="width:15%;text-align:center;"><?php esc_html_e('Delete', 'backuply'); ?></th>
							<th style="width:15%;text-align:center;"><?php esc_html_e('Download', 'backuply'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php
					$backup_infos = backuply_get_backups_info();

					foreach($backup_infos as $count => $all_info){
						$backup_loc_name = 'Local';
						$backup_protocol = 'local';
						$backup_server_host = '-';
						$proto_id = !empty($all_info->backup_location) ? $all_info->backup_location : '';
		
						if(!empty($all_info->backup_location)) {

							if(empty($backuply_remote_backup_locs[$all_info->backup_location]) || empty($backuply_remote_backup_locs[$all_info->backup_location]['protocol'])) {
								$backups_folder = backuply_glob('backups');

								// This is to fix the given case: When the user uploads the backup file, and syncs it and if 
								// the info file has a backup_location set with some id and there is no backup location added
								// with that ID then the backup does not shows in the history tab.
								// So if that case happens we unset the backup_location so that the backups could be shown as
								// local folder backup.
								if(empty($backups_folder) ||  !file_exists($backups_folder . '/'. $all_info->name. '.tar.gz')){
									continue;
								}

								$all_info->backup_location = null;
							}

							if(!empty($all_info->backup_location) && !array_key_exists($backuply_remote_backup_locs[$all_info->backup_location]['protocol'], backuply_get_protocols())) {
								continue;
							}
							
							if(!empty($all_info->backup_location)){
								$backup_protocol = $backuply_remote_backup_locs[$all_info->backup_location]['protocol'];
								$s3_compat = !empty($backuply_remote_backup_locs[$all_info->backup_location]['s3_compatible']) ? $backuply_remote_backup_locs[$all_info->backup_location]['s3_compatible'] : '';
								$backup_loc_name = $backuply_remote_backup_locs[$all_info->backup_location]['name'];
								$backup_server_host = isset($backuply_remote_backup_locs[$all_info->backup_location]['server_host']) ? $backuply_remote_backup_locs[$all_info->backup_location]['server_host'] : '-';
							}

						}

						echo '
						<tr data-proto-id="'.esc_attr($proto_id).'">
							<td>
								<input type="checkbox" name="backuply_selected_bak[]" value="'.esc_attr($all_info->name .'.'. $all_info->ext).'"/>
							</td>
							<td>
								<div style="position:relative;" title="URL where the Backup was created '.(!empty($all_info->backup_site_url) ? esc_url($all_info->backup_site_url) : '').'">'.esc_html(backuply_format_unix_time($all_info->btime));

						if(!empty($all_info->auto_backup)){
							echo ' <span class="backuply-auto-mark">Auto</span>';
						}

						echo '<span class="dashicons dashicons-media-text backuply-backup-last-log" title="Logs" style="cursor:pointer; text-decoration:none;" data-file-name="'.esc_attr($all_info->name) . '_log.php"></span>';

						if(!empty($all_info->backup_note)){
							echo '<span class="backuply-backup-note-tip" title="'.esc_attr($all_info->backup_note).'" style="cursor:pointer; vertical-align:middle;"><svg xmlns="http://www.w3.org/2000/svg" height="16" width="14" viewBox="0 0 448 512" fill="#f9be01"><path d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H288V368c0-26.5 21.5-48 48-48H448V96c0-35.3-28.7-64-64-64H64zM448 352H402.7 336c-8.8 0-16 7.2-16 16v66.7V480l32-32 64-64 32-32z"/></svg></span>';
						}

						echo'</div>
						</td>';

						$remote_icon = $backup_protocol;
						if(!empty($s3_compat)){
							$remote_icon = $s3_compat;
						}

						echo '<td>
							<div style="display:flex; align-items:center;">';
								if(!empty($all_info->backup_location)){
									echo '<img src="'.BACKUPLY_URL.'/assets/images/'.esc_attr($remote_icon).'.svg" height="26" width="26" alt="Remote Location: '.esc_attr($backup_protocol).'"/ > &nbsp; ';
								}

								if($backup_loc_name == 'Local') {
									echo '<img src="'.BACKUPLY_URL . '/assets/images/local.svg" height="26" width="26" alt="Location : Local" /> &nbsp; ';
								}
								
								echo esc_html($backup_loc_name).'
							</div>	
						</td>
						<td>'.esc_html($backup_server_host).'</td>
						<td>'.esc_html(backuply_format_size($all_info->size)).'</td>';
						?>
							<td><?php if(!empty($all_info->backup_dir) && !empty($all_info->backup_db)){echo 'Files & Folders, Database';}else{if(!empty($all_info->backup_dir)){echo 'Files & Folders'; }else{echo 'Database';}} ?></td>
							<td style="text-align:right;">
					
								<form id="restoreform_<?php echo esc_attr($count); ?>" data-protocol="<?php echo esc_attr($backup_protocol); ?>" data-bak-name="<?php echo esc_attr($backup_loc_name); ?>">
						
									<input type="hidden" name="loc_id" value="<?php echo isset($all_info->backup_location) ? esc_attr($all_info->backup_location) : ''; ?>" />
									<input type="hidden" name="restore_dir" value="<?php echo esc_attr($all_info->backup_dir); ?>" />
									<input type="hidden" name="restore_db" value="<?php echo esc_attr($all_info->backup_db); ?>" />
									<input type="hidden" name="backup_backup_dir" value="<?php echo esc_attr(BACKUPLY_BACKUP_DIR); ?>" />
									<input type="hidden" name="fname" value="<?php echo esc_attr($all_info->name .'.'. $all_info->ext); ?>" />
									<input type="hidden" name="softpath" value="<?php echo esc_attr($dir_path); ?>" />
									<input type="hidden" name="dbexist" value="<?php if($all_info->backup_db != 0){echo 'softsql.sql';} ?>" />
									<input type="hidden" name="soft_version" value="yes" />
									<input type="hidden" name="backup_file_loc" value="<?php echo esc_attr($backup_file_loc); ?>" />
									<input type="hidden" name="size" value="<?php echo esc_attr($all_info->size); ?>" />
									<input type="hidden" name="backup_site_url" value="<?php echo esc_attr($all_info->backup_site_url); ?>" />
									<input type="hidden" name="backup_site_path" value="<?php echo esc_attr($all_info->backup_site_path); ?>" />
									
									<input name="backuply_restore_submit" class="button button-primary action" value="<?php esc_html_e('Restore', 'backuply'); ?>" onclick="backuply_restorequery('#restoreform_<?php echo esc_attr($count); ?>')" type="submit" />
							
								</form>
							</td>
							<td style="text-align:center;">
								<form method="post">
									<input type="hidden" name="tar_file" value="<?php echo esc_attr($all_info->name .'.'. $all_info->ext); ?>"/>
									<input type="hidden" name="security" value="<?php echo esc_attr($backuply_nonce); ?>"/>
									<input name="backuply_delete_backup" class="button button-primary action" onclick="return conf_del('Are you sure you want to delete the backup file ?');" value="<?php esc_html_e('Delete', 'backuply'); ?>"  type="submit" />
								</form>
							</td>
							<td style="text-align:center;">
								<?php if($backup_loc_name == 'Local') {
									?>
									<a class="button button-primary" href="<?php echo admin_url('admin-post.php?backup_name='.esc_attr($all_info->name .'.'. $all_info->ext) . '&security='.wp_create_nonce('backuply_download_security').'&action=backuply_download_backup'); ?>" download><?php esc_html_e('Download', 'backuply'); ?></a>
								<?php
								}else if($backup_loc_name == 'Backuply Cloud'){
									echo '<button type="button" class="button button-primary backuply-download-bcloud" data-name="'.esc_attr($all_info->name .'.'. $all_info->ext).'">'.esc_html__('Download', 'backuply').'</button>';
								}else {
									echo '-';
								} ?>
							</td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="postbox" id="backuply-upload-backup" title="Upload Backup File" style="display:none;">
	<?php
	
		echo '<div class="backuply-backup-uploader-selection">
			<input type="file" id="backuply-upload-backup-input" name="backuply-upload-backup" accept=".tar.gz, application/gzip" style="display:none;"/>
			<p style="font-size:18px; margin-bottom:6px;">Drag and Drop a file</p>
			<p style="font-size: 14px; margin-bottom:6px;">OR</p>
			<p><button class="button button-primary backuply-upload-select-file-btn">'.__('Select File').'</button></p>
			<p style="color:#6d6d6d; font-size:12px; margin-top:4px;">Supported file format .tar.gz</p>
		
		</div>
		<div class="backuply-upload-backup">
			<div class="backuply-upload-info-row">
				<div class="backuply-upload-info">
					<div class="backuply-upload-info-image">
						<img src="'.BACKUPLY_URL.'/assets/images/targz.png"/>
					</div>
					<div class="backuply-upload-info-text">
						<span class="backuply-upload-backup-name">Backup Name</span>
						<span class="backuply-upload-backup-size">Size</span>
					</div>
				</div>
				<div class="backuply-upload-stop-upload">
					<span class="dashicons dashicons-no-alt"></span>
				</div>
			</div>
			<div class="backuply-upload-progress-row">
				<div class="backuply-upload-bar">
					<div id="backuply-upload-bar-progress"></div>
				</div>
				<div class="backuply-upload-percentage">0%</div>
			</div>	
		</div>
		<div id="backuply-upload-alert" class="backuply-upload-alert">Backup Upload Alert</div>
		';
	?>
	</div>
	
	<!--Support-->
	<div id="backuply-support"  class="postbox backuply-tab" style="display:none;">
		<div class="postbox-header">
			<h2 class="hndle">
				<span><?php esc_html_e('Support', 'backuply'); ?></span>
			</h2>
		</div>
		<div class="backuply-settings-block">
			You can contact the Backuply Team via email. Our email address is <a href="mailto:support@backuply.com">support@backuply.com</a> or through Our <a href="https://softaculous.deskuss.com/open.php?topicId=17" target="_blank">Support Ticket System</a>
			<p>You can also check the docs <a href="https://backuply.com/docs/" target="_blank">https://backuply.com/docs/</a> to review some common issues. You might find something helpful there.</p>
			
			<h3><?php esc_html_e('Environment Info', 'backuply');?></h3>
			<table class="widefat striped">
				<tbody>
					<tr>
						<td>WP_MEMORY_LIMIT</td>
						<td><?php echo(defined('WP_MEMORY_LIMIT') ? WP_MEMORY_LIMIT : '-');?></td>
					</tr>
					
					<tr>
						<td>WP_MAX_MEMORY_LIMIT</td>
						<td><?php echo(defined('WP_MAX_MEMORY_LIMIT') ? WP_MAX_MEMORY_LIMIT : '-');?></td>
					</tr>
					<?php
					if(function_exists('ini_get')){ ?>
					
					<tr>
						<td><?php esc_html_e('PHP memory limit', 'backuply');?></td>
						<td><?php echo ini_get('memory_limit');?></td>
					</tr>
					
					<tr>
						<td><?php esc_html_e('PHP time limit', 'backuply');?></td>
						<td><?php echo ini_get('max_execution_time');?></td>
					</tr>
					<tr>
						<td><?php esc_html_e('PHP post max size', 'backuply');?></td>
						<td><?php echo ini_get('post_max_size');?></td>
					<?php } ?>
					<tr>
						<td>Server</td>
						<td><?php echo(isset($_SERVER['SERVER_SOFTWARE']) ? esc_html($_SERVER['SERVER_SOFTWARE']) : __('Unknown server', 'backuply'));?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php 
	if(!defined('BACKUPLY_PRO')){ ?>
	<!--Pro-->
	<div id="backuply-pro"  class="postbox backuply-tab" style="display:none;">
		<div class="postbox-header">
			<h2 class="hndle">
				<span><?php esc_html_e('Backuply Pro', 'backuply'); ?></span>
			</h2>
		</div>
		<div class="backuply-settings-block">
			<table class="wp-list-table widefat fixed striped table-view-list pages" style="text-align:center;">
				<thead>
					<tr>
						<th style="text-align:center;"><strong>Features</strong></th>
						<th style="text-align:center;"><strong>Backuply Free</strong></th>
						<th style="text-align:center;"><strong>Backuply Pro</strong></th>
					</tr>
				</thead>
				<tbody>
				<?php 
				
					$features = array(
						array('Local Backup', true),
						array('One-Click Restore', true),
						array('Direct Site-to-Site Migration', true),
						array('Migration Clone Ability', true),
						array('FTP Backups', true),
						array('SFTP And FTPS Backups', false),
						array('Backup To Dropbox', false),
						array('Backup To Google Drive', true),
						array('Backup To OneDrive', false),
						array('Backup To S3', false),
						array('Backup To WebDAV', false),
						array('Backup To S3 Compatible Storage', false),
						array('Auto Backups', false),
						array('Backup Rotation', false),
						array('WP-CLI Support', false),
						array('Faster Support', false)
					);
				
					foreach($features as $feature){
						
						$style = 'style=color:red';
						if(!empty($feature[1])){
							$style = 'style=color:green';
						}
						
						echo '<tr>
						<td>'.esc_html($feature[0]). '</td>
						<td><span class="dashicons '. esc_html(!empty($feature[1]) ? 'dashicons-yes' : 'dashicons-no-alt').'" '.esc_attr($style). '></span></td>
						<td><span class="dashicons dashicons-yes" style="color:green"></span></td>
					</tr>';
					} ?>
					
					<tr>
						<td></td>
						<td>Active Now</td>
						<td><a href="https://backuply.com/pricing" target="_blank" class="button button-primary">Get Pro</a></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php } ?>
</div>
<div class="backuply-modal" id="backuply-backup-progress" <?php echo $is_restoring ? '' : 'style="display:none;"';?> data-process="<?php echo $is_restoring ? 'restore' : 'backup';?>">
	<div class="backuply-modal__inner">
		<div class="backuply-modal__header">
			<div class="backuply-modal-header__title">
				<?php 
				$active_proto = 'local';
				$active_name = 'Local';
				
				if(isset($backuply['status']['backup_location']) && !empty($backuply_remote_backup_locs[$backuply['status']['backup_location']]['protocol'])){
					$active_proto = $backuply_remote_backup_locs[$backuply['status']['backup_location']]['protocol'];
					$active_name = $backuply_remote_backup_locs[$backuply['status']['backup_location']]['name'];
				}
				
				if($is_restoring){
					$restro_info = backuply_get_restoration_data();
					
					if(!empty($restro_info)){
						$active_proto = $restro_info['protocol'];
						$active_name = $restro_info['name'];
					}
				}
				
				echo '<img src="'.BACKUPLY_URL . '/assets/images/'.esc_attr($active_proto).'.svg'.'" height="26" width="26" title="'.esc_attr($active_name).'" class="backuply-modal-bak-icon"/>'
				
				?>
				<span class="backuply-title-text">
					<span class="backuply-title-backup"><?php esc_html_e('Backup in Progress', 'backuply'); ?></span>
					<span class="backuply-title-restore"><?php esc_html_e('Restore in progress', 'backuply'); ?></span>
				</span>
				
			</div>
			
			<div class="backuply-modal-header__actions">
				<?php echo !empty($is_restoring) ? '' : '<span class="dashicons dashicons-no"></span>'; ?>
			</div>
		</div>
		<div class="backuply-modal__content">
			<p class="backuply-loc-bak-name" align="center"><?php echo esc_html__('Backup Location', 'backuply').':'.esc_html($active_proto); ?></p>
			<p class="backuply-loc-restore-name" style="display:none" align="center">Restoring From: <?php echo esc_attr($active_proto); ?></p>
			<p class="backuply-progress-extra-backup" align="center"><?php esc_html_e('We are backing up your site it may take some time.', 'backuply'); ?></p>
			<p class="backuply-progress-extra-restore" align="center" style="display:none;"><?php esc_html_e('We are restoring your site it may take some time.', 'backuply'); ?></p>
			<div class="backuply-progress-bar" ><div class="backuply-progress-value" style="width:0%;"  data-done="0%"></div></div>
			<p id="backuply-rate-on-restore" align="center" style="display:none;"><a href="https://wordpress.org/support/plugin/backuply/reviews/?filter=5#new-post" target="_blank"><?php esc_html_e('Rate Us if you find Backuply useful', 'backuply'); ?></a></p>
			<div class="backuply-backup-status">
				
			</div>
		</div>
		<div class="backuply-modal_footer">
			<div>
				<button class="button-secondary" id="backuply-kill-process-btn"><?php esc_html_e('Kill Process', 'backuply'); ?></button>
			</div>
			<div>
				<button class="backuply-btn backuply-btn--danger backuply-stop-backup"><?php esc_html_e('Stop', 'backuply'); ?></button>
				<button class="backuply-btn backuply-btn--success backuply-disabled backuply-backup-finish" disabled><?php esc_html_e('Finish', 'backuply'); ?></button>
			</div>
			</div>
		</div>
	</div>
</div>

<div class="postbox" id="backuply-backup-last-log" title="Last Backup Log" style="display:none;">
	<span class="spinner"></span>
	<div class="backuply-last-logs-block"></div>
</div>
<div class="postbox" id="backuply-restore-last-log" title="Last Restore Log" style="display:none;">
	<span class="spinner"></span>
	<div class="backuply-last-logs-block"></div>
</div>

<div class="postbox" id="backuply-exclude-pattern" title="Exclude By Pattern" style="display:none;">
	<div class="backuply-exclude-pattern-block">
		<select name="exclude_pattern_type">
			<option value="extension"><?php echo backuply_pattern_type_text('extension'); ?></option>
			<option value="beginning"><?php echo backuply_pattern_type_text('beginning'); ?></option>
			<option value="end"><?php echo backuply_pattern_type_text('end'); ?></option>
			<option value="anywhere"><?php echo backuply_pattern_type_text('anywhere'); ?></option>
		</select>
		<input type="text" name="exclude_pattern_val" style="width:55%;"/>
		<span class="dashicons dashicons-insert backuply-pattern-insert"></span>
	</div>

	<?php
	if(!empty($backuply['excludes'])){
		
		foreach($backuply['excludes'] as $type => $pattern_arr) {
			
			if($type == 'exact' || $type == 'db'){
				continue;
			}
			
			foreach($pattern_arr as $key => $pattern) {
				echo '<div class="backuply-exclude-pattern-block" data-edit="true" data-type="'.esc_attr($type).'" data-key="'.esc_attr($key).'">
					<span class="backuply-exclude-pattern-type">'.backuply_pattern_type_text($type).'</span>
					<span class="backuply-exclude-pattern-val"><input type="text" name="exclude_pattern_val" style="width:90%;" value="'.esc_attr($pattern).'" disabled/></span>
					<span class="dashicons dashicons-trash backuply-pattern-delete"></span>
					<span class="dashicons dashicons-edit backuply-pattern-edit"></span>
					<span class="dashicons dashicons-insert backuply-pattern-insert" style="display:none;"></span>
					<span class="spinner" style="display:none;"></span>
				</div>';
			}
		}
	}

?>	
	
</div>

<div class="postbox" id="backuply-exclude-specific" title="Exclude Specific File/Folder" style="display:none;">
	<?php
	
		echo '<h3 align="center">Select the file/folder you want to exclude</h3>
		<div class="backuply-js-tree" style="max-height: 60%; overflow:scroll;"></div>
		<button class="button button-primary backuply-exclude-add-exact">Exclude this</button>
		<h4 align="center">List of Excluded Files/Folders</h4>';
		
		if(!empty($backuply['excludes']['exact'])){
		
			foreach($backuply['excludes']['exact'] as $key => $pattern) {
				
				echo '<div class="backuply-exclude-pattern-block" data-type="exact" data-key="'.esc_attr($key).'">
				<span class="backuply-exclude-pattern-val" style="width:95%;">'.esc_html($pattern).'</span>
				<span class="dashicons dashicons-trash backuply-pattern-delete"></span></div>';
			}
		}
	?>
</div>
</td>

<td valign="top">
	<?php
	if(!defined('SITEPAD')) {
		backuply_promotion_tmpl();
	}
	?>
</td>
</tr>
</table>
</div>
</div>
</div>
</div>

	<?php 

	if($backuply['htaccess_error']) { ?>

	<div class="backuply-modal" id="backuply-htaccess-modal" style="display:none;">
		<div class="backuply-modal__inner">
			<div class="backuply-modal__header">
				<div class="backuply-modal-header__title">
					<span class="dashicons dashicons-privacy"></span><span> Secure your backup folder</span>
				</div>
				
				<div class="backuply-modal-header__actions">
					<span class="dashicons dashicons-no" onclick="backuply_close_modal(this)"></span>
				</div>
			</div>
			<div class="backuply-modal__content">
				<p></p>
				<span>We were not able to create a <strong>.htaccess</strong> file so you will need to do it manually.</span>
				<p>To retry creation of .htaccess <button class="button-primary" id="backuply_retry_htaccess">Click here</button></p>
				<p><strong>Follow the given steps to secure the backup folder manually:</strong></p>
				
				<ul style="list-style: disk inside;">
					<li>Go to the your Wordpress Install.</li>
					<li>Go in wp-content folder and you will find a folder named <strong>backuply</strong> inside it.</li>
					<li>In backuply folder create a <strong>.htaccess</strong> file and in it paste the text given below and save it</li>
				</ul>
				<div class="backuply-code-text-wrap">
					<span class="backuply-code-text">deny from all</span>
					<span class="backuply-code-copy">Copy</span>
					<span class="backuply-code-copied">Copied</span>
				</div>
			</div>
		</div>
	</div>

	<?php }
	
	if($backuply['index_html_error']) { ?>

	<div class="backuply-modal" id="backuply-index-html-modal" style="display:none;">
		<div class="backuply-modal__inner">
			<div class="backuply-modal__header">
				<div class="backuply-modal-header__title">
					<span class="dashicons dashicons-privacy"></span><span> Secure your backup folder</span>
				</div>
				
				<div class="backuply-modal-header__actions">
					<span class="dashicons dashicons-no" onclick="backuply_close_modal(this)"></span>
				</div>
			</div>
			<div class="backuply-modal__content">
				<p></p>
				<span>We were not able to create a <strong>index.html</strong> file so you will need to do it manually.</span>
				<p><strong>Follow the given steps to secure the backup folder manually:</strong></p>
				
				<ul style="list-style: disk inside;">
					<li>Go to the your Wordpress Install.</li>
					<li>Go in wp-content folder and you will find a folder named <strong>backuply</strong> inside it.</li>
					<li>In backuply folder create a <strong>index.html</strong> file and in it paste the text given below and save it</li>
					<li>There would be more folders inside backuply folder like backups-randomstring and backups_info-randomstring, so do the same as above inside those folders too.</li>
					<li>Or you can contact backuply at support@backuply.com we will help you out</li>
				</ul>
				<div class="backuply-code-text-wrap">
					<span class="backuply-code-text"><?php echo esc_html('<html><body><a href="https://backuply.com" target="_blank">WordPress backups by Backuply</a></body></html>'); ?></span>
					<span class="backuply-code-copy">Copy</span>
					<span class="backuply-code-copied">Copied</span>
				</div>
			</div>
		</div>
	</div>

	<?php }
}

// Formats time if the time is in utc timezone
function backuply_format_unix_time($unix_time){
	// Making time zone conversions.
	$unix_time = (int) $unix_time;

	$default_timezone = date_default_timezone_get();
	if($default_timezone == 'UTC' && class_exists('DateTime') && class_exists('DateTimeZone')){
		$time_zone_string = wp_timezone_string();
		$time_zone = new DateTimeZone($time_zone_string);
		$date_time = new DateTime('@'.$unix_time);
		$date_time->setTimezone($time_zone);
		$formated_time = $date_time->format('jS F Y h:i A');

		return $formated_time;
	}

	$formated_time = date('jS F Y h:i A', $unix_time);	
	return $formated_time;
}