<?php
/**
 * Theme functions and definitions
 *
 * @package hd
 */

$theme = wp_get_theme( 'hd' );

define( 'W_THEME_VERSION', $theme['Version'] );
define( 'W_THEME_DIR', trailingslashit( get_template_directory() ) );
define( 'W_THEME_URI', trailingslashit( esc_url( get_template_directory_uri() ) ) );

const INC = __DIR__ . '/inc';

require_once INC . '/core/helpers.php';
require_once INC . '/core/common-functions.php'; // helpers functions

require INC . '/custom-types/custom-type.php';
require INC . '/compatibility/compatibility.php'; // Theme compatibility.
require INC . '/W_Customizer.php'; // Customizer additions.

$W = (object) array(
	'version' => W_THEME_VERSION,

	// Initialize all the things.
	'main' => require INC . '/W.php',
);

if ( is_woocommerce_activated() ) {
	$W->woocommerce = require INC . '/compatibility/woocommerce/class-W_Woocommerce.php';
	require INC . '/compatibility/woocommerce/woocommerce-functions.php';
}

if ( is_elementor_activated() ) {
	$W->elementor = require INC . '/compatibility/elementor/class-W_Elementor_Pro.php';
	require INC . '/compatibility/elementor/elementor-functions.php';
}

require INC . '/autoload.php';
require INC . '/responsive-images.php';
require INC . '/template-functions.php'; // filter functions

require INC . '/ajax.php';
require INC . '/frontend.php';

require INC . '/W_Hooks.php'; // hooks functions
require INC . '/W_Deferred.php';
require INC . '/W_Shortcode.php';
require INC . '/W_Admin.php'; // custom admin
