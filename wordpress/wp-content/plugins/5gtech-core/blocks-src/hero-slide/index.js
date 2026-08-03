import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { PanelBody, Button, TextControl } from '@wordpress/components';
import metadata from './block.json';
registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { imageId, title, alt } = attributes;
		return (
			<div { ...useBlockProps( { className: 'g5-editor-card' } ) }>
				<InspectorControls>
					<PanelBody title="Skaidrė">
						<MediaUploadCheck>
							<MediaUpload onSelect={ ( m ) => setAttributes( { imageId: m.id } ) } allowedTypes={ [ 'image' ] } value={ imageId }
								render={ ( { open } ) => <Button variant="secondary" onClick={ open }>{ imageId ? 'Pakeisti nuotrauką' : 'Pasirinkti nuotrauką' }</Button> } />
						</MediaUploadCheck>
						<TextControl label="Alternatyvusis tekstas" value={ alt } onChange={ ( v ) => setAttributes( { alt: v } ) } />
					</PanelBody>
				</InspectorControls>
				<RichText tagName="strong" allowedFormats={ [] } value={ title }
					onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Skaidrės pavadinimas" />
			</div>
		);
	},
	save() { return null; },
} );
