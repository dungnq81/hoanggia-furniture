<?php
defined( 'ABSPATH' ) || exit;

/**
 * Class W_Stories_Widget
 */
class W_Stories_Widget extends WP_Widget {
	/**
	 * W_Stories_Widget constructor.
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'w_stories_widget',
			'description'                 => __( 'Display the stories section', 'hd' ),
			'customize_selective_refresh' => true,
		);

		parent::__construct( 'w_stories_widget', __( 'W - Stories', 'hd' ), $widget_ops );
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

		$cat_banner     = filter_var( $instance['cat_banner'], FILTER_SANITIZE_NUMBER_INT );
		$full_width     = isset( $instance['full_width'] ) ? $instance['full_width'] : false;
		$autoplay       = isset( $instance['autoplay'] ) ? $instance['autoplay'] : false;
		$loop           = isset( $instance['loop'] ) ? $instance['loop'] : false;
		$delay          = isset( $instance['delay'] ) ? $instance['delay'] : 6000;

		$css_class = get_field( 'css_class', 'widget_' . $args['widget_id']);
		$_class = $this->id;
		if ($css_class)
			$_class = $_class . ' ' . $css_class;

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
		<section class="section stories <?=$_class?>">
		    <?php if ($title) : ?>
            <h3 class="heading-title h4"><?php echo $title; ?></h3>
		    <?php endif; ?>
            <div class="swiper-section">
	            <?php if ( ! $full_width ) echo '<div class="grid-container">'; ?>
                <div class="w-swiper swiper-container">
	                <?php
	                $_data = '';
	                $_data .= ' data-autoview = 1';
	                $_data .= ' data-pagination = "fraction"';
	                $_data .= ' data-navigation = 1';
	                $_data .= ' data-progressbar = 1';

	                if ( $autoplay ) $_data .= ' data-autoplay = 1';
	                if ( $loop ) $_data .= ' data-loop = 1';
	                if ( $delay ) $_data .= ' data-delay = ' . $delay;

	                ?>
                    <div class="swiper-wrapper"<?php echo $_data?>>
                        <?php
                        // Load slides loop.
                        while ( $slides_query->have_posts() ) : $slides_query->the_post();
                            $post = get_post();
                            if ( has_post_thumbnail() ) :
                                $img = get_post_thumbnail( $post, 'medium_large', false );

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

                                $thumbnail = get_field( 'thumbnail', $post->ID );
                                $thumbnail_info = get_field( 'thumbnail_info', $post->ID );
	                            $check_thumbnail_info     = preg_replace( '/\s+/', '', wp_strip_all_tags( $thumbnail_info ) );
                        ?>
                        <div class="swiper-slide">
                            <article class="item">
                                <picture>
		                            <?php if ( $responsive_img ) : ?>
                                    <source media="(max-width: 639.98px)" srcset="<?= $responsive_img ?>">
		                            <?php endif; ?>
                                    <source srcset="<?= $img ?>">
	                                <?php echo get_the_post_thumbnail( $post, 'large');?>
                                    <!--<img src="<?= $img ?>" loading="lazy" alt="<?php the_title_attribute() ?>">-->
                                </picture>
                                <div class="content-wrap">
                                    <div class="content--inner">
	                                    <?php if ( $check_title2 ) : ?>
                                        <div class="st-title st-animated"><?= $title2 ?></div>
	                                    <?php endif;?>
	                                    <?php if ( $check_desc ) : ?>
                                        <div class="st-desc st-animated"><?= $desc ?></div>
	                                    <?php endif; ?>
	                                    <?php if ( $url ) : ?>
                                        <div class="st-button"><a class="button st-animated st-link<?=$is_video_flag?>" href="<?= $url ?>" aria-label="<?php esc_attr_e( $btn_name, 'hd' ); ?>"><?php echo __( $btn_name, 'hd' )?></a></div>
	                                    <?php endif; ?>
                                        <?php if ($thumbnail) : ?>
                                        <div class="st-thumbnail st-animated">
                                            <img loading="lazy" src="<?=$thumbnail?>" alt width="150" height="150">
                                            <?php if ($check_thumbnail_info) : ?>
                                            <div class="thumb-info"><?=$thumbnail_info?></div>
                                            <?php endif;?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </article>
                        </div>
                        <?php
                            endif;
                        endwhile;
                        wp_reset_postdata();
                        ?>
                    </div>
                </div>
	            <?php if ( ! $full_width ) echo '</div>'; ?>
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
				'cat_banner'     => '',
				'full_width' => true,
				'autoplay'   => false,
				'loop'       => true,
				'delay'      => 6000,
			]
		);

		$title = esc_attr( $instance['title'] );
		$cat_banner     = filter_var( $instance['cat_banner'], FILTER_SANITIZE_NUMBER_INT );
		$full_width = (bool) $instance['full_width'];
		$autoplay   = (bool) $instance['autoplay'];
		$loop       = (bool) $instance['loop'];
		$delay      = filter_var( $instance['delay'], FILTER_SANITIZE_NUMBER_INT );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo __( 'Title', 'hd' ); ?> </label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>"/>
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
		$instance['cat_banner'] = filter_var( $new_instance['cat_banner'], FILTER_SANITIZE_NUMBER_INT );
		$instance['full_width'] = isset( $new_instance['full_width'] ) ? (bool) $new_instance['full_width'] : false;
		$instance['autoplay']   = isset( $new_instance['autoplay'] ) ? (bool) $new_instance['autoplay'] : false;
		$instance['loop']       = isset( $new_instance['loop'] ) ? (bool) $new_instance['loop'] : false;
		$instance['delay']      = isset( $new_instance['delay'] ) ? filter_var( $new_instance['delay'], FILTER_SANITIZE_NUMBER_INT ) : 6000;

		return $instance;
	}
}

add_action( 'widgets_init', function () {
	register_widget( 'W_Stories_Widget' );
} );
