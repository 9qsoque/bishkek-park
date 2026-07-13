<?php
/**
 * Bishkek Park theme functions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BISHKEK_PARK_VERSION', wp_get_theme()->get( 'Version' ) );
define( 'BISHKEK_PARK_DIR', get_stylesheet_directory() );
define( 'BISHKEK_PARK_URI', get_stylesheet_directory_uri() );

require BISHKEK_PARK_DIR . '/inc/post-types.php';
require BISHKEK_PARK_DIR . '/inc/meta-boxes.php';
require BISHKEK_PARK_DIR . '/inc/translatable-strings.php';
require BISHKEK_PARK_DIR . '/inc/seo.php';

/**
 * Returns the inline logo SVG markup so its fill can follow CSS `color` (currentColor).
 */
function bishkek_park_get_logo_svg() {
	static $svg = null;

	if ( null === $svg ) {
		$path = BISHKEK_PARK_DIR . '/assets/icons/logo.svg';
		$svg  = file_exists( $path ) ? file_get_contents( $path ) : '';
		$svg  = preg_replace( '/<\?xml.*?\?>/', '', $svg );
		$svg  = str_replace( '<svg ', '<svg class="bp-logo__img" ', $svg );
	}

	return $svg;
}

/**
 * Builds the URL to a theme icon in /icons/.
 */
function bishkek_park_icon_url( $filename ) {
	return BISHKEK_PARK_URI . '/assets/icons/' . $filename;
}

/**
 * Builds the URL to a theme image in /images/.
 */
function bishkek_park_image_url( $filename ) {
	return BISHKEK_PARK_URI . '/assets/images/' . $filename;
}

/**
 * Resolves a post ID queried in the default language to its translation in the
 * current language, falling back to the given (default-language) post when no
 * translation exists yet — so untranslated bp_shop/bp_movie/bp_event entries
 * still show up (in Russian) instead of disappearing on the KY site.
 */
function bishkek_park_get_localized_post_id( $post_id ) {
	if ( ! function_exists( 'pll_current_language' ) || ! function_exists( 'pll_get_post' ) ) {
		return $post_id;
	}

	$translated_id = pll_get_post( $post_id, pll_current_language() );

	return $translated_id ? $translated_id : $post_id;
}

/**
 * Basic theme setup: supported features and the mobile nav menu location.
 */
function bishkek_park_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

	register_nav_menus(
		array(
			'primary' => __( 'Основное меню', 'bishkek-park' ),
		)
	);
}
add_action( 'after_setup_theme', 'bishkek_park_setup' );

/**
 * Enqueue theme stylesheets and homepage-specific assets.
 */
function bishkek_park_enqueue_assets() {
	wp_enqueue_style(
		'bishkek-park-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'bishkek-park-style',
		get_stylesheet_uri(),
		array( 'bishkek-park-fonts' ),
		BISHKEK_PARK_VERSION
	);

	wp_enqueue_script(
		'bishkek-park-header',
		BISHKEK_PARK_URI . '/assets/js/header.js',
		array(),
		filemtime( BISHKEK_PARK_DIR . '/assets/js/header.js' ),
		true
	);

	$bp_is_shop_catalog = is_post_type_archive( 'bp_shop' );

	// front-page.css/.bp-shops-grid & .bp-shop-card also back the shop
	// catalog archive's card grid, so it shares that stylesheet.
	if ( is_front_page() || $bp_is_shop_catalog ) {
		wp_enqueue_style(
			'bishkek-park-front-page',
			BISHKEK_PARK_URI . '/assets/css/front-page.css',
			array( 'bishkek-park-style' ),
			filemtime( BISHKEK_PARK_DIR . '/assets/css/front-page.css' )
		);
	}

	if ( is_front_page() ) {
		wp_enqueue_script(
			'bishkek-park-front-page',
			BISHKEK_PARK_URI . '/assets/js/front-page.js',
			array(),
			filemtime( BISHKEK_PARK_DIR . '/assets/js/front-page.js' ),
			true
		);
	}

	if ( $bp_is_shop_catalog ) {
		wp_enqueue_style(
			'bishkek-park-shop-catalog',
			BISHKEK_PARK_URI . '/assets/css/shop-catalog.css',
			array( 'bishkek-park-front-page' ),
			filemtime( BISHKEK_PARK_DIR . '/assets/css/shop-catalog.css' )
		);

		wp_enqueue_script(
			'bishkek-park-shop-catalog',
			BISHKEK_PARK_URI . '/assets/js/shop-catalog.js',
			array(),
			filemtime( BISHKEK_PARK_DIR . '/assets/js/shop-catalog.js' ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'bishkek_park_enqueue_assets' );
