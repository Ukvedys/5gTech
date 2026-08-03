import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { label, title, text, url } = attributes;
		return (
			<div { ...useBlockProps( { className: 'audience-item' } ) }>
				<InspectorControls>
					<PanelBody title="Kortelės nuoroda">
						<TextControl label="Adresas" value={ url } help="Pvz. /karjera/ arba pilna nuoroda"
							onChange={ ( v ) => setAttributes( { url: v } ) } />
					</PanelBody>
				</InspectorControls>
				<RichText tagName="small" allowedFormats={ [] } value={ label }
					onChange={ ( v ) => setAttributes( { label: v } ) } placeholder="Žyma" />
				<RichText tagName="h3" allowedFormats={ [] } value={ title }
					onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Pavadinimas" />
				<RichText tagName="p" allowedFormats={ [] } value={ text }
					onChange={ ( v ) => setAttributes( { text: v } ) } placeholder="Aprašymas" />
				<span className="audience-arrow">→</span>
			</div>
		);
	},
	save() { return null; },
} );
