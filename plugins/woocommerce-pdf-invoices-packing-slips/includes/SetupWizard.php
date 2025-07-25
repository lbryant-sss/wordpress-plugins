<?php
namespace WPO\IPS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( '\\WPO\\IPS\\SetupWizard' ) ) :

class SetupWizard {

	/** @var string Current Step */
	private $step   = '';

	/** @var array Steps for the setup wizard */
	private $steps  = array();

	protected static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {
		if ( WPO_WCPDF()->settings->user_can_manage_settings() ) {
			add_action( 'admin_menu', array( $this, 'admin_menus' ) );
			remove_all_actions( 'admin_init' ); // prevents other plugins from adding their own actions
			add_action( 'admin_init', array( $this, 'setup_wizard' ) );
		}
	}

	/**
	 * Add admin menus/screens.
	 */
	public function admin_menus() {
		add_dashboard_page( '', '', 'manage_options', 'wpo-wcpdf-setup', '' );
	}

	/**
	 * Show the setup wizard.
	 */
	public function setup_wizard() {
		$suffix  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$request = stripslashes_deep( $_REQUEST ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( empty( $request['page'] ) || 'wpo-wcpdf-setup' !== $request['page'] ) {
			return;
		}

		if ( is_null ( get_current_screen() ) ) {
			set_current_screen();
		}

		$this->steps = array(
			'shop-name' => array(
				'name'	=> __( 'Shop Name', 'woocommerce-pdf-invoices-packing-slips' ),
				'view'	=> WPO_WCPDF()->plugin_path() . '/views/setup-wizard/shop-name.php',
			),
			'logo' => array(
				'name'	=> __( 'Your logo', 'woocommerce-pdf-invoices-packing-slips' ),
				'view'	=> WPO_WCPDF()->plugin_path() . '/views/setup-wizard/logo.php',
			),
			'attach-to' => array(
				'name'	=> __( 'Attachments', 'woocommerce-pdf-invoices-packing-slips' ),
				'view'	=> WPO_WCPDF()->plugin_path() . '/views/setup-wizard/attach-to.php',
			),
			'display-options' => array(
				'name'	=> __( 'Display options', 'woocommerce-pdf-invoices-packing-slips' ),
				'view'	=> WPO_WCPDF()->plugin_path() . '/views/setup-wizard/display-options.php',
			),
			'paper-format' => array(
				'name'	=> __( 'Paper format', 'woocommerce-pdf-invoices-packing-slips' ),
				'view'	=> WPO_WCPDF()->plugin_path() . '/views/setup-wizard/paper-format.php',
			),
			'show-action-buttons' => array(
				'name'	=> __( 'Action buttons', 'woocommerce-pdf-invoices-packing-slips' ),
				'view'	=> WPO_WCPDF()->plugin_path() . '/views/setup-wizard/show-action-buttons.php',
			),
			'good-to-go' => array(
				'name'	=> __( 'Ready!', 'woocommerce-pdf-invoices-packing-slips' ),
				'view'	=> WPO_WCPDF()->plugin_path() . '/views/setup-wizard/good-to-go.php',
			),
		);
		$this->step = isset( $request['step'] ) ? sanitize_text_field( $request['step'] ) : current( array_keys( $this->steps ) );

		wp_enqueue_style(
			'wpo-wcpdf-setup',
			WPO_WCPDF()->plugin_url() . '/assets/css/setup-wizard' . $suffix . '.css',
			array( 'dashicons', 'install' ),
			WPO_WCPDF_VERSION
		);

		wp_enqueue_style(
			'wpo-wcpdf-toggle-switch',
			WPO_WCPDF()->plugin_url() . '/assets/css/toggle-switch' . $suffix . '.css',
			array(),
			WPO_WCPDF_VERSION
		);

		if ( ! wp_style_is( 'woocommerce_admin_styles', 'enqueued' ) ) {
			wp_enqueue_style(
				'woocommerce_admin_styles',
				WC()->plugin_url() . '/assets/css/admin.css',
				array(),
				WC_VERSION
			);
		}

		wp_register_script(
			'wpo-wcpdf-media-upload',
			WPO_WCPDF()->plugin_url() . '/assets/js/media-upload' . $suffix . '.js',
			array( 'jquery', 'media-editor', 'mce-view' ),
			WPO_WCPDF_VERSION
		);

		wp_localize_script(
			'wpo-wcpdf-media-upload',
			'wpo_wcpdf_admin',
			array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) )
		);

		wp_register_script(
			'wpo-wcpdf-setup',
			WPO_WCPDF()->plugin_url() . '/assets/js/setup-wizard' . $suffix . '.js',
			array( 'jquery', 'wpo-wcpdf-media-upload' ),
			WPO_WCPDF_VERSION
		);
		
		wp_localize_script(
			'wpo-wcpdf-setup',
			'wpo_wcpdf_setup',
			array(
				'ajaxurl'                       => admin_url( 'admin-ajax.php' ),
				'shop_country_changed_messages' => array(
					'loading' => __( 'Loading', 'woocommerce-pdf-invoices-packing-slips' ) . '...',
					'empty'   => __( 'No states available', 'woocommerce-pdf-invoices-packing-slips' ),
					'error'   => __( 'Error loading', 'woocommerce-pdf-invoices-packing-slips' ),
				),
			)
		);

		if ( ! wp_script_is( 'jquery-blockui', 'enqueued' ) ) {
			wp_register_script(
				'jquery-blockui',
				WC()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js',
				array( 'jquery' ),
				WC_VERSION
			);
		}

		if ( ! wp_script_is( 'select2', 'enqueued' ) ) {
			wp_register_script(
				'select2',
				WC()->plugin_url() . '/assets/js/select2/select2.full.min.js',
				array( 'jquery', 'jquery-blockui' ),
				WC_VERSION
			);
		}

		wp_enqueue_media();

		$step_keys = array_keys( $this->steps );
		if ( end( $step_keys ) === $this->step ) {
			wp_register_script(
				'wpo-wcpdf-setup-confetti',
				WPO_WCPDF()->plugin_url() . '/assets/js/confetti' . $suffix . '.js',
				array( 'jquery' ),
				WPO_WCPDF_VERSION
			);
		}

		if ( ! empty( $request['save_step'] ) ) {
			$this->save_step();
		}

		// disable query monitor during wizard
		add_filter( 'qm/dispatch/html', '__return_false' );

		ob_start();
		$this->setup_wizard_header();
		$this->setup_wizard_steps();
		$this->setup_wizard_content();
		$this->setup_wizard_footer();
		exit;
	}

	/**
	 * Setup Wizard Header.
	 */
	public function setup_wizard_header() {
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?> class="wpo-wizard">
		<head>
			<meta name="viewport" content="width=device-width" />
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title>PDF Invoices & Packing Slips for WooCommerce &rsaquo; <?php esc_html_e( 'Setup Wizard', 'woocommerce-pdf-invoices-packing-slips' ); ?></title>
			<?php wp_print_scripts( 'wpo-wcpdf-setup' ); ?>
			<?php wp_print_scripts( 'wpo-wcpdf-setup-confetti' ); ?>
			<?php wp_print_scripts( 'select2' ); ?>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_head' ); ?>
		</head>
		<body class="wpo-wcpdf-setup wp-core-ui">
			<?php if( $this->step == 'good-to-go' ) { echo "<div id='confetti'></div>"; } ?>
			<form method="post">
		<?php
	}

	/**
	 * Output the steps.
	 */
	public function setup_wizard_steps() {
		$output_steps = $this->steps;
		// array_shift( $output_steps );
		?>
		<div class="wpo-setup-card">
			<h1 class="wpo-plugin-title">PDF Invoices & Packing Slips for WooCommerce</h1>
			<ol class="wpo-progress-bar">
				<?php foreach ( $output_steps as $step_key => $step ) : ?>
					<li class="<?php
						if ( $step_key === $this->step ) {
							echo 'active';
						} elseif ( array_search( $this->step, array_keys( $this->steps ) ) > array_search( $step_key, array_keys( $this->steps ) ) ) {
							echo 'completed';
						}
					?>"><a href="<?php echo esc_attr( $this->get_step_link( $step_key ) ); ?>" ><div class="wpo-progress-marker"></div></a></li>
				<?php endforeach; ?>
			</ol>
			<?php
	}

	/**
	 * Output the content for the current step.
	 */
	public function setup_wizard_content() {
		echo '<div class="wpo-setup-content">';
		include( $this->steps[ $this->step ]['view'] );
		echo '</div>';
	}

	/**
	 * Setup Wizard Footer.
	 */
	public function setup_wizard_footer() {
		?>
						<input type="hidden" name="wpo_wcpdf_step" value="<?php echo esc_attr( $this->step ); ?>">
						<div class="wpo-setup-buttons">
							<?php if ( $step = $this->get_step( -1 ) ): ?>
								<a href="<?php echo esc_attr( $this->get_step_link( $step ) ); ?>" class="wpo-button-previous"><?php esc_html_e( 'Previous', 'woocommerce-pdf-invoices-packing-slips' ); ?></a>
							<?php endif ?>
							<!-- <input type="submit" class="wpo-button-next" value="Next" /> -->
							<?php if ( $step = $this->get_step( 1 ) ): ?>
								<?php wp_nonce_field( 'wpo-wcpdf-setup' ); ?>
								<input type="submit" class="wpo-button-next" value="<?php esc_attr_e( 'Next', 'woocommerce-pdf-invoices-packing-slips' ); ?>" name="save_step" />
								<a href="<?php echo esc_attr( $this->get_step_link( $step ) ); ?>" class="wpo-skip-step"><?php esc_html_e( 'Skip this step', 'woocommerce-pdf-invoices-packing-slips' ); ?></a>
							<?php else: ?>
								<a href="<?php echo esc_attr( $this->get_step_link($step) ); ?>" class="wpo-button-next"><?php esc_html_e( 'Finish', 'woocommerce-pdf-invoices-packing-slips' ); ?></a>
							<?php endif ?>
						</div>
					</div>
				</form>
				<?php do_action( 'admin_footer' ); // for media uploader templates ?>
			</body>
		</html>
		<?php
	}

	public function get_step_link( $step ) {
		$step_keys = array_keys( $this->steps );
		if ( end( $step_keys ) === $this->step && empty( $step ) ) {
			return admin_url('admin.php?page=wpo_wcpdf_options_page&tab=general');
		}
		return esc_url_raw( add_query_arg( 'step', $step ) );
	}


	public function get_step( $delta ) {
		$step_keys = array_keys( $this->steps );
		$current_step_pos = array_search( $this->step, $step_keys );
		$new_step_pos = $current_step_pos + $delta;
		if ( isset( $step_keys[$new_step_pos] ) ) {
			return $step_keys[$new_step_pos];
		} else {
			return false;
		}
	}

	public function save_step() {
		$request = stripslashes_deep( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( isset( $this->steps[ $this->step ]['handler'] ) ) {
			check_admin_referer( 'wpo-wcpdf-setup' );
			// for doing more than just saving an option value
			call_user_func( $this->steps[ $this->step ]['handler'] );
		} else {
			if ( ! empty( $request['wcpdf_settings'] ) && is_array( $request['wcpdf_settings'] ) ) {
				check_admin_referer( 'wpo-wcpdf-setup' );

				foreach ( $request['wcpdf_settings'] as $option => $settings ) {
					// sanitize posted settings
					foreach ( $settings as $key => $value ) {
						if ( 'attach_to_email_ids' === $key ) {
							$value = array_fill_keys( $value, '1' );
						}

						if ( 'shop_address_additional' === $key && function_exists( 'sanitize_textarea_field' ) ) {
							$sanitize_function = 'sanitize_textarea_field';
						} else {
							$sanitize_function = 'sanitize_text_field';
						}

						$value = stripslashes_deep( $value );

						if ( is_array( $value ) ) {
							$settings[$key] = array_map( $sanitize_function, $value );
						} else {
							$settings[$key] = call_user_func( $sanitize_function, $value );
						}
					}

					$current_settings = get_option( $option, array() );
					$new_settings = $settings + $current_settings;
					update_option( $option, $new_settings );
				}
			} elseif ( ! empty( $request['wpo_wcpdf_step'] ) && 'show-action-buttons' === $request['wpo_wcpdf_step'] ) {
				$orders_column_hidden_key = WPO_WCPDF()->order_util->custom_orders_table_usage_is_enabled()
					? 'managewoocommerce_page_wc-orderscolumnshidden'
					: 'manageedit-shop_ordercolumnshidden';

				$user_id    = get_current_user_id();
				$hidden     = get_user_meta( $user_id, $orders_column_hidden_key, true );
				$column_key = 'wc_actions';

				if ( is_array( $hidden ) ) {
					if ( ! empty( $request['wc_show_action_buttons'] ) ) {
						$new_hidden = array_filter( $hidden, function( $setting ) use ( $column_key ) {
							return $setting !== $column_key;
						} );
					} else {
						$new_hidden = array_unique( array_merge( $hidden, array( $column_key ) ) );
					}

					if ( $new_hidden !== $hidden ) {
						update_user_meta( $user_id, $orders_column_hidden_key, $new_hidden );
					}
				}
			}
		}

		wp_redirect( esc_url_raw( $this->get_step_link( $this->get_step(1) ) ) );
	}

}

endif; // class_exists
