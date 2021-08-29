<?php
defined( 'ABSPATH' ) || exit;

/**
 * Category list walker class.
 * @extends Walker
 */
class W_Category_List_Walker extends Walker {

	/**
	 * @var string
	 */
	public $tree_type = 'category';

	/**
	 * @var string[]
	 */
	public $db_fields = array(
		'parent' => 'parent',
		'id'     => 'term_id',
		'slug'   => 'slug',
	);

	/**
	 * @param string $output
	 * @param int $depth
	 * @param array $args
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		if ( 'list' !== $args['style'] ) {
			return;
		}

		$indent  = str_repeat( "\t", $depth );
		$output .= "$indent<ul class='children vertical nested menu'>\n";
	}

	/**
	 * @param string $output
	 * @param int $depth
	 * @param array $args
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		if ( 'list' !== $args['style'] ) {
			return;
		}

		$indent  = str_repeat( "\t", $depth );
		$output .= "$indent</ul>\n";
	}

	/**
	 * @param string $output
	 * @param object $cat
	 * @param int $depth
	 * @param array $args
	 * @param int $current_object_id
	 */
	public function start_el( &$output, $cat, $depth = 0, $args = array(), $current_object_id = 0 ) {
		$cat_id = intval( $cat->term_id );

		$output .= '<li class="cat-item cat-item-' . $cat_id;

		if ( $args['current_category'] === $cat_id ) {
			$output .= ' current-cat active is-active';
		}

		if ( $args['has_children'] && $args['hierarchical'] && ( empty( $args['max_depth'] ) || $args['max_depth'] > $depth + 1 ) ) {
			$output .= ' cat-parent is-active';
		}

		$output .= '"><a href="' . get_term_link( $cat_id, $this->tree_type ) . '">' . apply_filters( 'list_product_cats', $cat->name, $cat ) . '</a>';

		if ( $args['show_count'] ) {
			$output .= ' <span class="count">(' . $cat->count . ')</span>';
		}
	}

	/**
	 * @param string $output
	 * @param object $cat
	 * @param int $depth
	 * @param array $args
	 */
	public function end_el( &$output, $cat, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}

	/**
	 * @param object $element
	 * @param array $children_elements
	 * @param int $max_depth
	 * @param int $depth
	 * @param array $args
	 * @param string $output
	 */
	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
		if ( ! $element || ( 0 === $element->count && ! empty( $args[0]['hide_empty'] ) ) ) {
			return;
		}
		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}
}
