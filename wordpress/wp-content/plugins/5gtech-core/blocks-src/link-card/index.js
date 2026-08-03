import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { label, title, linkText, url } = attributes;
		return (
			<div { ...useBlockProps( { className: 'g5-editor-card' } ) }>
				<InspectorControls>
					<PanelBody title="Nuoroda">
						<TextControl label="Adresas (pvz. /akademija/)" value={ url }
							onChange={ ( v ) => setAttributes( { url: v } ) } />
					</PanelBody>
				</InspectorControls>
				<RichText tagName="span" allowedFormats={ [] } value={ label }
					onChange={ ( v ) => setAttributes( { label: v } ) } placeholder="Žyma" />
				<RichText tagName="h3" className="g5-heading-md" allowedFormats={ [] } value={ title }
					onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Kortelės antraštė" />
				<RichText tagName="span" allowedFormats={ [] } value={ linkText }
					onChange={ ( v ) => setAttributes( { linkText: v } ) } placeholder="Nuorodos tekstas →" />
			</div>
		);
	},
	save() { return null; },
} );
