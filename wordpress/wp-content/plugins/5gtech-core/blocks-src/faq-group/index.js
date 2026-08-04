import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { Disabled, PanelBody, SelectControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		return (
			<div { ...useBlockProps() }>
				<InspectorControls>
					<PanelBody title="Sekcijos nustatymai">
						<SelectControl label="DUK grupė" value={ attributes.group } options={ [ { label: 'Darbo pradžia', value: 'start' }, { label: 'Komandiruotės', value: 'travel' }, { label: 'Sauga ir priemonės', value: 'safety' }, { label: 'Kasdienis darbas', value: 'daily' } ] } onChange={ ( v ) => setAttributes( { group: v } ) } />
						<p className="components-base-control__help">Klausimai valdomi skiltyje „Dažniausi klausimai”.</p>
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
