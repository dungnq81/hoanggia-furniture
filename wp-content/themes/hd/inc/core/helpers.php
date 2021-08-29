<?php
/**
 * Helpers functions
 * @author   WEBHD
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// ---------------------------------------------------

if ( ! function_exists( 'debug_r' ) ) {
	/**
	 * @param string $input
	 * @param bool $trace
	 * @param bool $halt
	 */
	function debug_r( $input, $trace = false, $halt = false ) {
		ob_start();

		echo "<br>----- DEBUG DUMP -----";
		echo "<pre>";
		var_dump( $input );
		echo "</pre>";

		if ( $trace ) {
			if ( is_php( '5.3.6' ) ) {
				$debug = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );
			} else {
				$debug = debug_backtrace( false );
			}

			echo "<br>-----STACK TRACE-----";
			echo "<pre>";
			var_dump( $debug );
			echo "</pre>";
		}

		echo "</pre>";
		echo "---------------------------<br>";

		$ret = ob_get_contents();
		ob_end_clean();

		echo $ret;
		if ( $halt == true ) {
			exit();
		}
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'dump' ) ) {
	/**
	 * Debug Helper
	 * Outputs the given variable with formatting and location
	 */
	function dump() {
		$args = func_get_args();
		foreach ( $args as $k => $arg ) {
			echo '<fieldset class="debug">
        <legend>' . ( $k + 1 ) . '</legend>';
			CVarDumper::dump( $arg, 10, true );
			echo '</fieldset>';
		}
		die;
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'is_http2' ) ) {
	/**
	 * Detect if using the HTTP/2 Prootocol.
	 *
	 * This detects if the user's sesion is running over HTTP/2
	 *
	 * @return boolean Returns true if running HTTP/2
	 */
	function is_http2() {
		if ( ! isset( $_SERVER['SERVER_PROTOCOL'] ) ) {
			return false;
		}

		switch ( strtolower( wp_unslash( $_SERVER['SERVER_PROTOCOL'] ) ) ) {
			case 'h2': // Falls through
			case 'http/2.0':
				return true;

			default:
				return false;
		}
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'str_contains' ) ) {
	/**
	 * @param string $haystack
	 * @param string $needle
	 *
	 * @return bool
	 */
	function str_contains( string $haystack, string $needle ): bool {
		return '' === $needle || false !== strpos( $haystack, $needle );
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'safe_mailto' ) ) {
	/**
	 * Encoded Mailto Link
	 *
	 * Create a spam-protected mailto link written in Javascript
	 *
	 * @param string $email the email address
	 * @param string $title the link title
	 * @param mixed $attributes any attributes
	 *
	 * @return string
	 */
	function safe_mailto( string $email, string $title = '', $attributes = '' ): string {
		if ( trim( $title ) === '' ) {
			$title = $email;
		}

		$x = str_split( '<a href="mailto:', 1 );

		for ( $i = 0, $l = strlen( $email ); $i < $l; $i ++ ) {
			$x[] = '|' . ord( $email[ $i ] );
		}

		$x[] = '"';

		if ( $attributes !== '' ) {
			if ( is_array( $attributes ) ) {
				foreach ( $attributes as $key => $val ) {
					$x[] = ' ' . $key . '="';
					for ( $i = 0, $l = strlen( $val ); $i < $l; $i ++ ) {
						$x[] = '|' . ord( $val[ $i ] );
					}
					$x[] = '"';
				}
			} else {
				for ( $i = 0, $l = mb_strlen( $attributes ); $i < $l; $i ++ ) {
					$x[] = mb_substr( $attributes, $i, 1 );
				}
			}
		}

		$x[] = '>';

		$temp = [];
		for ( $i = 0, $l = strlen( $title ); $i < $l; $i ++ ) {
			$ordinal = ord( $title[ $i ] );

			if ( $ordinal < 128 ) {
				$x[] = '|' . $ordinal;
			} else {
				if ( empty( $temp ) ) {
					$count = ( $ordinal < 224 ) ? 2 : 3;
				}

				$temp[] = $ordinal;
				if ( count( $temp ) === $count ) // @phpstan-ignore-line
				{
					$number = ( $count === 3 ) ? ( ( $temp[0] % 16 ) * 4096 ) + ( ( $temp[1] % 64 ) * 64 ) + ( $temp[2] % 64 ) : ( ( $temp[0] % 32 ) * 64 ) + ( $temp[1] % 64 );
					$x[]    = '|' . $number;
					$count  = 1;
					$temp   = [];
				}
			}
		}

		$x[] = '<';
		$x[] = '/';
		$x[] = 'a';
		$x[] = '>';

		$x = array_reverse( $x );

		// improve obfuscation by eliminating newlines & whitespace
		$output = '<script type="text/javascript">'
				  . 'var l=new Array();';

		foreach ( $x as $i => $value ) {
			$output .= 'l[' . $i . "] = '" . $value . "';";
		}

		return $output . ( 'for (var i = l.length-1; i >= 0; i=i-1) {'
						   . "if (l[i].substring(0, 1) === '|') document.write(\"&#\"+unescape(l[i].substring(1))+\";\");"
						   . 'else document.write(unescape(l[i]));'
						   . '}'
						   . '</script>' );
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'is_php' ) ) {
	/**
	 * @param string $version
	 *
	 * @return  bool
	 */
	function is_php( string $version = '5.0.0' ) {
		static $phpVer;

		if ( ! isset( $phpVer[ $version ] ) ) {
			$phpVer[ $version ] = ! ( ( version_compare( PHP_VERSION, $version ) < 0 ) );
		}

		return $phpVer[ $version ];
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'safe_html' ) ) {
	/**
	 * Produces nice safe html for presentation.
	 *
	 * @param $input - accepts a string.
	 *
	 * @return string
	 */
	function safe_html( $input ) {

		$args = array(
			// formatting.
			'span'   => array(
				'class' => array(),
			),
			'h2'     => array(
				'class' => array(),
			),
			'del'    => array(),
			'ins'    => array(),
			'strong' => array(),
			'em'     => array(),
			'b'      => array(),
			'i'      => array(
				'class' => array(),
			),
			'img'    => array(
				'href'   => array(),
				'alt'    => array(),
				'class'  => array(),
				'scale'  => array(),
				'width'  => array(),
				'height' => array(),
				'src'    => array(),
				'srcset' => array(),
				'sizes'  => array(),
			),
			'p'      => array(),

			// links.
			'a'      => array(
				'href'  => array(),
				'class' => array(),
			),
		);

		return wp_kses( $input, $args );
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'sanitize_input' ) ) {
	/**
	 * https://catswhocode.com/php-sanitize-input/
	 *
	 * @param $input
	 *
	 * @return array
	 */
	function sanitize_input( $input ) {
		if ( is_array( $input ) ) {
			foreach ( $input as $var => $val ) {
				$output[ $var ] = sanitize_input( $val );
			}
		} else {
			$input = stripslashes( $input );
			$output = cleanInput( $input );
		}

		return $output;
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'cleanInput' ) ) {
	/**
	 * https://catswhocode.com/php-sanitize-input/
	 *
	 * @param $input
	 *
	 * @return array|string|string[]|null
	 */
	function cleanInput( $input ) {
		$search = array(
			'@<script[^>]*?>.*?</script>@si',   // Strip out javascript
			'@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
			'@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
			'@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
		);

		$output = preg_replace( $search, '', $input );

		return $output;
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'variables_post_key' ) ) {
	/**
	 * @param array $expected_post
	 *
	 * @return void
	 */
	function variables_post_key( array $expected_post = [] ) {
		foreach ( $expected_post as $key ) {
			if ( ! empty( $_POST[ $key ] ) ) {
				${$key} = $_POST[ $key ];
			} else {
				${$key} = null;
			}
		}
	}
}

// ------------------------------------------------------

if ( ! function_exists( 'w_do_shortcode' ) ) {
	/**
	 * Call a shortcode function by tag name.
	 *
	 * @param string $tag The shortcode whose function to call.
	 * @param array $atts The attributes to pass to the shortcode function. Optional.
	 * @param array $content The shortcode's content. Default is null (none).
	 *
	 * @return string|bool False on failure, the result of the shortcode on success.
	 * @since  1.0.0
	 *
	 */
	function w_do_shortcode( $tag, array $atts = array(), $content = null ) {
		global $shortcode_tags;

		if ( ! isset( $shortcode_tags[ $tag ] ) ) {
			return false;
		}

		return call_user_func( $shortcode_tags[ $tag ], $atts, $content, $tag );
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'millitime' ) ) {
	/**
	 * @return string
	 */
	function millitime() {
		$microtime = microtime();
		$comps     = explode( ' ', $microtime );

		// Note: Using a string here to prevent loss of precision
		// in case of "overflow" (PHP converts it to a double)
		return sprintf( '%d%03d', $comps[1], $comps[0] * 1000 );
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'trim_css' ) ) {
	/**
	 * Trim CSS
	 *
	 * @param string $css CSS content to trim.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	function trim_css( $css = '' ) {

		// Trim white space for faster page loading.
		if ( ! empty( $css ) ) {
			$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
			$css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );
			$css = str_replace( ', ', ',', $css );
		}

		return $css;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'normalize_path' ) ) {
	/**
	 * Normalize the given path. On Windows servers backslash will be replaced
	 * with slash. Removes unnecessary double slashes and double dots. Removes
	 * last slash if it exists.
	 *
	 * Examples:
	 * path::normalize("C:\\any\\path\\") returns "C:/any/path"
	 * path::normalize("/your/path/..//home/") returns "/your/home"
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	function normalize_path( $path ) {

		// Backslash to slash convert
		if ( strtoupper( substr( PHP_OS, 0, 3 ) ) == "WIN" ) {
			$path = preg_replace( '/([^\\\])\\\+([^\\\])/s', "$1/$2", $path );
			if ( substr( $path, - 1 ) == "\\" ) {
				$path = substr( $path, 0, - 1 );
			}
			if ( substr( $path, 0, 1 ) == "\\" ) {
				$path = "/" . substr( $path, 1 );
			}
		}

		$path = preg_replace( '/\/+/s', "/", $path );
		$path = "/$path";
		if ( substr( $path, - 1 ) != "/" ) {
			$path .= "/";
		}

		$expr = '/\/([^\/]{1}|[^\.\/]{2}|[^\/]{3,})\/\.\.\//s';
		while ( preg_match( $expr, $path ) ) {
			$path = preg_replace( $expr, "/", $path );
		}

		$path = substr( $path, 0, - 1 );
		$path = substr( $path, 1 );

		return $path;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'ip_address' ) ) {
	/**
	 * @return string
	 */
	function ip_address() {
		$ip = '127.0.0.1';

		// Get user IP address
		foreach (
			[
				'HTTP_CLIENT_IP',
				'HTTP_X_FORWARDED_FOR',
				'HTTP_X_FORWARDED',
				'HTTP_X_CLUSTER_CLIENT_IP',
				'HTTP_FORWARDED_FOR',
				'HTTP_FORWARDED',
				'REMOTE_ADDR',
			] as $key
		) {
			if ( array_key_exists( $key, $_SERVER ) === true ) {
				foreach ( explode( ',', $_SERVER[ $key ] ) as $ip ) {
					$ip = trim( $ip ); // just to be safe
					$ip = ( validate_ip( $ip ) === false ) ? '127.0.0.1' : $ip;
				}
			}
		}

		return $ip;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'validate_ip' ) ) {
	/**
	 * @param $ip
	 *
	 * @return bool
	 */
	function validate_ip( $ip ) {
		if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) === false ) {
			return false;
		}

		return true;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'locale_array' ) ) {
	/**
	 * @return array
	 */
	function locale_array() {
		return [
			'af' => 'af_ZA',
			'ar' => 'ar_AR',
			'az' => 'az_AZ',
			'ca' => 'ca_ES',
			'cy' => 'cy_GB',
			'el' => 'el_GR',
			'eo' => 'eo_EO',
			'et' => 'et_EE',
			'eu' => 'eu_ES',
			'fi' => 'fi_FI',
			'gu' => 'gu_IN',
			'hr' => 'hr_HR',
			'hy' => 'hy_AM',
			'ja' => 'ja_JP',
			'kk' => 'kk_KZ',
			'km' => 'km_KH',
			'lv' => 'lv_LV',
			'mn' => 'mn_MN',
			'mr' => 'mr_IN',
			'ps' => 'ps_AF',
			'sq' => 'sq_AL',
			'te' => 'te_IN',
			'th' => 'th_TH',
			'tl' => 'tl_PH',
			'uk' => 'uk_UA',
			'ur' => 'ur_PK',
			'vi' => 'vi_VN',
		];
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'youtube_iframe' ) ) {
	/**
	 * @param $url
	 * @param int $autoplay
	 * @param bool $lazyload
	 * @param bool $control
	 *
	 * @return string|null
	 */
	function youtube_iframe( $url, $autoplay = 0, $lazyload = true, $control = true ) {

		parse_str( parse_url( $url, PHP_URL_QUERY ), $vars );
		if ( isset( $vars['v'] ) ) {
			$idurl     = $vars['v'];
			$_size     = ' width="640px" height="320px"';
			$_autoplay = 'autoplay=' . $autoplay;
			$_auto     = ' allow="accelerometer; encrypted-media; gyroscope; picture-in-picture"';
			if ( $autoplay == 1 ) {
				$_auto = ' allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"';
			}
			$_src     = 'https://www.youtube.com/embed/' . $idurl . '?wmode=transparent&origin=' . home_url() . '&' . $_autoplay;
			$_control = '';
			if ( $control == false ) {
				$_control = '&modestbranding=1&controls=0&rel=0&version=3&loop=1&enablejsapi=1&iv_load_policy=3&playlist=' . $idurl . '&playerapiid=ng_video_iframe_' . $idurl;
			}
			$_src  .= $_control . '&html5=1';
			$_src  = ' src="' . $_src . '"';
			$_lazy = '';
			if ( $lazyload == true ) {
				$_lazy = ' loading="lazy"';
			}
			$_iframe = '<iframe id="w_video_iframe_' . $idurl . '" title="' . __( 'YouTube video player', 'hd' ) . '" frameborder="0" allowfullscreen' . $_lazy . $_auto . $_size . $_src . '></iframe>';

			return $_iframe;
		}

		return null;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'youtube_image' ) ) {
	/**
	 * @param $url
	 * @param array $resolution
	 *
	 * @return string
	 */
	function youtube_image( $url, $resolution = [] ) {
		if ( ! is_array( $resolution ) or empty( $resolution ) ) {
			$resolution = [
				'sddefault',
				'hqdefault',
				'mqdefault',
				'default',
				'maxresdefault',
			];
		}

		$url_img = pixel_img();
		parse_str( parse_url( $url, PHP_URL_QUERY ), $vars );
		if ( isset( $vars['v'] ) ) {
			$id = $vars['v'];
			for ( $x = 0; $x < sizeof( $resolution ); $x ++ ) {
				$url_img = 'https://img.youtube.com/vi/' . $id . '/' . $resolution[ $x ] . '.jpg';
				if ( check_url_exists( $url_img ) ) {
					break;
				}
			}
		}

		return $url_img;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'check_url_exists' ) ) {
	/**
	 * @param $url
	 *
	 * @return bool
	 */
	function check_url_exists( $url ) {
		$url     = preg_replace( '/\s+/', '', $url );
		$headers = @get_headers( $url );

		return stripos( $headers[0], "200 OK" ) ? true : false;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'pixel_img' ) ) {
	/**
	 * @param string $img_url
	 *
	 * @return string
	 */
	function pixel_img( string $img_url = '' ) {
		if ( file_exists( $img_url ) ) {
			return $img_url;
		}

		return "data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==";
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'keywords' ) ) {
	/**
	 * Keywords
	 * Takes multiple words separated by spaces and changes them to keywords
	 * Makes sure the keywords are separated by a comma followed by a space.
	 *
	 * @param string $str The keywords as a string, separated by whitespace.
	 *
	 * @return string The list of keywords in a comma separated string form.
	 */
	function keywords( string $str ) {
		return preg_replace( '/[\s]+/', ', ', trim( $str ) );
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'array_unshift_assoc' ) ) {
	/**
	 * @param $arr
	 * @param $key
	 * @param $val
	 *
	 * @return array
	 */
	function array_unshift_assoc( array &$arr, $key, $val ) {
		$arr         = array_reverse( $arr, true );
		$arr[ $key ] = $val;

		return $arr = array_reverse( $arr, true );
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'get_file_extension' ) ) {
	/**
	 * @param $filename
	 * @param bool $include_dot
	 *
	 * @return string
	 */
	function get_file_extension( $filename, $include_dot = false ) {

		$dot = '';
		if ( $include_dot == true ) {
			$dot = '.';
		}

		return $dot . strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'get_file_name' ) ) {
	/**
	 * @param $filename
	 * @param bool $include_ext
	 *
	 * @return string
	 */
	function get_file_name( $filename, $include_ext = false ) {
		return $include_ext ? pathinfo( $filename, PATHINFO_FILENAME ) . get_file_extension( $filename ) : pathinfo( $filename, PATHINFO_FILENAME );
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'is_image' ) ) {
	/**
	 * @param $url
	 *
	 * @return bool
	 */
	function is_image( $url ) {

		if ($url) {
			$headers = @get_headers( $url, 1 ); // @ to suppress errors. Remove when debugging.
			if ( isset( $headers['Content-Type'] ) ) {
				if ( strpos( $headers['Content-Type'], 'image/' ) === false ) {
					return false;
				} else {
					return true;
				}
			}
		}

		return false;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'array_push_after' ) ) {
	/**
	 * Insert an element at the begining of the array
	 *
	 * @param $src
	 * @param $in
	 * @param $pos
	 *
	 * @return array
	 */
	function array_push_after( $src, $in, $pos ) {
		if ( is_int( $pos ) ) {
			$R = array_merge( array_slice( $src, 0, $pos + 1 ), $in, array_slice( $src, $pos + 1 ) );
		} else {
			foreach ( $src as $k => $v ) {
				$R[ $k ] = $v;
				if ( $k == $pos ) {
					$R = array_merge( $R, $in );
				}
			}
		}

		return $R;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'w_strposa' ) ) {
	/**
	 * Strpos over an array.
	 *
	 * @param $haystack
	 * @param $needles
	 * @param int $offset
	 *
	 * @return bool
	 */
	function w_strposa( $haystack, $needles, $offset = 0 ) {

		if ( ! is_array( $needles ) ) {
			$needles = array( $needles );
		}

		foreach ( $needles as $query ) {

			if ( strpos( $haystack, $query, $offset ) !== false ) {
				// stop on first true result.
				return true;
			}
		}

		return false;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'getTimeZone' ) ) {
	/**
	 * @return mixed|string
	 */
	function getTimeZone() {
		$json     = file_get_contents( 'http://ip-api.com/json' );
		$timezone = '';
		if ( ! empty( $json ) ) {
			$ipData = json_decode( $json, true );
			if ( ! empty( $ipData['timezone'] ) ) {
				$timezone = $ipData['timezone'];
			}
		}

		return $timezone;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'get_file_data' ) ) {
	/**
	 * @param $file
	 * @param bool $convert_to_array
	 *
	 * @return false|mixed|string
	 */
	function get_file_data( $file, $convert_to_array = true ) {
		$file = @file_get_contents( $file );
		if ( ! empty( $file ) ) {
			if ( $convert_to_array ) {
				return json_decode( $file, true );
			}

			return $file;
		}

		return false;
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'json_encode_prettify' ) ) {
	/**
	 * @param $data
	 *
	 * @return false|string
	 */
	function json_encode_prettify( $data ) {
		return json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'save_file_data' ) ) {
	/**
	 * @param $path
	 * @param $data
	 * @param bool $json
	 *
	 * @return bool
	 */
	function save_file_data( $path, $data, $json = true ) {
		try {
			if ( $json ) {
				$data = json_encode_prettify( $data );
			}
			@file_put_contents( $path, $data );

			return true;
		} catch ( Exception $ex ) {
			return false;
		}
	}
}

// -------------------------------------------------------------

if ( ! function_exists( 'off_canvas_html' ) ) {
	/**
	 * off_canvas_html
	 */
	function off_canvas_html() {
		?>
		<div class="off-canvas-content" data-off-canvas-content>
			<button class="menu-lines" type="button" data-open="offCanvasMenu" aria-label="button">
				<span class="menu-txt">Menu</span>
				<span class="line line-1"></span>
				<span class="line line-2"></span>
				<span class="line line-3"></span>
			</button>
		</div>
		<?php
	}
}
