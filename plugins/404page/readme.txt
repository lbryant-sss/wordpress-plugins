=== Smart Custom 404 Error Page ===
Contributors: nerdpressteam, petersplugins, jchristopher
Tags: 404, 404 page, custom 404, not found, 404 error
Author: NerdPress
Author URI: https://www.nerdpress.net
Tested up to: 6.8
Stable tag: 11.4.8
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Create a custom 404 error page the easy way! No coding, and no redirects.

== Description ==

Bringing visitors to your website takes time and effort. Every visitor is important. The default 404 error page of most themes does not provide any information on what to find on your site. A first-time visitor, who does not know you, is left in a dead end and leaves your website. Set up a helpful custom 404 error page to keep them on your site!

This handy plugin allows you to easily create your own 404 error page without any effort and it works with almost every theme.

== Out of Retirement! ==

NerdPress has adopted Smart Custom 404 Page! [Read the announcement here.](https://www.nerdpress.net/announcing-404-page/)

We've been fans of this plugin for many years, and we're grateful for Peter's many years of service to the community. Peter retired from plugin development in October 2023, so we've jumped in to help ensure this plugin continues to work well now and in the future.

== Usage ==

Create your custom 404 error page just like any other page using the WordPress Editor (`Pages > Add New`). Then go to `Appearance > 404 Error Page` and select the created page as your custom 404 error page. That's it!

== Why use this plugin? ==

Unlike similar plugins the 404page plugin **does not create redirects**. That’s **quite important** because a correct code 404 is delivered which tells search engines that the page does not exist and has to be removed from the index.

Additionally, the 404page plugin **does not create additional server requests**. 

== Requirements ==

The only requirement for this plugin is that you change the Permalink Structure in `Settings > Permalinks` to anything else but "Plain." This also activates the WordPress 404 error handling.

== Block & Shortcode ==

= Block =

The Plugin offers a block "URL causing 404 error" for the block-based editor to show the URL that caused the error. The block offers three display options:

* "Page" to show the page including path ( e.g. `does/not/exist` )
* "Domain Path" to show the URL without protocol and parameters ( e.g. `example.com/does/not/exist` )
* "Full" to show the complete URL ( e.g. `https://example.com/does/not/exist?p=1` )

= Shortcode =

The Plugin offers a shortcode "pp_404_url" for the classic editor to show the URL that caused the error. There are three possible options:

* **`[pp_404_url page]`** to show the page including path ( e.g. `does/not/exist` )
* **`[pp_404_url domainpath]`** to show the URL without protocol and parameters ( e.g. `example.com/does/not/exist` )
* **`[pp_404_url]`** or **`[pp_404_url full]`** to show the complete URL ( e.g. `https://example.com/does/not/exist?p=1` )

== Plugin Privacy Information ==

* This plugin does not set cookies
* This plugin does not collect or store any data
* This plugin does not send any data to external servers

== For developers ==

= Action Hook =
The plugin adds an action hook `404page_after_404` which you can use to add extra functionality. The exact position the action occurs after an 404 error is detected depends on the Operating Method. Your function must not generate any output. There are no parameters.

= Constant =
If the 404page plugin is installed and activated it defines the PHP constant `PP_404`. Check existence of it to detect the 404page plugin.

= Functions =

The Plugin provides the following functions:

* **`pp_404_is_active()`** to check if there is a custom 404 page selected and the selected page exists
* **`pp_404_get_page_id()`** to get the ID of the 404 page 
* **`pp_404_get_all_page_ids()`** to get an array of page IDs in all languages
* **`pp_404_get_the_url( $type )`** to get the URL that caused the 404 error
  * Parameter $type string Optional
    * "page" to get the page including path ( e.g. `does/not/exist` )
	* "domainpath" to get the URL without protocol and parameters ( e.g. `example.com/does/not/exist` )
	* "full" (default) to get the complete URL ( e.g. `https://example.com/does/not/exist?p=1` )

= Native Mode =

If you are a theme developer you can add native support for the 404page plugin to your theme for full control.

== Changelog ==

= 11.4.8 (2024-10-02) =
* Address potential XSS vulnerability. Thanks to Webbernaut for responsible disclosure.

= 11.4.7 (2024-09-16) OUT OF RETIREMENT! =
* NerdPress has adopted Smart Custom 404 Page! [Read the announcement here.](https://www.nerdpress.net/announcing-404-page/)
* Tested up to WP 6.6
* Modernized direct file access protection and removed closing PHP tags

= 11.4.6 (2024-04-17) URGENT BUGFIX =
* Bugfix after Cleanup

= 11.4.5 (2024-04-16) CLEANUP =
* Cleanup

= 11.4.4 (2022-10-05) FINAL VERSION =
* removed all links to webiste
* removed request for rating
* removed manual

= 11.4.3 (2022-11-05) =
* bugfix for WP 6.1

= 11.4.2 (2022-11-01) =
* also add class error404 to body tag if page is called directly
* plugin renamed

= 11.4.1 (2022-10-16) =
* bugfix: load Javascript for Block only when needed

= 11.4.0 (2022-10-13) =
* Block added
* Shortcode added
* Function pp_404_get_the_url() added

= 11.3.1 (2022-04-05) =
* just cosmetics
* Plugin Foundation updated to PPF08

= 11.3.0 (2021-01-06) =
* new option to always send an 410 instead of an 404
* Plugin Foundation updated to PPF07

= 11.2.6 (2020-08-23) =
* Plugin Foundation updated to PPF06

= 11.2.5 (2020-08-22) =
* minor UI adjustments

= 11.2.4 (2020-08-16) =
* bug fix for Flamingo ([see topic](https://wordpress.org/support/topic/error-page-trashed/)) plus potentially other plugins (thanks to [garfiedo](https://wordpress.org/support/users/garfiedo/) for supporting me to find the reason)

= 11.2.3 (2020-07-05) =
* fix for Polylang ([see topic](https://wordpress.org/support/topic/undefined-function-pll_get_post/))

= 11.2.2 (2020-03-28) =
* changes to the notification for hopefully better compatibility
* Plugin Foundation swtiched to PPF04

= 11.2.1 (2020-01-04) =
* if W3 Total Cache is installed and caching is active URLs that result in an 404 error are automatically excluded from caching

= 11.2.0 (2020-01-01) =
* if WP Super Cache is installed and caching is active URLs that result in an 404 error are automatically excluded from caching

= 11.1.4 (2019-12-29) =
* urgent bug fix for PPF03

= 11.1.3 (2019-12-29) =
* Plugin Foundation updated to PPF03, no functional changes

= 11.1.2 (2019-11-19) =
* exclude 404 page from XML sitemap generated by Jetpack

= 11.1.1 (2019-11-16) =
* from now on it is not only detected if Yoast SEO Plugin is active, but also if the sitemap feature is activated

= 11.1.0 (2019-11-10) =
* now uses Plugin Foundation PPF02 for plugin compatibility
* introduces two new functions for developers pp_404_get_page_id() and pp_404_get_all_page_ids()

= 11.0.5 (2019-10-22) =
* bugfix for Yoast SEO XML Sitemap ([see topic](https://wordpress.org/support/topic/small-bug-with-wpseo_exclude_from_sitemap_by_post_ids/))
* added a note to settings page if Yoast SEO is active

= 11.0.4 (2019-10-06) =
* bugfix for WPML

= 11.0.3 (2019-09-01) =
* bugfix for REST API call (see [here](https://wordpress.org/support/topic/bug-woocommerce-rest-api-500-error/))

= 11.0.2 (2019-08-30) =
* two bugs fixed (see [here](https://wordpress.org/support/topic/version-11-0-1-error-in-log-file/) and [here](https://wordpress.org/support/topic/cant-activate-compatibility-mode/))

= 11.0.1 (2019-08-13) =
* fix for PHP 7.1 - __construct() access level in subclass - this is an PHP error that was fixed in PHP 7.2, but I've changed my code to also work with PHP 7.1

= 11.0.0 (2019-08-13) =
* mostly rewritten based on my own newly created Plugin Foundation

= 10.5 (2019-04-01) =
* some more security improvements

= 10.4 (2019-03-31) =
* security vulnerability in AJAX call fixed (thanks to [Julio Potier](https://secupress.me/) for pointing me to this)

= 10.3 (2019-02-21) =
* fix for compatibility with iThemes Sync ([ticket](https://wordpress.org/support/topic/ithemes-sync-issue/))

= 10.2 (2019-02-19) =
* just another small change to prevent from potential problems with version 10

= 10.1 (2019-02-14) =
* error fixed ([ticket](https://wordpress.org/support/topic/version-10-crashes-system/))

= 10 (2019-02-14) =
* workaround for WordPress Permalink bug [#46000](https://core.trac.wordpress.org/ticket/46000)
* code improvement
* performance tuning

= 9 (2019-01-24) =
* Gutenberg note added

= 8 (2019-01-11) =
* fixed compatibility issue with latest WPML version
* code improvement
* UI improvements

= 7 (2018-07-16) =
* corrected wrong image path
* added video links to admin page
* code improvements

= 6 (2018-06-18) =
* exclude 404 page from XML sitemap generated by Yoast SEO
* further UI-improvements

= 5 (2018-03-05) =
* show an indicator if the currently edited page is a 404 error page
* minor code- & UI-improvements

= 4 (2018-03-05) =
* bugfix for bbPress ([see topic](https://wordpress.org/support/topic/not-fully-bbpress-compatible/))

= 3.3 (2017-11-16) =
* support for right-to-left-languages added
* faulty display in WP 4.9 fixed

= 3.2 (2017-10-05) =
* new feature to send an HTTP 410 error for deleted objects

= 3.1 (2017-07-24) =
* bugfix for Polylang ([see topic](https://wordpress.org/support/topic/3-0-breaks-polylang-support/))
* bugfix for CLI ([see topic](https://wordpress.org/support/topic/uninstall-php-from-cli-failed/))
* add debug class to body tag
* also add body classes for Customizr theme
* do not add error404 class if already exists
* further redesign admin interface

= 3.0 (2017-07-05) =
* new feature to force 404 error after loading page
* new feature to disable URL autocorrection guessing 
* finally removed Polylang stuff disabled in 2.4
* redesigned admin interface
* code improvement

= 2.5 (2017-05-19) =
* hide 404 page from search results on front end (if WPML is active, all languages are hidden)
* do not fire a 404 in Compatibility Mode if the [DW Question & Answer plugin by DesignWall](https://www.designwall.com/wordpress/plugins/dw-question-answer/) is active and a question has no answers

= 2.4 (2017-03-08) =
* ensure that all core files are loaded properly ([see topic](https://wordpress.org/support/topic/had-to-deactivate-404page-to-make-wordpress-correctly))
* Polylang plugin does no longer require Compatibility Mode ([see topic](https://wordpress.org/support/topic/still-displaying-the-themes-404-page-with-polylang/))
* hide all translations if WPML is installed and "Hide 404 page" is active (thanks to the [WPML](https://wpml.org/) guys for pointing me at this)
* post status fix ([see topic](https://wordpress.org/support/topic/doesnt-work-with-custom-post-status/))
* [Enfold theme](https://themeforest.net/item/enfold-responsive-multipurpose-theme/4519990?ref=petersplugins) issue fix (thanks to the guys at [Kriesi.at](http://www.kriesi.at/) for supporting me)

= 2.3 (2016-11-21) =
* a few minor bugfixes solve some problems with page templates in certain combinations

= 2.2 (2016-09-26) =
* automatic switch to Compatibility Mode for several plugins removed
* enhanced support for WPML and Polylang
* remove the 404 page from search results (for all languages if WPML or Polylang is used)
* remove the 404 page from sitemap or other page lists (for all languages if WPML or Polylang is used)
* bugfix for author archives
* confusing admin message removed

= 2.1 (2016-04-22) =
* introduction of selectable Operating Methods
* several changes to Compatibility Mode for improved WPML and bbPress compatibility plus compatibility with Page Builder by SiteOrigin
* Polylang compatibility
* automatic switch to Compatibility Mode if WPML, bbPress, Polylang or Page Builder by SiteOrigin is detected
* completely new Customizr Compatibility Mode (automatically enabled if Customizr is detected)
* firing an 404 error in case of directly accessing the 404 error page can now be deactivated
* WP Super Cache support
* option to hide the 404 error page from the Pages list
* 404 error test
* plugin expandable by action
* delete all settings on uninstall

= 2.0 (2016-03-08) =
* WPML compatibility
* bbPress compatibility
* Customizr compatibility
* directly accessing the 404 error page now throws an 404 error
* class `error404` added to the classes that are assigned to the body HTML element
* the settings menu was moved from 'Settings' to 'Appearance'
* translation files removed, using GlotPress exclusively

= 1.4 (2015-08-07) =
* edit the 404 page directly from settings page
* Portuguese translation

= 1.3 (2015-01-12) =
* technical improvement (rewritten as class)
* cosmetics

= 1.2 (2014-07-28) =
* Spanish translation
* Serbo-Croatian translation

= 1.1 (2014-06-03) =
* Multilingual support added
* German translation

= 1.0 (2013-09-30) =
* Initial Release

== Upgrade Notice ==

= 11.4.3 =
bugfix for WP 6.1

= 11.4.2 =
also add class error404 to body tag if page is called directly

= 11.4.1 =
bugfix: load Javascript for Block only when needed

= 11.4.0 =
new block, new shortcode, new function

= 11.3.1 =
internal improvements without functional changes

= 11.3.0 =
new option to always send an 410 instead of an 404

= 11.2.6 =
Plugin Foundation updated to PPF06

= 11.2.5 =
minor UI adjustments

= 11.2.4 =
bug fix for Flamingo 

= 11.2.3 =
fix for Polylang

= 11.2.2 =
notification compatibility

= 11.2.1 =
if W3 Total Cache is installed and caching is active URLs that result in an 404 error are automatically excluded from caching

= 11.2.0 =
if WP Super Cache is installed and caching is active URLs that result in an 404 error are automatically excluded from caching

= 11.1.4 =
urgent bug fix for PPF03

= 11.1.3 =
Plugin Foundation updated to PPF03

= 11.1.2 =
exclude 404 page from XML sitemap generated by Jetpack

= 11.1.1 =
Yoast SEO sitemap feature detection

= 11.1.0 =
Plugin Foundation updated to version PPF02
two new functions for developers

= 11.0.5 =
bugfix for Yoast SEO XML Sitemap

= 11.0.4 =
bugfix for WPML

= 11.0.3 =
bugfix for REST API call

= 11.0.2 =
two bugs fixed

= 11.0.1 =
urgent hotfix for PHP 7.1

= 11.0.0 =
now uses my own Plugin Foundation

= 10.5 =
some more security improvements

= 10.4 =
security vulnerability in AJAX call fixed

= 10.3 =
fix for compatibility with iThemes Sync

= 10.2 =
preventive fix

= 10.1 =
error fix

= 10 =
workaround for WordPress bug

= 9 =
Gutenberg note added

= 8 =
fixed compatibility issue with latest WPML version

= 7 =
corrected wrong image path, added video links

= 6 =
exclude 404 page from XML sitemap generated by Yoast SEO

= 5 =
show an indicator if the currently edited page is a 404 error page

= 4 =
bugfix for bbPress

= 3.3 =
support for right-to-left-languages

= 3.2 =
new feature to send an HTTP 410 error for deleted objects

= 3.1 =
fixed two bugs, plus further enhancements

= 3.0 =
new features added to force 404 error after loading page and to disable URL autocorrection guessing, plus further enhancements

= 2.5 =
Hide 404 page from search results, compatibility with DW Question & Answer plugin

= 2.4 = 
Version 2.4 fixes several issues. See [changelog](https://wordpress.org/plugins/404page/changelog/) for details.

= 2.3 =
A few minor bugfixes solve some problems with page templates in certain combinations.

= 2.2 =
Enhanced compatibility. Automated Operating Method select removed. Several fixes.

= 2.1 =
Introduced Compatibility Mode, improved compatibility with several plugins. 

= 2.0 =
Version 2.0 is more or less a completely new development and a big step forward.

= 1.4 =
Editing of the 404 page is now possible directly from settings page. Portuguese translation added.
