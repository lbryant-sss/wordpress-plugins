<?php
namespace WprAddons\Admin\Includes;

use WprAddons\Plugin;
use WprAddons\Classes\Utilities;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WPR_Templates_Popups setup
 *
 * @since 1.0
 */
class WPR_Templates_Popups {

	/**
	** Instance of Elemenntor Frontend class.
	*
	** @var \Elementor\Frontend()
	*/
	private static $elementor_instance;

	/**
	** Constructor
	*/
	public function __construct() {
		// Elementor Frontend
		self::$elementor_instance = \Elementor\Plugin::instance();


		add_action( 'wp_footer', [ $this, 'render_popups' ] );
	}

	/**
	** Popups
	*/
	public function render_popups() {
    	$conditions = json_decode( get_option('wpr_popup_conditions'), true );

    	if ( ! empty( $conditions ) ) {
	    	$conditions = $this->reverse_template_conditions( $conditions );

	    	// Global
    		if ( isset( $conditions['global'] ) ) {
    			$this->display_popups_by_location( $conditions, 'global' );
    		}

    		// Archive
    		$this->archive_pages_popup_conditions( $conditions );

    		// Single
    		$this->single_pages_popup_conditions( $conditions );

    		// Enqueue ScrolBar JS //tmp - check if displayed multiple times
    		wp_enqueue_script( 'wpr-popup-scroll-js', WPR_ADDONS_URL .'assets/js/lib/perfectscrollbar/perfect-scrollbar.min.js', [ 'jquery' ], '0.4.9' );
        }
	}

    /**
    ** Archive Pages Popup Conditions
    */
    public function archive_pages_popup_conditions( $conditions ) {
    	$term_id = '';
    	$term_name = '';
    	$queried_object = get_queried_object();

    	// Get Terms
    	if ( ! is_null( $queried_object ) ) {
    		if ( isset( $queried_object->term_id ) && isset( $queried_object->taxonomy ) ) {
		        $term_id   = $queried_object->term_id;
		        $term_name = $queried_object->taxonomy;
    		}
    	}

        // Reset
        $template = null;

		// Archive Pages (includes search)
		if ( is_archive() || is_search() ) {
			if ( is_archive() && ! is_search() ) {
				// Author
				if ( is_author() ) {
					if ( isset( $conditions['archive/author'] ) ) {
	    				$this->display_popups_by_location( $conditions, 'archive/author' );
					}
				}

				// Date
				if ( is_date() ) {
					if ( isset( $conditions['archive/date'] ) ) {
	    				$this->display_popups_by_location( $conditions, 'archive/date' );
					}
				}

				// Category
				if ( is_category() ) {
					if ( isset( $conditions['archive/categories/'. $term_id] ) ) {
	    				$this->display_popups_by_location( $conditions, 'archive/categories/'. $term_id );
					}

					if ( isset( $conditions['archive/categories/all'] ) ) {
	    				$this->display_popups_by_location( $conditions, 'archive/categories/all' );
					}
				}

				// Tag
				if ( is_tag() ) {
					if ( isset( $conditions['archive/tags/'. $term_id] ) ) {
	    				$this->display_popups_by_location( $conditions, 'archive/tags/'. $term_id );
					}

					if ( isset( $conditions['archive/tags/all'] ) ) {
	    				$this->display_popups_by_location( $conditions, 'archive/tags/all' );
					}
				}

				// Custom Taxonomies
				if ( is_tax() ) {
					if ( isset( $conditions['archive/'. $term_name .'/'. $term_id] ) ) {
	    				$this->display_popups_by_location( $conditions, 'archive/'. $term_name .'/'. $term_id );
					}

					if ( isset( $conditions['archive/'. $term_name .'/all'] ) ) {
	    				$this->display_popups_by_location( $conditions, 'archive/'. $term_name .'/all' );
					}
				}

				// Products
				if ( class_exists( 'WooCommerce' ) && is_shop() ) {
					if ( isset( $conditions['archive/products'] ) ) {
	    				$this->display_popups_by_location( $conditions, 'archive/products' );
					}
		        }

			// Search Page
			} else {
				if ( isset( $conditions['archive/search'] ) ) {
    				$this->display_popups_by_location( $conditions, 'archive/search' );
				}
	        }

	    // Posts Page
		} elseif ( Utilities::is_blog_archive() ) {
			if ( isset( $conditions['archive/posts'] ) ) {
				$this->display_popups_by_location( $conditions, 'archive/posts' );
			}
		}
    }


    /**
    ** Single Pages Popup Conditions
    */
    public function single_pages_popup_conditions( $conditions ) {
        global $post;

        // Get Posts
        $post_id   = is_null($post) ? '' : $post->ID;
        $post_type = is_null($post) ? '' : $post->post_type;

        // Reset
        $template = null;

		// Single Pages
		if ( is_single() || is_front_page() || is_page() || is_404() ) {

			if ( is_single() ) {
				// Blog Posts
				if ( 'post' == $post_type ) {
					if ( isset( $conditions['single/posts/'. $post_id] ) ) {
	    				$this->display_popups_by_location( $conditions, 'single/posts/'. $post_id );
					}

					if ( isset( $conditions['single/posts/all'] ) ) {
	    				$this->display_popups_by_location( $conditions, 'single/posts/all' );
					}

				// CPT
		        } else {
					if ( isset( $conditions['single/'. $post_type .'/'. $post_id] ) ) {
	    				$this->display_popups_by_location( $conditions, 'single/'. $post_type .'/'. $post_id );
					}

					if ( isset( $conditions['single/'. $post_type .'/all'] ) ) {
	    				$this->display_popups_by_location( $conditions, 'single/'. $post_type .'/all' );
					}
		        }
			} else {
				// Front page
				if ( is_front_page() ) {
					if ( isset( $conditions['single/front_page'] ) ) {
	    				$this->display_popups_by_location( $conditions, 'single/front_page' );
					}
				// Error 404 Page
				} elseif ( is_404() ) {
					if ( isset( $conditions['single/page_404'] ) ) {
	    				$this->display_popups_by_location( $conditions, 'single/page_404' );
					}
				// Single Page
				} elseif ( is_page() ) {
					if ( isset( $conditions['single/pages/'. $post_id] ) ) {
	    				$this->display_popups_by_location( $conditions, 'single/pages/'. $post_id );
					}

					if ( isset( $conditions['single/pages/all'] ) ) {
	    				$this->display_popups_by_location( $conditions, 'single/pages/all' );
					}
		        }
			}

        }
    }

    /**
    ** Reverse Template Conditions
    */
	public function reverse_template_conditions( $conditions ) {
    	$reverse = [];

    	foreach ( $conditions as $key => $condition ) {
    		foreach( $condition as $location ) {
    			if ( ! isset( $reverse[$location] ) ) {
    				$reverse[$location] = [ $key ];
    			} else {
    				array_push( $reverse[$location], $key );
    			}
    		}
    	}

    	return $reverse;
	}

    /**
    ** Display Popups by Location
    */
	public function display_popups_by_location( $conditions, $page ) {
    	foreach ( $conditions[$page] as $key => $popup ) {
    		$this->display_elementor_content( $popup );
    	}
	}

	/**
	** Display Elementor Content
	*/
	public function display_elementor_content( $slug ) {
		// Deny if not Elemenntor Canvas
		if ( 'elementor_canvas' !== get_page_template_slug() ) {//tmp
			// return;
		}

		$template_name = '';

		$template_id = Utilities::get_template_id( $slug );
		$get_settings = $this->get_template_settings( $slug );
		$get_elementor_content = self::$elementor_instance->frontend->get_builder_content( $template_id, false );

		if ( '' === $get_elementor_content ) {
			return;
		}

		// Encode Settings
		$get_encoded_settings = ! empty( $get_settings ) ? wp_json_encode( $get_settings ) : '[]';

		// Template Attributes
		$template_id_attr = 'id="wpr-popup-id-'. $template_id .'"';
		$template_class_attr = 'class="wpr-template-popup"';
		$template_settings_attr = "data-settings='". $get_encoded_settings ."'";

		// Return if NOT available for current user
		if ( ! $this->check_available_user_roles( $get_settings['popup_show_for_roles'] ) ) {
			return;
		}

		if ( ! self::$elementor_instance->preview->is_preview_mode() ) {
	    	echo '<div '. $template_id_attr .' '. $template_class_attr .' '. $template_settings_attr .'>';
	    		echo '<div class="wpr-template-popup-inner">';

		    		// Popup Overlay & Close Button
	    			echo '<div class="wpr-popup-overlay"></div>';
	    			echo '<div class="wpr-popup-close-btn"><i class="eicon-close"></i></div>';

		    		// Template Container
	    			echo '<div class="wpr-popup-container">';

		    		// Popup Image Overlay & Close Button
	    			echo '<div class="wpr-popup-image-overlay"></div>';
	    			echo '<div class="wpr-popup-close-btn"><i class="eicon-close"></i></div>';

		    		// Template Content
					echo $get_elementor_content;

	    			echo '</div>';

	    		echo '</div>';
	    	echo '</div>';
		}
	}

    /**
    ** Get Template Settings
    */
	public function get_template_settings( $slug ) {
    	$settings = [];
    	$defaults = [];

		$template_id = Utilities::get_template_id( $slug );
		$meta_settings = get_post_meta( $template_id, '_elementor_page_settings', true );

		$popup_defaults = [
			'popup_trigger' => 'load',
			'popup_load_delay' => 1,
			'popup_scroll_progress' => 10,
			'popup_inactivity_time' => 15,
			'popup_element_scroll' => '',
			'popup_custom_trigger' => '',
			'popup_specific_date' => date( 'Y-m-d H:i', strtotime( '+1 month' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ),
			'popup_stop_after_date' => false,
			'popup_stop_after_date_select' => date( 'Y-m-d H:i', strtotime( '+1 day' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ),
			'popup_show_again_delay' => 1,
			'popup_disable_esc_key' => false,
			'popup_automatic_close_delay' => false,
			'popup_animation' => 'fade',
			'popup_animation_duration' => 1,
			'popup_show_for_roles' => '',
			'popup_show_via_referral' => false,
			'popup_referral_keyword' => '',
			'popup_display_as' => 'modal',
			'popup_show_on_device' => true,
			'popup_show_on_device_mobile' => true,
			'popup_show_on_device_tablet' => true,
			'popup_disable_page_scroll' => true,
			'popup_overlay_disable_close' => false,
			'popup_close_button_display_delay' => 0,
			'popup_close_button_position' => 'inside',
		];

		// Determine Template
		if ( strpos( $slug, 'popup') ) {
			$defaults = $popup_defaults;
		}

		foreach( $defaults as $option => $value ) {
			if ( isset($meta_settings[$option]) ) {
				$settings[$option] = $meta_settings[$option];
			}
		}

    	return array_merge( $defaults, $settings );
	}

	/**
	** Check Available User Rols
	*/
	public function check_available_user_roles( $selected_roles ) {
		if ( empty( $selected_roles ) ) {
			return true;
		}

		$current_user = wp_get_current_user();

		if ( ! empty( $current_user->roles ) ) {
			$role = $current_user->roles[0];
		} else {
			$role = 'guest';
		}

		if ( in_array( $role, $selected_roles ) ) {
			return true;
		}

		return false;
	}
}