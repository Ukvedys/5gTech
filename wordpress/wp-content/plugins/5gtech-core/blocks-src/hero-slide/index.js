import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { PanelBody, Button, TextControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { imageId, themeFile, title, alt } = attributes;
		const media = useSelect( ( select ) => ( imageId ? select( 'core' ).getMedia( imageId ) : null ), [ imageId ] );
		const themeUri = window.g5tech && window.g5tech.themeUri ? window.g5tech.themeUri : '';
		const imageUrl = ( media && media.source_url ) || ( themeFile && themeUri ? themeUri + '/' + themeFile.replace( /^\//, '' ) : '' );

		return (
			<div { ...useBlockProps( { className: 'g5-hero-slide' } ) }>
				<InspectorControls>
					<PanelBody title="Skaidrė">
						<MediaUploadCheck>
							<MediaUpload onSelect={ ( m ) => setAttributes( { imageId: m.id } ) } allowedTypes={ [ 'image' ] } value={ imageId }
								render={ ( { open } ) => <Button variant="secondary" onClick={ open }>{ imageId ? 'Pakeisti nuotrauką' : 'Pasirinkti nuotrauką' }</Button> } />
						</MediaUploadCheck>
						<TextControl label="Alternatyvusis tekstas" value={ alt } onChange={ ( v ) => setAttributes( { alt: v } ) } />
					</PanelBody>
				</InspectorControls>
				{ imageUrl
					? <img className="g5-hero-slide__img" src={ imageUrl } alt="" />
					: <div className="g5-hero-slide__img g5-hero-slide__img--empty">Nėra nuotraukos</div> }
				<RichText tagName="strong" allowedFormats={ [] } value={ title }
					onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Skaidrės pavadinimas" />
			</div>
		);
	},
	save() { return null; },
} );
