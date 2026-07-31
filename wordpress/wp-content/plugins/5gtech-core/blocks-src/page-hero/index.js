import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { eyebrow, title, lead, buttonLabel, buttonUrl, compact } = attributes;
		return (
			<>
				<InspectorControls>
					<PanelBody title="Mygtukas">
						<TextControl label="Mygtuko tekstas" value={ buttonLabel } onChange={ ( v ) => setAttributes( { buttonLabel: v } ) } />
						<TextControl label="Mygtuko nuoroda" value={ buttonUrl } onChange={ ( v ) => setAttributes( { buttonUrl: v } ) } />
						<ToggleControl label="Žemesnė antraštė" checked={ !! compact } onChange={ ( v ) => setAttributes( { compact: v } ) } />
					</PanelBody>
				</InspectorControls>
				<section { ...useBlockProps( { className: 'g5-editor-hero' } ) }>
					<RichText tagName="div" className="g5-eyebrow" allowedFormats={ [] } value={ eyebrow }
						onChange={ ( v ) => setAttributes( { eyebrow: v } ) } placeholder="Žyma" />
					<RichText tagName="h1" className="g5-display-xl" allowedFormats={ [] } value={ title }
						onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Antraštė" />
					<RichText tagName="p" className="g5-body-lg" allowedFormats={ [] } value={ lead }
						onChange={ ( v ) => setAttributes( { lead: v } ) } placeholder="Įžanga" />
				</section>
			</>
		);
	},
	save() { return null; },
} );
