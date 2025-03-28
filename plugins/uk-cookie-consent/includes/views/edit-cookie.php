<?php
/**
 * New Cookie/Edit Cookie page.
 *
 * @package Termly
 */

$dashboard_link = add_query_arg(
	[
		'utm_source'  => 'termly_wp_plugin',
		'utm_content' => 'termly-edit-cookie',
	],
	termly\Urls::get_dashboard_link()
);

require TERMLY_VIEWS . 'countries.php';

$fields = [
	'name'                => [
		'type'     => 'text',
		'required' => true,
		'label'    => __( 'Name', 'uk-cookie-consent' ),
		'editable' => false,
	],
	'category'            => [
		'type'     => 'select',
		'required' => true,
		'label'    => __( 'Category', 'uk-cookie-consent' ),
		'editable' => true,
		'options'  => [
			'essential'         => __( 'Essential', 'uk-cookie-consent' ),
			'performance'       => __( 'Performance and functionality', 'uk-cookie-consent' ),
			'analytics'         => __( 'Analytics and customization', 'uk-cookie-consent' ),
			'advertising'       => __( 'Advertising', 'uk-cookie-consent' ),
			'social_networking' => __( 'Social networking', 'uk-cookie-consent' ),
			'unclassified'      => __( 'Unclassified', 'uk-cookie-consent' ),
		],
	],
	'expire'              => [
		'type'     => 'text',
		'required' => false,
		'label'    => __( 'Expires on', 'uk-cookie-consent' ),
		'editable' => false,
	],
	'tracker_type'        => [
		'type'     => 'select',
		'required' => false,
		'label'    => __( 'Type', 'uk-cookie-consent' ),
		'editable' => false,
		'options'  => [
			'http_cookie'          => __( 'Http Cookie', 'uk-cookie-consent' ),
			'html_local_storage'   => __( 'Html Local Storage', 'uk-cookie-consent' ),
			'html_session_storage' => __( 'Html Session Storage', 'uk-cookie-consent' ),
			'server_cookie'        => __( 'Server Cookie', 'uk-cookie-consent' ),
			'pixel_tracker'        => __( 'Pixel Tracker', 'uk-cookie-consent' ),
			'indexed_db'           => __( 'IndexedDB', 'uk-cookie-consent' ),
		],
	],
	'country'             => [
		'type'     => 'select',
		'required' => false,
		'label'    => __( 'Country', 'uk-cookie-consent' ),
		// $country_list is imported from countries.php
		'options'  => $country_list,
		'editable' => true,
	],
	'domain'              => [
		'type'     => 'text',
		'required' => true,
		'label'    => __( 'Provider', 'uk-cookie-consent' ),
		'editable' => false,
	],
	'service'             => [
		'type'     => 'text',
		'required' => false,
		'label'    => __( 'Service', 'uk-cookie-consent' ),
		'editable' => true,
	],
	'service_policy_link' => [
		'type'     => 'text',
		'required' => false,
		'label'    => __( 'Service Privacy Policy', 'uk-cookie-consent' ),
		'editable' => true,
	],
	'source'              => [
		'type'     => 'text',
		'required' => false,
		'label'    => __( 'Resource URL', 'uk-cookie-consent' ),
		'editable' => true,
	],
	'value'               => [
		'type'     => 'text',
		'required' => false,
		'label'    => __( 'Example value', 'uk-cookie-consent' ),
		'editable' => true,
	],
	'en_us'               => [
		'type'     => 'textarea',
		'required' => false,
		'label'    => __( 'Purpose', 'uk-cookie-consent' ),
		'editable' => true,
	],
];

?>

<div class="wrap termly termly-edit-cookie">

	<div class="termly-content-wrapper">

		<div class="termly-content-cell termly-left-column">

			<?php
			/**
			 * Admin notice
			 */
			if ( isset( $status ) && count( $status ) > 1 ) {
				printf(
					'<div class="notice notice-%s">
						<p>%s</p>
					</div>',
					esc_html( sanitize_text_field( $status[0] ) ),
					esc_html( sanitize_text_field( $status[1] ) )
				);
			}
			?>
			<div class="termly-content-header">
				<?php require plugin_dir_path( __FILE__ ) . 'header-logo.php'; ?>
				<h1>
					<?php
					if ( $editing ) {
						esc_html_e( 'Edit Cookie', 'uk-cookie-consent' );
					} else {
						esc_html_e( 'Add Cookie', 'uk-cookie-consent' );
					}
					?>
				</h1>
				<?php if ( $editing ) { ?>
				<a href="<?php echo esc_attr( termly\Urls::get_new_cookie_url() ); ?>" class="page-title-action">
					<?php esc_html_e( 'New Cookie', 'uk-cookie-consent' ); ?>
				</a>
				<?php } ?>
				<div class="grower"></div>
				<div class="termly-dashboard-link-container">
					<a href="<?php echo esc_attr( $dashboard_link ); ?>" target="_blank">
						<span><?php esc_html_e( 'Go to Termly Dashboard', 'uk-cookie-consent' ); ?></span>
						<svg width="8" height="11" viewBox="0 0 8 11" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path fill-rule="evenodd" clip-rule="evenodd" d="M2.08997 10.91L7.08997 5.91L2.08997 0.910004L0.909973 2.09L4.74997 5.91L0.909973 9.73L2.08997 10.91Z" fill="#4672FF"/>
						</svg>
					</a>
				</div>
			</div>

			<hr class="wp-header-end">

			<div class="content">

				<form action="<?php echo esc_attr( wp_unslash( $_SERVER['REQUEST_URI'] ) ); ?>" method="post">
					<?php
					// Nonce field.
					wp_nonce_field( 'termly_cookie_nonce' );

					if ( $editing ) {
						// Cookie id field.
						printf(
							'<input type="hidden" name="cookie_id" value="%s">',
							intval( $cookie_id )
						);
					}

					// Action field.
					printf(
						'<input type="hidden" name="action" value="%s">',
						$editing ? 'edit' : 'add'
					);
					?>

					<table class="form-table" role="presentation">
						<tbody>
							<tr>
								<th scope="row"><?php esc_html_e( 'Cookie Information', 'uk-cookie-consent' ); ?></th>
								<td>
									<div class="termly-form">
							<?php

							foreach ( $fields as $name => $data ) {

								$input = '';

								if ( 'text' === $data['type'] ) {

									$value = $cookie ? esc_attr( $cookie->{ $name } ) : '';
									if ( isset( $_POST[ $name ] ) ) {
										$value = sanitize_text_field( wp_unslash( $_POST[ $name ] ) );
									}

									$input = sprintf(
										'<input name="%s" id="%s%s" value="%s" type="text" class="regular-text"%s>',
										esc_attr( $name ),
										esc_attr( $name_prefix ),
										esc_attr( $name ),
										esc_attr( $value ),
										! $editing || $data['editable'] ? '' : ' readonly'
									);
								} elseif ( 'textarea' === $data['type'] ) {

									$value = $cookie ? esc_attr( $cookie->{ $name } ) : '';
									if ( isset( $_POST[ $name ] ) ) {
										$value = sanitize_text_field( wp_unslash( $_POST[ $name ] ) );
									}

									$input = sprintf(
										'<textarea name="%s" id="%s%s" class="widefat"%s>%s</textarea>',
										esc_attr( $name ),
										esc_attr( $name_prefix ),
										esc_attr( $name ),
										! $editing || $data['editable'] ? '' : ' readonly',
										esc_attr( $value )
									);
								} elseif ( 'select' === $data['type'] ) {

									$options = [];

									$option_value = $cookie ? esc_attr( $cookie->{ $name } ) : '';
									if ( isset( $_POST[ $name ] ) ) {
										$option_value = sanitize_text_field( wp_unslash( $_POST[ $name ] ) );
									}

									foreach ( $data['options'] as $key => $value ) {
										$options[] = sprintf(
											'<option value="%s" %s>%s</option>',
											esc_attr( $key ),
											selected( $key === $option_value, true, false ),
											esc_html( $value )
										);
									}

									$input = sprintf(
										'<select name="%s" id="%s%s"%s>%s</select>',
										esc_attr( $name ),
										esc_attr( $name_prefix ),
										esc_attr( $name ),
										! $editing || $data['editable'] ? '' : ' disabled',
										implode( '', $options )
									);

								}

								printf(
									'<fieldset>
									<label for="%s%s">%s%s</label>
									%s
									</fieldset>',
									esc_attr( $name_prefix ),
									esc_attr( $name ),
									esc_html( $data['label'] ),
									$data['required'] ? ' <span class="required">*</span>' : '',
									$input // phpcs:ignore WordPress.Security.EscapeOutput
								);

							}

							?>
								</div>
							</td>
						</tr>
						</tbody>
					</table>
					<p class="submit">
						<a href="<?php echo esc_attr( termly\Urls::get_cookie_management_url() ); ?>" class="button button-secondary"><?php esc_html_e( 'Cancel', 'uk-cookie-consent' ); ?></a>
						<?php
						// Add option to add another if creating new.
						if ( ! $editing ) {
							?>
							<button class="button button-primary" type="submit" name="submit" value="add_another"><?php esc_html_e( 'Save &amp; Add Another', 'uk-cookie-consent' ); ?></button>
							<?php
						}
						?>
						<input type="submit" class="button button-primary" name="submit" value="<?php esc_attr_e( 'Save', 'uk-cookie-consent' ); ?>">
					</p>
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
