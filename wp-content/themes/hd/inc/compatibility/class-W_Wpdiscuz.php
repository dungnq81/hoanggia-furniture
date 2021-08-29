<?php

// If plugin - 'Wpdiscuz' not exist then return.
if ( ! class_exists( 'WpdiscuzCore' ) ) {
	return;
}

if ( ! class_exists( 'W_Wpdiscuz' ) ) {

	class W_Wpdiscuz {

		/**
		 * Member Variable
		 *
		 * @var object instance
		 */
		private static $instance;

		/**
		 * Initiator
		 *
		 * @return object|W_Woocommerce
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * W_Wpdiscuz constructor.
		 */
		public function __construct() {
			add_action( "wp_enqueue_scripts", [ $this, 'scripts' ], 11 );
			add_action( 'admin_init', [ $this, 'admin_init' ] );

			add_action( "wp_enqueue_scripts", [ $this, 'enqueue' ], 999 );
			add_action( 'get_footer', [ $this, 'footer_styles' ] );

			add_filter( 'style_loader_tag', [ $this, 'delay_style_loader_tag' ], 10, 2 );
			add_filter( 'script_loader_tag', [ $this, 'delay_script_loader_tag' ], 10, 3 );
		}

		/**
		 * Dequeue
		 */
		public function enqueue() {
			wp_dequeue_style( "wpdiscuz-frontend-css" );
			wp_dequeue_style( "wpdiscuz-combo-css" );
		}

		/**
		 * @return void
		 */
		public function footer_styles() {
			if ( wp_style_is( "wpdiscuz-frontend-css", 'registered') ) {
				wp_enqueue_style( "awe-font", get_template_directory_uri() . '/assets/css/awe.css', [], '5.13.0' );
				wp_enqueue_style( "wpdiscuz-frontend-css" );
			}
			if ( wp_style_is( "wpdiscuz-combo-css", 'registered') ) {
				wp_enqueue_style( "wpdiscuz-combo-css" );
			}
		}

		/**
		 * Enqueue scripts and styles.
		 * @return void
		 */
		public function scripts() {
			wp_deregister_style( "wpdiscuz-fa" );
			wp_deregister_style( "wpdiscuz-font-awesome" );
		}

		/**
		 * @param string $html
		 * @param string $handle
		 *
		 * @return string
		 */
		public function delay_style_loader_tag( string $html, string $handle ) {

			// add style handles to the array below
			$styles = [
				'awe-font',
				'wpdiscuz-frontend-css',
				'wpdiscuz-combo-css',
			];

			return style_loader_tag( $styles, $html, $handle );
		}

		/**
		 * @param string $tag
		 * @param string $handle
		 * @param string $src
		 *
		 * @return string
		 */
		public function delay_script_loader_tag( string $tag, string $handle, string $src ) {

			$str_parsed = [
				'wpdiscuz-combo-js' => 'delay',
			];

			return script_loader_tag( $str_parsed, $tag, $handle, $src );
		}

		/**
		 * fix_wpdiscuz_addons
		 */
		public function admin_init() {
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$all_plugins = get_plugins();
			foreach ( $all_plugins as $plugin_slug => $values ) {
				if ( strpos( $plugin_slug, "pdiscuz-" ) ) {
					$slug_parts   = explode( "/", $plugin_slug );
					$discuz_addon = $slug_parts[0];
					get_option( 'gvt_product_secret_' . $discuz_addon ) || add_option( 'gvt_product_secret_' . $discuz_addon, 'Congratulations! Plugin activated successfully.' );
				}
			}
		}
	}
}

W_Wpdiscuz::get_instance();
