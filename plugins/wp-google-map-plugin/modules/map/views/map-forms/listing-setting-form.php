<?php
/**
 * Filters Setting(s).
 * @package Maps
 */

$form->add_element(
	'group',
	'custom_filters',
	array(
		'value'  => esc_html__('Custom Filters', 'wp-google-map-plugin').WPGMP_PREMIUM_LINK,
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
		'parent_class'		=> 'fc-locked',
	)
);

$form->add_element(
	'group',
	'advanced_filter_functionality',
	array(
		'value'  => esc_html__('Advanced Filter Functionality', 'wp-google-map-plugin').WPGMP_PREMIUM_LINK,
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
		'parent_class'		=> 'fc-locked',
	)
);

$form->add_element(
	'group',
	'listing_settings',
	array(
		'value'  => esc_html__('Listing Settings', 'wp-google-map-plugin').WPGMP_PREMIUM_LINK,
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
		'parent_class'		=> 'fc-locked',
	)
);

$form->add_element(
	'group',
	'listing_item_skin',
	array(
		'value'  => esc_html__('Listing Item Skin', 'wp-google-map-plugin').WPGMP_PREMIUM_LINK,
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
		'parent_class'		=> 'fc-locked',
	)
);

$form->add_element(
	'group',
	'map_filter_setting',
	array(
		'value'  => esc_html__('Map Filter Settings', 'wp-google-map-plugin').WPGMP_PREMIUM_LINK,
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
		'parent_class'		=> 'fc-locked',
	)
);

$form->add_element(
	'group',
	'geo_json_setting',
	array(
		'value'  => esc_html__('Geo Json Settings', 'wp-google-map-plugin').WPGMP_PREMIUM_LINK,
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
		'parent_class'		=> 'fc-locked',
	)
);