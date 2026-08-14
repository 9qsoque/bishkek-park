<?php
/**
 * Reorders the top-level wp-admin sidebar menu to match the order content
 * editors actually work through, instead of the order WordPress core, this
 * theme's post types/settings pages, and Polylang happened to register
 * their entries in. Uses WordPress' built-in `custom_menu_order`/`menu_order`
 * filters (the documented mechanism for this) rather than reading/rewriting
 * the raw $menu global, so it doesn't have to fight over numeric
 * add_menu_page() positions with other registrants.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'custom_menu_order', '__return_true' );
add_filter( 'menu_order', 'bishkek_park_reorder_admin_menu' );

/**
 * Puts Страницы, Магазины, Кафе и рестораны, Карта ТЦ, Паркинг, Баннеры,
 * Синематика, Мероприятия, Руководство, Этапы развития, and Polylang's
 * Languages first, in that order; everything else (Dashboard, Comments,
 * Appearance, Plugins, Users, Tools, Settings, other plugins, ...) keeps its
 * normal relative order after them.
 */
function bishkek_park_reorder_admin_menu( $menu_order ) {
	$custom_order = array(
		'edit.php?post_type=page',
		'edit.php?post_type=bp_shop',
		'edit.php?post_type=bp_cafe',
		'bishkek-park-mall-map',
		'bishkek-park-parking',
		'edit.php?post_type=bp_banner',
		'bishkek-park-cinematica',
		'edit.php?post_type=bp_event',
		'edit.php?post_type=bp_leader',
		'edit.php?post_type=bp_milestone',
		'mlang',
	);

	$remaining = array_diff( $menu_order, $custom_order );

	return array_merge( $custom_order, $remaining );
}
