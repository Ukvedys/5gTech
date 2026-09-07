import { registerBlockType } from '@wordpress/blocks';
import AdminLink from '../shared/admin-link';
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
						<TextControl label="Trumpa žyma" value={ attributes.eyebrow } onChange={ ( v ) => setAttributes( { eyebrow: v } ) } />
<TextControl label="Antraštė" value={ attributes.title } onChange={ ( v ) => setAttributes( { title: v } ) } />
					</PanelBody>
					<PanelBody title="Turinys">
						<p className="components-base-control__help">Kortelės imamos iš skilties „Komanda”.</p>
						<AdminLink variant="primary" href="post-new.php?post_type=g5_team" target="_blank" className="g5-editor-action">Pridėti darbuotoją ↗</AdminLink>
						<AdminLink variant="secondary" href="edit.php?post_type=g5_team" target="_blank" className="g5-editor-action">Visa komanda ↗</AdminLink>
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
