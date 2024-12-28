<?php

namespace WPBannerize\WPBones\Foundation;

use WPBannerize\WPBones\Foundation\Http\Request;
use WPBannerize\WPBones\Support\ServiceProvider;

if (!defined('ABSPATH')) {
  exit();
}

abstract class WordPressCustomPostTypeServiceProvider extends ServiceProvider
{
  /**
   * Post type key. Must not exceed 20 characters and may only contain
   * lowercase alphanumeric characters, dashes, and underscores. See sanitize_key().
   *
   * `register_post_type( $post_type, $args = array() )`
   *
   * @var string
   */
  protected $id = '';

  /**
   * Name of the post type shown in the menu. Usually plural.
   *
   * @var string
   */
  protected $name = '';

  /**
   * Name of the post type shown in the menu as plural.
   *
   * @var string
   */
  protected $plural = '';

  /**
   * An array of labels for this post type.
   * If not set, post labels are inherited for non-hierarchical types and page labels for hierarchical ones.
   * You can see accepted values in {@link get_post_type_labels()}.
   *
   * @var array
   */
  protected $labels = [];

  /**
   * A short descriptive summary of what the post type is. Defaults to blank.
   *
   * @var string
   */
  protected $description = '';

  /**
   * Whether a post type is intended for use publicly either via the admin interface or by front-end users.
   * Defaults to false.
   * While the default settings of exclude_from_search, publicly_queryable, show_ui, and show_in_nav_menus are
   * inherited from public, each does not rely on this relationship and controls a very specific intention.
   *
   * @var bool
   */
  protected $public = false;

  /**
   * Whether the post type is hierarchical (e.g. page).
   * Defaults to false.
   *
   * @var bool
   */
  protected $hierarchical;

  /**
   * Whether to exclude posts with this post type from front end search results.
   * If not set, the opposite of public's current value is used.
   *
   * @var bool
   */
  protected $excludeFromSearch;

  /**
   * Whether queries can be performed on the front end for the post type as part of parse_request().
   *
   * ?post_type={post_type_key}
   * ?{post_type_key}={single_post_slug}
   * ?{post_type_query_var}={single_post_slug}
   *
   * If not set, the default is inherited from public.
   *
   * @var bool
   */
  protected $publiclyQueryable;

  /**
   * Whether to generate a default UI for managing this post type in the admin.
   * If not set, the default is inherited from public.
   *
   * @var bool
   */
  protected $showUI;

  /**
   * Where to show the post type in the admin menu.
   * If true, the post type is shown in its own top level menu.
   * If false, no menu is shown
   * If a string of an existing top level menu (eg. 'tools.php' or 'edit.php?post_type=page'), the post type will be
   * placed as a sub menu of that. show_ui must be true. If not set, the default is inherited from show_ui
   *
   * @var bool
   */
  protected $showInMenu;

  /**
   * Makes this post type available for selection in navigation menus.
   * If not set, the default is inherited from public.
   *
   * @var bool
   */
  protected $showInNavMenus;

  /**
   * Makes this post type available via the admin bar.
   * If not set, the default is inherited from showInMenu
   *
   * @var bool
   */
  protected $showInAdminBar;

  /**
   * Whether to include the post type in the REST API.
   * Set this to true for the post type to be available in the block editor.
   *
   * @var bool
   */
  protected $showInRest;

  /**
   * To change the base url of REST API route.
   * Default is the post type key.
   *
   * @var string
   */
  protected $restBase;

  /**
   * To change the namespace of REST API route.
   * Default is 'wp/v2'.
   *
   * @var string
   */
  protected $restNamespace;

  /**
   * To change the controller class of REST API route.
   * Default is 'WP_REST_Posts_Controller'.
   *
   * @var string
   */
  protected $restControllerClass;

  /**
   * To change the controller class of REST API route for autosaves.
   * Default is 'WP_REST_Autosaves_Controller'.
   *
   * @var string
   */
  protected $autoSaveRestControllerClass;

  /**
   * To change the controller class of REST API route for revisions.
   * Default is 'WP_REST_Revisions_Controller'.
   *
   * @var string
   */
  protected $revisionsRestControllerClass;

  /**
   * A flag to direct the REST API controllers for autosave / revisions
   * should be registered before/after the post type controller.
   *
   * @var string
   */
  protected $lateRouteRegistration;

  /**
   * The position in the menu order the post type should appear.
   * show_in_menu must be true
   * Defaults to null, which places it at the bottom of its area.
   *
   * @var null
   */
  protected $menuPosition;

  /**
   * The url to the icon to be used for this menu. Defaults to use the posts icon.
   * Pass a base64-encoded SVG using a data URI, which will be colored to match the color scheme.
   * This should begin with 'data:image/svg+xml;base64,'.
   * Pass the name of a Dashicons helper class to use a font icon, e.g. 'dashicons-piechart'.
   * Pass 'none' to leave div.wp-menu-image empty so an icon can be added via CSS.
   *
   * @var string
   */
  protected $menuIcon = '';

  /**
   * The string to use to build the read, edit, and delete capabilities. Defaults to 'post'.
   * May be passed as an array to allow for alternative plurals when using this argument as a base to construct the
   * capabilities, e.g. array('story', 'stories').
   *
   * @var string
   */
  protected $capabilityType;

  /**
   * Array of capabilities for this post type.
   * By default, the capability_type is used as a base to construct capabilities.
   * You can see accepted values in {@link get_post_type_capabilities()}.
   *
   * @var array
   */
  protected $capabilities = [];

  /**
   * Whether to use the internal default meta capability handling. Defaults to false.
   *
   * @var bool
   */
  protected $mapMetaCap;

  /**
   * An alias for calling add_post_type_support() directly. Defaults to title and editor.
   * See {@link add_post_type_support()} for documentation.
   *
   * @var array
   */
  protected $supports = [];

  /**
   * Provide a callback function that sets up the meta boxes
   * for the edit form. Do remove_meta_box() and add_meta_box() calls in the callback.
   *
   * @var null
   */
  protected $registerMetaBoxCallback;

  /**
   * An array of taxonomy identifiers that will be registered for the post type.
   * Default is no taxonomies.
   * Taxonomies can be registered later with register_taxonomy() or register_taxonomy_for_object_type().
   *
   * @var array
   */
  protected $taxonomies = [];

  /**
   * True to enable post type archives. Default is false.
   * Will generate the proper rewrite rules if rewrite is enabled.
   *
   * @var bool
   */
  protected $hasArchive;

  /**
   * Triggers the handling of rewrites for this post type. Defaults to true, using $post_type as slug.
   * To prevent rewrite, set to false.
   * To specify rewrite rules, an array can be passed with any of these keys
   * 'slug' => string Customize the permastruct slug. Defaults to $post_type key
   * 'with_front' => bool Should the permastruct be prepended with WP_Rewrite::$front. Defaults to true.
   * 'feeds' => bool Should a feed permastruct be built for this post type. Inherits default from has_archive.
   * 'pages' => bool Should the permastruct provide for pagination. Defaults to true.
   * 'ep_mask' => const Assign an endpoint mask.
   * If not specified and permalink_epmask is set, inherits from permalink_epmask.
   * If not specified and permalink_epmask is not set, defaults to EP_PERMALINK
   *
   * @var array
   */
  protected $rewrite = [];

  /**
   * Customize the permastruct slug. Defaults to $post_type key
   *
   * @var string
   */
  protected $slug;

  /**
   * Should the permastruct be prepended with WP_Rewrite::$front. Defaults to true.
   *
   * @var bool
   */
  protected $withFront;

  /**
   * Should a feed permastruct be built for this post type. Inherits default from has_archive.
   *
   * @var bool
   */
  protected $feeds;

  /**
   * Should the permastruct provide for pagination. Defaults to true.
   *
   * @var bool
   */
  protected $pages;

  /**
   * Assign an endpoint mask.
   * If not specified and permalink_epmask is set, inherits from permalink_epmask.
   * If not specified and permalink_epmask is not set, defaults to EP_PERMALINK
   *
   * @var int
   */
  protected $epMask = EP_PERMALINK;

  /**
   * Sets the query_var key for this post type. Defaults to $post_type key
   * If false, a post type cannot be loaded at ?{query_var}={post_slug}
   * If specified as a string, the query ?{query_var_string}={post_slug} will be valid.
   *
   * @var string
   */
  protected $queryVar;

  /**
   * Allows this post type to be exported. Defaults to true.
   *
   * @var bool
   */
  protected $canExport;

  /**
   * Whether to delete posts of this type when deleting a user.
   *
   * If true, posts of this type belonging to the user will be moved to trash when then user is deleted.
   * If false, posts of this type belonging to the user will *not* be trashed or deleted.
   * If not set (the default), posts are trashed if post_type_supports('author').
   * Otherwise posts are not trashed or deleted.
   *
   * @var bool
   */
  protected $deleteWithUser;

  /**
   * Array of blocks to use as the default initial state for an editor
   * session. Each item should be an array containing block name and
   * optional attributes. Default empty array.
   *
   * @var array
   */
  protected $template;

  /**
   * Whether the block template should be locked if $template is set.
   * - If set to 'all', the user is unable to insert new blocks,
   *   move existing blocks and delete blocks.
   * - If set to 'insert', the user is able to move existing blocks
   *   but is unable to insert new blocks and delete blocks.
   *   Default false.
   *
   * @var string|bool
   */
  protected $templateLock;

  /**
   * true if this post type is a native or "built-in" post_type. THIS IS FOR INTERNAL USE ONLY!
   *
   * @var bool
   */
  //private $_builtin = false;

  /**
   * URL segments to use for edit link of this post type. THIS IS FOR INTERNAL USE ONLY!
   *
   * @var string
   */
  //private $_editLink = '';

  public function register()
  {
    // you can override this method to set the properties
    $this->boot();

    // Register custom post type
    register_post_type($this->id, $this->optionalArgs());

    $this->initHooks();
  }

  /**
   * You may override this method in order to register your own actions and filters.
   *
   */
  public function boot()
  {
    // You may override this method
  }

  protected function optionalArgs(): array
  {
    $mapProperties = [
      'label' => 'name',
      'labels' => 'labels',
      'description' => 'description',
      'public' => 'public',
      'hierarchical' => 'hierarchical',
      'exclude_from_search' => 'excludeFromSearch',
      'publicly_queryable' => 'publiclyQueryable',
      'show_ui' => 'showUI',
      'show_in_menu' => 'showInMenu',
      'show_in_nav_menus' => 'showInNavMenus',
      'show_in_admin_bar' => 'showInAdminBar',
      'show_in_rest' => 'showInRest',
      'rest_base' => 'restBase',
      'rest_namespace' => 'restNamespace',
      'rest_controller_class' => 'restControllerClass',
      'autosave_rest_controller_class' => 'autoSaveRestControllerClass',
      'revisions_rest_controller_class' => 'revisionsRestControllerClass',
      'late_route_registration' => 'lateRouteRegistration',
      'menu_position' => 'menuPosition',
      'menu_icon' => 'menuIcon',
      'capability_type' => 'capabilityType',
      'capabilities' => 'capabilities',
      'map_meta_cap' => 'mapMetaCap',
      'supports' => 'supports',
      'register_meta_box_cb' => 'registerMetaBoxCallback',
      'taxonomies' => 'taxonomies',
      'has_archive' => 'hasArchive',
      'rewrite' => 'rewrite',
      'query_var' => 'queryVar',
      'can_export' => 'canExport',
      'delete_with_user' => 'deleteWithUser',
      'template' => 'template',
      'template_lock' => 'templateLock',
    ];

    return $this->mapPropertiesToArray($mapProperties);
  }

  /**
   * You can see accepted values in {@link get_post_type_labels()}.
   *
   * @return array
   */
  protected function labels(): array
  {
    $defaults = [
      'name' => $this->plural,
      'singular_name' => $this->name,
      'menu_name' => $this->name,
      'name_admin_bar' => $this->name,
      'add_new' => "Add {$this->name}",
      'add_new_item' => "Add New {$this->name}",
      'edit_item' => "Edit {$this->name}",
      'new_item' => "New {$this->name}",
      'view_item' => "View {$this->name}",
      'search_items' => "Search {$this->name}",
      'not_found' => "No {$this->name} found",
      'not_found_in_trash' => "No {$this->name} found in trash",
      'all_items' => $this->plural,
      'archive_title' => $this->name,
      'parent_item_colon' => '',
    ];

    if (empty($this->labels)) {
      return $defaults;
    }

    return array_merge($defaults, $this->labels);
  }

  /**
   * See {@link add_post_type_support()} for documentation.
   *
   * @return array
   */
  protected function supports(): array
  {
    if (empty($this->supports)) {
      return [
        'title',
        'editor',
        'author',
        'thumbnail',
        'excerpt',
        'trackbacks',
        'custom-fields',
        'comments',
        'revisions',
        'post-formats',
      ];
    }

    return $this->supports;
  }

  /**
   * To specify rewrite rules, an array can be passed with any of these keys
   *
   *   'slug' => string Customize the permastruct slug. Defaults to $post_type key
   *   'with_front' => bool Should the permastruct be prepended with WP_Rewrite::$front. Defaults to true.
   *   'feeds' => bool Should a feed permastruct be built for this post type. Inherits default from has_archive.
   *   'pages' => bool Should the permastruct provide for pagination. Defaults to true.
   *   'ep_mask' => const Assign an endpoint mask.
   *
   * If not specified and permalink_epmask is set, inherits from permalink_epmask.
   * If not specified and permalink_epmask is not set, defaults to EP_PERMALINK
   *
   * @return array
   */
  protected function rewrite(): array
  {
    if (!empty($this->rewrite)) {
      return $this->rewrite;
    }

    $mapProperties = [
      'slug' => 'slug',
      'with_front' => 'withFront',
      'pages' => 'pages',
      'ep_mask' => 'epMask',
    ];


    return $this->mapPropertiesToArray($mapProperties);
  }

  protected function initHooks()
  {
    // admin hooks
    if (is_admin()) {
      // Hook save post
      add_action('save_post_' . $this->id, [$this, 'save_post'], 10, 2);
    }
  }

  /**
   * Return TRUE if this custom post type is current view.
   *
   * @return bool
   */
  public function is(): bool
  {
    global $post_type, $typenow;

    return $post_type === $this->id || $typenow === $this->id;
  }

  /**
   * This action is called when a post is saved or updated. Use the `save_post_{post_type}` hook
   *
   * @brief Save/update post
   * @note  You DO NOT override this method, use `update()` instead
   *
   * @param int|string $post_id Post ID
   * @param object     $post    Optional. Post object
   *
   * @return void
   */
  public function save_post($post_id, $post = null)
  {
    // Do not save...
    if (
      (defined('DOING_AUTOSAVE') && true === DOING_AUTOSAVE) ||
      (defined('DOING_AJAX') && true === DOING_AJAX) ||
      (defined('DOING_CRON') && true === DOING_CRON)
    ) {
      return;
    }

    // Get post type information
    $post_type = get_post_type();
    $post_type_object = get_post_type_object($post_type);

    // Exit
    if (false == $post_type || is_null($post_type_object)) {
      return;
    }

    // This function only applies to the following post_types
    if (!in_array($post_type, [$this->id])) {
      return;
    }

    // Find correct capability from post_type arguments
    if (isset($post_type_object->cap->edit_posts)) {
      $capability = $post_type_object->cap->edit_posts;

      // Return if current user cannot edit this post
      if (!current_user_can($capability)) {
        return;
      }
    }

    // If all ok and post request then update()
    if (Request::isVerb('post')) {
      $this->update($post_id, $post);
    }
  }

  /**
   * Override this method to save/update your custom data.
   * This method is called by hook action save_post_{post_type}`
   *
   * @param int|string $post_id Post ID
   * @param object     $post    Optional. Post object
   *
   */
  public function update($post_id, $post)
  {
    // You can override this method to save your own data
  }
}
