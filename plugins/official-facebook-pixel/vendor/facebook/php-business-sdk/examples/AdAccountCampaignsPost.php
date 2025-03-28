<?php
 /*
 * Copyright (c) Meta Platforms, Inc. and affiliates.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */

require __DIR__ . '/vendor/autoload.php';

use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Campaign;
use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;

$access_token = '<ACCESS_TOKEN>';
$app_secret = '<APP_SECRET>';
$app_id = '<APP_ID>';
$id = '<AD_ACCOUNT_ID>';

$api = Api::init($app_id, $app_secret, $access_token);
$api->setLogger(new CurlLogger());

$fields = array(
);
$params = array(
  'name' => 'My campaign',
  'objective' => 'OUTCOME_TRAFFIC',
  'status' => 'PAUSED',
  'special_ad_categories' => array(),
);
echo json_encode((new AdAccount($id))->createCampaign(
  $fields,
  $params
)->exportAllData(), JSON_PRETTY_PRINT);

$fields = array(
);
$params = array(
  'name' => 'Lead generation campaign',
  'objective' => 'OUTCOME_LEADS',
  'status' => 'PAUSED',
  'special_ad_categories' => array(),
);
echo json_encode((new AdAccount($id))->createCampaign(
  $fields,
  $params
)->exportAllData(), JSON_PRETTY_PRINT);

$fields = array(
);
$params = array(
  'name' => 'Local ad campaign',
  'objective' => 'OUTCOME_AWARENESS',
  'status' => 'PAUSED',
  'special_ad_categories' => array(),
);
echo json_encode((new AdAccount($id))->createCampaign(
  $fields,
  $params
)->exportAllData(), JSON_PRETTY_PRINT);

$fields = array(
);
$params = array(
  'name' => 'Mobile App Installs Campaign',
  'objective' => 'OUTCOME_APP_PROMOTION',
  'status' => 'PAUSED',
  'special_ad_categories' => array(),
);
echo json_encode((new AdAccount($id))->createCampaign(
  $fields,
  $params
)->exportAllData(), JSON_PRETTY_PRINT);

$fields = array(
);
$params = array(
  'name' => 'App Installs Campaign with Dynamic Product Ads',
  'objective' => 'OUTCOME_APP_PROMOTION',
  'status' => 'PAUSED',
  'special_ad_categories' => array(),
);
echo json_encode((new AdAccount($id))->createCampaign(
  $fields,
  $params
)->exportAllData(), JSON_PRETTY_PRINT);

$fields = array(
);
$params = array(
  'name' => 'Video Views campaign',
  'objective' => 'OUTCOME_ENGAGEMENT',
  'status' => 'PAUSED',
  'special_ad_categories' => array(),
);
echo json_encode((new AdAccount($id))->createCampaign(
  $fields,
  $params
)->exportAllData(), JSON_PRETTY_PRINT);

$fields = array(
);
$params = array(
  'name' => 'My First Campaign',
  'objective' => 'OUTCOME_ENGAGEMENT',
  'status' => 'PAUSED',
  'special_ad_categories' => array(),
);
echo json_encode((new AdAccount($id))->createCampaign(
  $fields,
  $params
)->exportAllData(), JSON_PRETTY_PRINT);

$fields = array(
);
$params = array(
  'name' => 'My First Campaign',
  'objective' => 'OUTCOME_ENGAGEMENT',
  'status' => 'PAUSED',
  'special_ad_categories' => array(),
);
echo json_encode((new AdAccount($id))->createCampaign(
  $fields,
  $params
)->exportAllData(), JSON_PRETTY_PRINT);

$fields = array(
);
$params = array(
  'name' => 'My First Campaign with daily budget',
  'objective' => 'OUTCOME_LEADS',
  'status' => 'PAUSED',
  'daily_budget' => '1000',
  'special_ad_categories' => array(),
);
echo json_encode((new AdAccount($id))->createCampaign(
  $fields,
  $params
)->exportAllData(), JSON_PRETTY_PRINT);

$fields = array(
);
$params = array(
  'name' => 'My First Campaign with special ad categories',
  'objective' => 'OUTCOME_LEADS',
  'status' => 'PAUSED',
  'daily_budget' => '1000',
  'special_ad_categories' => array(),
  'special_ad_category_country' => array('MX'),
);
echo json_encode((new AdAccount($id))->createCampaign(
  $fields,
  $params
)->exportAllData(), JSON_PRETTY_PRINT);