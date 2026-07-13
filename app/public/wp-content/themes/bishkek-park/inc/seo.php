<?php
/**
 * Outputs the meta description tag for pages, using the _bp_seo_description
 * field defined in inc/meta-boxes.php. Applies to the front page too, since
 * it is a regular 'page' post once set as the static front page in
 * Settings -> Reading.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Prints <meta name="description"> when the current page has one set.
 */
function bishkek_park_output_meta_description() {
	if ( ! is_singular( 'page' ) ) {
		return;
	}

	$description = get_post_meta( get_queried_object_id(), '_bp_seo_description', true );

	if ( ! $description ) {
		return;
	}

	echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
}
add_action( 'wp_head', 'bishkek_park_output_meta_description' );
