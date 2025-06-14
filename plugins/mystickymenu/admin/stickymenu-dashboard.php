<?php 
	$stickymenus_widgets = get_option( 'mysticky_option_welcomebar' );
	if ( !isset( $stickymenus_widgets['mysticky_welcomebar_enable'])) {
		$widget_status = 0;
	}
	if ( isset( $stickymenus_widgets['mysticky_welcomebar_enable']) ) {
		$widget_status = $stickymenus_widgets['mysticky_welcomebar_enable'];
	}
	$mysticky_options = get_option( 'mysticky_option_name' );


	function getRecentContactLead(){
		global $wpdb;
		$table_name = $wpdb->prefix . "mystickymenu_contact_lists";
		$query = "SELECT * FROM {$table_name} ORDER BY ID DESC LIMIT 3";
		$result     = $wpdb->get_results( $query );

		return $result;
	}
?>
<h1></h1>
<!-- Updated design -->
<div class="wrap mystickymenu-wrap">
	<div class="mystickymenu-dashboard">
		<h2></h2>
		<?php if(isset($stickymenus_widgets) && !empty($stickymenus_widgets)) :  ?>
		<div class="mystickymenu-dashboard welcomebars-list-table">
		
			<div class="mystickymenu-dashboard header-section">
				<div class="mystickymenu-dashboard heading-title"><h3><?php esc_html_e( 'Dashboard', 'mystickymenu');?></h3></div>
				<div class="mystickymenu-dashboard mystickymenu-widgets-btn-wrap"><a href="<?php echo esc_url(admin_url('admin.php?page=my-stickymenu-new-welcomebar'))?>" class="btn add_new_welcombar"><span class="dashicons dashicons-insert" style="font-size:18px;color:#fff;"></span>&nbsp; <?php echo esc_html_e('Add a New Bar','mystickymenu');?></a></div>
			</div>
		
			<table class="mystickymenu-widgets-lists">
				<thead>
					<tr>
						<th style="text-align: center; padding: 4px !important;"><?php esc_html_e( 'Status', 'mystickymenu');?></th>
						<th style="width:66%;text-align: left;padding: 0 0 0 30px !important;"><?php esc_html_e( 'Bars', 'mystickymenu');?></th>
						<th><?php esc_html_e( 'Quick Action', 'mystickymenu');?></th>
					</tr>
				</thead>
				<tbody>
					<?php if(isset($stickymenus_widgets) && !empty($stickymenus_widgets)) : ?>
					<tr id="stickymenu-widget-0">
						<td style="text-align: center; padding: 4px !important;">
							<label class="mysticky-welcomebar-switch welcombar-status-switch">
								<input type="checkbox" data-id="0" class="mystickymenu-widget-enabled" name ="mystickymenu-widget-enabled" data-id = "0" id = "mystickymenu-widget-enabled-0" value="1" <?php checked( $widget_status, 1 ); ?> />
								<span class="slider round"></span>
							</label>
							<div class="mystickymenu-action-popup welcombar-enabled-status" id="widget-status-dialog-0" style="display:none;">
								<div class="mystickymenu-action-popup-header">
									<h3><?php esc_html_e('Are you sure?','mystickymenu');?></h3>
									<span class="dashicons dashicons-no-alt close-button" data-from = "welcome-bar-status"data-id="0"></span>
								</div>
								<div class="mystickymenu-action-popup-body">
									<p><?php esc_html_e("You're about to turn off the bar. Are you sure about that?",'mystickymenu');?></p>
								</div>
								<div class="mystickymenu-action-popup-footer">
									<button type="button" class="btn-enable btn-nevermind-status" data-id="0"><?php esc_html_e('Nevermind','mystickymenu');?></button>
									<button type="button" class="btn-disable-cancel btn-turnoff-status" data-id="0"><?php esc_html_e('Turn off','mystickymenu');?></button>
								</div>
							</div>
							<div class="mystickymenupopup-overlay mystickymenupopup-widget-status-overlay" id="mystickymenu-status-popup-overlay-0" data-id="0" data-from="welcomebar-status" data-fromoverlay="welcombar_status"></div>
						</td>
						<td style="text-align: left;padding: 0 0 0 30px !important;"><?php echo esc_html_e('Bar #0','mystickymenu'); ?></td>
						<td>
							<div class="tooltip">
								<span class="tooltiptext"><?php esc_html_e('Edit','mystickymenu');?></span>
								<a href="<?php echo admin_url("admin.php?page=my-stickymenu-welcomebar&widget=0&isedit=1" );?>" ><img src="<?php echo esc_url(MYSTICKYMENU_URL); ?>/images/edit-icon.svg" /></a>
							</div>
							<div class="tooltip">
								<span class="tooltiptext"><?php esc_html_e('Duplicate','mystickymenu');?></span>
								<a class="copyicon" href='<?php echo admin_url("admin.php?page=my-stickymenu-new-welcomebar&duplicate_from=1");?>'><img src="<?php echo esc_url(MYSTICKYMENU_URL); ?>/images/copy-icon.svg" /></a>
							</div>
								
							<div class="tooltip">
								<span class="tooltiptext"><?php esc_html_e('Delete','mystickymenu');?></span>
								<a href="javascript:void(0);" class="mystickymenu-delete-widget" id="delete-widget-0" data-widget-id="0"><img src="<?php echo esc_url(MYSTICKYMENU_URL); ?>/images/delete-icon.svg" /></a>
							</div>
							
							
							<div class="mystickymenu-action-popup" id="widget-delete-dialog-0" style="display:none;">
								<div class="mystickymenu-action-popup-header">
									<h3><?php esc_html_e('Are you sure?','mystickymenu');?></h3>
									<span class="dashicons dashicons-no-alt close-button" data-from = "welcome-bar-delete"data-id="0"></span>
								</div>
								<div class="mystickymenu-action-popup-body">
									<p><?php esc_html_e("Are you sure want to delete the bar? You will lose the bar permanently and will not be able to retrieve it",'mystickymenu');?></p>
								</div>
								<div class="mystickymenu-action-popup-footer">
									<button type="button" class="btn-enable btn-delete-cancel"  data-id="0"><?php esc_html_e('Nevermind','mystickymenu');?></button>
									<button type="button" class="btn-disable-cancel btn-delete" data-id="0"><?php esc_html_e('Delete','mystickymenu');?></button>
								</div>
							</div>
							<div class="mystickymenupopup-overlay" id="mystickymenu-delete-popup-overlay-0" data-id="0" data-fromoverlay="welcombar_delete"></div>
						</td>
					</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<?php else:?>
		
		<div class="mystickymenu-dashboard new-welcomebar-section-wrap">
			<div class="mystickymenu-welcome-img">
				<img src="<?php echo esc_url(MYSTICKYMENU_URL); ?>/images/firstwelcombar.svg" />
			</div>
			<div class="mystickymenu-newwelcomebar-contents">
				<h2><?php esc_html_e("Welcome 🎉","mystickymenu");?></h2>
				<p><?php esc_html_e("You're one step away from creating a bar.","mystickymenu")?> </p> 
				<p><?php esc_html_e("Add top and bottoms bars for various purposes like showing updates, offers, countdown, flash sales, and more. You can also make any WordPress menu sticky easily.","mystickymenu");?></p>
				<a class="copyicon add_new_welcombar" href="<?php echo esc_url(admin_url('admin.php?page=my-stickymenu-new-welcomebar'));?>" class="btn add_new_welcombar"><span class="dashicons dashicons-arrow-right-alt" style="font-size:18px;color:#fff;"></span>&nbsp; <?php echo esc_html_e('Add a New Bar','mystickymenu');?></a>
			</div>	
			<div class="mystickymenu-features">
				<div class="mystickymenu-feature-title">
				<img src="<?php echo esc_url(MYSTICKYMENU_URL); ?>/images/crown.svg" alt="My Happy SVG" />
				<?php esc_html_e("Features","mystickymenu");?></div>
				<div class="mystickymenu-features-list">
					<ul class="documents-wrap-list">
						<li><?php esc_html_e("Create new bars with unique customization","mystickymenu");?></li>
						<li><?php esc_html_e("Make your WordPress navigation menu sticky","mystickymenu");?></li>
						<li><?php esc_html_e("Explore more triggers & targeting options","mystickymenu");?></li>
					</ul>
				</div>
			</div>	
		</div>
		<?php endif; ?>
		<!-- /**/ */ -->
		
		<div class="mystickymenu-tab-boxs-wrap msmenu-flexbox">

			<!--Main 1st -->

			<div class="mystickymenu-tab-stickymenu msmenu-blockbox msmenu-box50">
				<?php $result = getRecentContactLead(); ?>
				
				<!-- 1 -->
				<div class="contact-recent-lead msm-bgbox">
					<div class="stickymenubox-title-section">
						<h3><?php esc_html_e("Recent Leads","mystickymenu");?></h3> 
						<?php if(isset($result) && count($result) > 0) : ?>
							<a class="msmenu-btn" href="<?php echo esc_url(admin_url('admin.php?page=my-sticky-menu-leads'));?>"><?php echo esc_html_e('View All','mystickymenu');?></a>
						<?php endif; ?>
					</div>
					<?php 	
					if( isset($result) && count($result) > 0 ){?>
						<div class="stickymenu recent-lead-table">
							<table>
								<tr>
									<th><?php esc_html_e('Name','mystickymenu');?></th>
									<th><?php esc_html_e('Email','mystickymenu');?></th>
									<th><?php esc_html_e('Phone','mystickymenu');?></th>
								</tr>
								<?php 
									foreach( $result as $key => $val ){
										echo "<tr>";
										echo "<td>". esc_html($val->contact_name) ." </td>";
										echo "<td>". esc_html($val->contact_email) ." </td>";
										echo "<td>". esc_html($val->contact_phone) ." </td>";
										echo "</tr>";
									}
								?>
							</table>
						</div>
					<?php
					}else{?>
						<div class="stickymenu-no-lead">
							<?php echo '<img src="'. esc_url(MYSTICKYMENU_URL) .'images/empty_lead.png" />'; ?>
							<p><?php  esc_html_e("Once you get a new lead, it’ll appear here","mystickymenu");?></p>
						</div>
						<?php	
					}
					?>
					
				</div>
				<!-- 2 -->
				<div class="contactus-tab-option msm-bgbox">
					<div class="contactus-title">
						<h3>Create Popups That Convert</h3>
					</div>					
					<div class="premio-footer-option">
						<h3><img src="<?php echo esc_url(MYSTICKYMENU_URL . "images/poptin-popups.png"); ?>" /></h3>
						<div class="premio-content-list">
							<span>Engaging popups and forms for your website. Build in minutes.</span>
						</div>
						<?php 
						if( class_exists( 'POPTIN_Plugin_Base' ) ) { 
							$poptin_url = admin_url('admin.php?page=Poptin');
						} else {
							$poptin_url = admin_url('admin.php?page=install-poptin-plugin');
						}
						?>
						<label class="stickymenu-box-button settings-buttons"><a class="btn btn-poptin-popups" href="<?php echo esc_url($poptin_url);?>" target="_blank">

							<?php 
								if(get_option('poptin_id', false) != false){
									echo esc_html_e("Create Your Popup","mystickymenu");
								}else{
								    echo esc_html_e("Create Your First Popup","mystickymenu");
								}
							?>
						</a></label>
					</div>
					
				</div>
				<!--  -->

			</div>

			<!--Main 2nd -->
			
			<div class="msmenu-blockbox msmenu-box50">
			<!-- 1 -->
				<div class="stickymenu-tab-option msm-bgbox">
					<div class="stickymenubox-title-section"><h3><?php esc_html_e("Sticky menu","mystickymenu");?></h3></div>
					<div class="stickymenu-settings">
						<div class="settings-content">
							<?php 
								if(isset($mysticky_options['stickymenu_enable']) && $mysticky_options['stickymenu_enable'] == 1){
									echo '<p>Sticky menu is currently turned on.</p>';
								}else{
									echo '<p>Sticky menu is not currently configured. Configure to enable.</p>';	
								}
							?>
						</div>
						<div class="stickymenu-box-button settings-buttons"> 
							<?php if(isset($mysticky_options['stickymenu_enable']) && $mysticky_options['stickymenu_enable'] == 1):
							?>
							<a href="<?php echo esc_url(admin_url("admin.php?page=my-stickymenu-settings"));?>" id="btn-config-settings" style="color:#6559f6;border-color:#c7c2fb;"><?php esc_html_e("Settings","mystickymenu"); ?></a>
							<a href="javascript:void(0);" id="btn-config-disable" style="color:#d3465c;border-color:#efbcc4;"><?php esc_html_e("Disable","mystickymenu"); ?></a>
							<?php else : ?>
							<a href="<?php echo esc_url(admin_url("admin.php?page=my-stickymenu-settings"));?>"><?php esc_html_e("Configure","mystickymenu"); ?></a>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<!-- 2 -->
				<div class="mystickymenu-tab-documentation msm-bgbox">
					<h3>Documentation</h3>
					<div class="stickymenu-box-container flex-col"> 
						<ul class="documents-wrap-list">
							<li><a href="https://premio.io/help/mystickymenu/how-to-use-my-sticky-menu/" target="_blank"><?php esc_html_e('How to use My Sticky Bar?','mystickymenu');?></a></li>
							<li><a href="https://premio.io/help/mystickymenu/how-to-add-your-sticky-menu-on-specific-pages-only/" target="_blank"><?php esc_html_e('How to add your sticky menu on specific pages only','mystickymenu');?></a></li>
							<li><a href="https://premio.io/help/mystickymenu/how-to-create-a-welcome-bar/" target="_blank"><?php esc_html_e('How to create a Bar','mystickymenu');?></a></li>							
						</ul>

						<span class="flex align-middle gap-x-px">
							<span><?php esc_html_e("Need more help? Visit our ","mystickymenu");?></span>
							<a class="btn btn-primary-outline" href="https://premio.io/help/mystickymenu/?utm_source=msmhelp" target="_blank"><?php esc_html_e("Help Center","mystickymenu"); ?></a>
						</span>
					</div>
				</div>
			
			</div>
			
		</div>
		<div class="mystickymenu-tab-boxs-wrap msmenu-flexbox">
			<div class="mystickymenu-tab-stickymenu contactus-tab-option-wrap msmenu-blockbox">
				<div class="contactus-tab-option msm-bgbox">
					<div class="contactus-tab-option-left">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path opacity="0.3" d="M12 1L9 9L1 12L9 15L12 23L15 15L23 12L15 9L12 1Z" fill="black"/></svg>
						<?php esc_html_e('Explore amazing products from the premio team and supercharge your wordpress website!', 'mystickymenu')?>
						<a class="copyicon1" href="https://premio.io/" target="_blank"><span class="dashicons dashicons-external"></span><?php esc_html_e('Visit website', 'mystickymenu');?></a>
					</div>
					<div class="contactus-tab-option-right">
						<div class="contactus-contents-buttons">
							<span class="folous"><?php esc_html_e('Follow Us', 'mystickymenu');?> </span> 
							<a href="https://www.facebook.com/groups/premioplugins/" class="facebook-link copyicon" target="_blank">
								<span class="dashicons dashicons-facebook-alt"></span>
							</a>
							<a href="https://twitter.com/premioplugins" class="tweeter-link copyicon" target="_blank">
								<span class="dashicons dashicons-twitter"></span>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- 00000 END  -->
		
		<div class="mystickymenu-action-popup new-center" id="stickymenu_status_popupbox" style="display:none;">
				<div class="mystickymenu-action-popup-header">
					<h3><?php esc_html_e("Are you sure?","mystickymenu"); ?></h3>
					<span class="dashicons dashicons-no-alt close-button" data-from = "stickymenu-status"></span>
				</div>
				<div class="mystickymenu-action-popup-body">
					<p><?php esc_html_e("You’re about to turn off the sticky menu feature. Are you sure about that?","mystickymenu"); ?></p>
				</div>
				<div class="mystickymenu-action-popup-footer">
					<button type="button" class="btn-enable btn-nevermind-status" id="stickymenu_status_nevermind" ><?php esc_html_e("Nevermind","mystickymenu"); ?></button>
					<button type="button" class="btn-disable-cancel" id="stickymenu_status_turnoff" ><?php esc_html_e("Turn off","mystickymenu"); ?></button>
				</div>
			</div>
			<div class="mystickymenupopup-overlay" id="stickymenuconfig-overlay-popup"></div>
	</div>
</div>	