import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import metadata from './block.json';
registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		return (
			<div { ...useBlockProps( { className: 'g5-editor-step' } ) }>
				<RichText tagName="strong" allowedFormats={ [] } value={ attributes.title }
					onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Žingsnio pavadinimas" />
				<RichText tagName="p" allowedFormats={ [] } value={ attributes.text }
					onChange={ ( v ) => setAttributes( { text: v } ) } placeholder="Žingsnio aprašymas" />
			</div>
		);
	},
	save() { return null; },
} );
