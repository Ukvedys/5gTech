import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { eyebrow, title, lead, theme, anchorId, sectionId } = attributes;
		const blockProps = useBlockProps( { className: 'g5-editor-section g5-editor-section--' + theme } );
		const innerProps = useInnerBlocksProps( { className: 'g5-editor-section__body' }, { template: [ [ 'g5tech/card-grid' ] ] } );
		return (
			<>
				<InspectorControls>
					<PanelBody title="Sekcijos nustatymai">
						<SelectControl label="Fonas" value={ theme } options={ [
							{ label: 'Šviesus', value: 'light' },
							{ label: 'Pilkas', value: 'paper' },
							{ label: 'Tamsus', value: 'dark' },
						] } onChange={ ( v ) => setAttributes( { theme: v } ) } />
						<TextControl label="Inkaro ID" value={ anchorId } onChange={ ( v ) => setAttributes( { anchorId: v } ) } />
						<TextControl label="Sekcijos ID (nuorodoms su #)" value={ sectionId } onChange={ ( v ) => setAttributes( { sectionId: v } ) } />
					</PanelBody>
				</InspectorControls>
				<section { ...blockProps }>
					<RichText tagName="div" className="g5-eyebrow" allowedFormats={ [] } value={ eyebrow }
						onChange={ ( v ) => setAttributes( { eyebrow: v } ) } placeholder="Žyma" />
					<RichText tagName="h2" className="g5-display-md" allowedFormats={ [] } value={ title }
						onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Sekcijos antraštė" />
					<RichText tagName="p" className="g5-body" allowedFormats={ [] } value={ lead }
						onChange={ ( v ) => setAttributes( { lead: v } ) } placeholder="Įžanga (nebūtina)" />
					<div { ...innerProps } />
				</section>
			</>
		);
	},
	save() { return null; },
} );
