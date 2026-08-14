( function () {
	'use strict';

	// Tabs: one floor visible at a time. Unit ids repeat across floors
	// ("unit-1" exists on every floor), so every lookup below is scoped to
	// a single .bp-map-admin container rather than the whole document.
	var tabBtns = Array.prototype.slice.call( document.querySelectorAll( '[data-bp-map-admin-tab]' ) );
	var floorPanels = Array.prototype.slice.call( document.querySelectorAll( '[data-bp-map-admin-floor]' ) );
	var form = document.querySelector( '.bp-map-admin-tabs' ) ? document.querySelector( '.bp-map-admin-tabs' ).closest( 'form' ) : null;

	function activateTab( floor ) {
		var matched = false;

		tabBtns.forEach( function ( b ) {
			var isMatch = b.getAttribute( 'data-bp-map-admin-tab' ) === floor;
			b.classList.toggle( 'is-active', isMatch );
			matched = matched || isMatch;
		} );
		floorPanels.forEach( function ( panel ) {
			panel.classList.toggle( 'is-active', panel.getAttribute( 'data-bp-map-admin-floor' ) === floor );
		} );

		return matched;
	}

	// Saving this form is a full page reload to options.php and back (not an
	// ajax submit), which would otherwise always land back on the first tab
	// PHP marks active — bishkek_park_render_map_settings_page() has no way
	// to know which one was open. Stash it in the URL hash instead: on
	// submit, the handler below writes the current tab into the
	// `_wp_http_referer` field's value (which is what options.php's redirect
	// actually navigates back to, see wp_get_referer()), and on load here we
	// read it back and restore that tab.
	var hashFloor = window.location.hash.replace( /^#floor-/, '' );
	if ( hashFloor ) {
		activateTab( hashFloor );
	}

	tabBtns.forEach( function ( btn ) {
		btn.addEventListener( 'click', function () {
			var floor = btn.getAttribute( 'data-bp-map-admin-tab' );
			activateTab( floor );
			history.replaceState( null, '', '#floor-' + floor );
		} );
	} );

	if ( form ) {
		form.addEventListener( 'submit', function () {
			var referer = form.querySelector( 'input[name="_wp_http_referer"]' );
			var activeTab = tabBtns.filter( function ( b ) {
				return b.classList.contains( 'is-active' );
			} )[ 0 ];

			if ( referer && activeTab ) {
				referer.value = referer.value.replace( /#.*$/, '' ) + '#floor-' + activeTab.getAttribute( 'data-bp-map-admin-tab' );
			}
		} );
	}

	floorPanels.forEach( function ( panel ) {
		var units = Array.prototype.slice.call( panel.querySelectorAll( '.bp-map-admin__plan [data-bp-unit]' ) );
		var rows = Array.prototype.slice.call( panel.querySelectorAll( '[data-bp-map-admin-row]' ) );

		function unitsFor( unitId ) {
			return units.filter( function ( unit ) {
				return unit.getAttribute( 'data-bp-unit' ) === unitId;
			} );
		}

		function rowFor( unitId ) {
			return rows.filter( function ( row ) {
				return row.getAttribute( 'data-bp-map-admin-row' ) === unitId;
			} )[ 0 ];
		}

		function highlight( unitId, on ) {
			unitsFor( unitId ).forEach( function ( unit ) {
				unit.classList.toggle( 'is-highlighted', on );
			} );
			var row = rowFor( unitId );
			if ( row ) {
				row.classList.toggle( 'is-highlighted', on );
			}
		}

		units.forEach( function ( unit ) {
			var id = unit.getAttribute( 'data-bp-unit' );
			unit.addEventListener( 'mouseenter', function () {
				highlight( id, true );
			} );
			unit.addEventListener( 'mouseleave', function () {
				highlight( id, false );
			} );
			unit.addEventListener( 'click', function () {
				var row = rowFor( id );
				var field = row ? row.querySelector( 'select, input' ) : null;
				if ( field ) {
					field.focus();
					field.scrollIntoView( { block: 'center', behavior: 'smooth' } );
				}
			} );
		} );

		rows.forEach( function ( row ) {
			var id = row.getAttribute( 'data-bp-map-admin-row' );
			row.addEventListener( 'mouseenter', function () {
				highlight( id, true );
			} );
			row.addEventListener( 'mouseleave', function () {
				highlight( id, false );
			} );
		} );
	} );
}() );
