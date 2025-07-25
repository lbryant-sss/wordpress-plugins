<?php

namespace Give\Donations\Actions;

use Give\Framework\Support\Facades\Scripts\ScriptAsset;
use Give\Helpers\Language;

/**
 * @since 4.6.0
 */
class LoadDonationDetailsAssets
{
    /**
     * @since 4.6.0
     */
    public function __invoke()
    {
        $handleName = 'givewp-admin-donation-details';
        $scriptAsset = ScriptAsset::get(GIVE_PLUGIN_DIR . 'build/donationDetails.asset.php');

        wp_enqueue_editor();

        wp_register_script(
            $handleName,
            GIVE_PLUGIN_URL . 'build/donationDetails.js',
            $scriptAsset['dependencies'],
            $scriptAsset['version'],
            true
        );

        wp_enqueue_script($handleName);

        Language::setScriptTranslations($handleName);

        wp_enqueue_style('givewp-design-system-foundation');
        wp_enqueue_style(
            $handleName,
            GIVE_PLUGIN_URL . 'build/donationDetails.css',
            /** @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-components/#usage */
            ['wp-components'],
            $scriptAsset['version']
        );
    }
}
