<?php
/**
 * Class and methods to extend the WP_Image_Editor_Imagick class.
 *
 * @link https://ewww.io
 * @package EWWW_Image_Optimizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Extension of the WP_Image_Editor_Imagick class to auto-compress edited images.
 *
 * @see WP_Image_Editor_Imagick
 */
class EWWWIO_Imagick_Editor extends WP_Image_Editor_Imagick {

	/**
	 * GD Resource.
	 *
	 * @access protected
	 * @var resource|GdImage $ewww_image
	 */
	protected $ewww_image;

	/**
	 * Site (URL) for the plugin to use.
	 *
	 * @access protected
	 * @var string $site_url
	 */
	protected $modified = false;

	/**
	 * Type of PNG image: PNG8, PNG24, PNG32, or ''.
	 *
	 * @access protected
	 * @var string $png_color_depth
	 */
	protected $png_color_depth = '';

	/**
	 * Stores the information whether the image is indexed-color encoded.
	 *
	 * @access protected
	 * @var bool
	 */
	protected $indexed_color_encoded = false;

	/**
	 * Stores the information whether the image is indexed-color encoded.
	 *
	 * @access protected
	 * @var int
	 */
	protected $indexed_pixel_depth = false;

	/**
	 * How many colors are allowed for an image that is indexed-color encoded.
	 *
	 * @access protected
	 * @var int
	 */
	protected $indexed_max_colors = false;

	/**
	 * Whether an indexed grayscale image needs further processing to reduce the palette.
	 *
	 * @access protected
	 * @var int
	 */
	protected $indexed_grayscale_alpha_problem = false;

	/**
	 * Gets the bit depth for PNG images and checks for indexed-color mode.
	 *
	 * Access the file directly, in case Imagick fails to identify the number of colors in an indexed image.
	 *
	 * @since 7.7.0
	 * @since 8.1.5 Only used to get the bit depth when IM returns 8 and getImageColors() isn't working.
	 *
	 * @return int The bit depth for PNG images, 8 by default.
	 */
	protected function get_png_color_depth() {
		ewwwio_debug_message( '<b>' . __METHOD__ . '()</b>' );
		$bit_depth = 8;
		if ( 'image/png' !== $this->mime_type ) {
			return $bit_depth;
		}
		if ( wp_is_stream( $this->file ) ) {
			return $bit_depth;
		}
		if ( ! is_file( $this->file ) ) {
			return $bit_depth;
		}
		if ( filesize( $this->file ) < 24 ) {
			return $bit_depth;
		}

		$file_handle = fopen( $this->file, 'rb' );

		if ( ! $file_handle ) {
			return $bit_depth;
		}

		$png_header = fread( $file_handle, 4 );
		if ( chr( 0x89 ) . 'PNG' !== $png_header ) {
			return $bit_depth;
		}

		// Move forward 8 bytes.
		fread( $file_handle, 8 );
		$png_ihdr = fread( $file_handle, 4 );

		// Make sure we have an IHDR.
		if ( 'IHDR' !== $png_ihdr ) {
			return $bit_depth;
		}

		// Skip past the dimensions.
		$dimensions = fread( $file_handle, 8 );

		// Bit depth: 1 byte
		// Bit depth is a single-byte integer giving the number of bits per sample or
		// per palette index (not per pixel).
		//
		// Valid values are 1, 2, 4, 8, and 16, although not all values are allowed for all color types.
		$bit_depth = ord( (string) fread( $file_handle, 1 ) );

		// Color type is a single-byte integer that describes the interpretation of the image data.
		// Color type codes represent sums of the following values:
		// 1 (palette used), 2 (color used), and 4 (alpha channel used).
		// The valid color types are:
		// 0 => Grayscale
		// 2 => Truecolor
		// 3 => Indexed
		// 4 => Greyscale with alpha
		// 6 => Truecolour with alpha
		//
		// Valid values are 0, 2, 3, 4, and 6.
		$color_type = ord( (string) fread( $file_handle, 1 ) );

		fclose( $file_handle );

		ewwwio_debug_message( "detected bit depth $bit_depth, color type $color_type" );
		return $bit_depth;
	}

	/**
	 * Resizes current image.
	 * Wraps _resize, since _resize returns a GD Resource.
	 *
	 * At minimum, either a height or width must be provided.
	 * If one of the two is set to null, the resize will
	 * maintain aspect ratio according to the provided dimension.
	 *
	 * @since 4.6.0
	 *
	 * @param int|null $max_w Image width.
	 * @param int|null $max_h Image height.
	 * @param bool     $crop Optional. Scale by default, crop if true.
	 * @return true|WP_Error
	 */
	public function resize( $max_w, $max_h, $crop = false ) {
		ewwwio_debug_message( '<b>wp_image_editor_imagick::' . __FUNCTION__ . '()</b>' );
		if ( (int) $this->size['width'] === (int) $max_w && (int) $this->size['height'] === (int) $max_h ) {
			return true;
		}

		$dims = image_resize_dimensions( $this->size['width'], $this->size['height'], $max_w, $max_h, $crop );
		if ( ! $dims ) {
			return new WP_Error( 'error_getting_dimensions', __( 'Could not calculate resized image dimensions' ) ); // phpcs:ignore WordPress.WP.I18n.MissingArgDomain
		}
		list( $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h ) = $dims;

		$resized = $this->_resize( $dims, $crop );

		if ( is_string( $resized ) ) {
			$this->ewww_image = $resized;
			return $resized;
		} elseif ( is_wp_error( $resized ) ) {
			return $resized;
		}
		return $this->update_size( $dst_w, $dst_h );
	}

	/**
	 * Resizes current image.
	 *
	 * Uses gifsicle to preserve GIF animations.
	 *
	 * @since 4.6.0
	 *
	 * @param array $dims {
	 *     All parameters necessary for resizing.
	 *
	 *     @type int $dst_x X-coordinate of destination image (usually 0).
	 *     @type int $dst_y Y-coordinate of destination image (usually 0).
	 *     @type int $src_x X-coordinate of source image (usually 0 unless cropping).
	 *     @type int $src_y Y-coordinate of source image (usually 0 unless cropping).
	 *     @type int $dst_w Desired image width.
	 *     @type int $dst_h Desired image height.
	 *     @type int $src_w Source width.
	 *     @type int $src_h Source height.
	 * }
	 * @param bool  $crop Should we crop or should we scale.
	 * @return bool|WP_Error
	 */
	protected function _resize( $dims, $crop ) {
		ewwwio_debug_message( '<b>wp_image_editor_imagick::' . __FUNCTION__ . '()</b>' );
		list( $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h ) = $dims;
		if ( defined( 'EWWWIO_EDITOR_AGR' ) && ! EWWWIO_EDITOR_AGR ) {
			ewwwio_debug_message( 'AGR disabled' );
			if ( $crop ) {
				return $this->crop( $src_x, $src_y, $src_w, $src_h, $dst_w, $dst_h );
			}
			return $this->thumbnail_image( $dst_w, $dst_h );
		}
		if ( defined( 'EWWWIO_EDITOR_BETTER_RESIZE' ) && ! EWWW_IO_EDITOR_BETTER_RESIZE ) {
			ewwwio_debug_message( 'API resize disabled' );
			if ( $crop ) {
				return $this->crop( $src_x, $src_y, $src_w, $src_h, $dst_w, $dst_h );
			}
			return $this->thumbnail_image( $dst_w, $dst_h );
		}
		$return_parent = false; // An indicator for whether we should short-circuit and use the parent thumbnail_image method.
		$ewww_status   = get_transient( 'ewww_image_optimizer_cloud_status' );
		if (
			'image/gif' === $this->mime_type &&
			! ewww_image_optimizer_get_option( 'ewww_image_optimizer_cloud_key' )
		) {
			$gifsicle_path = ewwwio()->local->get_path( 'gifsicle' );
			if ( empty( $gifsicle_path ) ) {
				ewwwio_debug_message( 'no gifsicle to resize an animated GIF' );
				$return_parent = true;
			}
		}
		// If this is a GIF, and we have no gifsicle, and there's an API key, but the status isn't 'great'.
		if (
			'image/gif' === $this->mime_type &&
			empty( $gifsicle_path ) &&
			false === strpos( $ewww_status, 'great' ) &&
			ewww_image_optimizer_get_option( 'ewww_image_optimizer_cloud_key' ) &&
			! ewww_image_optimizer_cloud_verify( ewww_image_optimizer_get_option( 'ewww_image_optimizer_cloud_key' ) )
		) {
			ewwwio_debug_message( 'no API key to resize an animated GIF' );
			$return_parent = true;
		}
		// If this is some other image, and there's an API key, but the status isn't 'great'.
		if (
			'image/gif' !== $this->mime_type &&
			false === strpos( $ewww_status, 'great' ) &&
			ewww_image_optimizer_get_option( 'ewww_image_optimizer_cloud_key' )
		) {
			ewwwio_debug_message( 'no API to resize the image' );
			$return_parent = true;
		}
		// If this isn't a GIF, or if the GIF isn't animated, then bail.
		if ( 'image/gif' !== $this->mime_type || ! ewww_image_optimizer_is_animated( $this->file ) ) {
			ewwwio_debug_message( 'not an animated GIF' );
			$return_parent = true;
		}
		if ( $this->modified ) {
			ewwwio_debug_message( 'image already altered, leave it alone' );
			$return_parent = true;
		}
		if (
			! $this->file ||
			ewww_image_optimizer_stream_wrapped( $this->file ) ||
			0 === strpos( $this->file, 'http' ) ||
			0 === strpos( $this->file, 'ftp' ) ||
			! ewwwio_is_file( $this->file )
		) {
			ewwwio_debug_message( 'could not load original file, or remote path detected' );
			$return_parent = true;
		}
		if ( $return_parent ) {
			if ( $crop ) {
				return $this->crop( $src_x, $src_y, $src_w, $src_h, $dst_w, $dst_h );
			}
			return $this->thumbnail_image( $dst_w, $dst_h );
		}
		$resize_result = ewww_image_optimizer_better_resize( $this->file, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h );
		if ( is_wp_error( $resize_result ) ) {
			return $resize_result;
		}

		$imagick  = new Imagick();
		$new_size = array();
		try {
			$imagick->clear();
			$imagick->readImageBlob( $resize_result );
			if ( function_exists( 'getimagesizefromstring' ) ) {
				$gd_size_info = getimagesizefromstring( $resize_result );
			}
			if ( ! empty( $gd_size_info ) ) {
				$new_size['width']  = $gd_size_info[0];
				$new_size['height'] = $gd_size_info[1];
			} else {
				$new_size = $imagick->getImageGeometry();
			}
			ewwwio_debug_message( 'new size: ' . implode( 'x', $new_size ) );
		} catch ( Exception $e ) {
			return new WP_Error( 'image_resize_error', $e->getMessage(), $this->file );
		}
		$this->image = $imagick;
		if ( empty( $new_size ) ) {
			return new WP_Error( 'image_resize_error', __( 'Image resize failed.', 'ewww-image-optimizer' ), );
		}
		$this->update_size( $new_size['width'], $new_size['height'] );
		return $resize_result;
	}

	/**
	 * Efficiently resize the current image
	 *
	 * This is a WordPress specific implementation of Imagick::thumbnailImage(),
	 * which resizes an image to given dimensions and removes any associated profiles.
	 *
	 * @since 4.5.0
	 *
	 * @param int    $dst_w       The destination width.
	 * @param int    $dst_h       The destination height.
	 * @param string $filter_name Optional. The Imagick filter to use when resizing. Default 'FILTER_TRIANGLE'.
	 * @param bool   $strip_meta  Optional. Strip all profiles, excluding color profiles, from the image. Default true.
	 * @return void|WP_Error
	 */
	protected function thumbnail_image( $dst_w, $dst_h, $filter_name = 'FILTER_TRIANGLE', $strip_meta = true ) {
		ewwwio_debug_message( '<b>wp_image_editor_imagick::' . __FUNCTION__ . '()</b>' );
		$allowed_filters = array(
			'FILTER_POINT',
			'FILTER_BOX',
			'FILTER_TRIANGLE',
			'FILTER_HERMITE',
			'FILTER_HANNING',
			'FILTER_HAMMING',
			'FILTER_BLACKMAN',
			'FILTER_GAUSSIAN',
			'FILTER_QUADRATIC',
			'FILTER_CUBIC',
			'FILTER_CATROM',
			'FILTER_MITCHELL',
			'FILTER_LANCZOS',
			'FILTER_BESSEL',
			'FILTER_SINC',
		);

		$filter_name = apply_filters( 'eio_image_resize_filter', $filter_name, $dst_w, $dst_h );
		ewwwio_debug_message( "using resize filter $filter_name" );
		/**
		 * Set the filter value if '$filter_name' name is in the allowed list and the related
		 * Imagick constant is defined or fall back to the default filter.
		 */
		if ( in_array( $filter_name, $allowed_filters, true ) && defined( 'Imagick::' . $filter_name ) ) {
			$filter = constant( 'Imagick::' . $filter_name );
		} else {
			$filter = defined( 'Imagick::FILTER_TRIANGLE' ) ? Imagick::FILTER_TRIANGLE : false;
		}

		if ( 'image/png' === $this->mime_type ) {
			$png_color_type      = '6';
			$png_has_alpha       = false;
			$alpha_channel_depth = 1;
			if ( method_exists( $this->image, 'getImageProperty' ) ) {
				$png_color_type = $this->image->getImageProperty( 'png:IHDR.color-type-orig' );
			}
			ewwwio_debug_message( 'this image is type: ' . $this->image->getImageType() );
			ewwwio_debug_message( 'this image has color type: ' . $png_color_type );
			if ( '3' === $png_color_type ) {
				$current_colors = 500; // Fail-safe for more than any indexed PNG could have.
				if ( is_callable( array( $this->image, 'getImageColors' ) ) ) {
					$current_colors = $this->image->getImageColors();
					ewwwio_debug_message( "image has $current_colors colors" );
				}
				$bit_depth = 8;
				if ( is_callable( array( $this->image, 'getImageDepth' ) ) ) {
					$bit_depth = (int) $this->image->getImageDepth();
					// Only use the direct file method if the current colors is higher than normal.
					// We'll need an accurate bit depth in that case, to make sure we properly set the maximum.
					if ( $bit_depth >= 8 && $current_colors > 256 ) {
						$bit_depth = $this->get_png_color_depth();
					}
				}
				switch ( $bit_depth ) {
					case 8:
						$max_colors = 255;
						break;
					case 4:
						$max_colors = 16;
						break;
					case 2:
						$max_colors = 4;
						break;
					case 1:
						$max_colors = 2;
						break;
					default:
						$max_colors = 255;
				}
				$this->indexed_max_colors = min( $max_colors, $current_colors );
				if ( is_callable( array( $this->image, 'getImageAlphaChannel' ) ) && $this->image->getImageAlphaChannel() ) {
					ewwwio_debug_message( 'image has alpha channel' );
					$png_has_alpha = true;
					if ( is_callable( array( $this->image, 'getImageChannelDepth' ) ) && defined( 'Imagick::CHANNEL_ALPHA' ) ) {
						$alpha_channel_depth = $this->image->getImageChannelDepth( Imagick::CHANNEL_ALPHA );
						ewwwio_debug_message( "alpha channel depth is $alpha_channel_depth" );
					}
				}
				ewwwio_debug_message( "indexed image with pixel depth {$bit_depth} limiting to {$this->indexed_max_colors} colors" );
			}
		}

		/**
		 * Filters whether to strip metadata from images when they're resized.
		 *
		 * This filter only applies when resizing using the Imagick editor since GD
		 * always strips profiles by default.
		 *
		 * @since 4.5.0
		 *
		 * @param bool $strip_meta Whether to strip image metadata during resizing. Default true.
		 */
		if ( apply_filters( 'image_strip_meta', $strip_meta ) ) {
			$this->strip_meta(); // Fail silently if not supported.
		}

		try {
			/*
			 * To be more efficient, resample large images to 5x the destination size before resizing
			 * whenever the output size is less that 1/3 of the original image size (1/3^2 ~= .111),
			 * unless we would be resampling to a scale smaller than 128x128.
			 */
			if ( is_callable( array( $this->image, 'sampleImage' ) ) ) {
				$resize_ratio  = ( $dst_w / $this->size['width'] ) * ( $dst_h / $this->size['height'] );
				$sample_factor = 5;

				if ( $resize_ratio < .111 && ( $dst_w * $sample_factor > 128 && $dst_h * $sample_factor > 128 ) ) {
					$this->image->sampleImage( $dst_w * $sample_factor, $dst_h * $sample_factor );
				}
			}

			/*
			 * Use resizeImage() when it's available and a valid filter value is set.
			 * Otherwise, fall back to the scaleImage() method for resizing, which
			 * results in better image quality over resizeImage() with default filter
			 * settings and retains backward compatibility with pre 4.5 functionality.
			 */
			if ( is_callable( array( $this->image, 'resizeImage' ) ) && $filter ) {
				$this->image->setOption( 'filter:support', '2.0' );
				$this->image->resizeImage( $dst_w, $dst_h, $filter, 1 );
			} else {
				$this->image->scaleImage( $dst_w, $dst_h );
			}

			// Set appropriate quality settings after resizing.
			if ( 'image/jpeg' === $this->mime_type ) {
				if ( apply_filters( 'eio_use_adaptive_sharpen', false, $dst_w, $dst_h ) && is_callable( array( $this->image, 'adaptiveSharpenImage' ) ) ) {
					$radius = apply_filters( 'eio_adaptive_sharpen_radius', 0, $dst_w, $dst_h );
					$sigma  = apply_filters( 'eio_adaptive_sharpen_sigma', 1, $dst_w, $dst_h );
					ewwwio_debug_message( "running adaptiveSharpenImage( $radius, $sigma )" );
					$this->image->adaptiveSharpenImage( $radius, $sigma );
				} elseif ( is_callable( array( $this->image, 'unsharpMaskImage' ) ) ) {
					$radius    = apply_filters( 'eio_sharpen_radius', 0.25, $dst_w, $dst_h );
					$sigma     = apply_filters( 'eio_sharpen_sigma', 0.25, $dst_w, $dst_h );
					$amount    = apply_filters( 'eio_sharpen_amount', 8, $dst_w, $dst_h );
					$threshold = apply_filters( 'eio_sharpen_threshold', 0.065, $dst_w, $dst_h );
					ewwwio_debug_message( "running unsharpMaskImage( $radius, $sigma, $amount, $threshold )" );
					$this->image->unsharpMaskImage( $radius, $sigma, $amount, $threshold );
					// $this->image->unsharpMaskImage( 0.25, 0.25, 8, 0.065 ); // core WP defaults.
					// $this->image->unsharpMaskImage( 0, 0.4, 1.2, 0.01 ); // values from the Better Images plugin.
				}

				$this->image->setOption( 'jpeg:fancy-upsampling', 'off' );
			}

			if ( 'image/png' === $this->mime_type ) {
				$this->image->setOption( 'png:compression-filter', '5' );
				$this->image->setOption( 'png:compression-level', '9' );
				$this->image->setOption( 'png:compression-strategy', '1' );
				if ( '3' === $png_color_type ) {
					if ( $png_has_alpha ) {
						$this->image->setOption( 'png:include-chunk', 'tRNS' );
					} else {
						$this->image->setOption( 'png:exclude-chunk', 'all' );
					}
					if ( ! empty( $this->indexed_max_colors ) && ! ewww_image_optimizer_pngquant_reduce_available() ) {
						ewwwio_debug_message( "doing quantizeImage on $this->file ($dst_w,$dst_h) to reduce colors from " . $this->image->getImageColors() . " to $this->indexed_max_colors" );
						$this->image->quantizeImage( $this->indexed_max_colors, $this->image->getColorspace(), 0, false, false );
						ewwwio_debug_message( "originally we had $current_colors colors, and now we have " . $this->image->getImageColors() );
						/**
						 * ImageMagick likes to convert gray indexed images to grayscale.
						 * So, if the colorspace has changed to 'gray', use the png8 format
						 * to ensure it stays indexed.
						 */
						if ( $alpha_channel_depth > 1 && Imagick::COLORSPACE_GRAY === $this->image->getImageColorspace() ) {
							ewwwio_debug_message( 'COLORSPACE_GRAY found, but alpha channel is too complicated for png8 mode' );
							$this->indexed_grayscale_alpha_problem = true;
						} elseif ( Imagick::COLORSPACE_GRAY === $this->image->getImageColorspace() ) {
							ewwwio_debug_message( 'COLORSPACE_GRAY found, setting png:format = png8' );
							$this->image->setOption( 'png:format', 'png8' );
						}
					}
				}
			}

			/*
			 * If alpha channel is not defined, set it opaque.
			 *
			 * Note that Imagick::getImageAlphaChannel() is only available if Imagick
			 * has been compiled against ImageMagick version 6.4.0 or newer.
			 */
			if ( is_callable( array( $this->image, 'getImageAlphaChannel' ) )
				&& is_callable( array( $this->image, 'setImageAlphaChannel' ) )
				&& defined( 'Imagick::ALPHACHANNEL_UNDEFINED' )
				&& defined( 'Imagick::ALPHACHANNEL_OPAQUE' )
			) {
				if ( $this->image->getImageAlphaChannel() === Imagick::ALPHACHANNEL_UNDEFINED ) {
					$this->image->setImageAlphaChannel( Imagick::ALPHACHANNEL_OPAQUE );
				}
			}

			// Limit the bit depth of resized images to 8 bits per channel.
			if ( is_callable( array( $this->image, 'getImageDepth' ) ) && is_callable( array( $this->image, 'setImageDepth' ) ) ) {
				if ( 8 < $this->image->getImageDepth() ) {
					$this->image->setImageDepth( 8 );
				}
			}

			if ( is_callable( array( $this->image, 'setInterlaceScheme' ) ) && defined( 'Imagick::INTERLACE_NO' ) ) {
				$this->image->setInterlaceScheme( Imagick::INTERLACE_NO );
			}
		} catch ( Exception $e ) {
			return new WP_Error( 'image_resize_error', $e->getMessage() );
		}
	}

	/**
	 * Resize multiple images from a single source.
	 *
	 * @since 4.6.0
	 *
	 * @param array $sizes {
	 *     An array of image size arrays. Default sizes are 'small', 'medium', 'medium_large', 'large'.
	 *
	 *     Either a height or width must be provided.
	 *     If one of the two is set to null, the resize will
	 *     maintain aspect ratio according to the provided dimension.
	 *     Likewise, if crop is false, aspect ratio will also be preserved.
	 *
	 *     @type array $size {
	 *         Array of height, width values, and whether to crop.
	 *
	 *         @type int  $width  Image width. Optional if `$height` is specified.
	 *         @type int  $height Image height. Optional if `$width` is specified.
	 *         @type bool $crop   Optional. Whether to crop the image. Default false.
	 *     }
	 * }
	 * @return array An array of resized images' metadata by size.
	 */
	public function multi_resize( $sizes ) {
		ewwwio_debug_message( '<b>wp_image_editor_imagick::' . __FUNCTION__ . '()</b>' );
		$metadata   = array();
		$orig_size  = $this->size;
		$orig_image = $this->image->getImage();
		foreach ( $sizes as $size => $size_data ) {
			if ( ! $this->image ) {
				$this->image = $orig_image->getImage();
			}
			if ( ! isset( $size_data['width'] ) && ! isset( $size_data['height'] ) ) {
				continue;
			}
			if ( ! isset( $size_data['width'] ) ) {
				$size_data['width'] = null;
			}
			if ( ! isset( $size_data['height'] ) ) {
				$size_data['height'] = null;
			}
			if ( ! isset( $size_data['crop'] ) ) {
				$size_data['crop'] = false;
			}

			$resize_result = $this->resize( $size_data['width'], $size_data['height'], $size_data['crop'] );
			$duplicate     = ( (int) $orig_size['width'] === (int) $size_data['width'] && (int) $orig_size['height'] === (int) $size_data['height'] );

			if ( ! is_wp_error( $resize_result ) && ! $duplicate ) {
				if ( is_string( $resize_result ) ) {
					$resized = $this->_save_file_from_string( $resize_result );
					unset( $resize_result );
					// Note sure this bit is necessary, but just to be safe.
					$this->image->clear();
					$this->image->destroy();
					$this->image = null;
				} else {
					$resized = $this->_save( $this->image );
					$this->image->clear();
					$this->image->destroy();
					$this->image = null;
				}
				if ( ! is_wp_error( $resized ) && $resized ) {
					unset( $resized['path'] );
					$metadata[ $size ] = $resized;
				}
			}
			$this->size = $orig_size;
		}
		$this->image = $orig_image;
		return $metadata;
	}

	/**
	 * Crops Image.
	 *
	 * Currently does nothing more than call the parent and let us know the image is modified.
	 *
	 * @since 4.6.0
	 *
	 * @param int  $src_x The start x position to crop from.
	 * @param int  $src_y The start y position to crop from.
	 * @param int  $src_w The width to crop.
	 * @param int  $src_h The height to crop.
	 * @param int  $dst_w Optional. The destination width.
	 * @param int  $dst_h Optional. The destination height.
	 * @param bool $src_abs Optional. If the source crop points are absolute.
	 * @return bool|WP_Error
	 */
	public function crop( $src_x, $src_y, $src_w, $src_h, $dst_w = null, $dst_h = null, $src_abs = false ) {
		$this->modified = 'crop';
		return parent::crop( $src_x, $src_y, $src_w, $src_h, $dst_w, $dst_h, $src_abs );
	}

	/**
	 * Rotates current image counter-clockwise by $angle.
	 *
	 * Currently does nothing more than call the parent and let us know the image is modified.
	 *
	 * @since 4.6.0
	 *
	 * @param float $angle The number of degrees for rotation.
	 * @return true|WP_Error
	 */
	public function rotate( $angle ) {
		$this->modified = 'rotate';
		return parent::rotate( $angle );
	}

	/**
	 * Flips current image.
	 *
	 * Currently does nothing more than call the parent and let us know the image is modified.
	 *
	 * @since 4.6.0
	 *
	 * @param bool $horz Flip along Horizontal Axis.
	 * @param bool $vert Flip along Vertical Axis.
	 * @return true|WP_Error
	 */
	public function flip( $horz, $vert ) {
		$this->modified = 'flip';
		return parent::flip( $horz, $vert );
	}

	/**
	 * Saves a file from the EWWW IO API-enabled image editor.
	 *
	 * @since 4.6.0
	 *
	 * @param string $image A string containing the image contents.
	 * @param string $filename Optional. The name of the file to be saved to.
	 * @param string $mime_type Optional. The mimetype of the file.
	 * @return WP_Error|array The full path, base filename, and mimetype.
	 */
	protected function _save_file_from_string( $image, $filename = null, $mime_type = null ) {
		ewwwio_debug_message( '<b>wp_image_editor_imagick::' . __FUNCTION__ . '()</b>' );
		list( $filename, $extension, $mime_type ) = $this->get_output_format( $filename, $mime_type );
		if ( ! $filename ) {
			$filename = $this->generate_filename( null, null, $extension );
		}
		if ( wp_is_stream( $filename ) ) {
			unset( $this->ewww_image );
			return parent::_save( $this->image, $filename, $mime_type );
		}
		if ( ! is_string( $image ) ) {
			unset( $this->ewww_image );
			return parent::_save( $image, $filename, $mime_type );
		}
		if ( apply_filters( 'ewwwio_editor_prevent_overwrite', true ) && ewwwio_is_file( $filename ) ) {
			ewwwio_debug_message( "detected existing file: $filename" );
			$current_size = wp_getimagesize( $filename );
			if ( $current_size && (int) $this->size['width'] === (int) $current_size[0] && (int) $this->size['height'] === (int) $current_size[1] ) {
				ewwwio_debug_message( "existing file has same dimensions, not saving $filename" );
				return array(
					'path'      => $filename,
					'file'      => wp_basename( apply_filters( 'image_make_intermediate_size', $filename ) ),
					'width'     => $this->size['width'],
					'height'    => $this->size['height'],
					'mime-type' => $mime_type,
				);
			}
		}
		// Write out the image (substitute for $this->make_image()).
		wp_mkdir_p( dirname( $filename ) );
		$result = file_put_contents( $filename, $image );
		if ( $result ) {
			ewwwio_debug_message( "image editor (imagick-api-enhanced) saved: $filename" );
			if ( is_writable( $filename ) ) {
				$stat  = stat( dirname( $filename ) );
				$perms = $stat['mode'] & 0000666; // Same permissions as parent folder with executable bits stripped.
				ewwwio_chmod( $filename, $perms );
			}
			ewwwio_memory( __FUNCTION__ );
			return array(
				'path'      => $filename,
				'file'      => wp_basename( apply_filters( 'image_make_intermediate_size', $filename ) ),
				'width'     => $this->size['width'],
				'height'    => $this->size['height'],
				'mime-type' => $mime_type,
			);
		}
		ewwwio_memory( __FUNCTION__ );
		return new WP_Error( 'image_save_error', __( 'Image Editor Save Failed' ) ); // phpcs:ignore WordPress.WP.I18n.MissingArgDomain
	}

	/**
	 * Saves a file from the image editor.
	 *
	 * @since 1.7.0
	 *
	 * @param resource $image An Imagick image object. Not actually used for some reason.
	 * @param string   $filename Optional. The name of the file to be saved to.
	 * @param string   $mime_type Optional. The mimetype of the file.
	 * @return WP_Error| array The full path, base filename, width, height, and mimetype.
	 */
	protected function _save( $image, $filename = null, $mime_type = null ) {
		ewwwio_debug_message( '<b>wp_image_editor_imagick::' . __FUNCTION__ . '()</b>' );

		// If something is in the 'ewww_image' property, meaning this is likely a GIF that has been
		// resized by EWWW IO, and the image wasn't otherwise cropped, rotated, etc.
		if ( ! empty( $this->ewww_image ) && ! $this->modified ) {
			return $this->_save_file_from_string( $this->ewww_image, $filename, $mime_type );
		}

		list( $filename, $extension, $mime_type ) = $this->get_output_format( $filename, $mime_type );
		if ( ! $filename ) {
			$filename = $this->generate_filename( null, null, $extension );
		}
		\ewwwio_debug_message( "saving $filename" );

		if ( apply_filters( 'ewwwio_editor_prevent_overwrite', true ) && ewwwio_is_file( $filename ) ) {
			ewwwio_debug_message( "detected existing file: $filename" );
			$current_size = wp_getimagesize( $filename );
			if ( $current_size && (int) $this->size['width'] === (int) $current_size[0] && (int) $this->size['height'] === (int) $current_size[1] ) {
				ewwwio_debug_message( "existing file has same dimensions, not saving $filename" );
				return array(
					'path'      => $filename,
					'file'      => wp_basename( apply_filters( 'image_make_intermediate_size', $filename ) ),
					'width'     => $this->size['width'],
					'height'    => $this->size['height'],
					'mime-type' => $mime_type,
				);
			}
		}

		$sharp_yuv = defined( 'EIO_WEBP_SHARP_YUV' ) && EIO_WEBP_SHARP_YUV ? true : false;
		if ( empty( $sharp_yuv ) && \ewww_image_optimizer_get_option( 'ewww_image_optimizer_sharpen' ) ) {
			$sharp_yuv = true;
		}
		$profiles = array();
		try {
			// Store original format.
			$orig_format = $this->image->getImageFormat();
			\ewwwio_debug_message( "original format is $orig_format" );

			if ( 'image/webp' === $mime_type ) {
				if ( ewww_image_optimizer_get_option( 'ewww_image_optimizer_metadata_remove' ) ) {
					// Getting possible color profiles.
					$profiles = $this->image->getImageProfiles( 'icc', true );
				}
				\ewwwio_debug_message( 'checking colorspace for WebP (in case of incompatible CMYK)' );
				$color = $this->image->getImageColorspace();
				\ewwwio_debug_message( "color space is $color" );
				if ( Imagick::COLORSPACE_CMYK === $color ) {
					\ewwwio_debug_message( 'found CMYK image' );
					if ( \ewwwio_is_file( EWWW_IMAGE_OPTIMIZER_PLUGIN_PATH . 'vendor/icc/sRGB2014.icc' ) ) {
						\ewwwio_debug_message( 'adding sRGB2014.icc profile' );
						$icc_profile = file_get_contents( EWWW_IMAGE_OPTIMIZER_PLUGIN_PATH . 'vendor/icc/sRGB2014.icc' );
						$this->image->profileImage( 'icc', $icc_profile );
					}
					\ewwwio_debug_message( 'attempting SRGB transform' );
					$this->image->transformImageColorspace( Imagick::COLORSPACE_SRGB );
					\ewwwio_debug_message( 'removing icc profile' );
					$this->image->setImageProfile( '*', null );
					$profiles = array();
				}
			}

			$this->image->setImageFormat( strtoupper( $this->get_extension( $mime_type ) ) );

			if ( 'image/webp' === $mime_type ) {
				if ( $sharp_yuv ) {
					\ewwwio_debug_message( 'enabling sharp_yuv' );
					$this->image->setOption( 'webp:use-sharp-yuv', 'true' );
				}

				if ( \ewww_image_optimizer_get_option( 'ewww_image_optimizer_metadata_remove' ) ) {
					\ewwwio_debug_message( 'removing meta' );
					$this->image->stripImage();
					if ( ! empty( $profiles ) ) {
						\ewwwio_debug_message( 'adding color profile to WebP' );
						$this->image->profileImage( 'icc', $profiles['icc'] );
					}
				}
			}
		} catch ( Exception $e ) {
			return new WP_Error( 'image_save_error', $e->getMessage(), $filename );
		}

		if ( method_exists( $this->image, 'setInterlaceScheme' )
			&& method_exists( $this->image, 'getInterlaceScheme' )
			&& defined( 'Imagick::INTERLACE_PLANE' )
		) {
			$orig_interlace = $this->image->getInterlaceScheme();

			/** This filter is documented in wp-includes/class-wp-image-editor-gd.php */
			if ( apply_filters( 'image_save_progressive', false, $mime_type ) ) {
				$this->image->setInterlaceScheme( Imagick::INTERLACE_PLANE ); // True - line interlace output.
			} else {
				$this->image->setInterlaceScheme( Imagick::INTERLACE_NO ); // False - no interlace output.
			}
		}

		$write_image_result = $this->write_image( $this->image, $filename );
		if ( is_wp_error( $write_image_result ) ) {
			return $write_image_result;
		}

		try {
			// Reset original format.
			$this->image->setImageFormat( $orig_format );

			if ( isset( $orig_interlace ) ) {
				$this->image->setInterlaceScheme( $orig_interlace );
			}
		} catch ( Exception $e ) {
			return new WP_Error( 'image_save_error', $e->getMessage(), $filename );
		}

		// Set correct file permissions.
		$stat  = stat( dirname( $filename ) );
		$perms = $stat['mode'] & 0000666; // Same permissions as parent folder, strip off the executable bits.
		chmod( $filename, $perms );

		if ( ewwwio_is_file( $filename ) ) {
			if ( $this->indexed_max_colors ) {
				ewwwio_debug_message( "reducing to $this->indexed_max_colors colors" );
				ewww_image_optimizer_reduce_palette( $filename, $this->indexed_max_colors, $this->indexed_grayscale_alpha_problem );
			}

			global $ewww_preempt_editor;
			if ( empty( $ewww_preempt_editor ) && defined( 'EWWW_IMAGE_OPTIMIZER_ENABLE_EDITOR' ) && EWWW_IMAGE_OPTIMIZER_ENABLE_EDITOR ) {
				ewww_image_optimizer( $filename );
			}
			ewwwio_debug_message( "image editor (imagick) saved: $filename" );
			$image_size = ewww_image_optimizer_filesize( $filename );
			ewwwio_debug_message( "image editor size: $image_size" );
		}

		return array(
			'path'      => $filename,
			/** This filter is documented in wp-includes/class-wp-image-editor-gd.php */
			'file'      => wp_basename( apply_filters( 'image_make_intermediate_size', $filename ) ),
			'width'     => $this->size['width'],
			'height'    => $this->size['height'],
			'mime-type' => $mime_type,
			'filesize'  => wp_filesize( $filename ),
		);
	}

	/**
	 * Writes an image to a file or stream.
	 *
	 * @since 8.1.0
	 *
	 * @param Imagick $image An Imagick image object.
	 * @param string  $filename The destination filename or stream URL.
	 * @return true|WP_Error
	 */
	private function write_image( $image, $filename ) {
		if ( wp_is_stream( $filename ) ) {
			/*
			 * Due to reports of issues with streams with `Imagick::writeImageFile()` and `Imagick::writeImage()`, copies the blob instead.
			 * Checks for exact type due to: https://www.php.net/manual/en/function.file-put-contents.php
			 */
			if ( file_put_contents( $filename, $image->getImageBlob() ) === false ) {
				return new WP_Error(
					'image_save_error',
					sprintf(
						/* translators: %s: PHP function name. */
						__( '%s failed while writing image to stream.', 'ewww-image-optimizer' ),
						'<code>file_put_contents()</code>'
					),
					$filename
				);
			} else {
				return true;
			}
		} else {
			$dirname = dirname( $filename );

			if ( ! wp_mkdir_p( $dirname ) ) {
				return new WP_Error(
					'image_save_error',
					sprintf(
						/* translators: %s: Directory path. */
						__( 'Unable to create directory %s. Is its parent directory writable by the server?', 'ewww-image-optimizer' ),
						esc_html( $dirname )
					)
				);
			}

			try {
				return $image->writeImage( $filename );
			} catch ( Exception $e ) {
				return new WP_Error( 'image_save_error', $e->getMessage(), $filename );
			}
		}
	}
}
