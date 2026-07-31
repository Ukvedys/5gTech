import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import metadata from './block.json';
registerBlockType( metadata.name, {
	edit() {
		const inner = useInnerBlocksProps( useBlockProps( { className: 'g5-editor-steps' } ), {
			template: [ [ 'g5tech/step' ], [ 'g5tech/step' ] ], orientation: 'vertical',
		} );
		return <div { ...inner } />;
	},
	save() { return null; },
} );
