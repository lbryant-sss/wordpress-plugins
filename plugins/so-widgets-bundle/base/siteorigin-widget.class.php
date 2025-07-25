<?php

/**
 * Class SiteOrigin_Widget
 *
 * @author SiteOrigin <support@siteorigin.com>
 */
abstract class SiteOrigin_Widget extends WP_Widget {
	protected $form_options;
	protected $base_folder;
	protected $field_ids;
	protected $fields;

	/**
	 * The name of this widget class. Whatever the key for $wp_widgets_factory is.
	 *
	 * @var string
	 */
	protected $widget_class;

	/**
	 * @var array The array of registered frontend scripts
	 */
	protected $frontend_scripts = array();

	/**
	 * @var array The array of registered frontend styles
	 */
	protected $frontend_styles = array();

	protected $generated_css = array();
	protected $current_instance;
	protected $instance_storage;

	/**
	 * @var int How many seconds a CSS file is valid for.
	 */
	public static $css_expire = 604800; // 7 days

	/**
	 * @param string $id
	 * @param string $name
	 * @param array  $widget_options  Optional Normal WP_Widget widget options and a few extras.
	 *   - help: A URL which, if present, causes a help link to be displayed on the Edit Widget modal.
	 *   - instance_storage: Whether or not to temporarily store instances of this widget.
	 *   - has_preview: Whether or not this widget has a preview to display. If false, the form does not output a
	 *                  'Preview' button.
	 * @param array  $control_options Optional Normal WP_Widget control options.
	 * @param array  $form_options    Optional An array describing the form fields used to configure SiteOrigin widgets.
	 * @param mixed  $base_folder     Optional
	 */
	public function __construct( $id, $name, $widget_options = array(), $control_options = array(), $form_options = array(), $base_folder = false ) {
		$this->form_options = $form_options;
		$this->base_folder = $base_folder;
		$this->field_ids = array();
		$this->fields = array();

		$widget_options = wp_parse_args( $widget_options, array(
			'has_preview' => true,
		) );

		$control_options = wp_parse_args( $control_options, array(
			'width' => 800,
		) );

		if ( empty( $this->widget_class ) ) {
			$this->widget_class = get_class( $this );
		}

		parent::__construct( $id, $name, $widget_options, $control_options );
		$this->initialize();

		// Let other plugins do additional initializing here
		do_action( 'siteorigin_widgets_initialize_widget_' . $this->id_base, $this );
	}

	/**
	 * Initialize this widget in whatever way we need to. Run before rendering widget or form.
	 */
	public function initialize() {
	}

	/**
	 * Get the main widget form. This should be overwritten by child widgets.
	 *
	 * @return array
	 */
	public function get_widget_form() {
		return method_exists( $this, 'initialize_form' ) ? $this->initialize_form() : array();
	}

	private function get_cached_widget_form() {
		$cache_key = $this->id_base . '_form';
		$form_options = wp_cache_get( $cache_key, 'siteorigin_widgets' );

		if ( empty( $form_options ) ) {
			$form_options = $this->get_widget_form();
			wp_cache_set( $cache_key, $form_options, 'siteorigin_widgets' );
		}

		return $form_options;
	}

	/**
	 * Check if a child widget implements a specific form type.
	 *
	 * @param string $form_type
	 *
	 * @return bool
	 */
	public function has_form( $form_type = 'widget' ) {
		return method_exists( $this, 'get_' . $form_type . '_form' );
	}

	/**
	 * Get a specific type of form.
	 *
	 * @return array The form array, or an empty array if the form doesn't exist.
	 */
	public function get_form( $form_type ) {
		$form_options = $this->has_form( $form_type ) ? call_user_func( array( $this, 'get_' . $form_type . '_form' ) ) : array();

		if ( $form_type == 'settings' ) {
			// Allow plugins to filter global widgets form.
			$form_options = apply_filters( 'siteorigin_widgets_settings_form', $form_options, $this );
			$form_options = apply_filters( 'siteorigin_widgets_settings_form_' . $this->id_base, $form_options, $this );
		}

		return $form_options;
	}

	/**
	 * Get the main form options and allow child widgets to modify that form.
	 *
	 * @param bool|SiteOrigin_Widget $parent
	 *
	 * @return mixed
	 */
	public function form_options( $parent = false ) {
		if ( empty( $this->form_options ) ) {
			// If the widget doesn't have form_options defined from the constructor, then it might be defining them in the get_widget_form function
			$this->form_options = $this->get_cached_widget_form();
		}

		$form_options = $this->modify_form( $this->form_options );

		if ( ! empty( $parent ) ) {
			$form_options = $parent->modify_child_widget_form( $form_options, $this );
		}

		// Give other plugins a way to modify this form.
		$form_options = apply_filters( 'siteorigin_widgets_form_options', $form_options, $this );
		$form_options = apply_filters( 'siteorigin_widgets_form_options_' . $this->id_base, $form_options, $this );

		return $form_options;
	}

	/**
	 * Display the widget.
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		if ( empty( $this->form_options ) ) {
			$form_options = $this->get_cached_widget_form();
		} else {
			$form_options = $this->modify_form( $this->form_options );
		}

		$instance = $this->modify_instance( $instance );

		// Filter the instance
		$instance = apply_filters( 'siteorigin_widgets_instance', $instance, $this );
		$instance = apply_filters( 'siteorigin_widgets_instance_' . $this->id_base, $instance, $this );

		$args = wp_parse_args( $args, array(
			'before_widget' => '',
			'after_widget' => '',
			'before_title' => '',
			'after_title' => '',
		) );

		// Add any missing default values to the instance
		$instance = $this->add_defaults( $form_options, $instance );

		if ( empty( $GLOBALS[ 'SITEORIGIN_PANELS_PREVIEW_RENDER' ] ) ) {
			$css_name = $this->generate_and_enqueue_instance_styles( $instance );
			$this->enqueue_frontend_scripts( $instance );
		} else {
			$css_name = 'panels-preview';
		}

		$template_vars = $this->get_template_variables( $instance, $args );
		$template_vars = apply_filters( 'siteorigin_widgets_template_variables_' . $this->id_base, $template_vars, $instance, $args, $this );

		// Storage hash allows templates to get access to
		$template_vars[ 'storage_hash' ] = '';

		if ( ! empty( $this->widget_options['instance_storage'] ) ) {
			$stored_instance = $this->modify_stored_instance( $instance );
			// We probably don't want panels_info
			unset( $stored_instance['panels_info'] );

			$template_vars[ 'storage_hash' ] = substr( md5( serialize( $stored_instance ) ), 0, 8 );

			if ( ! empty( $stored_instance ) && !$this->is_preview( $instance ) ) {
				// Store this if we have a non empty instance and are not previewing
				set_transient( 'sow_inst[' . $this->id_base . '][' . $template_vars['storage_hash'] . ']', $stored_instance, 7 * 86400 );
			}
		}

		if ( ! method_exists( $this, 'get_html_content' ) ) {
			$template_file = siteorigin_widget_get_plugin_dir_path( $this->id_base ) . $this->get_template_dir( $instance ) . '/' . $this->get_template_name( $instance ) . '.php';
			$template_file = apply_filters( 'siteorigin_widgets_template_file_' . $this->id_base, $template_file, $instance, $this );
			$template_file = realpath( $template_file );

			// Don't accept non PHP files
			if ( substr( $template_file, -4 ) != '.php' ) {
				$template_file = false;
			}

			ob_start();

			if ( ! empty( $template_file ) && file_exists( $template_file ) ) {
				extract( $template_vars );
				@ include $template_file;
			}
			$template_html = ob_get_clean();

			// This is a legacy, undocumented filter.
			$template_html = apply_filters( 'siteorigin_widgets_template', $template_html, $this->widget_class, $instance, $this );
			$template_html = apply_filters( 'siteorigin_widgets_template_html_' . $this->id_base, $template_html, $instance, $this );
		} else {
			$template_html = $this->get_html_content( $instance, $args, $template_vars, $css_name );
		}

		$wrapper_classes = apply_filters(
			'siteorigin_widgets_wrapper_classes_' . $this->id_base,
			array( 'so-widget-' . $this->id_base, 'so-widget-' . $css_name ),
			$instance,
			$this
		);
		$wrapper_classes = array_map( 'sanitize_html_class', $wrapper_classes );
		$wrapper_id = apply_filters( 'siteorigin_widgets_wrapper_id_' . $this->id_base, '', $instance, $this );

		$wrapper_data_string = $this->get_wrapper_data( $instance );

		do_action( 'siteorigin_widgets_before_widget_' . $this->id_base, $instance, $this );
		echo $args['before_widget'];
		echo '<div
			' . ( ! empty( $wrapper_id ) ? 'id="' . esc_attr( $wrapper_id ) . '"' : '' ) . '
			class="' . esc_attr( implode( ' ', $wrapper_classes ) ) . '"
			' . $wrapper_data_string . '
		>';
		echo $template_html;
		echo '</div>';
		echo $args['after_widget'];
		do_action( 'siteorigin_widgets_after_widget_' . $this->id_base, $instance, $this );

		// If this is a widget preview, we need to print the styling inline
		if ( $this->is_preview( $instance ) ) {
			siteorigin_widget_print_styles();
		}
	}

	private function get_wrapper_data( $instance ) {
		$data = apply_filters(
			'siteorigin_widgets_wrapper_data_' . $this->id_base,
			array(),
			$instance,
			$this
		);
		$wrapper_attr_string = '';

		foreach ( $data as $name => $value ) {
			$wrapper_attr_string .= ' data-' . siteorigin_sanitize_attribute_key( $name ) . '="' . esc_attr( $value ) . '"';
		}

		return $wrapper_attr_string;
	}

	/**
	 * Generate the CSS for this widget and display it in the appropriate way
	 *
	 * @param $instance array The instance array
	 *
	 * @return string The CSS name
	 */
	public function generate_and_enqueue_instance_styles( $instance ) {
		if ( empty( $form_options ) ) {
			$form_options = $this->get_cached_widget_form();
		} else {
			$form_options = $this->modify_form( $this->form_options );
		}

		// We'll assume empty instances don't have styles
		if ( empty( $instance ) ) {
			return;
		}

		// Make sure all the default values are in place
		$instance = $this->add_defaults( $form_options, $instance );

		$this->current_instance = $instance;
		$style = $this->get_style_name( $instance );

		$upload_dir = wp_upload_dir();

		if ( ! empty( $style ) ) {
			$hash = $this->get_style_hash( $instance );
			$css_name = $this->id_base . '-' . $style . '-' . $hash . ( ! empty( $instance['panels_info'] ) && ! isset( $instance['panels_info']['builder'] ) ? '-' . get_the_id() : '' );

			//Ensure styles aren't generated and enqueued more than once.
			$in_preview = $this->is_preview( $instance ) || ( isset( $_POST['action'] ) && $_POST['action'] == 'so_widgets_preview' );

			if ( ! in_array( $css_name, $this->generated_css ) || $in_preview ) {
				if ( $in_preview || ( defined( 'SITEORIGIN_WIDGETS_DEBUG' ) && SITEORIGIN_WIDGETS_DEBUG ) ) {
					siteorigin_widget_add_inline_css( $this->get_instance_css( $instance ) );
				} else {
					if ( ! file_exists( $upload_dir['basedir'] . '/siteorigin-widgets/' . $css_name . '.css' ) ) {
						// Attempt to recreate the CSS
						$this->save_css( $instance );
					}

					if ( file_exists( $upload_dir['basedir'] . '/siteorigin-widgets/' . $css_name . '.css' ) ) {
						if ( ! wp_style_is( $css_name ) ) {
							wp_enqueue_style(
								$css_name,
								set_url_scheme( $upload_dir['baseurl'] . '/siteorigin-widgets/' . $css_name . '.css' )
							);
						}
					} else {
						// Fall back to using inline CSS if we can't find the cached CSS file.
						// Try get the cached value.
						$css = wp_cache_get( $css_name, 'siteorigin_widgets' );

						if ( empty( $css ) ) {
							$css = $this->get_instance_css( $instance );
						}
						siteorigin_widget_add_inline_css( $css );
					}
				}
				$this->generated_css[] = $css_name;
			}
		} else {
			$css_name = $this->id_base . '-base';
		}

		$this->current_instance = false;

		return $css_name;
	}

	private function is_customize_preview() {
		global $wp_customize;

		return is_a( $wp_customize, 'WP_Customize_Manager' ) && $wp_customize->is_preview();
	}

	protected function is_block_editor_page() {
		return SiteOrigin_Widgets_Bundle::single()->is_block_editor();
	}

	/**
	 * Get an array of variables to make available to templates. By default, just return an array. Should be overwritten by child widgets.
	 *
	 * @return array
	 */
	public function get_template_variables( $instance, $args ) {
		return array();
	}

	/**
	 * Render a sub widget. This should be used inside template files.
	 */
	public function sub_widget( $class, $args, $instance, $return = false ) {
		if ( ! class_exists( $class ) ) {
			return;
		}
		$widget = new $class();

		$args['before_widget'] = '';
		$args['after_widget'] = '';

		if ( $return ) {
			ob_start();
		}

		$widget->widget( $args, $instance );

		if ( $return ) {
			return ob_get_clean();
		}
	}

	/**
	 * Add default values to the instance.
	 */
	public function add_defaults( $form, $instance = array(), $level = 0 ) {
		if ( $level > 10 ) {
			return $instance;
		}
		
		// Ensure $instance is an array - if not, return it as-is to prevent type errors.
		if ( ! is_array( $instance ) ) {
			return $instance;
		}

		foreach ( $form as $id => $field ) {
			if ( $field['type'] == 'repeater' ) {
				if ( is_array( $instance[ $id ] ) ) {
					foreach ( array_keys( $instance[ $id ] ) as $i ) {
						$instance[ $id ][ $i ] = $this->add_defaults( $field['fields'], $instance[ $id ][ $i ], $level + 1 );
					}
				}
			} elseif ( $field['type'] == 'section' ) {
				if ( empty( $instance ) ) {
					$instance = array();
				}

				if ( empty( $instance[ $id ] ) ) {
					$instance[ $id ] = array();
				}

				$instance[ $id ] = $this->add_defaults( $field['fields'], $instance[ $id ], $level + 1 );
			} elseif ( $field['type'] == 'measurement' ) {
				if ( ! isset( $instance[ $id ] ) ) {
					$instance[ $id ] = isset( $field['default'] ) ? $field['default'] : '';
				}

				if ( empty( $instance[ $id . '_unit' ] ) ) {
					$instance[ $id . '_unit' ] = 'px';
				}
			} elseif ( $field['type'] == 'order' ) {
				if ( empty( $instance[ $id ] ) ) {
					if ( ! empty( $field['default'] ) ) {
						$instance[ $id ] = $field['default'];
					} else {
						// If no default order is specified, just use the order of the options.
						$instance[ $id ] = array_keys( $field['options'] );
					}
				}

			} elseif ( $field['type'] === 'widget' ) {
				// We need to load the widget to be able to get its defaults.
				$sub_widget = new $field['class'];
				if ( ! is_a( $sub_widget, 'SiteOrigin_Widget' ) ) {
					continue;
				}

				if ( empty( $instance[ $id ] ) ) {
					$instance[ $id ] = array();
				}

				// Does this widget have a form filter?
				if (
					! empty( $field['form_filter'] ) &&
					is_callable( $field['form_filter'] )
				) {

					$fields = call_user_func(
						$field['form_filter'],
						$sub_widget->form_options()
					);

					$instance[ $id ] = $this->add_defaults(
						$fields,
						$instance[ $id ],
						$level + 1
					);

					continue;
				}

				$instance[ $id ] = $this->add_defaults(
					$sub_widget->form_options(),
					$instance[ $id ],
					$level + 1
				);
			} elseif ( ! isset( $instance[ $id ] ) ) {
				$instance[ $id ] = isset( $field['default'] ) ? $field['default'] : '';
			}
		}

		return $instance;
	}

	/**
	 * Display the widget form.
	 *
	 * @param array  $instance
	 * @param string $form_type Which type of form we're using
	 *
	 * @return string|void
	 */
	public function form( $instance, $form_type = 'widget' ) {
		if ( $form_type == 'widget' ) {
			if ( empty( $this->form_options ) ) {
				$this->form_options = $this->form_options();
			}
			$form_options = $this->form_options;
		} else {
			$form_options = $this->get_form( $form_type );
		}

		$instance = $this->modify_instance( $instance );
		$instance = $this->add_defaults( $form_options, $instance );

		if ( empty( $this->number ) ) {
			// Compatibility with form widgets.
			$this->number = 1;
		}

		// Filter the instance specifically for the form
		$instance = apply_filters( 'siteorigin_widgets_form_instance_' . $this->id_base, $instance, $this );

		// `more_entropy` adds a period to the id.
		$id = str_replace( '.', '', uniqid( rand(), true ) );
		$form_id = 'siteorigin_widget_form_' . md5( $id );
		$class_name = str_replace( '_', '-', strtolower( $this->widget_class ) );

		if ( empty( $instance['_sow_form_id'] ) ) {
			$instance['_sow_form_id'] = $id;
		}
		?>
		<div class="siteorigin-widget-form siteorigin-widget-form-main siteorigin-widget-form-main-<?php echo esc_attr( $class_name ); ?>"
			 id="<?php echo $form_id; ?>" data-class="<?php echo esc_attr( $this->widget_class ); ?>"
			 data-id-base="<?php echo esc_attr( $this->id_base ); ?>" style="display: none">
			<?php
			$this->display_teaser_message();
		/* @var $field_factory SiteOrigin_Widget_Field_Factory */
		$field_factory = SiteOrigin_Widget_Field_Factory::single();
		$fields_javascript_variables = array();

		foreach ( $form_options as $field_name => $field_options ) {
			/* @var $field SiteOrigin_Widget_Field_Base */
			$field = $field_factory->create_field( $field_name, $field_options, $this );
			$field->render( isset( $instance[ $field_name ] ) ? $instance[ $field_name ] : null, $instance );
			$field_js_vars = $field->get_javascript_variables();

			if ( ! empty( $field_js_vars ) ) {
				$fields_javascript_variables[ $field_name ] = $field_js_vars;
			}
			$field->enqueue_scripts();
			$this->fields[ $field_name ] = $field;
		}
		?>
			<input type="hidden" name="<?php echo $this->so_get_field_name( '_sow_form_id' ); ?>" value="<?php echo esc_attr( $instance['_sow_form_id'] ); ?>" class="siteorigin-widgets-form-id" />
			<input type="hidden" name="<?php echo $this->so_get_field_name( '_sow_form_timestamp' ); ?>" value="<?php echo ! empty( $instance['_sow_form_timestamp'] ) ? esc_attr( $instance['_sow_form_timestamp'] ) : ''; ?>" class="siteorigin-widgets-form-timestamp" />
		</div>
		<div class="siteorigin-widget-form-no-styles">
			<?php $this->scripts_loading_message(); ?>
		</div>

		<?php if ( $this->show_preview_button() ) { ?>
			<div class="siteorigin-widget-preview" style="display: none">
				<a href="#" class="siteorigin-widget-preview-button button-secondary">
					<?php echo esc_html__( 'Preview', 'so-widgets-bundle' ); ?>
				</a>
			</div>
		<?php } ?>

		<?php if ( ! empty( $this->widget_options['help'] ) ) { ?>
			<a href="<?php echo sow_esc_url( $this->widget_options['help'] ); ?>" class="siteorigin-widget-help-link siteorigin-panels-help-link" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'Help', 'so-widgets-bundle' ); ?>
			</a>
		<?php } ?>

		<script type="text/javascript">
			( function( $ ) {
				if ( typeof window.sow_field_javascript_variables == 'undefined' ) window.sow_field_javascript_variables = {};
				window.sow_field_javascript_variables["<?php echo addslashes( $this->widget_class ); ?>"] = <?php echo json_encode( $fields_javascript_variables ); ?>;

				if ( typeof $.fn.sowSetupForm != 'undefined' ) {
					$('#<?php echo $form_id; ?>').sowSetupForm();
				}
				else {
					// Init once admin scripts have been loaded
					$( document).on( 'sowadminloaded', function() {
						$('#<?php echo $form_id; ?>').sowSetupForm();
					} );
				}
			} )( jQuery );
		</script>
		<?php

		$this->enqueue_scripts( $form_type );
	}

	/**
	 * Display the teaser message.
	 */
	public function display_teaser_message() {
		if (
			method_exists( $this, 'get_form_teaser' ) &&
			( $teaser = $this->get_form_teaser() )
		) {
			if ( ! is_admin() ) {
				wp_enqueue_style( 'dashicons' );
			}

			$dismissed = get_user_meta( get_current_user_id(), 'teasers_dismissed', true );

			if ( empty( $dismissed[ $this->id_base ] ) ) {
				$dismiss_url = add_query_arg( array(
					'action' => 'so_dismiss_widget_teaser',
					'widget' => $this->id_base,
				), admin_url( 'admin-ajax.php' ) );
				$dismiss_url = wp_nonce_url( $dismiss_url, 'dismiss-widget-teaser' );

				if ( is_array( $teaser ) ) {
					$teaser = $teaser[ array_rand( $teaser ) ];
				}
				?>
				<section class="siteorigin-widget-teaser">
					<p class="siteorigin-widget-teaser-message">
						<?php echo wp_kses_post( $teaser ); ?>.
					</p>

					<button
						type="button"
						class="siteorigin-widget-teaser-dismiss dashicons dashicons-dismiss"
						data-dismiss-url="<?php echo esc_url( $dismiss_url ); ?>"
					>
						<span class="screen-reader-text">
							<?php echo esc_html( 'Dismiss this message', 'so-widgets-bundle' ); ?>
						</span>
					</button>
				</section>
				<?php
			}
		}
	}

	/**
	 * Should we display the teaser for SiteOrigin Premium
	 *
	 * @return bool
	 */
	public function display_siteorigin_premium_teaser() {
		return apply_filters( 'siteorigin_premium_upgrade_teaser', true ) &&
			! defined( 'SITEORIGIN_PREMIUM_VERSION' );
	}

	public function scripts_loading_message() {
		?>
		<p>
			<strong><?php echo esc_html__( 'This widget has scripts and styles that need to be loaded before you can use it. Please save and reload your current page.', 'so-widgets-bundle' ); ?></strong>
		</p>
		<p>
			<strong><?php echo esc_html__( 'You will only need to do this once.', 'so-widgets-bundle' ); ?></strong>
		</p>
		<?php
	}

	/**
	 * Enqueue the admin scripts for the widget form.
	 *
	 * @param bool|string $form_type Should we enqueue the field scripts too?
	 */
	public function enqueue_scripts( $form_type = false ) {
		if ( ! wp_script_is( 'siteorigin-widget-admin' ) ) {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'siteorigin-widget-admin', plugin_dir_url( SOW_BUNDLE_BASE_FILE ) . 'base/css/admin.css', array( 'media-views' ), SOW_BUNDLE_VERSION );

			wp_enqueue_media();
			wp_enqueue_script(
				'siteorigin-widget-admin',
				plugin_dir_url( SOW_BUNDLE_BASE_FILE ) . 'base/js/admin' . SOW_BUNDLE_JS_SUFFIX . '.js',
				array(
					'jquery',
					'jquery-ui-sortable',
					'jquery-ui-slider',
					'underscore'
				),
				SOW_BUNDLE_VERSION,
				true
			);
			wp_localize_script( 'siteorigin-widget-admin', 'soWidgets', array(
				'ajaxurl' => wp_nonce_url( admin_url( 'admin-ajax.php' ), 'widgets_action', '_widgets_nonce' ),
				'sure' => __( 'Are you sure?', 'so-widgets-bundle' ),
				'missing_required' => __( 'You have empty required fields. Are you sure you wish to continue?', 'so-widgets-bundle' ),
				'table' => array(
					'header' => __( 'Table Header', 'so-widgets-bundle' ),
					'actions' => __( 'Actions', 'so-widgets-bundle' ),
				),
				'backup' => array(
					'enabled' => apply_filters( 'siteorigin_widgets_backup', true ),
					'newerVersion' => __( "There is a newer version of this widget's content available.", 'so-widgets-bundle' ),
					'restore' => __( 'Restore', 'so-widgets-bundle' ),
					'dismiss' => __( 'Dismiss', 'so-widgets-bundle' ),
					'replaceWarning' => sprintf(
						__( 'Clicking %s will replace the current widget contents. You can revert by refreshing the page before updating.', 'so-widgets-bundle' ),
						'<em>' . __( 'Restore', 'so-widgets-bundle' ) . '</em>'
					),
				),
				'fonts' => siteorigin_widgets_font_families(),
				'icons' => array(),
			) );

			if ( ! class_exists( 'FLBuilderModel' ) || ! FLBuilderModel::is_builder_active() ) {
				wp_enqueue_script(
					'wp-color-picker-alpha',
					plugin_dir_url( SOW_BUNDLE_BASE_FILE ) . 'js/lib/wp-color-picker-alpha' . SOW_BUNDLE_JS_SUFFIX . '.js',
					array( 'wp-color-picker' ),
					'3.0.2',
					true
				);
			}

			global $wp_customize;

			if ( isset( $wp_customize ) ) {
				$this->footer_admin_templates();
			} else {
				add_action( 'admin_footer', array( $this, 'footer_admin_templates' ) );
			}
		}

		if ( ! empty( $form_type ) && $this->has_form( $form_type ) ) {
			// Enqueue field scripts for the given form type
			$form_options = $this->get_form( $form_type );
			$this->enqueue_field_scripts( $form_options );
		}

		// This lets the widget enqueue any specific admin scripts
		$this->enqueue_admin_scripts();
		do_action( 'siteorigin_widgets_enqueue_admin_scripts_' . $this->id_base, $this );
	}

	public function enqueue_field_scripts( $fields ) {
		/* @var $field_factory SiteOrigin_Widget_Field_Factory */
		$field_factory = SiteOrigin_Widget_Field_Factory::single();

		foreach ( $fields as $field_name => $field_options ) {
			/* @var $field SiteOrigin_Widget_Field_Base */
			$field = $field_factory->create_field( $field_name, $field_options, $this );
			$field->enqueue_scripts();

			if ( ! empty( $field_options['fields'] ) ) {
				$this->enqueue_field_scripts( $field_options['fields'] );
			}
		}
	}

	/**
	 * Display all the admin stuff for the footer
	 */
	public function footer_admin_templates() {
		?>
		<script type="text/template" id="so-widgets-bundle-tpl-preview-dialog">
			<div class="so-widgets-dialog">
				<div class="so-widgets-dialog-overlay"></div>

				<div class="so-widgets-toolbar">
					<h3>
						<?php echo esc_html__( 'Widget Preview', 'so-widgets-bundle' ); ?>
					</h3>
					<div class="close" tabindex="0"><span class="dashicons dashicons-arrow-left-alt2"></span></div>
				</div>

				<div class="so-widgets-dialog-frame">
					<iframe name="siteorigin-widgets-preview-iframe" id="siteorigin-widget-preview-iframe" style="visibility: hidden"></iframe>
				</div>

				<form target="siteorigin-widgets-preview-iframe" action="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-ajax.php' ), 'widgets_action', '_widgets_nonce' ) ); ?>" method="post">
					<input type="hidden" name="action" value="so_widgets_preview" />
					<input type="hidden" name="data" value="" />
					<input type="hidden" name="class" value="" />
				</form>

			</div>
		</script>
		<?php

		// Give other plugins a chance to add their own
		do_action( 'siteorigin_widgets_footer_admin_templates' );
	}

	/**
	 * Update the widget instance.
	 *
	 * @param array  $new_instance
	 * @param array  $old_instance
	 * @param string $form_type    The type of form we're using.
	 *
	 * @return array|void
	 */
	public function update( $new_instance, $old_instance, $form_type = 'widget' ) {
		if ( ! class_exists( 'SiteOrigin_Widgets_Color_Object' ) ) {
			require plugin_dir_path( __FILE__ ) . 'inc/color.php';
		}

		if ( $form_type == 'widget' ) {
			if ( empty( $this->form_options ) ) {
				$this->form_options = $this->form_options();
			}
			$form_options = $this->form_options;
		} else {
			$form_options = $this->get_form( $form_type );
		}

		if ( ! empty( $form_options ) ) {
			if ( isset( $_GET['fl_builder'] ) && is_array( $new_instance ) ) {
				$key = array_keys( $new_instance )[0];
				$new_instance = $this->update_fields(
					$new_instance[ $key ],
					$old_instance,
					$form_options
				);
			} else {
				$new_instance = $this->update_fields(
					$new_instance,
					$old_instance,
					$form_options
				);
			}
		}

		// Remove the old CSS, it'll be regenerated on page load.
		if ( $form_type == 'widget' ) {
			$this->delete_css( $this->modify_instance( $old_instance ) );
		}

		return $new_instance;
	}

	private function update_fields( $new_instance, $old_instance, $form_options ) {
		if ( ! empty( $form_options ) ) {
			/* @var $field_factory SiteOrigin_Widget_Field_Factory */
			$field_factory = SiteOrigin_Widget_Field_Factory::single();

			foreach ( $form_options as $field_name => $field_options ) {
				/* @var $field SiteOrigin_Widget_Field_Base */
				if ( ! empty( $this->fields ) && ! empty( $this->fields[ $field_name ] ) ) {
					$field = $this->fields[ $field_name ];
				} else {
					$field = $field_factory->create_field( $field_name, $field_options, $this );
					$this->fields[ $field_name ] = $field;
				}

				$new_instance[ $field_name ] = $field->sanitize(
					isset( $new_instance[ $field_name ] ) ? $new_instance[ $field_name ] : null,
					$new_instance,
					isset( $old_instance[ $field_name ] ) ? $old_instance[ $field_name ] : null
				);
				$new_instance = $field->sanitize_instance( $new_instance );
			}

			// Let other plugins also sanitize the instance
			$new_instance = apply_filters( 'siteorigin_widgets_sanitize_instance', $new_instance, $form_options, $this );
			$new_instance = apply_filters( 'siteorigin_widgets_sanitize_instance_' . $this->id_base, $new_instance, $form_options, $this );
			return $new_instance;
		}
	}

	/**
	 * Save the CSS to the filesystem
	 *
	 * @return bool|string
	 */
	private function save_css( $instance ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';

		$style = $this->get_style_name( $instance );
		$hash = $this->get_style_hash( $instance );
		$name = $this->id_base . '-' . $style . '-' . $hash . ( ! empty( $instance['panels_info'] ) && ! isset( $instance['panels_info']['builder'] ) ? '-' . get_the_id() : '' ) . '.css';

		$css = $this->get_instance_css( $instance );

		if ( ! empty( $css ) ) {
			if ( WP_Filesystem() ) {
				global $wp_filesystem;
				$upload_dir = wp_upload_dir();

				$dir_exists = $wp_filesystem->is_dir( $upload_dir['basedir'] . '/siteorigin-widgets/' );

				if ( empty( $dir_exists ) ) {
					// The 'siteorigin-widgets' directory doesn't exist, so try to create it.
					$dir_exists = $wp_filesystem->mkdir( $upload_dir['basedir'] . '/siteorigin-widgets/' );
				}

				if ( ! empty( $dir_exists ) ) {
					// The 'siteorigin-widgets' directory exists, so we can try to write the CSS to a file.
					$wp_filesystem->delete( $upload_dir['basedir'] . '/siteorigin-widgets/' . $name );
					$file_put_success = $wp_filesystem->put_contents(
						$upload_dir['basedir'] . '/siteorigin-widgets/' . $name,
						$css
					);

					// Alert other plugins that we've added a new CSS file
					do_action( 'siteorigin_widgets_stylesheet_added', $name, $instance );
				}
			}

			// We couldn't write to file, so let's use cache instead.
			if ( empty( $file_put_success ) ) {
				wp_cache_add( $name, $css, 'siteorigin_widgets' );
			}

			return $hash;
		}

		return false;
	}

	/**
	 * Clears CSS for a specific instance
	 */
	private function delete_css( $instance ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';

		if ( WP_Filesystem() ) {
			global $wp_filesystem;
			$upload_dir = wp_upload_dir();

			$style = $this->get_style_name( $instance );
			$hash = $this->get_style_hash( $instance );
			$name = $this->id_base . '-' . $style . '-' . $hash . ( ! empty( $instance['panels_info'] ) && ! isset( $instance['panels_info']['builder'] ) ? '-' . get_the_id() : '' );

			$wp_filesystem->delete( $upload_dir['basedir'] . '/siteorigin-widgets/' . $name . '.css' );

			if ( in_array( $name, $this->generated_css ) ) {
				$index = array_search( $name, $this->generated_css );
				unset( $this->generated_css[ $index ] );
				//Reindex array.
				$this->generated_css = array_values( $this->generated_css );
			}

			// Alert other plugins that we've deleted a CSS file.
			do_action( 'siteorigin_widgets_stylesheet_deleted', $name, $instance );
		}
	}

	/**
	 * Clear all old CSS files.
	 *
	 * @var bool Whether to forcefully clear the file cache.
	 */
	public static function clear_file_cache( $force_delete = false ) {
		SiteOrigin_Widgets_Bundle::single()->clear_file_cache( $force_delete, self::$css_expire );
	}

	/**
	 * Generate the CSS for the widget.
	 *
	 * @return string
	 */
	public function get_instance_css( $instance ) {
		if ( ! class_exists( 'SiteOrigin_LessC' ) ) {
			require plugin_dir_path( __FILE__ ) . 'inc/lessc.inc.php';
		}

		if ( ! class_exists( 'SiteOrigin_Widgets_Less_Functions' ) ) {
			require plugin_dir_path( __FILE__ ) . 'inc/less-functions.php';
		}

		if ( !method_exists( $this, 'get_less_content' ) ) {
			$style_name = $this->get_style_name( $instance );

			if ( empty( $style_name ) ) {
				return '';
			}

			$less_file = siteorigin_widget_get_plugin_dir_path( $this->id_base ) . 'styles/' . $style_name . '.less';
			$less_file = apply_filters( 'siteorigin_widgets_less_file_' . $this->id_base, $less_file, $instance, $this );

			$less = ( substr( $less_file, -5 ) == '.less' && file_exists( $less_file ) ) ? file_get_contents( $less_file ) : '';
		} else {
			// The widget is going handle getting the instance LESS
			$less = $this->get_less_content( $instance );
		}

		// Substitute the variables
		if ( ! class_exists( 'SiteOrigin_Widgets_Color_Object' ) ) {
			require plugin_dir_path( __FILE__ ) . 'inc/color.php';
		}

		// Lets widgets insert their own custom generated LESS
		$less = preg_replace_callback( '/\.widget-function\((.*)\);/', array( $this, 'less_widget_inject' ), $less );

		//handle less @import statements
		$less = preg_replace_callback( '/^@import\s+".*?\/?([\w\-\.]+)";/m', array( $this, 'get_less_import_contents' ), $less );

		$vars = apply_filters( 'siteorigin_widgets_less_variables_' . $this->id_base, $this->get_less_variables( $instance ), $instance, $this );

		$less = apply_filters( 'siteorigin_widgets_styles_vars', $less, $vars, $this->widget_class, $instance );
		$less = apply_filters( 'siteorigin_widgets_less_vars_' . $this->id_base, $less, $vars, $instance, $this );

		if ( ! empty( $vars ) ) {
			foreach ( $vars as $name => $value ) {
				// Ignore empty string, false and null values (but keep '0')
				if ( $value === '' || $value === false || $value === null ) {
					continue;
				}

				$less = preg_replace( '/\@' . preg_quote( $name ) . ' *\:.*?;/', '@' . $name . ': ' . $value . ';', $less );
			}
		}

		$less = apply_filters( 'siteorigin_widgets_styles', $less, $this->widget_class, $instance );
		$less = apply_filters( 'siteorigin_widgets_less_' . $this->id_base, $less, $instance, $this );

		$css = '';

		if ( ! empty( $less ) ) {
			$style = $this->get_style_name( $instance );
			$hash = $this->get_style_hash( $instance );
			$css_name = $this->id_base . '-' . $style . '-' . $hash . ( ! empty( $instance['panels_info'] ) && ! isset( $instance['panels_info']['builder'] ) ? '-' . get_the_id() : '' );

			//we assume that any remaining @imports are plain css imports and should be kept outside selectors
			$css_imports = '';

			if ( preg_match_all( '/^@import.+/m', $less, $imports ) ) {
				$css_imports = implode( "\n", $imports[0] );
				$less = preg_replace( '/^@import.+/m', '', $less );
			}

			$less = $css_imports . "\n\n" . '.so-widget-' . $css_name . " { \n" . $less . "\n } ";

			$compiler = new SiteOrigin_LessC();
			$lc_functions = new SiteOrigin_Widgets_Less_Functions( $this, $instance );
			$lc_functions->registerFunctions( $compiler );
			$compiler = apply_filters( 'siteorigin_widgets_less_compiler', $compiler, $instance, $this );

			try {
				if ( method_exists( $compiler, 'compile' ) ) {
					$css = @ $compiler->compile( $less );
				}
			} catch ( Exception $e ) {
				if ( defined( 'SITEORIGIN_WIDGETS_DEBUG' ) && SITEORIGIN_WIDGETS_DEBUG ) {
					throw $e;
				}
			}

			// Remove any attributes with default as the value
			$css = preg_replace( '/[a-zA-Z\-]+ *: *default *;/', '', $css );

			// Remove any empty CSS
			$css = preg_replace( '/[^{}]*\{\s*\}/m', '', $css );
			$css = trim( $css );
		}

		return apply_filters( 'siteorigin_widgets_instance_css', $css, $instance, $this );
	}

	/**
	 * Replaces LESS imports with the content from the actual files. This used as a preg callback.
	 *
	 * @return string
	 */
	private function get_less_import_contents( $matches ) {
		$filename = $matches[1];

		// First, we'll deal with a few special cases
		switch( $filename ) {
			case 'mixins':
				return file_get_contents( plugin_dir_path( __FILE__ ) . 'less/mixins.less' );
				break;

			case 'lesshat':
				return file_get_contents( plugin_dir_path( __FILE__ ) . 'less/lesshat.less' );
				break;
		}

		//get file extension
		preg_match( '/\.\w+$/', $filename, $ext );
		//if there is a file extension and it's not .less or .css we ignore
		if ( ! empty( $ext ) ) {
			if ( ! ( $ext[0] == '.less' || $ext[0] == '.css' ) ) {
				return '';
			}
		} else {
			$filename .= '.less';
		}
		//first check local widget styles directory and then bundle less directory
		$search_path = array(
			siteorigin_widget_get_plugin_dir_path( $this->id_base ) . 'styles/',
			plugin_dir_path( __FILE__ ) . 'less/',
		);

		foreach ( $search_path as $dir ) {
			if ( file_exists( $dir . $filename ) ) {
				return file_get_contents( $dir . $filename ) . "\n\n";
			}
		}

		//file not found
		return '';
	}

	/**
	 * Used as a preg callback to replace .widget-function('some_function', ...) with output from less_some_function($instance, $args).
	 *
	 * @return mixed|string
	 */
	private function less_widget_inject( $matches ) {
		// We're going to lazily split the arguments by comma
		$args = explode( ',', $matches[1] );

		if ( empty( $args[0] ) ) {
			return '';
		}

		// Shift the function name from the arguments
		$func = 'less_' . trim( array_shift( $args ), '\'"' );

		if ( !method_exists( $this, $func ) ) {
			return '';
		}

		// Finally call the function and include the
		$args = array_map( 'trim', $args );

		return call_user_func( array( $this, $func ), $this->current_instance, $args );
	}

	/**
	 * Utility function to get a field name for a widget field.
	 *
	 * @param array $container
	 *
	 * @return mixed|string
	 */
	public function so_get_field_name( $field_name, $container = array() ) {
		if ( empty( $container ) ) {
			$name = $this->get_field_name( $field_name );
		} else {
			// We also need to add the container fields
			$container_extras = '';

			foreach ( $container as $r ) {
				$container_extras .= '[' . $r['name'] . ']';

				if ( $r['type'] == 'repeater' ) {
					$container_extras .= '[#' . $r['name'] . '#]';
				}
			}

			$name = $this->get_field_name( '{{{FIELD_NAME}}}' );
			$name = str_replace( '[{{{FIELD_NAME}}}]', $container_extras . '[' . esc_attr( $field_name ) . ']', $name );
		}

		$name = apply_filters( 'siteorigin_widgets_get_field_name', $name );
		$name = apply_filters( 'siteorigin_widgets_get_field_name_' . $this->id_base, $name );

		return $name;
	}

	/**
	 * Get the ID of this field.
	 *
	 * @param array $container
	 * @param bool  $is_template
	 *
	 * @return string
	 */
	public function so_get_field_id( $field_name, $container = array(), $is_template = false ) {
		if ( empty( $container ) ) {
			return $this->get_field_id( $field_name );
		} else {
			$name = array();

			foreach ( $container as $container_item ) {
				$name[] = $container_item['name'];
			}
			$name[] = $field_name;
			$field_id_base = $this->get_field_id( implode( '-', $name ) );

			if ( $is_template ) {
				return $field_id_base . '-_id_';
			}

			if ( ! isset( $this->field_ids[ $field_id_base ] ) ) {
				$this->field_ids[ $field_id_base ] = 1;
			}
			$curId = $this->field_ids[ $field_id_base ]++;

			return $field_id_base . '-' . $curId;
		}
	}

	/**
	 * Parse markdown
	 *
	 * @return string The HTML
	 *
	 * @deprecated Will be removed in version 2.0
	 */
	public function parse_markdown( $markdown ) {
		if ( ! class_exists( 'Parsedown' ) ) {
			include plugin_dir_path( __FILE__ ) . 'inc/Parsedown.php';
		}
		$parser = new Parsedown();

		return $parser->text( $markdown );
	}

	/**
	 * Get a hash that uniquely identifies this instance.
	 *
	 * @return string
	 */
	public function get_style_hash( $instance ) {
		$style_hash = apply_filters( 'siteorigin_widgets_widget_style_hash', '', $this );

		if ( empty( $style_hash ) ) {
			if ( method_exists( $this, 'get_style_hash_variables' ) ) {
				$vars = apply_filters( 'siteorigin_widgets_hash_variables_' . $this->id_base, $this->get_style_hash_variables( $instance ), $instance, $this );
			} else {
				$vars = apply_filters( 'siteorigin_widgets_less_variables_' . $this->id_base, $this->get_less_variables( $instance ), $instance, $this );
			}
			$version = property_exists( $this, 'version' ) ? $this->version : '';

			$style_hash = substr( md5( json_encode( $vars ) . $version ), 0, 12 );
		}

		return $style_hash;
	}

	/**
	 * Get the template name that we'll be using to render this widget.
	 *
	 * @return mixed
	 */
	public function get_template_name( $instance ) {
		return 'default';
	}

	/**
	 * Get the name of the directory in which we should look for the template. Relative to root of widget folder.
	 *
	 * @return mixed
	 */
	public function get_template_dir( $instance ) {
		return 'tpl';
	}

	/**
	 * Get the LESS style name we'll be using for this widget.
	 *
	 * @return mixed
	 */
	public function get_style_name( $instance ) {
		return 'default';
	}

	/**
	 * Get any variables that need to be substituted by
	 *
	 * @return array
	 */
	public function get_less_variables( $instance ) {
		return array();
	}

	/**
	 * Filter the variables we'll be storing in temporary storage for this instance if we're using `instance_storage`
	 *
	 * @return mixed
	 */
	public function modify_stored_instance( $instance ) {
		return $instance;
	}

	/**
	 * Get the stored instance based on the hash.
	 *
	 * @return object The instance
	 */
	public function get_stored_instance( $hash ) {
		return get_transient( 'sow_inst[' . $this->id_base . '][' . $hash . ']' );
	}

	/**
	 * This function can be overwritten to modify form values in the child widget.
	 *
	 * @return mixed
	 */
	public function modify_form( $form ) {
		return $form;
	}

	/**
	 * This function can be overwritten to modify form values in the child widget.
	 *
	 * @return mixed
	 */
	public function modify_child_widget_form( $child_widget_form, $child_widget ) {
		return $child_widget_form;
	}

	/**
	 * This function should be overwritten by child widgets to filter an instance. Run before rendering the form and widget.
	 *
	 * @return mixed
	 */
	public function modify_instance( $instance ) {
		return $instance;
	}

	/**
	 * Can be overwritten by child widgets to make variables available to javascript via ajax calls. These are designed to be used in the admin.
	 */
	public function get_javascript_variables() {
	}

	/**
	 * Used by child widgets to register scripts to be enqueued for the frontend.
	 *
	 * @param array $scripts an array of scripts. Each element is an array that corresponds to wp_enqueue_script arguments
	 */
	public function register_frontend_scripts( $scripts ) {
		foreach ( $scripts as $script ) {
			if ( ! isset( $this->frontend_scripts[ $script[0] ] ) ) {
				$this->frontend_scripts[ $script[0] ] = $script;
			}
		}
	}

	/**
	 * Enqueue all the registered scripts
	 */
	public function enqueue_registered_scripts( $instance ) {
		$f_scripts = apply_filters(
			'siteorigin_widgets_frontend_scripts_' . $this->id_base,
			$this->frontend_scripts,
			$instance,
			$this
		);

		foreach ( $f_scripts as $f_script ) {
			if ( ! wp_script_is( $f_script[0] ) ) {
				wp_enqueue_script(
					$f_script[0],
					isset( $f_script[1] ) ? $f_script[1] : false,
					isset( $f_script[2] ) ? $f_script[2] : array(),
					! empty( $f_script[3] ) ? $f_script[3] : SOW_BUNDLE_VERSION,
					isset( $f_script[4] ) ? $f_script[4] : false
				);
			}
		}
	}

	/**
	 * Used by child widgets to register styles to be enqueued for the frontend.
	 *
	 * @param array $styles an array of styles. Each element is an array that corresponds to wp_enqueue_style arguments
	 */
	public function register_frontend_styles( $styles ) {
		foreach ( $styles as $style ) {
			if ( ! isset( $this->frontend_styles[ $style[0] ] ) ) {
				$this->frontend_styles[ $style[0] ] = $style;
			}
		}
	}

	/**
	 * Enqueue any frontend styles that were registered
	 */
	public function enqueue_registered_styles( $instance ) {
		$f_styles = apply_filters(
			'siteorigin_widgets_frontend_styles_' . $this->id_base,
			$this->frontend_styles,
			$instance,
			$this
		);

		foreach ( $f_styles as $f_style ) {
			if ( ! wp_style_is( $f_style[0] ) ) {
				wp_enqueue_style(
					$f_style[0],
					isset( $f_style[1] ) ? $f_style[1] : false,
					isset( $f_style[2] ) ? $f_style[2] : array(),
					! empty( $f_style[3] ) ? $f_style[3] : SOW_BUNDLE_VERSION,
					isset( $f_style[4] ) ? $f_style[4] : 'all'
				);
			}
		}
	}

	/**
	 * Can be overridden by child widgets to enqueue scripts and styles for the frontend, but child widgets should
	 * rather register scripts and styles using register_frontend_scripts() and register_frontend_styles(). This function
	 * will then ensure that the scripts are not enqueued more than once.
	 */
	public function enqueue_frontend_scripts( $instance ) {
		$this->enqueue_registered_scripts( $instance );
		$this->enqueue_registered_styles( $instance );

		// Give plugins a chance to enqueue additional frontend scripts
		do_action( 'siteorigin_widgets_enqueue_frontend_scripts_' . $this->id_base, $instance, $this );
	}

	/**
	 * Can be overwritten by child widgets to enqueue admin scripts and styles if necessary.
	 */
	public function enqueue_admin_scripts() {
	}

	/**
	 * Check if we're currently in a preview
	 *
	 * @param array $instance
	 *
	 * @return bool
	 */
	public function is_preview( $instance = array() ) {
		// Check if the instance is a preview
		if ( ! empty( $instance[ 'is_preview' ] ) ) {
			return true;
		}

		// Check if the general request is a preview
		$is_preview =
			is_preview() || // Is this a standard preview
			$this->is_customize_preview() || // Is this a customizer preview
			$this->is_block_editor_page() || // Is this a block editor page
			! empty( $_GET['siteorigin_panels_live_editor'] ) || // Is this a Page Builder live editor request
			( ! empty( $_REQUEST['action'] ) && $_REQUEST['action'] == 'so_panels_builder_content' ) || // Is this a Page Builder content ajax request
			! empty( $GLOBALS[ 'SITEORIGIN_PANELS_PREVIEW_RENDER' ] ); // Is this a Page Builder preview render.

		return apply_filters( 'siteorigin_widgets_is_preview', $is_preview, $this );
	}

	/**
	 * Whether or not so show the 'Preview' button
	 *
	 * @return bool
	 */
	public function show_preview_button() {
		$show_preview = ! empty( $this->widget_options['has_preview'] ) && ! $this->is_customize_preview();
		$show_preview = apply_filters( 'siteorigin_widgets_form_show_preview_button', $show_preview, $this );

		return $show_preview;
	}

	/**
	 * Get the global settings from the options table.
	 *
	 * @param string|null $key
	 *
	 * @return mixed
	 */
	public function get_global_settings( $key = null ) {
		$values = get_option( 'so_widget_settings[' . $this->widget_class . ']', array() );

		// Add in the defaults
		if ( $this->has_form( 'settings' ) ) {
			// Allow plugins to filter global widgets form.
			$form_options = apply_filters( 'siteorigin_widgets_settings_form', $this->get_settings_form(), $this );
			$form_options = apply_filters( 'siteorigin_widgets_settings_form_' . $this->id_base, $form_options, $this );
			$values = $this->add_defaults( $form_options, $values );
		}

		return ! empty( $key ) ? $values[ $key ] : $values;
	}

	/**
	 * Save the global settings. Handles validation too.
	 *
	 * @param array $values The new values
	 *
	 * @return array The sanitized values.
	 */
	public function save_global_settings( $values ) {
		$current = $this->get_global_settings();

		$values = $this->update( $values, $current, 'settings' );

		unset( $values['_sow_form_id'] );
		update_option( 'so_widget_settings[' . $this->widget_class . ']', $values );

		return $values;
	}

	/**
	 * Add state_handler for fields based on how they're adjusted by preset data.
	 *
	 * @param string $state_name
	 * @param array  $preset_data
	 * @param array  $fields      The fields to apply state handlers too.
	 *
	 * @return array $fields with any state_handler's applied.
	 */
	public function dynamic_preset_state_handler( $state_name, $preset_data, $fields ) {
		// Build an array of all the adjusted fields by the preset data, and note which presets adjust them.
		$adjusted_fields = array();

		foreach ( $preset_data as $preset_id => $preset ) {
			$adjusted_fields = array_merge_recursive(
				$this->dynamic_preset_extract_fields(
					$preset['values'],
					$preset_id
				),
				$adjusted_fields
			);
		}

		// Apply state handlers to fields.
		return $this->dynamic_preset_add_state_handler(
			$state_name,
			$adjusted_fields,
			$fields,
			true
		);
	}

	/**
	 * Build an array of all of fields adjusted by the preset data, and note which presets adjust them.
	 *
	 * @param array $fields    The fields to extract preset usage from.
	 * @param array $preset_id
	 *
	 * @return array An array containing extracted fields.
	 */
	private function dynamic_preset_extract_fields( $fields, $preset_id ) {
		$extracted_fields = array();

		foreach ( $fields as $field_key => $field ) {
			// Does this field have sub fields?
			if ( is_array( $field ) ) {
				$extracted_fields[ $field_key ] = $this->dynamic_preset_extract_fields( $field, $preset_id );
			} else {
				$extracted_fields[ $field_key ][] = $preset_id;
				// Add a key that contains all of the presets that adjust this section.
				$extracted_fields['key'][ $preset_id ] = $preset_id;
			}
		}

		return $extracted_fields;
	}

	/**
	 * Add state_handler to fields based on preset adjusted fields.
	 *
	 * @param string $state_name
	 * @param array  $preset_adjusted_fields
	 * @param array  $fields                 The fields to apply state handlers too.
	 * @param array false $exclude_section Whether to add state emitter to the current section.
	 *
	 * @return array $fields with any state_handler's applied.
	 */
	private function dynamic_preset_add_state_handler( $state_name, $adjusted_fields, $fields, $exclude_section = false ) {
		foreach ( $adjusted_fields as $field => $field_value ) {
			// Skip field if it's not adjusted by of the presets, or if the field has a state_handler already.
			if (
				! isset( $fields[ $field ] ) ||
				isset( $fields[ $field ]['state_handler'] )
			) {
				continue;
			}

			$used_by = null;

			// If this is a section field, we need to apply the state handlers for sub fields.
			if ( $fields[ $field ]['type'] == 'section' ) {
				$fields[ $field ]['fields'] = $this->dynamic_preset_add_state_handler(
					$state_name,
					$field_value,
					$fields[ $field ]['fields']
				);

				if ( isset( $field_value['key'] ) ) {
					$used_by = implode( ',', $field_value['key'] );
				}
			} else {
				$used_by = implode( ',', $field_value );
			}

			if ( ! $exclude_section && ! empty( $used_by ) ) {
				$fields[ $field ]['state_handler'] = array(
					$state_name . '[' . $used_by . ']' => array( 'show' ),
						'_else[' . $state_name . ']' => array( 'hide' ),
				);
			}
		}

		return $fields;
	}
}
