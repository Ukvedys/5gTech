import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import metadata from './block.json';
registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { eyebrow, title, lead, button1Label, button2Label } = attributes;
		const innerProps = useInnerBlocksProps( { className: 'g5-editor-section__body' }, {
			allowedBlocks: [ 'g5tech/hero-slide' ],
			template: [ [ 'g5tech/hero-slide' ] ],
		} );
		return (
			<section { ...useBlockProps( { className: 'g5-editor-hero' } ) }>
				<InspectorControls>
					<PanelBody title="Mygtukai">
						<TextControl label="1 mygtukas (į kontaktus)" value={ button1Label }
							onChange={ ( v ) => setAttributes( { button1Label: v } ) } />
						<TextControl label="2 mygtukas (į paslaugas)" value={ button2Label }
							onChange={ ( v ) => setAttributes( { button2Label: v } ) } />
					</PanelBody>
				</InspectorControls>
				<RichText tagName="div" className="g5-eyebrow" allowedFormats={ [] } value={ eyebrow }
					onChange={ ( v ) => setAttributes( { eyebrow: v } ) } placeholder="Žyma" />
				<RichText tagName="h1" className="g5-display-xl" allowedFormats={ [] } value={ title }
					onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Antraštė" />
				<RichText tagName="p" className="g5-body-lg" allowedFormats={ [] } value={ lead }
					onChange={ ( v ) => setAttributes( { lead: v } ) } placeholder="Įžanga" />
				<p><strong>Skaidrės:</strong></p>
				<div { ...innerProps } />
			</section>
		);
	},
	save() { return null; },
} );
