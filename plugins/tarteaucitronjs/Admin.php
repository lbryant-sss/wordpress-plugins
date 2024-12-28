<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

add_action('admin_menu', 'tarteaucitron_settings');
function tarteaucitron_settings() {
    add_options_page("tarteaucitron.js", "tarteaucitron.js", 'manage_options', "tarteaucitronjs", 'tarteaucitron_config_page');
}

add_action('admin_enqueue_scripts', 'tarteaucitron_admin_css');
function tarteaucitron_admin_css() {
    wp_register_style('tarteaucitronjs', plugins_url('tarteaucitronjs/css/admin.css'));

    wp_enqueue_style('tarteaucitronjs');
    wp_enqueue_script('tarteaucitronjs', plugins_url('tarteaucitronjs/js/admin.js'));
}

function tarteaucitron_config_page() {
    
    settings_fields( 'tarteaucitron' );

    if(isset($_POST['tarteaucitronEmail']) AND isset($_POST['tarteaucitronPass']) AND wp_verify_nonce( $_POST['_wpnonce'], 'tac_login' )) {

        update_option('tarteaucitronUUID', tac_sanitize($_POST['tarteaucitronEmail'], 'uuid'));
        update_option('tarteaucitronToken', tac_sanitize($_POST['tarteaucitronPass'], 'token'));
    }
            
    if(get_option('tarteaucitronUUID') != '' OR get_option('tarteaucitronToken') != '') {
    
        if(tarteaucitron_post('check=1') == "0") {

            ?><div class="error notice">
              <p><?php _e( 'ID or Token incorrect, you have been logged out.', 'tarteaucitronjs' ); ?></p>
            </div><?php
        }
    }
            
    if (isset($_POST['tarteaucitronLogout']) && wp_verify_nonce( $_POST['_wpnonce'], 'tac_logout' )) {
    
        tarteaucitron_post('remove=1');
        update_option('tarteaucitronUUID', '');
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
                
    if(get_option('tarteaucitronUUID', '') == '') {

        echo '<div class="tarteaucitronjs wrap">
	       	<form method="post" action="">';
                wp_nonce_field('tac_login');
                echo '<div class="tarteaucitronDiv" style="margin-bottom:25px;background: #ffe;text-align: center;margin-top: 27px;">
                   <p style="font-size: 16px;">'.__('Get the ID and Token on the "My account" tab', 'tarteaucitronjs').'<br/><b><a href="https://tarteaucitron.io/pro/login/#account" target="_blank">'.__("View my account", 'tarteaucitronjs').'</a></b></p>
                </div>
                <h2 style="margin-bottom:20px">'.__('Login', 'tarteaucitronjs').'</h2>
                <div class="tarteaucitronDiv">
                    <table class="form-table">
				        <tr valign="top">
                            <th scope="row">'.__('ID', 'tarteaucitronjs').'</th>
                            <td><input type="text" name="tarteaucitronEmail" /></td>
                        </tr>
				        <tr valign="top">
                            <th scope="row">'.__('Token', 'tarteaucitronjs').'</th>
                            <td><input type="text" name="tarteaucitronPass" /></td>
                        </tr>
				        <tr valign="top">
                            <th scope="row">&nbsp;</th>
                            <td><input type="submit" class="button button-primary" /><br/><br/><br/><a href="https://tarteaucitron.io/pro/" target="_blank">'.__("Sign up", 'tarteaucitronjs').'</a></td>
                        </tr>
                    </table>
                </div>
			</form>
        </div>
        <style type="text/css">.tarteaucitronDiv{background:#FFF;padding: 10px;border: 1px solid #eee;border-bottom: 2px solid #ddd;max-width: 500px;}</style>';
    } else {
                
        $abo = tarteaucitron_post('abonnement=1');
        $css = 'background: green;border: 0;box-shadow: 0 0 0;text-shadow: 0 0 0;';

        if($abo > time()) {
            $abonnement = __('Subscription active', 'tarteaucitronjs').' '.date('Y-m-d', $abo);
        } else {
            if (!tarteaucitronNeedSubscription()) {
                $abonnement = __('Subscription expired (limited to 3 services)', 'tarteaucitronjs');
            } else {
                $abonnement = __('Subscription expired', 'tarteaucitronjs');
            }
            $css = 'background: red;border: 0;box-shadow: 0 0 0;text-shadow: 0 0 0;';
        }
                
        echo '<div class="tarteaucitronjs wrap">
		
            <form method="post" action="">';
                wp_nonce_field('tac_logout');
                echo '<input type="hidden" name="tarteaucitronLogout" />
                <div class="tarteaucitronDiv">
                    <table class="form-table" style="margin:0 !important">
    		            <tr valign="top">
                            <td><a class="button button-primary" href="https://tarteaucitron.io/pro/login/" target="_blank">'.__('Statistics', 'tarteaucitronjs').'</a> <a class="button button-primary" href="https://tarteaucitron.io/pro/login/#paiement" target="_blank" style="'.$css.'">'.$abonnement.'</a> <input class="button button-primary" type="submit" value="'.__('Logout', 'tarteaucitronjs').'" style="float:right;background: #F7F7F7;text-shadow: 0 0 0;border: 1px solid #aaa;box-shadow: 0 0 0;color: #555;" /></td>
                        </tr>
                    </table>
                </div>
			</form>
            
            <div class="tarteaucitronDiv" style="margin-bottom: 120px;max-width:600px;padding:20px;background:#fff;margin-top:20px">
				
                <div style="background: #F6FFFF;padding: 20px;border: 1px solid #188DC5;border-radius: 20px;margin: 10px 0 50px;font-size: 17px;line-height: 25px;">'.__('Invisible services (analytics, APIs, ...) are on this page, the others (social network, comment, ads ...) need to be added with <b><a href="widgets.php">WordPress Widgets</a></b>', 'tarteaucitronjs').'.</div>
                
                <form action="" method="post" target="" class="tarteaucitronjs" onsubmit="" style="width: 100%;float: none;">';
                wp_nonce_field('tac_service');
                echo tarteaucitron_post('getForm=3').'
                </form>
            </div>
        </div>
        <style type="text/css">.tarteaucitronDiv{background:#FFF;padding: 10px;border: 1px solid #eee;border-bottom: 2px solid #ddd;max-width: 500px;}</style>';
    }
}

