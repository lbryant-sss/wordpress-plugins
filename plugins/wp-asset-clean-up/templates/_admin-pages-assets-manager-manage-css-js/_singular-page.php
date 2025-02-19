<?php
/*
 * No direct access to this file
 */

use WpAssetCleanUp\MetaBoxes;
use WpAssetCleanUp\Misc;

if (! isset($data)) {
	exit;
}
?>
    <div style="margin: 18px 0 0;" class="clearfix"></div>
<?php
$wpacuNoLoadInTargetPage = false;

if (isset($data['post_id']) && $data['post_id']) {
	$data['fetch_url'] = Misc::getPageUrl( $data['post_id'] );
}

$strAdminUrl  = 'admin.php?page='.WPACU_PLUGIN_ID.'_assets_manager&wpacu_for='.$data['for'];

if ( $data['for'] !== 'homepage' && isset($data['post_id']) && $data['post_id'] ) {
    $strAdminUrl .= '&wpacu_post_id=' . $data['post_id'];
}

$strAdminUrl .= '&wpacu_rand='.uniqid(time(), true);

if ( isset($_GET['wpacu_manage_dash']) || isset($_REQUEST['force_manage_dash']) ) { // For debugging purposes
    $strAdminUrl .= '&wpacu_manage_dash';
}

// Show the search form on tabs such as "Posts", "Pages", "Custom Post Types"
// Do not how it in the homepage (that was set to be a singular page) as it could confuse the admin
if ( ! (isset($data['is_homepage_tab']) && $data['is_homepage_tab']) ) {
    require_once __DIR__ . '/_singular-page-search-form.php';
}
?>
<form id="wpacu_dash_assets_manager_form" method="post" action="<?php echo esc_url(admin_url($strAdminUrl)); ?>">
    <input type="hidden"
           id="wpacu_manage_singular_page_assets"
           name="wpacu_manage_singular_page_assets"
           value="1" />

    <?php
    if ( isset($data['external_srcs_ref']) && $data['external_srcs_ref'] ) {
        ?>
            <span data-wpacu-external-srcs-ref="<?php echo esc_attr($data['external_srcs_ref']); ?>" style="display: none;"></span>
        <?php
    }
    ?>

    <input type="hidden"
           id="wpacu_manage_singular_page_id"
           name="wpacu_manage_singular_page_id"
           value="<?php echo (int)$data['post_id']; ?>" />

    <input type="hidden"
           id="wpacu_ajax_fetch_assets_list_dashboard_view"
           name="wpacu_ajax_fetch_assets_list_dashboard_view"
           value="1" />

    <?php
    if (isset($data['post_type']) && $data['post_type']) {
        $postTypeObject = get_post_type_object( $data['post_type'] );
        $postTypeLabels = $postTypeObject->labels;
        $postName = $postTypeLabels->singular_name;
    }

    if (isset($data['is_homepage_tab']) && $data['is_homepage_tab']) {
        $pageUrlTitle = __('Homepage URL', 'wp-asset-clean-up');
    } else {
        $pageUrlTitle = __('Page URL', 'wp-asset-clean-up');
    }
    ?>
    <div class="wpacu_verified">
        <strong><?php echo esc_html($pageUrlTitle); ?>:</strong> <a target="_blank" href="<?php echo esc_url($data['fetch_url']); ?>"><span><?php echo esc_url($data['fetch_url']); ?></span></a>
        | <strong><?php echo isset($postName) ? esc_html($postName) : ''; ?> Title:</strong> <?php echo get_the_title($data['post_id']); ?> | <strong>Post ID:</strong> <?php echo (int)$data['post_id']; ?>
    </div>

    <?php
    $wpacuNoLoadMatchesStatus = assetCleanUpHasNoLoadMatches($data['fetch_url'], true);
    if ($wpacuNoLoadInTargetPage = in_array($wpacuNoLoadMatchesStatus, array('is_set_in_settings', 'is_set_in_page'))) {
	    if ($wpacuNoLoadMatchesStatus === 'is_set_in_settings') { // Asset CleanUp Pro is set not to load for the front-page
		    ?>
            <p class="wpacu-warning"
               style="margin: 15px 0 0; padding: 10px; font-size: inherit; width: 99%;">
            <span style="color: red;"
                  class="dashicons dashicons-info"></span> <?php echo sprintf(__('This page\'s URI is matched by one of the RegEx rules you have in <strong>"Settings"</strong> -&gt; <strong>"Plugin Usage Preferences"</strong> -&gt; <strong>"Do not load the plugin on certain pages"</strong>, thus %s is not loaded on that page and no CSS/JS are to be managed. If you wish to view the CSS/JS manager, please remove the matching RegEx rule and reload this page.', 'wp-asset-clean-up'), WPACU_PLUGIN_TITLE); ?>
            </p>
		    <?php
	    } elseif ($wpacuNoLoadMatchesStatus === 'is_set_in_page') { // Asset CleanUp Pro is set not to load for the front-page
		    ?>
            <p class="wpacu-warning"
               style="margin: 15px 0 0; padding: 10px; font-size: inherit; width: 99%;">
            <span style="color: red;"
                  class="dashicons dashicons-info"></span> <?php echo sprintf(__('This page\'s URI is matched by the rule you have in the "Page Options", thus %s is not loaded on that page and no CSS/JS are to be managed. If you wish to view the CSS/JS manager, please uncheck the following option shown below: <em>"Do not load Asset CleanUp Pro on this page (this will disable any functionality of the plugin"</em>.', 'wp-asset-clean-up'), WPACU_PLUGIN_TITLE); ?>
            </p>
		    <?php
	    }

        $data['show_page_options'] = true;

        if ($data['post_id'] > 0) {
	        $pageOptionsType = 'post';
        } elseif (isset($data['is_homepage_tab']) && $data['is_homepage_tab']) {
            $pageOptionsType = 'front_page';
        }

        $data['page_options'] = MetaBoxes::getPageOptions($data['post_id'], $pageOptionsType);
        $data['page_options_with_assets_manager_no_load'] = true;

        include dirname(__DIR__).'/meta-box-loaded-assets/_page-options.php';
    } else {
    ?>
        <div id="wpacu_meta_box_content">
            <?php
            // "Select a retrieval way:" is set to "Direct" (default one) in "Plugin Usage Preferences" -> "Manage in the Dashboard"
            if ($data['wpacu_settings']['dom_get_type'] === 'direct') {
                ?>
                <div id="wpacu-list-step-default-status" style="display: none;"><img src="<?php echo esc_url(admin_url('images/spinner.gif')); ?>" align="top" width="20" height="20" alt="" />&nbsp; Please wait...</div>
                <div id="wpacu-list-step-completed-status" style="display: none;"><span style="color: green;" class="dashicons dashicons-yes-alt"></span> Completed</div>
                <div>
                    <ul class="wpacu_meta_box_content_fetch_steps">
                        <li id="wpacu-fetch-list-step-1-wrap"><strong>Step 1</strong>: Fetch the assets from the targeted page... <span id="wpacu-fetch-list-step-1-status"><img src="<?php echo esc_url(admin_url('images/spinner.gif')); ?>" align="top" width="20" height="20" alt="" />&nbsp; Please wait...</span></li>
                        <li id="wpacu-fetch-list-step-2-wrap"><strong>Step 2</strong>: Build the list of the fetched assets and print it... <span id="wpacu-fetch-list-step-2-status"></span></li>
                    </ul>
                </div>
                <?php
            } else {
                // "Select a retrieval way:" is set to "WP Remote POST" (one AJAX call) in "Plugin Usage Preferences" -> "Manage in the Dashboard"
                ?>
                <img src="<?php echo esc_url(admin_url('images/spinner.gif')); ?>" align="top" width="20" height="20" alt="" />&nbsp;
                <?php esc_html_e('Retrieving the loaded scripts and styles for the home page. Please wait...', 'wp-asset-clean-up');
            }
            ?>

            <p><?php echo sprintf(
                    __('If you believe fetching the page takes too long and the assets should have loaded by now, I suggest you go to "Settings", make sure "Manage in front-end" is checked and then %smanage the assets in the front-end%s.', 'wp-asset-clean-up'),
                    '<a href="'.esc_url($data['fetch_url']).'#wpacu_wrap_assets">',
                    '</a>'
                ); ?></p>
        </div>
    <?php
    }

    wp_nonce_field($data['nonce_action'], $data['nonce_name']);
    ?>
    <div id="wpacu-update-button-area" class="no-left-margin">
        <p class="submit"><input type="submit" name="submit" class="button button-primary <?php if ( ! $wpacuNoLoadInTargetPage ) { ?> hidden <?php } ?>" value="<?php esc_attr_e('Update', 'wp-asset-clean-up'); ?>"></p>
        <div id="wpacu-updating-settings" style="margin-left: 100px;">
            <img src="<?php echo esc_url(admin_url('images/spinner.gif')); ?>" align="top" width="20" height="20" alt="" />
        </div>
    </div>
</form>