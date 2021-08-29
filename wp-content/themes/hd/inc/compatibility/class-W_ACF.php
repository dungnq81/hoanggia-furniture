<?php

// If plugin - 'ACF' not exist then return.
if ( ! class_exists( 'ACF' ) ) {
	return;
}

if ( ! class_exists( 'W_ACF' ) ) {

	class W_ACF {

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
		 * W_ACF constructor.
		 */
		public function __construct() {
			add_filter( 'acf/fields/wysiwyg/toolbars', [ $this, 'wysiwyg_toolbars' ] );
			//add_filter( 'pre_option_acf_pro_license', [ $this, 'acf_pro_license' ], 10, 3 );
		}

		/**
		 * @param $value
		 * @param $option
		 * @param $default
		 *
		 * @return string
		 */
		public function acf_pro_license( $value, $option, $default ) {
			return base64_encode(
				serialize(
					[
						'key' => '...',
						'url' =>  home_url(),
					]
				)
			);
		}

		/**
		 * @param $toolbars
		 *
		 * @return mixed
		 */
		public function wysiwyg_toolbars( $toolbars ) {

			// Add a new toolbar called "W - Simple" - this toolbar has only 1 row of buttons
			$toolbars['W - Simple']    = array();
			$toolbars['W - Simple'][1] = array(
				'formatselect',
				'bold',
				'italic',
				'underline',
				'link',
				'unlink',
				'forecolor',
				'blockquote'
			);

			// remove the 'Basic' toolbar completely (if you want)
			//unset( $toolbars['Full' ] );
			//unset( $toolbars['Basic' ] );

			// return $toolbars - IMPORTANT!
			return $toolbars;
		}
	}
}

W_ACF::get_instance();
