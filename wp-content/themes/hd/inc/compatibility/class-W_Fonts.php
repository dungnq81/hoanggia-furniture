<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'W_Fonts' ) ) {

	class W_Fonts {

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
		 * W_Fonts constructor.
		 */
		public function __construct() {
			add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ], 10 );
			add_filter( 'style_loader_tag', [ $this, 'delay_style_loader_tag' ], 10, 2 );
		}

		/**
		 * Enqueue scripts and styles.
		 * @return void
		 */
		public function scripts() {

			/*Fonts*/
			//wp_enqueue_style( "gfont-montserrat", 'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap' );
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
				//'gfont-montserrat',
			];

			return style_loader_tag( $styles, $html, $handle );
		}
	}
}

W_Fonts::get_instance();
