<?php

/**
 * Smash Balloon Instagram Feed Header Template
 * Adds account information and an avatar to the top of the feed
 *
 * @version 2.9 Instagram Feed by Smash Balloon
 */

// Don't load directly.
if (!defined('ABSPATH')) {
	die('-1');
}

$doing_cutomizer = sbi_doing_customizer($settings);
$username = SB_Instagram_Parse::get_username($header_data);
$avatar = SB_Instagram_Parse::get_avatar($header_data, $settings);
$name = SB_Instagram_Parse::get_name($header_data);
$header_text_color_style = SB_Instagram_Display_Elements::get_header_text_color_styles($settings);
// style="color: #517fa4;" already escaped.

$size_class = SB_Instagram_Display_Elements::get_header_size_class($settings);
$bio = SB_Instagram_Parse::get_bio($header_data, $settings);
$should_show_bio = $settings['showbio'] && $bio !== '';
$bio_class = !$should_show_bio ? ' sbi_no_bio' : '';
$avatar_class = $avatar !== '' ? '' : ' sbi_no_avatar';
$header_atts = SB_Instagram_Display_Elements::get_header_data_attributes($settings, $header_data);
$header_image_atts = SB_Instagram_Display_Elements::get_header_img_data_attributes($settings, $header_data);
$avatar_el_atts = SB_Instagram_Display_Elements::get_avatar_element_data_attributes($settings, $header_data);
$avatar_hover_data_attributes = SB_Instagram_Display_Elements::get_avatar_hover_data_attributes($settings);
$avatar_svg_data_attributes = SB_Instagram_Display_Elements::get_avatar_svg_data_attributes($settings);
$header_text_color_style = SB_Instagram_Display_Elements::get_header_text_color_styles($settings);
// style="color: #517fa4;" already escaped.

$header_classes = SB_Instagram_Display_Elements::get_header_class($settings, $avatar);
$header_heading_attribute = SB_Instagram_Display_Elements::get_header_heading_data_attributes($settings);
$should_show_bio = $settings['showbio'] && $bio !== '';
$header_text_class = SB_Instagram_Display_Elements::get_header_text_class($header_data, $settings);
$bio_attribute = SB_Instagram_Display_Elements::get_bio_data_attributes($settings);
$header_link = SB_Instagram_Display_Elements::get_header_link($settings, $username);
$header_link_title = SB_Instagram_Display_Elements::get_header_link_title($settings, $username);

?>
<div<?php echo $header_classes; ?> <?php echo $header_atts; ?>>
	<a class="sbi_header_link" target="_blank"
	   rel="nofollow noopener"<?php echo $header_link ?><?php echo $header_link_title ?>>
		<div<?php echo $header_text_class; ?>>
			<div class="sbi_header_img"<?php echo $header_image_atts; ?>>
				<?php if ($avatar !== '' || $doing_cutomizer) : ?>
					<div class="sbi_header_img_hover" <?php echo $avatar_hover_data_attributes ?>><?php echo SB_Instagram_Display_Elements::get_icon('newlogo', 'svg'); ?></div>
					<img<?php echo $avatar_el_atts; ?> width="50" height="50">
				<?php endif; ?>

				<?php if (empty($avatar) || $doing_cutomizer) : ?>
					<div class="sbi_header_hashtag_icon" <?php echo $avatar_svg_data_attributes ?>><?php echo SB_Instagram_Display_Elements::get_icon('newlogo', 'svg'); ?></div>
				<?php endif; ?>
			</div>

			<div class="sbi_feedtheme_header_text">
				<h3<?php echo $header_text_color_style . $header_heading_attribute; ?>><?php echo esc_html($username); ?></h3>
				<?php if ($should_show_bio) : ?>
					<p class="sbi_bio"<?php echo $header_text_color_style . $bio_attribute; ?>><?php echo str_replace('&lt;br /&gt;', '<br>', esc_html(nl2br($bio))); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</a>
</div>
