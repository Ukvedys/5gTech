import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps, RichText } from '@wordpress/block-editor';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const set = ( key ) => ( v ) => setAttributes( { [ key ]: v } );
		const innerProps = useInnerBlocksProps( { className: 'strategy-grid g5-editor-strategy-grid' }, { allowedBlocks: [ 'g5tech/labeled-item' ] } );
		return (
			<section { ...useBlockProps( { className: 'g5-section strategy-section g5-grid-lines' } ) }>
				<div className="g5-container">
					<div className="strategy-intro">
						<div className="strategy-intro__copy">
							<RichText tagName="div" className="g5-eyebrow" allowedFormats={ [] } value={ attributes.eyebrow }
								onChange={ set( 'eyebrow' ) } placeholder="Žyma" />
							<RichText tagName="h2" className="g5-display-lg" allowedFormats={ [] } value={ attributes.title }
								onChange={ set( 'title' ) } placeholder="Antraštė" />
							<RichText tagName="p" className="g5-body" allowedFormats={ [] } value={ attributes.lead }
								onChange={ set( 'lead' ) } placeholder="Įžanga" />
						</div>
					</div>
					<div { ...innerProps } />
				</div>
			</section>
		);
	},
	save() { return null; },
} );
