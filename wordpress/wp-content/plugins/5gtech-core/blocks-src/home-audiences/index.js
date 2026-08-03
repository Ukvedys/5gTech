import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps, RichText } from '@wordpress/block-editor';
import metadata from './block.json';
registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const innerProps = useInnerBlocksProps( { className: 'g5-editor-section__body' }, {
			allowedBlocks: [ 'g5tech/audience-item' ],
			template: [ [ 'g5tech/audience-item' ] ],
		} );
		return (
			<section { ...useBlockProps( { className: 'g5-editor-section g5-editor-section--light' } ) }>
				<RichText tagName="h2" className="g5-display-md" allowedFormats={ [] } value={ attributes.title }
					onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Antraštė" />
				<RichText tagName="p" className="g5-body" allowedFormats={ [] } value={ attributes.lead }
					onChange={ ( v ) => setAttributes( { lead: v } ) } placeholder="Įžanga" />
				<div { ...innerProps } />
			</section>
		);
	},
	save() { return null; },
} );
