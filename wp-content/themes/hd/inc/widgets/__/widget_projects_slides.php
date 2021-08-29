<?php
defined( 'ABSPATH' ) || exit;

class W_Projects_Slides_Widget extends WP_Widget {
	/**
	 * W_Projects_Slides_Widget constructor.
	 */
	public function __construct() {

		$widget_ops = array(
			'classname'                   => 'w_projects_slides',
			'description'                 => __( 'Collection of projects banners', 'hd' ),
			'customize_selective_refresh' => true,
		);

		parent::__construct( 'w_projects_slides', __( 'W - Projects Slides', 'hd' ), $widget_ops );
		$this->alt_option_name = 'w_projects_slides';
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
		$row = ( ! empty( $instance['row'] ) ) ? absint( $instance['row'] ) : 1;
		$pagination     = isset( $instance['pagination'] ) ? $instance['pagination'] : true;
		$navigation     = isset( $instance['navigation'] ) ? $instance['navigation'] : true;
		$full_width     = isset( $instance['full_width'] ) ? $instance['full_width'] : false;
		$autoplay       = isset( $instance['autoplay'] ) ? $instance['autoplay'] : true;
		$freeMode       = isset( $instance['freeMode'] ) ? $instance['freeMode'] : false;
		$loop           = isset( $instance['loop'] ) ? $instance['loop'] : true;
		$gap           = isset( $instance['gap'] ) ? $instance['gap'] : false;
		$delay          = isset( $instance['delay'] ) ? $instance['delay'] : 6000;

		$html_title = get_field( 'html_title', 'widget_' . $args['widget_id'] );
		$desc = get_field( 'desc', 'widget_' . $args['widget_id'], false );
		$css_class = get_field( 'css_class', 'widget_' . $args['widget_id'] );

		$check_desc     =  strip_whitespace( $desc );
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
        <section id="<?=$this->id?>" class="section swipers projects_carousel <?=$_class?>">
			<div class="blob pos-right">
				<svg width="419" height="840" viewBox="0 0 419 840" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd" clip-rule="evenodd" d="M423.701 0.898349C337.621 10.8443 302.785 116.351 233.336 168.051C165.297 218.701 61.9344 224.357 22.6661 299.441C-18.1687 377.52 3.6134 472.997 29.88 557.075C56.4103 641.998 99.0947 723.788 171.468 775.749C243.653 827.575 334.786 841.805 423.701 839.823C510.992 837.877 609.053 829.052 667.876 764.685C724.561 702.657 687.896 603.309 713.042 523.21C737.744 444.525 825.636 384.778 812.71 303.339C799.561 220.491 715.371 171.581 649.049 120.019C580.316 66.5808 510.265 -9.10351 423.701 0.898349Z" fill="#F5F3FB"></path>
				</svg>
			</div>
	        <div class="grid-container">
				<?php if ($html_title) : ?>
				<h2 class="title w-trigger"><?php echo $html_title; ?></h2>
				<?php endif; if ( $check_desc ) : ?>
				<div class="desc w-trigger"><?= $desc ?></div>
				<?php endif;?>
			</div>
			<?php if ( ! $full_width ) echo '<div class="grid-container">'; ?>
				<div class="swiper-section<?php if ($gap) echo ' px-15';?>">
					<div class="w-swiper swiper-container">
						<?php
						$_data = '';
						if ( $row < 2 ) {
							$_data .= ' data-autoview=1';
							if ( $loop ) {
								$_data .= ' data-loop=1';
							}
						} else {
							$_data .= ' data-desktop=3';
							$_data .= ' data-tablet=2';
							$_data .= ' data-mobile=1';
							$_data .= ' data-row=' . $row;
						}

						if ( $gap ) $_data .= ' data-gap=1';
						if ( $delay ) $_data .= ' data-delay=' . $delay;
						if ( $freeMode ) $_data .= ' data-freemode=1';
						if ( $autoplay ) $_data .= ' data-autoplay=1';
						if ( $pagination ) $_data .= ' data-pagination="dynamic"';
						if ( $navigation ) $_data .= ' data-navigation=1';

						?>
						<div class="swiper-wrapper"<?php echo $_data?>>
							<?php
							// Load slides loop.
							while ( $slides_query->have_posts() ) : $slides_query->the_post();
								$post = get_post();
								if ( has_post_thumbnail() ) :
									//$img = get_post_thumbnail( $post, 'large', false );

									// custom fields
									$url            = get_field( 'url', $post->ID );
									$responsive_img = get_field( 'responsive_image', $post->ID );
									$title2         = get_field( 'title2', $post->ID );
									$desc           = get_field( 'desc', $post->ID, false );

									$check_title2   = strip_whitespace( $title2 );
									$check_desc     = strip_whitespace( $desc );

									$btn_name = get_field( 'button_name', $post->ID );
									if ( empty( $btn_name ) )
										$btn_name = 'Read more';

									$video_link = get_field( 'video_link', $post->ID );
									$is_video_flag = ' _blank';
									if ( $video_link ) $is_video_flag = ' popup-video';
							?>
							<div class="swiper-slide project-slide w-trigger2">
								<article class="item">
									<div class="content-wrap">
										<?php if ( $check_title2 ) : ?>
										<div class="project-title"><?= $title2 ?></div>
										<?php endif;?>
										<?php if ( $check_desc ) : ?>
										<div class="project-desc"><?= $desc ?></div>
										<?php endif; ?>
										<?php if ( $url ) : ?>
										<div><a class="button project-link<?=$is_video_flag?>" href="<?= $url ?>" aria-label="<?php esc_attr_e( $btn_name, 'hd' ); ?>"><?php echo __( $btn_name, 'hd' )?></a></div>
										<?php endif; ?>
									</div>
									<picture>
										<?php if ( $responsive_img ) : ?>
											<source media="(max-width: 639.98px)" srcset="<?= $responsive_img; ?>">
										<?php else : ?>
											<source srcset="<?= get_post_thumbnail( $post, 'medium_large', false ); ?>">
										<?php endif;?>
										<?php echo get_the_post_thumbnail( $post, 'medium_large');?>
									</picture>
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
	        <?php if ( ! $full_width ) echo '</div>'; ?>
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
				'row'     => 1,
				'pagination' => true,
				'navigation' => true,
				'freeMode'   => false,
				'full_width' => false,
				'autoplay'   => false,
				'loop'       => false,
				'gap'       => false,
				'delay'      => 6000,
			]
		);

		$title      = $instance['title'];
		$cat_banner     = filter_var( $instance['cat_banner'], FILTER_SANITIZE_NUMBER_INT );
		$row        = filter_var( $instance['row'], FILTER_SANITIZE_NUMBER_INT );
		$pagination = (bool) $instance['pagination'];
		$navigation = (bool) $instance['navigation'];
		$autoplay   = (bool) $instance['autoplay'];
		$full_width = (bool) $instance['full_width'];
		$freeMode   = (bool) $instance['freeMode'];
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
			<label for="<?php echo $this->get_field_id( 'row' ); ?>"><?php echo __( 'Number of rows', 'hd' ); ?></label>
			<input class="tiny-text" id="<?php echo $this->get_field_id( 'row' ); ?>" name="<?php echo $this->get_field_name( 'row' ); ?>" type="number" step="1" min="1" value="<?php echo $row; ?>" size="3" />
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
            <span style="display: block;margin-top: 5px;"><?php echo __( 'Tắt tính năng này nếu số dòng lớn hơn 1', 'hd' ); ?></span>
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
		$instance['row']        = filter_var( $new_instance['row'], FILTER_SANITIZE_NUMBER_INT );
		$instance['pagination'] = isset( $new_instance['pagination'] ) ? (bool) $new_instance['pagination'] : false;
		$instance['navigation'] = isset( $new_instance['navigation'] ) ? (bool) $new_instance['navigation'] : false;
		$instance['freeMode']   = isset( $new_instance['freeMode'] ) ? (bool) $new_instance['freeMode'] : false;
		$instance['full_width'] = isset( $new_instance['full_width'] ) ? (bool) $new_instance['full_width'] : false;
		$instance['autoplay']   = isset( $new_instance['autoplay'] ) ? (bool) $new_instance['autoplay'] : false;
		$instance['loop']       = isset( $new_instance['loop'] ) ? (bool) $new_instance['loop'] : false;
		$instance['gap']       = isset( $new_instance['gap'] ) ? (bool) $new_instance['gap'] : false;
		$instance['delay']      = isset( $new_instance['delay'] ) ? filter_var( $new_instance['delay'], FILTER_SANITIZE_NUMBER_INT ) : 6000;

		return $instance;
	}
}

add_action( 'widgets_init', function () {
	register_widget( 'W_Projects_Slides_Widget' );
} );
