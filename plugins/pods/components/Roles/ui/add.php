<div class="wrap pods-admin">
	<div id="icon-pods" class="icon32"><br /></div>

	<form action="" method="post" class="pods-submittable">
		<div class="pods-submittable-fields">
			<?php echo PodsForm::field( 'action', 'pods_admin_components', 'hidden' ); ?>
			<?php echo PodsForm::field( 'component', $component, 'hidden' ); ?>
			<?php echo PodsForm::field( 'method', $method, 'hidden' ); ?>
			<?php echo PodsForm::field( '_wpnonce', wp_create_nonce( 'pods-component-' . $component . '-' . $method ), 'hidden' ); ?>

			<h2 class="italicized"><?php _e( 'Roles &amp; Capabilities: Add New Role', 'pods' ); ?></h2>

			<img src="<?php echo esc_url( PODS_URL ); ?>ui/images/pods-logo-notext-rgb-transparent.png" class="pods-leaf-watermark-right" />

			<div id="pods-wizard-box" class="pods-wizard-steps-2">
				<div id="pods-wizard-heading">
					<ul>
						<li class="pods-wizard-menu-current" data-step="1">
							<i></i> <span>1</span> <?php esc_html_e( 'Step 1: Naming', 'pods' ); ?>
							<em></em>
						</li>
						<li data-step="2">
							<i></i> <span>2</span> <?php esc_html_e( 'Step 2: Capabilities', 'pods' ); ?>
							<em></em>
						</li>
					</ul>
				</div>
				<div id="pods-wizard-main">
					<div id="pods-wizard-panel-1" class="pods-wizard-panel">
						<div class="pods-wizard-content">
							<p><?php esc_html_e( 'Roles allow you to specify which capabilities a user should be able to do within WordPress.', 'pods' ); ?></p>
						</div>

						<div class="stuffbox">
							<h3><label for="link_name"><?php esc_html_e( 'Name your new Role', 'pods' ); ?></label></h3>

							<div class="inside pods-manage-field">
								<div class="pods-field__container">
									<?php
									echo PodsForm::label( 'role_label', __( 'Label', 'pods' ), __( 'Users will see this as the name of their role', 'pods' ) );
									echo PodsForm::field( 'role_label', pods_v( 'role_label', 'post' ), 'text', [
										'class' => 'pods-validate pods-validate-required',
									] );
									?>
								</div>

								<div class="pods-field__container">
									<?php
									echo PodsForm::label( 'role_name', __( 'Name', 'pods' ), __( 'You will use this name to programmatically reference this role throughout WordPress', 'pods' ) );
									echo PodsForm::field( 'role_name', pods_v( 'role_name', 'post' ), 'slug', [
										'slug_sluggable' => 'role_label',
										'class'          => 'pods-validate pods-validate-required pods-slugged-lower pods-slugged-sanitize-title',
									] );
									?>
								</div>
							</div>
						</div>
					</div>
					<div id="pods-wizard-panel-2" class="pods-wizard-panel pods-wizard-option-content">
						<div class="pods-wizard-content">
							<p><?php esc_html_e( 'Choose below which Capabilities you would like this new user role to have.', 'pods' ); ?></p>
						</div>

						<div class="stuffbox">
							<h3><label for="link_name"><?php esc_html_e( 'Assign the Capabilities for', 'pods' ); ?>
									<strong class="pods-slugged" data-sluggable="role_label"></strong></label></h3>

							<div class="inside pods-manage-field pods-dependency">
								<div class="pods-field-option-group">
									<div class="pods-pick-values pods-pick-checkbox pods-zebra">
										<p>
											<a href="#toggle" class="button" id="toggle-all"><?php esc_html_e( 'Toggle All Capabilities on / off', 'pods' ); ?></a>
										</p>

										<ul>
											<?php
											$zebra = false;

											foreach ( $capabilities as $capability ) {
												$checked = false;

												if ( in_array( $capability, $defaults, true ) ) {
													$checked = true;
												}

												$class = ( $zebra ? 'even' : 'odd' );

												$zebra = ( ! $zebra );
												?>
												<li class="pods-zebra-<?php echo esc_attr( $class ); ?>" data-capability="<?php echo esc_attr( $capability ); ?>">
													<?php echo PodsForm::field( 'capabilities[' . $capability . ']', pods_v( 'capabilities[' . $capability . ']', 'post', $checked ), 'boolean', [
														'boolean_yes_label' => $capability,
														'disable_dfv'       => true,
													] ); ?>
												</li>
												<?php
											}
											?>
										</ul>
									</div>
								</div>

								<div class="pods-field-option-group">
									<p class="pods-field-option-group-label">
										<?php
										echo PodsForm::label( 'custom_capabilities[0]', __( 'Custom Capabilities', 'pods' ), __( 'These capabilities will automatically be created and assigned to this role', 'pods' ) );
										?>
									</p>

									<div class="pods-pick-values pods-pick-checkbox">
										<ul id="custom-capabilities">
											<li class="pods-repeater hidden">
												<?php
												echo PodsForm::field( 'custom_capabilities[--1]', '', 'text', [
													'disable_dfv' => true,
												] );
												?>
											</li>
											<li>
												<?php
												echo PodsForm::field( 'custom_capabilities[0]', '', 'text', [
													'disable_dfv' => true,
												] );
												?>
											</li>
										</ul>

										<p>
											<a href="#add-capability" id="add-capability" class="button">Add Another Custom Capability</a>
										</p>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div id="pods-wizard-actions" class="pods-wizard-button-interface">
						<div id="pods-wizard-toolbar">
							<button id="pods-wizard-start" class="button button-secondary hidden"><?php esc_html_e( 'Start Over', 'pods' ); ?></button>
							<button id="pods-wizard-next" class="button button-primary" data-next="<?php esc_attr_e( 'Next Step', 'pods' ); ?>" data-finished="<?php esc_attr_e( 'Finished', 'pods' ); ?>" data-processing="<?php esc_attr_e( 'Processing', 'pods' ); ?>.."><?php esc_html_e( 'Next Step', 'pods' ); ?></button>
						</div>
						<div id="pods-wizard-finished">

						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
	var pods_admin_submit_callback = function ( id ) {
		id = parseInt( id );
		document.location = 'admin.php?page=pods-component-<?php echo esc_js( $component ); ?>&do=create';
	};

	jQuery( function ( $ ) {
		$( document ).Pods( 'validate' );
		$( document ).Pods( 'submit' );
		$( document ).Pods( 'wizard' );
		$( document ).Pods( 'dependency' );
		$( document ).Pods( 'advanced' );
		$( document ).Pods( 'confirm' );
		$( document ).Pods( 'sluggable' );

		var toggle_all = true;

		$( '#toggle-all' ).on( 'click', function ( e ) {
			e.preventDefault();

			$( '.pods-field.pods-boolean input[type="checkbox"]' ).prop( 'checked', toggle_all );

			toggle_all = (!toggle_all);
		} );

		$( '#add-capability' ).on( 'click', function ( e ) {
			e.preventDefault();

			var new_id = $( 'ul#custom-capabilities li' ).length;
			var html = $( 'ul#custom-capabilities li.pods-repeater' ).html().replace( '--1', new_id );

			$( 'ul#custom-capabilities' ).append( '<li id="capability-' + new_id + '">' + html + '</li>' );
			$( 'li#capability-' + new_id + ' input' ).focus();
		} );
	} );
</script>
