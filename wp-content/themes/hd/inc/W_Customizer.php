<?php
/**
 * Customize Class
 * @author   WEBHD
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'W_Customize' ) ) {

	/**
	 * CUSTOMIZER SETTINGS
	 */
	class W_Customize {

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
		 * W_Customize constructor.
		 */
		public function __construct() {

			// Setup the Theme Customizer settings and controls.
			add_action( 'customize_register', [ $this, 'register' ], 30 );
		}

		/**
		 * Register customizer options.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 */
		public function register( $wp_customize ) {

			/* 2X Header Logo ---------------- */
			$wp_customize->add_setting(
				'retina_logo',
				array(
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => array( $this, 'sanitize_checkbox' ),
					'transport'         => 'postMessage',
				)
			);

			$wp_customize->add_control(
				'retina_logo',
				array(
					'type'        => 'checkbox',
					'section'     => 'title_tagline',
					'priority'    => 11,
					'label'       => __( 'Retina logo', 'hd' ),
					'description' => __( 'Scales the logo to half its uploaded size, making it sharp on high-res screens.', 'hd' ),
				)
			);

			// logo mobile
			$wp_customize->add_setting( 'logo_mobile' );
			$wp_customize->add_control(
				new WP_Customize_Image_Control(
					$wp_customize,
					'logo_mobile',
					[
						'label'    => __( 'Mobile Logo', 'hd' ),
						'section'  => 'title_tagline',
						'settings' => 'logo_mobile',
						'priority' => 8,
					]
				)
			);

			// -------------------------------------------------------------
			// -------------------------------------------------------------

			// Create custom panels
			$wp_customize->add_panel(
				'addon_menu_settings',
				[
					'priority'       => 140,
					'theme_supports' => '',
					'title'          => __( 'HD', 'hd' ),
					'description'    => __( 'Controls the add-on menu', 'hd' ),
				]
			);

			// -------------------------------------------------------------
			// -------------------------------------------------------------

			// Create offcanvas section
			$wp_customize->add_section(
				'offcanvas_menu_section',
				[
					'title'    => __( 'offCanvas Menu', 'hd' ),
					'panel'    => 'addon_menu_settings',
					'priority' => 1000,
				]
			);

			// add offcanvas control
			$wp_customize->add_setting(
				'offcanvas_menu_setting',
				[
					'default'           => 'default',
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'refresh',
				]
			);
			$wp_customize->add_control(
				'offcanvas_menu_control',
				[
					'label'    => __( 'offCanvas position', 'hd' ),
					'type'     => 'radio',
					'section'  => 'offcanvas_menu_section',
					'settings' => 'offcanvas_menu_setting',
					'choices'  => [
						'left'    => __( 'Left', 'hd' ),
						'right'   => __( 'Right', 'hd' ),
						'top'     => __( 'Top', 'hd' ),
						'bottom'  => __( 'Bottom', 'hd' ),
						'default' => __( 'Default (left)', 'hd' ),
					],
				]
			);

			// -------------------------------------------------------------

			// Create news section
			$wp_customize->add_section(
				'news_menu_section',
				[
					'title'    => __( 'News image', 'hd' ),
					'panel'    => 'addon_menu_settings',
					'priority' => 1001,
				]
			);

			// add news control
			$wp_customize->add_setting(
				'news_menu_setting',
				[
					'default'           => 'default',
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'refresh',
				]
			);
			$wp_customize->add_control(
				'news_menu_control',
				[
					'label'    => __( 'Image ratio', 'hd' ),
					'type'     => 'radio',
					'section'  => 'news_menu_section',
					'settings' => 'news_menu_setting',
					'choices'  => [
						'1v1'     => __( '1:1', 'hd' ),
						'3v2'     => __( '3:2', 'hd' ),
						'4v3'     => __( '4:3', 'hd' ),
						'16v9'    => __( '16:9', 'hd' ),
						'default' => __( 'Ratio default (3:2)', 'hd' ),
					],
				]
			);

			// -------------------------------------------------------------

			// Create product section
			$wp_customize->add_section(
				'product_menu_section',
				[
					'title'    => __( 'Products image', 'hd' ),
					'panel'    => 'addon_menu_settings',
					'priority' => 1001,
				]
			);

			// add product control
			$wp_customize->add_setting(
				'product_menu_setting',
				[
					'default'           => 'default',
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'refresh',
				]
			);
			$wp_customize->add_control(
				'product_menu_control',
				[
					'label'    => __( 'Image ratio', 'hd' ),
					'type'     => 'radio',
					'section'  => 'product_menu_section',
					'settings' => 'product_menu_setting',
					'choices'  => [
						'1v1'     => __( '1:1', 'hd' ),
						'3v2'     => __( '3:2', 'hd' ),
						'4v3'     => __( '4:3', 'hd' ),
						'16v9'    => __( '16:9', 'hd' ),
						'default' => __( 'Ratio default (3:2)', 'hd' ),
					],
				]
			);

			// -------------------------------------------------------------

			// Create video section
			$wp_customize->add_section(
				'video_menu_section',
				[
					'title'    => __( 'Video image', 'hd' ),
					'panel'    => 'addon_menu_settings',
					'priority' => 1002,
				]
			);

			// add news control
			$wp_customize->add_setting(
				'video_menu_setting',
				[
					'default'           => 'default',
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'refresh',
				]
			);
			$wp_customize->add_control(
				'video_menu_control',
				[
					'label'    => __( 'Image ratio', 'hd' ),
					'type'     => 'radio',
					'section'  => 'video_menu_section',
					'settings' => 'video_menu_setting',
					'choices'  => [
						'1v1'     => __( '1:1', 'hd' ),
						'3v2'     => __( '3:2', 'hd' ),
						'4v3'     => __( '4:3', 'hd' ),
						'16v9'    => __( '16:9', 'hd' ),
						'default' => __( 'Ratio default (16:9)', 'hd' ),
					],
				]
			);

			// -------------------------------------------------------------

			// Create custom field for social settings layout
			$wp_customize->add_section(
				'socials_menu_layout',
				[
					'title'    => __( 'Social', 'hd' ),
					'panel'    => 'addon_menu_settings',
					'priority' => 1005,
				]
			);

			// Add options for facebook appid
			$wp_customize->add_setting( 'fb_menu_layout', [ 'sanitize_callback' => 'sanitize_text_field', ] );
			$wp_customize->add_control(
				'fb_menu_layout',
				[
					'label'       => __( 'Facebook AppID', 'hd' ),
					'section'     => 'socials_menu_layout',
					'settings'    => 'fb_menu_layout',
					'type'        => 'text',
					'description' => __( "You can do this at <a href='https://developers.facebook.com/apps/'>developers.facebook.com/apps</a>", 'hd' ),
				]
			);

			// Add options for facebook page_id
			$wp_customize->add_setting( 'fbpage_menu_layout', [ 'sanitize_callback' => 'sanitize_text_field', ] );
			$wp_customize->add_control(
				'fbpage_menu_layout',
				[
					'label'       => __( 'Facebook PageID', 'hd' ),
					'section'     => 'socials_menu_layout',
					'settings'    => 'fbpage_menu_layout',
					'type'        => 'text',
					'description' => __( "How do I find my Facebook Page ID? <a href='https://www.facebook.com/help/1503421039731588'>facebook.com/help/1503421039731588</a>", 'hd' ),
				]
			);

			// Add options for facebook pixel
			$wp_customize->add_setting( 'fbpixel_menu_layout' );
			$wp_customize->add_control(
				'fbpixel_menu_layout',
				[
					'label'    => __( 'Facebook Pixel' ),
					'section'  => 'socials_menu_layout',
					'settings' => 'fbpixel_menu_layout',
				]
			);

			// Add options for analytics tiktok
			$wp_customize->add_setting( 'tiktok_menu_layout' );
			$wp_customize->add_control(
				'tiktok_menu_layout',
				[
					'label'    => __( 'Analytics Tiktok', 'hd' ),
					'section'  => 'socials_menu_layout',
					'settings' => 'tiktok_menu_layout',
				]
			);

			// Add options for Google Analytics
			$wp_customize->add_setting( 'gmt_menu_layout' );
			$wp_customize->add_control(
				'gmt_menu_layout',
				[
					'label'    => __( 'Google Analytics', 'hd' ),
					'section'  => 'socials_menu_layout',
					'settings' => 'gmt_menu_layout',
				]
			);

			// Zalo Appid
			$wp_customize->add_setting( 'zalo_menu_layout', [ 'sanitize_callback' => 'sanitize_text_field', ] );
			$wp_customize->add_control(
				'zalo_menu_layout',
				[
					'label'       => __( 'Zalo AppID', 'hd' ),
					'section'     => 'socials_menu_layout',
					'settings'    => 'zalo_menu_layout',
					'type'        => 'text',
					'description' => __( "You can do this at <a href='https://developers.zalo.me/docs/'>developers.zalo.me/docs/</a>", 'hd' ),
				]
			);

			// Zalo oaid
			$wp_customize->add_setting( 'zalo_oa_menu_setting', [ 'sanitize_callback' => 'sanitize_text_field', ] );
			$wp_customize->add_control(
				'zalo_oa_menu_control',
				[
					'label'       => __( 'Zalo OAID', 'hd' ),
					'section'     => 'socials_menu_layout',
					'settings'    => 'zalo_oa_menu_setting',
					'type'        => 'text',
					'description' => __( "You can do this at <a href='https://oa.zalo.me/manage/oa?option=create'>oa.zalo.me/manage/oa?option=create</a>", 'hd' ),
				]
			);

			// Youtube channel
			$wp_customize->add_setting( 'youtube_channel_layout', [ 'sanitize_callback' => 'esc_url_raw', ] );
			$wp_customize->add_control(
				'youtube_channel_layout',
				[
					'label'       => __( 'Youtube channel', 'hd' ),
					'section'     => 'socials_menu_layout',
					'settings'    => 'youtube_channel_layout',
					'type'        => 'url',
					'description' => __( "YouTube's Official Channel helps you discover what's new & trending globally.", 'hd' ),
				]
			);

			// -------------------------------------------------------------

			// Create hotline section
			$wp_customize->add_section(
				'hotline_menu_section',
				[
					'title'    => __( 'Hotline', 'hd' ),
					'panel'    => 'addon_menu_settings',
					'priority' => 1006,
				]
			);

			// add control
			$wp_customize->add_setting( 'hotline_layout', [
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh'
			] );
			$wp_customize->add_control(
				'hotline_layout',
				[
					'label'       => __( 'Hotline', 'hd' ),
					'section'     => 'hotline_menu_section',
					'settings'    => 'hotline_layout',
					'description' => __( 'Hotline number, support easier interaction on the phone', 'hd' ),
					'type'        => 'text',
				]
			);

			// add control
			$wp_customize->add_setting( 'hotline_zalo_setting', [ 'sanitize_callback' => 'sanitize_text_field', ] );
			$wp_customize->add_control(
				'hotline_zalo_control',
				[
					'label'       => __( 'Zalo Hotline', 'hd' ),
					'section'     => 'hotline_menu_section',
					'settings'    => 'hotline_zalo_setting',
					'type'        => 'text',
					'description' => __( 'Zalo Hotline number, support easier interaction on the zalo', 'hd' ),
				]
			);

			// -------------------------------------------------------------

			// Create GPKD section
			$wp_customize->add_section(
				'GPKD_menu_section',
				[
					'title'    => __( 'Giấy phép kinh doanh', 'hd' ),
					'panel'    => 'addon_menu_settings',
					'priority' => 1006,
				]
			);

			// add control
			$wp_customize->add_setting( 'GPKD_layout', [
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh'
			] );
			$wp_customize->add_control(
				'GPKD_layout',
				[
					'label'       => __( 'GPKD', 'hd' ),
					'section'     => 'GPKD_menu_section',
					'settings'    => 'GPKD_layout',
					'description' => __( 'Thông tin Giấy phép kinh doanh (nếu có)', 'hd' ),
					'type'        => 'text',
				]
			);

			// -------------------------------------------------------------

			// Create breadcrumbs background section
			$wp_customize->add_section(
				'breadcrumb_bg_section',
				[
					'title'    => __( 'Breadcrumb background', 'hd' ),
					'panel'    => 'addon_menu_settings',
					'priority' => 1007,
				]
			);

			// add control
			$wp_customize->add_setting( 'breadcrumb_bg_setting', [ 'transport' => 'refresh' ] );
			$wp_customize->add_control(
				new WP_Customize_Image_Control(
					$wp_customize,
					'breadcrumb_bg_control',
					[
						'label'    => __( 'Breadcrumb background', 'hd' ),
						'section'  => 'breadcrumb_bg_section',
						'settings' => 'breadcrumb_bg_setting',
						'priority' => 9,
					]
				)
			);

			// -------------------------------------------------------------

			// Create footer background section
			$wp_customize->add_section(
				'footer_bg_section',
				[
					'title'    => __( 'Footer background', 'hd' ),
					'panel'    => 'addon_menu_settings',
					'priority' => 1008,
				]
			);

			// add control
			$wp_customize->add_setting( 'footer_bg_setting', [ 'transport' => 'refresh' ] );
			$wp_customize->add_control(
				new WP_Customize_Image_Control(
					$wp_customize,
					'footer_bg_control',
					[
						'label'    => __( 'Footer background', 'hd' ),
						'section'  => 'footer_bg_section',
						'settings' => 'footer_bg_setting',
						'priority' => 9,
					]
				)
			);

			// -------------------------------------------------------------

			// Create footer background section
			$wp_customize->add_section(
				'footer_layout_section',
				[
					'title'    => __( 'Footer layouts', 'hd' ),
					'panel'    => 'addon_menu_settings',
					'priority' => 1009,
				]
			);

			// add control
			$wp_customize->add_setting( 'footer_row_setting', [ 'sanitize_callback' => 'sanitize_text_field', ] );
			$wp_customize->add_control(
				'footer_row_control',
				[
					'label'       => __( 'Footer row number', 'hd' ),
					'section'     => 'footer_layout_section',
					'settings'    => 'footer_row_setting',
					'type'        => 'number',
					'description' => __( 'Footer row number', 'hd' ),
				]
			);

			// add control
			$wp_customize->add_setting( 'footer_col_setting', [ 'sanitize_callback' => 'sanitize_text_field', ] );
			$wp_customize->add_control(
				'footer_col_control',
				[
					'label'       => __( 'Footer columns number', 'hd' ),
					'section'     => 'footer_layout_section',
					'settings'    => 'footer_col_setting',
					'type'        => 'number',
					'description' => __( 'Footer columns number', 'hd' ),
				]
			);
		}

		/**
		 * Sanitize select.
		 *
		 * @param string $input The input from the setting.
		 * @param object $setting The selected setting.
		 *
		 * @return string The input from the setting or the default setting.
		 */
		public function sanitize_select( $input, $setting ) {
			$input   = sanitize_key( $input );
			$choices = $setting->manager->get_control( $setting->id )->choices;

			return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
		}

		/**
		 * Sanitize boolean for checkbox.
		 *
		 * @param bool $checked Whether or not a box is checked.
		 *
		 * @return bool
		 */
		public function sanitize_checkbox( $checked ) {
			return ( ( isset( $checked ) && true === $checked ) ? true : false );
		}
	}
}

W_Customize::get_instance();
