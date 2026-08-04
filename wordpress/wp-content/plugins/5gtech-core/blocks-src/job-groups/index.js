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
						<TextControl label="Lietuvos grupės antraštė" value={ attributes.lithuaniaLabel } onChange={ ( v ) => setAttributes( { lithuaniaLabel: v } ) } />
<TextControl label="Europos grupės antraštė" value={ attributes.europeLabel } onChange={ ( v ) => setAttributes( { europeLabel: v } ) } />
<TextControl label="Biuro grupės antraštė" value={ attributes.officeLabel } onChange={ ( v ) => setAttributes( { officeLabel: v } ) } />
<TextControl label="Tekstas, kai pozicijų nėra" value={ attributes.emptyText } onChange={ ( v ) => setAttributes( { emptyText: v } ) } />
					</PanelBody>
					<PanelBody title="Turinys">
						<p className="components-base-control__help">Pozicijos imamos iš skilties „Darbo pozicijos”.</p>
						<Button variant="primary" href="post-new.php?post_type=g5_job" target="_blank" className="g5-editor-action">Pridėti poziciją ↗</Button>
						<Button variant="secondary" href="edit.php?post_type=g5_job" target="_blank" className="g5-editor-action">Visos pozicijos ↗</Button>
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
