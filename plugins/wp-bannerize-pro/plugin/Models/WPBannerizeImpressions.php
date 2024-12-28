<?php

namespace WPBannerize\Models;

use WPBannerize\WPBones\Database\Model;
use WPBannerize\Traits\AnalyticsTrait;

class WPBannerizeImpressions extends Model
{
  use AnalyticsTrait;

  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'w_p_bannerize_impressions';

  /**
   * The option name used to cleanup the table
   *
   * @example "clicks.max_records"
   *
   * @var string
   */
  protected $optionMaxRecord = 'impressions.max_records';

  /**
   * The table associated with the model.
   *
   * @var string 'clicks' or 'impressions'
   */
  protected $analytic = 'impressions';
}
