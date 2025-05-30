<?php
// phpcs:disable WordPress.WP.AlternativeFunctions.file_system_operations_fwrite
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WOE_Formatter_Xml extends WOE_Formatter {
	var $linebreak;

	public function __construct(
		$mode,
		$filename,
		$settings,
		$format,
		$labels,
		$field_formats,
		$date_format,
		$offset
	) {
		parent::__construct( $mode, $filename, $settings, $format, $labels, $field_formats, $date_format, $offset );

		$this->linebreak = apply_filters( "woe_xml_output_linebreak", "\n" );
	}

	public function start( $data = '' ) {
		parent::start( $data );

		fwrite( $this->handle,
			apply_filters( "woe_xml_output_header", '<?xml version="1.0" encoding="UTF-8"?>' ) . $this->linebreak );

		if ( @$this->settings['prepend_raw_xml'] ) {
			fwrite( $this->handle, $this->settings['prepend_raw_xml'] . $this->linebreak );
		}

		fwrite( $this->handle, apply_filters( "woe_xml_output_before_root_tag", '' ) );

		if ( $this->settings['root_tag'] ) {
			fwrite( $this->handle, "<" . $this->settings['root_tag'] . ">" . $this->linebreak );
		}

		fwrite( $this->handle, apply_filters( "woe_xml_output_after_root_tag", '', $this ) );

	}

	public function output( $rec ) {
		$rec = parent::output( $rec );
		$recs = apply_filters( 'woe_xml_prepare_records', array($rec) );
		foreach($recs as $rec)
			$this->output_one_record($rec);
	}

	public function output_one_record( $rec ) {
		$xml = new SimpleXMLElement( "<" . $this->settings['order_tag'] . "></" . $this->settings['order_tag'] . ">" );

		$labels = $this->labels['order'];
		$rec    = apply_filters( 'woe_xml_prepare_record', $rec );

		foreach ( $labels->get_labels() as $label_data ) {
			$original_key = $label_data['key'];
			$label        = $label_data['label'];
			$key          = $label_data['parent_key'] ? $label_data['parent_key'] : $original_key;

			$field_value = apply_filters( 'woe_xml_prepare_field_' . $original_key, $rec[ $key ], $rec );

			if ( is_array( $field_value ) ) {
				if ( $original_key == "products" ) {
					$child_tag    = $this->settings['product_tag'];
					$child_labels = $this->labels['products'];
				} elseif ( $original_key == "coupons" ) {
					$child_tag    = $this->settings['coupon_tag'];
					$child_labels = $this->labels['coupons'];
				} else {
					// array was created by hook!
					$child_tag    = '';
					$child_labels = array();
				}
				// modify children using filters
				$child_tag    = apply_filters( 'woe_xml_child_tagname_' . $original_key, $child_tag, $field_value,
					$rec );
				$child_labels = apply_filters( 'woe_xml_child_labels_' . $original_key, $child_labels, $field_value,
					$rec );

				if ( !is_array($child_labels) AND $child_labels->is_not_empty() == false ) // can't export!
				{
					continue;
				}

				$childs = $label ? $xml->addChild( $label ) : $xml; // add Products , or use root!

				foreach ( $field_value as $child_key => $child_element ) {
					$tag_name = $child_tag ? $child_tag : $child_key;
					// add nested Product if array!
					$child = $childs->addChild( $tag_name,
						is_array( $child_element ) ? null : $this->prepare_string( $child_element ) );
					// products/coupons
					if ( is_array( $child_element ) ) {
						foreach ( $child_labels->get_labels() as $child_label_data ) {
							$child_original_key = $child_label_data['key'];
							$child_label        = $child_label_data['label'];
							$child_key          = $child_label_data['parent_key'] ? $child_label_data['parent_key'] : $child_original_key;
							if ( isset( $child_element[ $child_key ] ) ) {
								$child->addChild( $child_label, $this->prepare_string( $child_element[ $child_key ] ) );
							}
						}
					}
				}
			} else {
				$xml->addChild( $label, $this->prepare_string( $field_value ) );
			}
		}

		//format it!
		$dom                              = dom_import_simplexml( $xml );
		$dom->ownerDocument->formatOutput = ( $this->mode == 'preview' || $this->settings['preview_format'] );
		$output_flags                     = ! empty( $this->settings['self_closing_tags'] ) ? 0 : LIBXML_NOEMPTYTAG;
		$xml                              = $dom->ownerDocument->saveXML( $dom->ownerDocument->documentElement,
			$output_flags );

		if ( $this->has_output_filter ) {
			$xml = apply_filters( "woe_xml_output_filter", $xml, $rec, $this );
		}

		if($xml)
			fwrite( $this->handle, $xml . $this->linebreak );
	}

	public function finish( $data = '' ) {
		fwrite( $this->handle, apply_filters( "woe_xml_output_before_closing_root_tag", '', $this ) );
		if ( $this->settings['root_tag'] ) {
			fwrite( $this->handle, "</" . $this->settings['root_tag'] . ">" . $this->linebreak );
		}
		if ( @$this->settings['append_raw_xml'] ) {
			fwrite( $this->handle, $this->settings['append_raw_xml'] . $this->linebreak );
		}
		do_action( "woe_xml_print_footer", $this->handle, $this );
		parent::finish();
	}

	private function prepare_string( $value ) {
		return apply_filters("woe_xml_encode_chars", true) ? htmlspecialchars( $value ) : $value;
	}
}