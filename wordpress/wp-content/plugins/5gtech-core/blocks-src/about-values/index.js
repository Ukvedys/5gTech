import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import metadata from './block.json';
registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const a = attributes;
		const innerProps = useInnerBlocksProps( { className: 'g5-editor-section__body' }, {
			allowedBlocks: [ 'g5tech/labeled-item' ],
			template: [ [ 'g5tech/labeled-item' ] ],
		} );
		return (
			<section { ...useBlockProps( { className: 'g5-editor-section g5-editor-section--light' } ) }>
				<InspectorControls>
					<PanelBody title="Kultūros kvietimas">
						<TextControl label="Mygtuko tekstas" value={ a.cultureButtonLabel }
							onChange={ ( v ) => setAttributes( { cultureButtonLabel: v } ) } />
						<TextControl label="Mygtuko nuoroda" value={ a.cultureUrl }
							onChange={ ( v ) => setAttributes( { cultureUrl: v } ) } />
					</PanelBody>
				</InspectorControls>
				<RichText tagName="div" className="g5-eyebrow" allowedFormats={ [] } value={ a.eyebrow }
					onChange={ ( v ) => setAttributes( { eyebrow: v } ) } placeholder="Žyma" />
				<RichText tagName="h2" className="g5-display-md" allowedFormats={ [] } value={ a.title }
					onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Antraštė" />
				<div { ...innerProps } />
				<div className="g5-editor-card">
					<RichText tagName="span" allowedFormats={ [] } value={ a.cultureLabel }
						onChange={ ( v ) => setAttributes( { cultureLabel: v } ) } placeholder="Kultūros žyma" />
					<RichText tagName="h3" allowedFormats={ [] } value={ a.cultureTitle }
						onChange={ ( v ) => setAttributes( { cultureTitle: v } ) } placeholder="Kultūros antraštė" />
					<RichText tagName="p" allowedFormats={ [] } value={ a.cultureText }
						onChange={ ( v ) => setAttributes( { cultureText: v } ) } placeholder="Kultūros tekstas" />
				</div>
			</section>
		);
	},
	save() { return null; },
} );
