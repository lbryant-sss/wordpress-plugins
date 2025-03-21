<?php

/**
 * Handles custom fonts functionality in control panel.
 *
 * @package JupiterX_Core\Control_Panel_2\Custom_Fonts
 *
 * @since 2.5.0
 */
class JupiterX_Core_Control_Panel_Custom_Fonts {

	private static $instance = null;

	const POST_TYPE = 'jupiterx-fonts';

	/**
	 * Instance of class.
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		add_action( 'wp_ajax_jupiterx_custom_fonts', [ $this, 'handle_ajax' ] );
		add_action( 'wp_ajax_jupiterx_custom_fonts_get_posts', [ $this, 'get_posts' ] );
	}

	/**
	 * Handle ajax requests.
	 * Gets Ajax call sub_action parameter and call a function based on parameter value.
	 *
	 * @return void
	 * @since 2.5.0
	 */
	public function handle_ajax() {
		check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'You do not have access to this section.', 'jupiterx-core' );
		}

		$action = filter_input( INPUT_POST, 'sub_action', FILTER_UNSAFE_RAW );

		if ( ! empty( $action ) && method_exists( $this, $action ) ) {
			call_user_func( [ $this, $action ] );
		}
	}

	/**
	 * Gets Custom fonts posts.
	 *
	 * @return void
	 * @since 2.5.0
	 */
	public function get_posts() {
		check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'You do not have access to this section.', 'jupiterx-core' );
		}

		$paged = filter_input( INPUT_GET, 'paged', FILTER_SANITIZE_NUMBER_INT );

		/**
		 * Filter List Table query arguments.
		 *
		 * @param array $args The query arguments.
		 *
		 * @since 2.5.0
		 */
		$args = apply_filters( 'jupiterx_custom_font_list_table_' . self::POST_TYPE . '_args', [
			'post_type'      => self::POST_TYPE,
			'paged'          => $paged,
			'posts_per_page' => 20,
		] );

		$query = new \WP_Query( $args );

		/**
		 * Filter List Table query posts.
		 *
		 * @param array $args The taxonomy arguments.
		 *
		 * @since 2.5.0
		 */
		$posts = apply_filters( 'jupiterx_custom_font_list_table_' . self::POST_TYPE . '_posts', $query->posts );

		/**
		 * Filter List Table columns.
		 *
		 * @param array $args The columns headings and values.
		 *
		 * @since 2.5.0
		 */
		$columns = apply_filters( 'jupiterx_custom_font_list_table_' . self::POST_TYPE . '_columns', [
			'labels' => [
				esc_html__( 'Author', 'jupiterx-core' ),
				esc_html__( 'Preview', 'jupiterx-core' ),
				esc_html__( 'Created on', 'jupiterx-core' ),
			],
			'values' => [ '' ],
		], $posts );

		// columns value.
		foreach ( $posts as $key => $post ) {
			$columns['values'][ "post_{$post->ID}" ] = [
				get_the_author_meta( 'user_login', get_post_field( 'post_author', $post->ID ) ),
				$this->get_custom_font_data( $post ),
				get_the_time( 'Y-m-d', $post->ID ),
			];

			$post->user_url = get_edit_user_link( get_the_author_meta( 'ID', get_post_field( 'post_author', $post->ID ) ) );
		}

		// Send response.
		wp_send_json_success( [
			'posts'         => $posts,
			'max_num_pages' => $query->max_num_pages,
			'columns'       => $columns,
		] );
	}

	/**
	 * Generates the CSS font face for each font from the font family name and font data.
	 *
	 * @param $font_family
	 * @param $data
	 *
	 * @return string
	 * @since 2.5.0
	 */
	public function get_font_face_from_data( $font_family, $data ) {
		$font_face = '';
		$src       = '';

		foreach ( $data as $variation ) {
			$src = $this->get_font_src_per_type( $variation );

			$font_face .= '@font-face{';
			$font_face .= 'font-family:\'' . $font_family . '\';';
			$font_face .= 'font-style:' . $variation['font_style'] . ';';
			$font_face .= 'font-weight:' . $variation['font_weight'] . ';';
			$font_face .= 'src:' . implode( ',', $src ) . ';';
			$font_face .= '}';
		}

		return $font_face;
	}

	private function get_font_src_per_type( $variation ) {
		$src = [];

		foreach ( [ 'woff', 'woff2', 'svg', 'ttf' ] as $type ) {
			if ( empty( $variation[ $type ] ) ) {
				continue;
			}

			if ( in_array( $type, [ 'woff', 'woff2', 'svg' ], true ) ) {
				$src[] = 'url(\'' . esc_attr( $variation[ $type ] ) . '\')format(\'' . $type . '\')';
			}

			if ( 'ttf' === $type ) {
				$src[] = 'url(\'' . esc_attr( $variation[ $type ] ) . '\')format(\'truetype\')';
			}
		}

		return $src;
	}

	/**
	 * Create and update post by ajax.
	 *
	 * @return void
	 * @since 2.5.0
	 */
	public function save_post() {
		$post = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_FORCE_ARRAY );

		if ( empty( $post['custom_fonts_post_title'] ) ) {
			wp_send_json_error( esc_html__( 'Name of the custom font can not be empty.', 'jupiterx-core' ) );
		}

		if ( empty( $post['custom_fonts_post_variation_url'] ) ) {
			wp_send_json_error( esc_html__( 'You should add font variations before saving.', 'jupiterx-core' ) );
		}

		//If it is not update, don't let duplicate title.
		$query_args = [
			'post_type'      => self::POST_TYPE,
			's'              => $post['custom_fonts_post_title'],
			'posts_per_page' => 1,
		];

		$fonts_query      = new WP_Query( $query_args );
		$current_font_obj = ( is_array( $fonts_query->get_posts() ) && count( $fonts_query->get_posts() ) > 0 ) ? (object) $fonts_query->get_posts()[0] : false;

		if ( empty( $post['custom_fonts_submit_mode'] ) && ! empty( $current_font_obj->ID ) ) {
			wp_send_json_error( esc_html__( 'This font title already exists. Please choose another one.', 'jupiterx-core' ) );
		}

		$post_data = [
			'post_title'   => wp_strip_all_tags( $post['custom_fonts_post_title'] ),
			'post_content' => wp_json_encode( $post['custom_fonts_post_variation_url'] ),
			'post_status'  => 'publish',
			'post_type'    => self::POST_TYPE,
			'meta_input'   => [
				'jupiterx_font_face' => $this->get_font_face_from_data( $post['custom_fonts_post_title'], $post['custom_fonts_post_variation_url'] ),
			],
		];

		// Check if it's update query.
		if ( '' !== $post['custom_fonts_submit_mode'] ) {
			$post_data['ID'] = $post['custom_fonts_submit_mode'];
		}

		$result = wp_insert_post( $post_data );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_message() );
		}

		wp_send_json_success();
	}

	/**
	 * Remove post by ajax.
	 *
	 * @return void
	 * @since 2.5.0
	 */
	public function remove_post() {
		$post   = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );
		$result = $this->delete_post( $post );

		if ( empty( $result ) ) {
			wp_send_json_error();
		}

		wp_send_json_success();
	}

	/**
	 * Delete a post.
	 *
	 * @param int $id
	 *
	 * @return array|false|WP_Post
	 * @since 2.5.0
	 */
	private function delete_post( $id ) {
		return wp_delete_post( $id, true );
	}

	/**
	 * Returns decoded custom font data as an object.
	 *
	 * @param $font
	 *
	 * @return object
	 * @since 2.5.0
	 */
	private function get_custom_font_data( $font ) {
		$font_settings = json_decode( $font->post_content );

		return (object) [ 'font_settings' => $font_settings ];
	}
}

JupiterX_Core_Control_Panel_Custom_Fonts::get_instance();
