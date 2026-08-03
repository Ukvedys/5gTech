import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import metadata from './block.json';
registerBlockType( metadata.name, {
	edit() {
		return (
			<div { ...useBlockProps( { className: 'g5-editor-partners' } ) }>
				<p><strong>Titulinis · įrangos gamintojai</strong></p>
				<p><em>Gamintojai imami iš skilties „Partneriai ir įranga“.</em></p>
			</div>
		);
	},
	save() { return null; },
} );
