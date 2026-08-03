import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import metadata from './block.json';
registerBlockType( metadata.name, {
	edit() {
		return (
			<div { ...useBlockProps( { className: 'g5-editor-partners' } ) }>
				<p><strong>Skaičių juosta</strong></p>
				<p><em>Skaičiai valdomi „5G TECH nustatymuose“.</em></p>
			</div>
		);
	},
	save() { return null; },
} );
