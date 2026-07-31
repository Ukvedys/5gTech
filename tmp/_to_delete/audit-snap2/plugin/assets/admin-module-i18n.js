( function () {
	'use strict';

	document.querySelectorAll( '[data-g5-i18n-editor]' ).forEach( function ( editor ) {
		const tabs = Array.from( editor.querySelectorAll( '[data-g5-i18n-tab]' ) );
		const panels = Array.from( editor.querySelectorAll( '[data-g5-i18n-panel]' ) );

		function selectLanguage( language, focus ) {
			tabs.forEach( function ( tab ) {
				const active = tab.dataset.g5I18nTab === language;
				tab.classList.toggle( 'is-active', active );
				tab.setAttribute( 'aria-selected', active ? 'true' : 'false' );
				tab.tabIndex = active ? 0 : -1;

				if ( active && focus ) {
					tab.focus();
				}
			} );

			panels.forEach( function ( panel ) {
				const active = panel.dataset.g5I18nPanel === language;
				panel.classList.toggle( 'is-active', active );
				panel.hidden = ! active;
			} );

			if ( window.history && window.history.replaceState ) {
				const url = new URL( window.location.href );
				url.searchParams.set( 'content_lang', language );
				window.history.replaceState( {}, '', url );
			}
		}

		tabs.forEach( function ( tab, index ) {
			tab.addEventListener( 'click', function () {
				selectLanguage( tab.dataset.g5I18nTab, false );
			} );

			tab.addEventListener( 'keydown', function ( event ) {
				if ( event.key !== 'ArrowLeft' && event.key !== 'ArrowRight' ) {
					return;
				}

				event.preventDefault();
				const direction = event.key === 'ArrowRight' ? 1 : -1;
				const next = ( index + direction + tabs.length ) % tabs.length;
				selectLanguage( tabs[ next ].dataset.g5I18nTab, true );
			} );
		} );

		const requested = new URL( window.location.href ).searchParams.get( 'content_lang' );
		if ( requested && tabs.some( function ( tab ) { return tab.dataset.g5I18nTab === requested; } ) ) {
			selectLanguage( requested, false );
		}
	} );
}() );

