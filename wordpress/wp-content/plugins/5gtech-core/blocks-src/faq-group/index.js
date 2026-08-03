import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import metadata from './block.json';

const GROUPS = [
	{ value: 'start', label: 'Darbo pradžia' },
	{ value: 'travel', label: 'Komandiruotės' },
	{ value: 'safety', label: 'Sauga ir priemonės' },
	{ value: 'daily', label: 'Kasdienis darbas' },
];

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const current = GROUPS.find( ( g ) => g.value === attributes.group );
		return (
			<div { ...useBlockProps( { className: 'g5-editor-partners' } ) }>
				<InspectorControls>
					<PanelBody title="DUK grupė">
						<SelectControl
							label="Klausimų grupė"
							value={ attributes.group }
							options={ GROUPS }
							onChange={ ( group ) => setAttributes( { group } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<p><strong>DUK grupė: { current ? current.label : attributes.group }</strong></p>
				<p><em>Klausimai valdomi skiltyje „Dažniausi klausimai“, priskiriant grupę.</em></p>
			</div>
		);
	},
	save() { return null; },
} );
