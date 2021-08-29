<?php
/**
 * Template part for displaying posts with excerpts
 * @package WordPress
 * @since   1.0.0
 */

$post_name   = get_the_title( $post->ID );
$url         = get_permalink();
$ratio       = get_theme_mod_ssl( 'product_menu_setting' );
$ratio_class = $ratio;
if ( 'default' == $ratio or ! $ratio ) {
	$ratio_class = '3v2';
}

?>
<article class="item">
	<figure class="cover">
		<span class="res auto res-<?=$ratio_class?>"><?php echo get_the_post_thumbnail( $post, 'medium' ); ?></span>
        <a href="<?= $url ?>" class="cover-trigger" aria-label="<?php echo esc_attr( $post_name ); ?>"></a>
	</figure>
	<div class="cover-content">
        <h4><a href="<?= $url ?>" title="<?php echo esc_attr( $post_name ); ?>"><?=$post_name?></a></h4>
		<?php echo loop_excerpt( $post ); ?>
		<a class="view-more" href="<?= $url ?>" title="<?php echo esc_attr( $post_name ); ?>">Chi tiết</a>
	</div>
</article>
