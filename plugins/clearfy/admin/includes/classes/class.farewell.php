<?php

/**
 * Class WCL_Farewell handles notices that are displayed to user about Clearfy transition.
 *
 * @since      2.3.4
 * @author     Webcraftic
 * @package    Clearfy
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2025, Webcraftic
 */
class WCL_Farewell {

	/**
	 * Meta key where the option (of the time the Farewell notice was dismissed) is saved.
	 *
	 * @since 2.3.4
	 *
	 * @var string
	 */
	const SLUG_DISMISS_TIME = 'clearfy_farewell_dismissed';

	/**
	 * Super Page Cache plugin page URL.
	 *
	 * @since 2.3.4
	 *
	 * @var string
	 */
	const URL_SUPER_PAGE_CACHE = 'https://wordpress.org/plugins/wp-cloudflare-page-cache/';

	/**
	 * WCL_Farewell constructor.
	 *
	 * @since 2.3.4
	 */
	public function __construct() {

		add_action( 'admin_head', [ $this, 'process_notices' ] );
	}

	/**
	 * Decide whether to display notices or not.
	 *
	 * @since 2.3.4
	 */
	public function process_notices() {

		/**
		 * Current screen object.
		 *
		 * @var WP_Screen $screen
		 */
		$screen = get_current_screen();

		if ( ! empty( $screen->base ) && ($screen->base === 'dashboard' || $screen->base === 'settings_page_quick_start-wbcr_clearfy' ) ) {

			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET['try_super_page_cache'] ) ) {
				$farewell = empty( $_GET['try_super_page_cache'] ) ? time() : 0;
				update_user_meta( get_current_user_id(), 'clearfy_farewell_dismissed', $farewell );
			}
			// phpcs:enable WordPress.Security.NonceVerification.Recommended

			$farewell = get_user_meta( get_current_user_id(), 'clearfy_farewell_dismissed', true );

			if ( empty( $farewell ) ) {
				$this->display_detailed_notice();
			}
		}

		if ( $this->is_grace_period_ended() ) {
			$this->display_short_notice();
		}
	}

	/**
	 * Compare dates since detailed notice dismissal date and now.
	 *
	 * @since 2.3.4
	 *
	 * @return bool
	 */
	protected function is_grace_period_ended() {

		$dismissed = (int) get_user_meta( get_current_user_id(), 'clearfy_farewell_dismissed', true );
        if(empty($dismissed)) {
            return false;
        }
		return (time() - $dismissed) >  MONTH_IN_SECONDS;
	}

	/**
	 * Dismissable big (Gutenberg-like) dashboard-only notice about Clearfy transition.
	 *
	 * @since 2.3.4
	 */
	public function display_detailed_notice() {

		// Only people appropriate people should see it.
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}
		?>

		<div id="try-super-page-cache-panel" class="try-super-page-cache-panel">
			<?php wp_nonce_field( 'try-super-page-cache-panel-nonce', 'trysuperpagecachepanelnonce', false ); ?>
			<a
					class="try-super-page-cache-panel-close" href="<?php echo esc_url( admin_url( '?try_super_page_cache=0' ) ); ?>"
					aria-label="<?php esc_attr_e( 'Dismiss the Try Super Page Cache panel', 'clearfy' ); ?>">
				<?php esc_html_e( 'Dismiss', 'clearfy' ); ?>
			</a>

			<div class="try-super-page-cache-panel-content">
				<h2><?php esc_html_e( 'A More Powerful Caching Solution is Here!', 'clearfy' ); ?></h2>

				<p class="about-description">
					<?php esc_html_e( 'Clearfy has been acquired and is now part of Super Page Cache. We recommend switching to Super Page Cache for superior caching performance and modern features.', 'clearfy' ); ?>
				</p>

				<hr/>

				<div class="try-super-page-cache-panel-column-container">
					<div class="try-super-page-cache-panel-column try-super-page-cache-panel-image-column">
						<picture>
							<source srcset="about:blank" media="(max-width: 1024px)">
							<img
									src="<?php echo esc_url( WCL_PLUGIN_URL . '/admin/assets/img/super-page-cache-screenshot.png' ); ?>"
									alt="<?php esc_attr_e( 'Screenshot from the Super Page Cache interface', 'clearfy' ); ?>"/>
						</picture>
					</div>
					<div class="try-super-page-cache-panel-column plugin-card-super-page-cache">

						<div>
							<h3><?php esc_html_e( 'Switch to Super Page Cache today.', 'clearfy' ); ?></h3>

							<p>
								<?php esc_html_e( 'Super Page Cache delivers full-page disk caching with seamless Cloudflare CDN integration for dramatically faster load times. Featuring automatic cache purging, advanced lazy loading, and defer/delay JavaScript capabilities to eliminate render-blocking scripts and improve Core Web Vitals scores.', 'clearfy' ); ?>
							</p>

							<p>
								<?php esc_html_e( 'Trusted by over 50,000 WordPress sites with 430+ five-star reviews. Works perfectly with WooCommerce, popular hosting providers, and major themes. Experience lightning-fast performance with setup that takes just minutes.', 'clearfy' ); ?>
							</p>
						</div>

						<div class="try-super-page-cache-action">
							<p>
								<a
										class="button button-primary button-hero thickbox open-plugin-details-modal"
										href="<?php echo esc_url( admin_url( 'plugin-install.php?tab=plugin-information&plugin=wp-cloudflare-page-cache&TB_iframe=true&width=600&height=550' ) ); ?>">
									<?php esc_html_e( 'View Super Page Cache', 'clearfy' ); ?>
								</a>
							</p>

							<p>
								<a href="<?php echo esc_url( self::URL_SUPER_PAGE_CACHE ); ?>" target="_blank" rel="noopener noreferrer">
									<?php esc_html_e( 'Learn more about Super Page Cache', 'clearfy' ); ?>
								</a>
							</p>
						</div>
					</div>

					<div class="try-super-page-cache-panel-column plugin-card-classic-editor">

						<div>
							<h3><?php esc_html_e( 'We\'re retiring Clearfy.', 'clearfy' ); ?></h3>

							<p>
								<?php esc_html_e( 'We\'re retiring the Clearfy plugin in favor of the more powerful Super Page Cache plugin. This means that there will be no new feature updates. We will continue to maintain the Clearfy plugin for any major security issues for the next 6 months.', 'clearfy' ); ?>
							</p>
							<p>
								<?php esc_html_e( 'We strongly recommend switching to Super Page Cache. It focuses exclusively on what it does best - making your WordPress site incredibly fast through advanced caching techniques with active development and regular updates.', 'clearfy' ); ?>
							</p>
							<?php 
							$is_premium = WCL_Plugin::app()->is_premium();
							$show_premium_transfer = $is_premium && ( time() < strtotime( '2025-12-01 00:00:00' ) );
							?>
							<?php if ( $show_premium_transfer ) : ?>
								<p style="background: #e7f7e7; padding: 12px; border-left: 3px solid #00a32a; margin-top: 15px;">
									<strong><?php esc_html_e( '✓ Premium License Transfer:', 'clearfy' ); ?></strong>
									<?php esc_html_e( 'We will transfer your existing Clearfy premium license to Super Page Cache during the next few days and will reach out to you via email once this is complete. If you want to speed up this process, please', 'clearfy' ); ?> 
									<a href="<?php echo esc_url( 'https://themeisle.com/contact/' ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'contact us', 'clearfy' ); ?></a>.
								</p>
							<?php elseif ( ! $is_premium ) : ?>
								<p style="background: #f0f6fc; padding: 12px; border-left: 3px solid #2271b1; margin-top: 15px;">
									<strong><?php esc_html_e( '🎁 Special Migration Offer:', 'clearfy' ); ?></strong>
									<?php esc_html_e( 'Get 90% off Super Page Cache Pro Personal plan for the first year! Use code', 'clearfy' ); ?> 
									<code style="background: #fff; padding: 2px 6px; font-weight: bold;">MIGRATEFROMCLRF90</code>
									<?php esc_html_e( 'at checkout. Valid for 2 weeks only.', 'clearfy' ); ?>
								</p>
							<?php endif; ?>
						</div>

						<div class="try-super-page-cache-action">
							<p>
								<a
										class="button button-secondary button-hero"
										href="<?php echo esc_url( 'https://rviv.ly/rd7CAW' ); ?>" target="_blank"
										rel="noopener noreferrer">
									<?php esc_html_e( 'Get 90% Off Pro', 'clearfy' ); ?>
								</a>
							</p>
						</div>
					</div>
				</div>
			</div>

		</div>

		<script>
			jQuery( document ).ready( function() {
                if( jQuery('#WBCR').length>0){
                    
                    jQuery( '#try-super-page-cache-panel' ).insertBefore( '#WBCR' ).show();
                }else{
                    jQuery( '#try-super-page-cache-panel' ).insertAfter( '#wpbody-content .wrap h1' ).show();
                }
			} );
		</script>

		<?php
	}

	/**
	 * Non-dismissable notice displayed to a user after detailed notice dismiss.
	 *
	 * @since        2.3.4
	 * @noinspection HtmlUnknownTarget
	 */
	public function display_short_notice() {

		echo '<div class="notice notice-error"><p>';
		printf(
			wp_kses(
				'<strong>Important:</strong> Clearfy is being retired and will no longer receive updates or support. For a faster, more reliable website experience, we recommend <a href="%1$s" class="thickbox open-plugin-details-modal">switching to Super Page Cache</a> - the modern caching solution built to deliver top performance.',
				[
					'br'     => [],
					'strong' => [],
					'a'      => [
						'href'   => [],
						'target' => [],
                        'class'  => [],
						'rel'    => [],
					],
				]
			),
			esc_url( admin_url( 'plugin-install.php?tab=plugin-information&plugin=wp-cloudflare-page-cache&TB_iframe=true&width=600&height=550' ) )
		);
		echo '</p></div>';
	}
}

