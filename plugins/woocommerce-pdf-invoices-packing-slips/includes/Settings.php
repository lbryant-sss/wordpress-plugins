<?php
namespace WPO\IPS;

use WPO\IPS\Documents\SequentialNumberStore;
use WPO\IPS\Settings\SettingsCallbacks;
use WPO\IPS\Settings\SettingsGeneral;
use WPO\IPS\Settings\SettingsDocuments;
use WPO\IPS\Settings\SettingsDebug;
use WPO\IPS\Settings\SettingsUbl;
use WPO\IPS\Settings\SettingsUpgrade;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( '\\WPO\\IPS\\Settings' ) ) :

class Settings {

	public $options_page_hook;
	public $callbacks;
	public $general;
	public $documents;
	public $debug;
	public $upgrade;
	public $ubl;
	public $general_settings;
	public $debug_settings;
	public $ubl_tax_settings;

	private $installed_templates       = array();
	private $installed_templates_cache = array();
	private $template_list_cache       = array();

	protected static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		$this->callbacks        = SettingsCallbacks::instance();
		$this->general          = SettingsGeneral::instance();
		$this->documents        = SettingsDocuments::instance();
		$this->debug            = SettingsDebug::instance();
		$this->ubl              = SettingsUbl::instance();
		$this->upgrade          = SettingsUpgrade::instance();

		$this->general_settings = get_option( 'wpo_wcpdf_settings_general' );
		$this->debug_settings   = get_option( 'wpo_wcpdf_settings_debug' );
		$this->ubl_tax_settings = get_option( 'wpo_wcpdf_settings_ubl_taxes' );

		// Settings menu item
		add_action( 'admin_menu', array( $this, 'menu' ), 999 ); // Add menu
		// Links on plugin page
		add_filter( 'plugin_action_links_'.WPO_WCPDF()->plugin_basename, array( $this, 'add_settings_link' ) );
		add_filter( 'plugin_row_meta', array( $this, 'add_support_links' ), 10, 2 );

		// settings capabilities
		add_filter( 'option_page_capability_wpo_wcpdf_general_settings', array( $this, 'user_settings_capability' ) );

		// AJAX set number store
		add_action( 'wp_ajax_wpo_wcpdf_set_next_number', array( $this, 'set_number_store' ) );

		// AJAX get header logo setting HTML
		add_action( 'wp_ajax_wpo_wcpdf_get_media_upload_setting_html', array( $this, 'get_media_upload_setting_html' ) );

		// refresh template path cache each time the general settings are updated
		add_action( "update_option_wpo_wcpdf_settings_general", array( $this, 'general_settings_updated' ), 10, 3 );
		// sets transient to flush rewrite rules
		add_action( "update_option_wpo_wcpdf_settings_debug", array( $this, 'debug_settings_updated' ), 10, 3 );
		add_action( 'init', array( $this, 'maybe_delete_flush_rewrite_rules_transient' ) );
		// migrate old template paths to template IDs before loading settings page
		add_action( 'wpo_wcpdf_settings_output_general', array( $this, 'maybe_migrate_template_paths' ), 9, 2 );

		// AJAX preview
		add_action( 'wp_ajax_wpo_wcpdf_preview', array( $this, 'ajax_preview' ) );
		// AJAX preview order search
		add_action( 'wp_ajax_wpo_wcpdf_preview_order_search', array( $this, 'preview_order_search' ) );

		// schedule yearly reset numbers
		add_action( 'wpo_wcpdf_schedule_yearly_reset_numbers', array( $this, 'yearly_reset_numbers' ) );

		// Apply categories to document settings.
		add_action( 'wpo_wcpdf_init_documents', array( $this, 'update_documents_settings_categories' ), 999 );

		// Apply categories to general settings.
		add_filter( 'wpo_wcpdf_settings_fields_general', array( $this, 'update_general_settings_categories' ), 999, 5 );

		// Sync address from WooCommerce address.
		add_action( 'wp_ajax_wpo_wcpdf_sync_address', array( $this, 'sync_shop_address_with_woo' ) );
	}

	public function menu() {
		$parent_slug = 'woocommerce';

		$this->options_page_hook = add_submenu_page(
			$parent_slug,
			esc_html__( 'PDF Invoices', 'woocommerce-pdf-invoices-packing-slips' ),
			esc_html__( 'PDF Invoices', 'woocommerce-pdf-invoices-packing-slips' ),
			$this->user_settings_capability(),
			'wpo_wcpdf_options_page',
			array( $this, 'settings_page' )
		);
	}

	/**
	 * Add settings link to plugins page
	 */
	public function add_settings_link( $links ) {
		$action_links = array(
			'settings' => '<a href="admin.php?page=wpo_wcpdf_options_page">'. esc_html__( 'Settings', 'woocommerce-pdf-invoices-packing-slips' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}

	/**
	 * Add various support links to plugin page
	 * after meta (version, authors, site)
	 */
	public function add_support_links( $links, $file ) {
		if ( $file == WPO_WCPDF()->plugin_basename ) {
			$row_meta = array(
				'docs'    => '<a href="https://docs.wpovernight.com/topic/woocommerce-pdf-invoices-packing-slips/" target="_blank" title="' . esc_html__( 'Documentation', 'woocommerce-pdf-invoices-packing-slips' ) . '">' . esc_html__( 'Documentation', 'woocommerce-pdf-invoices-packing-slips' ) . '</a>',
				'support' => '<a href="https://wordpress.org/support/plugin/woocommerce-pdf-invoices-packing-slips" target="_blank" title="' . esc_html__( 'Support Forum', 'woocommerce-pdf-invoices-packing-slips' ) . '">' . esc_html__( 'Support Forum', 'woocommerce-pdf-invoices-packing-slips' ) . '</a>',
			);

			return array_merge( $links, $row_meta );
		}
		return (array) $links;
	}

	/**
	 * Returns the first capability from a filterable list that the current user has access to.
	 * Falls back to the default capability if none match.
	 *
	 * @return string The matched or default user capability.
	 */
	public function user_settings_capability() {
		$manage_woocommerce = 'manage_woocommerce';

		// Get the default capability
		$default_capability = apply_filters( 'wpo_wcpdf_settings_default_user_capability', $manage_woocommerce );
		$default_capability = ( empty( $default_capability ) || ! is_string( $default_capability ) ) ? $manage_woocommerce : $default_capability;

		// Get the list of capabilities
		$capabilities = (array) apply_filters( 'wpo_wcpdf_settings_user_role_capabilities', array( $default_capability ) );

		// Loop through the list
		foreach ( $capabilities as $capability ) {
			if ( is_string( $capability ) && current_user_can( $capability ) ) {
				return $capability;
			}
		}

		// Fallback
		return ! empty( $default_capability ) ? $default_capability : $manage_woocommerce;
	}

	/**
	 * Check if user role can manage settings.
	 * @return bool
	 */
	public function user_can_manage_settings() {
		return current_user_can( $this->user_settings_capability() );
	}

	public function settings_page() {
		// feedback on settings save
		settings_errors();

		$settings_tabs = apply_filters( 'wpo_wcpdf_settings_tabs', array(
			'general' => array(
				'title'          => __( 'General', 'woocommerce-pdf-invoices-packing-slips' ),
				'preview_states' => 3,
			),
			'documents' => array(
				'title'          => __( 'Documents', 'woocommerce-pdf-invoices-packing-slips' ),
				'preview_states' => 3,
			),
		) );

		$settings_tabs['ubl'] = array(
			'title'          => __( 'Taxes', 'woocommerce-pdf-invoices-packing-slips' ),
			'preview_states' => 1,
			//'beta'           => true,
		);

		// add status and upgrade tabs last in row
		$settings_tabs['debug'] = array(
			'title'          => __( 'Advanced', 'woocommerce-pdf-invoices-packing-slips' ),
			'preview_states' => 1,
		);

		$settings_tabs['upgrade'] = array(
			'title'          => __( 'Upgrade', 'woocommerce-pdf-invoices-packing-slips' ),
			'preview_states' => 1,
		);

		$settings_tabs = $this->maybe_disable_preview_on_settings_tabs( $settings_tabs ); // disable preview on debug setting
		$default_tab   = apply_filters( 'wpo_wcpdf_settings_tabs_default', ! empty( $settings_tabs['general'] ) ? 'general' : key( $settings_tabs ) );
		$nonce         = wp_create_nonce( 'wp_wcpdf_settings_page_nonce' );

		include WPO_WCPDF()->plugin_path() . '/views/settings-page.php';
	}

	public function maybe_disable_preview_on_settings_tabs( $settings_tabs ) {
		$debug_settings = get_option( 'wpo_wcpdf_settings_debug', array() );
		$close_preview  = isset( $debug_settings['disable_preview'] );

		if ( $close_preview ) {
			foreach ( $settings_tabs as $tab_key => &$tab ) {
				if ( is_array( $tab ) && ! empty( $tab['preview_states'] ) ) {
					$tab['preview_states'] = 1;
				}
			}
		}

		return $settings_tabs;
	}

	public function ajax_preview() {
		check_ajax_referer( 'wpo_wcpdf_preview', 'security' );

		try {
			// check permissions
			if ( ! $this->user_can_manage_settings() ) {
				throw new \Exception( esc_html__( 'You do not have sufficient permissions to access this page.', 'woocommerce-pdf-invoices-packing-slips' ), 403 );
			}

			// get document type
			if ( ! empty( $_POST['document_type'] ) ) {
				$document_type = sanitize_text_field( wp_unslash( $_POST['document_type'] ) );
			} else {
				$document_type = 'invoice';
			}

			// get order ID
			if ( ! empty( $_POST['order_id'] ) ) {
				$order_id = sanitize_text_field( wp_unslash( $_POST['order_id'] ) );

				if ( $document_type == 'credit-note' ) {
					// get last refund ID of the order if available
					$refund = wc_get_orders(
						array(
							'type'   => 'shop_order_refund',
							'parent' => $order_id,
							'limit'  => 1,
						)
					);
					$order_id = ! empty( $refund ) ? $refund[0]->get_id() : $order_id;
				}
			} else {
				// default to last order
				$default_order_id = wc_get_orders( apply_filters( 'wpo_wcpdf_preview_default_order_id_query_args', array(
					'limit'  => 1,
					'return' => 'ids',
					'type'   => 'shop_order',
				), $document_type ) );
				$order_id = apply_filters( 'wpo_wcpdf_preview_default_order_id', ! empty( $default_order_id ) ? reset( $default_order_id ) : false );
			}

			// get PDF data for preview
			if ( $order_id ) {
				$order = apply_filters( 'wpo_wcpdf_preview_order_object', wc_get_order( $order_id ), $order_id, $document_type );

				if ( empty( $order ) ) {
					wp_send_json_error( array( 'error' => esc_html__( 'Order not found!', 'woocommerce-pdf-invoices-packing-slips' ) ) );
				}
				if ( ! in_array( $order->get_type(), array( 'shop_order', 'shop_order_refund' ) ) ) {
					wp_send_json_error( array( 'error' => esc_html__( 'Object found is not an order!', 'woocommerce-pdf-invoices-packing-slips' ) ) );
				}

				// process settings data
				if ( ! empty( $_POST['data'] ) ) {
					// parse form data
					parse_str( $_POST['data'], $form_data ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					$form_data = stripslashes_deep( $form_data );

					foreach ( $form_data as $option_key => $form_settings ) {
						if ( ! empty( $option_key ) && false === apply_filters( 'wpo_wcpdf_preview_filter_option', 0 === strpos( $option_key, 'wpo_wcpdf' ), $option_key ) ) {
							continue; // not our business
						}

						// validate option values
						$form_settings = $this->callbacks->validate( $form_settings );

						// filter the options
						add_filter( "option_{$option_key}", function( $value, $option ) use ( $form_settings ) {
							return maybe_unserialize( $form_settings );
						}, 99, 2 );
					}

					// reload settings
					$this->general_settings = get_option( 'wpo_wcpdf_settings_general' );
					$this->debug_settings   = get_option( 'wpo_wcpdf_settings_debug' );

					do_action( 'wpo_wcpdf_preview_after_reload_settings' );
				}

				$document = wcpdf_get_document( $document_type, $order );

				if ( $document ) {
					if ( ! $document->exists() ) {
						$document->set_date( current_time( 'timestamp', true ) );
						$number_store_method = $this->get_sequential_number_store_method();
						$number_store_name   = apply_filters( 'wpo_wcpdf_document_sequential_number_store', "{$document->slug}_number", $document );
						$number_store        = new SequentialNumberStore( $number_store_name, $number_store_method );
						$document->set_number( $number_store->get_next() );
					}

					// Update document date.
					$document->initiate_date();

					// Update document number only if it is not already set.
					if ( empty( $document->get_number() ) ) {
						$document_number = $document->get_document_number();
						if ( ! empty( $document_number ) ) {
							$document->set_number( $document_number );
						}
					}

					$document_number = $document->get_number( $document->get_type() );

					// Apply document number formatting.
					if ( $document_number ) {
						if ( ! empty( $document->settings['number_format'] ) && is_array( $document->settings['number_format'] ) ) {
							$document_number->load_number_data( $document->settings['number_format'] );
						}

						$document_number->apply_formatting( $document, $order );
					}

					// preview
					$output_format = ( ! empty( $_REQUEST['output_format'] ) && $_REQUEST['output_format'] != 'pdf' && in_array( $_REQUEST['output_format'], $document->output_formats ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['output_format'] ) ) : 'pdf';
					switch ( $output_format ) {
						default:
						case 'pdf':
							$preview_data = base64_encode( $document->preview_pdf() );
							break;
						case 'ubl':
							$preview_data = $document->preview_ubl();
							break;
					}

					wp_send_json_success( array(
						'preview_data'  => $preview_data,
						'output_format' => $output_format,
					) );
				} else {
					wp_send_json_error(
						array(
							'error' => sprintf(
								/* translators: order ID */
								esc_html__( 'Document not available for order #%s, try selecting a different order.', 'woocommerce-pdf-invoices-packing-slips' ),
								$order_id
							)
						)
					);
				}
			} else {
				wp_send_json_error( array( 'error' => esc_html__( 'No WooCommerce orders found! Please consider adding your first order to see this preview.', 'woocommerce-pdf-invoices-packing-slips' ) ) );
			}

		} catch ( \Throwable $th ) {
			wcpdf_log_error( 'Error trying to generate document: ' . $th->getTraceAsString(), 'critical' );

			wp_send_json_error(
				array(
					'error' => sprintf(
						/* translators: error message */
						esc_html__( 'Error trying to generate document: %s', 'woocommerce-pdf-invoices-packing-slips' ),
						$th->getMessage()
					)
				)
			);
		}

		wp_die();
	}

	public function preview_order_search() {
		check_ajax_referer( 'wpo_wcpdf_preview', 'security' );

		try {
			// check permissions
			if ( ! $this->user_can_manage_settings() ) {
				throw new \Exception( esc_html__( 'You do not have sufficient permissions to access this page.', 'woocommerce-pdf-invoices-packing-slips' ), 403 );
			}

			if ( ! empty( $_POST['search'] ) && ! empty( $_POST['document_type'] ) ) {
				$search        = sanitize_text_field( wp_unslash( $_POST['search'] ) );
				$document_type = sanitize_text_field( wp_unslash( $_POST['document_type'] ) );
				$results       = array();

				// we have an order ID
				if ( is_numeric( $search ) && wc_get_order( $search ) ) {
					$results = [ $search ];

				// no order ID, let's try with customer
				} else {
					$default_args = apply_filters( 'wpo_wcpdf_preview_order_search_args', array(
						'type'     => 'shop_order',
						'limit'    => 10,
						'orderby'  => 'date',
						'order'    => 'DESC',
						'return'   => 'ids',
					), $document_type );

					// search by email
					if ( is_email( $search ) ) {
						$args    = array( 'customer' => $search );
						$args    = $args + $default_args;
						$results = wc_get_orders( $args );

					// search by names
					} else {
						$names = array( 'billing_first_name', 'billing_last_name', 'billing_company' );
						foreach ( $names as $name ) {
							$args    = array( $name => $search );
							$args    = $args + $default_args;
							$results = wc_get_orders( $args );
							if ( count( $results ) > 0 ) {
								break;
							}
						}
					}
				}

				// filter results
				$results = apply_filters( 'wpo_wcpdf_preview_order_search_results', $results, $search, $document_type );

				// if we got here we have results!
				if ( ! empty( $results ) ) {
					$data = array();
					foreach ( $results as $value ) {
						$order = wc_get_order( $value );
						if ( empty( $order ) ) {
							continue;
						}
						$order_id                              = is_callable( array( $order, 'get_id' ) ) ? $order->get_id() : 0;
						$data[$order_id]['order_number']       = is_callable( array( $order, 'get_order_number' ) ) ? $order->get_order_number() : '';
						$data[$order_id]['billing_first_name'] = is_callable( array( $order, 'get_billing_first_name' ) ) ? wpo_wcpdf_sanitize_html_content( $order->get_billing_first_name(), 'first_name' ) : '';
						$data[$order_id]['billing_last_name']  = is_callable( array( $order, 'get_billing_last_name' ) ) ? wpo_wcpdf_sanitize_html_content( $order->get_billing_last_name(), 'last_name' ) : '';
						$data[$order_id]['billing_company']    = is_callable( array( $order, 'get_billing_company' ) ) ? wpo_wcpdf_sanitize_html_content( $order->get_billing_company(), 'company' ) : '';
						$data[$order_id]['date_created']       = is_callable( array( $order, 'get_date_created' ) ) ? '<strong>' . esc_attr__( 'Date', 'woocommerce-pdf-invoices-packing-slips' ) . ':</strong> ' . $order->get_date_created()->format( 'Y/m/d' ) : '';
						$data[$order_id]['total']              = is_callable( array( $order, 'get_total' ) ) ? '<strong>' . esc_attr__( 'Total', 'woocommerce-pdf-invoices-packing-slips' ) . ':</strong> ' . wc_price( $order->get_total() ) : '';
					}

					$data = apply_filters( 'wpo_wcpdf_preview_order_search_data', $data, $results );

					wp_send_json_success( $data );
				} else {
					wp_send_json_error( array( 'error' => esc_html__( 'No order(s) found!', 'woocommerce-pdf-invoices-packing-slips' ) ) );
				}
			} else {
				wp_send_json_error( array( 'error' => esc_html__( 'An error occurred when trying to process your request!', 'woocommerce-pdf-invoices-packing-slips' ) ) );
			}
		} catch ( \Throwable $th ) {
			wp_send_json_error(
				array(
					'error' => sprintf(
						/* translators: error message */
						esc_html__( 'Error trying to get orders: %s', 'woocommerce-pdf-invoices-packing-slips' ),
						$th->getMessage()
					)
				)
			);
		}

		wp_die();
	}

	public function add_settings_fields( $settings_fields, $page, $option_group, $option_name ) {
		foreach ( $settings_fields as $settings_field ) {
			if ( ! isset( $settings_field['callback'] ) ) {
				continue;
			} elseif ( is_callable( array( $this->callbacks, $settings_field['callback'] ) ) ) {
				$callback = array( $this->callbacks, $settings_field['callback'] );
			} elseif ( is_callable( $settings_field['callback'] ) ) {
				$callback = $settings_field['callback'];
			} else {
				continue;
			}

			if ( $settings_field['type'] == 'section' ) {
				add_settings_section(
					$settings_field['id'],
					$settings_field['title'],
					$callback,
					$page,
					$settings_field['args'] ?? array()
				);
			} else {
				add_settings_field(
					$settings_field['id'],
					$settings_field['title'],
					$callback,
					$page,
					$settings_field['section'],
					$settings_field['args']
				);
				// register option separately for singular options
				if ( is_string( $settings_field['callback'] ) && $settings_field['callback'] == 'singular_text_element') {
					register_setting( $option_group, $settings_field['args']['option_name'], array( $this->callbacks, 'validate' ) ); // phpcs:ignore PluginCheck.CodeAnalysis.SettingSanitization.register_settingDynamic
				}
			}
		}
		// $page, $option_group & $option_name are all the same...
		register_setting( $option_group, $option_name, array( $this->callbacks, 'validate' ) ); // phpcs:ignore PluginCheck.CodeAnalysis.SettingSanitization.register_settingDynamic
		add_filter( 'option_page_capability_'.$page, array( $this, 'user_settings_capability' ) );

	}

	/**
	 * Get document general settings.
	 *
	 * @return array
	 */
	public function get_common_document_settings(): array {
		return array(
			'paper_size'              => $this->general_settings['paper_size'] ?? '',
			'font_subsetting'         => isset( $this->general_settings['font_subsetting'] ) || ( defined( "DOMPDF_ENABLE_FONTSUBSETTING" ) && DOMPDF_ENABLE_FONTSUBSETTING === true ),
			'header_logo'             => $this->general_settings['header_logo'] ?? '',
			'header_logo_height'      => $this->general_settings['header_logo_height'] ?? '',
			'vat_number'              => $this->general_settings['vat_number'] ?? '',
			'coc_number'              => $this->general_settings['coc_number'] ?? '',
			'shop_name'               => $this->general_settings['shop_name'] ?? '',
			'shop_phone_number'       => $this->general_settings['shop_phone_number'] ?? '',
			'shop_email_address'      => $this->general_settings['shop_email_address'] ?? '',
			'shop_address_line_1'     => $this->general_settings['shop_address_line_1'] ?? '',
			'shop_address_line_2'     => $this->general_settings['shop_address_line_2'] ?? '',
			'shop_address_country'    => $this->general_settings['shop_address_country'] ?? '',
			'shop_address_state'      => $this->general_settings['shop_address_state'] ?? '',
			'shop_address_city'       => $this->general_settings['shop_address_city'] ?? '',
			'shop_address_postcode'   => $this->general_settings['shop_address_postcode'] ?? '',
			'shop_address_additional' => $this->general_settings['shop_address_additional'] ?? '',
			'footer'                  => $this->general_settings['footer'] ?? '',
			'extra_1'                 => $this->general_settings['extra_1'] ?? '',
			'extra_2'                 => $this->general_settings['extra_2'] ?? '',
			'extra_3'                 => $this->general_settings['extra_3'] ?? '',
		);
	}

	public function get_document_settings( $document_type, $output_format = 'pdf' ) {
		if ( ! empty( $document_type ) ) {
			$option_name = ( 'pdf' === $output_format ) ? "wpo_wcpdf_documents_settings_{$document_type}" : "wpo_wcpdf_documents_settings_{$document_type}_{$output_format}";
			return get_option( $option_name, array() );
		} else {
			return false;
		}
	}

	public function get_output_format( $document = null, $request = null ) {
		$output_format = 'pdf'; // default

		if ( isset( $this->debug_settings['html_output'] ) || ( isset( $request['output'] ) && 'html' === $request['output'] ) ) {
			$output_format = 'html';
		} elseif ( isset( $request['output'] ) && ! empty( $request['output'] ) && ! empty( $document ) && in_array( $request['output'], $document->output_formats ) ) {
			$document_settings = $this->get_document_settings( $document->get_type(), esc_attr( $request['output'] ) );
			if ( isset( $document_settings['enabled'] ) ) {
				$output_format = esc_attr( $request['output'] );
			}
		}

		return apply_filters( 'wpo_wcpdf_output_format', $output_format, $document );
	}

	public function get_output_mode() {
		if ( isset( $this->general_settings['download_display'] ) ) {
			switch ( $this->general_settings['download_display'] ) {
				case 'display':
					$output_mode = 'inline';
					break;
				case 'download':
				default:
					$output_mode = 'download';
					break;
			}
		} else {
			$output_mode = 'download';
		}
		return $output_mode;
	}

	public function get_template_path() {
		// return default path if no template selected
		if ( empty( $this->general_settings['template_path'] ) ) {
			return wp_normalize_path( WPO_WCPDF()->plugin_path() . '/templates/Simple' );
		}

		$installed_templates = $this->get_installed_templates();
		$selected_template = $this->general_settings['template_path'];
		if ( in_array( $selected_template, $installed_templates ) ) {
			return array_search( $selected_template, $installed_templates );
		} else {
			// unknown template or full template path (filter override)
			$template_path = wp_normalize_path( $selected_template );

			// add base path, checking if it's not already there
			// alternative setups like Bedrock have WP_CONTENT_DIR & ABSPATH separated
			if ( defined( 'WP_CONTENT_DIR' ) && ! empty( WP_CONTENT_DIR ) && false !== strpos( WP_CONTENT_DIR, ABSPATH ) ) {
				$base_path = wp_normalize_path( ABSPATH );
			} else {
				$base_path = wp_normalize_path( WP_CONTENT_DIR );
			}

			if ( ! empty( $template_path ) && false === strpos( $template_path, $base_path ) ) {
				$template_path = wp_normalize_path( $base_path . $template_path );
			}
		}

		return $template_path;
	}

	public function get_installed_templates( $force_reload = false ) {
		// because this method can be called (too) early we load from a cached list in those cases
		// this cache is updated each time the template settings are saved/updated
		if ( ! did_action( 'wpo_wcpdf_init_documents' ) && ( $cached_template_list = $this->get_template_list_cache() ) ) {
			return $cached_template_list;
		}

		// to save resources on the disk operations we only do this once
		if ( $force_reload === false && ! empty ( $this->installed_templates ) ) {
			return $this->installed_templates;
		}

		$installed_templates = array();
		// get base paths
		$template_base_path  = ( function_exists( 'WC' ) && is_callable( array( WC(), 'template_path' ) ) ) ? WC()->template_path() : apply_filters( 'woocommerce_template_path', 'woocommerce/' );
		$template_base_path  = untrailingslashit( $template_base_path );
		$template_paths      = array (
			// note the order: theme before child-theme, so that child theme is always preferred (overwritten)
			'default'     => WPO_WCPDF()->plugin_path() . '/templates/',
			'theme'       => get_template_directory() . "/{$template_base_path}/pdf/",
			'child-theme' => get_stylesheet_directory() . "/{$template_base_path}/pdf/",
		);

		$template_paths = apply_filters( 'wpo_wcpdf_template_paths', $template_paths );

		foreach ( $template_paths as $template_source => $template_path ) {
			$dirs = (array) glob( $template_path . '*' , GLOB_ONLYDIR );

			foreach ( $dirs as $dir ) {
				$clean_dir     = wp_normalize_path( $dir );
				$template_name = basename( $clean_dir );
				// let child theme override parent theme
				$group = ( $template_source == 'child-theme' ) ? 'theme' : $template_source;
				$installed_templates[ $clean_dir ] = "{$group}/{$template_name}" ;
			}
		}

		if ( empty( $installed_templates ) ) {
			// fallback to Simple template for servers with glob() disabled
			$simple_template_path = wp_normalize_path( $template_paths['default'] . 'Simple' );
			$installed_templates[$simple_template_path] = 'default/Simple';
		}

		$installed_templates = apply_filters( 'wpo_wcpdf_installed_templates', $installed_templates );

		$this->installed_templates = $installed_templates;

		if ( ! empty( $this->template_list_cache ) && array_diff_assoc( $this->template_list_cache, $this->installed_templates ) ) {
			$this->set_template_list_cache( $this->installed_templates );
		}

		return $installed_templates;
	}

	public function get_template_list_cache() {
		$template_list = get_option( 'wpo_wcpdf_installed_template_paths', array() );
		if ( ! empty( $template_list ) ) {
			$checked_list = array();
			$outdated = false;
			// cache could be outdated, so we check whether the folders exist
			foreach ( $template_list as $path => $template_id ) {
				if ( WPO_WCPDF()->file_system->is_dir( $path ) ) {
					$checked_list[$path] = $template_id; // folder exists
					continue;
				}

				$outdated = true;
				// folder does not exist, try replacing base if we can locate wp-content
				$wp_content_folder = 'wp-content';
				if ( ! empty( $path ) && false !== strpos( $path, $wp_content_folder ) && defined( WP_CONTENT_DIR ) ) {
					// try wp-content
					$relative_path = substr( $path, strrpos( $path, $wp_content_folder ) + strlen( $wp_content_folder ) );
					$new_path = WP_CONTENT_DIR . $relative_path;
					if ( WPO_WCPDF()->file_system->is_dir( $new_path ) ) {
						$checked_list[$new_path] = $template_id;
					}
				}
			}

			if ( $outdated ) {
				$this->set_template_list_cache( $checked_list );
			}

			$this->installed_templates_cache = $checked_list;

			return $checked_list;
		} else {
			return array();
		}
	}

	public function set_template_list_cache( $template_list ) {
		$this->template_list_cache = $template_list;
		update_option( 'wpo_wcpdf_installed_template_paths', $template_list );
	}

	public function delete_template_list_cache() {
		delete_option( 'wpo_wcpdf_installed_template_paths' );
	}

	public function general_settings_updated( $old_settings, $settings, $option ) {
		if ( is_array( $settings ) && ! empty ( $settings['template_path'] ) ) {
			$this->delete_template_list_cache();
			$this->set_template_list_cache( $this->get_installed_templates() );
		}
	}

	public function debug_settings_updated( $old_settings, $settings, $option ) {
		if ( is_array( $settings ) && is_array( $old_settings ) && empty( $old_settings['pretty_document_links'] ) && ! empty ( $settings['pretty_document_links'] ) ) {
			set_transient( 'wpo_wcpdf_flush_rewrite_rules', 'yes', HOUR_IN_SECONDS );
		}
	}

	public function maybe_delete_flush_rewrite_rules_transient() {
		if ( get_transient( 'wpo_wcpdf_flush_rewrite_rules' ) ) {
			flush_rewrite_rules();
			delete_transient( 'wpo_wcpdf_flush_rewrite_rules' );
		}
	}

	public function get_relative_template_path( $absolute_path ) {
		if ( empty( $absolute_path ) ) {
			return '';
		}

		if ( defined( 'WP_CONTENT_DIR' ) && ! empty( WP_CONTENT_DIR ) && false !== strpos( WP_CONTENT_DIR, ABSPATH ) ) {
			$base_path = wp_normalize_path( ABSPATH );
		} else {
			$base_path = wp_normalize_path( WP_CONTENT_DIR );
		}

		return str_replace( $base_path, '', wp_normalize_path( $absolute_path ) );
	}

	public function maybe_migrate_template_paths( $settings_section = null, $nonce = null ) {
		if ( ! wp_verify_nonce( $nonce, 'wp_wcpdf_settings_page_nonce' ) ) {
			return;
		}

		// bail if no template is selected yet (fresh install)
		if ( empty( $this->general_settings['template_path'] ) ) {
			return;
		}

		$installed_templates = $this->get_installed_templates( true );
		$selected_template = wp_normalize_path( $this->general_settings['template_path'] );
		$template_match = '';
		if ( ! in_array( $selected_template, $installed_templates ) && substr_count( $selected_template, '/' ) > 1 ) {
			// search for path match
			foreach ( $installed_templates as $path => $template_id ) {
				$path = wp_normalize_path( $path );
				// check if the last part of the path matches
				if ( substr( $path, -strlen( $selected_template ) ) === $selected_template ) {
					$template_match = $template_id;
					break;
				}
			}

			// fallback to template name if no path match
			if ( empty( $template_match ) ) {
				$template_ids = array_flip( array_unique( array_combine( $installed_templates, array_map( 'basename', $installed_templates ) ) ) );
				$template_name = basename( $selected_template );
				if ( ! empty ( $template_ids[$template_name] ) ) {
					$template_match = $template_ids[$template_name];
				}
			}

			// migrate setting if we have a match
			if ( ! empty( $template_match ) ) {
				$this->general_settings['template_path'] = $template_match;
				update_option( 'wpo_wcpdf_settings_general', $this->general_settings );
				/* translators: 1. path, 2. template ID */
				wcpdf_log_error( sprintf( 'Template setting migrated from %1$s to %2$s', $path, $template_id ), 'info' );
			}
		}
	}

	public function set_number_store() {
		$store = ! empty( $_POST['store'] ) ? sanitize_text_field( wp_unslash( $_POST['store'] ) ) : '';

		check_ajax_referer( "wpo_wcpdf_next_{$store}", 'security' );

		// check permissions
		if ( ! $this->user_can_manage_settings() ) {
			die();
		}

		$number = ! empty( $_POST['number'] ) ? (int) $_POST['number'] : 0;
		if ( $number > 0 ) {
			$number_store_method = $this->get_sequential_number_store_method();
			$number_store = new SequentialNumberStore( $store, $number_store_method );
			$number_store->set_next( $number );
			echo wp_kses_post( "next number ({$store}) set to {$number}" );
		}
		die();
	}

	public function get_sequential_number_store_method() {
		global $wpdb;
		$method = isset( $this->debug_settings['calculate_document_numbers'] ) ? 'calculate' : 'auto_increment';

		// safety first - always use calculate when auto_increment_increment is not 1
		$row = $wpdb->get_row( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
			"SHOW VARIABLES LIKE 'auto_increment_increment'"
		);

		if ( ! empty( $row ) && ! empty( $row->Value ) && $row->Value != 1 ) {
			$method = 'calculate';
		}

		return $method;
	}

	public function schedule_yearly_reset_numbers() {
		if ( ! $this->maybe_schedule_yearly_reset_numbers() ) {
			return;
		}

		// checks AS functions existence
		if ( ! function_exists( '\\as_schedule_single_action' ) || ! function_exists( '\\as_get_scheduled_actions' ) ) {
			wcpdf_log_error( 'Action Scheduler functions not available. Cannot schedule yearly document number reset.', 'critical' );
			return;
		}

		$next_year = strval( intval( current_time( 'Y' ) ) + 1 );
		$datetime  = new \WC_DateTime( "{$next_year}-01-01 00:00:01", new \DateTimeZone( wc_timezone_string() ) );
		$semaphore = new Semaphore( 'schedule_yearly_reset_numbers' );
		$hook      = 'wpo_wcpdf_schedule_yearly_reset_numbers';

		// checks if there are pending actions
		$scheduled_actions = count( \as_get_scheduled_actions( array(
			'hook'   => $hook,
			'status' => \ActionScheduler_Store::STATUS_PENDING,
		) ) );

		// if no concurrent actions sets the action
		if ( $scheduled_actions < 1 ) {

			if ( $semaphore->lock() ) {

				$semaphore->log( 'Lock acquired for yearly reset numbers schedule.', 'info' );

				try {
					$action_id = \as_schedule_single_action( $datetime->getTimestamp(), $hook );
					if ( ! empty( $action_id ) ) {
						wcpdf_log_error(
							"Yearly document numbers reset scheduled with the action id: {$action_id}",
							'info'
						);
					} else {
						wcpdf_log_error(
							'The yearly document numbers reset action schedule failed!',
							'critical'
						);
					}
				} catch ( \Exception $e ) {
					$semaphore->log( $e, 'critical' );
				} catch ( \Error $e ) {
					$semaphore->log( $e, 'critical' );
				}

				if ( $semaphore->release() ) {
					$semaphore->log( 'Lock released for yearly reset numbers schedule.', 'info' );
				}

			} else {
				$semaphore->log( 'Couldn\'t get the lock for yearly reset numbers schedule.', 'critical' );
			}

		} else {
			wcpdf_log_error(
				"Number of concurrent yearly document numbers reset actions found: {$scheduled_actions}",
				'error'
			);

			if ( function_exists( '\\as_unschedule_all_actions' ) ) {
				\as_unschedule_all_actions( $hook );
			} else {
				wcpdf_log_error( 'Action Scheduler functions not available. Cannot unschedule yearly document number reset.', 'critical' );
			}

			// reschedule
			$this->schedule_yearly_reset_numbers();
		}
	}

	public function yearly_reset_numbers() {
		$semaphore = new Semaphore( 'yearly_reset_numbers' );

		if ( $semaphore->lock() ) {

			$semaphore->log( 'Lock acquired for yearly reset numbers.', 'info' );

			try {
				// reset numbers
				$documents     = WPO_WCPDF()->documents->get_documents( 'all' );
				$number_stores = array();
				foreach ( $documents as $document ) {
					if ( is_callable( array( $document, 'get_sequential_number_store' ) ) ) {
						$number_stores[$document->get_type()] = $document->get_sequential_number_store();
					}
				}

				// log reset number events
				if ( ! empty( $number_stores ) ) {
					foreach( $number_stores as $document_type => $number_store ) {
						if ( $number_store->get_next() === 1 ) {
							wcpdf_log_error(
								"Yearly number reset succeed for '{$document_type}' with database table name: {$number_store->table_name}",
								'info'
							);
						} else {
							wcpdf_log_error(
								"An error occurred while trying to reset yearly number for '{$document_type}' with database table name: {$number_store->table_name}",
								'error'
							);
						}
					}
				}
			} catch ( \Exception $e ) {
				$semaphore->log( $e, 'critical' );
			} catch ( \Error $e ) {
				$semaphore->log( $e, 'critical' );
			}

			if ( $semaphore->release() ) {
				$semaphore->log( 'Lock release for yearly reset numbers.', 'info' );
			}

		} else {
			$semaphore->log( 'Couldn\'t get the lock for yearly reset numbers.', 'critical' );
		}

		// reschedule the action for the next year
		$this->schedule_yearly_reset_numbers();
	}

	public function maybe_schedule_yearly_reset_numbers() {
		$schedule = false;

		foreach ( WPO_WCPDF()->documents->get_documents( 'all' ) as $document ) {
			if ( isset( $document->settings['reset_number_yearly'] ) ) {
				$schedule = true;
				break;
			}
		}

		// unschedule existing actions
		if ( ! $schedule ) {
			if ( function_exists( '\\as_unschedule_all_actions' ) ) {
				\as_unschedule_all_actions( 'wpo_wcpdf_schedule_yearly_reset_numbers' );
			} else {
				wcpdf_log_error( 'Action Scheduler functions not available. Cannot unschedule yearly document number reset.', 'critical' );
			}
		}

		return $schedule;
	}

	public function yearly_reset_action_is_scheduled() {
		$is_scheduled = false;

		if ( ! function_exists( '\\as_get_scheduled_actions' ) ) {
			wcpdf_log_error( 'Action Scheduler function not available. Cannot check if the yearly numbering reset is scheduled.', 'critical' );
			return $is_scheduled;
		}

		$scheduled_actions = \as_get_scheduled_actions( array(
			'hook'   => 'wpo_wcpdf_schedule_yearly_reset_numbers',
			'status' => \ActionScheduler_Store::STATUS_PENDING,
		) );

		if ( ! empty( $scheduled_actions ) ) {
			$total_actions = count( $scheduled_actions );
			if ( $total_actions === 1 ) {
				$is_scheduled = true;
			} else {
				$message = sprintf(
					/* translators: total scheduled actions */
					__( 'Only 1 scheduled action should exist for the yearly reset of the numbering system, but %s were found', 'woocommerce-pdf-invoices-packing-slips' ),
					$total_actions
				);
				wcpdf_log_error( $message );
			}
		}

		return $is_scheduled;
	}

	public function get_media_upload_setting_html() {
		check_ajax_referer( 'wpo_wcpdf_get_media_upload_setting_html', 'security' );

		$request = stripslashes_deep( $_POST );

		// check permissions
		if ( ! $this->user_can_manage_settings() ) {
			wp_send_json_error();
		}

		// get previous (default) args and preset current
		$args            = isset( $request['args'] ) ? $request['args'] : array();
		$args['current'] = isset( $request['attachment_id'] ) ? absint( $request['attachment_id'] ) : 0;

		if ( isset( $args['translatable'] ) ) {
			$args['translatable'] = wc_string_to_bool( $args['translatable'] );
		}

		// get settings HTML
		ob_start();
		$this->callbacks->media_upload( $args );
		$html = ob_get_clean();

		return wp_send_json_success( $html );
	}

	public function move_setting_after_id( $settings, $insert_settings, $after_setting_id ) {
		$pos = 1; // this is already +1 to insert after the actual pos
		foreach ( $settings as $setting ) {
			if ( isset( $setting['id'] ) && $setting['id'] == $after_setting_id ) {
				$section = $setting['section'];
				break;
			} else {
				$pos++;
			}
		}

		// replace section
		if ( isset( $section ) ) {
			foreach ( $insert_settings as $key => $insert_setting ) {
				$insert_settings[$key]['section'] = $section;
			}
		} else {
			$empty_section = array(
				array(
					'type'     => 'section',
					'id'       => 'custom',
					'title'    => '',
					'callback' => 'section',
				),
			);
			$insert_settings = array_merge( $empty_section, $insert_settings );
		}
		// insert our api settings
		$new_settings = array_merge( array_slice( $settings, 0, $pos, true ), $insert_settings, array_slice( $settings, $pos, NULL, true ) );

		return $new_settings;
	}

	/**
	 * Applies categories to document settings.
	 *
	 * @return void
	 */
	public function update_documents_settings_categories(): void {
		$documents = WPO_WCPDF()->documents->get_documents( 'all' );

		foreach ( $documents as $document ) {
			foreach ( $document->output_formats as $output_format ) {
				add_filter(
					"wpo_wcpdf_settings_fields_documents_{$document->get_type()}_{$output_format}",
					array( $this, 'apply_document_settings_categories' ),
					999
				);
			}
		}
	}

	/**
	 * Apply categories to documents settings fields.
	 *
	 * @param array  $settings_fields
	 *
	 * @return array
	 */
	public function apply_document_settings_categories( array $settings_fields ): array {
		$current_filter = explode( '_', current_filter() );
		$output_format  = end( $current_filter );
		$document_type  = prev( $current_filter );
		$document       = wcpdf_get_document( $document_type, null );

		if ( ! $document ) {
			return $settings_fields;
		}

		$settings_categories = is_callable( array( $document, 'get_settings_categories' ) )
			? $document->get_settings_categories( $output_format )
			: array();

		// Return if no category found!
		if ( empty( $settings_categories ) ) {
			return $settings_fields;
		}

		return $this->apply_setting_categories( $settings_fields, $settings_categories );
	}

	/**
	 * Apply categories to general settings.
	 *
	 * @param array $settings_fields
	 * @param string $page
	 * @param string $option_group
	 * @param string $option_name
	 * @param SettingsGeneral $general_settings
	 *
	 * @return array
	 */
	public function update_general_settings_categories( array $settings_fields, string $page, string $option_group, string $option_name, \WPO\IPS\Settings\SettingsGeneral $general_settings ): array {
		$settings_categories = is_callable( array( $general_settings, 'get_settings_categories' ) )
			? $general_settings->get_settings_categories()
			: array();

		if ( empty( $settings_categories ) ) {
			return $settings_fields;
		}

		return $this->apply_setting_categories( $settings_fields, $settings_categories );
	}

	/**
	 * Apply categories to settings fields.
	 *
	 * @param array $settings_fields
	 * @param array $settings_categories
	 *
	 * @return array
	 */
	public function apply_setting_categories( array $settings_fields, array $settings_categories ): array {
		if ( empty( $settings_fields ) || empty( $settings_categories ) ) {
			return $settings_fields;
		}

		$modified_settings_fields = array();
		$settings_lookup          = array();
		$processed_keys           = array();

		foreach ( $settings_fields as $key => $settings_field ) {
			if ( 'section' === $settings_field['type'] ) {
				// Remove all sections.
				unset( $settings_fields[ $key ] );
				continue;
			}

			// Create a lookup array for settings fields by id.
			// This allows for quick access to settings fields by their id, reducing the time complexity
			// of finding a settings field from O(n*m) to O(n+m), where n is the number of category members
			// and m is the number of settings fields.
			$settings_lookup[ $settings_field['id'] ] = $key;
		}

		// Update settings fields.
		foreach ( $settings_categories as $category_name => $category_details ) {
			// Add section for each category.
			$modified_settings_fields[] = $this->create_section( $category_name, $category_details['title'] );

			// Add settings fields based on the order in the members array.
			foreach ( $category_details['members'] as $member ) {
				if ( isset( $settings_lookup[ $member ] ) ) {
					$key = $settings_lookup[ $member ];

					// Skip if the key has already been processed.
					if ( in_array( $key, $processed_keys, true ) ) {
						continue;
					}

					$settings_field             = $settings_fields[ $key ];
					$settings_field['section']  = $category_name;
					$modified_settings_fields[] = $settings_field;
					$processed_keys[]           = $key;
				}
			}
		}

		// Check for any unprocessed settings fields.
		$unprocessed_settings_fields = array_diff_key( $settings_fields, array_flip( $processed_keys ) );

		// Create an "Additional settings" section for uncategorized settings fields.
		if ( ! empty( $unprocessed_settings_fields ) ) {
			$category_name = 'additional';

			$modified_settings_fields[] = $this->create_section(
				$category_name,
				__( 'Additional settings', 'woocommerce-pdf-invoices-packing-slips' )
			);

			// Add rest of settings to the $modified_settings_fields array under "More" category
			foreach ( $unprocessed_settings_fields as $settings_field ) {
				$settings_field['section']  = $category_name;
				$modified_settings_fields[] = $settings_field;
			}
		}

		return $modified_settings_fields;
	}

	/**
	 * Creates a section array for settings fields.
	 *
	 * @param string $category_name The ID of the category.
	 * @param string $category_title The title of the section.
	 *
	 * @return array The section configuration array.
	 */
	private function create_section( string $category_name, string $category_title ): array {
		return array(
			'type'     => 'section',
			'id'       => $category_name,
			'title'    => $category_title,
			'callback' => 'section',
			'args'     => array(
				'before_section' => '<div class="settings_category" id="' . esc_attr( $category_name ) . '">',
				'after_section'  => '</div>',
			),
		);
	}

	/**
	 * Helper method to add a single setting field to a category.
	 *
	 * @param array    $settings_categories Array of existing settings categories, with category names as keys.
	 * @param string   $new_setting_id      The new setting ID to add to the specified category.
	 * @param string   $category_name       Name of the category to which the settings will be added.
	 * @param int|null $position            Optional. The position at which to insert the new settings (starts from 1). Defaults to appending at the end.
	 *
	 * @return array
	 */
	public function add_single_setting_field_to_category( array $settings_categories, string $new_setting_id, string $category_name, ?int $position = null ): array {
		return $this->add_setting_field_to_category( $settings_categories, array( $new_setting_id ), $category_name, $position );
	}

	/**
	 * Helper method to add multiple setting fields to a category.
	 *
	 * @param array    $settings_categories Array of existing settings categories, with category names as keys.
	 * @param array    $new_setting_ids     Array of new setting IDs to add to the specified category.
	 * @param string   $category_name       Name of the category to which the settings will be added.
	 * @param int|null $position            Optional. The position at which to insert the new settings (starts from 1). Defaults to appending at the end.
	 *
	 * @return array
	 */
	public function add_multiple_setting_fields_to_category( array $settings_categories, array $new_setting_ids, string $category_name, ?int $position = null ): array {
		return $this->add_setting_field_to_category( $settings_categories, $new_setting_ids, $category_name, $position );
	}

	/**
	 * Internal method to handle adding setting fields to a category.
	 *
	 * @param array    $settings_categories Array of existing settings categories, with category names as keys.
	 * @param array    $new_setting_ids     Array of new setting IDs to add to the specified category.
	 * @param string   $category_name       Name of the category to which the settings will be added.
	 * @param int|null $position            Optional. The position at which to insert the new settings (1-based index). Defaults to appending at the end.
	 *
	 * @return array
	 */
	private function add_setting_field_to_category( array $settings_categories, array $new_setting_ids, string $category_name, ?int $position = null ): array {
		if ( ! isset( $settings_categories[ $category_name ] ) ) {
			return $settings_categories;
		}

		$members = &$settings_categories[ $category_name ]['members'];

		if ( is_null( $position ) || 0 === $position ) {
			$members = array_merge( $members, $new_setting_ids );
		} else {
			array_splice( $members, $position - 1, 0, $new_setting_ids );
		}

		return $settings_categories;
	}

	/**
	 * Get the position of a specific setting in the settings array.
	 *
	 * @param array  $settings_categories Array of settings categories where the setting name is searched.
	 * @param string $category            Name of the category to search in.
	 * @param string $setting_name        Name of the setting to find in the settings array.
	 *
	 * @return int Position of the setting (1-based index) if found; otherwise, returns 0.
	 */
	public function get_setting_position( array $settings_categories, string $category, string $setting_name ): int {
		if ( empty( $settings_categories[ $category ]['members'] ) ) {
			return 0;
		}

		$key = array_search( $setting_name, $settings_categories[ $category ]['members'], true );

		return $key !== false ? absint( $key ) + 1: 0;
	}

	/**
	 * Helper method to add a setting category.
	 *
	 * @param array  $settings_categories
	 * @param string $category_name
	 * @param string $title
	 * @param array  $members
	 *
	 * @return array
	 */
	public function add_settings_category( array $settings_categories, string $category_name, string $title, array $members ): array {
		// Do not override if the category already exist.
		if ( isset( $settings_categories[ $category_name ] ) ) {
			return $settings_categories;
		}

		$settings_categories[ $category_name ] = array(
			'title'   => $title,
			'members' => $members,
		);

		return $settings_categories;
	}

	/**
	 * Syncs the address from WooCommerce settings.
	 *
	 * @return void
	 */
	public function sync_shop_address_with_woo(): void {
		check_ajax_referer( 'wpo_wcpdf_admin_nonce', 'security' );

		$address_field = ! empty( $_POST['address_field'] )
			? sanitize_text_field( wp_unslash( $_POST['address_field'] ) )
			: '';

		if ( empty( $address_field ) ) {
			wp_send_json_error( array( 'message' => __( 'Address field is required.', 'woocommerce-pdf-invoices-packing-slips' ) ) );
			return;
		}

		// Map address fields to the option keys used by WooCommerce.
		$address_map = array(
			'shop_address_line_1'   => 'woocommerce_store_address',
			'shop_address_line_2'   => 'woocommerce_store_address_2',
			'shop_address_country'  => 'woocommerce_default_country',
			'shop_address_state'    => 'woocommerce_default_country',
			'shop_address_city'     => 'woocommerce_store_city',
			'shop_address_postcode' => 'woocommerce_store_postcode',
		);

		// Validate the address field against the map.
		if ( ! array_key_exists( $address_field, $address_map ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid address field.', 'woocommerce-pdf-invoices-packing-slips' ) ) );
			return;
		}

		$option_key = $address_map[ $address_field ];
		$raw_value  = get_option( $option_key, '' );

		// Return, if the value is not set.
		if ( empty( $raw_value ) ) {
			wp_send_json_error( array( 'message' => __( 'The field is empty.', 'woocommerce-pdf-invoices-packing-slips' ) ) );
			return;
		}

		// Parse the value based on the address field.
		switch ( $address_field ) {
			case 'shop_address_state':
				$parsed = wc_format_country_state_string( $raw_value );
				$value  = $parsed['state'] ?? null;
				break;
			default:
				$value = $raw_value;
		}

		wp_send_json_success( array( 'value' => $value ) );
	}

}

endif; // class_exists
