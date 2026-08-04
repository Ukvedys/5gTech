import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';
import metadata from './block.json';

const THEME_CLASSES = {
	light: 'g5-section g5-grid-lines',
	paper: 'g5-section g5-section--paper g5-grid-lines',
	dark: 'g5-section g5-section--dark g5-grid-lines g5-grid-lines--dark',
};

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { eyebrow, title, lead, theme, anchorId, sectionId, dialect } = attributes;
		const isDark = 'dark' === theme;
		const site = 'site' === dialect;
		const headClass = site
			? 'g5-container g5-section-head' + ( isDark ? ' g5-section-head--dark' : '' )
			: 'g5-container section-head' + ( isDark ? ' section-head--dark' : '' );
		const copyClass = site ? 'g5-section-head__copy' : 'section-head__copy';
		const innerProps = useInnerBlocksProps( {}, { template: [ [ 'g5tech/card-grid' ] ] } );

		return (
			<section { ...useBlockProps( { className: THEME_CLASSES[ theme ] || THEME_CLASSES.light } ) }>
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
				<div className={ headClass }>
					<RichText tagName="div" className="g5-eyebrow" allowedFormats={ [] } value={ eyebrow }
						onChange={ ( v ) => setAttributes( { eyebrow: v } ) } placeholder="Žyma" />
					<div className={ copyClass }>
						<RichText tagName="h2" className="g5-display-md" allowedFormats={ [] } value={ title }
							onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Sekcijos antraštė" />
						<RichText tagName="p" className="g5-body" allowedFormats={ [] } value={ lead }
							onChange={ ( v ) => setAttributes( { lead: v } ) } placeholder="Įžanga (nebūtina)" />
					</div>
				</div>
				<div { ...innerProps } />
			</section>
		);
	},
	save() { return null; },
} );
