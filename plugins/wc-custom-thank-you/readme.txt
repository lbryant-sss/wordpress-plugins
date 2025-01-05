=== WC Custom Thank You ===
Contributors: nicolamustone
Tags: ecommerce, e-commerce, commerce, woothemes, wordpress ecommerce, store, sales, sell, shop, shopping, cart, checkout, configurable
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=mustone.nicola@gmail.com&item_name=Donation+for+WC+Custom+Thank+Tou
Requires at least: 4.1
Tested up to: 4.9.7
Stable tag: 1.2.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

This free WooCommerce extension allows you to define a specific and custom Thank You page for your customers.

== Description ==

This free WooCommerce extension allows you to define a specific and custom Thank You page for your customers.

After a purchase, your customers will be redirected to the custom Thank You page instead of the default WooCommerce's Thank You page.
This allows you to create your own Thank you message, specific to your store only and to your target.

= Configuration =

Once active, go to **WooCommerce > Settings > Advanced** and at the bottom find the Thank You page configuration dropdown. Choose the page you want to use as custom Thank you page.

= Support =

I provide support for this plugin as much as I can. If you have any question or need any help, open a new topic in the [forum](https://wordpress.org/support/plugin/wc-custom-thank-you/).
Make sure to tag `@nicolamustone` if you need urgent help, since I don't check the forum frequently.

= Get involved =

If you want to help, consider to [translate the plugin in your language](https://translate.wordpress.org/projects/wp-plugins/wc-custom-thank-you)!

== Installation ==

= Minimum Requirements =

* WordPress 4.2 or greater
* PHP version 5.2.4 or greater
* MySQL version 5.0 or greater
* WooCommerce 2.7.0 or greater

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t need to leave your web browser. To do an automatic install of WooCommerce Custom Thank You, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type "WC Custom Thank You" and click Search Plugins. Once you’ve found it you can view details about it such as the the point release, rating and description. Most importantly of course, you can install it by simply clicking “Install Now”.

= Manual installation =

The manual installation method involves downloading this plugin and uploading it to your webserver via your favourite FTP application. The WordPress codex contains [instructions on how to do this here](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

== Frequently Asked Questions ==

= I receive a PHP error on my custom "Thank You" page, why? =

Probably you are not running WooCommerce 2.7. Please update.

= Can I customize the templates? =

This plugin uses the default WooCommerce templates. You can customize them, learn how here: https://docs.woocommerce.com/document/template-structure/

= I have a custom language file, where do I save it? =

This plugin loads languages files from:

* `wp-content/languages/woocommerce-custom-thankyou/woocommerce-custom-thankyou-{YOURLOCALE}.mo`
* `wp-content/languages/plugins/woocommerce-custom-thankyou-{YOURLOCALE}.mo`
* `wp-content/plugins/woocommerce-custom-thankyou/languages/`

Put your custom language files in one of these locations, **the first one is recommended**. If you save the files in the last location you will lose them when updating the plugin.

== Changelog ==

= 1.2.1 - 2018-07-19 =
* Tested with WooCommerce 3.4.3
* Tested with WordPress 4.9.7
* Moved the page option in WooCommerce > Settings > Advanced

= 1.2.0 - 2017-04-05 =
* Compatibility test for WordPress 4.7.3
* Compatibility with WooCommerce 3.0
* Compatibility with WPML
* Dropped support for WooCommerce < 3.0 - Update WooCommerce to the most recent version BEFORE to update this plugin
* Templates removed, the default WooCommerce templates are now loaded instead. If you were using customized templates, you will need to adapt them for the WooCommerce default Templates
* `is_order_received_page` now returns `true` on the Custom Thank You page - This should fix compatibility with analytics plugins which use that function to add their code on the thank you page
* The Custom Thank You page content is now hidden if the order is not valid

= 1.1.0 - 2017-02-01 =
* Compatibility test for WordPress 4.7.2
* Compatibility test for WooCommerce 2.6.13
* Fixed coding standard so the code is now conform to the WordPress coding standards
* Fixed a notice for the missing variable for purchase notes on the custom Thank you page

= 1.0.0 - 2015-08-11 =
* First release!
