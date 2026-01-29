<?php
/**
 * Campaign Content Post Type
 *
 * @package Jcore\Portti
 */

namespace Jcore\Portti;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

add_action(
	'init',
	function () {
		register_post_type(
			JCORE_PORTTI_POST_TYPE,
			array(
				'labels'           => array(
					'name'                     => __( 'Campaign Content', 'jcore' ),
					'singular_name'            => __( 'Campaign Content', 'jcore' ),
					'menu_name'                => __( 'Campaign Content', 'jcore' ),
					'all_items'                => __( 'All Campaign Content', 'jcore' ),
					'edit_item'                => __( 'Edit Campaign Content', 'jcore' ),
					'view_item'                => __( 'View Campaign Content', 'jcore' ),
					'view_items'               => __( 'View Campaign Content', 'jcore' ),
					'add_new_item'             => __( 'Add New Campaign Content', 'jcore' ),
					'add_new'                  => __( 'Add New Campaign Content', 'jcore' ),
					'new_item'                 => __( 'New Campaign Content', 'jcore' ),
					'parent_item_colon'        => __( 'Parent Campaign Content:', 'jcore' ),
					'search_items'             => __( 'Search Campaign Content', 'jcore' ),
					'not_found'                => __( 'No Campaign Content found', 'jcore' ),
					'not_found_in_trash'       => __( 'No Campaign Content found in Trash', 'jcore' ),
					'archives'                 => __( 'Campaign Content Archives', 'jcore' ),
					'attributes'               => __( 'Campaign Content Attributes', 'jcore' ),
					'insert_into_item'         => __( 'Insert into Campaign Content', 'jcore' ),
					'uploaded_to_this_item'    => __( 'Uploaded to this Campaign Content', 'jcore' ),
					'filter_items_list'        => __( 'Filter Campaign Content list', 'jcore' ),
					'filter_by_date'           => __( 'Filter Campaign Content by date', 'jcore' ),
					'items_list_navigation'    => __( 'Campaign Content list navigation', 'jcore' ),
					'items_list'               => __( 'Campaign Content list', 'jcore' ),
					'item_published'           => __( 'Campaign Content published.', 'jcore' ),
					'item_published_privately' => __( 'Campaign Content published privately.', 'jcore' ),
					'item_reverted_to_draft'   => __( 'Campaign Content reverted to draft.', 'jcore' ),
					'item_scheduled'           => __( 'Campaign Content scheduled.', 'jcore' ),
					'item_updated'             => __( 'Campaign Content updated.', 'jcore' ),
					'item_link'                => __( 'Campaign Content Link', 'jcore' ),
					'item_link_description'    => __( 'A link to a Campaign Content.', 'jcore' ),
				),
				'public'           => false,
				'show_ui'          => true,
				'supports'         => array( 'title', 'editor', 'revisions', 'show_in_rest' ),
				'show_in_rest'     => true,
				'menu_icon'        => 'dashicons-share-alt',
				'rewrite'          => false,
				'delete_with_user' => false,
			)
		);

		register_taxonomy(
			JCORE_PORTTI_POST_TAXONOMY,
			JCORE_PORTTI_POST_TYPE,
			array(
				'labels'            => array(
					'name'                       => __( 'Portal Slots', 'jcore' ),
					'singular_name'              => __( 'Portal Slot', 'jcore' ),
					'menu_name'                  => __( 'Portal Slots', 'jcore' ),
					'all_items'                  => __( 'All Portal Slots', 'jcore' ),
					'edit_item'                  => __( 'Edit Portal Slot', 'jcore' ),
					'view_item'                  => __( 'View Portal Slot', 'jcore' ),
					'update_item'                => __( 'Update Portal Slot', 'jcore' ),
					'add_new_item'               => __( 'Add New Portal Slot', 'jcore' ),
					'new_item_name'              => __( 'New Portal Slot Name', 'jcore' ),
					'parent_item'                => __( 'Parent Portal Slot', 'jcore' ),
					'parent_item_colon'          => __( 'Parent Portal Slot:', 'jcore' ),
					'search_items'               => __( 'Search Portal Slots', 'jcore' ),
					'popular_items'              => __( 'Popular Portal Slots', 'jcore' ),
					'separate_items_with_commas' => __( 'Separate Portal Slots with commas', 'jcore' ),
					'add_or_remove_items'        => __( 'Add or remove Portal Slots', 'jcore' ),
					'choose_from_most_used'      => __( 'Choose from the most used Portal Slots', 'jcore' ),
					'not_found'                  => __( 'No Portal Slots found', 'jcore' ),
					'back_to_items'              => __( 'Back to Portal Slots', 'jcore' ),
				),
				'public'            => false,
				'show_ui'           => true,
				'show_in_menu'      => true,
				'show_in_nav_menus' => false,
				'show_admin_column' => true,
				'hierarchical'      => true,
				'show_in_rest'      => true,
				'rewrite'           => false,
			)
		);
	}
);

add_filter(
	'pll_get_post_types',
	function ( $post_types ) {
		$post_types[ JCORE_PORTTI_POST_TYPE ] = JCORE_PORTTI_POST_TYPE;
		return $post_types;
	}
);
