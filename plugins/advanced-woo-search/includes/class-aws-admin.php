<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


if ( ! class_exists( 'AWS_Admin' ) ) :

/**
 * Class for plugin admin panel
 */
class AWS_Admin {

    /*
     * Name of the plugin settings page
     */
    var $page_name = 'aws-options';

    /*
    * Constructor
    */
    public function __construct() {
        add_action( 'admin_menu', array( &$this, 'add_admin_page' ) );
        add_action( 'admin_init', array( &$this, 'register_settings' ) );
        add_filter( 'plugin_action_links', array( &$this, 'add_settings_link' ), 10, 2 );

        if ( ! get_option( 'aws_settings' ) ) {
            $this->initialize_settings();
        }

        add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );
    }

    /**
     * Add options page
     */
    public function add_admin_page() {
        add_options_page( __( 'Advanced Woo Search', 'aws' ), __( 'Advanced Woo Search', 'aws' ), 'manage_options', 'aws-options', array( &$this, 'display_admin_page' ) );
    }

    /**
     * Generate and display options page
     */
    public function display_admin_page() {

        $options = $this->options_array();

        $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $tabs = array(
            'general' => __( 'General', 'aws' ),
            //'query'   => __( 'Query', 'aws' ),
            //'view'    => __( 'View', 'aws' )
        );

        $current_tab = empty( $_GET['tab'] ) ? 'general' : sanitize_title( $_GET['tab'] );

        $tabs_html = '';

        foreach ( $tabs as $name => $label ) {
            $tabs_html .= '<a href="' . admin_url( 'options-general.php?page=aws-options&tab=' . $name ) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';
        }

        $tabs_html = '<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">'.$tabs_html.'</h2>';

        if( isset( $_POST["Submit"] ) ) {
            $update_settings = $this->get_settings();

            foreach ( $options[$current_tab] as $values ) {

                if ( $values['type'] === 'heading' ) {
                    continue;
                }

                if ( $values['type'] === 'checkbox' ) {

                    $checkbox_array = array();

                    foreach ( $values['choices'] as $key => $value ) {
                        $new_value = isset( $_POST[ $values['id'] ][$key] ) ? '1' : '0';
                        $checkbox_array[$key] = $new_value;
                    }

                    $update_settings[ $values['id'] ] = $checkbox_array;

                    continue;
                }

                $new_value = isset( $_POST[ $values['id'] ] ) ? $_POST[ $values['id'] ] : '';
                $update_settings[ $values['id'] ] = $new_value;

                if ( isset( $values['sub_option'] ) ) {
                    $new_value = isset( $_POST[ $values['sub_option']['id'] ] ) ? $_POST[ $values['sub_option']['id'] ] : '';
                    $update_settings[ $values['sub_option']['id'] ] = $new_value;
                }
            }

            update_option( 'aws_settings', $update_settings );
        }

        echo '<div class="wrap">';

        echo $tabs_html;

        echo '<form action="' . $actual_link . '" name="aws_form" id="aws_form" method="post">';

        echo '<table class="form-table">';
        echo '<tbody>';

        switch ($current_tab) {
            case('query'):
                $this->generate_options( $options['query'] );
                break;
            case('view'):
                $this->generate_options( $options['view'] );
                break;
            default:
                $this->generate_options( $options['general'] );
        }

        echo '</tbody>';
        echo '</table>';

        echo '<p class="submit"><input name="Submit" type="submit" class="button-primary" value="Save Changes" /></p>';

        echo '</form>';

        echo '</div>';

    }

    /**
     * Generate options
     */
    public function generate_options( $options ) {

        $plugin_options = get_option( 'aws_settings' );

        if ( empty( $options ) ) {
            return;
        }

        foreach ( $options as $k => $value ) {
            switch ( $value['type'] ) {

                case 'text': ?>
                    <tr valign="top">
                        <th scope="row"><?php echo $value['name']; ?></th>
                        <td>
                            <input type="text" name="<?php echo $value['id']; ?>" class="regular-text" value="<?php echo stripslashes( $plugin_options[ $value['id'] ] ); ?>">
                            <br><span class="description"><?php echo $value['desc']; ?></span>
                        </td>
                    </tr>
                    <?php break;

                case 'image': ?>
                    <tr valign="top">
                        <th scope="row"><?php echo $value['name']; ?></th>
                        <td>
                            <input type="text" name="<?php echo $value['id']; ?>" class="regular-text" value="<?php echo stripslashes( $plugin_options[ $value['id'] ] ); ?>">
                            <br><span class="description"><?php echo $value['desc']; ?></span>
                            <img style="display: block;max-width: 100px;margin-top: 20px;" src="<?php echo stripslashes( $plugin_options[ $value['id'] ] ); ?>">
                        </td>
                    </tr>
                    <?php break;

                case 'number': ?>
                    <tr valign="top">
                        <th scope="row"><?php echo $value['name']; ?></th>
                        <td>
                            <input type="number" name="<?php echo $value['id']; ?>" class="regular-text" value="<?php echo stripslashes( $plugin_options[ $value['id'] ] ); ?>">
                            <br><span class="description"><?php echo $value['desc']; ?></span>
                        </td>
                    </tr>
                    <?php break;

                case 'textarea': ?>
                    <tr valign="top">
                        <th scope="row"><?php echo $value['name']; ?></th>
                        <td>
                            <textarea id="<?php echo $value['id']; ?>" name="<?php echo $value['id']; ?>" cols="45" rows="3"><?php print stripslashes( $plugin_options[ $value['id'] ] ); ?></textarea>
                            <br><span class="description"><?php echo $value['desc']; ?></span>
                        </td>
                    </tr>
                    <?php break;

                case 'checkbox': ?>
                    <tr valign="top">
                        <th scope="row"><?php echo $value['name']; ?></th>
                        <td>
                            <?php $checkbox_options = $plugin_options[ $value['id'] ]; ?>
                            <?php foreach ( $value['choices'] as $val => $label ) { ?>
                                <input type="checkbox" name="<?php echo $value['id'] . '[' . $val . ']'; ?>" id="<?php echo $value['id'] . '_' . $val; ?>" value="1" <?php checked( $checkbox_options[$val], '1' ); ?>> <label for="<?php echo $value['id'] . '_' . $val; ?>"><?php echo $label; ?></label><br>
                            <?php } ?>
                            <br><span class="description"><?php echo $value['desc']; ?></span>
                        </td>
                    </tr>
                    <?php break;

                case 'radio': ?>
                    <tr valign="top">
                        <th scope="row"><?php echo $value['name']; ?></th>
                        <td>
                            <?php foreach ( $value['choices'] as $val => $label ) { ?>
                                <input class="radio" type="radio" name="<?php echo $value['id']; ?>" id="<?php echo $value['id'].$val; ?>" value="<?php echo $val; ?>" <?php checked( $plugin_options[ $value['id'] ], $val ); ?>> <label for="<?php echo $value['id'].$val; ?>"><?php echo $label; ?></label><br>
                            <?php } ?>
                            <br><span class="description"><?php echo $value['desc']; ?></span>
                        </td>
                    </tr>
                    <?php break;

                case 'select': ?>
                    <tr valign="top">
                        <th scope="row"><?php echo $value['name']; ?></th>
                        <td>
                            <select name="<?php echo $value['id']; ?>">
                                <?php foreach ( $value['choices'] as $val => $label ) { ?>
                                    <option value="<?php echo $val; ?>" <?php selected( $plugin_options[ $value['id'] ], $val ); ?>><?php echo $label; ?></option>
                                <?php } ?>
                            </select>
                            <br><span class="description"><?php echo $value['desc']; ?></span>
                        </td>
                    </tr>
                    <?php break;

                case 'select_advanced': ?>
                    <tr valign="top">
                        <th scope="row"><?php echo $value['name']; ?></th>
                        <td>
                            <select name="<?php echo $value['id'].'[]'; ?>" multiple class="chosen-select">
                                <?php $values = $plugin_options[ $value['id'] ]; ?>
                                <?php foreach ( $value['choices'] as $val => $label ) {  ?>
                                    <?php $selected = in_array( $val, $values ) ? ' selected="selected" ' : ''; ?>
                                    <option value="<?php echo $val; ?>"<?php echo $selected; ?>><?php echo $label; ?></option>
                                <?php } ?>
                            </select>
                            <br><span class="description"><?php echo $value['desc']; ?></span>

                            <?php if ( $value['sub_option'] ): ?>
                                <?php $sub_options = $value['sub_option']; ?>
                                <br><br>
                                <p>
                                    <label for="<?php echo $sub_options['id']; ?>">
                                        <input type="checkbox" value="1" id="<?php echo $sub_options['id']; ?>" name="<?php echo $sub_options['id']; ?>" <?php checked( $plugin_options[ $sub_options['id'] ], '1' ); ?>>
                                        <?php echo $sub_options['desc']; ?>
                                    </label>
                                </p>
                            <?php endif; ?>

                        </td>
                    </tr>
                    <?php break;

                case 'sortable': ?>
                    <tr valign="top">
                        <th scope="row"><?php echo $value['name']; ?></th>
                        <td>

                            <script>
                                jQuery(document).ready(function() {

                                    jQuery( "#sti-sortable1, #sti-sortable2" ).sortable({
                                        connectWith: ".connectedSortable",
                                        placeholder: "highlight",
                                        update: function(event, ui){
                                            var serviceList = '';
                                            jQuery("#sti-sortable2 li").each(function(){

                                                serviceList = serviceList + ',' + jQuery(this).attr('id');

                                            });
                                            var serviceListOut = serviceList.substring(1);
                                            jQuery('#<?php echo $value['id']; ?>').attr('value', serviceListOut);
                                        }
                                    }).disableSelection();

                                });
                            </script>

                            <span class="description"><?php echo $value['desc']; ?></span><br><br>

                            <?php
                            $all_buttons = $value['choices'];
                            $active_buttons = explode( ',', $plugin_options[ $value['id'] ] );
                            $inactive_buttons = array_diff($all_buttons, $active_buttons);
                            ?>

                            <div class="sortable-container">

                                <div class="sortable-title"><?php _e( 'Available fields', 'aws' ) ?></div>

                                <ul id="sti-sortable1" class="sti-sortable connectedSortable">
                                    <?php
                                    if ( count( $inactive_buttons ) > 0 ) {
                                        foreach ($inactive_buttons as $button) {
                                            echo '<li id="' . $button . '" class="sti-btn sti-' . $button . '-btn">' . $button . '</li>';
                                        }
                                    }
                                    ?>
                                </ul>

                            </div>

                            <div class="sortable-container">

                                <div class="sortable-title"><?php _e( 'Drag&drop to enable', 'aws' ) ?></div>

                                <ul id="sti-sortable2" class="sti-sortable connectedSortable">
                                    <?php
                                    if ( count( $active_buttons ) > 0 ) {
                                        foreach ($active_buttons as $button) {
                                            if ( ! $button ) continue;
                                            echo '<li id="' . $button . '" class="sti-btn sti-' . $button . '-btn">' . $button . '</li>';
                                        }
                                    }
                                    ?>
                                </ul>

                            </div>

                            <input type="hidden" id="<?php echo $value['id']; ?>" name="<?php echo $value['id']; ?>" value="<?php echo $plugin_options[ $value['id'] ]; ?>" />

                        </td>
                    </tr>
                    <?php break;

                case 'heading': ?>
                    <tr valign="top">
                        <th scope="row"><h3><?php echo $value['name']; ?></h3></th>
                    </tr>
                    <?php break;
            }
        }

    }

    /*
	 * Options array that generate settings page
	 */
    public function options_array() {

        require_once AWS_DIR .'/includes/options.php';

        return $options;
    }

    /*
	 * Register plugin settings
	 */
    public function register_settings() {
        register_setting( 'aws_settings', 'aws_settings' );
    }

    /*
	 * Get plugin settings
	 */
    public function get_settings() {
        $plugin_options = get_option( 'aws_settings' );
        return $plugin_options;
    }

    /**
     * Initialize settings to their default values
     */
    public function initialize_settings() {
        $options = $this->options_array();
        $default_settings = array();

        foreach ( $options as $section ) {
            foreach ($section as $values) {

                if ( $values['type'] === 'heading' ) {
                    continue;
                }

                $default_settings[$values['id']] = $values['value'];

                if (isset( $values['sub_option'])) {
                    $default_settings[$values['sub_option']['id']] = $values['sub_option']['value'];
                }
            }
        }

        update_option( 'aws_settings', $default_settings );
    }

    /*
	 * Add settings link to plugins
	 */
    public function add_settings_link( $links, $file ) {
        $plugin_base = plugin_basename( __FILE__ );

        if ( $file == $plugin_base ) {
            $setting_link = '<a href="options-general.php?page=aws-options">'.__( 'Settings', 'aws' ).'</a>';
            array_unshift( $links, $setting_link );
        }

        return $links;
    }

    /*
     * Enqueue admin scripts and styles
     */
    public function admin_enqueue_scripts() {

        if ( isset( $_GET['page'] ) && $_GET['page'] == 'aws-options' ) {
            wp_enqueue_style( 'plugin-admin-style', AWS_URL . '/assets/css/admin.css' );
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'jquery-ui-sortable' );
        }

    }

}

endif;


new AWS_Admin();