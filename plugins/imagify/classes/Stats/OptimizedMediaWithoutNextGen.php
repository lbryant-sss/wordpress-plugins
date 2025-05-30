<?php
declare(strict_types=1);

namespace Imagify\Stats;

use Imagify\Bulk\Bulk;
use Imagify\Optimization\Process\ProcessInterface;
use Imagify\EventManagement\SubscriberInterface;
use Imagify\Traits\InstanceGetterTrait;
use WP_Error;

/**
 * Class to get and cache the number of optimized media without next-gen versions.
 */
final class OptimizedMediaWithoutNextGen implements StatInterface, SubscriberInterface {
	use InstanceGetterTrait;

	/**
	 * Name of the transient storing the cached result.
	 *
	 * @var string
	 */
	const NAME = 'imagify_stat_without_next_gen';

	/**
	 * Array of events this subscriber listens to
	 *
	 * @return array
	 */
	public static function get_subscribed_events() {
		return [
			'imagify_after_optimize'         => [ 'maybe_clear_cache_after_optimization', 10, 2 ],
			'imagify_after_restore_media'    => [ 'maybe_clear_cache_after_restoration', 10, 4 ],
			'imagify_delete_media'           => 'maybe_clear_cache_on_deletion',
			'update_option_imagify_settings' => [ 'maybe_clear_stat_cache', 9, 2 ],
		];
	}

	/**
	 * Get the number of optimized media without next-gen versions.
	 *
	 * @since 2.2
	 *
	 * @return int
	 */
	public function get_stat() {
		$bulk = Bulk::get_instance();
		$stat = 0;

		// Sum the counts of each context.
		foreach ( imagify_get_context_names() as $context ) {
			$stat += $bulk->get_bulk_instance( $context )->has_optimized_media_without_nextgen();
		}

		return $stat;
	}

	/**
	 * Get and cache the number of optimized media without next-gen versions.
	 *
	 * @since 2.2
	 *
	 * @return int
	 */
	public function get_cached_stat() {
		$contexts = implode( '|', imagify_get_context_names() );
		$stat     = get_transient( self::NAME );

		if ( isset( $stat['stat'], $stat['contexts'] ) && $stat['contexts'] === $contexts ) {
			// The number is stored and the contexts are the same.
			return (int) $stat['stat'];
		}

		$stat = [
			'contexts' => $contexts,
			'stat'     => $this->get_stat(),
		];

		set_transient( self::NAME, $stat, 2 * DAY_IN_SECONDS );

		return $stat['stat'];
	}

	/**
	 * Clear the stat cache.
	 *
	 * @since 2.2
	 */
	public function clear_cache() {
		delete_transient( self::NAME );
	}

	/**
	 * Clear cache after optimizing a media.
	 *
	 * @since 2.2
	 *
	 * @param ProcessInterface $process The optimization process.
	 * @param array            $item    The item being processed.
	 */
	public function maybe_clear_cache_after_optimization( $process, $item ) {
		if ( ! $process->get_media()->is_image() || false === get_transient( self::NAME ) ) {
			return;
		}

		$sizes     = $process->get_data()->get_optimization_data();
		$sizes     = isset( $sizes['sizes'] ) ? (array) $sizes['sizes'] : [];
		$new_sizes = array_flip( $item['sizes_done'] );
		$new_sizes = array_intersect_key( $sizes, $new_sizes );
		$size_name = 'full' . $process::WEBP_SUFFIX;

		if ( 'avif' === get_imagify_option( 'optimization_format' ) ) {
			$size_name = 'full' . $process::AVIF_SUFFIX;
		}

		if ( ! isset( $new_sizes['full'] ) && ! empty( $new_sizes[ $size_name ]['success'] ) ) {
			/**
			 * We just successfully generated the next-gen version of the full size.
			 * The full size was not optimized at the same time, that means it was optimized previously.
			 * Meaning: we just added a next-gen version to a media that was previously optimized, so there is one less optimized media without next-gen.
			 */
			$this->clear_cache();
			return;
		}

		if ( ! empty( $new_sizes['full']['success'] ) && empty( $new_sizes[ $size_name ]['success'] ) ) {
			/**
			 * We now have a new optimized media without next-gen.
			 */
			$this->clear_cache();
		}
	}

	/**
	 * Clear cache after restoring a media.
	 *
	 * @since 2.2
	 *
	 * @param ProcessInterface $process The optimization process.
	 * @param bool|WP_Error    $response The result of the operation: true on success, a WP_Error object on failure.
	 * @param array            $files    The list of files, before restoring them.
	 * @param array            $data     The optimization data, before deleting it.
	 */
	public function maybe_clear_cache_after_restoration( $process, $response, $files, $data ) {
		if ( ! $process->get_media()->is_image() || false === get_transient( self::NAME ) ) {
			return;
		}

		$sizes     = isset( $data['sizes'] ) ? (array) $data['sizes'] : [];
		$size_name = 'full' . $process::WEBP_SUFFIX;

		if ( 'avif' === get_imagify_option( 'optimization_format' ) ) {
			$size_name = 'full' . $process::AVIF_SUFFIX;
		}

		if ( ! empty( $sizes['full']['success'] ) && empty( $sizes[ $size_name ]['success'] ) ) {
			/**
			 * This media had no next-gen versions.
			 */
			$this->clear_cache();
		}
	}

	/**
	 * Clear cache on media deletion.
	 *
	 * @since 2.2
	 *
	 * @param ProcessInterface $process An optimization process.
	 */
	public function maybe_clear_cache_on_deletion( $process ) {
		if ( false === get_transient( self::NAME ) ) {
			return;
		}

		$data      = $process->get_data()->get_optimization_data();
		$sizes     = isset( $data['sizes'] ) ? (array) $data['sizes'] : [];
		$size_name = 'full' . $process::WEBP_SUFFIX;

		if ( 'avif' === get_imagify_option( 'optimization_format' ) ) {
			$size_name = 'full' . $process::AVIF_SUFFIX;
		}

		if ( ! empty( $sizes['full']['success'] ) && empty( $sizes[ $size_name ]['success'] ) ) {
			/**
			 * This media had no next-gen versions.
			 */
			$this->clear_cache();
		}
	}

	/**
	 * Maybe clear the stat cache on option change
	 *
	 * @since 2.2
	 *
	 * @param array $old_value The old option value.
	 * @param array $value The new option value.
	 *
	 * @return void
	 */
	public function maybe_clear_stat_cache( $old_value, $value ) {
		if ( ! isset( $old_value['optimization_format'], $value['optimization_format'] ) ) {
			return;
		}

		if ( $old_value['optimization_format'] === $value['optimization_format'] ) {
			return;
		}

		$this->clear_cache();
	}
}
