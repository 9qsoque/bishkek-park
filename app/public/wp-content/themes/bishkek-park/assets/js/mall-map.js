( function () {
	'use strict';

	var stage = document.querySelector( '[data-bp-mall-map]' );
	var title = document.querySelector( '[data-bp-mall-map-title]' );
	var prevBtns = Array.prototype.slice.call( document.querySelectorAll( '[data-bp-mall-map-prev]' ) );
	var nextBtns = Array.prototype.slice.call( document.querySelectorAll( '[data-bp-mall-map-next]' ) );
	var shops = document.querySelector( '[data-bp-mall-map-shops]' );
	var shopsLink = document.querySelector( '[data-bp-mall-map-shops-link]' );
	var shopsCount = document.querySelector( '[data-bp-mall-map-shops-count]' );

	if ( ! stage || ! title || ! prevBtns.length || ! nextBtns.length ) {
		return;
	}

	var floors = Array.prototype.slice.call( stage.querySelectorAll( '[data-bp-mall-map-floor]' ) );

	if ( floors.length < 2 ) {
		prevBtns.concat( nextBtns ).forEach( function ( btn ) {
			btn.classList.add( 'is-disabled' );
		} );
	}

	var currentIndex = Math.max(
		floors.findIndex( function ( floor ) {
			return floor.classList.contains( 'is-active' );
		} ),
		0
	);

	// Shrinks each unit label on a floor's plan until it fits inside its
	// unit's actual drawn shape (measured via getBBox, which only works once
	// the floor is actually rendered/visible), falling back to a boxed
	// number label — and a legend entry below the plan — for units too small
	// to fit their name at any readable size. cx/cy live in the SVG's own
	// viewBox units, same as the fixed font-size in mall-map.css, so a
	// floor's fit only needs computing once ever, not on every resize —
	// hence the data-bp-mall-map-fitted guard.
	var LABEL_FONT_STEP = 2;
	var LABEL_FIT_RATIO = 0.82;
	var MIN_LABEL_FONT_SIZE = 14;
	var MIN_NUMBER_FONT_SIZE = 10;

	// The cx/cy every label starts at server-side (bishkek_park_get_map_units()
	// in inc/mall-map.php) is a hand-authored guess at each unit's centroid —
	// close enough for most units, but visibly off-center for a few, so once
	// the unit's real drawn shape is measurable this recenters the label onto
	// its actual geometric center instead of trusting that guess.
	function centerTextOnBox( text, unitBox ) {
		var cx = unitBox.x + unitBox.width / 2;
		var cy = unitBox.y + unitBox.height / 2;

		text.setAttribute( 'x', cx );
		text.setAttribute( 'y', cy );
		Array.prototype.slice.call( text.querySelectorAll( 'tspan' ) ).forEach( function ( tspan ) {
			tspan.setAttribute( 'x', cx );
		} );
	}

	// Shrinks `text`'s font-size (in the SVG's own viewBox units, same space
	// as unitBox) until it fits within unitBox, or gives up at minFontSize.
	// Returns whether it ended up fitting.
	function fitTextToBox( text, unitBox, minFontSize ) {
		var maxWidth = unitBox.width * LABEL_FIT_RATIO;
		var maxHeight = unitBox.height * LABEL_FIT_RATIO;

		text.style.fontSize = '';
		var fontSize = parseFloat( window.getComputedStyle( text ).fontSize ) || 28;
		var box = text.getBBox();

		while ( ( box.width > maxWidth || box.height > maxHeight ) && fontSize > minFontSize ) {
			fontSize -= LABEL_FONT_STEP;
			text.style.fontSize = fontSize + 'px';
			box = text.getBBox();
		}

		return box.width <= maxWidth && box.height <= maxHeight;
	}

	function fitFloorLabels( floor ) {
		if ( floor.hasAttribute( 'data-bp-mall-map-fitted' ) ) {
			return;
		}
		floor.setAttribute( 'data-bp-mall-map-fitted', '1' );

		// Units too small for their name (below) go in here, in plan order —
		// NOT the order every labeled unit on the floor was assigned. The
		// number a unit ends up showing, and the number it gets in the
		// legend, is this array's index — a sequence of only the units that
		// actually need a number, starting at 1, kept separate from
		// bishkek_park_get_map_unit_numbers()'s per-floor position numbers
		// (which the admin plan preview uses and count every unit, small or
		// not).
		var compacted = [];

		Array.prototype.slice.call( floor.querySelectorAll( '.bp-mall-map-unit-label-group' ) ).forEach( function ( group ) {
			var text = group.querySelector( '.bp-mall-map-unit-label' );
			var number = group.querySelector( '.bp-mall-map-unit-fallback__num' );
			var link = group.closest( '.bp-mall-map-unit-link' );
			var paths = link ? Array.prototype.slice.call( link.querySelectorAll( 'path' ) ) : [];

			if ( ! text || ! paths.length ) {
				return;
			}

			var unitBox = paths.reduce( function ( box, path ) {
				var b = path.getBBox();
				if ( ! box ) {
					return b;
				}
				var x2 = Math.max( box.x + box.width, b.x + b.width );
				var y2 = Math.max( box.y + box.height, b.y + b.height );
				box.x = Math.min( box.x, b.x );
				box.y = Math.min( box.y, b.y );
				box.width = x2 - box.x;
				box.height = y2 - box.y;
				return box;
			}, null );

			centerTextOnBox( text, unitBox );
			if ( number ) {
				centerTextOnBox( number, unitBox );
			}

			if ( fitTextToBox( text, unitBox, MIN_LABEL_FONT_SIZE ) ) {
				return;
			}

			group.classList.add( 'is-compact' );
			compacted.push( {
				number: number,
				unitBox: unitBox,
				name: group.getAttribute( 'data-bp-map-name' ),
			} );
		} );

		var legend = floor.querySelector( '[data-bp-mall-map-legend]' );

		compacted.forEach( function ( entry, index ) {
			var displayNumber = index + 1;

			if ( entry.number ) {
				entry.number.textContent = String( displayNumber );
				fitTextToBox( entry.number, entry.unitBox, MIN_NUMBER_FONT_SIZE );
			}

			if ( ! legend ) {
				return;
			}

			var item = document.createElement( 'li' );
			item.className = 'bp-mall-map-legend__item';

			var num = document.createElement( 'span' );
			num.className = 'bp-mall-map-legend__num';
			num.textContent = displayNumber + '.';

			item.appendChild( num );
			item.appendChild( document.createTextNode( ' ' + entry.name ) );
			legend.appendChild( item );
		} );

		if ( ! legend || ! compacted.length ) {
			return;
		}

		legend.hidden = false;
	}

	fitFloorLabels( floors[ currentIndex ] );

	function showFloor( index ) {
		currentIndex = ( index + floors.length ) % floors.length;

		floors.forEach( function ( floor, floorIndex ) {
			floor.classList.toggle( 'is-active', floorIndex === currentIndex );
		} );

		var activeFloor = floors[ currentIndex ];
		title.textContent = activeFloor.getAttribute( 'data-bp-mall-map-title' );
		fitFloorLabels( activeFloor );

		if ( shops && shopsLink && shopsCount ) {
			var count = parseInt( activeFloor.getAttribute( 'data-bp-mall-map-shop-count' ), 10 ) || 0;
			shops.classList.toggle( 'is-hidden', count <= 0 );
			shopsCount.textContent = count;
			shopsLink.setAttribute( 'href', activeFloor.getAttribute( 'data-bp-mall-map-shop-url' ) );
		}
	}

	prevBtns.forEach( function ( btn ) {
		btn.addEventListener( 'click', function () {
			showFloor( currentIndex - 1 );
		} );
	} );

	nextBtns.forEach( function ( btn ) {
		btn.addEventListener( 'click', function () {
			showFloor( currentIndex + 1 );
		} );
	} );

	// Swipe support: a horizontal drag across the map switches floors;
	// mostly-vertical drags are left alone so page scrolling still works.
	var SWIPE_THRESHOLD = 40;
	var touchStartX = null;
	var touchStartY = null;

	stage.addEventListener(
		'touchstart',
		function ( event ) {
			touchStartX = event.touches[ 0 ].clientX;
			touchStartY = event.touches[ 0 ].clientY;
		},
		{ passive: true }
	);

	stage.addEventListener(
		'touchend',
		function ( event ) {
			if ( null === touchStartX || null === touchStartY ) {
				return;
			}

			var touch = event.changedTouches[ 0 ];
			var deltaX = touch.clientX - touchStartX;
			var deltaY = touch.clientY - touchStartY;

			touchStartX = null;
			touchStartY = null;

			if ( Math.abs( deltaX ) < SWIPE_THRESHOLD || Math.abs( deltaX ) < Math.abs( deltaY ) ) {
				return;
			}

			showFloor( deltaX < 0 ? currentIndex + 1 : currentIndex - 1 );
		},
		{ passive: true }
	);
}() );
