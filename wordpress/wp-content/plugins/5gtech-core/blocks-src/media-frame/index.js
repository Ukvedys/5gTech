import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, MediaUpload, MediaUploadCheck, InspectorControls } from '@wordpress/block-editor';
import { Button, PanelBody, TextControl } from '@wordpress/components';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { imageId, imageUrl, alt, ratio } = attributes;
		return (
			<>
				<InspectorControls>
					<PanelBody title="Nuotrauka">
						<TextControl label="Alternatyvus tekstas" value={ alt }
							onChange={ ( v ) => setAttributes( { alt: v } ) } />
						<TextControl label="Proporcija" value={ ratio }
							onChange={ ( v ) => setAttributes( { ratio: v } ) } help="Pvz. 16 / 8" />
					</PanelBody>
				</InspectorControls>
				<figure { ...useBlockProps( { className: 'g5-editor-media' } ) }>
					{ imageUrl ? <img src={ imageUrl } alt={ alt } style={ { maxWidth: '100%' } } /> : <p>Nuotrauka nepasirinkta.</p> }
					<MediaUploadCheck>
						<MediaUpload
							allowedTypes={ [ 'image' ] }
							value={ imageId }
							onSelect={ ( media ) => setAttributes( {
								imageId: media.id,
								imageUrl: media.url,
								alt: media.alt || alt,
							} ) }
							render={ ( { open } ) => (
								<Button variant="secondary" onClick={ open }>
									{ imageId ? 'Keisti nuotrauką' : 'Pasirinkti nuotrauką' }
								</Button>
							) }
						/>
					</MediaUploadCheck>
				</figure>
			</>
		);
	},
	save() { return null; },
} );
