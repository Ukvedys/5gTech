import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, TextareaControl } from '@wordpress/components';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		return (
			<div { ...useBlockProps( { className: 'g5-editor-partners' } ) }>
				<InspectorControls>
					<PanelBody title="Grupių pavadinimai">
						<TextControl label="Lietuvos grupė" value={ attributes.lithuaniaLabel }
							onChange={ ( lithuaniaLabel ) => setAttributes( { lithuaniaLabel } ) } />
						<TextControl label="Europos grupė" value={ attributes.europeLabel }
							onChange={ ( europeLabel ) => setAttributes( { europeLabel } ) } />
						<TextControl label="Biuro grupė" value={ attributes.officeLabel }
							onChange={ ( officeLabel ) => setAttributes( { officeLabel } ) } />
						<TextareaControl label="Tekstas, kai pozicijų nėra" value={ attributes.emptyText }
							onChange={ ( emptyText ) => setAttributes( { emptyText } ) } />
					</PanelBody>
				</InspectorControls>
				<p><strong>Darbo pozicijų sąrašas</strong></p>
				<p><em>Pozicijos valdomos skiltyje „Karjera · skelbimai“. Rodomos tik aktyvios.</em></p>
			</div>
		);
	},
	save() { return null; },
} );
