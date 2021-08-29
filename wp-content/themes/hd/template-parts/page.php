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
if ( is_active_sidebar( 'w-post-sidebar' ) ):
	$is_sidebar = true;
endif;

$post_thumbnail = image_src( get_post_thumbnail_id($post->ID), 'post-thumbnail' );

?>
<section class="section single-post single-page">
	<div class="grid-container">
		<?php if ( true === $is_sidebar ): ?>
		<div class="col-sidebar">
			<div class="sidebar--wrap">
				<?php dynamic_sidebar('w-post-sidebar');?>
			</div>
		</div>
		<?php endif;?>
		<div class="col-content<?php if ( true === $is_sidebar ) echo ' has-sidebar';?>">
			<div class="single-title">
				<?php the_title('<h1 class="title">', '</h1>');?>
			</div>
			<?php if ($post_thumbnail) : ?>
			<div class="thumbnail">
				<?php echo get_the_post_thumbnail( $post, 'post-thumbnail' ); ?>
			</div>
			<?php endif; ?>
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

				get_template_part('template-parts/parts/inline-share');

				// If comments are open or we have at least one comment, load up the comment template.
				the_comment_html($post->ID);
				?>
			</div>
		</div>
	</div>
</section>
