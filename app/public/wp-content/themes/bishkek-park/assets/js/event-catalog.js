( function () {
	'use strict';

	var filterGroup = document.querySelector( '[data-bp-event-filter]' );
	var grid = document.querySelector( '[data-bp-event-grid]' );

	if ( ! filterGroup || ! grid ) {
		return;
	}

	var buttons = filterGroup.querySelectorAll( '[data-bp-event-filter-value]' );
	var cards = grid.querySelectorAll( '[data-bp-event-category]' );

	filterGroup.addEventListener( 'click', function ( event ) {
		var button = event.target.closest( '[data-bp-event-filter-value]' );
		if ( ! button ) {
			return;
		}

		buttons.forEach( function ( btn ) {
			btn.classList.toggle( 'is-active', btn === button );
		} );

		var value = button.getAttribute( 'data-bp-event-filter-value' );

		cards.forEach( function ( card ) {
			var matches = 'all' === value || card.getAttribute( 'data-bp-event-category' ) === value;
			card.classList.toggle( 'is-hidden', ! matches );
		} );
	} );
}() );
