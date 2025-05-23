<?php
/**
 * Block support for Presto Player.
 *
 * @package PrestoPlayer
 */

namespace PrestoPlayer\Support;

use PrestoPlayer\Models\Video;
use PrestoPlayer\Models\Player;
use PrestoPlayer\Models\Preset;
use PrestoPlayer\Models\AudioPreset;
use PrestoPlayer\Models\Setting;
use PrestoPlayer\Support\DynamicData;
use PrestoPlayer\Integrations\LearnDash\LearnDash;
use PrestoPlayer\Services\PreloadService;

/**
 * Base block class
 */
class Block {

	/**
	 * The block name (slug)
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * The translated block title
	 *
	 * @var string
	 */
	protected $title = 'Video';

	/**
	 * The template name
	 *
	 * @var string
	 */
	protected $template_name = 'video';

	/**
	 * Attributes
	 *
	 * @var array
	 */
	protected $attributes = array(
		'color'          => array(
			'type'    => 'string',
			'default' => '#00b3ff',
		),
		'blockAlignment' => array(
			'type' => 'string',
		),
		'autoplay'       => array(
			'type' => 'boolean',
		),
		'id'             => array(
			'type' => 'number',
		),
		'src'            => array(
			'type' => 'string',
		),
		'imageID'        => array(
			'type' => 'number',
		),
		'poster'         => array(
			'type' => 'string',
		),
		'content'        => array(
			'type' => 'boolean',
		),
		'pip'            => array(
			'type'    => 'boolean',
			'default' => true,
		),
		'fullscreen'     => array(
			'type'    => 'boolean',
			'default' => true,
		),
		'captions'       => array(
			'type'    => 'boolean',
			'default' => false,
		),
		'hideControls'   => array(
			'type'    => 'boolean',
			'default' => true,
		),
		'playLarge'      => array(
			'type'    => 'boolean',
			'default' => true,
		),
		'chapters'       => array(
			'type'    => 'array',
			'default' => array(),
		),
		'overlays'       => array(
			'type'    => 'array',
			'default' => array(),
		),
		'speed'          => array(
			'type'    => 'boolean',
			'default' => true,
		),
	);

	/**
	 * Attributes to pass to web component
	 *
	 * @var array
	 */
	protected $component_attributes = array(
		'preset',
		'chapters',
		'overlays',
		'tracks',
		'branding',
		'blockAttributes',
		'config',
		'skin',
		'analytics',
		'automations',
		'provider',
		'video_id',
		'videoAttributes',
		'audioAttributes',
		'provider_video_id',
		'youtube',
	);

	/**
	 * Default attributes for the block.
	 *
	 * @var array
	 */
	protected $default_attributes = array(
		'playsInline' => true,
	);

	/**
	 * Constructor.
	 *
	 * @param bool $is_premium Whether the plugin is premium.
	 * @param int  $version Plugin version.
	 */
	public function __construct( bool $is_premium = false, $version = 1 ) {
		do_action( 'presto_player_before_block_output', array( $this, 'middleware' ) );
	}

	/**
	 * Register the block type
	 *
	 * @return void
	 */
	public function register() {
		$this->registerBlockType();
	}

	/**
	 * Get additional attributes for the block.
	 *
	 * @return array
	 */
	public function additionalAttributes() {
		return array();
	}

	/**
	 * Register dynamic block type.
	 *
	 * @return void
	 */
	public function registerBlockType() {
		register_block_type(
			"presto-player/$this->name",
			array(
				'attributes'      => wp_parse_args( $this->additionalAttributes(), $this->attributes ),
				'render_callback' => array( $this, 'html' ),
			)
		);
	}

	/**
	 * Middleware to run before outputting template.
	 *
	 * @param array  $attributes Block attributes.
	 * @param string $content    Block content.
	 * @return boolean           Whether the block should load.
	 */
	public function middleware( $attributes, $content ) {
		return true;
	}

	/**
	 * Sanitize attributes function.
	 *
	 * @param array $attributes     Block attributes.
	 * @param array $default_config Default configuration.
	 * @return array                Sanitized attributes.
	 */
	public function sanitizeAttributes( $attributes, $default_config ) {
		return array();
	}

	/**
	 * Allow overriding attributes.
	 *
	 * @param  array $attributes Block attributes.
	 * @return array
	 */
	public function overrideAttributes( $attributes ) {
		return apply_filters( 'presto_video_block_attributes_override', $attributes, $this );
	}

	/**
	 * Must sanitize attributes.
	 *
	 * @param array $attributes Block attributes.
	 * @return array            Sanitized attributes.
	 */
	private function _sanitizeAttibutes( $attributes ) {

		// attribute overrides.
		$attributes = $this->overrideAttributes( $attributes );

		// Apply default attributes if not set.
		$attributes = $this->applyAttributeDefaults( $attributes );

		// video id.
		$id = ! empty( $attributes['id'] ) ? $attributes['id'] : 0;

		if ( 'audio' === $this->name ) {
			$preset       = $this->getAudioPreset( ! empty( $attributes['preset'] ) ? $attributes['preset'] : 0 );
			$preset->type = 'audio';
		} else {
			$preset = $this->getPreset( ! empty( $attributes['preset'] ) ? $attributes['preset'] : 0 );
		}
		$branding     = $this->getBranding( $preset );
		$class        = $this->getClasses( $attributes );
		$player_class = $this->getPlayerClasses( $id, $preset, $attributes );
		$styles       = $this->getPlayerStyles( $preset, $branding, $attributes );
		$css          = $this->getCSS( $id );
		$src          = ! empty( $attributes['src'] ) ? $attributes['src'] : '';

		// use title or source.
		if ( empty( $attributes['title'] ) ) {
			$video               = $id ? ( new Video( $id ) ) : false;
			$attributes['title'] = $video ? $video->title : $src;
		}

		// Default config.
		$default_config = apply_filters(
			'presto_player/block/default_attributes',
			array(
				'type'            => $this->name,
				'name'            => $this->title,
				'css'             => wp_kses_post( $css ),
				'class'           => $class,
				'is_hls'          => $this->isHls( $src ),
				'styles'          => $styles,
				'skin'            => $preset->skin,
				'playerClass'     => $player_class,
				'id'              => $id,
				'src'             => $src,
				'autoplay'        => ! empty( $attributes['autoplay'] ),
				'playsInline'     => ! empty( $attributes['playsInline'] ),
				'poster'          => ! empty( $attributes['poster'] ) ? $attributes['poster'] : '',
				'branding'        => $branding,
				'youtube'         => array(
					'noCookie'   => (bool) Setting::get( 'youtube', 'nocookie' ),
					'channelId'  => sanitize_text_field( Setting::get( 'youtube', 'channel_id' ) ),
					'show_count' => ! empty( $preset->action_bar['show_count'] ),
				),
				'preload'         => ! empty( $attributes['preload'] ) ? $attributes['preload'] : '',
				'tracks'          => ! empty( $attributes['tracks'] ) ? (array) $attributes['tracks'] : array(),
				'preset'          => $preset ? $preset->toArray() : array(),
				'chapters'        => ! empty( $attributes['chapters'] ) ? $attributes['chapters'] : array(),
				'overlays'        => DynamicData::replaceItems( ! empty( $attributes['overlays'] ) ? $attributes['overlays'] : array(), 'text' ),
				'blockAttributes' => $attributes,
				'provider'        => $this->name,
				'analytics'       => Setting::get( 'analytics', 'enable', false ),
				'automations'     => Setting::get( 'performance', 'automations', true ),
				'title'           => ! empty( $attributes['title'] ) ? html_entity_decode( $attributes['title'] ) : '',
			),
			$attributes
		);

		return wp_parse_args(
			$this->sanitizeAttributes( $attributes, $default_config ),
			$default_config
		);
	}

	/**
	 * Get CSS from settings.
	 * Is it an HLS playlist.
	 *
	 * @param  string $src src parameter.
	 * @return boolean
	 */
	public function isHls( $src ) {
		$src = ! empty( $src ) ? $src : '';
		return \strpos( $src, '.m3u8' ) !== false;
	}

	/**
	 * Get CSS from settings.
	 * Validates before output.
	 *
	 * @param  integer $id the video id.
	 * @return string
	 */
	public function getCSS( $id ) {
		return apply_filters(
			'presto_player/player/css',
			Utility::sanitizeCSS(
				Setting::get( 'branding', 'player_css' ),
				$id
			)
		);
	}

	/**
	 * Gets the preset.
	 *
	 * @param  integer $id Preset ID.
	 * @return \PrestoPlayer\Models\Preset
	 */
	public function getPreset( $id ) {
		$preset    = new Preset( ! empty( $id ) ? $id : 0 );
		$preset_id = $preset->id;

		if ( empty( $preset_id ) ) {
			$preset = $preset->findWhere( array( 'slug' => 'default' ) );
		}

		// replace watermark text.
		if ( ! empty( $preset->watermark['enabled'] ) ) {
			$watermark_text = array(
				'text' => DynamicData::replaceText( $preset->watermark['text'] ),
			);

			$preset->watermark = wp_parse_args( $watermark_text, $preset->watermark );
		}

		return apply_filters( 'presto_player/presto_player_presets/data', $preset, 'video' );
	}

	/**
	 * Gets the audio preset.
	 *
	 * @param  integer $id Preset ID.
	 * @return \PrestoPlayer\Models\AudioPreset
	 */
	public function getAudioPreset( $id ) {
		$preset    = new AudioPreset( ! empty( $id ) ? $id : 0 );
		$preset_id = $preset->id;

		if ( empty( $preset_id ) ) {
			$preset = $preset->findWhere( array( 'slug' => 'default' ) );
		}

		return apply_filters( 'presto_player/presto_player_presets/data', $preset, 'audio' );
	}

	/**
	 * Get player branding.
	 *
	 * @param  \PrestoPlayer\Models\Preset $preset the preset.
	 * @return array
	 */
	public function getBranding( $preset ) {
		$branding = Player::getBranding();

		// sanitize with sensible defaults.
		$branding['color']      = ! empty( $branding['color'] ) ? sanitize_hex_color( $branding['color'] ) : 'rgba(43,51,63,.7)';
		$branding['logo_width'] = ! empty( $branding['logo_width'] ) ? $branding['logo_width'] : 150;
		if ( isset( $branding['logo'] ) ) {
			$branding['logo'] = ! empty( $branding['logo'] && ! $preset->hide_logo ) ? $branding['logo'] : '';
		}

		return $branding;
	}

	/**
	 * Get block classes.
	 *
	 * @param  array $attributes the block attributes.
	 * @return string
	 */
	public function getClasses( $attributes ) {
		$block_alignment = isset( $attributes['align'] ) ? sanitize_text_field( $attributes['align'] ) : '';
		return ! empty( $block_alignment ) ? 'align' . $block_alignment : '';
	}

	/**
	 * Get player classes.
	 *
	 * @param  integer                     $id the video id.
	 * @param  \PrestoPlayer\Models\Preset $preset the preset.
	 * @param  array                       $attributes the block attributes.
	 * @return string
	 */
	public function getPlayerClasses( $id, $preset, $attributes ) {
		$skin          = $preset->skin;
		$player_class  = 'presto-video-id-' . (int) $id;
		$player_class .= ' presto-preset-id-' . (int) $preset->id;

		if ( ! empty( $skin ) ) {
			$player_class .= ' skin-' . sanitize_text_field( $skin );
		}

		$caption_style = $preset->caption_style;
		if ( ! empty( $caption_style ) ) {
			$player_class .= ' caption-style-' . sanitize_html_class( $caption_style );
		}

		if ( ! empty( $attributes['className'] ) ) {
			$player_class .= ' ' . (string) $attributes['className'];
		}

		return $player_class;
	}

	/**
	 * Get player styles.
	 *
	 * @param  \PrestoPlayer\Models\Preset $preset   the preset.
	 * @param  array                       $branding the branding object.
	 * @param  array                       $attributes the block attributes.
	 * @return string
	 */
	public function getPlayerStyles( $preset, $branding, $attributes ) {

		// Set brand color.
		$background_color = ( ! empty( $preset->background_color ) ? sanitize_hex_color( $preset->background_color ) : 'var(--presto-player-highlight-color, ' . sanitize_hex_color( $branding['color'] ) . ')' );
		$styles           = '--plyr-color-main: ' . $background_color . '; ';

		// video.
		if ( $preset->caption_background ) {
			$styles .= '--plyr-captions-background: ' . sanitize_hex_color( $preset->caption_background ) . '; ';
		}
		if ( $preset->border_radius ) {
			$styles .= '--presto-player-border-radius: ' . (int) $preset->border_radius . 'px; ';
		}

		if ( $branding['logo_width'] ) {
			$styles .= '--presto-player-logo-width: ' . (int) $branding['logo_width'] . 'px; ';
		}
		if ( ! empty( $preset->email_collection['border_radius'] ) ) {
			$styles .= '--presto-player-email-border-radius: ' . (int) $preset->email_collection['border_radius'] . 'px; ';
		}

		// audio.
		if ( 'audio' === $preset->type ) {
			if ( $preset->background_color ) {
				$styles .= '--plyr-audio-controls-background: ' . sanitize_hex_color( $preset->background_color ) . ';';
			} else {
				$styles .= '--plyr-audio-controls-background: ' . sanitize_hex_color( $branding['color'] ) . ';';
			}

			if ( $preset->control_color ) {
				$styles .= '--plyr-audio-control-color: ' . sanitize_hex_color( $preset->control_color ) . ';';
				$styles .= '--plyr-range-thumb-background: ' . sanitize_hex_color( $preset->control_color ) . ';';
				$styles .= '--plyr-range-fill-background: ' . sanitize_hex_color( $preset->control_color ) . ';';
				$styles .= '--plyr-audio-progress-buffered-background: ' . Utility::hex2rgba( sanitize_hex_color( $preset->control_color ), 0.35 ) . ';';
				$styles .= '--plyr-range-thumb-shadow: 0 1px 1px ' . Utility::hex2rgba( sanitize_hex_color( $preset->control_color ), 0.15 ) . ', 0 0 0 1px ' . Utility::hex2rgba( sanitize_hex_color( $preset->control_color ), 0.2 ) . ';';
			} else {
				$styles .= '--plyr-audio-control-color: #ffffff;';
				$styles .= '--plyr-range-thumb-background: #ffffff;';
				$styles .= '--plyr-range-fill-background: #ffffff;';
				$styles .= '--plyr-audio-progress-buffered-background: ' . Utility::hex2rgba( sanitize_hex_color( sanitize_hex_color( '#dcdcdc' ) ), 0.35 ) . ';';
			}
		}

		// Set aspect ratio css variable.
		if ( ! empty( $attributes['ratio'] ) ) {
			$styles .= '--presto-player-aspect-ratio: ' . str_replace( ':', '/', esc_attr( $attributes['ratio'] ) ) . ';';
		}

		return $styles;
	}

	/**
	 * Get block attributes.
	 *
	 * @param  array $attributes the block attributes.
	 * @return array
	 */
	public function getAttributes( $attributes ) {
		return $this->_sanitizeAttibutes( $attributes );
	}

	/**
	 * Dynamic block output.
	 *
	 * @param  array  $attributes the block attributes.
	 * @param  string $content    the post content.
	 * @return string
	 */
	public function html( $attributes, $content ) {
		global $presto_player_instance;
		if ( null === $presto_player_instance ) {
			$presto_player_instance = 0;
		}
		++$presto_player_instance;

		// html middleware.
		$load = $this->middleware( $attributes, $content );

		if ( is_feed() ) {
			return $this->getFeedHtml( $attributes );
		}

		if ( LearnDash::isEnabled() ) {
			if ( ! LearnDash::shouldVideoLoad() ) {
				return false;
			}
		}

		// let integrations filter loading capabilities.
		if ( ! apply_filters( 'presto_player_load_video', $load, $attributes, $content, $this->name ) ) {
			// allow a custom fallback.
			$fallback = apply_filters( 'presto_player_load_video_fallback', false, $attributes, $content, $this );
			if ( $fallback ) {
				return wp_kses_post( $fallback );
			}
			return $this->getFallbackHTMLForUnauthorizeAccess();
		}

		// get template data.
		$data = apply_filters( 'presto_player_block_data', $this->getAttributes( $attributes ), $this );

		// need and id and src.
		if ( empty( $data['id'] ) && empty( $data['src'] ) ) {
			return false;
		}

		// Preload component resources.
		$preload_service = new PreloadService();
		$preload_service->add( array( 'presto-player' ) );
		switch ( $this->name ) {
			case 'bunny':
				$preload_service->add( array( 'presto-bunny' ) );
				break;
			case 'youtube':
				$preload_service->add( array( 'presto-youtube' ) );
				break;
			case 'self-hosted':
				$preload_service->add( array( 'presto-video' ) );
				break;
			case 'vimeo':
				$preload_service->add( array( 'presto-vimeo' ) );
				break;
			case 'audio':
				$preload_service->add( array( 'presto-audio' ) );
				break;
			default:
				break;
		}
		$preload_service->bootstrap();

		// TODO: child template system.
		ob_start();

		if ( ! empty( $data['id'] ) ) {
			echo '<!--presto-player:video_id=' . (int) $data['id'] . '-->';
		}

		if ( file_exists( PRESTO_PLAYER_PLUGIN_DIR . "templates/{$this->template_name}.php" ) ) {
			include PRESTO_PLAYER_PLUGIN_DIR . "templates/{$this->template_name}.php";
		}

		$this->initComponentScript( $data['id'], $data, $presto_player_instance );
		$this->iframeFallback( $data );

		// output schema markup for optimized seo.
		$this->outputVideoSchemaMarkup( $this->getSchema( $data ) );

		$template = ob_get_contents();
		ob_end_clean();

		return $template;
	}

	/**
	 * Get json data for video schema.
	 * https://developers.google.com/search/docs/appearance/structured-data/video#video-object.
	 *
	 * @param array $data the block data.
	 *
	 * @return array|bool
	 */
	public function getSchema( $data ) {

		if ( isset( $data ) && empty( $data['id'] ) ) {
			return false;
		}

		if ( 'audio' === $data['type'] ) {
			return false;
		}

		$visibility = $data['blockAttributes']['visibility'] ?? false;
		if ( $visibility && 'private' === $visibility ) {
			return false;
		}

		$title = $data['title'] ?? get_the_title();
		if ( empty( $title ) ) {
			return false;
		}

		$poster = $data['poster'] ?? '';
		if ( empty( $poster ) ) {
			return false;
		}

		$video = new Video( (int) $data['id'] );

		return array(
			// required.
			'@context'     => 'https://schema.org',
			'@type'        => 'VideoObject',
			'name'         => wp_kses_post( $title ),
			'thumbnailUrl' => esc_url( $poster ),
			'uploadDate'   => wp_date( 'c', strtotime( $video->getCreatedAt() ) ),
			// recommended.
			'contentUrl'   => esc_url( $data['src'] ?? '' ),
		);
	}

	/**
	 * Output video schema markup.
	 *
	 * @param array $data the block data.
	 *
	 * @return void|bool
	 */
	public function outputVideoSchemaMarkup( $data ) {

		if ( empty( $data ) ) {
			return false;
		}

		?>
		<script type="application/ld+json">
			<?php
			echo wp_json_encode( $data );
			?>
		</script>
		<?php
	}

	/**
	 * Dynamically initialize component via script tag.
	 *
	 * We have to do this because we cannot send arrays or objects in plain HTML.
	 * This function generates a script tag that sets up the player attributes.
	 *
	 * @param int   $id       The video ID. Default is 0.
	 * @param array $data     An array of data to be passed to the component. Default is an empty array.
	 * @param int   $instance The instance number of the player on the page. Default is 1.
	 *
	 * @return void This function outputs HTML directly and doesn't return a value.
	 */
	public function initComponentScript( $id = 0, $data = array(), $instance = 1 ) {
		if ( ! $id ) {
			return;
		}
		?>
		<script>
			var player = document.querySelector('presto-player#presto-player-<?php echo (int) $instance; ?>');
			player.video_id = <?php echo (int) $id; ?>;
			<?php
			$attributes = apply_filters( 'presto_player/component/attributes', $this->component_attributes, $data );
			foreach ( $attributes as $attribute ) {
				?>
				<?php if ( isset( $data[ $attribute ] ) ) { ?>
					player.<?php echo esc_js( sanitize_text_field( $attribute ) ); ?> = <?php echo wp_json_encode( $data[ $attribute ] ); ?>;
				<?php } ?>
			<?php } ?>
		</script>
		<?php
	}

	/**
	 * Adds an iframe fallback script to the page in case js loading fails.
	 *
	 * This function checks if the video provider is YouTube or Vimeo and adds
	 * a filter to load an iframe fallback script if necessary. This ensures
	 * that the video can still be displayed even if JavaScript fails to load.
	 *
	 * @param array $data An array containing video data, including the 'provider' key.
	 *
	 * @return void This function doesn't return a value, but may add a filter.
	 */
	public function iframeFallback( $data ) {
		// must be vimeo or youtube.
		if ( in_array( $data['provider'], array( 'youtube', 'vimeo' ) ) ) {
			add_filter( 'presto_player/scripts/load_iframe_fallback', '__return_true' );
		}
	}

	/**
	 * This function return HTML for unauthorized access or curtain.
	 *
	 * @return string.
	 */
	public function getFallbackHTMLForUnauthorizeAccess() {
		// Get the branding CSS variable.
		$data = $this->getAttributes( array() );
		ob_start();
		if ( file_exists( PRESTO_PLAYER_PLUGIN_DIR . 'templates/unauthorized.php' ) ) {
			include PRESTO_PLAYER_PLUGIN_DIR . 'templates/unauthorized.php';
		}
		$template = ob_get_contents();
		ob_end_clean();
		return $template;
	}

	/**
	 * Return fallback html for feeds.
	 *
	 * @param array $atts array of attributes.
	 */
	public function getFeedHtml( $atts ) {
		if ( is_feed() ) {
			ob_start();
			?>

			<?php if ( in_array( $this->name, array( 'self-hosted', 'bunny' ) ) && ! empty( $atts['src'] ) ) { ?>
				<video controls preload="none">
					<source src="<?php echo esc_url( $atts['src'] ); ?>" />
				</video>
			<?php } ?>

			<?php if ( 'audio' === $this->name && ! empty( $atts['src'] ) ) { ?>
				<audio controls preload="none">
					<source src="<?php echo esc_url( $atts['src'] ); ?>" />
				</audio>
			<?php } ?>

			<?php if ( 'youtube' === $this->name && ! empty( $atts['video_id'] ) ) { ?>
				<?php echo wp_kses_post( wp_oembed_get( 'https://www.youtube.com/watch?v=' . esc_attr( $atts['video_id'] ) ) ); ?>
			<?php } ?>

			<?php if ( 'vimeo' === $this->name && ! empty( $atts['video_id'] ) ) { ?>
				<?php echo wp_kses_post( wp_oembed_get( 'https://vimeo.com/' . esc_attr( $atts['video_id'] ) ) ); ?>
			<?php } ?>

			<?php
			return ob_get_clean();
		}
	}

	/**
	 * Applies a default value to the attribute if attribute is not set.
	 *
	 * @param  array $attributes array of attributes.
	 * @return array The merged attributes after applying defaults.
	 */
	public function applyAttributeDefaults( $attributes ) {
		return wp_parse_args( $attributes, $this->default_attributes );
	}
}
