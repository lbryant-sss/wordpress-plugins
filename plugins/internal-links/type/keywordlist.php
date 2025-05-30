<?php
namespace ILJ\Type;

use ILJ\Database\Postmeta;
use ILJ\Helper\Encoding;
use ILJ\Helper\IndexAsset as IndexAsset;
use ILJ\Backend\Editor;

/**
 * Keywordlist Datatype
 *
 * A container for keywords with a comprehensive toolset for handling keyword data
 *
 * @package ILJ\Type
 * @since   1.0.0
 */
class KeywordList {

	/**
	 * Keyword List
	 *
	 * @var   array $keywords
	 * @since 1.0.0
	 */
	private $keywords = array();

	/**
	 * Constructor of the keywordlist datatype
	 *
	 * @since  1.0.0
	 * @param  array $keyword_list Initial list of keywords
	 * @return void
	 */
	public function __construct($keyword_list = array()) {
		if (!is_array($keyword_list) || !count($keyword_list)) {
			return;
		}

		foreach ($keyword_list as $keyword) {
			$this->addKeyword($keyword);
		}
	}

	/**
	 * Returns the number of keywords in the list
	 *
	 * @since  1.2.0
	 * @return int
	 */
	public function getCount() {
		return count($this->keywords);
	}

	/**
	 * Merges another KeywordList into current
	 *
	 * @since 1.2.0
	 * @param KeywordList $keyword_list The KeywordList to be merged
	 *
	 * @return void
	 */
	public function merge(KeywordList $keyword_list) {
		foreach ($keyword_list->getKeywords() as $keyword) {
			$this->addKeyword($keyword);
		}
	}

	/**
	 * Converts an user input of comma separated values with placeholders to keywordlist datatype
	 *
	 * @since  1.0.0
	 * @param  string $input The input string for object generation
	 * @return KeywordList
	 */
	public static function fromInput($input) {
		$keyword_list = new self(self::decoded($input));
		$keyword_list->clean();
		return $keyword_list;
	}

	/**
	 * Returns a empty KeywordList Object
	 *
	 * @return KeywordList
	 */
	public static function empty() {
		return new self();
	}

	/**
	 * Generates a KeywordList object from asset meta data
	 *
	 * @since 1.2.0
	 * @param int    $id       The assets ID
	 * @param string $type     The assets type (post, ...)
	 * @param string $meta_key The assets meta key to fetch
	 *
	 * @return KeywordList
	 */
	public static function fromMeta($id, $type, $meta_key = '') {
		 $meta_keywords = array();

		if ('' === $meta_key) {
			$meta_key = Postmeta::ILJ_META_KEY_LINKDEFINITION;
		}

		switch ($type) {
			case 'post':
			case 'custom':
			$meta_keywords = get_post_meta($id, $meta_key, true);
				break;
			case 'term':
			$meta_keywords = get_term_meta($id, $meta_key, true);
				break;
			default:
				break;
		}

		{
      if (Editor::ILJ_META_KEY_BLACKLISTDEFINITION == $meta_key && is_array($meta_keywords)) {
   				$meta_keywords = array_slice($meta_keywords, 0, 2);
   			}
  }

		if (!is_array($meta_keywords)) {
			$meta_keywords = array();
		}

		return new KeywordList($meta_keywords);
	}

	/**
	 * Adds a keyword to the internal list
	 *
	 * @since  1.0.0
	 * @param  string $keyword The keyword that should get added
	 * @return void
	 */
	public function addKeyword($keyword) {
		$this->keywords[] = $keyword;
		$this->clean();
	}

	/**
	 * Add multiple keywords.
	 *
	 * @since 2.23.5
	 * @param array<string> $keywords The list of keywords to be added.
	 * @return void
	 */
	public function add_keywords($keywords) {
		$this->keywords = array_merge($this->keywords, $keywords);
		$this->clean();
	}

	/**
	 * Returns the keyword list
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function getKeywords() {
		 return $this->keywords;
	}

	/**
	 * Encodes an array of keywords to a string format
	 *
	 * @since  1.0.0
	 * @param  bool $escape Whether the output keyword should be escaped or not
	 * @return string
	 */
	public function encoded($escape = true) {
		 $keywords = array();
		foreach ($this->keywords as $keyword) {
			$unmasked_keyword   = Encoding::unmaskSlashes($keyword);
			$translated_keyword = Encoding::translateRegexToPseudo($unmasked_keyword);
			$translated_keyword = str_replace('\\', '', $translated_keyword);
			$keywords[]         = $escape ? esc_attr($translated_keyword) : $translated_keyword;
		}
		return implode(',', $keywords);
	}

	/**
	 * Encodes a comma separated list of keywords to an array
	 *
	 * @since  1.0.0
	 * @param  string $csv_list A comma separated list of keywords
	 * @return array
	 */
	private static function decoded($csv_list) {
		$csv_list_clear      = preg_replace('/\s*,\s*/', ',', $csv_list);
		$keyword_list_pseudo = explode(',', $csv_list_clear);
		$keyword_list_regex  = array();
		foreach ($keyword_list_pseudo as $keyword) {
			$keyword              = Encoding::escape_ascii($keyword);
			$keyword              = Encoding::translatePseudoToRegex($keyword);
			$keyword              = Encoding::maskSlashes($keyword);
			$keyword_list_regex[] = $keyword;
		}
		return $keyword_list_regex;
	}

	/**
	 * Cleans the keyword list from empty strings and makes all keywords unique
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function clean() {
		$unspace_keyword_list    = preg_replace('/\s{2,}/', ' ', $this->keywords);
		$unique_keyword_list     = array_unique($unspace_keyword_list);
		$no_empties_keyword_list = array_filter(
			$unique_keyword_list,
			function ($keyword) {
				return (preg_match('/^\s+$/', $keyword) || '' == $keyword) ? false : $keyword;
			}
		);
		$this->keywords          = array_values($no_empties_keyword_list);
	}

	/**
	 * Checks if another keywordlist instance has additional keywords, the current instance doesn't
	 *
	 * @since  1.2.10
	 * @param  KeywordList $compare_keyword_list The keywordlis object to compare with
	 * @return bool
	 */
	public function hasAdditionalKeys(KeywordList $compare_keyword_list) {
		$compare_keywords = $compare_keyword_list->getKeywords();
		$diff             = array_intersect($this->getKeywords(), $compare_keyword_list->getKeywords());

		sort($compare_keywords);
		sort($diff);

		return $compare_keywords != $diff;
	}
}
