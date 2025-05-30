<?php

namespace CustomFacebookFeed\Integrations;

use CustomFacebookFeed\CFF_Utils;
use CustomFacebookFeed\Builder\CFF_Db;
use CustomFacebookFeed\Builder\CFF_Feed_Builder;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * CFF_Integration
 * Common funcions for Elementor/Divi/Gutenberg
 *
 * @since 4.3
 *
 * @return array
 */

class CFF_Integration
{
    /**
     * Get Widget/Module/Block Info
     *
     * @since 4.3
     *
     * @return array
     */
    public static function get_widget_info()
    {
        return [
            'plugin'                            => 'facebook',
            'cta_header'                        => __('Get started with your first feed from your<br/>Facebook Page or Group', 'custom-facebook-feed'),
            'cta_header2'                       => __('Select a Facebook feed to embed', 'custom-facebook-feed'),
            'cta_description_free'              => sprintf(
                __('You can display feeds of Facebook photos, videos, albums, events and more using the %sPro version%s ', 'custom-facebook-feed'),
                '<a  class="sb-elementor-cta-link" href="https://smashballoon.com/custom-facebook-feed/?utm_campaign=facebook-free&utm_source=elementor&utm_medium=widget&utm_content=proversion">',
                '</a>'
            ),
            'cta_description_pro'               => sprintf(
                __('You can also add Instagram, Twitter, and YouTube posts into your feed using our %sSocial Wall plugin%s ', 'custom-facebook-feed'),
                '<a  class="sb-elementor-cta-link" href="https://smashballoon.com/social-wall/?utm_campaign=facebook-pro&utm_source=elementor&utm_medium=widget&utm_content=socialwall">',
                '</a>'
            ),
            'plugins'                           => CFF_Feed_Builder::get_smashballoon_plugins_info()

        ];
    }


    /**
     * Widget CTA
     *
     * @since 4.3
     *
     * @return HTML
     */
    public static function get_widget_cta($type = 'dropdown')
    {
        $widget_cta_html = '';
        $feeds_list = CFF_Db::elementor_feeds_query();
        ob_start();
        self::get_widget_cta_html($feeds_list, $type);
        $widget_cta_html .= ob_get_contents();
        ob_get_clean();
        return $widget_cta_html;
    }

    public static function get_widget_cta_html($feeds_list, $type)
    {
        $info = self::get_widget_info();
        $feeds_exist = is_array($feeds_list) && sizeof($feeds_list) > 0;
        ?>
		<div class="sb-elementor-cta">
			<div class="sb-elementor-cta-img-ctn">
				<div class="sb-elementor-cta-img">
					<span><?php echo $info['plugins'][$info['plugin']]['icon']; ?></span>
					<svg class="sb-elementor-cta-logo" width="31" height="39" viewBox="0 0 31 39" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.62525 18.4447C1.62525 26.7883 6.60827 33.9305 13.3915 35.171L12.9954 36.4252L12.5937 37.6973L13.923 37.5843L18.4105 37.2026L20.0997 37.0589L19.0261 35.7468L18.4015 34.9834C24.7525 33.3286 29.3269 26.4321 29.3269 18.4447C29.3269 9.29016 23.2952 1.53113 15.4774 1.53113C7.65975 1.53113 1.62525 9.2899 1.62525 18.4447Z" fill="#FE544F" stroke="white" stroke-width="1.78661"/><path fill-rule="evenodd" clip-rule="evenodd" d="M18.5669 8.05676L19.1904 14.4905L25.6512 14.6761L20.9776 19.0216L24.6689 24.3606L18.4503 23.1916L16.5651 29.4104L13.7026 23.8415L7.92284 26.4899L10.1462 20.5199L4.50931 17.6767L10.5435 15.7361L8.8784 9.79176L14.5871 13.0464L18.5669 8.05676Z" fill="white"/></svg>
				</div>
			</div>
			<h3 class="sb-elementor-cta-heading"><?php echo $feeds_exist ? $info['cta_header2'] : $info['cta_header'] ?></h3>

			<?php if ($feeds_exist) : ?>
				<div class="sb-elementor-cta-selector">

					<?php if ($type == 'dropdown') : ?>
						<select class="sb-elementor-cta-feedselector">
							<?php foreach ($feeds_list as $feed_id => $feed_name) : ?>
								<option value="<?php echo $feed_id ?>"><?php echo $feed_name ?></option>
							<?php endforeach ?>
						</select>
					<?php elseif ($type == 'button') : ?>
						<a href="<?php echo esc_url(admin_url('admin.php?page=cff-feed-builder')) ?>" rel="noopener noreferrer" class="sb-elementor-cta-btn sb-elementor-cta-link">
							<?php echo esc_html__('Create Facebook Feed', 'custom-facebook-feed'); ?>
						</a>
					<?php endif; ?>

					<span>
						<?php
                            echo esc_html__('Or create a Feed for', 'custom-facebook-feed');
							unset($info['plugins'][$info['plugin']]);
							foreach ($info['plugins'] as $name => $plugin) :
           				 ?>
							<a class="sb-elementor-cta-link" href="<?php echo esc_url($plugin['link']) ?>" target="_blank"><?php echo $name ?></a>
						<?php endforeach ?>
					</span>
				</div>
			<?php else : ?>
				<a href="<?php echo esc_url(admin_url('admin.php?page=cff-feed-builder')) ?>" class="sb-elementor-cta-btn" rel="noopener noreferrer" target="_blank">
					<?php echo esc_html__('Create', 'custom-facebook-feed') . ' ' . $info['plugin'] . ' ' . esc_html__('Feed', 'custom-facebook-feed') ?>
				</a>
			<?php endif; ?>

			<div class="sb-elementor-cta-desc">
				<strong><?php echo esc_html__('Did you Know?', 'custom-facebook-feed') ?></strong>
				<span>
					<?php
                        echo CFF_Utils::cff_is_pro_version()
                        ? $info['cta_description_pro']
                        : $info['cta_description_free'];
        ?>
				</span>
			</div>
		</div>
		<?php
    }
}
