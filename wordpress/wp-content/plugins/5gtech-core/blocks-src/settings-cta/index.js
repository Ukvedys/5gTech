import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import metadata from './block.json';
registerBlockType( metadata.name, {
	edit() {
		return (
			<div { ...useBlockProps( { className: 'g5-editor-partners' } ) }>
				<p><strong>Kvietimas iš nustatymų</strong></p>
				<p><em>Antraštė, tekstas ir mygtukas valdomi „5G TECH nustatymuose“.</em></p>
			</div>
		);
	},
	save() { return null; },
} );
