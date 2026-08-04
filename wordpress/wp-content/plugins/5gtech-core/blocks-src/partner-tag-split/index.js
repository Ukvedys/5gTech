import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { Button, Disabled, PanelBody, TextControl } from '@wordpress/components';
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
					</PanelBody>
					<PanelBody title="Turinys">
						<p className="components-base-control__help">Sąrašai imami iš skilties „Partneriai ir įranga”.</p>
						<Button variant="primary" href="post-new.php?post_type=g5_partner" target="_blank" className="g5-editor-action">Pridėti partnerį/įrangą ↗</Button>
						<Button variant="secondary" href="edit.php?post_type=g5_partner" target="_blank" className="g5-editor-action">Visas katalogas ↗</Button>
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
