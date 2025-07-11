<?php

use function WCML\functions\isStandAlone;

class WCML_Cart {
	/** @var woocommerce_wpml */
	private $woocommerce_wpml;
	/** @var SitePress */
	private $sitepress;
	/** @var WooCommerce */
	private $woocommerce;

	/**
	 * WCML_Cart constructor.
	 *
	 * @param woocommerce_wpml $woocommerce_wpml
	 * @param SitePress        $sitepress
	 * @param WooCommerce      $woocommerce
	 */
	public function __construct( woocommerce_wpml $woocommerce_wpml, \WPML\Core\ISitePress $sitepress, WooCommerce $woocommerce ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->sitepress        = $sitepress;
		$this->woocommerce      = $woocommerce;
	}

	public function add_hooks() {

		if ( $this->is_clean_cart_enabled() ) {

			$this->enqueue_dialog_ui();

			add_action( 'wcml_removed_cart_items', [ $this, 'wcml_removed_cart_items_widget' ] );
			add_action( 'wp_ajax_wcml_cart_clear_removed_items', [ $this, 'wcml_cart_clear_removed_items' ] );
			add_action(
				'wp_ajax_nopriv_wcml_cart_clear_removed_items',
				[
					$this,
					'wcml_cart_clear_removed_items',
				]
			);

			if ( $this->is_clean_cart_enabled_for_currency_switch() ) {
				add_filter( 'wcml_switch_currency_exception', [ $this, 'cart_switching_currency' ], 10, 4 );
				add_action(
					'wcml_before_switch_currency',
					[
						$this,
						'switching_currency_empty_cart_if_needed',
					],
					10,
					2
				);
			}
		} else {
			// cart widget
			add_action( 'wp_ajax_woocommerce_get_refreshed_fragments', [ $this, 'wcml_refresh_fragments' ], 0 );
			add_action( 'wp_ajax_woocommerce_add_to_cart', [ $this, 'wcml_refresh_fragments' ], 0 );
			add_action(
				'wp_ajax_nopriv_woocommerce_get_refreshed_fragments',
				[
					$this,
					'wcml_refresh_fragments',
				],
				0
			);
			add_action( 'wp_ajax_nopriv_woocommerce_add_to_cart', [ $this, 'wcml_refresh_fragments' ], 0 );

			// cart
			add_action( 'woocommerce_before_checkout_process', [ $this, 'wcml_refresh_cart_total' ] );

			if ( ! isStandAlone() ) {
			    add_action( 'woocommerce_before_calculate_totals', [ $this, 'woocommerce_calculate_totals' ], 100 );
			    add_filter( 'woocommerce_cart_item_data_to_validate', [ $this, 'validate_cart_item_data' ], 10, 2 );
			    add_filter( 'woocommerce_cart_item_product', [ $this, 'adjust_cart_item_product_name' ] );
			    add_filter( 'woocommerce_cart_item_permalink', [ $this, 'cart_item_permalink' ], 10, 2 );
			    add_filter( 'woocommerce_paypal_args', [ $this, 'filter_paypal_args' ] );
			    add_filter(
			        'woocommerce_add_to_cart_sold_individually_found_in_cart',
			        [
			            $this,
			            'add_to_cart_sold_individually_exception',
			        ],
			        10,
			        4
			    );
			    add_filter( 'woocommerce_cart_hash_key', [ $this, 'add_language_to_cart_hash_key' ] );
			    add_filter('woocommerce_cart_crosssell_ids', [ $this, 'convert_crosssell_ids' ] );
			    $this->localize_flat_rates_shipping_classes();
			}
		}
	}

	public function is_clean_cart_enabled() {

		$cart_sync_settings   = $this->woocommerce_wpml->settings['cart_sync'];
		$wpml_cookies_enabled = $this->sitepress->get_setting( $this->sitepress->get_wp_api()->constant( 'WPML_Cookie_Setting::COOKIE_SETTING_FIELD' ) );

		if (
			$wpml_cookies_enabled &&
			(
				(
					$this->woocommerce_wpml->settings['enable_multi_currency'] === $this->sitepress->get_wp_api()->constant( 'WCML_MULTI_CURRENCIES_INDEPENDENT' ) &&
					$cart_sync_settings['currency_switch'] === $this->sitepress->get_wp_api()->constant( 'WCML_CART_CLEAR' )
				) ||
				$cart_sync_settings['lang_switch'] === $this->sitepress->get_wp_api()->constant( 'WCML_CART_CLEAR' )
			)
		) {
			return true;
		}

		return false;
	}

	private function is_clean_cart_enabled_for_currency_switch() {

		$cart_sync_settings   = $this->woocommerce_wpml->settings['cart_sync'];
		$wpml_cookies_enabled = $this->sitepress->get_setting( $this->sitepress->get_wp_api()->constant( 'WPML_Cookie_Setting::COOKIE_SETTING_FIELD' ) );

		if (
			$wpml_cookies_enabled &&
			$cart_sync_settings['currency_switch'] === $this->sitepress->get_wp_api()->constant( 'WCML_CART_CLEAR' )
		) {
			return true;
		}

		return false;
	}

	public function enqueue_dialog_ui() {

		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );

	}

	public function wcml_removed_cart_items_widget( $args = [] ) {

		if ( ! empty( $this->woocommerce->session ) ) {
			$removed_cart_items = new WCML_Removed_Cart_Items_UI( $args, $this->woocommerce_wpml, $this->sitepress, $this->woocommerce );
			$preview            = $removed_cart_items->get_view();

			if ( ! isset( $args['echo'] ) || $args['echo'] ) {
				echo $preview;
			} else {
				return $preview;
			}
		}

	}

	public function switching_currency_empty_cart_if_needed( $currency, $force_switch ) {
		if ( $force_switch && $this->woocommerce_wpml->settings['cart_sync']['currency_switch'] == $this->sitepress->get_wp_api()->constant( 'WCML_CART_CLEAR' ) ) {
			$this->empty_cart_if_needed( 'currency_switch' );
			$this->woocommerce->session->set( 'wcml_switched_type', 'currency' );
		}
	}

	public function empty_cart_if_needed( $switching_type ) {

		if ( $this->woocommerce_wpml->settings['cart_sync'][ $switching_type ] == $this->sitepress->get_wp_api()->constant( 'WCML_CART_CLEAR' ) ) {
			$removed_products = $this->woocommerce->session->get( 'wcml_removed_items' ) ? maybe_unserialize( $this->woocommerce->session->get( 'wcml_removed_items' ) ) : [];

			foreach ( WC()->cart->get_cart_for_session() as $item_key => $cart ) {
				if ( ! in_array( $cart['product_id'], $removed_products ) ) {
					$removed_products[] = $cart['product_id'];
				}
				WC()->cart->remove_cart_item( $item_key );
			}

			if ( ! empty( $this->woocommerce->session ) ) {
				$this->woocommerce->session->set( 'wcml_removed_items', serialize( $removed_products ) );
			}
		}
	}

	public function wcml_cart_clear_removed_items() {

		$nonce = filter_input( INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wcml_clear_removed_items' ) ) {
			die( 'Invalid nonce' );
		}

		$this->woocommerce->session->__unset( 'wcml_removed_items' );
		$this->woocommerce->session->__unset( 'wcml_switched_type' );
	}

	public function cart_switching_currency( $exc, $current_currency, $new_currency, $return = false ) {

		$cart_for_session = ! is_null( WC()->cart ) ? array_filter( WC()->cart->get_cart_contents() ) : false;

		if ( $this->woocommerce_wpml->settings['cart_sync']['currency_switch'] == WCML_CART_SYNC || empty( $cart_for_session ) ) {
			return $exc;
		}

		$dialog_title         = __( 'Switching currency?', 'woocommerce-multilingual' );
		$confirmation_message = __( 'Your cart is not empty! After you switched the currency, all items from the cart will be removed and you have to add them again.', 'woocommerce-multilingual' );
		/* translators: %s is a currency */
        $stay_in              = sprintf( __( 'Keep using %s', 'woocommerce-multilingual' ), $current_currency );
		$switch_to            = __( 'Proceed', 'woocommerce-multilingual' );

		ob_start();
		$this->cart_alert( $dialog_title, $confirmation_message, $switch_to, $stay_in, $new_currency, $current_currency );
		$html = ob_get_contents();
		ob_end_clean();

		if ( $return ) {
			return [ 'prevent_switching' => $html ];
		} else {
			wp_send_json_success( [ 'prevent_switching' => $html ] );
		}

		return true;
	}

	public function cart_alert( $dialog_title, $confirmation_message, $switch_to, $stay_in, $switch_to_value, $stay_in_value = false, $language_switch = false ) {
		if ( apply_filters( 'wcml_hide_cart_alert_dialog', false ) ) {
			$switching_type = $language_switch ? 'lang_switch' : 'currency_switch';
			$this->empty_cart_if_needed( $switching_type );
			return false;
		}?>
		<div id="wcml-cart-dialog-wrapper">
			<div class="wcml-cart-dialog-confirm">
				<p class="wcml-cart-dialog-title"><?php echo esc_attr( $dialog_title ); ?></p>
				<p class="wcml-cart-dialog-content"><?php echo esc_html( $confirmation_message ); ?></p>
				<div class="wcml-cart-dialog-buttons">
					<button id="wcml-cart-dialog-switch" class="button"><?php echo esc_html( $switch_to ); ?></button>
					<button id="wcml-cart-dialog-stay" class="button"><?php echo esc_html( $stay_in ); ?></button>
				</div>
			</div>
			<style>
				/* The Modal (background) */
				#wcml-cart-dialog-wrapper {
					position: fixed;
					z-index: 1;
					left: 0;
					top: 0;
					width: 100%;
					height: 100%;
					overflow: auto;
					background-color: rgb(0,0,0);
					background-color: rgba(0,0,0,0.7);
					z-index: 10000;
				}

				/* Modal Content */
				.wcml-cart-dialog-confirm {
					background-color: #fefefe;
					margin: 25% auto;
					padding: 20px;
					border: 1px solid #888;
					width: 50%;
					max-width: 560px;
				}

				.wcml-cart-dialog-title {
					border-bottom: 1px solid #dcdcde;
					font-size: 18px;
					font-weight: 600;
					padding: 0 0 10px;
				}

				.wcml-cart-dialog-buttons {
					text-align: right;
				}

				.wcml-cart-dialog-buttons button {
					color: #555;
					font-size: 16px;
					padding: 4px 8px;
					margin: 0 3px;
				}

				.wcml-cart-dialog-buttons button:hover,
				.wcml-cart-dialog-buttons button:focus {
					color: #000;
					cursor: pointer;
				}
			</style>

			<script type="text/javascript">
				(function(){
					const modal = document.getElementById('wcml-cart-dialog-wrapper');

					const btnStay   = document.getElementById('wcml-cart-dialog-stay');
					const btnSwitch = document.getElementById('wcml-cart-dialog-switch');

					const removeAjaxSpinners = function() {
						document.querySelectorAll('.wcml-spinner').forEach(function(spinner) {
							spinner.remove();
						});
					}

					const removeModal = function() {
						modal.remove();

						const scriptInsertedByAjax = document.getElementById('wcml-cart-dialog-script');

						if (scriptInsertedByAjax) {
							scriptInsertedByAjax.remove();
						}
					}

					const restoreCurrencyInSwitchers = function() {
						document.querySelectorAll('.wcml_currency_switcher').forEach(function(switcher) {
							switcher.disabled = false;
							switcher.value = '<?php echo esc_js( $stay_in_value ); ?>';
						});
					}

					btnSwitch.onclick = function() {
						removeModal();
						<?php if ( $language_switch ) : ?>
						window.location = '<?php echo esc_url( $switch_to_value, null, 'redirect' ); ?>';
						<?php else : ?>
						wcml_load_currency("<?php echo esc_js( $switch_to_value ); ?>", true);
						removeAjaxSpinners();
						<?php endif; ?>
					}

					btnStay.onclick = function() {
						removeModal();
						<?php if ( $language_switch ) : ?>
						window.location = '<?php echo esc_url( $stay_in_value, null, 'redirect' ); ?>';
						<?php else : ?>
						removeAjaxSpinners();
						restoreCurrencyInSwitchers();
						document.addEventListener('click', wcml_switch_currency_handler);
						<?php endif; ?>
					}
				})();
			</script>
		</div>
		<?php
	}

	public function wcml_refresh_fragments() {
		WC()->cart->calculate_totals();
	}

	/**
	 * Update cart and cart session when switch language.
	 *
	 * @param WC_Cart      $cart
	 * @param string|false $currency
	 */
	public function woocommerce_calculate_totals( $cart, $currency = false ) {

		$current_language = $this->sitepress->get_current_language();
		$new_cart_data    = [];

		foreach ( $cart->cart_contents as $key => $cart_item ) {
			$tr_product_id = apply_filters( 'wpml_object_id', $cart_item['product_id'], 'product', false, $current_language );
			// translate custom attr labels in cart object.
			// translate custom attr value in cart object.
			$tr_variation_id = null;
			if ( isset( $cart_item['variation'] ) && is_array( $cart_item['variation'] ) ) {
				$tr_variation_id = apply_filters( 'wpml_object_id', $cart_item['variation_id'], 'product_variation', false, $current_language );
				foreach ( $cart_item['variation'] as $attr_key => $attribute ) {
					$cart->cart_contents[ $key ]['variation'][ $attr_key ] = $this->get_cart_attribute_translation(
						$attr_key,
						$attribute,
						$tr_variation_id,
						$current_language,
						$cart_item['product_id'],
						$tr_product_id
					);
				}
			}

			if ( false !== $currency ) {
				$cart->cart_contents[ $key ]['data']->price = get_post_meta( $cart_item['product_id'], '_price', 1 );
			}

			$display_as_translated = apply_filters( 'wpml_is_display_as_translated_post_type', false, 'product' );
			if ( $cart_item['product_id'] == $tr_product_id || ( $display_as_translated && ! $tr_product_id ) ) {
				$new_cart_data[ $key ]              = apply_filters( 'wcml_cart_contents_not_changed', $cart->cart_contents[ $key ], $key, $current_language );
				$new_cart_data[ $key ]['data_hash'] = $this->get_data_cart_hash( $cart_item );
				continue;
			}

			if ( isset( $cart->cart_contents[ $key ]['variation_id'] ) && $cart->cart_contents[ $key ]['variation_id'] ) {
				if ( ! is_null( $tr_variation_id ) ) {
					$cart->cart_contents[ $key ]['product_id']   = intval( $tr_product_id );
					$cart->cart_contents[ $key ]['variation_id'] = intval( $tr_variation_id );
					$cart->cart_contents[ $key ]['data']->set_id( intval( $tr_variation_id ) );
					$cart->cart_contents[ $key ]['data']->set_parent_id( intval( $tr_product_id ) );
					$cart->cart_contents[ $key ]['data']->set_name( get_the_title( $tr_variation_id ) );

					$parent_data          = $cart->cart_contents[ $key ]['data']->get_parent_data();
					$parent_data['title'] = get_the_title( $tr_product_id );
					$cart->cart_contents[ $key ]['data']->set_parent_data( $parent_data );
				}
			} else {
				if ( ! is_null( $tr_product_id ) ) {
					$cart->cart_contents[ $key ]['product_id'] = intval( $tr_product_id );
					$cart->cart_contents[ $key ]['data']->set_id( intval( $tr_product_id ) );
					$cart->cart_contents[ $key ]['data']->set_name( get_the_title( $tr_product_id ) );
				}
			}

			if ( ! is_null( $tr_product_id ) ) {

				$new_key                                = $this->wcml_generate_cart_key( $cart->cart_contents, $key );
				$cart->cart_contents                    = apply_filters( 'wcml_update_cart_contents_lang_switch', $cart->cart_contents, $key, $new_key, $current_language );
				$new_cart_data[ $new_key ]              = $cart->cart_contents[ $key ];
				$new_cart_data[ $new_key ]['key']       = $new_key;
				$new_cart_data[ $new_key ]['data_hash'] = $this->get_data_cart_hash( $new_cart_data[ $new_key ] );

				$new_cart_data = apply_filters( 'wcml_cart_contents', $new_cart_data, $cart->cart_contents, $key, $new_key );
			}
		}

		$cart->cart_contents              = $this->wcml_check_on_duplicate_products_in_cart( $new_cart_data );
		$this->woocommerce->session->cart = $cart->cart_contents;
	}

	/**
	 * @param array $cart_item
	 *
	 * @return string
	 */
	public function get_data_cart_hash( $cart_item ) {

		$data_hash = '';

		if ( function_exists( 'wc_get_cart_item_data_hash' ) ) {
			$hash_product_object = wc_get_product( $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'] );
			if ( $hash_product_object ) {
				$data_hash = wc_get_cart_item_data_hash( $hash_product_object );
			}
		}

		return $data_hash;
	}

	/**
	 * @param array      $item_data
	 * @param WC_Product $product Product object
	 *
	 * @return array
	 */
	public function validate_cart_item_data( array $item_data, $product ) {

		if ( $item_data['attributes'] ) {

			$product_id       = $product->get_parent_id();
			$product_language = $this->sitepress->get_language_for_element( $product_id, 'post_' . $item_data['type'] );
			$tr_product_id    = apply_filters( 'wpml_object_id', $product_id, 'product', false, $product_language );

			foreach ( $item_data['attributes'] as $key => $name ) {
				$item_data['attributes'][ $key ] = $this->get_cart_attribute_translation( $key, $name, $product->get_id(), $product_language, $product_id, $tr_product_id );
			}
		}

		return $item_data;
	}

	public function wcml_check_on_duplicate_products_in_cart( $cart_contents ) {

		$exists_products = [];
		remove_action( 'woocommerce_before_calculate_totals', [ $this, 'woocommerce_calculate_totals' ], 100 );

		foreach ( $cart_contents as $key => $cart_content ) {
			$cart_contents = apply_filters( 'wcml_check_on_duplicated_products_in_cart', $cart_contents, $key, $cart_content );
			if ( apply_filters( 'wcml_exception_duplicate_products_in_cart', false, $cart_content ) ) {
				continue;
			}

			$quantity = $cart_content['quantity'];

			$search_key = $this->wcml_generate_cart_key( $cart_contents, $key );
			if ( array_key_exists( $search_key, $exists_products ) ) {
				unset( $cart_contents[ $key ] );
				$cart_contents[ $exists_products[ $search_key ] ]['quantity'] = $cart_contents[ $exists_products[ $search_key ] ]['quantity'] + $quantity;
				$this->woocommerce->cart->calculate_totals();
			} else {
				$exists_products[ $search_key ] = $key;
			}
		}

		add_action( 'woocommerce_before_calculate_totals', [ $this, 'woocommerce_calculate_totals' ], 100 );

		return $cart_contents;
	}

	public function get_cart_attribute_translation( $attr_key, $attribute, $variation_id, $current_language, $product_id, $tr_product_id ) {

		$attr_translation = $attribute;

		if ( ! empty( $attribute ) ) {

			$taxonomy = $this->remove_attribute_prefix( $attr_key );

			if ( taxonomy_exists( $taxonomy ) ) {
				if ( $this->woocommerce_wpml->attributes->is_translatable_attribute( $taxonomy ) ) {
					$term_id          = $this->woocommerce_wpml->terms->wcml_get_term_id_by_slug( $taxonomy, $attribute );
					$trnsl_term_id    = apply_filters( 'wpml_object_id', $term_id, $taxonomy, true, $current_language );
					$term             = $this->woocommerce_wpml->terms->wcml_get_term_by_id( $trnsl_term_id, $taxonomy );
					$attr_translation = $term->slug;
				}
			} elseif ( $variation_id ) {
				$trnsl_attr = get_post_meta( $variation_id, $attr_key, true );

				if ( $trnsl_attr ) {
					$attr_translation = $trnsl_attr;
				} else {
					$attr_translation = $this->woocommerce_wpml->attributes->get_custom_attr_translation( $product_id, $tr_product_id, $taxonomy, $attribute );
				}
			}
		}

		return $attr_translation;
	}

	/**
	 * @param string $attr_key
	 *
	 * @return string
	 */
	protected function remove_attribute_prefix( $attr_key ) {
		$taxonomy = $attr_key;

		$attribute_prefix = 'attribute_';
		if ( strpos( $attr_key, $attribute_prefix ) === 0 ) {
			$taxonomy = substr( $attr_key, strlen( $attribute_prefix ) );
		}

		return $taxonomy;
	}

	public function wcml_generate_cart_key( $cart_contents, $key ) {
		$cart_item_data = $this->get_cart_item_data_from_cart( $cart_contents[ $key ] );

		return $this->woocommerce->cart->generate_cart_id(
			$cart_contents[ $key ]['product_id'],
			$cart_contents[ $key ]['variation_id'],
			$cart_contents[ $key ]['variation'],
			$cart_item_data
		);
	}

	// get cart_item_data from existing cart array ( from session )
	public function get_cart_item_data_from_cart( $cart_contents ) {
		unset( $cart_contents['product_id'] );
		unset( $cart_contents['variation_id'] );
		unset( $cart_contents['variation'] );
		unset( $cart_contents['quantity'] );
		unset( $cart_contents['line_total'] );
		unset( $cart_contents['line_subtotal'] );
		unset( $cart_contents['line_tax'] );
		unset( $cart_contents['line_subtotal_tax'] );
		unset( $cart_contents['line_tax_data'] );
		unset( $cart_contents['data'] );
		unset( $cart_contents['key'] );

		return apply_filters( 'wcml_filter_cart_item_data', $cart_contents );
	}

	// refresh cart total to return correct price from WC object
	public function wcml_refresh_cart_total() {
		WC()->cart->calculate_totals();
	}


	public function localize_flat_rates_shipping_classes() {

		if ( wp_doing_ajax() && isset( $_POST['action'] ) && $_POST['action'] == 'woocommerce_update_order_review' ) {
			$shipping_methods = $this->woocommerce->shipping()->get_shipping_methods();
			foreach ( $shipping_methods as $method ) {
				if ( isset( $method->flat_rate_option ) ) {
					add_filter( 'option_' . $method->flat_rate_option, [ $this, 'translate_shipping_class' ] );
				}
			}
		}
	}

	public function translate_shipping_class( $rates ) {

		if ( is_array( $rates ) ) {
			foreach ( $rates as $shipping_class => $value ) {
				$term_id = $this->woocommerce_wpml->terms->wcml_get_term_id_by_slug( 'product_shipping_class', $shipping_class );

				if ( $term_id && ! is_wp_error( $term_id ) ) {
					$translated_term_id = apply_filters( 'wpml_object_id', $term_id, 'product_shipping_class', true );
					if ( $translated_term_id != $term_id ) {
						$term = $this->woocommerce_wpml->terms->wcml_get_term_by_id( $translated_term_id, 'product_shipping_class' );
						unset( $rates[ $shipping_class ] );
						$rates[ $term->slug ] = $value;

					}
				}
			}
		}

		return $rates;
	}

	public function filter_paypal_args( $args ) {
		$args['lc'] = $this->sitepress->get_current_language();

		// filter URL when default permalinks uses
		$wpml_settings = $this->sitepress->get_settings();
		if ( $wpml_settings['language_negotiation_type'] == 3 ) {
			$args['notify_url'] = str_replace( '%2F&', '&', $args['notify_url'] );
		}

		return $args;
	}

	public function add_to_cart_sold_individually_exception( $found_in_cart, $product_id, $variation_id, $cart_item_data ) {

		$post_id = $product_id;
		if ( $variation_id ) {
			$post_id = $variation_id;
		}

		foreach ( WC()->cart->cart_contents as $cart_item ) {
			if ( $this->sold_individually_product( $cart_item, $cart_item_data, $post_id ) ) {
				$found_in_cart = true;
				break;
			}
		}

		return $found_in_cart;
	}

	public function sold_individually_product( $cart_item, $cart_item_data, $post_id ) {

		$current_product_trid = $this->sitepress->get_element_trid( $post_id, 'post_' . get_post_type( $post_id ) );

		if ( ! empty( $cart_item['variation_id'] ) ) {
			$cart_element_trid = $this->sitepress->get_element_trid( $cart_item['variation_id'], 'post_product_variation' );
		} else {
			$cart_element_trid = $this->sitepress->get_element_trid( $cart_item['product_id'], 'post_product' );
		}

		if ( apply_filters( 'wcml_add_to_cart_sold_individually', true, $cart_item_data, $post_id, $cart_item['quantity'] ) &&
			 $current_product_trid == $cart_element_trid &&
			 $cart_item['quantity'] > 0
		) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @param string $permalink
	 * @param array  $cart_item
	 *
	 * @return string
	 */
	public function cart_item_permalink( $permalink, $cart_item ) {

		if ( ! $this->sitepress->get_setting( 'auto_adjust_ids' ) ) {
			$permalink = get_permalink( $cart_item['product_id'] );
		}

		return $permalink;
	}

	/**
	 * @param string $currency
	 *
	 * @return float
	 */
	public function convert_cart_total_to_currency( $currency ) {
		$total          = WC()->cart->get_total( 'raw' );
		$total_default  = $this->woocommerce_wpml->multi_currency->prices->unconvert_price_amount( $total );
		$total_currency = $this->woocommerce_wpml->multi_currency->prices->convert_price_amount( $total_default, $currency );

		return $total_currency;
	}

	/**
	 * @param string $currency
	 *
	 * @return string
	 */
	public function format_converted_cart_total_in_currency( $currency ) {
		return $this->woocommerce_wpml->multi_currency->prices->format_price_in_currency( $this->convert_cart_total_to_currency( $currency ), $currency );
	}

	public function convert_cart_shipping_to_currency( $currency ) {
		$shipping_amount_in_default_currency = $this->woocommerce_wpml->multi_currency->prices->unconvert_price_amount( WC()->cart->get_shipping_total() );

		return $this->woocommerce_wpml->multi_currency->prices->convert_price_amount( $shipping_amount_in_default_currency, $currency );
	}

	/**
	 * @param WC_Product $product
	 *
	 * @return WC_Product
	 */
	public function adjust_cart_item_product_name( $product ) {

		$product_id = $product->get_id();

		$current_product_id = wpml_object_id_filter( $product_id, get_post_type( $product_id ) );

		if ( $current_product_id ) {
			$product->set_name( wc_get_product( $current_product_id )->get_name() );
		}

		return $product;
	}

	/**
	 * @param string $cart_hash_key
	 *
	 * @return string
	 */
	public function add_language_to_cart_hash_key( $cart_hash_key ) {
		return $cart_hash_key . '-' . $this->sitepress->get_current_language();
	}

	/**
	 * @param int[] $productIds
	 *
	 * @return int[]
	 */
	public function convert_crosssell_ids( $productIds ) {
		$returnOriginal = $this->sitepress->is_display_as_translated_post_type( 'product' );

		// $convertId :: int -> int|null
		$convertId = function( $id ) use ( $returnOriginal ) {
			return $this->sitepress->get_object_id( $id, 'product', $returnOriginal );
		};

		return wpml_collect( $productIds )
			->map( $convertId )
			->filter()
			->toArray();
	}
}
