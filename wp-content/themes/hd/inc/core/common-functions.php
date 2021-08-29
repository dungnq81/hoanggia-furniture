<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// ------------------------------------------------------

if ( ! function_exists( 'is_woocommerce_activated' ) ) {
	/**
	 * Query WooCommerce activation
	 *
	 * @return bool
	 */
	function is_woocommerce_activated() {
		return class_exists( 'WooCommerce' );
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'is_elementor_activated' ) ) {
	/**
	 * Query Elementor Pro activation
	 *
	 * @return bool
	 */
	function is_elementor_activated() {
		return ! ( ( ! class_exists( '\Elementor\Plugin' ) || ! class_exists( 'ElementorPro\Modules\ThemeBuilder\Module' ) ) );
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'script_loader_tag' ) ) {
	/**
	 * @param array $arr_parsed [ $handle: $value ] -- $value[ 'defer', 'delay' ]
	 * @param string $tag
	 * @param string $handle
	 * @param string $src
	 *
	 * @return array|string|string[]|null
	 */
	function script_loader_tag( array $arr_parsed, string $tag, string $handle, string $src ) {
		if ( ! is_admin() ) {
			foreach ( $arr_parsed as $str => $value ) {
				if ( str_contains( $handle, $str ) ) {
					if ( 'defer' === $value ) {
						//$tag = '<script defer type=\'text/javascript\' src=\'' . $src . '\'></script>';
						$tag = preg_replace( '/\s+defer\s+/', ' ', $tag );
						$tag = preg_replace( '/\s+src=/', ' defer src=', $tag );
					} elseif ( 'delay' === $value ) {
						$tag = preg_replace( '/\s+defer\s+/', ' ', $tag );
						$tag = preg_replace( '/\s+src=/', ' defer data-type=\'lazy\' data-src=', $tag );
					}
				}
			}
		}

		return $tag;
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'style_loader_tag' ) ) {
	/**
	 * @param array $arr_styles [ $handle ]
	 * @param string $html
	 * @param string $handle
	 *
	 * @return array|string|string[]|null
	 */
	function style_loader_tag( array $arr_styles, string $html, string $handle ) {
		foreach ( $arr_styles as $style ) {
			if ( str_contains( $handle, $style ) ) {
				return preg_replace( '/media=\'all\'/', 'media=\'print\' onload=\'this.media="all"\'', $html );
			}
		}

		return $html;
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'url_to_path' ) ) {
	/**
	 * Convert an assets URL to a path.
	 *
	 * Makes a best guess as to the path of an asset.
	 *
	 * @param string $url The URL to the asset.
	 *
	 * @return string|boolean The path to the asset. False of failure.
	 */
	function url_to_path( $url ) {

		$url  = remove_query_arg( 'ver', $url );
		$path = str_replace(
			array( trailingslashit( content_url() ), trailingslashit( includes_url() ) ),
			array( trailingslashit( WP_CONTENT_DIR ), trailingslashit( ABSPATH . WPINC ) ),
			$url
		);

		if ( ! file_exists( $path ) ) {
			return false;
		}

		return $path;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'get_lang' ) ) {
	/**
	 * Get lang code
	 * @return string
	 */
	function get_lang() {
		return strtolower( substr( get_locale(), 0, 2 ) );
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'get_f_locale' ) ) {
	/**
	 * @return mixed|string
	 */
	function get_f_locale() {
		$arr     = locale_array();
		$arr_key = array_keys( $arr );
		$locale  = get_locale();
		if ( ! in_array( $locale, $arr_key ) ) {
			return $locale;
		}

		return $arr[ $locale ];
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'theme_assets' ) ) {
	/**
	 * @param string $asset
	 *
	 * @return string
	 */
	function theme_assets( string $asset = 'assets' ) {
		if ( empty( $asset ) ) {
			return get_stylesheet_directory_uri() . '/';
		}

		return get_stylesheet_directory_uri() . '/' . trim( $asset, '/' ) . '/';
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'theme_url' ) ) {
	/**
	 * @return string
	 */
	function theme_url() {
		return get_stylesheet_directory_uri() . '/';
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'get_theme_mod_ssl' ) ) {
	/**
	 * @param $mod_name
	 * @param bool $default
	 *
	 * @return mixed|string|string[]
	 */
	function get_theme_mod_ssl( $mod_name, $default = false ) {
		static $_is_loaded;
		if ( empty( $_is_loaded ) ) {

			// references cannot be directly assigned to static variables, so we use an array
			$_is_loaded[0] = [];
		}

		if ( $mod_name ) {
			if ( ! isset($_is_loaded[0][strtolower($mod_name)]) ) {
				//$_mod = preg_replace('/\s+/', '', get_theme_mod( $mod_name, $default ) );
				$_mod = get_theme_mod( $mod_name, $default );
				if ( is_ssl() ) {
					$_is_loaded[0][strtolower($mod_name)] = str_replace( [ 'http://', 'https://' ], 'https://', $_mod );
				}
				else {
					$_is_loaded[0][strtolower($mod_name)] = str_replace( [ 'http://', 'https://' ], 'http://', $_mod );
				}
			}

			return $_is_loaded[0][strtolower($mod_name)];
		}

		return $default;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'get_banner_query' ) ) {
	/**
	 * @param $term
	 * @param int $posts_per_page
	 * @param int $paged
	 *
	 * @return bool|WP_Query
	 */
	function get_banner_query( $term, $posts_per_page = 0, $paged = 0 ) {

		if ( ! $term ) {
			return false;
		}
		$_args = [
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'post_type'           => 'banner',
			'post_status'         => 'publish',
			'orderby'             => [
				'menu_order' => 'DESC',
				'date'       => 'DESC',
			],
			'tax_query'           => [
				[
					'taxonomy' => $term->taxonomy,
					'terms'    => [ $term->term_id ],
				],
			],
			'nopaging'            => true,
		];
		if ( $posts_per_page ) {
			$_args['posts_per_page'] = $posts_per_page;
		}
		if ( $paged ) {
			$_args['paged']    = $paged;
			$_args['nopaging'] = false;
		}

		$_query = new WP_Query( $_args );
		if ( ! $_query->have_posts() ) {
			return false;
		}

		return $_query;
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'social_nav' ) ) {

	/**
	 * @param string $location
	 * @param string $menu_class
	 *
	 * @return bool|false|string|void
	 */
	function social_nav( string $location = 'social-nav', string $menu_class = 'social-menu conn-lnk' ) {

		if ( empty( $location ) ) {
			return false;
		}
		$locations = get_nav_menu_locations();
		if ( ! isset( $locations[ $location ] ) ) {
			return false;
		}

		return wp_nav_menu( [
			'container'      => false,
			'theme_location' => $location,
			'menu_class'     => $menu_class,
			'depth'          => 1,
			'link_before'    => '<span class="social-text">',
			'link_after'     => '</span>',
			'echo'           => false,
		] );
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'second_nav' ) ) {

	/**
	 * @link http://codex.wordpress.org/Function_Reference/wp_nav_menu
	 *
	 * @param string $location
	 * @param string $menu_class
	 * @param string $menu_id
	 *
	 * @return bool|false|string|void
	 */
	function second_nav( $location = 'second-nav', $menu_class = 'desktop-menu', $menu_id = 'second-menu' ) {

		if ( empty( $location ) ) {
			return false;
		}
		$locations = get_nav_menu_locations();
		if ( ! isset( $locations[ $location ] ) ) {
			return false;
		}

		return wp_nav_menu( [
			'container'      => false,
			'menu_id'        => $menu_id,
			'menu_class'     => $menu_class,
			'items_wrap'     => '<ul role="menubar" id="%1$s" class="%2$s dropdown menu horizontal horizontal-menu" data-dropdown-menu>%3$s</ul>',
			'theme_location' => $location,
			'depth'          => 5,
			'fallback_cb'    => false,
			'walker'         => 'Topbar_Menu_Walker',
			'echo'           => false,
		] );
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'main_nav' ) ) {

	/**
	 * @link http://codex.wordpress.org/Function_Reference/wp_nav_menu
	 *
	 * @param string $location
	 * @param string $menu_class
	 * @param string $menu_id
	 *
	 * @return bool|false|string|void
	 */
	function main_nav( $location = 'main-nav', $menu_class = 'desktop-menu', $menu_id = 'main-menu' ) {

		if ( empty( $location ) ) {
			return false;
		}
		$locations = get_nav_menu_locations();
		if ( ! isset( $locations[ $location ] ) ) {
			return false;
		}

		return wp_nav_menu( [
			'container'      => false,
			'menu_id'        => $menu_id,
			'menu_class'     => $menu_class,
			'items_wrap'     => '<ul role="menubar" id="%1$s" class="%2$s dropdown menu horizontal horizontal-menu" data-dropdown-menu>%3$s</ul>',
			'theme_location' => $location,
			'depth'          => 5,
			'fallback_cb'    => false,
			'walker'         => 'Topbar_Menu_Walker',
			'echo'           => false,
		] );
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'mobile_nav' ) ) {

	/**
	 * @param string $location
	 * @param string $menu_class
	 *
	 * @return bool|false|string|void
	 */
	function mobile_nav( string $location = 'mobile-nav', string $menu_class = 'mobile-menu' ) {

		if ( empty( $location ) ) {
			return false;
		}
		$locations = get_nav_menu_locations();
		if ( ! isset( $locations[ $location ] ) ) {
			return false;
		}

		return wp_nav_menu( [
				'container'      => false,   // Remove nav container
				'menu_class'     => $menu_class,
				'theme_location' => $location,
				'items_wrap'     => '<ul role="menubar" class="%2$s vertical menu" data-accordion-menu data-submenu-toggle="true">%3$s</ul>',
				'fallback_cb'    => false,
				'walker'         => 'Mobile_Menu_Walker',
				'echo'           => false,
			]
		);
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'policy_nav' ) ) {

	/**
	 * @param string $location
	 * @param string $menu_class
	 *
	 * @return bool|false|string|void
	 */
	function policy_nav( $location = 'policy-nav', $menu_class = 'policy-menu' ) {

		if ( empty( $location ) ) {
			return false;
		}
		$locations = get_nav_menu_locations();
		if ( ! isset( $locations[ $location ] ) ) {
			return false;
		}

		return wp_nav_menu( [
			'container'      => false,
			'menu_class'     => $menu_class,
			'theme_location' => $location,
			'items_wrap'     => '<ul role="menubar" class="%2$s dropdown menu horizontal horizontal-menu" data-dropdown-menu>%3$s</ul>',
			'depth'          => 1,
			'fallback_cb'    => false,
			'echo'           => false,
		] );
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'the_breadcrumb_theme' ) ) {
	/**
	 * the_breadcrumb_theme()
	 * return void
	 */
	function the_breadcrumb_theme() {
		get_template_part( 'template-parts/parts/breadcrumbs' );
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'w_the_breadcrumbs' ) ) {
	/**
	 * Breadcrumbs
	 * return void
	 */
	function w_the_breadcrumbs() {

		global $post;
		global $wp_query;

		$before = '<li class="current">';
		$after  = '</li>';

		if ( ! is_home() && ! is_front_page() || is_paged() || $wp_query->is_posts_page ) {
			echo '<ol id="crumbs" class="breadcrumbs" aria-label="breadcrumbs">';
			echo '<li><a class="home" href="' . get_base_url() . '">' . __( 'Home', 'hd' ) . '</a></li>';

			/**
			 * @todo viết thêm cho trường hợp taxonomy
			 */
			if ( is_category() || is_tax() ) {

				$cat_obj   = $wp_query->get_queried_object();
				$thisCat   = get_category( $cat_obj->term_id );
				$parentCat = get_category( $thisCat->parent );

				if ( 0 != $thisCat->parent ) {
					if ( ! is_wp_error( $cat_code = get_category_parents( $parentCat->term_id, true, '' ) ) ) {
						$cat_code = str_replace( '<a', '<li><a', $cat_code );
						echo $cat_code = str_replace( '</a>', '</a></li>', $cat_code );
					}
				}

				echo $before . single_cat_title( '', false ) . $after;
			} elseif ( is_day() ) {
				echo '<li><a href="' . get_year_link( get_the_time( 'Y' ) ) . '">' . get_the_time( 'Y' ) . '</a></li>';
				echo '<li><a href="' . get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) . '">' . get_the_time( 'F' ) . '</a></li>';
				echo $before . get_the_time( 'd' ) . $after;
			} elseif ( is_month() ) {
				echo '<li><a href="' . get_year_link( get_the_time( 'Y' ) ) . '">' . get_the_time( 'Y' ) . '</a></li>';
				echo $before . get_the_time( 'F' ) . $after;
			} elseif ( is_year() ) {
				echo $before . get_the_time( 'Y' ) . $after;
			} elseif ( is_single() && ! is_attachment() ) {
				if ( 'post' != get_post_type() ) {
					$post_type = get_post_type_object( get_post_type() );
					$slug      = $post_type->rewrite;
					if ( $slug ) {
						echo '<li><a href="' . get_base_url() . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a></span>';
					}

					//echo $before . get_the_title() . $after;
				} else {
					$cat = primary_term( $post );
					if ( ! empty( $cat ) ) {
						if ( ! is_wp_error( $cat_code = get_category_parents( $cat->term_id, true, '' ) ) ) {
							$cat_code = str_replace( '<a', '<li><a', $cat_code );
							echo $cat_code = str_replace( '</a>', '</a></li>', $cat_code );
						}
					}

					//echo $before . get_the_title() . $after;
				}
			} elseif ( ( is_page() && ! $post->post_parent ) || ( function_exists( 'bp_current_component' ) && bp_current_component() ) ) {
				echo $before . get_the_title() . $after;
			} elseif ( is_search() ) {
				echo $before;
				printf( __( 'Search Results for: %s', 'hd' ), get_search_query() );
				echo $after;
			} elseif ( ! is_single() && ! is_page() && 'post' != get_post_type() ) {
				$post_type = get_post_type_object( get_post_type() );
				echo $before . $post_type->labels->singular_name . $after;
			} elseif ( is_attachment() ) {
				$parent = get_post( $post->post_parent );
				echo '<li><a href="' . get_permalink( $parent ) . '">' . $parent->post_title . '</a></li>';
				echo $before . get_the_title() . $after;
			} elseif ( is_page() && $post->post_parent ) {
				$parent_id   = $post->post_parent;
				$breadcrumbs = [];

				while ( $parent_id ) {
					$page          = get_post( $parent_id );
					$breadcrumbs[] = '<li><a href="' . get_permalink( $page->ID ) . '">' . get_the_title( $page->ID ) . '</a></li>';
					$parent_id     = $page->post_parent;
				}

				$breadcrumbs = array_reverse( $breadcrumbs );
				foreach ( $breadcrumbs as $crumb ) {
					echo $crumb;
				}

				echo $before . get_the_title() . $after;
			} elseif ( is_tag() ) {
				echo $before;
				printf( __( 'Tag Archives: %s', 'hd' ), single_tag_title( '', false ) );
				echo $after;
			} elseif ( is_author() ) {
				global $author;

				$userdata = get_userdata( $author );
				echo $before;
				echo $userdata->display_name;
				echo $after;
			} elseif ( $wp_query->is_posts_page ) {
				$posts_page_title = get_the_title( get_option( 'page_for_posts', true ) );
				echo $before . $posts_page_title . $after;
			} elseif ( is_404() ) {
				echo $before;
				__( 'Not Found', 'hd' );
				echo $after;
			}
			if ( get_query_var( 'paged' ) ) {
				echo '<li class="paged">';
				if ( is_category() || is_tax() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) {
					echo ' (';
				}
				echo __( 'page', 'hd' ) . ' ' . get_query_var( 'paged' );
				if ( is_category() || is_tax() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) {
					echo ')';
				}
				echo $after;
			}

			echo '</ol>';
		}

		// reset
		wp_reset_query();
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'pagination_links' ) ) {
	/**
	 * @param bool $echo
	 *
	 * @return string|null
	 */
	function pagination_links( $echo = true ) {
		global $wp_query;
		if ( $wp_query->max_num_pages > 1 ) {

			// This needs to be an unlikely integer
			$big = 999999999;

			// For more options and info view the docs for paginate_links()
			// http://codex.wordpress.org/Function_Reference/paginate_links
			$paginate_links = paginate_links(
				apply_filters(
					'wp_pagination_args',
					[
						'base'      => str_replace( $big, '%#%', html_entity_decode( get_pagenum_link( $big ) ) ),
						'current'   => max( 1, get_query_var( 'paged' ) ),
						'total'     => $wp_query->max_num_pages,
						'end_size'  => 3,
						'mid_size'  => 3,
						'prev_next' => true,
						'prev_text' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192 512"><path d="M25.1 247.5l117.8-116c4.7-4.7 12.3-4.7 17 0l7.1 7.1c4.7 4.7 4.7 12.3 0 17L64.7 256l102.2 100.4c4.7 4.7 4.7 12.3 0 17l-7.1 7.1c-4.7 4.7-12.3 4.7-17 0L25 264.5c-4.6-4.7-4.6-12.3.1-17z"/></svg>',
						'next_text' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192 512"><path d="M166.9 264.5l-117.8 116c-4.7 4.7-12.3 4.7-17 0l-7.1-7.1c-4.7-4.7-4.7-12.3 0-17L127.3 256 25.1 155.6c-4.7-4.7-4.7-12.3 0-17l7.1-7.1c4.7-4.7 12.3-4.7 17 0l117.8 116c4.6 4.7 4.6 12.3-.1 17z"/></svg>',
						'type'      => 'list',
					]
				)
			);

			$paginate_links = str_replace( "<ul class='page-numbers'>", '<ul class="pagination">', $paginate_links );
			$paginate_links = str_replace( '<li><span class="page-numbers dots">&hellip;</span></li>', '<li class="ellipsis"></li>', $paginate_links );
			$paginate_links = str_replace( '<li><span aria-current="page" class="page-numbers current">', '<li class="current"><span aria-current="page" class="show-for-sr">You\'re on page </span>', $paginate_links );
			$paginate_links = str_replace( '</span></li>', '</li>', $paginate_links );
			$paginate_links = preg_replace( '/\s*page-numbers\s*/', '', $paginate_links );
			$paginate_links = preg_replace( '/\s*class=""/', '', $paginate_links );

			// Display the pagination if more than one page is found.
			if ( $paginate_links ) {
				$paginate_links = '<nav aria-label="Pagination">' . $paginate_links . '</nav>';
				if ( $echo ) {
					echo $paginate_links;
				} else {
					return $paginate_links;
				}
			}
		}

		return null;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'w_term_children' ) ) {
	/**
	 * @param $term_id
	 * @param string $taxonomy
	 *
	 * @return array|WP_Error
	 */
	function w_term_children( $term_id, string $taxonomy = 'category' ) {
		if ( ! $taxonomy ) {
			$taxonomy = 'category';
		}

		return get_term_children( $term_id, $taxonomy );
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'site_title_or_logo' ) ) {
	/**
	 * @param bool $echo
	 *
	 * @return string|void
	 */
	function site_title_or_logo( bool $echo = true ) {
		if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
			$logo = get_custom_logo();
			$html = ( is_home() || is_front_page() ) ? '<h1 class="logo">' . $logo . '</h1>' : $logo;
		} else {
			$tag  = is_home() ? 'h1' : 'div';
			$html = '<' . esc_attr( $tag ) . ' class="site-title"><a href="' . esc_url( home_url( '/' ) ) . '" rel="home">' . esc_html( get_bloginfo( 'name' ) ) . '</a></' . esc_attr( $tag ) . '>';
			if ( '' !== get_bloginfo( 'description' ) ) {
				$html .= '<p class="site-description">' . esc_html( get_bloginfo( 'description', 'display' ) ) . '</p>';
			}
		}

		if ( ! $echo ) {
			return $html;
		}

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'footer_logo' ) ) {
	/**
	 * @return string
	 */
	function footer_logo() {
		$html = '';
		if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
			$logo = get_custom_logo();
			$html = '<div class="footer-branding">' . $logo . '</div>';
		}

		return $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'custom_tax_thumb_by' ) ) {
	/**
	 * @param $tax_id
	 * @param null $field_name
	 * @param string $size
	 * @param bool $img_wrap
	 *
	 * @return string
	 */
	function custom_tax_thumb_by( $tax_id, $field_name = null, $size = "large", $img_wrap = false ) {
		if ( $field_name && function_exists( 'get_field' ) ) {
			if ( $attach_id = get_field( $field_name, get_term( $tax_id ) ) ) {
				if ( is_image( $attach_id ) ) {
					$attach_id = attachment_url_to_postid( $attach_id );
				}

				if ( $img_wrap == true ) {
					$_img = wp_get_attachment_image( $attach_id, $size );
				} else {
					$_thumbnail = wp_get_attachment_image_src( $attach_id, $size );
					$_img       = $_thumbnail[0];
				}

				return $_img;
			}
		}

		return placeholder_img_src( $img_wrap );
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'get_image' ) ) {
	/**
	 * @param $image_id
	 * @param string $thumbnail
	 *
	 * @param string $attribute
	 *
	 * @return string
	 */
	function get_image( $image_id, $thumbnail = 'large', $alt = '' ) {

		! $thumbnail || $thumbnail = 'large';

		// if is url
		if ( is_image( $image_id ) ) {
			[ $width, $height ] = getimagesize( $image_id );

			return '<img width="' . $width . '" height="' . $height . '" src="' . $image_id . '" alt="' . $alt . '" loading="lazy">';
		}

		$attr['alt'] = $alt;
		if ( empty( $alt ) ) {

			// set the image alt attribute to an empty string
			$attr['alt'] = trim( strip_tags( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ) );
			if ( empty( $attr['alt'] ) && ( is_home() || is_front_page() ) ) {
				$attr['alt'] = get_bloginfo( 'name', 'display' );
			}
		}

		/*
		 * If the alt attribute is not empty, there's no need to explicitly pass it
		 * because wp_get_attachment_image() already adds the alt attribute.
		 */

		return wp_get_attachment_image( $image_id, $thumbnail, false, $attr );
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'term_children_list' ) ) {
	/**
	 * @param array $terms_id
	 * @param string $taxonomy
	 * @param bool $ul
	 *
	 * @return string|null
	 */
	function term_children_list( $terms_id = [], $taxonomy = '', $ul = true ) {

		if ( ! $terms_id ) {
			return null;
		}

		if ( empty( $taxonomy ) ) {
			$taxonomy = 'category';
		}

		$str       = '';
		$_li_open  = ( $ul == true ) ? '<li>' : '';
		$_li_close = ( $ul == true ) ? '</li>' : '';
		$_ul_open  = ( $ul == true ) ? '<ul class="ul-terms">' : '';
		$_ul_close = ( $ul == true ) ? '</ul>' : '';
		foreach ( $terms_id as $term_id ) {

			$term = get_term_by( 'id', $term_id, $taxonomy );

			$term_link = get_term_link( $term, $taxonomy );
			if ( is_wp_error( $term_link ) ) {
				continue;
			}

			$str .= $_li_open . '<a title="' . esc_attr( $term->name ) . '" href="' . $term_link . '">' . $term->name . '</a>' . $_li_close;
		}

		return $_ul_open . $str . $_ul_close;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'get_post_thumbnail_src' ) ) {
	/**
	 * @param $post
	 * @param string $thumbnail
	 * @param bool $placeholder
	 * @param bool $placeholder_thumb
	 *
	 * @return string
	 */
	function get_post_thumbnail_src( $post, $thumbnail = 'large', $placeholder = true, $placeholder_thumb = false ) {
		return get_post_thumbnail( $post, $thumbnail, $placeholder, $placeholder_thumb );
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'get_post_thumbnail' ) ) {
	/**
	 * @param $post
	 * @param string $thumbnail
	 * @param bool $placeholder
	 * @param bool $placeholder_thumb
	 *
	 * @return string
	 */
	function get_post_thumbnail( $post, $thumbnail = 'large', $placeholder = true, $placeholder_thumb = false ) {

		if ( is_numeric( $post ) ) {
			$post_id = $post;
		} else {
			$post_id = $post->ID;
		}
		$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $thumbnail );
		$img_url   = $thumbnail[0];
		if ( ! $img_url and $placeholder == true ) {
			$img_url = placeholder_img_src( false, $placeholder_thumb );
		}

		return $img_url;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'image_src' ) ) {
	/**
	 * @param $attachment_id
	 * @param string $size
	 *
	 * @return string|null
	 */
	function image_src( $attachment_id, $size = "large" ) {
		if ( is_string( $attachment_id ) ) {
			return $attachment_id;
		} else if ( $attachment_id and is_int( $attachment_id ) ) {
			$image = wp_get_attachment_image_src( $attachment_id, $size );
			if ( $image ) {
				[ $src, $width, $height ] = $image;

				return $src;
			}
		}

		return null;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'get_the_post_thumb' ) ) {
	/**
	 * @param string $size
	 * @param string $attr
	 *
	 * @return string
	 */
	function get_the_post_thumb( $size = 'large', $attr = '' ) {
		$thumbnail = get_the_post_thumbnail( null, $size, $attr );
		if ( empty( $thumbnail ) ) {
			$thumbnail = placeholder_img_src( true, true );
		}

		return $thumbnail;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'get_post_thumb' ) ) {
	/**
	 * @param $post
	 * @param string $thumbnail
	 * @param bool $placeholder
	 * @param bool $placeholder_thumb
	 *
	 * @return string
	 */
	function get_post_thumb( $post, $thumbnail = 'large', $placeholder = true, $placeholder_thumb = false ) {

		$img_url = get_the_post_thumbnail( $post, $thumbnail );
		if ( empty( $img_url ) and $placeholder == true ) {
			$img_url = placeholder_img_src( true, $placeholder_thumb );
		}

		return $img_url;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'placeholder_img_src' ) ) {
	/**
	 * @return string
	 */
	function placeholder_img_src( $img_wrap = false, $thumb = false ) {
		$src = WP_CONTENT_URL . '/uploads/woocommerce-placeholder.png';
		if ( $thumb == true ) {
			$src = WP_CONTENT_URL . '/uploads/woocommerce-placeholder-320x320.png';
		}
		if ( $img_wrap == true ) {
			$src = "<img src=\"{$src}\" alt=\"placeholder\" class=\"wp-placeholder-image\">";
		}

		return $src;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'placeholder_img_src' ) ) {
	/**
	 * @param string $taxonomy
	 */
	function entry_tags( $taxonomy = 'post_tag' ) {

		/* translators: used between list items, there is a space after the comma */
		$separate_meta = ' ';

		// Get Tags for posts.
		$tags_list = get_the_tag_list( '', $separate_meta );
		if ( empty( $tags_list ) ) {
			$tags_list = get_the_term_tag_list( $taxonomy, '', $separate_meta );
		}

		// We don't want to output .entry-footer if it will be empty, so make sure its not.
		if ( $tags_list ) {
			echo '<div class="entry-tags">';
			printf(
			/* translators: 1: SVG icon. 2: posted in label, only visible to screen readers. 3: list of tags. */
				'<div class="tags-links links">%1$s<span class="screen-reader-text">%2$s </span>%3$s</div>',
				'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M446.381 182.109l1.429-8c1.313-7.355-4.342-14.109-11.813-14.109h-98.601l20.338-113.891C359.047 38.754 353.392 32 345.92 32h-8.127a12 12 0 0 0-11.813 9.891L304.89 160H177.396l20.338-113.891C199.047 38.754 193.392 32 185.92 32h-8.127a12 12 0 0 0-11.813 9.891L144.89 160H42.003a12 12 0 0 0-11.813 9.891l-1.429 8C27.448 185.246 33.103 192 40.575 192h98.6l-22.857 128H13.432a12 12 0 0 0-11.813 9.891l-1.429 8C-1.123 345.246 4.532 352 12.003 352h98.601L90.266 465.891C88.953 473.246 94.608 480 102.08 480h8.127a12 12 0 0 0 11.813-9.891L143.11 352h127.494l-20.338 113.891C248.953 473.246 254.608 480 262.08 480h8.127a12 12 0 0 0 11.813-9.891L303.11 352h102.886a12 12 0 0 0 11.813-9.891l1.429-8c1.313-7.355-4.342-14.109-11.813-14.109h-98.601l22.857-128h102.886a12 12 0 0 0 11.814-9.891zM276.318 320H148.825l22.857-128h127.494l-22.858 128z"/></svg>',
				__( 'Tags', 'hd' ),
				$tags_list
			); // WPCS: XSS OK.

			echo '</div>';
		}
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'entry_hashtag' ) ) {
	/**
	 * @param string $taxonomy
	 * @param int $id
	 */
	function entry_hashtag( $taxonomy = 'post_tag', $id = 0 ) {

		/* translators: used between list items, there is a space after the comma */
		$separate_meta = __( ' ', 'hd' );

		// Get hashtag for posts.
		$hashtag_list = get_the_hashtag_list( $taxonomy, $separate_meta, $id );

		// We don't want to output .entry-footer if it will be empty, so make sure its not.
		if ( $hashtag_list ) {
			echo '<div class="entry-hashtag">';
			echo '<label>' . __( 'Hashtag:', 'hd' ) . '</label>';
			printf(
				'<div class="hashtag-links links">%1$s</div>',
				$hashtag_list
			); // WPCS: XSS OK.

			echo '</div>';
		}
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'get_the_hashtag_list' ) ) {
	/**
	 * @param string $taxonomy
	 * @param string $sep
	 * @param int $id
	 *
	 * @return bool|false|string|WP_Error|WP_Term[]
	 */
	function get_the_hashtag_list( $taxonomy = 'post_tag', $sep = '', $id = 0 ) {

		$terms = get_the_terms( $id, $taxonomy );
		if ( is_wp_error( $terms ) ) {
			return false;
		}
		if ( empty( $terms ) ) {
			return false;
		}

		$links = [];
		foreach ( $terms as $term ) {
			$link = get_term_link( $term, $taxonomy );
			if ( is_wp_error( $link ) ) {
				return $link;
			}
			$links[] = '<a href="' . esc_url( $link ) . '" rel="tag"><span class="hide" aria-label="hashtag">#</span><span>' . $term->name . '</span></a>';
		}

		$hashtag_links = apply_filters( "hashtag_links-{$taxonomy}", $links );
		return join( $sep, $hashtag_links );
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'get_the_term_tag_list' ) ) {
	/**
	 * @param string $taxonomy
	 * @param string $before
	 * @param string $sep
	 * @param string $after
	 * @param int $id
	 *
	 * @return mixed
	 */
	function get_the_term_tag_list( $taxonomy = 'post_tag', $before = '', $sep = '', $after = '', $id = 0 ) {

		/**
		 * Filters the tags list for a given post.
		 *
		 * @param string $tag_list List of tags.
		 * @param string $before String to use before tags.
		 * @param string $sep String to use between the tags.
		 * @param string $after String to use after tags.
		 * @param int $id Post ID.
		 *
		 * @since 2.3.0
		 */
		return apply_filters( 'the_term_tags', get_the_term_list( $id, $taxonomy, $before, $sep, $after ), $before, $sep, $after, $id );
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'loop_excerpt' ) ) {
	/**
	 * @param null $post
	 * @param string $_class_wrap
	 *
	 * @return string|null
	 */
	function loop_excerpt( $post = null, $_class_wrap = 'excerpt' ) {
		$excerpt = get_the_excerpt( $post );
		if ( ! preg_replace( '/\s+/', '', wp_strip_all_tags( $excerpt ) ) ) {
			return null;
		}

		if ( empty( $_class_wrap ) ) {
			return $excerpt;
		}

		return "<p class=\"$_class_wrap\">{$excerpt}</p>";
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'term_excerpt' ) ) {
	/**
	 * @param int $term
	 * @param string $_class_wrap
	 * @param string $_tag
	 *
	 * @return string|null
	 */
	function term_excerpt( $term = 0, $_class_wrap = 'desc', $_tag = 'div' ) {
		$description = term_description( $term );
		if ( ! preg_replace( '/\s+/', '', wp_strip_all_tags( $description ) ) ) {
			return null;
		}

		if ( empty( $_class_wrap ) ) {
			return $description;
		}
		if ( 'div' === $_tag ) {
			return "<div class=\"$_class_wrap\">$description</div>";
		}

		return "<p class=\"$_class_wrap\">$description</p>";
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'current_url' ) ) {
	/**
	 * @return string
	 */
	function current_url() {
		global $wp;

		return trailingslashit( home_url( $wp->request ) );
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'primary_term' ) ) {
	/**
	 * @param null $post
	 * @param string $taxonomy
	 *
	 * @return array|bool|int|mixed|object|WP_Error|WP_Term|null
	 */
	function primary_term( $post = null, $taxonomy = '' ) {
		$post = get_post( $post );
		$ID   = is_numeric( $post ) ? $post : $post->ID;

		if ( empty( $taxonomy ) ) {
			$post_type  = get_post_type( $ID );
			$taxonomies = get_object_taxonomies( $post_type );
			if ( isset( $taxonomies[0] ) ) {
				if ( 'product_type' == $taxonomies[0] && isset( $taxonomies[2] ) ) {
					$taxonomy = $taxonomies[2];
				}
			}
		}

		if ( empty( $taxonomy ) ) {
			$taxonomy = 'category';
		}

		// Rank Math SEO
		// https://vi.wordpress.org/plugins/seo-by-rank-math/
		$primary_term_id = get_post_meta( get_the_ID(), 'rank_math_primary_' . $taxonomy, true );
		if ( $primary_term_id ) {
			$term = get_term( $primary_term_id, $taxonomy );
			if ( $term ) {
				return $term;
			}
		}

		// Default, first category
		$post_terms = get_the_terms( $post, $taxonomy );
		if ( is_array( $post_terms ) ) {
			return $post_terms[0];
		}

		return false;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'w_post_term' ) ) {
	/**
	 * @param $post
	 * @param string $taxonomy
	 * @param string $wrapper_open
	 * @param string $wrapper_close
	 *
	 * @return string|null
	 */
	function w_post_term( $post, $taxonomy = '', $wrapper_open = '<div class="cat">', $wrapper_close = '</div>' ) {
		if ( empty( $taxonomy ) ) {
			$taxonomy = 'category';
		}

		$link       = '';
		$post_terms = get_the_terms( $post, $taxonomy );

		if ( $post_terms ) {
			foreach ( $post_terms as $term ) {
				if ( $term ) {
					$link .= '<a href="' . esc_url( get_term_link( $term, $taxonomy ) ) . '" title="' . esc_attr( $term->name ) . '">' . $term->name . '</a>';
				}
			}

			if ( $wrapper_open && $wrapper_close ) {
				$link = $wrapper_open . $link . $wrapper_close;
			}
		}

		return $link;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'the_primary_term' ) ) {
	/**
	 * @param        $post
	 * @param string $taxonomy
	 * @param string $wrapper_open
	 * @param string $wrapper_close
	 */
	function the_primary_term( $post, $taxonomy = '', $wrapper_open = '<li class="cat">', $wrapper_close = '</li>' ) {
		$term = primary_term( $post, $taxonomy );
		if ( ! $term ) {
			return;
		}

		$link = '<a href="' . esc_url( get_term_link( $term, $taxonomy ) ) . '" title="' . esc_attr( $term->name ) . '">' . $term->name . '</a>';
		if ( $wrapper_open && $wrapper_close ) {
			$link = $wrapper_open . $link . $wrapper_close;
		}
		echo $link;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'get_base_url' ) ) {
	/**
	 * @param string $uri
	 * @param bool $relative relative path. Default empty
	 *
	 * @return string|string[]|null
	 */
	function get_base_url( string $uri = '', bool $relative = false ) {

		if ( empty( $uri ) ) {
			$uri = '/';
		}
		elseif ( $uri && is_string( $uri ) ) {
			$uri = '/' . trim( $uri, '/' ) . '/';
		}

		$base_url = esc_url( home_url( '/' ) );
		//$base_url = esc_url( site_url( '/' ) );
		$base_url = rtrim( $base_url, '/' );
		if ( $relative == true ) {
			$base_url = preg_replace( '(https?://)', '//', $base_url );
		}

		$current_lg = get_lang();
		$tmp        = $current_lg;

		// polylang plugin
		if ( function_exists( 'pll_default_language' ) ) {
			$tmp = strtolower( substr( pll_default_language(), 0, 2 ) );
		}
		if ( strcmp( $tmp, $current_lg ) !== 0 ) {
			return $base_url . '/' . $current_lg . $uri;
		}

		return $base_url . $uri;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'menu_fallback' ) ) {
	/**
	 * A fallback when no navigation is selected by default.
	 */
	function menu_fallback( $container = 'grid-container' ) {

		echo '<div class="menu-fallback">';
		if ( $container ) {
			echo '<div class="' . $container . '">';
		}
		/* translators: %1$s: link to menus, %2$s: link to customize. */
		printf(
			__( 'Please assign a menu to the primary menu location under %1$s or %2$s the design.', 'hd' ),
			/* translators: %s: menu url */
			sprintf(
				__( '<a class="_blank" href="%s">Menus</a>', 'hd' ),
				get_admin_url( get_current_blog_id(), 'nav-menus.php' )
			),
			/* translators: %s: customize url */
			sprintf(
				__( '<a class="_blank" href="%s">Customize</a>', 'hd' ),
				get_admin_url( get_current_blog_id(), 'customize.php' )
			)
		);
		if ( $container ) {
			echo '</div>';
		}
		echo '</div>';
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'posted_on_humanize' ) ) {
	/**
	 * Determines the difference between two timestamps.
	 * The difference is returned in a human readable format such as "1 hour", "5 mins", "2 days".
	 *
	 * @param string $from Unix timestamp from which the difference begins.
	 * @param string $to Optional. Unix timestamp to end the time difference.
	 *
	 * @param null $post
	 * @param null $_time
	 *
	 * @return string Human readable time difference.
	 */
	function posted_on_humanize( $from = '', $to = '', $post = null, $_time = null ) {
		$flag = false;
		$_ago = __( 'ago', 'hd' );
		if ( empty( $to ) ) {
			$to = current_time( 'timestamp' );
		}
		if ( empty( $from ) ) {
			$from = get_the_time( 'U', $post );
		}

		$diff = (int) abs( $to - $from );
		if ( $diff < HOUR_IN_SECONDS ) {
			$mins = round( $diff / MINUTE_IN_SECONDS );
			if ( $mins <= 1 ) {
				$mins = 1;
			}
			/* translators: Time difference between two dates, in minutes (min=minute). %s: Number of minutes */
			$since = sprintf( _n( '%s min', '%s mins', $mins ), $mins );
		} elseif ( $diff < DAY_IN_SECONDS && $diff >= HOUR_IN_SECONDS ) {
			$hours = round( $diff / HOUR_IN_SECONDS );
			if ( $hours <= 1 ) {
				$hours = 1;
			}
			/* translators: Time difference between two dates, in hours. %s: Number of hours */
			$since = sprintf( _n( '%s hour', '%s hours', $hours ), $hours );
		} elseif ( $diff < WEEK_IN_SECONDS && $diff >= DAY_IN_SECONDS ) {
			$days = round( $diff / DAY_IN_SECONDS );
			if ( $days <= 1 ) {
				$days = 1;
			}
			/* translators: Time difference between two dates, in days. %s: Number of days */
			$since = sprintf( _n( '%s day', '%s days', $days, 'hd' ), $days );
		} else {
			$flag  = true;
			$since = ( $_time == null ) ? get_the_date( '', $post ) : sprintf( __( '%1$s at %2$s', 'hd' ), date( get_option( 'date_format' ), $from ), $_time );
		}
		if ( $flag == false ) {
			$since = $since . ' <span class="ago">' . $_ago . '</span>';
		}

		return apply_filters( 'posted_on_humanize', $since, $diff, $from, $to );
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'comment_count' ) ) {
	/**
	 * comment_count
	 *
	 * @param string $html_open_tag
	 * @param string $html_close_tag
	 */
	function comment_count( $html_open_tag = '', $html_close_tag = '' ) {

		if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			if ( $html_open_tag ) {
				echo $html_open_tag;
			}
			echo '<span class="comments-link">';
			echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M256 32C114.6 32 0 125.1 0 240c0 47.6 19.9 91.2 52.9 126.3C38 405.7 7 439.1 6.5 439.5c-6.6 7-8.4 17.2-4.6 26S14.4 480 24 480c61.5 0 110-25.7 139.1-46.3C192 442.8 223.2 448 256 448c141.4 0 256-93.1 256-208S397.4 32 256 32zm0 368c-26.7 0-53.1-4.1-78.4-12.1l-22.7-7.2-19.5 13.8c-14.3 10.1-33.9 21.4-57.5 29 7.3-12.1 14.4-25.7 19.9-40.2l10.6-28.1-20.6-21.8C69.7 314.1 48 282.2 48 240c0-88.2 93.3-160 208-160s208 71.8 208 160-93.3 160-208 160z"/></svg>';

			/* translators: %s: Name of current post. Only visible to screen readers. */
			comments_popup_link( sprintf( __( 'Leave a comment<span class="screen-reader-text"> on %s</span>', 'hd' ), get_the_title() ) );
			echo '</span>';
			if ( $html_close_tag ) {
				echo $html_close_tag;
			}
		}
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'title_attribute' ) ) {
	/**
	 * @param string $args
	 *
	 * @return string
	 */
	function title_attribute( $args = '' ) {

		$defaults    = [
			'before' => '',
			'after'  => '',
			'echo'   => false,
			'post'   => get_post(),
		];
		$parsed_args = wp_parse_args( $args, $defaults );
		$title       = get_the_title( $parsed_args['post'] );
		if ( strlen( $title ) === 0 ) {
			return __return_empty_string();
		}

		$title = $parsed_args['before'] . esc_attr( $title ) . $parsed_args['after'];

		return $title;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'the_comment_html' ) ) {
	/**
	 * @param mixed $id The ID, to load a single record; Provide array of $params to run 'find'
	 */
	function the_comment_html( $id = null ) {

		if ( is_null( $id ) ) {
			$id = get_post()->ID;
		}
		/*
		 * If the current post is protected by a password and
		 * the visitor has not yet entered the password we will
		 * return early without loading the comments.
		*/
		if ( post_password_required( $id ) ) {
			return;
		}
		$wrapper_open  = "<div class=\"comments-wrapper\">";
		$wrapper_close = "</div>";
		$post_type     = get_post_type( $id );

		$facebook_comment = false;
		$zalo_comment     = false;

		if ( class_exists( 'ACF' ) && function_exists( 'get_field' ) ) {
			$facebook_comment = get_field( 'facebook_comment', $id );
			$zalo_comment     = get_field( 'zalo_comment', $id );
		}

		if ( comment_enable() or true === $facebook_comment or true === $zalo_comment ) {
			echo $wrapper_open;
			if ( comment_enable() ) {
				if ( ( class_exists( 'WooCommerce' ) && 'product' != $post_type ) || ! class_exists( 'WooCommerce' ) ) {
					comments_template();
				}
			}
			if ( true === $facebook_comment ) {
				get_template_part( 'template-parts/comment/facebook' );
			}
			if ( true === $zalo_comment ) {
				get_template_part( 'template-parts/comment/zalo' );
			}

			echo $wrapper_close;
		}
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'comment_enable' ) ) {
	/**
	 * @return bool
	 */
	function comment_enable() {
		return ( comments_open() or (bool) get_comments_number() ) && ! post_password_required();
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'strip_whitespace' ) ) {
	/**
	 * @param $string
	 *
	 * @return array|string|string[]|null
	 */
	function strip_whitespace( $string ) {
		$string = preg_replace( '/\s+/', '', $string );
		$string = preg_replace('~\x{00a0}~','',$string);

		return $string;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'post_excerpt' ) ) {
	/**
	 * @param null $post
	 * @param string $_class_wrap
	 *
	 * @return string|null
	 */
	function post_excerpt( $post = null, $_class_wrap = 'excerpt', $_blockquote = false ) {
		$post = get_post( $post );
		if ( ! preg_replace( '/\s+/', '', wp_strip_all_tags( $post->post_excerpt ) ) ) {
			return null;
		}

		$_open  = '';
		$_close = '';
		if ( ! empty( $_class_wrap ) ) {
			$_open  = '<div class="' . $_class_wrap . '">';
			$_close = '</div>';
		}

		if ( $_blockquote ) {
			$_open  = $_open . '<blockquote>';
			$_close = '</blockquote>' . $_close;
		}

		return $_open . $post->post_excerpt . $_close;
	}
}
