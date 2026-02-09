<?php
/**
 * Render template for the Portal Slot block.
 *
 * @package Jcore\Portti
 */

namespace Jcore\Portti;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$slot_id   = $attributes['slotId'] ?? '';
$max_items = $attributes['maxItems'] ?? 1;
$rotate    = $attributes['rotate'] ?? false;

if ( empty( $slot_id ) ) {
	if ( is_admin() ) {
		echo '<div class="jcore-portal-slot-placeholder">';
		esc_html_e( 'Please select a portal slot in the block settings.', 'jcore-portti' );
		echo '</div>';
	}
	return;
}

$active_posts = get_active_portal_content(
	$slot_id,
	array(
		'limit'  => $max_items,
		'rotate' => $rotate,
	)
);

if ( ! empty( $active_posts ) ) {
	foreach ( $active_posts as $active_post ) {
		$portal_content = apply_filters( 'the_content', $active_post->post_content );
		printf(
			'<div class="jcore-portal-slot jcore-portal-slot--%s" data-portal-id="%s">',
			esc_attr( $slot_id ),
			esc_attr( $active_post->ID )
		);
		echo $portal_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>';
	}
} else {
	if ( ! empty( $content ) ) {
		echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	if ( is_admin() ) {
		echo '<div class="jcore-portal-slot-placeholder">';
		printf(
			/* translators: %s: slot slug */
			esc_html__( 'No active campaign content found for slot: %s', 'jcore-portti' ),
			'<strong>' . esc_html( $slot_id ) . '</strong>'
		);
		if ( ! empty( $content ) ) {
			echo ' <br><em>' . esc_html__( 'Showing fallback content.', 'jcore-portti' ) . '</em>';
		}
		echo '</div>';
	}
}
