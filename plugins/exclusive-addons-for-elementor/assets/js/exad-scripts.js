(function ($) {
    "use strict";

    var editMode = false;
    
// accordion script starts

var exclusiveAccordion = function( $scope, $ ) {
    var accordionTitle = $scope.find( '.exad-accordion-title' );

    // Open default actived tab
    accordionTitle.each(function(){
        if($(this).hasClass( 'active-default' ) ){
            $(this).addClass( 'active' );
            $(this).next( '.exad-accordion-content' ).slideDown();
        }
    });
    
    // Remove multiple click event for nested accordion
    accordionTitle.unbind( 'click' );

    //$accordionWrapper.children('.exad-accordion-content').first().show();
    accordionTitle.click(function(e){
        e.preventDefault();
        if ($(this).hasClass( 'active' ) ) {
            $(this).removeClass( 'active' );
            $(this).next().slideUp( 400 );
        } else {
            $(this).parent().parent().find( '.exad-accordion-title' ).removeClass( 'active' );
            $(this).parent().parent().find( '.exad-accordion-content' ).slideUp( 400 );
            $(this).toggleClass( 'active' );
            $(this).next().slideToggle( 400 );
        }	
    } );        
}

// accordion script ends

//alert script starts

var exclusiveAlert = function( $scope, $ ) {
    var alertClose = $scope.find( '[data-alert]' ).eq(0);
    alertClose.each( function(index){
        var alert = $(this);
        alert.find( '.exad-alert-element-dismiss-icon' ).click(function(e){
            e.preventDefault();
            alert.fadeOut(500);
        });
        alert.find( '.exad-alert-element-dismiss-button' ).click(function(e){
            e.preventDefault();
            alert.fadeOut(500);
        });
    });
}

//alert script ends

// animated text script starts

var exclusiveAnimatedText = function( $scope, $ ) {
  
	var animatedWrapper = $scope.find( '.exad-typed-strings' ).eq(0),
	animateSelector     = animatedWrapper.find( '.exad-animated-text-animated-heading' ),
	animationType       = animatedWrapper.data( 'heading_animation' ),
	animationStyle      = animatedWrapper.data( 'animation_style' ),
	animationSpeed      = animatedWrapper.data( 'animation_speed' ),
	typeSpeed           = animatedWrapper.data( 'type_speed' ),
	startDelay          = animatedWrapper.data( 'start_delay' ),
	backTypeSpeed       = animatedWrapper.data( 'back_type_speed' ),
	backDelay           = animatedWrapper.data( 'back_delay' ),
	loop                = animatedWrapper.data( 'loop' ) ? true : false,
	showCursor          = animatedWrapper.data( 'show_cursor' ) ? true : false,
	fadeOut             = animatedWrapper.data( 'fade_out' ) ? true : false,
	smartBackspace      = animatedWrapper.data( 'smart_backspace' ) ? true : false,	
	id                  = animateSelector.attr('id');

	if ( 'function' === typeof Typed ) {
		if( 'exad-typed-animation' === animationType ){
			var typed = new Typed( '#'+id, {
				strings: animatedWrapper.data('type_string'),
				loop: loop,
				typeSpeed: typeSpeed,
				backSpeed: backTypeSpeed,
				showCursor : showCursor,
				fadeOut : fadeOut,
				smartBackspace : smartBackspace,
				startDelay : startDelay,
				backDelay : backDelay,
				contentType: 'null'
			});
		}
	}


 	if ( $.isFunction( $.fn.Morphext ) ) {
		if( 'exad-morphed-animation' === animationType ){
			$( animateSelector ).Morphext({
				animation: animationStyle,
				speed: animationSpeed
			});
		}
	}
}

// animated text script ends

// exclusive Button script starts

var exclusiveButton = function ( $scope, $ ) {
    // position on hover a button in button style seven
    var mouseHoverEffect8 = $scope.find( '.effect-8.mouse-hover-effect' ).eq(0);

    mouseHoverEffect8.on( 'mouseenter', function (e) {
        var parentOffset = $(this).offset(),
        relX = e.pageX - parentOffset.left,
        relY = e.pageY - parentOffset.top;
        $(this).find( '.effect-8-position' ).css({
            top: relY,
            left: relX
        })
    } );

    mouseHoverEffect8.on( 'mouseout', function (e) {
        var parentOffset = $(this).offset(),
        relX = e.pageX - parentOffset.left,
        relY = e.pageY - parentOffset.top;
        $(this).find( '.effect-8-position' ).css({
            top: relY,
            left: relX
        } )
    } );
    // position on hover a button in button style seven
}

// exclusive Button script ends

// Corona script starts
var exclusiveCorona = function ($scope, $) {
	var exadCoronaWrapper = $scope.find('.exad-corona').eq(0);
	var searchData = exadCoronaWrapper.find('#exad_search_data');
	var dataTtableRow = exadCoronaWrapper.find('#data_table .data_table_row');
	var continentBtn = exadCoronaWrapper.find('#exad-covid-filters .exad-covid-continent-btn');
	var parentClass = exadCoronaWrapper.find('.exad-corona-table-heading.yes th');
	searchData.on("keyup", function () {
		var value = $(this).val().toLowerCase();
		dataTtableRow.filter(function () {
			$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
		});
	});
	continentBtn.click(function () {
		if (this.id == 'all') {
			dataTtableRow.fadeIn(450);
		} else {
			var el = $('.' + this.id).fadeIn(450);
			dataTtableRow.not(el).hide();
		}
		continentBtn.removeClass('active');
		$(this).addClass('active');
	});
	if ( parentClass.length > 0 ) {
		var parent = document.querySelector('.exad-corona-table-heading.yes th').parentElement;
		while (parent) {
			var hasOverflow = getComputedStyle(parent).overflow;
			if (hasOverflow !== 'visible') {
				parent.style.overflow = "visible"
			}
			parent = parent.parentElement;
		}
	}
}
// Corona script ends
// countdown timer script starts

var exclusiveCountdownTimer = function ( $scope, $ ) {
    var countdownTimerWrapper = $scope.find( '[data-countdown]' ).eq(0);

    if ( 'undefined' !== typeof countdownTimerWrapper && null !== countdownTimerWrapper ) {
        var $this   = countdownTimerWrapper,
        finalDate   = $this.data( 'countdown' ),
        day         = $this.data( 'day' ),
        hours       = $this.data( 'hours' ),
        minutes     = $this.data( 'minutes' ),
        seconds     = $this.data( 'seconds' ),
        expiredText = $this.data( 'expired-text' );
		
        let rnsc = /[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
		
        day = day.replace( rnsc, '' );
        hours = hours.replace( rnsc, '' );
        minutes = minutes.replace( rnsc, '' );
        seconds = seconds.replace( rnsc, '' );

        if ( $.isFunction( $.fn.countdown ) ) {
            $this.countdown( finalDate, function ( event ) {
                $( this ).html( event.strftime(' ' +
                    '<div class="exad-countdown-container"><div class="exad-countdown-timer-wrapper"><span class="exad-countdown-count">%-D </span><span class="exad-countdown-title">' + day + '</span></div></div>' +
                    '<div class="exad-countdown-container"><div class="exad-countdown-timer-wrapper"><span class="exad-countdown-count">%H </span><span class="exad-countdown-title">' + hours + '</span></div></div>' +
                    '<div class="exad-countdown-container"><div class="exad-countdown-timer-wrapper"><span class="exad-countdown-count">%M </span><span class="exad-countdown-title">' + minutes + '</span></div></div>' +
                    '<div class="exad-countdown-container"><div class="exad-countdown-timer-wrapper"><span class="exad-countdown-count">%S </span><span class="exad-countdown-title">' + seconds + '</span></div></div>'));
            } ).on( 'finish.countdown', function (event) {
                let tagP = $( '<p>' ).addClass('message');
                tagP.text( expiredText );
                $(this).html( tagP );
            } );
        }
    }
}

// countdown timer script ends

/* Facebook Feed */

var exadFacebookFeed = function($scope) {
    var button = $scope.find('.exad-facebook-load-more');
    var facebook_wrap = $scope.find('.exad-facebook-feed-wrapper');
    
    button.on("click", function(e) {
        e.preventDefault();
        var $self = $(this),
            query_settings = $self.data("settings"),
            total = $self.data("total"),
            items = $scope.find('.exad-facebook-feed-item').length;
        $.ajax({
            url: exad_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: "exad_facebook_feed_action",
                security: exad_ajax_object.nonce,
                query_settings: query_settings,
                loaded_item: items,
            },
            success: function(response) {
                if(total > items){
                    $(response).appendTo(facebook_wrap);
                } else {
                    $self.text('All Loaded').addClass('loaded');
                    setTimeout( function(){
                        $self.css({"display": "none"});
                    },3000);
                }
            },
            error: function(error) {}
        });
    });
};

// filterable post script starts

var exclusiveFilterablePost = function( $scope, $ ) {
	
        if ( $.isFunction( $.fn.isotope ) ) {
            var exadGetGallery       = $scope.find( '.filterable-post-container' ).eq( 0 ),
            currentFilteredId         = '#' + exadGetGallery.attr( 'id' ),
            $container             = $scope.find( currentFilteredId ).eq( 0 );
            
            var filterableMainWrapper = $scope.find( '.exad-filterable-items' ).eq( 0 ),
            filterableItem            = '#' + filterableMainWrapper.attr( 'id' );

            $container.isotope({
                filter: '*',
                layoutMode: 'fitRows',
                animationOptions: {
                    queue: true
                }
            });

            $( filterableItem + ' .exad-filterable-menu li' ).click( function() {
                $( filterableItem + ' .exad-filterable-menu li.current' ).removeClass( 'current' );
                $( this ).addClass( 'current' );
         
                var selector = $( this ).attr( 'data-filter' );
                $container.isotope( {
                    filter: selector,
                    layoutMode: 'masonry',
                    getSortData: {
                        name: '.name',
                        symbol: '.symbol',
                        number: '.number parseInt',
                        category: '[data-category]',
                        weight: function( itemElem ) {
                            var weight = $( itemElem ).find( '.weight' ).text();
                            return parseFloat( weight.replace( /[\(\)]/g, '' ) );
                        }
                    },
                    animationOptions: {
                        queue: true
                    },
                    masonry: {
                        columnWidth: 1
                    }
                 } );
                 return false;
            } ); 

            $container.imagesLoaded().progress( function() {
                $container.isotope('layout');
            });
        }
}

// filterable post script ends


// filterable gallery script starts

var exclusiveFilterableGallery = function( $scope, $ ) {

        if ( $.isFunction( $.fn.isotope ) ) {
            var exadGetGallery       = $scope.find( '.exad-gallery-element' ).eq( 0 ),
            currentGalleryId         = '#' + exadGetGallery.attr( 'id' ),
            $container             = $scope.find( currentGalleryId ).eq( 0 );
            
            var galleryMainWrapper = $scope.find( '.exad-gallery-items' ).eq( 0 ),
            galleryItem            = '#' + galleryMainWrapper.attr( 'id' );

            $container.isotope({
                filter: '*',
                animationOptions: {
                    queue: true
                }
            });

            $( galleryItem + ' .exad-gallery-menu button' ).click( function() {
                $( galleryItem + ' .exad-gallery-menu button.current' ).removeClass( 'current' );
                $( this ).addClass( 'current' );
         
                var selector = $( this ).attr( 'data-filter' );
                $container.isotope( {
                    filter: selector,
                    layoutMode: 'fitRows',
                    getSortData: {
                        name: '.name',
                        symbol: '.symbol',
                        number: '.number parseInt',
                        category: '[data-category]',
                        weight: function( itemElem ) {
                            var weight = $( itemElem ).find( '.weight' ).text();
                            return parseFloat( weight.replace( /[\(\)]/g, '' ) );
                        }
                    },
                    animationOptions: {
                        queue: true
                    }
                 } );
                 return false;
            } ); 
        }
}

// filterable gallery script ends

// google maps script starts

var exclusiveGoogleMaps = function($scope, $) {

    if ( $.isFunction($.fn.gmap3) ) {
        var googleMaps         = $scope.find( '.exad-google-maps' ).eq(0),
        latitude               = googleMaps.data( 'exad-lat' ),
        longitude              = googleMaps.data( 'exad-lng' ),
        mapTheme               = googleMaps.data( 'exad-theme' ),
        mapZoom                = googleMaps.data( 'exad-zoom' ),
        mapAddress             = googleMaps.data( 'exad-address' ),
        map_streeview_control  = googleMaps.data( 'exad-streeview-control' ),
        map_type_control       = googleMaps.data( 'exad-type-control' ),
        map_zoom_control       = googleMaps.data( 'exad-zoom-control' ),
        map_fullscreen_control = googleMaps.data( 'exad-fullscreen-control' ),
        map_scroll_zoom        = googleMaps.data( 'exad-scroll-zoom' ),   
        center                 = [latitude, longitude],
        address                = false;

        googleMaps.gmap3({
            center: center,
            address: address,
            zoom: mapZoom,
            streetViewControl: map_streeview_control,
            mapTypeControl: map_type_control,
            zoomControl: map_zoom_control,
            fullscreenControl: map_fullscreen_control,
            scrollwheel: map_scroll_zoom,
            mapTypeId: mapTheme,
        }).styledmaptype(
            "standard",
            [],
            {name: "standard"}
        ).styledmaptype(
            "retro",
            [{"elementType":"geometry","stylers":[{"color":"#ebe3cd"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#523735"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#f5f1e6"}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#c9b2a6"}]},{"featureType":"administrative.land_parcel","elementType":"geometry.stroke","stylers":[{"color":"#dcd2be"}]},{"featureType":"administrative.land_parcel","elementType":"labels.text.fill","stylers":[{"color":"#ae9e90"}]},{"featureType":"landscape.natural","elementType":"geometry","stylers":[{"color":"#dfd2ae"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#dfd2ae"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#93817c"}]},{"featureType":"poi.park","elementType":"geometry.fill","stylers":[{"color":"#a5b076"}]},{"featureType":"poi.park","elementType":"labels.text.fill","stylers":[{"color":"#447530"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#f5f1e6"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#fdfcf8"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#f8c967"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#e9bc62"}]},{"featureType":"road.highway.controlled_access","elementType":"geometry","stylers":[{"color":"#e98d58"}]},{"featureType":"road.highway.controlled_access","elementType":"geometry.stroke","stylers":[{"color":"#db8555"}]},{"featureType":"road.local","elementType":"labels.text.fill","stylers":[{"color":"#806b63"}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"color":"#dfd2ae"}]},{"featureType":"transit.line","elementType":"labels.text.fill","stylers":[{"color":"#8f7d77"}]},{"featureType":"transit.line","elementType":"labels.text.stroke","stylers":[{"color":"#ebe3cd"}]},{"featureType":"transit.station","elementType":"geometry","stylers":[{"color":"#dfd2ae"}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#b9d3c2"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#92998d"}]}],
            {name: "retro"}
        ).styledmaptype(
            "silver",
            [{"elementType":"geometry","stylers":[{"color":"#f5f5f5"}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#616161"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#f5f5f5"}]},{"featureType":"administrative.land_parcel","elementType":"labels.text.fill","stylers":[{"color":"#bdbdbd"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#eeeeee"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#e5e5e5"}]},{"featureType":"poi.park","elementType":"labels.text.fill","stylers":[{"color":"#9e9e9e"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#ffffff"}]},{"featureType":"road.arterial","elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#dadada"}]},{"featureType":"road.highway","elementType":"labels.text.fill","stylers":[{"color":"#616161"}]},{"featureType":"road.local","elementType":"labels.text.fill","stylers":[{"color":"#9e9e9e"}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"color":"#e5e5e5"}]},{"featureType":"transit.station","elementType":"geometry","stylers":[{"color":"#eeeeee"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#c9c9c9"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#9e9e9e"}]}],
            {name: "silver"}
        ).styledmaptype(
            "dark",
            [{"elementType":"geometry","stylers":[{"color":"#212121"}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#212121"}]},{"featureType":"administrative","elementType":"geometry","stylers":[{"color":"#757575"}]},{"featureType":"administrative.country","elementType":"labels.text.fill","stylers":[{"color":"#9e9e9e"}]},{"featureType":"administrative.land_parcel","stylers":[{"visibility":"off"}]},{"featureType":"administrative.locality","elementType":"labels.text.fill","stylers":[{"color":"#bdbdbd"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#181818"}]},{"featureType":"poi.park","elementType":"labels.text.fill","stylers":[{"color":"#616161"}]},{"featureType":"poi.park","elementType":"labels.text.stroke","stylers":[{"color":"#1b1b1b"}]},{"featureType":"road","elementType":"geometry.fill","stylers":[{"color":"#2c2c2c"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#8a8a8a"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#373737"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#3c3c3c"}]},{"featureType":"road.highway.controlled_access","elementType":"geometry","stylers":[{"color":"#4e4e4e"}]},{"featureType":"road.local","elementType":"labels.text.fill","stylers":[{"color":"#616161"}]},{"featureType":"transit","elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#3d3d3d"}]}],
            {name: "dark"}
        ).styledmaptype(
            "night",
            [{"elementType":"geometry","stylers":[{"color":"#242f3e"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#746855"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#242f3e"}]},{"featureType":"administrative.locality","elementType":"labels.text.fill","stylers":[{"color":"#d59563"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#d59563"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#263c3f"}]},{"featureType":"poi.park","elementType":"labels.text.fill","stylers":[{"color":"#6b9a76"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#38414e"}]},{"featureType":"road","elementType":"geometry.stroke","stylers":[{"color":"#212a37"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#9ca5b3"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#746855"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#1f2835"}]},{"featureType":"road.highway","elementType":"labels.text.fill","stylers":[{"color":"#f3d19c"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#2f3948"}]},{"featureType":"transit.station","elementType":"labels.text.fill","stylers":[{"color":"#d59563"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#17263c"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#515c6d"}]},{"featureType":"water","elementType":"labels.text.stroke","stylers":[{"color":"#17263c"}]}],
            {name: "night"}
        ).styledmaptype(
            "aubergine",
            [{"elementType":"geometry","stylers":[{"color":"#1d2c4d"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#8ec3b9"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#1a3646"}]},{"featureType":"administrative.country","elementType":"geometry.stroke","stylers":[{"color":"#4b6878"}]},{"featureType":"administrative.land_parcel","elementType":"labels.text.fill","stylers":[{"color":"#64779e"}]},{"featureType":"administrative.province","elementType":"geometry.stroke","stylers":[{"color":"#4b6878"}]},{"featureType":"landscape.man_made","elementType":"geometry.stroke","stylers":[{"color":"#334e87"}]},{"featureType":"landscape.natural","elementType":"geometry","stylers":[{"color":"#023e58"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#283d6a"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#6f9ba5"}]},{"featureType":"poi","elementType":"labels.text.stroke","stylers":[{"color":"#1d2c4d"}]},{"featureType":"poi.park","elementType":"geometry.fill","stylers":[{"color":"#023e58"}]},{"featureType":"poi.park","elementType":"labels.text.fill","stylers":[{"color":"#3C7680"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#304a7d"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#98a5be"}]},{"featureType":"road","elementType":"labels.text.stroke","stylers":[{"color":"#1d2c4d"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#2c6675"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#255763"}]},{"featureType":"road.highway","elementType":"labels.text.fill","stylers":[{"color":"#b0d5ce"}]},{"featureType":"road.highway","elementType":"labels.text.stroke","stylers":[{"color":"#023e58"}]},{"featureType":"transit","elementType":"labels.text.fill","stylers":[{"color":"#98a5be"}]},{"featureType":"transit","elementType":"labels.text.stroke","stylers":[{"color":"#1d2c4d"}]},{"featureType":"transit.line","elementType":"geometry.fill","stylers":[{"color":"#283d6a"}]},{"featureType":"transit.station","elementType":"geometry","stylers":[{"color":"#3a4762"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#0e1626"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#4e6d70"}]}],
            {name: "aubergine"}
        );
    }
}

// google maps script ends


// Google Reviews Carousel
var exclusiveGoogleReviews = function( $scope, $ ) {

  var $slider = $scope.find( '.exad-google-reviews-carousel-wrapper' );
      
      if ( ! $slider.length ) {
          return;
      }

  var $sliderContainer = $slider.find('.swiper-container'),
    $settings 		 = $slider.data('settings');

  var swiper = new Swiper($sliderContainer, $settings);

  if ($settings.pauseOnHover) {
     $($sliderContainer).hover(function() {
      (this).swiper.autoplay.stop();
    }, function() {
      (this).swiper.autoplay.start();
    });
  }

};
// image comparison script starts

var exclusiveImageComparison = function($scope, $) {
    var imageComparison  = $scope.find( '.exad-image-comparision-element' ).eq(0),
    exadOrientation      = imageComparison.data( 'exad-oriantation' ),
    exadBeforeLabel      = imageComparison.data( 'exad-before_label' ),
    exadAfterLabel       = imageComparison.data( 'exad-after_label' ),
    exadDefaultOffsetPct = imageComparison.data( 'exad-default_offset_pct' ),
    exadNoOverlay        = imageComparison.data( 'exad-no_overlay' ),
    exadMoveSliderOnHover        = imageComparison.data( 'exad-move_slider_on_hover' ),
    exadMoveWithHandleOnly        = imageComparison.data( 'exad-move_with_handle_only' ),
    exadClickToMove        = imageComparison.data( 'exad-click_to_move' );
        
    if ( $.isFunction($.fn.twentytwenty) ) {    
        imageComparison.twentytwenty({
            orientation: exadOrientation,
            before_label: exadBeforeLabel,
            after_label: exadAfterLabel,
            default_offset_pct: exadDefaultOffsetPct,
            no_overlay: exadNoOverlay,
            move_slider_on_hover: exadMoveSliderOnHover,
            move_with_handle_only: exadMoveWithHandleOnly,
            click_to_move: exadClickToMove
        } );
    }
}

// image comparison script ends

// image magnifier script starts

var exclusiveImageMagnifier = function($scope, $) {

    var $magnify = $scope.find( '.exad-image-magnify' ).eq(0),
    $large       = $magnify.find( '.exad-magnify-large' ),
    $small       = $magnify.find( '.exad-magnify-small > img' );
	
	// Support lazysizes JS library (used by Wordpress plugins like EWWW Image Optimizer)
	if ( $small.hasClass('lazyload') 
		&& typeof window.lazySizesConfig !== 'undefined' ) {
		
		document.addEventListener('lazyloaded', function(e){
			
			exclusiveImageMagnifier( $scope, $ );
		});
		
		return;
	}
    
    if ( $large.hasClass('exad-image-magnified') ) {
		
		return;
    }
	
    var native_width  = 0;
    var native_height = 0;
    $large.css("background","url('" + $small.attr("src") + "') no-repeat");
    $large.addClass('exad-image-magnified');
    
    //Now the mousemove function
    $magnify.on( "mousemove", function( e ) {
        
        if(!native_width && !native_height) {
            var image_object = new Image();
            image_object.src = $small.attr("src");
            
            native_width = image_object.width;
            native_height = image_object.height;
        } else {
            var magnify_offset = $(this).offset();
            var mx = e.pageX - magnify_offset.left;
            var my = e.pageY - magnify_offset.top;
            
            //Finally the code to fade out the glass if the mouse is outside the container
            if(mx < $(this).width() && my < $(this).height() && mx > 0 && my > 0){
                $large.fadeIn(100);
            } else {
                $large.fadeOut(100);
            }
            
            if($large.is(":visible")) {
                
                var rx = Math.round(mx/$small.width()*native_width - $large.width()/2)*-1;
                var ry = Math.round(my/$small.height()*native_height - $large.height()/2)*-1;
                var bgp = rx + "px " + ry + "px";
                
                //Time to move the magnifying glass with the mouse
                var px = mx - $large.width()/2;
                var py = my - $large.height()/2;
                
                $large.css({left: px, top: py, backgroundPosition: bgp});
            }
        }
    } )
}

// image magnifier script ends


function eae_isValidAbsoluteURL( string ) {
	
	try {
		
		const newUrl = new URL( string );
		
		return newUrl.protocol !== 'javascript:';
		
	} catch (err) {
		
		return false;
	}
}

function eae_isValidURL( string ) {
	
	try {
		
		let elm = document.createElement('input');
		elm.setAttribute('type', 'url');
		elm.setAttribute('required', 'true');
		elm.value = string;
		
		return elm.validity;
		
	} catch ( err ) {

		return false;
	}
}


// Container Link JS started

var exadPageBody = $('body');
exadPageBody.on('click.onWrapperLink', '[data-exad-element-link]', function(e) {
    var $wrapper = $(this),
        data     = $wrapper.data('exad-element-link'),
        id       = $wrapper.data('id'),
        anchor   = document.createElement('a'),
        anchorReal,
        timeout;
		
    if ( 'undefined' === typeof data.url ) {
		
        return false;
    }

    let url = encodeURI( data.url )
    , validUrl = eae_isValidURL( url );
	
    if ( validUrl.badInput === false
        && validUrl.customError === false
        && validUrl.patternMismatch === false
        && validUrl.rangeOverflow === false
        && validUrl.rangeUnderflow === false
        && validUrl.stepMismatch === false
        && validUrl.tooLong === false
        && validUrl.tooShort === false
        && validUrl.typeMismatch === true
        && validUrl.valid === false
        && validUrl.valueMissing === false ) {
		
        url = document.location.href.replace('#', '') + url;
		
        validUrl = eae_isValidURL( url );
    }
	else {
		
		validUrl = eae_isValidAbsoluteURL( url );
	}
	
    if ( ( 'undefined' !== typeof validUrl.valid 
		&& validUrl.valid === false ) || validUrl === false ) {
		
		e.preventDefault();
		e.stopPropagation();
		
		return false;
    }
	
    anchor.id            = 'exad-link-anything-' + id;
    anchor.href          = url;
    anchor.target        = data.is_external ? '_blank' : '_self';
    anchor.rel           = data.nofollow ? 'nofollow noreferer' : '';
    anchor.style.display = 'none';

    document.body.appendChild(anchor);

    anchorReal = document.getElementById(anchor.id);
    anchorReal.click();

    timeout = setTimeout(function() {
        document.body.removeChild(anchorReal);
        clearTimeout(timeout);
    });
});

// Container Link JS end

// logo carousel script starts

var exclusiveLogoCarousel   = function ( $scope, $ ) {
    var logoCarouselWrapper = $scope.find( '.exad-logo-carousel-element' ).eq(0),
    slidesToShow            = logoCarouselWrapper.data( 'slidestoshow' ),
    carouselColumnTablet    = logoCarouselWrapper.data( 'slidestoshow-tablet' ),
    carouselColumnMobile    = logoCarouselWrapper.data( 'slidestoshow-mobile' ),
    slidesToScroll          = logoCarouselWrapper.data( 'slidestoscroll' ),
    carouselNav             = logoCarouselWrapper.data( 'carousel-nav' ),
    direction               = logoCarouselWrapper.data( 'direction' ),
    loop                    = undefined !== logoCarouselWrapper.data( 'loop' ) ? logoCarouselWrapper.data( 'loop' ) : false,
    autoPlay                = undefined !== logoCarouselWrapper.data( 'autoplay' ) ? logoCarouselWrapper.data( 'autoplay' ) : false,
    autoplaySpeed           = undefined !== logoCarouselWrapper.data( 'autoplayspeed' ) ? logoCarouselWrapper.data( 'autoplayspeed' ) : false,
    Smooth                  = undefined !== logoCarouselWrapper.data( 'smooth' ) ? logoCarouselWrapper.data( 'smooth' ) : false,
    SmoothSpeed             = undefined !== logoCarouselWrapper.data( 'smooth-speed' ) ? logoCarouselWrapper.data( 'smooth-speed' ) : 300;

    var arrows, dots, cssEase;

    if ( Smooth ){
        cssEase = 'linear';
        autoplaySpeed = 0;
    } else {
        cssEase = 'ease';
    }
    if ( 'both' === carouselNav ) {
        arrows = true;
        dots   = true;
    } else if ( 'arrows' === carouselNav ) {
        arrows = true;
        dots   = false;
    } else if ( 'dots' === carouselNav ) {
        arrows = false;
        dots   = true;
    } else {
        arrows = false;
        dots   = false;
    }

    if ( $.isFunction( $.fn.slick ) ) {
        logoCarouselWrapper.slick( {
            infinite: loop,
            slidesToShow: slidesToShow,
            slidesToScroll: slidesToScroll,
            autoplay: autoPlay,
            autoplaySpeed: autoplaySpeed,
            dots: dots,
            rtl: direction,
            arrows: arrows,
            speed: SmoothSpeed,
            cssEase: cssEase,
            prevArrow: '<div class="exad-logo-carousel-prev"><i class="eicon-chevron-left"></i></div>',
            nextArrow: '<div class="exad-logo-carousel-next"><i class="eicon-chevron-right"></i></div>',
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: carouselColumnTablet
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: carouselColumnTablet
                    }
                },
                {
                    breakpoint: 450,
                    settings: {
                        slidesToShow: carouselColumnMobile
                    }
                }
            ]
        } );	
    }
}

// logo carousel script ends

// modal popup script starts

var exclusiveModalPopup = function ($scope, $) {

    var modalWrapper    = $scope.find( '.exad-modal' ).eq(0),
    modalOverlayWrapper = $scope.find( '.exad-modal-overlay' ),
    modalItem           = $scope.find( '.exad-modal-item' ),
    modalAction         = modalWrapper.find( '.exad-modal-image-action' ),
    closeButton         = modalWrapper.find( '.exad-close-btn' ),
	getBodyWidth = function() {
      // https://developer.mozilla.org/en-US/docs/Web/API/Window/innerWidth#usage_notes
      const documentWidth = document.documentElement.clientWidth;
      return Math.abs(window.innerWidth - documentWidth);
    },
    preventPageScroll = function( modalStatus ) {
		
        const pageBody = exadPageBody[ 0 ]
        , cssClass = 'exad-modal-prevent-page-scroll'
        , styleProperty = 'padding-right'
        , exadSavedPaddingAttr = 'data-exad_saved_paddingright_value'
        , scrollbarWidth = getBodyWidth()
        , calculatedValue = Number.parseFloat( window.getComputedStyle( pageBody ).getPropertyValue( styleProperty ) ) + scrollbarWidth
        , preventPageScrollval = modalItem.attr( 'data-exad_modal_prevent_page_scroll' );
		
        if ( 'yes' === preventPageScrollval ) {
			
            if ( 'show' === modalStatus ) {
				
                exadPageBody.addClass( cssClass );
				
                exadPageBody.attr( exadSavedPaddingAttr, pageBody.style.getPropertyValue(styleProperty) );
                exadPageBody.css( styleProperty, `${Number.parseFloat(calculatedValue)}px` );
            }
			
            if ( 'hide' === modalStatus ) {
				
                exadPageBody.removeClass( cssClass );
				
                let exad_saved_paddingright_value = exadPageBody.attr(  exadSavedPaddingAttr );
				
                exadPageBody.css( styleProperty, '' );
                if ( null !== exad_saved_paddingright_value 
                    && 0 !== exad_saved_paddingright_value
                    && '' !== exad_saved_paddingright_value ) {
						
                    exadPageBody.css( styleProperty, exad_saved_paddingright_value );
                }
                pageBody.removeAttribute( exadSavedPaddingAttr );
            }
        }
    };

    modalAction.on( 'click', function(e) {
        e.preventDefault();
        var modalOverlay = $(this).parents().eq(1).next();
        var modal         = $(this).data( 'exad-modal' );
        
        var overlay = $(this).data( 'exad-overlay' );
        modalItem.css( 'display', 'block' );
        setTimeout( function() {
            $(modal).addClass( 'active' );
        }, 100);
        if ( 'yes' === overlay ) {
            modalOverlay.addClass( 'active' );
        }
        preventPageScroll( 'show' );
        
    } );

    closeButton.click( function() {
        var modalOverlay = $(this).parents().eq(3).next();
        var modalItem    = $(this).parents().eq(2);
        modalOverlay.removeClass( 'active' );
        modalItem.removeClass( 'active' );

        var modal_iframe = modalWrapper.find( 'iframe' ),
        $modal_video_tag  = modalWrapper.find( 'video' );

        if ( modal_iframe.length ) {
            var modal_src = modal_iframe.attr( 'src' ).replace( '&autoplay=1', '' );
            modal_iframe.attr( 'src', '' );
            modal_iframe.attr( 'src', modal_src );
        }
        if ( $modal_video_tag.length ) {
            $modal_video_tag[0].pause();
            $modal_video_tag[0].currentTime = 0;
        }
        preventPageScroll( 'hide' );
        
    } );

    modalOverlayWrapper.click( function() {
        var overlay_click_close = $(this).data( 'exad_overlay_click_close' );
        if( 'yes' === overlay_click_close ){
            $(this).removeClass( 'active' );
            $( '.exad-modal-item' ).removeClass( 'active' );

            var modal_iframe = modalWrapper.find( 'iframe' ),
            $modal_video_tag = modalWrapper.find( 'video' );

            if ( modal_iframe.length ) {
                var modal_src = modal_iframe.attr( 'src' ).replace( '&autoplay=1', '' );
                modal_iframe.attr( 'src', '' );
                modal_iframe.attr( 'src', modal_src );
            }
            if ( $modal_video_tag.length ) {
                $modal_video_tag[0].pause();
                $modal_video_tag[0].currentTime = 0;
            }
            preventPageScroll( 'hide' );
        }
    } );
}

// modal popup script ends

// news ticker script starts

var exclusiveNewsTicker = function( $scope, $ ) {

    var exad_news_ticker = $scope.find( '.exad-news-ticker' );

    if ( $.isFunction( $.fn.breakingNews ) ) {  
        exad_news_ticker.each( function() {
            var t            = $(this),
            auto             = t.data( 'autoplay' ) ? !0 : !1,
            animationEffect  = t.data( 'animation' ) ? t.data( 'animation' ) : '',                                   
            fixedPosition      = t.data( 'fixed_position' ) ? t.data( 'fixed_position' ) : '',                                   
            pauseOnHover     = t.data( 'pause_on_hover' ) ? t.data( 'pause_on_hover' ) : '',                                   
            animationSpeed   = t.data( 'animation_speed' ) ? t.data( 'animation_speed' ) : '',                                   
            autoplayInterval = t.data( 'autoplay_interval' ) ? t.data( 'autoplay_interval' ) : '',                                   
            height           = t.data( 'ticker_height' ) ? t.data( 'ticker_height' ) : '',                                   
            direction        = t.data( 'direction' ) ? t.data( 'direction' ) : ''; 

            $(this).breakingNews( {
                position: fixedPosition,
                play: auto,
                direction: direction,
                scrollSpeed: animationSpeed,
                stopOnHover: pauseOnHover,
                effect: animationEffect,
                delayTimer: autoplayInterval,                    
                height: height,
                fontSize: 'default',
                themeColor: 'default',
                background: 'default'             
            } );    
        } );
    }
}

// news ticker script ends

// Post grid script starts

var exclusivePostGrid = function( $scope, $ ) {
    var exadPostgridWrapped = $scope.find( '.exad-post-grid' );

    var exadPostArticle = exadPostgridWrapped.find('.exad-post-grid-three .exad-post-grid-container.exad-post-grid-equal-height-yes');
    var exadPostWrapper = exadPostgridWrapped.find('.exad-row-wrapper');
    // Match Height
    exadPostArticle.matchHeight({
        byRow: 0
    });

    var btn = exadPostgridWrapped.find('.exad-post-grid-paginate-btn');
    var btnText = btn.text();

    var page = 2;

    $(btn).on("click", function(e){
        e.preventDefault();
        $.ajax({
			url: exad_ajax_object.ajax_url,
			type: 'POST',
			data: {
				action: 'ajax_pagination',
                paged : page,
                post_type: $(this).data('post-type'),
                posts_per_page: $(this).data('posts_per_page'),
            	post_offset: $(this).data('post-offset'),
                post_thumbnail: $(this).data('post-thumbnail'),
                post_thumb_size: $(this).data('post-thumb-size'),
                equal_height: $(this).data('equal_height'),
                enable_details_btn: $(this).data('enable_details_btn'),
                details_btn_text: $(this).data('details_btn_text'),
                details_btn_text_tab: $(this).data('details_btn_text_tab'),
                show_user_avatar: $(this).data('show-user-avatar'),
                show_user_name: $(this).data('show_user_name'),
                post_data_position: $(this).data('post_data_position'),
                show_title: $(this).data('show_title'),
                show_title_parmalink: $(this).data('show_title_parmalink'),
                title_full: $(this).data('title_full'),
                title_tag: $(this).data('title_tag'),
                show_read_time: $(this).data('show_read_time'),
                show_comment: $(this).data('show_comment'),
                show_excerpt: $(this).data('show_excerpt'),
                excerpt_length: $(this).data('excerpt_length'),
                show_user_name_tag: $(this).data('show_user_name_tag'),
                user_name_tag: $(this).data('user_name_tag'),
                show_date: $(this).data('show_date'),
                show_date_tag: $(this).data('show_date_tag'),
                date_tag: $(this).data('date_tag'),
                title_length: $(this).data('title_length'),
                image_align: $(this).data('image_align'),
                category_default_position: $(this).data('category_default_position'),
                category_position_over_image: $(this).data('category_position_over_image'),
                show_category: $(this).data('show_category'),
                category: $(this).data('category'),
                tags: $(this).data('tags'),
                offset: $(this).data('offset'),
                exclude_post: $(this).data('exclude_post')
            },
            beforeSend : function ( xhr ) {
				btn.text('Loading...');
			},
            success: function( html ) {
                if( html.length > 0 ){
                    btn.text(btnText);
                    exadPostWrapper.append( html );
                    page++;
                    setTimeout(function(){
                        var newExadPostArticle = exadPostgridWrapped.find('.exad-post-grid-three .exad-post-grid-container.exad-post-grid-equal-height-yes');
                        newExadPostArticle.matchHeight({
                            byRow: 0
                        });
                    }, 10);
                } else {
					btn.remove();
				}
            },
		});
    });

    
          
}

// post grid script ends

// progress bar script starts

function animatedProgressbar( id, type, value, strokeColor, trailColor, strokeWidth, strokeTrailWidth ){
    var triggerClass = '.exad-progress-bar-' + id;
    if ( 'function' === typeof ldBar ) {
        if( 'line' === type ) {
            new ldBar( triggerClass, {
                'type'              : 'stroke',
                'path'              : 'M0 10L100 10',
                'aspect-ratio'      : 'none',
                'stroke'			: strokeColor,
                'stroke-trail'	    : trailColor,
                'stroke-width'      : strokeWidth,
                'stroke-trail-width': strokeTrailWidth
            } ).set( value );
        }
        if( 'line-bubble' === type ) {
            new ldBar( triggerClass, {
                'type'              : 'stroke',
                'path'              : 'M0 10L100 10',
                'aspect-ratio'      : 'none',
                'stroke'			: strokeColor,
                'stroke-trail'		: trailColor,
                'stroke-width'      : strokeWidth,
                'stroke-trail-width': strokeTrailWidth
            } ).set( value );
            $( $( '.exad-progress-bar-' + id ).find( '.ldBar-label' ) ).animate( {
                left: value + '%'
            }, 1000, 'swing');
        }
        if( 'circle' === type ) {
            new ldBar( triggerClass, {
                'type'				: 'stroke',
                'path'			    : 'M50 10A40 40 0 0 1 50 90A40 40 0 0 1 50 10',
                'stroke-dir'		: 'normal',
                'stroke'		    : strokeColor,
                'stroke-trail'	    : trailColor,
                'stroke-width'	    : strokeWidth,
                'stroke-trail-width': strokeTrailWidth
            } ).set( value );
        }
        if( 'fan' === type ) {
            new ldBar( triggerClass, {
                'type': 'stroke',
                'path': 'M10 90A40 40 0 0 1 90 90',
                'stroke': strokeColor,
                'stroke-trail': trailColor,
                'stroke-width': strokeWidth,
                'stroke-trail-width': strokeTrailWidth
            } ).set( value );
        }
    }
}

var exclusiveProgressBar = function ( $scope, $ ){
    var progressBarWrapper = $scope.find( '[data-progress-bar]' ).eq( 0 );
    if ( $.isFunction( $.fn.waypoint ) ) {
        progressBarWrapper.waypoint( function () {
            var element      = $( this.element ),
            id               = element.data( 'id' ),
            type             = element.data( 'type' ),
            value            = element.data( 'progress-bar-value' ),
            strokeWidth      = element.data( 'progress-bar-stroke-width' ),
            strokeTrailWidth = element.data( 'progress-bar-stroke-trail-width' ),
            color            = element.data( 'stroke-color' ),
            trailColor       = element.data( 'stroke-trail-color' );
            animatedProgressbar( id, type, value, color, trailColor, strokeWidth, strokeTrailWidth );
            this.destroy();
        }, {
            offset: 'bottom-in-view'
        } );
    }
}

// progress bar script ends


// Sticky script starts
var exclusiveSticky = function ($scope, $) {
	var exadStickySection = $scope.find('.exad-sticky-section-yes').eq(0);

	exadStickySection.each(function(i) {
		var dataSettings = $(this).data('settings');
		$.each( dataSettings, function(index, value) { 
			if( index === 'exad_sticky_top_spacing' ){
				$scope.find('.exad-sticky-section-yes').css( "top", value + "px" );
			}
		}); 
    });
	$scope.each(function(i) {
		var sectionSettings = $scope.data("settings");
		$.each( sectionSettings, function(index, value) { 
			if( index === 'exad_sticky_top_spacing' ){
				$scope.css( "top", value + "px" );
			}
		}); 
    });
    
	if ( exadStickySection.length > 0 ) {
		var parent = document.querySelector('.exad-sticky-section-yes').parentElement;
		while (parent) {
			var hasOverflow = getComputedStyle(parent).overflow;
			if (hasOverflow !== 'visible') {
				parent.style.overflow = "visible"
			}
			parent = parent.parentElement;
		}
	}

	var columnClass = $scope.find( '.exad-column-sticky' );
	var dataId = columnClass.data('id');
	var dataType = columnClass.data('type');
	var topSpacing = columnClass.data('top_spacing');
	
	if( dataType === 'column' ){
		var $target  = $scope
		, wrapClass = columnClass.find( '.elementor-widget-wrap' )
		, containerSelector = '.elementor-row'
		, containers = document.querySelectorAll( containerSelector );
		
		if ( containers.length === 0 ) {
			
			containerSelector = 'body #content .container:first-child'
			containers = document.querySelectorAll( containerSelector );
		}
		
		if ( containers.length === 0 ) {
			
			containerSelector = '.content-wrapper.container .row'
			containers = document.querySelectorAll( containerSelector );
		}
		
		if ( containers.length === 0 ) {
			
			containerSelector = false;
		}
		
		wrapClass.stickySidebar({
			topSpacing: topSpacing,
			bottomSpacing: 60,
			containerSelector: containerSelector,
        	innerWrapperSelector: '.elementor-column-wrap',
		});
	}

}
// Sticky script ends

// tabs script starts

var exclusiveTabs   = function( $scope, $ ) {
    var tabsWrapper = $scope.find( '.exad-tabs-'+ $scope.data("id") ).eq(0);
    tabsWrapper.each( function() {
        var tab         = $scope.find( '.exad-tabs-'+ $scope.data("id") ),
        isTabActive     = false,
        isContentActive = false;
        tab.children().find( ' > [data-tab]' ).each( function (){
            if( $(this).hasClass( 'active' ) ){
                isTabActive = true;
            }
        } );
        tab.find( ' > .exad-advance-tab-content' ).each( function (){
            if( $(this).hasClass( 'active' ) ){
                isContentActive = true;
            }
        } );
        if( !isContentActive ){
            tab.find( ' > .exad-advance-tab-content' ).eq(0).addClass( 'active' );
        }
        if( !isTabActive ){
            tab.find( '[data-tab]' ).eq(0).addClass( 'active' );
        }
        tab.children().find( ' > [data-tab]' ).click(function() {
            tab.find( '[data-tab]' ).removeClass( 'active' );
            tab.find( ' > .exad-advance-tab-content' ).removeClass( 'active' );
            $(this).addClass( 'active' );
            tab.find( ' > [data-tab]' ).eq($(this).index()).addClass( 'active' );
            tab.find( ' > .exad-advance-tab-content' ).eq($(this).index()).addClass( 'active' );
        } );
    } );
}

// tabs script ends

// Flip Box script starts

var exclusiveFlipBox = function( $scope, $ ) {
	
    let flipBox = $scope.find( '.exad-flip-box' )
	, $flipBox = $( flipBox )
	, cssHoverSelector = 'efb-hovered';
	
    $flipBox.on("click", function(e) {
		
        $( this ).toggleClass( cssHoverSelector );
    });   
}

// Flip Box script ends


$(window).on('elementor/frontend/init', function () {
    if( elementorFrontend.isEditMode() ) {
        editMode = true;
    }
    
    elementorFrontend.hooks.addAction( 'frontend/element_ready/exad-exclusive-accordion.default', exclusiveAccordion );
    elementorFrontend.hooks.addAction( 'frontend/element_ready/exad-post-grid.default', exclusivePostGrid );
    elementorFrontend.hooks.addAction( 'frontend/element_ready/exad-exclusive-alert.default', exclusiveAlert );
    elementorFrontend.hooks.addAction( 'frontend/element_ready/exad-animated-text.default', exclusiveAnimatedText );
    elementorFrontend.hooks.addAction( 'frontend/element_ready/exad-exclusive-button.default', exclusiveButton );
    elementorFrontend.hooks.addAction( 'frontend/element_ready/exad-countdown-timer.default', exclusiveCountdownTimer );
    elementorFrontend.hooks.addAction( 'frontend/element_ready/exad-filterable-gallery.default', exclusiveFilterableGallery );
    elementorFrontend.hooks.addAction( 'frontend/element_ready/exad-google-maps.default', exclusiveGoogleMaps );
    elementorFrontend.hooks.addAction( 'frontend/element_ready/exad-image-comparison.default', exclusiveImageComparison );
    elementorFrontend.hooks.addAction( 'frontend/element_ready/exad-image-magnifier.default', exclusiveImageMagnifier );
    elementorFrontend.hooks.addAction( 'frontend/element_ready/exad-logo-carousel.default', exclusiveLogoCarousel );
    elementorFrontend.hooks.addAction( 'frontend/element_ready/exad-modal-popup.default', exclusiveModalPopup );
    elementorFrontend.hooks.addAction( 'frontend/element_ready/exad-news-ticker.default', exclusiveNewsTicker );
    elementorFrontend.hooks.addAction( 'frontend/element_ready/exad-progress-bar.default', exclusiveProgressBar );
    elementorFrontend.hooks.addAction( 'frontend/element_ready/exad-exclusive-tabs.default', exclusiveTabs );
    elementorFrontend.hooks.addAction( 'frontend/element_ready/exad-covid-19.default', exclusiveCorona );
    elementorFrontend.hooks.addAction( 'frontend/element_ready/exad-facebook-feed.default', exadFacebookFeed );
    elementorFrontend.hooks.addAction( 'frontend/element_ready/exad-google-reviews.default', exclusiveGoogleReviews );
    elementorFrontend.hooks.addAction( 'frontend/element_ready/exad-filterable-post.default', exclusiveFilterablePost);
    elementorFrontend.hooks.addAction( 'frontend/element_ready/exad-flipbox.default', exclusiveFlipBox );
    elementorFrontend.hooks.addAction( 'frontend/element_ready/section', exclusiveSticky);
});	

}(jQuery));