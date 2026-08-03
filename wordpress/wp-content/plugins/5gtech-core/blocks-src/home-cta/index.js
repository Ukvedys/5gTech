import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import metadata from './block.json';
registerBlockType( metadata.name, {
	edit() {
		return (
			<div { ...useBlockProps( { className: 'g5-editor-partners' } ) }>
				<p><strong>Titulinis · baigiamasis kvietimas</strong></p>
				<p><em>El. paštas ir telefonas imami iš „5G TECH nustatymų“.</em></p>
			</div>
		);
	},
	save() { return null; },
} );
