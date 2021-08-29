<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$term = get_queried_object();
$is_sidebar = false;
if ( is_active_sidebar( 'w-product-sidebar' ) ):
	$is_sidebar = true;
endif;

$title = $term->name;
$desc = term_excerpt( $term, null );

if ( ! $title ) {
	$title = $term->post_title;
	$desc = post_excerpt( $term, null );
}

$check_desc = strip_whitespace( $desc );

?>
<section class="section title-nav-section title-nav-product" role="contentinfo" tabindex="-1">
	<div class="grid-container">
		<h1 class="archive-title"><?=$title?></h1>
		<?php the_breadcrumb_theme(); ?>
	</div>
</section>
<section class="section archive-products">
	<div class="grid-container">
		<?php if ( true === $is_sidebar ): ?>
		<div class="col-sidebar">
			<div class="sidebar--wrap">
				<?php dynamic_sidebar('w-product-sidebar');?>
			</div>
		</div>
		<?php endif;?>
		<div class="col-content<?php if ( true === $is_sidebar ) echo ' has-sidebar';?>">
			<?php get_template_part('template-parts/product/grid');?>
		</div>
	</div>
</section>
