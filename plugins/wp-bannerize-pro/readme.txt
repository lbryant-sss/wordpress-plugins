=== WP Bannerize Pro ===
Contributors: gfazioli
Donate link: https://www.paypal.com/donate/?hosted_button_id=L77YYA8AVH2UW
Tags: Banner Management, Advertising, Marketing Tools, Ad Placement, Campaign Optimization
Requires at least: 6.2
Tested up to: 6.7
Stable tag: 1.11.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Bannerize simplifies banner creation and management. Track views and clicks to gauge campaign success.

== Description ==

Bannerize is a WordPress plugin that allows you to create and manage advertising banners easily and quickly. The banners can be created in different formats and placed in various areas of the site. Bannerize allows you to track views and clicks on the banners, so you can monitor the effectiveness of advertising campaigns.

**FEATURES**

* Manage your Banners as Custom Post Types for image, HTML/Javascript and free text
* Sort your Banners with easy Drag & Drop
* Set the filters such as random order, numbers, user roles and campaigns filters
* Date Time schedule
* ✨ Max Impressions
* ✨ Max Clicks
* Display your Banners by PHP code, WordPress shortcode or Widget (🚧 Block is coming soon)
* Manage WordPress Users roles for Banners and Campaigns
* Create your Banners Campaigns
* Clicks and Impressions Counter engine for stats
* CTR (Click-through rate)
* Geolocalization support (by IPStack)
* Analytics reports
* Auto clean up old stats


**DOCS**

* [Documentation](https://bannerize.vercel.app/docs)

== Installation ==

This section describes [how to install](https://bannerize.vercel.app/docs/GettingStarted/installation) the plugin and get it working.

1. Upload the entire content of plugin archive to your `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress (deactivate and reactivate if you're upgrading).
3. Done. Enjoy.

== Frequently Asked Questions ==

= Can I customize the HTML output? =

[Customize Output](https://bannerize.vercel.app/docs/DisplayBanners/customize-output)


== Screenshots ==

1. Add new banner by local media library
2. Add new banner text
3. Real time preview
4. Date range rules
5. Enable impressions and click for single banner
6. Banner categories
7. Statistics overview
8. Single report with filters
9. Settings
10. Widget

Your screenshot

== Changelog ==

= 1.11.0 =

Security & Enhancement Updates

🔒 Security
* SSRF Protection: Added Server-Side Request Forgery (SSRF) protection for external banner image URLs
* Added wp_bannerize_is_remote_image() method to validate remote image URLs
* Only allows JPEG, PNG, and GIF image formats from external sources
* Returns HTTP 200 status validation for remote images
* Prevents malicious URL exploitation through banner uploads
* Added admin error notice when invalid image URLs are submitted

 🎨 Code Quality
* Code Formatting: Standardized code indentation and formatting in WPBannerizeServiceProvider.php
* Improved readability and consistency across the codebase
* Fixed indentation issues throughout the service provider class

🚨 User Experience
* Error Handling: Added user-friendly error messages
* Display admin notice when invalid banner image URLs are entered
* Clear feedback for users when external image URLs fail validation

= 1.10.0 =

* Added max impressions and max clicks for single banner
* Minor improvements

= 1.9.1 =

* Improved data security on the banner
* Now you can view a banner as a Post
* Now you can view Campaigns as categories
* Added views for the top 5 most viewed campaigns and the top 5 most clicked campaigns

= 1.9.0 =

* Added the ability to select one or more Campaigns in the Report view
* Added the ability to select one or more Banners in the Report view
* Added the Bannerize Users Role: Banners Manager, Campaigns Manager, and Campaigns viewer
* Create the Bannerize Website with the documentation
* Minor bug fixes
* [New Website](https://bannerize.vercel.app/)
* [Documentation](https://bannerize.vercel.app/docs)

= 1.8.0 =

* Redesigned the Settings page
* Redesigned the Analytics page
* Introducing Campaigns in place of Categories
* Added the ability to delete the clicks and impressions when they exceed a certain number
* Minor bug fixes

= 1.7.0 =

* Fixed security issues
