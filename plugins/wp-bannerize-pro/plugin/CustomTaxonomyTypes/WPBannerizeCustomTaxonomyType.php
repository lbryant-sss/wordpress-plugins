<?php

namespace WPBannerize\CustomTaxonomyTypes;

use WPBannerize\WPBones\Foundation\WordPressCustomTaxonomyTypeServiceProvider as ServiceProvider;

class WPBannerizeCustomTaxonomyType extends ServiceProvider
{

    protected $id = 'wp_bannerize_tax';
    protected $name = 'Banner';
    protected $plural = 'Campaigns';
    protected $objectType = 'wp_bannerize';
    protected $queryVar = 'bannerize_category';
    protected $hierarchical = true;
    protected $showInRest = true;
    protected $showTagcloud = false;
    protected $showAdminColumn = true;
    protected $withFront = true;

    // protected $labels = [
    //     'name' => 'Campaigns',
    //     'singular_name' => 'Banner Campaign',
    //     'menu_name' => __('Banner Campaigns #2', 'wp-bannerize'),
    //     'name_admin_bar' => "Banner Campaign",
    //     'search_items' => 'Search Banner Campaigns',
    //     'popular_items' => "Popular Banner Campaigns",
    //     'all_items' => 'All Banner Campaigns',
    //     'edit_item' => 'Edit Banner Campaign',
    //     'view_item' => "View Banner campaign",
    //     'update_item' => 'Update Banner Campaign',
    //     'add_new_item' => 'Add New Banner Campaign',
    //     'new_item_name' => 'New Banner Campaign Name',
    //     'separate_items_with_commas' => "Separate Banner Campaigns with comas",
    //     'add_or_remove_items' => "Add or remove Banner Campaigns",
    //     'choose_from_most_used' => "Choose from the most used Banner Campaigns",
    //     'parent_item' => 'Parent Banner Campaign',
    //     'parent_item_colon' => 'Parent Banner Campaign:',
    // ];

    //    protected $capabilities = [
    //        'manage_terms' => 'manage_campaigns',
    //        'edit_terms' => 'edit_campaigns',
    //        'delete_terms' => 'delete_campaigns',
    //        'assign_terms' => 'assign_campaigns'
    //    ];

    /**
     * You may override this method in order to register your own actions and filters.
     *
     */
    public function boot()
    {
        // You may override this method
        $this->labels = [
            'name' => __('Banner Campaigns', 'wp-bannerize'),
            'singular_name' => __('Banner Campaign', 'wp-bannerize'),
            'menu_name' => __('Banner Campaigns', 'wp-bannerize'),
            'name_admin_bar' => __('Banner Campaign', 'wp-bannerize'),
            'search_items' => __('Search Banner Campaigns', 'wp-bannerize'),
            'popular_items' => __('Popular Banner Campaigns', 'wp-bannerize'),
            'all_items' => __('All Banner Campaigns', 'wp-bannerize'),
            'edit_item' => __('Edit Banner Campaign', 'wp-bannerize'),
            'view_item' => __('View Banner campaign', 'wp-bannerize'),
            'update_item' => __('Update Banner Campaign', 'wp-bannerize'),
            'add_new_item' => __('Add New Banner Campaign', 'wp-bannerize'),
            'new_item_name' => __('New Banner Campaign Name', 'wp-bannerize'),
            'separate_items_with_commas' => __('Separate Banner Campaigns with commas', 'wp-bannerize'),
            'add_or_remove_items' => __('Add or remove Banner Campaigns', 'wp-bannerize'),
            'choose_from_most_used' => __('Choose from the most used Banner Campaigns', 'wp-bannerize'),
            'parent_item' => __('Parent Banner Campaign', 'wp-bannerize'),
            'parent_item_colon' => __('Parent Banner Campaign:', 'wp-bannerize'),
        ];
        
    }
}
