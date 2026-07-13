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
			'has_archive'  => false,
			'rewrite'      => array( 'slug' => 'events' ),
		)
	);
}
add_action( 'init', 'bishkek_park_register_event_post_type' );

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
