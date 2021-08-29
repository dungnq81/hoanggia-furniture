<?php
/**
 * Template Filters
 * @author   WEBHD
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// -------------------------------------------------------------

/**
 * DEBUG is false
 */
if ( ! WP_DEBUG ) {

	// Remove WP version from RSS.
	add_filter( 'the_generator', '__return_empty_string' );

	add_filter( 'style_loader_src', 'remove_version_scripts_styles', 11, 1 );
	add_filter( 'script_loader_src', 'remove_version_scripts_styles', 11, 1 );
	/**
	 * Remove version from scripts and styles
	 *
	 * @param $src
	 *
	 * @return bool|string
	 */
	function remove_version_scripts_styles( $src ) {
		if ( $src && str_contains( $src, 'ver=' ) ) {
			$src = remove_query_arg( 'ver', $src );
		}

		return $src;
	}
}

// -------------------------------------------------------------

// Changing the alt text on the logo to show your site name
add_filter( 'login_headertext', function () {
	return get_option( 'blogname' );
} );

// Changing the logo link from wordpress.org to your site
add_filter( 'login_headerurl', function () {
	return esc_url( site_url( '/' ) );
} );

// -------------------------------------------------------------

// Adding Shortcode in WordPress Using Custom HTML Widget
add_filter( 'widget_text', 'shortcode_unautop' );
add_filter( 'widget_text', 'do_shortcode' );

// -------------------------------------------------------------

// comment off
add_filter( 'wp_insert_post_data', function ( $data ) {
	if ( $data['post_status'] == 'auto-draft' ) {
		$data['comment_status'] = 0;
		$data['ping_status']    = 0;
	}

	return $data;
}, 10, 1 );

// -------------------------------------------------------------

/**
 * Add support for buttons in the top-bar menu:
 * 1) In WordPress admin, go to Apperance -> Menus.
 * 2) Click 'Screen Options' from the top panel and enable 'CSS CLasses' and 'Link Relationship (XFN)'
 * 3) On your menu item, type 'has-form' in the CSS-classes field. Type 'button' in the XFN field
 * 4) Save Menu. Your menu item will now appear as a button in your top-menu
 */
add_filter( 'wp_nav_menu', function ( $ulclass ) {
	$find    = [ '/<a rel="button"/', '/<a title=".*?" rel="button"/' ];
	$replace = [ '<a rel="button" class="button"', '<a rel="button" class="button"' ];

	return preg_replace( $find, $replace, $ulclass, 1 );
} );

// -------------------------------------------------------------

add_filter( 'sanitize_file_name', function ( string $filename ) {
	$filename = remove_accents( $filename );

	return $filename;
}, 10, 1 );

// -------------------------------------------------------------

add_filter( 'wp_nav_menu_args', function ( $args ) {
	if ( isset( $args['walker'] ) && is_string( $args['walker'] ) && class_exists( $args['walker'] ) ) {
		$args['walker'] = new $args['walker'];
	}

	return $args;
}, 1001 );

// -------------------------------------------------------------

add_filter( "posts_search", function ( $search, $wp_query ) {

	global $wpdb;
	if ( empty( $search ) ) {
		return $search; // skip processing – no search term in query
	}

	$q      = $wp_query->query_vars;
	$n      = ! empty( $q['exact'] ) ? '' : '%';
	$search =
	$searchand = '';
	foreach ( (array) $q['search_terms'] as $term ) {
		$term      = esc_sql( $wpdb->esc_like( $term ) );
		$search    .= "{$searchand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
		$searchand = " AND ";
	}
	if ( ! empty( $search ) ) {
		$search = " AND ({$search}) ";
		if ( ! is_user_logged_in() ) {
			$search .= " AND ($wpdb->posts.post_password = '') ";
		}
	}

	return $search;
}, 500, 2 );

// -------------------------------------------------------------

add_filter( 'excerpt_more', function () {
	return '...';
} );

// -------------------------------------------------------------

// Remove id li navigation
add_filter( 'nav_menu_item_id', '__return_null', 10, 3 );

// -------------------------------------------------------------

/**
 * @param $output
 * @param $r
 *
 * @return mixed|string|string[]
 */
add_filter( 'wp_dropdown_cats', function ( $output, $r ) {

	if ( isset( $r['multiple'] ) && $r['multiple'] ) {
		$output = preg_replace( '/^<select/i', '<select multiple', $output );
		$output = str_replace( "name='{$r['name']}'", "name='{$r['name']}[]'", $output );
		foreach ( array_map( 'trim', explode( ",", $r['selected'] ) ) as $value ) {
			$output = str_replace( "value=\"{$value}\"", "value=\"{$value}\" selected", $output );
		}
	}

	return $output;

}, 10, 2 );

// -------------------------------------------------------------

/**
 * @param array $args
 *
 * @return array
 */
add_filter( 'widget_tag_cloud_args', function ( array $args ) {

	$args['smallest'] = '10';
	$args['largest']  = '19';
	$args['unit']     = 'px';
	$args['number']   = 12;

	return $args;
} );

// -------------------------------------------------------------

/**
 * Use the is-active class of ZURB Foundation on wp_list_pages output.
 * From required+ Foundation http://themes.required.ch.
 */
add_filter( 'wp_list_pages', function ( $input ) {

	$pattern = '/current_page_item/';
	$replace = 'current_page_item is-active active';
	$output  = preg_replace( $pattern, $replace, $input );

	return $output;

}, 10, 2 );

// -------------------------------------------------------------

// add class to achor link
add_filter( 'nav_menu_link_attributes', function ( $atts ) {
	//$atts['class'] = "nav-link";
	return $atts;
}, 100, 1 );

// ------------------------------------------------------

add_filter( 'upload_mimes', function ( $mime_types ) {

	//Add additional mime types here
	//$mime_types['svg'] = 'image/svg+xml';
	return $mime_types;

}, 10, 1 );

// ------------------------------------------------------

/**
 * @param $item_output
 * @param $item
 * @param $depth
 * @param $args
 *
 * @return string|string[]
 */
add_filter( 'walker_nav_menu_start_el', function ( $item_output, $item, $depth, $args ) {

	// Change SVG icon inside social links menu if there is supported URL.
	if ( 'social-nav' === $args->theme_location && class_exists( 'W_SVG_Icons' ) ) {
		$svg = W_SVG_Icons::get_social_link_svg( $item->url, 24 );
		if ( ! empty( $svg ) ) {
			$item_output = str_replace( $args->link_before, $svg, $item_output );
		}
	}

	return $item_output;

}, 10, 4 );

