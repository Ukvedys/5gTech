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
						<TextControl type="number" label="Kiek naujienų rodyti" value={ attributes.limit } onChange={ ( v ) => setAttributes( { limit: parseInt( v, 10 ) || 0 } ) } />
<TextControl label="Tekstas, kai naujienų nėra" value={ attributes.emptyText } onChange={ ( v ) => setAttributes( { emptyText: v } ) } />
					</PanelBody>
					<PanelBody title="Turinys">
						<p className="components-base-control__help">Įrašai imami iš skilties „Naujienos”.</p>
						<Button variant="primary" href="post-new.php" target="_blank" className="g5-editor-action">Pridėti naujieną ↗</Button>
						<Button variant="secondary" href="edit.php" target="_blank" className="g5-editor-action">Visos naujienos ↗</Button>
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
