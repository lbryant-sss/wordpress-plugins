<?php

/**
 * Backup execution.
 *
 * Handles database backups at scheduled interval.
 *
 * @since   4.0.0
 *
 * @package iThemes_Security
 */
class ITSEC_Backup {

	/**
	 * The module's saved options
	 *
	 * @since  4.0.0
	 * @access private
	 * @var array
	 */
	private $settings;

	/**
	 * Setup the module's functionality.
	 *
	 * Loads the backup detection module's unpriviledged functionality including
	 * performing the scans themselves.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	function run() {

		$this->settings = ITSEC_Modules::get_settings( 'backup' );

		add_action( 'itsec_execute_backup_cron', array( $this, 'do_backup' ) );

		add_filter( 'debug_information', [ $this, 'add_site_health_info' ] );

		add_filter( 'itsec_notifications', array( $this, 'register_notification' ) );
		add_filter( 'itsec_backup_notification_strings', array( $this, 'notification_strings' ) );

		if ( class_exists( 'pb_backupbuddy' ) ) {
			// Don't run when BackupBuddy is active.
			return;
		}

		ITSEC_Core::get_scheduler()->register_custom_schedule( 'backup', DAY_IN_SECONDS * $this->settings['interval'] );

		if ( ! $this->settings['enabled'] || $this->settings['interval'] <= 0 ) {
			// Don't run when scheduled backups aren't enabled or the interval is zero or less.
			return;
		}

		add_action( 'itsec_scheduled_backup', array( $this, 'scheduled_callback' ) );
	}

	/**
	 * Called when it is time for the backup to run.
	 */
	public function scheduled_callback() {
		$this->do_backup();
	}

	/**
	 * Public function to get lock and call backup.
	 *
	 * Attempts to get a lock to prevent concurrent backups and calls the backup function itself.
	 *
	 * @since 4.0.0
	 *
	 * @param boolean $one_time whether this is a one time backup
	 *
	 * @return array|WP_Error false on error or nothing
	 */
	public function do_backup( $one_time = false ) {

		if ( ! ITSEC_Lib::get_lock( 'backup', 180 ) ) {
			return new WP_Error( 'itsec-backup-do-backup-already-running', __( 'Unable to create a backup at this time since a backup is currently being created. If you wish to create an additional backup, please wait a few minutes before trying again.', 'better-wp-security' ) );
		}

		ITSEC_Lib::set_minimum_memory_limit( '256M' );
		$result = $this->execute_backup( $one_time );
		ITSEC_Lib::release_lock( 'backup' );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		switch ( $this->settings['method'] ) {
			case 'both':
				$message = __( 'Backup complete. The backup was sent to the selected email recipients and was saved locally.', 'better-wp-security' );
				break;
			case 'email':
				$message = __( 'Backup complete. The backup was sent to the selected email recipients.', 'better-wp-security' );
				break;
			default:
				$message = __( 'Backup complete. The backup was saved locally.', 'better-wp-security' );
				break;
		}

		$result['message'] = $message;

		return $result;
	}

	/**
	 * Executes backup function.
	 *
	 * Handles the execution of database backups.
	 *
	 * @since 4.0.0
	 *
	 * @param bool $one_time whether this is a one-time backup
	 *
	 * @return array|WP_Error
	 */
	private function execute_backup( $one_time = false ) {
		global $wpdb;

		require_once( ITSEC_Core::get_core_dir() . 'lib/class-itsec-lib-directory.php' );

		$dir    = $this->settings['location'];
		$result = ITSEC_Lib_Directory::create( $dir );

		if ( is_wp_error( $result ) ) {
			return $result;
		} elseif ( ! $result ) {
			return new WP_Error( 'itsec-backup-failed-to-create-backup-dir', esc_html__( 'Unable to create the backup directory due to an unknown error.', 'better-wp-security' ) );
		}

		$file = "$dir/backup-" . substr( sanitize_title( get_bloginfo( 'name' ) ), 0, 20 ) . '-' . current_time( 'Ymd-His' ) . '-' . wp_generate_password( 30, false ) . '.sql';

		if ( false === ( $fh = @fopen( $file, 'w' ) ) ) {
			return new WP_Error( 'itsec-backup-failed-to-write-backup-file', esc_html__( 'Unable to write the backup file. This may be due to a permissions or disk space issue.', 'better-wp-security' ) );
		}

		if ( false === $one_time ) {
			ITSEC_Modules::set_setting( 'backup', 'last_run', ITSEC_Core::get_current_time_gmt() );
		}

		$tables = $wpdb->get_col( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->base_prefix . '%' ) );

		$max_rows_per_query = 1000;

		foreach ( $tables as $table ) {
			$create_table = $wpdb->get_var( "SHOW CREATE TABLE `$table`;", 1 ) . ';' . PHP_EOL . PHP_EOL;
			$create_table = preg_replace( '/^CREATE TABLE /', 'CREATE TABLE IF NOT EXISTS ', $create_table );
			@fwrite( $fh, $create_table );

			if ( in_array( substr( $table, strlen( $wpdb->prefix ) ), $this->settings['exclude'] ) ) {
				// User selected to exclude the data from this table.
				fwrite( $fh, PHP_EOL . PHP_EOL );
				continue;
			}


			$num_fields = count( $wpdb->get_results( "DESCRIBE `$table`;" ) );

			$offset        = 0;
			$has_more_rows = true;

			while ( $has_more_rows ) {
				$rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `$table` LIMIT %d, %d", $offset, $max_rows_per_query ), ARRAY_N );

				foreach ( $rows as $row ) {
					$sql = "INSERT INTO `$table` VALUES (";

					for ( $j = 0; $j < $num_fields; $j ++ ) {
						if ( isset( $row[ $j ] ) ) {
							$row[ $j ] = addslashes( $row[ $j ] );

							if ( PHP_EOL !== "\n" ) {
								$row[ $j ] = preg_replace( '#' . PHP_EOL . '#', "\n", $row[ $j ] );
							}

							$sql .= '"' . $row[ $j ] . '"';
						} else {
							$sql .= '""';
						}

						if ( $j < ( $num_fields - 1 ) ) {
							$sql .= ',';
						}
					}

					$sql .= ");" . PHP_EOL;

					@fwrite( $fh, $sql );
				}

				if ( count( $rows ) < $max_rows_per_query ) {
					$has_more_rows = false;
				} else {
					$offset += $max_rows_per_query;
				}

			}

			@fwrite( $fh, PHP_EOL . PHP_EOL );

		}

		@fwrite( $fh, PHP_EOL . PHP_EOL );
		@fclose( $fh );

		$backup_file = $file;

		if ( $this->settings['zip'] ) {
			if ( ! class_exists( 'PclZip' ) ) {
				require( ABSPATH . 'wp-admin/includes/class-pclzip.php' );
			}

			$zip_file = substr( $file, 0, - 4 ) . '.zip';
			$pclzip   = new PclZip( $zip_file );

			if ( 0 != $pclzip->create( $file, PCLZIP_OPT_REMOVE_PATH, $dir ) ) {
				@unlink( $file );
				$file = $zip_file;
			}
		}

		if ( 'local' !== $this->settings['method'] || true === $one_time ) {
			$mail_success = $this->send_mail( $file );
		} else {
			$mail_success = null;
		}

		$log_data = array(
			'settings'     => $this->settings,
			'mail_success' => $mail_success,
			'file'         => $backup_file,
			'output_file'  => $file,
			'size'         => @filesize( $file ),
		);

		if ( 'email' === $this->settings['method'] ) {
			@unlink( $file );
		} elseif ( $this->settings['retain'] > 0 ) {
			$files = scandir( $dir, 1 );

			if ( is_array( $files ) && count( $files ) > 0 ) {
				$count = 0;

				foreach ( $files as $file ) {
					if ( ! strstr( $file, 'backup' ) ) {
						continue;
					}

					if ( $count >= $this->settings['retain'] ) {
						@unlink( trailingslashit( $dir ) . $file );
					}

					$count ++;
				}
			}
		}

		if ( 'both' === $this->settings['method'] ) {
			if ( false === $mail_success ) {
				ITSEC_Log::add_warning( 'backup', 'email-failed-file-stored', $log_data );
			} else {
				ITSEC_Log::add_notice( 'backup', 'email-succeeded-file-stored', $log_data );
			}
		} elseif ( 'email' === $this->settings['method'] ) {
			if ( false === $mail_success ) {
				ITSEC_Log::add_error( 'backup', 'email-failed', $log_data );
			} else {
				ITSEC_Log::add_notice( 'backup', 'email-succeeded', $log_data );
			}
		} else {
			ITSEC_Log::add_notice( 'backup', 'file-stored', $log_data );
		}

		return $log_data;
	}

	private function send_mail( $file ) {

		$nc   = ITSEC_Core::get_notification_center();
		$mail = $nc->mail();

		$mail->add_header(
			esc_html__( 'Database Backup', 'better-wp-security' ),
			sprintf( esc_html__( 'Site Database Backup for %s', 'better-wp-security' ),
				'<b>' . date_i18n( get_option( 'date_format' ) ) . '</b>' ),
			false,
			esc_html__( 'Attached is the database backup file for your site.', 'better-wp-security' ),
		);

		$mail->add_section_heading( esc_html__( 'Website', 'better-wp-security' ) );
		$mail->add_text( $mail->get_display_url() );

		$mail->add_section_heading( esc_html__( 'Date', 'better-wp-security' ) );
		$mail->add_text( esc_html( date_i18n( get_option( 'date_format' ) ) ) );

		$mail->add_footer();


		$mail->set_recipients( $nc->get_recipients( 'backup' ) );

		$subject = $mail->prepend_site_url_to_subject( $nc->get_subject( 'backup' ) );
		$subject = apply_filters( 'itsec_backup_email_subject', $subject );
		$mail->set_subject( $subject, false );

		$mail->add_attachment( $file );

		return $nc->send( 'backup', $mail );
	}

	/**
	 * Add backup count to Site Health.
	 *
	 * @param array $info
	 *
	 * @return array
	 */
	public function add_site_health_info( $info ) {
		$method = ITSEC_Modules::get_setting( 'backup', 'method' );

		if ( 'email' === $method ) {
			if ( 'file' === ITSEC_Modules::get_setting( 'global', 'log_type' ) ) {
				return $info;
			}

			$count = ITSEC_Log::get_number_of_entries( [ 'module' => 'backup' ] );
		} else {
			$dir = trailingslashit( ITSEC_Modules::get_setting( 'backup', 'location' ) );

			if ( ! $dir || ! @file_exists( $dir ) ) {
				return $info;
			}

			$files = scandir( $dir, SCANDIR_SORT_DESCENDING );
			$files = array_unique( $files );

			$count = count( $files );
		}

		$info['solid-security']['fields']['total-backups'] = [
			'label' => __( 'Total Backups', 'better-wp-security' ),
			'value' => $count,
			'debug' => $count,
		];

		return $info;
	}

	/**
	 * Register the Backup notification email.
	 *
	 * @param array $notifications
	 *
	 * @return array
	 */
	public function register_notification( $notifications ) {

		$method = ITSEC_Modules::get_setting( 'backup', 'method' );

		if ( 'local' !== $method ) {
			$notifications['backup'] = array(
				'subject_editable' => true,
				'recipient'        => ITSEC_Notification_Center::R_EMAIL_LIST,
				'schedule'         => ITSEC_Notification_Center::S_NONE,
				'module'           => 'backup',
			);
		}

		return $notifications;
	}

	/**
	 * Register the strings for the Backup email.
	 *
	 * @return array
	 */
	public function notification_strings() {
		return array(
			'label'       => __( 'Database Backup', 'better-wp-security' ),
			'description' => sprintf(
				__( 'The %1$sDatabase Backup%2$s module will send a copy of any backups to the email addresses listed below.', 'better-wp-security' ),
				ITSEC_Core::get_link_for_settings_route( ITSEC_Core::get_settings_module_route( 'backup' ) ),
				'</a>'
			),
			'subject'     => __( 'Database Backup', 'better-wp-security' ),
		);
	}

}
