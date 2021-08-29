<?php
defined( 'ABSPATH' ) || exit;

/**
 * Class W_Dropdown_Search_widget
 */
class W_Dropdown_Search_widget extends WP_Widget {

	/**
	 * HD_Dropdown_Search_widget constructor.
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'w_dropdown_search_widget',
			'description'                 => __( 'Dropdown Search', 'hd' ),
			'customize_selective_refresh' => true,
		);

		parent::__construct( 'w_dropdown_search_widget', __( 'W - Dropdown Search', 'hd' ), $widget_ops );
	}

	/**
	 * Creating widget front-end
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$widget_title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$search_kind = strip_tags( $instance['search_kind'] );
		$css_class      = isset( $instance['css_class'] ) ? trim( strip_tags( $instance['css_class'] ) ) : '';

		if ( ! empty( $widget_title ) ) {
			//echo $args['before_widget'];
			echo '<h4 class="heading-title">' . $widget_title . '</h4>';
		}

		$_unique_id = esc_attr( uniqid( 'search-form-' ) );
		$title = __( 'Search', 'hd' );
		$attr_title = esc_attr( $title );
		$placeholder_title = esc_attr( __( 'Search ...', 'hd' ) );
		$close_title = __( 'Close', 'hd' );

		?>
        <div class="search-dropdown--wrap <?php echo $css_class; ?>">
            <a class="trigger-s" title="<?php echo $attr_title; ?>" href="javascript:;" data-toggle="dropdown-<?=$_unique_id?>">
				<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px"><path d="M0 0h24v24H0z" fill="none"/><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                <span><?php echo $title; ?></span>
            </a>
            <div role="search" class="dropdown-pane" id="dropdown-<?=$_unique_id?>" data-dropdown data-auto-focus="true">
                <form role="form" action="<?php echo get_base_url(); ?>" class="frm-search" method="get" accept-charset="UTF-8" data-abide novalidate>
                    <div class="grid-container">
                        <div class="frm-container">
                            <input id="<?php echo $_unique_id; ?>" required pattern="^(.*\S+.*)$" type="search" name="s" value="<?php echo get_search_query(); ?>" placeholder="<?php echo $placeholder_title; ?>" title>
                            <button class="btn-s" type="submit">
								<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px"><path d="M0 0h24v24H0z" fill="none"/><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
								<span><?php echo $title; ?></span>
							</button>
                            <button class="trigger-s-close" type="button">
								<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px"><path d="M0 0h24v24H0z" fill="none"/><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
								<span><?php echo $close_title; ?></span>
							</button>
                        </div>
                    </div>
                    <?php if ( 'video' == $search_kind ) : ?>
                    <input type="hidden" name="post_type" value="video">
					<?php elseif ( 'product' == $search_kind ) : ?>
					<input type="hidden" name="post_type" value="product">
					<?php endif;?>
                </form>
            </div>
        </div>
		<?php
		//if ( ! empty( $widget_title ) ) echo $args['after_widget'];
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
				'css_class' => '',
			]
		);
		$title = esc_attr( $instance['title'] );
		$search_kind = strip_tags( $instance['search_kind'] );
		$css_class = strip_tags( $instance['css_class'] );

		?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo __( 'Title', 'hd' ); ?> </label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'search_kind' ); ?>"><?php echo __( 'Type the search', 'hd' ); ?> </label>
            <select class="postform widefat" name="<?php echo $this->get_field_name( 'search_kind' ) ?>" id="<?php echo $this->get_field_id( 'search_kind' ) ?>" title>
                <option value="blog"<?php if ( 'blog' == $search_kind ) echo ' selected="selected"' ?>><?php echo __( 'Blog', 'hd' )?></option>
                <option value="video"<?php if ( 'video' == $search_kind ) echo ' selected="selected"' ?>><?php echo __( 'Video', 'hd' );?></option>
                <option value="product"<?php if ( 'product' == $search_kind ) echo ' selected="selected"' ?>><?php echo __( 'Product', 'hd' );?></option>
            </select>
            <span style="display: block;margin-top: 5px"><?php echo __( 'Select a search type, news, video or product', 'hd' ); ?></span>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'css_class' ); ?>"><?php echo __( 'CSS class', 'hd' ); ?> </label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'css_class' ); ?>" name="<?php echo $this->get_field_name( 'css_class' ); ?>" type="text" value="<?php echo $css_class; ?>"/>
            <span style="display: block;margin-top: 5px;"><?php echo __( 'Mỗi class cách nhau bởi khoảng trắng', 'hd' ); ?></span>
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
		$instance['title']     = sanitize_text_field( $new_instance['title'] );
		$instance['search_kind'] = $new_instance['search_kind'];
		$instance['css_class']      = isset( $new_instance['css_class'] ) ? sanitize_text_field( $new_instance['css_class'] ) : '';

		return $instance;
	}
}

add_action( 'widgets_init', function () {
	register_widget( 'W_Dropdown_Search_widget' );
} );
