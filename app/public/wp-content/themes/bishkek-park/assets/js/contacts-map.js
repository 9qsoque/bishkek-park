/**
 * Contacts page map: an OpenStreetMap Leaflet map (standard OSM tiles, muted
 * to grey in contacts.css so the pink marker stands out) locked
 * to Bishkek, with a single logo-flower marker on Bishkek Park. Everything
 * map-specific comes from the data-bp-map-* attributes on the container (see
 * template-parts/contacts/page-contacts.php), so this file holds no data.
 */
( function () {
	var el = document.querySelector( '[data-bp-map]' );

	if ( ! el || typeof L === 'undefined' ) {
		return;
	}

	var lat = parseFloat( el.getAttribute( 'data-bp-map-lat' ) );
	var lng = parseFloat( el.getAttribute( 'data-bp-map-lng' ) );
	var bounds = JSON.parse( el.getAttribute( 'data-bp-map-bounds' ) );

	var map = L.map( el, {
		center: [ lat, lng ],
		zoom: 16,
		minZoom: 12,
		maxZoom: 18,
		maxBounds: bounds,
		maxBoundsViscosity: 1,
		scrollWheelZoom: true,
	} );

	// Standard OSM tiles need no API key (CARTO's basemaps now do).
	L.tileLayer( 'https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
		bounds: bounds,
		maxZoom: 18,
		attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
	} ).addTo( map );

	var icon = L.divIcon( {
		className: 'bp-map-marker',
		html: '<span class="bp-map-marker__pin"><img src="' + el.getAttribute( 'data-bp-map-icon' ) + '" alt=""></span>',
		iconSize: [ 52, 52 ],
		iconAnchor: [ 26, 63 ],
		tooltipAnchor: [ 0, -66 ],
	} );

	L.marker( [ lat, lng ], { icon: icon, title: el.getAttribute( 'data-bp-map-label' ) } )
		.addTo( map )
		.bindTooltip( el.getAttribute( 'data-bp-map-label' ), {
			permanent: true,
			direction: 'top',
			className: 'bp-map-tooltip',
		} );

	// The block fades in via reveal.js; recompute size once it is visible so
	// tiles aren't laid out against a stale container size.
	window.addEventListener( 'load', function () {
		map.invalidateSize();
	} );
} )();
