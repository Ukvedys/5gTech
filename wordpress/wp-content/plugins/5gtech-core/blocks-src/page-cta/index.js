import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { eyebrow, title, body, buttonLabel, buttonUrl } = attributes;
		return (
			<>
				<InspectorControls>
					<PanelBody title="Mygtukas">
						<TextControl label="Mygtuko tekstas" value={ buttonLabel } onChange={ ( v ) => setAttributes( { buttonLabel: v } ) } />
						<TextControl label="Mygtuko nuoroda" value={ buttonUrl } onChange={ ( v ) => setAttributes( { buttonUrl: v } ) } />
					</PanelBody>
				</InspectorControls>
				<section { ...useBlockProps( { className: 'g5-editor-cta' } ) }>
					<RichText tagName="div" className="g5-eyebrow" allowedFormats={ [] } value={ eyebrow }
						onChange={ ( v ) => setAttributes( { eyebrow: v } ) } placeholder="Žyma" />
					<RichText tagName="h2" className="g5-display-lg" allowedFormats={ [] } value={ title }
						onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Kvietimo antraštė" />
					<RichText tagName="p" className="g5-body" allowedFormats={ [] } value={ body }
						onChange={ ( v ) => setAttributes( { body: v } ) } placeholder="Papildomas tekstas" />
				</section>
			</>
		);
	},
	save() { return null; },
} );
