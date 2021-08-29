<?php
/**
 * The template for displaying the footer.
 * Contains the body & html closing tags.
 * @package hd
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) :
?>
<div class="site-footer">
	<?php

	do_action( 'w_footer' );
	?>
</div>
<!--<div class="__blur"></div>-->
<!--<div class="__line"></div>-->
<?php endif;
wp_footer();
?>
</body>
</html>
