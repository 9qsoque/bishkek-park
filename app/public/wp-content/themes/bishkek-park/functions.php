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

// How long (ms) the loading screen stays up on the first page of a browser
// session, so its logo animation is actually seen; see
// bishkek_park_preloader_head_script().
define( 'BISHKEK_PARK_PRELOADER_MIN_MS', 1400 );

require BISHKEK_PARK_DIR . '/inc/post-types.php';
require BISHKEK_PARK_DIR . '/inc/meta-boxes.php';
require BISHKEK_PARK_DIR . '/inc/translatable-strings.php';
require BISHKEK_PARK_DIR . '/inc/seo.php';
require BISHKEK_PARK_DIR . '/inc/cinematica-settings.php';
require BISHKEK_PARK_DIR . '/inc/cinematica-api.php';
require BISHKEK_PARK_DIR . '/inc/parking-settings.php';
require BISHKEK_PARK_DIR . '/inc/mall-map.php';
require BISHKEK_PARK_DIR . '/inc/admin-menu.php';

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
 * translation exists yet — so untranslated bp_shop/bp_event entries still
 * show up (in Russian) instead of disappearing on the KY site.
 */
function bishkek_park_get_localized_post_id( $post_id ) {
	if ( ! function_exists( 'pll_current_language' ) || ! function_exists( 'pll_get_post' ) ) {
		return $post_id;
	}

	$translated_id = pll_get_post( $post_id, pll_current_language() );

	return $translated_id ? $translated_id : $post_id;
}

/**
 * The display text for a floor's own number/code, e.g. "2" or (for the
 * basement level, stored internally as "-1" — see bishkek_park_get_map_floors()
 * in inc/mall-map.php) "М1". Only the display text changes here; the
 * underlying floor key stays "-1" everywhere it's used as data (post meta,
 * map assignments, the `{floor}-floor.svg` filename, the `?floor=` query
 * arg) so existing shop assignments and URLs keep working. Every place that
 * shows a floor number to a visitor or editor should go through this
 * (directly, or via bishkek_park_get_shop_floor_label() below) instead of
 * echoing the raw floor value.
 */
function bishkek_park_get_floor_number_label( $floor ) {
	return '-1' === $floor ? 'М1' : $floor;
}

/**
 * Formats a bp_shop's raw `_bp_floor` meta value (stored as a bare number,
 * e.g. "2") into the "Этаж 2" label shown on shop cards, catalog filters,
 * and the shop detail page, translated via Polylang. Returns '' if no floor
 * is set. Editors enter just the number in wp-admin — see
 * bishkek_park_sanitize_shop_floor() in inc/post-types.php, which strips a
 * leading "Этаж" if someone types the old style anyway.
 */
function bishkek_park_get_shop_floor_label( $floor ) {
	if ( '' === $floor ) {
		return '';
	}

	return pll_esc_html__( 'Этаж' ) . ' ' . esc_html( bishkek_park_get_floor_number_label( $floor ) );
}

/**
 * Formats a bp_event's raw `_bp_date` meta value — stored as an ISO `Y-m-d`
 * string from the admin date-picker input (see `_bp_date`'s 'date' field
 * type in inc/meta-boxes.php) — into the "15 июля 2025" display string used
 * on event cards and the single event page. Falls back to returning the raw
 * value untouched if it isn't a parseable ISO date (e.g. legacy free-text
 * entries from before the date-picker existed), so old content doesn't just
 * disappear.
 */
function bishkek_park_format_event_date( $date ) {
	if ( '' === $date ) {
		return '';
	}

	$parsed = DateTime::createFromFormat( 'Y-m-d', $date );
	if ( ! $parsed ) {
		return $date;
	}

	$months = array(
		1  => 'января',
		2  => 'февраля',
		3  => 'марта',
		4  => 'апреля',
		5  => 'мая',
		6  => 'июня',
		7  => 'июля',
		8  => 'августа',
		9  => 'сентября',
		10 => 'октября',
		11 => 'ноября',
		12 => 'декабря',
	);

	return (int) $parsed->format( 'j' ) . ' ' . $months[ (int) $parsed->format( 'n' ) ] . ' ' . $parsed->format( 'Y' );
}

/**
 * Registers the Funcity page template with the Page edit screen's template
 * dropdown. The file lives in template-parts/funcity/ per the theme's
 * template-parts/{something}/ convention, but WordPress only auto-discovers
 * `Template Name:` headers one directory deep — so it's registered here
 * explicitly. Once assigned to a page, core's get_page_template() resolves
 * the relative path itself; no page_template filter is needed.
 */
function bishkek_park_register_page_templates( $templates ) {
	$templates['template-parts/funcity/page-funcity.php']   = 'Funcity';
	$templates['template-parts/contacts/page-contacts.php'] = 'Контакты';
	$templates['template-parts/map/page-map.php']           = 'Карта ТЦ';
	$templates['template-parts/about/page-about.php']       = 'О нас';

	return $templates;
}
add_filter( 'theme_page_templates', 'bishkek_park_register_page_templates' );

/**
 * URL of the page the Funcity template is assigned to, for nav links.
 * Queries the default language (see CLAUDE.md "Polylang CPT translation")
 * and swaps in the current-language translation when one exists, so the
 * link doesn't vanish on the KY site while the page is untranslated.
 * Returns '#' (matching the header's other not-yet-built links) if no page
 * uses the template yet.
 */
function bishkek_park_get_funcity_page_url() {
	static $url = null;

	if ( null === $url ) {
		$pages = get_posts(
			array(
				'post_type'      => 'page',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'lang'           => function_exists( 'pll_default_language' ) ? pll_default_language() : '',
				'meta_key'       => '_wp_page_template',
				'meta_value'     => 'template-parts/funcity/page-funcity.php',
			)
		);

		$url = $pages ? get_permalink( bishkek_park_get_localized_post_id( $pages[0] ) ) : '#';
	}

	return $url;
}

/**
 * Turns a displayed phone number (e.g. "+996 (312) 312 031") into a `tel:`
 * href by keeping only digits and a leading `+`. Used by the Контакты page
 * template so editors can type the number naturally in wp-admin without
 * having to also enter a separate dial string.
 */
function bishkek_park_get_tel_href( $phone ) {
	return 'tel:' . preg_replace( '/[^0-9+]/', '', $phone );
}

/**
 * The leasing/sales WhatsApp numbers shown in the footer and on the contacts
 * page (plus the leasing CTA button, which uses the 'leasing' entry), keyed
 * by purpose. `label` is a Polylang-registered string (inc/translatable-strings.php)
 * to print with pll_esc_html_e(); `url` opens a WhatsApp chat (wa.me takes
 * the number as bare digits).
 */
function bishkek_park_get_whatsapp_contacts() {
	$contacts = array(
		'leasing' => array(
			'label'  => 'Аренда',
			'number' => '+996 555 777 945',
		),
		'sales'   => array(
			'label'  => 'Продажи',
			'number' => '+996 998 111 333',
		),
	);

	foreach ( $contacts as &$contact ) {
		$contact['url'] = 'https://wa.me/' . preg_replace( '/\D/', '', $contact['number'] );
	}
	unset( $contact );

	return $contacts;
}

/**
 * URL of the page the Контакты (contacts) template is assigned to, for nav
 * links. Same pattern as bishkek_park_get_funcity_page_url() above.
 */
function bishkek_park_get_contacts_page_url() {
	static $url = null;

	if ( null === $url ) {
		$pages = get_posts(
			array(
				'post_type'      => 'page',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'lang'           => function_exists( 'pll_default_language' ) ? pll_default_language() : '',
				'meta_key'       => '_wp_page_template',
				'meta_value'     => 'template-parts/contacts/page-contacts.php',
			)
		);

		$url = $pages ? get_permalink( bishkek_park_get_localized_post_id( $pages[0] ) ) : '#';
	}

	return $url;
}

/**
 * URL of the page the Карта ТЦ (mall map) template is assigned to, for nav
 * links. Same pattern as bishkek_park_get_funcity_page_url() above.
 */
function bishkek_park_get_mall_map_page_url() {
	static $url = null;

	if ( null === $url ) {
		$pages = get_posts(
			array(
				'post_type'      => 'page',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'lang'           => function_exists( 'pll_default_language' ) ? pll_default_language() : '',
				'meta_key'       => '_wp_page_template',
				'meta_value'     => 'template-parts/map/page-map.php',
			)
		);

		$url = $pages ? get_permalink( bishkek_park_get_localized_post_id( $pages[0] ) ) : '#';
	}

	return $url;
}

/**
 * URL of the page the О нас (about) template is assigned to, for nav links.
 * Same pattern as bishkek_park_get_funcity_page_url() above.
 */
function bishkek_park_get_about_page_url() {
	static $url = null;

	if ( null === $url ) {
		$pages = get_posts(
			array(
				'post_type'      => 'page',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'lang'           => function_exists( 'pll_default_language' ) ? pll_default_language() : '',
				'meta_key'       => '_wp_page_template',
				'meta_value'     => 'template-parts/about/page-about.php',
			)
		);

		$url = $pages ? get_permalink( bishkek_park_get_localized_post_id( $pages[0] ) ) : '#';
	}

	return $url;
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

	wp_enqueue_script(
		'bishkek-park-reveal',
		BISHKEK_PARK_URI . '/assets/js/reveal.js',
		array(),
		filemtime( BISHKEK_PARK_DIR . '/assets/js/reveal.js' ),
		true
	);

	$bp_is_shop_catalog  = is_post_type_archive( 'bp_shop' );
	$bp_is_single_shop   = is_singular( 'bp_shop' );
	$bp_is_cafe_catalog  = is_post_type_archive( 'bp_cafe' );
	$bp_is_single_cafe   = is_singular( 'bp_cafe' );
	$bp_is_event_catalog = is_post_type_archive( 'bp_event' );
	$bp_is_single_event  = is_singular( 'bp_event' );
	$bp_is_funcity       = is_page_template( 'template-parts/funcity/page-funcity.php' );
	$bp_is_contacts      = is_page_template( 'template-parts/contacts/page-contacts.php' );
	$bp_is_mall_map      = is_page_template( 'template-parts/map/page-map.php' );
	$bp_is_about         = is_page_template( 'template-parts/about/page-about.php' );

	// front-page.css/.bp-shops-grid & .bp-shop-card also back the shop and
	// cafe catalog archives' card grids and the single shop/cafe pages'
	// "related" grids — bp_cafe deliberately reuses bp_shop's card markup
	// (see bishkek_park_register_cafe_post_type()), so all five share this
	// stylesheet instead of duplicating it. The events catalog archive and
	// single event page reuse the same file for .bp-back-link and its
	// .bp-events-grid/.bp-event-card (already defined there for the homepage
	// teaser section). The Funcity page reuses its .bp-back-link and
	// .bp-section styles the same way, as does the Карта ТЦ (mall map) page
	// and the О нас (about) page.
	if ( is_front_page() || $bp_is_shop_catalog || $bp_is_single_shop || $bp_is_cafe_catalog || $bp_is_single_cafe || $bp_is_event_catalog || $bp_is_single_event || $bp_is_funcity || $bp_is_contacts || $bp_is_mall_map || $bp_is_about ) {
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

	// shop-catalog.css/.js implement the floor-filter grid generically (via
	// data-bp-shop-filter/-grid/-floor attributes, not shop-specific
	// selectors), so the bp_cafe archive reuses them as-is too.
	if ( $bp_is_shop_catalog || $bp_is_cafe_catalog ) {
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

	// shop-single.css's .bp-shop-detail-* classes are likewise reused as-is
	// on the single bp_cafe page.
	if ( $bp_is_single_shop || $bp_is_single_cafe ) {
		wp_enqueue_style(
			'bishkek-park-shop-single',
			BISHKEK_PARK_URI . '/assets/css/shop-single.css',
			array( 'bishkek-park-front-page' ),
			filemtime( BISHKEK_PARK_DIR . '/assets/css/shop-single.css' )
		);
	}

	// event-catalog.css/.js implement the category-filter grid: a different
	// shape from the shop/cafe floor filter (categories are free-text, not a
	// fixed set), so bp_event gets its own pair rather than reusing
	// shop-catalog.css/.js.
	if ( $bp_is_event_catalog ) {
		wp_enqueue_style(
			'bishkek-park-event-catalog',
			BISHKEK_PARK_URI . '/assets/css/event-catalog.css',
			array( 'bishkek-park-front-page' ),
			filemtime( BISHKEK_PARK_DIR . '/assets/css/event-catalog.css' )
		);

		wp_enqueue_script(
			'bishkek-park-event-catalog',
			BISHKEK_PARK_URI . '/assets/js/event-catalog.js',
			array(),
			filemtime( BISHKEK_PARK_DIR . '/assets/js/event-catalog.js' ),
			true
		);
	}

	// event-single.css's .bp-event-detail-* classes are single-event-page-only.
	if ( $bp_is_single_event ) {
		wp_enqueue_style(
			'bishkek-park-event-single',
			BISHKEK_PARK_URI . '/assets/css/event-single.css',
			array( 'bishkek-park-front-page' ),
			filemtime( BISHKEK_PARK_DIR . '/assets/css/event-single.css' )
		);
	}

	// funcity.css's .bp-funcity-* classes are Funcity-template-only. The About
	// page also loads it, since its "Остались вопросы?" CTA reuses the
	// .bp-funcity-cta markup/classes verbatim instead of duplicating them.
	if ( $bp_is_funcity || $bp_is_about ) {
		wp_enqueue_style(
			'bishkek-park-funcity',
			BISHKEK_PARK_URI . '/assets/css/funcity.css',
			array( 'bishkek-park-front-page' ),
			filemtime( BISHKEK_PARK_DIR . '/assets/css/funcity.css' )
		);
	}

	// about.css's .bp-about-* classes are About-page-only.
	if ( $bp_is_about ) {
		wp_enqueue_style(
			'bishkek-park-about',
			BISHKEK_PARK_URI . '/assets/css/about.css',
			array( 'bishkek-park-front-page' ),
			filemtime( BISHKEK_PARK_DIR . '/assets/css/about.css' )
		);
	}

	// contacts.css's .bp-contacts-*/.bp-faq-* classes are contacts-page-only.
	// Leaflet (the contacts map, see assets/js/contacts-map.js) is vendored in
	// assets/vendor/leaflet rather than pulled from a CDN; contacts.css depends
	// on its stylesheet so the theme's map overrides load after Leaflet's own.
	if ( $bp_is_contacts ) {
		wp_enqueue_style(
			'bishkek-park-leaflet',
			BISHKEK_PARK_URI . '/assets/vendor/leaflet/leaflet.css',
			array(),
			'1.9.4'
		);

		wp_enqueue_style(
			'bishkek-park-contacts',
			BISHKEK_PARK_URI . '/assets/css/contacts.css',
			array( 'bishkek-park-front-page', 'bishkek-park-leaflet' ),
			filemtime( BISHKEK_PARK_DIR . '/assets/css/contacts.css' )
		);

		wp_enqueue_script(
			'bishkek-park-leaflet',
			BISHKEK_PARK_URI . '/assets/vendor/leaflet/leaflet.js',
			array(),
			'1.9.4',
			true
		);

		wp_enqueue_script(
			'bishkek-park-contacts-map',
			BISHKEK_PARK_URI . '/assets/js/contacts-map.js',
			array( 'bishkek-park-leaflet' ),
			filemtime( BISHKEK_PARK_DIR . '/assets/js/contacts-map.js' ),
			true
		);
	}

	// mall-map.css/.js implement the floor switcher (prev/next + the
	// data-bp-mall-map-* attributes driving which floor plan is shown) —
	// Карта ТЦ template-only.
	if ( $bp_is_mall_map ) {
		wp_enqueue_style(
			'bishkek-park-mall-map',
			BISHKEK_PARK_URI . '/assets/css/mall-map.css',
			array( 'bishkek-park-front-page' ),
			filemtime( BISHKEK_PARK_DIR . '/assets/css/mall-map.css' )
		);

		wp_enqueue_script(
			'bishkek-park-mall-map',
			BISHKEK_PARK_URI . '/assets/js/mall-map.js',
			array(),
			filemtime( BISHKEK_PARK_DIR . '/assets/js/mall-map.js' ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'bishkek_park_enqueue_assets' );

/**
 * Turn on the scroll-reveal hidden state (html.bp-reveal) before first paint,
 * so [data-bp-reveal] blocks don't flash visible and then vanish while
 * assets/js/reveal.js loads in the footer. Skipped for reduced-motion users;
 * if reveal.js hasn't marked itself ready within a few seconds (failed or
 * slow load) the class is removed again so content is never stuck hidden.
 */
function bishkek_park_reveal_head_script() {
	?>
	<script>
	( function () {
		var root = document.documentElement;
		if ( ! window.matchMedia || window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
			return;
		}
		root.classList.add( 'bp-reveal' );
		window.setTimeout( function () {
			if ( ! root.classList.contains( 'bp-reveal-ready' ) ) {
				root.classList.remove( 'bp-reveal' );
			}
		}, 4000 );
	}() );
	</script>
	<?php
}
add_action( 'wp_head', 'bishkek_park_reveal_head_script', 1 );

/**
 * Drives the loading screen (template-parts/preloader/preloader.php): adds
 * html.bp-preloading before first paint, then on window `load` swaps it for
 * html.bp-preloaded (which fades the overlay out) and fires a `bp:preloaded`
 * event that assets/js/reveal.js waits for, so scroll-reveal animations play
 * as the overlay fades instead of hidden behind it.
 *
 * The first page of a browser session keeps the screen up for at least
 * BISHKEK_PARK_PRELOADER_MIN_MS so the logo animation is seen; later pages
 * hide it as soon as they've loaded. A hard timeout guarantees the overlay
 * never sticks if `load` is held up by a slow third-party resource.
 */
function bishkek_park_preloader_head_script() {
	?>
	<script>
	( function () {
		var root = document.documentElement;
		var minDuration = 0;
		var maxDuration = 8000;
		var start = Date.now();
		var hidden = false;

		try {
			if ( ! window.sessionStorage.getItem( 'bpPreloaderSeen' ) ) {
				window.sessionStorage.setItem( 'bpPreloaderSeen', '1' );
				minDuration = <?php echo (int) BISHKEK_PARK_PRELOADER_MIN_MS; ?>;
			}
		} catch ( e ) {}

		root.classList.add( 'bp-preloading' );

		var hide = function () {
			if ( hidden ) {
				return;
			}
			hidden = true;
			root.classList.remove( 'bp-preloading' );
			root.classList.add( 'bp-preloaded' );
			document.dispatchEvent( new CustomEvent( 'bp:preloaded' ) );
		};

		window.addEventListener( 'load', function () {
			window.setTimeout( hide, Math.max( 0, minDuration - ( Date.now() - start ) ) );
		} );
		window.setTimeout( hide, maxDuration );
	}() );
	</script>
	<?php
}
add_action( 'wp_head', 'bishkek_park_preloader_head_script', 1 );
