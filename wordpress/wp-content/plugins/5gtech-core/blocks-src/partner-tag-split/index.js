import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import metadata from './block.json';
registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		return (
			<div { ...useBlockProps( { className: 'g5-editor-partners' } ) }>
				<InspectorControls>
					<PanelBody title="Stulpelių antraštės">
						<TextControl label="Kairysis stulpelis" value={ attributes.leftTitle }
							onChange={ ( leftTitle ) => setAttributes( { leftTitle } ) } />
						<TextControl label="Dešinysis stulpelis" value={ attributes.rightTitle }
							onChange={ ( rightTitle ) => setAttributes( { rightTitle } ) } />
					</PanelBody>
				</InspectorControls>
				<p><strong>Partnerių sąrašai: { attributes.leftTitle } · { attributes.rightTitle }</strong></p>
				<p><em>Sąrašai valdomi skiltyje „Partneriai ir įranga“.</em></p>
			</div>
		);
	},
	save() { return null; },
} );
