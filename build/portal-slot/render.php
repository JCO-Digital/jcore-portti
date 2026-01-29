<?php
/**
 * Render template for global content block.
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 * @package Jcore\Portti
 */

namespace Jcore\Portti;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$term_ids   = isset( $attributes['termIds'] ) ? $attributes['termIds'] : array();
$is_preview = isset( $attributes['preview'] ) ? $attributes['preview'] : false;

// Display preview in editor.
if ( $is_preview ) {
	if ( empty( $term_ids ) ) {
		echo '<div style="padding: 20px; text-align: center; color: #666;">';
		echo esc_html__( 'Select portal slot terms from the block settings', 'jcore-portti' );
		echo '</div>';
	} else {
		$terms = get_terms(
			array(
				'taxonomy'   => 'jcore-portal-slot',
				'include'    => $term_ids,
				'hide_empty' => false,
			)
		);

		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			echo '<div style="padding: 20px;">';
			echo '<strong>' . esc_html__( 'Selected Portal Slots:', 'jcore-portti' ) . '</strong><br>';
			$term_names = array_map(
				function ( $term ) {
					return esc_html( $term->name );
				},
				$terms
			);
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped via esc_html in array_map.
			echo implode( ', ', $term_names );
			echo '</div>';
		}
	}
	return;
}

// Frontend rendering - placeholder for future random post display logic.
if ( ! empty( $term_ids ) ) {
	// TODO: Implement random post selection logic based on selected terms.
	// For now, just add a wrapper div with data attribute.
	echo '<div class="jcore-portal-slot" data-term-ids="' . esc_attr( implode( ',', $term_ids ) ) . '">';
	echo '<!-- Portal slot content will be rendered here -->';
	echo '</div>';
}
