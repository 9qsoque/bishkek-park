( function () {
	'use strict';

	var toggle = document.querySelector( '.bp-menu-toggle' );
	var menu = document.getElementById( 'bp-mobile-menu' );

	if ( ! toggle || ! menu ) {
		return;
	}

	var setOpen = function ( isOpen ) {
		menu.classList.toggle( 'is-open', isOpen );
		toggle.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
		menu.setAttribute( 'aria-hidden', isOpen ? 'false' : 'true' );
		document.body.classList.toggle( 'bp-menu-open', isOpen );
	};

	toggle.addEventListener( 'click', function () {
		setOpen( ! menu.classList.contains( 'is-open' ) );
	} );

	document.addEventListener( 'click', function ( event ) {
		if ( ! menu.classList.contains( 'is-open' ) ) {
			return;
		}

		if ( menu.contains( event.target ) || toggle.contains( event.target ) ) {
			return;
		}

		setOpen( false );
	} );
}() );

( function () {
	'use strict';

	var langSwitch = document.querySelector( '[data-bp-lang-switch]' );
	var langToggle = langSwitch ? langSwitch.querySelector( '.bp-lang-switch__toggle' ) : null;
	var langMenu = langSwitch ? langSwitch.querySelector( '.bp-lang-switch__menu' ) : null;

	if ( ! langSwitch || ! langToggle || ! langMenu ) {
		return;
	}

	var setLangOpen = function ( isOpen ) {
		langMenu.classList.toggle( 'is-open', isOpen );
		langToggle.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
	};

	langToggle.addEventListener( 'click', function ( event ) {
		event.stopPropagation();
		setLangOpen( ! langMenu.classList.contains( 'is-open' ) );
	} );

	document.addEventListener( 'click', function ( event ) {
		if ( ! langMenu.classList.contains( 'is-open' ) || langSwitch.contains( event.target ) ) {
			return;
		}

		setLangOpen( false );
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( 'Escape' === event.key && langMenu.classList.contains( 'is-open' ) ) {
			setLangOpen( false );
			langToggle.focus();
		}
	} );
}() );
