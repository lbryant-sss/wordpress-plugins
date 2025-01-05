<?php
namespace WprAddons\Admin\Includes;

use WprAddons\Classes\Utilities;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WPR_Templates_All setup
 *
 * @since 1.0
 */
class WPR_Templates_All {

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

		// Custom Canvas Template
		add_filter( 'template_include', [ $this, 'convert_to_canvas_template' ], 12 );

		// Canvas Page Header and Footer 
		add_action( 'elementor/page_templates/canvas/before_content', [ $this, 'render_header' ], -1 );
		add_action( 'elementor/page_templates/canvas/after_content', [ $this, 'render_footer' ] );

		// Canvas Page Content
		add_action( 'elementor/page_templates/canvas/wpr_content', [ $this, 'canvas_page_content_display' ], 1 );

	}


    /**
    ** Canvas Header
    */
    public function render_header() {
    	$conditions = json_decode( get_option('wpr_header_conditions'), true );

    	if ( ! empty( $conditions ) ) {
    		$this->canvas_before_after_content( $conditions );
        }
    }

	/**
	** Canvas Footer
	*/
	public function render_footer() {
    	$conditions = json_decode( get_option('wpr_footer_conditions'), true );

    	if ( ! empty( $conditions ) ) {
    		$this->canvas_before_after_content( $conditions );
        }
	}

    /**
    ** Archive Templates Conditions
    */
    public function archive_templates_conditions( $conditions ) {
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
	    			$template = $this->get_template_slug( $conditions, 'archive/author' );
				// Date
				} elseif ( is_date() ) {
	    			$template = $this->get_template_slug( $conditions, 'archive/date' );
				// Category
				} elseif ( is_category() ) {
					$template = $this->get_template_slug( $conditions, 'archive/categories', $term_id );
				// Tag
				} elseif ( is_tag() ) {
					$template = $this->get_template_slug( $conditions, 'archive/tags', $term_id );
				// Custom Taxonomies
				} elseif ( is_tax() ) {
					$template = $this->get_template_slug( $conditions, 'archive/'. $term_name, $term_id );
				// Products
				} elseif ( class_exists( 'WooCommerce' ) && is_shop() ) {
					$template = $this->get_template_slug( $conditions, 'archive/products' );
		        }

			// Search Page
			} else {
	    		$template = $this->get_template_slug( $conditions, 'archive/search' );
	        }

	    // Posts Page
		} elseif ( Utilities::is_blog_archive() ) {
			$template = $this->get_template_slug( $conditions, 'archive/posts' );
		}

	    return $template;
    }

    /**
    ** Single Templates Conditions
    */
    public function single_templates_conditions( $conditions, $pages ) {
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
	    			$template = $this->get_template_slug( $conditions, 'single/posts', $post_id );
				// CPT
		        } else {
	    			$template = $this->get_template_slug( $conditions, 'single/'. $post_type, $post_id );
		        }
			} else {
				// Front page
				if ( $pages && is_front_page() ) {
	    			$template = $this->get_template_slug( $conditions, 'single/front_page' );
				// Error 404 Page
				} elseif ( is_404() ) {
	    			$template = $this->get_template_slug( $conditions, 'single/page_404' );
				// Single Page
				} elseif ( $pages && is_page() ) {
	    			$template = $this->get_template_slug( $conditions, 'single/pages', $post_id );
		        }
			}

        }

	    return $template;
    }

    /**
    ** Canvas Page Before/After Content
    */
    public function canvas_before_after_content( $conditions ) {
    	// Template Type
    	$post_terms = wp_get_post_terms( get_the_ID(), 'wpr_template_type' );
        $template_type = ! empty($post_terms) ? $post_terms[0]->slug : '';

        // Global
        $template = $this->get_template_slug( $conditions, 'global' );

        // Custom
        if ( ! empty($conditions) && (sizeof( $conditions ) > 1 || sizeof( reset($conditions) ) > 1) ) {

			// Archive Pages (includes search)
			if ( ! is_null( $this->archive_templates_conditions( $conditions ) ) ) {
				$template = $this->archive_templates_conditions( $conditions );
			}

        	// Single Pages
			if ( ! is_null( $this->single_templates_conditions( $conditions, true ) ) ) {
				$template = $this->single_templates_conditions( $conditions, true );
			}

        }

	    // Display Template
	    if ( 'header' !== $template_type && 'footer' !== $template_type && 'popup' !== $template_type ) {
	    	$this->display_elementor_content( $template );
	    }
    }

	/**
	** Canvas Page Templates
	*/
	public function canvas_page_content_display() {
		// Get Conditions
		$archives = json_decode( get_option( 'wpr_archive_conditions' ), true );
		$archives = is_null( $archives ) ? [] : $archives;
		$singles  = json_decode( get_option( 'wpr_single_conditions' ), true );
		$singles  = is_null( $singles ) ? [] : $singles;

		// Reset
		$template = '';

		// Archive Pages (includes search)
		if ( ! is_null( $this->archive_templates_conditions( $archives ) ) ) {
			$template = $this->archive_templates_conditions( $archives );
		}

    	// Single Pages
		if ( ! is_null( $this->single_templates_conditions( $singles, false ) ) ) {
			$template = $this->single_templates_conditions( $singles, false );
		}

		// Display Template
		$this->display_elementor_content( $template );
	}


	/**
	** Custom Canvas Template
	*/
	public function convert_to_canvas_template( $template ) {
		if ( \Elementor\Plugin::$instance->preview->is_preview_mode() && 'wpr_templates' === get_queried_object()->post_type ) {
			return WPR_ADDONS_MODULES_PATH . '/page-templates/wpr-canvas.php';
		} else {
			return $template;
		}
	}


    /**
    ** Get Template Slug
    */
	public function get_template_slug( $data, $page, $post_id = '' ) {
		$template = null;

		// Custom
		if ( sizeof($data) > 1 ) {
			// Find a Custom Condition
			foreach( $data as $id => $conditions ) {
				if ( in_array( $page .'/'. $post_id, $conditions) ) {
					$template = $id;
				} elseif ( in_array( $page .'/all', $conditions) ) {
					$template = $id;
				} elseif ( in_array( $page, $conditions) ) {
					$template = $id;
				}
			}

			// If a Custom NOT Found, use Global
			if ( is_null($template) ) {
				foreach( $data as $id => $conditions ) {
					if ( in_array( 'global', $conditions) ) {
						$template = $id;
					}
				}
			}
		// Global
		} else {
			$template = key( $data );
		}

		return $template;
	}


	/**
	** Display Elementor Content
	*/
	public function display_elementor_content( $slug ) {
		// Deny if not Elemenntor Canvas
		if ( 'elementor_canvas' !== get_page_template_slug() ) {//tmp
			// return;
		}

		$template_id = Utilities::get_template_id( $slug );
		$get_elementor_content = self::$elementor_instance->frontend->get_builder_content( $template_id, false );

		if ( '' === $get_elementor_content ) {
			return;
		}

    	// Template Content
		echo $get_elementor_content;
	}

}