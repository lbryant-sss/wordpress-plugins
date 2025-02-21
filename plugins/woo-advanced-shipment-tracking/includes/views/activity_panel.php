<div class="menu-container">
	<button class="menu-button">
		<span class="menu-icon">
			<span class="dashicons dashicons-menu-alt"></span>
		</span>
	</button>
	<div class="popup-menu">
		<?php
		$support_link = class_exists( 'ast_pro' ) ? 'https://www.zorem.com/?support=1' : 'https://wordpress.org/support/plugin/woo-advanced-shipment-tracking/#new-topic-0' ;
		// Plugin directory URL
		$plugin_url = esc_url( wc_advanced_shipment_tracking()->plugin_dir_url() );
		?>
		<a href="<?php echo esc_url( $support_link ); ?>" class="menu-item" target="_blank" >
			<span class="menu-icon">
				<img src="<?php echo esc_attr( $plugin_url ); ?>assets/images/get-support-icon-20.svg" alt="Get Support">
			</span>
			Get Support
		</a>
		<a href="https://docs.zorem.com/docs/ast-free/" class="menu-item" target="_blank">
			<span class="menu-icon">
				<img src="<?php echo esc_attr( $plugin_url ); ?>assets/images/documentation-icon-20.svg" alt="Documentation">
			</span>
			Documentation
		</a>
		<?php if ( !class_exists( 'ast_pro' ) ) { ?>
			<a href="https://www.zorem.com/ast-pro/?utm_source=wp-admin&utm_medium=plugin-setting&utm_campaign=upgrade-now" class="menu-item" target="_blank">
				<span class="menu-icon">
					<img src="<?php echo esc_attr( $plugin_url ); ?>assets/images/upgrade-to-pro-23.svg" alt="Upgrade To Pro">
				</span>
				Upgrade To Pro
			</a>
		<?php } ?>
	</div>
</div>
