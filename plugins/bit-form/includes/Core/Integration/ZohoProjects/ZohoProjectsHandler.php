<?php

/**
 * ZohoProjects Integration
 *
 */

namespace BitCode\BitForm\Core\Integration\ZohoProjects;

use BitCode\BitForm\Core\Integration\IntegrationHandler;
use BitCode\BitForm\Core\Util\HttpHelper;
use BitCode\BitForm\Core\Util\IpTool;
use WP_Error;

/**
 * Provide functionality for ZohoCrm integration
 */
class ZohoProjectsHandler
{
  private $_formID;
  private $_integrationID;

  public function __construct($integrationID, $formID)
  {
    $this->_formID = $formID;
    $this->_integrationID = $integrationID;
  }

  /**
   * Helps to register ajax function's with wp
   *
   * @return null
   */
  public static function registerAjax()
  {
    add_action('wp_ajax_bitforms_zprojects_generate_token', [__CLASS__, 'generateTokens']);
    add_action('wp_ajax_bitforms_zprojects_refresh_portals', [__CLASS__, 'refreshPortalsAjaxHelper']);
    add_action('wp_ajax_bitforms_zprojects_refresh_projects', [__CLASS__, 'refreshProjectsAjaxHelper']);
    add_action('wp_ajax_bitforms_zprojects_refresh_milestones', [__CLASS__, 'refreshMilestonesAjaxHelper']);
    add_action('wp_ajax_bitforms_zprojects_refresh_tasklists', [__CLASS__, 'refreshTasklistsAjaxHelper']);
    add_action('wp_ajax_bitforms_zprojects_refresh_tasks', [__CLASS__, 'refreshTasksAjaxHelper']);
    add_action('wp_ajax_bitforms_zprojects_refresh_fields', [__CLASS__, 'refreshFieldsAjaxHelper']);
    add_action('wp_ajax_bitforms_zprojects_refresh_users', [__CLASS__, 'refreshUsersAjaxHelper']);
    add_action('wp_ajax_bitforms_zprojects_refresh_task_layouts', [__CLASS__, 'refreshTaskLaysAjaxHelper']);
    add_action('wp_ajax_bitforms_zprojects_refresh_groups', [__CLASS__, 'refreshGroupsAjaxHelper']);
    add_action('wp_ajax_bitforms_zprojects_refresh_tags', [__CLASS__, 'refreshTagsAjaxHelper']);
  }

  /**
   * Process ajax request for generate_token
   *
   * @return string zoho crm api response and status
   */
  public static function generateTokens()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $requestsParams = json_decode($inputJSON);
      if (
        empty($requestsParams->{'accounts-server'})
        || empty($requestsParams->dataCenter)
        || empty($requestsParams->clientId)
        || empty($requestsParams->clientSecret)
        || empty($requestsParams->redirectURI)
        || empty($requestsParams->code)
      ) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }

      $apiEndpoint = \urldecode($requestsParams->{'accounts-server'}) . '/oauth/v2/token';
      $requestParams = [
        'grant_type'    => 'authorization_code',
        'client_id'     => $requestsParams->clientId,
        'client_secret' => $requestsParams->clientSecret,
        'redirect_uri'  => \urldecode($requestsParams->redirectURI),
        'code'          => $requestsParams->code
      ];
      $apiResponse = HttpHelper::post($apiEndpoint, $requestParams);

      if (is_wp_error($apiResponse) || !empty($apiResponse->error)) {
        wp_send_json_error(
          empty($apiResponse->error) ? 'Unknown' : $apiResponse->error,
          400
        );
      }
      $apiResponse->generates_on = \time();
      wp_send_json_success($apiResponse, 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public static function refreshPortalsAjaxHelper()
  {
    $authorizationHeader = null;
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);
      if (
        empty($queryParams->tokenDetails)
        || empty($queryParams->dataCenter)
        || empty($queryParams->clientId)
        || empty($queryParams->clientSecret)
      ) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }
      $response = [];
      if ((intval($queryParams->tokenDetails->generates_on) + (55 * 60)) < time()) {
        $response['tokenDetails'] = ZohoProjectsHandler::_refreshAccessToken($queryParams);
      }

      $portalsMetaApiEndpoint = "https://projectsapi.zoho.{$queryParams->dataCenter}/restapi/portals/";

      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $portalsMetaResponse = HttpHelper::get($portalsMetaApiEndpoint, null, $authorizationHeader);

      if (!is_wp_error($portalsMetaResponse)) {
        $allPortals = [];
        $portals = $portalsMetaResponse->portals;

        if (count($portals) > 0) {
          foreach ($portals as $portal) {
            $allPortals[$portal->name] = (object) [
              'portalId'   => $portal->id,
              'portalName' => $portal->name
            ];
          }
        }
        uksort($allPortals, 'strnatcasecmp');
        $response['portals'] = $allPortals;
      } else {
        wp_send_json_error(
          empty($portalsMetaResponse->data) ? 'Unknown' : $portalsMetaResponse->error,
          400
        );
      }
      if (!empty($response['tokenDetails']) && !empty($queryParams->id)) {
        ZohoProjectsHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['lists']);
      }
      wp_send_json_success($response, 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public static function refreshProjectsAjaxHelper()
  {
    $authorizationHeader = null;
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);
      if (
        empty($queryParams->tokenDetails)
        || empty($queryParams->dataCenter)
        || empty($queryParams->clientId)
        || empty($queryParams->clientSecret)
        || empty($queryParams->portalId)
      ) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }
      $response = [];
      if ((intval($queryParams->tokenDetails->generates_on) + (55 * 60)) < time()) {
        $response['tokenDetails'] = ZohoProjectsHandler::_refreshAccessToken($queryParams);
      }

      $projectsMetaApiEndpoint = "https://projectsapi.zoho.{$queryParams->dataCenter}/restapi/portal/{$queryParams->portalId}/projects/?range=1000&status=active";

      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $projectsMetaResponse = HttpHelper::get($projectsMetaApiEndpoint, null, $authorizationHeader);

      // wp_send_json_success($projectsMetaResponse, 200);

      if (!is_wp_error($projectsMetaResponse)) {
        $allProjects = [];
        $projects = $projectsMetaResponse->projects;

        if (count($projects) > 0) {
          foreach ($projects as $project) {
            $allProjects[$project->name] = (object) [
              'projectId'   => $project->id_string,
              'projectName' => $project->name
            ];
          }
        }
        uksort($allProjects, 'strnatcasecmp');
        $response['projects'] = $allProjects;
      } else {
        wp_send_json_error(
          empty($projectsMetaResponse->data) ? 'Unknown' : $projectsMetaResponse->error,
          400
        );
      }
      if (!empty($response['tokenDetails']) && !empty($queryParams->id)) {
        ZohoProjectsHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['lists']);
      }
      wp_send_json_success($response, 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public static function refreshMilestonesAjaxHelper()
  {
    $authorizationHeader = null;
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);
      if (
        empty($queryParams->tokenDetails)
        || empty($queryParams->dataCenter)
        || empty($queryParams->clientId)
        || empty($queryParams->clientSecret)
        || empty($queryParams->portalId)
        || empty($queryParams->projectId)
      ) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }
      $response = [];
      if ((intval($queryParams->tokenDetails->generates_on) + (55 * 60)) < time()) {
        $response['tokenDetails'] = ZohoProjectsHandler::_refreshAccessToken($queryParams);
      }

      $milestonesMetaApiEndpoint = "https://projectsapi.zoho.{$queryParams->dataCenter}/restapi/portal/{$queryParams->portalId}/projects/{$queryParams->projectId}/milestones/?range=1000";

      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $milestonesMetaResponse = HttpHelper::get($milestonesMetaApiEndpoint, null, $authorizationHeader);

      if (!is_wp_error($milestonesMetaResponse)) {
        $allMilestones = [];
        $milestones = $milestonesMetaResponse->milestones;

        if (count($milestones) > 0) {
          foreach ($milestones as $milestone) {
            $allMilestones[$milestone->name] = (object) [
              'milestoneId'   => $milestone->id_string,
              'milestoneName' => $milestone->name
            ];
          }
        }
        uksort($allMilestones, 'strnatcasecmp');
        $response['milestones'] = $allMilestones;
      } else {
        wp_send_json_error(
          empty($milestonesMetaResponse->data) ? 'Unknown' : $milestonesMetaResponse->error,
          400
        );
      }
      if (!empty($response['tokenDetails']) && !empty($queryParams->id)) {
        ZohoProjectsHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['lists']);
      }
      wp_send_json_success($response, 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public static function refreshTasklistsAjaxHelper()
  {
    $authorizationHeader = null;
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);
      if (
        empty($queryParams->tokenDetails)
        || empty($queryParams->dataCenter)
        || empty($queryParams->clientId)
        || empty($queryParams->clientSecret)
        || empty($queryParams->portalId)
        || empty($queryParams->projectId)
        || empty($queryParams->tasklistFlag)
      ) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }
      $response = [];
      if ((intval($queryParams->tokenDetails->generates_on) + (55 * 60)) < time()) {
        $response['tokenDetails'] = ZohoProjectsHandler::_refreshAccessToken($queryParams);
      }

      $tasklistsMetaApiEndpoint = "https://projectsapi.zoho.{$queryParams->dataCenter}/restapi/portal/{$queryParams->portalId}/projects/{$queryParams->projectId}/tasklists/?range=1000&flag={$queryParams->tasklistFlag}";

      if ($queryParams->milestoneId) {
        $tasklistsMetaApiEndpoint .= "&milestone_id={$queryParams->milestoneId}";
      }

      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $tasklistsMetaResponse = HttpHelper::get($tasklistsMetaApiEndpoint, null, $authorizationHeader);

      if (!is_wp_error($tasklistsMetaResponse)) {
        $allTasklists = [];
        $tasklists = $tasklistsMetaResponse->tasklists;

        if (count($tasklists) > 0) {
          foreach ($tasklists as $tasklist) {
            $allTasklists[$tasklist->name] = (object) [
              'tasklistId'   => $tasklist->id_string,
              'tasklistName' => $tasklist->name
            ];
          }
        }
        uksort($allTasklists, 'strnatcasecmp');
        $response['tasklists'] = $allTasklists;
      } else {
        wp_send_json_error(
          empty($tasklistsMetaResponse->data) ? 'Unknown' : $tasklistsMetaResponse->error,
          400
        );
      }
      if (!empty($response['tokenDetails']) && !empty($queryParams->id)) {
        ZohoProjectsHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['lists']);
      }
      wp_send_json_success($response, 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public static function refreshTasksAjaxHelper()
  {
    $authorizationHeader = null;
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);
      if (
        empty($queryParams->tokenDetails)
        || empty($queryParams->dataCenter)
        || empty($queryParams->clientId)
        || empty($queryParams->clientSecret)
        || empty($queryParams->portalId)
        || empty($queryParams->projectId)
      ) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }
      $response = [];
      if ((intval($queryParams->tokenDetails->generates_on) + (55 * 60)) < time()) {
        $response['tokenDetails'] = ZohoProjectsHandler::_refreshAccessToken($queryParams);
      }

      $tasksMetaApiEndpoint = "https://projectsapi.zoho.{$queryParams->dataCenter}/restapi/portal/{$queryParams->portalId}/projects/{$queryParams->projectId}/tasks/?range=1000";

      if ($queryParams->milestoneId) {
        $tasksMetaApiEndpoint .= "&milestone_id={$queryParams->milestoneId}";
      }
      if ($queryParams->tasklistId) {
        $tasksMetaApiEndpoint .= "&tasklist_id={$queryParams->tasklistId}";
      }

      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $tasksMetaResponse = HttpHelper::get($tasksMetaApiEndpoint, null, $authorizationHeader);

      if (!is_wp_error($tasksMetaResponse)) {
        $allTasks = [];
        $tasks = $tasksMetaResponse->tasks;

        if (count($tasks) > 0) {
          foreach ($tasks as $task) {
            $allTasks[$task->name] = (object) [
              'taskId'   => $task->id_string,
              'taskName' => $task->name
            ];
          }
        }
        uksort($allTasks, 'strnatcasecmp');
        $response['tasks'] = $allTasks;
      } else {
        wp_send_json_error(
          empty($tasksMetaResponse->data) ? 'Unknown' : $tasksMetaResponse->error,
          400
        );
      }
      if (!empty($response['tokenDetails']) && !empty($queryParams->id)) {
        ZohoProjectsHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['lists']);
      }
      wp_send_json_success($response, 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public static function refreshFieldsAjaxHelper()
  {
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);
      if (
        empty($queryParams->tokenDetails)
        || empty($queryParams->dataCenter)
        || empty($queryParams->clientId)
        || empty($queryParams->clientSecret)
        || empty($queryParams->portalId)
        || empty($queryParams->event)
      ) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }
      $response = [];
      if ((intval($queryParams->tokenDetails->generates_on) + (55 * 60)) < time()) {
        $response['tokenDetails'] = ZohoProjectsHandler::_refreshAccessToken($queryParams);
      }

      if ('project' === $queryParams->event) {
        $response = ZohoProjectsHandler::getProjectFields($queryParams->dataCenter, $queryParams->portalId, $queryParams->tokenDetails->access_token);
      } elseif ('milestone' === $queryParams->event) {
        $response = ZohoProjectsHandler::getMilestoneFields();
      } elseif ('tasklist' === $queryParams->event) {
        $response = ZohoProjectsHandler::getTasklistFields();
      } elseif ('task' === $queryParams->event || 'subtask' === $queryParams->event) {
        $response = ZohoProjectsHandler::getTaskFields($queryParams->dataCenter, $queryParams->portalId, $queryParams->tokenDetails->access_token, $queryParams->projectId);
      } elseif ('issue' === $queryParams->event) {
        $response = ZohoProjectsHandler::getIssueFields($queryParams->dataCenter, $queryParams->portalId, $queryParams->tokenDetails->access_token, $queryParams->projectId);
      }

      if (!empty($response['tokenDetails']) && $response['tokenDetails'] && !empty($queryParams->id)) {
        $response['queryModule'] = $queryParams->module;
        ZohoProjectsHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response);
      }
      wp_send_json_success($response, 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  protected static function getProjectFields($dataCenter, $portalId, $access_token)
  {
    $allFields = [
      'Project Title' => [
        'apiName'      => 'name',
        'displayLabel' => 'Project Title',
        'required'     => true
      ],
      'Project Overview' => [
        'apiName'      => 'description',
        'displayLabel' => 'Project Overview'
      ],
      'Start Date' => [
        'apiName'      => 'start_date',
        'displayLabel' => 'Start Date'
      ],
      'End Date' => [
        'apiName'      => 'end_date',
        'displayLabel' => 'End Date'
      ],
      'Strict Project' => [
        'apiName'      => 'strict_project',
        'displayLabel' => 'Strict Project'
      ],
      'Project Budget' => [
        'apiName'      => 'budget_type',
        'displayLabel' => 'Project Budget'
      ],
      'Budget Amount' => [
        'apiName'      => 'budget_value',
        'displayLabel' => 'Budget Amount'
      ],
      'Billing Method' => [
        'apiName'      => 'billing_method',
        'displayLabel' => 'Billing Method'
      ],
      'Currency' => [
        'apiName'      => 'currency',
        'displayLabel' => 'Currency'
      ],
      'Threshold Limit' => [
        'apiName'      => 'threshold',
        'displayLabel' => 'Threshold Limit'
      ],
      'Rate Per Hour' => [
        'apiName'      => 'project_rate',
        'displayLabel' => 'Rate Per Hour'
      ],
      'Fixed Cost' => [
        'apiName'      => 'fixed_cost',
        'displayLabel' => 'Fixed Cost'
      ],
      'Bill Status' => [
        'apiName'      => 'bill_status',
        'displayLabel' => 'Bill Status'
      ]
    ];

    $response['required'][] = 'name';

    $customFieldsMetaApiEndpoint = "https://projectsapi.zoho.{$dataCenter}/restapi/portal/{$portalId}/projects/customfields/";
    $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$access_token}";
    $customFieldsMetaResponse = HttpHelper::get($customFieldsMetaApiEndpoint, null, $authorizationHeader);

    if (!is_wp_error($customFieldsMetaResponse)) {
      $fields = $customFieldsMetaResponse->project_custom_fields;

      if (count($fields) > 0) {
        foreach ($fields as $field) {
          $allFields[$field->field_name] = (object) [
            'apiName'      => $field->field_id,
            'displayLabel' => $field->field_name
          ];

          if ($field->is_mandatory) {
            $response['required'][] = $field->field_id;
          }
        }
      }
    }

    $response['fields'] = $allFields;
    uksort($response['fields'], 'strnatcasecmp');
    usort($response['required'], 'strnatcasecmp');

    return $response;
  }

  protected static function getMilestoneFields()
  {
    $allFields = [
      'Milestone Title' => [
        'apiName'      => 'name',
        'displayLabel' => 'Milestone Title',
        'required'     => true
      ],
      'Start Date' => [
        'apiName'      => 'start_date',
        'displayLabel' => 'Start Date',
        'required'     => true
      ],
      'End Date' => [
        'apiName'      => 'end_date',
        'displayLabel' => 'End Date',
        'required'     => true
      ]
    ];

    $response['required'] = ['name', 'start_date', 'end_date'];

    $response['fields'] = $allFields;
    uksort($response['fields'], 'strnatcasecmp');
    usort($response['required'], 'strnatcasecmp');

    return $response;
  }

  protected static function getTasklistFields()
  {
    $allFields = [
      'Tasklist Title' => [
        'apiName'      => 'name',
        'displayLabel' => 'Tasklist Title',
        'required'     => true
      ]
    ];

    $response['required'] = ['name'];

    $response['fields'] = $allFields;
    uksort($response['fields'], 'strnatcasecmp');
    usort($response['required'], 'strnatcasecmp');

    return $response;
  }

  protected static function getTaskFields($dataCenter, $portalId, $access_token, $projectId)
  {
    $allFields = [
      'Task Title' => [
        'apiName'      => 'name',
        'displayLabel' => 'Task Title',
        'required'     => true
      ],
      'Task Description' => [
        'apiName'      => 'description',
        'displayLabel' => 'Task Description'
      ],
      'Start Date' => [
        'apiName'      => 'start_date',
        'displayLabel' => 'Start Date'
      ],
      'End Date' => [
        'apiName'      => 'end_date',
        'displayLabel' => 'End Date'
      ],
      'Duration' => [
        'apiName'      => 'duration',
        'displayLabel' => 'Duration'
      ],
      'Duration Type' => [
        'apiName'      => 'duration_type',
        'displayLabel' => 'Duration Type'
      ],
      'Start Time' => [
        'apiName'      => 'start_time',
        'displayLabel' => 'Start Time'
      ],
      'End Time' => [
        'apiName'      => 'end_time',
        'displayLabel' => 'End Time'
      ],
      'Rate Per Hour' => [
        'apiName'      => 'rate_per_hour',
        'displayLabel' => 'Rate Per Hour'
      ],
      'Priority' => [
        'apiName'      => 'priority',
        'displayLabel' => 'Priority'
      ]
    ];

    $response['required'][] = 'name';

    // for associate with project
    $authorizationHeader = null;
    if ($projectId) {
      $customFieldsMetaApiEndpoint = "https://projectsapi.zoho.{$dataCenter}/restapi/portal/{$portalId}/projects/{$projectId}/tasklayouts";
      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$access_token}";
      $customFieldsMetaResponse = HttpHelper::get($customFieldsMetaApiEndpoint, null, $authorizationHeader);

      if (!is_wp_error($customFieldsMetaResponse)) {
        $sections = $customFieldsMetaResponse->section_details;

        if (count($sections) > 0) {
          foreach ($sections as $section) {
            $fields = $section->customfield_details;
            foreach ($fields as $field) {
              if (!$field->is_default) {
                $allFields[$field->display_name] = (object) [
                  'apiName'      => 'cf_' . $field->column_name,
                  'displayLabel' => $field->display_name
                ];
                if ($field->is_mandatory) {
                  $response['required'][] = 'cf_' . $field->column_name;
                }
              }
            }
          }
        }
      }
    }

    $response['fields'] = $allFields;
    uksort($response['fields'], 'strnatcasecmp');
    usort($response['required'], 'strnatcasecmp');

    return $response;
  }

  protected static function getIssueFields($dataCenter, $portalId, $access_token, $projectId)
  {
    $allFields = [
      'Issue Title' => [
        'apiName'      => 'title',
        'displayLabel' => 'Issue Title',
        'required'     => true
      ],
      'Description' => [
        'apiName'      => 'description',
        'displayLabel' => 'Description'
      ],
      'Due Date' => [
        'apiName'      => 'due_date',
        'displayLabel' => 'Due Date'
      ],
      'Rate Per Hour' => [
        'apiName'      => 'rate_per_hour',
        'displayLabel' => 'Rate Per Hour'
      ]
    ];

    $response['required'] = ['title'];

    // for associate with project
    $authorizationHeader = null;
    if ($projectId) {
      // Custom Fields
      $customFieldsMetaApiEndpoint = "https://projectsapi.zoho.{$dataCenter}/restapi/portal/{$portalId}/projects/{$projectId}/bugs/customfields/";
      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$access_token}";
      $customFieldsMetaResponse = HttpHelper::get($customFieldsMetaApiEndpoint, null, $authorizationHeader);

      if (!is_wp_error($customFieldsMetaResponse)) {
        $fields = $customFieldsMetaResponse->customfields;

        if (count($fields) > 0) {
          foreach ($fields as $field) {
            $allFields[$field->label_name] = (object) [
              'apiName'      => $field->column_name,
              'displayLabel' => $field->label_name
            ];

            if ('true' === $field->is_mandatory) {
              $response['required'][] = $field->column_name;
            }
          }
        }
      }

      // Default Fields
      $defaultFieldsMetaApiEndpoint = "https://projectsapi.zoho.{$dataCenter}/restapi/portal/{$portalId}/projects/{$projectId}/bugs/defaultfields/";
      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$access_token}";
      $defaultFieldsMetaResponse = HttpHelper::get($defaultFieldsMetaApiEndpoint, null, $authorizationHeader);

      if (!is_wp_error($defaultFieldsMetaResponse)) {
        $response['defaultfields'] = $defaultFieldsMetaResponse->defaultfields;
      }
    }

    $response['fields'] = $allFields;
    uksort($response['fields'], 'strnatcasecmp');
    usort($response['required'], 'strnatcasecmp');

    return $response;
  }

  public static function refreshUsersAjaxHelper()
  {
    $authorizationHeader = null;
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);
      if (
        empty($queryParams->tokenDetails)
        || empty($queryParams->dataCenter)
        || empty($queryParams->clientId)
        || empty($queryParams->clientSecret)
        || empty($queryParams->portalId)
      ) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }
      $response = [];
      if ((intval($queryParams->tokenDetails->generates_on) + (55 * 60)) < time()) {
        $response['tokenDetails'] = ZohoProjectsHandler::_refreshAccessToken($queryParams);
      }

      $usersMetaApiEndpoint = '';

      if ($queryParams->projectId) {
        $usersMetaApiEndpoint = "https://projectsapi.zoho.{$queryParams->dataCenter}/restapi/portal/{$queryParams->portalId}/projects/{$queryParams->projectId}/users/";
      } else {
        $usersMetaApiEndpoint = "https://projectsapi.zoho.{$queryParams->dataCenter}/restapi/portal/{$queryParams->portalId}/users/";
      }

      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $usersMetaResponse = HttpHelper::get($usersMetaApiEndpoint, null, $authorizationHeader);

      // wp_send_json_success($usersMetaResponse, 200);

      if (!is_wp_error($usersMetaResponse)) {
        $users = $usersMetaResponse->users;

        if (count($users) > 0) {
          foreach ($users as $user) {
            $response['users'][] = (object) [
              'userId'    => $user->id,
              'userName'  => $user->name,
              'userEmail' => $user->email,
            ];
          }
        }
      } else {
        wp_send_json_error(
          empty($usersMetaResponse->data) ? 'Unknown' : $usersMetaResponse->error,
          400
        );
      }
      if (!empty($response['tokenDetails']) && !empty($queryParams->id)) {
        ZohoProjectsHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['lists']);
      }
      wp_send_json_success($response, 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public static function refreshTaskLaysAjaxHelper()
  {
    $authorizationHeader = null;
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);
      if (
        empty($queryParams->tokenDetails)
        || empty($queryParams->dataCenter)
        || empty($queryParams->clientId)
        || empty($queryParams->clientSecret)
        || empty($queryParams->portalId)
      ) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }
      $response = [];
      if ((intval($queryParams->tokenDetails->generates_on) + (55 * 60)) < time()) {
        $response['tokenDetails'] = ZohoProjectsHandler::_refreshAccessToken($queryParams);
      }

      $taskLaysMetaApiEndpoint = "https://projectsapi.zoho.{$queryParams->dataCenter}/restapi/portal/{$queryParams->portalId}/tasklayouts";

      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $taskLaysMetaResponse = HttpHelper::get($taskLaysMetaApiEndpoint, null, $authorizationHeader);

      // wp_send_json_success($taskLaysMetaResponse, 200);

      if (!is_wp_error($taskLaysMetaResponse)) {
        $taskLays = $taskLaysMetaResponse->layouts;

        if (count($taskLays) > 0) {
          foreach ($taskLays as $taskLay) {
            $response['taskLays'][] = (object) [
              'taskLayId'   => $taskLay->layout_id,
              'taskLayName' => $taskLay->layout_name
            ];
          }
        }
      } else {
        wp_send_json_error(
          empty($taskLaysMetaResponse->data) ? 'Unknown' : $taskLaysMetaResponse->error,
          400
        );
      }
      if (!empty($response['tokenDetails']) && !empty($queryParams->id)) {
        ZohoProjectsHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['lists']);
      }
      wp_send_json_success($response, 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public static function refreshGroupsAjaxHelper()
  {
    $authorizationHeader = null;
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);
      if (
        empty($queryParams->tokenDetails)
        || empty($queryParams->dataCenter)
        || empty($queryParams->clientId)
        || empty($queryParams->clientSecret)
        || empty($queryParams->portalId)
      ) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }
      $response = [];
      if ((intval($queryParams->tokenDetails->generates_on) + (55 * 60)) < time()) {
        $response['tokenDetails'] = ZohoProjectsHandler::_refreshAccessToken($queryParams);
      }

      $groupsMetaApiEndpoint = "https://projectsapi.zoho.{$queryParams->dataCenter}/restapi/portal/{$queryParams->portalId}/projects/groups";

      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $groupsMetaResponse = HttpHelper::get($groupsMetaApiEndpoint, null, $authorizationHeader);

      // wp_send_json_success($groupsMetaResponse, 200);

      if (!is_wp_error($groupsMetaResponse)) {
        $groups = $groupsMetaResponse->groups;

        if (count($groups) > 0) {
          foreach ($groups as $group) {
            $response['groups'][] = (object) [
              'groupId'   => $group->id,
              'groupName' => $group->name
            ];
          }
        }
      } else {
        wp_send_json_error(
          empty($groupsMetaResponse->data) ? 'Unknown' : $groupsMetaResponse->error,
          400
        );
      }
      if (!empty($response['tokenDetails']) && !empty($queryParams->id)) {
        ZohoProjectsHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['lists']);
      }
      wp_send_json_success($response, 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  public static function refreshTagsAjaxHelper()
  {
    $authorizationHeader = null;
    if (isset($_REQUEST['_ajax_nonce']) && wp_verify_nonce($_REQUEST['_ajax_nonce'], 'bitforms_save')) {
      $inputJSON = file_get_contents('php://input');
      $queryParams = json_decode($inputJSON);
      if (
        empty($queryParams->tokenDetails)
        || empty($queryParams->dataCenter)
        || empty($queryParams->clientId)
        || empty($queryParams->clientSecret)
        || empty($queryParams->portalId)
      ) {
        wp_send_json_error(
          __(
            'Requested parameter is empty',
            'bit-form'
          ),
          400
        );
      }

      $response = [];
      if ((intval($queryParams->tokenDetails->generates_on) + (55 * 60)) < time()) {
        $response['tokenDetails'] = ZohoProjectsHandler::_refreshAccessToken($queryParams);
      }

      $tagsMetaApiEndpoint = "https://projectsapi.zoho.{$queryParams->dataCenter}/api/v3/portal/{$queryParams->portalId}/tags";

      $authorizationHeader['Authorization'] = "Zoho-oauthtoken {$queryParams->tokenDetails->access_token}";
      $tagsMetaResponse = HttpHelper::get($tagsMetaApiEndpoint, null, $authorizationHeader);

      // wp_send_json_success($tagsMetaResponse, 200);

      if (!is_wp_error($tagsMetaResponse)) {
        $tags = $tagsMetaResponse->tags;

        if (count($tags) > 0) {
          $allTags = [];
          foreach ($tags as $tag) {
            $allTags[$tag->name] = (object) [
              'tagId'   => $tag->id,
              'tagName' => $tag->name
            ];
          }
          uksort($allTags, 'strnatcasecmp');
          $response['tags'] = $allTags;
        }
      } else {
        wp_send_json_error(
          empty($tagsMetaResponse->data) ? 'Unknown' : $tagsMetaResponse->error,
          400
        );
      }
      if (!empty($response['tokenDetails']) && !empty($queryParams->id)) {
        ZohoProjectsHandler::_saveRefreshedToken($queryParams->formID, $queryParams->id, $response['tokenDetails'], $response['lists']);
      }
      wp_send_json_success($response, 200);
    } else {
      wp_send_json_error(
        __(
          'Token expired',
          'bit-form'
        ),
        401
      );
    }
  }

  protected static function _refreshAccessToken($apiData)
  {
    if (
      empty($apiData->dataCenter)
      || empty($apiData->clientId)
      || empty($apiData->clientSecret)
      || empty($apiData->tokenDetails)
    ) {
      return false;
    }
    $tokenDetails = $apiData->tokenDetails;

    $dataCenter = $apiData->dataCenter;
    $apiEndpoint = "https://accounts.zoho.{$dataCenter}/oauth/v2/token";
    $requestParams = [
      'grant_type'    => 'refresh_token',
      'client_id'     => $apiData->clientId,
      'client_secret' => $apiData->clientSecret,
      'refresh_token' => $tokenDetails->refresh_token,
    ];

    $apiResponse = HttpHelper::post($apiEndpoint, $requestParams);
    if (is_wp_error($apiResponse) || !empty($apiResponse->error)) {
      return false;
    }
    $tokenDetails->generates_on = \time();
    $tokenDetails->access_token = $apiResponse->access_token;
    return $tokenDetails;
  }

  /**
   * Save updated access_token to avoid unnecessary token generation
   *
   * @param integer $formID        ID of Integration related form
   * @param integer $integrationID ID of Zoho crm Integration
   * @param object $tokenDetails  refreshed token info
   *
   * @return null
   */
  protected static function _saveRefreshedToken($formID, $integrationID, $tokenDetails, $others = null)
  {
    if (empty($formID) || empty($integrationID)) {
      return;
    }

    $integrationHandler = new IntegrationHandler($formID, IpTool::getUserDetail());
    $zprojectsDetails = $integrationHandler->getAIntegration($integrationID);

    if (is_wp_error($zprojectsDetails)) {
      return;
    }
    $newDetails = json_decode($zprojectsDetails[0]->integration_details);

    $newDetails->tokenDetails = $tokenDetails;
    if (!empty($others['portals'])) {
      $newDetails->default->portals = $others['portals'];
    }

    $integrationHandler->updateIntegration($integrationID, $zprojectsDetails[0]->integration_name, 'Zoho Projects', wp_json_encode($newDetails), 'form');
  }

  public function execute(IntegrationHandler $integrationHandler, $integrationData, $fieldValues, $entryID, $logID)
  {
    $integrationDetails = is_string($integrationData->integration_details) ? json_decode($integrationData->integration_details) : $integrationData->integration_details;

    $tokenDetails = $integrationDetails->tokenDetails;
    $portalId = $integrationDetails->portalId;
    $dataCenter = $integrationDetails->dataCenter;
    $fieldMap = $integrationDetails->field_map;
    if (
      empty($tokenDetails)
      || empty($portalId)
      || empty($fieldMap)
    ) {
      return new WP_Error('REQ_FIELD_EMPTY', __('list are required for zoho projects api', 'bit-form'));
    }

    $requiredParams = null;
    if ((intval($tokenDetails->generates_on) + (55 * 60)) < time()) {
      $requiredParams['clientId'] = $integrationDetails->clientId;
      $requiredParams['clientSecret'] = $integrationDetails->clientSecret;
      $requiredParams['dataCenter'] = $integrationDetails->dataCenter;
      $requiredParams['tokenDetails'] = $tokenDetails;
      $newTokenDetails = ZohoProjectsHandler::_refreshAccessToken((object)$requiredParams);
      if ($newTokenDetails) {
        ZohoProjectsHandler::_saveRefreshedToken($this->_formID, $this->_integrationID, $newTokenDetails);
        $tokenDetails = $newTokenDetails;
      }
    }

    $recordApiHelper = new RecordApiHelper($tokenDetails, $this->_integrationID, $logID);

    $zprojectsApiResponse = $recordApiHelper->executeRecordApi(
      $this->_formID,
      $entryID,
      $integrationDetails,
      $dataCenter,
      $fieldMap,
      $fieldValues
    );

    if (is_wp_error($zprojectsApiResponse)) {
      return $zprojectsApiResponse;
    }
    return $zprojectsApiResponse;
  }
}
