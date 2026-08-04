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
						<TextControl type="number" label="Kiek projektų rodyti" value={ attributes.limit } onChange={ ( v ) => setAttributes( { limit: parseInt( v, 10 ) || 0 } ) } />
<TextControl label="Mygtuko tekstas" value={ attributes.buttonLabel } onChange={ ( v ) => setAttributes( { buttonLabel: v } ) } />
					</PanelBody>
					<PanelBody title="Turinys">
						<p className="components-base-control__help">Projektai imami iš skilties „Projektai”.</p>
						<Button variant="primary" href="post-new.php?post_type=g5_project" target="_blank" className="g5-editor-action">Pridėti projektą ↗</Button>
						<Button variant="secondary" href="edit.php?post_type=g5_project" target="_blank" className="g5-editor-action">Visi projektai ↗</Button>
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
