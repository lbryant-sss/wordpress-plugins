<?php

if (!defined('ABSPATH')) {
die('No direct access.');
}

/**
 * Flex Slider specific markup, javascript, css and settings.
 */
class MetaFlexSlider extends MetaSlider
{
    protected $js_function = 'flexslider';
    protected $js_path = 'sliders/flexslider/jquery.flexslider.min.js';
    protected $css_path = 'sliders/flexslider/flexslider.css';

    /**
     * Constructor
     *
     * @param integer $id slideshow ID
     */
    /**
     * Constructor
     *
     * @param int   $id                 ID
     * @param array $shortcode_settings Short code settings
     */
    public function __construct($id, $shortcode_settings)
    {
        parent::__construct($id, $shortcode_settings);

        add_filter('metaslider_flex_slider_parameters', array( $this, 'enable_carousel_mode' ), 10, 2);
        add_filter('metaslider_flex_slider_parameters', array( $this, 'manage_easing' ), 10, 2);
        add_filter('metaslider_flex_slider_parameters', array( $this, 'manage_tabindex' ), 99, 3);
        add_filter('metaslider_flex_slider_parameters', array( $this, 'manage_aria_current' ), 99, 3);
        add_filter('metaslider_flex_slider_parameters', array( $this, 'manage_progress_bar' ), 99, 3);
        add_filter('metaslider_flex_slider_parameters', array( $this, 'manage_tabbed_slider' ), 99, 3);
        add_filter('metaslider_flex_slider_parameters', array( $this, 'manage_pausePlay_button' ), 99, 3);
        add_filter('metaslider_flex_slider_parameters', array( $this, 'manage_dots_onhover' ), 10, 3);

        if(metaslider_pro_is_active() == false) {
            add_filter('metaslider_flex_slider_parameters', array( $this, 'metaslider_flex_loop'), 99, 3);
        }

        if( metaslider_pro_is_active() ) {
            add_filter( 'metaslider_flex_slider_parameters', array( $this, 'custom_delay_per_slide' ), 99, 3 );
        }

        add_filter('metaslider_css', array( $this, 'get_carousel_css' ), 11, 3);
        add_filter('metaslider_css', array( $this, 'hide_for_mobile' ), 11, 3);
        add_filter('metaslider_css', array( $this, 'show_hide_play_text' ), 11, 3);
        add_filter('metaslider_css', array( $this, 'show_hide_play_button' ), 11, 3);
        add_filter('metaslider_css_classes', array( $this, 'remove_bottom_margin' ), 11, 3);

        $global_settings = get_option( 'metaslider_global_settings' );
        if (
            !isset($global_settings['mobileSettings']) ||
            (isset($global_settings['mobileSettings']) && true == $global_settings['mobileSettings'])
        ) {
            if($this->check_mobile_settings() == true) {
                add_filter("metaslider_flex_slider_javascript_before", array( $this, 'manage_responsive' ), 10, 3);
            }
        }
    }

    /**
     * Adjust the slider parameters so they're comparible with the carousel mode
     *
     * @param array   $options   Slider options
     * @param integer $slider_id Slider ID
     * @return array $options
     */
    public function enable_carousel_mode($options, $slider_id)
    {
        if (isset($options["carouselMode"])) {  
            if ($options["carouselMode"] == "true") {
                $options["itemWidth"] = $this->get_setting('width');
                $options["animation"] = "'slide'";
                $options["direction"] = "'horizontal'";
                $options["minItems"] = $this->get_setting('minItems');
                $options["move"] = 1;
                $options["itemMargin"] = apply_filters('metaslider_carousel_margin', $this->get_setting('carouselMargin'), $slider_id);
                //activate infinite loop when carousel is set to 'continously' and 'autoplay'
                if($this->get_setting('infiniteLoop') == 'true'){
                    $options["controlNav"] = "false";
                    $options["directionNav"] = "false";
                    $options["slideshow"] = "false";
                    $options['start'] = isset($options['start']) ? $options['start'] : array();
                    $options['start'] = array_merge($options['start'], array("
                        var ul = $('#metaslider_" . $slider_id . " .slides');
                        ul.find('li').clone(true).appendTo(ul);
                    "));
                }
                
                if ( (int) $options['minItems'] > 1 && $this->get_setting('forceHeight') == 'true' ) {
                    $options['init'] = isset( $options['init'] ) ? $options['init'] : array();
                    $options['init'] = array_merge( $options['init'], array("
                        var container = $('#metaslider-id-" . $slider_id . "');
                        var height = container.attr('data-height') || false;

                        if (height) {
                            container.addClass('ms-carousel-force-height');
                            container.find('.slides > li').each(function () {
                                $(this).css({height: height + 'px'});
                            });
                        }
                    "));
                }
            }
            unset($options["carouselMode"]);
        }

        // we don't want this filter hanging around if there's more than one slideshow on the page
        remove_filter('metaslider_flex_slider_parameters', array( $this, 'enable_carousel_mode' ), 10, 2);
        return $options;
    }

    /**
     * Ensure CSS transitions are disabled when easing is enabled.
     *
     * @param array   $options   Slider options
     * @param integer $slider_id Slider ID
     * @return array $options
     */
    public function manage_easing($options, $slider_id)
    {
        if ($options["animation"] == '"fade"') {
            unset($options['easing']);
        }

        if (isset($options["easing"]) && $options["easing"] != '"linear"') {
            $options['useCSS'] = 'false';
        }
        remove_filter('metaslider_flex_slider_parameters', array( $this, 'manage_easing' ), 10, 2);

        return $options;
    }

    /**
     * Add optional custom delay per slide
     * 
     * @since 3.61
     */
    public function custom_delay_per_slide( $options, $slider_id, $settings )
    {
        if ( class_exists( 'MetaSliderAdvancedSettings' ) ) {
            $get_slides = $this->get_slides();
            $advancedSettings = new MetaSliderAdvancedSettings;

            $options = $advancedSettings->build_custom_delay_js(
                $options,
                $settings,
                $get_slides->posts,
                $slider_id
            );
        }

        remove_filter('metaslider_flex_slider_parameters', array($this, 'custom_delay_per_slide'));
        return $options;
    }

     /**
     * Add JavaScript to stop slideshow
     *
     * @param array $options SLide options
     * @param integer $slider_id Slider ID
     * @param array $settings Slide settings
     * @return array
     */
    public function metaslider_flex_loop($options, $slider_id, $settings)
    {
        if (isset($settings['loop']) && 'stopOnFirst' === $settings['loop']) {
            $options['after'] = isset($options['after']) ? $options['after'] : array();
            $options['after'] = array_merge(
                $options['after'],
                array("if (slider.currentSlide == 0) { slider.pause(); }")
            );
        }

        if (isset($settings['loop']) && 'stopOnLast' === $settings['loop']) {
            $options['animationLoop'] = "false";
        }

        return $options;
    }

    /**
     * Add a 'nav-hidden' class to slideshows where the navigation is hidde
     *
     * @param  string $class    Slider class
     * @param  int    $id       Slider ID
     * @param  array  $settings Slider Settings
     * @return string
     */
    public function remove_bottom_margin($class, $id, $settings)
    {
        if (isset($settings["navigation"]) && 'false' == $settings['navigation']) {
            return $class .= " nav-hidden";
        }

        // We don't want this filter hanging around if there's more than one slideshow on the page
        remove_filter('metaslider_css_classes', array($this, 'remove_bottom_margin' ), 12);
        return $class;
    }

    /**
     * Return css to ensure our slides are rendered correctly in the carousel
     *
     * @param string  $css       Css
     * @param array   $settings  Css settings
     * @param integer $slider_id SliderID
     * @return string $css
     */
    public function get_carousel_css($css, $settings, $slider_id)
    {
        if (isset($settings['carouselMode']) && $settings['carouselMode'] == 'true') {
            $margin = apply_filters('metaslider_carousel_margin', $this->get_setting('carouselMargin'), $slider_id);
            $css .= "\n        #metaslider_{$slider_id}.flexslider .slides li {margin-right: {$margin}px !important;}";
            if(isset($settings['infiniteLoop']) && $settings['infiniteLoop'] == 'true'){
                //check if theres mobile setting to subtract from the slide count
                if($this->check_mobile_settings() == true) {
                    $slides = count($this->slides) - 1;
                } else {
                    $slides = count($this->slides);
                }
                $double = $slides * 2;
                $animationtime = ($settings['animationSpeed'] * $slides) + ($settings['delay'] * $slides);
                $transform_width = $margin + $settings["width"];
                $css .= "
                    @keyframes infiniteloop_" . $slider_id . " {
                        0% {
                            transform: translateX(0);
                            visibility: visible;
                        }
                        100% {
                            transform: translateX(calc(-" . $transform_width . "px * " . $slides . "));
                            visibility: visible;
                        }
                    }
                    #metaslider_{$slider_id}.flexslider .slides {
                        -webkit-animation: infiniteloop_" . $slider_id . " " . $animationtime . "ms linear infinite;
                                animation: infiniteloop_" . $slider_id . " " . $animationtime . "ms linear infinite;
                        display: flex;
                        width: calc(" . $transform_width . "px * " . $double . ");
                    }
                    #metaslider_{$slider_id}.flexslider .slides:hover{
                        animation-play-state: paused;
                    }
                ";
            }
        }

        // we don't want this filter hanging around if there's more than one slideshow on the page
        remove_filter('metaslider_css', array( $this, 'get_carousel_css' ), 11, 3);

        return $css;
    }

    /**
     * Hide slideshow with mobile settings on first load
     */
    public function hide_for_mobile($css, $settings, $slider_id)
    {
        $global_settings = get_option( 'metaslider_global_settings' );
        if ( isset($global_settings['mobileSettings']) && true == $global_settings['mobileSettings']
        ){
            if($this->check_mobile_settings() == true) {
                $css .= "\n        #metaslider_{$slider_id}.flexslider {display: none;}";
            }
        }
        remove_filter('metaslider_css', array( $this, 'hide_for_mobile' ), 11, 3);
        return $css;
    }

    /**
     * Show/Hide Play Button Text
     */
    public function show_hide_play_text($css, $settings, $slider_id)
    {
        if (isset($settings['showPlayText']) && $settings['showPlayText'] == 'true') {
            $css .= "\n 
            #metaslider_{$slider_id}.flexslider .flex-pauseplay .flex-play,
            #metaslider_{$slider_id}.flexslider .flex-pauseplay .flex-pause {
               width: auto;
               height: auto;
            }
            #metaslider_{$slider_id}.flexslider .flex-pauseplay .flex-play::before,
            #metaslider_{$slider_id}.flexslider .flex-pauseplay .flex-pause::before {
                margin-right: 5px;
            }";
            
        }
        remove_filter('metaslider_css', array( $this, 'show_hide_play_text' ), 11, 3);
        return $css;
    }

    /**
     * Show/Hide Play Button When Infinite Loop is enabled
     */
    public function show_hide_play_button($css, $settings, $slider_id)
    {
        if (isset($settings['infiniteLoop']) && $settings['infiniteLoop'] == 'true') {
            $css .= "\n 
            #metaslider_{$slider_id}.flexslider .flex-pauseplay {
               display: none;
            }"; 
        }
        remove_filter('metaslider_css', array( $this, 'show_hide_play_text' ), 11, 3);
        return $css;
    }

    /**
     * Enable the parameters that are accepted by the slider
     *
     * @param  string $param Parameters
     * @return array|boolean enabled parameters (false if parameter doesn't exist)
     */
    protected function get_param($param)
    {
        $params = array(
            'effect' => 'animation',
            'direction' => 'direction',
            'prevText' => 'prevText',
            'nextText' => 'nextText',
            'delay' => 'slideshowSpeed',
            'animationSpeed' => 'animationSpeed',
            'hoverPause' => 'pauseOnHover',
            'reverse' => 'reverse',
            'keyboard' => 'keyboard',
            'touch' => 'touch',
            'navigation' => 'controlNav',
            'links' => 'directionNav',
            'carouselMode' => 'carouselMode',
            'easing' => 'easing',
            'autoPlay' => 'slideshow',
            'firstSlideFadeIn' => 'fadeFirstSlide',
            'smoothHeight' => 'smoothHeight',
            'pausePlay' => 'pausePlay',
            'showPlayText' => 'showPlayText',
            'playText' => 'playText',
            'pauseText' => 'pauseText'
        );
        return isset($params[$param]) ? $params[$param] : false;
    }

    /**
     * Include slider assets
     */
    public function enqueue_scripts()
    {
        parent::enqueue_scripts();

        if ($this->get_setting('printJs') == 'true' && ( $this->get_setting('effect') == 'slide' || $this->get_setting('carouselMode') == 'true' )) {
            wp_enqueue_script('metaslider-easing', METASLIDER_ASSETS_URL . 'easing/jQuery.easing.min.js', array( 'jquery' ), METASLIDER_ASSETS_VERSION);
        }
    }

    /**
     * Build the HTML for a slider.
     *
     * @return string slider markup.
     */
    protected function get_html()
    {
        $class = $this->get_setting('noConflict') == 'true' ? "" : ' class="flexslider"';

        //accessibility option
        if ($this->get_setting('ariaLive') == 'true' && $this->get_setting('autoPlay') == 'false') {
            $aria_live = " aria-live='polite'";
        } elseif ($this->get_setting('ariaLive') == 'true' && $this->get_setting('autoPlay') == 'true') {
            $aria_live = " aria-live='off'";
        } else {
            $aria_live = '';
        }
        
        $return_value = '';
        if($this->check_mobile_settings() == true) {
            $return_value .= '<div id="temp_' . $this->get_identifier() . '" class="flexslider">';
            $return_value .= "<ul" . $aria_live . " class='slides'></ul></div>";
        }
        $return_value .= '<div id="' . $this->get_identifier() . '"' . $class . '>';
        $return_value .= "\n            <ul" . $aria_live . " class='slides'>";

        foreach ($this->slides as $slide) {
            // backwards compatibility with older versions of MetaSlider Pro (< v2.0)
            // MS Pro < 2.0 does not include the <li>
            // MS Pro 2.0+ returns the <li>
            if (strpos($slide, '<li') === 0) {
                $return_value .= "\n                " . $slide;
            } else {
                $return_value .= "\n                <li style=\"display: none;\" >" . $slide . "</li>";
            }
        }

        $return_value .= "\n            </ul>";

        if ($this->get_setting('autoPlay') == 'true' 
            && $this->get_setting('progressBar') == 'true' 
            && ($this->get_setting('infiniteLoop') == 'false' || $this->get_setting('carouselMode') == 'false')
        ) {
            $return_value .= "\n        <div class='flex-progress-bar'></div>";
        }

        $return_value .= "\n        </div>";

        // show the first slide
        if ($this->get_setting('carouselMode') != 'true') {
            $return_value =  preg_replace('/none/', 'block', $return_value, 1);
        }

        return apply_filters('metaslider_flex_slider_get_html', $return_value, $this->id, $this->settings);
    }

    /**
     * Function to show/hide slides per device on FlexSlider
     */
    public function manage_responsive($javascript)
    {
        $js = $javascript;
        $identifier = $this->get_identifier();
        $global_settings = get_option( 'metaslider_global_settings' );
        if (
            !isset($global_settings['mobileSettings']) ||
            (isset($global_settings['mobileSettings']) && true == $global_settings['mobileSettings'])
        ) {
            if($this->check_mobile_settings() == true) {
                $js .= "
                jQuery(document).ready(function($){
                    var newBreakpoint = window.getComputedStyle(document.body, ':after').getPropertyValue('content');";
                $js .= 'newBreakpoint = newBreakpoint.replace(/"/g, "");';
                $js .= "
                    var liHTML = $('#" . $identifier . " .slides li:not(.clone, .hidden_' + newBreakpoint + ')').removeAttr('style').toArray();
                    $('#temp_" . $identifier . " .slides').append(liHTML);
                    $('#" . $identifier . "').remove();
                    $('#temp_" . $identifier . "')." . $this->js_function . "({" .   $this->_get_javascript_parameters() . "});
                    $('#temp_" . $identifier . "').attr('id', '" . $identifier . "');
                    $(document).trigger('metaslider/initialized', '#" . $identifier . "');
                    $('#" . $identifier . "').show();
                });";
            }
        }
        remove_filter('metaslider_flex_slider_javascript_before', array( $this, 'manage_responsive' ), 10, 3);
        return $js;
    }

    /**
     * Add JavaScript for tabindex
     *
     * @param array $options SLide options
     * @param integer $slider_id Slider ID
     * @param array $settings Slide settings
     * @return array
     */
    public function manage_tabindex($options, $slider_id, $settings)
    {
        if (isset($settings['tabIndex']) && 'true' == $settings['tabIndex']) {
            $options['start'] = isset($options['start']) ? $options['start'] : array();
            $options['start'] = array_merge(
                $options['start'],
                array(
                    "
                    $('#metaslider_" . $slider_id . " .flex-control-nav').attr('role', 'tablist');
                    $('#metaslider_" . $slider_id . " .flex-control-nav a:not(.flex-active)').attr('tabindex', '-1');
                    $('#metaslider_" . $slider_id . " .slides li:not(.flex-active-slide) a').attr('tabindex', '-1');
                    "
                )
            );

            $options['after'] = isset($options['after']) ? $options['after'] : array();
            $options['after'] = array_merge(
                $options['after'],
                array(
                    "
                    $('#metaslider_" . $slider_id . " .flex-control-nav a.flex-active').removeAttr('tabindex');
                    $('#metaslider_" . $slider_id . " .flex-control-nav a:not(.flex-active)').attr('tabindex', '-1');
                    $('#metaslider_" . $slider_id . " .slides li.flex-active-slide a.flex-active').removeAttr('tabindex');
                    $('#metaslider_" . $slider_id . " .slides li:not(.flex-active-slide) a').attr('tabindex', '-1');
                    "
                )
            );
        }

        return $options;
    }

    /**
     * Add aria-current attribute to active navigation
     *
     * @param array $options SLide options
     * @param integer $slider_id Slider ID
     * @param array $settings Slide settings
     * @return array
     * @since 3.90
     */
    public function manage_aria_current($options, $slider_id, $settings)
    {
        if (isset($settings['ariaCurrent']) && 'true' == $settings['ariaCurrent']) {
            $options['start'] = isset($options['start']) ? $options['start'] : array();
            $options['start'] = array_merge(
                $options['start'],
                array(
                    "
                    $('#metaslider_" . $slider_id . " .flex-control-nav a.flex-active').attr('aria-current', 'true');
                    $('#metaslider_" . $slider_id . " .flex-control-nav a:not(.flex-active)').removeAttr('aria-current');
                    "
                )
            );
            $options['after'] = isset($options['after']) ? $options['after'] : array();
            $options['after'] = array_merge(
                $options['after'],
                array(
                    "
                    $('#metaslider_" . $slider_id . " .flex-control-nav a.flex-active').attr('aria-current', 'true');
                    $('#metaslider_" . $slider_id . " .flex-control-nav a:not(.flex-active)').removeAttr('aria-current');
                    "
                )
            );
        }
        return $options;
    }

    /**
     * Add JavaScript for progressBar
     *
     * @param array $options SLide options
     * @param integer $slider_id Slider ID
     * @param array $settings Slide settings
     * @return array
     */
    public function manage_progress_bar($options, $slider_id, $settings)
    {
        if (isset($settings['progressBar']) 
            && $settings['progressBar'] == 'true'
            && $settings['autoPlay'] == 'true' 
            && ($settings['infiniteLoop'] == 'false' || $settings['carouselMode'] == 'false')
        ) {
            $options['start'] = isset($options['start']) ? $options['start'] : array();
            $totalTime = $settings['delay'] - $settings['animationSpeed'];
            $options['start'] = array_merge(
                $options['start'],
                array(
                    "
                    $('#metaslider_" . $slider_id . " .flex-progress-bar')[0].offsetWidth;
                    $('#metaslider_" . $slider_id . " .flex-progress-bar').css({
                        width: '100%',
                        transition: `width " . $totalTime . "ms linear`
                    });
                    "
                )
                
            );
            $options['before'] = isset($options['before']) ? $options['before'] : array();
            $options['before'] = array_merge(
                $options['before'],
                array(
                    "
                    $('#metaslider_" . $slider_id . " .flex-progress-bar').css({width: '0%',transition: 'none'});
                    "
                )
            );
            $options['after'] = isset($options['after']) ? $options['after'] : array();
            $options['after'] = array_merge(
                $options['after'],
                array(
                    "
                    $('#metaslider_" . $slider_id . " .flex-progress-bar')[0].offsetWidth;
                    $('#metaslider_" . $slider_id . " .flex-progress-bar').css({
                        width: '100%',
                        transition: `width " . $totalTime . "ms linear`
                    });
                    "
                )
            );
        }

        return $options;
    }

    /**
     * Trigger resize when slideshow is inside a tab
     */
    public function manage_tabbed_slider($options, $slider_id, $settings)
    {
        if ($settings['effect'] == 'slide') {
            $options['start'] = isset( $options['start'] ) ? $options['start'] : array();
            $options['start'] = array_merge(
                $options['start'],
                array(
                    "document.addEventListener('click', function (event) {
                        if (event.target.closest('[role=\'tab\']')) {
                            $('#metaslider_" . $slider_id . "').resize();
                        }
                    });"
                )  
            );
        }
        return $options;
    }

    /**
     * Change button to play icon when autoplay is disabled
     */
    public function manage_pausePlay_button($options, $slider_id, $settings)
    {
        if (isset($settings['pausePlay']) && $settings['pausePlay'] === 'true' && $settings['autoPlay'] === 'false') {
            $script = "$('.flex-pauseplay a').removeClass('flex-pause').addClass('flex-play');";
        
            if (isset($settings['showPlayText']) && $settings['showPlayText'] === 'true' && !empty($settings['playText'])) {
                $script .= "$('.flex-pauseplay a').text('" . addslashes($settings['playText']) . "');";
            }
        
            $options['start'] = isset($options['start']) ? $options['start'] : array();
            $options['start'] = array_merge($options['start'], [$script]);
        }
        /* @since 3.97 - disable hover on pause when play button is enabled */
        if (isset($settings['pausePlay']) && $settings['pausePlay'] === 'true') {
            unset($options['pauseOnHover']);
        }
        
        return $options;
    }

    /**
     * Modify the JavaScript parameters to delay the navigation fade out
     *
     * @since 2.46
     * 
     * @param array $options - javascript parameters
     * @param integer $slider_id - slideshow ID
     * @param array $settings - slideshow settings
     *
     * @return array modified javascript parameters
     */
    public function manage_dots_onhover( $options, $slider_id, $settings )
    {
        if ( 'dots_onhover' === $settings['navigation'] ) {
            $options['start'] = isset( $options['start'] ) ? $options['start'] : array();
            $options['start'] = array_merge( $options['start'], array(
                "setTimeout(function() {
                    slider.find('.flex-control-paging').css('opacity', '0');
                }, 2000);"
            ));
        }

        return $options;
    }
}

