import { Button } from '@wordpress/components';

// The server supplies only destinations available to the current editor.
export default function AdminLink( { href, ...props } ) {
	const destination = window.g5tech?.editorLinks?.[ href ];
	return destination ? <Button { ...props } href={ destination } rel="noopener noreferrer" /> : null;
}
