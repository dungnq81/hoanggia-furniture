<?php
/**
 * Global Class
 * @author   WEBHD
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'W' ) ) {

	class W {

		/**
		 * Setup class.
		 */
		public function __construct() {
			add_action( 'after_setup_theme', array( $this, 'setup' ) );
			add_action( 'init', array( $this, 'init' ) );
			add_action( 'widgets_init', array( $this, 'widgets_init' ) );

			add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ), 10 );
			add_action( 'wp_enqueue_scripts', array( $this, 'non_latin_languages' ), 28 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_inline_css' ), 30 ); // After WooCommerce.
			add_action( 'wp_enqueue_scripts', array( $this, 'child_scripts' ), 99 );

			add_filter( 'body_class', array( $this, 'body_classes' ) );
			add_filter( 'post_class', array( $this, 'post_classes' ) );
			add_filter( 'nav_menu_css_class', array( $this, 'nav_menu_css_class' ), 10, 2 );

			add_action( 'login_enqueue_scripts', array( $this, 'login_enqueue_script' ), 30 );
			add_action( 'enqueue_block_editor_assets', array( $this, 'block_editor_assets' ) );
		}

		/**
		 * Sets up theme defaults and registers support for various WordPress features.
		 *
		 * Note that this function is hooked into the after_setup_theme hook, which
		 * runs before the init hook. The init hook is too late for some features, such
		 * as indicating support for post thumbnails.
		 */
		public function setup() {

			/**
			 * Make theme available for translation.
			 * Translations can be filed at WordPress.org.
			 * See: https://translate.wordpress.org/projects/wp-themes/hello-elementor
			 */
			load_theme_textdomain( 'hd', trailingslashit( WP_LANG_DIR ) . 'themes/' );
			load_theme_textdomain( 'hd', get_stylesheet_directory() . '/languages' );
			load_theme_textdomain( 'hd', get_template_directory() . '/languages' );

			/**
			 * Add default posts and comments RSS feed links to head.
			 */
			add_theme_support( 'automatic-feed-links' );

			/*
			 * Let WordPress manage the document title.
			 * This theme does not use a hard-coded <title> tag in the document head,
			 * WordPress will provide it for us.
			 */
			add_theme_support( 'title-tag' );

			/*
			 * Enable support for Post Thumbnails on posts and pages.
			 *
			 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
			 */
			add_theme_support( 'post-thumbnails' );
			set_post_thumbnail_size( 1200, 9999 );

			/*
			 * Add theme support for selective refresh for widgets.
			 */
			add_theme_support( 'customize-selective-refresh-widgets' );

			add_editor_style( get_template_directory_uri() . "/assets/css/editor-style.css" );
			add_post_type_support( 'page', 'excerpt' );

			/*
			 * Switch default core markup for search form, comment form, and comments
			 * to output valid HTML5.
			 */
			add_theme_support(
				'html5', apply_filters(
					'hd_html5_args', array(
						'search-form',
						'comment-form',
						'comment-list',
						'gallery',
						'caption',
						'widgets',
					)
				)
			);

			/**
			 * Add support for core custom logo.
			 *
			 * @link https://codex.wordpress.org/Theme_Logo
			 */
			$logo_height = 100;
			$logo_width  = 240;

			// If the retina setting is active, double the recommended width and height.
			if ( get_theme_mod_ssl( 'retina_logo' ) ) {
				$logo_height = floor( $logo_height * 2 );
				$logo_width  = floor( $logo_width * 2 );
			}

			add_theme_support(
				'custom-logo', apply_filters(
					'hd_custom_logo_args', array(
						'height'      => $logo_height,
						'width'       => $logo_width,
						'flex-height' => true,
						'flex-width'  => true,
						//'unlink-homepage-logo' => true,
					)
				)
			);

			//add_theme_support( 'wp-block-styles' );
			add_theme_support( 'align-wide' );
			add_theme_support( 'responsive-embeds' );

			/*
			 * Remove WordPress core custom header feature.
			 */
			remove_theme_support( 'custom-header' );

			// Set default values for the upload media box
			update_option( 'image_default_align', 'center' );
			update_option( 'image_default_size', 'large' );

			if ( class_exists( 'W_Script_Loader' ) ) {

				// Adds `async` and `defer` support for scripts registered
				// or enqueued by the theme.
				$loader = new W_Script_Loader();
				add_filter( 'script_loader_tag', [ $loader, 'filter_script_loader_tag' ], 10, 2 );
			}

			/**
			 * Register Menus
			 * @link http://codex.wordpress.org/Function_Reference/register_nav_menus#Examples
			 */
			register_nav_menus(
				[
					'main-nav'   => __( 'Primary Menu', 'hd' ),
					'second-nav' => __( 'Secondary Menu', 'hd' ),
					'mobile-nav' => __( 'Handheld Menu', 'hd' ),
					'social-nav' => __( 'Social menu', 'hd' ),
					'policy-nav' => __( 'Privacy Policy menu', 'hd' ),
				]
			);
		}

		/**
		 * Enqueue scripts and styles.
		 * @return void
		 */
		public function scripts() {

			/*dequeue style*/
			wp_dequeue_style( "wp-block-library" );
			wp_dequeue_style( "wp-block-library-theme" );

			/*Theme stylesheet.*/
			wp_register_style( "plugin-style", get_template_directory_uri() . '/assets/css/plugins.css', [], W_THEME_VERSION );
			wp_enqueue_style( "app-style", get_template_directory_uri() . '/assets/css/app.css', [ "plugin-style" ], W_THEME_VERSION );

			// Theme JS
			wp_register_script( "fx", get_template_directory_uri() . '/assets/js/plugins/fx.min.js', [], '6.6.3', true );
			wp_script_add_data( "fx", "defer", true );

			if ( ! wp_script_is( "swiper", 'registered' ) ) {
				wp_register_script( "swiper", get_template_directory_uri() . '/assets/js/plugins/swiper.min.js', [], '5.3.6', true );
				wp_script_add_data( "swiper", "defer", true );
			}

			wp_register_script( "helpers", get_template_directory_uri() . '/assets/js/helpers.js', ["jquery"], W_THEME_VERSION, true );
			wp_script_add_data( "helpers", "defer", true );

			/*extra scripts*/
			wp_enqueue_script( "backtop", get_template_directory_uri() . "/assets/js/plugins/backtop.min.js", [], false, true );
			wp_enqueue_script( "shares", get_template_directory_uri() . "/assets/js/plugins/shares.min.js", [ "jquery" ], false, true );

			// app.js
			wp_enqueue_script( "app", get_template_directory_uri() . "/assets/js/app.js", [
				"jquery",
				"helpers",
				"swiper",
				"fx",
			], W_THEME_VERSION, true );
			wp_script_add_data( "app", "defer", true );

			wp_register_style( 'inline-style', false );
			wp_enqueue_style( 'inline-style' );

			// inline js
			$l10n = [
				'base_url'  => trailingslashit( esc_url( site_url() ) ),
				'theme_url' => trailingslashit( esc_url( get_template_directory_uri() ) ),
				'locale'    => get_f_locale(),
				'lang'      => get_lang(),
			];

			$fb_appid  = get_theme_mod_ssl( 'fb_menu_layout' );
			$fb_pageid = get_theme_mod_ssl( 'fbpage_menu_layout' );
			$fb_pixel = get_theme_mod_ssl( 'fbpixel_menu_layout' );
			$tiktok = get_theme_mod_ssl( 'tiktok_menu_layout' );
			$gtm = get_theme_mod_ssl( 'gmt_menu_layout' );
			$zalo_appid = get_theme_mod_ssl( 'zalo_menu_layout' );
			$zalo_oaid    = get_theme_mod_ssl( 'zalo_oa_menu_setting' );

			if ( $fb_appid ) $l10n['fb_appid'] = $fb_appid;
			if ( $fb_pageid ) $l10n['fb_pageid'] = $fb_pageid;
			if ( $fb_pixel ) $l10n['fb_pixel'] = $fb_pixel;
			if ( $tiktok ) $l10n['tiktok'] = $tiktok;
			if ( $gtm ) $l10n['gtm'] = $gtm;
			if ( $zalo_appid ) $l10n['zalo_appid'] = $zalo_appid;
			if ( $zalo_oaid ) $l10n['zalo_oaid'] = $zalo_oaid;

			wp_localize_script( 'jquery-core', 'HD', $l10n );

			/*comments*/
			if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
				wp_enqueue_script( 'comment-reply' );
			}
		}

		/**
		 * Enqueue child theme stylesheet.
		 * A separate function is required as the child theme css needs to be enqueued _after_ the parent theme
		 * primary css and the separate WooCommerce css.
		 */
		public function child_scripts() {
			if ( is_child_theme() ) {
				$child_theme = wp_get_theme( get_stylesheet() );
				wp_enqueue_style( 'w-child-style', get_stylesheet_uri(), array(), $child_theme->get( 'Version' ) );
			}
		}

		/**
		 * Launching operation cleanup.
		 */
		public function init() {

			// Xóa widget mặc định "Welcome to WordPress".
			remove_action( 'welcome_panel', 'wp_welcome_panel' );

			// wp_head
			remove_action( 'wp_head', 'rsd_link' ); // Remove the EditURI/RSD link
			remove_action( 'wp_head', 'wlwmanifest_link' ); // Remove Windows Live Writer Manifest link
			remove_action( 'wp_head', 'wp_shortlink_wp_head' ); // Remove the shortlink
			remove_action( 'wp_head', 'wp_generator' ); // remove WordPress Generator
			remove_action( 'wp_head', 'feed_links_extra', 3 ); //remove comments feed.
			remove_action( 'wp_head', 'adjacent_posts_rel_link' ); // Remove relational links for the posts adjacent to the current post.
			remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' ); // Remove prev and next links
			remove_action( 'wp_head', 'parent_post_rel_link' );
			remove_action( 'wp_head', 'start_post_rel_link' );
			remove_action( 'wp_head', 'index_rel_link' );
			remove_action( 'wp_head', 'feed_links', 2 );

			/**
			 * Remove wp-json header from WordPress
			 * Note that the REST API functionality will still be working as it used to;
			 * this only removes the header code that is being inserted.
			 */
			remove_action( 'wp_head', 'rest_output_link_wp_head' );
			remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

			// all actions related to emojis
			remove_action( 'wp_print_styles', 'print_emoji_styles' );
			remove_action( 'admin_print_styles', 'print_emoji_styles' );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );

			// Emoji detection script.
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );

			// staticize_emoji
			remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
			remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
			remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		}

		/**
		 * Register widget area.
		 *
		 * @link https://codex.wordpress.org/Function_Reference/register_sidebar
		 */
		public function widgets_init() {

			// homepage
			register_sidebar(
				[
					'container'     => false,
					'id'            => 'w-homepage-sidebar',
					'name'          => __( 'W - Homepage', 'hd' ),
					'description'   => __( 'Widgets added here will appear in homepage.', 'hd' ),
					'before_widget' => '<div class="%2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<h4 class="heading-title">',
					'after_title'   => '</h4>',
				]
			);

			// top header
			register_sidebar(
				[
					'container'     => false,
					'id'            => 'w-topheader-sidebar',
					'name'          => __( 'TopHeader', 'hd' ),
					'description'   => __( 'Widgets added here will appear in top header.', 'hd' ),
					'before_widget' => '<div class="header-widgets %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<span>',
					'after_title'   => '</span>',
				]
			);

			// header
			register_sidebar(
				[
					'container'     => false,
					'id'            => 'w-header-sidebar',
					'name'          => __( 'Header', 'hd' ),
					'description'   => __( 'Widgets added here will appear in header.', 'hd' ),
					'before_widget' => '<div class="header-widgets %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<span>',
					'after_title'   => '</span>',
				]
			);

			// sidebar product
			register_sidebar(
				[
					'container'     => false,
					'id'            => 'w-product-sidebar',
					'name'          => __( 'Sidebar Products', 'hd' ),
					'description'   => __( 'Widgets added here will appear in products category.', 'hd' ),
					'before_widget' => '<div class="%2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<span>',
					'after_title'   => '</span>',
				]
			);

			// footer columns
			$sidebar_args = [];

			$rows    = (int) get_theme_mod_ssl( 'footer_row_setting' );
			$regions = (int) get_theme_mod_ssl( 'footer_col_setting' );
			for ( $row = 1; $row <= $rows; $row ++ ) {
				for ( $region = 1; $region <= $regions; $region ++ ) {
					$footer_n = $region + $regions * ( $row - 1 ); // Defines footer sidebar ID.
					$footer   = sprintf( 'footer_%d', $footer_n );
					if ( 1 === $rows ) {

						/* translators: 1: column number */
						$footer_region_name = sprintf( __( 'Footer Column %1$d', 'hd' ), $region );

						/* translators: 1: column number */
						$footer_region_description = sprintf( __( 'Widgets added here will appear in column %1$d of the footer.', 'hd' ), $region );
					} else {

						/* translators: 1: row number, 2: column number */
						$footer_region_name = sprintf( __( 'Footer Row %1$d - Column %2$d', 'hd' ), $row, $region );

						/* translators: 1: column number, 2: row number */
						$footer_region_description = sprintf( __( 'Widgets added here will appear in column %1$d of footer row %2$d.', 'hd' ), $region, $row );
					}

					$sidebar_args[ $footer ] = [
						'name'        => $footer_region_name,
						'id'          => sprintf( 'w-footer-%d', $footer_n ),
						'description' => $footer_region_description,
					];
				}
			}

			foreach ( $sidebar_args as $args ) {
				$widget_tags = [
					'container'     => false,
					'before_widget' => '<div class="widget %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<h5 class="widget-title">',
					'after_title'   => '</h5>',
				];

				if ( is_array( $widget_tags ) ) {
					register_sidebar( $args + $widget_tags );
				}
			}
		}

		/**
		 * Adds custom classes to the array of body classes.
		 *
		 * @param array $classes Classes for the body element.
		 *
		 * @return array
		 */
		public function body_classes( $classes ) {
			global $post;

			$_elementor = null;
			if ( is_elementor_activated() && class_exists( 'ACF' ) ) {
				$_elementor = get_field( 'load_elementor', $post->ID );
			}

			// Check whether we're in the customizer preview.
			if ( is_customize_preview() ) {
				$classes[] = 'customizer-preview';
			}

			foreach ( $classes as $class ) {
				if ( str_contains( $class, 'page-template-templates' )
				     || str_contains( $class, 'page-template-templatespage-homepage-php' )
				     || str_contains( $class, 'wp-custom-logo' )
				) {
					$classes = array_diff( $classes, [ $class ] );
				}

				if ( true !== $_elementor
				     && is_elementor_activated()
				     && ! \Elementor\Plugin::$instance->editor->is_edit_mode()
				     && ! \Elementor\Plugin::$instance->preview->is_preview_mode()
				     && str_contains( $class, 'elementor-' )
				) {
					$classes = array_diff( $classes, [ $class ] );
				}
			}

			// dark mode func
			$classes[] = 'light-mode';
			return $classes;
		}

		/**
		 * Adds custom classes to the array of post classes.
		 *
		 * @param array $classes Classes for the post element.
		 *
		 * @return array
		 */
		public function post_classes( $classes ) {

			// remove_sticky_class
			if ( in_array( 'sticky', $classes ) ) {
				$classes   = array_diff( $classes, [ "sticky" ] );
				$classes[] = 'wp-sticky';
			}

			// remove tag-, category- classes
			foreach ( $classes as $class ) {
				if ( str_contains( $class, 'tag-' )
				     || str_contains( $class, 'category-' )
				     || str_contains( $class, 'video_category-' )
				     || str_contains( $class, 'project_category-' )
				     || str_contains( $class, 'product_category-' )
				     || str_contains( $class, 'product_cat-' )
				     || str_contains( $class, 'gallery_category-' )
				     || str_contains( $class, 'service_category-' )
				     || str_contains( $class, 'video_tag-' )
				     || str_contains( $class, 'project_tag-' )
				     || str_contains( $class, 'product_tag-' )
				     || str_contains( $class, 'gallery_tag-' )
				     || str_contains( $class, 'service_tag-' )
				) {
					$classes = array_diff( $classes, [ $class ] );
				}
			}

			return $classes;
		}

		/**
		 * Add Foundation 'is-active' class for the current menu item.
		 *
		 * @param $classes
		 * @param $item
		 *
		 * @return array
		 */
		public function nav_menu_css_class( $classes, $item ) {
			if ( ! is_array( $classes ) ) {
				$classes = [];
			}
			// remove menu-item-type-, menu-item-object- classes
			foreach ( $classes as $class ) {
				if ( false !== strpos( $class, 'menu-item-type-' )
				     || false !== strpos( $class, 'menu-item-object-' )
				) {
					$classes = array_diff( $classes, [ $class ] );
				}
			}
			if ( 1 == $item->current
			     || true == $item->current_item_ancestor
			     || true == $item->current_item_parent
			) {
				//$classes[] = 'is-active';
				$classes[] = 'active';
			}

			return $classes;
		}

		/**
		 * Customize login page
		 */
		public function login_enqueue_script() {
			wp_enqueue_style( "login-style", get_template_directory_uri() . "/assets/css/admin.css", [], W_THEME_VERSION );
			wp_enqueue_script( "login", get_template_directory_uri() . "/assets/js/admin.js", [ "jquery" ], W_THEME_VERSION, true );

			// custom script/style
			$logo    = get_theme_file_uri( "/assets/img/logo.png" );
			$logo_bg = get_theme_file_uri( "/assets/img/login-bg.jpg" );

			$css = new W_CSS();
			if ( $logo_bg ) {
				$css->set_selector( 'body.login' );
				$css->add_property( 'background-image', 'url(' . $logo_bg . ')' );
			}
			if ( $logo ) {
				$css->set_selector( 'body.login #login h1 a' );
				$css->add_property( 'background-image', 'url(' . $logo . ')' );
			}

			if ( $css->css_output() ) {
				wp_add_inline_style( 'login-style', $css->css_output() );
			}
		}

		/**
		 * Gutenberg editor
		 * @return void
		 */
		public function block_editor_assets() {
			//wp_enqueue_style( "gfont-montserrat", 'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap' );
			wp_enqueue_style( 'w-editor-style', get_template_directory_uri() . "/assets/css/editor-style.css" );
		}

		/**
		 * Add CSS for third-party plugins.
		 * @return void
		 */
		public function enqueue_inline_css() {
			$css = new W_CSS();

			// footer bg
			$_footer_bg = get_theme_mod_ssl( 'footer_bg_setting' );
			if ( $_footer_bg ) {
				$css->set_selector( 'footer#colophon::before' );
				$css->add_property( 'background-image', 'url(' . $_footer_bg . ')' );
			}

			// breadcrumbs bg
			$breadcrumb_bg = get_theme_mod_ssl( 'breadcrumb_bg_setting' );
			if ( $breadcrumb_bg ) {
				$css->set_selector( '.section.title-nav-section::before' );
				$css->add_property( 'background-image', 'url(' . $breadcrumb_bg . ')' );
			}

			if ( $css->css_output() ) {
				wp_add_inline_style( 'inline-style', $css->css_output() );
			}
		}

		/**
		 * Enqueue non-latin language styles
		 * @return void
		 */
		public function non_latin_languages() {
			$custom_css = $this->_get_non_latin_css();
			if ( $custom_css ) {
				wp_add_inline_style( 'inline-style', $custom_css );
			}
		}

		/**
		 * @param string $type
		 *
		 * @return string
		 */
		private function _get_non_latin_css( string $type = 'front-end' ) {

			// Fetch site locale.
			$locale = get_bloginfo( 'language' );

			// Define fallback fonts for non-latin languages.
			$font_family = apply_filters(
				'w_get_localized_font_family_types',
				array(
					// Chinese Simplified (China) - Noto Sans SC.
					'zh-CN' => array(
						'\'PingFang SC\'',
						'\'Helvetica Neue\'',
						'\'Microsoft YaHei New\'',
						'\'STHeiti Light\'',
						'sans-serif'
					),

					// Chinese Traditional (Taiwan) - Noto Sans TC.
					'zh-TW' => array(
						'\'PingFang TC\'',
						'\'Helvetica Neue\'',
						'\'Microsoft YaHei New\'',
						'\'STHeiti Light\'',
						'sans-serif'
					),

					// Chinese (Hong Kong) - Noto Sans HK.
					'zh-HK' => array(
						'\'PingFang HK\'',
						'\'Helvetica Neue\'',
						'\'Microsoft YaHei New\'',
						'\'STHeiti Light\'',
						'sans-serif'
					),

					// Korean.
					'ko-KR' => array(
						'\'Apple SD Gothic Neo\'',
						'\'Malgun Gothic\'',
						'\'Nanum Gothic\'',
						'Dotum',
						'sans-serif'
					),

					// Thai.
					'th'    => array( '\'Sukhumvit Set\'', '\'Helvetica Neue\'', 'Helvetica', 'Arial', 'sans-serif' ),
				)
			);

			// Return if the selected language has no fallback fonts.
			if ( empty( $font_family[ $locale ] ) ) {
				return '';
			}

			// Define elements to apply fallback fonts to.
			$elements = apply_filters(
				'w_get_localized_font_family_elements',
				array(
					'front-end' => array(
						'body',
						'input',
						'textarea',
						'button',
						'.button',
						'.faux-button',
						'.wp-block-button__link',
						'.wp-block-file__button',
						'.has-drop-cap:not(:focus)::first-letter',
						'.has-drop-cap:not(:focus)::first-letter',
						'.entry-content .wp-block-archives',
						'.entry-content .wp-block-categories',
						'.entry-content .wp-block-cover-image',
						'.entry-content .wp-block-latest-comments',
						'.entry-content .wp-block-latest-posts',
						'.entry-content .wp-block-pullquote',
						'.entry-content .wp-block-quote.is-large',
						'.entry-content .wp-block-quote.is-style-large',
						'.entry-content .wp-block-archives *',
						'.entry-content .wp-block-categories *',
						'.entry-content .wp-block-latest-posts *',
						'.entry-content .wp-block-latest-comments *',
						'.entry-content p',
						'.entry-content ol',
						'.entry-content ul',
						'.entry-content dl',
						'.entry-content dt',
						'.entry-content cite',
						'.entry-content figcaption',
						'.entry-content .wp-caption-text',
						'.comment-content p',
						'.comment-content ol',
						'.comment-content ul',
						'.comment-content dl',
						'.comment-content dt',
						'.comment-content cite',
						'.comment-content figcaption',
						'.comment-content .wp-caption-text',
						'.widget_text p',
						'.widget_text ol',
						'.widget_text ul',
						'.widget_text dl',
						'.widget_text dt',
						'.widget-content .rssSummary',
						'.widget-content cite',
						'.widget-content figcaption',
						'.widget-content .wp-caption-text'
					),
				)
			);

			// Return if the specified type doesn't exist.
			if ( empty( $elements[ $type ] ) ) {
				return '';
			}

			// Return the specified styles.
			$css = new W_CSS();
			$css->set_selector( implode( ',', $elements[ $type ] ) );
			$css->add_property( 'font-family', implode( ',', $font_family[ $locale ] ) );

			return $css->css_output();
		}
	}
}

return new W();
