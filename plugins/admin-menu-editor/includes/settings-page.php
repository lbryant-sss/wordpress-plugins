<?php
/**
 * This is the HTML template for the plugin settings page.
 *
 * These variables are provided by the plugin:
 * @var array $settings Plugin settings.
 * @var string $editor_page_url A fully qualified URL of the admin menu editor page.
 * @var string $settings_page_url
 * @var string $db_option_name
 */

$currentUser = wp_get_current_user();
$isMultisite = is_multisite();
$isSuperAdmin = is_super_admin();
$formActionUrl = add_query_arg('noheader', 1, $settings_page_url);
$isProVersion = apply_filters('admin_menu_editor_is_pro', false);
?>

<?php do_action('admin_menu_editor-display_header'); ?>

	<form method="post" action="<?php echo esc_url($formActionUrl); ?>" id="ws_plugin_settings_form">

		<table class="form-table">
			<tbody>
			<tr>
				<th scope="row">
					Who can access this plugin
				</th>
				<td>
					<fieldset>
						<p>
							<label>
								<input type="radio" name="plugin_access" value="super_admin"
									<?php checked('super_admin', $settings['plugin_access']); ?>
									<?php disabled( !$isSuperAdmin ); ?>>
								Super Admin

								<?php if ( !$isMultisite ) : ?>
									<br><span class="description">
										On a single site installation this is usually
										the same as the Administrator role.
									</span>
								<?php endif; ?>
							</label>
						</p>

						<p>
							<label>
								<input type="radio" name="plugin_access" value="manage_options"
									<?php checked('manage_options', $settings['plugin_access']); ?>
									<?php disabled( !current_user_can('manage_options') ); ?>>
								Anyone with the "manage_options" capability

								<br><span class="description">
									By default only Administrators have this capability.
								</span>
							</label>
						</p>

						<p>
							<label>
								<input type="radio" name="plugin_access" value="specific_user"
									<?php checked('specific_user', $settings['plugin_access']); ?>
									<?php disabled( $isMultisite && !$isSuperAdmin ); ?>>
								Only the current user

								<br>
								<span class="description">
									Login: <?php echo esc_html($currentUser->user_login); ?>,
								 	user ID: <?php echo esc_html(get_current_user_id()); ?>
								</span>
							</label>
						</p>
					</fieldset>

					<p>
						<label>
							<input type="checkbox" name="hide_plugin_from_others" value="1"
								<?php checked( $settings['plugins_page_allowed_user_id'] !== null ); ?>
								<?php disabled( !$isProVersion || ($isMultisite && !is_super_admin()) ); ?>
							>
							Hide "Admin Menu Editor<?php if ( $isProVersion ) { echo ' Pro'; } ?>"
							<?php if ( defined('WS_ADMIN_BAR_EDITOR_FILE') || defined('AME_BRANDING_ADD_ON_FILE') ) {
								echo 'and its add-ons';
							} ?>
							from the "Plugins" page for other users
							<?php if ( !$isProVersion ) {
								echo '(Pro version only)';
							} ?>
						</label>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					Multisite settings
				</th>
				<td>
					<fieldset id="ame-menu-scope-settings">
						<p>
							<label>
								<input type="radio" name="menu_config_scope" value="global"
								       id="ame-menu-config-scope-global"
									<?php checked('global', $settings['menu_config_scope']); ?>
									<?php disabled(!$isMultisite || !$isSuperAdmin); ?>>
								Global &mdash;
								Use the same admin menu settings for all network sites.
							</label>
						</p>

						<p>
							<label>
								<input type="radio" name="menu_config_scope" value="site"
									<?php checked('site', $settings['menu_config_scope']); ?>
									<?php disabled(!$isMultisite || !$isSuperAdmin); ?>>
								Per-site &mdash;
								Use different admin menu settings for each site.
							</label>
						</p>
					</fieldset>
				</td>
			</tr>

			<?php do_action('admin-menu-editor-display_addons'); ?>

			<tr id="ame-available-modules">
				<th scope="row">
					Modules
					<a class="ws_tooltip_trigger"
					   title="Modules are plugin features that can be turned on or off.
					&lt;br&gt;
					Turning off unused features will slightly increase performance and may help with certain compatibility issues.
					">
						<div class="dashicons dashicons-info"></div>
					</a>
				</th>
				<td>
					<fieldset>
						<?php
						global $wp_menu_editor;
						foreach ($wp_menu_editor->get_available_modules() as $moduleId => $module) {
							if ( !empty($module['isAlwaysActive']) ) {
								continue;
							}

							$isCompatible = $wp_menu_editor->is_module_compatible($module);
							$compatibilityNote = '';
							if ( !$isCompatible && !empty($module['requiredPhpVersion']) ) {
								if ( version_compare(phpversion(), $module['requiredPhpVersion'], '<') ) {
									$compatibilityNote = sprintf(
										'Required PHP version: %1$s or later. Installed PHP version: %2$s',
										esc_html($module['requiredPhpVersion']),
										esc_html(phpversion())
									);
								}
							}

							echo '<p>';
							/** @noinspection HtmlUnknownAttribute */
							printf(
								'<label>
									<input type="checkbox" name="active_modules[]" value="%1$s" %2$s %3$s>
								    %4$s
								</label>',
								esc_attr($moduleId),
								$wp_menu_editor->is_module_active($moduleId, $module) ? 'checked="checked"' : '',
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Constant strings.
								$isCompatible ? '' : 'disabled="disabled"',
								esc_html(!empty($module['title']) ? $module['title'] : $moduleId)
							);

							if ( !empty($compatibilityNote) ) {
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $compatibilityNote was escaped when generated.
								printf('<br><span class="description">%s</span>', $compatibilityNote);
							}

							echo '</p>';
						}
						?>
					</fieldset>
				</td>
			</tr>

			<tr>
				<th scope="row">Interface</th>
				<td>
					<p>
						<label>
							<input type="checkbox" name="hide_advanced_settings"
								<?php checked($settings['hide_advanced_settings']); ?>>
							Hide advanced menu options by default
						</label>
					</p>

					<?php if ($isProVersion): ?>
						<p>
						<label>
							<input type="checkbox" name="show_deprecated_hide_button"
								<?php checked($settings['show_deprecated_hide_button']); ?>>
							Enable the "Hide (cosmetic)" toolbar button
						</label>
						<br><span class="description">
							This button hides the selected menu item without making it inaccessible.
						</span>
						</p>
					<?php endif; ?>
				</td>
			</tr>

			<tr>
				<th scope="row">Editor colour scheme</th>
				<td>
					<fieldset>
						<p>
							<label>
								<input type="radio" name="ui_colour_scheme" value="classic"
									<?php checked('classic', $settings['ui_colour_scheme']); ?>>
								Blue and yellow
							</label>
						</p>

						<p>
							<label>
								<input type="radio" name="ui_colour_scheme" value="modern-one"
									<?php checked('modern-one', $settings['ui_colour_scheme']); ?>>
								Modern
							</label>
						</p>

						<p>
							<label>
								<input type="radio" name="ui_colour_scheme" value="wp-grey"
									<?php checked('wp-grey', $settings['ui_colour_scheme']); ?>>
								Grey
							</label>
						</p>
					</fieldset>
				</td>
			</tr>

			<?php if ($isProVersion): ?>
			<tr>
				<th scope="row">Show submenu icons</th>
				<td>
					<fieldset id="ame-submenu-icons-settings">
						<p>
							<label>
								<input type="radio" name="submenu_icons_enabled" value="always"
									<?php checked('always', $settings['submenu_icons_enabled']); ?>>
								Always
							</label>
						</p>

						<p>
							<label>
								<input type="radio" name="submenu_icons_enabled" value="if_custom"
									<?php checked('if_custom', $settings['submenu_icons_enabled']); ?>>
								Only when manually selected
							</label>
						</p>

						<p>
							<label>
								<input type="radio" name="submenu_icons_enabled" value="never"
									<?php checked('never', $settings['submenu_icons_enabled']); ?>>
								Never
							</label>
						</p>
					</fieldset>
				</td>
			</tr>

				<tr>
					<th scope="row">
						New menu visibility
						<a class="ws_tooltip_trigger"
						   title="This setting controls the default permissions of menu items that are
						    not present in the last saved menu configuration.
							&lt;br&gt;&lt;br&gt;
							This includes new menus added by plugins and themes.
							In Multisite, it also applies to menus that exist on some sites but not others.
							It doesn't affect menu items that you add through the Admin Menu Editor interface.">
							<div class="dashicons dashicons-info"></div>
						</a>
					</th>
					<td>
						<fieldset>
							<p>
								<label>
									<input type="radio" name="unused_item_permissions" value="unchanged"
										<?php checked('unchanged', $settings['unused_item_permissions']); ?>>
									Leave unchanged (default)

									<br><span class="description">
										No special restrictions. Visibility will depend on the plugin
										that added the menus.
									</span>
								</label>
							</p>

							<p>
								<label>
									<input type="radio" name="unused_item_permissions" value="match_plugin_access"
										<?php checked('match_plugin_access', $settings['unused_item_permissions']); ?>>
									Show only to users who can access this plugin

									<br><span class="description">
										Automatically hides all new and unrecognized menus from regular users.
										To make new menus visible, you have to manually enable them in the menu editor.
									</span>
								</label>
							</p>

						</fieldset>
					</td>
				</tr>
			<?php endif; ?>

			<tr>
			<th scope="row">
				New menu position
				<a class="ws_tooltip_trigger"
				   title="This setting controls the position of menu items that are not present in the last saved menu
					configuration.
					&lt;br&gt;&lt;br&gt;
					This includes new menus added by plugins and themes.
					In Multisite, it also applies to menus that exist only on certain sites but not on all sites.
					It doesn't affect menu items that you add through the Admin Menu Editor interface.">
					<div class="dashicons dashicons-info"></div>
				</a>
			</th>
			<td>
				<fieldset>
					<p>
						<label>
							<input type="radio" name="unused_item_position" value="relative"
								<?php checked('relative', $settings['unused_item_position']); ?>>
							Maintain relative order

							<br><span class="description">
								Attempts to put new items in the same relative positions
								as they would be in in the default admin menu.
							</span>
						</label>
					</p>

					<p>
						<label>
							<input type="radio" name="unused_item_position" value="bottom"
								<?php checked('bottom', $settings['unused_item_position']); ?>>
							Bottom

							<br><span class="description">
								Puts new items at the bottom of the admin menu.
							</span>
						</label>
					</p>

				</fieldset>
			</td>
			</tr>

			<?php
			//The free version lacks the ability to render deeply nested menus in the dashboard, so the nesting
			//options are hidden by default. However, if the user somehow acquires a configuration where this
			//feature is enabled (e.g. by importing config from the Pro version), the free version can display
			//and even edit that configuration to a limited extent.
			if ( $isProVersion || !empty($settings['was_nesting_ever_changed']) ):
				?>
				<tr>
					<th scope="row">
						Three level menus
						<a class="ws_tooltip_trigger ame-warning-tooltip"
						   title="Caution: Experimental feature.&lt;br&gt;
						   This feature might not work as expected and it could cause conflicts with other plugins or themes.">
							<div class="dashicons dashicons-admin-tools"></div>
						</a>
					</th>
					<td>
						<fieldset>
							<?php
							$nestingOptions = array(
								'Ask on first use'                                     => null,
								'Enabled' . ($isProVersion ? '' : ' (only in editor)') => true,
								'Disabled'                                             => false,
							);
							foreach ($nestingOptions as $label => $nestingSetting):
								?>
								<p>
									<label>
										<input type="radio" name="deep_nesting_enabled"
										       value="<?php echo esc_attr(wp_json_encode($nestingSetting)); ?>"
											<?php
											if ( $settings['deep_nesting_enabled'] === $nestingSetting ) {
												echo ' checked="checked"';
											}
											?>>
										<?php echo esc_html($label); ?>
									</label>
								</p>
							<?php endforeach; ?>
						</fieldset>
					</td>
				</tr>
			<?php endif; ?>

			<?php do_action('admin_menu_editor-settings_page_extra'); ?>

			<tr>
				<th scope="row">
					WPML support
				</th>
				<td>
					<p>
						<label>
							<input type="checkbox" name="wpml_support_enabled"
								<?php checked($settings['wpml_support_enabled']); ?>>
							Make edited menu titles translatable with WPML

							<br><span class="description">
								The titles will appear in the "Strings" section in WPML.
								If you don't use WPML or a similar translation plugin,
								you can safely disable this option.
							</span>
						</label>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					bbPress override
				</th>
				<td>
					<p>
						<label>
							<input type="checkbox" name="bbpress_override_enabled"
								<?php checked($settings['bbpress_override_enabled']); ?>>
							Prevent bbPress from resetting role capabilities

							<br><span class="description">
								By default, bbPress will automatically undo any changes that are made to dynamic
								bbPress roles. Enable this option to override that behaviour and make it possible
								to change bbPress role capabilities.
							</span>
						</label>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row">Error verbosity level</th>
				<td>
					<fieldset id="ame-submenu-icons-settings">
						<p>
							<label>
								<input type="radio" name="error_verbosity" value="<?php echo esc_attr(WPMenuEditor::VERBOSITY_LOW); ?>"
									<?php checked(WPMenuEditor::VERBOSITY_LOW, $settings['error_verbosity']); ?>>
								Low

								<br><span class="description">
									Shows a generic error message without any details.
								</span>
							</label>
						</p>

						<p>
							<label>
								<input type="radio" name="error_verbosity" value="<?php echo esc_attr(WPMenuEditor::VERBOSITY_NORMAL); ?>"
									<?php checked(WPMenuEditor::VERBOSITY_NORMAL, $settings['error_verbosity']); ?>>
								Normal

								<br><span class="description">
									Shows a one or two sentence explanation. For example: "The current user doesn't have
									the "manage_options" capability that is required to access the "Settings" menu item."
								</span>
							</label>
						</p>

						<p>
							<label>
								<input type="radio" name="error_verbosity" value="<?php echo esc_attr(WPMenuEditor::VERBOSITY_VERBOSE); ?>"
									<?php checked(WPMenuEditor::VERBOSITY_VERBOSE, $settings['error_verbosity']); ?>>
								Verbose

								<br><span class="description">
									Like "normal", but also includes a log of menu settings and permissions that
									caused the current menu to be hidden. Useful for debugging.
								</span>
							</label>
						</p>
					</fieldset>
				</td>
			</tr>

			<tr>
				<th scope="row">Debugging</th>
				<td>
					<p>
					<label>
						<input type="checkbox" name="security_logging_enabled"
							<?php checked($settings['security_logging_enabled']); ?>>
						Show menu access checks performed by the plugin on every admin page
					</label>
					<br><span class="description">
						This can help track down configuration problems and figure out why
						your menu permissions don't work the way they should.

						Note: It's not recommended to use this option on a live site as
						it can reveal information about your menu configuration.
					</span>
					</p>

					<p>
						<label>
							<input type="checkbox" name="force_custom_dashicons"
								<?php checked($settings['force_custom_dashicons']); ?>>
							Attempt to override menu icon CSS that was added by other plugins
						</label>
					</p>

					<p>
						<label>
							<input type="checkbox" name="delete_orphan_actor_settings"
								<?php checked($settings['delete_orphan_actor_settings']); ?>>
							Automatically delete settings associated with missing roles and users
						</label>
						<br><span class="description">
							Applies only to certain parts of the plugin. In Multisite, this option is disabled
							by default because different sites can potentially have different roles.
						</span>
					</p>

					<p>
						<label>
							<input type="checkbox" name="optimize_custom_menu_size"
								<?php checked($settings['optimize_custom_menu_size']); ?>>
							Optimize menu configuration size (default)
						</label>
						<br><span class="description">
							Eliminates some redundancy in the menu configuration, making it smaller.
						</span>
					</p>

					<p>
						<label>
							<input type="checkbox" name="compress_custom_menu"
								<?php checked($settings['compress_custom_menu']); ?>>
							Compress menu configuration data that's stored in the database
						</label>
						<br><span class="description">
							Significantly reduces the size of
							the <code><?php echo esc_html($db_option_name); ?></code> DB option,
							but adds decompression overhead to every page.
						</span>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row">Server info</th>
				<td>
					<figure>
						<figcaption>PHP error log:</figcaption>

						<code><?php
						echo esc_html(ini_get('error_log'));
						?></code>
					</figure>

					<figure>
						<figcaption>PHP memory usage:</figcaption>

						<?php
						printf(
							'%.2f MiB of %s',
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- We're using sprintf() to format a float.
							memory_get_peak_usage() / (1024 * 1024),
							esc_html(ini_get('memory_limit'))
						);
						?>
					</figure>
				</td>
			</tr>
			</tbody>
		</table>

		<input type="hidden" name="action" value="save_settings">
		<?php
		wp_nonce_field('save_settings');
		submit_button();
		?>
	</form>

<?php do_action('admin_menu_editor-display_footer'); ?>

<script type="text/javascript">
	jQuery(function($) {
		//Set up tooltips
		$('.ws_tooltip_trigger').qtip({
			style: {
				classes: 'qtip qtip-rounded ws_tooltip_node ws_wide_tooltip'
			}
		});
	});
</script>