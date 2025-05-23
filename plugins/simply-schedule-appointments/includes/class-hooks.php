<?php
/**
 * Simply Schedule Appointments Hooks.
 *
 * @since   2.6.9
 * @package Simply_Schedule_Appointments
 */

/**
 * Simply Schedule Appointments Hooks.
 *
 * @since 2.6.9
 */
class SSA_Hooks {
	/**
	 * Parent plugin class.
	 *
	 * @since 2.6.9
	 *
	 * @var   Simply_Schedule_Appointments
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since  2.6.9
	 *
	 * @param  Simply_Schedule_Appointments $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  2.6.9
	 */
	public function hooks() {
		// maybe do_action( 'ssa/appointment/booked' ):
		add_action( 'ssa/appointment/after_insert', array( $this, 'maybe_do_appointment_booked_hook' ), 1000, 2 );
		add_action( 'ssa/appointment/after_insert', array( $this, 'maybe_do_appointment_pending_hook' ), 1000, 2 );
		add_action( 'ssa/appointment/after_update', array( $this, 'maybe_do_appointment_booked_hook' ), 1000, 4 );
		add_action( 'ssa/appointment_meta/after_insert', array( $this, 'maybe_do_appointment_no_show_hook' ), 1000, 2 );
		add_action( 'ssa/appointment_meta/after_insert', array( $this, 'maybe_do_appointment_no_show_removed_hook' ), 1000, 2 );

		// maybe do_action( 'ssa/appointment/customer_information_edited' );
		add_action( 'ssa/appointment/after_update', array( $this, 'maybe_do_appointment_customer_information_edited_hook' ), 10, 4 );
		add_action( 'ssa/appointment/after_update', array( $this, 'maybe_do_appointment_edited_hook' ), 10, 4 );
		add_action( 'ssa/appointment/after_update', array( $this, 'maybe_do_appointment_rescheduled_hook' ), 10, 4 );
		add_action( 'ssa/appointment/after_update', array( $this, 'maybe_do_appointment_abandoned_hook' ), 10, 4 );

		// maybe do_action( 'ssa/appointment/canceled' );
		add_action( 'ssa/appointment/after_update', array( $this, 'maybe_do_appointment_canceled_hook' ), 10, 4 );
	}

	/**
	 * Maybe do_action( 'ssa/appointment/no_show' )
	 *
	 * @param integer $appointment_meta_id
	 * @param array $data:
	 *									[appointment_id] =>
	 *									[meta_key] =>
	 *									[meta_value] =>
	 * @return void
	 */
	public function maybe_do_appointment_no_show_hook( $appointment_meta_id, $data ) {
		if ( empty( $appointment_meta_id ) ) {
			ssa_debug_log( 'ssa/appointment_meta/after_insert called with empty $appointment_meta_id', 10 );
			ssa_debug_log( $data, 10 );
			return;
		}

		if ( empty( $data['appointment_id'] ) ) {
			return;
		}
		if( empty( $data['meta_key'] ) ) {
			return;
		}
		if ( empty( $data['meta_value'] ) ) {
			return;
		}
		if ( 'status' !== $data['meta_key'] ) {
			return;
		}
		if ( 'no_show' !== $data['meta_value'] ) {
			return;
		}

		$appointment_array = $this->plugin->appointment_model->query( array(
			'id' => $data['appointment_id'],
		) );

		$data_after = array_shift( $appointment_array );
		if ( empty( $data_after ) ) {
			return;
		}
		
		do_action( 'ssa/appointment/no_show', $data['appointment_id'], $data_after, $data );
	}

	/**
	 * Maybe do_action( 'ssa/appointment/no_show_reverted' )
	 *
	 * @param integer $appointment_meta_id
	 * @param array $data:
	 *									[appointment_id] =>
	 *									[meta_key] =>
	 *									[meta_value] =>
	 * @return void
	 */
	public function maybe_do_appointment_no_show_removed_hook( $appointment_meta_id, $data ) {
		if ( empty( $appointment_meta_id ) ) {
			ssa_debug_log( 'ssa/appointment_meta/after_insert called with empty $appointment_meta_id', 10 );
			ssa_debug_log( $data, 10 );
			return;
		}

		if ( empty( $data['appointment_id'] ) ) {
			return;
		}
		if( empty( $data['meta_key'] ) ) {
			return;
		}
		if ( empty( $data['meta_value'] ) ) {
			return;
		}
		if ( 'status' !== $data['meta_key'] ) {
			return;
		}
		if ( 'no-show-reverted' !== $data['meta_value'] ) {
			return;
		}

		$appointment_array = $this->plugin->appointment_model->query( array(
			'id' => $data['appointment_id'],
		) );

		$data_after = array_shift( $appointment_array );
		if ( empty( $data_after ) ) {
			return;
		}
		
		do_action( 'ssa/appointment/no_show_reverted', $data['appointment_id'], $data_after, $data );
	}



	public function maybe_do_appointment_pending_hook ( $appointment_id, $data, $data_before = array(), $response = null ) {
		if ( empty( $appointment_id ) ) {
			ssa_debug_log( 'ssa/appointment/after_insert called with $appointment_id = 0', 10 );
			ssa_debug_log( $data, 10 );
			return;
		}

		if ( empty( $data ) || empty( $data['status'] ) ) {
			return;
		}

		if ( ! SSA_Appointment_Model::is_a_reserved_status( $data['status'] ) ) {
			return;
		}

		do_action( 'ssa/appointment/pending', $appointment_id, $data,  $data_before, $response );

	}

	public function maybe_do_appointment_booked_hook( $appointment_id, $data, $data_before = array(), $response = null ) {
		if ( empty( $appointment_id ) ) {
			ssa_debug_log( 'ssa/appointment/after_insert called with $appointment_id = 0', 10 );
			ssa_debug_log( $data, 10 );
			return;
		}

		if ( empty( $data['status'] ) || ! SSA_Appointment_Model::is_a_booked_status( $data['status'] ) ) {
			return;
		}

		if ( ! empty( $data_before['status'] ) && SSA_Appointment_Model::is_a_booked_status( $data_before['status'] ) ) {
			return;
		}

		// We have a newly booked appointment 
		// either brand new "booked" appointment
		// or a recently changed status – like "pending_payment" – to "booked"

		do_action( 'ssa/appointment/booked', $appointment_id, $data, $data_before, $response );
	}

	public function maybe_do_appointment_customer_information_edited_hook( $appointment_id, $data_after, $data_before, $response ) {
		if ( empty( $data_before['status'] ) || empty( $data_before['customer_information'] ) || empty( $data_after['customer_information'] ) ) {
			return; // we don't want to send a hook if we don't have the pieces we need
		}

		if ( json_encode( $data_before['customer_information'] ) === json_encode( $data_after['customer_information'] ) ) {
			return; // we don't want to send a customer_information_edited hook if anything besides customer information changed (eg. Google Calendar ID)
		}

		if ( SSA_Appointment_Model::is_a_canceled_status( $data_after['status'] ) && SSA_Appointment_Model::is_a_booked_status( $data_before['status'] ) ) {
			return; // we don't want to send a customer_information_edited hook when a canceled one will be sent instead (this should be redundant, but just in case)
		}
		
		do_action( 'ssa/appointment/customer_information_edited', $appointment_id, $data_after, $data_before, $response );
	}

	public function maybe_do_appointment_edited_hook( $appointment_id, $data_after, $data_before, $response ) {
		if ( empty( $data_before['status'] ) || empty( $data_after['status'] ) ) {
			return; // we don't want to send a hook if we don't have the pieces we need
		}

		if ( $data_before['status'] !== $data_after['status'] ) {
			return; // Bail if the status has been changed
		}

		$default_fields_to_watch = array(
			'appointment_type_id' => '',
			'author_id' => '',
			'customer_id' => '',
			'customer_information' => '',
			'title' => '',
			'description' => '',
			'payment_received' => '',
			'staff_ids' => '',
		);

		// Check if the fields to watch are set already in both data_after & data_before
		$fields_to_watch = array();
		foreach ($default_fields_to_watch as $key => $value) {
			if( isset( $data_after[ $key ] ) && isset( $data_before[ $key ] ) ) {
				$fields_to_watch[ $key ] = $value;
			}
		}

		$before_hash = md5( json_encode( shortcode_atts( $fields_to_watch, $data_before ) ) );
		$after_hash = md5( json_encode( shortcode_atts( $fields_to_watch, $data_after ) ) );

		if ( $before_hash == $after_hash ) {
			return; // nothing changed that we care about (this also eliminates an undesirable firing of "edited" when an appointment is first booked)
		}

		do_action( 'ssa/appointment/edited', $appointment_id, $data_after, $data_before, $response );
	}
	public function maybe_do_appointment_rescheduled_hook( $appointment_id, $data_after, $data_before, $response ) {
		
		if ( 'booked' !== $data_before['status'] ) {
			return; // Bail if the appointment has never been confirmed
		}
		
		if ( ! isset( $data_after['start_date'] ) ) {
			return;
		}
		
		if( $data_before['start_date'] === $data_after['start_date'] ){
			return; // here we only fire the hook if the selected time slot was changed
		}
		
		do_action('ssa/appointment/rescheduled', $appointment_id, $data_after, $data_before, $response );
	}

	public function maybe_do_appointment_canceled_hook( $appointment_id, $data_after, $data_before, $response ) {
		if ( empty( $data_before['status'] ) || empty( $data_after['status'] ) ) {
			return; // we don't want to send a hook if we don't have the pieces we need
		}

		if ( ! SSA_Appointment_Model::is_a_canceled_status( $data_after['status'] ) ) {
			return;
		}

		if ( ! SSA_Appointment_Model::is_a_booked_status( $data_before['status'] ) ) {
			return; // we only want to send a webhook when an appointment goes from booked -> canceled
		}
		
		do_action( 'ssa/appointment/canceled', $appointment_id, $data_after, $data_before, $response );
	}

	public function maybe_do_appointment_abandoned_hook( $appointment_id, $data_after, $data_before, $response ) {
		if ( empty( $appointment_id ) || empty( $data_before['status'] ) || empty( $data_after['status'] ) ) {
			return; // we don't want to send a hook if we don't have the pieces we need
		}

		if ( ! SSA_Appointment_Model::is_a_reserved_status( $data_before['status'] ) ) {
			if ( "abandoned" === $data_after['status'] ) {
				// something is wrong, we should never get here
				// allow the hook to fire, so we get a revision of what happened
			} else {
				return; // Since status before should be either pending_payment or pending_form
			}
		}
		
		if ( ! SSA_Appointment_Model::is_an_abandoned_status( $data_after['status'] ) ) {
			return;
		}

		do_action( 'ssa/appointment/abandoned', $appointment_id, $data_after, $data_before, $response );

	}

}
