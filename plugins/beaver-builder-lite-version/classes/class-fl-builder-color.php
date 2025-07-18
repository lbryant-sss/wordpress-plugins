<?php

/**
 * Helper class for working with color values.
 *
 * @since 1.0
 */
final class FLBuilderColor {

	static public function is_hex_value( $value ) {
		return FLBuilderUtils::ctype_xdigit( ltrim( trim( $value ), '#' ) );
	}

	static public function is_rgb_value( $value ) {
		return is_string( $value ) && 0 === strpos( $value, 'rgb' );
	}

	/**
	 * Converts a hex string into an array of RGB values.
	 *
	 * @since 1.0
	 * @since 2.2 Added support for rgba values.
	 * @since 2.3 Added php7.4 fixes
	 * @param string $hex A hex color value with or without the # sign.
	 * @return array An array of RGB values.
	 */
	static public function hex_to_rgb( $hex ) {

		$hex = self::hex_or_rgb( $hex );
		// if $hex is empty or false return basic rgb data.
		if ( ! $hex || strstr( $hex, 'var(--' ) || strstr( $hex, 'hw' ) ) {
			return array(
				'r' => 0,
				'g' => 0,
				'b' => 0,
			);
		}

		if ( strstr( $hex, 'rgb' ) ) {
			$rgb = explode( ',', preg_replace( '/[a-z\(\)]/', '', $hex ) );
			return array(
				'r' => $rgb[0],
				'g' => $rgb[1],
				'b' => $rgb[2],
			);
		}

		list($r, $g, $b) = array_map( function ( $hex ) {
			return hexdec( str_pad( $hex, 2, $hex ) );
		}, str_split( ltrim( trim( $hex ), '#' ), strlen( $hex ) > 4 ? 2 : 1 ) );
		return array(
			'r' => $r,
			'g' => $g,
			'b' => $b,
		);
	}

	/**
	 * Returns RGB or hex color value, OR keyword.
	 *
	 * @since 1.10.8
	 * @param string $color A color to check.
	 * @return string
	 */
	static public function hex_or_rgb( $color ) {

		// ACF colors can be an array
		// [red] => 62
		// [green] => 122
		// [blue] => 81
		// [alpha] => 1
		if ( is_array( $color ) && ! empty( $color ) && 4 === count( $color ) ) {
			$r = '';
			$g = '';
			$b = '';
			$a = '';
			foreach ( $color as $k => $value ) {
				switch ( $k ) {
					case 'red':
					case 'r':
						$r = $value;
						break;
					case 'green':
					case 'g':
						$g = $value;
						break;
					case 'blue':
					case 'b':
						$b = $value;
						break;
					case 'alpha':
					case 'a':
						$a = $value;
						break;
				}
			}
			$color = sprintf( 'rgba(%s, %s, %s, %s)', $r, $g, $b, $a );
		}

		// Handle Prepending # To hex values
		if ( self::is_hex_value( $color ) ) {
			$color = '#' . ltrim( trim( $color ), '#' );
		}

		return $color;
	}

	/**
	 * Adjusts the brightness of a hex or rgba color value based on
	 * the number of steps provided.
	 *
	 * @since 1.0
	 * @since 2.2 Added support for rgba values.
	 * @param string $value A hex or rgba color value.
	 * @param int $steps The number of steps to adjust the color.
	 * @param string $type The type of adjustment to make. Either lighten, darken or reverse.
	 * @return string The adjusted value string.
	 */
	static public function adjust_brightness( $value, $steps, $type ) {
		$is_rgb = self::is_rgb_value( $value );

		// Don't attempt to adjust brightness on other value types
		if ( ! $is_rgb && ! self::is_hex_value( $value ) ) {
			return $value;
		}

		// Get rgb vars.
		if ( $is_rgb ) {
			$rgb = explode( ',', preg_replace( '/[a-z\(\)]/', '', $value ) );
			$r   = $rgb[0];
			$g   = $rgb[1];
			$b   = $rgb[2];
			$a   = count( $rgb ) > 3 ? $rgb[3] : false;
		} else {
			$rgb = self::hex_to_rgb( $value );
			$r   = $rgb['r'];
			$g   = $rgb['g'];
			$b   = $rgb['b'];
		}

		// Should we darken the color?
		if ( 'reverse' == $type && $r + $g + $b > 382 ) {
			$steps = -$steps;
		} elseif ( 'darken' == $type ) {
			$steps = -$steps;
		}

		// Adjust the rgb values.
		$steps = max( -255, min( 255, $steps ) );
		$r     = max( 0, min( 255, $r + $steps ) );
		$g     = max( 0, min( 255, $g + $steps ) );
		$b     = max( 0, min( 255, $b + $steps ) );

		// Return the adjusted color value.
		if ( $is_rgb ) {
			$value = false === $a ? "rgb($r,$g,$b)" : "rgba($r,$g,$b,$a)";
		} else {
			$r_hex = str_pad( dechex( $r ), 2, '0', STR_PAD_LEFT );
			$g_hex = str_pad( dechex( $g ), 2, '0', STR_PAD_LEFT );
			$b_hex = str_pad( dechex( $b ), 2, '0', STR_PAD_LEFT );
			$value = $r_hex . $g_hex . $b_hex;
		}

		return $value;
	}

	/**
	 * Returns a gradient value string. Must be passed a
	 * gradient setting array from a gradient field.
	 *
	 * @since 2.2
	 * @param array $setting
	 * @return string
	 */
	static public function gradient( $setting, $test = false ) {
		$gradient = '';
		$values   = array();

		if ( ! is_array( $setting ) ) {
			return $gradient;
		}

		$is_gradient_field_ok = isset( $setting['type'] )
			&& isset( $setting['angle'] )
			&& isset( $setting['position'] )
			&& isset( $setting['colors'] )
			&& isset( $setting['stops'] );

		if ( ! $is_gradient_field_ok ) {
			return $gradient;
		}

		/**
		 * There should be 2 colours here even if one is blank
		 * SD mode strips the blank one, we need to add it back
		 * Same goes for stops.
		 */
		if ( count( $setting['colors'] ) < 2 ) {
			$setting['colors'][] = '';
		}
		if ( count( $setting['stops'] ) < 2 ) {
			$setting['stops'][] = '';
		}

		foreach ( $setting['colors'] as $i => $color ) {
			$stop = $setting['stops'][ $i ];

			if ( empty( $color ) ) {
				if ( $test ) {
					return false;
				}
				$color = 'rgba(255,255,255,0)';
			}
			if ( ! strstr( $color, 'rgb' ) && ! strstr( $color, 'var' ) ) {
				$color = self::hex_or_rgb( $color );
			}
			if ( ! is_numeric( $stop ) ) {
				$stop = 0;
			}

			$values[] = $color . ' ' . $stop . '%';
		}

		$values = implode( ', ', $values );

		if ( 'linear' === $setting['type'] ) {
			if ( ! is_numeric( $setting['angle'] ) ) {
				$setting['angle'] = 0;
			}
			$gradient = 'linear-gradient(' . $setting['angle'] . 'deg, ' . $values . ')';
		} else {
			$gradient = 'radial-gradient(at ' . $setting['position'] . ', ' . $values . ')';
		}

		return $gradient;
	}

	/**
	 * Returns a shadow value string. Must be passed a
	 * shadow setting array from a shadow field.
	 *
	 * @since 2.2
	 * @param array $setting
	 * @return string
	 */
	static public function shadow( $setting ) {
		$shadow = '';

		if ( isset( $setting['color'] ) && '' !== $setting['color'] ) {

			if ( ! isset( $setting['horizontal'] ) || '' === $setting['horizontal'] ) {
				$setting['horizontal'] = 0;
			}
			if ( ! isset( $setting['vertical'] ) || '' === $setting['vertical'] ) {
				$setting['vertical'] = 0;
			}
			if ( ! isset( $setting['blur'] ) || '' === $setting['blur'] ) {
				$setting['blur'] = 0;
			}
			if ( isset( $setting['spread'] ) && '' === $setting['spread'] ) {
				$setting['spread'] = 0;
			}

			// Process color value
			$setting['color'] = self::hex_or_rgb( $setting['color'] );

			$shadow  = $setting['horizontal'] . 'px ';
			$shadow .= $setting['vertical'] . 'px ';
			$shadow .= $setting['blur'] . 'px ';

			if ( isset( $setting['spread'] ) ) {
				$shadow .= $setting['spread'] . 'px ';
			}

			$shadow .= $setting['color'];
		}

		return $shadow;
	}

	/**
	 * Get the raw rgba values for a color value
	 *
	 * @since 2.2
	 * @param String $color - hex or rgb value
	 * @return Array
	 */
	static public function rgba_values_for_color( $value = '' ) {
		$is_rgb = strstr( $value, 'rgb' );

		// Get rgb vars.
		if ( $is_rgb ) {
			$rgb = explode( ',', preg_replace( '/[a-z\(\)]/', '', $value ) );
			$r   = $rgb[0];
			$g   = $rgb[1];
			$b   = $rgb[2];
			$a   = count( $rgb ) > 3 ? $rgb[3] : false;
		} else {
			$rgb = self::hex_to_rgb( $value );
			$r   = $rgb['r'];
			$g   = $rgb['g'];
			$b   = $rgb['b'];
			$a   = 1;
		}
		if ( count( $rgb ) === 4 ) {
			$rgb = array_slice( $rgb, 0, 3 );
		}
		return array(
			'r'   => $r,
			'g'   => $g,
			'b'   => $b,
			'a'   => $a,
			'rgb' => 'rgb(' . implode( ',', $rgb ) . ')',
		);
	}
}
