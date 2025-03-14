<?php
/**
 * The Template for displaying admin section group for style this plugin.
 *
 * @since             1.0.0
 * @package           TInvWishlist\Admin\Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<tbody <?php echo $extra; // WPCS: xss ok. ?>>
<tr class="tinvwl-bodies-border">
	<td>
		<div class="tinvwl-inner"></div>
	</td>
	<td class="tinvwl-info w-bg-grey">
		<div class="tinvwl-inner"></div>
	</td>
</tr>
<tr>
	<td>
		<div class="tinvwl-inner">
			<?php if ( $show_names && $title ) : ?>
				<h2><?php echo $title; // WPCS: xss ok. ?></h2>
			<?php endif; ?>
		</div>
	</td>
	<?php if ( $show_helper ) : ?>
		<td class="tinvwl-info w-bg-grey" rowspan="2">
			<?php if ( ! empty( $desc ) ) {
				TInvWL_View::view( 'section-infoblock', array( 'desc' => $desc ), '' );
			} ?>
		</td>
	<?php endif; ?>
</tr>
<tr>
	<td>
		<div class="tinvwl-inner">
			<div class="form-horizontal">
				<div class="form-group">
					<?php echo $fields; // WPCS: xss ok. ?>
				</div>
			</div>
		</div>
	</td>
</tr>
</tbody>
