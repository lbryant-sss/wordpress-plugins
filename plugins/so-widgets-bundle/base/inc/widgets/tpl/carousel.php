<div class="sow-carousel-title<?php if ( ! empty( $settings['title'] ) ) {
	echo ' has-title';
} ?>">
	<?php
	if ( ! empty( $settings['title'] ) ) {
		echo $args['before_title'] . esc_html( $settings['title'] ) . $args['after_title'];
	}

	if ( $settings['navigation'] == 'title' ) {
		?>
		<div class="sow-carousel-navigation <?php echo ! $settings['navigation_arrows'] ? 'sow-carousel-navigation-hidden' : ''; ?>">
			<?php $this->render_navigation( 'both' ); ?>
		</div>
	<?php } ?>
</div>

<div class="sow-carousel-container <?php echo ! empty( $container_classes ) ? esc_attr( $container_classes ) : ''; ?>">
	<?php if ( $settings['navigation'] == 'side' || $settings['navigation'] == 'full' ) { ?>
		<div class="sow-carousel-navigation sow-carousel-navigation-prev <?php echo ! $settings['navigation_arrows'] ? 'sow-carousel-navigation-hidden' : ''; ?>">
			<?php $this->render_navigation( 'prev' ); ?>
		</div>
	<?php } ?>
	<div class="sow-carousel-wrapper"
		data-dir="<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>"
		style="opacity: 0;"
		<?php
		foreach ( $settings['attributes'] as $n => $v ) {
			if ( ! empty( $n ) ) {
				echo 'data-' . siteorigin_sanitize_attribute_key( $n ) . '="' . esc_attr( $v ) . '" ';
			}
		}
		?>
	>
		<div
			class="sow-carousel-items"
			<?php
			if ( ! empty( $settings['item_overflow'] ) ) {
				echo 'style="width: 200vw; opacity: 0;"';
			}
			?>
		>
			<?php require $settings['item_template']; ?>
		</div>
		<?php if ( $settings['navigation'] == 'container' || $settings['navigation'] == 'full' ) { ?>
			<div class="sow-carousel-nav" <?php echo ! $settings['navigation_arrows'] && empty( $settings['navigation_dots'] ) ? 'style="display: none;"' : ''; ?>>
				<?php if ( $settings['navigation'] !== 'full' ) { ?>
					<div class="sow-carousel-nav-arrows" <?php echo ! $settings['navigation_arrows'] ? 'style="display: none;"' : ''; ?>>
						<?php $this->render_navigation( 'both' ); ?>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
	</div>

	<?php if ( $settings['navigation'] == 'side' || $settings['navigation'] == 'full' ) { ?>
		<div class="sow-carousel-navigation sow-carousel-navigation-next <?php echo ! $settings['navigation_arrows'] ? 'sow-carousel-navigation-hidden' : ''; ?>">
			<?php $this->render_navigation( 'next' ); ?>
		</div>
	<?php } ?>
</div>
