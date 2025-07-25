<?php
/**
 *  A Smush Task manager class
 */

if (!defined('ABSPATH')) die('Access denied.');

if (!class_exists('Updraft_Task_Manager_Commands_1_0')) require_once(WPO_PLUGIN_MAIN_PATH . 'vendor/team-updraft/common-libs/src/updraft-tasks/class-updraft-task-manager-commands.php');

if (!class_exists('Updraft_Smush_Manager_Commands')) :

class Updraft_Smush_Manager_Commands extends Updraft_Task_Manager_Commands_1_0 {

	/**
	 * Stores the bulk of images to be processed
	 *
	 * @var array
	 */
	private $images = array();

	/**
	 * Flag to return a valid envelope on AJAX calls
	 *
	 * @var bool
	 */
	public $background_command = false;

	/**
	 * Flag to return a valid envelope on heartbeat AJAX calls
	 *
	 * @var bool
	 */
	public $heartbeat_command = false;

	/**
	 * Store the response to be sent at shutdown
	 *
	 * @var array
	 */
	public $final_response = array();

	/**
	 * The commands constructor
	 *
	 * @param mixed $task_manager - A task manager instance
	 */
	public function __construct($task_manager) {
		parent::__construct($task_manager);
	}

	/**
	 * Returns a list of commands available for smush related operations
	 */
	public static function get_allowed_ajax_commands() {

		$commands = parent::get_allowed_ajax_commands();

		$smush_commands = array(
			'compress_single_image',
			'restore_single_image',
			'process_bulk_smush',
			'update_smush_options',
			'get_ui_update',
			'process_pending_images',
			'clear_pending_images',
			'clear_smush_stats',
			'check_server_status',
			'get_smush_logs',
			'mark_as_compressed',
			'mark_all_as_uncompressed',
			'clean_all_backup_images',
			'reset_webp_serving_method',
			'convert_to_webp_format',
			'update_webp_options',
			'get_smush_details',
			'get_smush_settings_form',
		);

		return array_merge($commands, $smush_commands);
	}

	/**
	 * Process the compression of a single image
	 *
	 * @param mixed $data - sent in via AJAX
	 * @return WP_Error|array - information about the operation or WP_Error object on failure
	 */
	public function compress_single_image($data) {

		$options = empty($data['smush_options']) ? $this->task_manager->get_smush_options() : $data['smush_options'];
		
		$image = isset($data['selected_image']['attachment_id']) ? absint($data['selected_image']['attachment_id']) : 0;
		$blog = isset($data['selected_image']['blog_id']) ? absint($data['selected_image']['blog_id']) : 0;
		
		if (0 === $image) {
			return new WP_Error('invalid_image', __('Image ID is invalid', 'wp-optimize'));
		}
		
		if (0 === $blog && is_multisite()) {
			return new WP_Error('invalid_blog', __('Blog ID is invalid', 'wp-optimize'));
		}

		// A subsite administrator can only compress their own image. If the blog ID isn't theirs, return an error.
		if ($blog && is_multisite() && get_current_blog_id() != $blog && !current_user_can('manage_network_options')) {
			return new WP_Error('compression_not_permitted', __('The blog ID provided does not match the current blog.', 'wp-optimize'));
		}
		
		$server = isset($options['compression_server']) ? sanitize_text_field($options['compression_server']) : $this->task_manager->get_default_webservice();
		
		$lossy = isset($options['lossy_compression']) ? filter_var($options['lossy_compression'], FILTER_VALIDATE_BOOLEAN) : false;
		$backup = isset($options['back_up_original']) ? filter_var($options['back_up_original'], FILTER_VALIDATE_BOOLEAN) : true;
		$exif = isset($options['preserve_exif']) ? filter_var($options['preserve_exif'], FILTER_VALIDATE_BOOLEAN) : false;
		$quality = isset($options['image_quality']) ? absint($options['image_quality']) : 92;


		$options = array(
			'attachment_id' 	=> $image,
			'blog_id'		   => $blog,
			'image_quality' 	=> $quality,
			'keep_original'		=> $backup,
			'lossy_compression' => $lossy,
			'preserve_exif'	 => $exif
		);

		if (filesize(get_attached_file($image)) > 5242880) {
			$options['request_timeout'] = 180;
		}

		$success = $this->task_manager->compress_single_image($image, $options, $server);

		if (!$success) {
			return new WP_Error('compress_failed', get_post_meta($image, 'smush-info', true));
		}

		$response = array();
		$response['status'] = true;
		$response['operation'] = 'compress';
		$response['options'] = $options;
		$response['server'] = $server;
		$response['success'] = $success;
		$response['restore_possible'] = $backup;
		$response['summary'] = get_post_meta($image, 'smush-info', true);

		$smush_stats = get_post_meta($image, 'smush-stats', true);
		if (isset($smush_stats['sizes-info'])) {
			$response['sizes-info'] = WP_Optimize()->include_template('images/smush-details.php', true, array('sizes_info' => $smush_stats['sizes-info']));
		}

		$response['media_column_html'] = $this->get_smush_media_column_content($blog, $image);

		return $response;
	}

	/**
	 * Restores a single image, if backup is available
	 *
	 * @param mixed $data - Sent in via AJAX
	 * @return WP_Error|array - information about the operation or a WP_Error object on failure
	 */
	public function restore_single_image($data) {
		
		$blog_id = isset($data['blog_id']) ? absint($data['blog_id']) : 0;
		$image_id   = isset($data['selected_image']) ? absint($data['selected_image']) : 0;


		$success = $this->task_manager->restore_single_image($image_id, $blog_id);

		if (is_wp_error($success)) {
			return $success;
		}

		$response = array();
		$response['status'] = true;
		$response['operation'] = 'restore';
		$response['blog_id'] = $blog_id;
		$response['image']	 = $image_id;
		$response['success'] = $success;
		$response['summary'] = __('The image was restored successfully', 'wp-optimize');

		$response['media_column_html'] = $this->get_smush_media_column_content($blog_id, $image_id);
		
		return $response;
	}

	/**
	 * Process the compression of multiple images
	 *
	 * @param mixed $data - Sent in via AJAX
	 * @return array
	 */
	public function process_bulk_smush($data = array()) {
		$images = isset($data['selected_images']) && is_array($data['selected_images']) ? $this->sanitize_images($data['selected_images']) : array();
		
		$this->images = $images;

		$this->background_command = true;

		$this->final_response = $this->get_ui_update($this->images);

		add_action('shutdown', array($this, 'process_bulk_smush_shutdown'));

		return $this->final_response;
	}

	/**
	 * Close request connection at the `shutdown` hook and start processing the bulk
	 *
	 * @return void
	 */
	public function process_bulk_smush_shutdown() {
		WP_Optimize()->close_browser_connection(wp_json_encode($this->final_response));

		$this->task_manager->process_bulk_smush($this->images);
		exit;
	}

	/**
	 * Returns useful information for the UI and closes the connection
	 *
	 * @param mixed $data - Sent in via AJAX
	 *
	 * @return array - Information for the UI
	 */
	public function get_ui_update($data) {
		$use_cache = isset($data['use_cache']) ? sanitize_text_field($data['use_cache']) : 'true';
		$image_list = isset($data['image_list']) && is_array($data['image_list']) ? $this->sanitize_images($data['image_list']) : false;

		$ui_update = array();
		$ui_update['status'] = true;
		$ui_update['is_multisite'] = is_multisite() ? 1 : 0;
		$pending_tasks = $this->task_manager->get_pending_tasks();
		
		$ui_update['pending_tasks'] = is_array($pending_tasks) ? count($this->task_manager->get_pending_tasks()) : 0;
		$ui_update['unsmushed_images'] = $this->task_manager->get_uncompressed_images($use_cache);
		$ui_update['admin_urls'] = $this->task_manager->get_admin_urls();
		$ui_update['completed_task_count'] = $this->task_manager->options->get_option('completed_task_count', 0);
		$ui_update['bytes_saved'] = WP_Optimize()->format_size($this->task_manager->options->get_option('total_bytes_saved', 0));
		$ui_update['percent_saved'] = number_format($this->task_manager->options->get_option('total_percent_saved', 1), 2).'%';
		$ui_update['failed_task_count'] = $this->task_manager->get_failed_task_count();

		if (is_multisite()) {
			// translators: %d: number of images compressed, %2$s: size of saved space, %3$02d: average percent saved
			$ui_update['summary'] = sprintf(__('Since the last reset of compression statistics on this multisite, a total of %d image(s) were compressed across the network.', 'wp-optimize').' '.__('This saved approximately %2$s of space at an average of %3$02d percent per image.', 'wp-optimize'), $ui_update['completed_task_count'], $ui_update['bytes_saved'], $ui_update['percent_saved']);
		} else {
			// translators: %d: number of images compressed, %2$s: size of saved space, %3$02d: average percent saved
			$ui_update['summary'] = sprintf(__('Since your compression statistics were last reset, a total of %d image(s) were compressed on this site.', 'wp-optimize').' '.__('This saved approximately %2$s of space at an average of %3$02d percent per image.', 'wp-optimize'), $ui_update['completed_task_count'], $ui_update['bytes_saved'], $ui_update['percent_saved']);
		}
		// translators: %d: number of images that could not be compressed
		$ui_update['failed'] = sprintf(__("%d image(s) could not be compressed.", 'wp-optimize'), $ui_update['failed_task_count']) . ' ' . __('Please see the logs for more information, or try again later.', 'wp-optimize');
		// translators: %d: number of images that were selected for compression, and pending processing
		$ui_update['pending'] = sprintf(__("%d image(s) images were selected for compressing previously, but were not all processed.", 'wp-optimize'), $ui_update['pending_tasks']) . ' ' . __('You can either complete them now or cancel and retry later.', 'wp-optimize');
		$ui_update['smush_complete'] = $this->task_manager->is_queue_processed();
		
		if ($image_list) {
			$stats = $this->task_manager->get_session_stats($image_list);
			$ui_update['session_stats'] = "";

			if (!empty($stats['success'])) {
				// translators: %d: number of images compressed
				$ui_update['session_stats'] .= sprintf(__("A total of %d image(s) were successfully compressed in this iteration.", 'wp-optimize'), $stats['success']);
			}

			if (!empty($stats['fail'])) {
				// translators: %d: number of images that could not be compressed
				$ui_update['session_stats'] .= sprintf(__("%d selected image(s) could not be compressed.", 'wp-optimize'), $stats['fail']) . ' ' . __('Please see the logs for more information, you may try again later.', 'wp-optimize');
			}
		}
		
		return $ui_update;

	}

	/**
	 * Updates webp related options
	 *
	 * @param mixed $data - Sent in via AJAX
	 * @return WP_Error|array - information about the operation or WP_Error object on failure
	 */
	public function update_webp_options($data) {
		$webp_instance = WP_Optimize()->get_webp_instance();
		$options = array();
		$options['webp_conversion'] = isset($data['webp_conversion']) ? filter_var($data['webp_conversion'], FILTER_VALIDATE_BOOLEAN) : false;

		// Only run checks when trying to enable WebP
		if ($options['webp_conversion']) {
			//Run checks if we are enabling webp conversion
			if (!$webp_instance->shell_functions_available()) {
				$webp_instance->disable_webp_conversion();
				$webp_instance->log("Required WebP shell functions are not available on the server, disabling WebP conversion");
				return new WP_Error('update_failed_no_shell_functions', __('Required WebP shell functions are not available on the server.', 'wp-optimize'));
			}

			// Run conversion test if not already done and set necessary option value
			if ($webp_instance->should_run_webp_conversion_test()) {
				$converter_status = WPO_WebP_Test_Run::get_converter_status();

				if (!$webp_instance->is_webp_conversion_successful()) {
					$webp_instance->disable_webp_conversion();
					$webp_instance->log("No working WebP converter was found on the server when updating WebP options, disabling WebP conversion");
					return new WP_Error('update_failed_no_working_webp_converter', __('No working WebP converter was found on the server.', 'wp-optimize'));
				}

				$options['webp_conversion_test'] = true;
				$options['webp_converters'] = $converter_status['working_converters'];
			}

			// Run serving methods tests and set necessary option values
			// Not possible to test alter html since test is browser based
			$webp_instance->save_htaccess_rules();
			if (!$webp_instance->is_webp_redirection_possible()) {
				$webp_instance->empty_htaccess_file();
				$options['redirection_possible'] = 'false';
			} else {
				$options['redirection_possible'] = 'true';
			}
		}

		$success = $this->task_manager->update_smush_options($options);

		if (!$success) {
			$webp_instance->disable_webp_conversion();
			$webp_instance->log("WebP options could not be updated");
			return new WP_Error('update_failed', __('WebP options could not be updated.', 'wp-optimize'));
		}

		// Setup daily CRON only when enabling WebP and Delete daily CRON when disabling WebP
		if ($options['webp_conversion']) {
			$webp_instance->init_webp_cron_scheduler();
		} else {
			$webp_instance->remove_webp_cron_schedules();
			$webp_instance->empty_htaccess_file();
		}

		do_action('wpo_save_images_settings');

		$response = array();
		$response['status'] = true;
		$response['saved'] = $success;
		$response['summary'] = __('WebP options updated successfully.', 'wp-optimize');

		return $response;
	}

	/**
	 * Updates smush related options
	 *
	 * @param mixed $data - Sent in via AJAX
	 * @return WP_Error|array - information about the operation or WP_Error object on failure
	 */
	public function update_smush_options($data) {
		$options = array();
		$options['compression_server'] = isset($data['compression_server']) ? sanitize_text_field($data['compression_server']) : $this->task_manager->get_default_webservice();
		$options['lossy_compression'] = isset($data['lossy_compression']) ? filter_var($data['lossy_compression'], FILTER_VALIDATE_BOOLEAN) : false;
		$options['back_up_original'] = isset($data['back_up_original']) ? filter_var($data['back_up_original'], FILTER_VALIDATE_BOOLEAN) : true;
		$options['back_up_delete_after'] = isset($data['back_up_delete_after']) ? filter_var($data['back_up_delete_after'], FILTER_VALIDATE_BOOLEAN) : true;
		$options['back_up_delete_after_days'] = isset($data['back_up_delete_after_days']) ? absint($data['back_up_delete_after_days']) : 50;
		$options['preserve_exif'] = isset($data['preserve_exif']) ? filter_var($data['preserve_exif'], FILTER_VALIDATE_BOOLEAN) : false;
		$options['autosmush'] = isset($data['autosmush']) ? filter_var($data['autosmush'], FILTER_VALIDATE_BOOLEAN) : false;
		$options['image_quality'] = isset($data['image_quality']) ? absint($data['image_quality']) : 92;
		$options['show_smush_metabox'] = isset($data['show_smush_metabox']) && filter_var($data['show_smush_metabox'], FILTER_VALIDATE_BOOLEAN) ? 'show' : 'hide';

		$success = $this->task_manager->update_smush_options($options);

		if (!$success) {
			return new WP_Error('update_failed', __('Smush options could not be updated', 'wp-optimize'));
		}

		do_action('wpo_save_images_settings');

		$response = array();
		$response['status'] = true;
		$response['saved'] = $success;
		$response['summary'] = __('Options updated successfully', 'wp-optimize');
		
		return $response;
	}

	/**
	 * Clears any smush related stats
	 *
	 * @return WP_Error|array - information about the operation or WP_Error object on failure
	 */
	public function clear_smush_stats() {

		$success = $this->task_manager->clear_smush_stats();

		if (!$success) {
			return new WP_Error('update_failed', __('Stats could not be cleared', 'wp-optimize'));
		}

		$response = array();
		$response['status'] = true;
		$response['summary'] = __('Stats cleared successfully', 'wp-optimize');

		return $response;
	}

	/**
	 * Checks if the selected server is online
	 *
	 * @param mixed $data - Sent in via AJAX
	 */
	public function check_server_status($data) {
		$server = isset($data['server']) ? sanitize_text_field($data['server']) : $this->task_manager->get_default_webservice();
		$response = array();
		$response['status'] = true;
		$response['online'] = $this->task_manager->check_server_online($server);
		
		if (!$response['online']) {
			$response['error'] = get_option($this->task_manager->get_associated_task($server));
		}

		return $response;
	}

	/**
	 * Completes any pending tasks
	 *
	 * @return array
	 */
	public function process_pending_images() {
		return $this->process_bulk_smush();
	}

	/**
	 * Deletes and removes any pending tasks from queue
	 *
	 * @param array $data - in 'restore_images' index passed an array with ids of images to restore
	 * @return WP_Error|array - information about the operation or WP_Error object on failure
	 */
	public function clear_pending_images($data) {

		if (!empty($data['restore_images']) && is_array($data['restore_images'])) {
			$restore_images = $this->sanitize_images($data['restore_images']);
			foreach ($restore_images as $image) {
				$this->task_manager->restore_single_image($image['attachment_id'], $image['blog_id']);
			}
		}

		$success = $this->task_manager->clear_pending_images();

		if (!$success) {
			return new WP_Error('error_deleting_tasks', __('Pending tasks could not be cleared', 'wp-optimize'));
		}

		$response = array();
		$response['status'] = true;
		$response['summary'] = __('Pending tasks cleared successfully', 'wp-optimize');
		
		return $response;
	}

	/**
	 * Mark selected images as already compressed.
	 *
	 * @param array $data
	 * @return array
	 */
	public function mark_as_compressed($data) {
		$response = array();
		$selected_images = array();

		$unmark = isset($data['unmark']) && $data['unmark'];
		$image_list = isset($data['selected_images']) && is_array($data['selected_images']) ? $this->sanitize_images($data['selected_images']) : array();

		foreach ($image_list as $image) {
			if (!array_key_exists($image['blog_id'], $selected_images)) $selected_images[$image['blog_id']] = array();
			
			$selected_images[$image['blog_id']][] = $image['attachment_id'];
		}

		$info = __('This image is marked as already compressed by another tool.', 'wp-optimize');

		foreach (array_keys($selected_images) as $blog_id) {
			if (is_multisite()) switch_to_blog($blog_id);

			foreach ($selected_images[$blog_id] as $attachment_id) {
				if ($unmark) {
					delete_post_meta($attachment_id, 'smush-complete');
					delete_post_meta($attachment_id, 'smush-marked');
					delete_post_meta($attachment_id, 'smush-info');
				} else {
					update_post_meta($attachment_id, 'smush-complete', true);
					update_post_meta($attachment_id, 'smush-marked', true);
					update_post_meta($attachment_id, 'smush-info', $info);
				}
			}

			if (is_multisite()) restore_current_blog();
		}

		$response['status'] = true;

		if ($unmark) {
			$response['summary'] = _n('The selected image was successfully marked as uncompressed', 'The selected images were successfully marked as uncompressed', count($image_list), 'wp-optimize');
		} else {
			$response['summary'] = _n('The selected image was successfully marked as compressed', 'The selected images were successfully marked as compressed', count($image_list), 'wp-optimize');
		}

		$response['info'] = $info;

		if (1 === count($image_list)) {
			$selected_image = reset($image_list);
			$response['media_column_html'] = $this->get_smush_media_column_content($selected_image['blog_id'], $selected_image['attachment_id']);
		}

		return $response;
	}

	/**
	 * Mark all images as uncompressed and if posted restore_backup argument
	 * then try to restore images form backup.
	 *
	 * @param array $data
	 * @return array
	 */
	public function mark_all_as_uncompressed($data) {

		$restore_backup = isset($data['restore_backup']) && $data['restore_backup'];
		$images_per_request = apply_filters('mark_all_as_uncompressed_images_per_request', 100);
		$delete_only_backups_meta = isset($data['delete_only_backups_meta']) && $data['delete_only_backups_meta'];

		if (is_multisite()) {
			// option where we store last completed blog id
			$option_name = 'mark_as_uncompressed_last_blog_id';
			$smushed_images_total_option_name = 'smushed_images_total';
			// set default value for response
			$response = array(
				'completed' => true,
				'message' => __('All the compressed images were successfully restored.', 'wp-optimize'),
			);

			// get all blogs ids
			$blogs = WP_Optimize()->get_sites();
			$blogs_ids = wp_list_pluck($blogs, 'blog_id');
			sort($blogs_ids);

			// select the blog for processing
			$last_completed_blog_id = $this->task_manager->options->get_option($option_name, false);
			$index = $last_completed_blog_id ? array_search($last_completed_blog_id, $blogs_ids) + 1 : 0;

			if ($index < count($blogs_ids)) {
				$blog_id = $blogs_ids[$index];
				$response = $this->task_manager->bulk_restore_compressed_images($restore_backup, $blog_id, $images_per_request, $delete_only_backups_meta);
				$smushed_images_total = $this->task_manager->options->get_option($smushed_images_total_option_name, 0) + $response['smushed_images_count'];
				// if we get completed the current blog then update last completed blog option value
				// and if we have other blogs for processing then set complete to false as we have not
				// processed all blogs
				if ($response['completed']) {
					if ($index + 1 < count($blogs_ids)) {
						$response['completed'] = false;
						$this->task_manager->options->update_option($option_name, $blog_id);
						$this->task_manager->options->update_option($smushed_images_total_option_name, $smushed_images_total);
					} else {
						if ($delete_only_backups_meta) {
							if ($smushed_images_total > 0) {
								$response['message'] = __('All the compressed images with backup copies of their original files were successfully restored.', 'wp-optimize');
								// translators: %s - number of smushed images
								$response['message'] .= ' '.sprintf(_n('Unable to restore %s image without backup files.', 'Unable to restore %s images without backup files.', $smushed_images_total, 'wp-optimize'), $smushed_images_total);
							} else {
								$response['message'] = __('All the compressed images were successfully restored.', 'wp-optimize');
							}
						} else {
							$response['message'] = __('All the compressed images were successfully marked as uncompressed.', 'wp-optimize');
						}
					}
				}
			}

			// if we get an error or completed the work then delete option with last completed blog id.
			if ($response['completed'] || isset($response['error'])) {
				$this->task_manager->options->delete_option($option_name);
				$this->task_manager->options->delete_option($smushed_images_total_option_name);
			}
		} else {
			$response = $this->task_manager->bulk_restore_compressed_images($restore_backup, 0, $images_per_request, $delete_only_backups_meta);
		}

		return $response;
	}

	/**
	 * Returns the log file
	 *
	 * @return WP_Error|file - logfile or WP_Error object on failure
	 */
	public function get_smush_logs() {

		$logfile = $this->task_manager->get_logfile_path();

		if (!file_exists($logfile)) {
			 $this->task_manager->write_log_header();
		}

		if (is_file($logfile)) {
			if ($this->heartbeat_command) {
				// The response will be inside the heartbeat response envelope, as each response of a heartbeat goes in its own unique ID key
				readfile($logfile); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile -- Using WP_Filesystem and get contents will result in `echo` and unescaped error
			} else {
				// Headers are needed for the `Download logs` link, which will run this command and just prompt a file download
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="'.basename($logfile).'"');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($logfile));
				readfile($logfile); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile -- Using WP_Filesystem and get contents will result in `echo` and unescaped error
				exit;
			}
		} else {
			return new WP_Error('log_file_error', __('Log file does not exist or could not be read', 'wp-optimize'));
		}
	}

	/**
	 * Clean all backup images command.
	 *
	 * @return array
	 */
	public function clean_all_backup_images() {
		$upload_dir = wp_upload_dir(null, false);
		$base_dir = $upload_dir['basedir'];

		$this->task_manager->clear_backup_images_directory($base_dir, 0);

		return array(
			'status' => true,
		);
	}

	/**
	 * Resets webp serving method
	 *
	 * @return array|WP_Error
	 */
	public function reset_webp_serving_method() {
		$webp_instance = WP_Optimize()->get_webp_instance();
		//Run checks before calling reset_webp_serving_method
		if (!$webp_instance->shell_functions_available()) {
			$webp_instance->disable_webp_conversion();
			$webp_instance->log("The WebP serving method cannot be reset because required WebP shell functions are not available on the server");
			return new WP_Error('reset_failed_no_shell_functions', __('The WebP serving method cannot be reset because required WebP shell functions are not available on the server', 'wp-optimize'));
		} elseif (!$webp_instance->is_webp_conversion_enabled()) {
			$webp_instance->disable_webp_conversion();
			$webp_instance->log("The WebP serving method cannot be reset because WebP conversion is currently disabled");
			return new WP_Error('reset_failed_webp_conversion_disabled', __('The WebP serving method cannot be reset because WebP conversion is currently disabled', 'wp-optimize'));
		}

		$webp_instance->reset_webp_serving_method();
		return array(
			'success' => true,
		);
	}

	/**
	 * Convert the image to webp format
	 *
	 * @param array $data
	 * @return array
	 */
	public function convert_to_webp_format($data) {
		$attachment_id = isset($data['attachment_id']) ? absint($data['attachment_id']) : 0;
		if (0 === $attachment_id) return $this->image_not_found_response();

		$images = WPO_Image_Utils::get_attachment_files($attachment_id);
		if (empty($images)) return $this->image_not_found_response();

		$images['original'] = get_attached_file($attachment_id);
		foreach ($images as $image) {
			WPO_WebP_Utils::do_webp_conversion($image);
		}

		return array(
			'success' => __('Image is converted to WebP format.', 'wp-optimize'),
		);
	}

	/**
	 * Get Smush settings form
	 *
	 * @param array $data
	 * @return array
	 */
	public function get_smush_settings_form($data) {
		$attachment_id = isset($data['attachment_id']) ? absint($data['attachment_id']) : 0;
		if (0 === $attachment_id) return $this->image_not_found_response();

		$compressed = get_post_meta($attachment_id, 'smush-complete', true) ? true : false;

		$smush_options = Updraft_Smush_Manager()->get_smush_options();

		$extract = array(
			'post_id' => $attachment_id,
			'smush_options' => $smush_options,
			'custom' => 90 >= $smush_options['image_quality'] && 65 <= $smush_options['image_quality'],
			'smush_display' => $compressed ? "display:none;" : "display:block;",
		);

		return array(
			'success' => true,
			'html' => WP_Optimize()->include_template('admin-metabox-smush-settings.php', true, $extract),
		);
	}

	/**
	 * Get content for Media Library column content
	 *
	 * @param int $blog_id
	 * @param int $attachment_id
	 *
	 * @return string
	 */
	private function get_smush_media_column_content(int $blog_id, int $attachment_id): string {
		if (is_multisite()) switch_to_blog($blog_id);

		$content = Updraft_Smush_Manager()->get_smush_details($attachment_id);
		
		if (is_multisite()) restore_current_blog();
		
		return $content;
	}

	/**
	 * Returns image not found response
	 *
	 * @return array
	 */
	private function image_not_found_response() {
		return array(
			'error' => __('Image not found', 'wp-optimize'),
		);
	}
	
	/**
	 * Get smush details of given image IDs
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function get_smush_details($data) {
		$selected_images = isset( $data['selected_images'] ) && is_array( $data['selected_images'] ) ? array_map( 'absint', $data['selected_images'] ) : array();
		$smush_details = array();
		foreach ($selected_images as $attachment_id) {
			$smush_details[$attachment_id] = $this->task_manager->get_smush_details($attachment_id);
		}
		
		return array(
			'success' => true,
			'smush_details' => $smush_details,
		);
	}

	/**
	 * Sanitize array of images ensuring proper integer values for attachment_id and blog_id
	 *
	 * @param array $images Array of image data to sanitize
	 * @return array
	 */
	private function sanitize_images(array $images) : array {
		$result = array();
		
		foreach ($images as $image) {
			$attachment_id = isset($image['attachment_id']) ? absint($image['attachment_id']) : 0;
			$blog_id = isset($image['blog_id']) ? absint($image['blog_id']) : 0;
			
			// Skip entries where either value is zero
			if (0 === $attachment_id || 0 === $blog_id) {
				continue;
			}
			
			$result[] = array(
				'attachment_id' => $attachment_id,
				'blog_id'       => $blog_id,
			);
		}
		
		return $result;
	}
}

endif;
