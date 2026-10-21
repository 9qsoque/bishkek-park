/**
 * Scroll-reveal animations, driven entirely by data attributes:
 *
 * - [data-bp-reveal]          the element itself fades/slides in once it
 *                             enters the viewport.
 * - [data-bp-reveal-stagger]  each direct child fades/slides in as it enters
 *                             the viewport; children that enter together
 *                             (e.g. one row of cards) cascade one after another.
 *
 * Children are observed individually (not the container) so long catalog
 * grids animate row by row while scrolling, and cards hidden by a catalog
 * filter animate in once they're shown and scrolled into view.
 *
 * The hidden starting state only applies under html.bp-reveal (added by an
 * inline <head> script, see bishkek_park_reveal_head_script()), so content
 * stays visible if JS is off or the visitor prefers reduced motion.
 */
( function () {
	'use strict';

	var root = document.documentElement;
	var staggerStep = 90;
	var staggerMaxSteps = 6;

	if ( ! root.classList.contains( 'bp-reveal' ) ) {
		return;
	}

	root.classList.add( 'bp-reveal-ready' );

	var reveal = function ( el, delay ) {
		el.style.setProperty( '--bp-reveal-delay', delay + 'ms' );
		el.classList.add( 'is-revealed' );

		// Drop the animation once it's finished so its transform doesn't
		// override the cards' own hover transforms.
		el.addEventListener( 'animationend', function onEnd( e ) {
			if ( e.target !== el ) {
				return;
			}
			el.removeEventListener( 'animationend', onEnd );
			el.classList.add( 'is-reveal-done' );
		} );
	};

	var targets = [];

	document.querySelectorAll( '[data-bp-reveal]' ).forEach( function ( el ) {
		targets.push( el );
	} );

	document.querySelectorAll( '[data-bp-reveal-stagger]' ).forEach( function ( container ) {
		Array.prototype.forEach.call( container.children, function ( child ) {
			child.setAttribute( 'data-bp-reveal-item', '' );
			targets.push( child );
		} );
	} );

	if ( ! ( 'IntersectionObserver' in window ) ) {
		targets.forEach( function ( el ) {
			reveal( el, 0 );
		} );
		return;
	}

	var observer = new IntersectionObserver(
		function ( entries ) {
			var staggerIndex = 0;

			entries.forEach( function ( entry ) {
				if ( ! entry.isIntersecting ) {
					return;
				}
				observer.unobserve( entry.target );

				if ( entry.target.hasAttribute( 'data-bp-reveal-item' ) ) {
					reveal( entry.target, Math.min( staggerIndex, staggerMaxSteps ) * staggerStep );
					staggerIndex++;
				} else {
					reveal( entry.target, 0 );
				}
			} );
		},
		{ rootMargin: '0px 0px -8% 0px', threshold: 0.1 }
	);

	var start = function () {
		targets.forEach( function ( el ) {
			observer.observe( el );
		} );
	};

	// Hold off while the loading screen is up (see
	// bishkek_park_preloader_head_script()), otherwise above-the-fold blocks
	// would finish animating hidden behind it.
	if ( root.classList.contains( 'bp-preloading' ) ) {
		document.addEventListener( 'bp:preloaded', start, { once: true } );
	} else {
		start();
	}
}() );
