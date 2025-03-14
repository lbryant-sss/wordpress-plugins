=== WooCommerce Stripe Payment Gateway ===
Contributors: woocommerce, automattic, royho, akeda, mattyza, bor0, woothemes
Tags: credit card, stripe, apple pay, payment request, google pay, sepa, bancontact, alipay, giropay, ideal, p24, woocommerce, automattic
Requires at least: 6.5
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 9.3.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Attributions: thorsten-stripe

Take credit card payments on your store using Stripe.

== Description ==

Changing consumer behavior has resulted in an explosion of payment methods and experiences, which are great for increasing conversion and lowering costs—but can be difficult for businesses to maintain. Give customers a best-in-class checkout experience while you remain focused on your core business. This is the official plugin created by Stripe and WooCommerce.

= Drive 11.9% in revenue with an optimized checkout experience from Stripe =

The enhanced checkout experience from Stripe can help customers:

- **Boost conversion:** Provide an optimal experience across mobile, tablet, and desktop with a responsive checkout, and offer 23 payment methods, including [Link](https://stripe.com/payments/link), [Apple Pay](https://woocommerce.com/apple-pay/), and [Google Pay](https://www.google.com/payments/solutions/), out of the box.
- **Expand your customer base:** Convert customers who might otherwise abandon their cart with buy now, pay later methods like Klarna, Affirm, and Afterpay/Clearpay, wallets like Apple Pay, Google Pay, Alipay, and WeChat Pay, and local payment methods such as Bancontact in Europe and Alipay in Asia Pacific. Deliver a localized payment experience with out-of-the-box support for localized error messages, right-to-left languages, and automatic adjustment of input fields based on payment method and country.
- **Meet existing customer demand and localize the experience:** Offer [local payment methods](https://stripe.com/guides/payment-methods-guide), such as Bancontact, Boleto, Cash App Pay, EPS, giropay, iDEAL, Multibanco, OXXO, Przelewy 24, and SEPA Direct Debit.
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

= 9.3.0 - 2025-03-13 =
* Dev - Adds a new README.md file to the plugin with specific development-focused instructions.
* Add - Implements the Single Payment Element feature for the new checkout experience on the block checkout page.
* Dev - Additional replacements for payment method constant values on the backend.
* Fix - Improves the checking for existing customer attribute when retrieving a payment method that may be detached from a subscription.
* Fix - Reverts the default value for the `capture_method` property to avoid breaking Amazon Pay when creating a payment intent.
* Add - Adds a new feature flag to handle the Single Payment Element feature.
* Dev - Moves the method to check if the subscriptions extension is enabled to a new helper class.
* Dev - Moves and refactor some of the UPE-related code to make Single Payment Element development easier.
* Add - Add logging of IP address issues when setting up mandate data.
* Fix - Fixes a fatal error that might happen when a payment method ID cannot be retrieved during the processing of an order (new checkout experience).
* Dev - Generates a code coverage report for PHP Unit tests as a comment on PRs.
* Add - Adds Stripe specific information to the System Status Report data.
* Fix - Fixes a fatal error that might happen during extension install due to missing Amazon Pay default settings data, when registering the settings route.
* Dev - Adds the payment method constants to the payment methods map file (frontend side).
* Add - Adds a new notice for store admins when there are subscriptions without a payment method attached.
* Fix - Hides "pay" and "cancel" buttons on the order received page when an Amazon Pay order is pending, since it may take a while to be confirmed.
* Fix - Prepare the redirect URL at the end of 'process_payment' method.
* Fix - Fix uncaught error in block editor when the new checkout experience is enabled.
* Fix - Fix error when processing a subscription via Amazon Pay.
* Fix - Make Amazon Pay compatible with upfront pre-orders.
* Add - Include minimum amounts in the capture_terminal_payment endpoint when a capture fails.
* Dev - Fix changelog action
* Tweak - Map feature flags into a standard array for easier maintenance.
* Dev - Fix QIT Tests GitHub workflow.
* Fix - Fix issue where payment methods do not refresh after address changes.
* Add - Bacs: Process Payment with Saved Bank Details
* Tweak - Update payment method logos on the checkout page.
* Update - Refactor unsupported deferred intent in the blocks checkout.
* Add - Use idempotency keys when creating payment intents, to help prevent duplicate charges for a single order.
* Fix - Allow to save card during checkout with account creation.
* Add - Add BLIK LPM feature flag.
* Fix - Skip unnecessary save step when already using a saved payment method for legacy checkout.
* Fix - Avoid duplicate payment method element for classic checkout.
* Fix - ACSS: Handle errors and edge cases.
* Add - Add subscriptions support to Bacs.
* Update - Add tracks events for payment method settings updates.
* Fix - Fix issue where Legacy Checkout settings get overwritten with old value.
* Add - Add WooCommerce Pre-Orders support to Bacs.
* Tweak - Fix background in express checkout settings.
* Fix - Prevent potential duplicate renewal charges by ensuring subscription integration hooks are only attached once per Gateway ID
* Update - Update Amazon Pay icon to use image from WooCommerce Design Library.
* Add - Show upcoming legacy checkout experience deprecation notice.

[See changelog for all versions](https://raw.githubusercontent.com/woocommerce/woocommerce-gateway-stripe/trunk/changelog.txt).
