<?php
/**
 * The template for displaying homepage
 * Template Name: W - Homepage
 * Template Post Type: page
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; /*Exit if accessed directly.*/
}

get_header();

// homepage widget
if ( is_active_sidebar( 'w-homepage-sidebar' ) ) :
	dynamic_sidebar( 'w-homepage-sidebar' );
endif;

get_footer();
