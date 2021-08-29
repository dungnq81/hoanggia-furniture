<?php
/**
 * Admin Class
 * @author   WEBHD
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! is_admin() ) {
	return;
}

if ( ! class_exists( 'W_Admin' ) ) {

	class W_Admin {

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
		 * W_Admin constructor.
		 */
		public function __construct() {

			/*remove admin wp version*/
			if ( ! WP_DEBUG ) {
				add_filter( 'update_footer', '__return_empty_string', 11 );
			}

			add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ) );
			//add_filter( 'display_post_states', array( $this, 'display_post_states' ), 10, 2 );
			add_filter( 'site_transient_update_plugins', array( $this, 'filter_plugin_updates' ) );

			/*dashboard widgets*/
			add_action( 'admin_menu', array( $this, 'dashboard_meta_box' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 31 );
			//add_action( 'admin_head', array( $this, 'select2_inline' ) );

			add_action( 'admin_init', array( $this, 'admin_init' ), 1 );

			// Disables the block editor from managing widgets in the Gutenberg plugin.
			add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );

			// Disables the block editor from managing widgets.
			add_filter( 'use_widgets_block_editor', '__return_false' );

			// Use Classic Editor - Disable Gutenberg Editor
			add_filter( 'use_block_editor_for_post_type', '__return_false' );
		}

		// -------------------------------------------------------------

		/**
		 * Add admin column
		 */
		public function admin_init() {

			// Add customize column taxonomy
			// https://wordpress.stackexchange.com/questions/77532/how-to-add-the-category-id-to-admin-page
			$taxonomy_arr = [
				'category',
				'post_tag',
				//'video_category',
				//'video_tag',
				'banner_category',
				'product_category',
				'product_cat',
				'product_tag',
			];
			foreach ( $taxonomy_arr as $taxonomy ) {
				add_filter( "{$taxonomy}_row_actions", array( $this, 'tax_action_links' ), 10, 2 );
			}

			// customize row_actions
			$actions_arr = [
				'user',
				'post',
				'page',
				'product',
			];
			foreach ( $actions_arr as $action ) {
				add_filter( "{$action}_row_actions", array( $this, 'post_action_links' ), 10, 2 );
			}

			// thumb post
			add_filter( 'manage_posts_columns', array( $this, 'post_header' ), 11, 1 );
			add_filter( 'manage_posts_custom_column', array( $this, 'post_column' ), 11, 3 );

			// thumb page
			add_filter( 'manage_pages_columns', array( $this, 'post_header' ), 5, 1 );
			add_filter( 'manage_pages_custom_column', array( $this, 'post_column' ), 5, 2 );

			// thumb tax
			$thumb_taxs = [
				'category',
				//'video_category',
				'banner_category',
				'product_category',
			];
			foreach ( $thumb_taxs as $taxonomy ) {
				add_filter( "manage_edit-{$taxonomy}_columns", array( $this, 'tax_header' ), 11, 1 );
				add_filter( "manage_{$taxonomy}_custom_column", array( $this, 'tax_column' ), 11, 3 );
			}

			// exclude thumb post column
			$exclude_thumb_posts = [
				//'product',
				'wpcf7_contact_form',
			];

			foreach ( $exclude_thumb_posts as $_exclude ) {
				add_filter( "manage_{$_exclude}_posts_columns", array( $this, 'post_exclude_header' ), 12, 1 );
			}
		}

		// -------------------------------------------------------------

		/**
		 * @param $columns
		 *
		 * @return mixed
		 */
		public function post_exclude_header( $columns ) {
			unset( $columns['post_thumb'] );

			return $columns;
		}

		/**
		 * @param $value
		 * @param $name
		 * @param $id
		 */
		public function tax_column( $value, $name, $id ) {
			switch ( $name ) {
				case 'tax_thumb':
					echo custom_tax_thumb_by( $id, $name, "thumbnail", true );
					break;
			}
		}

		/**
		 * @param $columns
		 *
		 * @return array
		 */
		public function tax_header( $columns ) {
			$in = [
				"tax_thumb" => sprintf( '<span class="wc-image tips">%1$s</span>', __( "Thumb", 'hd' ) ),
			];

			$columns = array_push_after( $columns, $in, 0 );

			return $columns;
		}

		/**
		 * @param $name
		 * @param $id
		 */
		public function post_column( $name, $id ) {
			switch ( $name ) {
				case 'post_thumb':
					$post_type = get_post_type( $id );
					if ( ! in_array( $post_type, [ 'video' ] ) ) {
						echo get_the_post_thumb( 'thumbnail' );
					} else if ( 'video' == $post_type ) {
						$post_thumbnail_id = get_post_thumbnail_id( $id );
						if ( ! $post_thumbnail_id && $url = get_field( 'url', $id ) ) {
							$img_src = youtube_image( esc_url( $url ), [ 'default' ] );
							echo "<img alt=\"" . title_attribute() . "\" src=\"" . $img_src . "\" />";
						} else {
							echo get_the_post_thumb( 'thumbnail' );
						}
					}

					break;
			}
		}

		/**
		 * @param $columns
		 *
		 * @return array
		 */
		public function post_header( $columns ) {
			$in = [
				"post_thumb" => sprintf( '<span class="wc-image tips">%1$s</span>', __( "Thumb", 'hd' ) ),
			];

			$columns = array_push_after( $columns, $in, 0 );
			return $columns;
		}

		/**
		 * @param $actions
		 * @param $_object
		 *
		 * @return mixed
		 */
		public function post_action_links( $actions, $_object ) {
			//if ( 'product' != $_object->post_type ) {
				array_unshift_assoc( $actions, 'action_id', 'Id:' . $_object->ID );
			//}

			return $actions;
		}

		/**
		 * @param $actions
		 * @param $_object
		 *
		 * @return mixed
		 */
		public function tax_action_links( $actions, $_object ) {
			array_unshift_assoc( $actions, 'action_id', 'Id: ' . $_object->term_id );

			return $actions;
		}

		/**
		 * @return void
		 */
		public function select2_inline() {
			?>
			<style>.select2-container--default .select2-selection--multiple .select2-selection__rendered li {margin-bottom: 0;}</style>
			<script>
				!function ($) {
					$(function () {
						$(".select2").select2({width: "100%";minimumResultsForSearch: Infinity});
					});
				}(jQuery);
			</script>
			<?php
		}

		/**
		 * @return void
		 */
		public function admin_enqueue_scripts() {
			wp_enqueue_style( "admin-style", get_template_directory_uri() . "/assets/css/admin.css", [], W_THEME_VERSION );
			//wp_register_script( "select2", get_template_directory_uri() . "/assets/js/plugins/select2.min.js", [], false, true );
			wp_enqueue_script( "admin", get_template_directory_uri() . "/assets/js/admin.js", [ "jquery" ], W_THEME_VERSION, true );
		}

		/**
		 * @param $value
		 *
		 * @return mixed
		 */
		public function filter_plugin_updates( $value ) {
			//unset( $value->response['advanced-custom-fields-pro/acf.php'] );
			//unset( $value->response['seo-by-rank-math-pro/rank-math-pro.php'] );

			return $value;
		}

		/**
		 * This always shows the current post status in the labels.
		 *
		 * @param array $states current states.
		 * @param WP_Post $post current post object.
		 *
		 * @return array
		 */
		public function display_post_states( $states, $post ) {

			/* Receive the post status object by post status name */
			$post_status_object = get_post_status_object( $post->post_status );

			/* Checks if the label exists */
			if ( in_array( $post_status_object->label, $states, true ) ) {
				return $states;
			}

			/* Adds the label of the current post status */
			//$states[ $post_status_object->name ] = $post_status_object->label;

			// video
			if ( 'video' == $post->post_name ) {
				$states[ $post_status_object->name ] = __( 'Video Page', 'hd' );
			}

			return $states;
		}

		/**
		 * Remove dashboard widgets
		 *
		 * @return void
		 */
		public function dashboard_meta_box() {

			/*Incoming Links Widget*/
			remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'core' );

			/*Remove WordPress Events and News*/
			remove_meta_box( 'dashboard_primary', 'dashboard', 'core' );
			remove_meta_box( 'dashboard_secondary', 'dashboard', 'core' );
		}

		/**
		 * @return void
		 */
		public function admin_footer_text() {
			printf( '<span id="footer-thankyou">%1$s <a href="https://webhd.vn/" target="_blank">Webhd</a>.&nbsp;</span>', __( 'Powered by', 'hd' ) );
		}
	}
}

W_Admin::get_instance();
