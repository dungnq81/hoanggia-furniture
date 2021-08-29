<?php
defined( 'ABSPATH' ) || exit;

class W_Banner_Carousel_Widget extends WP_Widget {
	/**
	 * W_Banner_Carousel_Widget constructor.
	 */
	public function __construct() {

		$widget_ops = array(
			'classname'                   => 'w_banner_carousel',
			'description'                 => __( 'Collection of Banners Carousel', 'hd' ),
			'customize_selective_refresh' => true,
		);

		parent::__construct( 'w_banner_carousel', __( 'W - Banners Carousel', 'hd' ), $widget_ops );
		$this->alt_option_name = 'w_banner_carousel';
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
		$pagination     = ! isset( $instance['pagination'] ) || $instance['pagination'];
		$navigation     = ! isset( $instance['navigation'] ) || $instance['navigation'];
		$autoplay       = $instance['autoplay'] ?? false;
		$loop           = $instance['loop'] ?? false;
		$gap           = $instance['gap'] ?? false;
		$delay          = $instance['delay'] ?? 6000;

		$html_title = get_field( 'html_title', 'widget_' . $args['widget_id'] );
		$css_class = get_field( 'css_class', 'widget_' . $args['widget_id'] );
		$_class = $this->id;
		if ($css_class) {
			$_class = $_class . ' ' . $css_class;
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
        <section id="<?=$this->id?>" class="section swipers <?=$_class?>">
	        <div class="grid-container">
		        <?php if ($title) : ?>
                <h6 class="subtitle"><?php echo $title; ?></h6>
                <?php endif; if ($html_title): ?>
				<h2 class="title"><?php echo $html_title; ?></h2>
				<?php endif;?>
            </div>
            <div class="swiper-section">
                <div class="w-swiper swiper-container slide-trigger">
	                <?php
	                $_data = '';
	                $_data .= ' data-autoview=1';

	                if ( $delay ) $_data .= ' data-delay=' . $delay;
	                if ( $loop ) $_data .= ' data-loop=1';
					if ( $gap ) $_data .= ' data-gap=1';
	                if ( $autoplay ) $_data .= ' data-autoplay=1';
	                if ( $pagination ) $_data .= ' data-pagination="progressbar"';
	                if ( $navigation ) $_data .= ' data-navigation=1';

	                ?>
                    <div class="swiper-wrapper"<?php echo $_data?>>
                        <?php
					    // Load slides loop.
					    while ( $slides_query->have_posts() ) : $slides_query->the_post();
						    $post = get_post();
						    if ( has_post_thumbnail() ) :

							    // custom fields
								$url            = get_field( 'url', $post->ID );
								$responsive_img = get_field( 'responsive_image', $post->ID );
								$title2         = get_field( 'title2', $post->ID ); // tiêu đề có định dạng html
								$subtitle       = get_field( 'subtitle', $post->ID ); // tiêu đề phụ
								$desc           = get_field( 'desc', $post->ID, false ); // có định dạng html

								$check_title2   = preg_replace( '/\s+/', '', wp_strip_all_tags( $title2 ) );
								$check_subtitle = preg_replace( '/\s+/', '', wp_strip_all_tags( $subtitle ) );
								$check_desc     = preg_replace( '/\s+/', '', wp_strip_all_tags( $desc ) );

							    $btn_name = get_field( 'button_name', $post->ID );
							    if ( empty( $btn_name ) )
								    $btn_name = 'Read more';

							    $video_link = get_field( 'video_link', $post->ID );
							    $is_video_flag = ' _blank';
							    if ( $video_link ) $is_video_flag = ' popup-video';
					    ?>
                        <div class="swiper-slide">
                            <article class="item">
								<figure>
									<picture>
										<?php if ( $responsive_img ) : ?>
										<source media="(max-width: 639.98px)" srcset="<?= $responsive_img; ?>">
										<?php else : ?>
										<source srcset="<?= get_post_thumbnail( $post, 'medium_large', false ); ?>">
										<?php endif;?>
										<?php echo get_the_post_thumbnail( $post, 'medium_large');?>
									</picture>
									<figcaption>
										<?php if ( $check_title2 ) : ?>
										<h3 class="title"><?= $title2 ?></h3>
										<?php endif; ?>
										<?php if ( $check_desc ) : ?>
										<p class="desc"><?= $desc ?></p>
										<?php endif; ?>
										<div class="action">
											<?php if ( $url ) : ?>
											<a class="btn-effect link<?=$is_video_flag?>" href="<?= $url ?>" aria-label="<?php esc_attr_e( $btn_name, 'hd' ); ?>">
											<?php endif;?>
												<span class="line"></span>
												<?php if ( $check_subtitle ) : ?>
													<span class="subtitle"><?= $subtitle ?></span>
												<?php endif;?>
											<?php if ( $url ) : ?>
												<span class="btn-name"><?php echo __( $btn_name, 'hd' )?></span>
											</a>
											<?php endif;?>
										</div>
									</figcaption>
								</figure>
                            </article>
                        </div>
                        <?php
						    endif;
					    endwhile;
                        wp_reset_postdata();
                        ?>
                    </div>
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
		$instance   = wp_parse_args(
			(array) $instance,
			[
				'title'      => '',
				'cat_banner'     => '',
				'pagination' => true,
				'navigation' => true,
				'autoplay'   => true,
				'loop'       => false,
				'gap'       => false,
				'delay'      => 6000,
			]
		);

		$title      = $instance['title'];
		$cat_banner     = filter_var( $instance['cat_banner'], FILTER_SANITIZE_NUMBER_INT );
		$pagination = (bool) $instance['pagination'];
		$navigation = (bool) $instance['navigation'];
		$autoplay   = (bool) $instance['autoplay'];
		$loop       = (bool) $instance['loop'];
		$gap       = (bool) $instance['gap'];
		$delay      = filter_var( $instance['delay'], FILTER_SANITIZE_NUMBER_INT );

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
				'class'        => 'postform widefat select2',
				'selected'     => $cat_banner,
				'required'     => true,
			];

			wp_dropdown_categories( $tax_args );

			?>
			<span style="display: block;margin-top: 5px"><?php echo __( 'The photo category is the place where the images are displayed', 'hd' ); ?></span>
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
			<input class="checkbox" type="checkbox"<?php checked( $loop ); ?> id="<?php echo $this->get_field_id( 'loop' ); ?>" name="<?php echo $this->get_field_name( 'loop' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'loop' ); ?>"><?php echo __( 'Loop', 'hd' ); ?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox"<?php checked( $gap ); ?> id="<?php echo $this->get_field_id( 'gap' ); ?>" name="<?php echo $this->get_field_name( 'gap' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'gap' ); ?>"><?php echo __( 'Gap', 'hd' ); ?></label>
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
		$instance               = $old_instance;
		$instance['title']      = sanitize_text_field( $new_instance['title'] );
		$instance['cat_banner'] = filter_var( $new_instance['cat_banner'], FILTER_SANITIZE_NUMBER_INT );
		$instance['pagination'] = isset( $new_instance['pagination'] ) ? (bool) $new_instance['pagination'] : false;
		$instance['navigation'] = isset( $new_instance['navigation'] ) ? (bool) $new_instance['navigation'] : false;
		$instance['autoplay']   = isset( $new_instance['autoplay'] ) ? (bool) $new_instance['autoplay'] : false;
		$instance['loop']       = isset( $new_instance['loop'] ) ? (bool) $new_instance['loop'] : false;
		$instance['gap']       = isset( $new_instance['gap'] ) ? (bool) $new_instance['gap'] : false;
		$instance['delay']      = isset( $new_instance['delay'] ) ? filter_var( $new_instance['delay'], FILTER_SANITIZE_NUMBER_INT ) : 6000;

		return $instance;
	}
}

add_action( 'widgets_init', function () {
	register_widget( 'W_Banner_Carousel_Widget' );
} );
