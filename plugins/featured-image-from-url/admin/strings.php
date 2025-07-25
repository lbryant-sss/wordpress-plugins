<?php

function fifu_get_strings_settings() {
    $fifu = array();

    // options
    $fifu['options']['settings'] = function () {
        return __("Settings", FIFU_SLUG);
    };
    $fifu['options']['cloud'] = function () {
        return __("Cloud", FIFU_SLUG);
    };
    $fifu['options']['troubleshooting'] = function () {
        return __("Troubleshooting", FIFU_SLUG);
    };
    $fifu['options']['status'] = function () {
        return __("Status", FIFU_SLUG);
    };
    $fifu['options']['upgrade'] = function () {
        return __("Upgrade to <b>PRO</b>", FIFU_SLUG);
    };
    $fifu['options']['key'] = function () {
        return __("License key", FIFU_SLUG);
    };
    $fifu['options']['expired'] = function () {
        return __("License Key Expired", FIFU_SLUG);
    };

    // php
    $fifu['php']['message']['wait'] = function () {
        return __("Please wait a few seconds...", FIFU_SLUG);
    };
    $fifu['php']['message']['wait1'] = function () {
        return __("Please wait 1 minute...", FIFU_SLUG);
    };

    // buttons
    $fifu['button']['submit'] = function () {
        _e("Submit", FIFU_SLUG);
    };
    $fifu['button']['clipboard'] = function () {
        _e("Copy to clipboard", FIFU_SLUG);
    };

    // details
    $fifu['detail']['important'] = function () {
        _e("Important", FIFU_SLUG);
    };
    $fifu['detail']['requirement'] = function () {
        _e("Requirement", FIFU_SLUG);
    };
    $fifu['detail']['tip'] = function () {
        _e("Tip", FIFU_SLUG);
    };
    $fifu['detail']['example'] = function () {
        _e("Example", FIFU_SLUG);
    };
    $fifu['detail']['eg'] = function () {
        _e("e.g.:", FIFU_SLUG);
    };
    $fifu['detail']['result'] = function () {
        _e("Result", FIFU_SLUG);
    };
    $fifu['detail']['notice'] = function () {
        _e("Note", FIFU_SLUG);
    };
    $fifu['detail']['developers'] = function () {
        _e("Developers", FIFU_SLUG);
    };

    // words
    $fifu['word']['color'] = function () {
        _e("color", FIFU_SLUG);
    };
    $fifu['word']['mode'] = function () {
        _e("mode", FIFU_SLUG);
    };
    $fifu['word']['inline'] = function () {
        _e("inline", FIFU_SLUG);
    };
    $fifu['word']['lightbox'] = function () {
        _e("lightbox", FIFU_SLUG);
    };
    $fifu['word']['zindex'] = function () {
        _e("z-index", FIFU_SLUG);
    };
    $fifu['word']['size'] = function () {
        _e("size", FIFU_SLUG);
    };
    $fifu['word']['zoom'] = function () {
        _e("zoom", FIFU_SLUG);
    };
    $fifu['word']['function'] = function () {
        _e("Function", FIFU_SLUG);
    };
    $fifu['word']['field'] = function () {
        _e("Fields", FIFU_SLUG);
    };
    $fifu['word']['delimiter'] = function () {
        _e("Delimiter", FIFU_SLUG);
    };
    $fifu['word']['pro'] = function () {
        _e("PRO", FIFU_SLUG);
    };
    $fifu['word']['yes'] = function () {
        _e("yes", FIFU_SLUG);
    };
    $fifu['word']['no'] = function () {
        _e("no", FIFU_SLUG);
    };
    $fifu['word']['local'] = function () {
        _e("Local", FIFU_SLUG);
    };
    $fifu['word']['external'] = function () {
        _e("Remote", FIFU_SLUG);
    };
    $fifu['word']['status'] = function () {
        _e("Status", FIFU_SLUG);
    };
    $fifu['word']['troubleshooting'] = function () {
        _e("Troubleshooting", FIFU_SLUG);
    };
    $fifu['word']['name'] = function () {
        _e("Name", FIFU_SLUG);
    };
    $fifu['word']['type'] = function () {
        _e("Type", FIFU_SLUG);
    };
    $fifu['word']['data'] = function () {
        _e("Data", FIFU_SLUG);
    };
    $fifu['word']['width'] = function () {
        _e("Width", FIFU_SLUG);
    };
    $fifu['word']['height'] = function () {
        _e("Height", FIFU_SLUG);
    };
    $fifu['word']['crop'] = function () {
        _e("Crop", FIFU_SLUG);
    };
    $fifu['word']['pages'] = function () {
        _e("Pages", FIFU_SLUG);
    };
    $fifu['word']['saving'] = function () {
        return __("Saving", FIFU_SLUG);
    };
    $fifu['word']['saved'] = function () {
        return __("Saved", FIFU_SLUG);
    };
    $fifu['word']['error'] = function () {
        return __("Error", FIFU_SLUG);
    };
    $fifu['word']['reset'] = function () {
        return __("Reset", FIFU_SLUG);
    };
    $fifu['word']['save'] = function () {
        return __("Save", FIFU_SLUG);
    };

    // where
    $fifu['where']['page'] = function () {
        _e("on page", FIFU_SLUG);
    };
    $fifu['where']['post'] = function () {
        _e("on post ", FIFU_SLUG);
    };
    $fifu['where']['cpt'] = function () {
        _e("on custom post type", FIFU_SLUG);
    };
    $fifu['where']['home'] = function () {
        _e("on homepage (or shop)", FIFU_SLUG);
    };
    $fifu['where']['else'] = function () {
        _e("elsewhere", FIFU_SLUG);
    };
    $fifu['where']['single'] = function () {
        _e("on single post types", FIFU_SLUG);
    };
    $fifu['where']['desktop'] = function () {
        _e("on desktop", FIFU_SLUG);
    };
    $fifu['where']['mobile'] = function () {
        _e("on mobile phone", FIFU_SLUG);
    };

    // player
    $fifu['player']['available']['for'] = function () {
        _e("Available for", FIFU_SLUG);
    };
    $fifu['player']['available']['mouse'] = function () {
        _e("YouTube, Vimeo, local videos, remote video files", FIFU_SLUG);
    };
    $fifu['player']['available']['autoplay'] = function () {
        _e("YouTube, Vimeo, Odysee, Rumble, local videos, remote video files", FIFU_SLUG);
    };
    $fifu['player']['available']['loop'] = function () {
        _e("YouTube, Vimeo, local videos, remote video files", FIFU_SLUG);
    };
    $fifu['player']['available']['mute'] = function () {
        _e("YouTube, Vimeo, local videos, remote video files", FIFU_SLUG);
    };
    $fifu['player']['available']['background'] = function () {
        _e("Vimeo", FIFU_SLUG);
    };
    $fifu['player']['available']['privacy'] = function () {
        _e("YouTube", FIFU_SLUG);
    };

    // chrome
    $fifu['chrome']['link'] = function () {
        _e("Chrome extension", FIFU_SLUG);
    };
    $fifu['server']['details'] = function () {
        _e("Async requests from the FIFU server to your site may be used to split data processing into parts, avoiding execution timeouts on slower tasks and excessive memory usage on larger tasks. They also bypass the lack of a real cron system in WordPress, keeping some automatic features running even when the site is not receiving visitors.", FIFU_SLUG);
    };
    $fifu['server']['success'] = function () {
        return __("Test connection with the FIFU server completed successfully.", FIFU_SLUG);
    };
    $fifu['server']['fail'] = function () {
        return __("Test connection with the FIFU server failed. If you are using a firewall, please allow FIFU requests.", FIFU_SLUG);
    };
    $fifu['key']['success']['main'] = function () {
        return __("Your license key was successfully registered.", FIFU_SLUG);
    };
    $fifu['key']['expired']['main'] = function () {
        return __("Your license key has expired.", FIFU_SLUG);
    };
    $fifu['key']['invalid']['main'] = function () {
        return __("Your license key is invalid.", FIFU_SLUG);
    };
    $fifu['key']['success']['details'] = function () {
        return __("FIFU is now fully activated, and all features are unlocked.", FIFU_SLUG);
    };
    $fifu['key']['expired']['details'] = function () {
        return __("Please renew or upgrade it to continue receiving updates and support.", FIFU_SLUG);
    };
    $fifu['key']['invalid']['details'] = function () {
        return __("Please check the key and try again, or contact support for assistance. A valid license key is required for the plugin to work correctly and without limitations.", FIFU_SLUG);
    };

    // messages
    $fifu['message']['wait'] = function () {
        _e("Please wait a few seconds...", FIFU_SLUG);
    };

    // tabs
    $fifu['tab']['help'] = function () {
        _e("Help", FIFU_SLUG);
    };
    $fifu['tab']['admin'] = function () {
        _e("Admin", FIFU_SLUG);
    };
    $fifu['tab']['image'] = function () {
        _e("Image", FIFU_SLUG);
    };
    $fifu['tab']['auto'] = function () {
        _e("Automatic", FIFU_SLUG);
    };
    $fifu['tab']['metadata'] = function () {
        _e("Metadata", FIFU_SLUG);
    };
    $fifu['tab']['dev'] = function () {
        _e("Developers", FIFU_SLUG);
    };
    $fifu['tab']['slider'] = function () {
        _e("Slider", FIFU_SLUG);
    };
    $fifu['tab']['audio'] = function () {
        _e("Audio", FIFU_SLUG);
    };
    $fifu['tab']['video'] = function () {
        _e("Video", FIFU_SLUG);
    };
    $fifu['tab']['trouble'] = function () {
        _e("Troubleshooting", FIFU_SLUG);
    };
    $fifu['tab']['key'] = function () {
        _e("License key", FIFU_SLUG);
    };
    $fifu['tab']['renewal'] = function () {
        _e("Renew or upgrade", FIFU_SLUG);
    };
    $fifu['tab']['cloud'] = function () {
        _e("Cloud", FIFU_SLUG);
    };

    // titles
    $fifu['title']['support'] = function () {
        _e("Technical support", FIFU_SLUG);
    };
    $fifu['title']['start'] = function () {
        _e("Getting started", FIFU_SLUG);
    };
    $fifu['title']['reset'] = function () {
        _e("Reset Settings", FIFU_SLUG);
    };
    $fifu['title']['media'] = function () {
        _e("Save in the Media Library", FIFU_SLUG);
    };
    $fifu['title']['auto'] = function () {
        _e("Auto set featured image using post title and search engine", FIFU_SLUG);
    };
    $fifu['title']['isbn'] = function () {
        _e("Auto set featured image from ISBN", FIFU_SLUG);
    };
    $fifu['title']['asin'] = function () {
        _e("Auto set product images from ASIN", FIFU_SLUG);
    };
    $fifu['title']['customfield'] = function () {
        _e("Auto set featured media from custom field", FIFU_SLUG);
    };
    $fifu['title']['screenshot'] = function () {
        _e("Auto set screenshot as featured image", FIFU_SLUG);
    };
    $fifu['title']['finder'] = function () {
        _e("Auto set featured media using web page address", FIFU_SLUG);
    };
    $fifu['title']['tags'] = function () {
        _e("Auto set featured image from Unsplash using tags", FIFU_SLUG);
    };
    $fifu['title']['block'] = function () {
        _e("Disable right-click", FIFU_SLUG);
    };
    $fifu['title']['replace'] = function () {
        _e("Replace Not Found Image", FIFU_SLUG);
    };
    $fifu['title']['default'] = function () {
        _e("Default Featured Image", FIFU_SLUG);
    };
    $fifu['title']['pcontent'] = function () {
        _e("Modify Post Content", FIFU_SLUG);
    };
    $fifu['title']['hide'] = function () {
        _e("Hide Featured Media", FIFU_SLUG);
    };
    $fifu['title']['popup'] = function () {
        _e("Custom Popup", FIFU_SLUG);
    };
    $fifu['title']['redirection'] = function () {
        _e("Page Redirection", FIFU_SLUG);
    };
    $fifu['title']['html'] = function () {
        _e("Auto set featured media from post content", FIFU_SLUG);
    };
    $fifu['title']['metadata'] = function () {
        _e("Image Metadata", FIFU_SLUG);
    };
    $fifu['title']['clean'] = function () {
        _e("Clear Metadata", FIFU_SLUG);
    };
    $fifu['title']['schedule'] = function () {
        _e("Schedule Metadata Generation", FIFU_SLUG);
    };
    $fifu['title']['delete'] = function () {
        _e("Delete All URLs", FIFU_SLUG);
    };
    $fifu['title']['audio'] = function () {
        _e("Featured Audio", FIFU_SLUG);
    };
    $fifu['title']['debug'] = function () {
        _e("Debug Mode", FIFU_SLUG);
    };
    $fifu['title']['jetpack'] = function () {
        _e("Optimized Images", FIFU_SLUG);
    };
    $fifu['title']['api'] = function () {
        _e("WP / WooCommerce REST API", FIFU_SLUG);
    };
    $fifu['title']['shortcodes'] = function () {
        _e("FIFU Shortcode", FIFU_SLUG);
    };
    $fifu['title']['slider'] = function () {
        _e("Featured Slider", FIFU_SLUG);
    };
    $fifu['title']['bbpress'] = function () {
        _e("bbPress and BuddyBoss Platform", FIFU_SLUG);
    };
    $fifu['title']['taxonomy'] = function () {
        _e("Taxonomy Image", FIFU_SLUG);
    };
    $fifu['title']['video'] = function () {
        _e("Featured Video", FIFU_SLUG);
    };
    $fifu['title']['thumbnail'] = function () {
        _e("Video Thumbnail", FIFU_SLUG);
    };
    $fifu['title']['play'] = function () {
        _e("Play Button", FIFU_SLUG);
    };
    $fifu['title']['width'] = function () {
        _e("Minimum Width", FIFU_SLUG);
    };
    $fifu['title']['controls'] = function () {
        _e("Video Controls", FIFU_SLUG);
    };
    $fifu['title']['mouseover'] = function () {
        _e("Autoplay on Mouseover", FIFU_SLUG);
    };
    $fifu['title']['autoplay'] = function () {
        _e("Autoplay", FIFU_SLUG);
    };
    $fifu['title']['loop'] = function () {
        _e("Playback Loop", FIFU_SLUG);
    };
    $fifu['title']['mute'] = function () {
        _e("Mute", FIFU_SLUG);
    };
    $fifu['title']['background'] = function () {
        _e("Background Video", FIFU_SLUG);
    };
    $fifu['title']['privacy'] = function () {
        _e("Privacy Enhanced Mode", FIFU_SLUG);
    };
    $fifu['title']['later'] = function () {
        _e("Watch Later", FIFU_SLUG);
    };
    $fifu['title']['zoom'] = function () {
        _e("Lightbox and Zoom", FIFU_SLUG);
    };
    $fifu['title']['category'] = function () {
        _e("Auto set category images", FIFU_SLUG);
    };
    $fifu['title']['order-email'] = function () {
        _e("Add image to order email", FIFU_SLUG);
    };
    $fifu['title']['import'] = function () {
        _e("Import", FIFU_SLUG);
    };
    $fifu['title']['addon'] = function () {
        _e("Add-On", FIFU_SLUG);
    };
    $fifu['title']['activation'] = function () {
        _e("Activation", FIFU_SLUG);
    };
    $fifu['title']['gallery'] = function () {
        _e("FIFU Product Gallery", FIFU_SLUG);
    };
    $fifu['title']['buy'] = function () {
        _e("Quick Buy", FIFU_SLUG);
    };

    // support
    $fifu['support']['email'] = function () {
        _e("If you need help, refer to the troubleshooting or send an email to", FIFU_SLUG);
    };
    $fifu['support']['with'] = function () {
        _e("with this", FIFU_SLUG);
    };
    $fifu['support']['status'] = function () {
        _e("status", FIFU_SLUG);
    };
    $fifu['support']['disappeared'] = function () {
        _e("All images disappeared", FIFU_SLUG);
    };
    $fifu['support']['plugin'] = function () {
        _e("A plugin isn't working with FIFU", FIFU_SLUG);
    };
    $fifu['support']['facebook'] = function () {
        _e("Facebook doesn't share images", FIFU_SLUG);
    };
    $fifu['support']['money'] = function () {
        _e("Broken image icon", FIFU_SLUG);
    };
    $fifu['support']['resolution'] = function () {
        _e("Low-resolution or unduly cropped images", FIFU_SLUG);
    };
    $fifu['support']['disappeared-desc'] = function () {
        _e("You may solve it by: 1) accessing Metadata tab; 2) running Clear Metadata; 3) activating Image Metadata (~100,000 URLs/min); 4) clearing your cache (optional).", FIFU_SLUG);
    };
    $fifu['support']['plugin-desc'] = function () {
        _e("Contact us. If you are available to discuss the details, we should provide an integration. Or contact its developer and ask him to use the functions at 'Developers → FIFU API'.", FIFU_SLUG);
    };
    $fifu['support']['facebook-desc'] = function () {
        _e("You probably have a plugin or theme that sets a default image as the Facebook image (og:image meta tag). Just find and disable the option.", FIFU_SLUG);
    };
    $fifu['support']['money-desc'] = function () {
        _e("Possibilities: a) CDN can't serve it; b) image deleted by owner; c) hotlink protection; d) incorrect URL. For (a), disable 'Image → Optimized images' temporarily and contact us. For (b) or (c), try FIFU Cloud.", FIFU_SLUG);
    };
    $fifu['support']['resolution-desc'] = function () {
        _e("By default, the CDN loads images in the sizes registered by the theme or other plugins. You can adjust them at 'Image > Optimized images > Registered sizes'.", FIFU_SLUG);
    };
    $fifu['support']['wp-automatic'] = function () {
        _e("\"WP Automatic\" posts have no images", FIFU_SLUG);
    };
    $fifu['support']['media-library'] = function () {
        _e("Images saved in the media library", FIFU_SLUG);
    };
    $fifu['support']['others'] = function () {
        _e("Changes have no effect", FIFU_SLUG);
    };
    $fifu['support']['wp-automatic-desc'] = function () {
        _e("Notify WP Automatic (or WPeMatico) support that image URLs are not sent to FIFU. Alternatively, use our PRO version to obtain images using page addresses or post titles.", FIFU_SLUG);
    };
    $fifu['support']['media-library-desc'] = function () {
        _e("This plugin is unable to save images to the media library unless you're using the \"Save in the media library\" feature. Another plugin or your theme may be causing this.", FIFU_SLUG);
    };
    $fifu['support']['others-desc'] = function () {
        _e("If you're using a performance plugin, clearing the cache may help. This ensures that backend changes propagate effectively to the frontend.", FIFU_SLUG);
    };

    // start
    $fifu['start']['url']['external'] = function () {
        _e("Hi, I'm a REMOTE image!", FIFU_SLUG);
    };
    $fifu['start']['url']['not'] = function () {
        _e("It means I'm NOT in your media library.", FIFU_SLUG);
    };
    $fifu['start']['url']['url'] = function () {
        _e("Don't you believe me? So why don't you check my Internet address (also known as URL)?", FIFU_SLUG);
    };
    $fifu['start']['url']['right'] = function () {
        _e("1. Right-click on me now", FIFU_SLUG);
    };
    $fifu['start']['url']['copy'] = function () {
        _e("2. Select \"Copy image address\"", FIFU_SLUG);
    };
    $fifu['start']['url']['paste'] = function () {
        _e("3. Paste it here:", FIFU_SLUG);
    };
    $fifu['start']['url']['drag'] = function () {
        _e("Or just drag me and drop me here", FIFU_SLUG);
    };
    $fifu['start']['url']['click'] = function () {
        _e("Right click me!", FIFU_SLUG);
    };
    $fifu['start']['post']['famous'] = function () {
        _e("Now that you have my address (also known as URL), how about making me famous?", FIFU_SLUG);
    };
    $fifu['start']['post']['create'] = function () {
        _e("You just need to create a post and use me as the featured image:", FIFU_SLUG);
    };
    $fifu['start']['post']['new'] = function () {
        _e("1. Add a new post", FIFU_SLUG);
    };
    $fifu['start']['post']['box'] = function () {
        _e("2. Find the box", FIFU_SLUG);
    };
    $fifu['start']['post']['featured'] = function () {
        _e("Featured image", FIFU_SLUG);
    };
    $fifu['start']['post']['address'] = function () {
        _e("3. Paste my address into \"Image URL\" field.", FIFU_SLUG);
    };
    $fifu['start']['post']['storage'] = function () {
        _e("And don't worry about storage. I will remain REMOTE. I will NOT be uploaded to your media library.", FIFU_SLUG);
    };

    // dev
    $fifu['dev']['function'] = function () {
        _e("Are you a WordPress developer? Now you can easily integrate your code with FIFU using the functions below.", FIFU_SLUG);
    };
    $fifu['dev']['args'] = function () {
        _e("All you need is to provide the post ID and the image URL(s). FIFU plugin will handle the rest by setting the custom fields and creating the metadata.", FIFU_SLUG);
    };
    $fifu['dev']['field']['image'] = function () {
        _e("Featured image", FIFU_SLUG);
    };
    $fifu['dev']['field']['video'] = function () {
        _e("Featured video", FIFU_SLUG);
    };
    $fifu['dev']['field']['slider'] = function () {
        _e("Featured slider", FIFU_SLUG);
    };
    $fifu['dev']['field']['product']['image'] = function () {
        _e("Product image", FIFU_SLUG);
    };
    $fifu['dev']['field']['product']['video'] = function () {
        _e("Product video", FIFU_SLUG);
    };
    $fifu['dev']['field']['gallery']['image'] = function () {
        _e("Image gallery", FIFU_SLUG);
    };
    $fifu['dev']['field']['gallery']['video'] = function () {
        _e("Video gallery", FIFU_SLUG);
    };
    $fifu['dev']['field']['category']['image'] = function () {
        _e("Product category image", FIFU_SLUG);
    };
    $fifu['dev']['field']['category']['video'] = function () {
        _e("Product category video", FIFU_SLUG);
    };

    // cli
    $fifu['cli']['desc'] = function () {
        _e("Configure FIFU via command line.", FIFU_SLUG);
    };
    $fifu['cli']['tab']['commands'] = function () {
        _e("FIFU commands", FIFU_SLUG);
    };
    $fifu['cli']['tab']['fields'] = function () {
        _e("FIFU custom fields", FIFU_SLUG);
    };
    $fifu['cli']['documentation']['site'] = function () {
        _e("WP-CLI", FIFU_SLUG);
    };
    $fifu['cli']['fields']['create'] = function () {
        _e("Create a post", FIFU_SLUG);
    };
    $fifu['cli']['fields']['api'] = function () {
        _e("Other custom fields can be found under Settings → Developers → REST API → Custom fields.", FIFU_SLUG);
    };
    $fifu['cli']['column']['tab'] = function () {
        _e("Tab", FIFU_SLUG);
    };
    $fifu['cli']['column']['section'] = function () {
        _e("Section", FIFU_SLUG);
    };
    $fifu['cli']['column']['feature'] = function () {
        _e("Feature", FIFU_SLUG);
    };
    $fifu['cli']['column']['option'] = function () {
        _e("Option", FIFU_SLUG);
    };
    $fifu['cli']['column']['command'] = function () {
        _e("Command", FIFU_SLUG);
    };
    $fifu['cli']['column']['eg'] = function () {
        _e("e.g. (args)", FIFU_SLUG);
    };
    $fifu['cli']['column']['action'] = function () {
        _e("Action", FIFU_SLUG);
    };
    $fifu['cli']['column']['example'] = function () {
        _e("Command example", FIFU_SLUG);
    };

    // reset
    $fifu['reset']['desc'] = function () {
        _e("Reset FIFU settings to the default values.", FIFU_SLUG);
    };
    $fifu['reset']['reset'] = function () {
        _e("reset settings", FIFU_SLUG);
    };

    // media library
    $fifu['media']['desc'] = function () {
        _e("It's possible to save remote images in the media library and automatically set them as standard WordPress/WooCommerce featured images or gallery images. Make a backup before running the scheduled event, as making an image local cannot be reverted.", FIFU_SLUG);
    };
    $fifu['media']['upload'] = function () {
        _e("show upload button on post editor", FIFU_SLUG);
    };
    $fifu['media']['job'] = function () {
        _e("run a function that periodically searches for remote images and saves them in the media library.", FIFU_SLUG);
    };
    $fifu['media']['tab']['main'] = function () {
        _e("Main", FIFU_SLUG);
    };
    $fifu['media']['tab']['domain'] = function () {
        _e("Domain filter", FIFU_SLUG);
    };
    $fifu['media']['tab']['proxy'] = function () {
        _e("Proxy", FIFU_SLUG);
    };
    $fifu['media']['proxy']['desc'] = function () {
        _e("Proxies are utilized to bypass IP bans that can occur when your site downloads a large number of images from the same host. However, using proxies has certain disadvantages. A proxy can impose limitations on the number of requests, and its IP address may also get banned. In such cases, the plugin will attempt to find alternative proxies until it discovers a functional one. Consequently, the use of proxies can potentially slow down the process, unless you have access to a private proxy. By default, FIFU plugin incorporates a list of public proxies that are updated every 30 minutes. The plugin also caches the proxies that are working correctly with your image URLs.", FIFU_SLUG);
    };
    $fifu['media']['proxy']['toggle'] = function () {
        _e("use proxies to intermediate the traffic between your site and the image hosts", FIFU_SLUG);
    };
    $fifu['media']['proxy']['private'] = function () {
        _e("Private proxy", FIFU_SLUG);
    };
    $fifu['media']['proxy']['placeholder'] = function () {
        _e("192.168.0.1:80, 127.0.0.1:8080, username:password@172.16.0.0:443", FIFU_SLUG);
    };
    $fifu['media']['domain']['desc'] = function () {
        _e("Only save images from specific domains.", FIFU_SLUG);
    };

    // auto set
    $fifu['auto']['desc'] = function () {
        _e("Set featured images automatically. The plugin checks every minute for post types without featured images and performs web searches based on post titles to retrieve the image URLs (1 per minute).", FIFU_SLUG);
    };
    $fifu['auto']['important2'] = function () {
        _e("Don't restrict the search too much. Because depending on the post title and the filters applied, the search engine might return an image that's not very relevant, or even no image at all. A quick and easy way to test if the search engine has relevant images for the applied filters is by accessing the post editor or FIFU quick editor and performing a search with the 'Keywords' field empty. A list of the top images based on the post's title will be displayed.", FIFU_SLUG);
    };
    $fifu['auto']['tab']['auto'] = function () {
        _e("Auto set", FIFU_SLUG);
    };
    $fifu['auto']['tab']['search'] = function () {
        _e("Search filters", FIFU_SLUG);
    };
    $fifu['auto']['tab']['credits'] = function () {
        _e("Credits", FIFU_SLUG);
    };
    $fifu['auto']['tab']['filters'] = function () {
        _e("Size filter", FIFU_SLUG);
    };
    $fifu['auto']['tab']['blocklist'] = function () {
        _e("Blocklist", FIFU_SLUG);
    };
    $fifu['auto']['tab']['cpt'] = function () {
        _e("Post types", FIFU_SLUG);
    };
    $fifu['auto']['tab']['source'] = function () {
        _e("Source filter", FIFU_SLUG);
    };
    $fifu['auto']['tab']['layout'] = function () {
        _e("Layout filter", FIFU_SLUG);
    };
    $fifu['auto']['filter']['width'] = function () {
        _e("minimum width (px)", FIFU_SLUG);
    };
    $fifu['auto']['filter']['height'] = function () {
        _e("minimum height (px)", FIFU_SLUG);
    };
    $fifu['auto']['filter']['blocklist'] = function () {
        _e("List of prohibited strings in the image URL:", FIFU_SLUG);
    };
    $fifu['auto']['cpt']['desc'] = function () {
        _e("This feature is pre-configured to work only with the post type \"post.\" You can include more post types below (separated by commas).", FIFU_SLUG);
    };
    $fifu['auto']['cpt']['found'] = function () {
        _e("Post types found on your site:", FIFU_SLUG);
    };
    $fifu['auto']['source']['desc'] = function () {
        _e("Limit the search to one or more specific sites.", FIFU_SLUG);
    };
    $fifu['auto']['layout']['all'] = function () {
        _e("All", FIFU_SLUG);
    };
    $fifu['auto']['layout']['square'] = function () {
        _e("Square", FIFU_SLUG);
    };
    $fifu['auto']['layout']['tall'] = function () {
        _e("Tall", FIFU_SLUG);
    };
    $fifu['auto']['layout']['wide'] = function () {
        _e("Wide", FIFU_SLUG);
    };
    $fifu['auto']['credits']['desc'] = function () {
        _e("When FIFU imports an image URL from the search engine, it also retrieves the address of the remote post that owns the image. By enabling 'Settings → Image → Page Redirection', FIFU adds a link to the image when it’s displayed on singular posts of your site. Then, by clicking on the image, the visitor is redirected to the remote post. This is FIFU’s way of giving credit to the author, since the search engine may include copyrighted images in the results.
", FIFU_SLUG);
    };

    // isbn
    $fifu['isbn']['desc'] = function () {
        _e("Set featured images automatically. The plugin checks every minute for post types without featured images and performs web searches based on ISBN to retrieve the book cover URLs.", FIFU_SLUG);
    };
    $fifu['isbn']['tab']['auto'] = function () {
        _e("Auto set", FIFU_SLUG);
    };
    $fifu['isbn']['tab']['custom'] = function () {
        _e("Custom field", FIFU_SLUG);
    };
    $fifu['isbn']['custom']['desc'] = function () {
        _e("If you already have the ISBN saved in your database, specify its custom field name here. The plugin will access that and import the value. For example, if the ISBN is saved in the SKU field, you can add \"_sku,\" which is the field where the SKU is stored.", FIFU_SLUG);
    };

    // asin
    $fifu['asin']['desc'] = function () {
        _e("Set product images automatically. The plugin checks every minute for post types without images and uses the ASIN and Amazon's Product Advertising API to retrieve the image URLs. For WooCommerce products, the URLs will be saved as the featured image and gallery, while for other post types, the URLs will be saved as a featured slider.", FIFU_SLUG);
    };
    $fifu['asin']['tab']['auto'] = function () {
        _e("Auto set", FIFU_SLUG);
    };
    $fifu['asin']['tab']['custom'] = function () {
        _e("Custom field", FIFU_SLUG);
    };
    $fifu['asin']['tab']['credentials'] = function () {
        _e("Credentials", FIFU_SLUG);
    };
    $fifu['asin']['custom']['desc'] = function () {
        _e("If you already have the ASIN saved in your database, specify its custom field name here. The plugin will access that and import the value. For example, if the ASIN is saved in the SKU field, you can add \"_sku,\" which is the field where the SKU is stored.", FIFU_SLUG);
    };
    $fifu['asin']['label']['partner'] = function () {
        return _e("Partner tag", FIFU_SLUG);
    };
    $fifu['asin']['label']['access'] = function () {
        return _e("Access key", FIFU_SLUG);
    };
    $fifu['asin']['label']['secret'] = function () {
        return _e("Secret key", FIFU_SLUG);
    };
    $fifu['asin']['label']['locale'] = function () {
        return _e("Locale", FIFU_SLUG);
    };

    // customfield
    $fifu['customfield']['desc'] = function () {
        _e("Set featured media automatically. The plugin checks every minute for post types without featured images or videos and performs searches on the informed custom fields to retrieve the URLs. With that, you can integrate FIFU with any third-party plugin or theme that stores URLs in the database.", FIFU_SLUG);
    };
    $fifu['customfield']['prefix'] = function () {
        _e("Most users will simply add the custom field name, which is expected to contain an image or video URL. However, if all your URLs follow the same pattern, i.e., have the same prefix and suffix, you could add something like this into the field above: https://domain/{custom_field}.webp, where custom_field is some kind of ID instead of a URL.", FIFU_SLUG);
    };
    $fifu['customfield']['tab']['auto'] = function () {
        _e("Auto set", FIFU_SLUG);
    };
    $fifu['customfield']['tab']['custom'] = function () {
        _e("Custom field", FIFU_SLUG);
    };

    // screenshot
    $fifu['screenshot']['desc'] = function () {
        _e("To use a website screenshot as the featured image of a post, add \"https://screenshot.fifu.app/\" before the website address. Then use this new address as the image URL. For example, if you want the screenshot of the website \"https://openai.com/\" as the featured image of a post, your image URL should be: https://screenshot.fifu.app/https://openai.com/", FIFU_SLUG);
    };
    $fifu['screenshot']['desc3'] = function () {
        _e("If the GIF \"Generating Preview\" appears, ignore it. It means the screenshot was not found in the cache but is being generated on the server. You can save the post and exit the editor. The screenshot will be ready in a few seconds. Refresh the page.", FIFU_SLUG);
    };
    $fifu['screenshot']['custom']['desc'] = function () {
        _e("If you have the web page address saved in your database, please specify its custom field name here. The plugin will access it, generate the screenshot URL, and set that as the featured image automatically.", FIFU_SLUG);
    };
    $fifu['screenshot']['tab']['auto'] = function () {
        _e("Auto set", FIFU_SLUG);
    };
    $fifu['screenshot']['tab']['custom'] = function () {
        _e("Custom field", FIFU_SLUG);
    };
    $fifu['screenshot']['tab']['size'] = function () {
        _e("Size", FIFU_SLUG);
    };

    // find
    $fifu['finder']['desc'] = function () {
        _e("Automatically defines featured media using images found on remote web pages. The plugin checks every minute for post types without featured media and accesses the provided web page URLs to retrieve the main image. FIFU looks for the Open Graph tag image (used for sharing on social media). If og:image is not found, it retrieves the largest image available. It can also search for embedded videos and set the first one found as the featured video. Videos take priority over images.", FIFU_SLUG);
    };
    $fifu['finder']['auto'] = function () {
        _e("auto set featured media using web page address", FIFU_SLUG);
    };
    $fifu['finder']['video'] = function () {
        _e("look for embedded videos", FIFU_SLUG);
    };
    $fifu['finder']['tab']['auto'] = function () {
        _e("Auto set", FIFU_SLUG);
    };
    $fifu['finder']['tab']['custom'] = function () {
        _e("Custom field", FIFU_SLUG);
    };
    $fifu['finder']['amazon']['gallery'] = function () {
        _e("set gallery images and videos", FIFU_SLUG);
    };
    $fifu['finder']['custom']['desc'] = function () {
        _e("If you already have the web page address saved in your database, specify its custom field name here. The plugin will access that and import the value. For example, if the web page URL is saved in the Product URL field, you can add \"_product_url,\" which is the field where the remote URL to the product is stored. For posts created by \"WordPress Automatic Plugin\", add \"original_link\".", FIFU_SLUG);
    };

    // tags
    $fifu['tags']['desc'] = function () {
        _e("Set images from Unsplash as featured images automatically. The plugin checks every minute for post types without featured images and performs searches on Unsplash based on the tags to retrieve the image URLs.", FIFU_SLUG);
    };
    $fifu['tags']['tab']['auto'] = function () {
        _e("Auto set", FIFU_SLUG);
    };
    $fifu['tags']['tab']['orientation'] = function () {
        _e("Orientation filter", FIFU_SLUG);
    };
    $fifu['tags']['orientation']['all'] = function () {
        _e("All", FIFU_SLUG);
    };
    $fifu['tags']['orientation']['landscape'] = function () {
        _e("Landscape", FIFU_SLUG);
    };
    $fifu['tags']['orientation']['portrait'] = function () {
        _e("Portrait", FIFU_SLUG);
    };

    // block
    $fifu['block']['desc'] = function () {
        _e("Disable right-click on all images.", FIFU_SLUG);
    };

    // popup
    $fifu['popup']['desc'] = function () {
        _e("Adds a new box to the post editor where you can paste any embed code. Its content will be displayed in a popup when the visitor clicks on the featured image. Here's an example of a popup containing a TikTok embed code:", FIFU_SLUG);
    };

    // redirection
    $fifu['redirection']['desc'] = function () {
        _e("Adds a new box in the post editor where you can specify a forwarding URL. When accessing a post and clicking on the featured image, the user will be redirected to the specified address.", FIFU_SLUG);
    };

    // replace
    $fifu['replace']['desc'] = function () {
        _e("Define the URL of an image to be displayed in case of an image not found error.", FIFU_SLUG);
    };

    // default
    $fifu['default']['desc'] = function () {
        _e("Define the URL of a default image to be displayed when you create or update a post without a featured image.", FIFU_SLUG);
    };
    $fifu['default']['tab']['url'] = function () {
        _e("Default featured image", FIFU_SLUG);
    };
    $fifu['default']['tab']['cpt'] = function () {
        _e("Post type filter", FIFU_SLUG);
    };
    $fifu['default']['cpt']['found'] = function () {
        _e("Post types found on your site:", FIFU_SLUG);
    };
    $fifu['default']['cpt']['info'] = function () {
        _e("After adding or removing a post type, you need to restart the feature by disabling and enabling the toggle below.", FIFU_SLUG);
    };
    $fifu['default']['placeholder']['url'] = function () {
        _e("Image URL", FIFU_SLUG);
    };

    // pcontent
    $fifu['pcontent']['desc'] = function () {
        _e("Post content refers to the main body of text within a post. It comes after the post title and doesn't include the featured media (image or video). However, with this feature, you can add the featured media to the top of the content, which may be useful when the theme doesn't render the featured media on single posts. You can also remove media from the content, which is useful when the same media is defined as the featured media, causing unwanted duplication. These changes are dynamic, happening only on page load, so they won't change the database and can be easily undone.", FIFU_SLUG);
    };
    $fifu['pcontent']['tab']['modify'] = function () {
        _e("Modify post content", FIFU_SLUG);
    };
    $fifu['pcontent']['tab']['type'] = function () {
        _e("Post type filter", FIFU_SLUG);
    };
    $fifu['pcontent']['option']['add'] = function () {
        _e("add missing media", FIFU_SLUG);
    };
    $fifu['pcontent']['option']['remove'] = function () {
        _e("remove duplicated media", FIFU_SLUG);
    };
    $fifu['pcontent']['type']['found'] = function () {
        _e("Apply the feature to specific post types:", FIFU_SLUG);
    };

    // hide
    $fifu['hide']['desc'] = function () {
        _e("Hide the featured media (image, video, or slider) on posts but keep its visibility on the homepage.", FIFU_SLUG);
    };
    $fifu['hide']['tab']['hide'] = function () {
        _e("Hide", FIFU_SLUG);
    };
    $fifu['hide']['tab']['cpt'] = function () {
        _e("Post type filter", FIFU_SLUG);
    };
    $fifu['hide']['tab']['format'] = function () {
        _e("Post format filter", FIFU_SLUG);
    };
    $fifu['hide']['type']['apply'] = function () {
        _e("Apply the feature to specific post formats:  ", FIFU_SLUG);
    };
    $fifu['hide']['format']['found'] = function () {
        _e("Post formats found on your site:", FIFU_SLUG);
    };

    // configuration
    $fifu['html']['desc'] = function () {
        _e("Set featured media automatically. The plugin reads the HTML of your post content and uses the first image or video found as the featured media when the post is created or updated.", FIFU_SLUG);
    };
    $fifu['html']['tab']['auto'] = function () {
        _e("Auto set", FIFU_SLUG);
    };
    $fifu['html']['tab']['all'] = function () {
        _e("Run it for all posts", FIFU_SLUG);
    };
    $fifu['html']['tab']['source'] = function () {
        _e("Source filter", FIFU_SLUG);
    };
    $fifu['html']['tab']['type'] = function () {
        _e("Post type filter", FIFU_SLUG);
    };
    $fifu['html']['tab']['media'] = function () {
        _e("Media type filter", FIFU_SLUG);
    };
    $fifu['html']['first'] = function () {
        _e("auto set featured media from post content", FIFU_SLUG);
    };
    $fifu['html']['overwrite'] = function () {
        _e("overwrite the existing featured media", FIFU_SLUG);
    };
    $fifu['html']['skip']['desc'] = function () {
        _e("Skip URLs containing these keywords:", FIFU_SLUG);
    };
    $fifu['html']['media']['image'] = function () {
        _e("Image", FIFU_SLUG);
    };
    $fifu['html']['media']['video'] = function () {
        _e("Video", FIFU_SLUG);
    };
    $fifu['html']['media']['all'] = function () {
        _e("All", FIFU_SLUG);
    };

    // all
    $fifu['all']['desc'] = function () {
        _e("Update all your posts applying the above configuration. To repeat the process, enable the toggle again.", FIFU_SLUG);
    };
    $fifu['all']['update'] = function () {
        _e("run it for each post, only once, and right now", FIFU_SLUG);
    };

    // metadata
    $fifu['metadata']['desc'] = function () {
        _e("Generate the necessary database records for WordPress components to work with remote images.", FIFU_SLUG);
    };
    $fifu['metadata']['generate'] = function () {
        _e("generate the missing metadata now", FIFU_SLUG);
    };

    // clear
    $fifu['clean']['desc'] = function () {
        _e("Clear the Image Metadata generated by FIFU, but not the URLs. Run this function if you intend to deactivate the plugin and use only local featured images again.", FIFU_SLUG);
    };
    $fifu['clean']['disabled'] = function () {
        _e("the toggle will be automatically disabled when finished", FIFU_SLUG);
    };

    // schedule
    $fifu['schedule']['desc'] = function () {
        _e("If you are setting the image URLs in a non-standard way, the images may not be displayed to visitors because additional metadata is required. Here, you can schedule an event to run every minute and check for image URLs without metadata and generate it.", FIFU_SLUG);
    };

    // delete
    $fifu['delete']['important'] = function () {
        _e("this plugin doesn't save images in the media library. Enabling this toggle will remove all featured images from post types that have remote featured images, and this action cannot be undone. This also applies to FIFU galleries, videos, audios, and sliders.", FIFU_SLUG);
    };
    $fifu['delete']['now'] = function () {
        _e("delete all your URLs now", FIFU_SLUG);
    };
    $fifu['delete']['requirement'] = function () {
        _e("Requirement: Go to Plugins → Plugin Editor → Select plugin to edit → Featured Image from URL → Select. Then change the value of FIFU_DELETE_ALL_URLS from false to true.", FIFU_SLUG);
    };

    // jetpack
    $fifu['jetpack']['tab']['optimize'] = function () {
        _e("Optimize", FIFU_SLUG);
    };
    $fifu['jetpack']['tab']['sizes'] = function () {
        _e("Registered sizes", FIFU_SLUG);
    };
    $fifu['jetpack']['tab']['fifu'] = function () {
        _e("FIFU CDN", FIFU_SLUG);
    };
    $fifu['jetpack']['desc'] = function () {
        _e("Your remote images will be automatically optimized and served from a public CDN. In addition, the plugin will load the thumbnails in the exact size your site requires, further enhancing performance.", FIFU_SLUG);
    };
    $fifu['jetpack']['toggle']['cdn'] = function () {
        _e("optimize featured images", FIFU_SLUG);
    };
    $fifu['jetpack']['toggle']['content'] = function () {
        _e("optimize secondary images", FIFU_SLUG);
    };
    $fifu['jetpack']['toggle']['otfcdn'] = function () {
        _e("optimize images with FIFU CDN", FIFU_SLUG);
    };
    $fifu['jetpack']['toggle']['domain'] = function () {
        _e("use your site's domain", FIFU_SLUG);
    };
    $fifu['jetpack']['toggle']['square'] = function () {
        _e("square all images", FIFU_SLUG);
    };
    $fifu['jetpack']['sizes']['desc'] = function () {
        _e("Registered sizes are predefined dimensions that themes and plugins create to display images, and the FIFU CDN reads these values to deliver remote images in the exact requested size.", FIFU_SLUG);
    };
    $fifu['jetpack']['sizes']['reset'] = function () {
        _e("The sizes listed here are automatically detected by FIFU during page load. So, if you need to reset the size values for any reason, simply navigate through your pages again or wait for your visitors to do so, and FIFU will detect and list them here once more.", FIFU_SLUG);
    };
    $fifu['jetpack']['otfcdn']['desc'] = function () {
        _e("For a long time, the 'Optimized Images' feature relied solely on a public third-party CDN. While this approach offered cost advantages, it also introduced several challenges. Now, FIFU provides its own CDN, designed to achieve the following goals:", FIFU_SLUG);
    };
    $fifu['jetpack']['otfcdn']['goal']['domain'] = function () {
        _e("Serve remote images using your site's domain", FIFU_SLUG);
    };
    $fifu['jetpack']['otfcdn']['goal']['source'] = function () {
        _e("Support any image sources", FIFU_SLUG);
    };
    $fifu['jetpack']['otfcdn']['goal']['format'] = function () {
        _e("Render images in a modern and efficient web format (WebP)", FIFU_SLUG);
    };
    $fifu['jetpack']['otfcdn']['goal']['time'] = function () {
        _e("Retrieve cached images in under 100ms", FIFU_SLUG);
    };
    $fifu['jetpack']['otfcdn']['goal']['first'] = function () {
        _e("Provide first-request images (non-cached) in less than 1 second", FIFU_SLUG);
    };
    $fifu['jetpack']['otfcdn']['goal']['smart'] = function () {
        _e("Smart crop images", FIFU_SLUG);
    };
    $fifu['jetpack']['otfcdn']['goal']['process'] = function () {
        _e("Expand image processing capabilities", FIFU_SLUG);
    };
    $fifu['jetpack']['otfcdn']['goal']['seo'] = function () {
        _e("Optimize image URLs for SEO", FIFU_SLUG);
    };
    $fifu['jetpack']['otfcdn']['goal']['replication'] = function () {
        _e("Enhance content replication", FIFU_SLUG);
    };
    $fifu['jetpack']['otfcdn']['goal']['regional'] = function () {
        _e("Improve regional availability", FIFU_SLUG);
    };
    $fifu['jetpack']['otfcdn']['goal']['cache'] = function () {
        _e("Enable cache purging", FIFU_SLUG);
    };
    $fifu['jetpack']['otfcdn']['goal']['support'] = function () {
        _e("Provide dedicated technical support", FIFU_SLUG);
    };
    $fifu['jetpack']['otfcdn']['goal']['replace'] = function () {
        _e("Seamlessly replace or integrate with other features", FIFU_SLUG);
    };
    $fifu['jetpack']['otfcdn']['cost'] = function () {
        _e("Due to the costs associated with processing and serving millions of images daily, this feature is not currently available in the free version of FIFU or on staging sites using the PRO version. However, a small fee may soon be introduced to ensure its sustainability. For PRO users, the FIFU CDN will initially work only with the production site, unless the key has expired.", FIFU_SLUG);
    };
    $fifu['jetpack']['otfcdn']['setup'] = function () {
        _e("To serve remote images with your site domain, simply create a DNS record for your domain with the following details:", FIFU_SLUG);
    };

    // audio
    $fifu['audio']['desc'] = function () {
        _e("This feature enables the featured audio field, where you can set the URL of an audio file, such as MP3 or OGG. Player controls will then be added to the remote featured image, allowing visitors to play the audio. You can configure the behavior of the audio using the settings available in the 'Featured Video' tab.", FIFU_SLUG);
    };
    $fifu['audio']['requirement'] = function () {
        _e("you must set a remote featured image as well.", FIFU_SLUG);
    };

    // debug
    $fifu['debug']['desc'] = function () {
        _e("When FIFU is in debug mode, JavaScript and CSS files are not cached.", FIFU_SLUG);
    };
    $fifu['debug']['tables'] = function () {
        _e("It also allows the development team to read a few entries in the database that are related to images.", FIFU_SLUG);
    };

    // api
    $fifu['api']['tab']['endpoints'] = function () {
        _e("Endpoints", FIFU_SLUG);
    };
    $fifu['api']['tab']['custom'] = function () {
        _e("FIFU custom fields", FIFU_SLUG);
    };
    $fifu['api']['tab']['product'] = function () {
        _e("Creating your first product", FIFU_SLUG);
    };
    $fifu['api']['tab']['category'] = function () {
        _e("product category", FIFU_SLUG);
    };
    $fifu['api']['tab']['variable'] = function () {
        _e("variable product", FIFU_SLUG);
    };
    $fifu['api']['tab']['variation'] = function () {
        _e("product variation", FIFU_SLUG);
    };
    $fifu['api']['tab']['batch-product'] = function () {
        _e("batch of products", FIFU_SLUG);
    };
    $fifu['api']['tab']['batch-category'] = function () {
        _e("batch of categories", FIFU_SLUG);
    };
    $fifu['api']['tab']['post'] = function () {
        _e("WordPress post", FIFU_SLUG);
    };
    $fifu['api']['tab']['documentation'] = function () {
        _e("Documentation", FIFU_SLUG);
    };
    $fifu['api']['endpoint']['product'] = function () {
        _e("Product", FIFU_SLUG);
    };
    $fifu['api']['endpoint']['category'] = function () {
        _e("Product category", FIFU_SLUG);
    };
    $fifu['api']['endpoint']['variation'] = function () {
        _e("Product variation", FIFU_SLUG);
    };
    $fifu['api']['endpoint']['batch-product'] = function () {
        _e("Batch of products", FIFU_SLUG);
    };
    $fifu['api']['endpoint']['batch-category'] = function () {
        _e("Batch of categories", FIFU_SLUG);
    };
    $fifu['api']['endpoint']['post'] = function () {
        _e("WordPress post", FIFU_SLUG);
    };
    $fifu['api']['endpoint']['cpt'] = function () {
        _e("Custom post type", FIFU_SLUG);
    };
    $fifu['api']['custom']['image'] = function () {
        _e("Image", FIFU_SLUG);
    };
    $fifu['api']['custom']['title'] = function () {
        _e("Image title", FIFU_SLUG);
    };
    $fifu['api']['custom']['images'] = function () {
        _e("Product image + gallery", FIFU_SLUG);
    };
    $fifu['api']['custom']['titles'] = function () {
        _e("Product image title + gallery", FIFU_SLUG);
    };
    $fifu['api']['custom']['video'] = function () {
        _e("Video", FIFU_SLUG);
    };
    $fifu['api']['custom']['videos'] = function () {
        _e("Product video + gallery", FIFU_SLUG);
    };
    $fifu['api']['custom']['slider'] = function () {
        _e("Slider", FIFU_SLUG);
    };
    $fifu['api']['custom']['isbn'] = function () {
        _e("ISBN", FIFU_SLUG);
    };
    $fifu['api']['custom']['asin'] = function () {
        _e("ASIN", FIFU_SLUG);
    };
    $fifu['api']['custom']['finder'] = function () {
        _e("Media finder (webpage URL)", FIFU_SLUG);
    };
    $fifu['api']['custom']['redirection'] = function () {
        _e("Page redirection (forwarding URL)", FIFU_SLUG);
    };
    $fifu['api']['custom']['key'] = function () {
        _e("Key", FIFU_SLUG);
    };
    $fifu['api']['documentation']['wordpress'] = function () {
        _e("WordPress REST API", FIFU_SLUG);
    };
    $fifu['api']['documentation']['woocommerce'] = function () {
        _e("WooCommerce REST API", FIFU_SLUG);
    };

    // FIFU shortcodes
    $fifu['shortcodes']['desc'] = function () {
        _e("Add FIFU elements anywhere with a shortcode.", FIFU_SLUG);
    };
    $fifu['shortcodes']['tab']['shortcodes'] = function () {
        _e("Display media", FIFU_SLUG);
    };
    $fifu['shortcodes']['tab']['edition'] = function () {
        _e("Display form", FIFU_SLUG);
    };
    $fifu['shortcodes']['column']['shortcode'] = function () {
        _e("Shortcode", FIFU_SLUG);
    };
    $fifu['shortcodes']['column']['description'] = function () {
        _e("Description", FIFU_SLUG);
    };
    $fifu['shortcodes']['column']['optional'] = function () {
        _e("Optional parameters", FIFU_SLUG);
    };
    $fifu['shortcodes']['column']['required'] = function () {
        _e("Required parameters", FIFU_SLUG);
    };
    $fifu['shortcodes']['description']['fifu'] = function () {
        _e("Displays the featured image/video", FIFU_SLUG);
    };
    $fifu['shortcodes']['description']['slider'] = function () {
        _e("Displays the featured slider", FIFU_SLUG);
    };
    $fifu['shortcodes']['description']['gallery'] = function () {
        _e("Displays the product gallery", FIFU_SLUG);
    };
    $fifu['shortcodes']['description']['taxonomy'] = function () {
        _e("Displays the taxonomy image", FIFU_SLUG);
    };
    $fifu['shortcodes']['description']['form']['image'] = function () {
        _e("Input field for featured image URL", FIFU_SLUG);
    };

    // slider
    $fifu['slider']['desc'] = function () {
        _e("This feature allows to have a slider of images and/or videos instead of a regular featured image. It is particularly useful for certain types of websites, such as real estate, and can handle a large number of high-resolution images with optimal performance.", FIFU_SLUG);
    };
    $fifu['slider']['tab']['configuration'] = function () {
        _e("Configuration", FIFU_SLUG);
    };
    $fifu['slider']['featured'] = function () {
        _e("Featured slider", FIFU_SLUG);
    };
    $fifu['slider']['pause'] = function () {
        _e("Pause autoplay on hover", FIFU_SLUG);
    };
    $fifu['slider']['buttons'] = function () {
        _e("Show previous/next buttons", FIFU_SLUG);
    };
    $fifu['slider']['start'] = function () {
        _e("Start autoplay automatically", FIFU_SLUG);
    };
    $fifu['slider']['click'] = function () {
        _e("Show gallery on click", FIFU_SLUG);
    };
    $fifu['slider']['thumb'] = function () {
        _e("Show thumbnail gallery", FIFU_SLUG);
    };
    $fifu['slider']['counter'] = function () {
        _e("Show counter", FIFU_SLUG);
    };
    $fifu['slider']['crop'] = function () {
        _e("Display images at the same height", FIFU_SLUG);
    };
    $fifu['slider']['single'] = function () {
        _e("Load slider on singular post types only", FIFU_SLUG);
    };
    $fifu['slider']['vertical'] = function () {
        _e("Vertical mode", FIFU_SLUG);
    };
    $fifu['slider']['time'] = function () {
        _e("Time between each transition (in ms)", FIFU_SLUG);
    };
    $fifu['slider']['duration'] = function () {
        _e("Transition duration (in ms)", FIFU_SLUG);
    };
    $fifu['slider']['left'] = function () {
        _e("Previous button", FIFU_SLUG);
    };
    $fifu['slider']['right'] = function () {
        _e("Next button", FIFU_SLUG);
    };
    $fifu['slider']['optional'] = function () {
        _e("Image URL (optional)", FIFU_SLUG);
    };

    // quick buy
    $fifu['buy']['enable'] = function () {
        _e("Quick buy", FIFU_SLUG);
    };
    $fifu['buy']['text']['text'] = function () {
        _e("Text button", FIFU_SLUG);
    };
    $fifu['buy']['disclaimer']['text'] = function () {
        _e("Disclaimer", FIFU_SLUG);
    };
    $fifu['buy']['cf']['text'] = function () {
        _e("Custom field", FIFU_SLUG);
    };
    $fifu['buy']['text']['placeholder'] = function () {
        _e("Buy now (optional)", FIFU_SLUG);
    };
    $fifu['buy']['disclaimer']['placeholder'] = function () {
        _e("Disclaimer (optional)", FIFU_SLUG);
    };
    $fifu['buy']['cf']['placeholder'] = function () {
        _e("Custom field name (optional)", FIFU_SLUG);
    };

    // bbpress
    $fifu['bbpress']['desc'] = function () {
        _e("Enable the addition of featured images to forums, topics, and replies.", FIFU_SLUG);
    };

    // taxonomy
    $fifu['taxonomy']['desc'] = function () {
        _e("Enables the featured image field for taxonomies. It's automatically integrated with the 'Variation Swatches for WooCommerce' plugin. To display the image for taxonomies from different plugins or themes, use a shortcode like this:", FIFU_SLUG);
    };

    // video
    $fifu['video']['desc'] = function () {
        _e("FIFU supports videos and audios from YouTube, Vimeo, Twitter, 9GAG, Cloudinary, Tumblr, Publitio, JW Player, VideoPress, Sprout, Odysee, Rumble, Dailymotion, Cloudflare Stream, Bunny Stream, Amazon, BitChute, Brighteon, Google Drive, Spotify and SoundCloud. It also supports remote and local video files.", FIFU_SLUG);
    };
    $fifu['video']['tab']['video'] = function () {
        _e("Featured video", FIFU_SLUG);
    };
    $fifu['video']['tab']['local'] = function () {
        _e("Video files", FIFU_SLUG);
    };
    $fifu['video']['local']['desc'] = function () {
        _e("It's possible to use videos from your media library as featured videos. However it's required to create a video thumbnail, that will be stored in your media library. For that, in the Feature video box, forward the video to a frame you like and click on \"set this frame as thumbnail\" button. Save the post and that's it.", FIFU_SLUG);
    };
    $fifu['video']['external']['desc'] = function () {
        _e("Remote videos are also supported, but a remote featured image (video thumbnail) must be set as well.", FIFU_SLUG);
    };
    $fifu['video']['external']['import'] = function () {
        _e("When using import plugins, add the URL of the thumbnail after the video URL, separated by a backslash. For example: 'video_url\image_url'.", FIFU_SLUG);
    };
    $fifu['video']['tip']['frame'] = function () {
        _e("Start at", FIFU_SLUG);
    };
    $fifu['video']['tip']['type'] = function () {
        _e("Supported types", FIFU_SLUG);
    };
    $fifu['video']['tip']['time'] = function () {
        _e("You can add #t=N to the end of the local or remote video URL, where N represents the number of seconds at which the video should start. The format #t=N,M is used to specify a frame where the video should stop as well.", FIFU_SLUG);
    };

    // thumbnail
    $fifu['thumbnail']['desc'] = function () {
        _e("Show the video thumbnail instead of the video itself. Thumbnails are images, so they load much faster than embedded videos.", FIFU_SLUG);
    };

    // play
    $fifu['play']['desc'] = function () {
        _e("Add a play button to the video thumbnail. When clicking on the button, the video will start (in inline or lightbox mode).", FIFU_SLUG);
    };
    $fifu['play']['hide'] = function () {
        _e("Hide from grid", FIFU_SLUG);
    };

    // width
    $fifu['width']['desc'] = function () {
        _e("Define a minimum width for a container to display a video. If the minimum width is not reached, the plugin automatically shows a thumbnail instead.", FIFU_SLUG);
    };

    // black
    $fifu['controls']['desc'] = function () {
        _e("You can disable video controls here.", FIFU_SLUG);
    };
    // mouseover
    $fifu['mouseover']['desc'] = function () {
        _e("Play a video on \"mouseover\" and pause on \"mouseout\". Requires \"Video Controls\" (except for video files) and \"Mute\".", FIFU_SLUG);
    };

    // autoplay
    $fifu['autoplay']['desc'] = function () {
        _e("Video autoplay based on viewport. Requires \"Mute\".", FIFU_SLUG);
    };

    // loop
    $fifu['loop']['desc'] = function () {
        _e("Looping video playback.", FIFU_SLUG);
    };

    // mute
    $fifu['mute']['desc'] = function () {
        _e("Start videos without audio.", FIFU_SLUG);
    };

    // background
    $fifu['background']['desc'] = function () {
        _e("Start videos in the background, meaning autoplay with no controls and no sound.", FIFU_SLUG);
    };
    $fifu['background']['video'] = function () {
        _e("background video", FIFU_SLUG);
    };
    $fifu['background']['single'] = function () {
        _e("on single post types only", FIFU_SLUG);
    };

    // privacy
    $fifu['privacy']['desc'] = function () {
        _e("The Privacy Enhanced Mode of the YouTube embedded player prevents views of embedded YouTube content from affecting the viewer's browsing experience on YouTube.", FIFU_SLUG);
    };

    // watch later
    $fifu['later']['desc'] = function () {
        _e("Adds the Watch Later and Queue buttons.", FIFU_SLUG);
    };
    $fifu['later']['button']['add'] = function () {
        _e("watch later and queue buttons", FIFU_SLUG);
    };
    $fifu['later']['button']['left'] = function () {
        _e("move to left", FIFU_SLUG);
    };

    // zoom
    $fifu['zoom']['desc'] = function () {
        _e("Disable lightbox and zoom for image gallery.", FIFU_SLUG);
    };

    // category
    $fifu['category']['desc'] = function () {
        _e("Set one image for each category. The chosen image will be a random featured image from the products in that category.", FIFU_SLUG);
    };

    // gallery
    $fifu['gallery']['desc'] = function () {
        _e("To work correctly, some galleries provided by some themes require that the dimensions of the images are saved in the database, which can be impractical due to the slowness of this process. So the plugin offers its own product gallery that does not depend on the dimensions of remote images to function properly. You can configure the behavior of this gallery in the \"Featured slider\" tab.", FIFU_SLUG);
    };
    $fifu['gallery']['toggle'] = function () {
        _e("FIFU product gallery", FIFU_SLUG);
    };
    $fifu['gallery']['adaptive'] = function () {
        _e("Adaptive height", FIFU_SLUG);
    };
    $fifu['gallery']['videos'] = function () {
        _e("Videos before images", FIFU_SLUG);
    };
    $fifu['gallery']['variations'] = function () {
        _e("Variantions images in the main gallery", FIFU_SLUG);
    };
    $fifu['gallery']['tab']['custom'] = function () {
        _e("Custom content", FIFU_SLUG);
    };
    $fifu['gallery']['custom'] = function () {
        _e("The lightbox can display custom content instead of an image or video, such as a 360º product view, PDF, Google Map, web page, or any other content that can be rendered through an iframe tag. To set this up, go to the product editor, select a slot in the 'Image gallery,' and fill out both the 'Image URL' and 'iframe URL' fields.", FIFU_SLUG);
    };

    // buy
    $fifu['buy']['desc'] = function () {
        _e("This is a faster alternative to the WooCommerce single product page. Clicking on a product image from the shop page will display the main product information in a lightbox. The \"Buy Now\" button adds the product to the cart and redirects to the checkout page.", FIFU_SLUG);
    };

    // order email
    $fifu['order-email']['desc'] = function () {
        _e("Add product images to order emails.", FIFU_SLUG);
    };

    // import
    $fifu['import']['desc'] = function () {
        _e("Use FIFU with the import tool from WooCommerce.", FIFU_SLUG);
    };
    $fifu['import']['tab']['import'] = function () {
        _e("Importing products...", FIFU_SLUG);
    };
    $fifu['import']['tab']['custom'] = function () {
        _e("Custom fields", FIFU_SLUG);
    };
    $fifu['import']['import']['csv'] = function () {
        _e("CSV example", FIFU_SLUG);
    };
    $fifu['import']['custom']['key'] = function () {
        _e("Key", FIFU_SLUG);
    };
    $fifu['import']['custom']['image'] = function () {
        _e("Featured image URL", FIFU_SLUG);
    };
    $fifu['import']['custom']['alt'] = function () {
        _e("Featured image title", FIFU_SLUG);
    };
    $fifu['import']['custom']['video'] = function () {
        _e("Featured video URL", FIFU_SLUG);
    };
    $fifu['import']['custom']['images'] = function () {
        _e("Product image URL + gallery", FIFU_SLUG);
    };
    $fifu['import']['custom']['titles'] = function () {
        _e("Product image title + gallery", FIFU_SLUG);
    };
    $fifu['import']['custom']['videos'] = function () {
        _e("Product video URL + gallery", FIFU_SLUG);
    };
    $fifu['import']['custom']['slider'] = function () {
        _e("Featured slider's URLs", FIFU_SLUG);
    };
    $fifu['import']['custom']['isbn'] = function () {
        _e("ISBN", FIFU_SLUG);
    };
    $fifu['import']['custom']['asin'] = function () {
        _e("ASIN", FIFU_SLUG);
    };
    $fifu['import']['custom']['finder'] = function () {
        _e("Media finder (webpage URL)", FIFU_SLUG);
    };
    $fifu['import']['custom']['redirection'] = function () {
        _e("Page redirection (forwarding URL)", FIFU_SLUG);
    };
    $fifu['import']['custom']['iframes'] = function () {
        _e("iframe URLs", FIFU_SLUG);
    };

    // addon
    $fifu['addon']['desc'] = function () {
        _e("The plugin automatically adds its add-on to WP All Import.", FIFU_SLUG);
    };
    $fifu['addon']['tab']['import'] = function () {
        _e("Importing...", FIFU_SLUG);
    };
    $fifu['addon']['tab']['faq'] = function () {
        _e("FAQ", FIFU_SLUG);
    };
    $fifu['addon']['import']['csv'] = function () {
        _e("CSV example", FIFU_SLUG);
    };
    $fifu['addon']['faq']['woocommerce'] = function () {
        _e("Importing variable products", FIFU_SLUG);
    };
    $fifu['addon']['faq']['variation-child-xml'] = function () {
        _e('Examples for "Variations As Child XML Elements"', FIFU_SLUG);
    };
    $fifu['addon']['faq']['export-local-images-urls'] = function () {
        _e('Exporting local image URLs using the "WP All Export" plugin.', FIFU_SLUG);
    };
    $fifu['addon']['faq']['xml'] = function () {
        _e("XML", FIFU_SLUG);
    };
    $fifu['addon']['faq']['words']['section'] = function () {
        _e("Section", FIFU_SLUG);
    };
    $fifu['addon']['faq']['words']['for'] = function () {
        _e("For", FIFU_SLUG);
    };
    $fifu['addon']['faq']['words']['description'] = function () {
        _e("Description", FIFU_SLUG);
    };
    $fifu['addon']['faq']['template'] = function () {
        _e("Import template", FIFU_SLUG);
    };
    $fifu['addon']['faq']['how']['examples'] = function () {
        _e("Examples", FIFU_SLUG);
    };
    $fifu['addon']['faq']['how']['not'] = function () {
        _e("How NOT to configure WP All Import", FIFU_SLUG);
    };
    $fifu['addon']['faq']['how']['to'] = function () {
        _e("How to configure the FIFU Add-On", FIFU_SLUG);
    };
    $fifu['addon']['faq']['section']['images'] = function () {
        _e("Images", FIFU_SLUG);
    };
    $fifu['addon']['faq']['section']['addon'] = function () {
        _e("FIFU Add-On", FIFU_SLUG);
    };
    $fifu['addon']['faq']['section']['cf'] = function () {
        _e("Custom Fields", FIFU_SLUG);
    };
    $fifu['addon']['faq']['for']['delimiter'] = function () {
        _e("List of URLs delimited by comma", FIFU_SLUG);
    };
    $fifu['addon']['faq']['for']['columns'] = function () {
        _e("URLs in different columns", FIFU_SLUG);
    };
    $fifu['addon']['faq']['description']['empty'] = function () {
        _e("Do NOT add filenames or URLs. Keep the text fields EMPTY", FIFU_SLUG);
    };
    $fifu['addon']['faq']['description']['cf'] = function () {
        _e("Do NOT add FIFU custom fields", FIFU_SLUG);
    };
    $fifu['addon']['faq']['description']['delimiter'] = function () {
        _e("Enter a comma in the \"List delimiter\" field", FIFU_SLUG);
    };

    // key
    $fifu['key']['desc'] = function () {
        _e("Please insert your email and license key below to receive updates and use this plugin without limitations.", FIFU_SLUG);
    };
    $fifu['key']['buy'] = function () {
        _e("If you intend to use FIFU on multiple distinct sites, you can purchase additional license keys <a href='https://fifu.app/#price' target='_blank'>here</a>.", FIFU_SLUG);
    };
    $fifu['key']['renew'] = function () {
        _e("You can renew your license key(s) or get more information about that <a href='https://ws.featuredimagefromurl.com/keys/' target='_blank'>here</a>.", FIFU_SLUG);
    };
    $fifu['key']['email'] = function () {
        _e("Email", FIFU_SLUG);
    };
    $fifu['key']['address'] = function () {
        _e("Address where you received the license key", FIFU_SLUG);
    };
    $fifu['key']['key'] = function () {
        _e("License key", FIFU_SLUG);
    };
    $fifu['key']['tab']['activation'] = function () {
        _e("Activation", FIFU_SLUG);
    };
    $fifu['key']['tab']['documentation'] = function () {
        _e("Documentation", FIFU_SLUG);
    };
    $fifu['key']['documentation'] = function () {
        _e("FIFU activation is based on the domain. Submitting your license key from the site example.com, [subdomain].example.com, or example.com/[anything] will activate the domain example.com. After that, you can use the same license key on the sites example.com, [subdomain].example.com, and example.com/[anything]. You can also use the same license key to activate a second domain for your test/development/stage site. If your domain has changed, please contact support.", FIFU_SLUG);
    };
    $fifu['key']['important'] = function () {
        _e("Even though with 1 license you can use FIFU on unlimited sites from the same domain, the technical support is still limited to 1 site.", FIFU_SLUG);
    };

    // cloud

    $fifu['cloud']['details']['photon'] = function () {
        _e("FIFU Cloud can also serve optimized thumbnails from a global CDN, providing image storage (not just cache), support for any image source, technical support, smart cropping, hotlink protection, and more.", FIFU_SLUG);
    };
    $fifu['cloud']['details']['click'] = function () {
        _e("FIFU Cloud improves the security of your images by offering hotlink protection. With this feature, even if a bot or someone else obtains image URLs from the source code of your website, they won't be able to embed them on other websites. Instead, an error message is displayed instead of the images.", FIFU_SLUG);
    };
    $fifu['cloud']['details']['library'] = function () {
        _e("FIFU Cloud can work as an alternative to the WordPress media library. Both store images, but FIFU Cloud processes them in the cloud, while the WordPress core consumes a lot of your website's resources. Additionally, FIFU Cloud is able to process and store thousands of images simultaneously in a few seconds, while the media library works with one image at a time.", FIFU_SLUG);
    };
    $fifu['cloud']['details']['replace'] = function () {
        _e("FIFU Cloud prevents image loss by saving your local or remote images in the cloud.", FIFU_SLUG);
    };

    // pro
    $fifu['unlock'] = function () {
        _e("Unlock all PRO features for €29.90", FIFU_SLUG);
    };

    return $fifu;
}

function fifu_get_strings_meta_box() {
    $fifu = array();

    // word
    $fifu['word']['remove'] = function () {
        _e("Remove", FIFU_SLUG);
    };

    // common
    $fifu['common']['alt'] = function () {
        _e("Alternative text", FIFU_SLUG);
    };
    $fifu['common']['image'] = function () {
        _e("Image URL", FIFU_SLUG);
    };
    $fifu['common']['ok'] = function () {
        _e("OK", FIFU_SLUG);
    };
    $fifu['common']['preview'] = function () {
        _e("Preview", FIFU_SLUG);
    };
    $fifu['common']['video'] = function () {
        _e("Video URL", FIFU_SLUG);
    };
    $fifu['common']['capture'] = function () {
        _e("Set frame as thumbnail", FIFU_SLUG);
    };

    // details
    $fifu['detail']['ratio'] = function () {
        _e("Ratio", FIFU_SLUG);
    };
    $fifu['detail']['eg'] = function () {
        _e("e.g.:", FIFU_SLUG);
    };

    // titles
    $fifu['title']['category']['video'] = function () {
        _e("Featured video", FIFU_SLUG);
    };
    $fifu['title']['category']['image'] = function () {
        _e("Featured image", FIFU_SLUG);
    };

    // video
    $fifu['video']['remove'] = function () {
        _e("Remove remote video", FIFU_SLUG);
    };
    $fifu['video']['url'] = function () {
        return __("Video URL", FIFU_SLUG);
    };
    $fifu['video']['ok'] = function () {
        return __("OK", FIFU_SLUG);
    };

    // image
    $fifu['image']['screenshot'] = function () {
        _e("Use screenshot", FIFU_SLUG);
    };
    $fifu['image']['keywords'] = function () {
        _e("Image URL or Keywords", FIFU_SLUG);
    };
    $fifu['image']['remove'] = function () {
        _e("Remove remote image", FIFU_SLUG);
    };
    $fifu['image']['sirv']['add'] = function () {
        _e("Add image from Sirv", FIFU_SLUG);
    };
    $fifu['image']['sirv']['choose'] = function () {
        _e("Choose Sirv image", FIFU_SLUG);
    };
    $fifu['image']['upload'] = function () {
        _e("Upload to media library", FIFU_SLUG);
    };
    $fifu['image']['alt'] = function () {
        return __("Alternative text", FIFU_SLUG);
    };
    $fifu['image']['ifm'] = function () {
        return __("iframe URL", FIFU_SLUG);
    };
    $fifu['image']['url'] = function () {
        return __("Image URL", FIFU_SLUG);
    };
    $fifu['image']['ok'] = function () {
        return __("OK", FIFU_SLUG);
    };
    $fifu['alt']['help'] = function () {
        _e("This field is used to provide alternative text for images, enhancing accessibility and SEO. If it is empty, then FIFU will use the post title automatically.", FIFU_SLUG);
    };

    // ads
    $fifu['ads']['plans'] = function () {
        _e("Know our yearly and one-time plans", FIFU_SLUG);
    };
    $fifu['ads']['video'] = function () {
        _e("Featured video", FIFU_SLUG);
    };
    $fifu['ads']['audio'] = function () {
        _e("Featured audio", FIFU_SLUG);
    };
    $fifu['ads']['slider'] = function () {
        _e("Featured slider", FIFU_SLUG);
    };
    $fifu['ads']['gallery'] = function () {
        _e("WooCommerce image/video gallery", FIFU_SLUG);
    };
    $fifu['ads']['api'] = function () {
        _e("Integration with import plugins and REST API", FIFU_SLUG);
    };
    $fifu['ads']['search'] = function () {
        _e("Auto set featured image using title and a search engine", FIFU_SLUG);
    };
    $fifu['ads']['web'] = function () {
        _e("Auto set featured media using a web page address", FIFU_SLUG);
    };
    $fifu['ads']['subscribe'] = function () {
        _e("Subscribe now", FIFU_SLUG);
    };
    $fifu['ads']['storage'] = function () {
        _e("Never lose an image! FIFU Cloud securely stores local or remote images in Google Cloud Storage.", FIFU_SLUG);
    };
    $fifu['ads']['wait'] = function () {
        _e("No more waiting! FIFU Cloud speeds up image loading. With optimized WebP thumbnails served by Google Cloud CDN, your website's SEO score will soar.", FIFU_SLUG);
    };
    $fifu['ads']['process'] = function () {
        _e("No more torture for your website! FIFU Cloud processes images 100% on Google Cloud servers, saving your website's computational resources.", FIFU_SLUG);
    };
    $fifu['ads']['money'] = function () {
        _e("No wasted money! FIFU Cloud charges based on stored images. Pay-as-you-go with flexible cancellation.", FIFU_SLUG);
    };
    $fifu['ads']['crop'] = function () {
        _e("No more scary images! FIFU Cloud uses AI to detect and crop pictures for themes or social media. No more headless people.", FIFU_SLUG);
    };
    $fifu['ads']['protection'] = function () {
        _e("No longer be stolen! FIFU Cloud protects your images. They can't be embedded on other websites.", FIFU_SLUG);
    };

    // placeholder
    $fifu['placeholder']['forwarding'] = function () {
        _e("Forwarding URL", FIFU_SLUG);
    };
    $fifu['placeholder']['audio'] = function () {
        _e("Audio URL", FIFU_SLUG);
    };
    $fifu['placeholder']['page'] = function () {
        _e("Web page URL", FIFU_SLUG);
    };
    $fifu['placeholder']['isbn'] = function () {
        _e("ISBN", FIFU_SLUG);
    };
    $fifu['placeholder']['asin'] = function () {
        _e("ASIN", FIFU_SLUG);
    };
    $fifu['placeholder']['embed'] = function () {
        _e("Embed code", FIFU_SLUG);
    };

    return $fifu;
}

function fifu_get_strings_meta_box_php() {
    $fifu = array();

    // common
    $fifu['common']['wait'] = function () {
        return __("Please wait...", FIFU_SLUG);
    };
    $fifu['common']['image'] = function () {
        return __("Image URL", FIFU_SLUG);
    };
    $fifu['common']['video'] = function () {
        return __("Video URL", FIFU_SLUG);
    };

    // wait
    $fifu['title']['product']['image'] = function () {
        return __("Product image", FIFU_SLUG);
    };
    $fifu['title']['product']['images'] = function () {
        return __("Image gallery", FIFU_SLUG);
    };
    $fifu['title']['product']['video'] = function () {
        return __("Featured video", FIFU_SLUG);
    };
    $fifu['title']['product']['videos'] = function () {
        return __("Video gallery", FIFU_SLUG);
    };
    $fifu['title']['product']['slider'] = function () {
        return __("Featured slider", FIFU_SLUG);
    };
    $fifu['title']['post']['image'] = function () {
        return __("Featured image", FIFU_SLUG);
    };
    $fifu['title']['post']['video'] = function () {
        return __("Featured video", FIFU_SLUG);
    };
    $fifu['title']['post']['slider'] = function () {
        return __("Featured slider", FIFU_SLUG);
    };
    $fifu['title']['post']['isbn'] = function () {
        return __("ISBN", FIFU_SLUG);
    };
    $fifu['title']['post']['asin'] = function () {
        return __("ASIN", FIFU_SLUG);
    };
    $fifu['title']['post']['finder'] = function () {
        return __("Media finder", FIFU_SLUG);
    };
    $fifu['title']['post']['audio'] = function () {
        return __("Featured audio", FIFU_SLUG);
    };
    $fifu['title']['post']['redirection'] = function () {
        return __("Page redirection", FIFU_SLUG);
    };
    $fifu['title']['post']['popup'] = function () {
        return __("Custom popup", FIFU_SLUG);
    };

    // variation
    $fifu['variation']['field'] = function () {
        return __("Product Image (URL)", FIFU_SLUG);
    };
    $fifu['variation']['info'] = function () {
        return __("Powered by FIFU plugin", FIFU_SLUG);
    };
    $fifu['variation']['image'] = function () {
        return __("Image URL", FIFU_SLUG);
    };
    $fifu['variation']['images'] = function () {
        return __("Gallery Image (URL)", FIFU_SLUG);
    };
    $fifu['variation']['upload'] = function () {
        return __("Upload to media library", FIFU_SLUG);
    };

    return $fifu;
}

function fifu_get_strings_wai() {
    $fifu = array();

    // titles
    $fifu['title']['image'] = function () {
        return __("Featured image (URL)", FIFU_SLUG);
    };
    $fifu['title']['title'] = function () {
        return __("Featured image title", FIFU_SLUG);
    };
    $fifu['title']['video'] = function () {
        return __("Featured video (URL)", FIFU_SLUG);
    };
    $fifu['title']['images'] = function () {
        return __("Product image URL + gallery URLs", FIFU_SLUG);
    };
    $fifu['title']['titles'] = function () {
        return __("Product image title + gallery titles", FIFU_SLUG);
    };
    $fifu['title']['videos'] = function () {
        return __("Product video URL + gallery URLs", FIFU_SLUG);
    };
    $fifu['title']['slider'] = function () {
        return __("Featured slider (URLs)", FIFU_SLUG);
    };
    $fifu['title']['delimiter'] = function () {
        return __("List delimiter", FIFU_SLUG);
    };
    $fifu['title']['isbn'] = function () {
        return __("ISBN", FIFU_SLUG);
    };
    $fifu['title']['asin'] = function () {
        return __("ASIN", FIFU_SLUG);
    };
    $fifu['title']['finder'] = function () {
        return __("Media finder (webpage URL)", FIFU_SLUG);
    };
    $fifu['title']['redirection'] = function () {
        return __("Page redirection (forwarding URL)", FIFU_SLUG);
    };

    // info
    $fifu['info']['delimited'] = function () {
        return __("By default, FIFU uses | as the URL delimiter. You can define a different value in the 'List delimiter' field.", FIFU_SLUG);
    };
    $fifu['info']['default'] = function () {
        return __("Default is '|'", FIFU_SLUG);
    };
    $fifu['info']['finder'] = function () {
        return __("Works with \"Auto set featured media using web page address\"", FIFU_SLUG);
    };
    $fifu['info']['alts'] = function () {
        return __("If empty, FIFU will automatically use the product title as alternative text for every image.", FIFU_SLUG);
    };
    $fifu['info']['alt'] = function () {
        return __("If empty, FIFU will automatically use the post title as alternative text.", FIFU_SLUG);
    };

    return $fifu;
}

function fifu_get_strings_widget() {
    $fifu = array();

    // words
    $fifu['word']['settings'] = function () {
        return _e("Settings", FIFU_SLUG);
    };
    $fifu['word']['rows'] = function () {
        return _e("Rows", FIFU_SLUG);
    };
    $fifu['word']['columns'] = function () {
        return _e("Columns", FIFU_SLUG);
    };

    // label
    $fifu['label']['gallery'] = function () {
        return _e("Product gallery settings", FIFU_SLUG);
    };

    // titles
    $fifu['title']['media'] = function () {
        return __("Featured media", FIFU_SLUG);
    };
    $fifu['title']['grid'] = function () {
        return __("Featured grid", FIFU_SLUG);
    };
    $fifu['title']['gallery'] = function () {
        return __("Product gallery", FIFU_SLUG);
    };
    $fifu['title']['slider'] = function () {
        return _e("Featured slider", FIFU_SLUG);
    };

    // description
    $fifu['description']['media'] = function () {
        return __("Displays the featured image, video, or slider from the current post, page, or custom post type.", FIFU_SLUG);
    };
    $fifu['description']['grid'] = function () {
        return __("Displays the images from the featured slider in a grid format.", FIFU_SLUG);
    };
    $fifu['description']['gallery'] = function () {
        return __("Displays the product gallery.", FIFU_SLUG);
    };

    return $fifu;
}

function fifu_get_strings_quick_edit() {
    $fifu = array();

    // titles
    $fifu['title']['image'] = function () {
        return __("Featured image", FIFU_SLUG);
    };
    $fifu['title']['video'] = function () {
        return __("Featured video", FIFU_SLUG);
    };
    $fifu['title']['slider'] = function () {
        return __("Featured slider", FIFU_SLUG);
    };
    $fifu['title']['search'] = function () {
        return __("Image search", FIFU_SLUG);
    };
    $fifu['title']['gallery']['image'] = function () {
        return __("Image gallery", FIFU_SLUG);
    };
    $fifu['title']['gallery']['video'] = function () {
        return __("Video gallery", FIFU_SLUG);
    };
    $fifu['title']['variable']['product'] = function () {
        return __("Product", FIFU_SLUG);
    };
    $fifu['title']['variable']['variation'] = function () {
        return __("Variations", FIFU_SLUG);
    };
    $fifu['title']['variable']['name'] = function () {
        return __("Name", FIFU_SLUG);
    };

    // tips
    $fifu['tip']['column'] = function () {
        return __("Quick edit", FIFU_SLUG);
    };
    $fifu['tip']['image'] = function () {
        return __("Set featured image with URL", FIFU_SLUG);
    };
    $fifu['tip']['video'] = function () {
        return __("Set featured video with URL", FIFU_SLUG);
    };
    $fifu['tip']['search'] = function () {
        return __("Search Unsplash images. Example: sun,sea", FIFU_SLUG);
    };

    // placeholder
    $fifu['url']['image'] = function () {
        return __("Image URL", FIFU_SLUG);
    };
    $fifu['url']['video'] = function () {
        return __("Video URL", FIFU_SLUG);
    };
    $fifu['image']['keywords'] = function () {
        return __("Keywords", FIFU_SLUG);
    };

    // button
    $fifu['button']['save'] = function () {
        return __("Save", FIFU_SLUG);
    };
    $fifu['button']['clean'] = function () {
        return __("Clear", FIFU_SLUG);
    };
    $fifu['button']['upload'] = function () {
        return __("Upload to media library", FIFU_SLUG);
    };

    // pro
    $fifu['unlock'] = function () {
        return __("Unlock all PRO features for €29.90", FIFU_SLUG);
    };

    return $fifu;
}

function fifu_get_strings_help() {
    $fifu = array();

    // title
    $fifu['title']['examples'] = function () {
        return __("Examples", FIFU_SLUG);
    };
    $fifu['title']['keywords'] = function () {
        return __("Keywords", FIFU_SLUG);
    };
    $fifu['title']['empty'] = function () {
        return __("Empty", FIFU_SLUG);
    };
    $fifu['title']['more'] = function () {
        return __("More", FIFU_SLUG);
    };
    $fifu['title']['url'] = function () {
        return __("Image URL", FIFU_SLUG);
    };
    $fifu['desc']['url'] = function () {
        return __("Loads the corresponding image. You should use an absolute URL, which means including the protocol (http/https) and the domain.", FIFU_SLUG);
    };
    $fifu['desc']['keywords'] = function () {
        return __("Loads a list of images from Unsplash. Choose the image that is most suitable. The filters configured in 'FIFU Settings → Automatic → Auto set featured image from Unsplash using tags' works here.", FIFU_SLUG);
    };
    $fifu['desc']['empty'] = function () {
        return __("Loads a list of images from a search engine based on the post's title. Choose the most suitable image. The filters configured in 'FIFU Settings → Automatic → Auto set featured image using post title and search engine' works here.", FIFU_SLUG);
    };
    $fifu['desc']['more'] = function () {
        return __("FIFU can auto set images based on post title, tags, remote web page address, and more. Check FIFU Settings → Automatic.", FIFU_SLUG);
    };
    $fifu['unsplash']['unlock'] = function () {
        return __("Unlock all PRO features for €29.90", FIFU_SLUG);
    };
    $fifu['unsplash']['more'] = function () {
        return __("Loading more...", FIFU_SLUG);
    };
    $fifu['unsplash']['loading'] = function () {
        return __("Loading...", FIFU_SLUG);
    };
    $fifu['warning']['video']['thumbnail'] = function () {
        return __("You should also set a featured image. It will be used as the video thumbnail.", FIFU_SLUG);
    };

    return $fifu;
}

function fifu_get_strings_cloud() {
    $fifu = array();

    // title
    $fifu['title']['price'] = function () {
        return _e("Pricing", FIFU_SLUG);
    };
    $fifu['title']['account'] = function () {
        return _e("Account", FIFU_SLUG);
    };
    $fifu['title']['hotlink'] = function () {
        return _e("Hotlink protection", FIFU_SLUG);
    };
    $fifu['title']['payment'] = function () {
        return _e("Payment and billing information", FIFU_SLUG);
    };
    $fifu['title']['add'] = function () {
        return _e("Upload to Cloud", FIFU_SLUG);
    };
    $fifu['title']['delete'] = function () {
        return _e("Delete from Cloud", FIFU_SLUG);
    };
    $fifu['title']['media'] = function () {
        return _e("Link local image URLs to FIFU plugin", FIFU_SLUG);
    };
    $fifu['title']['billing'] = function () {
        return _e("Billing", FIFU_SLUG);
    };

    // tabs
    $fifu['tabs']['welcome'] = function () {
        return _e("Welcome", FIFU_SLUG);
    };
    $fifu['tabs']['send'] = function () {
        return _e("Upload", FIFU_SLUG);
    };
    $fifu['tabs']['delete'] = function () {
        return _e("Delete", FIFU_SLUG);
    };
    $fifu['tabs']['media'] = function () {
        return _e("Local images", FIFU_SLUG);
    };
    $fifu['tabs']['trash'] = function () {
        return _e("Trash", FIFU_SLUG);
    };
    $fifu['tabs']['account'] = function () {
        return _e("Account", FIFU_SLUG);
    };

    // info
    $fifu['ws']['down'] = function () {
        return __("Web service is down", FIFU_SLUG);
    };
    $fifu['ws']['connection']['ok'] = function () {
        return __("Connected", FIFU_SLUG);
    };
    $fifu['ws']['connection']['fail'] = function () {
        return __("Not connected", FIFU_SLUG);
    };

    // table
    $fifu['table']['no']['images'] = function () {
        return __("No images available", FIFU_SLUG);
    };
    $fifu['table']['no']['posts'] = function () {
        return __("No posts available", FIFU_SLUG);
    };
    $fifu['table']['no']['data'] = function () {
        return __("No data available", FIFU_SLUG);
    };
    $fifu['table']['select']['all'] = function () {
        return __("select all", FIFU_SLUG);
    };
    $fifu['table']['select']['none'] = function () {
        return __("select none", FIFU_SLUG);
    };
    $fifu['table']['load'] = function () {
        return __("load more", FIFU_SLUG);
    };
    $fifu['table']['limit'] = function () {
        return __("1,000 rows limit", FIFU_SLUG);
    };
    $fifu['table']['delete'] = function () {
        return __("delete", FIFU_SLUG);
    };
    $fifu['table']['upload'] = function () {
        return __("upload", FIFU_SLUG);
    };
    $fifu['table']['link'] = function () {
        return __("link", FIFU_SLUG);
    };
    $fifu['table']['dialog']['delete'] = function () {
        return __("Delete", FIFU_SLUG);
    };
    $fifu['table']['dialog']['cancel'] = function () {
        return __("Cancel", FIFU_SLUG);
    };
    $fifu['table']['dialog']['yes'] = function () {
        return __("Yes", FIFU_SLUG);
    };
    $fifu['table']['dialog']['no'] = function () {
        return __("No", FIFU_SLUG);
    };
    $fifu['table']['category'] = function () {
        return __("category", FIFU_SLUG);
    };
    $fifu['table']['slider'] = function () {
        return __("slider", FIFU_SLUG);
    };
    $fifu['table']['gallery'] = function () {
        return __("gallery", FIFU_SLUG);
    };
    $fifu['table']['featured'] = function () {
        return __("featured media", FIFU_SLUG);
    };
    $fifu['table']['filter'] = function () {
        return __("Filter results", FIFU_SLUG);
    };

    // support
    $fifu['support']['whats'] = function () {
        _e("FIFU Cloud is a cloud-based service that securely stores your images within the robust infrastructure of Google Cloud. Not only does FIFU Cloud ensure image preservation, but it also optimizes and rapidly delivers them through Google's global Edge Network. Additionally, FIFU Cloud automatically generates thumbnails for each image and serves them in the efficient webp format, enhancing the overall performance of your website.", FIFU_SLUG);
    };
    $fifu['support']['save'] = function () {
        _e("Never lose an image again", FIFU_SLUG);
    };
    $fifu['support']['fast'] = function () {
        _e("Images load much faster", FIFU_SLUG);
    };
    $fifu['support']['process'] = function () {
        _e("Images processed in the cloud", FIFU_SLUG);
    };
    $fifu['support']['price'] = function () {
        _e("Pay per stored image", FIFU_SLUG);
    };
    $fifu['support']['smart'] = function () {
        _e("Smart cropping", FIFU_SLUG);
    };
    $fifu['support']['hotlink'] = function () {
        _e("Hotlink protection", FIFU_SLUG);
    };
    $fifu['support']['save-desc'] = function () {
        _e("Image sources sometimes remove or change the URLs of their images, either due to internal restructuring or to prevent their embedding on other websites. This can cause significant problems for websites that had previously embedded these images, as they become lost and cannot be retrieved. However, FIFU Cloud offers a solution to this issue. It saves your embedded images in the cloud and provides stable URLs to access them. By replacing the existing URLs with FIFU Cloud URLs, you eliminate the problem. Additionally, if needed, you have the option to revert back to the original URLs.", FIFU_SLUG);
    };
    $fifu['support']['fast-desc'] = function () {
        _e("One major drawback of embedding remote images on your website is the lack of thumbnails. Without thumbnails, your website loads the same large image file regardless of whether it's viewed on desktop or mobile phone, on a post or homepage. Additionally, there are instances where the image may not be optimized or hosted on a slow server. FIFU Cloud addresses all these concerns by storing and serving optimized thumbnails through a fast content delivery network (CDN). This means that when visitors access your pages, they receive only the smallest image files required to display the images without any loss in quality. The smaller the file size, the faster the images are rendered, resulting in improved loading times for your website.", FIFU_SLUG);
    };
    $fifu['support']['process-desc'] = function () {
        _e("Your website was not designed for image processing. However, when you save an image in the media library, the WordPress core, along with your theme and plugins, initiate multiple tasks to process the image locally. These tasks include conversions, duplications, rotations, resizing, cropping, compression, and more. Depending on the number of images, this process can take weeks, and eventually, the website needs to repeat the entire process again. This consumes significant storage, memory, and processing power, which can result in slow website performance for users. In contrast, FIFU Cloud eliminates the need to use your own computing resources. We process your images entirely on Google Cloud servers. By leveraging the power of the cloud, we can efficiently process and store thousands of images simultaneously within seconds.", FIFU_SLUG);
    };
    $fifu['support']['price-desc2'] = function () {
        _e("Similar cloud services often charge based on the number of accesses to images or sell static plans where you pay for the allocated storage, even if it remains unused. However, FIFU Cloud takes a different approach. It only charges for the daily average of stored images over a 30-day period, excluding thumbnails from the billing. Let's consider an example: on the first day, you stored 1000 images; ten days later, you deleted all of them; and then, ten days after that, you added 1100 images, which were stored for ten days. Thus, the average usage over the 30-day period would be 700 images per day, and you will only be charged for that amount. If there are no changes in the next period, the average would be 1100, resulting in an increased cost. However, if you remove all the images in the subsequent period, there will be no charge incurred.", FIFU_SLUG);
    };
    $fifu['support']['smart-desc'] = function () {
        _e("WordPress themes and social media platforms often crop the central area of non-standard images, which can be problematic as the main object is often not centered. For example, Facebook, Twitter, and LinkedIn display featured images at ~1200×630 pixels in landscape orientation. However, sharing a full-body portrait photo may result in the cropped person losing their head and feet. FIFU Cloud, on the other hand, utilizes face and object detection to intelligently crop images, showcasing what truly matters without compromising style or information.", FIFU_SLUG);
    };
    $fifu['support']['hotlink-desc'] = function () {
        _e("Protecting your website's content, including text and image URLs, from unauthorized access and extraction by bots can be a challenging task. Once this data is obtained, it can be replicated elsewhere, diverting the rightful visitors to other platforms. Fortunately, FIFU Cloud offers a solution with hotlink protection. This feature restricts other websites (excluding social media platforms) from displaying your images. While it may not completely solve the problem, it significantly hinders the unauthorized usage of your content, as posts with blocked images are less appealing to those attempting to extract information.", FIFU_SLUG);
    };

    // pricing
    $fifu['pricing']['table']['quantity'] = function () {
        _e("Quantity of images", FIFU_SLUG);
    };
    $fifu['pricing']['desc'] = function () {
        _e("€0.001 per image. Payment is based on the daily average of stored images in FIFU Cloud, billed every 30 days.", FIFU_SLUG);
    };
    $fifu['pricing']['thumbnails'] = function () {
        _e("You don't pay for the multiple thumbnails generated for each image.", FIFU_SLUG);
    };
    $fifu['pricing']['example'] = function () {
        _e("Price calculation example", FIFU_SLUG);
    };
    $fifu['pricing']['table']['interval'] = function () {
        _e("30-day period interval", FIFU_SLUG);
    };
    $fifu['pricing']['table']['days'] = function () {
        _e("Number of days", FIFU_SLUG);
    };
    $fifu['pricing']['table']['stored'] = function () {
        _e("Quantity of images in FIFU Cloud", FIFU_SLUG);
    };
    $fifu['pricing']['table']['average'] = function () {
        _e("30-day average usage", FIFU_SLUG);
    };
    $fifu['pricing']['table']['price'] = function () {
        _e("Price per image", FIFU_SLUG);
    };
    $fifu['pricing']['table']['total'] = function () {
        _e("Total price", FIFU_SLUG);
    };

    // upload
    $fifu['upload']['desc'] = function () {
        _e("Costs start from the upload date.", FIFU_SLUG);
    };
    $fifu['upload']['automatic']['title'] = function () {
        _e("Automatic upload", FIFU_SLUG);
    };
    $fifu['upload']['automatic']['desc'] = function () {
        _e("Automatically uploads remote images to the cloud.", FIFU_SLUG);
    };

    // delete
    $fifu['delete']['desc'] = function () {
        _e("When an image is deleted from the cloud, you are no longer charged from the next day.", FIFU_SLUG);
    };
    $fifu['delete']['automatic']['title'] = function () {
        _e("Automatic delete", FIFU_SLUG);
    };
    $fifu['delete']['automatic']['desc'] = function () {
        _e("Automatically delete images from the cloud when they are no longer in use on the site, for example, due to a deleted post.", FIFU_SLUG);
    };

    // media
    $fifu['media']['desc'] = function () {
        _e("Before uploading local images to the cloud, you should copy their URLs to FIFU custom fields by clicking the \"link\" button. Have a database backup as post metadata will be replaced, making this plugin responsible for displaying images. Do not delete images from the media library until ensuring they were saved in the cloud.", FIFU_SLUG);
    };

    // billing
    $fifu['billing']['desc'] = function () {
        _e("FIFU Cloud charges based on the average number of stored images within each 30-day period. The data below is updated hourly.", FIFU_SLUG);
    };
    $fifu['billing']['current'] = function () {
        _e("Current 30-day period", FIFU_SLUG);
    };
    $fifu['billing']['column']['start'] = function () {
        _e("Start date", FIFU_SLUG);
    };
    $fifu['billing']['column']['end'] = function () {
        _e("End date", FIFU_SLUG);
    };
    $fifu['billing']['column']['average'] = function () {
        _e("Daily average of stored images", FIFU_SLUG);
    };
    $fifu['billing']['column']['cost'] = function () {
        _e("Current cost", FIFU_SLUG);
    };

    // keys
    $fifu['keys']['header'] = function () {
        _e("Multiple image selection", FIFU_SLUG);
    };
    $fifu['keys']['adjacent'] = function () {
        _e("Adjacent", FIFU_SLUG);
    };
    $fifu['keys']['non-adjacent'] = function () {
        _e("Non-adjacent", FIFU_SLUG);
    };
    $fifu['keys']['shift'] = function () {
        _e("To select multiple images adjacent to each other, click the first image, press <b>SHIFT</b> and click the last image.", FIFU_SLUG);
    };
    $fifu['keys']['ctrl'] = function () {
        _e("To select multiple non-adjacent images, click the first image, press the <b>CTRL</b> key, and click each desired image.", FIFU_SLUG);
    };

    // label
    $fifu['label']['email'] = function () {
        _e("Email", FIFU_SLUG);
    };
    $fifu['label']['website'] = function () {
        _e("Site", FIFU_SLUG);
    };
    $fifu['label']['title']['email'] = function () {
        _e("Enter your email", FIFU_SLUG);
    };

    // pro
    $fifu['unlock'] = function () {
        _e("Unlock all PRO features for €29.90", FIFU_SLUG);
    };

    // reset
    $fifu['reset']['button'] = function () {
        _e("Reset credentials", FIFU_SLUG);
    };

    // signup
    $fifu['signup']['email']['message'] = function () {
        _e("Please enter your email", FIFU_SLUG);
    };
    $fifu['signup']['button'] = function () {
        _e("Sign up", FIFU_SLUG);
    };

    // column
    $fifu['column']['image'] = function () {
        _e("Image", FIFU_SLUG);
    };
    $fifu['column']['title'] = function () {
        _e("Post title", FIFU_SLUG);
    };
    $fifu['column']['published'] = function () {
        _e("Post date", FIFU_SLUG);
    };
    $fifu['column']['id'] = function () {
        _e("Post ID", FIFU_SLUG);
    };
    $fifu['column']['location'] = function () {
        _e("Image location", FIFU_SLUG);
    };
    $fifu['column']['upload'] = function () {
        _e("Upload date", FIFU_SLUG);
    };
    $fifu['column']['featured'] = function () {
        _e("Featured image", FIFU_SLUG);
    };
    $fifu['column']['gallery'] = function () {
        _e("Gallery images", FIFU_SLUG);
    };
    $fifu['column']['date'] = function () {
        _e("Date", FIFU_SLUG);
    };
    $fifu['column']['number'] = function () {
        _e("Number of images", FIFU_SLUG);
    };

    // search
    $fifu['search']['url'] = function () {
        _e("Image URL", FIFU_SLUG);
    };
    $fifu['search']['search'] = function () {
        _e("Search", FIFU_SLUG);
    };

    // update
    $fifu['update']['button'] = function () {
        _e("Update payment method", FIFU_SLUG);
    };

    // close
    $fifu['close']['button'] = function () {
        _e("Close account", FIFU_SLUG);
    };
    $fifu['close']['title'] = function () {
        _e("Close account", FIFU_SLUG);
    };
    $fifu['close']['delete'] = function () {
        _e("All the images you uploaded to FIFU Cloud will be deleted. Are you sure?", FIFU_SLUG);
    };

    // delete dialog
    $fifu['delete']['dialog']['title'] = function () {
        _e("Remove selected image(s)", FIFU_SLUG);
    };
    $fifu['delete']['dialog']['sure'] = function () {
        _e("The selected images will be permanently removed from FIFU Cloud and cannot be recovered. Are you sure?", FIFU_SLUG);
    };

    $fifu['message']['new'] = function () {
        _e("Please wait, a much better FIFU Cloud is coming...", FIFU_SLUG);
    };
    $fifu['message']['waitlist'] = function () {
        _e("During the 2 years that FIFU Cloud has been operating, we have learned a lot and received valuable feedback from our users, allowing us to now develop a much better product. The new FIFU Cloud will be much easier to use, will give you full access to the image server, provide friendly titles to image files, and, most importantly, will be capable of storing and delivering tens of thousands of optimized images at an extremely low price, as the infrastructure will be based on free internet services. If you are already a user, you can continue using the current version of FIFU Cloud normally until the migration, when we will get in touch. And if you are not yet a user but are interested in the service, please send an email to cloud@fifu.app with the subject 'WAITLIST.' The project is expected to be completed in a few months.", FIFU_SLUG);
    };

    return $fifu;
}

function fifu_get_strings_uninstall() {
    $fifu = array();

    $fifu['button']['text']['clean'] = function () {
        return __("Clear metadata and deactivate", FIFU_SLUG);
    };
    $fifu['button']['text']['deactivate'] = function () {
        return __("Deactivate", FIFU_SLUG);
    };
    $fifu['button']['description']['clean'] = function () {
        return __("If you don't intend to use FIFU again", FIFU_SLUG);
    };
    $fifu['button']['description']['deactivate'] = function () {
        return __("If it's a temporary deactivation", FIFU_SLUG);
    };
    $fifu['text']['why'] = function () {
        return __("Why are you deactivating FIFU?", FIFU_SLUG);
    };
    $fifu['text']['email'] = function () {
        return __("The developer will respond within 8 hours.", FIFU_SLUG);
    };
    $fifu['text']['reason']['conflict'] = function () {
        return __("Doesn't work with a specific theme, plugin, or URL...", FIFU_SLUG);
    };
    $fifu['text']['reason']['pro'] = function () {
        return __("Works well, but I would need a new or PRO feature...", FIFU_SLUG);
    };
    $fifu['text']['reason']['seo'] = function () {
        return __("Concerned about SEO, performance, or copyright...", FIFU_SLUG);
    };
    $fifu['text']['reason']['local'] = function () {
        return __("I wish it worked with my local images...", FIFU_SLUG);
    };
    $fifu['text']['reason']['undestand'] = function () {
        return __("I didn't understand how it works...", FIFU_SLUG);
    };
    $fifu['text']['reason']['others'] = function () {
        return __("Others...", FIFU_SLUG);
    };

    return $fifu;
}

function fifu_get_strings_dokan() {
    $fifu = array();

    $fifu['title']['product']['image'] = function () {
        _e("Product image", FIFU_SLUG);
    };
    $fifu['title']['product']['gallery'] = function () {
        _e("Image gallery", FIFU_SLUG);
    };

    $fifu['placeholder']['product']['image'] = function () {
        _e("Image URL", FIFU_SLUG);
    };

    return $fifu;
}

function fifu_get_strings_plugins() {
    $fifu = array();

    $fifu['support'] = function () {
        return __("Technical support", FIFU_SLUG);
    };
    $fifu['upgrade'] = function () {
        return __("Upgrade to <b>PRO</b> for €29.90", FIFU_SLUG);
    };
    $fifu['star'] = function () {
        return __("Are you enjoying FIFU? Please give it a 5-star rating!", FIFU_SLUG);
    };
    $fifu['settings'] = function () {
        return __("Settings", FIFU_SLUG);
    };

    return $fifu;
}

function fifu_get_strings_elementor() {
    $fifu = array();

    $fifu['title']['image'] = function () {
        return __("Featured Image", FIFU_SLUG);
    };
    $fifu['section']['image'] = function () {
        return __("Featured image", FIFU_SLUG);
    };
    $fifu['control']['image'] = function () {
        return __("Image URL", FIFU_SLUG);
    };
    $fifu['title']['video'] = function () {
        return __("Featured Video", FIFU_SLUG);
    };
    $fifu['section']['video'] = function () {
        return __("Featured video", FIFU_SLUG);
    };
    $fifu['control']['video'] = function () {
        return __("Video URL", FIFU_SLUG);
    };
    $fifu['control']['pro'] = function () {
        return __("Requires FIFU PRO", FIFU_SLUG);
    };

    return $fifu;
}

function fifu_get_strings_gravity_forms() {
    $fifu = array();

    $fifu['title']['addon'] = function () {
        return __("Field Add-on", FIFU_SLUG);
    };
    $fifu['field']['image'] = function () {
        return __("Featured Image", FIFU_SLUG);
    };
    $fifu['field']['slider'] = function () {
        return __("Featured Slider", FIFU_SLUG);
    };
    $fifu['field']['video'] = function () {
        return __("Featured Video", FIFU_SLUG);
    };
    $fifu['placeholder']['image'] = function () {
        return __("Image URL", FIFU_SLUG);
    };
    $fifu['placeholder']['video'] = function () {
        return __("Video URL", FIFU_SLUG);
    };
    $fifu['css']['title'] = function () {
        return __("Input CSS Classes", FIFU_SLUG);
    };
    $fifu['css']['desc'] = function () {
        return __("The CSS Class names to be added to the field input.", FIFU_SLUG);
    };
    $fifu['css']['settings'] = function () {
        return _e("Input CSS Classes", FIFU_SLUG);
    };

    return $fifu;
}

function fifu_get_strings_api() {
    $fifu = array();

    $fifu['info']['try'] = function () {
        return __("try again later", FIFU_SLUG);
    };

    return $fifu;
}

function fifu_get_strings_shortcode() {
    $fifu = array();

    $fifu['label']['image'] = function () {
        return __("Featured image", FIFU_SLUG);
    };
    $fifu['placeholder']['image'] = function () {
        return __("Image URL", FIFU_SLUG);
    };

    return $fifu;
}

function fifu_get_strings_video() {
    $fifu = array();

    $fifu['button']['later'] = function () {
        return __("Watch later", FIFU_SLUG);
    };
    $fifu['button']['queue'] = function () {
        return __("Queue", FIFU_SLUG);
    };

    return $fifu;
}

function fifu_get_strings_image() {
    $fifu = array();

    $fifu['photo']['credit'] = function () {
        return __("Photo credit", FIFU_SLUG);
    };

    return $fifu;
}

function fifu_get_strings_notice() {
    $fifu = array();

    // options
    $fifu['notice']['key'] = function () {
        return __("Please submit your activation key to use the plugin without limitations.", FIFU_SLUG);
    };
    $fifu['notice']['expired'] = function () {
        return __("Your license key has expired. Please renew or upgrade it to continue receiving updates and support.", FIFU_SLUG);
    };

    return $fifu;
}
