import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl } from '@wordpress/components';
import metadata from './block.json';
registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		return (
			<div { ...useBlockProps( { className: 'g5-editor-partners' } ) }>
				<InspectorControls>
					<PanelBody title="Naujienų tinklelis">
						<RangeControl
							label="Kiek įrašų rodyti"
							value={ attributes.limit }
							onChange={ ( limit ) => setAttributes( { limit } ) }
							min={ 3 }
							max={ 24 }
						/>
					</PanelBody>
				</InspectorControls>
				<p><strong>Naujienų tinklelis</strong></p>
				<p><em>Rodomi { attributes.limit } naujausi įrašai iš skilties „Naujienos“.</em></p>
			</div>
		);
	},
	save() { return null; },
} );
