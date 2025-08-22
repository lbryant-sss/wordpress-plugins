import React from 'react';
import RenderFields from '@Components/RenderFields';
import ConditionsHelper from '@Utils/conditions';

const EmailTemplatesDrawerBody = ( { formState, handleChange, errors } ) => {
	const emailFields = cart_abandonment_admin?.settings_fields?.email_fields;
	const conditions = new ConditionsHelper();

	return (
		<div className="p-1 flex flex-col gap-1 bg-light-background rounded-lg">
			{ /* <div className="px-4 rounded-md bg-white shadow-sm">
				<ToggleField
					title={ __(
						'Use WooCommerce email style',
						'woo-cart-abandonment-recovery'
					) }
					description={ __(
						'Email will be sent in WooCommerce email format. Also the sender name and sender email address will be replaced by the Woocommerce Email sender options.',
						'woo-cart-abandonment-recovery'
					) }
					name="wcf_use_woo_email_style"
					value={ formState.wcf_use_woo_email_style }
					handleChange={ handleChange }
				/>
				<TestEmail
					title={ __(
						'Send Test Email To',
						'woo-cart-abandonment-recovery'
					) }
				/>
			</div>
			<div className="px-4 rounded-md bg-white shadow-sm">
				<ToggleField
					title={ __(
						'Create Coupon Code',
						'woo-cart-abandonment-recovery'
					) }
					description={ __(
						'Auto-create the special coupon for the abandoned cart to send over the emails.',
						'woo-cart-abandonment-recovery'
					) }
					name="wcf_override_global_coupon"
					value={ formState.wcf_override_global_coupon }
					handleChange={ handleChange }
				/>
				{ formState.wcf_override_global_coupon && (
					<>
						<SelectField
							title={ __(
								'Discount Type',
								'woo-cart-abandonment-recovery'
							) }
							description={ __(
								'Select the Discount Type.',
								'woo-cart-abandonment-recovery'
							) }
							name="wcf_email_discount_type"
							value={ formState.wcf_email_discount_type }
							handleChange={ handleChange }
							optionsArray={ [
								{
									id: 1,
									name: 'Percentage Discount',
								},
								{
									id: 2,
									name: 'Fixed Cart Discount',
								},
							] }
						/>
						<NumberField
							title={ __(
								'Coupon Amount',
								'woo-cart-abandonment-recovery'
							) }
							description={ __(
								'Consider cart abandoned after above entered minutes of item being added to cart and order not placed.',
								'woo-cart-abandonment-recovery'
							) }
							name="wcf_email_discount_amount"
							value={ formState.wcf_email_discount_amount }
							handleChange={ handleChange }
						/>
						<TimeField
							title={ __(
								'Coupon Expires After',
								'woo-cart-abandonment-recovery'
							) }
							description={ __(
								'Enter zero (0) to restrict coupon from expiring.',
								'woo-cart-abandonment-recovery'
							) }
							name="wcf_email_coupon_expiry_date"
							value={ formState.wcf_email_coupon_expiry_date }
							unitKey={ 'wcf_coupon_expiry_unit' }
							unitOptions={ [
								{ id: 'hours', name: 'Hour(s)' },
								{ id: 'days', name: 'Days(s)' },
							] }
							handleChange={ handleChange }
						/>
						<ToggleField
							title={ __(
								'Free Shipping',
								'woo-cart-abandonment-recovery'
							) }
							description={ __(
								'Allows you to grant free shipping. A free shipping method must be enabled in your shipping zone and be set to require "a valid free shipping coupon".',
								'woo-cart-abandonment-recovery'
							) }
							name="wcf_free_shipping_coupon"
							value={ formState.wcf_free_shipping_coupon }
							handleChange={ handleChange }
						/>

						<ToggleField
							title={ __(
								'Individual use only',
								'woo-cart-abandonment-recovery'
							) }
							description={ __(
								'Check this box if the coupon cannot be used in conjunction with other coupons.',
								'woo-cart-abandonment-recovery'
							) }
							name="wcf_individual_use_only"
							value={ formState.wcf_individual_use_only }
							handleChange={ handleChange }
						/>
						<ToggleField
							title={ __(
								'Auto Apply Coupon',
								'woo-cart-abandonment-recovery'
							) }
							description={ __(
								'Automatically add the coupon to the cart at the checkout.',
								'woo-cart-abandonment-recovery'
							) }
							name="wcf_auto_coupon_apply"
							value={ formState.wcf_auto_coupon_apply }
							handleChange={ handleChange }
						/>
					</>
				) }
				<TextField
					title={ __(
						'Enter a coupon code to add into email',
						'woo-cart-abandonment-recovery'
					) }
					name="custom_coupon_code"
					value={ formState.custom_coupon_code }
					handleChange={ handleChange }
				/>
			</div> */ }
			{ emailFields &&
				Object.entries( emailFields ).map( ( [ field, data ] ) => {
					const isFieldActive = conditions.isActiveControl(
						data,
						formState
					);

					// Don't render wrapper div if field is not active
					if ( ! isFieldActive ) {
						return null;
					}

					return (
						<div
							key={ field }
							className="px-4 rounded-md bg-white shadow-sm"
						>
							<RenderFields
								data={ data }
								value={
									[ 'time', 'test_email' ].includes(
										data?.type
									)
										? formState
										: formState[ field ] || ''
								}
								isActive={ true }
								handleChange={ handleChange }
								errors={ errors }
								autoSave={ false }
							/>
						</div>
					);
				} ) }
		</div>
	);
};

export default EmailTemplatesDrawerBody;
