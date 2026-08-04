import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps, RichText, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { PanelBody, Button } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { eyebrow, title, lead, body1, body2, body3, image1Id, image2Id, caption1, caption2 } = attributes;
		const themeUri = window.g5tech && window.g5tech.themeUri ? window.g5tech.themeUri : '';
		const media1 = useSelect( ( select ) => ( image1Id ? select( 'core' ).getMedia( image1Id ) : null ), [ image1Id ] );
		const media2 = useSelect( ( select ) => ( image2Id ? select( 'core' ).getMedia( image2Id ) : null ), [ image2Id ] );
		const url1 = ( media1 && media1.source_url ) || ( themeUri ? themeUri + '/assets/images/team/team-work-01.jpg' : '' );
		const url2 = ( media2 && media2.source_url ) || ( themeUri ? themeUri + '/assets/images/team/team-work-02.jpg' : '' );
		const innerProps = useInnerBlocksProps( { className: 'story-facts' }, { allowedBlocks: [ 'g5tech/labeled-item' ] } );

		return (
			<section { ...useBlockProps( { className: 'g5-section about-story g5-grid-lines' } ) }>
				<InspectorControls>
					<PanelBody title="Nuotraukos">
						<MediaUploadCheck>
							<MediaUpload onSelect={ ( m ) => setAttributes( { image1Id: m.id } ) } allowedTypes={ [ 'image' ] } value={ image1Id }
								render={ ( { open } ) => <Button variant="secondary" onClick={ open }>1 nuotrauka</Button> } />
							{ ' ' }
							<MediaUpload onSelect={ ( m ) => setAttributes( { image2Id: m.id } ) } allowedTypes={ [ 'image' ] } value={ image2Id }
								render={ ( { open } ) => <Button variant="secondary" onClick={ open }>2 nuotrauka</Button> } />
						</MediaUploadCheck>
					</PanelBody>
				</InspectorControls>
				<div className="g5-container">
					<div className="editorial-head">
						<RichText tagName="div" className="g5-eyebrow" allowedFormats={ [] } value={ eyebrow }
							onChange={ ( v ) => setAttributes( { eyebrow: v } ) } placeholder="Žyma" />
						<div className="editorial-head__copy">
							<RichText tagName="h2" className="g5-display-lg" allowedFormats={ [] } value={ title }
								onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Antraštė" />
							<RichText tagName="p" className="g5-body" allowedFormats={ [] } value={ lead }
								onChange={ ( v ) => setAttributes( { lead: v } ) } placeholder="Įžanga" />
						</div>
					</div>
					<div className="story-layout">
						<div className="story-copy">
							<RichText tagName="p" className="g5-body" allowedFormats={ [] } value={ body1 }
								onChange={ ( v ) => setAttributes( { body1: v } ) } placeholder="1 pastraipa" />
							<RichText tagName="p" className="g5-body" allowedFormats={ [] } value={ body2 }
								onChange={ ( v ) => setAttributes( { body2: v } ) } placeholder="2 pastraipa" />
							<RichText tagName="p" className="g5-body" allowedFormats={ [] } value={ body3 }
								onChange={ ( v ) => setAttributes( { body3: v } ) } placeholder="3 pastraipa" />
						</div>
						<div { ...innerProps } />
					</div>
					<div className="about-media">
						<figure>
							{ url1 ? <img src={ url1 } alt="" /> : null }
							<RichText tagName="figcaption" allowedFormats={ [] } value={ caption1 }
								onChange={ ( v ) => setAttributes( { caption1: v } ) } placeholder="1 nuotraukos parašas" />
						</figure>
						<figure>
							{ url2 ? <img src={ url2 } alt="" /> : null }
							<RichText tagName="figcaption" allowedFormats={ [] } value={ caption2 }
								onChange={ ( v ) => setAttributes( { caption2: v } ) } placeholder="2 nuotraukos parašas" />
						</figure>
					</div>
				</div>
			</section>
		);
	},
	save() { return null; },
} );
