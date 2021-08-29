<?php

// If plugin - 'ArContactUs' not exist then return.
if ( ! class_exists( 'ArContactUs' ) ) {
	return;
}

if ( ! class_exists( 'W_ArContactUs' ) ) {

	class W_ArContactUs {

		/**
		 * Member Variable
		 *
		 * @var object instance
		 */
		private static $instance;

		/**
		 * Initiator
		 *
		 * @return object|W_ArContactUs
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * W_ArContactUs constructor.
		 */
		public function __construct() {
			add_action( "wp_enqueue_scripts", [ $this, 'enqueue' ], 999 );
			add_action( 'get_footer', [ $this, 'footer_styles' ] );

			add_filter( 'style_loader_tag', [ $this, 'style_loader_tag' ], 10, 2 );
			add_filter( 'script_loader_tag', [ $this, 'script_loader_tag' ], 10, 3 );
		}

		/**
		 * Dequeue Style
		 */
		public function enqueue() {
			wp_dequeue_style( "jquery.contactus.css" );
			wp_dequeue_style( "contactus.generated.desktop.css" );

			wp_dequeue_script( 'jquery.contactus.scripts' );
			wp_dequeue_script( 'jquery.contactus' );
		}

		/**
		 * @return void
		 */
		public function footer_styles() {
			if ( wp_style_is( "jquery.contactus.css", 'registered') ) {
				wp_enqueue_style( "jquery.contactus.css" );
			}

			if ( wp_style_is( "contactus.generated.desktop.css", 'registered') ) {
				wp_enqueue_style( "contactus.generated.desktop.css" );
			}

			wp_enqueue_script( 'jquery.contactus' );
			wp_enqueue_script( 'jquery.contactus.scripts' );
		}

		/**
		 * @param string $tag
		 * @param string $handle
		 * @param string $src
		 *
		 * @return string
		 */
		public function script_loader_tag( string $tag, string $handle, string $src ) {

			$str_parsed = [
				'jquery.contactus'              => 'defer',
				'jquery.contactus.scripts'      => 'defer',
			];

			return script_loader_tag( $str_parsed, $tag, $handle, $src );
		}

		/**
		 * @param string $html
		 * @param string $handle
		 *
		 * @return string
		 */
		public function style_loader_tag( string $html, string $handle ) {

			// add style handles to the array below
			$styles = [
				'jquery.contactus.css',
				'contactus.generated.desktop.css',
			];

			return style_loader_tag( $styles, $html, $handle );
		}
	}

	W_ArContactUs::get_instance();
}
