=== LazyLoad Plugin – Lazy Load Images, Videos, and Iframes ===
Contributors: wp_rocket, wp_media
Tags: lazyload, lazy load, images, iframes, thumbnail, thumbnails, smiley, smilies, avatar, gravatar, youtube
Requires at least: 4.9
Tested up to: 6.8
Requires PHP: 7.3
Stable tag: 2.3.9
Tags: lazy load, lazy loading, defer offscreen images, lazy load plugin, lazy load images, image lazy loading, iframe lazy load, video lazy load

The best free lazy load plugin for WordPress. Lazy load images, videos, and iframes to improve performance and Core Web Vitals scores.

== Description ==

LazyLoad is the best free lazy load plugin for WordPress to lazy load images, videos, and iframes on WordPress. In a nutshell, LazyLoad displays images, videos, and iframes on a page only when they are visible to the user – that’s one crucial way to [speed up your WordPress site](https://wp-rocket.me/blog/guide-to-page-speed-optimization-for-wordpress/) and [optimize images for Google PageSpeed](https://imagify.io/blog/optimize-images-page-speed-google/#lazy-loading).

You can lazy load images in post content or widget text, plus thumbnails, avatars, and smilies. LazyLoad takes care of iframe lazy load, too: you’ll easily replace Youtube iframes with a preview thumbnail to further speed up the loading time of your website.

No JavaScript library such as jQuery is used, and the script weight is less than 10KB.

= Why is lazy loading crucial for performance? =

Lazy loading is a key performance technique to make your site faster. You’ll reduce loading time, [improve your Lighthouse performance score](https://wp-rocket.me/lighthouse-performance-score-wordpress/) and [optimize your Core Web Vitals grades](https://wp-rocket.me/google-core-web-vitals-wordpress/).

[Lazy loading your images on WordPress](https://wp-rocket.me/blog/lazy-loading-wordpress-5-5/) will help you achieve a better PageSpeed Insights score for three main reasons:

* You’ll address a specific PageSpeed Insights recommendation: [Defer offscreen images](https://wp-rocket.me/google-core-web-vitals-wordpress/defer-offscreen-images/, which means image lazy loading.
* You’ll improve the performance of two key metrics: [First Input Delay](https://wp-rocket.me/google-core-web-vitals-wordpress/improve-first-input-delay/) (Core Web Vital) and [Total Blocking Time](https://wp-rocket.me/lighthouse-performance-score-wordpress/reduce-total-blocking-time/) (Lighthouse metric).
* You’ll [make fewer HTTP requests](https://wp-rocket.me/blog/reduce-http-requests-speed-wordpress-site/) – that is another way to boost your site speed and [improve the Largest Contentful Paint score](https://wp-rocket.me/google-core-web-vitals-wordpress/improve-largest-contentful-paint/) (another Core Web Vital).

Take a look at our complete list of reasons [why you should use lazy loading](https://wp-rocket.me/blog/lazyloading/#section-2). Then, turn on LazyLoad and make your WordPress website faster!


= Dependencies =

LazyLoad script: [https://github.com/verlok/lazyload](https://github.com/verlok/lazyload)

== Installation ==

1. Upload the complete `rocket-lazy-load` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= How can I use native lazyload? =
To use native lazyload on browsers supporting this feature, you need to use the following line:

`add_filter( 'rocket_use_native_lazyload', '__return_true' );`

Browsers that do not support native lazyload will use the JS-based solution as before.

= How can I deactivate Lazy Load on some pages? =

You can use the `do_rocket_lazyload` filter.

Here is an example to put in functions.php files that disable lazyload on posts:

`
add_action( 'wp', 'deactivate_rocket_lazyload_on_single' );
function deactivate_rocket_lazyload_on_single() {
	if ( is_single() ) {
		add_filter( 'do_rocket_lazyload', '__return_false' );
	}
}
`

= How can I deactivate Lazy Load on some images? =

Simply add a `data-no-lazy="1"` property in you `img` or `iframe` tag.

You can also use the filters `rocket_lazyload_excluded_attributes` or `rocket_lazyload_excluded_src` to exclude specific patterns.

For iframes, the filter is `rocket_lazyload_iframe_excluded_patterns`.

= How can I change the threshold to trigger the load? =

You can use the `rocket_lazyload_threshold` filter.

Code sample:

`
function rocket_lazyload_custom_threshold( $threshold ) {
	return 100;
}
add_filter( 'rocket_lazyload_threshold', 'rocket_lazyload_custom_threshold' );
`

= I use plugin X and my images don't show anymore =

Some plugins are not compatible without lazy loading. Please open a support thread, and we will see how we can solve the issue by excluding lazy loading for this plugin.

= How can I lazy load a background-image? =

The plugin will automatically lazy load background-images set with a `style` attribute to a `div` element:

`<div style="background-image: url(image.jpg);">`

You can also apply it manually. The element you want to apply lazy load on must have this specific markup:

`<div class="rocket-lazyload" data-bg="url(../img/image.jpg)"></div>`

The element must have the class `rocket-lazyload`, and a `data-bg` attribute, which value is the CSS url for the image.

= Where do I report security bugs found in this plugin? =

You can report any security bugs found in the source code of the site-reviews plugin through the [Patchstack Vulnerability Disclosure Program](https://patchstack.com/database/vdp/rocket-lazy-load). The Patchstack team will assist you with verification, CVE assignment and take care of notifying the developers of this plugin.

= Related Plugins =

* [Imagify: The Best image optimizer](https://imagify.io/?utm_source=wordpress.org&utm_medium=referral&utm_campaign=LazyLoadPlugin) to speed up your website with lighter images.
* [WP Rocket: Best performance plugin](https://wp-rocket.me/?utm_source=wordpress.org&utm_medium=referral&utm_campaign=LazyLoadPlugin) to speed up your WordPress website.
* [Heartbeat Control by WP Rocket](https://wordpress.org/plugins/heartbeat-control/): Heartbeat Control by WP Rocket: The best plugin to control the WordPress Heartbeat API and reduce CPU usage.
* [RocketCDN: The best CDN plugin for WordPress](https://rocketcdn.me/wordpress/) to propel your content at the speed of light – no matter where your users are located in the world.
* [Increase Max upload file size](https://wordpress.org/plugins/upload-max-file-size/) is the best plugin to increase the upload file size limit to any value with one click.

== Changelog ==
= 2.3.9 =
Updated version to fix a mismatch between the tag of the release on Github and the release version which leads to a deployment issue that.

= 2.3.8 =
Enhancement: Launchpad compatibility (see https://github.com/wp-launchpad)
Enhancement: Raised compatibility with PHP > 7.3
Bug: Removed `wp-media/rocket-lazyload-common` from vendors
Enhancement: Raised `wp-media/rocket-lazyload-common` to 3.0

= 2.3.7 =
Bugfix: Removed `rocket_lazyload_polyfill` filter due to a vulnerability on polyfill

= 2.3.5 =
Enhancement: Test the plugin with latest version of WordPress v5.9.3
Enhancement: Change WP readme content.

= 2.3.4 =
Enhancement: Allow `<a>` tags to lazyload background images
Enhancement: Add <noscript> tag to lazyloaded picture elements
Bugfix: Prevent a Fatal error related to the League Container package conflict with WooCommerce 4.4
Bugfix: Update lazyload for background images support for new version of lazyload script
Bugfix: Correctly apply the rocket-lazyload class on elements with a background-image and an empty class value
Bugfix: Correctly apply the rocket-lazyloadclass on elements with malformed HTML
Bugfix: Prevent a display issue with background-images when using different types of quotes around the URL
Bugfix: Prevent Layout from breaking when <img> alt attribute has any html encoded characters

= 2.3.3 =
Enhancement: Add data-skip-lazy and skip-lazy class to exclusions list as part of the interoperability initiative between lazyload plugins
Enhancement: Use native lazyload only if filter `rocket_use_native_lazyload` is true
Enhancement: Apply lazyload on background images set on `figure` elements
Bugfix: Correctly add the rocket-lazyload class when class attribute is empty on an element with a background image
Bugfix: Correctly replace YouTube iframe with preview image when using relative protocol
Bugfix: Preserve youtube-nocookie.com during LazyLoad

= 2.3.2 =
Bugfix: Incorrect characters used in Youtube thumbnail HTML code

= 2.3.1 =
Bugfix: Prevent a conflict with WP Rocket
Bugfix: apply loading="lazy" on Youtube thumbnail
Bugfix: Add autoplay attribute on iframe loaded with Youtube thumbnail

= 2.3 =
Enhancement: Add support for browser native lazyload
Bugfix: Prevent broken image in some cases for picture element
Bugfix: Prevent wrong lazy attributes for srcset and sizes on an image inside a picture element

= 2.2.3 =
* Enhancement: Improve compatibility for the picture element
* Enhancement: Apply lazyload on background images set on section, span and li elements
* Enhancement: also pass $width and $height values to the rocket_lazyload_placeholder filter
* Bugfix: Use 0 instead of 1 for the default placeholder dimensions to improve compatibility
* Bugfix: Improve infinite scroll support
* Bugfix: Exclude Enfold avia-background-fixed background images and data-large_image from lazyload

= 2.2.2 =
* Bugfix: Auto-exclude data-height-percentage attribute to prevent display issues
* Bugfix: Correctly handle responsive videos using fitVids again

= 2.2.1 =
* Enhancement: add a way to customize the lazyload script options
* Bugfix: Prevent error on Internet Explorer 11
* Bugfix: Prevent conflict with WooCommerce variation swatches
* Bugfix: Prevent empty `src` when the image is an inline base64
* Bugfix: Prevent issue when the original `src` attribute uses single quotes

= 2.2 =
* Enhancement: Update lazyload script to the latest version
* Enhancement: Use the dimensions of the original image for the placeholder size when possible, to reduce content reflow
* Enhancement: Ignore images using the new loading attribute introduce by Chrome for browser-native lazyload

= 2.1.5 =
* Bugfix: Prevent matching with the wrong data when a data-style attribute is on a div for background images
* Remove data-cfasync="false" by default
* Enhancement: Add filter rocket_lazyload_script_tag to modify the lazyload script HTML if needed
* Enhancement: Add data-no-minify attribute to the lazyload script tag to prevent it from being combined by JS combiners
* Enhancement: Improve MutationObserver code to only call the lazyload update method if an image/iframe or element with .rocket-lazyload is contained in the new node(s) added to the DOM

= 2.1.4 =
* Regression fix: Correctly exclude scripts from lazyload again

= 2.1.3 =
* Bugfix: Ignore content inside noscript tags to prevent modifying them and causing some display issues

= 2.1.2 =
* Enhancement: Update lazyload script to the latest version
* Enhancement: Add a way to lazyload the Youtube thumbnail image
* Enhancement: Add width and height attributes to the Youtube thumbnail image depending on the resolution
* Enhancement: Disable polyfill for intersectionObserver by default, added a way to activate it instead
* Enhancement: Add data-cfasync="false" to the lazyload script tag
* Enhancement: Prevent lazyload on the Oxygen Builder page editor
* Bugfix: Wrap no JS CSS in noscript tag and remove the no-js identifier


= 2.1.1 =
* Bugfix: Correctly apply lazyload on `picture` elements
* Bugfix: Prevent double loading of an image when an `img` element inside a `picture` element only has a `srcset` attribute and no `src` attribute

= 2.1 =
* Enhancement: Update lazyload script to the latest version
* Enhancement: Apply lazyload on picture elements found on the page
* Enhancement: Apply lazyload on div elements with a background image found on the page. See FAQ for more info.

= 2.0.4 =
* Enhancement: Add filter for iframe lazyload pattern exclusion
* Enhancement: Auto-exclude soliloquy-image pattern from lazyload
* Bugfix: Prevent issue when an image/iframe is duplicated on the same page
* Bugfix: Prevent W3C validation error for the SVG placeholder

= 2.0.3.2 =
* Bugfix: Correctly ignore inline scripts with line breaks inside

= 2.0.3.1 =
* Bugfix: Correct an issue preventing lazyload from working

= 2.0.3 =
* Bugfix: Prevent incorrect display if JavaScript is disabled
* Bugfix: Don't apply lazyload on Divi/Extra/Beaver Builder Editor pages
* Bugfix: Use the correct URL for each iframe when multiple iframes are on the same page
* Bugfix: Ignore content inside inline script tags to prevent applying lazyload in it

= 2.0.2 =
* Bugfix: Fix an error in the compatibility for the AMP plugin

= 2.0.1 =
* Bugfix: Prevent a fatal error on case sensitive operating systems

= 2.0 =
* Enhancement: Lazyload is now applied on the template_redirect hook, which should allow the plugin to apply the optimization on more images and encountering less conflicts at the same time
* Enhancement: Specifically target with the lazyload script images/iframes elements with a data-lazy-src attribute
* Enhancement: Update lazyload script to the latest version
* Enhancement: Possibility to apply lazyload on background-images with a specific markup, see FAQ
* Enhancement: Use a svg image as placeholder instead of a base64 gif
* Bugfix: Only use MutationObserver if available in the browser
* Bugfix: When using the Youtube thumbnail option, correctly format the Youtube query if the video URL is encoded
* Bugfix: Improve iframe matching to prevent unexpected results
* Bugfix: Update CSS for the Youtube thumbnail option to prevent issue with the Gutenberg embeds block

= 1.4.9 =
* Enhancement: Update lazyload script to the latest available version
* Enhancement: Use lazy-sizes to prevent W3C validation error when sizes is defined but srcset is not
* Enhancement: Parse images or iframes only if the element is selected to be lazyloaded in the options
* Fix: Prevent warning for lazyload+v in Google Search Console
* Fix: Prevent PHP Notice with WooCommerce for product images

= 1.4.8 =
* Notice: Minimum WordPress version required is now 4.7
* Enhancement: Update lazyload script version
* Enhancement: Remove placeholder image to improve perceived loading time
* Enhancement: Compatibility with Youtube privacy URL
* Enhancement: Update play image to match Youtube logo
* Enhancement: Support Youtube URL parameters
* Enhancement: Lazyload images displayed with wp_get_attachment_image(). /!\ no fallback if JavaScript is disabled
* Fix: Use the correct size set in srcset for the lazyloaded image
* Fix: Prevent Youtube thumbnail replacement on playlists
* Fix: Prevent iframe lazyload on AMP pages
* Fix: Correct text domain for translations (thanks @ Chantal Coolsma)

= 1.4.7 =
* Fix compatibility with infinite scroll
* Prevent lazyload on masterSlider images

= 1.4.6 =
* Correctly include version 8.5.2 of lazyload script
* Prevent 404 error on lazyload script if URL contains "-v"

= 1.4.5 =
* Rename Setting Page Name in WP Menu
* New Product Banner in Settings Page
* Conditionally load a different version of the script depending on browser support of IntersectionObserver
* Fix a bug where images initially hidden are not correctly displayed when coming into view (slider, tabs, accordion)

= 1.4.4 =
* Admin Redesign

= 1.4.3 =
* Plugin is compatible again with PHP < 5.4

= 1.4.2 =
* Update lazyload script to bring back compatibility with IE9/10

= 1.4.1 =
* Fix bug caused by a too aggressive cleanup

= 1.4 =
* New option: replace Youtube videos by thumbnail. This option can improve your loading time a lot, especially if you have multiple videos on the same page

= 1.3.3 =
* 2017-09-16
* Prevent scripts and styles being removed during html parsing

= 1.3.2 =
* 2017-09-12
* Fix images not displaying in certain conditions because image attributes exclusion was not working correctly

= 1.3.1 =
* 2017-09-07
* Don't apply lazyload on Divi slider

= 1.3 =
* 2017-09-01
* Improve HTML parsing of images and iframes to be faster and more efficient
* Make the lazyload compatible with fitVids for iframes
* Don't apply lazyload on AMP pages (compatible with AMP plugin from Automattic)
* Use about:blank as default iframe placeholder to prevent warning in browser console
* Don't apply lazyload on upPrev thumbnail

= 1.2.1 =
* 2017-08-22
* Fix missing lazyload script
* Don't lazyload for images in REST API requests

= 1.2 =
* 2017-08-22
* Update lazyload script to latest version
* Change the way the script is loaded

= 1.1.1 =
* 2017-02-13
* Bug fix: Remove use of short tag to prevent 500 error on some installations

= 1.1 =
* 2017-02-12
* *New*
 * JS library updated
 * Support for iFrame
 * Support for srcset and sizes
 * New options page

= 1.0.4 =
* 2015-04-28
* Bug Fix: Resolved a conflict between LazyLoad & Emoji since WordPress 4.2

= 1.0.3 =
* 2015-01-08
* Bug Fix: Don't apply LazyLoad on captcha from Really Simple CAPTCHA to prevent conflicts.

= 1.0.2 =
* 2014-12-28
* Improvement: Add « rocket_lazyload_html » filter to manage the output that will be printed.

= 1.0.1.1 =
* 2014-07-25
* Fix stupid error with new regex in 1.0.1

= 1.0.1 =
* 2014-07-16
* Bug Fix: when a IMG tag or content (widget or post) contains the string "data-no-lazy", all IMG tags were ignored instead of one.
* Security fix: The preg_replace() could lead to a XSS vuln, thanks to Alexander Concha
* Code compliance

= 1.0 =
* 2014-01-01
* Initial release.
