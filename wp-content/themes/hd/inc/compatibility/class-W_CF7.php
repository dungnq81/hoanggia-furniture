<?php

// If plugin - 'WPCF7' not exist then return.
if ( ! class_exists( 'WPCF7' ) ) {
	return;
}

if ( ! class_exists( 'W_CF7' ) ) {

	class W_CF7 {

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
		 * W_CF7 constructor.
		 */
		public function __construct() {

			// remove <p> and <br> contact form 7 plugin
			add_filter( 'wpcf7_autop_or_not', '__return_false' );

			add_filter( 'wpcf7_form_tag', [ $this, 'dynamic_select_list' ], 10, 1 );
			add_action( "wp_enqueue_scripts", [ $this, 'enqueue' ], 999 );
			add_action( 'get_footer', [ $this, 'footer_styles' ] );

			add_filter( 'style_loader_tag', [ $this, 'delay_style_loader_tag' ], 10, 2 );
			add_filter( 'script_loader_tag', [ $this, 'delay_script_loader_tag' ], 10, 3 );
		}

		/**
		 * Dequeue CF7 css
		 */
		public function enqueue() {
			wp_dequeue_style( "contact-form-7" );
		}

		/**
		 * @return void
		 */
		public function footer_styles() {
			if ( wp_style_is( "contact-form-7", 'registered') ) {
				wp_enqueue_style( "contact-form-7" );
			}
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
				'contact-form-7' => 'defer',
			];

			return script_loader_tag( $str_parsed, $tag, $handle, $src );
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
				'contact-form-7',
			];

			return style_loader_tag( $styles, $html, $handle );
		}

		/**
		 * Dynamic Select List for Contact Form 7
		 *
		 * @usage [select name taxonomy:{$taxonomy} ...]
		 * @param array $tag
		 *
		 * @return array $tag
		 */
		public function dynamic_select_list( $tag  ) {

			// Only run on select lists
			if ( 'select' !== $tag['type'] && ( 'select*' !== $tag['type'] ) ) {
				return $tag;
			} else if ( empty( $tag['options'] ) ) {
				return $tag;
			}
			$term_args = array();

			// Loop options to look for our custom options
			foreach ( $tag['options'] as $option ) {
				$matches = explode( ':', $option );
				if ( ! empty( $matches ) ) {
					switch ( $matches[0] ) {
						case 'taxonomy':
							$term_args['taxonomy'] = $matches[1];
							break;
						case 'parent':
							$term_args['parent'] = intval( $matches[1] );
							break;
					}
				}
			}

			// Ensure we have a term arguments to work with
			if ( empty( $term_args ) ) {
				return $tag;
			}

			// Merge dynamic arguments with static arguments
			$term_args = array_merge( $term_args, array(
				'hide_empty'   => false,
				'hierarchical' => 1,
			) );
			$terms     = get_terms( $term_args );

			// Add terms to values
			if ( ! empty( $terms ) && ! is_wp_error( $term_args ) ) {
				foreach ( $terms as $term ) {
					$tag['values'][] = $term->name;
				}
			}

			return $tag;
		}
	}
}

W_CF7::get_instance();
