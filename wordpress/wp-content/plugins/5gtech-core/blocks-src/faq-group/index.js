import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { Button, Disabled, PanelBody, SelectControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		return (
			<div { ...useBlockProps() }>
				<InspectorControls>
					<PanelBody title="Sekcijos nustatymai">
						<SelectControl label="DUK grupė" value={ attributes.group } options={ [ { label: 'Darbo pradžia', value: 'start' }, { label: 'Komandiruotės', value: 'travel' }, { label: 'Sauga ir priemonės', value: 'safety' }, { label: 'Kasdienis darbas', value: 'daily' } ] } onChange={ ( v ) => setAttributes( { group: v } ) } />
					</PanelBody>
					<PanelBody title="Turinys">
						<p className="components-base-control__help">Klausimai valdomi skiltyje „Dažniausi klausimai”. Naujam klausimui parinkite temą „Kandidatams” ir šią grupę.</p>
						<Button variant="primary" href="post-new.php?post_type=g5_faq" target="_blank" className="g5-editor-action">Pridėti klausimą ↗</Button>
						<Button variant="secondary" href="edit.php?post_type=g5_faq" target="_blank" className="g5-editor-action">Visi klausimai ↗</Button>
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
