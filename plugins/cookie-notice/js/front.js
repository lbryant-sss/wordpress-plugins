// CustomEvent polyfil for IE support
( function() {

	if ( typeof window.CustomEvent === "function" )
		return false;

	function CustomEvent( event, params ) {
		params = params || { bubbles: false, cancelable: false, detail: undefined };

		var evt = document.createEvent( 'CustomEvent' );

		evt.initCustomEvent( event, params.bubbles, params.cancelable, params.detail );

		return evt;
	}

	CustomEvent.prototype = window.Event.prototype;

	window.CustomEvent = CustomEvent;
} )();

// ClassList polyfil for IE/Safari support
( function() {
	var regExp = function ( name ) {
		return new RegExp( '(^| )' + name + '( |$)' );
	};

	var forEach = function ( list, fn, scope ) {
		for ( var i = 0; i < list.length; i++ ) {
			fn.call( scope, list[i] );
		}
	};

	function ClassList( element ) {
		this.element = element;
	}

	ClassList.prototype = {
		add: function() {
			forEach( arguments, function ( name ) {
				if ( !this.contains( name ) ) {
					this.element.className += this.element.className.length > 0 ? ' ' + name : name;
				}
			}, this );
		},
		remove: function() {
			forEach( arguments, function ( name ) {
				this.element.className =
					this.element.className.replace( regExp( name ), '' );
			}, this );
		},
		toggle: function ( name ) {
			return this.contains( name )
				? ( this.remove( name ), false ) : ( this.add( name ), true );
		},
		contains: function ( name ) {
			return regExp( name ).test( this.element.className );
		},
		// bonus..
		replace: function ( oldName, newName ) {
			this.remove( oldName ), this.add( newName );
		}
	};

	// IE8/9, Safari
	if ( !( 'classList' in Element.prototype ) ) {
		Object.defineProperty( Element.prototype, 'classList', {
			get: function() {
				return new ClassList( this );
			}
		} );
	}

	if ( window.DOMTokenList && DOMTokenList.prototype.replace == null )
		DOMTokenList.prototype.replace = ClassList.prototype.replace;
} )();

// cookieNotice
( function ( window, document, undefined ) {

	var cookieNotice = new function() {
		// cookie status
		this.cookiesAccepted = null;

		// notice container
		this.noticeContainer = null;

		// set cookie value
		this.setStatus = function ( cookieValue ) {
			var _this = this;
			var cookieDomain = '';
			var cookiePath = '';
			var date = new Date();
			var laterDate = new Date();

			// remove listening to scroll event
			if ( cnArgs.onScroll )
				window.removeEventListener( 'scroll', this.handleScroll );

			// set cookie type and expiry time in seconds
			if ( cookieValue === 'accept' ) {
				cookieValue = 'true';
				laterDate.setTime( parseInt( date.getTime() ) + parseInt( cnArgs.cookieTime ) * 1000 );
			} else {
				cookieValue = 'false';
				laterDate.setTime( parseInt( date.getTime() ) + parseInt( cnArgs.cookieTimeRejected ) * 1000 );
			}

			if ( cnArgs.globalCookie )
				cookieDomain = this.getDomain( document.location.hostname );

			// get domain path in localhost
			if ( document.location.hostname === 'localhost' )
				cookiePath = document.location.pathname.split( '/' )[1];

			var secureValue = '';

			if ( document.location.protocol === 'https:' )
				secureValue = ';secure';

			// set cookie
			document.cookie = cnArgs.cookieName + '=' + cookieValue + ';expires=' + laterDate.toUTCString() + ';path=/' + cookiePath + ';domain=' + cookieDomain + secureValue;

			// update global status
			this.cookiesAccepted = cookieValue === 'true';

			// trigger custom event
			var event = new CustomEvent(
				'setCookieNotice',
				{
					detail: {
						value: cookieValue,
						time: date,
						expires: laterDate,
						data: cnArgs
					}
				}
			);

			document.dispatchEvent( event );

			this.setBodyClass( [ 'cookies-set', cookieValue === 'true' ? 'cookies-accepted' : 'cookies-refused' ] );

			this.hideCookieNotice();

			// show revoke notice if enabled
			if ( cnArgs.revokeCookiesOpt === 'automatic' ) {
				// show cookie notice after the revoke is hidden
				this.noticeContainer.addEventListener( 'animationend', function handler() {
					_this.noticeContainer.removeEventListener( 'animationend', handler );
					_this.showRevokeNotice();
				} );
				this.noticeContainer.addEventListener( 'webkitAnimationEnd', function handler() {
					_this.noticeContainer.removeEventListener( 'webkitAnimationEnd', handler );
					_this.showRevokeNotice();
				} );
			}

			// redirect?
			if ( cnArgs.redirection && ( ( cookieValue === 'true' && this.cookiesAccepted === null ) || ( cookieValue !== this.cookiesAccepted && this.cookiesAccepted !== null ) ) ) {
				var url = window.location.protocol + '//',
					hostname = window.location.host + '/' + window.location.pathname;

				// is cache enabled?
				if ( cnArgs.cache ) {
					url = url + hostname.replace( '//', '/' ) + ( window.location.search === '' ? '?' : window.location.search + '&' ) + 'cn-reloaded=1' + window.location.hash;

					window.location.href = url;
				} else {
					url = url + hostname.replace( '//', '/' ) + window.location.search + window.location.hash;

					window.location.reload( true );
				}

				return;
			}
		};

		// get domain
		this.getDomain = function( url ) {
			var regex = new RegExp( /https?:\/\// );

			if ( ! regex.test( url ) )
				url = 'http://' + url;

			var parts = new URL( url ).hostname.split( '.' );

			return parts.slice( 0 ).slice( -( parts.length === 4 ? 3 : 2 ) ).join( '.' );
		}

		// get cookie value
		this.getStatus = function ( bool ) {
			var value = "; " + document.cookie,
				parts = value.split( '; cookie_notice_accepted=' );

			if ( parts.length === 2 ) {
				var val = parts.pop().split( ';' ).shift();

				if ( bool )
					return val === 'true';
				else
					return val;
			} else
				return null;
		};

		// display cookie notice
		this.showCookieNotice = function() {
			var _this = this;

			// trigger custom event
			var event = new CustomEvent(
				'showCookieNotice',
				{
					detail: {
						data: cnArgs
					}
				}
			);

			document.dispatchEvent( event );

			this.noticeContainer.classList.remove( 'cookie-notice-hidden' );
			this.noticeContainer.classList.add( 'cn-animated' );
			this.noticeContainer.classList.add( 'cookie-notice-visible' );

			// detect animation
			this.noticeContainer.addEventListener( 'animationend', function handler() {
				_this.noticeContainer.removeEventListener( 'animationend', handler );
				_this.noticeContainer.classList.remove( 'cn-animated' );
			} );
			this.noticeContainer.addEventListener( 'webkitAnimationEnd', function handler() {
				_this.noticeContainer.removeEventListener( 'webkitAnimationEnd', handler );
				_this.noticeContainer.classList.remove( 'cn-animated' );
			} );
		};

		// hide cookie notice
		this.hideCookieNotice = function() {
			var _this = this;

			// trigger custom event
			var event = new CustomEvent(
				'hideCookieNotice',
				{
					detail: {
						data: cnArgs
					}
				}
			);

			document.dispatchEvent( event );

			this.noticeContainer.classList.add( 'cn-animated' );
			this.noticeContainer.classList.remove( 'cookie-notice-visible' );

			// detect animation
			this.noticeContainer.addEventListener( 'animationend', function handler() {
				_this.noticeContainer.removeEventListener( 'animationend', handler );
				_this.noticeContainer.classList.remove( 'cn-animated' );
				_this.noticeContainer.classList.add( 'cookie-notice-hidden' );
			} );
			this.noticeContainer.addEventListener( 'webkitAnimationEnd', function handler() {
				_this.noticeContainer.removeEventListener( 'webkitAnimationEnd', handler );
				_this.noticeContainer.classList.remove( 'cn-animated' );
				_this.noticeContainer.classList.add( 'cookie-notice-hidden' );
			} );
		};

		// display revoke notice
		this.showRevokeNotice = function() {
			var _this = this;

			// trigger custom event
			var event = new CustomEvent(
				'showRevokeNotice',
				{
					detail: {
						data: cnArgs
					}
				}
			);

			document.dispatchEvent( event );

			this.noticeContainer.classList.remove( 'cookie-revoke-hidden' );
			this.noticeContainer.classList.add( 'cn-animated' );
			this.noticeContainer.classList.add( 'cookie-revoke-visible' );

			// detect animation
			this.noticeContainer.addEventListener( 'animationend', function handler() {
				_this.noticeContainer.removeEventListener( 'animationend', handler );
				_this.noticeContainer.classList.remove( 'cn-animated' );
			} );
			this.noticeContainer.addEventListener( 'webkitAnimationEnd', function handler() {
				_this.noticeContainer.removeEventListener( 'webkitAnimationEnd', handler );
				_this.noticeContainer.classList.remove( 'cn-animated' );
			} );
		};

		// hide revoke notice
		this.hideRevokeNotice = function() {
			var _this = this;

			// trigger custom event
			var event = new CustomEvent(
				'hideRevokeNotice',
				{
					detail: {
						data: cnArgs
					}
				}
			);

			document.dispatchEvent( event );

			this.noticeContainer.classList.add( 'cn-animated' );
			this.noticeContainer.classList.remove( 'cookie-revoke-visible' );

			// detect animation
			this.noticeContainer.addEventListener( 'animationend', function handler() {
				_this.noticeContainer.removeEventListener( 'animationend', handler );
				_this.noticeContainer.classList.remove( 'cn-animated' );
				_this.noticeContainer.classList.add( 'cookie-revoke-hidden' );
			} );
			this.noticeContainer.addEventListener( 'webkitAnimationEnd', function handler() {
				_this.noticeContainer.removeEventListener( 'webkitAnimationEnd', handler );
				_this.noticeContainer.classList.remove( 'cn-animated' );
				_this.noticeContainer.classList.add( 'cookie-revoke-hidden' );
			} );
		};

		// change body classes
		this.setBodyClass = function ( classes ) {
			// remove body classes
			document.body.classList.remove( 'cookies-revoke' );
			document.body.classList.remove( 'cookies-accepted' );
			document.body.classList.remove( 'cookies-refused' );
			document.body.classList.remove( 'cookies-set' );
			document.body.classList.remove( 'cookies-not-set' );

			// add body classes
			for ( var i = 0; i < classes.length; i++ ) {
				document.body.classList.add( classes[i] );
			}
		};

		// handle mouse scrolling
		this.handleScroll = function() {
			var scrollTop = window.pageYOffset || ( document.documentElement || document.body.parentNode || document.body ).scrollTop

			// accept cookie
			if ( scrollTop > parseInt( cnArgs.onScrollOffset ) )
				this.setStatus( 'accept' );
		};

		// cross browser compatible closest function
		this.getClosest = function ( elem, selector ) {
			// element.matches() polyfill
			if ( !Element.prototype.matches ) {
				Element.prototype.matches =
					Element.prototype.matchesSelector ||
					Element.prototype.mozMatchesSelector ||
					Element.prototype.msMatchesSelector ||
					Element.prototype.oMatchesSelector ||
					Element.prototype.webkitMatchesSelector ||
					function ( s ) {
						var matches = ( this.document || this.ownerDocument ).querySelectorAll( s ),
							i = matches.length;
						while ( --i >= 0 && matches.item( i ) !== this ) {
						}
						return i > -1;
					};
			}

			// get the closest matching element
			for ( ; elem && elem !== document; elem = elem.parentNode ) {
				if ( elem.matches( selector ) )
					return elem;
			}

			return null;
		};

		// check if displaye in an iframe
		this.inIframe = function() {
			try {
				return window.self !== window.top;
			} catch (e) {
				return true;
			}
		}

		// initialize
		this.init = function() {
			var _this = this;

			// bail if in iframe
			if ( this.inIframe() === true )
				return;

			this.cookiesAccepted = this.getStatus( true );
			this.noticeContainer = document.getElementById( 'cookie-notice' );

			// no container?
			if ( ! this.noticeContainer )
				return;

			var cookieButtons = document.getElementsByClassName( 'cn-set-cookie' ),
				revokeButtons = document.getElementsByClassName( 'cn-revoke-cookie' ),
				linkButton = document.getElementById( 'cn-more-info' ),
				closeButton = document.getElementById( 'cn-close-notice' );

			// add effect class
			this.noticeContainer.classList.add( 'cn-effect-' + cnArgs.hideEffect );

			// check cookies status
			if ( this.cookiesAccepted === null ) {
				// handle on scroll
				if ( cnArgs.onScroll )
					window.addEventListener( 'scroll', function ( e ) {
						_this.handleScroll();
					} );

				// handle on click
				if ( cnArgs.onClick )
					window.addEventListener( 'click', function ( e ) {
						var outerContainer = _this.getClosest( e.target, '#cookie-notice' );

						// accept notice if clicked element is not inside the container
						if ( outerContainer === null )
							_this.setStatus( 'accept' );
					}, true );

				this.setBodyClass( [ 'cookies-not-set' ] );

				// show cookie notice
				this.showCookieNotice();
			} else {
				this.setBodyClass( [ 'cookies-set', this.cookiesAccepted === true ? 'cookies-accepted' : 'cookies-refused' ] );

				// show revoke notice if enabled
				if ( cnArgs.revokeCookies && cnArgs.revokeCookiesOpt === 'automatic' )
					this.showRevokeNotice();
			}

			// handle cookie buttons click
			for ( var i = 0; i < cookieButtons.length; i++ ) {
				cookieButtons[i].addEventListener( 'click', function ( e ) {
					e.preventDefault();
					// Chrome double click event fix
					e.stopPropagation();

					_this.setStatus( this.dataset.cookieSet );
				} );
			}
			
			// handle link button
			if ( linkButton !== null ) {
				linkButton.addEventListener( 'click', function ( e ) {
					e.preventDefault();
					// Chrome double click event fix
					e.stopPropagation();
					
					console.log( this );
					
					var linkUrl = this.dataset.linkUrl;
					var linkTarget = this.dataset.linkTarget;

					window.open( linkUrl, linkTarget );
				} );
			}

			// handle close button
			if ( closeButton !== null ) {
				closeButton.addEventListener( 'click', function ( e ) {
					e.preventDefault();
					// Chrome double click event fix
					e.stopPropagation();

					_this.setStatus( 'reject' );
				} );
			}

			// handle revoke buttons click
			for ( var i = 0; i < revokeButtons.length; i++ ) {
				revokeButtons[i].addEventListener( 'click', function ( e ) {
					e.preventDefault();

					// hide revoke notice
					if ( _this.noticeContainer.classList.contains( 'cookie-revoke-visible' ) ) {
						_this.hideRevokeNotice();

						// show cookie notice after the revoke is hidden
						_this.noticeContainer.addEventListener( 'animationend', function handler() {
							_this.noticeContainer.removeEventListener( 'animationend', handler );
							_this.showCookieNotice();
						} );
						_this.noticeContainer.addEventListener( 'webkitAnimationEnd', function handler() {
							_this.noticeContainer.removeEventListener( 'webkitAnimationEnd', handler );
							_this.showCookieNotice();
						} );
						// show cookie notice
					} else if ( _this.noticeContainer.classList.contains( 'cookie-notice-hidden' ) && _this.noticeContainer.classList.contains( 'cookie-revoke-hidden' ) )
						_this.showCookieNotice();
				} );
			}
		};
	}

	// initialize plugin
	window.addEventListener( 'load', function() {
		cookieNotice.init();
	}, false );

} )( window, document, undefined );