<?php // Google Analytics - Settings Display

if (!function_exists('add_action')) die(); ?>

<div class="wrap">
	
	<h1><?php echo GAP_NAME; ?> <small><?php echo 'v'. GAP_VERSION; ?></small></h1>
	
	<div class="gap-toggle-all"><a href="<?php echo admin_url(GAP_PATH); ?>"><?php esc_html_e('Toggle all panels', 'ga-google-analytics'); ?></a></div>
	
	<form method="post" action="options.php">
		
		<?php settings_fields('gap_plugin_options'); ?>
		
		<div class="metabox-holder">
			
			<div class="meta-box-sortables ui-sortable">
				
				<div id="gap-panel-overview" class="postbox">
					
					<h2><?php esc_html_e('Overview', 'ga-google-analytics'); ?></h2>
					
					<div class="toggle<?php if (isset($_GET['settings-updated'])) echo ' default-hidden'; ?>">
						
						<div class="gap-panel-overview">
							
							<div class="gap-col-1">
								
								<p>
									<?php esc_html_e('This plugin adds the', 'ga-google-analytics'); ?> 
									<abbr title="<?php esc_attr_e('Google Analytics', 'ga-google-analytics'); ?>"><?php esc_html_e('GA', 'ga-google-analytics'); ?></abbr> 
									<?php esc_html_e('tracking snippet to your site. Log in to your Google account to view your stats.', 'ga-google-analytics'); ?>
								</p>
								
								<ul>
									<li><a class="gap-toggle" data-target="usage" href="#gap-panel-usage"><?php esc_html_e('How to Use', 'ga-google-analytics'); ?></a></li>
									<li><a class="gap-toggle" data-target="settings" href="#gap-panel-settings"><?php esc_html_e('Plugin Settings', 'ga-google-analytics'); ?></a></li>
									<li><a target="_blank" rel="noopener noreferrer" href="https://wordpress.org/plugins/ga-google-analytics/"><?php esc_html_e('Plugin Homepage', 'ga-google-analytics'); ?></a></li>
								</ul>
								
								<p>
									<?php esc_html_e('If you like this plugin, please', 'ga-google-analytics'); ?> 
									<a target="_blank" rel="noopener noreferrer" href="https://wordpress.org/support/plugin/ga-google-analytics/reviews/?rate=5#new-post" title="<?php esc_attr_e('THANK YOU for your support!', 'ga-google-analytics'); ?>">
										<?php esc_html_e('give it a 5-star rating', 'ga-google-analytics'); ?>&nbsp;&raquo;
									</a>
								</p>
								
							</div>
							
							<div class="gap-col-2">
								
								<p>
									🔥 <?php esc_html_e('Check out the', 'ga-google-analytics'); ?> 
									<a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/ga-google-analytics-pro/" title="<?php esc_html_e('GA Google Analytics Pro', 'ga-google-analytics'); ?>"><?php esc_html_e('PRO Version', 'ga-google-analytics'); ?>&nbsp;&raquo;</a>
								</p>
								
							</div>
							
						</div>
						
					</div>
					
				</div>
				
				<div id="gap-panel-usage" class="postbox">
					
					<h2><?php esc_html_e('How to Use', 'ga-google-analytics'); ?></h2>
					
					<div class="toggle default-hidden">
						
						<div class="gap-panel-usage">
							
							<p><strong><?php esc_html_e('How to add Google Analytics 4 to your site:', 'ga-google-analytics'); ?></strong></p>
							
							<ol>
								<li><?php esc_html_e('Follow', 'ga-google-analytics'); ?> <a target="_blank" rel="noopener noreferrer" href="https://support.google.com/analytics/answer/9304153"><?php esc_html_e('this guide', 'ga-google-analytics'); ?></a> <?php esc_html_e('to create your GA account', 'ga-google-analytics'); ?></li>
								<li><?php esc_html_e('During account creation, you&rsquo;ll get a tracking (measurement) ID', 'ga-google-analytics'); ?></li>
								<li><?php esc_html_e('Toggle open the "Plugin Settings" panel on this page (below)', 'ga-google-analytics'); ?></li>
								<li><?php esc_html_e('Add your Google tracking (measurement) ID to the plugin setting, "GA Tracking ID"', 'ga-google-analytics'); ?></li>
								<li><?php esc_html_e('Select "GA4" for the plugin setting, "Tracking Method"', 'ga-google-analytics'); ?></li>
								<li><?php esc_html_e('Configure any other plugin settings as desired (optional)', 'ga-google-analytics'); ?></li>
							</ol>
							
							<p><?php esc_html_e('Save changes and done. After 24-48 hours, you can log into your Google Analytics account to view your stats.', 'ga-google-analytics'); ?></p>
							
							<div class="gap-caption">
								<?php esc_html_e('Note that it can take 24-48 hours after adding the tracking code before any analytical data appears in your', 'ga-google-analytics'); ?> 
								<a target="_blank" rel="noopener noreferrer" href="https://www.google.com/analytics/"><?php esc_html_e('Google Analytics account', 'ga-google-analytics'); ?></a>. 
								<?php esc_html_e('To check that the GA tacking code is included properly, examine the source code of your web pages. Learn more at the', 'ga-google-analytics'); ?> 
								<a target="_blank" rel="noopener noreferrer" href="https://support.google.com/analytics/"><?php esc_html_e('Google Analytics Help Center', 'ga-google-analytics'); ?></a>.
							</div>
							
						</div>
						
					</div>
					
				</div>
				
				<div id="gap-panel-settings" class="postbox">
					
					<h2><?php esc_html_e('Plugin Settings', 'ga-google-analytics'); ?></h2>
					
					<div class="toggle<?php if (!isset($_GET['settings-updated'])) echo ' default-hidden'; ?>">
						
						<div class="gap-panel-settings">
							
							<table class="widefat">
								<tr>
									<th><label for="gap_options[gap_id]"><?php esc_html_e('GA Tracking ID', 'ga-google-analytics') ?></label></th>
									<td>
										<input id="gap_options[gap_id]" name="gap_options[gap_id]" type="text" size="30" maxlength="30" value="<?php if (isset($gap_options['gap_id'])) echo esc_attr($gap_options['gap_id']); ?>">
										<div class="gap-caption">
											<?php esc_html_e('Enter your Google Tracking ID.', 'ga-google-analytics'); ?> 
											<a class="toggle-link" href="#more-info"><?php esc_html_e('Show info', 'ga-google-analytics'); ?></a> 
											<span class="toggle-data"> <?php esc_html_e('Note: the Tracking ID also may be referred to as Tag ID, Measurement ID, or Property ID. Supported ID formats include AW-XXXXXXXXX, G-XXXXXXXXX, GT-XXXXXXXXX, and UA-XXXXXXXXX. Google Tag Manager (GTM-XXXXXXXXX) currently is not supported.', 'ga-google-analytics'); ?></span>
										</div>
									</td>
								</tr>
								<tr>
									<th><label for="gap_options[gap_enable]"><?php esc_html_e('Tracking Method', 'ga-google-analytics') ?></label></th>
									<td><?php echo $this->select_menu($this->options_libraries(), 'gap_enable'); ?></td>
								</tr>
							</table>
							
							<div class="gap-info-universal<?php if (isset($gap_options['gap_enable']) && $gap_options['gap_enable'] == 2) echo ' default-hidden'; ?>">
								
								<table class="widefat">
									<tr>
										<th><label for="gap_options[gap_display_ads]"><?php esc_html_e('Display Advertising', 'ga-google-analytics') ?></label></th>
										<td>
											<input id="gap_options[gap_display_ads]" name="gap_options[gap_display_ads]" type="checkbox" value="1" <?php if (isset($gap_options['gap_display_ads'])) checked('1', $gap_options['gap_display_ads']); ?>> 
											<?php esc_html_e('Enable support for', 'ga-google-analytics'); ?> 
											<a target="_blank" rel="noopener noreferrer" href="https://support.google.com/analytics/answer/2444872"><?php esc_html_e('Display Advertising', 'ga-google-analytics'); ?></a>
										</td>
									</tr>
									<tr>
										<th><label for="gap_options[link_attr]"><?php esc_html_e('Link Attribution', 'ga-google-analytics') ?></label></th>
										<td>
											<input id="gap_options[link_attr]" name="gap_options[link_attr]" type="checkbox" value="1" <?php if (isset($gap_options['link_attr'])) checked('1', $gap_options['link_attr']); ?>> 
											<?php esc_html_e('Enable support for', 'ga-google-analytics'); ?> 
											<a target="_blank" rel="noopener noreferrer" href="https://support.google.com/analytics/answer/7377126"><?php esc_html_e('Enhanced Link Attribution', 'ga-google-analytics'); ?></a>
										</td>
									</tr>
									<tr>
										<th><label for="gap_options[gap_anonymize]"><?php esc_html_e('IP Anonymization', 'ga-google-analytics') ?></label></th>
										<td>
											<input id="gap_options[gap_anonymize]" name="gap_options[gap_anonymize]" type="checkbox" value="1" <?php if (isset($gap_options['gap_anonymize'])) checked('1', $gap_options['gap_anonymize']); ?>> 
											<?php esc_html_e('Enable support for', 'ga-google-analytics'); ?> 
											<a target="_blank" rel="noopener noreferrer" href="https://support.google.com/analytics/answer/2763052"><?php esc_html_e('IP Anonymization', 'ga-google-analytics'); ?></a>
										</td>
									</tr>
									<tr>
										<th><label for="gap_options[gap_force_ssl]"><?php esc_html_e('Force SSL', 'ga-google-analytics') ?></label></th>
										<td>
											<input id="gap_options[gap_force_ssl]" name="gap_options[gap_force_ssl]" type="checkbox" value="1" <?php if (isset($gap_options['gap_force_ssl'])) checked('1', $gap_options['gap_force_ssl']); ?>>
											<?php esc_html_e('Enable support for Force SSL', 'ga-google-analytics'); ?>
										</td>
									</tr>
								</table>
								
							</div>
							
							<table class="widefat">
								<tr>
									<th><label for="gap_options[gap_location]"><?php esc_html_e('Tracking Code Location', 'ga-google-analytics'); ?></label></th>
									<td>
										<?php echo $this->select_menu($this->options_locations(), 'gap_location'); ?>
										<div class="gap-caption">
											<?php esc_html_e('Tip: Google recommends including the tracking code in the page head, but including it in the footer can benefit page performance.', 'ga-google-analytics'); ?> 
											<?php esc_html_e('If in doubt, go with the head option.', 'ga-google-analytics'); ?>
										</div>
									</td>
								</tr>
								<tr>
									<th><label for="gap_options[tracker_object]"><?php esc_html_e('Custom Tracker Objects', 'ga-google-analytics'); ?></label></th>
									<td>
										<textarea id="gap_options[tracker_object]" name="gap_options[tracker_object]" type="textarea" rows="4" cols="70"><?php if (isset($gap_options['tracker_object'])) echo esc_textarea($gap_options['tracker_object']); ?></textarea>
										<div class="gap-caption"> 
											<?php esc_html_e('Optional code added to', 'ga-google-analytics'); ?> <span class="gap-code">gtag('config')</span> 
											<?php esc_html_e('for GA4, or added to', 'ga-google-analytics'); ?> <span class="gap-code">ga('create')</span> 
											<?php esc_html_e('for Universal Analytics.', 'ga-google-analytics'); ?>
										</div>
									</td>
								</tr>
								<tr>
									<th><label for="gap_options[gap_custom_code]"><?php esc_html_e('Custom GA Code', 'ga-google-analytics'); ?></label></th>
									<td>
										<textarea id="gap_options[gap_custom_code]" name="gap_options[gap_custom_code]" type="textarea" rows="4" cols="70"><?php if (isset($gap_options['gap_custom_code'])) echo esc_textarea($gap_options['gap_custom_code']); ?></textarea>
										<div class="gap-caption"> 
											<?php esc_html_e('Optional code added to the GA tracking snippet. This is useful for things like creating multiple trackers and user opt-out. Note: you can use', 'ga-google-analytics'); ?> 
											<span class="gap-code">%%userid%%</span> <?php esc_html_e('and', 'ga-google-analytics'); ?> <span class="gap-code">%%username%%</span> 
											<?php esc_html_e('to get the current user ID and login name.', 'ga-google-analytics'); ?>
										</div>
									</td>
								</tr>
								<tr>
									<th><label for="gap_options[gap_custom]"><?php esc_html_e('Custom Code', 'ga-google-analytics'); ?></label></th>
									<td>
										<textarea id="gap_options[gap_custom]" name="gap_options[gap_custom]" type="textarea" rows="4" cols="70"><?php if (isset($gap_options['gap_custom'])) echo esc_textarea($gap_options['gap_custom']); ?></textarea>
										<div class="gap-caption">
											<?php esc_html_e('Optional markup added to', 'ga-google-analytics'); ?> <span class="gap-code">&lt;head&gt;</span> 
											<?php esc_html_e('or footer, depending on the previous setting, Tracking Code Location.', 'ga-google-analytics'); ?>
										</div>
									</td>
								</tr>
								<tr>
									<th><label for="gap_options[gap_custom_loc]"><?php esc_html_e('Custom Code Location', 'ga-google-analytics'); ?></label></th>
									<td>
										<input id="gap_options[gap_custom_loc]" name="gap_options[gap_custom_loc]" type="checkbox" value="1" <?php if (isset($gap_options['gap_custom_loc'])) checked('1', $gap_options['gap_custom_loc']); ?>> 
										<?php esc_html_e('Display Custom Code', 'ga-google-analytics'); ?> <em><?php esc_html_e('before', 'ga-google-analytics'); ?></em> 
										<?php esc_html_e('the GA tracking code (leave unchecked to display', 'ga-google-analytics'); ?> <em><?php esc_html_e('after', 'ga-google-analytics'); ?></em> <?php esc_html_e('the tracking code)', 'ga-google-analytics'); ?>
									</td>
								</tr>
								<tr>
									<th><label for="gap_options[admin_area]"><?php esc_html_e('Admin Area', 'ga-google-analytics') ?></label></th>
									<td>
										<input id="gap_options[admin_area]" name="gap_options[admin_area]" type="checkbox" value="1" <?php if (isset($gap_options['admin_area'])) checked('1', $gap_options['admin_area']); ?>> 
										<?php esc_html_e('Enable tracking in WP Admin Area (adds tracking code only; to view stats log into your Google account)', 'ga-google-analytics'); ?>
									</td>
								</tr>
								<tr>
									<th><label for="gap_options[disable_admin]"><?php esc_html_e('Admin Users', 'ga-google-analytics') ?></label></th>
									<td>
										<input id="gap_options[disable_admin]" name="gap_options[disable_admin]" type="checkbox" value="1" <?php if (isset($gap_options['disable_admin'])) checked('1', $gap_options['disable_admin']); ?>> 
										<?php esc_html_e('Disable tracking of Admin-level users', 'ga-google-analytics') ?>
									</td>
								</tr>
								<tr>
									<th><label><?php esc_html_e('More Options', 'ga-google-analytics') ?></label></th>
									<td class="ga-pro-info">
										<?php esc_html_e('For advanced features, check out', 'ga-google-analytics') ?> 
										<a target="_blank" rel="noopener noreferrer" href="https://plugin-planet.com/ga-google-analytics-pro/"><?php esc_html_e('GA Google Analytics Pro', 'ga-google-analytics'); ?>&nbsp;&raquo;</a>
									</td>
								</tr>
							</table>
							
						</div>
						
						<input type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes', 'ga-google-analytics'); ?>" />
						
					</div>
					
				</div>
				
				<div id="gap-panel-restore" class="postbox">
					
					<h2><?php esc_html_e('Restore Defaults', 'ga-google-analytics'); ?></h2>
					
					<div class="toggle default-hidden">
						
						<p><?php esc_html_e('Click the link to restore the default plugin options.', 'ga-google-analytics'); ?></p>
						
						<p><?php echo $this->callback_reset(); ?></p>
						
					</div>
					
				</div>
				
				<div id="gap-panel-current" class="postbox">
					
					<h2><?php esc_html_e('WP Resources', 'ga-google-analytics'); ?></h2>
					
					<div class="toggle<?php if (isset($_GET['settings-updated'])) echo ' default-hidden'; ?>">
						
						<?php require_once GAP_DIR .'inc/support-panel.php'; ?>
						
					</div>
					
				</div>
				
			</div>
			
		</div>
		
		<div class="gap-credit-info">
			
			<a target="_blank" rel="noopener noreferrer" href="<?php echo esc_url(GAP_HOME); ?>" title="<?php esc_attr_e('Plugin Homepage', 'ga-google-analytics'); ?>"><?php echo GAP_NAME; ?></a> 
			<?php esc_attr_e('by', 'ga-google-analytics'); ?> 
			<a target="_blank" rel="noopener noreferrer" href="https://x.com/perishable" title="<?php esc_attr_e('Jeff Starr on X (Twitter)', 'ga-google-analytics'); ?>">Jeff Starr</a> 
			<?php esc_attr_e('@', 'ga-google-analytics'); ?> 
			<a target="_blank" rel="noopener noreferrer" href="https://monzillamedia.com/" title="<?php esc_attr_e('Obsessive Web Development', 'ga-google-analytics'); ?>">Monzilla Media</a>
			
		</div>
		
	</form>
	
</div>