=== Kadence WooCommerce Email Designer ===
Contributors: britner
Tags: woocommerce, mail, email, email template, email templates
Donate link: https://www.kadencewp.com/about-us/
Requires PHP: 5.2.4
Requires at least: 5.3
Tested up to: 6.8
Stable tag: 1.5.17
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Customize the default WooCommerce email templates design and text through the native WordPress customizer. Preview emails and send test emails.

== Description ==

This plugin lets you easily customize the default transactional WooCommerce email templates. Edit the design using the native WordPress customizer for instant visual edits. Customize the text (including body text) or each email template in WooCommerce without editing code.

https://www.youtube.com/watch?v=e3rouozVOCA

= Features Include =

* Live preview your WooCommerce emails.
* Import pre-built email designs to get started.
* Customize emails to match your brand style.
* Customize heading, subtitle, and body Text
* Send test emails for real email inbox testing.
* Export and import your settings with ease.

= Don't send bland emails =

Make an impression with your customers and represent your brand well by customizing your WooCommerce emails design and content with Kadence WooCommerce Email Designer.

= Full Video Walkthrough =
[Watch video walkthrough here](https://www.youtube.com/watch?v=eQmI3aSY4uI).

= Support =

We are happy to help as best we can with questions! Please use the support forums.

= Credits =

Thanks to RightPress, we heavily pulled code and structure for this plugin from [Decorator – WooCommerce Email Customizer by RightPress](https://wordpress.org/plugins/decorator-woocommerce-email-customizer/).
Thanks to The Beaver Builder Team, we adapted our import/export control functionality from [Customizer Export/Import](https://wordpress.org/plugins/customizer-export-import/)
Thanks to cristian-ungureanu, we adapted our footer social media control functionality from [Customizer Repeater](https://github.com/cristian-ungureanu/customizer-repeater/)
Thanks to soderlind, we adapted our range control functionality from [WordPress Customizer Range Value Control](https://github.com/soderlind/class-customizer-range-value-control/)
Thanks to soderlind, we adapted our toggle control functionality from [WordPress Customizer Toggle Control](https://github.com/soderlind/class-customizer-toggle-control/)

== Installation ==

Install the plugin into the `/wp-content/plugins/` folder, and activate it.

== Frequently Asked Questions ==

= What emails can I customize? =

The Kadence WooCommerce Email Designer plugin allows you to customize the style and text of all core WooCommerce emails. This plugin hooks into the main woocommerce email function to style emails and setup templates to allow you to customize the design and text. Some WooCommerce extensions add new emails to woocommerce core. Generally these emails will adapt the style (header, footer, colors, etc) you set up with the plugin but they will not be available to preview and there text will not be editable through our plugin.

We have added support for some WooCommerce extensions like WooCommerce Subscriptions, Germanized for WooCommerce, Woocommerce Memberships and WooCommerce Stripe Payment Gateway.

If you are a plugin author and wish to collaborate to add support for your plugin please [contact us](https://www.kadencewp.com/contact-us/).


= My body text is not changing, what can I do? =

In your admin navigate to WooCommerce > Status and scroll down to template overrides. If you are override email templates then that is your issue and you need to remove those overrides if you want the Kadence WooCommerce Email Designer plugin to control the templates.

= My emails are not translated? =

WooCommerce core has a large group of translators that generously translate the plugin into many languages. The Kadence WooCommerce Email Designer plugin does not have translations for many of these languages so you will need to translate some strings yourself. The easiest way to do this is through the [Loco Translate](https://wordpress.org/plugins/loco-translate/) plugin. You can install this plugin and translate parts of the emails like Price, Product, Quantity, etc into your language.

After you create your translations make sure to clear your server cache from any caching plugins. Also go to woocommerce > status > tools and clear your template cache. 

= Where do I report security bugs found in this plugin? =

Please report security bugs found in the source code of the
Kadence WooCommerce Email Designer plugin through the Patchstack
Vulnerability Disclosure Program https://patchstack.com/database/vdp/kadence-woocommerce-email-designer. The
Patchstack team will assist you with verification, CVE assignment, and
notify the developers of this plugin.

== Screenshots ==

1. Overview of available options

2. Customize colors, fonts, backgrounds, etc

3. Change/edit text copy to match your brand

== Security Policy ==

= Reporting Security Bugs =

Please report security bugs found in the
Kadence WooCommerce Email Designer plugin's source code through the
Patchstack Vulnerability Disclosure
Program https://patchstack.com/database/vdp/kadence-woocommerce-email-designer. The Patchstack team will
assist you with verification, CVE assignment, and notify the
developers of this plugin.

== Changelog ==

= 1.5.17 =
* Fix: Stricter validation when importing settings.

= 1.5.16 =
* Fix: PHP Deprecation notice.

= 1.5.15 =
* Update: WC version support.
* Update: Tweak settings import.

= 1.5.14 =
* Update: adjusting needs payment checks on invoices to a more flexible function.

= 1.5.13 =
* Fix: PHP deprecated notice.

= 1.5.12 =
* Update: WC version support.
* Update: Nonce verification with ajax calls.

= 1.5.11 =
* Fix: Issue with subtitle translation for WPML.
* Fix: Issue with disabling mobile notice.

= 1.5.10 =
* Fix: style issue in customizer since 6.2

= 1.5.9 =
* Update: Tweak getting order meta for HPOS support.

= 1.5.8 =
* Add: Option to Disable additional content.
* Add: Option to disable mobile messaging.
* Update: Declare HPOS support.
* Update: Template file version numbers.
* Fix: Typo in settings.

= 1.5.7 =
* Fix: Possible issue with admin+ PHP objection injection in settings importer.

= 1.5.6 =
* Fix: Prevent email mockup url from direct access.

= 1.5.5 =
* Fix: Possible bug with placeholders and WPML

= 1.5.4 =
* Fix: Possible bug with get_id for WPML filter.

= 1.5.3 =
* Update: Add some filters for body content and subtitle WPML support.

= 1.5.2 =
* Update: Template version numbers to prevent confusion.
* Update: Small tweek for outlook styles.

= 1.5.1 =
* Update: Prevent an error if using old woocommerce.

= 1.5.0 =
* Update: Add in Shipping Phone.
* Update: Pot file.

= 1.4.10 =
* Update: Support WC 6.0 email template changes

= 1.4.9 =
* Fix: home url in email header.
* Fix: html error in email header.

= 1.4.8 =
* Update: Better image support for outlook email client.
* Update: Make the email selection translatable.
* Fix: issue with heading text resetting when you type in certain areas.

= 1.4.7 =
* Fix: Possible issue with billing address format.

= 1.4.6 =
* Fix: Button Text color not changing.
* Fix: Import/Export support for special characters.

= 1.4.5 =
* Update: Make email address a link so it can be styled.
* Update: RTL improvement with light order table style.

= 1.4.4 =
* Update: Show support for WC 4.8 and WP 5.6

= 1.4.3 =
* Update: Tweak address css.
* Update: Show WC 4.3.0 support.
* Add: {site_address} placeholder.
* Add: Image size option for product images.

= 1.4.2 =
* Update: Tweak some email styling for better consistency.
* Update: Prevent an error when previewing invoice emails with Germanized

= 1.4.1 =
* Update: Tweak some email styling for better consistency.
* Add: Alt to footer social image.

= 1.4.0 =
* Update: Link header image to site.
* Update: Show WC 4.0 support.

= 1.3.14 =
* Update: Fix multilingual bug.

= 1.3.13 =
* Update: Fix possible bug in mac mail app.
* Update: Add change_locale action for polylang.

= 1.3.12 =
* Fix: Classes to order totals table so it's possible to style specifically.
* Fix: Additional Information in customer retry payment email.

= 1.3.11 =
* Update: Add Classes to order totals table so it's possible to style specifically.
* Update: Add Classes to email body so you can add styles that only effect certain emails.
* Add: Option to Customer Payment Retry email to make payment link a button.
* Add: Option to Customer Payment Retry email to use {invoice_pay_link} placeholder in content.
* Add: Option to edit Store manager Payment Retry email.
* Update: Add {customer_email} placeholder.
* Update: get_product to wc_get_product - thanks @dustinpitcher

= 1.3.10 =
* Update: Tweak android style.

= 1.3.9 =
* Update: Prevent some third-party issues with filter arguments.

= 1.3.8 =
* Update: Fix bug in plan text email.
* Update: Add $email default to prevent some fatal errors with plugins using non standard email templates.

= 1.3.7 =
* Update: Fix bug in Gmail mobile app causing footer to not fill the space.

= 1.3.6 =
* Update: Translation issue with account email.
* Fix: Header image styling bug.
* Fix: Better placeholder fall back for names in account emails.
* Update: Check for order object for email preview.

= 1.3.5 =
* Update: Translation issue with subscriptions email.
* Add: Preview Support for Stripe emails
* Fix: Tweak how languages are set in emails for better support.

= 1.3.4 =
* Update: Woo Waiting Update
* Fix: Translation String.
* Update: Check if $email isset.

= 1.3.3 =
* Remove: Retry Payment Preview Generate until more testing can be done or consulting with the plugin authors. Currently will only preview with orders that are actively in the Retrying payment phase.

= 1.3.2 =
* Fix: Language string for account button.
* Add: Option to remove account link in welcome email.
* Add: More placeholders for Additional Content.
* Add: Customer Payment Retry Email Type.

= 1.3.1 =
* Add: Formal German po.
* Update: Language files.

= 1.3.0 =
* ADD: Fluid/Responsive Setting
* ADD: Inner Max width Setting
* ADD: Additional Content
* ADD: Address Padding
* ADD: Button Styles ( Plus options for some links to be buttons ).
* UPDATE: All tempaltes updated for WC 3.7
* Update: Table Padding now has left and right seperate from top and bottom padding.
* Update: Container border has top, right, bottom, left width options.

= 1.2.2 =
* Update: Prevent errors from breaking emails when 3rd party plugins are doing it wrong.
* Update: Woocommerce subscriptions template translation string.
* Add: hungarian files.
* Fix: WooCommerce Subscriptions Switch preview email.
* Fix: Font family selection not working issue.

= 1.2.1 =
* Change: Advanced Shipment Tracking for WooCommerce support.
* Fix: Issue with emails missing style.
* Update: Polish Translation.
* Update: Plugin Icon.
* Add: option to turn off social section.

= 1.2.0 =
* Add: Polish Translation. Thanks Łukasz
* Add: Advanced Shipment Tracking for WooCommerce support.

= 1.1.9 =
* Update: Show WC 3.6 support.

= 1.1.8 =
* Update: WC Membership Support.

= 1.1.7 =
* Fix: PHP error.

= 1.1.6 =
* Add: Username placeholder for some emails.
* Add: Flatsome Support.

= 1.1.5 =
* Fix: placeholder for account emails.
* Add: Whatsapp and Pinterest Images.

= 1.1.4 =
* Fix: Padding issue with order details.
* Fix: Subscription Cancelled email preview.

= 1.1.3 =
* Fix: Issue with WP Multilang.
* Add: {year} for use in the footer.
* Fix: Force image to refreash email loaded so settings work.

= 1.1.2 =
* Fix: Issue with WooCommerce Order Status Manager.
* Update: Better fallback if Order is Deleted.

= 1.1.1 =
* Fix: Divi theme issue in email customizer.
* Fix: Issue with Themeisle theme.

= 1.1.0 =
* Fix: PHP undefined notices
* Fix: Some emails not showing image when image turned on.
* Add: Danish translation.

= 1.0.9 =
* Fix: Container Border
* Update: WPML Config

= 1.0.8 =
* Add: Warning about Box Shadow not being well supported by email clients.
* Update: Better Outlook Inbox support.

= 1.0.7 =
* Add: Romania and Catalan Translation.
* Update: Preview url for better support.
* Add: Waitlist Support.

= 1.0.6 =
* Fix: Partial refund text.
* Add: New option for product photo in email.
* Fix: Outlook issue.
* Update: Woo 3.5 currently not adding the footer text to templates. May consider adding later and making editable.

= 1.0.5 =
* Add Translations: Finnish.
* Update: Translations, remove some fuzzy.

= 1.0.4 =
* Add Translations: Spanish, Dutch, German, French, Italian, Portuguese (BR), Russian.

= 1.0.3 =
* Bug Fix: Notices in customzier.

= 1.0.2 =
* Bug Fix: PHP 5.2.4 issue.

= 1.0.1 =
* Bug Fix: Behind separator can't be absolute positioned.

= 1.0.0 =
* initial release
