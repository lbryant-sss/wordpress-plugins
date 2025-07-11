<?php

namespace WCML\Media\Wrapper;

use SitePress;
use woocommerce_wpml;
use wpdb;
use WPML_Media_Attachments_Duplication_Factory;

class Translatable implements IMedia {

	const META_KEY_THUMBNAIL_ID          = '_thumbnail_id';
	const META_KEY_PRODUCT_IMAGE_GALLERY = '_product_image_gallery';

	/** @var woocommerce_wpml */
	private $woocommerce_wpml;
	/** @var \SitePress */
	private $sitepress;
	/** @var wpdb */
	private $wpdb;

	/** @var array */
	public $settings = [];

	/** @var array */
	private $products_being_synced = [];

	public function __construct( $woocommerce_wpml, $sitepress, $wpdb ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->sitepress        = $sitepress;
		$this->wpdb             = $wpdb;
	}

	public function add_hooks() {
		// when save new attachment duplicate product gallery.
		add_action( 'wpml_media_create_duplicate_attachment', [ $this, 'sync_product_gallery_duplicate_attachment' ], 11, 2 );
	}

	public function product_images_ids( $product_id ) {
		$product_images_ids = [];

		// thumbnail image.
		$tmb = get_post_meta( $product_id, self::META_KEY_THUMBNAIL_ID, true );
		if ( $tmb ) {
			$product_images_ids[] = $tmb;
		}

		// product gallery.
		$product_gallery = get_post_meta( $product_id, self::META_KEY_PRODUCT_IMAGE_GALLERY, true );
		if ( $product_gallery ) {
			$product_gallery = explode( ',', $product_gallery );
			foreach ( $product_gallery as $img ) {
				$product_images_ids[] = $img;
			}
		}

		foreach ( wp_get_post_terms( $product_id, 'product_type', [ 'fields' => 'names' ] ) as $type ) {
			$product_type = $type;
		}

		if ( isset( $product_type ) && 'variable' === $product_type ) {
			// phpcs:disable WordPress.WP.PreparedSQL.NotPrepared
			$get_post_variations_image = $this->wpdb->get_col(
				$this->wpdb->prepare(
					"SELECT pm.meta_value FROM {$this->wpdb->posts} AS p
                                                LEFT JOIN {$this->wpdb->postmeta} AS pm ON p.ID = pm.post_id
                                                WHERE pm.meta_key='" . self::META_KEY_THUMBNAIL_ID . "'
                                                  AND p.post_status IN ('publish','private')
                                                  AND p.post_type = 'product_variation'
                                                  AND p.post_parent = %d
                                                ORDER BY ID",
					$product_id
				)
			);
			// phpcs:enable
			foreach ( $get_post_variations_image as $variation_image ) {
				if ( $variation_image ) {
					$product_images_ids[] = $variation_image;
				}
			}
		}

		$product_images_ids = array_unique( array_map( 'intval', $product_images_ids ) );

		foreach ( $product_images_ids as $key => $image ) {
			if ( ! get_post_status( $image ) ) {
				unset( $product_images_ids[ $key ] );
			}
		}

		return $product_images_ids;
	}

	/**
	 * @param int    $original_product_id
	 * @param int    $translated_product_id
	 * @param string $language
	 *
	 * @see \WCML\Synchronization\Attachments::run
	 */
	public function sync_thumbnail_id( $original_product_id, $translated_product_id, $language ) {
		if ( $this->is_thumbnail_image_duplication_enabled( $original_product_id ) ) {
			$translated_thumbnail_id = $this->get_translated_thumbnail_id( $original_product_id, $language );
			if ( $translated_thumbnail_id ) {
				update_post_meta( $translated_product_id, self::META_KEY_THUMBNAIL_ID, $translated_thumbnail_id );
			}
		}
	}

	/**
	 * @param int    $variation_id
	 * @param int    $translated_variation_id
	 * @param string $language
	 *
	 * @see \WCML\Synchronization\VariationAttachments::run
	 */
	public function sync_variation_thumbnail_id( $variation_id, $translated_variation_id, $language ) {
		if ( $this->is_thumbnail_image_duplication_enabled( wp_get_post_parent_id( $variation_id ) ) ) {
			$translated_thumbnail_id = $this->get_translated_thumbnail_id( $variation_id, $language );
			if ( ! $translated_thumbnail_id ) {
				return null;
			}

			$stored_translated_variation_thumbnail_id = (int) get_post_meta( $translated_variation_id, self::META_KEY_THUMBNAIL_ID, true );
			if ( (int) $translated_thumbnail_id !== $stored_translated_variation_thumbnail_id ) {
				update_post_meta( $translated_variation_id, self::META_KEY_THUMBNAIL_ID, $translated_thumbnail_id );
				update_post_meta( $variation_id, '_wpml_media_duplicate', 1 );
				update_post_meta( $variation_id, '_wpml_media_featured', 1 );
			}
		}
	}

	/**
	 * @param int|string $post_id
	 * @param string     $language
	 *
	 * @return int|null
	 */
	private function get_translated_thumbnail_id( $post_id, $language ) {
		$thumbnail_id = get_post_meta( $post_id, self::META_KEY_THUMBNAIL_ID, true );
		if ( ! $thumbnail_id ) {
			return null;
		}

		$translated_thumbnail_id = $this->sitepress->get_object_id( $thumbnail_id, 'attachment', false, $language );
		if ( is_null( $translated_thumbnail_id ) ) {
			$factory = new WPML_Media_Attachments_Duplication_Factory();

			/** @var \WPML_Media_Attachments_Duplication */
			$media_duplicate         = $factory->create();
			$translated_thumbnail_id = $media_duplicate->create_duplicate_attachment(
				$thumbnail_id,
				wp_get_post_parent_id( $thumbnail_id ),
				$language
			);
		}

		return $translated_thumbnail_id;
	}

	/**
	 * @param int    $orig_post_id
	 * @param int    $trnsl_post_id
	 * @param string $lang
	 *
	 * @see \WCML\Synchronization\Attachments::run
	 */
	public function sync_product_gallery( $orig_post_id, $trnsl_post_id, $lang ) {
		if ( $this->is_media_duplication_enabled( $orig_post_id ) ) {
			$product_gallery              = get_post_meta( $orig_post_id, self::META_KEY_PRODUCT_IMAGE_GALLERY, true );
			$gallery_ids                  = explode( ',', $product_gallery );
			$translated_gallery_ids       = $this->translated_gallery_ids( $gallery_ids, $trnsl_post_id, $lang );
			$translated_gallery_ids_value = implode( ',', $translated_gallery_ids );
			update_post_meta( $trnsl_post_id, self::META_KEY_PRODUCT_IMAGE_GALLERY, $translated_gallery_ids_value );
		}
	}

	/**
	 * @param int $product_id
	 */
	public function sync_product_gallery_to_all_languages( $product_id ) {
		if ( $this->is_media_duplication_enabled( $product_id ) ) {
			$product_gallery = get_post_meta( $product_id, self::META_KEY_PRODUCT_IMAGE_GALLERY, true );
			$gallery_ids     = explode( ',', $product_gallery );
			$trid            = $this->sitepress->get_element_trid( $product_id, 'post_product' );
			$translations    = $this->sitepress->get_element_translations( $trid, 'post_product', true );

			foreach ( $translations as $translation ) {
				if ( $translation->original ) {
					continue;
				}
				$translated_gallery_ids       = $this->translated_gallery_ids( $gallery_ids, $translation->element_id, $translation->language_code );
				$translated_gallery_ids_value = implode( ',', $translated_gallery_ids );
				update_post_meta( $translation->element_id, self::META_KEY_PRODUCT_IMAGE_GALLERY, $translated_gallery_ids_value );
			}
		}
	}

	/**
	 * @param int[]  $gallery_ids
	 * @param int    $translation_id
	 * @param string $lang
	 *
	 * @return int[]
	 */
	private function translated_gallery_ids( $gallery_ids, $translation_id, $lang ) {
		$translated_gallery_ids = [];
		foreach ( $gallery_ids as $image_id ) {
			if ( null === get_post( $image_id ) ) {
				continue;
			}
			$duplicated_id = apply_filters(
				'wpml_object_id',
				$image_id,
				'attachment',
				false,
				$lang
			);
			if ( is_null( $duplicated_id ) && $image_id ) {
				$duplicated_id = $this->create_base_media_translation(
					$image_id,
					$translation_id,
					$lang
				);
			}
			$translated_gallery_ids[] = $duplicated_id;
		}
		return $translated_gallery_ids;
	}

	public function create_base_media_translation( $attachment_id, $parent_id, $target_lang ) {
		$factory = new WPML_Media_Attachments_Duplication_Factory();

		/** @var \WPML_Media_Attachments_Duplication */
		$media_duplicate = $factory->create();
		$duplicated_id   = $media_duplicate->create_duplicate_attachment( $attachment_id, $parent_id, $target_lang );

		return $duplicated_id;
	}

	public function sync_product_gallery_duplicate_attachment( $att_id, $dup_att_id ) {
		$product_id = wp_get_post_parent_id( $att_id );
		$post_type  = get_post_type( $product_id );
		if ( 'product' !== $post_type || array_key_exists( $product_id, $this->products_being_synced ) ) {
			return;
		}
		$this->products_being_synced[ $product_id ] = 1;
		$this->sync_product_gallery_to_all_languages( $product_id );
		unset( $this->products_being_synced[ $product_id ] );
	}

	private function is_thumbnail_image_duplication_enabled( $product_id ) {
		return $this->is_duplication_enabled( $product_id, 'WPML\Media\Option::DUPLICATE_FEATURED_KEY', 'WPML_Admin_Post_Actions::DUPLICATE_FEATURED_GLOBAL_KEY' );
	}

	private function is_media_duplication_enabled( $product_id ) {
		return $this->is_duplication_enabled( $product_id, 'WPML\Media\Option::DUPLICATE_MEDIA_KEY', 'WPML_Admin_Post_Actions::DUPLICATE_MEDIA_GLOBAL_KEY' );
	}

	private function is_duplication_enabled( $product_id, $meta_key, $global_key ) {

		$setting_value = get_post_meta(
			$product_id,
			$this->sitepress->get_wp_api()->constant( $meta_key ),
			true
		);

		if ( '' === $setting_value ) {
			// fallback to global setting.
			$media_options      = get_option( '_wpml_media', [] );
			$global_setting_key = $this->sitepress->get_wp_api()->constant( $global_key );
			if ( isset( $media_options['new_content_settings'][ $global_setting_key ] ) ) {
				$setting_value = $media_options['new_content_settings'][ $global_setting_key ];
			}
		}

		return (bool) $setting_value;
	}

}
