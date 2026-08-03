import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { PanelBody, Button } from '@wordpress/components';
import metadata from './block.json';
registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { eyebrow, title, imageId } = attributes;
		return (
			<section { ...useBlockProps( { className: 'g5-editor-section g5-editor-section--light' } ) }>
				<InspectorControls>
					<PanelBody title="Žemėlapis">
						<MediaUploadCheck>
							<MediaUpload
								onSelect={ ( media ) => setAttributes( { imageId: media.id } ) }
								allowedTypes={ [ 'image' ] }
								value={ imageId }
								render={ ( { open } ) => (
									<Button variant="secondary" onClick={ open }>
										{ imageId ? 'Pakeisti žemėlapį' : 'Pasirinkti žemėlapį (kitaip — temos)' }
									</Button>
								) }
							/>
						</MediaUploadCheck>
					</PanelBody>
				</InspectorControls>
				<RichText tagName="div" className="g5-eyebrow" allowedFormats={ [] } value={ eyebrow }
					onChange={ ( v ) => setAttributes( { eyebrow: v } ) } placeholder="Žyma" />
				<RichText tagName="h2" className="g5-display-md" allowedFormats={ [] } value={ title }
					onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Sekcijos antraštė" />
				<p><em>Šalių sąrašas imamas iš „5G TECH nustatymų“, žemėlapis — iš temos arba pasirinktas.</em></p>
			</section>
		);
	},
	save() { return null; },
} );
