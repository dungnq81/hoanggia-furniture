<?php
defined( 'ABSPATH' ) || exit;

/**
 * Class W_Posts_Carousel_Widget
 */
class W_Posts_Carousel_Widget extends WP_Widget {
	/**
	 * W_Posts_Carousel_Widget constructor.
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'w_posts_carousel_widget',
			'description'                 => __( 'Your site&#8217;s filter posts carousel by category.', 'hd' ),
			'customize_selective_refresh' => true,
		);

		parent::__construct( 'w_posts_carousel_widget', __( 'W - Posts Carousel', 'hd' ), $widget_ops );
		$this->alt_option_name = 'w_posts_carousel_widget';
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

		//$desc           = isset( $instance['desc'] ) ? $instance['desc'] : '';
		$posts_per_page = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 12;
		$pagination     = isset( $instance['pagination'] ) ? $instance['pagination'] : true;
		$navigation     = isset( $instance['navigation'] ) ? $instance['navigation'] : true;
		$full_width     = isset( $instance['full_width'] ) ? $instance['full_width'] : false;
		$autoplay       = isset( $instance['autoplay'] ) ? $instance['autoplay'] : false;
		$freeMode       = isset( $instance['freeMode'] ) ? $instance['freeMode'] : false;
		$loop           = isset( $instance['loop'] ) ? $instance['loop'] : false;
		$delay          = isset( $instance['delay'] ) ? $instance['delay'] : 6000;
		$css_class      = isset( $instance['css_class'] ) ? trim( strip_tags( $instance['css_class'] ) ) : '';

		//$check_desc     = preg_replace( '/\s+/', '', wp_strip_all_tags( $desc ) );
		$_class = $this->id;
		if ($css_class) {
			$_class = $_class . ' ' . $css_class;
		}

		$html_title = get_field( 'html_title', 'widget_' . $args['widget_id'] );

		$chuyen_muc       = get_field( 'chuyen_muc', 'widget_' . $args['widget_id'] );
		$include_children = get_field( 'include_children', 'widget_' . $args['widget_id'] );
		$view_more_url    = get_field( 'view_more', 'widget_' . $args['widget_id'] );
		$button_name      = get_field( 'button_name', 'widget_' . $args['widget_id'] );
		$button_name      = $button_name ? __( $button_name, 'hd' ) : __( 'View more', 'hd' );

		$tax_query = [];
		if ($chuyen_muc) {
			$tax_query[] = [
				'taxonomy' => 'category',
				'terms'    => $chuyen_muc,
				'include_children' => (bool) $include_children,
			];
        }

		// query
		$r = new WP_Query(
			[
				'post_status'         => 'publish',
				'orderby'             => [ 'date' => 'DESC', ],
				'tax_query'           => $tax_query,
				'posts_per_page'      => $posts_per_page,
				'nopaging'            => true,
				'no_found_rows'       => true,
				'ignore_sticky_posts' => true,
			]
		);

		if ( ! $r->have_posts() ) return;

		?>
        <section class="section swipers posts_carousel <?=$_class?>">
			<div class="grid-container">
				<?php if ($title) : ?>
					<h6 class="subtitle"><?php echo $title; ?></h6>
				<?php endif; if ($html_title): ?>
					<h2 class="title"><?php echo $html_title; ?></h2>
				<?php endif;?>
			</div>
	        <?php if ( ! $full_width ) echo '<div class="grid-container">'; ?>
            <div class="swiper-section">
                <?php
                $_data = '';
                $_data .= ' data-autoview = 1';

                if ( $delay ) $_data .= ' data-delay = ' . $delay;
                if ( $loop ) $_data .= ' data-loop = 1';
                if ( $freeMode ) $_data .= ' data-freemode = 1';
                if ( $autoplay ) $_data .= ' data-autoplay = 1';
                if ( $pagination ) $_data .= ' data-pagination = 1';
                if ( $navigation ) $_data .= ' data-navigation = 1';

                ?>
                <div class="w-swiper swiper-container swiper-news-container grid-gutter">
                    <div class="swiper-wrapper"<?php echo $_data?>>
	                    <?php
	                    $i = 0;

	                    // Load slides loop.
	                    while ( $r->have_posts() && $i < $posts_per_page ) : $r->the_post();
		                    echo '<div class="swiper-slide">';
		                    get_template_part( 'template-parts/post/loop-carousel' );
		                    echo '</div>';
		                    ++ $i;
	                    endwhile;
	                    wp_reset_postdata();
	                    ?>
                    </div>
                </div>
            </div>
            <?php if ( $view_more_url) : ?>
            <a href="<?= esc_url( $view_more_url ) ?>" class="view-more" title="<?php echo esc_attr( $button_name ) ?>">
                <?php echo $button_name; ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M10.296 7.71L14.621 12 10.296 16.29 11.704 17.71 17.461 12 11.704 6.29z"></path><path d="M6.704 6.29L5.296 7.71 9.621 12 5.296 16.29 6.704 17.71 12.461 12z"></path></svg>
            </a>
	        <?php endif; ?>
	        <?php if ( ! $full_width ) echo '</div>'; ?>
        </section>
		<?php
	}

	/**
	 * @param array $instance
	 *
	 * @return string|void
	 */
	public function form( $instance ) {
		$instance   = wp_parse_args(
			(array) $instance,
			[
				'title'      => '',
				//'desc'       => '',
				'number'     => 12,
				'full_width' => false,
				'freeMode'   => false,
				'pagination' => true,
				'navigation' => true,
				'autoplay'   => true,
				'loop'       => false,
				'delay'      => 6000,
				'css_class'  => '',
			]
		);

		$title      = $instance['title'];
		//$desc       = strip_tags( $instance['desc'] );
		$number     = filter_var( $instance['number'], FILTER_SANITIZE_NUMBER_INT );
		$pagination = (bool) $instance['pagination'];
		$navigation = (bool) $instance['navigation'];
		$autoplay   = (bool) $instance['autoplay'];
		$full_width = (bool) $instance['full_width'];
		$freeMode   = (bool) $instance['freeMode'];
		$loop       = (bool) $instance['loop'];
		$delay      = filter_var( $instance['delay'], FILTER_SANITIZE_NUMBER_INT );
		$css_class  = strip_tags( $instance['css_class'] );

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo __( 'Title', 'hd' ); ?> </label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php echo __( 'Number of posts to show', 'hd' ); ?></label>
			<input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
		</p>
		<p>
			<input class="checkbox" type="checkbox"<?php checked( $navigation ); ?> id="<?php echo $this->get_field_id( 'navigation' ); ?>" name="<?php echo $this->get_field_name( 'navigation' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'navigation' ); ?>"><?php echo __( 'Navigation', 'hd' ); ?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox"<?php checked( $pagination ); ?> id="<?php echo $this->get_field_id( 'pagination' ); ?>" name="<?php echo $this->get_field_name( 'pagination' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'pagination' ); ?>"><?php echo __( 'Pagination', 'hd' ); ?></label>
		</p>
        <p>
            <input class="checkbox" type="checkbox"<?php checked( $freeMode ); ?> id="<?php echo $this->get_field_id( 'freeMode' ); ?>" name="<?php echo $this->get_field_name( 'freeMode' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'freeMode' ); ?>"><?php echo __( 'freeMode', 'hd' ); ?></label>
        </p>
        <p>
            <input class="checkbox" type="checkbox"<?php checked( $full_width ); ?> id="<?php echo $this->get_field_id( 'full_width' ); ?>" name="<?php echo $this->get_field_name( 'full_width' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'full_width' ); ?>"><?php echo __( 'Full width', 'hd' ); ?></label>
        </p>
		<p>
			<input class="checkbox" type="checkbox"<?php checked( $autoplay ); ?> id="<?php echo $this->get_field_id( 'autoplay' ); ?>" name="<?php echo $this->get_field_name( 'autoplay' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'autoplay' ); ?>"><?php echo __( 'Autoplay', 'hd' ); ?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox"<?php checked( $loop ); ?> id="<?php echo $this->get_field_id( 'loop' ); ?>" name="<?php echo $this->get_field_name( 'loop' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'loop' ); ?>"><?php echo __( 'Loop', 'hd' ); ?></label>
		</p>
        <p>
            <label for="<?php echo $this->get_field_id( 'delay' ); ?>"><?php echo __( 'Delay', 'hd' ); ?></label>
            <input type="number" min="0" class="widefat" id="<?php echo $this->get_field_id( 'delay' ); ?>" name="<?php echo $this->get_field_name( 'delay' ); ?>" value="<?php echo $delay; ?>" />
        </p>
		<p>
			<label for="<?php echo $this->get_field_id( 'css_class' ); ?>"><?php echo __( 'CSS class', 'hd' ); ?> </label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'css_class' ); ?>" name="<?php echo $this->get_field_name( 'css_class' ); ?>" type="text" value="<?php echo $css_class; ?>"/>
			<span style="display: block;margin-top: 5px;"><?php echo __( 'Các class cách nhau bởi khoảng trắng,<br>Class định nghĩa sẵn: "sm-2, md-2, lg-3, style2"', 'hd' ); ?></span>
		</p>
	<?php
	}

	/**
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance               = $old_instance;
		$instance['title']      = sanitize_text_field( $new_instance['title'] );
		//$instance['desc']       = sanitize_textarea_field( $new_instance['desc'] );
		$instance['number']     = isset( $new_instance['number'] ) ? filter_var( $new_instance['number'], FILTER_SANITIZE_NUMBER_INT ) : 12;
		$instance['pagination'] = isset( $new_instance['pagination'] ) ? (bool) $new_instance['pagination'] : false;
		$instance['navigation'] = isset( $new_instance['navigation'] ) ? (bool) $new_instance['navigation'] : false;
		$instance['freeMode']   = isset( $new_instance['freeMode'] ) ? (bool) $new_instance['freeMode'] : false;
		$instance['full_width'] = isset( $new_instance['full_width'] ) ? (bool) $new_instance['full_width'] : false;
		$instance['autoplay']   = isset( $new_instance['autoplay'] ) ? (bool) $new_instance['autoplay'] : false;
		$instance['loop']       = isset( $new_instance['loop'] ) ? (bool) $new_instance['loop'] : false;
		$instance['delay']      = isset( $new_instance['delay'] ) ? filter_var( $new_instance['delay'], FILTER_SANITIZE_NUMBER_INT ) : 6000;
		$instance['css_class']  = isset( $new_instance['css_class'] ) ? sanitize_text_field( $new_instance['css_class'] ) : '';

		return $instance;
	}
}

add_action( 'widgets_init', function () {
	register_widget( 'W_Posts_Carousel_Widget' );
} );
