<?php

namespace WPBannerize\WPBones\Routing;

if (!defined('ABSPATH')) {
  exit();
}

use WPBannerize\WPBones\Support\ServiceProvider;
use WPBannerize\WPBones\Support\Str;

/**
 * Class AdminMenuProvider
 *
 * This class is a service provider for the WP Bones framework.
 * Here we're going to read the menu configuration file and register the menus.
 *
 * @package WPBannerize\WPBones\Routing
 */

class AdminMenuProvider extends ServiceProvider
{
  // register
  public function register()
  {
    $menus = include_once "{$this->plugin->basePath}/config/menus.php";

    if (!empty($menus) && is_array($menus)) {
      foreach ($menus as $topLevelSlug => $menu) {
        // sanitize array keys
        $menu['position'] = isset($menu['position']) ? $menu['position'] : null;
        $menu['capability'] = isset($menu['capability']) ? $menu['capability'] : 'read';
        $menu['icon'] = isset($menu['icon']) ? $menu['icon'] : '';
        $page_title = isset($menu['page_title']) ? $menu['page_title'] : $menu['menu_title'];
        $menu['page_title'] = sanitize_title($page_title);

        // icon
        $icon = $menu['icon'];
        $hasImage = false;

        if (isset($menu['icon']) && !empty($menu['icon']) && 'dashicons' != substr($menu['icon'], 0, 9) && 'data:' != substr($menu['icon'], 0, 5)) {
          $icon = $this->plugin->images . '/' . $menu['icon'];
          $hasImage = true;
        }

        $firstMenu = true;

        if (substr($topLevelSlug, 0, 8) !== 'edit.php') {
          $suffix = add_menu_page(
            $menu['page_title'],
            $menu['menu_title'],
            $menu['capability'],
            $topLevelSlug,
            '',
            $icon,
            $menu['position']
          );

          if ($hasImage) {
            add_action('admin_head', function () use ($suffix) {
              echo '<style>li.' . $suffix . ' div.wp-menu-image img {padding:6px 0 !important;}</style>';
            });
          }
        } else {
          $firstMenu = false;
        }

        foreach ($menu['items'] as $key => $subMenu) {
          if (is_null($subMenu)) {
            continue;
          }

          // index 0
          if (empty($key)) {
            $key = '0';
          }

          // sanitize array keys
          $subMenu['capability'] = isset($subMenu['capability']) ? $subMenu['capability'] : $menu['capability'];
          $subMenu['page_title'] = isset($subMenu['page_title']) ? $subMenu['page_title'] : $subMenu['menu_title'];

          // key could be a number
          $key = str_replace('-', '_', sanitize_title($key));

          $array = explode('\\', __NAMESPACE__);
          $namespace = sanitize_title($array[0]);

          // submenu slug
          $submenuSlug = "{$namespace}_{$key}";

          if ($firstMenu) {
            $firstMenu = false;
            $submenuSlug = $topLevelSlug;
          }

          // get hook
          $hook = $this->plugin->getCallableHook($subMenu['route']);

          $subMenuHook = add_submenu_page(
            $topLevelSlug,
            $subMenu['page_title'],
            $subMenu['menu_title'],
            $subMenu['capability'],
            $submenuSlug,
            $hook
          );

          if (isset($subMenu['route']['load'])) {
            [$controller, $method] = Str::parseCallback($subMenu['route']['load']);

            add_action("load-{$subMenuHook}", function () use ($controller, $method) {
              $className = "WPBannerize\\Http\\Controllers\\{$controller}";
              $instance = new $className();

              return $instance->{$method}();
            });
          }

          if (isset($subMenu['route']['resource'])) {
            $controller = $subMenu['route']['resource'];

            add_action("load-{$subMenuHook}", function () use ($controller) {
              $className = "WPBannerize\\Http\\Controllers\\{$controller}";
              $instance = new $className();
              if (method_exists($instance, 'load')) {
                return $instance->load();
              }
            });
          }
        }
      }
    }
  }
}
