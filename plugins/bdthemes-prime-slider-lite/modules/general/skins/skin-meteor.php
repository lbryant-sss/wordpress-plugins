<?php

namespace PrimeSlider\Modules\General\Skins;


use Elementor\Skin_Base as Elementor_Skin_Base;
use Elementor\Group_Control_Image_Size;
use PrimeSlider\Utils;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Skin_Meteor extends Elementor_Skin_Base {
    
    private $total_slides = 1;
    
    public function get_id() {
        return 'meteor';
    }

    public function get_title() {
        return esc_html__('Meteor', 'bdthemes-prime-slider');
    }

    public function render_navigation_dots() {
        $settings = $this->parent->get_settings_for_display();

        ?>

        <?php if ($settings['show_navigation_dots']) : ?>

            <ul class="bdt-slideshow-nav bdt-dotnav bdt-dotnav-vertical bdt-position-center-right reveal-muted"></ul>

        <?php endif; ?>

        <?php
    }

    public function rendar_item_image($image, $alt = '') {
        $image_src = wp_get_attachment_image_src($image['image']['id'], 'thumbnail');

        if ($image_src) : ?>
        <img src="<?php echo esc_url($image_src[0]); ?>" alt="<?php echo esc_html($image['title']); ?>" bdt-cover>
        <?php endif;

        return 0;
    }

    public function render_footer() {
        $settings = $this->parent->get_settings_for_display();

        ?>

        </ul>

                <?php $this->render_navigation_dots(); ?>

                    <div class="bdt-prime-slider-footer-content bdt-height-small bdt-flex-middle bdt-position-bottom-right" bdt-grid>
                        <div class="bdt-width-1-6">
                            <?php $this->parent->render_scroll_button(); ?>
                        </div>
                        <div class="bdt-width-1-6">
                            <div class="bdt-slide-thumbnav-img bdt-height-small">
                                <?php $slide_index = 1;
                                    
                                        foreach ($settings['slides'] as $slide) : ?>
                                    <li bdt-slideshow-item="<?php echo esc_attr((($slide_index - 2) == -1 ) ? ($this->total_slides - 2) : $slide_index - 2); ?>" data-label="<?php echo esc_attr(str_pad($slide_index, 2, '0', STR_PAD_LEFT)); ?>">

                                        <?php if (($slide['background'] == 'image') && $slide['image']) : ?>
                                            <?php $this->rendar_item_image($slide, $slide['title']); ?>
                                        <?php elseif (($slide['background'] == 'video') && $slide['video_link']) : ?>
                                            <?php $this->parent->rendar_item_video($slide); ?>
                                        <?php elseif (($slide['background'] == 'youtube') && $slide['youtube_link']) : ?>
                                            <?php $this->parent->rendar_item_youtube($slide); ?>
                                        <?php endif; ?>

                                    </li>
                                <?php $slide_index++;
                                        endforeach; ?>
                            </div>
                        </div>
                        <div class="bdt-width-expand bdt-social-background bdt-height-small">
                            <ul class="bdt-ps-meta">
                                <?php $slide_index = 1;
                                foreach ($settings['slides'] as $slide) : ?>
                                    <li bdt-slideshow-item="<?php echo esc_attr($slide_index - 1); ?>" data-label="<?php echo esc_attr(str_pad($slide_index, 2, '0', STR_PAD_LEFT)); ?>">

                                        <?php if ($slide['excerpt'] && ('yes' == $settings['show_excerpt'])) : ?>
                                            <div class="bdt-slider-excerpt bdt-column-1-2" data-reveal="reveal-active" data-bdt-slideshow-parallax="y: 300,0,-100; opacity: 1,1,0">
                                                <?php echo wp_kses_post($slide['excerpt']); ?>
                                            </div>
                                        <?php endif; ?>

                                    </li>
                                <?php $slide_index++;
                                endforeach; ?>

                            </ul>
                        </div>
                        <div class="bdt-width-1-6 bdt-flex bdt-flex-middle bdt-flex-center bdt-height-small  bdt-social-bg-color bdt-padding-remove">
                            <?php $this->parent->render_social_link('top'); ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <?php
    }

    public function render_item_content($slide_content) {
        $settings = $this->parent->get_settings_for_display();

        $this->parent->add_render_attribute('title-link', 'class', 'bdt-slider-title-link', true);
        if ($slide_content['title']) {
            $this->parent->add_link_attributes('title-link', $slide_content['title_link'], true);
        }
        

        $parallax_sub_title = 'data-bdt-slideshow-parallax="x: 300,0,-100; opacity: 1,1,0"';   
        $parallax_title     = 'data-bdt-slideshow-parallax="x: 500,0,-100; opacity: 1,1,0"';

        if ( true === _is_ps_pro_activated() ) {
            if($settings['animation_status'] == 'yes' && !empty($settings['animation_of'])){

                if( in_array( ".bdt-ps-sub-title" ,$settings['animation_of'] ) )
                {
                    $parallax_sub_title ='';
                }
                if( in_array( ".bdt-title-tag" ,$settings['animation_of'] ) )
                {
                    $parallax_title ='';
                }

            }
        }

        ?>
        <div class="bdt-prime-slider-wrapper">
            <div class="bdt-prime-slider-content">
                <div class="bdt-prime-slider-desc">

                    <?php if ($slide_content['sub_title'] && ('yes' == $settings['show_sub_title'])) : ?>
                        <div class="bdt-sub-title">
                            <<?php echo esc_attr(Utils::get_valid_html_tag($settings['sub_title_html_tag'])); ?> <?php echo wp_kses_post($parallax_sub_title); ?> data-reveal="reveal-active" class="bdt-ps-sub-title">
                                <?php echo wp_kses_post($slide_content['sub_title']); ?>
                            </<?php echo esc_attr(Utils::get_valid_html_tag($settings['sub_title_html_tag'])); ?>>
                        </div>
                    <?php endif; ?>

                    <?php if ($slide_content['title'] && ('yes' == $settings['show_title'])) : ?>
                        <div class="bdt-main-title"  <?php echo wp_kses_post($parallax_title); ?> data-reveal="reveal-active">
                            <<?php echo esc_attr(Utils::get_valid_html_tag($settings['title_html_tag'])); ?> class="bdt-title-tag">
                                <?php if ('' !== $slide_content['title_link']['url']) : ?>
                                    <a <?php $this->parent->print_render_attribute_string( 'title-link' ); ?>>
                                <?php endif; ?>
                                    <?php echo wp_kses_post($slide_content['title']); ?>
                                <?php if ('' !== $slide_content['title_link']['url']) : ?>
                                    </a>
                                <?php endif; ?>
                            </<?php echo esc_attr(Utils::get_valid_html_tag($settings['title_html_tag'])); ?>>
                        </div>
                    <?php endif; ?>

                    <?php if ($slide_content['excerpt'] && ('yes' == $settings['show_excerpt'])) : ?>
                        <div class="bdt-slider-excerpt" data-reveal="reveal-active" data-bdt-slideshow-parallax="x: 600,0,-100; opacity: 1,1,0">
                            <?php echo wp_kses_post($slide_content['excerpt']); ?>
                        </div>
                    <?php endif; ?>

                    <div data-bdt-slideshow-parallax="x: 700,0,-100; opacity: 1,1,0">

                        <?php $this->parent->render_button($slide_content); ?>

                    </div>
                </div>

            </div>
        </div>
        <?php
    }

    public function render_slides_loop() {
        $settings = $this->parent->get_settings_for_display();

        $kenburns_reverse = $settings['kenburns_reverse'] ? ' bdt-animation-reverse' : '';
    

        foreach ($settings['slides'] as $slide) : ?>

            <li class="bdt-slideshow-item bdt-flex bdt-flex-middle elementor-repeater-item-<?php echo esc_attr($slide['_id']); ?>">
                <?php if ('yes' == $settings['kenburns_animation']) : ?>
                    <div class="bdt-position-cover bdt-animation-kenburns<?php echo esc_attr($kenburns_reverse); ?> bdt-transform-origin-center-left">
                    <?php endif; ?>

                    <?php if (($slide['background'] == 'image') && $slide['image']) : ?>
                        <?php $this->parent->rendar_item_image($slide, $slide['title']); ?>
                    <?php elseif (($slide['background'] == 'video') && $slide['video_link']) : ?>
                        <?php $this->parent->rendar_item_video($slide); ?>
                    <?php elseif (($slide['background'] == 'youtube') && $slide['youtube_link']) : ?>
                        <?php $this->parent->rendar_item_youtube($slide); ?>
                    <?php endif; ?>

                    <?php if ('yes' == $settings['kenburns_animation']) : ?>
                    </div>
                <?php endif; ?>

                <?php if ('none' !== $settings['overlay']) :
                                $blend_type = ('blend' == $settings['overlay']) ? ' bdt-blend-' . $settings['blend_type'] : ''; ?>
                    <div class="bdt-overlay-default bdt-position-cover<?php echo esc_attr($blend_type); ?>"></div>
                <?php endif; ?>

                <?php

                $this->render_item_content($slide);

                ?>
            </li>

        <?php
            $this->total_slides++;
        endforeach;
    }

    public function render() {
        
        $skin_name = 'meteor';

        $this->parent->render_header($skin_name);

        $this->render_slides_loop();

        $this->render_footer();
            
    }
}
