<?php

namespace WeDevs\Dokan\Emails;

use WC_Email;
use WeDevs\Dokan\Vendor\Vendor;

/**
 * Completed Order Email.
 *
 * An email sent to the admin when a order is completed for.
 *
 * @class       VendorCompletedOrder
 * @version     3.2.2
 * @package     Dokan/Classes/Emails
 * @author      weDevs
 * @extends     WC_Email
 */
class VendorCompletedOrder extends WC_Email {
    public $order_info;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->id             = 'dokan_vendor_completed_order';
        $this->title          = __( 'Dokan Vendor Completed Order', 'dokan-lite' );
        $this->description    = __( 'Completed order emails are sent to the vendor when a order is completed.', 'dokan-lite' );
        $this->template_html  = 'emails/vendor-completed-order.php';
        $this->template_plain = 'emails/plain/vendor-completed-order.php';
        $this->template_base  = DOKAN_DIR . '/templates/';
        $this->placeholders   = array(
            '{order_date}'   => '',
            '{order_number}' => '',
        );

        // Triggers for this email.
        add_action( 'woocommerce_order_status_completed_notification', array( $this, 'trigger' ), 10, 2 );
	    //Prevent admin email for sub-order
        add_filter( 'woocommerce_email_enabled_new_order', [ $this, 'prevent_sub_order_admin_email' ], 10, 2 );
        // Call parent constructor.
        parent::__construct();

        // Other settings.
        $this->recipient = 'vendor@ofthe.product';
    }

    /**
     * Get email subject.
     *
     * @since  3.2.2
     * @return string
     */
    public function get_default_subject() {
        return __( '[{site_title}] Your customer order is now complete ({order_number}) - {order_date}', 'dokan-lite' );
    }

    /**
     * Get email heading.
     *
     * @since  3.2.2
     * @return string
     */
    public function get_default_heading() {
        return __( 'Complete Customer Order: #{order_number}', 'dokan-lite' );
    }

    /**
     * Trigger the sending of this email.
     *
     * @param int $order_id The Order ID.
     * @param array $order.
     */
    public function trigger( $order_id, $order = false ) {
        if ( ! $this->is_enabled() ) {
            return;
        }

        if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
            $order = wc_get_order( $order_id );
        }

        if ( ! is_a( $order, 'WC_Order' ) ) {
            return;
        }

        $this->setup_locale();
        $this->object                         = $order;
        $this->placeholders['{order_date}']   = wc_format_datetime( $this->object->get_date_created() );
        $this->placeholders['{order_number}'] = $this->object->get_order_number();

        $seller_id = dokan_get_seller_id_by_order( $order_id );
        if ( empty( $seller_id ) ) {
            return;
        }

        // check has sub order
        if ( $order->get_meta( 'has_sub_order' ) ) {
            // same hook will be called again for sub-orders, so we don't need to process this from here.
            return;
        }
        $seller_info = new Vendor( $seller_id );
        if ( ! $seller_info->get_id() ) {
            return;
        }
        $seller_email     = $seller_info->get_email();
        $this->order_info = dokan_get_vendor_order_details( $order_id );
        $this->send( $seller_email, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

        $this->restore_locale();
    }

    /**
     * Get content html.
     *
     * @access public
     * @return string
     */
    public function get_content_html() {
        return wc_get_template_html(
            $this->template_html, array(
                'order'              => $this->object,
                'email_heading'      => $this->get_heading(),
                'additional_content' => $this->get_additional_content(),
                'sent_to_admin'      => true,
                'plain_text'         => false,
                'email'              => $this,
                'order_info'         => $this->order_info,
            ), 'dokan/', $this->template_base
        );
    }

    /**
     * Get content plain.
     *
     * @access public
     * @return string
     */
    public function get_content_plain() {
        return wc_get_template_html(
            $this->template_plain, array(
                'order'              => $this->object,
                'email_heading'      => $this->get_heading(),
                'additional_content' => $this->get_additional_content(),
                'sent_to_admin'      => true,
                'plain_text'         => true,
                'email'              => $this,
                'order_info'         => $this->order_info,
            ), 'dokan/', $this->template_base
        );
    }

    /**
     * Initialise settings form fields.
     */
    public function init_form_fields() {
        /* translators: %s: list of placeholders */
        $placeholder_text = sprintf( __( 'Available placeholders: %s', 'dokan-lite' ), '<code>' . esc_html( implode( '</code>, <code>', array_keys( $this->placeholders ) ) ) . '</code>' );
        $this->form_fields = array(
            'enabled'    => array(
                'title'   => __( 'Enable/Disable', 'dokan-lite' ),
                'type'    => 'checkbox',
                'label'   => __( 'Enable this email notification', 'dokan-lite' ),
                'default' => 'yes',
            ),
            'subject'    => array(
                'title'       => __( 'Subject', 'dokan-lite' ),
                'type'        => 'text',
                'desc_tip'    => true,
                'description' => $placeholder_text,
                'placeholder' => $this->get_default_subject(),
                'default'     => '',
            ),
            'heading'    => array(
                'title'       => __( 'Email heading', 'dokan-lite' ),
                'type'        => 'text',
                'desc_tip'    => true,
                'description' => $placeholder_text,
                'placeholder' => $this->get_default_heading(),
                'default'     => '',
            ),
            'additional_content' => array(
                'title'       => __( 'Additional content', 'dokan-lite' ),
                'description' => __( 'Text to appear below the main email content.', 'dokan-lite' ) . ' ' . $placeholder_text,
                'css'         => 'width:400px; height: 75px;',
                'placeholder' => __( 'N/A', 'dokan-lite' ),
                'type'        => 'textarea',
                'default'     => $this->get_default_additional_content(),
                'desc_tip'    => true,
            ),
            'email_type' => array(
                'title'       => __( 'Email type', 'dokan-lite' ),
                'type'        => 'select',
                'description' => __( 'Choose which format of email to send.', 'dokan-lite' ),
                'default'     => 'html',
                'class'       => 'email_type wc-enhanced-select',
                'options'     => $this->get_email_type_options(),
                'desc_tip'    => true,
            ),
        );
    }

    /**
     * Prevent sub-order email for admin
     *
     * @param $bool
     * @param $order
     *
     * @return bool
     */
    public function prevent_sub_order_admin_email( $bool, $order ) {
        if ( ! $order ) {
            return $bool;
        }

        if ( $order->get_parent_id() ) {
            return false;
        }

        return $bool;
    }
}
