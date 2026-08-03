import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl, TextControl } from '@wordpress/components';
import metadata from './block.json';
registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		return (
			<div { ...useBlockProps( { className: 'g5-editor-partners' } ) }>
				<InspectorControls>
					<PanelBody title="Projektų peržiūra">
						<RangeControl label="Kiek projektų rodyti" value={ attributes.limit } min={ 1 } max={ 6 }
							onChange={ ( limit ) => setAttributes( { limit } ) } />
						<TextControl label="Mygtuko tekstas" value={ attributes.buttonLabel }
							onChange={ ( buttonLabel ) => setAttributes( { buttonLabel } ) } />
					</PanelBody>
				</InspectorControls>
				<p><strong>Atrinkti projektai ({ attributes.limit })</strong></p>
				<p><em>Projektai valdomi skiltyje „Projektai“.</em></p>
			</div>
		);
	},
	save() { return null; },
} );
