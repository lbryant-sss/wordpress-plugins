<?php
/**
 * @var array $args
 * @var array $instance
 * @var array $tabs
 * @var array $initial_tab_index
 */
if ( ! empty( $instance['title'] ) ) {
	echo $args['before_title'] . wp_kses_post( $instance['title'] ) . $args['after_title'];
}
?>
<div class="sow-tabs">
	<div class="sow-tabs-tab-container" role="tablist">
	<?php foreach ( $tabs as $i => $tab ) { ?>
		<div
			class="sow-tabs-tab
			<?php
			if ( $i == $initial_tab_index ) {
				echo ' sow-tabs-tab-selected';
			}
			?>
			"
			role="tab"
			data-anchor-id="<?php echo esc_attr( sanitize_title( $tab['anchor'] ) ); ?>"
			aria-selected="<?php echo $i == $initial_tab_index ? 'true' : 'false'; ?>"
			tabindex="0"
		>
			<div class="sow-tabs-title <?php echo empty( $tab['after_title'] ) ? 'sow-tabs-title-icon-left' : 'sow-tabs-title-icon-right'; ?>">
				<?php echo $tab['before_title']; ?>
				<?php echo wp_kses_post( $tab['title'] ); ?>
				<?php echo $tab['after_title']; ?>
			</div>
		</div>
	<?php } ?>
	</div>

	<div class="sow-tabs-panel-container">
	<?php foreach ( $tabs as $i => $tab ) { ?>
		<div class="sow-tabs-panel">
			<div
				class="sow-tabs-panel-content"
				role="tabpanel"
				aria-hidden="<?php echo $i != $initial_tab_index ? 'true' : 'false'; ?>"
			>
				<?php $this->render_panel_content( $tab, $instance ); ?>
			</div>
		</div>
	<?php } ?>
	</div>
</div>
