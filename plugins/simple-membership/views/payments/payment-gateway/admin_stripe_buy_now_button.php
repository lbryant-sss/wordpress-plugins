<?php
/* * ***************************************************************
 * Render the new Stripe Buy Now payment button creation interface
 * ************************************************************** */
add_action( 'swpm_create_new_button_for_stripe_buy_now', 'swpm_create_new_stripe_buy_now_button' );

function swpm_create_new_stripe_buy_now_button() {
    $button_type = isset($_REQUEST['button_type']) ? sanitize_text_field($_REQUEST['button_type']) : '';

	//Test for PHP v5.6.0 or show error and don't show the remaining interface.
	if ( version_compare( PHP_VERSION, '5.6.0' ) < 0 ) {
		//This server can't handle Stripe library
		echo '<div class="swpm-red-box">';
		echo '<p>'.__('The Stripe payment gateway libary requires at least PHP 5.6.0. Your server is using a very old version of PHP that Stripe does not support.', 'simple-membership').'</p>';
		echo '<p>'. __('Request your hosting provider to upgrade your PHP to a more recent version then you will be able to use the Stripe gateway.', 'simple-membership').'<p>';
		echo '</div>';
		return;
	}
	?>

<div class="swpm-orange-box">
	<?php _e('View the', 'simple-membership') ?> <a target="_blank" href="https://simple-membership-plugin.com/create-stripe-buy-now-button-for-membership-payment/"><?php _e('documentation', 'simple-membership') ?></a>&nbsp;
	<?php _e('to learn how to create a Stripe Buy Now payment button and use it.
</div>', 'simple-membership') ?>

<div class="postbox">
	<h3 class="hndle"><label for="title"><?php _e( 'Stripe Buy Now Button Configuration' ); ?></label></h3>
	<div class="inside">

		<form id="stripe_button_config_form" method="post">
			<input type="hidden" name="button_type" value="<?php echo esc_attr( $button_type ); ?>">
			<input type="hidden" name="swpm_button_type_selected" value="1">

			<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">

				<tr valign="top">
					<th scope="row"><?php _e( 'Button Title' , 'simple-membership'); ?></th>
					<td>
						<input type="text" size="50" name="button_name" value="" required />
						<p class="description"><?php _e('Give this membership payment button a name. Example: Gold membership payment', 'simple-membership') ?></p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e( 'Membership Level', 'simple-membership' ); ?></th>
					<td>
						<select id="membership_level_id" name="membership_level_id">
							<?php echo SwpmUtils::membership_level_dropdown(); ?>
						</select>
						<p class="description"><?php _e('Select the membership level this payment button is for.', 'simple-membership') ?></p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e( 'Payment Amount' , 'simple-membership'); ?></th>
					<td>
						<input type="text" size="6" name="payment_amount" value="" required />
						<p class="description"><?php _e('Enter payment amount. Example values: 10.00 or 19.50 or 299.95 etc (do not put currency symbol).', 'simple-membership') ?></p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e( 'Payment Currency' , 'simple-membership'); ?></th>
					<td>
						<select id="payment_currency" name="payment_currency">
							<option selected="selected" value="USD">US Dollars ($)</option>
							<option value="EUR">Euros (€)</option>
							<option value="GBP">Pounds Sterling (£)</option>
							<option value="AUD">Australian Dollars ($)</option>
							<option value="BRL">Brazilian Real (R$)</option>
							<option value="CAD">Canadian Dollars ($)</option>
							<option value="CNY">Chinese Yuan</option>
							<option value="CZK">Czech Koruna</option>
							<option value="DKK">Danish Krone</option>
							<option value="HKD">Hong Kong Dollar ($)</option>
							<option value="HUF">Hungarian Forint</option>
							<option value="INR">Indian Rupee</option>
							<option value="IDR">Indonesia Rupiah</option>
							<option value="ILS">Israeli Shekel</option>
							<option value="JPY">Japanese Yen (¥)</option>
							<option value="MYR">Malaysian Ringgits</option>
							<option value="MXN">Mexican Peso ($)</option>
							<option value="NZD">New Zealand Dollar ($)</option>
							<option value="NOK">Norwegian Krone</option>
							<option value="PHP">Philippine Pesos</option>
							<option value="PLN">Polish Zloty</option>
							<option value="SGD">Singapore Dollar ($)</option>
							<option value="ZAR">South African Rand (R)</option>
							<option value="KRW">South Korean Won</option>
							<option value="SEK">Swedish Krona</option>
							<option value="CHF">Swiss Franc</option>
							<option value="TWD">Taiwan New Dollars</option>
							<option value="THB">Thai Baht</option>
							<option value="TRY">Turkish Lira</option>
							<option value="VND">Vietnamese Dong</option>
						</select>
						<p class="description"><?php _e('Select the currency for this payment button.', 'simple-membership') ?></p>
					</td>
				</tr>

				<tr valign="top">
					<th colspan="2">
						<div class="swpm-grey-box"><?php _e( 'Stripe API keys. You can get this from your Stripe account.', 'simple-membership' ); ?></div>
					</th>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e( 'Use Global API Keys Settings' , 'simple-membership'); ?></th>
					<td>
						<input type="checkbox" name="stripe_use_global_keys" value="1" checked />
						<p class="description"><?php _e( 'Use API keys from <a href="admin.php?page=simple_wp_membership_payments&tab=payment_settings&subtab=ps_stripe" target="_blank">Payment Settings</a> tab.' , 'simple-membership'); ?></p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e( 'Test Publishable Key', 'simple-membership' ); ?></th>
					<td>
						<input type="text" size="50" name="stripe_test_publishable_key" value="" required />
						<p class="description"><?php _e('Enter your Stripe test publishable key.', 'simple-membership') ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( 'Test Secret Key' , 'simple-membership'); ?></th>
					<td>
						<input type="text" size="50" name="stripe_test_secret_key" value="" required />
						<p class="description"><?php _e('Enter your Stripe test secret key.', 'simple-membership') ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( 'Live Publishable Key' , 'simple-membership'); ?></th>
					<td>
						<input type="text" size="50" name="stripe_live_publishable_key" value="" required />
						<p class="description"><?php _e('Enter your Stripe live publishable key.', 'simple-membership') ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( 'Live Secret Key' , 'simple-membership'); ?></th>
					<td>
						<input type="text" size="50" name="stripe_live_secret_key" value="" required />
						<p class="description"><?php _e('Enter your Stripe live secret key.', 'simple-membership') ?></p>
					</td>
				</tr>

				<tr valign="top">
					<th colspan="2">
						<div class="swpm-grey-box"><?php _e( 'The following details are optional.', 'simple-membership' ); ?></div>
					</th>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e( 'Collect Customer Address', 'simple-membership'); ?></th>
					<td>
						<input type="checkbox" name="collect_address" value="1" />
						<p class="description"><?php _e('Enable this option if you want to collect customer address during Stripe checkout.', 'simple-membership', 'simple-membership') ?></p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e( 'Return URL' , 'simple-membership'); ?></th>
					<td>
						<input type="text" size="100" name="return_url" value="" />
						<p class="description"><?php _e('This is the URL the user will be redirected to after a successful payment. Enter the URL of your Thank You page here.', 'simple-membership') ?></p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e( 'Button Image URL' , 'simple-membership'); ?></th>
					<td>
						<input type="text" size="100" name="button_image_url" value="" />
						<p class="description"><?php _e('If you want to customize the look of the button using an image then enter the URL of the image.', 'simple-membership') ?></p>
					</td>
				</tr>

			</table>

			<script>
			var swpmInputsArr = ['stripe_test_publishable_key', 'stripe_test_secret_key', 'stripe_live_publishable_key', 'stripe_live_secret_key'];
			jQuery('input[name="stripe_use_global_keys"').change(function() {
				var checked = jQuery(this).prop('checked');
				jQuery.each(swpmInputsArr, function(index, el) {
					jQuery('input[name="' + el + '"]').prop('disabled', checked);
				});
			});
			jQuery('input[name="stripe_use_global_keys"').trigger('change');
			</script>

			<p class="submit">
				<?php wp_nonce_field( 'swpm_admin_add_edit_stripe_buy_now_btn', 'swpm_admin_create_stripe_buy_now_btn' ); ?>
				<input type="submit" name="swpm_stripe_buy_now_save_submit" class="button-primary" value="<?php _e( 'Save Payment Data', 'simple-membership' ); ?>">
			</p>

		</form>

	</div>
</div>
	<?php
}

/*
 * Process submission and save the new Stripe Buy now payment button data
 */
add_action( 'swpm_create_new_button_process_submission', 'swpm_save_new_stripe_buy_now_button_data' );

function swpm_save_new_stripe_buy_now_button_data() {
	if ( isset( $_REQUEST['swpm_stripe_buy_now_save_submit'] ) ) {
		//This is a Stripe buy now button save event. Process the submission.
		check_admin_referer( 'swpm_admin_add_edit_stripe_buy_now_btn', 'swpm_admin_create_stripe_buy_now_btn' );

		//Save the button data
		$button_id = wp_insert_post(
			array(
				'post_title'   => sanitize_text_field( $_REQUEST['button_name'] ),
				'post_type'    => 'swpm_payment_button',
				'post_content' => '',
				'post_status'  => 'publish',
			)
		);

		$button_type = sanitize_text_field( $_REQUEST['button_type'] );
		add_post_meta( $button_id, 'button_type', $button_type );
		add_post_meta( $button_id, 'membership_level_id', sanitize_text_field( $_REQUEST['membership_level_id'] ) );
		add_post_meta( $button_id, 'payment_amount', trim( sanitize_text_field( $_REQUEST['payment_amount'] ) ) );
		add_post_meta( $button_id, 'payment_currency', sanitize_text_field( $_REQUEST['payment_currency'] ) );

		$stripe_test_secret_key = isset( $_POST['stripe_test_secret_key'] ) ? sanitize_text_field( stripslashes ( $_POST['stripe_test_secret_key'] ) ) : '';
		$stripe_test_publishable_key = isset( $_POST['stripe_test_publishable_key'] ) ? sanitize_text_field( stripslashes ( $_POST['stripe_test_publishable_key'] ) ) : '';

		if ( ! is_null( $stripe_test_secret_key ) ) {
			update_post_meta( $button_id, 'stripe_test_secret_key', trim( $stripe_test_secret_key ) );
		}

		if ( ! is_null( $stripe_test_publishable_key ) ) {
			update_post_meta( $button_id, 'stripe_test_publishable_key', trim( $stripe_test_publishable_key ) );
		}

		$stripe_live_secret_key = isset( $_POST['stripe_live_secret_key'] ) ? sanitize_text_field( stripslashes ( $_POST['stripe_live_secret_key'] ) ) : '';
		$stripe_live_publishable_key = isset( $_POST['stripe_live_publishable_key'] ) ? sanitize_text_field( stripslashes ( $_POST['stripe_live_publishable_key'] ) ) : '';

		if ( ! is_null( $stripe_live_secret_key ) ) {
			update_post_meta( $button_id, 'stripe_live_secret_key', trim( $stripe_live_secret_key ) );
		}

		if ( ! is_null( $stripe_live_publishable_key ) ) {
			update_post_meta( $button_id, 'stripe_live_publishable_key', trim( $stripe_live_publishable_key ) );
		}

		$stripe_use_global_keys = filter_input( INPUT_POST, 'stripe_use_global_keys', FILTER_SANITIZE_NUMBER_INT );
		$stripe_use_global_keys = $stripe_use_global_keys ? true : false;

		update_post_meta( $button_id, 'stripe_use_global_keys', $stripe_use_global_keys );

		add_post_meta( $button_id, 'stripe_collect_address', isset( $_POST['collect_address'] ) ? '1' : '' );

		add_post_meta( $button_id, 'return_url', trim( sanitize_text_field( $_REQUEST['return_url'] ) ) );
		add_post_meta( $button_id, 'button_image_url', esc_url( $_REQUEST['button_image_url'] ) );
		//Redirect to the edit interface of this button with $button_id
		//$url = admin_url() . 'admin.php?page=simple_wp_membership_payments&tab=edit_button&button_id=' . $button_id . '&button_type=' . $button_type;
		//Redirect to the manage payment buttons interface
		$url = admin_url() . 'admin.php?page=simple_wp_membership_payments&tab=payment_buttons';
		SwpmMiscUtils::redirect_to_url( $url );
	}
}

/* * **********************************************************************
 * End of new Stripe Buy now payment button stuff
 * ********************************************************************** */


/* * ***************************************************************
 * Render edit Stripe Buy now payment button interface
 * ************************************************************** */
add_action( 'swpm_edit_payment_button_for_stripe_buy_now', 'swpm_edit_stripe_buy_now_button' );

function swpm_edit_stripe_buy_now_button() {

	//Retrieve the payment button data and present it for editing.

	$button_id   = sanitize_text_field( $_REQUEST['button_id'] );
	$button_id   = absint( $button_id );
	$button_type = sanitize_text_field( $_REQUEST['button_type'] );

	$button = get_post( $button_id ); //Retrieve the CPT for this button

	$membership_level_id = get_post_meta( $button_id, 'membership_level_id', true );
	$payment_amount      = get_post_meta( $button_id, 'payment_amount', true );
	$payment_currency    = get_post_meta( $button_id, 'payment_currency', true );

	$stripe_test_secret_key      = get_post_meta( $button_id, 'stripe_test_secret_key', true );
	$stripe_test_publishable_key = get_post_meta( $button_id, 'stripe_test_publishable_key', true );
	$stripe_live_secret_key      = get_post_meta( $button_id, 'stripe_live_secret_key', true );
	$stripe_live_publishable_key = get_post_meta( $button_id, 'stripe_live_publishable_key', true );

	$collect_address = get_post_meta( $button_id, 'stripe_collect_address', true );
	if ( $collect_address == '1' ) {
		$collect_address = ' checked';
	} else {
		$collect_address = '';
	}

	$use_global_keys = get_post_meta( $button_id, 'stripe_use_global_keys', true );

	$use_global_keys = empty( $use_global_keys ) ? false : true;

	$return_url       = get_post_meta( $button_id, 'return_url', true );
	$button_image_url = get_post_meta( $button_id, 'button_image_url', true );
	?>
<div class="postbox">
	<h3 class="hndle"><label for="title"><?php _e( 'Stripe Buy Now Button Configuration' , 'simple-membership'); ?></label></h3>
	<div class="inside">

		<form id="stripe_button_config_form" method="post">
			<input type="hidden" name="button_type" value="<?php echo esc_attr($button_type); ?>">

			<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">

				<tr valign="top">
					<th scope="row"><?php _e( 'Button ID' , 'simple-membership'); ?></th>
					<td>
						<input type="text" size="10" name="button_id" value="<?php echo esc_attr($button_id); ?>" readonly required />
						<p class="description"><?php _e('This is the ID of this payment button. It is automatically generated for you and it cannot be changed.', 'simple-membership') ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( 'Button Title' , 'simple-membership'); ?></th>
					<td>
						<input type="text" size="50" name="button_name" value="<?php echo esc_attr($button->post_title); ?>" required />
						<p class="description"><?php _e('Give this membership payment button a name. Example: Gold membership payment', 'simple-membership') ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( 'Membership Level' , 'simple-membership'); ?></th>
					<td>
						<select id="membership_level_id" name="membership_level_id">
							<?php echo SwpmUtils::membership_level_dropdown( $membership_level_id ); ?>
						</select>
						<p class="description"><?php _e('Select the membership level this payment button is for.', 'simple-membership') ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( 'Payment Amount' , 'simple-membership'); ?></th>
					<td>
						<input type="text" size="6" name="payment_amount" value="<?php echo esc_attr($payment_amount); ?>" required />
						<p class="description"><?php _e('Enter payment amount. Example values: 10.00 or 19.50 or 299.95 etc (do not put currency symbol).', 'simple-membership') ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( 'Payment Currency', 'simple-membership' ); ?></th>
					<td>
						<select id="payment_currency" name="payment_currency">
							<option value="USD" <?php echo ( $payment_currency == 'USD' ) ? 'selected="selected"' : ''; ?>>US Dollars ($)</option>
							<option value="EUR" <?php echo ( $payment_currency == 'EUR' ) ? 'selected="selected"' : ''; ?>>Euros (€)</option>
							<option value="GBP" <?php echo ( $payment_currency == 'GBP' ) ? 'selected="selected"' : ''; ?>>Pounds Sterling (£)</option>
							<option value="AUD" <?php echo ( $payment_currency == 'AUD' ) ? 'selected="selected"' : ''; ?>>Australian Dollars ($)</option>
							<option value="BRL" <?php echo ( $payment_currency == 'BRL' ) ? 'selected="selected"' : ''; ?>>Brazilian Real (R$)</option>
							<option value="CAD" <?php echo ( $payment_currency == 'CAD' ) ? 'selected="selected"' : ''; ?>>Canadian Dollars ($)</option>
							<option value="CNY" <?php echo ( $payment_currency == 'CNY' ) ? 'selected="selected"' : ''; ?>>Chinese Yuan</option>
							<option value="CZK" <?php echo ( $payment_currency == 'CZK' ) ? 'selected="selected"' : ''; ?>>Czech Koruna</option>
							<option value="DKK" <?php echo ( $payment_currency == 'DKK' ) ? 'selected="selected"' : ''; ?>>Danish Krone</option>
							<option value="HKD" <?php echo ( $payment_currency == 'HKD' ) ? 'selected="selected"' : ''; ?>>Hong Kong Dollar ($)</option>
							<option value="HUF" <?php echo ( $payment_currency == 'HUF' ) ? 'selected="selected"' : ''; ?>>Hungarian Forint</option>
							<option value="INR" <?php echo ( $payment_currency == 'INR' ) ? 'selected="selected"' : ''; ?>>Indian Rupee</option>
							<option value="IDR" <?php echo ( $payment_currency == 'IDR' ) ? 'selected="selected"' : ''; ?>>Indonesia Rupiah</option>
							<option value="ILS" <?php echo ( $payment_currency == 'ILS' ) ? 'selected="selected"' : ''; ?>>Israeli Shekel</option>
							<option value="JPY" <?php echo ( $payment_currency == 'JPY' ) ? 'selected="selected"' : ''; ?>>Japanese Yen (¥)</option>
							<option value="MYR" <?php echo ( $payment_currency == 'MYR' ) ? 'selected="selected"' : ''; ?>>Malaysian Ringgits</option>
							<option value="MXN" <?php echo ( $payment_currency == 'MXN' ) ? 'selected="selected"' : ''; ?>>Mexican Peso ($)</option>
							<option value="NZD" <?php echo ( $payment_currency == 'NZD' ) ? 'selected="selected"' : ''; ?>>New Zealand Dollar ($)</option>
							<option value="NOK" <?php echo ( $payment_currency == 'NOK' ) ? 'selected="selected"' : ''; ?>>Norwegian Krone</option>
							<option value="PHP" <?php echo ( $payment_currency == 'PHP' ) ? 'selected="selected"' : ''; ?>>Philippine Pesos</option>
							<option value="PLN" <?php echo ( $payment_currency == 'PLN' ) ? 'selected="selected"' : ''; ?>>Polish Zloty</option>
							<option value="SGD" <?php echo ( $payment_currency == 'SGD' ) ? 'selected="selected"' : ''; ?>>Singapore Dollar ($)</option>
							<option value="ZAR" <?php echo ( $payment_currency == 'ZAR' ) ? 'selected="selected"' : ''; ?>>South African Rand (R)</option>
							<option value="KRW" <?php echo ( $payment_currency == 'KRW' ) ? 'selected="selected"' : ''; ?>>South Korean Won</option>
							<option value="SEK" <?php echo ( $payment_currency == 'SEK' ) ? 'selected="selected"' : ''; ?>>Swedish Krona</option>
							<option value="CHF" <?php echo ( $payment_currency == 'CHF' ) ? 'selected="selected"' : ''; ?>>Swiss Franc</option>
							<option value="TWD" <?php echo ( $payment_currency == 'TWD' ) ? 'selected="selected"' : ''; ?>>Taiwan New Dollars</option>
							<option value="THB" <?php echo ( $payment_currency == 'THB' ) ? 'selected="selected"' : ''; ?>>Thai Baht</option>
							<option value="TRY" <?php echo ( $payment_currency == 'TRY' ) ? 'selected="selected"' : ''; ?>>Turkish Lira</option>
							<option value="VND" <?php echo ( $payment_currency == 'VND' ) ? 'selected="selected"' : ''; ?>>Vietnamese Dong</option>
						</select>
						<p class="description"><?php _e('Select the currency for this payment button.', 'simple-membership') ?></p>
					</td>
				</tr>

				<tr valign="top">
					<th colspan="2">
						<div class="swpm-grey-box"><?php _e( 'Stripe API keys. You can get this from your Stripe account.' , 'simple-membership'); ?></div>
					</th>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e( 'Use Global API Keys Settings' , 'simple-membership'); ?></th>
					<td>
						<input type="checkbox" name="stripe_use_global_keys" value="1" <?php echo esc_attr($use_global_keys) ? ' checked' : ''; ?> />
						<p class="description"><?php _e( 'Use API keys from <a href="admin.php?page=simple_wp_membership_payments&tab=payment_settings&subtab=ps_stripe" target="_blank">Payment Settings</a> tab.' , 'simple-membership'); ?></p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e( 'Test Publishable Key' , 'simple-membership'); ?></th>
					<td>
						<input type="text" size="50" name="stripe_test_publishable_key" value="<?php echo esc_attr($stripe_test_publishable_key); ?>" required />
						<p class="description"><?php _e('Enter your Stripe test publishable key.', 'simple-membership') ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( 'Test Secret Key' , 'simple-membership'); ?></th>
					<td>
						<input type="text" size="50" name="stripe_test_secret_key" value="<?php echo esc_attr($stripe_test_secret_key); ?>" required />
						<p class="description"><?php _e('Enter your Stripe test secret key.', 'simple-membership') ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( 'Live Publishable Key' , 'simple-membership'); ?></th>
					<td>
						<input type="text" size="50" name="stripe_live_publishable_key" value="<?php echo esc_attr($stripe_live_publishable_key); ?>" required />
						<p class="description"><?php _e('Enter your Stripe live publishable key.', 'simple-membership') ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( 'Live Secret Key' , 'simple-membership'); ?></th>
					<td>
						<input type="text" size="50" name="stripe_live_secret_key" value="<?php echo esc_attr($stripe_live_secret_key); ?>" required />
						<p class="description"><?php _e('Enter your Stripe live secret key.', 'simple-membership') ?></p>
					</td>
				</tr>

				<tr valign="top">
					<th colspan="2">
						<div class="swpm-grey-box"><?php _e( 'The following details are optional.' , 'simple-membership'); ?></div>
					</th>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e( 'Collect Customer Address' , 'simple-membership'); ?></th>
					<td>
						<input type="checkbox" name="collect_address" value="1" <?php echo esc_attr($collect_address); ?> />
						<p class="description"><?php _e('Enable this option if you want to collect customer address during Stripe checkout.', 'simple-membership') ?></p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e( 'Return URL', 'simple-membership' ); ?></th>
					<td>
						<input type="text" size="100" name="return_url" value="<?php echo esc_url_raw($return_url); ?>" />
						<p class="description"><?php _e('This is the URL the user will be redirected to after a successful payment. Enter the URL of your Thank You page here.', 'simple-membership') ?></p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e( 'Button Image URL' , 'simple-membership'); ?></th>
					<td>
						<input type="text" size="100" name="button_image_url" value="<?php echo esc_url_raw($button_image_url); ?>" />
						<p class="description"><?php _e('If you want to customize the look of the button using an image then enter the URL of the image.', 'simple-membership') ?></p>
					</td>
				</tr>				

			</table>

			<script>
			var swpmInputsArr = ['stripe_test_publishable_key', 'stripe_test_secret_key', 'stripe_live_publishable_key', 'stripe_live_secret_key'];
			jQuery('input[name="stripe_use_global_keys"').change(function() {
				var checked = jQuery(this).prop('checked');
				jQuery.each(swpmInputsArr, function(index, el) {
					jQuery('input[name="' + el + '"]').prop('disabled', checked);
				});
			});
			jQuery('input[name="stripe_use_global_keys"').trigger('change');
			</script>

			<p class="submit">
				<?php wp_nonce_field( 'swpm_admin_add_edit_stripe_buy_now_btn', 'swpm_admin_edit_stripe_buy_now_btn' ); ?>
				<input type="submit" name="swpm_stripe_buy_now_edit_submit" class="button-primary" value="<?php _e( 'Save Payment Data' , 'simple-membership'); ?>">
			</p>

		</form>

	</div>
</div>
	<?php
}

/*
 * Process submission and save the edited Stripe Buy now payment button data
 */
add_action( 'swpm_edit_payment_button_process_submission', 'swpm_edit_stripe_buy_now_button_data' );

function swpm_edit_stripe_buy_now_button_data() {
	if ( isset( $_REQUEST['swpm_stripe_buy_now_edit_submit'] ) ) {
		//This is a Stripe buy now button edit event. Process the submission.
		check_admin_referer( 'swpm_admin_add_edit_stripe_buy_now_btn', 'swpm_admin_edit_stripe_buy_now_btn' );
		//Update and Save the edited payment button data
		$button_id   = sanitize_text_field( $_REQUEST['button_id'] );
		$button_id   = absint( $button_id );
		$button_type = sanitize_text_field( $_REQUEST['button_type'] );
		$button_name = sanitize_text_field( $_REQUEST['button_name'] );

		$button_post = array(
			'ID'         => $button_id,
			'post_title' => $button_name,
			'post_type'  => 'swpm_payment_button',
		);
		wp_update_post( $button_post );

		update_post_meta( $button_id, 'button_type', $button_type );
		update_post_meta( $button_id, 'membership_level_id', sanitize_text_field( $_REQUEST['membership_level_id'] ) );
		update_post_meta( $button_id, 'payment_amount', trim( sanitize_text_field( $_REQUEST['payment_amount'] ) ) );
		update_post_meta( $button_id, 'payment_currency', sanitize_text_field( $_REQUEST['payment_currency'] ) );

		$stripe_test_secret_key = isset( $_POST['stripe_test_secret_key'] ) ? sanitize_text_field( stripslashes ( $_POST['stripe_test_secret_key'] ) ) : '';
		$stripe_test_publishable_key = isset( $_POST['stripe_test_publishable_key'] ) ? sanitize_text_field( stripslashes ( $_POST['stripe_test_publishable_key'] ) ) : '';		

		if ( ! is_null( $stripe_test_secret_key ) ) {
			update_post_meta( $button_id, 'stripe_test_secret_key', trim( $stripe_test_secret_key ) );
		}

		if ( ! is_null( $stripe_test_publishable_key ) ) {
			update_post_meta( $button_id, 'stripe_test_publishable_key', trim( $stripe_test_publishable_key ) );
		}

		$stripe_live_secret_key = isset( $_POST['stripe_live_secret_key'] ) ? sanitize_text_field( stripslashes ( $_POST['stripe_live_secret_key'] ) ) : '';
		$stripe_live_publishable_key = isset( $_POST['stripe_live_publishable_key'] ) ? sanitize_text_field( stripslashes ( $_POST['stripe_live_publishable_key'] ) ) : '';

		if ( ! is_null( $stripe_live_secret_key ) ) {
			update_post_meta( $button_id, 'stripe_live_secret_key', trim( $stripe_live_secret_key ) );
		}

		if ( ! is_null( $stripe_live_publishable_key ) ) {
			update_post_meta( $button_id, 'stripe_live_publishable_key', trim( $stripe_live_publishable_key ) );
		}

		update_post_meta( $button_id, 'stripe_collect_address', isset( $_POST['collect_address'] ) ? '1' : '' );

		$stripe_use_global_keys = filter_input( INPUT_POST, 'stripe_use_global_keys', FILTER_SANITIZE_NUMBER_INT );
		$stripe_use_global_keys = $stripe_use_global_keys ? true : false;

		update_post_meta( $button_id, 'stripe_use_global_keys', $stripe_use_global_keys );

		update_post_meta( $button_id, 'stripe_collect_address', isset( $_POST['collect_address'] ) ? '1' : '' );

		update_post_meta( $button_id, 'return_url', trim( sanitize_text_field( $_REQUEST['return_url'] ) ) );
		update_post_meta( $button_id, 'button_image_url', esc_url( $_REQUEST['button_image_url'] ) );

		echo '<div id="message" class="updated fade"><p>'.__('Payment button data successfully updated!', 'simple-membership').'</p></div>';
	}
}

/************************************************************************
 * End of edit Stripe Buy now payment button stuff
 ************************************************************************/
