<?php
/**
 * Plugin Name:       JCORE Portti
 * Description:       A portal block for use in campaigns or other changing content.
 * Version:           0.1.0
 * Requires at least: 6.7
 * Requires PHP:      8.2
 * Author:            J&Co Digital
 * Author URI:        https://jco.fi
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       jcore-portti
 * Domain Path:       /languages
 *
 * @package Jcore\Portti
 */

namespace Jcore\Portti;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'JCORE_PORTTI_BUILD_DIR', __DIR__ . '/build' );
define( 'JCORE_PORTTI_MANIFEST', JCORE_PORTTI_BUILD_DIR . '/blocks-manifest.php' );
define( 'JCORE_PORTTI_POST_TYPE', 'jcore-portal-content' );
define( 'JCORE_PORTTI_POST_TAXONOMY', 'jcore-portal-slot' );

require_once __DIR__ . '/post-type.php';
require_once __DIR__ . '/inc/logic.php';

/**
 * Let Jcore know we are loaded.
 */
add_filter(
	'jcore_plugins_loaded',
	function ( $plugins ) {
		$plugins['jcore-portti'] = __DIR__;
		return $plugins;
	}
);


/**
 * Registers the block using a `blocks-manifest.php` file, which improves the performance of block type registration.
 * Behind the scenes, it also registers all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://make.wordpress.org/core/2025/03/13/more-efficient-block-type-registration-in-6-8/
 * @see https://make.wordpress.org/core/2024/10/17/new-block-type-registration-apis-to-improve-performance-in-wordpress-6-7/
 */
function block_init() {

	// Check if build folder exists.
	if ( ! file_exists( JCORE_PORTTI_MANIFEST ) ) {
		return;
	}

	/**
	 * Registers the block(s) metadata from the `blocks-manifest.php` and registers the block type(s)
	 * based on the registered block metadata.
	 * Added in WordPress 6.8 to simplify the block metadata registration process added in WordPress 6.7.
	 *
	 * @see https://make.wordpress.org/core/2025/03/13/more-efficient-block-type-registration-in-6-8/
	 */
	if ( function_exists( 'wp_register_block_types_from_metadata_collection' ) ) {
		wp_register_block_types_from_metadata_collection( JCORE_PORTTI_BUILD_DIR, JCORE_PORTTI_MANIFEST );
		return;
	}

	/**
	 * Registers the block(s) metadata from the `blocks-manifest.php` file.
	 * Added to WordPress 6.7 to improve the performance of block type registration.
	 *
	 * @see https://make.wordpress.org/core/2024/10/17/new-block-type-registration-apis-to-improve-performance-in-wordpress-6-7/
	 */
	if ( function_exists( 'wp_register_block_metadata_collection' ) ) {
		wp_register_block_metadata_collection( JCORE_PORTTI_BUILD_DIR, JCORE_PORTTI_MANIFEST );
	}
	/**
	 * Registers the block type(s) in the `blocks-manifest.php` file.
	 *
	 * @see https://developer.wordpress.org/reference/functions/register_block_type/
	 */
	$manifest_data = require JCORE_PORTTI_MANIFEST;
	foreach ( array_keys( $manifest_data ) as $block_type ) {
		register_block_type( __DIR__ . "/build/{$block_type}" );
	}
}
add_action( 'init', '\Jcore\Portti\block_init' );

/**
 * Set the script translations.
 *
 * @return void
 */
function set_script_translations() {
	if ( ! is_dir( JCORE_PORTTI_BUILD_DIR ) ) {
		return;
	}

	$block_json_paths = glob( JCORE_PORTTI_BUILD_DIR . '/*/block.json' );
	foreach ( $block_json_paths as $block_json_path ) {
		if ( ! file_exists( $block_json_path ) ) {
			continue;
		}

		$block_json = json_decode( file_get_contents( $block_json_path ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		if ( empty( $block_json ) || ! is_array( $block_json ) ) {
			continue;
		}

		$name       = $block_json['name'];
		$textdomain = $block_json['textdomain'];
		if ( empty( $textdomain ) || empty( $name ) ) {
			continue;
		}

		// Replace slashes with dashes in the block name for the script handle.
		$script_handle = str_replace( '/', '-', $name );
		wp_set_script_translations( $script_handle . '-view-script', $textdomain, plugin_dir_path( __FILE__ ) . 'languages' );
		wp_set_script_translations( $script_handle . '-editor-script', $textdomain, plugin_dir_path( __FILE__ ) . 'languages' );
	}
}
add_action( 'wp_enqueue_scripts', 'Jcore\Portti\set_script_translations' );
add_action( 'admin_enqueue_scripts', 'Jcore\Portti\set_script_translations' );
