<?php

/**
 * @class FLVideoModule
 */
class FLVideoModule extends FLBuilderModule {

	/**
	 * @property $data
	 */
	public $data = null;

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'            => __( 'Video', 'fl-builder' ),
			'description'     => __( 'Render a WordPress or embedable video.', 'fl-builder' ),
			'category'        => __( 'Basic', 'fl-builder' ),
			'icon'            => 'format-video.svg',
			'partial_refresh' => true,
			'include_wrapper' => false,
		));

		$this->add_js( 'jquery-fitvids' );

		add_filter( 'wp_video_shortcode', __CLASS__ . '::mute_video', 10, 4 );
	}

	/**
	 * @method get_data
	 */
	public function get_data() {
		if ( ! $this->data ) {

			$this->data = FLBuilderPhoto::get_attachment_data( $this->settings->video );

			if ( ! empty( $this->data ) && isset( $this->settings->data ) ) {
				$this->data = $this->settings->data;
			} else {
				$data            = new stdClass();
				$data->url       = '';
				$data->extension = '';

				if ( ! empty( $this->settings->connections['video'] ) || ! empty( $this->settings->connections['video_webm'] ) ) {
					$data->url = ! empty( $this->settings->video ) ? $this->settings->video : $this->settings->video_webm;
				} elseif ( ! empty( $this->settings->data->url ) ) {
					$data->url = $this->settings->data->url;
				}

				if ( ! empty( $data->url ) ) {
					$video_file      = wp_check_filetype( $data->url );
					$data->extension = $video_file['ext'];
				}

				$this->data = $data;
			}

			if ( $this->data ) {
				$parts     = ! empty( $this->data->filename ) ? explode( '.', $this->data->filename ) : [];
				$extension = '';
				if ( isset( $this->data->extension ) ) {
					$extension = $this->data->extension;
				} elseif ( ! empty( $parts ) ) {
					$extension = array_pop( $parts );
				}

				/**
				 * Set video file extension.
				 * @see fl_builder_video_module_extension
				 */
				$this->data->extension = apply_filters( 'fl_builder_video_module_extension', $extension, $this->settings );
				$this->data->poster    = isset( $this->settings->poster_src ) ? $this->settings->poster_src : '';
				$this->data->loop      = isset( $this->settings->loop ) && $this->settings->loop ? ' loop="yes"' : '';
				$this->data->autoplay  = isset( $this->settings->autoplay ) && $this->settings->autoplay ? ' autoplay="yes"' : '';

				// WebM format
				$webm_data              = FLBuilderPhoto::get_attachment_data( $this->settings->video_webm );
				$this->data->video_webm = isset( $this->settings->video_webm ) && $webm_data ? ' webm="' . $webm_data->url . '"' : '';

				if ( $webm_data && empty( $this->data->url ) ) {
					$this->data->url = $webm_data->url;
				}
			}
		}

		return $this->data;
	}

	/**
	 * @since 2.4
	 * @method render_poster_html
	 */
	public function render_video_html( $schema ) {
		$video_html   = '';
		$video_poster = $this->get_poster_url();
		$video_meta   = '';

		if ( 'media_library' === $this->settings->video_type ) {
			$vid_data = $this->get_data();
			$preload  = FLBuilderModel::is_builder_active() && ! empty( $vid_data->poster ) ? ' preload="none"' : '';

			$video_meta .= '<meta itemprop="url" content="' . ( empty( $vid_data->url ) ? '' : $vid_data->url ) . '" />';
			if ( $schema ) {
				$video_meta .= '<meta itemprop="thumbnail" content="' . $video_poster . '" />';
			}

			$video_html = $video_meta;

			$video_sc = sprintf( '%s', __( 'Video not specified. Please select one to display.', 'fl-builder' ) );

			if ( ! empty( $vid_data->url ) ) {
				$video_sc = '[video src="' . preg_replace( '/\/?\?.*/', '', $vid_data->url ) . '" ' . $vid_data->extension . '="' . preg_replace( '/\/?\?.*/', '', $vid_data->url ) . '"' . $vid_data->video_webm . ' poster="' . $video_poster . '" ' . $vid_data->autoplay . $vid_data->loop . $preload . '][/video]';
			}

			if ( 'yes' === $this->settings->video_lightbox ) {
				$video_html .= '<div id="fl-node-' . $this->node . '-lightbox-content" class="fl-node-' . $this->node . '-lightbox-content' . ' fl-video-lightbox-content ' . ( empty( $vid_data->url ) ? '' : 'mfp-hide' ) . '">';
				$video_html .= $video_sc;
				$video_html .= '</div>';
			} else {
				$video_html .= $video_sc;
			}
		} elseif ( 'embed' === $this->settings->video_type ) {
			global $wp_embed;

			$video_embed = '';
			if ( ! empty( $this->settings->embed_code ) ) {
				$video_embed = $wp_embed->autoembed( do_shortcode( $this->settings->embed_code ) );
			} elseif ( ! isset( $this->settings->connections ) ) {
				$video_embed = sprintf( '%s', __( 'Video embed code not specified.', 'fl-builder' ) );
			}

			if ( 'yes' == $this->settings->video_lightbox ) {
				$video_html  = '<div id="fl-node-' . $this->node . '-lightbox-content" class="fl-node-' . $this->node . '-lightbox-content' . ' fl-video-lightbox-content ' . ( empty( $this->settings->embed_code ) ? '' : 'mfp-hide' ) . '">';
				$video_html .= $video_embed;
				$video_html .= '</div>';
			} else {
				$video_html = $video_embed;
			}
		}

		echo $video_html;
	}

	/**
	 * @since 2.4
	 * @method render_poster_html
	 */
	public function render_poster_html() {
		$poster_html = '';
		if ( 'yes' === $this->settings->video_lightbox ) {
			$poster_url = $this->get_poster_url();
			if ( empty( $poster_url ) ) {
				$poster_html .= '<div class="fl-video-poster">';
				$poster_html .= sprintf( '%s', __( 'Please specify a poster image if Video Lightbox is enabled.', 'fl-builder' ) );
				$poster_html .= '</div>';
			} else {
				$video_url    = esc_url( $this->get_video_url() );
				$size         = isset( $this->settings->poster_size ) && ! empty( $this->settings->poster_size ) ? $this->settings->poster_size : 'large';
				$poster_html .= '<div class="fl-video-poster" ';
				$poster_html .= 'role="button" ';
				$poster_html .= 'tabindex="0" ';
				$poster_html .= 'aria-label="' . esc_attr__( 'Play Video', 'fl-builder' ) . '" ';
				$poster_html .= 'data-mfp-src="' . $video_url . '">';
				$poster_html .= wp_get_attachment_image( $this->settings->poster, $size, '', array( 'class' => 'img-responsive' ) );
				$poster_html .= '</div>';
			}
		}

		echo $poster_html;
	}

	/**
	 * @since 2.4
	 * @method get_poster_url
	 */
	private function get_poster_url() {
		$url = empty( $this->settings->poster ) ? '' : $this->settings->poster_src;
		return $url;
	}

	/**
	 * @since 2.4
	 * @method get_video_url
	 */
	private function get_video_url() {
		$settings  = $this->settings;
		$video_url = '';

		if ( 'yes' === $settings->video_lightbox ) {
			if ( 'embed' == $settings->video_type ) {
				if ( strstr( $settings->embed_code, 'vimeo.com' ) ) {
					$vid_id    = $this->get_video_id( 'vimeo', $settings->embed_code );
					$video_url = 'https://vimeo.com/' . $vid_id;
				} elseif ( strstr( $settings->embed_code, 'facebook.com' ) ) {
					$video_url = $this->get_video_id( 'facebook', $settings->embed_code );
				} elseif ( strstr( $settings->embed_code, 'youtube' ) || strstr( $settings->embed_code, 'youtu.be' ) ) {
					$vid_id = $this->get_video_id( 'youtube', $settings->embed_code );
					if ( strstr( $settings->embed_code, 'youtube-nocookie' ) ) {
						$video_url = 'https://youtube-nocookie.com/embed/' . $vid_id;
					} else {
						$video_url = 'https://youtube.com/watch?v=' . $vid_id;
					}
				} else {
					$video_url = '';
				}
			} elseif ( 'media_library' == $settings->video_type ) {
				$vid_data  = $this->get_data();
				$video_url = ! empty( $vid_data->url ) ? $vid_data->url : '';
			}
		}

		return $video_url;
	}

	/**
	 * @method get_video_id
	 * @param string $source
	 * @param string $embed_code
	 */
	private function get_video_id( $source = '', $embed_code = '' ) {
		$matches = array();
		$id      = '';
		$regex   = '';

		$youtube_regex  = '#(?:youtube(?:-nocookie)?\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})#ix';
		$vimeo_regex    = '~(?:<iframe [^>]*src=")?(?:https?:\/\/(?:[\w]+\.)*vimeo\.com(?:[\/\w]*\/videos?)?\/([0-9]+)[^\s]*)"?(?:[^>]*></iframe>)?(?:<p>.*</p>)?~ix';
		$facebook_regex = '/(?<=src=").*?(?=[\*"])/';

		if ( 'vimeo' == $source ) {
			$regex = $vimeo_regex;
		} elseif ( 'youtube' == $source ) {
			$regex = $youtube_regex;
		} elseif ( 'facebook' == $source ) {
			$regex = $facebook_regex;
		}

		preg_match( $regex, $embed_code, $matches );

		if ( ! empty( $matches ) ) {
			$id = $matches[ count( $matches ) - 1 ];
		}

		return $id;
	}

	/**
	 * @method update
	 * @param $settings {object}
	 */
	public function update( $settings ) {
		// Cache the attachment data.
		if ( 'media_library' == $settings->video_type ) {

			$video = FLBuilderPhoto::get_attachment_data( $settings->video );

			if ( $video ) {
				$settings->data = $video;
			} else {
				$settings->data = null;
			}
		}

		return $settings;
	}

	/**
	 * Temporary fix for autoplay in Chrome & Safari. Video shortcode doesn't support `muted` parameter.
	 * Bug report: https://core.trac.wordpress.org/ticket/42718.
	 *
	 * @since 2.1.3
	 * @param string $output  Video shortcode HTML output.
	 * @param array  $atts    Array of video shortcode attributes.
	 * @param string $video   Video file.
	 * @param int    $post_id Post ID.
	 * @return string
	 */
	static public function mute_video( $output, $atts, $video, $post_id ) {
		if ( preg_match( '/\sautoplay[\s|=]/', $output ) && FLBuilderModel::get_post_id() == $post_id ) {
			$output = str_replace( '<video', '<video muted', $output );
		}
		return $output;
	}

	/**
	 * Calculate video aspect ratio for style.
	 *
	 * @since 2.2
	 * @return float
	 */
	public function video_aspect_ratio() {
		$data = $this->get_data();
		if ( $data && function_exists( 'bcdiv' ) && isset( $data->width ) && isset( $data->height ) && ( (int) $data->height > 0 ) && ( (int) $data->width > 0 ) ) {
			$ratio = ( $data->height / $data->width ) * 100;
			return bcdiv( $ratio, 1, 2 );
		}
	}

	/**
	 * Returns structured data markup.
	 * @since 2.2
	 */
	public function get_structured_data() {
		$settings = $this->settings;

		if ( 'yes' != $settings->schema_enabled ) {
			return false;
		}

		$markup = '';
		if ( ! empty( $settings->name ) ) {
			$markup .= sprintf( '<meta itemprop="name" content="%s" />', esc_attr( $settings->name ) );
		}
		if ( ! empty( $settings->up_date ) ) {
			$markup .= sprintf( '<meta itemprop="uploadDate" content="%s" />', esc_attr( gmdate( DATE_ATOM, strtotime( $settings->up_date ) ) ) );
		}
		if ( ! empty( $settings->thumbnail_src ) ) {
			$markup .= sprintf( '<meta itemprop="thumbnailUrl" content="%s" />', $settings->thumbnail_src );
		}
		if ( ! empty( $settings->description ) ) {
			$markup .= sprintf( '<meta itemprop="description" content="%s" />', esc_attr( $settings->description ) );
		}
		if ( ! empty( $settings->content_url ) ) {
			$markup .= sprintf( '<meta itemprop="contentUrl" content="%s" />', esc_attr( $settings->content_url ) );
		}
		if ( ! empty( $settings->embed_url ) ) {
			$markup .= sprintf( '<meta itemprop="embedUrl" content="%s" />', esc_attr( $settings->embed_url ) );
		}

		return $markup;
	}

	/**
	 * @method enqueue_scripts
	 * @since 2.4
	 */
	public function enqueue_scripts() {
		if ( $this->settings && 'yes' == $this->settings->video_lightbox ) {
			$this->add_js( 'jquery-magnificpopup' );
			$this->add_css( 'jquery-magnificpopup' );
		}
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLVideoModule', array(
	'general' => array(
		'title'    => __( 'General', 'fl-builder' ),
		'sections' => array(
			'general'                => array(
				'title'  => '',
				'fields' => array(
					'video_type'       => array(
						'type'    => 'select',
						'label'   => __( 'Video Type', 'fl-builder' ),
						'default' => 'wordpress',
						'options' => array(
							'media_library' => __( 'Media Library', 'fl-builder' ),
							'embed'         => __( 'Embed', 'fl-builder' ),
						),
						'toggle'  => array(
							'media_library' => array(
								'sections' => array( 'video_controls_section' ),
								'fields'   => array( 'video', 'video_webm', 'autoplay', 'loop' ),
							),
							'embed'         => array(
								'fields' => array( 'embed_code' ),
							),
						),
					),
					'video'            => array(
						'type'        => 'video',
						'label'       => __( 'Main Video (MP4)', 'fl-builder' ),
						'connections' => array( 'custom_field' ),
						'help'        => __( 'Main video, which could be in mp4, m4v, webm, ogv, or wmv format. Most modern browsers support this format.', 'fl-builder' ),
						'show_remove' => true,
					),
					'video_webm'       => array(
						'type'        => 'video',
						'show_remove' => true,
						'label'       => __( 'Fallback Video (WebM)', 'fl-builder' ),
						'connections' => array( 'custom_field' ),
						'help'        => __( 'A video in the WebM format to use as fallback. This format is required to support browsers such as FireFox and Opera.', 'fl-builder' ),
						'preview'     => array(
							'type' => 'none',
						),
					),
					'embed_code'       => array(
						'type'        => 'code',
						'wrap'        => true,
						'editor'      => 'html',
						'label'       => '',
						'rows'        => '9',
						'connections' => array( 'custom_field', 'url' ),
					),
					'video_lightbox'   => array(
						'type'    => 'select',
						'label'   => __( 'Show Video on Lightbox', 'fl-builder' ),
						'default' => 'no',
						'options' => array(
							'yes' => __( 'Yes', 'fl-builder' ),
							'no'  => __( 'No', 'fl-builder' ),
						),
						'help'    => __( 'Poster Image must be specified for the Lightbox to work.', 'fl-builder' ),
					),
					'poster'           => array(
						'type'        => 'photo',
						'show_remove' => true,
						'label'       => _x( 'Poster', 'Video preview/fallback image.', 'fl-builder' ),
						'help'        => __( 'An image must be specified for the Lightbox to work.', 'fl-builder' ),
					),
					'autoplay'         => array(
						'type'    => 'select',
						'label'   => __( 'Auto Play', 'fl-builder' ),
						'default' => '0',
						'options' => array(
							'0' => __( 'No', 'fl-builder' ),
							'1' => __( 'Yes', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
					'loop'             => array(
						'type'    => 'select',
						'label'   => __( 'Loop', 'fl-builder' ),
						'default' => '0',
						'options' => array(
							'0' => __( 'No', 'fl-builder' ),
							'1' => __( 'Yes', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
					'sticky_on_scroll' => array(
						'type'    => 'select',
						'label'   => __( 'Sticky on Scroll', 'fl-builder' ),
						'default' => 'no',
						'options' => array(
							'no'  => __( 'No', 'fl-builder' ),
							'yes' => __( 'Yes', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
				),
			),
			'video_controls_section' => array(
				'title'  => 'Video Controls',
				'fields' => array(
					'play_pause'  => array(
						'type'    => 'select',
						'label'   => __( 'Play/Pause', 'fl-builder' ),
						'default' => 'show',
						'options' => array(
							'show' => __( 'Show', 'fl-builder' ),
							'hide' => __( 'Hide', 'fl-builder' ),
						),
					),
					'timer'       => array(
						'type'    => 'select',
						'label'   => __( 'Timer', 'fl-builder' ),
						'default' => 'show',
						'options' => array(
							'show' => __( 'Show', 'fl-builder' ),
							'hide' => __( 'Hide', 'fl-builder' ),
						),
					),
					'time_rail'   => array(
						'type'    => 'select',
						'label'   => __( 'Time Rail', 'fl-builder' ),
						'default' => 'show',
						'options' => array(
							'show' => __( 'Show', 'fl-builder' ),
							'hide' => __( 'Hide', 'fl-builder' ),
						),
					),
					'duration'    => array(
						'type'    => 'select',
						'label'   => __( 'Duration', 'fl-builder' ),
						'default' => 'show',
						'options' => array(
							'show' => __( 'Show', 'fl-builder' ),
							'hide' => __( 'Hide', 'fl-builder' ),
						),
					),
					'volume'      => array(
						'type'    => 'select',
						'label'   => __( 'Volume', 'fl-builder' ),
						'default' => 'show',
						'options' => array(
							'show' => __( 'Show', 'fl-builder' ),
							'hide' => __( 'Hide', 'fl-builder' ),
						),
					),
					'full_screen' => array(
						'type'    => 'select',
						'label'   => __( 'Fullscreen', 'fl-builder' ),
						'default' => 'show',
						'options' => array(
							'show' => __( 'Show', 'fl-builder' ),
							'hide' => __( 'Hide', 'fl-builder' ),
						),
					),
				),
			),
		),
	),
	'schema'  => array(
		'title'    => 'Structured Data',
		'sections' => array(
			'schema' => array(
				'fields' => array(
					'schema_enabled' => array(
						'type'    => 'select',
						'label'   => __( 'Enable Structured Data?', 'fl-builder' ),
						'default' => 'no',
						'preview' => array(
							'type' => 'none',
						),
						'toggle'  => array(
							'yes' => array(
								'fields' => array( 'name', 'description', 'thumbnail', 'up_date', 'content_url', 'embed_url' ),
							),
						),
						'options' => array(
							'yes' => __( 'Yes', 'fl-builder' ),
							'no'  => __( 'No', 'fl-builder' ),
						),
					),
					'name'           => array(
						'type'        => 'text',
						'label'       => __( 'Video Name', 'fl-builder' ),
						'connections' => array( 'string' ),
						'preview'     => array(
							'type' => 'none',
						),
					),
					'description'    => array(
						'type'        => 'text',
						'label'       => __( 'Video Description', 'fl-builder' ),
						'connections' => array( 'string' ),
						'preview'     => array(
							'type' => 'none',
						),
					),
					'content_url'    => array(
						'type'        => 'text',
						'label'       => __( 'Content URL', 'fl-builder' ),
						'connections' => array( 'url' ),
						'preview'     => array(
							'type' => 'none',
						),
					),
					'embed_url'      => array(
						'type'        => 'text',
						'label'       => __( 'Embed URL', 'fl-builder' ),
						'connections' => array( 'url' ),
						'preview'     => array(
							'type' => 'none',
						),
					),
					'thumbnail'      => array(
						'type'        => 'photo',
						'label'       => __( 'Video Thumbnail', 'fl-builder' ),
						'connections' => array( 'photo', 'url' ),
						'show_remove' => true,
						'preview'     => array(
							'type' => 'none',
						),
					),
					'up_date'        => array(
						'type'        => 'date',
						'label'       => __( 'Upload Date', 'fl-builder' ),
						'connections' => array( 'string' ),
						'preview'     => array(
							'type' => 'none',
						),
					),
				),
			),
		),
	),
));
