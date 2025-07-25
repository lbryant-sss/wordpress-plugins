<?php
/*
Advanced iFrame Pro
https://www.advanced-iframe.com/advanced-iframe
Michael Dempfle
Administration include
*/
?>
<?php
defined('_VALID_AI') or die('Direct Access to this location is not allowed.');

include_once dirname(__FILE__) . '/includes/advanced-iframe-admin-functions.php';
include_once dirname(__FILE__) . '/includes/advanced-iframe-admin-quickstart.php';

global $aiVersion, $isFreemius, $isFreemiusMigration, $showFreemiusMigration, $ai_fs, $aiSlug;

$updated = false;
$evanto = (file_exists(dirname(__FILE__) . "/includes/class-cw-envato-api.php"));
if ($isFreemius) {
  global $ai_fs;
  $evanto = $ai_fs->can_use_premium_code__premium_only();
}

if (is_user_logged_in() && is_admin()) {
  $scrollposition = 0;
  $devOptions = $this->getAiAdminOptions();

  if ($devOptions['admin_was_loaded'] === false) {
    // we disable the check of the src.
    $devOptions['check_iframe_url_when_load'] = 'false';
    echo '<div class="error"><p><strong>';
    esc_html_e('The administration was not loaded until the end last time. It seems the integrated check of the "URL" field failed and therefore "Check URL on load" and "Check iframes on save" are now disabled. You can enable this again on the "Options" tab. Check the description of this options what maybe caused this problem.', 'advanced-iframe');
    echo '</strong></p></div>';
  }

  $devOptions['admin_was_loaded'] = false;
  update_option($this->adminOptionsName, $devOptions);

  if ($evanto) {
    $devOptions['demo'] = 'false';
  } else {
    $devOptions['alternative_shortcode'] = '';
  }


  if (isset($_POST['scrollposition'])) {
    $scrollposition = urlencode($_POST['scrollposition']);
  }

  $is_latest = true;
  if ($evanto) {
    $latest_version = $this->ai_getlatestVersion();
    if ($latest_version != -1) {
      if (version_compare($latest_version, $aiVersion) === 1) {
        if (!(isset($devOptions['closed_messages']) && isset($devOptions['closed_messages']['show-version-message']))) {
          echo '<div id="show-version-message" class="notice notice-success is-dismissible is-permanent-closable"><p><strong>';
          echo __('Version ', 'advanced-iframe') . $latest_version . __(' of Advanced iFrame Pro is available. See the <a href="//www.advanced-iframe.com/advanced-iframe/advanced-iframe-history" target="_blank">history</a> for details. Please download the latest version from your download page of codecanyon.', 'advanced-iframe');
          echo '</strong></p></div>';
        }
        $is_latest = false;
      }
    } else {
      $is_latest = true;
    }
  } else {
    $is_latest = false;
  }

  $current_tab = ($devOptions['donation_bottom'] === 'false') ? 0 : 1;
  if (isset($_POST['current_tab'])) {
    $current_tab = urlencode($_POST['current_tab']);
  }
  if (isset($_GET['current_tab'])) {
    $current_tab = urlencode($_GET['current_tab']);
  }
  $current_tab = aiProcessConfigActions($current_tab);

  if (isset($_POST['update_iframe-loader'])) { //save option changes
    $adminSettings = array('securitykey', 'src', 'width', 'height', 'scrolling',
      'marginwidth', 'marginheight', 'frameborder', 'transparency',
      'content_id', 'content_styles', 'hide_elements', 'class',
      'shortcode_attributes', 'url_forward_parameter', 'id', 'name',
      'onload', 'onload_resize', 'onload_scroll_top', 'loading',
      'additional_js', 'additional_css', 'store_height_in_cookie', 'additional_height',
      'iframe_content_id', 'iframe_content_styles', 'iframe_hide_elements', 'version_counter',
      'onload_show_element_only', 'donation_bottom',
      'include_url', 'include_content', 'include_height', 'include_fade', 'include_hide_page_until_loaded',
      'onload_resize_width', 'resize_on_ajax', 'resize_on_ajax_jquery', 'resize_on_click',
      'resize_on_click_elements', 'hide_page_until_loaded',
      'show_part_of_iframe', 'show_part_of_iframe_x', 'show_part_of_iframe_y',
      'show_part_of_iframe_width', 'show_part_of_iframe_height',
      'show_part_of_iframe_new_window', 'show_part_of_iframe_new_url',
      'show_part_of_iframe_next_viewports_hide', 'show_part_of_iframe_next_viewports',
      'show_part_of_iframe_next_viewports_loop', 'style',
      'use_shortcode_attributes_only', 'enable_external_height_workaround',
      'keep_overflow_hidden', 'hide_page_until_loaded_external',
      'onload_resize_delay', 'expert_mode', 'accordeon_menu',
      'show_part_of_iframe_allow_scrollbar_vertical', 'show_part_of_iframe_allow_scrollbar_horizontal',
      'hide_part_of_iframe', 'change_parent_links_target',
      'change_iframe_links', 'change_iframe_links_target',
      'iframe_redirect_url', 'show_part_of_iframe_style',
      'map_parameter_to_url', 'iframe_zoom',
      'tab_visible', 'tab_hidden', 'enable_responsive_iframe',
      'allowfullscreen', 'iframe_height_ratio',
      'show_iframe_loader', 'enable_lazy_load',
      'enable_lazy_load_threshold', 'enable_lazy_load_fadetime',
      'pass_id_by_url', 'include_scripts_in_footer',
      'enable_lazy_load_manual', 'write_css_directly',
      'resize_on_element_resize', 'resize_on_element_resize_delay',
      'add_css_class_parent',
      'auto_zoom', 'single_save_button', 'enable_lazy_load_manual_element',
      'alternative_shortcode', 'load_jquery',
      'show_iframe_as_layer', 'auto_zoom_by_ratio',
      'add_iframe_url_as_param', 'add_iframe_url_as_param_prefix',
      'add_iframe_url_as_param_direct', 'use_iframe_title_for_parent',
      'reload_interval', 'iframe_content_css',
      'additional_js_file_iframe', 'additional_css_file_iframe',
      'add_css_class_iframe',
      'enable_lazy_load_reserve_space', 'editorbutton',
      'hide_content_until_iframe_color', 'include_html',
      'enable_ios_mobile_scolling', 'sandbox',
      'show_iframe_as_layer_header_file', 'show_iframe_as_layer_header_height',
      'show_iframe_as_layer_header_position', 'show_iframe_as_layer_full',
      'demo', 'show_part_of_iframe_zoom',
      'external_height_workaround_delay',
      'add_document_domain', 'document_domain',
      'multi_domain_enabled', 'check_shortcode',
      'use_post_message', 'element_to_measure_offset',
      'data_post_message', 'element_to_measure',
      'show_iframe_as_layer_keep_content', 'roles',
      'parent_content_css', 'debug_js',
      'check_iframe_cronjob', 'check_iframe_cronjob_email',
      'enable_content_filter', 'add_ai_external_local', 'title',
      'check_iframes_when_save', 'admin_was_loaded',
      'check_iframe_url_when_load', 'modify_iframe_if_cookie',
      'allow', 'safari_fix_url', 'external_scroll_top',
      'change_iframe_links_href', 'delete_options_db',
      'inline_config_file', 'replace_iframe_tags',
      'check_iframe_batch_size', 'check_iframe_cronjob_email_always',
      'remove_elements_from_height', 'add_ai_to_all_pages',
      'show_part_of_iframe_media_query', 'remove_page_param_from_query',
      'src_hide', 'purchase_code', 'cookie_samesite_filter',
      'show_iframe_as_layer_autoclick_delay', 'show_iframe_as_layer_autoclick_hide_time',
      'fullscreen_button', 'optimize_rendering',
      'referrerpolicy', 'add_surrounding_p',
      'custom', 'fullscreen_button_hide_elements',
      'fullscreen_button_full', 'fullscreen_button_style',
      'enable_ai_content_pages', 'show_support_message' 
    );
    if (!wp_verify_nonce($_POST['twg-options'], 'twg-options')) {
      die('Sorry, your nonce did not verify.');
    }

    if (!isset($_POST['action']) || $_POST['action'] !== 'reset') {
      foreach ($adminSettings as $item) {
        if ($item === 'version_counter') {
          $text = rand(100000, 999999);
        } elseif ($item === 'additional_height') {
          $text = trim(trim($_POST[$item]), 'px%emt'); // remove px...
        } else {
          if (isset($_POST[$item])) {
            $text = trim($_POST[$item], " \n\r\t\v\0,");
          } else {
            if ($item === 'show_part_of_iframe' || $item === 'show_part_of_iframe_next_viewports_loop'
              || $item === 'show_iframe_loader' || $item === 'enable_lazy_load_manual'
              || $item === 'show_part_of_iframe_next_viewports_hide' || $item === 'write_css_directly'
              || $item === 'enable_responsive_iframe' || $item === 'enable_lazy_load'
              || $item === 'accordeon_menu'
              || $item === 'show_iframe_as_layer' || $item === 'add_iframe_url_as_param'
              || $item === 'add_iframe_url_as_param_direct' || $item === 'use_iframe_title_for_parent'
              || $item === 'auto_zoom' || $item === 'show_part_of_iframe_zoom'
              || $item === 'demo' || $item === 'enable_ios_mobile_scolling'
              || $item === 'store_height_in_cookie' || $item === 'show_iframe_as_layer_full'
              || $item === 'use_post_message'
              || $item === 'enable_content_filter' || $item === 'add_ai_external_local'
              || $item === 'check_iframe_cronjob' || $item === 'modify_iframe_if_cookie'
              || $item === 'add_ai_to_all_pages' || $item === 'remove_page_param_from_query'
              || $item === 'hide_part_of_iframe' || $item === 'fullscreen_button') {
              $text = 'false';
            } elseif ($item === 'resize_on_ajax_jquery'
              || $item === 'show_iframe_as_layer_keep_content' || $item === 'admin_was_loaded'
              || $item === 'single_save_button' || $item === 'multi_domain_enabled') {
              $text = 'true';
            } elseif ($item === 'resize_on_element_resize_delay') {
              $text = '250';
            } elseif ($item === 'show_iframe_as_layer_header_height') {
              $text = '100';
            } elseif ($item === 'show_iframe_as_layer_header_position') {
              $text = 'top';
            } elseif ($item === 'external_height_workaround_delay' || $item === 'element_to_measure_offset' ||
              $item === '$show_iframe_as_layer_autoclick_delay' || $item === 'show_iframe_as_layer_autoclick_hide_time') {
              $text = '0';
            } elseif ($item === 'element_to_measure') {
              $text = 'default';
            } elseif ($item === 'check_iframe_batch_size') {
              $text = '100';
            } elseif ($item === 'roles' || $item === 'inline_config_file') {
              $text = 'none';
            } else {
              $text = '';
            }
          }
        }

        // Mixed single and double quotes are only allowed for the parameters below because they do support
        // shortcodes as input where both quotes are used.
        if ($item != 'src' && $item != 'onload') {
          $text = str_replace('"', "'", $text);
        } elseif ($devOptions['src'] !== $text) {
          delete_transient('aip_cache_check_' . $devOptions['src']);
        }
        if ($item === 'purchase_code') {
          if (!preg_match("/^([a-f0-9]{8})-(([a-f0-9]{4})-){3}([a-f0-9]{12})$/i", $text)) {
            $text = '';
          }
        }
        if ($item === 'enable_ai_content_pages' && $text === 'true') {
          flush_rewrite_rules();
        }
        if ($item === 'roles') {
          // roles can only be changed by administrators!
          $user = wp_get_current_user();
          if (in_array('administrator', (array)$user->roles)) {
            $devOptions[$item] = stripslashes($text);
          }
          // replace ' with "
        } elseif ($item === 'include_url' || $item === 'src') {
          $text = str_replace('{', '__BRACKETS_OPEN__', $text);
          $text = str_replace('}', '__BRACKETS_CLOSE__', $text);
          $text = esc_url($text);
          $text = str_replace('__BRACKETS_OPEN__', '{', $text);
          $text = str_replace('__BRACKETS_CLOSE__', '}', $text);
          $devOptions[$item] = stripslashes($text);
        } elseif ($item === 'include_html') {
          $text = wp_kses($text, array(
            'strong' => array(),
            'br' => array(),
            'em' => array(),
            'p' => array(),
            'div' => array('id' => array(), 'class' => array(), 'style' => array()),
            'a' => array('href' => array(), 'target' => array(), 'class' => array(), 'style' => array()),
            'img' => array('src' => array(), 'class' => array(), 'style' => array(), 'width' => array(), 'height')
          ));
          $text = balanceTags($text, true);
          $devOptions[$item] = stripslashes($text);
        } elseif (function_exists('sanitize_text_field')) {
          $devOptions[$item] = stripslashes(sanitize_text_field($text));
        } else {
          $devOptions[$item] = stripslashes($text);
        }
        if ($item === 'id') {
          $devOptions[$item] = preg_replace("/\W/", "_", $text);
          // remove trailing numbers
          $devOptions[$item] = preg_replace('/^\d+/', '', $devOptions[$item]);
        }

        // we check if we have an invalid configuration!
        if ($devOptions['shortcode_attributes'] === 'false' && $devOptions['use_shortcode_attributes_only'] === 'true') {
          $devOptions['shortcode_attributes'] = 'true';
          AdvancedIframeHelper::aiPrintError(__('You have set "Allow shortcode attributes" to "No" and "Use shortcode attributes only" to "Yes". This combination is not valid. "Allow shortcode attributes" was set to "Yes". Please check if this is what you  want. "Allow shortcode attributes" overrules "Use shortcode attributes only" if you set "Use shortcode attributes only" directly in the shortcode with use_shortcode_attributes_only="true".', "advanced-iframe"));
          $scrollposition = 0;
        }
      }
    } else {
      $securitykey = $devOptions['securitykey'];
      $it = $devOptions['install_date'];
      $pc = $devOptions['purchase_code'];
      $devOptions = advancediFrame::iframe_defaults();
      $devOptions['securitykey'] = $securitykey;
      $devOptions['install_date'] = $it;
      $devOptions['purchase_code'] = $pc;
      delete_transient('aip_discount');
      delete_transient('aip_discount_message');
      delete_transient('aip_version');
      $this->resetMetaBoxes();
    }
    if ($evanto && empty($devOptions['install_date'])) {
      $devOptions['install_date'] = time();
    }
    update_option($this->adminOptionsName, $devOptions);

    // create the external js file with the url of the wordpress installation
    $this->saveExternalJsFile();


    $updated = true;
  }

// read closed postboxes;
  $current_user = wp_get_current_user();
  $closedArray = get_user_meta($current_user->ID, 'closedpostboxes_toplevel_page_advanced-iframe', true);
  if (empty($closedArray)) {
    $closedArray = array();
  }

// needs to be set after the save again.
  if ($evanto) {
    $devOptions['demo'] = 'false';
  }
  $isDemo = $devOptions['demo'] === 'true';

  if ($evanto && clearstatscache($devOptions)) {
    AdvancedIframeHelper::aiPrintError('Yo' . 'ur ver' . 'sion of Adv' . 'anced iFr' . 'ame Pro s' . 'eems to be an ill' . 'egal co' . 'py and is now wo' . 'rking in the fr' . 'eeware m' . 'ode ag' . 'ain.<br />Ple' . 'ase get the of' . 'fical v' . 'ersion from co' . 'decanyon or co' . 'ntact the au' . 'thor thr' . 'ough code' . 'canyon if you th' . 'ink this is a fa' . 'lse al' . 'arm.');
  }
  if (clearstatscache($devOptions)) {
    $evanto = false;
  }
  ?>
  <div id="ai" class="wrap">
    <form id="ai_form" name="ai_form" method="post" action="admin.php?page=advanced-iframe">
      <div id="wpadminbar" class="wp-core-ui ai-save-bar">
        <div>
          <input id="wpbarbutton" class="button-primary" type="submit" name="update_iframe-loader"
                 value="<?php _e('Update Settings', 'advanced-iframe') ?>"/> <input id="wpresetbutton"
                                                                                    class="button-secondary confirmation"
                                                                                    name="update_iframe-loader"
                                                                                    onclick="aiResetAiSettings();"
                                                                                    type="submit"
                                                                                    value="<?php _e('Reset Settings', 'advanced-iframe') ?>"/>
          <?php
          $updated_display_text = ($updated) ? 'visible' : 'hidden';
          ?>
          <div id="ai-updated-text" style="visibility:<?php echo $updated_display_text; ?>;"><?php
            if (!isset($_POST['action']) || $_POST['action'] !== 'reset') {
              _e("Settings updated.", "advanced-iframe");
            } else {
              _e("Settings reseted.", "advanced-iframe");
            }
            ?></div>
          <input type="hidden" name="action" id="action" value="update">

        </div>
      </div>
      <input type="hidden" id="scrollposition" name="scrollposition" value="0">

      <input type="hidden" id="current_tab" name="current_tab" value="<?php echo $current_tab; ?>">

      <?php
      wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
      wp_nonce_field('twg-options', 'twg-options');
      ?>

      <div id="icon-options-general" class="icon_ai show-always">
        <br/>
      </div>
      <h1 class="show-always" class="full-width"><?php
        _e('Advanced iFrame ', 'advanced-iframe');
        if ($evanto) {
          _e('Pro', 'advanced-iframe');
        }
        echo ' <small>v' . $aiVersion . '</small>';
        if ($evanto) {
          if ($is_latest) {
            echo ' <small class="hide-print"><small><small>' . __('(Your installation is up to date - <a href="//www.advanced-iframe.com/advanced-iframe/advanced-iframe-history" target="_blank">view history</a>)', 'advanced-iframe') . '</small></small></small>';
          } elseif ($isFreemiusMigration) {
            echo ' <small class="hide-print"><small><small>' . __('(<a href="//www.advanced-iframe.com/advanced-iframe/advanced-iframe-history" target="_blank">Version ', 'advanced-iframe') . $latest_version . __('</a> is available. Please update the plugin on the "Plugins" page.', 'advanced-iframe') . '</small></small></small>';
          } else {
            echo ' <small class="hide-print"><small><small>' . __('(<a href="//www.advanced-iframe.com/advanced-iframe/advanced-iframe-history" target="_blank">Version ', 'advanced-iframe') . $latest_version . __('</a> is available. <a href="https://codecanyon.net/downloads" target="_blank">Download</a> it from CodeCanyon and follow the <a href="https://1.envato.market/WQJ3O#item-description__upgrade" target="blank">update instructions</a>!', 'advanced-iframe') . '</small></small></small>';
          }
        } else {
          echo ' <small class="hide-print"><small><small>' . __('(<a href="//www.advanced-iframe.com/advanced-iframe/advanced-iframe-history" target="_blank">view history</a>)', 'advanced-iframe') . '</small></small></small>';
        }
        ?>
      </h1>
      <?php
      if ($isDemo && !$isFreemiusMigration) { ?>
        <div class="notice notice-success">
          <p>
            <strong>
              <?php _e('The administration is running in the pro modus. The settings in blue are the ones from the <a target="_blank" href="https://1.envato.market/4nR50">pro version</a>. Everything which can be set by a shortcode is also working in the preview! Get the pro version at <a target="_blank" href="https://1.envato.market/4nR50">codecanyon</a>.', 'advanced-iframe'); ?>
            </strong>
          </p>
        </div>
        <?php
      }

      $showRegMessage = empty($devOptions['purchase_code']) && !$isFreemius;
      $viewsPercent = get_option('default_a_options') / 100;
      $showTrailMessage = $isFreemius && $ai_fs->is_trial();
      $hasTrailNotUsedMessage = $isFreemius && !$ai_fs->is_trial() && !$ai_fs->is_trial_utilized();
      $showNoLicenseMessage = $isFreemius && $ai_fs->is_premium() && $ai_fs->is_not_paying() && !$ai_fs->is_trial();
      $isRegistered = $isFreemiusMigration && $ai_fs->is_registered() && $ai_fs->is_tracking_allowed();
	  
      if ($evanto && $showRegMessage) {
        echo '<div id="show-registration-message" class="notice notice-success is-dismissible is-permanent-closable"><p><strong>';
        echo __('<p><p>Thank you for installing advanced iframe pro.</p><p>Please enter your purchase code on the <a href="#" class="enter-registration">Options tab</a>. It will unlock all the additional features you purchased.<br>Without a purchase code the 10.000 views/month of the free version is active.</p>', 'advanced-iframe');
        echo '</strong></p></p></div>';
      }
      if (!$evanto && !$isRegistered && $isFreemiusMigration) {
		echo '<div id="show-registration-message" class="notice notice-success"><p>';
        echo __('<p>Please do not forget to OPT-IN to get additional benefits. For more details, go to the <a href="#" class="enter-pro">Options tab</a>.</p>', 'advanced-iframe');
        echo '</p></div>';
	  }

      if ($showTrailMessage) {
        echo '<div id="show-registration-message" class="notice notice-success is-dismissible is-permanent-closable"><p><strong>';
        echo '<p>' . __('The trial version is active. All pro features are enabled.', 'advanced-iframe') . '</p>';
        echo '</strong></p></div>';
      }

      if ($showNoLicenseMessage) {
        $accountUrl = $ai_fs->get_account_url();
        $trialUrl = $ai_fs->get_trial_url();
        echo '<div id="show-registration-message" class="notice notice-success is-dismissible is-permanent-closable"><p><strong>';
        echo __('<p><p>You are currently using the pro version without a license. The free version is enabled now. Please enter a license key on the ', 'advanced-iframe');
		echo '<a href="' . $accountUrl . '">';
		echo __('Account page -> Activate License</a> to enable the pro version ', 'advanced-iframe');
        if ($hasTrailNotUsedMessage) {
          echo __('or enable your <a href="', 'advanced-iframe'); 
          echo $trialUrl; 
		  echo __('">30 days trial</a> to test all features.', 'advanced-iframe');
        }
        echo ".</p>";
        echo '</strong></p></p></div>';
      }

      $showProMessage = !(isset($devOptions['closed_messages']) && isset($devOptions['closed_messages']['test-pro-admin']));
      if (!$evanto && !$isDemo && $showProMessage) {
        echo '<div id="test-pro-admin" class="notice notice-success is-dismissible is-permanent-closable"><p><strong>';
        if ($isFreemiusMigration) {
          $pricingUrl = get_admin_url() . 'admin.php?page=advanced-iframe-pricing&trial=true';
          echo __('Curious about the pro features? <a href="', 'advanced-iframe');
          echo $pricingUrl;
          echo __('">Start your 30-day free trial</a>.', 'advanced-iframe');
        } else {
          echo __('Curious about the pro features? Enable them on the <a href="#" class="enable-admin">options tab</a> and test them in the preview.', 'advanced-iframe');
        }
        echo '</strong></p></div>';
      }

      aiPostboxOpen("id-search", "Read this first", $closedArray, '48%', " show-always postbox-container-space");
      echo __('If you start using advanced iframe please read the "<a href="//www.advanced-iframe.com/advanced-iframe/advanced-iframe-checklist" target="_blank">Advanced iframe checklist</a>" first. Then continue with the quickstart guide on the options tab. After that continue with an iframe like described on the basic tab. Only if the iframe appears add additional features. Go to the <a href="//www.advanced-iframe.com/advanced-iframe/demo-advanced-iframe-2-0" target="_blank">free</a> and the <a href="//www.advanced-iframe.com/advanced-iframe/advanced-iframe-pro-demo" target="_blank">pro demos</a> page for running examples.', 'advanced-iframe');

      echo '<p>';
      echo renderExampleIcon("javascript:void();");
      echo __(' links to a working example of the feature. ', 'advanced-iframe');
      echo '<br>';
      echo renderExternalWorkaroundIcon(true);
      echo __(' shows that this setting is rendered in the ai_external.js. See the "<a id="external-workaround-link" href="#xss">External workaround</a>" tab for details.', 'advanced-iframe');
      echo '</p>';
      aiPostboxClose();


      aiPostboxOpen("id-search", "Search", $closedArray, '48%', " show-always");
      _e('<input type="search" class="ai-input-search" placeholder="Search for settings" />
	<div id="ai-input-search-result">
	No settings found.
	</div><div id="ai-input-search-result-show">&nbsp;</div>
	<div style="clear:left;"></div>
	<div id="ai-input-search-help">
	The search does look for the search term in the label and the description of each setting on all tabs. Tabs with findings are marked yellow. Please use the browser search to search in the additional documentation that does exist in each section.
	</div>', 'advanced-iframe');
      aiPostboxClose();

      echo '<div class="clear"></div>';


      _e('<h2 class="nav-tab-wrapper show-always">', 'advanced-iframe');
      if ($devOptions['donation_bottom'] === 'false') {
        _e('<a id="tab_0" class="nav-tab options-tab nav-tab-active" href="#introduction"><span>Options</span></a>
      <a id="tab_1" class="nav-tab" href="#basic"><span>Basic Settings</span></a>
      <a id="tab_2" class="nav-tab advanced-settings-tab" href="#advanced"><span>Advanced Settings</span></a>
      <a id="tab_3" class="nav-tab external-workaround" href="#external-workaround"><span>External workaround</span></a>
      <a id="tab_4" class="nav-tab" href="#add-files"><span>Add/Include files</span></a>
      <a id="tab_5" class="nav-tab help-tab" href="#help"><span>Help / FAQ</span></a>', 'advanced-iframe');
      } else {
        _e('<a id="tab_1" class="nav-tab nav-tab-active" href="#basic"><span>Basic Settings</span></a>
    <a id="tab_2" class="nav-tab advanced-settings-tab" href="#advanced"><span>Advanced Settings</span></a>
    <a id="tab_3" class="nav-tab external-workaround" href="#external-workaround"><span>External workaround</span></a>
    <a id="tab_4" class="nav-tab" href="#add-files"><span>Add/Include files</span></a>
    <a id="tab_5" class="nav-tab help-tab" href="#help"><span>Help / FAQ</span></a>
    <a id="tab_0" class="nav-tab options-tab" href="#introduction"><span>Options</span></a>', 'advanced-iframe');
      }
      _e('
</h2>
', 'advanced-iframe');
      ?>

      <div style="clear:both;"></div>
      <div id="tab_wrapper">
        <?php

        if ($devOptions['donation_bottom'] === 'false') {
          echo '<section id="section-quickstart" class="tab_0">';
          printDonation($devOptions, $evanto, $closedArray);
          echo "</div>";
          echo '</section>';
        }

        echo '<section id="section-default" class="tab_1">';
        include_once dirname(__FILE__) . '/includes/advanced-iframe-admin-default.php';
        echo '</section><section id="section-advanced" class="tab_2">';
        include_once dirname(__FILE__) . '/includes/advanced-iframe-admin-advanced.php';
        include_once dirname(__FILE__) . '/includes/advanced-iframe-admin-resize.php';
        include_once dirname(__FILE__) . '/includes/advanced-iframe-admin-modify-iframe.php';
        include_once dirname(__FILE__) . '/includes/advanced-iframe-admin-modify-parent.php';
        include_once dirname(__FILE__) . '/includes/advanced-iframe-admin-zoom.php';
        include_once dirname(__FILE__) . '/includes/advanced-iframe-admin-lazy-load.php';
        include_once dirname(__FILE__) . '/includes/advanced-iframe-admin-parameters.php';
        echo '</div>';
        echo '</section><section id="section-external-workaround" class="tab_3">';
        include_once dirname(__FILE__) . '/includes/advanced-iframe-admin-external-workaround.php';
        echo '</section><section id="section-add-files" class="tab_4">';
        include_once dirname(__FILE__) . '/includes/advanced-iframe-admin-add-files.php';
        include_once dirname(__FILE__) . '/includes/advanced-iframe-admin-include-directly.php';
        echo '</section><section  id="section-help" class="tab_5">';
        include_once dirname(__FILE__) . '/includes/advanced-iframe-admin-video.php';
        include_once dirname(__FILE__) . '/includes/advanced-iframe-admin-faq.php';
        include_once dirname(__FILE__) . '/includes/advanced-iframe-admin-forum.php';
        include_once dirname(__FILE__) . '/includes/advanced-iframe-admin-support.php';
        include_once dirname(__FILE__) . '/includes/advanced-iframe-admin-find-id.php';
        include_once dirname(__FILE__) . '/includes/advanced-iframe-admin-jquery.php';
        include_once dirname(__FILE__) . '/includes/advanced-iframe-admin-browser.php';
        include_once dirname(__FILE__) . '/includes/advanced-iframe-admin-help-post.php';
        include_once dirname(__FILE__) . '/includes/advanced-iframe-admin-twg.php';
        echo '</section>';
        if ($devOptions['donation_bottom'] === 'true') {
          echo '<section id="section-quickstart" class="tab_0">';
          printDonation($devOptions, $evanto, $closedArray);
          echo "</div>";
          echo '</section>';
        }
        ?>
      </div>

    </form>
  </div>
  <script type="text/javascript">
    jQuery(function () {
      if (typeof aiInitAdminConfiguration == 'function') {
        aiInitAdminConfiguration(<?php echo ($evanto) ? "true" : "false"; ?>, 'false');
      } else {
        setTimeout(function () {
          aiInitAdminConfiguration(<?php echo ($evanto) ? "true" : "false"; ?>,<?php echo '"' . $devOptions['accordeon_menu'] . '"'; ?>);
        }, 100);
      }
      document.getElementById('tab_<?php echo $current_tab; ?>').click();
      setTimeout(function () {
        jQuery(document).scrollTop(<?php echo $scrollposition; ?>);
        aiAccTime = 400;
      }, 100);
    });
  </script>
  <?php
  $devOptions['admin_was_loaded'] = true;
  update_option($this->adminOptionsName, $devOptions);
}
?>