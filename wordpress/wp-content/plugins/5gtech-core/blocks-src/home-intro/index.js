import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { PanelBody, Button, TextControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { eyebrow, title, body, imageId, imageAlt } = attributes;
		const media = useSelect( ( select ) => ( imageId ? select( 'core' ).getMedia( imageId ) : null ), [ imageId ] );
		const themeUri = window.g5tech && window.g5tech.themeUri ? window.g5tech.themeUri : '';
		const imageUrl = ( media && media.source_url ) || ( themeUri ? themeUri + '/assets/images/home/infrastructure-line.png' : '' );

		return (
			<section { ...useBlockProps( { className: 'intro' } ) }>
				<InspectorControls>
					<PanelBody title="Iliustracija">
						<MediaUploadCheck>
							<MediaUpload onSelect={ ( m ) => setAttributes( { imageId: m.id } ) } allowedTypes={ [ 'image' ] } value={ imageId }
								render={ ( { open } ) => <Button variant="secondary" onClick={ open }>{ imageId ? 'Pakeisti' : 'Pasirinkti (kitaip — temos)' }</Button> } />
						</MediaUploadCheck>
						<TextControl label="Alternatyvusis tekstas" value={ imageAlt } onChange={ ( v ) => setAttributes( { imageAlt: v } ) } />
					</PanelBody>
				</InspectorControls>
				<div className="container">
					<div className="intro-grid">
						<RichText tagName="div" className="eyebrow" allowedFormats={ [] } value={ eyebrow }
							onChange={ ( v ) => setAttributes( { eyebrow: v } ) } placeholder="Žyma" />
						<div>
							<RichText tagName="h2" allowedFormats={ [] } value={ title }
								onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Antraštė" />
							<div className="intro-copy">
								<RichText tagName="p" allowedFormats={ [] } value={ body }
									onChange={ ( v ) => setAttributes( { body: v } ) } placeholder="Tekstas" />
							</div>
						</div>
					</div>
					{ imageUrl ? <div className="network-strip"><img src={ imageUrl } alt="" /></div> : null }
				</div>
			</section>
		);
	},
	save() { return null; },
} );
