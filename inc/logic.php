<?php
/**
 * Portal Content Selection Logic
 *
 * @package Jcore\Portti
 */

namespace Jcore\Portti;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the currently active portal content for a specific slot.
 *
 * Tracks position in the item stack between calls on the same page load,
 * returning the next $limit items each time. If $rotate is true, wraps
 * around to the beginning when running out of items.
 *
 * @param string $slot_slug The slug of the portal slot taxonomy term.
 * @param array  $options {
 *     Optional. Array of options.
 *
 *     @type int    $limit   Maximum number of items to return. Default 1.
 *     @type bool   $rotate  Whether to rotate back to start when exhausted. Default false.
 *     @type int    $post_id The post ID to match against. Default is current queried object ID.
 *     @type string $path    The path to match against. Default is current request path.
 * }
 * @return \WP_Post[]|null  The matching post object(s) or null if none found.
 */
function get_active_portal_content( $slot_slug, $options = array() ) {
	static $slot_positions = array();
	static $slot_stacks    = array();

	if ( empty( $slot_slug ) ) {
		return null;
	}

	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';

	$args = wp_parse_args(
		$options,
		array(
			'limit'   => 1,
			'rotate'  => false,
			'post_id' => get_queried_object_id(),
			'path'    => untrailingslashit( wp_parse_url( $request_uri ?: '/', PHP_URL_PATH ) ),
		)
	);

	// Normalize path so that root is consistently represented and distinct from null.
	$normalized_path = $args['path'];
	if ( '' === $normalized_path ) {
		$normalized_path = '/';
	}
	$args['path'] = $normalized_path;

	// Build a cache key that encodes types to avoid collisions between null and ''.
	$cache_key = md5( wp_json_encode( array( $slot_slug, (int) $args['post_id'], $args['path'] ) ) );
	// Cache the item stack per slot to avoid re-querying.
	if ( ! isset( $slot_stacks[ $cache_key ] ) ) {
		$slot_stacks[ $cache_key ]    = get_item_stack( $slot_slug, $args['post_id'], $args['path'] );
		$slot_positions[ $cache_key ] = 0;
	}

	$matches = $slot_stacks[ $cache_key ];

	if ( empty( $matches ) ) {
		return null;
	}

	$total_items = count( $matches );
	$position    = $slot_positions[ $cache_key ];

	// If we've exhausted all items and rotation is disabled, return null.
	if ( $position >= $total_items && ! $args['rotate'] ) {
		return null;
	}

	// Return array of posts up to the limit.
	$results = array();
	for ( $i = 0; $i < $args['limit']; $i++ ) {
		// Calculate the actual index, wrapping if rotation is enabled.
		if ( $args['rotate'] ) {
			$index = ( $position + $i ) % $total_items;
		} else {
			$index = $position + $i;
			if ( $index >= $total_items ) {
				break;
			}
		}
		$results[] = $matches[ $index ]['post'];
	}

	// Update the position for the next call.
	$slot_positions[ $cache_key ] = $position + $args['limit'];

	return $results;
}

/**
 * Get all matching portal items for a specific slot, filtered by date and context.
 *
 * @param string      $slot_slug The slug of the portal slot taxonomy term.
 * @param int|null    $post_id   Optional. The post ID to match against.
 * @param string|null $path      Optional. The path to match against.
 * @return array[] Array of match data arrays.
 */
function get_item_stack( $slot_slug, $post_id = null, $path = null ) {
	$now = current_time( 'mysql' );

	$args = array(
		'post_type'      => JCORE_PORTTI_POST_TYPE,
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'tax_query'      => array(
			array(
				'taxonomy' => JCORE_PORTTI_POST_TAXONOMY,
				'field'    => 'slug',
				'terms'    => $slot_slug,
			),
		),
		'meta_query'     => array(
			'relation' => 'AND',
			// Filter by start date (if set, must be in the past).
			array(
				'relation' => 'OR',
				array(
					'key'     => '_jcore_portti_start_date',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => '_jcore_portti_start_date',
					'value'   => '',
					'compare' => '=',
				),
				array(
					'key'     => '_jcore_portti_start_date',
					'value'   => $now,
					'compare' => '<=',
					'type'    => 'DATETIME',
				),
			),
			// Filter by end date (if set, must be in the future).
			array(
				'relation' => 'OR',
				array(
					'key'     => '_jcore_portti_end_date',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => '_jcore_portti_end_date',
					'value'   => '',
					'compare' => '=',
				),
				array(
					'key'     => '_jcore_portti_end_date',
					'value'   => $now,
					'compare' => '>=',
					'type'    => 'DATETIME',
				),
			),
		),
	);

	$query = new \WP_Query( $args );

	if ( ! $query->have_posts() ) {
		return array();
	}

	$matches = array();

	foreach ( $query->posts as $post ) {
		$selected_post_id = (int) get_post_meta( $post->ID, '_jcore_portti_selected_post', true );
		$route_path       = get_post_meta( $post->ID, '_jcore_portti_route_path', true );
		$priority         = get_post_meta( $post->ID, '_jcore_portti_priority', true );

		if ( empty( $priority ) ) {
			$priority = 'medium';
		}

		$is_match = false;

		// If a specific post/page is selected, check if we're on that post/page.
		if ( $selected_post_id > 0 ) {
			$is_match = ( null === $post_id || $post_id === $selected_post_id );
		} else {
			// Otherwise, use route path matching.
			$is_match = ( null === $path || match_route( $path, $route_path ) );
		}

		if ( $is_match ) {
			$matches[] = array(
				'post'             => $post,
				'priority'         => get_priority_score( $priority ),
				'date'             => get_the_date( 'U', $post->ID ),
				'selected_post_id' => $selected_post_id,
			);
		}
	}

	// Sort by specificity (selected post > route path), then by priority (desc), then by date (desc).
	usort(
		$matches,
		function ( $a, $b ) {
			// Selected post matches are more specific than route matches.
			$a_specificity = $a['selected_post_id'] > 0 ? 1 : 0;
			$b_specificity = $b['selected_post_id'] > 0 ? 1 : 0;

			if ( $a_specificity !== $b_specificity ) {
				return $b_specificity - $a_specificity;
			}

			if ( $a['priority'] !== $b['priority'] ) {
				return $b['priority'] - $a['priority'];
			}

			return $b['date'] - $a['date'];
		}
	);

	return $matches;
}

/**
 * Matches a current path against a route pattern (supporting wildcards).
 *
 * @param string $current_path The current path to match against.
 * @param string $pattern The route pattern to match.
 * @return bool True if the path matches the pattern, false otherwise.
 */
function match_route( $current_path, $pattern ) {
	if ( empty( $pattern ) ) {
		return true; // Empty matches everything.
	}

	$pattern = untrailingslashit( $pattern );
	if ( empty( $current_path ) ) {
		$current_path = '/'; // Default to root path.
	}

	if ( str_ends_with( $pattern, '*' ) ) {
		$base = rtrim( $pattern, '*' );
		return str_starts_with( $current_path, $base );
	}

	return $current_path === $pattern;
}

/**
 * Converts priority string to numeric score.
 *
 * @param string $priority The priority string.
 * @return int The numeric score.
 */
function get_priority_score( $priority ) {
	return match ( $priority ) {
		'high'   => 10,
		'low'    => 1,
		default  => 5,
	};
}
