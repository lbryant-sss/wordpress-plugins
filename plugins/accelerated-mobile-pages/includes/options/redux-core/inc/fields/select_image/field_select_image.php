<?php
namespace ReduxCore\ReduxFramework;
/**
 * Field Select Image
 *
 * @package     Wordpress
 * @subpackage  ReduxFramework
 * @since       3.1.2
 * @author      Kevin Provance <kprovance>
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'ReduxFramework_select_image' ) ) {
    class ReduxFramework_select_image {

        public $parent;
        public $field;
        public $value;
        
        /**
         * Field Constructor.
         * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
         *
         * @since ReduxFramework 1.0.0
         */
        function __construct( $field = array(), $value = '', $parent = ' ' ) {
            $this->parent = $parent;
            $this->field  = $field;
            $this->value  = $value;
        }

        /**
         * Field Render Function.
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since ReduxFramework 1.0.0
         */
        function render() {

            // If options is NOT empty, the process
            if ( ! empty( $this->field['options'] ) ) {

                // Strip off the file ext
                if ( isset( $this->value ) ) {
                    $name        = explode( ".", $this->value );
                    $name        = str_replace( '.' . end( $name ), '', $this->value );
                    $name        = basename( $name );
                    //$this->value = trim( $name );
                    $filename = trim($name);
                }

                // beancounter
                $x = 1;

                // Process width
                if ( ! empty( $this->field['width'] ) ) {
                    $width = ' style="width:' . $this->field['width'] . ';"';
                } else {
                    $width = ' style="width: 40%;"';
                }

                // Process placeholder
                $placeholder = ( isset( $this->field['placeholder'] ) ) ? esc_attr( $this->field['placeholder'] ) : esc_attr__( 'Select an item', 'accelerated-mobile-pages' );

                if ( isset( $this->field['select2'] ) ) { // if there are any let's pass them to js
                    $select2_params = wp_json_encode( $this->field['select2'] );
                    $select2_params = htmlspecialchars( $select2_params, ENT_QUOTES );
/* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
                    echo '<input type="hidden" class="select2_params" value="' . $select2_params . '">';
                }                    

                // Begin the <select> tag
                /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped,WordPress.Security.EscapeOutput.OutputNotEscaped,WordPress.Security.EscapeOutput.OutputNotEscaped,WordPress.Security.EscapeOutput.OutputNotEscaped,WordPress.Security.EscapeOutput.OutputNotEscaped,WordPress.Security.EscapeOutput.OutputNotEscaped */
                echo '<select data-id="' . $this->field['id'] . '" data-placeholder="' . $placeholder . '" name="' . $this->field['name'] . $this->field['name_suffix'] . '" class="redux-select-item redux-select-images ' . $this->field['class'] . '"' . $width . ' rows="6">';
                echo '<option></option>';


                // Enum through the options array
                foreach ( $this->field['options'] as $k => $v ) {
					if($v['upgreade']==1){
						$selected = selected( $this->value, $v['value'], false );
						
						// If selected returns something other than a blank space, we
						// found our default/saved name.  Save the array number in a
						// variable to use later on when we want to extract its associted
						// url.
						if ( '' != $selected ) {
							$arrNum = $x;
						}
						// No alt?  Set it to title.  We do this so the alt tag shows
						// something.  It also makes HTML/SEO purists happy.
						if ( ! isset( $v['alt'] ) ) {
							$v['alt'] = $v['title'];
						}
                        if ( ! isset( $v['demo_link'] ) ) {
                            $v['demo_link'] = '';
                        }
						// Add the option tag, with values.
                        /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped,WordPress.Security.EscapeOutput.OutputNotEscaped,WordPress.Security.EscapeOutput.OutputNotEscaped,WordPress.Security.EscapeOutput.OutputNotEscaped,WordPress.Security.EscapeOutput.OutputNotEscaped,WordPress.Security.EscapeOutput.OutputNotEscaped */
						echo '<option value="' . $v['value'] . '" ' . $selected . ' data-image="'. $v['img'].'" data-alt="'. $v['alt'] .'" data-demolink="'. $v['demo_link'] .'">' . $v['title'] . '</option>';
					}else{
						// No array?  No problem!
						if ( ! is_array( $v ) ) {
							$v = array( 'img' => $v );
						}

						// No title set?  Make it blank.
						if ( ! isset( $v['title'] ) ) {
							$v['title'] = '';
						}

						// No alt?  Set it to title.  We do this so the alt tag shows
						// something.  It also makes HTML/SEO purists happy.
						if ( ! isset( $v['alt'] ) ) {
							$v['alt'] = $v['title'];
						}

						// Set the selected entry
						$selected = selected( $this->value, $v['img'], false );

						// If selected returns something other than a blank space, we
						// found our default/saved name.  Save the array number in a
						// variable to use later on when we want to extract its associted
						// url.
						if ( '' != $selected ) {
							$arrNum = $x;
						}

						// Add the option tag, with values.
                        /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped,WordPress.Security.EscapeOutput.OutputNotEscaped,WordPress.Security.EscapeOutput.OutputNotEscaped */
						echo '<option value="' . $v['img'] . '" ' . $selected . '>' . $v['alt'] . '</option>';
					}
					// Add a bean
                    $x ++;
                }

                // Close the <select> tag
                echo '</select>';

                // Some space
                echo '<br /><br />';

                // Show the preview image.
                echo '<div class="amp-theme-selector-img">';

                // just in case.  You never know.
                if ( ! isset( $arrNum ) ) {
                    $this->value = '';
                }

                // Set the default image.  To get the url from the default name,
                // we save the array count from the for/each loop, when the default image
                // is mark as selected.  Since the for/each loop starts at one, we must
                // substract one from the saved array number.  We then pull the url
                // out of the options array, and there we go.
                if ( '' == $this->value ) {
                    /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped,PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage */
                    echo '<img src="#" class="redux-preview-image" style="visibility:hidden;" id="image_' . $this->field['id'] . '">';
                } else {
                    $demo="#";
                    if (isset($this->field['options'][ $arrNum - 1 ]['demo_link'])) {
                        $demo = $this->field['options'][ $arrNum - 1 ]['demo_link'];
                    }
                    /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped,WordPress.Security.EscapeOutput.OutputNotEscaped,WordPress.Security.EscapeOutput.OutputNotEscaped,PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage */
                    echo '<img src=' . $this->field['options'][ $arrNum - 1 ]['img'] . ' class="redux-preview-image" id="image_' . $this->field['id'] . '"  onclick="return window.open(\''.$demo.'\')">'; 
                    if (isset($this->field['options'][ $arrNum - 1 ]['demo_link'])) {
                        /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
                        echo '<a href="'. $demo .'" id="theme-selected-demo-link" target="_blank">  
                                Demo 
                            </a>';
                    }
                }

                // Close the <div> tag.
                echo '</div>';
            } else {

                // No options specified.  Really?
                /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */
                echo '<strong>' . __( 'No items of this type were found.', 'accelerated-mobile-pages' ) . '</strong>';
            }
        } //function

        /**
         * Enqueue Function.
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since ReduxFramework 1.0.0
         */
        function enqueue() {
            wp_enqueue_style( 'select2-css' );

            wp_enqueue_script(
                'field-select-image-js',
                ReduxFramework::$_url . 'inc/fields/select_image/field_select_image' . Redux_Functions::isMin() . '.js',
                array('jquery', 'select2-js', 'redux-js'),
                time(),
                true
            );

            if ($this->parent->args['dev_mode']) {
                wp_enqueue_style(
                    'redux-field-select-image-css',
                    ReduxFramework::$_url . 'inc/fields/select_image/field_select_image.css',
                    array(),
                    time(),
                    'all'
                );
            }
        } //function
    } //class
}