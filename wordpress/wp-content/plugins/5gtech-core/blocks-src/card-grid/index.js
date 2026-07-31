import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit() {
		const blockProps = useBlockProps( { className: 'g5-editor-cards' } );
		const innerProps = useInnerBlocksProps( blockProps, {
			template: [ [ 'g5tech/card' ], [ 'g5tech/card' ], [ 'g5tech/card' ] ],
			orientation: 'vertical',
		} );
		return <div { ...innerProps } />;
	},
	save() { return null; },
} );
