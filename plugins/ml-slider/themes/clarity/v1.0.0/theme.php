<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct access.' );
}

/**
 * Main theme file
 */
class MetaSlider_Theme_Clarity extends MetaSlider_Theme_Base
{
    /**
     * Theme ID
     *
     * @var string
     */
    public $id = 'clarity';

    /**
     * Theme Version
     *
     * @var string
     */
    public $version = '1.0.0';

    public function __construct()
    {
        parent::__construct( $this->id, $this->version );
        add_filter( 'metaslider_flex_slider_responsive_arrows_enable', '__return_true' );
    }

    /**
     * Parameters
     *
     * @var string
     */
    public $slider_parameters = array();

    /**
     * Enqueues theme specific styles and scripts
     */
    public function enqueue_assets()
    {
        wp_enqueue_style( 
            "metaslider_{$this->id}_theme_styles", 
            METASLIDER_THEMES_URL. "{$this->id}/v{$this->version}/style.css", 
            array( 'metaslider-public' ), 
            $this->version 
        );
        wp_enqueue_script('metaslider_clarity_theme_script', METASLIDER_THEMES_URL . $this->id . '/v1.0.0/script.js', array('jquery'), '1.0.0', true);
    }
}

if ( ! isset( MetaSlider_Theme_Base::$themes['clarity'] ) ) {
    new MetaSlider_Theme_Clarity();
}
