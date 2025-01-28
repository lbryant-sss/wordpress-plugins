<?php
class BeRocket_aapf_custom_sidebar_addon extends BeRocket_framework_addon_lib {
    public $addon_file = __FILE__;
    public $plugin_name = 'ajax_filters';
    public $php_file_name   = 'sidebar_include';
    function get_addon_data() {
        $data = parent::get_addon_data();
        return array_merge($data, array(
            'addon_name'    => __('Custom Sidebar', 'BeRocket_AJAX_domain'),
            'image'         => plugins_url('/custom_sidebar.png', __FILE__),
            'paid'          => true,
            'tooltip'       => __('Option enables a floating sidebar where filter or filter group widgets
                        can be added. This custom sidebar will be hidden until the button is clicked. It is a must for
                        mobiles or themes without a sidebar.', 'BeRocket_AJAX_domain')
        ));
    }
}
new BeRocket_aapf_custom_sidebar_addon();
