<?php
/**
 * Handles the display of the Site Scan admin page.
 *
 * @package termly
 */

$dashboard_link = add_query_arg(
	[
		'utm_source'  => 'termly_wp_plugin',
		'utm_content' => 'site-scan',
	],
	termly\Urls::get_dashboard_link()
);

?>
<div class="wrap termly termly-site-scan">

	<div class="termly-content-wrapper">

		<div class="termly-content-cell termly-left-column">

			<div class="termly-content-header">
				<?php require plugin_dir_path( __FILE__ ) . 'header-logo.php'; ?>
				<h1><?php esc_html_e( 'Site Scan', 'uk-cookie-consent' ); ?></h1>

				<form action="<?php echo esc_attr( termly\Urls::get_scan_url() ); ?>" method="POST" class="grower">
					<input type="hidden" name="action" value="new-scan"/>
					<button type="submit" class="page-title-action scan-now">
						<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path fill-rule="evenodd" clip-rule="evenodd" d="M1.66663 3.33332C1.66663 2.41285 2.41282 1.66666 3.33329 1.66666H6.66663V3.33332H3.33329V6.66666H1.66663V3.33332Z" fill="white"/>
							<path fill-rule="evenodd" clip-rule="evenodd" d="M1.66663 16.6667C1.66663 17.5872 2.41282 18.3333 3.33329 18.3333H6.66663V16.6667H3.33329V13.3333H1.66663V16.6667Z" fill="white"/>
							<path fill-rule="evenodd" clip-rule="evenodd" d="M18.3334 3.33332C18.3334 2.41285 17.5872 1.66666 16.6667 1.66666H13.3334V3.33332H16.6667V6.66666H18.3334V3.33332Z" fill="white"/>
							<path fill-rule="evenodd" clip-rule="evenodd" d="M18.3334 16.6667C18.3334 17.5872 17.5872 18.3333 16.6667 18.3333H13.3334V16.6667H16.6667V13.3333H18.3334V16.6667Z" fill="white"/>
							<rect x="1.66663" y="9.16666" width="16.6667" height="1.66667" fill="white"/>
						</svg>
						<?php esc_html_e( 'Scan Now', 'uk-cookie-consent' ); ?>
					</button>
				</form>
				<div class="termly-dashboard-link-container">
					<a href="<?php echo esc_attr( $dashboard_link ); ?>" target="_blank">
						<span><?php esc_html_e( 'Go to Termly Dashboard', 'uk-cookie-consent' ); ?></span>
						<svg width="8" height="11" viewBox="0 0 8 11" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path fill-rule="evenodd" clip-rule="evenodd" d="M2.08997 10.91L7.08997 5.91L2.08997 0.910004L0.909973 2.09L4.74997 5.91L0.909973 9.73L2.08997 10.91Z" fill="#4672FF"/>
						</svg>
					</a>
				</div>
			</div>
			<?php settings_errors( 'termly_site_scan' ); ?>
			<hr class="wp-header-end">

			<div class="content">
				<h2 class="title"><?php esc_html_e( 'Cookie Scan Results', 'uk-cookie-consent' ); ?></h2>
				<p>
					<?php
					printf(
						'%s <a href="%s">%s</a>.',
						esc_html__( 'After the scan is complete, you can view your cookie list by going to', 'uk-cookie-consent' ),
						esc_attr( termly\Urls::get_cookie_management_url() ),
						esc_html__( 'Cookie Management', 'uk-cookie-consent' )
					);
					?>
				</p>

				<h2 class="title"><?php esc_html_e( 'Site Scan Settings', 'uk-cookie-consent' ); ?></h2>
				<p><?php esc_html_e( 'Termly can automatically scan your site for cookies. Schedule regular scans to keep your consent banner compliant.', 'uk-cookie-consent' ); ?></p>
				<form action='options.php' method='post'>
				<?php
					settings_fields( 'termly_site_scan' );
					do_settings_sections( 'termly_site_scan' );
					submit_button();
				?>
				</form>
			</div>

		</div>

		<div class="termly-content-cell termly-right-column">

			<?php require TERMLY_VIEWS . 'consent-toggle-sidebar.php'; ?>
			<?php require TERMLY_VIEWS . 'upgrade-notice-sidebar.php'; ?>
			<?php require TERMLY_VIEWS . 'wp-rocket-notice-sidebar.php'; ?>
			<?php require TERMLY_VIEWS . 'review-notice-sidebar.php'; ?>

		</div>

	</div>

</div>
