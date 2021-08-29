<?php

// If plugin - 'RankMath' not exist then return.
if ( ! class_exists( 'RankMath' ) ) {
	return;
}

if ( ! class_exists( 'W_RankMath' ) ) {

	class W_RankMath    {

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
		 * W_RankMath constructor.
		 */
		public function __construct() {
			add_filter( 'rank_math/frontend/breadcrumb/args', [ $this, 'breadcrumb_args' ] );
			add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ), 10 );
			add_action( 'get_footer', [ $this, 'footer_styles' ] );

			add_filter( 'style_loader_tag', [ $this, 'style_loader_tag' ], 10, 2 );
			add_filter( 'script_loader_tag', [ $this, 'script_loader_tag' ], 10, 3 );
		}

		/**
		 * Enqueue scripts and styles.
		 * @return void
		 */
		public function scripts() {
			wp_dequeue_style( "rank-math" );
		}

		/**
		 * @return void
		 */
		public function footer_styles() {
			if ( wp_style_is( "rank-math", 'registered') ) {
				wp_enqueue_style( "rank-math" );
			}
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
				'rank-math' => 'defer',
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
				'rank-math',
			];

			return style_loader_tag( $styles, $html, $handle );
		}

		/**
		 * @param $args
		 *
		 * @return string[]
		 */
		public function breadcrumb_args( $args ) {
			$args = [
				'delimiter'   => '',
				'wrap_before' => '<ol id="crumbs" class="breadcrumbs" aria-label="breadcrumbs">',
				'wrap_after'  => '</ol>',
				'before'      => '<li><span>',
				'after'       => '</span></li>',
			];

			return $args;
		}
	}

	W_RankMath::get_instance();
}
