<?php
/**
 * Customize the output of menus for Foundation topbar walker
 *
 * @package FoundationPress
 * @since FoundationPress 1.0.0
 */

if ( ! class_exists( 'Topbar_Menu_Walker' ) ) {
	/**
	 * Class Topbar_Menu_Walker
	 */
	class Topbar_Menu_Walker extends Walker_Nav_Menu {
		/**
		 * @param string $output
		 * @param int $depth
		 * @param array $args
		 */
		function start_lvl( &$output, $depth = 0, $args = [] ) {
			if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
				$t = '';
				$n = '';
			} else {
				$t = "\t";
				$n = "\n";
			}
			$indent = str_repeat( $t, $depth );
			$output .= "{$n}{$indent}<ul class=\"vertical menu\">{$n}";
		}
	}
}
