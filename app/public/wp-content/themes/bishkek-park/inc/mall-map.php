<?php
/**
 * Interactive mall map for every floor that has one: lets an editor attach
 * an already-published post (bp_shop and/or bp_cafe, depending on the
 * floor) — or, on floor 3, a free-text label+URL — to each physical unit on
 * that floor's assets/icons/{floor}-floor.svg plan. Changing a tenant is a
 * wp-admin dropdown instead of a new SVG export.
 *
 * Each floor SVG's own room-outline <path> elements are tagged with a
 * `data-bp-unit="unit-N"` attribute baked into the asset file itself — the
 * actual, pixel-exact shape of that unit as drawn, not an approximated CSS
 * box (an earlier version of this feature did that and it visibly didn't
 * line up with the real walls). Both the front end
 * (template-parts/map/page-map.php) and the admin preview below inline that
 * SVG file directly and hook into those `data-bp-unit` paths via
 * bishkek_park_render_map_svg() instead of reconstructing the geometry in
 * CSS/JS. `cx`/`cy` per unit (in the SVG's own coordinate space) is only
 * used to place the text label at that unit's centroid.
 *
 * What a unit can be assigned to depends on the floor
 * (bishkek_park_get_map_floor_post_types()): floors 0/-1/1/2 all allow
 * either bp_shop or bp_cafe (one combined <select> per unit, grouped by
 * post type — see bishkek_park_render_map_admin_row()), floor 3 has no CPT
 * at all (Синематика/Funcity/etc. aren't posts) so it uses a different
 * assignment kind entirely — free-text label + URL, stored separately and
 * rendered as plain text inputs — see bishkek_park_map_floor_uses_links()
 * and bishkek_park_get_map_link().
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Physical units on a floor's plan: hint (admin display only — the tenant
 * at the time this was built, or just a rough "top/middle/bottom,
 * left/center/right" position for floors with no readable labels in the
 * source art; has no effect on the site and doesn't need to stay accurate)
 * plus cx/cy (that unit's centroid in the floor SVG's own coordinate
 * space, for placing its text label). Empty for any floor with no map data.
 */
function bishkek_park_get_map_units( $floor ) {
	$units = array(
		'0' => array(
			// Intersport and Puma share one open floor area with no wall
			// between them in the source artwork, so this is genuinely one
			// L-shaped path/unit, not an approximation.
			'unit-1'  => array( 'hint' => 'Intersport / Puma', 'cx' => 483.0, 'cy' => 480.0 ),
			'unit-2'  => array( 'hint' => 'Mexx', 'cx' => 725.0, 'cy' => 525.2 ),
			'unit-3'  => array( 'hint' => 'Campo Marzio', 'cx' => 691.5, 'cy' => 608.1 ),
			'unit-4'  => array( 'hint' => 'Koton', 'cx' => 961.2, 'cy' => 524.8 ),
			'unit-5'  => array( 'hint' => 'Mango', 'cx' => 1035.1, 'cy' => 812.3 ),
			'unit-6'  => array( 'hint' => 'Adidas', 'cx' => 438.5, 'cy' => 806.3 ),
			'unit-7'  => array( 'hint' => 'Kulikov', 'cx' => 694.4, 'cy' => 749.3 ),
			'unit-8'  => array( 'hint' => 'Beeline', 'cx' => 843.9, 'cy' => 905.8 ),
			'unit-9'  => array( 'hint' => 'Swiss Time', 'cx' => 610.9, 'cy' => 904.1 ),
			'unit-10' => array( 'hint' => 'Diamant', 'cx' => 610.8, 'cy' => 959.0 ),
			'unit-11' => array( 'hint' => 'Caprice', 'cx' => 610.9, 'cy' => 1013.6 ),
			'unit-12' => array( 'hint' => 'April', 'cx' => 1042.6, 'cy' => 1031.1 ),
			'unit-13' => array( 'hint' => 'Twinset', 'cx' => 1056.7, 'cy' => 1093.7 ),
			'unit-14' => array( 'hint' => 'Hugo', 'cx' => 1056.9, 'cy' => 1199.4 ),
			'unit-15' => array( 'hint' => 'Armani Exchange', 'cx' => 1056.8, 'cy' => 1308.9 ),
			'unit-16' => array( 'hint' => 'KICB', 'cx' => 1056.7, 'cy' => 1435.8 ),
			'unit-17' => array( 'hint' => 'Nursace', 'cx' => 961.9, 'cy' => 1450.7 ),
			'unit-18' => array( 'hint' => 'Euphoria Parfum', 'cx' => 567.8, 'cy' => 1274.8 ),
			'unit-19' => array( 'hint' => 'Terranova', 'cx' => 396.1, 'cy' => 1450.6 ),
			'unit-20' => array( 'hint' => 'Sadik Yilmaz', 'cx' => 596.2, 'cy' => 1442.4 ),
			'unit-21' => array( 'hint' => 'Sokolov', 'cx' => 703.7, 'cy' => 1442.4 ),
			'unit-22' => array( 'hint' => 'Inglot', 'cx' => 608.2, 'cy' => 1211.5 ),
			'unit-23' => array( 'hint' => 'Nature Republic', 'cx' => 589.5, 'cy' => 1356.0 ),
			// The rest were small enclosed shapes on the plan with no shop
			// name drawn even in the original artwork (little kiosks/nooks
			// along the walkway and the atrium island) — still real physical
			// spots someone could lease, so they get a unit too, just with an
			// honest "where this is" hint instead of a guessed shop name.
			'unit-24' => array( 'hint' => 'Italiano (между Koton и Mango)', 'cx' => 970.9, 'cy' => 683.0 ),
			'unit-25' => array( 'hint' => 'ниша под Mango', 'cx' => 1015.2, 'cy' => 929.7 ),
			'unit-26' => array( 'hint' => 'ниша у Adidas (нижняя)', 'cx' => 653.4, 'cy' => 1143.2 ),
			'unit-27' => array( 'hint' => 'ниша у Adidas (верхняя)', 'cx' => 653.4, 'cy' => 1088.8 ),
			'unit-29' => array( 'hint' => 'ниша у атриума (слева, нижняя)', 'cx' => 719.0, 'cy' => 1114.2 ),
			'unit-30' => array( 'hint' => 'ниша у атриума (справа, верхняя)', 'cx' => 872.4, 'cy' => 779.5 ),
			'unit-31' => array( 'hint' => 'ниша у атриума (справа, нижняя)', 'cx' => 948.1, 'cy' => 1107.0 ),
			'unit-32' => array( 'hint' => 'ниша у атриума (слева, верхняя)', 'cx' => 718.8, 'cy' => 1052.8 ),
			'unit-33' => array( 'hint' => 'ниша у атриума (сверху, левая)', 'cx' => 793.4, 'cy' => 763.8 ),
			'unit-34' => array( 'hint' => 'ниша у атриума (сверху, правая)', 'cx' => 945.7, 'cy' => 969.5 ),
		),
		'-1' => array(
			'unit-1'  => array( 'hint' => 'верх, левая', 'cx' => 238.0, 'cy' => 223.7 ),
			'unit-2'  => array( 'hint' => 'верх, правая', 'cx' => 634.8, 'cy' => 223.7 ),
			'unit-3'  => array( 'hint' => 'середина, правая', 'cx' => 709.3, 'cy' => 409.5 ),
			'unit-4'  => array( 'hint' => 'середина, левая', 'cx' => 192.2, 'cy' => 563.1 ),
			'unit-5'  => array( 'hint' => 'середина, левая', 'cx' => 151.9, 'cy' => 477.2 ),
			'unit-6'  => array( 'hint' => 'верх, центр', 'cx' => 432.3, 'cy' => 378.3 ),
			'unit-7'  => array( 'hint' => 'верх, центр', 'cx' => 432.2, 'cy' => 172.1 ),
			'unit-8'  => array( 'hint' => 'верх, центр', 'cx' => 432.5, 'cy' => 266.7 ),
			'unit-9'  => array( 'hint' => 'середина, левая', 'cx' => 259.0, 'cy' => 477.0 ),
			'unit-10' => array( 'hint' => 'середина, левая', 'cx' => 241.3, 'cy' => 405.2 ),
			'unit-11' => array( 'hint' => 'середина, левая', 'cx' => 256.5, 'cy' => 633.3 ),
			'unit-12' => array( 'hint' => 'низ, левая', 'cx' => 150.9, 'cy' => 918.3 ),
			'unit-13' => array( 'hint' => 'низ, левая', 'cx' => 216.6, 'cy' => 1044.8 ),
			// 14 and 15 used to be two separate units; merged into one shop
			// space (bishkek_park_get_map_svg_source( '-1' )) since nothing
			// actually separates them on the plan.
			'unit-14' => array( 'hint' => 'низ, правая', 'cx' => 703.4, 'cy' => 1048.3 ),
			'unit-16' => array( 'hint' => 'середина, правая', 'cx' => 733.8, 'cy' => 662.9 ),
			'unit-17' => array( 'hint' => 'низ, центр', 'cx' => 344.3, 'cy' => 1071.3 ),
			'unit-18' => array( 'hint' => 'низ, центр', 'cx' => 429.3, 'cy' => 1071.7 ),
			'unit-19' => array( 'hint' => 'низ, центр', 'cx' => 506.2, 'cy' => 1071.7 ),
			'unit-20' => array( 'hint' => 'середина, правая', 'cx' => 733.8, 'cy' => 750.5 ),
			'unit-21' => array( 'hint' => 'низ, правая', 'cx' => 585.3, 'cy' => 1071.7 ),
			'unit-22' => array( 'hint' => 'низ, правая', 'cx' => 733.8, 'cy' => 902.7 ),
			'unit-23' => array( 'hint' => 'низ, правая', 'cx' => 733.8, 'cy' => 851.3 ),
			'unit-24' => array( 'hint' => 'низ, правая', 'cx' => 733.8, 'cy' => 808.8 ),
			'unit-25' => array( 'hint' => 'низ, центр (слева от атриума)', 'cx' => 390.0, 'cy' => 910.0 ),
			'unit-26' => array( 'hint' => 'низ, центр', 'cx' => 312.3, 'cy' => 743.7 ),
			'unit-31' => array( 'hint' => 'середина, правая (у атриума)', 'cx' => 614.8, 'cy' => 676.9 ),
			'unit-34' => array( 'hint' => 'середина, центр (у атриума)', 'cx' => 456.8, 'cy' => 584.7 ),
			'unit-35' => array( 'hint' => 'середина, центр (у атриума)', 'cx' => 456.4, 'cy' => 633.8 ),
			'unit-36' => array( 'hint' => 'середина, центр (у атриума)', 'cx' => 388.9, 'cy' => 744.2 ),
			'unit-37' => array( 'hint' => 'середина, центр (у атриума)', 'cx' => 389.0, 'cy' => 662.4 ),
			'unit-38' => array( 'hint' => 'середина, центр (у атриума)', 'cx' => 391.0, 'cy' => 590.4 ),
			'unit-39' => array( 'hint' => 'середина, центр (у атриума)', 'cx' => 515.1, 'cy' => 482.5 ),
		),
		'1' => array(
			'unit-1'  => array( 'hint' => 'низ, центр (весь нижний ряд)', 'cx' => 398.4, 'cy' => 1012.5 ),
			'unit-2'  => array( 'hint' => 'середина, правая', 'cx' => 733.8, 'cy' => 676.8 ),
			'unit-3'  => array( 'hint' => 'низ, левая', 'cx' => 246.9, 'cy' => 795.7 ),
			'unit-4'  => array( 'hint' => 'низ, левая', 'cx' => 246.8, 'cy' => 887.0 ),
			'unit-5'  => array( 'hint' => 'низ, правая', 'cx' => 733.8, 'cy' => 803.2 ),
			'unit-6'  => array( 'hint' => 'низ, правая', 'cx' => 733.8, 'cy' => 886.3 ),
			'unit-7'  => array( 'hint' => 'середина, центр', 'cx' => 333.3, 'cy' => 688.2 ),
			'unit-8'  => array( 'hint' => 'низ, центр', 'cx' => 333.3, 'cy' => 736.6 ),
			'unit-9'  => array( 'hint' => 'верх, правая', 'cx' => 632.9, 'cy' => 103.0 ),
			'unit-10' => array( 'hint' => 'верх, левая', 'cx' => 117.9, 'cy' => 107.8 ),
			'unit-11' => array( 'hint' => 'верх, центр', 'cx' => 394.5, 'cy' => 103.0 ),
			'unit-12' => array( 'hint' => 'верх, левая', 'cx' => 236.0, 'cy' => 103.0 ),
			'unit-13' => array( 'hint' => 'середина, левая', 'cx' => 76.4, 'cy' => 409.3 ),
			'unit-14' => array( 'hint' => 'середина, центр', 'cx' => 289.2, 'cy' => 534.0 ),
			'unit-15' => array( 'hint' => 'середина, левая', 'cx' => 149.3, 'cy' => 534.0 ),
			'unit-16' => array( 'hint' => 'середина, центр', 'cx' => 289.2, 'cy' => 616.3 ),
			'unit-17' => array( 'hint' => 'середина, левая', 'cx' => 213.5, 'cy' => 534.0 ),
			'unit-20' => array( 'hint' => 'середина, правая', 'cx' => 711.0, 'cy' => 378.1 ),
			'unit-21' => array( 'hint' => 'верх, правая', 'cx' => 710.5, 'cy' => 276.7 ),
			'unit-22' => array( 'hint' => 'середина, правая', 'cx' => 711.3, 'cy' => 472.3 ),
			'unit-23' => array( 'hint' => 'середина, правая (у атриума)', 'cx' => 689.9, 'cy' => 534.9 ),
			'unit-24' => array( 'hint' => 'середина, центр (у атриума)', 'cx' => 390.6, 'cy' => 378.5 ),
			'unit-25' => array( 'hint' => 'верх, центр (у атриума)', 'cx' => 390.6, 'cy' => 320.4 ),
			'unit-26' => array( 'hint' => 'верх, центр (у атриума)', 'cx' => 276.3, 'cy' => 338.7 ),
			'unit-27' => array( 'hint' => 'середина, центр (у атриума)', 'cx' => 396.4, 'cy' => 719.1 ),
			'unit-28' => array( 'hint' => 'низ, центр (у атриума)', 'cx' => 396.4, 'cy' => 736.0 ),
			'unit-29' => array( 'hint' => 'середина, центр (у атриума)', 'cx' => 276.1, 'cy' => 425.4 ),
			'unit-30' => array( 'hint' => 'середина, правая (у атриума)', 'cx' => 547.8, 'cy' => 380.0 ),
			'unit-31' => array( 'hint' => 'середина, правая (у атриума)', 'cx' => 621.1, 'cy' => 578.2 ),
		),
		'2' => array(
			'unit-1'  => array( 'hint' => 'верх, левая', 'cx' => 157.3, 'cy' => 287.7 ),
			'unit-2'  => array( 'hint' => 'верх, центр', 'cx' => 388.2, 'cy' => 125.9 ),
			'unit-3'  => array( 'hint' => 'верх, правая', 'cx' => 634.4, 'cy' => 125.9 ),
			'unit-4'  => array( 'hint' => 'середина, центр', 'cx' => 272.9, 'cy' => 537.1 ),
			'unit-5'  => array( 'hint' => 'середина, центр', 'cx' => 280.7, 'cy' => 603.5 ),
			'unit-6'  => array( 'hint' => 'низ, левая', 'cx' => 158.0, 'cy' => 961.0 ),
			'unit-7'  => array( 'hint' => 'середина, правая', 'cx' => 724.5, 'cy' => 658.3 ),
			'unit-8'  => array( 'hint' => 'низ, левая', 'cx' => 254.8, 'cy' => 818.8 ),
			'unit-9'  => array( 'hint' => 'низ, правая', 'cx' => 724.5, 'cy' => 864.8 ),
			'unit-10' => array( 'hint' => 'низ, правая', 'cx' => 724.5, 'cy' => 782.9 ),
			'unit-11' => array( 'hint' => 'низ, правая', 'cx' => 724.5, 'cy' => 1021.0 ),
			'unit-12' => array( 'hint' => 'низ, правая', 'cx' => 678.7, 'cy' => 1049.2 ),
			'unit-13' => array( 'hint' => 'низ, правая', 'cx' => 724.5, 'cy' => 968.3 ),
			'unit-14' => array( 'hint' => 'низ, правая', 'cx' => 724.5, 'cy' => 926.8 ),
			'unit-17' => array( 'hint' => 'низ, центр (атриум)', 'cx' => 508.3, 'cy' => 807.9 ),
			'unit-20' => array( 'hint' => 'середина, правая', 'cx' => 710.1, 'cy' => 365.2 ),
			'unit-21' => array( 'hint' => 'верх, правая', 'cx' => 710.1, 'cy' => 286.7 ),
			'unit-22' => array( 'hint' => 'середина, правая', 'cx' => 710.6, 'cy' => 450.7 ),
			'unit-23' => array( 'hint' => 'середина, правая (у атриума)', 'cx' => 707.5, 'cy' => 503.1 ),
			'unit-24' => array( 'hint' => 'низ, центр (у атриума)', 'cx' => 511.9, 'cy' => 993.6 ),
			'unit-25' => array( 'hint' => 'середина, центр (у атриума)', 'cx' => 332.2, 'cy' => 697.2 ),
			'unit-26' => array( 'hint' => 'верх, центр (у атриума)', 'cx' => 466.0, 'cy' => 306.0 ),
			'unit-27' => array( 'hint' => 'середина, центр (у атриума)', 'cx' => 434.5, 'cy' => 371.5 ),
		),
		// Синематика/Funcity aren't posts, so these three don't get a
		// bp_shop/bp_cafe dropdown — see bishkek_park_map_floor_uses_links().
		'3' => array(
			'unit-1' => array( 'hint' => 'низ (полукругом вокруг атриума)', 'cx' => 399.8, 'cy' => 777.6 ),
			'unit-2' => array( 'hint' => 'верх, правая', 'cx' => 570.1, 'cy' => 248.5 ),
			'unit-3' => array( 'hint' => 'верх, левая', 'cx' => 199.2, 'cy' => 267.5 ),
		),
	);

	return isset( $units[ $floor ] ) ? $units[ $floor ] : array();
}

/**
 * Which post type(s) a floor's units can be assigned to. Floors with more
 * than one type get a single <select> per unit grouped with <optgroup> —
 * see bishkek_park_render_map_admin_row(). An empty array means the floor
 * doesn't use posts at all (see bishkek_park_map_floor_uses_links()).
 */
function bishkek_park_get_map_floor_post_types( $floor ) {
	$map = array(
		'0'  => array( 'bp_shop', 'bp_cafe' ),
		'-1' => array( 'bp_shop', 'bp_cafe' ),
		'1'  => array( 'bp_shop', 'bp_cafe' ),
		'2'  => array( 'bp_shop', 'bp_cafe' ),
	);

	return isset( $map[ $floor ] ) ? $map[ $floor ] : array();
}

/**
 * Floor 3 (Синематика / Funcity / ...) has no backing CPT — its units are
 * assigned a free-text label + URL instead of a post. See
 * bishkek_park_get_map_link() and bishkek_park_get_map_link_defaults().
 */
function bishkek_park_map_floor_uses_links( $floor ) {
	return '3' === $floor;
}

/**
 * Raw contents of a floor's SVG, cached per request — both the front-end
 * render and the admin preview need this same file.
 */
function bishkek_park_get_map_svg_source( $floor ) {
	static $cache = array();

	if ( isset( $cache[ $floor ] ) ) {
		return $cache[ $floor ];
	}

	$path = BISHKEK_PARK_DIR . '/assets/icons/' . $floor . '-floor.svg';

	$cache[ $floor ] = file_exists( $path ) ? file_get_contents( $path ) : '';

	return $cache[ $floor ];
}

/**
 * Splits a label into up to two lines so it fits inside its unit's SVG
 * label without overflowing tiny cells ("Armani Exchange" -> two tspans).
 */
function bishkek_park_split_map_label( $text ) {
	if ( mb_strlen( $text ) <= 12 || false === strpos( $text, ' ' ) ) {
		return array( $text );
	}

	$words = explode( ' ', $text );
	$mid   = (int) ceil( count( $words ) / 2 );

	return array(
		implode( ' ', array_slice( $words, 0, $mid ) ),
		implode( ' ', array_slice( $words, $mid ) ),
	);
}

/**
 * Every unit's stable position number within a floor (1-based, in the order
 * bishkek_park_get_map_units() defines them) — the same numbering the admin
 * plan preview has always shown (see bishkek_park_render_map_admin_floor()).
 * The front end reuses it as the fallback label for units too small to fit
 * their full name (see bishkek_park_get_map_label_markup()) so a shop's
 * number is identical whether you're looking at wp-admin or the live map.
 */
function bishkek_park_get_map_unit_numbers( $floor ) {
	$numbers = array();
	$number  = 1;

	foreach ( bishkek_park_get_map_units( $floor ) as $unit_id => $unit ) {
		$numbers[ $unit_id ] = $number;
		++$number;
	}

	return $numbers;
}

/**
 * <text> (one or two <tspan> lines) centered on a unit's centroid — vertical
 * centering uses a `dy="0.35em"` baseline shift rather than
 * `dominant-baseline="middle"`, which some browsers render with a visible
 * upward offset (text sitting noticeably above center, occasionally crossing
 * the unit's top edge). When $wrap_fallback is true, the text is wrapped
 * together with an empty numbered fallback (bare text, no background shape)
 * that assets/js/mall-map.js fills in and reveals in place of the name —
 * toggling a .is-compact class on the wrapping <g> and auto-fitting its own
 * font-size the same way it does the name — for units too small to fit the
 * name at any readable size. That JS assigns the fallback number itself (a
 * separate 1, 2, 3... sequence counting only the units on that floor that
 * actually end up needing it, not every labeled unit) and lists it in the
 * floor's legend (data-bp-mall-map-legend in
 * template-parts/map/page-map.php) via this markup's data-bp-map-name
 * attribute, so a name that doesn't fit on the plan is still findable "6 =
 * Kofemania" style. The admin plan preview passes $wrap_fallback as false
 * (the default), since it only ever renders a bare number as the whole
 * label to begin with and so has no fallback to wrap.
 */
function bishkek_park_get_map_label_markup( $cx, $cy, $text, $css_class, $wrap_fallback = false ) {
	$lines = bishkek_park_split_map_label( $text );

	if ( 1 === count( $lines ) ) {
		$text_markup = sprintf(
			'<text class="%s" x="%s" y="%s" text-anchor="middle" dy="0.35em">%s</text>',
			esc_attr( $css_class ),
			esc_attr( $cx ),
			esc_attr( $cy ),
			esc_html( $lines[0] )
		);
	} else {
		$text_markup = sprintf(
			'<text class="%1$s" x="%2$s" y="%3$s" text-anchor="middle"><tspan x="%2$s" dy="-0.5em">%4$s</tspan><tspan x="%2$s" dy="1.1em">%5$s</tspan></text>',
			esc_attr( $css_class ),
			esc_attr( $cx ),
			esc_attr( $cy ),
			esc_html( $lines[0] ),
			esc_html( $lines[1] )
		);
	}

	if ( ! $wrap_fallback ) {
		return $text_markup;
	}

	return sprintf(
		'<g class="bp-mall-map-unit-label-group" data-bp-map-name="%1$s">%2$s<text class="bp-mall-map-unit-fallback__num" x="%3$s" y="%4$s" text-anchor="middle" dy="0.35em"></text></g>',
		esc_attr( $text ),
		$text_markup,
		esc_attr( $cx ),
		esc_attr( $cy )
	);
}

/**
 * Inlines a floor's SVG (adding the theme's usual `bp-mall-map-floor__img`
 * class to its root so it keeps scaling exactly like the <img> it replaces)
 * and, for each entry in `$unit_markup`, wraps that unit's real path(s) in a
 * link (if `href` given) and/or drops a text label at its centroid (if
 * `label` given). This is what makes hover/click match the actual drawn
 * shape instead of an approximated box — see the file docblock.
 *
 * A linked unit's label is nested inside its own `<a>` (rather than batched
 * with the rest) so `.bp-mall-map-unit-link:hover .bp-mall-map-unit-label`
 * in mall-map.css only affects that unit's own label — with every label
 * appended as one batch at the end instead, `:hover ~` sibling matching
 * would light up every later unit's label too, not just the hovered one.
 * Unlinked labels (e.g. the admin preview's plain unit numbers, which have
 * no `href` to nest inside) still go through the batch.
 *
 * @param string $floor       Floor key, e.g. '0'.
 * @param array  $unit_markup unit_id => array( 'href' => string|null, 'label' => string|null, 'label_class' => string ).
 */
function bishkek_park_render_map_svg( $floor, array $unit_markup ) {
	$svg = bishkek_park_get_map_svg_source( $floor );

	if ( ! $svg ) {
		return '';
	}

	$svg = str_replace( '<svg ', '<svg class="bp-mall-map-floor__img" ', $svg );

	$units  = bishkek_park_get_map_units( $floor );
	$labels = '';

	foreach ( $unit_markup as $unit_id => $entry ) {
		if ( ! isset( $units[ $unit_id ] ) ) {
			continue;
		}

		$unit        = $units[ $unit_id ];
		$label_markup = ! empty( $entry['label'] )
			? bishkek_park_get_map_label_markup( $unit['cx'], $unit['cy'], $entry['label'], $entry['label_class'], ! empty( $entry['fallback'] ) )
			: '';

		if ( ! empty( $entry['href'] ) ) {
			$href = $entry['href'];
			// `+` (not a single match): a couple of units are drawn as more
			// than one <path> sharing the same data-bp-unit — e.g. a room
			// plus a same-colored decorative overlay on top of it (see
			// unit-25 on floor -1). Matching a whole consecutive run wraps
			// them all in one <a>, so `:hover path` in mall-map.css lights
			// up every piece together instead of just whichever one the
			// pointer happens to be over.
			$svg = preg_replace_callback(
				'/(?:<path\b[^>]*\bdata-bp-unit="' . preg_quote( $unit_id, '/' ) . '"[^>]*\/>\s*)+/',
				function ( $matches ) use ( $href, $label_markup ) {
					return '<a href="' . esc_url( $href ) . '" class="bp-mall-map-unit-link">' . $matches[0] . $label_markup . '</a>';
				},
				$svg
			);
		} elseif ( $label_markup ) {
			$labels .= $label_markup;
		}
	}

	if ( $labels ) {
		// Floor 0's SVG wraps its paths in a <g transform="translate(...)">
		// (left over from an earlier coordinate-offset step); the other
		// floors' paths are direct children of <svg>, no <g> at all. Labels
		// must land inside that <g> when it exists — their cx/cy assume the
		// same transform every path in there already gets — or they render
		// shifted by the full translate offset and end up outside the
		// visible viewBox. Fall back to right before </svg> when there's no
		// <g> to land in.
		if ( preg_match( '/<\/g>\s*<\/svg>\s*$/', $svg ) ) {
			$svg = preg_replace( '/<\/g>(\s*<\/svg>\s*)$/', $labels . '</g>$1', $svg, 1 );
		} else {
			$svg = preg_replace( '/<\/svg>\s*$/', $labels . '</svg>', $svg, 1 );
		}
	}

	return $svg;
}

/**
 * Every post a floor's units could be assigned to — published, canonical
 * (default-language) list per post type, same querying convention as
 * template-parts/homepage/shops.php (see CLAUDE.md "Polylang CPT
 * translation"): untranslated shops/cafes must still show up here. Returns
 * post_type => array of WP_Post.
 */
function bishkek_park_get_map_assignable_posts( $floor ) {
	$posts = array();

	foreach ( bishkek_park_get_map_floor_post_types( $floor ) as $post_type ) {
		$posts[ $post_type ] = get_posts(
			array(
				'post_type'      => $post_type,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'lang'           => function_exists( 'pll_default_language' ) ? pll_default_language() : '',
			)
		);
	}

	return $posts;
}

/**
 * Resolves a floor+unit to its assigned post, or null if the unit is free
 * or was assigned a post that's since been unpublished/deleted. Assignments
 * are stored as "post_type:ID" (e.g. "bp_cafe:42") so a floor that allows
 * both shops and cafes can tell them apart; a bare numeric value (how floor
 * 0's assignments were stored before cafes existed) is read as a bp_shop
 * for backwards compatibility.
 */
function bishkek_park_get_map_assignment( $floor, $unit_id ) {
	$assignments = get_option( 'bishkek_park_map_assignments', array() );
	$raw         = isset( $assignments[ $floor ][ $unit_id ] ) ? $assignments[ $floor ][ $unit_id ] : '';

	if ( ! $raw ) {
		return null;
	}

	if ( is_numeric( $raw ) ) {
		$post_type = 'bp_shop';
		$post_id   = (int) $raw;
	} else {
		list( $post_type, $post_id ) = array_pad( explode( ':', $raw, 2 ), 2, '' );
		$post_id                     = (int) $post_id;
	}

	if ( ! $post_id || get_post_type( $post_id ) !== $post_type || 'publish' !== get_post_status( $post_id ) ) {
		return null;
	}

	return array(
		'post_type' => $post_type,
		'post_id'   => $post_id,
	);
}

/**
 * Hardcoded fallback label+URL for the floor-3 units that already have an
 * obvious destination elsewhere in the theme (Синематика/Funcity both
 * already have their own "where does this link to" getter — see
 * inc/cinematica-settings.php and functions.php). Used only when the admin
 * hasn't overridden that unit's label/URL — see bishkek_park_get_map_link().
 * Any other floor-3 unit (and any future one) just has no default, i.e. it
 * stays blank until an editor fills it in.
 */
function bishkek_park_get_map_link_defaults( $floor, $unit_id ) {
	if ( '3' === $floor && 'unit-2' === $unit_id ) {
		return array(
			'label' => __( 'Синематика', 'bishkek-park' ),
			'url'   => bishkek_park_get_cinematica_link_url(),
		);
	}

	if ( '3' === $floor && 'unit-3' === $unit_id ) {
		return array(
			'label' => __( 'Funcity', 'bishkek-park' ),
			'url'   => bishkek_park_get_funcity_page_url(),
		);
	}

	return array(
		'label' => '',
		'url'   => '',
	);
}

/**
 * Resolves a floor+unit (on a link-based floor) to its label+URL: whatever
 * was saved in wp-admin, falling back to bishkek_park_get_map_link_defaults()
 * for the two units that already have one. Null if there's nothing to show
 * either way.
 */
function bishkek_park_get_map_link( $floor, $unit_id ) {
	$links    = get_option( 'bishkek_park_map_links', array() );
	$saved    = isset( $links[ $floor ][ $unit_id ] ) ? $links[ $floor ][ $unit_id ] : array();
	$defaults = bishkek_park_get_map_link_defaults( $floor, $unit_id );

	$label = ! empty( $saved['label'] ) ? $saved['label'] : $defaults['label'];
	$url   = ! empty( $saved['url'] ) ? $saved['url'] : $defaults['url'];

	if ( ! $label || ! $url ) {
		return null;
	}

	return array(
		'label' => $label,
		'url'   => $url,
	);
}

/**
 * "Карта ТЦ" settings page: its own top-level admin sidebar entry (rather
 * than nested under Магазины) since it also covers bp_cafe assignments and
 * floor-3 links, not just shops.
 */
function bishkek_park_register_map_settings_page() {
	$hook = add_menu_page(
		__( 'Карта ТЦ', 'bishkek-park' ),
		__( 'Карта ТЦ', 'bishkek-park' ),
		'manage_options',
		'bishkek-park-mall-map',
		'bishkek_park_render_map_settings_page',
		'dashicons-location-alt',
		22
	);

	add_action( 'admin_print_styles-' . $hook, 'bishkek_park_print_map_admin_assets' );
}
add_action( 'admin_menu', 'bishkek_park_register_map_settings_page' );

/**
 * The admin map preview (SVG + numbered zones synced with the table rows
 * below it) only needs its CSS/JS on this one settings page.
 */
function bishkek_park_print_map_admin_assets() {
	wp_enqueue_style(
		'bishkek-park-mall-map-admin',
		BISHKEK_PARK_URI . '/assets/css/mall-map-admin.css',
		array(),
		filemtime( BISHKEK_PARK_DIR . '/assets/css/mall-map-admin.css' )
	);
	wp_enqueue_script(
		'bishkek-park-mall-map-admin',
		BISHKEK_PARK_URI . '/assets/js/mall-map-admin.js',
		array(),
		filemtime( BISHKEK_PARK_DIR . '/assets/js/mall-map-admin.js' ),
		true
	);
}

/**
 * Every floor that has a map, bottom to top — same order as
 * template-parts/map/page-map.php's floor switcher.
 */
function bishkek_park_get_map_floors() {
	return array( '-1', '0', '1', '2', '3' );
}

/**
 * Keeps only entries that point at a currently-published, allowed-for-that-
 * floor post (or empty) — so a stray/tampered post value can't attach a
 * unit to something other than a real shop/cafe, and a shop can't sneak
 * into a cafe-only slot or vice versa.
 */
function bishkek_park_sanitize_map_assignments( $value ) {
	$clean = array();

	if ( ! is_array( $value ) ) {
		return $clean;
	}

	foreach ( $value as $floor => $unit_map ) {
		$floor = sanitize_key( $floor );
		if ( ! is_array( $unit_map ) ) {
			continue;
		}

		$post_types = bishkek_park_get_map_floor_post_types( $floor );
		if ( ! $post_types ) {
			continue;
		}

		$valid_ids = array();
		foreach ( $post_types as $post_type ) {
			$valid_ids[ $post_type ] = get_posts(
				array(
					'post_type'      => $post_type,
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'fields'         => 'ids',
				)
			);
		}

		foreach ( bishkek_park_get_map_units( $floor ) as $unit_id => $unit ) {
			$raw = isset( $unit_map[ $unit_id ] ) ? sanitize_text_field( $unit_map[ $unit_id ] ) : '';
			if ( ! $raw ) {
				continue;
			}

			if ( is_numeric( $raw ) ) {
				$post_type = 'bp_shop';
				$post_id   = (int) $raw;
			} else {
				list( $post_type, $post_id ) = array_pad( explode( ':', $raw, 2 ), 2, '' );
				$post_id                     = (int) $post_id;
			}

			if ( isset( $valid_ids[ $post_type ] ) && in_array( $post_id, $valid_ids[ $post_type ], true ) ) {
				$clean[ $floor ][ $unit_id ] = $post_type . ':' . $post_id;
			}
		}
	}

	return $clean;
}

/**
 * Sanitizes floor 3's free-text label/URL pairs.
 */
function bishkek_park_sanitize_map_links( $value ) {
	$clean = array();

	if ( ! is_array( $value ) ) {
		return $clean;
	}

	foreach ( $value as $floor => $unit_map ) {
		$floor = sanitize_key( $floor );
		if ( ! is_array( $unit_map ) || ! bishkek_park_map_floor_uses_links( $floor ) ) {
			continue;
		}

		foreach ( bishkek_park_get_map_units( $floor ) as $unit_id => $unit ) {
			if ( ! isset( $unit_map[ $unit_id ] ) || ! is_array( $unit_map[ $unit_id ] ) ) {
				continue;
			}

			$label = isset( $unit_map[ $unit_id ]['label'] ) ? sanitize_text_field( $unit_map[ $unit_id ]['label'] ) : '';
			$url   = isset( $unit_map[ $unit_id ]['url'] ) ? esc_url_raw( $unit_map[ $unit_id ]['url'] ) : '';

			if ( $label || $url ) {
				$clean[ $floor ][ $unit_id ] = array(
					'label' => $label,
					'url'   => $url,
				);
			}
		}
	}

	return $clean;
}

function bishkek_park_register_map_settings() {
	register_setting(
		'bishkek_park_map_settings',
		'bishkek_park_map_assignments',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'bishkek_park_sanitize_map_assignments',
			'default'           => array(),
		)
	);
	register_setting(
		'bishkek_park_map_settings',
		'bishkek_park_map_links',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'bishkek_park_sanitize_map_links',
			'default'           => array(),
		)
	);
}
add_action( 'admin_init', 'bishkek_park_register_map_settings' );

/**
 * One <select> (grouped by post type when a floor allows more than one) or,
 * on a link floor, one pair of text inputs — whichever a unit's row needs.
 */
function bishkek_park_render_map_admin_row_field( $floor, $unit_id, $unit ) {
	if ( bishkek_park_map_floor_uses_links( $floor ) ) {
		$links    = get_option( 'bishkek_park_map_links', array() );
		$saved    = isset( $links[ $floor ][ $unit_id ] ) ? $links[ $floor ][ $unit_id ] : array();
		$defaults = bishkek_park_get_map_link_defaults( $floor, $unit_id );
		?>
		<input
			type="text"
			class="regular-text"
			placeholder="<?php echo esc_attr( $defaults['label'] ? $defaults['label'] : __( 'Название', 'bishkek-park' ) ); ?>"
			name="bishkek_park_map_links[<?php echo esc_attr( $floor ); ?>][<?php echo esc_attr( $unit_id ); ?>][label]"
			value="<?php echo esc_attr( isset( $saved['label'] ) ? $saved['label'] : '' ); ?>"
		>
		<input
			type="url"
			class="regular-text code"
			placeholder="<?php echo esc_attr( $defaults['url'] ? $defaults['url'] : 'https://' ); ?>"
			name="bishkek_park_map_links[<?php echo esc_attr( $floor ); ?>][<?php echo esc_attr( $unit_id ); ?>][url]"
			value="<?php echo esc_attr( isset( $saved['url'] ) ? $saved['url'] : '' ); ?>"
		>
		<?php
		return;
	}

	$post_types = bishkek_park_get_map_floor_post_types( $floor );
	$posts      = bishkek_park_get_map_assignable_posts( $floor );
	$saved      = get_option( 'bishkek_park_map_assignments', array() );
	$current    = isset( $saved[ $floor ][ $unit_id ] ) ? $saved[ $floor ][ $unit_id ] : '';
	if ( is_numeric( $current ) ) {
		$current = 'bp_shop:' . $current;
	}

	$post_type_labels = array(
		'bp_shop' => __( 'Магазины', 'bishkek-park' ),
		'bp_cafe' => __( 'Кафе', 'bishkek-park' ),
	);
	?>
	<select name="bishkek_park_map_assignments[<?php echo esc_attr( $floor ); ?>][<?php echo esc_attr( $unit_id ); ?>]">
		<option value=""><?php esc_html_e( '— Свободно —', 'bishkek-park' ); ?></option>
		<?php foreach ( $post_types as $post_type ) : ?>
			<?php if ( empty( $posts[ $post_type ] ) ) continue; ?>
			<?php if ( count( $post_types ) > 1 ) : ?>
				<optgroup label="<?php echo esc_attr( isset( $post_type_labels[ $post_type ] ) ? $post_type_labels[ $post_type ] : $post_type ); ?>">
			<?php endif; ?>
				<?php foreach ( $posts[ $post_type ] as $post ) : ?>
					<?php $value = $post_type . ':' . $post->ID; ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current, $value ); ?>>
						<?php echo esc_html( $post->post_title ); ?>
					</option>
				<?php endforeach; ?>
			<?php if ( count( $post_types ) > 1 ) : ?>
				</optgroup>
			<?php endif; ?>
		<?php endforeach; ?>
	</select>
	<?php
}

/**
 * Renders one floor's plan (each unit numbered at its real shape's
 * centroid) plus its assignment table. Hovering a zone on the plan or its
 * table row highlights both (assets/js/mall-map-admin.js) via the same
 * `data-bp-unit` attributes baked into the SVG — so an editor can tell
 * which physical unit they're assigning without relying on the (possibly
 * stale, or just positional) hint alone.
 */
function bishkek_park_render_map_admin_floor( $floor ) {
	$units = bishkek_park_get_map_units( $floor );
	if ( ! $units ) {
		return;
	}

	$numbers     = bishkek_park_get_map_unit_numbers( $floor );
	$unit_markup = array();
	foreach ( $units as $unit_id => $unit ) {
		$unit_markup[ $unit_id ] = array(
			'label'       => (string) $numbers[ $unit_id ],
			'label_class' => 'bp-map-admin-unit-number',
		);
	}
	$svg = bishkek_park_render_map_svg( $floor, $unit_markup );
	?>
	<div class="bp-map-admin__plan">
		<?php echo $svg; // phpcs:ignore -- our own asset, built by bishkek_park_render_map_svg(). ?>
	</div>
	<table class="widefat bp-map-admin__table">
		<thead>
			<tr>
				<th class="bp-map-admin__table-num"></th>
				<th><?php esc_html_e( 'Место', 'bishkek-park' ); ?></th>
				<th><?php echo bishkek_park_map_floor_uses_links( $floor ) ? esc_html__( 'Название и ссылка', 'bishkek-park' ) : esc_html__( 'Магазин / кафе', 'bishkek-park' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $units as $unit_id => $unit ) : ?>
				<tr data-bp-map-admin-row="<?php echo esc_attr( $unit_id ); ?>">
					<td class="bp-map-admin__table-num"><?php echo esc_html( $numbers[ $unit_id ] ); ?></td>
					<td><?php echo esc_html( $unit['hint'] ); ?></td>
					<td><?php bishkek_park_render_map_admin_row_field( $floor, $unit_id, $unit ); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php
}

function bishkek_park_render_map_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$floors = bishkek_park_get_map_floors();
	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'Карта ТЦ', 'bishkek-park' ); ?></h1>
		<p>
			<?php esc_html_e( 'Привязка магазинов/кафе (или, для этажа 3, названия и ссылки) к местам на плане каждого этажа. Можно выбрать только уже опубликованные магазины/кафе. Если место свободно — оставьте "— Свободно —". Наведите курсор на зону плана или на строку таблицы — они подсвечивают друг друга, так проще понять, где какое место.', 'bishkek-park' ); ?>
		</p>
		<form method="post" action="options.php">
			<?php settings_fields( 'bishkek_park_map_settings' ); ?>
			<div class="bp-map-admin-tabs">
				<?php foreach ( $floors as $i => $floor ) : ?>
					<button
						type="button"
						class="bp-map-admin-tabs__btn<?php echo 0 === $i ? ' is-active' : ''; ?>"
						data-bp-map-admin-tab="<?php echo esc_attr( $floor ); ?>"
					>
						<?php
						// translators: %s is the floor number, e.g. "2" (or "М1" for the basement level stored as "-1").
						printf( esc_html__( 'Этаж %s', 'bishkek-park' ), esc_html( bishkek_park_get_floor_number_label( $floor ) ) );
						?>
					</button>
				<?php endforeach; ?>
			</div>
			<?php foreach ( $floors as $i => $floor ) : ?>
				<div class="bp-map-admin<?php echo 0 === $i ? ' is-active' : ''; ?>" data-bp-map-admin-floor="<?php echo esc_attr( $floor ); ?>">
					<?php bishkek_park_render_map_admin_floor( $floor ); ?>
				</div>
			<?php endforeach; ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}