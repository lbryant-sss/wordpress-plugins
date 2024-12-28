<?php

namespace WPBannerize\Http\Controllers;

class WPBannerizeAnalyticsController extends Controller
{
    public function index()
    {
        return WPBannerize()
            ->view('analytics.index')
            ->withLocalizeScript('analytics/analytics', 'WPBannerize', [
                'nonce' => wp_create_nonce('wp-bannerize-pro'),
                'version' => WPBannerize()->Version,
                'preferences' => WPBannerize()->options->toArray(),
                'health' => wp_create_nonce('wp_rest'),
                'manage_analytics' => current_user_can('manage_analytics'),
            ])
            ->withAdminAppsScript('analytics/analytics', true);
    }
}
