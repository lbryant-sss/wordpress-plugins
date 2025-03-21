<?php
/**
 * Edit images integration for the plugin, including backup and restore support.
 *
 * @package webp-uploads
 *
 * @since 1.0.0
 */

// @codeCoverageIgnoreStart
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
// @codeCoverageIgnoreEnd

/**
 * Adds sources to metadata for an attachment.
 *
 * @since 1.0.0
 *
 * @phpstan-param array{
 *      width: int,
 *      height: int,
 *      file: string,
 *      sizes: array<string, array{ file: string, width: int, height: int, 'mime-type': string, sources?: array<string, array{ file: string, filesize: int }> }>,
 *      image_meta: array<string, mixed>,
 *      filesize: int,
 *      sources?: array<string, array{ file: string, filesize: int }>,
 *      original_image?: string
 * } $metadata
 * @phpstan-param array<string, array{ file: string, path: string }> $main_images
 * @phpstan-param array<string, array<string, array{ file: string }>> $subsized_images
 *
 * @param array    $metadata              Metadata of the attachment.
 * @param string[] $valid_mime_transforms List of valid mime transforms for current image mime type.
 * @param array    $main_images           Path of all main image files of all mime types.
 * @param array    $subsized_images       Path of all subsized image file of all mime types.
 *
 * @return array{
 *     width: int,
 *     height: int,
 *     file: string,
 *     sizes: array<string, array{ file: string, width: int, height: int, 'mime-type': string, sources?: array<string, array{ file: string, filesize: int }> }>,
 *     image_meta: array<string, mixed>,
 *     filesize: int,
 *     original_image?: string,
 *     sources?: array<string, array{ file: string, filesize: int }>
 * } Metadata with sources added.
 */
function webp_uploads_update_sources( array $metadata, array $valid_mime_transforms, array $main_images, array $subsized_images ): array {
	foreach ( $valid_mime_transforms as $targeted_mime ) {
		// Make sure the path and file exists as those values are required.
		$image_directory = null;
		if ( isset( $main_images[ $targeted_mime ]['path'], $main_images[ $targeted_mime ]['file'] ) && file_exists( $main_images[ $targeted_mime ]['path'] ) ) {
			// Add sources to original image metadata.
			$metadata['sources'][ $targeted_mime ] = array(
				'file'     => $main_images[ $targeted_mime ]['file'],
				'filesize' => wp_filesize( $main_images[ $targeted_mime ]['path'] ),
			);
			$image_directory                       = pathinfo( $main_images[ $targeted_mime ]['path'], PATHINFO_DIRNAME );
		}

		/**
		 * If no original image was provided the image_directory can't be determined, in that scenario try to
		 * find it from the `file` property.
		 *
		 * @see get_attached_file()
		 */
		if (
			null === $image_directory
			&& isset( $metadata['file'] )
			&& 0 !== strpos( $metadata['file'], '/' )
			&& ':\\' !== substr( $metadata['file'], 1, 2 )
		) {
			$uploads = wp_get_upload_dir();
			if ( false === $uploads['error'] && isset( $uploads['basedir'] ) ) {
				$file = path_join( $uploads['basedir'], $metadata['file'] );
				if ( file_exists( $file ) ) {
					$image_directory = pathinfo( $file, PATHINFO_DIRNAME );
				}
			}
		}

		if ( null === $image_directory ) {
			continue;
		}

		foreach ( $metadata['sizes'] as $size_name => $size_details ) {
			if (
				! isset( $subsized_images[ $targeted_mime ][ $size_name ]['file'] ) ||
				! is_string( $subsized_images[ $targeted_mime ][ $size_name ]['file'] ) ||
				'' === $subsized_images[ $targeted_mime ][ $size_name ]['file']
			) {
				continue;
			}

			// Add sources to resized image metadata.
			$subsize_path = path_join( $image_directory, $subsized_images[ $targeted_mime ][ $size_name ]['file'] );
			if ( ! file_exists( $subsize_path ) ) {
				continue;
			}

			$metadata['sizes'][ $size_name ]['sources'][ $targeted_mime ] = array(
				'file'     => $subsized_images[ $targeted_mime ][ $size_name ]['file'],
				'filesize' => wp_filesize( $subsize_path ),
			);
		}
	}

	return $metadata;
}

/**
 * Creates additional image formats when original image is edited.
 *
 * @since 1.0.0
 *
 * @param bool|null|mixed $override  Value to return instead of saving. Default null.
 * @param string          $file_path Name of the file to be saved.
 * @param WP_Image_Editor $editor    The image editor instance.
 * @param string          $mime_type The mime type of the image.
 * @param int             $post_id   Attachment post ID.
 * @return bool|null Potentially modified $override value.
 */
function webp_uploads_update_image_onchange( $override, string $file_path, WP_Image_Editor $editor, string $mime_type, int $post_id ): ?bool {
	if ( null !== $override ) {
		return (bool) $override;
	}

	$transforms = webp_uploads_get_upload_image_mime_transforms();
	if ( ! isset( $transforms[ $mime_type ] ) || ! is_array( $transforms[ $mime_type ] ) || 0 === count( $transforms[ $mime_type ] ) ) {
		return null;
	}

	$mime_transforms = $transforms[ $mime_type ];
	// This variable allows to unhook the logic from within the closure without the need for a function name.
	$callback_executed = false;
	add_filter(
		'wp_update_attachment_metadata',
		static function ( $metadata, $post_meta_id ) use ( $post_id, $file_path, $mime_type, $editor, $mime_transforms, &$callback_executed ) {
			if ( $post_meta_id !== $post_id ) {
				return $metadata;
			}

			// This callback was already executed for this post, nothing to do at this point.
			if ( $callback_executed ) {
				return $metadata;
			}
			$callback_executed = true;
			// No sizes to be created.
			if ( ! isset( $metadata['sizes'] ) || ! is_array( $metadata['sizes'] ) || 0 === count( $metadata['sizes'] ) ) {
				return $metadata;
			}

			$old_metadata = wp_get_attachment_metadata( $post_id );
			$resize_sizes = array();
			// PHPCS ignore reason: A nonce check is not necessary here as this logic directly ties in with WordPress core
			// function `wp_ajax_image_editor()` which already has one.
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$target = isset( $_REQUEST['target'] ) ? sanitize_key( $_REQUEST['target'] ) : 'all';

			if ( isset( $old_metadata['sizes'] ) ) {
				foreach ( $old_metadata['sizes'] as $size_name => $size_details ) {
					// If the target is 'nothumb', skip generating the 'thumbnail' size.
					if ( webp_uploads_image_edit_thumbnails_separately() && 'nothumb' === $target && 'thumbnail' === $size_name ) {
						continue;
					}

					if (
						isset( $metadata['sizes'][ $size_name ]['file'] ) &&
						$metadata['sizes'][ $size_name ]['file'] !== $old_metadata['sizes'][ $size_name ]['file']
					) {
						$resize_sizes[ $size_name ] = $metadata['sizes'][ $size_name ];
					}
				}
			}

			$allowed_mimes      = array_flip( wp_get_mime_types() );
			$original_directory = pathinfo( $file_path, PATHINFO_DIRNAME );
			$filename           = pathinfo( $file_path, PATHINFO_FILENAME );
			$main_images        = array();
			$subsized_images    = array();

			foreach ( $mime_transforms as $targeted_mime ) {
				if ( $targeted_mime === $mime_type ) {
					// If the target is `thumbnail` make sure it is the only selected size.
					if ( webp_uploads_image_edit_thumbnails_separately() && 'thumbnail' === $target ) {
						if ( isset( $metadata['sizes']['thumbnail'] ) ) {
							$subsized_images[ $targeted_mime ] = array( 'thumbnail' => $metadata['sizes']['thumbnail'] );
						}
						// When the targeted thumbnail is selected no additional size and subsize is set.
						continue;
					}

					$main_images[ $targeted_mime ]     = array(
						'path' => $file_path,
						'file' => pathinfo( $file_path, PATHINFO_BASENAME ),
					);
					$subsized_images[ $targeted_mime ] = $metadata['sizes'];
					continue;
				}

				if ( ! isset( $allowed_mimes[ $targeted_mime ] ) || ! is_string( $allowed_mimes[ $targeted_mime ] ) ) {
					continue;
				}

				if ( $editor instanceof WP_Image_Editor && ! $editor::supports_mime_type( $targeted_mime ) ) {
					continue;
				}

				$extension = explode( '|', $allowed_mimes[ $targeted_mime ] );
				$extension = $extension[0];

				// If the target is `thumbnail` make sure only that size is generated.
				if ( webp_uploads_image_edit_thumbnails_separately() && 'thumbnail' === $target ) {
					if ( ! isset( $subsized_images[ $mime_type ]['thumbnail']['file'] ) ) {
						continue;
					}
					$thumbnail_file = $subsized_images[ $mime_type ]['thumbnail']['file'];
					$image_path     = path_join( $original_directory, $thumbnail_file );
					$editor         = wp_get_image_editor( $image_path, array( 'mime_type' => $targeted_mime ) );

					if ( is_wp_error( $editor ) ) {
						continue;
					}

					$current_extension = pathinfo( $thumbnail_file, PATHINFO_EXTENSION );
					// Create a file with then new extension out of the targeted file.
					$target_file_name     = preg_replace( "/\.$current_extension$/", ".$extension", $thumbnail_file );
					$target_file_location = path_join( $original_directory, $target_file_name );

					remove_filter( 'image_editor_output_format', 'webp_uploads_filter_image_editor_output_format', 10 );
					$result = $editor->save( $target_file_location, $targeted_mime );
					add_filter( 'image_editor_output_format', 'webp_uploads_filter_image_editor_output_format', 10, 3 );

					if ( is_wp_error( $result ) ) {
						continue;
					}

					$subsized_images[ $targeted_mime ] = array( 'thumbnail' => $result );
				} elseif ( $editor instanceof WP_Image_Editor ) {
					$destination = trailingslashit( $original_directory ) . "{$filename}.{$extension}";

					remove_filter( 'image_editor_output_format', 'webp_uploads_filter_image_editor_output_format', 10 );
					$result = $editor->save( $destination, $targeted_mime );
					add_filter( 'image_editor_output_format', 'webp_uploads_filter_image_editor_output_format', 10, 3 );

					if ( is_wp_error( $result ) ) {
						continue;
					}

					$main_images[ $targeted_mime ]     = $result;
					$subsized_images[ $targeted_mime ] = $editor->multi_resize( $resize_sizes );
				}
			}

			return webp_uploads_update_sources( $metadata, $mime_transforms, $main_images, $subsized_images );
		},
		10,
		2
	);

	return null;
}
add_filter( 'wp_save_image_editor_file', 'webp_uploads_update_image_onchange', 10, 5 );

/**
 * Inspect if the current call to `wp_update_attachment_metadata()` was done from within the context
 * of an edit to an attachment either restore or other type of edit, in that case we perform operations
 * to save the sources properties, specifically for the `full` size image due this is a virtual image size.
 *
 * @since 1.0.0
 *
 * @see wp_update_attachment_metadata()
 *
 * @phpstan-param array{
 *        width: int,
 *        height: int,
 *        file: string,
 *        sizes: array<string, array{ file: string, width: int, height: int, 'mime-type': string, sources?: array<string, array{ file: string, filesize: int }> }>,
 *        image_meta: array<string, mixed>,
 *        filesize: int,
 *        original_image: string
 *    } $data
 *
 * @param array<string, mixed> $data          The current metadata of the attachment.
 * @param int                  $attachment_id The ID of the current attachment.
 *
 * @return array{
 *     width: int,
 *     height: int,
 *     file: string,
 *     sizes: array<string, array{ file: string, width: int, height: int, 'mime-type': string, sources?: array<string, array{ file: string, filesize: int }> }>,
 *     image_meta: array<string, mixed>,
 *     filesize: int,
 *     original_image: string
 * } The updated metadata for the attachment to be stored in the meta table.
 */
function webp_uploads_update_attachment_metadata( array $data, int $attachment_id ): array {
	// PHPCS ignore reason: Update the attachment's metadata by either restoring or editing it.
	// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_debug_backtrace
	$trace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 10 );

	foreach ( $trace as $element ) {
		switch ( $element['function'] ) {
			case 'wp_save_image':
				// Right after an image has been edited.
				return webp_uploads_backup_sources( $attachment_id, $data );
			case 'wp_restore_image':
				// When an image has been restored.
				$data = webp_uploads_backup_sources( $attachment_id, $data );
				return webp_uploads_restore_image( $attachment_id, $data );
		}
	}

	return $data;
}
add_filter( 'wp_update_attachment_metadata', 'webp_uploads_update_attachment_metadata', 10, 2 );

/**
 * Before saving the metadata of the image store a backup values for the sources and file property
 * those files would be used and deleted by the backup mechanism, right after the metadata has
 * been updated. It removes the current sources property due once this function is executed
 * right after an edit has taken place and the current sources are no longer accurate.
 *
 * @since 1.0.0
 *
 * @phpstan-param array{
 *       width: int,
 *       height: int,
 *       file: string,
 *       sizes: array<string, array{ file: string, width: int, height: int, 'mime-type': string }>,
 *       image_meta: array<string, mixed>,
 *       filesize: int,
 *       original_image: string,
 *       sources?: array<string, array{ file: string, filesize: int }>
 *   } $data
 *
 * @param int                  $attachment_id The ID representing the attachment.
 * @param array<string, mixed> $data          The current metadata of the attachment.
 *
 * @return array{
 *     width: int,
 *     height: int,
 *     file: string,
 *     sizes: array<string, array{ file: string, width: int, height: int, 'mime-type': string }>,
 *     image_meta: array<string, mixed>,
 *     filesize: int,
 *     original_image: string
 * } The updated metadata for the attachment.
 */
function webp_uploads_backup_sources( int $attachment_id, array $data ): array {
	// PHPCS ignore reason: A nonce check is not necessary here as this logic directly ties in with WordPress core
	// function `wp_ajax_image_editor()` which already has one.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$target = isset( $_REQUEST['target'] ) ? sanitize_key( $_REQUEST['target'] ) : 'all';

	// When an edit to an image is only applied to a thumbnail there's nothing we need to back up.
	if ( webp_uploads_image_edit_thumbnails_separately() && 'thumbnail' === $target ) {
		return $data;
	}

	$metadata = wp_get_attachment_metadata( $attachment_id );
	// Nothing to back up.
	if ( ! isset( $metadata['sources'] ) ) {
		return $data;
	}

	$sources = $metadata['sources'];
	// Prevent execution of the callbacks more than once if the callback was already executed.
	$has_been_processed = false;

	$hook = static function ( $meta_id, $post_id, $meta_name ) use ( $attachment_id, $sources, &$has_been_processed ): void {
		// Make sure this hook is only executed in the same context for the provided $attachment_id.
		if ( $post_id !== $attachment_id ) {
			return;
		}

		// This logic should work only if we are looking at the meta key: `_wp_attachment_backup_sizes`.
		if ( '_wp_attachment_backup_sizes' !== $meta_name ) {
			return;
		}

		if ( $has_been_processed ) {
			return;
		}

		$has_been_processed = true;
		webp_uploads_backup_full_image_sources( $post_id, $sources );
	};

	add_action( 'added_post_meta', $hook, 10, 3 );
	add_action( 'updated_post_meta', $hook, 10, 3 );

	// Remove the current sources as at this point the current values are no longer accurate.
	// TODO: Requires to be updated from https://github.com/WordPress/performance/issues/158.
	unset( $data['sources'] );

	return $data;
}

/**
 * Stores the provided sources for the attachment ID in the `_wp_attachment_backup_sources`  with
 * the next available target if target is `null` no source would be stored.
 *
 * @since 1.0.0
 *
 * @param int                                                 $attachment_id The ID of the attachment.
 * @param array<string, array{ file: string, filesize: int }> $sources       An array with the full sources to be stored on the next available key.
 */
function webp_uploads_backup_full_image_sources( int $attachment_id, array $sources ): void {
	if ( 0 === count( $sources ) ) {
		return;
	}

	$target = webp_uploads_get_next_full_size_key_from_backup( $attachment_id );
	if ( null === $target ) {
		return;
	}

	$backup_sources            = get_post_meta( $attachment_id, '_wp_attachment_backup_sources', true );
	$backup_sources            = is_array( $backup_sources ) ? $backup_sources : array();
	$backup_sources[ $target ] = $sources;
	// Store the `sources` property into the full size if present.
	update_post_meta( $attachment_id, '_wp_attachment_backup_sources', $backup_sources );
}

/**
 * It finds the next available `full-{orig or hash}` key on the images if the name
 * has not been used as part of the backup sources it would be used if no size is
 * found or backup exists `null` would be returned instead.
 *
 * @since 1.0.0
 *
 * @param int $attachment_id The ID of the attachment.
 * @return null|string The next available full size name.
 */
function webp_uploads_get_next_full_size_key_from_backup( int $attachment_id ): ?string {
	$backup_sizes = get_post_meta( $attachment_id, '_wp_attachment_backup_sizes', true );
	$backup_sizes = is_array( $backup_sizes ) ? $backup_sizes : array();

	if ( 0 === count( $backup_sizes ) ) {
		return null;
	}

	$backup_sources = get_post_meta( $attachment_id, '_wp_attachment_backup_sources', true );
	$backup_sources = is_array( $backup_sources ) ? $backup_sources : array();
	foreach ( array_keys( $backup_sizes ) as $size_name ) {
		// If the target already has the sources attributes find the next one.
		if ( isset( $backup_sources[ $size_name ] ) ) {
			continue;
		}

		// We are only interested in the `full-` sizes.
		if ( strpos( $size_name, 'full-' ) === false ) {
			continue;
		}

		return $size_name;
	}

	return null;
}

/**
 * Restore an image from the backup sizes, the current hook moves the `sources` from the `full-orig` key into
 * the top level `sources` into the metadata, in order to ensure the restore process has a reference to the right
 * images.
 *
 * @since 1.0.0
 *
 * @phpstan-param array{
 *        width: int,
 *        height: int,
 *        file: string,
 *        sizes: array<string, array{ file: string, width: int, height: int, 'mime-type': string }>,
 *        image_meta: array<string, mixed>,
 *        filesize: int,
 *        original_image: string
 *    } $data
 *
 * @param int                  $attachment_id The ID of the attachment.
 * @param array<string, mixed> $data          The current metadata to be stored in the attachment.
 * @return array{
 *     width: int,
 *     height: int,
 *     file: string,
 *     sizes: array<string, array{ file: string, width: int, height: int, 'mime-type': string }>,
 *     image_meta: array<string, mixed>,
 *     filesize: int,
 *     sources?: array<string, array{ file: string, filesize: int }>,
 *     original_image: string
 * } The updated metadata of the attachment.
 */
function webp_uploads_restore_image( int $attachment_id, array $data ): array {
	$backup_sources = get_post_meta( $attachment_id, '_wp_attachment_backup_sources', true );
	if ( ! is_array( $backup_sources ) ) {
		$backup_sources = array();
	}

	if ( ! isset( $backup_sources['full-orig'] ) || ! is_array( $backup_sources['full-orig'] ) ) {
		return $data;
	}

	// TODO: Handle the case If `IMAGE_EDIT_OVERWRITE` is defined and is truthy remove any edited images if present before replacing the metadata.
	// See: https://github.com/WordPress/performance/issues/158.
	$data['sources'] = $backup_sources['full-orig'];

	return $data;
}

/**
 * Compatibility function to check whether editing image thumbnails separately is enabled.
 *
 * The filter {@see 'image_edit_thumbnails_separately'} was introduced in WordPress 6.3 with default value of `false`,
 * for a behavior that previously was always enabled.
 *
 * @since 1.0.2
 *
 * @see https://core.trac.wordpress.org/ticket/57685
 *
 * @return bool True if editing image thumbnails is enabled, false otherwise.
 */
function webp_uploads_image_edit_thumbnails_separately(): bool {
	/** This filter is documented in wp-admin/includes/image-edit.php */
	return (bool) apply_filters( 'image_edit_thumbnails_separately', false );
}
