<?php
namespace DynamicVisibilityForElementor;

use ElementorPro\Core\Utils;

trait Form {

	/** In Elementor forms validation and process functions, Elementor
	 *  unfortunately does not pass the field settings, so we have to fetch
	 *  them manually. */
	public static function get_form_field_settings( $id, $record ) {
		$field_settings = $record->get_form_settings( 'form_fields' );
		$field_settings = array_filter($field_settings, function ( $field ) use ( $id ) {
			return $field['custom_id'] === $id;
		});
		return array_values( $field_settings )[0];
	}

	/**
	 * -!h- Don't allow the evaluation of tokens in form fields for security
	 *  reasons.
	 */
	public static function sanitize_form_field( $str ) {
		return preg_replace( '/\[([\w-]+)([:|\]])/', '[ $1 $2', $str );
	}

	public static function get_form_data( $record ) {
		// Get sumitetd Form data
		$raw_fields = $record->get( 'fields' );
		// Normalize the Form Data
		$fields = [];
		foreach ( $raw_fields as $id => $field ) {
			$fields[ $id ] = self::sanitize_form_field( $field['value'] );
		}

		$extra_fields = self::get_form_extra_data( $record, $fields );
		foreach ( $extra_fields as $key => $value ) {
			$fields[ $key ] = self::sanitize_form_field( $value );
		}

		global $dce_form;
		if ( ! empty( $dce_form ) && is_array( $dce_form ) ) {
			foreach ( $fields as $key => $value ) {
				$dce_form[ $key ] = $value;
			}
		} else {
			$dce_form = $fields; // for form tokens
		}

		if ( ! empty( $fields['submitted_on_id'] ) ) {
			global $post, $user;
			if ( empty( $post ) ) {
				$post = get_post( $fields['submitted_on_id'] );
			}
		}

		if ( ! empty( $fields['post_id'] ) ) {
			global $post;
			$post = get_post( $fields['post_id'] );
		}

		return $fields;
	}

	public static function get_form_extra_data( $record, $fields = null, $settings = null ) {

		$this_page = false;
		$referrer = isset( $_POST['referrer'] ) ? sanitize_text_field( $_POST['referrer'] ) : '';

		$form_name = '';
		if ( is_object( $record ) ) {
			$form_name = sanitize_text_field( $record->get_form_settings( 'form_name' ) );
		} elseif ( ! empty( $settings['form_name'] ) ) {
			$form_name = sanitize_text_field( $settings['form_name'] );
		}

		$this_post = get_queried_object();
		if ( $this_post instanceof \WP_Post ) {
			$this_page = $this_post;
		}

		if ( ! $this_page && $referrer ) {
			$post_id = url_to_postid( $referrer );
			if ( $post_id ) {
				$this_page = get_post( $post_id );
				$this_post = $this_page;
			}
		}
		if ( ! $this_page && isset( $_POST['post_id'] ) ) {
			$this_page = get_post( intval( $_POST['post_id'] ) );
		}

		return [
			'submitted_on_id' => $this_page->ID,
			'submitted_by_id' => get_current_user_id(),
			'ip_address' => Utils::get_client_ip(),
			'referrer' => $referrer,
			'form_name' => $form_name,
		];
	}

	public static function replace_setting_shortcodes( $setting, $fields = array(), $urlencode = false ) {
		// Shortcode can be `[field id="fds21fd"]` or `[field title="Email" id="fds21fd"]`, multiple shortcodes are allowed
		$setting = preg_replace_callback('/(\[field[^]]*id="(\w+)"[^]]*\])/', function ( $matches ) use ( $urlencode, $fields ) {
			$value = '';
			if ( isset( $fields[ $matches[2] ] ) ) {
				$value = $fields[ $matches[2] ];
			}
			if ( $urlencode ) {
				$value = urlencode( $value );
			}
			return $value;
		}, $setting);

		if ( ! empty( $fields ) ) {
			if ( strpos( $setting, '[field id=' ) !== false ) {
				foreach ( $fields as $fkey => $fvalue ) {
					if ( ! is_object( $fvalue ) ) {
						$fvalue = self::to_string( $fvalue );
						if ( $urlencode ) {
							$fvalue = urlencode( $fvalue );
						}
						if ( ! is_object( $fvalue ) ) {
							$setting = str_replace( '[field id=' . $fkey . ']', $fvalue, $setting );
							$setting = str_replace( '[field id="' . $fkey . '"]', $fvalue, $setting );
						}
					}
				}
			}
		}
		return $setting;
	}

	public static function form_field_value( $arr = array(), $default = null ) {
		$str = '';
		if ( empty( $arr ) ) {
			return $str; }
		if ( is_string( $arr ) ) {
			$arr = Helper::str_to_array( ',', $arr );
		}
		if ( is_object( $arr ) ) {
			$arr = (array) $arr;
		}
		if ( ! empty( $arr ) && is_array( $arr ) ) {
			$i = 0;
			foreach ( $arr as $key => $value ) {
				$str_tmp = '';

				// object
				if ( is_object( $value ) && get_class( $value ) == 'WP_Post' ) {
					$str_tmp .= $value->ID;
				}
				if ( is_object( $value ) && get_class( $value ) == 'WP_User' ) {
					$str_tmp .= $value->ID;
				}
				if ( is_object( $value ) && get_class( $value ) == 'WP_Term' ) {
					$str_tmp .= $value->term_id;
				}

				// array
				if ( is_array( $value ) && isset( $value['post_title'] ) ) {
					$str_tmp .= $value['ID'];
				}
				if ( is_array( $value ) && isset( $value['display_name'] ) ) {
					$str_tmp .= $value['ID'];
				}
				if ( is_array( $value ) && isset( $value['term_id'] ) ) {
					$str_tmp .= $value['term_id'];
				}

				// INT or String
				if ( ! $str_tmp ) {
					$str_tmp = $value;
				}

				$str .= $str_tmp;
				if ( $i < count( $arr ) - 1 ) {
					$str .= ',';
				}
				++$i;
			}
		}
		if ( $str == '' ) {
			$str = $default;
		}
		return $str;
	}

	// convert an array to a options list compatible with Elementor Pro Form
	public static function array_options( $arr = array(), $val = 'keys' ) {
		$str = '';
		if ( empty( $arr ) ) {
			return false; }
		if ( is_string( $arr ) ) {
			$arr = Helper::str_to_array( ',', $arr );
		}
		if ( is_object( $arr ) ) {
			$arr = (array) $arr;
		}
		if ( ! empty( $arr ) && is_array( $arr ) ) {
			if ( $val && ! in_array( $val, array( 'keys', 'post', 'term', 'user' ) ) ) {
				$str = $val . '|' . PHP_EOL;
			}
			$i = 0;
			foreach ( $arr as $key => $value ) {
				$str_tmp = '';

				// object
				if ( is_object( $value ) && get_class( $value ) == 'WP_Post' ) {
					$str_tmp .= esc_html( $value->post_title ) . '|' . $value->ID;
				}
				if ( is_object( $value ) && get_class( $value ) == 'WP_User' ) {
					$str_tmp .= esc_html( $value->display_name ) . '|' . $value->ID;
				}
				if ( is_object( $value ) && get_class( $value ) == 'WP_Term' ) {
					$str_tmp .= esc_html( $value->name ) . '|' . $value->term_id;
				}

				// array
				if ( is_array( $value ) && isset( $value['post_title'] ) ) {
					$str_tmp .= esc_html( $value['post_title'] ) . '|' . $value['ID'];
				}
				if ( is_array( $value ) && isset( $value['display_name'] ) ) {
					$str_tmp .= esc_html( $value['display_name'] ) . '|' . $value['ID'];
				}
				if ( is_array( $value ) && isset( $value['name'] ) ) {
					$str_tmp .= esc_html( $value['name'] ) . '|' . $value['term_id'];
				}

				// INT
				if ( is_numeric( $value ) ) {
					$value = intval( $value );
					if ( $val == 'post' ) {
						$str_tmp = wp_kses_post( get_the_title( $value ) ) . '|' . $value;
					}
					if ( $val == 'user' ) {
						$tmp_user = get_user_by( 'ID', $value );
						if ( $tmp_user ) {
							$str_tmp = esc_html( $tmp_user->display_name ) . '|' . $value;
						}
					}
					if ( $val == 'term' ) {
						$tmp_term = Helper::get_term_by( 'id', $value );
						if ( $tmp_term ) {
							$str_tmp = esc_html( $tmp_term->name ) . '|' . $value;
						}
					}
				}

				if ( ! $str_tmp ) {
					if ( $val == 'keys' || ! is_numeric( $key ) ) {
						$str_tmp .= esc_html( $value ) . '|' . $key;
					} else {
						$str_tmp .= esc_html( $value );
					}
				}

				$str .= $str_tmp;
				if ( $i < count( $arr ) - 1 ) {
					$str .= PHP_EOL;
				}
				++$i;
			}
		}
		return $str;
	}

	public static function get_field( $custom_id, $settings = array() ) {
		if ( ! empty( $settings['form_fields'] ) ) {
			foreach ( $settings['form_fields'] as $afield ) {
				if ( $afield['custom_id'] == $custom_id ) {
					return $afield;
				}
			}
		}
		return false;
	}

	public static function get_field_type( $custom_id, $settings = array() ) {
		$field = self::get_field( $custom_id, $settings );
		if ( $field ) {
			return $field['field_type'];
		}
		return false;
	}
}
