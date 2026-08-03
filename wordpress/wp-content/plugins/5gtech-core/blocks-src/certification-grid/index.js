import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import metadata from './block.json';
registerBlockType( metadata.name, {
	edit() {
		return (
			<div { ...useBlockProps( { className: 'g5-editor-partners' } ) }>
				<p><strong>Sertifikatų juosta</strong></p>
				<p><em>Sąrašas valdomas „5G TECH nustatymuose“ (eilutė: reikšmė | paaiškinimas).</em></p>
			</div>
		);
	},
	save() { return null; },
} );
