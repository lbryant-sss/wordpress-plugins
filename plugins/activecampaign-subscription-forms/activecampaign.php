<?php
/*
Plugin Name: ActiveCampaign
Plugin URI: http://www.activecampaign.com/apps/wordpress
Description: Allows you to add ActiveCampaign contact forms to any post, page, or sidebar. Also allows you to embed <a href="http://www.activecampaign.com/help/site-event-tracking/" target="_blank">ActiveCampaign site tracking</a> code in your pages. To get started, please activate the plugin and add your <a href="http://www.activecampaign.com/help/using-the-api/" target="_blank">API credentials</a> in the <a href="options-general.php?page=activecampaign">plugin settings</a>.
Author: ActiveCampaign
Version: 8.1.18
Author URI: http://www.activecampaign.com
*/

# Changelog
## version 1: - initial release
## version 1.1: Verified this works with latest versions of WordPress and ActiveCampaign; Updated installation instructions
## version 2.0: Re-configured to work with ActiveCampaign version 5.4. Also improved some areas.
## version 2.1: Changed internal API requests to use only API URL and Key instead of Username and Password. Also provided option to remove style blocks, and converting `input type="button"` into `input type="submit"`
## version 3.0: Re-wrote widget backend to use most recent WordPress Widget structure. Improvements include streamlined code and API usage, ability to reset or refresh your forms, and better form width detection.
## version 3.5: You can now use a shortcode to display your subscription form.
## version 4.0: Added many additional settings to control how your form is displayed and submitted.
## version 4.5: Added ActiveCampaign to the Settings menu so you can use the shortcode independent of the widget.
## version 5.0: Added support for multiple forms. Removed widget entirely.
## version 5.1: Added button to TinyMCE toolbar to more easily choose and embed the form shortcode into the post body.
## version 5.2: Default form behavior is now "sync." This coincided with WordPress version 3.9 release.
## version 5.5: Added site tracking.
## version 5.6: Patched major security bug.
## version 5.7: Removed ability to add custom form "action" URL.
## version 5.8: Security fix.
## version 5.9: Use current user's email for site tracking.
## version 5.91: Updates to avoid conflicts with other plugins using the ActiveCampaign PHP API wrapper.
## version 5.92: Support for captcha validation when using the 'Submit form without refreshing page' (Ajax) option. Also added success or error CSS classes to the Ajax response div.
## version 5.93: Fix for issue with captcha verification when using the Ajax ("Submit form without refreshing page") form submission option.
## version 6.0: Added support for new form builder.
## version 6.1: Fix for issue with new forms not displaying properly.
## version 6.2: Fix for compatability issue with live composer plugin.
## version 6.25: Fix for SSL issue (when the page is loaded via HTTPS and the AC account uses a CNAME, forms would not show up).
## version 6.2.6: Fix for certain error messages not being displayed properly.
## version 6.2.7: Fix for 6.2.6 change missing another check.
## version 6.2.8: Fix for `Undefined index: css` error.
## version 6.2.9: Fix for "Keep original form CSS" checkbox not being respected.
## version 6.2.10: Limit amount of ActiveCampaign account data shown in JavaScript (for site tracking).
## version 6.2.11: Fix for when the "site_tracking" key is undefined.
## version 6.2.12: Fix for when the "form_id" key is undefined.
## version 6.3: Added site tracking options for GDPR
## version 7.0: Force upgrade prompt for users on 6.25
## version 7.1: Update Site Tracking snippet to route through Prism.
## version 7.1.1: Include our own host header on requests.
## version 7.1.2: Update tracking code copy to be more specific to Site Tracking and Conversations. Fix old link to Forms page. Make install code toggle focusable.
## version 7.1.3: Updated readme
## version 7.1.4: Updated listing
## version 8.0.0: Update ActiveCampaign forms embed to be compatible with Gutenberg editor, Resolve account connection UI bug
## version 8.0.1: Removing php 7 feature usage
## version 8.0.2: Security fix to address CSRF vulnerability, general fix to address browser warning for invalid cookie attribute
## version 8.0.3: Pluggable bug fix
## version 8.1.0: Improvements to Gutenberg Editor experience, including live preview of Form embeds. Background color bug fix. Shortcode support for optional 'css' and 'static' attributes that default to plugin settings. Avoiding global namespace conflicts of on-demand chunks bug fix.
## version 8.1.1: Improved error handling on expired credentials and misconfigurations. Shortening Block widget name to 'AC Forms'.
## version 8.1.2: Simplifying plugin settings options. Dropping 'Global' CSS option for block, defaulting to 'Use ActiveCampaign CSS'. Converting to Dynamic Block pattern.
## version 8.1.3: Hotfix for Default CSS option deprecation. Moving from global assignment to block/shortcode assignment. Allowing fallback for existing blocks without CSS setting.
## version 8.1.4: Rolling back settings page form/css deprecations. We have improved testing workflows moving forward.
## version 8.1.5: Updating Readme with up to date screenshots and better descriptions. Updating Plugin Settings with clearer descriptions of form and shortcode use cases. Fixing block editor CSS class input on dynamic div output. Fixing display of Site Tracking settings without forms. Migrating Site Tracking JS to vgo() from pgo(). Fixing bug with Tracking ID fetch. Adding admin notice stack for future plugin updates.
## version 8.1.6: Improving credential check to fix permissions bug. Fixing non-inline form previews in block editor. Removing unnecessary Google Font loads on no-style embeds. Updating Plugin description.
## version 8.1.7: Updated listing
## version 8.1.8: Updated listing
## version 8.1.9: Updated authentication for internal API requests
## version 8.1.10: Verify 6.0 Compatibility. Updated listing
## version 8.1.11: Removing obsolete Javascript
## version 8.1.12: Security fix to address XSS vulnerability
## version 8.1.13: Verify 6.3.1 Compatibility. Updated listing
## version 8.1.14: Fixing shortcode CSS display in Form Preview
## version 8.1.15: Security fix to address SSRF vulnerability with API URL verification and wp_safe_remote_get. Removing unreachable deprecated curl code
## version 8.1.16: Verify 6.5 Compatibility. Updated listing
## version 8.1.17: Security fix to address XSS vulnerability with API URL and API Key verification
## version 8.1.18: Update readme.txt, address supported version of WordPress

define("ACTIVECAMPAIGN_URL", "");
define("ACTIVECAMPAIGN_API_KEY", "");
require_once(dirname(__FILE__) . "/activecampaign-api-php/ActiveCampaign.class.php");
require_once(dirname(__FILE__) . "/activecampaign-form-block/activecampaign-form-block.php");

/**
 * Get the source code for the form itself.
 * In the past we just returned the form HTML code (CSS + HTML), but the new version of forms just uses the JavaScript stuff (HTML JavaScript include).
 *
 * @param  array   settings  The saved ActiveCampaign settings (from the WordPress admin section).
 * @param  array   form      The individual form metadata (that we obtained from the forms/getforms API call).
 * @param  boolean static    Set to true so the "floating" forms don't float. Typically this is done for the admin section only.
 * @param  boolean nostyles  Manual override value for nostyles URL Param. Used in shortcode eval to bypass plugin setting.
 * @param  boolean preview   Manual value for preview URL Param.
 * @return string            The raw source code that will render the form in the browser.
 */
function activecampaign_form_source($settings, $form, $static = false, $nostyles = null, $preview = false)
{
    $script_src = activecampaign_form_script_src($settings, $form, $static, $nostyles, $preview);
    $source = "";
    if (isset($form["version"]) && $form["version"] == 2 && isset($script_src)) {
        if ($form["layout"] == "inline-form") {
            $source .= "<div class='_form_" . $form["id"] . "'></div>";
        }
        $source .= "<script type='text/javascript' src='".$script_src."'></script>";
    } else {
        // Version 1 forms.
    }
    return $source;
}
/**
 * Get the script src attribute value for the form's embed.
 * This is used in shortcode rendering as well as block editor initialization.
 *
 * @param  array   settings  The saved ActiveCampaign settings (from the WordPress admin section).
 * @param  array   form      The individual form metadata (that we obtained from the forms/getforms API call).
 * @param  boolean static    Set to true so the "floating" forms don't float. Typically this is done for the admin section only.
 * @param  boolean nostyles  Manual override value for nostyles URL Param. Used in shortcode eval to bypass plugin setting.
 * @param  boolean preview   Manual value for preview URL Param.
 * @return string            The URL-Encoded src for the rendered script tag
 */
function activecampaign_form_script_src($settings, $form, $static = false, $nostyles = null, $preview = false)
{
    // Set to activehosted.com domain by default.
	$domain = (isset($settings["account_view"]) && isset($settings["account_view"]["account"]))? $settings["account_view"]["account"] : null;
	if(!isset($domain)){
		return null;
	}

    $source = sprintf("https://%s/f/embed.php?", $domain);

	$css = (isset($settings["css"]) && isset($settings["css"][$form["id"]]))? $settings["css"][$form["id"]] : null;

    // Always passing params for JS eval in block editor
    $source .= ($static)? "static=1&" : "static=0&";

    $source .= sprintf("id=%d&%s", $form["id"], strtoupper(uniqid()));

	$source .= (
		(isset($nostyles) && $nostyles === true)
		|| (!isset($nostyles) && (!isset($css) || !$css))
	)? "&nostyles=1" : "&nostyles=0";

    $source .= ($preview)? "&preview=1" : "&preview=0";

    return $source;
}

function activecampaign_shortcodes($args)
{
    // check for Settings options saved first.
    $settings = get_option("settings_activecampaign");
    if ($settings) {
        if (isset($settings["forms"]) && $settings["forms"]) {
            if (isset($args) && isset($args["form"]) && isset($settings["forms"][$args["form"]])) {
                $form_id = $args["form"];
                $form = $settings["forms"][$form_id];

                $static = false;
                if (isset($args["static"]) && ($args["static"] === 1 || $args["static"] === '1' || $args["static"] === 'true')) {
                    $static = true;
                }

                $preview = false;
                if (isset($args["preview"]) && ($args["preview"] === 1 || $args["preview"] === '1' || $args["preview"] === 'true')) {
                    $preview = true;
                }

                // Use null default for undefined settings fallback
				$nostyles = null;
				if (isset($args["css"])) {
					if ($args["css"] === 1 || $args["css"] === '1' || $args["css"] === 'true') {
						$nostyles = false;
					} elseif ($args["css"] === 0 || $args["css"] === '0' || $args["css"] === 'false') {
						$nostyles = true;
					}
                }

                return activecampaign_form_source($settings, $form, $static, $nostyles, $preview);
            }
        }
        // Returning un-filtered tag for block editor eval
        return "[activecampaign]";
    } else {
        // try widget options.
        $widget = get_option("widget_activecampaign_widget");
        // it comes out as an array with other things in it, so loop through it
        if(!empty($widget)){
			foreach ($widget as $k => $v) {
				// look for the one that appears to be the ActiveCampaign widget settings
				if (isset($v["api_url"]) && isset($v["api_key"]) && isset($v["form_html"])) {
					$widget_display = $v["form_html"];
					return $widget_display;
				}
			}
        }
    }
    return "";
}

function activecampaign_verify_api_host($api_host)
{
    return (bool) preg_match('/^https:\/\/([a-zA-Z0-9_\-]+)\.(api\-us1|activehosted)\.com$/', $api_host);
}

/*
 * The ActiveCampaign settings page.
 */
function activecampaign_plugin_options()
{

    if (!current_user_can("manage_options")) {
        wp_die(__("You do not have sufficient permissions to access this page."));
    }

    $step = 1;
    $instance = array();
    $connected = false;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // saving the settings page.

        $sanitizedApiUrl = sanitize_text_field($_POST["api_url"]);
        $sanitizedApiKey = sanitize_text_field($_POST["api_key"]);

        if ($sanitizedApiUrl && $sanitizedApiKey) {
            //Nonce check for preventing CSRF
            if (isset($_REQUEST["_wpnonce"])) {
                $nonce = $_REQUEST["_wpnonce"];
            } else {
                $nonce = wp_create_nonce("invalid_nonce");
            }
            if (! wp_verify_nonce($nonce, "activecampaign_save_settings")) {
                exit;
            }

            // Verify host against whitelist of known domains
            if(!activecampaign_verify_api_host($sanitizedApiUrl)){
                $access_denied_string = "Access denied: Invalid API URL " . $sanitizedApiUrl .  ". Please ensure the API URL uses https, and has no extra whitespace.";
                echo "<p style='margin: 0 0 20px; padding: 14px; font-size: 14px; color: #873c3c; font-family:arial; background: #ec9999; line-height: 19px; border-radius: 5px; overflow: hidden;'>" . __($access_denied_string, "menu-activecampaign") . "</p>";
            }
            else{
                $ac = new ActiveCampaignWordPress($sanitizedApiUrl, $sanitizedApiKey);

                if (!(int)$ac->credentials_test()) {
                    echo "<p style='margin: 0 0 20px; padding: 14px; font-size: 14px; color: #873c3c; font-family:arial; background: #ec9999; line-height: 19px; border-radius: 5px; overflow: hidden;'>" . __("Access denied: Invalid credentials (URL and/or API key). \"{$sanitizedApiUrl}\"", "menu-activecampaign") . "</p>";
                } else {
                    $instance = $_POST;

                    // first form submit (after entering API credentials).

                    // get account details.
                    $account = $ac->api("account/view");
                    $instance["account_view"] = get_object_vars($account);
                    $instance["account"] = property_exists($account, 'account')? $account->account : null;
                    $instance["tracking_actid"] = activecampaign_fetch_accountid($ac);

                    // get forms.
                    $instance = activecampaign_getforms($ac, $instance);
                    $instance = activecampaign_form_html($ac, $instance);

                    $connected = true;

                    // If fetches didn't return domain or account, we can't display embeds, so fail out. This can happen with non-admin API credentials
                    $domain = (isset($instance["account_view"]) && isset($instance["account_view"]["account"]))? $instance["account_view"]["account"] : null;
                    if(!isset($instance["account"]) || !isset($domain)){
                        $connected = false;
                        $instance = [];
                        echo "<p style='margin: 0 0 20px; padding: 14px; font-size: 14px; color: #873c3c; font-family:arial; background: #ec9999; line-height: 19px; border-radius: 5px; overflow: hidden;'>" . __("Access denied: You entered valid credentials, but the associated API User is not an ActiveCampaign Admin group member. Please use API credentials from a User within the Admin group.", "menu-activecampaign") . "</p>";
                    }
                }
            }
        }
        // else one or both of the credentials fields is empty. it will just disconnect below because $instance is empty.

        update_option("settings_activecampaign", $instance);
    } else {
        $instance = get_option("settings_activecampaign");
//dbg($instance);

        if (
            isset($instance["api_url"], $instance["api_key"])
            && $instance["api_url"] && $instance["api_key"]
            && activecampaign_verify_api_host($instance["api_url"])
        ) {
            // instance saved already.
            $connected = true;
        } else {
            // settings not saved yet.

            // see if they set up our widget (maybe we can pull the API URL and Key from that).
            $widget = get_option("widget_activecampaign_widget");

            if ($widget) {
                // if the ActiveCampaign widget is activated in a sidebar (dragged to a sidebar).

                $widget_info = current($widget); // take the first item.

                if (
                    isset($widget_info["api_url"], $widget_info["api_key"])
                    && $widget_info["api_url"] && $widget_info["api_key"]
                    && activecampaign_verify_api_host($widget_info["api_url"])
                ) {
                    // if they already supplied an API URL and key in the widget.
                    $instance["api_url"] = $widget_info["api_url"];
                    $instance["api_key"] = $widget_info["api_key"];
                }
            }
        }
    }

    ?>

    <div class="wrap">

        <div id="icon-options-general" class="icon32"><br></div>

        <h2><?php echo __("ActiveCampaign Settings", "menu-activecampaign"); ?></h2>

        <p style='font-family: Arial, Helvetica, sans-serif; font-size: 13px; line-height: 1.5;'>
            <?php

                echo __("Configure your ActiveCampaign subscription form to be used as a shortcode anywhere on your site. Use <code>[activecampaign form=ID]</code> shortcode in posts, pages, or a sidebar after setting up everything below. Questions or problems? Contact help@activecampaign.com.", "menu-activecampaign");

            ?>
        </p>

        <form name="activecampaign_settings_form" method="post" action="" style='font-family: Arial, Helvetica, sans-serif; font-size: 13px; line-height: 1.5;'>

            <hr style="border: 1px dotted #ccc; border-width: 1px 0 0 0; margin-top: 30px;" />

            <h3><?php echo __("API Credentials", "menu-activecampaign"); ?></h3>

            <p>
                <b><?php echo __("API URL", "menu-activecampaign"); ?>:</b>
                <br />
                <input type="text" name="api_url" id="activecampaign_api_url" value="<?php echo esc_attr(isset($instance["api_url"]) ? $instance["api_url"] : ""); ?>" style="width: 400px;" />
            </p>

            <p>
                <b><?php echo __("API Key", "menu-activecampaign"); ?>:</b>
                <br />
                <input type="text" name="api_key" id="activecampaign_api_key" value="<?php echo esc_attr(isset($instance["api_key"]) ? $instance["api_key"] : ""); ?>" style="width: 500px;" />
            </p>

            <?php
                $button_value = ($connected) ? "Update Settings" : "Connect";

            if ($button_value == "Update Settings") {
                // Only show this additional form submit button if they are already connected.
                ?>
                    <p><button type="submit" style="font-size: 16px; margin-top: 25px; padding: 10px;"><?php echo __($button_value, "menu-activecampaign"); ?></button></p>
                    <?php
            }

            if (!$connected) {
                ?>

                    <p style='font-family: Arial, Helvetica, sans-serif; font-size: 13px; line-height: 1.5;'><?php echo __("Get your API credentials from the Settings > Developer section:", "menu-activecampaign"); ?></p>

                    <p><img src="<?php echo plugins_url("activecampaign-subscription-forms"); ?>/settings1.png" /></p>

                    <?php
            } else { // is connected
                ?>


            <hr style="border: 1px dotted #ccc; border-width: 1px 0 0 0; margin-top: 30px;" />

            <h3><?php echo __("Subscription Forms", "menu-activecampaign"); ?></h3>
            <p style='font-family: Arial, Helvetica, sans-serif; font-size: 13px; line-height: 1.5;'><?php echo __("Below is a list of your available ActiveCampaign forms. To add new forms go to <a href=\"http://" . $instance["account"] . "/app/forms\" target=\"_blank\" style='color: #23538C !important;'>ActiveCampaign > Forms</a>. <br><br>Users of the Gutenberg Block Editor will find all forms and CSS options are available within the AC Forms block. <br><br>  Shortcodes can be used anywhere regardless of widgets in the following format: (where css=1 uses ActiveCampaign's suggested CSS and css=0 does not)<br><code>[activecampaign form=ID css=1]</code> or <code>[activecampaign form=ID css=0]</code> <br><br>Users of the Classic Editor experience can check the checkbox next to forms they would like to enable in the classic widget, and also manage their form's global CSS setting below.", "menu-activecampaign"); ?></p>

            <?php

            if (isset($instance["forms"]) && $instance["forms"]) {


                    // just a flag to know if ANY form is checked (chosen)
                    $form_checked = 0;


                    foreach ($instance["forms"] as $form) {
                        // $instance["form_id"] is an array of form ID's (since we allow multiple now).

                        $checked = "";
                        $options_visibility = "none";
                        if (isset($instance["form_id"]) && $instance["form_id"] && in_array($form["id"], $instance["form_id"])) {
                            $checked = "checked=\"checked\"";
                            $form_checked = 1;
                            $options_visibility = "block";
                        }

                        $settings_swim_checked = (isset($instance["syim"][$form["id"]]) && $instance["syim"][$form["id"]] == "swim") ? "checked=\"checked\"" : "";
                        $settings_sync_checked = (isset($instance["syim"][$form["id"]]) && $instance["syim"][$form["id"]] == "sync") ? "checked=\"checked\"" : "";
                        if (!$settings_swim_checked && !$settings_sync_checked) {
                            $settings_swim_checked = "checked=\"checked\""; // default
                        }
                        $settings_ajax_checked = (isset($instance["ajax"][$form["id"]]) && (int)$instance["ajax"][$form["id"]]) ? "checked=\"checked\"" : "";

                        $settings_css_checked = "";
                        if ((isset($instance["css"][$form["id"]]) && (int)$instance["css"][$form["id"]]) || !$form_checked) {
                            // either it's been checked before, OR
                            // no form is chosen yet, so it's likely coming from step 1, so default the CSS checkbox to checked.
                            $settings_css_checked = "checked=\"checked\"";
                        }

                        $settings_action_value = (isset($instance["action"][$form["id"]]) && $instance["action"][$form["id"]]) ? $instance["action"][$form["id"]] : "";

                        ?>

                        <hr style="border: 1px dotted #ccc; border-width: 1px 0 0 0; margin: 30px 0 20px 0;" />

                        <input type="checkbox" name="form_id[]" id="activecampaign_form_<?php echo $form["id"]; ?>" value="<?php echo $form["id"]; ?>" onclick="toggle_form_options(this.value, this.checked);" <?php echo $checked; ?> />
                <label for="activecampaign_form_<?php echo $form["id"]; ?>"><?php echo $form["name"]; ?></label> (ID: <?php echo $form["id"]; ?>) - <a href="http://<?php echo $instance["account"]; ?>/app/forms/<?php echo $form["id"]; ?>" target="_blank">Edit in ActiveCampaign</a> - <a href="javascript:ac_copy_shortcode(<?php echo $form["id"]; ?>)">Copy Shortcode</a> <span style="opacity:0;" class="copied-alert" id="copied_alert_<?php echo $form["id"]; ?>">Copied!</span>
                        <br />

                        <div id="form_options_<?php echo $form["id"]; ?>" style="display: <?php echo $options_visibility; ?>; margin-left: 30px;">
                            <h4><?php echo __("Form Options", "menu-activecampaign"); ?></h4>
                            <p><i><?php echo __("Leave as default for normal behavior, or customize based on your needs.", "menu-activecampaign"); ?></i></p>
                            <div style="display: none;">
                                <input type="radio" name="syim[<?php echo $form["id"]; ?>]" id="activecampaign_form_swim_<?php echo $form["id"]; ?>" value="swim" <?php echo $settings_swim_checked; ?> onchange="swim_toggle(<?php echo $form["id"]; ?>, this.checked);" />
                                <label for="activecampaign_form_swim_<?php echo $form["id"]; ?>" style=""><?php echo __("Add Subscriber", "menu-activecampaign"); ?></label>
                                <br />
                                <input type="radio" name="syim[<?php echo $form["id"]; ?>]" id="activecampaign_form_sync_<?php echo $form["id"]; ?>" value="sync" <?php echo $settings_sync_checked; ?> onchange="sync_toggle(<?php echo $form["id"]; ?>, this.checked);" />
                                <label for="activecampaign_form_sync_<?php echo $form["id"]; ?>" style=""><?php echo __("Sync Subscriber", "menu-activecampaign"); ?></label>
                                <br />
                                <br />
                            </div>
                        <?php if (!isset($form["version"]) || $form["version"] != 2) : ?>
                            <input type="checkbox" name="ajax[<?php echo $form["id"]; ?>]" id="activecampaign_form_ajax_<?php echo $form["id"]; ?>" value="1" <?php echo $settings_ajax_checked; ?> onchange="ajax_toggle(<?php echo $form["id"]; ?>, this.checked);" />
                            <label for="activecampaign_form_ajax_<?php echo $form["id"]; ?>" style=""><?php echo __("Submit form without refreshing page", "menu-activecampaign"); ?></label>
                            <br />
                        <?php endif; ?>
                            <input type="checkbox" name="css[<?php echo $form["id"]; ?>]" id="activecampaign_form_css_<?php echo $form["id"]; ?>" value="1" <?php echo $settings_css_checked; ?> />
                            <label for="activecampaign_form_css_<?php echo $form["id"]; ?>" style=""><?php echo __("Use ActiveCampaign's form CSS", "menu-activecampaign"); ?></label>
                        </div>

                    <?php
                        } // End form foreach

                    } else{ // End form if
                        echo '<hr style="border: 1px dotted #ccc; border-width: 1px 0 0 0; margin-top: 30px;" />';
                        echo '<h4>'.__("No forms were found", "menu-activecampaign").'</h4>';
                    }

                    // "Enable Site Tracking" toggle
                    $settings_st_enabled = isset($instance["site_tracking"]) && (int)$instance["site_tracking"];
                    $settings_st_checked = $settings_st_enabled ? "checked=\"checked\"" : "";

                    // Site Tracking default option
                    /* Default to "Track by default" option if any of these are true:
                    1. It's already been chosen and saved
                    2. Site tracking is just being enabled for the first time
                    3. Site tracking was already enabled but the new default options are not set yet
                    */
                    $settings_st_default_on = (
                        (
                            isset($instance["activecampaign_site_tracking_default"]) &&
                            (int)$instance["activecampaign_site_tracking_default"]
                        )
                        ||
                        ! isset($instance["site_tracking"])
                        ||
                        ! isset($instance["activecampaign_site_tracking_default"])
                    );
                    $settings_st_default_on_checked = $settings_st_default_on ? "checked=\"checked\"" : "";
                    $settings_st_default_off_checked = ! $settings_st_default_on_checked ? "checked=\"checked\"" : "";

                    ?>

                    <hr style="border: 1px dotted #ccc; border-width: 1px 0 0 0; margin: 30px 0 20px 0;" />

                    <div class="activecampaign_site_tracking">

                        <h3><?php echo __("Install Code", "menu-activecampaign"); ?></h3>
                        <p>
                        <?php echo __("Installing this code snippet allows you to enable Site Tracking and the Conversations chat widget through your ActiveCampaign account. You can control on which pages these will be loaded on the  <a href=\"http://" . $instance["account"] . "/app/settings/tracking\" target=\"_blank\" style='color: #23538C !important;'>Settings > Tracking page</a> in your ActiveCampaign account.", "menu-activecampaign"); ?>
                        </p>

                        <label>
                            <input type="hidden" name="site_tracking" value="<?php echo (int)$settings_st_enabled; ?>" />
                            <input type="checkbox" id="activecampaign_site_tracking" <?php echo $settings_st_checked; ?> onchange="site_tracking_toggle(this.checked);">
                            <span class="slider round"></span>
                        </label>
                        <label for="activecampaign_site_tracking" style=""><?php echo __("Install ActiveCampaign code", "menu-activecampaign"); ?></label>

                        <h4><?php echo __("Site Tracking", "menu-activecampaign"); ?></h4>
                        <p>
                        <?php echo __("Site tracking enables you to record visitor history on your site to use for targeted segmenting. Tracking includes page visits and IP addresses for all known contacts. Note: This is considered personal data.", "menu-activecampaign"); ?>
                            <a href="https://help.activecampaign.com/hc/en-us/articles/221542267-An-overview-of-Site-Tracking" target="_blank"><?php echo __("Learn more about site tracking"); ?></a>.
                        </p>

                        <?php if(empty($instance['tracking_actid'])){ ?>
                            <div class="notice notice-info is-dismissible"><p><?php echo __("ActiveCampaign Site Tracking Account ID could not be found. Site tracking will not work without this. Please contact support.", "menu-activecampaign"); ?></p></div>
                        <?php } ?>

                        <div id="activecampaign_site_tracking_options" class="<?php echo (! $settings_st_enabled) ? 'disabled' : ''; ?>">

                            <input type="radio" id="activecampaign_site_tracking_default_on" name="activecampaign_site_tracking_default" value="1" <?php echo $settings_st_default_on_checked; ?> />
                            <label for="activecampaign_site_tracking_default_on"><?php echo __("Track by default", "menu-activecampaign"); ?></label>
                            <p><?php echo __("This option will track all known contacts by default, and will not provide an additional tracking consent notice to your contacts.", "menu-activecampaign"); ?></p>

                            <input type="radio" id="activecampaign_site_tracking_default_off" name="activecampaign_site_tracking_default" value="0" <?php echo $settings_st_default_off_checked; ?> />
                            <label for="activecampaign_site_tracking_default_off"><?php echo __("Do not track by default", "menu-activecampaign"); ?></label>
                            <p>
                            <?php echo __("This option will not track all known contacts by default. Your contacts will only be tracked after they confirm tracking consent. You must develop a tracking consent notice, and connect it to this plugin, to use this option. Learn more about", "menu-activecampaign"); ?>
                                <a href="https://help.activecampaign.com/hc/en-us/articles/360000872064-Site-tracking-and-the-GDPR" target="_blank"><?php echo __("Site tracking and the GDPR", "menu-activecampaign") ?></a>.
                            </p>

                        </div>

                        <h4><?php echo __("Conversations", "menu-activecampaign"); ?></h4>
                        <p>
                        <?php echo __("Capture more leads and provide highly personalized support all while keeping your customer data in ActiveCampaign. Conversations enables you to engage with your customers through live chat and email and allows you to send, receive and manage messages through a unified inbox. You can also connect your Conversations to automations, deals and more.", "menu-activecampaign"); ?>
                            <a href="https://help.activecampaign.com/hc/en-us/articles/360003700720-Conversations-Overview" target="_blank"><?php echo __("Learn more about Conversations"); ?></a>.
                        </p>

                    </div>

                        <?php
                    } // End $connected if

                    ?>

                    <script type='text/javascript'>

                        function ac_copy_shortcode(form_id){
                            var input = document.createElement('input');
                            var cssEl = document.getElementById('activecampaign_form_css_'+form_id);

                            input.value = '[activecampaign form='+form_id+' css='+((cssEl && cssEl.checked)? '1':'0')+']';
                            document.body.appendChild(input);
                            input.select();
                            input.setSelectionRange(0,100);
                            document.execCommand('copy');
                            document.body.removeChild(input);
                            var alert = document.getElementById('copied_alert_'+form_id);
                            alert.style.opacity = '1.0';
                            setTimeout(function(){ alert.style.opacity = '0'; }, 1000);
                        }

                        // shows or hides the sub-options section beneath each form checkbox.
                        function toggle_form_options(form_id, ischecked) {
                            var form_options = document.getElementById("form_options_" + form_id);
                            var display = (ischecked) ? "block" : "none";
                            form_options.style.display = display;
                        }

                        //var swim_radio = document.getElementById("activecampaign_form_swim");

                        function ac_str_is_url(url) {
                            url += '';
                            return url.match( /((http|https|ftp):\/\/|www)[a-z0-9\-\._]+\/?[a-z0-9_\.\-\?\+\/~=&#%;:\|,\[\]]*[a-z0-9\/=?&;%\[\]]{1}/i );
                        }

                        function swim_toggle(form_id, swim_checked) {
                            if (swim_checked) {

                            }
                        }

                        function sync_toggle(form_id, sync_checked) {
                            var ajax_checkbox = document.getElementById("activecampaign_form_ajax_" + form_id);
                            var action_textbox = document.getElementById("activecampaign_form_action_" + form_id);
                            if (sync_checked && action_textbox.value == "") {
                                // if Sync is chosen, and there is no custom action URL, check the Ajax option.
                                ajax_checkbox.checked = true;
                            }
                        }

                        function ajax_toggle(form_id, ajax_checked) {
                            var ajax_checkbox = document.getElementById("activecampaign_form_ajax_" + form_id);
                            var sync_radio = document.getElementById("activecampaign_form_sync_" + form_id);
                            var action_textbox = document.getElementById("activecampaign_form_action_" + form_id);
                            var site_tracking_checkbox = document.getElementById("activecampaign_site_tracking");
                            if (ajax_checked && site_tracking_checkbox.checked)  {
                                alert("If you use this option, site tracking cannot be enabled.");
                                site_tracking_checkbox.checked = false;
                            }
                        }

                        function action_toggle(form_id, action_value) {
                            var action_textbox = document.getElementById("activecampaign_form_action_" + form_id);
                            if (action_textbox.value && ac_str_is_url(action_textbox.value)) {

                            }
                        }

                        function site_tracking_toggle(is_checked) {

                            // Set the hidden element based on whether site tracking is enabled or not
                            var hiddenSiteTracking = document.getElementsByName("site_tracking")[0];
                            hiddenSiteTracking.value = is_checked ? 1 : 0;

                            // Pre-select the correct radio option underneath "Site Tracking"
                            var site_tracking_options = document.getElementById("activecampaign_site_tracking_options");
                            site_tracking_options.className = is_checked ? "" : "disabled";

                            // we can't allow site tracking if ajax is used because that uses the API.
                            // so here we check to see if they have chosen ajax for any form, an if so alert them and uncheck the ajax options.
                            if (is_checked)  {
                                var inputs = document.getElementsByTagName("input");
                                // if Sync is checked, and action value is empty or invalid, and they UNcheck Ajax, alert them.
                                var checked_already = [];
                                for (var i in inputs) {
                                    var c = inputs[i];
                                    if (c.type == "checkbox" && c.name.match(/^ajax\[/) && c.checked) {;
                                        // example: <input type="checkbox" name="ajax[1642]" id="activecampaign_form_ajax_1642" value="1" checked="checked" onchange="ajax_toggle(1642, this.checked);">
                                        checked_already.push(c.id);
                                    }
                                }
                                if (checked_already.length) {
                                    // if at least one of the ajax checkboxes is checked.
                                    alert("If you enable site tracking, a page refresh is required.");
                                    for (var i in checked_already) {
                                        var id = checked_already[i];
                                        var dom_item = document.getElementById(id);
                                        dom_item.checked = false;
                                    }
                                }
                            }

                        }

                    </script>

                <p><button type="submit" style="font-size: 16px; margin-top: 25px; padding: 10px;"><?php echo __($button_value, "menu-activecampaign"); ?></button></p>
                <?php wp_nonce_field('activecampaign_save_settings'); ?>

        </form>

        <?php

        if (isset($instance["forms"])) {
            ?>

                <hr style="border: 1px dotted #ccc; border-width: 1px 0 0 0; margin-top: 30px;" />
                <h3><?php echo __("Subscription Form(s) Preview", "menu-activecampaign"); ?></h3>

                <?php

                foreach ($instance["forms"] as $form_id => $form_metadata) {
                    $form_source = activecampaign_form_source($instance, $form_metadata, true);
                    echo $form_source;

                    ?>

                    <p><?php echo __("Embed using"); ?><code>[activecampaign form=<?php echo $form_id; ?> css=<?php echo (isset($instance["css"]) && !empty($instance["css"][$form_id]))? '1' : '0'; ?>]</code></p>

                    <hr style="border: 1px dotted #ccc; border-width: 1px 0 0 0; margin-top: 40px;" />

                    <?php
                }
        }

        ?>

    </div>

    <?php
}

function ac_dbg($var, $continue = 0, $element = "pre")
{
    echo "<" . $element . ">";
    echo "Vartype: " . gettype($var) . "\n";
    if (is_array($var)) {
        echo "Elements: " . count($var) . "\n\n";
    } elseif (is_string($var)) {
        echo "Length: " . strlen($var) . "\n\n";
    }
    print_r($var);
    echo "</" . $element . ">";
    if (!$continue) {
        exit();
    }
}

function activecampaign_fetch_accountid($ac){
    /*
        It appears user/me doesn't always work, but for some reason that seems to be the only endpoint with
        param level access to account ID. As a fallback, let's fetch the script via API and extract the Account ID
    */

	$user_me = $ac->api("user/me");
    if(isset($user_me) && !empty($user_me->trackid)){
		return $user_me->trackid;
    }

	// Try v3 code check
    $script = $ac->api3('siteTracking/code', [], false);
    //try regex to extract from script call.
    $matches = [];
    if(preg_match('/\'setAccount\', \'(\d*)\'\)/', $script, $matches) !== false && !empty($matches[1])){
        return $matches[1];
    }

    return '';
}

function activecampaign_getforms($ac, $instance)
{
    $forms = $ac->api("form/getforms");
    if ((int)$forms->success) {
        $items = array();
        $forms = get_object_vars($forms);
        foreach ($forms as $key => $value) {
            if (is_numeric($key)) {
                $items[$value->id] = get_object_vars($value);
            }
        }
        $instance["forms"] = $items;
    } else {
        if ($forms->error == "Failed: Nothing is returned") {
            $instance["error"] = "Nothing was returned. Do you have at least one form created in your ActiveCampaign account?";
        } else {
            $instance["error"] = $forms->error;
        }
    }
    return $instance;
}

function activecampaign_form_html($ac, $instance)
{

    if ($instance["forms"]) {
        foreach ($instance["forms"] as $form) {
            // $instance["form_id"] is an array of form ID's (since we allow multiple now).

            if (isset($instance["form_id"]) && in_array($form["id"], $instance["form_id"])) {
                if (isset($form["version"]) && $form["version"] == 2) {
                    // Nothing to do here - we'll generate the form source code on page load.
                    continue;
                }

                // Version 1 forms only should proceed here!!

                $domain = $instance["account"];
                $protocol = "https:";

                $form_embed_params = array(
                    "id" => $form["id"],
                    "ajax" => $instance["ajax"][$form["id"]],
                    "css" => $instance["css"][$form["id"]],
                );

                $sync = ($instance["syim"][$form["id"]] == "sync") ? 1 : 0;

                if ($instance["action"][$form["id"]]) {
                    $form_embed_params["action"] = $instance["action"][$form["id"]];
                }

                if ((int)$form_embed_params["ajax"] && !isset($form_embed_params["action"])) {
                    // if they are using Ajax, but have not provided a custom action URL, we need to push it to a script where we can submit the form/process API request.
                    // remove the "http(s)" portion, because it was conflicting with the Ajax request (I was getting 404's).
                    $api_url_process = preg_replace("/https:\/\//", "", $instance["api_url"]);
                    $form_embed_params["action"] = plugins_url("form_process.php?sync=" . $sync, __FILE__);
                }

                // prepare the params for the API call
                $api_params = array();
                foreach ($form_embed_params as $var => $val) {
                    $api_params[] = $var . "=" . urlencode($val);
                }

                // fetch the HTML source
                $html = $ac->api("form/embed?" . implode("&", $api_params));

                if ((int)$form_embed_params["ajax"]) {
                    // used for the result message that is displayed after submitting the form via Ajax
                    $html = "<div id=\"form_result_message\"></div>" . $html;
                }

                if ($html) {
                    if ($instance["account"]) {
                        // replace the API URL with the account URL (IE: https://account.api-us1.com is changed to http://account.activehosted.com).
                        // (the form has to submit to the account URL.)
                        if (!$instance["action"]) {
                            $html = preg_replace("/action=['\"][^'\"]+['\"]/", "action='" . $protocol . "//" . $domain . "/proc.php'", $html);
                        }
                    }
                    // replace the Submit button to be an actual submit type.
                    //$html = preg_replace("/input type='button'/", "input type='submit'", $html);
                }

                if ((int)$form_embed_params["css"]) {
                    // get the style content so we can prepend each rule with the form ID (IE: #_form_1341).
                    // this is in case there are multiple forms on the same page - their styles need to be unique.
                    preg_match_all("|<style[^>]*>(.*)</style>|iUs", $html, $style_blocks);
                    if (isset($style_blocks[1]) && isset($style_blocks[1][0]) && $style_blocks[1][0]) {
                        $css = $style_blocks[1][0];
                        // remove excess whitespace from within the string.
                        $css = preg_replace("/\s+/", " ", $css);
                        // remove whitespace from beginning and end of string.
                        $css = trim($css);
                        $css_rules = explode("}", $css);
                        $css_rules_new = array();
                        foreach ($css_rules as $rule) {
                            $rule_array = explode("{", $rule);
                            $rule_array[0] = preg_replace("/\s+/", " ", $rule_array[0]);
                            $rule_array[0] = trim($rule_array[0]);
                            $rule_array[1] = preg_replace("/\s+/", " ", $rule_array[1]);
                            $rule_array[1] = trim($rule_array[1]);
                            if ($rule_array[1]) {
                                // there could be comma-separated rules.
                                $rule_array2 = explode(",", $rule_array[0]);
                                foreach ($rule_array2 as $rule_) {
                                    $rule_ = "#_form_" . $form["id"] . " " . $rule_;
                                    $css_rules_new[] = $rule_ . " {" . $rule_array[1] . "}";
                                }
                            }
                        }
                    };

                    $new_css = implode("\n\n", $css_rules_new);
                    // remove existing styles.
                    $html = preg_replace("/<style[^>]*>(.*)<\/style>/s", "", $html);
                    // replace with updated CSS string.
                    $html = "<style>" . $new_css . "</style>" . $html;
                }

                // check for custom width.
                if ((int)$form["widthpx"]) {
                    // if there is a custom width set
                    // find the ._form CSS rule
                    preg_match_all("/\._form {[^}]*}/", $html, $_form_css);
                    if (isset($_form_css[0]) && $_form_css[0]) {
                        foreach ($_form_css[0] as $_form) {
                            // find "width:400px"
                            preg_match("/width:[0-9]+px/", $_form, $width);
                            if (isset($width[0]) && $width[0]) {
                                // IE: replace "width:400px" with "width:200px"
                                $html = preg_replace("/" . $width[0] . "/", "width:" . (int)$form["widthpx"] . "px", $html);
                            }
                        }
                    }
                }

                $instance["form_html"][$form["id"]] = $html;
            }
        }
    } else {
        // no forms created in the AC account yet.
        echo "<p style='margin: 0 0 20px; padding: 14px; font-size: 14px; color: #776e30; font-family:arial; background: #fff3a5; line-height: 19px; border-radius: 5px; overflow: hidden;'>" . __("Make sure you have at least one form created in ActiveCampaign.") . "</p>";
    }

    return $instance;
}

function activecampaign_register_widgets()
{
    register_widget("ActiveCampaign_Widget");
}

function activecampaign_display($args)
{
    extract($args);
}

function activecampaign_register_shortcodes()
{
    add_shortcode("activecampaign", "activecampaign_shortcodes");
}

function activecampaign_plugin_menu()
{
    add_options_page(__("ActiveCampaign Settings", "menu-activecampaign"), __("ActiveCampaign", "menu-activecampaign"), "manage_options", "activecampaign", "activecampaign_plugin_options");
}

function activecampaign_editor_buttons()
{
    add_filter("mce_external_plugins", "activecampaign_add_buttons");
    add_filter("mce_buttons", "activecampaign_register_buttons");
}

function activecampaign_add_buttons($plugin_array)
{
    $plugin_array["activecampaign_editor_buttons"] = plugins_url("editor_buttons.js", __FILE__);
    //we need to load the JS for this button as well
    //and we should load it on any page that has the button loaded, since some plugins allow editing pages from anywhere
    wp_enqueue_script("editor_pages", plugins_url("editor_pages.js", __FILE__), array(), false, true);

    // any data we need to access in JavaScript.
    $data = array(
        "site_url" => __(site_url()),
        "wp_version" => $GLOBALS["wp_version"],
    );
    wp_localize_script("editor_pages", "php_data", $data);

    return $plugin_array;
}

function activecampaign_register_buttons($buttons)
{
    array_push($buttons, "activecampaign_editor_forms");
    return $buttons;
}

//add_action("widgets_init", "activecampaign_register_widgets");
add_action("init", "activecampaign_register_shortcodes");
add_action("init", "activecampaign_editor_buttons");
add_action("init", "activecampaign_form_block_init");
add_action("admin_menu", "activecampaign_plugin_menu");
add_filter("widget_text", "do_shortcode");

global $pagenow;

add_action("wp_ajax_activecampaign_get_forms", "activecampaign_get_forms_callback");
add_action("wp_ajax_activecampaign_get_forms_html", "activecampaign_get_forms_html_callback");
add_action("admin_enqueue_scripts", "activecampaign_custom_wp_admin_style");
add_action("wp_enqueue_scripts", "activecampaign_frontend_scripts");

// get the raw forms data (array) for use in multiple spots.
function activecampaign_get_forms_ajax()
{
    // get forms that are cached after setting things up from the ActiveCampaign settings page.
    global $wpdb; // this is how you get access to the database
    $forms = array();
    $settings = get_option("settings_activecampaign");
//ac_dbg($settings);
    if ($settings["form_id"]) {
        foreach ($settings["forms"] as $form) {
            if (in_array($form["id"], $settings["form_id"])) {
                $forms[$form["id"]] = $form["name"];
            }
        }
    }
    return $forms;
}

// JSON output.
function activecampaign_get_forms_callback()
{
    $forms = activecampaign_get_forms_ajax();
    $forms = json_encode($forms);
    echo $forms;
    die();
}

// HTML output of forms (for the post dialog/window after you click the icon in the editor toolbar).
// version 3.9 has this.
function activecampaign_get_forms_html_callback()
{
    $forms = activecampaign_get_forms_ajax();
    echo "<div style='padding: 0 10px;font-family: Arial, Helvetica, sans-serif; font-size: 13px; line-height: 1.5;'>";
    echo "<p>" . __("Choose an integration form below to embed into your post or page body. Add or edit forms in ActiveCampaign and then refresh the forms on the <a href='" . get_site_url() . "/wp-admin/options-general.php?page=activecampaign' target='_blank' style='color: #23538C !important;'>Settings page</a>.") . "</p>";
    if ($forms) {
        echo "<div style='padding: 0; margin: 0 -3px'>";
    }
    foreach ($forms as $formid => $formname) {
        echo "<a href='#' onclick='parent.activecampaign_editor_form_embed(" . $formid . "); return false;' style='display: inline-block; text-transform: capitalize; margin: 3px 3px 10px; padding: 7px 11px; font-size: 13px; text-align: center; text-decoration: none !important; border-radius: 4px !important; color: #5d5d5d !important; background: #fff !important; border: 1px solid #c0c0c0 !important; cursor: pointer !important; zoom: 1; -webkit-appearance: none; line-height: 1.42857143; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; font-weight: 600;'
>" . $formname . "</a>";
    }
    if ($forms) {
        echo "</div>";
    }
    echo "</div>";
    die();
}

function activecampaign_custom_wp_admin_style()
{
    wp_register_style("activecampaign-subscription-forms", "//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css");
    wp_enqueue_style("activecampaign-settings", plugins_url("admin_styles.css", __FILE__));
    wp_enqueue_script("jquery-ui-dialog");
    wp_enqueue_style("wp-jquery-ui-dialog");
}

// scripts run only on the front-end.
function activecampaign_frontend_scripts()
{
    wp_enqueue_script("site_tracking", plugins_url("site_tracking.js", __FILE__), array(), false, true);
    $settings = get_option("settings_activecampaign");
    unset($settings["api_url"]);
    unset($settings["api_key"]);
    $current_user = wp_get_current_user();
    $user_email = "";
    if (isset($current_user->data->user_email)) {
        $user_email = $current_user->data->user_email;
    }
    // any data we need to access in JavaScript.
    $data = array(
        "ac_settings" => array(
            "tracking_actid" => (!empty($settings["tracking_actid"]))? $settings["tracking_actid"]: null,
            "site_tracking_default" => 1,
        ),
        "user_email" => $user_email,
    );
    if (isset($settings["site_tracking"]) && (int)$settings["site_tracking"]) {
        // This will only be set if the checkbox is checked on the ActiveCampaign settings page.
        $data["ac_settings"]["site_tracking"] = 1;
        if (isset($settings["activecampaign_site_tracking_default"])) {
            $data["ac_settings"]["site_tracking_default"] = (int)$settings["activecampaign_site_tracking_default"];
        }
    }
    wp_localize_script("site_tracking", "php_data", $data);
}

// Adding a notification mechanism

function activecampaign_admin_notice(){
    $screen = get_current_screen();
	if( !$screen || $screen->base !== 'settings_page_activecampaign'){
	    return;
    }

	$notices = [
		'v8.1.5 - We have updated the descriptions around form settings below. Please take a moment to read. Also note, if you are handling site tracking manually (GDPR), all JavaScript references to pgo() are now using the properly documented vgo() method.'
	];

	$index = (int) get_option("activecampaign_notice_index", 0);

	// Very first notice should get shown anyway since we have no count tracking yet
    if(count($notices) === 1 && empty($index)){
		$index = 0;
    }
	// Default to notices count so we never show notices on fresh installs
    elseif(empty($index)){
        $index = count($notices);
    }
	$initialIndex = $index;

	for($i = $index; $i < count($notices); $i++){
		echo '<div class="notice notice-info is-dismissible"><p>'.$notices[$i].'</p></div>';
		$index++;
	}

	if($initialIndex !== $index){
		update_option("activecampaign_notice_index", $index);
    }
}
add_action('admin_notices', 'activecampaign_admin_notice');
