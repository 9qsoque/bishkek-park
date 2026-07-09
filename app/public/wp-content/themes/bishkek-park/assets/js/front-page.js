( function () {
	'use strict';

	var toggle = document.querySelector( '.bp-menu-toggle' );
	var menu = document.getElementById( 'bp-mobile-menu' );

	if ( ! toggle || ! menu ) {
		return;
	}

	toggle.addEventListener( 'click', function () {
		var isOpen = menu.classList.toggle( 'is-open' );
		toggle.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
		menu.setAttribute( 'aria-hidden', isOpen ? 'false' : 'true' );
	} );
}() );

( function () {
	'use strict';

	var navs = document.querySelectorAll( '[data-bp-slider-nav]' );

	navs.forEach( function ( nav ) {
		var track = document.getElementById( nav.getAttribute( 'data-bp-slider-nav' ) );
		var prevBtn = nav.querySelector( '[data-bp-slider-prev]' );
		var nextBtn = nav.querySelector( '[data-bp-slider-next]' );

		if ( ! track || ! prevBtn || ! nextBtn ) {
			return;
		}

		var scrollByAmount = function ( direction ) {
			track.scrollBy( { left: direction * track.clientWidth * 0.9, behavior: 'smooth' } );
		};

		var updateButtons = function () {
			var maxScroll = track.scrollWidth - track.clientWidth - 1;
			prevBtn.classList.toggle( 'is-disabled', track.scrollLeft <= 0 );
			nextBtn.classList.toggle( 'is-disabled', track.scrollLeft >= maxScroll );
		};

		prevBtn.addEventListener( 'click', function () {
			scrollByAmount( -1 );
		} );

		nextBtn.addEventListener( 'click', function () {
			scrollByAmount( 1 );
		} );

		track.addEventListener( 'scroll', updateButtons, { passive: true } );
		window.addEventListener( 'resize', updateButtons );
		updateButtons();
	} );
}() );
