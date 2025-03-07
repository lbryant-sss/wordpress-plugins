<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

add_action('admin_menu', 'tarteaucitron_settings');
function tarteaucitron_settings() {
    add_options_page("tarteaucitron.io", "tarteaucitron.io", 'manage_options', "tarteaucitronjs", 'tarteaucitron_config_page');
}

add_action('admin_enqueue_scripts', 'tarteaucitron_admin_css');
function tarteaucitron_admin_css() {
    wp_register_style('tarteaucitronjs', plugins_url('tarteaucitronjs/css/admin.css'));

    wp_enqueue_style('tarteaucitronjs');
    wp_enqueue_script('tarteaucitronjs', plugins_url('tarteaucitronjs/js/admin.js'));
}

function tarteaucitron_config_page() {

    settings_fields( 'tarteaucitron' );

    echo '<style>.tarteaucitronjs .button.button-primary {
    background: #333;
    border-color:#333;
}
.tarteaucitronjs a {
    color:#333
}</style><h1 style="display: flex; align-items: center;"><img src="'.plugins_url('assets/cropped-logo.png', __FILE__).'" style="margin-right: 10px; height: 40px;" /> tarteaucitron.io</h1>';

    if(isset($_POST['tarteaucitronEmail']) AND isset($_POST['tarteaucitronPass']) AND wp_verify_nonce( $_POST['_wpnonce'], 'tac_login' )) {

        if (tac_sanitize($_POST['tarteaucitronEmail'], 'uuid') != "" && tac_sanitize($_POST['tarteaucitronPass'], 'token') != "") {
            update_option('tarteaucitronUUID', tac_sanitize($_POST['tarteaucitronEmail'], 'uuid'));
            update_option('tarteaucitronToken', tac_sanitize($_POST['tarteaucitronPass'], 'token'));
        }
    }

    if(get_option('tarteaucitronUUID') != '' && get_option('tarteaucitronToken') != '') {

        if(tarteaucitron_post('check=1') != "1") {

            update_option('tarteaucitronToken', '');

            ?><div class="error notice">
              <p><?php _e( 'ID or Token incorrect, you have been logged out.', 'tarteaucitronjs' ); ?></p>
            </div><?php
        }
    }

    if (isset($_POST['tarteaucitronLogout']) && wp_verify_nonce( $_POST['_wpnonce'], 'tac_logout' )) {

        tarteaucitron_post('remove=1');
        update_option('tarteaucitronToken', '');

    } elseif(isset($_POST['tarteaucitron_send_services_static']) AND $_POST['wp_tarteaucitron__service'] != '' AND wp_verify_nonce( $_POST['_wpnonce'], 'tac_service' )) {

        $service = tac_sanitize($_POST['wp_tarteaucitron__service'], 'alpha');
        $r = 'service='.$service.'&configure_services='.tac_sanitize($_POST['wp_tarteaucitron__configure_services'], 'numeric').'&';

        foreach ($_POST as $key => $val) {
            if (preg_match('#^wp_tarteaucitron__'.$service.'#', $key)) {
                $r .= preg_replace('#^wp_tarteaucitron__#', '', $key).'='.$val.'&';
            }
        }
        tarteaucitron_post(trim($r, '&'));
    }

    if(get_option('tarteaucitronToken', '') == '' OR get_option('tarteaucitronUUID', '') == '') {

        echo '<div class="tarteaucitronjs wrap">
	       	<h2 style="margin-bottom:20px">'.__('Login', 'tarteaucitronjs').'</h2>
	       	<form method="post" action="">';
                wp_nonce_field('tac_login');
                echo '
                <div class="notice notice-info" style="max-width: 743px; border-left-color: color(srgb 0.9411 0.6425 0.1384);"><p>'.__('Get the ID and Token on the "My account" tab', 'tarteaucitronjs').' <a href="https://tarteaucitron.io/dashboard/" target="_blank">https://tarteaucitron.io/dashboard/</a></p></div>
                <div class="tarteaucitronDiv">
                    <table class="form-table">
				        <tr valign="top">
                            <th scope="row">'.__('ID', 'tarteaucitronjs').'</th>
                            <td><input type="text" name="tarteaucitronEmail" value="'.get_option('tarteaucitronUUID', '').'" /></td>
                        </tr>
				        <tr valign="top">
                            <th scope="row">'.__('Token', 'tarteaucitronjs').'</th>
                            <td><input type="text" name="tarteaucitronPass" /></td>
                        </tr>
				        <tr valign="top">
                            <th scope="row">&nbsp;</th>
                            <td><input type="submit" class="button button-primary" /></td>
                        </tr>
                    </table>
                </div>
			</form>
        </div>
        <style type="text/css">.tarteaucitronDiv{background:#FFF;padding: 10px;border: 1px solid #ccc;border-bottom: 2px solid #bbb;max-width: 750px;}</style>';
    } else {

        $abo = tarteaucitron_post('abonnement=1');

        if($abo > time()) {
            $abonnement = "<span style='color:darkgreen'>OK</span>";


            $url = 'https://tarteaucitron.io/load.js?domain=' . $_SERVER['SERVER_NAME'] . '&uuid=' . tac_sanitize(get_option('tarteaucitronUUID'), 'uuid');
            $response = wp_remote_get($url);

            if (!is_wp_error($response) && isset($response['body'])) {
                $body = $response['body'];

                if (strpos($body, 'window.tarteaucitron_blacklist') === 0) {
                    update_option('tarteaucitronShowWidget', 'invisible');
                    $mode = "⚡️Automatic";
                } elseif (strpos($body, 'console.error') === 0) {

                } else {
                    update_option('tarteaucitronShowWidget', 'visible');
                    $mode = "🧑‍💻 Manual";
                }
            }


        } else {
            $abonnement = "<span style='color:darkred'>".__('invalid', 'tarteaucitronjs')."</span>";
        }

        echo '<div class="tarteaucitronjs wrap">
		<h2 style="margin-bottom:20px">'.__('Dashboard', 'tarteaucitronjs').'<br/><a href="https://tarteaucitron.io/dashboard/" style="font-size:14px" target="_blank">https://tarteaucitron.io/dashboard/ ↗</a></h2>
            <form method="post" action="">';
                wp_nonce_field('tac_logout');
                echo '<input type="hidden" name="tarteaucitronLogout" />
                <div class="tarteaucitronDiv">
                    <table class="form-table">
                    
                    <tr valign="top">
                            <th scope="row">'.__('Login', 'tarteaucitronjs').'</th>
                            <td><b style="color:darkgreen">OK</b> <input style="display: inline-block; float:right; margin: -8px 0 0 50px; opacity: 0.5;" class="button button-primary" type="submit" value="'.__('Logout', 'tarteaucitronjs').'" /></td>
                        </tr>
                    <tr valign="top">
                            <th scope="row">'.__('License', 'tarteaucitronjs').'</th>
                            <td><b>'.$abonnement.'</b></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">'.__('ID', 'tarteaucitronjs').'</th>
                            <td>'.get_option('tarteaucitronUUID', '').'</td>
                        </tr>
                        
 
                        
                        <tr valign="top">
                            <th scope="row">'.__('Domain', 'tarteaucitronjs').'</th>
                            <td>'.$_SERVER['SERVER_NAME'].'</td>
                        </tr>
                    
                    <tr valign="top">
                            <th scope="row">'.__('Mode', 'tarteaucitronjs').'</th>
                            <td>'.$mode.'</td>
                        </tr>
                    </table>
                </div>
			</form>';

            if (get_option('tarteaucitronShowWidget', 'visible') == 'visible') {
                echo '<div class="tarteaucitronDiv" style = "margin-bottom: 20px;max-width:730px;padding:20px;background:#fff;margin-top:20px" >
                <h4 style="margin-top:0">'.__('How it works', 'tarteaucitronjs').'</h4>
                ' . __('In manual mode, you must remove the HTML markers and/or plugins that load third party services from your site and add them via the tarteaucitron dashboard.', 'tarteaucitronjs') . '
                </div>';
            } else {
                echo '<div class="tarteaucitronDiv" style = "margin-bottom: 20px;max-width:730px;padding:20px;background:#fff;margin-top:20px" >
                <h4 style="margin-top:0">'.__('How it works', 'tarteaucitronjs').'</h4>
                ' . __("In automatic mode, you don't need to remove plugins or services from your sites or from your tag manager, tarteaucitron will automatically detect them and request consent before loading them.", 'tarteaucitronjs') . '
                </div>';
            }

            if (get_option('tarteaucitronShowWidget', 'visible') == 'visible') {

                echo '<div class="tarteaucitronDiv" style="margin-bottom: 120px;max-width:730px;padding:20px;background:#fff;margin-top:20px">
				
                <div style="background: #F6FFFF;padding: 20px;border: 1px solid #188DC5;border-radius: 20px;margin: 10px 0 50px;font-size: 17px;line-height: 25px;">' . __('Invisible services (analytics, APIs, ...) are on this page, the others (social network, comment, ads ...) need to be added with <b><a href="widgets.php">WordPress Widgets</a></b>', 'tarteaucitronjs') . '.</div>
                
                <form action="" method="post" target="" class="tarteaucitronjs" onsubmit="" style="width: 100%;float: none;">';
                wp_nonce_field('tac_service');
                echo tarteaucitron_post('getForm=3') . '
                </form>
                </div>
                </div>';
            }
        echo '<style type="text/css">.tarteaucitronDiv{background:#FFF;padding: 10px;border: 1px solid #ccc;border-bottom: 2px solid #bbb;max-width: 750px;}#tarteaucitron-expired-notice {max-width: 743px;}</style>';
    }
}

/** Check licence **/
add_action('admin_notices', function () {

    if (!current_user_can('manage_options')) {return;}

    if (tac_sanitize(get_option('tarteaucitronUUID'), 'uuid') == "") {return;}

    $url = 'https://tarteaucitron.io/load.js?domain=' . $_SERVER['SERVER_NAME'] . '&uuid=' . tac_sanitize(get_option('tarteaucitronUUID'), 'uuid');
    $response = wp_remote_get($url);

    if (!is_wp_error($response) && isset($response['body'])) {
        $body = $response['body'];

        if (!empty($body) && strpos($body, 'console.error') === 0) {
            ?>
            <style>#wp-admin-bar-tarteaucitronjs {background: rgb(180, 0, 0)!important;}</style>

            <?php
            $current_screen = get_current_screen();
            if ($current_screen && $current_screen->id === 'settings_page_tarteaucitronjs') { ?>
                <div class="notice notice-error" id="tarteaucitron-expired-notice">
                    <?php
                    if (get_user_locale() === 'fr_FR') { ?>
                        <p><big>🍪🚨 Votre licence tarteaucitron.io est expirée</big><br/>Connectez-vous pour prolonger la licence : <a href="https://tarteaucitron.io/dashboard/" target="_blank">https://tarteaucitron.io/dashboard/</a></p>
                    <?php } else { ?>
                        <p><big>🍪🚨 Your tarteaucitron.io license is expired</big><br/>Login to renew your license: <a href="https://tarteaucitron.io/en/my-dashboard/" target="_blank">https://tarteaucitron.io/en/my-dashboard/</a></p>
                    <?php } ?>
                </div>
                <?php
            }
        }
    }
});
