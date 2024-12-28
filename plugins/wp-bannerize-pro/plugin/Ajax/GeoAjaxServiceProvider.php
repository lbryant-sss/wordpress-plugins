<?php

namespace WPBannerize\Ajax;

use WPBannerize\GeoLocalizer\GeoLocalizerProvider;

class GeoAjaxServiceProvider extends AjaxServiceProvider
{

    /**
     * List of the ajax actions executed only by logged-in users.
     * Here you will use a methods list.
     *
     * @var array
     */
    protected $logged = ['wp_bannerize_get_geo'];

    /**
     * Return the geo information
     *
     * @return void
     */
    public function wp_bannerize_get_geo()
    {
        WPBannerize()->options->get('geolocalization.ipstack.api_key');
        $info = GeoLocalizerProvider::geoIP();
        wp_send_json($info);
    }
}
