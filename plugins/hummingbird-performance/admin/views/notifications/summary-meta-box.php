<?php
/**
 * Notifications summary meta box.
 *
 * @since 3.1.1
 * @package Hummingbird
 *
 * @var int    $active_notifications  Number of active notifications.
 * @var string $next_notification     Next scheduled notification.
 */

use Hummingbird\Core\Hub_Connector;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$branded_image = apply_filters( 'wpmudev_branding_hero_image', '' );
?>

<?php if ( $branded_image ) : ?>
	<div class="sui-summary-image-space" aria-hidden="true" style="background-image: url('<?php echo esc_url( $branded_image ); ?>')"></div>
<?php else : ?>
	<div class="sui-summary-image-space" aria-hidden="true"></div>
<?php endif; ?>
<div class="sui-summary-segment">
	<div class="sui-summary-details">
		<span class="sui-summary-large">0</span>
		<span class="sui-summary-sub">
			<?php esc_html_e( 'Active notifications', 'wphb' ); ?>
		</span>
	</div>
</div>
<?php if ( ! is_multisite() || is_network_admin() ) : ?>
	<div class="sui-summary-segment">
		<ul class="sui-list">
			<li>
				<span class="sui-list-label"><?php esc_html_e( 'Next scheduled reporting', 'wphb' ); ?></span>
				<a href="<?php echo esc_url( Hub_Connector::get_connect_site_url( 'wphb-notifications' ) ); ?>" class="sui-button sui-button-blue">
					<?php esc_html_e( 'CONNECT SITE', 'wphb' ); ?>
				</a>
			</li>
		</ul>
	</div>
<?php endif; ?>
