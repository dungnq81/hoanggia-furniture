<?php
defined( 'ABSPATH' ) || exit;

/**
 * Class W_Newsletter_Widget
 */
class W_CF7_Widget extends WP_Widget {

	/**
	 * W_Newsletter_Widget constructor.
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'w_widget_newsletter',
			'description'                 => __( 'CF7 Form', 'hd' ),
			'customize_selective_refresh' => true,
		);

		parent::__construct( 'w_widget_newsletter', __( 'W - CF7 Form', 'hd' ), $widget_ops );
	}

	/**
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$default_title = '';
		$title         = ( ! empty( $instance['title'] ) ) ? $instance['title'] : $default_title;

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$tieu_de_form = get_field( 'tieu_de_form', 'widget_' . $args['widget_id'] );
		$form_lien_he = get_field( 'form_lien_he', 'widget_' . $args['widget_id'] );

		if ( ! empty( $title ) ) {
			//echo $args['before_widget'];
			echo $args['before_title'] . $title . $args['after_title'];
		}

		if ( strip_whitespace( $tieu_de_form ) ) {
			echo '<p class="desc">' . $tieu_de_form . '</p>';
        }
		if ( $form_lien_he ) {
			echo do_shortcode( '[contact-form-7 id="' . $form_lien_he->ID . '" title="' . esc_attr( $form_lien_he->post_title ) . '"]' );
        }

		//if ( ! empty( $title ) ) echo $args['after_widget'];
	}

	/**
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );

		return $instance;
	}

	/**
	 * @param array $instance
	 *
	 * @return string|void
	 */
	public function form( $instance ) {
		$instance = wp_parse_args(
			(array) $instance,
			[
				'title' => '',
			]
		);
		$title = esc_attr( $instance['title'] );
		?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo __( 'Title', 'hd' ); ?> </label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>"/>
        </p>
		<?php
	}
}

add_action( 'widgets_init', function () {
	register_widget( 'W_CF7_Widget' );
} );
