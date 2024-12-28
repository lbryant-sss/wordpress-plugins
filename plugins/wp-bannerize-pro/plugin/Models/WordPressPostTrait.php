<?php

namespace WPBannerize\Models;

trait WordPressPostTrait
{

    /**
     * Create a post duplicate.
     *
     * @param string|int|object $post       Post ID, post object, post slug.
     * @param array             $args       Optional. Additional args.
     *                                      {
     *
     * @type string             $status     The post status. Default 'draft'.
     * @type bool               $taxonomies Whether to duplicate taxonomies. Default TRUE.
     * @type bool               $post_meta  Whether to duplicate post meta. Default TRUE.
     *        }
     *
     * @return int|\WP_Error|null
     */
    public function duplicate($post, $args = [])
    {
        // Defaults args
        $defaults = [
          'status'     => 'draft',
          'taxonomies' => true,
          'post_meta'  => true,
        ];

        $args = wp_parse_args($args, $defaults);

        // Get original post
        $original = $this->find($post);

        // Create an empty post
        $array = [];

        // Loop in properties
        foreach ($original as $property => $value) {

            // Preserve some properties
            if (!in_array($property, ['ID'])) {
                $array[$property] = $value;
            }
        }

        // Update the date
        $array['post_date']         = gmdate('Y-m-d H:i:s');
        $array['post_date_gmt']     = $array['post_date'];
        $array['post_modified']     = $array['post_date'];
        $array['post_modified_gmt'] = $array['post_date'];

        // Status
        $array['post_status'] = $args['status'];

        // Insert
        $duplicate = wp_insert_post($array);

        if (empty($duplicate)) {
            return null;
        }

        // Duplicate taxonomies
        if (true === $args['taxonomies']) {

            // Clear default category (added by wp_insert_post)
            wp_set_object_terms($duplicate->ID, null, 'category');

            // Get taxonomy ID for this post
            $taxonomies = get_object_taxonomies($original->post_type);

            foreach ($taxonomies as $taxonomy_id) {
                // Get template categories
                $terms = wp_get_post_terms($original->ID, $taxonomy_id);

                $set_terms = [];
                foreach ($terms as $term) {
                    $set_terms[] = $term->term_id;
                }

                if (!empty($set_terms)) {
                    wp_set_object_terms($duplicate->ID, $set_terms, $taxonomy_id);
                }
            }
        }

        // Duplicate post meta
        if (true == $args['post_meta']) {

            // Get post meta keys
            $post_meta_keys = get_post_custom_keys($original->ID);

            // Loop into the meta key
            foreach ($post_meta_keys as $meta_key) {

                // Get the meta values - could be more than one
                $meta_values = get_post_custom_values($meta_key, $original->ID);

                // Loop into meta values
                foreach ($meta_values as $meta_value) {
                    $meta_value = maybe_unserialize($meta_value);
                    add_post_meta($duplicate->ID, $meta_key, $meta_value);
                }
            }
        }

        return $duplicate;
    }

    // -------------------------------------------------------------------------------------------------------------------
    // Clone
    // -------------------------------------------------------------------------------------------------------------------

    public static function find($id, $objectType = 'post')
    {
        $instance = new static;

        if (is_numeric($id)) {
            $instance->post = get_post($id);
        } elseif (is_string($id)) {
            // Try by path
            $instance->post = get_page_by_path($id, OBJECT, $objectType);

            if (is_null($instance->post)) {

                // Try by title
                $instance->post = wp_bannerize_get_page_by_title($id, $objectType);
            }
        }

        return $instance;
    }
}
