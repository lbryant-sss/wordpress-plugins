<?php

class Meow_WPMC_Core {

	
	public $admin = null;
	public $is_rest = false;
	public $is_cli = false;
	public $is_pro = false;
	public $engine = null;
	public $catch_timeout = true; // This will halt the plugin before reaching the PHP timeout.
	public $types = "jpg|jpeg|jpe|gif|png|tiff|bmp|csv|svg|pdf|xls|xlsx|doc|docx|odt|wpd|rtf|tiff|mp3|mp4|mov|wav|lua|webp|avif|ico";
	public $current_method = 'media';
	public $servername = null; // meowapps.com (site URL without http/https)
	public $site_url = null; // https://meowapps.com
	public $upload_path = null; // /www/wp-content/uploads (path to uploads)
	public $upload_url = null; // wp-content/uploads (uploads without domain)
	private $option_name = 'wpmc_options';
	private $nonce = null; // Nonce for the REST API

	private $regex_file = '/[A-Za-z0-9-_,.\(\)\s]+[.]{1}(MIMETYPES)/';

	private $refcache = array();
	private $use_cached_references = false;
	private $progress_key = 'wpmc_progress';
	private $cached_ids_key = 'wpmc_cached_ids';
	private $cached_urls_key = 'wpmc_cached_urls';

	private $cached_ids_cli  = array();
	private $cached_urls_cli = array();

	private $check_content = null;
	private $debug_logs = null;
	private $multilingual = false;
	private $languages = array();
	private $shortcode_analysis = false;

	public function get_shortcode_analysis() {
		return $this->shortcode_analysis;
	}

	private $ref_index_exists = false;

	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'delete_attachment', array( $this, 'delete_attachment_related_data' ), 10, 1 );
		add_action( 'trashed_post', array( $this, 'delete_attachment_related_data' ), 10, 1 );
	}

	function plugins_loaded() {



		// Variables
		$this->site_url = get_site_url();
		$this->multilingual = $this->is_multilingual();
		$this->languages = $this->get_languages();
		$this->current_method = $this->get_option( 'method' );
		$this->regex_file = str_replace( "MIMETYPES", $this->types, $this->regex_file );
		$this->servername = str_replace( 'http://', '', str_replace( 'https://', '', $this->site_url ) );
		$uploaddir = wp_upload_dir();
		$this->upload_path = $uploaddir['basedir'];
		$this->upload_url = substr( $uploaddir['baseurl'], strlen( $this->site_url ) );
		$this->check_content = $this->get_option( 'content' );
		$this->debug_logs = $this->get_option( 'debuglogs' );
		$this->is_rest = MeowCommon_Helpers::is_rest();
		$this->is_cli = defined( 'WP_CLI' ) && WP_CLI;
		$this->shortcode_analysis = !$this->get_option( 'shortcodes_disabled' );
		$this->use_cached_references = $this->get_option( 'use_cached_references' );
		
		global $wpmc;
		$wpmc = $this;

		// Language
		load_plugin_textdomain( WPMC_DOMAIN, false, basename( WPMC_PATH ) . '/languages' );

		// Admin
		$this->admin = new Meow_WPMC_Admin( $this );

		// Advanced core
		if ( class_exists( 'MeowPro_WPMC_Core' ) ) {
			new MeowPro_WPMC_Core( $this );
		}

		// Install hooks and engine only if they might be used
		if ( is_admin() || $this->is_rest || $this->is_cli ) {
			add_action( 'wpmc_initialize_parsers', array( $this, 'initialize_parsers' ), 10, 0 );
			add_filter( 'wp_unique_filename', array( $this, 'wp_unique_filename' ), 10, 3 );
			$this->engine = new Meow_WPMC_Engine( $this, $this->admin );
		}

		// Only for REST
		if ( $this->is_rest ) {
			new Meow_WPMC_Rest( $this, $this->admin );
		}

		if ( is_admin() ) {
			new Meow_WPMC_UI( $this );
		}
	}

	function init() {
		remove_action( 'wp_scheduled_delete', 'wp_scheduled_delete' );
	}

	public function get_nonce( $force = false ) {
		if ( !$force && !is_user_logged_in() ) {
			return null;
		}
		if ( isset( $this->nonce ) ) {
			return $this->nonce;
		}

		$this->nonce = wp_create_nonce( 'wp_rest' );
		return $this->nonce;
	}

	function initialize_parsers() {
		include_once( 'parsers.php' );
		new Meow_WPMC_Parsers();
	}

	function deepsleep( $seconds ) {
		$start_time = time();
		while( true ) {
			if ( ( time() - $start_time ) > $seconds ) {
				return false;
			}
			get_post( array( 'posts_per_page' => 50 ) );
		}
	}

	private $start_time;
	private $time_elapsed = 0;
	private $time_remaining = 0;
	private $item_scan_avg_time = 0;
	private $wordpress_init_time = 0.5;
	private $max_execution_time;
	private $items_checked = 0;
	private $items_count = 0;

	function get_max_execution_time() {
		if ( isset( $this->max_execution_time ) )
			return $this->max_execution_time;

		$this->max_execution_time = ini_get( "max_execution_time" );
		if ( empty( $this->max_execution_time ) || $this->max_execution_time < 5 )
			$this->max_execution_time = 30;

		return $this->max_execution_time;
	}

	function timeout_check_start( $count ) {
		$this->start_time = time();
		$this->items_count = $count;
		$this->get_max_execution_time();
	}

	function timeout_get_elapsed() {
		return $this->time_elapsed . 'ms';
	}

	function timeout_check() {
		$this->time_elapsed = time() - $this->start_time;
		$this->time_remaining = $this->max_execution_time - $this->wordpress_init_time - $this->time_elapsed;
		if ( $this->catch_timeout ) {
			if ( $this->time_remaining - $this->item_scan_avg_time < 0 ) {
				error_log("Media Cleaner Timeout! Check the Media Cleaner logs for more info.");
				$this->log( "😵 Timeout! Some info for debug:" );
				$this->log( "🍀 Elapsed time: $this->time_elapsed" );
				$this->log( "🍀 WP init time: $this->wordpress_init_time" );
				$this->log( "🍀 Remaining time: $this->time_remaining" );
				$this->log( "🍀 Scan time per item: $this->item_scan_avg_time" );
				$this->log( "🍀 PHP max_execution_time: $this->max_execution_time" );
				header("HTTP/1.0 408 Request Timeout");
				exit;
			}
		}
	}

	function delete_attachment_related_data( $post_id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_scan";
		$wpdb->query( $wpdb->prepare( "DELETE FROM $table_name WHERE postId = %d", $post_id ) );
	}

	function timeout_check_additem() {
		$this->items_checked++;
		$this->time_elapsed = time() - $this->start_time;
		$this->item_scan_avg_time = ceil( ( $this->time_elapsed / $this->items_checked ) * 10 ) / 10;
	}

	// This checks if a new uploaded filename isn't the same one as a currently
	// filename in the trash (that would cause issues)
	function wp_unique_filename( $filename, $ext, $dir ) {
		$fullpath = trailingslashit( $dir ) . $filename;
		$relativepath = $this->clean_uploaded_filename( $fullpath );
		$trashfilepath = trailingslashit( $this->get_trashdir() ) . $relativepath;
		if ( file_exists( $trashfilepath ) ) {
			$path_parts = pathinfo( $fullpath );
			$filename_noext = $path_parts['filename'];
			$new_filename = $filename_noext . '-' . date('Ymd-His', time()) . '.' . $path_parts['extension'];
			//error_log( 'POTENTIALLY TRASH PATH: ' . $trashfilepath );
			//error_log( 'POTENTIALLY NEW FILE: ' . $new_filename );
			return $new_filename;
		}
		return $filename;
	}

	function array_to_ids_or_urls( $meta, &$ids, &$urls, $recursive = false, $filters = array() ) {
		foreach ( $meta as $k => $m ) {

			if ( is_numeric( $m ) ) {

				if ( !empty( $filters ) && is_array( $filters ) && !in_array( $k, $filters ) ) {
					continue;
				}

				// Probably a Media ID
				if ( $m > 0 )
				{
					array_push( $ids, $m );
				}
			}

			else if ( is_array( $m ) ) {
				// If it's an array with a width, probably that the index is the Media ID
				if ( isset( $m['width'] ) && is_numeric( $k ) ) {

					if ( !empty( $filters ) && is_array( $filters ) && !in_array( $k, $filters ) ) {
						continue;
					}

					if ( $k > 0 )
					{
						array_push( $ids, $k );
					}

					continue;
				}
				
				if ( $recursive ) {
					// If it's an array, we need to go deeper
					$this->array_to_ids_or_urls( $m, $ids, $urls, true, $filters );
				}

			}
			else if ( !empty( $m ) ) {

				if ( !empty( $filters ) && is_array( $filters ) && !in_array( $k, $filters ) ) {
					continue;
				}

				if ( is_string( $m ) && preg_match( '/^[\d\s,]+$/', $m ) && strpos( $m, ',' ) !== false ) {
					// If this is a string that contains only digits, spaces, and commas, and contains at least one comma
					// it is probably a list of IDs. So we should explode it to make an array
					// Remove any spaces

					$m = str_replace( ' ', '', $m );
					$m = explode( ',', $m );

					foreach ( $m as $mv ) {
						if ( is_numeric( $mv ) && !in_array( (int)$mv, $ids ) ) {
							array_push( $ids, (int)$mv );
						}
					}

					continue;
				}

				// If it's a string, maybe it's a file (with an extension)
				if ( preg_match( $this->regex_file, $m ) )
				{
					$clean_url = $this->clean_url( $m );
					array_push( $urls, $clean_url );
				}
			}
		}
	}

	function get_favicon() {
			// Yoast SEO plugin
			$vals = get_option( 'wpseo_titles' );
			if ( !empty( $vals ) && isset( $vals['company_logo'] ) ) {
				$url = $vals['company_logo'];
				if ( $this->is_url( $url ) )
					return $this->clean_url( $url );
			}
		}

	function get_all_shortcodes_attributes( $html, $ids_attr = array(), $urls_attr = array() ) {
		// Get all the shortcodes from html, and check for each attributes of the shortcode if it is an ID or a URL and add the value in an array to return
		$urls_values = array();
		$ids_values = array();

		$pattern = get_shortcode_regex();
		if ( preg_match_all( '/'. $pattern .'/s', $html, $matches ) )
		{
			foreach( $matches[0] as $key => $value) {
				// $matches[3] return the shortcode attribute as string
				// replace space with '&' for parse_str() function
				$get = str_replace(" ", "&" , trim( $matches[3][$key] ) );
				$get = str_replace('"', '' , $get );
				parse_str( $get, $sub_output );

				foreach ( $sub_output as $attr_key => $attr_value ) {

					if ( in_array( $attr_key, $ids_attr ) ) {
						if ( is_numeric( $attr_value ) && !in_array( (int)$attr_value, $ids_values ) ) {
							array_push( $ids_values, (int)$attr_value );
						}

						// In case of separated by commas
						else if ( strpos( $attr_value, ',' ) !== false ) {
							$attr_value = str_replace(' ', '', $attr_value );
							$pieces = explode( ',', $attr_value );
							foreach ( $pieces as $pval ) {
								if ( is_numeric( $pval ) && !in_array( (int)$pval, $ids_values ) ) {
									array_push( $ids_values, (int)$pval );
								}
							}
						}
					}

					else if ( in_array( $attr_key, $urls_attr ) ) {
						if ( !empty( trim( $attr_value ) ) && !in_array( trim( $attr_value ), $urls_values ) && !is_numeric( trim( $attr_value ) ) && strpos( trim( $attr_value ), 'http' ) !== false ) {
							array_push( $urls_values, trim( $this->clean_url( $attr_value ) ) );
						}
					}
				}
			}
		}

		// Remove duplicates
		$urls_values = array_unique( $urls_values );
		$ids_values  = array_unique( $ids_values );

		// Return the values
		$values = array(
			'urls' => $urls_values,
			'ids' => $ids_values
		);

		return $values;

	}



		/**
		 * Recursively transforms a string with WordPress shortcodes into a
		 * hierarchical tree structure (an Abstract Syntax Tree).
		 *
		 * @param string $content The string containing the shortcodes.
		 * @return array An array of nodes, where each node can be a shortcode with its
		 * own 'children' array, or a simple text node.
		 */
		function nested_shortcodes_to_array(string $content): array
		{
			$nodes = [];
			$last_pos = 0;

			$pattern = '/\\[' . '(\\[?)' . '([\w-]+)' . '(?![\\w-])' . '(' . '[^\\]\\/]*' . '(?:' . '\\/(?!\\])' . '[^\\]\\/]*' . ')*?' . ')' . '(?:' . '(\\/)' . '\\]' . '|' . '\\]' . '(?:' . '(' . '[^\\[]*+' . '(?:' . '\\[(?!\\/\\2\\])' . '[^\\[]*+' . ')*+' . ')' . '\\[\\/\\2\\]' . ')?' . ')' . '(\\]?)/s';

			// preg_match_all with PREG_OFFSET_CAPTURE is key to tracking positions.
			if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
				foreach ($matches as $match) {
					// Get the position and content of the full shortcode match
					$match_start_pos = $match[0][1];
					$match_full_string = $match[0][0];
					$match_end_pos = $match_start_pos + strlen($match_full_string);

					// 1. Capture any text that appeared *before* this shortcode
					if ($match_start_pos > $last_pos) {
						$text_content = substr($content, $last_pos, $match_start_pos - $last_pos);
						if (trim($text_content) !== '') {
							$nodes[] = [
								'type' => 'text',
								'content' => $text_content
							];
						}
					}

					// 2. Process the shortcode match itself
					$tag = $match[2][0];
					$attributes_string = $match[3][0];
					// Use isset since self-closing tags won't have inner content (group 5)
					$inner_content = isset($match[5]) ? $match[5][0] : null;

					// Parse attributes from the attribute string
					$parsed_attributes = [];
					if (preg_match_all('/([\w-]+)\s*=\s*(["\'])([^"\']*?)\2/', $attributes_string, $attr_matches)) {
						foreach ($attr_matches[1] as $attr_index => $key) {
							$parsed_attributes[$key] = $attr_matches[3][$attr_index];
						}
					}

					$shortcode_node = [
						'type' => 'shortcode',
						'tag' => $tag,
						'attributes' => $parsed_attributes,
					];

					// 3. This is the recursion!
					// If there is inner content, parse it with the same function.
					if ($inner_content !== null) {
						$children = $this->nested_shortcodes_to_array($inner_content);
						if (!empty($children)) {
							$shortcode_node['children'] = $children;
						}
					}

					$nodes[] = $shortcode_node;

					// Update the last position to the end of the current match
					$last_pos = $match_end_pos;
				}
			}

			// 4. Capture any remaining text after the very last shortcode
			if ($last_pos < strlen($content)) {
				$text_content = substr($content, $last_pos);
				if (trim($text_content) !== '') {
					$nodes[] = [
						'type' => 'text',
						'content' => $text_content
					];
				}
			}

			return $nodes;
		}



	
		function get_shortcode_attributes( $shortcode_tag, $post ) {
		if ( has_shortcode( $post->post_content, $shortcode_tag ) ) {
			$output = array();
			//get shortcode regex pattern wordpress function
			$pattern = get_shortcode_regex( [ $shortcode_tag ] );
			if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches ) )
			{
					$keys = array();
					$output = array();
					foreach( $matches[0] as $key => $value) {
							// $matches[3] return the shortcode attribute as string
							// replace space with '&' for parse_str() function
							$get = str_replace(" ", "&" , trim( $matches[3][$key] ) );
							$get = str_replace('"', '' , $get );
							parse_str( $get, $sub_output );

							//get all shortcode attribute keys
							$keys = array_unique( array_merge(  $keys, array_keys( $sub_output )) );
							$output[] = $sub_output;
					}
					if ( $keys && $output ) {
							// Loop the output array and add the missing shortcode attribute key
							foreach ($output as $key => $value) {
									// Loop the shortcode attribute key
									foreach ($keys as $attr_key) {
											$output[$key][$attr_key] = isset( $output[$key] )  && isset( $output[$key] ) ? $output[$key][$attr_key] : NULL;
									}
									//sort the array key
									ksort( $output[$key]);
							}
					}
			}
			return $output;
		}
		else {
				return false;
		}
	}

	function get_urls_from_html( $html ) {
		if ( empty( $html ) ) {
			return array();
		}


		// Proposal/fix by @copytrans
		// Discussion: https://wordpress.org/support/topic/bug-in-core-php/#post-11647775
		// Modified by Jordy again in 2021 for those who don't have MB enabled
		if ( function_exists( 'mb_encode_numericentity' ) ) {
			$convmap = [0x80, 0xffff, 0, 0xffff];
			$html = mb_encode_numericentity( $html, $convmap, 'UTF-8' );
		} else {
			$html = preg_replace_callback(
				'/[\x80-\xFF]/',
				function( $match ) {
					return '&#' . ord( $match[0] ) . ';';
				},
				$html
			);
		}

		// Resolve src-set and shortcodes
		if ( $this->get_shortcode_analysis() ) {
			$html = do_shortcode( $html );
		}

		// TODO: Since WP 5.5, wp_filter_content_tags should be used instead of wp_make_content_images_responsive.
		$html = function_exists( 'wp_filter_content_tags' ) ? wp_filter_content_tags( $html ) :
			wp_make_content_images_responsive( $html );

		// Create the DOM Document
		if ( !class_exists("DOMDocument") ) {
			error_log( 'Media Cleaner: The DOM extension for PHP is not installed.' );
			throw new Error( 'The DOM extension for PHP is not installed.' );
		}

		
		if ( empty( $html ) ) {
			return array();
		}

		libxml_use_internal_errors(true);
		$dom = new DOMDocument();
		@$dom->loadHTML( $html );
		libxml_clear_errors();
		$results = array();

		// <meta> tags in <head> area
		$metas = $dom->getElementsByTagName( 'meta' );
		foreach ( $metas as $meta ) {
			$property = $meta->getAttribute( 'property' );
			if ( $property == 'og:image' || $property == 'og:image:secure_url' || $property == 'twitter:image' ) {
				$url = $meta->getAttribute( 'content' );
				if ( $this->is_url( $url ) ) {
					$src = $this->clean_url( $url );
					if ( !empty( $src ) ) {
						array_push( $results, $src );
					}
				}
			}
		}

		

		// IFrames (by Mike Meinz)
		$iframes = $dom->getElementsByTagName( 'iframe' );
		foreach( $iframes as $iframe ) {
			$iframe_src = $iframe->getAttribute( 'src' );
			// Ignore if the iframe src is not on this server
			if ( ( strpos( $iframe_src, $this->servername ) !== false) || ( substr( $iframe_src, 0, 1 ) == "/" ) ) {
				// Create a new DOM Document to hold iframe
				$iframe_doc = new DOMDocument();
				// Load the url's contents into the DOM
				libxml_use_internal_errors( true ); // ignore html formatting problems
				$rslt = @$iframe_doc->loadHTMLFile( $iframe_src );
				libxml_clear_errors();
				libxml_use_internal_errors( false );
				if ( $rslt ) {
					// Get the resulting html
					$iframe_html = $iframe_doc->saveHTML();
					if ( $iframe_html !== false ) {
						// Scan for links in the iframe
						$iframe_urls = $this->get_urls_from_html( $iframe_html ); // Recursion
						if ( !empty( $iframe_urls ) ) {	
							$results = array_merge( $results, $iframe_urls );
						}
					}
				}
				else {
					$this->log( '🚫 Failed to load iframe: ' . $iframe_src );
				}
			}
		}


		// Images: src, srcset
		$imgs = $dom->getElementsByTagName( 'img' );
		foreach ( $imgs as $img ) {
			//error_log($img->getAttribute('src'));
			$src = $this->clean_url( $img->getAttribute('src') );
    			array_push( $results, $src );
			$srcset = $img->getAttribute('srcset');
			if ( !empty( $srcset ) ) {
				$setImgs = explode( ',', trim( $srcset ) );
				foreach ( $setImgs as $setImg ) {
					$finalSetImg = explode( ' ', trim( $setImg ) );
					if ( is_array( $finalSetImg ) ) {
						array_push( $results, $this->clean_url( $finalSetImg[0] ) );
					}
				}
			}
		}

		// Videos: src, poster, and attached file
		$videos = $dom->getElementsByTagName( 'video' );
		foreach ($videos as $video) {
			// Get src attribute
			$raw_video_src = $video->getAttribute( 'src' );
			$src = $this->clean_url( $raw_video_src );
			if ( !empty( $src ) ) {
				$video_id = $this->custom_attachment_url_to_postid( $raw_video_src );

				$attached_file = get_post_meta( $video_id, '_wp_attached_file', true );
				if ( !empty( $attached_file ) ) {
					array_push( $results, $attached_file );
				}
			}
			
			// Get poster attribute
			$raw_poster_src = $video->getAttribute( 'poster' );
			$poster = $this->clean_url( $raw_poster_src );
			if ( !empty( $poster ) ) {
				$poster_id = $this->custom_attachment_url_to_postid( $raw_poster_src );
				
				$attached_file = get_post_meta( $poster_id, '_wp_attached_file', true );
				if ( !empty( $attached_file ) ) {
					array_push( $results, $attached_file );
				}
			}

		}

		// Audios: src
		$audios = $dom->getElementsByTagName( 'audio' );
		foreach ( $audios as $audio ) {
			//error_log($audio->getAttribute('src'));
			$src = $this->clean_url( $audio->getAttribute('src') );
    	array_push( $results, $src );
		}

		// Sources: src
		$audios = $dom->getElementsByTagName( 'source' );
		foreach ( $audios as $audio ) {
			//error_log($audio->getAttribute('src'));
			$src = $this->clean_url( $audio->getAttribute('src') );
    	array_push( $results, $src );
		}

		// Links, href
		$urls = $dom->getElementsByTagName( 'a' );
		foreach ( $urls as $url ) {
			$url_href = $url->getAttribute('href'); // mm change
			if ( $this->is_url( $url_href ) ) { // mm change
				$src = $this->clean_url( $url_href );  // mm change
				if ( !empty( $src ) )
					array_push( $results, $src );
			}
		}

		// <link> tags in <head> area
		$urls = $dom->getElementsByTagName( 'link' );
		foreach ( $urls as $url ) {
			$url_href = $url->getAttribute( 'href' );
			if ( $this->is_url( $url_href ) ) {
				$src = $this->clean_url( $url_href );
				if ( !empty( $src ) ) {
					array_push( $results, $src );
				}
			}
		}

		// PDF
		preg_match_all( "/((https?:\/\/)?[^\\&\#\[\] \"\?]+\.pdf)/", $html, $res );
		if ( !empty( $res ) && isset( $res[1] ) && count( $res[1] ) > 0 ) {
			foreach ( $res[1] as $url ) {
				if ( $this->is_url( $url ) )
					array_push( $results, $this->clean_url( $url ) );
			}
		}

		// Background images
		preg_match_all( "/url\(\'?\"?((https?:\/\/)?[^\\&\#\[\] \"\?]+\.(jpe?g|gif|png))\'?\"?/", $html, $res );
		if ( !empty( $res ) && isset( $res[1] ) && count( $res[1] ) > 0 ) {
			foreach ( $res[1] as $url ) {
				if ( $this->is_url( $url ) )
					array_push( $results, $this->clean_url( $url ) );
			}
		}

		return $results;
	}

	/**
	 * 
	 *  Get the IDs and URLs from the blocks of a post.
	 * 
	 * @param string $html The HTML content of the post.
	 * @param string $prefix The prefix of the blocks to look for.
	 * @param array $keys The keys to look for in the blocks.
	 * @param array $urls The array to fill with the URLs.
	 * @param array $ids The array to fill with the IDs.
	 * 
	 */
	function get_from_blocks( $html, $prefix, $keys, &$urls, &$ids ) {

		$blocks = parse_blocks( $html );

		if ( ! is_array( $blocks )  || ! isset( $blocks[0] ) ) {
			return;
		}
		

		foreach ( $blocks as $block ) {

			if ( strpos( $block['blockName'], $prefix ) === false ) {
				continue;
			}

			$this->array_to_ids_or_urls( $block, $ids, $urls, true, $keys );

		}
		
		// $this->get_from_meta(
		// 	$data,
		// 	$keys,
		// 	$ids,
		// 	$urls
		// );

		
		
	}
	// Parse a meta, visit all the arrays, look for the attributes, fill $ids and $urls arrays
	// If rawMode is enabled, it will not check if the value is an ID or an URL, it will just returns it in URLs
	function get_from_meta( $meta, $lookFor, &$ids, &$urls, $rawMode = false ) {
		if ( !is_array( $meta ) && !is_object( $meta) ) {
			return;
		}
		foreach ( $meta as $key => $value ) {
			if ( is_object( $value ) || is_array( $value ) )
				$this->get_from_meta( $value, $lookFor, $ids, $urls, $rawMode );
			else if ( in_array( $key, $lookFor ) ) {
				if ( empty( $value ) ) {
					continue;
				}
				else if ( $rawMode ) {
					array_push( $urls, $value );
				}
				else if ( is_numeric( $value ) ) {
					// It this an ID?
					array_push( $ids, $value );
				}
				else {
					if ( $this->is_url( $value ) ) {
						// Is this an URL?
						array_push( $urls, $this->clean_url( $value ) );
					}
					else {
						// Is this an array of IDs, encoded as a string? (like "20,13")
						$pieces = explode( ',', $value );
						foreach ( $pieces as $pval ) {
							if ( is_numeric( $pval ) ) {
								array_push( $ids, $pval );
							}
						}
					}
				}
			}
		}
	}

	function get_images_from_themes( &$ids, &$urls ) {
		// USE CURRENT THEME AND WP API
		$ch = get_custom_header();
		if ( !empty( $ch ) && !empty( $ch->url ) ) {
			array_push( $urls, $this->clean_url( $ch->url ) );
		}
		if ( $this->is_url( $ch->thumbnail_url ) ) {
			array_push( $urls, $this->clean_url( $ch->thumbnail_url ) );
		}
		if ( !empty( $ch ) && !empty( $ch->attachment_id ) ) {
			array_push( $ids, $ch->attachment_id );
		}
		$cl = get_custom_logo();
		if ( $this->is_url( $cl ) ) {
			$urls = array_merge( $this->get_urls_from_html( $cl ), $urls );
		}
		$custom_logo = get_theme_mod( 'custom_logo' );
		if ( !empty( $custom_logo ) && is_numeric( $custom_logo ) ) {
			array_push( $ids, (int)$custom_logo );
		}
		$si = get_site_icon_url();
		if ( $this->is_url( $si ) ) {
			array_push( $urls, $this->clean_url( $si ) );
		}
		$si_id = get_option( 'site_icon' );
		if ( !empty( $si_id ) && is_numeric( $si_id ) ) {
			array_push( $ids, (int)$si_id );
		}
		$cd = get_background_image();
		if ( $this->is_url( $cd ) ) {
			array_push( $urls, $this->clean_url( $cd ) );
		}
		$photography_hero_image = get_theme_mod( 'photography_hero_image' );
		if ( !empty( $photography_hero_image ) ) {
			array_push( $ids, $photography_hero_image );
		}
		$author_profile_picture = get_theme_mod( 'author_profile_picture' );
		if ( !empty( $author_profile_picture ) ) {
			array_push( $ids, $author_profile_picture );
		}
		if ( function_exists ( 'get_uploaded_header_images' ) ) {
			$header_images = get_uploaded_header_images();
			if ( !empty( $header_images ) ) {
				foreach ( $header_images as $hi ) {
					if ( !empty ( $hi['attachment_id'] ) ) {
						array_push( $ids, $hi['attachment_id'] );
					}
				}
			}
		}
	}

	#region LOGS

	function log( $data = null, $force = false ) {
		if ( !$this->debug_logs && !$force )
			return;

		$php_logs = $this->get_option( 'php_error_logs' );
		$log_file_path = $this->get_logs_path();

		$fh = @fopen( $log_file_path, 'a' );
		if ( !$fh ) { return false; }
		$date = date( "Y-m-d H:i:s" );
		if ( is_null( $data ) ) {
			fwrite( $fh, "\n" );
		}
		else {
			fwrite( $fh, "$date: {$data}\n" );
			if ( $php_logs ) {
				error_log( "[MEDIA CLEANER] " . $data );
			}
		}
		fclose( $fh );
		return true;
	}

	//WPMC_PREFIX

	function get_logs_path() {
		$uploads_dir = wp_upload_dir();
		$uploads_dir_path = trailingslashit( $uploads_dir['basedir'] );

		$path = $this->get_option( 'logs_path' );

		if ( $path && file_exists( $path ) ) {
			// make sure the path is legal (within the uploads directory with the WPMC_PREFIX prefix and log extension)
			if ( strpos( $path, $uploads_dir_path ) !== 0 || strpos( $path, WPMC_PREFIX ) === false || substr( $path, -4 ) !== '.log' ) {
				$path = null;
			} else {
				return $path;
			}
		}

		if ( !$path ) {
			$path = $uploads_dir_path . WPMC_PREFIX . "_" . $this->random_ascii_chars() . ".log";
			if ( !file_exists( $path ) ) {
				touch( $path );
			}
			
			$options = $this->get_all_options();
			$options['logs_path'] = $path;
			$this->update_options( $options );
		}

		return $path;
	}
	

	function get_logs() {
		$log_file_path = $this->get_logs_path();

		if ( !file_exists( $log_file_path ) ) {
			return "No logs found.";
		}

		$content = file_get_contents( $log_file_path );
		$lines = explode( "\n", $content );
		$lines = array_filter( $lines );
		$lines = array_reverse( $lines );
		$content = implode( "\n", $lines );
		return $content;
	}

	function clear_logs() {
		$logPath = $this->get_logs_path();
		if ( file_exists( $logPath ) ) {
			unlink( $logPath );
		}

		$options = $this->get_all_options();
		$options['logs_path'] = null;
		$this->update_options( $options );
	}

	#endregion

	/**
	 *
	 * HELPERS
	 *
	 */

	private function random_ascii_chars($length = 8)
	{
		$characters = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
		$characters_length = count($characters);
		$random_string = '';

		for ($i = 0; $i < $length; $i++) {
			$random_string .= $characters[rand(0, $characters_length - 1)];
		}

		return $random_string;
	}

	function get_trashdir() {
		return trailingslashit( $this->upload_path ) . 'wpmc-trash';
	}

	function get_trashurl() {
		return trailingslashit( $this->upload_url ) . 'wpmc-trash';
	}

	function clean_ob(){
		$disabled = $this->get_option( 'output_buffer_cleaning_disabled' );
		$ob_content = ob_get_contents();
		if ( !empty( trim( $ob_content ) ) ) {

			if ( $disabled ) {
				$this->log( "🚨 If the server's response was broken, try to let Output Buffer Cleaning enabled." );
				return;
			}

			$this->log( "🧹 The response is broken due to output buffering, it will be cleaned." );
			$this->log( "📄 Output buffer content: " . $ob_content );

			ob_end_clean();
		}
	}

	/**
	 *
	 * I18N RELATED HELPERS
	 *
	 */

	function is_multilingual() {
		return function_exists( 'icl_get_languages' );
	}

	function get_languages() {
		$results = array();
		if ( $this->is_multilingual() ) {
			$languages = icl_get_languages();
			foreach ( $languages as $language ) {
				if ( isset( $language['code'] ) ) {
					array_push( $results, $language['code'] );
				}
				else if ( isset( $language['language_code'] ) ) {
					array_push( $results, $language['language_code'] );
				}
			}
		}
		return $results;
	}

	function get_translated_media_ids( $mediaId ) {
		$translated_ids = array();
		foreach ( $this->languages as $language ) {
			$id = apply_filters( 'wpml_object_id', $mediaId, 'attachment', false, $language );
			if ( !empty( $id ) ) {
				array_push( $translated_ids, $id );
			}
		}
		return $translated_ids;
	}

	/**
	 *
	 * DELETE / SCANNING / RESET
	 *
	 */

	function recover_file( $path ) {
		$originalPath = trailingslashit( $this->upload_path ) . $path;
		$trashPath = trailingslashit( $this->get_trashdir() ) . $path;
		if ( !file_exists( $trashPath ) ) {
			$this->log( "🚫 The file $originalPath actually does not exist in the trash." );
			return true;
		}
		$path_parts = pathinfo( $originalPath );
		if ( !file_exists( $path_parts['dirname'] ) && !wp_mkdir_p( $path_parts['dirname'] ) ) {
			die( 'Failed to create folder.' );
		}
		if ( !rename( $trashPath, $originalPath ) ) {
			die( 'Failed to move the file.' );
		}
		return true;
	}

	function recover( $id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_scan";
		$issue = $this->get_issue( $id );

		if ( empty( $issue ) ) {
			$this->log( "🚫 Issue #{$id} does not exist. Cannot recover this." );
			return false;
		}

		// Files
		if ( $issue->type === 0 ) {
			$this->recover_file( $issue->path );
			$wpdb->query( $wpdb->prepare( "UPDATE $table_name SET deleted = 0 WHERE id = %d", $id ) );
			$this->log( "✅ Recovered {$issue->path}." );
			return true;
		}
		// Media
		else if ( $issue->type === 1 ) {

			// If there is no file attached, doesn't handle the files
			$fullpath = get_attached_file( $issue->postId );
			if ( empty( $fullpath ) ) {
				$this->log( "🚫 Media #{$issue->postId} does not have attached file anymore." );
				error_log( "Media #{$issue->postId} does not have attached file anymore." );
				return false;
			}

			$paths = $this->get_paths_from_attachment( $issue->postId );
			foreach ( $paths as $path ) {
				if ( !$this->recover_file( $path ) ) {
					$this->log( "🚫 Could not recover $path." );
					error_log( "Media Cleaner: Could not recover $path." );
				}
			}
			if ( !wp_update_post( array( 'ID' => $issue->postId, 'post_type' => 'attachment' ) ) ) {
				$this->log( "🚫 Failed to Untrash Post {$issue->postId} (but deleted it from Cleaner DB)." );
				error_log( "Media Cleaner: Failed to Untrash Post {$issue->postId} (but deleted it from Cleaner DB)." );
				return false;
			}
			$wpdb->query( $wpdb->prepare( "UPDATE $table_name SET deleted = 0 WHERE id = %d", $id ) );
			$this->log( "✅ Recovered Media #{$issue->postId}." );
			return true;
		}
	}

	function trash_file( $fileIssuePath ) {
		$originalPath = trailingslashit( $this->upload_path ) . $fileIssuePath;
		$trashPath = trailingslashit( $this->get_trashdir() ) . $fileIssuePath;
		$path_parts = pathinfo( $trashPath );

		try {
			if ( !file_exists( $path_parts['dirname'] ) && !wp_mkdir_p( $path_parts['dirname'] ) ) {
				$this->log( "🚫 Could not create the trash directory for Media Cleaner." );
				error_log( "Media Cleaner: Could not create the trash directory." );
				return false;
			}
			// Rename the file (move). 'is_dir' is just there for security (no way we should move a whole directory)
			if ( is_dir( $originalPath ) ) {
				$this->log( "🚫 Attempted to delete a directory instead of a file ($originalPath). Can't do that." );
				error_log( "Media Cleaner: Attempted to delete a directory instead of a file ($originalPath). Can't do that." );
				return false;
			}
			if ( !file_exists( $originalPath ) ) {
				$this->log( "🚫 The file $originalPath actually does not exist." );
				error_log( "Media Cleaner: The file $originalPath actually does not exist." );
				return true;
			}
			if ( !@rename( $originalPath, $trashPath ) ) {
				error_log( "Media Cleaner: Unknown error occured while trying to delete a file ($originalPath)." );
				return false;
			}
		}
		catch ( Exception $e ) {
			return false;
		}
		$this->clean_dir( dirname( $originalPath ) );
		return true;
	}

	function repair( $id ) {
		$repair = $this->get_repair( $id );
		if ( empty( $repair ) ) {
			$this->log( "🚫 Repair #{$id} does not exist. Cannot repair this." );
			return false;
		}
		foreach ( $repair->child_ids as $child_id ) {
			if ( !$this->delete( $child_id ) ) {
				$this->log( "🚫 Failed to repair the file." );
				return false;
			}
		}
		$full_path = $this->get_full_upload_path( $repair->path );
		$filetype = wp_check_filetype( basename( $full_path ), null );
		$wp_upload_dir = wp_upload_dir();
		$attachment = array(
			'guid'           => $wp_upload_dir['url'] . '/' . basename( $full_path ), 
			'post_mime_type' => $filetype['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $full_path ) ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);

		$attach_id = wp_insert_attachment( $attachment, $full_path );

		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		$attach_data = wp_generate_attachment_metadata( $attach_id, $full_path );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_scan";
		$wpdb->query( $wpdb->prepare( "DELETE FROM $table_name WHERE id = %d OR parentId = %d", $id, $id ) );
		$this->log( "✅ Repaired {$repair->path}." );
		return true;
	}

	function ignore( $id, $ignore ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_scan";
		$issue = $this->get_issue( $id );

		if ( empty( $issue ) ) {
			$this->log( "🚫 Issue #{$id} does not exist. Cannot ignore this." );
			return false;
		}

		if ( !$ignore ) {
			$wpdb->query( $wpdb->prepare( "UPDATE $table_name SET ignored = 0 WHERE id = %d", $id ) );
		}
		else {
			// If it is in trash, recover it
			if ( $issue->deleted ) {
				$this->recover( $id );
			}
			$wpdb->query( $wpdb->prepare( "UPDATE $table_name SET ignored = 1 WHERE id = %d", $id ) );
		}
		return true;
	}

	function endsWith( $haystack, $needle )
	{
		$length = strlen( $needle );
		if ( $length == 0 )
			return true;
		return ( substr( $haystack, -$length ) === $needle );
	}

	function clean_dir( $dir ) {
		if ( !file_exists( $dir ) )
			return;
		else if ( $this->endsWith( $dir, 'uploads' ) )
			return;
		$found = array_diff( scandir( $dir ), array( '.', '..' ) );
		if ( count( $found ) < 1 ) {
			if ( rmdir( $dir ) ) {
				$this->clean_dir( dirname( $dir ) );
			}
		}
	}

	function get_issue( $id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_scan";
		$issue = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $id ), OBJECT );
		if ( empty( $issue ) ) {
			return false;
		}
		$issue->id = (int)$issue->id;
		$issue->postId = (int)$issue->postId;
		$issue->type = (int)$issue->type;
		$issue->deleted = (int)$issue->deleted;
		$issue->ignored = (int)$issue->ignored;
		$issue->path = stripslashes( $issue->path );
		return $issue;
	}

	function get_repair( $id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_scan";
		$repair = $wpdb->get_row( $wpdb->prepare( "SELECT
				main.id AS id,
				main.path AS path,
				GROUP_CONCAT(child.id) AS child_ids
			FROM
				$table_name AS main
			LEFT JOIN
				$table_name AS child ON main.id = child.parentId
				WHERE main.id = %d", $id
			), OBJECT );
		if ( empty( $repair ) ) {
			return false;
		}

		// If $repair->path is null or empty return false
		if ( empty( $repair->path ) ) {
			$this->log( "🚫 Repair #{$id} does not have a path. Cannot repair this." );
			return false;
		}


		$repair->id = (int)$repair->id;
		$regex = "^(.*)(\\s\\(\\+.*)$";
		$repair->path = preg_replace( '/' . $regex . '/i', '$1', stripslashes( $repair->path ) );
		$repair->child_ids = $repair->child_ids ? explode( ',', $repair->child_ids ) : [];
		return $repair;
	}

	function get_issues_to_repair( $order_by = 'id', $order = 'asc', $search = '', $skip = 0, $limit = 10 ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_scan";

		$search_clause = '';
		if ( !empty( $search ) ) {
			$search_clause = $wpdb->prepare("AND main.path LIKE %s", ( '%' . $search . '%' ));
		}

		$order_clause = 'ORDER BY main.id ASC';
		if ( $order_by === 'path' ) {
			$order_clause = 'ORDER BY main.path ' . ( $order === 'asc' ? 'ASC' : 'DESC' );
		}
		else if ( $order_by === 'issue' ) {
			$order_clause = 'ORDER BY main.issue ' . ( $order === 'asc' ? 'ASC' : 'DESC' );
		}
		else if ( $order_by === 'size' ) {
			$order_clause = 'ORDER BY main.size ' . ( $order === 'asc' ? 'ASC' : 'DESC' );
		}

		$result = $wpdb->get_results( $wpdb->prepare( "SELECT
				main.id AS id,
				main.path AS path,
				GROUP_CONCAT(child.id) AS child_ids,
				GROUP_CONCAT(child.path) AS child_paths,
				main.type AS type,
				main.postId AS postId,
				main.size AS size,
				main.ignored AS ignored,
				main.deleted AS deleted,
				main.issue AS issue
			FROM
				$table_name AS main
			LEFT JOIN
				$table_name AS child ON main.id = child.parentId
			WHERE
				main.path IS NOT NULL AND main.parentId IS NULL
				AND main.deleted = 0 AND main.ignored = 0
				AND main.type = 0
				$search_clause
			GROUP BY main.id
			$order_clause
			LIMIT %d, %d;
		", $skip, $limit ) );

		return $result;
	}

	function get_repair_ids ( $search = '' ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_scan";

		$search_clause = '';
		if ( !empty( $search ) ) {
			$search_clause = $wpdb->prepare("AND main.path LIKE %s", ( '%' . $search . '%' ));
		}

		return $wpdb->get_col( "SELECT DISTINCT main.id
			FROM
				$table_name AS main
				LEFT JOIN $table_name AS child ON main.id = child.parentId
			WHERE
				main.path IS NOT NULL
				AND main.parentId IS NULL
				$search_clause
			GROUP BY
				main.id
			;"
		);
	}

	function get_stats_of_issues_to_repair( $search = '' ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_scan";

		$search_clause = '';
		if ( !empty( $search ) ) {
			$search_clause = $wpdb->prepare("AND main.path LIKE %s", ( '%' . $search . '%' ));
		}

		return $wpdb->get_row( "SELECT
			COUNT(id) AS entries,
			SUM(size) AS size
			FROM (
				SELECT
					COUNT(DISTINCT main.id) as id,
					main.size as size
				FROM
					$table_name AS main
				LEFT JOIN
					$table_name AS child ON main.id = child.parentId
				WHERE
					main.path IS NOT NULL AND main.parentId IS NULL AND main.deleted = 0 AND main.ignored = 0
					$search_clause
				GROUP BY main.id
			) t;
		" );
	}

	function get_count_of_issues_to_repair( $search ) {
		$stats = $this->get_stats_of_issues_to_repair( $search );
		return $stats->entries;
	}

	function delete( $id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_scan";
		$issue = $this->get_issue( $id );

		if ( empty( $issue ) ) {
			$this->log( "🚫 Issue #{$id} does not exist. Cannot delete this." );
			return false;
		}

		$regex = "^(.*)(\\s\\(\\+.*)$";
		$issue->path = preg_replace( '/' . $regex . '/i', '$1', $issue->path ); // remove " (+ 6 files)" from path
		$skip_trash = $this->get_option( 'skip_trash' );

		if ( $issue->type === 0 ) {

			// Delete file from the trash
			if ( $issue->deleted === 1 ) {
				$trashPath = trailingslashit( $this->get_trashdir() ) . $issue->path;
				if ( unlink( $trashPath ) ) {
					$wpdb->query( $wpdb->prepare( "DELETE FROM $table_name WHERE id = %d", $id ) );
					$this->clean_dir( dirname( $trashPath ) );
					return true;
				}
			}
			// Delete file without using trash
			else if ( $skip_trash ) {
				$originalPath = trailingslashit( $this->upload_path ) . $issue->path;
				if ( unlink( $originalPath ) ) {
					$wpdb->query( $wpdb->prepare( "DELETE FROM $table_name WHERE id = %d", $id ) );
					$this->clean_dir( dirname( $originalPath ) );
					return true;
				}
			}
			// Move file to the trash
			else  if ( $this->trash_file( $issue->path ) ) {
				$wpdb->query( $wpdb->prepare( "UPDATE $table_name SET deleted = 1, ignored = 0 WHERE id = %d", $id ) );
				return true;
			}

			$this->log( "🚫 Failed to delete/trash the file." );
			error_log( "Media Cleaner: Failed to delete/trash the file." );
		}

		if ( $issue->type === 1 ) {

			// Trash Media definitely by recovering it (to be like a normal Media) and remove it through the
			// standard WordPress workflow
			if ( $issue->deleted === 1 || $skip_trash  ) {
				if ( $issue->deleted === 1 ) {
					$this->recover( $id );
				}
				wp_update_post( array( 'ID' => $issue->postId, 'post_type' => 'attachment' ) );
				wp_delete_attachment( $issue->postId, true );
				$wpdb->query( $wpdb->prepare( "DELETE FROM $table_name WHERE id = %d", $id ) );
				return true;
			}
			else {
				// Move Media to trash
				// Let's copy the images to the trash so that it can be recovered.
				$paths = $this->get_paths_from_attachment( $issue->postId );
				foreach ( $paths as $path ) {
					if ( !$this->trash_file( $path ) ) {
						$this->log( "🚫 Could not trash $path." );
						error_log( "Media Cleaner: Could not trash $path." );
						return false;
					}
				}
				wp_update_post( array( 'ID' => $issue->postId, 'post_type' => 'wmpc-trash' ) );
				$wpdb->query( $wpdb->prepare( "UPDATE $table_name SET deleted = 1, ignored = 0 WHERE id = %d", $id ) );
				return true;
			}
		}
		return false;
	}

	function delete_directory_recurcively( $dir ) {
		if ( !is_dir( $dir ) ) {
			return;
		}
		$files = array_diff( scandir( $dir ), array( '.', '..' ) );
		foreach ( $files as $file ) {
			if ( is_dir( "$dir/$file" ) ) {
				$this->delete_directory_recurcively( "$dir/$file" );
			}
			else {
				unlink( "$dir/$file" );
			}
		}
		rmdir( $dir );
	}

	function force_trash() {

		$res = [
			'message' => 'The trash folder has been emptied.',
			'success' => true
		];

		// Delete all the files in the trash folder.
		$trashDirPath = trailingslashit( $this->get_trashdir() );
		if ( file_exists( $trashDirPath ) && is_dir( $trashDirPath ) ) {
			$this->delete_directory_recurcively( $trashDirPath, true );
		}
	
		// Clean the Database: DELETE FROM wp_mclean_scan WHERE deleted = 1
		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_scan";
		$wpdb->query( $wpdb->prepare( "DELETE FROM $table_name WHERE deleted = 1" ) );
		

		return $res;
	}

	/**
	 *
	 * SCANNING / RESET
	 *
	 */

	function add_reference_url( $urlOrUrls, $type, $origin = null, $extra = null ) {
		$urlOrUrls = !is_array( $urlOrUrls ) ? array( $urlOrUrls ) : $urlOrUrls;
		foreach ( $urlOrUrls as $url ) {
			// With files, we need both filename without resolution and filename with resolution, it's important
			// to make sure the original file is not deleted if a size exists for it.
			// With media, all URLs should be without resolution to make sure it matches Media.
			if ( $this->current_method == 'files' ) {
				$this->add_reference( null, $url, $type, $origin );
				$this->add_reference( 0, $this->clean_url_from_resolution( $url ), $type, $origin );
			}
			else {
				// 2021/11/08: I added this, the problem is that sometimes users create image filenames with the resolution
				// in it, even though it is the original.
				$this->add_reference( null, $url, $type, $origin );

				$this->add_reference( 0, $this->clean_url_from_resolution( $url ), $type, $origin );
			}
		}
	}

	function add_reference_id( $idOrIds, $type, $origin = null, $extra = null ) {
		$idOrIds = !is_array( $idOrIds ) ? array( $idOrIds ) : $idOrIds;
		foreach ( $idOrIds as $id ) {
			$this->add_reference( $id, "", $type, $origin );
			if ( $this->multilingual ) {
				$translatedIds = $this->get_translated_media_ids( (int)$id );
				
				// Test for WPML
				// if ( $id ===  '350') {
				// 	$translatedIds = $this->get_translated_media_ids( (int)$id );
				// 	$count = count($translatedIds);
				// 	error_log( "${id} => ${count}" );
				// }

				if ( !empty( $translatedIds ) ) {
					foreach ( $translatedIds as $translatedId ) {
						$this->add_reference( $translatedId, "", $type, $origin );
					}
				}
			}
		}
	}


	// Returns the reference with the type, origin, related to a Media ID it is referenced
	public function get_reference_for_media_id( $id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_refs";
		$refs = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE mediaId = %d", $id ), OBJECT );
		if ( empty( $refs ) ) {
			return false;
		}
		$ref = $refs[0];
		$ref->id = (int)$ref->id;
		$ref->mediaId = (int)$ref->mediaId;
		$ref->originType = (int)$ref->originType;
		$ref->origin = stripslashes( $ref->origin );
		$ref->parentId = empty( $ref->parentId ) ? null : (int)$ref->parentId;
		return $ref;
	}

	// Return the references related to a Post ID
	public function get_references_for_post_id( $id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_refs";
		$refs = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE originType LIKE %s", "%[$id]" ), OBJECT );
		if ( empty( $refs ) ) {
			return [];
		}
		$fresh_refs = array();
		foreach ( $refs as $ref ) {
			$mediaId = (int)$ref->mediaId > 0 ? (int)$ref->mediaId : null;
			if ( !$mediaId && !empty( $ref->mediaUrl ) ) {
				$mediaId = $this->find_media_id_from_file( $ref->mediaUrl, false );
				$mediaId = !empty( $mediaId ) ? (int)$mediaId : null;
			}
			if ( !$mediaId ) {
				continue;
			}
			array_push( $fresh_refs, [
				'id' => (int)$ref->id,
				'mediaId' => $mediaId,
				'mediaUrl' => $ref->mediaUrl,
				'originType' => $ref->originType,
				'parentId' => empty( $ref->parentId ) ? null : (int)$ref->parentId,
			] );
		}
		return $fresh_refs;
	}

	// The references are actually not being added directly in the DB, they are being pushed
	// into a cache ($this->refcache).
	private function add_reference( $id, $url, $type, $origin = null, $extra = null ) {

		if ( !empty( $origin ) ) {
			$type = $type . " [$origin]";
		}

		if ( !empty( $id ) ) {

			if( $this->use_cached_references ) {

				$added = $this->add_cached_id( $id );
				if ( $added ) {
					array_push( $this->refcache, array( 'id' => $id, 'url' => null, 'type' => $type, 'origin' => $origin ) );
				}
				
				
			}

			if( !$this->use_cached_references ) {
				array_push( $this->refcache, array( 'id' => $id, 'url' => null, 'type' => $type, 'origin' => $origin ) );
			}
			
		}
		if ( !empty( $url ) ) {
			// The URL shouldn't contain http, https, javascript at the beginning (and there are probably many more cases)
			// The URL must be cleaned before being passed as a reference.
			if ( substr( $url, 0, 5 ) === "http:" || substr( $url, 0, 6 ) === "https:" || substr( $url, 0, 11 ) === "javascript:" ) {
				return;
			}

			if( $this->use_cached_references ) {

				$added = $this->add_cached_url( $url );
				if ( $added ) {
					array_push( $this->refcache, array( 'id' => null, 'url' => $url, 'type' => $type, 'origin' => $origin ) );
				}

			}

			if( !$this->use_cached_references ) {
				array_push( $this->refcache, array( 'id' => null, 'url' => $url, 'type' => $type, 'origin' => $origin ) );
			}

		}

	}

	//* Let's only use transient to avoid PHP memory issues. Commented out the CLI version.
	private function get_cached_ids() {
		//if( !$this->is_cli ) {
			$cached_ids = get_transient($this->cached_ids_key);
			return $cached_ids !== false ? $cached_ids : array();
		//}

		
		// if( $this->is_cli ) {
		// 	return $this->cached_ids_cli;
		// }
		
	}

	private function get_cached_urls() {
		//if( !$this->is_cli ) {
			$cached_urls = get_transient($this->cached_urls_key);
			return $cached_urls !== false ? $cached_urls : array();
		//}

		// if( $this->is_cli ) {
		// 	return $this->cached_urls_cli;
		// }
	}

	private function add_cached_id($id) {
		$cached_ids = $this->get_cached_ids();
		if ( !in_array( $id, $cached_ids ) ) {
			$cached_ids[] = $id;

			// if( $this->is_cli ) {
			// 	$this->cached_ids_cli[] = $id;
			// }

			//if( !$this->is_cli ) {
				set_transient( $this->cached_ids_key, $cached_ids, 0 );
			//}

			return true;
			
		}

		return false;
	}

	private function add_cached_url($url) {
		$cached_urls = $this->get_cached_urls();
		if ( !in_array( $url, $cached_urls ) ) {
			$cached_urls[] = $url;

			// if( $this->is_cli ) {
			// 	$this->cached_urls_cli[] = $url;
			// }
			//if ( !$this->is_cli ) {
				set_transient($this->cached_urls_key, $cached_urls, 0);
			//}
			
			return true;
		}

		return false;
	}

	function reset_cached_references() {
		delete_transient( $this->cached_ids_key );
		delete_transient( $this->cached_urls_key );

		$this->cached_ids_cli = array();
		$this->cached_urls_cli = array();
	}

	function insert_references($entries)
	{
		global $wpdb;
		$table = $wpdb->prefix . "mclean_refs";
		$values = array();
		$place_holders = array();
		$query = "INSERT INTO $table (mediaId, mediaUrl, originType, parentId) VALUES ";

		foreach ( $entries as $value ) {
			if ( !is_null($value['id'] ) ) {
				// Media Reference
				array_push( $values, $value['id'], $value['type'] );
				$place_holders[] = "('%d', NULL, '%s', NULL)";

				if ($this->debug_logs) {
					$this->log("＋ Media #{$value['id']} (as ID)");
				}
			}
			else if ( !is_null($value['url'] ) ) {
				// File Reference
				array_push( $values, $value['url'], $value['type'] );
				if ( isset( $value['parentId'] ) ) {
					array_push( $values, $value['parentId'] );
					$place_holders[] = "(NULL, '%s', '%s', '%d')";
					if ( $this->debug_logs ) {
						$this->log( "＋ {$value['url']} (as URL) (ParentID: {$value['parentId']})" );
					}
				} else {
					$place_holders[] = "(NULL, '%s', '%s', NULL)";
					if ( $this->debug_logs ) {
						$this->log("＋ {$value['url']} (as URL)");
					}
				}
			}
		}

		if ( !empty( $values ) ) {
			$query .= implode( ', ', $place_holders );
			$prepared = $wpdb->prepare( "$query ", $values );
			$wpdb->query( $prepared );
		}
	}

	function reset_progress() {
		// Reset the progress by deleting the transient.
		delete_transient( $this->progress_key );
	}

	function clear_step_progress() {
		// Clear step progress when scanning completes
		delete_transient( $this->progress_key );
	}

	function save_progress( $step, $data = array() ) {
		// Save progress with step and optional data
		// Data can include type, limit, limitSize, and any other progress information
		$progress = array(
			'step' => $step,
			'time' => time(),
			'data' => $data
		);

		set_transient( $this->progress_key, $progress, 0 );
	}

	function get_progress() {
		return get_transient( $this->progress_key );
	}

	function get_step_progress() {
		$options = $this->get_all_options();
		return isset( $options['step_progress'] ) ? $options['step_progress'] : null;
	}

	// The cache containing the references is wrote to the DB.
	function write_references() {
		global $wpdb;
		$table = $wpdb->prefix . "mclean_refs";

		$potential_parents = array();
		$potential_children = array();

		foreach ( $this->refcache as $value ) {
			$potentialParentPath = !is_null( $value['url'] ) ? $this->clean_url_from_resolution( $value['url'] ) : null;
			if ( $potentialParentPath === $value['url'] ) {
				$potential_parents[] = $value;
			}
			else {
				$potential_children[] = $value;
			}
		}

		$this->insert_references( $potential_parents );

		// Resolve parentId for potential children
		foreach ( $potential_children as &$child ) {
			$potentialParentPath = $this->clean_url_from_resolution( $child['url'] );
			$parentId = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $table WHERE mediaUrl = %s", $potentialParentPath ) );
			if ( !empty( $parentId ) ) {
				$child['parentId'] = (int)$parentId;
			}
		}

		// Insert potential children with resolved parentIds
		$this->insert_references( $potential_children );
		$this->refcache = array();
	}

	function check_is_ignore( $file ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_scan";
		$count = $wpdb->get_var( "SELECT COUNT(*)
			FROM $table_name
			WHERE ignored = 1
			AND path LIKE '%".  esc_sql( $wpdb->esc_like( $file ) ) . "%'" );
		if ( $count > 0 ) {
			$this->log( "🚫 Could not trash $file." );
		}
		return ($count > 0);
	}

	function find_media_id_from_file( $file, $doLog ) {
		global $wpdb;
		$postmeta_table_name = $wpdb->prefix . 'postmeta';
		$file = $this->clean_uploaded_filename( $file );
		$sql = $wpdb->prepare( "SELECT post_id
			FROM {$postmeta_table_name}
			WHERE meta_key = '_wp_attached_file'
			AND meta_value = %s", $file
		);
		$ret = $wpdb->get_var( $sql );
		if ( $doLog ) {
			if ( empty( $ret ) )
				$this->log( "🚫 File $file not found as _wp_attached_file (Library)." );
			else {
				$this->log( "✅ File $file found as Media $ret." );
			}
		}

		return $ret;
	}

	function get_thumbnails_urls( $id, $sizes_as_key = false ) {
		$sizes = get_intermediate_image_sizes();
		// For each size use wp_get_attachment_image_src() to get the URL
		$urls = array();
		foreach ( $sizes as $size ) {
			$src = wp_get_attachment_image_src( $id, $size );
			if ( $src ) {
				$urls[$size] = $this->clean_url( $src[0] );
			}
		}

		return $sizes_as_key ? $urls : array_values( $urls );
	}


	function get_thumbnails_urls_from_srcset( $id, $size = 'full'  ) {

		$image_size = $this->get_attachment_size_by_id( $id, $size );

		$sizes = array_keys( $this->get_image_sizes() );
		$sizes[] = $image_size;

		$urls = array();
		foreach ( $sizes as $image_size ) {
			$srcset     = wp_get_attachment_image_srcset( $id, $image_size );

			// Extract URLs from srcset
			if ( !empty( $srcset ) ) {
				$srcset = explode( ', ', $srcset );
				foreach ( $srcset as $src ) {
					$parts = explode( ' ', $src );
					$url = trim( $parts[0] );
					if ( !empty( $url ) ) {
						$urls[] = $this->clean_url( $url );
					}
				}
			}
		}
		
		return $urls;

	}

	function get_attachment_size_by_id( $attachment_id, $default_size = 'full' ) {

		if ( ! $attachment_id ) {
			return $default_size;
		}

		$url = wp_get_attachment_url( $attachment_id );
		if ( ! $url ) {
			return $default_size;
		}

		$metadata = wp_get_attachment_metadata( $attachment_id );

		if ( ! is_array( $metadata ) ) {
			return $default_size;
		}

		$size = $default_size;

		if ( isset( $metadata['file'] ) && strpos( $url, $metadata['file'] ) === ( strlen( $url ) - strlen( $metadata['file'] ) ) ) {
			$size = array( $metadata['width'], $metadata['height'] );
		} elseif ( preg_match( '/-(\d+)x(\d+)\.(jpg|jpeg|gif|png|svg|webp)$/', $url, $match ) ) {
			// Get the image width and height.
			// Example: https://regex101.com/r/7JwGz7/1.
			$size = array( $match[1], $match[2] );
		}

		return $size;
	}

	function get_image_sizes() {
		$sizes = array();
		global $_wp_additional_image_sizes;
		foreach ( get_intermediate_image_sizes() as $s ) {
			$crop = false;
			if ( isset( $_wp_additional_image_sizes[$s] ) ) {
				$width = intval( $_wp_additional_image_sizes[$s]['width'] );
				$height = intval( $_wp_additional_image_sizes[$s]['height'] );
				$crop = $_wp_additional_image_sizes[$s]['crop'];
			} else {
				$width = get_option( $s.'_size_w' );
				$height = get_option( $s.'_size_h' );
				$crop = get_option( $s.'_crop' );
			}
			$sizes[$s] = array( 'width' => $width, 'height' => $height, 'crop' => $crop );
		}
		return $sizes;
	}

	function clean_url_from_resolution( $url ) {
		if ( !isset( $url ) ) return $url;

		$pattern = '/[_-]\d+x\d+(?=\.[a-z]{3,4}$)/';
		$url = preg_replace( $pattern, '', $url );
		return $url;
	}

	function is_url( $url ) {
		return ( (
			!empty( $url ) ) &&
			is_string( $url ) &&
			strlen( $url ) > 4 && (
				strtolower( substr( $url, 0, 4) ) == 'http' || $url[0] == '/'
			)
		);
	}

	function clean_url_from_resolution_ref( &$url ) {
		$url = $this->clean_url_from_resolution( $url );
	}

	// From a url to the shortened and cleaned url (for example '2013/02/file.png')
	function clean_url( $url ) {
		// if ( is_array( $url ) ) {
		// 	error_log( print_r( $url, 1 ) );
		// }
		$dirIndex = strpos( $url, $this->upload_url );
		if ( empty( $url ) || $dirIndex === false ) {
			$finalUrl =  null;
		}
		else {
			$finalUrl = urldecode( substr( $url, 1 + strlen( $this->upload_url ) + $dirIndex ) );
		}
		return $finalUrl;
	}

	function custom_attachment_url_to_postid( $url ) {
		global $wpdb;
		
		// Remove the query string
		$url = preg_replace('/\?.*/', '', $url);
		
		// Try to find the attachment ID by matching the URL with the guid
		$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid LIKE %s AND post_type = 'attachment';", '%' . $wpdb->esc_like( $url ) ) );
		
		// If found, return the first attachment ID
		if ( !empty( $attachment ) ) {
			return ( int )$attachment[0];
		}
		
		// If not found, try to match the URL without the upload directory path
		$upload_dir = wp_upload_dir();
		$url_relative = str_replace( $upload_dir['baseurl'] . '/', '', $url );
		
		$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wp_attached_file' AND meta_value LIKE %s;", '%' . $wpdb->esc_like( $url_relative ) ) );
		
		// If found, return the first attachment ID
		if ( !empty( $attachment ) ) {
			return ( int )$attachment[0];
		}
		
		// If still not found, return 0
		return 0;
	}

	// From a fullpath to the shortened and cleaned path (for example '2013/02/file.png')
	// Original version by Jordy
	// function clean_uploaded_filename( $fullpath ) {
	// 	$basedir = $this->upload_path;
	// 	$file = str_replace( $basedir, '', $fullpath );
	// 	$file = str_replace( "./", "", $file );
	// 	$file = trim( $file,  "/" );
	// 	return $file;
	// }

	// From a fullpath to the shortened and cleaned path (for example '2013/02/file.png')
	// Faster version, more difficult to read, by Mike Meinz
	function clean_uploaded_filename( $fullpath ) {
		$dirIndex = strpos( $fullpath, $this->upload_url );
		if ( $dirIndex == false ) {
			$file = $fullpath;
		}
		else {
		// Remove first part of the path leaving yyyy/mm/filename.ext
			$file = substr( $fullpath, 1 + strlen( $this->upload_url ) + $dirIndex );
		}
		if ( substr( $file, 0, 2 ) == './' ) {
			$file = substr( $file, 2 );
		}
		if ( substr( $file, 0, 1 ) == '/' ) {
			$file = substr( $file, 1 );
		}
		return $file;
	}

	/**
	 * Check if the file or the Media ID is used in the install.
	 * That file or ID will be checked against the database of references created by the plugin
	 * by the parsers.
	 */
	function reference_exists( $file, $mediaId ) {
		global $wpdb;

		$table = $wpdb->prefix . "mclean_refs";
		$this->create_mediaId_index( $table );

		$row = null;
		if ( !empty( $mediaId ) ) {
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT originType FROM $table WHERE mediaId = %d", $mediaId ) );
			if ( !empty( $row ) ) {
				$origin = $row->originType === 'MEDIA LIBRARY' ? 'Media Library' : 'content';
				$this->log( "✅ Media #{$mediaId} used by {$origin}" );
				return $row->originType;
			}
		}
		if ( !empty( $file ) ) {
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT originType FROM $table WHERE mediaUrl = %s", $file ) );
			if ( !empty( $row ) ) {
				$origin = $row->originType === 'MEDIA LIBRARY' ? 'Media Library' : 'content';
				$this->log( "✅ File {$file} used by {$origin}" );
				return $row->originType;
			}
		}
		return false;
	}

	function create_mediaId_index( $table ) {
		if ( $this->ref_index_exists ) return;

		global $wpdb;
		// If the index already exists, return
		$index = $wpdb->get_results( "SHOW INDEX FROM {$wpdb->prefix}mclean_refs WHERE Key_name = 'mediaId_index'" );
		if ( !empty( $index ) ) {
			$this->ref_index_exists = true;
			return;
		}

		$wpdb->query("CREATE INDEX mediaId_index ON $table (mediaId)");
	}

	function get_full_upload_path( $relative_path ) {
		$wp_upload_dir = wp_upload_dir();
		$full_path = trailingslashit( $wp_upload_dir['basedir'] ) . $relative_path;
		return $full_path;
	}

	function get_paths_from_attachment( $attachmentId ) {
		$paths = array();
		$fullpath = get_attached_file( $attachmentId );
		if ( empty( $fullpath ) ) {
			$this->log( 'Could not find attached file for Media ID ' . $attachmentId );
			return array();
		}
		$mainfile = $this->clean_uploaded_filename( $fullpath );
		array_push( $paths, $mainfile );
		$baseUp = pathinfo( $mainfile );
		$filespath = trailingslashit( $this->upload_path ) . trailingslashit( $baseUp['dirname'] );
		$meta = wp_get_attachment_metadata( $attachmentId );
		if ( isset( $meta['original_image'] ) ) {
			$original_image = $this->clean_uploaded_filename( $filespath . $meta['original_image'] );
			array_push( $paths, $original_image );
		}
		$isImage = isset( $meta, $meta['width'], $meta['height'] );
		$sizes = $this->get_image_sizes();
		if ( $isImage && isset( $meta['sizes'] ) ) {
			foreach ( $meta['sizes'] as $name => $attr ) {
				if  ( isset( $attr['file'] ) ) {
					$file = $this->clean_uploaded_filename( $filespath . $attr['file'] );
					array_push( $paths, $file );
				}
			}
		}
		return $paths;
	}

	function is_media_ignored( $attachmentId ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_scan";
		$issue = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE postId = %d", $attachmentId ), OBJECT );
		//error_log( $attachmentId );
		//error_log( print_r( $issue, 1 ) );
		if ( $issue && $issue->ignored )
			return true;
		return false;
	}

	function check_media( $attachmentId, $checkOnly = false ) {

		// Is Media ID ignored, consider as used.
		if ( $this->is_media_ignored( $attachmentId ) ) {
			return true;
		}

		// Remove everything related to this media from the database.
		if ( !$checkOnly ) {
			$this->delete_attachment_related_data( $attachmentId );
		}

		$size = 0;
		$countfiles = 0;
		$check_broken_media = !$this->check_content;
		$fullpath = get_attached_file( $attachmentId );
		$is_broken = apply_filters( 'wpmc_is_file_broken', !file_exists( $fullpath ), $attachmentId );

		// It's a broken-only scan
		if ( $check_broken_media && !$is_broken ) {
			$is_considered_used = apply_filters( 'wpmc_check_media', true, $attachmentId, false );
			return $is_considered_used;
		}

		// Let's analyze the usage of each path (thumbnails included) for this Media ID.
		$issue = 'NO_CONTENT';
		$paths = $this->get_paths_from_attachment( $attachmentId );
		foreach ( $paths as $path ) {
			
			// If it's found in the content, we stop the scan right away
			if ( $this->check_content && $this->reference_exists( $path, $attachmentId ) ) {
				$is_considered_used = apply_filters( 'wpmc_check_media', true, $attachmentId, false );
				if ( $is_considered_used ) {
					return true;
				}
			}

			// Let's count the size of the files for later, in case it's unused
			$filepath = trailingslashit( $this->upload_path ) . $path;
			if ( file_exists( $filepath ) )
				$size += filesize( $filepath );
			$countfiles++;
		}
		
		// This Media ID seems not in used (or broken)
		// Let's double-check through the filter (overridable by users)
		$is_considered_used = apply_filters( 'wpmc_check_media', false, $attachmentId, $is_broken );
		if ( !$is_considered_used ) {
			if ( $is_broken ) {
				$this->log( "🚫 File {$fullpath} does not exist." );
				$issue = 'ORPHAN_MEDIA';
			}
			if ( !$checkOnly ) {
				global $wpdb;
				$table_name = $wpdb->prefix . "mclean_scan";
				$mainfile = $this->clean_uploaded_filename( $fullpath );
				$wpdb->insert( $table_name,
					array(
						'time' => current_time('mysql'),
						'type' => 1,
						'size' => $size,
						'path' => $mainfile . ( $countfiles > 0 ? ( " (+ " . $countfiles . " files)" ) : "" ),
						'postId' => $attachmentId,
						'issue' => $issue
						)
					);
			}
		}
		return $is_considered_used;
	}

	// Delete all issues
	function reset_issues( $includingIgnored = false ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_scan";
		if ( $includingIgnored ) {
			$wpdb->query( "DELETE FROM $table_name WHERE deleted = 0" );
		}
		else {
			$wpdb->query( "DELETE FROM $table_name WHERE ignored = 0 AND deleted = 0" );
		}
		if ( file_exists( WPMC_PATH . '/logs/media-cleaner.log' ) ) {
			file_put_contents( WPMC_PATH . '/logs/media-cleaner.log', '' );
		}
	}

	function is_image_extension( $ext ) {
		$ext = strtolower( $ext );
		$valid = apply_filters( 'wpmc_valid_image_extensions', array( 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'ico', 'webp', 'avif' ) );

		return in_array( $ext, $valid );

	}
		

	function reset_references() {
		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_refs";
		$wpdb->query("TRUNCATE $table_name");
		$this->reset_cached_references();
	}

	function get_issue_for_postId( $postId ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "mclean_scan";
		$issue = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE postId = %d", $postId ), OBJECT );
		return $issue;
	}

	function echo_issue( $issue ) {
		if ( $issue == 'NO_CONTENT' ) {
			_e( "Not found in content", 'media-cleaner' );
		}
		else if ( $issue == 'ORPHAN_FILE' ) {
			_e( "Not in Library", 'media-cleaner' );
		}
		else if ( $issue == 'ORPHAN_RETINA' ) {
			_e( "Orphan Retina", 'media-cleaner' );
		}
		else if ( $issue == 'ORPHAN_WEBP' ) {
			_e( "Orphan WebP", 'media-cleaner' );
		}
		else if ( $issue == 'ORPHAN_MEDIA' ) {
			_e( "No attached file", 'media-cleaner' );
		}
		else {
			echo $issue;
		}
	}

	function get_uploads_directory_hierarchy() {
		$uploads_dir = wp_upload_dir();
		$base_dir = wp_normalize_path( $uploads_dir['basedir'] );
		$root = '/' . wp_basename( $base_dir );
		$directories = array();
	
		// Get all subdirectories of the base directory
		$dir_iterator = new RecursiveDirectoryIterator( $base_dir, FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::SKIP_DOTS );
		$iterator = new RecursiveIteratorIterator( $dir_iterator, RecursiveIteratorIterator::SELF_FIRST );
	
		foreach ( $iterator as $file ) {
			if ( $file->isDir() ) {
				// Normalize path for consistency
				$file_path = wp_normalize_path( $file->getPathname() );
				// Remove base_dir from path
				$directory = str_replace( $base_dir, '', $file_path );
				if ( $directory ) {
					$directories[] = $root . $directory;
				}
			}
		}
	
		// Return the hierarchy as a JSON file
		return json_encode( $directories );
	}

	/**
	 *
	 * Roles & Access Rights
	 *
	 */
	public function can_access_settings() {
		return apply_filters( 'wpmc_allow_setup', current_user_can( 'manage_options' ) );
	}

	public function can_access_features() {
		return apply_filters( 'wpmc_allow_usage', current_user_can( 'administrator' ) );
	}

	#region Options

	function list_options() {
		return array(
			'method' => 'media',
			'content' => true,
			'filesystem_content' => false,
			'media_library' => true,
			'live_content' => false,
			'debuglogs' => false,
			'images_only' => false,
			'attach_is_use' => false,
			'thumbnails_only' => false,
			'dirs_filter' => '',
			'files_filter' => '',
			'hide_thumbnails' => false,
			'hide_warning' => false,
			'skip_trash' => false,
			'medias_buffer' => 100,
			'posts_buffer' => 5,
			'analysis_buffer' => 100,
			'file_op_buffer' => 20,
			'delay' => 100,
			'shortcodes_disabled' => false,
			'use_cached_references' => true,
			'output_buffer_cleaning_disabled' => false,
			'php_error_logs' => false,
			'posts_per_page' => 10,
			'clean_uninstall' => false,
			'repair_mode' => false,
			'expert_mode' => false,
			'logs_path' => null,
		);
	}

	function reset_options() {
		delete_option( $this->option_name );
	}

	function get_option( $option ) {
		$options = $this->get_all_options();
		return $options[$option];
	}

	function get_all_options() {
		$options = get_option( $this->option_name, null );
		$options = $this->check_options( $options );
		return $options;
	}

	// Let's work on this function if we need it.
	// Right now, it looks like the options are all updated at the same time.

	// function update_option( $option, $value ) {
	// 	if ( !array_key_exists( $name, $options ) ) {
	// 		return new WP_REST_Response([ 'success' => false, 'message' => 'This option does not exist.' ], 200 );
	// 	}
	//  $value = is_bool( $params['value'] ) ? ( $params['value'] ? '1' : '' ) : $params['value'];
	// }

	function update_options( $options ) {
		if ( !update_option( $this->option_name, $options, false ) ) {
			return false;
		}
		$options = $this->sanitize_options();
		return $options;
	}

	// Upgrade from the old way of storing options to the new way.
	function check_options( $options = [] ) {
		$plugin_options = $this->list_options();
		$options = empty( $options ) ? [] : $options;
		$hasChanges = false;
		foreach ( $plugin_options as $option => $default ) {
			// The option already exists
			if ( isset( $options[$option] ) ) {
				continue;
			}
			// The option does not exist, so we need to add it.
			// Let's use the old value if any, or the default value.
			$options[$option] = get_option( 'wpmc_' . $option, $default );
			delete_option( 'wpmc_' . $option );
			$hasChanges = true;
		}
		if ( $hasChanges ) {
			update_option( $this->option_name , $options );
		}

		// Dynamically added options
		//TODO: we should have a rest route to fetch this instead of using the options directly. This is temporary.
		$options['scan_progress'] = get_transient( $this->progress_key );

		return $options;
	}

	// Validate and keep the options clean and logical.
	function sanitize_options() {
		$options = $this->get_all_options();
		$medias = $options['medias_buffer'];
		$posts = $options['posts_buffer'];
		$analysis = $options['analysis_buffer'];
		$fileOp = $options['file_op_buffer'];
		$delay = $options['delay'];
		$hasChanges = false;
		if ( $medias === '' ) {
			$options['medias_buffer'] = 100;
			$hasChanges = true;
		}
		if ( $posts === '' ) {
			$options['posts_buffer'] = 5;
			$hasChanges = true;
		}
		if ( $analysis === '' ) {
			$options['analysis_buffer'] = 100;
			$hasChanges = true;
		}
		if ( $fileOp === '' ) {
			$options['file_op_buffer'] = 20;
			$hasChanges = true;
		}
		if ( $delay === '' ) {
			$options['delay'] = 100;
			$hasChanges = true;
		}
		if ( $hasChanges ) {
			update_option( $this->option_name, $options, false );
		}
		return $options;
	}

	#endregion
}

// Check the DB. If does not exist, let's create it.
// TODO: When PHP 7 only, let's clean this and use anonymous functions.
function wpmc_check_database() {
	global $wpdb;
	static $wpmc_check_database_done = false;
	if ( $wpmc_check_database_done ) {
		return true;
	}
	$table_refs = $wpdb->prefix . "mclean_refs";
	$table_scan = $wpdb->prefix . "mclean_scan";
	$db_init = !( strtolower( $wpdb->get_var( "SHOW TABLES LIKE '$table_refs'" ) ) != strtolower( $table_refs )
		|| strtolower( $wpdb->get_var( "SHOW TABLES LIKE '$table_scan'" ) ) != strtolower( $table_scan ) );
	if ( !$db_init ) {
		wpmc_create_database();
		$db_init = !( strtolower( $wpdb->get_var( "SHOW TABLES LIKE '$table_refs'" ) ) != strtolower( $table_refs )
			|| strtolower( $wpdb->get_var( "SHOW TABLES LIKE '$table_scan'" ) ) != strtolower( $table_scan ) );
	}

	// Check if parentId column exists in the table
	// TODO: Delete this after June 2024
	$parentIdExists = $wpdb->get_var( "SHOW COLUMNS FROM $table_refs LIKE 'parentId'" );
	if ( !$parentIdExists ) {
		$wpdb->query( "ALTER TABLE $table_refs ADD parentId BIGINT(20) NULL;" );
		$wpdb->query( "ALTER TABLE $table_scan ADD parentId BIGINT(20) NULL;" );
	}

	$wpmc_check_database_done = true;
}

function wpmc_create_database() {
	global $wpdb;
	$table_name = $wpdb->prefix . "mclean_scan";
	$charset_collate = $wpdb->get_charset_collate();
	$sql = "CREATE TABLE $table_name (
		id BIGINT(20) NOT NULL AUTO_INCREMENT,
		time DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL,
		type TINYINT(1) NOT NULL,
		postId BIGINT(20) NULL,
		path TINYTEXT NULL,
		size INT(9) NULL,
		ignored TINYINT(1) NOT NULL DEFAULT 0,
		deleted TINYINT(1) NOT NULL DEFAULT 0,
		issue TINYTEXT NOT NULL,
		parentId BIGINT(20) NULL,
		PRIMARY KEY  (id)
	) " . $charset_collate . ";" ;
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	$sql="ALTER TABLE $table_name ADD INDEX IgnoredIndex (ignored) USING BTREE;";
	$wpdb->query($sql);
	$table_name = $wpdb->prefix . "mclean_refs";
	$charset_collate = $wpdb->get_charset_collate();
	// This key doesn't work on too many installs because of the 'Specified key was too long' issue
	// KEY mediaLookUp (mediaId, mediaUrl)
	$sql = "CREATE TABLE $table_name (
		id BIGINT(20) NOT NULL AUTO_INCREMENT,
		mediaId BIGINT(20) NULL,
		mediaUrl TINYTEXT NULL,
		originType TINYTEXT NOT NULL,
		parentId BIGINT(20) NULL,
		PRIMARY KEY  (id)
	) " . $charset_collate . ";";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

function wpmc_remove_database() {
	global $wpdb;
	$table_name1 = $wpdb->prefix . "mclean_scan";
	$table_name2 = $wpdb->prefix . "mclean_refs";
	$table_name3 = $wpdb->prefix . "wpmcleaner";
	$sql = "DROP TABLE IF EXISTS $table_name1, $table_name2, $table_name3;";
	$wpdb->query( $sql );
}

#region Install / Uninstall

/*
	INSTALL / UNINSTALL
*/

function wpmc_init( $mainfile ) {
	//register_activation_hook( $mainfile, 'wpmc_install' );
	//register_deactivation_hook( $mainfile, 'wpmc_uninstall' );
	register_uninstall_hook( $mainfile, 'wpmc_uninstall' );
}

function wpmc_install() {
	wpmc_create_database();
}

function wpmc_reset () {
	wpmc_remove_database();
	wpmc_create_database();
}

function wpmc_remove_options() {
	global $wpdb;
	$options = $wpdb->get_results( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'wpmc_%'" );
	foreach( $options as $option ) {
		delete_option( $option->option_name );
	}
}

function wpmc_uninstall () {
	$options = get_option( 'wpmc_options', [] );
	$cleanUninstall = $options['clean_uninstall'];
	if ($cleanUninstall) {
		wpmc_remove_options();
		wpmc_remove_database();
	}
}

#endregion