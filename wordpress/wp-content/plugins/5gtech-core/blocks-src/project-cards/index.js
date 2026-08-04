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
					<PanelBody title="Sekcijos nustatymai">
						<TextControl label="Tekstas, kai projektų nėra" value={ attributes.emptyText } onChange={ ( v ) => setAttributes( { emptyText: v } ) } />
						<p className="components-base-control__help">Kortelės imamos iš skilties „Projektai”.</p>
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
