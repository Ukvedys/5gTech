import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit() {
		return (
			<div { ...useBlockProps( { className: 'g5-editor-partners' } ) }>
				<p><strong>Kandidatavimo forma</strong></p>
				<p><em>Pozicijų sąrašas atsinaujina pagal aktyvius skelbimus. CV siunčiamas karjeros el. paštu iš nustatymų.</em></p>
			</div>
		);
	},
	save() { return null; },
} );
