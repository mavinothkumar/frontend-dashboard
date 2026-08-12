<?php
/**
 * Show Taxonomy in Shortcode or Widget
 * Format:
 * [fed_list_taxonomy taxonomy=TAXONOMY_NAME]
 *
 * @package Frontend Dashboard.
 */

add_filter( 'widget_text', 'do_shortcode' );

add_shortcode( 'fed_list_taxonomy', 'fed_list_taxonomy' );

/**
 * Shortcode: [fed_list_taxonomy taxonomy="category" ...]
 *
 * Safely list out terms from any registered taxonomy. All inputs are sanitized/validated
 * before passing into wp_list_categories(). The final HTML is also escaped via wp_kses().
 *
 * @param  array  $attributes  {
 *     Shortcode attributes, all optional.
 *
 * @type int $child_of Parent term ID to only show child terms of this term. (default: 0)
 * @type int $current_category Term ID to mark as “current” (default: 0)
 * @type int $depth Hierarchical depth (default: 0)
 * @type int $echo Always forced to 0 (we want returned HTML). (default: 0)
 * @type string $exclude Comma‐separated term IDs to exclude. (default: '')
 * @type string $exclude_tree Comma‐separated term IDs whose descendants to exclude. (default: '')
 * @type string $feed Feed URL to append to each term link. (default: '')
 * @type string $feed_image URL of an image to use for the feed link. (default: '')
 * @type string $feed_type Whether to use “rss2” / “atom” / etc. (default: '')
 * @type bool $hide_empty Whether to hide terms with 0 posts. (default: true)
 * @type bool $hide_title_if_empty Hide title_li if no terms exist. (default: false)
 * @type bool $hierarchical Whether to show hierarchy. (default: true)
 * @type string $order “ASC” or “DESC” (default: 'ASC')
 * @type string $orderby Field to sort by (default: 'name')
 * @type string $separator HTML (or text) used to separate terms when style is “none”. (default: '<br />')
 * @type bool $show_count Whether to show post count in parentheses. (default: false)
 * @type string $show_option_all Text for “All Categories” link. (default: '')
 * @type string $show_option_none Text to show if no terms exist. (default: __( 'No categories', 'frontend-dashboard' ))
 * @type string $style “list” or “none” (default: 'list')
 * @type string $taxonomy REQUIRED: Taxonomy slug to list. (default: '')
 * @type string $title_li Text to use as the wrapping <li> title. Any HTML will be stripped. (default: '')
 * @type bool $use_desc_for_title Whether to use a term’s description for the title attribute on the <a>. (default: true)
 * }
 *
 * @return string Safely escaped HTML (or an error message) containing a <ul>…</ul> of terms.
 */
function fed_list_taxonomy( $attributes ) {
	$defaults = array(
		'child_of'            => 0,
		'current_category'    => 0,
		'depth'               => 0,
		'echo'                => 0,
		'exclude'             => '',
		'exclude_tree'        => '',
		'feed'                => '',
		'feed_image'          => '',
		'feed_type'           => '',
		'hide_empty'          => true,
		'hide_title_if_empty' => false,
		'hierarchical'        => true,
		'order'               => 'ASC',
		'orderby'             => 'name',
		'separator'           => '<br />',
		'show_count'          => false,
		'show_option_all'     => '',
		'show_option_none'    => __( 'No categories', 'frontend-dashboard' ),
		'style'               => 'list',
		'taxonomy'            => '',
		'title_li'            => '',
		'use_desc_for_title'  => true,
	);

	$args = shortcode_atts( $defaults, $attributes, 'fed_list_taxonomy' );

	$taxonomy = sanitize_key( $args['taxonomy'] );
	if ( empty( $taxonomy ) ) {
		return esc_html__( 'Please add a valid taxonomy name, e.g. [fed_list_taxonomy taxonomy="category"].', 'frontend-dashboard' );
	}
	if ( ! taxonomy_exists( $taxonomy ) ) {
		return esc_html__( 'Invalid Taxonomy. Please check the taxonomy slug.', 'frontend-dashboard' );
	}

	$child_of         = absint( $args['child_of'] );
	$current_category = absint( $args['current_category'] );
	$depth            = intval( $args['depth'] );

	$echo                = 0;
	$hide_empty          = (bool) $args['hide_empty'];
	$hide_title_if_empty = (bool) $args['hide_title_if_empty'];
	$hierarchical        = (bool) $args['hierarchical'];
	$show_count          = (bool) $args['show_count'];
	$use_desc_for_title  = (bool) $args['use_desc_for_title'];

	$order = strtoupper( sanitize_key( $args['order'] ) );
	if ( ! in_array( $order, array( 'ASC', 'DESC' ), true ) ) {
		$order = 'ASC';
	}

	$allowed_orderbys = array(
		'name',
		'count',
		'slug',
		'term_group',
		'term_id',
		'id',
		'description',
		'include',
		'menu_order',
	);
	$orderby          = sanitize_key( $args['orderby'] );
	if ( ! in_array( $orderby, $allowed_orderbys, true ) ) {
		$orderby = 'name';
	}

	$style = sanitize_key( $args['style'] );
	if ( ! in_array( $style, array( 'list', 'none' ), true ) ) {
		$style = 'list';
	}
	$exclude          = sanitize_text_field( $args['exclude'] );
	$exclude_tree     = sanitize_text_field( $args['exclude_tree'] );
	$feed             = sanitize_text_field( $args['feed'] );
	$feed_type        = sanitize_text_field( $args['feed_type'] );
	$feed_image       = esc_url_raw( $args['feed_image'] );
	$separator        = wp_kses_post( $args['separator'] );
	$show_option_all  = sanitize_text_field( $args['show_option_all'] );
	$show_option_none = sanitize_text_field( $args['show_option_none'] );
	$title_li         = sanitize_text_field( $args['title_li'] );
	$category_args    = array(
		'taxonomy'            => $taxonomy,
		'child_of'            => $child_of,
		'current_category'    => $current_category,
		'depth'               => $depth,
		'echo'                => $echo,
		'exclude'             => $exclude,
		'exclude_tree'        => $exclude_tree,
		'feed'                => $feed,
		'feed_image'          => $feed_image,
		'feed_type'           => $feed_type,
		'hide_empty'          => $hide_empty,
		'hide_title_if_empty' => $hide_title_if_empty,
		'hierarchical'        => $hierarchical,
		'order'               => $order,
		'orderby'             => $orderby,
		'separator'           => $separator,
		'show_count'          => $show_count,
		'show_option_all'     => $show_option_all,
		'show_option_none'    => $show_option_none,
		'style'               => $style,
		'title_li'            => $title_li,
		'use_desc_for_title'  => $use_desc_for_title,
	);
	$categories_html  = wp_list_categories( $category_args );
	if ( empty( $categories_html ) ) {
		return esc_html__( 'No terms found for this taxonomy.', 'frontend-dashboard' );
	}
	$allowed_tags = array(
		'ul'     => array(),
		'li'     => array( 'class' => array() ),
		'a'      => array(
			'href'  => array(),
			'class' => array(),
			'title' => array(),
		),
		'span'   => array( 'class' => array() ),
		'em'     => array(),
		'strong' => array(),
		'img'    => array(
			'src'    => array(),
			'alt'    => array(),
			'width'  => array(),
			'height' => array(),
		),
	);

	$output = '<ul>';
	$output .= wp_kses( $categories_html, $allowed_tags );
	$output .= '</ul>';

	return $output;
}
