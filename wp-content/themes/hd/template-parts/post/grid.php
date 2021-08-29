<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( have_posts() ) :

?>
<div class="grid-post-wrapper grid-x grid-padding-x small-up-1 medium-up-2 large-up-3">
    <?php

    // Start the Loop.
    while ( have_posts() ) : the_post();

        echo "<div class=\"cell\">";
        get_template_part( 'template-parts/post/loop' );
        echo "</div>";

        // End the loop.
    endwhile;
    wp_reset_postdata();
    ?>
</div>
<?php
	// Previous/next page navigation.
	pagination_links();
else :
	get_template_part( 'template-parts/parts/content-none' );
endif;
