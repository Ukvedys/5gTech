import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import metadata from './block.json';
registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { label, title, text, url } = attributes;
		return (
			<div { ...useBlockProps( { className: 'g5-editor-card' } ) }>
				<InspectorControls>
					<PanelBody title="Nuoroda">
						<TextControl label="Adresas (pvz. /vadovams/)" value={ url }
							onChange={ ( v ) => setAttributes( { url: v } ) } />
					</PanelBody>
				</InspectorControls>
				<RichText tagName="small" allowedFormats={ [] } value={ label }
					onChange={ ( v ) => setAttributes( { label: v } ) } placeholder="Žyma" />
				<RichText tagName="h3" allowedFormats={ [] } value={ title }
					onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Antraštė" />
				<RichText tagName="p" allowedFormats={ [] } value={ text }
					onChange={ ( v ) => setAttributes( { text: v } ) } placeholder="Tekstas" />
			</div>
		);
	},
	save() { return null; },
} );
