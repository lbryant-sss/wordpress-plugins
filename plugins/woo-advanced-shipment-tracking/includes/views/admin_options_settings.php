<?php
/**
 * Html code for settings tab
 */
?>
<section id="content2" class="tab_section">
	<?php do_action( 'ast_settings_admin_notice'); ?>
	<form method="post" id="wc_ast_settings_form" action="" enctype="multipart/form-data">
		<div class="accordion_container">
			<div class="accordion_set">
				<div class="accordion heading add-tracking-option">
					<label>
						<?php esc_html_e( 'General Settings', 'woo-advanced-shipment-tracking' ); ?>
						<span class="ast-accordion-btn">
							<div class="spinner workflow_spinner" style="float:none"></div>
							<button name="save" class="button-primary woocommerce-save-button btn_ast2" type="submit" value="Save changes"><?php esc_html_e( 'Save & Close', 'woo-advanced-shipment-tracking' ); ?></button>
						</span>	
						<span class="dashicons dashicons-arrow-right-alt2"></span>
					</label>
				</div>
				<div class="panel options add-tracking-option">
					<?php $this->get_html_ul( $this->get_add_tracking_options() ); ?>
				</div>
			</div>
			
			<div class="accordion_set">
				<div class="accordion heading add-tracking-option">
					<label>
						<?php esc_html_e( 'Order Statuses & Notifications', 'woo-advanced-shipment-tracking' ); ?>
						<span class="ast-accordion-btn">
							<div class="spinner workflow_spinner" style="float:none"></div>
							<button name="save" class="button-primary woocommerce-save-button btn_ast2" type="submit" value="Save changes"><?php esc_html_e( 'Save & Close', 'woo-advanced-shipment-tracking' ); ?></button>
						</span>	
						<span class="dashicons dashicons-arrow-right-alt2"></span>
					</label>
				</div>
				<div class="panel options add-tracking-option">
					<?php require_once( 'admin_options_osm.php' ); ?>
				</div>
			</div>
			
			<div class="accordion_set">
				<div class="accordion heading add-tracking-option">
					<label>
						<?php esc_html_e( 'Shipment Tracking API', 'woo-advanced-shipment-tracking' ); ?>
						<span class="ast-accordion-btn">
							<div class="spinner workflow_spinner" style="float:none"></div>
							<button name="save" class="button-primary woocommerce-save-button btn_ast2" type="submit" value="Save changes"><?php esc_html_e( 'Save & Close', 'woo-advanced-shipment-tracking' ); ?></button>
						</span>	
						<span class="dashicons dashicons-arrow-right-alt2"></span>
					</label>
				</div>
				<div class="panel options add-tracking-option">
					<?php $this->get_html_ul( $this->get_shipment_tracking_api_options() ); ?>
				</div>
			</div>
			<div class="accordion_set">
				<div class="accordion heading ast_premium">
					<label>
						<?php esc_html_e( 'PayPal Tracking', 'woo-advanced-shipment-tracking' ); ?>
						<span class="ast_premium_badge">PRO</span>
						<span class="dashicons dashicons-arrow-right-alt2"></span>
					</label>
				</div>
			</div>
			<div class="accordion_set">
				<div class="accordion heading ast_premium">
					<label>
						<?php esc_html_e( 'Stripe Tracking', 'woo-advanced-shipment-tracking' ); ?>
						<span class="ast_premium_badge">PRO</span>
						<span class="dashicons dashicons-arrow-right-alt2"></span>
					</label>
				</div>					
			</div>
			<div class="accordion_set">
				<div class="accordion heading ast_premium">
					<label>
						<?php esc_html_e( 'Automated CSV Import through FTP/SFTP', 'woo-advanced-shipment-tracking' ); ?>
						<span class="ast_premium_badge">PRO</span>
						<span class="dashicons dashicons-arrow-right-alt2"></span>
					</label>
				</div>					
			</div>
		</div>		
		<?php wp_nonce_field( 'wc_ast_settings_form', 'wc_ast_settings_form_nonce' ); ?>
		<input type="hidden" name="action" value="wc_ast_settings_form_update">							
	</form>	
</section>
