<?php
defined( 'ABSPATH' ) || exit;

/**
 * Class W_Product_Title_widget
 */
class W_Product_Title_widget extends WP_Widget {

	/**
	 * W_Product_Title_widget constructor.
	 */
	public function __construct() {

		$widget_ops = array(
			'classname'                   => 'w_product_title_widget',
			'description'                 => __( 'Display the product title', 'hd' ),
			'customize_selective_refresh' => true,
		);

		parent::__construct( 'w_product_title_widget', __( 'W - Product Title', 'hd' ), $widget_ops );
	}

	/**
	 * Creating widget front-end
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$default_title = '';
		$title         = ( ! empty( $instance['title'] ) ) ? $instance['title'] : $default_title;

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$widget_title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		if ( $widget_title ) {
			//echo $args['before_widget'];
			echo $args['before_title'] . $widget_title . $args['after_title'];
		}

		?>
		<section class="section title-nav-section title-nav-product" role="contentinfo" tabindex="-1">
			<div class="grid-container">
				<?php the_title('<h1 class="single-title">', '</h1>');?>
				<?php the_breadcrumb_theme(); ?>
			</div>
		</section>
		<?php
		//if ( $widget_title ) echo $args['after_widget'];
	}

	/**
	 * Widget Backend
	 *
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

	/**
	 * Updating widget replacing old instances with new
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array|void
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		return $instance;
	}
}

add_action( 'widgets_init', function () {
	register_widget( 'W_Product_Title_widget' );
} );
