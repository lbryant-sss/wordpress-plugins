<?php

namespace EssentialBlocks\Blocks;

use EssentialBlocks\Core\Block;
use EssentialBlocks\Utils\Helper;

class Form extends Block
{
    protected $frontend_scripts = [ 'essential-blocks-form-frontend' ];
    protected $frontend_styles  = [ 'essential-blocks-fontawesome' ];

    /**
     * Default attributes
     *
     * @var array
     */
    protected $default_attributes;

    public function __construct()
    {
        $this->default_attributes = [
            'btnAddIcon'   => false,
            'iconPosition' => 'right',
            'buttonText'   => __( 'Submit', 'essential-blocks' ),
            'icon'         => 'fas fa-chevron-right',
            'formLayout'   => 'block',
            'formStyle'    => 'form-style-classic',
            'enableMultistepForm' => false,
            'stepIndecator' => true,
            'stepNavigationStyle' => 'progress-bar',
            'enableStepCount' => false,
            'enableStepIcon' => true,
            'enableStepSubtitle' => false,
            'nextBtnText' => 'Next',
            'prevBtnText' => 'Previous',
         ];
    }

    /**
     * Unique name of the block.
     * @return string
     */
    public function get_name()
    {
        return 'form';
    }

    /**
     * Initialize the InnerBlocks for Accordion
     * @return array<Block>
     */
    public function inner_blocks()
    {
        return [
            FormTextField::get_instance()
         ];
    }

    /**
     * Register all other scripts
     * @return void
     */
    public function register_scripts()
    {
        $this->assets_manager->register(
            'form-frontend',
            $this->path() . '/frontend.js',
            [ 'essential-blocks-babel-bundle' ]
        );
    }

    /**
     * Render callback
     */
    public function render_callback( $attributes, $content )
    {
        if ( is_admin() ) {
            return;
        }

        //Modify wp_kses rules for Form Block kses
        add_filter( 'wp_kses_allowed_html', [ new Helper, 'eb_allowed_html' ], 99, 2 );

        $_essential_attributes = [
            'formType'         => 'contact_form',
            'preset'           => 'contact_form_1',
            'confirmationType' => 'message',
            'successMessage'   => __( 'Your form has been submitted Successfully!', 'essential-blocks' ),
            'errorMessage'     => __( 'Your form couldn\'t been submitted! Please try again', 'essential-blocks' )
         ];

        foreach ( $_essential_attributes as $key => $value ) {
            if ( isset( $attributes[ $key ] ) && is_bool( $attributes[ $key ] ) ) {
                $_essential_attributes[ $key ] = $attributes[ $key ];
            } elseif ( ! empty( $attributes[ $key ] ) ) {
                $_essential_attributes[ $key ] = $attributes[ $key ];
            } else {
                $_essential_attributes[ $key ] = $value;
            }
        }


        $attributes = wp_parse_args( $attributes, $this->default_attributes );

        if ( empty( $attributes[ 'formType' ] ) ) {
            return '';
        }
        if ( empty( $content ) ) {
            return '';
        }
        $classHook = isset( $attributes[ 'classHook' ] ) ? $attributes[ 'classHook' ] : '';

        $submit_btn_attr = apply_filters( 'eb_form_submit_btn_attr', [
            'data-id'      => $attributes[ 'blockId' ],
            'data-form-id' => $attributes[ 'formId' ]
         ] );

        $submit_btn_classes = apply_filters( 'eb_form_submit_btn_classes', 'btn btn-primary eb-form-submit-button' );

        $submit_button_icon = sprintf(
            '%1$s',
            Helper::eb_render_icon( Helper::eb_get_icon_type( $attributes[ 'icon' ] ), 'eb-button-icon', $attributes[ 'icon' ] )
        );

        $submit_button_html = sprintf(
            '<button type="submit" class="%1$s" %2$s>
                %4$s%3$s%5$s
                <img class="eb-form-submit-loader" src="%6$sassets/images/loading.svg" />
            </button>',
            $submit_btn_classes,
            http_build_query( $submit_btn_attr, '', ' ' ),
            $attributes[ 'buttonText' ],
            $attributes[ 'btnAddIcon' ] && $attributes[ 'iconPosition' ] === 'left' ? $submit_button_icon : '',
            $attributes[ 'btnAddIcon' ] && $attributes[ 'iconPosition' ] === 'right' ? $submit_button_icon : '',
            ESSENTIAL_BLOCKS_URL
        );

        $confirmation_div_attr = apply_filters( 'eb_form_confirmation_div_attr', [
            'data-confirmation-type' => esc_attr( $_essential_attributes[ 'confirmationType' ] ),
            'data-success'           => esc_html( $_essential_attributes[ 'successMessage' ] ),
            'data-error'             => esc_html( $_essential_attributes[ 'errorMessage' ] )
         ], $attributes );

        $attributes_html = '';
        foreach ( $confirmation_div_attr as $key => $value ) {
            $attributes_html .= sprintf( '%s="%s" ', esc_attr( $key ), esc_attr( $value ) );
        }

        $confirmation_div_html = sprintf(
            '<div class="eb_form_submit_response" %1$s></div>',
            trim( $attributes_html )
        );

        // $confirmation_div_html = sprintf(
        //     '<div class="eb_form_submit_response" %1$s></div>',
        //     http_build_query( $confirmation_div_attr, '', ' ' )
        // );

        ob_start();
        Helper::views( 'form-block', array_merge( $attributes, [
            'essentialAttr'         => $_essential_attributes,
            'classHook'             => $classHook,
            'submit_button_html'    => $submit_button_html,
            'confirmation_div_html' => $confirmation_div_html,
            'content'               => $content,
            'nonce'                 => wp_create_nonce( $attributes[ 'blockId' ] . '-nonce' )

         ] ) );

        return ob_get_clean();
    }
}