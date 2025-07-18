<?php
/**
 * Admin View: Page - Forms
 *
 * @package UserRegistration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="wrap user-registration__wrap ur-form-container">
	<h1 style="display:none"></h1> <!-- To manage notices -->
	<div id="menu-management-liquid" class="ur-form-subcontainer">
		<div class="ur-loading-container">
			<div class="ur-circle-loading"></div>
		</div>
		<div id="menu-management">
			<div class="menu-edit">
				<input type="hidden" name="ur_form_id" id="ur_form_id" value="<?php echo esc_attr( $form_id ); ?>"/>
				<div id="nav-menu-header">
					<div class="ur-brand-logo ur-px-2">
						<img src="<?php echo esc_url( UR()->plugin_url() . '/assets/images/logo.svg' ); ?>" alt="">
					</div>
					<?php
					if ( ! empty( $form_data ) ) {
						?>
						<span class="ur-form-title"><?php echo isset( $form_data->post_title ) ? esc_html( $form_data->post_title ) : ''; ?></span>
						<span class="ur-editing-tag"><?php esc_html_e( 'Now Editing', 'user-registration' ); ?></span>
						<?php
					}
					?>
					<div class="major-publishing-actions wp-clearfix">
						<div class="publishing-action">
							<?php
							if ( ! empty( $form_data ) ) {
								?>
								<input type="text" onfocus="this.select();" readonly="readonly"
									value='[user_registration_form id=<?php echo '"' . esc_attr( $form_id ) . '"'; ?>]'
									class=" code" size="35">

								<button id="copy-shortcode" class="button button-primary button-large ur-copy-shortcode " href="#" data-tip="<?php esc_attr_e( 'Copy Shortcode!', 'user-registration' ); ?>" data-copied="<?php esc_attr_e( 'Copied!', 'user-registration' ); ?>">
									<!-- <svg xmlns="http://www.w3.org/2000/svg" width="14" height="16" viewBox="0 0 14 16">
										<path fill-rule="evenodd" d="M2 13h4v1H2v-1zm5-6H2v1h5V7zm2 3V8l-3 3 3 3v-2h5v-2H9zM4.5 9H2v1h2.5V9zM2 12h2.5v-1H2v1zm9 1h1v2c-.02.28-.11.52-.3.7-.19.18-.42.28-.7.3H1c-.55 0-1-.45-1-1V4c0-.55.45-1 1-1h3c0-1.11.89-2 2-2 1.11 0 2 .89 2 2h3c.55 0 1 .45 1 1v5h-1V6H1v9h10v-2zM2 5h8c0-.55-.45-1-1-1H8c-.55 0-1-.45-1-1s-.45-1-1-1-1 .45-1 1-.45 1-1 1H3c-.55 0-1 .45-1 1z"/>
									</svg> -->
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
											<path fill="#383838" fill-rule="evenodd" d="M3.116 3.116A1.25 1.25 0 0 1 4 2.75h9A1.25 1.25 0 0 1 14.25 4v1a.75.75 0 0 0 1.5 0V4A2.75 2.75 0 0 0 13 1.25H4A2.75 2.75 0 0 0 1.25 4v9A2.75 2.75 0 0 0 4 15.75h1a.75.75 0 0 0 0-1.5H4A1.25 1.25 0 0 1 2.75 13V4c0-.332.132-.65.366-.884ZM9.75 11c0-.69.56-1.25 1.25-1.25h9c.69 0 1.25.56 1.25 1.25v9c0 .69-.56 1.25-1.25 1.25h-9c-.69 0-1.25-.56-1.25-1.25v-9ZM11 8.25A2.75 2.75 0 0 0 8.25 11v9A2.75 2.75 0 0 0 11 22.75h9A2.75 2.75 0 0 0 22.75 20v-9A2.75 2.75 0 0 0 20 8.25h-9Z" clip-rule="evenodd"/>
									</svg>
								</button>
								<?php
							}
							?>
							<button id="ur-full-screen-mode" class="button button-secondary button-large button-icon closed" title="<?php esc_attr_e( 'Fullscreen', 'user-registration' ); ?>"><span class="ur-fs-open-label dashicons dashicons-editor-expand"></span><span class="ur-fs-close-label dashicons dashicons-editor-contract"></span></button>
							<?php
							if ( isset( $preview_link ) ) {
								?>
								<a href="<?php echo esc_url( ( $preview_link ) ); ?>" rel="noreferrer noopener" target="_blank" class="button button-secondary button-large" title="<?php esc_attr_e( 'Preview Form', 'user-registration' ); ?>"><?php esc_html_e( 'Preview', 'user-registration' ); ?></a>
							<?php } ?>
				<button name="embed_form" data-form_id="<?php echo esc_html( isset( $_GET['edit-registration'] ) ? absint( sanitize_text_field( wp_unslash( $_GET['edit-registration'] ) ) ) : 0 ); ?>" class="button  button-large ur-embed-form-button" type="button" value="<?php esc_attr_e( 'Embed', 'user-registration' ); ?>"><?php esc_html_e( 'Embed', 'user-registration' ); ?></button>
							<button type="button" name="save_form" id="save_form_footer" class="button button-primary button-large menu-form ur_save_form_action_button"> <?php echo esc_html( $save_label ); ?> </button>
						</div><!-- END .publishing-action -->
					</div><!-- END .major-publishing-actions -->
				</div><!-- END .nav-menu-header -->
				<div id="post-body">
					<div class="ur-registered-from">
						<div class="ur-registered-inputs">
							<nav class="nav-tab-wrapper ur-tabs">
								<ul class="ur-tab-lists">
									<li><a href="#ur-tab-registered-fields"
										class="nav-tab active"><?php esc_html_e( 'Fields', 'user-registration' ); ?></a>
									</li>
									<li><a href="#ur-tab-field-options" class="nav-tab"><?php esc_html_e( 'Field Options', 'user-registration' ); ?></a>
									</li>

									<?php
									/**
									 * Filter to add form builder tabs.
									 */
									do_action( 'user_registration_form_bulder_tabs' ); // TODO:: Needs refactor. Move after field-settings and sort.
									?>

									<li><a href="#ur-tab-field-settings"
										class="nav-tab"><?php esc_html_e( 'Form Settings', 'user-registration' ); ?></a>
									</li>
								</ul>
								<div style="clear:both"></div>

								<div class="ur-tab-contents" >
									<div id="ur-tab-registered-fields" class="ur-tab-content">
										<div class="ur-search-input ur-search-fields">
											<input id="ur-search-fields" class="ur-type-text" type="text" placeholder="Search Fields..." />
											<svg xmlns="http://www.w3.org/2000/svg" height="24px" width="24px" viewBox="0 0 24 24" fill="#a1a4b9"><path d="M21.71,20.29,18,16.61A9,9,0,1,0,16.61,18l3.68,3.68a1,1,0,0,0,1.42,0A1,1,0,0,0,21.71,20.29ZM11,18a7,7,0,1,1,7-7A7,7,0,0,1,11,18Z"/></svg>
										</div>
										<div class="ur-fields-not-found" hidden>
											<img src="<?php echo esc_url( plugin_dir_url( UR_PLUGIN_FILE ) . 'assets/images/not-found.png' ); ?>" />
											<h3 class="ur-fields-not-found-title"><?php esc_html_e( 'Whoops!', 'user-registration' ); ?></h3>
											<span><?php esc_html_e( 'There is not any field that you were searching for.', 'user-registration' ); ?></span>
										</div>
										<h2 class='ur-toggle-heading closed'><?php esc_html_e( 'Default User Fields', 'user-registration' ); ?></h2>
										<hr/>
										<?php $this->get_registered_user_form_fields(); ?>
										<h2 class='ur-toggle-heading closed'><?php esc_html_e( 'Extra Fields', 'user-registration' ); ?></h2>
										<hr/>
										<?php $this->get_registered_other_form_fields(); ?>
										<?php do_action( 'user_registration_extra_fields' ); ?>
									</div>
									<div id="ur-tab-field-options" class="ur-tab-content">
									</div>
									<div id="ur-tab-field-settings" class="ur-tab-content">

										<form method="post" id="ur-field-settings" onsubmit="return false;" style='display:none'>
											<div id="ur-field-all-settings">
												<?php ur_admin_form_settings( $form_id ); ?>
												<?php
												/**
												 * Action to add settings after form.
												 *
												 * @param int $form_id Form if to add the settings.
												 */
												do_action( 'user_registration_after_form_settings', $form_id );
												?>
											</div>
										</form>
									</div>
									<?php
									/**
									 * Action to add form builder content.
									 *
									 * @param int $form_id Form id to add form builder content.
									 */
									do_action( 'user_registration_form_bulder_content', $form_id );
									?>
								</div>
							</nav>
							<button id="ur-collapse" class="close">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
									<path fill="#6B6B6B" d="M16.5 22a1.003 1.003 0 0 1-.71-.29l-9-9a1 1 0 0 1 0-1.42l9-9a1.004 1.004 0 1 1 1.42 1.42L8.91 12l8.3 8.29A.999.999 0 0 1 16.5 22Z"/>
								</svg>
							</button>
						</div>
						<?php
						/**
						 * Filter to add the builder class.
						 *
						 * @param array.
						 */
						$builder_class = apply_filters( 'user_registration_builder_class', array() );
						$builder_class = implode( ' ', $builder_class );
						?>
						<div class='ur-builder-wrapper <?php echo esc_attr( $builder_class ); ?>'>
							<?php
							if ( ! empty( $form_data ) && isset( $_GET['edit-registration'] ) && is_numeric( $_GET['edit-registration'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
								$this->get_edit_form_field( $form_data );
							} else {
								?>
								<div class="ur-selected-inputs">
									<div class="ur-builder-wrapper-content">
										<div class="user-registration-editable-title ur-form-name-wrapper ur-my-4" >
											<input name="ur-form-name" id="ur-form-name" type="text" class="user-registration-editable-title__input ur-form-name regular-text menu-item-textbox ur-editing" autofocus="autofocus" onfocus="this.select()" value="<?php esc_html_e( 'Untitled', 'user-registration' ); ?>" data-editing="false">
											<span id="ur-form-name-edit-button" class="user-registration-editable-title__icon ur-edit-form-name dashicons dashicons-edit"></span>
										</div>
										<div class="ur-input-grids">

										</div>
									</div>
								</div>
							<?php } ?>
							<div class="ur-builder-wrapper-footer">
								<a href='#' class="ur-button-quick-links" title="Quick Links"><span>?</span></a>
								<ul class="ur-quick-links-content" hidden>
									<li><a href="#" id="ur-keyboard-shortcut-link"><?php echo esc_html__( 'Keyboard Shortcuts', 'user-registration' ); ?></a></li>
									<li><a href="https://wpuserregistration.com/support" rel="noreferrer noopener" target='_blank'><?php echo esc_html__( 'Get Support', 'user-registration' ); ?></a></li>
									<li><a href="https://docs.wpuserregistration.com/docs/how-to-show-login-form/" rel="noreferrer noopener" target='_blank'><?php echo esc_html__( 'Create Login Form', 'user-registration' ); ?></a></li>
									<li><a href="https://docs.wpuserregistration.com/" rel="noreferrer noopener" target='_blank'><?php echo esc_html__( 'Documentation', 'user-registration' ); ?></a></li>
								</ul>
								<?php
								/**
								 * Filter to add form builder wrapper for footer.
								 */
								do_action( 'user_registration_form_builder_wrapper_footer' );
								?>
							</div>
						</div>
					</div>
				</div><!-- /#post-body -->
			</div><!-- /.menu-edit -->
		</div><!-- /#menu-management -->
	</div>
</div>
