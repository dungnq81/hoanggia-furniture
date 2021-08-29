<?php
/**
 * Hooks Class
 * @author   WEBHD
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'W_Hooks' ) ) {

	class W_Hooks {

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
		 * W_Hooks constructor.
		 */
		public function __construct() {

			// wp_head
			add_action( 'wp_head', [ $this, 'prefetch_header' ], 7 );
			add_action( 'wp_head', [ $this, 'gtm_header' ], 9 );
			add_action( 'wp_head', [ $this, 'extra_header' ], 10 );
			add_action( 'wp_head', [ $this, 'facebook_pixel_header' ], 11 );

			// wp_footer
			add_action( 'wp_footer', [ $this, 'bubble_hotline' ], 97 );
			add_action( 'wp_footer', [ $this, 'back_to_top' ], 98 );
			add_action( 'wp_footer', [ $this, 'extra_script' ], 99 );

			// wp_print_footer_scripts
			add_action( 'wp_print_footer_scripts', [ $this, 'skip_link_focus_fix' ] );

			// w_before_header
			add_action( 'w_before_header', [ $this, 'body_open' ], 11 );
			add_action( 'w_before_header', [ $this, 'before_header_extra' ], 14 );

			// w_off_canvas
			add_action( 'w_off_canvas', [ $this, 'off_canvas_menu' ], 10 );

			// w_header
			add_action( 'w_header', [ $this, 'header_content' ], 10 );

			// w_footer
			add_action( 'w_footer', [ $this, 'footer_widgets' ], 10 );
			add_action( 'w_footer', [ $this, 'footer_credit' ], 20 );

			// hide admin bar
			add_action( "user_register", [ $this, 'user_register' ] );
		}

		/**************************************/

		/**
		 * @param $user_id
		 * @return void
		 */
		public function user_register( $user_id ) {
			update_user_meta( $user_id, 'show_admin_bar_front', 'false' );
			update_user_meta( $user_id, 'show_admin_bar_admin', 'false' );
		}

		/**
		 * @return void
		 */
		public function footer_credit() {
			?>
			<footer class="footer-credit">
				<div class="grid-container">
					<div class="grid-x grid-padding-x align-justify align-middle">
						<?php if ( has_nav_menu( 'policy-nav' ) ) : ?>
						<div class="cell medium-shrink nav"><?php echo policy_nav(); ?></div>
						<?php endif; ?>
						<div class="cell medium-shrink copyright">
							<details class="webhd">
								<summary>&copy; <?=date('Y')?>&nbsp;<?=get_bloginfo('name')?>, All rights reserved.</summary>
								<?php
								$GPKD  = get_theme_mod_ssl( 'GPKD_layout' );
								if (strip_whitespace($GPKD))
									echo '<p>' . $GPKD . '</p>'
								?>
							</details>
						</div>
					</div>
				</div>
			</footer>
		<?php }

		/**
		 * @return void
		 */
		public function footer_widgets() {

			$rows    = (int) get_theme_mod_ssl( 'footer_row_setting' );
			$regions = (int) get_theme_mod_ssl( 'footer_col_setting' );
			for ( $row = 1; $row <= $rows; $row ++ ) {

				// Defines the number of active columns in this footer row.
				for ($region = $regions; 0 < $region; $region--) {
					if (is_active_sidebar('w-footer-' . esc_attr($region + $regions * ($row - 1)))) {
						$columns = $region;
						break;
					}
				}

				if (isset($columns)) :
					?>
					<footer id="colophon" class="footer-widgets" role="contentinfo">
						<div class="grid-container">
							<div class="grid-x grid-padding-x">
								<?php
								for ($column = 1; $column <= $columns; $column++) :
									$footer_n = $column + $regions * ($row - 1);
									if (is_active_sidebar('w-footer-' . esc_attr($footer_n))) :
										?>
										<div class="cell footer-widget footer-widget-<?php echo esc_attr($column); ?>">
											<?php dynamic_sidebar('w-footer-' . esc_attr($footer_n)); ?>
										</div>
									<?php
									endif;
								endfor;
								?>
							</div>
						</div>
					</footer><!-- #colophon-->
					<?php
					unset($columns);
				endif;
			}
		}

		/**
		 * @return void
		 */
		public function header_content() {
			$_header = is_active_sidebar( 'w-header-sidebar' );
			$_topheader = is_active_sidebar( 'w-topheader-sidebar' );
			?>
			<div class="top-header">
				<div class="grid-container">
					<?php get_template_part('template-parts/header/second-menu'); ?>
					<?php if ($_topheader) : ?>
						<div class="topheader-widget">
							<?php dynamic_sidebar('w-topheader-sidebar'); ?>
						</div>
					<?php endif;?>
				</div>
			</div>
			<div class="header__top-border"></div>
			<div class="inside-header">
				<div class="grid-container">
					<div class="site-logo">
						<?php site_title_or_logo(); ?>
					</div>
					<div class="site-navigation">
						<?php get_template_part('template-parts/header/primary-menu'); ?>
					</div>
					<?php if ($_header) : ?>
						<div class="header-widget-group">
							<?php dynamic_sidebar('w-header-sidebar'); ?>
						</div>
					<?php endif;?>
				</div>
			</div>
			<?php
		}

		/**
		 * @return void
		 */
		public function off_canvas_menu() {
			// mobile navigation
			$position = get_theme_mod_ssl( 'offcanvas_menu_setting' );
			if ( 'right' == $position ) {
				get_template_part( 'template-parts/header/navigation-right-offcanvas' );
			} elseif ( 'top' == $position ) {
				get_template_part( 'template-parts/header/navigation-top-offcanvas' );
			} elseif ( 'bottom' == $position ) {
				get_template_part( 'template-parts/header/navigation-bottom-offcanvas' );
			} else {
				get_template_part( 'template-parts/header/navigation-left-offcanvas' );
			}
		}

		/**
		 * @return void
		 */
		public function before_header_extra() {
			?>
			<a class="skip-link screen-reader-text" href="#site-navigation"><?php echo __('Skip to navigation', 'hd'); ?></a>
			<a class="skip-link screen-reader-text" href="#site-content"><?php echo __('Skip to the content', 'hd'); ?></a>
			<?php
		}

		/**
		 * @return void
		 */
		public function body_open() {
			if ( function_exists( 'wp_body_open' ) ) {
				wp_body_open();
			} else {
				do_action( 'wp_body_open' );
			}
		}

		/**
		 * Fix skip link focus.
		 *
		 * This does not enqueue the script because it is tiny and because it is only for IE11,
		 * thus it does not warrant having an entire dedicated blocking script being loaded.
		 *
		 * @link https://git.io/vWdr2
		 */
		public function skip_link_focus_fix() {
			echo '<script>';
			include get_template_directory() . '/assets/js/plugins/skip-link-focus-fix.js';
			echo '</script>';

			// The following is minified via `npx terser --compress --mangle -- assets/js/skip-link-focus-fix.js`.
		}

		/**
		 * @return void
		 */
		public function extra_script() {
			?>
			<script>document.documentElement.classList.remove("no-js");</script>
			<script>if (-1 !== navigator.userAgent.indexOf('MSIE') || -1 !== navigator.appVersion.indexOf('Trident/')) {document.documentElement.classList.add('is-IE');}</script>
			<?php
			// tiktok
			$tiktok = get_theme_mod_ssl( 'tiktok_menu_layout' );
			if ( $tiktok ) {
				?>
				<!-- analytics tiktok -->
				<script>
					(function(root) {
						var ta = document.createElement('script'); ta.type = 'text/javascript'; ta.async = true;
						ta.src = 'https://analytics.tiktok.com/i18n/pixel/sdk.js?sdkid=<?=$tiktok?>>';
						var s = document.getElementsByTagName('script')[0];
						s.parentNode.insertBefore(ta, s);
					})(window);
				</script>
				<!-- analytics tiktok -->
				<?php echo "\n";
			}
		}

	    /**
		 * Build the back to top button
		 *
		 * - GeneratePress
		 * - @since 1.3.24
		 */
		public function back_to_top() {
			$back_to_top = apply_filters( 'w_back_to_top', true );
			if ( ! $back_to_top ) return;

			echo apply_filters( // phpcs:ignore
				'w_back_to_top_output',
				sprintf(
					'<a title="%1$s" aria-label="%1$s" rel="nofollow" href="#" class="w-back-to-top toTop" style="opacity:0;visibility:hidden;" data-scroll-speed="%2$s" data-start-scroll="%3$s">%4$s</a>',
					esc_attr__( 'Scroll back to top', 'hd' ),
					absint( apply_filters( 'w_back_to_top_scroll_speed', 400 ) ),
					absint( apply_filters( 'w_back_to_top_start_scroll', 300 ) ),
					W_SVG_Icons::get_svg( 'ui', 'arrow_right', 24 )
				)
			);
		}

		/**
		 * @return void
		 */
		public function bubble_hotline() {

			// hotline
			$_hotline = get_theme_mod_ssl( 'hotline_layout' );
			$_tel     = preg_replace( '/\s+/', '', wp_strip_all_tags( $_hotline ) );

			if ( $_tel ) {
				echo '<div class="hotline-mobile draggable">';
				echo '<a title="' . esc_attr( $_hotline ) . '" href="tel:' . $_tel . '">';
				echo '<div class="hl-circle"></div><div class="hl-circle-fill"></div><div class="hl-circle-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M497.39 361.8l-112-48a24 24 0 0 0-28 6.9l-49.6 60.6A370.66 370.66 0 0 1 130.6 204.11l60.6-49.6a23.94 23.94 0 0 0 6.9-28l-48-112A24.16 24.16 0 0 0 122.6.61l-104 24A24 24 0 0 0 0 48c0 256.5 207.9 464 464 464a24 24 0 0 0 23.4-18.6l24-104a24.29 24.29 0 0 0-14.01-27.6z"/></svg></div>';
				echo '</a>';
				echo '</div>';
			}
		}

		/**
		 * @return void
		 */
		public function facebook_pixel_header() {
			// Facebook pixel
			$fb_pixel = get_theme_mod_ssl( 'fbpixel_menu_layout' );
			if ( $fb_pixel && ! is_customize_preview() ) {
?>
<!-- Facebook Pixel Code -->
<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window, document,'script', 'https://connect.facebook.net/en_US/fbevents.js');fbq('init', '<?php echo $fb_pixel; ?>');fbq('track', 'PageView');</script>
<!-- End Facebook Pixel Code -->
<?php echo "\n";
			}
		}

		/**
		 * @return void
		 */
		public function extra_header() {
			$fb_appid = get_theme_mod_ssl('fb_menu_layout' );
			if ($fb_appid) {
				echo '<meta property="fb:app_id" content="' . $fb_appid . '" />';
			}
			echo "\n";
		}

		/**
		 * @return void
		 */
		public function gtm_header() {

			// Google Tag Manager
			$gtm = get_theme_mod_ssl('gmt_menu_layout');
			if ($gtm && !is_customize_preview()) {
?>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?=$gtm?>"></script>
<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());
	gtag('config', '<?=$gtm?>');
</script>
<?php
			}
		}

		/**
		 * @return void
		 */
		public function prefetch_header() {
			//echo "<meta name=\"theme-color\" content=\"#188b45\" />";
			//echo '<link rel="preconnect" href="https://fonts.gstatic.com">';
			//echo "<link rel=\"manifest\" href=\"/manifest.json\">";
			//echo "\n";
?>
<!--<style>.no-js img,.no-js svg {opacity:0;transition:opacity 0.08s ease-in;}</style>-->
<?php
		}
	}

	W_Hooks::get_instance();
}
