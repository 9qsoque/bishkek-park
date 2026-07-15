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

	function showFloor( index ) {
		currentIndex = ( index + floors.length ) % floors.length;

		floors.forEach( function ( floor, floorIndex ) {
			floor.classList.toggle( 'is-active', floorIndex === currentIndex );
		} );

		var activeFloor = floors[ currentIndex ];
		title.textContent = activeFloor.getAttribute( 'data-bp-mall-map-title' );

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
