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
						<p className="components-base-control__help">Rodomi pasirinkti katalogo įrašai; pavadinimai atsinaujina iš katalogo.</p>
						<AdminLink variant="primary" href="post-new.php?post_type=g5_partner" target="_blank" className="g5-editor-action">Pridėti partnerį/įrangą ↗</AdminLink>
						<AdminLink variant="secondary" href="edit.php?post_type=g5_partner" target="_blank" className="g5-editor-action">Visas katalogas ↗</AdminLink>
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
