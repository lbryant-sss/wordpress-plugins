<?php

/*
Plugin Name: Advanced Woo Search
Description: Advance ajax WooCommerce product search.
Version: 1.00
Author: ILLID
Text Domain: aws
*/


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


define( 'AWS_DIR', dirname( __FILE__ ) );
define( 'AWS_URL', plugins_url( '', __FILE__ ) );


if ( ! class_exists( 'AWS_Main' ) ) :

/**
 * Main plugin class
 *
 * @class AWS_Main
 */
final class AWS_Main {

	/**
	 * @var AWS_Main The single instance of the class
	 */
	protected static $_instance = null;

    /**
     * @var AWS_Main Array of all plugin data $data
     */
    private $data = array();

	/**
	 * Main AWS_Main Instance
	 *
	 * Ensures only one instance of AWS_Main is loaded or can be loaded.
	 *
	 * @static
	 * @return AWS_Main - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {

        $this->data['settings'] = get_option( 'aws_settings' );

		add_filter( 'widget_text', 'do_shortcode' );

		add_shortcode( 'aws_search_form', array( $this, 'markup' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );

		add_action( 'wp_ajax_aws_action', array( $this, 'action_callback' ) );
		add_action('wp_ajax_nopriv_aws_action', array( $this, 'action_callback' ) );

		add_filter( 'plugin_action_links', array( $this, 'add_settings_link' ), 10, 2 );

		//load_plugin_textdomain( 'aws', false, dirname( plugin_basename( __FILE__ ) ). '/languages/' );

        $this->includes();

	}

    /**
     * Include required core files used in admin and on the frontend.
     */
    public function includes() {
        include_once( 'includes/class-aws-admin.php' );
        include_once( 'includes/widget.php' );
    }

	/*
	 * Generate search box markup
	 */
	 public function markup( $args = array() ) {

		$placeholder  = $this->get_settings( 'search_field_text' );
		$min_chars    = $this->get_settings( 'min_chars' );
		$show_loader  = $this->get_settings( 'show_loader' );

		$params_string = '';

		$params = array(
			'data-url'          => admin_url('admin-ajax.php'),
			'data-siteurl'      => site_url(),
			'data-show-loader'  => $show_loader,
            'data-min-chars'    => $min_chars,
		);

		foreach( $params as $key => $value ) {
            $params_string .= $key . '="' . $value . '"';
		}

        $markup = '';
        $markup .= '<div class="aws-container" ' . $params_string . '>';
            $markup .= '<form class="aws-search-form" action="' . site_url() . '" method="get" role="search" >';
                $markup .= '<input  type="text" name="s" value="' . get_search_query() . '" class="aws-search-field" placeholder="' . $placeholder . '" autocomplete="off" />';
                $markup .= '<div class="aws-search-result" style="display: none;"></div>';
            $markup .= '</form>';
        $markup .= '</div>';

        return $markup;

	}

	/*
	 * Load assets for search form
	 */
	public function load_scripts() {
		wp_enqueue_style( 'aws-style', AWS_URL . '/assets/css/common.css' );
		wp_enqueue_script( 'aws-script', AWS_URL . '/assets/js/common.js', array('jquery'), '1.0', true );
	}

    /*
     * Get array of included to search result posts ids
     */
    private function get_posts_ids( $sql ) {

        global $wpdb;

        $posts_ids = array();

        $search_results = $wpdb->get_results( $sql );


        if ( !empty( $search_results ) && !is_wp_error( $search_results ) && is_array( $search_results ) ) {
            foreach ( $search_results as $search_result ) {
                $posts_ids[] = intval( $search_result->ID );
            }
        }

        unset( $search_results );

        return $posts_ids;

    }

    /*
     * AJAX call action callback
     */
    public function action_callback() {

        global $wpdb;

        $show_cats     = $this->get_settings( 'show_cats' );
        $show_tags     = $this->get_settings( 'show_tags' );
        $exact_match   = $this->get_settings( 'exact_match' );
        $results_num   = $this->get_settings( 'results_num' );
		$search_in     = $this->get_settings( 'search_in' );

		$search_in_arr = explode( ',',  $this->get_settings( 'search_in' ) );

        // Search in title if all options is disabled
        if ( ! $search_in ) {
			$search_in_arr = array( 'title' );
        }

        $categories_array = array();
        $tags_array = array();
		$query = array();


		$s = esc_attr( $_POST['keyword'] );
		$s = stripslashes( $s );
		$s = str_replace( array( "\r", "\n" ), '', $s );

		$this->data['s'] = $s;
		$this->data['search_terms'] = array();
		$this->data['search_in'] = $search_in_arr;

		if ( $exact_match === 'true' ) {
			$this->data['search_terms'] = array( $s );
		} else {
			$this->data['search_terms'] = array_unique( explode( ' ', $s ) );

			if ( count( $this->data['search_terms'] ) > 0 ) {
				if ( count( $this->data['search_terms'] ) > 9 ) {
					$this->data['search_terms'] = array( $s );
				}
			} else {
				$this->data['search_terms'] = array( $s );
			}

		}


        // Generate search query

        $query['search'] = '';
        $query['relevance'] = '';

		$temp_search_array = array();
		$relevance_array = array();
		$new_relevance_array = array();

		foreach ( $this->data['search_terms'] as $search_term ) {

			$like = '%' . $wpdb->esc_like( $search_term ) . '%';

			$search_in_array = array();

			foreach ( $search_in_arr as $search_in_term ) {

				switch ( $search_in_term ) {

					case 'title':
						$search_in_array[] = $wpdb->prepare( '( posts.post_title LIKE %s )', $like );
						$relevance_array['title'][] = $wpdb->prepare( "( case when ( post_title LIKE %s ) then 10 else 0 end )", $like );
						break;

					case 'content':
						$search_in_array[] = $wpdb->prepare( '( posts.post_content LIKE %s )', $like );
						$relevance_array['content'][] = $wpdb->prepare( "( case when ( post_content LIKE %s ) then 7 else 0 end )", $like );
						break;

					case 'excerpt':
						$search_in_array[] = $wpdb->prepare( '( posts.post_excerpt LIKE %s )', $like );
						$relevance_array['content'][] = $wpdb->prepare( "( case when ( post_excerpt LIKE %s ) then 7 else 0 end )", $like );
						break;

					case 'sku':
						$search_in_array[] = $wpdb->prepare( '( postmeta.meta_value LIKE %s )', $like );
						break;

				}

			}

			$temp_search_array[] = sprintf( ' ( %s ) ', implode( ' OR ', $search_in_array ) );

		}

		$query['search'] .= sprintf( ' AND ( %s )', implode( ' OR ', $temp_search_array ) );

		// Sort 'relevance' queries in the array by search priority
		foreach ( $search_in_arr as $search_in_item ) {
			if ( isset( $relevance_array[$search_in_item] ) ) {
				$new_relevance_array[$search_in_item] = implode( ' + ', $relevance_array[$search_in_item] );
			}
		}

		$query['relevance'] .= sprintf( ' ( %s ) ', implode( ' + ', $new_relevance_array ) );


		$sql = "SELECT
                    ID,
                    {$query['relevance']} as relevance
                FROM
                    $wpdb->posts AS posts,
                    $wpdb->postmeta AS postmeta
                WHERE
                    posts.post_type = 'product'
				    AND posts.post_status = 'publish'
				    AND posts.ID = postmeta.post_id
				    AND postmeta.meta_key = '_sku'
				    {$query['search']}
				ORDER BY
				    relevance DESC,
				    posts.post_date DESC
				LIMIT 0, {$results_num}
		";


        $posts_ids = $this->get_posts_ids( $sql );

		$products_array = $this->get_products( $posts_ids );


        if ( $show_cats === 'true' ) {
            $categories_array = $this->get_taxonomies( $this->data['s'], 'product_cat' );
        }

        if ( $show_tags === 'true' ) {
            $tags_array = $this->get_taxonomies( $this->data['s'], 'product_tag' );
        }

		echo json_encode( array(
            'cats'     => $categories_array,
            'tags'     => $tags_array,
            'products' => $products_array
        ) );

		die;

	}

	/*
	 * Get products info
	 */
	private function get_products( $posts_ids ) {

		$products_array = array();

		if ( count( $posts_ids ) > 0 ) {

			$show_excerpt      = $this->get_settings( 'show_excerpt' );
			$excerpt_source    = $this->get_settings( 'desc_source' );
			$excerpt_length    = $this->get_settings( 'excerpt_length' );
			$mark_search_words = $this->get_settings( 'mark_words' );
			$show_price        = $this->get_settings( 'show_price' );
			$show_sale         = $this->get_settings( 'show_sale' );
			$show_image        = $this->get_settings( 'show_image' );

			foreach ( $posts_ids as $post_id ) {

				$product = new WC_product( $post_id );

				$post_data = $product->get_post_data();

				$title = $product->get_title();

                $excerpt = '';
                $price   = '';
				$on_sale = '';
				$image = '';

                if ( $show_excerpt === 'true' ) {
                    $excerpt = ( $excerpt_source === 'excerpt' && $post_data->post_excerpt ) ? $post_data->post_excerpt : $post_data->post_content;
                    $excerpt = wp_trim_words( $excerpt, $excerpt_length, '...' );
                }

				if ( $mark_search_words === 'true'  ) {

					$marked_content = $this->mark_search_words( $title, $excerpt );

					$title   = $marked_content['title'];
					$excerpt = $marked_content['excerpt'];

				}

				if ( $show_price === 'true' ) {
                    $price = $product->get_price_html();
                }

				if ( $show_sale === 'true' ) {
					$on_sale = $product->is_on_sale();
				}

				if ( $show_image === 'true' ) {
					$image_id = $product->get_image_id();
					$image_attributes = wp_get_attachment_image_src( $image_id );
					$image = $image_attributes[0];
				}

				$categories = $product->get_categories( ',' );

				$tags = $product->get_tags( ',' );

				$new_result = array(
					'title'      => $title,
					'excerpt'    => $excerpt,
					'link'       => get_permalink( $post_id ),
					'image'      => $image,
					'price'      => $price,
					'categories' => $categories,
					'tags'       => $tags,
					'on_sale'    => $on_sale
				);

				$products_array[] = $new_result;
			}

		}

		return $products_array;

	}

	/*
	 * Mark search words
	 */
	private function mark_search_words( $title, $excerpt ) {

		$show_excerpt = $this->get_settings( 'show_excerpt' );

		$pattern = array();

		foreach( $this->data['search_terms'] as $search_in ) {
			$pattern[] = '(' . $search_in . ')+';
		}

		usort( $pattern, array( $this, 'sort_by_length' ) );
		$pattern = implode( '|', $pattern );
		$pattern = sprintf( '/%s/i', $pattern );

		if ( in_array( 'title', $this->data['search_in'] ) ) {
			$title = preg_replace($pattern, '<strong>${0}</strong>', $title);
		}

		if ( $show_excerpt === 'true' && in_array( 'content', $this->data['search_in'] ) ) {
			$excerpt = preg_replace( $pattern, '<strong>${0}</strong>', $excerpt );
		}

		return array(
			'title'   => $title,
			'excerpt' => $excerpt
		);

	}

	/*
	 * Sort array by its values length
	 */
	private function sort_by_length( $a, $b ) {
		return strlen( $b ) - strlen( $a );
	}

    /*
     * Check if the terms are suitable for searching
     */
    private function parse_search_terms( $terms ) {

        $strtolower = function_exists( 'mb_strtolower' ) ? 'mb_strtolower' : 'strtolower';
        $checked = array();

        $stopwords = $this->get_search_stopwords();

        foreach ( $terms as $term ) {

            // Avoid single A-Z.
            if ( ! $term || ( 1 === strlen( $term ) && preg_match( '/^[a-z]$/i', $term ) ) )
                continue;

            if ( in_array( call_user_func( $strtolower, $term ), $stopwords, true ) )
                continue;

            $checked[] = $term;
        }

        return $checked;

    }

    /*
     * Get array of stopwords
     */
    private function get_search_stopwords() {

        $stopwords = array( 'about','an','are','as','at','be','by','com','for','from','how','in','is','it','of','on','or','that','the','this','to','was','what','when','where','who','will','with','www' );

        return $stopwords;

    }

    /*
     * Query product taxonomies
     */
    private function get_taxonomies( $s, $taxonomy ) {

        global $wpdb;

        $result_array = array();
        $excludes = '';

        $sql = "
			SELECT
				distinct($wpdb->terms.name),
				$wpdb->terms.term_id,
				$wpdb->term_taxonomy.taxonomy,
				$wpdb->term_taxonomy.count
			FROM
				$wpdb->terms
				, $wpdb->term_taxonomy
			WHERE
				name LIKE '%{$s}%'
				AND $wpdb->term_taxonomy.taxonomy = '{$taxonomy}'
				AND $wpdb->term_taxonomy.term_id = $wpdb->terms.term_id
			$excludes
			LIMIT 0, 10";

        $search_results = $wpdb->get_results( $sql );

        if ( ! empty( $search_results ) && !is_wp_error( $search_results ) ) {

            foreach ( $search_results as $result ) {

				$term = get_term( $result->term_id, $result->taxonomy );

				if ( $term != null && !is_wp_error( $term ) ) {
					$term_link = get_term_link( $term );
				} else {
					$term_link = '';
				}

                $new_result = array(
                    'name'     => $result->name,
                    'count'    => $result->count,
                    'link'     => $term_link
                );

                $result_array[] = $new_result;

            }

        }

        return $result_array;

    }

	/*
	 * Get plugin settings
	 */
	public function get_settings( $name ) {
		$plugin_options = $this->data['settings'];
		return $plugin_options[ $name ];
	}

	/*
	 * Add settings link to plugins
	 */
	public function add_settings_link( $links, $file ) {
		$plugin_base = plugin_basename( __FILE__ );

		if ( $file == $plugin_base ) {
			$setting_link = '<a href="options-general.php?page=aws-options">'.__( 'Settings', 'aws' ).'</a>';
			array_unshift( $links, $setting_link );
		}

		return $links;
	}

}

endif;

/**
 * Returns the main instance of AWS_Main
 *
 * @return AWS_Main
 */
function AWS() {
    return AWS_Main::instance();
}


/*
 * Check if WooCommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	add_action( 'woocommerce_loaded', 'aws_init' );
} else {
	add_action( 'admin_notices', 'aws_install_woocommerce_admin_notice' );
}

/*
 * Error notice if WooCommerce plugin is not active
 */
function aws_install_woocommerce_admin_notice() {
	?>
	<div class="error">
		<p><?php _e( 'Advanced Woo Search plugin is enabled but not effective. It requires WooCommerce in order to work.', 'aws' ); ?></p>
	</div>
	<?php
}

/*
 * Init AWS plugin
 */
function aws_init() {
    AWS();
}