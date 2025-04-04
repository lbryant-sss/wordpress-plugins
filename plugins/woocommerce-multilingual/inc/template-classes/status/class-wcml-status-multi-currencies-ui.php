<?php

class WCML_Status_Multi_Currencies_UI extends WCML_Templates_Factory {

	private $woocommerce_wpml;

	/**
	 * WCML_Status_Multi_Currencies_UI constructor.
	 *
	 * @param woocommerce_wpml $woocommerce_wpml
	 */
	public function __construct( $woocommerce_wpml ) {
		// @todo Cover by tests, required for wcml-3037.
		parent::__construct();

		$this->woocommerce_wpml = $woocommerce_wpml;
	}

	public function get_model() {

		$sec_currencies       = [];
		$sec_currencies_codes = [];

		if ( $this->woocommerce_wpml->settings['enable_multi_currency'] == WCML_MULTI_CURRENCIES_INDEPENDENT ) {
			$sec_currencies = $this->woocommerce_wpml->multi_currency->get_currencies();
			foreach ( $sec_currencies as $code => $sec_currency ) {
				$sec_currencies_codes[] = $code;
			}
		}

		$model = [
			'mc_enabled'     => $this->woocommerce_wpml->settings['enable_multi_currency'] == WCML_MULTI_CURRENCIES_INDEPENDENT,
			'sec_currencies' => join( ', ', $sec_currencies_codes ),
			'add_cur_link'   => \WCML\Utilities\AdminUrl::getMultiCurrencyTab(),
			'strings'        => [
				'mc_missing'     => __( 'Multicurrency', 'woocommerce-multilingual' ),
				'no_secondary'   => __( "You haven't added any secondary currencies.", 'woocommerce-multilingual' ),
				/* translators: %s is a list of currency codes */
				'sec_currencies' => __( 'Secondary currencies: %s', 'woocommerce-multilingual' ),
				'not_enabled'    => __( 'Multi-currency is not enabled.', 'woocommerce-multilingual' ),
				'add_cur'        => __( 'Add Currencies', 'woocommerce-multilingual' ),
			],
		];

		return $model;

	}

	public function init_template_base_dir() {
		$this->template_paths = [
			WCML_PLUGIN_PATH . '/templates/status/',
		];
	}

	public function get_template() {
		return 'multi_currencies.twig';
	}

}
