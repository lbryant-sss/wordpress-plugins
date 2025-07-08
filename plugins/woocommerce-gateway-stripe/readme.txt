=== WooCommerce Stripe Payment Gateway ===
Contributors: woocommerce, automattic, royho, akeda, mattyza, bor0, woothemes
Tags: credit card, stripe, payments, woocommerce, woo
Requires at least: 6.6
Tested up to: 6.8.1
Requires PHP: 7.4
Stable tag: 9.5.3
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Attributions: thorsten-stripe

Accept debit and credit cards in 135+ currencies, many local methods like Alipay, ACH, and SEPA, and express checkout with Apple Pay and Google Pay.

== Description ==

Changing consumer behavior has resulted in an explosion of payment methods and experiences, which are great for increasing conversion and lowering costs—but can be difficult for businesses to maintain. Give customers a best-in-class checkout experience while you remain focused on your core business. This is the official plugin created by Stripe and WooCommerce.

= Drive 11.9% in revenue with an optimized checkout experience from Stripe =

The enhanced checkout experience from Stripe can help customers:

- **Boost conversion:** Provide an optimal experience across mobile, tablet, and desktop with a responsive checkout, and offer 23 payment methods, including [Link](https://stripe.com/payments/link), [Apple Pay](https://woocommerce.com/apple-pay/), and [Google Pay](https://www.google.com/payments/solutions/), out of the box.
- **Expand your customer base:** Convert customers who might otherwise abandon their cart with buy now, pay later methods like Klarna, Affirm, and Afterpay/Clearpay, wallets like Apple Pay, Google Pay, Alipay, and WeChat Pay, and local payment methods such as Bancontact in Europe and Alipay in Asia Pacific. Deliver a localized payment experience with out-of-the-box support for localized error messages, right-to-left languages, and automatic adjustment of input fields based on payment method and country.
- **Meet existing customer demand and localize the experience:** Offer [local payment methods](https://stripe.com/guides/payment-methods-guide), such as ACH Direct Debit, Bacs Direct Debit, Bancontact, BECS Direct Debit, BLIK, Boleto, Cash App Pay, EPS, iDEAL, Multibanco, OXXO, Pre-authorized debit payments, Przelewy 24, and SEPA Direct Debit.
- **Fight fraud:** Detect and prevent fraud with [Stripe Radar](https://stripe.com/radar), which offers seamlessly integrated, powerful fraud-detection tools that use machine learning to detect and flag potentially fraudulent transactions.
- **Accept in-person payments for products and services:** Use the Stripe Terminal M2 card reader or get started with no additional hardware using Tap to Pay on iPhone, or Tap to Pay on Android.
- **Support subscriptions:** Support recurring payments with various payment methods via [WooCommerce Subscriptions](https://woocommerce.com/products/woocommerce-subscriptions/).
- **Manage cash flow:** Get paid within minutes with Stripe Instant Payouts, if eligible.
- **Achieve [PCI-DSS](https://docs.stripe.com/security) compliance with [Stripe Elements](https://stripe.com/payments/elements) hosted input fields.**
- Support Strong Customer Authentication (SCA).

Stripe is available for store owners and merchants in [46 countries worldwide](https://stripe.com/global), with more to come.

== Frequently Asked Questions ==

= In which specific countries is Stripe available? =

Stripe is available in the following countries, with more to come:

- Australia
- Austria
- Belgium
- Brazil
- Bulgaria
- Canada
- Croatia
- Cyprus
- Czech Republic
- Denmark
- Estonia
- Finland
- France
- Germany
- Gibraltar
- Greece
- Hong Kong
- Hungary
- India
- Ireland
- Italy
- Japan
- Latvia
- Liechtenstein
- Lithuania
- Luxembourg
- Malaysia
- Malta
- Mexico
- Netherlands
- New Zealand
- Norway
- Poland
- Portugal
- Romania
- Singapore
- Slovakia
- Slovenia
- Spain
- Sweden
- Switzerland
- Thailand
- United Arab Emirates
- United Kingdom
- United States

= Does this require an SSL certificate? =

Yes. In Live Mode, an SSL certificate must be installed on your site to use Stripe. In addition to SSL encryption, Stripe provides an extra JavaScript method to secure card data using [Stripe Elements](https://stripe.com/elements).

= Does this support both production mode and sandbox mode for testing? =

Yes, it does. Both production and test (sandbox) modes are driven by the API keys you use with a checkbox in the admin settings to toggle between both.

= Where can I find documentation? =

Refer to the [Stripe WooCommerce Extension documentation for more information, including how to set up and configure the extension](https://woocommerce.com/document/stripe/).

= Where can I get support or talk to other users? =

If you get stuck, you can ask for help in the [Plugin Forum](https://wordpress.org/support/plugin/woocommerce-gateway-stripe/).

== Screenshots ==

1. With the enhanced checkout from Stripe, you can surface 23 payment methods including buy now, pay later methods; and Link, an accelerated checkout experience.
2. Link autofills your customers’ payment information to create an easy and secure checkout experience.
3. Convert customers who would usually abandon their cart and increase average order value with buy now, pay later options like Klarna, Afterpay, and Affirm. Accept credit and debit card payments from Visa, Mastercard, American Express, Discover, and Diners.
4. Stripe Radar offers seamlessly integrated, powerful fraud-detection tools that use machine learning to detect and flag potentially fraudulent transactions.
5. Accept in-person payments for products and services using the Stripe Terminal M2 card reader.
6. Get started with no additional hardware using Tap to Pay on iPhone, or Tap to Pay on Android.

== Changelog ==

= 9.6.0 - 2025-07-07 =

**New Features**

* Legacy checkout experience has been deprecated, new checkout experience is now the default for all sites
* Voucher payment methods (Boleto, Multibanco, and Oxxo) can now be used when purchasing subscriptions if manual renewals are enabled or required
* Show an icon beside the payment methods that support automatic recurring payments
* Include extension data from block checkout when submitting an express checkout order

**Important Fixes and Updates**

* Update - Support block checkout custom fields when using express payment methods like Apple Pay and Google Pay
* Update - Express Checkout: introduce new WP actions for supporting custom checkout fields for classic, shortcode-based checkout
* Fix - Apply shipping country restrictions to Express Checkout
* Add - Introduced `wc_stripe_force_save_payment_method` filter
* Update - Removes the customization of individual payment method titles and descriptions
* Fix - Add order locking when processing payment redirects, to mitigate cases of double status updates
* Fix - Correctly handle countries without states when using the express payment methods
* Update - Remove legacy checkout checkbox from settings
* Update - Remove BACS from the unsupported ‘change payment method for subscription’ page
* Update - Remove verification steps for Apple Pay domain registration, as this is no longer required by Stripe
* Update - Update deprecation notice message to specify that legacy checkout experience has been deprecated since version 9.6.0
* Fix - Correctly notifies customers and merchants of a failed refund and reverts the refunded status
* Fix - Void intent when cancelling an uncaptured order
* Fix - Fixes page crash when Klarna payment method is not supported in the merchant's country by returning an empty array instead of throwing an error
* Dev - Deprecates the WC_Stripe_Order class and removes its inclusion call

**Other Fixes**

* Fix - Fix payment processing for $0 subscription with recurring coupon
* Fix - Fixes an edge case where the express payment method buttons would not be displayed on the checkout if taxes used to be enabled
* Fix - Fixes a fatal error when the fingerprint property is not available for a card payment method
* Fix - Fix payment method title display when new payment settings experience is enabled
* Fix - Prevent styles from non-checkout pages affecting the appearance of Stripe element
* Fix - Send correct attribute when setting the default payment method
* Fix - Hide future payments message from payment element when manual renewal is required
* Fix - Fix a rare warning when searching customers with missing name
* Fix - Fix legacy deprecation notice displayed on new plugin installs
* Fix - When the user is deleted via WP CLI, take into account the environment type before detaching their payment methods
* Fix - Throws a specific exception on an edge case where a saved payment method could not be found when processing an order in the new checkout experience
* Fix - Register Express Checkout script before use to restore buttons on “order-pay” pages
* Fix - Add safety check when checking error object

**Internal Changes and Upcoming Features**

* Update - Remove Payment Method Configurations fallback cache
* Update - Add prefix to the custom database cache keys
* Add - Introduces a new marketing note to promote BNPLs (Buy Now Pay Later) payment methods (Klarna and Affirm) on WooCommerce admin home page
* Add - Adds a new promotional banner to promote the BNPL payment methods (Klarna, Afterpay, and Affirm) on the settings page.
* Fix - Checks if the store has other BNPL extensions installed before displaying the promotional banner
* Fix - Restricts the BNPLs promotional banner to only be displayed after version 9.7.0
* Add - [Optimized Checkout] Adds a new filter (wc_stripe_is_optimized_checkout_available) to allow merchants to test the Optimized Checkout feature earlier
* Fix - [Optimized Checkout] Sends missing information to Stripe when completing transactions with WeChat Pay, Blik and Klarna, using the Optimized Checkout
* Fix - [Optimized Checkout] Fixes the availability of the saving payment method checkbox in the classic checkout when the Optimized Checkout is enabled and signup is disabled during checkout
* Fix - [Optimized Checkout] Makes payment methods dynamically available on the shortcode checkout when the Optimized Checkout is enabled depending on the saving method checkbox value
* Fix - [Optimized Checkout] Fixes the payment method title when using the classic checkout with the Optimized Checkout enabled
* Update - [Optimized Checkout] Removes the change display order feature from the settings page when the Optimized Checkout is enabled
* Fix - [Optimized Checkout] Fixes some inconsistencies related to the Optimized Checkout feature and improves its unit tests
* Dev - Implements the PSR-4 autoloading standard for the plugin unit tests (PHP)
* Dev - Moves the main Stripe class to a new file
* Dev - Renames all PHP Unit test files to follow the PSR-4
* Dev - Dynamically retrieves versions of WooCommerce and WordPress to use in the PHP code coverage GitHub Actions Workflow.
* Dev - Add e2e tests for BLIK
* Dev - Add e2e tests for BECS
* Dev - Add e2e tests to cover Affirm purchase flow
* Dev - Add Klarna e2e tests
* Dev - Improve e2e tests of some of the LPMs
* Dev - Build dynamic WordPress and WooCommerce dependencies for unit tests
* Dev - Prevent changelog entries with trailing periods
* Dev - Fix failing optimized checkout e2e test due to incorrect order of operations
* Dev - Re-include the deprecated WC_Stripe_Order class to avoid breaking changes for merchants using it

[See changelog for full details across versions](https://raw.githubusercontent.com/woocommerce/woocommerce-gateway-stripe/trunk/changelog.txt).
