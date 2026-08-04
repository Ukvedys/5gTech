import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { eyebrow, title, lead, buttonLabel, buttonUrl, buttonIcon, button2Label, button2Url, compact, dialect } = attributes;
		const site = 'site' === dialect;
		const sectionClass = site
			? 'g5-inner-hero g5-grid-lines g5-grid-lines--dark'
			: 'inner-hero ' + ( compact ? 'inner-hero--compact ' : '' ) + 'g5-grid-lines g5-grid-lines--dark';
		const copyClass = site ? 'g5-inner-hero__copy' : 'inner-hero__copy';
		const actionsClass = site ? 'g5-inner-hero__actions' : 'inner-hero__actions';

		return (
			<section { ...useBlockProps( { className: sectionClass } ) }>
				<InspectorControls>
					<PanelBody title="Mygtukai">
						<TextControl label="Mygtuko tekstas" value={ buttonLabel } onChange={ ( v ) => setAttributes( { buttonLabel: v } ) } />
						<TextControl label="Mygtuko nuoroda" value={ buttonUrl } help="Pvz. /kandidatuoti/" onChange={ ( v ) => setAttributes( { buttonUrl: v } ) } />
						<TextControl label="Mygtuko ženklas" value={ buttonIcon } help="Pvz. → arba ↓" onChange={ ( v ) => setAttributes( { buttonIcon: v } ) } />
						<TextControl label="2 mygtuko tekstas" value={ button2Label } onChange={ ( v ) => setAttributes( { button2Label: v } ) } />
						<TextControl label="2 mygtuko nuoroda" value={ button2Url } onChange={ ( v ) => setAttributes( { button2Url: v } ) } />
					</PanelBody>
					<PanelBody title="Išdėstymas" initialOpen={ false }>
						<ToggleControl label="Žemesnė antraštės juosta" checked={ !! compact } onChange={ ( v ) => setAttributes( { compact: v } ) } />
					</PanelBody>
				</InspectorControls>
				<div className="g5-container g5-grid">
					<div className={ copyClass }>
						<RichText tagName="div" className="g5-eyebrow" allowedFormats={ [] } value={ eyebrow }
							onChange={ ( v ) => setAttributes( { eyebrow: v } ) } placeholder="Žyma" />
						<RichText tagName="h1" className="g5-display-xl" allowedFormats={ [] } value={ title }
							onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Antraštė" />
						<RichText tagName="p" className="g5-body-lg" allowedFormats={ [] } value={ lead }
							onChange={ ( v ) => setAttributes( { lead: v } ) } placeholder="Įžanga (nebūtina)" />
						{ ( buttonLabel || button2Label ) && (
							<div className={ actionsClass }>
								{ buttonLabel ? <span className="g5-button g5-button--primary">{ buttonLabel } <span className="g5-button__icon" aria-hidden="true">{ buttonIcon || '→' }</span></span> : null }
								{ button2Label ? <span className="g5-button g5-button--outline-light">{ button2Label }</span> : null }
							</div>
						) }
					</div>
				</div>
			</section>
		);
	},
	save() { return null; },
} );
