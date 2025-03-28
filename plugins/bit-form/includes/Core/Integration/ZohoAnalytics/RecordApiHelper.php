<?php

/**
 * ZohoRecruit Record Api
 *
 */

namespace BitCode\BitForm\Core\Integration\ZohoAnalytics;

use BitCode\BitForm\Core\Util\ApiResponse as UtilApiResponse;
use BitCode\BitForm\Core\Util\FieldValueHandler;
use BitCode\BitForm\Core\Util\HttpHelper;

/**
 * Provide functionality for Record insert,upsert
 */
class RecordApiHelper
{
  private $_defaultHeader;
  private $_apiDomain;
  private $_tokenDetails;

  private $_integrationID;
  private $_logID;
  private $_logResponse;

  private $_formId;

  private $_entryId;

  public function __construct($tokenDetails, $integId, $logID, $formId, $entryId)
  {
    $this->_defaultHeader['Authorization'] = "Zoho-oauthtoken {$tokenDetails->access_token}";
    $this->_apiDomain = \urldecode($tokenDetails->api_domain);
    $this->_tokenDetails = $tokenDetails;
    $this->_integrationID = $integId;
    $this->_logID = $logID;
    $this->_logResponse = new UtilApiResponse();

    $this->_formId = $formId;
    $this->_entryId = $entryId;
  }

  public function insertRecord($workspace, $table, $ownerEmail, $dataCenter, $data)
  {
    $insertRecordEndpoint = "https://analyticsapi.zoho.{$dataCenter}/api/{$ownerEmail}/{$workspace}/{$table}?ZOHO_ACTION=ADDROW&ZOHO_OUTPUT_FORMAT=JSON&ZOHO_ERROR_FORMAT=JSON&ZOHO_API_VERSION=1.0";
    return HttpHelper::post($insertRecordEndpoint, $data, $this->_defaultHeader);
  }

  public function updateRecord($workspace, $table, $ownerEmail, $dataCenter, $criteria, $data)
  {
    $updateRecordEndpoint = "https://analyticsapi.zoho.{$dataCenter}/api/{$ownerEmail}/{$workspace}/{$table}?ZOHO_ACTION=UPDATE&ZOHO_OUTPUT_FORMAT=JSON&ZOHO_ERROR_FORMAT=JSON&ZOHO_API_VERSION=1.0&ZOHO_CRITERIA={$criteria}";

    return HttpHelper::post($updateRecordEndpoint, $data, $this->_defaultHeader);
  }

  public function shareTable($dataCenter, $ownerEmail, $workspace, $table, $data)
  {
    $shareTableEndpoint = "https://analyticsapi.zoho.{$dataCenter}/api/{$ownerEmail}/{$workspace}/?ZOHO_VIEWS={$table}&ZOHO_ACTION=SHARE&ZOHO_OUTPUT_FORMAT=JSON&ZOHO_ERROR_FORMAT=JSON&ZOHO_API_VERSION=1.0&";

    $shareTableEndpoint .= build_query($data);

    return HttpHelper::post($shareTableEndpoint, null, $this->_defaultHeader);
  }

  public function executeRecordApi($workspace, $table, $ownerEmail, $dataCenter, $actions, $defaultConf, $fieldValues, $fieldMap)
  {
    $fieldData = [];
    foreach ($fieldMap as $fieldKey => $fieldPair) {
      if (!empty($fieldPair->zohoFormField) && !empty($fieldPair->formField)) {
        if ('custom' === $fieldPair->formField && isset($fieldPair->customValue)) {
          $fieldData[$fieldPair->zohoFormField] = $fieldPair->customValue;
        } else {
          $fieldData[$fieldPair->zohoFormField] = $fieldValues[$fieldPair->formField];
        }
      }
    }

    $entryDetails = [
      'formId'      => $this->_formId,
      'entryId'     => $this->_entryId,
      'fieldValues' => $fieldValues
    ];

    if (isset($actions->update->criteria)) {
      $recordApiResponse = $this->updateRecord($workspace, $table, $ownerEmail, $dataCenter, $actions->update->criteria, $fieldData);

      $recordApiResponse = json_decode(preg_replace("/\\\'/", "'", $recordApiResponse));

      if (isset($recordApiResponse->response->error)) {
        $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'record', 'type_name' => 'update'], 'error', $recordApiResponse, $entryDetails);
      } else {
        $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'record', 'type_name' => 'update'], 'success', $recordApiResponse, $entryDetails);
      }

      if ($actions->update->insert && '0' === $recordApiResponse->response->result->updatedRows) {
        $recordApiResponse = $this->insertRecord($workspace, $table, $ownerEmail, $dataCenter, $fieldData);

        if (isset($recordApiResponse->response->error)) {
          $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'record', 'type_name' => 'insert'], 'error', $recordApiResponse, $entryDetails);
        } else {
          $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'record', 'type_name' => 'insert'], 'success', $recordApiResponse, $entryDetails);
        }
      }
    } else {
      $recordApiResponse = $this->insertRecord($workspace, $table, $ownerEmail, $dataCenter, $fieldData);
      if (isset($recordApiResponse->response->error)) {
        $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'record', 'type_name' => 'insert'], 'error', $recordApiResponse, $entryDetails);
      } else {
        $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'record', 'type_name' => 'insert'], 'success', $recordApiResponse, $entryDetails);
      }
    }

    if (isset($actions->share)) {
      $share_arr = [];

      if (!empty($actions->share->email)) {
        $share_arr['ZOHO_EMAILS'] = FieldValueHandler::replaceFieldWithValue($actions->share->email, $fieldValues);
      }

      if (isset($actions->share->permissions)) {
        $permissions = $actions->share->permissions;

        foreach ($permissions as $permission) {
          foreach ($permission as $perm) {
            $share_arr[$perm] = 'true';
          }
        }
      }

      if (!empty($share_arr['ZOHO_EMAILS'])) {
        $recordApiResponse = $this->shareTable($dataCenter, $ownerEmail, $workspace, $table, $share_arr);
        if (isset($recordApiResponse->response->error)) {
          $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'share', 'type_name' => $table], 'error', $recordApiResponse, $entryDetails);
        } else {
          $this->_logResponse->apiResponse($this->_logID, $this->_integrationID, ['type' => 'record', 'type_name' => $table], 'success', $recordApiResponse, $entryDetails);
        }
      }
    }

    return $recordApiResponse;
  }
}
