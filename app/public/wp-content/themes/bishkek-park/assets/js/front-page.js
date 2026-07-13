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

	var heroSliders = document.querySelectorAll( '[data-bp-hero-slider]' );
	var heroSwipeThreshold = 40;
	var heroAutoplayDelay = 10000;

	heroSliders.forEach( function ( slider ) {
		var slides = slider.querySelectorAll( '.bp-hero__slide' );
		var dots = slider.parentElement.querySelectorAll( '.bp-hero__dot' );

		if ( slides.length < 2 || slides.length !== dots.length ) {
			return;
		}

		var currentIndex = 0;
		var autoplayTimer = null;

		var goToSlide = function ( index ) {
			currentIndex = index;
			slides.forEach( function ( slide, slideIndex ) {
				slide.classList.toggle( 'is-active', slideIndex === index );
			} );
			dots.forEach( function ( dot, dotIndex ) {
				dot.classList.toggle( 'is-active', dotIndex === index );
			} );
		};

		var restartAutoplay = function () {
			if ( autoplayTimer ) {
				window.clearInterval( autoplayTimer );
			}
			autoplayTimer = window.setInterval( function () {
				goToSlide( ( currentIndex + 1 ) % slides.length );
			}, heroAutoplayDelay );
		};

		dots.forEach( function ( dot, index ) {
			dot.addEventListener( 'click', function () {
				goToSlide( index );
				restartAutoplay();
			} );
		} );

		var pointerStartX = null;
		var pointerId = null;

		slider.addEventListener( 'pointerdown', function ( e ) {
			pointerStartX = e.clientX;
			pointerId = e.pointerId;
		} );

		slider.addEventListener( 'pointerup', function ( e ) {
			if ( pointerStartX === null || e.pointerId !== pointerId ) {
				return;
			}

			var deltaX = e.clientX - pointerStartX;
			pointerStartX = null;

			if ( Math.abs( deltaX ) < heroSwipeThreshold ) {
				return;
			}

			if ( deltaX < 0 ) {
				goToSlide( ( currentIndex + 1 ) % slides.length );
			} else {
				goToSlide( ( currentIndex - 1 + slides.length ) % slides.length );
			}

			restartAutoplay();
		} );

		slider.addEventListener( 'pointercancel', function () {
			pointerStartX = null;
		} );

		restartAutoplay();
	} );
}() );
