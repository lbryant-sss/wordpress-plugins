<?php
/**
 * Custom runner system for recurring notifications.
 *
 * @since 7.14
 * @author Mircea Sandu
 * @package ExactMetrics
 */

/**
 * Class ExactMetrics_Notification_Event_Runner
 */
class ExactMetrics_Notification_Event_Runner {

	/**
	 * The instance of the current class.
	 *
	 * @var ExactMetrics_Notification_Event_Runner
	 */
	private static $instance;

	/**
	 * The static notifications registered.
	 *
	 * @var array
	 */
	private static $notifications = array();

	/**
	 * The key used to store in the options table the last run times for notifications.
	 *
	 * @var string
	 */
	private $last_run_key = 'exactmetrics_notifications_run';

	/**
	 * This will be populated on demand with the last run timestamps for all the notifications.
	 *
	 * @var array|bool
	 */
	private $last_run = array();

	/**
	 * Only update the option if something changed.
	 *
	 * @var bool
	 */
	private $changed = false;

	/**
	 * ExactMetrics_Notification_Event_Runner constructor.
	 */
	private function __construct() {
		add_action( 'wp_ajax_exactmetrics_vue_get_notifications', array( $this, 'maybe_add_notifications' ), 9 );
	}

	/**
	 * Get the singleton instance.
	 *
	 * @return ExactMetrics_Notification_Event_Runner
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get the stored option for the last run times.
	 *
	 * @return false|mixed|void
	 */
	public function get_notifications_last_run() {
		if ( empty( $this->last_run ) ) {
			$this->last_run = get_option( $this->last_run_key, array() );
		}

		return $this->last_run;
	}

	/**
	 * Update the last run time with a default of time.
	 *
	 * @param string $notification_id The notification id to update the last run time for.
	 * @param string|int $time The timestamp to store the last run time.
	 */
	public function update_last_run( $notification_id, $time = '' ) {
		if ( empty( $time ) ) {
			$time = time();
		}

		$this->last_run[ $notification_id ] = $time;
		$this->changed                      = true;
	}

	/**
	 * Update the option stored in the db with the last run times.
	 */
	public function save_last_runs() {
		if ( $this->changed ) {
			update_option( $this->last_run_key, $this->last_run, false );
		}
	}

	/**
	 * Loop through notifications and check if they should be added based on the time passed since they were last added.
	 */
	public function maybe_add_notifications() {

		if ( ! current_user_can( 'exactmetrics_view_dashboard' ) ) {
			// No need to try adding the notification if the user can't see it.
			return;
		}

		$notifications = $this->get_registered_notifications();
		$last_runs     = $this->get_notifications_last_run();

		$current_runs = 0;

		// Loop through registered notifications.
		foreach ( $notifications as $notification ) {
			/**
			 * The notification instance.
			 *
			 * @var ExactMetrics_Notification_Event $notification
			 */
			if ( empty( $last_runs[ $notification->notification_id ] ) ) {
				// If the notification never ran, save current time to show it after the interval.
				$this->update_last_run( $notification->notification_id );
			} else {
				// Has run before so let's check if enough days passed since the last run.
				$time_since = $last_runs[ $notification->notification_id ] + $notification->notification_interval * DAY_IN_SECONDS;
				$time_now   = time();
				if ( $time_since < $time_now ) {
					// Interval passed since it ran so let's add this one.

					$current_runs ++;
					$added_notification = $notification->add_notification();

					// Update the last run date as right now.
					$this->update_last_run( $notification->notification_id );

					// Avoid adding multiple notifications at the same time, and
					// also avoid running more than 5 notifications that returned
					// no data, otherwise this request would take too long
					if ( $added_notification || $current_runs > 5 ) {
						// Let's not add multiple notifications at the same time.
						break;
					}
				}
			}
		}

		// Update the option with the new times.
		$this->save_last_runs();

	}

	/**
	 * Get the static notifications array.
	 *
	 * @return array
	 */
	public function get_registered_notifications() {
		return self::$notifications;
	}

	/**
	 * Register the notification for running it later.
	 *
	 * @param ExactMetrics_Notification_Event $notification The instance of the notification.
	 */
	public function register_notification( $notification ) {

		$notification_id = isset( $notification->notification_id ) ? $notification->notification_id : false;
		if ( ! empty( $notification_id ) && ! isset( self::$notifications[ $notification_id ] ) ) {
			self::$notifications[ $notification_id ] = $notification;
		}

	}

	/**
	 * Delete the data on uninstall.
	 */
	public function delete_data() {
		delete_option( $this->last_run_key );
	}

}

/**
 * Get the single instance of the event runner class.
 *
 * @return ExactMetrics_Notification_Event_Runner
 */
function exactmetrics_notification_event_runner() {
	return ExactMetrics_Notification_Event_Runner::get_instance();
}
