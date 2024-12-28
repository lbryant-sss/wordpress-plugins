<?php

namespace WPBannerize\Providers;

use WPBannerize\WPBones\Support\Traits\HasAttributes;

class WPBannerizeRolesServiceProvider
{
    use HasAttributes;

    protected $roles = [
        'campaigns_manager' => [
            'read' => true,
            'manage_analytics' => true,
            'view_analytics' => true,
        ],
        'campaigns_viewer' => [
            'read' => true,
            'view_analytics' => true
        ]
    ];

    protected $administratorCaps = [
        'edit_banners',
        'edit_others_banners',
        'publish_banners',
        'read_banner',
        'delete_banner',
        'edit_campaigns',
        'delete_campaigns',
        'assign_campaigns',
        'analytics',
        'manage_campaigns',
    ];

    public static function init()
    {
        $instance = new self();
        return $instance;
    }

    public function getRolesAttribute()
    {
        return $this->roles;
    }

    public function activated()
    {
        // The administrator can do anything
        $admin = get_role('administrator');
        $admin->add_cap('manage_banners');
        $admin->add_cap('manage_campaigns');
        $admin->add_cap('manage_analytics');
        $admin->add_cap('view_analytics');

        // Starting from Editor, we're going to create a new role Banners Manager
        $editor = get_role('editor');
        $capabilities = $editor->capabilities;

        $capabilities['manage_banners'] = true;
        $capabilities['manage_campaigns'] = true;
        $capabilities['manage_analytics'] = true;
        $capabilities['view_analytics'] = true;

        add_role('banners_manager', 'Banners Manager', $capabilities);

        add_role('campaigns_manager', 'Campaigns Manager', $this->roles['campaigns_manager']);

        add_role('campaigns_viewer', 'Campaigns Viewer', $this->roles['campaigns_viewer']);

//        $role = get_role('administrator');
//        foreach ($this->administratorCaps as $cap) {
//            $role->add_cap($cap);
//        }
    }

    public function deactivated()
    {
        remove_role('banners_manager');
        remove_role('campaigns_manager');
        remove_role('campaigns_viewer');

        $admin = get_role('administrator');
        $admin->remove_cap('manage_banners');
        $admin->remove_cap('manage_campaigns');
        $admin->remove_cap('manage_analytics');
        $admin->remove_cap('view_analytics');
    }
}