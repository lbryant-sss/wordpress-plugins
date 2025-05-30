<?php
/**
 * Handle the recipe nutrition label shortcode.
 *
 * @link       http://bootstrapped.ventures
 * @since      4.0.0
 *
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public/shortcodes/recipe
 */

/**
 * Handle the recipe nutrition label shortcode.
 *
 * @since      4.0.0
 * @package    WP_Recipe_Maker
 * @subpackage WP_Recipe_Maker/includes/public/shortcodes/recipe
 * @author     Brecht Vandersmissen <brecht@bootstrapped.ventures>
 */
class WPRM_SC_Nutrition_Label extends WPRM_Template_Shortcode {
	public static $shortcode = 'wprm-nutrition-label';

	public static function init() {
		$atts = array(
			'id' => array(
				'default' => '0',
			),
			'style' => array(
				'default' => 'label',
				'type' => 'dropdown',
				'options' => array(
					'label' => 'Label',
					'simple' => 'Simple Text',
					'grouped' => 'Grouped',
				),
			),
			'group_width' => array(
				'default' => '180px',
				'type' => 'size',
				'dependency' => array(
					'id' => 'style',
					'value' => 'grouped',
				),
			),
			'group_column_gap' => array(
				'default' => '0px',
				'type' => 'size',
				'dependency' => array(
					'id' => 'style',
					'value' => 'grouped',
				),
			),
			'bottom_border' => array(
				'default' => '0',
				'type' => 'toggle',
				'dependency' => array(
					'id' => 'style',
					'value' => 'grouped',
				),
			),
			'bottom_border_gap' => array(
				'default' => '5px',
				'type' => 'size',
				'dependency' => array(
					array(
						'id' => 'style',
						'value' => 'grouped',
					),
					array(
						'id' => 'bottom_border',
						'value' => '1',
					),
				),
			),
			'bottom_border_width' => array(
				'default' => '1px',
				'type' => 'size',
				'dependency' => array(
					array(
						'id' => 'style',
						'value' => 'grouped',
					),
					array(
						'id' => 'bottom_border',
						'value' => '1',
					),
				),
			),
			'bottom_border_style' => array(
				'default' => 'solid',
				'type' => 'dropdown',
				'options' => 'border_styles',
				'dependency' => array(
					array(
						'id' => 'style',
						'value' => 'grouped',
					),
					array(
						'id' => 'bottom_border',
						'value' => '1',
					),
				),
			),
			'bottom_border_color' => array(
				'default' => '#eeeeee',
				'type' => 'color',
				'dependency' => array(
					array(
						'id' => 'style',
						'value' => 'grouped',
					),
					array(
						'id' => 'bottom_border',
						'value' => '1',
					),
				),
			),
		);

		if ( 'legacy' === WPRM_Settings::get( 'nutrition_label_style' ) ) {
			$atts['label_background_color'] = array(
				'default' => '#ffffff',
				'type' => 'color',
				'dependency' => array(
					'id' => 'style',
					'value' => 'label',
				),
			);
			$atts['label_text_color'] = array(
				'default' => '#000000',
				'type' => 'color',
				'dependency' => array(
					'id' => 'style',
					'value' => 'label',
				),
			);
		}

		$section_atts = WPRM_Shortcode_Helper::get_section_atts();
		$section_atts['text_style']['dependency'] = array(
			'id' => 'style',
			'value' => 'label',
			'type' => 'inverse',
		);

		$atts = array_merge( $atts, WPRM_Shortcode_Helper::get_section_atts() );

		$atts = array_merge( $atts, array(
				'text_style' => array(
					'default' => 'normal',
					'type' => 'dropdown',
					'options' => 'text_styles',
					'dependency' => array(
						'id' => 'style',
						'value' => 'label',
						'type' => 'inverse',
					),
				),
				'header' => array(
					'default' => '',
					'type' => 'text',
				),
				'header_tag' => array(
					'default' => 'h3',
					'type' => 'dropdown',
					'options' => 'header_tags',
					'dependency' => array(
						'id' => 'header',
						'value' => '',
						'type' => 'inverse',
					),
				),
				'header_style' => array(
					'default' => 'bold',
					'type' => 'dropdown',
					'options' => 'text_styles',
					'dependency' => array(
						'id' => 'header',
						'value' => '',
						'type' => 'inverse',
					),
				),
				'container_header' => array(
					'type' => 'header',
					'default' => __( 'Nutrition Container', 'wp-recipe-maker' ),
				),
				'nutrition_header' => array(
					'type' => 'header',
					'default' => __( 'Nutrition Fields', 'wp-recipe-maker' ),
					'dependency' => array(
						'id' => 'style',
						'value' => 'label',
						'type' => 'inverse',
					),
				),
				'label_color' => array(
					'default' => '#777777',
					'type' => 'color',
					'dependency' => array(
						'id' => 'style',
						'value' => 'label',
						'type' => 'inverse',
					),
				),
				'value_color' => array(
					'default' => '#333333',
					'type' => 'color',
					'dependency' => array(
						'id' => 'style',
						'value' => 'label',
						'type' => 'inverse',
					),
				),
				'label_separator' => array(
					'default' => ': ',
					'type' => 'text',
					'dependency' => array(
						'id' => 'style',
						'value' => 'label',
						'type' => 'inverse',
					),
				),
				'label_style' => array(
					'default' => 'normal',
					'type' => 'dropdown',
					'options' => 'text_styles',
					'dependency' => array(
						'id' => 'style',
						'value' => 'label',
						'type' => 'inverse',
					),
				),
				'nutrition_separator' => array(
					'default' => ' | ',
					'type' => 'text',
					'dependency' => array(
						'id' => 'style',
						'value' => 'simple',
					),
				),
				'unit_separator' => array(
					'default' => '',
					'type' => 'text',
					'dependency' => array(
						'id' => 'style',
						'value' => 'label',
						'type' => 'inverse',
					),
				),
				'nutrition_values' => array(
					'default' => 'serving',
					'type' => 'dropdown',
					'options' => array(
						'serving' => __( 'Per serving', 'wp-recipe-maker' ),
						'100g' => __( 'Per 100g', 'wp-recipe-maker' ),
					),
					'dependency' => array(
						'id' => 'style',
						'value' => 'label',
						'type' => 'inverse',
					),
				),
				'daily' => array(
					'default' => '0',
					'type' => 'toggle',
					'dependency' => array(
						'id' => 'style',
						'value' => 'label',
						'type' => 'inverse',
					),
				),
				'daily_seperator' => array(
					'default' => 'parentheses',
					'type' => 'dropdown',
					'options' => array(
						'parentheses' => 'Parentheses',
						'dash' => 'Dash',
						'none' => 'None',
					),
					'dependency' => array(
						array(
							'id' => 'style',
							'value' => 'label',
							'type' => 'inverse',
						),
						array(
							'id' => 'daily',
							'value' => '1',
						),
					),
				),
				'align' => array(
					'default' => 'left',
					'type' => 'dropdown',
					'options' => array(
						'left' => 'Aligned left',
						'center' => 'Aligned center',
						'right' => 'Aligned right',
					),
				),
			)
		);

		$atts = WPRM_Shortcode_Helper::insert_atts_after_key( $atts, 'container_header', WPRM_Shortcode_Helper::get_internal_container_atts() );
		self::$attributes = $atts;

		parent::init();
	}

	/**
	 * Output for the shortcode.
	 *
	 * @since	4.0.0
	 * @param	array $atts Options passed along with the shortcode.
	 */
	public static function shortcode( $atts ) {
		$atts = parent::get_attributes( $atts );

		$recipe = WPRM_Template_Shortcodes::get_recipe( $atts['id'] );
		if ( ! $recipe || 'disabled' === $atts['align'] ) {
			return apply_filters( parent::get_hook(), '', $atts, $recipe );
		}

		$output = '';

		// Show teaser for Premium only shortcode in Template editor.
		if ( $atts['is_template_editor_preview'] ) {
			$output = '<div class="wprm-template-editor-premium-only">The Nutrition Label is only available in <a href="https://bootstrapped.ventures/wp-recipe-maker/get-the-plugin/">WP Recipe Maker Premium</a>.</div>';
		}

		return apply_filters( parent::get_hook(), $output, $atts, $recipe );
	}
}

WPRM_SC_Nutrition_Label::init();