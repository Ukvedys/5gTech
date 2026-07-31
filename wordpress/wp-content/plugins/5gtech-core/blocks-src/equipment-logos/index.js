import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import metadata from './block.json';
registerBlockType( metadata.name, {
	edit( { attributes } ) {
		const names = attributes.names || [];
		return (
			<div { ...useBlockProps( { className: 'g5-editor-partners' } ) }>
				<p><strong>Įrangos logotipai</strong></p>
				{ names.length ? <p>{ names.join( ' · ' ) }</p> : <p>Įranga nepasirinkta.</p> }
				<p><em>Logotipai imami iš skilties „Partneriai ir įranga“.</em></p>
			</div>
		);
	},
	save() { return null; },
} );
