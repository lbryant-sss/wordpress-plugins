<?php
/**
 * ZohoDesk Files Api
 *
 */

namespace BitCode\BitForm\Core\Integration\ZohoDesk;

use BitCode\BitForm\Core\Util\FileHandler;
use BitCode\BitForm\Core\Util\HttpHelper;

/**
 * Provide functionality for Upload files
 */
final class FilesApiHelper
{
  private $_defaultHeader;
  private $_apiDomain;
  private $_payloadBoundary;
  private $_basepath;

  /**
   *
   * @param object  $tokenDetails Api token details
   * @param integer $formID       ID of the form, for which integration is executing
   * @param integer $entryID      Current submission ID
   */
  public function __construct($tokenDetails, $orgId, $formID, $entryID)
  {
    $this->_payloadBoundary = wp_generate_password(24);
    $this->_defaultHeader['Authorization'] = "Zoho-oauthtoken {$tokenDetails->access_token}";
    $this->_defaultHeader['orgId'] = $orgId;
    $this->_defaultHeader['content-type'] = 'multipart/form-data; boundary=' . $this->_payloadBoundary;
    $this->_apiDomain = \urldecode($tokenDetails->api_domain);
    $this->_basepath = FileHandler::getEntriesFileUploadDir($formID, $entryID) . DIRECTORY_SEPARATOR;
  }

  /**
   * Helps to execute upload files api
   *
   * @param mixed $files        Files path
   * @param bool  $isAttachment Check upload type
   * @param mixed $module       Attachment Module name
   * @param mixed $recordID     Record id
   *
   * @return mixed $uploadedFiles ID's of uploaded file in Zoho Desk
   */
  public function uploadFiles($files, $ticketId, $dataCenter)
  {
    $uploadFileEndpoint = "https://desk.zoho.{$dataCenter}/api/v1/tickets/{$ticketId}/attachments";
    $payload = '';
    if (is_array($files)) {
      foreach ($files as $fileIndex => $fileName) {
        if (file_exists("{$this->_basepath}{$fileName}")) {
          $payload .= '--' . $this->_payloadBoundary;
          $payload .= "\r\n";
          $payload .= 'Content-Disposition: form-data; name="' . 'file' .
              '"; filename="' . basename("{$this->_basepath}{$fileName}") . '"' . "\r\n";
          $payload .= "\r\n";
          $payload .= file_get_contents("{$this->_basepath}{$fileName}");
          $payload .= "\r\n";
        }
      }
    } elseif (file_exists("{$this->_basepath}{$files}")) {
      $payload .= '--' . $this->_payloadBoundary;
      $payload .= "\r\n";
      $payload .= 'Content-Disposition: form-data; name="' . 'file' .
          '"; filename="' . basename("{$this->_basepath}{$files}") . '"' . "\r\n";
      $payload .= "\r\n";
      $payload .= file_get_contents("{$this->_basepath}{$files}");
      $payload .= "\r\n";
    }

    if (empty($payload)) {
      return false;
    }
    $payload .= '--' . $this->_payloadBoundary . '--';

    $uploadResponse = HttpHelper::post($uploadFileEndpoint, $payload, $this->_defaultHeader);

    return $uploadResponse;
  }
}
