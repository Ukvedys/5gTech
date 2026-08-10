import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps, RichText, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { PanelBody, Button, TextControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { eyebrow, title, lead, body1, body2, body3, imageId, imageFallback, imageAlt, caption, anchorId } = attributes;
		const themeUri = window.g5tech && window.g5tech.themeUri ? window.g5tech.themeUri : '';
		const media = useSelect( ( select ) => ( imageId ? select( 'core' ).getMedia( imageId ) : null ), [ imageId ] );
		const imageUrl = ( media && media.source_url ) || ( imageFallback && themeUri ? themeUri + '/' + imageFallback.replace( /^\//, '' ) : '' );
		const innerProps = useInnerBlocksProps(
			{ className: 'landing-story__moments' },
			{
				allowedBlocks: [ 'g5tech/labeled-item' ],
				template: [ [ 'g5tech/labeled-item' ], [ 'g5tech/labeled-item' ], [ 'g5tech/labeled-item' ] ],
			}
		);

		return (
			<section { ...useBlockProps( { className: 'g5-section landing-story g5-grid-lines' } ) }>
				<InspectorControls>
					<PanelBody title="Nuotrauka">
						<MediaUploadCheck>
							<MediaUpload onSelect={ ( selected ) => setAttributes( { imageId: selected.id } ) } allowedTypes={ [ 'image' ] } value={ imageId }
								render={ ( { open } ) => <Button variant="secondary" onClick={ open }>Pasirinkti nuotrauką</Button> } />
						</MediaUploadCheck>
						<TextControl label="Temos nuotraukos kelias" value={ imageFallback } onChange={ ( value ) => setAttributes( { imageFallback: value } ) } />
						<TextControl label="Alternatyvus tekstas" value={ imageAlt } onChange={ ( value ) => setAttributes( { imageAlt: value } ) } />
					</PanelBody>
					<PanelBody title="Nuoroda" initialOpen={ false }>
						<TextControl label="Antraštės ID" value={ anchorId } onChange={ ( value ) => setAttributes( { anchorId: value } ) } />
					</PanelBody>
				</InspectorControls>
				<div className="g5-container">
					<div className="landing-story__head">
						<RichText tagName="div" className="g5-eyebrow" allowedFormats={ [] } value={ eyebrow }
							onChange={ ( value ) => setAttributes( { eyebrow: value } ) } placeholder="Žyma" />
						<div className="landing-story__head-copy">
							<RichText tagName="h2" className="g5-display-lg" allowedFormats={ [] } value={ title }
								onChange={ ( value ) => setAttributes( { title: value } ) } placeholder="Antraštė" />
							<RichText tagName="p" className="g5-body-lg" allowedFormats={ [] } value={ lead }
								onChange={ ( value ) => setAttributes( { lead: value } ) } placeholder="Įžanga" />
						</div>
					</div>
					<div className="landing-story__layout">
						<div className="landing-story__copy">
							<RichText tagName="p" className="g5-body" allowedFormats={ [] } value={ body1 } onChange={ ( value ) => setAttributes( { body1: value } ) } placeholder="1 pastraipa" />
							<RichText tagName="p" className="g5-body" allowedFormats={ [] } value={ body2 } onChange={ ( value ) => setAttributes( { body2: value } ) } placeholder="2 pastraipa" />
							<RichText tagName="p" className="g5-body" allowedFormats={ [] } value={ body3 } onChange={ ( value ) => setAttributes( { body3: value } ) } placeholder="3 pastraipa" />
						</div>
						<figure className="landing-story__media">
							{ imageUrl ? <img src={ imageUrl } alt="" /> : null }
							<RichText tagName="figcaption" allowedFormats={ [] } value={ caption } onChange={ ( value ) => setAttributes( { caption: value } ) } placeholder="Nuotraukos parašas" />
						</figure>
					</div>
					<div { ...innerProps } />
				</div>
			</section>
		);
	},
	save() { return null; },
} );
