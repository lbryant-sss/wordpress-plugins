<?php

namespace JupiterX_Core\Raven\Modules\WooCommerce_Settings;

defined( 'ABSPATH' ) || die();

use JupiterX_Core\Raven\Base\Module_base;
use JupiterX_Core\Raven\Modules\WooCommerce_Settings\Controls;

class Module extends Module_Base {
	public function __construct() {
		parent::__construct();

		if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
			return;
		}

		$this->dependencies();

		add_action( 'elementor/kit/register_tabs', [ $this, 'add_woocommerce_section_to_editor_site_Settings' ], 1, 40 );
		add_action( 'wp_ajax_jupiterx_woocommerce_settings_notice_html', [ $this, 'grab_proper_html_per_notice_type' ] );
		add_filter( 'body_class', [ $this, 'body_classes_for_woocommerce_notices' ] );
	}

	public static function is_active() {
		return function_exists( 'WC' );
	}

	private function dependencies() {
		jupiterx_core()->load_files(
			[
				'extensions/raven/includes/modules/woocommerce-settings/controls',
			]
		);
	}

	public function add_woocommerce_section_to_editor_site_Settings( \Elementor\Core\Kits\Documents\Kit $kit ) {
		$kit->register_tab( 'raven-settings-woocommerce', Controls::class );
	}

	public function grab_proper_html_per_notice_type() {
		check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

		if ( ! current_user_can( 'edit_others_posts' ) || ! current_user_can( 'edit_others_pages' ) ) {
			wp_send_json_error( 'You do not have access to this section', 'jupiterx-core' );
		}

		$type = filter_input( INPUT_POST, 'type', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$real = filter_input( INPUT_POST, 'real_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		wc_clear_notices();

		if ( 'error' === $type ) {
			$message = sprintf(
				'%1$s <a href="#" class="wc-backward">%2$s</a>',
				esc_html__( 'This is how an error notice would look.', 'jupiterx-core' ),
				esc_html__( 'Here\'s a link', 'jupiterx-core' )
			);
		}

		if ( 'message' === $type ) {
			$message = sprintf(
				'<a href="#" tabindex="1" class="button wc-forward">%1$s</a> %2$s <a href="#" class="restore-item">%3$s</a>',
				esc_html__( 'Button', 'jupiterx-core' ),
				esc_html__( 'This is what a WooCommerce message notice looks like.', 'jupiterx-core' ),
				esc_html__( 'Here\'s a link', 'jupiterx-core' )
			);
		}

		if ( 'info' === $type ) {
			$message = sprintf(
				'<a href="#" tabindex="1" class="button wc-forward">%1$s</a> %2$s',
				esc_html__( 'Button', 'jupiterx-core' ),
				esc_html__( 'This is how WooCommerce provides an info notice.', 'jupiterx-core' )
			);
		}

		wc_add_notice( $message, $real );

		ob_start();
		?>
			<div class="jupiterx-woocommerce-notice-settings-wrapper elementor-section-wrap <?php echo esc_attr( 'jupiterx-woocommerce-notice-settings-wrapper-' . $type ); ?>">
				<div class="elementor-element elementor-element-edit-mode elementor-element-f9fa7a3 e-con e-con-boxed e-con--column" data-nesting-level="0">
					<div class="e-con-inner">
						<div class="elementor-widget-container">
							<?php
								wc_print_notices();
							?>
						</div>
					</div>
				</div>
			</div>
		<?php
		$html = ob_get_clean();

		wp_send_json_success( $html );
	}

	public function body_classes_for_woocommerce_notices( $classes ) {
		$classes[] = 'jupiterx-woocommerce-notices-style-initialized';

		return $classes;
	}
}
