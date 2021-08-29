<?php
defined( 'ABSPATH' ) || exit;

/**
 * Class W_Home_Slides_Widget
 */
class W_Home_Slides_Widget extends WP_Widget {
	public function __construct() {

		$widget_ops = array(
			'classname'                   => 'w_home_slides_widget',
			'description'                 => __( 'Collection of home banners', 'hd' ),
			'customize_selective_refresh' => true,
		);

		parent::__construct( 'w_home_slides_widget', __( 'W - Home Slides', 'hd' ), $widget_ops );
		$this->alt_option_name = 'w_home_slides_widget';
	}

	/**
	 * Creating widget front-end
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		//$title          = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$cat_banner     = filter_var( $instance['cat_banner'], FILTER_SANITIZE_NUMBER_INT );
		$parallax     = isset( $instance['parallax'] ) ? $instance['parallax'] : true;
		$pagination     = isset( $instance['pagination'] ) ? $instance['pagination'] : true;
		$navigation     = isset( $instance['navigation'] ) ? $instance['navigation'] : true;
		$autoplay       = isset( $instance['autoplay'] ) ? $instance['autoplay'] : false;
		$fade           = isset( $instance['fade'] ) ? $instance['fade'] : false;
		$loop           = isset( $instance['loop'] ) ? $instance['loop'] : false;
		$delay          = isset( $instance['delay'] ) ? $instance['delay'] : 8000;
		$css_class      = isset( $instance['css_class'] ) ? trim( strip_tags( $instance['css_class'] ) ) : '';

		$_class = $this->id;
		if ($css_class) {
		    $_class = $_class . ' ' . $css_class;
		}
		if ($parallax) {
			$_class = 'w-parallax ' . $_class;
        }

		if ( ! $cat_banner ) {
			return;
		}

		$term = get_term_by( 'id', $cat_banner, 'banner_category' );
		if ( ! $term ) {
			return;
		}

		$slides_query = get_banner_query( $term );
		if ( ! $slides_query ) {
			return;
		}

		?>
        <section class="section home-slider <?=$_class?>" data-depth="1.41">
            <div class="swiper-section">
                <div class="w-swiper swiper-container">
                    <?php
                        $_data = '';
                        $_data .= ' data-autoview = 1';

                        if ( $delay ) $_data .= ' data-delay = ' . $delay;
                        if ( $parallax ) $_data .= ' data-parallax = 1';
                        if ( $pagination ) $_data .= ' data-pagination = "fraction"';
                        if ( $navigation ) $_data .= ' data-navigation = 1';
                        if ( $autoplay ) $_data .= ' data-autoplay = 1';
                        if ( $fade ) $_data .= ' data-fade = 1';
                        if ( $loop ) $_data .= ' data-loop = 1';
                    ?>
                    <div class="swiper-wrapper"<?php echo $_data?>>
                        <?php
					    // Load slides loop.
					    while ( $slides_query->have_posts() ) : $slides_query->the_post();
						    $post = get_post();
						    if ( has_post_thumbnail() ) :
							    $img = get_post_thumbnail( $post, 'original', false );

							    // custom fields
							    $url            = get_field( 'url', $post->ID );
							    $responsive_img = get_field( 'responsive_image', $post->ID );
							    $title2         = get_field( 'title2', $post->ID );
							    $desc           = get_field( 'desc', $post->ID, false );

							    $check_title2   = preg_replace( '/\s+/', '', wp_strip_all_tags( $title2 ) );
							    $check_desc     = preg_replace( '/\s+/', '', wp_strip_all_tags( $desc ) );

							    $btn_name = get_field( 'button_name', $post->ID );
							    if ( empty( $btn_name ) )
								    $btn_name = 'Read more';

							    $video_link = get_field( 'video_link', $post->ID );
							    $is_video_flag = ' _blank';
							    if ( $video_link ) $is_video_flag = ' popup-video';

							    $_bg_data = '';
							    if ($parallax)
								    $_bg_data = ' data-overlay-dark="0" data-swiper-parallax="75%" data-parallax-scale="1.1"';
					    ?>
                        <div class="swiper-slide">
                            <article class="item">
                                <?php if ($responsive_img) : ?>
                                <span<?=$_bg_data?> class="--bg w--bg" style="background-image: url(<?=$img?>)"></span>
                                <span<?=$_bg_data?> class="--bg small--bg" style="background-image: url(<?=$responsive_img?>)"></span>
                                <?php else : ?>
                                <span<?=$_bg_data?> class="--bg" style="background-image: url(<?=$img?>)"></span>
                                <?php endif;?>
                                <div class="caption parallax-layer">
                                    <?php if ($check_title2) : ?>
                                    <h3 class="slide-heading h2"><?=$title2?></h3>
                                    <?php endif; if ($check_desc) : ?>
                                    <div class="slide-text"><?=$desc?></div>
                                    <?php endif; ?>
                                    <div class="slide-button">
							            <?php if ( $url ) : ?>
                                        <a href="<?=$url?>" class="button button1 <?=$is_video_flag?>" aria-label="<?php esc_attr_e( $btn_name, 'hd' ); ?>"><?php echo __( $btn_name, 'hd' )?></a>
                                        <?php endif;?>
                                        <a href="#" class="button button2" aria-label="<?php echo __('Liên hệ', 'hd')?>"><?php echo __('Liên hệ', 'hd')?></a>
                                    </div>
                                </div>
                            </article>
                        </div>
                        <?php endif;
					    endwhile;
                        wp_reset_postdata();
                        ?>
                    </div>
                </div>
            </div>
            <div class="wheel show parallax-layer"><span></span><span></span><span></span></div>
        </section>
		<?php
		//if ( $title ) echo $args['after_widget'];
	}

	/**
	 * Widget Backend
	 *
	 * @param array $instance
	 *
	 * @return string|void
	 */
	public function form( $instance ) {
		$instance   = wp_parse_args(
			(array) $instance,
			[
				'title'          => '',
				'cat_banner'     => '',
				'parallax'     => false,
				'pagination'     => true,
				'navigation'     => false,
				'autoplay'       => true,
				'fade'           => false,
				'loop'           => true,
				'delay'          => 8000,
				'css_class'      => '',
			]
		);

		$title          = $instance['title'];
		$cat_banner     = filter_var( $instance['cat_banner'], FILTER_SANITIZE_NUMBER_INT );
		$parallax     = (bool) $instance['parallax'];
		$pagination     = (bool) $instance['pagination'];
		$navigation     = (bool) $instance['navigation'];
		$autoplay       = (bool) $instance['autoplay'];
		$fade           = (bool) $instance['fade'];
		$loop           = (bool) $instance['loop'];
		$delay          = filter_var( $instance['delay'], FILTER_SANITIZE_NUMBER_INT );
		$css_class      = strip_tags( $instance['css_class'] );

		?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo __( 'Title', 'hd' ); ?> </label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'cat_banner' ); ?>"><?php echo __( 'Category', 'hd' ) . ' *'; ?> </label>
			<?php
			$tax_args = [
                'show_option_none' => __( '--Select category--', 'hd' ),
                'option_none_value' => '',
				'taxonomy'     => 'banner_category',
				'show_count'   => 1,
				'hierarchical' => 1,
				'name'         => $this->get_field_name( 'cat_banner' ),
				'id'           => $this->get_field_id( 'cat_banner' ),
				'class'        => 'postform widefat',
				'selected'     => $cat_banner,
				'required'     => true,
			];

			wp_dropdown_categories( $tax_args );

			?>
            <span style="display: block;margin-top: 5px"><?php echo __( 'The photo category is the place where the images are displayed', 'hd' ); ?></span>
        </p>
        <p>
            <input class="checkbox" type="checkbox"<?php checked( $parallax ); ?> id="<?php echo $this->get_field_id( 'parallax' ); ?>" name="<?php echo $this->get_field_name( 'parallax' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'parallax' ); ?>"><?php echo __( 'Parallax', 'hd' ); ?></label>
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
            <input class="checkbox" type="checkbox"<?php checked( $autoplay ); ?> id="<?php echo $this->get_field_id( 'autoplay' ); ?>" name="<?php echo $this->get_field_name( 'autoplay' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'autoplay' ); ?>"><?php echo __( 'Autoplay', 'hd' ); ?></label>
        </p>
        <p>
            <input class="checkbox" type="checkbox"<?php checked( $fade ); ?> id="<?php echo $this->get_field_id( 'fade' ); ?>" name="<?php echo $this->get_field_name( 'fade' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'fade' ); ?>"><?php echo __( 'Fade Effect', 'hd' ); ?></label>
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
		$instance                   = $old_instance;
		$instance['title']          = sanitize_text_field( $new_instance['title'] );
		$instance['cat_banner']     = filter_var( $new_instance['cat_banner'], FILTER_SANITIZE_NUMBER_INT );
		$instance['parallax'] = isset( $new_instance['parallax'] ) ? (bool) $new_instance['parallax'] : false;
		$instance['pagination'] = isset( $new_instance['pagination'] ) ? (bool) $new_instance['pagination'] : false;
		$instance['navigation'] = isset( $new_instance['navigation'] ) ? (bool) $new_instance['navigation'] : false;
		$instance['autoplay']   = isset( $new_instance['autoplay'] ) ? (bool) $new_instance['autoplay'] : false;
		$instance['fade']       = isset( $new_instance['fade'] ) ? (bool) $new_instance['fade'] : false;
		$instance['loop']       = isset( $new_instance['loop'] ) ? (bool) $new_instance['loop'] : false;
		$instance['delay']       = isset( $new_instance['delay'] ) ? filter_var( $new_instance['delay'], FILTER_SANITIZE_NUMBER_INT ) : 8000;
		$instance['css_class']      = isset( $new_instance['css_class'] ) ? sanitize_text_field( $new_instance['css_class'] ) : '';

		return $instance;
	}
}

add_action( 'widgets_init', function () {
	register_widget( 'W_Home_Slides_Widget' );
} );
