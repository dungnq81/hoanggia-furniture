<?php
defined( 'ABSPATH' ) || exit;

/**
 * Class W_Services_Widget
 */
class W_Services_Widget extends WP_Widget {

	/**
	 * W_Services_Widget constructor.
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'w_services_widget',
			'description'                 => __( 'Display the services section', 'hd' ),
			'customize_selective_refresh' => true,
		);

		parent::__construct( 'w_services_widget', __( 'W - Services', 'hd' ), $widget_ops );
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
		$thumbnail_image = get_field( 'background_image', 'widget_' . $args['widget_id'] );
		$css_class       = get_field( 'css_class', 'widget_' . $args['widget_id'] );

		// repeater field
		$iconbox = get_field( 'icon_box', 'widget_' . $args['widget_id'] );

		$_class = $this->id;
		if ($css_class)
			$_class = $_class . ' ' . $css_class;

		?>
		<section class="section services <?=$_class?>">
            <div class="grid-container">
                <?php if ($subtitle) : ?>
                <h6 class="sub-title"><?=$subtitle?></h6>
                <?php endif; if ($title) : ?>
                <h4 class="heading-title"><?php echo $title; ?></h4>
                <?php endif; if ($title_extra) : ?>
                <h3 class="heading-style"><?=$title_extra?></h3>
                <?php endif; if ( $iconbox ) : ?>
                <ul class="iconbox">
                    <?php foreach ( $iconbox as $item ) : ?>
                    <li>
                        <?php
                        $_link = preg_replace( '/\s+/', '', wp_strip_all_tags( $item['link'] ) );
                        $_tieude = preg_replace( '/\s+/', '', wp_strip_all_tags( $item['tieu_de'] ) );
                        $_desc = preg_replace( '/\s+/', '', wp_strip_all_tags( $item['noi_dung'] ) );

                        if ( $item['icon'] ) :
                            if ($_link) echo "<a aria-label='" . esc_attr( $item['tieu_de'] ) . "' href='" . $_link . "'>";
                            echo $item['icon'];
                            echo '</a>';
                        endif;
	                    if ( $item['img'] ) :
                            if ($_link) echo '<a aria-label="' . esc_attr( $item['tieu_de'] ) . '" class="icon-img" href="' . $_link . '">' . wp_get_attachment_image( $item['img'] ) . '</a>';
                            else echo '<span class="icon-img">' . wp_get_attachment_image( $item['img'] ) . '</span>';
	                    endif;

	                    // check tieu đề
                        if ( $_tieude ) {

                            $title = $item['tieu_de'];
                            if ($_link)
	                            $title = "<a title='" . esc_attr( $item['tieu_de'] ) . "' href='" . $_link . "'>" . $title . "</a>";

	                        echo '<div class="iconbox-title">';
	                        echo '<div class="title">' . $title . '</div>';
	                        if ( $_desc ) echo '<div class="desc">' . $item['noi_dung'] . '</div>';
	                        echo '</div>';
                        }
                        ?>
                    </li>
                    <?php endforeach;?>
                </ul>
                <?php endif; if ($thumbnail_image) : ?>
                <div class="thumbnail-img"><?php echo wp_get_attachment_image( $thumbnail_image, 'post-thumbnail' ); ?></div>
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
	register_widget( 'W_Services_Widget' );
} );
