<?php
namespace Wtpdf\Ubl\Invoice\Formats;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( !class_exists( '\\Wtpdf\\UBL\\Invoice\\Formats\\UblCiusNl' ) ) {
    class UblCiusNl extends \Wtpdf\Ubl\Documents\Invoice {
        CONST INVOICE_TYPE_CODE = '380';

        public $order = null;
        protected $typcode = self::INVOICE_TYPE_CODE;
        public $elements = array();
        public $ubl_format_name = 'ubl_cius_nl';
        public $data_format = 'Y-m-d';

        public function __construct( $order = null ) {
            parent::__construct();
            $this->order = $order;
            $this->elements = array(
                
                // BT-24: Specification identifier or Customization identifier.
                'specification' => array(
                    'enabled' => true,
                    'value_arr' => $this->get_formatted_specification(),
                ),

                // BT-23: Profile identifier is not available for this format.
                'profile_identifier' => array(
                    'enabled' => false,
                    'value_arr' => '',
                ),

                // BT-1: Invoice number.
                'invoice_number' => array(
                    'enabled' => true,
                    'value_arr' => $this->get_formatted_invoice_number( $this->order, $this->ubl_format_name ),
                ),

                // BT-2: Invoice issue date.
                'issue_date' => array(
                    'enabled' => true,
                    'value_arr' => $this->get_formatted_issue_date( $this->order, $this->ubl_format_name ),
                ),

                // BT-9: Invoice due date.
                'due_date' => array(
                    'enabled' => true,
                    'value_arr' => $this->get_formatted_due_date( $this->order, $this->ubl_format_name ),
                ),

                // BT-3: Invoice type code.
                'invoice_typecode' => array(
                    'enabled' => true,
                    'value_arr' => $this->get_formatted_invoice_typecode(),
                ),

                // BT-22: Invoice notes.
                'notes' => array(
                    'enabled' => true,
                    'value_arr' => $this->get_formatted_notes( $this->order, $this->ubl_format_name ),
                ),

                // BT-7: Invoice tax point date.
                'tax_points_date' => array(
                    'enabled' => true,
                    'value_arr' => $this->get_formatted_tax_points_date( $this->order, $this->ubl_format_name ),
                ),

                // BT-5: Invoice currency code.
                'currency_code' => array(
                    'enabled' => true,
                    'value_arr' => $this->get_formatted_currency_code( $this->order, $this->ubl_format_name ),
                ),

                // BT-6: Invoice tax currency code.
                'tax_currency_code' => array(
                    'enabled' => true,
                    'value_arr' => $this->get_formatted_tax_currency_code( $this->order, $this->ubl_format_name ),
                ),

                // BT-19: Buyer accounting reference.
                'buyer_accounting_reference' => array(
                    'enabled' => true,
                    'value_arr' => $this->get_formatted_buyer_accounting_reference( $this->order, $this->ubl_format_name ),
                ),

                // BT-10: Buyer reference.
                'buyer_reference' => array(
                    'enabled' => true,
                    'value_arr' => $this->get_formatted_buyer_reference( $this->order, $this->ubl_format_name ),
                ),

                // BG-14: Invoice period.
                'invoice_period' => array(
                    'enabled' => true,
                    'value_arr' => $this->get_formatted_invoice_period( $this->order, $this->ubl_format_name ),
                ),

                // BT-13: Purchase order reference and BT-14: Sales order reference
                'order_reference' => array(
                    'enabled' => true,
                    'value_arr' => $this->get_formatted_order_reference( $order, $this->ubl_format_name ),
                    
                ),

                // BG-3: Originator document reference.
                'originator_document_reference' => array(
                    'enabled' => true,
                    'value_arr' => $this->get_formatted_originator_document_reference( $this->order, $this->ubl_format_name ),
                ),

                // BT-12: Contract document reference.
                'contract_document_reference' => array(
                    'enabled' => true,
                    'value_arr' => $this->get_formatted_contract_document_reference( $this->order, $this->ubl_format_name ),
                ),

                // BG-24: Attachments node


                // Seller node
                'seller' => array(
                    'enabled' => true,
                    'value_arr' => $this->get_formatted_accounting_supplier_party( $this->order, $this->ubl_format_name ),
                ),

                // Buyer node
                'buyer' => array(
                    'enabled' => true,
                    'value_arr' => $this->get_formatted_accounting_customer_party( $this->order, $this->ubl_format_name ),
                ),

                
                // Payee node
                // Delivery node
                // Payment nodes
                // Allowances and charges

                // Invoice totals
                'tax_subtotal' => array(
                    'enabled' => true,
                    'value_arr' => $this->get_formatted_tax_subtotal_details(),
                ),

                // legal monitory tax total
                'legal_monitory_tax_total' => array(
                    'enabled' => true,
                    'value_arr' => $this->get_formatted_legal_monitory_tax_total(),
                ),

                // Invoice lines
                'invoice_lines' => array(
                    'enabled' => true,
                    'value_arr' => $this->get_formatted_invoice_lines(),
                ),
            );
        }

        /**
         * Creates a new instance of the class.
         *
         * @param mixed $order Optional. The order object or ID. Default is null.
         * @return self A new instance of the class.
         */
        public static function instance( $order = null ) {
            return new self( $order);
        }

        /**
         * Retrieves the document namespace for the UBL CIUS NL invoice format.
         *
         * This method returns an array containing the XML namespace definitions
         * required for the UBL CIUS NL invoice format. These namespaces are used
         * to define the structure and components of the UBL invoice.
         *
         * @return array An associative array with the following keys:
         *               - 'xmlns': The main namespace for the UBL invoice.
         *               - 'xmlns:cac': The namespace for common aggregate components.
         *               - 'xmlns:cbc': The namespace for common basic components.
         */
        public function get_document_namespace() {
            return array(
                'xmlns' => 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2',
                'xmlns:cac' => 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2',
                'xmlns:cbc' => "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
            );
        }

        /**
         * Retrieves the formatted elements for the UBL invoice.
         *
         * This method applies the 'wtpdf_ubl_format_elements' filter to the elements
         * of the UBL invoice, allowing for customization of the formatted elements.
         *
         * @return array The formatted elements for the UBL invoice.
         *
         * @hook wtpdf_ubl_format_elements
         * @param array $this->elements The elements of the UBL invoice.
         * @param object $this->order The WooCommerce order object.
         * @param string 'ublinvoice' The context for the filter.
         */
        public function get_formatted_elements() {
            return apply_filters( 'wtpdf_ubl_format_elements', $this->elements, $this->order, 'ublinvoice' );
        }
        
        /**
         * Retrieves the specification string for the UBL CIUS NL format.
         *
         * @return string The specification string indicating compliance with EN 16931:2017 and NL CIUS v1.0.
         */
        public function get_specification(): string {
            return "urn:cen.eu:en16931:2017#compliant#urn:fdc:nen.nl:nlcius:v1.0";
        }

        /**
         * Retrieves the formatted specification for the UBL CIUS NL invoice.
         *
         * This method returns an associative array containing the name and value
         * of the specification. The 'name' key is set to 'cbc:CustomizationID',
         * and the 'value' key is set to the result of the get_specification() method.
         *
         * @return array An associative array with 'name' and 'value' keys.
         */
        public function get_formatted_specification(): array {
            return array(
                'name' => 'cbc:CustomizationID',
                'value' => $this->get_specification(),
            );
        }

        /**
         * Retrieves the invoice type code for the given order.
         *
         * @param object $order The WooCommerce order object.
         * @return string The invoice type code.
         */
        public function get_invoice_typecode( $order ): string {
            return $this->typcode;
        }

        /**
         * Retrieves the formatted invoice type code.
         *
         * This method returns an array containing the name and value of the invoice type code.
         * The value is obtained by calling the get_invoice_typecode method with the order property.
         *
         * @return array An associative array with 'name' and 'value' keys representing the invoice type code.
         */
        public function get_formatted_invoice_typecode(): array {
            return array(
                'name' => 'cbc:InvoiceTypeCode',
                'value' => $this->get_invoice_typecode( $this->order ),
            );
        }

        /**
         * Retrieves the shop's country code.
         *
         * This method attempts to get the shop's country code from the WooCommerce Packing List plugin options.
         * If the country code is not set in the plugin options, it falls back to the WooCommerce default country setting.
         * If neither option is set, it returns an empty string.
         *
         * @return string The shop's country code. If the country code is not set, an empty string is returned.
         */
        public function get_shop_country() {
            if ( !empty( \Wf_Woocommerce_Packing_List::get_option( 'wf_country' ) ) ) {
                $result = \Wf_Woocommerce_Packing_List::get_option( 'wf_country' );   
            } else if ( !empty( get_option( 'woocommerce_default_country' ) ) ) {
                $result = get_option( 'woocommerce_default_country' );
            } else{
                $result = '';
            }
            
            // String type check before processing.
            if ( is_string( $result ) ) {
                $result = explode( ":", $result );
                return isset($result[0]) ? $result[0] : '';  // Added isset check to avoid undefined index notice.
            }
            
            return '';
        }

        /**
         * Retrieves the formatted tax subtotal details for the current WooCommerce order.
         *
         * This method processes the tax rates associated with the current WooCommerce order
         * and formats them into an array structure suitable for UBL (Universal Business Language)
         * CIUS (Core Invoice Usage Specification) NL (Netherlands) invoices.
         *
         * @return array The formatted tax subtotal details, including tax amounts, categories, and schemes.
         */
        public function get_formatted_tax_subtotal_details() {
            $formatted_tax_array = array_map( function( $item ) {
                return array(
                    'enabled' => true,
                    'name'  => 'cac:TaxSubtotal',
                    'value' => array(
                        array(
                            'name'       => 'cbc:TaxableAmount',
                            'value'      => round( $item['total_ex'], 2 ),
                            'attributes' => array(
                                'currencyID' => $this->order->get_currency(),
                            ),
                        ),
                        array(
                            'name'       => 'cbc:TaxAmount',
                            'value'      => round( $item['total_tax'], 2 ),
                            'attributes' => array(
                                'currencyID' => $this->order->get_currency(),
                            ),
                        ),
                        array(
                            'name'  => 'cac:TaxCategory',
                            'value' => array(
                                array(
                                    'name'  => 'cbc:ID',
                                    'value' => strtoupper( $item['category'] ),
                                ),
                                array(
                                    'name'  => 'cbc:Name',
                                    'value' => $item['name'],
                                ),
                                array(
                                    'name'  => 'cbc:Percent',
                                    'value' => round( $item['percentage'], 1 ),
                                ),
                                array(
                                    'name'  => 'cac:TaxScheme',
                                    'value' => array(
                                        array(
                                            'name'  => 'cbc:ID',
                                            'value' => strtoupper( $item['scheme'] ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                );
            }, $this->get_current_wc_order_tax_rates( $this->order ) );

            $formatted_tax_array = array_values( $formatted_tax_array );
            $array = array(
                'name'  => 'cac:TaxTotal',
                'value' => array(
                    array(
                        'name'       => 'cbc:TaxAmount',
                        'value'      => round( $this->order->get_total_tax(), 2 ),
                        'attributes' => array(
                            'currencyID' => $this->order->get_currency(),
                        ),
                    ),
                    $formatted_tax_array,
                ),
            );
            return $array;
        }

        /**
         * Retrieves the formatted legal monetary tax total for the order.
         *
         * This method calculates the total amounts including and excluding tax,
         * and returns an array representing the legal monetary total in a specific format.
         *
         * @return array The formatted legal monetary total, including:
         *               - 'enabled': A boolean indicating if the total is enabled.
         *               - 'name': The name of the legal monetary total element.
         *               - 'value': An array of monetary amounts, each with:
         *                   - 'name': The name of the monetary amount element.
         *                   - 'value': The monetary amount.
         *                   - 'attributes': An array of attributes for the monetary amount, including:
         *                       - 'currencyID': The currency ID of the order.
         */
        public function get_formatted_legal_monitory_tax_total() {
            $total         = $this->order->get_total();
            $total_inc_tax = $total;
            $total_exc_tax = $total - $this->order->get_total_tax();

            $legalMonetaryTotal = array(
                'enabled' => false,
                'name'  => 'cac:LegalMonetaryTotal',
                'value' => array(
                    array(
                        'name'       => 'cbc:LineExtensionAmount',
                        'value'      => $total_exc_tax,
                        'attributes' => array(
                            'currencyID' => $this->order->get_currency(),
                        ),
                    ),
                    array(
                        'name'       => 'cbc:TaxExclusiveAmount',
                        'value'      => $total_exc_tax,
                        'attributes' => array(
                            'currencyID' => $this->order->get_currency(),
                        ),
                    ),
                    array(
                        'name'       => 'cbc:TaxInclusiveAmount',
                        'value'      => $total_inc_tax,
                        'attributes' => array(
                            'currencyID' => $this->order->get_currency(),
                        ),
                    ),
                    array(
                        'name'       => 'cbc:PayableAmount',
                        'value'      => $total,
                        'attributes' => array(
                            'currencyID' => $this->order->get_currency(),
                        ),
                    ),
                ),
            );

            return $legalMonetaryTotal;
        }

        /**
         * Retrieves the formatted invoice lines for the current order.
         *
         * This method processes the items in the order, including line items, fees, and shipping,
         * and formats them into an array suitable for UBL (Universal Business Language) invoice generation.
         * Each item is processed to include tax details, quantities, and other necessary information.
         *
         * @return array An array of formatted invoice lines.
         */
        public function get_formatted_invoice_lines () {
            $items = $this->order->get_items( array( 'line_item', 'fee', 'shipping' ) );
            $data = array();

            // Build the invoice lines
            foreach ( $items as $item_id => $item ) {
                $taxSubtotal      = array();
                $taxDataContainer = ( 'line_item' === $item['type'] ) ? 'line_tax_data' : 'taxes';
                $taxDataKey       = ( 'line_item' === $item['type'] ) ? 'subtotal'      : 'total';
                $lineTotalKey     = ( 'line_item' === $item['type'] ) ? 'line_total'    : 'total';
                $line_tax_data    = $item[ $taxDataContainer ];

                foreach ( $line_tax_data[ $taxDataKey ] as $tax_id => $tax ) {
                    if ( ! is_numeric( $tax ) ) {
                        continue;
                    }

                    $order_tax_data = $this->get_current_wc_order_tax_rates( $this->order );
                    $taxOrderData  = $order_tax_data[ $tax_id ];

                    $taxSubtotal[] = array(
                        'enabled' => false,
                        'name'  => 'cac:TaxSubtotal',
                        'value' => array(
                            array(
                                'name'       => 'cbc:TaxableAmount',
                                'value'      => round( $item[ $lineTotalKey ], 2 ),
                                'attributes' => array(
                                    'currencyID' => $this->order->get_currency(),
                                ),
                            ),
                            array(
                                'name'       => 'cbc:TaxAmount',
                                'value'      => round( $tax, 2 ),
                                'attributes' => array(
                                    'currencyID' => $this->order->get_currency(),
                                ),
                            ),
                            array(
                                'name'  => 'cac:TaxCategory',
                                'value' => array(
                                    array(
                                        'name'  => 'cbc:ID',
                                        'value' => strtoupper( $taxOrderData['category'] ),
                                    ),
                                    array(
                                        'name'  => 'cbc:Name',
                                        'value' => $taxOrderData['name'],
                                    ),
                                    array(
                                        'name'  => 'cbc:Percent',
                                        'value' => round( $taxOrderData['percentage'], 2 ),
                                    ),
                                    array(
                                        'name'  => 'cac:TaxScheme',
                                        'value' => array(
                                            array(
                                                'name'  => 'cbc:ID',
                                                'value' => strtoupper( $taxOrderData['scheme'] ),
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    );
                }

                $invoiceLine = array(
                    'enabled' => false,
                    'name'  => 'cac:InvoiceLine',
                    'value' => array(
                        array(
                            'name'  => 'cbc:ID',
                            'value' => $item_id,
                        ),
                        array(
                            'name'  => 'cbc:InvoicedQuantity',
                            'value' => $item->get_quantity(),
                            'attributes' => array(
                                'unitCode' => apply_filters( 'wtpdf_ubl_invoice_line_quantity_unit', 'H87', $item, $this->order, $this->ubl_format_name, 'ublinvoice' ),
                            ),
                        ),
                        array(
                            'name'       => 'cbc:LineExtensionAmount',
                            'value'      => round( $item->get_total(), 2 ),
                            'attributes' => array(
                                'currencyID' => $this->order->get_currency(),
                            ),
                        ),
                        array(
                            'name'  => 'cac:TaxTotal',
                            'value' => array(
                                array(
                                    'name'       => 'cbc:TaxAmount',
                                    'value'      => round( $item->get_total_tax(), 2),
                                    'attributes' => array(
                                        'currencyID' => $this->order->get_currency(),
                                    ),
                                ),
                                $taxSubtotal,
                            ),
                        ),
                        array(
                            'name'  => 'cac:Item',
                            'value' => array(
                                array(
                                    'name'  => 'cbc:Name',
                                    'value' => $item->get_name(),
                                ),
                            ),
                        ),
                    ),
                );
                
                $data[] = $invoiceLine;
                // Empty this array at the end of the loop per item, so data doesn't stack
                $taxSubtotal = array();
            }

            return $data;
        }

    }
}