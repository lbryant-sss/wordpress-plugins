<?php

namespace WCML\Rest\Wrapper;

use WPML\FP\Fns;
use WPML\FP\Obj;
use WCML\Rest\Exceptions\InvalidLanguage;
use WCML\Rest\Exceptions\InvalidTerm;
use WCML\Rest\Exceptions\MissingLanguage;

class ProductTerms extends Handler {

	/** @var \SitePress */
	private $sitepress;
	/** @var \WPML_Term_Translation */
	private $wpmlTermTranslations;
	/** @var \WCML_Terms */
	private $wcmlTerms;

	public function __construct(
		\SitePress $sitepress,
		\WPML_Term_Translation $wpmlTermTranslations,
		\WCML_Terms $wcmlTerms
	) {
		$this->sitepress            = $sitepress;
		$this->wpmlTermTranslations = $wpmlTermTranslations;
		$this->wcmlTerms            = $wcmlTerms;
	}

	/**
	 * @param array            $args
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return array
	 */
	public function query( $args, $request ) {
		$language = Obj::prop( 'lang', $request->get_params() );

		if ( $language ) {
			if ( 'all' === $language ) {
				remove_filter( 'terms_clauses', [ $this->sitepress, 'terms_clauses' ], 10 );
				remove_filter( 'get_term', [ $this->sitepress, 'get_term_adjust_id' ], 1 );
				remove_filter( 'get_terms_args', [ $this->sitepress, 'get_terms_args_filter' ], 10 );
			} else {
				$this->checkLanguage( $language );
			}
		}

		return $args;
	}

	/**
	 * Appends the language and translation information to the get_product response
	 *
	 * @param \WP_REST_Response $response
	 * @param object|\WP_Term   $object
	 * @param \WP_REST_Request  $request
	 *
	 * @return \WP_REST_Response
	 */
	public function prepare( $response, $object, $request ) {

		$response->data['translations'] = [];

		$termTaxonomyId = (int) Obj::prop( 'term_taxonomy_id', $object );
		$trid           = $this->wpmlTermTranslations->get_element_trid( $termTaxonomyId );

		if ( $trid ) {
			$getTermId = function( $termTaxonomyId ) {
				$term = get_term_by( 'term_taxonomy_id', $termTaxonomyId );
				return isset( $term->term_id ) ? $term->term_id : null;
			};

			$response->data['translations'] = Fns::map(
				$getTermId,
				$this->wpmlTermTranslations->get_element_translations( $termTaxonomyId, $trid )
			);

			$response->data['lang'] = $this->wpmlTermTranslations->get_element_lang_code( $termTaxonomyId );
		}

		return $response;
	}

	/**
	 * Sets the product information according to the provided language
	 *
	 * @param \WP_Term         $term
	 * @param \WP_REST_Request $request
	 * @param bool             $creating if true, it is an insert event; otherwise an update.
	 *
	 * @throws MissingLanguage When no $language is set yet $translationOf is set.
	 * @throws InvalidTerm When updating the term and no $trid obtained.
	 */
	public function insert( $term, $request, $creating ) {
		$getParam = Obj::prop( Fns::__, $request->get_params() );

		$language      = $getParam( 'lang' );
		$translationOf = $getParam( 'translation_of' );

		if ( $language ) {

			$this->checkLanguage( $language );

			if ( $creating ) {
				if ( $translationOf ) {
					$translationOfTerm = get_term( $translationOf, $term->taxonomy );

					$trid = isset( $translationOfTerm->term_taxonomy_id )
						? $this->wpmlTermTranslations->get_element_trid( $translationOfTerm->term_taxonomy_id )
						: null;

					if ( ! $trid ) {
						throw new InvalidTerm( $translationOf );
					}
				} else {
					$trid = null;
				}
			} else {
				$trid = $this->wpmlTermTranslations->get_element_trid( $term->term_taxonomy_id );
			}

			$this->sitepress->set_element_language_details( $term->term_taxonomy_id, 'tax_' . $term->taxonomy, $trid, $language );

			$this->wcmlTerms->update_terms_translated_status( $term->taxonomy );
		} elseif ( $translationOf ) {
			throw new MissingLanguage();
		}
	}

	/**
	 * @param string $language
	 *
	 * @throws InvalidLanguage When $language is not active.
	 */
	private function checkLanguage( $language ) {
		if ( ! $this->sitepress->is_active_language( $language ) ) {
			throw new InvalidLanguage( $language );
		}
	}
}
