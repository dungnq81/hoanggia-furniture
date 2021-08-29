<?php
/**
 * WooCommerce Class
 *
 * @author   WEBHD
 * @since    1.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'W_Woocommerce' ) ) {

	class W_Woocommerce {

		/**
		 * W_Woocommerce constructor.
		 */
		public function __construct() {
			add_action( 'after_setup_theme', [ $this, 'woocommerce_setup' ], 31 );
			add_action( 'woocommerce_share', [ $this, 'woocommerce_share' ], 10 );
			add_action( 'woocommerce_email', [ $this, 'woocommerce_email_hooks' ], 100, 1 );
			add_action( 'woocommerce_price_format', [ $this, 'addPriceSuffix' ], 1, 2 );

			// Show only lowest prices in WooCommerce variable products
			add_filter( 'woocommerce_variable_sale_price_html', [$this, 'variation_price_format'], 10, 2 );
			add_filter( 'woocommerce_variable_price_html', [$this, 'variation_price_format'], 10, 2 );

			add_filter( 'woocommerce_output_related_products_args', [ $this, 'related_products_args' ], 20, 1 );
			add_filter( 'woocommerce_breadcrumb_defaults', [ $this, 'wc_breadcrumb_defaults' ], 11, 1 );
			add_filter( 'woocommerce_catalog_orderby', [ $this, 'wc_catalog_orderby' ], 100, 1 );

			add_filter( 'body_class', array( $this, 'woocommerce_body_class' ) );
		}

		// -------------------------------------------------------------

		/**
		 * @param $price
		 * @param $product
		 *
		 * @return string
		 */
		public function variation_price_format( $price, $product ) {

			// Main Price
			$prices = array( $product->get_variation_price( 'min', true ), $product->get_variation_price( 'max', true ) );
			$price = $prices[0] !== $prices[1] ? sprintf( __( 'From: %1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );

			// Sale Price
			$prices = array( $product->get_variation_regular_price( 'min', true ), $product->get_variation_regular_price( 'max', true ) );
			sort( $prices );
			$saleprice = $prices[0] !== $prices[1] ? sprintf( __( 'From: %1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );

			if ( $price !== $saleprice ) {
				$price = '<del>' . $saleprice . $product->get_price_suffix() . '</del> <ins>' . $price . $product->get_price_suffix() . '</ins>';
			}
			return $price;
		}

		// -------------------------------------------------------------

		/**
		 * Add 'woocommerce-active' class to the body tag
		 *
		 * @param  array $classes css classes applied to the body tag.
		 * @return array $classes modified to include 'woocommerce-active' class
		 */
		public function woocommerce_body_class( $classes ) {
			if ( is_woocommerce_activated() ) {
				$classes[] = 'wc-active';
			}

			return $classes;
		}

		/**
		 * @param $format
		 * @param $currency_pos
		 *
		 * @return string
		 */
		public function addPriceSuffix( $format, $currency_pos ) {
			switch ( $currency_pos ) {
				case 'left' :
					$currency = get_woocommerce_currency();
					$format   = '%1$s%2$s&nbsp;<span class="suffix">' . $currency . '</span>';
					break;
			}

			return $format;
		}

		/**
		 * @param $orders
		 *
		 * @return array
		 */
		public function wc_catalog_orderby( $orders ) {
			$orders = array(
				'menu_order' => __( 'Ordering', 'hd' ),
				'popularity' => __( 'Popularity', 'hd' ),
				'rating'     => __( 'Average rating', 'hd' ),
				'date'       => __( 'Latest', 'hd' ),
				'price'      => __( 'Price: low to high', 'hd' ),
				'price-desc' => __( 'Price: high to low', 'hd' ),
			);

			return $orders;
		}

		/**
		 * @param $mailer
		 */
		public function woocommerce_email_hooks( $mailer ) {
			add_action( 'woocommerce_order_status_pending_to_on-hold_notification', array(
				$mailer->emails['WC_Email_Customer_On_Hold_Order'],
				'trigger'
			) );
		}

		/**
		 * @param $defaults
		 *
		 * @return array
		 */
		public function wc_breadcrumb_defaults( $defaults ) {
			$defaults = [
				'delimiter'   => '',
				'wrap_before' => '<ol id="crumbs" class="breadcrumbs" aria-label="breadcrumbs">',
				'wrap_after'  => '</ol>',
				'before'      => '<li><span property="itemListElement" typeof="ListItem">',
				'after'       => '</span></li>',
				'home'        => _x( 'Home', 'breadcrumb', 'hd' ),
			];

			return $defaults;
		}

		/**
		 * @param $args
		 *
		 * @return int[]
		 */
		public function related_products_args( $args ) {
			$args = [
				'columns'        => 4,
				'posts_per_page' => 12,
			];

			return $args;
		}

		/**
		 * woocommerce_share action
		 * @return void
		 */
		public function woocommerce_share() {
			get_template_part('template-parts/parts/sharing');
		}

		/**
		 * Woocommerce setup
		 *
		 * @return void
		 */
		public function woocommerce_setup() {

			// Declare WooCommerce support.
			add_theme_support(
				'woocommerce', apply_filters(
					'w_woocommerce_args', array(
						'product_grid' => array(
							'default_columns' => 3,
							'default_rows'    => 4,
							'min_columns'     => 1,
							'max_columns'     => 6,
							'min_rows'        => 1,
						),
					)
				)
			);

			add_filter(
				'woocommerce_get_image_size_gallery_thumbnail', function( $size ) {
				return array(
					'width'  => 150,
					'height' => 150,
					'crop'   => 0,
				);
			}
			);

			// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).

			// Add support for WC features.
			add_theme_support( 'wc-product-gallery-zoom' );
			add_theme_support( 'wc-product-gallery-lightbox' );
			add_theme_support( 'wc-product-gallery-slider' );

			// Remove default WooCommerce wrappers.
			remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper' );
			remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end' );
			remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar' );
		}
	}
}

return new W_Woocommerce();
