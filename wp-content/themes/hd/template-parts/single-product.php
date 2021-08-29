<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if (have_posts()) the_post();
if (post_password_required()) :
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
endif;

$is_sidebar = false;
if ( is_active_sidebar( 'w-product-sidebar' ) ):
	$is_sidebar = true;
endif;

$term = primary_term();

?>
<section class="section title-nav-section title-nav-product" role="contentinfo" tabindex="-1">
	<div class="grid-container">
		<?php the_title('<h1 class="single-title">', '</h1>');?>
		<?php the_breadcrumb_theme(); ?>
	</div>
</section>
<section class="section single-product">
	<div class="grid-container">
		<?php if ( true === $is_sidebar ): ?>
		<div class="col-sidebar">
			<div class="sidebar--wrap">
				<?php dynamic_sidebar('w-product-sidebar');?>
			</div>
		</div>
		<?php endif;?>
		<div class="col-content<?php if ( true === $is_sidebar ) echo ' has-sidebar';?>">
			<div class="single-title">
				<?php
				$subtitle = get_field ('subtitle', $post->ID);
				if (!$subtitle)
					$subtitle = $post->post_title;
				?>
				<h2 class="title h1"><?=$subtitle?></h2>
			</div>
			<div class="single-info">
				<?php get_template_part('template-parts/parts/sharing'); ?>
			</div>
			<?php echo post_excerpt($post); ?>
			<div class="content clearfix">
				<?php
				// post content
				the_content(
					sprintf(
						wp_kses(
						/* translators: %s: Name of current post. Only visible to screen readers */
							__('Continue reading<span class="screen-reader-text"> "%s"</span>', 'hd'),
							['span' => ['class' => [],],]
						),
						get_the_title()
					)
				);

				entry_hashtag('product_tag');

				// If comments are open or we have at least one comment, load up the comment template.
				the_comment_html($post->ID);
				?>
			</div>
		</div>
	</div>
</section>
