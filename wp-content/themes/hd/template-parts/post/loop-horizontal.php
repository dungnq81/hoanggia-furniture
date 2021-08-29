<?php
/**
 * Template part for displaying posts with excerpts
 * @package WordPress
 * @since   1.0.0
 */

$post_name   = get_the_title( $post->ID );
$url         = get_permalink();
$ratio       = get_theme_mod_ssl( 'news_menu_setting' );
$ratio_class = $ratio;
if ( 'default' == $ratio or ! $ratio ) {
	$ratio_class = '3v2';
}

?>
<article class="item">
    <div class="cover-content">
        <?php echo w_post_term( $post ); ?>
        <h4><a href="<?= $url ?>" title="<?php echo esc_attr( $post_name ); ?>"><?=$post_name?></a></h4>
		<?php echo loop_excerpt( $post ); ?>
        <div class="btn-group">
            <a href="<?= $url ?>" class="button view-more" title="<?php esc_attr_e( 'Read more', 'hd' ) ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M11.293 17.293L12.707 18.707 19.414 12 12.707 5.293 11.293 6.707 15.586 11 6 11 6 13 15.586 13z"></path></svg>
				<?php _e( 'Read more', 'hd' ) ?>
            </a>
        </div>
    </div>
	<div class="cover">
        <span class="--bg" style="background-image: url(<?php echo get_post_thumbnail( $post, 'medium', false );?>)"></span>
        <a href="<?= $url ?>" class="cover-trigger" aria-label="<?php echo esc_attr( $post_name ); ?>"></a>
	</div>
</article>
