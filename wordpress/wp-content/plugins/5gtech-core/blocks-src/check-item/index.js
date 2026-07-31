import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import metadata from './block.json';
registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		return (
			<li { ...useBlockProps() }>
				<RichText tagName="span" allowedFormats={ [] } value={ attributes.text }
					onChange={ ( v ) => setAttributes( { text: v } ) } placeholder="Sąrašo eilutė" />
			</li>
		);
	},
	save() { return null; },
} );
