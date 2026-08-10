/**
 * Video paralaksas „Nuotrauka rėmelyje" blokui.
 *
 * Fiksuoto fono (background-attachment) video neturi, todėl vaizdas
 * stumdomas transformacija pagal sekcijos padėtį lange. Gerbiamas
 * prefers-reduced-motion — tada video rodomas statiškai.
 */
( function () {
	'use strict';

	if ( window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
		return;
	}

	var videos = Array.prototype.slice.call(
		document.querySelectorAll( '.media-frame--parallax .media-frame__video' )
	);

	if ( ! videos.length ) {
		return;
	}

	var ticking = false;

	function update() {
		ticking = false;

		var viewport = window.innerHeight;

		videos.forEach( function ( video ) {
			var frame = video.parentElement.getBoundingClientRect();

			if ( frame.bottom < 0 || frame.top > viewport ) {
				return;
			}

			// -1 (sekcija apačioje) .. 1 (sekcija viršuje)
			var progress = ( frame.top + frame.height / 2 - viewport / 2 ) / ( ( viewport + frame.height ) / 2 );
			var range = Math.max( 0, ( video.offsetHeight - frame.height ) / 2 );

			video.style.transform = 'translateY(' + ( progress * range ).toFixed( 1 ) + 'px)';
		} );
	}

	function request() {
		if ( ! ticking ) {
			ticking = true;
			window.requestAnimationFrame( update );
		}
	}

	window.addEventListener( 'scroll', request, { passive: true } );
	window.addEventListener( 'resize', request );
	update();
} )();
