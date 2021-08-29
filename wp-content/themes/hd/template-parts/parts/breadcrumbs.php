<?php
if ( ! defined( 'ABSPATH' ) )
    exit;

$breadcrumb_bg = get_theme_mod_ssl( 'breadcrumb_bg_setting' );
$_class = '';
if ( $breadcrumb_bg ) {
	$_class = ' has-background';
}

?>
<nav class="navigation-bar<?=$_class?>" aria-label="breadcrumbs">
	<?php
	if ( function_exists( 'w_the_breadcrumbs' ) ) :
		w_the_breadcrumbs();
    elseif ( function_exists( 'woocommerce_breadcrumb' ) ) :
		woocommerce_breadcrumb();
    elseif ( function_exists( 'rank_math_the_breadcrumbs' ) ) :
		rank_math_the_breadcrumbs();
	endif;
	?>
</nav>
