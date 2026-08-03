import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import metadata from './block.json';
registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { label, title, text } = attributes;
		return (
			<div { ...useBlockProps( { className: 'g5-editor-card' } ) }>
				<RichText tagName="small" allowedFormats={ [] } value={ label }
					onChange={ ( v ) => setAttributes( { label: v } ) } placeholder="Žyma (nebūtina)" />
				<RichText tagName="h3" className="g5-heading-sm" allowedFormats={ [] } value={ title }
					onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Antraštė" />
				<RichText tagName="p" allowedFormats={ [] } value={ text }
					onChange={ ( v ) => setAttributes( { text: v } ) } placeholder="Tekstas" />
			</div>
		);
	},
	save() { return null; },
} );
