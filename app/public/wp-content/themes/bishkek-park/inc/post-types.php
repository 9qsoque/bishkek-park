<?php
/**
 * Custom post types used to power the editable homepage sections:
 * Shops, Cinema listings, and Events.
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
			'has_archive'  => false,
			'rewrite'      => array( 'slug' => 'shops' ),
		)
	);
}
add_action( 'init', 'bishkek_park_register_shop_post_type' );

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
