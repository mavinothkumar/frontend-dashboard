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
 * List Taxonomy.
 *
 * @param  array $attributes  Attributes.
 *
 * @return string
 */
function fed_list_taxonomy( $attributes ) {
	$html = '';

	extract(
		shortcode_atts(
			array(
				'child_of'            => 0,
				'current_category'    => 0,
				'depth'               => 0,
				'echo'                => 0,
				'exclude'             => '',
				'exclude_tree'        => '',
				'feed'                => '',
				'feed_image'          => '',
				'feed_type'           => '',
				'hide_empty'          => 1,
				'hide_title_if_empty' => false,
				'hierarchical'        => true,
				'order'               => 'ASC',
				'orderby'             => 'name',
				'separator'           => '<br />',
				'show_count'          => 0,
				'show_option_all'     => '',
				'show_option_none'    => __( 'No categories' ),
				'style'               => 'list',
				'taxonomy'            => null,
				'title_li'            => '',
				'use_desc_for_title'  => 1,
			), $attributes
		)
	);


	if ( $taxonomy ) {
		$args = array(
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

		$categories = wp_list_categories( $args );

		if ( empty( $categories ) ) {
			return __( 'Invalid Taxonomy, Please Check The Taxonomy Name', 'frontend-dashboard' );
		}

		$html .= '<ul>';
		$html .= $categories;
		$html .= '</ul>';

		return $html;
	}

	return __( 'Please Add Taxonomy Name like [fed_list_taxonomy taxonomy=TAXONOMY_NAME]', 'frontend-dashboard' );
}