=== WP Add Mime Types ===
Contributors: kimipooh
Tags: mime,file extention
Requires at least: 6.2
Requires PHP: 7.4
Tested up to: 6.7.2
Stable tag: 3.1.2
License: GPL v2
License URI: http://www.gnu.org/licenses/gpl-2.0.html


The plugin additionally allows the mime types and file extensions to WordPress.
 
== Description ==

The plugin additionally allows the mime types and file extensions to WordPress. In other words, your WordPress site can upload various file extensions. 

== Installation ==

Please install this plugin and activate it.
If you use a language except English, please update the translation data in the updates of Dashboard.

If the multisite is enabled, please check the setting menu in the network administrator. 

= Usage =

 First of all, please check the "Media Type Settings" in the "Settings".
You can see the list of allowed mime types and file extensions by WordPress.

 When you add the mime type or file extension, the data will be added to last item in this list at the red color.

 About the mime type list, please see the list of mime types in the information of the Internet.
  Ex. http://www.freeformatter.com/mime-types-list.html

 The user who have the [manage_options](http://codex.wordpress.org/Roles_and_Capabilities#manage_options) permission can only add the setting.

 If you would like to translate it to your language, please visit the GlotPress from https://wordpress.org/plugins/wp-add-mime-types/ .

 If the multisite is enabled, the multisite network administrator can add/change/delete the mime type value in the multisite network setting menu. And the multisite network administrator or the site administrator can only see the past value (cannot change) before the site was migrated to the multisite.
 
== Frequently Asked Questions ==

=  It fails to upload a file for the security reasons of WordPress despite having set it up with this plugin! =
If it fails to upload a file for the security reasons of WordPress despite having set it up with this plugin, then the MIME type of the uploaded file recognised by WordPress may be different from the MIME type you have set. In that case, you need to find out how WordPress recognised the MIME type of the file.
So, turn on “Enable to debug output for file types recognized by WordPress when a file is uploaded by the media.” setting.
and upload a file and set “mime type” recognized by WordPress in the setting of this plugin.
Please keep in mind that the “Enable to debug output for file types recognised by WordPress when a file is uploaded by the media.” setting must be turned off after you finish checking the mime type. If it is left on, file uploads will not work.
Reference: https://docs.google.com/presentation/d/1o4YHqP7s-EcctYSEYM2L7HLJtqhjA_mkY4VMjvTdj20/edit?usp=sharing (Google Slide)

= How to check the uploaded file type from Media. =
WordPress recognizes the file mime type by finfo_file function (wp-includes/functions.php). However, sometimes, the standard MIME type of a file and the MIME type of a WordPress-recognized file are different. By enabling both this option (in setting menu) and the "Enable the attempt to determine the real file type of a file by WordPress core.", the file type is displayed if it is from Media. PLEASE keep in mind that a file uploads are stopped while they are being processed if the both of two options are enabled. Therefore, be sure to disable this debugging option after debugging.

= Cannot work =
If the added mime type does not work, please deactivate other mime type plugins or the setting of other mime type plugins.

For example, if you install Media Library Assistant plugin, please turn off "Enable Upload MIME Type Support" in the Upload tag in this plugin setting.

= Can the plugin support the multi extensions? =
Yes. The function was supported by Version 2.4.0.
WordPress sanitizes the filename in case of 2 or more extensions.
ex. XXX.YYY.ZZZ --> XXX_.YYY.ZZZ.
The plugin fixes the sanitized extension when a file is uploaded in the media in case of allowed extensions. 
ex. XXX.YYY.ZZZ -- sanitized --> XXX_.YYY.ZZZ -- fixed the plugin --> XXX.YYY.ZZZ
In detail, please see sanitize_file_name function in "wp-includes/formatting.php".

= Can I comment out in the setting value? =
Yes. You can comment out above version 2.3.0.

= Can the plugin avoid the security check for a file content by WordPress core? =
Yes. WordPress core has implemented the security check for a file content since version 4.7.1.
The plugin's default setting disables this security check .

= How do the plugin behave when it is installed and activated on the multisite network administration dashboard? =
The setting in the multisite network administration dashboard is taken precedence. The setting in each site administration dashboard is displayed, but the values aren't applied.

= How do the plugin behave when it is deactivated/uninstalled on the multisite network administration dashboard? =

The setting values in each site administration dashboard in case of activating the plugin in each site is applied. 

= Don't the setting values in the multisite network administration dashboard  and the setting values in each site administration dashboard influence each other? =

Yes, each setting values are saved as the other setting items.

= Is it possible to restrict the extensions?  =

Yes, it is available on the version 3.0.0.
 If the head in each line is set to '-', then the MIME type restricts.
ex. -bmp = image/bmp 
The files which has "bmp" file extention becomes not to be able to upload.

== Screenshots ==
1. Setting Menu
2. Setting Menu in case of the multisite
3. Ignore to the right of '#' on a line
4. Security Options

== Changelog ==
= 3.1.2 =
- Tested up WordPress 6.7.2 with PHP 8.3.19 and 8.4.5.
- PHP 7.4 will soon no longer be supported.

= 3.1.1 =
- Fixes the type format mismatch issues since PHP 8.1 or later.
* Tested up WordPress 6.6.1 with PHP 8.3.6.

= 3.1.0 =
- Fixes type format mismatch issues in variable initialisation.
- Tested up WordPress 6.4.2 with PHP 8.2.0.
- Changed to require WordPress 6.2+ and PHP 7.4+.
- Tested up WordPress 6.5.2 with PHP 8.3.6.

= 3.0.3 =
-  Fixed warning checking when retrieving serialized data.

= 3.0.2 =
-  Enhanced error checking when retrieving serialized data.

= 3.0.1 =
-  Fixed warning when the $settings data gets.

= 3.0.0 =
- Added the mime type restriction function by using '-' prefix. If the head in each line is set to '-', then the MIME type restricts. In case of "-bmp = image/bmp",  the files which has "bmp" file extention becomes not to be able to upload.
- Fixed the warning on the multisite.  
- Tested up WordPress 5.6.2 with PHP 8.0.0.
- Tested up WordPress 5.8
- Tested up WordPress 6.0

= 2.5.8 = 
* Fixed  the issue of the media uploads failing when activating this plugin for the first time, if the plugin settings are not saved whenever.

= 2.5.7 = 
* Removed the folder (trunk) for this plugin in this plugin folder. The “trunk” folder was not needed. Due to this, activating the plugin in version 2.5.6, you might get an error message "Error: The plugin does not have a valid header".

= 2.5.6 =
* Added the "Enable to debug output for file types recognized by WordPress when a file is uploaded by the media." security option. In detail, please see "Frequently Asked Questions" section.
* Tested up to WordPress 5.6 and PHP 7.4.

= 2.5.5 =
* Fixed the error "the "Too few argument" for WordPress 5.0 or previous versions.

= 2.5.4 =
* Added the function for removing this plugin's settings in the database when this plugin is uninstall. 
* Fixed the function names for not influencing the function name for other plugins.

= 2.5.3 = 
* Fixed the issue of "Undefined variable: f_exp_more2_flag" warning.

= 2.5.2 = 
* Improved the response of CSRF (Cross-Site Request Forgery) vulnerability for this plugin's settings.

= 2.5.1 = 
* Added the response of CSRF (Cross-Site Request Forgery) vulnerability for this plugin's settings.

= 2.5.0 = 
* Added the security option item in the admin menu for enabling the security check for a file content and for sanitizing the multiple file extensions within the filename by WordPress core.
* Supported the new language setting regarding load_plugin_textdomain function.

= 2.4.1 = 
* Changed remove_underscore function name to wpaddmimetypes_remove_underscore because of the avoidance of the name conflict.
* Tested up to WordPress 5.2.2 and PHP 7.4.

= 2.4.0 = 
* Support of the multi extension. Even if the file extension is "XXX.ZZZ", "XXX.YYY.ZZZ", "AAA.XXX.YYY.ZZZ" or more, it always picks up the tail of the extensions.

= 2.3.1 = 
* Tested up to WordPress 5.2.2 and PHP 7.3.

= 2.3.0 = 
* Tested up to WordPress 5.0 and PHP 7.2.

= 2.2.1 = 
* Tested up to WordPress 4.9.

= 2.2.0 = 
* Fixed foreach function warning if a setting value is empty.
* Added to escape HTML tags in a setting value using wp_strip_all_tags function.
* Added to ignore to the right of '#' on a line.

= 2.1.3 = 
* Tested up to WordPress 4.8 and PHP 7.1

= 2.1.2 = 
* Fixed the warning issue regarding explode function. 
* Tested up to WordPress 4.7.2

= 2.1.1 = 
* Fixed the issue that the safe files in WordPress, such as jpg, png, pdf cannot be uploaded. 

= 2.1.0 = 
* Tested up to WordPress 4.7.1
* Fixed finfo_file issue. See FAQ section.

= 2.0.6 = 
* Tested up to WordPress 4.7

= 2.0.5 = 
* Tested up to WordPress 4.6

= 2.0.4 = 
* Fixed the help message in the administration menu.

= 2.0.3 = 
* Fixed the help message in the administration menu.
* If the added mime type does not work, please turn off the mime type setting or deactivate other mime type plugins.

= 2.0.2 = 
* Fixed the compatibility with Media Library Assistant plugin.

= 2.0.1 = 
* Fixed the message in the setting menu

= 2.0.0 = 
* Supported Multisite.
* Tested up to WordPress 4.5.1

= 1.3.13 = 
* Tested up to WordPress 4.5.

= 1.3.12 = 
* Migrated the translation function to GlotPress. If you translate it to your language, please visit the GlotPress from https://wordpress.org/plugins/wp-add-mime-types/ .

= 1.3.11 = 
* Preparation of migrating the translation function to GlotPress.

= 1.3.10 = 
* Tested up to WordPress 4.4.2

= 1.3.9 = 
* Tested up to WordPress 4.4.1

= 1.3.8 = 
* Tested up to WordPress 4.4
* Fixed language translation setting.

= 1.3.7 = 
* Tested up to WordPress 4.3

= 1.3.6 = 
* Fixed load_plugin_textdomain setting.

= 1.3.5 = 
* Fixed load_plugin_textdomain setting.

= 1.3.4 = 
* Tested up to WordPress 4.2.2

= 1.3.3 = 
* Tested up to WordPress 4.1.1

= 1.3.2 = 
* Tested up to WordPress 4.0

= 1.3.1 =
* Tested up to WordPress 3.9.1

= 1.3.0 =
* Tested up to WordPress 3.9

= 1.2.1 =
* Fixed Language support

= 1.2.0 =
* Tested up to WordPress 3.8

= 1.1.0 =
* Tested up to WordPress 3.7.1

= 1.0.1 =
* Fixed the display error if the setting value is empty for the first time. 

= 1.0.0 =
* First Released.
* Language: English, Japanese


== Upgrade Notice ==

