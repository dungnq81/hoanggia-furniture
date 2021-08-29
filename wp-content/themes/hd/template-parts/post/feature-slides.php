<?php

if ( have_posts() ) :
?>
<div class="swiper-section">
	<?php
	$_data = '';
	$_data .= ' data-autoview = 1';
	$_data .= ' data-delay = 6000';
	$_data .= ' data-autoplay = 1';
	$_data .= ' data-loop = 1';
	//$_data .= ' data-navigation = 1';

	?>
    <div class="w-swiper swiper-container swiper-news-horizontal">
        <div class="swiper-wrapper"<?php echo $_data?>>
		    <?php
		    $i = 0;

		    // Load slides loop.
		    while ( have_posts() && $i < 2) : the_post();
			    echo '<div class="swiper-slide">';
			    get_template_part( 'template-parts/post/loop-horizontal' );
			    echo '</div>';
			    ++ $i;
		    endwhile;
		    wp_reset_postdata();
		    ?>
        </div>
    </div>
</div>
<?php endif;
