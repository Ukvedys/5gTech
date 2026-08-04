import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit() {
		const innerProps = useInnerBlocksProps(
			{ className: 'g5-container check-list g5-editor-check-list' },
			{ allowedBlocks: [ 'g5tech/check-item' ], template: [ [ 'g5tech/check-item' ] ] }
		);
		return <div { ...useBlockProps() }><ul { ...innerProps } /></div>;
	},
	save() { return null; },
} );
