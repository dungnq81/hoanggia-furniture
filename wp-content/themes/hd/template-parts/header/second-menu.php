<?php
/**
 * Displays main navigation
 *
 * @package WordPress
 */

if ( ! has_nav_menu( 'second-nav' ) ) :
	return;
endif;

?>
<nav id="second-navigation" class="second-nav" role="navigation" aria-label="<?php echo esc_attr__( 'Secondary Navigation', 'hd' ); ?>">
	<?php echo second_nav(); ?>
</nav>
