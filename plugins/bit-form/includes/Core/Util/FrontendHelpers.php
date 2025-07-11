<?php

namespace BitCode\BitForm\Core\Util;

use BitCode\BitForm\Core\Form\FormManager;
use WP_Rewrite;

final class FrontendHelpers
{
  public static $isPageBuilder = false;
  public static $bfFrontendFormIds = [];
  public static $bfFrontendViewIds = [];
  public static $bfFormIdsFromPost = [];
  public static $bfViewIdsFromPost = [];
  private static $formsPermissions = [];

  public static $pageBuilderQueryParamsList = [
    'et_pb_preview'                   => 'true', // divi
    'vc_editable'                     => 'true', // wp bakery
    'action'                          => 'ct_render_shortcode' // oxygen
  ];

  public static $pageBuilderURLParamsList = [
    'wp-json/bricks/v1/render', // bricks
  ];

  public static $pageBuilderRefererQueryParamsList = [
    'breakdance'                     => 'builder', // breakdance
  ];

  public static function getFormIdsFromPost()
  {
    global $post;
    global $wpdb;
    if (empty($post)) {
      self::$bfFormIdsFromPost = [];
      return [];
    }
    $postId = $post->ID;
    $shortcodeFormIds = [];
    $bfMetaValues = $wpdb->get_results(
      $wpdb->prepare(
        'SELECT meta_value FROM `' . $wpdb->postmeta . '` WHERE `post_id`=%d',
        $postId
      )
    );
    $postContent = $post->post_content;
    $bfMetaValues[] = (object) ['meta_value' => $postContent];
    foreach ($bfMetaValues as $bfShortcut) {
      $meta_value = (is_string($bfShortcut->meta_value) && !empty($bfShortcut->meta_value)) ? $bfShortcut->meta_value : '';
      $shortcodeIds = self::getShortCodeIds($meta_value);
      $shortcodeFormIds = array_merge($shortcodeFormIds, $shortcodeIds);
    }

    self::$bfFormIdsFromPost = $shortcodeFormIds;
    return $shortcodeFormIds;
  }

  public static function getShortCodeIds($content = '')
  {
    \preg_match_all("/\[bitform\s+id\s*=\s*('|\")\s*(\d+)\s*('|\")\]/", $content, $shortCode);
    $ids = $shortCode[2];

    return $ids;
  }

  public static function getViewIdsFromPost()
  {
    global $post;
    global $wpdb;
    if (empty($post)) {
      self::$bfViewIdsFromPost = [];
      return [];
    }
    $postId = $post->ID;
    $shortcodeViewIds = [];
    $bfMetaValues = $wpdb->get_results('SELECT meta_value FROM `' . $wpdb->postmeta . "` WHERE `post_id`={$postId}");
    $postContent = $post->post_content;

    $bfMetaValues[] = (object) ['meta_value' => $postContent];
    foreach ($bfMetaValues as $bfShortcut) {
      $meta_value = (is_string($bfShortcut->meta_value) && !empty($bfShortcut->meta_value)) ? $bfShortcut->meta_value : '';
      $shortcodeIds = self::getViewShortCodeIds($meta_value);
      $shortcodeViewIds = array_merge($shortcodeViewIds, $shortcodeIds);
    }

    self::$bfViewIdsFromPost = $shortcodeViewIds;
    return $shortcodeViewIds;
  }

  public static function getViewShortCodeIds($content = '')
  {
    \preg_match_all("/\[bitform-view\s+id\s*=\s*('|\")\s*(\d+)\s*('|\")\]/", $content, $shortCode);
    $ids = $shortCode[2];

    return $ids;
  }

  public static function checkIsPageBuilder($srvr)
  {
    if (is_admin()) {
      self::$isPageBuilder = true;
      return true;
    }
    $current_url = $srvr['REQUEST_URI'];
    $queryParams = self::parseQueryParams($current_url);
    foreach (self::$pageBuilderQueryParamsList as $key => $value) {
      if (isset($queryParams[$key]) && $queryParams[$key] === $value) {
        self::$isPageBuilder = true;
        return true;
      }
    }
    foreach (self::$pageBuilderURLParamsList as $value) {
      if (false !== strpos($current_url, $value)) {
        self::$isPageBuilder = true;
        return true;
      }
    }

    $referrer = isset($srvr['HTTP_REFERER']) ? $srvr['HTTP_REFERER'] : '';
    $referrerQueryParams = self::parseQueryParams($referrer);
    foreach (self::$pageBuilderRefererQueryParamsList as $key => $value) {
      if (isset($referrerQueryParams[$key]) && $referrerQueryParams[$key] === $value) {
        self::$isPageBuilder = true;
        return true;
      }
    }

    return self::$isPageBuilder;
  }

  public static function parseQueryParams($url)
  {
    $url_components = wp_parse_url($url);
    if (isset($url_components['query'])) {
      parse_str($url_components['query'], $queryParams);
      return $queryParams;
    }
    return [];
  }

  public static function isRestRequest()
  {
    $prefix = rest_get_url_prefix();
    if (defined('REST_REQUEST') && REST_REQUEST
    || isset($_GET['rest_route'])
    && 0 === strpos(trim($_GET['rest_route'], '\\/'), $prefix, 0)) {
      return true;
    }
    global $wp_rewrite;
    if (null === $wp_rewrite) {
      $wp_rewrite = new WP_Rewrite();
    }
    $rest_url = wp_parse_url(trailingslashit(rest_url()));
    $current_url = wp_parse_url(add_query_arg([]));
    return 0 === strpos($current_url['path'], $rest_url['path'], 0);
  }

  public static function isAdminRequest()
  {
    $current_url = home_url(add_query_arg(null, null));
    $admin_url = strtolower(admin_url());
    $referrer = strtolower(wp_get_referer());

    $requestFromBackend = self::isRestRequest() && strpos($admin_url, '/wp-admin/') > 0 && !strpos($admin_url, '/wp-admin/admin-ajax.php');

    if ($requestFromBackend) {
      return true;
    }

    if (0 === strpos($current_url, $admin_url)) {
      if (0 === strpos($referrer, $admin_url)) {
        return true;
      } else {
        if (function_exists('wp_doing_ajax')) {
          return !wp_doing_ajax();
        } else {
          return !(defined('DOING_AJAX') && DOING_AJAX);
        }
      }
    } else {
      return false;
    }
  }

  public static function parseUrlParams($url)
  {
    $url_components = wp_parse_url($url);
    if (isset($url_components['path'])) {
      $urlParams = explode('/', $url_components['path']);
      return $urlParams;
    }

    return [];
  }

  public static function setBfFrontendFormIds($formId)
  {
    self::$bfFrontendFormIds[] = $formId;
  }

  public static function getAllFormIdsInPage()
  {
    $bfFrontendFormIds = self::$bfFrontendFormIds;
    $bfFormIdsFromPost = self::getFormIdsFromPost();
    $allFormIds = array_merge($bfFrontendFormIds, $bfFormIdsFromPost);
    return $allFormIds;
  }

  public static function getAllViewIdsInPage()
  {
    $bfFrontendViewIds = self::$bfFrontendViewIds;
    $bfViewIdsFromPost = self::getViewIdsFromPost();
    $allViewIds = array_merge($bfFrontendViewIds, $bfViewIdsFromPost);
    return $allViewIds;
  }

  public static function getAllUniqFormIdsInPage()
  {
    return array_unique(self::getAllFormIdsInPage());
  }

  public static function hasMultipleForms()
  {
    $bfUniqFormIds = self::getAllFormIdsInPage();
    self::checkIsPageBuilder($_SERVER);
    $isPageBuilder = self::$isPageBuilder;
    $bfMultipleFormsExists = $isPageBuilder ? true : count($bfUniqFormIds) > 1;
    return $bfMultipleFormsExists;
  }

  public static function getFormPermissions($formId)
  {
    if (!isset(self::$formsPermissions[$formId])) {
      $formManager = FormManager::getInstance($formId);
      self::$formsPermissions[$formId] = $formManager->getFormPermission();
    }

    return self::$formsPermissions[$formId];
  }

  public static function is_current_user_can_access($formId, $action = 'entryViewAccess', $scope = '', $entryUserId = '')
  {
    $formPermissions = self::getFormPermissions($formId);
    $accessPermission = isset($formPermissions->{$action}) ? $formPermissions->{$action} : null;
    if (empty($accessPermission)) {
      return false;
    }
    if ('entryViewAccess' === $action && (!isset($accessPermission->preventPublicAccess) || !$accessPermission->preventPublicAccess)) {
      return true;
    }
    if (is_user_logged_in()) {
      $user = wp_get_current_user();
      $userId = (string) $user->ID;
      if (in_array('administrator', $user->roles) || current_user_can('manage_bitform')) {
        return true;
      }
      if ('entryEditAccess' === $action && !(isset($accessPermission->allowEntriesEdit) && $accessPermission->allowEntriesEdit)) {
        return false;
      }
      if (!empty($scope) && !empty($accessPermission->{$scope}) && is_string($accessPermission->{$scope})) {
        $accessRolesArray = explode(',', $accessPermission->{$scope});
        if (self::has_access_for_roles($user, $accessRolesArray) && empty($entryUserId)) {
          return true;
        }
        if (!empty($entryUserId) && (('ownEntries' === $scope && $userId === $entryUserId) || ('othersEntries' === $scope && $userId !== $entryUserId))) {
          return true;
        }
      }

      if (empty($scope) && isset($accessPermission->ownEntries) && !empty($accessPermission->ownEntries) && is_string($accessPermission->ownEntries)) {
        $accessRolesArray = explode(',', $accessPermission->ownEntries);
        if (self::has_access_for_roles($user, $accessRolesArray) && !empty($entryUserId) && $userId === $entryUserId) {
          return true;
        }
        if (self::has_access_for_roles($user, $accessRolesArray) && empty($entryUserId)) {
          return true;
        }
      }

      if (empty($scope) && isset($accessPermission->othersEntries) && !empty($accessPermission->othersEntries) && is_string($accessPermission->othersEntries)) {
        $accessRolesArray = explode(',', $accessPermission->othersEntries);
        if (self::has_access_for_roles($user, $accessRolesArray) && !empty($entryUserId) && $userId !== $entryUserId) {
          return true;
        }
        if (self::has_access_for_roles($user, $accessRolesArray) && empty($entryUserId)) {
          return true;
        }
      }
    }
    return false;
  }

  private static function has_access_for_roles($user, $accessRoles)
  {
    // If "all_logged_in_users" is in the allowed roles, grant access
    if (in_array('all_logged_in_users', $accessRoles)) {
      return true;
    }
    // Check if any of the user's roles match the allowed roles
    $userRoles = array_intersect($user->roles, $accessRoles);
    return !empty($userRoles);
  }
}
