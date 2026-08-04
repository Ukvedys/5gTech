import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { eyebrow, title, body, bodySetting, buttonLabel, dialect } = attributes;
		let sectionClass = 'g5-section cta-section g5-grid-lines g5-grid-lines--dark';
		let gridClass = 'g5-container cta-grid';

		if ( 'site' === dialect ) {
			sectionClass = 'g5-section g5-cta-section g5-grid-lines g5-grid-lines--dark';
			gridClass = 'g5-container g5-cta-grid';
		} else if ( 'team' === dialect ) {
			sectionClass = 'g5-section page-cta g5-grid-lines g5-grid-lines--dark';
			gridClass = 'g5-container page-cta__grid';
		}

		return (
			<section { ...useBlockProps( { className: sectionClass } ) }>
				<InspectorControls>
					<PanelBody title="Mygtukas">
						<TextControl label="Mygtuko tekstas" value={ buttonLabel } onChange={ ( v ) => setAttributes( { buttonLabel: v } ) } />
						<TextControl label="Mygtuko nuoroda" value={ attributes.buttonUrl } onChange={ ( v ) => setAttributes( { buttonUrl: v } ) } />
					</PanelBody>
				</InspectorControls>
				<div className={ gridClass }>
					<div>
						<RichText tagName="div" className="g5-eyebrow" allowedFormats={ [] } value={ eyebrow }
							onChange={ ( v ) => setAttributes( { eyebrow: v } ) } placeholder="Žyma" />
						<RichText tagName="h2" className="g5-display-lg" allowedFormats={ [] } value={ title }
							onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Antraštė" />
						{ bodySetting && ! body
							? <p className="g5-body g5-editor-muted">Tekstas imamas iš 5G TECH nustatymų.</p>
							: <RichText tagName="p" className="g5-body" allowedFormats={ [] } value={ body }
								onChange={ ( v ) => setAttributes( { body: v } ) } placeholder="Tekstas (nebūtina)" /> }
					</div>
					<span className="g5-button g5-button--primary">{ buttonLabel || 'Mygtukas' } <span className="g5-button__icon" aria-hidden="true">→</span></span>
				</div>
			</section>
		);
	},
	save() { return null; },
} );
