<?php
 /*
 * Copyright (c) Meta Platforms, Inc. and affiliates.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */

require __DIR__ . '/vendor/autoload.php';

use FacebookAds\Object\AdSet;
use FacebookAds\Object\Ad;
use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;

$access_token = '<ACCESS_TOKEN>';
$app_secret = '<APP_SECRET>';
$app_id = '<APP_ID>';
$id = '<AD_SET_ID>';

$api = Api::init($app_id, $app_secret, $access_token);
$api->setLogger(new CurlLogger());

$fields = array(
  'name',
  'id',
);
$params = array(
);
echo json_encode((new AdSet($id))->getAds(
  $fields,
  $params
)->getResponse()->getContent(), JSON_PRETTY_PRINT);