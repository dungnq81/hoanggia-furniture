<?php
/**
 * The site's entry point.
 *
 * Loads the relevant template part,
 * the loop is executed (when needed) by the relevant template part.
 *
 * @package hd
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();

if ( is_singular() ) {
	if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'single' ) ) {
		if ( is_single() ) {
			$templates = [];
			$post_type = $post->post_type;
			if ( '' !== $post_type ) {
				$templates[] = "template-parts/single-" . $post_type . ".php";
			}

			$templates[] = 'template-parts/single.php';
			locate_template( $templates, true);
			unset( $templates );

		} elseif ( is_page() ) {
			get_template_part( 'template-parts/page' );
		}
	}
}
elseif ( is_archive() || is_home() ) {
	if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'archive' ) ) {

		$term = get_queried_object();
		$templates = [];
		if ( is_object($term) && isset( $term->taxonomy ) ) {
			$taxonomy = (string) $term->taxonomy;
			if ( '' !== $taxonomy ) {
				$templates[] = "template-parts/archive-" . $taxonomy . ".php";
			}
		}

		$templates[] = 'template-parts/archive.php';
		locate_template( $templates, true);
		unset( $templates );
	}
}
elseif ( is_search() ) {
	if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'archive' ) ) {
		get_template_part( 'template-parts/search' );
	}
}
else {
	if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'single' ) ) {
		get_template_part( 'template-parts/404' );
	}
}

get_footer();
