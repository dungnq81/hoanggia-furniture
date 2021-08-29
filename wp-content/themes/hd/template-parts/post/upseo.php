<?php
defined( 'ABSPATH' ) || exit;

$up_seo_list        = get_field( 'up_seo', $post->ID );
if ( $up_seo_list ) :
	echo '<ul class="upseo-list">';
	foreach ( $up_seo_list as $up_id ) :
		$post = get_post( $up_id );
		$post_title = get_the_title( $up_id );
		$title      = ( ! empty( $post_title ) ) ? $post_title : __( '(no title)', 'hd' );
		?>
        <li>
            <a title="<?php echo esc_attr( $title ); ?>" class="post-title" href="<?php the_permalink( $up_id ); ?>"><?php echo $title; ?></a>
            <span class="post-date"><?php echo posted_on_humanize(); ?></span>
        </li>
	<?php
	endforeach;
	wp_reset_postdata();
	echo '</ul>';
endif;
