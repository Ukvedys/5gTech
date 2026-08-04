import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		return (
			<li { ...useBlockProps( { className: 'g5-editor-step' } ) }>
				<span className="g5-editor-auto-number"></span>
				<RichText tagName="strong" allowedFormats={ [] } value={ attributes.title }
					onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Etapo pavadinimas" />
				<RichText tagName="p" allowedFormats={ [] } value={ attributes.text }
					onChange={ ( v ) => setAttributes( { text: v } ) } placeholder="Aprašymas" />
			</li>
		);
	},
	save() { return null; },
} );
