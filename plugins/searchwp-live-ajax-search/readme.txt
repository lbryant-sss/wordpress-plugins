=== SearchWP Live Ajax Search ===
Contributors: jchristopher, pavloopanasenko
Tags: search, live, ajax
Requires at least: 4.8
Tested up to: 6.8
Stable tag: 1.8.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Template powered live search for any WordPress theme. Does not require SearchWP, but will utilize it if available.

== Description ==

**Does not require** [SearchWP](https://searchwp.com/?utm_source=WordPress&utm_medium=Plugin+Readme+Requirement+Link&utm_campaign=Live+Ajax+Search&utm_content=SearchWP), but will utilize it if available. [Full documentation](https://searchwp.com/extensions/live-search/) is available at searchwp.com.

SearchWP Live Ajax Search enables AJAX powered live search for your search forms. Designed to be a developer's best friend, SearchWP Live Ajax Search aims to stay out of your way but at the same time allowing you to customize everything about it. It's set up to work with any WordPress theme and uses a template loader to display results. The template based approach allows you to seamlessly customize your SearchWP Live Search implementation without messing with dozens of cluttered options.

= Works best with SearchWP (but SearchWP is not necessary) =

SearchWP Live Ajax Search *is best utilized* in conjunction with [SearchWP](https://searchwp.com/?utm_source=WordPress&utm_medium=Plugin+Readme+Best+Link&utm_campaign=Live+Ajax+Search&utm_content=SearchWP), but **it is not required**. If SearchWP is installed and activated, SearchWP Live Ajax Search can be customized to use any of your search engines *per search form*.

= Customization =

You can customize the implementation of SearchWP Live Ajax Search to a great degree, including any number of developer-defined configurations. The results are based on a template loader, allowing SearchWP Live Ajax Search to stay out of your way and letting you write the results template as you would any other WordPress theme file.

*Everything* is powered by adding a single HTML5 data attribute (<code>data-swplive="true"</code>) to the input field of your search form. This happens automagically out of the box for any search forms generated from `get_search_form()`.

= Widget support =

SearchWP Live Ajax Search ships with a Widget allowing you to insert live search forms wherever you'd like.

== Installation ==

1. Download the plugin and extract the files
1. Upload `searchwp-live-search` to your `~/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Optionally customize the configuration: [full documentation](https://searchwp.com/extensions/live-search/)
1. Optionally customize the results template: [full documentation](https://searchwp.com/extensions/live-search/)

== Frequently Asked Questions ==

= Documentation? =

Of course! [Full documentation](https://searchwp.com/extensions/live-search/)

= How do I add live search to my search form? =

SearchWP Live Ajax Search will **automatically** enable itself on any search forms generated with `get_search_form()`. You can prevent that with the following filter:

`add_filter( 'searchwp_live_search_hijack_get_search_form', '__return_false' );`

If you would like to manually enable SearchWP Live Ajax Search on a custom search form, simply add the following data attribute to the `input` you want to hook: <code>data-swplive="true"</code>

= How are search results generated? =

By default, SearchWP Live Ajax Search uses the default SearchWP Search Engine if you are using SearchWP. If you don't have SearchWP, native WordPress search results are provided. If you would like to customize which search engine SearchWP uses, simply add the following attribute to the form `input`: `data-swpengine="supplemental"` replacing 'supplemental' with your desired search engine name.

= How do I customize the results template =

SearchWP Live Ajax Search uses a template loader. In the plugin folder you will find a `templates` folder which includes `search-results.php` — that is what's used out of the box to output search results. To customize that output, simply create a folder called `searchwp-live-ajax-search` **in your theme directory** and copy `search-results.php` into that folder. SearchWP Live Ajax Search will then *use that file* instead of the one that shipped with the plugin, and you can customize it as you would other theme template files.

SearchWP Live Ajax Search also outputs two sets of styles. The primary set of styles simply preps the results wrapper to be positioned properly. The second set of styles controls the visual appearance. This abstraction was made to ensure customization is as straighforward as possible. You can disable the default 'theme' by dequeueing the applicable stylesheet, and you can also disable the foundational CSS as well. More information available in [the documentation](https://searchwp.com/extensions/live-search/#customizing-results).

= How do I customize the spinner =

SearchWP Live Ajax Search uses a filter — <a href="https://searchwp.com/extensions/live-search/#searchwp_live_search_configs"><code>searchwp_live_search_configs</code></a> — that allows you to fully customize the configuration used. Simply add a new key to the array passed through that filter, customizing the `default` values to whatever you want.

== Screenshots ==

1. SearchWP Live Ajax Search Results dropdown
2. SearchWP Live Ajax Search Settings page
3. SearchWP Live Ajax Search Forms settings page
4. SearchWP Live Ajax Search Forms embed options

== Changelog ==

= 1.8.6 =
* Updates deprecated jQuery methods.
* Improved results' dropdown alignment with Gutenberg search block.
* Adds minified assets for frontend.

= 1.8.5 =
* Fixes integration with SearchWP Modal Search Form plugin.

= 1.8.4 =
* Adds support for SearchWP multisite search.
* Improves search results dropdown role attribute, for better accessibility.
* Adjust results width to match Gutenberg form width when the search button is inside.
* Fixes the display of the results dropdown within a custom parent element.

= 1.8.3 =
* Prevents issues with caching plugins in some cases.
* Adds support for loading a custom template from a user specified directory path.

= 1.8.2 =
* Fixes integration with Relevanssi Premium.

= 1.8.1 =
* Fixes incorrect results when using a Live Search custom template in some cases.

= 1.8.0 =
* Adds Search Forms.
* Adds support for SearchWP custom search sources.
* Improves existing and adds new customizations to the Live Ajax Search settings page.
* Updates translation files.
* Fixes Settings link location in the plugins list when SearchWP is activated.

= 1.7.6 =
* Changes settings page appearance and menu structure to work seamlessly with a current version of SearchWP.

= 1.7.5 =
* Changes visual style of admin settings page to match the current version of SearchWP.

= 1.7.4 =
* Fixes widget Advanced settings button disappear without revealing the settings in some cases.
* Fixes body content override if the custom parent element is set in the configuration.

= 1.7.3 =
* Adds In-plugin notification system to get the latest updates from SearchWP.

= 1.7.2 =
* Adds a compatibility with the upcoming version of the SearchWP Modal Search Form plugin.
* Fixes an issue with a legacy version of the SearchWP plugin.

= 1.7.1 =
* Fixes the translations of the plugin.

= 1.7.0 =
* Adds new Settings admin page to control the behavior of the plugin easier.
* Introduces significant code refactoring to improve performance.

= 1.6.3 =
* Improves sanitization of the 'swpengine' search field argument.
* Standardizes direct access restrictions in classes throughout the plugin.

= 1.6.2 =
* Limits the arguments passed to the query when used with the WordPress native search.

= 1.6.1 =
* Fixes PHP Notice introduced in 1.6.0

= 1.6.0 =
* Adds support search form block in block editor (Gutenberg)

= 1.5.0 =
* Adds support for post_status, post_type, and posts_per_page query vars when not using SearchWP
* Updates translation source, updates translations with fixed textdomain
* Refactored bundle process
* Updates bundle dependencies

= 1.4.6 =
* Adds compatibility for SearchWP 4

= 1.4.5 =
* Fixes an issue with quoted phrase support
* Fixes an issue with post types that are excluded from search when registered (applies only to searches with SearchWP)

= 1.4.4 =
* Fixes an issue with bundle.js in some cases

= 1.4.3 =
* Improves SearchWP compatibility by suppressing filters when SearchWP performs the search
* Compatibility fix with SearchWP Modal Search Form

= 1.4.2 =
* Fixes an issue (JavaScript error) when a custom configuration disables the spinner

= 1.4.1 =
* Fixes an issue that may have prevented spinner customizations from being applied correctly

= 1.4 =
* Removes post type from default results template
* Fixes an issue with HTML entities in search string
* Updates translation source and translations
* Updates bundler build process
* Updates a11y where applicable
* Automatically cancels pending searches when search input is changed

= 1.3.1 =
* Fixes typo in results template function call

= 1.3.0 =
* Many a11y improvements (props @geoffreycrofte)
* Adds message when minimum characters have not been reached
* Added German translation (props @stefan-meier)
* Added Polish translation (props @boguslawski-piotr)
* Added Dutch translation (props Stefan G.)
* Updated translation source

= 1.2.0 =
* Reworked build process to be more straightforward
* Support script debugging (props mgratch)
* Remove form action requirement to better integrate with other code (props mgratch)
* Fixed an issue that prevented search from firing when re-entered too fast (props pierrestoffe)

= 1.1.8 =
* Added configuration option to prevent AJAX cancellation when Enter key is pressed
* Added some actions to make customization easier
* Added filter to customize location of results template

= 1.1.7 =
* Fixed an issue with missing engine name

= 1.1.6 =
* Fixed an issue that prevented multiple live search instances on a single page from using different engines

= 1.1.5 =
* Fixed an issue where <code>results_destroy_on_blur</code> was not properly applied if a custom configuration was used but that property was not defined in the custom configuration

= 1.1.4 =
* Added a number of JavaScript events to facilitate further extension/integration

= 1.1.3 =
* Fixed an issue with the build process which introduced an outdated version of the plugin JavaScript assets

= 1.1.2 =
* No longer limit results to built in post types
* Added <code>results_destroy_on_blur</code> config option to prevent automatic removal of results window when clicking outside results pane

= 1.1.1 =
* Fixed an issue that prevented SearchWP Supplemental engines from showing up in the Widget config
* Added French translation
* Added Serbian translation

= 1.1 =
* Define default post statuses when using WordPress native search
* New filter `searchwp_live_search_query_args` to manipulate query args before searches
* Fixed an offset when positioning results on top of the search field

= 1.0.6 =
* PHP Warning cleanup

= 1.0.5 =
* New action: `searchwp_live_search_alter_results`
* Adds Relevanssi support (based on Dave's WordPress Live Search's implementation)

= 1.0.4 =
* Corrected the default results template folder name to be `searchwp-live-ajax-search` as is in the documentation
* Improvement: hide the results box when query is emptied (props Lennard Voogdt)
* Fixed an issue with Media not showing in results when integrated with SearchWP

= 1.0.3 =
* Fixed an issue where a false set of no results would be returned (props Lennard Voogdt)

= 1.0.2 =
* Resolved an issue where hitting Enter/Return prevented the search query from being passed to the results page
* Fixed potential false positive for DOING_AJAX (props justinsainton)
* Removed unnecessary call to get_the_ID() in the default results template (props justinsainton)
* Added escaping to permalink and post type name in the default results template (props justinsainton)
* Utilize a WordPress core translated string instead of a custom one (props justinsainton)
* Increase the priority for the get_search_form filter so as to accommodate existing filters

= 1.0.2 =
* Added Serbo-Croatian translation (props Andrijana Nikolic)

= 1.0.1 =
* Fixed a directory URL issue
* Fixed an indexOf JavaScript error

= 1.0 =
*  Initial release!
