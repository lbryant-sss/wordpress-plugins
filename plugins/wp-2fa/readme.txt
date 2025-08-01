=== WP 2FA - Two-factor authentication for WordPress ===
Contributors: Melapress, robert681
Plugin URI: https://melapress.com/wordpress-2fa/
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl.html
Tags: 2FA, two-factor authentication, 2-factor authentication, WordPress authentication, google authenticator
Requires at least: 5.5
Tested up to: 6.8.2
Stable tag: 2.9.0
Requires PHP: 7.4.0

Get better WordPress login security; add two-factor authentication (2FA) for all your users with this easy-to-use plugin.

== Description ==

### A free and easy-to-use two-factor authentication plugin for WordPress

Add an extra layer of security to your WordPress website login and protect your users. Enable two-factor authentication (2FA), the best protection against password leaks, automated password guessing, and brute force attacks.

Use the WP 2FA plugin to enable two-factor authentication for your WordPress administrator, enforce 2FA for all your website users, or for users with specific roles. This plugin is very easy to use; everything can be configured via wizards with clear instructions, so even non-technical users can set up 2FA without requiring technical assistance.

[youtube https://www.youtube.com/watch?v=vRlX_NNGeFo]

[Features](https://melapress.com/wordpress-2fa/features/?utm_source=wp+repo&utm_medium=repo+link&utm_campaign=wordpress_org&utm_content=wp2fa) | [Getting Started](https://melapress.com/support/kb/wp-2fa-plugin-getting-started/?utm_source=wp+repo&utm_medium=repo+link&utm_campaign=wordpress_org&utm_content=wp2fa) | [Get the Premium!](https://melapress.com/wordpress-2fa/pricing/?utm_source=wp+repo&utm_medium=repo+link&utm_campaign=wordpress_org&utm_content=wp2fa)


### WP 2FA key plugin features and capabilities
- Free two-factor authentication (2FA) for all users
- Supports multiple 2FA methods including authenticator app TOTP, and code over email
- An API that allows you to integrate any alternative 2FA method such as WhatsApp, OTP Token, etc.
- Universal 2FA app support – generate codes from Google Authenticator, Authy, & any other 2FA app
- Supports 2FA backup codes
- Wizard-driven plugin configuration & 2FA setup – no technical knowledge required
- Use 2FA policies to enforce 2FA with a grace period or require users to instantly setup 2FA upon logging in
- No WordPress dashboard access is required for users to set up 2FA
- Fully editable email templates
- Much more
 
### Upgrade to WP 2FA Premium and get even more benefits

The premium version of WP 2FA comes bundled with even more features to take your WordPress website login security to the next level.

With the premium edition of WP 2FA, you get more 2FA methods, 1-click integration with WooCommerce, trusted devices feature, extensive white labeling capabilities, and much more!

### Premium features list

-   Everything in the free version
-   Full white labeling capabilities (change all the text and look and feel of the wizards, emails, SMS, and 2FA pages)
-   YubiKey hardware key support
-   Several other additional 2FA methods (such as 2FA over SMS, link in email & more)
-   Trusted devices (no 2FA required for a configured period of time)
-   Require 2FA on password reset
-   One-click integration to set up WooCommerce and two-factor authentication (2FA)
-   Much more

Refer to the [WP 2FA plugin features and benefits page](https://melapress.com/wordpress-2fa/features/?utm_source=wp+repo&utm_medium=repo+link&utm_campaign=wordpress_org&utm_content=wp2fa) to learn more about the benefits of upgrading to WP 2FA Premium.

## Free and premium support

Support for the free edition of WP 2FA is free on the [WordPress support forums](https://wordpress.org/support/plugin/wp-2fa/). Premium world-class support via one-to-one email is available to the Premium users - [upgrade to premium](https://melapress.com/wordpress-2fa/pricing/?utm_source=wp+repo&utm_medium=repo+link&utm_campaign=wordpress_org&utm_content=wp2fa) to benefit from email support.

For any other queries, feedback, or if you simply want to get in touch with us, please use our [contact form](https://melapress.com/contact/?utm_source=wp+repo&utm_medium=repo+link&utm_campaign=wordpress_org&utm_content=wp2fa).

#### MAINTAINED & SUPPORTED BY MELAPRESS

Melapress develops high-quality WordPress management and security plugins such as Melapress Login Security, Melapress Role Editor, and WP Activity Log; the #1 user-rated activity log plugin for WordPress.

Browse our list of [WordPress security and administration plugins](https://melapress.com/?utm_source=wp+repo&utm_medium=repo+link&utm_campaign=wordpress_org&utm_content=wp2fa) to see how our plugins can help you better manage and improve the security and administration of your WordPress websites and users.
    
== Installing WP 2FA ==

###From within WordPress

1.  Navigate to ‘Plugins' > 'Add New’
2.  Search for ‘WP 2FA’
3.  Install & activate WP 2FA from your Plugins page
  
###Manually

1.  Download the plugin from the WordPress plugins repository
2.  Unzip the zip file and upload the folder to the '/wp-content/plugins/ directory'
3.  Activate the WP 2FA plugin through the ‘Plugins’ menu in WordPress

## As featured on:

- [WP Beginner](https://www.wpbeginner.com/plugins/how-to-add-two-factor-authentication-for-wordpress/)
- [IsitWP](https://www.isitwp.com/best-wordpress-security-authentication-plugins/)
- [WP Astra](https://wpastra.com/two-factor-authentication-wordpress/)
- [MainWP](https://mainwp.com/how-to-use-the-wp-2fa-plugin-on-your-child-sites/)
- [FixRunner](https://www.fixrunner.com/wordpress-two-factor-authentication/)
- [Inmotion Hosting](https://www.inmotionhosting.com/support/edu/wordpress/plugins/wp-2fa/)
- [WP Marmite](https://wpmarmite.com/en/wordpress-two-factor-authentication/)

== Frequently Asked Questions ==

= Does the plugin send any data to Melapress? =
No, the plugin does not send any data to us whatsoever. The only data we recieve is license data from the premium edition of the plugin.

= What 2FA methods are available with the plugin? =
The free edition of WP 2FA includes the following 2FA methods: Authenticator app 2FA and code over email. This allows you to use Google authenticator OTP The premium edition adds Yubikey, one-click email link, SMS 2FA, and Authy push notifications. 

= How can I ensure I do not get locked out? =
WP 2FA includes backup authentication methods so that if the primary authentication method fails, you and your users can still log in. The free version of the plugin includes backup codes, which can be configured during 2FA configuration or at any point after that from the profile page. The premium edition adds 2FA backup codes over email.

= What happens if I get locked out? =
In the unlikely event that you are unable to supply your 2FA code, there are several steps you can take to gain access to your WordPress dashbaord. First, check if there is another administrator who can reset your 2FA. If this is not possible, manually deactivate the plugin, log in without 2FA, re-activate the plugin, and then reconfigure your 2FA. 

=  Does WP 2FA support multi-site networks? = 
Yes, WP 2FA is multisite compatible. The plugin can be activated at the network level. 2FA policies can be enforced on all users, sub section of users, or per site on the network. It also supports network setups with different domains.

= Does the plugin receive updates? =
We update the plugin fairly regularly to ensure the plugin continues to run in tip-top shape while adding new features from time to time.

= Does the plugin support Google Authenticator? =
Yes, WP 2FA fully supports Google Authenticator on WordPress. [WP 2FA also supports many other 2FA authenticator apps](https://melapress.com/support/kb/wp-2fa-configuring-2fa-apps/?utm_source=wp+repo&utm_medium=repo+link&utm_campaign=wordpress_org&utm_content=wp2fa).

= Can I get support if I get stuck? =
Support for the free edition of the plugin is provided only via the WordPress.org support forums. You can also refer to our [support pages](https://melapress.com/support/?utm_source=wp+repo&utm_medium=repo+link&utm_campaign=wordpress_org&utm_content=wp2fa) for all the technical and product documentation.

If you are using the Premium edition, you get direct access to our support team via one-to-one [email support](https://melapress.com/support/submit-ticket/?utm_source=wp+repo&utm_medium=repo+link&utm_campaign=wordpress_org&utm_content=mls).

= How can I report security bugs? =
You can report security bugs through the Patchstack Vulnerability Disclosure Program. Please use this [form](https://patchstack.com/database/vdp/wp-2fa). For more details please refer to our [Melapress plugins security program](https://melapress.com/plugins-security-program/).

== Screenshots ==

1. The first-time install wizard allows you to setup 2FA on your website and for your user within seconds.
2. The wizards make setting up 2FA very easy, so even non technical users can setup 2FA without requiring help.
3. You can require users to enable 2FA and also give them a grace period to do so.
4. Users can also use one-time codes via email as a two-factor authentication method.
5. You can use policies to require users to instantly set up and use 2FA, so the next time they login they will be prompted with this.
6. You can give users a grace period until they configure 2FA. You can also specify what should the plugin do once the grace period is over.
7. It is recommended for all users to also generate backup codes, in case they cannot access the primary device.
8. In the user profile users only have a few 2FA options, so it is not confusing for them and everything is self explanatory.

== Changelog ==

= 2.9.0 (2025-07-31) =

* **New features**
	 * REST API endpoints for 2FA code verification and other operations, thus making it much easier to integrate the plugin in custom processes.
	 * Option to allow temporary login without 2FA for a specific user or number of users.
	 * New filter _wp_2fa_oob_redirect_url_ to assist with user redirection post-login when Link via email (OOB) 2FA method is in use.
	 * Quick Links section with useful inks.

 * **Plugin & functionality improvements**
	 * Bumped up the minimum supported PHP version from 7.3 to 7.4.
	 * Bumped up the minimum supported WordPress Core version from  5.0 to 5.5.
	 * Better support for setups in which access to the wp-login.php file is restricted or denied.
	 * Plugin no longer supports 2FA enforcement on users without any role, to adhere to the new Wordpress core changes.
	 * Improved performance: plugin now better loads and handles it's files and scripts .
	 * Updated the 2FA setup wizard UI – available methods are now displayed vertically for improved readability and layout consistency.
	 * Changed the default template of the 2FA code email for improved email deliverability (new installs only).
	 * Tweaked the redirection of users on Woocommerce to cater for latest Woocommerce version, ensuing correct and consistent redirection flow post-login.
	 * White Labeling - added option to enable help text to assist users during 2FA configuration for all methods.
	 * White Labeling - Changed the placeholder title on the 2FA code page text to "Verification code" for consistency.
	 * White Labeling - added a new white labeling option to enable/disable our plugin's signature from the 2FA Frontend configuration page.
	 * White Labeling - made more wizard elements translatable by assisting with localizing text inside JS elements.
	 * White Labeling - Tweaked the 2FA page code elements by introducing new unique classes, to make it easier for users to customize their logo with the right size and format.
	 * Switched the default setting for HOTP to now allow users to use another email address during configuration. 
	 * Removed old links and imagery related to Captcha 4WP plugin.
	 * Added [Melapress Role Editor](https://melapress.com/wordpress-user-roles-editor/) in the About Us page.
	 * Reviewed all links in the plugin; fixed few broken links and added UTM parameters.
	 * Tweaked the UI inside a few wizards and plugin pages to avoid orphaned words or hanging elements.
	 * When "Log out users after 2FA configuration" is enabled, users are no longer logged out after they configure a backup method only.
	 * Made the 2FA notice regarding WP 2FA Encrypt key storage in wp-config.php dismissable.
	 * Authy method was removed from the setup wizard - service is being decommissioned by Twilio.
	 * Added our own custom libraries for Twilio integration, replacing the official SDK for improved performance and reduced dependencies.
	 * Removed the "User licensing" tab from the Settings which was redundant (used by the old licensing model).
	 * Improved the code that retrieves the number of subsites on a multisite network.
	 * Woocommerce Integration - 2FA Configuration page from My Account dashboard is now correctly positioned above the Log Out button. 
	 * Yubico method will now show up in 2FA method selection wizard even when it's the only method enabled.
	 * Removed a redundant wizard steps when only one method was active (Yubico) for a smoother process.
	 * Updated the text and layout of the Yubikey configuration wizard.

 * **Bug fixes**
	 * Fixed a PHP Notice "Function _load_textdomain_just_in_time" which could constantly occur in certain site setups .
	 * Translations: Fixed an edge case where Admin settings switch to Dutch once .po files are loaded, preventing the inheritance of actual site language.
	 * Fixed a bug causing the WordPress logo to be hidden on the 2FA code page in the Premium edition of WP 2FA.
	 * Fixed a scenario where users could see the "Remove 2FA" button on their profile page even though 2FA was enforced and no grace period was allowed.
	 * Fixed a handful of user role Inheritance issues which were causing some 2FA policies to not be correctly enforced to certain roles.
	 * Fixed an error which could occur when redirecting a user to a non-existent URL after configuring 2FA. 
	 * Fixed a variety of PHP warnings related to Yubico, the out of band 2FA method, and the Reports page.
	 * Fixed a bug which could prevent users with SMS via Clickatell to use a backup code via email to log in.
	 * Fixed a bug which was causing the "grace period time left" shortcode to always show time in UTC format instead of site's timezone.
	 * Fixed a bug in which users using Yubico as primary method were unable to configure the email backup method.
	 * Added a check to avoid the plugin from writing multiple comments inside the wp-config.php file when the file is refreshed by third parties.
	 * Fixed a PHP deprecation: Function _print_emoji_styles_ which occured on fresh installations.
	 * Fixed a user reported edge case error involving WP 2FA and Paid Membership plugin when Authy 2FA method was in use.
	 * Fixed a scenario where the user could get locked out even though the setting to lock users with exceeded grace period was disabled.
	 * Fixed a user-reported PHP error - Uncaught Error: Call to a member function get_page_permastruct() on null.
	 * Fixed a some user-reported PHP errors that could occur inside Reports page under very specific circumstances.
	 * Fixed a UI glitch which could cause users to be prompted with "This page is asking you to confirm that you want to leave - information you've entered may not be saved." when configuring 2FA.
	 * Fixed a PHP 8.4 Deprecated notice: WP2FA_Vendor\BaconQrCode\Encoder\Encoder::chooseMode().
	 * Fixed a number of issues on how the 2FA frontend configuration pages are created on each subsite on a multisite nework.
	 * Fixed a shortcode behavior _{from_email}_ which was pulling the site admin email instead of the actual From email address.
	 * Fixed a user-reported edge case that could intermittently cause the wrong 2FA method to be selected during configuration, loading OTP via email wizard instead of the Authenticator app.
	 * Fixed a scenario where users with multiple roles on multiple websites have 2FA removed if "No role for this website" is selected.
	
Refer to the complete [plugin changelog](https://melapress.com/support/kb/wp-2fa-plugin-changelog/?utm_source=wordpress.org&utm_medium=referral&utm_campaign=WP2FA&utm_content=plugin+repos+description) for more detailed information about what was new, improved and fixed in previous version updates of WP 2FA.
