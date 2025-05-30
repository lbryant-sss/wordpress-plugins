<?php

// CSS selectors with compat for v1 so this file doesn't need to be deprecated.
$group_selector   = $module->root_selector( [ '.fl-button-group' ] );
$buttons_selector = $module->root_selector( [ '.fl-button-group', '.fl-button-group-buttons' ] );
$vert_selector    = $module->root_selector( [ '.fl-button-group-layout-vertical', '.fl-button-group-buttons' ] );
$horiz_selector   = $module->root_selector( [ '.fl-button-group-layout-horizontal', '.fl-button-group-buttons' ] );

// Width, Alignment, Space Between buttons
$width = '';
if ( '' === $settings->width ) {
	$width = '100%';
} elseif ( 'custom' === $settings->width ) {
	$width = $settings->custom_width . $settings->custom_width_unit;
}
?>

<?php echo $vert_selector; ?> a.fl-button,
<?php echo $horiz_selector; ?> a.fl-button {
	width: <?php echo $width; ?>;
}
<?php echo $horiz_selector; ?> {
	<?php
	$button_group_horiz_align = '';
	if ( 'left' == $settings->align ) {
		$button_group_horiz_align = 'flex-start';
	} elseif ( 'center' == $settings->align ) {
		$button_group_horiz_align = 'center';
	} elseif ( 'right' == $settings->align ) {
		$button_group_horiz_align = 'flex-end';
	}
	?>
	justify-content: <?php echo $button_group_horiz_align; ?>
}

<?php

// Alignment on vertical layout.
FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'align',
	'selector'     => "$vert_selector .fl-button-group-button .fl-button-wrap",
	'prop'         => 'text-align',
) );

// Align Horizontal -- Desktop
if ( 'horizontal' === $settings->layout && ! empty( $settings->align ) ) {
	FLBuilderCSS::rule( array(
		'selector' => $horiz_selector,
		'media'    => 'default',
		'props'    => array(
			'justify-content' => $module->map_horizontal_alignment( $settings->align ),
		),
	) );
}

// Align Horizontal -- Large
if ( 'horizontal' === $settings->layout && ! empty( $settings->align_large ) ) {
	FLBuilderCSS::rule( array(
		'selector' => $horiz_selector,
		'media'    => 'large',
		'props'    => array(
			'justify-content' => $module->map_horizontal_alignment( $settings->align_large ),
		),
	) );
}

// Align Horizontal -- Medium
if ( 'horizontal' === $settings->layout && ! empty( $settings->align_medium ) ) {
	FLBuilderCSS::rule( array(
		'selector' => $horiz_selector,
		'media'    => 'medium',
		'props'    => array(
			'justify-content' => $module->map_horizontal_alignment( $settings->align_medium ),
		),
	) );
}

// Align Horizontal -- Responsive
if ( 'horizontal' === $settings->layout && ! empty( $settings->align_responsive ) ) {
	FLBuilderCSS::rule( array(
		'selector' => $horiz_selector,
		'media'    => 'responsive',
		'props'    => array(
			'justify-content' => $module->map_horizontal_alignment( $settings->align_responsive ),
		),
	) );
}

// Button Spacing
FLBuilderCSS::dimension_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'button_spacing',
	'selector'     => ".fl-builder-content $buttons_selector .fl-button-group-button",
	'props'        => array(
		'padding-top'    => 'button_spacing_top',
		'padding-right'  => 'button_spacing_right',
		'padding-bottom' => 'button_spacing_bottom',
		'padding-left'   => 'button_spacing_left',
	),
) );

// Text (Color, Typography, etc)
if ( ! empty( $settings->text_color ) ) :
	?>
	.fl-builder-content <?php echo $group_selector; ?> a.fl-button > span,
	.fl-builder-content <?php echo $group_selector; ?> a.fl-button > i {
		color: <?php echo FLBuilderColor::hex_or_rgb( $settings->text_color ); ?>;
	}
<?php endif; ?>

<?php if ( ! empty( $settings->text_hover_color ) ) : ?>
	.fl-builder-content <?php echo $group_selector; ?> a.fl-button:hover > span,
	.fl-builder-content <?php echo $group_selector; ?> a.fl-button:focus > span,
	.fl-builder-content <?php echo $group_selector; ?> a.fl-button:hover > i,
	.fl-builder-content <?php echo $group_selector; ?> a.fl-button:focus > i {
		color: <?php echo FLBuilderColor::hex_or_rgb( $settings->text_hover_color ); ?>;
	}
<?php endif; ?>

<?php
// Typography
FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'typography',
	'selector'     => ".fl-builder-content $group_selector a.fl-button, .fl-builder-content $group_selector a.fl-button:visited",
) );

// Button Padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'button_spacing',
	'selector'     => ".fl-builder-content $buttons_selector .fl-button-group-button a.fl-button",
	'unit'         => 'px',
	'props'        => array(
		'padding-top'    => 'button_padding_top',
		'padding-right'  => 'button_padding_right',
		'padding-bottom' => 'button_padding_bottom',
		'padding-left'   => 'button_padding_left',
	),
) );

// Container Padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'padding',
	'selector'     => ".fl-builder-content $buttons_selector",
	'unit'         => 'px',
	'props'        => array(
		'padding-top'    => 'padding_top',
		'padding-right'  => 'padding_right',
		'padding-bottom' => 'padding_bottom',
		'padding-left'   => 'padding_left',
	),
) );

// Default background hover color
if ( ! empty( $settings->bg_color ) && empty( $settings->bg_hover_color ) ) {
	$settings->bg_hover_color = $settings->bg_color;
}

// Default background color for gradient styles.
if ( empty( $settings->bg_color ) && 'gradient' === $settings->style ) {
	$settings->bg_color = 'a3a3a3';
}

// Background Gradient
$use_default_button_group_border = false;
if ( ! empty( $settings->bg_color ) ) :
	$use_default_button_group_border = empty( $settings->border['style'] )
		&& empty( $settings->border['color'] )
		&& empty( $settings->border['width']['top'] )
		&& empty( $settings->border['width']['bottom'] )
		&& empty( $settings->border['width']['left'] )
		&& empty( $settings->border['width']['right'] );

	$bgroup_default_border = '';
	if ( $use_default_button_group_border ) {
		$bgroup_default_border = 'border: 1px solid ' . FLBuilderColor::hex_or_rgb( FLBuilderColor::adjust_brightness( $settings->bg_color, 12, 'darken' ) ) . ';';
	}

	$bg_grad_start = FLBuilderColor::adjust_brightness( $settings->bg_color, 30, 'lighten' );
	?>
.fl-builder-content <?php echo $buttons_selector; ?> a.fl-button {
	background: <?php echo FLBuilderColor::hex_or_rgb( $settings->bg_color ); ?>;
	<?php echo $bgroup_default_border; ?>
	<?php if ( 'gradient' == $settings->style ) : ?>
	background: linear-gradient(to bottom,  <?php echo FLBuilderColor::hex_or_rgb( $bg_grad_start ); ?> 0%, <?php echo FLBuilderColor::hex_or_rgb( $settings->bg_color ); ?> 100%);
	<?php endif; ?>
}
	<?php
endif;

// Background Hover Gradient
if ( ! empty( $settings->bg_hover_color ) ) :
	$bg_hover_grad_start = FLBuilderColor::adjust_brightness( $settings->bg_hover_color, 30, 'lighten' );
	?>
.fl-builder-content <?php echo $buttons_selector; ?> a.fl-button:hover,
.fl-builder-content <?php echo $buttons_selector; ?> a.fl-button:focus {

	background: <?php echo FLBuilderColor::hex_or_rgb( $settings->bg_hover_color ); ?>;

	<?php if ( 'gradient' == $settings->style ) : // Gradient ?>
	background: linear-gradient(to bottom,  <?php echo FLBuilderColor::hex_or_rgb( $bg_hover_grad_start ); ?> 0%, <?php echo FLBuilderColor::hex_or_rgb( $settings->bg_hover_color ); ?> 100%);
	<?php endif; ?>
}
	<?php
endif;

// Background Gradient
if ( 'adv-gradient' === $settings->style ) :
	$adv_grad_css_rule = array();
	if ( empty( $settings->bg_gradient['colors'][0] ) && empty( $settings->bg_gradient['colors'][1] ) ) {
		$adv_grad_bg_color       = 'a3a3a3';
		$adv_grad_bg_color_start = FLBuilderColor::hex_or_rgb( FLBuilderColor::adjust_brightness( $adv_grad_bg_color, 30, 'lighten' ) );
		$adv_grad_bg_color_end   = FLBuilderColor::hex_or_rgb( $adv_grad_bg_color );
		$adv_grad_border_color   = FLBuilderColor::hex_or_rgb( FLBuilderColor::adjust_brightness( $adv_grad_bg_color, 12, 'darken' ) );

		$adv_grad_css_rule['selector'] = "$buttons_selector .fl-button-group-button a.fl-button, $buttons_selector .fl-button-group-button a.fl-button:hover";
		$adv_grad_css_rule['props']    = array(
			'border'           => "1px solid $adv_grad_border_color",
			'background-image' => "linear-gradient(to bottom, $adv_grad_bg_color_start 0%, $adv_grad_bg_color_end 100%)",
		);
	} else {
		$adv_grad_css_rule['selector'] = "$buttons_selector .fl-button-group-button a.fl-button";
		$adv_grad_css_rule['props']    = array(
			'background-image' => FLBuilderColor::gradient( $settings->bg_gradient ),
		);
	}

	FLBuilderCSS::rule( $adv_grad_css_rule );

endif;

$group_custom_gradient_hover_enable = 'adv-gradient' === $settings->style && ! ( empty( $settings->bg_gradient_hover['colors'][0] ) && empty( $settings->bg_gradient_hover['colors'][1] ) );
FLBuilderCSS::rule( array(
	'selector' => "$buttons_selector .fl-button-group-button a.fl-button:hover",
	'enabled'  => $group_custom_gradient_hover_enable,
	'props'    => array(
		'background-image' => FLBuilderColor::gradient( $settings->bg_gradient_hover ),
	),
) );

if ( 'adv-gradient' !== $settings->style ) {
	$temp_border_color = empty( $settings->border['color'] ) ? '' : $settings->border['color'];
	if ( empty( $temp_border_color ) ) {
		$temp_border_color = empty( $settings->bg_color ) ? 'a3a3a3' : $settings->bg_color;
	}
	if ( ! empty( $settings->border['color'] ) ) {
		$settings->border['color'] = FLBuilderColor::hex_or_rgb( FLBuilderColor::adjust_brightness( $temp_border_color, 12, 'darken' ) );
	}
} else {
	$temp_border_color = empty( $settings->border['color'] ) ? 'a3a3a3' : $settings->border['color'];
	if ( ! empty( $settings->border['color'] ) ) {
		$settings->border['color'] = FLBuilderColor::hex_or_rgb( FLBuilderColor::adjust_brightness( $temp_border_color, 12, 'darken' ) );
	}
}
// Border - Settings
FLBuilderCSS::border_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'border',
	'selector'     => ".fl-builder-content $buttons_selector a.fl-button",
) );

// Border - Hover Settings
if ( 'adv-gradient' !== $settings->style ) {
	$temp_border_hover_color = empty( $settings->border_hover_color ) ? '' : $settings->border_hover_color;
	if ( empty( $temp_border_hover_color ) ) {
		$temp_border_hover_color = empty( $settings->bg_color ) ? 'a3a3a3' : $settings->bg_color;
	} else {
		$temp_border_hover_color = $settings->border_hover_color;
	}
	if ( ! empty( $settings->border['color'] ) ) {
		$settings->border['color'] = FLBuilderColor::hex_or_rgb( FLBuilderColor::adjust_brightness( $temp_border_hover_color, 12, 'darken' ) );
	}
} else {
	if ( ! empty( $settings->border_hover_color ) && ! empty( $settings->border['color'] ) ) {
		$settings->border['color'] = FLBuilderColor::hex_or_rgb( FLBuilderColor::adjust_brightness( $settings->border_hover_color, 12, 'darken' ) );
	}
}

FLBuilderCSS::border_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'border',
	'selector'     => ".fl-builder-content $buttons_selector a.fl-button:hover",
) );

// Default background color for gradient styles.
if ( empty( $settings->bg_color ) && 'gradient' === $settings->style ) {
	$settings->bg_color = 'a3a3a3';
}

// Border - Default
FLBuilderCSS::rule( array(
	'selector' => "$buttons_selector a.fl-button, $buttons_selector a.fl-button:visited",
	'enabled'  => ! empty( $settings->bg_color ) && 'gradient' === $settings->style,
	'props'    => array(
		'border' => '1px solid ' . FLBuilderColor::hex_or_rgb( FLBuilderColor::adjust_brightness( $settings->bg_color, 12, 'darken' ) ),
	),
) );

// Style for the individual button in the group.
for ( $i = 0; $i < count( $settings->items ); $i++ ) :
	$button_group_button_id = "#fl-button-group-button-$id-$i";

	if ( ! is_object( $settings->items[ $i ] ) ) {
		continue;
	}

	// Padding
	FLBuilderCSS::dimension_field_rule( array(
		'settings'     => $settings->items[ $i ],
		'setting_name' => 'padding',
		'selector'     => "$button_group_button_id a.fl-button",
		'unit'         => 'px',
		'props'        => array(
			'padding-top'    => 'padding_top',
			'padding-right'  => 'padding_right',
			'padding-bottom' => 'padding_bottom',
			'padding-left'   => 'padding_left',
		),
	) );

	// Text Color
	if ( ! empty( $settings->items[ $i ]->button_item_text_color ) ) :
		?>
		<?php echo $button_group_button_id; ?> a.fl-button > span,
		<?php echo $button_group_button_id; ?> a.fl-button > i {
			color: <?php echo FLBuilderColor::hex_or_rgb( $settings->items[ $i ]->button_item_text_color ); ?>;
		}
		<?php
	endif;

	// Typography
	FLBuilderCSS::typography_field_rule( array(
		'settings'     => $settings->items[ $i ],
		'setting_name' => 'button_item_typography',
		'selector'     => "$button_group_button_id a.fl-button, $button_group_button_id a.fl-button:visited",
	) );


	if ( ! empty( $settings->items[ $i ]->button_item_text_hover_color ) ) :
		?>
		<?php echo $button_group_button_id; ?> a.fl-button:hover > span,
		<?php echo $button_group_button_id; ?> a.fl-button:focus > span,
		<?php echo $button_group_button_id; ?> a.fl-button:hover > i,
		<?php echo $button_group_button_id; ?> a.fl-button:focus > i {
			color: <?php echo FLBuilderColor::hex_or_rgb( $settings->items[ $i ]->button_item_text_hover_color ); ?>;
		}
		<?php
	endif;

	if ( ! empty( $settings->items[ $i ]->button_item_style ) && 'gradient' === $settings->items[ $i ]->button_item_style ) {
		if ( empty( $settings->items[ $i ]->button_item_bg_color ) ) {
			$settings->items[ $i ]->button_item_bg_color = 'a3a3a3';
		}
		$button_item_bg_grad_start = FLBuilderColor::adjust_brightness( $settings->items[ $i ]->button_item_bg_color, 30, 'lighten' );
	}
	?>

	<?php echo $button_group_button_id; ?> a.fl-button {
		<?php if ( ! empty( $settings->items[ $i ]->button_item_bg_color ) ) : ?>
				<?php

				$bi_border                      = $settings->items[ $i ]->button_item_border;
				$use_default_button_item_border = empty( $bi_border->style )
					&& empty( $bi_border->color )
					&& empty( $bi_border->width->top )
					&& empty( $bi_border->width->bottom )
					&& empty( $bi_border->width->left )
					&& empty( $bi_border->width->right );

				$bi_default_border = '';
				if ( $use_default_button_item_border ) {
					$bi_default_border = 'border: 1px solid ' . FLBuilderColor::hex_or_rgb( FLBuilderColor::adjust_brightness( $settings->items[ $i ]->button_item_bg_color, 12, 'darken' ) ) . ';';
				}
				?>
			background: <?php echo FLBuilderColor::hex_or_rgb( $settings->items[ $i ]->button_item_bg_color ); ?>;
			<?php echo $bi_default_border; ?>
		<?php endif; ?>

		<?php if ( ! empty( $settings->items[ $i ]->button_item_style ) && 'gradient' === $settings->items[ $i ]->button_item_style ) : ?>
		background: linear-gradient(to bottom,  <?php echo FLBuilderColor::hex_or_rgb( $button_item_bg_grad_start ); ?> 0%, <?php echo FLBuilderColor::hex_or_rgb( $settings->items[ $i ]->button_item_bg_color ); ?> 100%);
		<?php endif; ?>
	}

	<?php
	if ( ! empty( $settings->items[ $i ]->button_item_bg_hover_color ) ) :
		$button_item_bg_hover_grad_start = FLBuilderColor::adjust_brightness( $settings->items[ $i ]->button_item_bg_hover_color, 30, 'lighten' );
		?>
		<?php echo $button_group_button_id; ?> a.fl-button:hover,
		<?php echo $button_group_button_id; ?> a.fl-button:focus {
			background: <?php echo FLBuilderColor::hex_or_rgb( $settings->items[ $i ]->button_item_bg_hover_color ); ?>;
			<?php if ( ! empty( $settings->items[ $i ]->button_item_style ) && 'gradient' === $settings->items[ $i ]->button_item_style ) : ?>
			background: linear-gradient(to bottom,  <?php echo FLBuilderColor::hex_or_rgb( $button_item_bg_hover_grad_start ); ?> 0%, <?php echo FLBuilderColor::hex_or_rgb( $settings->items[ $i ]->button_item_bg_hover_color ); ?> 100%);
			<?php endif; ?>
		}
		<?php
	endif;

	if ( 'adv-gradient' === $settings->items[ $i ]->button_item_style ) :
		// Background Gradient
		$button_item_gradient = json_decode( json_encode( $settings->items[ $i ]->button_item_bg_gradient ), true );
		$adv_grad_css_rule    = array();
		if ( empty( $button_item_gradient['colors'][0] ) && empty( $button_item_gradient['colors'][1] ) ) {
			$adv_grad_bg_color       = 'a3a3a3';
			$adv_grad_bg_color_start = FLBuilderColor::hex_or_rgb( FLBuilderColor::adjust_brightness( $adv_grad_bg_color, 30, 'lighten' ) );
			$adv_grad_bg_color_end   = FLBuilderColor::hex_or_rgb( $adv_grad_bg_color );
			$adv_grad_border_color   = FLBuilderColor::hex_or_rgb( FLBuilderColor::adjust_brightness( $adv_grad_bg_color, 12, 'darken' ) );

			$adv_grad_css_rule['selector'] = "$button_group_button_id a.fl-button, $button_group_button_id a.fl-button:hover";
			$adv_grad_css_rule['props']    = array(
				'border'           => "1px solid $adv_grad_border_color",
				'background-image' => "linear-gradient(to bottom, $adv_grad_bg_color_start 0%, $adv_grad_bg_color_end 100%)",
			);
		} else {
			$adv_grad_css_rule['selector'] = "$button_group_button_id a.fl-button";
			$adv_grad_css_rule['props']    = array(
				'background-image' => FLBuilderColor::gradient( $button_item_gradient ),
			);
		}

		FLBuilderCSS::rule( $adv_grad_css_rule );

		// Background Hover Gradient
		$button_item_gradient_hover = json_decode( json_encode( $settings->items[ $i ]->button_item_bg_gradient_hover ), true );
		if ( ! ( empty( $button_item_gradient_hover['colors'][0] ) && empty( $button_item_gradient_hover['colors'][1] ) ) ) :
			FLBuilderCSS::rule( array(
				'selector' => "$button_group_button_id a.fl-button:hover",
				'props'    => array(
					'background-image' => FLBuilderColor::gradient( $button_item_gradient_hover ),
				),
			) );
		endif;
	endif;

	if ( 'flat' === $settings->items[ $i ]->button_item_style && ! empty( $settings->items[ $i ]->button_item_button_transition ) ) :
		$button_item_selector      = "$button_group_button_id .fl-button, $button_group_button_id .fl-button *";
		$button_item_bg_transition = ( 'enable' === $settings->items[ $i ]->button_item_button_transition ) ? 'all 0.2s linear' : 'none';
		FLBuilderCSS::rule( array(
			'selector' => $button_item_selector,
			'props'    => array(
				'transition'         => $button_item_bg_transition,
				'-moz-transition'    => $button_item_bg_transition,
				'-webkit-transition' => $button_item_bg_transition,
				'-o-transition'      => $button_item_bg_transition,
			),
		));
	endif;

	if ( ( 'html' == $settings->items[ $i ]->lightbox_content_type ) && ! empty( $settings->items[ $i ]->lightbox_content_html ) ) :
		$button_node_id = "fl-node-$id-$i";
		?>

		.<?php echo "$button_node_id.fl-button-lightbox-content"; ?> {
			background: #fff none repeat scroll 0 0;
			margin: 20px auto;
			max-width: 600px;
			padding: 20px;
			position: relative;
			width: auto;
		}

		.<?php echo "$button_node_id.fl-button-lightbox-content"; ?> .mfp-close,
		.<?php echo "$button_node_id.fl-button-lightbox-content"; ?> .mfp-close:hover {
			top: -10px!important;
			right: -10px;
		}

		.mfp-wrap .<?php echo "$button_node_id.fl-button-lightbox-content"; ?> .mfp-close,
		.mfp-wrap .<?php echo "$button_node_id.fl-button-lightbox-content"; ?> .mfp-close:hover {
			color:#333!important;
			right: -4px;
			top: -10px!important;
		}
		<?php
	endif;

	// Click action - lightbox
	if ( isset( $settings->items[ $i ]->click_action ) && 'lightbox' == $settings->items[ $i ]->click_action ) :
		if ( 'video' == $settings->items[ $i ]->lightbox_content_type ) :
			?>
			.fl-button-lightbox-wrap .mfp-content {
				background: #fff;
			}
			.fl-button-lightbox-wrap .mfp-iframe-scaler iframe {
				left: 2%;
				height: 94%;
				top: 3%;
				width: 96%;
			}
			.mfp-wrap.fl-button-lightbox-wrap .mfp-close,
			.mfp-wrap.fl-button-lightbox-wrap .mfp-close:hover {
				color: #333!important;
				right: -4px;
				top: -10px!important;
			}
			<?php
		endif;
	endif;

	// Border
	if ( ! empty( $settings->items[ $i ]->button_item_border->style ) ) {
		if ( empty( $settings->items[ $i ]->button_item_border->width->top ) ) {
			$settings->items[ $i ]->button_item_border->width->top = $settings->border['width']['top'];
		}
		if ( empty( $settings->items[ $i ]->button_item_border->width->bottom ) ) {
			$settings->items[ $i ]->button_item_border->width->bottom = $settings->border['width']['bottom'];
		}
		if ( empty( $settings->items[ $i ]->button_item_border->width->left ) ) {
			$settings->items[ $i ]->button_item_border->width->left = $settings->border['width']['left'];
		}
		if ( empty( $settings->items[ $i ]->button_item_border->width->right ) ) {
			$settings->items[ $i ]->button_item_border->width->right = $settings->border['width']['right'];
		}
		FLBuilderCSS::border_field_rule( array(
			'settings'     => $settings->items[ $i ],
			'setting_name' => 'button_item_border',
			'selector'     => "$button_group_button_id a.fl-button",
		) );
	}

	// Border Hover
	if ( ! empty( $settings->items[ $i ]->button_item_border_hover_color ) ) {
		?>
		<?php echo $button_group_button_id; ?> a.fl-button:hover {
			border-color: <?php echo FLBuilderColor::hex_or_rgb( $settings->items[ $i ]->button_item_border_hover_color ); ?>;
		}
		<?php
	}

endfor;

// Transition
if ( 'flat' === $settings->style ) :
	$transition = ( 'enable' === $settings->button_transition ) ? 'all 0.2s linear' : 'none';
	?>
	.fl-builder-content .fl-node-<?php echo $id; ?> .fl-button,
	.fl-builder-content .fl-node-<?php echo $id; ?> .fl-button * {
		transition: <?php echo $transition; ?>;
		-moz-transition: <?php echo $transition; ?>;
		-webkit-transition: <?php echo $transition; ?>;
		-o-transition: <?php echo $transition; ?>;
	}
<?php endif; ?>
