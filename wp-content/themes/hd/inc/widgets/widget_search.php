<?php
defined( 'ABSPATH' ) || exit;

/**
 * Class W_Search_widget
 */
class W_Search_widget extends WP_Widget {

	/**
	 * W_Search_widget constructor.
	 */
	public function __construct() {

		$widget_ops = array(
			'classname'                   => 'w_search_widget',
			'description'                 => __( 'Display the search box', 'hd' ),
			'customize_selective_refresh' => true,
		);

		parent::__construct( 'w_search_widget', __( 'W - Search', 'hd' ), $widget_ops );
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
		$search_kind = strip_tags( $instance['search_kind'] );
		$css_class      = isset( $instance['css_class'] ) ? trim( strip_tags( $instance['css_class'] ) ) : '';

		if ( $widget_title ) {
			//echo $args['before_widget'];
			echo $args['before_title'] . $widget_title . $args['after_title'];
		}

		$_unique_id = esc_attr( uniqid( 'search-form-' ) );
		$title = __( 'Search', 'hd' );
		$title_for = __( 'Search for', 'hd' );
		$placeholder_title = esc_attr( __( 'Search ...', 'hd' ) );

		?>
        <form role="search" action="<?php echo get_base_url(); ?>" class="frm-search <?php echo $css_class; ?>" method="get" accept-charset="UTF-8" data-abide novalidate>
            <label for="<?php echo $_unique_id; ?>" class="screen-reader-text"><?php echo $title_for; ?></label>
            <input id="<?php echo $_unique_id; ?>" required pattern="^(.*\S+.*)$" type="search" autocomplete="off" name="s" value="<?php echo get_search_query(); ?>" placeholder="<?php echo $placeholder_title; ?>">
            <button type="submit">
				<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px"><path d="M0 0h24v24H0z" fill="none"/><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
				<span><?php echo $title; ?></span>
			</button>
	        <?php if ( 'video' == $search_kind ) : ?>
			<input type="hidden" name="post_type" value="video">
	        <?php elseif ( 'product' == $search_kind ) : ?>
			<input type="hidden" name="post_type" value="product">
			<?php endif;?>
        </form>
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
				'search_kind' => 'blog',
				'css_class'  => '',
			]
		);
		$title = esc_attr( $instance['title'] );
		$search_kind = strip_tags( $instance['search_kind'] );
		$css_class      = strip_tags( $instance['css_class'] );

		?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo __( 'Title', 'hd' ); ?> </label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'search_kind' ); ?>"><?php echo __( 'Type the search', 'hd' ); ?> </label>
            <select class="postform widefat" name="<?php echo $this->get_field_name( 'search_kind' ) ?>" id="<?php echo $this->get_field_id( 'search_kind' ) ?>">
                <option value="blog"<?php if ( 'blog' == $search_kind ) echo ' selected="selected"' ?>><?php echo __( 'Blog', 'hd' )?></option>
                <option value="video"<?php if ( 'video' == $search_kind ) echo ' selected="selected"' ?>><?php echo __( 'Video', 'hd' );?></option>
                <option value="product"<?php if ( 'product' == $search_kind ) echo ' selected="selected"' ?>><?php echo __( 'Product', 'hd' );?></option>
            </select>
            <span style="display: block;margin-top: 5px"><?php echo __( 'Select a search type, news, video or product', 'hd' ); ?></span>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'css_class' ); ?>"><?php echo __( 'CSS class', 'hd' ); ?> </label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'css_class' ); ?>" name="<?php echo $this->get_field_name( 'css_class' ); ?>" type="text" value="<?php echo $css_class; ?>"/>
            <span style="display: block;margin-top: 5px;"><?php echo __( 'Each class is separated by a space', 'hd' ); ?></span>
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
		$instance['search_kind'] = $new_instance['search_kind'];
		$instance['css_class']  = isset( $new_instance['css_class'] ) ? sanitize_text_field( $new_instance['css_class'] ) : '';
		return $instance;
	}
}

add_action( 'widgets_init', function () {
	register_widget( 'W_Search_widget' );
} );
