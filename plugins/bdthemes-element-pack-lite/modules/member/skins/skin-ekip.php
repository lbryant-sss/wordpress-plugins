<?php
namespace ElementPack\Modules\Member\Skins;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;
use ElementPack\Base\Module_Base;

use Elementor\Skin_Base as Elementor_Skin_Base;

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class Skin_Ekip extends Elementor_Skin_Base {
	protected function _register_controls_actions() {
		parent::_register_controls_actions();

		add_action( 'elementor/element/bdt-member/section_style/before_section_start', [ $this, 'register_ekip_style_controls' ] );

	}

	public function get_id() {
		return 'bdt-ekip';
	}

	public function get_title() {
		return __( 'Ekip', 'bdthemes-element-pack' );
	}

	public function register_ekip_style_controls( Module_Base $widget ) {
		$this->parent = $widget;

		$this->start_controls_section(
			'section_style_phaedra',
			[ 
				'label' => __( 'Ekip', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'ekip_overlay_background_color',
				'label'    => __( 'Background', 'bdthemes-element-pack' ),
				'types'    => [ 'gradient' ],
				'selector' => '{{WRAPPER}} .bdt-member.skin-ekip .ekip-overlay',
			]
		);

		$this->add_control(
			'ekip_overlay_line_color',
			[ 
				'label'     => __( 'Overlay Line Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-member.skin-ekip .ekip-overlay' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	public function render() {
		$ekip_id  = 'ekip' . $this->parent->get_id();
		$settings = $this->parent->get_settings_for_display();

		$image_mask = $settings['image_mask_popover'] == 'yes' ? ' bdt-image-mask' : '';
		$this->parent->add_render_attribute( 'skin-ekip', 'class', 'bdt-member skin-ekip bdt-transition-toggle' . $image_mask );

		if ( ( $settings['member_alternative_photo'] ) and ( ! empty ( $settings['alternative_photo']['url'] ) ) ) {
			$this->parent->add_render_attribute( 'skin-ekip', 'class', [ 'bdt-position-relative', 'bdt-overflow-hidden', 'bdt-transition-toggle' ] );
			$this->parent->add_render_attribute( 'skin-ekip', 'bdt-toggle', 'target: > div > .bdt-member-photo-flip; mode: hover; animation: bdt-animation-fade; queued: true; duration: 300;' );
		}

		if ( ! isset ( $settings['social_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['social_icon'] = 'fab fa-facebook-f';
		}

		?>
		<div <?php $this->parent->print_render_attribute_string( 'skin-ekip' ); ?>>

			<?php if ( ! empty ( $settings['photo']['url'] ) ) :
				$photo_hover_animation = ( '' != $settings['photo_hover_animation'] ) ? ' bdt-transition-scale-' . $settings['photo_hover_animation'] : ''; ?>

				<div class="bdt-member-photo-wrapper">

					<?php if ( ( $settings['member_alternative_photo'] ) and ( ! empty ( $settings['alternative_photo']['url'] ) ) ) : ?>
						<div class="bdt-member-photo-flip bdt-position-absolute bdt-position-z-index">
							<?php echo wp_kses_post( Group_Control_Image_Size::get_attachment_image_html( $settings, 'alternative_photo' ) ); ?>
						</div>
					<?php endif; ?>

					<div class="bdt-member-photo">
						<div class="<?php echo esc_attr( $photo_hover_animation ); ?>">
							<?php echo wp_kses_post( Group_Control_Image_Size::get_attachment_image_html( $settings, 'photo' ) ); ?>
						</div>
					</div>

				</div>

			<?php endif; ?>

			<div class="ekip-overlay bdt-position-z-index">
				<div class="bdt-member-desc">

					<div class="bdt-member-content">
						<?php if ( ! empty ( $settings['role'] ) ) : ?>
							<span class="bdt-member-role">
								<?php echo wp_kses( $settings['role'], element_pack_allow_tags( 'title' ) ); ?>
							</span>
						<?php endif; ?>
						<?php if ( ! empty ( $settings['name'] ) ) : ?>
							<span class="bdt-member-name">
								<?php echo wp_kses( $settings['name'], element_pack_allow_tags( 'title' ) ); ?>
							</span>
						<?php endif; ?>
					</div>

					<?php $this->parent->render_social_icons(''); ?>
				</div>
			</div>

		</div>
		<?php
	}
}

