<?php
/*  Copyright 2019  RavanH  (email : ravanhagen@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, <http://www.gnu.org/licenses/> or
    write to the Free Software Foundation Inc., 59 Temple Place,
    Suite 330, Boston, MA  02111-1307  USA.

    The GNU General Public License does not permit incorporating this
    program into proprietary programs.
*/
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
	die( 'You can not access this page directly!' );
}

/**
 * Class HighlightSearchTerms
 */
class HighlightSearchTerms {

	/**
	 * version
	 *
	 * @var string
	 */
	private static $version = '1.5';

	/**
	 * filtered search terms
	 *
	 * @var null
	 */
	private static $search_terms = null;

	// Change or extend this to match themes content div ID or classes.
	// The hilite script will test div ids/classes and use the first one it finds.
	// When referencing an *ID name*, just be sure to begin with a '#'.
	// When referencing a *class name*, try to put the tag in front,
	// followed by a '.' and then the class name to *improve script speed*.
	static $areas = [
		'.main-content-search',
		'.list-search-wrapper',
	];

	/**
	 * init
	 */
	public static function init() {
		// -- HOOKING INTO WP -- //
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_script' ] );

		// Set query string as js variable in footer
		//add_action( 'wp_footer', array(__CLASS__, 'print_script') );

		// append search query string to results permalinks
		add_action( 'parse_query', [ __CLASS__, 'add_url_filters' ] );
	}

	/**
	 * add_url_filters
	 */
	public static function add_url_filters() {
		if ( is_search() ) {
			add_filter( 'post_link', [ __CLASS__, 'append_search_query' ] );
			add_filter( 'post_type_link', [ __CLASS__, 'append_search_query' ] );
			add_filter( 'page_link', [ __CLASS__, 'append_search_query' ] );
			add_filter( 'bbp_get_topic_permalink', [ __CLASS__, 'append_search_query' ] );
		}

		// for bbPress search result links, but prevent bbp_is_search on admin triggered by Gravity Forms
		if ( function_exists( 'bbp_is_search' ) && ! is_admin() && bbp_is_search() ) {
			add_filter( 'bbp_get_topic_permalink', [ __CLASS__, 'append_search_query' ] );
			add_filter( 'bbp_get_reply_url', [ __CLASS__, 'append_search_query' ] );
		}
	}

	/**
	 * @param $url
	 *
	 * @return string
	 */
	public static function append_search_query( $url ) {

		// do we need in_the_loop() check here ?
		// (it breaks bbPress url support)
		if ( self::have_search_terms() ) {
			$url = add_query_arg( 'hilite', urlencode( "'" . implode( "','", self::$search_terms ) . "'" ), $url );
		}

		return esc_url( $url );
	}

	/**
	 * enqueue_script
	 */
	public static function enqueue_script() {
		wp_enqueue_script( 'hlst-extend', get_template_directory_uri() . "/assets/js/plugins/hlst-extend.min.js", [ "jquery" ], self::$version, true );
		wp_script_add_data( "hlst-extend", "defer", true );

		$script = 'var hlst_query = ';
		$script .= self::have_search_terms() ? wp_json_encode( (array) self::$search_terms ) : '[]';
		$script .= '; var hlst_areas = ' . wp_json_encode( (array) self::$areas ) . ';';
		wp_add_inline_script( 'hlst-extend', $script, 'before' );
	}

	/**
	 * @param $search
	 *
	 * @return array
	 */
	public static function split_search_terms( $search ) {
		$return = [];
		if ( preg_match_all( '/([^\s"\',\+]+)|"([^"]*)"|\'([^\']*)\'/', stripslashes( urldecode( $search ) ), $terms ) ) {
			foreach ( $terms[0] as $term ) {
				$term = trim( str_replace( [ '"', '\'', '%22', '%27' ], '', $term ) );
				if ( ! empty( $term ) ) {
					$return[] = $term;
				}
			}
		}

		return $return;
	}

	/**
	 * @return bool
	 */
	private static function have_search_terms() {

		// did we not look for search terms before?
		if ( ! isset( self::$search_terms ) ) {

			// try regular parsed WP search terms
			if ( $searches = get_query_var( 'search_terms', false ) ) {
				self::$search_terms = $searches;
			} // try for bbPress search or click-through from WPsearch results page
			elseif ( $search = get_query_var( 'bbp_search', false ) or ( isset( $_GET['hilite'] ) and $search = $_GET['hilite'] ) ) {
				self::$search_terms = self::split_search_terms( $search );
			} // nothing? then just leave empty array
			else {
				self::$search_terms = [];
			}
		}

		return empty( self::$search_terms ) ? false : true;
	}
}

if ( is_search() ) {
	HighlightSearchTerms::init();
}
