import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const set = ( key ) => ( v ) => setAttributes( { [ key ]: v } );
		const innerProps = useInnerBlocksProps( { className: 'value-grid g5-editor-value-grid' }, { allowedBlocks: [ 'g5tech/labeled-item' ] } );
		return (
			<section { ...useBlockProps( { className: 'g5-section values-section g5-grid-lines' } ) }>
				<InspectorControls>
					<PanelBody title="Kultūros kvietimas">
						<TextControl label="Mygtuko nuoroda" value={ attributes.cultureUrl } onChange={ set( 'cultureUrl' ) } />
					</PanelBody>
				</InspectorControls>
				<div className="g5-container">
					<div className="editorial-head">
						<RichText tagName="div" className="g5-eyebrow" allowedFormats={ [] } value={ attributes.eyebrow }
							onChange={ set( 'eyebrow' ) } placeholder="Žyma" />
						<div className="editorial-head__copy">
							<RichText tagName="h2" className="g5-display-lg" allowedFormats={ [] } value={ attributes.title }
								onChange={ set( 'title' ) } placeholder="Antraštė" />
						</div>
					</div>
					<div { ...innerProps } />
					<div className="culture-callout">
						<div className="culture-callout__copy">
							<div>
								<RichText tagName="span" className="purpose-card__label" allowedFormats={ [] } value={ attributes.cultureLabel } onChange={ set( 'cultureLabel' ) } placeholder="Žyma" />
								<RichText tagName="h3" className="g5-heading-lg" allowedFormats={ [] } value={ attributes.cultureTitle } onChange={ set( 'cultureTitle' ) } placeholder="Antraštė" />
							</div>
							<RichText tagName="p" className="g5-body" allowedFormats={ [] } value={ attributes.cultureText } onChange={ set( 'cultureText' ) } placeholder="Tekstas" />
						</div>
						<div className="culture-callout__action">
							<RichText tagName="span" className="g5-button g5-button--primary" allowedFormats={ [] } value={ attributes.cultureButtonLabel } onChange={ set( 'cultureButtonLabel' ) } placeholder="Mygtukas" />
						</div>
					</div>
				</div>
			</section>
		);
	},
	save() { return null; },
} );
