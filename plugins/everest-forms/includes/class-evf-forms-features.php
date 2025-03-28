<?php
/**
 * EverestForms features
 *
 * @package EverestForms\Admin
 * @since   1.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Features Class.
 */
class EVF_Forms_Features {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'everest_forms_fields', array( $this, 'form_fields' ) );
	}

	/**
	 * Load additional fields available in the Pro version.
	 *
	 * @param  array $fields Registered form fields.
	 * @return array
	 */
	public function form_fields( $fields ) {
		$pro_fields = array(
			'EVF_Field_Image_Upload',
			'EVF_Field_Hidden',
			'EVF_Field_Phone',
			'EVF_Field_Password',
			'EVF_Field_HTML',
			'EVF_Field_Divider',
			'EVF_Field_Title',
			'EVF_Field_Signature',
			'EVF_Field_Address',
			'EVF_Field_Country',
			'EVF_Field_Range_Slider',
			'EVF_Field_Privacy_Policy',
			'EVF_Field_Captcha',
			'EVF_Field_Repeater',
			'EVF_Field_Wysiwyg',
			'EVF_Field_Progress',
			'EVF_Field_Payment_Single',
			'EVF_Field_Payment_Radio',
			'EVF_Field_Payment_Checkbox',
			'EVF_Field_Payment_Quantity',
			'EVF_Field_Payment_Total',
			'EVF_Field_Payment_Subtotal',
			'EVF_Field_Payment_Coupon',
			'EVF_Field_Payment_Square',
			'EVF_Field_Payment_Subscription_Plan',
			'EVF_Field_Credit_Card',
			'EVF_Field_Payment_Authorize_Net',
			'EVF_Field_Rating',
			'EVF_Field_Likert',
			'EVF_Field_Scale_Rating',
			'EVF_Field_Yes_No',
			'EVF_Field_Color',
			'EVF_Field_Reset',
			'EVF_Field_Lookup',

		);

		return array_merge( $fields, $pro_fields );
	}
}

new EVF_Forms_Features();
