import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes } ) {
		const names = attributes.names || [];
		return (
			<div { ...useBlockProps( { className: 'g5-editor-partners' } ) }>
				<p><strong>Įranga ir gamintojai</strong></p>
				{ names.length
					? <p>{ names.join( ' · ' ) }</p>
					: <p>Įranga nepasirinkta.</p> }
				<p><em>Sąrašas imamas iš skilties „Partneriai ir įranga“.</em></p>
			</div>
		);
	},
	save() { return null; },
} );
