( function () {
	'use strict';

	const languages = [ 'lt', 'en', 'de' ];

	function parseEditorData( config ) {
		const node = config.querySelector( '[data-g5-page-i18n-data]' );

		if ( ! node ) {
			return { en: {}, de: {} };
		}

		try {
			return JSON.parse( node.textContent ) || { en: {}, de: {} };
		} catch ( error ) {
			return { en: {}, de: {} };
		}
	}

	function isTranslatableControl( control ) {
		if ( control.disabled || control.dataset.g5I18nIgnore === 'true' ) {
			return false;
		}

		if ( control.tagName === 'TEXTAREA' ) {
			return control.type !== 'hidden' && ! control.matches( '[data-g5-page-i18n-payload]' );
		}

		if ( control.tagName !== 'INPUT' || control.type !== 'text' ) {
			return false;
		}

		const name = control.name || '';

		return ! (
			/(?:^|[_\[])(?:url|email|phone|code|image_id|_id)(?:\]|$)/i.test( name )
			|| /\[(?:url|email|phone|code|image_id|_id)\]/i.test( name )
		);
	}

	function controlsIn( container, selector, form ) {
		const controls = selector
			? Array.from( form.querySelectorAll( selector ) ).filter( function ( control ) {
				return container === form || container.contains( control );
			} )
			: Array.from( container.querySelectorAll( 'input[type="text"], textarea' ) );

		return controls
			.filter( isTranslatableControl );
	}

	function translatedDefault( source, language, data ) {
		if ( data[ language ] && Object.prototype.hasOwnProperty.call( data[ language ], source ) ) {
			return data[ language ][ source ];
		}

		if ( source.includes( '\n' ) ) {
			return source.split( /\r?\n/ ).map( function ( line ) {
				const trimmed = line.trim();

				if (
					trimmed
					&& data[ language ]
					&& Object.prototype.hasOwnProperty.call( data[ language ], trimmed )
				) {
					return data[ language ][ trimmed ];
				}

				return line;
			} ).join( '\n' );
		}

		return source;
	}

	function createTabs( onSelect ) {
		const tabs = document.createElement( 'div' );
		tabs.className = 'g5tech-page-i18n-tabs';
		tabs.setAttribute( 'role', 'tablist' );
		tabs.setAttribute( 'aria-label', 'Turinio kalba' );

		languages.forEach( function ( language ) {
			const button = document.createElement( 'button' );
			button.type = 'button';
			button.className = 'g5tech-page-i18n-tab';
			button.dataset.g5PageI18nTab = language;
			button.setAttribute( 'role', 'tab' );
			button.textContent = language.toUpperCase();
			button.addEventListener( 'click', function () {
				onSelect( language );
			} );
			tabs.appendChild( button );
		} );

		return tabs;
	}

	document.querySelectorAll( '[data-g5-page-i18n]' ).forEach( function ( config ) {
		const form = config.closest( 'form' );

		if ( ! form ) {
			return;
		}

		const context = config.dataset.g5PageI18nContext || 'page';
		const selector = config.dataset.g5PageI18nSelector || '';
		const containerSelector = config.dataset.g5PageI18nContainer || '';
		const payload = config.querySelector( '[data-g5-page-i18n-payload]' );
		const data = parseEditorData( config );
		const sources = new Map();
		const working = {
			en: Object.assign( {}, data.en || {} ),
			de: Object.assign( {}, data.de || {} )
		};
		let currentLanguage = 'lt';
		const tabGroups = [];

		function registerControls() {
			controlsIn( form, selector, form ).forEach( function ( control ) {
				if ( ! sources.has( control ) ) {
					sources.set( control, control.value );
				}
			} );
		}

		function captureCurrentLanguage() {
			registerControls();

			if ( currentLanguage === 'lt' ) {
				sources.forEach( function ( source, control ) {
					sources.set( control, control.value );
				} );
				return;
			}

			sources.forEach( function ( source, control ) {
				if ( source ) {
					working[ currentLanguage ][ source ] = control.value;
				}
			} );
		}

		function updateTabs() {
			tabGroups.forEach( function ( group ) {
				group.querySelectorAll( '[data-g5-page-i18n-tab]' ).forEach( function ( tab ) {
					const isActive = tab.dataset.g5PageI18nTab === currentLanguage;
					tab.classList.toggle( 'is-active', isActive );
					tab.setAttribute( 'aria-selected', isActive ? 'true' : 'false' );
					tab.tabIndex = isActive ? 0 : -1;
				} );
			} );
		}

		function selectLanguage( language ) {
			if ( ! languages.includes( language ) || language === currentLanguage ) {
				return;
			}

			captureCurrentLanguage();
			currentLanguage = language;
			registerControls();

			sources.forEach( function ( source, control ) {
				control.value = language === 'lt'
					? source
					: translatedDefault( source, language, working );
				control.dispatchEvent( new Event( 'input', { bubbles: true } ) );
			} );

			form.dataset.g5PageI18nLanguage = language;
			updateStructuralControls( language );
			updateTabs();

			if ( window.history && window.history.replaceState ) {
				const url = new URL( window.location.href );
				url.searchParams.set( 'content_lang', language );
				window.history.replaceState( {}, '', url );
			}

			try {
				window.localStorage.setItem( 'g5tech-admin-language-' + context, language );
			} catch ( error ) {
				// Naršyklė gali neleisti naudoti localStorage; redagavimas vis tiek veikia.
			}
		}

		/**
		 * Kartotinių elementų struktūra kuriama tik lietuvių kalbos režimu.
		 * EN ir DE režimu pridėjimo, šalinimo ir tempimo valdikliai paslepiami,
		 * kad naujai sukurta eilutė nebūtų įrašyta kaip lietuviškas šaltinis.
		 */
		function updateStructuralControls( language ) {
			const isSource = 'lt' === language;
			const structural = form.querySelectorAll(
				'.g5tech-repeater__add, .g5tech-repeater__remove, .g5tech-repeater__handle'
			);

			structural.forEach( function ( control ) {
				control.hidden = ! isSource;
				control.setAttribute( 'aria-hidden', isSource ? 'false' : 'true' );

				if ( 'BUTTON' === control.tagName || 'INPUT' === control.tagName ) {
					control.disabled = ! isSource;
				}
			} );

			form.querySelectorAll( '.g5tech-repeater' ).forEach( function ( repeater ) {
				repeater.classList.toggle( 'g5tech-repeater--translation-mode', ! isSource );

				if ( window.jQuery && window.jQuery( repeater ).data( 'ui-sortable' ) ) {
					window.jQuery( repeater ).sortable( isSource ? 'enable' : 'disable' );
				}
			} );

			let notice = form.querySelector( '[data-g5-i18n-structure-notice]' );

			if ( ! isSource && ! notice && structural.length ) {
				notice = document.createElement( 'p' );
				notice.className = 'description';
				notice.setAttribute( 'data-g5-i18n-structure-notice', '' );
				notice.textContent = 'Verčiamas esamų elementų tekstas. Pridėti, šalinti ar perrikiuoti elementus galima tik LT režimu.';
				const firstRepeater = form.querySelector( '.g5tech-repeater' );

				if ( firstRepeater && firstRepeater.parentNode ) {
					firstRepeater.parentNode.insertBefore( notice, firstRepeater );
				}
			}

			if ( notice ) {
				notice.hidden = isSource;
			}
		}

		registerControls();

		const groups = Array.from( form.querySelectorAll( '.g5tech-admin-group__content' ) );
		const selectedContainers = containerSelector
			? Array.from( form.querySelectorAll( containerSelector ) )
			: [];
		const containers = selectedContainers.length
			? selectedContainers
			: ( groups.length ? groups : [ form ] );

		containers.forEach( function ( container ) {
			if ( ! controlsIn( container, selector, form ).length ) {
				return;
			}

			const tabs = createTabs( selectLanguage );
			container.insertBefore( tabs, container.firstChild );
			tabGroups.push( tabs );
		} );

		form.dataset.g5PageI18nLanguage = 'lt';
		updateStructuralControls( 'lt' );
		updateTabs();

		form.addEventListener( 'submit', function () {
			captureCurrentLanguage();
			const result = { en: [], de: [] };

			sources.forEach( function ( source ) {
				if ( ! source ) {
					return;
				}

				[ 'en', 'de' ].forEach( function ( language ) {
					const translation = working[ language ][ source ];

					if ( translation && translation !== source ) {
						result[ language ].push( {
							source: source,
							translation: translation
						} );
					}
				} );
			} );

			if ( payload ) {
				payload.value = JSON.stringify( result );
			}

			sources.forEach( function ( source, control ) {
				control.value = source;
			} );
		} );

		let requested = new URL( window.location.href ).searchParams.get( 'content_lang' );

		if ( ! languages.includes( requested ) ) {
			try {
				requested = window.localStorage.getItem( 'g5tech-admin-language-' + context );
			} catch ( error ) {
				requested = 'lt';
			}
		}

		if ( requested && requested !== 'lt' && languages.includes( requested ) ) {
			selectLanguage( requested );
		}
	} );
}() );
