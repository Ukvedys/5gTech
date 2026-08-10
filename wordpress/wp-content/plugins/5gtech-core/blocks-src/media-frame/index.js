import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { PanelBody, Button, TextControl, ToggleControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { imageId, imageUrl, alt, ratio, themeFallback, parallax, videoId, videoFallback } = attributes;
		const media = useSelect( ( select ) => ( imageId ? select( 'core' ).getMedia( imageId ) : null ), [ imageId ] );
		const videoMedia = useSelect( ( select ) => ( videoId ? select( 'core' ).getMedia( videoId ) : null ), [ videoId ] );
		const themeUri = window.g5tech && window.g5tech.themeUri ? window.g5tech.themeUri : '';
		const themePath = ( p ) => ( p && themeUri ? themeUri + '/' + p.replace( /^\//, '' ) : '' );
		const url = ( media && media.source_url ) || imageUrl || themePath( themeFallback );
		const videoUrl = ( videoMedia && videoMedia.source_url ) || themePath( videoFallback );

		const controls = (
			<InspectorControls>
				<PanelBody title="Nuotrauka arba video">
					<MediaUploadCheck>
						<MediaUpload onSelect={ ( m ) => setAttributes( { imageId: m.id } ) } allowedTypes={ [ 'image' ] } value={ imageId }
							render={ ( { open } ) => <Button variant="secondary" onClick={ open }>{ imageId ? 'Pakeisti nuotrauką' : 'Pasirinkti nuotrauką' }</Button> } />
					</MediaUploadCheck>
					<div style={ { height: '8px' } } />
					<MediaUploadCheck>
						<MediaUpload onSelect={ ( m ) => setAttributes( { videoId: m.id } ) } allowedTypes={ [ 'video' ] } value={ videoId }
							render={ ( { open } ) => <Button variant="secondary" onClick={ open }>{ videoId ? 'Pakeisti video' : 'Pasirinkti video' }</Button> } />
					</MediaUploadCheck>
					{ ( videoId || videoFallback ) && (
						<Button variant="link" isDestructive onClick={ () => setAttributes( { videoId: 0, videoFallback: '' } ) }>Pašalinti video</Button>
					) }
					<TextControl label="Alternatyvusis tekstas" value={ alt } onChange={ ( v ) => setAttributes( { alt: v } ) } />
					<ToggleControl
						label="Per visą plotį su paralakso efektu"
						help="Užima visą ekrano plotį, o skrolinant lėtai atsidengia. Video visada be garso ir kartojasi."
						checked={ !! parallax }
						onChange={ ( v ) => setAttributes( { parallax: v } ) }
					/>
					{ ! parallax && (
						<TextControl label="Proporcija" value={ ratio } help="Pvz. 16 / 8" onChange={ ( v ) => setAttributes( { ratio: v } ) } />
					) }
				</PanelBody>
			</InspectorControls>
		);

		const videoEl = videoUrl ? <video className="media-frame__video" src={ videoUrl } muted loop autoPlay playsInline /> : null;

		if ( parallax ) {
			return (
				<figure { ...useBlockProps( {
					className: 'media-frame media-frame--parallax' + ( videoUrl ? ' media-frame--video' : '' ),
					style: ! videoUrl && url ? { backgroundImage: 'url(' + url + ')' } : undefined,
				} ) }>
					{ controls }
					{ videoEl }
					{ ! videoUrl && ! url && <div className="g5-editor-media-empty">Nuotrauka ar video nepasirinkti</div> }
				</figure>
			);
		}

		return (
			<figure { ...useBlockProps( { className: 'g5-container media-frame' + ( videoUrl ? ' media-frame--video' : '' ), style: { aspectRatio: ( ratio || '16 / 8' ) } } ) }>
				{ controls }
				{ videoEl }
				{ ! videoUrl && ( url ? <img src={ url } alt="" /> : <div className="g5-editor-media-empty">Nuotrauka ar video nepasirinkti</div> ) }
			</figure>
		);
	},
	save() { return null; },
} );
