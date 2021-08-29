<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( is_admin() ) {
	return;
}

// -------------------------------------------------------------

add_action( 'pre_get_posts', function ( $query ) {
	$posts_per_page = get_option( 'posts_per_page' );
	if ( isset( $_GET['pagenum'] ) && $query->is_main_query() ) {
		$pagenum = esc_sql( $_GET['pagenum'] );
		if ( in_array( $pagenum, [ 1, 6, 12, 24, 30, 36, 48, 50 ] ) ) {
			$posts_per_page = $pagenum;
		}

		$query->set( 'posts_per_page', $posts_per_page );
	}
} );
