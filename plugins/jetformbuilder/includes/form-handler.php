<?php

namespace Jet_Form_Builder;

use Jet_Form_Builder\Actions\Action_Handler;
use Jet_Form_Builder\Actions\Events\Default_Process\Default_Process_Event;
use Jet_Form_Builder\Actions\Events\Default_Required\Default_Required_Event;
use Jet_Form_Builder\Admin\Tabs_Handlers\Tab_Handler_Manager;
use Jet_Form_Builder\Admin\Tabs_Handlers\Options_Handler;
use Jet_Form_Builder\Classes\Tools;
use Jet_Form_Builder\Exceptions\Action_Exception;
use Jet_Form_Builder\Exceptions\Handler_Exception;
use Jet_Form_Builder\Exceptions\Not_Router_Request;
use Jet_Form_Builder\Exceptions\Repository_Exception;
use Jet_Form_Builder\Exceptions\Request_Exception;
use Jet_Form_Builder\Request\Form_Request_Router;
use Jet_Form_Builder\Request\Request_Handler;
use JFB_Modules\Rich_Content\Macros_Parser;
use JFB_Modules\Rich_Content\Module;
use JFB_Modules\Security\Exceptions\Spam_Exception;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * @property Macros_Parser parser
 *
 * Define Jet_Engine_Booking_Forms_Handler class
 */
class Form_Handler {

	public $hook_key          = 'jet_form_builder_submit';
	public $hook_val          = 'submit';
	public $form_data         = array();
	public $response_data     = array();
	public $is_ajax           = false;
	public $is_success        = false;
	public $response_args     = array();
	public $user_journey_data = array();

	public $form_id;
	public $refer;
	public $manager;

	/** @var Action_Handler */
	public $action_handler;

	public $form_key         = '_jet_engine_booking_form_id';
	public $refer_key        = '_jet_engine_refer';
	public $post_id_key      = '__queried_post_id';
	public $user_journey_key = '_user_journey';
	/**
	 * @var Request_Handler
	 */
	public $request_handler;


	/**
	 * Constructor for the class
	 */
	public function __construct() {
		$this->set_jfb_request_args();
		$this->action_handler  = new Action_Handler();
		$this->request_handler = new Request_Handler();
	}

	public function call_form() {
		try {
			Form_Request_Router::listen();
		} catch ( Not_Router_Request $exception ) {
			return;
		}

		add_action( 'jet-form-builder/request', array( $this, 'merge_request' ), 1 );

		if ( wp_doing_ajax() ) {
			add_action(
				'wp_ajax_' . $this->hook_key,
				array( $this, 'process_ajax_form' )
			);
			add_action(
				'wp_ajax_nopriv_' . $this->hook_key,
				array( $this, 'process_ajax_form' )
			);
		} else {
			add_action( 'wp_loaded', array( $this, 'process_form' ), 0 );
		}
	}

	public function core_fields() {
		return apply_filters(
			'jet-form-builder/form-handler/core-fields',
			array(
				$this->form_key    => array(
					'callback' => array( $this, 'set_form_id' ),
				),
				$this->refer_key   => array(
					'callback' => array( $this, 'set_referrer' ),
				),
				$this->post_id_key => array(
					'callback' => array( Tools::class, 'set_current_post' ),
				),
				$this->user_journey_key => array(
					'callback' => array( $this, 'set_user_journey_data' ),
				),
			)
		);
	}

	public function merge_request() {
		$fields = array(
			'__form_id' => $this->get_form_id(),
			'__refer'   => $this->get_referrer(),
			'__is_ajax' => $this->is_ajax(),
		);

		foreach ( $fields as $name => $value ) {
			jet_fb_context()->set_field_type( 'text-field', $name );
			jet_fb_context()->update_request( $value, $name );
			jet_fb_context()->make_secure( $name );
		}
	}

	public function set_user_journey_data( $data ) {
		$this->user_journey_data = $data;
	}

	public function get_user_journey_data() {
		return $this->user_journey_data;
	}


	public function set_referrer( $url ): Form_Handler {
		$refer = remove_query_arg(
			array( 'values', 'status', 'fields' ),
			esc_url_raw( $url )
		);

		$this->refer = wp_validate_redirect( $refer );

		return $this;
	}

	public function get_referrer() {
		return $this->refer;
	}

	public function set_form_id( $form_id ) {
		$this->form_id = absint( $form_id );

		return $this;
	}

	public function get_form_id() {
		return $this->form_id;
	}

	/**
	 * Is ajax form processing or not
	 *
	 * @return boolean [description]
	 */
	public function is_ajax(): bool {
		return $this->is_ajax;
	}

	/**
	 * Is form processing or not
	 *
	 * @return bool
	 */
	public function is_process(): bool {
		return ! empty( $this->refer );
	}

	/**
	 * Setup form data
	 *
	 * @return void [description]
	 */
	public function setup_form() {

		if ( $this->form_id ) {
			return;
		}

		$fields = $this->core_fields();

		foreach ( $fields as $field_name => $options ) {
			// phpcs:disable WordPress.Security
			if ( ! isset( $_POST[ $field_name ] ) ) {
				continue;
			}

			Tools::call( $options['callback'] ?? false, $_POST[ $field_name ] );
			// phpcs:enable WordPress.Security
		}
	}

	private function get_response_manager() {
		if ( ! $this->form_id ) {
			$this->setup_form();
		}

		if ( $this->is_ajax ) {
			jet_fb_action_handler()->set_form_id( $this->form_id );

			return new Form_Response\Types\Ajax_Response(
				array(
					'form_id' => $this->form_id,
					'actions' => jet_fb_action_handler()->get_all(),
				)
			);
		} else {
			return new Form_Response\Types\Reload_Response(
				array(
					'refer' => $this->refer,
				)
			);
		}
	}

	/**
	 * Process form with Ajax
	 *
	 * @return void
	 */
	public function process_ajax_form() {
		$this->is_ajax = true;
		$this->process_form();
	}

	/**
	 * Process current form
	 *
	 * @return void [description]
	 */
	public function process_form() {
		$this->setup_form();

		if ( ! $this->form_id || ! $this->refer ) {
			$this->add_response_data(
				array(
					'reload' => true,
				)
			);
			$this->set_response_args(
				array(
					'status' => 'failed',
				)
			);
			$this->send_raw_response();
		}

		$this->try_send();

		if ( true === $this->is_success ) {
			$this->send_response(
				array(
					'status' => 'success',
				)
			);
		} else {
			$this->send_response(
				array(
					'status' => 'failed',
				)
			);
		}
	}

	public function try_send() {
		try {
			$this->send_form();
		} catch ( Request_Exception $exception ) {
			$this->send_response(
				array(
					'status' => $exception->get_form_status(),
					'errors' => $exception->get_fields_errors(),
				)
			);
		} catch ( Action_Exception $exception ) {
			/**
			 * @see https://github.com/Crocoblock/jetformbuilder/issues/334
			 */
			$this->is_success = $exception->is_success();

			$this->send_response(
				array(
					'status' => $exception->get_form_status(),
				)
			);
		} catch ( Spam_Exception $exception ) {
			$this->send_response(
				array(
					'status' => $exception->get_form_status(),
				)
			);
		}
	}

	/**
	 * @throws Request_Exception|Action_Exception|Spam_Exception
	 */
	public function send_form() {
		$this->action_handler->set_form_id( $this->form_id );
		$this->request_handler->set_form_data();

		do_action( 'jet-form-builder/form-handler/before-send', $this );

		// execute all actions
		jet_fb_events()->execute( Default_Process_Event::class );

		$this->add_response_data( $this->action_handler->response_data );

		$this->is_success = true;
	}


	/**
	 * Add new properties into response data
	 *
	 * @param array $data [description]
	 */
	public function add_response_data( $data = array() ) {
		$this->response_data = array_merge( $this->response_data, $data );
	}

	public function set_response_args( $args ) {
		$this->response_args = array_merge( $this->response_args, $args );

		return $this;
	}

	public function get_response_args() {
		return $this->response_args;
	}

	/**
	 * Redirect back to refer
	 *
	 * @param array $args [description]
	 *
	 * @return void [description]
	 */
	public function send_response( $args = array() ) {
		try {
			$this->set_response_args( $args );

			jet_fb_events()->execute( Default_Required_Event::class );

			do_action( 'jet-form-builder/form-handler/after-send', $this, $this->is_success );
		} catch ( Repository_Exception $exception ) {
			$this->send_raw_response();

			return;
		} catch ( Handler_Exception $exception ) {
			$this->is_success    = false;
			$this->response_args = array(
				'status' => $exception->get_form_status(),
			);
			$this->response_data = array();

			$this->send_raw_response();

			return;
		}

		$this->send_raw_response();
	}

	public function send_raw_response() {
		( new Form_Response\Response(
			$this->get_response_manager(),
			$this->response_data
		) )->init( $this->response_args )->send();
	}

	/**
	 * Backward compatibility to deprecated properties
	 *
	 * @param $name
	 *
	 * @return mixed
	 * @throws Repository_Exception
	 */
	public function __get( $name ) {
		switch ( $name ) {
			case 'parser':
				/** @var Module $rich */
				$rich = jet_form_builder()->module( 'rich-content' );

				return $rich->get_parser();
			default:
				return null;
		}
	}

	public function set_jfb_request_args() {
		$options_handler = new Options_Handler();
		$options_handler->set_jfb_request_args();
		$options = Tab_Handler_Manager::get_options( 'options-tab' );
		if ( isset( $options['gfb_request_args_key'] ) ) {
			$this->hook_key = $options['gfb_request_args_key'];
		}
		if ( isset( $options['gfb_request_args_value'] ) ) {
			$this->hook_val = $options['gfb_request_args_value'];
		}
	}

}
