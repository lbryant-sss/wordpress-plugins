<?php

namespace PrimeSlider\Modules\Isolate\Skins;

use Elementor\Icons_Manager;
use Elementor\Skin_Base as Elementor_Skin_Base;
use PrimeSlider\Utils;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Skin_Locate extends Elementor_Skin_Base {

    public function get_id() {
        return 'locate';
    }

    public function get_title() {
        return esc_html__('Locate', 'bdthemes-prime-slider');
    }


    public function render_social_link($position = 'right', $label = false, $class = []) {
		$settings  = $this->parent->get_active_settings();

		if ('' == $settings['show_social_icon']) {
			return;
		}

		$this->parent->add_render_attribute('social-icon', 'class', 'bdt-prime-slider-social-icon reveal-muted');
		$this->parent->add_render_attribute('social-icon', 'class', $class);

		?>

			<div <?php $this->parent->print_render_attribute_string('social-icon'); ?>>

				<?php if ($label) : ?>
					<h3><?php esc_html_e('Follow Us', 'bdthemes-prime-slider'); ?></h3>
				<?php endif; ?>

                <?php
                foreach ( $settings['social_link_list'] as $index => $link ) :
                    
                    $link_key = 'link_' . $index;

                    $tooltip = '';
                    if ( 'yes' === $settings['social_icon_tooltip'] ) {
                        $tooltip_text = wp_kses_post(strip_tags( $link['social_link_title']));
					    $tooltip = 'title: ' . htmlspecialchars($tooltip_text, ENT_QUOTES) . '; pos: ' . esc_attr( $position );
                    }

                    if ( isset($link['social_icon_link']['url']) && ! empty($link['social_icon_link']['url']) ) {
                        $this->parent->add_link_attributes($link_key, $link['social_icon_link']);
                    }
                    
                    ?>
                    <a <?php $this->parent->print_render_attribute_string($link_key); ?> data-bdt-tooltip="<?php echo $tooltip; ?>">
                        <?php Icons_Manager::render_icon( $link['social_icon'], [ 'aria-hidden' => 'true', 'class' => 'fa-fw' ] ); ?>
                    </a>
                <?php endforeach; ?>
			</div>

		<?php
	}
    
    public function render_navigation_arrows() {
        $settings = $this->parent->get_settings_for_display();
		$id     = $this->parent->get_id();
		$is_rtl = is_rtl() ? 'dir="ltr"' : '';

        ?>
            <?php if ($settings['show_navigation_arrows']) : ?>

                <div class="bdt-navigation-arrows reveal-muted">
                    <div id="<?php echo esc_attr($id); ?>_nav">
                        <div class="bdt-flex" <?php echo esc_attr($is_rtl); ?>>
                            <a class="bdt-prime-slider-previous" href="#" bdt-slideshow-item="previous">
                                <i class="ps-wi-arrow-left-5"></i>
                                <span class="bdt-slider-nav-text"><?php esc_html_e( 'Prev', 'bdthemes-prime-slider' ) ?></span>
                            </a>
                            <a class="bdt-prime-slider-next" href="#" bdt-slideshow-item="next">
                                <span class="bdt-slider-nav-text"><?php esc_html_e( 'Next', 'bdthemes-prime-slider' ) ?></span>
                                <i class="ps-wi-arrow-right-5"></i>
                            </a>
                        </div>
                    </div>
                </div>

			<?php endif; ?>
		<?php
	}

    public function render_navigation_dots() {
        $settings = $this->parent->get_settings_for_display();

        ?>

        <?php if ($settings['show_navigation_dots']) : ?>

            <ul class="bdt-ps-dotnav bdt-position-bottom-right reveal-muted">
                <?php $slide_index = 1; foreach ( $settings['slides'] as $slide ) : ?>
                    <li bdt-slideshow-item="<?php echo esc_attr($slide_index - 1); ?>" data-label="<?php echo esc_attr(str_pad( $slide_index, 2, '0', STR_PAD_LEFT)); ?>" ><a href="#"><?php echo esc_attr(str_pad( $slide_index, 2, '0', STR_PAD_LEFT)); ?></a></li>
                <?php $slide_index++;  endforeach; ?>

                <span><?php echo esc_attr(str_pad( $slide_index - 1, 2, '0', STR_PAD_LEFT)); ?></span>
            </ul>

        <?php endif; ?>

        <?php
    }

    public function render_footer() {
        
        ?>

                </ul>

                <?php $this->render_navigation_arrows(); ?>
                <?php $this->render_navigation_dots(); ?>
                
            </div>
            <?php $this->render_social_link(); ?>
            <?php $this->parent->render_scroll_button(); ?>
        </div>
        </div>
        <?php
    }

    public function render_item_content($slide_content) {
        $settings = $this->parent->get_settings_for_display();

        $parallax_button = $parallax_sub_title = $parallax_title = $parallax_inner_excerpt = $parallax_excerpt = '';
        if ( $settings['animation_parallax'] == 'yes' ) {
            $parallax_sub_title     = 'data-bdt-slideshow-parallax="y: 50,0,-50; opacity: 1,1,0"';
            $parallax_title 	    = ' data-bdt-slideshow-parallax="y: 75,0,-75; opacity: 1,1,0"'; 
            $parallax_excerpt 	    = 'data-bdt-slideshow-parallax="y: 100,0,-80; opacity: 1,1,0"';
            $parallax_button 	    = 'data-bdt-slideshow-parallax="y: 150,0,-100; opacity: 1,1,0"';
        }

        if ( true === _is_ps_pro_activated() ) {
            if ($settings['animation_status'] == 'yes' && !empty($settings['animation_of'])) {

                if (in_array(".bdt-ps-sub-title", $settings['animation_of'])) {
                    $parallax_sub_title = '';
                }
                if (in_array(".bdt-title-tag", $settings['animation_of'])) {
                    $parallax_title = '';
                }
                if (in_array(".bdt-slider-excerpt", $settings['animation_of'])) {
                    $parallax_excerpt = '';
                }
            }
        }

        if ($slide_content['title']) {
            $this->parent->add_link_attributes('title-link', $slide_content['title_link'], true);
        }
        

        ?>
            <div class="bdt-slideshow-content-wrapper">
                <div class="bdt-prime-slider-wrapper">
                    <div class="bdt-prime-slider-content">
                        <div class="bdt-prime-slider-desc bdt-flex bdt-flex-column">

                            <?php if ($slide_content['sub_title'] && ('yes' == $settings['show_sub_title'])) : ?>
                                <div class="bdt-sub-title bdt-ps-sub-title">
                                    <<?php echo esc_attr(Utils::get_valid_html_tag($settings['sub_title_html_tag'])); ?>  class="bdt-sub-title-tag" data-reveal="reveal-active" <?php echo wp_kses_post($parallax_sub_title); ?>>
                                        <?php echo wp_kses_post($slide_content['sub_title']); ?>
                                    </<?php echo esc_attr( Utils::get_valid_html_tag( $settings['sub_title_html_tag'] ) ); ?>>
                                </div>
                            <?php endif; ?>

                           <?php if ($slide_content['title'] && ('yes' == $settings['show_title'])) : ?>
                                <div class="bdt-main-title">
                                    <<?php echo esc_attr(Utils::get_valid_html_tag($settings['title_html_tag'])); ?> 
                                    class="bdt-title-tag" data-reveal="reveal-active" <?php echo wp_kses_post($parallax_title); ?>>
                                        <?php if ('' !== $slide_content['title_link']['url']) : ?>
                                            <a <?php $this->parent->print_render_attribute_string('title-link');?>>
                                            <?php endif; ?>
                                            <?php echo wp_kses_post(prime_slider_first_word($slide_content['title'])); ?>
                                            <?php if ('' !== $slide_content['title_link']['url']) : ?>
                                            </a>
                                        <?php endif; ?>
                                    </<?php echo esc_attr(Utils::get_valid_html_tag($settings['title_html_tag'])); ?>>
                                </div>
                            <?php endif; ?>

                            <?php if ($slide_content['excerpt'] && ('yes' == $settings['show_excerpt'])) : ?>
                                <div class="bdt-slider-excerpt" data-reveal="reveal-active" <?php echo wp_kses_post($parallax_excerpt); ?>>
                                    <?php echo wp_kses_post($slide_content['excerpt']); ?>
                                </div>
                            <?php endif; ?>

                            <div class="bdt-isolate-btn" data-reveal="reveal-active" <?php echo wp_kses_post($parallax_button); ?>>
                                <?php $this->parent->render_button($slide_content); ?>
							</div>
							
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

            <li class="bdt-slideshow-item bdt-flex bdt-flex-column bdt-flex-middle elementor-repeater-item-<?php echo esc_attr($slide['_id']); ?>">
                <div class="bdt-width-1-1 bdt-width-1-2@s">

                    <div class="bdt-position-relative bdt-slide-overlay" data-reveal="reveal-active">
                        <?php if ('yes' == $settings['kenburns_animation']) : ?>
                            <div class="bdt-animation-kenburns<?php echo esc_attr($kenburns_reverse); ?> bdt-transform-origin-center-left">
                            <?php endif; ?>
        
                                <?php $this->parent->rendar_item_image($slide); ?>
        
                            <?php if ('yes' == $settings['kenburns_animation']) : ?>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
                <div class="bdt-width-1-1 bdt-width-1-2@s">
                    <?php $this->render_item_content($slide); ?>
                </div>
            </li>

        <?php endforeach;
    }

    public function render() {

        $skin_name = 'locate';

        $this->parent->render_header($skin_name);

        $this->render_slides_loop();

        $this->render_footer();
    }
}
