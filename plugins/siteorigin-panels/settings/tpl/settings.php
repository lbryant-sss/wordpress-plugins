<div class="wrap" id="panels-settings-page">
	<div class="settings-banner">

		<span class="icon">
			<img src="<?php echo esc_url( siteorigin_panels_url( 'settings/images/icon-layer.png' ) ); ?>" class="layer-3" />
			<img src="<?php echo esc_url( siteorigin_panels_url( 'settings/images/icon-layer.png' ) ); ?>" class="layer-2" />
			<img src="<?php echo esc_url( siteorigin_panels_url( 'settings/images/icon-layer.png' ) ); ?>" class="layer-1" />
		</span>
		<h1><?php esc_html_e( 'SiteOrigin Page Builder', 'siteorigin-panels' ); ?></h1>

		<div id="panels-settings-search">
			<input type="search" placeholder="<?php esc_attr_e( 'Search Settings', 'siteorigin-panels' ); ?>" />

			<ul class="results">
			</ul>
		</div>
	</div>

	<ul class="settings-nav">
		<?php
		foreach ( $settings_fields as $section_id => $section ) {
			?>
			<li><a href="#<?php echo esc_attr( $section_id ); ?>"><?php echo esc_html( $section['title'] ); ?></a></li>
			<?php
		}
		?>
		<li><a href="#welcome"><?php esc_html_e( 'Welcome', 'siteorigin-panels' ); ?></a></li>
	</ul>

	<?php if ( $this->settings_saved ) { ?>
		<div id="setting-error-settings_updated" class="updated settings-error">
			<p><strong><?php esc_html_e( 'Settings Saved', 'siteorigin-panels' ); ?></strong></p>
		</div>
	<?php } ?>

	<form action="<?php echo esc_url( admin_url( 'options-general.php?page=siteorigin_panels' ) ); ?>" method="post" >

		<div id="panels-settings-sections">
			<?php
			foreach ( $settings_fields as $section_id => $section ) {
				?>
			<div id="panels-settings-section-<?php echo esc_attr( $section_id ); ?>" class="panels-settings-section" data-section="<?php echo esc_attr( $section_id ); ?>">
				<table class="form-table">
					<tbody>
						<?php foreach ( $section['fields'] as $field_id => $field ) { ?>
							<tr class="panels-setting">
								<th scope="row">
									<label><?php echo esc_html( $field['label'] ); ?></label></th>
								<td>
									<?php
									$this->display_field( $field_id, $field );

									if ( ! empty( $field['description'] ) ) {
										?>
										<small class="description" data-keywords="
										<?php
										if ( ! empty( $field['keywords'] ) ) {
											echo esc_attr( $field['keywords'] );
										}
										?>
										">
											<?php
											echo wp_kses(
												$field['description'],
												array(
													'a' => array(
														'href' => array(),
														'title' => array(),
													),
													'em' => array(),
													'strong' => array(),
												)
											);
											?>
										</small>
									<?php } ?>
								</td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
			</div>
				<?php
			}
			?>

			<div id="panels-settings-section-welcome" class="panels-settings-section" data-section="welcome">
				<?php require plugin_dir_path( __FILE__ ) . 'welcome.php'; ?>
			</div>

		</div>

		<div class="submit">
			<?php wp_nonce_field( 'panels-settings' ); ?>
			<input type="submit" value="<?php esc_html_e( 'Save Settings', 'siteorigin-panels' ); ?>" class="button-primary" />
		</div>
	</form>

</div>
