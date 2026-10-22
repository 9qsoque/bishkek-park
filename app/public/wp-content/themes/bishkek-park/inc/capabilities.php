<?php
/**
 * Per-page capabilities for the theme's own settings screens ("Карта ТЦ",
 * "Паркинг", "Синематика") instead of `manage_options`, so a non-admin role
 * (e.g. Editor) can be allowed onto just one of them without also getting
 * every other Settings screen. Not granted to anyone in code except
 * administrators — give them to other roles from wp-admin (User Role Editor
 * lists them under "Custom capabilities").
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BISHKEK_PARK_MAP_CAPABILITY', 'bishkek_park_edit_map' );
define( 'BISHKEK_PARK_PARKING_CAPABILITY', 'bishkek_park_edit_parking' );
define( 'BISHKEK_PARK_CINEMATICA_CAPABILITY', 'bishkek_park_edit_cinematica' );

/**
 * Each settings page's `settings_fields()` group => the capability that
 * guards it.
 */
function bishkek_park_settings_page_capabilities() {
	return array(
		'bishkek_park_map_settings'        => BISHKEK_PARK_MAP_CAPABILITY,
		'bishkek_park_parking_settings'    => BISHKEK_PARK_PARKING_CAPABILITY,
		'bishkek_park_cinematica_settings' => BISHKEK_PARK_CINEMATICA_CAPABILITY,
	);
}

/**
 * Role caps live in the database, so this only writes once per capability
 * (the has_cap() guard) — and it's what makes them show up in role editors
 * at all, since those list caps that at least one role already has.
 */
function bishkek_park_grant_admin_capabilities() {
	$role = get_role( 'administrator' );
	if ( ! $role ) {
		return;
	}

	foreach ( bishkek_park_settings_page_capabilities() as $capability ) {
		if ( ! $role->has_cap( $capability ) ) {
			$role->add_cap( $capability );
		}
	}
}
add_action( 'admin_init', 'bishkek_park_grant_admin_capabilities' );

/**
 * options.php checks `manage_options` on save by default regardless of who
 * could see the form — without this, a non-admin with the page's capability
 * would load it fine and then get "Sorry, you are not allowed" on Save.
 */
function bishkek_park_register_option_page_capabilities() {
	foreach ( bishkek_park_settings_page_capabilities() as $option_group => $capability ) {
		add_filter(
			'option_page_capability_' . $option_group,
			function () use ( $capability ) {
				return $capability;
			}
		);
	}
}
add_action( 'init', 'bishkek_park_register_option_page_capabilities' );
