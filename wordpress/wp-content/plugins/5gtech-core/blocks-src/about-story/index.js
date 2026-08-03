import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps, RichText, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { PanelBody, Button, TextControl } from '@wordpress/components';
import metadata from './block.json';
registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { eyebrow, title, lead, body1, body2, body3, image1Id, image2Id, caption1, caption2 } = attributes;
		const innerProps = useInnerBlocksProps( { className: 'g5-editor-section__body' }, {
			allowedBlocks: [ 'g5tech/labeled-item' ],
			template: [ [ 'g5tech/labeled-item' ] ],
		} );
		return (
			<section { ...useBlockProps( { className: 'g5-editor-section g5-editor-section--light' } ) }>
				<InspectorControls>
					<PanelBody title="Nuotraukos">
						<MediaUploadCheck>
							<MediaUpload onSelect={ ( m ) => setAttributes( { image1Id: m.id } ) } allowedTypes={ [ 'image' ] } value={ image1Id }
								render={ ( { open } ) => <Button variant="secondary" onClick={ open }>{ image1Id ? 'Pakeisti 1 nuotrauką' : 'Pasirinkti 1 nuotrauką' }</Button> } />
						</MediaUploadCheck>
						<TextControl label="1 nuotraukos parašas" value={ caption1 } onChange={ ( v ) => setAttributes( { caption1: v } ) } />
						<MediaUploadCheck>
							<MediaUpload onSelect={ ( m ) => setAttributes( { image2Id: m.id } ) } allowedTypes={ [ 'image' ] } value={ image2Id }
								render={ ( { open } ) => <Button variant="secondary" onClick={ open }>{ image2Id ? 'Pakeisti 2 nuotrauką' : 'Pasirinkti 2 nuotrauką' }</Button> } />
						</MediaUploadCheck>
						<TextControl label="2 nuotraukos parašas" value={ caption2 } onChange={ ( v ) => setAttributes( { caption2: v } ) } />
					</PanelBody>
				</InspectorControls>
				<RichText tagName="div" className="g5-eyebrow" allowedFormats={ [] } value={ eyebrow }
					onChange={ ( v ) => setAttributes( { eyebrow: v } ) } placeholder="Žyma" />
				<RichText tagName="h2" className="g5-display-md" allowedFormats={ [] } value={ title }
					onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Antraštė" />
				<RichText tagName="p" className="g5-body" allowedFormats={ [] } value={ lead }
					onChange={ ( v ) => setAttributes( { lead: v } ) } placeholder="Įžanga" />
				<RichText tagName="p" allowedFormats={ [] } value={ body1 }
					onChange={ ( v ) => setAttributes( { body1: v } ) } placeholder="1 pastraipa" />
				<RichText tagName="p" allowedFormats={ [] } value={ body2 }
					onChange={ ( v ) => setAttributes( { body2: v } ) } placeholder="2 pastraipa" />
				<RichText tagName="p" allowedFormats={ [] } value={ body3 }
					onChange={ ( v ) => setAttributes( { body3: v } ) } placeholder="3 pastraipa" />
				<p><strong>Faktai šone:</strong></p>
				<div { ...innerProps } />
			</section>
		);
	},
	save() { return null; },
} );
