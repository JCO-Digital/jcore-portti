<?php
/**
 * Plugin activation
 *
 * @package Jcore\Portti
 */

namespace Jcore\Portti;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Activation hook to set a flag for creating default portal slot.
 */
register_activation_hook(
	JCORE_PORTTI_PLUGIN_FILE,
	function () {
		set_transient( 'jcore_portti_activated', true );
	}
);

/**
 * Create default portal slot after taxonomy is registered.
 */
add_action(
	'init',
	function () {
		if ( ! get_transient( 'jcore_portti_activated' ) ) {
			return;
		}

		delete_transient( 'jcore_portti_activated' );

		// Check if any terms exist in the taxonomy.
		$terms = get_terms(
			array(
				'taxonomy'   => JCORE_PORTTI_POST_TAXONOMY,
				'hide_empty' => false,
				'number'     => 1,
				'fields'     => 'ids',
			)
		);

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			wp_insert_term(
				__( 'Default Slot', 'jcore-portti' ),
				JCORE_PORTTI_POST_TAXONOMY,
				array(
					'slug' => 'default-slot',
				)
			);
		}
	},
	20 // Priority 20 to run after taxonomy registration on default priority 10.
);
