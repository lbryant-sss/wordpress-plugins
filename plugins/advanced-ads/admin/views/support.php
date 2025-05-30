<?php
/**
 * The view for the support page
 *
 * @package AdvancedAds
 * @author  Advanced Ads <info@wpadvancedads.com>
 */

use AdvancedAds\Utilities\Conditional;

?>
<div class="wrap">
	<?php if ( Advanced_Ads_Admin_Notices::get_instance()->can_display( 'nl_first_steps' ) && Conditional::user_can_subscribe( 'nl_first_steps' ) ) : ?>
	<div class="notice notice-info advads-admin-notice inline is-dismissible" data-notice="nl_first_steps">
		<p>
			<?php
			printf(
				/* translators: %s: number of free add-ons. */
				esc_html__(
					'Subscribe to our free First Steps email course, receive our newsletter for periodic tutorials, and get %s for Advanced Ads.',
					'advanced-ads'
				),
				'<strong>' . esc_html__( '2 free add-ons', 'advanced-ads' ) . '</strong>'
			);
			?>
			<button type="button" class="button-primary advads-notices-button-subscribe" data-notice="nl_first_steps"><?php esc_html_e( 'Subscribe me now', 'advanced-ads' ); ?></button>
		</p>
	</div>
	<?php endif; ?>

	<p><?php esc_html_e( 'Please fix the red highlighted issues on this page or try to understand their consequences before contacting support.', 'advanced-ads' ); ?></p>
	<h2><?php esc_html_e( 'Possible Issues', 'advanced-ads' ); ?></h2>
	<ul>
	<li><a href="https://wpadvancedads.com/manual/ads-not-showing-up/?utm_source=advanced-ads&utm_medium=link&utm_campaign=support"><?php esc_html_e( 'Ads not showing up', 'advanced-ads' ); ?></a></li>
	<li><a href="https://wpadvancedads.com/manual/purchase-licenses/?utm_source=advanced-ads&utm_medium=link&utm_campaign=support"><?php esc_html_e( 'Purchase & Licenses', 'advanced-ads' ); ?></a></li>
	<li><a href="https://wpadvancedads.com/manual/issues-after-updates/?utm_source=advanced-ads&utm_medium=link&utm_campaign=support"><?php esc_html_e( 'Issues after updating', 'advanced-ads' ); ?></a></li>
	<li><a href="https://wpadvancedads.com/manual-category/troubleshooting/?utm_source=advanced-ads&utm_medium=link&utm_campaign=support"><?php esc_html_e( 'General Issues', 'advanced-ads' ); ?></a></li>
	<li><a href="https://wpadvancedads.com/manual-category/add-on-issues/?utm_source=advanced-ads&utm_medium=link&utm_campaign=support"><?php esc_html_e( 'Issues with Add-Ons', 'advanced-ads' ); ?></a></li>
	</ul>
	<p><?php esc_html_e( 'Use the following form to search for solutions in the manual on wpadvancedads.com', 'advanced-ads' ); ?></p>
	<form action="https://wpadvancedads.com/?utm_source=advanced-ads&utm_medium=link&utm_campaign=support-form" method="get" class="advads-support-form">
	<input type="search" name="s"/>
	<input type="submit" class="button button-primary" value="<?php esc_html_e( 'search', 'advanced-ads' ); ?>">
	</form>
	<?php if ( Advanced_Ads_Admin_Licenses::any_license_valid() ) : ?>
	<p>
		<?php
		printf(
			wp_kses(
				/* translators: %s is a URL. */
				__( 'Take a look at more common issues or contact us directly through the <a href="%s" target="_blank">support page</a>.', 'advanced-ads' ),
				[
					'a' => [
						'href'   => [],
						'target' => [],
					],
				]
			),
			'https://wpadvancedads.com/support/?utm_source=advanced-ads&utm_medium=link&utm_campaign=support'
		);
		?>
	</p>
	<?php else : ?>
	<p>
		<?php
		printf(
			wp_kses(
				/* translators: %s is a URL. */
				__( 'Upgrade to any premium add-on and get <strong>priority email support</strong> or reach out through the <a href="%s" target="_blank">support forum</a> for individual help.', 'advanced-ads' ),
				[
					'a' => [
						'href'   => [],
						'target' => [],
					],
				]
			),
			'https://wordpress.org/support/plugin/advanced-ads'
		);
		?>
	</p>
	<?php endif; ?>
<?php $constants = Advanced_Ads_Checks::get_defined_constants(); ?>
<?php if ( $constants ) : ?>
	<h2><?php esc_html_e( 'Advanced Ads related constants enabled', 'advanced-ads' ); ?></h2>
	<ul>
		<li>
	<?php
	echo wp_kses(
		implode( '</li><li>', $constants ),
		[ 'li' => [] ]
	);
	?>
		</li>
	</ul>
<?php endif; ?>
</div>
<?php AdvancedAds\Modules\ProductExperimentationFramework\Module::get_instance()->render( 'support' ); ?>
