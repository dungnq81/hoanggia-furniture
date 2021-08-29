<?php
/**
 * Elementor Compatibility File.
 *
 * @package HD
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'W_Elementor_Pro' ) ) {

	class W_Elementor_Pro {

		/**
		 * W_Elementor_Pro constructor.
		 */
		public function __construct() {

			// load google fonts later
			add_filter( 'elementor/frontend/print_google_fonts', '__return_false' );
			add_action( 'elementor/theme/register_locations', array( $this, 'register_locations' ) );

			add_action( "wp_enqueue_scripts", array( $this, 'enqueue' ), 31 );
			add_filter( 'style_loader_tag', [ $this, 'style_loader_tag' ], 10, 2 );
			add_filter( 'script_loader_tag', [ $this, 'script_loader_tag' ], 10, 3 );

			add_filter( 'w_elementor_page_title', array( $this, 'check_hide_title' ), 10, 1 );

			// Override post meta.
			//add_action( 'wp', array( $this, 'override_meta' ), 0 );
		}

		/**
		 * Elementor Enqueue styles and scripts
		 */
		public function enqueue() {

			// load awesome font later
			wp_deregister_style( "elementor-icons-fa-solid" );
			wp_deregister_style( "elementor-icons-fa-regular" );
			wp_deregister_style( "elementor-icons-fa-brands" );

			if ( $this->check_singular_dequeue() ) {
				$this->dequeue_all();
			} else {
				wp_enqueue_style( "elementor-style", get_template_directory_uri() . '/assets/css/elementor.css', [ "app-style" ], W_THEME_VERSION );
			}

			if ( is_archive() && ! elementor_theme_do_location( 'archive' ) ) {
				$this->dequeue_all();
			}
		}

		/**
		 * @return bool
		 */
		public function check_singular_dequeue($post = null) {
			if ( is_singular() && is_elementor_activated() && class_exists( 'ACF' ) ) {
				$post = get_post($post);
				$_elementor = get_field( 'load_elementor', $post->ID );
				if ( true !== $_elementor
				     && ! \Elementor\Plugin::$instance->editor->is_edit_mode()
				     && ! \Elementor\Plugin::$instance->preview->is_preview_mode()
				) {
					return true;
				}
			}

			return false;
		}

		/**
		 * dequeue_all elementor style and script
		 */
		public function dequeue_all() {
			wp_dequeue_style( "elementor-common" );
			wp_dequeue_style( "elementor-icons" );
			wp_dequeue_script( "elementor-app-loader" );
			wp_dequeue_script( "elementor-common" );
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
				'pro-elements-handlers'         => 'defer',
				'elementor-waypoints'           => 'defer',
				'elementor-pro-frontend'        => 'defer',
				'elementor-pro-webpack-runtime' => 'defer',
				'elementor-app-loader'          => 'defer',
				'jquery-ui-draggable'           => 'defer',
				'jquery-ui-mouse'               => 'defer',
				'jquery-ui-core'                => 'defer',
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
				'elementor-post-',
				'elementor-icons',
				'elementor-pro',
			];

			return style_loader_tag( $styles, $html, $handle );
		}

		/**
		 * Register Elementor Locations.
		 *
		 * @param object $manager Location manager.
		 * @return void
		 */
		public function register_locations( $manager ) {
			$manager->register_all_core_location();
		}

		/**
		 * @param $val
		 *
		 * @return false|mixed
		 */
		public function check_hide_title( $val ) {
			$current_doc = \Elementor\Plugin::instance()->documents->get( get_the_ID() );
			if ( $current_doc && 'yes' === $current_doc->get_settings( 'hide_title' ) ) {
				$val = false;
			}

			return $val;
		}

		/**
		 * Override sidebar, title etc with post meta
		 *
		 * @return bool|void
		 */
		public function override_meta() {

			// don't override meta for `elementor_library` post type.
			if ( 'elementor_library' == get_post_type() ) {
				return;
			}

			//...

			return true;
		}

		/**
		 * Check is elementor activated.
		 *
		 * @param int $id Post/Page Id.
		 * @return boolean
		 */
		public function is_elementor_activated( $id ) {
			return \Elementor\Plugin::$instance->documents->get( $id )->is_built_with_elementor();
		}

		/**
		 * Check if Elementor Editor is open.
		 *
		 * @since  1.2.7
		 *
		 * @return boolean True IF Elementor Editor is loaded, False If Elementor Editor is not loaded.
		 */
		public function is_elementor_editor() {
			if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return true;
			}

			return false;
		}
	}
}

return new W_Elementor_Pro();
