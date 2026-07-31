import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import metadata from './block.json';
registerBlockType( metadata.name, {
	edit() {
		const inner = useInnerBlocksProps( useBlockProps( { className: 'g5-editor-checklist' } ), {
			template: [ [ 'g5tech/check-item' ], [ 'g5tech/check-item' ] ], orientation: 'vertical',
		} );
		return <ul { ...inner } />;
	},
	save() { return null; },
} );
