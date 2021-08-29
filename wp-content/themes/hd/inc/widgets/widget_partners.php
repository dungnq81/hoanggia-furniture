<?php
defined( 'ABSPATH' ) || exit;

class W_Partners_widget extends WP_Widget {

	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'w_partners_widget',
			'description'                 => __( 'Display the partners section', 'hd' ),
			'customize_selective_refresh' => true,
		);

		parent::__construct( 'w_partners_widget', __( 'W - Partners', 'hd' ), $widget_ops );
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
		$desc = get_field( 'desc', 'widget_' . $args['widget_id'], false );
		$css_class = get_field( 'css_class', 'widget_' . $args['widget_id'] );
		$check_desc     = preg_replace( '/\s+/', '', wp_strip_all_tags( $desc ) );
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
		<section id="<?=$this->id?>" class="section partners <?=$_class?>">
			<div class="grid-container">
				<?php if ($title) : ?>
				<h3 class="heading-title"><?php echo $title; ?></h3>
				<?php endif; if ( $check_desc ) : ?>
				<div class="heading-desc"><?= $desc ?></div>
				<?php endif;?>
            </div>
            <div class="grid-container grid-extra">
                <ul class="partners-wrapper">
					<?php
					// Load slides loop.
					while ( $slides_query->have_posts() ) : $slides_query->the_post();
						$post = get_post();

						if ( has_post_thumbnail() ) :
							$responsive_img = get_field( 'responsive_image', $post->ID );
							$url = get_field( 'url', $post->ID );
							if ( ! $url )
							    $url = '#';

							$btn_name = get_field( 'button_name', $post->ID );

							?>
                            <li>
                                <a class="_blank" href="<?=$url?>" title="<?php the_title_attribute() ?>">
                                    <picture class="res res-3v2 auto scale">
										<?php if ( $responsive_img ) : ?>
										<source media="(max-width: 639.98px)" srcset="<?= $responsive_img ?>">
										<?php endif; ?>
                                        <source srcset="<?= get_post_thumbnail( $post, 'medium_large', false ) ?>">
                                        <?php echo get_the_post_thumbnail( $post, 'medium_large');?>
                                    </picture>
                                    <?php if ($btn_name) : ?>
                                    <div class="p-title" aria-label="<?php esc_attr_e( $btn_name, 'hd' ); ?>"><?php echo __( $btn_name, 'hd' ); ?></div>
                                    <?php endif; ?>
                                </a>
                            </li>
						<?php
						endif;
					endwhile;
					wp_reset_postdata();
					?>
                </ul>
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
				'cat_banner' => '',
				'column'     => 5,
			]
		);

		$title = esc_attr( $instance['title'] );
		$cat_banner     = filter_var( $instance['cat_banner'], FILTER_SANITIZE_NUMBER_INT );
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

		return $instance;
	}
}

add_action( 'widgets_init', function () {
	register_widget( 'W_Partners_widget' );
} );
