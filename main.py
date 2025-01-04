import os
import json
import time
import requests
import zipfile
import shutil
from git import Repo

# Use environment variable for authentication
GITHUB_TOKEN = os.environ.get('GH_PAT')
REPO_URL = f"https://oauth2:{GITHUB_TOKEN}@github.com/lbryant-sss/wordpress-plugins.git"

# Configuration
REPO_DIR = './repo'
CACHE_FILE = "cache.json"

# Ensure cache directory exists
os.makedirs(os.path.dirname(CACHE_FILE) or '.', exist_ok=True)

WORDPRESS_API_URL = "https://api.wordpress.org/plugins/info/1.2/"
DOWNLOAD_URL = "https://downloads.wordpress.org/plugin/"
TIME_INTERVAL = 1  # Interval between requests in seconds
MAX_PLUGINS = 1000

def load_cache():
    """Load plugin cache file or create a new one with default structure."""
    try:
        if not os.path.exists(CACHE_FILE):
            return {"timestamp": 0, "plugins": {}}
        
        with open(CACHE_FILE, "r") as file:
            content = file.read().strip()
            
            # If file is empty or malformed, return default cache
            if not content or content == '{}':
                return {"timestamp": 0, "plugins": {}}
            
            # Try to parse JSON
            return json.loads(content)
    
    except (json.JSONDecodeError, IOError) as e:
        print(f"Error reading cache file: {e}")
        # Create a fresh, valid cache file
        default_cache = {"timestamp": 0, "plugins": {}}
        save_cache(default_cache)
        return default_cache

def save_cache(cache):
    """Save the plugin cache, ensuring proper JSON formatting."""
    try:
        with open(CACHE_FILE, "w") as file:
            json.dump(cache, file, indent=2)  # Added indent for readability
    except IOError as e:
        print(f"Error saving cache file: {e}")

def get_plugin_list():
    """Return a static list of plugins or fetch dynamically."""
    return ["elementor",
    "contact-form-7",
    "wordpress-seo",
    "classic-editor",
    "woocommerce",
    "akismet",
    "litespeed-cache",
    "wpforms-lite",
    "all-in-one-wp-migration",
    "wordfence",
    "really-simple-ssl",
    "google-site-kit",
    "jetpack",
    "duplicate-post",
    "wp-mail-smtp",
    "updraftplus",
    "duplicate-page",
    "all-in-one-seo-pack",
    "wordpress-importer",
    "seo-by-rank-math",
    "google-analytics-for-wordpress",
    "redirection",
    "insert-headers-and-footers",
    "classic-widgets",
    "hostinger",
    "tinymce-advanced",
    "limit-login-attempts-reloaded",
    "mailchimp-for-wp",
    "essential-addons-for-elementor-lite",
    "header-footer-elementor",
    "advanced-custom-fields",
    "astra-sites",
    "loco-translate",
    "wps-hide-login",
    "wp-super-cache",
    "sg-cachepress",
    "duplicator",
    "elementskit-lite",
    "disable-comments",
    "cookie-law-info",
    "optinmonster",
    "wp-fastest-cache",
    "google-sitemap-generator",
    "better-search-replace",
    "svg-support",
    "wp-file-manager",
    "envato-elements",
    "worker",
    "loginizer",
    "one-click-demo-import",
    "wp-smushit",
    "wp-optimize",
    "regenerate-thumbnails",
    "custom-post-type-ui",
    "instagram-feed",
    "sg-security",
    "w3-total-cache",
    "complianz-gdpr",
    "ewww-image-optimizer",
    "cookie-notice",
    "all-in-one-wp-security-and-firewall",
    "wp-multibyte-patch",
    "redux-framework",
    "maintenance",
    "ultimate-addons-for-gutenberg",
    "autoptimize",
    "code-snippets",
    "safe-svg",
    "google-listings-and-ads",
    "breadcrumb-navxt",
    "imagify",
    "smart-slider-3",
    "image-optimization",
    "coming-soon",
    "better-wp-security",
    "woocommerce-gateway-stripe",
    "woocommerce-payments",
    "flamingo",
    "polylang",
    "woocommerce-services",
    "premium-addons-for-elementor",
    "yith-woocommerce-wishlist",
    "tablepress",
    "sucuri-scanner",
    "antispam-bee",
    "ninja-forms",
    "hello-dolly",
    "duracelltomi-google-tag-manager",
    "popup-maker",
    "the-events-calendar",
    "user-role-editor",
    "creame-whatsapp-me",
    "gtranslate",
    "mainwp-child",
    "under-construction-page",
    "meta-box",
    "disable-gutenberg",
    "custom-css-js",
    "woocommerce-paypal-payments",
    "kirki",
    "post-types-order",
    "ocean-extra",
    "broken-link-checker",
    "siteorigin-panels",
    "backwpup",
    "wpvivid-backuprestore",
    "wp-statistics",
    "mailpoet",
    "wp-maintenance-mode",
    "enable-media-replace",
    "header-footer-code-manager",
    "ml-slider",
    "easy-wp-smtp",
    "contact-form-cfdb7",
    "click-to-chat-for-whatsapp",
    "facebook-for-woocommerce",
    "wp-pagenavi",
    "siteguard",
    "wp-reviews-plugin-for-google",
    "so-widgets-bundle",
    "shortcodes-ultimate",
    "easy-table-of-contents",
    "taxonomy-terms-order",
    "forminator",
    "amp",
    "ga-google-analytics",
    "fluentform",
    "woocommerce-legacy-rest-api",
    "royal-elementor-addons",
    "official-facebook-pixel",
    "pixelyoursite",
    "creative-mail-by-constant-contact",
    "kadence-blocks",
    "webp-converter-for-media",
    "nextgen-gallery",
    "woo-checkout-field-editor-pro",
    "font-awesome",
    "metform",
    "mailchimp-for-woocommerce",
    "google-analytics-dashboard-for-wp",
    "limit-login-attempts",
    "happy-elementor-addons",
    "coblocks",
    "really-simple-captcha",
    "pinterest-for-woocommerce",
    "megamenu",
    "post-smtp",
    "intuitive-custom-post-order",
    "admin-menu-editor",
    "password-protected",
    "wp-reset",
    "add-to-any",
    "webp-express",
    "contact-form-7-honeypot",
    "templately",
    "header-footer",
    "breeze",
    "otter-blocks",
    "formidable",
    "copy-delete-posts",
    "custom-fonts",
    "cmb2",
    "translatepress-multilingual",
    "ad-inserter",
    "woocommerce-pdf-invoices-packing-slips",
    "wp-google-maps",
    "child-theme-configurator",
    "health-check",
    "extendify",
    "newsletter",
    "woo-variation-swatches",
    "fluent-smtp",
    "pdf-embedder",
    "fast-indexing-api",
    "stops-core-theme-and-plugin-updates",
    "leadin",
    "black-studio-tinymce-widget",
    "gdpr-cookie-compliance",
    "chaty",
    "wp-seopress",
    "nextend-facebook-connect",
    "burst-statistics",
    "jetpack-boost",
    "force-regenerate-thumbnails",
    "backuply",
    "shortpixel-image-optimiser",
    "woo-cart-abandonment-recovery",
    "wp-mail-logging",
    "wp-sitemap-page",
    "host-webfonts-local",
    "sticky-header-effects-for-elementor",
    "wp-rollback",
    "gutenberg",
    "pretty-link",
    "members",
    "wp-crontrol",
    "wpcf7-redirect",
    "table-of-contents-plus",
    "wpcf7-recaptcha",
    "userfeedback-lite",
    "simple-custom-post-order",
    "jeg-elementor-kit",
    "unlimited-elements-for-elementor",
    "aryo-activity-log",
    "favicon-by-realfavicongenerator",
    "loginpress",
    "widget-importer-exporter",
    "wp-whatsapp-chat",
    "simple-history",
    "wp-migrate-db",
    "imsanity",
    "all-404-redirect-to-homepage",
    "cyr2lat",
    "use-any-font",
    "cmp-coming-soon-maintenance",
    "blocksy-companion",
    "post-duplicator",
    "woocommerce-google-analytics-integration",
    "call-now-button",
    "iwp-client",
    "eps-301-redirects",
    "astra-widgets",
    "white-label-cms",
    "cartflows",
    "tiktok-for-business",
    "cloudflare",
    "wp-headers-and-footers",
    "wp-security-audit-log",
    "easy-fancybox",
    "honeypot",
    "popup-builder",
    "variation-swatches-woo",
    "optimole-wp",
    "generateblocks",
    "speedycache",
    "kadence-starter-templates",
    "malcare-security",
    "photo-gallery",
    "ultimate-member",
    "php-compatibility-checker",
    "mw-wp-form",
    "themeisle-companion",
    "header-and-footer-scripts",
    "pojo-accessibility",
    "cleantalk-spam-protect",
    "wp-user-avatar",
    "supreme-modules-for-divi",
    "user-switching",
    "adminimize",
    "olympus-google-fonts",
    "yith-woocommerce-compare",
    "filebird",
    "qi-addons-for-elementor",
    "complianz-terms-conditions",
    "simple-page-ordering",
    "wpfront-scroll-top",
    "ssl-insecure-content-fixer",
    "post-views-counter",
    "post-type-switcher",
    "query-monitor",
    "custom-facebook-feed",
    "woosidebars",
    "broken-link-checker-seo",
    "pagelayer",
    "redis-cache",
    "so-css",
    "page-links-to",
    "easy-google-fonts",
    "recent-posts-widget-with-thumbnails",
    "custom-post-type-permalinks",
    "customizer-export-import",
    "instant-images",
    "google-captcha",
    "autodescription",
    "search-regex",
    "wordpress-popular-posts",
    "gotmls",
    "menu-icons",
    "checkout-plugins-stripe-woo",
    "tawkto-live-chat",
    "fileorganizer",
    "ajax-search-for-woocommerce",
    "file-manager-advanced",
    "tiny-compress-images",
    "responsive-lightbox",
    "head-footer-code",
    "disable-xml-rpc",
    "iubenda-cookie-law-solution",
    "hide-my-wp",
    "omnisend",
    "simple-301-redirects",
    "acf-content-analysis-for-yoast-seo",
    "performance-lab",
    "wp-sweep",
    "advanced-google-recaptcha",
    "gosmtp",
    "layout-grid",
    "envira-gallery-lite",
    "foogallery",
    "presto-player",
    "custom-twitter-feeds",
    "ads-txt",
    "template-kit-import",
    "simple-custom-css",
    "youtube-embed-plus",
    "wp-content-copy-protector",
    "vk-all-in-one-expansion-unit",
    "statify",
    "resmushit-image-optimizer",
    "xml-sitemap-feed",
    "essential-blocks",
    "woocommerce-multilingual",
    "search-and-replace",
    "simple-lightbox",
    "cloudflare-flexible-ssl",
    "bbpress",
    "page-optimize",
    "3d-flipbook-dflip-lite",
    "addquicktag",
    "ultimate-social-media-icons",
    "block-bad-queries",
    "beaver-builder-lite-version",
    "pubsubhubbub",
    "woo-order-export-lite",
    "404-to-301",
    "skyboot-custom-icons-for-elementor",
    "page-scroll-to-id",
    "admin-site-enhancements",
    "real-cookie-banner",
    "ti-woocommerce-wishlist",
    "make-column-clickable-elementor",
    "image-widget",
    "advanced-nocaptcha-recaptcha",
    "ele-custom-skin",
    "no-category-base-wpml",
    "wp-downgrade",
    "connect-polylang-elementor",
    "disable-remove-google-fonts",
    "yet-another-related-posts-plugin",
    "mailin",
    "widget-options",
    "nginx-helper",
    "flexible-shipping",
    "codepress-admin-columns",
    "sassy-social-share",
    "widget-google-reviews",
    "cookiebot",
    "content-views-query-and-display-post-page",
    "migrate-guru",
    "https-redirection",
    "widget-logic",
    "cf7-conditional-fields",
    "menu-image",
    "jetpack-protect",
    "advanced-access-manager",
    "templates-patterns-collection",
    "show-current-template",
    "give",
    "wp-postviews",
    "google-language-translator",
    "crowdsignal-forms",
    "buddypress",
    "custom-sidebars",
    "simple-social-icons",
    "woo-stripe-payment",
    "squirrly-seo",
    "foobox-image-lightbox",
    "facebook-messenger-customer-chat",
    "yith-woocommerce-quick-view",
    "woocommerce-mercadopago",
    "404page",
    "seo-simple-pack",
    "relevanssi",
    "the-plus-addons-for-elementor-page-builder",
    "edit-author-slug",
    "wp-parsidate",
    "advanced-ads",
    "mystickymenu",
    "nitropack",
    "login-lockdown",
    "bdthemes-prime-slider-lite",
    "bdthemes-element-pack-lite",
    "contact-form-7-dynamic-text-extension",
    "robin-image-optimizer",
    "wp-consent-api",
    "email-address-encoder",
    "schema-and-structured-data-for-wp",
    "feeds-for-youtube",
    "ninjafirewall",
    "invisible-recaptcha",
    "wp-whatsapp",
    "aruba-hispeed-cache",
    "tenweb-speed-optimizer",
    "local-google-fonts",
    "polldaddy",
    "wp-all-import",
    "simple-local-avatars",
    "googleanalytics",
    "download-manager",
    "string-locator",
    "stackable-ultimate-gutenberg-blocks",
    "intelly-related-posts",
    "advanced-database-cleaner",
    "backupwordpress",
    "everest-forms",
    "q2w3-fixed-widget",
    "minimal-coming-soon-maintenance-mode",
    "modula-best-grid-gallery",
    "post-expirator",
    "a3-lazy-load",
    "disable-admin-notices",
    "social-icons-widget-by-wpzoom",
    "sticky-menu-or-anything-on-scroll",
    "tracking-code-manager",
    "pods",
    "popups-for-divi",
    "luckywp-table-of-contents",
    "microsoft-clarity",
    "enable-jquery-migrate-helper",
    "accelerated-mobile-pages",
    "manage-notification-emails",
    "shortcoder",
    "wp-staging",
    "insert-php-code-snippet",
    "woo-discount-rules",
    "wps-limit-login",
    "mollie-payments-for-woocommerce",
    "wp-force-ssl",
    "yikes-inc-easy-custom-woocommerce-product-tabs",
    "wp-asset-clean-up",
    "uk-cookie-consent",
    "rocket-lazy-load",
    "addon-elements-for-elementor-page-builder",
    "web-stories",
    "xserver-typesquare-webfonts",
    "public-post-preview",
    "indexnow",
    "custom-permalinks",
    "capability-manager-enhanced",
    "contact-form-7-simple-recaptcha",
    "vk-block-patterns",
    "duplicate-menu",
    "peters-login-redirect",
    "woolentor-addons",
    "kadence-woocommerce-email-designer",
    "the-post-grid",
    "wordpress-popup",
    "advanced-custom-fields-font-awesome",
    "add-search-to-menu",
    "widget-css-classes",
    "cache-enabler",
    "strong-testimonials",
    "check-email",
    "sidebar-manager",
    "woocommerce-checkout-manager",
    "embedpress",
    "wp-all-export",
    "tidio-live-chat",
    "hummingbird-performance",
    "vk-blocks",
    "woocommerce-products-filter",
    "async-javascript",
    "depicter",
    "heartbeat-control",
    "cyr3lat",
    "themegrill-demo-importer",
    "disable-xml-rpc-api",
    "easy-theme-and-plugin-upgrades",
    "duplicate-wp-page-post",
    "tuxedo-big-file-uploads",
    "colibri-page-builder",
    "wp-meta-and-date-remover",
    "remove-footer-credit",
    "php-code-widget",
    "persian-woocommerce",
    "wp-external-links",
    "featured-image-from-url",
    "auto-terms-of-service-and-privacy-policy",
    "onesignal-free-web-push-notifications",
    "woocommerce-menu-bar-cart",
    "login-customizer",
    "powerpack-lite-for-elementor",
    "title-remover",
    "simple-css",
    "tutor",
    "display-posts-shortcode",
    "mainwp-child-reports",
    "quick-pagepost-redirect-plugin",
    "matomo",
    "defender-security",
    "disable-json-api",
    "recent-posts-widget-extended",
    "woocommerce-extra-checkout-fields-for-brazil",
    "all-in-one-favicon",
    "disable-comments-rb",
    "list-category-posts",
    "meta-tag-manager",
    "wp-job-manager",
    "auto-image-attributes-from-filename-with-bulk-updater",
    "advanced-import",
    "ht-mega-for-elementor",
    "mobile-menu",
    "one-user-avatar",
    "yith-woocommerce-ajax-navigation",
    "wp-live-chat-support",
    "siteground-migrator",
    "wp-db-backup",
    "maxbuttons",
    "wp-nested-pages",
    "hotjar",
    "download-monitor",
    "email-encoder-bundle",
    "wp-slimstat",
    "acf-extended",
    "responsive-menu",
    "user-menus",
    "learnpress",
    "real-media-library-lite",
    "woo-smart-wishlist",
    "ocean-social-sharing",
    "woocommerce-direct-checkout",
    "kk-star-ratings",
    "resize-image-after-upload",
    "real-time-find-and-replace",
    "jupiterx-core",
    "temporary-login-without-password",
    "wp-post-page-clone",
    "insta-gallery",
    "product-import-export-for-woo",
    "anywhere-elementor",
    "side-cart-woocommerce",
    "blogvault-real-time-backup",
    "webp-uploads",
    "kliken-marketing-for-google",
    "suretriggers",
    "permalink-manager",
    "flexible-checkout-fields",
    "gravity-forms-zero-spam",
    "event-tickets",
    "go-live-update-urls",
    "official-statcounter-plugin-for-wordpress",
    "scheduled-post-trigger",
    "woo-product-feed-pro",
    "reviews-feed",
    "official-mailerlite-sign-up-forms",
    "backup-backup",
    "nav-menu-roles",
    "better-font-awesome",
    "ai-engine",
    "wp-show-posts",
    "brizy",
    "buttonizer-multifunctional-button",
    "change-wp-admin-login",
    "redirect-redirection",
    "iframe",
    "email-log",
    "wpdiscuz",
    "contact-form-7-image-captcha",
    "wp-retina-2x",
    "ninja-tables",
    "regenerate-thumbnails-advanced",
    "mailgun",
    "woocommerce-square",
    "ajax-search-lite",
    "a2-optimized-wp",
    "pymntpl-paypal-woocommerce",
    "add-from-server",
    "events-manager",
    "woo-razorpay",
    "wp-ulike",
    "default-featured-image",
    "enhanced-media-library",
    "media-cleaner",
    "email-subscribers",
    "advanced-cf7-db",
    "anti-spam",
    "woocommerce-germanized",
    "advanced-excerpt",
    "instagram-widget-by-wpzoom",
    "folders",
    "login-recaptcha",
    "option-tree",
    "automatic-translator-addon-for-loco-translate",
    "simple-sitemap",
    "woo-smart-quick-view",
    "mousewheel-smooth-scroll",
    "column-shortcodes",
    "master-slider",
    "surecart",
    "wp-add-custom-css",
    "wp-dbmanager",
    "wp-smtp",
    "two-factor",
    "omnisend-connect",
    "import-users-from-csv-with-meta",
    "filester",
    "wp-google-map-plugin",
    "interactive-3d-flipbook-powered-physics-engine",
    "csv-xml-import-for-acf",
    "bookly-responsive-appointment-booking-tool",
    "custom-typekit-fonts",
    "rvg-optimize-database",
    "wpdatatables",
    "ameliabooking",
    "simple-image-sizes",
    "mesmerize-companion",
    "kubio",
    "loftloader",
    "to-top",
    "wp-bulk-delete",
    "elementor-beta",
    "live-sales-notifications-for-woocommerce",
    "theme-my-login",
    "advanced-woo-search",
    "metricool",
    "post-and-page-builder",
    "customer-reviews-woocommerce",
    "stream",
    "sydney-toolbox",
    "missed-scheduled-posts-publisher",
    "wp-piwik",
    "wc-custom-thank-you",
    "insert-php",
    "webappick-product-feed-for-woocommerce",
    "conditional-menus",
    "wp-maximum-upload-file-size",
    "wp-clone-by-wp-academy",
    "media-library-assistant",
    "jetformbuilder",
    "independent-analytics",
    "contextual-related-posts",
    "userway-accessibility-widget",
    "boldgrid-backup",
    "blogger-importer",
    "wptouch",
    "headers-security-advanced-hsts-wp",
    "simple-author-box",
    "contact-form-entries",
    "wp-hide-security-enhancer",
    "blocks-animation",
    "page-or-post-clone",
    "timeline-widget-addon-for-elementor",
    "clearfy",
    "visual-portfolio",
    "wonderm00ns-simple-facebook-open-graph-tags",
    "dynamicconditions",
    "smtp-mailer",
    "wp-fail2ban",
    "woo-smart-compare",
    "imagemagick-engine",
    "if-menu",
    "facebook-pagelike-widget",
    "wp-2fa",
    "users-customers-import-export-for-wp-woocommerce",
    "site-reviews",
    "exclusive-addons-for-elementor",
    "portfolio-post-type",
    "captcha-code-authentication",
    "boldgrid-easy-seo",
    "disable-xml-rpc-pingback",
    "wp-carousel-free",
    "colorlib-login-customizer",
    "user-registration",
    "astra-import-export",
    "mailchimp",
    "disable-emojis",
    "sakura-rs-wp-ssl",
    "internal-links",
    "wpcat2tag-importer",
    "navz-photo-gallery",
    "shortcode-in-menus",
    "re-add-underline-justify",
    "customizer-search",
    "wordfence-login-security",
    "ultimate-dashboard",
    "weight-based-shipping-for-woocommerce",
    "button-contact-vr",
    "spotlight-social-photo-feeds",
    "wp-table-builder",
    "variation-swatches-for-woocommerce",
    "zarinpal-woocommerce-payment-gateway",
    "perfect-woocommerce-brands",
    "contact-form-7-add-confirm",
    "embed-any-document",
    "wp-members",
    "categories-images",
    "wp-letsencrypt-ssl",
    "yith-woocommerce-catalog-mode",
    "print-invoices-packing-slip-labels-for-woocommerce",
    "auto-post-thumbnail",
    "weglot",
    "html-editor-syntax-highlighter",
    "woo-advanced-shipment-tracking",
    "upload-max-file-size",
    "pages-with-category-and-tag",
    "hcaptcha-for-forms-and-more",
    "404-to-homepage",
    "cms-tree-page-view",
    "drag-and-drop-multiple-file-upload-contact-form-7",
    "genesis-enews-extended",
    "getwid",
    "woocommerce-ajax-filters",
    "companion-auto-update",
    "blog2social",
    "ooohboi-steroids-for-elementor",
    "woocommerce-currency-switcher",
    "easy-accordion-free",
    "google-calendar-events",
    "theme-editor",
    "search-exclude",
    "wp-extra-file-types",
    "wp-edit",
    "simple-tags",
    "metronet-profile-picture",
    "simple-cloudflare-turnstile",
    "advanced-popups",
    "disqus-comment-system",
    "woo-product-filter",
    "wp-store-locator",
    "activecampaign-subscription-forms",
    "visualcomposer",
    "advanced-iframe",
    "advanced-custom-fields-table-field",
    "quick-featured-images",
    "phoenix-media-rename",
    "image-hover-effects-addon-for-elementor",
    "booking",
    "wp-polls",
    "smart-custom-fields",
    "bing-webmaster-tools",
    "order-import-export-for-woocommerce",
    "simple-share-buttons-adder",
    "profile-builder",
    "auxin-portfolio",
    "woocommerce-google-adwords-conversion-tracking-tag",
    "qi-blocks",
    "addons-for-elementor",
    "wp-yandex-metrika",
    "instagram-slider-widget",
    "hide-page-and-post-title",
    "fluent-crm",
    "wp-add-mime-types",
    "ultimate-blocks",
    "searchwp-live-ajax-search",
    "ultimate-addons-for-contact-form-7",
    "dokan-lite",
    "virtue-toolkit",
    "printful-shipping-for-woocommerce",
    "api-key-for-google-maps",
    "revision-control",
    "woo-permalink-manager",
    "postmark-approved-wordpress-plugin",
    "yoast-test-helper",
    "zapier",
    "jwt-authentication-for-wp-rest-api",
    "fast-velocity-minify",
    "calculated-fields-form",
    "underconstruction",
    "category-posts",
    "simple-banner",
    "site-mailer",
    "dynamic-visibility-for-elementor",
    "wp-maintenance",
    "athemes-starter-sites",
    "safe-redirect-manager",
    "reveal-ids-for-wp-admin-25",
    "all-in-one-event-calendar",
    "dominant-color-images",
    "custom-taxonomy-order-ne",
    "bold-page-builder",
    "wp-htaccess-editor",
    "super-progressive-web-apps",
    "zipaddr-jp",
    "export-media-with-selected-content",
    "woocommerce-advanced-free-shipping",
    "export-all-urls",
    "wp-rss-aggregator",
    "remove-category-url",
    "sina-extension-for-elementor",
    "ultimate-category-excluder",
    "wp-revisions-control",
    "what-the-file",
    "wp-recipe-maker",
    "form-maker",
    "koko-analytics",
    "search-filter",
    "feedzy-rss-feeds",
    "display-php-version",
    "addons-for-divi",
    "widget-context",
    "simply-schedule-appointments",
    "wp-cloudflare-page-cache",
    "easy-digital-downloads",
    "clever-fox",
    "real-time-auto-find-and-replace",
    "woocommerce", "akismet", "jetpack", 'shortcodes-ultimate','ultimate-member','profile-builder','cleantalk-spam-protect','wpcf7-recaptcha','bit-form','wpcf7-redirect','ultimate-addons-for-contact-form-7','cf7-conditional-fields','ninja-forms','formidable','forminator','wpforms-lite','contact-form-7','wp-native-php-sessions','wp-bannerize-pro','upi-qr-code-payment-for-woocommerce','wordfence','captcha-code-authentication','tablepress','insert-headers-and-footers','elementor','wpo-tweaks','team-free','s2member','wp-members','tlp-team','paid-member-subscriptions','simple-membership','members','ultimate-member','armember-membership','buddypress-members-only','user-switching','tarteaucitronjs','cookies-and-content-security-policy','tenweb-speed-optimize','wp-rocket','autoptimize','wp-fastest-cache','w3-total-cache','wp-super-cache','litespeed-cache','comet-cache','hummingbird-performance','wp-optimize','wp-sweep','wp-dbmanager','updraftplus','backwpup','all-in-one-wp-migration','duplicator','backupbuddy','wp-migrate-db','wp-staging','wp-rollback','wp-rollback-core','wp-rollback-theme','wp-rollback-plugin','wp-rollback-wp','wp-rollback-wpml','wp-rollback-woocommerce','wp-rollback-woocommerce-gateway-stripe','wp-rollback-woocommerce-gateway-paypal-express-checkout','wp-rollback-woocommerce-gateway-paypal-powered-by-braintree', 'advanced-custom-fields','custom-post-type-ui',
]  # Example plugins

def download_plugin(plugin_slug, dest_dir):
    """Download and extract a WordPress plugin."""
    print(f"Downloading plugin: {plugin_slug}")
    response = requests.get(f"{DOWNLOAD_URL}{plugin_slug}.zip", stream=True)
    if response.status_code == 200:
        zip_path = os.path.join(dest_dir, f"{plugin_slug}.zip")
        with open(zip_path, "wb") as file:
            file.write(response.content)
        with zipfile.ZipFile(zip_path, "r") as zip_ref:
            zip_ref.extractall(dest_dir)
        os.remove(zip_path)
        print(f"Downloaded and extracted {plugin_slug}.")
    else:
        print(f"Failed to download {plugin_slug} (Status: {response.status_code}).")

def update_plugins(repo_dir, plugin_list):
    """Update plugins in the local repository."""
    cache = load_cache()
    plugins_dir = os.path.join(repo_dir, "plugins")
    
    # Create plugins directory if not exists
    os.makedirs(plugins_dir, exist_ok=True)
    
    # Track if any changes were made
    changes_made = False
    
    for plugin_slug in plugin_list:
        print(f"Processing plugin: {plugin_slug}")
        try:
            response = requests.get(f"{WORDPRESS_API_URL}?action=plugin_information&request[slug]={plugin_slug}")
            
            # Check for rate limits in the response headers
            if response.status_code == 429:  # Too many requests
                reset_time = int(response.headers.get('X-RateLimit-Reset', time.time() + 60))  # Retry after reset time
                wait_time = reset_time - int(time.time()) + 1  # Add 1 second buffer
                print(f"Rate limit exceeded. Waiting for {wait_time} seconds...")
                time.sleep(wait_time)
                continue  # Retry after sleeping
            
            if response.status_code != 200:
                print(f"Failed to fetch plugin info for {plugin_slug}. Skipping... (Status code: {response.status_code})")
                continue
            
            plugin_data = response.json()
            latest_version = plugin_data.get("version")
            plugin_path = os.path.join(plugins_dir, plugin_slug)
            
            # Skip download if version is up-to-date
            if cache["plugins"].get(plugin_slug) == latest_version:
                print(f"{plugin_slug} is already up-to-date.")
                continue
            
            # Remove old plugin folder and download the latest version
            if os.path.exists(plugin_path):
                shutil.rmtree(plugin_path)
            
            download_plugin(plugin_slug, plugins_dir)
            
            # Update cache
            cache["plugins"][plugin_slug] = latest_version
            save_cache(cache)
            
            # Mark that changes were made
            changes_made = True
            
            # Respect rate limits
            time.sleep(TIME_INTERVAL)
        
        except Exception as e:
            print(f"Error processing plugin {plugin_slug}: {e}")
            continue
    
    # Commit changes to the repository
    try:
        repo = Repo(repo_dir)
        
        # Stage all changes
        repo.git.add(A=True)
        
        # Check if there are any changes to commit
        if changes_made and repo.is_dirty():
            print("Changes detected. Preparing to commit...")
            
            # Configure git user for the commit
            with repo.config_writer() as git_config:
                git_config.set_value("user", "name", "GitHub Actions Bot")
                git_config.set_value("user", "email", "actions@github.com")
            
            # Commit changes
            commit_message = f"Update WordPress plugins: {', '.join(plugin_list)}"
            repo.index.commit(commit_message)
            
            print("Committing changes...")
            
            # Push changes with more robust error handling
            try:
                origin = repo.remote(name='origin')
                push_result = origin.push()
                
                # Check push result
                for info in push_result:
                    if info.flags & info.ERROR:
                        print(f"Push failed: {info.summary}")
                        raise Exception(f"Git push error: {info.summary}")
                
                print("Successfully pushed changes.")
            
            except Exception as push_error:
                print(f"Error during push: {push_error}")
                # Optionally, you could re-raise the exception if you want the action to fail
                # raise
        
        else:
            print("No changes to commit.")
    
    except Exception as repo_error:
        print(f"Repository error: {repo_error}")
        # Optionally, you could re-raise the exception if you want the action to fail
        # raise

    return changes_made

def main():
    """Main script execution."""
    print("Checking if repository exists...")

    # Ensure REPO_DIR exists and is empty before cloning
    if os.path.exists(REPO_DIR):
        shutil.rmtree(REPO_DIR)
    
    print("Cloning repository...")
    Repo.clone_from(REPO_URL, REPO_DIR)

    print("Updating plugins...")
    plugin_list = get_plugin_list()
    update_plugins(REPO_DIR, plugin_list)

if __name__ == "__main__":
    main()
