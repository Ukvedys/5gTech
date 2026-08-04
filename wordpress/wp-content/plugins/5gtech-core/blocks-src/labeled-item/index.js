import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		return (
			<div { ...useBlockProps( { className: 'g5-editor-labeled-item' } ) }>
				<RichText tagName="small" allowedFormats={ [] } value={ attributes.label }
					onChange={ ( v ) => setAttributes( { label: v } ) } placeholder="Žyma" />
				<RichText tagName="strong" allowedFormats={ [] } value={ attributes.title }
					onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Pavadinimas" />
				<RichText tagName="p" allowedFormats={ [] } value={ attributes.text }
					onChange={ ( v ) => setAttributes( { text: v } ) } placeholder="Tekstas" />
			</div>
		);
	},
	save() { return null; },
} );
