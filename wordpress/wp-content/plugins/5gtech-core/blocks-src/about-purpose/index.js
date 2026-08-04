import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const set = ( key ) => ( v ) => setAttributes( { [ key ]: v } );
		return (
			<section { ...useBlockProps( { className: 'g5-section purpose-section g5-grid-lines g5-grid-lines--dark' } ) }>
				<div className="g5-container">
					<div className="editorial-head">
						<RichText tagName="div" className="g5-eyebrow" allowedFormats={ [] } value={ attributes.eyebrow }
							onChange={ set( 'eyebrow' ) } placeholder="Žyma" />
						<div className="editorial-head__copy">
							<RichText tagName="h2" className="g5-display-lg" allowedFormats={ [] } value={ attributes.title }
								onChange={ set( 'title' ) } placeholder="Antraštė" />
						</div>
					</div>
					<div className="purpose-grid">
						<article className="purpose-card">
							<RichText tagName="span" className="purpose-card__label" allowedFormats={ [] } value={ attributes.missionLabel } onChange={ set( 'missionLabel' ) } placeholder="Žyma" />
							<RichText tagName="h3" className="g5-heading-lg" allowedFormats={ [] } value={ attributes.missionTitle } onChange={ set( 'missionTitle' ) } placeholder="Misija" />
							<RichText tagName="p" className="g5-body" allowedFormats={ [] } value={ attributes.missionText } onChange={ set( 'missionText' ) } placeholder="Tekstas" />
						</article>
						<article className="purpose-card">
							<RichText tagName="span" className="purpose-card__label" allowedFormats={ [] } value={ attributes.visionLabel } onChange={ set( 'visionLabel' ) } placeholder="Žyma" />
							<RichText tagName="h3" className="g5-heading-lg" allowedFormats={ [] } value={ attributes.visionTitle } onChange={ set( 'visionTitle' ) } placeholder="Vizija" />
							<RichText tagName="p" className="g5-body" allowedFormats={ [] } value={ attributes.visionText } onChange={ set( 'visionText' ) } placeholder="Tekstas" />
						</article>
					</div>
				</div>
			</section>
		);
	},
	save() { return null; },
} );
