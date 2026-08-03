import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps, RichText } from '@wordpress/block-editor';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const innerProps = useInnerBlocksProps( { className: 'audience-list' }, {
			allowedBlocks: [ 'g5tech/audience-item' ],
			template: [ [ 'g5tech/audience-item' ] ],
		} );

		return (
			<section { ...useBlockProps( { className: 'audiences' } ) }>
				<div className="container">
					<div className="audiences-head">
						<RichText tagName="h2" allowedFormats={ [] } value={ attributes.title }
							onChange={ ( v ) => setAttributes( { title: v } ) } placeholder="Antraštė" />
						<RichText tagName="p" allowedFormats={ [] } value={ attributes.lead }
							onChange={ ( v ) => setAttributes( { lead: v } ) } placeholder="Įžanga" />
					</div>
					<div { ...innerProps } />
				</div>
			</section>
		);
	},
	save() { return null; },
} );
