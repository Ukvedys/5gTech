import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		return (
			<div { ...useBlockProps( { className: 'info-card' } ) }>
				<span className="info-card__number g5-editor-auto-number"></span>
				<RichText tagName="h3" className="g5-heading-sm" allowedFormats={ [] } value={ attributes.title }
					onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Kortelės antraštė" />
				<RichText tagName="p" allowedFormats={ [] } value={ attributes.text }
					onChange={ ( v ) => setAttributes( { text: v } ) } placeholder="Tekstas" />
			</div>
		);
	},
	save() { return null; },
} );
