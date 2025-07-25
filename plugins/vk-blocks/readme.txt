=== VK Blocks ===
Contributors: vektor-inc,kurudrive,naoki0h,nc30,una9,kaorock72,rickaddison7634,mimitips,mthaichi,shimotomoki,sysbird,chiakikouno,doshimaf,mtdkei
Donate link:
Tags: Gutenberg,FAQ,alert
Requires at least: 6.5
Tested up to: 6.8
Stable tag: 1.108.0.1
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This is a plugin that extends Gutenberg's blocks.

== Description ==

This is a plugin that extends Gutenberg's blocks.

[Blocks]
* Alert
* Balloon
* Border Box
* Button
* Classic FAQ
* New FAQ
* Flow
* Heading (not recommended)
* Icon
* Icon Outer
* Page Content
* PR Blocks (not recommended)
* PR Content (not recommended)
* Responsive Spacer
* Staff (not recommended)
* Page list from ancestor
* Slider
* Visual Embed
* Accordion [ Pro ]
* Animation [ Pro ]
* Archive list [ Pro ]
* Blog Card [ Pro ]
* Breadcrumb [ Pro ]
* Button Outer [ Pro ]
* Card [ Pro ] (not recommended)
* Category Badge [ Pro ]
* Child page list [ Pro ]
* Dynamic Text ( Post Type name / Ancestor Page name ) [ Pro ]
* Fixed Display [ Pro ]
* Icon Card [ Pro ] (not recommended)
* Post list [ Pro ]
* Post list Slider [ Pro ]
* New Badge [ Pro ]
* Selected Post List [ Pro ]
* Step [ Pro ]
* Tab [ Pro ]
* Table of Contents [ Pro ]
* Taxonomy [ Pro ]
* Timeline [ Pro ]
* Grid Column [ Pro ]
* Grid Column Card [ Pro ]
* Outer [ Pro ]

[Extensions]
* Hidden extension
* Highlighter
* Inline Font Size
* Margin extension
* Nowrap
* Responsive BR
* Columns direction
* Custom CSS [ Pro ]
* Link toolbar

[ Settings ]
* Balloon
* Custom Format [ Pro ]
* Custom Block Style [ Pro ]
* Common Margin
* Load Separate
* FAQ Block [ Pro ]
* Custom CSS [ Pro ]
* Block Manager
* Block Style Manager

[ Tools ]
* Import Export

[ Editing and correction ]

The source code of this plugin is below.
https://github.com/vektor-inc/vk-blocks

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==


== Screenshots ==

1. VK Blocks can be selected from the VK Blocks Panel.
1. VK Blocks examples.

== Changelog ==

= 1.108.0 =
[ Add function ][ Post List (Pro) / Post List Slider (Pro) ] We have added items within the past year to the date filtering.
[ Add function ][ Blog Card (Pro) ] Added internal link search and suggestions feature.
[ Specification change ][ Slider ] Added recommendation message and control for Zoom Animation with Fade effect.
[ Specification change ][ Hidden extension ] Excluded functionally incompatible blocks from hidden settings options.
[ Bug fix ][ Slider / Post List Slider (Pro) ] Fixed an issue where setting display time to 0 caused the slider to pause instead of continuous motion.

= 1.107.0 =
[ Specification change ][ Button ] Migrated link functionality to use the enhanced Link Toolbar component with improved UX, accessibility features, and SEO options.
[ Bug fix ][ Slider / Post List Slider (Pro) ] Fixed XSS vulnerability.
[ Bug fix ][ Table of Contents (Pro) ] Limit heading extraction to heading blocks in site-wide settings.
[ Bug fix ][ Table of Contents (Pro) ] Fixed H2 selection issue in heading level settings and improved code structure.
[ Bug fix ][ Table of Contents (Pro) ] Fixed an issue where clicking the OPEN/CLOSE button on second or subsequent table of contents blocks would affect the first block when multiple blocks were placed on the same page.
[ Bug fix ][ Table of Contents (Pro) ] Fixed license key exposure vulnerability in frontend and implemented XSS protection.

= 1.106.1 =
[ Bug Fix ][ Table of Contents (Pro) ] Fixed a security issue.

= 1.106.0 =
[ Add function ][ Slider ] Added zoom animation feature.
[ Specification change ][ Table of Contents (Pro) ] Removed the restriction that prevented nested headings from being displayed in the list.
[ Bug fix ][ Button ] Add fallback to vk color primary.
[ Bug fix ][ Core List ] Fixed an issue where list items were not applying colors selected from Japanese-named color palettes and semi-transparent colors.
[ Bug Fix ][ Slider ] Fix an issue where the inner element of a slide item does not match the content width in the edit screen.

= 1.105.1 =
[ Bug Fix ][ Grid Column Card (Pro) ] Fixed the column padding adjustment slider's initial state functionality issue.
[ Other ][ Highlighter ] Resolved circular dependency issue.

= 1.105.0 =
[ Add function ][ Core Post title ] Added top and bottom margin settings to the toolbar.
[ Add function ][ Tab (Pro) ] Added support for inserting FontAwesome icons into tab labels.
[ Add function ][ Table of Contents (Pro) ] Added heading level control (global and per block).
[ Add function ][ Table of Contents (Pro) ] Added the ability to exclude individual headings.
[ Add function ][ Grid Column Card (Pro) ] Added the ability to select the border style from "Solid", "Dashed", or "Dotted"
[ Add function ][ Alert ] Added option to position the icon at the top on mobile devices.
[ Add function ][ Classic FAQ ] Added support for converting Classic FAQ block to new FAQ block from the toolbar.
[ Add function ][ Highlighter ] Support vertical writing mode direction.
[ Specification change ][ Blog Card (Pro) ] Changed the specification to not generate a blog card for URLs where is_embeddable is false
[ Specification change ][ Table of Contents (Pro) ] Removed the restriction that prevented nested headings from being displayed in the list.
[ Bug Fix ] Fixed an issue where Japanese characters in custom color names in block themes caused class names to break, preventing colors from being applied.
[ Bug Fix ][ Category Badge (Pro) ] Fixed missing badge in Query Loop when no category or term was selected.
[ Bug Fix ][ Slider Item ] Reset margin-block-start by core.

= 1.104.0 =
[ Specification change / Bugfix ][ Grid Column Card (Pro) ] Removed Edit mode — only the selected item block is now editable.
[ Bug fix ][ Tab ] Fixed tab items not being properly selected when clicked from the list view.
[ Other ][ PR Content ][ Staff ][ Card ][ Icon Card ]Added "not recommended" to block name.

= 1.103.0 =
[ Add function ][ Core Excerpt ] Added top and bottom margin settings to the toolbar.
[ Add function ][ Outer (Pro) ] Added background offset feature.
[ Design Bug Fix ][ Outer (Pro) ] Removed unintended borders left and right separators.
[ Design Bug Fix ][ Balloon ] Fixed an issue where text inside balloon blocks unintentionally inherited the border color when using a color palette.
[ Other ][ Tab (Pro) / Tab Item ] Added block description
[ Other ][ Outer (Pro) / Slider ] Refactored the numeric value & unit input for each device into a shared component

= 1.102.0 =
[ Design Bug Fix ][ Outer (Pro) ] Fixed unexpected margin affecting background color display due to layout flow styles in WordPress 6.8.
[ Design Bug Fix ][ Outer (Pro) ] Removed unintended borders above and below separators.
[ Bug Fix ][ Pro ] When VK Blocks Pro is activated, VK Blocks should be automatically deactivated.

= 1.101.0 =
[ Add function ][ Dynamic Text (Pro) ] Vertical writing is now available.
[ Specification change ][ Dynamic Text (Pro) ] Allows i-tags in custom field link text.
[ Bug Fix ][ Step (Pro) ] Fix it so that leaving the starting number for a step blank does not result in an error.

= 1.100.0 =
[ Add function ][ Dynamic Text (Pro) ] When you set a link URL in the custom field display, you can now specify the link text.
[ Specification change ][ Dynamic Text (Pro) ] Disable link on edit screen.
[ Other ][ Table of Contents New (Pro)] Improve performance and reliability.
[ Other ][ Some Block ] Compatible for WordPress 6.8
[ Bug Fix ][ Flow ] Fix selection state not visible when block is selected (change arrow from CSS to HTML in editor)

= 1.99.0 =
[ Add function ][ Dynamic Text (Pro) ] Add "Post Slug" as a display element.
[ Add function ][ Column / Cover ] Added noreferrer, nofollow, and link description options to the link feature.
[ Specification change ][ Some Block ] Change the editor component from Button Group to Toggle Group Control.
[ Bug Fix ][ Blog Card (Pro) ] Fix PHP 8.x warning caused by missing 'clearCache' key.
[ Bug Fix ][ Flow ] Fixed image overflow on the frontend and arrow misalignment in the editor.
[ Bug Fix ][ Core Group ] Fixed blank editor issue when layout is undefined or invalid in some themes.

= 1.98.0 =
[ Specification change ][ Slider ] Changed slider height minimum from 40px to 24px.

= 1.97.0 =
[ Add function ][ Animation (Pro) ] Added the "Slide Down" effect to the Animation effect options.
[ Add function ][ Post List (Pro) / Post List Slider (Pro) ] Added the ability to set sticky posts to display.
[ Add function ][ Outer (Pro) / Slider ] Added a replace button to allow changing the background image without deleting it.
[ Add function ][ Button ] Added support for typography settings.
[ Specification change ][ Visual Embed ] Added aligncenter when iframeWidth is set to a value other than "100%".
[ Design Bug Fix ][ Table ] Fixed overlapping borders when using vertical cells, and ensured the border color follows the theme settings.
[ Editor Design Bug Fix ][ Animation (Pro) / Fixed Display (Pro) / New Badge (Pro) / Category Badge (Pro) ] Fixed elements not becoming semi-transparent when set to be hidden in the editing screen.
[ Editor Design Bug Fix ][ Balloon ] Fixed a layout distortion issue when enabling the vk_hidden setting in the editor.
[ Bug fix ][ Ancestor Page List ] Fixed a warning message that appeared on the console when placing blocks.

= 1.96.2 =
[ Bug fix ][ Post list (Pro) / Post list slider (Pro) / Tab (Pro) / Slider ] Fixed errors occurring regardless of whether the editing screen environment is in an iframe and improved the stability of feature performance.
[ Bug fix ][ Post list (Pro) / Post list slider (Pro) ] Fixed the text displayed when no posts are available by removing the 's' notation and standardizing the first letter to lowercase.
[ Other ] Rollback 1.96.0

= 1.96.1 =
[ Other ] Rollback 1.95.0.3

= 1.96.0 =
[ Add function ][ Slider / group / Icon / Grid Column Card (Pro) ] Added noreferrer, nofollow, and link description options to the link feature.
[ Specification change ][ Animation (Pro) / Fixed Display (Pro) / Outer (Pro) ] Added support for layout.allowJustification: false, allowing full-width/wide alignment in block themes where wrapped blocks previously did not appear as full-width or wide in the editor.
[ Specification change ] Fixed the zoom-out toggle not always displaying in the editor toolbar (updated blocks.json API version from 2 to 3).
[ Editor Design Bug Fix ][ Tab (Pro) ] Fixed the issue where the block inserter button (block-list-appender) inside the tab block (.vk_tab) was affecting other block inserter buttons.
[ Editor Design Bug Fix ][ Grid Column (Pro) ] Fixed an issue where the 'is-vertical' layout of grid column items was not displayed vertically.
[ Bug fix ][ Icon ] Fixed an unwanted bottom margin appearing.

= 1.95.0 =
[ Add function ][ Icon / Slider ] Added noreferrer, nofollow, and link description options to the link feature.
[ Add function ][ Core Table ] Add table cell vertical setting.
[ Specification Change ][ Page Content ] Modify the specification so that the display is shown only when the referenced page is public.
[ Editor Design Bug Fix ][ Grid Column (Pro) ] Fixed an issue where the 'is-vertical' layout of grid column items was not displayed vertically.

= 1.94.2 =
[ Bug fix / Specification Change ][ Visual Embed ] Strengthened validation for XSS protection and restricted the range of allowed URLs.

= 1.94.1 =
[ Specification change ][ Visual Embed ] Add margin:0; to iframe tag.
[ Add function ][ Outer (Pro) ] Added the ability to set a focal point for background images.

= 1.94.0 =
[ Add Block ][ Visual Embed ] Add Visual Embed block.
[ Add function ][ Grid Column (Pro) ] Added noreferrer, nofollow, and link description options to the link feature.
[ Add function ][ Fixed Display (Pro) / Outer (Pro) ] Added transforms settings to wrap and unwrap for util.
[ Specification change ][ Animation (Pro) ] Fixed transforms settings to wrap and unwrap for util.
[ Bug fix ][ Fixed Display (Pro) ] When the number of seconds until display is zero, it will not be displayed again until the browser is closed, but this has been fixed.
[ Bug fix ][ Fixed Display (Pro) ] Fixed a problem with enabling "Display on Scroll" under certain conditions that caused a recovery error.
[ Design Bug Fix ] Change CSS order.
[ Design Bug fix ][ Slider ][ Post List Slider (Pro) ] Fix an issue where full width alignment does not work in tt4 and tt5.
[ Design Bug Fix ][ Post List Slider (Pro) ] Added bottom margin when the display type is set to "Media" to create spacing between the pagination and the block.
[ Editor Design Bug Fix ][ Grid Column Card (Pro) ]Fixed an issue where buttons in vk_gridcolcard_item_footer were not expanding their width when wrapped in an Outer block.
[ Specification Change ] Load VK Components from Composer.

= 1.93.2 =
[ Bug fix ][ Post List Slider (Pro) ] Fixed sliding behavior when multiple post-list-slider blocks are present.

= 1.93.1 =
[ Bug fix ][ Post List (Pro) / Post List Slider (Pro) ] Fix the issue where posts are always displayed from the first post regardless of the "Display from the first post always" setting.
[ Bug fix ][ Post List (Pro) / Post List Slider (Pro) ] Fixed missing "No relevant posts." message when no post types are selected.
[ Bug fix ][ Tab (Pro) ] Added a CSS class to improve style priority with the Lightning theme.
[ Bug fix ][ Core List / Border Box ] Improved handling of List's is-style-default style and Border Box color.
[ Specification change ][ Outer (Pro) ] Removed the aria-label attribute from links, and updated to exclude target and rel attributes when they are empty.
[ Design Bug Fix ][ Core List ][ Border Box ] Improved handling of List's is-style-default style and Border Box color.

= 1.93.0 =
[ Add function ][ Link toolbar ][ Outer (Pro) ] Added settings for "rel" and link description.
[ Add function ][ Balloon ] Add default avatar.
[ Other ][ Post list (Pro) / Post list slider (Pro) ] Refactored "Display Conditions" by modularizing them into components.
[ Bug fix ][ Post List (Pro) ][ Post List Slider (Pro) ] Fixed missing "No relevant posts." message when no post types are selected.
[ Bug fix ][ Table of Contents (Pro) ] Fix line break.
[ Bug fix ][ Category Badge (Pro) ] Exclude post_tag.
[ Bug fix ][ Post List Slider (Pro) ] Fix 404 Error of Load Module at free version.

= 1.92.1 =
[ Bug fix ][ Post List (Pro) ] Remove white space above title
[ Design Bug Fix ][ Post list Slider (Pro) ] Fixed an issue where post list did not have consistent heights.
[ Design Bug Fix ][ VK Button ] Fixed an issue where the text of a button with an icon was dropping in a column. Reset the word-break property to its initial value for better compatibility in Firefox.

= 1.92.0 =
[ Add Block ] Add Post list Slider block.
[ Add function ][ Fixed Display (Pro) ] Added a timer feature to control block appearance/disappearance and a "Do not show again for returning visitors" option. Session Storage is used, and no personal data is collected or tracked. Since Session Storage is used, the block may reappear after the browser is closed or in private browsing mode.
[ Bug Fix ][ Grid Column Card (Pro) ] Fixed only dots being displayed as separators.
[ Bug fix ][ Post List (Pro) ] Since WordPress 6.7.1 started outputting contain-intrinsic-size: 3000px 1500px, add a reset for this in .card.
[ Other ][ Slider ] Refactoring editor script.

= 1.91.2 =
[ Bug fix ] Fix checkbox misalignment in the admin panel.

= 1.91.1 =
[ Bug fix ] Avoiding translation errors in WordPress 6.7

= 1.91.0 =
[ Specification change ] Adjusted translation file loading to comply with changes in WordPress 6.7.
[ Bug fix ][ Table of Contents (Pro) ] Fixed "CLOSE" label not appearing after clicking the "OPEN" button when the initial state is set to "CLOSE".

= 1.90.1 =
[ Bug fix ][ Cover ] Fixed inline styles not being retained when adding a link.

= 1.90.0 =
[ Add function ][ Outer (Pro) ] Add book and pyramid in divider style.
[ Bug fix ] The split loading option is now supported for core/heading, core/image, and core/table styles for block editor.
[ Bug fix ][ Cover ] Fixed an issue where, after setting a link in the Cover block and adding two unstyled headings inside it, the content positioning would not apply upon returning to the editing screen (editing screen only).
[ Other ][ Slider ] Add alert message.

= 1.89.0 =
[ Add function ][ Outer (Pro) ] Added support for core text color settings.
[ Specification Change ][ Accordion (Pro) ] Added the ability to toggle the Accordion block open and close in the editing screen.
[ Bug fix ][ Grid Column Card (Pro) ] Fixed slider overflow in Grid Column Card Item Body.
[ Bug fix ][ core/group ] Fixed an issue where unwanted classes were assigned when links were present in the group block.

= 1.88.0 =
[ Specification change ][ Grid Column Card (Pro) ] Changed the default settings of headerDisplay and footerDisplay from "Delete" to "Display".
[ Specification change ] Add filter vk_post_taxonomies_html ( Update VK Components 1.6.1 )
[ Bug fix ][ Link Component ] Fix adding "http://" with "tel:" and "mailto:" link.
[ Bug fix ][ Slider ] Fix an issue where full width alignment does not work in tt4 and tt5.

= 1.87.0 =
[ Add function ][ Link Toolbar ] Added to skip retrieving metadata (title and favicon) for external links in link toolbar to prevent CORS errors.
[ Add function ][ icon ] Add toolbar link for components.
[ Bug fix ][ Slider ] Adjusting the timing of loading swiper to prevent the slider from collapsing.
[ Bug fix ][ Grid Column Card (Pro) ] Add translation.
[ Bug fix ][ Category Badge (Pro) ] Added Pro label to the inserter.

= 1.86.1 =
[ Bug fix ] Roll back 1.85.1

= 1.86.0 =
[ Add function ][ Fixed Display (Pro) ] Added an option for "Fixed display from the bottom."
[ Add function ] Added to apply the vk-scroll-hint class to all elements with the vk_hidden and vk_hidden-XXX classes.
[ Bug fix ][ Core/table ] Fixed the class was not removed when table scrolling was disabled.
[ Bug fix ] Fix load styles when separate load is enable.
[ Bug fix ][ Tab (Pro) ] Added a process to dynamically calculate and set the iframe height when the tab becomes active.
[ Bug fix ] Fixed an issue where disabling separated loading caused all block CSS to load.

= 1.85.1 =
[ Bug fix ] Due to an issue where the hidden setting does not function properly when TreeShaking is enabled and CSS splitting is disabled, TreeShaking has been temporarily disabled as a workaround.

= 1.85.0 =
[ Add function ][ Core Table ] Add scroll hint for horizontal scrolling.
[ Specification change ][ Post List (Pro) ] Disable link on edit screen.
[ Specification change ][ Alert ] Fixed the alert variation icon.
[ Bug fix ][ Breadcrumb (Pro) ] Fixed an issue where the separator setting in the Breadcrumb block was not being loaded correctly (now loaded from view.js).
[ Bug fix ] Fixed an issue where abnormal load occurred on a server that cannot use auto-update.

= 1.84.2 =
[ Bug fix ][ Custom CSS (Pro) ] Replaced an improved sanitation function in `vk_blocks_output custom css` to handle various CSS patterns.

= 1.84.1 =
[ Bug fix ][ Custom CSS (Pro) ] Replaced wp_kses with a sanitization function in vk_blocks_output_custom_css.

= 1.84.0 =
[ Add function ][ Column ] Add toolbar link for components.
[ Specification change ][ Classic FAQ / New FAQ ] Support structured data.
[ Bug fix ][ Link toolbar ] Fix to prevent adding http:// or https:// when a relative path is entered.
[ Bug fix ][ Post list (Pro) ] Fixed pagination customization in post list.
[ Other ] Add a mode to extract class name for Font awesome icon.
[ Other ] Supports custom CSS including pseudo-elements even in block-by-block lazy loading mode.

= 1.83.0 =
[ Add function ][ Alert ] Add icon setting and inner block.
[ Bug fix ][ Grid Column Card (Pro) ] Fixed an issue where using a synced pattern would cause the destination edit page to crash.
[ Bug fix ][ Core List ] Add support for handling list color in old settings.
[ Bug fix ][ Tab Item ] When duplicating an active tab-item block, no more than one tab-item block becomes active.

= 1.82.0 =
[ Add function ][ Core Cover ] Add toolbar link for components.
[ Specification change ] Change the location of the margin-related CSS code.
[ Bug fix ] Added dynamic color settings for `vk-has-*` and `has-vk-*` classes.
[ Bug fix ][ Post list (Pro) / Selected post list (Pro) / Child page list (Pro) ] Fix error when 'new_date' attribute is empty.
[ Bug fix ][ Post list (Pro) / Selected post list (Pro) / Child page list (Pro) ] Fixed an issue where the button would remain aligned to the left even when it was set to align to the right.

= 1.81.0 =
[ Add function ][ Table ] Add horizontal scrolling Setting.
[ Specification change ][ Image ] Add a circle style to the image block.
[ Specification change ][ Slider ] Delete width setting from sidebar.
[ Specification change ] Update VK Component.
[ Bug fix ] Added dynamic color settings for common css.
[ Bug fix ][ Slider Item ] Fixed to hide content that exceeds the height when setting the slider height.
[ Bug fix ][ Category Badge (Pro) ] Fixed an issue where the spinner continued to display on the edit screen in WordPress 6.6
[ Bug fix ][ Slider Item ] Fix infinite loop in slider item block when used in reusable blocks.
[ Bug fix ][ Accordion(Pro) ] Remove resize event causing accordion closure on scroll.
[ Bug Fix ][ Blog Card ] Make it editable in the edit screen in WordPress 6.6
[ Bug Fix ][ Grid Column Card (Pro) ] Setting a grid column block inside a reusable block no longer causes an error.
[ Bug fix ][ Post list (Pro) ] Fixed post query to prevent memory overflow by paginating results.
[ Bug fix ][ Post List (Pro) ] Fixed pagination handling.
[ Bug fix ] The split loading option is now supported for core/heading, core/image, and core/table styles.

= 1.80.1 =
[ Bug Fix ] Fixed in WordPress 6.4.x / 6.5.x so that blocks can be used.
[ Design Bug Fix ][ Fixed Display (Pro) ] Add max-width for position top and bottom.

= 1.80.0 =
[ Add function ][ Outer (Pro) ] Add serrated and large triangle in divider style.
[ Bug Fix ][ Outer (Pro) ] Add vertical padding variables.
[ Bug Fix ][ Grid Colum (Pro) ] Fix error

= 1.79.1 =
[ Bug Fix (Free) ] Fixed an issue where blocks couldn't be used in WordPress 6.6.

= 1.79.0 =
[ Add function ][ Tab (Pro) ] Add settings for when there are many labels or the screen width is narrow.
[ Add function ][ Core Group ] Add toolbar link for components.
[ Add function ][ Grid Column (Pro) ] Add toolbar link for components by item.
[ Add function ][ Slider ] Add 'Wide' to width.
[ Specification change ] Hide license key when license key is valid.
[ Specification change ][ Category Badge (Pro) ] Enabled taxonomy specification regardless of post type.
[ Specification change ][ Slider ] Change padding style to core system from original.
[ Bug fix ] Fixed an error in the component link toolbar in WordPress version 6.6.
[ Bug fix ][ Tab (Pro) ] Fixed extra space being added to tabs depending on theme.
[ Bug fix ][ Fixed Display (Pro) ] Fixed an issue with redundant JavaScript loading in the WordPress 6.5 environment.
[ Other ][ Table of Contents (Pro) ] Improved pseudo elements for frontend page accessibility.
[ Other ][ Outer ] Refactored CSS of width treatment to prevent layout corruption on the edit screen.

= 1.78.0 =
[ Other ] Attend to WordPress 6.6

= 1.77.0 =
[ Add function ][ Outer (Pro) ] Add toolbar link for components.
[ Add function ][ Accordion(Pro) ] Added initial display state setting.
[ Specification change ][ Tab (Pro) ] Accessibility support
[ Bug fix ][ Table of Contents (Pro) ] Fix "OBJ" characters appeared in the Table of Contents on Windows.
[ Bug fix ] Delete unnecessary development files included in 1.76.2.
[ Fix ][ Classic FAQ / New FAQ ] Add aria-label for accessibility.

= 1.76.2 =
[ Specification change ] Change Font Awesome Version to 6.5.2
[ Bug fix ] Fix load font awesome files.

= 1.76.1 =
[ Bug fix ][ Fixed Display (Pro) ] Add css for position and alignment.
[ Bug fix ][ Post List (Pro) ] Fixed sorting by display order other than publication date to work for multiple post types.
[ Other ] Add Transform vk-blocks/heading ( Not Recommended ) to core/heading.
[ Other ] Delete old deprecated code.

= 1.76.0 =
[ Add function ] Add Setting for the position of VK Blocks on all block inserter.
[ Add function ][ Border Box ] Add title tag setting.
[ Add function ][ Slider ] Add url interface to block toolbar for slider item.
[ Specification change ][ Post List (Pro) ] Displayed taxonomies now only show for selected post types.
[ Specification change ][ Responsive Spacer ] Added CSS to remove margins from the default theme.
[ Bug fix ][ Tab ] Fix background color of tab disappearing when clicking active tab.
[ Add function ][ Slider Item ] Add toolbar link for components.
[ Other ] Add toolbar link to components.

= 1.75.1 =
[ Specification change ][ Fixed Display (Pro) ] Do not fix the edit screen as it makes editing difficult.
[ Bug fix ] Fixed an issue where the Pro version could not be activated in an environment where the Free version is active.

= 1.75.0 =
[ Add Block ][ Fixed Display block (Pro) ] Add Fixed display block.
[ Bug fix ][ Tab (Pro) ] Fix the default line style to show the top of the border.
[ Specification change ] Delete unnecessary css value ( We abolished the --vk-size-text and changed it to 1rem. ).

= 1.74.0 =
[ Specification change ] Fix block categories order
[ Specification change ][ VK Button ] Add unit "percent" for border radius.
[ Bug fix ][ VK Button ] Fixed button URL being output in editor.
[ Bug fix ][ Tab (Pro) ] Fix inactive tab color setting.

= 1.73.0 =
[ Add Block ] Add Tab Block
[ Add function ][ Columns ] Add direction reverse option.
[ Add function ][ VK Button ] Add border radius setting.
[ Add function ][ Spacer / Common margin ] Add size option XXL/XXS.
[ Add function ][ Grid Column Card (Pro) ] Add border width setting to grid column card block.
[ Add function ][ Outer (Pro) ] Added option to min height setting.
[ Specification change ][ Custom CSS (Pro) ] Changed the custom CSS text area to be wider
[ Specification change ][ Outer ] Remove the negative margin for .vk_outer-width-full.
[ Bug fix ] Fixed an issue with redundant JavaScript loading in the WordPress 6.5 environment.
[ Bug fix ] Remove min-height from "Custom CSS" on edit screen.
[ Other ] Fixed useSetting deprecated

= 1.72.1 =
[ Specification change ][ Pro ] Attend to I18N Improvements in 6.5.
[ Other ] Fixed useSetting deprecated
[ Bug fix ] Fixed an issue with redundant JavaScript loading in the WordPress 6.5 environment.

= 1.72.0 =
[ Specification change ][ Child Page List ] Hide "Term's name on Image" and "Taxonomies (all)" display options.
[ Design Bug Fix ][ Balloon ] Harmonized icon image display in balloon blocks with frontend editing.
[ Design Bug fix ][ Grid Column ] Fixed vk_gridColumn_item to be aligned to the beginning and to be the basic width size of the parent element.
[ Design Bug fix ][ group ] Fixed an issue where internal links were not working in group block style stitches.
[ Design Bug fix ][ Balloon ] Harmonized icon image display in balloon blocks with frontend editing.
[ Editor Design Bug fix ][ Slider ] Fixed an issue where slider item height disappears when editorMode is slide and alignfull.

= 1.71.0 =
[ Add function ][ Accordion(Pro) ] Add plain style to accordion block.
[ Add function ][ icon ] Add Font color option in solid type icon.
[ Bug fix ][ Button ] Fixed buttonColorCustom in the editor to display the correct color.
[ Bug fix ][ Table of Contents (Pro) ] Fixed visibility issue in 6.5.
[ Bug fix ][ Core Group ] Fix stitching styles for theme.json in Group blocks.
[ Bug fix ][ Flow ] Fixed margin-block-start being attached to flow block
[ Bug fix ][ Grid Column (Pro) ] Fixed right margin when using grid columns outside of Lightning.
[ Bug fix ][ Grid Column (Pro) ] Content does not span full width when using grid columns outside of Lightning in col-12.
[ Bug fix ][ New FAQ ] Fix a typo in the font-family specification.

= 1.70.0 =
[ Specification change ] core/social-link, core/site-logo, core/site-title and core/site-tagline correspond to margin-extension
[ Add function ][ Breadcrumb ] Add the ability to input breadcrumb separators.

= 1.69.1 =
[ Bug fix ][ Inline Font Size ] Applies only Font Size has a numeric value.
[ Bug fix ][ Grid Column Card (Pro) ] Fixed a bug where vertical alignment settings for card blocks did not apply in the editing screen.
[ Bug fix ][ Slider ] Corresponding Slider Mode to Site Editor / Widget Editor.
[ Bug fix ][ Slider ] Fixed a bug where placing heading blocks and similar elements directly in slides made them uneditable.

= 1.69.0 =
[ Add function ][ Slider ] Add Slider Mode on Editor.
[ Add function ][ Post List (Pro) ] Add paged lock setting.
[ Specification change ] Change the required PHP version to 7.4 or higher.
[ Bug fix ][ Image ]Fixed a bug in the photo frame with activated zoom in the image block styled 'photoFrame-tilt'
[ Bug fix ] Update CSS Optimizer 0.2.2

= 1.68.0 =
[ Add function ][ Dynamic Text (Pro) ] Add feature to display logged-in username.
[ Specification change ][ Hidden extension ] Add VK Icon
[ Specification change ][ Outer (Pro) ] Allowed decimal points in 'Container Inner Side Space Setting'.
[ Specification change ][ Heading ] Marked as Not Recommended

= 1.67.0 =
[ Add Block ][ Category Badge (Pro) ] Creates badges displaying linked categories or terms for posts, with flexible design customization.

= 1.66.2 =
[ Bug fix ][ Slider ] Fixed a bug in version 1.66.1 where the slider width did not match the content width in certain themes.

= 1.66.1 =
[ Bug fix ][ Slider ] Fixed a bug in version 1.66.0 where the slider width did not match the content width in certain themes.

= 1.66.0 =
[ Specification change ] Change VK Component Posts Horizontal col class ( Update VK Component 1.6.0 / Discontinued the use of Bootstrap's Grid system )
[ Bug fix ] Fix 1.65.0 translate

= 1.65.0 =
[ Add function ][ Grid Column Card (Pro) ] Enabled gradient specification for background colors.
[ Bug fix ][ Grid Column Card (Pro) ] Fixed that Grid Column Card Item Body block vertical align not work

= 1.64.2 =
[ Bug fix ][ Custom Format (Pro) ] Fixed Toolbar Icon Size Issue in WordPress 6.4

= 1.64.1 =
[ Specification change ] Update Swiper 11.0.1
[ Bug fix ][ Toolbar ] Fixed Toolbar Icon Size Issue in WordPress 6.4
[ Bug fix ][ Grid Column (Pro) ] Fixed inner list block style.
[ Bug fix ][ Outer (Pro) ] Fix padding in divider style
[ Bug fix ][ Grid Column Card ] Alignment issue of images placed within the body.

= 1.64.0 =
[ Add function ][ Dynamic Text Block (Pro) ] Added option to display parent page title.
[ Bug fix ][ Page list from ancestor ] Fixed XSS issue.

= 1.63.0 =
[ Add function ][ Blog Card (Pro) ] Add block variation function.
[ Add filter ][ Post List (Pro) ] Add vk_blocks_post_list_query_args filter hook.
[ Bug fix ][ Balloon ] Fixed word break position.
[ Bug fix ][ Table of Contents ] Fixed the issue with inner blocks not working correctly.
[ Bug fix ][ Outer (Pro) ] Fixed a bug in the initial setting of divider for each device.

= 1.62.0 =
[ Add Block ][ Blog Card (Pro) ] Add blog card block.
[ Add Block ][ New Badge Block (Pro) ] Add a block to display recent posts.
[ Add function ][ Outer (Pro) ] Add options to Divider for each PC, Tablet, and Mobile
[ Specification change ] Updated Font Awesome to 6.4.2.
[ Bug fix ][ Admin screen ] Fixed a bug where settings saved in an array were not imported.
[ Bug fix ][ Button ] Fix console warning

= 1.61.2 =
[ Bug fix ][ Outer (Pro) ] Fixed opacity with previous Outer version.

= 1.61.1 =
[ Bug fix ] Fix error WordPress 6.3 live previewing block themes.
[ Bug fix ] VK Term color update 0.6.6

= 1.61.0 =
[ Specification change ] Update the required WordPress version.
[ Specification change ] Changed to display Font Awesome version change button only when select is changed.
[ Specification change ][ Outer (Pro) ] Change opacity setting can be set in increments of 0.01.
[ Specification change ][ Animation(Pro) ] Fix WordPress 6.3 transforms settings.
[ Bug fix ][ Taxonomy (Pro) ] Fixed error when selected taxonomy dose not exists.
[ Bug fix ][ Taxonomy (Pro) ] Fixed individual CSS was loaded on all pages with classic theme.

= 1.60.0 =
[ Add function ] Add Font Awesome icon custom list function.
[ Add function ][ Dynamic Text Block (Pro) ] URL support for custom fields.
[ Add function ][ New FAQ ] Add an accordion default option on a per-block.
[ Specification change ] Disable HTML editing for blocks with inner blocks, as the blocks are broken.
[ Bug fix ][ Dynamic Text (Pro) ] Fix php warning that brought by can't get post type name on search result page.
[ Bug fix ][ Dynamic Text (Pro) ] Fix php warning that brought by can't get ancestor page on search result page.
[ Bug fix ][ Icon Block ] Fix enable hidden extension.

= 1.59.0 =
[ Add Filter Hook (Pro) ] Add filter fook of display license key form or not
[ Specification change ] Change option value update via Redux Store.
[ Bug fix ][ Page Content ] Fix duplicate Additional CSS classes.
[ Bug fix ] Fix swiper file path ( // -> / )

= 1.58.1 =
[ Bug fix ][ Admin screen ] Fix block style manager changes other option values.
[ Bug fix ][ Admin screen ] Fix block manager changes other option values.
[ Other ] Delete inc/vk-helpers
[ Other ][ Dynamic Text (Pro) ] Translation update

= 1.58.0 =
[ Add function ][ Dynamic Text Block (Pro) ] Add custom fields to displayElement.
[ Add function ][ Admin screen ] Added import export tool.
[ Other ][ Heading style ] Cope with dark background color
[ Other ] Update VK Admin 0.4.0 ( Cope with English information )
[ Other ] Update VK Component 1.5.0 ( Remove dependency on VK_Helpers )
[ Bug fix ][ Spacer ] Allow 0 height.
[ Bug fix ] Update option value Modify API authority
[ Bug fix ][ Dynamic Text (Pro) ] show/hide option when displaying ancestor page is applied to post types other than "page".

= 1.57.1 =
[ Bug fix ] Update option value Modify API authority
[ Bug fix ][ Grid Column Card ] Delete non intentional link underline

= 1.57.0.5 =
[ Bug fix ][ Free Version ] Fix display non intentional Licencekey alert message and update become don't work. ( Please delete once VK Blocks 1.57.0.1 and reinstall 1.57.0.4 or higher )

= 1.57.0 =
[ Add function ] Allow alpha value in some blocks.
[ Add function ][ Dynamic Text (Pro) ] Added an option to show/hide the title when an ancestor page is selected and the ancestor page is display.
[ Add function ][ Animation (Pro) ] Added transforms settings to wrap and unwrap.
[ Bug fix ] Fix Textdomain( Translate ) for VK Blocks Pro
[ Bug fix ][ Child page list (Pro) ] "There are no page." is not displayed on the front page.
[ Bug fix ][ Breadcrumb (Pro) ] Delete non intentional margin on editor screen
[ Bug fix ][ Taxonomy (Pro) ] Fix the "Show hierarchy" option bug.
[ Bug fix ][ Slider ] Fixed file reference bug of slider under specific environment such as Windows ( Update Swiper 9.3.2 )
[ Bug fix ][ Dynamic Text Block (Pro) ] Fixed a bug that caused an error when setting the style.
[ Other ][ Slider ] Add loop alert

= 1.56.0 =
[ Add Block ][ Dynamic Text (Pro) ] Add Dynamic text block.
[ Add function ][ Admin screen ] Added block style manager function.
[ Specification change ][ Slider ] Update Swiper to 9.2.3.
[ Bug fix ][ Taxonomy block (Pro) ] Fix Dropdown Script.
[ Bug fix ][ Custom Format Setting (Pro) ] Custom Format Setting WordPress 6.2 UI adjustment.
[ Bug fix ][ Step (Pro) / Time Line(Pro) ] Fix item content overflow

= 1.55.0 =
[ Add Block ][ Archive list block (Pro) ] Displays a list of archives
[ Add Block ][ Taxonomy block (Pro) ] Displays a list of taxonomy terms
[ Specification change ][ List ] cope with custom color palette (WordPress 6.2 or higher)
[ Bug fix ][ Spacer ] Fix custom css variable

= 1.54.0 =
[ Add Setting ][ margin / spacer ] Add custom value to margin setting
[ Other ] Update the required WordPress version
[ Bug fix ][ Outer (Pro) ] Fix Outer Container CSS.
[ Bug fix ] Cope with XSS

= 1.53.0 =
[ Add setting ][ Slider ] Allow decimalon slider per view Setting & Add Setting of Centering Active Slide
[ Specification change ][ Custom Block Style Setting (Pro) ] Don't limit target blocks to VK Blocks.
[ Bug fix ] Fix inline css when css sepalate load mode.

= 1.52.0 =
[ Specification change ] Add multiple length units.
[ Specification change ][ Custom CSS (Pro) ] Changed specification to output CSS wrapped by .editor-styles-wrapper in block editor.
[ Specification change ][ Custom Block Style Setting (Pro) ] Changed the specification to save the CSS wrapped in .editor-styles-wrapper and output that CSS in the block editor.
[ Specification change ][ Outer (Pro) ] When the block is made full width, the class name of "alignfull" will be given.
[ Bug fix ][ Custom CSS (Pro) ] Fix PHP 8.1 warning
[ Bug fix ][ Slider ] Allow 0 to be entered in numeric form.
[ Bug fix ][ Slider ] Fix content width under case of use .is-layout-constrained and editor screen
[ other ] Removed unused display_vk_block_template option value.

= 1.51.0 =
[ Improvement ][ Balloon ] Improvement to allow any number of registrations in admin.
[ Bug fix ][ Step/timeline ] Fix title align center

= 1.50.１ =
[ Other ] CSS Optimize ( Tree Shaking ) Library update

= 1.50.0 =
[ Add function ][ Slider ] Add Slider per view Setting for Mobile, Tablet, PC.

= 1.49.0 =
[ Add function ][ Custom Block Style Setting (Pro) ] Add Custom Block Style Setting extension in admin.
[ Specification change ][ Post List (Pro) ] Lightweight data acquisition process
[ Bug fix ][ Step(Pro) / Time Line(Pro) ] Fix item content overflow hidden

= 1.48.1 =
[ Bug fix ][ Slider ] Set default value for unset time and speed.
[ Bug fix ][ table style ] Fix bug of under the active theme.json environment, If you use the table styles that, table border property become not reflection.
[ Other ] Update Plugin Update Checker to 5.0
[ Other ] Update VK Breadcrumb lib 0.2.5

= 1.48.0 =
[ Add function ][ Admin screen ] Added block manager function.
[ Add function ][ Custom Format Setting (Pro) ] Add Custom Format Setting extension in admin.
[ Bug fix ][ Custom CSS (Pro) ] Fixed bug in not replacing all selector strings.
[ Specification change ][ Animation(Pro) ] add setting option Animation only the first view.

= 1.47.1 =
[ Bug fix ][ Slider ] Stick out background image on setting panel of site editor

= 1.47.0 =
[ Other ][ Slider (Pro) ] Change Pro to Free.
[ Bug fix ][ Button ] Fixed a bug where the default color was not hit in all themes except Lightning.

= 1.46.0 =
[ Add function ][ Custom CSS (Pro) ] Add custom css extension in inspector controls.
[ Specification change ][ Grid Column (Pro) ] Changed margin setting from 1 to 0.1 separator.
[ Specification change ] Update the required WordPress version.
[ Bug fix ][ Highlighter ] Fixed a bug that custom colors cannot be used.
[ Bug fix ][ Button ] Fixed a bug that the color palette does not change on the edit screen when there is no background.
[ Bug fix ][ List ] Fixed a bug that the color of the list icon was not reflected on the edit screen
[ Bug fix ][ Step (Pro) / Time line (Pro) ] Fix margin of theme.json
[ Bug fix ][ Grid Column (Pro) ] Fix bg color overflow bug on edit screen.

= 1.45.0 =
[ Other ] Cope with WordPress 6.1
[ Specification change ] Color palette manager use wp_theme_json_data_default filter.
[ Bug fix ][ Button ] Delete non intentional underline.
[ Other ] color palette manager added warning that --vk-color-custom-${number} is deprecated and replaced with --wp--preset--color--vk-color-custom-${number}.

= 1.44.0 =
[ Add function ][ Post List (Pro) ] Add post filter taxonomy relation.
[ Add function ][ Button ] Add Button Effect option in Solid color button.
[ Specification change ][ Post List (Pro) ] Change to don't display unpublic posttype and terms list.
[ Bug fix ][ Grid Column Card(Pro) ] Fixed a bug that css for editor was displayed in front.
[ Bug fix ][ Select Post List Item (Pro) ] Fixed a bug where additional CSS classes were not attached.

= 1.43.0 =
[ Add function ][ Spacer ][ Common mergin ] Add size option xl/xs.
[ Improvement ][ Admin screen ] Display the SaveButton sticky.
[ Bug fix ] Fix PHP 8.X Error

= 1.42.1 =
[ Other ] Update VK Component Posts 1.3.1

= 1.42.0 =
[ Specification change ] Update VK Component Posts ( Can use input tag on filter )
[ Improvement ] Delete vk_blocks_get_options() function and change to VK_Blocks_Options::get_options().

= 1.41.2 =
[ Improvement ][ Page list from ancestor ] Fix behavior site editor.
[ Other ] Add PHPUnit test on Several PHP and WordPress versions
[ Bug fix ] Fatal error in WordPress 5.8
[ Bug fix ][ highlighter ] cope with color palette with alpha.
[ Bug fix ][ button ] buttonColorCustom clear convert to primary.

= 1.41.1 =
[ Bug fix ] Fix don't display Admin screen in case of spacific option value

= 1.41.0 =
[ Add function ][ Balloon ] Add width option.
[ Bug fix ][ Outer (Pro) ] Fixed the border color of the Outer block within the Outer block was not attached.
[ Specification change ][ Breadcrumb(Pro) ] Hidden front page breadcrumb.

= 1.40.1 =
[ Specification change ][Step(Pro)/Timeline(Pro)] Change lineStyle ui.
[ Bug fix ][ Hidden extension ] fix hidden extension class when Screen size xl.
[ Bug fix ][ FAQ ] fix faq block js error when load separation mode.

= 1.40.0 =
[ Improvement ][ Hidden extension ] Changed to add common hidden class names to additional CSS classes.
[ Add Block ][ Page list from ancestors ]
[ Add function ][ Button ] Enable inline font size and add icon size option.
[ Specification change ] Change the style of the options page to Gutenberg components.
[ Bug fix ] fix editor style in Inline font size and Highlighter.
[ Bug fix ] Fixed a bug that CSS for edit screen is not loaded in iframe.
[ Bug fix ][ Breadcrumb ] Fix duplicate Additional CSS classes.

= 1.39.2 =
[ Bug fix ][ Breadcrumb ] Fix in case of filter search result category & keyword
[ Bug fix ][ Table style ] Delete border left and right specified vk-table-border-top-bottom
[ Specification change ][ icon ] enable float value at icon size and margin

= 1.39.1 =
[ Bug fix ][ Grid Column Card ] fix bug when aspect retio is empty.
[ Other ] Change the script loaded on the options page to a script file.

= 1.39.0 =
[ Improvement ] License key remove space.
[ Bug fix ][ Common margin ] cope with table margin bottom 0,margin top 0
[ Bug fix ][ Grid Column Card (Pro) ] cope with custom color palette
[ Other ] VK Compo ( mini-content ) Update ( Fix slider align )

= 1.38.0 =
[ Bug fix ][ Post List (Pro) ] cope with pagenation hook

= 1.37.0 =
[ Specification change ][ Step Item(Pro) / Timeline Item()Pro ] Change padding-bottom to inner-item last-child margin-bottom
[ Specification change ][ Timeline ] If no label, the outer html will not be displayed.
[ Specification change ][ Outer ] Move width control to block toolbar.
[ Specification change ] Change the style loaded on the options page to a css file.
[ Bug fix ][ Tree Shaking ] cope with not(***,***)
[ Bug fix ][ Heading design ] Fix text-align

= 1.36.2 =
[ Specification change ] allow iframe on post list filter
[ Bug fix ][ Slider (Pro) ] Add compatibility process.
[ Bug fix ][ Heading design ] Fix plain design text-align

= 1.36.1 =
[ Bug fix ] Fix active pro version

= 1.36.0 =
[ Add function ][ Button outer(Pro) ] Add gap option.
[ Bug fix ][ Accordion(Pro) ] Fix margin bottom on Accordion close.

= 1.35.0 =
[ Specification change ][ Animation(Pro) ] Corresponds reuse block & duplicate
[ Specification change ][ Grid Column Card(Pro) ] Corresponds reuse block & duplicate automatically
[ Specification change ][ Card(Pro) ] Corresponds reuse block & duplicate automatically
[ Specification change ][ Slider(Pro) ] Corresponds reuse block & duplicate automatically
[ Specification change ][ Slider(Pro) ] Changed slider breakpoints to match specified breakpoints in VK Blocks
[ Specification change ][ Slider(Pro) ] Change width class name
[ Bug fix ][ List ] cope with custom color palette
[ Bug fix ][ Slider(Pro) ] Fixed not to output empty CSS tags
[ Bug fix ][ Outer (Pro) ] Fix WordPress 6.0 border color class.
[ Bug fix ][ Spacer ][ Animation (Pro) ][ Slider (Pro) ] height and border style on Full Site Editor

= 1.34.1 =
[ Bug fix ] Fix term color library ( since 1.34.0 )

= 1.34.0 =
[ Improvement ][ Button ] Support for transformation of paragraph block to VK button block.
[ Other ] add term color composer library
[ Bug fix ][ Grid Col Card(Pro) ] Fix bocome narrow width in case of innner block on edit screen

= 1.33.2 =
[ Bug fix ][ Button ] Fix horizontal padding for X-T9
[ Bug fix ][ Common mergin ] cope with table bottom
[ Bug fix ][ Breadcrumb(Pro) ] Fix front page breadcrumb
[ Bug fix ][ Grid Column(Pro) ] fix row block layout in Grid Column Block
[ Improvement ][ Button ] Delete block id UI.

= 1.33.1 =
[ Bug fix ] Fix PHP notice ( Pro version only )
[ Fix ] Translation ( Pro version only )

= 1.33.0 =
[ Specification change ] Required license key entry.
[ Bug fix ][ Slider(Pro) ] Cope with auto height.
[ Bug fix ][ Heading ] Heading-Adjusted padding when there is a background.

= 1.32.0 =
[ Add function ][ Grid Column Card (Pro) ] add column min-width setting for tablet and pc.
[ Add function ][ Button outer(Pro) ] Add button width option for mobile or tablet.
[ Improvement ][ margin extension ] Enable margin settings in Grid column(Pro).
[ Bug fix ] Fix FAQ option array error for free.
[ Bug fix ] Fix load Font Awesome Files on WordPress.com
[ Bug fix ][ Button ] Fix icon inner classes.

= 1.31.0 =
[ Add function ][ Grid Column Card(Pro) ] Add link URL in toolbar and sidebar.
[ Add function ][ Breadcrumb(Pro) ] Add support fontSize and spacing.
[ Improvement ][ margin extension ] Changed to add common margin class names to additional CSS classes.
[ Bug fix ][ Accordion(Pro) ] Fix do not intend margin bottom 0 added to p tag.

= 1.30.0 =
[ Add function ][ Border Box ] Add body align control in toolbar.
[ Add function ][ Button outer(Pro) ] Add button width option.
[ Update ][ Font Awesome ] Add version chenge setting on block-editor screen.
[ Specification change ][ Button ] Move url interface to block toolbar.
[ Specification change ] Change default --vk-margin-md size 2rem -> 2.4rem
[ Specification change ][ icon ] fix icon margin bottom

= 1.29.2 =
[ Bug fix ][ margin extension ] Exclude grid column blocks because they are covered by the setting values.
[ Bug fix ][ margin extension ] Optimize excludes core block list.
[ Bug fix ] fix editor style of button-outer & icon-outer in page-content

= 1.29.1 =
[ Bug fix ][ margin extension ] Optimize excludes block list.

= 1.29.0 =
[ Add function ] Add margin extension in toolbar.
[ Add Block ] Grid Column Card(Pro)
[ Fix ][ Breadcrumb(Pro) ] Add inserter Pro label.
[ Other ] Update Font Awesome 6 -> 6.1

= 1.28.0 =
[ Add Block ] Breadcrumb(Pro)
[ Specification change ][ Spacer ] Change break point
[ Bug fix ][ Outer (Pro) ] Border radius range above 0.
[ Bug fix ][ Outer (Pro) ] background position fix

= 1.27.9 =
[ Bug fix ][ Slider(Pro) ] Fixed a bug that the background was not transparent when the transparency setting was set to 0.

= 1.27.8 =
[ Bug fix ][ Outer (Pro) ] Fixed a bug where css was hitting inner content

= 1.27.7 =
[ Bug fix ][ Post list an so on (Pro) ] Fixed number of days to display the new post mark was not saved.
[ Bug fix ][ Post list an so on (Pro) ] Fixed no post message php notice.

= 1.27.6 =
[ Other ] Fix deploy flow

= 1.27.5 =
[ Bug fix ][ Outer(Pro) ] Compatibility support
[ Bug fix ][ Post List(Pro) ] Fix no post message

= 1.27.1 - 1.27.4 =
[ Other ] Deploy Free Version.

= 1.27.0 =
[ Add function ][ Font Awesome ] Cope with Font Awesome 6
[ Add function ][ Post List (Pro) ] Add no-post message filter hook
[ Bug fix ][ Outer(Pro) ] cope with custom color palette

= 1.26.2 =
[ Other ] Rerelease

[ Bug fix ][ Spacer ] fix spacer

= 1.26.0 =
[ fix ] fix plugin settings links.
[ Add function ][ Spacer ] Add margin-top mode

= 1.25.1 =
Change stable version

= 1.25.0 =
[ Improvement ] Change to always load bootstrap when the theme is not Lightning,Lightning Pro,Katawara.
[ fix ] Fix load FontAwesome when the theme is not Lightning,Lightning Pro,Katawara.
[ fix ] color palette manager support border color

= 1.24.5 =
[ fix ][ Button ] Fix button alignment
[ fix ][ Card(Pro) ] fix unify breakpoints.

= 1.24.4 =
[ Specification change ][ Heading Design ] Strengthen selector on editor screen

= 1.24.3 =
[ Bug fix ][ FAQ ] Fix list block last item can't edit.
[ Bug fix ][ Slider(Pro) ] Fix Slider in Image block caption can't edit.
[ Bug fix ] Bug fix Color palette not working in widget

= 1.24.2 =
[ Deploy ] Deploy Free Version 1.24.0

= 1.24.1 =
[ Specification change ][ Dev ] fix deploy free script

= 1.24.0 =
[ fix ][ Icon outer ] add orientation horizontal & fix appender
[ fix ][ Card(Pro) ] add orientation horizontal & fix appender
[ fix ][ selected post list(Pro) ] add orientation horizontal & fix appender
[ fix ][ Button outer(Pro) ] add orientation horizontal & fix appender
[ fix ][ Icon Card(Pro) ] add orientation horizontal & fix appender
[ fix ][ Grid column(Pro) ] add orientation horizontal & fix appender
[ Specification change ][ Dev ] change build script

= 1.23.0 =
[ Specification change ][ Flow ] Release Image float on xs screen
[ Specification change ] Change required WordPress version to 5.8

= 1.22.4 =
[ Other ] Update the required WordPress version
[ Bug fix ][ Heading ] Fix bug the color does not change when the style setting is no decoration.
[ Bug fix ][ Slider(Pro) ] cope with custom color palette
[ Bug fix ][ Icon Card(Pro) ] fix text align
[ Bug fix ][ TOC(Pro) ] fix css corruption of block load separation mode

= 1.22.3 =
[ Bug fix ][ core/heading ] fix width

= 1.22.2 =
[ Bug fix ][ selected post list(Pro) ] fix post id

= 1.22.1 =
Cope with WordPress 5.9
[ Add function ] load separate block setting from setting > vk blocks
[ fix ][ Heading ] Make text size not auto-include when changing heading level
[ fix ][ Grid Column ] fix translate
[ fix ][ Heading ] vertical heading levels for wp-5.9
[ fix ][ Border Box ] cope with custom color palette
[ Bug fix ] fix block style enqueue point
[ Bug fix ][ button ] fix block-block size

= 1.21.0 =
[ Add function ][ Post List (Pro) ] add date filtering
[ Bug fix ][ Balloon ] Balloon icon background color when custom color is selected
[ Bug fix ][ Group ] fix alert style custom color

= 1.20.7 =
[ Bug fix ][ Heading ] cope with custom color palette
[ Bug fix ][ Heading ] Fix recovery

= 1.20.6 =
[ Bug fix ][ Heading ] Fix recovery
[ Bug fix ][ Heading ] cope with custom color palette
[ Bug fix ][ Button ] change option order in panel
[ Bug fix ][ Grid Column(Pro) ] cope with custom color palette
[ Bug fix ][ Balloon ] cope with custom color palette

= 1.20.5 =
[ Bug fix ] cope with Old PHP.
[ Bug fix ][ Timeline(Pro) ] cope with custom color palette

= 1.20.4 =
[ Other ] Only change stable version ( Deploy free version )

= 1.20.3 =
[ Bug fix ][ Post Media Layout ] Cope with not vektor theme
[ Bug fix ][ PR Blocks ] cope with custom color palette
[ Bug fix ][ Step(Pro) ] cope with custom color palette
[ Bug fix ][ Icon Card(Pro) ] cope with custom color palette
[ Other ] Change TGM & Plugin Update Checker load from composer

= 1.20.2 =
[ Bug fix ] Color palette manager in case of other theme

= 1.20.1 =
[ Bug fix ] Fix -wp5.7 error

= 1.20.0 =
[ Other ][ All ] Refactoring all blocks.
[ Bug fix ] Color palette manager in case of other theme

= 1.19.1 =
[ Bug fix ][ Button ] Fix bootstrap color crash

= 1.19.0 =
[ Add function ][ Button ] Set the text color with palette
[ Bug fix ][ Button ] Add primary color css variable in case of other theme

= 1.18.6 =
[ Bug fix ][ Button ] Custom color value don't refrect reopen

= 1.18.5 =
[ Other ] Change stable version only.

= 1.18.4 =
[ Bug fix ] 1.18.0 update ( Add color palette manager in plugin )
[ Bug fix ][ Button ] Fix bug in case of used button block for reusable block.

= 1.18.3 =
[ Bug fix ] Revert 1.17.0

= 1.18.2 =
[ Bug fix ] Revert 1.17.0

= 1.18.1 =
[ Bug fix ] Revert 1.17.0

= 1.18.0 =
[ Add function ] Add color palette manager in plugin

= 1.17.0 =
[ Add Block ][ Button Outer(Pro) ]

= 1.16.11 =
[ Bug fix ][ Button ] Fix color on katawara

= 1.16.10 =
[ Bug fix ][ border box ] Fix border box title conflict by title design function

= 1.16.9 =
[ Bug fix ][ icon ] Fix css bug from 1.16.0

= 1.16.8 =
[ Bug fix ][ Button ] cope with custom color palette

= 1.16.7 =
[ Bug fix ][ Button ] Fix primary bg color first aid

= 1.16.6 =
[ Bug fix ][ Button ] Fix primary bg color first aid

= 1.16.5 =
[ Bug fix ][ Button ] Fix wide size

= 1.16.4 =
[ Bug fix ][ Button ] Fix outline color

= 1.16.3 =
[ Bug fix ] Cope with excerpt br

= 1.16.2 =
[ Other ] Cope with old ssl error

= 1.16.1 =
[ Bug fix ][ Card ] Fix broke layout on edit screen

= 1.16.0 =
[ Add New Block ] Icon Outer
[ Bug fix ][ icon ] Fixed a bug the style was reset when the icon color clear
[ Bug fix ][ Ordered List ] Fixed a bug in standard style

= 1.15.1 =
[ Bug fix ][ Spacer ] Fix common margin setting in case of unspecified

= 1.15.0 =
[ Add function ][ Grid Column Item ] Add color and padding setting

= 1.14.1 =
[ Bug fix ][ Spacer ] Fix common setting input

= 1.14.0 =
[ Add function ][ Spacer ] Add common spacer type and common spacer responsive size
[ Bug fix ] Fix Lightning G3 Pro text size not work

= 1.13.2 =
[ Bug fix ][ Slider ]Fixed bug would break put a class in additional CSS class

= 1.13.1 =
[ Other ] version only

= 1.13.0 =
[ Specification change ][ Post list (Pro) ] Cope with ruby tag

= 1.12.0 =
[ Add New Block ] Icon Block
[ Improvement ][ Slider(Pro) ] add navigation position
[ Specification change ] VK Components Update ( can be customize title by hook )
[ Specification change ][ Slider(Pro) ] If set slide type fade that disable slide step number
[ Bug fix ][ Slider(Pro) ] Fix bug that to be same id under case of copy slide item
[ Bug fix ][ Social icon ] Fix css in grid block

= 1.11.4 =
[ Bug fix ][ Step(Pro) ] Fixed icon position

= 1.11.3 =
[ Bug fix ][ Step(Pro) ] Fixed icon position at G3

= 1.11.2 =
[ Improvement ] add block description
[ Bug fix ] Fixed widget screen warning
[ Bug fix ][ Slider(Pro) ] Change id when copy slider & slider-item.

= 1.11.1 =
[ Bug fix ][ Table style ] add botder top and bottom style

= 1.11.0 =
[ Specification change ][ Button ] Change margin getready to core button block.

= 1.10.0 =
[ Specification change ][ Slider(Pro) ] Add no height setting.
[ Bug fix ][ Slider(Pro) ]Fix bug where pagination design would change when tree shake was enabled.

= 1.9.2 =
[ Bug fix ] Fix for 5.8
[ Specification change ][ Slider(Pro) ] add Pagination Setting
[ Specification change ][ Heading ] Font size of title & sub text can set null.

= 1.9.1 =
[ Bug fix ] Fix AllowedBlocks of InnerBlocks.

= 1.9.0 =
[ Improvement ][ Inline Font Size ] setting font size in toolbar
[ Bug fix ][ Icon card ] Fix css bug

= 1.8.2 =
[ Bug fix ][ FAQ ] Fixed CSS of answer part when closing in the initial state
[ Bug fix ][ Responsive BR ] Fixed Console Warning

= 1.8.1 =
[ Bug fix ][ Flow ] Fixed to reflect alt of image

= 1.8.0 =
[ Improvement ][ Spacer ] Add common space size setting
[ Bug fix ] PR Content button after icon position

= 1.7.1 =
[ Bug fix ][ Heading ] Fix Color Palette default setting.

= 1.7.0 =
[ Improvement ][ Spacer ] Add common space size style

= 1.6.0 =
[ Improvement ][ Grid Column(Pro) ] enable setting margin bottom
[ Bug fix ][ Heading ] Fix Heading design when using Lightning Heading Setting

= 1.5.0 =
[ Improvement ][ highlighter ] setting color in toolbar
[ Specification change ][ Table of Contents ] Add notice about copying heading.

= 1.4.6 =
[ Specification change ] Update VK Admin Library.

= 1.4.5 =
[ Specification change ] Update VK Admin Library.

= 1.4.4 =
[ Specification change ] Change Version Only.

= 1.4.3 =
[ Specification change ] Update VK Admin Library.

= 1.4.2 =
[ Bug fix ][ Title ] Fix color code reset

= 1.4.1 =
[ Specification change ] Stop exclude CSS Var by Tree shaking

= 1.4.0 =
[ Improvement ][ title ] Convert to VK Blocks Title from core title block
[ Improvement ][ Select Post List Item ]setting from toolbar and enable search URL
[ Bug fix ][ translate ] in Select Post List Item
[ Bug fix ][ New FAQ ] Fix multiple select
[ Bug fix ][ Accordion ] Fix multiple select
[ Specification change ][ Border Box ] Delete margin-top from first element / delete margin-bottom from last element

= 1.3.9 =
[ Bug fix ][ page ] To be use private page

= 1.3.8 =
[ Bug fix ][ border-box ] Fix not being able to drag and drop other blocks.

= 1.3.7 =
[ Bug fix ][ image ] fix image/photo-frame

= 1.3.6 =
[ Bug fix ][ table ] fix border/stripe

= 1.3.5 =
[ Bug fix ] Fix translattion

= 1.3.4 =
[ Design bug fix ][ flow ] fix dd bug on Lightning G3 and so on
[ Add style ][ table ] border table / border/stripe
[ Bug fix ][ Slider ] fix css bug under active css optimize

= 1.3.3 =
*[ Design bug fix ][ Image ] fix style vk-image-rounded bug for core/image block.

= 1.3.2 =
[ Bug fix ][ Card ] Fix sentence of align control.
[ Bug fix ][ Heading Extention ] Fix editor css is not reflected.
[ Bug fix ][ Button ] Fix Button width of 'Wide' on editor.
[ Bug fix / Specification Change ][ Staff ] Add font selector of heading, mintyo or not.

= 1.3.1 =
[ Bug fix ][ Accordion ] Supports Tree Shaking and add Pro Icon
[ Bug fix ] Fix relative path to full path

= 1.3.0 =
[ Add New Block ][ Accordion ] Add New Accordion Block
[ Translation ][ Post List (Pro) ] Corrected the notation of the classification name
[ Specification change ][ Card ] Remove link tag from title

= 1.2.4 =
[ Bug fix / Specification Change ][ Post List (Pro) ] Fix too many tags and change tag UI
[ Bug fix ][ Slider ] Bug fix under css optimize

= 1.2.3 =
[ Bug fix ][ New FAQ ] Fix PHP notices.

= 1.2.2 =
[ Specification change ][ Slider ] Fix default slide speed and use cubic-bezier.
[ Bug fix ] Fix FontAwsome Popup in case of WP5.7

= 1.2.1 =
[ Bug fix ] Fix translate of VK Blocks Setting

= 1.2.0 =
[ Specification change ] Cope with swiper load tree shaking (improvement of load speed)
[ Bug fix ] toolbar icon display

= 1.1.0 =
[ Specification change ][ Slider Block ] Display multiple images on one slide.

= 1.0.16 =
[ Specification change ] Update VK-Admin Library

= 1.0.15 =
[ Bug fix ][ Outer ] Fixed a bug where hiding doesn't work.

= 1.0.14 =
[ Specification change ][ Post List (Pro) ] Filter terms by taxonomy in sidebar.
[ Translation ] Fix translation in balloon settings.

= 1.0.13 =
[ Bug fix ][ Editor ] Fix editor CSS of Grid Column, Card, Icon Card, Select Post.

= 1.0.12 =
[ Bug fix ][Table Of Contents] fix table of contents order bug.

= 1.0.11 =
[ Bug fix ][PageContent] fix bug can not use newline.

= 1.0.10 =
[ Translation ] Fix translation.

= 1.0.9 =
[ Other ] version only

= 1.0.8 =
[ Fix Translation System ] fix command to generate pot file.

= 1.0.7 =
[ Fix deprecated ][ Card ] fix deprecated.
[ Design bug fix ][ Image ] fix style bug for core/image block.

= 1.0.6 =
[ Design bug fix ][ Slider ][ Staff ][ PR Blocks ][ PR Content ] fix style bug for katawara.

= 1.0.5 =
[ Bug fix ][ Animation ][ Balloon ][ Heading ][ PR Content ][ Icon Card ] Fix data by old versoin needs recovery.
[ Design bug fix ][ QA Block ] Fix style in editor.
[ Design bug fix ][ Button ] Fix style of sub caption.

[ Add Block ] Add Page Content Block
[ Add function ] Add nowrap
[ Add function ][ Balloon ] Add border function

= 1.0.4 =
[ Bug fix ][ Table of Contents ] fix bug for deprecated.

= 1.0.3 =
[ Bug fix ][ Spacer ] fix bug for deprecated.

= 1.0.2 =
[ Bug fix ] fix bug when instert block.

= 1.0.1 =

= 1.0.0 =
[ Other ][ All ] Refactoring all blocks.

= 0.57.4 =
[ Specification change ] Load Term Color on init

= 0.57.3 =
[ Bug fix ][ nowrap ] fix can not work not bootstrap themes.

= 0.57.2 =
[ Other ] Cope with block sample preview

= 0.57.1 =
[ Bug fix ][ Slider ] Fix block need recovery bug in case of filled in custom css class.

= 0.57.0 =
[ Specification change ] Change icons ( include vk icon )

= 0.56.3 =
[ Bug fix ][ Balloon ] fix border bug

= 0.56.2 =
[ Bug fix ][ Balloon ] fix border bug

= 0.56.1 =

= 0.56.0 =
[ Add Block ] Add Page Content Block
[ Add function ] Add nowrap
[ Add function ][ Balloon ] Add border function

= 0.55.0 =
[ Add function ][ Outer ] Background image repeat
[ Bug fix ] fix can not save on some plugin using enviroment
[ Specification change ][ Heading ] delete font weight specification from Plain style

= 0.54.2 =
[ Bug fix ][ Slider ] Fix slider broken in page.

= 0.54.1 =
[ Design bug fix ] Fix do not effect text align on theming by horizontal border

= 0.54.0 =
[ Add function ][ Post List (Pro) ] Add Card (nonorder) layout and add display taxonomy
[ Bug fix ][ Animation ] fix deprecated

= 0.53.2 =
 [ Bug fix ][ ToC (Pro)] Fix id Adding System

= 0.53.1 =
 [ Bug fix ][ Step (Pro)] Fix Step Number Reset System

= 0.53.0 =
 [ Design Change ] VK Blocks Icon design change

= 0.52.2 =
[ Other ] version only

= 0.52.1 =
[ Bug fix ][ Grid Column(Pro) ] Fixed a bug that the hidden specification is removed when re-editing
[ Bug fix ][ Slider(Pro) ] Fix Can not stop loop
[ Bug fix ][ Outer(Pro) ] Fix cant edit outer block(cant save style tag) on Editor role user

= 0.52.0 =
[ Specification change ][ CSS Optimize ] Delete package

= 0.51.0 =
[ Specification change ][ CSS Optimize ] default off / exclude wooCommerce preload
[ Add function ][ CSS Optimize ] Add exclude handles

= 0.50.3 =
[ Other ] version only

= 0.50.2 =
[ Other ] version only

= 0.50.1 =
[ Other ] version only

= 0.50.0 =
[ Specification change ][ CSS Optimize ] Common management of CSS optimization function
[ Bug fix ][ Icon Card ] Fix endless recovery

= 0.49.8 =
[ Bug fix ] fix edit screen white out

= 0.49.7 =
[ Bug fix ] fix edit screen white out
[ Specification change ][ button ] allow some text style
[ Design specification change ][ PR Content ] Change line height

= 0.49.6 =
[ Design bug fix ][ balloon icon ] Fix the icon so that the rectangular image also fits in the square

= 0.49.5 =
[ editor design bug fix ][ balloon icon ] Round trimming of registered icons

= 0.49.4 =
Version change only

= 0.49.3 =
[ Bug fix ][ core/image ] Fix again wave style do not reflected

= 0.49.2 =
[ Bug fix ] fix edit screen white out

= 0.49.1 =
[ Bug fix ][ PR Content ] fix can not click button at textarea
[ Bug fix ][ core/image ] fix wave style do not reflected

= 0.49.0 =
[ Add function ][ Responsive Spacer ] add space-type (can be select margin-top)
[ Add function ][ Old FAQ ] Can be accordion (Pro version only)
[ Bug fix ][ Child page list ] Can be select "this page" ( In case of only one page that has children, can't select that page on other page's put child page list )

= 0.48.0 =
[ Bug fix ][ Outer ] Fix bug of border-color clear button.
[ Add Style ][ Core/Image ] add extra style to core/image.

= 0.47.0 =
[ Add function ][ Post List(Pro) ] add order by title / add order desc/asc

= 0.46.1 =
[ Bug fix ][ New FAQ ] Fix accordion system on enable Tree Shaking

= 0.46.0 =
[ Add function ][ New FAQ ] Add accordion(Pro)
[ Bug fix ][ Grid Column(Pro) ] fix unexpected class name "undefined"

= 0.45.2 =
[ Bug fix ] Fix bug in card block deparection.

= 0.45.1 =
[ Bug fix ] Delete useless file

= 0.45.0 =
[ Add function ] Responsive BR

= 0.44.13 =
[ Design bug fix ] vk_heading plain css adjustment

= 0.44.12 =
[ Design bug fix ] Block heading css adjustment

= 0.44.11 =
version only

= 0.44.10 =
[ Bug fix ][ Grid column(Pro) ] fix could choice 5 columns

= 0.44.9 =
* rebuild

= 0.44.8 =
[ Bug fix ][ Grid column(Pro) ] Fix block wrap on xxl

= 0.44.7 =
[ Bug fix ][ block pattern(Pro) ] Fix block pattern

= 0.44.6 =
[ Bug fix ][ block pattern(Pro) ] Fix block pattern
[ Bug fix ] Stop ExUnit VK Blocks
[ Bug fix ][ border box ] Cope with lightning pro heading design ( balloon )

= 0.44.5 =
[ Design bug fix ] Block heading design text align tuning

= 0.44.4 =
version only

= 0.44.3 =
 [ Specification change ] abolish preload
 [ Design bug fix ] Block heading design font color tuning

= 0.44.2 =
[ Design bug fix ] Block heading design tuning

= 0.44.1 =
[ Design tuning ] Tag change

= 0.44.0
[ Specification change ] Re Cope with xxl size
[ Bug fix ][ Card(Pro) ] fix critical error on card and recover to 0.43.0
[ Bug fix ][ Child Page(Pro) ] fix use in vk page widget
[ Bug fix ][ Grid Column(Pro) ] New column drag bug fix

= 0.43.4(0.42.1) =
rebuild 0.42.1

= 0.43.3(0.42.1) =
revert to 0.42.1

= 0.43.2 =
Add translate

= 0.43.0 =
[ Specification change ] Cope with xxl size
[ Specification change ] Stop ExUnit VK Blocks on VK Blocks Free
[ Bug fix ][ Flow block ] Arrow image don't display on using tree shaking

= 0.42.1 =
[ Specification change ][ term color ] Add exclude taxonomy

= 0.42.0 =
[ Add function ][ Block Template Setting ] Add Block pattern Display and Hide setting.

= 0.41.2 =
[ Bug fix ][ Button ] Fix static id of button again.

= 0.41.1 =
[ Bug fix ][ Button ] Fix static id of button.

= 0.41.0 =
[ Add function ][ Animation(Pro) ] Add animation variation
[ Add New Block ][ New FAQ ] Add New FAQ Block

= 0.40.3 =
[ Bug fix ][ BorderBox ] Fix collapse under the tree shaking

= 0.40.2 =
[ Bug fix ][ Grid Column ] Fix collapse in edit screen

= 0.40.1 =
[ Bug Fix / Specification Change ][ Heading ] Fix margin of title below, and change default value of title below margin.

= 0.40.0 =
[ Add function ] CSS Optimize

= 0.39.5 =
[ Bug fix ][ slider ] Fix collapse of first view

= 0.39.4 =
[ Bug fix ][ border-box ] Fix btn-primary on editor side.

= 0.39.3 =
[ Bug fix ][ border-box ] Fix heading color.

= 0.39.2 =
[ Bug fix ] Fix button color of editor on WP 5.5

= 0.39.1 =
[ Bug fix ] Posts list date bug fix by WP5.5

= 0.39.0 =
[ Add function ] Add VK Block's Block patterns.

= 0.38.9 =
[ Bug fix ][ Outer(Pro) ] Background cover become do not worked bug fix.

= 0.38.8 =
[ Bug fix ] Rebuild

= 0.38.6 =
[ Bug fix ] Fix button layout settings

= 0.38.5 =
[ Specification change ] Delete button class in src/blocks/button/

= 0.38.4 =
[ Bug fix ][ Card(Pro) ] Image height change bug fix

= 0.38.3 =
[ Bug fix ][ Card(Pro) ] Image height change bug fix

= 0.38.2 =
[ Bug Fix / Specification Change ][ Balloon ] Change Balloon HTML structure

= 0.38.1 =
[ Add new block ][ Slider(Pro) ]
[ Add function ][ Card(Pro) ] Add Image height setting
[ Bug Fix / Specification Change ][ Balloon ] Change Balloon width 100%
[ Add function ][ Border box ] add fill background

= 0.37.4 =
[ Specification change ] Admin directory change

= 0.37.3 =
[ Specification change ] Admin lib link change

= 0.37.2 =
[ Bug Fix ] PHP error

= 0.37.1 =
[ Bug Fix ][ Title ] Set text erased

= 0.37.0 =
[ Add function ][ Balloon ] Fixed the problem that the entered text disappears

= 0.36.0 =
[ Add function ][ FAQ ] The answer part has been changed to an inner block and it is now possible to place other than letters
[ Add function ][ FAQ ] You can now choose the design style
[ Add function ][ Title ] Icons can be used before and after letters
[ Add function ][ Balloon ] You can now choose the style of image
[ Add function ][ Animation(Pro) ] Addition of animation speed/distance specification function
[ Add function ][ Card(Pro) ] Add an inner block area to the text part You can add and arrange any blocks you like.
[ Add function ][ Child Page(Pro) ] Do not display pages without child pages in pulldown
[ Bug fix ][ Timeline(Pro) ] Fixed a bug that color cannot be reset

= 0.35.5 =
[ Specification change ][ Border Box ] icon list update

= 0.35.4 =
[ Bug fix ][ BorderBox ] icon list layout bug fix

= 0.35.3 =
build only

= 0.35.2 =
build only

= 0.35.1 =
[ Bug fix ][ Step Block ] Can not edit bug fix

= 0.35.0 =
[ Add function ] Fontawesome icon selector update
[ Add Setting ][ Outer(Pro) ] Add LR no padding
[ Specification change ][ Border Box ] Padding tuning ( Wide screen )

= 0.34.0 =
[ Add block ][ Animation(Pro) ]
[ Add function ] Add fontawesome icon selector to Border box block

= 0.33.3 =
[ Bug fix ] reverse to 0.33.1

= 0.33.2 =
[ Bug fix ][ Card (pro) ] if url is null, no a tag of card title.

= 0.33.1 =
[ Bug fix ][ Table of content (pro) ] open close bug fix

= 0.33.0 =
[ Add function ][ Card (pro) ] Cope with card block Image round
[ Bug fix ][ Table of content (pro) ] open close bug fix

= 0.32.5 =
[ Other ] Update Block Template Pattern (Pro)

= 0.32.4 =
[ Bug fix ][ Outer ] Background fix bug.

= 0.32.3 =
[ Bug fix ][ Outer ] Background fix bug.

= 0.32.2 =
[ Bug fix ][ Icon Card ] Fix alignment of icon and card item.

= 0.32.1 =
[ Bug fix ][ Border Box ] Fix title design is overwritten by theme.

= 0.32.0 =
[ Add new block ][ Icon Card (pro) ]
[ Specification change ] Font Awesome Version 5.13.0
[ Specification change ] Bootstrap Version 4.5.0

= 0.31.0 =
[ Add new block ][ Border Box ]
[ Bug fix ][ Outer (pro) ] BG fix error on iPhone

= 0.30.0 =
[ Add function ][ Post list (pro) / Card (pro) / Grid Column (pro) ] Cope with 6 column

= 0.29.7 =
[ Bug fix ][ Outer (pro) ] fix outer block error

= 0.29.6 =
[ Delete function ][ Group block ] Delete border custom color

= 0.29.5 =
[ Design tuning ][ Group block style ] Add alert style

= 0.29.4 =
build only

= 0.29.3 =
[ Bug fix ][ toc (pro) ] Cope with old type toc

= 0.29.2 =
build only

= 0.29.1 =
[ Bug fix ][ toc (pro) ] Cope with deprecated

= 0.29.0 =
[ Add new block ][ Grid Column(pro) ]
[ Add function ][ Group ] Use custom border color
[ Bug fix ][ toc (pro) ] Many title character number
[ Bug fix ][ list ] 2digits number display bug fix

= 0.28.4 =
[ Bug fix ] Drag and drop inserter bug fix

= 0.28.3 =
[ Bug fix ][ toc (pro) ] Marker tag error fix

= 0.28.2 =
[ Bug fix ][ toc (pro) ] Revert

= 0.28.1 =
[ Bug fix ][ toc (pro) ] Marker tag error fix

= 0.28.0 =
[ Bug fix ][ toc (pro) ] close toc block bug fix
[ Add function ][ Child Page List (pro) ] function of exclude this page.

= 0.27.5 =
[ Bug fix ][ Step (pro) ] When add inner block that real time step number bug fix.

= 0.27.4 =
[ Bug fix ][ Post List(pro) ] Additional class name not reflected bug fix

= 0.27.3 =
build only

= 0.27.2 =
[ Bug fix ][ Outer(pro) ] when reset border that block breaks
[ Bug fix ][ Table Of Content(pro) ] Can't all display title

= 0.27.1 =
[ Bug fix ][ PR Content ] Layout bug fix on firefox
[ Design tuning ][ Table of content(pro) ] Padding tuning

= 0.27.0 =
[ Add function ][ Outer(pro) ] Responsive background image
[ Specification change ][ staff ] Change title size
[ Specification change ][ Responsive Spacer ] Display dotted line in edit screen

= 0.26.9 =
[ Bug fix ][ Card(pro) ] column bug fix at edit screen

= 0.26.8 =
[ Bug fix ][ Card(pro) ] Disappear card url bug fix.

= 0.26.7 =
[ Bug fix ][ Card(pro) ] column bug fix at edit screen

= 0.26.6 =
[ Bug fix ] Dynamic attribute class error

= 0.26.4 =
[ Bug fix ][ button ] Change Color bug fix.

= 0.26.3 =
[ Bug fix ][ button ] outline style bug fix.

= 0.26.2 =
[ Bug fix ] Auto stop function bug fix of Both(Free and Pro) used.

= 0.26.0 =
[ Add function ][ button ] Add text style
[ Add function ][ button ] Add wide style
[ Bug fix ] Bug fix of when use hidden function and Posts column

= 0.25.4 =
[ Bug fix ][ term color ] Fixed bug that gives arise from no custom taxonomy / term.

= 0.25.3 =
[ Bug fix ][ Post list(Pro) ] Offset Bug Fix
[ Bug fix ][ Card ] Block clash bug fix
[ Bug fix ][ Hidden Function ] ReEnable


= 0.25.1 =
[ Specification change ][ Hidden Function ] Exclude Reuse block

= 0.25.0 =
[ Add function ][ Hidden Function ] Add
[ Add function ][ Post list(Pro) ] Add text list layout

= 0.24.2 =
[ Bug fix ][ Table of Content(Pro) ] Title in Outer Block bug fix

= 0.24.1 =
[ Bug fix ] Cope with WordPress5.4
[ Bug fix ][ Block Template (Pro) ] indent bug fix

= 0.24.0 =
[ Add function ][ Template block(Pro) ] Add
[ Add function ][ Table of Content(Pro) ] Add Template block
[ Bug fix ][ Table of Content(Pro) ] Link Target Point

= 0.23.0 =
[ Add function ][ post list(Pro) ] Add setting ( offset / order / exclude self )
[ Bug fix ][ Outer(Pro) ] Can't remove background image fix

= 0.22.4 =
[ Bug fix ] Editor style CSS not working

= 0.22.3 =
[ Bug fix ][ PR Content ] Error fix
[ Specification change ] Add css load point hook
[ Specification change ][ YouTube ] Add 100% width

= 0.22.2 =
[ Specification change( Revert ) ] load css on header from footer

= 0.22.0 =
[ Specification change ] load font awesome on footer from header

= 0.21.0 =
[ Specification change ] load css on footer from header

= 0.20.4 =
[ Bug fix ][ Card(Pro) ] reuse and group bug fix

= 0.20.3 =
[ Bug fix ][ PR Content ] fix deprecated

= 0.20.2 =
[ Bug fix ][ Card(Pro) ] fix md size convert error

= 0.20.1 =
[ Specification change ][ Card(Pro) ] btn margin tuning

= 0.20.0 =
[ add block ][ Card block ] * Pro Version Only
[ Specification change ] Delete Lightning Pro Limited

= 0.19.2 =
[ Design tuning ][ Child Page List(Pro) ] Add margin top
[ Specification change ][ Post-list(Pro) / child-page(Pro) ] Change Outer class name

= 0.19.1 =
[ Bug fix ] vk-components.css link
[ Specification change ] Exclude link to vk-components.css for lightning user

= 0.19.0 =
[ Add function ][ QA Block ] Add html anchor

= 0.18.2 =
[ Bug fix ][ Post list (Pro) ] Display item control

= 0.18.0 =
[ Bug fix ][ Post list (Pro) ] item control
[ Add function ][ Child Page (Pro) ] Can be set the parent page.

= 0.17.7 =
[ Bug fix ] Table of contents render bug fix
[ Design tuning ][ Step block / Timeline block ]

= 0.17.6 =
[ Bug fix ] heading style color bug fix

= 0.17.5 =
[ Bug fix ] viewHelpers.js Reregistration

= 0.17.4 =
[ deploy setting from GitHub ]

= 0.17.2 =
[ Bug fix ][ Button ] Custom color palette.
[ Bug fix ][ PR Block ] Fixed image alt

= 0.17.1 =
[ Bug fix ][ Button ] Fixed input text bug on Safari
[ Bug fix ][ Balloon / PR Content ] Fixed image alt

= 0.17.0 =
[ add block ][ child page list block ] * Pro Version Only
[ add style ][ group block ] border round
[ add id ][ Responsive spacer ] add specified id by user

= 0.16.4 =
[ Bug fix ][ step block ] font size
[ Bug fix ][ heading style ] add clear :before and :after

= 0.16.3 =
[ Bug fix ] deactivate free version function

= 0.16.2 =
[ Bug fix ] function declare

= 0.16.0 =
[ Add Style ] Add title styles

= 0.15.1 =
[ Design tuning ] Delete margin bottom of the last block in the group block.

= 0.15.0 =
[ Add Style ] Add Group styles
[ Add Style ] Add Image styles

= 0.14.0 =
[ Add Style ] Add list styles

= 0.13.3 =
[ Bug fix ][ Step ] first block don't become H4 in second later item block bug fix.

= 0.13.2 =
[ Specification change ] Delete width specified of edit page width ( Change to the theme specify )

= 0.13.1 =
[ Specification change ][ Step Block ] Delete First Caption

= 0.13.0 =
[ Add Block ] Step Block
[ Add Block ] Timeline Block

= 0.12.7 =
[ Bug fix ][ table of contents ] bug fix

= 0.12.6 =
[ Bug fix ][ column ] WordPress 5.3 column bug fix

= 0.12.5 =
[ Bug fix ][ Post List (Pro) ] WordPress 5.3 bug fix

= 0.12.4 =
[ Bug fix ][ Post List (Pro) ] button display bug fix

= 0.12.3 =
[ Bug fix ] updater

= 0.12.2 =
[ Bug fix ][ Post List (Pro) ] taxonomy bug list
[ add function ] updater

= 0.12.1 =
[ Bug fix ] common css html font size

= 0.12.0 =
[ Add New Block ] Post List Block

= 0.10.1 =
[ Bug fix ] balloon css

= 0.10.0 =
[ Add function ] Add marker

= 0.9.0 =
[Add New Block] Table Of Contents ( Pro Only )

= 0.8.3 =
[Specification change][ Staff ] CSS minor tuning

= 0.8.2 =
[Specification change][ Staff ] Change H tag and other.

= 0.8.1 =
[Bug fix] When title margin set that Title align not work.

= 0.8.0 =
[Add New Block] Staff

= 0.7.1 =
[Bug fix][ Title ] Part of margin bottom functions was not working.

= 0.7.0 =
[Bug fix][ PR Blocks ] When link url not set that no print a Tags
[Specification change][ PR Blocks ] Change outer tag article to div
[Specification change][ PR Blocks ] Change h1 tag to h3 tag

= 0.6.0 =
[Add New Block] Title
[Add New Block] Responsive Spacer
[Bug fix][ outer ] FireFox and Edge design fix
[Add Function][ Outer ] Add link id setting

= 0.5.2 =
[Bug fix][ outer ] Lightning Pro theme（No child） does not work bug fix

= 0.5.1 =
[Bug fix][ outer ] Fix bug where child theme does not work

= 0.5.0 =
[Add Function][ outer ] Add border

= 0.4.1 =
[Specification change][ PR Content ] markup change
[Add Function][ button ] Add caption

= 0.4.0 =
[Add New Block] Outer
[Add New Block] PR Content

= 0.3.0 =
[Add New Block] Button Block
[Add New Block] PR Blocks Block

= 0.2.2 =
[bug fix] Fixed bug that becomes unusable in WordPress 5.0

= 0.2.0 =
[Specification change] CSS Name rule Changed

= 0.1.0 =
First release
