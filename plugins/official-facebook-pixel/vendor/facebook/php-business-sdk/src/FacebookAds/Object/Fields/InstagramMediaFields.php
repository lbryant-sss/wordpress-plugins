<?php
 /*
 * Copyright (c) Meta Platforms, Inc. and affiliates.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */

namespace FacebookAds\Object\Fields;

use FacebookAds\Enum\AbstractEnum;

/**
 * This class is auto-generated.
 *
 * For any issues or feature requests related to this class, please let us know
 * on github and we'll fix in our codegen framework. We'll not be able to accept
 * pull request for this class.
 *
 */

class InstagramMediaFields extends AbstractEnum {

  const CAPTION_TEXT = 'caption_text';
  const COMMENT_COUNT = 'comment_count';
  const CONTENT_TYPE = 'content_type';
  const DISPLAY_URL = 'display_url';
  const FILTER_NAME = 'filter_name';
  const ID = 'id';
  const IG_MEDIA_ID = 'ig_media_id';
  const LATITUDE = 'latitude';
  const LIKE_COUNT = 'like_count';
  const LOCATION = 'location';
  const LOCATION_NAME = 'location_name';
  const LONGITUDE = 'longitude';
  const OWNER_INSTAGRAM_USER = 'owner_instagram_user';
  const PERMALINK = 'permalink';
  const TAKEN_AT = 'taken_at';
  const VIDEO_URL = 'video_url';

  public function getFieldTypes() {
    return array(
      'caption_text' => 'string',
      'comment_count' => 'int',
      'content_type' => 'int',
      'display_url' => 'string',
      'filter_name' => 'string',
      'id' => 'string',
      'ig_media_id' => 'string',
      'latitude' => 'float',
      'like_count' => 'int',
      'location' => 'Location',
      'location_name' => 'string',
      'longitude' => 'float',
      'owner_instagram_user' => 'InstagramUser',
      'permalink' => 'string',
      'taken_at' => 'datetime',
      'video_url' => 'string',
    );
  }
}
