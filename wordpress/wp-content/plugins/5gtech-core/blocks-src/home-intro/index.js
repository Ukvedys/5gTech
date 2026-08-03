import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { PanelBody, Button, TextControl } from '@wordpress/components';
import metadata from './block.json';
registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { eyebrow, title, body, imageId, imageAlt } = attributes;
		return (
			<section { ...useBlockProps( { className: 'g5-editor-section g5-editor-section--light' } ) }>
				<InspectorControls>
					<PanelBody title="Iliustracija">
						<MediaUploadCheck>
							<MediaUpload onSelect={ ( m ) => setAttributes( { imageId: m.id } ) } allowedTypes={ [ 'image' ] } value={ imageId }
								render={ ( { open } ) => <Button variant="secondary" onClick={ open }>{ imageId ? 'Pakeisti' : 'Pasirinkti (kitaip — temos)' }</Button> } />
						</MediaUploadCheck>
						<TextControl label="Alternatyvusis tekstas" value={ imageAlt } onChange={ ( v ) => setAttributes( { imageAlt: v } ) } />
					</PanelBody>
				</InspectorControls>
				<RichText tagName="div" className="g5-eyebrow" allowedFormats={ [] } value={ eyebrow }
					onChange={ ( v ) => setAttributes( { eyebrow: v } ) } placeholder="Žyma" />
				<RichText tagName="h2" className="g5-display-md" allowedFormats={ [] } value={ title }
					onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Antraštė" />
				<RichText tagName="p" allowedFormats={ [] } value={ body }
					onChange={ ( v ) => setAttributes( { body: v } ) } placeholder="Tekstas" />
			</section>
		);
	},
	save() { return null; },
} );
