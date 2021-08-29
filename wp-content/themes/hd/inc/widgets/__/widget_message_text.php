<?php
defined( 'ABSPATH' ) || exit;

class W_Message_Text_widget extends WP_Widget {

	/**
	 * W_Message_Text_widget constructor.
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'w_message_text_widget',
			'description'                 => __( 'Display the message text section', 'hd' ),
			'customize_selective_refresh' => true,
		);

		parent::__construct( 'w_message_text_widget', __( 'W - Message Text', 'hd' ), $widget_ops );
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

		$subtitle        = get_field( 'subtitle', 'widget_' . $args['widget_id'] );
		$title_extra     = get_field( 'title_extra', 'widget_' . $args['widget_id'] );
		$desc = get_field( 'desc', 'widget_' . $args['widget_id'], false );
		$url = get_field( 'url', 'widget_' . $args['widget_id']);
		$button_text = get_field( 'button_name', 'widget_' . $args['widget_id'] );
		$css_class = get_field( 'css_class', 'widget_' . $args['widget_id'] );

		$_check_desc = preg_replace( '/\s+/', '', wp_strip_all_tags( $desc ) );
		$_class = $this->id;
		if ($css_class)
			$_class = $_class . ' ' . $css_class;

		?>
		<section class="section message-text <?=$_class?>">
			<div class="grid-container">
				<div class="grid-x grid-padding-x">
					<div class="cell cell-left">
						<?php if ($subtitle) : ?>
						<h6 class="sub-title"><?=$subtitle?></h6>
						<?php endif; if ($title) : ?>
						<h4 class="heading-title"><?php echo $title; ?></h4>
						<?php endif; if ($title_extra) : ?>
						<h3 class="heading-style"><?=$title_extra?></h3>
						<?php endif; ?>
					</div>
					<div class="cell cell-right">
						<?php if ($url) : ?>
						<div class="message__action">
							<a aria-label="<?=$button_text?>" class="button" href="<?=$url?>"><?=$button_text?></a>
						</div>
						<?php endif; ?>
					</div>
					<?php if ($_check_desc) : ?>
					<div class="cell text-desc"><?=$desc?></div>
					<?php endif; ?>
				</div>
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
	register_widget( 'W_Message_Text_widget' );
} );