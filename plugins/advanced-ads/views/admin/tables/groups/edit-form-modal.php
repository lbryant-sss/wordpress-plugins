<?php
/**
 * Advanced Ads – form to edit ad groups in the admin
 *
 * @package AdvancedAds
 * @author  Advanced Ads <info@wpadvancedads.com>
 *
 * @var AdvancedAds\Abstracts\Group $group Group instance.
 * @var string                      $this  ->type_error Contains an error message if the group type is missing.
 */

use AdvancedAds\Admin\Upgrades;
use AdvancedAds\Utilities\WordPress;

$group_types = wp_advads_get_group_types();
$group_id    = $group->get_id();
?>
<div class="advads-ad-group-form">
	<form name="update-group" method="post">
		<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'advads-update-group' ) ); ?>">
		<?php
		echo $group->get_hints_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		ob_start();
		?>
		<input type="hidden" class="advads-group-id" name="advads-groups[<?php echo esc_attr( $group_id ); ?>][id]" value="<?php echo esc_attr( $group_id ); ?>"/>
		<input type="text" name="advads-groups[<?php echo esc_attr( $group_id ); ?>][name]" value="<?php echo esc_attr( $group->get_name() ); ?>"/>
		<?php
		$option_content = ob_get_clean();

		WordPress::render_option(
			'group-name static',
			__( 'Name', 'advanced-ads' ),
			$option_content
		);

		ob_start();
		?>
		<div class="advads-ad-group-type">
			<?php if ( $this->type_error ) : ?>
				<p class="advads-notice-inline advads-error"><?php echo esc_html( $this->type_error ); ?></p>
				<?php
			endif;
			foreach ( $group_types as $group_type ) :
				if ( $group_type->is_premium() ) {
					continue;
				}
				?>
				<label title="<?php echo esc_html( $group_type->get_description() ); ?>">
					<input type="radio" name="advads-groups[<?php echo esc_attr( $group_id ); ?>][type]" value="<?php echo esc_attr( $group_type->get_id() ); ?>" <?php checked( $group->get_type(), $group_type->get_id() ); ?>/>
					<?php echo esc_html( $group_type->get_title() ); ?>
				</label>
			<?php endforeach; ?>

			<?php
			foreach ( $group_types as $group_type ) :
				if ( ! $group_type->is_premium() ) {
					continue;
				}
				?>
				<label title="<?php echo esc_html( $group_type->get_description() ); ?>">
					<input type="radio" name="advads-groups[<?php echo esc_attr( $group_id ); ?>][type]" value="<?php echo esc_attr( $group_type->get_id() ); ?>" disabled/>
					<?php echo esc_html( $group_type->get_title() ); ?>
				</label>
			<?php endforeach; ?>

			<?php if ( wp_advads_get_group_type_manager()->has_premium() ) : ?>
				<label>
					<?php Upgrades::upgrade_link( __( 'Get all group types with All Access', 'advanced-ads' ), 'https://wpadvancedads.com/add-ons/all-access/', 'upgrades-pro-groups' ); ?>
				</label>
			<?php endif; ?>
		</div>
		<?php
		$option_content = ob_get_clean();

		WordPress::render_option(
			'group-type static',
			esc_attr__( 'Type', 'advanced-ads' ),
			$option_content
		);

		ob_start();
		?>
		<select name="advads-groups[<?php echo esc_attr( $group_id ); ?>][ad_count]">
			<?php
			$sorted_ads_count = count( $group->get_sorted_ads() );
			$max_ads          = $sorted_ads_count >= 10 ? $sorted_ads_count + 2 : 10;
			for ( $i = 1; $i <= $max_ads; $i++ ) :
				?>
				<option <?php selected( $group->get_ad_count(), $i ); ?>><?php echo esc_html( $i ); ?></option>
				<?php
			endfor;
			?>
			<option <?php selected( $group->get_ad_count(), 'all' ); ?> value="all"><?php echo esc_attr_x( 'all', 'option to display all ads in an ad groups', 'advanced-ads' ); ?></option>
		</select>
		<?php
		$option_content = ob_get_clean();

		WordPress::render_option(
			'group-number advads-group-type-default advads-group-type-ordered',
			esc_attr__( 'Visible ads', 'advanced-ads' ),
			$option_content,
			esc_attr__( 'Number of ads that are visible at the same time', 'advanced-ads' )
		);

		do_action( 'advanced-ads-group-form-options', $group );

		ob_start();
		require ADVADS_ABSPATH . 'views/admin/tables/groups/list-row-option-ads.php';
		$option_content = ob_get_clean();
		WordPress::render_option(
			'group-ads static',
			esc_attr__( 'Ads', 'advanced-ads' ),
			$option_content
		);
		?>
	</form>
</div>
