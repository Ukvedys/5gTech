(() => {
	'use strict';

	const selectors = '.g5-language-switcher__details';

	document.addEventListener('click', (event) => {
		document.querySelectorAll(`${selectors}[open]`).forEach((details) => {
			if (!details.contains(event.target)) {
				details.removeAttribute('open');
			}
		});
	});

	document.addEventListener('keydown', (event) => {
		if (event.key !== 'Escape') {
			return;
		}

		document.querySelectorAll(`${selectors}[open]`).forEach((details) => {
			details.removeAttribute('open');
			details.querySelector('summary')?.focus();
		});
	});
})();
