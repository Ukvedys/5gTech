import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps, RichText } from '@wordpress/block-editor';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const set = ( key ) => ( v ) => setAttributes( { [ key ]: v } );
		const innerProps = useInnerBlocksProps( { className: 'competence-list g5-editor-competence-list' }, { allowedBlocks: [ 'g5tech/labeled-item' ] } );
		return (
			<section { ...useBlockProps( { className: 'g5-section competence-section g5-grid-lines g5-grid-lines--dark' } ) }>
				<div className="g5-container competence-grid">
					<div className="competence-grid__intro">
						<RichText tagName="div" className="g5-eyebrow" allowedFormats={ [] } value={ attributes.eyebrow }
							onChange={ set( 'eyebrow' ) } placeholder="Žyma" />
						<RichText tagName="h2" className="g5-heading-lg" allowedFormats={ [] } value={ attributes.title }
							onChange={ set( 'title' ) } placeholder="Antraštė" />
					</div>
					<div { ...innerProps } />
				</div>
			</section>
		);
	},
	save() { return null; },
} );
