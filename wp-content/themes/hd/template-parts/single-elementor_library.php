<?php
/**
 * The template for displaying singular post-types: posts, pages and user-defined custom post types.
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if (have_posts()) the_post();

?>
<section class="section title-nav-section title-single-section" role="contentinfo" tabindex="-1">
    <div class="grid-container">
		<?php the_title('<h1 class="title">', '</h1>');?>
		<?php the_breadcrumb_theme(); ?>
    </div>
</section>
<section class="section single-elementor">
    <div class="col-content clearfix">
		<?php
		// post content
		the_content(
			sprintf(
				wp_kses(
				/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'hd' ),
					[ 'span' => [ 'class' => [], ], ]
				),
				get_the_title()
			)
		);
		?>
    </div>
</section>
