<?php
/**
 * Contro Positioning over google maps.
 *
 * @package Maps
 * @author Flipper Code <hello@flippercode.com>
 */

$form->add_element(
	'group', 'map_marker_cluster', array(
		'value'  => esc_html__( 'Marker Cluster Settings', 'wp-google-map-plugin' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
		'tutorial_link' => 'https://www.wpmapspro.com/docs/what-are-marker-clusters-and-how-to-use/'
	)
);

$form->add_element(
	'checkbox', 'map_cluster_setting[marker_cluster]', array(
		'label'   => esc_html__( 'Apply Marker Cluster', 'wp-google-map-plugin' ),
		'value'   => 'true',
		'id'      => 'wpgmp_marker_cluster',
		'current' => isset( $data['map_cluster_setting']['marker_cluster'] ) ? $data['map_cluster_setting']['marker_cluster'] : '',
		'desc'    => esc_html__( 'Please check to apply marker cluster.', 'wp-google-map-plugin' ),
		'class'   => 'chkbox_class switch_onoff',
		'data'    => array( 'target' => '.marker_cluster_setting' ),
	)
);

$form->add_element(
	'text', 'map_cluster_setting[grid]', array(
		'label'         => esc_html__( 'Grid', 'wp-google-map-plugin' ),
		'value'         => isset( $data['map_cluster_setting']['grid'] ) ? $data['map_cluster_setting']['grid'] : '',
		'default_value' => 15,
		'desc'          => 'Enter grid here. Default is 15.',
		'class'         => 'marker_cluster_setting form-control',
		'before'        => '<div class="fc-6">',
		'after'         => '</div>',
		'show'          => 'false',
	)
);
$zoom_values = array();
for ( $i = 1; $i < 20; $i++ ) {
	$zoom_values[ $i ] = $i;
}
$form->add_element(
	'select', 'map_cluster_setting[max_zoom]', array(
		'label'   => esc_html__( 'Max Zoom Level', 'wp-google-map-plugin' ),
		'current' => isset( $data['map_cluster_setting']['max_zoom'] ) ? $data['map_cluster_setting']['max_zoom'] : '',
		'desc'    => esc_html__( 'Available options 1 to 19.', 'wp-google-map-plugin' ),
		'options' => $zoom_values,
		'class'   => 'marker_cluster_setting form-control',
		'show'    => 'false',
		'before'  => '<div class="fc-6">',
		'after'   => '</div>',
	)
);

$form->add_element(
	'select', 'map_cluster_setting[location_zoom]', array(
		'label'         => esc_html__( 'Marker Zoom Level', 'wp-google-map-plugin' ),
		'current'       => isset( $data['map_cluster_setting']['location_zoom'] ) ? $data['map_cluster_setting']['location_zoom'] : '',
		'desc'          => esc_html__( 'Set zoom level on marker or location click. Available options 1 to 19.', 'wp-google-map-plugin' ),
		'options'       => $zoom_values,
		'class'         => 'marker_cluster_setting form-control',
		'show'          => 'false',
		'before'        => '<div class="fc-6">',
		'after'         => '</div>',
		'default_value' => '10',
	)
);

$form->add_element(
	'checkbox', 'map_cluster_setting[marker_cluster_style]', array(
		'label'   => esc_html__( 'Apply Style(s)', 'wp-google-map-plugin' ),
		'value'   => 'true',
		'id'      => 'marker_cluster_style',
		'current' => isset( $data['map_cluster_setting']['marker_cluster_style'] ) ? $data['map_cluster_setting']['marker_cluster_style'] : '',
		'desc'    => esc_html__( 'Apply styles to marker clusters?', 'wp-google-map-plugin' ),
		'class'   => 'chkbox_class switch_onoff marker_cluster_setting',
		'show'    => 'false',
		'data'    => array( 'target' => '.marker_cluster_style' ),
	)
);


$icon_set = array(
	'1.png'  => "<img src='" . WPGMP_IMAGES . "/cluster/1.png' />",
	'2.png'  => "<img src='" . WPGMP_IMAGES . "/cluster/2.png' />",
	'3.png'  => "<img src='" . WPGMP_IMAGES . "/cluster/3.png' />",
	'4.png'  => "<img src='" . WPGMP_IMAGES . "/cluster/4.png' />",
	'5.png'  => "<img src='" . WPGMP_IMAGES . "/cluster/5.png' />",
	'6.png'  => "<img src='" . WPGMP_IMAGES . "/cluster/6.png' />",
	'7.png'  => "<img src='" . WPGMP_IMAGES . "/cluster/7.png' />",
	'8.png'  => "<img src='" . WPGMP_IMAGES . "/cluster/8.png' />",
	'9.png'  => "<img src='" . WPGMP_IMAGES . "/cluster/9.png' />",
	'10.png' => "<img src='" . WPGMP_IMAGES . "/cluster/10.png' />",
);

$form->add_element(
	'radio', 'map_cluster_setting[icon]', array(
		'label'           => esc_html__( 'Cluster Color', 'wp-google-map-plugin' ),
		'radio-val-label' => $icon_set,
		'current'         => isset( $data['map_cluster_setting']['icon'] ) ? $data['map_cluster_setting']['icon'] : '',
		'class'           => 'chkbox_class marker_cluster_style',
		'show'            => 'false',
		'default_value'   => '4.png',
		'before' => '<div class="fc-6"><div class="fc-d-flex fc-flex-wrap fc-gap-10">',
		'after'  => '</div></div>',
	)
);

$hover_icon_set = array(
	'1.png'  => "<img src='" . WPGMP_IMAGES . "/cluster/1.png' />",
	'2.png'  => "<img src='" . WPGMP_IMAGES . "/cluster/2.png' />",
	'3.png'  => "<img src='" . WPGMP_IMAGES . "/cluster/3.png' />",
	'4.png'  => "<img src='" . WPGMP_IMAGES . "/cluster/4.png' />",
	'5.png'  => "<img src='" . WPGMP_IMAGES . "/cluster/5.png' />",
	'6.png'  => "<img src='" . WPGMP_IMAGES . "/cluster/6.png' />",
	'7.png'  => "<img src='" . WPGMP_IMAGES . "/cluster/7.png' />",
	'8.png'  => "<img src='" . WPGMP_IMAGES . "/cluster/8.png' />",
	'9.png'  => "<img src='" . WPGMP_IMAGES . "/cluster/9.png' />",
	'10.png' => "<img src='" . WPGMP_IMAGES . "/cluster/10.png' />",
);

$form->add_element(
	'radio', 'map_cluster_setting[hover_icon]', array(
		'label'           => esc_html__( 'Mouseover Cluster Color', 'wp-google-map-plugin' ),
		'radio-val-label' => $hover_icon_set,
		'current'         => isset( $data['map_cluster_setting']['hover_icon'] ) ? $data['map_cluster_setting']['hover_icon'] : '',
		'class'           => 'chkbox_class marker_cluster_style',
		'show'            => 'false',
		'default_value'   => '4.png',
		'before' => '<div class="fc-6"><div class="fc-d-flex fc-flex-wrap fc-gap-10">',
		'after'  => '</div></div>',
	)
);
