<?php

$title = get_option( 'wooccm_order_upload_files_title', esc_html__( 'Uploaded files', 'woocommerce-checkout-manager' ) );
?>

<div class="wooccm_customer_attachments_wrapper">
	<h2 class="woocommerce-order-details__title">
		<?php esc_html_e( $title ); ?>
	</h2>
	<table class="woocommerce_order_items shop_table" style="width: 100% !important;">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Field', 'woocommerce-checkout-manager' ); ?></th>
				<th><?php esc_html_e( 'File', 'woocommerce-checkout-manager' ); ?></th>
				<th><?php esc_html_e( 'Filename', 'woocommerce-checkout-manager' ); ?></th>
				<th><?php esc_html_e( 'Dimensions', 'woocommerce-checkout-manager' ); ?></th>
				<th><?php esc_html_e( 'Extension', 'woocommerce-checkout-manager' ); ?></th>
				<th class="column-actions"><?php esc_html_e( 'Actions', 'woocommerce-checkout-manager' ); ?></th>
			</tr>
		</thead>
		<tbody class="product_images">
			<?php
			$attachment_ids_array = array();
			if ( ! empty( $attachments ) ) :
				foreach ( $attachments as $key => $attachment ) :
					$image_attributes       = wp_get_attachment_url( $attachment['attachment_id'] );
					$is_image               = wp_attachment_is_image( $attachment['attachment_id'] );
					$filename               = basename( $image_attributes );
					$wp_filetype            = wp_check_filetype( $filename );
					$attachment_ids_array[] = $attachment['attachment_id'];
					?>
					<tr class="image">
						<td><?php echo esc_html( $attachment['field_label'] ); ?></td>
						<td><?php echo wp_get_attachment_link( $attachment['attachment_id'], '', false, false, wp_get_attachment_image( $attachment['attachment_id'], array( 75, 75 ), false ) ); ?></td>
						<td><?php echo wp_get_attachment_link( $attachment['attachment_id'], '', false, false, preg_replace( '/\.[^.]+$/', '', $filename ) ); ?></td>
						<td>
							<?php
							if ( $is_image ) {

								$sizes = wp_get_attachment_image_src( $attachment['attachment_id'], 'full-size' );

								echo esc_html( $sizes[1] . 'x' . $sizes[2] );
							}
							?>
						</td>
						<td>
							<?php echo esc_html( strtoupper( $wp_filetype['ext'] ) ); ?>
						</td>
						<td class="column-actions">
							<a class="button wooccm_delete_attachment" data-input_id="<?php echo esc_attr( $key ); ?>" data-attachment_id="<?php echo esc_attr( $attachment['attachment_id'] ); ?>" data-tip="<?php esc_html_e( 'Delete', 'woocommerce-checkout-manager' ); ?>"><?php esc_html_e( 'Delete', 'woocommerce-checkout-manager' ); ?></a>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="6" style="text-align:left;"><?php esc_html_e( 'No files have been uploaded to this customer.', 'woocommerce-checkout-manager' ); ?></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
	<input type="hidden" id="delete_attachments_ids" name="delete_attachments_ids" value="<?php echo esc_attr( implode( ',', $attachment_ids_array ) ); ?>" />
	<input type="hidden" id="all_attachments_ids" name="all_attachments_ids" value="<?php echo esc_attr( implode( ',', $attachment_ids_array ) ); ?>" />
</div>
