<?php
defined( 'ABSPATH' ) || exit;

/**
 * Class W_IconBox_widget
 */
class W_IconBox_widget extends WP_Widget {

	/**
	 * W_IconBox_widget constructor.
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'w_iconbox_widget',
			'description'                 => __( 'Display the repeater iconbox', 'hd' ),
			'customize_selective_refresh' => true,
		);

		parent::__construct( 'w_iconbox_widget', __( 'W - IconBox', 'hd' ), $widget_ops );
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
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		if ( ! empty( $title ) ) {
			//echo $args['before_widget'];
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$mo_ta = get_field( 'mo_ta', 'widget_' . $args['widget_id'] );
		$css_class = get_field( 'css_class', 'widget_' . $args['widget_id'] );
		$_desc = preg_replace( '/\s+/', '', wp_strip_all_tags( $mo_ta ) );
		if ( $_desc )
		    echo '<p class="desc">' . $mo_ta . '</p>';

		$_class = $this->id;
		if ($css_class)
			$_class = $_class . ' ' . $css_class;

		// repeater field
		$iconbox = get_field( 'iconbox', 'widget_' . $args['widget_id'] );
		if ( $iconbox ) {
			echo '<ul class="iconbox ' . $_class . '">';
			foreach ( $iconbox as $item ) {
			    echo ( ! $item['class'] ) ? '<li>' : '<li class="' . $item['class'] . '">';
				if ( $item['icon'] ) echo $item['icon'];
				if ( $item['img'] ) {
					echo '<span class="cover"><span class="res res-1v1"></span>';
					echo wp_get_attachment_image( $item['img'] );
					echo '</span>';
				}

				$_tieude = preg_replace( '/\s+/', '', wp_strip_all_tags( $item['tieu_de'] ) );
				$_desc = preg_replace( '/\s+/', '', wp_strip_all_tags( $item['noi_dung'] ) );
				if ( $_tieude ) {
					echo '<div class="iconbox-title">';
					echo '<div class="title">' . $item['tieu_de'] . '</div>';
					if ( $_desc ) echo '<div class="desc">' . $item['noi_dung'] . '</div>';

					if ( $item['link'] ) {
						?>
                        <a href="<?= esc_url( $item['link'] ) ?>" class="view-more" title>
                            <span class="icon"></span>
                            <span class="txt"><?php echo __('Xem thêm', 'hd')?></span>
                        </a>
						<?php
					}
					echo '</div>';
				}
				echo '</li>';
			}

			echo '</ul>';
        }

		// after_widget
		//if ( ! empty( $title ) ) echo $args['after_widget'];
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
	register_widget( 'W_IconBox_widget' );
} );
