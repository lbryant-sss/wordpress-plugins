=== XML Sitemap Generator for Google ===
Contributors: auctollo
Tags: SEO, xml sitemap, video sitemap, news sitemap, html sitemap
Requires at least: 4.7
Tested up to: 6.8
Stable tag: 4.1.21
Requires PHP: 5.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Generate multiple types of sitemaps to improve SEO and get your website indexed quickly.

== Description ==

Generate XML and HTML sitemaps for your website with ease using the XML Sitemap Generator for Google. This plugin enables you to improve your SEO rankings by creating page, image, news, video, HTML, and RSS sitemaps. It also supports custom post types and taxonomies, allowing you to ensure that all of your content is being indexed by search engines. With a user-friendly interface, you can easily configure the plugin to suit your needs and generate sitemaps in just a few clicks. Keep your website up-to-date and make sure that search engines are aware of all of your content by using the XML Sitemap Generator for Google.

The plugin supports all kinds of WordPress generated pages as well as custom URLs. Additionally it notifies all major search engines every time you create a post about the new content.

Supported for more than a decade and [rated among the best](https://wordpress.org/plugins/browse/popular/page/2/#:~:text=XML%20Sitemap%20Generator%20for%20Google), it will do exactly what it's supposed to do - providing a complete XML sitemap for search engines!

> If you like the plugin, feel free to rate it! :)

Related Links:

* <a href="http://wordpress.org/support/topic/read-before-opening-a-new-support-topic">Support Forum</a>

== Installation ==

1. Deactivate or disable other sitemap generators.
2. Install the plugin like you always install plugins, either by uploading it via FTP or by using the "Add Plugin" function of WordPress.
3. Activate the plugin on the plugin administration page
4. If you want: Open the plugin configuration page, which is located under Settings -> XML-Sitemap and customize settings like priorities and change frequencies.
5. The plugin will automatically update your sitemap if you publish new content, so there is nothing more to do :)

== Frequently Asked Questions ==

= How do I generate a sitemap for my website? =

To generate a sitemap for your website, follow these steps:

* Install and activate the XML Sitemap Generator for Google plugin on your WordPress site.
* Once activated, the plugin will automatically generate a sitemap.xml file for your website.
* You can access the sitemap by appending `/sitemap.xml` to your website's URL (e.g., [https://example.com/sitemap.xml](#)).
* Submit your sitemap to search engines like Google, Bing, Yandex, and Baidu to ensure that your site is indexed and crawled properly.

= How do I submit my sitemap to search engines? =

* Go to the Google Search Console and sign in with your Google account.
* Click on the "Sitemaps" tab and enter the URL of your sitemap.xml file (e.g., [https://example.com/sitemap.xml](#)).
* Click on "Submit" to submit your sitemap to Google.
* Repeat the process for other search engines like Bing, Yandex, and Baidu.

= How often should I update my sitemap? =

It's recommended that you update your sitemap whenever you make significant changes to your website's content or structure. This will ensure that search engines are aware of any new pages or changes to existing pages on your site.

= What types of sitemaps does the plugin generate? =

The XML Sitemap Generator for Google plugin can generate sitemaps in XML, HTML, RSS formats and in various types, including: Pages/Posts, Google News, Video, Image, Mobile, and more! Note: Some formats and types are only available to subscribers.

= Can I include images and videos in my sitemap? =

Yes, you can include images and videos in your sitemap using the Google XML Sitemap Generator plugin. This will help search engines like Google and Bing to crawl and index your media files more efficiently.

= How does the plugin work with WooCommerce? =

The XML Sitemap Generator for Google plugin is compatible with WooCommerce and can generate sitemaps for your online store's product pages, categories, and tags. This will help search engines to index your products and improve your store's visibility in search results.

= Can I customize the robots.txt file using this plugin? =

Yes, you can customize the robots.txt file using the Google XML Sitemap Generator for Google plugin. This will allow you to control which pages and directories search engines can crawl and index on your site.

= Does this plugin support the Google Site Kit? =

Yes, the XML Sitemap Generator for Google plugin is compatible with the Google Site Kit. This will allow you to track your site's performance in Google Search Console and Google Analytics directly from your WordPress dashboard.

= Does this plugin support schema markup? =

Yes, the XML Sitemap Generator for Google plugin supports schema markup, which can help improve your site's visibility in search results by providing more information about your content to search engines.

= Where can I find the options page of the plugin? =

It is under Settings > XML-Sitemap. I know nowadays many plugins add top-level menu items, but in most of the cases it is just not necessary. I've seen WP installations which looked like an Internet Explorer ten years ago with 20 toolbars installed. ;-)

= Does this plugin use static files or "I can't find the sitemap.xml file!" =

Not anymore. Since version 4, these files are dynamically generated just like any other WordPress content.

= There are no comments yet (or I've disabled them) and all my postings have a priority of zero! =

Please disable automatic priority calculation and define a static priority for posts.

= So many configuration options... Do I need to change them? =

No, only if you want to. Default values are ok for most sites.

= My question isn't answered here =

Please post your question at the [WordPress support forum](https://wordpress.org/support/plugin/google-sitemap-generator/).


== Changelog ==

= 4.1.21 (2024-04-21) =
* Fixed a regression with saving post/page exclusions.
* Fixed an issue with post/tag priority settings.

= 4.1.20 (2024-04-14) =
* Fixed empty ping error
* Fixed response code issue with index sitemap
* Fixed interoperability with existing robots.txt file
* Fixed URL for guest authors
* Fixed uninstalled plugin error
* Fixed sitemap indexation
* Fixed "Trying to access array offset on value of type bool..." warning
* Fixed setting of custom taxonomy change frequency and priority
* Fixed missing custom taxonomies issue
* Fixed issue with hierarchical taxonomies
* Fixed missing categories issue
* Fixed sort order of URLs (changed from descending to ascending)
* Improved WooCommerce support (added products sitemap)
* Improved multisite support
* Improved SEO plugin interoperability
* Improved Nginx configuration
* Improved sitemap generation performance for large sites
* Added [filter support](https://auctollo.com/products/google-xml-sitemap-generator/help/#:~:text=not%20be%20useful.-,Filters,-Customize%20the%20behavior) for behavior/settings customization 
* Added latest Microsoft Bing API Key to settings page

= 4.1.19 (2024-01-31) =
* Fixed "Links per page" bug causing sitemaps to not include all posts depending on the setting. Following Search Engine guidance is minimum links per sitemap is 1,000 pages.
* Fixed Google Search Console errors including "Fetch error" and "noindex" header
* Fixed the issue with "null" sitemaps
* Fixed sitemap generation for tags, categories, etc for large sites
* Improved performance by optimizing queries
* Improved IndexNow implementation
* Added WordPress' sitemap to the list of detected sitemaps for deactivation

= 4.1.18 (2024-01-12) =
* Resolved functionality regressions since v4.1.13
* Improved IndexNow Protocol implementation
* Improved WooCommerce support
* Improved WPML support
* Fixed sitemap 404 issues (for Single and Multisite modes)
* Fixed auto update rewrite rule
* Fixed rewrite issues during plugin upgrade
* Fixed invalid XML syntax (cause of parsing issues in Google Search Console)

= 4.1.17 (2024-01-05) =
* Fixed sitemap URL issue in robots.txt etc
* Improved LastMod syntax for better support for indexation by Google
* Improved Network mode support
* Improved localization plugin support
* Improved custom taxonomy support
* Added IndexNow Protocol support for Microsoft Bing. Deprecated Sitemap Ping Protocol
* Added JetPack sitemap generator conflict detection

= 4.1.16 (2023-12-18) =
* Fixed a syntax error causing fatal errors for some users.

= 4.1.15 (2023-12-14) =
* Improved security by adding capability check via the current_user_can() calls
* Changed the text domain from "sitemap" to "google-sitemap-generator"

= 4.1.14 (2023-12-05) =
* Improved security with authentication and plugin management anchors
* Fixed an error in sitemap file name conventions
* Fixed an issue with disabling conflicting sitemap generators
* Added last modified date to category sitemaps
* Added min and max option for number of posts per sitemap
* Added support for local links in sitemap for different languages

= 4.1.13 (2023-08-04) =
* Fixed warning error displayed when Yoast SEO is not installed/active

= 4.1.12 (2023-08-02) =
* Improved various UI elements and notifications
* Fixed browser console errors
* Fixed null value in set_time_limit

= 4.1.11 (2023-05-19) =
* Fixed version compatibility thresholds
* Improved user interface

= 4.1.10 (2023-04-24) =
* Added support for automatic updates

= 4.1.9 (2023-04-12) =
* Improved beta program notifications and workflow to comply with guidelines
* Improved various UI elements

= 4.1.8 (2023-03-31) =
* Added Beta testing program
* Added check for disabled PHP functions
* Fixed handling of taxonomies containing hyphens

= 4.1.7 (2022-11-24) =
* Fixed custom taxonomy unit generation issue
* Fixed plugin deactivation notice

= 4.1.6 (2022-11-23) =
* Fixed mishandling of empty categories
* Fixed _url undefined notice error
* Fixed error when build_taxonomies throws a fatal error when accessing sub-sitemap without pagination
* Improved handling of line breaks e.g. showing <br/> tag without escaping HTML
* Improved handling of the Google TID field optional to ping Google
* Improved documentation, given some renaming of the methods
* Added support for paginated sitemap posts, pages, and product links
* Added conditional statements to prevent rewrite rules from being checked every time the sitemap loads

= 4.1.5 (2022-06-14) =
* Fixed code regressions moving from git to svn (preventing recent fixes from being available)

= 4.1.4 (2022-06-06) =
* Fixed the issue of PHP warnings
* Fixed links per page issue
* Improved WordPress 6.0 compatibility

= 4.1.3 (2022-05-31) =
* Added backward compatibility settings
* Changed Google Tracking ID field to optional
* Fixed PHP warnings

= 4.1.2 (2022-04-15) =
* Fixed security issue related to Cross-Site Scripting attacks on debug page
* Fixed HTTP error while generating sitemap (because of conflict of www and now www site)
* Fixed handling WordPress core sitemap entry from robots.txt
* Added option to flush database rewrite on plugin deactivation
* Added option to split the custom categories into multiple sitemaps by custom taxonomy
* Added option to omit the posts specified as disallow in robots.txt
* Added option to set links per page for tags and categories
* Added option to set a custom filename for the sitemap
* Added option to list custom post in the archive sitemap

= 4.1.1 (2022-04-07) =
* fix security issue related to Cross-Site Scripting attacks on debug page
* fix  HTTP error while generating sitemap (because of conflict of www and now www site)
* fix handles the removal of Wordpress native sitemap entry from robots.txt
* added option for flush database rewrite on deactivate plugin 
* added options for split the custom categories into multiple sitemap by custom taxonomy
* added options to omit the posts which added in robots.txt to disallow
* added option to set links per page for tags and categories
* added option for provide the custom name for the sitemap.xml file
* added option for custom post type's list into the archive sitemap
* added support of manage priorities and frequencies for products category

= 4.1.0 (2018-12-18) =
* Fixed security issue related to escaping external URLs
* Fixed security issue related to option tags in forms

= 4.0.9 (2017-07-24) =
* Fixed security issue related to donation functionality.

= 4.0.8 (2014-11-15) =
* Fixed bug regarding the exclude categories feature, thanks to Claus Schöffel!

= 4.0.7.1 (2014-09-02) =
* Sorry, no new features this time… This release only updates the Compatibility-Tag to WordPress 4.0. Unfortunately there is no way to do this anymore without a new version

= 4.0.7 (2014-06-23) =
* Better compatibility with GoDaddy managed WP hosting
* Better compatibility with QuickCache
* Removed WordPress version from the sitemap
* Corrected link to WordPress privacy settings (if search engines are blocked)
* Changed hook which is being used for sitemap pings to avoid pings on draft edit

= 4.0.6 (2014-06-03) =
* Added option to disable automatic gzipping
* Fixed bug with duplicated external sitemap entries
* Don’t gzip if behind Varnish since Varnish can do that

= 4.0.5 (2014-05-18) =
* Added function to manually start ping for main-sitemap or all sub-sitemaps
* Added support for changing the base of the sitemap URL to another URL (for WP installations in sub-folders)
* Fixed issue with empty post sitemaps (related to GMT/local time offset)
* Fixed some timing issues in archives
* Improved check for possible problems before gzipping
* Fixed empty archives and author sitemaps in case there were no posts
* Fixed bug which caused the Priority Provider to disappear in recent PHP versions
* Plugin will also ping with the corresponding sub-sitemap in case a post was modified
* Better checking for empty external urls
* Changed text in XSL template to be more clear about sitemap-index and sub-sitemaps
* Changed content type to text/xml to improve compatibility with caching plugins
* Changed query parameters to is_feed=true to improve compatibility with caching plugins
* Switched from using WP_Query to load posts to a custom SQL statement to avoid problems with other plugin filters
* Added caching of some SQL statements
* Added support feed for more help topics
* Added link to new help page
* Cleaned up code and renamed variables to be more readable
* Updated Japanese Translation, thanks to Daisuke Takahashi

= 4.0.4 (2014-04-19) =
* Removed deprecated get_page call
* Changed last modification time of sub-sitemaps to the last modification date of the posts instead of the publish date
* Removed information window if the statistic option has not been activated
* Added link regarding new sitemap format
* Updated Portuguese translation, thanks to Pedro Martinho
* Updated German translation

= 4.0.3 (2014-04-13) =
* Fixed compression if an gzlib handler was already active
* Help regarding permalinks for Nginx users
* Fix with gzip compression in case there was other output before already
* Return 404 for HTML sitemaps if the option has been disabled
* Updated translations

= 4.0.2 (2014-04-01) =
* Fixed warning if an gzip handler is already active

= 4.0.1 (2014-03-31) =
* Fixed bug with custom post types including a "-"
* Fixed some 404 Not Found Errors

= 4.0 (2014-03-30) =
* No static files anymore, sitemap is created on the fly!
* Sitemap is split-up into sub-sitemaps by month, allowing up to 50.000 posts per month! [More information](http://www.arnebrachhold.de/projects/wordpress-plugins/google-xml-sitemaps-generator/google-xml-sitemap-generator-new-sitemap-format/)
* Support for custom post types and custom taxonomis!
* 100% Multisite compatible, including by-blog and network activation.
* Reduced server resource usage due to less content per request.
* New API allows other plugins to add their own, separate sitemaps.
* Note: PHP 5.1 and WordPress 3.3 is required! The plugin will not work with lower versions!
* Note: This version will try to rename your old sitemap files to *-old.xml. If that doesn’t work, please delete them manually since no static files are needed anymore!

= 3.4.1 (2014-04-10) =
* Compatibility with mysqli

= Version 3.4 (2013-11-24) =
* Fixed deprecation warnings in PHP 5.4, thanks to Dion Hulse!

= 3.3 (2013-09-28) =
* Fixed problem with file permission checking
* Filter out hashs (#) in URLs

= 3.2.9 (2013-01-11) =
* Fixed security issue with change frequencies and filename of sitemap file. Exploit was only possible with admin account.

= 3.2.8 (2012-08-08) =
* Fixed wrong custom taxonomy URLs, thanks to ramon fincken of the wordpress.org forum!
* Removed ASK ping since they shut down their service.
* Exclude post_format taxonomy from custom taxonomy list

= 3.2.7 (2012-04-24) =
* Fixed custom post types, thanks to clearsite of the wordpress.org forum!
* Fixed broken admin layout on WP 3.4

= 3.2.6 (2011-09-19) =
* Removed YAHOO ping since YAHOO uses bing now
* Removed deprecated function call

= 3.2.5 (2011-07-11) =
* Backported Bing ping success fix from beta
* Added friendly hint to try out the new beta

= 3.2.4 (2010-05-29) =
* Added (GMT) to date column in sitemap xslt template to avoid confusion with different time zones
* Fixed wrong SQL statement for author pages, thanks to twoenoug
* Fixed several deprecated function calls
* Note: This release does not support the new multisite feature of WordPress yet and will not be active when multisite is enabled.

= 3.2.3 (2010-04-02) =
* Fixed that all pages were missing in the sitemap if the “Uncategorized” category was excluded

= 3.2.2 (2009-12-19) =
* Updated compatibility tag to WordPress 2.9
* Fixed PHP4 problems

= 3.2.1 (2009-12-16) =
* Notes and update messages at the top of the admin page could interfere with the manual build function
* Help links in the WP contextual help were not shown anymore since the last update
* IE 7 sometimes displayed a cached admin page
* Removed invalid link to config page from the plugin description (The link lead to a "Not enough permission error")
* Improved performance of getting the current plugin version by caching
* Updated Spanish language files

= 3.2 (2009-11-23) =
* Added function to show the actual results of a ping instead of only linking to the url
* Added new hook (sm_rebuild) for third party plugins to start building the sitemap
* Fixed bug which showed the wrong URL for the latest Google ping result
* Added some missing documentation
* Removed hardcoded php name for sitemap file in admin urls
* Uses KSES for showing ping test results
* Ping test fixed for WP < 2.3

= 3.1.9 (2009-11-13) =
* Fixed MySQL Error if author pages were included

= 3.1.8 (2009-11-07) =
* Improved custom taxonomy handling and fixed wrong last modification date
* Fixed fatal error in WordPress versions lower than 2.3
* Fixed Update Notice for WordPress 2.8 and higher
* Added warning if blog privacy is activated
* Fixed priorities of additional pages were shown as 0 instead of 1

= 3.1.7 (2009-10-21) =
* Added support for custom taxonomies. Thanks to Lee!

= 3.1.6 (2009-08-31) =
* Fixed PHP error “Only variables can be passed by reference”
* Fixed wrong URLS of multi-page posts (Thanks artstorm!)
* Updated many language files

= 3.1.5 (2009-08-24) =
* Added option to completely disable the last modification time
* Fixed problem with HTTPS url for the XSL stylesheet if the sitemap was build via the admin panel
* Improved handling of homepage entry if a single page was set for it
* Fixed mktime warning which appeared sometimes
* Fixed bug which caused inf. reloads after rebuilding the sitemap via the admin panel
* Improved handling of missing sitemaps files if WP was moved to another location

= 3.1.4 (2009-06-22) =
* Fixed bug which broke all pings in WP older than 2.7
* Added more output in debug mode if pings fail
* Moved global post variable so other plugins can use it in get_permalink()
* Added small icon for ozh admin menu
* Added more help links in UI

= 3.1.3 (2009-06-07) =
* Changed MSN Live Search to Bing
* Exclude categories also now exludes the category itself and not only the posts
* Pings now use the new WordPress HTTP API instead of Snoopy
* Fixed bug that in localized WP installations priorities could not be saved
* The sitemap cron job is now cleared after a manual rebuild or after changing the config
* Adjusted style of admin area for WP 2.8 and refreshed icons
* Disabled the “Exclude categories” feature for WP 2.5.1, since it doesn’t have the required functions yet

= 3.1.2 (2008-12-26) =
* Changed the way the stylesheet is saved (default / custom stylesheet)
* Sitemap is now rebuild when a page is published
* Removed support for static robots.txt files, this is now handled via WordPress functions
* Added compat. exceptions for WP 2.0 and WP 2.1

= 3.1.1 (2008-12-21) =
* Fixed redirect issue if wp-admin is rewritten via mod_rewrite, thanks to macjoost
* Fixed wrong path to assets, thanks PozHonks
* Fixed wrong plugin URL if wp-content was renamed / redirected, thanks to wnorris
* Updated WP User Interface for 2.7
* Various other small things

= 3.1.0.1 (2008-05-27) =
* Extracted UI JS to external file
* Enabled the option to include following pages of multi-page posts
* Script tries to raise memory and time limit if active

= 3.1 (2008-05-22) =
* Marked as stable

= 3.1b3 (2008-05-19) =
* Cleaned up plugin directory and moved img files to subfolders
* Fixed background building bug in WP 2.1
* Removed auto-update plugin link for WP < 2.5

= 3.1b2 (2008-05-18) =
* Fixed critical bug with the build in background option
* Added notification if a build is scheduled

= 3.1b1 (2008-05-08) =
* Splitted plugin in loader, generator and user interface to save memory
* Generator and UI will only be loaded when needed
* Secured all admin actions with nonces
* Improved WP 2.5 handling
* New "Suggest a Feature" link

= 3.0.3.3 (2008-04-29) =
* Fixed author pages
* Enhanced background building and increased delay to 15 seconds
* Enabled background building by default

= 3.0.3.2 (2008-04-28) =
* Improved WP 2.5 handling (fixes blank screens and timeouts)

= 3.0.3.1 (2008-03-30) =
* Added compatibility CSS for WP 2.5

= 3.0.3 (2007-12-30) =
* Added option to ping MSN Live Search
* Removed some WordPress hooks (the sitemap isn’t updates with every comment anymore)

= 3.0.2.1 (2007-11-28) =
* Fixed wrong XML Schema Location (Thanks to Emanuele Tessore)
* Added Russian Language files by Sergey http://ryvkin.ru

= 3.0.2 (2007-11-25) =
* Fixed bug which caused that some settings were not saved correctly
* Added option to exclude pages or post by ID
* Restored YAHOO ping service with API key since the other one is to unreliable

= 3.0.1 (2007-11-03) =
* Changed HTTP client for ping requests to Snoopy
* Added "safemode" for SQL which doesn’t use unbuffered results
* Added option to run the building process in background using wp-cron
* Added links to test the ping if it failed

= 3.0 final (2007-09-24) =
* Marked as stable
* Removed useless functions

= 3.0b11 (2007-09-23) =
* Changed mysql queries to unbuffered queries
* Uses MUCH less memory
* Option to limit the number of posts

= 3.0b10 (2007-09-04) =
* Added category support for WordPress 2.3
* Fixed bug with empty URLs in sitemap
* Repaired GET building

= 3.0b9 (2007-09-02) =
* Added tag support for WordPress 2.3
* Fixed archive bug with static pages (Thanks to Peter Claus Lamprecht)
* Fixed some missing translation strings, thanks to Kirin Lin

= 3.0b8 (2007-07-22) =
* Fixed bug with empty categories
* Fixed bug with translation plugins
* Added support for robots.txt
* Switched YAHOO ping API from YAHOO Web Services to the “normal” ping service
* Search engines will only be pinged if the sitemap file has changed

= 3.0b7 (2007-05-17) =
* Added Ask.com notification
* Added option to include the author pages like /author/john
* Fixed WP 2.1 / Pre 2.1 post / pages database changes
* Added check to not build the sitemap if importing posts
* Fixed wrong XSLT location (Thanks froosh)
* Small enhancements and bug fixes

= 3.0b6 (2007-01-23) =
* sitemap.xml.gz was not compressed
* YAHOO update-notification was PHP5 only (Thanks to Joseph Abboud!)
* More WP 2.1 optimizations
* Reduced memory usage with PHP5

= 3.0b5 (2007-01-19) =
* WordPress 2 Design
* YAHOO update notification
* New status report, removed ugly logfiles
* Added option to define a XSLT stylesheet and added a default one
* Fixed bug with sub-pages, thanks to [Mike](http://baptiste.us/), [Peter](http://fastagent.de/) and [Glenn](http://publicityship.com.au/)
* Improved file handling, thanks to [VJTD3](http://www.vjtd3.com/)
* WP 2.1 improvements

= 3.0b4 (2006-11-16) =
* Fixed some smaller bugs
* Decreased memory usage which should solve timeout and memory problems
* Updated namespace to support YAHOO and MSN

= 3.0b2 (2006-01-14) =
* Fixed several bugs reported by users

= 3.0b (2005-11-25) =
* WordPress 2.0 (Beta, RC1) compatible
* Added different priority calculation modes and introduced an API to create custom ones (Some people didn’t like the way to calculate the post priority based on the count of user comments. This will give you the possibility to develop custom priority providers which fit your needs.)
* Added support to use the [Popularity Contest](http://www.alexking.org/blog/2005/07/27/popularity-contest-11/) plugin by [Alex King](http://www.alexking.org/) to calculate post priority (If you are already using the Popularity Contest plugin, this will be the best way to determine the priority of the posts. Uses to new priority API noted above.)
* Added option to exclude password protected posts (This was one of the most requested features.)
* Posts and pages marked for publish with a date in the future won’t be included
* Added function to start sitemap creation via GET and a secret key (If you are using external software which directly writes into the database without using the WordPress API, you can rebuild the sitemap with a simple HTTP Request. This can be made with a cron job for example.)
* Improved compatibility with other plugins (There should no longer be problems with other plugins now which checked for existence of a specified function to determine if you are in the control panel or not.)
* Recoded plugin architecture which is now fully OOP (The code is now cleaner and better to understand which makes it easier to modify. This should also avoid namespace problems.)
* Improved speed and optimized settings handling (Settings and pages are only loaded if the sitemap generation process starts and not every time a page loads. This saves one MySQL Query on every request.)
* Added Button to restore default configuration (Messed up the config? You’ll need just one click to restore all settings.)
* Added log file to check everything is running (In the new log window you can see when your sitemap was rebuilt or if there was any error.)
* Improved user-interface
* Added several links to homepage and support (This includes the Notify List about new releases and the WordPress support forum.)

= 2.7 (2005-11-25) =
* Added Polish Translation by [kuba](http://kubazwolinski.com/)

= 2.7 (2005-11-01) =
* Added French Translation by [Thierry Lanfranchi](http://www.chezthierry.info/)

= 2.7 (2005-07-21) =
* Fixed bug with incorrect date in additional pages (wrong format)
* Added Swedish Translation by [Tobias Bergius](http://tobiasbergius.se/)

= 2.6 (2005-07-16) =
* Included Chinese (Simplified) language files by [june6](http://www.june6.cn/)

= 2.6 (2005-07-04) =
* Added support to store the files at a custom location
* Changed the home URL to have a slash at the end
* Fixed errors with wp-mail
* Added support for other plugins to add content to the sitemap

= 2.5 (2005-06-15) =
* You can include now external pages which aren’t generated by WordPress or are not recognized by this plugin
* You can define a minimum post priority, which will overrride the calculated value if it’s too low
* The plugin will automatically ping Google whenever the sitemap gets regenerated
* Update 1: Included Spanish translations by [Cesar Gomez Martin](http://www.cesargomez.org/)
* Update 2: Included Italian translations by [Stefano Aglietti](http://wordpress-it.it/)
* Update 3: Included Traditional Chinese translations by [Kirin Lin](http://kirin-lin.idv.tw/)

= 2.2 (2005-06-08) =
* Language file support: [Hiromasa](http://hiromasa.zone.ne.jp/) from [http://hiromasa.zone.ne.jp](http://hiromasa.zone.ne.jp/) sent me a japanese version of the user interface and modified the script to support it! Thanks for this! Check [the WordPress Codex](http://codex.wordpress.org/WordPress_Localization) how to set the language in WordPress.
* Added Japanese user interface by [Hiromasa](http://hiromasa.zone.ne.jp/)
* Added German user interface by me

= 2.12 (2005-06-07) =
* Changed SQL Statement for categories that it also works on MySQL 3

= 2.11 (2005-06-07) =
* Fixed a hardcoded tablename which made a SQL error

= 2.1 (2005-06-07) =
* Can also generate a gzipped version of the xml file (sitemap.xml.gz)
* Uses correct last modification dates for categories and archives. (Thanks to thx [Rodney Shupe](http://www.shupe.ca/) for the SQL)
* Supports now different WordPress / Blog directories
* Fixed bug which ignored different post/page priorities (Reported by [Brad](http://h3h.net/))

= 2.01 (2005-06-07) =
* Fixed compatibility for PHP installations which are not configured to use short open tags
* Changed Line 147 from _e($i); to _e(strval($i));
* Thanks to [Christian Aust](http://publicvoidblog.de/) for reporting this!

== Screenshots ==

1. Plugin options page
2. Sample XML sitemap (with a stylesheet for making it readable)
3. Sample XML sitemap (without stylesheet)

== License ==

Good news, this plugin is free for everyone! Since it's released under the GPL, you can use it free of charge on your personal or commercial site.

== Translations ==

The plugin comes with various translations, please refer to the [WordPress Codex](http://codex.wordpress.org/Installing_WordPress_in_Your_Language "Installing WordPress in Your Language") for more information about activating the translation. If you want to help to translate the plugin to your language, please have a look at the sitemap.pot file which contains all definitions and may be used with a [gettext](http://www.gnu.org/software/gettext/) editor like [Poedit](http://www.poedit.net/) (Windows).

== Upgrade Notice ==

= 4.1.18 =
Thank you for using XML Sitemap Generator! This release resolves critical issues reported in the support forum. Thank you to all who reported issues! Make sure to "Enable auto-updates!" 