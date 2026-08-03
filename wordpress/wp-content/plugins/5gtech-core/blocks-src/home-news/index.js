import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import metadata from './block.json';
registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		return (
			<div { ...useBlockProps( { className: 'g5-editor-partners' } ) }>
				<RichText tagName="h2" allowedFormats={ [] } value={ attributes.title }
					onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Sekcijos antraštė" />
				<p><em>Rodomi 3 naujausi įrašai iš skilties „Naujienos“.</em></p>
			</div>
		);
	},
	save() { return null; },
} );
