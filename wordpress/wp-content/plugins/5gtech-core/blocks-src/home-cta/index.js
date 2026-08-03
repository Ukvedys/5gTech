import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { Disabled, PanelBody, TextControl, TextareaControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		return (
			<div { ...useBlockProps() }>
				<InspectorControls>
					<PanelBody title="Baigiamasis kvietimas">
						<TextControl label="Žyma" value={ attributes.eyebrow } onChange={ ( v ) => setAttributes( { eyebrow: v } ) } />
						<TextControl label="Antraštė" value={ attributes.title } onChange={ ( v ) => setAttributes( { title: v } ) } />
						<TextareaControl label="Tekstas" value={ attributes.body } onChange={ ( v ) => setAttributes( { body: v } ) } />
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
