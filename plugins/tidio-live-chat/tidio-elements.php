<?php

if (!defined('WPINC')) {
    die('File loaded directly. Exiting.');
}

/**
 * Plugin Name: Tidio Chat
 * Plugin URI: http://www.tidio.com
 * Description: Tidio Live Chat - live chat boosted with chatbots for your online business. Integrates with your website in less than 20 seconds.
 * Version: 6.0.29
 * Requires at least: 4.7
 * Requires PHP: 7.2
 * Author: Tidio LLC
 * Author URI: http://www.tidio.com
 * License: GPL2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: tidio-live-chat
 * Domain Path: /languages/
 * Update URI: https://wordpress.org/plugins/tidio-live-chat/
 */

define('TIDIOCHAT_VERSION', '6.0.29');
define('AFFILIATE_CONFIG_FILE_PATH', get_template_directory() . '/tidio_affiliate_ref_id.txt');

require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

use TidioLiveChat\Encryption\Service\EncryptionServiceFactory;
use TidioLiveChat\IntegrationState;
use TidioLiveChat\TidioLiveChat;

/**
 * @return void
 */
function initializeTidioLiveChat()
{
    if (!empty($_GET['tidio_chat_version'])) {
        echo TIDIOCHAT_VERSION;
        exit;
    }

    $container = new \TidioLiveChat\Container();
    $tidioLiveChat = new TidioLiveChat($container);
    $tidioLiveChat->load();
}

add_action('init', 'initializeTidioLiveChat');

$encryptionService = (new EncryptionServiceFactory())->create();
register_activation_hook(__FILE__, [new IntegrationState($encryptionService), 'turnOnAsyncLoading']);

/**
 * @param string $plugin
 * @return void
 */
function redirectToTidioPluginPage($plugin)
{
    if ($plugin == plugin_basename(__FILE__)) {
        exit(wp_safe_redirect(admin_url('admin.php?page=tidio-live-chat')));
    }
}

add_action('activated_plugin', 'redirectToTidioPluginPage');
