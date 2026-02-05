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
 * @param string $slot_slug The slug of the portal slot taxonomy term.
 * @return \WP_Post|null The matching post object or null if none found.
 */
function get_active_portal_content( $slot_slug ) {
	if ( empty( $slot_slug ) ) {
		return null;
	}

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
		return null;
	}

	$current_path = untrailingslashit( wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ) );
	$matches      = array();

	foreach ( $query->posts as $post ) {
		$route_path = get_post_meta( $post->ID, '_jcore_portti_route_path', true );
		$priority   = get_post_meta( $post->ID, '_jcore_portti_priority', true ) ?: 'medium';

		if ( match_route( $current_path, $route_path ) ) {
			$matches[] = array(
				'post'     => $post,
				'priority' => get_priority_score( $priority ),
				'date'     => get_the_date( 'U', $post->ID ),
			);
		}
	}

	if ( empty( $matches ) ) {
		return null;
	}

	// Sort by priority (desc), then by date (desc).
	usort(
		$matches,
		function ( $a, $b ) {
			if ( $a['priority'] !== $b['priority'] ) {
				return $b['priority'] - $a['priority'];
			}
			return $b['date'] - $a['date'];
		}
	);

	return $matches[0]['post'];
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

	$pattern      = untrailingslashit( $pattern );
	$current_path = $current_path ?: '/';

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
