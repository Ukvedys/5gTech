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
						<TextControl label="Kairės pusės antraštė" value={ attributes.leftTitle } onChange={ ( v ) => setAttributes( { leftTitle: v } ) } />
<TextControl label="Dešinės pusės antraštė" value={ attributes.rightTitle } onChange={ ( v ) => setAttributes( { rightTitle: v } ) } />
						<p className="components-base-control__help">Sąrašai imami iš skilties „Partneriai ir įranga”.</p>
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
