import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { PanelBody, Button, TextControl, ToggleControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { imageId, imageUrl, alt, ratio, themeFallback, parallax } = attributes;
		const media = useSelect( ( select ) => ( imageId ? select( 'core' ).getMedia( imageId ) : null ), [ imageId ] );
		const themeUri = window.g5tech && window.g5tech.themeUri ? window.g5tech.themeUri : '';
		const url = ( media && media.source_url ) || imageUrl || ( themeFallback && themeUri ? themeUri + '/' + themeFallback.replace( /^\//, '' ) : '' );

		const controls = (
			<InspectorControls>
				<PanelBody title="Nuotrauka">
					<MediaUploadCheck>
						<MediaUpload onSelect={ ( m ) => setAttributes( { imageId: m.id } ) } allowedTypes={ [ 'image' ] } value={ imageId }
							render={ ( { open } ) => <Button variant="secondary" onClick={ open }>{ imageId ? 'Pakeisti nuotrauką' : 'Pasirinkti nuotrauką' }</Button> } />
					</MediaUploadCheck>
					<TextControl label="Alternatyvusis tekstas" value={ alt } onChange={ ( v ) => setAttributes( { alt: v } ) } />
					<ToggleControl
						label="Per visą plotį su paralakso efektu"
						help="Nuotrauka užima visą ekrano plotį, o skrolinant lėtai atsidengia."
						checked={ !! parallax }
						onChange={ ( v ) => setAttributes( { parallax: v } ) }
					/>
					{ ! parallax && (
						<TextControl label="Proporcija" value={ ratio } help="Pvz. 16 / 8" onChange={ ( v ) => setAttributes( { ratio: v } ) } />
					) }
				</PanelBody>
			</InspectorControls>
		);

		if ( parallax ) {
			return (
				<figure { ...useBlockProps( {
					className: 'media-frame media-frame--parallax',
					style: url ? { backgroundImage: 'url(' + url + ')' } : undefined,
				} ) }>
					{ controls }
					{ ! url && <div className="g5-editor-media-empty">Nuotrauka nepasirinkta</div> }
				</figure>
			);
		}

		return (
			<figure { ...useBlockProps( { className: 'g5-container media-frame', style: { aspectRatio: ( ratio || '16 / 8' ) } } ) }>
				{ controls }
				{ url ? <img src={ url } alt="" /> : <div className="g5-editor-media-empty">Nuotrauka nepasirinkta</div> }
			</figure>
		);
	},
	save() { return null; },
} );
