import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import metadata from './block.json';
registerBlockType( metadata.name, {
	edit() {
		return (
			<div { ...useBlockProps( { className: 'g5-editor-partners' } ) }>
				<p><strong>Paslaugų kortelės</strong></p>
				<p><em>Kortelės imamos iš skilties „Paslaugos“.</em></p>
			</div>
		);
	},
	save() { return null; },
} );
