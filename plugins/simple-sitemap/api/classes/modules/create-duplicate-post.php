<?php

namespace WPGO_Plugins\Plugin_Framework;

/**
 *
 * Create duplicate post.
 *
 * @since 0.0.1
 */
class Create_Duplicate_Post_FW
{
  protected $module_roots;
  private $custom_plugin_data;
  private $plugin_data;

  /* Class constructor. */
  public function __construct( $module_roots, $plugin_data, $custom_plugin_data )
  {
    $this->module_roots = $module_roots;
    $this->custom_plugin_data = $custom_plugin_data;
    $this->plugin_data = $plugin_data;

    add_action ( 'wp_before_admin_bar_render', array( &$this, 'duplicate_admin_bar_custom_link' ) );
    add_action( 'admin_action_add_duplicate_as_new_post', array( &$this, 'add_duplicate_as_new_post' ) );
    add_filter( 'post_row_actions', array( &$this, 'make_duplicate_link_row' ), 10, 2 );
  }

  /**
   * Create link to duplicate post.
   *
   * @param $post_id
   *
   * @return wp_nonce_url
   */
  public function duplicate_create_link( $post_id = 0 ) {
    if ( !$post_data = get_post( $post_id ) ) {
      return;
    }
    $action_name = "add_duplicate_as_new_post";
    $action = '?action='.$action_name.'&amp;post='.$post_data->ID;
    return wp_nonce_url( admin_url( "admin.php". $action ) );
  }

  /**
   * Create duplicate post with associated post meta.
   *
   * @param $post_data
   *
   * @return new_post_id
   */
  public function create_duplicate_post( $post_data ) {
    $old_post_id = $post_data->ID;
    $new_post_author = wp_get_current_user();
    $new_post_author_id = $new_post_author->ID;
    $new_post_status = "publish";
    $new_post_date = $post_data->post_date;
    $new_duplicate_post_args = array(
      'menu_order'            => $post_data->menu_order ? $post_data->menu_order : "",
      'comment_status'        => 'closed',
      'ping_status'           => 'closed',
      'post_author'           => get_current_user_id(),
      'post_content'          => $post_data->post_content ? $post_data->post_content : "" ,
      'post_content_filtered' => $post_data->post_content_filtered ? $post_data->post_content_filtered : "" ,
      'post_excerpt'          => $post_data->post_excerpt ? $post_data->post_excerpt : "",
      'post_mime_type'        => $post_data->post_mime_type,
      'post_parent'           => $post_data->post_parent ? $post_data->post_parent : "",
      'post_password'         => $post_data->post_password,
      'post_status'           => 'draft',
      'post_title'            => $post_data->post_title . " (Copy)",
      'post_type'             => $post_data->post_type,
      'post_name'             => $post_data->post_name,
      'post_date'             => $post_data->post_date,
      'post_date_gmt'         => get_gmt_from_date( $new_post_date )
    );
    $new_post_id = wp_insert_post( $new_duplicate_post_args );
    $taxonomies = get_object_taxonomies( $post_data->post_type ); // returns array of taxonomy names for post type, ex array("category", "post_tag");
    foreach ( $taxonomies as $taxonomy ) {
      $post_terms = wp_get_object_terms( $old_post_id, $taxonomy, array( 'fields' => 'slugs') );
      wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
    }
    
    /*
     * duplicate all post meta.
     */
    $post_meta_info = get_post_meta( $old_post_id );
    if ( ! empty( $post_meta_info ) ) {
      foreach ( $post_meta_info as $meta_key => $meta_values ) { // $meta_values is an array of values for the key
        if ( $meta_key == '_wp_old_slug' ) { // Skip this specific meta key
          continue;
        }
        foreach ( $meta_values as $meta_value ) {
          add_post_meta( $new_post_id, $meta_key, $meta_value );
        }
      }
    }
    return $new_post_id;
  }

  /**
   * Add the duplicate link to action list for wp_before_admin_bar_render.
   *
   * @param None
   *
   * @return add link on admin bar
   */
  public function duplicate_admin_bar_custom_link() {
    global $wp_admin_bar, $typenow;
    $current_object = get_queried_object();

    if ( is_admin() && isset( $_GET['post'] ) && !empty($typenow) && $typenow == $this->custom_plugin_data->cpt_slug ){
      $post_id = intval($_GET['post']);
      $post_to_duplicate = get_post($post_id);

      if ( !$post_to_duplicate ) {
        return;
      }

      $post_type_object = get_post_type_object( $post_to_duplicate->post_type );
      if ( !$post_type_object || 
           !current_user_can( $post_type_object->cap->create_posts ) || 
           !current_user_can( $post_type_object->cap->publish_posts ) ) { // Check create and publish capabilities
        return;
      }

      if( !is_null($post_to_duplicate) ) { // Redundant check, $post_to_duplicate is already checked
        $wp_admin_bar->add_menu(
          array(
            'id'    => 'duplicate-' . $this->custom_plugin_data->plugin_cpt_slug . '-cpt',
            'title' => esc_attr($this->custom_plugin_data->duplicate_post_label),
            'href'  => $this->duplicate_create_link( $post_id )
          )
        );
      }
    }
  }

  /**
   * Create duplicate post and redirect to newly create post.
   *
   * @param None
   *
   * @return wp_redirect on post page with new duplicated post
   */
  public function add_duplicate_as_new_post() {
    // CSRF / Nonce check - Rejected if the nonce is missing/invalid.
    check_admin_referer( 'wpgo_plugins_duplicate_post', 'wpgo_plugins_duplicate_nonce' );

    // Input validation.
    if ( empty( $_REQUEST['post'] ) ) {
        wp_die( __( 'No post supplied.', 'simple-sitemap' ), 400 );
    }
    $post_id = absint( $_REQUEST['post'] );

    // Permission (Broken-Access-Control fix).
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        wp_die( __( 'You are not allowed to duplicate this post.', 'simple-sitemap' ), 403 );
    }

    // Fetch and duplicate
    $original = get_post( $post_id );
    if ( ! $original ) {
        wp_die( __( 'Original post not found.', 'simple-sitemap' ), 404 );
    }

    // Create duplicate post and error handling.
    $new_id = $this->create_duplicate_post( $original );
    if ( is_wp_error( $new_id ) ) {
        wp_die( $new_id->get_error_message(), 500 );
    }

    // Redirect to newly created post.
    wp_safe_redirect( get_edit_post_link( $new_id, 'raw' ) );
    exit;
  }

  /**
  * Add the duplicate link to action list for post_row_actions.
  *
  * @param $actions, $post_data
  *
  * @return $actions
  */
  public function make_duplicate_link_row( $actions, $post_data ) {
    global $wp_admin_bar, $typenow;
    $title = _draft_or_post_title( $post_data );

    if ( $this->custom_plugin_data->cpt_slug == $typenow && $post_data->post_status != 'trash' ) {
      $post_type_object = get_post_type_object( $post_data->post_type );

      // Check if user can create and publish posts of this type
      if ( $post_type_object && 
           current_user_can( $post_type_object->cap->create_posts ) &&
           current_user_can( $post_type_object->cap->publish_posts ) ) {
        $actions['add_duplicate_as_new_post'] = '<a href="' .$this->duplicate_create_link( $post_data->ID). '" title="'.esc_attr($this->custom_plugin_data->duplicate_post_label).'" rel="permalink">'.esc_html__( 'Duplicate' ) .'</a>';
      }
    }
    return $actions;
  }
} /* End class definition */
