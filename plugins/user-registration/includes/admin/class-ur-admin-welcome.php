<?php
/**
 * Welcome Class
 *
 * Takes new users to Welcome Page.
 *
 * @package UserRegistration/Admin
 * @version 2.1.3
 */

defined( 'ABSPATH' ) || exit;

/**
 * Welcome class.
 */
class UR_Admin_Welcome {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		$wizard_ran      = get_option( 'user_registration_first_time_activation_flag', false );
		$onboard_skipped = get_option( 'user_registration_onboarding_skipped', false );

		// If Wizard was ran already or user is an old user of plugin, then do not proceed to Wizard page again.
		if ( ! $wizard_ran && ! $onboard_skipped ) {
			return;
		}

		add_action( 'admin_menu', array( __CLASS__, 'add_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'welcome_page' ), 30 );
	}

	/**
	 * Add admin menus/screens.
	 */
	public static function add_menu() {
		add_menu_page(
			esc_html__( 'Welcome to User Registration', 'user-registration' ),
			'user registration onboard',
			'manage_options',
			'user-registration-welcome',
			''
		);
	}

	/**
	 * Show the welcome page.
	 */
	public static function welcome_page() {

		if ( isset( $_GET['tab'] ) && 'setup-wizard' === $_GET['tab'] ) { //phpcs:ignore WordPress.Security.NonceVerification
			update_option( 'user_registration_first_time_activation_flag', false );
		}

		wp_register_script( 'ur-setup-wizard-script', UR()->plugin_url() . '/chunks/welcome.js', array( 'wp-element', 'wp-blocks', 'wp-editor' ), UR()->version, true );
		wp_enqueue_style( 'ur-setup-wizard-style', UR()->plugin_url() . '/assets/css/user-registration-setup-wizard.css', array(), UR()->version );
		wp_enqueue_script( 'ur-setup-wizard-script' );

		wp_localize_script(
			'ur-setup-wizard-script',
			'_UR_WIZARD_',
			array(
				'adminURL'        => esc_url( admin_url() ),
				'siteURL'         => esc_url( home_url( '/' ) ),
				'urRestApiNonce'  => wp_create_nonce( 'wp_rest' ),
				'onBoardIconsURL' => esc_url( UR()->plugin_url() . '/assets/images/onboard-icons' ),
				'restURL'         => rest_url(),
				'adminEmail'      => get_option( 'admin_email' ),
			)
		);

		if ( ! empty( $_GET['page'] ) && 'user-registration-welcome' === $_GET['page'] ) { //phpcs:ignore WordPress.Security.NonceVerification

			ob_start();
			self::setup_wizard_header();
			self::setup_wizard_body();
			self::setup_wizard_footer();
			exit;
		}
	}

	/**
	 * Setup wizard header content.
	 *
	 * @since 1.0.0
	 */
	public static function setup_wizard_header() {
		?>
			<!DOCTYPE html>
			<html <?php language_attributes(); ?>>
				<head>
					<meta name="viewport" content="width=device-width"/>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
					<title>
						<?php esc_html_e( 'User Registration - Setup Wizard', 'user-registration' ); ?>
					</title>
					<?php
						wp_print_head_scripts();
						wp_print_scripts( 'ur-setup-wizard-script' );
					?>
					<script>
						// To play welcome video.
						jQuery(document).on(
							"click",
							"#user-registration-welcome .welcome-video-play",
							function (event) {
							    event.preventDefault();

								jQuery(this).find(".user-registration-welcome-thumb, .user-registration-welcome-video__button").remove();

								var video = '<div class="welcome-video-container"><iframe width="560" height="315" src="https://www.youtube.com/embed/SYL24nGCChI?autoplay=1&rel=0&showinfo=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe></div>';

								jQuery(this).append(video);
							}
						);
					</script>
				</head>
		<?php
	}

	/**
	 * Setup wizard body content.
	 *
	 * @since 1.0.0
	 */
	public static function setup_wizard_body() {
		?>
			<body class="user-registration-welcome notranslate" translate="no">
				<?php
				if ( ! empty( $_GET['tab'] ) && 'setup-wizard' === $_GET['tab'] ) { //phpcs:ignore WordPress.Security.NonceVerification
					?>
					<div id="user-registration-setup-wizard"></div>
					<?php
				} else {
					?>
					<div id="user-registration-welcome" >
						<img src="<?php echo esc_url( UR()->plugin_url() . '/assets/images/onboard-icons/logo.png' ); ?>" alt="">
						<div class="user-registration-welcome-card" >
							<div class="user-registration-welcome-container">
							<div class="user-registration-welcome-video">
									<img src="<?php echo esc_url( UR()->plugin_url() . '/assets/images/onboard-icons/onboarding-thumbnail.png' ); ?>" alt="" class="onboarding-video-thumb">
									<a class="welcome-video-play">
										<button class="user-registration-welcome-video__button dashicons dashicons-controls-play">
											<span class="dashicons dashicons-controls-play"></span>
										</button>
									</a>
								</div>
								<div class="user-registration-welcome-container__header">
									<h2><?php esc_html_e( 'Welcome to User Registration & Membership', 'user-registration' ); ?></h2>
									<p><?php esc_html_e( 'Easily create custom registration forms, manage member access, receive payments and streamline your WordPress user workflow — all without code.', 'user-registration' ); ?></p>
								</div>
								<div class="user-registration-welcome-container__action">
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=user-registration-welcome&tab=setup-wizard' ) ); ?>" class="button button-primary">
										<h3 style="font-size: 18px; margin: 0px;"><?php esc_html_e( 'Get Started', 'user-registration' ); ?></h3>
										<span class="dashicons dashicons-arrow-right-alt" ></span>
									</a>
								</div>
							</div>
						</div>

						<!-- <div class="user-registration-skip-btn">
							<a href="<?php echo esc_url_raw( admin_url() . 'admin.php?page=user-registration-dashboard&end-setup-wizard=' . true . '&activeStep=install_pages' ); ?>">
								<p style="color: gray; font-style:italic;"><?php esc_html_e( 'Skip to Dashboard', 'user-registration' ); ?> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
								<path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
								</svg></p>
							</a>
						</div> -->
					</div>
					<?php
				}
				?>
			</body>
		<?php
	}

	/**
	 * Setup wizard footer content.
	 *
	 * @since 1.0.0
	 */
	public static function setup_wizard_footer() {
		if ( function_exists( 'wp_print_media_templates' ) ) {
			wp_print_media_templates();
		}
		wp_print_footer_scripts();
		wp_print_scripts( 'ur-setup-wizard-script' );
		?>
		</html>
		<?php
	}
}

UR_Admin_Welcome::init();
