import { registerBlockType } from '@wordpress/blocks';
import AdminLink from '../shared/admin-link';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { Button, Disabled, PanelBody } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes } ) {
		return (
			<div { ...useBlockProps() }>
				<InspectorControls>
					<PanelBody title="Turinys">
						<p className="components-base-control__help">Kortelės imamos iš skilties „Komanda” (pirmi nariai).</p>
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
