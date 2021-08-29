<?php
/**
 * Jetpack Compatibility File.
 *
 * @link https://jetpack.me/
 */

// If plugin - 'Jetpack' not exist then return.
if ( ! class_exists( 'Jetpack' ) ) {
	return;
}

// Jetpack Compatibility
if ( ! class_exists( 'W_Jetpack' ) ) {

	class W_Jetpack {

		/**
		 * Member Variable
		 *
		 * @var object instance
		 */
		private static $instance;

		/**
		 * Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * W_Jetpack constructor.
		 */
		public function __construct() {
			add_action( 'after_setup_theme', array( $this, 'jetpack_setup' ) );
			add_filter( 'jetpack_lazy_images_blocked_classes', array( $this, 'lazy_exclude' ) );
		}

		/**
		 * Excludes some classes from Jetpack's lazy load.
		 */
		public function lazy_exclude( $blacklisted_classes ) {
			$blacklisted_classes = array(
				'skip-lazy',
				'wp-post-image',
				'post-image',
				'wishlist-thumbnail',
				'custom-logo',
			);
			return $blacklisted_classes;
		}

		/**
		 * Add theme support
		 */
		public function jetpack_setup() {

			/**
			 * Add theme support for Infinite Scroll.
			 * See: https://jetpack.me/support/infinite-scroll/
			 */
			add_theme_support(
				'infinite-scroll',
				array(
					'container' => 'content',
					'render'    => array( $this, 'infinite_scroll_render' ),
					'footer'    => 'page',
				)
			);

			/**
			 *  Add support for the Site Logo plugin and the site logo functionality in JetPack
			 *  https://github.com/automattic/site-logo
			 *  http://jetpack.me/
			 */
			add_theme_support(
				'site-logo', apply_filters(
					'site_logo_args', array(
						'size' => 'full',
					)
				)
			);
		}

		/**
		 * Custom render function for Infinite Scroll.
		 */
		public function infinite_scroll_render() {
			while ( have_posts() ) {
				the_post();
				get_template_part( 'template-parts/single-jetpack' );
			}
		}
	}
}

W_Jetpack::get_instance();
