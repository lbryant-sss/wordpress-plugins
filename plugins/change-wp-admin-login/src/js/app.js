
	window.vue = require( 'vue' );
	window.vueRouter = require( 'vue-router' );
	window.Datatable = require( 'datatables.net-dt' );
	window.axios = require( 'axios' );

	window.axios = axios.create({
		baseURL: aio_login__app_object.rest_url,
		timeout: 1000 * 10 * 6,
		headers: {
			'X-WP-Nonce': aio_login__app_object.nonce,
		},
	});



	window.aio_login__object = {
		tabs: aio_login__app_object.tabs,
		pages: [],
		routes: [],
	};

	try {
		window.aio_login__object['qrcode'] = window.aioLoginQRcode
	} catch ( e ) {}

	var tabs = Object.values( aio_login__object.tabs );
	var current_location = window.location.href;
	current_location = new URL( current_location );
	current_location = new URLSearchParams( current_location.search );
	var current_tab = current_location.get( 'tab' );

	if ( null == current_tab ) {
		current_tab = 'dashboard';
	}

	tabs.forEach( function( tab ) {
		if ( current_tab === tab.slug ) {
			aio_login__object.pages.push( tab );
		}
	} );

	aio_login__object.pages.forEach( function( page ) {
		if ( null == page['sub-tabs'] ) {
			return;
		}

		var sub_tabs = Object.values( page['sub-tabs'] );
		sub_tabs.forEach( function( v, i ) {
			if ( ! i ) {
				window.aio_login__object.routes.push( {
					path: '/',
					redirect: to => ( {
						path: '/' + v.slug,
					} ),
				} );
			}

			window.aio_login__object.routes.push( {
				path: '/' + v.slug,
				my_slug: v.slug,
			} )
		} );
	} );

	if ( window.aio_login__object.routes.length ) {
		window.aio_login__object.routes.forEach( function( route, i ) {
			if ( 'change-login-url' === route.my_slug ) {
				window.aio_login__object.routes[ i ].component = require( './pages/change-login-url.vue' ).default;
			}

			if ( 'limit-login-attempts' === route.my_slug ) {
				window.aio_login__object.routes[ i ].component = require( './pages/limit-login-attempts.vue' ).default;
			}

			if ( 'block-ip-addresses' === route.my_slug ) {
				window.aio_login__object.routes[ i ].component = require( './pages/block-ip-addresses.vue' ).default;
			}

			if ( 'disable-common-usernames' === route.my_slug ) {
				window.aio_login__object.routes[ i ].component = require( './pages/disable-common-usernames.vue' ).default;
			}

			if ( 'password-strenght-checker' === route.my_slug ) {
				window.aio_login__object.routes[ i ].component = require( './pages/password-strenght-checker.vue' ).default;
			}

			if ( 'lockouts' === route.my_slug ) {
				window.aio_login__object.routes[ i ].component = require( './pages/lockouts.vue' ).default;
			}

			if ( 'failed-logins' === route.my_slug ) {
				window.aio_login__object.routes[i].component = require( './pages/failed-logins.vue' ).default;
			}

			if ( 'grecaptcha' === route.my_slug ) {
				window.aio_login__object.routes[i].component = require( './pages/grecaptcha.vue' ).default;
			}

			if ( '2fa' === route.my_slug ) {
				window.aio_login__object.routes[i].component = require( './pages/2fa.vue' ).default;
			}

			if ( 'logo' === route.my_slug ) {
				window.aio_login__object.routes[i].component = require( './pages/logo.vue' ).default;
			}

			if ( 'background' === route.my_slug ) {
				window.aio_login__object.routes[i].component = require( './pages/background.vue' ).default;
			}

			if ( 'custom-css' === route.my_slug ) {
				window.aio_login__object.routes[i].component = require( './pages/custom-css.vue' ).default;
			}

			if ( 'templates' === route.my_slug ) {
				window.aio_login__object.routes[i].component = require( './pages/templates.vue' ).default;
			}

			if ( 'social-login' === route.my_slug ) {
				window.aio_login__object.routes[i].component = require( './pages/social-login.vue' ).default;
			}
		} );
	}


	function aio_login_set_menu_position() {
		let current_url = window.location.href;
		let url = new URL( current_url );
		let searchParams = url.searchParams;
		let page, tab;
		page = searchParams.get( 'page' );
		tab = searchParams.get( 'tab' );

		if ('login-protection' === tab) {
			aio_login_active_menu(3);
		} else if ('activity-log' === tab) {
			aio_login_active_menu(4);
		} else if ('security' === tab) {
			aio_login_active_menu(5);
		} else if ('temp-access' === tab) {
			aio_login_active_menu(6);
		} else if ('social-login' === tab) {
			aio_login_active_menu(7);
		} else if ('customization' === tab) {
			aio_login_active_menu(8);
		} else if ('getpro' === tab) {
			aio_login_active_menu(9);
		} else {
			aio_login_active_menu(2);
		}
	}

	function aio_login_active_menu( $index ) {
		jQuery( `#toplevel_page_aio-login > ul > li` ).removeClass( 'current' );
		jQuery( `#toplevel_page_aio-login > ul > li:nth-child( ${$index} )` ).addClass( 'current' );
	}

	aio_login_set_menu_position();

	require( './vue' );


/**
 * This code is auto-generated and should not be modified
 */