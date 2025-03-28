=== Widget for Social Page Feeds ===
Contributors: Milap
Tags: facebook feeds, facebook like box, facebook like button, facebook feed widget, social post feed
Donate link: https://www.paypal.me/MilapPatel
Requires at least: 3.0.1
Tested up to: 6.7.2
Stable tag: 6.4.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin adds a simple Facebook Page Like Widget to your WordPress sidebar, footer area (as a widget), and page or post (as a Shortcode).

== Description ==

**Formerly "Facebook Page Like Widget".**

> Did you find this plugin helpful? Please consider [leaving a 5-star review](https://wordpress.org/support/plugin/facebook-pagelike-widget/reviews/?filter=5#new-post).

> Did this plugin made your life easy? Please consider [donating](https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=neetap179@gmail.com&lc=US&item_name=Providing+Excellent+WordPress+plugin+support&no_note=0&no_shipping=2&curency_code=USD&bn=PP-DonationsBF:btn_donateCC_LG.gif:NonHosted).

One of the most popular & lightweight plugin for Facebook page feeds widget with over 1.5 Million downloads and 80,000+ active installs.

How to use latest version 6.4:

https://www.youtube.com/watch?v=qayeaqlmofA

How to use older versions:

http://www.youtube.com/watch?v=8gulPNAd264


Please subscribe to my [YouTube channel](https://www.youtube.com/c/CodeCanvas/) for more technical videos.

This widget will provide you the most simple and attractive way to display Facebook page likes into your WordPress sidebar. 

**Why should you choose Facebook Page Like Widget from the many other plugins?**

* Light weight & easy to configure
* Add application id from your created Facebook application (Or you may use default application id), add it into widget & also URL of your Facebook page. 
* Configuration options like show/hide posts from timeline, show/hide cover, show/hide profile photos , show small header, width options, language selection, custom css.
* Shortcode support.
* Fast & helpful support.

It supports short code, open your Post or Page, Add **[fb_widget]** into Post or Page, Save it. You are done. Check FAQ for more Shortcode options.

**Paid Support**

If you need my help with installation / configurations / issues with my plugin, reach me at below for paid support,

* Gmail : cemilap.88@gmail.com
* Blog : https://patelmilap.wordpress.com/contact-me/
* Skype : milap_for_skype
* Facebook : https://www.facebook.com/milap112

I will try my best to reply you within 1 business day.

If you loved my plugin & support, please leave your review [Here](https://wordpress.org/support/view/plugin-reviews/facebook-pagelike-widget?filter=5) , so people can use it with confidence.

= Recommended Plugins =

The following plugins are recommended for users:

* [Ultimate Twitter Feeds](https://wordpress.org/plugins/ultimate-twitter-feeds/) With Ultimate Twitter Feeds Widget, you can display your Twitter Profile feeds, Profile List Feeds and Single Tweet on your website quickly.

= Privacy Notices =

With the default configuration, this plugin, in itself, does not:

* use cookies.
* track users by stealth.
* write any user personal data to the database.
* send any data to external servers.

== Installation ==

Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation and then activate the Plugin from Plugins page.

For more details,

http://codex.wordpress.org/Managing_Plugins

== Frequently Asked Questions ==


= How to use Shortcode ? =
* You can use below Shortcode in Post or Page.
`[fb_widget fb_url='http://www.facebook.com/Instagram']`
You can use more parameters like below.
`[fb_widget fb_url='http://www.facebook.com/Instagram' width='500' height='450' data_small_header='false' select_lng='ru_RU' data_adapt_container_width='false' data_hide_cover='false' data_show_facepile='false' data_tabs='timeline, messages, events' data_lazy='true']`

= Widget doesn't working in Mozilla Firefox  = 
* If widget works great in all browsers except Mozilla Firefox, You must press the settings off "Use protection against tracking in private window", its security settings for Mozilla, nothing to do with my plugin.

= How do I sign up for a Facebook application ID for my website = 
* You may create a new Facebook application or edit your existing Facebook application through the [Facebook Developers application interface](https://developers.facebook.com/apps/). You need to signup for a Facebook Developer account first.

= I am not sure how to get Facebook application ID ? =
* If you are not able to create Facebook application or you do not know how to do that, do not worry, you can use my default application id `1590918427791514` . I have created it for plugin users and it should work like a charm for you.

= Do I need a Facebook application ID to get this plugin to work? ? =
* Starting from plugin version 6.4, you no longer need a Facebook application ID to make this plugin work.

= It is working in some system, not working in other system ? =
* If it is working in some system & not working with other system, there are 2 possibilities :
	1) Anti virus of your computer can stop my widget loading. Some of my widget users had same problem in past. You can check with disable your anti virus temporarily.
	2) Your browser has some ad block extension installed that may cause stop my plugin loading.  


== Screenshots ==

1. screenshot-1.png - Explains how you can configure plugin in admin widget area.
2. screenshot-2.png - Shows how your plugin will display in frontend sidebar(widget) area.
3. screenshot-3.png - Shows how your plugin will display in Page or Post as Short code.


== Changelog ==

= Version 6.4.2 =
* Fixed suggested XSS Vulnerability for URL field.

= Version 6.4.1 =
* Fixed XSS Vulnerability for URL field.

= Version 6.4 =
* Removed the Custom CSS option from widget settings, as WordPress now includes its own Custom CSS option.

= Version 6.3 =
* Major code updates.
* Removed application id from code and configuration.
* Optimized code.
* Added support up to WordPress version 6.4.2.
* Added support up to PHP version 8.1.12.

= Version 6.2 =
* Removed deprecated data-show-posts parameter.
* Fixed fatal error while adding widget from Widget Area.
* Added data_lazy parameter for lazy loading option.

= Version 6.0 =
* Added code for Review link notice in admin.
* Fixed code readability.
* Code optimization.

= Version 5.1 =
* Fixed Cross-Site Scripting (XSS) Vulnerability with plugin Shortcode.

= Version 5.0 =
* Added support for 3 feed type options (timeline, events, messages).

= Version 4.2.3 =
* Added support for PHP version 7.2
* Removed Deprecated: Function create_function() error for PHP 7.2. 

= Version 4.2.2 =
* Solved Shortcode issue with attributes.
* Code Optimization.

= Version 4.2.1 =
* Updated few default values for Widget.
* Added placeholders in widget settings.
* Code optimization.

= Version 4.2 =
* Facebook has deprecated language URL, so now added static json file to read languages.

= Version 4.1 =
* Now you can add individual widget into Page or Post using shortcode.

= Version 4.0 =
* Changes in code to make plugin compatible for Translation.

= Version 3.1 =
* Removed offset warning for language dropdown.

= Version 3.0 =
* Plugin updated to Facebook Graph API v2.3.
* Removed old options like show/hide border, color scheme.
* Removed notices and warnings.
* Added unclosed div.

= Version 2.3 =
* Added options like Border, Language and custom CSS to shortcode function.

= Version 2.2 =

* Added support for localization (Multilanguage support)(Added .pot file)
* Added option to add custom css for widget
* Added option to select language to show your Facebook Page Feeds in any language you want.

= Version 2.1 =

* Added option to show or hide border from widget
* Added default values to all needed fields while you setup widget, it will help you.

== Upgrade Notice ==

* With the release of Graph API v2.3, the Like Box plugin is deprecated. Please use the updated Plugin instead. The Page Plugin allows you to embed a simple feed of content from a Page into your websites.
* If you do not manually upgrade to the Page Plugin, your Like Box plugin implementation will automatically fall back to the Page Plugin by June 23rd 2015.