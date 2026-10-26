/**
 * Language switch without a page reload. Clicking a language in the header
 * switcher fetches that language's version of the current page (the same
 * URL Polylang would navigate to) and patches the already-rendered page in
 * place — text, links, images, a whitelist of attributes — instead of
 * replacing it. Nothing is re-created, so every other script's state
 * survives: slider positions, the active hero slide, catalog filters, the
 * mall map's floor, scroll position, revealed blocks.
 *
 * This works because both language versions of a page come out of the same
 * template with the same posts (see the default-language-then-translate
 * query pattern in CLAUDE.md), so their DOMs line up node for node. Before
 * touching anything, the two documents are compared; if they don't line up
 * (e.g. a section exists in only one language) the switch falls back to a
 * normal page load rather than patching half a page.
 *
 * Classes and inline styles are never copied — those belong to the scripts
 * running on the page (is-active, is-revealed, fitted label sizes). Scripts
 * whose output is derived from text they read once at init listen for the
 * `bp:languagechange` event fired after a switch and re-render themselves
 * (see mall-map.js, contacts-map.js).
 */
( function () {
	'use strict';

	var langSwitch = document.querySelector( '[data-bp-lang-switch]' );

	if ( ! langSwitch || ! window.fetch || ! window.DOMParser || ! window.history.pushState ) {
		return;
	}

	var root = document.documentElement;

	// Attributes that carry translatable content. data-* attributes are
	// synced too (set, never removed, since scripts add their own).
	var SYNCED_ATTRIBUTES = [ 'href', 'src', 'srcset', 'sizes', 'alt', 'title', 'aria-label', 'placeholder', 'content', 'datetime', 'hreflang', 'lang' ];

	// Elements whose attributes are synced but whose children are left
	// alone: their contents are either not text or built by a script.
	var SKIP_CHILDREN = 'script, style, noscript, template, iframe, [data-bp-map], [data-bp-mall-map-legend]';

	// Regions allowed to differ in structure between languages; replaced
	// wholesale instead of patched when they do.
	var REPLACEABLE = '#wpadminbar';

	var SLOW_SWITCH_MS = 150;

	var cache = {};

	function load( url ) {
		if ( ! cache[ url ] ) {
			cache[ url ] = window.fetch( url, { credentials: 'same-origin' } )
				.then( function ( response ) {
					if ( ! response.ok ) {
						throw new Error( 'HTTP ' + response.status );
					}
					return response.text().then( function ( html ) {
						return {
							url: response.url || url,
							doc: new window.DOMParser().parseFromString( html, 'text/html' ),
						};
					} );
				} )
				.catch( function ( error ) {
					delete cache[ url ];
					throw error;
				} );
		}

		return cache[ url ];
	}

	function textNodes( el ) {
		return Array.prototype.filter.call( el.childNodes, function ( node ) {
			return 3 === node.nodeType && /\S/.test( node.nodeValue );
		} );
	}

	// Walks both trees in parallel. With `apply` false it only checks that
	// they line up; with `apply` true it copies text and attributes across.
	function walk( current, next, apply ) {
		if ( current.tagName !== next.tagName ) {
			return false;
		}

		if ( apply ) {
			syncAttributes( current, next );
		}

		if ( current.matches( SKIP_CHILDREN ) ) {
			return true;
		}

		var currentChildren = current.children;
		var nextChildren = next.children;

		if ( ! currentChildren.length && ! nextChildren.length ) {
			if ( apply && current.textContent !== next.textContent ) {
				current.textContent = next.textContent;
			}
			return true;
		}

		var currentText = textNodes( current );
		var nextText = textNodes( next );

		if ( currentChildren.length !== nextChildren.length || currentText.length !== nextText.length ) {
			if ( current.matches( REPLACEABLE ) ) {
				if ( apply ) {
					current.replaceWith( document.importNode( next, true ) );
				}
				return true;
			}
			return false;
		}

		if ( apply ) {
			currentText.forEach( function ( node, i ) {
				if ( node.nodeValue !== nextText[ i ].nodeValue ) {
					node.nodeValue = nextText[ i ].nodeValue;
				}
			} );
		}

		// Snapshot first: a replaced REPLACEABLE child would shift a live list.
		var pairs = Array.prototype.map.call( currentChildren, function ( child, i ) {
			return [ child, nextChildren[ i ] ];
		} );

		return pairs.every( function ( pair ) {
			return walk( pair[ 0 ], pair[ 1 ], apply );
		} );
	}

	function syncAttributes( current, next ) {
		SYNCED_ATTRIBUTES.forEach( function ( name ) {
			var value = next.getAttribute( name );
			if ( null === value ) {
				if ( current.hasAttribute( name ) ) {
					current.removeAttribute( name );
				}
			} else if ( current.getAttribute( name ) !== value ) {
				current.setAttribute( name, value );
			}
		} );

		Array.prototype.forEach.call( next.attributes, function ( attr ) {
			if ( 0 === attr.name.indexOf( 'data-' ) && current.getAttribute( attr.name ) !== attr.value ) {
				current.setAttribute( attr.name, attr.value );
			}
		} );
	}

	// The <head> isn't walked (its scripts/styles differ between pages for
	// reasons unrelated to language); only the per-language bits are copied.
	function syncHead( doc ) {
		document.title = doc.title;
		root.setAttribute( 'lang', doc.documentElement.getAttribute( 'lang' ) || '' );

		[ 'meta[name="description"]', 'meta[property="og:locale"]', 'link[rel="canonical"]' ].forEach( function ( selector ) {
			var current = document.head.querySelector( selector );
			var next = doc.head.querySelector( selector );
			if ( current && next ) {
				syncAttributes( current, next );
			} else if ( current ) {
				current.remove();
			} else if ( next ) {
				document.head.appendChild( document.importNode( next, true ) );
			}
		} );

		[ 'link[rel="alternate"]', 'link[rel="shortlink"]', 'script[type="application/ld+json"]' ].forEach( function ( selector ) {
			document.head.querySelectorAll( selector ).forEach( function ( el ) {
				el.remove();
			} );
			doc.head.querySelectorAll( selector ).forEach( function ( el ) {
				document.head.appendChild( document.importNode( el, true ) );
			} );
		} );
	}

	// The switcher is the one place where a server-set class (is-active)
	// legitimately differs between languages.
	function syncSwitcher( doc ) {
		var currentOptions = document.querySelectorAll( '.bp-lang-switch__option' );
		var nextOptions = doc.querySelectorAll( '.bp-lang-switch__option' );

		currentOptions.forEach( function ( option, i ) {
			if ( nextOptions[ i ] ) {
				option.className = nextOptions[ i ].className;
			}
		} );

		var menu = langSwitch.querySelector( '.bp-lang-switch__menu' );
		var toggle = langSwitch.querySelector( '.bp-lang-switch__toggle' );
		if ( menu && toggle ) {
			// Keyboard users were focused on an option inside the menu that's
			// about to close; keep them on the switcher.
			if ( menu.contains( document.activeElement ) ) {
				toggle.focus();
			}
			menu.classList.remove( 'is-open' );
			toggle.setAttribute( 'aria-expanded', 'false' );
		}
	}

	function switchTo( url, push ) {
		var slowTimer = window.setTimeout( function () {
			root.classList.add( 'bp-lang-switching' );
		}, SLOW_SWITCH_MS );

		return load( url )
			.then( function ( result ) {
				if ( ! walk( document.body, result.doc.body, false ) ) {
					throw new Error( 'Pages do not line up' );
				}

				walk( document.body, result.doc.body, true );
				syncHead( result.doc );
				syncSwitcher( result.doc );

				// An untranslated page's switcher links back to itself (see
				// site-header.php), which needs no new history entry.
				if ( push && result.url !== window.location.href ) {
					window.history.pushState( { bpLangSwitch: true }, '', result.url );
				}

				// Both versions of this page are now in the DOM's past; the
				// other language's links changed, so start fresh.
				cache = {};

				document.dispatchEvent(
					new CustomEvent( 'bp:languagechange', { detail: { lang: root.getAttribute( 'lang' ) } } )
				);
			} )
			.catch( function () {
				window.location.assign( url );
			} )
			.then( function () {
				window.clearTimeout( slowTimer );
				root.classList.remove( 'bp-lang-switching' );
			} );
	}

	function optionFromEvent( event ) {
		var option = event.target.closest ? event.target.closest( '.bp-lang-switch__option' ) : null;

		if ( ! option || ! langSwitch.contains( option ) || option.classList.contains( 'is-active' ) ) {
			return null;
		}

		var url = new URL( option.href, window.location.href );
		return url.origin === window.location.origin ? option : null;
	}

	langSwitch.addEventListener( 'click', function ( event ) {
		var option = optionFromEvent( event );

		if ( ! option || event.defaultPrevented || event.button || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey ) {
			return;
		}

		event.preventDefault();
		switchTo( option.href, true );
	} );

	// Start fetching the other language as soon as the visitor shows intent,
	// so the click itself usually has nothing left to wait for.
	function prefetch() {
		langSwitch.querySelectorAll( '.bp-lang-switch__option:not(.is-active)' ).forEach( function ( option ) {
			if ( new URL( option.href, window.location.href ).origin === window.location.origin ) {
				load( option.href ).catch( function () {} );
			}
		} );
	}

	[ 'pointerenter', 'focusin', 'touchstart' ].forEach( function ( type ) {
		langSwitch.addEventListener( type, prefetch, { passive: true } );
	} );

	// Back/forward between language versions patches the page the same way.
	window.history.replaceState( { bpLangSwitch: true }, '' );
	window.addEventListener( 'popstate', function ( event ) {
		if ( event.state && event.state.bpLangSwitch ) {
			switchTo( window.location.href, false );
		}
	} );
}() );
