<?php if (!defined('AIO_WP_SECURITY_PATH')) die('No direct access allowed'); ?>

<div class="aiowps_col aiowps_half_width aiowps_feature_cont">
	<header>
		<h3><?php echo esc_html__('All-In-One Security (AIOS) Free vs Premium Comparison Chart', 'all-in-one-wp-security-and-firewall'); ?></h3>
		<p>
			<a target="_blank" href="https://aiosplugin.com/faq/"><?php esc_html_e('FAQs', 'all-in-one-wp-security-and-firewall'); ?></a>
			|
			<a target="_blank" href="https://aiosplugin.com/general-enquiries/"><?php esc_html_e('Ask a pre-sales question', 'all-in-one-wp-security-and-firewall'); ?></a>
		</p>
	</header>
	<table class="aiowps_feat_table">
		<tbody>
		<tr>
			<td></td>
			<td>
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage -- Hard coded image. ?>
				<img src="<?php echo esc_url(AIO_WP_SECURITY_URL) . '/images/plugin-logos/aios-free.png'; ?>" alt="<?php esc_attr_e('All In One WP Security & Firewall Free', 'all-in-one-wp-security-and-firewall'); ?>" width="auto" height="80">
			</td>
			<td>
				<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage -- Hard coded image. ?>
				<img src="<?php echo esc_url(AIO_WP_SECURITY_URL) . '/images/plugin-logos/aios-premium.png'; ?>" alt="<?php esc_attr_e('All In One WP Security & Firewall Premium', 'all-in-one-wp-security-and-firewall'); ?>" width="auto" height="80">
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<p><?php esc_html_e('Installed', 'all-in-one-wp-security-and-firewall'); ?></p>
			</td>
			<td>
				<a class="button button-primary" href="https://aiosplugin.com/product/all-in-one-wp-security-and-firewall-premium/" target="_blank"><?php esc_html_e('Upgrade now', 'all-in-one-wp-security-and-firewall'); ?></a>
			</td>
		</tr>
		<tr class="aiowps-main-feature-row">
			<td class="aiowps-feature-text">
				<h4><?php esc_html_e('Login security feature suite', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php echo esc_html__('Protect against brute-force attacks and keep bots at bay.', 'all-in-one-wp-security-and-firewall').' '.esc_html__('AIOS takes WordPress\' default login security features to a whole new level.', 'all-in-one-wp-security-and-firewall'); ?></p>
				<br>
				<?php /* translators: %s: Features URL */ ?>
				<p><?php echo sprintf(esc_html__('To see all login security features, visit %s', 'all-in-one-wp-security-and-firewall'), '<a href="https://aiosplugin.com/features" target="_blank">https://aiosplugin.com/features</a>'); ?></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
		</tr>
		<tr class="aiowps-main-feature-row">
			<td class="aiowps-feature-text">
				<h4><?php esc_html_e('Firewall and file protection feature suite', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php echo esc_html__('Protection from the latest exploits.', 'all-in-one-wp-security-and-firewall').' '.esc_html__('Activate firewall settings ranging from basic, intermediate and advanced.', 'all-in-one-wp-security-and-firewall').' '.esc_html__('Get comprehensive, instant protection with All-in-One Security.', 'all-in-one-wp-security-and-firewall'); ?></p>
				<br>
				<?php /* translators: %s: Features URL */ ?>
				<p><?php echo sprintf(esc_html__('To see all firewall and file protection features, visit %s', 'all-in-one-wp-security-and-firewall'), '<a href="https://aiosplugin.com/features" target="_blank">https://aiosplugin.com/features</a>'); ?></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
		</tr>
		<tr class="aiowps-main-feature-row">
			<td class="aiowps-feature-text">
				<h4><?php esc_html_e('Content protection feature suite', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php esc_html_e('Eliminate spam and protect your content to dramatically improve your website\'s interactions with search engines.', 'all-in-one-wp-security-and-firewall'); ?></p>
				<br>
				<?php /* translators: %s: Features URL */ ?>
				<p><?php echo sprintf(esc_html__('To see all content protection features, visit %s', 'all-in-one-wp-security-and-firewall'), '<a href="https://aiosplugin.com/features" target="_blank">https://aiosplugin.com/features</a>'); ?></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
		</tr>
		<tr class="aiowps-main-feature-row">
			<td class="aiowps-feature-text" colspan="3">
				<h4><?php esc_html_e('Malware scanning', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php esc_html_e('Finding out by accident that your site has been infected with malware is too late.', 'all-in-one-wp-security-and-firewall'); ?></p>
				<br>
				<p><?php echo esc_html__('Malware can have a dramatic effect on your site\'s search rankings and you may not even know about it.', 'all-in-one-wp-security-and-firewall').' '.esc_html__('It can slow your website down, access customer data, send unsolicited emails, change your content or prevent users from accessing it.', 'all-in-one-wp-security-and-firewall'); ?></p>
			</td>
		</tr>
		<tr>
			<td class="aiowps-feature-text">
				<h4><?php esc_html_e('Automatic malware scanning', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php esc_html_e('Best-in-class scanning for the latest malware, trojans and spyware 24/7.', 'all-in-one-wp-security-and-firewall'); ?></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
		</tr>
		<tr>
			<td class="aiowps-feature-text">
				<h4><?php esc_html_e('Response time monitoring', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php esc_html_e('You\'ll know immediately if your website\'s response time is negatively affected.', 'all-in-one-wp-security-and-firewall'); ?></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
		</tr>
		<tr>
			<td class="aiowps-feature-text">
				<h4><?php esc_html_e('Up-time monitoring', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php echo esc_html__('AIOS checks your website\'s uptime every 5 minutes.', 'all-in-one-wp-security-and-firewall').' '.esc_html__('We\'ll notify you straight away if your site/server goes down.', 'all-in-one-wp-security-and-firewall'); ?></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
		</tr>
		<tr>
			<td class="aiowps-feature-text">
				<h4><?php esc_html_e('Alerts you of blacklisting by search engines', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php echo esc_html__('AIOS monitors your site\'s blacklist status daily.', 'all-in-one-wp-security-and-firewall').' '.esc_html__('We\'ll notify you within 24 hours if something\'s amiss so you can take action, before it\'s too late.', 'all-in-one-wp-security-and-firewall'); ?></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
		</tr>
		<tr>
			<td class="aiowps-feature-text">
				<h4><?php esc_html_e('Flexible assignment', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php esc_html_e('Register and remove websites from the scanning service at any time.', 'all-in-one-wp-security-and-firewall'); ?></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
		</tr>
		<tr>
			<td class="aiowps-feature-text">
				<h4><?php esc_html_e('Malware reports', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php esc_html_e('Reports are available via the \'My Account\' page and directly via email.', 'all-in-one-wp-security-and-firewall'); ?></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
		</tr>
		<tr class="aiowps-main-feature-row">
			<td class="aiowps-feature-text" colspan="3">
				<h4><?php esc_html_e('Flexible two-factor authentication', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php esc_html_e('With Two-Factor Authentication (TFA) users enter their username and password and a one-time code sent to a device to login.', 'all-in-one-wp-security-and-firewall'); ?></p>
				<br>
				<p><?php esc_html_e('TFA is a feature in both our free and premium packages, but AIOS Premium affords whole new levels of control over how TFA is implemented.', 'all-in-one-wp-security-and-firewall'); ?></p>
			</td>
		</tr>
		<tr>
			<td class="aiowps-feature-text">
				<h4><?php esc_html_e('Authenticator apps', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php echo esc_html__('Supports TOTP and HOTP protocols.', 'all-in-one-wp-security-and-firewall').' '.esc_html__('TFA Can be used with Google Authenticator, Microsoft Authenticator, Authy and many more.', 'all-in-one-wp-security-and-firewall'); ?></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
		</tr>
		<tr>
			<td class="aiowps-feature-text">
				<h4><?php esc_html_e('Role specific configuration', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php esc_html_e('Make it compulsory for certain roles e.g. for admin and editor roles.', 'all-in-one-wp-security-and-firewall'); ?></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
		</tr>
		<tr>
			<td class="aiowps-feature-text">
				<h4><?php esc_html_e('Require TFA after a set time period', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php esc_html_e('For example you could require all admins to have TFA once their accounts are a week old.', 'all-in-one-wp-security-and-firewall'); ?></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
		</tr>
		<tr>
			<td class="aiowps-feature-text">
				<h4><?php esc_html_e('Trusted devices - control how often TFA is required', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php esc_html_e('Ask for TFA after a chosen number of days for trusted devices instead of on every login.', 'all-in-one-wp-security-and-firewall'); ?></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
		</tr>
		<tr>
			<td class="aiowps-feature-text">
				<h4><?php esc_html_e('Anti-bot protection', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php esc_html_e('Option to hide the existence of forms on WooCommerce login pages unless JavaScript is active.', 'all-in-one-wp-security-and-firewall'); ?></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
		</tr>
		<tr>
			<td class="aiowps-feature-text">
				<h4><?php esc_html_e('Customise TFA design layout', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php esc_html_e('Customise the design of TFA so it aligns with your existing web design.', 'all-in-one-wp-security-and-firewall'); ?></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
		</tr>
		<tr>
			<td class="aiowps-feature-text">
				<h4><?php esc_html_e('TFA emergency codes', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php esc_html_e('Generate a one-time use emergency code to allow access if your device is lost.', 'all-in-one-wp-security-and-firewall'); ?></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
		</tr>
		<tr>
			<td class="aiowps-feature-text">
				<h4><?php esc_html_e('TFA multisite compatibility', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php esc_html_e('TFA is Compatible with multisite networks and sub-sites.', 'all-in-one-wp-security-and-firewall'); ?></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
		</tr>
		<tr>
			<td class="aiowps-feature-text">
				<h4><?php esc_html_e('TFA support for common login forms', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php echo esc_html__('Supports WooCommerce, Affiliates-WP and Theme my Login login forms.', 'all-in-one-wp-security-and-firewall');?></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
		</tr>
		<tr>
			<td class="aiowps-feature-text">
				<h4><?php esc_html_e('TFA support for other login forms', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php echo esc_html__('Supports Elementor Pro, bbPress and all third-party login forms without any further coding needed.', 'all-in-one-wp-security-and-firewall');?></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
		</tr>
		<tr class="aiowps-main-feature-row">
			<td class="aiowps-feature-text" colspan="3">
				<h4><?php esc_html_e('Smart 404 blocking', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php esc_html_e('404 errors can occur when someone legitimately mistypes a URL, but they\'re also generated by hackers searching for weaknesses in your site.', 'all-in-one-wp-security-and-firewall'); ?></p>
			</td>
		</tr>
		<tr>
			<td class="aiowps-feature-text">
				<h4><?php esc_html_e('Automatically and permanently blocks bots producing 404s', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php esc_html_e('AIOS Premium provides more protection than the competition by automatically and permanently blocking IP addresses of bots and hackers based on how many 404 errors they generate.', 'all-in-one-wp-security-and-firewall'); ?></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
		</tr>
		<tr>
			<td class="aiowps-feature-text">
				<h4><?php esc_html_e('404 error charts', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php esc_html_e('Handy charts keep you informed of how many 404s have occurred and which IP address or country is producing them.', 'all-in-one-wp-security-and-firewall'); ?></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
		</tr>
		<tr class="aiowps-main-feature-row">
			<td class="aiowps-feature-text" colspan="3">
				<h4><?php esc_html_e('Country blocking', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php esc_html_e('Most malicious attacks come from a handful of countries and so it\'s possible to prevent most attacks with our country blocking tool.', 'all-in-one-wp-security-and-firewall'); ?></p>
			</td>
		</tr>
		<tr>
			<td class="aiowps-feature-text">
				<h4><?php esc_html_e('Block traffic based on country of origin', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php esc_html_e('AIOS Premium utilises an IP database that promises 99.5% accuracy.', 'all-in-one-wp-security-and-firewall'); ?></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
		</tr>
		<tr>
			<td class="aiowps-feature-text">
				<h4><?php esc_html_e('Block traffic to specific pages based on country of origin', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php esc_html_e('Block access to your whole site or on a page-by-page basis.', 'all-in-one-wp-security-and-firewall'); ?></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
		</tr>
		<tr>
			<td class="aiowps-feature-text">
				<h4><?php esc_html_e('Whitelist some users from blocked countries', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php esc_html_e('Whitelist IP addresses or IP ranges even if they are part of a blocked country.', 'all-in-one-wp-security-and-firewall'); ?></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
		</tr>
		<tr class="aiowps-main-feature-row">
			<td class="aiowps-feature-text" colspan="3">
				<h4><?php esc_html_e('Premium support', 'all-in-one-wp-security-and-firewall'); ?></h4>
			</td>
		</tr>
		<tr>
			<td class="aiowps-feature-text">
				<h4><?php esc_html_e('Unlimited support', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php esc_html_e('Personalised, email support from our team of Security experts, as and when you need it.', 'all-in-one-wp-security-and-firewall'); ?></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
		</tr>
		<tr>
			<td class="aiowps-feature-text">
				<h4><?php esc_html_e('Guaranteed response time', 'all-in-one-wp-security-and-firewall'); ?></h4>
				<p><?php echo esc_html__('We offer a guaranteed response time of three days.', 'all-in-one-wp-security-and-firewall').' '.esc_html__('99% of our Premium customers receive a response to their enquiry within 24 hours during the working week.', 'all-in-one-wp-security-and-firewall'); ?></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-no-alt" aria-label="<?php esc_attr_e('No', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
			<td>
				<p><span class="dashicons dashicons-yes" aria-label="<?php esc_attr_e('Yes', 'all-in-one-wp-security-and-firewall'); ?>"></span></p>
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<p><?php esc_html_e('Installed', 'all-in-one-wp-security-and-firewall'); ?></p>
			</td>
			<td>
				<a class="button button-primary" href="https://aiosplugin.com/product/all-in-one-wp-security-and-firewall-premium/" target="_blank"><?php esc_html_e('Upgrade now', 'all-in-one-wp-security-and-firewall'); ?></a>
			</td>
		</tr>
		</tbody>
	</table>
</div>
<div class="aiowps_col  aiowps_half_width aiowps_plugin_family_cont aiowps-plugin-family__free">
	<header>
		<h3><?php esc_html_e('Our other plugins', 'all-in-one-wp-security-and-firewall'); ?></h3>
		<p>
			<a href="https://updraftplus.com/?utm_medium=software&utm_source=aios&utm_content=aios-mayalso-like-tab&utm_term=try-now-udp&utm_campaign=ad"><?php echo 'UpdraftPlus'; ?></a>
			|
			<a href="https://getwpo.com/?utm_medium=software&utm_source=aios&utm_content=aios-mayalso-like-tab&utm_term=try-now-wpo&utm_campaign=ad"><?php echo 'WP-Optimize'; ?></a>
			|
			<a href="https://updraftplus.com/updraftcentral/?utm_medium=software&utm_source=aios&utm_content=aios-mayalso-like-tab&utm_term=try-now-udc&utm_campaign=ad"><?php echo 'UpdraftCentral'; ?></a>
			|
			<a href="https://easyupdatesmanager.com/?utm_medium=software&utm_source=aios&utm_content=aios-mayalso-like-tab&utm_term=try-now-eum&utm_campaign=ad"><?php echo 'Easy Updates Manager'; ?></a>
			|
			<a href="https://www.internallinkjuicer.com/?utm_medium=software&utm_source=aios&utm_content=aios-mayalso-like-tab&utm_term=try-now-ilj&utm_campaign=ad"><?php echo 'Internal Link Juicer'; ?></a>
			|
			<a href="https://wpovernight.com/?utm_medium=software&utm_source=aios&utm_content=aios-mayalso-like-tab&utm_term=try-now-wp-overnight&utm_campaign=ad"><?php echo 'WP Overnight'; ?></a>
			|
			<a href="https://wpgetapi.com/?utm_medium=software&utm_source=aios&utm_content=aios-mayalso-like-tab&utm_term=try-now-wpgetapi&utm_campaign=ad"><?php echo 'WPGetAPI'; ?></a>
		</p>
	</header>
	<div class="aiowps-plugin-family__plugins">
		<div class="aiowps-plugin-family__plugin">
			<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage -- Hard coded image. ?>
			<a href="https://updraftplus.com/?utm_medium=software&utm_source=aios&utm_content=aios-mayalso-like-tab&utm_term=try-now-udp&utm_campaign=ad"><img class="addons" alt="UpdraftPlus" src="<?php echo esc_url(AIO_WP_SECURITY_URL) . '/images/plugin-logos/updraftplus_logo.png'; ?>"></a>
			<a class="other-plugin-title" href="https://updraftplus.com/"><h3><?php esc_html_e('UpdraftPlus – the ultimate protection for your site, hard work and business', 'all-in-one-wp-security-and-firewall'); ?></h3></a>
			<p><?php echo esc_html__('Simplifies backups and restoration.', 'all-in-one-wp-security-and-firewall').' '.esc_html__('It is the world\'s highest ranking and most popular scheduled backup plugin, with over three million currently-active installs.', 'all-in-one-wp-security-and-firewall'); ?></p>
			<a href="https://updraftplus.com/?utm_medium=software&utm_source=aios&utm_content=aios-mayalso-like-tab&utm_term=try-now-udp&utm_campaign=ad"><?php esc_html_e('Try for free', 'all-in-one-wp-security-and-firewall'); ?></a>
		</div>
		<div class="aiowps-plugin-family__plugin">
			<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage -- Hard coded image. ?>
			<a href="https://getwpo.com/?utm_medium=software&utm_source=aios&utm_content=aios-mayalso-like-tab&utm_term=try-now-wpo&utm_campaign=ad"><img class="addons" alt="WP-Optimize" src="<?php echo esc_url(AIO_WP_SECURITY_URL) . '/images/plugin-logos/wp-optimize.png'; ?>"></a>
			<a class="other-plugin-title" href="https://getwpo.com/"><h3><?php esc_html_e('WP-Optimize – keep your database fast and efficient', 'all-in-one-wp-security-and-firewall'); ?></h3></a>
			<p><?php echo esc_html__('Makes your site fast and efficient.', 'all-in-one-wp-security-and-firewall').' '.esc_html__('It cleans the database, compresses images and caches pages for ultimate speed.', 'all-in-one-wp-security-and-firewall'); ?></p>
			<a href="https://getwpo.com/?utm_medium=software&utm_source=aios&utm_content=aios-mayalso-like-tab&utm_term=try-now-wpo&utm_campaign=ad"><?php esc_html_e('Try for free', 'all-in-one-wp-security-and-firewall'); ?></a>
		</div>
		<div class="aiowps-plugin-family__plugin">
			<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage -- Hard coded image. ?>
			<a href="https://updraftplus.com/updraftcentral/?utm_medium=software&utm_source=aios&utm_content=aios-mayalso-like-tab&utm_term=try-now-udc&utm_campaign=ad"><img class="addons" alt="UpdraftCentral" src="<?php echo esc_url(AIO_WP_SECURITY_URL) . '/images/plugin-logos/updraft-central.png'; ?>"></a>
			<a class="other-plugin-title" href="https://updraftplus.com/updraftcentral/"><h3><?php esc_html_e('UpdraftCentral – save hours managing multiple WP sites from one place', 'all-in-one-wp-security-and-firewall'); ?></h3></a>
			<p><?php esc_html_e('Highly efficient way to manage, optimize, update and backup multiple websites from one place.', 'all-in-one-wp-security-and-firewall'); ?></p>
			<a href="https://updraftplus.com/updraftcentral/?utm_medium=software&utm_source=aios&utm_content=aios-mayalso-like-tab&utm_term=try-now-udc&utm_campaign=ad"><?php esc_html_e('Try for free', 'all-in-one-wp-security-and-firewall'); ?></a>
		</div>
		<div class="aiowps-plugin-family__plugin">
			<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage -- Hard coded image. ?>
			<a href="https://easyupdatesmanager.com/?utm_medium=software&utm_source=aios&utm_content=aios-mayalso-like-tab&utm_term=try-now-eum&utm_campaign=ad"><img class="addons" alt="Easy Updates Manager" src="<?php echo esc_url(AIO_WP_SECURITY_URL) . '/images/plugin-logos/easy-updates-manager-logo.png'; ?>"></a>
			<a class="other-plugin-title" href="https://easyupdatesmanager.com/?utm_medium=software&utm_source=aios&utm_content=aios-mayalso-like-tab&utm_term=try-now-eum&utm_campaign=ad"><h3><?php esc_html_e('Easy Updates Manager - keep your WordPress site up to date and bug free', 'all-in-one-wp-security-and-firewall'); ?></h3></a>
			<p>
				<?php
					echo esc_html__("A light yet powerful plugin that allows you to manage all kinds of updates.", 'all-in-one-wp-security-and-firewall') . "&nbsp;" .
						esc_html__("With a huge number of settings for endless customization.", 'all-in-one-wp-security-and-firewall') . "&nbsp;" .
						esc_html__("Easy Updates Manager is an obvious choice for anyone wanting to take control of their website updates.", 'all-in-one-wp-security-and-firewall');
				?>
	
			</p>
			<a href="https://easyupdatesmanager.com/?utm_medium=software&utm_source=aios&utm_content=aios-mayalso-like-tab&utm_term=try-now-eum&utm_campaign=ad"><?php esc_html_e('Try for free', 'all-in-one-wp-security-and-firewall'); ?></a>
		</div>
		<div class="aiowps-plugin-family__plugin">
			<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage -- Hard coded image. ?>
			<a href="https://www.internallinkjuicer.com/?utm_medium=software&utm_source=aios&utm_content=aios-mayalso-like-tab&utm_term=try-now-ilj&utm_campaign=ad"><img class="addons" alt="Internal Link Juicer" src="<?php echo esc_url(AIO_WP_SECURITY_URL) . '/images/plugin-logos/internal-link-juicer-logo-sm.png'; ?>"></a>
			<a class="other-plugin-title" href="https://www.internallinkjuicer.com/?utm_medium=software&utm_source=aios&utm_content=aios-mayalso-like-tab&utm_term=try-now-ilj&utm_campaign=ad"><h3><?php esc_html_e('Internal Link Juicer - a five-star rated internal linking plugin for WordPress', 'all-in-one-wp-security-and-firewall'); ?></h3></a>
			
			<p>
				<?php
				echo esc_html__("This five-star rated plugin automates internal linking.", 'all-in-one-wp-security-and-firewall') . "&nbsp;" .
					esc_html__("It strategically places relevant links within your content.", 'all-in-one-wp-security-and-firewall');
				?>
			</p>
			<p>
				<?php esc_html_e("Improve your SEO with just a few clicks.", 'all-in-one-wp-security-and-firewall');?>
			</p>
			<a href="https://www.internallinkjuicer.com/?utm_medium=software&utm_source=aios&utm_content=aios-mayalso-like-tab&utm_term=try-now-ilj&utm_campaign=ad"><?php esc_html_e('Try for free', 'all-in-one-wp-security-and-firewall'); ?></a>
		</div>
		<div class="aiowps-plugin-family__plugin">
			<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage -- Hard coded image. ?>
			<a href="https://wpovernight.com/?utm_medium=software&utm_source=aios&utm_content=aios-mayalso-like-tab&utm_term=try-now-wp-overnight&utm_campaign=ad"><img class="addons" alt="WP Overnight" src="<?php echo esc_url(AIO_WP_SECURITY_URL) . '/images/plugin-logos/wp-overnight-sm.png'; ?>"></a>
			<a class="other-plugin-title" href="https://wpovernight.com/?utm_medium=software&utm_source=aios&utm_content=aios-mayalso-like-tab&utm_term=try-now-wp-overnight&utm_campaign=ad"><h3><?php esc_html_e('WP Overnight - quality plugins for your WooCommerce store. 5 star rated invoicing, order and product management tools', 'all-in-one-wp-security-and-firewall'); ?></h3></a>
			<p>
				<?php
					echo esc_html__("WP Overnight is an independent plugin shop with a range of WooCommerce plugins.", 'all-in-one-wp-security-and-firewall') . "&nbsp;" .
						esc_html__("Our range of plugins have over 7,500,000 downloads and thousands of loyal customers.", 'all-in-one-wp-security-and-firewall');
				?>
			</p>
			<p>
				<?php esc_html_e("Create PDF invoices, automations, barcodes, reports and so much more.", 'all-in-one-wp-security-and-firewall');?>
			</p>
			<a href="https://wpovernight.com/?utm_medium=software&utm_source=aios&utm_content=aios-mayalso-like-tab&utm_term=try-now-wp-overnight&utm_campaign=ad"><?php esc_html_e('Try for free', 'all-in-one-wp-security-and-firewall'); ?></a>
		</div>
		<div class="aiowps-plugin-family__plugin">
			<?php // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage -- Hard coded image. ?>
			<a href="https://wpgetapi.com/?utm_medium=software&utm_source=aios&utm_content=aios-mayalso-like-tab&utm_term=try-now-wpgetapi&utm_campaign=ad"><img class="addons" alt="WP Get API" src="<?php echo esc_url(AIO_WP_SECURITY_URL) . '/images/plugin-logos/wpgetapi-sm.png'; ?>"></a>
			<a class="other-plugin-title" href="https://wpgetapi.com/?utm_medium=software&utm_source=aios&utm_content=aios-mayalso-like-tab&utm_term=try-now-wpgetapi&utm_campaign=ad"><h3><?php esc_html_e('WPGetAPI - connect WordPress to APIs without a developer', 'all-in-one-wp-security-and-firewall'); ?></h3></a>
			
			<p>
				<?php
					echo esc_html__("The easiest way to connect your WordPress website to an external API.", 'all-in-one-wp-security-and-firewall') . "&nbsp;" .
						esc_html__("WPGetAPI is free, powerful and easy to use.", 'all-in-one-wp-security-and-firewall') . "&nbsp;" .
						esc_html__("Connect to virtually any REST API and retrieve data without writing a line of code.", 'all-in-one-wp-security-and-firewall');
				?>
			</p>
			<a href="https://wpgetapi.com/?utm_medium=software&utm_source=aios&utm_content=aios-mayalso-like-tab&utm_term=try-now-wpgetapi&utm_campaign=ad"><?php esc_html_e('Try for free', 'all-in-one-wp-security-and-firewall'); ?></a>
		</div>
	</div><!-- END aiowps-plugin-family__plugins -->
</div>
<div class="clear"></div>