<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * The companion file to shortcodes.php
 *
 * This file contains the code that converts HTML embeds into shortcodes
 * for when the user copy/pastes in HTML.
 *
 * @package automattic/jetpack
 */

add_filter( 'pre_kses', array( 'Filter_Embedded_HTML_Objects', 'filter' ), 11 );
add_filter( 'pre_kses', array( 'Filter_Embedded_HTML_Objects', 'maybe_create_links' ), 100 ); // See WPCom_Embed_Stats::init().

/**
 * Helper class for identifying and parsing known HTML embeds (iframe, object, embed, etc. elements), then converting them to shortcodes.
 * For unknown HTML embeds, the class still tries to convert them to plain links so that at least something is preserved instead of having the entire element stripped by KSES.
 *
 * @since 4.5.0
 */
class Filter_Embedded_HTML_Objects {
	/**
	 * Array of patterns to search for via strpos().
	 * Keys are patterns, values are callback functions that implement the HTML -> shortcode replacement.
	 * Patterns are matched against URLs (src or movie HTML attributes).
	 *
	 * @var array
	 */
	public static $strpos_filters = array();
	/**
	 * Array of patterns to search for via preg_match().
	 * Keys are patterns, values are callback functions that implement the HTML -> shortcode replacement.
	 * Patterns are matched against URLs (src or movie HTML attributes).
	 *
	 * @var array
	 */
	public static $regexp_filters = array();
	/**
	 * HTML element being processed.
	 *
	 * @var string
	 */
	public static $current_element = false;
	/**
	 * Array of patterns to search for via strpos().
	 * Keys are patterns, values are callback functions that implement the HTML -> shortcode replacement.
	 * Patterns are matched against full HTML elements.
	 *
	 * @var array
	 */
	public static $html_strpos_filters = array();
	/**
	 * Array of patterns to search for via preg_match().
	 * Keys are patterns, values are callback functions that implement the HTML -> shortcode replacement.
	 * Patterns are matched against full HTML elements.
	 *
	 * @var array
	 */
	public static $html_regexp_filters = array();
	/**
	 * Failed embeds (stripped)
	 *
	 * @var array
	 */
	public static $failed_embeds = array();

	/**
	 * Store tokens found in Syntax Highlighter.
	 *
	 * @since 4.5.0
	 *
	 * @var array
	 */
	private static $sh_unfiltered_content_tokens;

	/**
	 * Capture tokens found in Syntax Highlighter and collect them in self::$sh_unfiltered_content_tokens.
	 *
	 * @since 4.5.0
	 *
	 * @param array $match Array of Syntax Highlighter matches.
	 *
	 * @return string
	 */
	public static function sh_regexp_callback( $match ) {
		$token                                        = sprintf(
			'[prekses-filter-token-%1$d-%2$s-%1$d]',
			wp_rand(),
			md5( $match[0] )
		);
		self::$sh_unfiltered_content_tokens[ $token ] = $match[0];
		return $token;
	}

	/**
	 * Look for HTML elements that match the registered patterns.
	 * Replace them with the HTML generated by the registered replacement callbacks.
	 *
	 * @param string $html Post content.
	 */
	public static function filter( $html ) {
		if ( ! $html || ! is_string( $html ) ) {
			return $html;
		}

		$regexps = array(
			'object' => '%<object[^>]*+>(?>[^<]*+(?><(?!/object>)[^<]*+)*)</object>%i',
			'embed'  => '%<embed[^>]*+>(?:\s*</embed>)?%i',
			'iframe' => '%<iframe[^>]*+>(?>[^<]*+(?><(?!/iframe>)[^<]*+)*)</iframe>%i',
			'div'    => '%<div[^>]*+>(?>[^<]*+(?><(?!/div>)[^<]*+)*+)(?:</div>)+%i',
			'script' => '%<script[^>]*+>(?>[^<]*+(?><(?!/script>)[^<]*+)*)</script>%i',
		);

		$unfiltered_content_tokens          = array();
		self::$sh_unfiltered_content_tokens = array();

		// Check here to make sure that SyntaxHighlighter is still used. (Just a little future proofing).
		if ( class_exists( 'SyntaxHighlighter' ) ) {
			/*
			 * Replace any "code" shortcode blocks with a token that we'll later replace with its original text.
			 * This will keep the contents of the shortcode from being filtered.
			 */
			global $SyntaxHighlighter; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

			// Check to see if the $syntax_highlighter object has been created and is ready for use.
			if ( isset( $SyntaxHighlighter ) && is_array( $SyntaxHighlighter->shortcodes ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
				$shortcode_regex           = implode( '|', array_map( 'preg_quote', $SyntaxHighlighter->shortcodes ) ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
				$html                      = preg_replace_callback(
					'/\[(' . $shortcode_regex . ')(\s[^\]]*)?\][\s\S]*?\[\/\1\]/m',
					array( __CLASS__, 'sh_regexp_callback' ),
					$html
				);
				$unfiltered_content_tokens = self::$sh_unfiltered_content_tokens;
			}
		}

		foreach ( $regexps as $element => $regexp ) {
			self::$current_element = $element;

			if ( false !== stripos( $html, "<$element" ) ) {
				$new_html = preg_replace_callback( $regexp, array( __CLASS__, 'dispatch' ), $html );
				if ( $new_html ) {
					$html = $new_html;
				}
			}

			if ( false !== stripos( $html, "&lt;$element" ) ) {
				$regexp_entities = self::regexp_entities( $regexp );
				$new_html        = preg_replace_callback( $regexp_entities, array( __CLASS__, 'dispatch_entities' ), $html );
				if ( $new_html ) {
					$html = $new_html;
				}
			}
		}

		if ( $unfiltered_content_tokens !== array() ) {
			// Replace any tokens generated earlier with their original unfiltered text.
			$html = str_replace( array_keys( $unfiltered_content_tokens ), $unfiltered_content_tokens, $html );
		}

		return $html;
	}

	/**
	 * Replace HTML entities in current HTML element regexp.
	 * This is useful when the content is HTML encoded by TinyMCE.
	 *
	 * @param string $regexp Selected regexp.
	 */
	public static function regexp_entities( $regexp ) {
		return preg_replace(
			'/\[\^&([^\]]+)\]\*\+/',
			'(?>[^&]*+(?>&(?!\1)[^&])*+)*+',
			str_replace( '?&gt;', '?' . '>', htmlspecialchars( $regexp, ENT_NOQUOTES ) )
		);
	}

	/**
	 * Register a filter to convert a matching HTML element to a shortcode.
	 *
	 * We can match the provided pattern against the source URL of the HTML element
	 * (generally the value of the src attribute of the HTML element), or against the full HTML element.
	 *
	 * The callback is passed an array containing the raw HTML of the element as well as pre-parsed attribute name/values.
	 *
	 * @param string $match          Pattern to search for: either a regular expression to use with preg_match() or a search string to use with strpos().
	 * @param string $callback       Function used to convert embed into shortcode.
	 * @param bool   $is_regexp      Is $match a regular expression? If true, match using preg_match(). If not, match using strpos(). Default false.
	 * @param bool   $is_html_filter Match the pattern against the full HTML (true) or just the source URL (false)? Default false.
	 */
	public static function register( $match, $callback, $is_regexp = false, $is_html_filter = false ) {
		if ( $is_html_filter ) {
			if ( $is_regexp ) {
				self::$html_regexp_filters[ $match ] = $callback;
			} else {
				self::$html_strpos_filters[ $match ] = $callback;
			}
		} elseif ( $is_regexp ) {
			self::$regexp_filters[ $match ] = $callback;
		} else {
			self::$strpos_filters[ $match ] = $callback;
		}
	}

	/**
	 * Delete an existing registered pattern/replacement filter.
	 *
	 * @param string $match Embed regexp.
	 */
	public static function unregister( $match ) {
		// Allow themes/plugins to remove registered embeds.
		unset( self::$regexp_filters[ $match ] );
		unset( self::$strpos_filters[ $match ] );
		unset( self::$html_regexp_filters[ $match ] );
		unset( self::$html_strpos_filters[ $match ] );
	}

	/**
	 * Filter and replace HTML element entity.
	 *
	 * @param array $matches Array of matches.
	 */
	private static function dispatch_entities( $matches ) {
		$orig_html       = $matches[0];
		$decoded_matches = array( html_entity_decode( $matches[0], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 ) );

		return self::dispatch( $decoded_matches, $orig_html );
	}

	/**
	 * Filter and replace HTML element.
	 *
	 * @param array  $matches Array of matches.
	 * @param string $orig_html Original html. Returned if no results are found via $matches processing.
	 */
	private static function dispatch( $matches, $orig_html = null ) {
		if ( null === $orig_html ) {
			$orig_html = $matches[0];
		}
		$html  = preg_replace( '%&#0*58;//%', '://', $matches[0] );
		$attrs = self::get_attrs( $html );
		if ( isset( $attrs['src'] ) ) {
			$src = $attrs['src'];
		} elseif ( isset( $attrs['movie'] ) ) {
			$src = $attrs['movie'];
		} else {
			// no src found, search html.
			foreach ( self::$html_strpos_filters as $match => $callback ) {
				if ( str_contains( $html, $match ) ) {
					return call_user_func( $callback, $attrs );
				}
			}

			foreach ( self::$html_regexp_filters as $match => $callback ) {
				if ( preg_match( $match, $html ) ) {
					return call_user_func( $callback, $attrs );
				}
			}

			return $orig_html;
		}

		$src = trim( $src );

		// check source filter.
		foreach ( self::$strpos_filters as $match => $callback ) {
			if ( str_contains( $src, $match ) ) {
				return call_user_func( $callback, $attrs );
			}
		}

		foreach ( self::$regexp_filters as $match => $callback ) {
			if ( preg_match( $match, $src ) ) {
				return call_user_func( $callback, $attrs );
			}
		}

		// check html filters.
		foreach ( self::$html_strpos_filters as $match => $callback ) {
			if ( str_contains( $html, $match ) ) {
				return call_user_func( $callback, $attrs );
			}
		}

		foreach ( self::$html_regexp_filters as $match => $callback ) {
			if ( preg_match( $match, $html ) ) {
				return call_user_func( $callback, $attrs );
			}
		}

		// Log the strip.
		if ( function_exists( 'wp_kses_reject' ) ) {
			wp_kses_reject(
				sprintf(
					/* translators: placeholder is an HTML tag. */
					__( '<code>%s</code> HTML tag removed as it is not allowed', 'jetpack' ),
					'&lt;' . self::$current_element . '&gt;'
				),
				array( self::$current_element => $attrs )
			);
		}

		// Keep the failed match so we can later replace it with a link,
		// but return the original content to give others a chance too.
		self::$failed_embeds[] = array(
			'match' => $orig_html,
			'src'   => esc_url( $src ),
		);

		return $orig_html;
	}

	/**
	 * Failed embeds are stripped, so let's convert them to links at least.
	 *
	 * @param string $string Failed embed string.
	 *
	 * @return string $string Linkified string.
	 */
	public static function maybe_create_links( $string ) {
		if ( empty( self::$failed_embeds ) ) {
			return $string;
		}

		foreach ( self::$failed_embeds as $entry ) {
			$html = sprintf( '<a href="%s">%s</a>', esc_url( $entry['src'] ), esc_url( $entry['src'] ) );
			// Check if the string doesn't contain iframe, before replace.
			if ( ! str_contains( $string, '<iframe ' ) ) {
				$string = str_replace( $entry['match'], $html, $string );
			}
		}

		self::$failed_embeds = array();

		return $string;
	}

	/**
	 * Parse post HTML for HTML tags.
	 *
	 * @param string $html Post HTML.
	 */
	public static function get_attrs( $html ) {
		if (
			! ( class_exists( 'DOMDocument' ) && function_exists( 'simplexml_load_string' ) ) ) {
			trigger_error( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
				esc_html__( 'PHP’s XML extension is not available. Please contact your hosting provider to enable PHP’s XML extension.', 'jetpack' )
			);
			return array();
		}
		// We have to go through DOM, since it can load non-well-formed XML (i.e. HTML).  SimpleXML cannot.
		$dom = new DOMDocument();
		// The @ is not enough to suppress errors when dealing with libxml,
		// we have to tell it directly how we want to handle errors.
		libxml_use_internal_errors( true );
		// Suppress parser warnings.
		@$dom->loadHTML( $html ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		libxml_use_internal_errors( false );
		$xml = false;
		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		foreach ( $dom->childNodes as $node ) {
			// find the root node (html).
			if ( XML_ELEMENT_NODE === $node->nodeType ) {
				/*
				 * Use simplexml_load_string rather than simplexml_import_dom
				 * as the later doesn't cope well if the XML is malformmed in the DOM
				 * See #1688-wpcom.
				 */
				libxml_use_internal_errors( true );
				// html->body->object.
				$xml = simplexml_load_string( $dom->saveXML( $node->firstChild->firstChild ) );
				libxml_clear_errors();
				break;
			}
		}
		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

		if ( ! $xml ) {
			return array();
		}

		$attrs              = array();
		$attrs['_raw_html'] = $html;

		// <param> elements
		foreach ( $xml->param as $param ) {
			$attrs[ (string) $param['name'] ] = (string) $param['value'];
		}

		// <object> attributes
		foreach ( $xml->attributes() as $name => $attr ) {
			$attrs[ $name ] = (string) $attr;
		}

		// <embed> attributes
		if ( $xml->embed ) {
			foreach ( $xml->embed->attributes() as $name => $attr ) {
				$attrs[ $name ] = (string) $attr;
			}
		}

		return $attrs;
	}
}
