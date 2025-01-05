(function($){
    "use strict";


    $.fn.aws_search = function( options ) {


        var self           = $(this),
            $searchForm    = self.find('.aws-search-form'),
            $searchField   = self.find('.aws-search-field'),
            $searchResults = self.find('.aws-search-result'),
            haveResults    = false,
            requests       = Array(),
            searchFor      = '',
            cachedResponse = new Array();


        var opts = $.extend( {
            url       : ( self.data('url')         !== undefined ) ? self.data('url') : false,
            siteUrl   : ( self.data('siteurl')     !== undefined ) ? self.data('siteurl') : false,
            minChars  : ( self.data('min-chars')   !== undefined ) ? self.data('min-chars') : 1,
            showLoader: ( self.data('show-loader') !== undefined ) ? self.data('show-loader') : true
        }, options );


        $searchField.on( 'keyup', function(e) {
            methods.onKeyup(e);
        });


        $searchField.on( 'focus', function (e) {
            methods.onFocus(e);
        });


        $(document).on( 'click', function (e) {
            methods.hideResults(e);
        });


        var methods = {

            onKeyup: function(e) {

                searchFor = $searchField.val();
                searchFor = searchFor.trim();
                searchFor = searchFor.replace( /[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '' );
                searchFor = searchFor.replace( /\s\s+/g, ' ' );

                for ( var i = 0; i < requests.length; i++ ) {
                    requests[i].abort();
                }

                if ( searchFor === '' ) {
                    $searchResults.html('');
                    return;
                }

                if ( typeof cachedResponse[searchFor] != 'undefined') {
                    methods.showResults( cachedResponse[searchFor] );
                    return;
                }

                if ( searchFor.length < opts.minChars ) {
                    $searchResults.html('');
                    return;
                }

                if ( opts.showLoader ) {
                    $searchForm.addClass('processing');
                }

                var data = {
                    action: 'aws_action',
                    keyword : searchFor,
                    page: 0
                };

                requests.push(

                    $.ajax({
                        type: 'POST',
                        url: opts.url,
                        data: data,
                        success: function( response ) {

                            var response = $.parseJSON( response );

                            cachedResponse[searchFor] = response;

                            methods.showResults( response );

                            $searchResults.show();

                        },
                        error: function (data, dummy) {
                        }
                    })

                );

            },

            showResults: function( response ) {

                var html = '<ul>';


                if ( response.cats.length > 0 ) {

                    $.each(response.cats, function (i, result) {

                        html += '<li class="aws_result_item aws_result_cat">';
                            html += '<a class="aws_result_link" href="' + result.link + '" >';
                                html += '<span class="aws_result_content">';
                                    html += '<span class="aws_result_title">';
                                        html += result.name + '(' + result.count + ')';
                                    html += '</span>';
                                html += '</span>';
                            html += '</a>';
                        html += '</li>';

                    });

                }

                if ( response.tags.length > 0 ) {

                    $.each(response.tags, function (i, result) {

                        html += '<li class="aws_result_item aws_result_tag">';
                            html += '<a class="aws_result_link" href="' + result.link + '" >';
                                html += '<span class="aws_result_content">';
                                    html += '<span class="aws_result_title">';
                                        html += result.name + '(' + result.count + ')';
                                    html += '</span>';
                                html += '</span>';
                            html += '</a>';
                        html += '</li>';

                    });

                }

                if ( response.products.length > 0 ) {

                    $.each(response.products, function (i, result) {

                        html += '<li class="aws_result_item">';
                            html += '<a class="aws_result_link" href="' + result.link + '" >';

                            if ( result.image ) {
                                html += '<span class="aws_result_image">';
                                html += '<img src="' + result.image + '">';
                                html += '</span>';
                            }

                            html += '<span class="aws_result_content">';
                                html += '<span class="aws_result_title">' + result.title + '</span>';

                                if ( result.excerpt ) {
                                    html += '<span class="aws_result_excerpt">' + result.excerpt + '</span>';
                                }

                                if ( result.price ) {
                                    html += '<span class="aws_result_price">' + result.price + '</span>';
                                }

                            html += '</span>';

                            if ( result.on_sale ) {
                                html += '<span class="aws_result_sale">';
                                    html += '<span class="aws_onsale">Sale!</span>';
                                html += '</span>';
                            }

                            html += '</a>';
                        html += '</li>';

                    });

                    //html += '<li class="aws_result_item aws_search_more"><a href="' + opts.siteUrl + '/?s=' + searchFor + '&post_type=product">View all</a></li>';
                    //html += '<li class="aws_result_item"><a href="#">Next Page</a></li>';

                }

                if ( response.cats.length <= 0 && response.tags.length <= 0 && response.products.length <= 0 ) {
                    html += '<li class="aws_result_item aws_no_result">Nothing found</li>';
                }


                html += '</ul>';

                $searchForm.removeClass('processing');
                $searchResults.html( html );

                $searchResults.show();

            },

            onFocus: function( event ) {
                if ( searchFor !== '' ) {
                    $searchResults.show();
                }
            },

            hideResults: function( event ) {
                if ( ! $(event.target).closest( ".aws-container" ).length ) {
                    $searchResults.hide();
                }
            }

        };

    };


    // Call plugin method
    $(document).ready(function() {

        $('.aws-container').each( function() {
            $(this).aws_search();
        });

    });


})( jQuery );