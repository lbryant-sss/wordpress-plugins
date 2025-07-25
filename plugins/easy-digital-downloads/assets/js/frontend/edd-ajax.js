/* global edd_scripts, edd_global_vars */

/**
 * Internal dependencies.
 */
import { recalculateTaxes } from './checkout/utils.js';

jQuery( document ).ready( function( $ ) {
	// Hide unneeded elements. These are things that are required in case JS breaks or isn't present
	$( '.edd-add-to-cart:not(.edd-no-js)' ).addClass( 'edd-has-js' );

	// Send Remove from Cart requests
	$( document.body ).on( 'click.eddRemoveFromCart', '.edd-remove-from-cart', function( event ) {
		const $this = $( this ),
			item = $this.data( 'cart-item' ),
			action = $this.data( 'action' ),
			id = $this.data( 'download-id' ),
			nonce = $this.data( 'nonce' ),
			data = {
				action: action,
				cart_item: item,
				nonce: nonce,
				timestamp: $this.data( 'timestamp' ),
				token: $this.data( 'token' ),
				current_page: edd_scripts.current_page,
			};

		 $.ajax( {
			type: 'POST',
			data: data,
			dataType: 'json',
			url: edd_scripts.ajaxurl,
			xhrFields: {
				withCredentials: true,
			},
			success: function( response ) {
				if ( response.removed ) {
					if ( ( parseInt( edd_scripts.position_in_cart, 10 ) === parseInt( item, 10 ) ) || edd_scripts.has_purchase_links ) {
						window.location = window.location;
						return false;
					}

					// Remove the selected cart item
					$( '.edd-cart' ).each( function() {
						$( this ).find( "[data-cart-item='" + item + "']" ).parent().remove();
					} );

					//Reset the data-cart-item attributes to match their new values in the EDD session cart array
					$( '.edd-cart' ).each( function() {
						let cart_item_counter = 0;
						$( this ).find( '[data-cart-item]' ).each( function() {
							$( this ).attr( 'data-cart-item', cart_item_counter );
							cart_item_counter = cart_item_counter + 1;
						} );
					} );

					// Check to see if the purchase form(s) for this download is present on this page
					if ( $( '[id^=edd_purchase_' + id + ']' ).length ) {
						$( '[id^=edd_purchase_' + id + '] .edd_go_to_checkout' ).hide();
						$( '[id^=edd_purchase_' + id + '] .edd-add-to-cart.edd-has-js' ).show().removeAttr( 'data-edd-loading' );
						if ( edd_scripts.quantities_enabled === '1' ) {
							$( '[id^=edd_purchase_' + id + '] .edd_download_quantity_wrapper' ).show();
						}
					}

					$( 'span.edd-cart-quantity' ).text( response.cart_quantity );
					$( document.body ).trigger( 'edd_quantity_updated', [ response.cart_quantity ] );
					if ( edd_scripts.taxes_enabled ) {
						$( '.cart_item.edd_subtotal span' ).html( response.subtotal );
						$( '.cart_item.edd_cart_tax span' ).html( response.tax );
					}

					$( '.cart_item.edd_total span' ).html( response.total );

					if ( response.cart_quantity === 0 ) {
						$( '.cart_item.edd_subtotal,.edd-cart-number-of-items,.cart_item.edd_checkout,.cart_item.edd_cart_tax,.cart_item.edd_total' ).hide();
						$( '.edd-cart' ).each( function() {
							const cart_wrapper = $( this ).parent();
							if ( cart_wrapper.length ) {
								cart_wrapper.addClass( 'cart-empty' );
								cart_wrapper.removeClass( 'cart-not-empty' );
							}

							$( this ).append( '<li class="cart_item empty">' + edd_scripts.empty_cart_message + '</li>' );
						} );
					}

					$( document.body ).trigger( 'edd_cart_item_removed', [ response ] );
				}
			},
		} ).fail( function( response ) {
			if ( window.console && window.console.log ) {
				console.log( response );
			}
		} ).done( function( response ) {

		} );

		return false;
	} );

	// Send Add to Cart request
	$( document.body ).on( 'click.eddAddToCart', '.edd-add-to-cart', function( e ) {
		e.preventDefault();

		var $this = $( this ),
			form = $this.closest( 'form' );

		// Disable button, preventing rapid additions to cart during ajax request
		$this.prop( 'disabled', true );

		const $spinner = $this.find( '.edd-loading' );
		const container = $this.closest( 'div' );

		// Show the spinner
		$this.attr( 'data-edd-loading', '' );

		var form = $this.parents( 'form' ).last();
		const download = $this.data( 'download-id' );
		const variable_price = $this.data( 'variable-price' );
		const price_mode = $this.data( 'price-mode' );
		const nonce = $this.data( 'nonce' );
		const item_price_ids = [];
		let free_items = true;

		if ( variable_price === 'yes' ) {
			if ( form.find( '.edd_price_option_' + download + '[type="hidden"]' ).length > 0 ) {
				item_price_ids[ 0 ] = $( '.edd_price_option_' + download, form ).val();
				if ( form.find( '.edd-submit' ).data( 'price' ) && form.find( '.edd-submit' ).data( 'price' ) > 0 ) {
					free_items = false;
				}
			} else {
				if ( ! form.find( '.edd_price_option_' + download + ':checked', form ).length ) {
					 // hide the spinner
					$this.removeAttr( 'data-edd-loading' );
					alert( edd_scripts.select_option );
					e.stopPropagation();
					$this.prop( 'disabled', false );
					return false;
				}

				form.find( '.edd_price_option_' + download + ':checked', form ).each( function( index ) {
					item_price_ids[ index ] = $( this ).val();

					// If we're still only at free items, check if this one is free also
					if ( true === free_items ) {
						const item_price = $( this ).data( 'price' );
						if ( item_price && item_price > 0 ) {
							// We now have a paid item, we can't use add_to_cart
							free_items = false;
						}
					}
				} );
			}
		} else {
			item_price_ids[ 0 ] = download;
			if ( $this.data( 'price' ) && $this.data( 'price' ) > 0 ) {
				free_items = false;
			}
		}

		// If we've got nothing but free items being added, change to add_to_cart
		if ( free_items ) {
			form.find( '.edd_action_input' ).val( 'add_to_cart' );
		}

		if ( 'straight_to_gateway' === form.find( '.edd_action_input' ).val() ) {
			form.submit();
			return true; // Submit the form
		}

		const action = $this.data( 'action' );
		const data = {
			action: action,
			download_id: download,
			price_ids: item_price_ids,
			post_data: $( form ).serialize(),
			nonce: nonce,
			current_page: edd_scripts.current_page,
			timestamp: $this.data( 'timestamp' ),
			token: $this.data( 'token' ),
		};

		$.ajax( {
			type: 'POST',
			data: data,
			dataType: 'json',
			url: edd_scripts.ajaxurl,
			xhrFields: {
				withCredentials: true,
			},
			success: function( response ) {
				const store_redirect = edd_scripts.redirect_to_checkout === '1';
				const item_redirect = form.find( 'input[name=edd_redirect_to_checkout]' ).val() === '1';

				if ( ( store_redirect && item_redirect ) || ( ! store_redirect && item_redirect ) ) {
					window.location = edd_scripts.checkout_page;
				} else {
					// Add the new item to the cart widget
					if ( edd_scripts.taxes_enabled === '1' ) {
						$( '.cart_item.edd_subtotal' ).show();
						$( '.cart_item.edd_cart_tax' ).show();
					}

					$( '.cart_item.edd_total' ).show();
					$( '.cart_item.edd_checkout' ).show();

					if ( $( '.cart_item.empty' ).length ) {
						$( '.cart_item.empty' ).hide();
					}

					$( '.widget_edd_cart_widget .edd-cart' ).each( function( cart ) {
						const target = $( this ).find( '.edd-cart-meta:first' );
						$( response.cart_item ).insertBefore( target );

						const cart_wrapper = $( this ).parent();
						if ( cart_wrapper.length ) {
							cart_wrapper.addClass( 'cart-not-empty' );
							cart_wrapper.removeClass( 'cart-empty' );
						}
					} );

					// Update the totals
					if ( edd_scripts.taxes_enabled === '1' ) {
						$( '.edd-cart-meta.edd_subtotal span' ).html( response.subtotal );
						$( '.edd-cart-meta.edd_cart_tax span' ).html( response.tax );
					}

					$( '.edd-cart-meta.edd_total span' ).html( response.total );

					// Update the cart quantity
					const items_added = $( '.edd-cart-item-title', response.cart_item ).length;

					$( 'span.edd-cart-quantity' ).each( function() {
						$( this ).text( response.cart_quantity );
						$( document.body ).trigger( 'edd_quantity_updated', [ response.cart_quantity ] );
					} );

					// Show the "number of items in cart" message
					if ( $( '.edd-cart-number-of-items' ).css( 'display' ) === 'none' ) {
						$( '.edd-cart-number-of-items' ).show( 'slow' );
					}

					if ( variable_price === 'no' || price_mode !== 'multi' ) {
						// Switch purchase to checkout if a single price item or variable priced with radio buttons
						$( '.edd-add-to-cart.edd-has-js', container ).toggle();
						$( '.edd_go_to_checkout', container ).show();
					}

					if ( price_mode === 'multi' ) {
						// remove spinner for multi
						$this.removeAttr( 'data-edd-loading' );
					}

					// Update all buttons for same download
					if ( $( '.edd_download_purchase_form' ).length && ( variable_price === 'no' || ! form.find( '.edd_price_option_' + download ).is( 'input:hidden' ) ) ) {
						const parent_form = $( '.edd_download_purchase_form *[data-download-id="' + download + '"]' ).parents( 'form' );
						$( '.edd-add-to-cart', parent_form ).hide();
						if ( price_mode !== 'multi' ) {
							parent_form.find( '.edd_download_quantity_wrapper' ).slideUp();
						}
						$( '.edd_go_to_checkout', parent_form ).show().removeAttr( 'data-edd-loading' );
					}

					if ( response !== 'incart' ) {
						// Show the added message
						$( '.edd-cart-added-alert', container ).fadeIn();
						setTimeout( function() {
							$( '.edd-cart-added-alert', container ).fadeOut();
						}, 3000 );
					}

					// Re-enable the add to cart button
					$this.prop( 'disabled', false );

					$( document.body ).trigger( 'edd_cart_item_added', [ response ] );
				}
			},
		} ).fail( function( response ) {
			if ( window.console && window.console.log ) {
				console.log( response );
			}
		} ).done( function( response ) {

		} );

		return false;
	} );

	// Show the login form on the checkout page
	$( '#edd_checkout_form_wrap' ).on( 'click', '.edd_checkout_register_login', function() {
		const $this = $( this ),
			data = {
				action: $this.data( 'action' ),
				nonce: $this.data( 'nonce' ),
			};

		// Show the ajax loader
		$( '.edd-cart-ajax' ).show();

		$.post( edd_scripts.ajaxurl, data, function( checkout_response ) {
			$( '#edd_checkout_login_register' ).html( edd_scripts.loading );
			$( '#edd_checkout_login_register' ).html( checkout_response );
			// Hide the ajax loader
			$( '.edd-cart-ajax' ).hide();
		} );
		return false;
	} );

	// Process the login form via ajax
	$( document ).on( 'click', '#edd_purchase_form #edd_login_fields input[type=submit]', function( e ) {
		e.preventDefault();

		const complete_purchase_val = $( this ).val();

		$( this ).attr( 'data-original-value', complete_purchase_val );

		$( this ).val( edd_global_vars.purchase_loading );

		$( this ).after( '<span class="edd-loading-ajax edd-loading"></span>' );

		const data = {
			action: 'edd_process_checkout_login',
			edd_ajax: 1,
			edd_user_login: $( '#edd_login_fields #edd_user_login' ).val(),
			edd_user_pass: $( '#edd_login_fields #edd_user_pass' ).val(),
			edd_login_nonce: $( '#edd_login_nonce' ).val(),
		};

		$.post( edd_global_vars.ajaxurl, data, function( data ) {
			if ( data.trim() === 'success' ) {
				$( '.edd_errors' ).remove();
				window.location = edd_scripts.checkout_page;
			} else {
				$( '#edd_login_fields input[type=submit]' ).val( complete_purchase_val );
				$( '.edd-loading-ajax' ).remove();
				$( '.edd_errors' ).remove();
				$( '#edd-user-login-submit' ).before( data );
			}
		} );
	} );

	// Load the fields for the selected payment method
	$(document).on('change', 'select#edd-gateway, input.edd-gateway', function (e) {
		const payment_mode = $( '#edd-gateway option:selected, input.edd-gateway:checked' ).val();

		// Ensure the change is applied if this is the input.edd-gateway.
		if ( $( this ).is( 'input.edd-gateway' ) ) {
			// Ensure the correct gateway is 'checked'.
			$( 'input.edd-gateway[value="' + payment_mode + '"]' ).prop( 'checked', 'checked' );
		}

		if ( payment_mode === '0' ) {
			return false;
		}

		edd_load_gateway( payment_mode );
	} );

	// Auto load first payment gateway
	if ( edd_scripts.is_checkout === '1' ) {
		let chosen_gateway = false;
		let ajax_needed = false;

		if ( $( 'select#edd-gateway, input.edd-gateway' ).length ) {
			chosen_gateway = $( "meta[name='edd-chosen-gateway']" ).attr( 'content' );
			ajax_needed = true;
		}

		if ( ! chosen_gateway ) {
			chosen_gateway = edd_scripts.default_gateway;
		}

		if ( ajax_needed ) {
			// If we need to ajax in a gateway form, send the requests for the POST.
			setTimeout( function() {
				edd_load_gateway( chosen_gateway );
			}, 200 );
		} else {
			// The form is already on page, just trigger that the gateway is loaded so further action can be taken.
			setTimeout( function() {
				$( 'body' ).trigger( 'edd_gateway_loaded', [ chosen_gateway ] );
			}, 300 );
		}
	}

	// Process checkout
	$( document ).on( 'click', '#edd_purchase_form #edd_purchase_submit [type=submit]', function( e ) {
		const eddPurchaseform = document.getElementById( 'edd_purchase_form' );

		if ( typeof eddPurchaseform.checkValidity === 'function' && false === eddPurchaseform.checkValidity() ) {
			return;
		}

		e.preventDefault();

		const complete_purchase_val = $( this ).val();

		$( this ).val( edd_global_vars.purchase_loading );

		$( this ).prop( 'disabled', true );

		$( this ).after( '<span class="edd-loading-ajax edd-loading"></span>' );

		$.post( edd_global_vars.ajaxurl, $( '#edd_purchase_form' ).serialize() + '&action=edd_process_checkout&edd_ajax=true', function( data ) {
			if ( data.trim() === 'success' ) {
				$( '.edd_errors' ).remove();
				$( '.edd-error' ).hide();
				$( eddPurchaseform ).submit();
			} else {
				$( '#edd-purchase-button' ).val( complete_purchase_val );
				$( '.edd-loading-ajax' ).remove();
				$( '.edd_errors' ).remove();
				$( '.edd-error' ).hide();
				$( edd_global_vars.checkout_error_anchor ).before( data );
				$( '#edd-purchase-button' ).prop( 'disabled', false );

				$( document.body ).trigger( 'edd_checkout_error', [ data ] );
			}
		} );
	} );

	// Update state field
	$( document.body ).on( 'change', '#edd_cc_address input.card_state, #edd_cc_address select, #edd_address_country, .edd-stripe-card-item .card-address-fields .address_country', update_state_field );

	function update_state_field() {
		const $this = $( this ),
			is_checkout = typeof edd_global_vars !== 'undefined';
		let field_name = 'card_state';
		if ( $( this ).attr( 'id' ) === 'edd_address_country' ) {
			field_name = 'edd_address_state';
		} else if ( $( this ).hasClass( 'address_country' ) ) {
			// Get the data-source from the parent form element.
			let payment_method = $( this ).closest( 'form' ).data( 'source' );
			if ( payment_method ) {
				payment_method = payment_method.replace( 'edd-', '' );
				field_name = 'edds_address_state_' + payment_method;
			}
		}

		const stateInput = $( '#' + field_name );

		if ( field_name !== $this.attr( 'id' ) && stateInput.length ) {

			// If the country field has changed, we need to update the state/province field
			const postData = {
				action: 'edd_get_shop_states',
				country: $this.val(),
				field_name: field_name,
				nonce: $( this ).data( 'nonce' ),
			};

			$.ajax( {
				type: 'POST',
				data: postData,
				url: edd_scripts.ajaxurl,
				xhrFields: {
					withCredentials: true,
				},
				success: function ( response ) {

					let newStateField = '';
					if ( response.trim() === 'nostates' ) {
						newStateField = '<input type="text" id="' + field_name + '" name="card_state" class="card_state edd-input required" value=""/>';
					} else {
						newStateField = response;
					}
					if ( newStateField ) {
						stateInput.replaceWith( newStateField );
					}

					if ( is_checkout ) {
						$( document.body ).trigger( 'edd_cart_billing_address_updated', [ response ] );
					}
				},
			} ).fail( function( data ) {
				if ( window.console && window.console.log ) {
					console.log( data );
				}
			} ).done( function( data ) {
				if ( is_checkout ) {
					recalculateTaxes();
				}
			} );
		} else if ( is_checkout ) {
			recalculateTaxes();
		}

		return false;
	}

	// Backwards compatibility. Assign function to global namespace.
	window.update_state_field = update_state_field;

	// If is_checkout, recalculate sales tax on postalCode change.
	$( document.body ).on( 'change', '#edd_cc_address input[name=card_zip]', function() {
		if ( typeof edd_global_vars !== 'undefined' ) {
			recalculateTaxes();
		}
	} );
} );

// Load a payment gateway
function edd_load_gateway( payment_mode ) {
	// Show the ajax loader
	jQuery( '.edd-cart-ajax' ).show();
	jQuery( '#edd_purchase_form_wrap' ).html( '<span class="edd-loading-ajax edd-loading"></span>' );

	const nonce = document.getElementById( 'edd-gateway-' + payment_mode ).getAttribute( 'data-' + payment_mode + '-nonce' );
	let url = edd_scripts.ajaxurl;

	if ( url.indexOf( '?' ) > 0 ) {
		url = url + '&';
	} else {
		url = url + '?';
	}

	url = url + 'payment-mode=' + payment_mode;

	jQuery.post( url, { action: 'edd_load_gateway', edd_payment_mode: payment_mode, nonce: nonce, current_page: edd_scripts.current_page },
		function( response ) {
			jQuery( '#edd_purchase_form_wrap' ).html( response );
			jQuery( 'body' ).trigger( 'edd_gateway_loaded', [ payment_mode ] );
		}
	);
}

// Backwards compatibility. Assign function to global namespace.
window.edd_load_gateway = edd_load_gateway;
