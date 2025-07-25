=== Loginizer ===
Contributors: softaculous, loginizer, pagelayer
Tags: security, access, admin, Loginizer, login, logs, ban ip, failed login, ip, whitelist ip, blacklist ip, failed attempts, lockouts, hack, authentication, login, security, rename login url, rename login, rename wp-admin, secure wp-admin, rename admin url, secure admin, brute force protection
Requires at least: 3.0
Tested up to: 6.8
Requires PHP: 5.5
Stable tag: 2.0.1
License: LGPLv2.1
License URI: http://www.gnu.org/licenses/lgpl-2.1.html

Loginizer is a WordPress security plugin which helps you fight against bruteforce attacks.

== Description ==

Loginizer is a WordPress plugin which helps you fight against bruteforce attack by blocking login for the IP after it reaches maximum retries allowed. You can blacklist or whitelist IPs for login using Loginizer. You can use various other features like Two Factor Auth, reCAPTCHA, PasswordLess Login, etc. to improve security of your website.

Loginizer is actively used by more than 1000000+ WordPress websites.

You can find our official documentation at <a href="https://loginizer.com/docs">https://loginizer.com/docs</a>. We are also active in our community support forums on <a href="https://wordpress.org/support/plugin/loginizer">wordpress.org</a> if you are one of our free users. Our Premium Support Ticket System is at <a href="https://loginizer.deskuss.com">https://loginizer.deskuss.com</a>

Free Features :

* Brute force protection. IPs trying to brute force your website will be blocked for 15 minutes after 3 failed login attempts. After multiple lockouts the IP is blocked for 24 hours. This is the default configuration and can be changed from Loginizer -> Brute force page in WordPress admin panel.
* Failed login attempts logs.
* Blacklist IPs
* Whitelist IPs
* Custom error messages on failed login.
* Permission check for important files and folders.
* Allow only Trusted IP.
* Blocked Screen in place of the Login page.
* Email Notification on successful login.
* Let users login with LinkedIn

= Get Support and Pro Features =

Get professional support from our experts and pro features to take your site's security to the next level with <a href="https://loginizer.com/pricing">Loginizer-Security</a>.

Pro Features :

* MD5 Checksum - of Core WordPress Files. The admin can check and ignore files as well.
* PasswordLess Login - At the time of Login, the username / email address will be asked and an email will be sent to the email address of that account with a temporary link to login.
* Two Factor Auth via Email - On login, an email will be sent to the email address of that account with a temporary 6 digit code to complete the login.
* Two Factor Auth via App - The user can configure the account with a 2FA App like Google Authenticator, Authy, etc.
* Login Challenge Question - The user can setup a <i>Challenge Question and Answer</i> as an additional security layer. After Login, the user will need to answer the question to complete the login.
* reCAPTCHA - Google's reCAPTCHA v3/v2, Cloudflare Turnstile, hCAPTCHA can be configured for the Login screen, Comments Section, Registration Form, etc. to prevent automated brute force attacks. Supports WooCommerce as well.
* Rename Login Page - The Admin can rename the login URL (slug) to something different from wp-login.php to prevent automated brute force attacks.
* Rename WP-Admin URL - The Admin area in WordPress is accessed via wp-admin. With loginizer you can change it to anything e.g. site-admin
* CSRF Protection - This helps in preventing CSRF attacks as it updates the admin URL with a session string which makes it difficult and nearly impossible for the attacker to predict the URL.
* Rename Login with Secrecy - If set, then all Login URL's will still point to wp-login.php and users will have to access the New Login Slug by typing it in the browser.
* Disable XML-RPC - An option to simply disable XML-RPC in WordPress. Most of the WordPress users don't need XML-RPC and can disable it to prevent automated brute force attacks.
* Rename XML-RPC - The Admin can rename the XML-RPC to something different from xmlrpc.php to prevent automated brute force attacks.
* Username Auto Blacklist - Attackers generally use common usernames like admin, administrator, or variations of your domain name / business name. You can specify such username here and Loginizer will auto-blacklist the IP Address(s) of clients who try to use such username(s).
* New Registration Domain Blacklist - If you would like to ban new registrations from a particular domain, you can use this utility to do so.
* Change the Admin Username - The Admin can rename the admin username to something more difficult.
* Auto Blacklist IPs - IPs will be auto blacklisted, if certain usernames saved by the Admin are used to login by malicious bots / users.
* Disable Pingbacks - Simple way to disable PingBacks.
* SSO - Single Sign-on, let any user access to your WordPress Dashboard without the need to share username or password.
* Limit Concurrent Logins - It prevents user to login from different devices concurrently, you can define how many devices you want to allow, and how you want to restrict the user when concurrent limit is reached.
* Social Login - Users can login or register with their Google, Github, Facebook, X (Twitter), Discord, Twitch, LinkedIn, Microsoft with support for WooCommerce and Ultimate Member.
* Key Less Social Login - Use Loginizer's Social Auth for easy key less Social login configuration, now supports Google, GitHub, X, LinkedIn more to be added later

Features in Loginizer include:

* Blocks IP after maximum retries allowed
* Extended Lockout after maximum lockouts allowed
* Email notification to admin after max lockouts
* Blacklist IP/IP range
* Whitelist IP/IP range
* Check logs of failed attempts
* Create IP ranges
* Delete IP ranges
* Licensed under LGPLv2.1
* Safe & Secure


== Installation ==

Upload the Loginizer plugin to your blog, Activate it.
That's it. You're done!

== Screenshots ==

1. Login Failed Error message
2. Loginizer Dashboard page
3. Loginizer Brute Force Settings page

== Changelog ==

= 2.0.1 =
* [Feature Pro] Ultimate Member is now compatible with Loginizer's Bruteforce, Social Login, Captcha and Two-Factor Auth.
* [Feature Pro] Social Login using Loginizer's Login Keys, making Social Login single click setup.
* [Feature Pro] Support for Microsoft Social Login.
* [Bug Fix] [Pro] There was an issue with Hide WP-Admin in rename login, that has been fixed.
* [Bug Fix] [Pro] There was an issue with refresh button of 2FA App QR Code when it was displays on a page other than WP-Admin, that has been fixed.
* [Bug Fix] [Pro] Login Notification was not working with 2FA login this has been fixed.
* [Task] Social Login restructured and refactored.

= 2.0.0 =
* [Task] Tested with WordPress 6.8.
* [Bug-Fix] A minor issue has been fixed which was reported by HedgeByte Security.

= 1.9.9 =
* [Task] There was a warning on PHP 8.2, that has been fixed.
* [Bug-Fix] In some cases the session in Social Login was breaking that has been fixed.
* [Bug-Fix Pro] For some page builders, changing the login slug caused the wp-login.php 404 error page to not load shortcodes properly. This issue has now been fixed.

= 1.9.8 =
* [Feature Pro] Now you can hide the wp-admin totally from non logged in users.
* [Improvement Pro] Option to Disable Passwordless login for specific login page.
* [Task] There was a Typo in X Social Login button which has been fixed and X buttons won't have Formerly Twitter text.
* [Bug Fix Pro] There was an issue with Passwordless login for WooCommerce login page, that has been fixed.

= 1.9.7 =
* [Task] A notice has been tweaked to prevent confusion among users.

= 1.9.6 =
* [Task] Removed wpCentral Promo from Loginizer.

= 1.9.5 =
* [Task] A few typos in description of features have been fixed.

= 1.9.4 =
* [Task] Tested with WordPress 6.7, fixed translation Notice.
* [Bug-Fix] HOTP and Base32 caused conflict with some plugins that has been fixed.

= 1.9.3 =
* [Security] There was a security issue in the Pro version of the plugin which has been fixed, was reported by wesley (wcraft)[Wordfence]
* [Task] Improved Compatibility with Softaculous Plugin.

= 1.9.2 =
* [Task] Improved license handling.

= 1.9.1 =
* [Bug-Fix] Social Login was not working on WooCommerce or registration page, that has been fixed.
* [Bug-Fix] A PHP warning has been fixed.

= 1.9.0 =
* [Bug-Fix] For some users there was an issue in updating that has been fixed.

= 1.8.9 =
* [Task] Structural changes.
* [Task] Tested with WordPress 6.6.

= 1.8.8 =
* [Bug-Fix] Verison in one file was not updated, this has been fixed.

= 1.8.7 =
* [Feature] Social Login: Now you can let the users login through LinkedIn Login.
* [Feature] Send Login Notification as HTML email.
* [Pro Feature] Supports social login with Google, GitHub, Facebook, X(Formerly Twitter) and more Login Providers.

= 1.8.6 =
* [Bug-Fix] There was an issue with Login Notification body and subject, it was adding \(slashes) if "(double-quotes) where being used. This has been fixed.
* [Task] Removal of unwanted code.

= 1.8.5 =
* [Feature] Added Option to disable Login notification for whitelisted IPs.
* [Improvement] We have added variables for custom subject in Login notification.
* [Bug-Fix] Now the time shown in the Login Notification email, will respect the timezone set in the WordPress settings.
* [Bug-Fix] Error notice when 2FA fails had some CSS issue which has been fixed.
* [Task] We have remove unwanted code in reCAPTCHA.

= 1.8.4 =
* [Feature] Block Page, now instead of showing error on the Login page of user being blacklisted, you can just show a page with error, reducing the resource being used to show the error.
* [Feature] Email notification on successful login and you can enforce this on your users too.
* [Pro Feature] Added Cloudflare Turnstile, and hCaptcha.
* [Task] Tested with WordPress 6.5.

= 1.8.3 =
* [Task] We have removed unwanted code.

= 1.8.2 =
* [Task] Tested on WordPress 6.4.
* [Improvement] Now SSO can live for multiple Login attempts, default being 1 and maximum is 15 Login access.
* [Imrpovement] Now SSO can live longer for upto 2 days.
* [Bug-Fixes] A few Warning related to PHP 8.2 has been fixed

= 1.8.1 =
*[Bug-Fix] There was an issue while checking checksum, if the WordPress install was in en_US but the language was set to some other languages from the settings, then the checksum was comparing the checksums from the language selected in WordPress settings which is now always the language of the install, this has been fixed.

= 1.8.0 =
* [Feature][Pro] We have added Single Sign-on for you to create temporary login to share to let other login to your account without sharing password.
* [Refactor] We have reduced the amount of code that was being loaded when a login attempt was made by around 150KB.
* [Refactor] Screenshots of Loginizer were included in the plugin, we have shifted that to assets of WordPress.org, reducing the overall size of plugin by more than 100KB.

= 1.7.9 =
* [Bug-Fix] Users were getting PHP notice in init.php file that has been fixed.
* [Bug-Fix] Math cookie has been set as secure now.
* [Security] We were sanitizing an output in place of escaping it, that has been fixed [Reported by Erwan Le Rousseau from WPScan]

= 1.7.8 =
* [Task] Tested with WordPress 6.2
* [Feature] [Pro] Limit Concurrent user login, you can either block login attempt or revoke when limit of concurrent user is reached.
* [Feature] Login attempts stats chart on Loginizer Dashboard.

= 1.7.7 =
* [Feature] Ability to allow only Whitelisted IP's to be able to login with Trusted IP's.
* [Feature] [Pro] Option to add custom redirect on 2FA Login based on user role.
* [Bug-Fix] [Pro] User's were getting redirected to WP Admin when logging in from Checkout page in Passwordless and 2FA options that has been fixed.
* [Bug-Fix] Some users were getting PHP Warnings that has been fixed.

= 1.7.6 =
* [Security] Minor security issues reported by patchstack have been fixed with in 24 hours of reporting.
* [Bug-Fix] For some themes the Maths capatch input was invisible that has been fixed.

= 1.7.5 =
* [Task] Tested compatibility with WordPress 6.1
* [Bug Fix] There was an issue with sanitizing URL that has been fixed.

= 1.7.4 =
* [Feature] CSRF Protection adds a unique session key in your admin URL when you login to it, which adds another layer of security to your WordPress website as it makes it difficult to predict the URL hence making it difficult and nearly impossible to do CSRF attacks on your WordPress admin panel.
* [Task] 2FA Support for MasterStudy Custom Login
* [Bug Fix] Some users were facing an error when using 2FA App verification that has been fixed.

= 1.7.3 =
* [Bug Fix] Added validation not to allow values less than 0 for all Brute Force admin settings.

= 1.7.2 =
* [Improvement] [Pro] Allowed HTML characters in Passwordless email.
* [Bug Fix] Improved performance on sites running Loginizer with WooCommerce.
* [Bug Fix] Added validation not to allow values less than 0 in Brute Force admin settings.
* [Bug Fix] Some language strings were hardcoded in English and could not be translated. This is fixed and all strings can now be translated. 
* [Bug Fix] Resolved PHP Warnings and Notices on latest PHP versions.

= 1.7.1 =
* [Improvement] [Pro] Added error message to not allow using same slug for wp-login.php and wp-admin as it causes conflict.
* [Improvement] [Pro] Added exception for readme.html, license.txt and wp-config-sample.php while checking the checksum to avoid false alarm about checksum mismatch.
* [Bug Fix] [Pro] In WordPress Multisite, on changing the admin username the super admins list was not updated. This is fixed now. 
* [Task] Compatibility with WordPress 6.0 

= 1.7.0 =
* Compatible with WordPress 5.9
* [Feature] [Pro] Added option to choose recaptcha.net instead of google.com for countries that do not support google
* [Bug Fix] [Pro] Fix to email the correct unblock time when an IP is blocked for extended hours.

= 1.6.9 =
* [Bug Fix] [Pro] Fix to not show Loginizer 2FA Security Settings in Edit Account page in WooCommerce Customer area. It will be shown in Security (registered by Loginizer) tab instead.

= 1.6.8 =
* [Feature] Added option to export failed login attempts to CSV file.
* [Improvement] Added option to send failed login notifications to a custom email.
* [Improvement] [Pro] Added support for 2FA for WooCommerce customers
* [Bug Fix] [Pro] On WooCommerce customer login page the password field was not hidden when Passwordless login was enabled in Loginizer.
* [Bug Fix] [Pro] Autofill enabled in the browser caused the OTP field on 2FA login to be prefilled.

= 1.6.7 =
* [Feature] Added Bulk Export/Import Blacklist and Whitelist IPs via CSV.
* [Improvement] Added option to Blacklist selected IPs from Failed Login Attempts Logs.
* [Improvement] Added external link in Brute Force logs for IP information of the IPs attempting brute force.
* [Improvement] [Pro] Added Loginizer 2FA status column on Users list page to show 2FA preferences selected by users.
* [Improvement] [Pro] Added Show/Hide button for OTP field on 2FA login page.
* [Bug Fix] [Pro] Two Factor Authentication lead to 502 Bad Gateway error on WP Engine instances. This is resolved now.

= 1.6.6 =
* [Improvement] For new installs, the loginizer_logs table will now use the server default MySQL Engine.
* [Improvement] For the login attempts blocked by Loginizer, some other Activity Logs plugin still reported such blocked attempt as a failed login attempt. 
* [Bug Fix] In rare cases when the username received in failed login attempt was blank, Loginizer failed to save such requests in the failed login logs table. This is fixed now. 

= 1.6.5 =
* [Bug Fix] After Interim Login due to session timeout, the popup for login was not closed. This is fixed now.
* [Bug Fix] reCAPTCHA was not working on registration page with BuddyPress plugin. This is fixed now. 

= 1.6.4 =
This version includes a security fix and we recommend all users to upgrade to 1.6.4 or higher immediately.

* [Security Fix] : A properly crafted username used to login could lead to SQL injection. This has been fixed by using the prepare function in PHP which prepares the SQL query for safe execution.

* [Security Fix] : If the IP HTTP header was modified to have a null byte it could lead to stored XSS. This has been fixed by properly sanitizing the IP HTTP header before using the same. 

= 1.6.3 =
* [Fix] Fixed a PHP Notice that was caused by a change released yesterday. 

= 1.6.2 =
* [Feature] Added option to send Password Less Login email as HTML.
* [Fix] When reCAPTCHA was disabled on Woocommerce checkout page, Loginizer reported captcha error if a user tried to register on checkout page. This is fixed now. 
* [Fix] The email sent to admin for brute force login attempts will now contain the site url as well.
* [Fix] Fixed PHP Notice on Two Factor Authentication page.

= 1.6.1 =
* [Fix] The captcha on Registration form when using WooCommerce was not being rendered if the "WooCommerce Checkout" captcha setting was disabled in Loginizer. This is fixed now and this captcha can be disabled with "Registration Form" captcha setting in Loginizer. 
* [Fix] Minor checkbox pre-filling UI fix on Two Factor Authentication page.

= 1.6.0 =
* [Feature] Admin can white list an IP or an IP range for Two Factor Authentication.
* [Fix] If the plugins or themes which are included in the default WordPress package were not updated, the Checksum reported that the files for such plugins and themes did not matched. This is fixed now. 

= 1.5.9 =
* [Task] Admins can now customize email template for 2FA OTP via email.
* [Task] Admins can now customize the 2FA messages on login screen.
* [Fix] Changed the OTP via App field on login page to password type.

= 1.5.8 =
* [Task] Permission for / folder was suggested as 0755 and 0750 permission which is secure was reported as insecure. This is fixed now. 
* [Fix] Prevent PHP Deprecated Warning on plugin upgrade page on servers running PHP 7.3+

= 1.5.7 =
* [Fix] Prevent PHP Notice on 1st failed login attempt from an IP.

= 1.5.6 =
* [Task] Admins can now subscribe to our newsletter if they decide to opt-in.

= 1.5.5 =
* [Bug Fix] Remember me during login was not working with 2FA features. This is fixed. 
* [Task] Loginizer is now supported for translation via WordPress.
* [Task] Added option to fully customize the Lockout error message.

= 1.5.4 =
* [Task] Added option to customize Lockout Error message.

= 1.5.3 =
* [Task] Compatible with WordPress 5.5
* [Bug Fix] Due to a conflict with some plugin the upgrade for Loginizer Premium version did not work. This is fixed.

= 1.5.2 =
* [Task] Some strings were not available in translations. They can now be translated.

= 1.5.1 =
* [Task] Allowed to change the username of any administrator account. Previously it was supported only for user id 1
* [Bug Fix] Fixed some lines that generated PHP notice

= 1.5.0 =
* [Task] Admins can now customize "attempt(s) left" error message.

= 1.4.9 =
* [Bug Fix] Prevent brute force on 2FA pages.

= 1.4.8 =
* [Premium Feature] Added Google reCAPTCHA v3 and v2 invisible.

= 1.4.7 =
* [Security Fix] Our team internally conducted a security audit and have fixed couple of security issues. We recommend all users to upgrade to the latest version asap.

= 1.4.6 =
* [Task] Added Timezone offset in the Brute Force attempts list to get the exact time of the failed login attempt.
* [Bug Fix] For HTTP_X_FORWARDED_FOR if the value had multiple IPs including proxied IPs, the user IP detection failed. This is fixed.
* [Bug Fix] Undefined variable was used in the title on the dashboard page. This is fixed.

= 1.4.5 =
* [Announcement] Loginizer has joined forces with Softaculous team. 
* [Task] Added OTP validity time in the email sent for OTP for login.
* [Bug Fix] In the premium version the Math Captcha used to fail in some conditions. This is fixed. 

= 1.4.4 =
* [Task] Made Loginizer compatible with PHP 7.4
* [Bug Fix] The password field was not hidden in some themes for PasswordLess Login. This is fixed.

= 1.4.3 =
* [Bug Fix] At the time of login if recaptcha or 2FA using OTP was enabled and if you check mark on "Remember me", the login used to go to an invalid redirect URL and did not load anything. This has been fixed now.

= 1.4.2 =
* [Task] Tested up to: WordPress 5.2.0
* [Bug Fix] Placement of Captcha corrected for WooCommerce at the time of checkout for end users.
* [Bug Fix] Checksum check shall now skip for the files which are present in default WordPress package and does not exist in the installation like deleted theme(s)/plugin(s).
* [Bug Fix] Grammar correction

= 1.4.1 =
* [Task] Tested up to: WordPress 5.0.2
* [Task] Refresh license will throw an error if the response received from our server is invalid
* [Bug Fix] The OTP input box (with respect to 2FA via SMS) was empty if the user was freshly registered and did not login at all. This is fixed.

= 1.4.0 =
* [Feature] New Registration Domain Blacklist - If you would like to ban new registrations from a particular domain, you can use this utility to do so.
* [Feature] Made Loginizer Security for BuddyPress compatibility.
* [Task] Added a method to reset wp-admin rename settings if you get locked out.
* [Bug Fix] There is an XSS bug introduced in version 1.3.8. This is fixed. Please upgrade ASAP.
* [Bug Fix] In the user 2FA security wizard, the default selected option was wrongly shown when the user had not set any preference for 2FA. This is fixed. 

= 1.3.9 =
* [Feature] Added an option to Enable / Disable Brute Force checks.
* [Feature] Added the feature to log the URL of the page from which the brute force attempt is being made.
* [Bug Fix] Blanking the login slug used to show the value after submission. This is fixed.
* [Bug Fix] Allowed HTML chars in wp_admin_msg for renaming WP-ADMIN.

= 1.3.8 =
* [Feature] Added Roles selection for Two Factor Authentication. The Admin can now enable 2FA for specific roles.
* [Feature] Added a Tester for WP-Admin Slug Renaming feature. Now you can test the new slug before saving it.
* [Feature] Added option to customize the Passwordless email being sent to the user.
* [Feature] Added a custom WP-Admin restriction message if wp-admin is restricted.
* [Feature] Added an option to Delete the entire Blacklist / Whitelist IP Ranges.
* [Feature] Custom IP Header added as an option for detecting the IP as per the Proxy settings of a server.
* [Task] Added an option to clear reCAPTCHA settings.
* [Task] Added Debugger in Updater
* [Task] Updater will show "Install License Key to check for Updates"
* [Bug Fix] In WooCommerce the number of login retries left was not being shown. This is fixed.

= 1.3.7 =
* [Bug Fix] Blacklist and Whitelist IPs were not being deleted. This is fixed.

= 1.3.6 =
* [Feature] Pagination added to the Blacklist and Whitelist IPs
* [Bug Fix] There used to be a login issue over SSL when wp-admin area is renamed. This is fixed.
* [Bug Fix] SQL Injection fix for X-Forwarded-For. This is fixed. Vulnerability was found by Jonas Lejon of WPScans.com
* [Bug Fix] There was a missing referrer check in Blacklist and Whitelist IP Wizard. This is fixed.

= 1.3.5 =
* [Feature] Added a simple Math Captcha to show Maths Questions, if someone doesn’t want to use Google Captcha
* [Feature] Added a wizard for admins to set their own language strings for Brute Force messages
* [Bug Fix] In WooCommerce the Lost Password, Reset Password and Comment Form captcha verification failed. This is fixed.
* [Bug Fix] Hide Captcha for logged in users was not working. This is fixed.
* [Bug Fix]	Twitter box shown in Loginizer was not accessed over HTTPS.

= 1.3.4 =
* [Bug Fix] Fixed the BigInteger Class for PHP 7 compatibility.

= 1.3.3 =
* [Feature] IPv6 support has been added.
* [Feature] The last attempted username will now be shown in the Login Logs.
* [Bug Fix] If the login page had been renamed, and wp-login.php was accessed over HTTPS, the login screen was shown instead of 404 not found. This is now fixed.
* [Bug Fix] If the user had used a "/" in the rename login slug, the new slug would not work without the "/" in the URL. This is now fixed.
* [Bug Fix] The license key could get reset in some cases. This also caused plugin updates to fail. This is now fixed.
* [Bug Fix] Wild Cards “*” in the Username Auto Blacklist did not work. This is now fixed.
* [Bug Fix] The documentation in the plugin was pointing to a wrong link. This is now fixed.

= 1.3.2 =
* [Feature] Rename the wp-admin access URL is now possible with Loginizer
* [Feature] WooCommerce support has been improved for reCAPTCHA 
* [Feature] Loginizer will now show a Notification to the Enduser to setup the preferred 2FA settings
* [Feature] Added option to choose between REMOTE_ADDR, HTTP_CLIENT_IP and HTTP_X_FORWARDED for websites behind a proxy 
* [Task] Multiple reCAPTHCA on a single page is now supported
* [Task] Added a link to Google's reCAPTCHA website for easy access in our reCAPTCHA wizard

= 1.3.1 =
* [Feature] Admin's can now remove a user's Two Factor Authentication if needed
* [Feature] Added an option to change the Admin Username
* [Feature] Auto Blacklist IPs if certain usernames saved by the Admin are used to login by malicious bots / users
* [Feature] The Login attempt logs will now be shown as per the last attempt TIME and in Descending Order
* [Feature] Added an option to Reset the Login attempts for all or specific IPs 

= 1.3.0 =
* [Feature] Added MD5 File Checksum feature. If any core files are changed, Loginizer will log the differences and notify the Admin
* [Feature] Added an option to make Email OTP as the default Two Factor Auth when a user has not set the OTP method of their choice
* [Feature] Added WooCommerce support for Captcha forms
* [Feature] Added pagination in the Brute Force Logs Wizard
* [Bug Fix] Disabling and Re-Enabling Loginizer caused an SQL error

= 1.2.0 =
* [Feature] Rename Login with Secrecy : If set, then all Login URL's will still point to wp-login.php and users will have to access the New Login Slug by typing it in the browser.
* [Task] The brute force logs will now be sorted as per the time of failed login attempts
* [Bug Fix] Dashboard showed wrong permissions if wp-content path had been changed
* [Bug Fix] Added Directory path to include files which caused issues with some plugins

= 1.1.1 =
* [Bug Fix] Added ABSPATH instead of get_home_path()

= 1.1.0 =
* [Feature] PasswordLess Login
* [Feature] Two Factor Auth - Email
* [Feature] Two Factor Auth - App
* [Feature] Login Challenge Question
* [Feature] reCAPTCHA
* [Feature] Rename Login Page
* [Feature] Disable XML-RPC
* [Feature] Rename XML-RPC
* [Feature] Disable Pingbacks
* [Feature] New Dashboard
* [Feature] System Information added in the new Dashboard
* [Feature] File Permissions added in the new Dashboard
* [Feature] New UI
* [Bug Fix] Fixed bug to add IP Range from 0.0.0.1 - 255.255.255.255
* [Bug Fix] Removed /e from preg_replace causing warnings in PHP

= 1.0.2 =

* Fixed Extended Lockout bug
* Fixed Lockout bug
* Handle login attempts via XML-RPC

= 1.0.1 =

* Database structure changes to make the plugin work faster
* Minor fixes

= 1.0 =

* Blocks IP after maximum retries allowed
* Extended Lockout after maximum lockouts allowed
* Email notification to admin after max lockouts
* Blacklist IP/IP range
* Whitelist IP/IP range
* Check logs of failed attempts
* Create IP ranges
* Delete IP ranges
* Licensed under LGPLv2.1
* Safe & Secure
