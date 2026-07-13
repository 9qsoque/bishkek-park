( function () {
	'use strict';

	var filterGroup = document.querySelector( '[data-bp-shop-filter]' );
	var grid = document.querySelector( '[data-bp-shop-grid]' );

	if ( ! filterGroup || ! grid ) {
		return;
	}

	var buttons = filterGroup.querySelectorAll( '[data-bp-shop-filter-value]' );
	var cards = grid.querySelectorAll( '[data-bp-shop-floor]' );

	filterGroup.addEventListener( 'click', function ( event ) {
		var button = event.target.closest( '[data-bp-shop-filter-value]' );
		if ( ! button ) {
			return;
		}

		buttons.forEach( function ( btn ) {
			btn.classList.toggle( 'is-active', btn === button );
		} );

		var value = button.getAttribute( 'data-bp-shop-filter-value' );

		cards.forEach( function ( card ) {
			var matches = 'all' === value || card.getAttribute( 'data-bp-shop-floor' ) === value;
			card.classList.toggle( 'is-hidden', ! matches );
		} );
	} );
}() );
