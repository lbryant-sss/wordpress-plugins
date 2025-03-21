const wpp_params = document.currentScript.dataset;
const WordPressPopularPosts = (function() {

    "use strict";

    const noop = function(){};

    const get = function( url, params, callback, additional_headers ){
        callback = ( 'function' === typeof callback ) ? callback : noop;
        ajax( "GET", url, params, callback, additional_headers );
    };

    const post = function( url, params, callback, additional_headers ){
        callback = ( 'function' === typeof callback ) ? callback : noop;
        ajax( "POST", url, params, callback, additional_headers );
    };

    const ajax = function( method, url, params, callback, additional_headers ){
        /* Create XMLHttpRequest object and set variables */
        let xhr = new XMLHttpRequest(),
            target = url,
            args = params,
            valid_methods = ["GET", "POST"],
            headers = {
                'X-Requested-With': 'XMLHttpRequest'
            };

        method = -1 != valid_methods.indexOf( method ) ? method : "GET";

        /* Set request headers */
        if ( 'POST' == method ) {
            headers['Content-Type'] = 'application/x-www-form-urlencoded';
        }

        if ( 'object' == typeof additional_headers && Object.keys(additional_headers).length ) {
            headers = Object.assign({}, headers, additional_headers);
        }

        /* Set request method and target URL */
        xhr.open( method, target + ( 'GET' == method ? '?' + args : '' ), true );

        for (const key in headers) {
            if ( headers.hasOwnProperty(key) ) {
                xhr.setRequestHeader( key, headers[key] );
            }
        }

        /* Hook into onreadystatechange */
        xhr.onreadystatechange = function() {
            if ( 4 === xhr.readyState && 200 <= xhr.status && 300 > xhr.status ) {
                if ( 'function' === typeof callback ) {
                    callback.call( undefined, xhr.response );
                }
            }
        };

        /* Send request */
        xhr.send( ( 'POST' == method ? args : null ) );
    };

    const theme = function(wpp_list) {
        let base_styles = document.createElement('style'),
            dummy_list = document.createElement('ul');

        dummy_list.innerHTML = '<li><a href="#"></a></li>';
        wpp_list.parentNode.appendChild(dummy_list);

        let dummy_list_item_styles = getComputedStyle(dummy_list.querySelector('li')),
            dummy_link_item_styles = getComputedStyle(dummy_list.querySelector('li a'));

        base_styles.innerHTML = '.wpp-list li {font-size: '+ dummy_list_item_styles.fontSize +'}';
        base_styles.innerHTML += '.wpp-list li a {color: '+ dummy_link_item_styles.color +'}';

        wpp_list.parentNode.removeChild(dummy_list);

        let wpp_list_sr = wpp_list.attachShadow({mode: "open"});

        wpp_list_sr.append(base_styles);

        while(wpp_list.firstElementChild) {
            wpp_list_sr.append(wpp_list.firstElementChild);
        }
    };

    return {
        get: get,
        post: post,
        ajax: ajax,
        theme: theme
    };

})();

(function(){
    if ( ! Object.keys(wpp_params).length ) {
        console.error('WPP params not found, if you are using a JS minifier tool please add wpp.min.js to its exclusion list');
        return;
    }

    const post_id = Number(wpp_params.postId);
    let do_request = true;

    if ( post_id ) {
        if ( '1' == wpp_params.sampling ) {
            let num = Math.floor(Math.random() * wpp_params.samplingRate) + 1;
            do_request = ( 1 === num );
        }

        if ( 'boolean' === typeof window.wpp_do_request ) {
            do_request = window.wpp_do_request;
        }

        if ( do_request ) {
            WordPressPopularPosts.post(
                wpp_params.apiUrl + '/v2/views/' + post_id,
                "_wpnonce=" + wpp_params.token + "&sampling=" + wpp_params.sampling + "&sampling_rate=" + wpp_params.samplingRate,
                function( response ) {
                    wpp_params.debug&&window.console&&window.console.log&&window.console.log(JSON.parse(response));
                }
            );
        }
    }
})();

document.addEventListener('DOMContentLoaded', function() {
    if ( ! Object.keys(wpp_params).length ) {
        return;
    }

    const widget_placeholders = document.querySelectorAll('.wpp-widget-block-placeholder, .wpp-shortcode-placeholder');
    let w = 0;

    while ( w < widget_placeholders.length ) {
        fetchWidget(widget_placeholders[w]);
        w++;
    }

    const sr = document.querySelectorAll('.popular-posts-sr');

    if ( sr.length ) {
        for( let s = 0; s < sr.length; s++ ) {
            WordPressPopularPosts.theme(sr[s]);
        }
    }

    function fetchWidget(widget_placeholder) {
        let headers = {
                'Content-Type': 'application/json',
                'X-WP-Nonce': wpp_params.token
            },
            params = '',
            method = 'POST',
            url = wpp_params.apiUrl + '/v2/widget?is_single=' + wpp_params.postId + ( wpp_params.lang ? '&lang=' + wpp_params.lang : '' );

        let json_tag = widget_placeholder.parentNode.querySelector('script[type="application/json"]');

        if ( json_tag ) {
            let args = JSON.parse(json_tag.textContent.replace(/[\n\r]/g,''));
            params = JSON.stringify(args);
        }

        WordPressPopularPosts.ajax(
            method,
            url,
            params,
            function(response) {
                renderWidget(response, widget_placeholder);
            },
            headers
        );
    }

    function renderWidget(response, widget_placeholder) {
        widget_placeholder.insertAdjacentHTML('afterend', JSON.parse(response).widget);

        let parent = widget_placeholder.parentNode,
            sr = parent.querySelector('.popular-posts-sr'),
            json_tag = parent.querySelector('script[type="application/json"]');

        if ( json_tag )
            parent.removeChild(json_tag);

        parent.removeChild(widget_placeholder);
        parent.classList.add('wpp-ajax');

        if ( sr ) {
            WordPressPopularPosts.theme(sr);
        }

        let event = new Event("wpp-onload", {"bubbles": true, "cancelable": false});
        parent.dispatchEvent(event);
    }
});
