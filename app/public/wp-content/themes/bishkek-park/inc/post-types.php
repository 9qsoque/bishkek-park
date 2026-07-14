<?php
/**
 * Custom post types used to power the editable homepage sections:
 * Shops, Cinema listings, Events, and hero Banners.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the "Shops" post type (Магазины section).
 */
function bishkek_park_register_shop_post_type() {
	register_post_type(
		'bp_shop',
		array(
			'label'        => __( 'Магазины', 'bishkek-park' ),
			'labels'       => array(
				'name'          => __( 'Магазины', 'bishkek-park' ),
				'singular_name' => __( 'Магазин', 'bishkek-park' ),
				'add_new_item'  => __( 'Добавить магазин', 'bishkek-park' ),
				'edit_item'     => __( 'Редактировать магазин', 'bishkek-park' ),
			),
			'public'       => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-cart',
			'supports'     => array( 'title', 'thumbnail', 'page-attributes' ),
			'has_archive'  => true,
			'rewrite'      => array( 'slug' => 'shops' ),
		)
	);
}
add_action( 'init', 'bishkek_park_register_shop_post_type' );

/**
 * The bp_shop archive (template: archive-bp_shop.php) is the full shop
 * catalog with a floor filter, so show every shop on one page, ordered the
 * same way as the homepage teaser, instead of WordPress' default paged loop.
 */
function bishkek_park_shop_catalog_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( 'bp_shop' ) ) {
		return;
	}

	$query->set( 'posts_per_page', -1 );
	$query->set( 'orderby', 'menu_order' );
	$query->set( 'order', 'ASC' );

	// Same reasoning as the homepage teaser query in
	// template-parts/homepage/shops.php: query the default (RU) language as
	// the canonical list instead of Polylang's automatic current-language
	// filtering, so untranslated shops don't vanish from the catalog on
	// other languages. archive-bp_shop.php already swaps in each post's
	// current-language translation where one exists.
	if ( function_exists( 'pll_default_language' ) ) {
		$query->set( 'lang', pll_default_language() );
	}
}
add_action( 'pre_get_posts', 'bishkek_park_shop_catalog_query' );

/**
 * Strips a leading "Этаж" (any case, optional following whitespace) from the
 * _bp_floor meta value on save, so it stays a bare number/label even if
 * someone types the old "Этаж 2" style out of habit —
 * bishkek_park_get_shop_floor_label() (functions.php) always adds the
 * "Этаж" prefix back when displaying it, so the stored value only needs to
 * be the part after it.
 */
function bishkek_park_sanitize_shop_floor( $value ) {
	return preg_replace( '/^\s*этаж\s*/iu', '', $value );
}

/**
 * bp_shop's archive and single templates live in template-parts/shop/
 * (grouped like template-parts/homepage/) instead of the theme root, so
 * point WordPress' template hierarchy at that location explicitly — by
 * default it only looks for archive-bp_shop.php/single-bp_shop.php in the
 * theme root.
 */
function bishkek_park_shop_archive_template( $template ) {
	if ( is_post_type_archive( 'bp_shop' ) ) {
		$theme_template = locate_template( 'template-parts/shop/archive-bp_shop.php' );
		if ( $theme_template ) {
			return $theme_template;
		}
	}

	return $template;
}
add_filter( 'archive_template', 'bishkek_park_shop_archive_template' );

function bishkek_park_shop_single_template( $template ) {
	if ( is_singular( 'bp_shop' ) ) {
		$theme_template = locate_template( 'template-parts/shop/single-bp_shop.php' );
		if ( $theme_template ) {
			return $theme_template;
		}
	}

	return $template;
}
add_filter( 'single_template', 'bishkek_park_shop_single_template' );

/**
 * Register the "Cafes & restaurants" post type (Кафе и рестораны section).
 * Deliberately mirrors bp_shop's shape (same meta fields, same card/grid
 * markup) so its archive/single templates can reuse bp_shop's CSS/JS
 * (front-page.css, shop-catalog.css/.js, shop-single.css) instead of
 * duplicating them — see the enqueue logic in functions.php.
 */
function bishkek_park_register_cafe_post_type() {
	register_post_type(
		'bp_cafe',
		array(
			'label'        => __( 'Кафе и рестораны', 'bishkek-park' ),
			'labels'       => array(
				'name'          => __( 'Кафе и рестораны', 'bishkek-park' ),
				'singular_name' => __( 'Кафе/ресторан', 'bishkek-park' ),
				'add_new_item'  => __( 'Добавить кафе/ресторан', 'bishkek-park' ),
				'edit_item'     => __( 'Редактировать кафе/ресторан', 'bishkek-park' ),
			),
			'public'       => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-food',
			'supports'     => array( 'title', 'thumbnail', 'page-attributes' ),
			'has_archive'  => true,
			'rewrite'      => array( 'slug' => 'cafes' ),
		)
	);
}
add_action( 'init', 'bishkek_park_register_cafe_post_type' );

/**
 * Same reasoning as bishkek_park_shop_catalog_query() above: the bp_cafe
 * archive is a single-page catalog, not a paged loop.
 */
function bishkek_park_cafe_catalog_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( 'bp_cafe' ) ) {
		return;
	}

	$query->set( 'posts_per_page', -1 );
	$query->set( 'orderby', 'menu_order' );
	$query->set( 'order', 'ASC' );

	if ( function_exists( 'pll_default_language' ) ) {
		$query->set( 'lang', pll_default_language() );
	}
}
add_action( 'pre_get_posts', 'bishkek_park_cafe_catalog_query' );

/**
 * bp_cafe's archive/single templates live in template-parts/cafe/, same
 * pattern as template-parts/shop/ — see bishkek_park_shop_archive_template()
 * above.
 */
function bishkek_park_cafe_archive_template( $template ) {
	if ( is_post_type_archive( 'bp_cafe' ) ) {
		$theme_template = locate_template( 'template-parts/cafe/archive-bp_cafe.php' );
		if ( $theme_template ) {
			return $theme_template;
		}
	}

	return $template;
}
add_filter( 'archive_template', 'bishkek_park_cafe_archive_template' );

function bishkek_park_cafe_single_template( $template ) {
	if ( is_singular( 'bp_cafe' ) ) {
		$theme_template = locate_template( 'template-parts/cafe/single-bp_cafe.php' );
		if ( $theme_template ) {
			return $theme_template;
		}
	}

	return $template;
}
add_filter( 'single_template', 'bishkek_park_cafe_single_template' );

/**
 * Register the "Movies" post type (Синематика section).
 */
function bishkek_park_register_movie_post_type() {
	register_post_type(
		'bp_movie',
		array(
			'label'        => __( 'Синематика', 'bishkek-park' ),
			'labels'       => array(
				'name'          => __( 'Синематика', 'bishkek-park' ),
				'singular_name' => __( 'Сеанс', 'bishkek-park' ),
				'add_new_item'  => __( 'Добавить сеанс', 'bishkek-park' ),
				'edit_item'     => __( 'Редактировать сеанс', 'bishkek-park' ),
			),
			'public'       => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-tickets-alt',
			'supports'     => array( 'title', 'thumbnail', 'page-attributes' ),
			'has_archive'  => false,
			'rewrite'      => array( 'slug' => 'cinema' ),
		)
	);
}
add_action( 'init', 'bishkek_park_register_movie_post_type' );

/**
 * Register the "Events" post type (Мероприятия section).
 */
function bishkek_park_register_event_post_type() {
	register_post_type(
		'bp_event',
		array(
			'label'        => __( 'Мероприятия', 'bishkek-park' ),
			'labels'       => array(
				'name'          => __( 'Мероприятия', 'bishkek-park' ),
				'singular_name' => __( 'Мероприятие', 'bishkek-park' ),
				'add_new_item'  => __( 'Добавить мероприятие', 'bishkek-park' ),
				'edit_item'     => __( 'Редактировать мероприятие', 'bishkek-park' ),
			),
			'public'       => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-calendar-alt',
			'supports'     => array( 'title', 'thumbnail', 'page-attributes' ),
			'has_archive'  => true,
			'rewrite'      => array( 'slug' => 'events' ),
		)
	);
}
add_action( 'init', 'bishkek_park_register_event_post_type' );

/**
 * The bp_event archive (template: archive-bp_event.php) is the full events
 * catalog with a category filter, so show every event on one page, ordered
 * the same way as the homepage teaser, instead of WordPress' default paged
 * loop. Same reasoning as bishkek_park_shop_catalog_query() above.
 */
function bishkek_park_event_catalog_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( 'bp_event' ) ) {
		return;
	}

	$query->set( 'posts_per_page', -1 );
	$query->set( 'orderby', 'menu_order' );
	$query->set( 'order', 'ASC' );

	if ( function_exists( 'pll_default_language' ) ) {
		$query->set( 'lang', pll_default_language() );
	}
}
add_action( 'pre_get_posts', 'bishkek_park_event_catalog_query' );

/**
 * bp_event's archive template lives in template-parts/event/, same pattern
 * as template-parts/shop/ and template-parts/cafe/ — see
 * bishkek_park_shop_archive_template() above.
 */
function bishkek_park_event_archive_template( $template ) {
	if ( is_post_type_archive( 'bp_event' ) ) {
		$theme_template = locate_template( 'template-parts/event/archive-bp_event.php' );
		if ( $theme_template ) {
			return $theme_template;
		}
	}

	return $template;
}
add_filter( 'archive_template', 'bishkek_park_event_archive_template' );

/**
 * bp_event's single template lives in template-parts/event/, same pattern
 * as template-parts/shop/ and template-parts/cafe/ — see
 * bishkek_park_shop_single_template() above.
 */
function bishkek_park_event_single_template( $template ) {
	if ( is_singular( 'bp_event' ) ) {
		$theme_template = locate_template( 'template-parts/event/single-bp_event.php' );
		if ( $theme_template ) {
			return $theme_template;
		}
	}

	return $template;
}
add_filter( 'single_template', 'bishkek_park_event_single_template' );

/**
 * Register the "Banners" post type (homepage hero slider).
 */
function bishkek_park_register_banner_post_type() {
	register_post_type(
		'bp_banner',
		array(
			'label'        => __( 'Баннеры', 'bishkek-park' ),
			'labels'       => array(
				'name'          => __( 'Баннеры', 'bishkek-park' ),
				'singular_name' => __( 'Баннер', 'bishkek-park' ),
				'add_new_item'  => __( 'Добавить баннер', 'bishkek-park' ),
				'edit_item'     => __( 'Редактировать баннер', 'bishkek-park' ),
			),
			'public'       => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-images-alt2',
			'supports'     => array( 'title', 'thumbnail', 'page-attributes' ),
			'has_archive'  => false,
			'rewrite'      => array( 'slug' => 'banners' ),
		)
	);
}
add_action( 'init', 'bishkek_park_register_banner_post_type' );
