<?php
defined( 'ABSPATH' ) || exit;

/**
 * Class W_Message_Cover_widget
 */
class W_Message_Cover_widget extends WP_Widget {

	/**
	 * W_IconBox_widget constructor.
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'w_message_cover_widget',
			'description'                 => __( 'Display the message cover section', 'hd' ),
			'customize_selective_refresh' => true,
		);

		parent::__construct( 'w_message_cover_widget', __( 'W - Message Cover', 'hd' ), $widget_ops );
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

		$bg = get_field( 'bg', 'widget_' . $args['widget_id']);
		$content = get_field( 'content', 'widget_' . $args['widget_id'], false);
		$url = get_field( 'url', 'widget_' . $args['widget_id'] );
		$button_text = get_field( 'button_text', 'widget_' . $args['widget_id'] );
		$css_class = get_field( 'css_class', 'widget_' . $args['widget_id'] );

		$_check_content = preg_replace( '/\s+/', '', wp_strip_all_tags( $content ) );

		$_class = $this->id;
		if ($css_class)
		    $_class = $_class . ' ' . $css_class;
		?>
		<section class="section message-cover cover <?=$_class?>">
			<?php if ($bg) : ?>
			<div class="message-cover__bg" style="background-image: url(<?=$bg?>)"></div>
			<?php endif; ?>
			<div class="grid-container">
				<?php
				if ( ! empty( $title ) ) {
					echo $args['before_title'] . $title . $args['after_title'];
				}
				if ($_check_content) :

				?>
				<div class="message__content">
					<?=$content?>
				</div>
				<?php endif; if ($url) : ?>
				<div class="message__action">
					<a aria-label="<?=$button_text?>" class="button _blank" href="<?=$url?>"><?=$button_text?></a>
				</div>
				<?php endif; ?>
			</div>
		</section>
	<?php
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
	register_widget( 'W_Message_Cover_widget' );
} );
