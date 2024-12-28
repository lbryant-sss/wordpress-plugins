<?php

namespace WPBannerize\Models;

use WPBannerize\Traits\AnalyticsTrait;
use WPBannerize\WPBones\Database\Model;

class WPBannerizeClicks extends Model
{
    use AnalyticsTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'w_p_bannerize_clicks';

    /**
     * The option name used to clean up the table
     *
     * @example "clicks.max_records"
     *
     * @var string
     */
    protected $optionMaxRecord = 'clicks.max_records';

    /**
     * The table associated with the model.
     *
     * @var string 'clicks' or 'impressions'
     */
    protected string $analytic = 'clicks';
}
