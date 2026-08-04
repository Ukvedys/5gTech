import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		return (
			<div { ...useBlockProps( { className: 'info-card' } ) }>
				<InspectorControls>
					<PanelBody title="Nuoroda">
						<TextControl label="Adresas" value={ attributes.url } help="Pvz. /mokymai/"
							onChange={ ( v ) => setAttributes( { url: v } ) } />
					</PanelBody>
				</InspectorControls>
				<RichText tagName="span" className="info-card__number" allowedFormats={ [] } value={ attributes.label }
					onChange={ ( v ) => setAttributes( { label: v } ) } placeholder="Žyma" />
				<RichText tagName="h3" className="g5-heading-md" allowedFormats={ [] } value={ attributes.title }
					onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Antraštė" />
				<RichText tagName="span" className="info-card__link" allowedFormats={ [] } value={ attributes.linkText }
					onChange={ ( v ) => setAttributes( { linkText: v } ) } placeholder="Nuorodos tekstas" />
			</div>
		);
	},
	save() { return null; },
} );
