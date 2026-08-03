import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { Disabled, PanelBody, TextControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		return (
			<div { ...useBlockProps() }>
				<InspectorControls>
					<PanelBody title="Naujienų sekcija">
						<TextControl label="Antraštė" value={ attributes.title } onChange={ ( v ) => setAttributes( { title: v } ) } />
						<TextControl label="Nuorodos tekstas" value={ attributes.linkLabel } onChange={ ( v ) => setAttributes( { linkLabel: v } ) } />
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
