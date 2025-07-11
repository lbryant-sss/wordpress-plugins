<?php
/**
 * Location listings for maps.
 *
 * @package Maps
 */

global $wpdb;
$modelFactory = new WPGMP_Model();
$category     = $modelFactory->create_object( 'group_map' );
$location     = $modelFactory->create_object( 'location' );
$locations    = $location->fetch();
$categories   = $category->fetch();
$categories_data = array();
if ( ! empty( $categories ) ) {
	
	foreach ( $categories as $cat ) {
		$categories_data[ $cat->group_map_id ] = $cat->group_map_title;
	}
}

if ( ! isset( $data['map_locations'] ) ) {
	$data['map_locations'] = array();
}

$all_locations = array();

if ( ! empty( $locations ) ) {

	foreach ( $locations as $loc ) {
		$assigned_categories = array();

		if ( isset( $loc->location_group_map ) and !empty( $loc->location_group_map ) ) {
			foreach ( $loc->location_group_map as $c => $cat ) {

				if ( isset($categories_data[ $cat ]) ) {
					$assigned_categories[] = $categories_data[ $cat ];
				}
			}
		}
		$assigned_categories = implode( ',', $assigned_categories );
		$loc_checkbox        = $form->field_checkbox(
			'map_locations[]', array(
				'value'   => $loc->location_id,
				'current' => ( ( in_array( $loc->location_id, (array) $data['map_locations'] ) ) ? $loc->location_id : '' ),
				'class'   => 'fc-form-check-input chkbox_class',
				'before'  => '<div class="fc-1">',
				'after'   => '</div>',
			)
		);
		$all_locations[]     = array( $loc_checkbox, $loc->location_title, $loc->location_address, $assigned_categories );
	}
}

$table_group = $form->add_element(
	'group', 'map_location_listing', array(
		'value'  => esc_html__( 'Assign Locations To Map', 'wp-google-map-plugin' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
		'tutorial_link' => 'https://www.wpmapspro.com/docs/assign-a-location-in-a-map/'
	)
);

$table_group .= $form->field_select(
	'select_all', array(
		'options' => array(
			''             => esc_html__( 'Choose', 'wp-google-map-plugin' ),
			'select_all'   => esc_html__( 'Select All', 'wp-google-map-plugin' ),
			'deselect_all' => esc_html__( 'Deselect All', 'wp-google-map-plugin' ),
		),
	)
);

$form->add_element(
	'html', 'map_location_listing_div', array(
		'html'   => $table_group,
		'before' => '<div class="fc-12 wpgmp_location_selection">',
		'after'  => '</div>',
	)
);

$form->add_element(
	'table', 'map_selected_locations', array(
		'heading' => array( esc_html__( 'Select', 'wp-google-map-plugin' ), esc_html__( 'Title', 'wp-google-map-plugin' ), esc_html__( 'Address', 'wp-google-map-plugin' ), esc_html__( 'Category', 'wp-google-map-plugin' ) ),
		'data'    => $all_locations,
		'before'  => '<div class="fc-12">',
		'after'   => '</div>',
		'id'      => 'wpgmp_google_map_data_table',
		'current' => $data['map_locations'],
	)
);
