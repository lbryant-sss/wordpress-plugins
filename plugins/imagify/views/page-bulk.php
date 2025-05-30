<?php
defined( 'ABSPATH' ) || exit;
?>
<div class="wrap imagify-settings imagify-bulk">

	<?php $this->print_template( 'part-bulk-optimization-header' ); ?>

	<div class="imagify-settings-section">

		<div class="imagify-columns">
			<div class="imagify-col col-overview">
				<h2 class="imagify-h2-like">
					<span class="dashicons dashicons-chart-line"></span>
					<?php esc_html_e( 'Overview', 'imagify' ); ?>
				</h2>

				<div class="imagify-columns">
					<div class="imagify-col col-statistics">
						<h3 class="screen-reader-text"><?php esc_html_e( 'Statistics', 'imagify' ); ?></h3>

						<div class="imagify-number-you-optimized">
							<p>
								<span id="imagify-total-optimized-attachments" class="number"><?php echo esc_html( $data['already_optimized_attachments'] ); ?></span>
								<span class="text">
									<?php
									printf(
										/* translators: you can use %s to include a line break. */
										esc_html__( 'that\'s the number of original images%s you optimized with Imagify', 'imagify' ),
										'<br>'
									);
									?>
								</span>
							</p>
						</div>

						<div class="imagify-bars">
							<p><?php esc_html_e( 'Original size', 'imagify' ); ?></p>
							<div class="imagify-bar-negative base-transparent right-outside-number">
								<div id="imagify-original-bar" class="imagify-progress" style="width: 100%"><span class="imagify-barnb"><?php echo esc_html( $data['original_human'] ); ?></span></div>
							</div>

							<p><?php esc_html_e( 'Optimized size', 'imagify' ); ?></p>
							<div class="imagify-bar-primary base-transparent right-outside-number">
								<div id="imagify-optimized-bar" class="imagify-progress" style="width: <?php echo max( 100 - $data['optimized_percent'], 0 ); ?>%"><span class="imagify-barnb"><?php echo esc_html( $data['optimized_human'] ); ?></span></div>
							</div>

						</div>

						<div class="imagify-number-you-optimized">
							<p>
								<span id="imagify-total-optimized-attachments-pct" class="number"><?php echo esc_html( number_format_i18n( $data['optimized_percent'] ) ); ?>%</span>
								<span class="text">
									<?php
									printf(
										/* translators: %s is a line break. */
										esc_html__( 'that\'s the size you saved %sby using Imagify', 'imagify' ),
										'<br>'
									);
									?>
								</span>
							</p>
						</div>
					</div><!-- .imagify-col.col-statistics -->

					<div class="imagify-col col-chart">
						<div class="imagify-chart-container imagify-overview-chart-container">
							<canvas id="imagify-overview-chart" width="180" height="180" data-unoptimized="<?php echo esc_attr( $data['unoptimized_attachments'] ); ?>" data-optimized="<?php echo esc_attr( $data['optimized_attachments'] ); ?>" data-errors="<?php echo esc_attr( $data['errors_attachments'] ); ?>"></canvas>
							<div id="imagify-overview-chart-percent" class="imagify-chart-percent"><?php echo esc_html( min( $data['optimized_attachments_percent'], 100 ) ); ?><span>%</span></div>
						</div>
						<div id="imagify-overview-chart-legend"></div>

						<p class="imagify-global-optim-phrase imagify-clear">
							<?php
							printf(
								/* translators: %s is a percentage. */
								esc_html__( 'You optimized %s of your website\'s images', 'imagify' ),
								'<span class="imagify-total-percent">' . esc_html( min( $data['optimized_attachments_percent'], 100 ) ) . '%</span>'
							);
							?>
						</p>
					</div><!-- .imagify-col -->
				</div>
			</div><!-- .imagify-col.col-overview -->

			<div class="imagify-col imagify-account-info-col">

				<?php
				if (
					( ! defined( 'IMAGIFY_HIDDEN_ACCOUNT' ) || ! IMAGIFY_HIDDEN_ACCOUNT )
					&&
					Imagify_Requirements::is_api_key_valid()
				) {
					?>
					<div class="imagify-options-title">
						<div class="imagify-th-titles imagify-flex imagify-vcenter">
							<span class="dashicons dashicons-admin-users"></span>
							<p class="imagify-meteo-title">
								<?php esc_html_e( 'Your Account', 'imagify' ); ?>
							</p>
						</div>
						<a href="<?php echo esc_url( imagify_get_external_url( 'subscription' ) ); ?>" target="_blank"><?php _e( 'View your profile', 'imagify' ); ?></a>
					</div>
					<?php $this->print_template( 'part-upsell' ); ?>
				<?php } ?>

			</div><!-- .imagify-account-info-col -->

		</div><!-- .imagify-columns -->

		<?php
		$this->print_template( 'part-bulk-optimization-success' );

		$this->print_template( 'part-bulk-optimization-table', $data );

		if ( ! empty( $data['no-custom-folders'] ) ) {
			$this->print_template( 'part-bulk-optimization-newbie' );
		}
		?>

		<div class="imagify-bulk-submit imagify-flex imagify-vcenter">
			<div class="imagify-pr2">
				<p>
					<?php wp_nonce_field( 'imagify-bulk-optimize', 'imagifybulkuploadnonce' ); ?>
					<?php
					$disabled = '';
					$class    = '';

					if (
						false !== get_transient( 'imagify_wp_optimize_running' )
						||
						false !== get_transient( 'imagify_custom-folders_optimize_running' )
					) {
						$disabled = 'disabled="disabled"';
						$class    = 'rotate';
					}

					?>
					<button id="imagify-bulk-action" type="button" class="button button-primary" <?php echo $disabled; ?>>
						<span class="dashicons dashicons-admin-generic <?php echo $class; ?>"></span>
						<span class="button-text"><?php _e( 'Imagif’em all', 'imagify' ); ?></span>
					</button>
				</p>
			</div>
			<?php if ( ! is_wp_error( get_imagify_max_image_size() ) ) { ?>
				<p>
					<?php
					printf(
						/* translators: %s is a file size. */
						esc_html__( 'All images greater than %s (after resizing, if any) will be optimized when using a paid plan.', 'imagify' ),
						esc_html( imagify_size_format( get_imagify_max_image_size() ) )
					);
					?>
				</p>
			<?php } ?>
		</div><!-- .imagify-bulk-submit -->
	</div><!-- .imagify-settings-section -->

	<?php
	if ( Imagify_Requirements::is_api_key_valid() ) {
		$display_infos = get_transient( 'imagify_bulk_optimization_infos' );

		?>
		<script type="text/html" id="tmpl-imagify-overquota-alert">
			<?php $this->print_template( 'part-bulk-optimization-overquota-alert' ); ?>
		</script>
		<?php

		if ( ! $display_infos ) {
			?>
			<script type="text/html" id="tmpl-imagify-bulk-infos">
				<?php
				$this->print_template(
					'part-bulk-optimization-infos',
					[
						'quota'       => $data['unconsumed_quota'],
						'quota_class' => $data['quota_class'],
						'library'     => ! empty( $data['groups']['library'] ),
					]
				);
				?>
			</script>
			<?php
		}
	}
	?>
</div>
