import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button, Disabled, PanelBody, TextControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		return (
			<div { ...useBlockProps() }>
				<InspectorControls>
					<PanelBody title="Sekcijos nustatymai">
						<TextControl label="Trumpa žyma" value={ attributes.eyebrow } onChange={ ( v ) => setAttributes( { eyebrow: v } ) } />
<TextControl label="Antraštė" value={ attributes.title } onChange={ ( v ) => setAttributes( { title: v } ) } />
						<MediaUploadCheck>
							<MediaUpload onSelect={ ( m ) => setAttributes( { imageId: m.id } ) } allowedTypes={ [ 'image' ] } value={ attributes.imageId }
								render={ ( { open } ) => <Button variant="secondary" onClick={ open }>{ attributes.imageId ? 'Pakeisti nuotrauką' : 'Pasirinkti nuotrauką' }</Button> } />
						</MediaUploadCheck>
						<p className="components-base-control__help">Šalių sąrašas imamas iš 5G TECH nustatymų.</p>
					</PanelBody>
				</InspectorControls>
				<Disabled>
					<ServerSideRender block={ metadata.name } attributes={ attributes } />
				</Disabled>
			</div>
		);
	},
	save() { return null; },
} );
