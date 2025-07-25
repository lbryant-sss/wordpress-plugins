<?php

namespace Automattic\Jetpack_Boost\Modules\Image_Size_Analysis;

use Automattic\Jetpack\Schema\Schema;
use Automattic\Jetpack\WP_JS_Data_Sync\Data_Sync;
use Automattic\Jetpack_Boost\Contracts\Feature;
use Automattic\Jetpack_Boost\Contracts\Has_Data_Sync;
use Automattic\Jetpack_Boost\Contracts\Is_Always_On;
use Automattic\Jetpack_Boost\Contracts\Needs_Website_To_Be_Public;
use Automattic\Jetpack_Boost\Lib\Premium_Features;
use Automattic\Jetpack_Boost\Modules\Image_Size_Analysis\Data_Sync\Data_Sync_Schema;
use Automattic\Jetpack_Boost\Modules\Image_Size_Analysis\Data_Sync\Image_Size_Analysis_Entry;
use Automattic\Jetpack_Boost\Modules\Image_Size_Analysis\Data_Sync\Image_Size_Analysis_Summary;
use Automattic\Jetpack_Boost\Modules\Image_Size_Analysis\Data_Sync\Image_Size_Analysis_UI_State;
use Automattic\Jetpack_Boost\REST_API\Endpoints\Image_Analysis_Action_Fix;
use Automattic\Jetpack_Boost\REST_API\Endpoints\Image_Size_Analysis_Summary_Action_Paginate;
use Automattic\Jetpack_Boost\REST_API\Endpoints\Image_Size_Analysis_Summary_Action_Start;

class Image_Size_Analysis implements Feature, Is_Always_On, Has_Data_Sync, Needs_Website_To_Be_Public {

	public function setup() {
		Image_Size_Analysis_Fixer::setup();
	}

	public function register_data_sync( Data_Sync $instance ) {
		$instance->register( 'image_size_analysis', Data_Sync_Schema::image_size_analysis(), new Image_Size_Analysis_Entry() );
		$instance->register_action( 'image_size_analysis', 'paginate', Data_Sync_Schema::image_size_analysis_paginate(), new Image_Size_Analysis_Summary_Action_Paginate() );
		$instance->register_action( 'image_size_analysis', 'fix', Data_Sync_Schema::image_size_analysis_fix(), new Image_Analysis_Action_Fix() );
		$instance->register( 'image_size_analysis_summary', Data_Sync_Schema::image_size_analysis_summary(), new Image_Size_Analysis_Summary() );
		$instance->register_action( 'image_size_analysis_summary', 'start', Schema::as_void(), new Image_Size_Analysis_Summary_Action_Start() );
		$instance->register( 'image_size_analysis_ui_state', Data_Sync_Schema::image_size_analysis_ui_state(), new Image_Size_Analysis_UI_State() );
	}

	public static function is_available() {
		return Premium_Features::has_feature( Premium_Features::IMAGE_SIZE_ANALYSIS );
	}

	/**
	 * Check if the Image Size Analysis UI should be displayed on the main page.
	 *
	 * This filter allows the UI to be conditionally hidden for deprecation purposes.
	 * Defaults to false to hide the UI by default.
	 *
	 * @return bool True if the UI should be displayed, false otherwise.
	 */
	public static function should_display_ui() {
		/**
		 * Filter whether the Image Size Analysis UI should be displayed on the main page.
		 *
		 * @param bool $should_display Whether the UI should be displayed. Default false.
		 * @since 4.2.0
		 */
		return apply_filters( 'jetpack_boost_image_size_analysis_display_ui', false );
	}

	public static function get_slug() {
		return 'image_size_analysis';
	}
}
