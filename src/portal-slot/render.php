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

$slot_id         = $attributes['slotId'] ?? '';
$preview_post_id = $attributes['previewPostId'] ?? 0;

if ( empty( $slot_id ) ) {
	if ( is_admin() ) {
		echo '<div class="jcore-portal-slot-placeholder">';
		esc_html_e( 'Please select a portal slot in the block settings.', 'jcore-portti' );
		echo '</div>';
	}
	return;
}

$active_post = null;
if ( is_admin() && $preview_post_id > 0 ) {
	$active_post = get_post( $preview_post_id );
}

if ( ! $active_post ) {
	$active_post = get_active_portal_content( $slot_id );
}

if ( $active_post ) {
	$portal_content = apply_filters( 'the_content', $active_post->post_content );
	?>
	<div class="jcore-portal-slot jcore-portal-slot--<?php echo esc_attr( $slot_id ); ?>" data-portal-id="<?php echo esc_attr( $active_post->ID ); ?>">
		<?php echo $portal_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
	<?php
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
