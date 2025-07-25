<?php
    if ( !defined('ABSPATH' ) )
        exit();
?>

<div id="trp-settings-page" class="wrap">
    <?php require_once TRP_PLUGIN_DIR . 'partials/settings-header.php'; ?>

    <?php do_action ( 'trp_settings_navigation_tabs' ); ?>

    <?php
    //initialize the object
    $trp_addons_listing = new TRP_Addons_List_Table();
    $trp_addons_listing->images_folder = TRP_PLUGIN_URL.'assets/images/';
    $trp_addons_listing->text_domain = 'translatepress-multilingual';

    if( defined( 'TRANSLATE_PRESS' ) )
        $trp_addons_listing->current_version = TRANSLATE_PRESS;
    else
        $trp_addons_listing->current_version = 'TranslatePress - Multilingual';//in free version we do not define the constant as free version needs to be active always
    $trp_addons_listing->tooltip_header = __( 'TranslatePress Add-ons', 'translatepress-multilingual' );
    $trp_addons_listing->tooltip_content = sprintf( __( 'You must first purchase this version to have access to the addon %1$shere%2$s', 'translatepress-multilingual' ), '<a target="_blank" href="'. trp_add_affiliate_id_to_link('https://translatepress.com/pricing/?utm_source=wpbackend&utm_medium=clientsite&utm_content=add-on-page&utm_campaign=TRP').'">', '</a>' );

    //for Pro versions we need to check if the license is valid
    $trp = TRP_Translate_Press::get_trp_instance();
    if( !in_array( 'TranslatePress', $trp->tp_product_name ) ){
        $license_status = get_option('trp_license_status');
        if ($license_status === 'invalid')
            $trp_addons_listing->tooltip_content = sprintf(__('You need an active license to have access to the addon. Renew or purchase a new one %1$shere%2$s.', 'translatepress-multilingual'), '<a target="_blank" href="https://translatepress.com/pricing/?utm_source=tp-addons&utm_medium=client-site&utm_campaign=expired-license">', '</a>');
        else if ($license_status == false) {
            $trp_addons_listing->tooltip_content = sprintf(__('Please %1$senter your license%2$s key first, to activate this addon.', 'translatepress-multilingual'), '<a target="_blank" href="' . admin_url('admin.php?page=trp_license_key') . '">', '</a>');
        }
    }

    //Add an Advanced section
    $trp_addons_listing->section_header = array( 'title' => __('Advanced Add-ons', 'translatepress-multilingual' ), 'description' => __('These addons extend your translation plugin and are available in the Developer, Business and Personal plans.', 'translatepress-multilingual')  );
    $trp_addons_listing->section_versions = array( 'TranslatePress - Dev', 'TranslatePress - Personal', 'TranslatePress - Business', 'TranslatePress - Developer' );

    $seo_pack_name = __('SEO Pack', 'translatepress-multilingual');
    $option = get_option( 'trp_advanced_settings', true );
    if ( isset( $option['load_legacy_seo_pack'] ) && $option['load_legacy_seo_pack'] === 'yes' ){
        $seo_pack_name = __('SEO Pack (Legacy)', 'translatepress-multilingual');
    }

    $trp_addons_listing->items = array(
        array(  'slug' => 'tp-add-on-seo-pack/tp-seo-pack.php',
            'type' => 'add-on',
            'name' => $seo_pack_name,
            'description' => __( 'SEO support for page slug, page title, description and facebook and twitter social graph information. The HTML lang attribute is properly set.', 'translatepress-multilingual' ),
            'icon' => 'seo_icon_translatepress_addon_page.png',
            'doc_url' => 'https://translatepress.com/docs/addons/seo-pack/?utm_source=wpbackend&utm_medium=clientsite&utm_content=add-on-page&utm_campaign=TRP',
        ),
        array(  'slug' => 'tp-add-on-extra-languages/tp-extra-languages.php',
            'type' => 'add-on',
            'name' => __( 'Multiple Languages', 'translatepress-multilingual' ),
            'description' => __( 'Add as many languages as you need for your project to go global. Publish your language only when all your translations are done.', 'translatepress-multilingual' ),
            'icon' => 'multiple_lang_addon_page.png',
            'doc_url' => 'https://translatepress.com/docs/addons/multiple-languages/?utm_source=wpbackend&utm_medium=clientsite&utm_content=add-on-page&utm_campaign=TRP',
        ),
    );
    $trp_addons_listing->add_section();

    //Add Pro Section
    $trp_addons_listing->section_header = array( 'title' => __('Pro Add-ons', 'translatepress-multilingual' ), 'description' => __('These addons extend your translation plugin and are available in the Business and Developer plans.', 'translatepress-multilingual')  );
    $trp_addons_listing->section_versions = array( 'TranslatePress - Dev', 'TranslatePress - Business', 'TranslatePress - Developer' );
    $trp_addons_listing->items = array(
        array(  'slug' => 'tp-add-on-deepl/index.php',
            'type' => 'add-on',
            'name' => __( 'DeepL Automatic Translation', 'translatepress-multilingual' ),
            'description' => __( 'Automatically translate your website through the DeepL API.', 'translatepress-multilingual' ),
            'icon' => 'deepl-add-on-page.png',
            'doc_url' => 'https://translatepress.com/docs/addons/deepl-automatic-translation/?utm_source=wpbackend&utm_medium=clientsite&utm_content=add-on-page&utm_campaign=TRP',
        ),
        array(  'slug' => 'tp-add-on-automatic-language-detection/tp-automatic-language-detection.php',
            'type' => 'add-on',
            'name' => __( 'Automatic User Language Detection', 'translatepress-multilingual' ),
            'description' => __( 'Prompts visitors to switch to their preferred language based on their browser settings or IP address and remembers the last visited language.', 'translatepress-multilingual' ),
            'icon' => 'automatic_user_lang_detection_addon_page.png',
            'doc_url' => 'https://translatepress.com/docs/addons/automatic-user-language-detection/?utm_source=wpbackend&utm_medium=clientsite&utm_content=add-on-page&utm_campaign=TRP',
        ),
        array(  'slug' => 'tp-add-on-translator-accounts/index.php',
            'type' => 'add-on',
            'name' => __( 'Translator Accounts', 'translatepress-multilingual' ),
            'description' => __( 'Create translator accounts for new users or allow existing users that are not administrators to translate your website.', 'translatepress-multilingual' ),
            'icon' => 'translator_accounts_addon_page.png',
            'doc_url' => 'https://translatepress.com/docs/addons/translator-accounts/?utm_source=wpbackend&utm_medium=clientsite&utm_content=add-on-page&utm_campaign=TRP',
        ),
        array(  'slug' => 'tp-add-on-browse-as-other-roles/tp-browse-as-other-role.php',
            'type' => 'add-on',
            'name' => __( 'Browse As User Role', 'translatepress-multilingual' ),
            'description' => __( 'Navigate your website just like a particular user role would. Really useful for dynamic content or hidden content that appears for particular users.', 'translatepress-multilingual' ),
            'icon' => 'browse_as_user_role_addon_page.png',
            'doc_url' => 'https://translatepress.com/docs/addons/browse-as-role/?utm_source=wpbackend&utm_medium=clientsite&utm_content=add-on-page&utm_campaign=TRP',
        ),
        array(  'slug' => 'tp-add-on-navigation-based-on-language/tp-navigation-based-on-language.php',
            'type' => 'add-on',
            'name' => __( 'Navigation Based on Language', 'translatepress-multilingual' ),
            'description' => __( 'Configure different menu items for different languages.', 'translatepress-multilingual' ),
            'icon' => 'navigation_based_on_lang_addon_page.png',
            'doc_url' => 'https://translatepress.com/docs/addons/navigate-based-language/?utm_source=wpbackend&utm_medium=clientsite&utm_content=add-on-page&utm_campaign=TRP',
        ),
    );
    $trp_addons_listing->add_section();


    //Add Recommended Plugins
    $trp_addons_listing->section_header = array( 'title' => __('Recommended Plugins', 'translatepress-multilingual' ), 'description' => __('A short list of plugins you can use to extend your website.', 'translatepress-multilingual')  );
    $trp_addons_listing->section_versions = array( 'TranslatePress - Dev', 'TranslatePress - Personal', 'TranslatePress - Business', 'TranslatePress - Developer', 'TranslatePress - Multilingual' );
    $trp_addons_listing->items = array(
        array(  'slug' => 'profile-builder/index.php',
            'short-slug' => 'pb',
            'type' => 'plugin',
            'name' => __( 'Profile Builder', 'translatepress-multilingual' ),
            'description' => __( 'Capture more user information on the registration form with the help of Profile Builder\'s custom user profile fields and/or add an Email Confirmation process to verify your customers accounts.', 'translatepress-multilingual' ),
            'icon' => 'pb_logo.jpg',
            'doc_url' => 'https://www.cozmoslabs.com/wordpress-profile-builder/?utm_source=tpbackend&utm_medium=clientsite&utm_content=tp-addons-page&utm_campaign=TPPB',
            'disabled' => $plugin_settings['pb']['disabled'],
            'install_button' =>  $plugin_settings['pb']['install_button'],
            'action' => $plugin_settings['pb']['action']
        ),
        array(  'slug' => 'paid-member-subscriptions/index.php',
            'short-slug' => 'pms',
            'type' => 'plugin',
            'name' => __( 'Paid Member Subscriptions', 'translatepress-multilingual' ),
            'description' => __( 'Accept user payments, create subscription plans and restrict content on your membership site.', 'translatepress-multilingual' ),
            'icon' => 'pms_logo.jpg',
            'doc_url' => 'https://www.cozmoslabs.com/wordpress-paid-member-subscriptions/?utm_source=tpbackend&utm_medium=clientsite&utm_content=tp-addons-page&utm_campaign=TPPMS',
            'disabled' => $plugin_settings['pms']['disabled'],
            'install_button' =>  $plugin_settings['pms']['install_button'],
            'action' => $plugin_settings['pms']['action']
        ),
        array( 'slug' => 'wp-webhooks/wp-webhooks.php',
            'short-slug' => 'wha',
            'type' => 'plugin',
            'name' => __( 'WP Webhooks Automator', 'translatepress-multilingual' ),
            'description' => __( 'Create no-code automations and workflows on your WordPress site. Easily connect your plugins, sites and apps together.', 'translatepress-multilingual' ),
            'icon' => 'wha_logo.png',
            'doc_url' => 'https://wp-webhooks.com/integrations/?utm_source=tpbackend&utm_medium=clientsite&utm_content=tp-addons-page&utm_campaign=TPWPW',
            'disabled' => $plugin_settings['wha']['disabled'],
            'install_button' =>  $plugin_settings['wha']['install_button'],
            'action' => $plugin_settings['wha']['action']
        )
    );
    $trp_addons_listing->add_section();


    //Display the whole listing
    $trp_addons_listing->display_addons();

    ?>


</div>
