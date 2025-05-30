<?php
/**
 * Course Widget Register
 *
 * @package Tutor
 * @author Themeum <support@themeum.com>
 * @link https://themeum.com
 * @since 1.3.1
 */

namespace TUTOR;

use Tutor\Models\CourseModel;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Course Widget Class
 *
 * @since 1.3.1
 */
class Course_Widget extends \WP_Widget {

	/**
	 * Constructor
	 *
	 * @since 1.3.1
	 * @return void
	 */
	public function __construct() {
		parent::__construct(
			'tutor_course_widget', // Base ID.
			_x( 'Tutor Course', 'widget title', 'tutor' ),
			array(
				'description' => _x( 'Display courses wherever widget support is available.', 'widget description', 'tutor' ),
			)
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @since 1.3.1
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {
		echo wp_kses(
			$args['before_widget'],
			array(
				'section' => array(
					'id'    => true,
					'class' => true,
				),
				'div'     => array(
					'id'    => true,
					'class' => true,
				),
			)
		);
		if ( ! empty( $instance['title'] ) ) {
			echo wp_kses( $args['before_title'], array( 'h2' => array( 'class' => true ) ) );
			echo esc_html( apply_filters( 'widget_title', $instance['title'] ) );
			echo wp_kses( $args['after_title'], array( 'h2' => array() ) );
		}

		$course_post_type = tutor()->course_post_type;

		$form_args = $instance;
		unset( $form_args['title'] );

		$default_args = array(
			'post_type'   => $course_post_type,
			'post_status' => 'publish',

			'id'          => '',
			'exclude_ids' => '',
			'category'    => '',

			'orderby'     => 'ID',
			'order'       => 'DESC',
			'count'       => '6',
		);

		$a = array_merge( $default_args, $form_args );

		if ( ! empty( $a['id'] ) ) {
			$ids           = (array) explode( ',', $a['id'] );
			$a['post__in'] = $ids;
		}

		if ( ! empty( $a['exclude_ids'] ) ) {
			$exclude_ids       = (array) explode( ',', $a['exclude_ids'] );
			$a['post__not_in'] = $exclude_ids;
		}
		if ( ! empty( $a['category'] ) ) {
			$category = (array) explode( ',', $a['category'] );

			$a['tax_query'] = array(
				array(
					'taxonomy' => CourseModel::COURSE_CATEGORY,
					'field'    => 'term_id',
					'terms'    => $category,
					'operator' => 'IN',
				),
			);
		}
		$a['posts_per_page'] = (int) $a['count'];

		wp_reset_query();
		query_posts( $a );
		ob_start();
		tutor_load_template( 'widget.courses' );
		$output = ob_get_clean();
		wp_reset_query();

		echo wp_kses_post( $output );

		echo wp_kses(
			$args['after_widget'],
			array(
				'section' => array(),
				'div'     => array(),
			)
		);
	}

	/**
	 * Back-end widget form.
	 *
	 * @since 1.3.1
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 * @return void
	 */
	public function form( $instance ) {
		$title       = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'New title', 'tutor' );
		$id          = ! empty( $instance['id'] ) ? $instance['id'] : '';
		$exclude_ids = ! empty( $instance['exclude_ids'] ) ? $instance['exclude_ids'] : '';
		$category    = ! empty( $instance['category'] ) ? $instance['category'] : '';
		$orderby     = ! empty( $instance['orderby'] ) ? $instance['orderby'] : '';
		$order       = ! empty( $instance['order'] ) ? $instance['order'] : '';
		$count       = ! empty( $instance['count'] ) ? $instance['count'] : '6';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Title', 'tutor' ); ?>:
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'id' ) ); ?>">
				<?php esc_html_e( 'ID', 'tutor' ); ?>:
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'id' ) ); ?>" type="text" value="<?php echo esc_attr( $id ); ?>"> <br />
			<span style="color: #AAAAAA">
				<?php esc_html_e( 'Place single course id or comma (,) separated course ids', 'tutor' ); ?>
			</span>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'exclude_ids' ) ); ?>"><?php esc_attr_e( 'Exclude IDS:', 'tutor' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'exclude_ids' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'exclude_ids' ) ); ?>" type="text" value="<?php echo esc_attr( $exclude_ids ); ?>"> <br />
			<span style="color: #AAAAAA">
				<?php esc_html_e( 'Place comma (,) separated courses ids which you like to exclude from the query', 'tutor' ); ?>
			</span>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>">
				<?php esc_html_e( 'Category', 'tutor' ); ?>:
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'category' ) ); ?>" type="text" value="<?php echo esc_attr( $category ); ?>"> <br />
			<span style="color: #AAAAAA">
				<?php esc_html_e( 'Place comma (,) separated category ids', 'tutor' ); ?>
			</span>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>">
				<?php esc_html_e( 'OrderBy', 'tutor' ); ?>
			</label>

			<select class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>" >
				<option value="ID" <?php selected( 'ID', $orderby ); ?> >ID</option>
				<option value="title" <?php selected( 'title', $orderby ); ?> >title</option>
				<option value="rand" <?php selected( 'rand', $orderby ); ?> >rand</option>
				<option value="date" <?php selected( 'date', $orderby ); ?> >date</option>
				<option value="menu_order" <?php selected( 'menu_order', $orderby ); ?> >menu_order</option>
				<option value="post__in" <?php selected( 'post__in', $orderby ); ?> >post__in</option>
			</select> <br />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>">
				<?php esc_html_e( 'Order', 'tutor' ); ?>
			</label>

			<select class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>" >
				<option value="DESC" <?php selected( 'DESC', $order ); ?> >DESC</option>
				<option value="ASC" <?php selected( 'ASC', $order ); ?> >ASC</option>
			</select> <br />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_attr_e( 'Count:', 'tutor' ); ?></label>
			<input  class="widefat tutor-form-number-verify" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="number" value="<?php echo esc_attr( $count ); ?>" min="1"> <br />
			<span style="color: #AAAAAA">
				<?php esc_html_e( 'Total results you like to show', 'tutor' ); ?>
			</span>
		</p>

		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @since 1.3.1
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                = array();
		$instance['title']       = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['id']          = ( ! empty( $new_instance['id'] ) ) ? sanitize_text_field( $new_instance['id'] ) : '';
		$instance['exclude_ids'] = ( ! empty( $new_instance['exclude_ids'] ) ) ? sanitize_text_field( $new_instance['exclude_ids'] ) : '';
		$instance['category']    = ( ! empty( $new_instance['category'] ) ) ? sanitize_text_field( $new_instance['category'] ) : '';
		$instance['orderby']     = ( ! empty( $new_instance['orderby'] ) ) ? sanitize_text_field( $new_instance['orderby'] ) : '';
		$instance['order']       = ( ! empty( $new_instance['order'] ) ) ? sanitize_text_field( $new_instance['order'] ) : '';
		$instance['count']       = ( ! empty( $new_instance['count'] ) ) ? sanitize_text_field( $new_instance['count'] ) : '';

		return $instance;
	}
}
